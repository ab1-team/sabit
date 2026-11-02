@php

    $formatRupiah = function ($angka) {
        if ($angka === null || $angka === '') {
            return '';
        }

        if ($angka < 0) {
            return '(' . \App\Utils\Angka::format(abs($angka), 2) . ')';
        }
        return \App\Utils\Angka::format($angka, 2);
    };

    $total_ns_debit = $total_ns_kredit = 0;
    $total_rl_debit = $total_rl_kredit = 0;
    $total_n_debit = $total_n_kredit = 0;
    $total_pendapatan = $total_beban = 0;
@endphp
<title>{{ $title }}</title>
@extends('laporan-keuangan.layout.dasar')

@section('content')
    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr>
            <td colspan="3" align="center">
                <div style="font-size: 18px; font-weight:bold; margin: 0; line-height: 1.2;">
                    {{ strtoupper($judul) }}
                </div>
                <div style="font-size: 16px; font-weight:bold; margin: 0; line-height: 1.2;">
                    BULAN {{ strtoupper($sub_judul) }}
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" height="5"></td>
        </tr>
    </table>

    <table width="100%" cellspacing="0" cellpadding="0"
        style="font-size: 11px; border-collapse: collapse; border: 1px solid #313131; table-layout: fixed;">
        {{--
            Catatan: dompdf mengabaikan tag <colgroup>, jadi lebar kolom
            harus ditulis pada sel baris header. Kolom Rekening diperkecil
            supaya kolom angka (Debit/Kredit) lebih lega.
        --}}
        <thead>
            <tr style="background: #c4c4c4; font-weight: 600; text-align: center;">
                <th rowspan="2" width="25%" style="border: 0.2px solid #313131; width: 25%; padding: 4px; padding-left: 8px;">Rekening</th>
                <th colspan="2" width="25%" style="border: 0.2px solid #313131; width: 25%;">Neraca Saldo</th>
                <th colspan="2" width="25%" style="border: 0.2px solid #313131; width: 25%;">Laba Rugi</th>
                <th colspan="2" width="25%" style="border: 0.2px solid #313131; width: 25%;">Neraca</th>
            </tr>
            <tr style="background: #c4c4c4; font-weight: 600; text-align: center;">
                <th width="12.5%" style="border: 0.2px solid #313131; width: 12.5%;">Debit</th>
                <th width="12.5%" style="border: 0.2px solid #313131; width: 12.5%;">Kredit</th>
                <th width="12.5%" style="border: 0.2px solid #313131; width: 12.5%;">Debit</th>
                <th width="12.5%" style="border: 0.2px solid #313131; width: 12.5%;">Kredit</th>
                <th width="12.5%" style="border: 0.2px solid #313131; width: 12.5%;">Debit</th>
                <th width="12.5%" style="border: 0.2px solid #313131; width: 12.5%;">Kredit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rekening as $rek)
                @php
                    $debit = $rek->total_debit;
                    $kredit = $rek->total_kredit;

                    $normal = strtolower(substr((string) $rek->jenis_mutasi, 0, 1));
                    $isDebetNormal = ($normal === 'd');

                    // pindahkan sisi jika negatif
                    if ($isDebetNormal) {
                        $selisih = $debit - $kredit;
                        $saldo_debit = $selisih >= 0 ? $selisih : 0;
                        $saldo_kredit = $selisih < 0 ? abs($selisih) : 0;
                    } else {
                        $selisih = $kredit - $debit;
                        $saldo_kredit = $selisih >= 0 ? $selisih : 0;
                        $saldo_debit = $selisih < 0 ? abs($selisih) : 0;
                    }

                    // Neraca Saldo
                    $ns_debit = $saldo_debit;
                    $ns_kredit = $saldo_kredit;
                    $total_ns_debit += $ns_debit;
                    $total_ns_kredit += $ns_kredit;

                    $rl_debit = $rl_kredit = $n_debit = $n_kredit = 0;

                    if ($rek->lev1 >= 4) {
                        $rl_debit = $saldo_debit;
                        $rl_kredit = $saldo_kredit;
                        $total_rl_debit += $rl_debit;
                        $total_rl_kredit += $rl_kredit;

                        if ($rek->lev1 == 4) {
                            $total_pendapatan += $isDebetNormal ? $saldo_debit : $saldo_kredit;
                        }
                        if ($rek->lev1 == 5) {
                            $total_beban += $isDebetNormal ? $saldo_debit : $saldo_kredit;
                        }
                    } else {
                        $n_debit = $saldo_debit;
                        $n_kredit = $saldo_kredit;
                        $total_n_debit += $n_debit;
                        $total_n_kredit += $n_kredit;
                    }
                @endphp

                <tr>
                    <td style="border: 1px solid #313131; padding: 4px; padding-left: 8px;">
                        {{ $rek->kode_akun }} - {{ $rek->nama_akun }}
                    </td>
                    <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($ns_debit) }}</td>
                    <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($ns_kredit) }}</td>
                    <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($rl_debit) }}</td>
                    <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($rl_kredit) }}</td>
                    <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($n_debit) }}</td>
                    <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($n_kredit) }}</td>
                </tr>
            @endforeach
        </tbody>
        @php
            $surplus_defisit = $total_pendapatan - $total_beban;
        @endphp
        <tr style="background: #d6d6d6; font-weight: bold;">
            <td style="border: 1px solid #313131; text-align: center;">Surplus / Defisit</td>
            <td colspan="2"></td>
            <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($surplus_defisit) }}</td>
            <td></td>
            <td></td>
            <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($surplus_defisit) }}</td>
        </tr>
        <tr style="background: #f5f5f5; font-weight: bold;">
            <td style="border: 1px solid #313131; text-align: center;">Jumlah</td>
            <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($total_ns_debit) }}</td>
            <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($total_ns_kredit) }}</td>
            <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">
                {{ $formatRupiah($total_rl_debit + $surplus_defisit) }}</td>
            <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($total_rl_kredit) }}</td>
            <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">{{ $formatRupiah($total_n_debit) }}</td>
            <td style="border: 1px solid #313131; text-align: right; white-space: nowrap;">
                {{ $formatRupiah($total_n_kredit + $surplus_defisit) }}</td>
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
