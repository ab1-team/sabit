# Dokumentasi Tombol "Simpan Saldo" — Laporan Keuangan

**Modul**: `Laporan Keuangan` (`resources/views/laporan-keuangan/daftar.blade.php`)
**Backend**: `LaporanController@simpanSaldo` (`app/Http/Controllers/LaporanController.php:991`)
**Tujuan**: Merekap akumulasi `jumlah` pada tabel `transaksi` (per `rekening_debit` & `rekening_kredit`) ke tabel `saldo` untuk periode `bulan/tahun` yang dipilih, sehingga Buku Besar, CaLK, dan Laba Rugi konsisten.

---

## 1. Lokasi & Tampilan Tombol

### 1.1 Tombol (Form Laporan)

| Properti | Nilai |
|---|---|
| File view | `resources/views/laporan-keuangan/daftar.blade.php` |
| Baris | `141` |
| Selector | `#btnSimpanSaldo` |
| Class | `btn btn-danger` |
| Label | `Simpan Saldo` |
| Tipe | `button` (tidak submit form) |

```html
<button type="button" id="btnSimpanSaldo" class="btn btn-danger">Simpan Saldo</button>
```

### 1.2 Kartu Penjelasan (Info Card)

- Baris `151–160` view yang sama
- Ikon `fa-floppy-disk`, judul **Simpan Saldo**
- Deskripsi: *"Menyimpan ringkasan saldo periode laporan ke database untuk arsip/riwayat."*

---

## 2. Alur Klik (Client-side)

Handler jQuery pada baris `349–366`:

```js
$('#btnSimpanSaldo').on('click', function() {
    const tahun = $('select[name="tahun"]').val();
    const bulan = $('select[name="bulan"]').val();
    if (!tahun || !bulan) return;

    const loading = Swal.fire({
        title: 'Mohon Menunggu..',
        html: 'Menyimpan saldo periode ' + (NAMA_BULAN[bulan] || '') + ' ' + tahun,
        timerProgressBar: true,
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    window.open(
        '/app/laporan-keuangan/simpan-saldo?tahun=' + tahun + '&bulan=' + bulan,
        '_blank'
    );
});
```

Setelah child window mengirim `postMessage("closed")`, popup loading ditutup dan halaman direload:

```js
window.addEventListener('message', function(e) {
    if (e.data === 'closed') {
        Swal.close();
        window.location.reload();
    }
});
```

> Peta nama bulan dideklarasikan di baris `305–308` sebagai objek `NAMA_BULAN`.

---

## 3. Route & Endpoint

| Route | Method | Controller | Middleware |
|---|---|---|---|
| `/app/laporan-keuangan/simpan-saldo` | GET | `LaporanController@simpanSaldo` | tenant (admin) (`routes/tenant-admin.php:116`) |

```php
Route::get('/laporan-keuangan/simpan-saldo', [LaporanController::class, 'simpanSaldo']);
```

---

## 4. Logika `LaporanController@simpanSaldo`

Method ini merupakan **proses tunggal per-bulan** yang menghitung dan men-upsert baris `saldo` untuk bulan terpilih, lalu otomatis maju ke bulan berikutnya.

### 4.1 Penentuan Periode

```php
$tahun = request()->get('tahun') ?: date('Y');
$bulan = str_pad(request()->get('bulan') ?: date('m'), 2, '0', STR_PAD_LEFT);

if ($bulan === '00') {
    $bulan = 12;
    $tahun = (int) $tahun - 1;
}

$start = "$tahun-01-01";
$end   = date('Y-m-t', strtotime("$tahun-$bulan-01"));
```

- Rentang agregat selalu `[tahun-01-01 .. akhir_bulan]`.
- `bulan = '00'` dipetakan ke `Desember tahun sebelumnya` agar inisialisasi tahun bisa dilakukan dengan tahun yang sama dan bulan 0.

### 4.2 Daftar Rekening Aktif

Hanya rekening yang **belum dinonaktifkan** (`tgl_nonaktif` NULL) yang ikut direkap:

```php
$rekening = Rekening::whereNull('tgl_nonaktif')->orderBy('kode_akun')->get(['kode_akun']);
$kodeList = $rekening->pluck('kode_akun')->all();
```

### 4.3 Agregasi Debit & Kredit

Agregat dijalankan sebagai **2 query langsung** (bukan `withSum` eager-load) untuk menekan jumlah round-trip MySQL:

