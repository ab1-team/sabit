# Dokumentasi Alur "Buat Tenant Baru" — Master Console

**Modul**: Master Console (Pusat) — `TenantController`
**Route**: `POST /tenant` → `tenant.tenant.store` (`routes/web.php:113`)
**Controller**: `App\Http\Controllers\Tenant\TenantController@store` (`app/Http\Controllers/Tenant/TenantController.php:113`)
**View hasil**: `resources/views/tenant/tenant/index.blade.php` (di-redirect ke `GET /tenant`)

---

## 1. Tujuan

Membuat record tenant baru (sekolah) di central database, lengkap dengan 2 domain
(landing page & panel admin). Tiap tenant punya database sendiri dengan nama
`<TENANT_DB_PREFIX><tenant_id>`, contoh: `sabit_sma1`.

---

## 2. Alur Lengkap (end-to-end)

### 2.1 Dari sisi pengguna

1. User login ke Master Console via `/login` (host central, mis. `sabit.test`).
2. Buka menu **Sekolah** (`GET /tenant`) — `tenant.tenant.index`.
3. Klik tombol **Tambah Sekolah** → modal form muncul (SweetAlert-style modal).
4. Isi field: `Nama Sekolah`, `ID Tenant`, `Email Admin`,
   `Domain Landing Page`, `Domain Administrator`.
5. Klik **Simpan & Provisioning** → submit `POST /tenant`.

### 2.2 Dari sisi server

`TenantController@store` memproses request dengan urutan:

| # | Aksi | Lokasi kode |
|---|---|---|
| 1 | Validasi input (`id`, `nama_sekolah`, `domain_landing`, `domain_admin`, `email`) | `TenantController.php:115-123` |
| 2 | Cek konflik domain di tabel `domains` (bentrok dengan tenant lain) | `TenantController.php:130-136` |
| 3 | Buka DB transaction di koneksi `central` | `TenantController.php:138` |
| 4 | `Tenant::create(...)` — generate `tenancy_db_name = <prefix><id-sanitized>` | `TenantController.php:140-148` |
| 5 | Insert domain landing (`type = TYPE_LANDING`) | `TenantController.php:151-154` |
| 6 | Insert domain admin (`type = TYPE_ADMIN`) | `TenantController.php:157-160` |
| 7 | Flush cache `domain:type:{host}` (TTL 2 jam) untuk kedua domain | `TenantController.php:168-169` |
| 8 | Flush `TenantContext` cache (selector sidebar super-admin) | `TenantController.php:172` |
| 9 | Forget cache `tenant_map_simple` (modul invoice pusat) | `TenantController.php:174` |
| 10 | Redirect ke `tenant.tenant.index` dengan flash `success` | `TenantController.php:176-177` |

### 2.3 Yang terjadi setelah redirect

- Browser mengirim `GET /tenant` (302 → 200).
- `TenantController@index` mengembalikan `view('tenant.tenant.index')`.
- View memakai DataTable server-side (`tenant.tenant.data`) yang otomatis reload
  daftar tenant via AJAX. Pesan sukses ditampilkan via SweetAlert toast
  (`index.blade.php:411-413`).
- Klik tombol **Kelola** (ikon gear) di kolom Aksi → `GET /tenant/{tenant}` →
  `TenantController@show` → redirect ke `tenant.tenant.profil.index`
  (`ProfilSekolahController@index`).

---

## 3. Field & Aturan Validasi

| Field | Aturan | Catatan |
|---|---|---|
| `id` | `required`, `string`, `max:64`, unik di `tenants.id` | Lowercase letters, angka, dash (`pattern="^[a-z0-9-]+$"` di form). Setelah dibuat, **tidak bisa diubah** — di-`readonly` di form edit. |
| `nama_sekolah` | `required`, `string`, `max:191` | Bebas |
| `email` | `nullable`, `email`, `max:191` | Opsional, untuk kontak admin |
| `domain_landing` | `required`, `string`, `max:191`, `regex:/^[a-z0-9.-]+$/i` | Domain publik landing page (mis. `sma.sch.id`) |
| `domain_admin` | `required`, `string`, `max:191`, `regex:/^[a-z0-9.-]+$/i`, `different:domain_landing` | Domain panel admin sekolah (mis. `admin-sma.sch.id`) |

