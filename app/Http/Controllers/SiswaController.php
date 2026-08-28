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
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;


class SiswaController extends Controller
{
    public function __construct(protected SiswaService $service) {}

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
            ->orderByDesc('nama_tahun')
            ->limit(50);
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

        $query = Kelas::select('id', 'nama_kelas', 'kode_kelas', 'tingkat')
            ->limit(50);
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

        $siswaIds = $request->ids;
        $siswaList = Siswa::whereIn('id', $siswaIds)->get()->keyBy('id');

        $currentAnggota = AnggotaKelas::whereIn('id_siswa', $siswaIds)
            ->where('status', 'aktif')
            ->orderByDesc('id')
            ->get()
            ->groupBy('id_siswa')
            ->map(fn ($g) => $g->first());

        $tahunAktif = TahunAkademik::where('status', 'aktif')->value('nama_tahun') ?? date('Y');

        $nominal = (int) (JenisBiaya::query()
            ->join('jenis_pembayaran', 'jenis_pembayaran.id', '=', 'jenis_biaya.id_jp')
            ->where('jenis_pembayaran.kode_akun', '4.1.01.01')
            ->where('jenis_biaya.angkatan', $tahunAktif)
            ->value('jenis_biaya.total_beban') ?? 0);

        $count = 0;
        foreach ($siswaIds as $idSiswa) {
            $siswa = $siswaList[$idSiswa] ?? null;
            if (!$siswa) continue;

            $siswa->update([
                'kode_kelas' => $kodeKelasBaru,
            ]);

            $anggota = $currentAnggota[$idSiswa] ?? null;

            if ($anggota && $anggota->tingkat === $tingkatBaru) {
                $anggota->update(['kode_kelas' => $kodeKelasBaru]);
                continue;
            }

            if ($anggota) {
                $anggota->update(['status' => 'nonaktif']);
            }

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
            $count++;
        }

        return response()->json([
            'success' => true,
            'msg'     => "Mutasi berhasil diproses untuk {$count} siswa!",
        ]);
    }

    public function create()
    {
        $title          = "Tambah Siswa";
        $kelas          = Kelas::orderBy('kode_kelas')->get(['id', 'kode_kelas', 'nama_kelas', 'tingkat']);
        $ruang          = Ruangan::where('status', 'aktif')->orderBy('kode_ruangan')->get(['id', 'kode_ruangan', 'nama_ruangan']);
        $tahunAkademmik = TahunAkademik::orderByDesc('nama_tahun')->get(['id', 'nama_tahun']);
        $nominalSpp     = $this->nominalSppDefault();

        return view('siswa.tambah', compact('title', 'kelas', 'ruang', 'tahunAkademmik', 'nominalSpp'));
    }

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

        $cacheKey = "spp_nominal_{$tahun}:" . (tenant('id') ?? 'central');

        return Cache::remember($cacheKey, 3600, function () use ($tahun) {
            $val = DB::table('jenis_biaya')
                ->join('jenis_pembayaran', 'jenis_pembayaran.id', '=', 'jenis_biaya.id_jp')
                ->where('jenis_pembayaran.kode_akun', '4.1.01.01')
                ->where('jenis_biaya.angkatan', $tahun)
                ->value('jenis_biaya.total_beban');
            return (int) ($val ?? 0);
        });
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

    public function show(Siswa $siswa)
    {
        $title = "Detail Siswa";
        $riwayat = Transaksi::with('spp')
            ->where('siswa_id', $siswa->id)
            ->whereNull('deleted_at')
            ->latest('tanggal_transaksi')
            ->limit(100)
            ->get();

        return view('siswa.detail', compact('title', 'siswa', 'riwayat'));
    }

    public function riwayatPembayaran($id)
    {
        $siswa = Siswa::findOrFail($id);
        $riwayat = Transaksi::with('spp')
            ->where('siswa_id', $siswa->id)
            ->whereNull('deleted_at')
            ->latest('tanggal_transaksi')
            ->limit(200)
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

    public function  edit(Siswa $siswa)
    {
        $title          = "Edit Siswa";
        $kelas          = Kelas::orderBy('kode_kelas')->get(['id', 'kode_kelas', 'nama_kelas', 'tingkat']);
        $ruang          = Ruangan::where('status', 'aktif')->orderBy('kode_ruangan')->get(['id', 'kode_ruangan', 'nama_ruangan']);
        $tahunAkademmik = TahunAkademik::orderByDesc('nama_tahun')->get(['id', 'nama_tahun']);
        $nominalSpp     = $this->nominalSppDefault();

        return view('siswa.edit', compact('title', 'kelas', 'siswa', 'ruang', 'tahunAkademmik', 'nominalSpp'));
    }

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
                DB::table('spp')
                    ->where('anggota_kelas', $anggota->id)
                    ->where('status', 'B')
                    ->update(['nominal' => $newSppNominal]);
            }
        }

        return response()->json([
            'success' => true,
            'msg'     => 'Siswa berhasil diupdate',
            'data'    => $siswa,
        ]);
    }

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
