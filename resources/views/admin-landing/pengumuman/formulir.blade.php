@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        /* Bungkus TinyMCE di dalam .input-group-outline supaya pojok konsisten
           melengkung + ada border menyatu (sama dengan artikel). */
        .lp-ann-tinymce-wrap.input-group.input-group-outline {
            border: 1px solid #d4d8dd;
            border-radius: .5rem;
            overflow: hidden;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .lp-ann-tinymce-wrap.input-group.input-group-outline.is-filled,
        .lp-ann-tinymce-wrap.input-group.input-group-outline:focus-within {
            border-color: #1d4ed8;
            box-shadow: 0 0 0 3px rgba(29,78,216,.12);
        }
        .lp-ann-tinymce-wrap.input-group.input-group-outline .tox-tinymce {
            border: none !important;
            border-radius: 0 !important;
        }
        .lp-ann-tinymce-wrap.input-group.input-group-outline .tox .tox-toolbar__primary {
            background: #f8fafc !important;
        }
        .lp-ann-tinymce-wrap.input-group.input-group-outline .tox .tox-edit-area__iframe {
            background: #fff;
        }

        /* Kotak lampiran: tinggi ringkas, override aspect-ratio default 1:1 */
        .lp-ann-preview-wrap .lp-preview-box {
            height: 160px;
            min-height: 160px;
            aspect-ratio: auto;
        }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
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
                <div class="row g-3 align-items-start">
                    <div class="col-md-8 d-flex flex-column">
                        <div class="input-group input-group-outline mb-2 @if(old('title', $announcement->title)) is-filled @endif">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input id="title" type="text" name="title" class="form-control" required maxlength="200"
                                   value="{{ old('title', $announcement->title) }}">
                        </div>

                        <div class="input-group input-group-outline mb-0 @if(old('content', $announcement->content)) is-filled @endif lp-ann-tinymce-wrap flex-grow-1 d-flex flex-column">
                            <label class="form-label">Isi Pengumuman <span class="text-danger">*</span></label>
                            <textarea id="content" name="content" class="form-control lp-tinymce flex-grow-1">{{ old('content', $announcement->content) }}</textarea>
                        </div>
                    </div>

                    <div class="col-md-4 d-flex flex-column">
                        <div class="input-group input-group-outline mb-2 @if(old('published_at', $announcement->published_at?->format('Y-m-d'))) is-filled @endif">
                            <label class="form-label">Tanggal Publikasi</label>
                            <input id="published_at" type="text" name="published_at" class="form-control datepicker"
                                   value="{{ old('published_at', $announcement->published_at?->format('Y-m-d')) }}">
                        </div>

                        <div class="lp-ann-side-card flex-grow-1">
                            <div class="lp-ann-preview-wrap mb-0">
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

                            <div class="form-check form-switch m-0 mt-2">
                                <input class="form-check-input" type="checkbox" name="is_published" value="1" id="isPublished"
                                       {{ old('is_published', $announcement->is_published ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isPublished">Publish pengumuman ini (jika nonaktif, tersimpan sebagai draft)</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body border-top d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 pb-1">
                <span class="fw-bold" style="font-size: 14px;">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <div class="d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('app.admin-landing.announcements') }}" class="btn btn-light d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded align-middle" style="font-size:18px;">arrow_back</span>
                        <span class="align-middle">Kembali</span>
                    </a>
                    <button type="submit" class="btn btn-info d-inline-flex align-items-center gap-1">
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
    // Flatpickr datepicker (gaya siswa)
    if (window.flatpickr) {
        flatpickr('.datepicker', { dateFormat: "Y-m-d" });
        $('.datepicker').on('change', function () {
            $(this).closest('.input-group').addClass('is-filled');
        });
    }

    // TinyMCE untuk isi pengumuman (auto-grow, tanpa scroll internal)
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: 'textarea.lp-tinymce',
            min_height: 220,
            height: 'auto',
            autoresize_bottom_margin: 8,
            menubar: false,
            plugins: 'lists link image table code autoresize',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link image table | code',
            branding: false,
            content_style: 'body { font-family: "Inter", sans-serif; font-size: 14px; }',
        });
    }
});
</script>
@endsection
