@php
    $jumlahTotal  = (float) $invoice->jumlah;
    $ppnRate      = 0.11;
    $subTotal     = $jumlahTotal > 0 ? round($jumlahTotal / (1 + $ppnRate), 2) : 0.0;
    $ppn          = round($jumlahTotal - $subTotal, 2);
    $diskon       = 0.0;
    $grandTotal   = round($jumlahTotal - $diskon, 2);
    $isPaid       = $invoice->status === 'paid';
    $tenantNama   = $tenant_nama ?? ($invoice->tenant_id ?? '-');
@endphp
<title>Invoice #{{ $invoice->id }}</title>

<style>
    *{
        font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
        font-size: 10.5px;
        color: #1e293b;
    }

    body{
        margin: 0;
        padding: 22px 26px;
    }

    /* ---------- Header ---------- */
    .header{
        width: 100%;
        border-top: 3px solid #4f46e5;
        border-bottom: 1px solid #e2e8f0;
        padding: 12px 0 14px 0;
    }

    .header-row{
        width: 100%;
    }

    .logo{
        width: 96px;
        vertical-align: top;
    }

    .logo img{
        width: 82px;
    }

    .company{
        vertical-align: top;
    }

    .company h2{
        margin: 0 0 3px 0;
        font-size: 19px;
        color: #4338ca;
        letter-spacing: 0.2px;
    }

    .company .tagline{
        margin: 0 0 6px 0;
        font-size: 9.5px;
        color: #64748b;
    }

    .company p{
        margin: 1px 0;
        font-size: 9px;
        color: #475569;
    }

    /* ---------- Judul ---------- */
    .doc-title{
        margin-top: 20px;
        width: 100%;
    }

    .doc-title .left{
        width: 62%;
        vertical-align: top;
    }

    .doc-title .right{
        width: 38%;
        text-align: right;
        vertical-align: top;
    }

    .doc-title h2{
        margin: 0;
        font-size: 26px;
        color: #0f172a;
        letter-spacing: 1px;
    }

    .doc-title .sub{
        margin: 2px 0 0 0;
        font-size: 10px;
        color: #64748b;
    }

    .invoice-meta{
        margin-top: 14px;
        width: 100%;
    }

    .invoice-meta td{
        padding: 2px 8px 2px 0;
        font-size: 10px;
        vertical-align: top;
    }

    .invoice-meta td.label{
        width: 110px;
        color: #64748b;
    }

    .invoice-meta td.value{
        color: #0f172a;
        font-weight: bold;
    }

    .paid-status{
        color: #059669;
        font-size: 22px;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .unpaid-status{
        color: #e11d48;
        font-size: 22px;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .paid-logo{
        width: 104px;
        display: block;
        margin: 6px 0 0 auto;
    }

    /* ---------- Section ---------- */
    .section{
        margin-top: 22px;
    }

    .section-title{
        margin: 0 0 6px 0;
        font-size: 11px;
        font-weight: bold;
        color: #4338ca;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 4px;
    }

    /* ---------- Tabel item ---------- */
    table.items{
        width: 100%;
        border-collapse: collapse;
    }

    table.items thead th{
        background: #4f46e5;
        color: #ffffff;
        padding: 8px 10px;
        font-size: 9.5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
    }

    table.items tbody td{
        padding: 11px 10px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 10.5px;
        vertical-align: top;
    }

    table.items tbody td.amount{
        text-align: right;
        font-weight: bold;
        white-space: nowrap;
    }

    .item-sub{
        color: #64748b;
        font-size: 9px;
    }

    /* ---------- Total ---------- */
    .totals{
        width: 100%;
        margin-top: 14px;
    }

    .totals td{
        padding: 5px 10px;
        font-size: 10px;
    }

    .totals td.label{
        text-align: right;
        color: #475569;
        width: 180px;
    }

    .totals td.value{
        text-align: right;
        font-weight: bold;
        white-space: nowrap;
    }

    .totals tr.grand td{
        background: #4f46e5;
        color: #ffffff;
        font-size: 12px;
        font-weight: bold;
        padding-top: 8px;
        padding-bottom: 8px;
    }

    .totals tr.grand td.label{
        color: #ffffff;
    }

    /* ---------- Kontak & tanda tangan ---------- */
    .bottom{
        margin-top: 34px;
        width: 100%;
    }

    .bottom td{
        vertical-align: top;
    }

    .contact{
        width: 52%;
    }

    .contact p{
        margin: 2px 0;
        font-size: 9.5px;
        color: #475569;
    }

    .contact .head{
        font-size: 10px;
        font-weight: bold;
        color: #4338ca;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 5px;
    }

    .signature{
        width: 44%;
        text-align: center;
    }

    .signature .sig-label{
        font-size: 10px;
        color: #475569;
        margin-bottom: 2px;
    }

    .signature img{
        width: 132px;
    }

    .signature .sig-name{
        margin-top: 4px;
        font-size: 10px;
        font-weight: bold;
        color: #0f172a;
    }

    .signature .sig-role{
        font-size: 9px;
        color: #64748b;
    }

    .footer{
        margin-top: 30px;
        text-align: center;
        color: #94a3b8;
        font-size: 8.5px;
        border-top: 1px solid #f1f5f9;
        padding-top: 8px;
    }
</style>

{{-- ================= HEADER ================= --}}
<div class="header">
    <table class="header-row">
        <tr>
            <td class="logo">
                @if(!empty($logo))
                    <img src="data:image/png;base64,{{ $logo }}" alt="Logo">
                @endif
            </td>
            <td class="company">
                <h2>PT. ASTA BRATA TEKNOLOGI</h2>
                <p class="tagline">IT Consulting, System, Training and Digital Audits</p>
                <p>SK. Kemenkumham RI Nomor AHU-01329.40.10.2014</p>
                <p>Office : Jalan Perintis Kemerdekaan Km 1.5 Banyurip, Magelang</p>
                <p>Telp : (0293) 3195558 &nbsp;|&nbsp; Email : info@astabratagroup.com &nbsp;|&nbsp; Web : astabratagroup.com</p>
            </td>
        </tr>
    </table>
</div>

{{-- ================= JUDUL ================= --}}
<div class="doc-title">
    <table style="width:100%;">
        <tr>
            <td class="left">
                <h2>INVOICE</h2>
                <p class="sub">Nomor : #{{ str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT) }}</p>

                <table class="invoice-meta">
                    <tr>
                        <td class="label">Tanggal Invoice</td>
                        <td class="value">{{ optional($invoice->tgl_invoice)->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tanggal Lunas</td>
                        <td class="value">{{ optional($invoice->tgl_lunas)->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Pembayaran</td>
                        <td class="value">{{ $invoice->jenis_pembayaran }}</td>
                    </tr>
                </table>
            </td>
            <td class="right">
                @if($isPaid)
                    <div class="paid-status">LUNAS</div>
                    @if(!empty($lunas))
                        <img class="paid-logo" src="data:image/png;base64,{{ $lunas }}" alt="LUNAS">
                    @endif
                @else
                    <div class="unpaid-status">BELUM LUNAS</div>
                @endif
            </td>
        </tr>
    </table>
</div>

{{-- ================= DITERIMA OLEH ================= --}}
<div class="section">
    <p class="section-title">Dikirim Kepada</p>
    <p style="margin:0;font-size:11px;font-weight:bold;color:#0f172a;">{{ $tenantNama }}</p>
    <p style="margin:2px 0 0 0;font-size:9.5px;color:#64748b;">
        Pemilik : {{ $invoice->user->nama_lengkap ?? '-' }}
        @if($invoice->user->email)
            &nbsp;|&nbsp; {{ $invoice->user->email }}
        @endif
    </p>
</div>

{{-- ================= RINCIAN ================= --}}
<div class="section">
    <table class="items">
        <thead>
            <tr>
                <th width="70%">Keterangan</th>
                <th style="text-align:right;">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    {{ $invoice->jenis_pembayaran }}
                    <div class="item-sub">
                        Masa Aktif :
                        {{ optional($invoice->tgl_invoice)->format('d/m/Y') ?? '-' }}
                        s.d.
                        {{ optional($invoice->tgl_lunas)->format('d/m/Y') ?? '-' }}
                    </div>
                </td>
                <td class="amount">Rp {{ number_format($jumlahTotal, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td class="label">Sub Total (belum termasuk PPN)</td>
            <td class="value">Rp {{ number_format($subTotal, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">PPN 11%</td>
            <td class="value">Rp {{ number_format($ppn, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td class="label">Diskon</td>
            <td class="value">Rp {{ number_format($diskon, 0, ',', '.') }}</td>
        </tr>
        <tr class="grand">
            <td class="label">TOTAL</td>
            <td class="value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
        </tr>
    </table>
</div>

{{-- ================= KONTAK & TTD ================= --}}
<div class="bottom">
    <table style="width:100%;">
        <tr>
            <td class="contact">
                <p class="head">Informasi Pembayaran</p>
                <p>Silakan lakukan pembayaran melalui transfer ke rekening perusahaan:</p>
                <p><b>PT. ASTA BRATA TEKNOLOGI</b></p>
                <p>Nomor rekening dapat diperoleh dengan menghubungi:</p>
                <p>Telp : (0293) 3195558 &nbsp;|&nbsp; Email : info@astabratagroup.com</p>
                <p style="margin-top:6px;">Sertakan nomor invoice <b>#{{ str_pad((string) $invoice->id, 5, '0', STR_PAD_LEFT) }}</b> sebagai berita transfer.</p>
            </td>
            <td class="signature">
                <div class="sig-label">Diterima oleh,</div>
                @if(!empty($ttd))
                    <img src="data:image/png;base64,{{ $ttd }}" alt="Tanda tangan">
                @endif
                <div class="sig-name">{{ $invoice->user->nama_lengkap ?? '-' }}</div>
                <div class="sig-role">Pemilik {{ $tenantNama }}</div>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    Invoice ini dibuat secara otomatis oleh sistem {{ env('APP_NAME') }} — PT. ASTA BRATA TEKNOLOGI<br>
    {{ \Carbon\Carbon::now()->translatedFormat('d F Y \p\u\k\k\ j\H\i') }}
</div>
