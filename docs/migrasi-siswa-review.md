# Review & Perbaikan Template Migrasi Siswa

> Dokumen ini merangkum hasil review fitur **Migrasi Siswa** (`/migrasi/siswa`) dan perubahan yang dilakukan untuk menghilangkan duplikasi, melengkapi field yang hilang, dan menutup beberapa bug pemfilteran.

Lokasi kode terkait:

- `app/Exports/MigrasiSiswaTemplateExport.php` — generator template Excel
- `app/Imports/MigrasiSiswaImport.php` — handler `Excel::import()`
- `app/Http/Controllers/Tenant/MigrasiSiswaController.php` — controller (`index`, `template`, `preview`, `previewQuickKurikulum`, `import`)
- `app/Services/SiswaService.php` — service bersama untuk create/edit siswa
- `app/Http/Controllers/SiswaController.php` — controller form manual (memiliki `nominalSppByTahun`)
- `resources/views/tenant/migrasi-siswa.blade.php` — UI upload + modal auto-create
- `routes/web.php` (baris 188-193) — 5 → 6 route untuk fitur ini

---

## Ringkasan Perubahan

| # | Bagian | Tindakan |
|---|---|---|
| 1 | `SiswaService` | Tambah `resolveDefaultSppNominal()` (refactor logika `JenisBiaya` dari `SiswaController`). |
| 2 | `SiswaController` | `nominalSppByTahun` delegasi ke service (1 sumber kebenaran). |
| 3 | `MigrasiSiswaTemplateExport` | Hapus `tingkat`; tambah `kode_jurusan`, `kode_ruangan`, `status_awal`, `tgl_masuk`, `spp_nominal`. |
| 4 | `MigrasiSiswaImport` | Baca field baru; validasi `kode_ruangan` ke tabel `ruangan`; validasi `kode_jurusan` ke tabel `jurusan`; pakai `kelas.tingkat` (bukan dari Excel); set `anggota_kelas.spp_nominal`; teruskan nominal ke `generateSppBulanan`. |
| 5 | `MigrasiSiswaController` | `preview()` kembalikan `missing_kode_ruangan` & `missing_kode_jurusan`; tambah endpoint `previewQuickJurusan`; tambah `new_ruangan` & `new_jurusan` di validator & handler `import()`; fix filter `kelas` di `index()` (kolom `tahun_akademik_id`/`jurusan_id` tidak pernah ada di tabel `kelas`). |
| 6 | `resources/views/tenant/migrasi-siswa.blade.php` | Chain modal: Ruangan → Jurusan → Kelas, lalu submit. Handler `submitImport` kirim 3 array ke server. |
| 7 | `routes/web.php` | Tambah `POST /migrasi/siswa/preview/quick-jurusan`. |

---

## Keputusan Desain

| Topik | Keputusan | Alasan |
|---|---|---|
| `tingkat` | Hapus dari template. | `kelas.tingkat` adalah sumber utama; UI sudah menurunkan dari token pertama `kode_kelas` (mis. `X-TKJ-1` → `X`). Menghilangkan inkonsistensi dan duplikasi. |
| `spp_nominal` | Tambah di template, **default = default sekolah per tahun akademik** (`JenisBiaya.total_beban` untuk `kode_akun='4.1.01.01'`). | Saat ini import selalu menulis `nominal = '0'` ke semua baris `spp` — bug yang membuat 1000+ siswa punya SPP Rp 0. Pemakaian `resolveDefaultSppNominal` mengikuti logika form manual `siswa.tambah` agar konsisten. |
| `kode_ruangan` | Tambah di template, **wajib**, validasi ke tabel `ruangan`. | Saat ini `ruang` teks bebas default `'-'`. Tabel `ruangan` sudah ada dengan `kode_ruangan`. Validasi supaya tidak ada orphan. |
| `kode_jurusan` | Tambah di template, **opsional**, validasi ke tabel `jurusan`. | Saat ini `siswa.kode_jurusan` diisi dari `$kelas->kode_kurikulum` (salah). Tabel `jurusan` sudah ada. Mengisi dengan benar atau null. |
| `status_awal` | Tambah di template, default `'baru'`. | Saat ini hardcoded — tidak bisa import siswa pindahan. |
| `tgl_masuk` | Tambah di template, default `today`. | Saat ini hardcoded — krusial untuk siswa pindahan (mis. masuk semester 2 mempengaruhi loop SPP bulanan). |

