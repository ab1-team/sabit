@php
    $initialModalOpen = $errors->any();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoices — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
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
        .select2-container { width: 100% !important; }
        .select2-container--bootstrap-5 .select2-selection { min-height: 44px; border-color: #cbd5e1; border-radius: .75rem; padding: .375rem .875rem; font-size: .875rem; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding: .25rem 1.5rem .25rem 0; color: #1e293b; line-height: 1.75rem; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { top: 9px; right: 10px; }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .select2-container--bootstrap-5 .select2-dropdown { border-color: #cbd5e1; border-radius: .75rem; overflow: hidden; }
        .flatpickr-input { cursor: pointer; }
        .flatpickr-calendar { z-index: 60; }
        .modal-scroll .select2-container--open { z-index: 70; }
        .modal-scroll .select2-container--bootstrap-5 .select2-dropdown { z-index: 70; }
        .modal-scroll .select2-container--bootstrap-5 .select2-search__field:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99, 102, 241, .15); }
        .modal-scroll .nominal { letter-spacing: .01em; }
        .modal-scroll .nominal:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .modal-scroll .input-group { width: 100%; }
        .modal-scroll .input-group-text { border-color: #cbd5e1; background: #f8fafc; color: #64748b; }
        .modal-scroll .flatpickr-calendar { box-shadow: 0 10px 30px rgba(15, 23, 42, .16); }
        .modal-scroll .flatpickr-day.selected, .modal-scroll .flatpickr-day.startRange, .modal-scroll .flatpickr-day.endRange { background: #4f46e5; border-color: #4f46e5; }
        .modal-scroll .flatpickr-day.today { border-color: #6366f1; }
        .modal-scroll .flatpickr-months .flatpickr-month, .modal-scroll .flatpickr-current-month .flatpickr-monthDropdown-months { color: #1e293b; }
        .table-wrap { overflow-x: auto; }
        #invoices { width: 100% !important; }
        #invoices thead th { background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%); border-bottom: 1px solid #e2e8f0; color: #475569; font-weight: 600; font-size: .75rem; letter-spacing: .04em; text-transform: uppercase; padding: .75rem 1rem; white-space: nowrap; }
        #invoices tbody td { padding: .875rem 1rem; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
        #invoices tbody tr:last-child td { border-bottom: 0; }
        #invoices tbody tr { transition: background-color .15s ease; }
        #invoices tbody tr:hover { background-color: #f8fafc; }
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
    @include('tenant.partials.topbar')

    <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-sm font-semibold text-indigo-600">Tenant Console</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Invoices</h2>
                <p class="mt-1 text-sm text-slate-500">Manage invoice records and payment status.</p>
            </div>
            <button type="button" id="add-invoice" class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-600/20 transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 sm:w-auto">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add invoice
            </button>
        </header>

        @include('tenant.partials.tenant-filter')

        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="table-wrap px-5 py-4">
                <table id="invoices" class="table align-items-center mb-0 w-full text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-600">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold">No</th>
                            <th class="px-3 py-3 text-left font-semibold">Payment type</th>
                            <th class="px-3 py-3 text-left font-semibold">Owner</th>
                            <th class="px-3 py-3 text-left font-semibold">Sekolah</th>
                            <th class="px-3 py-3 text-left font-semibold">Invoice date</th>
                            <th class="px-3 py-3 text-left font-semibold">Paid date</th>
                            <th class="px-3 py-3 text-right font-semibold">Amount</th>
                            <th class="px-3 py-3 text-center font-semibold">Status</th>
                            <th class="px-3 py-3 text-center font-semibold">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="invoice-modal" class="{{ $initialModalOpen ? 'flex' : 'hidden' }} fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true" aria-labelledby="modal-title">
        <div class="modal-scroll w-full max-w-2xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h3 id="modal-title" class="text-lg font-bold text-slate-900">Tambah Invoice</h3>
                    <p id="modal-description" class="mt-1 text-sm text-slate-500">Buat record invoice baru.</p>
                </div>
                <button type="button" id="close-invoice-modal" aria-label="Close dialog" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @if ($errors->any())
                <div class="mx-5 mt-5 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 sm:mx-6">
                    <svg class="mt-0.5 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z"/></svg>
                    <span>Periksa kembali field yang ditandai dan coba lagi.</span>
                </div>
            @endif

            <form id="invoice-form" action="{{ route('tenant.invoice.store') }}" method="POST" class="px-5 py-5 sm:px-6">
                @csrf
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @include('tenant.invoice._form')
                </div>
                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-invoice-modal" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">Batal</button>
                    <button type="submit" id="submit-invoice" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        <span id="submit-label">Tambah Invoice</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="payment-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true">
        <div class="w-full max-w-2xl max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4 sm:px-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Catat Pembayaran</h3>
                    <p id="payment-invoice-id" class="mt-1 text-sm text-slate-500"></p>
                </div>
                <button type="button" id="close-payment-modal" aria-label="Close" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700 focus:outline-none focus:ring-4 focus:ring-indigo-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <form id="payment-form" action="{{ route('tenant.transaksi.store') }}" method="POST" class="px-5 py-5 sm:px-6">
                @csrf
                <input type="hidden" id="payment-idv" name="idv">

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Payment type</label>
                        <input type="text" id="payment-jenis" readonly tabindex="-1" class="invoice-input cursor-not-allowed border-slate-200 bg-slate-50 font-medium text-slate-700 focus:border-slate-200 focus:shadow-none">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-sm font-semibold text-slate-700">Owner</label>
                        <input type="text" id="payment-owner" readonly tabindex="-1" class="invoice-input cursor-not-allowed border-slate-200 bg-slate-50 font-medium text-slate-700 focus:border-slate-200 focus:shadow-none">
                    </div>

                    <div>
                        <label for="payment-jumlah-input" class="mb-1.5 block text-sm font-semibold text-slate-700">Jumlah <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm font-semibold text-slate-400">Rp</span>
                            <input type="text" id="payment-jumlah-input" name="jumlah" inputmode="numeric" required class="invoice-input pl-10" value="1,000,000" autocomplete="off">
                        </div>
                        @error('jumlah')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="payment-tgl-lunas" class="mb-1.5 block text-sm font-semibold text-slate-700">Tgl Lunas</label>
                        <input type="text" id="payment-tgl-lunas" name="tgl_lunas" class="invoice-input" placeholder="Pilih tanggal" autocomplete="off">
                        @error('tgl_lunas')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="payment-rekening" class="mb-1.5 block text-sm font-semibold text-slate-700">Rekening <span class="text-rose-500">*</span></label>
                        <select id="payment-rekening" name="rekening_debit" required class="invoice-input select2 bg-white" data-placeholder="Pilih rekening">
                            <option value="">Pilih rekening</option>
                            @foreach (\App\Models\AdminRekening::orderBy('nama_rekening')->get() as $r)
                                <option value="{{ $r->kd_rekening }}">{{ $r->nama_rekening }}</option>
                            @endforeach
                        </select>
                        @error('rekening_debit')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="payment-keterangan" class="mb-1.5 block text-sm font-semibold text-slate-700">Keterangan</label>
                        <input type="text" id="payment-keterangan" name="keterangan" class="invoice-input" placeholder="Opsional">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50/60 px-3 py-3 transition hover:bg-emerald-50">
                            <input type="checkbox" id="payment-mark-paid" name="mark_paid" value="1" checked class="mt-0.5 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                            <div class="flex-1">
                                <span class="block text-sm font-semibold text-emerald-800">Tandai sebagai Paid</span>
                                <span class="block text-xs text-emerald-700/80">Centang untuk langsung mengubah status invoice menjadi lunas.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-payment-modal" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">Batal</button>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <form id="delete-invoice-form" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
    <script>
        const invoiceModal = document.getElementById('invoice-modal');
        const invoiceForm = document.getElementById('invoice-form');
        const deleteForm = document.getElementById('delete-invoice-form');
        const amountInput = $('#jumlah');

        const invoiceDatePicker = flatpickr('#tgl_invoice', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y',
            altInputClass: 'invoice-input',
            allowInput: true,
            disableMobile: true,
        });
        const paymentDatePicker = flatpickr('#payment-tgl-lunas', {
            dateFormat: 'Y-m-d',
            altInput: true,
            altFormat: 'd F Y',
            altInputClass: 'invoice-input',
            allowInput: true,
            disableMobile: true,
        });
        $('#status, #jenis_pembayaran, #tenant_id').each(function () {
            $(this).select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: $(this).data('placeholder'),
                allowClear: false,
                minimumResultsForSearch: (this.id === 'status' || this.id === 'jenis_pembayaran') ? Infinity : 0,
                dropdownParent: $('#invoice-modal'),
            });
        });

        $('#payment-rekening').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih rekening',
            minimumResultsForSearch: Infinity,
            dropdownParent: $('#payment-modal'),
        });

        amountInput.maskMoney({
            prefix: '',
            thousands: ',',
            decimal: '.',
            precision: 0,
            allowZero: true,
            allowNegative: false,
        });

        function setAmount(value) {
            if (value === '' || value === null || typeof value === 'undefined') {
                amountInput.val('');
                return;
            }
            amountInput.maskMoney('mask', Number(String(value).replace(/,/g, '')));
        }

        if (amountInput.val() !== '') {
            setAmount(amountInput.val());
        }

        const table = $('#invoices').DataTable({
            processing: true,
            serverSide: true,
            searching: true,
            responsive: true,
            scrollX: true,
            scrollCollapse: true,
            autoWidth: false,
            pageLength: 10,
            lengthMenu: [[10, 15, 25, 50, 100], [10, 15, 25, 50, 100]],
            ajax: {
                url: @json(route('tenant.invoice.data')),
            },
            language: {
                emptyTable: 'Belum ada invoice.',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Menampilkan 0 data',
                infoFiltered: '(difilter dari _MAX_ total)',
                lengthMenu: 'Tampilkan _MENU_ data',
                loadingRecords: 'Memuat…',
                processing: 'Memproses…',
                search: 'Cari:',
                zeroRecords: 'Tidak ada invoice yang cocok.',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            },
            order: [[4, 'desc']],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, width: '4%' },
                { data: 'jenis_pembayaran', name: 'admin_invoice.jenis_pembayaran', className: 'ps-3 font-semibold text-slate-800 whitespace-nowrap' },
                { data: 'owner', name: 'owner', className: 'ps-3 text-slate-700' },
                { data: 'tenant_nama', name: 'tenant_nama', className: 'ps-3' },
                { data: 'tgl_invoice_fmt', name: 'admin_invoice.tgl_invoice', className: 'ps-3 text-slate-600 whitespace-nowrap' },
                { data: 'tgl_lunas_fmt', name: 'admin_invoice.tgl_lunas', className: 'ps-3 text-slate-600 whitespace-nowrap' },
                { data: 'jumlah_fmt', name: 'admin_invoice.jumlah', className: 'ps-3 text-right text-slate-900 font-semibold tabular-nums whitespace-nowrap' },
                { data: 'status_badge', name: 'admin_invoice.status', className: 'text-center' },
                { data: 'action', orderable: false, searchable: false, className: 'text-center whitespace-nowrap' },
            ],
            columnDefs: [
                { targets: 0, orderable: false, searchable: false },
                { targets: -1, orderable: false, searchable: false },
            ],
            rowCallback: function (row, data) {
                $(row).attr('data-invoice-id', data.id);
                $(row).attr('data-payment-type', data.jenis_pembayaran || '');
                $(row).attr('data-owner', data.owner || '');
                $(row).attr('data-invoice-date', data.tgl_invoice_fmt || '');
                $(row).attr('data-amount', data.jumlah_fmt || '');
                $(row).attr('data-status', data.status || '');
                $(row).attr('data-tenant-nama', data.tenant_nama || '');
                if (data.status === 'unpaid') {
                    $(row).addClass('cursor-pointer hover:bg-indigo-50/50');
                }
            },
        });

        function setModalState(open) {
            invoiceModal.classList.toggle('hidden', !open);
            invoiceModal.classList.toggle('flex', open);
            document.body.classList.toggle('overflow-hidden', open);
        }

        function openCreateModal() {
            invoiceForm.reset();
            $('#jenis_pembayaran').val('').trigger('change');
            $('#tenant_id').val('').trigger('change');
            $('#status').val('unpaid').trigger('change');
            invoiceDatePicker.clear();
            amountInput.val('');
            setModalState(true);
            $('#jenis_pembayaran').focus();
        }

        invoiceForm.addEventListener('submit', function () {
            amountInput.val(amountInput.maskMoney('unmasked')[0]);
        });

        document.getElementById('add-invoice').addEventListener('click', openCreateModal);

        $('#invoices').on('click', '.delete-invoice', function () {
            const action = this.dataset.action;
            Swal.fire({
                title: 'Delete this invoice?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                buttonsStyling: false,
                customClass: {
                    confirmButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-rose-600 text-white font-semibold text-sm hover:bg-rose-700 mx-1',
                    cancelButton: 'inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 mx-1',
                },
            }).then(function (result) {
                if (!result.isConfirmed) return;
                deleteForm.action = action;
                deleteForm.submit();
            });
        });

        const paymentModal = document.getElementById('payment-modal');
        const paymentForm = document.getElementById('payment-form');

        function setPaymentModalState(open) {
            paymentModal.classList.toggle('hidden', !open);
            paymentModal.classList.toggle('flex', open);
            document.body.classList.toggle('overflow-hidden', open);
        }

        $('#invoices').on('click', '.print-invoice', function () {
            window.open(this.dataset.url, '_blank');
        });

        const paymentJumlah = $('#payment-jumlah-input').maskMoney({
            prefix: '',
            thousands: ',',
            decimal: '.',
            precision: 0,
            allowZero: true,
            allowNegative: false,
        });

        paymentForm.addEventListener('submit', function () {
            paymentJumlah.val(paymentJumlah.maskMoney('unmasked')[0]);
        });

        $('#invoices tbody').on('click', 'tr', function (e) {
            if ($(e.target).closest('.print-invoice, .delete-invoice').length) return;
            if (this.dataset.status !== 'unpaid') return;
            document.getElementById('payment-invoice-id').textContent = 'Invoice #' + this.dataset.invoiceId;
            document.getElementById('payment-idv').value = this.dataset.invoiceId;
            document.getElementById('payment-jenis').value = this.dataset.paymentType || '';
            document.getElementById('payment-owner').value = this.dataset.owner || '';
            paymentJumlah.maskMoney('mask', 1000000);
            paymentDatePicker.setDate(new Date(), true, 'Y-m-d');
            $('#payment-rekening').val('').trigger('change');
            document.getElementById('payment-keterangan').value = '';
            document.getElementById('payment-mark-paid').checked = true;
            setPaymentModalState(true);
        });

        document.getElementById('close-payment-modal').addEventListener('click', function () { setPaymentModalState(false); });
        document.getElementById('cancel-payment-modal').addEventListener('click', function () { setPaymentModalState(false); });
        paymentModal.addEventListener('click', function (event) {
            if (event.target === paymentModal) setPaymentModalState(false);
        });

        document.getElementById('close-invoice-modal').addEventListener('click', function () { setModalState(false); });
        document.getElementById('cancel-invoice-modal').addEventListener('click', function () { setModalState(false); });
        invoiceModal.addEventListener('click', function (event) {
            if (event.target === invoiceModal) setModalState(false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key !== 'Escape') return;
            if (!paymentModal.classList.contains('hidden')) {
                setPaymentModalState(false);
            } else if (!invoiceModal.classList.contains('hidden')) {
                setModalState(false);
            }
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
