<title>Invoice #{{ $invoice->id }}</title>

<style>
*{
    font-family: Arial, Helvetica, sans-serif;
    font-size:11px;
}

body{
    margin:25px;
}

.header{
    width:100%;
    border-top:2px solid #1c6b83;
    border-bottom:2px solid #1c6b83;
    padding:8px 0;
}

.logo{
    float:left;
    width:70px;
}

.logo img{
    width:60px;
}

.company{
    margin-left:75px;
}

.company h2{
    margin:0;
    font-size:22px;
    color:#0d5b74;
}

.company p{
    margin:1px 0;
    font-size:10px;
    color:#444;
}

.clear{
    clear:both;
}

.invoice-title{
    margin-top:15px;
}

.invoice-title h2{
    margin:0;
    font-size:28px;
}

.invoice-info{
    margin-top:3px;
}

.left{
    float:left;
    width:60%;
}

.right{
    float:right;
    width:40%;
    text-align:right;
}

.paid-status{
    color:#198754;
    font-size:24px;
    font-weight:bold;
}

.unpaid-status{
    color:#dc3545;
    font-size:24px;
    font-weight:bold;
}

.paid-logo{
    width:110px;
    height:auto;
    display:block;
    margin:8px 0 0 auto;
}

.section{
    margin-top:25px;
}

table{
    width:100%;
    border-collapse:collapse;
}

thead{
    background:#0d5b74;
    color:white;
}

thead th{
    padding:8px;
    font-size:11px;
}

tbody td{
    padding:10px;
    border-bottom:1px solid #ddd;
}

.total-table{
    width:300px;
    float:right;
    margin-top:10px;
}

.total-table td{
    padding:6px;
}

.total-table tr td:first-child{
    text-align:right;
    font-weight:bold;
}

.total-table tr td:last-child{
    background:#8da8b5;
    color:white;
    font-weight:bold;
    text-align:right;
}

.payment{
    margin-top:80px;
}

.signature{
    margin-top:70px;
    width:250px;
    float:right;
    text-align:center;
}

.signature img{
    width:140px;
}

.footer{
    margin-top:180px;
    text-align:center;
    color:#666;
    font-size:10px;
}

</style>

<div class="header">

    <div class="logo">
        @if(!empty($logo))
            <img src="data:image/{{ $logo_type }};base64,{{ $logo }}">
        @endif
    </div>

    <div class="company">
        <h2>PT. ASTA BRATA TEKNOLOGI</h2>

        <p>IT Consulting, System, Training and Digital Audits</p>
        <p>SK. Kemenkumham RI Nomor AHU-01329.40.10.2014</p>
        <p>Office : Jalan Perintis Kemerdekaan Km 1.5 Banyurip Magelang</p>
        <p>Telp : (0293) 3195558 |
            Email : info@astabratagroup.com |
            Website : astabratagroup.com
        </p>

    </div>

    <div class="clear"></div>

</div>

<div class="invoice-title">

    <div class="left">
        <h2>INVOICE #{{ $invoice->id }}</h2>

        <div class="invoice-info">
            <b>Tanggal Invoice :</b>
            {{ optional($invoice->tgl_invoice)->format('d/m/Y') }}
            <br>

            <b>Tanggal Lunas :</b>
            {{ optional($invoice->tgl_lunas)->format('d/m/Y') }}
        </div>

    </div>

    <div class="right">

        @if($invoice->status=='paid')
            <div class="paid-status">PAID</div>
            <img class="paid-logo" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/logo/lunas.png'))) }}" alt="LUNAS">
        @else
            <div class="unpaid-status">UNPAID</div>
        @endif

    </div>

    <div class="clear"></div>

</div>

<div class="section">

<b>Dikirim Kepada :</b><br><br>

@php
    $tenantModel = config('tenancy.tenant_model');
    $tenantData = $invoice->tenant_id ? $tenantModel::find($invoice->tenant_id) : null;
    $tenantNama = $tenantData && is_array($tenantData->data) ? ($tenantData->data['nama'] ?? $invoice->tenant_id) : ($invoice->tenant_id ?? '-');
    $tenantDomain = $tenantData && $tenantData->domains()->exists() ? $tenantData->domains()->first()->domain : null;
@endphp

<b>{{ $tenantNama }}</b><br>
@if($tenantDomain)
    {{ $tenantDomain }}.pembayaran-spp.test<br>
@endif
Owner: {{ $invoice->user->nama_lengkap ?? '-' }}<br>
Email: {{ $invoice->user->email ?? '-' }}

</div>

<div class="section">

<table>

<thead>

<tr>

<th width="70%">DESCRIPTION</th>

<th>TOTAL</th>

</tr>

</thead>

<tbody>

<tr>

<td align="center">

{{ $invoice->jenis_pembayaran }}<br>

Masa Aktif :
{{ optional($invoice->tgl_invoice)->format('d/m/Y') }}
-
{{ optional($invoice->tgl_lunas)->format('d/m/Y') }}

</td>

<td align="right">

{{ number_format($invoice->jumlah,2) }}

</td>

</tr>

</tbody>

</table>

<table class="total-table">

<tr>

<td>Sub Total</td>

<td>{{ number_format($invoice->jumlah,2) }}</td>

</tr>

<tr>

<td>PPN 11%</td>

<td>{{ number_format($invoice->jumlah*0.11,2) }}</td>

</tr>

<tr>

<td>Discount</td>

<td>0.00</td>

</tr>

<tr>

<td>TOTAL</td>

<td>{{ number_format($invoice->jumlah,2) }}</td>

</tr>

</table>

<div style="clear:both"></div>

</div>

<div class="payment">

<b>Pembayaran Transfer Via :</b>

<ul>

<li>Bank Mandiri xxxx</li>

<li>Bank BRI xxxx</li>

</ul>

</div>

    <div class="signature">

        Direktur Utama

        <br><br>

        @if(file_exists(public_path('assets/logo/ttd.png')))
            <img src="{{ public_path('assets/logo/ttd.png') }}" alt="Tanda tangan">
        @endif

        <br>

        <b>{{ $invoice->user->nama_lengkap ?? 'SANTOSO' }}</b>

    </div>

<div class="clear"></div>

</div>