<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Model;

class LpPpdbSetting extends Model
{
    protected $table = 'lp_ppdb_settings';

    protected $fillable = [
        'school_name',
        'eyebrow',
        'title',
        'subtitle',
        'cta_text',
        'cta_url',
        'secondary_text',
        'secondary_url',
        'hero_image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function current(): self
    {
        return static::query()->where('is_active', true)->first()
            ?? static::query()->first()
            ?? new static();
    }
}
