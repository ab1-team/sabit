<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Landing\PengumumanLanding;
use App\Models\Landing\PesanKontakLanding;
use App\Models\Landing\AcaraLanding;
use App\Models\Landing\GaleriLanding;
use App\Models\Landing\SlideHeroLanding;
use App\Models\Landing\MenuLanding;
use App\Models\Landing\HalamanLanding;
use App\Models\Landing\ArtikelLanding;
use App\Models\Landing\FaqPpdb;
use App\Models\Landing\PersyaratanPpdb;
use App\Models\Landing\JadwalPpdb;
use App\Models\Landing\PengaturanPpdb;
use App\Models\Landing\TahapanPpdb;
use App\Models\Landing\PengaturanLanding;
use App\Models\Landing\VideoLanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Landing page publik tenant (tanpa autentikasi).
 *
 * PENTING: seluruh method di sini dapat diakses publik sementara koneksi DB
 * sudah menunjuk ke database tenant. Hanya query tabel lp_* dan JANGAN pernah
 * mengekspos data siswa, transaksi, atau tabel operasional lainnya.
 */
class HalamanPublikController extends Controller
{
    public function index()
    {
        return view('halaman-publik.beranda', [
            'setting' => PengaturanLanding::current(),
            'slides' => SlideHeroLanding::active()->orderBy('sort_order')->get(),
            'menus' => $this->menus(),
            'posts' => ArtikelLanding::published()->latest('published_at')->limit(6)->get(),
            'events' => AcaraLanding::published()->upcoming()->orderBy('start_date')->limit(4)->get(),
            'announcements' => PengumumanLanding::published()->latest('published_at')->limit(5)->get(),
            'galleries' => GaleriLanding::published()->orderBy('sort_order')->limit(8)->get(),
        ]);
    }

    public function page(string $slug)
    {
        $page = HalamanLanding::published()->where('slug', $slug)->firstOrFail();

        return view('halaman-publik.halaman', [
            'setting' => PengaturanLanding::current(),
            'menus' => $this->menus(),
            'page' => $page,
        ]);
    }

    /**
     * Halaman profil sekolah terpadu (sidebar nav + konten).
     * Mengambil data Visi&Misi & Struktur Organisasi dari lp_pages;
     * jika belum ada di DB, view akan pakai fallback statis.
     */
    public function profile()
    {
        $pages = HalamanLanding::published()
            ->whereIn('slug', ['visi-dan-misi', 'struktur-organisasi'])
            ->get()
            ->keyBy('slug');

        $strukturItems = \App\Models\Landing\StrukturOrganisasiLanding::published()->ordered()->get();
        $fasilitasItems = \App\Models\Landing\FasilitasLanding::published()->ordered()->get();
        $overviewSection = \App\Models\Landing\BagianProfilLanding::getByKey('overview');
        $sejarahSection = \App\Models\Landing\BagianProfilLanding::getByKey('sejarah');
        $visiMisiSection = \App\Models\Landing\BagianProfilLanding::getByKey('visi_misi');
        $akreditasiSection = \App\Models\Landing\BagianProfilLanding::getByKey('akreditasi');

        return view('halaman-publik.profil-sekolah', [
            'setting' => PengaturanLanding::current(),
            'menus' => $this->menus(),
            'pageVisiMisi' => $pages->get('visi-dan-misi'),
            'pageStruktur' => $pages->get('struktur-organisasi'),
            'strukturItems' => $strukturItems,
            'fasilitasItems' => $fasilitasItems,
            'overviewSection' => $overviewSection,
            'sejarahSection' => $sejarahSection,
            'visiMisiSection' => $visiMisiSection,
            'akreditasiSection' => $akreditasiSection,
        ]);
    }

    public function posts()
    {
        return view('halaman-publik.daftar-artikel', [
            'setting' => PengaturanLanding::current(),
            'menus' => $this->menus(),
            'posts' => ArtikelLanding::published()->latest('published_at')->paginate(9),
        ]);
    }

    public function post(string $slug)
    {
        $post = ArtikelLanding::published()->where('slug', $slug)->firstOrFail();

        $post->increment('views');

        return view('halaman-publik.artikel', [
            'setting' => PengaturanLanding::current(),
            'menus' => $this->menus(),
            'post' => $post,
        ]);
    }

    public function galleries(Request $request)
    {
        $album = $request->query('album');

        $query = GaleriLanding::published()->orderBy('sort_order');

        if ($album) {
            $query->where('album', $album);
        }

        return view('halaman-publik.galeri', [
            'setting' => PengaturanLanding::current(),
            'menus' => $this->menus(),
            'galleries' => $query->paginate(24),
            'albums' => GaleriLanding::published()
                ->whereNotNull('album')
                ->where('album', '!=', '')
                ->distinct()
                ->orderBy('album')
                ->pluck('album'),
            'album' => $album,
        ]);
    }

    public function videos()
    {
        return view('halaman-publik.video', [
            'setting' => PengaturanLanding::current(),
            'menus' => $this->menus(),
            'videos' => VideoLanding::published()->latest('id')->paginate(12),
        ]);
    }

    public function announcements()
    {
        return view('halaman-publik.pengumuman', [
            'setting' => PengaturanLanding::current(),
            'menus' => $this->menus(),
            'announcements' => PengumumanLanding::published()->latest('published_at')->paginate(15),
        ]);
    }

    public function contact()
    {
        return view('halaman-publik.kontak', [
            'setting' => PengaturanLanding::current(),
            'menus' => $this->menus(),
        ]);
    }

    public function ppdb(?string $section = null)
    {
        $setting     = PengaturanLanding::current();
        $ppdb        = PengaturanPpdb::current();
        $menus       = $this->menus();
        $requirements = PersyaratanPpdb::published()->ordered()->get();
        $stages      = TahapanPpdb::published()->ordered()->get();
        $schedules   = JadwalPpdb::published()->ordered()->get();
        $faqs        = FaqPpdb::published()->ordered()->get();

        $allowed = ['pendaftaran', 'persyaratan', 'alur', 'jadwal', 'faq'];
        $active  = $section && in_array($section, $allowed, true) ? $section : 'pendaftaran';

        return view('halaman-publik.ppdb', compact(
            'setting', 'ppdb', 'menus', 'requirements', 'stages', 'schedules', 'faqs', 'active'
        ));
    }

    /**
     * Form kontak publik. Dibatasi rate limit karena endpoint terbuka
     * dan riwayat data lama menunjukkan tingkat spam bot yang tinggi.
     */
    public function contactStore(Request $request)
    {
        $key = 'lp-contact:' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            return back()
                ->withInput()
                ->withErrors(['message' => 'Terlalu banyak pengiriman. Silakan coba lagi nanti.']);
        }

        RateLimiter::hit($key, 3600);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'subject' => ['nullable', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $data['ip_address'] = $request->ip();

        PesanKontakLanding::create($data);

        return back()->with('success', 'Pesan Anda telah terkirim. Terima kasih.');
    }

    /**
     * Menu header & footer dalam bentuk tree (root + children).
     */
    private function menus(): array
    {
        $all = Cache::remember('lp_menus_active', 600, function () {
            return MenuLanding::active()->orderBy('sort_order')->get();
        });

        return [
            'header' => $this->tree($all->where('position', 'header')),
            'footer' => $this->tree($all->where('position', 'footer')),
        ];
    }

    private function tree($items)
    {
        $grouped = $items->groupBy(fn ($m) => $m->parent_id ?? 0);

        return $grouped->get(0, collect())->map(function ($root) use ($grouped) {
            $root->child_items = $grouped->get($root->id, collect());

            return $root;
        });
    }
}
