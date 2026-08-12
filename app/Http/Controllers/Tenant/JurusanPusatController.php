<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Jurusan;
use Illuminate\Http\Request;
use App\Models\Tenant;

class JurusanPusatController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = Jurusan::orderBy('nama')->get();
            return view('tenant.tenant.jurusan', [
                'tenant' => $tenant,
                'items'  => $items,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'nama'         => ['required', 'string', 'max:120'],
                'kode_jurusan' => ['required', 'string', 'max:50'],
                'status'       => ['required', 'in:aktif,nonaktif'],
            ]);

            Jurusan::create($data);

            return redirect()->route('tenant.tenant.jurusan.index', $tenant)
                ->with('success', "Jurusan {$data['nama']} ditambah");
        });
    }

    public function update(Request $request, Tenant $tenant, $jurusan)
    {
        return $this->runInTenant($tenant, function () use ($request, $jurusan, $tenant) {
            $data = $request->validate([
                'nama'         => ['required', 'string', 'max:120'],
                'kode_jurusan' => ['required', 'string', 'max:50'],
                'status'       => ['required', 'in:aktif,nonaktif'],
            ]);

            $j = Jurusan::findOrFail($jurusan);
            $j->update($data);

            return redirect()->route('tenant.tenant.jurusan.index', $tenant)
                ->with('success', "Jurusan {$j->nama} diperbarui");
        });
    }

    public function destroy(Tenant $tenant, $jurusan)
    {
        return $this->runInTenant($tenant, function () use ($jurusan, $tenant) {
            $j = Jurusan::findOrFail($jurusan);
            $nama = $j->nama;
            $j->delete();

            return redirect()->route('tenant.tenant.jurusan.index', $tenant)
                ->with('success', "Jurusan {$nama} dihapus");
        });
    }
}
