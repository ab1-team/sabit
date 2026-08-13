<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Landing\PengumumanLanding;
use App\Models\Landing\PesanKontakLanding;
use App\Models\Landing\GaleriLanding;
use App\Models\Landing\ArtikelLanding;
use App\Models\Landing\FaqPpdb;
use App\Models\Landing\PersyaratanPpdb;
use App\Models\Landing\JadwalPpdb;
use App\Models\Landing\PengaturanPpdb;
use App\Models\Landing\TahapanPpdb;
use App\Models\Landing\BagianProfilLanding;
use App\Models\Landing\PengaturanLanding;
use App\Models\Landing\StrukturOrganisasiLanding;
use App\Models\Landing\FasilitasLanding;
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
        if ($this->wantsJsonResponse($request) || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            return response()->json(array_merge([
                'success' => true,
                'msg' => $msg,
                'redirect' => $redirectRoute ? route($redirectRoute) : null,
            ], $extra));
        }
        if ($redirectRoute) {
            return redirect()->route($redirectRoute)->with('success', $msg);
        }
        return back()->with('success', $msg);
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
class AdminLandingController extends Controller
{
    use LandingAdminResponse;

    public function index()
    {
        $tenant = tenant();

        return view('admin-landing.indeks', [
            'title' => 'Landing Page',
            'setting' => PengaturanLanding::current(),
            'landingUrl' => $tenant?->landingUrl(),
            'stats' => [
                'posts' => ArtikelLanding::count(),
                'galleries' => GaleriLanding::count(),
                'announcements' => PengumumanLanding::count(),
                'unread_messages' => PesanKontakLanding::unread()->count(),
            ],
        ]);
    }

    public function pengaturan()
    {
        $tenant = tenant();

        return view('admin-landing.pengaturan', [
            'title' => 'Pengaturan Landing Page',
            'setting' => PengaturanLanding::current(),
            'landingUrl' => $tenant?->landingUrl(),
        ]);
    }

