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
            .'<p class="text-muted small mb-0">Atur judul, paragraf, tombol, dan poin-poin CTA di section PPDB halaman beranda. Nonaktifkan untuk menyembunyikan section.</p>';
    @endphp
    @include('landing-admin._page-header', [
        'subtitle' => 'Landing Page',
        'back' => route('app.landing.index'),
        'titleSlot' => $titleSlot,
    ])

    <div class="card mb-3 shadow-sm">
        <div class="card-body p-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="material-symbols-rounded text-primary">how_to_reg</span>
                <h6 class="mb-0 fw-bold">Kelola Konten Halaman PPDB</h6>
            </div>
            <p class="text-muted small mb-3">Halaman publik <code>/ppdb</code> terdiri dari CTA di beranda (form di bawah) dan konten detail (persyaratan, alur, jadwal, FAQ).</p>
            <div class="row g-2">
                <div class="col-6 col-md-3">
                    <a href="{{ route('app.landing.ppdb.requirements') }}" class="lp-admin-menu text-decoration-none">
                        <div class="lp-admin-menu-icon" style="background: linear-gradient(135deg, #37d17c22, #37d17c11); color: #37d17c;">
                            <span class="material-symbols-rounded">fact_check</span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-dark lh-1" style="font-size:.85rem;">Persyaratan</div>
                            <div class="text-muted" style="font-size:.7rem;">Dokumen & kelengkapan</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('app.landing.ppdb.stages') }}" class="lp-admin-menu text-decoration-none">
                        <div class="lp-admin-menu-icon" style="background: linear-gradient(135deg, #0ea5e922, #0ea5e911); color: #0ea5e9;">
                            <span class="material-symbols-rounded">timeline</span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-dark lh-1" style="font-size:.85rem;">Alur</div>
                            <div class="text-muted" style="font-size:.7rem;">Tahapan pendaftaran</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('app.landing.ppdb.schedules') }}" class="lp-admin-menu text-decoration-none">
                        <div class="lp-admin-menu-icon" style="background: linear-gradient(135deg, #f59e0b22, #f59e0b11); color: #f59e0b;">
                            <span class="material-symbols-rounded">event</span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-dark lh-1" style="font-size:.85rem;">Jadwal</div>
                            <div class="text-muted" style="font-size:.7rem;">Gelombang & tanggal</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('app.landing.ppdb.faqs') }}" class="lp-admin-menu text-decoration-none">
                        <div class="lp-admin-menu-icon" style="background: linear-gradient(135deg, #ec489922, #ec489911); color: #ec4899;">
                            <span class="material-symbols-rounded">quiz</span>
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <div class="fw-bold text-dark lh-1" style="font-size:.85rem;">FAQ</div>
                            <div class="text-muted" style="font-size:.7rem;">Pertanyaan umum</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @php
        $points = $cta['points'] ?? [
            'Pendaftaran online & mudah',
            'Kuota terbatas per jenjang',
            'Bantuan seleksi & verifikasi',
        ];
        $pointsString = old('points', is_array($points) ? implode("\n", $points) : $points);
    @endphp

    <form action="{{ $action }}" method="POST" class="lp-ajax">
        @csrf

        <div class="card my-3 shadow-sm">
            <div class="card-body p-3">
                <div class="row">
                    @include('landing-admin._components.text-input', [
                        'name' => 'title', 'label' => 'Judul CTA', 'required' => true,
                        'value' => old('title', $cta['title'] ?? ''), 'colClass' => 'col-md-8',
                        'help' => 'Gunakan <code>{{year}}</code> untuk placeholder tahun ajaran, mis. "PPDB {{year}}/".',
                    ])
                    @include('landing-admin._components.switch-input', [
                        'name' => 'is_active', 'label' => 'Aktif (tampilkan section)',
                        'checkedDefault' => $cta['is_active'] ?? true,
                        'inputId' => 'is_active_cta', 'colClass' => 'col-md-4',
                    ])
                    @include('landing-admin._components.textarea-input', [
                        'name' => 'paragraph', 'label' => 'Paragraf',
                        'value' => old('paragraph', $cta['paragraph'] ?? ''), 'rows' => 3,
                        'help' => 'Gunakan <code>{{school}}</code> untuk nama sekolah.',
                    ])
                    @include('landing-admin._components.textarea-input', [
                        'name' => 'points', 'label' => 'Poin-poin Keunggulan (satu per baris)',
                        'value' => $pointsString, 'rows' => 4,
                        'help' => 'Tiap baris jadi satu poin dengan icon centang.',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'button_primary_text', 'label' => 'Teks Tombol Utama', 'required' => true,
                        'value' => old('button_primary_text', $cta['button_primary_text'] ?? 'Daftar Sekarang'),
                        'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'button_primary_url', 'label' => 'URL Tombol Utama',
                        'placeholder' => '/ppdb atau https://...',
                        'value' => old('button_primary_url', $cta['button_primary_url'] ?? ''),
                        'colClass' => 'col-md-6',
                        'help' => 'Kosongkan untuk default ke halaman <code>/ppdb</code>.',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'button_secondary_text', 'label' => 'Teks Tombol Sekunder', 'required' => true,
                        'value' => old('button_secondary_text', $cta['button_secondary_text'] ?? 'Hubungi Kami'),
                        'colClass' => 'col-md-6',
                    ])
                    @include('landing-admin._components.text-input', [
                        'name' => 'button_secondary_url', 'label' => 'URL Tombol Sekunder',
                        'placeholder' => '/kontak atau https://...',
                        'value' => old('button_secondary_url', $cta['button_secondary_url'] ?? ''),
                        'colClass' => 'col-md-6',
                        'help' => 'Kosongkan untuk default ke halaman <code>/kontak</code>.',
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

@include('landing-admin._scripts')
