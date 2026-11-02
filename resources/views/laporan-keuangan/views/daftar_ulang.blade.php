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

    <table width="100%" cellpadding="4" cellspacing="0"
        style="border-collapse:collapse; font-size:11px; table-layout: fixed;">
        {{--
            Lebar kolom (persen) ditulis pada sel baris header. Catatan:
            dompdf mengabaikan tag <colgroup>, jadi lebar harus ditulis
            pada sel baris pertama tabel. Kolom No & Kelas diperkecil
            supaya kolom Nama Siswa / Keterangan lebih lega.
        --}}
        <thead>
            <tr style="text-align:center; font-weight:bold;">
                <th width="3%" style="border:1px solid #000; width: 3%;">No</th>
                <th width="7%" style="border:1px solid #000; width: 7%;">Kelas</th>
                <th width="10%" style="border:1px solid #000; width: 10%;">NISN</th>
                <th width="28%" style="border:1px solid #000; width: 28%; text-align:left; padding-left: 8px;">Nama Siswa</th>
                <th width="14%" style="border:1px solid #000; width: 14%;">Tgl Bayar Terakhir</th>
                <th width="16%" style="border:1px solid #000; width: 16%;">Jumlah Bayar</th>
                <th width="22%" style="border:1px solid #000; width: 22%; padding-left: 8px;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($anggotaKelas as $i => $row)
                @php $sudahBayar = $row->sudah_bayar ?? false; @endphp
                <tr>
                    <td style="border:1px solid #000; text-align:center;">{{ $nomor_awal + $i }}</td>

                    <td style="border:1px solid #000; text-align:center;">
                        {{ $row->kode_kelas }}
                    </td>

                    <td style="border:1px solid #000; text-align:center;">
                        {{ $row->siswa_nisn ?? ($row->siswa->nisn ?? '-') }}
                    </td>

                    <td style="border:1px solid #000; padding-left: 8px;">
                        {{ $row->siswa_nama ?? ($row->siswa->nama ?? '-') }}
                    </td>

                    <td style="border:1px solid #000; text-align:center;">
                        {{ $row->tgl_bayar_terakhir
                            ? \Carbon\Carbon::parse($row->tgl_bayar_terakhir)->translatedFormat('d F Y')
                            : '-' }}
                    </td>

                    <td style="border:1px solid #000; text-align:right; white-space:nowrap;">
                        {{ $sudahBayar ? \App\Utils\Angka::format($row->realisasi ?? 0, 2) : '-' }}
                    </td>

                    <td style="border:1px solid #000; text-align:center; color: {{ $sudahBayar ? 'black' : 'red' }};">
                        {{ $sudahBayar ? 'Sudah Bayar' : 'Belum Bayar' }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="border:1px solid #000; text-align:center; font-style:italic;">
                        Tidak ada siswa pada filter ini
                    </td>
                </tr>
            @endforelse
        </tbody>

        @if ($anggotaKelas->count() > 0 && ! empty($tampilkan_ringkasan))
            @php
                $totalSiswa = $total_siswa ?? $anggotaKelas->count();
                $sudahBayarCount = $total_sudah_bayar ?? $anggotaKelas->filter(fn($r) => $r->sudah_bayar ?? false)->count();
                $totalRealisasi = $total_realisasi ?? $anggotaKelas->sum(fn($r) => $r->sudah_bayar ? ($r->realisasi ?? 0) : 0);
            @endphp
            <tfoot>
                <tr style="font-weight:bold; background:#f1f5f9;">
                    <td colspan="5" style="border:1px solid #000; text-align:right; padding:6px;">
                        Total Sudah Bayar: {{ $sudahBayarCount }} / {{ $totalSiswa }} siswa
                    </td>
                    <td style="border:1px solid #000; text-align:right; white-space:nowrap; padding:6px;">
                        {{ \App\Utils\Angka::format($totalRealisasi, 2) }}
                    </td>
                    <td style="border:1px solid #000;"></td>
                </tr>
            </tfoot>
        @endif
    </table>
@endsection
