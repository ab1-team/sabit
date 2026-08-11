<?php

declare(strict_types=1);

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\PengaturanController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\JenisBiayaController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\SppController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\JurusanController;
use App\Http\Controllers\KelasController;
use App\Http\Controllers\KurikulumController;
use App\Http\Controllers\RuanganController;
use App\Http\Controllers\TahunAkademikController;
use App\Http\Controllers\JenisPembayaranController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\DaftarKelasController;
use App\Http\Controllers\Tenant\LandingAdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Routes untuk tenant (per sekolah). Loaded by TenancyServiceProvider
| setelah tenancy diinisialisasi. Semua route /app/* milik sekolah.
|
| Default: subdomain (misal: sma1.sabit.test).
| Opsional untuk development tanpa DNS: path-based /tenant/{id}/...
|
*/

Route::get('/', function () {
    return redirect('/app/dashboard');
})->name('tenant.home');

Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');

// Fallback symlink maker (jalankan sekali via browser jika public/storage belum ada)
Route::get('/link', function () {
    $target = base_path('storage/app/public');
    $shortcut = public_path('storage');

    $alreadyLinked = is_link($shortcut)
        || (DIRECTORY_SEPARATOR === '\\' && file_exists($shortcut) && strtolower(readlink($shortcut) ?: '') !== '');

    if ($alreadyLinked) {
        return response()->json([
            'status'  => 'ok',
            'message' => 'Symlink already exists.',
            'target'  => $target,
            'link'    => $shortcut,
        ]);
    }

    try {
        if (is_dir($shortcut) && !is_link($shortcut)) {
            if (count(scandir($shortcut)) > 2) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'public/storage adalah direktori nyata (bukan symlink) dan berisi file. Hapus atau pindahkan dulu sebelum membuat symlink.',
                    'link'    => $shortcut,
                ], 500);
            }
            @rmdir($shortcut);
        }

        if (DIRECTORY_SEPARATOR === '\\') {
            // Windows: pakai junction (butuh CMD) agar tidak perlu Developer Mode
            $cmd = sprintf('mklink /J %s %s', escapeshellarg($shortcut), escapeshellarg($target));
            exec($cmd, $out, $rc);
            if ($rc !== 0) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Failed to create junction: ' . implode("\n", $out),
                ], 500);
            }
        } else {
            symlink($target, $shortcut);
        }

        return response()->json([
            'status'  => 'ok',
            'message' => 'Symlink created successfully.',
            'target'  => $target,
            'link'    => $shortcut,
        ]);
    } catch (\Throwable $e) {
        return response()->json([
            'status'  => 'error',
            'message' => 'Failed to create symlink: ' . $e->getMessage(),
        ], 500);
    }
});

