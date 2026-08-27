@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-gal-form-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            background: #fff;
        }
        .lp-gal-form-card > .card-body { padding: .9rem 1.05rem; }

        /* Pakai .lp-field khusus untuk wrapper yang tidak ke-handle Material outlined. */
        .lp-field { display: flex; flex-direction: column; gap: .3rem; margin-bottom: .7rem; }
        .lp-field > label { font-size: .8rem; font-weight: 600; color: #334155; margin: 0; }
        .lp-field.req > label::after { content: " *"; color: #dc2626; }
        .lp-field .help, .help { font-size: .7rem; color: #94a3b8; margin-top: -.25rem; }

        /* Field visibility di-handle JS applyType() via [hidden] attr. */
        /* Material-dashboard sudah punya [hidden]{display:none!important}. */

        /* Template option Select2 album (custom; tema hijau di-handle base layout). */
        .lp-album-option {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            width: 100%;
        }
        .lp-album-icon {
            font-size: 18px;
            color: #15803d;
            line-height: 1;
        }
        .lp-album-icon-inline {
            font-size: 16px;
            color: #15803d;
            line-height: 1;
            margin-right: .35rem;
            vertical-align: -3px;
        }
        .lp-album-text {
            flex: 1 1 auto;
            min-width: 0;
        }
        .lp-album-tag {
            display: inline-block;
            padding: 0 .4rem;
            font-size: .65rem;
            font-weight: 600;
            line-height: 1.4;
            border-radius: .3rem;
            background: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            text-transform: uppercase;
            letter-spacing: .02em;
        }
        .lp-album-tag-default {
            background: #e6f9ed;
            color: #166534;
            border-color: #b6efc8;
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted .lp-album-tag,
        .select2-container--bootstrap-5 .select2-results__option--highlighted .lp-album-icon,
        .select2-container--bootstrap-5 .select2-results__option--highlighted .lp-album-text {
            color: #14532d !important;
        }
        .select2-container--bootstrap-5 .select2-results__option--selected .lp-album-tag {
            background: #37d17c;
            color: #fff;
            border-color: #37d17c;
        }

        .lp-form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            align-items: stretch;
        }
        @media (min-width: 768px) {
            .lp-form-grid {
                grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
                gap: 1rem;
            }
        }
        .lp-form-col {
            display: flex;
            flex-direction: column;
            gap: 0;
            min-width: 0;
        }

        /* Form helper banner */
        .lp-form-help {
            display: flex;
            align-items: center;
            padding: .65rem .85rem;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: .55rem;
            color: #1e40af;
            font-size: .85rem;
        }

        /* Tipe media segmented control */
        .lp-type-tabs {
            display: inline-flex;
            align-items: center;
            gap: 0;
            padding: .25rem;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: .75rem;
            margin-bottom: .85rem;
        }
        .lp-type-tab {
            border: 0;
            background: transparent;
            color: #475569;
            font-size: .85rem;
            font-weight: 600;
            padding: .4rem .9rem;
            border-radius: .5rem;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            cursor: pointer;
            transition: background .15s ease, color .15s ease, box-shadow .15s ease;
        }
        .lp-type-tab:hover { color: #1d4ed8; }
        .lp-type-tab.is-active {
            background: #fff;
            color: #1d4ed8;
            box-shadow: 0 1px 2px rgba(15,23,42,.06);
        }
        .lp-type-tab .material-symbols-rounded { font-size: 18px; }

        /* Preview gambar — identik formulir sebelumnya */
        .lp-preview-wrap {
            display: flex;
            flex-direction: column;
            flex: 0 0 auto;
            margin-bottom: .7rem;
        }
        .lp-preview-wrap > .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
            margin: 0 0 .3rem 0;
        }
        .lp-preview-box.lp-preview-cover {
            position: relative;
            height: 180px;
            min-height: 180px;
            padding: 0;
        }
        .lp-preview-box.lp-preview-cover img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .lp-preview-box.lp-preview-cover .lp-preview-empty,
        .lp-preview-box.lp-preview-cover .lp-preview-hint {
            position: relative;
            z-index: 1;
        }
        .lp-preview-box.lp-preview-cover:not(:has(img)) .lp-preview-empty,
        .lp-preview-box.lp-preview-cover:not(:has(img)) .lp-preview-hint {
            position: absolute;
        }
        .lp-preview-box.lp-preview-cover:not(:has(img)) .lp-preview-empty {
            top: 50%;
            left: 50%;
            transform: translate(-50%, -65%);
        }
        .lp-preview-box.lp-preview-cover:not(:has(img)) .lp-preview-hint {
            top: 50%;
            left: 50%;
            transform: translate(-50%, 30%);
        }
        .lp-preview-box.lp-preview-cover .lp-preview-hint {
            position: absolute;
            top: auto;
            bottom: .5rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15,23,42,.55);
            color: #fff;
            padding: .25rem .55rem;
            border-radius: .35rem;
            font-size: .72rem;
            font-weight: 500;
            white-space: nowrap;
            z-index: 2;
            opacity: .85;
        }
        .lp-preview-empty { font-size: 36px; }
        .lp-preview-title {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #475569;
            margin-top: .55rem;
        }

        /* Preview video (kolom kanan) */
        .lp-vid-preview-box {
            position: relative;
            width: 100%;
            aspect-ratio: 16 / 9;
            border-radius: .65rem;
            overflow: hidden;
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .lp-vid-preview-box img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .lp-vid-preview-box .lp-vid-preview-empty {
            color: rgba(255,255,255,.55);
            text-align: center;
            padding: .75rem;
        }
        .lp-vid-preview-box .lp-vid-preview-empty .material-symbols-rounded {
            font-size: 42px;
            display: block;
            margin-bottom: .25rem;
        }
        .lp-vid-preview-box .lp-vid-play-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 56px !important;
            pointer-events: none;
            text-shadow: 0 2px 6px rgba(0,0,0,.5);
        }
        #videoPreviewVideo {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #0f172a;
        }

        /* Source radio pill */
        .lp-radio-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .45rem .85rem;
            border: 1px solid #d4d8dd;
            border-radius: 999px;
            background: #fff;
            cursor: pointer;
            font-size: .85rem;
            color: #475569;
            transition: all .15s ease;
        }
        .lp-radio-pill input { display: none; }
        .lp-radio-pill .material-symbols-rounded { font-size: 18px; color: #64748b; }
        .lp-radio-pill:hover { border-color: #94a3b8; background: #f8fafc; }
        .lp-radio-pill:has(input:checked) {
            border-color: #1d4ed8;
            background: #eff6ff;
            color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.10);
        }
        .lp-radio-pill:has(input:checked) .material-symbols-rounded { color: #1d4ed8; }

        /* Publish card bar */
        .lp-publish-card {
            display: flex;
            flex-wrap: wrap;
            gap: .9rem;
            align-items: center;
            justify-content: space-between;
            padding: .55rem .85rem;
            border-top: 1px dashed #e2e8f0;
            flex: 0 0 auto;
            background: transparent;
        }
        .lp-publish-card .lp-field { margin-bottom: 0; flex: 0 0 auto; }
        .lp-publish-card .lp-field-sort { min-width: 130px; }
        .lp-publish-card .lp-field-switch { padding-bottom: 0; }
        .lp-publish-card .lp-switch-inline {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .lp-publish-card .lp-switch-text {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }
        .lp-publish-card .lp-switch-text strong { font-size: .82rem; color: #1f2937; }
        .lp-publish-card .lp-switch-text small { font-size: .7rem; color: #64748b; }
        .lp-publish-card .form-check.form-switch { margin: 0; flex: 0 0 auto; }
        @media (max-width: 575.98px) {
            .lp-publish-card .lp-field-sort,
            .lp-publish-card .lp-field-switch { flex: 1 1 100%; }
            .lp-publish-card .lp-field-sort { min-width: 0; }
        }

        /* Footer form */
        .lp-gal-foot {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            padding: .55rem 1.05rem;
            background: #f8fafc;
            border-radius: 0 0 .75rem .75rem;
        }
        @media (min-width: 768px) {
            .lp-gal-foot {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .lp-gal-foot .lp-foot-hint { font-size: .8rem; color: #475569; }
        .lp-gal-foot .btn {
            min-height: 38px;
            padding: .4rem 1rem;
            border-radius: .5rem;
            font-size: .88rem;
        }

        .lp-gal-page { padding: .5rem .75rem; }
        @media (max-width: 575.98px) {
            .lp-gal-page { padding: .5rem .5rem; }
        }
        @media (max-width: 380px) {
            .lp-gal-page { padding: .4rem .35rem; }
        }
    </style>
@endsection

@section('content')
<div class="lp-gal-page">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-2">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-2">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="alert alert-danger py-2 small mb-2 lp-ajax-errors" style="display:none;" role="alert">
        <strong class="lp-ajax-errors-title d-block mb-1">Data belum lengkap:</strong>
        <ul class="mb-0 ps-3 lp-ajax-errors-list"></ul>
    </div>

    @php
        $isEdit = !empty($formData) && !empty($formData['id']);
        $fd = $formData ?? [];
        // Pre-populated values for edit.
        $oldTitle       = old('title', $fd['title'] ?? '');
        $oldDescription = old('description', $fd['description'] ?? '');
        $oldAlbum       = old('album', $fd['album'] ?? '');
        $oldIsPublished = old('is_published', $fd['is_published'] ?? true);
        $oldSource      = old('source', $fd['source'] ?? 'youtube');
        $oldYoutubeUrl  = old('youtube_url', $fd['youtube_url'] ?? '');
        $imageUrl       = $fd['image_url'] ?? null;
        $posterUrl      = $fd['poster_url'] ?? null;
        $fileUrl        = $fd['file_url'] ?? null;
        $filePathOld    = $fd['file_path'] ?? null;
        $posterPathOld  = $fd['poster_path'] ?? null;
        $albumOptions   = $albumOptions ?? [];
    @endphp

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if ($isEdit) @method('PUT') @endif
        <input type="hidden" name="media_type" id="mediaTypeInput" value="{{ $mediaType }}">

        <div class="card my-2 lp-gal-form-card">
            <div class="card-body">
                @if($isEdit)
                    <div class="lp-form-help mb-3">
                        <span class="material-symbols-rounded align-middle" style="font-size:16px;color:#1d4ed8;">info</span>
                        <span class="align-middle ms-1">
                            Jenis konten: <strong>{{ $mediaType === 'video' ? 'Video' : 'Foto' }}</strong>. Tidak dapat diubah.
                        </span>
                    </div>
                @else
                    <div class="lp-type-tabs" id="lpTypeTabs" role="tablist">
                        <button type="button" class="lp-type-tab {{ $mediaType === 'photo' ? 'is-active' : '' }}" data-tab="photo" role="tab" aria-selected="{{ $mediaType === 'photo' ? 'true' : 'false' }}">
                            <span class="material-symbols-rounded">photo_library</span> Foto
                        </button>
                        <button type="button" class="lp-type-tab {{ $mediaType === 'video' ? 'is-active' : '' }}" data-tab="video" role="tab" aria-selected="{{ $mediaType === 'video' ? 'true' : 'false' }}">
                            <span class="material-symbols-rounded">smart_display</span> Video
                        </button>
                    </div>
                @endif

                <div class="lp-form-grid">
                    {{-- KOLOM KIRI --}}
                    <div class="lp-form-col">
                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Judul</label>
                            <input id="title" type="text" name="title" class="form-control"
                                   value="{{ $oldTitle }}" required>
                        </div>

                        {{-- Field khusus FOTO --}}
                        <div class="input-group input-group-outline mb-3 lp-photo-only" id="albumField" @if($mediaType !== 'photo') hidden @endif>
                            <select id="album" name="album" class="form-select select2">
                                @foreach(($albumDefaults ?? []) as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                                @foreach(($albumOptions ?? []) as $opt)
                                    @if(!in_array($opt, ($albumDefaults ?? []), true))
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>


                        {{-- Field khusus VIDEO --}}
                        <div class="lp-field req lp-video-only" id="ytUrlField" data-video-field @if($mediaType !== 'video') hidden @endif>
                            <label>Sumber Video</label>
                            <div class="d-flex flex-wrap gap-3 align-items-center" role="radiogroup" aria-label="Sumber video">
                                <label class="lp-radio-pill">
                                    <input type="radio" name="source" value="youtube" {{ $oldSource === 'youtube' ? 'checked' : '' }}>
                                    <span class="material-symbols-rounded">smart_display</span>
                                    <span>YouTube</span>
                                </label>
                                <label class="lp-radio-pill">
                                    <input type="radio" name="source" value="local" {{ $oldSource === 'local' ? 'checked' : '' }}>
                                    <span class="material-symbols-rounded">movie</span>
                                    <span>Upload File</span>
                                </label>
                            </div>
                            <small class="help">Pilih YouTube untuk embed video daring, atau Upload File untuk video lokal (mp4/webm/mov, maks 50MB).</small>
                        </div>

                        <div class="input-group input-group-outline mb-3 lp-video-only" id="ytUrlInput" @if($mediaType !== 'video') hidden @endif>
                            <label class="form-label">URL YouTube</label>
                            <input id="youtube_url" type="url" name="youtube_url" class="form-control"
                                   value="{{ $oldYoutubeUrl }}">
                        </div>

                        <div class="mb-3 lp-video-only" id="localFileField" @if($mediaType !== 'video') hidden @endif>
                            <label class="form-label">File Video</label>
                            <input id="video_file" type="file" name="video_file" class="form-control lp-bordered-input"
                                   accept="video/mp4,video/webm,video/quicktime,video/x-matroska,video/x-m4v,.mp4,.m4v,.mov,.webm,.mkv">
                         </div>

                        <div class="mb-3 lp-video-only" id="localPosterField" @if($mediaType !== 'video') hidden @endif>
                            <label class="form-label">Poster (Opsional)</label>
                            <input id="video_poster" type="file" name="video_poster" class="form-control lp-bordered-input"
                                   accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="input-group input-group-outline mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea id="description" name="description" class="form-control" rows="5">{{ $oldDescription }}</textarea>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="lp-form-col lp-form-col-side">
                        {{-- Preview FOTO --}}
                        <div class="lp-preview-wrap" id="photoPreviewWrap" @if($mediaType !== 'photo') hidden @endif>
                            <label for="imageInput" class="lp-preview-box lp-preview-cover d-block" id="imagePreviewBox" aria-label="Foto sampul">
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="Foto" id="imagePreviewImg">
                                @else
                                    <span class="material-symbols-rounded lp-preview-empty" id="imagePreviewEmpty">add_photo_alternate</span>
                                    <span class="lp-preview-title">Foto sampul @if(!$isEdit)<span class="text-danger">*</span>@endif</span>
                                @endif
                                <span class="lp-preview-hint">
                                    {{ $isEdit ? 'Klik untuk ganti foto (opsional)' : 'Klik untuk pilih foto (JPG/PNG/WEBP, maks 4MB)' }}
                                </span>
                            </label>
                            <input type="file" name="image" class="d-none" accept="image/*"
                                   id="imageInput">
                            @if ($isEdit && !empty($fd['image_path']))
                                <div class="small text-muted mt-1">File saat ini: <code>{{ $fd['image_path'] }}</code></div>
                            @endif
                        </div>

                        {{-- Preview VIDEO --}}
                        <div class="lp-vid-preview-wrap lp-video-only" id="videoPreviewWrap" @if($mediaType !== 'video') hidden @endif>
                            <label class="form-label">Preview Video</label>
                            <div class="lp-vid-preview-box" id="videoPreviewBox">
                                <span class="lp-vid-preview-empty" id="videoPreviewEmpty">
                                    <span class="material-symbols-rounded">smart_display</span>
                                    <div class="lp-vid-preview-empty-text">Belum ada video</div>
                                </span>
                                <span class="material-symbols-rounded lp-vid-play-overlay" id="videoPlayOverlay" style="display:none;">play_circle</span>
                                <img id="videoPreviewImg" src="{{ $posterUrl ?: '' }}" alt="" style="display:{{ $posterUrl ? 'block' : 'none' }};">
                                <video id="videoPreviewVideo" controls style="display:none;"></video>
                            </div>
                            <div class="small text-muted mt-1" id="videoPreviewUrl"></div>
                        </div>

                        <div class="lp-publish-card">
                            <div class="lp-field lp-field-switch">
                                <div class="lp-switch-inline">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                               id="is_pub_gallery" {{ $oldIsPublished ? 'checked' : '' }}>
                                    </div>
                                    <div class="lp-switch-text">
                                        <strong>Publish</strong>
                                        <small>Tampilkan di halaman publik.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="help w-100 mt-1 mb-0" id="publishHint">
                                {{ $mediaType === 'video' ? 'Video yang di-publish akan muncul di halaman /galeri (bagian video).' : 'Foto yang di-publish akan muncul di halaman /galeri.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lp-gal-foot border-top">
                <span class="lp-foot-hint">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('app.admin-landing.galleries') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">arrow_back</span>
                        <span class="align-middle">Kembali</span>
                    </a>
                    <button type="submit" id="submitBtn" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle lp-submit-icon" style="font-size:18px;">save</span>
                        <span class="material-symbols-rounded align-middle lp-spinner d-none" style="font-size:18px;">progress_activity</span>
                        <span class="align-middle lp-submit-label">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Konten' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
(function() {
    var typeInput = document.getElementById('mediaTypeInput');
    var mediaType = typeInput ? typeInput.value : 'photo';
    var isEdit = {{ $isEdit ? 'true' : 'false' }};
    var tabs = document.querySelectorAll('#lpTypeTabs .lp-type-tab');
    var photoEls = document.querySelectorAll('.lp-photo-only');
    var videoEls = document.querySelectorAll('.lp-video-only');
    var imageInput = document.getElementById('imageInput');
    var imagePreviewBox = document.getElementById('imagePreviewBox');
    var imagePreviewImg = document.getElementById('imagePreviewImg');
    var publishHint = document.getElementById('publishHint');
    var ytUrlInput = document.getElementById('youtube_url');
    var videoFileInput = document.getElementById('video_file');

    function getEl(id) { return document.getElementById(id); }

    function applyType(type) {
        mediaType = type;
        if (typeInput) typeInput.value = type;
        var isPhoto = type === 'photo';
        // Pakai [hidden] attr — material-dashboard CSS sudah punya [hidden]{display:none!important}
        photoEls.forEach(function (el) {
            if (isPhoto) el.removeAttribute('hidden');
            else el.setAttribute('hidden', '');
        });
        videoEls.forEach(function (el) {
            if (isPhoto) el.setAttribute('hidden', '');
            else el.removeAttribute('hidden');
        });
        // Toggle preview wrap (kolom kanan) sesuai tipe — pakai [hidden] juga
        var photoWrap = document.getElementById('photoPreviewWrap');
        var videoWrap = document.getElementById('videoPreviewWrap');
        if (photoWrap) {
            if (isPhoto) photoWrap.removeAttribute('hidden');
            else photoWrap.setAttribute('hidden', '');
        }
        if (videoWrap) {
            if (isPhoto) videoWrap.setAttribute('hidden', '');
            else videoWrap.removeAttribute('hidden');
        }
        if (publishHint) {
            publishHint.textContent = isPhoto
                ? 'Foto yang di-publish akan muncul di halaman /galeri.'
                : 'Video yang di-publish akan muncul di halaman /galeri (bagian video).';
        }
        tabs.forEach(function (tab) {
            var active = tab.getAttribute('data-tab') === type;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        if (!isPhoto) applySource();
    }

    // Tab switching (hanya mode create)
    tabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            if (isEdit) return;
            applyType(tab.getAttribute('data-tab'));
        });
    });

    // === Foto preview ===
    if (imageInput) {
        imageInput.addEventListener('change', function (e) {
            var file = e.target.files && e.target.files[0];
            if (!file) return;
            var reader = new FileReader();
            reader.onload = function (ev) {
                if (imagePreviewImg) {
                    imagePreviewImg.src = ev.target.result;
                    imagePreviewImg.style.position = 'absolute';
                } else {
                    var img = document.createElement('img');
                    img.src = ev.target.result;
                    img.alt = 'Foto';
                    img.id = 'imagePreviewImg';
                    img.style.cssText = 'position:absolute;inset:0;width:100%;height:100%;max-width:100%;max-height:100%;object-fit:cover;';
                    imagePreviewBox.insertBefore(img, imagePreviewBox.firstChild);
                }
                var empty = document.getElementById('imagePreviewEmpty');
                if (empty) empty.remove();
                var title = imagePreviewBox.querySelector('.lp-preview-title');
                if (title) title.remove();
            };
            reader.readAsDataURL(file);
        });
    }

    // === Video source switching ===
    function extractYoutubeId(url) {
        if (!url) return null;
        url = String(url).trim();
        var m;
        m = url.match(/youtu\.be\/([A-Za-z0-9_-]{6,})/i);
        if (m) return m[1];
        m = url.match(/youtube\.com\/embed\/([A-Za-z0-9_-]{6,})/i);
        if (m) return m[1];
        m = url.match(/youtube\.com\/shorts\/([A-Za-z0-9_-]{6,})/i);
        if (m) return m[1];
        m = url.match(/youtube\.com\/(?:watch\?v=|v\/)([A-Za-z0-9_-]{6,})/i);
        if (m) return m[1];
        return null;
    }

    function thumbUrl(id) {
        return 'https://i.ytimg.com/vi/' + id + '/hqdefault.jpg';
    }

    function showOnly(which) {
        var img = getEl('videoPreviewImg');
        var vid = getEl('videoPreviewVideo');
        var empty = getEl('videoPreviewEmpty');
        var overlay = getEl('videoPlayOverlay');
        if (img) img.style.display = 'none';
        if (vid) vid.style.display = 'none';
        if (overlay) overlay.style.display = 'none';
        if (which === 'img' && img) img.style.display = 'block';
        else if (which === 'video' && vid) vid.style.display = 'block';
        else if (which === 'empty' && empty) empty.style.display = '';
        if (which !== 'empty' && empty) empty.style.display = 'none';
    }

    function refreshYoutubePreview() {
        var url = (getEl('youtube_url') || {}).value || '';
        var id = extractYoutubeId(url);
        var img = getEl('videoPreviewImg');
        var overlay = getEl('videoPlayOverlay');
        var caption = getEl('videoPreviewUrl');

        if (id) {
            img.src = thumbUrl(id);
            showOnly('img');
            if (overlay) overlay.style.display = 'flex';
            if (caption) caption.textContent = 'ID: ' + id;
        } else {
            showOnly('empty');
            if (caption) caption.textContent = url ? 'URL tidak dikenali sebagai YouTube.' : '';
        }
    }

    function refreshLocalPreview() {
        var fileInput = getEl('video_file');
        var posterInput = getEl('video_poster');
        var vid = getEl('videoPreviewVideo');
        var overlay = getEl('videoPlayOverlay');
        var caption = getEl('videoPreviewUrl');

        if (fileInput && fileInput.files && fileInput.files[0]) {
            var file = fileInput.files[0];
            var url = URL.createObjectURL(file);
            vid.src = url;
            if (posterInput && posterInput.files && posterInput.files[0]) {
                vid.poster = URL.createObjectURL(posterInput.files[0]);
            } else {
                vid.removeAttribute('poster');
            }
            vid.load();
            showOnly('video');
            if (overlay) overlay.style.display = 'none';
            if (caption) caption.textContent = file.name + ' (' + Math.round(file.size / 1024) + ' KB)';
        } else {
            if (vid) vid.removeAttribute('src');
            if (caption) caption.textContent = 'Belum ada file dipilih.';
        }
    }

    function applySource() {
        var checked = document.querySelector('input[name="source"]:checked');
        var source = checked ? checked.value : 'youtube';
        var ytInput = getEl('ytUrlInput');
        var lf = getEl('localFileField');
        var lp = getEl('localPosterField');

        if (source === 'youtube') {
            if (ytInput) ytInput.removeAttribute('hidden');
            if (lf) lf.setAttribute('hidden', '');
            if (lp) lp.setAttribute('hidden', '');
            refreshYoutubePreview();
        } else {
            if (ytInput) ytInput.setAttribute('hidden', '');
            if (lf) lf.removeAttribute('hidden');
            if (lp) lp.removeAttribute('hidden');
            refreshLocalPreview();
        }
    }

    // === Validasi client-side ===
    var VIDEO_MAX_BYTES = 50 * 1024 * 1024;
    var POSTER_MAX_BYTES = 5 * 1024 * 1024;
    var VIDEO_ALLOWED_EXT = ['mp4','m4v','mov','webm','mkv','qt'];
    var VIDEO_ALLOWED_MIME = [
        'video/mp4','video/webm','video/quicktime','video/x-matroska',
        'video/x-m4v','application/octet-stream','application/mp4',
    ];
    function getExt(name) {
        if (!name) return '';
        var i = name.lastIndexOf('.');
        return i >= 0 ? name.substring(i + 1).toLowerCase() : '';
    }
    function validateFileForUpload(file, allowedExt, allowedMime, maxBytes, label) {
        if (!file) return 'File ' + label + ' tidak ditemukan.';
        if (file.size <= 0) return 'File ' + label + ' kosong (0 byte).';
        if (file.size > maxBytes) {
            var mb = Math.round(file.size / 1024 / 1024);
            var maxMb = Math.round(maxBytes / 1024 / 1024);
            return 'Ukuran ' + label + ' ' + mb + 'MB melebihi batas ' + maxMb + 'MB.';
        }
        var ext = getExt(file.name);
        var mime = (file.type || '').toLowerCase();
        var okExt = allowedExt.indexOf(ext) >= 0;
        var okMime = allowedMime.indexOf(mime) >= 0
            || (mime && mime.indexOf('video/') === 0);
        if (!okExt && !okMime) {
            return 'Format ' + label + ' tidak didukung.';
        }
        return null;
    }

    jQuery(document).ready(function($) {
        // Inisialisasi Select2 standar (pola siswa/tambah.blade.php).
        $('.select2').select2({ theme: 'bootstrap-5', allowClear: true, width: '100%' });

        // Album: upgrade dengan tags + template option (icon folder + badge Default/Custom).
        var $album = $('#album');
        if ($album.length) {
            var defaultAlbums = @json($albumDefaults ?? []);
            var oldAlbum = @json($oldAlbum);
            if (oldAlbum && $album.find('option[value="' + oldAlbum.replace(/"/g, '\\"') + '"]').length === 0) {
                $album.append(new Option(oldAlbum, oldAlbum, true, true));
            }

            function albumResult(state) {
                if (!state.id) return state.text;
                var isDefault = defaultAlbums.indexOf(state.id) >= 0;
                var tag = isDefault
                    ? '<span class="lp-album-tag lp-album-tag-default">Default</span>'
                    : '<span class="lp-album-tag">Custom</span>';
                return '<span class="lp-album-option"><span class="material-symbols-rounded lp-album-icon">photo_library</span><span class="lp-album-text">' + state.text + '</span>' + tag + '</span>';
            }
            function albumSelection(state) {
                if (!state.id) return state.text;
                var isDefault = defaultAlbums.indexOf(state.id) >= 0;
                return '<span class="material-symbols-rounded lp-album-icon-inline">' + (isDefault ? 'folder' : 'photo_library') + '</span><span class="lp-album-text">' + state.text + '</span>';
            }

            // Destroy init pertama dari baris $('.select2').select2(...), lalu init ulang dengan tags + template.
            $album.select2('destroy');
            $album.select2({
                theme: 'bootstrap-5',
                tags: true,
                allowClear: true,
                placeholder: 'Pilih album…',
                width: '100%',
                escapeMarkup: function (markup) { return markup; },
                templateResult: albumResult,
                templateSelection: albumSelection
            }).on('change', function () {
                // Sync Material outlined floating label (is-filled) ke wrapper input-group-outline.
                var wrap = $album.closest('.input-group-outline')[0];
                if (wrap) {
                    if ($album.val()) wrap.classList.add('is-filled');
                    else wrap.classList.remove('is-filled');
                }
            });
            if (oldAlbum) $album.val(oldAlbum).trigger('change');
        }

        // Terapkan mode sesuai mediaType (untuk edit: kunci; untuk create: tampil sesuai mediaType default).
        applyType(mediaType);

        // Mode create: kalau tab video (default dari ?type=video), panggil applySource untuk render preview video.
        if (!isEdit && mediaType === 'video') {
            applySource();
        }

        var lpForm = document.querySelector('form.lp-ajax');
        if (lpForm) {
            lpForm.addEventListener('submit', function (e) {
                // Set mediaType otomatis sebelum submit
                var hasImage = imageInput && imageInput.files && imageInput.files.length > 0;
                var hasYt = ytUrlInput && ytUrlInput.value.trim() !== '';
                var hasFile = videoFileInput && videoFileInput.files && videoFileInput.files.length > 0;
                var detected;
                if (isEdit) {
                    detected = mediaType;
                } else {
                    detected = hasImage ? 'photo' : ((hasYt || hasFile) ? 'video' : null);
                }
                if (!detected) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                        Swal.fire({ icon: 'error', title: 'Konten belum lengkap', text: 'Isi foto (image) atau video (YouTube URL / file video).' });
                    } else { alert('Isi foto atau video.'); }
                    return false;
                }

                // Validasi untuk create: minimal field judul & 1 input konten
                if (!isEdit) {
                    var title = (getEl('title') || {}).value || '';
                    if (!title.trim()) {
                        e.preventDefault();
                        e.stopImmediatePropagation();
                        if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                            Swal.fire({ icon: 'error', title: 'Judul wajib diisi' });
                        } else { alert('Judul wajib diisi.'); }
                        return false;
                    }
                }

                // Set hidden input sebelum submit
                if (typeInput) typeInput.value = detected;

                // Validasi file video kalau source=local
                if (detected === 'video' && !isEdit) {
                    var checked = document.querySelector('input[name="source"]:checked');
                    var source = checked ? checked.value : 'youtube';
                    if (source === 'local') {
                        var f = getEl('video_file');
                        if (!f || !f.files || !f.files[0]) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                                Swal.fire({ icon: 'error', title: 'File video belum dipilih' });
                            } else { alert('Pilih file video.'); }
                            return false;
                        }
                        var err = validateFileForUpload(f.files[0], VIDEO_ALLOWED_EXT, VIDEO_ALLOWED_MIME, VIDEO_MAX_BYTES, 'video');
                        if (err) {
                            e.preventDefault();
                            e.stopImmediatePropagation();
                            if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                                Swal.fire({ icon: 'error', title: 'File video tidak valid', text: err });
                            } else { alert(err); }
                            return false;
                        }
                        var p = getEl('video_poster');
                        if (p && p.files && p.files[0]) {
                            var posterExt = getExt(p.files[0].name);
                            var posterMime = (p.files[0].type || '').toLowerCase();
                            var posterOk = ['jpg','jpeg','png','webp'].indexOf(posterExt) >= 0 || posterMime.indexOf('image/') === 0;
                            if (!posterOk) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                                    Swal.fire({ icon: 'error', title: 'Poster tidak valid', text: 'Poster harus JPG/PNG/WebP.' });
                                } else { alert('Poster harus JPG/PNG/WebP.'); }
                                return false;
                            }
                            if (p.files[0].size > POSTER_MAX_BYTES) {
                                e.preventDefault();
                                e.stopImmediatePropagation();
                                if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                                    Swal.fire({ icon: 'error', title: 'Poster terlalu besar', text: 'Maks 5MB.' });
                                } else { alert('Poster terlalu besar. Maks 5MB.'); }
                                return false;
                            }
                        }
                    }
                }

                // Semua validasi passed: kunci tombol agar tidak diklik dua kali.
                var submitBtn = getEl('submitBtn');
                if (submitBtn && !submitBtn.disabled) {
                    var icon = submitBtn.querySelector('.lp-submit-icon');
                    var spin = submitBtn.querySelector('.lp-spinner');
                    var label = submitBtn.querySelector('.lp-submit-label');
                    if (icon) icon.classList.add('d-none');
                    if (spin) spin.classList.remove('d-none');
                    if (label) label.textContent = 'Menyimpan…';
                    submitBtn.disabled = true;
                }
            }, true);
        }

        document.querySelectorAll('input[name="source"]').forEach(function(r) {
            r.addEventListener('change', applySource);
        });
        if (ytUrlInput) {
            ytUrlInput.addEventListener('input', refreshYoutubePreview);
            ytUrlInput.addEventListener('change', refreshYoutubePreview);
        }
        if (videoFileInput) {
            videoFileInput.addEventListener('change', refreshLocalPreview);
        }
        var posterInput = getEl('video_poster');
        if (posterInput) posterInput.addEventListener('change', refreshLocalPreview);
    });
})();
</script>
@include('admin-landing._skrip')
@endsection
