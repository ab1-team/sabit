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
    <link href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f8fafc; }
        .invoice-input { width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: .75rem; background: #fff; padding: .625rem .875rem; font-size: .875rem; color: #1e293b; transition: border-color .15s ease, box-shadow .15s ease; }
        .invoice-input::placeholder { color: #94a3b8; }
        .invoice-input:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .modal-scroll { max-height: calc(100vh - 2rem); overflow-y: auto; }
        .table-wrap { overflow-x: auto; }
        #tenants { width: 100% !important; }
        #tenants thead th { background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 600; font-size: .75rem; letter-spacing: .04em; text-transform: uppercase; padding: .75rem 1rem; white-space: nowrap; }
        #tenants tbody td { padding: .875rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        #tenants tbody tr:last-child td { border-bottom: 0; }
        #tenants tbody tr { transition: background-color .15s ease; }
        #tenants tbody tr:hover { background-color: #f8fafc; }
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

        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="table-wrap px-5 py-4">
                <table id="tenants" class="table align-items-center mb-0 w-full text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-600">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold">No</th>
                            <th class="px-3 py-3 text-left font-semibold">ID</th>
                            <th class="px-3 py-3 text-left font-semibold">Nama Sekolah</th>
                            <th class="px-3 py-3 text-left font-semibold">Domain Landing</th>
                            <th class="px-3 py-3 text-left font-semibold">Domain Administrator</th>
                            <th class="px-3 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="tenant-modal" class="{{ $initialModalOpen ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-scroll w-full max-w-3xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
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

    <div id="detail-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true" aria-labelledby="detail-modal-title">
        <div class="modal-scroll w-full max-w-2xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
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

    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
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

        document.addEventListener('click', function (e) {
            const btn = e.target.closest && e.target.closest('.open-detail-modal');
            if (btn) openDetailModal(btn);
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

        document.addEventListener('click', function (e) {
            const btn = e.target.closest && e.target.closest('.delete-tenant');
            if (!btn) return;
            const action = btn.dataset.action;
            const name = btn.dataset.name;
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

        document.addEventListener('click', function (e) {
            const btn = e.target.closest && e.target.closest('.open-edit-modal');
            if (btn) openEditModal(btn);
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

        tenantForm.addEventListener('submit', function (e) {
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

        const table = $('#tenants').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            responsive: true,
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            pageLength: 15,
            lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
            ajax: @json(route('tenant.tenant.data')),
            language: {
                emptyTable: 'Belum ada tenant.',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 data',
                infoFiltered: '(difilter dari _MAX_ total)',
                lengthMenu: 'Tampilkan _MENU_ data',
                loadingRecords: 'Memuat…',
                processing: 'Memproses…',
                search: 'Cari:',
                zeroRecords: 'Tidak ada tenant yang cocok.',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            },
            order: [[2, 'asc']],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, width: '4%' },
                { data: 'id', name: 'tenants.id', className: 'ps-3 font-mono text-slate-700' },
                { data: 'nama_sekolah', name: 'tenants.nama_sekolah', className: 'ps-3 font-semibold text-slate-800' },
                { data: 'domain_landing_html', name: 'domain_landing', orderable: false, searchable: false, className: 'ps-3' },
                { data: 'domain_admin_html', name: 'domain_admin', orderable: false, searchable: false, className: 'ps-3' },
                { data: 'action', orderable: false, searchable: false, className: 'text-center whitespace-nowrap' },
            ],
        });
    </script>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true }).then(function () { if (window.jQuery && $('#tenants').length && $.fn.DataTable) { $('#tenants').DataTable().ajax.reload(null, false); } });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
