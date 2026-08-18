<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('tenant.dashboard.title') }} — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f3f5f9; }

        /* Lock page into single-screen dashboard */
        html, body { height: 100%; overflow: hidden; }
        .dash-shell { height: 100vh; display: flex; flex-direction: column; }

        .dash-main { flex: 1; min-height: 0; overflow-y: auto; }
        .dash-main::-webkit-scrollbar { width: 6px; }
        .dash-main::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }

        /* Cards / Glass */
        .card { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; box-shadow: 0 1px 2px rgba(15, 23, 42, 0.04); }
        .card-pad { padding: 14px 16px; }
        .gradient-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(102, 126, 234, .55); }
        .gradient-emerald { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(17, 153, 142, .55); }
        .gradient-amber { background: linear-gradient(135deg, #f7971e 0%, #ffd200 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(247, 151, 30, .55); }
        .gradient-sky { background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(33, 147, 176, .55); }
        .gradient-rose { background: linear-gradient(135deg, #ee0979 0%, #ff6a00 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(238, 9, 121, .55); }
        .gradient-slate { background: linear-gradient(135deg, #232526 0%, #414345 100%); color: #fff; border: none; }
        .gradient-violet { background: linear-gradient(135deg, #8e2de2 0%, #4a00e0 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(142, 45, 226, .55); }
        .gradient-ocean { background: linear-gradient(135deg, #2980b9 0%, #6dd5fa 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(41, 128, 185, .55); }
        .gradient-sunset { background: linear-gradient(135deg, #ff512f 0%, #dd2476 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(255, 81, 47, .55); }
        .gradient-mint { background: linear-gradient(135deg, #00b09b 0%, #96c93d 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(0, 176, 155, .55); }
        .gradient-aurora { background: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%); color: #fff; border: none; box-shadow: 0 10px 25px -10px rgba(0, 114, 255, .55); }

        .stat-icon { width: 38px; height: 38px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .gradient-card .stat-icon,
        .gradient-emerald .stat-icon,
        .gradient-amber .stat-icon,
        .gradient-sky .stat-icon,
        .gradient-rose .stat-icon,
        .gradient-violet .stat-icon,
        .gradient-ocean .stat-icon,
        .gradient-sunset .stat-icon,
        .gradient-mint .stat-icon,
        .gradient-aurora .stat-icon { background: rgba(255,255,255,0.22); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); }

        .scroll-thin::-webkit-scrollbar { width: 4px; height: 4px; }
        .scroll-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 2px; }

        .table-clean thead.sticky th { position: sticky; top: 0; z-index: 10; background: #f8fafc; box-shadow: inset 0 -1px 0 #e5e7eb; }

        .table-clean th { font-size: 10px; text-transform: uppercase; letter-spacing: 0.04em; color: #64748b; font-weight: 600; padding: 8px 12px; background: #f8fafc; border-bottom: 1px solid #e5e7eb; text-align: left; white-space: nowrap; }
        .table-clean td { font-size: 12px; padding: 9px 12px; border-bottom: 1px solid #f1f5f9; color: #1e293b; vertical-align: middle; }
        .table-clean tr:last-child td { border-bottom: 0; }
        .table-clean tbody tr:hover { background: #f8fafc; }

        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 9999px; font-size: 10px; font-weight: 600; }
        .badge-paid { background: #d1fae5; color: #047857; }
        .badge-unpaid { background: #fef3c7; color: #b45309; }
        .badge-active { background: #dbeafe; color: #1d4ed8; }

        .pulse-dot { width: 6px; height: 6px; border-radius: 9999px; background: #34d399; display: inline-block; box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); animation: pulse 1.6s infinite; }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(52, 211, 153, 0); }
            100% { box-shadow: 0 0 0 0 rgba(52, 211, 153, 0); }
        }

        /* RTL adjustments */
        [dir="rtl"] .table-clean th, [dir="rtl"] .table-clean td { text-align: right; }
    </style>
</head>
<body>
    <div class="dash-shell">
        @include('tenant.partials.bilah-atas')

        <main class="dash-main mx-auto w-full max-w-7xl px-3 py-3 sm:px-5 sm:py-4">
            @php
                $stats = $stats ?? [];
                $tenants = $tenants ?? [];
                $chartIncome = $chartIncome ?? ['labels' => [], 'values' => [], 'max' => 1];
                $chartTenant = $chartTenant ?? ['labels' => [], 'values' => [], 'total' => 0];

                $primaryCards = [
                    ['label' => __('tenant.dashboard.stats.total_schools'),  'value' => $stats['tenant_total'] ?? 0, 'sub' => number_format($stats['tenant_active'] ?? 0) . ' ' . __('tenant.dashboard.stats.active_schools'), 'grad' => 'gradient-violet', 'icon' => 'school'],
                    ['label' => __('tenant.dashboard.stats.invoice_total'),  'value' => $stats['invoice_total'] ?? 0, 'sub' => number_format($stats['invoice_paid'] ?? 0) . ' ' . __('tenant.dashboard.stats.invoice_paid'), 'grad' => 'gradient-emerald', 'icon' => 'invoice'],
                    ['label' => __('tenant.dashboard.stats.invoice_open'),   'value' => $stats['invoice_open'] ?? 0, 'sub' => 'Rp ' . number_format($stats['nominal_open'] ?? 0, 0, ',', '.'), 'grad' => 'gradient-sunset', 'icon' => 'open'],
                ];
            @endphp

            <header class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <div>
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">{{ __('tenant.dashboard.title') }}</h1>
                    <p class="text-xs text-slate-500">{{ __('tenant.dashboard.subtitle') }}</p>
                </div>
                <div class="flex flex-wrap items-center gap-1.5 text-[11px] font-semibold">
                    <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-indigo-700">
                        <span class="pulse-dot"></span> {{ $stats['tenant_total'] ?? 0 }} {{ __('tenant.nav.tenant') }}
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-emerald-700">
                        {{ $stats['invoice_paid'] ?? 0 }} {{ __('tenant.dashboard.tables.status_paid') }}
                    </span>
                    <span class="inline-flex items-center gap-1 rounded-full bg-amber-50 px-2.5 py-1 text-amber-700">
                        {{ $stats['invoice_open'] ?? 0 }} {{ __('tenant.dashboard.tables.status_unpaid') }}
                    </span>
                </div>
            </header>

            {{-- Primary stats --}}
            <section class="grid grid-cols-1 gap-2.5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($primaryCards as $c)
                    <article class="{{ $c['grad'] }} card card-pad flex flex-row items-center gap-3">
                        <div class="stat-icon">
                            @if ($c['icon'] === 'school')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            @elseif ($c['icon'] === 'owner')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @elseif ($c['icon'] === 'invoice')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            @else
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[10px] font-semibold uppercase tracking-wider opacity-90">{{ $c['label'] }}</p>
                            <p class="truncate text-xl font-bold leading-tight drop-shadow-sm sm:text-2xl">{{ $c['value'] }}</p>
                            <p class="truncate text-[11px] leading-tight opacity-90">{{ $c['sub'] }}</p>
                        </div>
                    </article>
                @endforeach
            </section>

            {{-- Tables row --}}
            <section class="mt-2.5 grid grid-cols-1 gap-2.5 xl:grid-cols-3 items-stretch">
                {{-- Recent Invoices --}}
                <article class="card xl:col-span-2 flex flex-col overflow-hidden min-h-[340px]">
                    <header class="flex flex-shrink-0 items-center justify-between border-b border-slate-100 px-4 py-2.5">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ __('tenant.dashboard.tables.recent_invoices') }}</p>
                            <p class="text-[11px] text-slate-400">{{ __('tenant.dashboard.stats.this_month') }}</p>
                        </div>
                        <a href="{{ route('tenant.invoice.index') }}" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700">{{ __('tenant.dashboard.tables.view_all') }} →</a>
                    </header>
                    <div class="scroll-thin flex-1 overflow-y-auto overflow-x-auto">
                        <table class="table-clean min-w-[520px] w-full">
                            <thead class="sticky top-0 z-10">
                                <tr>
                                    <th>{{ __('tenant.dashboard.tables.col_jenis') }}</th>
                                    <th>{{ __('tenant.dashboard.tables.col_tanggal') }}</th>
                                    <th>{{ __('tenant.dashboard.tables.col_sekolah') }}</th>
                                    <th class="text-right">{{ __('tenant.dashboard.tables.col_nominal') }}</th>
                                    <th class="text-center">{{ __('tenant.dashboard.tables.col_status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($invoices as $invoice)
                                    @php
                                        $invoiceTenantId = $invoice->tenant_id;
                                        $invoiceTenant = $invoiceTenantId ? collect($tenants)->firstWhere('id', $invoiceTenantId) : null;
                                    @endphp
                                    <tr>
                                        <td class="font-semibold">{{ $invoice->jenis_pembayaran }}</td>
                                        <td class="whitespace-nowrap text-slate-600">{{ $invoice->tgl_invoice?->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($invoiceTenant)
                                                <div class="font-semibold text-slate-800">{{ $invoiceTenant->nama }}</div>
                                                <div class="text-[10px] text-slate-500">{{ $invoice->user?->nama_lengkap ?? '—' }}</div>
                                            @else
                                                <span class="text-[10px] text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap text-right font-semibold tabular-nums">Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ $invoice->status === 'paid' ? 'badge-paid' : 'badge-unpaid' }}">
                                                {{ $invoice->status === 'paid' ? __('tenant.dashboard.tables.status_paid') : __('tenant.dashboard.tables.status_unpaid') }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-6 text-center text-[11px] text-slate-400">{{ __('tenant.dashboard.tables.no_data') }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>

                {{-- Recent Schools --}}
                <article class="card flex flex-col overflow-hidden min-h-[340px]">
                    <header class="flex flex-shrink-0 items-center justify-between border-b border-slate-100 px-4 py-2.5">
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-500">{{ __('tenant.dashboard.tables.recent_schools') }}</p>
                            <p class="text-[11px] text-slate-400">{{ number_format($stats['tenant_total'] ?? 0) }} {{ __('tenant.nav.tenant') }}</p>
                        </div>
                        <a href="{{ route('tenant.tenant.index') }}" class="text-[11px] font-semibold text-indigo-600 hover:text-indigo-700">{{ __('tenant.dashboard.tables.view_all') }} →</a>
                    </header>
                    <div class="scroll-thin flex-1 overflow-y-auto">
                        <ul class="divide-y divide-slate-100">
                            @forelse ($recentSchools as $s)
                                <li class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-50">
                                    <div class="min-w-0">
                                        <p class="truncate text-[12px] font-semibold text-slate-800">{{ $s->nama }}</p>
                                        <p class="truncate text-[10px] text-slate-500">{{ $s->domain_admin ?? '—' }}</p>
                                    </div>
                                    <span class="badge badge-active">{{ strtoupper(substr($s->nama ?? $s->id, 0, 2)) }}</span>
                                </li>
                            @empty
                                <li class="px-4 py-6 text-center text-[11px] text-slate-400">{{ __('tenant.dashboard.tables.no_data') }}</li>
                            @endforelse
                        </ul>
                    </div>
                </article>
            </section>

            <p class="mt-3 text-center text-[10px] text-slate-400">&copy; {{ date('Y') }} {{ env('APP_NAME') }} · {{ __('tenant.app.tagline') }}</p>
        </main>
    </div>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('msg'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: @json(session('icon') ?? 'success'), title: @json(session('msg')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif

    <script>
        // Income chart
        (function () {
            const ctx = document.getElementById('incomeChart');
            if (!ctx) return;
            const values = @json($chartIncome['values']);
            const labels = @json($chartIncome['labels']);
            const max = @json($chartIncome['max']);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: values.map(v => v > 0 ? 'rgba(79, 70, 229, 0.85)' : 'rgba(203, 213, 225, 0.5)'),
                        borderRadius: 6,
                        borderSkipped: false,
                        maxBarThickness: 28,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 }, color: '#64748b' } },
                        y: { grid: { color: '#f1f5f9' }, ticks: { display: false }, beginAtZero: true, suggestedMax: max }
                    }
                }
            });
        })();
    </script>
</body>
</html>
