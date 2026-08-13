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
            .'<p class="text-muted small mb-0">Atur judul, subjudul, dan tombol halaman publik PPDB (<code>/ppdb</code>). Teks di sini khusus halaman PPDB, tidak terkait dengan section CTA PPDB di beranda.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'titleSlot' => $titleSlot,
    ])

    <form action="{{ $action }}" method="POST" class="lp-ajax">
        @csrf

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="material-symbols-rounded text-primary">campaign</span>
                    <h6 class="mb-0 fw-bold">Hero & Tombol Halaman PPDB</h6>
                </div>
                <p class="text-muted small mb-3">Teks yang tampil di hero halaman publik <code>/ppdb</code>. Berbeda dengan <strong>CTA PPDB</strong> yang muncul di section beranda.</p>
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'school_name', 'label' => 'Nama Sekolah (header)',
                        'value' => old('school_name', $ppdb->school_name),
                        'colClass' => 'col-md-6',
                        'placeholder' => $setting->school_name ?? 'Nama Sekolah',
                        'help' => 'Kosongkan untuk otomatis pakai nama sekolah dari Identitas.',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'eyebrow', 'label' => 'Eyebrow (kecil di atas judul)',
                        'value' => old('eyebrow', $ppdb->eyebrow),
                        'colClass' => 'col-md-6',
                        'placeholder' => 'Penerimaan Peserta Didik Baru',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'title', 'label' => 'Judul Utama', 'required' => true,
                        'value' => old('title', $ppdb->title),
                        'colClass' => 'col-md-12',
                        'placeholder' => 'Penerimaan Peserta Didik Baru 2026/2027',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'subtitle', 'label' => 'Subjudul',
                        'type' => 'textarea', 'rows' => 3,
                        'value' => old('subtitle', $ppdb->subtitle),
                        'colClass' => 'col-md-12',
                        'placeholder' => 'Mari bergabung bersama kami wujudkan pendidikan berkualitas.',
                    ])
                </div>
            </div>
        </div>

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <h6 class="fw-bold mb-3">Tombol CTA Hero</h6>
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'cta_text', 'label' => 'Teks Tombol Utama',
                        'value' => old('cta_text', $ppdb->cta_text),
                        'colClass' => 'col-md-6',
                        'placeholder' => 'Formulir Pendaftaran Online',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'cta_url', 'label' => 'URL Tombol Utama',
                        'value' => old('cta_url', $ppdb->cta_url),
                        'colClass' => 'col-md-6',
                        'placeholder' => '/kontak atau https://...',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'secondary_text', 'label' => 'Teks Tombol Sekunder',
                        'value' => old('secondary_text', $ppdb->secondary_text),
                        'colClass' => 'col-md-6',
                        'placeholder' => 'Kontak Kami',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'secondary_url', 'label' => 'URL Tombol Sekunder',
                        'value' => old('secondary_url', $ppdb->secondary_url),
                        'colClass' => 'col-md-6',
                        'placeholder' => '/kontak atau https://...',
                    ])
                </div>
            </div>
        </div>

        <div class="card my-3 shadow-sm">
            <div class="card-body d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-2 p-2 pb-1">
                <div class="d-flex align-items-center gap-2">
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_active', 'label' => 'Aktif (tampilkan halaman PPDB)',
                        'checkedDefault' => old('is_active', $ppdb->is_active ?? true),
                        'inputId' => 'is_active_ppdb',
                    ])
                    <span class="fw-bold ms-3" style="font-size: 14px;">
                        Isi semua kolom bertanda <span class="text-danger">*</span>.
                    </span>
                </div>
                <button type="submit" class="btn btn-info w-100 w-md-auto mb-1">
                    <span class="material-symbols-rounded align-middle" style="font-size:18px;">save</span>
                    Simpan
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@include('landing-admin._scripts')
