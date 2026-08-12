<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;
use Stancl\Tenancy\Tenancy;

abstract class BaseSchoolController extends Controller
{
    protected function runInTenant(TenantContract $tenant, callable $callback)
    {
        /** @var Tenancy $tenancy */
        $tenancy = app(Tenancy::class);

        $currentKey = app()->bound(TenantContract::class)
            ? optional(app(TenantContract::class))->getTenantKey()
            : null;

        if ($currentKey !== null && $currentKey === $tenant->getTenantKey()) {
            return $callback();
        }

        $tenancy->initialize($tenant);
        try {
            return $callback();
        } finally {
            $tenancy->end();
        }
    }
}

