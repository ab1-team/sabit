<?php

namespace App\Http\Controllers;

use App\Models\Jenis_Biaya;
use App\Models\JenisPembayaran;
use App\Models\Tahun_Akademik;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class JenisBiayaController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Jenis_Biaya::with('get_jenis_pembayaran')->select('jenis_biaya.*');
            return DataTables::eloquent($data)
                ->addIndexColumn()
                ->addColumn('nama_jenis', function ($row) {
                    return $row->get_jenis_pembayaran->nama ?? '-';
                })
                ->addColumn('kode_akun', function ($row) {
                    return $row->get_jenis_pembayaran->kode_akun ?? '-';
                })
                ->editColumn('total_beban', function ($row) {
                    return \App\Utils\Angka::format($row->total_beban, 2);
                })
                ->orderColumn('nama_jenis', 'jenis_biaya.id_jp $1')
                ->orderColumn('kode_akun', 'jenis_biaya.id_jp $1')
                ->filterColumn('nama_jenis', function ($q, $kw) {
                    $q->whereHas('get_jenis_pembayaran', fn($qq) => $qq->where('nama', 'like', "%{$kw}%"));
                })
                ->filterColumn('kode_akun', function ($q, $kw) {
                    $q->whereHas('get_jenis_pembayaran', fn($qq) => $qq->where('kode_akun', 'like', "%{$kw}%"));
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-inline-flex gap-1">
                            <button class="btn btn-warning btn-compact btnEdit" data-id="'.$row->id.'">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            <button class="btn btn-danger btn-compact btnDelete" data-id="'.$row->id.'">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    ';
                })
                ->toJson();
        }
        return view('jenis-biaya.index', ['title' => 'Jenis Keuangan']);
    }

    public function create()
    {
        $jenisPembayaran = JenisPembayaran::orderBy('nama')->get();
        $tahunAkademiks  = Tahun_Akademik::orderBy('nama_tahun', 'desc')->get();
        $title = 'Tambah Nominal Keuangan';

        return view('jenis-biaya.create', compact('title', 'jenisPembayaran', 'tahunAkademiks'));
    }

    public function createForm()
    {
        $jenisPembayaran = JenisPembayaran::orderBy('nama')->get();
        $tahunAkademiks  = Tahun_Akademik::orderBy('nama_tahun', 'desc')->get();
        return response()->json([
            'html' => view('jenis-biaya._form', [
                'mode'            => 'create',
                'jenisPembayaran' => $jenisPembayaran,
                'tahunAkademiks'  => $tahunAkademiks,
                'jenis_biaya'     => new Jenis_Biaya(),
            ])->render(),
        ]);
    }

    public function editForm(Jenis_Biaya $jenis_biaya)
    {
        $jenis_biaya->load('get_jenis_pembayaran');
        $jenisPembayaran = JenisPembayaran::orderBy('nama')->get();
        $tahunAkademiks  = Tahun_Akademik::orderBy('nama_tahun', 'desc')->get();
        return response()->json([
            'html' => view('jenis-biaya._form', [
                'mode'            => 'edit',
                'jenisPembayaran' => $jenisPembayaran,
                'tahunAkademiks'  => $tahunAkademiks,
                'jenis_biaya'     => $jenis_biaya,
            ])->render(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'total_beban' => 'required|numeric|min:0',
            'angkatan'    => 'required',
            'id_jp'       => 'required|exists:jenis_pembayaran,id|unique:jenis_biaya,id_jp,NULL,id,angkatan,' . $request->input('angkatan'),
        ], [
            'id_jp.unique' => 'Pembayaran untuk tahun akademik ini sudah ada, edit saja nilainya.',
        ]);

        Jenis_Biaya::create([
            'id_jp'       => $data['id_jp'],
            'angkatan'    => $data['angkatan'],
            'total_beban' => $this->normalizeNominal($data['total_beban']),
        ]);

        return response()->json([
            'success' => true,
            'msg'     => 'Jenis Biaya berhasil ditambahkan',
        ]);
    }

    public function edit(Jenis_Biaya $jenis_biaya)
    {
        $jenis_biaya->load('get_jenis_pembayaran');
        $jenisPembayaran = JenisPembayaran::orderBy('nama')->get();
        $tahunAkademiks  = Tahun_Akademik::orderBy('nama_tahun', 'desc')->get();
        $title = 'Edit Nominal Keuangan';

        return view('jenis-biaya.edit', compact('title', 'jenisPembayaran', 'tahunAkademiks', 'jenis_biaya'));
    }

    public function update(Request $request, Jenis_Biaya $jenis_biaya)
    {
        $data = $request->validate([
            'total_beban' => 'required|numeric|min:0',
            'angkatan'    => 'required',
            'id_jp'       => 'required|exists:jenis_pembayaran,id|unique:jenis_biaya,id_jp,' . $jenis_biaya->id . ',id,angkatan,' . $request->input('angkatan'),
        ], [
            'id_jp.unique' => 'Pembayaran untuk tahun akademik ini sudah ada.',
        ]);

        $jenis_biaya->update([
            'id_jp'       => $data['id_jp'],
            'angkatan'    => $data['angkatan'],
            'total_beban' => $this->normalizeNominal($data['total_beban']),
        ]);

        return response()->json([
            'success' => true,
            'msg'     => 'Jenis Biaya berhasil diupdate',
        ]);
    }

    public function destroy(Jenis_Biaya $jenis_biaya)
    {
        $jenis_biaya->delete();
        return response()->json([
            'success' => true,
            'msg'     => 'Data Biaya berhasil dihapus',
        ]);
    }

    private function normalizeNominal($value): int
    {
        return \App\Utils\Angka::parseInt($value);
    }
}