    /**
     * Whitelist field per-section. Dipakai agar submit parsial (per-card)
     * hanya memproses field milik section yang dikirim — section lain
     * di DB tidak boleh ter-overwrite.
     *
     * Setiap section menentukan:
     *  - rules   : aturan validasi Laravel untuk field section tsb.
     *  - fields  : kolom DB yang akan di-save (sisanya diabaikan).
     *  - messages: pesan error kustom (opsional).
     *  - label   : label section untuk pesan sukses.
     */
    private function pengaturanSections(): array
    {
        return [
            'identitas' => [
                'label' => 'Identitas Sekolah',
                'fields' => ['school_name', 'tagline', 'logo', 'favicon'],
                'rules' => [
                    'school_name' => ['required', 'string', 'max:150'],
                    'tagline' => ['nullable', 'string', 'max:255'],
                    'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
                    'favicon' => ['nullable', 'image', 'mimes:png,ico,jpg,jpeg', 'max:512'],
                ],
                'messages' => [],
            ],
            'kontak' => [
                'label' => 'Kontak',
                'fields' => ['email', 'phone', 'whatsapp', 'address', 'google_maps_url'],
                'rules' => [
                    'email' => ['nullable', 'email', 'max:150'],
                    'phone' => ['nullable', 'string', 'max:30'],
                    'whatsapp' => ['nullable', 'string', 'max:30'],
                    'address' => ['nullable', 'string'],
                    'google_maps_url' => ['nullable', 'string'],
                ],
                'messages' => [],
            ],
            'medsos' => [
                'label' => 'Media Sosial',
                'fields' => ['facebook', 'instagram', 'youtube', 'tiktok'],
                'rules' => [
                    'facebook' => ['nullable', 'url', 'max:255'],
                    'instagram' => ['nullable', 'url', 'max:255'],
                    'youtube' => ['nullable', 'url', 'max:255'],
                    'tiktok' => ['nullable', 'url', 'max:255'],
                ],
                'messages' => [],
            ],
            'background' => [
                'label' => 'Background Tema',
                'fields' => ['hero_background'],
                'rules' => [
                    'hero_background_choice' => ['nullable', 'string', 'max:255'],
                    'hero_background_custom' => [
                        'nullable',
                        'image',
                        'mimes:jpg,jpeg,png,webp',
                        'max:10240',
                    ],
                ],
                'messages' => [
                    'hero_background_custom.image' => 'File background harus berupa gambar.',
                    'hero_background_custom.mimes' => 'Format background harus JPG, JPEG, PNG, atau WEBP.',
                    'hero_background_custom.max' => 'Ukuran background maksimal 10 MB (akan otomatis dikecilkan ke 1920×1080).',
                ],
            ],
            'warna' => [
                'label' => 'Warna Tombol & Text',
                'fields' => ['theme_button_color', 'theme_text_color'],
                'rules' => [
                    'theme_button_color_choice' => ['nullable', 'string', 'max:255'],
                    'theme_button_color_custom' => ['nullable', 'string', 'max:20', 'regex:/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/'],
                ],
                'messages' => [],
            ],
            'seo' => [
                'label' => 'SEO',
                'fields' => ['meta_description', 'meta_keywords'],
                'rules' => [
                    'meta_description' => ['nullable', 'string', 'max:255'],
                    'meta_keywords' => ['nullable', 'string', 'max:255'],
                ],
                'messages' => [],
            ],
            'badge' => [
                'label' => 'Badge Hero',
                'fields' => ['hero_badges'],
                'rules' => [
                    'badges' => ['nullable', 'array', 'max:6'],
                    'badges.*.icon' => ['nullable', 'string', 'max:80'],
                    'badges.*.text' => ['nullable', 'string', 'max:120'],
                ],
                'messages' => [
                    'badges.array' => 'Format badge tidak valid.',
                    'badges.max' => 'Maksimal 6 badge.',
                ],
            ],
            'sambutan' => [
                'label' => 'Sambutan Kepala Sekolah',
                'fields' => ['welcome'],
                'rules' => [
                    'welcome_photo_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
                    'welcome_quote' => ['nullable', 'string', 'max:255'],
                    'welcome_paragraph_1' => ['nullable', 'string'],
                    'welcome_paragraph_2' => ['nullable', 'string'],
                    'welcome_head_name' => ['nullable', 'string', 'max:150'],
                    'welcome_head_role' => ['nullable', 'string', 'max:200'],
                ],
                'messages' => [
                    'welcome_photo_upload.image' => 'File foto harus berupa gambar.',
                    'welcome_photo_upload.mimes' => 'Format foto harus JPG, JPEG, PNG, atau WEBP.',
                    'welcome_photo_upload.max' => 'Ukuran foto maksimal 4 MB.',
                ],
            ],
            'statistik' => [
                'label' => 'Statistik',
                'fields' => ['stats'],
                'rules' => [
                    'stats_icon_1' => ['nullable', 'string', 'max:80'],
                    'stats_color_1' => ['nullable', 'string', 'max:20'],
                    'stats_value_1' => ['nullable', 'string', 'max:30'],
                    'stats_label_1' => ['nullable', 'string', 'max:80'],
                    'stats_icon_2' => ['nullable', 'string', 'max:80'],
                    'stats_color_2' => ['nullable', 'string', 'max:20'],
                    'stats_value_2' => ['nullable', 'string', 'max:30'],
                    'stats_label_2' => ['nullable', 'string', 'max:80'],
                    'stats_icon_3' => ['nullable', 'string', 'max:80'],
                    'stats_color_3' => ['nullable', 'string', 'max:20'],
                    'stats_value_3' => ['nullable', 'string', 'max:30'],
                    'stats_label_3' => ['nullable', 'string', 'max:80'],
                ],
                'messages' => [],
            ],
            'jenjang' => [
                'label' => 'Jenjang Pendidikan',
                'fields' => ['jenjang'],
                'rules' => [
                    'jenjang_age_1' => ['nullable', 'string', 'max:60'],
                    'jenjang_title_1' => ['nullable', 'string', 'max:80'],
                    'jenjang_icon_1' => ['nullable', 'string', 'max:80'],
                    'jenjang_desc_1' => ['nullable', 'string'],
                    'jenjang_age_2' => ['nullable', 'string', 'max:60'],
                    'jenjang_title_2' => ['nullable', 'string', 'max:80'],
                    'jenjang_icon_2' => ['nullable', 'string', 'max:80'],
                    'jenjang_desc_2' => ['nullable', 'string'],
                    'jenjang_age_3' => ['nullable', 'string', 'max:60'],
                    'jenjang_title_3' => ['nullable', 'string', 'max:80'],
                    'jenjang_icon_3' => ['nullable', 'string', 'max:80'],
                    'jenjang_desc_3' => ['nullable', 'string'],
                    'jenjang_age_4' => ['nullable', 'string', 'max:60'],
                    'jenjang_title_4' => ['nullable', 'string', 'max:80'],
                    'jenjang_icon_4' => ['nullable', 'string', 'max:80'],
                    'jenjang_desc_4' => ['nullable', 'string'],
                ],
                'messages' => [],
            ],
            'keunggulan' => [
                'label' => 'Keunggulan Sekolah',
                'fields' => ['keunggulan'],
                'rules' => [
                    'keunggulan_color_1' => ['nullable', 'string', 'max:20'],
                    'keunggulan_icon_1' => ['nullable', 'string', 'max:80'],
                    'keunggulan_title_1' => ['nullable', 'string', 'max:120'],
                    'keunggulan_desc_1' => ['nullable', 'string'],
                    'keunggulan_color_2' => ['nullable', 'string', 'max:20'],
                    'keunggulan_icon_2' => ['nullable', 'string', 'max:80'],
                    'keunggulan_title_2' => ['nullable', 'string', 'max:120'],
                    'keunggulan_desc_2' => ['nullable', 'string'],
                    'keunggulan_color_3' => ['nullable', 'string', 'max:20'],
                    'keunggulan_icon_3' => ['nullable', 'string', 'max:80'],
                    'keunggulan_title_3' => ['nullable', 'string', 'max:120'],
                    'keunggulan_desc_3' => ['nullable', 'string'],
                    'keunggulan_color_4' => ['nullable', 'string', 'max:20'],
                    'keunggulan_icon_4' => ['nullable', 'string', 'max:80'],
                    'keunggulan_title_4' => ['nullable', 'string', 'max:120'],
                    'keunggulan_desc_4' => ['nullable', 'string'],
                    'keunggulan_color_5' => ['nullable', 'string', 'max:20'],
                    'keunggulan_icon_5' => ['nullable', 'string', 'max:80'],
                    'keunggulan_title_5' => ['nullable', 'string', 'max:120'],
                    'keunggulan_desc_5' => ['nullable', 'string'],
                    'keunggulan_color_6' => ['nullable', 'string', 'max:20'],
                    'keunggulan_icon_6' => ['nullable', 'string', 'max:80'],
                    'keunggulan_title_6' => ['nullable', 'string', 'max:120'],
                    'keunggulan_desc_6' => ['nullable', 'string'],
                ],
                'messages' => [],
            ],
        ];
    }

