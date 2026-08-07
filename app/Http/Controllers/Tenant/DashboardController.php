<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AdminInvoice;
use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $tid = $this->currentTenantId($request);
        $tenants = $request->attributes->get('tenants', []);

        $invoiceBase = AdminInvoice::query();
        if ($tid) {
            $invoiceBase->where('tenant_id', $tid);
        }

        $summary = (clone $invoiceBase)
            ->selectRaw("COUNT(*) as total, SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid, SUM(CASE WHEN status = 'unpaid' THEN 1 ELSE 0 END) as unpaid, COALESCE(SUM(jumlah), 0) as total_amount")
            ->first();

        $ownerBase = AdminUser::query();
        if ($tid) {
            $ownerBase->where('tenant_id', $tid);
        }
        $ownerScopedCount = (clone $ownerBase)->count();

        $ownerPerTenant = AdminUser::query()
            ->select('tenant_id', DB::raw('COUNT(*) as total'))
            ->groupBy('tenant_id')
            ->pluck('total', 'tenant_id');

        $stats = [
            'invoice_total' => (int) ($summary->total ?? 0),
            'invoice_paid' => (int) ($summary->paid ?? 0),
            'invoice_open' => (int) ($summary->unpaid ?? 0),
            'nominal_total' => (float) ($summary->total_amount ?? 0),
            'owner_count' => $tid ? (int) $ownerScopedCount : (int) AdminUser::query()->distinct()->count('id'),
            'owner_total' => (int) AdminUser::query()->distinct()->count('id'),
            'tenant_total' => count($tenants ?? []),
        ];

        $invoices = (clone $invoiceBase)
            ->with('user')
            ->latest('tgl_invoice')
            ->latest('id')
            ->limit(5)
            ->get();

        return view('tenant.dashboard', [
            'stats' => $stats,
            'invoices' => $invoices,
            'tenants' => $tenants,
            'ownerPerTenant' => $ownerPerTenant,
            'currentTenantId' => $tid,
            'currentTenant' => $request->attributes->get('current_tenant'),
        ]);
    }

    private function currentTenantId(Request $request): ?string
    {
        return $request->attributes->get('current_tenant_id');
    }
}
