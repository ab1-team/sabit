@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        /* ===== Layout: Toolbar ===== */
        .lp-gal-toolbar {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .lp-gal-toolbar .lp-gal-search {
            flex: 1 1 220px;
            position: relative;
            max-width: 380px;
        }
        .lp-gal-toolbar .lp-gal-search input {
            width: 100%;
            padding: .55rem .95rem .55rem 2.2rem;
            border: 1px solid #d4d8dd;
            border-radius: .65rem;
            font-size: .875rem;
            background: #fff;
            color: #1f2937;
            transition: border-color .15s, box-shadow .15s;
        }
        .lp-gal-toolbar .lp-gal-search input:focus {
            outline: none;
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12);
        }
        .lp-gal-toolbar .lp-gal-search .lp-search-icon {
            position: absolute;
            left: .75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            pointer-events: none;
        }
        .lp-gal-stats {
            color: #64748b;
            font-size: .8125rem;
            font-weight: 500;
        }

/* ===== Card (override / extend dari artikel) ===== */
        .lp-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.1rem;
        }
        .lp-card {
            background: #fff;
            border: none;
            border-radius: 1rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,.10), 0 2px 4px -1px rgba(0,0,0,.06);
            transition: transform .18s ease, box-shadow .18s ease;
        }
        .lp-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -5px rgba(0,0,0,.12) !important;
        }
        .lp-card-cover { position: relative; aspect-ratio: 16 / 9; background: #f1f5f9; overflow: hidden; }
        .lp-card-img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .lp-card-img--empty { color: #cbd5e1; font-size: 2.25rem; }
        .lp-card-play-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 56px !important;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(0,0,0,0) 40%, rgba(0,0,0,.35) 100%);
            text-shadow: 0 4px 16px rgba(0,0,0,.55);
        }

        .lp-card-body {
            padding: 1rem .75rem .75rem;
            display: flex;
            flex-direction: column;
            gap: .5rem;
            flex: 1 1 auto;
        }
        .lp-card-meta-top { display: flex; flex-wrap: wrap; gap: .35rem; align-items: center; }
        .lp-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
            word-break: break-word;
        }
        .lp-card-excerpt {
            margin: 0;
            color: #475569;
            font-size: .85rem;
            line-height: 1.55;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .lp-card-meta-bottom {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .25rem .5rem;
            color: #64748b;
            font-size: .78rem;
        }
        .lp-card-meta-bottom .material-symbols-rounded { color: #94a3b8; }

        /* Status badge spesifik galeri */
        .lp-status-badge.lp-type-photo {
            background: #eff6ff;
            color: #1d4ed8;
        }
        .lp-status-badge.lp-type-video {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* Card actions (toggle + edit + hapus) — identik dengan artikel */
        .lp-card-actions {
            display: flex;
            flex-wrap: nowrap;
            gap: .35rem;
            align-items: center;
            margin-top: auto;
            padding: .5rem 0 .15rem;
            border-top: 1px dashed #e2e8f0;
        }
        .lp-card-toggles {
            display: flex;
            flex-wrap: nowrap;
            gap: .25rem .55rem;
            align-items: center;
            flex: 1 1 auto;
            min-width: 0;
        }
        .lp-card-action-buttons {
            display: inline-flex;
            flex-wrap: nowrap;
            gap: .35rem;
            align-items: center;
        }
        .lp-card-action-buttons .btn {
            width: 32px;
            height: 32px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            position: relative;
        }
        .lp-card-action-buttons .btn::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
        }
        .lp-card-action-buttons .btn .material-symbols-rounded {
            font-size: 15px;
        }
        .lp-card-action-buttons form { display: inline-flex; }
        .lp-card-actions .btn {
            padding: .3rem .65rem;
            font-size: .8rem;
            margin-bottom: 0;
        }

        /* iOS-style switch toggle (kecil) — identik dengan artikel */
        .lp-switch {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            cursor: pointer;
            user-select: none;
            font-size: .7rem;
            color: #475569;
            line-height: 1;
            white-space: nowrap;
        }
        .lp-switch-input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
            width: 0;
            height: 0;
        }
        .lp-switch-track {
            position: relative;
            display: inline-block;
            width: 26px;
            height: 14px;
            background: #cbd5e1;
            border-radius: 999px;
            transition: background .18s ease;
            flex: 0 0 auto;
        }
        .lp-switch-thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 10px;
            height: 10px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 1px 2px rgba(15,23,42,.25);
            transition: transform .18s ease;
        }
        .lp-switch-input:checked + .lp-switch-track {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        .lp-switch-input:checked + .lp-switch-track .lp-switch-thumb {
            transform: translateX(12px);
        }
        .lp-switch-input:focus-visible + .lp-switch-track {
            box-shadow: 0 0 0 3px rgba(16,185,129,.25);
        }
        .lp-switch-label {
            display: inline-flex;
            align-items: center;
            gap: .2rem;
        }
        .lp-switch-label .material-symbols-rounded { color: #94a3b8; font-size: 13px; }
        .lp-switch-input:checked ~ .lp-switch-label .material-symbols-rounded { color: #059669; }
        .lp-switch.is-busy { opacity: .55; pointer-events: none; }

        .lp-switch-text { display: inline; }
        .lp-switch-icon { display: none; }
        @media (max-width: 575.98px) {
            .lp-card { border-radius: .85rem; }
            .lp-card-body { padding: .85rem .65rem .6rem; gap: .45rem; }
            .lp-card-actions { gap: .5rem; padding: .5rem 0 .15rem; }
            .lp-card-toggles { gap: .25rem; flex: 1 1 auto; min-width: 0; }
            .lp-card-action-buttons { flex: 0 0 auto; gap: .25rem; }
            .lp-card-action-buttons .btn { width: 36px; height: 36px; }
            .lp-card-action-buttons .btn .material-symbols-rounded { font-size: 16px; }
            .lp-switch {
                font-size: 0;
                gap: 0;
            }
            .lp-switch-text { display: none; }
            .lp-switch-icon { display: inline-flex; }
            .lp-switch-label .material-symbols-rounded { font-size: 14px; }
            .lp-switch-track { width: 30px; height: 16px; }
            .lp-switch-thumb { width: 12px; height: 12px; }
            .lp-switch-input:checked + .lp-switch-track .lp-switch-thumb { transform: translateX(14px); }
        }

        /* ===== Empty state & Load-more ===== */
        .lp-card-empty {
            grid-column: 1 / -1;
            padding: 4rem 1rem;
            text-align: center;
            color: #94a3b8;
        }
        .lp-card-empty .material-symbols-rounded {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: .65rem;
        }
        .lp-load-more-wrap {
            display: flex;
            justify-content: center;
            margin: 1.5rem 0 .5rem;
        }
        .lp-load-more-wrap .btn { min-width: 180px; }

        .lp-card-skeleton {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            overflow: hidden;
        }
        .lp-card-skeleton .lp-skel-cover { aspect-ratio: 16 / 9; background: linear-gradient(90deg,#f1f5f9 0%,#e2e8f0 50%,#f1f5f9 100%); background-size: 200% 100%; animation: lpSkel 1.2s ease-in-out infinite; }
        .lp-card-skeleton .lp-skel-line { height: 12px; margin: .65rem 1.1rem; border-radius: 6px; background: linear-gradient(90deg,#f1f5f9 0%,#e2e8f0 50%,#f1f5f9 100%); background-size: 200% 100%; animation: lpSkel 1.2s ease-in-out infinite; }
        @keyframes lpSkel { 0% { background-position: 200% 0; } 100% { background-position: -200% 0; } }

        @media (max-width: 640px) {
            .lp-gal-toolbar { gap: .55rem; }
            .lp-gal-toolbar .lp-gal-search { max-width: none; flex-basis: 100%; }
            .lp-card-grid { grid-template-columns: 1fr; gap: .85rem; }
            .lp-add-btn-text { display: none; }
            .lp-add-btn { padding-left: .55rem; padding-right: .55rem; }
            .lp-add-btn .material-symbols-rounded { font-size: 18px; }
            .lp-gal-stats { display: none; }
        }
        @media (max-width: 380px) {
            .lp-card-actions { padding: .45rem 0 .1rem; gap: .35rem; }
            .lp-card-toggles { gap: .2rem; }
            .lp-card-action-buttons .btn { width: 34px; height: 34px; }
            .lp-card-action-buttons .btn .material-symbols-rounded { font-size: 15px; }
            .lp-switch-track { width: 28px; height: 15px; }
            .lp-switch-thumb { width: 11px; height: 11px; }
            .lp-switch-input:checked + .lp-switch-track .lp-switch-thumb { transform: translateX(13px); }
        }

        /* ===== Preview Trigger (klik cover/title/excerpt) ===== */
        .lp-preview-trigger {
            cursor: zoom-in;
            transition: opacity .15s ease;
        }
        .lp-preview-trigger:hover { opacity: .92; }
        .lp-preview-trigger:focus-visible {
            outline: 2px solid #10b981;
            outline-offset: 2px;
            border-radius: 4px;
        }

        /* ===== Modal Preview (Bootstrap 5 pola contact-messages) =====
           - Pakai class .modal fade + .modal-dialog standar Bootstrap.
           - Background PUTIH SOLID + shadow, dipindah ke <body> via JS.
           - Backdrop gelap .modal-backdrop.show otomatis di-handle Bootstrap.
           - TIDAK ADA BLUR — supaya preview tetap sharp & jelas. */
        #lpPreviewModal {
            z-index: 12050 !important;
        }
        body > .modal-backdrop.show {
            z-index: 12049 !important;
            background-color: rgba(15, 23, 42, .55) !important;
            opacity: 1 !important;
        }
        #lpPreviewModal .modal-content {
            background-color: #ffffff !important;
            border: 0 !important;
            border-radius: .75rem !important;
            box-shadow: 0 24px 64px -12px rgba(15, 23, 42, .35) !important;
            overflow: hidden;
        }
        #lpPreviewModal .modal-header {
            background-color: #ffffff !important;
            border-bottom: 1px solid #eef0f3 !important;
            padding: .75rem 1rem !important;
        }
        #lpPreviewModal .modal-body {
            background-color: #f8fafc !important;
            padding: 0 !important;
            text-align: center;
            max-height: 70vh;
            overflow: auto;
        }
        #lpPreviewModal .modal-body img,
        #lpPreviewModal .modal-body video {
            display: block;
            width: 100%;
            height: auto;
            max-height: 65vh;
            object-fit: contain;
            background: #0f172a;
        }
        #lpPreviewModal .modal-body iframe {
            display: block;
            width: 100%;
            aspect-ratio: 16 / 9;
            border: 0;
            background: #0f172a;
        }
        #lpPreviewModal .modal-footer {
            background-color: #ffffff !important;
            border-top: 1px solid #eef0f3 !important;
            padding: .6rem 1rem !important;
            font-size: .8rem;
            color: #475569;
        }
        #lpPreviewModal .modal-title {
            font-size: .95rem;
            font-weight: 600;
            color: #0f172a;
            line-height: 1.3;
            word-break: break-word;
        }
        #lpPreviewModal .lp-modal-meta {
            font-size: .72rem;
            color: #64748b;
            font-weight: 400;
            margin-top: .15rem;
        }
        #lpPreviewModal .lp-modal-empty {
            padding: 3rem 1rem;
            color: #94a3b8;
            text-align: center;
        }
        #lpPreviewModal .lp-modal-empty .material-symbols-rounded {
            font-size: 48px;
            margin-bottom: .25rem;
            display: block;
        }
        #lpPreviewModal .lp-modal-desc {
            margin: 0;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        /* Lebar: foto lebih kecil, video sedikit lebih lebar */
        #lpPreviewModal .modal-dialog { max-width: 480px; }
        #lpPreviewModal .modal-dialog.modal-dialog--video { max-width: 600px; }

        /* === Responsive HP (≤575.98px): modal full-width dengan margin aman === */
        @media (max-width: 575.98px) {
            #lpPreviewModal .modal-dialog {
                max-width: none;
                width: auto;
                margin: .35rem;
            }
            #lpPreviewModal .modal-dialog.modal-dialog--video {
                max-width: none;
            }
            #lpPreviewModal .modal-content {
                border-radius: .6rem !important;
            }
            /* Modal body: pakai dvh supaya adaptif thd keyboard HP yang naik */
            #lpPreviewModal .modal-body {
                max-height: calc(100dvh - 180px);
            }
            #lpPreviewModal .modal-body img,
            #lpPreviewModal .modal-body video {
                max-height: 55dvh;
            }
            #lpPreviewModal .modal-header,
            #lpPreviewModal .modal-footer {
                padding: .55rem .75rem !important;
            }
            #lpPreviewModal .modal-title { font-size: .85rem; }
            #lpPreviewModal .lp-modal-meta { font-size: .68rem; }
        }
        /* HP super kecil (≤380px) */
        @media (max-width: 380px) {
            #lpPreviewModal .modal-dialog { margin: .25rem; }
            #lpPreviewModal .modal-body { max-height: calc(100dvh - 160px); }
        }

        /* Trigger kursor: area yang bisa di-klik untuk preview */
        .lp-preview-trigger { cursor: zoom-in; }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $addBtn = '<a href="'.e(route('app.admin-landing.galleries.create')).'" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">'
            .'<span class="material-symbols-rounded align-middle" style="font-size:16px;">add</span>'
            .'<span class="align-middle lp-add-btn-text">Tambah Konten</span></a>';
    @endphp

    <div class="lp-gal-toolbar">
        <div class="lp-gal-search">
            <span class="material-symbols-rounded lp-search-icon">search</span>
            <input type="search" id="lp-gal-q" placeholder="Cari judul, deskripsi, album…" autocomplete="off">
        </div>
        {!! str_replace('btn btn-sm btn-primary', 'btn btn-sm btn-primary ms-auto', $addBtn) !!}
    </div>

    <div id="lp-card-grid" class="lp-card-grid"></div>

    <div class="lp-load-more-wrap">
        <button type="button" id="lp-load-more" class="btn btn-light d-none">
            <span class="material-symbols-rounded align-middle" style="font-size:18px;">expand_more</span>
            <span class="align-middle">Muat lagi</span>
        </button>
    </div>

    {{-- Modal Preview (gambar / video lokal / YouTube) - Bootstrap 5 standar --}}
    <div class="modal fade" id="lpPreviewModal" tabindex="-1" aria-labelledby="lpPreviewTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="flex-grow-1 min-w-0">
                        <h5 class="modal-title" id="lpPreviewTitle">Pratinjau</h5>
                        <div class="lp-modal-meta" id="lpPreviewMeta"></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup pratinjau"></button>
                </div>
                <div class="modal-body" id="lpPreviewBody"></div>
                <div class="modal-footer text-start">
                    <p class="lp-modal-desc" id="lpPreviewDesc"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    var grid      = document.getElementById('lp-card-grid');
    var btnMore   = document.getElementById('lp-load-more');
    var searchEl  = document.getElementById('lp-gal-q');
    var dataUrl   = @json(route('app.admin-landing.galleries.cards'));
    var csrfMeta  = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    var state = {
        page: 0,
        per_page: 24,
        total_pages: 1,
        total: 0,
        q: '',
        type: '',
        busy: false,
    };

    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function renderEmpty() {
        if (state.page > 1) return;
        grid.innerHTML = '<div class="lp-card-empty">'
            + '<div class="material-symbols-rounded">photo_library</div>'
            + '<div class="fw-semibold mb-1">Belum ada item</div>'
            + '<div class="small">Tambahkan konten melalui tombol di atas.</div>'
            + '</div>';
    }

    function renderSkeleton() {
        var html = '';
        for (var i = 0; i < 3; i++) {
            html += '<div class="lp-card-skeleton">'
                + '<div class="lp-skel-cover"></div>'
                + '<div class="lp-skel-line" style="width:60%"></div>'
                + '<div class="lp-skel-line" style="width:90%"></div>'
                + '<div class="lp-skel-line" style="width:75%"></div>'
                + '</div>';
        }
        return html;
    }

    function fetchPage(append) {
        if (state.busy) return;
        state.busy = true;
        btnMore.disabled = true;
        btnMore.querySelector('span.align-middle').textContent = 'Memuat…';

        if (!append) {
            grid.insertAdjacentHTML('beforeend', renderSkeleton());
        }

        var url = dataUrl
            + '?page=' + (state.page + 1)
            + '&per_page=' + state.per_page
            + '&q=' + encodeURIComponent(state.q);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (j) {
                document.querySelectorAll('.lp-card-skeleton').forEach(function (n) { n.remove(); });

                state.page         = j.page;
                state.total_pages  = j.total_pages;
                state.total        = j.total;

                if (j.empty) {
                    if (!append) renderEmpty();
                } else if (j.html) {
                    grid.insertAdjacentHTML('beforeend', j.html);
                }

                if (j.has_more) {
                    btnMore.classList.remove('d-none');
                    btnMore.querySelector('span.align-middle').textContent = 'Muat lagi';
                } else {
                    btnMore.classList.add('d-none');
                }
            })
            .catch(function () {
                document.querySelectorAll('.lp-card-skeleton').forEach(function (n) { n.remove(); });
                if (!append) renderEmpty();
            })
            .then(function () {
                state.busy = false;
                btnMore.disabled = false;
            });
    }

    function resetAndFetch() {
        state.page = 0;
        grid.innerHTML = '';
        btnMore.classList.add('d-none');
        fetchPage(false);
    }

    btnMore.addEventListener('click', function () { fetchPage(true); });

    var searchDebounce;
    searchEl.addEventListener('input', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(function () {
            state.q = searchEl.value.trim();
            resetAndFetch();
        }, 300);
    });

    // Konfirmasi hapus (event delegation untuk form di kartu).
    document.addEventListener('submit', function (e) {
        var form = e.target.closest('form.lp-card-delete[data-confirm]');
        if (!form) return;
        if (form.dataset.bound) return;
        form.dataset.bound = '1';
        e.preventDefault();
        var msg = form.getAttribute('data-confirm') || 'Yakin ingin menghapus data ini?';
        if (typeof Swal === 'undefined' || !Swal.fire) {
            if (window.confirm(msg)) form.submit();
            return;
        }
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
        }).then(function (r) {
            if (!r.isConfirmed) return;
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: new URLSearchParams(new FormData(form)),
            }).then(function (resp) {
                var ct = resp.headers.get('content-type') || '';
                var showOk = function (msg) {
                    lpGalToast.fire({ icon: 'success', title: msg || 'Berhasil dihapus' });
                    var card = form.closest('.lp-card');
                    if (card) card.remove();
                    state.total = Math.max(0, state.total - 1);
                    var shown = grid.querySelectorAll('.lp-card').length;
                    if (shown === 0 && state.page === 1) renderEmpty();
                    if (shown === 0 && state.page < state.total_pages) fetchPage(true);
                };
                var showErr = function (msg) {
                    lpGalToast.fire({ icon: 'error', title: msg || 'Gagal menghapus data.' });
                };
                if (resp.ok || resp.status === 204 || resp.redirected) {
                    if (ct.indexOf('application/json') >= 0) {
                        resp.json().then(function (data) {
                            if (data && data.success) showOk(data.msg); else showErr((data && data.msg) || 'Gagal menghapus data.');
                        }).catch(function () { showOk(); });
                    } else {
                        showOk();
                    }
                } else if (ct.indexOf('application/json') >= 0) {
                    resp.json().then(function (data) { showErr((data && data.msg) || 'Gagal menghapus data.'); }).catch(function () { showErr(); });
                } else {
                    showErr();
                }
            });
        });
    });

    // Toast helper pojok kanan atas (konsisten dengan helper lain).
    var lpGalToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1600,
        timerProgressBar: true,
    });

    function lpUpdateStatusBadges(card, isPublished) {
        var metaTop = card.querySelector('.lp-card-meta-top');
        if (!metaTop) return;
        metaTop.querySelectorAll('.lp-status-badge.is-published, .lp-status-badge.is-draft').forEach(function (b) { b.remove(); });
        var badge = document.createElement('span');
        badge.className = isPublished ? 'lp-status-badge is-published' : 'lp-status-badge is-draft';
        badge.textContent = isPublished ? 'Dipublikasikan' : 'Draft';
        metaTop.appendChild(badge);
    }

    function lpPostToggle(inputEl) {
        var card  = inputEl.closest('.lp-card');
        var wrap  = inputEl.closest('.lp-switch');
        var url   = inputEl.getAttribute('data-url');
        if (!url || !card || !wrap) return;
        if (wrap.classList.contains('is-busy')) return;
        wrap.classList.add('is-busy');

        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: new URLSearchParams({ '_token': csrfToken, '_method': 'PATCH' }),
        }).then(function (resp) {
            if (!resp.ok) throw new Error('http ' + resp.status);
            return resp.json();
        }).then(function (data) {
            inputEl.checked = !!data.is_published;
            lpUpdateStatusBadges(card, !!data.is_published);
            lpGalToast.fire({
                icon: 'success',
                title: (data.label || 'Publish') + ' · Tersimpan',
            });
        }).catch(function () {
            inputEl.checked = !inputEl.checked;
            lpGalToast.fire({
                icon: 'error',
                title: 'Publish · Gagal menyimpan',
            });
        }).then(function () {
            wrap.classList.remove('is-busy');
        });
    }

    document.addEventListener('change', function (e) {
        var t = e.target;
        if (!(t instanceof HTMLElement)) return;
        if (t.classList.contains('lp-toggle-publish')) { lpPostToggle(t); }
    });

    // ===== Modal Preview (gambar / video lokal / YouTube) - pola contact-messages =====
    var lpModal      = document.getElementById('lpPreviewModal');
    var lpModalEl    = lpModal; // alias
    var lpModalBody  = document.getElementById('lpPreviewBody');
    var lpModalTitle = document.getElementById('lpPreviewTitle');
    var lpModalMeta  = document.getElementById('lpPreviewMeta');
    var lpModalDesc  = document.getElementById('lpPreviewDesc');
    var lpLastFocus  = null;

    // Pindahkan modal ke <body> agar tidak terjebak stacking context .main-content
    if (lpModal && lpModal.parentNode !== document.body) {
        document.body.appendChild(lpModal);
    }

    // Instance Bootstrap Modal (fallback ke custom bila Bootstrap belum ada)
    var lpBsModal = null;
    if (lpModal && window.bootstrap && bootstrap.Modal) {
        lpBsModal = bootstrap.Modal.getOrCreateInstance(lpModal, { backdrop: true, keyboard: true });
        // Bersihkan media saat modal ditutup
        lpModal.addEventListener('hidden.bs.modal', function () {
            lpModalBody.innerHTML = '';
            if (lpLastFocus && typeof lpLastFocus.focus === 'function') {
                lpLastFocus.focus();
            }
        });
    }

    function lpTypeLabel(card) {
        var t = card.getAttribute('data-type');
        if (t === 'video') {
            var src = card.getAttribute('data-preview-source') || '';
            return src === 'local' ? 'Video · Upload Lokal' : 'Video · YouTube';
        }
        return 'Foto';
    }

    function lpRenderMedia(card) {
        var url    = card.getAttribute('data-preview-url') || '';
        var kind   = card.getAttribute('data-preview-kind') || '';
        var title  = card.getAttribute('data-preview-title') || (card.querySelector('.lp-card-title') ? card.querySelector('.lp-card-title').textContent.trim() : 'Pratinjau');
        var poster = card.getAttribute('data-preview-poster') || '';
        var escAttr = function (s) { return String(s).replace(/"/g, '&quot;'); };

        // Lebar dialog: foto lebih kecil, video sedikit lebih lebar
        var dialog = lpModal.querySelector('.modal-dialog');
        if (dialog) {
            if (kind === 'video' || kind === 'youtube') {
                dialog.classList.add('modal-dialog--video');
            } else {
                dialog.classList.remove('modal-dialog--video');
            }
        }

        var mediaHtml = '';
        if (kind === 'image' && url) {
            mediaHtml = '<img src="' + escAttr(url) + '" alt="' + escAttr(title) + '">';
        } else if (kind === 'video' && url) {
            var posterAttr = poster ? ' poster="' + escAttr(poster) + '"' : '';
            mediaHtml = '<video controls preload="metadata" playsinline' + posterAttr + ' src="' + escAttr(url) + '"></video>';
        } else if (kind === 'youtube' && url) {
            mediaHtml = '<iframe src="' + escAttr(url) + '" title="' + escAttr(title) + '" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>';
        } else {
            mediaHtml = '<div class="lp-modal-empty">'
                + '<span class="material-symbols-rounded">broken_image</span>'
                + '<div>Media tidak tersedia untuk pratinjau.</div>'
                + '</div>';
        }
        lpModalBody.innerHTML = mediaHtml;
    }

    function lpOpenPreview(card) {
        if (!card || !lpModal) return;
        var url   = card.getAttribute('data-preview-url') || '';
        var kind  = card.getAttribute('data-preview-kind') || '';
        var title = card.getAttribute('data-preview-title') || (card.querySelector('.lp-card-title') ? card.querySelector('.lp-card-title').textContent.trim() : 'Pratinjau');
        var desc  = card.getAttribute('data-preview-desc') || '';

        lpModalTitle.textContent = title;
        lpModalMeta.textContent  = lpTypeLabel(card);
        lpModalDesc.textContent  = desc;

        // Render konten media SEBELUM show, supaya tidak flicker
        lpRenderMedia(card);

        lpLastFocus = document.activeElement;

        if (lpBsModal) {
            lpBsModal.show();
        } else {
            // Fallback: tampilkan manual bila Bootstrap tidak tersedia
            lpModal.classList.add('show');
            lpModal.style.display = 'block';
            lpModal.removeAttribute('aria-hidden');
            document.body.classList.add('modal-open');
        }
        // Adaptasi keyboard HP: reposisi modal agar tidak tertekan keyboard
        lpAttachViewportHandler();
    }

    // === VisualViewport: reposisi modal agar tidak tertekan keyboard HP ===
    var lpVpHandler = null;
    function lpAttachViewportHandler() {
        if (!window.visualViewport || lpVpHandler) return;
        var vp = window.visualViewport;
        var apply = function () {
            var vpH = vp.height;
            var winH = window.innerHeight;
            var isKeyboard = vpH < winH * 0.75;
            if (lpModal) {
                if (isKeyboard) {
                    lpModal.style.alignItems = 'flex-start';
                    lpModal.style.paddingTop = '.5rem';
                } else {
                    lpModal.style.alignItems = '';
                    lpModal.style.paddingTop = '';
                }
            }
        };
        vp.addEventListener('resize', apply);
        vp.addEventListener('scroll', apply);
        lpVpHandler = apply;
        // Cleanup saat modal ditutup
        var cleanupOnce = function () {
            if (vp && lpVpHandler) {
                vp.removeEventListener('resize', lpVpHandler);
                vp.removeEventListener('scroll', lpVpHandler);
            }
            lpVpHandler = null;
            if (lpModal) {
                lpModal.style.alignItems = '';
                lpModal.style.paddingTop = '';
            }
            lpModal.removeEventListener('hidden.bs.modal', cleanupOnce);
        };
        lpModal.addEventListener('hidden.bs.modal', cleanupOnce);
    }

    function lpClosePreview() {
        if (lpBsModal) {
            lpBsModal.hide();
        } else if (lpModal) {
            lpModal.classList.remove('show');
            lpModal.style.display = 'none';
            lpModal.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('modal-open');
            lpModalBody.innerHTML = '';
        }
    }

    // Buka modal: klik pada cover / judul / excerpt (delegation).
    grid.addEventListener('click', function (e) {
        var t = e.target;
        if (!(t instanceof HTMLElement)) return;
        // Hindari trigger pada elemen interaktif di dalam card (toggle, edit, hapus).
        if (t.closest('.lp-card-actions')) return;
        if (t.closest('a, button, input, label, form')) return;
        var trig = t.closest('.lp-preview-trigger');
        if (!trig) return;
        var card = trig.closest('.lp-card');
        if (!card) return;
        e.preventDefault();
        lpOpenPreview(card);
    });

    // Aksesibilitas: Enter / Space pada trigger membuka modal.
    grid.addEventListener('keydown', function (e) {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        var t = e.target;
        if (!(t instanceof HTMLElement)) return;
        if (!t.classList.contains('lp-preview-trigger')) return;
        e.preventDefault();
        var card = t.closest('.lp-card');
        if (card) lpOpenPreview(card);
    });

    // ESC untuk tutup (Bootstrap sudah handle, tapi backup)
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && lpModal && lpModal.classList.contains('show')) {
            lpClosePreview();
        }
    });

    // Muat halaman pertama
    resetAndFetch();
})();
</script>
@include('admin-landing._skrip')
@endsection
