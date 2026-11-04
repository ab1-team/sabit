<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TenantContext
{
    private const CACHE_KEY = 'central:tenants_for_select';
    private const CACHE_TTL = 300;

    /**
     * Nilai sentinel yang dipakai select2 untuk opsi "Semua Sekolah".
     * Dikirim sebagai tenant_id agar tidak bentrok dengan string kosong
     * (yang oleh framework diubah menjadi null oleh ConvertEmptyStringsToNull).
     */
    public const ALL_SENTINEL = '__all__';

    public function handle(Request $request, Closure $next): Response
    {
        $tenants = $this->tenantsForSelect();

        $request->attributes->set('tenants_all', $tenants);

        $session = session('central_tenant_id');

        // PENTING: bedakan "parameter tenant_id tidak dikirim sama sekali"
        // dari "tenant_id sengaja dikosongkan (= pilih Semua Sekolah)".
        // Tanpa pembedaan ini, pengguna tidak pernah bisa kembali ke tampilan
        // "Semua Sekolah" setelah pernah memilih sekolah tertentu.
        //
        // Filter konsol pusat hanya boleh diubah lewat request navigasi (GET).
        // Form POST (mis. simpan invoice) juga membawa `tenant_id` milik
        // formulirnya sendiri, sehingga tidak boleh mengubah cakupan konsol.
        if ($request->isMethod('GET') && ($request->query->has('tenant_id') || $request->request->has('tenant_id'))) {
            $requested = $request->query('tenant_id', $request->input('tenant_id'));

            // Nilai kosong / sentinel = tidak memfilter sekolah tertentu.
            if ($requested === null || $requested === '' || $requested === 'all' || $requested === self::ALL_SENTINEL) {
                session()->forget('central_tenant_id');
            } else {
                $exists = collect($tenants)->firstWhere('id', $requested);
                if ($exists) {
                    session(['central_tenant_id' => (string) $requested]);
                }
            }
        } elseif ($session !== null && $session !== 'all') {
            // Tenant yang tersimpan sudah dihapus → bersihkan filter.
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
            return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
                $model = config('tenancy.tenant_model');
                return $model::query()
                    ->with(['domains' => fn($q) => $q->select('id', 'tenant_id', 'domain', 'type')])
                    ->select('id', 'nama_sekolah', 'email')
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
            });
        } catch (\Throwable $e) {
            return [];
        }
    }

    public static function flushCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
