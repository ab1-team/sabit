<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Landing\LpAnnouncement;
use App\Models\Landing\LpEvent;
use App\Models\Landing\LpGallery;
use App\Models\Landing\LpHeroSlide;
use App\Models\Landing\LpMenu;
use App\Models\Landing\LpPage;
use App\Models\Landing\LpPost;
use App\Models\Landing\LpPpdbFaq;
use App\Models\Landing\LpPpdbRequirement;
use App\Models\Landing\LpPpdbSchedule;
use App\Models\Landing\LpPpdbSetting;
use App\Models\Landing\LpPpdbStage;
use App\Models\Landing\LpSetting;
use App\Models\Landing\LpVideo;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Seeder dummy/testing khusus untuk landing page publik tenant.
 *
 * Menghasilkan 10 entitas per resource utama + 5 hero slide + settings + menu
 * lengkap (header + dropdown + footer) sehingga seluruh halaman landing
 * (/, /berita, /galeri, /video, /pengumuman, /kontak, /{slug}) bisa ditinjau
 * dengan konten yang representatif.
 *
 * Gambar di-download dari picsum.photos ke storage/app/public/landing/
 * agar konsisten dengan disk('public') yang dipakai seluruh view landing.
 *
 * Aman dijalankan berulang: pakai firstOrCreate berdasar slug/title.
 */
class LandingPageDummySeeder extends Seeder
{
    private const TENANT_FOLDER = 'landing';

    private string $diskPath = '';