Route::group(['middleware' => ['auth'], 'prefix' => 'app'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('app.dashboard');

    Route::put('/profile/update/{id}', [ProfilController::class, 'update']);
    Route::get('/profile', [ProfilController::class, 'index']);

    Route::get('/system/generate-tunggakan', [SystemController::class, 'GenerateTunggakan']);
    Route::get('/system/piutang-status', [SystemController::class, 'piutangStatus']);

    Route::get('/Transaksi/pembayaran-spp', [TransaksiController::class, 'pembayaranSPP']);
    Route::get('/transaksi/daftar-inventaris', [TransaksiController::class, 'daftarInventaris']);
    Route::get('/transaksi/saldo/{kode_akun}', [TransaksiController::class, 'saldo']);
    Route::post('/transaksi/ProsesPembayaran', [TransaksiController::class, 'pembayaranSPPStore']);
    Route::get('/transaksi/kwitansi-spp', [TransaksiController::class, 'pembayaranSPPPrint']);
    Route::get('/transaksi/pembayaran/printAllSelected', [TransaksiController::class, 'printAllSelected']);
    Route::get('/transaksi/pembayaran/printAll/{id}', [TransaksiController::class, 'pembayaranSPPPrintAll']);
    Route::get('/transaksi/pembayaranSPPDetail/{id}', [TransaksiController::class, 'pembayaranSPPDetail']);
    Route::get('/transaksi/pembayaranSPPDetailTagihan/{id}', [TransaksiController::class, 'pembayaranSPPDetailTagihan']);
    Route::get('/transaksi/cetakPadaKartu', [TransaksiController::class, 'CetakPadaKartu']);
    Route::get('/transaksi/cetak-kartu-spp/{id}', [TransaksiController::class, 'cetakKartuSpp']);
    Route::get('/transaksi/cetak-kartu-ujian/{id}/{jenis}', [TransaksiController::class, 'cetakKartuUjian'])
        ->where('jenis', 'uts1|uts2|pas1|pas2');
    Route::delete('/transaksi/pembayaranSPPDestroy/{Transaksi}', [TransaksiController::class, 'pembayaranSPPDestroy']);
    Route::resource('/Transaksi', TransaksiController::class);
    Route::get('/Transaksi/jurnal-umum/data', [TransaksiController::class, 'jurnalUmumData'])->name('Transaksi.jurnalUmumData');
    Route::get('/Transaksi/jurnal-umum/detail', [TransaksiController::class, 'jurnalUmumDetail'])->name('Transaksi.jurnalUmumDetail');
    Route::get('/Transaksi/jurnal-umum/cetak', [TransaksiController::class, 'jurnalUmumCetak'])->name('Transaksi.jurnalUmumCetak');
    Route::get('/Transaksi/jurnal-umum/printDokumen/{jenis}', [TransaksiController::class, 'jurnalUmumPrintDokumen'])->name('Transaksi.jurnalUmumPrintDokumen');
    Route::delete('/Transaksi/jurnal-umum/{transaksi}', [TransaksiController::class, 'jurnalUmumDestroy'])->name('Transaksi.jurnalUmumDestroy');

    Route::resource('/jenis-biaya', JenisBiayaController::class);
    Route::get('/jenis-biaya-create-form', [JenisBiayaController::class, 'createForm']);
    Route::get('/jenis-biaya-edit-form/{jenis_biaya}', [JenisBiayaController::class, 'editForm']);
    Route::get('/spp/CariSiswa', [SppController::class, 'CariSiswaAktif']);
    Route::get('/spp/Pembayaran-spp/{id}', [SppController::class, 'spp']);
    Route::resource('/spp', SppController::class);

    Route::get('/pengaturan/sop', [PengaturanController::class, 'sop']);
    Route::get('/pengaturan/coa', [PengaturanController::class, 'coa']);
    Route::get('/pengaturan/ttd-pelaporan', [PengaturanController::class, 'ttdPelaporan']);
    Route::get('/pengaturan/invoice', [PengaturanController::class, 'invoice'])->name('app.pengaturan.invoice');
    Route::get('/pengaturan/invoice/data', [PengaturanController::class, 'invoice'])->name('app.pengaturan.invoice.data');
    Route::get('/pengaturan/invoice/{invoice}/print', [PengaturanController::class, 'invoicePrint'])->name('app.pengaturan.invoice.print');
    Route::post('/pengaturan/simpan/ttd-pelaporan', [PengaturanController::class, 'ttdPelaporanStore']);
    Route::put('/pengaturan/lembaga/{id}', [PengaturanController::class, 'lembaga']);
    Route::put('/pengaturan/logo/{id}', [PengaturanController::class, 'logo']);
    Route::put('/pengaturan/jatuh_tempo/{id}', [PengaturanController::class, 'jatuhTempo']);
    Route::put('/pengaturan/sop-pembayaran/{id}', [PengaturanController::class, 'sopPembayaran']);
    Route::resource('/pengaturan', PengaturanController::class);

    Route::get('/siswa/listTahun', [SiswaController::class, 'listTahun']);
    Route::get('/siswa/listKelas', [SiswaController::class, 'listKelas']);
    Route::get('/siswa/printSiswa', [SiswaController::class, 'printSiswa']);
    Route::get('/siswa/nominal-spp', [SiswaController::class, 'getNominalSpp']);
    Route::post('/siswa/mutasi', [SiswaController::class, 'mutasi']);
    Route::get('/siswa/riwayatPembayaran/{id}', [SiswaController::class, 'riwayatPembayaran']);
    Route::resource('/siswa', SiswaController::class);

    Route::get('/daftar-kelas/listTahun', [DaftarKelasController::class, 'listTahun']);
    Route::get('/daftar-kelas/listKelas', [DaftarKelasController::class, 'listKelas']);
    Route::get('/daftar-kelas/data', [DaftarKelasController::class, 'data']);
    Route::get('/daftar-kelas/cetak-kartu-batch', [DaftarKelasController::class, 'cetakKartuBatch']);
    Route::get('/daftar-kelas', [DaftarKelasController::class, 'index']);

    Route::get('/laporan-keuangan', [LaporanController::class, 'index']);
    Route::get('/laporan-keuangan/simpan-saldo', [LaporanController::class, 'simpanSaldo']);

    Route::get('/dashboard/siswa-aktif', [DashboardController::class, 'siswaAktifTable']);
    Route::get('/dashboard/siswa-menunggak', [DashboardController::class, 'siswaMenunggakTable']);

    Route::get('/pelaporan/preview', [LaporanController::class, 'preview']);

    Route::get('/pelaporan/sub_laporan/{file}', [LaporanController::class, 'subLaporan']);

    Route::resource('jurusan', JurusanController::class)
        ->parameters(['jurusan' => 'jurusan'])
        ->names('app.jurusan');

    Route::resource('kelas', KelasController::class)
        ->parameters(['kelas' => 'kelas'])
        ->names('app.kelas');

    Route::resource('kurikulum', KurikulumController::class)
        ->parameters(['kurikulum' => 'kurikulum'])
        ->names('app.kurikulum');

    Route::resource('ruangan', RuanganController::class)
        ->parameters(['ruangan' => 'ruangan'])
        ->names('app.ruangan');

    Route::resource('tahun-akademik', TahunAkademikController::class)
        ->parameters(['tahun-akademik' => 'tahun_akademik'])
        ->names('app.tahun-akademik');

    Route::post('tahun-akademik/{tahun_akademik}/aktifkan', [TahunAkademikController::class, 'aktifkan'])
        ->name('app.tahun-akademik.aktifkan');

    Route::resource('jenis-pembayaran', JenisPembayaranController::class)
        ->parameters(['jenis-pembayaran' => 'jenis_pembayaran'])
        ->names('app.jenis-pembayaran');

    Route::get('coa', [RekeningController::class, 'tree'])->name('app.coa');
    Route::post('coa/{rekening}/nonaktifkan', [RekeningController::class, 'nonaktifkan'])->name('app.coa.nonaktifkan');
    Route::post('coa/{rekening}/aktifkan', [RekeningController::class, 'aktifkan'])->name('app.coa.aktifkan');

    // NOTE: Dinonaktifkan sementara. App\Http\Controllers\Tenant\School\HakAksesController
    // belum ada sehingga route ini memicu ReflectionException (route:list gagal total).
    // Pengelolaan hak akses tetap tersedia dari console pusat via HakAksesPusatController.
    // Route::get('/hak-akses', [HakAksesController::class, 'index'])->name('app.hak-akses');
    // Route::post('/hak-akses', [HakAksesController::class, 'store'])->name('app.hak-akses.store');
    // Route::put('/hak-akses/{user}', [HakAksesController::class, 'update'])->name('app.hak-akses.update');

    // Landing page: kelola konten website publik sekolah.
    // Dibatasi hak_akses menu 'Landing Page' (lihat middleware hak.akses).
    Route::prefix('landing')->name('app.landing.')->middleware('hak.akses:landing')->group(function () {
        Route::get('/', [LandingAdminController::class, 'index'])->name('index');

        Route::get('/pengaturan', [LandingAdminController::class, 'pengaturan'])->name('pengaturan');
        Route::post('/pengaturan', [LandingAdminController::class, 'pengaturanStore'])->name('pengaturan.store');

        Route::get('/hero', [LandingAdminController::class, 'hero'])->name('hero');
        Route::post('/hero', [LandingAdminController::class, 'heroStore'])->name('hero.store');
        Route::put('/hero/{slide}', [LandingAdminController::class, 'heroUpdate'])->name('hero.update');
        Route::delete('/hero/{slide}', [LandingAdminController::class, 'heroDestroy'])->name('hero.destroy');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


