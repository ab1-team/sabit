@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
    <style>
        .lp-ann-form .card-body { padding: 1rem 1.1rem; }
        .lp-ann-form .input-group input[type="file"] { display: none; }
        .lp-ann-form .lp-preview-box {
            width: 100%;
            height: 140px;
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
            transition: border-color .15s ease, background .15s ease;
            text-align: center;
            padding: .5rem;
        }
        .lp-ann-form .lp-preview-box:hover {
            border-color: #37d17c;
            background: #fff;
        }
        .lp-ann-form .lp-preview-box img {
            max-width: 70%;
            max-height: 70%;
            object-fit: contain;
        }
        .lp-ann-form .lp-preview-empty {
            font-size: 38px;
            color: #94a3b8;
            line-height: 1;
        }
        .lp-ann-form .lp-preview-hint {
            font-size: .78rem;
            color: #64748b;
            font-weight: 500;
        }
        .lp-ann-form .lp-preview-box:hover .lp-preview-hint { color: #1f9d57; }
        .lp-ann-form .lp-preview-meta {
            font-size: .72rem;
            color: #94a3b8;
            text-align: center;
            margin-top: .35rem;
        }
        .lp-ann-form .lp-preview-meta code {
            color: #475569;
            background: #f1f5f9;
            padding: 1px 5px;
            border-radius: .25rem;
            font-size: .72rem;
        }
        .lp-ann-foot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: .75rem;
            flex-wrap: wrap;
            padding: .75rem 1rem;
            border-top: 1px dashed #e2e8f0;
        }
        .lp-ann-foot small { color: #64748b; }
    </style>
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger py-2 small mb-3">
            <ul class="mb-0 ps-3">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @php
        $isEdit = ($announcement->exists ?? false);
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">'.($isEdit ? 'Perbarui pengumuman yang sudah ada.' : 'Tambahkan pengumuman baru untuk ditampilkan di halaman publik.').'</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.announcements'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax lp-ann-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <div class="input-group input-group-outline @if (old('title', $announcement->title)) is-filled @endif">
                            <label class="form-label">Judul <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="form-control" required maxlength="200"
                                   value="{{ old('title', $announcement->title) }}">
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold d-block mb-1">Isi Pengumuman <span class="text-danger">*</span></label>
                        <textarea name="content" rows="10" class="form-control lp-tinymce" required>{{ old('content', $announcement->content) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group input-group-outline @if (old('published_at', $announcement->published_at?->format('Y-m-d H:i'))) is-filled @endif">
                            <label class="form-label">Tanggal Publikasi</label>
                            <input type="text" name="published_at" class="form-control lp-date"
                                   value="{{ old('published_at', $announcement->published_at?->format('Y-m-d H:i')) }}">
                        </div>
                        <small class="text-muted d-block mt-1">Kosongkan untuk otomatis menggunakan waktu sekarang.</small>

                        <div class="form-check form-switch d-flex align-items-center gap-2 ps-0 mt-4">
                            <input class="form-check-input ms-0" type="checkbox" name="is_published" value="1" id="isPublished"
                                   {{ old('is_published', $announcement->is_published ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="isPublished">Publish pengumuman ini</label>
                        </div>
                        <small class="text-muted d-block ms-4 ps-1">Jika nonaktif, pengumuman tersimpan sebagai draft.</small>
                    </div>
                    @php
                        $fileAccept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
                        $fileUrl = $announcement->file ? Storage::disk('public')->url('landing/'.$announcement->file) : null;
                        $fileExt = $announcement->file ? strtolower(pathinfo($announcement->file, PATHINFO_EXTENSION)) : '';
                        $isImageFile = in_array($fileExt, ['jpg','jpeg','png']);
                    @endphp
                    <div class="col-md-6">
                        <label class="form-label small fw-bold d-block mb-1">Lampiran (opsional)</label>
                        <label for="fileInput" class="lp-preview-box" id="filePreviewBox">
                            @if ($fileUrl && $isImageFile)
                                <img src="{{ $fileUrl }}" alt="Lampiran" id="filePreviewImg">
                            @else
                                <span class="material-symbols-rounded lp-preview-empty" id="filePreviewEmpty">upload_file</span>
                            @endif
                            <span class="lp-preview-hint">{{ $announcement->file ? 'Klik untuk ganti lampiran' : 'Klik untuk pilih lampiran' }}</span>
                        </label>
                        <input type="file" name="file" class="d-none" accept="{{ $fileAccept }}" id="fileInput">
                        @if ($announcement->file)
                            <div class="lp-preview-meta">File saat ini: <code>{{ $announcement->file }}</code></div>
                        @endif
                        <div class="lp-preview-meta">PDF, DOC, DOCX, JPG, PNG (maks 4MB).</div>
                    </div>
                </div>

                <div class="lp-ann-foot">
                    <small>Kolom bertanda <span class="text-danger">*</span> wajib diisi.</small>
                    <button type="submit" class="btn btn-info d-inline-flex align-items-center gap-1">
                        <span class="material-symbols-rounded" style="font-size:18px;">save</span>
                        Simpan Pengumuman
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
    // TinyMCE untuk isi pengumuman
    if (typeof tinymce !== 'undefined') {
        tinymce.init({
            selector: 'textarea.lp-tinymce',
            height: 320,
            menubar: false,
            plugins: 'lists link image table code',
            toolbar: 'undo redo | bold italic underline | bullist numlist | link image table | code',
            branding: false,
            content_style: 'body { font-family: "Inter", sans-serif; font-size: 14px; }',
        });
    }

    // Preview lampiran (gambar)
    const fileInput = document.getElementById('fileInput');
    const previewBox = document.getElementById('filePreviewBox');
    const previewImg = document.getElementById('filePreviewImg');
    const previewEmpty = document.getElementById('filePreviewEmpty');
    const previewHint = previewBox ? previewBox.querySelector('.lp-preview-hint') : null;

    if (fileInput && previewBox) {
        fileInput.addEventListener('change', function (e) {
            const f = e.target.files && e.target.files[0];
            if (!f) return;
            if (previewImg) {
                if (f.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        previewImg.src = ev.target.result;
                        previewImg.style.display = '';
                    };
                    reader.readAsDataURL(f);
                } else {
                    previewImg.style.display = 'none';
                }
            } else {
                const img = document.createElement('img');
                img.id = 'filePreviewImg';
                if (f.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function (ev) {
                        img.src = ev.target.result;
                    };
                    reader.readAsDataURL(f);
                } else {
                    img.style.display = 'none';
                }
                previewBox.insertBefore(img, previewBox.firstChild);
            }
            if (previewEmpty) previewEmpty.style.display = 'none';
            if (previewHint) previewHint.textContent = 'Klik untuk ganti lampiran';
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
