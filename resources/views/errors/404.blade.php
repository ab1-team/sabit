<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Halaman Tidak Ditemukan — {{ config('app.name') }}</title>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        .container { max-width: 640px; margin: 80px auto; padding: 32px; background: #fff; border-radius: 12px; box-shadow: 0 4px 16px rgba(0,0,0,.06); }
        h1 { font-size: 28px; margin: 0 0 8px; color: #dc2626; }
        .lead { font-size: 16px; color: #475569; margin: 0 0 24px; }
        .host { display: inline-block; padding: 4px 10px; background: #f1f5f9; border-radius: 6px; font-family: monospace; font-size: 14px; color: #0f172a; margin: 4px 0; }
        .actions { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 24px; }
        .btn { padding: 10px 18px; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; }
        .btn-primary { background: #4f46e5; color: #fff; }
        .btn-secondary { background: #e2e8f0; color: #1e293b; }
        .help { margin-top: 28px; padding: 16px; background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 6px; font-size: 14px; color: #78350f; }
        .help code { background: #fde68a; padding: 2px 6px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>404 — Halaman Tidak Ditemukan</h1>
        <p class="lead">Domain <span class="host">{{ $host ?? request()->getHost() }}</span> tidak dikenali atau halaman yang diminta tidak tersedia.</p>

        <div class="actions">
            @if (app(\App\Support\HostContext::class) && \App\Support\HostContext::isCentral(request()->getHost()))
                <a href="{{ route('tenant.login') }}" class="btn btn-primary">Login Tenant Console</a>
            @else
                <a href="https://{{ request()->getHost() }}/login" class="btn btn-primary">Coba Login Sekolah</a>
            @endif
            <a href="javascript:history.back()" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="help">
            <strong>Untuk administrator:</strong><br />
            Domain ini belum terdaftar di tabel <code>domains</code> pada database central.
            Tambahkan domain sekolah (mis. <code>al-islam.sch.id</code>) via panel pusat,
            lalu jalankan <code>php artisan cache:clear</code>.
        </div>
    </div>
</body>
</html>