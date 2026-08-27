@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <style>
        #lpMsgTable {
            width: 100% !important;
            table-layout: auto;
        }
        .lp-msg-table-scroll {
            overflow-x: auto;
        }
        /* Pastikan sel-sel konten panjang tidak ter-truncate oleh
           aturan table-nowrap global dari material-dashboard. */
        #lpMsgTable.table.table-nowrap td,
        #lpMsgTable.table.table-nowrap th {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: clip !important;
        }
        #lpMsgTable thead th {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
            font-weight: 600;
            font-size: .72rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: .65rem .6rem;
            white-space: nowrap;
        }
        #lpMsgTable tbody td {
            padding: .6rem .6rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: .85rem;
        }
        /* Pengirim & subjek: ellipsis agar tidak memanjang */
        #lpMsgTable td.lp-cell-truncate {
            max-width: 240px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        #lpMsgTable tbody tr:last-child td { border-bottom: 0; }
        #lpMsgTable tbody tr { transition: background-color .15s ease; }
        #lpMsgTable tbody tr:hover { background-color: #f8fafc; }

        /* Sel pengirim & subject – bold jika status masih 'baru' */
        #lpMsgTable .lp-msg-new {
            background-color: rgba(37,99,235,.03);
        }

        /* Tombol toggle status – loading state */
        .lp-toggle-status:disabled,
        .lp-toggle-status.is-loading {
            opacity: .65;
            cursor: not-allowed;
        }

        /* Style DataTables wrapper – konsisten dengan halaman admin-landing lain */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            color: #475569;
            font-size: .8125rem;
        }
        .dataTables_wrapper .dataTables_length label,
        .dataTables_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            font-weight: 500;
        }
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #d4d8dd !important;
            border-radius: .5rem !important;
            padding: .4rem .65rem !important;
            font-size: .875rem !important;
            color: #1f2937;
            background: #fff;
            box-shadow: none !important;
        }
        .dataTables_wrapper .dataTables_length select { min-width: 72px; }
        .dataTables_wrapper .dataTables_filter input { min-width: 220px; }
        .dataTables_wrapper .dataTables_length select:focus,
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #1d4ed8 !important;
            outline: none !important;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
        }
        .dataTables_wrapper .dataTables_info { padding-top: 1rem; color: #64748b !important; }
        .dataTables_wrapper .dataTables_paginate { padding-top: 1rem; }
        .dataTables_wrapper .dataTables_paginate .paginate_button {
            box-sizing: border-box;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 .65rem !important;
            margin: 0 2px !important;
            border-radius: .5rem !important;
            border: 1px solid #e2e8f0 !important;
            background: #fff !important;
            color: #475569 !important;
            font-weight: 600;
            font-size: .8125rem;
            cursor: pointer;
            transition: all .15s ease;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled):not(.current) {
            background: #eff6ff !important;
            border-color: #bfdbfe !important;
            color: #1d4ed8 !important;
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
            color: #fff !important;
            box-shadow: 0 1px 2px rgba(29,78,216,.25);
        }
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
            color: #cbd5e1 !important;
            background: #f8fafc !important;
            border-color: #f1f5f9 !important;
            cursor: not-allowed;
        }
        .dataTables_empty {
            padding: 3rem 1rem !important;
            color: #94a3b8 !important;
        }

        /* Checkbox selection – dengan icon check SVG (Material-style) */
        #lpMsgTable input[type="checkbox"].form-check-input,
        #lpSelectAll.form-check-input {
            cursor: pointer;
            background-color: #fff;
            border: 2px solid #94a3b8;
            box-shadow: none !important;
            /* checkmark: SVG centang putih (inline) */
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='3.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 12.5 10 17.5 19 7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: center center;
            background-size: 0% 0%; /* sembunyi saat unchecked */
            transition: background-size .15s ease, border-color .15s ease, background-color .15s ease;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }
        #lpMsgTable input[type="checkbox"].form-check-input:hover,
        #lpSelectAll.form-check-input:hover {
            border-color: #1d4ed8;
        }
        #lpMsgTable input[type="checkbox"].form-check-input:focus,
        #lpSelectAll.form-check-input:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29, 78, 216, .18) !important;
        }
        #lpMsgTable input[type="checkbox"].form-check-input:checked,
        #lpSelectAll.form-check-input:checked {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            background-size: 65% 65%; /* icon check muncul */
        }
        #lpMsgTable input[type="checkbox"].form-check-input:indeterminate,
        #lpSelectAll.form-check-input:indeterminate {
            background-color: #1d4ed8;
            border-color: #1d4ed8;
            /* indeterminate: garis horizontal putih (dash) */
            background-image: url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23ffffff' stroke-width='4' stroke-linecap='round'%3E%3Cpath d='M6 12 H18'/%3E%3C/svg%3E");
            background-size: 70% 70%;
        }
        #lpMsgTable input[type="checkbox"].form-check-input {
            width: 1.1rem;
            height: 1.1rem;
        }
        #lpSelectAll.form-check-input {
            width: 1.15rem;
            height: 1.15rem;
        }

        /* Counter badge di dalam tombol Hapus Terpilih */
        #lpBulkDeleteBtn #lpSelectedCount {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 1.1rem;
            height: 1.1rem;
            padding: 0 .35rem;
            font-size: .72rem;
            font-weight: 700;
            line-height: 1;
            vertical-align: middle;
        }

        /* === Mobile (≤768px): ubah tabel jadi compact card list === */
        @media (max-width: 768px) {
            .lp-msg-table-scroll { overflow-x: visible !important; }
            #lpMsgTable {
                table-layout: auto !important;
                width: 100% !important;
            }
            #lpMsgTable thead { display: none !important; }

            /* Baris = kartu ringkas, 1 baris horizontal:
               [email (flex)] [view] [toggle] [delete] */
            #lpMsgTable tbody tr {
                display: flex !important;
                flex-wrap: nowrap;
                align-items: center;
                background: #fff;
                border: 1px solid #e2e8f0;
                border-radius: .65rem;
                padding: .5rem .6rem;
                margin: 0 0 .45rem 0;
                box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
                transition: border-color .15s ease, box-shadow .15s ease;
                gap: .5rem;
            }
            #lpMsgTable tbody tr:hover {
                border-color: #cbd5e1;
                box-shadow: 0 2px 6px rgba(15, 23, 42, .06);
            }

            #lpMsgTable tbody td {
                border: 0 !important;
                padding: 0 !important;
                display: block !important;
                float: none !important;
            }
            #lpMsgTable tbody td::before { content: none !important; }

            /* Sembunyikan di HP: checkbox, subjek, status, tanggal */
            #lpMsgTable td.lp-col-check { display: none !important; }
            #lpMsgTable td.lp-col-subject { display: none !important; }
            #lpMsgTable td.lp-col-status { display: none !important; }
            #lpMsgTable td.lp-col-date { display: none !important; }
            #lpBulkDeleteBtn { display: none !important; }

            /* Email: ambil sisa ruang, dipotong dengan ellipsis */
            #lpMsgTable td.lp-col-sender {
                flex: 1 1 auto;
                min-width: 0;
                line-height: 1.2;
                align-self: center;
            }
            #lpMsgTable td.lp-col-sender > div {
                width: 100% !important;
                max-width: 100% !important;
                white-space: nowrap !important;
                overflow: hidden !important;
                text-overflow: ellipsis !important;
            }
            #lpMsgTable td.lp-col-sender > div:nth-child(1) {
                display: none !important; /* nama */
            }
            #lpMsgTable td.lp-col-sender > div:nth-child(2) {
                font-size: .85rem;
                font-weight: 500;
                color: #334155 !important;
                line-height: 1.2;
            }

            /* Aksi: tombol compact di pojok kanan */
            #lpMsgTable td.lp-col-action {
                flex: 0 0 auto;
                margin-left: 0;
                align-self: center;
            }
            .lp-table-actions {
                display: inline-flex !important;
                flex-wrap: nowrap;
                align-items: center;
                justify-content: flex-end;
                gap: .3rem !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .lp-table-actions form { display: inline-flex !important; margin: 0 !important; }
            .lp-table-actions .btn.btn-icon {
                width: 1.85rem;
                height: 1.85rem;
                min-width: 1.85rem;
                padding: 0;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: .45rem;
            }
            .lp-table-actions .btn.btn-icon .material-symbols-rounded {
                font-size: .95rem;
                line-height: 1;
            }

            /* Toolbar / wrapper DataTables */
            .dataTables_wrapper .dataTables_filter { float: none; text-align: left; }
            .dataTables_wrapper .dataTables_length { float: none; text-align: left; }
            .dataTables_wrapper .dataTables_filter input {
                width: 100%;
                min-width: 0;
                margin-left: 0 !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                min-width: 2rem;
                height: 2rem;
                font-size: .75rem;
                padding: 0 .5rem !important;
                margin: 0 1px !important;
            }
        }

        /* HP kecil (≤576px): padding lebih ketat, toolbar stack vertikal */
        @media (max-width: 576px) {
            .px-2.py-2 { padding-left: .65rem !important; padding-right: .65rem !important; }
            .card.my-3 { margin-left: -.25rem; margin-right: -.25rem; }
            .card-body.px-3.py-3 { padding: .75rem !important; }

            /* Toolbar: stack ke bawah */
            .d-flex.justify-content-between.align-items-center.mb-3 {
                flex-direction: column;
                align-items: stretch !important;
                gap: .65rem !important;
            }
            .d-flex.justify-content-between.align-items-center.mb-3 > div:first-child {
                text-align: center;
            }
            .d-flex.align-items-center.gap-2 {
                justify-content: center;
            }

            /* Kartu lebih ringkas */
            #lpMsgTable tbody tr {
                padding: .45rem .55rem;
                margin-bottom: .4rem;
                gap: .4rem;
            }
            #lpMsgTable td.lp-col-sender > div:nth-child(2) { font-size: .82rem; }

            /* Tombol aksi kompak (≥1.75rem) */
            .lp-table-actions .btn.btn-icon {
                width: 1.75rem;
                height: 1.75rem;
                min-width: 1.75rem;
            }
            .lp-table-actions .btn.btn-icon .material-symbols-rounded { font-size: .9rem; }
            .lp-table-actions { gap: .2rem !important; }

            /* DataTables filter & length: center */
            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter,
            .dataTables_wrapper .dataTables_info {
                text-align: center !important;
            }
            .dataTables_wrapper .dataTables_filter input { margin-top: .35rem; }
            .dataTables_wrapper .dataTables_paginate { text-align: center !important; }
        }

        /* HP sangat kecil (≤380px): kompres lebih lanjut */
        @media (max-width: 380px) {
            #lpMsgTable tbody tr { padding: .35rem .45rem; gap: .3rem; margin-bottom: .3rem; }
            #lpMsgTable td.lp-col-sender > div:nth-child(2) { font-size: .75rem; }
            .lp-table-actions .btn.btn-icon {
                width: 1.6rem;
                height: 1.6rem;
                min-width: 1.6rem;
                border-radius: .4rem;
            }
            .lp-table-actions .btn.btn-icon .material-symbols-rounded { font-size: .82rem; }
            .lp-table-actions { gap: .15rem !important; }
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div class="text-muted small">
            Pesan dari formulir kontak di halaman landing publik
            <span class="text-muted">/kontak</span>.
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" id="lpBulkDeleteBtn" class="btn btn-sm btn-outline-danger d-none" disabled>
                <span class="material-symbols-rounded">delete_sweep</span>
                <span class="d-none d-sm-inline ms-1">Hapus Terpilih</span>
                <span id="lpSelectedCount" class="badge rounded-pill bg-danger text-white ms-1 d-none">0</span>
            </button>
        </div>
    </div>

    <div class="card my-3">
        <div class="card-body px-3 py-3">
            @if ($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="lp-msg-table-scroll">
                <table id="lpMsgTable" class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:36px;min-width:36px" class="text-center">
                                <input type="checkbox" id="lpSelectAll" class="form-check-input" aria-label="Pilih semua">
                            </th>
                            <th style="min-width:140px">Pengirim</th>
                            <th style="min-width:170px">Subjek</th>
                            <th style="width:110px;min-width:110px">Status</th>
                            <th style="width:110px;min-width:110px">Tanggal</th>
                            <th class="text-center" style="width:80px;min-width:80px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="lpMessageModal" tabindex="-1" aria-labelledby="lpMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="lpMessageModalLabel">Detail Pesan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <dl class="row small mb-0">
                    <dt class="col-sm-3">Pengirim</dt><dd class="col-sm-9" id="lpMsgName">—</dd>
                    <dt class="col-sm-3">Surel</dt><dd class="col-sm-9" id="lpMsgEmail">—</dd>
                    <dt class="col-sm-3">Subjek</dt><dd class="col-sm-9 fw-semibold" id="lpMsgSubject">—</dd>
                    <dt class="col-sm-3">Tanggal</dt><dd class="col-sm-9" id="lpMsgDate">—</dd>
                    <dt class="col-sm-3">Pesan</dt><dd class="col-sm-9" style="white-space:pre-wrap;" id="lpMsgBody">—</dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@include('admin-landing._skrip')

@section('script')
<script>
$(document).ready(function() {
    // ====== Bulk selection state (dideklarasikan SEBELUM DataTables,
    //       supaya rowCallback/drawCallback bisa langsung merujuk) ======
    var selectedIds = new Set();
    var $selectAll   = $('#lpSelectAll');
    var $countBadge  = $('#lpSelectedCount');
    var $bulkBtn     = $('#lpBulkDeleteBtn');

    function syncSelectionUi() {
        var n = selectedIds.size;
        if (n > 0) {
            $countBadge.text(n).removeClass('d-none');
            $bulkBtn.removeClass('d-none').prop('disabled', false);
        } else {
            $countBadge.addClass('d-none');
            $bulkBtn.addClass('d-none').prop('disabled', true);
        }
        // Supaya badge counter proporsional di dalam tombol
        var minWidth = (n > 99) ? '1.75rem' : '1.1rem';
        $countBadge.css('min-width', minWidth);
        var visibleChecks = $('#lpMsgTable tbody input.lp-row-check').toArray()
            .filter(function(cb) { return $(cb).is(':visible'); });
        var checkedVisible = visibleChecks.filter(function(cb) { return cb.checked; }).length;
        if (visibleChecks.length > 0 && checkedVisible === visibleChecks.length) {
            $selectAll.prop('checked', true).prop('indeterminate', false);
        } else if (checkedVisible > 0) {
            $selectAll.prop('checked', false).prop('indeterminate', true);
        } else {
            $selectAll.prop('checked', false).prop('indeterminate', false);
        }
    }

    const table = $('#lpMsgTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('app.admin-landing.contact-messages.data') }}',
        paging: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: true,
        info: true,
        autoWidth: false,
        scrollX: false,
        responsive: false,
        order: [[3, 'desc']],
        language: {
            lengthMenu: 'Tampilkan _MENU_ data',
            search: 'Cari:',
            info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada pesan.',
            infoFiltered: '(difilter dari _MAX total)',
            zeroRecords: 'Tidak ada pesan yang cocok.',
            emptyTable: 'Belum ada pesan masuk.',
            loadingRecords: 'Memuat…',
            processing: 'Memproses…',
            paginate: { first: '«', last: '»', next: '›', previous: '‹' },
        },
        columns: [
            { data: 'checkbox',    name: 'id',      orderable: false, searchable: false, className: 'text-center align-middle lp-col-check',
              createdCell: function(td) { td.setAttribute('data-label', ''); } },
            { data: 'sender',      name: 'name',    orderable: true,  searchable: true,  className: 'lp-col-sender',
              createdCell: function(td) { td.setAttribute('data-label', 'Pengirim'); } },
            { data: 'subject_col', name: 'subject', orderable: true,  searchable: true,  className: 'lp-col-subject',
              createdCell: function(td) { td.setAttribute('data-label', 'Subjek'); } },
            { data: 'status_col',  name: 'status',  orderable: true,  searchable: false, className: 'lp-col-status',
              createdCell: function(td) { td.setAttribute('data-label', 'Status'); } },
            { data: 'created_at',  name: 'created_at', orderable: true, searchable: false, className: 'lp-col-date',
              createdCell: function(td) { td.setAttribute('data-label', 'Tanggal'); } },
            { data: 'action',      name: 'action',  orderable: false, searchable: false, className: 'text-center lp-col-action',
              createdCell: function(td) { td.setAttribute('data-label', 'Aksi'); } },
        ],
        rowCallback: function(row, data) {
            if (data && data.status === 'baru') {
                row.classList.add('lp-msg-new');
            }
            var cb = row.querySelector('.lp-row-check');
            if (cb) {
                cb.checked = selectedIds.has(String(data.id));
                cb.addEventListener('change', function() {
                    if (cb.checked) selectedIds.add(String(data.id));
                    else selectedIds.delete(String(data.id));
                    syncSelectionUi();
                });
            }
        },
        drawCallback: function() {
            syncSelectionUi();
        },
    });

    $selectAll.on('change', function() {
        var checked = this.checked;
        $('#lpMsgTable tbody input.lp-row-check').each(function() {
            this.checked = checked;
            var id = String(this.dataset.id || '');
            if (checked && id) selectedIds.add(id);
            else if (id) selectedIds.delete(id);
        });
        syncSelectionUi();
    });

    $bulkBtn.on('click', function() {
        if (selectedIds.size === 0) return;
        var ids = Array.from(selectedIds);
        Swal.fire({
            title: 'Hapus ' + ids.length + ' pesan?',
            text: 'Pesan yang dipilih akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, hapus semua',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then(function(r) {
            if (!r.isConfirmed) return;
            var fd = new FormData();
            fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
            ids.forEach(function(id) { fd.append('ids[]', id); });

            $bulkBtn.prop('disabled', true);
            fetch('{{ route('app.admin-landing.contact-messages.bulk-destroy') }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: fd,
            }).then(function(resp) {
                var ct = resp.headers.get('content-type') || '';
                if (ct.includes('application/json')) {
                    return resp.json().then(function(d) { return { status: resp.status, data: d }; });
                }
                return { status: resp.status, data: null };
            }).then(function(out) {
                if (out.data && out.data.success) {
                    selectedIds.clear();
                    Swal.fire({
                        icon: 'success',
                        title: out.data.msg || 'Pesan dihapus',
                        toast: true,
                        position: 'top-end',
                        timer: 1400,
                        timerProgressBar: true,
                        showConfirmButton: false,
                    });
                    table.ajax.reload(null, false);
                } else {
                    Swal.fire({ icon: 'error', title: (out.data && out.data.msg) || 'Gagal menghapus' });
                    $bulkBtn.prop('disabled', false);
                }
            }).catch(function() {
                Swal.fire({ icon: 'error', title: 'Cek koneksi Anda.' });
                $bulkBtn.prop('disabled', false);
            });
        });
    });

    // Reset pilihan saat tabel di-reload (mis. setelah toggle status / delete per baris)
    table.on('draw', function() {
        // Sinkronkan ulang checkbox dengan selectedIds yang masih ada
        $('#lpMsgTable tbody input.lp-row-check').each(function() {
            var id = String(this.dataset.id || '');
            this.checked = selectedIds.has(id);
        });
        syncSelectionUi();
    });

    // Tombol toggle status: maju ke status berikutnya via fetch
    $('#lpMsgTable').on('click', '.lp-toggle-status', function() {
        const btn = this;
        const id  = btn.dataset.id;
        const currentStatus = btn.dataset.current;
        const nextStatus    = btn.dataset.next;
        const labelNext     = btn.dataset.labelNext;
        if (!id || !nextStatus) return;
        btn.classList.add('is-loading');
        btn.disabled = true;

        const url = '{{ url('/app/admin-landing/contact-messages') }}/' + id + '/status';
        const fd = new FormData();
        fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        fd.append('status', nextStatus);

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: fd,
        }).then(function(r) {
            return r.json().then(function(d) { return { status: r.status, data: d }; });
        }).then(function(out) {
            if (out.status === 422) {
                Swal.fire({ icon: 'error', title: 'Status tidak valid', timer: 1800, showConfirmButton: false });
                table.ajax.reload(null, false);
                return;
            }
            if (out.data && out.data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Status diubah ke "' + labelNext + '"',
                    toast: true,
                    position: 'top-end',
                    timer: 1400,
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
                table.ajax.reload(null, false);
            } else {
                Swal.fire({ icon: 'error', title: (out.data && out.data.msg) || 'Gagal memperbarui status' });
                btn.disabled = false;
                btn.classList.remove('is-loading');
            }
        }).catch(function() {
            Swal.fire({ icon: 'error', title: 'Cek koneksi Anda.' });
            btn.disabled = false;
            btn.classList.remove('is-loading');
        });
    });

    // Modal detail pesan
    const modal = document.getElementById('lpMessageModal');
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const btn = event.relatedTarget;
            document.getElementById('lpMsgName').textContent    = btn.dataset.name || '—';
            document.getElementById('lpMsgEmail').textContent   = btn.dataset.email || '—';
            document.getElementById('lpMsgSubject').textContent = btn.dataset.subject || '(tanpa subjek)';
            document.getElementById('lpMsgDate').textContent    = btn.dataset.date || '—';
            document.getElementById('lpMsgBody').textContent    = btn.dataset.message || '—';
        });
    }

    // Konfirmasi hapus untuk form yang di-render oleh DataTables (di luar DOM awal)
    $(document).on('submit', 'form[data-confirm]', function(e) {
        const $form = $(this);
        if ($form.data('confirm-bound')) return;
        $form.data('confirm-bound', true);
        e.preventDefault();
        const msg = $form.attr('data-confirm') || 'Yakin ingin menghapus data ini?';
        Swal.fire({
            title: 'Hapus data?',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, hapus',
            cancelButtonText: 'Batal',
            reverseButtons: true,
        }).then(function(r) {
            if (r.isConfirmed) {
                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                }).done(function(resp) {
                    var msg = (resp && resp.msg) || 'Berhasil dihapus';
                    var ok = !resp || resp.success !== false;
                    if (ok) {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg, timer: 1800, timerProgressBar: true, showConfirmButton: false });
                    } else {
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, timer: 3500, timerProgressBar: true, showConfirmButton: false });
                    }
                    table.ajax.reload(null, false);
                }).fail(function(xhr) {
                    var msg = 'Gagal menghapus data.';
                    if (xhr && xhr.responseJSON && xhr.responseJSON.msg) msg = xhr.responseJSON.msg;
                    Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, timer: 3500, timerProgressBar: true, showConfirmButton: false });
                });
            }
        });
    });
});
</script>

<style>
    /* Pastikan modal pesan-kontak tampil solid & di atas sidenav (z-index 9999).
       Stacking context .main-content (position:relative + z-index:1) bisa
       membatasi modal position:fixed di dalamnya. Pindahkan via JS ke <body>. */
    #lpMessageModal {
        z-index: 12050 !important;
    }
    #lpMessageModal + .modal-backdrop,
    body > .modal-backdrop.show {
        z-index: 12049 !important;
    }
    #lpMessageModal .modal-content {
        background-color: #ffffff !important;
        opacity: 1 !important;
        box-shadow: 0 24px 64px -12px rgba(15, 23, 42, .35) !important;
    }
    #lpMessageModal .modal-header,
    #lpMessageModal .modal-footer,
    #lpMessageModal .modal-body {
        background-color: #ffffff !important;
    }
</style>

<script>
    // Pindahkan modal ke <body> agar tidak terjebak stacking context
    // .main-content yang membatasi position:fixed + z-index.
    (function() {
        var m = document.getElementById('lpMessageModal');
        if (m && m.parentNode !== document.body) {
            document.body.appendChild(m);
        }
    })();
</script>
@endsection
