<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\Tenant\TenantInvoice;
use App\Models\Tenant\TenantAdminUser;
use App\Models\Tenant\TenantTransaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tid = $this->currentTenantId($request);
        $tenants = $request->attributes->get('tenants', []);

        $invoiceBase = TenantInvoice::query();
        if ($tid) {
            $invoiceBase->where('tenant_id', $tid);
        }

        $summary = (clone $invoiceBase)
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid, SUM(CASE WHEN status = 'unpaid' THEN 1 ELSE 0 END) as unpaid, COALESCE(SUM(jumlah), 0) as total_amount")
            ->first();

        $ownerScopedCount = $tid
            ? TenantAdminUser::query()->where('tenant_id', $tid)->count()
            : TenantAdminUser::query()->distinct()->count('id');

        $ownerPerTenant = TenantAdminUser::query()
            ->select('tenant_id', DB::raw('COUNT(*) as total'))
            ->groupBy('tenant_id')
            ->pluck('total', 'tenant_id');

        $tenantStats = $this->tenantStats($tid);

        $stats = [
            'invoice_total' => (int) ($summary->total ?? 0),
            'invoice_paid'  => (int) ($summary->paid ?? 0),
            'invoice_open'  => (int) ($summary->unpaid ?? 0),
            'nominal_total' => (float) ($summary->total_amount ?? 0),
            'nominal_open'  => (float) (clone $invoiceBase)->where('status', 'unpaid')->sum('jumlah'),
            'owner_count'   => $tid ? (int) $ownerScopedCount : (int) TenantAdminUser::query()->distinct()->count('id'),
            'owner_total'   => (int) TenantAdminUser::query()->distinct()->count('id'),
            'tenant_total'  => count($tenants ?? []),
            'tenant_active' => $tenantStats['active'],
            'tenant_new'    => $tenantStats['new'],
        ];

        $invoices = (clone $invoiceBase)
            ->with('user')
            ->latest('tgl_invoice')
            ->latest('id')
            ->limit(10)
            ->get();

        $recentSchools = $this->recentSchools($tid, 10);

        $recentOwners = $this->recentOwners($tid, 5);

        $recentPayments = $this->recentPayments($tid, 5);

        $chartIncome = $this->chartIncome($tid);

        $chartTenant = $this->chartTenantDistribution();

        return view('tenant.dashboard', [
            'stats'           => $stats,
            'invoices'        => $invoices,
            'tenants'         => $tenants,
            'ownerPerTenant'  => $ownerPerTenant,
            'currentTenantId' => $tid,
            'currentTenant'   => $request->attributes->get('current_tenant'),
            'recentSchools'   => $recentSchools,
            'recentOwners'    => $recentOwners,
            'recentPayments'  => $recentPayments,
            'chartIncome'     => $chartIncome,
            'chartTenant'     => $chartTenant,
        ]);
    }

    private function currentTenantId(Request $request): ?string
    {
        return $request->attributes->get('current_tenant_id');
    }

    private function tenantStats(?string $tid): array
    {
        $base = Tenant::query();
        if ($tid) {
            $base->where('id', $tid);
        }

        $new = (clone $base)->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $active = (clone $base)->has('domains')->count();

        return [
            'new'    => (int) $new,
            'active' => (int) $active,
        ];
    }

    private function recentSchools(?string $tid, int $limit)
    {
        $base = Tenant::query()->with('domains');
        if ($tid) {
            $base->where('id', $tid);
        }

        return $base->orderByDesc('created_at')->limit($limit)->get()->map(function ($t) {
            $admin = $t->domains->firstWhere('type', 'admin');
            $landing = $t->domains->firstWhere('type', 'landing');
            return (object) [
                'id'           => $t->id,
                'nama'         => $t->nama_sekolah ?? $t->id,
                'email'        => $t->email,
                'domain_admin' => $admin->domain ?? null,
                'domain_landing' => $landing->domain ?? null,
                'created_at'   => $t->created_at,
            ];
        });
    }

    private function recentOwners(?string $tid, int $limit)
    {
        $base = TenantAdminUser::query();
        if ($tid) {
            $base->where('tenant_id', $tid);
        }

        return $base->orderByDesc('id')->limit($limit)->get()->map(function ($u) {
            $tenant = $u->tenant_id ? Tenant::find($u->tenant_id) : null;
            return (object) [
                'id'        => $u->id,
                'nama'      => $u->nama_lengkap,
                'email'     => $u->email,
                'tenant_id' => $u->tenant_id,
                'tenant'    => $tenant?->nama_sekolah ?? $u->tenant_id,
                'created_at' => $u->created_at,
            ];
        });
    }

    private function recentPayments(?string $tid, int $limit)
    {
        $base = TenantTransaksi::query()->with('invoice');
        if ($tid) {
            $base->where('tenant_id', $tid);
        }

        return $base->orderByDesc('tgl_transaksi')->orderByDesc('idt')->limit($limit)->get()->map(function ($t) {
            $tenant = $t->tenant_id ? Tenant::find($t->tenant_id) : null;
            return (object) [
                'id'         => $t->idt,
                'idv'        => $t->idv,
                'tanggal'    => $t->tgl_transaksi,
                'jumlah'     => $t->jumlah,
                'keterangan' => $t->keterangan_transaksi,
                'tenant'     => $tenant?->nama_sekolah ?? $t->tenant_id,
                'jenis'      => $t->invoice?->jenis_pembayaran,
                'status'     => $t->invoice?->status,
            ];
        });
    }

    private function chartIncome(?string $tid): array
    {
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $d = Carbon::now()->subMonths($i);
            $months->push([
                'key'   => $d->format('Y-m'),
                'label' => $d->translatedFormat('M'),
                'start' => $d->copy()->startOfMonth()->toDateString(),
                'end'   => $d->copy()->endOfMonth()->toDateString(),
            ]);
        }

        $base = TenantInvoice::query()->where('status', 'paid');
        if ($tid) {
            $base->where('tenant_id', $tid);
        }

        $rows = $base
            ->selectRaw('DATE_FORMAT(tgl_invoice, "%Y-%m") as ym, COALESCE(SUM(jumlah), 0) as total')
            ->where('tgl_invoice', '>=', Carbon::now()->subMonths(5)->startOfMonth()->toDateString())
            ->groupBy('ym')
            ->pluck('total', 'ym');

        $labels = [];
        $values = [];
        foreach ($months as $m) {
            $labels[] = $m['label'];
            $values[] = (float) ($rows[$m['key']] ?? 0);
        }

        $max = max($values ?: [0]);
        return [
            'labels' => $labels,
            'values' => $values,
            'max'    => $max > 0 ? $max : 1,
        ];
    }

    private function chartTenantDistribution(): array
    {
        $tenants = Tenant::query()->with('domains')->get();
        $withAdmin = $tenants->filter(fn ($t) => $t->domains->firstWhere('type', 'admin'))->count();
        $withLanding = $tenants->filter(fn ($t) => $t->domains->firstWhere('type', 'landing'))->count();
        $new = $tenants->where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $total = max($tenants->count(), 1);

        return [
            'labels'  => ['Aktif', 'Baru (30 hari)', 'Lainnya'],
            'values'  => [
                $withAdmin,
                $new,
                max($total - $withAdmin - $new, 0),
            ],
            'total'   => $tenants->count(),
        ];
    }
}