---

## Detail Perubahan

### 1. `SiswaService::resolveDefaultSppNominal()` (baru)

```php
public function resolveDefaultSppNominal(?string $namaTahun): int
{
    if (!$namaTahun) {
        $namaTahun = TahunAkademik::where('status', 'aktif')->value('nama_tahun') ?? date('Y');
    }

    $cacheKey = 'spp_nominal_' . $namaTahun . ':' . (tenant('id') ?? 'central');

    return Cache::remember($cacheKey, 3600, function () use ($namaTahun) {
        $val = DB::table('jenis_biaya')
            ->join('jenis_pembayaran', 'jenis_pembayaran.id', '=', 'jenis_biaya.id_jp')
            ->where('jenis_pembayaran.kode_akun', '4.1.01.01')
            ->where('jenis_biaya.angkatan', $namaTahun)
            ->value('jenis_biaya.total_beban');
        return (int) ($val ?? 0);
    });
}
```

`SiswaController::nominalSppByTahun` sekarang:

```php
public function nominalSppByTahun(?string $tahun): int
{
    return $this->service->resolveDefaultSppNominal($tahun);
}
```

### 2. Struktur Template Excel Baru

| Urutan | Header | Wajib? |
|---|---|---|
| 1-12 | `nik`, `nama`, `jenis_kelamin`, `nipd`, `nisn`, `no_kk`, `tempat_lahir`, `tanggal_lahir`, `agama`, `password`, `alamat`, `rt` | sebagian wajib, lihat `required` di import |
| 13-22 | `rw`, `dusun`, `kelurahan`, `kecamatan`, `kode_pos`, `kebutuhan_khusus`, `jenis_tinggal`, `alat_transportasi`, `hp`, `email` | – |
| 23-29 | field ayah (7) | – |
| 30-36 | field ibu (7) | – |
| 37-43 | field wali (7) | – |
| 44 | `kode_kelas` | **wajib** |
| 45 | `kode_jurusan` | opsional |
| 46 | `kode_ruangan` | **wajib** |
| 47 | `status_awal` | opsional (`baru`/`pindahan`, default `baru`) |
| 48 | `tgl_masuk` | opsional (YYYY-MM-DD, default hari ini) |
| 49 | `spp_nominal` | opsional (default = default sekolah per tahun akademik) |

### 3. Alur Import yang Diperbaiki

```
Excel row
  ↓
normalize (snake_case + trim)
  ↓
required fields check (termasuk kode_ruangan)
  ↓
kode_kelas → cari di kelasMap → throw jika tidak ada → ambil tingkat dari kelas
  ↓
kode_ruangan → cek tabel ruangan → throw jika tidak ada
  ↓
kode_jurusan (opsional) → cek tabel jurusan jika diisi → null jika kosong
  ↓
status_awal validasi enum
tgl_masuk parse → fallback ke default
spp_nominal parse → fallback ke resolveDefaultSppNominal
  ↓
Siswa::create / fill+save (NISN = upsert key)
  ↓
AnggotaKelas::firstOrCreate (id_siswa, tahun_akademik, kode_kelas)
  └─ defaults include: tingkat, spp_nominal, tgl_masuk, tgl_keluar, status
  ↓
generateSppBulanan(anggota, sppNominal, tglMasuk)
  └─ insertOrIgnore 12 baris SPP per bulan (Juli-Juni) dengan nominal dari Excel/default
```

### 4. Alur Multi-Modal UI