```php
$debits = DB::table('transaksi')
    ->whereNull('deleted_at')
    ->whereBetween('tanggal_transaksi', [$start, $end])
    ->whereIn('rekening_debit', $kodeList)
    ->groupBy('rekening_debit')
    ->selectRaw('rekening_debit as kode_akun, SUM(jumlah) as total')
    ->pluck('total', 'kode_akun');

$kredits = DB::table('transaksi')
    ->whereNull('deleted_at')
    ->whereBetween('tanggal_transaksi', [$start, $end])
    ->whereIn('rekening_kredit', $kodeList)
    ->groupBy('rekening_kredit')
    ->selectRaw('rekening_kredit as kode_akun, SUM(jumlah) as total')
    ->pluck('total', 'kode_akun');
```

> Soft-deleted transaksi (`deleted_at IS NOT NULL`) otomatis di-exclude — lihat `App\Models\Transaksi` yang memakai trait `SoftDeletes`.

### 4.4 Penulisan ke Tabel `saldo` (UPSERT)

Setiap rekening dipetakan ke baris `saldo` dengan **kunci unik `(kode_akun, tahun, bulan)`**:

```php
$rows = [];
$now = now();
foreach ($kodeList as $kode) {
    $rows[] = [
        'kode_akun' => $kode,
        'tahun'     => (int) $tahun,
        'bulan'     => (int) $bulan,
        'debit'     => (float) ($debits[$kode] ?? 0),
        'kredit'    => (float) ($kredits[$kode] ?? 0),
        'updated_at' => $now,
        'created_at' => $now,
    ];
}

DB::table('saldo')->upsert(
    $rows,
    ['kode_akun', 'tahun', 'bulan'],
    ['debit', 'kredit', 'updated_at']
);
```

> **Pola penting**: proses *upsert* (bukan delete+insert) — baris saldo yang sudah ada akan **diperbarui**, baris baru akan **disisipkan**. Aman untuk dijalankan berulang kali tanpa menggandakan baris.

### 4.5 Rekurens Otomatis Bulanan

Setelah bulan berjalan selesai, method mem-forward popup ke bulan berikutnya, sampai bulan ke-12:

```php
$nextBulan = (int) $bulan + 1;
$nextTahun = (int) $tahun;
if ($nextBulan > 12) {
    return '<script>window.opener.postMessage("closed","*");window.close();</script>';
}

$url = url('/app/laporan-keuangan/simpan-saldo')
    . '?tahun=' . $nextTahun
    . '&bulan=' . str_pad($nextBulan, 2, '0', STR_PAD_LEFT);

return '<a id="next" href="' . $url . '"></a><script>document.getElementById("next").click()</script>';
```

Setelah bulan ke-12 (atau bulan berjalan di tahun sekarang), child window mengirim `postMessage("closed")` ke `window.opener` dan menutup diri.

---

## 5. Model & Tabel yang Terlibat

### 5.1 `App\Models\Transaksi`

- Tabel: `transaksi`
- Primary key: `id` (bigIncrements)
- `SoftDeletes` aktif (`deleted_at`)
- Kolom utama yang dipakai Simpan Saldo:
  - `tanggal_transaksi` — date, dipakai di filter `whereBetween`
  - `rekening_debit`, `rekening_kredit` — varchar, foreign key ke `rekening.kode_akun`
  - `jumlah` — decimal(2), diagregatkan via `SUM`
  - `user_id`, `siswa_id`, `kode_spp`, `idtp`, `keterangan`, `invoice`, `kelas`
  - `created_at`, `updated_at`, `deleted_at`
- Relasi: `rekeningDebit()`, `rekeningKredit()`, `user()`, `siswa()`, `spp()`

### 5.2 `App\Models\Rekening`

- Tabel: `rekening`
- Primary key: `kode_akun` (varchar)
- Filter aktif: `whereNull('tgl_nonaktif')`
- Kolom: `kode_akun`, `nama_akun`, `lev1`, `jenis_mutasi`, `saldo`, `tgl_nonaktif`
- Relasi: `akunLevel3()`, `transaksiDebit()`, `transaksiKredit()`, `kom_saldo()`

### 5.3 `App\Models\Saldo`