Domain dicek **sebelum** transaction untuk mencegah tenant setengah-jadi
(`TenantController.php:128-136`).

---

## 4. Middleware yang Melindungi Route

Routes tenant master console dibungkus di `routes/web.php:105-203`:

```php
Route::group(['middleware' => ['auth:tenant'], 'prefix' => ''], function () {
    // /locale/switch, /dashboard, /tenant, /tenant/{tenant}/*
});
```

Middleware yang aktif saat request:

| Middleware | Sumber | Fungsi |
|---|---|---|
| `web` (group) | `app/Http/Kernel.php:59-69` | Session, cookies, CSRF, share `$errors`, `TenantContext`, `SetTenantLocale` |
| `auth:tenant` | alias | Pakai guard `tenant` (provider `tenant_users` → `App\Models\Tenant\TenantAdminUser` table `admin_user`) |
| `ShareErrorsFromSession` | `web` group | Inject variabel `$errors` ke view (penting untuk `tenant.tenant.index` line 2) |
| `TenantContext` | `web` group | Share data tenant aktif ke view (untuk sidebar selector) |
| `SetTenantLocale` | `web` group | Set locale sesuai preferensi user |

Penting: middleware `ShareErrorsFromSession` **wajib** ada di group `web` —
tanpanya, view `tenant.tenant.index` akan throw `Undefined variable $errors`
pada baris:

```php
$initialModalOpen = $errors->any() || in_array($modalAction ?? null, ['create', 'edit'], true);
```

---

## 5. View yang Terlibat

| View path | Lokasi | Dipakai oleh |
|---|---|---|
| `tenant.tenant.index` | `resources/views/tenant/tenant/index.blade.php` | `index()` & hasil redirect `store()` |
| `tenant.tenant._formulir` | `resources/views/tenant/tenant/_formulir.blade.php` | Partial form (create & edit) |
| `tenant.partials.bilah-atas` | `resources/views/tenant/partials/bilah-atas.blade.php` | Top nav (require `Auth::guard('tenant')->user()`) |
| `tenant.tenant.profil` | `resources/views/tenant/tenant/profil.blade.php` | Halaman profil (target tombol "Kelola") |
| `tenant.partials.tenant_subnav` | `resources/views/tenant/partials/tenant_subnav.blade.php` | Sub-nav per-tenant |
| `tenant.tenant._formulir_profil` | `resources/views/tenant/tenant/_formulir_profil.blade.php` | Partial form profil |

View `index` dan `profil` keduanya `@include('tenant.partials.bilah-atas')`
yang membaca user login dari `Auth::guard('tenant')->user()`. Pastikan
user sudah login sebelum akses — jika tidak, akan muncul error
`Attempt to read property "nama_lengkap" on null`.

---

## 6. Troubleshooting

### 6.1 Database tenant jadi, tapi view "gagal dibuka"

**Gejala**: Submit form sukses, tenant tersimpan di DB, tapi halaman tujuan
setelah redirect blank / error 500 / tidak muncul apa-apa.

**Cek hal berikut secara berurutan**:

1. **Cache browser**: Hard refresh (`Ctrl+Shift+R` / `Cmd+Shift+R`).
   JS `nprogress` di `bilah-atas.blade.php:280-294` bisa stuck di UI.

2. **CSRF token kadaluarsa**: Form `@csrf` di `index.blade.php:115`.
   Logout/login ulang untuk refresh token.

3. **View error 500**: Buka `storage/logs/laravel.log` dan cari entry
   terbaru dengan timestamp saat submit. Yang sering muncul:
   - `Undefined variable $errors` → middleware `ShareErrorsFromSession`
     tidak aktif. Periksa route grup `web` di `app/Http/Kernel.php:59-69`.
   - `Attempt to read property "nama_lengkap" on null` → user belum
     login. Biasanya karena guard salah. Cek `config/auth.php:47-50`.

4. **Domain konflik**: Kalau `domain_landing` atau `domain_admin` sudah
   dipakai tenant lain, controller `back()->withErrors(...)` — view akan
   terbuka kembali dengan modal form + pesan error merah. **Bukan blank**.

5. **Session driver**: `.env` `SESSION_DRIVER=file` (default). Folder
   `storage/framework/sessions` harus writable.

