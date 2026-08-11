<?php

namespace App\Http\Controllers;


use App\Models\Jurusan;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Jurusan::query();
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-inline-flex gap-1">
                            <a href="'.route('app.jurusan.edit', $row->id).'" class="btn btn-warning">
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
        return view('jurusan.index', ['title' => 'Data Jurusan']);
    }

    public function create()
    {
        return view('jurusan.create', ['title' => 'Tambah Jurusan']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_jurusan' => 'required|unique:jurusan,kode_jurusan',
            'nama'         => 'required',
            'status'       => 'required|in:aktif,nonaktif',
        ]);

        Jurusan::create($data);

        return response()->json(['success' => true, 'msg' => 'Jurusan berhasil ditambahkan']);
    }

    public function edit(Jurusan $jurusan)
    {
        return view('jurusan.edit', ['title' => 'Edit Jurusan', 'jurusan' => $jurusan]);
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $data = $request->validate([
            'kode_jurusan' => 'required|unique:jurusan,kode_jurusan,'.$jurusan->id,
            'nama'         => 'required',
            'status'       => 'required|in:aktif,nonaktif',
        ]);

        $jurusan->update($data);

        return response()->json(['success' => true, 'msg' => 'Jurusan berhasil diupdate']);
    }

    public function destroy(Jurusan $jurusan)
    {
        $jurusan->delete();
        return response()->json(['success' => true, 'msg' => 'Jurusan berhasil dihapus']);
    }
}