- Tabel: `saldo`
- Kolom: `id`, `kode_akun`, `bulan` (tinyint), `tahun` (smallint), `debit` (decimal 15,2), `kredit` (decimal 15,2), `created_at`, `updated_at`
- Unique key: `(kode_akun, bulan, tahun)`
- Foreign key: `kode_akun` → `rekening.kode_akun` (cascade on delete)
- Relasi: `rekening()`

---

## 6. Invariant Aplikasi pada `transaksi`

Agar hasil Simpan Saldo **akurat dan konsisten**, proses input/koreksi/hapus transaksi **wajib** mempertahankan invariant berikut.

### 6.1 Invariant Wajib Saat Insert Transaksi

| # | Invariant | Lokasi Implementasi | Tujuan |
|---|---|---|---|
| I1 | Set `tanggal_transaksi` valid (YYYY-MM-DD) | `TransaksiController::simpan`, `batal`, dll. | Dipakai di filter `whereBetween`. |
| I2 | Isi `rekening_debit` & `rekening_kredit` lengkap | Setiap jurnal | Tanpa ini, sum `jumlah` lewat `rekening_debit/rekening_kredit` = salah. |
| I3 | Isi `jumlah` numerik (decimal) | Setiap jurnal | Agregat `SUM(jumlah)`. |
| I4 | Pastikan `kode_akun` (debit/kredit) ada di `rekening` (FK terpenuhi) | Validasi sebelum insert | Mencegah transaksi yatim. |
| I5 | `tgl_transaksi` / `rekening_*` / `jumlah` konsisten | Validasi form | Cegah saldo simpang siur. |

### 6.2 Invariant Wajib Saat Update / Koreksi

| # | Invariant | Lokasi |
|---|---|---|
| I6 | **Bisa mengubah `tanggal_transaksi`** | `TransaksiController::update` |
| I7 | **Bisa mengubah `rekening_debit/kredit`** | `TransaksiController::update` |
| I8 | **Bisa mengubah `jumlah`** | `TransaksiController::update` |

> Perubahan pada kolom di atas **wajib** memancing operator untuk menjalankan ulang Simpan Saldo agar saldo Buku Besar ikut berubah.

### 6.3 Invariant Wajib Saat Delete / Batal

| # | Invariant | Lokasi |
|---|---|---|
| I9 | **Soft delete only** (`update deleted_at`), **JANGAN hard delete** | `TransaksiController::hapus`, `::batal`, `::delete()` |
| I10 | Saat reversal, buat transaksi baru dengan `jumlah * -1` dan `keterangan` berawalan `KOREKSI idt (...)` | `TransaksiController::reversal` |
| I11 | Hindari `Transaksi::forceDelete()` di manapun | - |

> Query Simpan Saldo memfilter `whereNull('deleted_at')`, sehingga transaksi yang di-soft-delete **tidak ikut terhitung**. Ini sudah benar secara desain, tapi mensyaratkan I9/I11 ditegakkan.

### 6.4 Invariant Saat Restore

| # | Invariant | Lokasi |
|---|---|---|
| I12 | `deleted_at` di-clear (`restore()`) akan otomatis membuat transaksi kembali dihitung pada Simpan Saldo berikutnya. | - |

---

## 7. Ringkasan Alur

```
[User klik Simpan Saldo]
        │
        ▼
GET /app/laporan-keuangan/simpan-saldo?tahun=YYYY&bulan=MM
        │
        ▼
LaporanController@simpanSaldo
        │
        ├── Tentukan start = tahun-01-01, end = akhir_bulan
        ├── Ambil daftar rekening aktif (tgl_nonaktif IS NULL)
        ├── Agregat SUM(jumlah) untuk rekening_debit  → $debits
        ├── Agregat SUM(jumlah) untuk rekening_kredit → $kredits
        │
        ▼
DB::table('saldo')->upsert(
    rows, ['kode_akun','tahun','bulan'], ['debit','kredit','updated_at']
)
        │
        ▼
bulan += 1 → buka ulang URL tsb di popup yang sama
        │
        └── bulan > 12 → postMessage('closed') → tutup popup
                          └── window.opener: Swal.close() + reload()
```

---

## 8. Catatan Penting untuk Developer

