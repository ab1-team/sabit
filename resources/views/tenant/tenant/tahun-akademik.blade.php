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

        table.dataTable thead th { font-weight: 600; }
        table.dataTable tbody td { border-bottom: 1px solid #f1f5f9; }
        table.dataTable tbody tr:last-child td { border-bottom: 0; }
        .dataTables_wrapper .dataTables_filter input { border-radius: .75rem; border: 1px solid #cbd5e1; padding: .45rem .75rem; font-size: .875rem; }
        .dataTables_wrapper .dataTables_filter input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .12); }
        .dataTables_wrapper .dataTables_length select { border-radius: .75rem; border: 1px solid #cbd5e1; padding: .3rem 2rem .3rem .75rem; font-size: .875rem; background-color: #fff; }
        .dataTables_wrapper .dataTables_info { padding-top: 1rem; font-size: .8125rem; color: #475569; }
        .dataTables_wrapper .dataTables_paginate { padding-top: 1rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { padding: .35rem .7rem; margin: 0 .12rem; border-radius: .5rem; border: 1px solid #e2e8f0; background: #fff; color: #334155; font-size: .8125rem; font-weight: 600; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #6366f1 !important; color: #fff !important; border-color: #6366f1 !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover { background: #f1f5f9 !important; color: #6366f1 !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { color: #cbd5e1 !important; cursor: not-allowed; }
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control { padding-left: 2rem; }
        @media (max-width: 767.98px) {
            .dataTables_wrapper .dataTables_filter { text-align: left; }
        }
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
            <div class="p-4 sm:p-5 overflow-x-auto">
                <table id="ta-table" class="w-full text-sm" style="width:100%">
                    <thead class="bg-slate-50 text-left text-xs uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-3 py-3 font-semibold">No</th>
                            <th class="px-3 py-3 font-semibold">Tahun</th>
                            <th class="px-3 py-3 font-semibold">Keterangan</th>
                            <th class="px-3 py-3 font-semibold">Status</th>
                            <th class="px-3 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
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
                <div class="mt-6 flex flex-col gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="submit" id="submit-ta" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 sm:w-auto">
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
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">
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
            setModalState(true);
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
            setModalState(true);
            if (window.initFancyInputs) window.initFancyInputs('#ta-modal');
            document.getElementById('nama_tahun').focus();
        }

        document.getElementById('add-tahun-akademik').addEventListener('click', openCreateModal);
        document.addEventListener('click', function (e) {
            var editBtn = e.target.closest('.open-edit-modal');
            if (editBtn) { openEditModal(editBtn); return; }
            var delBtn = e.target.closest('.delete-ta');
            if (delBtn) {
                Swal.fire({
                    title: 'Hapus tahun akademik ini?',
                    text: 'Tahun akademik ' + delBtn.dataset.name + ' akan dihapus. Tindakan ini tidak dapat dibatalkan.',
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
                    deleteForm.action = delBtn.dataset.action;
                    deleteForm.submit();
                });
            }
            var actBtn = e.target.closest('.activate-ta');
            if (actBtn) {
                Swal.fire({
                    title: 'Aktifkan tahun akademik ini?',
                    text: 'Tahun akademik ' + actBtn.dataset.name + ' akan diaktifkan (nonaktifkan yang lain).',
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
                    activateForm.action = actBtn.dataset.action;
                    activateForm.submit();
                });
            }
        });
        document.getElementById('close-ta-modal').addEventListener('click', function () { setModalState(false); });
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

        $(function () {
            $('#ta-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                searching: true,
                pageLength: 15,
                lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
                ajax: {
                    url: @json(route('tenant.tenant.tahun-akademik.data', $tenant)),
                    error: function (xhr) {
                        console.error('DataTables ta error:', xhr.status, xhr.responseText);
                    }
                },
                order: [[1, 'desc']],
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'px-3 py-3 text-slate-500' },
                    { data: 'nama_tahun', name: 'nama_tahun', className: 'px-3 py-3 font-semibold text-slate-800' },
                    { data: 'keterangan_text', name: 'keterangan', orderable: false, searchable: true, className: 'px-3 py-3 text-slate-700' },
                    { data: 'status_badge', name: 'status', orderable: true, searchable: false, className: 'px-3 py-3' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'px-3 py-3 text-center' },
                ],
                language: {
                    emptyTable: 'Belum ada tahun akademik.',
                    info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                    infoEmpty: 'Menampilkan 0 dari 0 data',
                    infoFiltered: '(filter dari _MAX_ total)',
                    lengthMenu: 'Tampilkan _MENU_',
                    loadingRecords: 'Memuat…',
                    processing: 'Memproses…',
                    search: 'Cari:',
                    zeroRecords: 'Tidak ada hasil.',
                    paginate: { first: '«', last: '»', next: '›', previous: '‹' }
                }
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
