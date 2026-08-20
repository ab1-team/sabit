<title>{{ $title }}</title>

@php

    $index = 1;
    $total_bulan_ini = 0;
    $saldo_bulan_lalu = $saldo_bulan_lalu ?? 0;

    function formatKas($jumlah)
    {
        return $jumlah < 0 ? '(' . \App\Utils\Angka::format(abs($jumlah), 2) . ')' : \App\Utils\Angka::format($jumlah, 2);
    }
@endphp

@extends('laporan-keuangan.layout.dasar')

@section('content')
    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr>
            <td colspan="4" align="center">
                <div style="font-size: 18px; font-weight:bold; line-height: 1.2;">ARUS KAS</div>
                <div style="font-size: 16px; font-weight:bold; line-height: 1.2;">{{ strtoupper($sub_judul) }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="3" height="8"></td>
        </tr>
    </table>

    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr style="background: rgb(200,200,200)">
            <th colspan="2">Nama Akun</th>
            <th>Jumlah</th>
        </tr>
        <tr>
            <td colspan="4" height="8"></td>
        </tr>

        @foreach ($arus_kas as $ak)
            @php
                $dot = substr($ak->nama_akun, 1, 1);
                $bg = $dot == '.' ? '150,150,150' : '128,128,128';
                $romawi = !($dot == '.' && $ak->id != '2') && $ak->id != '9' ? $index : 0;
                $sub_total = 0;
                $total = 0;
            @endphp

            <tr style="background: rgb({{ $bg }})">
                <td width="5%" align="center">{{ $romawi ?: '' }}</td>
                <td width="80%">{{ $ak->nama_akun }}</td>
                <td width="15%" align="right">
                    @if ($ak->id == 1)
                        {{ formatKas($saldo_bulan_lalu) }}
                    @endif
                </td>
            </tr>

            @php $number = 0; @endphp
            @foreach ($ak->child as $child)
                @php
                    $akun3 = $child->rek_debit ?: $child->rek_kredit;
                @endphp

                @if ($akun3)
                    @php
                        $number++;
                        $bg = $number % 2 == 0 ? '240,240,240' : '200,200,200';
                        $jumlah = 0;

                        // Akumulasi jumlah dari transaksi_list (sudah disiapkan controller):
                        //   debit -> +;   kredit -> -.
                        foreach (($child->transaksi_list ?? collect()) as $trx) {
                            $inRange = $trx->tanggal_transaksi >= $tgl_awal_bulan
                                    && $trx->tanggal_transaksi <= $tgl_akhir_bulan;
                            if (!$inRange) continue;
                            if (in_array($trx->rekening_debit, $akun3->rekeningByPrefix()->pluck('kode_akun')->all())) {
                                $jumlah += (float) $trx->jumlah;
                            }
                            if (in_array($trx->rekening_kredit, $akun3->rekeningByPrefix()->pluck('kode_akun')->all())) {
                                $jumlah -= (float) $trx->jumlah;
                            }
                        }

                        $sub_total += $jumlah;
                        $total += $jumlah;
                    @endphp

                    <tr style="background: rgb({{ $bg }})">
                        <td>&nbsp;</td>
                        <td>{{ $akun3->nama_akun }}</td>
                        <td align="right">{{ formatKas($jumlah) }}</td>
                    </tr>
                @endif
            @endforeach

            @if ($dot == '.')
                <tr style="background: rgb(150,150,150); font-weight:bold;">
                    <td>&nbsp;</td>
                    <td>Jumlah {{ substr(ucwords(strtolower($ak->nama_akun)), 3) }}</td>
                    <td align="right">{{ formatKas($sub_total) }}</td>
                </tr>
                <tr>
                    <td colspan="4" height="8"></td>
                </tr>
            @endif

            @php
                if (!($dot == '.' && $ak->id != '2') && $ak->id != '9') {
                    $index++;
                    $section_arr = explode(' ', $ak->nama_akun);
                    $section_name = ucwords(strtolower(end($section_arr)));
                    $title = 'Kas Bersih yang diperoleh dari aktivitas ' . $section_name;
                    $title .= $section_name == 'Operasi' ? ' (A-B-C)' : ' (A-B)';
                    $total_bulan_ini += $total;
                } else {
                    continue;
                }
            @endphp

            <tr style="background: rgb(128,128,128)">
                <td>&nbsp;</td>
                <td>{{ $title }}</td>
                <td align="right">{{ formatKas($total) }}</td>
            </tr>
        @endforeach

        <tr>
            <td colspan="3" style="padding: 0 !important;">
                <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
                    <tr style="background: rgb(128,128,128)">
                        <td width="5%" align="center">&nbsp;</td>
                        <td width="80%">Kenaikan (Penurunan) Kas</td>
                        <td width="15%" align="right">{{ formatKas($total_bulan_ini) }}</td>
                    </tr>
                    <tr style="background: rgb(128,128,128); font-weight:bold;">
                        <td>&nbsp;</td>
                        <td>SALDO AKHIR KAS SETARA KAS</td>
                        <td align="right">{{ formatKas($total_bulan_ini + $saldo_bulan_lalu) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
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
