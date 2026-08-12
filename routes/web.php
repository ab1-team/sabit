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
| Domain pusat: env('CENTRAL_DOMAIN') => pusat.sabit.test
| Mengelola sekolah (subdomain tenant) dari pusat.
| Login pusat di /login (sebelumnya /master/login).
| Sekolah diakses via subdomain, mis: demo.sabit.test
|
*/

$centralRoutes = function () {
    Route::get('/', function () {
        return redirect('/login');
    });

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
            Route::post('jabatan', [JabatanPusatController::class, 'store'])->name('jabatan.store');
            Route::put('jabatan/{jabatan}', [JabatanPusatController::class, 'update'])->name('jabatan.update');
            Route::delete('jabatan/{jabatan}', [JabatanPusatController::class, 'destroy'])->name('jabatan.destroy');

            Route::get('kurikulum', [KurikulumPusatController::class, 'index'])->name('kurikulum.index');
            Route::post('kurikulum', [KurikulumPusatController::class, 'store'])->name('kurikulum.store');
            Route::put('kurikulum/{kurikulum}', [KurikulumPusatController::class, 'update'])->name('kurikulum.update');
            Route::delete('kurikulum/{kurikulum}', [KurikulumPusatController::class, 'destroy'])->name('kurikulum.destroy');

            Route::get('jurusan', [JurusanPusatController::class, 'index'])->name('jurusan.index');
            Route::post('jurusan', [JurusanPusatController::class, 'store'])->name('jurusan.store');
            Route::put('jurusan/{jurusan}', [JurusanPusatController::class, 'update'])->name('jurusan.update');
            Route::delete('jurusan/{jurusan}', [JurusanPusatController::class, 'destroy'])->name('jurusan.destroy');

            Route::get('kelas', [KelasPusatController::class, 'index'])->name('kelas.index');
            Route::post('kelas', [KelasPusatController::class, 'store'])->name('kelas.store');
            Route::put('kelas/{kelas}', [KelasPusatController::class, 'update'])->name('kelas.update');
            Route::delete('kelas/{kelas}', [KelasPusatController::class, 'destroy'])->name('kelas.destroy');

            Route::get('ruangan', [RuanganPusatController::class, 'index'])->name('ruangan.index');
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

$centralHosts = array_values(array_filter(array_unique(
    (array) config('tenancy.central_domains', [])
)));

foreach ($centralHosts as $host) {
    Route::domain($host)->group($centralRoutes);
}
