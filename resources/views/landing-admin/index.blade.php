@extends('layouts.tenant.base')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Landing Page</h4>
            <p class="text-muted mb-0">Kelola konten website publik sekolah.</p>
        </div>
        @if ($landingUrl)
            <a href="{{ $landingUrl }}" target="_blank" rel="noopener" class="btn btn-outline-primary">
                Lihat Website
            </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3 mb-4">
        @foreach ([
            'Hero Slide' => $stats['slides'],
            'Berita' => $stats['posts'],
            'Galeri' => $stats['galleries'],
            'Pengumuman' => $stats['announcements'],
            'Pesan Belum Dibaca' => $stats['unread_messages'],
        ] as $label => $value)
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <div class="h3 mb-1">{{ $value }}</div>
                        <div class="text-muted small">{{ $label }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Pengaturan Website</h5>
                    <p class="text-muted">
                        Nama sekolah, tagline, logo, kontak, media sosial, dan meta SEO.
                    </p>
                    <a href="{{ route('app.landing.pengaturan') }}" class="btn btn-primary">Buka Pengaturan</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title">Hero Slider</h5>
                    <p class="text-muted">
                        Gambar dan teks utama yang tampil di bagian atas halaman depan.
                    </p>
                    <a href="{{ route('app.landing.hero') }}" class="btn btn-primary">Kelola Slider</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
