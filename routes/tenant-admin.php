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
use App\Http\Controllers\AdminLandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Routes untuk tenant (per sekolah). Loaded by TenancyServiceProvider
| setelah tenancy diinisialisasi. Semua route /app/* milik sekolah.
|
| Default: subdomain (misal: sma1.example.test).
| Opsional untuk development tanpa DNS: path-based /tenant/{id}/...
|
*/

Route::get('/', function () {
    return redirect()->route('login');
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

    Route::get('/transaksi/pembayaran-spp', [TransaksiController::class, 'pembayaranSPP']);
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
    Route::resource('/transaksi', TransaksiController::class);
    Route::get('/transaksi/jurnal-umum/data', [TransaksiController::class, 'jurnalUmumData'])->name('transaksi.jurnalUmumData');
    Route::get('/transaksi/jurnal-umum/detail', [TransaksiController::class, 'jurnalUmumDetail'])->name('transaksi.jurnalUmumDetail');
    Route::get('/transaksi/jurnal-umum/cetak', [TransaksiController::class, 'jurnalUmumCetak'])->name('transaksi.jurnalUmumCetak');
    Route::get('/transaksi/jurnal-umum/printDokumen/{jenis}', [TransaksiController::class, 'jurnalUmumPrintDokumen'])->name('transaksi.jurnalUmumPrintDokumen');
    Route::delete('/transaksi/jurnal-umum/{transaksi}', [TransaksiController::class, 'jurnalUmumDestroy'])->name('transaksi.jurnalUmumDestroy');

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

    // NOTE: Dinonaktifkan sementara. App\Http\Controllers\School\HakAksesController
    // belum ada sehingga route ini memicu ReflectionException (route:list gagal total).
    // Pengelolaan hak akses tetap tersedia dari console pusat via HakAksesPusatController.
    // Route::get('/hak-akses', [HakAksesController::class, 'index'])->name('app.hak-akses');
    // Route::post('/hak-akses', [HakAksesController::class, 'store'])->name('app.hak-akses.store');
    // Route::put('/hak-akses/{user}', [HakAksesController::class, 'update'])->name('app.hak-akses.update');

    // Landing page: kelola konten website publik sekolah.
    // Dibatasi hak_akses menu 'Landing Page' (lihat middleware hak.akses).
    Route::prefix('admin-landing')->name('app.admin-landing.')->middleware('hak.akses:landing')->group(function () {
        Route::get('/', [AdminLandingController::class, 'index'])->name('index');

        Route::get('/pengaturan', [AdminLandingController::class, 'pengaturan'])->name('pengaturan');
        Route::post('/pengaturan', [AdminLandingController::class, 'pengaturanStore'])->name('pengaturan.store');
        Route::delete('/pengaturan/custom-background', [AdminLandingController::class, 'hapusCustomBackground'])->name('pengaturan.custom.destroy');

        Route::get('/posts', [AdminLandingController::class, 'posts'])->name('posts');
        Route::get('/posts/data', [AdminLandingController::class, 'postsData'])->name('posts.data');
        Route::get('/posts/cards', [AdminLandingController::class, 'postsCards'])->name('posts.cards');
        Route::get('/posts/create', [AdminLandingController::class, 'postCreate'])->name('posts.create');
        Route::post('/posts', [AdminLandingController::class, 'postStore'])->name('posts.store');
        Route::get('/posts/{post}/edit', [AdminLandingController::class, 'postEdit'])->name('posts.edit');
        Route::put('/posts/{post}', [AdminLandingController::class, 'postUpdate'])->name('posts.update');
        Route::delete('/posts/{post}', [AdminLandingController::class, 'postDestroy'])->name('posts.destroy');
        Route::post('/posts/upload-content', [AdminLandingController::class, 'postUploadContent'])->name('posts.upload-content');

        Route::get('/announcements', [AdminLandingController::class, 'announcements'])->name('announcements');
        Route::get('/announcements/data', [AdminLandingController::class, 'announcementsData'])->name('announcements.data');
        Route::get('/announcements/create', [AdminLandingController::class, 'announcementCreate'])->name('announcements.create');
        Route::post('/announcements', [AdminLandingController::class, 'announcementStore'])->name('announcements.store');
        Route::get('/announcements/{announcement}/edit', [AdminLandingController::class, 'announcementEdit'])->name('announcements.edit');
        Route::put('/announcements/{announcement}', [AdminLandingController::class, 'announcementUpdate'])->name('announcements.update');
        Route::delete('/announcements/{announcement}', [AdminLandingController::class, 'announcementDestroy'])->name('announcements.destroy');

        Route::get('/galleries', [AdminLandingController::class, 'galleries'])->name('galleries');
        Route::get('/galleries/data', [AdminLandingController::class, 'galleriesData'])->name('galleries.data');
        Route::get('/galleries/create', [AdminLandingController::class, 'galleryCreate'])->name('galleries.create');
        Route::post('/galleries', [AdminLandingController::class, 'galleryStore'])->name('galleries.store');
        Route::get('/galleries/{gallery}/edit', [AdminLandingController::class, 'galleryEdit'])->name('galleries.edit');
        Route::put('/galleries/{gallery}', [AdminLandingController::class, 'galleryUpdate'])->name('galleries.update');
        Route::delete('/galleries/{gallery}', [AdminLandingController::class, 'galleryDestroy'])->name('galleries.destroy');

        Route::get('/videos', [AdminLandingController::class, 'videos'])->name('videos');
        Route::get('/videos/data', [AdminLandingController::class, 'videosData'])->name('videos.data');
        Route::get('/videos/create', [AdminLandingController::class, 'videoCreate'])->name('videos.create');
        Route::post('/videos', [AdminLandingController::class, 'videoStore'])->name('videos.store');
        Route::get('/videos/{video}/edit', [AdminLandingController::class, 'videoEdit'])->name('videos.edit');
        Route::put('/videos/{video}', [AdminLandingController::class, 'videoUpdate'])->name('videos.update');
        Route::delete('/videos/{video}', [AdminLandingController::class, 'videoDestroy'])->name('videos.destroy');

        Route::get('/struktur', [AdminLandingController::class, 'struktur'])->name('struktur');
        Route::get('/struktur/create', [AdminLandingController::class, 'strukturCreate'])->name('struktur.create');
        Route::post('/struktur', [AdminLandingController::class, 'strukturStore'])->name('struktur.store');
        Route::get('/struktur/{item}/edit', [AdminLandingController::class, 'strukturEdit'])->name('struktur.edit');
        Route::put('/struktur/{item}', [AdminLandingController::class, 'strukturUpdate'])->name('struktur.update');
        Route::delete('/struktur/{item}', [AdminLandingController::class, 'strukturDestroy'])->name('struktur.destroy');

        Route::get('/fasilitas', [AdminLandingController::class, 'fasilitas'])->name('fasilitas');
        Route::get('/fasilitas/create', [AdminLandingController::class, 'fasilitasCreate'])->name('fasilitas.create');
        Route::post('/fasilitas', [AdminLandingController::class, 'fasilitasStore'])->name('fasilitas.store');
        Route::get('/fasilitas/{item}/edit', [AdminLandingController::class, 'fasilitasEdit'])->name('fasilitas.edit');
        Route::put('/fasilitas/{item}', [AdminLandingController::class, 'fasilitasUpdate'])->name('fasilitas.update');
        Route::delete('/fasilitas/{item}', [AdminLandingController::class, 'fasilitasDestroy'])->name('fasilitas.destroy');

        Route::get('/profile-sections', [AdminLandingController::class, 'profileSections'])->name('profile-sections');
        Route::put('/profile-sections', [AdminLandingController::class, 'profileSectionsUpdateAll'])->name('profile-sections.updateAll');
        Route::get('/profile-sections/{item}/edit', [AdminLandingController::class, 'profileSectionEdit'])->name('profile-sections.edit');
        Route::put('/profile-sections/{item}', [AdminLandingController::class, 'profileSectionUpdate'])->name('profile-sections.update');
        Route::post('/profile-sections/{item}/toggle', [AdminLandingController::class, 'profileSectionToggle'])->name('profile-sections.toggle');

        Route::get('/ppdb-cta', [AdminLandingController::class, 'ppdbCta'])->name('ppdb-cta');
        Route::post('/ppdb-cta', [AdminLandingController::class, 'ppdbCtaStore'])->name('ppdb-cta.store');

        Route::get('/ppdb-setting', [AdminLandingController::class, 'ppdbSetting'])->name('ppdb-setting');
        Route::post('/ppdb-setting', [AdminLandingController::class, 'ppdbSettingStore'])->name('ppdb-setting.store');

        // Kontak: pesan masuk form landing.
        Route::get('/contact-messages', [AdminLandingController::class, 'contactMessages'])->name('contact-messages');
        Route::get('/contact-messages/data', [AdminLandingController::class, 'contactMessagesData'])->name('contact-messages.data');
        Route::post('/contact-messages/{message}/status', [AdminLandingController::class, 'contactMessageStatus'])->name('contact-messages.status');
        Route::post('/contact-messages/{message}/mark', [AdminLandingController::class, 'contactMessageMark'])->name('contact-messages.mark');
        Route::delete('/contact-messages/{message}', [AdminLandingController::class, 'contactMessageDestroy'])->name('contact-messages.destroy');

        // PPDB: sub-CRUD (persyaratan, tahapan, jadwal, FAQ).
        Route::get('/ppdb/persyaratan', [AdminLandingController::class, 'ppdbRequirements'])->name('ppdb.requirements');
        Route::post('/ppdb/persyaratan', [AdminLandingController::class, 'ppdbRequirementStore'])->name('ppdb.requirements.store');
        Route::put('/ppdb/persyaratan/{item}', [AdminLandingController::class, 'ppdbRequirementUpdate'])->name('ppdb.requirements.update');
        Route::delete('/ppdb/persyaratan/{item}', [AdminLandingController::class, 'ppdbRequirementDestroy'])->name('ppdb.requirements.destroy');

        Route::get('/ppdb/tahapan', [AdminLandingController::class, 'ppdbStages'])->name('ppdb.stages');
        Route::post('/ppdb/tahapan', [AdminLandingController::class, 'ppdbStageStore'])->name('ppdb.stages.store');
        Route::put('/ppdb/tahapan/{item}', [AdminLandingController::class, 'ppdbStageUpdate'])->name('ppdb.stages.update');
        Route::delete('/ppdb/tahapan/{item}', [AdminLandingController::class, 'ppdbStageDestroy'])->name('ppdb.stages.destroy');

        Route::get('/ppdb/jadwal', [AdminLandingController::class, 'ppdbSchedules'])->name('ppdb.schedules');
        Route::post('/ppdb/jadwal', [AdminLandingController::class, 'ppdbScheduleStore'])->name('ppdb.schedules.store');
        Route::put('/ppdb/jadwal/{item}', [AdminLandingController::class, 'ppdbScheduleUpdate'])->name('ppdb.schedules.update');
        Route::delete('/ppdb/jadwal/{item}', [AdminLandingController::class, 'ppdbScheduleDestroy'])->name('ppdb.schedules.destroy');

        Route::get('/ppdb/faq', [AdminLandingController::class, 'ppdbFaqs'])->name('ppdb.faqs');
        Route::post('/ppdb/faq', [AdminLandingController::class, 'ppdbFaqStore'])->name('ppdb.faqs.store');
        Route::put('/ppdb/faq/{item}', [AdminLandingController::class, 'ppdbFaqUpdate'])->name('ppdb.faqs.update');
        Route::delete('/ppdb/faq/{item}', [AdminLandingController::class, 'ppdbFaqDestroy'])->name('ppdb.faqs.destroy');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});


