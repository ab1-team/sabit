<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Model;

class LpFasilitas extends Model
{
    protected $table = 'lp_fasilitas';

    protected $fillable = [
        'title',
        'description',
        'icon',
        'color_key',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('id');
    }

    public function iconClass(): string
    {
        return $this->icon ?: 'bi-building';
    }

    public function colorKey(): string
    {
        return $this->color_key ?: 'blue';
    }
}
