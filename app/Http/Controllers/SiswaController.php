<?php

namespace App\Http\Controllers;


use App\Models\Siswa;
use App\Models\AnggotaKelas;
use App\Models\Ruangan;
use App\Models\JenisBiaya;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use Illuminate\Http\Request;
use App\Models\Transaksi;
use Yajra\DataTables\Facades\DataTables;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Http\Requests\SiswaRequest;
use App\Services\SiswaService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;


class SiswaController extends Controller
{
    public function __construct(protected SiswaService $service) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $ta    = $request->tahun_akademik;
            $kelas = $request->kelas;

            $query = Siswa::select('id', 'nisn', 'nama', 'status_siswa')
                ->with(['anggotaKelas' => function ($q) use ($ta, $kelas) {
                    if ($ta)    $q->where('tahun_akademik', $ta);
                    if ($kelas) $q->where('kode_kelas', $kelas);
                    $q->orderByDesc('id');
                }]);

            if ($ta || $kelas) {
                $query->whereHas('anggotaKelas', function ($q) use ($ta, $kelas) {
                    if ($ta)    $q->where('tahun_akademik', $ta);
                    if ($kelas) $q->where('kode_kelas', $kelas);
                });
            }

            return DataTables::eloquent($query)
                ->addIndexColumn()
                ->addColumn('kode_kelas', function ($row) {
                    return optional($row->anggotaKelas->first())->kode_kelas ?: '-';
                })
->addColumn('tahun_akademik', function ($row) {
                    return optional($row->anggotaKelas->first())->tahun_akademik ?: '-';
                })
                ->addColumn('checkbox', function ($row) {
                    return '<div class="form-check">
                                <input class="form-check-input checkItem" type="checkbox" value="' . $row->id . '">
                            </div>';
                })
                ->addColumn('action', function ($row) {
                    return '
                                <button class="btn btn-secondary btnMutasi"
                                    data-id="' . $row->id . '"
                                    title="Mutasi Siswa">
                                    <i class="fa-solid fa-right-left"></i>
                                </button>
                            ';
                })
                ->rawColumns(['checkbox', 'action'])
                ->toJson();
        }

        return view('siswa.index', ['title' => 'Data Siswa']);
    }

    public function listTahun(Request $request)
    {
        $search = $request->get('q');

        $query = TahunAkademik::select('id', 'nama_tahun')
            ->orderByDesc('nama_tahun');
        if ($search) {
            $query->where('nama_tahun', 'like', "%{$search}%");
        }

        return response()->json(
            $query->get()->map(fn($item) => [
                'id'            => $item->id,
                'nama_tahun'    => $item->nama_tahun
            ])
        );
    }

    public function listKelas(Request $request)
    {
        $search = $request->get('q');

        $query = Kelas::select('id', 'nama_kelas', 'kode_kelas', 'tingkat');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_kelas', 'like', "%{$search}%")
                    ->orWhere('nama_kelas', 'like', "%{$search}%")
                    ->orWhere('tingkat', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $query->get()->map(fn($item) => [
                'id'            => $item->id,
                'nama_kelas'    => $item->nama_kelas,
                'kode_kelas'    => $item->kode_kelas,
                'tingkat'       => $item->tingkat,
            ])
        );
    }

    public function printSiswa(Request $request)
    {
        $ids = explode(',', $request->ids);
        $siswa = Siswa::whereIn('id', $ids)->get();

        $title = 'Daftar Siswa';
        $data = [
            'title' => $title,
            'siswa' => $siswa
        ];
        $logoPath = \App\Models\Profil::logoPath();
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = pathinfo($logoPath, PATHINFO_EXTENSION);
        }
        $pdf = Pdf::loadView('siswa.view.cetak', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('daftar_siswa.pdf');
    }

    public function mutasi(Request $request)
    {
        $request->validate([
            'kelas' => 'required|string',
            'ids'   => 'required|array',
        ]);

        [$kodeKelasBaru, $tingkatBaru] = $this->service->splitKelas($request->kelas);

        $year = Carbon::now()->year;
        $tahunAkademik = $year . '/' . ($year + 1);
        $tglMasuk = Carbon::today();
        $tglKeluar = Carbon::today()->addYear();

        foreach ($request->ids as $idSiswa) {
            $siswa = Siswa::find($idSiswa);
            if (!$siswa) continue;

            $siswa->update([
                'kode_kelas' => $kodeKelasBaru,
            ]);

            $anggota = AnggotaKelas::where('id_siswa', $idSiswa)
                ->orderByDesc('id')
                ->first();

            if ($anggota && $anggota->tingkat === $tingkatBaru) {
                $anggota->update(['kode_kelas' => $kodeKelasBaru]);
                continue;
            }

            if ($anggota) {
                $anggota->update(['status' => 'nonaktif']);
            }

            $nominal = JenisBiaya::whereHas('get_jenis_pembayaran', fn($q) => $q->where('kode_akun', '4.1.01.01'))
                ->where('angkatan', TahunAkademik::where('status', 'aktif')->value('nama_tahun') ?? date('Y'))
                ->value('total_beban') ?? 0;

            $anggotaBaru = AnggotaKelas::create([
                'id_siswa'       => $idSiswa,
                'tahun_akademik' => $tahunAkademik,
                'tingkat'        => $tingkatBaru,
                'kode_kelas'     => $kodeKelasBaru,
                'spp_nominal'    => $nominal > 0 ? (string) $nominal : null,
                'tgl_masuk'      => $tglMasuk->format('Y-m-d'),
                'tgl_keluar'     => $tglKeluar->format('Y-m-d'),
                'status'         => 'aktif',
            ]);

            $this->service->generateSppBulanan(
                $anggotaBaru,
                (int) $this->service->normalizeNominal($nominal),
                ['tanggal_masuk' => $tglMasuk->format('Y-m-d')]
            );
        }

        return response()->json([
            'success' => true,
            'msg'     => 'Mutasi berhasil diproses!',
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $title          = "Tambah Siswa";
        $kelas          = Kelas::get();
        $ruang          = Ruangan::get();
        $tahunAkademmik = TahunAkademik::get();
        $nominalSpp     = $this->nominalSppDefault();

        return view('siswa.tambah', compact('title', 'kelas', 'ruang', 'tahunAkademmik', 'nominalSpp'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SiswaRequest $request)
    {
        $data = $request->validated();
        $data['foto'] = $this->handleFoto($request);

        $defaultSpp = (int) ($this->nominalSppDefault() ?? 0);

        try {
            $siswa = $this->service->createWithKelasDanSpp($data, $defaultSpp);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => ['nisn' => [$e->getMessage()]],
            ], 422);
        }

        return response()->json([
            'success' => true,
            'msg'     => 'Siswa berhasil disimpan',
            'data'    => $siswa,
        ]);
    }

    private function nominalSppDefault(): int
    {
        return $this->nominalSppByTahun(null);
    }

    public function nominalSppByTahun(?string $tahun): int
    {
        if (!$tahun) {
            $tahun = TahunAkademik::where('status', 'aktif')->value('nama_tahun') ?? date('Y');
        }

        return (int) (JenisBiaya::whereHas('get_jenis_pembayaran', fn($q) => $q->where('kode_akun', '4.1.01.01'))
            ->where('angkatan', $tahun)
            ->value('total_beban') ?? 0);
    }

    public function getNominalSpp(Request $request)
    {
        $tahun = $request->get('tahun_akademik');

        if (!$tahun) {
            return response()->json(['nominal' => 0]);
        }

        return response()->json(['nominal' => $this->nominalSppByTahun($tahun)]);
    }

    private function handleFoto(Request $request, ?string $existing = null): string
    {
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fileName = time() . '_' . $request->file('foto')->getClientOriginalName();
            $request->file('foto')->storeAs('siswa', $fileName, 'public');
            return $fileName;
        }
        return $existing ?? 'default.png';
    }

    /**
     * Display the specified resource.
     */
    public function show(Siswa $siswa)
    {
        $title = "Detail Siswa";
        $siswa = Siswa::where('id', $siswa->id)->first();
        $riwayat = Transaksi::with('spp')->where('siswa_id', $siswa->id)->get();

        return view('siswa.detail', compact('title', 'siswa', 'riwayat'));
    }

    public function riwayatPembayaran($id)
    {
        $siswa = Siswa::where('id', $id)->first();
        $riwayat = Transaksi::with('spp')
            ->where('siswa_id', $siswa->id)
            ->get();

        $data = [
            'title'   => 'Riwayat Pembayaran Siswa',
            'riwayat' => $riwayat,
            'siswa'   => $siswa,
        ];

        $logoPath = \App\Models\Profil::logoPath();
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = pathinfo($logoPath, PATHINFO_EXTENSION);
        }

        $pdf = Pdf::loadView('siswa.view.riwayatPembayaran', $data);
        $pdf->setPaper('A4', 'portrait');
        return $pdf->stream('Riwayat_pembayaran.pdf');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function  edit(Siswa $siswa)
    {
        $title          = "Edit Siswa";
        $kelas          = Kelas::get();
        $ruang          = Ruangan::get();
        $tahunAkademmik = TahunAkademik::get();
        $nominalSpp     = $this->nominalSppDefault();

        return view('siswa.edit', compact('title', 'kelas', 'siswa', 'ruang', 'tahunAkademmik', 'nominalSpp'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SiswaRequest $request, Siswa $siswa)
    {
        $data = $request->validated();
        $data['foto'] = $this->handleFoto($request, $siswa->foto);

        if (empty($data['password'])) {
            unset($data['password']);
        }

        [$kodeKls, $tingkat] = $this->service->splitKelas($data['kelas']);
        $data['kode_kelas'] = $kodeKls;
        $data['ruang'] = $data['ruangan'];
        $data['id_user'] = auth()->id();
        $data['alat_transportasi'] = $data['transportasi'];
        $data['no_telepon_ayah'] = $data['no_telp_ayah'];
        $data['no_telepon_ibu'] = $data['no_telp_ibu'];
        $data['no_telepon_wali'] = $data['no_telp_wali'];
        $data['tgl_masuk'] = $data['tanggal_masuk'] ?? null;
        $data['hp'] = $data['hp'] ?? $data['telepon'] ?? '-';

        $newSppNominal = (string) $this->service->normalizeNominal($data['spp_nominal'] ?? 0);

        unset($data['kelas'], $data['ruangan'], $data['transportasi'],
              $data['no_telp_ayah'], $data['no_telp_ibu'], $data['no_telp_wali'],
              $data['tanggal_masuk'], $data['tingkat'], $data['spp_nominal'],
              $data['jurusan']);

        $siswa->update($data);

        // Sync kelas, tingkat & spp_nominal ke AnggotaKelas aktif
        $anggota = $siswa->anggotaKelas()->where('status', 'aktif')->orderByDesc('id')->first();
        if ($anggota) {
            $oldSppNominal = (string) ($anggota->spp_nominal ?? '0');
            $anggota->update([
                'kode_kelas'     => $kodeKls,
                'tingkat'        => $tingkat,
                'tahun_akademik' => $data['tahun_akademik'] ?? $anggota->tahun_akademik,
                'spp_nominal'    => (int) $newSppNominal > 0 ? (string) $newSppNominal : null,
            ]);

            if ($newSppNominal !== $oldSppNominal && (int) $newSppNominal > 0) {
                DB::table('spp as s')
                    ->where('s.anggota_kelas', $anggota->id)
                    ->where('s.status', 'B')
                    ->update(['s.nominal' => $newSppNominal]);
            }
        }

        return response()->json([
            'success' => true,
            'msg'     => 'Siswa berhasil diupdate',
            'data'    => $siswa,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->update([
            'status_siswa' => 'blokir'
        ]);

        return response()->json([
            'success' => true,
            'msg' => 'Siswa berhasil diblokir',
        ]);
    }
}


