<?php

namespace App\Http\Controllers;


use App\Models\TahunAkademik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TahunAkademikController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = TahunAkademik::query();
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('badge', function ($row) {
                    $cls = $row->status === 'aktif' ? 'bg-gradient-success' : 'bg-gradient-secondary';
                    return '<span class="badge '.$cls.'">'.ucfirst($row->status).'</span>';
                })
                ->addColumn('action', function ($row) {
                    $btnAktif = $row->status === 'aktif'
                        ? '<button class="btn btn-success btnAktifkan" data-id="'.$row->id.'" disabled><i class="fa-solid fa-check"></i> Aktif</button>'
                        : '<button class="btn btn-outline-success btnAktifkan" data-id="'.$row->id.'"><i class="fa-solid fa-check"></i> Aktifkan</button>';

                    return '
                        <div class="d-inline-flex gap-1">
                            '.$btnAktif.'
                            <a href="'.route('app.tahun-akademik.edit', $row->id).'" class="btn btn-warning">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button class="btn btn-danger btnDelete" data-id="'.$row->id.'">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->rawColumns(['badge', 'action'])
                ->toJson();
        }
        return view('tahun-akademik.index', ['title' => 'Tahun Akademik']);
    }

    public function create()
    {
        return view('tahun-akademik.create', ['title' => 'Tambah Tahun Akademik']);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_tahun' => 'required|unique:tahun_akademik,nama_tahun',
            'keterangan' => 'nullable',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($data) {
            if ($data['status'] === 'aktif') {
                TahunAkademik::where('status', 'aktif')->update(['status' => 'nonaktif']);
            }
            TahunAkademik::create($data);
        });

        return response()->json(['success' => true, 'msg' => 'Tahun akademik berhasil ditambahkan']);
    }

    public function edit(TahunAkademik $tahun_akademik)
    {
        return view('tahun-akademik.edit', [
            'title' => 'Edit Tahun Akademik',
            'tahun' => $tahun_akademik,
        ]);
    }

    public function update(Request $request, TahunAkademik $tahun_akademik)
    {
        $data = $request->validate([
            'nama_tahun' => 'required|unique:tahun_akademik,nama_tahun,'.$tahun_akademik->id,
            'keterangan' => 'nullable',
            'status'     => 'required|in:aktif,nonaktif',
        ]);

        DB::transaction(function () use ($data, $tahun_akademik) {
            if ($data['status'] === 'aktif') {
                TahunAkademik::where('status', 'aktif')
                    ->where('id', '!=', $tahun_akademik->id)
                    ->update(['status' => 'nonaktif']);
            }
            $tahun_akademik->update($data);
        });

        return response()->json(['success' => true, 'msg' => 'Tahun akademik berhasil diupdate']);
    }

    public function destroy(TahunAkademik $tahun_akademik)
    {
        $tahun_akademik->delete();

        return response()->json(['success' => true, 'msg' => 'Tahun akademik berhasil dihapus']);
    }

    public function aktifkan(TahunAkademik $tahun_akademik)
    {
        DB::transaction(function () use ($tahun_akademik) {
            TahunAkademik::where('status', 'aktif')->update(['status' => 'nonaktif']);
            $tahun_akademik->update(['status' => 'aktif']);
        });

        return response()->json(['success' => true, 'msg' => 'Tahun akademik diaktifkan']);
    }
}