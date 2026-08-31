<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\TenantInvoiceController;
use App\Http\Controllers\Tenant\AuthController;
use App\Http\Controllers\Tenant\CoaController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\HakAksesPusatController;
use App\Http\Controllers\Tenant\LocaleController;
use App\Http\Controllers\Tenant\JabatanPusatController;
use App\Http\Controllers\Tenant\JenisPembayaranController;
use App\Http\Controllers\Tenant\KelasPusatController;
use App\Http\Controllers\Tenant\KurikulumPusatController;
use App\Http\Controllers\Tenant\JurusanPusatController;
use App\Http\Controllers\Tenant\MigrasiSiswaController;
use App\Http\Controllers\Tenant\ProfilSekolahController;
use App\Http\Controllers\Tenant\RuanganPusatController;
use App\Http\Controllers\Tenant\TahunAkademikController;
use App\Http\Controllers\Tenant\TenantController;
use App\Http\Controllers\Tenant\TransaksiController;
use App\Http\Controllers\Tenant\UserOperatorController;

/*
|--------------------------------------------------------------------------
| TENANT (Master Console / Pusat) Routes
|--------------------------------------------------------------------------
|
| Domain pusat: env('CENTRAL_DOMAIN') => pusat.example.test (atau value kosong jika single-domain).
| Mengelola sekolah (subdomain tenant) dari pusat.
| Login pusat di /login (sebelumnya /master/login).
| Sekolah diakses via subdomain, mis: demo.example.test
| (atau tanpa subdomain jika deploy single-domain).
|
*/

