@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-vid-form-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            background: #fff;
        }
        .lp-vid-form-card > .card-body { padding: .9rem 1.05rem; }

        .lp-field {
            display: flex;
            flex-direction: column;
            gap: .3rem;
            margin-bottom: .7rem;
        }
        .lp-field > label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
            margin: 0;
        }
        .lp-field .form-control,
        .lp-field textarea.form-control {
            height: 38px;
            padding: .45rem .7rem;
            border-radius: .5rem;
            border: 1px solid #d4d8dd;
            background: #fff;
            color: #1f2937;
            font-size: .9rem;
            transition: border-color .15s ease, box-shadow .15s ease;
            box-shadow: none !important;
        }
        .lp-field textarea.form-control {
            height: auto;
            min-height: 110px;
            line-height: 1.5;
            resize: vertical;
        }
        .lp-field .form-control:focus,
        .lp-field textarea.form-control:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
            outline: none;
        }
        .lp-field .help {
            font-size: .7rem;
            color: #94a3b8;
        }
        .lp-field.req > label::after {
            content: " *";
            color: #dc2626;
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

        /* Preview video */
        .lp-vid-preview-wrap {
            display: flex;
            flex-direction: column;
            flex: 0 0 auto;
            margin-bottom: .7rem;
        }
        .lp-vid-preview-wrap > .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
            margin: 0 0 .3rem 0;
        }
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
        .lp-vid-preview-box .lp-vid-preview-empty-text {
            font-size: .78rem;
            color: rgba(255,255,255,.7);
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
        .lp-publish-card .lp-switch-text strong {
            font-size: .82rem;
            color: #1f2937;
        }
        .lp-publish-card .lp-switch-text small {
            font-size: .7rem;
            color: #64748b;
        }
        .lp-publish-card .form-check.form-switch {
            margin: 0;
            flex: 0 0 auto;
        }

        .lp-vid-foot {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            padding: .55rem 1.05rem;
            background: #f8fafc;
            border-radius: 0 0 .75rem .75rem;
        }
        @media (min-width: 768px) {
            .lp-vid-foot {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .lp-vid-foot .lp-foot-hint { font-size: .8rem; color: #475569; }
        .lp-vid-foot .btn {
            min-height: 38px;
            padding: .4rem 1rem;
            border-radius: .5rem;
            font-size: .88rem;
        }

        .lp-vid-page { padding: .35rem .5rem; }
        @media (max-width: 575.98px) {
            .lp-vid-page { padding: .35rem .35rem; }
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
        .lp-radio-pill .material-symbols-rounded {
            font-size: 18px;
            color: #64748b;
        }
        .lp-radio-pill:hover { border-color: #94a3b8; background: #f8fafc; }
        .lp-radio-pill:has(input:checked) {
            border-color: #1d4ed8;
            background: #eff6ff;
            color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.10);
        }
        .lp-radio-pill:has(input:checked) .material-symbols-rounded { color: #1d4ed8; }

        #videoPreviewVideo {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #0f172a;
        }
    </style>
@endsection

@section('content')
<div class="lp-vid-page">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-2">{{ session('success') }}</div>
    @endif
    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger py-2 small mb-2">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif
    <div class="alert alert-danger py-2 small mb-2 lp-ajax-errors" style="display:none;" role="alert">
        <strong class="lp-ajax-errors-title d-block mb-1">Data belum lengkap:</strong>
        <ul class="mb-0 ps-3 lp-ajax-errors-list"></ul>
    </div>

    @php
        $isEdit = ($video->exists ?? false);
        $currentSource = old('source', $video->source ?: ($video->file_path ? 'local' : 'youtube'));
        $currentPoster = $video->poster ? \Illuminate\Support\Facades\Storage::disk('public')->url($video->poster) : null;
    @endphp

    <form action="{{ $action }}" method="POST" class="lp-ajax" enctype="multipart/form-data">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="card my-2 lp-vid-form-card">
            <div class="card-body">
                <div class="lp-form-grid">
                    {{-- KOLOM KIRI --}}
                    <div class="lp-form-col">
                        <div class="lp-field req">
                            <label for="title">Judul Video</label>
                            <input id="title" type="text" name="title" class="form-control"
                                   value="{{ old('title', $video->title) }}"
                                   placeholder="mis. Profil Sekolah 2026" required>
                        </div>

                        <div class="lp-field req">
                            <label>Sumber Video</label>
                            <div class="d-flex flex-wrap gap-3 align-items-center" role="radiogroup" aria-label="Sumber video">
                                <label class="lp-radio-pill">
                                    <input type="radio" name="source" value="youtube" {{ $currentSource === 'youtube' ? 'checked' : '' }}>
                                    <span class="material-symbols-rounded">smart_display</span>
                                    <span>YouTube</span>
                                </label>
                                <label class="lp-radio-pill">
                                    <input type="radio" name="source" value="local" {{ $currentSource === 'local' ? 'checked' : '' }}>
                                    <span class="material-symbols-rounded">movie</span>
                                    <span>Upload File</span>
                                </label>
                            </div>
                            <small class="help">Pilih YouTube untuk embed video daring, atau Upload File untuk video lokal (mp4/webm/mov, maks 50MB).</small>
                        </div>

                        <div class="lp-field req" id="ytUrlField">
                            <label for="youtube_url">URL YouTube</label>
                            <input id="youtube_url" type="url" name="youtube_url" class="form-control"
                                   value="{{ old('youtube_url', $video->youtube_url) }}"
                                   placeholder="https://www.youtube.com/watch?v=... atau https://youtu.be/...">
                            <small class="help">Bisa paste URL lengkap (watch?v=, youtu.be, shorts, atau embed). Sistem akan otomatis dikonversi ke format embed.</small>
                        </div>

                        <div class="lp-field" id="localFileField" style="display:none;">
                            <label for="video_file">File Video</label>
                            <input id="video_file" type="file" name="video_file" class="form-control"
                                   accept="video/mp4,video/webm,video/quicktime,video/x-matroska,video/x-m4v,.mp4,.m4v,.mov,.webm,.mkv">
                            <small class="help">Format: MP4, M4V, MOV (iPhone), WebM, atau MKV. Maks 50MB. {{ $isEdit && $video->file_path ? 'Kosongkan jika tidak ingin mengganti.' : '' }}</small>
                            @if ($isEdit && $video->file_path)
                                <div class="small text-muted mt-1" id="currentFileLabel">
                                    File saat ini: <span class="font-monospace">{{ $video->file_path }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="lp-field" id="localPosterField" style="display:none;">
                            <label for="video_poster">Poster (Opsional)</label>
                            <input id="video_poster" type="file" name="video_poster" class="form-control"
                                   accept="image/jpeg,image/png,image/webp">
                            <small class="help">Gambar thumbnail untuk video lokal. JPG/PNG/WebP, maks 5MB.</small>
                            @if ($isEdit && $video->poster)
                                <div class="small text-muted mt-1">
                                    Poster saat ini: <span class="font-monospace">{{ $video->poster }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="lp-field">
                            <label for="description">Deskripsi</label>
                            <textarea id="description" name="description" class="form-control" rows="4"
                                      placeholder="Keterangan singkat tentang video (opsional)">{{ old('description', $video->description) }}</textarea>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="lp-form-col lp-form-col-side">
                        <div class="lp-vid-preview-wrap">
                            <label class="form-label">Preview</label>
                            <div class="lp-vid-preview-box" id="videoPreviewBox">
                                <span class="lp-vid-preview-empty" id="videoPreviewEmpty">
                                    <span class="material-symbols-rounded">smart_display</span>
                                    <div class="lp-vid-preview-empty-text">Belum ada video</div>
                                </span>
                                <span class="material-symbols-rounded lp-vid-play-overlay" id="videoPlayOverlay" style="display:none;">play_circle</span>
                                <img id="videoPreviewImg" src="" alt="" style="display:none;">
                                <video id="videoPreviewVideo" controls style="display:none;"></video>
                            </div>
                            <div class="small text-muted mt-1" id="videoPreviewUrl"></div>
                        </div>

                        <div class="lp-publish-card">
                            <div class="lp-field lp-field-switch">
                                <div class="lp-switch-inline">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1"
                                               id="is_pub_video" {{ ($video->is_published ?? true) ? 'checked' : '' }}>
                                    </div>
                                    <div class="lp-switch-text">
                                        <strong>Publish</strong>
                                        <small>Tampilkan di halaman publik.</small>
                                    </div>
                                </div>
                            </div>
                            <div class="help w-100 mt-1 mb-0">Video yang di-publish akan muncul di halaman /video.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lp-vid-foot border-top">
                <span class="lp-foot-hint">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('app.admin-landing.galleries') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">arrow_back</span>
                        <span class="align-middle">Kembali ke Galeri</span>
                    </a>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="align-middle">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Video' }}</span>
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

    function $(id) { return document.getElementById(id); }

    function showOnly(which) {
        var img = $('videoPreviewImg');
        var vid = $('videoPreviewVideo');
        var empty = $('videoPreviewEmpty');
        var overlay = $('videoPlayOverlay');
        img.style.display = 'none';
        vid.style.display = 'none';
        if (overlay) overlay.style.display = 'none';
        if (which === 'img') img.style.display = 'block';
        else if (which === 'video') vid.style.display = 'block';
        else if (which === 'empty') { if (empty) empty.style.display = ''; }
        if (which !== 'empty' && empty) empty.style.display = 'none';
    }

    function refreshYoutubePreview() {
        var url = ($('youtube_url') || {}).value || '';
        var id = extractYoutubeId(url);
        var img = $('videoPreviewImg');
        var overlay = $('videoPlayOverlay');
        var caption = $('videoPreviewUrl');

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
        var fileInput = $('video_file');
        var posterInput = $('video_poster');
        var vid = $('videoPreviewVideo');
        var overlay = $('videoPlayOverlay');
        var caption = $('videoPreviewUrl');

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
            vid.removeAttribute('src');
            if (caption) caption.textContent = 'Belum ada file dipilih.';
        }
    }

    function applySource() {
        var checked = document.querySelector('input[name="source"]:checked');
        var source = checked ? checked.value : 'youtube';
        var yt = $('ytUrlField');
        var lf = $('localFileField');
        var lp = $('localPosterField');
        var caption = $('videoPreviewUrl');

        if (source === 'youtube') {
            if (yt) yt.style.display = '';
            if (lf) lf.style.display = 'none';
            if (lp) lp.style.display = 'none';
            refreshYoutubePreview();
        } else {
            if (yt) yt.style.display = 'none';
            if (lf) lf.style.display = '';
            if (lp) lp.style.display = '';
            refreshLocalPreview();
        }
    }

    // Batas & ekstensi yang diizinkan — disinkronkan dengan validator di
    // controller (lihat AdminLandingController::handleVideoStore/Update).
    var VIDEO_MAX_BYTES = 50 * 1024 * 1024;   // 50MB
    var POSTER_MAX_BYTES = 5 * 1024 * 1024;   // 5MB
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
            || (mime && mime.indexOf('video/') === 0)
            || (mime && mime.indexOf('image/') === 0);
        if (!okExt && !okMime) {
            return 'Format ' + label + ' tidak didukung. Ekstensi: ' + (ext || '(none)') + ', MIME: ' + (mime || '(none)') + '.';
        }
        return null;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Validasi client-side SEBELUM submit, agar user dapat pesan jelas
        // tanpa harus round-trip ke server. Ini khusus untuk file besar
        // yang sering gagal di Windows (.mov iPhone, .mkv).
        var lpForm = document.querySelector('form.lp-ajax');
        if (lpForm) {
            lpForm.addEventListener('submit', function (e) {
                var checked = document.querySelector('input[name="source"]:checked');
                var source = checked ? checked.value : 'youtube';
                if (source !== 'local') return;
                var f = $('video_file');
                if (!f || !f.files || !f.files[0]) return; // biarkan validator server yang handle required
                var err = validateFileForUpload(f.files[0], VIDEO_ALLOWED_EXT, VIDEO_ALLOWED_MIME, VIDEO_MAX_BYTES, 'video');
                if (err) {
                    e.preventDefault();
                    e.stopImmediatePropagation();
                    if (typeof Swal !== 'undefined' && Swal && Swal.fire) {
                        Swal.fire({ icon: 'error', title: 'File video tidak valid', text: err });
                    } else {
                        alert(err);
                    }
                    return false;
                }
                var p = $('video_poster');
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
            }, true); // capture phase: jalan SEBELUM handler .lp-ajax
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[name="source"]').forEach(function(r) {
            r.addEventListener('change', applySource);
        });
        var ytInput = $('youtube_url');
        if (ytInput) {
            ytInput.addEventListener('input', refreshYoutubePreview);
            ytInput.addEventListener('change', refreshYoutubePreview);
        }
        var fileInput = $('video_file');
        if (fileInput) {
            fileInput.addEventListener('change', refreshLocalPreview);
        }
        var posterInput = $('video_poster');
        if (posterInput) {
            posterInput.addEventListener('change', refreshLocalPreview);
        }
        applySource();
    });
})();
</script>
@include('admin-landing._skrip')
@endsection
