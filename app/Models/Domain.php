<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Stancl\Tenancy\Database\Models\Domain as BaseDomain;

class Domain extends BaseDomain
{
    public const TYPE_ADMIN = 'admin';
    public const TYPE_LANDING = 'landing';

    protected $fillable = [
        'domain',
        'type',
        'tenant_id',
    ];

    public function scopeAdmin(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_ADMIN);
    }

    public function scopeLanding(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_LANDING);
    }

    public function isAdmin(): bool
    {
        return $this->type === self::TYPE_ADMIN;
    }

    public function isLanding(): bool
    {
        return $this->type === self::TYPE_LANDING;
    }
}
