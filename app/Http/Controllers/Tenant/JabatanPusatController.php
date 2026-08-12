<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use App\Models\Tenant;

class JabatanPusatController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = Jabatan::withCount('users')->orderBy('nama_jabatan')->get();
            return view('tenant.tenant.jabatan', [
                'tenant' => $tenant,
                'items'  => $items,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'nama_jabatan' => ['required', 'string', 'max:100'],
                'kode_jabatan' => ['nullable', 'string', 'max:50'],
            ]);

            Jabatan::create($data);

            return redirect()->route('tenant.tenant.jabatan.index', $tenant)
                ->with('success', "Jabatan {$data['nama_jabatan']} ditambah");
        });
    }

    public function update(Request $request, Tenant $tenant, $jabatan)
    {
        return $this->runInTenant($tenant, function () use ($request, $jabatan, $tenant) {
            $data = $request->validate([
                'nama_jabatan' => ['required', 'string', 'max:100'],
                'kode_jabatan' => ['nullable', 'string', 'max:50'],
            ]);

            $j = Jabatan::findOrFail($jabatan);
            $j->update($data);

            return redirect()->route('tenant.tenant.jabatan.index', $tenant)
                ->with('success', "Jabatan {$j->nama_jabatan} diperbarui");
        });
    }

    public function destroy(Tenant $tenant, $jabatan)
    {
        return $this->runInTenant($tenant, function () use ($jabatan, $tenant) {
            $j = Jabatan::findOrFail($jabatan);
            $nama = $j->nama_jabatan;
            if ($j->users()->exists()) {
                return redirect()->route('tenant.tenant.jabatan.index', $tenant)
                    ->with('error', "Jabatan {$nama} masih dipakai user. Pindahkan dulu user-nya.");
            }
            $j->delete();

            return redirect()->route('tenant.tenant.jabatan.index', $tenant)
                ->with('success', "Jabatan {$nama} dihapus");
        });
    }
}