    /** @var array<string,string> map kategori posts -> path file di storage */
    private array $images = [];

    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'timeout' => 30,
            'connect_timeout' => 10,
            'http_errors' => false,
        ]);
    }

    public function run(): void
    {
        $this->command?->info('LandingPageDummySeeder: menyiapkan folder & gambar...');

        $this->prepareStorage();
        $this->downloadImages();

        $tenant = tenant();
        $namaSekolah = $tenant?->nama_sekolah ?: 'Sekolah Demo';
        $email = $tenant?->email ?: 'demo@sekolah.test';

        $this->seedSetting($namaSekolah, $email);
        $this->seedMenus();
        $this->seedPages($namaSekolah);
        $this->seedHeroSlides($namaSekolah);
        $this->seedPosts();
        $this->seedEvents();
        $this->seedGalleries();
        $this->seedAnnouncements();
        $this->seedVideos();
        $this->seedPpdb($namaSekolah);

        $this->command?->info('LandingPageDummySeeder: selesai.');
    }

    /* -----------------------------------------------------------------
     | Storage & gambar
     * -----------------------------------------------------------------*/

    private function prepareStorage(): void
    {
        $this->diskPath = Storage::disk('public')->path(self::TENANT_FOLDER);
        if (!is_dir($this->diskPath)) {
            @mkdir($this->diskPath, 0775, true);
        }
    }

    /**
     * Download gambar dummy dari picsum.photos lalu simpan ke storage/landing/.
     * Daftar nama file digunakan oleh seeders di bawah untuk di-relasikan
     * ke masing-masing entitas.
     */
    private function downloadImages(): void
    {
        // Map: key internal -> [seed, width, height]
        $specs = [
            'hero-school'   => ['school',    1600, 900],
            'hero-students' => ['students',  1600, 900],
            'hero-class'    => ['classroom', 1600, 900],
            'hero-event'    => ['event',     1600, 900],
            'hero-celebrate'=> ['celebrate', 1600, 900],

            'page-welcome'  => ['welcome',    900, 1200],
            'page-vision'   => ['vision',     900,  600],
            'page-org'      => ['orgchart',   900,  600],
            'page-head'     => ['headmaster', 900, 1200],

            'post-1'        => ['post-1',  900, 600],
            'post-2'        => ['post-2',  900, 600],
            'post-3'        => ['post-3',  900, 600],
            'post-4'        => ['post-4',  900, 600],
            'post-5'        => ['post-5',  900, 600],
            'post-6'        => ['post-6',  900, 600],
            'post-7'        => ['post-7',  900, 600],
            'post-8'        => ['post-8',  900, 600],
            'post-9'        => ['post-9',  900, 600],
            'post-10'       => ['post-10', 900, 600],

            'event-1'       => ['event-1',  900, 600],
            'event-2'       => ['event-2',  900, 600],
            'event-3'       => ['event-3',  900, 600],
            'event-4'       => ['event-4',  900, 600],

            'gal-1'         => ['gal-1',   800, 800],
            'gal-2'         => ['gal-2',   800, 800],
            'gal-3'         => ['gal-3',   800, 800],
            'gal-4'         => ['gal-4',   800, 800],
            'gal-5'         => ['gal-5',   800, 800],
            'gal-6'         => ['gal-6',   800, 800],
            'gal-7'         => ['gal-7',   800, 800],
            'gal-8'         => ['gal-8',   800, 800],
            'gal-9'         => ['gal-9',   800, 800],
            'gal-10'        => ['gal-10',  800, 800],

            'vid-1'         => ['vid-1',   800, 450],
            'vid-2'         => ['vid-2',   800, 450],
            'vid-3'         => ['vid-3',   800, 450],
            'vid-4'         => ['vid-4',   800, 450],
            'vid-5'         => ['vid-5',   800, 450],
        ];

        foreach ($specs as $key => [$seed, $w, $h]) {
            $filename = "{$key}.jpg";
            $fullPath = $this->diskPath . DIRECTORY_SEPARATOR . $filename;

            if (file_exists($fullPath) && filesize($fullPath) > 1024) {
                $this->images[$key] = $filename;
                continue;
            }

            // picsum.photos: /seed/{seed}/{w}/{h}.jpg
            $url = "https://picsum.photos/seed/{$seed}/{$w}/{$h}.jpg";

            try {
                $response = $this->http->get($url, ['allow_redirects' => true]);
                $code = $response->getStatusCode();

                if ($code === 200 && ($body = $response->getBody()->getContents()) !== '') {
                    file_put_contents($fullPath, $body);
                    $this->images[$key] = $filename;
                    $this->command?->getOutput()?->writeln("  + {$filename}");
                } else {
                    $this->command?->warn("Lewati {$key}: HTTP {$code}");
                }
            } catch (GuzzleException $e) {
                $this->command?->warn("Gagal download {$key}: " . $e->getMessage());
            }
        }
    }

    private function img(string $key): ?string
    {
        return $this->images[$key] ?? null;
    }

    /* -----------------------------------------------------------------
     | Seeders per resource
     * -----------------------------------------------------------------*/

    private function seedSetting(string $namaSekolah, string $email): void
    {
        $setting = LpSetting::query()->first() ?? new LpSetting();
        $setting->fill([
            'school_name'      => $namaSekolah,
            'tagline'          => 'Mendidik dengan Hati, Meraih Prestasi',
            'logo'             => null,
            'favicon'          => null,
            'email'            => $email,
            'phone'            => '(021) 1234-5678',
            'whatsapp'         => '6281234567890',
            'address'          => 'Jl. Pendidikan No. 1, Jakarta Selatan 12345',
            'google_maps_url'  => 'https://maps.google.com/?q=Jakarta',
            'facebook'         => 'https://facebook.com/sekolah',
            'instagram'        => 'https://instagram.com/sekolah',
            'youtube'          => 'https://youtube.com/@sekolah',
            'tiktok'           => 'https://tiktok.com/@sekolah',
            'meta_description' => 'Website resmi ' . $namaSekolah . ' - informasi PPDB, berita kegiatan, dan pengumuman.',
            'meta_keywords'    => 'sekolah, ppdb, pendidikan, ' . strtolower($namaSekolah),
        ]);
        $setting->save();
    }

    private function seedMenus(): void
    {
        if (LpMenu::query()->exists()) {
            return;
        }

        $home = LpMenu::create([
            'title'      => 'Home',
            'url'        => '/',
            'position'   => 'header',
            'sort_order' => 1,
            'is_active'  => true,
        ]);

        $profil = LpMenu::create([
            'title'      => 'Profil',
            'url'        => '#',
            'position'   => 'header',
            'sort_order' => 2,
            'is_active'  => true,
        ]);

        $profilSubs = [
            ['title' => 'Sekilas Pandang', 'url' => '/sekilas-pandang'],
            ['title' => 'Visi & Misi',     'url' => '/visi-dan-misi'],
            ['title' => 'Struktur Organisasi', 'url' => '/struktur-organisasi'],
            ['title' => 'Sambutan Kepala Sekolah', 'url' => '/sambutan-kepala-sekolah'],
        ];
        foreach ($profilSubs as $i => $sub) {
            LpMenu::create($sub + [
                'parent_id'  => $profil->id,
                'position'   => 'header',
                'sort_order' => $i + 1,
                'is_active'  => true,
            ]);
        }

        $headerRest = [
            ['title' => 'Berita',     'url' => '/berita',     'sort_order' => 3],
            ['title' => 'Galeri',     'url' => '/galeri',     'sort_order' => 4],
            ['title' => 'Video',      'url' => '/video',      'sort_order' => 5],
            ['title' => 'Pengumuman', 'url' => '/pengumuman', 'sort_order' => 6],
            ['title' => 'Kontak',     'url' => '/kontak',     'sort_order' => 7],
        ];
        foreach ($headerRest as $m) {
            LpMenu::create($m + ['position' => 'header', 'is_active' => true]);
        }

        // Footer mirror (lebih lengkap, untuk review konsistensi).
        $footerProfil = LpMenu::create([
            'title' => 'Profil', 'url' => '/profil',
            'position' => 'footer', 'sort_order' => 1, 'is_active' => true,
        ]);
        foreach ($profilSubs as $i => $sub) {
            LpMenu::create($sub + [
                'parent_id' => $footerProfil->id,
                'position' => 'footer', 'sort_order' => $i + 1, 'is_active' => true,
            ]);
        }
        $footerRest = [
            ['title' => 'Berita',     'url' => '/berita',     'sort_order' => 2],
            ['title' => 'Galeri',     'url' => '/galeri',     'sort_order' => 3],
            ['title' => 'Video',      'url' => '/video',      'sort_order' => 4],
            ['title' => 'Pengumuman', 'url' => '/pengumuman', 'sort_order' => 5],
            ['title' => 'Kontak Kami', 'url' => '/kontak',    'sort_order' => 6],
        ];
        foreach ($footerRest as $m) {
            LpMenu::create($m + ['position' => 'footer', 'is_active' => true]);
        }
    }

    private function seedPages(string $namaSekolah): void
    {
        $pages = [
            [
                'slug'    => 'sekilas-pandang',
                'title'   => 'Sekilas Pandang',
                'content' => '<p>' . e($namaSekolah) . ' merupakan lembaga pendidikan yang berkomitmen '
                    . 'menyelenggarakan proses belajar mengajar yang bermutu, membentuk karakter peserta didik, '
                    . 'serta mengembangkan potensi akademik dan non-akademik secara seimbang.</p>'
                    . '<p>Didirikan sejak tahun 1990, sekolah kami telah meluluskan ribuan alumni yang '
                    . 'berkiprah di berbagai bidang. Fasilitas modern, tenaga pendidik profesional, dan '
                    . 'lingkungan belajar yang nyaman menjadi komitmen kami untuk masa depan peserta didik.</p>'
                    . '<p><em>Artikel ini contoh. Sunting melalui panel admin.</em></p>',
                'image'   => $this->img('page-welcome'),
            ],
            [
                'slug'    => 'visi-dan-misi',
                'title'   => 'Visi dan Misi',
                'content' => '<h3>Visi</h3>'
                    . '<p>Menjadi sekolah unggul dalam prestasi, berakhlak mulia, dan berwawasan global.</p>'
                    . '<h3>Misi</h3>'
                    . '<ol>'
                    . '<li>Menyelenggarakan pembelajaran aktif, kreatif, efektif, dan menyenangkan.</li>'
                    . '<li>Menumbuhkan penghayatan nilai keagamaan dan budaya bangsa.</li>'
                    . '<li>Mengembangkan potensi peserta didik sesuai bakat dan minat.</li>'
                    . '<li>Meningkatkan kompetensi dan profesionalisme tenaga pendidik.</li>'
                    . '<li>Membangun kerja sama harmonis dengan orang tua dan masyarakat.</li>'
                    . '</ol>',
                'image'   => $this->img('page-vision'),
            ],
            [
                'slug'    => 'struktur-organisasi',
                'title'   => 'Struktur Organisasi',
                'content' => '<p>Struktur organisasi ' . e($namaSekolah) . ' disusun untuk mendukung '
                    . 'tata kelola yang efektif dan akuntabel.</p>'
                    . '<ol>'
                    . '<li>Kepala Sekolah</li>'
                    . '<li>Wakil Kepala Sekolah Bidang Kurikulum</li>'
                    . '<li>Wakil Kepala Sekolah Bidang Kesiswaan</li>'
                    . '<li>Wakil Kepala Sekolah Bidang Sarana Prasarana</li>'
                    . '<li>Wakil Kepala Sekolah Bidang Humas</li>'
                    . '<li>Tata Usaha</li>'
                    . '<li>Bendahara Sekolah</li>'
                    . '<li>Wali Kelas dan Guru Mata Pelajaran</li>'
                    . '</ol>',
                'image'   => $this->img('page-org'),
            ],
            [
                'slug'    => 'sambutan-kepala-sekolah',
                'title'   => 'Sambutan Kepala Sekolah',
                'content' => '<p>Assalamualaikum warahmatullahi wabarakatuh.</p>'
                    . '<p>Puji syukur kami sampaikan. Selamat datang di website resmi ' . e($namaSekolah) . '. '
                    . 'Website ini kami hadirkan sebagai sarana informasi dan komunikasi antara sekolah, '
                    . 'orang tua, peserta didik, dan masyarakat.</p>'
                    . '<p>Semoga website ini memberikan manfaat bagi kita semua.</p>',
                'image'   => $this->img('page-head'),
            ],
        ];

        foreach ($pages as $page) {
            LpPage::updateOrCreate(
                ['slug' => $page['slug']],
                $page + ['is_published' => true]
            );
        }
    }

    private function seedHeroSlides(string $namaSekolah): void
    {
        $slides = [
            [
                'title'       => 'Selamat Datang di ' . $namaSekolah,
                'subtitle'    => 'Mendidik dengan hati, membentuk karakter, meraih prestasi gemilang.',
                'image'       => $this->img('hero-school'),
                'button_text' => 'Profil Sekolah',
                'button_url'  => '/profil',
                'sort_order'  => 1,
            ],
            [
                'title'       => 'Penerimaan Peserta Didik Baru 2026/2027',
                'subtitle'    => 'Daftarkan putra-putri Anda sekarang. Kuota terbatas setiap jenjang.',
                'image'       => $this->img('hero-students'),
                'button_text' => 'Info PPDB',
                'button_url'  => '/kontak',
                'sort_order'  => 2,
            ],
            [
                'title'       => 'Pembelajaran Aktif & Kreatif',
                'subtitle'    => 'Kurikulum merdeka dengan pendekatan student-centered dan fasilitas modern.',
                'image'       => $this->img('hero-class'),
                'button_text' => 'Program Unggulan',
                'button_url'  => '/berita',
                'sort_order'  => 3,
            ],
            [
                'title'       => 'Ekstrakurikuler Beragam',
                'subtitle'    => 'Wadah pengembangan bakat dan minat siswa di berbagai bidang.',
                'image'       => $this->img('hero-event'),
                'button_text' => 'Galeri Kegiatan',
                'button_url'  => '/galeri',
                'sort_order'  => 4,
            ],
            [
                'title'       => 'Prestasi Membanggakan',
                'subtitle'    => 'Ratusan prestasi akademik & non-akademik tingkat kota hingga nasional.',
                'image'       => $this->img('hero-celebrate'),
                'button_text' => 'Berita Terbaru',
                'button_url'  => '/berita',
                'sort_order'  => 5,
            ],
        ];

        foreach ($slides as $s) {
            LpHeroSlide::updateOrCreate(
                ['sort_order' => $s['sort_order']],
                $s + ['is_active' => true]
            );
        }
    }

    private function seedPosts(): void
    {
        $categories = ['Pengumuman', 'Prestasi', 'Kegiatan', 'Akademik', 'Tips'];

        $posts = [
            ['Selamat Datang di Website Resmi Sekolah', 'pengumuman', 'Website resmi kini hadir sebagai pusat informasi kegiatan dan pengumuman sekolah.'],
            ['Juara 1 Lomba Sains Tingkat Provinsi', 'prestasi', 'Siswa kami berhasil meraih juara 1 lomba sains tingkat provinsi.'],
            ['Pentas Seni Akhir Tahun Sukses Digelar', 'kegiatan', 'Pentas seni akhir tahun menampilkan berbagai kreativitas siswa.'],
            ['Workshop Kurikulum Merdeka untuk Guru', 'akademik', 'Pelatihan kurikulum merdeka untuk meningkatkan kualitas pembelajaran.'],
            ['Tips Belajar Efektif untuk Siswa', 'tips', 'Berbagai tips belajar efektif yang bisa diterapkan siswa sehari-hari.'],
            ['Kunjungan Industri ke Perusahaan Teknologi', 'kegiatan', 'Siswa Jurusan TKJ melakukan kunjungan industri ke perusahaan teknologi.'],
            ['Penerimaan Rapor Semester Ganjil', 'pengumuman', 'Pembagian rapor semester ganjil akan dilaksanakan sesuai jadwal.'],
            ['Olimpiade Matematika Nasional 2026', 'prestasi', 'Tim olimpiade matematika siap berlaga di tingkat nasional.'],
            ['Bakti Sosial di Panti Asuhan', 'kegiatan', 'OSIS mengadakan bakti sosial di panti asuhan terdekat.'],
            ['Sosialisasi PPDB 2026/2027', 'pengumuman', 'Sosialisasi PPDB untuk orang tua calon peserta didik baru.'],
        ];

        foreach ($posts as $i => [$title, $cat, $excerpt]) {
            $slug = Str::slug($title);
            $imgKey = 'post-' . ($i + 1);

            LpPost::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'        => $title,
                    'excerpt'      => $excerpt,
                    'content'      => '<p>' . e($excerpt) . '</p>'
                        . '<p>Artikel ini adalah contoh konten untuk pengujian tampilan landing page. '
                        . 'Anda dapat menyunting atau menghapus artikel ini melalui panel admin sekolah. '
                        . 'Teks lengkap dapat ditambahkan dengan format HTML sesuai kebutuhan.</p>'
                        . '<p>Bagian ini memperlihatkan bagaimana paragraf, daftar, dan elemen HTML lainnya '
                        . 'ditampilkan di halaman detail berita.</p>',
                    'image'        => $this->img($imgKey),
                    'category'     => $cat,
                    'is_featured'  => $i < 3,
                    'is_published' => true,
                    'published_at' => now()->subDays($i * 3),
                    'views'        => rand(50, 500),
                    'tags'         => 'sekolah,' . $cat,
                ]
            );
        }
    }

    private function seedEvents(): void
    {
        $events = [
            ['Upacara Bendera Memperingati Hari Kemerdekaan', 'Lapangan Utama Sekolah', '08:00'],
            ['Rapat Orang Tua Siswa',                        'Aula Sekolah',              '09:00'],
            ['Lomba kebersihan kelas antar kelas',           'Seluruh Ruangan Kelas',     '07:30'],
            ['Pentas Seni & Budaya',                         'Gedung Serbaguna',          '19:00'],
            ['Class Meeting Akhir Semester',                 'Lapangan & Aula',           '08:00'],
            ['Study Tour ke Museum',                         'Museum Nasional',           '07:00'],
            ['Bazaar & Bazar Amal',                          'Halaman Sekolah',           '08:00'],
            ['Malam Apresiasi Seni',                         'Aula Serbaguna',            '18:30'],
            ['Workshop Anti Bullying',                       'Ruang Multimedia',          '10:00'],
            ['Pelepasan Alumni',                             'Aula Utama',                '13:00'],
        ];

        foreach ($events as $i => [$title, $loc, $time]) {
            $start = now()->addDays(($i + 1) * 5);

            LpEvent::updateOrCreate(
                ['title' => $title],
                [
                    'description' => 'Acara ' . $title . ' akan dilaksanakan sesuai jadwal. '
                        . 'Detail teknis akan diinformasikan lebih lanjut melalui pengumuman resmi.',
                    'location'    => $loc,
                    'image'       => $this->img('event-' . (($i % 4) + 1)),
                    'start_date'  => $start->toDateString(),
                    'end_date'    => $start->copy()->addDay()->toDateString(),
                    'start_time'  => $time,
                    'is_published'=> true,
                ]
            );
        }
    }

    private function seedGalleries(): void
    {
        $albums = ['Kegiatan Sekolah', 'Akademik', 'Ekstrakurikuler', 'Prestasi'];

        $items = [
            ['Upacara Bendera 17 Agustus',   'kegiatan'],
            ['Lomba Cerdas Cermat',          'akademik'],
            ['Pentas Seni Tari',             'ekstrakurikuler'],
            ['Juara Lomba Sains',            'prestasi'],
            ['Kunjungan Perpustakaan',       'akademik'],
            ['Latihan Drumband',             'ekstrakurikuler'],
            ['Wisuda Angkatan',              'kegiatan'],
            ['Class Meeting',                'kegiatan'],
            ['Workshop Robotik',             'ekstrakurikuler'],
            ['Bakti Sosial',                 'kegiatan'],
        ];

        foreach ($items as $i => [$title, $album]) {
            LpGallery::updateOrCreate(
                ['title' => $title],
                [
                    'description' => 'Dokumentasi ' . $title . ' yang dilaksanakan oleh sekolah.',
                    'image'       => $this->img('gal-' . ($i + 1)),
                    'album'       => $albums[$i % count($albums)],
                    'sort_order'  => $i + 1,
                    'is_published'=> true,
                ]
            );
        }
    }

    private function seedAnnouncements(): void
    {
        $items = [
            'Jadwal Ujian Tengah Semester',
            'Libur Nasional dan Cuti Bersama',
            'Pemberitahuan Pembayaran SPP',
            'Pendaftaran Ekstrakurikuler Dibuka',
            'Hasil Seleksi PPDB Tahap 1',
            'Jadwal Rapat Komite Sekolah',
            'Pemberlakuan Tata Tertib Baru',
            'Undangan Orang Tua Siswa',
            'Informasi Beasiswa Prestasi',
            'Peringatan Hari Besar Nasional',
        ];

        foreach ($items as $i => $title) {
            LpAnnouncement::updateOrCreate(
                ['title' => $title],
                [
                    'content'      => '<p>' . e($title) . ' - Mohon memperhatikan informasi ini dengan seksama. '
                        . 'Detail lengkap dapat dilihat pada papan pengumuman sekolah atau menghubungi '
                        . 'panitia terkait. Informasi ini berlaku untuk seluruh warga sekolah.</p>'
                        . '<p>Terima kasih atas perhatian dan kerja samanya.</p>',
                    'published_at' => now()->subDays($i * 2),
                    'is_published' => true,
                ]
            );
        }
    }

    private function seedVideos(): void
    {
        $videos = [
            ['Profil Sekolah',          'dQw4w9WgXcQ'],
            ['Kegiatan Belajar Mengajar','9bZkp7q19f0'],
            ['Pentas Seni 2025',        'kJQP7kiw5Fk'],
            ['Wisuda Angkatan 2025',    'RgKAFK5djSk'],
            ['Highlight Lomba Sains',   'OPf0YbXqDm0'],
        ];

        foreach ($videos as $i => [$title, $ytId]) {
            LpVideo::updateOrCreate(
                ['title' => $title],
                [
                    'description' => 'Video dokumentasi ' . $title . ' dalam format YouTube.',
                    'youtube_url' => "https://www.youtube.com/watch?v={$ytId}",
                    'thumbnail'   => $this->img('vid-' . ($i + 1)),
                    'is_published'=> true,
                ]
            );
        }
    }

    private function seedPpdb(string $namaSekolah): void
    {
        $ppdb = LpPpdbSetting::query()->first() ?? new LpPpdbSetting();
        $ppdb->fill([
            'school_name'    => $namaSekolah,
            'eyebrow'        => 'Penerimaan Peserta Didik Baru',
            'title'          => 'Penerimaan Peserta Didik Baru (PPDB) ' . $namaSekolah,
            'subtitle'       => 'Mari bergabung bersama kami wujudkan pendidikan berkualitas. Pendaftaran tahun ajaran ' . (date('Y')) . '/' . (date('Y') + 1) . ' telah dibuka untuk seluruh jenjang.',
            'cta_text'       => 'Formulir Pendaftaran Online',
            'cta_url'        => '/kontak',
            'secondary_text' => 'Konsultasi & Bantuan',
            'secondary_url'  => '/kontak',
            'is_active'      => true,
        ]);
        $ppdb->save();

        $requirements = [
            [
                'group'      => 'umum',
                'title'      => 'Syarat Umum',
                'sort_order' => 1,
                'items'      => [
                    'Berusia sesuai jenjang pada saat pendaftaran.',
                    'Membayar biaya pendaftaran sesuai gelombang yang dipilih.',
                    'Melengkapi formulir pendaftaran online dengan data yang benar.',
                    'Menyerahkan seluruh dokumen persyaratan ke panitia PPDB.',
                ],
            ],
            [
                'group'      => 'dokumen',
                'title'      => 'Dokumen yang Diperlukan',
                'sort_order' => 2,
                'items'      => [
                    'Fotokopi akta kelahiran (2 lembar).',
                    'Fotokopi Kartu Keluarga (2 lembar).',
                    'Pas foto berwarna 3x4 sebanyak 4 lembar.',
                    'Raport terakhir (semester ganjil & genap).',
                ],
            ],
            [
                'group'      => 'tambahan',
                'title'      => 'Syarat Tambahan',
                'sort_order' => 3,
                'items'      => [
                    'Surat keterangan sehat dari dokter.',
                    'Surat kelakuan baik dari sekolah asal.',
                    'Surat pernyataan orang tua/wali bermaterai.',
                ],
            ],
        ];

        foreach ($requirements as $req) {
            LpPpdbRequirement::updateOrCreate(
                ['title' => $req['title']],
                [
                    'group'        => $req['group'],
                    'title'        => $req['title'],
                    'items'        => json_encode($req['items'], JSON_UNESCAPED_UNICODE),
                    'sort_order'   => $req['sort_order'],
                    'is_published' => true,
                ]
            );
        }

        $stages = [
            ['title' => 'Registrasi Online',           'description' => 'Mengisi formulir pendaftaran online pada website resmi sekolah dan mengunggah dokumen yang diperlukan.', 'sort_order' => 1],
            ['title' => 'Verifikasi Berkas',           'description' => 'Panitia PPDB memverifikasi kelengkapan dan keabsahan dokumen yang telah diunggah.',                       'sort_order' => 2],
            ['title' => 'Tes Seleksi & Wawancara',     'description' => 'Peserta mengikuti tes akademik dan wawancara sesuai jadwal yang telah ditentukan.',                    'sort_order' => 3],
            ['title' => 'Pengumuman Hasil',            'description' => 'Hasil seleksi diumumkan melalui website dan papan pengumuman sekolah.',                                   'sort_order' => 4],
            ['title' => 'Daftar Ulang',                'description' => 'Peserta yang diterima melakukan daftar ulang dengan membayar biaya pendidikan sesuai ketentuan.',         'sort_order' => 5],
        ];

        foreach ($stages as $s) {
            LpPpdbStage::updateOrCreate(
                ['title' => $s['title']],
                $s + ['is_published' => true]
            );
        }

        $year = (int) date('Y');
        $schedules = [
            ['gelombang' => 'Gelombang 1', 'start_date' => "$year-01-15", 'end_date' => "$year-03-15", 'biaya_daftar' => 'Rp 250.000', 'spp_bulanan' => 'Rp 500.000', 'sort_order' => 1],
            ['gelombang' => 'Gelombang 2', 'start_date' => "$year-03-16", 'end_date' => "$year-05-15", 'biaya_daftar' => 'Rp 300.000', 'spp_bulanan' => 'Rp 550.000', 'sort_order' => 2],
            ['gelombang' => 'Gelombang 3', 'start_date' => "$year-05-16", 'end_date' => "$year-07-15", 'biaya_daftar' => 'Rp 350.000', 'spp_bulanan' => 'Rp 600.000', 'sort_order' => 3],
        ];

        foreach ($schedules as $sc) {
            LpPpdbSchedule::updateOrCreate(
                ['gelombang' => $sc['gelombang']],
                $sc + ['is_published' => true]
            );
        }

        $faqs = [
            [
                'question' => 'Apakah boleh mendaftarkan anak di dua gelombang yang berbeda?',
                'answer'   => 'Tidak. Setiap calon peserta didik hanya boleh mendaftar pada satu gelombang. Jika diterima, peserta tidak dapat dialihkan ke gelombang lain.',
                'sort_order' => 1,
            ],
            [
                'question' => 'Bagaimana jika belum memiliki akta kelahiran?',
                'answer'   => 'Calon peserta didik tetap dapat mendaftar dengan menggunakan surat keterangan lahir dari rumah sakit atau kelurahan, kemudian melengkapi akta sebelum daftar ulang.',
                'sort_order' => 2,
            ],
            [
                'question' => 'Apakah ada tes akademik untuk calon peserta didik?',
                'answer'   => 'Ya, untuk jenjang SD tidak ada tes akademik. Untuk SMP dan SMA akan ada tes akademik dasar (matematika, bahasa Indonesia, IPA) serta wawancara orang tua.',
                'sort_order' => 3,
            ],
            [
                'question' => 'Bagaimana cara mengetahui hasil seleksi?',
                'answer'   => 'Hasil seleksi dapat dilihat di website resmi sekolah pada menu Pengumuman, serta diumumkan di papan informasi sekolah.',
                'sort_order' => 4,
            ],
            [
                'question' => 'Apakah tersedia beasiswa untuk calon peserta didik berprestasi?',
                'answer'   => 'Ya, tersedia beasiswa prestasi akademik dan non-akademik. Informasi lengkap dapat diperoleh dengan menghubungi panitia PPDB.',
                'sort_order' => 5,
            ],
        ];

        foreach ($faqs as $f) {
            LpPpdbFaq::updateOrCreate(
                ['question' => $f['question']],
                $f + ['is_published' => true]
            );
        }
    }
}
