<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Kelas;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use App\Models\Tenant;

class KelasPusatController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = Kelas::with('kurikulum', 'kurikulumByName')->orderBy('kode_kelas')->get();
            $kurikulums = Kurikulum::orderBy('nama_kurikulum')->get();
            return view('tenant.tenant.kelas', [
                'tenant'     => $tenant,
                'items'      => $items,
                'kurikulums' => $kurikulums,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'kode_kelas'     => ['required', 'string', 'max:50'],
                'nama_kelas'     => ['required', 'string', 'max:120'],
                'tingkat'        => ['required', 'string', 'max:20'],
                'kode_kurikulum' => ['required'],
            ]);

            Kelas::create($data);

            return redirect()->route('tenant.tenant.kelas.index', $tenant)
                ->with('success', 'Kelas ditambah');
        });
    }

    public function update(Request $request, Tenant $tenant, $kelas)
    {
        return $this->runInTenant($tenant, function () use ($request, $kelas, $tenant) {
            $data = $request->validate([
                'kode_kelas'     => ['required', 'string', 'max:50'],
                'nama_kelas'     => ['required', 'string', 'max:120'],
                'tingkat'        => ['required', 'string', 'max:20'],
                'kode_kurikulum' => ['required'],
            ]);

            $k = Kelas::findOrFail($kelas);
            $k->update($data);

            return redirect()->route('tenant.tenant.kelas.index', $tenant)
                ->with('success', 'Kelas ' . $k->kode_kelas . ' diperbarui');
        });
    }

    public function destroy(Tenant $tenant, $kelas)
    {
        return $this->runInTenant($tenant, function () use ($kelas, $tenant) {
            $k = Kelas::findOrFail($kelas);
            $kode = $k->kode_kelas;
            $k->delete();

            return redirect()->route('tenant.tenant.kelas.index', $tenant)
                ->with('success', "Kelas {$kode} dihapus");
        });
    }
}
