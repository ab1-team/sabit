<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
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
use App\Models\Landing\VideoLanding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

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

    /**
     * Kembalikan response error validasi (422) untuk AJAX, atau
     * redirect back dengan error bag untuk form biasa. Dipakai oleh
     * method store/update agar pesan error konsisten dan muncul
     * baik di toast (AJAX) maupun di blok error form (non-AJAX).
     */
    protected function saveValidationError(Request $request, \Illuminate\Contracts\Support\MessageProvider $errors)
    {
        if ($this->wantsJsonResponse($request)) {
            return response()->json([
                'success' => false,
                'msg' => 'Data belum lengkap.',
                'errors' => $errors->getMessageBag()->toArray(),
            ], 422);
        }
        return back()->withErrors($errors)->withInput();
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
                'videos' => VideoLanding::count(),
                'announcements' => PengumumanLanding::count(),
                'unread_messages' => PesanKontakLanding::unread()->count(),
            ],
        ]);
    }

    public function pengaturan()
    {
        $tenant = tenant();

        $heroSlide = \App\Models\Landing\SlideHeroLanding::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        return view('admin-landing.pengaturan', [
            'title' => 'Pengaturan Landing Page',
            'setting' => PengaturanLanding::current(),
            'heroTitle' => $heroSlide?->title,
            'heroSubtitle' => $heroSlide?->subtitle,
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
            'hero' => [
                'label' => 'Hero Beranda',
                'fields' => [], // sentinel: tidak disimpan via fill() — ditangani di cabang khusus.
                'rules' => [
                    'hero_title' => ['nullable', 'string', 'max:150'],
                    'hero_subtitle' => ['nullable', 'string', 'max:255'],
                ],
                'messages' => [],
            ],
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

        // Pecah paragraf panjang berdasarkan baris kosong. Mendukung admin yang
        // menulis banyak paragraf di satu textarea dengan newline kosong sebagai
        // pemisah.
        $p1 = $strip('paragraph_1') ?: ($current['paragraph_1'] ?? '');
        $p2 = $strip('paragraph_2') ?: ($current['paragraph_2'] ?? '');
        $combined = trim($p1 . "\n\n" . $p2);
        $chunks = $combined === '' ? [] : preg_split('/\R{2,}/u', $combined);
        $paragraphs = [];
        foreach ($chunks as $c) {
            $c = trim((string) $c);
            if ($c !== '') $paragraphs[] = $c;
        }

        return [
            'photo' => $photo,
            'quote' => $strip('quote') ?: ($current['quote'] ?? null),
            'paragraph_1' => $paragraphs[0] ?? null,
            'paragraph_2' => $paragraphs[1] ?? null,
            'paragraphs' => $paragraphs,
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

        Log::info('[pengaturanStore] section=' . $sectionKey, [
            'tenant' => tenant('id'),
            'host'   => $request->getHost(),
            'all'    => $request->all(),
        ]);

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
            Log::info('[pengaturanStore:bg] BEFORE', [
                'hero_background_db' => $setting->hero_background,
                'bgChoice'           => $request->input('hero_background_choice'),
                'hasFile'            => $request->hasFile('hero_background_custom'),
                'fileMeta'           => $request->hasFile('hero_background_custom')
                    ? [
                        'original' => $request->file('hero_background_custom')->getClientOriginalName(),
                        'mime'     => $request->file('hero_background_custom')->getMimeType(),
                        'size'     => $request->file('hero_background_custom')->getSize(),
                    ]
                    : null,
            ]);

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

        // Section 'hero' — update title/subtitle pada baris PERTAMA
        // tabel lp_slide_hero (model SlideHeroLanding). Baris diambil
        // berurutan sort_order ASC, id ASC (tie-break). Kalau tabel
        // kosong, otomatis dibuat slide baru dengan is_active=true.
        if ($sectionKey === 'hero') {
            $title = isset($data['hero_title']) ? trim((string) $data['hero_title']) : '';
            $subtitle = isset($data['hero_subtitle']) ? trim((string) $data['hero_subtitle']) : '';
            $title = $title !== '' ? $title : null;
            $subtitle = $subtitle !== '' ? $subtitle : null;

            $slide = \App\Models\Landing\SlideHeroLanding::query()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();
            if (!$slide) {
                $slide = new \App\Models\Landing\SlideHeroLanding();
                $slide->sort_order = 1;
                $slide->is_active = true;
            }
            $slide->title = $title;
            $slide->subtitle = $subtitle;
            $slide->save();

            // Section hero tidak menyentuh $setting (PengaturanLanding),
            // sehingga skip fill() di bawah.
            if ($this->wantsJsonResponse($request)) {
                return response()->json([
                    'success' => true,
                    'msg' => 'Hero Beranda berhasil disimpan.',
                    'section' => 'hero',
                    'saved_fields' => ['hero_title', 'hero_subtitle'],
                    'values' => $this->buildSectionValues('hero', $setting),
                    'hero_background_url' => $setting->heroBackgroundUrl(),
                    'hero_background_key' => $setting->activeThemeBackgroundKey(),
                    'hero_background_meta' => null,
                    'landing_url' => tenant()?->landingUrl(),
                    'redirect' => route('app.admin-landing.pengaturan'),
                ]);
            }
            return redirect()
                ->route('app.admin-landing.pengaturan')
                ->with('success', 'Hero Beranda berhasil disimpan.');
        }

        // Filter $data HANYA ke kolom yang di-whitelist untuk section ini.
        // Ini mencegah field dari section lain (yang ikut terkirim via form
        // lain di halaman yang sama) men-overwrite data DB.
        $allowedFields = $section['fields'];
        $payload = array_intersect_key($data, array_flip($allowedFields));

        Log::info('[pengaturanStore:after-filter] section=' . $sectionKey, [
            'payload' => $payload,
            'data_all' => $data,
        ]);

        $setting->fill($payload)->save();
        $setting->refresh();

        Log::info('[pengaturanStore:after-save] section=' . $sectionKey, [
            'hero_background_db' => $setting->hero_background,
            'theme_button_color' => $setting->theme_button_color,
            'school_name'        => $setting->school_name,
        ]);

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
                'values' => $this->buildSectionValues($sectionKey, $setting),
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
        $categories = ArtikelLanding::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin-landing.artikel.indeks', [
            'title' => 'Program / Berita',
            'categories' => $categories,
        ]);
    }

    /**
     * Endpoint AJAX untuk mode "1 data 1 kotak" (card list + load more).
     * GET /app/admin-landing/posts/cards?page=1&per_page=12&q=...
     */
    public function postsCards(Request $request)
    {
        $perPage = max(4, min(48, (int) $request->query('per_page', 12)));
        $page    = max(1, (int) $request->query('page', 1));
        $q       = trim((string) $request->query('q', ''));

        $query = ArtikelLanding::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        if ($q !== '') {
            $like = '%'.$q.'%';
            $query->where(function ($w) use ($like) {
                $w->where('title', 'like', $like)
                  ->orWhere('slug', 'like', $like)
                  ->orWhere('category', 'like', $like)
                  ->orWhere('excerpt', 'like', $like);
            });
        }

        $total      = (clone $query)->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $rows       = $query->forPage($page, $perPage)->get();

        $html = '';
        foreach ($rows as $row) {
            $html .= $this->renderPostCard($row);
        }

        return response()->json([
            'html'        => $html,
            'page'        => $page,
            'per_page'    => $perPage,
            'total'       => $total,
            'total_pages' => $totalPages,
            'has_more'    => $page < $totalPages,
            'empty'       => $rows->isEmpty(),
        ]);
    }

    /**
     * Render satu kartu HTML untuk artikel.
     */
    protected function renderPostCard(ArtikelLanding $row): string
    {
        $imgUrl = $row->image
            ? \Illuminate\Support\Facades\Storage::disk('public')->url('landing/'.$row->image)
            : null;
        $title   = e($row->title);
        $slug    = e($row->slug);
        $excerpt = $row->excerpt ? e(\Illuminate\Support\Str::limit(strip_tags($row->excerpt), 160)) : '';
        $category = $row->category ? '<span class="lp-cat-chip">'.e($row->category).'</span>' : '';
        $featured = $row->is_featured
            ? '<span class="lp-status-badge is-featured" title="Unggulan"><span class="material-symbols-rounded" style="font-size:14px;">star</span></span>'
            : '';
        $statusBadge = $row->is_published
            ? '<span class="lp-status-badge is-published">Dipublikasikan</span>'
            : '<span class="lp-status-badge is-draft">Draft</span>';
        $date = $row->published_at ? $row->published_at->format('d M Y') : '—';

        $cover = $imgUrl
            ? '<img src="'.$imgUrl.'" class="lp-card-img" alt="">'
            : '<div class="lp-card-img lp-card-img--empty d-inline-flex align-items-center justify-content-center"><span class="material-symbols-rounded">image</span></div>';

        $editUrl = route('app.admin-landing.posts.edit', $row->id);
        $delUrl  = route('app.admin-landing.posts.destroy', $row->id);
        $publishUrl = route('app.admin-landing.posts.toggle-publish', $row->id);
        $featuredUrl = route('app.admin-landing.posts.toggle-featured', $row->id);

        $pubCheck  = $row->is_published ? 'checked' : '';
        $featCheck = $row->is_featured ? 'checked' : '';

        $actions = '<div class="lp-card-actions pb-0">'
            .'<div class="lp-card-toggles">'
            .'<label class="lp-switch" title="Publish — tampilkan di halaman publik">'
            .'<input type="checkbox" class="lp-switch-input lp-toggle-publish" data-url="'.$publishUrl.'" '.$pubCheck.'>'
            .'<span class="lp-switch-track"><span class="lp-switch-thumb"></span></span>'
            .'<span class="lp-switch-label"><span class="material-symbols-rounded" style="font-size:13px;">publish</span> Publish</span>'
            .'</label>'
            .'<label class="lp-switch" title="Tampilkan di Beranda — sematkan sebagai artikel pilihan">'
            .'<input type="checkbox" class="lp-switch-input lp-toggle-featured" data-url="'.$featuredUrl.'" '.$featCheck.'>'
            .'<span class="lp-switch-track"><span class="lp-switch-thumb"></span></span>'
            .'<span class="lp-switch-label"><span class="material-symbols-rounded" style="font-size:13px;">star</span> Beranda</span>'
            .'</label>'
            .'</div>'
            .'<div class="lp-card-action-buttons">'
            .'<a href="'.$editUrl.'" class="btn btn-sm btn-icon btn-outline-primary" title="Edit artikel" aria-label="Edit">'
            .'<span class="material-symbols-rounded" style="font-size:16px;">edit</span></a>'
            .'<form action="'.$delUrl.'" method="POST" class="d-inline lp-card-delete" data-confirm="Hapus artikel &quot;'.$title.'&quot;?" style="display:inline">'
            .csrf_field().method_field('DELETE')
            .'<button type="submit" class="btn btn-sm btn-icon btn-outline-danger" title="Hapus artikel" aria-label="Hapus">'
            .'<span class="material-symbols-rounded" style="font-size:16px;">delete</span></button>'
            .'</form>'
            .'</div>'
            .'</div>';

        return '<article class="lp-card" data-id="'.$row->id.'">'
            .'<div class="lp-card-cover">'.$cover.'</div>'
            .'<div class="lp-card-body">'
            .'<div class="lp-card-meta-top">'.$category.$featured.$statusBadge.'</div>'
            .'<h3 class="lp-card-title">'.$title.'</h3>'
            .($excerpt !== '' ? '<p class="lp-card-excerpt">'.$excerpt.'</p>' : '')
            .'<div class="lp-card-meta-bottom"><span class="material-symbols-rounded" style="font-size:14px;">link</span> /'.$slug.' · '.$date.' · <span class="material-symbols-rounded" style="font-size:14px;">visibility</span> '.(int)($row->views ?? 0).'x</div>'
            .$actions
            .'</div>'
            .'</article>';
    }

    public function postsData(Request $request)
    {
        $query = ArtikelLanding::query()
            ->orderByDesc('published_at')
            ->orderByDesc('id');

        return DataTables::eloquent($query)
            ->editColumn('title', function ($row) {
                $star = $row->is_featured
                    ? '<span class="material-symbols-rounded lp-featured-star" title="Ditampilkan di beranda">star</span> '
                    : '';
                $excerpt = $row->excerpt
                    ? '<div class="lp-row-excerpt">'.e(\Illuminate\Support\Str::limit(strip_tags($row->excerpt), 100)).'</div>'
                    : '';
                return $star
                    .'<div class="lp-row-title">'.e($row->title).'</div>'
                    .$excerpt
                    .'<div class="lp-row-slug">/'.e($row->slug).'</div>';
            })
            ->editColumn('image', function ($row) {
                if ($row->image) {
                    $url = \Illuminate\Support\Facades\Storage::disk('public')->url('landing/'.$row->image);
                    return '<img src="'.$url.'" class="lp-thumb" alt="">';
                }
                return '<span class="lp-thumb-empty material-symbols-rounded">image</span>';
            })
            ->editColumn('category', function ($row) {
                return $row->category
                    ? '<span class="lp-cat-chip">'.e($row->category).'</span>'
                    : '<span class="text-muted small">—</span>';
            })
            ->editColumn('published_at', function ($row) {
                return $row->published_at
                    ? '<span class="text-muted small">'.$row->published_at->format('d M Y').'</span>'
                    : '<span class="text-muted small">—</span>';
            })
            ->editColumn('is_published', function ($row) {
                return $row->is_published
                    ? '<span class="lp-status-badge is-published">Dipublikasikan</span>'
                    : '<span class="lp-status-badge is-draft">Draft</span>';
            })
            ->addColumn('action', function ($row) {
                $detailData = [
                    'id' => $row->id,
                    'title' => $row->title,
                    'slug' => $row->slug,
                    'excerpt' => $row->excerpt,
                    'category' => $row->category,
                    'published_at' => $row->published_at ? $row->published_at->format('d M Y H:i') : null,
                    'is_published' => (bool) $row->is_published,
                    'is_featured' => (bool) $row->is_featured,
                    'views' => (int) ($row->views ?? 0),
                    'image' => $row->image ? \Illuminate\Support\Facades\Storage::disk('public')->url('landing/'.$row->image) : null,
                ];
                $json = e(json_encode($detailData, JSON_UNESCAPED_UNICODE));
                $editUrl = e(route('app.admin-landing.posts.edit', $row->id));
                $delUrl  = e(route('app.admin-landing.posts.destroy', $row->id));
                return '<div class="lp-table-actions">'
                    .'<button type="button" class="btn btn-sm btn-outline-secondary btn-icon lp-row-detail" '
                    .'data-detail=\''.$json.'\' title="Lihat detail"><span class="material-symbols-rounded">more_horiz</span></button>'
                    .'<a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary btn-icon" title="Edit artikel"><span class="material-symbols-rounded">edit</span></a>'
                    .'<form action="'.$delUrl.'" method="POST" class="d-inline lp-row-delete" data-confirm="Hapus artikel &quot;'.e($row->title).'&quot;?" style="display:inline">'
                    .csrf_field().method_field('DELETE')
                    .'<button type="submit" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus artikel"><span class="material-symbols-rounded">delete</span></button>'
                    .'</form>'
                    .'</div>';
            })
            ->rawColumns(['title', 'image', 'category', 'published_at', 'is_published', 'action'])
            ->toJson();
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

    /**
     * Toggle publish/unpublish dari halaman indeks (tanpa buka form edit).
     * PATCH /app/admin-landing/posts/{post}/toggle-publish
     */
    public function postTogglePublish(Request $request, $post)
    {
        $model = ArtikelLanding::findOrFail($post);
        $model->is_published = ! $model->is_published;
        // Saat dipublikasikan pertama kali & belum ada tanggal → set tanggal publish.
        if ($model->is_published && empty($model->published_at)) {
            $model->published_at = now();
        }
        $model->save();

        return response()->json([
            'ok'           => true,
            'is_published' => (bool) $model->is_published,
            'label'        => 'Publish',
            'msg'          => $model->is_published
                ? 'Artikel ditampilkan di halaman publik.'
                : 'Artikel disembunyikan dari halaman publik.',
        ]);
    }

    /**
     * Toggle featured/unfeatured (sematkan sebagai pilihan) dari halaman indeks.
     * PATCH /app/admin-landing/posts/{post}/toggle-featured
     */
    public function postToggleFeatured(Request $request, $post)
    {
        $model = ArtikelLanding::findOrFail($post);
        $model->is_featured = ! $model->is_featured;
        $model->save();

        return response()->json([
            'ok'           => true,
            'is_featured'  => (bool) $model->is_featured,
            'label'        => 'Beranda',
            'msg'          => $model->is_featured
                ? 'Artikel disematkan sebagai pilihan.'
                : 'Artikel dilepas dari sematan pilihan.',
        ]);
    }

    public function postUploadContent(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:51200'],
            'type' => ['nullable', Rule::in(['image', 'video'])],
        ]);

        $file = $request->file('file');
        $mime = (string) $file->getMimeType();
        $isImage = str_starts_with($mime, 'image/');
        $isVideo = str_starts_with($mime, 'video/');

        if (! $isImage && ! $isVideo) {
            return response()->json([
                'success' => false,
                'msg' => 'Tipe file tidak didukung. Unggah gambar atau video.',
            ], 422);
        }

        $ext = strtolower($file->getClientOriginalExtension() ?: $file->extension());
        $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'file';
        $name = $base . '-' . substr((string) Str::uuid(), 0, 8) . ($ext ? '.' . $ext : '');

        $subdir = $isVideo ? 'posts/videos' : 'posts/images';
        $stored = $file->storeAs($this->uploadDir() . '/' . $subdir, $name, 'public');

        $url = Storage::disk('public')->url($stored);
        $kind = $isVideo ? 'video' : 'image';

        return response()->json([
            'success' => true,
            'kind' => $kind,
            'location' => $url,
            'path' => $stored,
        ]);
    }

    private function validatePost(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'excerpt' => ['nullable', 'string'],
            'content' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'published_at' => ['nullable', 'date'],
        ]);

        // Tag otomatis diturunkan dari kategori (slug-friendly) supaya konsisten
        // tanpa meminta input tambahan dari pengguna.
        $data['tags'] = !empty($data['category']) ? $data['category'] : null;

        return $data;
    }

    public function announcements()
    {
        return view('admin-landing.pengumuman.indeks', [
            'title' => 'Pengumuman',
        ]);
    }

    public function announcementsData()
    {
        $query = PengumumanLanding::query();

        return DataTables::eloquent($query)
            ->addColumn('title_col', function ($a) {
                $html = '<div class="lp-ann-title">'.e($a->title).'</div>';
                if (!empty($a->content)) {
                    $excerpt = \Illuminate\Support\Str::limit(strip_tags($a->content), 140);
                    $html .= '<div class="lp-ann-content">'.e($excerpt).'</div>';
                }
                return $html;
            })
            ->addColumn('file_col', function ($a) {
                if (!empty($a->file)) {
                    return '<span class="lp-ann-file" title="'.e($a->file).'">'
                        .'<span class="material-symbols-rounded">attach_file</span>'
                        .'<span>'.e($a->file).'</span></span>';
                }
                return '<span class="text-muted small">—</span>';
            })
            ->addColumn('status_col', function ($a) {
                return $a->is_published
                    ? '<span class="lp-status-badge is-published">Aktif</span>'
                    : '<span class="lp-status-badge is-draft">Draft</span>';
            })
            ->addColumn('action', function ($a) {
                $edit = route('app.admin-landing.announcements.edit', $a->id);
                $destroy = route('app.admin-landing.announcements.destroy', $a->id);
                $html  = '<div class="lp-table-actions justify-content-center">';
                $html .= '<a href="'.e($edit).'" class="btn btn-sm btn-outline-primary btn-icon" title="Edit">';
                $html .= '<span class="material-symbols-rounded">edit</span></a>';
                $html .= '<form action="'.e($destroy).'" method="POST" data-confirm="Hapus pengumuman &quot;'.e($a->title).'&quot; ?" class="d-inline">';
                $html .= csrf_field().method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus">';
                $html .= '<span class="material-symbols-rounded">delete</span></button>';
                $html .= '</form></div>';
                return $html;
            })
            ->editColumn('published_at', function ($a) {
                return $a->published_at ? $a->published_at->format('Y-m-d H:i:s') : '';
            })
            ->editColumn('is_published', function ($a) {
                return $a->is_published ? 1 : 0;
            })
            ->rawColumns(['title_col', 'file_col', 'status_col', 'action'])
            ->orderColumn('published_at', 'published_at $1')
            ->make(true);
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
        return view('admin-landing.galeri.indeks', [
            'title' => 'Galeri',
        ]);
    }

    public function galleriesData(Request $request)
    {
        $query = GaleriLanding::query();

        return DataTables::eloquent($query)
            ->addColumn('image', function ($g) {
                if ($g->image) {
                    $url = Storage::disk('public')->url('landing/'.$g->image);
                    return '<img src="'.e($url).'" alt="" class="lp-gallery-thumb">';
                }
                return '<span class="lp-gallery-thumb-empty"><span class="material-symbols-rounded">image</span></span>';
            })
            ->addColumn('title_col', function ($g) {
                $html = '<div class="lp-gallery-title">'.e($g->title).'</div>';
                if (!empty($g->description)) {
                    $excerpt = \Illuminate\Support\Str::limit(strip_tags($g->description), 90);
                    $html .= '<small>'.e($excerpt).'</small>';
                }
                return $html;
            })
            ->addColumn('album_col', function ($g) {
                if (!empty($g->album)) {
                    return '<span class="badge text-bg-light border">'.e($g->album).'</span>';
                }
                return '<span class="text-muted small">—</span>';
            })
            ->addColumn('sort_col', function ($g) {
                return '<span class="text-muted small">'.e($g->sort_order ?? 0).'</span>';
            })
            ->addColumn('status_col', function ($g) {
                return $g->is_published
                    ? '<span class="lp-status-badge is-published">Dipublikasikan</span>'
                    : '<span class="lp-status-badge is-draft">Draft</span>';
            })
            ->addColumn('action', function ($g) {
                $edit = route('app.admin-landing.galleries.edit', $g->id);
                $destroy = route('app.admin-landing.galleries.destroy', $g->id);
                $html  = '<div class="lp-table-actions justify-content-center">';
                $html .= '<a href="'.e($edit).'" class="btn btn-sm btn-outline-primary btn-icon" title="Edit foto">';
                $html .= '<span class="material-symbols-rounded">edit</span></a>';
                $html .= '<form action="'.e($destroy).'" method="POST" data-confirm="'.e('Hapus foto "'.$g->title.'" ?').'" class="d-inline">';
                $html .= csrf_field().method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus foto">';
                $html .= '<span class="material-symbols-rounded">delete</span></button>';
                $html .= '</form></div>';
                return $html;
            })
            ->editColumn('sort_order', function ($g) {
                return (int) ($g->sort_order ?? 0);
            })
            ->editColumn('is_published', function ($g) {
                return $g->is_published ? 1 : 0;
            })
            ->rawColumns(['image', 'title_col', 'album_col', 'sort_col', 'status_col', 'action'])
            ->orderColumn('sort_order', 'sort_order $1')
            ->make(true);
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

    public function videos(Request $request)
    {
        return view('admin-landing.video.indeks', [
            'title' => 'Video',
        ]);
    }

    public function videosData(Request $request)
    {
        $query = VideoLanding::query();

        $stats = [
            'total'     => (int) VideoLanding::count(),
            'youtube'   => (int) VideoLanding::where('source', 'youtube')->count(),
            'local'     => (int) VideoLanding::where('source', 'local')->count(),
            'published' => (int) VideoLanding::where('is_published', true)->count(),
        ];

        return DataTables::eloquent($query)
            ->with(['extra' => ['stats' => $stats]])
            ->addColumn('preview_col', function ($v) {
                $thumb   = $v->display_thumb;
                $isLocal = $v->isLocal();
                $ytId    = $v->isYoutube() ? ($v->youtube_id ?? '') : '';
                $local   = $isLocal && $v->file_path
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($v->file_path)
                    : '';
                $poster  = $v->poster
                    ? \Illuminate\Support\Facades\Storage::disk('public')->url($v->poster)
                    : '';

                $attrs = ' data-yt-id="'.e($ytId).'"'
                    .' data-local-src="'.e($local).'"'
                    .' data-poster="'.e($poster).'"'
                    .' data-title="'.e($v->title).'"'
                    .' data-description="'.e(strip_tags((string) $v->description)).'"';

                if ($thumb) {
                    $html  = '<button type="button" class="lp-video-thumb-btn lp-video-trigger"'.$attrs.' aria-label="Putar '.e($v->title).'">';
                    $html .= '<div class="lp-video-thumb"><img src="'.e($thumb).'" alt="'.e($v->title).'" loading="lazy"></div>';
                    $html .= '<span class="material-symbols-rounded lp-video-play">play_circle</span>';
                    $html .= '</button>';
                    return $html;
                }
                if ($isLocal) {
                    return '<button type="button" class="lp-video-thumb-btn lp-video-thumb-empty-wrap lp-video-trigger"'.$attrs
                        .' aria-label="Putar '.e($v->title).'">'
                        .'<div class="lp-video-thumb lp-video-thumb-empty"><span class="material-symbols-rounded">movie</span></div>'
                        .'<span class="material-symbols-rounded lp-video-play">play_circle</span>'
                        .'</button>';
                }
                return '<div class="lp-video-thumb lp-video-thumb-empty"><span class="material-symbols-rounded">videocam_off</span></div>';
            })
            ->addColumn('source_col', function ($v) {
                if ($v->isLocal()) {
                    return '<span class="lp-status-badge is-local">'
                        .'<span class="material-symbols-rounded" style="font-size:14px;vertical-align:-3px;">movie</span> Lokal</span>';
                }
                return '<span class="lp-status-badge is-yt">'
                    .'<span class="material-symbols-rounded" style="font-size:14px;vertical-align:-3px;">smart_display</span> YouTube</span>';
            })
            ->addColumn('title_col', function ($v) {
                $html = '<div class="lp-video-title">'.e($v->title).'</div>';
                if (!empty($v->description)) {
                    $excerpt = \Illuminate\Support\Str::limit(strip_tags($v->description), 90);
                    $html .= '<small>'.e($excerpt).'</small>';
                }
                return $html;
            })
            ->addColumn('url_col', function ($v) {
                if ($v->isLocal()) {
                    $path = $v->file_path ?: '-';
                    $short = \Illuminate\Support\Str::limit($path, 60);
                    return '<span class="lp-video-url" title="'.e($path).'">'
                        .'<span class="material-symbols-rounded" style="font-size:14px;vertical-align:-2px;">description</span> '
                        .e($short).'</span>';
                }
                $url = (string) $v->youtube_url;
                $short = \Illuminate\Support\Str::limit($url, 60);
                $html = '<a href="'.e($url).'" target="_blank" rel="noopener" class="text-decoration-none lp-video-url" title="'.e($url).'">';
                $html .= '<span class="material-symbols-rounded" style="font-size:14px;vertical-align:-2px;">open_in_new</span> ';
                $html .= e($short).'</a>';
                return $html;
            })
            ->addColumn('status_col', function ($v) {
                return $v->is_published
                    ? '<span class="lp-status-badge is-published">Dipublikasikan</span>'
                    : '<span class="lp-status-badge is-draft">Draft</span>';
            })
            ->addColumn('action', function ($v) {
                $edit = route('app.admin-landing.videos.edit', $v->id);
                $destroy = route('app.admin-landing.videos.destroy', $v->id);
                $html  = '<div class="lp-table-actions justify-content-center">';
                $html .= '<a href="'.e($edit).'" class="btn btn-sm btn-outline-primary btn-icon" title="Edit video">';
                $html .= '<span class="material-symbols-rounded">edit</span></a>';
                $html .= '<form action="'.e($destroy).'" method="POST" data-confirm="'.e('Hapus video "'.$v->title.'" ?').'" class="d-inline">';
                $html .= csrf_field().method_field('DELETE');
                $html .= '<button type="submit" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus video">';
                $html .= '<span class="material-symbols-rounded">delete</span></button>';
                $html .= '</form></div>';
                return $html;
            })
            ->editColumn('is_published', function ($v) {
                return $v->is_published ? 1 : 0;
            })
            ->rawColumns(['preview_col', 'source_col', 'title_col', 'url_col', 'status_col', 'action'])
            ->orderColumn('id', 'id $1')
            ->make(true);
    }

    public function videoCreate()
    {
        return view('admin-landing.video.formulir', [
            'title' => 'Tambah Video',
            'video' => new VideoLanding(),
            'action' => route('app.admin-landing.videos.store'),
        ]);
    }

    public function videoStore(Request $request)
    {
        // Log masuk method untuk membantu debug request yang gagal.
        \Illuminate\Support\Facades\Log::info('[videoStore] masuk', [
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'expects_json' => $request->expectsJson(),
            'host' => $request->getHost(),
            'url' => $request->fullUrl(),
            'has_title' => $request->has('title'),
            'has_source' => $request->has('source'),
            'has_youtube_url' => $request->has('youtube_url'),
            'has_video_file' => $request->hasFile('video_file'),
            'has_video_poster' => $request->hasFile('video_poster'),
            'all_keys' => array_keys($request->all()),
            'files' => array_keys($request->allFiles()),
        ]);

        try {
            return $this->handleVideoStore($request);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Validasi gagal — biarkan Laravel tangani untuk return 422 JSON otomatis.
            throw $ve;
        } catch (\Throwable $e) {
            // Tangani error tak terduga. Untuk AJAX selalu return JSON agar
            // handler JS tidak melihat HTML error page.
            \Illuminate\Support\Facades\Log::error('[videoStore] exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            if ($this->wantsJsonResponse($request)) {
                return response()->json([
                    'success' => false,
                    'msg' => $e->getMessage() ?: 'Terjadi kesalahan saat menyimpan video.',
                    'error' => class_basename($e),
                    'debug' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ] : null,
                ], 500);
            }
            throw $e;
        }
    }

    /**
     * Logika inti store video — dipisah dari wrapper videoStore() agar
     * try-catch dapat membungkus semuanya tanpa mengganggu alur
     * `return` di tengah method.
     */
    private function handleVideoStore(Request $request)
    {
        $source = $request->input('source', VideoLanding::SOURCE_YOUTUBE);

        // Diagnosa: catat kondisi file upload SEBELUM validasi supaya kalau
        // server error kita bisa lihat apakah file sampai ke PHP, ukuran,
        // MIME, dan kode error upload. Penting untuk file .mov iPhone
        // yang sering gagal di Windows karena MIME tidak standar.
        if ($source === VideoLanding::SOURCE_LOCAL) {
            $f = $request->file('video_file');
            \Illuminate\Support\Facades\Log::info('[videoStore] file diagnostics', [
                'has_file' => $request->hasFile('video_file'),
                'is_valid' => $f ? $f->isValid() : null,
                'upload_error' => $f ? $f->getError() : null,
                'error_message' => $f ? $f->getErrorMessage() : null,
                'size_bytes' => $f ? $f->getSize() : null,
                'client_mime' => $f ? $f->getClientMimeType() : null,
                'server_mime' => $f ? $f->getMimeType() : null,
                'client_ext' => $f ? $f->getClientOriginalExtension() : null,
                'client_name' => $f ? $f->getClientOriginalName() : null,
                'tmp_path_ok' => $f ? (is_file($f->getPathname()) ? 'yes' : 'no') : null,
                'tmp_readable' => $f ? (is_readable($f->getPathname()) ? 'yes' : 'no') : null,
                'poster_has' => $request->hasFile('video_poster'),
            ]);
        }

        $rules = [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'source' => ['required', Rule::in([VideoLanding::SOURCE_YOUTUBE, VideoLanding::SOURCE_LOCAL])],
            'is_published' => ['nullable', 'boolean'],
        ];
        if ($source === VideoLanding::SOURCE_YOUTUBE) {
            $rules['youtube_url'] = ['required', 'string', 'max:500'];
        } else {
            // Cek file video lewat BOTH ekstensi dan MIME (libmagic) — kasusumum:
            //  - MP4 iPhone/Canon tulis sebagai 'application/octet-stream'
            //  - MOV iPhone tulis sebagai 'video/quicktime'
            //  - MKV terdeteksi 'video/x-matroska'
            // Jadi gunakan kombinasi yang longgar. Poster tetap opsional.
            $rules['video_file'] = ['required', 'file', 'max:51200', function ($attr, $value, $fail) {
                if (! $value instanceof \Illuminate\Http\UploadedFile || ! $value->isValid()) return;
                $ext = strtolower($value->getClientOriginalExtension());
                $okExt = in_array($ext, ['mp4','m4v','mov','webm','mkv','qt'], true);
                $mime = strtolower((string) $value->getMimeType());
                $okMime = in_array($mime, [
                    'video/mp4','video/webm','video/quicktime','video/x-matroska','video/x-m4v',
                    'application/octet-stream','application/mp4',
                ], true);
                if (! $okExt && ! $okMime) {
                    $fail('Format video harus mp4, mov, m4v, webm, atau mkv. (Terdeteksi ekstensi: '.$ext.', MIME: '.$mime.')');
                }
            }];
            $rules['video_poster'] = ['nullable', 'file', 'max:5120', function ($attr, $value, $fail) {
                if (! $value instanceof \Illuminate\Http\UploadedFile) return;
                if (! $value->isValid()) return;
                $ext = strtolower($value->getClientOriginalExtension());
                $okExt = in_array($ext, ['jpg','jpeg','png','webp'], true);
                $mime = strtolower((string) $value->getMimeType());
                $okMime = str_starts_with($mime, 'image/');
                if (! $okExt && ! $okMime) {
                    $fail('Poster harus berupa gambar (jpg, jpeg, png, webp).');
                }
            }];
        }
        $data = $request->validate($rules, [
            'title.required' => 'Judul video wajib diisi.',
            'source.required' => 'Sumber video wajib dipilih.',
            'source.in' => 'Sumber video tidak valid.',
            'youtube_url.required' => 'URL YouTube wajib diisi.',
            'video_file.required' => 'File video wajib dipilih.',
            'video_file.mimes' => 'Format video harus mp4, mov, m4v, webm, atau mkv.',
            'video_file.max' => 'Ukuran video maksimal 50MB.',
            'video_poster.mimes' => 'Poster harus berformat jpg, jpeg, png, atau webp.',
            'video_poster.max' => 'Ukuran poster maksimal 5MB.',
        ]);

        $data['source'] = $source;
        $data['is_published'] = $request->boolean('is_published');

        if ($source === VideoLanding::SOURCE_YOUTUBE) {
            $embed = self::normalizeYoutubeEmbed($data['youtube_url']);
            if (! $embed) {
                return back()->withErrors(['youtube_url' => 'URL YouTube tidak dikenali.'])->withInput();
            }
            $data['youtube_url'] = $embed;
        } else {
            // Store file video ke disk 'public'. Catat ukuran & path yang
            // dihasilkan supaya diagnosa mudah kalau file corrupt / disk penuh.
            $file = $request->file('video_file');
            \Illuminate\Support\Facades\Log::info('[videoStore] storing video file', [
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'ext'  => $file->getClientOriginalExtension(),
            ]);
            try {
                $path = $file->store($this->uploadDir() . '/videos', 'public');
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('[videoStore] gagal store file video: ' . $e->getMessage(), [
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'upload_error' => $file->getError(),
                    'error_message' => $file->getErrorMessage(),
                ]);
                throw new \RuntimeException(
                    'Gagal menyimpan file video. Periksa ukuran (maks 50MB), format, dan ruang disk server. (' . $e->getMessage() . ')',
                    500,
                    $e
                );
            }
            \Illuminate\Support\Facades\Log::info('[videoStore] video stored', ['path' => $path]);
            $data['file_path'] = $path;
            if ($request->hasFile('video_poster')) {
                $data['poster'] = $request->file('video_poster')->store($this->uploadDir() . '/videos/posters', 'public');
            }
            // Jangan set 'poster' = null di create — biarkan tidak di-set agar
            // Eloquent fill() tidak overwrite kolom ke null bila nanti
            // aturan diubah. youtube_url di-null-kan karena source=local.
            $data['youtube_url'] = null;
        }

        try {
            $video = VideoLanding::create($data);
        } catch (\Throwable $e) {
            // DB insert gagal setelah upload sukses → bersihkan file agar tidak jadi yatim.
            if (! empty($data['file_path'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($data['file_path']);
            }
            if (! empty($data['poster'])) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($data['poster']);
            }
            \Illuminate\Support\Facades\Log::error('Gagal simpan Video: ' . $e->getMessage(), [
                'source' => $source,
                'title' => $data['title'] ?? null,
            ]);
            throw $e;
        }

        \Illuminate\Support\Facades\Log::info('Video berhasil disimpan', [
            'id' => $video->id ?? null,
            'title' => $video->title ?? null,
            'source' => $video->source ?? null,
            'is_ajax' => $this->wantsJsonResponse($request),
        ]);

        return $this->saveSuccess($request, 'Video berhasil ditambahkan.', 'app.admin-landing.videos');
    }

    public function videoEdit($video)
    {
        $model = VideoLanding::findOrFail($video);

        return view('admin-landing.video.formulir', [
            'title' => 'Edit Video',
            'video' => $model,
            'action' => route('app.admin-landing.videos.update', $model->id),
        ]);
    }

    public function videoUpdate(Request $request, $video)
    {
        \Illuminate\Support\Facades\Log::info('[videoUpdate] masuk', [
            'id' => $video,
            'method' => $request->method(),
            'is_ajax' => $request->ajax(),
            'expects_json' => $request->expectsJson(),
            'host' => $request->getHost(),
            'url' => $request->fullUrl(),
            'all_keys' => array_keys($request->all()),
            'files' => array_keys($request->allFiles()),
        ]);
        try {
            return $this->handleVideoUpdate($request, $video);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            throw $ve;
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[videoUpdate] exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            if ($this->wantsJsonResponse($request)) {
                return response()->json([
                    'success' => false,
                    'msg' => $e->getMessage() ?: 'Terjadi kesalahan saat memperbarui video.',
                    'error' => class_basename($e),
                    'debug' => config('app.debug') ? [
                        'file' => $e->getFile(),
                        'line' => $e->getLine(),
                    ] : null,
                ], 500);
            }
            throw $e;
        }
    }

    private function handleVideoUpdate(Request $request, $video)
    {
        $model = VideoLanding::findOrFail($video);
        \Illuminate\Support\Facades\Log::info('[videoUpdate] model loaded', ['id' => $model->id, 'source' => $model->source]);
        $source = $request->input('source', $model->source ?: VideoLanding::SOURCE_YOUTUBE);

        // Diagnosa file upload jika source baru = local.
        if ($source === VideoLanding::SOURCE_LOCAL) {
            $f = $request->file('video_file');
            \Illuminate\Support\Facades\Log::info('[videoUpdate] file diagnostics', [
                'has_file' => $request->hasFile('video_file'),
                'is_valid' => $f ? $f->isValid() : null,
                'upload_error' => $f ? $f->getError() : null,
                'error_message' => $f ? $f->getErrorMessage() : null,
                'size_bytes' => $f ? $f->getSize() : null,
                'client_mime' => $f ? $f->getClientMimeType() : null,
                'server_mime' => $f ? $f->getMimeType() : null,
                'client_ext' => $f ? $f->getClientOriginalExtension() : null,
                'client_name' => $f ? $f->getClientOriginalName() : null,
                'poster_has' => $request->hasFile('video_poster'),
                'source_changed' => $model->source !== $source,
            ]);
        }

        $rules = [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'source' => ['required', Rule::in([VideoLanding::SOURCE_YOUTUBE, VideoLanding::SOURCE_LOCAL])],
            'is_published' => ['nullable', 'boolean'],
        ];
        if ($source === VideoLanding::SOURCE_YOUTUBE) {
            $rules['youtube_url'] = ['required', 'string', 'max:500'];
        } else {
            // Saat UPDATE: jika source tetap local → file boleh null (pakai
            // yang lama). Jika source BERUBAH youtube→local → file WAJIB
            // diupload supaya tidak ada baris dengan source=local tanpa file.
            $fileRequired = ($model->source !== VideoLanding::SOURCE_LOCAL);
            $rules['video_file'] = [$fileRequired ? 'required' : 'nullable', 'file', 'max:51200', function ($attr, $value, $fail) {
                if (! $value instanceof \Illuminate\Http\UploadedFile || ! $value->isValid()) return;
                $ext = strtolower($value->getClientOriginalExtension());
                $okExt = in_array($ext, ['mp4','m4v','mov','webm','mkv','qt'], true);
                $mime = strtolower((string) $value->getMimeType());
                $okMime = in_array($mime, [
                    'video/mp4','video/webm','video/quicktime','video/x-matroska','video/x-m4v',
                    'application/octet-stream','application/mp4',
                ], true);
                if (! $okExt && ! $okMime) {
                    $fail('Format video harus mp4, mov, m4v, webm, atau mkv. (Terdeteksi ekstensi: '.$ext.', MIME: '.$mime.')');
                }
            }];
            $rules['video_poster'] = ['nullable', 'file', 'max:5120', function ($attr, $value, $fail) {
                if (! $value instanceof \Illuminate\Http\UploadedFile) return;
                if (! $value->isValid()) return;
                $ext = strtolower($value->getClientOriginalExtension());
                $okExt = in_array($ext, ['jpg','jpeg','png','webp'], true);
                $mime = strtolower((string) $value->getMimeType());
                $okMime = str_starts_with($mime, 'image/');
                if (! $okExt && ! $okMime) {
                    $fail('Poster harus berupa gambar (jpg, jpeg, png, webp).');
                }
            }];
        }
        $data = $request->validate($rules, [
            'title.required' => 'Judul video wajib diisi.',
            'source.required' => 'Sumber video wajib dipilih.',
            'source.in' => 'Sumber video tidak valid.',
            'youtube_url.required' => 'URL YouTube wajib diisi.',
            'video_file.mimes' => 'Format video harus mp4, mov, m4v, webm, atau mkv.',
            'video_file.max' => 'Ukuran video maksimal 50MB.',
            'video_poster.mimes' => 'Poster harus berformat jpg, jpeg, png, atau webp.',
            'video_poster.max' => 'Ukuran poster maksimal 5MB.',
        ]);
        \Illuminate\Support\Facades\Log::info('[videoUpdate] validated', ['keys' => array_keys($data), 'source' => $source]);

        $data['source'] = $source;
        $data['is_published'] = $request->boolean('is_published');

        if ($source === VideoLanding::SOURCE_YOUTUBE) {
            $embed = self::normalizeYoutubeEmbed($data['youtube_url']);
            if (! $embed) {
                return back()->withErrors(['youtube_url' => 'URL YouTube tidak dikenali.'])->withInput();
            }
            $data['youtube_url'] = $embed;
            // Jika source BERUBAH dari local ke youtube, bersihkan file lokal lama.
            if ($model->source !== VideoLanding::SOURCE_YOUTUBE) {
                if ($model->file_path && Storage::disk('public')->exists($model->file_path)) {
                    Storage::disk('public')->delete($model->file_path);
                }
                if ($model->poster && Storage::disk('public')->exists($model->poster)) {
                    Storage::disk('public')->delete($model->poster);
                }
                $data['file_path'] = null;
                $data['poster'] = null;
            }
        } else {
            $newFilePath = null;
            $newPoster = null;
            if ($request->hasFile('video_file')) {
                // Upload file baru → hapus file lama (kalau ada) supaya tidak jadi yatim.
                if ($model->file_path && Storage::disk('public')->exists($model->file_path)) {
                    Storage::disk('public')->delete($model->file_path);
                }
                $file = $request->file('video_file');
                \Illuminate\Support\Facades\Log::info('[videoUpdate] storing video file', [
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'ext'  => $file->getClientOriginalExtension(),
                ]);
                $newFilePath = $file->store($this->uploadDir() . '/videos', 'public');
                \Illuminate\Support\Facades\Log::info('[videoUpdate] video stored', ['path' => $newFilePath]);
                $data['file_path'] = $newFilePath;
            }
            if ($request->hasFile('video_poster')) {
                if ($model->poster && Storage::disk('public')->exists($model->poster)) {
                    Storage::disk('public')->delete($model->poster);
                }
                $newPoster = $request->file('video_poster')->store($this->uploadDir() . '/videos/posters', 'public');
                $data['poster'] = $newPoster;
            }
            // Jika source BERUBAH dari youtube ke local, bersihkan youtube_url lama.
            // Kalau tidak upload file DAN source tetap local, biarkan file_path/poster
            // lama apa adanya (jangan di-null-kan).
            if ($model->source !== $source) {
                $data['youtube_url'] = null;
            } else {
                // Source sama (local) dan tidak ada upload baru → unset key supaya fill()
                // tidak overwrite ke null. file_path & poster tetap nilai lama.
                unset($data['youtube_url']);
            }
        }

        try {
            $model->fill($data)->save();
            \Illuminate\Support\Facades\Log::info('Video berhasil diperbarui', ['id' => $model->id, 'title' => $model->title, 'source' => $model->source]);
        } catch (\Throwable $e) {
            // Kalau upload baru berhasil tapi save() gagal, hapus file baru
            // agar tidak jadi yatim saat DB roll back (atau model tidak konsisten).
            if (! empty($newFilePath) && Storage::disk('public')->exists($newFilePath)) {
                Storage::disk('public')->delete($newFilePath);
            }
            \Illuminate\Support\Facades\Log::error('Gagal update Video: ' . $e->getMessage(), ['id' => $model->id]);
            throw $e;
        }

        return $this->saveSuccess($request, 'Video berhasil diperbarui.', 'app.admin-landing.videos');
    }

    public function videoDestroy(Request $request, $video)
    {
        $model = VideoLanding::findOrFail($video);
        if ($model->file_path && Storage::disk('public')->exists($model->file_path)) {
            Storage::disk('public')->delete($model->file_path);
        }
        if ($model->poster && Storage::disk('public')->exists($model->poster)) {
            Storage::disk('public')->delete($model->poster);
        }
        $model->delete();

        return $this->deleteSuccess($request, 'Video berhasil dihapus.', 'app.admin-landing.videos');
    }

    /**
     * Ekstrak ID video YouTube dari berbagai format URL:
     *  - https://www.youtube.com/watch?v=ID
     *  - https://youtu.be/ID
     *  - https://www.youtube.com/embed/ID
     *  - https://www.youtube.com/shorts/ID
     *  - https://m.youtube.com/watch?v=ID
     * Mengembalikan null bila tidak terdeteksi.
     */
    public static function extractYoutubeId(?string $url): ?string
    {
        if (! $url) return null;
        $url = trim($url);

        if (preg_match('#youtu\.be/([A-Za-z0-9_-]{6,})#i', $url, $m)) {
            return $m[1];
        }
        if (preg_match('#youtube\.com/embed/([A-Za-z0-9_-]{6,})#i', $url, $m)) {
            return $m[1];
        }
        if (preg_match('#youtube\.com/shorts/([A-Za-z0-9_-]{6,})#i', $url, $m)) {
            return $m[1];
        }
        if (preg_match('#youtube\.com/(?:watch\?v=|v/)([A-Za-z0-9_-]{6,})#i', $url, $m)) {
            return $m[1];
        }
        if (preg_match('#^([A-Za-z0-9_-]{6,})$#', $url)) {
            return $url;
        }
        return null;
    }

    /**
     * Normalisasi URL YouTube apapun jadi URL embed resmi:
     *  https://www.youtube.com/embed/ID
     * Aman dipanggil di view publik.
     */
    public static function normalizeYoutubeEmbed(?string $url): ?string
    {
        $id = self::extractYoutubeId($url);
        return $id ? 'https://www.youtube.com/embed/'.$id : null;
    }

    /**
     * URL thumbnail YouTube. Gunakan maxresdefault sebagai default,
     * fallback ke hqdefault jika tidak tersedia.
     */
    public static function youtubeThumbnailUrl(string $id): string
    {
        return 'https://i.ytimg.com/vi/'.$id.'/hqdefault.jpg';
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
        return view('admin-landing.pesan-kontak.indeks', [
            'title' => 'Pesan Masuk',
        ]);
    }

    public function contactMessagesData()
    {
        $query = PesanKontakLanding::query();

        return DataTables::eloquent($query)
            ->addColumn('sender', function ($m) {
                $name = $m->name ?: 'Anonim';
                $email = $m->email ?: '—';
                $bold = $m->status === PesanKontakLanding::STATUS_BARU ? ' fw-semibold' : '';
                return '<div class="'.$bold.'">'.e($name).'</div>'
                    .'<div class="text-muted small fw-normal">'.e($email).'</div>';
            })
            ->addColumn('subject_col', function ($m) {
                $subj = $m->subject ?: '(tanpa subjek)';
                $excerpt = \Illuminate\Support\Str::limit(strip_tags($m->message), 90);
                $bold = $m->status === PesanKontakLanding::STATUS_BARU ? ' fw-semibold' : '';
                return '<div class="'.$bold.'">'.e($subj).'</div>'
                    .'<div class="text-muted small fw-normal text-truncate" style="max-width:380px;">'.e($excerpt).'</div>';
            })
            ->addColumn('status_col', function ($m) {
                // Badge read-only untuk menampilkan status (default: 'baru')
                $badgeClass = $m->statusBadgeClass();
                $label = $m->statusLabel();
                return '<span class="'.$badgeClass.'">'.e($label).'</span>';
            })
            ->addColumn('action', function ($m) {
                // Tombol toggle: maju ke status berikutnya (atau kembali ke 'baru' jika sudah selesai)
                $nextStatus = match ($m->status) {
                    PesanKontakLanding::STATUS_BARU     => PesanKontakLanding::STATUS_DIBACA,
                    PesanKontakLanding::STATUS_DIBACA   => PesanKontakLanding::STATUS_SELESAI,
                    default                              => PesanKontakLanding::STATUS_BARU,
                };
                $toggleIcon  = match ($m->status) {
                    PesanKontakLanding::STATUS_BARU     => 'mark_email_read',
                    PesanKontakLanding::STATUS_DIBACA   => 'task_alt',
                    default                              => 'mark_email_unread',
                };
                $toggleTitle = match ($m->status) {
                    PesanKontakLanding::STATUS_BARU     => 'Tandai sudah dibaca',
                    PesanKontakLanding::STATUS_DIBACA   => 'Tandai selesai',
                    default                              => 'Buka lagi (baru)',
                };
                $toggleOutline = match ($m->status) {
                    PesanKontakLanding::STATUS_BARU     => 'btn-outline-primary',
                    PesanKontakLanding::STATUS_DIBACA   => 'btn-outline-success',
                    default                              => 'btn-outline-secondary',
                };

                $btnView = '<button type="button" class="btn btn-sm btn-outline-primary lp-view-message btn-icon" '
                    .'data-bs-toggle="modal" data-bs-target="#lpMessageModal" '
                    .'data-id="'.e((string) $m->id).'" '
                    .'data-name="'.e($m->name).'" '
                    .'data-email="'.e($m->email).'" '
                    .'data-subject="'.e($m->subject).'" '
                    .'data-message="'.e($m->message).'" '
                    .'data-date="'.e($m->created_at?->format('d M Y H:i') ?: '').'" '
                    .'title="Lihat detail">'
                    .'<span class="material-symbols-rounded">visibility</span></button>';

                $btnToggle = '<button type="button" class="btn btn-sm '.e($toggleOutline).' lp-toggle-status btn-icon" '
                    .'data-id="'.e((string) $m->id).'" '
                    .'data-current="'.e($m->status).'" '
                    .'data-next="'.e($nextStatus).'" '
                    .'data-label-current="'.e($m->statusLabel()).'" '
                    .'data-label-next="'.e(match ($nextStatus) {
                        PesanKontakLanding::STATUS_DIBACA  => 'Dibaca',
                        PesanKontakLanding::STATUS_SELESAI => 'Selesai',
                        default                              => 'Baru',
                    }).'" '
                    .'title="'.e($toggleTitle).'">'
                    .'<span class="material-symbols-rounded">'.e($toggleIcon).'</span></button>';

                $destroy = route('app.admin-landing.contact-messages.destroy', $m->id);
                $btnDel  = '<form action="'.e($destroy).'" method="POST" data-confirm="Hapus pesan ini?" class="d-inline">'
                    .csrf_field().method_field('DELETE')
                    .'<button type="submit" class="btn btn-sm btn-outline-danger btn-icon" title="Hapus">'
                    .'<span class="material-symbols-rounded">delete</span></button>'
                    .'</form>';

                return '<div class="lp-table-actions justify-content-center">'.$btnView.$btnToggle.$btnDel.'</div>';
            })
            ->editColumn('created_at', function ($m) {
                return $m->created_at ? $m->created_at->format('Y-m-d H:i:s') : '';
            })
            ->rawColumns(['sender', 'subject_col', 'status_col', 'action'])
            ->orderColumn('created_at', 'created_at $1')
            ->make(true);
    }

    public function contactMessageStatus(Request $request, $message)
    {
        $model = PesanKontakLanding::findOrFail($message);
        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', PesanKontakLanding::STATUSES)],
        ]);
        $oldStatus = $model->status;
        $model->status = $data['status'];
        // Set is_read = true setiap kali status berubah dari 'baru' ke yang lain
        if ($oldStatus === PesanKontakLanding::STATUS_BARU && $model->status !== PesanKontakLanding::STATUS_BARU) {
            $model->is_read = true;
        }
        $model->save();

        return $this->saveSuccess(
            $request,
            'Status pesan diperbarui menjadi "'.$model->statusLabel().'".',
            null,
            ['status' => $model->status, 'label' => $model->statusLabel(), 'badge_class' => $model->statusBadgeClass()]
        );
    }

    public function contactMessageMark(Request $request, $message)
    {
        // Kompatibilitas mundur: redirect ke update status 'dibaca'
        $model = PesanKontakLanding::findOrFail($message);
        $markRead = $request->boolean('is_read', true);
        $model->status = $markRead ? PesanKontakLanding::STATUS_DIBACA : PesanKontakLanding::STATUS_BARU;
        if ($markRead) {
            $model->is_read = true;
        }
        $model->save();

        return $this->saveSuccess(
            $request,
            $markRead ? 'Pesan ditandai sudah dibaca.' : 'Pesan ditandai belum dibaca.',
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
    /**
     * Susun payload 'values' per section untuk dikirim ke klien via JSON,
     * agar JS bisa menyegarkan card tanpa reload halaman.
     *
     * - 'identitas' : teks school_name/tagline + URL & filename logo & favicon
     *                (mengikuti pola view: Storage::disk('public')->url('landing/...'))
     * - 'kontak'    : teks email/phone/whatsapp/address/google_maps_url
     * - 'medsos'    : teks facebook/instagram/youtube/tiktok
     * - 'background': key aktif + URL background + metadata file (kalau custom)
     * - 'warna'     : theme_button_color (hex uppercase) + theme_text_color
     * - 'sambutan'  : teks quote/head_name/head_role/paragraph_1/paragraph_2 +
     *                URL foto (kalau uploaded) atau URL apa adanya
     */
    private function buildSectionValues(string $sectionKey, PengaturanLanding $setting): array
    {
        $disk = Storage::disk('public');

        switch ($sectionKey) {
            case 'hero':
                $slide = \App\Models\Landing\SlideHeroLanding::query()
                    ->orderBy('sort_order')
                    ->orderBy('id')
                    ->first();
                return [
                    'hero_title' => $slide?->title,
                    'hero_subtitle' => $slide?->subtitle,
                ];

            case 'identitas':
                $logoUrl = $setting->logo ? $disk->url('landing/' . $setting->logo) : null;
                $faviconUrl = $setting->favicon ? $disk->url('landing/' . $setting->favicon) : null;
                return [
                    'school_name' => $setting->school_name,
                    'tagline' => $setting->tagline,
                    'logo_url' => $logoUrl,
                    'logo_filename' => $setting->logo,
                    'favicon_url' => $faviconUrl,
                    'favicon_filename' => $setting->favicon,
                ];

            case 'kontak':
                return [
                    'email' => $setting->email,
                    'phone' => $setting->phone,
                    'whatsapp' => $setting->whatsapp,
                    'address' => $setting->address,
                    'google_maps_url' => $setting->google_maps_url,
                ];

            case 'medsos':
                return [
                    'facebook' => $setting->facebook,
                    'instagram' => $setting->instagram,
                    'youtube' => $setting->youtube,
                    'tiktok' => $setting->tiktok,
                ];

            case 'background':
                $isCustom = (bool) $setting->hero_background && str_starts_with((string) $setting->hero_background, 'custom:');
                $meta = null;
                if ($isCustom) {
                    $f = substr((string) $setting->hero_background, strlen('custom:'));
                    if ($f !== '' && $disk->exists($this->diskPath($f))) {
                        $fullPath = $disk->path($this->diskPath($f));
                        [$w, $h] = getimagesize($fullPath) ?: [0, 0];
                        $bytes = filesize($fullPath);
                        $meta = [
                            'name' => $f,
                            'width' => $w,
                            'height' => $h,
                            'size_label' => $bytes >= 1048576
                                ? number_format($bytes / 1048576, 2) . ' MB'
                                : number_format($bytes / 1024, 0) . ' KB',
                        ];
                    }
                }
                return [
                    'hero_background_key' => $setting->activeThemeBackgroundKey(),
                    'hero_background_url' => $setting->heroBackgroundUrl(),
                    'hero_background_meta' => $meta,
                    'is_custom' => $isCustom,
                ];

            case 'warna':
                return [
                    'theme_button_color' => $setting->activeThemeButtonColor(),
                    'theme_text_color' => $setting->activeThemeTextColor(),
                ];

            case 'sambutan':
                $stored = $setting->welcome ?: [];
                $photo = $stored['photo'] ?? null;
                $photoUrl = null;
                if (is_string($photo) && $photo !== '') {
                    if (str_starts_with($photo, 'uploaded:')) {
                        $f = substr($photo, strlen('uploaded:'));
                        if ($f !== '') {
                            $photoUrl = $disk->url('landing/' . $f);
                        }
                    } else {
                        $photoUrl = $photo;
                    }
                }
                return [
                    'photo_url' => $photoUrl,
                    'photo_raw' => $photo,
                    'quote' => $stored['quote'] ?? null,
                    'paragraph_1' => $stored['paragraph_1'] ?? null,
                    'paragraph_2' => $stored['paragraph_2'] ?? null,
                    'head_name' => $stored['head_name'] ?? null,
                    'head_role' => $stored['head_role'] ?? null,
                ];

            default:
                return [];
        }
    }

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
