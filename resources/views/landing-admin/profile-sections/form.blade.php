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
        $titleSlot = '<h5 class="lp-page-title mb-1">'.e($title).'</h5>'
            .'<p class="text-muted small mb-0">Section <code>'.e($item->section_key).'</code>. Perubahan langsung tampil di halaman publik.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.profile-sections'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" class="lp-ajax">
        @csrf
        @method('PUT')

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'title', 'label' => 'Judul', 'required' => true,
                        'value' => old('title', $item->title), 'colClass' => 'col-md-8',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'subtitle', 'label' => 'Subtitle',
                        'value' => old('subtitle', $item->subtitle), 'colClass' => 'col-md-4',
                    ])
                    @include('landing-admin._components.textarea-input', [
                        'name' => 'content', 'label' => 'Konten',
                        'value' => old('content', $item->content), 'rows' => 8,
                        'inputClass' => 'lp-tinymce',
                        'help' => 'Gunakan editor WYSIWYG di bawah untuk format teks kaya (bold, list, dll).',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'badge_text', 'label' => 'Badge Text (kiri atas)',
                        'placeholder' => 'Akreditasi A',
                        'value' => old('badge_text', $item->badge_text), 'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'badge_icon', 'label' => 'Badge Icon',
                        'placeholder' => 'bi-patch-check-fill',
                        'value' => old('badge_icon', $item->badge_icon), 'colClass' => 'col-md-3',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'badge_extra', 'label' => 'Badge Extra (mis. NPSN)',
                        'placeholder' => '20212345',
                        'value' => old('badge_extra', $item->badge_extra), 'colClass' => 'col-md-3',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'extra_label', 'label' => 'Label untuk Badge Extra',
                        'placeholder' => 'NPSN',
                        'value' => old('extra_label', $item->extra_label), 'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_active', 'label' => 'Aktif (tampilkan di halaman publik)',
                        'checkedDefault' => $item->is_active ?? true,
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
        height: 250,
        menubar: false,
        plugins: 'lists link',
        toolbar: 'undo redo | bold italic underline | bullist numlist | link',
        branding: false,
    });
});
</script>
@include('landing-admin._scripts')
@endsection
