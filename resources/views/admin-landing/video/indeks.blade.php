@extends('layouts.tenant.base')

@section('title', 'Video')

@section('style')
    @include('admin-landing._gaya')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">
    <style>
        /* ==========================================================
           Halaman Video Admin Landing
           ========================================================== */

        .lp-vid-page { padding: 1rem 1rem 1.25rem; }

        .lp-vid-head {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem 1rem;
            align-items: center;
            justify-content: space-between;
            padding: .9rem 1rem;
            background: linear-gradient(135deg, #fff 0%, #fef2f2 100%);
            border: 1px solid #e2e8f0;
            border-radius: .75rem .75rem 0 0;
        }
        .lp-vid-head-title {
            display: flex;
            align-items: center;
            gap: .6rem;
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }
        .lp-vid-head-title .material-symbols-rounded {
            color: #ef4444;
            font-size: 24px;
        }
        .lp-vid-head-sub {
            font-size: .78rem;
            color: #64748b;
            margin: .15rem 0 0;
        }
        .lp-vid-head-cta {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem 1rem;
            font-size: .875rem;
            font-weight: 600;
            border-radius: .55rem;
        }
        .lp-vid-head-cta .material-symbols-rounded { font-size: 18px; }

        .lp-vid-stat-row {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: .65rem;
            margin: .65rem 0 .9rem;
        }
        @media (max-width: 720px) {
            .lp-vid-stat-row { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        .lp-vid-stat {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .65rem .85rem;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: .55rem;
            transition: border-color .15s ease, transform .15s ease;
        }
        .lp-vid-stat:hover {
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .lp-vid-stat-icon {
            width: 34px;
            height: 34px;
            border-radius: .45rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
        }
        .lp-vid-stat-icon .material-symbols-rounded { font-size: 18px; }
        .lp-vid-stat-body { display: flex; flex-direction: column; line-height: 1.15; }
        .lp-vid-stat-label { font-size: .7rem; color: #64748b; text-transform: uppercase; letter-spacing: .04em; }
        .lp-vid-stat-value { font-size: 1.05rem; font-weight: 700; color: #0f172a; }

        .lp-vid-card {
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            background: #fff;
        }

        /* Tabel */
        #lpVideoTable {
            width: 100% !important;
            table-layout: auto;
            margin: 0;
        }
        .lp-video-table-scroll { overflow-x: auto; }
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
            vertical-align: middle;
        }
        #lpVideoTable tbody tr:last-child td { border-bottom: 0; }
        #lpVideoTable tbody tr { transition: background-color .15s ease; }
        #lpVideoTable tbody tr:hover { background-color: #f8fafc; }

        #lpVideoTable .lp-row-title-cell { min-width: 240px; }
        #lpVideoTable .lp-row-title-cell .lp-video-title {
            font-size: .92rem;
            font-weight: 600;
            line-height: 1.35;
            word-break: break-word;
            color: #0f172a;
        }
        #lpVideoTable .lp-row-title-cell small {
            display: block;
            margin-top: .2rem;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.4;
        }

        .lp-video-thumb {
            position: relative;
            width: 132px;
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
        .lp-video-thumb-empty .material-symbols-rounded { font-size: 32px; }
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

        /* Tombol trigger play (thumbnail jadi tombol) */
        .lp-video-thumb-btn {
            display: inline-block;
            padding: 0;
            margin: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            line-height: 0;
            border-radius: .5rem;
            transition: transform .15s ease, box-shadow .15s ease;
            position: relative;
        }
        .lp-video-thumb-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(15,23,42,.25);
        }
        .lp-video-thumb-btn:focus-visible {
            outline: 3px solid #ef4444;
            outline-offset: 2px;
        }
        .lp-video-thumb-btn .lp-video-play {
            opacity: .85;
            transition: opacity .15s ease, transform .15s ease;
        }
        .lp-video-thumb-btn:hover .lp-video-play {
            opacity: 1;
            transform: scale(1.08);
        }
        .lp-video-thumb-empty-wrap .lp-video-thumb {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modal pemutar video */
        .lp-vid-modal-content {
            background: #0b1220;
            color: #f8fafc;
            border: 1px solid rgba(255,255,255,.08);
        }
        .lp-vid-modal-header {
            border-bottom: 1px solid rgba(255,255,255,.08);
            padding: .7rem 1rem;
        }
        .lp-vid-modal-header .modal-title {
            font-size: 1rem;
            font-weight: 600;
            color: #f8fafc;
        }
        .lp-vid-modal-body { background: #000; }
        .lp-vid-modal-footer {
            border-top: 1px solid rgba(255,255,255,.08);
            padding: .65rem 1rem;
            background: rgba(0,0,0,.4);
        }

        .lp-video-url {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            font-size: .78rem;
            color: #475569;
            word-break: break-all;
            max-width: 100%;
        }
        .lp-video-url:hover { color: #1d4ed8; }

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
        .lp-status-badge.is-published {
            background: rgba(55,209,124,.12);
            color: #1f9d57;
        }
        .lp-status-badge.is-draft {
            background: rgba(100,116,139,.15);
            color: #475569;
        }

        /* DataTables wrapper */
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

        /* Empty state khusus DataTables */
        .lp-vid-empty {
            padding: 2.5rem 1.5rem;
            text-align: center;
            color: #94a3b8;
        }
        .lp-vid-empty .material-symbols-rounded {
            font-size: 48px;
            color: #cbd5e1;
            display: block;
            margin-bottom: .5rem;
        }
        .lp-vid-empty-title { font-size: .95rem; font-weight: 600; color: #475569; }
        .lp-vid-empty-sub { font-size: .82rem; color: #94a3b8; margin-top: .15rem; }

        @media (max-width: 720px) {
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
<div class="lp-vid-page">

    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    {{-- Header card --}}
    <div class="lp-vid-head">
        <div>
            <h1 class="lp-vid-head-title">
                <span class="material-symbols-rounded">play_circle</span>
                <span>Video</span>
            </h1>
            <p class="lp-vid-head-sub">Kelola video YouTube dan upload lokal untuk halaman /video publik.</p>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center">
            <a href="{{ route('app.admin-landing.galleries') }}" class="btn btn-outline-secondary lp-vid-head-back" title="Kembali ke Galeri">
                <span class="material-symbols-rounded align-middle" style="font-size:18px;">arrow_back</span>
                <span class="align-middle">Kembali ke Galeri</span>
            </a>
            <a href="{{ route('app.admin-landing.videos.create') }}" class="btn btn-primary lp-vid-head-cta">
                <span class="material-symbols-rounded">add</span>
                <span>Tambah Video</span>
            </a>
        </div>
    </div>

    {{-- Stat strip --}}
    @php
        $totalVideo = (int) (App\Models\Landing\VideoLanding::count());
        $totalYt    = (int) (App\Models\Landing\VideoLanding::where('source', 'youtube')->count());
        $totalLocal = (int) (App\Models\Landing\VideoLanding::where('source', 'local')->count());
        $totalPub   = (int) (App\Models\Landing\VideoLanding::where('is_published', true)->count());
    @endphp
    <div class="lp-vid-stat-row" id="lpVideoStatRow">
        <div class="lp-vid-stat">
            <span class="lp-vid-stat-icon" style="background:#fef2f2;color:#b91c1c;">
                <span class="material-symbols-rounded">smart_display</span>
            </span>
            <div class="lp-vid-stat-body">
                <span class="lp-vid-stat-label">Total</span>
                <span class="lp-vid-stat-value">{{ $totalVideo }}</span>
            </div>
        </div>
        <div class="lp-vid-stat">
            <span class="lp-vid-stat-icon" style="background:#fef2f2;color:#dc2626;">
                <span class="material-symbols-rounded">play_circle</span>
            </span>
            <div class="lp-vid-stat-body">
                <span class="lp-vid-stat-label">YouTube</span>
                <span class="lp-vid-stat-value">{{ $totalYt }}</span>
            </div>
        </div>
        <div class="lp-vid-stat">
            <span class="lp-vid-stat-icon" style="background:#ecfeff;color:#0e7490;">
                <span class="material-symbols-rounded">movie</span>
            </span>
            <div class="lp-vid-stat-body">
                <span class="lp-vid-stat-label">Lokal</span>
                <span class="lp-vid-stat-value">{{ $totalLocal }}</span>
            </div>
        </div>
        <div class="lp-vid-stat">
            <span class="lp-vid-stat-icon" style="background:rgba(55,209,124,.12);color:#1f9d57;">
                <span class="material-symbols-rounded">check_circle</span>
            </span>
            <div class="lp-vid-stat-body">
                <span class="lp-vid-stat-label">Dipublikasikan</span>
                <span class="lp-vid-stat-value">{{ $totalPub }}</span>
            </div>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="lp-vid-card">
        @if ($errors->any())
            <div class="alert alert-danger py-2 small m-3 mb-0">
                <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif
        <div class="lp-video-table-scroll px-3 pb-3 pt-2">
            <table id="lpVideoTable" class="table table-striped table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:152px;min-width:152px">Preview</th>
                        <th style="min-width:240px">Judul</th>
                        <th style="width:120px;min-width:120px">Sumber</th>
                        <th style="min-width:260px">URL / File</th>
                        <th style="width:140px;min-width:140px">Status</th>
                        <th class="text-center" style="width:110px;min-width:110px">Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<script id="lpVideoEmptyTpl" type="text/x-handlebars-template">
    <div class="lp-vid-empty">
        <span class="material-symbols-rounded">videocam_off</span>
        <div class="lp-vid-empty-title">Belum ada video</div>
        <div class="lp-vid-empty-sub">Tambah video YouTube atau upload video lokal untuk ditampilkan di halaman publik.</div>
    </div>
</script>
@endsection

@section('modal')
{{-- Modal pemutar video (dipakai oleh thumbnail di tabel).
     Ditempatkan di @section('modal') (di luar .main-content) agar tidak
     ikut ter-blur oleh efek dimmed layout saat modal show. --}}
<div class="modal fade" id="lpVideoPlayModal" tabindex="-1" aria-hidden="true" aria-labelledby="lpVideoPlayTitle">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content lp-vid-modal-content">
            <div class="modal-header lp-vid-modal-header">
                <h5 class="modal-title" id="lpVideoPlayTitle">Video</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0 lp-vid-modal-body">
                <div id="lpVideoPlayPlayer" class="ratio ratio-16x9"></div>
            </div>
            <div class="modal-footer lp-vid-modal-footer justify-content-between align-items-start" id="lpVideoPlayFooter" style="display:none;">
                <div id="lpVideoPlayDesc" class="text-muted small"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    @if (session('success'))
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: @json(session('success')),
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                });
            }
        });
    @endif
    @if (session('error'))
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: @json(session('error')),
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true,
                });
            }
        });
    @endif
    $(document).ready(function() {
        var $tbl = $('#lpVideoTable');
        var emptyTpl = ($('#lpVideoEmptyTpl').html() || '').trim();

        var dt = $tbl.DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route('app.admin-landing.videos.data') }}',
                dataSrc: function (json) {
                    // Reload stat strip setelah data baru diterima.
                    if (json && json.extra && typeof json.extra.stats !== 'undefined') {
                        var s = json.extra.stats;
                        var $row = $('#lpVideoStatRow');
                        if ($row.length && s) {
                            $row.find('.lp-vid-stat').eq(0).find('.lp-vid-stat-value').text(s.total);
                            $row.find('.lp-vid-stat').eq(1).find('.lp-vid-stat-value').text(s.youtube);
                            $row.find('.lp-vid-stat').eq(2).find('.lp-vid-stat-value').text(s.local);
                            $row.find('.lp-vid-stat').eq(3).find('.lp-vid-stat-value').text(s.published);
                        }
                    }
                    return json.data;
                },
            },
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
                emptyTable: emptyTpl || 'Belum ada video.',
                loadingRecords: 'Memuat…',
                processing: 'Memproses…',
                paginate: { first: '«', last: '»', next: '›', previous: '‹' },
            },
            columns: [
                { data: 'preview_col', name: 'id',         orderable: true,  searchable: false },
                { data: 'title_col',   name: 'title',      orderable: true,  searchable: true,  className: 'lp-row-title-cell' },
                { data: 'source_col',  name: 'source',     orderable: true,  searchable: true },
                { data: 'url_col',     name: 'youtube_url', orderable: false, searchable: true },
                { data: 'status_col',  name: 'is_published', orderable: true, searchable: true },
                { data: 'action',      name: 'action',     orderable: false, searchable: false, className: 'text-center' },
            ],
        });

        // Konfirmasi hapus (form di-render oleh DataTables, di luar DOM awal)
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
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    }).done(function(resp) {
                        var msg = (resp && resp.msg) || 'Berhasil dihapus';
                        var ok = !resp || resp.success !== false;
                        if (ok) {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: msg, timer: 1800, timerProgressBar: true, showConfirmButton: false });
                        } else {
                            Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, timer: 3500, timerProgressBar: true, showConfirmButton: false });
                        }
                        dt.ajax.reload(null, false);
                    }).fail(function(xhr) {
                        var msg = 'Gagal menghapus data.';
                        if (xhr && xhr.responseJSON && xhr.responseJSON.msg) msg = xhr.responseJSON.msg;
                        Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: msg, timer: 3500, timerProgressBar: true, showConfirmButton: false });
                    });
                }
            });
        });

        // ============== Modal pemutar video ==============
        var $modal   = $('#lpVideoPlayModal');
        var $player  = $('#lpVideoPlayPlayer');
        var $title   = $('#lpVideoPlayTitle');
        var $desc    = $('#lpVideoPlayDesc');
        var $footer  = $('#lpVideoPlayFooter');
        var bsModal  = null;
        if ($modal.length && window.bootstrap && bootstrap.Modal) {
            bsModal = new bootstrap.Modal($modal[0]);
        }

        function resetPlayer() {
            $player.empty();
        }

        function openVideoFromTrigger(trigger) {
            var ytId   = $(trigger).attr('data-yt-id') || '';
            var local  = $(trigger).attr('data-local-src') || '';
            var poster = $(trigger).attr('data-poster') || '';
            var title  = $(trigger).attr('data-title') || 'Video';
            var desc   = $(trigger).attr('data-description') || '';

            $title.text(title);
            if (desc) {
                $desc.text(desc);
                $footer.show();
            } else {
                $desc.text('');
                $footer.hide();
            }

            resetPlayer();

            if (ytId) {
                var src = 'https://www.youtube.com/embed/' + encodeURIComponent(ytId) + '?autoplay=1&rel=0';
                var $iframe = $('<iframe>', {
                    src: src,
                    title: title,
                    allow: 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share',
                }).attr('allowfullscreen', '').css({ border: 0, position: 'absolute', inset: 0, width: '100%', height: '100%' });
                $player.append($iframe);
            } else if (local) {
                var $vid = $('<video>', { src: local, controls: true, autoplay: true })
                    .css({ position: 'absolute', inset: 0, width: '100%', height: '100%', objectFit: 'contain', background: '#000' });
                if (poster) $vid.attr('poster', poster);
                $player.append($vid);
            } else {
                $player.html('<div class="d-flex align-items-center justify-content-center h-100 text-muted">'
                    + '<div class="text-center"><span class="material-symbols-rounded" style="font-size:48px;">videocam_off</span>'
                    + '<div class="small mt-2">Video tidak tersedia.</div></div></div>');
            }

            if (bsModal) bsModal.show();
        }

        // Pakai event delegation supaya tetap menangkap trigger yang baru
        // di-render oleh DataTables (paginasi / sort / search).
        $(document).on('click', '.lp-video-trigger', function(e) {
            e.preventDefault();
            openVideoFromTrigger(this);
        });

        // Bersihkan player saat modal ditutup.
        if ($modal.length) {
            $modal.on('hidden.bs.modal', function () {
                resetPlayer();
                $title.text('Video');
                $desc.text('');
                $footer.hide();
            });
        }
    });
</script>
@include('admin-landing._skrip')
@endsection
