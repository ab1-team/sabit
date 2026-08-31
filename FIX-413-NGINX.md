# Perbaikan HTTP 413 "Content Too Large" di sabit.al-maruf.sch.id

## Diagnosa

Error `413 Request Entity Too Large` berasal dari **layer web server / PHP**, bukan dari aplikasi Laravel. Penyebab:

- **Nginx** punya batas `client_max_body_size` (default 1 MB).
- **PHP** punya batas `upload_max_filesize` dan `post_max_size` (default 2 MB / 8 MB).

Form upload foto/video di `/app/admin-landing/galleries` mengirim file hingga 50 MB (untuk video lokal) — melewati batas default di atas.

## Yang perlu diubah di server production

### 1. Nginx — naikkan `client_max_body_size`

Cari file konfigurasi site (umumnya `/etc/nginx/sites-available/sabit.al-maruf.sch.id` atau di dalam `/etc/nginx/conf.d/*.conf`):

```nginx
server {
    listen 443 ssl http2;
    server_name sabit.al-maruf.sch.id;

    # === TAMBAHKAN BARIS INI ===
    client_max_body_size 64m;
    # ============================

    root /var/www/sabit.al-maruf.sch.id/public;
    ...
}
```

Jika situs pakai panel (aaPanel / RunCloud / ServerPilot / CyberPanel), cari menu **Nginx Config** → tambahkan `client_max_body_size 64m;` di dalam blok `server { ... }`.

### 2. PHP — naikkan `upload_max_filesize` & `post_max_size`

Cari `php.ini` yang aktif di server:

```bash
php -i | grep "Loaded Configuration File"
```

Edit file tersebut, cari dan ubah:

```ini
upload_max_filesize = 64M
post_max_size       = 64M
memory_limit        = 256M
```

> Catatan: `post_max_size` harus ≥ `upload_max_filesize` (idealnya sedikit lebih besar, mis. `65M`).

### 3. Reload service

```bash
# Cek syntax Nginx dulu
sudo nginx -t

# Kalau OK, reload
sudo systemctl reload nginx

# Untuk PHP-FPM (cek versi: php8.1-fpm / php8.2-fpm / php8.3-fpm)
sudo systemctl reload php8.2-fpm
# atau kalau pakai php-fpm generik
sudo systemctl reload php-fpm
```

## Verifikasi

Setelah reload, cek dari CLI server:

```bash
php -i | grep -E "upload_max_filesize|post_max_size|memory_limit"
nginx -T 2>/dev/null | grep client_max_body_size
```

Harus muncul nilai ≥ 64 MB.

Atau dari halaman admin Laravel (browser DevTools → Network), upload foto 5 MB:
- Request size di header harus terkirim penuh (tidak ada `413`).
- Status response `200` / `302` (sukses redirect).

## Nilai rekomendasi

| Use case | client_max_body_size | upload_max_filesize | post_max_size |
|---|---|---|---|
| Hanya foto (≤ 4 MB) | 8 MB | 8 MB | 10 MB |
| Foto + Video lokal (≤ 50 MB) | 64 MB | 64 MB | 65 MB |

Untuk galeri sekolah yang support upload video lokal, gunakan **64 MB** di ketiganya.
