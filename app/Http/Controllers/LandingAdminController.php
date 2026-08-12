<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Landing\LpAnnouncement;
use App\Models\Landing\LpContactMessage;
use App\Models\Landing\LpGallery;
use App\Models\Landing\LpHeroSlide;
use App\Models\Landing\LpPage;
use App\Models\Landing\LpPost;
use App\Models\Landing\LpPpdbFaq;
use App\Models\Landing\LpPpdbRequirement;
use App\Models\Landing\LpPpdbSchedule;
use App\Models\Landing\LpPpdbSetting;
use App\Models\Landing\LpPpdbStage;
use App\Models\Landing\LpProfileSection;
use App\Models\Landing\LpSetting;
use App\Models\Landing\LpStrukturOrganisasi;
use App\Models\Landing\LpFasilitas;
use App\Models\Landing\LpVideo;
use App\Models\Landing\LpEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Trait LandingAdminResponse - helper untuk mengirim response
 * yang konsisten antara submit AJAX (SweetAlert) dan submit biasa (redirect).
 */
trait LandingAdminResponse
{
    protected function wantsJsonResponse(Request $request): bool
    {
        return $request->wantsJson() || $request->ajax() || $request->headers->has('X-Requested-With');
    }

    protected function saveSuccess(Request $request, string $msg, ?string $redirectRoute = null, array $extra = [])
    {
        if ($this->wantsJsonResponse($request)) {
            return response()->json(array_merge([
                'success' => true,
                'msg' => $msg,
                'redirect' => $redirectRoute ? route($redirectRoute) : null,
            ], $extra));
        }
        return redirect()->route($redirectRoute)->with('success', $msg);
    }

    protected function deleteSuccess(Request $request, string $msg, ?string $redirectRoute = null)
    {
        if ($this->wantsJsonResponse($request)) {
            return response()->json([
                'success' => true,
                'msg' => $msg,
            ]);
        }
        return redirect()->route($redirectRoute)->with('success', $msg);
    }
}

/**
 * Pengelolaan konten landing page dari domain admin (/app/landing/*).
 *
 * Akses dibatasi middleware hak.akses:landing sehingga hanya user dengan
 * hak akses menu "Landing Page" (atau superadmin '*') yang dapat membuka.
 */
class LandingAdminController extends Controller
{
    use LandingAdminResponse;

    public function index()
    {
        $tenant = tenant();

        return view('landing-admin.index', [
            'title' => 'Landing Page',
            'setting' => LpSetting::current(),
            'landingUrl' => $tenant?->landingUrl(),
            'stats' => [
                'posts' => LpPost::count(),
                'galleries' => LpGallery::count(),
                'announcements' => LpAnnouncement::count(),
                'unread_messages' => LpContactMessage::unread()->count(),
            ],
        ]);
    }

    public function pengaturan()
    {
        $tenant = tenant();

        return view('landing-admin.pengaturan', [
            'title' => 'Pengaturan Landing Page',
            'setting' => LpSetting::current(),
            'landingUrl' => $tenant?->landingUrl(),
        ]);
    }

