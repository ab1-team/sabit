<?php

namespace App\Http\Controllers\Tenant;

use App\Models\Rekening;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Tenant;

class CoaController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $rekenings = Rekening::with('akunLevel3')
                ->orderBy('kode_akun')
                ->get();

            return view('tenant.tenant.coa', [
                'tenant'    => $tenant,
                'rekenings' => $rekenings,
            ]);
        });
    }

    public function nonaktifkan(Tenant $tenant, Rekening $rekening)
    {
        return $this->runInTenant($tenant, function () use ($rekening, $tenant) {
            $rekening->tgl_nonaktif = now()->toDateString();
            $rekening->save();

            return redirect()->route('tenant.tenant.coa.index', $tenant)
                ->with('success', "Rekening {$rekening->kode_akun} dinonaktifkan");
        });
    }

    public function aktifkan(Tenant $tenant, Rekening $rekening)
    {
        return $this->runInTenant($tenant, function () use ($rekening, $tenant) {
            $rekening->tgl_nonaktif = null;
            $rekening->save();

            return redirect()->route('tenant.tenant.coa.index', $tenant)
                ->with('success', "Rekening {$rekening->kode_akun} diaktifkan");
        });
    }
}