$centralRoutes = function () {
    Route::get('/', function () {
        return redirect('/login');
    });

    // Login pusat. Hanya untuk host central — host tenant akan ditolak
    // oleh HostContext::isCentral() check di Tenant\AuthController.
    // Tidak dibungkus Route::domain() agar tidak ada duplikat route name
    // (lihat helper HostContext::centralHosts() untuk daftar host central).
    Route::get('/login', [AuthController::class, 'index'])->name('tenant.login');
    Route::post('/login', [AuthController::class, 'login'])->name('tenant.auth');
    Route::post('/logout', [AuthController::class, 'logout'])->name('tenant.logout');

    // Symlink maker (accessible dari pusat atau subdomain tenant)
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

    Route::group(['middleware' => ['auth:tenant'], 'prefix' => ''], function () {
        Route::post('/locale/switch', [LocaleController::class, 'switch'])->name('tenant.locale.switch');

        Route::get('/dashboard', [DashboardController::class, 'index'])->name('tenant.dashboard');

        // Tenant management (pusat mengelola sekolah)
        Route::get('/tenant', [TenantController::class, 'index'])->name('tenant.tenant.index');
        Route::get('/tenant/data', [TenantController::class, 'data'])->name('tenant.tenant.data');
        Route::post('/tenant', [TenantController::class, 'store'])->name('tenant.tenant.store');
        Route::get('/tenant/{tenant}', [TenantController::class, 'show'])->name('tenant.tenant.show');
        Route::put('/tenant/{tenant}', [TenantController::class, 'update'])->name('tenant.tenant.update');
        Route::delete('/tenant/{tenant}', [TenantController::class, 'destroy'])->name('tenant.tenant.destroy');

        // Sub-CRUD per Tenant (kelola isi sekolah dari pusat)
        Route::prefix('tenant/{tenant}')->name('tenant.tenant.')->group(function () {
            Route::get('profil', [ProfilSekolahController::class, 'index'])->name('profil.index');
            Route::put('profil', [ProfilSekolahController::class, 'update'])->name('profil.update');

            Route::get('user', [UserOperatorController::class, 'index'])->name('user.index');
            Route::post('user', [UserOperatorController::class, 'store'])->name('user.store');
            Route::put('user/{user}', [UserOperatorController::class, 'update'])->name('user.update');
            Route::delete('user/{user}', [UserOperatorController::class, 'destroy'])->name('user.destroy');
            Route::post('user/{user}/reset-password', [UserOperatorController::class, 'resetPassword'])->name('user.reset-password');

            Route::get('tahun-akademik', [TahunAkademikController::class, 'index'])->name('tahun-akademik.index');
            Route::get('tahun-akademik/data', [TahunAkademikController::class, 'data'])->name('tahun-akademik.data');
            Route::post('tahun-akademik', [TahunAkademikController::class, 'store'])->name('tahun-akademik.store');
            Route::put('tahun-akademik/{tahun_akademik}', [TahunAkademikController::class, 'update'])->name('tahun-akademik.update');
            Route::delete('tahun-akademik/{tahun_akademik}', [TahunAkademikController::class, 'destroy'])->name('tahun-akademik.destroy');
            Route::post('tahun-akademik/{tahun_akademik}/aktifkan', [TahunAkademikController::class, 'aktifkan'])->name('tahun-akademik.aktifkan');

            Route::get('jenis-pembayaran', [JenisPembayaranController::class, 'index'])->name('jenis-pembayaran.index');
            Route::post('jenis-pembayaran', [JenisPembayaranController::class, 'store'])->name('jenis-pembayaran.store');
            Route::put('jenis-pembayaran/{jenis_pembayaran}', [JenisPembayaranController::class, 'update'])->name('jenis-pembayaran.update');
            Route::delete('jenis-pembayaran/{jenis_pembayaran}', [JenisPembayaranController::class, 'destroy'])->name('jenis-pembayaran.destroy');

            Route::get('coa', [CoaController::class, 'index'])->name('coa.index');
            Route::post('coa/{rekening}/nonaktifkan', [CoaController::class, 'nonaktifkan'])->name('coa.nonaktifkan');
            Route::post('coa/{rekening}/aktifkan', [CoaController::class, 'aktifkan'])->name('coa.aktifkan');

            // Master data akademik + referensi (pusat)
            Route::get('jabatan', [JabatanPusatController::class, 'index'])->name('jabatan.index');
            Route::get('jabatan/data', [JabatanPusatController::class, 'data'])->name('jabatan.data');
            Route::post('jabatan', [JabatanPusatController::class, 'store'])->name('jabatan.store');
            Route::put('jabatan/{jabatan}', [JabatanPusatController::class, 'update'])->name('jabatan.update');
            Route::delete('jabatan/{jabatan}', [JabatanPusatController::class, 'destroy'])->name('jabatan.destroy');

            Route::get('kurikulum', [KurikulumPusatController::class, 'index'])->name('kurikulum.index');
            Route::get('kurikulum/data', [KurikulumPusatController::class, 'data'])->name('kurikulum.data');
            Route::post('kurikulum', [KurikulumPusatController::class, 'store'])->name('kurikulum.store');
            Route::put('kurikulum/{kurikulum}', [KurikulumPusatController::class, 'update'])->name('kurikulum.update');
            Route::delete('kurikulum/{kurikulum}', [KurikulumPusatController::class, 'destroy'])->name('kurikulum.destroy');

            Route::get('jurusan', [JurusanPusatController::class, 'index'])->name('jurusan.index');
            Route::get('jurusan/data', [JurusanPusatController::class, 'data'])->name('jurusan.data');
            Route::post('jurusan', [JurusanPusatController::class, 'store'])->name('jurusan.store');
            Route::put('jurusan/{jurusan}', [JurusanPusatController::class, 'update'])->name('jurusan.update');
            Route::delete('jurusan/{jurusan}', [JurusanPusatController::class, 'destroy'])->name('jurusan.destroy');

            Route::get('kelas', [KelasPusatController::class, 'index'])->name('kelas.index');
            Route::get('kelas/data', [KelasPusatController::class, 'data'])->name('kelas.data');
            Route::post('kelas', [KelasPusatController::class, 'store'])->name('kelas.store');
            Route::put('kelas/{kelas}', [KelasPusatController::class, 'update'])->name('kelas.update');
            Route::delete('kelas/{kelas}', [KelasPusatController::class, 'destroy'])->name('kelas.destroy');

            Route::get('ruangan', [RuanganPusatController::class, 'index'])->name('ruangan.index');
            Route::get('ruangan/data', [RuanganPusatController::class, 'data'])->name('ruangan.data');
            Route::post('ruangan', [RuanganPusatController::class, 'store'])->name('ruangan.store');
            Route::put('ruangan/{ruangan}', [RuanganPusatController::class, 'update'])->name('ruangan.update');
            Route::delete('ruangan/{ruangan}', [RuanganPusatController::class, 'destroy'])->name('ruangan.destroy');
        });

        Route::get('/invoice/data', [TenantInvoiceController::class, 'data'])->name('tenant.invoice.data');
        Route::get('/invoice/{invoice}/print', [TenantInvoiceController::class, 'print'])->name('tenant.invoice.print');

        Route::get('/transaksi', [TransaksiController::class, 'index'])->name('tenant.transaksi.index');
        Route::get('/transaksi/data', [TransaksiController::class, 'index'])->name('tenant.transaksi.data');
        Route::get('/transaksi/{invoice}/payment', [TransaksiController::class, 'paymentForm'])->name('tenant.transaksi.paymentForm');
        Route::post('/transaksi', [TransaksiController::class, 'store'])->name('tenant.transaksi.store');
        Route::resource('invoice', TenantInvoiceController::class)
            ->only(['index', 'store', 'destroy'])
            ->names('tenant.invoice');

        Route::get('/migrasi/siswa', [MigrasiSiswaController::class, 'index'])->name('tenant.migrasi.siswa');
        Route::get('/migrasi/siswa/template', [MigrasiSiswaController::class, 'template'])->name('tenant.migrasi.siswa.template');
        Route::post('/migrasi/siswa/preview', [MigrasiSiswaController::class, 'preview'])->name('tenant.migrasi.siswa.preview');
        Route::post('/migrasi/siswa/preview/quick-kurikulum', [MigrasiSiswaController::class, 'previewQuickKurikulum'])->name('tenant.migrasi.siswa.preview-quick-kurikulum');
        Route::post('/migrasi/siswa/import', [MigrasiSiswaController::class, 'import'])->name('tenant.migrasi.siswa.import');

        // Hak Akses per Lokasi (pusat kelola hak akses semua tenant)
        Route::prefix('hak-akses')->name('tenant.hak-akses.')->group(function () {
            Route::get('/', [HakAksesPusatController::class, 'index'])->name('index');
            Route::post('/{tenant}/user', [HakAksesPusatController::class, 'storeUser'])->name('user.store');
            Route::put('/{tenant}/user/{userId}', [HakAksesPusatController::class, 'updateUser'])->name('user.update');
            Route::put('/{tenant}/user/{userId}/hak-akses', [HakAksesPusatController::class, 'updateHakAkses'])->name('hak-akses.update');
            Route::delete('/{tenant}/user/{userId}', [HakAksesPusatController::class, 'destroyUser'])->name('user.destroy');
            Route::post('/{tenant}/user/{userId}/reset-password', [HakAksesPusatController::class, 'resetPassword'])->name('user.reset-password');
        });
    });
};

