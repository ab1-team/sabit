@php
    $tenant = $tenant ?? null;
    $active = $active ?? '';
    $subNav = [
        ['key' => 'profil', 'route' => 'tenant.tenant.profil.index', 'match' => 'tenant.tenant.profil.*', 'label' => 'Profil Sekolah', 'icon' => 'building'],
        ['key' => 'user', 'route' => 'tenant.tenant.user.index', 'match' => 'tenant.tenant.user.*', 'label' => 'User Operator', 'icon' => 'users'],
        ['key' => 'tahun-akademik', 'route' => 'tenant.tenant.tahun-akademik.index', 'match' => 'tenant.tenant.tahun-akademik.*', 'label' => 'Tahun Akademik', 'icon' => 'calendar'],
        ['key' => 'jenis-pembayaran', 'route' => 'tenant.tenant.jenis-pembayaran.index', 'match' => 'tenant.tenant.jenis-pembayaran.*', 'label' => 'Jenis Pembayaran', 'icon' => 'cash'],
        ['key' => 'coa', 'route' => 'tenant.tenant.coa.index', 'match' => 'tenant.tenant.coa.*', 'label' => 'COA', 'icon' => 'tree'],
    ];
@endphp

@if ($tenant)
    <section class="mt-6 rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tenant</p>
                <h3 class="truncate text-base font-bold text-slate-900 sm:text-lg">{{ $tenant->nama_sekolah ?? $tenant->id }}</h3>
                <p class="mt-0.5 truncate font-mono text-xs text-slate-500">{{ $tenant->id }}{{ $tenant->domains->first() ? ' · '.$tenant->domains->first()->domain : '' }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('tenant.tenant.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                    Kembali
                </a>
                @if ($tenant->domains->first())
                    <a href="http://{{ $tenant->domains->first()->domain }}" target="_blank" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 015.656 0l1.415 1.415a4 4 0 010 5.656l-3 3a4 4 0 01-5.656 0M10.172 13.828a4 4 0 01-5.656 0l-1.415-1.415a4 4 0 010-5.656l3-3a4 4 0 015.656 0"/></svg>
                        Buka
                    </a>
                @endif
            </div>
        </div>

        <nav class="overflow-x-auto px-2 py-2">
            <ul class="flex min-w-max items-center gap-1">
                @foreach ($subNav as $item)
                    @php $isActive = request()->routeIs($item['match']); @endphp
                    <li>
                        <a href="{{ route($item['route'], $tenant) }}" class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg px-3 py-2 text-sm font-medium transition {{ $isActive ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            @if ($item['icon'] === 'home')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8a1 1 0 001 1h3m4-9l2 2m-2-2v8a1 1 0 01-1 1h-3m0 0v-6a1 1 0 011-1h2a1 1 0 011 1v6"/></svg>
                            @elseif ($item['icon'] === 'building')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            @elseif ($item['icon'] === 'users')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @elseif ($item['icon'] === 'calendar')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            @elseif ($item['icon'] === 'cash')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            @elseif ($item['icon'] === 'tree')
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>
                            @endif
                            {{ $item['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    </section>
@endif
