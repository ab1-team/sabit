@extends('landing.layout')

@section('content')

@php $hero = $slides->first(); @endphp

<section class="lp-hero position-relative {{ $hero && $hero->image ? 'lp-hero-img' : '' }}"
         @if ($hero && $hero->image)
             style="background-image:url('{{ Storage::disk('public')->url('landing/' . $hero->image) }}')"
         @endif>
    <div class="container position-relative py-5">
        <div class="col-lg-8">
            <h1 class="display-5 fw-bold">
                {{ $hero->title ?? ('Selamat Datang di ' . $setting->school_name) }}
            </h1>
            <p class="lead mb-4">
                {{ $hero->subtitle ?? $setting->tagline }}
            </p>
            @if ($hero && $hero->button_text && $hero->button_url)
                <a href="{{ $hero->button_url }}" class="btn btn-light btn-lg">{{ $hero->button_text }}</a>
            @endif
        </div>
    </div>
</section>

@if ($announcements->isNotEmpty())
<section class="py-4 bg-warning-subtle">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Pengumuman</h5>
            <a href="{{ route('landing.announcements') }}" class="small">Lihat semua</a>
        </div>
        <ul class="list-unstyled mb-0">
            @foreach ($announcements as $item)
                <li class="mb-2">
                    <span class="fw-semibold">{{ $item->title }}</span>
                    @if ($item->published_at)
                        <span class="text-muted small">
                            &mdash; {{ $item->published_at->translatedFormat('d F Y') }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif

<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Berita Terbaru</h4>
            <a href="{{ route('landing.posts') }}" class="small">Lihat semua</a>
        </div>

        @if ($posts->isEmpty())
            <p class="text-muted">Belum ada berita.</p>
        @else
            <div class="row g-4">
                @foreach ($posts as $post)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            @if ($post->image)
                                <img src="{{ Storage::disk('public')->url('landing/' . $post->image) }}"
                                     class="card-img-top lp-card-img" alt="">
                            @else
                                <div class="lp-placeholder">Tanpa gambar</div>
                            @endif
                            <div class="card-body">
                                @if ($post->category)
                                    <span class="badge bg-secondary mb-2">{{ $post->category }}</span>
                                @endif
                                <h6 class="card-title">
                                    <a href="{{ route('landing.post', $post->slug) }}"
                                       class="text-decoration-none text-dark">{{ $post->title }}</a>
                                </h6>
                                <p class="card-text text-muted small">
                                    {{ \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 110) }}
                                </p>
                            </div>
                            @if ($post->published_at)
                                <div class="card-footer bg-white border-0 text-muted small">
                                    {{ $post->published_at->translatedFormat('d F Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

@if ($events->isNotEmpty())
<section class="py-5 bg-light">
    <div class="container">
        <h4 class="mb-4">Agenda Kegiatan</h4>
        <div class="row g-3">
            @foreach ($events as $event)
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="text-primary fw-bold small mb-1">
                                {{ $event->start_date?->translatedFormat('d F Y') }}
                            </div>
                            <h6 class="card-title">{{ $event->title }}</h6>
                            @if ($event->location)
                                <p class="text-muted small mb-0">{{ $event->location }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($galleries->isNotEmpty())
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Galeri</h4>
            <a href="{{ route('landing.galleries') }}" class="small">Lihat semua</a>
        </div>
        <div class="row g-3">
            @foreach ($galleries as $item)
                <div class="col-6 col-md-3">
                    @if ($item->image)
                        <img src="{{ Storage::disk('public')->url('landing/' . $item->image) }}"
                             class="img-fluid rounded lp-card-img w-100" alt="{{ $item->title }}">
                    @else
                        <div class="lp-placeholder rounded">Tanpa gambar</div>
                    @endif
                    <div class="small mt-2">{{ $item->title }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
