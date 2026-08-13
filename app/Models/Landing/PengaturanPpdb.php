<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PengaturanPpdb extends Model
{
    protected $table = 'lp_ppdb_pengaturan';

    protected $fillable = [
        'school_name',
        'eyebrow',
        'title',
        'subtitle',
        'cta_text',
        'cta_url',
        'secondary_text',
        'secondary_url',
        'bottom_eyebrow',
        'bottom_title',
        'bottom_paragraph',
        'bottom_primary_text',
        'bottom_primary_url',
        'bottom_secondary_text',
        'bottom_secondary_url',
        'bottom_meta',
        'gambar_hero',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    private const CACHE_KEY = 'lp_ppdb_pengaturan:current';
    private const CACHE_TTL = 3600;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::CACHE_KEY));
        static::deleted(fn () => Cache::forget(self::CACHE_KEY));
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public static function current(): self
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return static::query()->where('is_active', true)->first()
                ?? static::query()->first()
                ?? new static();
        });
    }
}