6. **Flush cache**: Setelah error, jalankan:
   ```bash
   php artisan view:clear
   php artisan cache:clear
   php artisan config:clear
   ```

### 6.2 Halaman login tidak muncul

**Cek**:

1. Host yang diakses adalah **central host** — sesuai
   `config('tenancy.central_domains')` yang baca `CENTRAL_DOMAIN` & `CENTRAL_BASE_DOMAIN`
   di `.env`. Cek dengan `php artisan tinker` →
   `config('tenancy.central_domains')`.
2. `routes/web.php:218-249` skip registrasi route central kalau host
   bukan central host. Tanpa route `/login`, host akan return 404.
3. Apache/Nginx virtual host: `ServerName`/`server_name` harus match
   `CENTRAL_DOMAIN`. Cek `C:\laragon\etc\apache2\sites-enabled\`
   atau nginx conf.

### 6.3 DataTables kosong / "Tidak ada tenant yang cocok."

1. Buka DevTools → Network → request `GET /tenant/data`.
2. Cek response JSON. Kalau `data: []` padahal DB ada tenant, kemungkinan:
   - Query `Tenant::query()->with('domains')` kena eager-load error.
   - Lihat `app/Http/Controllers/Tenant/TenantController.php:31-111`.

### 6.4 Logout setelah submit (session hilang)

Kalau setelah submit tenant user tiba-tiba logout dan redirect ke `/login`:
- Kemungkinan session lama di-regenerate di tempat lain saat `store()`.
- Cek `AuthController@login` (`app/Http/Controllers/Tenant/AuthController.php:25-57`)
  line 37: `$request->session()->regenerate()` — ini hanya jalan saat
  login, tidak terkait store.

---

## 7. Testing yang Sudah Dilakukan

Verifikasi end-to-end via `tests/Feature` atau simulasi HTTP via `php artisan` script:

| Step | URL | Method | Expected |
|---|---|---|---|
| 1 | `/login` | GET | 200 (view login) |
| 2 | `/login` | POST email+password | 302 → `/dashboard` |
| 3 | `/tenant` | GET | 200 (view index) |
| 4 | `/tenant` | POST data tenant | 302 → `/tenant` (flash success) |
| 5 | `/tenant` | GET (setelah redirect) | 200 (view index + toast sukses) |
| 6 | `/tenant/{id}` | GET | 302 → `/tenant/{id}/profil` |
| 7 | `/tenant/{id}/profil` | GET | 200 (view profil) |

Semua step mengembalikan status sesuai expected dengan user pusat
`admin@gmail.com` / password `password` (lihat `database/seeders/TenantAdminUserSeeder.php`).

---

## 8. Referensi Berkas

| Berkas | Peran |
|---|---|
| `app/Http/Controllers/Tenant/TenantController.php` | CRUD tenant + DataTables source |
| `app/Http/Controllers/Tenant/ProfilSekolahController.php` | Profil per-tenant (target show) |
| `app/Http/Controllers/Tenant/AuthController.php` | Login/logout pusat |
| `app/Http/Middleware/EnsureDomainType.php` | Validasi tipe domain (admin/landing) |
| `app/Http/Middleware/TenantContext.php` | Share tenant aktif + flush cache |
| `app/Models/Tenant.php` | Model tenant + `landingDomain()`/`adminDomain()` |
| `app/Models/Domain.php` | Model domain dengan `TYPE_LANDING`/`TYPE_ADMIN` |
| `app/Support/HostContext.php` | Deteksi host central vs tenant |
| `config/tenancy.php` | `central_domains`, `prefix`, `template_tenant_connection` |
| `config/auth.php` | Guard `tenant` & provider `tenant_users` |
| `routes/web.php` | Route master console (line 105-203) |
| `resources/views/tenant/tenant/index.blade.php` | View utama daftar tenant |
| `resources/views/tenant/tenant/profil.blade.php` | View profil per-tenant |
| `resources/views/tenant/partials/bilah-atas.blade.php` | Top nav (header + side drawer) |
| `resources/views/tenant/login.blade.php` | Halaman login pusat |
| `database/seeders/TenantAdminUserSeeder.php` | Default user pusat |
| `lang/id/tenant.php` | String terjemahan ID |
