<?php

declare(strict_types=1);

namespace App\Models\Landing;

use Illuminate\Database\Eloquent\Model;

class StrukturOrganisasiLanding extends Model
{
    protected $table = 'lp_struktur_organisasi';

    protected $fillable = [
        'name',
        'role',
        'photo',
        'is_lead',
        'sort_order',
        'is_published',
    ];

    protected $casts = [
        'is_lead' => 'boolean',
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('is_lead', 'desc')->orderBy('sort_order')->orderBy('id');
    }

    public function photoUrl(): ?string
    {
        return $this->photo
            ? \Illuminate\Support\Facades\Storage::disk('public')->url('landing/' . $this->photo)
            : null;
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->name ?? ''));
        $letters = '';
        foreach (array_slice($parts, 0, 2) as $p) {
            $letters .= mb_substr($p, 0, 1);
        }

        return strtoupper($letters ?: '?');
    }
}
