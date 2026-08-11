<?php

namespace App\Http\Controllers;


use App\Models\JenisPembayaran;
use App\Models\Rekening;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JenisPembayaranController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = JenisPembayaran::query();
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-inline-flex gap-1">
                            <a href="'.route('app.jenis-pembayaran.edit', $row->id).'" class="btn btn-warning">
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
        return view('jenis_pembayaran.index', ['title' => 'Jenis Pembayaran']);
    }

    public function create()
    {
        $rekening = Rekening::where('kode_akun', 'like', '4.1.%')
            ->orderBy('kode_akun')
            ->get();
        return view('jenis_pembayaran.create', [
            'title'    => 'Tambah Jenis Pembayaran',
            'rekening' => $rekening,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama'      => 'required|unique:jenis_pembayaran,nama',
            'kode_akun' => 'required|exists:rekening,kode_akun',
        ]);

        JenisPembayaran::create($data);

        return response()->json(['success' => true, 'msg' => 'Jenis pembayaran berhasil ditambahkan']);
    }

    public function edit(JenisPembayaran $jenis_pembayaran)
    {
        $rekening = Rekening::where('kode_akun', 'like', '4.1.%')
            ->orderBy('kode_akun')
            ->get();
        return view('jenis_pembayaran.edit', [
            'title'            => 'Edit Jenis Pembayaran',
            'jenisPembayaran'  => $jenis_pembayaran,
            'rekening'         => $rekening,
        ]);
    }

    public function update(Request $request, JenisPembayaran $jenis_pembayaran)
    {
        $data = $request->validate([
            'nama'      => 'required|unique:jenis_pembayaran,nama,'.$jenis_pembayaran->id,
            'kode_akun' => 'required|exists:rekening,kode_akun',
        ]);

        $jenis_pembayaran->update($data);

        return response()->json(['success' => true, 'msg' => 'Jenis pembayaran berhasil diupdate']);
    }

    public function destroy(JenisPembayaran $jenis_pembayaran)
    {
        $jenis_pembayaran->delete();
        return response()->json(['success' => true, 'msg' => 'Jenis pembayaran berhasil dihapus']);
    }
}
