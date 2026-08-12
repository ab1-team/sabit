<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use App\Models\Tenant;

class KurikulumPusatController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = Kurikulum::orderBy('nama_kurikulum')->get();
            return view('tenant.tenant.kurikulum', [
                'tenant' => $tenant,
                'items'  => $items,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'nama_kurikulum' => ['required', 'string', 'max:191'],
                'kode_kurikulum' => ['nullable', 'string', 'max:50'],
                'status'         => ['required', 'in:aktif,nonaktif'],
            ]);

            Kurikulum::create($data);

            return redirect()->route('tenant.tenant.kurikulum.index', $tenant)
                ->with('success', "Kurikulum {$data['nama_kurikulum']} ditambah");
        });
    }

    public function update(Request $request, Tenant $tenant, $kurikulum)
    {
        return $this->runInTenant($tenant, function () use ($request, $kurikulum, $tenant) {
            $data = $request->validate([
                'nama_kurikulum' => ['required', 'string', 'max:191'],
                'kode_kurikulum' => ['nullable', 'string', 'max:50'],
                'status'         => ['required', 'in:aktif,nonaktif'],
            ]);

            $kurikulum = Kurikulum::findOrFail($kurikulum);
            $kurikulum->update($data);

            return redirect()->route('tenant.tenant.kurikulum.index', $tenant)
                ->with('success', "Kurikulum {$kurikulum->nama_kurikulum} diperbarui");
        });
    }

    public function destroy(Tenant $tenant, $kurikulum)
    {
        return $this->runInTenant($tenant, function () use ($kurikulum, $tenant) {
            $k = Kurikulum::findOrFail($kurikulum);
            $nama = $k->nama_kurikulum;
            $k->delete();

            return redirect()->route('tenant.tenant.kurikulum.index', $tenant)
                ->with('success', "Kurikulum {$nama} dihapus");
        });
    }
}
