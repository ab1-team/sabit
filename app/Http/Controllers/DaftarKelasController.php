<?php

namespace App\Http\Controllers;


use App\Models\AnggotaKelas;
use App\Models\Siswa;
use App\Models\Spp;
use App\Models\TahunAkademik;
use App\Models\Kelas;
use App\Models\Profil;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DaftarKelasController extends Controller
{
    public function index()
    {
        $tahunBerjalan = $this->tahunBerjalan();

        return view('daftar-kelas.index', [
            'title'          => 'Daftar Siswa Per-Kelas',
            'tahunBerjalan'  => $tahunBerjalan,
        ]);
    }

    private function tahunBerjalan(): ?string
    {
        $now = \Carbon\Carbon::now();
        $yy  = (int) $now->format('Y');
        $mm  = (int) $now->format('n');

        if ($mm >= 7) {
            $candidate = sprintf('%d/%d', $yy, $yy + 1);
        } else {
            $candidate = sprintf('%d/%d', $yy - 1, $yy);
        }

        $found = TahunAkademik::where('nama_tahun', $candidate)->value('nama_tahun');
        if ($found) {
            return $found;
        }

        return TahunAkademik::orderByDesc('nama_tahun')->value('nama_tahun');
    }

    public function listTahun(Request $request)
    {
        $search = $request->get('q');

        $query = TahunAkademik::select('id', 'nama_tahun')
            ->orderBy('nama_tahun');
        if ($search) {
            $query->where('nama_tahun', 'like', "%{$search}%");
        }

        return response()->json(
            $query->get()->map(fn($item) => [
                'id'         => $item->id,
                'nama_tahun' => $item->nama_tahun,
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
                'id'         => $item->id,
                'nama_kelas' => $item->nama_kelas,
                'kode_kelas' => $item->kode_kelas,
                'tingkat'    => $item->tingkat,
            ])
        );
    }

    public function data(Request $request)
    {
        $ta    = $request->tahun_akademik;
        $kelas = $request->kelas;
        if ($kelas === '__all__') {
            $kelas = null;
        }
        if (!$kelas) {
            $kelas = 'I.A';
        }

        $query = Siswa::query()
            ->whereHas('anggotaKelas', function ($q) use ($ta, $kelas) {
                $q->where('status', 'aktif');
                if ($ta)    $q->where('tahun_akademik', $ta);
                if ($kelas) $q->where('kode_kelas', $kelas);
            })
            ->with(['anggotaKelas' => function ($q) use ($ta, $kelas) {
                $q->where('status', 'aktif');
                if ($ta)    $q->where('tahun_akademik', $ta);
                if ($kelas) $q->where('kode_kelas', $kelas);
                $q->orderByDesc('id');
            }, 'anggotaKelas.spp'])
            ->orderBy('siswa.nama', 'asc');

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->order(function ($q) {
                $q->orderBy('siswa.nama', 'asc');
            })
            ->filterColumn('nisn', function ($q, $kw) {
                $q->where('siswa.nisn', 'like', "%{$kw}%");
            })
            ->filterColumn('nama', function ($q, $kw) {
                $q->where('siswa.nama', 'like', "%{$kw}%");
            })
            ->addColumn('nisn', function ($row) {
                return $row->nisn;
            })
            ->addColumn('nama', function ($row) {
                return $row->nama;
            })
            ->addColumn('spp_per_bulan', function ($row) {
                $ak = $row->anggotaKelas->first();
                $nominal = (int) ($ak->spp_nominal ?? 0);
                return $nominal;
            })
            ->addColumn('target_sampai_bulan_ini', function ($row) {
                $ak = $row->anggotaKelas->first();
                if (!$ak) return 0;
                $bulanSekarang = (int) date('n');
                $tahunSekarang = (int) date('Y');
                return $ak->spp
                    ->filter(function ($s) use ($bulanSekarang, $tahunSekarang) {
                        if (!$s->tanggal) return false;
                        $ts = $s->tanggal instanceof \DateTimeInterface
                            ? $s->tanggal
                            : \Carbon\Carbon::parse($s->tanggal);
                        return (int) $ts->format('Y') < $tahunSekarang
                            || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                    })
                    ->sum('nominal');
            })
            ->addColumn('tagihan_bulan_ini', function ($row) {
                $ak = $row->anggotaKelas->first();
                if (!$ak) return 0;
                $bulanSekarang = (int) date('n');
                $tahunSekarang = (int) date('Y');

                $totalTagihan = $ak->spp
                    ->filter(function ($s) use ($bulanSekarang, $tahunSekarang) {
                        if (!$s->tanggal) return false;
                        $ts = $s->tanggal instanceof \DateTimeInterface
                            ? $s->tanggal
                            : \Carbon\Carbon::parse($s->tanggal);
                        return (int) $ts->format('Y') < $tahunSekarang
                            || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                    })
                    ->sum('nominal');

                $totalPembayaran = $ak->spp
                    ->where('status', 'L')
                    ->filter(function ($s) use ($bulanSekarang, $tahunSekarang) {
                        if (!$s->tgl_lunas) {
                            if (!$s->tanggal) return false;
                            $ts = $s->tanggal instanceof \DateTimeInterface
                                ? $s->tanggal
                                : \Carbon\Carbon::parse($s->tanggal);
                            return (int) $ts->format('Y') < $tahunSekarang
                                || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                        }
                        $ts = $s->tgl_lunas instanceof \DateTimeInterface
                            ? $s->tgl_lunas
                            : \Carbon\Carbon::parse($s->tgl_lunas);
                        return (int) $ts->format('Y') < $tahunSekarang
                            || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                    })
                    ->sum('nominal');

                $sisa = $totalTagihan - $totalPembayaran;
                return $sisa > 0 ? $sisa : 0;
            })
            ->addColumn('realisasi_sampai_bulan_ini', function ($row) {
                $ak = $row->anggotaKelas->first();
                if (!$ak) return 0;
                $bulanSekarang = (int) date('n');
                $tahunSekarang = (int) date('Y');
                return $ak->spp
                    ->where('status', 'L')
                    ->filter(function ($s) use ($bulanSekarang, $tahunSekarang) {
                        if (!$s->tgl_lunas) {
                            if (!$s->tanggal) return false;
                            $ts = $s->tanggal instanceof \DateTimeInterface
                                ? $s->tanggal
                                : \Carbon\Carbon::parse($s->tanggal);
                            return (int) $ts->format('Y') < $tahunSekarang
                                || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                        }
                        $ts = $s->tgl_lunas instanceof \DateTimeInterface
                            ? $s->tgl_lunas
                            : \Carbon\Carbon::parse($s->tgl_lunas);
                        return (int) $ts->format('Y') < $tahunSekarang
                            || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                    })
                    ->sum('nominal');
            })
            ->addColumn('status_tagihan', function ($row) {
                $ak = $row->anggotaKelas->first();
                if (!$ak) return 'menunggak';
                $bulanSekarang = (int) date('n');
                $tahunSekarang = (int) date('Y');
                $target = $ak->spp
                    ->filter(function ($s) use ($bulanSekarang, $tahunSekarang) {
                        if (!$s->tanggal) return false;
                        $ts = $s->tanggal instanceof \DateTimeInterface
                            ? $s->tanggal
                            : \Carbon\Carbon::parse($s->tanggal);
                        return (int) $ts->format('Y') < $tahunSekarang
                            || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                    })
                    ->sum('nominal');
                $realisasi = $ak->spp
                    ->where('status', 'L')
                    ->filter(function ($s) use ($bulanSekarang, $tahunSekarang) {
                        if (!$s->tgl_lunas) {
                            if (!$s->tanggal) return false;
                            $ts = $s->tanggal instanceof \DateTimeInterface
                                ? $s->tanggal
                                : \Carbon\Carbon::parse($s->tanggal);
                            return (int) $ts->format('Y') < $tahunSekarang
                                || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                        }
                        $ts = $s->tgl_lunas instanceof \DateTimeInterface
                            ? $s->tgl_lunas
                            : \Carbon\Carbon::parse($s->tgl_lunas);
                        return (int) $ts->format('Y') < $tahunSekarang
                            || ((int) $ts->format('Y') === $tahunSekarang && (int) $ts->format('n') <= $bulanSekarang);
                    })
                    ->sum('nominal');
                if ($realisasi > $target) return 'lebih';
                if ($realisasi == $target && $target > 0) return 'pas';
                return 'menunggak';
            })
            ->addColumn('target_daftar_ulang', function ($row) {
                return 0;
            })
            ->addColumn('realisasi_daftar_ulang', function ($row) {
                return 0;
            })
            ->addColumn('action', function ($row) use ($request) {
                $params = [
                    'prefill_id'       => $row->id,
                    'prefill_nama'     => $row->nama,
                    'prefill_status'   => 'aktif',
                    'prefill_jenis'    => 'spp',
                    'tahun_akademik'   => $request->tahun_akademik,
                    'kelas'            => $request->kelas,
                ];
                $url = '/app/transaksi/pembayaran-spp?' . http_build_query($params);
                return '<a href="' . $url . '" class="btn btn-info btn-sm text-white d-inline-flex align-items-center gap-1 px-2" title="Bayar Sekarang" style="font-size:.75rem;line-height:1.4;">'
                    . '<span>Bayar Sekarang</span>'
                    . '<i class="material-icons align-middle" style="font-size:14px">arrow_forward</i>'
                    . '</a>';
            })
            ->rawColumns(['action', 'target_sampai_bulan_ini'])
            ->toJson();
    }

    public function cetakKartuBatch(Request $request)
    {
        $jenis = $request->query('jenis', 'kartu_spp');
        $allowed = ['kartu_spp', 'uts1', 'pas1', 'uts2', 'pas2'];
        if (!in_array($jenis, $allowed, true)) {
            abort(404, 'Jenis cetak tidak dikenal.');
        }

        $ta    = $request->query('tahun_akademik');
        $kelas = $request->query('kelas');
        if ($kelas === '__all__' || !$kelas) {
            abort(422, 'Pilih kelas terlebih dahulu.');
        }

        $profil = Profil::first();
        $sopPts = (int) ($profil->cetak_pts ?? 3);
        $sopPas = (int) ($profil->cetak_pas ?? 3);

        $siswaQuery = Siswa::query()
            ->whereHas('anggotaKelas', function ($q) use ($ta, $kelas) {
                $q->where('status', 'aktif');
                if ($ta)    $q->where('tahun_akademik', $ta);
                if ($kelas) $q->where('kode_kelas', $kelas);
            })
            ->with(['anggotaKelas' => function ($q) use ($ta, $kelas) {
                $q->where('status', 'aktif');
                if ($ta)    $q->where('tahun_akademik', $ta);
                if ($kelas) $q->where('kode_kelas', $kelas);
                $q->orderByDesc('id');
            }, 'anggotaKelas.spp'])
            ->orderBy('nama');

        $siswaAll = $siswaQuery->get();

        $kelasLabel = trim($kelas.' · '.$ta);

        $logoPath = \App\Models\Profil::logoPath();
        $data = [
            'profil'      => $profil,
            'siswaList'   => [],
            'kelasLabel'  => $kelasLabel,
            'tahun_pel'   => $ta,
        ];
        if (file_exists($logoPath)) {
            $data['logo'] = base64_encode(file_get_contents($logoPath));
            $data['logo_type'] = pathinfo($logoPath, PATHINFO_EXTENSION);
        }

        if ($jenis === 'kartu_spp') {
            $data['siswaList'] = $siswaAll->map(function ($siswa) {
                $ak = $siswa->anggotaKelas->first();
                return [
                    'siswa'         => $siswa,
                    'spp_perbulan'  => (int) ($ak->spp_nominal ?? 0),
                ];
            })->values()->all();

            $pdf = Pdf::loadView('daftar-kelas.arsip.view.cetak_kartu_spp_batch', $data)
                ->setPaper('A4', 'portrait');

            return $pdf->stream('kartu-spp-'.$kelas.'.pdf');
        }

        $syarat = $jenis === 'uts1' || $jenis === 'pas1' ? $sopPts : $sopPas;
        $kat = str_starts_with($jenis, 'uts') ? 'uts' : 'pas';
        $periode = substr($jenis, -1);
        $periodeRoman = $periode === '1' ? 'I' : 'II';
        $jenisUjian = ($kat === 'uts' ? 'UJIAN TENGAH SEMESTER' : 'PENILAIAN AKHIR SEMESTER').' '.$periodeRoman;

        $data['siswaList'] = $siswaAll->map(function ($siswa) use ($syarat) {
            $bulanLunas = Spp::bulanLunasBySiswa((int) $siswa->id);
            return [
                'siswa'        => $siswa,
                'bulan_lunas'  => $bulanLunas,
                'memenuhi'     => $bulanLunas >= $syarat,
            ];
        })
        ->filter(fn ($row) => $row['memenuhi'])
        ->values()
        ->all();

        $data['syarat']     = $syarat;
        $data['jenis_ujian'] = $jenisUjian;

        $pdf = Pdf::loadView('daftar-kelas.arsip.view.cetak_kartu_ujian_batch', $data)
            ->setPaper('A4', 'portrait');

        return $pdf->stream('kartu-'.($kat).$periode.'-'.$kelas.'.pdf');
    }
}



