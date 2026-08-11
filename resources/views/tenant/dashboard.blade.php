<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f8fafc; }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('tenant.partials.bilah-atas')

    <main class="mx-auto max-w-7xl px-4 py-4 sm:px-6 sm:py-5 lg:px-8">
        <header class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h2 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">Dasbor</h2>
                <p class="text-xs text-slate-500">Ringkasan invoice dari semua sekolah.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="inline-flex items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 font-semibold text-indigo-700">{{ ($stats['tenant_total'] ?? count($tenants ?? [])) }} sekolah</span>
                <span class="inline-flex items-center gap-1 rounded-full bg-sky-50 px-2.5 py-1 font-semibold text-sky-700">{{ $stats['owner_count'] ?? 0 }} pemilik</span>
            </div>
        </header>

        @php
            $cards = [
                ['label' => 'Total', 'value' => $stats['invoice_total'], 'color' => 'indigo'],
                ['label' => 'Lunas', 'value' => $stats['invoice_paid'], 'color' => 'emerald'],
                ['label' => 'Belum Lunas', 'value' => $stats['invoice_open'], 'color' => 'amber'],
                ['label' => 'Nominal', 'value' => 'Rp ' . number_format($stats['nominal_total'], 0, ',', '.'), 'color' => 'violet'],
            ];
        @endphp

        <section class="mt-4 grid grid-cols-2 gap-3 xl:grid-cols-4">
            @foreach ($cards as $card)
                <article class="min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-{{ $card['color'] }}-50 text-{{ $card['color'] }}-600">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>
                    </div>
                    <p class="mt-2 text-[11px] font-medium uppercase tracking-wide text-slate-500">{{ $card['label'] }}</p>
                    <p class="mt-0.5 truncate text-base font-bold tracking-tight text-slate-900 sm:text-lg">{{ $card['value'] }}</p>
                </article>
            @endforeach
        </section>

        <section class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3">
                <h3 class="text-sm font-bold text-slate-900">Invoice Terbaru</h3>
                <a href="{{ route('tenant.invoice.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">Lihat semua →</a>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-[640px] w-full text-sm">
                    <thead class="bg-slate-50 text-left text-[11px] uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-2 font-semibold">Jenis</th>
                            <th class="px-4 py-2 font-semibold">Tanggal</th>
                            <th class="px-4 py-2 font-semibold">Sekolah / Pemilik</th>
                            <th class="px-4 py-2 text-right font-semibold">Nominal</th>
                            <th class="px-4 py-2 text-center font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($invoices as $invoice)
                            @php
                                $invoiceTenantId = $invoice->tenant_id;
                                $invoiceTenant = $invoiceTenantId ? collect($tenants ?? [])->firstWhere('id', $invoiceTenantId) : null;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-2 font-semibold text-slate-800">{{ $invoice->jenis_pembayaran }}</td>
                                <td class="whitespace-nowrap px-4 py-2 text-slate-600">{{ $invoice->tgl_invoice?->format('d/m/Y') }}</td>
                                <td class="px-4 py-2">
                                    <div class="flex flex-col">
                                        @if ($invoiceTenant)
                                            <span class="text-xs font-semibold text-slate-800">{{ $invoiceTenant->nama }}</span>
                                        @elseif ($invoiceTenantId)
                                            <span class="text-xs font-mono text-slate-500">#{{ $invoiceTenantId }}</span>
                                        @else
                                            <span class="text-xs text-slate-400">—</span>
                                        @endif
                                        <span class="text-[11px] text-slate-500">{{ $invoice->user?->nama_lengkap ?? '—' }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-2 text-right font-semibold tabular-nums text-slate-900">Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if ($invoice->status === 'paid')
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Lunas</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-700">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-xs text-slate-400">Belum ada invoice.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <ul class="divide-y divide-slate-100 md:hidden">
                @forelse ($invoices as $invoice)
                    @php
                        $invoiceTenantId = $invoice->tenant_id;
                        $invoiceTenant = $invoiceTenantId ? collect($tenants ?? [])->firstWhere('id', $invoiceTenantId) : null;
                    @endphp
                    <li class="px-4 py-3.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $invoice->jenis_pembayaran }}</p>
                                <p class="mt-0.5 text-[11px] text-slate-500">{{ $invoice->tgl_invoice?->format('d/m/Y') }}</p>
                                <div class="mt-1 flex flex-col">
                                    @if ($invoiceTenant)
                                        <span class="truncate text-xs font-semibold text-slate-800">{{ $invoiceTenant->nama }}</span>
                                    @elseif ($invoiceTenantId)
                                        <span class="text-xs font-mono text-slate-500">#{{ $invoiceTenantId }}</span>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                    <span class="truncate text-[11px] text-slate-500">{{ $invoice->user?->nama_lengkap ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 flex-col items-end gap-1">
                                <span class="whitespace-nowrap text-sm font-semibold tabular-nums text-slate-900">Rp {{ number_format($invoice->jumlah, 0, ',', '.') }}</span>
                                @if ($invoice->status === 'paid')
                                    <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Lunas</span>
                                @else
                                    <span class="inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-700">Belum Lunas</span>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-4 py-10 text-center text-xs text-slate-400">Belum ada invoice.</li>
                @endforelse
            </ul>
        </section>

        <p class="mt-4 text-center text-[11px] text-slate-400">&copy; {{ date('Y') }} {{ env('APP_NAME') }}</p>
    </main>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('msg'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: @json(session('icon') ?? 'success'), title: @json(session('msg')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>