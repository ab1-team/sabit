<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Profil extends Model
{
    use HasFactory;
    protected $table = 'profil';
    protected $guarded = ['id'];

    protected static ?self $cached = null;

    protected static function safeFirst(): ?self
    {
        if (self::$cached !== null) {
            return self::$cached;
        }
        try {
            if (!Schema::hasTable('profil')) {
                return self::$cached = null;
            }
            return self::$cached = self::first();
        } catch (\Throwable $e) {
            return self::$cached = null;
        }
    }

    public static function flushCache(): void
    {
        self::$cached = null;
    }

    protected static function defaultLogo(): string
    {
        return asset('assets/img/apple-icon.png');
    }

    public static function logoUrl(): string
    {
        $profil = self::safeFirst();
        if (!$profil || !$profil->logo) {
            return self::defaultLogo();
        }
        return self::tenantStorageUrl('logo/' . $profil->logo, $profil->logo);
    }

    public static function logoVersion(): string
    {
        $profil = self::safeFirst();
        if (!$profil || !$profil->logo) {
            return '0';
        }
        return (string) ($profil->updated_at?->timestamp ?? time());
    }

    public static function logoDiskPath(): ?string
    {
        $profil = self::safeFirst();
        if ($profil && $profil->logo && Storage::disk('public')->exists('logo/' . $profil->logo)) {
            return Storage::disk('public')->path('logo/' . $profil->logo);
        }
        return null;
    }

    public static function logoPath(): string
    {
        return self::logoDiskPath() ?? public_path('assets/img/apple-icon.png');
    }

    public static function tenantStorageUrl(string $path, ?string $existsCheck = null): string
    {
        $path = ltrim($path, '/');

        if ($existsCheck !== null) {
            $disk = Storage::disk('public');
            if (!$disk->exists($path)) {
                return self::defaultLogo();
            }
        }

        try {
            $url = Storage::disk('public')->url($path);
            if ($url) {
                return $url;
            }
        } catch (\Throwable $e) {
        }
        return asset('storage/' . $path);
    }

    public static function namaLembaga(): string
    {
        $profil = self::safeFirst();
        return $profil->nama ?? config('app.name');
    }
}
