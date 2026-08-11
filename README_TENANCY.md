# Multi-Tenancy — stancl/tenancy v3

Aplikasi SAbIT berjalan sebagai **multi-tenant (multi-database)**.

## Struktur Folder (ringkas, mudah dikontrol)

```
app/
├── Http/Controllers/
│   ├── Tenant/                       ← Tenant Console (pusat / master admin)
│   │   ├── AuthController.php        ← login pusat (guard: tenant)
│   │   ├── DashboardController.php
│   │   ├── AdminInvoiceController.php
│   │   ├── MigrasiSiswaController.php
│   │   ├── TransaksiController.php
│   │   ├── TenantController.php      ← CRUD daftar sekolah
│   │   └── School/                   ← Sub-CRUD per sekolah (Profil, User, TA, COA, JP)
│   │       ├── BaseSchoolController.php
│   │       ├── ProfilSekolahController.php
│   │       ├── UserOperatorController.php
│   │       ├── TahunAkademikController.php
│   │       ├── JenisPembayaranController.php
│   │       └── CoaController.php
│   └── School/                       ← Controller untuk halaman sekolah (subdomain)
│       └── HakAksesController.php
└── Models/
    ├── Tenant.php                    ← Model stancl/tenancy
    ├── TenantUser.php                ← User pusat (admin pusat)
    ├── User.php                      ← User sekolah (per tenant)
    └── ... (model-model tenant)

resources/views/
├── tenant/                           ← View Tenant Console (pusat)
│   ├── dashboard.blade.php
│   ├── login.blade.php
│   ├── migrasi-siswa.blade.php
│   ├── invoice/
│   ├── transaksi/
│   ├── partials/
│   │   ├── topbar.blade.php
│   │   └── tenant_subnav.blade.php  ← Sub-nav halaman detail sekolah
│   └── tenant/                       ← View CRUD sekolah + sub-CRUD
│       ├── index.blade.php
│       ├── create.blade.php
│       ├── edit.blade.php
│       ├── show.blade.php
│       ├── profil.blade.php
│       ├── user.blade.php
│       ├── tahun-akademik.blade.php
│       ├── coa.blade.php
│       └── jenis-pembayaran.blade.php
└── sekolah/                          ← View halaman sekolah (subdomain)
    ├── hak-akses.blade.php
    └── partials/topbar.blade.php

routes/
├── web.php                           ← Routes pusat (Tenant Console)
├── tenant.php                        ← Routes sekolah (subdomain)
└── ...
```

## Akses URL

- **Tenant Console (pusat)**: `http://pembayaran-spp.test/`
  - Login di `/login` (guard `tenant`, tabel `admin_user`).
  - Menu: Dashboard, Tenant (CRUD sekolah), Migrasi, Invoices.
  - Klik tenant → halaman detail (Overview, Profil Sekolah, User Operator, Tahun Akademik, COA, Jenis Pembayaran).

- **Sekolah**: `http://<id>.pembayaran-spp.test/`
  - Contoh: `http://demo.pembayaran-spp.test/`
  - Login pakai akun `users` (operator sekolah, guard `web`).
  - Menu: Dashboard, Transaksi, Siswa, Daftar Kelas, Pengaturan, Laporan, Hak Akses.

## Konfigurasi DNS / Host

Tambahkan ke `C:\Windows\System32\drivers\etc\hosts` (Laragon biasanya otomatis):

```
127.0.0.1   pembayaran-spp.test
127.0.0.1   demo.pembayaran-spp.test
127.0.0.1   sma-bina-jaya.pembayaran-spp.test
```

`.env` di root project:

```
CENTRAL_DOMAIN=pembayaran-spp.test
DB_CONNECTION_CENTRAL=central
DB_HOST_CENTRAL=103.112.245.8
DB_PORT_CENTRAL=3306
DB_DATABASE_CENTRAL=sinkrone_sabit
DB_USERNAME_CENTRAL=sinkrone_alm_app
DB_PASSWORD_CENTRAL=sinkrone_alm_app
TENANT_DB_PREFIX=sinkrone_sabit_
```

## Alur Menambah Sekolah Baru

Lewat **Tenant Console → Tenant → Tambah Sekolah**:

1. Isi **ID Tenant** (tanpa spasi, mis: `sma-bina-jaya`).
2. Isi **Nama Sekolah** (mis: `SMA Bina Jaya`).
3. Isi **Subdomain/Domain** (mis: `sma-bina-jaya.pembayaran-spp.test`).
4. Submit. Sistem otomatis:
   - Membuat record di tabel `tenants` & `domains`.
   - Membuat database `tenant<id>`.
   - Menjalankan migration tenant dari `database/migrations/tenant/`.
   - Menjalankan `TenantDatabaseSeeder` (default operator sekolah + menu + COA).

Setelah tenant dibuat, klik tenant di daftar → halaman **Overview** (statistik) → sub-menu:

- **Profil Sekolah** — edit identitas, telepon, alamat, jatuh tempo.
- **User Operator** — tambah/edit/hapus/reset-password user sekolah.
- **Tahun Akademik** — CRUD + aktifkan tahun ajaran.
- **Jenis Pembayaran** — CRUD master jenis pembayaran.
- **COA** — lihat + aktifkan/nonaktifkan rekening.

## Default Akun Setiap Tenant

| Username | Password   | Keterangan         |
| -------- | ---------- | ------------------ |
| `admin`  | `password` | Superadmin sekolah |

Ganti password setelah login pertama (atau lewat Tenant Console → User Operator → Reset Password).

## Default Akun Pusat (Tenant Console)

| Email                | Password | Keterangan       |
| -------------------- | -------- | ---------------- |
| `admin@gmail.com`    | …        | Admin pusat      |

## Perintah Penting

```bash
# Lihat semua tenant
php artisan tenants:list

# Jalankan ulang migration semua tenant
php artisan tenants:migrate

# Seed ulang tenant tertentu
php artisan tenants:seed --tenants=sma-bina-jaya

# Rollback migration tenant tertentu
php artisan tenants:rollback --tenants=sma-bina-jaya
```

## Catatan Teknis

- Setiap tenant punya storage path sendiri (`storage/app/tenant<id>/`).
- Cache di-scope per tenant via tag.
- `CreateDatabase` job di-skip (DB tenant di-pre-create manual di shared hosting).
- `template_tenant_connection` di-set ke `tenant_template` di `config/database.php`.
- `TENANT_DB_PREFIX` di `.env` menentukan prefix nama DB tenant.

## ⚠️ Pre-Create Database Tenant

Di shared hosting `sinkrone_alm_app` yang **tidak punya hak CREATE DATABASE**, admin hosting harus pre-create database kosong untuk setiap tenant dengan format: `sinkrone_sabit_<tenant_id>`.

Contoh:

| Tenant ID          | Database name                          |
| ------------------ | -------------------------------------- |
| `sma-bina-jaya`    | `sinkrone_sabit_sma-bina-jaya`         |
| `demo`             | `sinkrone_sabit_demo`                  |

Override nama DB per-tenant jika tidak sesuai format:

```php
$tenant = App\Models\Tenant::find('sabit-demo');
$tenant->setInternal('db_name', 'sinkrone_sabit_demo');
$tenant->save();
```

Atau via skrip `php bind_tenant_dbname.php` untuk pemetaan manual.
