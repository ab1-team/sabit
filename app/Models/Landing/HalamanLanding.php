<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class HalamanLanding extends Model
{
    protected $table = 'lp_halaman';

    protected $fillable = [
        'title',
        'slug',
        'content',
        'image',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
