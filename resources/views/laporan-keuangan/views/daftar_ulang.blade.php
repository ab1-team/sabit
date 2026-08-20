<title>{{ $title }}</title>
@extends('laporan-keuangan.layout.dasar')

@section('content')
    <table width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:10px;">
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

    <table width="100%" cellpadding="4" cellspacing="0" style="border-collapse:collapse; font-size:11px;">

        <thead>
            <tr style="text-align:center; font-weight:bold;">
                <th style="border:1px solid #000; width:5%;">No</th>
                <th style="border:1px solid #000; width:10%;">NISN</th>
                <th style="border:1px solid #000; width:25%;">Nama Siswa</th>
                <th style="border:1px solid #000; width:15%;">Tgl Bayar Terakhir</th>
                <th style="border:1px solid #000; width:20%;">Jumlah Bayar</th>
                <th style="border:1px solid #000; width:25%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($anggotaKelas as $i => $row)
                <tr>
                    <td style="border:1px solid #000; text-align:center;">{{ $i + 1 }}</td>

                    <td style="border:1px solid #000; text-align:center;">
                        {{ $row->siswa->nisn ?? '-' }}
                    </td>

                    <td style="border:1px solid #000;">
                        {{ $row->siswa->nama ?? '-' }}
                    </td>

                    <td style="border:1px solid #000; text-align:center;">
                        {{ $row->tgl_bayar_terakhir
                            ? \Carbon\Carbon::parse($row->tgl_bayar_terakhir)->translatedFormat('d F Y')
                            : '-' }}
                    </td>

                    <td style="border:1px solid #000; text-align:right;">
                        {{ \App\Utils\Angka::format($row->realisasi ?? 0, 2) }}
                    </td>

                    <td style="border:1px solid #000; text-align:center;">
                        Sudah Bayar
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="border:1px solid #000; text-align:center; font-style:italic;">
                        Tidak ada siswa yang membayar pada periode ini
                    </td>
                </tr>
            @endforelse
        </tbody>

    </table>
@endsection
