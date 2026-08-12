@php
    $initialModalOpen = $errors->any();
    $tahunAkademikItem = $tahunAkademikItem ?? new \App\Models\TahunAkademik();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tahun Akademik — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('tenant.partials._fancy_inputs_head')
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
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Tahun Akademik</h2>
                <p class="mt-1 text-sm text-slate-500">Tahun akademik.</p>
            </div>
            <button type="button" id="add-tahun-akademik" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Tahun Akademik
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
                            <th class="px-5 py-3 text-center font-semibold">Aksi</th>
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
                                <td class="px-5 py-3 text-center">
                                    <div class="inline-flex items-center gap-1">
                                        @if ($ta->status !== 'aktif')
                                            <button type="button" class="activate-ta inline-flex items-center rounded-lg bg-emerald-100 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200" data-action="{{ route('tenant.tenant.tahun-akademik.aktifkan', [$tenant, $ta]) }}" data-name="{{ $ta->nama_tahun }}" title="Aktifkan">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        @endif
                                        <button type="button" class="open-edit-modal inline-flex items-center rounded-lg bg-indigo-100 px-2.5 py-1.5 text-xs font-semibold text-indigo-700 hover:bg-indigo-200"
                                            data-update-url="{{ route('tenant.tenant.tahun-akademik.update', [$tenant, $ta]) }}"
                                            data-nama="{{ $ta->nama_tahun }}"
                                            data-ket="{{ $ta->keterangan }}"
                                            data-status="{{ $ta->status }}">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <button type="button" class="delete-ta inline-flex items-center rounded-lg bg-rose-100 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-200" data-action="{{ route('tenant.tenant.tahun-akademik.destroy', [$tenant, $ta]) }}" data-name="{{ $ta->nama_tahun }}">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.48 0 00-7.5 0"/></svg>
                                        </button>
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
                                    <button type="button" class="activate-ta inline-flex items-center rounded-lg bg-emerald-100 p-1.5 text-emerald-700 hover:bg-emerald-200" data-action="{{ route('tenant.tenant.tahun-akademik.aktifkan', [$tenant, $ta]) }}" data-name="{{ $ta->nama_tahun }}" title="Aktifkan">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                @endif
                                <button type="button" class="open-edit-modal inline-flex items-center rounded-lg bg-indigo-100 p-1.5 text-indigo-700 hover:bg-indigo-200"
                                    data-update-url="{{ route('tenant.tenant.tahun-akademik.update', [$tenant, $ta]) }}"
                                    data-nama="{{ $ta->nama_tahun }}"
                                    data-ket="{{ $ta->keterangan }}"
                                    data-status="{{ $ta->status }}">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button type="button" class="delete-ta inline-flex items-center rounded-lg bg-rose-100 p-1.5 text-rose-700 hover:bg-rose-200" data-action="{{ route('tenant.tenant.tahun-akademik.destroy', [$tenant, $ta]) }}" data-name="{{ $ta->nama_tahun }}">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.48 0 00-7.5 0"/></svg>
                                </button>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="px-5 py-14 text-center text-sm text-slate-400">Belum ada tahun akademik.</li>
                @endforelse
            </ul>
        </section>
    </main>

    <div id="ta-modal" class="{{ $initialModalOpen ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-scroll w-full max-w-2xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-900">Tambah Tahun Akademik</h3>
                    <p id="modal-description" class="mt-1 text-sm text-slate-500">Tambahkan tahun akademik baru.</p>
                </div>
                <button type="button" id="close-ta-modal" aria-label="Tutup dialog" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($errors->any())
                <div class="mx-5 mt-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 sm:mx-6">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <span>Periksa kembali field yang ditandai dan coba lagi.</span>
                </div>
            @endif

            <form id="ta-form" action="{{ route('tenant.tenant.tahun-akademik.store', $tenant) }}" method="POST" class="px-5 py-5 sm:px-6">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @include('tenant.tenant._formulir_tahun_akademik', ['tahunAkademikItem' => $tahunAkademikItem])
                </div>
                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-ta-modal" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">Batal</button>
                    <button type="submit" id="submit-ta" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span id="submit-label">Tambah Tahun Akademik</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form id="activate-ta-form" method="POST" class="hidden">
        @csrf
    </form>
    <form id="delete-ta-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @include('tenant.partials._fancy_inputs_scripts')
    <script>
        const taModal = document.getElementById('ta-modal');
        const taForm = document.getElementById('ta-form');
        const formMethod = document.getElementById('form-method');
        const submitLabel = document.getElementById('submit-label');
        const modalTitle = document.getElementById('modal-title');
        const modalDesc = document.getElementById('modal-description');
        const deleteForm = document.getElementById('delete-ta-form');
        const activateForm = document.getElementById('activate-ta-form');

        function setModalState(open) {
            taModal.classList.toggle('hidden', !open);
            taModal.classList.toggle('flex', open);
            document.body.classList.toggle('overflow-hidden', open);
        }

        function openCreateModal() {
            taForm.action = @json(route('tenant.tenant.tahun-akademik.store', $tenant));
            formMethod.value = 'POST';
            taForm.reset();
            document.getElementById('status').value = 'nonaktif';
            modalTitle.textContent = 'Tambah Tahun Akademik';
            modalDesc.textContent = 'Tambahkan tahun akademik baru.';
            submitLabel.textContent = 'Tambah Tahun Akademik';
            if (window.initFancyInputs) window.initFancyInputs('#ta-modal');
            document.getElementById('nama_tahun').focus();
        }

        function openEditModal(btn) {
            taForm.action = btn.dataset.updateUrl;
            formMethod.value = 'PUT';
            document.getElementById('nama_tahun').value = btn.dataset.nama || '';
            document.getElementById('keterangan').value = btn.dataset.ket || '';
            document.getElementById('status').value = btn.dataset.status || 'nonaktif';
            modalTitle.textContent = 'Ubah Tahun Akademik';
            modalDesc.textContent = 'Perbarui tahun akademik ' + (btn.dataset.nama || '') + '.';
            submitLabel.textContent = 'Simpan Perubahan';
            if (window.initFancyInputs) window.initFancyInputs('#ta-modal');
            document.getElementById('nama_tahun').focus();
        }

        document.getElementById('add-tahun-akademik').addEventListener('click', openCreateModal);
        document.querySelectorAll('.open-edit-modal').forEach(function (btn) {
            btn.addEventListener('click', function () { openEditModal(this); });
        });
        document.getElementById('close-ta-modal').addEventListener('click', function () { setModalState(false); });
        document.getElementById('cancel-ta-modal').addEventListener('click', function () { setModalState(false); });
        taModal.addEventListener('click', function (event) {
            if (event.target === taModal) setModalState(false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !taModal.classList.contains('hidden')) {
                setModalState(false);
            }
        });

        taForm.addEventListener('submit', function () {
            const btn = document.getElementById('submit-ta');
            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');
            submitLabel.textContent = 'Menyimpan…';
        });

        document.querySelectorAll('.activate-ta').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = this.dataset.action;
                const name = this.dataset.name;
                Swal.fire({
                    title: 'Aktifkan tahun akademik ini?',
                    text: 'Tahun akademik ' + name + ' akan diaktifkan (nonaktifkan yang lain).',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, aktifkan',
                    cancelButtonText: 'Batal',
                    reverseButtons: true,
                    buttonsStyling: false,
                    customClass: {
                        confirmButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-emerald-600 text-white font-semibold text-sm hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200 mx-1',
                        cancelButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 focus:outline-none focus:ring-4 focus:ring-slate-200 mx-1',
                    },
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    activateForm.action = action;
                    activateForm.submit();
                });
            });
        });

        document.querySelectorAll('.delete-ta').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const action = this.dataset.action;
                const name = this.dataset.name;
                Swal.fire({
                    title: 'Hapus tahun akademik ini?',
                    text: 'Tahun akademik ' + name + ' akan dihapus. Tindakan ini tidak dapat dibatalkan.',
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
    </script>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
