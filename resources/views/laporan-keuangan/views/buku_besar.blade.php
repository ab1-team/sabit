@extends('laporan-keuangan.layout.dasar')
<title>{{ $judul }}</title>
@section('content')
    @php
        use App\Utils\Tanggal;

        $fmt = function ($val) {
            return $val < 0 ? '(' . \App\Utils\Angka::format(abs($val), 2) . ')' : \App\Utils\Angka::format($val, 2);
        };
    @endphp

    <table border="0" width="100%" cellspacing="0" cellpadding="0" style="font-size: 11px;">
        <tr>
            <td colspan="8" align="center">
                <div style="font-size: 18px; font-weight: bold;">BUKU BESAR {{ strtoupper($rek->nama_akun) }}</div>
                <div style="font-size: 16px; font-weight: bold;">
                    {{ strtoupper($sub_judul ?? 'Bulan ' . Tanggal::namaBulan($tgl_awal_bulan) . ' ' . $tahun) }}</div>
            </td>
        </tr>
        <tr>
            <td colspan="8" height="5"></td>
        </tr>
    </table>

    <div style="width: 100%; text-align: right; color:#000; font-weight:normal;">
        Kode Akun: {{ $rek->kode_akun ?? '-' }}
    </div>
    <table border="0" width="100%" cellspacing="0" cellpadding="3"
        style="font-size: 11px; border-collapse: collapse; table-layout: fixed;">
        <colgroup>
            <col style="width: 4%;">
            <col style="width: 10%;">
            <col style="width: 8%;">
            <col style="width: 39%;">
            <col style="width: 13%;">
            <col style="width: 13%;">
            <col style="width: 13%;">
        </colgroup>
        <tr style="background: rgb(74,74,74); color:#fff; font-weight:bold;">
            <td align="center">No</td>
            <td align="center">Tanggal</td>
            <td align="center">Ref ID.</td>
            <td align="center">Keterangan</td>
            <td align="center">Debit</td>
            <td align="center">Kredit</td>
            <td align="center">Saldo</td>
        </tr>

        @if (!empty($tampilkan_awal))
            {{-- Saldo Awal Tahun --}}
            <tr style="background: rgb(230,230,230);">
                <td></td>
                <td></td>
                <td></td>
                <td>Saldo Awal Tahun</td>
                <td align="right">0.00</td>
                <td align="right">0.00</td>
                <td align="right">{{ $fmt($saldo_awal ?? 0) }}</td>
            </tr>

            {{-- Komulatif s/d Bulan Lalu --}}
            <tr style="background: rgb(255,255,255);">
                <td></td>
                <td></td>
                <td></td>
                <td>Komulatif Transaksi s/d Bulan Lalu</td>
                <td align="right">{{ $fmt($komulatif_bulan_lalu_debit) }}</td>
                <td align="right">{{ $fmt($komulatif_bulan_lalu_kredit) }}</td>
                <td align="right">{{ $fmt($komulatif_bulan_lalu_saldo) }}</td>
            </tr>
        @endif

        {{-- Loop Transaksi Bulan Ini --}}
        @php
            $total_saldo = $saldo_awal_chunk ?? ($komulatif_bulan_lalu_saldo ?? 0);
            $total_debit = 0;
            $total_kredit = 0;
            $nomor_awal = $nomor_awal ?? 1;
        @endphp

        @forelse ($transaksi as $i => $trx)
            @php
                $debit = $trx->rekening_debit == $rek->kode_akun ? floatval($trx->jumlah) : 0;
                $kredit = $trx->rekening_kredit == $rek->kode_akun ? floatval($trx->jumlah) : 0;
                $saldo_mutasi = strtolower($rek->jenis_mutasi ?? '') === 'debet' ? $debit - $kredit : $kredit - $debit;

                $total_saldo += $saldo_mutasi;
                $total_debit += $debit;
                $total_kredit += $kredit;

                $bg = (($nomor_awal - 1) + $i) % 2 == 0 ? 'rgb(255,255,255)' : 'rgb(230,230,230)';
            @endphp
            <tr style="background: {{ $bg }}">
                <td align="center">{{ $nomor_awal + $i }}</td>
                <td align="center">{{ \App\Utils\Tanggal::tglIndo($trx->tanggal_transaksi) }}</td>
                <td align="center">{{ $trx->id }}</td>
                <td>{{ $trx->keterangan }}</td>
                <td align="right">{{ $fmt($debit) }}</td>
                <td align="right">{{ $fmt($kredit) }}</td>
                <td align="right">{{ $fmt($total_saldo) }}</td>
            </tr>
        @empty
            <tr style="background: rgb(255,255,255);">
                <td></td>
                <td></td>
                <td></td>
                <td><i>Tidak ada transaksi pada periode ini</i></td>
                <td align="right">0.00</td>
                <td align="right">0.00</td>
                <td align="right">{{ $fmt($total_saldo) }}</td>
            </tr>
        @endforelse

        @if (!empty($tampilkan_akhir))
            {{-- Total Bulan Ini --}}
            <tr style="background: rgb(233,233,233); font-weight:bold;">
                <td colspan="4">Total Transaksi Bulan {{ Tanggal::namaBulan($tgl_awal_bulan) }} {{ $tahun }}</td>
                <td align="right">{{ $fmt($total_bulan_ini['debit']) }}</td>
                <td align="right">{{ $fmt($total_bulan_ini['kredit']) }}</td>
                <td align="right"></td>
            </tr>

            {{-- Total s/d Bulan Ini --}}
            <tr style="background: rgb(210,210,210); font-weight:bold;">
                <td colspan="4">Total Transaksi sampai dengan Bulan {{ Tanggal::namaBulan($tgl_awal_bulan) }}
                    {{ $tahun }}</td>
                <td align="right">{{ $fmt($total_sampai_bulan_ini['debit']) }}</td>
                <td align="right">{{ $fmt($total_sampai_bulan_ini['kredit']) }}</td>
                <td align="right">{{ $fmt($total_saldo) }}</td>
            </tr>

            {{-- Total Komulatif Tahun --}}
            <tr style="background: rgb(190,190,190); font-weight:bold;">
                <td colspan="4">Total Transaksi Komulatif sampai dengan Tahun {{ $tahun }}</td>
                <td align="right">{{ $fmt($total_tahun_ini['debit']) }}</td>
                <td align="right">{{ $fmt($total_tahun_ini['kredit']) }}</td>
                <td align="right"></td>
            </tr>
        @endif

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
