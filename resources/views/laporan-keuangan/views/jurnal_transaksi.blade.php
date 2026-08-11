@php
    use App\Utils\Tanggal;
    $tglUtil = new Tanggal();
@endphp
<title>{{ $title }} {{ $sub_judul }}</title>

@extends('laporan-keuangan.layout.dasar')
@section('content')
    <style>
        .row-white {
            background-color: #fff;
        }

        .row-black {
            background-color: #f3f3f3;
        }
    </style>

    <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td colspan="8" align="center" style="padding: 4px 0;">
                <div style="font-size: 18px !important; margin: 0; line-height: 1.2;">
                    <b style="font-size: inherit;">{{ strtoupper($judul) }}</b>
                </div>
                <div style="font-size: 16px !important; margin: 0; line-height: 1.2;">
                    <b style="font-size: inherit;">{{ strtoupper($sub_judul) }}</b>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="4" height="8"></td>
        </tr>
    </table>

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <thead>
            <tr style="background: rgb(74, 74, 74); font-weight: bold; color: #fff;">
                <th align="center" width="4%">No</th>
                <th align="center" width="10%">Tanggal</th>
                <th align="center" width="8%">Ref ID.</th>
                <th align="center" width="10%">Kd. Rek</th>
                <th align="center" width="33%">Keterangan</th>
                <th align="center" width="15%">Debit</th>
                <th align="center" width="15%">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalDebit = 0;
                $totalKredit = 0;
            @endphp

            @foreach ($transaksis as $i => $trx)
                @php
                    $rowClass = $i % 2 == 0 ? 'row-white' : 'row-black';
                @endphp

                {{-- Baris DEBIT --}}
                <tr class="{{ $rowClass }}">
                    <td align="center">{{ $i + 1 }}</td>
                    <td align="center">{{ $tglUtil->tglIndo($trx->tanggal_transaksi) }}</td>
                    <td align="center">{{ $trx->id }}</td>
                    <td align="center">{{ $trx->rekeningDebit->kode_akun ?? '' }}</td>
                    <td>{{ $trx->rekeningDebit->nama_akun ?? $trx->keterangan }}</td>
                    <td align="right">{{ \App\Utils\Angka::format($trx->jumlah, 2) }}</td>
                    <td align="right">{{ \App\Utils\Angka::format(0, 2) }}</td>
                </tr>

                {{-- Baris KREDIT --}}
                <tr class="{{ $rowClass }}">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="center">{{ $i + 1 }}</td>
                    <td align="center">{{ $trx->rekeningKredit->kode_akun ?? '' }}</td>
                    <td>{{ $trx->rekeningKredit->nama_akun ?? $trx->keterangan }}</td>
                    <td align="right">{{ \App\Utils\Angka::format(0, 2) }}</td>
                    <td align="right">{{ \App\Utils\Angka::format($trx->jumlah, 2) }}</td>
                </tr>

                @php
                    $totalDebit += $trx->jumlah;
                    $totalKredit += $trx->jumlah;
                @endphp
            @endforeach

            {{-- Total --}}
            <tr style="background-color: #d3d3d3; font-weight: bold;">
                <td colspan="5" align="center"><strong>Total</strong></td>
                <td align="right">{{ \App\Utils\Angka::format($totalDebit, 2) }}</td>
                <td align="right">{{ \App\Utils\Angka::format($totalKredit, 2) }}</td>
            </tr>
        </tbody>
    </table>
    <table class="table table-bordered" width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
                <div class="ttd-wrapper">
                    {!! $ttd->tanda_tangan ?? '' !!}
                </div>
            </td>
        </tr>
    </table>
@endsection
