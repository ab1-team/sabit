@extends('halaman-publik.tata-letak')

@section('title', 'Video — ' . $setting->school_name)

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Multimedia</span>
            <h2 class="lp-section-title">Video</h2>
            <p class="lp-section-sub">Liputan dan dokumentasi kegiatan sekolah dalam video.</p>
        </div>

        @if ($videos->isEmpty())
            <div class="text-center text-muted py-5 lp-reveal" data-from="zoom">
                <i class="bi bi-camera-video" style="font-size:3rem; opacity:.3;"></i>
                <p class="mt-3 mb-0">Belum ada video.</p>
            </div>
        @else
            <div class="row g-3 g-lg-4">
                @foreach ($videos as $i => $video)
                    <div class="col-md-6 col-lg-4">
                        <button type="button"
                                class="lp-glass lp-media-card lp-reveal lp-video-trigger h-100 p-0 border-0 text-start"
                                data-from="zoom"
                                data-delay="{{ (($i % 3) + 1) }}"
                                data-yt-id="{{ $video->isYoutube() ? $video->youtube_id : '' }}"
                                data-local-src="{{ $video->isLocal() && $video->file_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($video->file_path) : '' }}"
                                data-poster="{{ $video->poster ? \Illuminate\Support\Facades\Storage::disk('public')->url($video->poster) : '' }}"
                                data-title="{{ $video->title }}"
                                data-description="{{ strip_tags($video->description ?? '') }}"
                                aria-label="Putar video: {{ $video->title }}">
                            <div class="ratio ratio-16x9 lp-video-frame">
                                @if ($video->display_thumb)
                                    <img src="{{ $video->display_thumb }}" alt="{{ $video->title }}" loading="lazy" class="lp-video-thumb-img">
                                @endif
                                <span class="lp-video-play-icon" aria-hidden="true">
                                    <svg width="64" height="64" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="32" cy="32" r="30" fill="rgba(15,23,42,.6)" stroke="rgba(255,255,255,.85)" stroke-width="2"/>
                                        <polygon points="26,20 26,44 46,32" fill="#fff"/>
                                    </svg>
                                </span>
                                @if ($video->isLocal())
                                    <span class="lp-video-source-badge">
                                        <span class="material-symbols-rounded" style="font-size:13px;">movie</span>
                                        Lokal
                                    </span>
                                @endif
                            </div>
                            <div class="lp-media-body">
                                <h6 class="fw-bold mb-2">{{ $video->title }}</h6>
                                @if ($video->description)
                                    <p class="text-muted small mb-0">
                                        {{ Str::limit(strip_tags($video->description), 100) }}
                                    </p>
                                @endif
                            </div>
                        </button>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $videos->links() }}
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
                @if (false)
                {{-- description rendered only when triggered --}}
                @endif
            </div>
            <div class="modal-footer lp-video-modal-footer justify-content-between align-items-start" id="lpVideoModalFooter" style="display:none;">
                <div id="lpVideoModalDesc" class="text-muted small"></div>
            </div>
        </div>
    </div>
</div>

<style>
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
    .lp-video-play-icon svg { display: block; }
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
    .lp-video-modal-body {
        background: #000;
    }
    .lp-video-modal-footer {
        border-top: 1px solid rgba(255,255,255,.08);
        padding: .65rem 1rem;
        background: rgba(0,0,0,.4);
    }

    @media (prefers-reduced-motion: reduce) {
        .lp-video-thumb-img,
        .lp-video-play-icon {
            transition: none;
        }
    }
</style>
@endonce

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

        function resetPlayer() {
            player.innerHTML = '';
        }

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
@endsection
