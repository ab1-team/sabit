@php
    $navItems = [
        ['route' => 'tenant.dashboard', 'match' => 'tenant.dashboard*', 'label' => __('tenant.nav.dashboard'), 'icon' => 'home'],
        ['route' => 'tenant.tenant.index', 'match' => 'tenant.tenant.*', 'label' => __('tenant.nav.tenant'), 'icon' => 'tenant'],
        ['route' => 'tenant.hak-akses.index', 'match' => 'tenant.hak-akses.*', 'label' => __('tenant.nav.hak_akses'), 'icon' => 'shield'],
        ['route' => 'tenant.migrasi.siswa', 'match' => 'tenant.migrasi.*', 'label' => __('tenant.nav.migrasi'), 'icon' => 'migrasi'],
        ['route' => 'tenant.invoice.index', 'match' => 'tenant.invoice.*', 'label' => __('tenant.nav.invoice'), 'icon' => 'doc'],
    ];
    $admin = Auth::guard('tenant')->user();
    $currentLocale = app()->getLocale();
    $availableLocales = [
        'id' => ['label' => 'ID', 'name' => 'Bahasa Indonesia'],
        'en' => ['label' => 'EN', 'name' => 'English'],
        'ar' => ['label' => 'AR', 'name' => 'العربية'],
    ];
@endphp
<link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
<style>
    #nprogress .bar { background: #37d17c !important; height: 3px !important; }
    #nprogress .peg { box-shadow: 0 0 10px #37d17c, 0 0 5px #37d17c !important; }
    #nprogress .spinner-icon { border-top-color: #37d17c !important; border-left-color: #37d17c !important; }

    /* Side drawer (mobile nav) */
    .drawer-backdrop { background: rgba(15, 23, 42, 0.55); }
    .drawer-panel { width: 280px; max-width: 85vw; transform: translateX(-100%); height: 100dvh; max-height: 100dvh; }
    .drawer-backdrop { opacity: 0; }
    .drawer-shown .drawer-panel { transform: translateX(0); }
    .drawer-shown .drawer-backdrop { opacity: 1; }
    .drawer-backdrop, .drawer-panel { transition: opacity .25s ease, transform .25s ease; }
    /* Safe-area untuk iPhone notch */
    .drawer-safe-top { padding-top: max(env(safe-area-inset-top), .5rem); }
    .drawer-safe-bottom { padding-bottom: max(env(safe-area-inset-bottom), .5rem); }
