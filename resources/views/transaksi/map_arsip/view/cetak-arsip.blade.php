@php
    use App\Utils\Tanggal;
@endphp
<title>{{ $title }}</title>
@extends('laporan-keuangan.layout.dasar')
@section('content')

    <head>
        <style>
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                margin: 10px 20px 15px 20px;
                /* atas diperkecil */
            }

            h2,
            h3 {
                text-align: center;
                margin: 0;
            }

            table {
                width: 100%;
                border-collapse: collapse;
            }

            table.data {
                margin-top: 10px;
                /* jarak aman sebelum tabel data */
            }

            table.data th,
            table.data td {
                border: 1px solid #000;
                padding: 6px 8px;
            }

            th {
                background-color: #f0f0f0;
            }
        </style>
    </head>

    <body>

        <!-- JUDUL -->
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
            <tr>
                <td align="center">
                    <div style="font-size:18px;font-weight:bold;">PEMBAYARAN SPP</div>
                </td>
            </tr>
            <tr>
                <td colspan="5" height="8"></td>
            </tr>
        </table>

        <!-- IDENTITAS -->
        <table style="border:none;margin-top:10px;">
            <tr>
                <td style="border:none;" width="15%">Nama Siswa</td>
                <td style="border:none;" width="85%">: {{ $header->siswa->nama }}</td>
            </tr>
            <tr>
                <td style="border:none;">Kelas</td>
                <td style="border:none;">: {{ $header->siswa->kode_kelas }}</td>
            </tr>
            <tr>
                <td style="border:none;">Tanggal Cetak</td>
                <td style="border:none;">: {{ Tanggal::tglIndo(now()) }}</td>
            </tr>
        </table>

        <!-- DATA TRANSAKSI -->
        <table class="data">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Nominal</th>
                </tr>
            </thead>
            <tbody>
                @php $total = 0; @endphp
                @foreach ($transaksis as $index => $transaksi)
                    <tr>
                        <td align="center">{{ $index + 1 }}</td>
                        <td>{{ Tanggal::tglIndo($transaksi->tanggal) }}</td>
                        <td>{{ $transaksi->keterangan ?? '-' }}</td>
                        <td align="right">{{ \App\Utils\Angka::format($transaksi->getRawOriginal('jumlah'), 2) }}</td>
                    </tr>
                    @php $total += (float) $transaksi->getRawOriginal('jumlah'); @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3">TOTAL</th>
                    <th align="right">
                        {{ \App\Utils\Angka::format($total, 2) }}
                    </th>
                </tr>
            </tfoot>
        </table>

    </body>
@endsection
