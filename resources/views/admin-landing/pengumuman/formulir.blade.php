@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
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
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.announcements'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if (($announcement->exists ?? false))
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'title', 'label' => 'Judul', 'required' => true,
                        'value' => old('title', $announcement->title), 'colClass' => 'col-12',
                    ])
                    @include('admin-landing._komponen.input-teksarea', [
                        'name' => 'content', 'label' => 'Isi Pengumuman', 'required' => true,
                        'value' => old('content', $announcement->content), 'rows' => 10,
                        'inputClass' => 'lp-tinymce',
                    ])
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'published_at', 'label' => 'Tanggal Publikasi',
                        'value' => old('published_at', $announcement->published_at?->format('Y-m-d H:i')),
                        'colClass' => 'col-md-6', 'inputClass' => 'lp-date',
                    ])
                    @php
                        $fileAccept = '.pdf,.doc,.docx,.jpg,.jpeg,.png';
                        $fileUrl = $announcement->file ? Storage::disk('public')->url('landing/'.$announcement->file) : null;
                        $fileExt = $announcement->file ? strtolower(pathinfo($announcement->file, PATHINFO_EXTENSION)) : '';
                        $isImageFile = in_array($fileExt, ['jpg','jpeg','png']);
                    @endphp
                    <div class="col-md-6">
                        <label class="form-label small fw-bold d-block">Lampiran (opsional)</label>
                        <label for="fileInput" class="lp-preview-box d-block" id="filePreviewBox">
                            @if ($fileUrl && $isImageFile)
                                <img src="{{ $fileUrl }}" alt="Lampiran" id="filePreviewImg">
                            @else
                                <span class="material-symbols-rounded lp-preview-empty" id="filePreviewEmpty">upload_file</span>
                            @endif
                            <span class="lp-preview-hint">{{ $announcement->file ? 'Klik untuk ganti lampiran' : 'Klik untuk pilih lampiran' }}</span>
                        </label>
                        <input type="file" name="file" class="d-none"
                               accept="{{ $fileAccept }}" id="fileInput">
                        @if ($announcement->file)
                            <div class="small text-muted mt-1 text-center">File saat ini: <code>{{ $announcement->file }}</code></div>
                        @endif
                        <div class="small text-muted mt-1 text-center">PDF, DOC, DOCX, JPG, PNG (maks 4MB).</div>
                    </div>
                    @include('admin-landing._komponen.input-saklar', [
                        'name' => 'is_published', 'label' => 'Publish',
                        'checkedDefault' => $announcement->is_published ?? true,
                    ])
                </div>
            </div>
        </div>

        <div class="card my-3 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 pb-1">
                <span class="fw-bold" style="font-size: 14px;">
                    Isi semua kolom bertanda <span class="text-danger">*</span>.
                </span>
                <button type="submit" class="btn btn-info w-100 w-md-auto mb-1">
                    <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof tinymce === 'undefined') return;
    tinymce.init({
        selector: 'textarea.lp-tinymce',
        height: 300,
        menubar: false,
        plugins: 'lists link image table code',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link image table | code',
        branding: false,
    });
});
</script>
@include('admin-landing._skrip')
@endsection
