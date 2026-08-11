<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LpPpdbSchedule extends Model
{
    protected $table = 'lp_ppdb_schedules';

    protected $fillable = [
        'gelombang',
        'start_date',
        'end_date',
        'biaya_daftar',
        'spp_bulanan',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'start_date'   => 'date',
        'end_date'     => 'date',
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('start_date');
    }
}
