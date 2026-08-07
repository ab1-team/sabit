<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COA — Tenant {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html, body { font-family: 'Inter', system-ui, sans-serif; } body { background:#f8fafc; }</style>
</head>
<body class="min-h-screen text-slate-800">
    @include('tenant.partials.topbar')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <header class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Chart of Account (COA)</h2>
                <p class="mt-1 text-sm text-slate-500">Daftar rekening untuk tenant {{ $tenant->id }}.</p>
            </div>
        </header>

        @include('tenant.partials.tenant_subnav', ['tenant' => $tenant, 'active' => 'coa'])

        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-[640px] w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Kode Akun</th>
                            <th class="px-5 py-3 font-semibold">Nama Akun</th>
                            <th class="px-5 py-3 font-semibold">Jenis Mutasi</th>
                            <th class="px-5 py-3 text-right font-semibold">Saldo</th>
                            <th class="px-5 py-3 text-center font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($rekenings as $r)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono text-xs text-slate-700">{{ $r->kode_akun }}</td>
                                <td class="px-5 py-3 font-semibold text-slate-800">{{ $r->nama_akun }}</td>
                                <td class="px-5 py-3 text-slate-700">{{ ucfirst($r->jenis_mutasi ?? '—') }}</td>
                                <td class="px-5 py-3 text-right tabular-nums text-slate-700">Rp {{ number_format((float) $r->saldo, 0, ',', '.') }}</td>
                                <td class="px-5 py-3 text-center">
                                    @if ($r->tgl_nonaktif)
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Nonaktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Aktif</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    @if ($r->tgl_nonaktif)
                                        <form action="{{ route('tenant.tenant.coa.aktifkan', [$tenant, $r]) }}" method="POST" class="inline">
                                            @csrf @method('POST')
                                            <button class="inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600" title="Aktifkan">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('tenant.tenant.coa.nonaktifkan', [$tenant, $r]) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan rekening {{ $r->kode_akun }}?');">
                                            @csrf @method('POST')
                                            <button class="inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Nonaktifkan">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-14 text-center text-sm text-slate-400">Belum ada rekening.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <ul class="divide-y divide-slate-100 md:hidden">
                @forelse ($rekenings as $r)
                    <li class="px-4 py-3.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="font-mono text-xs text-slate-500">{{ $r->kode_akun }}</p>
                                <p class="mt-0.5 truncate text-sm font-semibold text-slate-900">{{ $r->nama_akun }}</p>
                                <p class="mt-0.5 text-xs text-slate-600">{{ ucfirst($r->jenis_mutasi ?? '—') }} · Rp {{ number_format((float) $r->saldo, 0, ',', '.') }}</p>
                                <div class="mt-1">
                                    @if ($r->tgl_nonaktif)
                                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">Nonaktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Aktif</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-1">
                                @if ($r->tgl_nonaktif)
                                    <form action="{{ route('tenant.tenant.coa.aktifkan', [$tenant, $r]) }}" method="POST" class="inline">
                                        @csrf @method('POST')
                                        <button class="inline-flex items-center rounded-md bg-emerald-100 p-1.5 text-emerald-700 hover:bg-emerald-200" title="Aktifkan">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('tenant.tenant.coa.nonaktifkan', [$tenant, $r]) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan rekening {{ $r->kode_akun }}?');">
                                        @csrf @method('POST')
                                        <button class="inline-flex items-center rounded-md bg-rose-100 p-1.5 text-rose-700 hover:bg-rose-200" title="Nonaktifkan">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-14 text-center text-sm text-slate-400">Belum ada rekening.</li>
                @endforelse
            </ul>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
