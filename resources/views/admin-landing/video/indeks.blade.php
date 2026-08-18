@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <style>
        #lpVideoTable {
            width: 100% !important;
            table-layout: auto;
        }
        .lp-video-table-scroll {
            overflow-x: auto;
        }
        #lpVideoTable.table.table-nowrap td,
        #lpVideoTable.table.table-nowrap th {
            white-space: normal !important;
            overflow: visible !important;
            text-overflow: clip !important;
        }
        #lpVideoTable thead th {
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
        #lpVideoTable tbody td {
            padding: .85rem 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: top;
        }
        #lpVideoTable tbody tr:last-child td { border-bottom: 0; }
        #lpVideoTable tbody tr { transition: background-color .15s ease; }
        #lpVideoTable tbody tr:hover { background-color: #f8fafc; }

        #lpVideoTable .lp-row-title-cell {
            min-width: 240px;
        }
        #lpVideoTable .lp-row-title-cell .lp-video-title {
            font-size: .92rem;
            line-height: 1.35;
            word-break: break-word;
        }

        .lp-video-thumb {
            position: relative;
            width: 120px;
            aspect-ratio: 16 / 9;
            border-radius: .5rem;
            overflow: hidden;
            background: #0f172a;
            box-shadow: 0 1px 2px rgba(15,23,42,.25);
        }
        .lp-video-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .lp-video-thumb-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,.6);
        }
        .lp-video-thumb-empty .material-symbols-rounded {
            font-size: 32px;
        }
        .lp-video-play {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 36px !important;
            background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,.35) 100%);
            pointer-events: none;
        }

        .lp-video-url {
            font-size: .78rem;
            color: #475569;
            word-break: break-all;
        }
        .lp-video-url:hover {
            color: #1d4ed8;
        }

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

        .lp-status-badge.is-local {
            background: #ecfeff;
            color: #0e7490;
            border: 1px solid #a5f3fc;
        }
        .lp-status-badge.is-yt {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }
        .lp-status-badge {
            display: inline-flex;
            align-items: center;
            gap: .25rem;
            padding: .25rem .55rem;
            border-radius: 999px;
            font-size: .72rem;
            font-weight: 600;
            white-space: nowrap;
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $addBtn = '<a href="'.e(route('app.admin-landing.videos.create')).'" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">'
            .'<span class="material-symbols-rounded align-middle" style="font-size:16px;">add</span>'
            .'<span class="align-middle">Tambah Video</span></a>';
    @endphp
    <div class="d-flex justify-content-end mb-3">
        {!! $addBtn !!}
    </div>

    <div class="card my-3">
        <div class="card-body px-3 py-3">
            @if ($errors->any())
                <div class="alert alert-danger py-2 small mb-3">
                    <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div class="lp-video-table-scroll">
                <table id="lpVideoTable" class="table table-striped table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th style="width:140px;min-width:140px">Preview</th>
                            <th style="min-width:240px">Judul</th>
                            <th style="width:120px;min-width:120px">Sumber</th>
                            <th style="min-width:260px">URL / File</th>
                            <th style="width:140px;min-width:140px">Status</th>
                            <th class="text-center" style="width:100px;min-width:100px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {
        $('#lpVideoTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('app.admin-landing.videos.data') }}',
            paging: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            searching: true,
            info: true,
            autoWidth: false,
            scrollX: true,
            responsive: false,
            order: [[0, 'desc']],
            language: {
                lengthMenu: 'Tampilkan _MENU_ data',
                search: 'Cari:',
                info: 'Menampilkan _START_–_END_ dari _TOTAL_ data',
                infoEmpty: 'Tidak ada video.',
                infoFiltered: '(difilter dari _MAX_ total)',
                zeroRecords: 'Tidak ada video yang cocok.',
                emptyTable: 'Belum ada video.',
                loadingRecords: 'Memuat…',
                processing: 'Memproses…',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            },
            columns: [
                { data: 'preview_col', name: 'id', orderable: true, searchable: false },
                { data: 'title_col', name: 'title', orderable: true, searchable: true, className: 'lp-row-title-cell' },
                { data: 'source_col', name: 'source', orderable: true, searchable: true },
                { data: 'url_col', name: 'youtube_url', orderable: false, searchable: true },
                { data: 'status_col', name: 'is_published', orderable: true, searchable: true },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center' },
            ],
        });

        $(document).on('submit', 'form[data-confirm]', function(e) {
            var $form = $(this);
            if ($form.data('confirm-bound')) return;
            $form.data('confirm-bound', true);
            e.preventDefault();
            var msg = $form.attr('data-confirm') || 'Yakin ingin menghapus data ini?';
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
                        $('#lpVideoTable').DataTable().ajax.reload(null, false);
                    }).fail(function() {
                        Swal.fire({ icon: 'error', title: 'Gagal menghapus data.' });
                    });
                }
            });
        });
    });
</script>
@include('admin-landing._skrip')
@endsection
