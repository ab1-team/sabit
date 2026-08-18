<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;

use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Yajra\DataTables\Facades\DataTables;

class TahunAkademikController extends BaseSchoolController
{
    public function index(Tenant $tenant, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->runInTenant($tenant, function () use ($request, $tenant) {
                $data = TahunAkademik::query();

                return DataTables::eloquent($data)
                    ->addIndexColumn()
                    ->addColumn('status_badge', function ($row) {
                        if ($row->status === 'aktif') {
                            return '<span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Aktif</span>';
                        }
                        return '<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Nonaktif</span>';
                    })
                    ->addColumn('keterangan_text', function ($row) {
                        return $row->keterangan ?? '—';
                    })
                    ->addColumn('action', function ($row) use ($tenant) {
                        $update = route('tenant.tenant.tahun-akademik.update', [$tenant, $row->id]);
                        $delete = route('tenant.tenant.tahun-akademik.destroy', [$tenant, $row->id]);
                        $activate = route('tenant.tenant.tahun-akademik.aktifkan', [$tenant, $row->id]);

                        $activateBtn = '';
                        if ($row->status !== 'aktif') {
                            $activateBtn = '<button type="button" class="activate-ta inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200" data-action="'.$activate.'" data-name="'.e($row->nama_tahun).'" title="Aktifkan"><svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></button>';
                        }

                        return '
                            <div class="inline-flex gap-1">
                                '.$activateBtn.'
                                <button type="button" class="open-edit-modal inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200" data-update-url="'.$update.'" data-nama="'.e($row->nama_tahun).'" data-ket="'.e($row->keterangan ?? '').'" data-status="'.e($row->status).'">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" class="delete-ta inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-200" data-action="'.$delete.'" data-name="'.e($row->nama_tahun).'">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.48 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        ';
                    })
                    ->rawColumns(['status_badge', 'action'])
                    ->toJson();
            });
        }

        return $this->runInTenant($tenant, function () use ($tenant) {
            $tahunAkademikItem = new \App\Models\TahunAkademik();
            return view('tenant.tenant.tahun-akademik', [
                'tenant' => $tenant,
                'tahunAkademikItem' => $tahunAkademikItem,
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