// Route central didaftarkan tanpa Route::domain() wrapper untuk menghindari
// duplikat route name. Host detection menggunakan HostContext::centralHosts().
//
// File ini di-load oleh RouteServiceProvider sebelum request tersedia.
// Kita deteksi host dari $_SERVER['HTTP_HOST'] saat runtime web (bukan CLI).
//
// PENTING: Skip registrasi route central kalau host yang request adalah
// subdomain tenant (mis. demo.sabit.test). Tanpa ini, URI '/login' pusat
// akan override '/login' sekolah di tenant-admin.php, sehingga sekolah
// tidak bisa login (route central match duluan karena web.php load duluan).
$centralHostList = App\Support\HostContext::centralHosts();

if (PHP_SAPI === 'cli') {
    // Saat CLI murni (route:list, tinker, dll), daftarkan supaya command
    // bisa resolve route central. Tapi route central HARUS dibatasi
    // domain ke host central saja — kalau tidak, request simulasi via
    // $kernel->handle() dalam script CLI akan kecocokkan route GET /
    // central untuk SEMUA host (tenant ikut kena redirect).
    if (empty($_SERVER['HTTP_HOST'])) {
        // Tidak ada host context — pakai daftar host central sebagai filter
        // agar tidak bocor ke host tenant.
        foreach ($centralHostList as $h) {
            Route::domain($h)->group($centralRoutes);
        }
    } else {
        $host = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST']);
        if (App\Support\HostContext::isCentral($host)) {
            foreach ($centralHostList as $h) {
                Route::domain($h)->group($centralRoutes);
            }
        }
        // else: skip — biar tenant route yang handle request di host ini.
    }
} else {
    $requestHost = $_SERVER['HTTP_HOST'] ?? '';
    // Strip port kalau ada (mis. localhost:8000)
    $host = preg_replace('/:\d+$/', '', $requestHost);

    if (App\Support\HostContext::isCentral($host)) {
        foreach ($centralHostList as $h) {
            Route::domain($h)->group($centralRoutes);
        }
    }
    // else: skip — biar tenant route yang handle request di host ini.
}
