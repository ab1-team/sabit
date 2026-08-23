# Production Hardening Runbook

Dokumen ini berisi langkah-langkah yang **harus dijalankan di server produksi**
(`/var/www/sim-akademik/` → domain `al-maruf.sch.id`, host `siupk.com`).
Repo ini sudah berisi patch yang relevan; runbook ini menjelaskan eksekusinya
di server.

> Semua langkah harus dijalankan sebagai `root` atau user deploy (bukan `www-data`).

---

## 1. Permission & ownership `.env`

```bash
cd /var/www/sim-akademik

# Lock down .env — readable only by owner.
chmod 600 .env
chown deploy:www-data .env   # owner = deploy script, group = web server

# Lock down .env.example (boleh world-readable, tapi rapikan).
chmod 644 .env.example
```

Verifikasi:
```bash
stat -c '%a %U:%G %n' .env
# Expected: 600 deploy:www-data .env
```

## 2. Rotate database password lama

Karena `.env.remote_bak` pernah ter-commit, **password DB lama harus dianggap
compromised**.

```bash
# Login MySQL sebagai root
mysql -uroot -p
```

```sql
-- Untuk central DB (lihat DB_DATABASE di .env saat ini)
ALTER USER 'user_db_central'@'localhost' IDENTIFIED BY 'NEW_RANDOM_PASSWORD_32CHARS';

-- Untuk setiap tenant DB (prefix sabit_)
-- Cari user dari CREATE DATABASE / GRANT log, atau pakai script di bawah
SELECT user, host FROM mysql.user WHERE user LIKE 'sabit%';

-- Untuk tiap user:
ALTER USER 'sabit_<tenant_id>'@'localhost' IDENTIFIED BY 'NEW_RANDOM_PASSWORD_32CHARS';
FLUSH PRIVILEGES;
```

Generate password acak:
```bash
openssl rand -base64 32
```

Lalu update `.env` di server:
```bash
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=NEW_PASSWORD/' .env
```

## 3. Hapus tracked secrets dari git history (server)

`.env.remote_bak` dan `storage/backups/*.sql` sudah di-`git rm --cached` di repo
ini, tapi history lama masih mengandung isinya. Untuk benar-benar membersihkan:

```bash
cd /var/www/sim-akademik

# Install git-filter-repo (lebih cepat dari BFG)
pip install git-filter-repo   # atau apt install git-filter-repo

# Hapus file dari seluruh history
git filter-repo --invert-paths --path .env.remote_bak --path storage/backups/

# Force push ke remote (HATI-HATI: minta konfirmasi owner dulu)
git remote add origin git@github.com:org/sim-akademik.git
git push origin --force --all
git push origin --force --tags
```

> Setelah force-push, semua kontributor harus `git pull --rebase` atau re-clone.

## 4. Set environment variables produksi

Edit `/var/www/sim-akademik/.env`:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://al-maruf.sch.id

# Multi-tenant (Stancl) — single-domain deployment
CENTRAL_DOMAIN=al-maruf.sch.id
CENTRAL_BASE_DOMAIN=al-maruf.sch.id
TENANT_BASE_DOMAIN=al-maruf.sch.id

# Session cookie shared across subdomain (jika ada) atau host-only
SESSION_DOMAIN=.al-maruf.sch.id

# Mail (ganti dengan SMTP provider beneran, BUKAN mailpit)
MAIL_MAILER=smtp
MAIL_HOST=smtp.your-provider.com
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="no-reply@al-maruf.sch.id"
```

Lalu:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Cek route central tidak duplikat:**
```bash
php artisan route:list --name=central 2>&1 | head -30
# Setiap route harus muncul SEKALI, bukan 2x.
```
Kalau masih dobel → cek `CENTRAL_BASE_DOMAIN` dan `CENTRAL_DOMAIN` di `.env`
harus berisi nilai valid (atau kosong jika single-domain).

## 5. Verifikasi pasca-hardening

```bash
# APP_DEBUG harus false
php artisan tinker --execute="echo config('app.debug') ? 'LEAK!' : 'OK';"

# Route central harus unik
php artisan route:list 2>&1 | grep -c '^| GET\|POST'   # sanity

# File permission
ls -la .env   # -rw------- (600)

# Tidak ada SQL backup ter-commit
git ls-files | grep -E '\.(sql|bak)$'   # harus kosong

# Mail bisa kirim (test forgot-password sekali)
php artisan tinker --execute="\Mail::raw('test', fn(\$m) => \$m->to('admin@al-maruf.sch.id')->subject('test'));"
```

## 6. Diskusi arsitektur (item #6 review)

Aplikasi saat ini multi-tenant (Stancl) tapi dideploy single-domain. Dua opsi:

| Opsi | Pro | Kon |
|------|-----|-----|
| **A. Lanjut multi-tenant** | Skala ke banyak sekolah tanpa deploy ulang. Konsisten dengan arsitektur sekarang. | Butuh wildcard DNS + SSL SAN, tuning DB connection pool. |
| **B. Simplify → single-tenant** | Hapus overhead tenancy, debug lebih mudah. | Harus refactor `central_domains`, route binding, DB bootstrapper. Kehilangan fleksibilitas. |

Putuskan dengan owner sebelum refactor.

---

## Quick checklist (copy-paste ke ticket)

```
[ ] chmod 600 .env
[ ] Rotate semua DB password (central + tiap tenant)
[ ] Update .env di server (APP_ENV, APP_DEBUG, CENTRAL_*, SESSION_DOMAIN, MAIL_*)
[ ] git filter-repo + force-push (HANYA setelah konfirmasi owner)
[ ] php artisan optimize
[ ] Verifikasi route:list tidak ada duplikat
[ ] Verifikasi forgot-password email terkirim
[ ] Diskusi arsitektur multi-tenant vs simplify dengan owner
```