</style>

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur" style="position: sticky; top: 0; z-index: 30;">
    <div class="mx-auto max-w-7xl px-3 sm:px-6 lg:px-8">
        <div class="flex min-h-16 items-center justify-between gap-2 sm:gap-3">
            {{-- Hamburger (mobile only) --}}
            <button type="button" id="drawer-toggle" aria-controls="side-drawer" aria-expanded="false" class="inline-flex h-10 w-10 flex-shrink-0 cursor-pointer items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100 md:hidden" style="touch-action: manipulation;">
                <span class="sr-only">Buka navigasi</span>
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            {{-- Brand --}}
            <div class="flex min-w-0 flex-shrink-0 items-center gap-3">
                <a href="{{ route('tenant.dashboard') }}" aria-label="{{ __('tenant.app.name') }}" class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-md shadow-indigo-500/20">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </a>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('tenant.app.name') }}</p>
                    <h1 class="truncate text-sm font-bold text-slate-900 sm:text-base">{{ env('APP_NAME') }}</h1>
                </div>
            </div>

            {{-- Desktop nav --}}
            <nav class="hidden flex-1 items-center justify-center gap-1 md:flex">
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['match'] ?? $item['route']); @endphp
                    <a href="{{ route($item['route']) }}" class="inline-flex items-center gap-2 rounded-lg px-3.5 py-2 text-sm font-medium transition {{ $active ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                        @if ($item['icon'] === 'home')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8a1 1 0 001 1h3m4-9l2 2m-2-2v8a1 1 0 01-1 1h-3m0 0v-6a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 001 1m-6 0h6"/></svg>
                        @elseif ($item['icon'] === 'tenant')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        @elseif ($item['icon'] === 'shield')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @elseif ($item['icon'] === 'migrasi')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5 5 5M12 5v12"/></svg>
                        @elseif ($item['icon'] === 'cash')
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @else
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Right cluster (lang + account + logout) --}}
            <div class="flex flex-shrink-0 items-center gap-1 sm:gap-2">
                {{-- Language switcher --}}
                <div class="relative" id="lang-switcher">
                    <button type="button" id="lang-switcher-button" aria-haspopup="true" aria-expanded="false" class="inline-flex items-center gap-1 rounded-lg px-2 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/></svg>
                        <span class="uppercase">{{ $currentLocale }}</span>
                    </button>
                    <div id="lang-switcher-menu" role="menu" class="absolute right-0 mt-2 hidden w-44 origin-top-right overflow-hidden rounded-xl border border-slate-200 bg-white shadow-lg ring-1 ring-black/5">
                        @foreach ($availableLocales as $code => $meta)
                            <form method="POST" action="{{ route('tenant.locale.switch') }}" role="none">
                                @csrf
                                <input type="hidden" name="locale" value="{{ $code }}">
                                <button type="submit" role="menuitem" class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm {{ $currentLocale === $code ? 'bg-indigo-50 font-semibold text-indigo-700' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <span class="truncate">{{ $meta['name'] }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">{{ $meta['label'] }}</span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <div class="hidden items-center gap-3 border-r border-slate-200 pr-3 sm:flex">
                    <div class="min-w-0 text-right">
                        <p class="max-w-[10rem] truncate text-sm font-semibold leading-tight text-slate-800">{{ $admin->nama_lengkap }}</p>
                        <p class="max-w-[10rem] truncate text-xs leading-tight text-slate-500">{{ $admin->email }}</p>
                    </div>
                    <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 font-bold text-indigo-700 ring-2 ring-white">
                        {{ strtoupper(mb_substr($admin->nama_lengkap ?? 'M', 0, 1)) }}
                    </div>
                </div>

                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-sm font-bold text-indigo-700 ring-2 ring-white sm:hidden" title="{{ $admin->nama_lengkap }}">
                    {{ strtoupper(mb_substr($admin->nama_lengkap ?? 'M', 0, 1)) }}
                </div>

                <form id="logout-form" action="{{ route('tenant.logout') }}" method="POST" class="hidden sm:block">
                    @csrf
                    <button type="button" onclick="confirmLogout(event)" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100 hover:text-rose-600 focus:outline-none focus:ring-4 focus:ring-indigo-100 sm:px-4">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l4-4m-4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden sm:inline">{{ __('tenant.app.logout') }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>

{{-- Side drawer (mobile + tablet) --}}
<div id="side-drawer" class="md:hidden" aria-hidden="true" style="position: fixed; inset: 0; z-index: 50; display: none;">
    <div id="drawer-backdrop" class="drawer-backdrop absolute inset-0"></div>
    <aside class="drawer-panel relative flex flex-col bg-white shadow-2xl">
        <div class="drawer-safe-top flex items-center justify-between border-b border-slate-200 px-4 py-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 shadow-md shadow-indigo-500/20">
                    <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                </span>
                <div class="min-w-0">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-slate-400">{{ __('tenant.app.name') }}</p>
                    <p class="truncate text-sm font-bold text-slate-900">{{ env('APP_NAME') }}</p>
                </div>
            </div>
            <button type="button" id="drawer-close" class="inline-flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                <span class="sr-only">Tutup</span>
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="border-b border-slate-200 bg-slate-50 px-4 py-3">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-base font-bold text-indigo-700 ring-2 ring-white">
                    {{ strtoupper(mb_substr($admin->nama_lengkap ?? 'M', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $admin->nama_lengkap }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $admin->email }}</p>
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto px-2 py-3" aria-label="Navigasi utama">
            <div class="space-y-0.5">
                @foreach ($navItems as $item)
                    @php $active = request()->routeIs($item['match'] ?? $item['route']); @endphp
                    <a href="{{ route($item['route']) }}" data-drawer-link class="group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $active ? 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100' : 'text-slate-700 hover:bg-slate-100' }}">
                        @if ($item['icon'] === 'home')
                            <svg class="h-5 w-5 {{ $active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7m-9 2v8a1 1 0 001 1h3m4-9l2 2m-2-2v8a1 1 0 01-1 1h-3m0 0v-6a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 001 1m-6 0h6"/></svg>
                        @elseif ($item['icon'] === 'tenant')
                            <svg class="h-5 w-5 {{ $active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        @elseif ($item['icon'] === 'shield')
                            <svg class="h-5 w-5 {{ $active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        @elseif ($item['icon'] === 'migrasi')
                            <svg class="h-5 w-5 {{ $active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5-5 5 5M12 5v12"/></svg>
                        @elseif ($item['icon'] === 'cash')
                            <svg class="h-5 w-5 {{ $active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        @else
                            <svg class="h-5 w-5 {{ $active ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-500' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        @endif
                        <span>{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </nav>

        <div class="drawer-safe-bottom border-t border-slate-200 p-3">
            <button type="button" onclick="confirmLogout(event)" class="flex w-full items-center justify-center gap-2 rounded-lg bg-rose-50 px-4 py-2.5 text-sm font-semibold text-rose-700 hover:bg-rose-100 focus:outline-none focus:ring-4 focus:ring-rose-100">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l4-4m-4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>{{ __('tenant.app.logout') }}</span>
            </button>
        </div>
    </aside>
</div>

<script>
    (function () {
        const drawer = document.getElementById('side-drawer');
        const toggle = document.getElementById('drawer-toggle');
        const closeBtn = document.getElementById('drawer-close');
        const backdrop = document.getElementById('drawer-backdrop');
        if (!drawer || !toggle) return;

        function openDrawer() {
            drawer.style.display = 'block';
            drawer.classList.add('drawer-shown');
            drawer.setAttribute('aria-hidden', 'false');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            drawer.classList.remove('drawer-shown');
            drawer.setAttribute('aria-hidden', 'true');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            setTimeout(function () { drawer.style.display = 'none'; }, 250);
        }

        toggle.addEventListener('click', function () {
            if (drawer.classList.contains('drawer-shown')) closeDrawer();
            else openDrawer();
        });
        if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
        if (backdrop) backdrop.addEventListener('click', closeDrawer);

        drawer.querySelectorAll('[data-drawer-link]').forEach(function (a) {
            a.addEventListener('click', function () { closeDrawer(); });
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && drawer.classList.contains('drawer-shown')) closeDrawer();
        });
    })();

    // Language switcher
    (function () {
        const btn = document.getElementById('lang-switcher-button');
        const menu = document.getElementById('lang-switcher-menu');
        if (!btn || !menu) return;
        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = !menu.classList.toggle('hidden');
            btn.setAttribute('aria-expanded', String(isOpen));
        });
        document.addEventListener('click', function (e) {
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add('hidden');
                btn.setAttribute('aria-expanded', 'false');
            }
        });
    })();

    function confirmLogout(e) {
        e.preventDefault();
        if (typeof Swal === 'undefined') {
            if (confirm('Logout?')) document.getElementById('logout-form').submit();
            return;
        }
        Swal.fire({
            title: @json(__('tenant.app.logout_confirm_title')),
            text: @json(__('tenant.app.logout_confirm_text')),
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: @json(__('tenant.app.logout_confirm_yes')),
            cancelButtonText: @json(__('tenant.app.logout_confirm_no')),
            reverseButtons: true,
            buttonsStyling: false,
            customClass: {
                confirmButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-rose-600 text-white font-semibold text-sm hover:bg-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-200 mx-1',
                cancelButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 focus:outline-none focus:ring-4 focus:ring-slate-200 mx-1',
            },
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }
</script>
<script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
<script>
    if (window.NProgress) {
        NProgress.configure({ showSpinner: false, trickleSpeed: 120, minimum: 0.2 });
        document.addEventListener('click', function (e) {
            var a = e.target.closest && e.target.closest('a');
            if (!a) return;
            var href = a.getAttribute('href');
            if (!href || href.startsWith('#') || href.startsWith('javascript:') || a.target === '_blank' || e.ctrlKey || e.metaKey || e.shiftKey) return;
            if (a.hasAttribute('data-no-progress') || a.hasAttribute('data-bs-toggle') || a.hasAttribute('data-toggle')) return;
            if (a.origin && a.origin !== window.location.origin) return;
            NProgress.start();
        });
        window.addEventListener('beforeunload', function () { NProgress.set(0.9); });
        window.addEventListener('pageshow', function (e) { if (e.persisted) NProgress.remove(); else NProgress.done(true); });
    }
</script>
