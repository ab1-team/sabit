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
                    @if (!empty($namaTahunAkademik))
                        &mdash; Tahun Akademik {{ $namaTahunAkademik }}
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
        // Nomor baris dimulai dari $nomor_awal supaya penomoran tetap
        // kontinyu ketika laporan dipecah menjadi beberapa bagian.
        $nomor_awal = $nomor_awal ?? 1;
        // Ringkasan (baris JUMLAH) hanya ditampilkan pada bagian terakhir dan
        // memakai data lengkap supaya angkanya tetap konsisten.
        $tampilkan_ringkasan = $tampilkan_ringkasan ?? true;
    @endphp

    <table width="100%" cellpadding="3" cellspacing="0"
        style="border-collapse:collapse; font-size:9px; table-layout:fixed;">
        {{--
            Catatan: dompdf mengabaikan tag <colgroup>, jadi lebar kolom
            harus ditulis pada sel baris header.
        --}}
        <thead>
            <tr style="text-align:center; font-weight:bold;">
                <th rowspan="2" style="border:1px solid #000;" width="4%">No</th>
                <th rowspan="2" style="border:1px solid #000;" width="11%">NISN</th>
                <th rowspan="2" style="border:1px solid #000; text-align:left;" width="25%">Nama Siswa</th>
                <th rowspan="2" style="border:1px solid #000;" width="11%">Target / Bulan</th>
                <th colspan="3" style="border:1px solid #000;" width="37%">Akumulasi</th>
                <th rowspan="2" style="border:1px solid #000;" width="12%">Keterangan</th>
            </tr>
            <tr style="text-align:center; font-weight:bold;">
                <th style="border:1px solid #000; border-top:none;" width="12.3%">Target s.d. Saat Ini</th>
                <th style="border:1px solid #000; border-top:none;" width="12.3%">Bayar s.d. Periode Ini</th>
                <th style="border:1px solid #000; border-top:none;" width="12.4%">Sisa</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($anggotaKelas as $i => $row)
                <tr>
                    <td style="border:1px solid #000; text-align:center;">{{ $nomor_awal + $i }}</td>
                    <td style="border:1px solid #000; text-align:center;">{{ $row->siswa_nisn ?? ($row->siswa->nisn ?? '-') }}</td>
                    <td style="border:1px solid #000; padding-left: 8px;">{{ $row->siswa_nama ?? ($row->siswa->nama ?? '-') }}</td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap;">{{ \App\Utils\Angka::format($row->per_bulan, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap;">{{ \App\Utils\Angka::format($row->target_sd_saat_ini, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap;">{{ \App\Utils\Angka::format($row->sd_periode_ini, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap; color: {{ $row->sisa > 0 ? 'red' : 'black' }};">
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
            @if (isset($totals) && $anggotaKelas->count() > 0 && ! empty($tampilkan_ringkasan))
                <tr style="font-weight:bold; background:#f0f0f0;">
                    <td colspan="3" style="border:1px solid #000; text-align:center;">JUMLAH</td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap;">{{ \App\Utils\Angka::format($totals['per_bulan'] ?? 0, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap;">{{ \App\Utils\Angka::format($totals['target_sd_saat_ini'] ?? 0, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap;">{{ \App\Utils\Angka::format($totals['sd_periode_ini'] ?? 0, 2) }}</td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap;">{{ \App\Utils\Angka::format($totals['sisa'] ?? 0, 2) }}</td>
                    <td style="border:1px solid #000;"></td>
                </tr>
            @endif
        </tbody>
    </table>
@endsection
