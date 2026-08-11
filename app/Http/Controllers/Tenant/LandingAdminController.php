<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Landing\LpAnnouncement;
use App\Models\Landing\LpContactMessage;
use App\Models\Landing\LpGallery;
use App\Models\Landing\LpHeroSlide;
use App\Models\Landing\LpPost;
use App\Models\Landing\LpSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * Pengelolaan konten landing page dari domain admin (/app/landing/*).
 *
 * Akses dibatasi middleware hak.akses:landing sehingga hanya user dengan
 * hak akses menu "Landing Page" (atau superadmin '*') yang dapat membuka.
 */
class LandingAdminController extends Controller
{
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
                'slides' => LpHeroSlide::count(),
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
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:png,ico,jpg,jpeg', 'max:512'],
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

        $setting->fill($data)->save();

        return redirect()
            ->route('app.landing.pengaturan')
            ->with('success', 'Pengaturan landing page berhasil disimpan.');
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

        return redirect()->route('app.landing.hero')->with('success', 'Slide berhasil ditambahkan.');
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

        return redirect()->route('app.landing.hero')->with('success', 'Slide berhasil diperbarui.');
    }

    public function heroDestroy($slide)
    {
        $model = LpHeroSlide::findOrFail($slide);

        if ($model->image) {
            Storage::disk('public')->delete($this->diskPath($model->image));
        }

        $model->delete();

        return redirect()->route('app.landing.hero')->with('success', 'Slide berhasil dihapus.');
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
}
