<?php

declare(strict_types=1);

use App\Http\Controllers\HalamanPublikController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Landing Page Routes (PUBLIK)
|--------------------------------------------------------------------------
|
| Route website publik per sekolah. Didaftarkan oleh TenancyServiceProvider
| dengan middleware domain.type:landing sehingga hanya bisa diakses dari
| domain bertipe 'landing' (mis. sma1.example.test).
|
| Domain admin (mis. admin-sma1.example.test) memuat routes/tenant.php.
|
| PERINGATAN: seluruh route di file ini TIDAK memerlukan autentikasi, tetapi
| koneksi database sudah menunjuk ke database tenant. Hanya query tabel lp_*.
| Jangan pernah menambahkan route yang mengekspos data siswa atau transaksi
| di sini tanpa autentikasi dan pertimbangan keamanan yang matang.
|
*/

Route::get('/', [HalamanPublikController::class, 'index'])->name('halaman-publik.beranda');

Route::get('/berita', [HalamanPublikController::class, 'posts'])->name('halaman-publik.daftar-artikel');
Route::get('/berita/{slug}', [HalamanPublikController::class, 'post'])->name('halaman-publik.artikel');

Route::get('/galeri', [HalamanPublikController::class, 'galleries'])->name('halaman-publik.galeri');
Route::get('/video', [HalamanPublikController::class, 'videos'])->name('halaman-publik.video');
Route::get('/pengumuman', [HalamanPublikController::class, 'announcements'])->name('halaman-publik.pengumuman');

Route::get('/kontak', [HalamanPublikController::class, 'contact'])->name('halaman-publik.kontak');
Route::post('/kontak', [HalamanPublikController::class, 'contactStore'])->name('halaman-publik.kontak.store');

Route::get('/ppdb', [HalamanPublikController::class, 'ppdb'])->name('halaman-publik.ppdb');
Route::get('/ppdb/{section}', [HalamanPublikController::class, 'ppdb'])
    ->where('section', 'pendaftaran|persyaratan|alur|jadwal|faq')
    ->name('halaman-publik.ppdb.section');

// Halaman profil sekolah terpadu (sidebar nav: Overview, History, dll).
Route::get('/profil', [HalamanPublikController::class, 'profile'])->name('halaman-publik.profil');

// Halaman statis dinamis (visi-misi, sekilas-pandang, dst).
// Diletakkan paling akhir agar tidak menangkap URL di atas.
Route::get('/{slug}', [HalamanPublikController::class, 'page'])->name('halaman-publik.halaman');