1. **Recreate child window** setiap bulan — endpoint menggunakan `<a id="next">` yang otomatis diklik. Jangan ubah ke JSON/redirect biasa, karena popup akan kehilangan `window.opener`.
2. **Pakai `upsert`, bukan `delete+insert`** — kunci unik `(kode_akun, tahun, bulan)` pada tabel `saldo` menjamin idempoten. Pola delete+insert di versi lama rentan menggandakan baris bila migrasi data gagal di tengah jalan.
3. **Filter `tgl_nonaktif` IS NULL** pada `rekening` — rekening yang sudah dinonaktifkan tidak ikut direkap dan tidak masuk tabel `saldo`. Jika nanti diaktifkan kembali, perlu dijalankan ulang Simpan Saldo untuk bulan-bulan yang terlewat.
4. **Tidak ada field `lokasi` di tabel `saldo`/`transaksi`** — tenancy sudah memisahkan database per tenant, sehingga semua query di method ini otomatis scope ke DB tenant aktif (tidak perlu filter manual).
5. **Setelah import transaksi dari modul lain** (mis. `AdminTransaksi` / `SystemController`), operator **wajib** menjalankan tombol Simpan Saldo agar Buku Besar ikut ter-update untuk bulan-bulan yang terdampak.
6. **Performa** — method menggunakan **2 query agregat + 1 upsert** per bulan, bukan `withSum` per rekening. Untuk N rekening aktif, total query per bulan = 3 (vs N×3 pada pendekatan lama).

---

## 9. Trigger Database pada Tabel `transaksi`

Selain tombol **Simpan Saldo** (batch, dipicu manual dari UI), tabel `transaksi` juga memiliki **3 trigger MySQL** yang menjaga tabel `saldo` tetap ter-update secara **otomatis & real-time** setiap kali ada `INSERT`, `UPDATE`, atau `DELETE` pada `transaksi`.

