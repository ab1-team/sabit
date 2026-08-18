@php
    $initialModalOpen = $errors->any();
    $kelasItem = $kelasItem ?? new \App\Models\Kelas();
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelas — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    @include('tenant.partials._fancy_inputs_head')
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f8fafc; }
        .invoice-input { width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: .75rem; background: #fff; padding: .625rem .875rem; font-size: .875rem; color: #1e293b; transition: border-color .15s ease, box-shadow .15s ease; }
        .invoice-input::placeholder { color: #94a3b8; }
        .invoice-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .modal-scroll { max-height: calc(100vh - 2rem); overflow-y: auto; }
        .select2-container { width: 100% !important; }
        .select2-container--bootstrap-5 .select2-selection { min-height: 44px; border-color: #cbd5e1; border-radius: .75rem; padding: .375rem .875rem; font-size: .875rem; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding: .25rem 1.5rem .25rem 0; color: #1e293b; line-height: 1.75rem; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { top: 9px; right: 10px; }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .select2-container--bootstrap-5 .select2-dropdown { border-color: #cbd5e1; border-radius: .75rem; overflow: hidden; }
        .modal-scroll .select2-container--open { z-index: 70; }
        .modal-scroll .select2-container--bootstrap-5 .select2-dropdown { z-index: 70; }
        .modal-scroll .select2-container--bootstrap-5 .select2-search__field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, .15); }

        /* DataTables tema invoice-style */
        .table-wrap { overflow-x: auto; }
        #kelas { width: 100% !important; }
        #kelas thead th { background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 600; font-size: .75rem; letter-spacing: .04em; text-transform: uppercase; padding: .75rem 1rem; white-space: nowrap; }
        #kelas tbody td { padding: .875rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        #kelas tbody tr:last-child td { border-bottom: 0; }
        #kelas tbody tr { transition: background-color .15s ease; }
        #kelas tbody tr:hover { background-color: #f8fafc; }
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter { margin-bottom: .75rem; }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label { color: #475569; font-size: .8125rem; font-weight: 500; display: inline-flex; align-items: center; gap: .5rem; }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #cbd5e1 !important; border-radius: .5rem !important; padding: .375rem .625rem !important; font-size: .875rem !important; color: #1e293b; background: #fff; }
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus { border-color: #6366f1 !important; outline: none !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important; }
        .dataTables_wrapper .dataTables_filter input { min-width: 220px; }
        .dataTables_wrapper .dataTables_info { color: #64748b !important; font-size: .8125rem !important; padding-top: .75rem; }
        .dataTables_wrapper .dataTables_paginate { padding-top: .75rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button { box-sizing: border-box; display: inline-flex; align-items: center; justify-content: center; min-width: 2.25rem; height: 2.25rem; padding: 0 .65rem !important; margin: 0 2px !important; border-radius: .5rem !important; border: 1px solid #e2e8f0 !important; background: #fff !important; color: #475569 !important; font-weight: 600; font-size: .8125rem; cursor: pointer; transition: all .15s ease; }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input { max-width: 100%; }
        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_filter { float: none; text-align: left; }
            .dataTables_wrapper .dataTables_length { float: none; text-align: left; }
            .dataTables_wrapper .dataTables_filter input { width: 100%; min-width: 0; margin-left: 0; }
            .dataTables_wrapper .dataTables_paginate .paginate_button { min-width: 2rem; height: 2rem; font-size: .75rem; padding: 0 .5rem !important; margin: 0 1px !important; }
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) { background: #eef2ff !important; border-color: #c7d2fe !important; color: #4f46e5 !important; }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current { background: #4f46e5 !important; border-color: #4f46e5 !important; color: #fff !important; box-shadow: 0 1px 2px rgba(79, 70, 229, .25); }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { color: #cbd5e1 !important; background: #f8fafc !important; border-color: #f1f5f9 !important; cursor: not-allowed; }
        .dataTables_wrapper .dataTables_processing { background: rgba(255, 255, 255, .85) !important; border: 1px solid #e2e8f0 !important; border-radius: .75rem !important; color: #475569 !important; font-weight: 600 !important; box-shadow: 0 10px 25px rgba(15, 23, 42, .08); }
        .dataTables_empty { padding: 2.5rem 1rem !important; color: #94a3b8 !important; font-size: .875rem; }
    </style>
</head>
<body class="min-h-screen text-slate-800">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('tenant.partials.bilah-atas')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <header class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Kelas</h2>
                <p class="mt-1 text-sm text-slate-500">Daftar kelas.</p>
            </div>
            <button type="button" id="add-kelas" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Tambah Kelas
            </button>
        </header>

        @include('tenant.partials.tenant_subnav', ['tenant' => $tenant, 'active' => 'kelas'])

        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="table-wrap px-5 py-4">
                <table id="kelas" class="table align-items-center mb-0 w-full text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-600">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold">No</th>
                            <th class="px-3 py-3 text-left font-semibold">Kode</th>
                            <th class="px-3 py-3 text-left font-semibold">Nama Kelas</th>
                            <th class="px-3 py-3 text-left font-semibold">Tingkat</th>
                            <th class="px-3 py-3 text-left font-semibold">Kurikulum</th>
                            <th class="px-3 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="kelas-modal" class="{{ $initialModalOpen ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-scroll w-full max-w-2xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-900">Tambah Kelas</h3>
                    <p id="modal-description" class="mt-1 text-sm text-slate-500">Tambahkan data kelas baru.</p>
                </div>
                <button type="button" id="close-kelas-modal" aria-label="Tutup dialog" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($errors->any())
                <div class="mx-5 mt-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 sm:mx-6">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <span>Periksa kembali field yang ditandai dan coba lagi.</span>
                </div>
            @endif

            <form id="kelas-form" action="{{ route('tenant.tenant.kelas.store', $tenant) }}" method="POST" class="px-5 py-5 sm:px-6">
                @csrf
                <input type="hidden" name="_method" id="form-method" value="POST">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @include('tenant.tenant._formulir_kelas', ['kelasItem' => $kelasItem])
                </div>
                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="submit" id="submit-kelas" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 sm:w-auto">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span id="submit-label">Tambah Kelas</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form id="delete-kelas-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    @include('tenant.partials._fancy_inputs_scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        const kelasModal = document.getElementById('kelas-modal');
        const kelasForm = document.getElementById('kelas-form');
        const formMethod = document.getElementById('form-method');
        const submitLabel = document.getElementById('submit-label');
        const modalTitle = document.getElementById('modal-title');
        const modalDesc = document.getElementById('modal-description');
        const deleteForm = document.getElementById('delete-kelas-form');

        $('#kode_kurikulum').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: $('#kode_kurikulum').data('placeholder'),
            allowClear: false,
            minimumResultsForSearch: 0,
            dropdownParent: $('#kelas-modal'),
        });

        function setModalState(open) {
            kelasModal.classList.toggle('hidden', !open);
            kelasModal.classList.toggle('flex', open);
            document.body.classList.toggle('overflow-hidden', open);
        }

        const table = $('#kelas').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            responsive: true,
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
            ajax: {
                url: @json(route('tenant.tenant.kelas.data', $tenant)),
                error: function (xhr, error, thrown) {
                    console.error('[DataTables kelas] AJAX error:', error, thrown);
                    console.error('[DataTables kelas] Status:', xhr.status);
                    console.error('[DataTables kelas] Response:', xhr.responseText ? xhr.responseText.substring(0, 1500) : '(empty)');
                    var msg = '<div class="px-5 py-4 text-left"><div class="font-semibold text-rose-700">Gagal memuat data kelas (HTTP ' + xhr.status + ').</div><pre class="mt-2 max-h-72 overflow-auto whitespace-pre-wrap break-words rounded-lg bg-slate-50 p-3 text-xs text-slate-700">' + (xhr.responseText ? $('<div>').text(xhr.responseText.substring(0, 1500)).html() : '(kosong)') + '</pre></div>';
                    $('#kelas tbody').html('<tr><td colspan="6">' + msg + '</td></tr>');
                },
            },
            language: {
                emptyTable: 'Belum ada kelas.',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 data',
                infoFiltered: '(difilter dari _MAX_ total)',
                lengthMenu: 'Tampilkan _MENU_ data',
                loadingRecords: 'Memuat…',
                processing: 'Memproses…',
                search: 'Cari:',
                zeroRecords: 'Tidak ada kelas yang cocok.',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            },
            order: [[1, 'asc']],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, className: 'ps-3 text-slate-500 tabular-nums' },
                { data: 'kode_kelas', name: 'kelas.kode_kelas', className: 'ps-3 font-mono text-xs font-semibold text-slate-700 whitespace-nowrap' },
                { data: 'nama_kelas', name: 'kelas.nama_kelas', className: 'ps-3 font-semibold text-slate-800' },
                { data: 'tingkat', name: 'kelas.tingkat', className: 'ps-3' },
                { data: 'kurikulum_nama', name: 'kurikulum.nama_kurikulum', orderable: false, className: 'ps-3 text-slate-700' },
                { data: 'action', orderable: false, searchable: false, className: 'text-center whitespace-nowrap' },
            ],
        });

        function openCreateModal() {
            kelasForm.action = @json(route('tenant.tenant.kelas.store', $tenant));
            formMethod.value = 'POST';
            kelasForm.reset();
            $('#kode_kurikulum').val('').trigger('change');
            modalTitle.textContent = 'Tambah Kelas';
            modalDesc.textContent = 'Tambahkan data kelas baru.';
            submitLabel.textContent = 'Tambah Kelas';
            setModalState(true);
            if (window.initFancyInputs) window.initFancyInputs('#kelas-modal');
            document.getElementById('kode_kelas').focus();
        }

        function openEditModal(btn) {
            kelasForm.action = btn.dataset.updateUrl;
            formMethod.value = 'PUT';
            document.getElementById('kode_kelas').value = btn.dataset.kode || '';
            document.getElementById('nama_kelas').value = btn.dataset.nama || '';
            document.getElementById('tingkat').value = btn.dataset.tingkat || '';
            $('#kode_kurikulum').val(String(btn.dataset.kurikulum || '')).trigger('change');
            modalTitle.textContent = 'Ubah Kelas';
            modalDesc.textContent = 'Perbarui data kelas ' + (btn.dataset.kode || '') + '.';
            submitLabel.textContent = 'Simpan Perubahan';
            setModalState(true);
            if (window.initFancyInputs) window.initFancyInputs('#kelas-modal');
            document.getElementById('kode_kelas').focus();
        }

        document.getElementById('add-kelas').addEventListener('click', openCreateModal);

        $('#kelas').on('click', '.open-edit-modal', function () {
            openEditModal(this);
        });

        document.getElementById('close-kelas-modal').addEventListener('click', function () { setModalState(false); });
        kelasModal.addEventListener('click', function (event) {
            if (event.target === kelasModal) setModalState(false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !kelasModal.classList.contains('hidden')) {
                setModalState(false);
            }
        });

        kelasForm.addEventListener('submit', function () {
            const btn = document.getElementById('submit-kelas');
            btn.disabled = true;
            btn.classList.add('opacity-60', 'cursor-not-allowed');
            submitLabel.textContent = 'Menyimpan…';
        });

        $('#kelas').on('click', '.delete-kelas', function () {
            const action = this.dataset.action;
            const name = this.dataset.name;
            Swal.fire({
                title: 'Hapus kelas ini?',
                text: 'Kelas ' + name + ' akan dihapus. Tindakan ini tidak dapat dibatalkan.',
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

        @if ($errors->any())
        setModalState(true);
        @endif
    </script>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true }).then(function () { table.ajax.reload(null, false); });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
