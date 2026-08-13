<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;

use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use App\Models\Tenant;

class TahunAkademikController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = TahunAkademik::orderByDesc('id')->get();

            return view('tenant.tenant.tahun-akademik', [
                'tenant' => $tenant,
                'items'  => $items,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'nama_tahun' => ['required', 'string', 'max:30'],
                'keterangan' => ['nullable', 'string', 'max:191'],
                'status'     => ['required', 'in:aktif,nonaktif'],
            ]);

            $ta = TahunAkademik::create($data);

            if ($data['status'] === 'aktif') {
                $ta->aktifkan();
            }

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$ta->nama_tahun} ditambah");
        });
    }

    public function update(Request $request, Tenant $tenant, $tahunAkademik)
    {
        return $this->runInTenant($tenant, function () use ($request, $tahunAkademik, $tenant) {
            $ta = TahunAkademik::findOrFail($tahunAkademik);
            $data = $request->validate([
                'nama_tahun' => ['required', 'string', 'max:30'],
                'keterangan' => ['nullable', 'string', 'max:191'],
                'status'     => ['required', 'in:aktif,nonaktif'],
            ]);

            $ta->update($data);
            if ($data['status'] === 'aktif') {
                $ta->aktifkan();
            }

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$ta->nama_tahun} diperbarui");
        });
    }

    public function destroy(Tenant $tenant, $tahunAkademik)
    {
        return $this->runInTenant($tenant, function () use ($tahunAkademik, $tenant) {
            $ta = TahunAkademik::findOrFail($tahunAkademik);
            $nama = $ta->nama_tahun;
            $ta->delete();

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$nama} dihapus");
        });
    }

    public function aktifkan(Tenant $tenant, $tahunAkademik)
    {
        return $this->runInTenant($tenant, function () use ($tahunAkademik, $tenant) {
            $ta = TahunAkademik::findOrFail($tahunAkademik);
            $ta->aktifkan();

            return redirect()->route('tenant.tenant.tahun-akademik.index', $tenant)
                ->with('success', "Tahun akademik {$ta->nama_tahun} diaktifkan");
        });
    }
}



