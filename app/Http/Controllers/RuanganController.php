<?php

namespace App\Http\Controllers;


use App\Models\Ruangan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class RuanganController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Ruangan::query();
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-inline-flex gap-1">
                            <a href="'.route('app.ruangan.edit', $row->id).'" class="btn btn-warning">
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
        return view('ruangan.index', ['title' => 'Data Ruangan']);
    }

    public function create()
    {
        return view('ruangan.create', ['title' => 'Tambah Ruangan']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_gedung'       => 'required',
            'kode_ruangan'      => 'required|unique:ruangan,kode_ruangan',
            'nama_ruangan'      => 'required',
            'kapasitas_belajar' => 'nullable',
            'kapasitas_ujian'   => 'nullable',
            'keterangan'        => 'nullable',
            'status'            => 'required|in:aktif,non_aktif',
        ]);

        Ruangan::create($data);

        return response()->json(['success' => true, 'msg' => 'Ruangan berhasil ditambahkan']);
    }

    public function edit(Ruangan $ruangan)
    {
        return view('ruangan.edit', ['title' => 'Edit Ruangan', 'ruangan' => $ruangan]);
    }

    public function update(Request $request, Ruangan $ruangan)
    {
        $data = $request->validate([
            'kode_gedung'       => 'required',
            'kode_ruangan'      => 'required|unique:ruangan,kode_ruangan,'.$ruangan->id,
            'nama_ruangan'      => 'required',
            'kapasitas_belajar' => 'nullable',
            'kapasitas_ujian'   => 'nullable',
            'keterangan'        => 'nullable',
            'status'            => 'required|in:aktif,non_aktif',
        ]);

        $ruangan->update($data);

        return response()->json(['success' => true, 'msg' => 'Ruangan berhasil diupdate']);
    }

    public function destroy(Ruangan $ruangan)
    {
        $ruangan->delete();
        return response()->json(['success' => true, 'msg' => 'Ruangan berhasil dihapus']);
    }
}
