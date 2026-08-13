<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Model;

class BagianProfilLanding extends Model
{
    protected $table = 'lp_bagian_profil';

    protected $fillable = [
        'section_key',
        'title',
        'subtitle',
        'content',
        'badge_text',
        'badge_icon',
        'badge_extra',
        'extra_label',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }

    public static function getByKey(string $key): ?self
    {
        return static::where('section_key', $key)->first();
    }
}
