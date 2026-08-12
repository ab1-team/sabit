@extends('layouts.tenant.base')

@section('style')
    @include('landing-admin._styles')
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
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.pages'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="lp-ajax">
        @csrf
        @if (($page->exists ?? false))
            @method('PUT')
        @endif

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'title', 'label' => 'Judul', 'required' => true,
                        'value' => old('title', $page->title), 'colClass' => 'col-md-8',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'slug', 'label' => 'Slug',
                        'placeholder' => 'otomatis dari judul',
                        'value' => old('slug', $page->slug), 'colClass' => 'col-md-4',
                        'help' => 'Kosongkan untuk generate otomatis dari judul.',
                    ])
                    @include('landing-admin._components.textarea-input', [
                        'name' => 'content', 'label' => 'Konten', 'required' => true,
                        'value' => old('content', $page->content), 'rows' => 14,
                        'inputClass' => 'lp-tinymce',
                    ])
                    @include('landing-admin._components.file-input', [
                        'name' => 'image', 'label' => 'Gambar Sampul',
                        'current' => $page->image,
                        'currentUrl' => $page->image ? Storage::disk('public')->url('landing/'.$page->image) : null,
                        'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_published', 'label' => 'Publish',
                        'checkedDefault' => $page->is_published ?? true,
                        'inputId' => 'is_pub_page', 'colClass' => 'col-md-6',
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
        height: 400,
        menubar: false,
        plugins: 'lists link image table code',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link image table | code',
        branding: false,
    });
});
</script>
@include('landing-admin._scripts')
@endsection
