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

    <table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-size: 11px; table-layout: fixed;">
        {{--
            Catatan: dompdf mengabaikan tag <colgroup>, jadi lebar kolom
            harus ditulis pada sel baris header.
        --}}
        <thead>
            <tr style="background: rgb(74, 74, 74); font-weight: bold; color: #fff;">
                <th width="4%" align="center">No</th>
                <th width="10%" align="center">Tanggal</th>
                <th width="7%" align="center">Ref ID.</th>
                <th width="10%" align="center">Kd. Rek</th>
                <th width="33%" align="center" style="padding-left: 8px;">Keterangan</th>
                <th width="18%" align="center">Debit</th>
                <th width="18%" align="center">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @php
                // Total global (dihitung di controller) dipakai agar baris "Total"
                // tetap konsisten walaupun laporan dipecah menjadi beberapa bagian.
                // Kalau total global belum tersedia, hitung sendiri dari baris ini.
                $pakaiTotalGlobal = isset($total_debit) && isset($total_kredit);
                $totalDebit  = $pakaiTotalGlobal ? (float) $total_debit : 0.0;
                $totalKredit = $pakaiTotalGlobal ? (float) $total_kredit : 0.0;
                $nomor_awal  = $nomor_awal ?? 1;
            @endphp

            @forelse ($transaksis as $i => $trx)
                @php
                    $rowClass = (($nomor_awal - 1) + $i) % 2 == 0 ? 'row-white' : 'row-black';
                    $no = $nomor_awal + $i;
                @endphp

                {{-- Baris DEBIT --}}
                <tr class="{{ $rowClass }}">
                    <td align="center">{{ $no }}</td>
                    <td align="center">{{ $tglUtil->tglIndo($trx->tanggal_transaksi) }}</td>
                    <td align="center">{{ $trx->id }}</td>
                    <td align="center">{{ $trx->rekeningDebit->kode_akun ?? '' }}</td>
                    <td style="padding-left: 8px;">{{ $trx->rekeningDebit->nama_akun ?? $trx->keterangan }}</td>
                    <td align="right" style="white-space: nowrap;">{{ \App\Utils\Angka::format($trx->jumlah, 2) }}</td>
                    <td align="right" style="white-space: nowrap;">{{ \App\Utils\Angka::format(0, 2) }}</td>
                </tr>

                {{-- Baris KREDIT --}}
                <tr class="{{ $rowClass }}">
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td align="center">{{ $no }}</td>
                    <td align="center">{{ $trx->rekeningKredit->kode_akun ?? '' }}</td>
                    <td style="padding-left: 8px;">{{ $trx->rekeningKredit->nama_akun ?? $trx->keterangan }}</td>
                    <td align="right" style="white-space: nowrap;">{{ \App\Utils\Angka::format(0, 2) }}</td>
                    <td align="right" style="white-space: nowrap;">{{ \App\Utils\Angka::format($trx->jumlah, 2) }}</td>
                </tr>

                @php
                    if (! $pakaiTotalGlobal) {
                        $totalDebit  += $trx->jumlah;
                        $totalKredit += $trx->jumlah;
                    }
                @endphp
            @empty
                <tr class="row-white">
                    <td colspan="7" align="center"><i>Tidak ada transaksi pada periode ini</i></td>
                </tr>
            @endforelse

            @if (!empty($tampilkan_akhir))
                {{-- Total --}}
                <tr style="background-color: #d3d3d3; font-weight: bold;">
                    <td colspan="5" align="center"><strong>Total</strong></td>
                    <td align="right" style="white-space: nowrap;">{{ \App\Utils\Angka::format($totalDebit, 2) }}</td>
                    <td align="right" style="white-space: nowrap;">{{ \App\Utils\Angka::format($totalKredit, 2) }}</td>
                </tr>
            @endif
        </tbody>
    </table>
    @if (!empty($tampilkan_akhir))
        <table class="table table-bordered" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td>
                    <div class="ttd-wrapper">
                        {!! $ttd->tanda_tangan ?? '' !!}
                    </div>
                </td>
            </tr>
        </table>
    @endif
@endsection
