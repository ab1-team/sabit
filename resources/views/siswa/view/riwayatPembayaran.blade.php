<title>{{ $title }}</title>
@extends('laporan-keuangan.layout.dasar')

@section('content')
    @php
        use App\Utils\Tanggal;
    @endphp
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin: 0 0 15px 0;
        }

        /* =======================
           TABLE DATA
        ======================== */
        .table-data {
            width: 100%;
            border-collapse: collapse;
        }

        .table-data th,
        .table-data td {
            border: 1px solid #000;
            padding: 6px;
        }

        .table-data th {
            background-color: #f2f2f2;
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        /* =======================
           TABLE TANDA TANGAN
        ======================== */
        .table-signature {
            width: 100%;
            border-collapse: collapse;
        }

        .table-signature td {
            padding: 30px 0;
            text-align: center;
        }
    </style>

    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr>
            <td colspan="8" align="center">
                <div style="font-size: 18px; font-weight: bold;">RIWAYAT PEMBAYARAN SISWA</div>
            </td>
        </tr>
        <tr>
            <td colspan="8" height="8"></td>
        </tr>
    </table>
    <br>

    <body>
        <table border="0">
            <tr>
                <td width="40%">Nama Siswa </td>
                <td width="60%">: {{ $siswa->nama }}</td>
            </tr>
            <tr>
                <td width="40%">Kelas Siswa </td>
                <td width="60%">: {{ $siswa->kode_kelas }}</td>
            </tr>
            <tr>
                <td width="40%">Tahun Akademik </td>
                <td width="60%">: {{ $siswa->tahun_akademik }}</td>
            </tr>
        </table>
        <br>
        <table class="table-data">
            <thead>
                <tr>
                    <th align="center" width="15%">Tanggal</th>
                    <th width="15%">Alokasi</th>
                    <th width="60%">Keterangan</th>
                    <th width="10%" class="text-end">Nominal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($riwayat as $item)
                    <tr>
                        <td>{{ $item->tanggal_transaksi }}</td>
                        <td>
                            @if ($item->spp)
                                {{ Tanggal::namabulan($item->spp->tanggal) }}
                            @else
                                Daftar Ulang
                            @endif
                        </td>
                        <td>{{ $item->keterangan }}</td>
                        <td class="text-end">{{ \App\Utils\Angka::format($item->jumlah, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">Belum ada data</td>
                    </tr>
                @endforelse

                <tr>
                    <th colspan="3" class="text-end">Total Bayar</th>
                    <th class="text-end">{{ \App\Utils\Angka::format($riwayat->sum('jumlah'), 2) }}</th>
                </tr>
            </tbody>
        </table>
        <table class="table-signature">
            <tr>
                <td width="50%">
                    Mengetahui<br><br><br>
                    <strong>Kepala Sekolah</strong>
                </td>
                <td width="50%">
                    <br><br><br>
                    <strong>Kasir</strong>
                </td>
            </tr>
        </table>
    </body>
@endsection
