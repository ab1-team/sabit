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

    private const CACHE_PREFIX = 'lp_ppdb_pengaturan:current:';
    private const CACHE_TTL = 3600;

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::cacheKey()));
        static::deleted(fn () => Cache::forget(self::cacheKey()));
    }

    protected static function cacheKey(): string
    {
        return self::CACHE_PREFIX . (tenant('id') ?? 'central');
    }

    public static function flushCache(): void
    {
        Cache::forget(self::cacheKey());
    }

    public static function current(): self
    {
        $tenantId = tenant('id');
        if (! $tenantId) {
            // Tenancy belum aktif — jangan bikin cache entry dengan suffix
            // 'central' (bisa bocor ke tenant lain jika driver cache shared).
            return static::query()->where('is_active', true)->first()
                ?? static::query()->first()
                ?? new static();
        }

        return Cache::remember(self::cacheKey(), self::CACHE_TTL, function () {
            return static::query()->where('is_active', true)->first()
                ?? static::query()->first()
                ?? new static();
        });
    }
}
