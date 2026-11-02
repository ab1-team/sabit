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
        {{--
            Lebar kolom (persen). Catatan: dompdf mengabaikan tag <colgroup>,
            jadi lebar harus ditulis pada sel baris header. Persentase kolom
            "No" dan "Ref ID." diperkecil supaya kolom Keterangan & angka
            (Debit/Kredit/Saldo) mendapat ruang lebih lega.
        --}}
        <tr style="background: rgb(74,74,74); color:#fff; font-weight:bold;">
            <td width="4%" align="center">No</td>
            <td width="10%" align="center">Tanggal</td>
            <td width="7%" align="center">Ref ID.</td>
            <td width="35%" align="center" style="padding-left: 8px;">Keterangan</td>
            <td width="15%" align="center">Debit</td>
            <td width="15%" align="center">Kredit</td>
            <td width="14%" align="center">Saldo</td>
        </tr>

        @if (!empty($tampilkan_awal))
            {{-- Saldo Awal Tahun --}}
            <tr style="background: rgb(230,230,230);">
                <td></td>
                <td></td>
                <td></td>
                <td style="padding-left: 8px;">Saldo Awal Tahun</td>
                <td align="right" style="white-space: nowrap;">0.00</td>
                <td align="right" style="white-space: nowrap;">0.00</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($saldo_awal ?? 0) }}</td>
            </tr>

            {{-- Komulatif s/d Bulan Lalu --}}
            <tr style="background: rgb(255,255,255);">
                <td></td>
                <td></td>
                <td></td>
                <td style="padding-left: 8px;">Komulatif Transaksi s/d Bulan Lalu</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($komulatif_bulan_lalu_debit) }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($komulatif_bulan_lalu_kredit) }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($komulatif_bulan_lalu_saldo) }}</td>
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
                <td style="padding-left: 8px;">{{ $trx->keterangan }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($debit) }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($kredit) }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_saldo) }}</td>
            </tr>
        @empty
            <tr style="background: rgb(255,255,255);">
                <td></td>
                <td></td>
                <td></td>
                <td style="padding-left: 8px;"><i>Tidak ada transaksi pada periode ini</i></td>
                <td align="right" style="white-space: nowrap;">0.00</td>
                <td align="right" style="white-space: nowrap;">0.00</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_saldo) }}</td>
            </tr>
        @endforelse

        @if (!empty($tampilkan_akhir))
            {{-- Total Bulan Ini --}}
            <tr style="background: rgb(233,233,233); font-weight:bold;">
                <td colspan="4" style="padding-left: 8px;">Total Transaksi Bulan {{ Tanggal::namaBulan($tgl_awal_bulan) }} {{ $tahun }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_bulan_ini['debit']) }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_bulan_ini['kredit']) }}</td>
                <td align="right"></td>
            </tr>

            {{-- Total s/d Bulan Ini --}}
            <tr style="background: rgb(210,210,210); font-weight:bold;">
                <td colspan="4" style="padding-left: 8px;">Total Transaksi sampai dengan Bulan {{ Tanggal::namaBulan($tgl_awal_bulan) }}
                    {{ $tahun }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_sampai_bulan_ini['debit']) }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_sampai_bulan_ini['kredit']) }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_saldo) }}</td>
            </tr>

            {{-- Total Komulatif Tahun --}}
            <tr style="background: rgb(190,190,190); font-weight:bold;">
                <td colspan="4" style="padding-left: 8px;">Total Transaksi Komulatif sampai dengan Tahun {{ $tahun }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_tahun_ini['debit']) }}</td>
                <td align="right" style="white-space: nowrap;">{{ $fmt($total_tahun_ini['kredit']) }}</td>
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
