<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Landing\PengumumanLanding;
use App\Models\Landing\GaleriLanding;
use App\Models\Landing\SlideHeroLanding;
use App\Models\Landing\MenuLanding;
use App\Models\Landing\HalamanLanding;
use App\Models\Landing\ArtikelLanding;
use App\Models\Landing\PengaturanLanding;
use Illuminate\Database\Seeder;

/**
 * Konten default landing page untuk tenant baru.
 *
 * Dijalankan otomatis lewat TenantDatabaseSeeder saat tenant dibuat dari pusat,
 * sehingga setiap sekolah baru langsung punya website yang tampil rapi dan
 * tinggal disunting, bukan halaman kosong.
 *
 * Semua pemanggilan memakai firstOrCreate agar aman dijalankan ulang (idempoten).
 */
class LandingPageSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = tenant();
        $namaSekolah = $tenant?->nama_sekolah ?: 'Sekolah';
        $email = $tenant?->email;

        $this->seedSetting($namaSekolah, $email);
        $this->seedMenus();
        $this->seedPages($namaSekolah);
        $this->seedHeroSlides($namaSekolah);
        $this->seedSamplePost($namaSekolah);
        $this->seedSampleAnnouncement();
        $this->seedGalleryPlaceholder();
    }

    private function seedSetting(string $namaSekolah, ?string $email): void
    {
        if (PengaturanLanding::query()->exists()) {
            return;
        }

        PengaturanLanding::create([
            'school_name' => $namaSekolah,
            'tagline' => 'Website Resmi ' . $namaSekolah,
            'email' => $email,
            'meta_description' => 'Informasi resmi, berita, kegiatan, dan pengumuman ' . $namaSekolah . '.',
            'meta_keywords' => 'sekolah, pendidikan, ' . strtolower($namaSekolah),
        ]);
    }

    private function seedMenus(): void
    {
        if (MenuLanding::query()->exists()) {
            return;
        }

        // Header: ringkas, hanya tautan utama.
        $header = [
            ['title' => 'Home', 'url' => '/', 'sort_order' => 1],
            ['title' => 'Profil', 'url' => '/profil', 'sort_order' => 2],
            ['title' => 'Berita', 'url' => '/berita', 'sort_order' => 3],
            ['title' => 'Galeri', 'url' => '/galeri', 'sort_order' => 4],
            ['title' => 'Video', 'url' => '/video', 'sort_order' => 5],
            ['title' => 'Pengumuman', 'url' => '/pengumuman', 'sort_order' => 6],
            ['title' => 'Kontak', 'url' => '/kontak', 'sort_order' => 7],
        ];

        foreach ($header as $item) {
            MenuLanding::create($item + ['position' => 'header', 'is_active' => true]);
        }

        // Footer: Profil sebagai induk dengan sub-menu.
        $profil = MenuLanding::create([
            'title' => 'Profil',
            'url' => '#',
            'position' => 'footer',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $subProfil = [
            ['title' => 'Sekilas Pandang', 'url' => '/sekilas-pandang', 'sort_order' => 1],
            ['title' => 'Visi dan Misi', 'url' => '/visi-dan-misi', 'sort_order' => 2],
            ['title' => 'Struktur Organisasi', 'url' => '/struktur-organisasi', 'sort_order' => 3],
            ['title' => 'Sambutan Kepala Sekolah', 'url' => '/sambutan-kepala-sekolah', 'sort_order' => 4],
        ];

        foreach ($subProfil as $item) {
            MenuLanding::create($item + [
                'parent_id' => $profil->id,
                'position' => 'footer',
                'is_active' => true,
            ]);
        }

        $footer = [
            ['title' => 'Berita', 'url' => '/berita', 'sort_order' => 2],
            ['title' => 'Galeri', 'url' => '/galeri', 'sort_order' => 3],
            ['title' => 'Video', 'url' => '/video', 'sort_order' => 4],
            ['title' => 'Pengumuman', 'url' => '/pengumuman', 'sort_order' => 5],
            ['title' => 'Kontak Kami', 'url' => '/kontak', 'sort_order' => 6],
        ];

        foreach ($footer as $item) {
            MenuLanding::create($item + ['position' => 'footer', 'is_active' => true]);
        }
    }

    private function seedPages(string $namaSekolah): void
    {
        $pages = [
            [
                'title' => 'Sekilas Pandang',
                'slug' => 'sekilas-pandang',
                'content' => '<p>' . e($namaSekolah) . ' merupakan lembaga pendidikan yang berkomitmen '
                    . 'menyelenggarakan proses belajar mengajar yang bermutu, membentuk karakter peserta didik, '
                    . 'serta mengembangkan potensi akademik dan non-akademik secara seimbang.</p>'
                    . '<p><em>Silakan sunting halaman ini melalui menu Landing Page pada panel admin '
                    . 'untuk menyesuaikan dengan profil sekolah Anda.</em></p>',
            ],
            [
                'title' => 'Visi dan Misi',
                'slug' => 'visi-dan-misi',
                'content' => '<h3>Visi</h3>'
                    . '<p>Menjadi sekolah unggul dalam prestasi, berakhlak mulia, dan berwawasan global.</p>'
                    . '<h3>Misi</h3>'
                    . '<ol>'
                    . '<li>Menyelenggarakan pembelajaran yang aktif, kreatif, efektif, dan menyenangkan.</li>'
                    . '<li>Menumbuhkan penghayatan nilai keagamaan dan budaya bangsa.</li>'
                    . '<li>Mengembangkan potensi peserta didik secara optimal sesuai bakat dan minat.</li>'
                    . '<li>Meningkatkan kompetensi dan profesionalisme tenaga pendidik.</li>'
                    . '<li>Membangun kerja sama yang harmonis dengan orang tua dan masyarakat.</li>'
                    . '</ol>'
                    . '<p><em>Silakan sunting halaman ini melalui panel admin.</em></p>',
            ],
            [
                'title' => 'Struktur Organisasi',
                'slug' => 'struktur-organisasi',
                'content' => '<p>Struktur organisasi ' . e($namaSekolah) . ':</p>'
                    . '<ol>'
                    . '<li>Kepala Sekolah</li>'
                    . '<li>Wakil Kepala Sekolah Bidang Kurikulum</li>'
                    . '<li>Wakil Kepala Sekolah Bidang Kesiswaan</li>'
                    . '<li>Wakil Kepala Sekolah Bidang Sarana dan Prasarana</li>'
                    . '<li>Wakil Kepala Sekolah Bidang Hubungan Masyarakat</li>'
                    . '<li>Tata Usaha</li>'
                    . '<li>Bendahara Sekolah</li>'
                    . '<li>Wali Kelas dan Guru Mata Pelajaran</li>'
                    . '</ol>'
                    . '<p><em>Silakan lengkapi nama pejabat melalui panel admin.</em></p>',
            ],
            [
                'title' => 'Sambutan Kepala Sekolah',
                'slug' => 'sambutan-kepala-sekolah',
                'content' => '<p>Assalamualaikum warahmatullahi wabarakatuh.</p>'
                    . '<p>Puji syukur kami sampaikan atas kehadirat Tuhan Yang Maha Esa. Selamat datang di '
                    . 'website resmi ' . e($namaSekolah) . '. Website ini kami hadirkan sebagai sarana '
                    . 'informasi dan komunikasi antara sekolah, orang tua, peserta didik, dan masyarakat.</p>'
                    . '<p>Semoga kehadiran website ini memberikan manfaat bagi kita semua.</p>'
                    . '<p><em>Silakan sunting sambutan ini melalui panel admin.</em></p>',
            ],
        ];

        foreach ($pages as $page) {
            HalamanLanding::firstOrCreate(
                ['slug' => $page['slug']],
                $page + ['is_published' => true]
            );
        }
    }

    private function seedHeroSlides(string $namaSekolah): void
    {
        if (SlideHeroLanding::query()->exists()) {
            return;
        }

        // image dikosongkan: view menampilkan fallback gradient bila belum ada gambar,
        // sehingga admin dapat mengunggah gambar sendiri tanpa file placeholder di repo.
        SlideHeroLanding::create([
            'title' => 'Selamat Datang di ' . $namaSekolah,
            'subtitle' => 'Mencerdaskan, membentuk karakter, dan menyiapkan generasi unggul.',
            'image' => '',
            'button_text' => 'Profil Sekolah',
            'button_url' => '/profil',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        SlideHeroLanding::create([
            'title' => 'Informasi Pendaftaran',
            'subtitle' => 'Dapatkan informasi penerimaan peserta didik baru.',
            'image' => '',
            'button_text' => 'Hubungi Kami',
            'button_url' => '/kontak',
            'sort_order' => 2,
            'is_active' => false,
        ]);
    }

    private function seedSamplePost(string $namaSekolah): void
    {
        ArtikelLanding::firstOrCreate(
            ['slug' => 'selamat-datang-di-website-sekolah'],
            [
                'title' => 'Selamat Datang di Website ' . $namaSekolah,
                'excerpt' => 'Website resmi sekolah kini hadir sebagai pusat informasi kegiatan dan pengumuman.',
                'content' => '<p>Dengan bangga kami luncurkan website resmi ' . e($namaSekolah) . '. '
                    . 'Melalui website ini, sekolah akan membagikan berita kegiatan, pengumuman penting, '
                    . 'galeri foto, serta informasi akademik lainnya.</p>'
                    . '<p>Artikel ini adalah contoh. Anda dapat menyuntingnya atau menghapusnya '
                    . 'melalui panel admin sekolah.</p>',
                'category' => 'Pengumuman',
                'is_featured' => true,
                'is_published' => true,
                'published_at' => now(),
                'tags' => 'sekolah,informasi',
            ]
        );
    }

    private function seedSampleAnnouncement(): void
    {
        PengumumanLanding::firstOrCreate(
            ['title' => 'Contoh Pengumuman Sekolah'],
            [
                'content' => '<p>Ini adalah contoh pengumuman. Gunakan menu Landing Page pada panel admin '
                    . 'untuk menambahkan pengumuman resmi seperti jadwal ujian, agenda kegiatan, '
                    . 'atau edaran untuk orang tua.</p>',
                'published_at' => now(),
                'is_published' => true,
            ]
        );
    }

    private function seedGalleryPlaceholder(): void
    {
        if (GaleriLanding::query()->exists()) {
            return;
        }

        // Album default tanpa gambar; admin cukup mengunggah foto ke album ini.
        GaleriLanding::create([
            'title' => 'Contoh Album Kegiatan',
            'description' => 'Unggah foto kegiatan sekolah melalui panel admin.',
            'image' => '',
            'album' => 'Kegiatan Sekolah',
            'sort_order' => 1,
            'is_published' => false,
        ]);
    }
}