    public function pengaturanStore(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['required', 'string', 'max:150'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'whatsapp' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string'],
            'google_maps_url' => ['nullable', 'string'],
            'facebook' => ['nullable', 'url', 'max:255'],
            'instagram' => ['nullable', 'url', 'max:255'],
            'youtube' => ['nullable', 'url', 'max:255'],
            'tiktok' => ['nullable', 'url', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'hero_background_choice' => ['nullable', 'string', 'max:255'],
            'hero_background_custom' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:10240',
            ],
            'theme_button_color_choice' => ['nullable', 'string', 'max:255'],
            'theme_button_color_custom' => ['nullable', 'string', 'max:20', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico,jpg,jpeg', 'max:512'],
        ], [
            'hero_background_custom.image' => 'File background harus berupa gambar.',
            'hero_background_custom.mimes' => 'Format background harus JPG, JPEG, PNG, atau WEBP.',
            'hero_background_custom.max' => 'Ukuran background maksimal 10 MB (akan otomatis dikecilkan ke 1920×1080).',
        ]);

        $setting = LpSetting::query()->first() ?? new LpSetting();

        foreach (['logo', 'favicon'] as $field) {
            if ($request->hasFile($field)) {
                if ($setting->{$field}) {
                    Storage::disk('public')->delete($this->diskPath($setting->{$field}));
                }
                $data[$field] = basename(
                    $request->file($field)->store($this->uploadDir(), 'public')
                );
            } else {
                unset($data[$field]);
            }
        }

        $bgChoice = $request->input('hero_background_choice');
        if ($request->hasFile('hero_background_custom')) {
            if ($setting->hero_background && str_starts_with($setting->hero_background, 'custom:')) {
                $oldName = substr($setting->hero_background, strlen('custom:'));
                if ($oldName !== '') {
                    Storage::disk('public')->delete($this->diskPath($oldName));
                }
            }

            $uploaded = $request->file('hero_background_custom');
            $disk = Storage::disk('public');
            $dir = $this->uploadDir();

            // Cover-fit resize ke 1920x1080 (JPG, kualitas 85).
            $resizedTmp = $this->resizeToCover($uploaded->getRealPath(), 1920, 1080);

            $customName = null;
            if ($resizedTmp && is_file($resizedTmp)) {
                $basename = 'hero-bg-' . Str::random(10) . '.jpg';
                $stored = $disk->putFileAs($dir, new \Illuminate\Http\UploadedFile($resizedTmp, $basename, 'image/jpeg', null, true), $basename);
                @unlink($resizedTmp);
                if ($stored) {
                    $customName = basename($stored);
                }
            }

            // Fallback: kalau resize gagal, simpan file original apa adanya.
            if (!$customName) {
                $customName = basename($uploaded->store($dir, 'public'));
            }

            $data['hero_background'] = 'custom:' . $customName;
        } elseif ($bgChoice === 'custom') {
            $data['hero_background'] = $setting->hero_background;
        } elseif ($bgChoice) {
            $validKeys = array_column(LpSetting::themeBackgroundDefaults(), 'key');
            $data['hero_background'] = in_array($bgChoice, $validKeys, true) ? $bgChoice : 'default-1';
        } else {
            unset($data['hero_background']);
        }

        unset($data['hero_background_choice'], $data['hero_background_custom']);

        $resolvedButton = $this->resolveThemeColor(
            $request->input('theme_button_color_choice'),
            $request->input('theme_button_color_custom'),
            array_column(LpSetting::themeButtonColorDefaults(), 'key', 'key'),
        );
        if ($resolvedButton !== null) {
            $data['theme_button_color'] = $resolvedButton;
            $data['theme_text_color'] = $resolvedButton;
        }

        unset($data['theme_button_color_choice'], $data['theme_button_color_custom']);

        $setting->fill($data)->save();

        // Kumpulkan metadata file baru (untuk konfirmasi 'tersimpan' di modal post-save).
        $newFileMeta = null;
        if ($setting->hero_background && str_starts_with($setting->hero_background, 'custom:')) {
            $f = substr($setting->hero_background, strlen('custom:'));
            $disk = Storage::disk('public');
            if ($disk->exists($this->diskPath($f))) {
                $fullPath = $disk->path($this->diskPath($f));
                [$w, $h] = getimagesize($fullPath) ?: [0, 0];
                $bytes = filesize($fullPath);
                $newFileMeta = [
                    'name' => $f,
                    'width' => $w,
                    'height' => $h,
                    'size_label' => $bytes >= 1048576
                        ? number_format($bytes / 1048576, 2) . ' MB'
                        : number_format($bytes / 1024, 0) . ' KB',
                ];
            }
        }

        if ($this->wantsJsonResponse($request)) {
            return response()->json([
                'success' => true,
                'msg' => 'Pengaturan landing page berhasil disimpan.',
                'hero_background_url' => $setting->heroBackgroundUrl(),
                'hero_background_key' => $setting->activeThemeBackgroundKey(),
                'hero_background_meta' => $newFileMeta,
                'landing_url' => tenant()?->landingUrl(),
                'redirect' => route('app.landing.pengaturan'),
            ]);
        }

        return redirect()
            ->route('app.landing.pengaturan')
            ->with('success', 'Pengaturan landing page berhasil disimpan.');
    }

    /**
     * Hapus custom background yang tersimpan dan kembalikan ke default-1.
     * Dipakai admin saat ingin membuang foto upload sendiri tanpa upload baru.
     */
    public function hapusCustomBackground(Request $request)
    {
        $setting = LpSetting::query()->first();
        $cleared = false;

        if ($setting && $setting->hero_background && str_starts_with($setting->hero_background, 'custom:')) {
            $oldName = substr($setting->hero_background, strlen('custom:'));
            if ($oldName !== '') {
                Storage::disk('public')->delete($this->diskPath($oldName));
            }
            $setting->hero_background = 'default-1';
            $setting->save();
            $cleared = true;
        }

        if ($this->wantsJsonResponse($request)) {
            return response()->json([
                'success' => $cleared,
                'msg' => $cleared
                    ? 'Background custom berhasil dihapus, dikembalikan ke Standar 1.'
                    : 'Tidak ada background custom yang tersimpan.',
                'redirect' => route('app.landing.pengaturan'),
            ]);
        }

        return redirect()
            ->route('app.landing.pengaturan')
            ->with($cleared ? 'success' : 'info', $cleared
                ? 'Background custom berhasil dihapus, dikembalikan ke Standar 1.'
                : 'Tidak ada background custom yang tersimpan.');
    }

    public function hero()
    {
        return view('landing-admin.hero', [
            'title' => 'Hero Slider',
            'slides' => LpHeroSlide::orderBy('sort_order')->get(),
        ]);
    }

    public function heroStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        $data['sort_order'] = $data['sort_order'] ?? (LpHeroSlide::max('sort_order') + 1);
        $data['is_active'] = $request->boolean('is_active');

        LpHeroSlide::create($data);