    /**
     * Bangun payload untuk section 'sambutan' (welcome JSON).
     * Field di-prefix 'welcome_' lalu di-strip, digabung dengan foto existing
     * bila admin tidak upload baru. Placeholder {{school}} tidak diubah di sini.
     */
    private function buildWelcomePayload(Request $request, PengaturanLanding $setting, array $validated): array
    {
        $current = $setting->welcome ?: [];
        $photo = $current['photo'] ?? null;

        if ($request->hasFile('welcome_photo_upload')) {
            if ($photo && str_starts_with((string) $photo, 'uploaded:')) {
                $old = substr($photo, strlen('uploaded:'));
                if ($old !== '') {
                    Storage::disk('public')->delete($this->diskPath($old));
                }
            }
            $photo = 'uploaded:' . basename($request->file('welcome_photo_upload')->store($this->uploadDir(), 'public'));
        } elseif ($request->boolean('welcome_photo_clear')) {
            if ($photo && str_starts_with((string) $photo, 'uploaded:')) {
                $old = substr($photo, strlen('uploaded:'));
                if ($old !== '') {
                    Storage::disk('public')->delete($this->diskPath($old));
                }
            }
            $photo = null;
        }

        $strip = static function (string $key) use ($validated): ?string {
            $full = 'welcome_' . $key;
            $v = $validated[$full] ?? null;
            return $v !== null ? trim((string) $v) : null;
        };

        return [
            'photo' => $photo,
            'quote' => $strip('quote') ?: ($current['quote'] ?? null),
            'paragraph_1' => $strip('paragraph_1') ?: ($current['paragraph_1'] ?? null),
            'paragraph_2' => $strip('paragraph_2') ?: ($current['paragraph_2'] ?? null),
            'head_name' => $strip('head_name') ?: ($current['head_name'] ?? null),
            'head_role' => $strip('head_role') ?: ($current['head_role'] ?? null),
        ];
    }

    /**
     * Bangun payload untuk section 'statistik' (stats JSON).
     * Mengambil field stats_*_{1..3} dan menyusun menjadi array of 3 items.
     * Field kosong = pakai nilai existing (fallback ke default model).
     */
    private function buildStatsPayload(Request $request, PengaturanLanding $setting, array $validated): array
    {
        $current = $setting->stats ?: [];
        $allowedColors = ['blue', 'green', 'amber', 'pink', 'purple', 'cyan'];

        $items = [];
        for ($i = 1; $i <= 3; $i++) {
            $cur = $current[$i - 1] ?? [];
            $color = $validated["stats_color_{$i}"] ?? ($cur['color'] ?? 'blue');
            if (!in_array($color, $allowedColors, true)) {
                $color = 'blue';
            }
            $items[] = [
                'icon' => trim((string) ($validated["stats_icon_{$i}"] ?? ($cur['icon'] ?? 'bi-people-fill'))),
                'color' => $color,
                'value' => trim((string) ($validated["stats_value_{$i}"] ?? ($cur['value'] ?? ''))),
                'label' => trim((string) ($validated["stats_label_{$i}"] ?? ($cur['label'] ?? ''))),
            ];
        }

        return $items;
    }

    /**
     * Bangun payload untuk section 'jenjang' (jenjang JSON).
     * Mengambil field jenjang_*_{1..4}.
     */
    private function buildJenjangPayload(Request $request, PengaturanLanding $setting, array $validated): array
    {
        $current = $setting->jenjang ?: [];
        $items = [];
        for ($i = 1; $i <= 4; $i++) {
            $cur = $current[$i - 1] ?? [];
            $items[] = [
                'age' => trim((string) ($validated["jenjang_age_{$i}"] ?? ($cur['age'] ?? ''))),
                'title' => trim((string) ($validated["jenjang_title_{$i}"] ?? ($cur['title'] ?? ''))),
                'icon' => trim((string) ($validated["jenjang_icon_{$i}"] ?? ($cur['icon'] ?? 'bi-mortarboard-fill'))),
                'desc' => trim((string) ($validated["jenjang_desc_{$i}"] ?? ($cur['desc'] ?? ''))),
            ];
        }

        return $items;
    }

    /**
     * Bangun payload untuk section 'keunggulan' (keunggulan JSON).
     * Mengambil field keunggulan_*_{1..6}.
     */
    private function buildKeunggulanPayload(Request $request, PengaturanLanding $setting, array $validated): array
    {
        $current = $setting->keunggulan ?: [];
        $allowedColors = ['blue', 'green', 'amber', 'pink', 'purple', 'cyan'];
        $items = [];
        for ($i = 1; $i <= 6; $i++) {
            $cur = $current[$i - 1] ?? [];
            $color = $validated["keunggulan_color_{$i}"] ?? ($cur['color'] ?? 'blue');
            if (!in_array($color, $allowedColors, true)) {
                $color = 'blue';
            }
            $items[] = [
                'color' => $color,
                'icon' => trim((string) ($validated["keunggulan_icon_{$i}"] ?? ($cur['icon'] ?? 'bi-book-fill'))),
                'title' => trim((string) ($validated["keunggulan_title_{$i}"] ?? ($cur['title'] ?? ''))),
                'desc' => trim((string) ($validated["keunggulan_desc_{$i}"] ?? ($cur['desc'] ?? ''))),
            ];
        }

        return $items;
    }

    /**
     * Bangun payload section 'badge' (hero_badges JSON).
     * Admin isi field `badges` sebagai array of {icon, text}.
     * Item kosong (icon & text kosong) di-skip agar tidak menambah junk.
     */
    private function buildHeroBadgesPayload(Request $request, PengaturanLanding $setting): array
    {
        $badges = $request->input('badges', []);
        if (!is_array($badges)) {
            $badges = [];
        }

        $items = [];
        foreach ($badges as $row) {
            $icon = trim((string) ($row['icon'] ?? ''));
            $text = trim((string) ($row['text'] ?? ''));
            if ($icon === '' && $text === '') {
                continue;
            }
            $items[] = [
                'icon' => $icon !== '' ? $icon : 'bi-patch-check-fill',
                'text' => $text,
            ];
        }

        return $items;
    }

    public function pengaturanStore(Request $request)
    {
        $sectionKey = (string) $request->input('section', '');
        $sections = $this->pengaturanSections();

        if (!array_key_exists($sectionKey, $sections)) {
            $msg = 'Section pengaturan tidak dikenali.';
            if ($this->wantsJsonResponse($request)) {
                return response()->json(['success' => false, 'msg' => $msg], 422);
            }
            return redirect()->route('app.admin-landing.pengaturan')->with('error', $msg);
        }

        $section = $sections[$sectionKey];

        $data = $request->validate($section['rules'], $section['messages']);

        $setting = PengaturanLanding::query()->first() ?? new PengaturanLanding();

        // Proses upload logo/favicon HANYA untuk section identitas.
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

        // Background tema — HANYA diproses di section 'background'.
        if ($sectionKey === 'background') {
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
                // Pilih custom tanpa upload baru -> pertahankan nilai lama.
                $data['hero_background'] = $setting->hero_background;
            } elseif ($bgChoice) {
                $validKeys = array_column(PengaturanLanding::themeBackgroundDefaults(), 'key');
                $data['hero_background'] = in_array($bgChoice, $validKeys, true) ? $bgChoice : 'default-1';
            } else {
                // Tidak ada pilihan sama sekali -> jangan sentuh kolom ini.
                unset($data['hero_background']);
            }
        }

