<?php

namespace App\Http\Controllers;


use App\Models\Kurikulum;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class KurikulumController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Kurikulum::query();
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-inline-flex gap-1">
                            <a href="'.route('app.kurikulum.edit', $row->id).'" class="btn btn-warning">
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
        return view('kurikulum.index', ['title' => 'Data Kurikulum']);
    }

    public function create()
    {
        return view('kurikulum.create', ['title' => 'Tambah Kurikulum']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_kurikulum' => 'required|unique:kurikulum,nama_kurikulum',
            'status'         => 'required|in:aktif,nonaktif',
        ]);

        Kurikulum::create($data);

        return response()->json(['success' => true, 'msg' => 'Kurikulum berhasil ditambahkan']);
    }

    public function edit(Kurikulum $kurikulum)
    {
        return view('kurikulum.edit', ['title' => 'Edit Kurikulum', 'kurikulum' => $kurikulum]);
    }

    public function update(Request $request, Kurikulum $kurikulum)
    {
        $data = $request->validate([
            'nama_kurikulum' => 'required|unique:kurikulum,nama_kurikulum,'.$kurikulum->id,
            'status'         => 'required|in:aktif,nonaktif',
        ]);

        $kurikulum->update($data);

        return response()->json(['success' => true, 'msg' => 'Kurikulum berhasil diupdate']);
    }

    public function destroy(Kurikulum $kurikulum)
    {
        $kurikulum->delete();
        return response()->json(['success' => true, 'msg' => 'Kurikulum berhasil dihapus']);
    }
}
