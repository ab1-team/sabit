@php
    $initialModalOpen = $errors->any() || in_array($modalAction ?? null, ['create', 'edit'], true);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tenant — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f8fafc; }
        .invoice-input { width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: .75rem; background: #fff; padding: .625rem .875rem; font-size: .875rem; color: #1e293b; transition: border-color .15s ease, box-shadow .15s ease; }
        .invoice-input::placeholder { color: #94a3b8; }
        .invoice-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .modal-scroll { max-height: calc(100vh - 2rem); overflow-y: auto; }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('tenant.partials.bilah-atas')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Sekolah</h2>
                <p class="mt-1 text-sm text-slate-500">Setiap tenant punya basis data dan 2 domain terpisah: admin &amp; landing page.</p>
            </div>
            <button type="button" id="open-create-modal" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Sekolah
            </button>
        </header>

        <form method="GET" action="{{ route('tenant.tenant.index') }}" class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <div class="relative w-full sm:max-w-md">
                <input type="text" name="q" value="{{ $q }}" placeholder="Cari tenant (id/nama)…" class="block w-full rounded-lg border border-slate-200 bg-white py-2.5 pl-10 pr-3 text-sm placeholder-slate-400 shadow-sm focus:border-indigo-300 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                <svg class="pointer-events-none absolute left-3 top-3 h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
            </div>
            <button type="submit" class="inline-flex items-center justify-center rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-800">Cari</button>
        </form>

        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-[860px] w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">ID</th>
                            <th class="px-5 py-3 font-semibold">Nama Sekolah</th>
                            <th class="px-5 py-3 font-semibold">Domain Landing</th>
                            <th class="px-5 py-3 font-semibold">Domain Administrator</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($tenants as $t)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono text-slate-700">{{ $t->id }}</td>
                                <td class="px-5 py-3 font-semibold text-slate-800">{{ $t->nama_sekolah ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @php $landing = optional($t->landingDomain())->domain; @endphp
                                    @if ($landing)
                                        <a href="http://{{ $landing }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-md bg-indigo-50 px-2 py-1 text-xs font-medium text-indigo-700 hover:bg-indigo-100">
                                            <span class="font-mono">{{ $landing }}</span>
                                            <svg class="h-3 w-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3">
                                    @php $admin = optional($t->adminDomain())->domain; @endphp
                                    @if ($admin)
                                        <a href="http://{{ $admin }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1.5 rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700 hover:bg-amber-100">
                                            <span class="font-mono">{{ $admin }}</span>
                                            <svg class="h-3 w-3 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        <button type="button" class="open-detail-modal inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-200" title="Detail"
                                            data-id="{{ $t->id }}"
                                            data-nama="{{ $t->nama_sekolah ?? '—' }}"
                                            data-domain-landing="{{ optional($t->landingDomain())->domain ?? '—' }}"
                                            data-domain-admin="{{ optional($t->adminDomain())->domain ?? '—' }}"
                                            data-email="{{ $t->email ?? '—' }}"
                                            data-db="tenant{{ $t->id }}"
                                            data-created="{{ optional($t->created_at)->format('d/m/Y H:i') ?? '—' }}">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        </button>
                                        <button type="button" class="open-edit-modal inline-flex items-center rounded-lg bg-amber-100 px-2.5 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-200" title="Ubah"
                                            data-edit-url="{{ route('tenant.tenant.update', $t) }}"
                                            data-id="{{ $t->id }}"
                                            data-nama="{{ $t->nama_sekolah ?? '' }}"
                                            data-domain-landing="{{ optional($t->landingDomain())->domain ?? '' }}"
                                            data-domain-admin="{{ optional($t->adminDomain())->domain ?? '' }}"
                                            data-email="{{ $t->email ?? '' }}">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                        </button>
                                        <button type="button" class="delete-tenant inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-200" title="Hapus" data-action="{{ route('tenant.tenant.destroy', $t) }}" data-name="{{ $t->id }}">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 0.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 0 00-7.5 0"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-5 py-14 text-center text-sm text-slate-400">Belum ada tenant.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <ul class="divide-y divide-slate-100 md:hidden">
                @forelse ($tenants as $t)
                    <li class="px-4 py-4">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-900">{{ $t->nama_sekolah ?? '—' }}</p>
                                <p class="mt-0.5 font-mono text-xs text-slate-500">{{ $t->id }}</p>
                                <div class="mt-1 flex flex-col gap-1">
                                    @foreach ($t->domains as $d)
                                        <span class="inline-flex items-center gap-1.5 rounded-md {{ $d->type === \App\Models\Domain::TYPE_ADMIN ? 'bg-amber-50 text-amber-700' : 'bg-indigo-50 text-indigo-700' }} px-2 py-0.5 text-[11px] font-medium">
                                            <span class="text-[9px] uppercase tracking-wider opacity-70">{{ $d->type }}</span>
                                            <span class="font-mono">{{ $d->domain }}</span>
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-1">
                                <button type="button" class="open-detail-modal inline-flex items-center rounded-md bg-slate-100 p-1.5 text-slate-600 hover:bg-slate-200" title="Detail"
                                    data-id="{{ $t->id }}"
                                    data-nama="{{ $t->nama_sekolah ?? '—' }}"
                                    data-domain-landing="{{ optional($t->landingDomain())->domain ?? '—' }}"
                                    data-domain-admin="{{ optional($t->adminDomain())->domain ?? '—' }}"
                                    data-email="{{ $t->email ?? '—' }}"
                                    data-db="tenant{{ $t->id }}"
                                    data-created="{{ optional($t->created_at)->format('d/m/Y H:i') ?? '—' }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </button>
                                <button type="button" class="open-edit-modal inline-flex items-center rounded-md bg-amber-100 p-1.5 text-amber-700 hover:bg-amber-200" title="Ubah"
                                    data-edit-url="{{ route('tenant.tenant.update', $t) }}"
                                    data-id="{{ $t->id }}"
                                    data-nama="{{ $t->nama_sekolah ?? '' }}"
                                    data-domain-landing="{{ optional($t->landingDomain())->domain ?? '' }}"
                                    data-domain-admin="{{ optional($t->adminDomain())->domain ?? '' }}"
                                    data-email="{{ $t->email ?? '' }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"/></svg>
                                </button>
                                <button type="button" class="delete-tenant inline-flex items-center rounded-md bg-rose-100 p-1.5 text-rose-700 hover:bg-rose-200" title="Hapus" data-action="{{ route('tenant.tenant.destroy', $t) }}" data-name="{{ $t->id }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 0.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-14 text-center text-sm text-slate-400">Belum ada tenant.</li>
                @endforelse
            </ul>

            <div class="overflow-x-auto border-t border-slate-100 px-3 py-4 sm:px-5">
                {{ $tenants->links() }}
            </div>
        </section>
    </main>

    <div id="tenant-modal" class="{{ $initialModalOpen ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-scroll w-full max-w-3xl rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-900">Tambah Sekolah</h3>
                    <p id="modal-description" class="mt-1 text-sm text-slate-500"></p>
                </div>
                <button type="button" id="close-tenant-modal" aria-label="Tutup dialog" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($errors->any())
                <div class="mx-5 mt-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 sm:mx-6">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <span>Periksa kembali field yang ditandai dan coba lagi.</span>
                </div>
            @endif

            <form id="tenant-form" method="POST" class="px-5 py-5 sm:px-6">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @include('tenant.tenant._formulir', ['tenant' => new \App\Models\Tenant()])
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-tenant-modal" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">Batal</button>
                    <button type="submit" id="tenant-submit-btn" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <span id="submit-label">Simpan &amp; Provisioning</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="detail-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="detail-modal-title">
        <div class="modal-scroll w-full max-w-2xl rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h3 id="detail-modal-title" class="text-lg font-bold text-slate-900">Detail Sekolah</h3>
                    <p id="detail-modal-subtitle" class="mt-1 text-sm text-slate-500">Informasi identitas tenant.</p>
                </div>
                <button type="button" id="close-detail-modal" aria-label="Tutup dialog" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="px-5 py-5 sm:px-6">
                <dl class="divide-y divide-slate-100 rounded-xl border border-slate-200">
                    <div class="flex items-center justify-between px-4 py-3">
                        <dt class="text-sm font-medium text-slate-500">ID</dt>
                        <dd id="detail-id" class="font-mono text-sm text-slate-800"></dd>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <dt class="text-sm font-medium text-slate-500">Nama</dt>
                        <dd id="detail-nama" class="text-sm font-semibold text-slate-800"></dd>
                    </div>
                    <div class="flex items-start justify-between gap-3 px-4 py-3">
                        <dt class="text-sm font-medium text-slate-500">Domain Landing</dt>
                        <dd class="flex items-center gap-2 text-right">
                            <span class="rounded-md bg-indigo-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-indigo-700">landing</span>
                            <span id="detail-domain-landing" class="font-mono text-sm text-slate-800"></span>
                        </dd>
                    </div>
                    <div class="flex items-start justify-between gap-3 px-4 py-3">
                        <dt class="text-sm font-medium text-slate-500">Domain Admin</dt>
                        <dd class="flex items-center gap-2 text-right">
                            <span class="rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-700">admin</span>
                            <span id="detail-domain-admin" class="font-mono text-sm text-slate-800"></span>
                        </dd>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <dt class="text-sm font-medium text-slate-500">Basis Data</dt>
                        <dd id="detail-db" class="font-mono text-sm text-slate-800"></dd>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <dt class="text-sm font-medium text-slate-500">Email Admin</dt>
                        <dd id="detail-email" class="text-sm text-slate-800"></dd>
                    </div>
                    <div class="flex items-center justify-between px-4 py-3">
                        <dt class="text-sm font-medium text-slate-500">Dibuat</dt>
                        <dd id="detail-created" class="text-sm text-slate-800"></dd>
                    </div>
                </dl>
                <div class="mt-5 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-detail-modal" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">Tutup</button>
                    <a id="detail-preview-link" href="#" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 015.656 0l1.415 1.415a4 4 0 010 5.656l-3 3a4 4 0 01-5.656 0M10.172 13.828a4 4 0 01-5.656 0l-1.415-1.415a4 4 0 010-5.656l3-3a4 4 0 015.656 0"/></svg>
                        Preview Landing
                    </a>
                    <a id="detail-admin-link" href="#" target="_blank" rel="noopener" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-amber-600 px-5 text-sm font-semibold text-white hover:bg-amber-700 focus:outline-none focus:ring-4 focus:ring-amber-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 015.656 0l1.415 1.415a4 4 0 010 5.656l-3 3a4 4 0 01-5.656 0M10.172 13.828a4 4 0 01-5.656 0l-1.415-1.415a4 4 0 010-5.656l3-3a4 4 0 015.656 0"/></svg>
                        Preview Admin
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form id="delete-tenant-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        const detailModal = document.getElementById('detail-modal');

        function setDetailModalState(open) {
            detailModal.classList.toggle('hidden', !open);
            detailModal.classList.toggle('flex', open);
            document.body.classList.toggle('overflow-hidden', open);
        }

        function buildPreviewHref(domain) {
            if (domain && domain !== '—' && !/^https?:\/\//i.test(domain)) {
                return 'http://' + domain;
            }
            return domain || '#';
        }

        function openDetailModal(btn) {
            document.getElementById('detail-modal-subtitle').textContent = 'Informasi identitas ' + btn.dataset.id + '.';
            document.getElementById('detail-id').textContent = btn.dataset.id;
            document.getElementById('detail-nama').textContent = btn.dataset.nama;
            document.getElementById('detail-domain-landing').textContent = btn.dataset.domainLanding || '—';
            document.getElementById('detail-domain-admin').textContent = btn.dataset.domainAdmin || '—';
            document.getElementById('detail-db').textContent = btn.dataset.db;
            document.getElementById('detail-email').textContent = btn.dataset.email;
            document.getElementById('detail-created').textContent = btn.dataset.created;

            document.getElementById('detail-preview-link').href = buildPreviewHref(btn.dataset.domainLanding);
            document.getElementById('detail-admin-link').href = buildPreviewHref(btn.dataset.domainAdmin);
            setDetailModalState(true);
        }

        document.querySelectorAll('.open-detail-modal').forEach(function (btn) {
            btn.addEventListener('click', function () { openDetailModal(this); });
        });

        document.getElementById('close-detail-modal').addEventListener('click', function () { setDetailModalState(false); });
        document.getElementById('cancel-detail-modal').addEventListener('click', function () { setDetailModalState(false); });
        detailModal.addEventListener('click', function (event) {
            if (event.target === detailModal) setDetailModalState(false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !detailModal.classList.contains('hidden')) {
                setDetailModalState(false);
            }
        });

        const deleteForm = document.getElementById('delete-tenant-form');

        document.querySelectorAll('.delete-tenant').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = this.dataset.action;
                const name = this.dataset.name;
                Swal.fire({
                    title: 'Hapus tenant ini?',
                    text: 'Tenant ' + name + ', database tenant' + name + ', dan folder storage akan dihapus. Tindakan ini tidak dapat dibatalkan.',
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
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    deleteForm.action = action;
                    deleteForm.submit();
                });
            });
        });

        const tenantModal = document.getElementById('tenant-modal');
        const tenantForm = document.getElementById('tenant-form');
        const formMethod = document.getElementById('form-method');
        const modalTitle = document.getElementById('modal-title');
        const modalDescription = document.getElementById('modal-description');
        const submitLabel = document.getElementById('submit-label');
        const idInput = document.getElementById('tenant-id');
        const landingInput = document.getElementById('tenant-domain-landing');
        const adminInput = document.getElementById('tenant-domain-admin');
        const emailInput = document.getElementById('tenant-email');
        const namaInput = document.getElementById('tenant-nama');

        function setModalState(open) {
            tenantModal.classList.toggle('hidden', !open);
            tenantModal.classList.toggle('flex', open);
            document.body.classList.toggle('overflow-hidden', open);
        }

        function openCreateModal() {
            tenantForm.reset();
            tenantForm.action = @json(route('tenant.tenant.store'));
            formMethod.value = 'POST';
            modalTitle.textContent = 'Tambah Sekolah';
            modalDescription.innerHTML = '';
            submitLabel.textContent = 'Simpan & Provisioning';
            idInput.readOnly = false;
            idInput.required = true;
            idInput.classList.remove('cursor-not-allowed', 'border-slate-200', 'bg-slate-50', 'text-slate-700', 'focus:border-slate-200', 'focus:shadow-none');
            idInput.classList.add('font-mono');
            if (landingInput) landingInput.value = '';
            if (adminInput) adminInput.value = '';
            const info = document.getElementById('default-users-info');
            if (info) info.classList.remove('hidden');
            setModalState(true);
            namaInput.focus();
        }

        function openEditModal(btn) {
            tenantForm.action = btn.dataset.editUrl;
            formMethod.value = 'PUT';
            modalTitle.textContent = 'Ubah Sekolah';
            modalDescription.textContent = 'Perbarui nama sekolah, kedua domain, dan email admin.';
            submitLabel.textContent = 'Simpan Perubahan';
            namaInput.value = btn.dataset.nama || '';
            idInput.value = btn.dataset.id || '';
            landingInput.value = btn.dataset.domainLanding || '';
            adminInput.value = btn.dataset.domainAdmin || '';
            emailInput.value = btn.dataset.email || '';
            idInput.readOnly = true;
            idInput.required = false;
            idInput.classList.add('cursor-not-allowed', 'border-slate-200', 'bg-slate-50', 'text-slate-700', 'focus:border-slate-200', 'focus:shadow-none');
            idInput.classList.remove('font-mono');
            const info = document.getElementById('default-users-info');
            if (info) info.classList.add('hidden');
            setModalState(true);
        }

        document.getElementById('open-create-modal').addEventListener('click', openCreateModal);

        document.querySelectorAll('.open-edit-modal').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openEditModal(this);
            });
        });

        document.getElementById('close-tenant-modal').addEventListener('click', function () { setModalState(false); });
        document.getElementById('cancel-tenant-modal').addEventListener('click', function () { setModalState(false); });
        tenantModal.addEventListener('click', function (event) {
            if (event.target === tenantModal) setModalState(false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !tenantModal.classList.contains('hidden')) {
                setModalState(false);
            }
        });

        // --- Handler domain ---
        tenantForm.addEventListener('submit', function (e) {
            // Client-side: cegah submit bila kedua domain identik
            const l = (landingInput.value || '').trim().toLowerCase();
            const a = (adminInput.value || '').trim().toLowerCase();
            if (l && a && l === a) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Domain tidak boleh sama',
                    text: 'Domain administrator dan domain landing page harus berbeda.',
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'inline-flex items-center justify-center px-5 py-2.5 rounded-lg bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200',
                    },
                });
                return;
            }
            const btn = document.getElementById('tenant-submit-btn');
            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');
            submitLabel.textContent = 'Menyimpan…';
        });
    </script>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
