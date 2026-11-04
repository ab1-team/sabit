<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tagihan Belum Lunas — {{ \App\Models\Profil::namaLembaga() }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        body {
            margin: 0;
            min-height: 100vh;
            background: linear-gradient(160deg, #eef2ff 0%, #f8fafc 45%, #fff1f2 100%);
            color: #1e293b;
        }
        .wrap { max-width: 940px; margin: 0 auto; padding: 2.5rem 1rem 3rem; }
        .card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 18px 40px -24px rgba(15, 23, 42, .35);
            overflow: hidden;
        }
        .card-head {
            padding: 1.5rem 1.75rem;
            background: linear-gradient(135deg, #e11d48 0%, #f43f5e 60%, #fb7185 100%);
            color: #fff;
        }
        .card-body { padding: 1.75rem; }
        .alert {
            display: flex;
            gap: .9rem;
            padding: 1rem 1.15rem;
            border-radius: .85rem;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            color: #7c2d12;
            font-size: .875rem;
            line-height: 1.6;
        }
        .alert b { color: #9a3412; }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .6rem;
            border-radius: 999px;
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .02em;
        }
        .badge-danger { background: #ffe4e6; color: #be123c; }
        .badge-warn { background: #fef3c7; color: #b45309; }
        table { width: 100%; border-collapse: collapse; font-size: .875rem; }
        thead th {
            text-align: left;
            font-size: .6875rem;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            color: #64748b;
            background: #f8fafc;
            padding: .7rem .85rem;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }
        tbody td { padding: .8rem .85rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        tbody tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: #f8fafc; }
        .num { text-align: right; white-space: nowrap; font-variant-numeric: tabular-nums; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .6rem 1.05rem;
            border-radius: .65rem;
            font-size: .8125rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all .15s ease;
        }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-primary:hover { background: #4338ca; }
        .btn-ghost { background: #fff; color: #475569; border-color: #cbd5e1; }
        .btn-ghost:hover { background: #f1f5f9; }
        .kicker { font-size: .6875rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #fb7185; }
        .title { margin: .35rem 0 .5rem; font-size: 1.5rem; font-weight: 800; line-height: 1.25; }
        .sub { margin: 0; font-size: .875rem; color: #475569; opacity: .9; }
        .stat { padding: 1rem 1.15rem; border: 1px solid #e2e8f0; border-radius: .85rem; background: #fff; }
        .stat .label { font-size: .6875rem; font-weight: 600; letter-spacing: .05em; text-transform: uppercase; color: #94a3b8; }
        .stat .value { margin-top: .3rem; font-size: 1.25rem; font-weight: 800; color: #0f172a; }
        .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: .85rem; margin-bottom: 1.5rem; }
        @media (max-width: 720px) {
            .grid { grid-template-columns: 1fr; }
            .table-scroll { overflow-x: auto; }
        }
        .steps { counter-reset: s; list-style: none; margin: 0; padding: 0; }
        .steps li {
            counter-increment: s;
            position: relative;
            padding-left: 2.25rem;
            margin-bottom: .95rem;
            font-size: .875rem;
            color: #475569;
            line-height: 1.6;
        }
        .steps li::before {
            content: counter(s);
            position: absolute;
            left: 0;
            top: 0;
            width: 1.5rem;
            height: 1.5rem;
            border-radius: 999px;
            background: #eef2ff;
            color: #4338ca;
            font-size: .75rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .steps b { color: #1e293b; }
    </style>
</head>

<body>
    <div class="wrap">
        <div class="card">
            <div class="card-head">
                <div class="kicker">Pembayaran Diperlukan</div>
                <h1 class="title">Tagihan Lisensi Belum Lunas</h1>
                <p class="sub">
                    {{ \App\Models\Profil::namaLembaga() }} — Anda tidak dapat masuk ke aplikasi
                    sekolah sampai tagihan lisensi di bawah ini dilunasi.
                </p>
                @if (auth()->check())
                    <p class="sub" style="margin-top:.6rem;">
                        <i class="bi bi-info-circle"></i>
                        Anda masih masuk sebagai <strong>{{ auth()->user()->nama ?? auth()->user()->username }}</strong>.
                        Silakan lakukan pembayaran terlebih dahulu.
                    </p>
                @endif
            </div>

            <div class="card-body">
                <div class="alert">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:1.25rem;line-height:1.4;"></i>
                    <div>
                        <b>Akses dibatasi.</b> Login ke aplikasi sekolah ditolak sampai seluruh
                        tagihan di bawah ini berstatus lunas.
                        Pembayaran dilakukan melalui transfer ke rekening perusahaan —
                        nomor rekening diperoleh dengan menghubungi tim kami.
                    </div>
                </div>

                <div class="grid" style="margin-top:1.5rem;">
                    <div class="stat">
                        <div class="label">Total Tagihan</div>
                        <div class="value">{{ $summary['total'] }}</div>
                    </div>
                    <div class="stat">
                        <div class="label">Terlambat</div>
                        <div class="value" style="color:#be123c;">{{ $summary['terlambat'] }}</div>
                    </div>
                    <div class="stat">
                        <div class="label">Total Nominal</div>
                        <div class="value" style="font-size:1.1rem;">{{ $summary['nominal_fmt'] }}</div>
                    </div>
                </div>

                @if (count($summary['invoices']) > 0)
                    <div class="table-scroll">
                        <table>
                            <thead>
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Jenis Pembayaran</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Status</th>
                                    <th class="num">Nominal</th>
                                    <th class="num">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($summary['invoices'] as $inv)
                                    <tr>
                                        <td style="font-family:ui-monospace,monospace;font-size:.8125rem;">{{ $inv['nomor'] }}</td>
                                        <td style="font-weight:600;color:#1e293b;">{{ $inv['jenis_pembayaran'] }}</td>
                                        <td style="white-space:nowrap;">
                                            {{ $inv['tgl_lunas'] ?? '—' }}
                                            @if ($inv['terlambat'])
                                                <div style="font-size:.6875rem;color:#be123c;font-weight:600;">
                                                    Terlambat {{ abs((int) $inv['sisa_hari']) }} hari
                                                </div>
                                            @elseif ($inv['sisa_hari'] !== null)
                                                <div style="font-size:.6875rem;color:#64748b;">
                                                    {{ $inv['sisa_hari'] }} hari lagi
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($inv['terlambat'])
                                                <span class="badge badge-danger"><i class="bi bi-exclamation-circle"></i> Terlambat</span>
                                            @else
                                                <span class="badge badge-warn"><i class="bi bi-clock"></i> Belum Lunas</span>
                                            @endif
                                        </td>
                                        <td class="num" style="font-weight:700;">{{ $inv['jumlah_fmt'] }}</td>
                                        <td class="num">
                                            <a class="btn btn-ghost" style="padding:.4rem .7rem;font-size:.75rem;"
                                               href="{{ route('app.tagihan.print', ['invoice' => $inv['id']]) }}" target="_blank">
                                                <i class="bi bi-file-earmark-pdf"></i> Cetak
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <h2 style="margin:1.75rem 0 .85rem;font-size:1rem;font-weight:700;">Cara menyelesaikan pembayaran</h2>
                <ol class="steps">
                    <li>Cetak <b>invoice</b> pada tabel di atas untuk melihat rincian tagihan.</li>
                    <li>Lakukan <b>transfer</b> ke rekening perusahaan. Nomor rekening dapat diperoleh dengan menghubungi
                        <b>{{ config('support.telepon_tagihan') }}</b> atau <b>{{ config('support.email_tagihan') }}</b>.</li>
                    <li>Sertakan nomor invoice (mis. <b>INV-00001</b>) sebagai <b>berita transfer</b>.</li>
                    <li>Konfirmasi pembayaran ke tim kami. Tagihan akan ditandai lunas dan akun Anda otomatis aktif kembali.</li>
                </ol>

                {{-- Kontak dukungan teknis ABT --}}
                <div style="margin-top:1.5rem;padding:1.1rem 1.25rem;border:1px solid #e2e8f0;border-radius:.85rem;background:#f8fafc;">
                    <div style="font-size:.875rem;font-weight:700;color:#1e293b;margin-bottom:.15rem;">
                        <i class="bi bi-headset" style="color:#4f46e5;"></i>
                        {{ config('support.nama') }}
                    </div>
                    <div style="font-size:.8125rem;color:#64748b;margin-bottom:.85rem;">
                        Jika ada kendalaTagihan atau pembayaran, hubungi tim teknis kami.
                    </div>
                    <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
                        <a href="mailto:{{ config('support.email') }}" class="btn btn-ghost"
                            style="padding:.45rem .8rem;font-size:.78rem;">
                            <i class="bi bi-envelope"></i> {{ config('support.email') }}
                        </a>
                        <a href="https://wa.me/{{ config('support.whatsapp') }}" target="_blank" rel="noopener"
                            class="btn btn-ghost" style="padding:.45rem .8rem;font-size:.78rem;">
                            <i class="bi bi-whatsapp"></i> WhatsApp
                        </a>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', config('support.telepon')) }}" class="btn btn-ghost"
                            style="padding:.45rem .8rem;font-size:.78rem;">
                            <i class="bi bi-telephone"></i> {{ config('support.telepon') }}
                        </a>
                    </div>
                </div>

                <div style="display:flex;flex-wrap:wrap;gap:.6rem;margin-top:1.75rem;padding-top:1.35rem;border-top:1px solid #e2e8f0;">
                    <a class="btn btn-primary" href="{{ url('/login') }}">
                        <i class="bi bi-arrow-clockwise"></i> Coba Login Lagi
                    </a>
                    <a class="btn btn-ghost"
                        href="mailto:{{ config('support.email') }}?subject=Konfirmasi Pembayaran Lisensi">
                        <i class="bi bi-envelope"></i> Hubungi Tim Kami
                    </a>
                    @if (auth()->check())
                        <form method="POST" action="{{ route('app.tagihan.logout') }}" style="margin:0;">
                            @csrf
                            <button type="submit" class="btn btn-ghost" style="width:100%;">
                                <i class="bi bi-box-arrow-right"></i> Keluar
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <p style="margin-top:1.25rem;text-align:center;font-size:.75rem;color:#94a3b8;">
            {{ config('app.name') }} — {{ config('support.perusahaan') }}
        </p>
    </div>
</body>

</html>
