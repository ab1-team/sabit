@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-ann-form-card {
            border: 1px solid #e2e8f0 !important;
            border-radius: .75rem !important;
            background: #fff;
        }
        .lp-ann-form-card > .card-body { padding: .9rem 1.05rem; }

        /* Field: ringkas, label kecil, input seragam */
        .lp-ann-field {
            display: flex;
            flex-direction: column;
            gap: .3rem;
            margin-bottom: .7rem;
        }
        .lp-ann-field > label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
            margin: 0;
        }
        .lp-ann-field .form-control {
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
        .lp-ann-field .form-control:focus {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12) !important;
            outline: none;
        }
        .lp-ann-field .help {
            font-size: .7rem;
            color: #94a3b8;
        }
        .lp-ann-field.req > label::after {
            content: " *";
            color: #dc2626;
        }

        /* Layout 2 kolom via CSS Grid (sejajar posts & galleries) */
        .lp-ann-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
            align-items: stretch;
        }
        @media (min-width: 768px) {
            .lp-ann-grid {
                grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
                gap: 1rem;
            }
        }
        .lp-ann-col {
            display: flex;
            flex-direction: column;
            gap: 0;
            min-width: 0;
        }
        .lp-ann-col-side .lp-ann-field:last-of-type { margin-bottom: 0; }
        .lp-ann-col-side .lp-ann-side-card { margin-top: 0; }

        /* Textarea konten: TinyMCE height tetap seragam */
        .lp-ann-tinymce-wrap .tox-tinymce {
            border-radius: .5rem !important;
            border-color: #d4d8dd !important;
        }
        .lp-ann-tinymce-wrap .tox .tox-toolbar {
            background: #f8fafc !important;
        }
        .lp-ann-tinymce-wrap .tox .tox-edit-area__iframe {
            background: #fff;
        }

        /* Side card: padding tipis, border-top dashed antar blok */
        .lp-ann-side-card {
            display: flex;
            flex-direction: column;
            gap: .7rem;
            padding: .55rem .85rem;
            border-top: 1px dashed #e2e8f0;
            flex: 0 0 auto;
            background: transparent;
        }
        .lp-ann-side-card .lp-ann-field { margin-bottom: 0; }

        /* Switch row (Publish) – ringkas */
        .lp-ann-switch-row {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding-bottom: .15rem;
        }
        .lp-ann-switch-row .lp-ann-switch-text {
            display: flex;
            flex-direction: column;
            line-height: 1.15;
        }
        .lp-ann-switch-row .lp-ann-switch-text strong {
            font-size: .82rem;
            color: #1f2937;
        }
        .lp-ann-switch-row .lp-ann-switch-text small {
            font-size: .7rem;
            color: #64748b;
        }
        .lp-ann-switch-row .form-check.form-switch {
            margin: 0;
            flex: 0 0 auto;
        }

        /* Preview box lampiran: ringkas, full cover saat gambar */
        .lp-ann-preview-wrap {
            display: flex;
            flex-direction: column;
            flex: 0 0 auto;
        }
        .lp-ann-preview-wrap > .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: #334155;
            margin: 0 0 .3rem 0;
        }
        .lp-ann-preview-wrap .lp-preview-box {
            position: relative;
            height: 140px;
            min-height: 140px;
            padding: 0;
            width: 100%;
            border: 2px dashed #cbd5e1;
            border-radius: .65rem;
            background: #f8fafc;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: .35rem;
            overflow: hidden;
            cursor: pointer;
            text-align: center;
            transition: border-color .15s ease, background .15s ease;
        }
        .lp-ann-preview-wrap .lp-preview-box:hover {
            border-color: #1d4ed8;
            background: #fff;
        }
        .lp-ann-preview-wrap .lp-preview-box img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }
        .lp-ann-preview-wrap .lp-preview-box:has(img) .lp-preview-empty,
        .lp-ann-preview-wrap .lp-preview-box:has(img) .lp-preview-hint {
            display: none;
        }
        .lp-ann-preview-wrap .lp-preview-empty {
            font-size: 36px;
            color: #94a3b8;
            line-height: 1;
        }
        .lp-ann-preview-wrap .lp-preview-hint {
            position: absolute;
            bottom: .4rem;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15,23,42,.55);
            color: #fff;
            padding: .2rem .5rem;
            border-radius: .35rem;
            font-size: .72rem;
            font-weight: 500;
            white-space: nowrap;
            z-index: 2;
            opacity: .9;
        }
        .lp-ann-preview-wrap .lp-preview-box:not(:has(img)) .lp-preview-empty {
            position: relative;
            top: auto;
            left: auto;
            transform: none;
        }
        .lp-ann-preview-wrap .lp-preview-box:not(:has(img)) .lp-preview-hint {
            position: relative;
            top: auto;
            left: auto;
            transform: none;
            background: transparent;
            color: #64748b;
            padding: 0;
            font-size: .78rem;
            opacity: 1;
        }
        .lp-ann-preview-wrap .lp-preview-meta {
            font-size: .7rem;
            color: #94a3b8;
            margin-top: .3rem;
        }
        .lp-ann-preview-wrap .lp-preview-meta code {
            color: #475569;
            background: #f1f5f9;
            padding: 1px 5px;
            border-radius: .25rem;
            font-size: .72rem;
        }

        /* Footer form */
        .lp-ann-foot {
            display: flex;
            flex-direction: column;
            gap: .5rem;
            padding: .55rem 1.05rem;
            background: #f8fafc;
            border-radius: 0 0 .75rem .75rem;
        }
        @media (min-width: 768px) {
            .lp-ann-foot {
                flex-direction: row;
                align-items: center;
                justify-content: space-between;
            }
        }
        .lp-ann-foot .lp-foot-hint { font-size: .8rem; color: #475569; }
        .lp-ann-foot .btn {
            min-height: 38px;
            padding: .4rem 1rem;
            border-radius: .5rem;
            font-size: .88rem;
        }

        .lp-ann-page { padding: .35rem .5rem; }
        @media (max-width: 575.98px) {
            .lp-ann-page { padding: .35rem .35rem; }
        }
    </style>
@endsection

@section('content')
<div class="lp-ann-page">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-2">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-2">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @php
        $isEdit = ($announcement->exists ?? false);
        $fileUrl = $announcement->file ? Storage::disk('public')->url('landing/'.$announcement->file) : null;
        $fileExt = $announcement->file ? strtolower(pathinfo($announcement->file, PATHINFO_EXTENSION)) : '';
        $isImageFile = in_array($fileExt, ['jpg','jpeg','png','webp']);
    @endphp

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax lp-ann-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card my-2 lp-ann-form-card">
            <div class="card-body">
                <div class="lp-ann-grid">
                    {{-- KOLOM KIRI --}}
                    <div class="lp-ann-col">
                        <div class="lp-ann-field req">
                            <label for="title">Judul</label>
                            <input id="title" type="text" name="title" class="form-control" required maxlength="200"
                                   value="{{ old('title', $announcement->title) }}"
                                   placeholder="mis. Libur Nasional 17 Agustus">
                        </div>

                        <div class="lp-ann-field req lp-ann-tinymce-wrap">
                            <label for="content">Isi Pengumuman</label>
                            <textarea id="content" name="content" class="form-control lp-tinymce" rows="6"
                                      placeholder="Tulis isi pengumuman di sini...">{{ old('content', $announcement->content) }}</textarea>
                        </div>
                    </div>

                    {{-- KOLOM KANAN --}}
                    <div class="lp-ann-col lp-ann-col-side">
                        <div class="lp-ann-field">
                            <label for="published_at">Tanggal Publikasi</label>
                            <input id="published_at" type="text" name="published_at" class="form-control lp-date"
                                   value="{{ old('published_at', $announcement->published_at?->format('Y-m-d H:i')) }}"
                                   placeholder="YYYY-MM-DD HH:MM">
                            <div class="help">Kosongkan untuk otomatis menggunakan waktu saat ini.</div>
                        </div>

                        <div class="lp-ann-side-card">
                            <div class="lp-ann-preview-wrap">
                                <label class="form-label">Lampiran (opsional)</label>
                                <label for="fileInput" class="lp-preview-box d-block" id="filePreviewBox">
                                    @if ($fileUrl && $isImageFile)
                                        <img src="{{ $fileUrl }}" alt="Lampiran" id="filePreviewImg">
                                    @else
                                        <span class="material-symbols-rounded lp-preview-empty" id="filePreviewEmpty">upload_file</span>
                                    @endif
                                    <span class="lp-preview-hint">
                                        {{ $announcement->file ? 'Klik untuk ganti lampiran' : 'Klik untuk pilih lampiran' }}
                                    </span>
                                </label>
                                <input type="file" name="file" class="d-none"
                                       accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" id="fileInput">
                                @if ($announcement->file)
                                    <div class="lp-preview-meta">File saat ini: <code>{{ $announcement->file }}</code></div>
                                @else
                                    <div class="lp-preview-meta">PDF, DOC, DOCX, JPG, PNG (maks 4MB).</div>
                                @endif
                            </div>

                            <div class="lp-ann-field">
                                <label>Status Publikasi</label>
                                <div class="lp-ann-switch-row">
                                    <div class="form-check form-switch m-0">
                                        <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublished"
                                               {{ old('is_published', $announcement->is_published ?? true) ? 'checked' : '' }}>
                                    </div>
                                    <div class="lp-ann-switch-text">
                                        <strong>Publish pengumuman ini</strong>
                                        <small>Jika nonaktif, pengumuman tersimpan sebagai draft.</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lp-ann-foot border-top">
                <span class="lp-foot-hint">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('app.admin-landing.announcements') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">arrow_back</span>
                        <span class="align-middle">Kembali</span>
                    </a>
                    <button type="submit" class="btn btn-primary d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                        <span class="align-middle">{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Pengumuman' }}</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
@include('admin-landing._skrip')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // TinyMCE untuk isi pengumuman (tinggi tetap, ringkas)
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: 'textarea.lp-tinymce',
            height: 240,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link image table | code',
            branding: false,
            content_style: 'body { font-family: "Inter", sans-serif; font-size: 14px; }',
        });
    }

    // Preview lampiran (gambar & non-gambar)
    const fileInput = document.getElementById('fileInput');
    const previewBox = document.getElementById('filePreviewBox');
    const previewEmpty = document.getElementById('filePreviewEmpty');
    const previewHint = previewBox ? previewBox.querySelector('.lp-preview-hint') : null;

    function showPreview(file) {
        if (!previewBox) return;
        let img = previewBox.querySelector('img#filePreviewImg');
        if (file && file.type && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function (ev) {
                if (!img) {
                    img = document.createElement('img');
                    img.id = 'filePreviewImg';
                    previewBox.insertBefore(img, previewBox.firstChild);
                }
                img.src = ev.target.result;
                img.style.display = '';
            };
            reader.readAsDataURL(file);
        } else if (img) {
            img.style.display = 'none';
        }
        if (previewEmpty) previewEmpty.style.display = file && file.type && file.type.startsWith('image/') ? 'none' : '';
        if (previewHint) previewHint.textContent = 'Klik untuk ganti lampiran';
    }

    if (fileInput && previewBox) {
        fileInput.addEventListener('change', function (e) {
            const f = e.target.files && e.target.files[0];
            if (f) showPreview(f);
        });
    }

    // Flatpickr tanggal
    if (window.flatpickr) {
        flatpickr('input.lp-date', {
            enableTime: true,
            dateFormat: 'Y-m-d H:i',
            time_24hr: true,
            allowInput: true,
        });
    }
});
</script>
@endsection
