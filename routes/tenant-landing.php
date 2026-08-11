<?php

declare(strict_types=1);

use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Landing Page Routes (PUBLIK)
|--------------------------------------------------------------------------
|
| Route website publik per sekolah. Didaftarkan oleh TenancyServiceProvider
| dengan middleware domain.type:landing sehingga hanya bisa diakses dari
| domain bertipe 'landing' (mis. sma1.sabit.test).
|
| Domain admin (mis. admin-sma1.sabit.test) memuat routes/tenant.php.
|
| PERINGATAN: seluruh route di file ini TIDAK memerlukan autentikasi, tetapi
| koneksi database sudah menunjuk ke database tenant. Hanya query tabel lp_*.
| Jangan pernah menambahkan route yang mengekspos data siswa atau transaksi
| di sini tanpa autentikasi dan pertimbangan keamanan yang matang.
|
*/

Route::get('/', [LandingController::class, 'index'])->name('landing.home');

Route::get('/berita', [LandingController::class, 'posts'])->name('landing.daftar-artikel');
Route::get('/berita/{slug}', [LandingController::class, 'post'])->name('landing.artikel');

Route::get('/galeri', [LandingController::class, 'galleries'])->name('landing.galeri');
Route::get('/video', [LandingController::class, 'videos'])->name('landing.video');
Route::get('/pengumuman', [LandingController::class, 'announcements'])->name('landing.pengumuman');

Route::get('/kontak', [LandingController::class, 'contact'])->name('landing.kontak');
Route::post('/kontak', [LandingController::class, 'contactStore'])->name('landing.kontak.store');

Route::get('/ppdb', [LandingController::class, 'ppdb'])->name('landing.ppdb');
Route::get('/ppdb/{section}', [LandingController::class, 'ppdb'])
    ->where('section', 'pendaftaran|persyaratan|alur|jadwal|faq')
    ->name('landing.ppdb.section');

// Halaman profil sekolah terpadu (sidebar nav: Overview, History, dll).
Route::get('/profil', [LandingController::class, 'profile'])->name('landing.profil');

// Halaman statis dinamis (visi-misi, sekilas-pandang, dst).
// Diletakkan paling akhir agar tidak menangkap URL di atas.
Route::get('/{slug}', [LandingController::class, 'page'])->name('landing.halaman');
