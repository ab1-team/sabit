<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Tenant\BaseSchoolController;
use App\Models\Jabatan;
use Illuminate\Http\Request;
use App\Models\Tenant;
use Yajra\DataTables\Facades\DataTables;

class JabatanPusatController extends BaseSchoolController
{
    public function index(Tenant $tenant, Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return $this->runInTenant($tenant, function () use ($request, $tenant) {
                $data = Jabatan::withCount('users');

                return DataTables::eloquent($data)
                    ->addIndexColumn()
                    ->addColumn('users_count_badge', function ($row) {
                        return '<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">'.$row->users_count.' user</span>';
                    })
                    ->addColumn('kode_jabatan_text', function ($row) {
                        return $row->kode_jabatan ?? '—';
                    })
                    ->addColumn('action', function ($row) use ($tenant) {
                        $update = route('tenant.tenant.jabatan.update', [$tenant, $row->id]);
                        $delete = route('tenant.tenant.jabatan.destroy', [$tenant, $row->id]);

                        return '
                            <div class="inline-flex gap-1">
                                <button type="button" class="open-edit-modal inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200" data-update-url="'.$update.'" data-nama="'.e($row->nama_jabatan).'" data-kode="'.e($row->kode_jabatan ?? '').'">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" class="delete-jabatan inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-200" data-action="'.$delete.'" data-name="'.e($row->nama_jabatan).'">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.48 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        ';
                    })
                    ->rawColumns(['users_count_badge', 'action'])
                    ->toJson();
            });
        }

        return $this->runInTenant($tenant, function () use ($tenant) {
            $jabatanItem = new \App\Models\Jabatan();
            return view('tenant.tenant.jabatan', [
                'tenant' => $tenant,
                'jabatanItem' => $jabatanItem,
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
