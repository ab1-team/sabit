<?php

namespace App\Models;

use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\DatabaseConfig;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    protected $table = 'tenants';

    protected $fillable = [
        'id',
        'nama_sekolah',
        'email',
    ];

    public static function getCustomColumns(): array
    {
        return [
            'id',
            'nama_sekolah',
            'email',
            'created_at',
            'updated_at',
        ];
    }

    /**
     * Disable VirtualColumn trait — columns are real DB columns.
     */
    protected function encodeAttributes(): void
    {
        // no-op: skip encoding attributes to JSON column
    }

    public function database(): DatabaseConfig
    {
        return new DatabaseConfig($this);
    }
}