<title>{{ $title }}</title>
@extends('laporan-keuangan.layout.dasar')

@section('content')
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:8px;">
        <tr>
            <td align="center" style="padding:0;">
                <div style="font-size:16px; font-weight:bold; margin:0; padding:0; line-height:1.2;">
                    {{ $title }}
                    @if (!empty($kelas))
                        &mdash; Kelas {{ $kelas->kode_kelas }}
                    @endif
                </div>
                <div style="font-size:12px; margin:2px 0 0 0; padding:0; line-height:1.2;">
                    Periode
                    {{ $periode['awal']->translatedFormat('d F Y') }}
                    s.d.
                    {{ $periode['akhir']->translatedFormat('d F Y') }}
                </div>
            </td>
        </tr>
    </table>

    @php
        $nBulan = count($bulanList);
        $bulanW = max(4, min(6, floor(56 / max(1, $nBulan))));
        $fixedW = 100 - ($bulanW * $nBulan);
    @endphp

    <table width="100%" cellpadding="3" cellspacing="0"
        style="border-collapse:collapse; font-size:9px; table-layout:fixed;">
        <colgroup>
            <col style="width:3%;">
            <col style="width:8%;">
            <col style="width:18%;">
            <col style="width:7%;">
            @for ($i = 0; $i < $nBulan; $i++)
                <col style="width:{{ $bulanW }}%;">
            @endfor
            <col style="width:8%;">
            <col style="width:8%;">
            <col style="width:8%;">
            <col style="width:{{ max(4, $fixedW - 24) }}%;">
        </colgroup>

        <thead>
    <tr style="text-align:center; font-weight:bold;">
        <th rowspan="2" style="border:1px solid #000;" width="5%">No</th>
        <th rowspan="2" style="border:1px solid #000;" width="12%">NISN</th>
        <th rowspan="2" style="border:1px solid #000;" width="25%">Nama Siswa</th>
        <th rowspan="2" style="border:1px solid #000;" width="12%">Target / Bulan</th>
        <th colspan="3" style="border:1px solid #000;" width="36%">Akumulasi</th>
        <th rowspan="2" style="border:1px solid #000;" width="10%">Keterangan</th>
    </tr>
    <tr style="text-align:center; font-weight:bold;">
        <th style="border:1px solid #000; border-top:none;" width="12%">Target s.d. Saat Ini</th>
        <th style="border:1px solid #000; border-top:none;" width="12%">Bayar s.d. Periode Ini</th>
        <th style="border:1px solid #000; border-top:none;" width="12%">Sisa</th>
    </tr>
</thead>

        <tbody>
            @forelse ($anggotaKelas as $i => $row)
                <tr>
                    <td style="border:1px solid #000; text-align:center;">{{ $i + 1 }}</td>
                    <td style="border:1px solid #000; text-align:center;">{{ $row->getSiswa->nisn ?? '-' }}</td>
                    <td style="border:1px solid #000;">{{ $row->getSiswa->nama ?? '-' }}</td>
                    <td style="border:1px solid #000; text-align:right;">{{ \App\Utils\Angka::format($row->per_bulan, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right;">{{ \App\Utils\Angka::format($row->target_sd_saat_ini, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right;">{{ \App\Utils\Angka::format($row->sd_periode_ini, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right; color: {{ $row->sisa > 0 ? 'red' : 'black' }};">
                        {{ $row->sisa > 0 ? \App\Utils\Angka::format($row->sisa, 2) : '-' }}
                    </td>
                    <td style="border:1px solid #000; text-align:center; color: {{ $row->sisa > 0 ? 'red' : 'black' }};">
                        {{ $row->sisa > 0 ? 'Belum Lunas' : 'Lunas' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 4 + $nBulan + 4 }}" style="border:1px solid #000; text-align:center; font-style:italic;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
