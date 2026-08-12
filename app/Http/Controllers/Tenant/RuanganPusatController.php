<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use App\Models\Tenant;

class RuanganPusatController extends BaseSchoolController
{
    public function index(Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($tenant) {
            $items = Ruangan::orderBy('kode_ruangan')->get();
            return view('tenant.tenant.ruangan', [
                'tenant' => $tenant,
                'items'  => $items,
            ]);
        });
    }

    public function store(Request $request, Tenant $tenant)
    {
        return $this->runInTenant($tenant, function () use ($request, $tenant) {
            $data = $request->validate([
                'kode_gedung'       => ['required', 'string', 'max:50'],
                'kode_ruangan'      => ['required', 'string', 'max:50'],
                'nama_ruangan'      => ['required', 'string', 'max:120'],
                'kapasitas_belajar' => ['nullable', 'integer', 'min:0'],
                'kapasitas_ujian'   => ['nullable', 'integer', 'min:0'],
                'keterangan'        => ['nullable', 'string'],
                'status'            => ['required', 'in:aktif,non_aktif'],
            ]);

            Ruangan::create($data);

            return redirect()->route('tenant.tenant.ruangan.index', $tenant)
                ->with('success', "Ruangan {$data['kode_ruangan']} ditambah");
        });
    }

    public function update(Request $request, Tenant $tenant, $ruangan)
    {
        return $this->runInTenant($tenant, function () use ($request, $ruangan, $tenant) {
            $data = $request->validate([
                'kode_gedung'       => ['required', 'string', 'max:50'],
                'kode_ruangan'      => ['required', 'string', 'max:50'],
                'nama_ruangan'      => ['required', 'string', 'max:120'],
                'kapasitas_belajar' => ['nullable', 'integer', 'min:0'],
                'kapasitas_ujian'   => ['nullable', 'integer', 'min:0'],
                'keterangan'        => ['nullable', 'string'],
                'status'            => ['required', 'in:aktif,non_aktif'],
            ]);

            $r = Ruangan::findOrFail($ruangan);
            $r->update($data);

            return redirect()->route('tenant.tenant.ruangan.index', $tenant)
                ->with('success', "Ruangan {$r->kode_ruangan} diperbarui");
        });
    }

    public function destroy(Tenant $tenant, $ruangan)
    {
        return $this->runInTenant($tenant, function () use ($ruangan, $tenant) {
            $r = Ruangan::findOrFail($ruangan);
            $kode = $r->kode_ruangan;
            $r->delete();

            return redirect()->route('tenant.tenant.ruangan.index', $tenant)
                ->with('success', "Ruangan {$kode} dihapus");
        });
    }
}
