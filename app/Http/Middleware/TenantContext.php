<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenants = $this->tenantsForSelect();

        $request->attributes->set('tenants_all', $tenants);

        $requested = $request->query('tenant_id', $request->input('tenant_id'));
        $session = session('central_tenant_id');

        if ($requested !== null && $requested !== '' && $requested !== 'all') {
            $exists = collect($tenants)->firstWhere('id', $requested);
            if ($exists) {
                session(['central_tenant_id' => (string) $requested]);
            }
        } elseif ($session !== null && $session !== 'all') {
            $exists = collect($tenants)->firstWhere('id', $session);
            if (!$exists) {
                session()->forget('central_tenant_id');
            }
        }

        $currentTenantId = session('central_tenant_id');
        $currentTenant = $currentTenantId ? collect($tenants)->firstWhere('id', $currentTenantId) : null;

        $request->attributes->set('tenants', $tenants);
        $request->attributes->set('current_tenant_id', $currentTenant?->id);
        $request->attributes->set('current_tenant', $currentTenant);

        view()->share('tenants', $tenants);
        view()->share('currentTenantId', $currentTenant?->id);
        view()->share('currentTenant', $currentTenant);

        return $next($request);
    }

    private function tenantsForSelect(): array
    {
        try {
            $model = config('tenancy.tenant_model');
            return $model::query()
                ->with('domains')
                ->orderBy('id')
                ->get()
                ->map(function ($t) {
                    $admin = $t->domains->firstWhere('type', 'admin');
                    $landing = $t->domains->firstWhere('type', 'landing');

                    return (object) [
                        'id'      => (string) $t->id,
                        'nama'    => $t->nama_sekolah ?? $t->id,
                        'domain'  => $admin->domain ?? optional($t->domains->first())->domain ?? '—',
                        'landing' => $landing->domain ?? null,
                    ];
                })
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }
}