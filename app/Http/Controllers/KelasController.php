<?php

namespace App\Http\Controllers;


use App\Models\Kelas;
use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kelas::query();
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('nama_kurikulum', function ($row) {
                    return $row->kurikulumResolve?->nama_kurikulum ?? '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-inline-flex gap-1">
                            <a href="'.route('app.kelas.edit', $row->id).'" class="btn btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button class="btn btn-danger btnDelete" data-id="'.$row->id.'">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['action'])
                ->toJson();
        }
        return view('kelas.index', ['title' => 'Data Kelas']);
    }

    public function create()
    {
        $kurikulum = Kurikulum::where('status', 'aktif')->orderBy('nama_kurikulum')->get();
        return view('kelas.create', ['title' => 'Tambah Kelas', 'kurikulum' => $kurikulum]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_kelas'     => 'required|unique:kelas,kode_kelas',
            'nama_kelas'     => 'required',
            'tingkat'        => 'required',
            'kode_kurikulum' => 'required',
        ]);

        Kelas::create($data);

        return response()->json(['success' => true, 'msg' => 'Kelas berhasil ditambahkan']);
    }

    public function edit(Kelas $kelas)
    {
        $kurikulum = Kurikulum::orderBy('nama_kurikulum')->get();
        return view('kelas.edit', [
            'title'      => 'Edit Kelas',
            'kelas'      => $kelas,
            'kurikulum'  => $kurikulum,
            'selectedId' => is_numeric($kelas->kode_kurikulum) ? (int) $kelas->kode_kurikulum : null,
            'selectedName' => is_numeric($kelas->kode_kurikulum) ? null : $kelas->kode_kurikulum,
        ]);
    }

    public function update(Request $request, Kelas $kelas)
    {
        $data = $request->validate([
            'kode_kelas'     => 'required|unique:kelas,kode_kelas,'.$kelas->id,
            'nama_kelas'     => 'required',
            'tingkat'        => 'required',
            'kode_kurikulum' => 'required',
        ]);

        $kelas->update($data);

        return response()->json(['success' => true, 'msg' => 'Kelas berhasil diupdate']);
    }

    public function destroy(Kelas $kelas)
    {
        $kelas->delete();
        return response()->json(['success' => true, 'msg' => 'Kelas berhasil dihapus']);
    }
}