        return $this->saveSuccess($request, 'Slide berhasil ditambahkan.', 'app.landing.hero');
    }

    public function heroUpdate(Request $request, $slide)
    {
        $model = LpHeroSlide::findOrFail($slide);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'button_text' => ['nullable', 'string', 'max:100'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($model->image) {
                Storage::disk('public')->delete($this->diskPath($model->image));
            }
            $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        } else {
            unset($data['image']);
        }

        $data['is_active'] = $request->boolean('is_active');

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Slide berhasil diperbarui.', 'app.landing.hero');
    }

    public function heroDestroy(Request $request, $slide)
    {
        $model = LpHeroSlide::findOrFail($slide);

        if ($model->image) {
            Storage::disk('public')->delete($this->diskPath($model->image));
        }

        $model->delete();

        return $this->deleteSuccess($request, 'Slide berhasil dihapus.', 'app.landing.hero');
    }

    public function posts()
    {
        return view('landing-admin.posts.index', [
            'title' => 'Program / Berita',
            'posts' => LpPost::orderByDesc('published_at')->orderByDesc('id')->paginate(15)->withQueryString(),
        ]);
    }

    public function postCreate()
    {
        return view('landing-admin.posts.form', [
            'title' => 'Tambah Program / Berita',
            'post' => new LpPost(),
            'action' => route('app.landing.posts.store'),
        ]);
    }

    public function postStore(Request $request)
    {
        $data = $this->validatePost($request);
        $data['slug'] = $this->uniqueSlug($data['title'], LpPost::class);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['published_at'] ?? now();

        if ($request->hasFile('image')) {
            $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        }

        LpPost::create($data);

        return $this->saveSuccess($request, 'Program / berita berhasil ditambahkan.', 'app.landing.posts');
    }

    public function postEdit($post)
    {
        $model = LpPost::findOrFail($post);

        return view('landing-admin.posts.form', [
            'title' => 'Edit Program / Berita',
            'post' => $model,
            'action' => route('app.landing.posts.update', $model->id),
        ]);
    }

    public function postUpdate(Request $request, $post)
    {
        $model = LpPost::findOrFail($post);
        $data = $this->validatePost($request);

        if (Str::lower($data['title']) !== Str::lower($model->title)) {
            $data['slug'] = $this->uniqueSlug($data['title'], LpPost::class, $model->id);
        }

        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($model->image) {
                Storage::disk('public')->delete($this->diskPath($model->image));
            }
            $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        }

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Program / berita berhasil diperbarui.', 'app.landing.posts');
    }

    public function postDestroy(Request $request, $post)
    {
        $model = LpPost::findOrFail($post);
        if ($model->image) {
            Storage::disk('public')->delete($this->diskPath($model->image));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Program / berita berhasil dihapus.', 'app.landing.posts');
    }

    private function validatePost(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);
    }

    public function announcements()
    {
        return view('landing-admin.announcements.index', [
            'title' => 'Pengumuman',
            'announcements' => LpAnnouncement::orderByDesc('published_at')->orderByDesc('id')->paginate(15)->withQueryString(),
        ]);
    }

    public function announcementCreate()
    {
        return view('landing-admin.announcements.form', [
            'title' => 'Tambah Pengumuman',
            'announcement' => new LpAnnouncement(),
            'action' => route('app.landing.announcements.store'),
        ]);
    }

    public function announcementStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:4096'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['published_at'] ?? now();

        if ($request->hasFile('file')) {
            $data['file'] = basename($request->file('file')->store($this->uploadDir(), 'public'));
        }

        LpAnnouncement::create($data);

        return $this->saveSuccess($request, 'Pengumuman berhasil ditambahkan.', 'app.landing.announcements');
    }

    public function announcementEdit($announcement)
    {
        $model = LpAnnouncement::findOrFail($announcement);

        return view('landing-admin.announcements.form', [
            'title' => 'Edit Pengumuman',
            'announcement' => $model,
            'action' => route('app.landing.announcements.update', $model->id),
        ]);
    }

    public function announcementUpdate(Request $request, $announcement)
    {
        $model = LpAnnouncement::findOrFail($announcement);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
            'file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png', 'max:4096'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('file')) {
            if ($model->file) {
                Storage::disk('public')->delete($this->diskPath($model->file));
            }
            $data['file'] = basename($request->file('file')->store($this->uploadDir(), 'public'));
        }

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Pengumuman berhasil diperbarui.', 'app.landing.announcements');
    }

    public function announcementDestroy(Request $request, $announcement)
    {
        $model = LpAnnouncement::findOrFail($announcement);
        if ($model->file) {
            Storage::disk('public')->delete($this->diskPath($model->file));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Pengumuman berhasil dihapus.', 'app.landing.announcements');
    }

    public function galleries()
    {
        return view('landing-admin.galleries.index', [
            'title' => 'Galeri',
            'galleries' => LpGallery::orderBy('sort_order')->orderByDesc('id')->paginate(24)->withQueryString(),
        ]);
    }

    public function galleryCreate()
    {
        return view('landing-admin.galleries.form', [
            'title' => 'Tambah Foto Galeri',
            'gallery' => new LpGallery(),
            'action' => route('app.landing.galleries.store'),
        ]);
    }

    public function galleryStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'album' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        $data['sort_order'] = $data['sort_order'] ?? (LpGallery::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        LpGallery::create($data);

        return $this->saveSuccess($request, 'Foto berhasil ditambahkan.', 'app.landing.galleries');
    }

    public function galleryEdit($gallery)
    {
        $model = LpGallery::findOrFail($gallery);

        return view('landing-admin.galleries.form', [
            'title' => 'Edit Foto Galeri',
            'gallery' => $model,
            'action' => route('app.landing.galleries.update', $model->id),
        ]);
    }

    public function galleryUpdate(Request $request, $gallery)
    {
        $model = LpGallery::findOrFail($gallery);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'album' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($model->image) {
                Storage::disk('public')->delete($this->diskPath($model->image));
            }
            $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        }

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Foto berhasil diperbarui.', 'app.landing.galleries');
    }

    public function galleryDestroy(Request $request, $gallery)
    {
        $model = LpGallery::findOrFail($gallery);
        if ($model->image) {
            Storage::disk('public')->delete($this->diskPath($model->image));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Foto berhasil dihapus.', 'app.landing.galleries');
    }

    public function videos()
    {
        return view('landing-admin.videos.index', [
            'title' => 'Video',
            'videos' => LpVideo::orderByDesc('id')->paginate(15)->withQueryString(),
        ]);
    }

    public function videoCreate()
    {
        return view('landing-admin.videos.form', [
            'title' => 'Tambah Video',
            'video' => new LpVideo(),
            'action' => route('app.landing.videos.store'),
        ]);
    }

    public function videoStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'youtube_url' => ['required', 'url', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = basename($request->file('thumbnail')->store($this->uploadDir(), 'public'));
        }

        LpVideo::create($data);

        return $this->saveSuccess($request, 'Video berhasil ditambahkan.', 'app.landing.videos');
    }

    public function videoEdit($video)
    {
        $model = LpVideo::findOrFail($video);

        return view('landing-admin.videos.form', [
            'title' => 'Edit Video',
            'video' => $model,
            'action' => route('app.landing.videos.update', $model->id),
        ]);
    }

    public function videoUpdate(Request $request, $video)
    {
        $model = LpVideo::findOrFail($video);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'youtube_url' => ['required', 'url', 'max:500'],
            'thumbnail' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('thumbnail')) {
            if ($model->thumbnail) {
                Storage::disk('public')->delete($this->diskPath($model->thumbnail));
            }
            $data['thumbnail'] = basename($request->file('thumbnail')->store($this->uploadDir(), 'public'));
        }

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Video berhasil diperbarui.', 'app.landing.videos');
    }

    public function videoDestroy(Request $request, $video)
    {
        $model = LpVideo::findOrFail($video);
        if ($model->thumbnail) {
            Storage::disk('public')->delete($this->diskPath($model->thumbnail));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Video berhasil dihapus.', 'app.landing.videos');
    }

    public function pages()
    {
        return view('landing-admin.pages.index', [
            'title' => 'Halaman Statis',
            'pages' => LpPage::orderByDesc('id')->paginate(15)->withQueryString(),
        ]);
    }

    public function pageCreate()
    {
        return view('landing-admin.pages.form', [
            'title' => 'Tambah Halaman',
            'page' => new LpPage(),
            'action' => route('app.landing.pages.store'),
        ]);
    }

    public function pageStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['slug'] = $data['slug']
            ? Str::slug($data['slug'])
            : $this->uniqueSlug($data['title'], LpPage::class);
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        }

        LpPage::create($data);

        return $this->saveSuccess($request, 'Halaman berhasil ditambahkan.', 'app.landing.pages');
    }

    public function pageEdit($page)
    {
        $model = LpPage::findOrFail($page);

        return view('landing-admin.pages.form', [
            'title' => 'Edit Halaman',
            'page' => $model,
            'action' => route('app.landing.pages.update', $model->id),
        ]);
    }

    public function pageUpdate(Request $request, $page)
    {
        $model = LpPage::findOrFail($page);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
            if ($data['slug'] !== $model->slug) {
                $existing = LpPage::where('slug', $data['slug'])->where('id', '!=', $model->id)->exists();
                if ($existing) {
                    return back()->withErrors(['slug' => 'Slug sudah dipakai halaman lain.'])->withInput();
                }
            }
        } else {
            unset($data['slug']);
        }

        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($model->image) {
                Storage::disk('public')->delete($this->diskPath($model->image));
            }
            $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        }

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Halaman berhasil diperbarui.', 'app.landing.pages');
    }

    public function pageDestroy(Request $request, $page)
    {
        $model = LpPage::findOrFail($page);
        if ($model->image) {
            Storage::disk('public')->delete($this->diskPath($model->image));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Halaman berhasil dihapus.', 'app.landing.pages');
    }

    public function events()
    {
        return view('landing-admin.events.index', [
            'title' => 'Acara / Agenda',
            'events' => LpEvent::orderByDesc('start_date')->orderByDesc('id')->paginate(15)->withQueryString(),
        ]);
    }

    public function eventCreate()
    {
        return view('landing-admin.events.form', [
            'title' => 'Tambah Acara',
            'event' => new LpEvent(),
            'action' => route('app.landing.events.store'),
        ]);
    }

    public function eventStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        LpEvent::create($data);

        return $this->saveSuccess($request, 'Acara berhasil ditambahkan.', 'app.landing.events');
    }

    public function eventEdit($event)
    {
        $model = LpEvent::findOrFail($event);

        return view('landing-admin.events.form', [
            'title' => 'Edit Acara',
            'event' => $model,
            'action' => route('app.landing.events.update', $model->id),
        ]);
    }

    public function eventUpdate(Request $request, $event)
    {
        $model = LpEvent::findOrFail($event);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Acara berhasil diperbarui.', 'app.landing.events');
    }

    public function eventDestroy(Request $request, $event)
    {
        $model = LpEvent::findOrFail($event);
        $model->delete();

        return $this->deleteSuccess($request, 'Acara berhasil dihapus.', 'app.landing.events');
    }

    public function struktur()
    {
        return view('landing-admin.struktur.index', [
            'title' => 'Struktur Organisasi',
            'items' => LpStrukturOrganisasi::orderByDesc('is_lead')->orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString(),
        ]);
    }

    public function strukturCreate()
    {
        return view('landing-admin.struktur.form', [
            'title' => 'Tambah Struktur',
            'item' => new LpStrukturOrganisasi(),
            'action' => route('app.landing.struktur.store'),
        ]);
    }

    public function strukturStore(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'role' => ['required', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_lead' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_lead'] = $request->boolean('is_lead');
        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? (LpStrukturOrganisasi::max('sort_order') + 1);

        if ($request->hasFile('photo')) {
            $data['photo'] = basename($request->file('photo')->store($this->uploadDir(), 'public'));
        }

        LpStrukturOrganisasi::create($data);

        return $this->saveSuccess($request, 'Struktur berhasil ditambahkan.', 'app.landing.struktur');
    }

    public function strukturEdit($item)
    {
        $model = LpStrukturOrganisasi::findOrFail($item);

        return view('landing-admin.struktur.form', [
            'title' => 'Edit Struktur',
            'item' => $model,
            'action' => route('app.landing.struktur.update', $model->id),
        ]);
    }

    public function strukturUpdate(Request $request, $item)
    {
        $model = LpStrukturOrganisasi::findOrFail($item);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'role' => ['required', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_lead' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_lead'] = $request->boolean('is_lead');
        $data['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('photo')) {
            if ($model->photo) {
                Storage::disk('public')->delete($this->diskPath($model->photo));
            }
            $data['photo'] = basename($request->file('photo')->store($this->uploadDir(), 'public'));
        }

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Struktur berhasil diperbarui.', 'app.landing.struktur');
    }

    public function strukturDestroy(Request $request, $item)
    {
        $model = LpStrukturOrganisasi::findOrFail($item);
        if ($model->photo) {
            Storage::disk('public')->delete($this->diskPath($model->photo));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Struktur berhasil dihapus.', 'app.landing.struktur');
    }

    public function fasilitas()
    {
        return view('landing-admin.fasilitas.index', [
            'title' => 'Fasilitas Sekolah',
            'items' => LpFasilitas::orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString(),
        ]);
    }

    public function fasilitasCreate()
    {
        return view('landing-admin.fasilitas.form', [
            'title' => 'Tambah Fasilitas',
            'item' => new LpFasilitas(),
            'action' => route('app.landing.fasilitas.store'),
        ]);
    }

    public function fasilitasStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:80'],
            'color_key' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['sort_order'] = $data['sort_order'] ?? (LpFasilitas::max('sort_order') + 1);

        LpFasilitas::create($data);

        return $this->saveSuccess($request, 'Fasilitas berhasil ditambahkan.', 'app.landing.fasilitas');
    }

    public function fasilitasEdit($item)
    {
        $model = LpFasilitas::findOrFail($item);

        return view('landing-admin.fasilitas.form', [
            'title' => 'Edit Fasilitas',
            'item' => $model,
            'action' => route('app.landing.fasilitas.update', $model->id),
        ]);
    }

    public function fasilitasUpdate(Request $request, $item)
    {
        $model = LpFasilitas::findOrFail($item);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'string', 'max:80'],
            'color_key' => ['nullable', 'string', 'max:30'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Fasilitas berhasil diperbarui.', 'app.landing.fasilitas');
    }

    public function fasilitasDestroy(Request $request, $item)
    {
        $model = LpFasilitas::findOrFail($item);
        $model->delete();

        return $this->deleteSuccess($request, 'Fasilitas berhasil dihapus.', 'app.landing.fasilitas');
    }

    public function profileSections()
    {
        $this->ensureProfileSectionDefaults();

        return view('landing-admin.profile-sections.index', [
            'title' => 'Section Profil',
            'items' => LpProfileSection::orderBy('id')->get(),
        ]);
    }

    private function ensureProfileSectionDefaults(): void
    {
        $defaults = [
            [
                'section_key' => 'overview',
                'title' => 'Nurturing Future Leaders Since 1995',
                'subtitle' => null,
                'content' => 'Sejarah Singkat: Elite Elementary School was founded with a vision to provide world-class education rooted in strong moral values. Over the decades, we have grown into a premier institution dedicated to academic excellence and character building.',
                'badge_text' => 'Akreditasi A',
                'badge_icon' => 'bi-patch-check-fill',
                'badge_extra' => '20212345',
                'extra_label' => 'NPSN',
                'is_active' => true,
            ],
            [
                'section_key' => 'sejarah',
                'title' => 'Sejarah',
                'subtitle' => null,
                'content' => 'Didirikan sejak tahun 1995, sekolah kami telah berkembang menjadi lembaga pendidikan yang dipercaya masyarakat. Perjalanan panjang ini ditandai dengan berbagai inovasi pembelajaran dan pencapaian prestasi di tingkat kota, provinsi, hingga nasional.',
                'badge_text' => null,
                'badge_icon' => null,
                'badge_extra' => null,
                'extra_label' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'akreditasi',
                'title' => 'Akreditasi',
                'subtitle' => null,
                'content' => 'Status akreditasi A (Sangat Baik) diberikan oleh BAN-SM, mencerminkan komitmen kami terhadap mutu pendidikan, manajemen sekolah, dan pencapaian lulusan yang berkualitas.',
                'badge_text' => 'Terakreditasi A',
                'badge_icon' => 'bi-award-fill',
                'badge_extra' => null,
                'extra_label' => null,
                'is_active' => true,
            ],
        ];

        foreach ($defaults as $row) {
            LpProfileSection::firstOrCreate(['section_key' => $row['section_key']], $row);
        }
    }

    public function profileSectionEdit($item)
    {
        $model = LpProfileSection::findOrFail($item);

        return view('landing-admin.profile-sections.form', [
            'title' => 'Edit Section: ' . $model->title,
            'item' => $model,
            'action' => route('app.landing.profile-sections.update', $model->id),
        ]);
    }

    public function profileSectionUpdate(Request $request, $item)
    {
        $model = LpProfileSection::findOrFail($item);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'badge_icon' => ['nullable', 'string', 'max:80'],
            'badge_extra' => ['nullable', 'string', 'max:100'],
            'extra_label' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Section profil berhasil diperbarui.', 'app.landing.profile-sections');
    }

    public function ppdbCta()
    {
        $setting = LpSetting::current();
        return view('landing-admin.ppdb-cta', [
            'title' => 'CTA PPDB Landing',
            'setting' => $setting,
            'action' => route('app.landing.ppdb-cta.store'),
            'cta' => $setting->ppdbCtaData(),
        ]);
    }

    public function ppdbCtaStore(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'paragraph' => ['nullable', 'string'],
            'button_primary_text' => ['required', 'string', 'max:80'],
            'button_primary_url' => ['nullable', 'string', 'max:255'],
            'button_secondary_text' => ['required', 'string', 'max:80'],
            'button_secondary_url' => ['nullable', 'string', 'max:255'],
            'points' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $points = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $request->input('points')))));

        $payload = [
            'title' => $data['title'],
            'paragraph' => $data['paragraph'] ?? '',
            'button_primary_text' => $data['button_primary_text'],
            'button_primary_url' => $data['button_primary_url'] ?? '',
            'button_secondary_text' => $data['button_secondary_text'],
            'button_secondary_url' => $data['button_secondary_url'] ?? '',
            'points' => $points,
            'is_active' => $request->boolean('is_active'),
        ];

        $setting = LpSetting::current();
        $setting->ppdb_cta = $payload;
        $setting->save();

        return $this->saveSuccess($request, 'CTA PPDB berhasil disimpan.', 'app.landing.ppdb-cta');
    }

    // -----------------------------------------------------------------
    // Sub-halaman yang di-link dari menu 'Profil'
    // (method struktur() & fasilitas() sudah didefinisikan di atas).
    // -----------------------------------------------------------------

    // -----------------------------------------------------------------
    // Menu 'Kontak' - pesan masuk dari form landing publik
    // -----------------------------------------------------------------

    public function contactMessages(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $query = LpContactMessage::query()->orderByDesc('id');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('subject', 'like', "%{$q}%")
                  ->orWhere('message', 'like', "%{$q}%");
            });
        }
        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        $messages = $query->paginate(20)->withQueryString();

        return view('landing-admin.contact-messages.index', [
            'title' => 'Pesan Masuk',
            'messages' => $messages,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function contactMessageMark(Request $request, $message)
    {
        $model = LpContactMessage::findOrFail($message);
        $model->is_read = $request->boolean('is_read', true);
        $model->save();

        return $this->saveSuccess(
            $request,
            $model->is_read ? 'Pesan ditandai sudah dibaca.' : 'Pesan ditandai belum dibaca.',
            'app.landing.contact-messages'
        );
    }

    public function contactMessageDestroy(Request $request, $message)
    {
        $model = LpContactMessage::findOrFail($message);
        $model->delete();

        return $this->deleteSuccess($request, 'Pesan berhasil dihapus.', 'app.landing.contact-messages');
    }

    // -----------------------------------------------------------------
    // Sub-CRUD PPDB (requirements, stages, schedules, faqs)
    // -----------------------------------------------------------------

    public function ppdbRequirements(Request $request)
    {
        return view('landing-admin.ppdb.requirements.index', [
            'title' => 'PPDB — Persyaratan',
            'items' => LpPpdbRequirement::ordered()->paginate(20)->withQueryString(),
        ]);
    }

    public function ppdbRequirementStore(Request $request)
    {
        $data = $request->validate([
            'group' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:200'],
            'items' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['items'] = $this->decodeItemsString($data['items'] ?? '');
        $data['sort_order'] = $data['sort_order'] ?? (LpPpdbRequirement::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        LpPpdbRequirement::create($data);
        return $this->saveSuccess($request, 'Persyaratan berhasil ditambahkan.', 'app.landing.ppdb.requirements');
    }

    public function ppdbRequirementUpdate(Request $request, $item)
    {
        $model = LpPpdbRequirement::findOrFail($item);
        $data = $request->validate([
            'group' => ['nullable', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:200'],
            'items' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['items'] = $this->decodeItemsString($data['items'] ?? '');
        $data['is_published'] = $request->boolean('is_published');

        $model->fill($data)->save();
        return $this->saveSuccess($request, 'Persyaratan berhasil diperbarui.', 'app.landing.ppdb.requirements');
    }

    public function ppdbRequirementDestroy(Request $request, $item)
    {
        $model = LpPpdbRequirement::findOrFail($item);
        $model->delete();
        return $this->deleteSuccess($request, 'Persyaratan berhasil dihapus.', 'app.landing.ppdb.requirements');
    }

    public function ppdbStages(Request $request)
    {
        return view('landing-admin.ppdb.stages.index', [
            'title' => 'PPDB — Alur / Tahapan',
            'items' => LpPpdbStage::ordered()->paginate(20)->withQueryString(),
        ]);
    }

    public function ppdbStageStore(Request $request)
    {
        $data = $request->validate([
            'step_label' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? (LpPpdbStage::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        LpPpdbStage::create($data);
        return $this->saveSuccess($request, 'Tahapan berhasil ditambahkan.', 'app.landing.ppdb.stages');
    }

    public function ppdbStageUpdate(Request $request, $item)
    {
        $model = LpPpdbStage::findOrFail($item);
        $data = $request->validate([
            'step_label' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $model->fill($data)->save();
        return $this->saveSuccess($request, 'Tahapan berhasil diperbarui.', 'app.landing.ppdb.stages');
    }

    public function ppdbStageDestroy(Request $request, $item)
    {
        $model = LpPpdbStage::findOrFail($item);
        $model->delete();
        return $this->deleteSuccess($request, 'Tahapan berhasil dihapus.', 'app.landing.ppdb.stages');
    }

    public function ppdbSchedules(Request $request)
    {
        return view('landing-admin.ppdb.schedules.index', [
            'title' => 'PPDB — Jadwal / Gelombang',
            'items' => LpPpdbSchedule::ordered()->paginate(20)->withQueryString(),
        ]);
    }

    public function ppdbScheduleStore(Request $request)
    {
        $data = $request->validate([
            'gelombang' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'biaya_daftar' => ['nullable', 'string', 'max:100'],
            'spp_bulanan' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? (LpPpdbSchedule::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        LpPpdbSchedule::create($data);
        return $this->saveSuccess($request, 'Jadwal berhasil ditambahkan.', 'app.landing.ppdb.schedules');
    }

    public function ppdbScheduleUpdate(Request $request, $item)
    {
        $model = LpPpdbSchedule::findOrFail($item);
        $data = $request->validate([
            'gelombang' => ['required', 'string', 'max:100'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'biaya_daftar' => ['nullable', 'string', 'max:100'],
            'spp_bulanan' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $model->fill($data)->save();
        return $this->saveSuccess($request, 'Jadwal berhasil diperbarui.', 'app.landing.ppdb.schedules');
    }

    public function ppdbScheduleDestroy(Request $request, $item)
    {
        $model = LpPpdbSchedule::findOrFail($item);
        $model->delete();
        return $this->deleteSuccess($request, 'Jadwal berhasil dihapus.', 'app.landing.ppdb.schedules');
    }

    public function ppdbFaqs(Request $request)
    {
        return view('landing-admin.ppdb.faqs.index', [
            'title' => 'PPDB — FAQ',
            'items' => LpPpdbFaq::ordered()->paginate(20)->withQueryString(),
        ]);
    }

    public function ppdbFaqStore(Request $request)
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:300'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['sort_order'] = $data['sort_order'] ?? (LpPpdbFaq::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        LpPpdbFaq::create($data);
        return $this->saveSuccess($request, 'FAQ berhasil ditambahkan.', 'app.landing.ppdb.faqs');
    }

    public function ppdbFaqUpdate(Request $request, $item)
    {
        $model = LpPpdbFaq::findOrFail($item);
        $data = $request->validate([
            'question' => ['required', 'string', 'max:300'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $model->fill($data)->save();
        return $this->saveSuccess($request, 'FAQ berhasil diperbarui.', 'app.landing.ppdb.faqs');
    }

    public function ppdbFaqDestroy(Request $request, $item)
    {
        $model = LpPpdbFaq::findOrFail($item);
        $model->delete();
        return $this->deleteSuccess($request, 'FAQ berhasil dihapus.', 'app.landing.ppdb.faqs');
    }

    /**
     * Decode string items newline-separated (atau JSON) menjadi array.
     */
    private function decodeItemsString(?string $raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return json_encode(array_values(array_filter(array_map('trim', $decoded), fn ($v) => $v !== '')));
        }
        $lines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw) ?: []), fn ($v) => $v !== ''));
        return json_encode($lines);
    }

    private function uniqueSlug(string $title, string $modelClass, ?int $ignoreId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        $query = $modelClass::query()->where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $base . '-' . $i++;
            $query = $modelClass::query()->where('slug', $slug);
            if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    /**
     * Disk 'public' sudah di-root ke storage/app/public/tenant/{id} oleh
     * TenantStorageServiceProvider, jadi path di sini relatif terhadap root
     * tersebut (jangan diprefix ulang dengan tenant/{id}).
     */
    private function uploadDir(): string
    {
        return 'landing';
    }

    private function diskPath(string $filename): string
    {
        return $this->uploadDir() . '/' . $filename;
    }

    /**
     * Resolusi pilihan warna tema dari form:
     * - 'custom' + nilai hex valid -> simpan hex
     * - 'custom' tanpa hex -> pertahankan nilai lama (jangan ditimpa jadi null)
     * - key preset valid -> simpan key
     * - null/kosong -> pertahankan nilai lama
     */
    private function resolveThemeColor(?string $choice, ?string $customHex, array $validKeys): ?string
    {
        if ($choice === 'custom') {
            if ($customHex && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $customHex)) {
                return strtoupper($customHex);
            }
            return null;
        }

        if ($choice && array_key_exists($choice, $validKeys)) {
            return $choice;
        }

        return null;
    }

    /**
     * Resize gambar dengan strategi COVER FIT (crop tengah, pertahankan rasio).
     * Cocok untuk Hero background yang selalu ditampilkan full-width.
     *
     * - Output: JPEG kualitas 85 pada dimensi target.
     * - Aspect ratio target = targetWidth / targetHeight.
     * - Source di-crop center untuk memenuhi area target.
     * - EXIF orientation dihormati agar foto HP tidak miring.
     * - Return: absolute path file hasil resize (belum disimpan).
     */
    private function resizeToCover(string $sourcePath, int $targetWidth, int $targetHeight): ?string
    {
        if (!extension_loaded('gd')) {
            return null;
        }

        [$width, $height, $type] = getimagesize($sourcePath) ?: [0, 0, IMAGETYPE_JPEG];

        $src = match ($type) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG  => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : false,
            default        => false,
        };
        if (!$src) {
            return null;
        }

        // EXIF orientation fix (foto dari HP)
        if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
            $exif = @exif_read_data($sourcePath);
            $orientation = (int) ($exif['Orientation'] ?? 1);
            if (in_array($orientation, [3, 6, 8], true)) {
                $degrees = [3 => 180, 6 => 270, 8 => 90][$orientation];
                $rotated = imagerotate($src, $degrees, 0);
                if ($rotated) {
                    imagedestroy($src);
                    $src = $rotated;
                    $width = imagesx($src);
                    $height = imagesy($src);
                }
            }
        }

        // Hitung crop area (cover fit, center)
        $srcRatio = $width / $height;
        $dstRatio = $targetWidth / $targetHeight;
        if ($srcRatio > $dstRatio) {
            // source lebih lebar: crop kiri-kanan
            $cropH = $height;
            $cropW = (int) round($height * $dstRatio);
            $cropX = (int) (($width - $cropW) / 2);
            $cropY = 0;
        } else {
            // source lebih tinggi: crop atas-bawah
            $cropW = $width;
            $cropH = (int) round($width / $dstRatio);
            $cropX = 0;
            $cropY = (int) (($height - $cropH) / 2);
        }

        $dst = imagecreatetruecolor($targetWidth, $targetHeight);
        // Latar hitam untuk area kosong (safety, walau cover fit tidak menyisakan kosong)
        imagefill($dst, 0, 0, imagecolorallocate($dst, 0, 0, 0));

        imagecopyresampled(
            $dst, $src,
            0, 0,
            $cropX, $cropY,
            $targetWidth, $targetHeight,
            $cropW, $cropH
        );

        $tmpPath = tempnam(sys_get_temp_dir(), 'lp_cover_') . '.jpg';
        imagejpeg($dst, $tmpPath, 85);

        imagedestroy($src);
        imagedestroy($dst);

        return is_file($tmpPath) ? $tmpPath : null;
    }
}
