@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        /* ===== Layout: Toolbar ===== */
        .lp-posts-toolbar {
            display: flex;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            margin-bottom: 1rem;
        }
        .lp-posts-toolbar .lp-posts-search {
            flex: 1 1 220px;
            position: relative;
            max-width: 380px;
        }
        .lp-posts-toolbar .lp-posts-search input {
            width: 100%;
            padding: .55rem .95rem .55rem 2.2rem;
            border: 1px solid #d4d8dd;
            border-radius: .65rem;
            font-size: .875rem;
            background: #fff;
            color: #1f2937;
            transition: border-color .15s, box-shadow .15s;
        }
        .lp-posts-toolbar .lp-posts-search input:focus {
            outline: none;
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12);
        }
        .lp-posts-toolbar .lp-posts-search .lp-search-icon {
            position: absolute;
            left: .75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 18px;
            pointer-events: none;
        }
        .lp-posts-stats {
            color: #64748b;
            font-size: .8125rem;
            font-weight: 500;
        }

        /* ===== Layout: Grid Kartu ===== */
        .lp-card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1.1rem;
        }
        .lp-card {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }
        .lp-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -16px rgba(15,23,42,.18);
            border-color: #cbd5e1;
        }

        /* Cover */
        .lp-card-cover { position: relative; aspect-ratio: 16 / 9; background: #f1f5f9; overflow: hidden; }
        .lp-card-img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .lp-card-img--empty { color: #cbd5e1; font-size: 2.25rem; }
        .lp-card-cover .lp-card-featured-corner {
            position: absolute;
            top: .65rem;
            left: .65rem;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: #fff;
            width: 30px; height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: .5rem;
            box-shadow: 0 6px 14px -6px rgba(245,158,11,.6);
        }

        /* Body */
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
            width: 26px;
            height: 26px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .lp-card-action-buttons .btn .material-symbols-rounded {
            font-size: 14px;
        }
        .lp-card-action-buttons form { display: inline-flex; }
        .lp-card-actions .btn {
            padding: .3rem .65rem;
            font-size: .8rem;
            margin-bottom: 0;
        }

        /* ===== iOS-style switch toggle (kecil) ===== */
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
        .lp-switch:has(.lp-toggle-featured:checked) .lp-switch-label .material-symbols-rounded { color: #b45309; }
        .lp-switch.is-busy { opacity: .55; pointer-events: none; }
        @media (max-width: 575.98px) {
            .lp-card-actions { gap: .25rem; }
            .lp-card-toggles { gap: .2rem .4rem; }
            .lp-card-action-buttons { flex: 0 0 auto; }
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
            .lp-posts-toolbar { gap: .55rem; }
            .lp-posts-toolbar .lp-posts-search { max-width: none; flex-basis: 100%; }
            .lp-card-grid { grid-template-columns: 1fr; gap: .85rem; }
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    @php
        $addBtn = '<a href="'.e(route('app.admin-landing.posts.create')).'" class="btn btn-sm btn-primary d-inline-flex align-items-center gap-1">'
            .'<span class="material-symbols-rounded align-middle" style="font-size:16px;">add</span>'
            .'<span class="align-middle">Tambah Program / Berita</span></a>';
    @endphp

    <div class="lp-posts-toolbar">
        <div class="lp-posts-search">
            <span class="material-symbols-rounded lp-search-icon">search</span>
            <input type="search" id="lp-posts-q" placeholder="Cari judul, slug, kategori…" autocomplete="off">
        </div>
        <div class="lp-posts-stats ms-auto" id="lp-posts-stats">Memuat…</div>
        {!! $addBtn !!}
    </div>

    <div id="lp-card-grid" class="lp-card-grid"></div>

    <div class="lp-load-more-wrap">
        <button type="button" id="lp-load-more" class="btn btn-light d-none">
            <span class="material-symbols-rounded align-middle" style="font-size:18px;">expand_more</span>
            <span class="align-middle">Muat lagi</span>
        </button>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    var grid      = document.getElementById('lp-card-grid');
    var btnMore   = document.getElementById('lp-load-more');
    var statsEl   = document.getElementById('lp-posts-stats');
    var searchEl  = document.getElementById('lp-posts-q');
    var dataUrl   = @json(route('app.admin-landing.posts.cards'));
    var csrfMeta  = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    var state = {
        page: 0,
        per_page: 12,
        total_pages: 1,
        total: 0,
        q: '',
        busy: false,
    };

    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    function renderEmpty(after) {
        if (state.page > 1) return; // bukan halaman pertama
        grid.innerHTML = '<div class="lp-card-empty">'
            + '<div class="material-symbols-rounded">article</div>'
            + '<div class="fw-semibold mb-1">Belum ada artikel</div>'
            + '<div class="small">Tambahkan program / berita melalui tombol di atas.</div>'
            + '</div>';
    }

    function renderSkeleton(after) {
        if (!after) return '';
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
        btnMore.querySelector('span.align-middle').textContent = append ? 'Memuat…' : 'Memuat…';

        if (!append) {
            grid.insertAdjacentHTML('beforeend', renderSkeleton(true));
        }

        var url = dataUrl
            + '?page=' + (state.page + 1)
            + '&per_page=' + state.per_page
            + '&q=' + encodeURIComponent(state.q);

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (j) {
                // Hapus skeleton yang baru ditambahkan
                document.querySelectorAll('.lp-card-skeleton').forEach(function (n) { n.remove(); });

                state.page         = j.page;
                state.total_pages  = j.total_pages;
                state.total        = j.total;

                if (j.empty) {
                    if (!append) renderEmpty(false);
                } else if (j.html) {
                    grid.insertAdjacentHTML('beforeend', j.html);
                }

                if (j.has_more) {
                    btnMore.classList.remove('d-none');
                    btnMore.querySelector('span.align-middle').textContent = 'Muat lagi';
                } else {
                    btnMore.classList.add('d-none');
                }

                statsEl.textContent = 'Menampilkan ' + grid.querySelectorAll('.lp-card').length
                    + ' dari ' + state.total + ' artikel';
            })
            .catch(function () {
                document.querySelectorAll('.lp-card-skeleton').forEach(function (n) { n.remove(); });
                if (!append) renderEmpty(false);
                statsEl.textContent = 'Gagal memuat data.';
            })
            .then(function () {
                state.busy = false;
                btnMore.disabled = false;
            });
    }

    // Reset & muat ulang
    function resetAndFetch() {
        state.page = 0;
        grid.innerHTML = '';
        btnMore.classList.add('d-none');
        fetchPage(false);
    }

    // Pasang handler: tombol muat lagi
    btnMore.addEventListener('click', function () { fetchPage(true); });

    // Pasang handler: search dengan debounce 300ms
    var searchDebounce;
    searchEl.addEventListener('input', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(function () {
            state.q = searchEl.value.trim();
            resetAndFetch();
        }, 300);
    });

    // Konfirmasi hapus (event delegation untuk form di kartu)
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
                if (resp.ok || resp.status === 204 || resp.redirected) {
                    Swal.fire({ icon: 'success', title: 'Berhasil dihapus', timer: 1100, showConfirmButton: false });
                    var card = form.closest('.lp-card');
                    if (card) card.remove();
                    state.total = Math.max(0, state.total - 1);
                    var shown = grid.querySelectorAll('.lp-card').length;
                    statsEl.textContent = 'Menampilkan ' + shown + ' dari ' + state.total + ' artikel';
                    if (shown === 0 && state.page === 1) renderEmpty(false);
                    if (shown === 0 && state.page < state.total_pages) fetchPage(true);
                } else {
                    Swal.fire({ icon: 'error', title: 'Gagal menghapus data.' });
                }
            });
        });
    });

    // Toast helper pojok kanan atas (SweetAlert2 toast, konsisten dengan helper lain).
    var lpPostToast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 1600,
        timerProgressBar: true,
    });

    // Helper: update badge status di meta-top sesuai state terbaru.
    function lpUpdateStatusBadges(card, isPublished) {
        var metaTop = card.querySelector('.lp-card-meta-top');
        if (!metaTop) return;
        // Hapus badge status (published/draft), sisakan category + featured.
        metaTop.querySelectorAll('.lp-status-badge.is-published, .lp-status-badge.is-draft').forEach(function (b) { b.remove(); });
        var badge = document.createElement('span');
        badge.className = isPublished ? 'lp-status-badge is-published' : 'lp-status-badge is-draft';
        badge.textContent = isPublished ? 'Dipublikasikan' : 'Draft';
        metaTop.appendChild(badge);
    }

    // Handler universal untuk toggle publish/featured dari halaman indeks.
    function lpPostToggle(inputEl, type) {
        var card  = inputEl.closest('.lp-card');
        var wrap  = inputEl.closest('.lp-switch');
        var url   = inputEl.getAttribute('data-url');
        if (!url || !card || !wrap) return;
        if (wrap.classList.contains('is-busy')) return;
        wrap.classList.add('is-busy');
        var willBe = !inputEl.checked; // server membalik, jadi state "akan-dikirim" = !current
        var fd = new FormData();
        fd.append('_token', csrfToken);
        fd.append('_method', 'PATCH');
        fetch(url, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: new URLSearchParams(fd),
        }).then(function (resp) {
            if (!resp.ok) throw new Error('http ' + resp.status);
            return resp.json();
        }).then(function (data) {
            // Sinkronkan UI dengan state dari server.
            if (type === 'publish') {
                inputEl.checked = !!data.is_published;
                lpUpdateStatusBadges(card, !!data.is_published);
            } else if (type === 'featured') {
                inputEl.checked = !!data.is_featured;
            }
            lpPostToast.fire({
                icon: 'success',
                title: (data.label || (type === 'publish' ? 'Publish' : 'Beranda')) + ' · Tersimpan',
            });
        }).catch(function () {
            // Revert UI ke state sebelumnya.
            inputEl.checked = !inputEl.checked;
            lpPostToast.fire({
                icon: 'error',
                title: (type === 'publish' ? 'Publish' : 'Beranda') + ' · Gagal menyimpan',
            });
        }).then(function () {
            wrap.classList.remove('is-busy');
        });
    }

    document.addEventListener('change', function (e) {
        var t = e.target;
        if (!(t instanceof HTMLElement)) return;
        if (t.classList.contains('lp-toggle-publish'))  { lpPostToggle(t, 'publish');  }
        if (t.classList.contains('lp-toggle-featured')) { lpPostToggle(t, 'featured'); }
    });

    // Muat halaman pertama
    resetAndFetch();
})();
</script>
@include('admin-landing._skrip')
@endsection
