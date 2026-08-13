<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PersyaratanPpdb extends Model
{
    protected $table = 'lp_ppdb_persyaratan';

    protected $fillable = [
        'group',
        'title',
        'items',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order'   => 'integer',
    ];

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    /**
     * Decode items yang disimpan sebagai JSON atau newline-separated.
     */
    public function getItemsListAttribute(): array
    {
        $raw = $this->items ?? '';
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return array_values(array_filter(array_map('trim', $decoded), fn ($v) => $v !== ''));
        }
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $raw) ?: []), fn ($v) => $v !== ''));
    }
}
