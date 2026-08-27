@extends('halaman-publik.tata-letak')

@section('title', 'Galeri — ' . $setting->school_name)

@php
    use Illuminate\Support\Str;
@endphp

@section('style')
<style>
    .lp-album-pill {
        padding: 0.5rem 1.1rem;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        color: #475569;
        font-weight: 500;
        font-size: 0.88rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    .lp-album-pill:hover {
        transform: translateY(-2px);
        color: var(--lp-primary);
        box-shadow: 0 6px 16px rgba(var(--lp-primary-rgb), 0.15);
    }
    .lp-album-pill.active {
        background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
        color: #fff;
        border-color: transparent;
        box-shadow: 0 8px 20px rgba(var(--lp-primary-rgb), 0.3);
    }
    @media (max-width: 767.98px) {
        .lp-album-pill { padding: 0.4rem 0.85rem; font-size: 0.82rem; }
    }
    @media (hover: none) {
        .lp-gallery-item::after { opacity: 1; }
        .lp-gallery-item .lp-gallery-overlay {
            transform: none;
            opacity: 1;
        }
    }
    /* Galeri video: section terpisah di bawah grid foto. */
    .lp-video-grid-section {
        margin-top: 3.5rem;
        padding-top: 2.5rem;
        border-top: 1px dashed rgba(15, 23, 42, 0.08);
    }
    .lp-video-trigger {
        width: 100%;
        cursor: pointer;
        background: transparent;
    }
    .lp-video-trigger:hover { background: rgba(255,255,255,.02); }
    .lp-video-trigger:focus-visible { outline: 3px solid var(--lp-primary, #2563eb); outline-offset: 2px; }
    .lp-video-frame {
        position: relative !important;
        overflow: hidden;
        background: #0f172a;
        border-radius: .65rem;
    }
    .lp-video-thumb-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s ease, filter .2s ease;
    }
    .lp-video-trigger:hover .lp-video-thumb-img {
        transform: scale(1.04);
        filter: brightness(.85);
    }
    .lp-video-play-icon {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
        transition: transform .25s ease;
        pointer-events: none;
        margin: 0;
    }
    .lp-video-trigger:hover .lp-video-play-icon { transform: scale(1.1); }
    .lp-video-source-badge {
        position: absolute !important;
        top: .5rem !important;
        left: .5rem !important;
        z-index: 2;
        display: inline-flex;
        align-items: center;
        gap: .25rem;
        padding: .2rem .55rem;
        border-radius: 999px;
        background: transparent;
        color: #fff;
        font-size: .68rem;
        font-weight: 600;
        letter-spacing: .04em;
        text-transform: uppercase;
        line-height: 1.2;
        text-shadow: 0 2px 6px rgba(0,0,0,.8);
    }
    .lp-video-modal-content {
        background: #0b1220;
        color: #f8fafc;
        border: 1px solid rgba(255,255,255,.08);
    }
    .lp-video-modal-header {
        border-bottom: 1px solid rgba(255,255,255,.08);
        padding: .7rem 1rem;
    }
    .lp-video-modal-header .modal-title {
        font-size: 1rem;
        font-weight: 600;
        color: #f8fafc;
    }
    .lp-video-modal-body { background: #000; }
    .lp-video-modal-footer {
        border-top: 1px solid rgba(255,255,255,.08);
        padding: .65rem 1rem;
        background: rgba(0,0,0,.4);
    }
    /* Card video di grid campuran (overlay play di tengah). */
    .lp-gallery-item--video {
        position: relative;
        display: block;
        width: 100%;
        padding: 0;
        background: #0f172a;
        cursor: pointer;
    }
    .lp-gallery-item--video > img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform .25s ease, filter .2s ease;
    }
    .lp-gallery-item--video:hover > img {
        transform: scale(1.04);
        filter: brightness(.85);
    }
    .lp-gallery-play {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
        pointer-events: none;
        transition: transform .25s ease;
    }
    .lp-gallery-item--video:hover .lp-gallery-play { transform: scale(1.1); }
    .lp-gallery-item--video:focus-visible { outline: 3px solid var(--lp-primary, #2563eb); outline-offset: 2px; }
    @media (prefers-reduced-motion: reduce) {
        .lp-video-thumb-img,
        .lp-video-play-icon,
        .lp-gallery-item--video > img,
        .lp-gallery-play { transition: none; }
    }
</style>
@endsection

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Momen</span>
            <h2 class="lp-section-title">Galeri</h2>
            <p class="lp-section-sub">Dokumentasi kegiatan &amp; momen berharga di sekolah kami — dalam foto &amp; video.</p>
        </div>

        @if ($albums->isNotEmpty())
            <div class="text-center mb-4 d-flex flex-wrap justify-content-center gap-2 lp-reveal" data-from="zoom">
                <a href="{{ route('halaman-publik.galeri') }}"
                   class="lp-album-pill {{ $album ? '' : 'active' }}">Semua</a>
                @foreach ($albums as $item)
                    <a href="{{ route('halaman-publik.galeri', ['album' => $item]) }}"
                       class="lp-album-pill {{ $album === $item ? 'active' : '' }}">{{ $item }}</a>
                @endforeach
            </div>
        @endif

        @if (($items ?? collect())->isEmpty() && ($videos ?? collect())->isEmpty())
            <div class="text-center text-muted py-5 lp-reveal" data-from="zoom">
                <i class="bi bi-image" style="font-size:3rem; opacity:.3;"></i>
                <p class="mt-3 mb-0">Belum ada dokumentasi.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach (($items ?? collect()) as $i => $item)
                    @if ($item->media_type === 'video')
                        <div class="col-6 col-md-4 col-lg-3">
                            <button type="button"
                                    class="lp-gallery-item lp-gallery-item--video lp-reveal lp-video-trigger border-0 p-0 d-block"
                                    data-from="zoom"
                                    data-delay="{{ (($i % 4) + 1) }}"
                                    data-yt-id="{{ $item->youtube_id ?? '' }}"
                                    data-local-src="{{ $item->local_src ?? '' }}"
                                    data-poster="{{ $item->poster_url ?? '' }}"
                                    data-title="{{ $item->title }}"
                                    data-description="{{ strip_tags($item->description ?? '') }}"
                                    aria-label="Putar video: {{ $item->title }}">
                                <img src="{{ $item->poster_url ?: 'https://i.ytimg.com/vi/'.($item->youtube_id ?? '').'/hqdefault.jpg' }}" alt="{{ $item->title }}" loading="lazy">
                                <span class="lp-gallery-play" aria-hidden="true">
                                    <svg width="48" height="48" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="32" cy="32" r="30" fill="rgba(15,23,42,.6)" stroke="rgba(255,255,255,.85)" stroke-width="2"/>
                                        <polygon points="26,20 26,44 46,32" fill="#fff"/>
                                    </svg>
                                </span>
                                <div class="lp-gallery-overlay">{{ $item->title }}</div>
                            </button>
                        </div>
                    @else
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ $item->image_path ? Storage::disk('public')->url('landing/' . $item->image_path) : '#' }}" target="_blank" class="lp-gallery-item lp-reveal d-block" data-from="zoom" data-delay="{{ (($i % 4) + 1) }}">
                                @if ($item->image_path)
                                    <img src="{{ Storage::disk('public')->url('landing/' . $item->image_path) }}" alt="{{ $item->title }}" loading="lazy">
                                    <div class="lp-gallery-overlay">{{ $item->title }}</div>
                                @endif
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>
</section>

@once
<div class="modal fade" id="lpVideoModal" tabindex="-1" aria-hidden="true" aria-labelledby="lpVideoModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-fullscreen-sm-down">
        <div class="modal-content lp-video-modal-content">
            <div class="modal-header lp-video-modal-header">
                <h5 class="modal-title" id="lpVideoModalTitle">Video</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body p-0 lp-video-modal-body">
                <div id="lpVideoModalPlayer" class="ratio ratio-16x9"></div>
            </div>
            <div class="modal-footer lp-video-modal-footer justify-content-between align-items-start" id="lpVideoModalFooter" style="display:none;">
                <div id="lpVideoModalDesc" class="text-muted small"></div>
            </div>
        </div>
    </div>
</div>
@endonce

@once
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl = document.getElementById('lpVideoModal');
        if (! modalEl || ! window.bootstrap) return;

        var player = document.getElementById('lpVideoModalPlayer');
        var titleEl = document.getElementById('lpVideoModalTitle');
        var descEl = document.getElementById('lpVideoModalDesc');
        var footerEl = document.getElementById('lpVideoModalFooter');
        var bsModal = null;
        try { bsModal = new bootstrap.Modal(modalEl); } catch (e) { return; }

        function resetPlayer() { player.innerHTML = ''; }

        // Body-lock navbar + konten saat modal video dibuka (mirip pola modal
        // sambutan). Pakai class pada <html> & <body> supaya sibling selector
        // `body.lp-media-modal-open > *` bisa memblur isi di belakang modal.
        function lockBody() {
            document.documentElement.classList.add('lp-media-modal-open');
            document.body.classList.add('lp-media-modal-open');
        }
        function unlockBody() {
            document.documentElement.classList.remove('lp-media-modal-open');
            document.body.classList.remove('lp-media-modal-open');
        }

        modalEl.addEventListener('show.bs.modal', lockBody);
        modalEl.addEventListener('hidden.bs.modal', unlockBody);

        function openVideoFromTrigger(trigger) {
            var ytId = trigger.getAttribute('data-yt-id') || '';
            var localSrc = trigger.getAttribute('data-local-src') || '';
            var poster = trigger.getAttribute('data-poster') || '';
            var title = trigger.getAttribute('data-title') || 'Video';
            var desc = trigger.getAttribute('data-description') || '';

            titleEl.textContent = title;
            if (desc) {
                descEl.textContent = desc;
                footerEl.style.display = '';
            } else {
                descEl.textContent = '';
                footerEl.style.display = 'none';
            }

            resetPlayer();

            if (ytId) {
                var src = 'https://www.youtube.com/embed/' + encodeURIComponent(ytId) + '?autoplay=1&rel=0';
                var iframe = document.createElement('iframe');
                iframe.src = src;
                iframe.title = title;
                iframe.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share');
                iframe.setAttribute('allowfullscreen', '');
                iframe.style.border = '0';
                player.appendChild(iframe);
            } else if (localSrc) {
                var vid = document.createElement('video');
                vid.src = localSrc;
                if (poster) vid.poster = poster;
                vid.controls = true;
                vid.autoplay = true;
                vid.style.width = '100%';
                vid.style.height = '100%';
                vid.style.objectFit = 'contain';
                vid.style.background = '#000';
                player.appendChild(vid);
            } else {
                player.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted">'
                    + '<div class="text-center"><span class="material-symbols-rounded" style="font-size:48px;">videocam_off</span>'
                    + '<div class="small mt-2">Video tidak tersedia.</div></div></div>';
            }

            bsModal.show();
        }

        document.querySelectorAll('.lp-video-trigger').forEach(function (trigger) {
            trigger.addEventListener('click', function () { openVideoFromTrigger(trigger); });
        });

        modalEl.addEventListener('hidden.bs.modal', function () {
            resetPlayer();
            titleEl.textContent = 'Video';
            descEl.textContent = '';
            footerEl.style.display = 'none';
        });
    });
</script>
@endonce
@endsection