        unset($data['hero_background_choice'], $data['hero_background_custom']);

        // Warna tombol & text — HANYA diproses di section 'warna'.
        if ($sectionKey === 'warna') {
            $resolvedButton = $this->resolveThemeColor(
                $request->input('theme_button_color_choice'),
                $request->input('theme_button_color_custom'),
                array_column(PengaturanLanding::themeButtonColorDefaults(), 'key', 'key'),
            );
            if ($resolvedButton !== null) {
                $data['theme_button_color'] = $resolvedButton;
                $data['theme_text_color'] = $resolvedButton;
            }
        }

        unset($data['theme_button_color_choice'], $data['theme_button_color_custom']);

        // Section JSON: bangun payload dari field-field prefixed.
        if ($sectionKey === 'sambutan') {
            $data['welcome'] = $this->buildWelcomePayload($request, $setting, $data);
            foreach (['welcome_photo_upload', 'welcome_photo_clear', 'welcome_quote', 'welcome_paragraph_1', 'welcome_paragraph_2', 'welcome_head_name', 'welcome_head_role'] as $k) {
                unset($data[$k]);
            }
        }
        if ($sectionKey === 'statistik') {
            $data['stats'] = $this->buildStatsPayload($request, $setting, $data);
            foreach (array_keys($data) as $k) {
                if (str_starts_with($k, 'stats_')) {
                    unset($data[$k]);
                }
            }
        }
        if ($sectionKey === 'jenjang') {
            $data['jenjang'] = $this->buildJenjangPayload($request, $setting, $data);
            foreach (array_keys($data) as $k) {
                if (str_starts_with($k, 'jenjang_')) {
                    unset($data[$k]);
                }
            }
        }
        if ($sectionKey === 'keunggulan') {
            $data['keunggulan'] = $this->buildKeunggulanPayload($request, $setting, $data);
            foreach (array_keys($data) as $k) {
                if (str_starts_with($k, 'keunggulan_')) {
                    unset($data[$k]);
                }
            }
        }
        if ($sectionKey === 'badge') {
            $data['hero_badges'] = $this->buildHeroBadgesPayload($request, $setting);
            unset($data['badges']);
        }

        // Filter $data HANYA ke kolom yang di-whitelist untuk section ini.
        // Ini mencegah field dari section lain (yang ikut terkirim via form
        // lain di halaman yang sama) men-overwrite data DB.
        $allowedFields = $section['fields'];
        $payload = array_intersect_key($data, array_flip($allowedFields));

        $setting->fill($payload)->save();

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

        $sectionLabel = $section['label'];
        $msg = $sectionLabel . ' berhasil disimpan.';

        if ($this->wantsJsonResponse($request)) {
            return response()->json([
                'success' => true,
                'msg' => $msg,
                'section' => $sectionKey,
                'saved_fields' => array_keys($payload),
                'hero_background_url' => $setting->heroBackgroundUrl(),
                'hero_background_key' => $setting->activeThemeBackgroundKey(),
                'hero_background_meta' => $newFileMeta,
                'landing_url' => tenant()?->landingUrl(),
                'redirect' => route('app.admin-landing.pengaturan'),
            ]);
        }

