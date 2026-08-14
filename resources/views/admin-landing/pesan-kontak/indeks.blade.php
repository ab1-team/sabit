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
            padding: .85rem 1rem;
            white-space: nowrap;
        }
        #lpMsgTable tbody td {
            padding: .85rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
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

        @media (max-width: 640px) {
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
    </div>

    <div class="card my-3">
        <div class="card-body px-3 py-3">
            @if ($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="lp-msg-table-scroll">
                <table id="lpMsgTable" class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="min-width:230px">Pengirim</th>
                            <th style="min-width:280px">Subjek</th>
                            <th style="width:140px;min-width:140px">Status</th>
                            <th style="width:130px;min-width:130px">Tanggal</th>
                            <th class="text-center" style="width:90px;min-width:90px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade modal-fullscreen" id="lpMessageModal" tabindex="-1" aria-labelledby="lpMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
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
        scrollX: true,
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
            { data: 'sender',      name: 'name',    orderable: true,  searchable: true },
            { data: 'subject_col', name: 'subject', orderable: true,  searchable: true },
            { data: 'status_col',  name: 'status',  orderable: true,  searchable: false },
            { data: 'created_at',  name: 'created_at', orderable: true, searchable: false },
            { data: 'action',      name: 'action',  orderable: false, searchable: false, className: 'text-center' },
        ],
        rowCallback: function(row, data) {
            // Highlight baris dengan status 'baru' (default)
            if (data && data.status === 'baru') {
                row.classList.add('lp-msg-new');
            }
        },
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
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                }).done(function() {
                    Swal.fire({ icon: 'success', title: 'Berhasil dihapus', timer: 1200, showConfirmButton: false });
                    table.ajax.reload(null, false);
                }).fail(function() {
                    Swal.fire({ icon: 'error', title: 'Gagal menghapus data.' });
                });
            }
        });
    });
});
</script>
@endsection
