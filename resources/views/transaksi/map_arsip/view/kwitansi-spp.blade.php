<title>{{ $title }}</title>
@extends('laporan-keuangan.layout.dasar')

@section('content')
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
        }

        .kwitansi {
            width: 170mm;
            border: 1px solid #000;
            padding: 20px;
            margin: auto;
        }

        .judul {
            text-align: center;
            font-weight: bold;
            letter-spacing: 6px;
            font-size: 18px;
        }

        .subjudul {
            text-align: center;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
        }

        td {
            padding: 6px 4px;
            vertical-align: top;
        }

        .label {
            width: 25%;
        }

        .colon {
            width: 3%;
        }

        .isi {
            width: 72%;
            border-bottom: 1px solid #000;
        }

        .nominal {
            margin-top: 30px;
            width: 220px;
            border-top: 1px solid #000;
            text-align: center;
            font-weight: bold;
            padding-top: 5px;
        }

        .kanan {
            text-align: center;
        }

        .ttd {
            margin-top: 40px;
            text-align: right;
            font-weight: bold;
        }
    </style>

    <body>

        <div class="kwitansi">
            {{-- JUDUL --}}
            <div class="judul">KWITANSI</div>
            <div class="subjudul">
                Nomor : {{ $header->kode_spp ?? '-' }}
                &nbsp; Tanggal :
                {{ \App\Utils\Tanggal::tglKwitansi($header->tanggal_transaksi) }}
            </div>

            @php
                $total = $spps->isNotEmpty() ? $spps->sum('nominal') : (float) $header->getRawOriginal('jumlah');
            @endphp

            <table>
                <tr>
                    <td class="label">Telah Diterima Dari</td>
                    <td class="colon">:</td>
                    <td class="isi">
                        {{ $header->siswa->nama }}
                    </td>
                </tr>

                <tr>
                    <td class="label">Uang Sebanyak</td>
                    <td class="colon">:</td>
                    <td class="isi">
                        {{ \App\Utils\Terbilang::rupiah($total) }} Rupiah
                    </td>
                </tr>

                <tr>
                    <td class="label">Untuk Pembayaran</td>
                    <td class="colon">:</td>
                    <td class="isi">
                        {{ trim(explode(' an.', $header->keterangan)[0]) }}
                    </td>
                </tr>

                {{-- <tr>
                    <td class="label">Kode Akun</td>
                    <td class="colon">:</td>
                    <td class="isi">
                        {{ $header->rekening_debit ?? '-' }}
                    </td>
                </tr> --}}
            </table>

            <table width="100%">
                <tr>
                    <td width="50%" style="vertical-align: top;">
                        <div
                            style="
            width:220px;
            border-top:1px solid #000;
            border-bottom:1px solid #000;
            text-align:center;
            font-weight:bold;
            margin-top:30px;
            padding:8px 0;
            box-sizing:border-box;
        ">
                            {{ \App\Utils\Angka::format($total, 0) }}
                        </div>
                    </td>

                    <td width="50%"
                        style="
        vertical-align: top;
        padding-left:40px;
        text-align:center;
    ">



                        Kecamatan,
                        {{ \App\Utils\Tanggal::tglIndo($header->tanggal) }}
                        <br>

                        <div style="text-align:center; margin-top:5px;">
                            Diterima Oleh,
                        </div>

                        <div
                            style="
                    margin-top:35px;
                    text-align:center;
                    font-weight:bold;
                ">
                            {{ $header->siswa->nama }}

                        </div>
                    </td>
                </tr>
            </table>

        </div>
    </body>
@endsection