Setelah preview sukses, jika ada missing di salah satu/ketiganya, chain otomatis:

1. **Modal Ruangan Baru** → input `nama_ruangan` + `kode_gedung` per baris → submit → `new_ruangan[]`
2. **Modal Jurusan Baru** → input `nama` per baris → submit → `new_jurusan[]`
3. **Modal Kelas Baru** → input `nama_kelas`, auto-fill `tingkat` dari token, dropdown `kode_kurikulum` → submit → `new_kelas[]`

Ketiganya dikirim bersamaan via FormData ke `POST /migrasi/siswa/import`.

### 5. Bug Filter Kelas Diperbaiki

`MigrasiSiswaController::index()` dulu memfilter `kelas` dengan `where('tahun_akademik_id', ...)` dan `where('jurusan_id', ...)`. Kolom tersebut **tidak ada** di tabel `kelas`. Hasilnya dropdown selalu kosong ketika tahun akademik dipilih. Sekarang: muat semua `kelas` di tenant itu saja (sesuai tenant scope).

> Catatan: filter per tahun akademik untuk kelas tetap bisa bermanfaat, tapi butuh migration schema — di luar scope perbaikan ini. Tabel `anggota_kelas` adalah satu-satunya yang saat ini menyimpan scope tahun untuk seorang siswa.

---

## Verifikasi Manual yang Disarankan

1. **Download template baru** dari `/migrasi/siswa/template`. Cek 49 header + sample row 49 nilai.
2. **Header tidak lagi memuat `tingkat`**.
3. **Buat file Excel uji** dengan 5 baris siswa, 1 kelas sudah ada, 1 kode_ruangan baru, 1 kode_jurusan baru.
4. **Upload → preview** → seharusnya muncul 3 modal secara berurutan.
5. **Submit → import**.
6. **Cek DB**:
   - `siswa.kode_jurusan` terisi sesuai Excel, bukan `$kelas->kode_kurikulum`.
   - `siswa.ruang` = `kode_ruangan` (mis. `R-101`), bukan `'-'`.
   - `anggota_kelas.spp_nominal` terisi sesuai Excel atau default sekolah.
   - Tabel `spp` punya 12 baris (atau sesuai tgl_masuk) dengan `nominal` bukan `0`.
7. **Cek SiswaService reuse**: edit manual siswa — `nominalSppByTahun` masih bekerja, karena delegasi ke service yang sama.

---

## Risiko & Catatan

- **Backward compatibility**: data Excel lama yang masih berisi `tingkat` akan diabaikan (tidak error karena `normalize` snake_case hanya membaca header). NISN tetap upsert key — data existing aman.
- **`generateSppBulanan` lama**: baris `spp` lama status `B` (belum bayar) akan di-update nominalnya mengikuti `anggota_kelas.spp_nominal` lewat flow edit manual (`SiswaController::update`). Untuk data baru (insert), 12 baris langsung terisi nominal benar.
- **`id_siswa` integer vs bigIncrements**: tidak disentuh (masalah pre-existing).
- **`kelas.tingkat` global**: untuk tenant dengan banyak `kode_kelas` sama lintas tahun, kenaikan tingkat antar tahun harus dilakukan manual — di luar scope.
- **`Jurusan` & `Kelas::kurikulum()`**: relasi `Kurikulum::id` vs `kelas.kode_kurikulum` (string) tidak disentuh. Hanya `parseKodeKelas()` yang dipakai, dan ia bekerja dengan string literal — tidak masalah.

---

## Berkas yang Berubah

| File |
|---|
| `app/Services/SiswaService.php` |
| `app/Http/Controllers/SiswaController.php` |
| `app/Exports/MigrasiSiswaTemplateExport.php` |
| `app/Imports/MigrasiSiswaImport.php` |
| `app/Http/Controllers/Tenant/MigrasiSiswaController.php` |
| `resources/views/tenant/migrasi-siswa.blade.php` |
| `routes/web.php` |