> Nama tabel di bawah ini sudah disesuaikan **tanpa suffix `_95`** (mengikuti skema per-database tenancy yang dipakai aplikasi — lihat catatan #4 di atas), dan kolom tanggal disesuaikan ke `tanggal_transaksi` sesuai struktur tabel `transaksi` yang sudah didokumentasikan di section 5.1 (bukan `tgl_transaksi`).

### 9.1 `create_saldo_debit` (AFTER INSERT)

```sql
CREATE TRIGGER `create_saldo_debit` AFTER INSERT ON `transaksi`
FOR EACH ROW
BEGIN
    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(NEW.rekening_debit, '.', ''), YEAR(NEW.tanggal_transaksi), LPAD(MONTH(NEW.tanggal_transaksi), 2, '0')),
        NEW.rekening_debit,
        YEAR(NEW.tanggal_transaksi),
        LPAD(MONTH(NEW.tanggal_transaksi), 2, '0'),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi))
    )
    ON DUPLICATE KEY UPDATE
        debit  = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)),
        kredit = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi));

    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(NEW.rekening_kredit, '.', ''), YEAR(NEW.tanggal_transaksi), LPAD(MONTH(NEW.tanggal_transaksi), 2, '0')),
        NEW.rekening_kredit,
        YEAR(NEW.tanggal_transaksi),
        LPAD(MONTH(NEW.tanggal_transaksi), 2, '0'),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi))
    )
    ON DUPLICATE KEY UPDATE
        debit  = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)),
        kredit = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi));
END
```

**Apa yang terjadi:** setiap ada transaksi baru, trigger ini menghitung ulang **dari nol** (full `SUM`, bukan increment) total `debit` dan `kredit` untuk **dua akun sekaligus** — akun di `rekening_debit` maupun akun di `rekening_kredit` — lalu meng-*upsert* baris `saldo` untuk periode `(kode_akun, tahun, bulan)` terkait. Meski namanya `create_saldo_debit`, isinya sebenarnya menangani **kedua sisi jurnal** (debit dan kredit), bukan cuma debit.

`id` pada tabel `saldo` di sini dibangun sebagai string sintetis: `kode_akun` (tanpa titik) + `tahun` + `bulan` (2 digit) — misalnya rekening `1.1.01.01` pada Agustus 2026 → id ``110101202608``. String ini berfungsi sebagai pengganti kunci unik `(kode_akun, tahun, bulan)` agar `ON DUPLICATE KEY UPDATE` bisa bekerja tanpa harus tahu ID numeriknya lebih dulu.

### 9.2 `delete_saldo_debit` (AFTER DELETE)

```sql
CREATE TRIGGER `delete_saldo_debit` AFTER DELETE ON `transaksi`
FOR EACH ROW
BEGIN
    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(OLD.rekening_debit, '.', ''), YEAR(OLD.tanggal_transaksi), LPAD(MONTH(OLD.tanggal_transaksi), 2, '0')),
        OLD.rekening_debit,
        YEAR(OLD.tanggal_transaksi),
        MONTH(OLD.tanggal_transaksi),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi))
    )
    ON DUPLICATE KEY UPDATE
        debit  = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)),
        kredit = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = OLD.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi));

    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(OLD.rekening_kredit, '.', ''), YEAR(OLD.tanggal_transaksi), LPAD(MONTH(OLD.tanggal_transaksi), 2, '0')),
        OLD.rekening_kredit,
        YEAR(OLD.tanggal_transaksi),
        MONTH(OLD.tanggal_transaksi),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = OLD.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi))
    )
    ON DUPLICATE KEY UPDATE
        debit  = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = OLD.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi)),
        kredit = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = OLD.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(OLD.tanggal_transaksi), '-01-01') AND LAST_DAY(OLD.tanggal_transaksi));
END
```

**Apa yang terjadi:** simetris dengan trigger insert — dijalankan setelah sebuah baris `transaksi` **benar-benar dihapus** (`DELETE`, bukan soft delete), lalu menghitung ulang saldo periode terkait tanpa baris tersebut.

**Penting:** karena Invariant **I9** mewajibkan aplikasi hanya melakukan **soft delete** (`UPDATE ... SET deleted_at = ...`) dan **melarang** `hard delete`/`forceDelete()`, trigger ini **seharusnya tidak pernah terpicu** dalam alur normal aplikasi. Trigger ini baru relevan kalau ada operasi hapus manual langsung di database (migrasi data, cleanup, query manual di phpMyAdmin) yang melanggar I9.

### 9.3 `update_saldo_debit` (AFTER UPDATE)

```sql
CREATE TRIGGER `update_saldo_debit` AFTER UPDATE ON `transaksi`
FOR EACH ROW
BEGIN
    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(NEW.rekening_debit, '.', ''), YEAR(NEW.tanggal_transaksi), LPAD(MONTH(NEW.tanggal_transaksi), 2, '0')),
        NEW.rekening_debit,
        YEAR(NEW.tanggal_transaksi),
        LPAD(MONTH(NEW.tanggal_transaksi), 2, '0'),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi))
    )
    ON DUPLICATE KEY UPDATE
        debit  = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)),
        kredit = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_debit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi));

    INSERT INTO saldo (`id`, `kode_akun`, `tahun`, `bulan`, `debit`, `kredit`)
    VALUES (
        CONCAT(REPLACE(NEW.rekening_kredit, '.', ''), YEAR(NEW.tanggal_transaksi), LPAD(MONTH(NEW.tanggal_transaksi), 2, '0')),
        NEW.rekening_kredit,
        YEAR(NEW.tanggal_transaksi),
        LPAD(MONTH(NEW.tanggal_transaksi), 2, '0'),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)),
        (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi))
    )
    ON DUPLICATE KEY UPDATE
        debit  = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_debit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi)),
        kredit = (SELECT SUM(jumlah) FROM transaksi WHERE rekening_kredit = NEW.rekening_kredit
            AND tanggal_transaksi BETWEEN CONCAT(YEAR(NEW.tanggal_transaksi), '-01-01') AND LAST_DAY(NEW.tanggal_transaksi));
END
```

**Apa yang terjadi:** dipicu setelah **setiap** `UPDATE` pada `transaksi` — termasuk soft delete (karena soft delete = `UPDATE deleted_at`) dan koreksi manual (I6–I8: ubah `tanggal_transaksi`, `rekening_debit/kredit`, atau `jumlah`). Trigger menghitung ulang saldo untuk periode `NEW.rekening_debit` dan `NEW.rekening_kredit` (nilai **setelah** update).

### 9.4 Hubungan dengan Tombol "Simpan Saldo"

Dengan trigger ini terpasang, tabel `saldo` sebenarnya sudah **auto-update secara real-time** setiap kali ada perubahan di `transaksi` — tanpa perlu menunggu operator klik tombol **Simpan Saldo**. Kedua mekanisme ini berjalan berdampingan:

| | Trigger (section 9) | Tombol Simpan Saldo (section 4) |
|---|---|---|
| Pemicu | Otomatis, per baris (`INSERT`/`UPDATE`/`DELETE`) | Manual, per klik, per bulan |
| Cakupan | Hanya akun yang tersentuh baris yang berubah | Seluruh rekening aktif (`tgl_nonaktif IS NULL`) untuk 1 bulan terpilih |
| Filter `deleted_at` | **Tidak ada** — subquery `SUM(jumlah)` di trigger tidak mengecualikan baris yang sudah soft-delete | Eksplisit `whereNull('deleted_at')` |

Karena perbedaan filter `deleted_at` ini, tombol **Simpan Saldo** tetap penting sebagai mekanisme **rekonsiliasi/perbaikan** — dijalankan untuk memastikan `saldo` konsisten dengan aturan "transaksi soft-deleted tidak dihitung", yang tidak dijamin oleh trigger.

### 9.5 Catatan & Potensi Masalah untuk Developer

1. **Trigger tidak memfilter `deleted_at`** — subquery `SUM(jumlah)` di ketiga trigger menghitung **semua** baris `transaksi` pada rentang tanggal tsb, termasuk yang sudah soft-delete (`deleted_at IS NOT NULL`). Ini berbeda dari `LaporanController@simpanSaldo` yang eksplisit `whereNull('deleted_at')`. Akibatnya, saldo hasil trigger bisa **lebih besar** dari saldo hasil tombol Simpan Saldo selama ada transaksi yang sudah dibatalkan (soft delete) di periode tsb, sampai tombol Simpan Saldo dijalankan ulang untuk mengoreksinya.

   > **FIXED di migration `2026_08_31_000001_create_transaksi_saldo_triggers.php`** — subquery `SUM(jumlah)` di ketiga trigger sudah ditambahkan filter `AND deleted_at IS NULL`, sehingga agregat konsisten dengan logika `LaporanController@simpanSaldo`. Tenant baru otomatis mendapatkan versi trigger yang sudah difilter ini via pipeline `TenantCreated → MigrateDatabase`. Tenant lama perlu menjalankan `php artisan tenants:migrate --tenants=<id>` secara manual untuk memperbarui triggernya.
2. **`update_saldo_debit` tidak menangani perubahan akun (I7)** — trigger ini hanya meng-upsert saldo untuk `NEW.rekening_debit` / `NEW.rekening_kredit`. Kalau operator mengoreksi transaksi dan **mengganti** `rekening_debit`/`rekening_kredit` ke akun lain, saldo akun **lama** (`OLD.rekening_debit`/`OLD.rekening_kredit`) tidak ikut dihitung ulang oleh trigger dan akan **basi** (tetap mengandung transaksi yang sebenarnya sudah pindah akun) sampai Simpan Saldo dijalankan ulang.
3. **Kolom tanggal harus `tanggal_transaksi`** — versi asli trigger yang diberikan memakai `tgl_transaksi`. Nama ini **tidak cocok** dengan struktur tabel `transaksi` aplikasi ini (section 5.1), yang kolomnya bernama `tanggal_transaksi`. Kalau trigger dibuat memakai `tgl_transaksi` apa adanya, `CREATE TRIGGER` akan gagal (`Unknown column`) karena kolom tsb tidak ada di tabel. Versi di section 9.1–9.3 sudah disesuaikan.
4. **Penamaan trigger menyesatkan** — `create_saldo_debit`/`update_saldo_debit` terdengar seolah hanya menangani sisi debit, padahal isinya menangani **debit maupun kredit** sekaligus (2 blok `INSERT ... ON DUPLICATE KEY UPDATE`). Perlu diperhatikan saat maintenance agar tidak salah asumsi hanya perlu duplikasi untuk sisi kredit.
5. **`delete_saldo_debit` praktis tidak pernah jalan** dalam operasi normal karena Invariant I9 (soft delete only). Trigger ini hanya berguna sebagai jaring pengaman kalau suatu saat ada hard delete yang melanggar kebijakan — bukan bagian dari alur bisnis utama.
6. **Rekomendasi:** karena adanya celah di atas (poin 2), trigger sebaiknya diperlakukan sebagai **cache real-time yang best-effort**, bukan sumber kebenaran akhir. Tombol **Simpan Saldo** tetap wajib dijalankan secara berkala (mis. akhir bulan, atau setelah proses koreksi/reversal transaksi massal) untuk memastikan tabel `saldo` benar-benar akurat.
