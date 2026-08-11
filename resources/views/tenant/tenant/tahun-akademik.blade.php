<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahun Akademik — Tenant {{ env('APP_NAME') }}</title>
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
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Tahun Akademik</h2>
                <p class="mt-1 text-sm text-slate-500">Tahun akademik untuk tenant {{ $tenant->id }}.</p>
            </div>
            <button type="button" onclick="openCreate()" class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah
            </button>
        </header>

        @include('tenant.partials.tenant_subnav', ['tenant' => $tenant, 'active' => 'tahun-akademik'])

        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-[480px] w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 font-semibold">Tahun</th>
                            <th class="px-5 py-3 font-semibold">Keterangan</th>
                            <th class="px-5 py-3 font-semibold">Status</th>
                            <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($items as $ta)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-semibold text-slate-800">{{ $ta->nama_tahun }}</td>
                                <td class="px-5 py-3 text-slate-700">{{ $ta->keterangan ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @if ($ta->status === 'aktif')
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-600">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        @if ($ta->status !== 'aktif')
                                            <form action="{{ route('tenant.tenant.tahun-akademik.aktifkan', [$tenant, $ta]) }}" method="POST" class="inline">
                                                @csrf @method('POST')
                                                <button class="inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-emerald-50 hover:text-emerald-600" title="Aktifkan">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" onclick="openEdit('{{ $ta->id }}', '{{ addslashes($ta->nama_tahun) }}', '{{ addslashes($ta->keterangan) }}', '{{ $ta->status }}')" class="inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-amber-50 hover:text-amber-600" title="Edit">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('tenant.tenant.tahun-akademik.destroy', [$tenant, $ta]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tahun akademik {{ $ta->nama_tahun }}?');">
                                            @csrf @method('DELETE')
                                            <button class="inline-flex items-center rounded-md p-1.5 text-slate-500 hover:bg-rose-50 hover:text-rose-600" title="Hapus">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 3h6a1 1 0 011 1v2H8V4a1 1 0 011-1z"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-14 text-center text-sm text-slate-400">Belum ada tahun akademik.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <ul class="divide-y divide-slate-100 md:hidden">
                @forelse ($items as $ta)
                    <li class="px-4 py-3.5">
                        <div class="flex items-start justify-between gap-2">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-slate-900">{{ $ta->nama_tahun }}</p>
                                <p class="mt-0.5 truncate text-xs text-slate-600">{{ $ta->keterangan ?? '—' }}</p>
                                <div class="mt-1">
                                    @if ($ta->status === 'aktif')
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-0.5 text-[11px] font-semibold text-emerald-700">Aktif</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-600">Nonaktif</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 items-center gap-1">
                                @if ($ta->status !== 'aktif')
                                    <form action="{{ route('tenant.tenant.tahun-akademik.aktifkan', [$tenant, $ta]) }}" method="POST" class="inline">
                                        @csrf @method('POST')
                                        <button class="inline-flex items-center rounded-md bg-emerald-100 p-1.5 text-emerald-700 hover:bg-emerald-200" title="Aktifkan">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                        </button>
                                    </form>
                                @endif
                                <button type="button" onclick="openEdit('{{ $ta->id }}', '{{ addslashes($ta->nama_tahun) }}', '{{ addslashes($ta->keterangan) }}', '{{ $ta->status }}')" class="inline-flex items-center rounded-md bg-amber-100 p-1.5 text-amber-700 hover:bg-amber-200" title="Edit">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <form action="{{ route('tenant.tenant.tahun-akademik.destroy', [$tenant, $ta]) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tahun akademik {{ $ta->nama_tahun }}?');">
                                    @csrf @method('DELETE')
                                    <button class="inline-flex items-center rounded-md bg-rose-100 p-1.5 text-rose-700 hover:bg-rose-200" title="Hapus">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M9 3h6a1 1 0 011 1v2H8V4a1 1 0 011-1z"/></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-14 text-center text-sm text-slate-400">Belum ada tahun akademik.</li>
                @endforelse
            </ul>
        </section>
    </main>

    <div id="modal-create" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4">
        <div class="w-full max-w-md max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-xl">
            <form method="POST" action="{{ route('tenant.tenant.tahun-akademik.store', $tenant) }}" class="space-y-4 p-6">
                @csrf
                <h3 class="text-lg font-bold text-slate-900">Tambah Tahun Akademik</h3>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Tahun</label>
                    <input type="text" name="nama_tahun" required placeholder="2026/2027" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Keterangan</label>
                    <input type="text" name="keterangan" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Status</label>
                    <select name="status" required class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <option value="nonaktif">Nonaktif</option>
                        <option value="aktif">Aktif</option>
                    </select>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-create')" class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="modal-edit" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4">
        <div class="w-full max-w-md max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-xl">
            <form id="form-edit" method="POST" class="space-y-4 p-6">
                @csrf @method('PUT')
                <h3 class="text-lg font-bold text-slate-900">Edit Tahun Akademik</h3>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Nama Tahun</label>
                    <input id="edit-nama" type="text" name="nama_tahun" required class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Keterangan</label>
                    <input id="edit-ket" type="text" name="keterangan" class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Status</label>
                    <select id="edit-status" name="status" required class="mt-1 block w-full rounded-lg border-slate-200 text-sm shadow-sm focus:border-indigo-300 focus:ring-4 focus:ring-indigo-100">
                        <option value="nonaktif">Nonaktif</option>
                        <option value="aktif">Aktif</option>
                    </select>
                </div>
                <div class="flex items-center justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-edit')" class="inline-flex items-center rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Batal</button>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const updateUrls = @json($items->mapWithKeys(fn ($ta) => [$ta->id => route('tenant.tenant.tahun-akademik.update', [$tenant, $ta])])->all());

        function openCreate() { document.getElementById('modal-create').classList.remove('hidden'); document.getElementById('modal-create').classList.add('flex'); }
        function openEdit(id, nama, ket, status) {
            const form = document.getElementById('form-edit');
            form.action = updateUrls[id] || '';
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-ket').value = ket === 'null' ? '' : ket;
            document.getElementById('edit-status').value = status;
            document.getElementById('modal-edit').classList.remove('hidden'); document.getElementById('modal-edit').classList.add('flex');
        }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); document.getElementById(id).classList.remove('flex'); }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
