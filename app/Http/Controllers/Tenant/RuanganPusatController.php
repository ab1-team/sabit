<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Yajra\DataTables\Facades\DataTables;

class RuanganPusatController extends BaseSchoolController
{
    public function index(Tenant $tenant, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->runInTenant($tenant, function () use ($request, $tenant) {
                $data = Ruangan::query();

                return DataTables::eloquent($data)
                    ->addIndexColumn()
                    ->addColumn('kapasitas_text', function ($row) {
                        return '<span class="text-xs text-slate-500">Bljr:</span> '.($row->kapasitas_belajar ?? '—').' <span class="text-xs text-slate-400">·</span> <span class="text-xs text-slate-500">Ujn:</span> '.($row->kapasitas_ujian ?? '—');
                    })
                    ->addColumn('status_badge', function ($row) {
                        if ($row->status === 'aktif') {
                            return '<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Aktif</span>';
                        }
                        return '<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Nonaktif</span>';
                    })
                    ->addColumn('action', function ($row) use ($tenant) {
                        $update = route('tenant.tenant.ruangan.update', [$tenant, $row->id]);
                        $delete = route('tenant.tenant.ruangan.destroy', [$tenant, $row->id]);

                        return '
                            <div class="inline-flex gap-1">
                                <button type="button" class="open-edit-modal inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200" data-update-url="'.$update.'" data-gedung="'.e($row->kode_gedung).'" data-kode="'.e($row->kode_ruangan).'" data-nama="'.e($row->nama_ruangan).'" data-kb="'.e($row->kapasitas_belajar ?? '').'" data-ku="'.e($row->kapasitas_ujian ?? '').'" data-ket="'.e($row->keterangan ?? '').'" data-status="'.e($row->status).'">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" class="delete-ruangan inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-200" data-action="'.$delete.'" data-name="'.e($row->kode_ruangan).'">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.48 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        ';
                    })
                    ->rawColumns(['kapasitas_text', 'status_badge', 'action'])
                    ->toJson();
            });
        }

        return $this->runInTenant($tenant, function () use ($tenant) {
            $ruanganItem = new \App\Models\Ruangan();
            return view('tenant.tenant.ruangan', [
                'tenant' => $tenant,
                'ruanganItem' => $ruanganItem,
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
