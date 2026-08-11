<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi — {{ env('APP_NAME') }}</title>
    <link rel="icon" type="image/png" href="{{ \App\Models\Profil::logoUrl() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html, body { font-family: 'Inter', system-ui, sans-serif; }
        body { -webkit-tap-highlight-color: transparent; background: #f8fafc; }
        .modal-scroll { max-height: calc(100vh - 2rem); overflow-y: auto; }
        .select2-container { width: 100% !important; }
        .select2-container--bootstrap-5 .select2-selection { min-height: 44px; border-color: #cbd5e1; border-radius: .75rem; padding: .375rem .875rem; font-size: .875rem; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered { padding: .25rem 1.5rem .25rem 0; color: #1e293b; line-height: 1.75rem; }
        .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow { top: 9px; right: 10px; }
        .select2-container--bootstrap-5.select2-container--focus .select2-selection,
        .select2-container--bootstrap-5.select2-container--open .select2-selection { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, .15); }
        .select2-container--bootstrap-5 .select2-dropdown { border-color: #cbd5e1; border-radius: .75rem; overflow: hidden; }
        .table-wrap { overflow-x: auto; }
        table.dataTable { border-collapse: collapse !important; }
        table.dataTable tbody tr { transition: background-color .15s ease; }
        table.dataTable tbody tr:hover { background-color: #f8fafc; }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input { border: 1px solid #cbd5e1 !important; border-radius: .5rem !important; padding: .375rem .625rem !important; font-size: .875rem !important; color: #1e293b; }
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus { border-color: #6366f1 !important; outline: none !important; box-shadow: 0 0 0 3px rgba(99, 102, 241, .15) !important; }
        .dataTables_wrapper .dataTables_info { color: #64748b !important; font-size: .8125rem !important; }
        .paginate_button { color: #6366f1 !important; }
        .paginate_button.current { background: #6366f1 !important; color: #fff !important; border-color: #6366f1 !important; }
        .paginate_button:hover { background: #eef2ff !important; color: #4f46e5 !important; }
        .modal-scroll .select2-container--open { z-index: 70; }
        .modal-scroll .select2-container--bootstrap-5 .select2-dropdown { z-index: 70; }
        @media (max-width: 640px) {
            .dataTables_wrapper .dataTables_filter { float: none; text-align: left; }
            .dataTables_wrapper .dataTables_length { float: none; text-align: left; }
            .dataTables_wrapper .dataTables_filter input { width: 100%; min-width: 0; margin-left: 0; }
            .paginate_button { min-width: 2rem !important; height: 2rem !important; font-size: .75rem !important; padding: 0 .5rem !important; }
        }
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
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Transaksi</h2>
                <p class="mt-1 text-sm text-slate-500">Catat pembayaran invoice.</p>
            </div>
        </header>

        @include('tenant.partials.tenant-filter')

        <section class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-bold text-slate-900">Invoice belum lunas</h3>
                    <p class="mt-1 text-xs text-slate-500">Pilih invoice untuk mencatat pembayaran.</p>
                </div>
            </div>
            <div class="table-wrap px-5 py-4">
                <table id="transaksis" class="table align-items-center mb-0 w-full text-sm text-slate-700">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wider text-slate-600">
                        <tr>
                            <th class="px-3 py-3 text-left font-semibold">No</th>
                            <th class="px-3 py-3 text-left font-semibold">Jenis Pembayaran</th>
                            <th class="px-3 py-3 text-left font-semibold">Admin</th>
                            <th class="px-3 py-3 text-left font-semibold">Tanggal Invoice</th>
                            <th class="px-3 py-3 text-right font-semibold">Jumlah</th>
                            <th class="px-3 py-3 text-center font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </section>
    </main>

    <div id="payment-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-slate-900/50 p-2 backdrop-blur-sm sm:p-4" role="dialog" aria-modal="true">
        <div class="modal-scroll w-full max-w-md max-h-[95vh] overflow-y-auto rounded-2xl bg-white shadow-2xl">
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
                <div class="space-y-4">
                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                        <div class="mb-2">
                            <span class="text-xs font-medium uppercase tracking-wide text-slate-400">Jenis Pembayaran</span>
                            <p id="payment-jenis" class="mt-0.5 font-semibold text-slate-800"></p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-xs font-medium uppercase tracking-wide text-slate-400">Admin</span>
                                <p id="payment-owner" class="mt-0.5 font-semibold text-slate-800"></p>
                            </div>
                            <div>
                                <span class="text-xs font-medium uppercase tracking-wide text-slate-400">Tanggal Invoice</span>
                                <p id="payment-tgl-invoice" class="mt-0.5 font-semibold text-slate-800"></p>
                            </div>
                        </div>
                        <div class="mt-2 rounded-lg bg-emerald-50 border border-emerald-200 px-3 py-2">
                            <span class="text-xs font-medium uppercase tracking-wide text-emerald-600">Jumlah</span>
                            <p id="payment-jumlah" class="mt-0.5 text-xl font-bold text-emerald-700"></p>
                        </div>
                    </div>

                    <div>
                        <label for="rekening_debit" class="mb-1.5 block text-sm font-semibold text-slate-700">Rekening <span class="text-rose-500">*</span></label>
                        <select id="rekening_debit" name="rekening_debit" required class="invoice-input select2 bg-white" data-placeholder="Pilih rekening">
                            <option value="">Pilih rekening</option>
                            @foreach ($rekenings as $r)
                                <option value="{{ $r->kd_rekening }}">{{ $r->nama_rekening }}</option>
                            @endforeach
                        </select>
                        @error('rekening_debit')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="keterangan" class="mb-1.5 block text-sm font-semibold text-slate-700">Keterangan</label>
                        <input type="text" id="keterangan" name="keterangan" class="invoice-input" placeholder="Opsional">
                        @error('keterangan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="mt-6 flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                    <button type="button" id="cancel-payment-modal" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 text-sm font-semibold text-white hover:bg-emerald-700 focus:outline-none focus:ring-4 focus:ring-emerald-200">
                        Simpan Pembayaran
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const paymentModal = document.getElementById('payment-modal');
        const paymentForm = document.getElementById('payment-form');

        $('#rekening_debit').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Pilih rekening',
            minimumResultsForSearch: Infinity,
            dropdownParent: $('#payment-modal'),
        });

        const table = $('#transaksis').DataTable({
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
                url: '{{ route('tenant.transaksi.data') }}',
            },
            language: {
                emptyTable: 'Tidak ada invoice belum lunas.',
                info: 'Showing _START_–_END_ of _TOTAL_ entries',
                infoEmpty: 'Showing 0–0 of 0 entries',
                infoFiltered: '(filtered from _MAX_ total)',
                lengthMenu: 'Show _MENU_ entries',
                loadingRecords: 'Loading…',
                processing: 'Processing…',
                search: 'Search:',
                zeroRecords: 'Tidak ada invoice yang cocok.',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            },
            order: [[3, 'desc']],
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false, width: '4%' },
                { data: 'jenis_pembayaran', name: 'admin_invoice.jenis_pembayaran', className: 'ps-3 font-semibold text-slate-800 whitespace-nowrap' },
                { data: 'owner', name: 'owner', className: 'ps-3 text-slate-700' },
                { data: 'tgl_invoice_fmt', name: 'admin_invoice.tgl_invoice', className: 'ps-3 text-slate-600 whitespace-nowrap' },
                { data: 'jumlah_fmt', name: 'admin_invoice.jumlah', className: 'ps-3 text-right text-slate-900 font-semibold tabular-nums whitespace-nowrap' },
                { data: 'action', orderable: false, searchable: false, className: 'text-center whitespace-nowrap' },
            ],
            columnDefs: [{ targets: '_all', orderable: false }],
        });

        function setPaymentModalState(open) {
            paymentModal.classList.toggle('hidden', !open);
            paymentModal.classList.toggle('flex', open);
            document.body.classList.toggle('overflow-hidden', open);
        }

        $('#transaksis').on('click', '.bayar-invoice', function () {
            fetch(this.dataset.url)
                .then(function (r) { return r.json(); })
                .then(function (d) {
                    document.getElementById('payment-invoice-id').textContent = 'Invoice #' + d.id;
                    document.getElementById('payment-idv').value = d.id;
                    document.getElementById('payment-jenis').textContent = d.jenis_pembayaran;
                    document.getElementById('payment-owner').textContent = d.owner;
                    document.getElementById('payment-tgl-invoice').textContent = d.tgl_invoice;
                    document.getElementById('payment-jumlah').textContent = d.jumlah;
                    $('#rekening_debit').val('').trigger('change');
                    document.getElementById('keterangan').value = '';
                    setPaymentModalState(true);
                });
        });

        document.getElementById('close-payment-modal').addEventListener('click', function () { setPaymentModalState(false); });
        document.getElementById('cancel-payment-modal').addEventListener('click', function () { setPaymentModalState(false); });
        paymentModal.addEventListener('click', function (event) {
            if (event.target === paymentModal) setPaymentModalState(false);
        });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !paymentModal.classList.contains('hidden')) {
                setPaymentModalState(false);
            }
        });
    </script>

    @if (session('success'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: @json(session('success')), showConfirmButton: false, timer: 3000, timerProgressBar: true }).then(function () { table.ajax.reload(null, false); });</script>
    @endif
    @if (session('error'))
        <script>Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: @json(session('error')), showConfirmButton: false, timer: 3000, timerProgressBar: true });</script>
    @endif
</body>
</html>
