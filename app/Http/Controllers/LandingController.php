<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Landing\LpAnnouncement;
use App\Models\Landing\LpContactMessage;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Landing page publik tenant (tanpa autentikasi).
 *
 * PENTING: seluruh method di sini dapat diakses publik sementara koneksi DB
 * sudah menunjuk ke database tenant. Hanya query tabel lp_* dan JANGAN pernah
 * mengekspos data siswa, transaksi, atau tabel operasional lainnya.
 */
class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index', [
            'setting' => LpSetting::current(),
            'slides' => LpHeroSlide::active()->orderBy('sort_order')->get(),
            'menus' => $this->menus(),
            'posts' => LpPost::published()->latest('published_at')->limit(6)->get(),
            'events' => LpEvent::published()->upcoming()->orderBy('start_date')->limit(4)->get(),
            'announcements' => LpAnnouncement::published()->latest('published_at')->limit(5)->get(),
            'galleries' => LpGallery::published()->orderBy('sort_order')->limit(8)->get(),
        ]);
    }

    public function page(string $slug)
    {
        $page = LpPage::published()->where('slug', $slug)->firstOrFail();

        return view('landing.halaman', [
            'setting' => LpSetting::current(),
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
        $pages = LpPage::published()
            ->whereIn('slug', ['visi-dan-misi', 'struktur-organisasi'])
            ->get()
            ->keyBy('slug');

        $strukturItems = \App\Models\Landing\LpStrukturOrganisasi::published()->ordered()->get();
        $fasilitasItems = \App\Models\Landing\LpFasilitas::published()->ordered()->get();
        $overviewSection = \App\Models\Landing\LpProfileSection::getByKey('overview');
        $sejarahSection = \App\Models\Landing\LpProfileSection::getByKey('sejarah');
        $visiMisiSection = \App\Models\Landing\LpProfileSection::getByKey('visi_misi');
        $akreditasiSection = \App\Models\Landing\LpProfileSection::getByKey('akreditasi');

        return view('landing.profil', [
            'setting' => LpSetting::current(),
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
        return view('landing.daftar-artikel', [
            'setting' => LpSetting::current(),
            'menus' => $this->menus(),
            'posts' => LpPost::published()->latest('published_at')->paginate(9),
        ]);
    }

    public function post(string $slug)
    {
        $post = LpPost::published()->where('slug', $slug)->firstOrFail();

        $post->increment('views');

        return view('landing.artikel', [
            'setting' => LpSetting::current(),
            'menus' => $this->menus(),
            'post' => $post,
        ]);
    }

    public function galleries(Request $request)
    {
        $album = $request->query('album');

        $query = LpGallery::published()->orderBy('sort_order');

        if ($album) {
            $query->where('album', $album);
        }

        return view('landing.galeri', [
            'setting' => LpSetting::current(),
            'menus' => $this->menus(),
            'galleries' => $query->paginate(24),
            'albums' => LpGallery::published()
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
        return view('landing.video', [
            'setting' => LpSetting::current(),
            'menus' => $this->menus(),
            'videos' => LpVideo::published()->latest('id')->paginate(12),
        ]);
    }

    public function announcements()
    {
        return view('landing.pengumuman', [
            'setting' => LpSetting::current(),
            'menus' => $this->menus(),
            'announcements' => LpAnnouncement::published()->latest('published_at')->paginate(15),
        ]);
    }

    public function contact()
    {
        return view('landing.kontak', [
            'setting' => LpSetting::current(),
            'menus' => $this->menus(),
        ]);
    }

    public function ppdb(?string $section = null)
    {
        $setting     = LpSetting::current();
        $ppdb        = LpPpdbSetting::current();
        $menus       = $this->menus();
        $requirements = LpPpdbRequirement::published()->ordered()->get();
        $stages      = LpPpdbStage::published()->ordered()->get();
        $schedules   = LpPpdbSchedule::published()->ordered()->get();
        $faqs        = LpPpdbFaq::published()->ordered()->get();

        $allowed = ['pendaftaran', 'persyaratan', 'alur', 'jadwal', 'faq'];
        $active  = $section && in_array($section, $allowed, true) ? $section : 'pendaftaran';

        return view('landing.ppdb', compact(
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

        LpContactMessage::create($data);

        return back()->with('success', 'Pesan Anda telah terkirim. Terima kasih.');
    }

    /**
     * Menu header & footer dalam bentuk tree (root + children).
     */
    private function menus(): array
    {
        $all = LpMenu::active()->orderBy('sort_order')->get();

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
