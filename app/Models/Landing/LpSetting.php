<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Model;

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
    ];

    public static function current(): self
    {
        return static::query()->first() ?? new static();
    }
}
