@php
    use App\Utils\Tanggal;
    use App\Utils\Angka;
    $bulanSekarang = (int) date('n');
    $tahunSekarang = (int) date('Y');

    $sppGabung = $sppLunas->merge($sppBelumLunas)
        ->sortBy(fn($s) => \Carbon\Carbon::parse($s->tanggal)->timestamp)
        ->values();
@endphp

<div class="card m-0" style="border-radius:0">
    <div class="card-body p-2">
        <div class="table-responsive">
            <table class="table table-sm table-striped align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-center" width="10%">No</th>
                        <th width="38%">Bulan</th>
                        <th class="text-end" width="18%">Nominal</th>
                        <th class="text-center" width="22%">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($sppGabung as $i => $item)
                        @php
                            $ts = \Carbon\Carbon::parse($item->tanggal);
                            $isLunas = $item->status === 'L';
                            $tahunTsg = (int) $ts->format('Y');
                            $bulanTsg = (int) $ts->format('n');
                            $diffTahun = $tahunTsg - $tahunSekarang;
                            $diffBulan = $bulanTsg - $bulanSekarang;
                            $totalBulan = $diffTahun * 12 + $diffBulan;
                            $bayarUrl = url('/app/transaksi/pembayaran-spp?' . http_build_query([
                                'prefill_id'     => $siswa->id,
                                'prefill_nama'   => $siswa->nama,
                                'prefill_status' => 'aktif',
                                'prefill_jenis'  => 'spp',
                                'tahun_akademik' => optional($anggota_kelas)->tahun_akademik,
                                'kelas'          => optional($anggota_kelas)->kode_kelas,
                            ]));
                        @endphp
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>{{ Tanggal::namaBulan($item->tanggal) }} {{ $ts->format('Y') }}</td>
                            <td class="text-end">
                                @if ($isLunas)
                                    {{ Angka::format((int) $item->nominal, 0) }}
                                @elseif ($totalBulan > 0)
                                    <span class="text-muted">-</span>
                                @else
                                    {{ Angka::format((int) $item->nominal, 0) }}
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        @if ($isLunas)
                                            <span class="badge bg-success" title="Tgl Lunas: {{ $item->tgl_lunas ? Tanggal::tglIndo($item->tgl_lunas) : '-' }}">
                                                Lunas
                                            </span>
                                        @elseif ($totalBulan < 0)
                                            <span class="badge bg-danger">Menunggak</span>
                                        @elseif ($totalBulan === 0)
                                            <span class="badge bg-warning text-dark">Belum Dibayar</span>
                                        @else
                                            <span class="badge bg-secondary">Belum Jatuh Tempo</span>
                                        @endif
                                    </div>
                                    @if (!$isLunas && $totalBulan <= 0)
                                        <a href="{{ $bayarUrl }}" class="btn btn-info btn-sm text-white rounded-circle d-inline-flex align-items-center justify-content-center" title="Bayar Sekarang" style="width:28px;height:28px;padding:0;">
                                            <i class="material-icons" style="font-size:16px">payments</i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center fw-bold py-4 text-muted">
                                Tidak ada tagihan SPP untuk siswa ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($sppGabung->count())
                    @php
                        $totalLunas = $sppLunas->sum('nominal');
                        $totalTagihan = $sppBelumLunas->filter(function ($item) {
                            $ts = \Carbon\Carbon::parse($item->tanggal);
                            $bulanSekarang = (int) date('n');
                            $tahunSekarang = (int) date('Y');
                            $totalBulan = ((int) $ts->format('Y') - $tahunSekarang) * 12
                                + ((int) $ts->format('n') - $bulanSekarang);
                            return $totalBulan <= 0;
                        })->sum('nominal');
                    @endphp
                    <tfoot>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-start">Total Lunas</td>
                            <td class="text-end text-success">{{ Angka::format($totalLunas, 0) }}</td>
                        </tr>
                        <tr class="fw-bold">
                            <td colspan="3" class="text-start">Total Tagihan</td>
                            <td class="text-end text-warning">{{ Angka::format($totalTagihan, 0) }}</td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>

