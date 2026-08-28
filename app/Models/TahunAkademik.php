<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class TahunAkademik extends Model
{
    use HasFactory;
    protected $table = 'tahun_akademik';
    protected $guarded = ['id'];

    private const CACHE_PREFIX_ACTIVE = 'tahun_akademik:active:';

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::activeCacheKey()));
        static::deleted(fn () => Cache::forget(self::activeCacheKey()));
    }

    protected static function activeCacheKey(): string
    {
        return self::CACHE_PREFIX_ACTIVE . (tenant('id') ?? 'central');
    }

    public function siswa()
    {
        return $this->hasMany(Siswa::class, 'tahun_akademik', 'nama_tahun');
    }

    public function anggotaKelas()
    {
        return $this->hasMany(AnggotaKelas::class, 'tahun_akademik', 'nama_tahun');
    }

    public static function aktif(): ?self
    {
        return Cache::remember(self::activeCacheKey(), 3600, function () {
            return static::query()->where('status', 'aktif')->first();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget(self::activeCacheKey());
    }

    public function aktifkan(): void
    {
        DB::transaction(function () {
            DB::table('tahun_akademik')->update([
                'status' => DB::raw("CASE WHEN id = " . (int) $this->id . " THEN 'aktif' ELSE 'nonaktif' END"),
            ]);
        });
        self::flushCache();
        $this->refresh();
    }
}
