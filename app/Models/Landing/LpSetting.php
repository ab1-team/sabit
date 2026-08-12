<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class LpSetting extends Model
{
    protected $table = 'lp_settings';

    protected $fillable = [
        'school_name',
        'tagline',
        'logo',
        'favicon',
        'email',
        'phone',
        'whatsapp',
        'address',
        'google_maps_url',
        'facebook',
        'instagram',
        'youtube',
        'tiktok',
        'meta_description',
        'meta_keywords',
        'hero_background',
        'theme_button_color',
        'theme_text_color',
        'hero_badges',
        'stats',
        'welcome',
        'jenjang',
        'keunggulan',
        'ppdb_cta',
    ];

    protected $casts = [
        'hero_badges' => 'array',
        'stats' => 'array',
        'welcome' => 'array',
        'jenjang' => 'array',
        'keunggulan' => 'array',
        'ppdb_cta' => 'array',
    ];

    public static function current(): self
    {
        return static::query()->first() ?? new static();
    }

    /**
     * Daftar 4 gambar default untuk pilihan "Background Tema" di Hero.
     * Asset disimpan di public/landing/themes/ sebagai SVG gradient
     * (bukan foto) agar ringan, scalable, dan tidak butuh unduh gambar.
     *
     * Struktur tiap item:
     *  - key   : identifier disimpan di kolom hero_background
     *  - label : label yang ditampilkan di admin
     *  - image : path relatif ke public/ (diawali tanpa /)
     *  - desc  : deskripsi singkat warna/tema untuk tooltip admin
     */
    public static function themeBackgroundDefaults(): array
    {
        return [
            ['key' => 'default-1', 'label' => 'Standar 1', 'image' => 'landing/themes/bg-1.svg', 'desc' => 'Biru Ceria — tenang & modern'],
            ['key' => 'default-2', 'label' => 'Standar 2', 'image' => 'landing/themes/bg-2.svg', 'desc' => 'Hijau Edukatif — segar & akademik'],
            ['key' => 'default-3', 'label' => 'Standar 3', 'image' => 'landing/themes/bg-3.svg', 'desc' => 'Emas Premium — prestisius'],
            ['key' => 'default-4', 'label' => 'Standar 4', 'image' => 'landing/themes/bg-4.svg', 'desc' => 'Ungu Kreatif — modern & kreatif'],
        ];
    }

    /**
     * Daftar 4 preset warna tombol + 1 slot custom.
     */
    public static function themeButtonColorDefaults(): array
    {
        return [
            ['key' => 'default', 'label' => 'Biru Standar', 'value' => '#2563eb'],
            ['key' => 'emerald', 'label' => 'Hijau Emerald', 'value' => '#059669'],
            ['key' => 'amber', 'label' => 'Kuning Amber', 'value' => '#d97706'],
            ['key' => 'rose', 'label' => 'Merah Rose', 'value' => '#e11d48'],
        ];
    }

    /**
     * Daftar 4 preset warna text + 1 slot custom.
     */
    public static function themeTextColorDefaults(): array
    {
        return [
            ['key' => 'default', 'label' => 'Gelap Standar', 'value' => '#0f172a'],
            ['key' => 'slate', 'label' => 'Abu Slate', 'value' => '#334155'],
            ['key' => 'navy', 'label' => 'Biru Navy', 'value' => '#1e3a8a'],
            ['key' => 'charcoal', 'label' => 'Arang', 'value' => '#1f2937'],
        ];
    }

    public function activeThemeButtonColor(): string
    {
        $value = $this->theme_button_color;

        if ($value && $this->isValidHex($value)) {
            return strtoupper($value);
        }

        foreach (self::themeButtonColorDefaults() as $row) {
            if ($row['key'] === $value) {
                return strtoupper($row['value']);
            }
        }

        return '#2563eb';
    }

    public function themePrimaryDark(): string
    {
        return $this->darkenHex($this->activeThemeButtonColor(), 0.85);
    }

    public function themePrimarySoft(): string
    {
        return $this->lightenHex($this->activeThemeButtonColor(), 0.85);
    }

    public function themePrimaryRgb(): string
    {
        $hex = ltrim($this->activeThemeButtonColor(), '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }

        return hexdec(substr($hex, 0, 2)) . ', ' . hexdec(substr($hex, 2, 2)) . ', ' . hexdec(substr($hex, 4, 2));
    }

    private function darkenHex(string $hex, float $factor): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = (int) round(hexdec(substr($hex, 0, 2)) * $factor);
        $g = (int) round(hexdec(substr($hex, 2, 2)) * $factor);
        $b = (int) round(hexdec(substr($hex, 4, 2)) * $factor);

        return '#' . strtoupper(
            str_pad(dechex(max(0, min(255, $r))), 2, '0', STR_PAD_LEFT) .
            str_pad(dechex(max(0, min(255, $g))), 2, '0', STR_PAD_LEFT) .
            str_pad(dechex(max(0, min(255, $b))), 2, '0', STR_PAD_LEFT)
        );
    }

    private function lightenHex(string $hex, float $factor): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        $r = (int) round(hexdec(substr($hex, 0, 2)) + (255 - hexdec(substr($hex, 0, 2))) * $factor);
        $g = (int) round(hexdec(substr($hex, 2, 2)) + (255 - hexdec(substr($hex, 2, 2))) * $factor);
        $b = (int) round(hexdec(substr($hex, 4, 2)) + (255 - hexdec(substr($hex, 4, 2))) * $factor);

        return '#' . strtoupper(
            str_pad(dechex(max(0, min(255, $r))), 2, '0', STR_PAD_LEFT) .
            str_pad(dechex(max(0, min(255, $g))), 2, '0', STR_PAD_LEFT) .
            str_pad(dechex(max(0, min(255, $b))), 2, '0', STR_PAD_LEFT)
        );
    }

    public function activeThemeTextColor(): string
    {
        $value = $this->theme_text_color;

        if ($value && $this->isValidHex($value)) {
            return strtoupper($value);
        }

        foreach (self::themeTextColorDefaults() as $row) {
            if ($row['key'] === $value) {
                return strtoupper($row['value']);
            }
        }

        return $this->activeThemeButtonColor();
    }

    public function isThemeButtonCustom(): bool
    {
        $v = $this->theme_button_color;
        return $v && $this->isValidHex($v);
    }

    public function isThemeTextCustom(): bool
    {
        $v = $this->theme_text_color;
        return $v && $this->isValidHex($v);
    }

    private function isValidHex(string $v): bool
    {
        return (bool) preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $v);
    }

    /**
     * Pilih key yang sedang aktif, dengan fallback ke default-1.
     */
    public function activeThemeBackgroundKey(): string
    {
        $key = $this->hero_background;

        if ($key && str_starts_with($key, 'custom:')) {
            return $key;
        }

        $valid = array_column(self::themeBackgroundDefaults(), 'key');

        return in_array($key, $valid, true) ? $key : 'default-1';
    }

    /**
     * Dapatkan URL gambar background sesuai setting aktif.
     * Mengembalikan path relatif (diawali /) agar otomatis mengikuti
     * host/port request saat ini (penting untuk multi-tenant beda subdomain).
     *
     * Catatan: untuk custom upload, URL dibangun via Storage::disk('public')
     * karena di context tenant, disk 'public' sudah ke-root ke
     * storage/app/public/tenant/<id> dengan URL prefix /storage/tenant/<id>.
     * Jangan hard-code /storage/landing/ karena akan 404 di tenant.
     */
    public function heroBackgroundUrl(): string
    {
        $active = $this->activeThemeBackgroundKey();

        if (str_starts_with($active, 'custom:')) {
            $filename = substr($active, strlen('custom:'));
            if ($filename !== '') {
                return Storage::disk('public')->url('landing/' . $filename);
            }
        }

        foreach (self::themeBackgroundDefaults() as $row) {
            if ($row['key'] === $active) {
                return '/' . ltrim($row['image'], '/');
            }
        }

        return '/landing/themes/bg-1.svg';
    }

    /**
     * True bila admin sudah pernah memilih background tema eksplisit
     * (key default-1..4 atau custom:...). Dipakai oleh view landing
     * untuk memutuskan: pakai background tema ATAU fallback ke hero slide.
     */
    public function hasThemeBackground(): bool
    {
        $key = $this->hero_background;

        if (!$key) {
            return false;
        }

        if (str_starts_with($key, 'custom:')) {
            $file = substr($key, strlen('custom:'));
            return $file !== '';
        }

        return in_array($key, array_column(self::themeBackgroundDefaults(), 'key'), true);
    }

    public function heroBadges(): array
    {
        return $this->hero_badges ?: [
            ['icon' => 'bi-patch-check-fill', 'text' => 'Terakreditasi A'],
            ['icon' => 'bi-trophy-fill', 'text' => '50+ Prestasi 2025'],
        ];
    }

    public function statsList(): array
    {
        return $this->stats ?: [
            ['icon' => 'bi-people-fill', 'color' => 'blue', 'value' => '1.200+', 'label' => 'Siswa Aktif'],
            ['icon' => 'bi-mortarboard-fill', 'color' => 'green', 'value' => '85+', 'label' => 'Guru & Staf Pengajar'],
            ['icon' => 'bi-trophy-fill', 'color' => 'amber', 'value' => '120+', 'label' => 'Prestasi Diraih'],
        ];
    }

    public function welcomeData(): array
    {
        return $this->welcome ?: [
            'photo' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=900&q=80',
            'quote' => 'Mendidik dengan Hati, Membentuk dengan Karakter.',
            'paragraph_1' => 'Selamat datang di {{school}}. Kami berkomitmen untuk memberikan pengalaman belajar terbaik bagi putra-putri Anda. Di era digital ini, kami memadukan kurikulum nasional dengan standar internasional untuk membentuk karakter yang kuat dan pemikiran yang kritis.',
            'paragraph_2' => 'Lingkungan belajar kami dirancang untuk menumbuhkan kreativitas, kolaborasi, dan kemandirian. Bersama-sama, mari kita wujudkan potensi maksimal setiap anak.',
            'head_name' => 'Dr. Budi Santoso, M.Pd.',
            'head_role' => 'Kepala Sekolah, {{school}}',
        ];
    }

    public function jenjangList(): array
    {
        return $this->jenjang ?: [
            ['key' => 'tk', 'age' => 'USIA 4–6 TAHUN', 'title' => 'TK / PAUD', 'icon' => 'bi-emoji-smile-fill', 'desc' => 'Pembelajaran bermain sambil belajar dengan pendekatan tematik untuk menumbuhkan rasa ingin tahu.'],
            ['key' => 'sd', 'age' => 'KELAS 1–6', 'title' => 'Sekolah Dasar', 'icon' => 'bi-pencil-square', 'desc' => 'Fondasi akademik yang kuat dengan literasi, numerasi, dan pengembangan karakter.'],
            ['key' => 'smp', 'age' => 'KELAS 7–9', 'title' => 'SMP', 'icon' => 'bi-backpack3-fill', 'desc' => 'Pendidikan menengah dengan eksplorasi minat, berpikir kritis, dan kepemimpinan.'],
            ['key' => 'sma', 'age' => 'KELAS 10–12', 'title' => 'SMA', 'icon' => 'bi-mortarboard-fill', 'desc' => 'Persiapan masuk perguruan tinggi terbaik dengan program akselerasi & bimbingan karir.'],
        ];
    }

    public function keunggulanList(): array
    {
        return $this->keunggulan ?: [
            ['color' => 'blue', 'icon' => 'bi-book-fill', 'title' => 'Kurikulum Merdeka', 'desc' => 'Penerapan kurikulum merdeka dengan pembelajaran yang fleksibel, mendalam, dan menyenangkan, mengembangkan potensi sesuai minat & bakat siswa.'],
            ['color' => 'green', 'icon' => 'bi-shield-check', 'title' => 'Pendidikan Karakter', 'desc' => 'Menumbuhkan nilai-nilai disiplin, integritas, dan kepemimpinan dalam setiap aspek kehidupan sekolah.'],
            ['color' => 'amber', 'icon' => 'bi-pc-display', 'title' => 'Digital Learning', 'desc' => 'Integrasi teknologi dalam pembelajaran dengan laboratorium komputer dan multimedia modern.'],
            ['color' => 'pink', 'icon' => 'bi-translate', 'title' => 'Lingkungan Dwibahasa', 'desc' => 'Penggunaan Bahasa Inggris & Indonesia secara aktif dalam keseharian sekolah, mempersiapkan siswa untuk komunikasi global.'],
            ['color' => 'purple', 'icon' => 'bi-palette-fill', 'title' => 'Seni & Kreativitas', 'desc' => 'Ruang ekspresi seni musik, lukis, dan tari yang mengembangkan kreativitas anak.'],
            ['color' => 'cyan', 'icon' => 'bi-trophy-fill', 'title' => 'Prestasi Gemilang', 'desc' => 'Ratusan prestasi akademik & non-akademik tingkat kota hingga nasional.'],
        ];
    }

    public function ppdbCtaData(): array
    {
        return $this->ppdb_cta ?: [
            'title' => 'Penerimaan Peserta Didik Baru {{year}} Telah Dibuka!',
            'paragraph' => 'Daftarkan putra-putri Anda sekarang dan jadilah bagian dari keluarga besar {{school}}. Kuota terbatas untuk setiap jenjang.',
            'button_primary_text' => 'Daftar Sekarang',
            'button_primary_url' => '#kontak',
            'button_secondary_text' => 'Hubungi Kami',
            'button_secondary_url' => '#kontak',
            'points' => [
                'Pendaftaran online & mudah',
                'Kuota terbatas per jenjang',
                'Bantuan seleksi & verifikasi',
            ],
            'is_active' => true,
        ];
    }
}