        return redirect()
            ->route('app.admin-landing.pengaturan')
            ->with('success', $msg);
    }

    /**
     * Hapus custom background yang tersimpan dan kembalikan ke default-1.
     * Dipakai admin saat ingin membuang foto upload sendiri tanpa upload baru.
     */
    public function hapusCustomBackground(Request $request)
    {
        $setting = PengaturanLanding::query()->first();
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
                'redirect' => route('app.admin-landing.pengaturan'),
            ]);
        }

        return redirect()
            ->route('app.admin-landing.pengaturan')
            ->with($cleared ? 'success' : 'info', $cleared
                ? 'Background custom berhasil dihapus, dikembalikan ke Standar 1.'
                : 'Tidak ada background custom yang tersimpan.');
    }

    public function posts(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');
        $category = trim((string) $request->query('category', ''));

        $posts = ArtikelLanding::query()
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->where('title', 'like', "%{$q}%")
                        ->orWhere('category', 'like', "%{$q}%")
                        ->orWhere('tags', 'like', "%{$q}%")
                        ->orWhere('excerpt', 'like', "%{$q}%");
                });
            })
            ->when($status === 'published', fn ($w) => $w->where('is_published', true))
            ->when($status === 'draft', fn ($w) => $w->where('is_published', false))
            ->when($category !== '', fn ($w) => $w->where('category', $category))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $categories = ArtikelLanding::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin-landing.artikel.indeks', [
            'title' => 'Program / Berita',
            'posts' => $posts,
            'q' => $q,
            'status' => $status,
            'category' => $category,
            'categories' => $categories,
        ]);
    }

    public function postCreate()
    {
        return view('admin-landing.artikel.formulir', [
            'title' => 'Tambah Program / Berita',
            'post' => new ArtikelLanding(),
            'action' => route('app.admin-landing.posts.store'),
        ]);
    }

    public function postStore(Request $request)
    {
        $data = $this->validatePost($request);
        $data['slug'] = $this->uniqueSlug($data['title'], ArtikelLanding::class);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['published_at'] ?? now();

        if ($request->hasFile('image')) {
            $data['image'] = basename($request->file('image')->store($this->uploadDir(), 'public'));
        }

        ArtikelLanding::create($data);

        return $this->saveSuccess($request, 'Program / berita berhasil ditambahkan.', 'app.admin-landing.posts');
    }

    public function postEdit($post)
    {
        $model = ArtikelLanding::findOrFail($post);

        return view('admin-landing.artikel.formulir', [
            'title' => 'Edit Program / Berita',
            'post' => $model,
            'action' => route('app.admin-landing.posts.update', $model->id),
        ]);
    }

    public function postUpdate(Request $request, $post)
    {
        $model = ArtikelLanding::findOrFail($post);
        $data = $this->validatePost($request);

        if (Str::lower($data['title']) !== Str::lower($model->title)) {
            $data['slug'] = $this->uniqueSlug($data['title'], ArtikelLanding::class, $model->id);
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

        return $this->saveSuccess($request, 'Program / berita berhasil diperbarui.', 'app.admin-landing.posts');
    }

    public function postDestroy(Request $request, $post)
    {
        $model = ArtikelLanding::findOrFail($post);
        if ($model->image) {
            Storage::disk('public')->delete($this->diskPath($model->image));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Program / berita berhasil dihapus.', 'app.admin-landing.posts');
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
        return view('admin-landing.pengumuman.indeks', [
            'title' => 'Pengumuman',
            'announcements' => PengumumanLanding::orderByDesc('published_at')->orderByDesc('id')->paginate(15)->withQueryString(),
        ]);
    }

    public function announcementCreate()
    {
        return view('admin-landing.pengumuman.formulir', [
            'title' => 'Tambah Pengumuman',
            'announcement' => new PengumumanLanding(),
            'action' => route('app.admin-landing.announcements.store'),
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

        PengumumanLanding::create($data);

        return $this->saveSuccess($request, 'Pengumuman berhasil ditambahkan.', 'app.admin-landing.announcements');
    }

    public function announcementEdit($announcement)
    {
        $model = PengumumanLanding::findOrFail($announcement);

        return view('admin-landing.pengumuman.formulir', [
            'title' => 'Edit Pengumuman',
            'announcement' => $model,
            'action' => route('app.admin-landing.announcements.update', $model->id),
        ]);
    }

    public function announcementUpdate(Request $request, $announcement)
    {
        $model = PengumumanLanding::findOrFail($announcement);
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

        return $this->saveSuccess($request, 'Pengumuman berhasil diperbarui.', 'app.admin-landing.announcements');
    }

    public function announcementDestroy(Request $request, $announcement)
    {
        $model = PengumumanLanding::findOrFail($announcement);
        if ($model->file) {
            Storage::disk('public')->delete($this->diskPath($model->file));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Pengumuman berhasil dihapus.', 'app.admin-landing.announcements');
    }

    public function galleries(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status', 'all');

        $galleries = GaleriLanding::query()
            ->when($q !== '', function ($w) use ($q) {
                $w->where(function ($x) use ($q) {
                    $x->where('title', 'like', "%{$q}%")
                        ->orWhere('album', 'like', "%{$q}%")
                        ->orWhere('description', 'like', "%{$q}%");
                });
            })
            ->when($status === 'published', fn ($w) => $w->where('is_published', true))
            ->when($status === 'draft', fn ($w) => $w->where('is_published', false))
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        return view('admin-landing.galeri.indeks', [
            'title' => 'Galeri',
            'galleries' => $galleries,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function galleryCreate()
    {
        return view('admin-landing.galeri.formulir', [
            'title' => 'Tambah Foto Galeri',
            'gallery' => new GaleriLanding(),
            'action' => route('app.admin-landing.galleries.store'),
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
        $data['sort_order'] = $data['sort_order'] ?? (GaleriLanding::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        GaleriLanding::create($data);

        return $this->saveSuccess($request, 'Foto berhasil ditambahkan.', 'app.admin-landing.galleries');
    }

    public function galleryEdit($gallery)
    {
        $model = GaleriLanding::findOrFail($gallery);

        return view('admin-landing.galeri.formulir', [
            'title' => 'Edit Foto Galeri',
            'gallery' => $model,
            'action' => route('app.admin-landing.galleries.update', $model->id),
        ]);
    }

    public function galleryUpdate(Request $request, $gallery)
    {
        $model = GaleriLanding::findOrFail($gallery);
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

        return $this->saveSuccess($request, 'Foto berhasil diperbarui.', 'app.admin-landing.galleries');
    }

    public function galleryDestroy(Request $request, $gallery)
    {
        $model = GaleriLanding::findOrFail($gallery);
        if ($model->image) {
            Storage::disk('public')->delete($this->diskPath($model->image));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Foto berhasil dihapus.', 'app.admin-landing.galleries');
    }

    public function struktur()
    {
        return view('admin-landing.struktur.indeks', [
            'title' => 'Struktur Organisasi',
            'items' => StrukturOrganisasiLanding::orderByDesc('is_lead')->orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString(),
        ]);
    }

    public function strukturCreate()
    {
        return view('admin-landing.struktur.formulir', [
            'title' => 'Tambah Struktur',
            'item' => new StrukturOrganisasiLanding(),
            'action' => route('app.admin-landing.struktur.store'),
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
        $data['sort_order'] = $data['sort_order'] ?? (StrukturOrganisasiLanding::max('sort_order') + 1);

        if ($request->hasFile('photo')) {
            $data['photo'] = basename($request->file('photo')->store($this->uploadDir(), 'public'));
        }

        StrukturOrganisasiLanding::create($data);

        return $this->saveSuccess($request, 'Struktur berhasil ditambahkan.', 'app.admin-landing.struktur');
    }

    public function strukturEdit($item)
    {
        $model = StrukturOrganisasiLanding::findOrFail($item);

        return view('admin-landing.struktur.formulir', [
            'title' => 'Edit Struktur',
            'item' => $model,
            'action' => route('app.admin-landing.struktur.update', $model->id),
        ]);
    }

    public function strukturUpdate(Request $request, $item)
    {
        $model = StrukturOrganisasiLanding::findOrFail($item);
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

        return $this->saveSuccess($request, 'Struktur berhasil diperbarui.', 'app.admin-landing.struktur');
    }

    public function strukturDestroy(Request $request, $item)
    {
        $model = StrukturOrganisasiLanding::findOrFail($item);
        if ($model->photo) {
            Storage::disk('public')->delete($this->diskPath($model->photo));
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Struktur berhasil dihapus.', 'app.admin-landing.struktur');
    }

    public function fasilitas()
    {
        return view('admin-landing.fasilitas.indeks', [
            'title' => 'Fasilitas Sekolah',
            'items' => FasilitasLanding::orderBy('sort_order')->orderBy('id')->paginate(20)->withQueryString(),
        ]);
    }

    public function fasilitasCreate()
    {
        return view('admin-landing.fasilitas.formulir', [
            'title' => 'Tambah Fasilitas',
            'item' => new FasilitasLanding(),
            'action' => route('app.admin-landing.fasilitas.store'),
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
        $data['sort_order'] = $data['sort_order'] ?? (FasilitasLanding::max('sort_order') + 1);

        FasilitasLanding::create($data);

        return $this->saveSuccess($request, 'Fasilitas berhasil ditambahkan.', 'app.admin-landing.fasilitas');
    }

    public function fasilitasEdit($item)
    {
        $model = FasilitasLanding::findOrFail($item);

        return view('admin-landing.fasilitas.formulir', [
            'title' => 'Edit Fasilitas',
            'item' => $model,
            'action' => route('app.admin-landing.fasilitas.update', $model->id),
        ]);
    }

    public function fasilitasUpdate(Request $request, $item)
    {
        $model = FasilitasLanding::findOrFail($item);
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

        return $this->saveSuccess($request, 'Fasilitas berhasil diperbarui.', 'app.admin-landing.fasilitas');
    }

    public function fasilitasDestroy(Request $request, $item)
    {
        $model = FasilitasLanding::findOrFail($item);
        $model->delete();

        return $this->deleteSuccess($request, 'Fasilitas berhasil dihapus.', 'app.admin-landing.fasilitas');
    }

    public function profileSections()
    {
        $this->ensureProfileSectionDefaults();
        $this->ensureFasilitasDefaults();

        return view('admin-landing.bagian-profil.indeks', [
            'title' => 'Section Profil',
            'items' => BagianProfilLanding::orderBy('id')->get(),
            'action' => route('app.admin-landing.profile-sections.updateAll'),
            'fasilitasItems' => FasilitasLanding::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function profileSectionsUpdateAll(Request $request)
    {
        $this->ensureProfileSectionDefaults();

        $items = BagianProfilLanding::orderBy('id')->get();
        $keys = $items->pluck('section_key')->all();
        $rules = [];
        foreach ($keys as $key) {
            $rules["sections.$key.title"]        = ['required', 'string', 'max:200'];
            $rules["sections.$key.subtitle"]     = ['nullable', 'string', 'max:255'];
            $rules["sections.$key.content"]      = ['nullable', 'string'];
            $rules["sections.$key.badge_text"]   = ['nullable', 'string', 'max:100'];
            $rules["sections.$key.badge_icon"]   = ['nullable', 'string', 'max:80'];
            $rules["sections.$key.badge_extra"]  = ['nullable', 'string', 'max:100'];
            $rules["sections.$key.extra_label"]  = ['nullable', 'string', 'max:100'];
            $rules["sections.$key.is_active"]    = ['nullable', 'boolean'];
        }
        $data = $request->validate($rules);

        $payload = $data['sections'] ?? [];
        foreach ($items as $model) {
            $row = $payload[$model->section_key] ?? [];
            $model->title       = $row['title']        ?? $model->title;
            $model->subtitle    = $row['subtitle']     ?? null;
            $model->content     = $row['content']      ?? null;
            $model->badge_text  = $row['badge_text']   ?? null;
            $model->badge_icon  = $row['badge_icon']   ?? null;
            $model->badge_extra = $row['badge_extra']  ?? null;
            $model->extra_label = $row['extra_label']  ?? null;
            $model->is_active   = isset($row['is_active']) && $row['is_active'] ? true : false;
            $model->save();
        }

        return $this->saveSuccess($request, 'Semua section profil berhasil diperbarui.', 'app.admin-landing.profile-sections');
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
                'content' => '<p>Didirikan sejak tahun 1995, sekolah kami telah berkembang menjadi lembaga pendidikan yang dipercaya masyarakat. Perjalanan panjang ini ditandai dengan berbagai inovasi pembelajaran dan pencapaian prestasi di tingkat kota, provinsi, hingga nasional.</p>',
                'badge_text' => null,
                'badge_icon' => null,
                'badge_extra' => null,
                'extra_label' => null,
                'is_active' => true,
            ],
            [
                'section_key' => 'visi_misi',
                'title' => 'Visi & Misi',
                'subtitle' => null,
                'content' => '<h3>Visi Kami</h3><p>Menjadi sekolah unggul dalam prestasi, berakhlak mulia, dan berwawasan global.</p><h3>Misi Kami</h3><ol><li>Menyelenggarakan pembelajaran aktif, inovatif, efektif, dan menyenangkan.</li><li>Menumbuhkan penghayatan nilai keagamaan, budaya, dan karakter.</li><li>Mengembangkan potensi peserta didik secara optimal sesuai bakat dan minat.</li><li>Membangun lingkungan sekolah yang aman, nyaman, dan inklusif.</li></ol>',
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
                'content' => '<p>Status akreditasi <strong>A (Sangat Baik)</strong> diberikan oleh BAN-SM, mencerminkan komitmen kami terhadap mutu pendidikan, manajemen sekolah, dan pencapaian lulusan yang berkualitas.</p>',
                'badge_text' => 'Terakreditasi A',
                'badge_icon' => 'bi-award-fill',
                'badge_extra' => null,
                'extra_label' => null,
                'is_active' => true,
            ],
        ];

        foreach ($defaults as $row) {
            BagianProfilLanding::firstOrCreate(['section_key' => $row['section_key']], $row);
        }
    }

    private function ensureFasilitasDefaults(): void
    {
        // Default fasilitas seragam dengan fallback statis di landing publik
        // (resources/views/halaman-publik/profil-sekolah.blade.php). Saat tabel
        // kosong, sisipkan 2 item pertama supaya admin & publik konsisten.
        if (FasilitasLanding::exists()) {
            return;
        }

        $defaults = [
            [
                'title' => 'Ruang Kelas Modern',
                'description' => 'Ruang belajar nyaman dengan pendingin ruangan, proyektor, dan akses internet untuk mendukung pembelajaran digital.',
                'icon' => 'bi-easel',
                'color_key' => 'blue',
                'sort_order' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Laboratorium & Perpustakaan',
                'description' => 'Laboratorium IPA, komputer, dan perpustakaan digital dengan koleksi buku yang lengkap untuk mendukung eksplorasi siswa.',
                'icon' => 'bi-cpu',
                'color_key' => 'cyan',
                'sort_order' => 2,
                'is_published' => true,
            ],
        ];

        foreach ($defaults as $row) {
            FasilitasLanding::firstOrCreate(
                ['title' => $row['title']],
                $row
            );
        }
    }

    public function profileSectionEdit($item)
    {
        $model = BagianProfilLanding::findOrFail($item);

        return view('admin-landing.bagian-profil.formulir', [
            'title' => 'Edit Section: ' . $model->title,
            'item' => $model,
            'action' => route('app.admin-landing.profile-sections.update', $model->id),
        ]);
    }

    public function profileSectionUpdate(Request $request, $item)
    {
        $model = BagianProfilLanding::findOrFail($item);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'content' => ['nullable', 'string'],
            'badge_text' => ['nullable', 'string', 'max:100'],
            'badge_extra' => ['nullable', 'string', 'max:100'],
            'extra_label' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $model->fill($data)->save();

        return $this->saveSuccess($request, 'Section "' . ($model->title ?: $model->section_key) . '" berhasil disimpan.', null);
    }

    public function profileSectionToggle(Request $request, $item)
    {
        $model = BagianProfilLanding::findOrFail($item);
        $model->is_active = !$model->is_active;
        $model->save();

        return $this->saveSuccess(
            $request,
            $model->is_active ? 'Section diaktifkan.' : 'Section dinonaktifkan.',
            'app.admin-landing.profile-sections'
        );
    }

    public function ppdbCta()
    {
        $setting = PengaturanLanding::current();
        return view('admin-landing.ppdb-cta', [
            'title' => 'CTA PPDB Landing',
            'setting' => $setting,
            'action' => route('app.admin-landing.ppdb-cta.store'),
            'cta' => $setting->ppdbCtaData(),
        ]);
    }

    public function ppdbCtaStore(Request $request)
    {
        $section = $request->input('section');

        // === Section "konten" → simpan ke tabel lp_ppdb_pengaturan ===
        if ($section === 'konten') {
            $data = $request->validate([
                'bottom_eyebrow' => ['nullable', 'string', 'max:100'],
                'bottom_title' => ['required', 'string', 'max:200'],
                'bottom_paragraph' => ['nullable', 'string'],
                'bottom_primary_text' => ['required', 'string', 'max:100'],
                'bottom_primary_url' => ['nullable', 'string', 'max:255'],
                'bottom_secondary_text' => ['nullable', 'string', 'max:100'],
                'bottom_secondary_url' => ['nullable', 'string', 'max:255'],
                'bottom_meta' => ['nullable', 'string', 'max:150'],
            ]);

            // Ambil baris aktif (kalau ada), atau baris pertama,
            // atau buat baru dengan default values untuk field NOT NULL.
            $ppdb = PengaturanPpdb::query()->where('is_active', true)->first()
                ?? PengaturanPpdb::query()->first();

            if ($ppdb) {
                $ppdb->fill($data)->save();
            } else {
                // Baris pertama — butuh title karena NOT NULL tanpa default.
                // Pakai bottom_title sebagai title default kalau field top belum diisi.
                PengaturanPpdb::create(array_merge([
                    'title' => $data['bottom_title'] ?? 'PPDB',
                ], $data));
            }

            PengaturanPpdb::flushCache();

            return $this->saveSuccess($request, 'Konten PPDB berhasil disimpan.', 'app.admin-landing.ppdb-cta');
        }

        // === Default: section "hero" → simpan ke JSON ppdb_cta ===
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'paragraph' => ['nullable', 'string'],
            'registration' => ['nullable', 'string'],
        ]);

        $setting = PengaturanLanding::current();
        $existing = $setting->ppdb_cta ?: [];

        // Pertahankan field lain (button_*, is_active) agar tidak hilang
        // saat admin hanya edit dari halaman ini.
        $payload = array_merge($existing, [
            'title' => $data['title'],
            'paragraph' => $data['paragraph'] ?? '',
            'registration' => $data['registration'] ?? '',
            'is_active' => $request->boolean('is_active', (bool) ($existing['is_active'] ?? true)),
        ]);

        $setting->ppdb_cta = $payload;
        $setting->save();

        return $this->saveSuccess($request, 'CTA PPDB berhasil disimpan.', 'app.admin-landing.ppdb-cta');
    }

    public function ppdbSetting()
    {
        $ppdb = PengaturanPpdb::current();
        return view('admin-landing.ppdb-setting', [
            'title' => 'Pengaturan Halaman PPDB',
            'ppdb' => $ppdb,
            'action' => route('app.admin-landing.ppdb-setting.store'),
        ]);
    }

    public function ppdbSettingStore(Request $request)
    {
        $data = $request->validate([
            'school_name' => ['nullable', 'string', 'max:150'],
            'eyebrow' => ['nullable', 'string', 'max:100'],
            'title' => ['required', 'string', 'max:200'],
            'subtitle' => ['nullable', 'string'],
            'cta_text' => ['nullable', 'string', 'max:100'],
            'cta_url' => ['nullable', 'string', 'max:255'],
            'secondary_text' => ['nullable', 'string', 'max:100'],
            'secondary_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $ppdb = PengaturanPpdb::query()->where('is_active', true)->first()
            ?? PengaturanPpdb::query()->first();

        if ($ppdb) {
            $ppdb->fill($data)->save();
        } else {
            $ppdb = PengaturanPpdb::create($data);
        }

        return $this->saveSuccess($request, 'Pengaturan Halaman PPDB berhasil disimpan.', 'app.admin-landing.ppdb-setting');
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

        $query = PesanKontakLanding::query()->orderByDesc('id');
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

        return view('admin-landing.pesan-kontak.indeks', [
            'title' => 'Pesan Masuk',
            'messages' => $messages,
            'q' => $q,
            'status' => $status,
        ]);
    }

    public function contactMessageMark(Request $request, $message)
    {
        $model = PesanKontakLanding::findOrFail($message);
        $model->is_read = $request->boolean('is_read', true);
        $model->save();

        return $this->saveSuccess(
            $request,
            $model->is_read ? 'Pesan ditandai sudah dibaca.' : 'Pesan ditandai belum dibaca.',
            'app.admin-landing.contact-messages'
        );
    }

    public function contactMessageDestroy(Request $request, $message)
    {
        $model = PesanKontakLanding::findOrFail($message);
        $model->delete();

        return $this->deleteSuccess($request, 'Pesan berhasil dihapus.', 'app.admin-landing.contact-messages');
    }

    // -----------------------------------------------------------------
    // Sub-CRUD PPDB (requirements, stages, schedules, faqs)
    // -----------------------------------------------------------------

    public function ppdbRequirements(Request $request)
    {
        return view('admin-landing.ppdb.persyaratan.indeks', [
            'title' => 'PPDB — Persyaratan',
            'items' => PersyaratanPpdb::ordered()->paginate(20)->withQueryString(),
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
        $data['sort_order'] = $data['sort_order'] ?? (PersyaratanPpdb::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        PersyaratanPpdb::create($data);
        return $this->saveSuccess($request, 'Persyaratan berhasil ditambahkan.', 'app.admin-landing.ppdb.requirements');
    }

    public function ppdbRequirementUpdate(Request $request, $item)
    {
        $model = PersyaratanPpdb::findOrFail($item);
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
        return $this->saveSuccess($request, 'Persyaratan berhasil diperbarui.', 'app.admin-landing.ppdb.requirements');
    }

    public function ppdbRequirementDestroy(Request $request, $item)
    {
        $model = PersyaratanPpdb::findOrFail($item);
        $model->delete();
        return $this->deleteSuccess($request, 'Persyaratan berhasil dihapus.', 'app.admin-landing.ppdb.requirements');
    }

    public function ppdbStages(Request $request)
    {
        return view('admin-landing.ppdb.tahapan.indeks', [
            'title' => 'PPDB — Alur / Tahapan',
            'items' => TahapanPpdb::ordered()->paginate(20)->withQueryString(),
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
        $data['sort_order'] = $data['sort_order'] ?? (TahapanPpdb::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        TahapanPpdb::create($data);
        return $this->saveSuccess($request, 'Tahapan berhasil ditambahkan.', 'app.admin-landing.ppdb.stages');
    }

    public function ppdbStageUpdate(Request $request, $item)
    {
        $model = TahapanPpdb::findOrFail($item);
        $data = $request->validate([
            'step_label' => ['required', 'string', 'max:30'],
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $model->fill($data)->save();
        return $this->saveSuccess($request, 'Tahapan berhasil diperbarui.', 'app.admin-landing.ppdb.stages');
    }

    public function ppdbStageDestroy(Request $request, $item)
    {
        $model = TahapanPpdb::findOrFail($item);
        $model->delete();
        return $this->deleteSuccess($request, 'Tahapan berhasil dihapus.', 'app.admin-landing.ppdb.stages');
    }

    public function ppdbSchedules(Request $request)
    {
        return view('admin-landing.ppdb.jadwal.indeks', [
            'title' => 'PPDB — Jadwal / Gelombang',
            'items' => JadwalPpdb::ordered()->paginate(20)->withQueryString(),
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
        $data['sort_order'] = $data['sort_order'] ?? (JadwalPpdb::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        JadwalPpdb::create($data);
        return $this->saveSuccess($request, 'Jadwal berhasil ditambahkan.', 'app.admin-landing.ppdb.schedules');
    }

    public function ppdbScheduleUpdate(Request $request, $item)
    {
        $model = JadwalPpdb::findOrFail($item);
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
        return $this->saveSuccess($request, 'Jadwal berhasil diperbarui.', 'app.admin-landing.ppdb.schedules');
    }

    public function ppdbScheduleDestroy(Request $request, $item)
    {
        $model = JadwalPpdb::findOrFail($item);
        $model->delete();
        return $this->deleteSuccess($request, 'Jadwal berhasil dihapus.', 'app.admin-landing.ppdb.schedules');
    }

    public function ppdbFaqs(Request $request)
    {
        return view('admin-landing.ppdb.faq.indeks', [
            'title' => 'PPDB — FAQ',
            'items' => FaqPpdb::ordered()->paginate(20)->withQueryString(),
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
        $data['sort_order'] = $data['sort_order'] ?? (FaqPpdb::max('sort_order') + 1);
        $data['is_published'] = $request->boolean('is_published');

        FaqPpdb::create($data);
        return $this->saveSuccess($request, 'FAQ berhasil ditambahkan.', 'app.admin-landing.ppdb.faqs');
    }

    public function ppdbFaqUpdate(Request $request, $item)
    {
        $model = FaqPpdb::findOrFail($item);
        $data = $request->validate([
            'question' => ['required', 'string', 'max:300'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);
        $data['is_published'] = $request->boolean('is_published');
        $model->fill($data)->save();
        return $this->saveSuccess($request, 'FAQ berhasil diperbarui.', 'app.admin-landing.ppdb.faqs');
    }

    public function ppdbFaqDestroy(Request $request, $item)
    {
        $model = FaqPpdb::findOrFail($item);
        $model->delete();
        return $this->deleteSuccess($request, 'FAQ berhasil dihapus.', 'app.admin-landing.ppdb.faqs');
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
