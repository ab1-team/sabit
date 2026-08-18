<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Kelas;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Yajra\DataTables\Facades\DataTables;

class KelasPusatController extends BaseSchoolController
{
    public function index(Tenant $tenant, Request $request)
    {
        if ($request->ajax()) {
            return $this->runInTenant($tenant, function () use ($request, $tenant) {
                $data = Kelas::with('kurikulum')->select('kelas.*');

                return DataTables::eloquent($data)
                    ->addIndexColumn()
                    ->addColumn('kurikulum_nama', function ($row) {
                        return $row->kurikulum?->nama_kurikulum ?? '—';
                    })
                    ->addColumn('action', function ($row) use ($tenant) {
                        $update = route('tenant.tenant.kelas.update', [$tenant, $row->id]);
                        $delete = route('tenant.tenant.kelas.destroy', [$tenant, $row->id]);

                        return '
                            <div class="inline-flex gap-1">
                                <button type="button" class="open-edit-modal inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200" data-update-url="'.$update.'" data-kode="'.e($row->kode_kelas).'" data-nama="'.e($row->nama_kelas).'" data-tingkat="'.e($row->tingkat).'" data-kurikulum="'.e($row->kode_kurikulum).'">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" class="delete-kelas inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-200" data-action="'.$delete.'" data-name="'.e($row->kode_kelas).'">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.48 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        ';
                    })
                    ->filterColumn('kurikulum_nama', function ($q, $kw) {
                        $q->whereHas('kurikulum', fn ($qq) => $qq->where('nama_kurikulum', 'like', "%{$kw}%"));
                    })
                    ->rawColumns(['action'])
                    ->toJson();
            });
        }

        return $this->runInTenant($tenant, function () use ($tenant) {
            $kurikulums = Kurikulum::orderBy('nama_kurikulum')->get();
            return view('tenant.tenant.kelas', [
                'tenant'     => $tenant,
                'kurikulums' => $kurikulums,
            ]);
        });
    }

    public function data(Tenant $tenant, Request $request)
    {
        return $this->index($tenant, $request);
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
