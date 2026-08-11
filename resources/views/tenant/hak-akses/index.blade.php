<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hak Akses per Lokasi — Master {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0-rc.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f8fafc; }
        .menu-scroll { max-height: 38vh; overflow-y: auto; }
        .menu-scroll::-webkit-scrollbar { width: 8px; }
        .menu-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .menu-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        .cb { accent-color: #4f46e5; cursor: pointer; }
        details > summary { list-style: none; cursor: pointer; }
        details > summary::-webkit-details-marker { display: none; }
        details > summary::marker { display: none; }
        details[open] .chev { transform: rotate(90deg); }
        .chev { transition: transform .18s ease; }
        .menu-row:hover { background: #f8fafc; }
        .group-card { box-shadow: 0 1px 0 rgba(15, 23, 42, .04), 0 2px 4px rgba(15, 23, 42, .02); }
        .toolbar-btn { transition: all .15s ease; }
        .toolbar-btn:hover { transform: translateY(-1px); }
        .menu-grid { container-type: inline-size; }
        @container (min-width: 1024px) {
            .menu-grid-inner { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        }
        @container (min-width: 640px) {
            .menu-grid-inner { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .invoice-input { width: 100%; min-height: 36px; border: 1px solid #cbd5e1; border-radius: .5rem; background: #fff; padding: .4rem .75rem; font-size: .8125rem; color: #1e293b; transition: border-color .15s ease, box-shadow .15s ease; }
        .invoice-input::placeholder { color: #94a3b8; }
        .invoice-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('tenant.partials.bilah-atas')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Hak Akses per Lokasi</h2>
                <p class="mt-1 text-sm text-slate-500">Atur menu yang dapat diakses oleh setiap user di setiap sekolah. Semua lokasi ditampilkan sekaligus.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-xs font-semibold text-slate-600 shadow-sm sm:px-3" title="Jumlah lokasi yang memiliki owner (admin)">
                    <svg class="h-3.5 w-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="text-slate-400">Pemilik</span>
                    <span class="text-slate-900">{{ collect($perTenant)->filter(fn ($p) => collect($p['users'])->contains(fn ($u) => $u['username'] === 'admin'))->count() }}</span>
                </div>
                <button id="expand-all" type="button" title="Buka semua" aria-label="Buka semua" class="toolbar-btn inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:border-indigo-200 hover:text-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100 sm:px-3">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                    <span class="hidden sm:inline">Buka semua</span>
                </button>
                <button id="collapse-all" type="button" title="Tutup semua" aria-label="Tutup semua" class="toolbar-btn inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2.5 py-2 text-xs font-semibold text-slate-700 shadow-sm hover:border-indigo-200 hover:text-indigo-600 focus:outline-none focus:ring-4 focus:ring-indigo-100 sm:px-3">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.75M15 9V4.75M9 15v4.25M15 15v4.25M4.75 9H9m6 0h4.25M4.75 15H9m6 0h4.25"/></svg>
                    <span class="hidden sm:inline">Tutup semua</span>
                </button>
            </div>
        </header>

        @forelse ($tenants as $t)
            @php
                $payload = $perTenant[$t->id] ?? ['grouped' => collect(), 'users' => []];
                $users = $payload['users'];
                $grouped = $payload['grouped'];
                $menuCount = $grouped->sum(fn ($g) => $g['parents']->count());
                $tenantName = $t->nama_sekolah ?? $t->id;
                $domain = optional($t->domains->first())->domain ?? '—';
            @endphp
            <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm tenant-block" data-tenant-id="{{ $t->id }}">
                <header class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white shadow-sm shadow-indigo-500/20">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="truncate text-base font-bold text-slate-900">{{ $tenantName }}</h3>
                                <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                    <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 015.656 0l1.415 1.415a4 4 0 010 5.656l-3 3a4 4 0 01-5.656 0M10.172 13.828a4 4 0 01-5.656 0l-1.415-1.415a4 4 0 010-5.656l3-3a4 4 0 015.656 0"/></svg>
                                    {{ $domain }}
                                </span>
                            </div>
                            <p class="mt-0.5 font-mono text-xs text-slate-500">tenant{{ $t->id }} · {{ count($users) }} user · {{ $menuCount }} menu</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" class="add-user-btn inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-3.5 py-2 text-xs font-semibold text-white shadow-sm shadow-indigo-600/20 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                            Tambah User
                        </button>
                        <a href="http://{{ $domain }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 015.656 0l1.415 1.415a4 4 0 010 5.656l-3 3a4 4 0 01-5.656 0M10.172 13.828a4 4 0 01-5.656 0l-1.415-1.415a4 4 0 010-5.656l3-3a4 4 0 015.656 0"/></svg>
                            Buka
                        </a>
                    </div>
                </header>

                <div class="hidden md:grid md:grid-cols-12 items-center gap-3 px-5 py-3 bg-slate-50 border-b border-slate-200 text-[11px] font-bold uppercase tracking-wider text-slate-500">
                    <div class="col-span-1"></div>
                    <div class="col-span-4">User</div>
                    <div class="col-span-3">Nama Pengguna</div>
                    <div class="col-span-1 text-center">Menu</div>
                    <div class="col-span-3 text-right">Aksi</div>
                </div>

                <div class="divide-y divide-slate-100 users-body" data-tenant-id="{{ $t->id }}">
                    @forelse ($users as $u)
                        @php
                            $selected = collect($u['hak_akses'] ?? [])->map(fn ($v) => (int) $v)->all();
                            $initial = strtoupper(mb_substr($u['nama'] ?? '?', 0, 1));
                        @endphp
                        <div class="user-row" data-user-id="{{ $u['id'] }}" data-username="{{ $u['username'] }}" data-email="{{ $u['email'] ?? '' }}" data-telepon="{{ $u['telepon'] ?? '' }}">
                            <details>
                                <summary class="flex items-start gap-3 px-4 py-3 transition hover:bg-slate-50 md:grid md:grid-cols-12 md:items-center md:gap-3 md:px-5 md:py-3.5">
                                    <div class="hidden md:flex md:col-span-1 md:justify-center">
                                        <svg class="chev h-4 w-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                                    </div>
                                    <div class="flex flex-1 items-center gap-3 min-w-0 md:col-span-4">
                                        <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 font-bold text-white text-xs shadow-sm">{{ $initial }}</div>
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-800">{{ $u['nama'] }}</p>
                                            <p class="truncate text-xs text-slate-500">{{ $u['username'] }}</p>
                                        </div>
                                    </div>
                                    <div class="hidden md:flex md:col-span-1 md:items-center md:justify-center">
                                        <span class="inline-flex items-center gap-1 rounded-md bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                                            <span class="user-count">{{ count($selected) }}</span>
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center justify-end gap-1 md:col-span-6 md:flex-nowrap">
                                        <button type="button" class="edit-user-btn inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-amber-50 hover:text-amber-600" title="Ubah" aria-label="Ubah">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" class="reset-pwd-btn inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-indigo-50 hover:text-indigo-600" title="Reset Kata Sandi" aria-label="Reset Kata Sandi">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586l4.293-4.293A6 6 0 1119 9z"/></svg>
                                        </button>
                                        <button type="button" class="delete-user-btn inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Hapus" aria-label="Hapus">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 3h6a1 1 0 011 1v2H8V4a1 1 0 011-1z"/></svg>
                                        </button>
                                        <button type="button" class="select-all-btn inline-flex items-center gap-1 rounded-md px-1.5 py-1 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 sm:px-2" title="Pilih semua menu" aria-label="Pilih semua menu">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <span class="hidden sm:inline">Semua</span>
                                        </button>
                                        <button type="button" class="clear-all-btn inline-flex items-center gap-1 rounded-md px-1.5 py-1 text-xs font-semibold text-rose-600 hover:bg-rose-50 sm:px-2" title="Kosongkan pilihan" aria-label="Kosongkan pilihan">
                                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            <span class="hidden sm:inline">Atur Ulang</span>
                                        </button>
                                    </div>
                                </summary>

                                <div class="px-4 md:px-5 pb-5 pt-2 bg-gradient-to-b from-slate-50/60 to-white border-t border-slate-100">
                                    <div class="menu-grid mt-3">
                                        <div class="menu-grid-inner grid grid-cols-1 gap-3">
                                            @foreach ($grouped as $groupName => $bucket)
                                                @if ($bucket['parents']->isNotEmpty())
                                                    <div class="group-card rounded-xl border border-slate-200 bg-white p-4">
                                                        <div class="mb-3 flex items-center gap-2 border-b border-slate-100 pb-2">
                                                            <span class="h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                                                            <p class="text-xs font-bold uppercase tracking-wider text-slate-700">{{ $groupName }}</p>
                                                            <span class="ml-auto text-[10px] font-semibold text-slate-400">{{ $bucket['parents']->count() }} menu</span>
                                                        </div>
                                                        <div class="space-y-0.5">
                                                            @foreach ($bucket['parents'] as $parent)
                                                                @php
                                                                    $children = $bucket['children']->get($parent->id, collect());
                                                                    $hasChildren = $children->isNotEmpty();
                                                                @endphp
                                                                <div>
                                                                    <label class="menu-row flex items-center gap-2.5 rounded-md px-2 py-1.5 cursor-pointer">
                                                                        <input type="checkbox" class="cb menu-cb parent-cb h-4 w-4 flex-shrink-0" value="{{ $parent->id }}" data-children='@json($children->pluck("id"))' @checked(in_array($parent->id, $selected))>
                                                                        <span class="text-sm font-medium text-slate-800">{{ $parent->nama_menu }}</span>
                                                                    </label>
                                                                    @if ($hasChildren)
                                                                        <div class="ml-7 mt-0.5 space-y-0.5 border-l-2 border-slate-200 pl-3">
                                                                            @foreach ($children as $child)
                                                                                <label class="menu-row flex items-center gap-2.5 rounded-md px-2 py-1 cursor-pointer">
                                                                                    <input type="checkbox" class="cb menu-cb child-cb h-3.5 w-3.5 flex-shrink-0" value="{{ $child->id }}" data-parent="{{ $parent->id }}" @checked(in_array($child->id, $selected))>
                                                                                    <span class="text-xs text-slate-600">{{ $child->nama_menu }}</span>
                                                                                </label>
                                                                            @endforeach
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>

                                    <div class="mt-4 flex flex-col-reverse gap-2 border-t border-slate-100 pt-3 sm:flex-row sm:justify-end">
                                        <button type="button" class="clear-all-btn inline-flex min-h-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 hover:border-slate-300 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100 transition">
                                            Atur Ulang
                                        </button>
                                        <button type="button" class="save-btn inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-xs font-semibold text-white shadow-sm shadow-indigo-500/20 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:opacity-50 transition">
                                            <svg class="h-3.5 w-3.5 save-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            <svg class="h-3.5 w-3.5 spin-icon hidden animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                                            <span class="save-label">Simpan Perubahan</span>
                                        </button>
                                    </div>
                                </div>
                            </details>
                        </div>
                    @empty
                        @php $dbStatus = $payload['db_status'] ?? 'ok'; @endphp
                        <div class="px-5 py-10 text-center">
                            @if ($dbStatus === 'no_db')
                                <div class="mx-auto inline-flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-left">
                                    <svg class="h-5 w-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                                    <div>
                                        <p class="text-sm font-semibold text-amber-800">Database tenant belum tersedia</p>
                                        <p class="mt-0.5 text-xs text-amber-700">Lokasi ini belum memiliki database sendiri, sehingga user & hak akses belum dapat dikelola.</p>
                                    </div>
                                </div>
                            @elseif ($dbStatus === 'no_tables')
                                <div class="mx-auto inline-flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-left">
                                    <svg class="h-5 w-5 flex-shrink-0 text-amber-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4a2 2 0 00-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                                    <div>
                                        <p class="text-sm font-semibold text-amber-800">Database tenant belum diinisialisasi</p>
                                        <p class="mt-0.5 text-xs text-amber-700">Tabel untuk lokasi ini belum dibuat, sehingga belum ada data user. Inisialisasi database terlebih dahulu.</p>
                                    </div>
                                </div>
                            @else
                                <p class="text-sm font-semibold text-slate-700">Belum ada user di lokasi ini.</p>
                                <p class="mt-1 text-xs text-slate-500">Tambahkan user operator untuk mengatur hak akses.</p>
                            @endif
                        </div>
                    @endforelse
                </div>
            </section>
        @empty
            <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white p-10 text-center shadow-sm">
                <p class="text-sm font-semibold text-slate-700">Belum ada lokasi (tenant).</p>
                <p class="mt-1 text-xs text-slate-500">Tambahkan sekolah terlebih dahulu di menu Tenant.</p>
            </section>
        @endforelse

        <p class="mt-6 text-center text-xs text-slate-400">&copy; {{ date('Y') }} {{ env('APP_NAME') }}</p>
    </main>

    <div id="add-user-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true" aria-labelledby="add-user-title">
        <div class="w-full max-w-5xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-3 sm:px-6">
                <div>
                    <h3 id="add-user-title" class="text-base font-bold text-slate-900">Tambah Pengguna Operator</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Buat akun baru lengkap dengan hak akses menu untuk lokasi <span id="add-user-tenant-label" class="font-semibold text-slate-700"></span>.</p>
                </div>
                <button type="button" id="close-add-user-modal" aria-label="Tutup" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form id="add-user-form" class="px-5 py-4 sm:px-6">
                <input type="hidden" id="add-tenant-id" name="tenant_id">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-5">
                    <div class="space-y-2 lg:col-span-2">
                        <div>
                            <label for="add-nama" class="mb-0.5 block text-[11px] font-semibold text-slate-700">Nama <span class="text-rose-500">*</span></label>
                            <input id="add-nama" name="nama" type="text" required class="invoice-input" placeholder="Nama lengkap">
                        </div>
                        <div>
                            <label for="add-username" class="mb-0.5 block text-[11px] font-semibold text-slate-700">Nama Pengguna <span class="text-rose-500">*</span></label>
                            <input id="add-username" name="username" type="text" required class="invoice-input" placeholder="username_baru">
                        </div>
                        <div>
                            <label for="add-telepon" class="mb-0.5 block text-[11px] font-semibold text-slate-700">Telepon</label>
                            <input id="add-telepon" name="telepon" type="text" class="invoice-input" placeholder="08xxx">
                        </div>
                        <div>
                            <label for="add-email" class="mb-0.5 block text-[11px] font-semibold text-slate-700">Email</label>
                            <input id="add-email" name="email" type="email" class="invoice-input" placeholder="opsional@mail.com">
                        </div>
                        <div>
                            <label for="add-password" class="mb-0.5 block text-[11px] font-semibold text-slate-700">Password <span class="text-rose-500">*</span></label>
                            <input id="add-password" name="password" type="password" required minlength="6" class="invoice-input" placeholder="Minimal 6 karakter">
                        </div>
                    </div>

                    <div class="lg:col-span-3 lg:border-l lg:border-slate-100 lg:pl-4">
                        <div class="mb-1.5 flex items-center justify-between">
                            <div>
                                <p class="text-[11px] font-semibold text-slate-700">Hak Akses Menu</p>
                                <p class="text-[10px] text-slate-500">Centang menu yang boleh diakses.</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" id="add-select-all" class="rounded-md px-2 py-0.5 text-[10px] font-semibold text-indigo-600 hover:bg-indigo-50">Semua</button>
                                <button type="button" id="add-clear-all" class="rounded-md px-2 py-0.5 text-[10px] font-semibold text-rose-600 hover:bg-rose-50">Atur Ulang</button>
                            </div>
                        </div>
                        <div class="menu-grid menu-scroll rounded-xl border border-slate-200 p-2.5">
                            <div id="add-menu-container" class="menu-grid-inner grid grid-cols-1 gap-2 sm:grid-cols-2"></div>
                        </div>
                    </div>
                </div>

                <div id="add-user-error" class="mt-2 hidden rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] text-rose-700"></div>

                <div class="mt-3 flex flex-col-reverse gap-2 border-t border-slate-100 pt-2.5 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-add-user-modal" class="inline-flex min-h-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-4 text-xs font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">Batal</button>
                    <button type="submit" id="submit-add-user" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 text-xs font-semibold text-white shadow-sm shadow-indigo-500/20 hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 disabled:opacity-50">
                        <svg class="h-3.5 w-3.5 submit-icon" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <svg class="h-3.5 w-3.5 spin-icon hidden animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/></svg>
                        <span class="submit-label">Simpan User</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="edit-user-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <div class="w-full max-w-md max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <form id="edit-user-form" class="space-y-4 p-6">
                <input type="hidden" id="edit-tenant-id">
                <input type="hidden" id="edit-user-id">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Ubah Pengguna</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Ubah data identitas user operator.</p>
                </div>
                <div>
                    <label class="mb-0.5 block text-[11px] font-semibold text-slate-700">Nama</label>
                    <input id="edit-nama" type="text" required class="invoice-input">
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="mb-0.5 block text-[11px] font-semibold text-slate-700">Email</label>
                        <input id="edit-email" type="email" class="invoice-input">
                    </div>
                    <div>
                        <label class="mb-0.5 block text-[11px] font-semibold text-slate-700">Telepon</label>
                        <input id="edit-telepon" type="text" class="invoice-input">
                    </div>
                </div>
                <div id="edit-user-error" class="hidden rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] text-rose-700"></div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('edit-user-modal')" class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="reset-pwd-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true">
        <div class="w-full max-w-md max-h-[90vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <form id="reset-pwd-form" class="space-y-4 p-6">
                <input type="hidden" id="reset-tenant-id">
                <input type="hidden" id="reset-user-id">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Reset Kata Sandi</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Reset kata sandi untuk <span id="reset-username" class="font-mono font-semibold text-slate-700"></span>.</p>
                </div>
                <div>
                    <label class="mb-0.5 block text-[11px] font-semibold text-slate-700">Password Baru</label>
                    <input id="reset-password" type="password" required minlength="6" class="invoice-input" placeholder="Minimal 6 karakter">
                </div>
                <div id="reset-pwd-error" class="hidden rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-[11px] text-rose-700"></div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('reset-pwd-modal')" class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const csrf = '{{ csrf_token() }}';
        const tenantMenus = @json($menusByTenant);
        const tenantLabels = @json($tenants->mapWithKeys(fn ($t) => [$t->id => ($t->nama_sekolah ?? $t->id)])->all());

        function closeModal(id) {
            const m = document.getElementById(id);
            m.classList.add('hidden');
            m.classList.remove('flex');
            document.body.classList.remove('overflow-hidden');
        }
        function openModal(id) {
            const m = document.getElementById(id);
            m.classList.remove('hidden');
            m.classList.add('flex');
            document.body.classList.add('overflow-hidden');
        }

        function updateCount(row) {
            const n = row.querySelectorAll('.menu-cb:checked').length;
            const c = row.querySelector('.user-count');
            if (c) c.textContent = n;
        }
        function syncParent(row, parentId) {
            const parent = row.querySelector(`.parent-cb[value="${parentId}"]`);
            if (!parent) return;
            const siblings = row.querySelectorAll(`.child-cb[data-parent="${parentId}"]`);
            parent.checked = siblings.length > 0 && Array.from(siblings).every((c) => c.checked);
        }

        document.querySelectorAll('.tenant-block').forEach((block) => {
            const tbody = block.querySelector('.users-body');
            tbody.querySelectorAll('.menu-cb').forEach((cb) => {
                cb.addEventListener('change', function () {
                    const row = this.closest('.user-row');
                    if (this.classList.contains('parent-cb')) {
                        let ids = [];
                        try { ids = JSON.parse(this.dataset.children || '[]'); } catch (_) {}
                        ids.forEach((id) => {
                            const c = row.querySelector(`.child-cb[value="${id}"]`);
                            if (c) c.checked = this.checked;
                        });
                    } else if (this.classList.contains('child-cb')) {
                        syncParent(row, this.dataset.parent);
                    }
                    updateCount(row);
                });
            });

            tbody.querySelectorAll('.select-all-btn').forEach((btn) => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const row = this.closest('.user-row');
                    const det = row.querySelector('details');
                    if (det && !det.open) det.open = true;
                    row.querySelectorAll('.menu-cb').forEach((c) => (c.checked = true));
                    updateCount(row);
                });
            });

            tbody.querySelectorAll('.clear-all-btn').forEach((btn) => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const row = this.closest('.user-row');
                    row.querySelectorAll('.menu-cb').forEach((c) => (c.checked = false));
                    updateCount(row);
                });
            });

            tbody.querySelectorAll('.save-btn').forEach((btn) => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const row = this.closest('.user-row');
                    const tenantId = block.dataset.tenantId;
                    const userId = row.dataset.userId;
                    const ids = Array.from(row.querySelectorAll('.menu-cb:checked')).map((c) => c.value);

                    this.disabled = true;
                    this.querySelector('.save-icon').classList.add('hidden');
                    this.querySelector('.spin-icon').classList.remove('hidden');
                    this.querySelector('.save-label').textContent = 'Menyimpan...';

                    const fd = new FormData();
                    fd.append('_token', csrf);
                    fd.append('_method', 'PUT');
                    ids.forEach((v) => fd.append('menu_ids[]', v));

                    fetch(`/hak-akses/${tenantId}/user/${userId}/hak-akses`, { method: 'POST', body: fd })
                        .then((r) => r.json().then((j) => ({ ok: r.ok, j })))
                        .then(({ ok, j }) => {
                            if (ok) Swal.fire({ icon: 'success', title: 'Tersimpan', text: `${j.count} menu disimpan`, timer: 1400, showConfirmButton: false });
                            else Swal.fire({ icon: 'error', title: 'Gagal', text: j.message || 'Coba lagi' });
                        })
                        .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Network error' }))
                        .finally(() => {
                            this.disabled = false;
                            this.querySelector('.save-icon').classList.remove('hidden');
                            this.querySelector('.spin-icon').classList.add('hidden');
                            this.querySelector('.save-label').textContent = 'Simpan Perubahan';
                        });
                });
            });

            const deleteUrlTemplate = (uid) => `/hak-akses/${block.dataset.tenantId}/user/${uid}`;

            tbody.querySelectorAll('.user-row').forEach((row) => {
                const userId = row.dataset.userId;

                row.querySelector('.edit-user-btn').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const namaEl = row.querySelector('summary p');
                    const nama = namaEl ? namaEl.textContent.trim() : (row.dataset.username || '');
                    document.getElementById('edit-tenant-id').value = block.dataset.tenantId;
                    document.getElementById('edit-user-id').value = userId;
                    document.getElementById('edit-nama').value = nama;
                    document.getElementById('edit-email').value = row.dataset.email || '';
                    document.getElementById('edit-telepon').value = row.dataset.telepon || '';
                    document.getElementById('edit-user-error').classList.add('hidden');
                    openModal('edit-user-modal');
                });

                row.querySelector('.reset-pwd-btn').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    document.getElementById('reset-tenant-id').value = block.dataset.tenantId;
                    document.getElementById('reset-user-id').value = userId;
                    document.getElementById('reset-username').textContent = row.dataset.username || '';
                    document.getElementById('reset-password').value = '';
                    document.getElementById('reset-pwd-error').classList.add('hidden');
                    openModal('reset-pwd-modal');
                });

                row.querySelector('.delete-user-btn').addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();
                    const username = row.dataset.username || '';
                    Swal.fire({
                        title: `Hapus user ${username}?`,
                        text: 'Tindakan ini tidak dapat dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, hapus',
                        cancelButtonText: 'Batal',
                        reverseButtons: true,
                        buttonsStyling: false,
                        customClass: {
                            confirmButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-rose-600 text-white font-semibold text-sm hover:bg-rose-700 focus:outline-none focus:ring-4 focus:ring-rose-200 mx-1',
                            cancelButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 focus:outline-none focus:ring-4 focus:ring-slate-200 mx-1',
                        },
                    }).then((res) => {
                        if (!res.isConfirmed) return;
                        const fd = new FormData();
                        fd.append('_token', csrf);
                        fd.append('_method', 'DELETE');
                        fetch(deleteUrlTemplate(userId), { method: 'POST', body: fd })
                            .then((r) => r.json().then((j) => ({ ok: r.ok, j })))
                            .then(({ ok, j }) => {
                                if (ok) {
                                    row.remove();
                                    Swal.fire({ icon: 'success', title: 'Dihapus', text: j.username + ' telah dihapus', timer: 1200, showConfirmButton: false });
                                } else {
                                    Swal.fire({ icon: 'error', title: 'Gagal', text: j.message || 'Coba lagi' });
                                }
                            })
                            .catch(() => Swal.fire({ icon: 'error', title: 'Gagal', text: 'Galat jaringan' }));
                    });
                });
            });

            block.querySelector('.add-user-btn').addEventListener('click', function () {
                const tid = block.dataset.tenantId;
                document.getElementById('add-tenant-id').value = tid;
                document.getElementById('add-user-tenant-label').textContent = tenantLabels[tid] || tid;
                document.getElementById('add-user-form').reset();
                renderAddMenus(tid);
                document.getElementById('add-user-error').classList.add('hidden');
                openModal('add-user-modal');
                document.getElementById('add-nama').focus();
            });
        });

        function renderAddMenus(tid) {
            const menus = tenantMenus[tid] || [];
            const cont = document.getElementById('add-menu-container');
            cont.innerHTML = '';
            if (!menus.length) {
                cont.innerHTML = '<div class="rounded-lg border border-dashed border-slate-200 bg-slate-50 px-3 py-6 text-center text-xs text-slate-400">Tenant ini belum punya data menu.</div>';
                return;
            }
            const groups = {};
            menus.forEach((m) => { (groups[m.group || 'Lainnya'] = groups[m.group || 'Lainnya'] || []).push(m); });
            Object.keys(groups).forEach((g) => {
                const wrap = document.createElement('div');
                wrap.className = 'group-card rounded-lg border border-slate-200 bg-white p-2';
                wrap.innerHTML = `<div class="mb-1 flex items-center gap-1.5 border-b border-slate-100 pb-1">
                    <span class="h-1 w-1 rounded-full bg-indigo-500"></span>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-700">${g}</p>
                </div>`;
                const list = document.createElement('div');
                list.className = 'space-y-0.5';
                groups[g].forEach((m) => {
                    const childIds = (m.children || []).map((c) => c.id);
                    const hasChildren = childIds.length > 0;
                    const label = document.createElement('label');
                    label.className = 'menu-row flex items-center gap-1.5 rounded-md px-1 py-0.5 cursor-pointer';
                    label.innerHTML = `<input type="checkbox" class="cb add-menu-cb add-parent-cb h-3.5 w-3.5 flex-shrink-0" value="${m.id}" data-children='${JSON.stringify(childIds)}'>
                        <span class="text-[11px] font-semibold text-slate-800">${m.nama}</span>`;
                    list.appendChild(label);
                    if (hasChildren) {
                        const sub = document.createElement('div');
                        sub.className = 'ml-5 space-y-0.5 border-l-2 border-slate-200 pl-2';
                        m.children.forEach((c) => {
                            const cl = document.createElement('label');
                            cl.className = 'menu-row flex items-center gap-1.5 rounded-md px-1 py-0.5 cursor-pointer';
                            cl.innerHTML = `<input type="checkbox" class="cb add-menu-cb add-child-cb h-3 w-3 flex-shrink-0" value="${c.id}" data-parent="${m.id}">
                                <span class="text-[10px] text-slate-600">${c.nama}</span>`;
                            sub.appendChild(cl);
                        });
                        list.appendChild(sub);
                    }
                });
                wrap.appendChild(list);
                cont.appendChild(wrap);
            });

            cont.querySelectorAll('.add-menu-cb').forEach((cb) => {
                cb.addEventListener('change', function () {
                    if (this.classList.contains('add-parent-cb')) {
                        let ids = [];
                        try { ids = JSON.parse(this.dataset.children || '[]'); } catch (_) {}
                        ids.forEach((id) => {
                            const c = cont.querySelector(`.add-child-cb[value="${id}"]`);
                            if (c) c.checked = this.checked;
                        });
                    } else if (this.classList.contains('add-child-cb')) {
                        const parent = cont.querySelector(`.add-parent-cb[value="${this.dataset.parent}"]`);
                        if (parent) {
                            const siblings = cont.querySelectorAll(`.add-child-cb[data-parent="${this.dataset.parent}"]`);
                            parent.checked = siblings.length > 0 && Array.from(siblings).every((c) => c.checked);
                        }
                    }
                });
            });
        }

        document.getElementById('add-select-all').addEventListener('click', () => {
            document.querySelectorAll('#add-menu-container .add-menu-cb').forEach((c) => (c.checked = true));
        });
        document.getElementById('add-clear-all').addEventListener('click', () => {
            document.querySelectorAll('#add-menu-container .add-menu-cb').forEach((c) => (c.checked = false));
        });
        document.getElementById('close-add-user-modal').addEventListener('click', () => closeModal('add-user-modal'));
        document.getElementById('cancel-add-user-modal').addEventListener('click', () => closeModal('add-user-modal'));
        document.getElementById('add-user-modal').addEventListener('click', (e) => { if (e.target === e.currentTarget) closeModal('add-user-modal'); });
        document.getElementById('edit-user-modal').addEventListener('click', (e) => { if (e.target === e.currentTarget) closeModal('edit-user-modal'); });
        document.getElementById('reset-pwd-modal').addEventListener('click', (e) => { if (e.target === e.currentTarget) closeModal('reset-pwd-modal'); });
        document.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;
            ['add-user-modal', 'edit-user-modal', 'reset-pwd-modal'].forEach((id) => {
                const el = document.getElementById(id);
                if (el && !el.classList.contains('hidden')) closeModal(id);
            });
        });

        document.getElementById('add-user-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const tid = document.getElementById('add-tenant-id').value;
            const btn = document.getElementById('submit-add-user');
            btn.disabled = true;
            btn.querySelector('.submit-icon').classList.add('hidden');
            btn.querySelector('.spin-icon').classList.remove('hidden');
            btn.querySelector('.submit-label').textContent = 'Menyimpan...';
            document.getElementById('add-user-error').classList.add('hidden');

            const fd = new FormData(this);
            fd.append('_token', csrf);
            const ids = Array.from(document.querySelectorAll('#add-menu-container .add-menu-cb:checked')).map((c) => c.value);
            ids.forEach((v) => fd.append('hak_akses[]', v));

            fetch(`/hak-akses/${tid}/user`, { method: 'POST', body: fd })
                .then((r) => r.json().then((j) => ({ ok: r.ok, j })))
                .then(({ ok, j }) => {
                    if (ok) {
                        Swal.fire({ icon: 'success', title: 'User ditambah', text: j.user.nama, timer: 1400, showConfirmButton: false })
                            .then(() => window.location.reload());
                    } else {
                        const errEl = document.getElementById('add-user-error');
                        errEl.textContent = j.message || (j.errors ? Object.values(j.errors).flat().join(' • ') : 'Coba lagi');
                        errEl.classList.remove('hidden');
                    }
                })
                                .catch(() => {
                                    const errEl = document.getElementById('add-user-error');
                                    errEl.textContent = 'Galat jaringan';
                                    errEl.classList.remove('hidden');
                                })
                .finally(() => {
                    btn.disabled = false;
                    btn.querySelector('.submit-icon').classList.remove('hidden');
                    btn.querySelector('.spin-icon').classList.add('hidden');
                    btn.querySelector('.submit-label').textContent = 'Simpan User';
                });
        });

        document.getElementById('edit-user-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const tid = document.getElementById('edit-tenant-id').value;
            const uid = document.getElementById('edit-user-id').value;
            const errEl = document.getElementById('edit-user-error');
            errEl.classList.add('hidden');
            const fd = new FormData();
            fd.append('_token', csrf);
            fd.append('_method', 'PUT');
            fd.append('nama', document.getElementById('edit-nama').value);
            fd.append('email', document.getElementById('edit-email').value);
            fd.append('telepon', document.getElementById('edit-telepon').value);
            fetch(`/hak-akses/${tid}/user/${uid}`, { method: 'POST', body: fd })
                .then((r) => r.json().then((j) => ({ ok: r.ok, j })))
                .then(({ ok, j }) => {
                    if (ok) {
                        closeModal('edit-user-modal');
                        Swal.fire({ icon: 'success', title: 'Tersimpan', timer: 1200, showConfirmButton: false })
                            .then(() => window.location.reload());
                    } else {
                        errEl.textContent = j.message || (j.errors ? Object.values(j.errors).flat().join(' • ') : 'Coba lagi');
                        errEl.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    errEl.textContent = 'Galat jaringan';
                    errEl.classList.remove('hidden');
                });
        });

        document.getElementById('reset-pwd-form').addEventListener('submit', function (e) {
            e.preventDefault();
            const tid = document.getElementById('reset-tenant-id').value;
            const uid = document.getElementById('reset-user-id').value;
            const errEl = document.getElementById('reset-pwd-error');
            errEl.classList.add('hidden');
            const fd = new FormData();
            fd.append('_token', csrf);
            fd.append('password', document.getElementById('reset-password').value);
            fetch(`/hak-akses/${tid}/user/${uid}/reset-password`, { method: 'POST', body: fd })
                .then((r) => r.json().then((j) => ({ ok: r.ok, j })))
                .then(({ ok, j }) => {
                    if (ok) {
                        closeModal('reset-pwd-modal');
                        Swal.fire({ icon: 'success', title: 'Password direset', timer: 1200, showConfirmButton: false });
                    } else {
                        errEl.textContent = j.message || (j.errors ? Object.values(j.errors).flat().join(' • ') : 'Coba lagi');
                        errEl.classList.remove('hidden');
                    }
                })
                .catch(() => {
                    errEl.textContent = 'Galat jaringan';
                    errEl.classList.remove('hidden');
                });
        });

        document.getElementById('expand-all').addEventListener('click', () => {
            document.querySelectorAll('.tenant-block details').forEach((d) => (d.open = true));
        });
        document.getElementById('collapse-all').addEventListener('click', () => {
            document.querySelectorAll('.tenant-block details').forEach((d) => (d.open = false));
        });
    </script>

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