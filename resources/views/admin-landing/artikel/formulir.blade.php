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
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Isi judul, ringkasan, dan konten artikel.</p>';
    @endphp
    @include('admin-landing._header-halaman', [
        'subtitle' => 'Landing Page',
        'back' => route('app.admin-landing.posts'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if (($post->exists ?? false))
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'title', 'label' => 'Judul', 'required' => true,
                        'value' => old('title', $post->title), 'colClass' => 'col-md-8',
                    ])
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'category', 'label' => 'Kategori',
                        'placeholder' => 'STEM, Seni, ...',
                        'value' => old('category', $post->category), 'colClass' => 'col-md-4',
                    ])
                    @include('admin-landing._komponen.input-teksarea', [
                        'name' => 'excerpt', 'label' => 'Ringkasan (Excerpt)',
                        'value' => old('excerpt', $post->excerpt), 'rows' => 2,
                    ])
                    @include('admin-landing._komponen.input-teksarea', [
                        'name' => 'content', 'label' => 'Konten', 'required' => true,
                        'value' => old('content', $post->content), 'rows' => 12,
                        'inputClass' => 'lp-tinymce',
                    ])
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'tags', 'label' => 'Tag',
                        'placeholder' => 'pisahkan dengan koma',
                        'value' => old('tags', $post->tags), 'colClass' => 'col-md-6',
                        'help' => 'Pisahkan dengan koma.',
                    ])
                    @include('admin-landing._komponen.input-teks', [
                        'name' => 'published_at', 'label' => 'Tanggal Publikasi',
                        'value' => old('published_at', $post->published_at?->format('Y-m-d H:i')),
                        'colClass' => 'col-md-6', 'inputClass' => 'lp-date',
                    ])
                    @include('admin-landing._komponen.input-file', [
                        'name' => 'image', 'label' => 'Gambar Sampul',
                        'current' => $post->image,
                        'currentUrl' => $post->image ? Storage::disk('public')->url('landing/'.$post->image) : null,
                        'colClass' => 'col-md-6',
                    ])
                    @include('admin-landing._komponen.input-saklar', [
                        'name' => 'is_published', 'label' => 'Publish',
                        'checkedDefault' => $post->is_published ?? true,
                        'colClass' => 'col-md-3',
                    ])
                    @include('admin-landing._komponen.input-saklar', [
                        'name' => 'is_featured', 'label' => 'Tampilkan di Beranda',
                        'checkedDefault' => $post->is_featured ?? false,
                        'colClass' => 'col-md-3',
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
        height: 350,
        menubar: false,
        plugins: 'lists link image table code',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link image table | code',
        branding: false,
    });
});
</script>
@include('admin-landing._skrip')
@endsection
