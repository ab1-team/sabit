@extends('halaman-publik.tata-letak')

@section('title', 'Berita — ' . $setting->school_name)

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Berita &amp; Artikel</span>
            <h2 class="lp-section-title">Berita Terbaru</h2>
            <p class="lp-section-sub">Informasi, kegiatan, dan cerita inspiratif dari sekolah kami.</p>
        </div>

        @if ($posts->isEmpty())
            <div class="text-center text-muted py-5 lp-reveal" data-from="zoom">
                <i class="bi bi-inbox" style="font-size:3rem; opacity:.3;"></i>
                <p class="mt-3 mb-0">Belum ada berita.</p>
            </div>
        @else
            <div class="row g-3 g-lg-4">
                @foreach ($posts as $i => $post)
                    <div class="col-md-6 col-lg-4">
                        <div class="lp-program-card lp-reveal h-100" data-from="zoom" data-delay="{{ (($i % 3) + 1) }}">
                            @if ($post->image)
                                <a href="{{ route('halaman-publik.artikel', $post->slug) }}" class="lp-thumb">
                                    <img src="{{ Storage::disk('public')->url('landing/' . $post->image) }}" alt="" loading="lazy">
                                </a>
                            @endif
                            <div class="lp-body">
                                @if ($post->category)
                                    <span class="lp-tag">{{ $post->category }}</span>
                                @endif
                                <h5>
                                    <a href="{{ route('halaman-publik.artikel', $post->slug) }}" class="text-dark">{{ $post->title }}</a>
                                </h5>
                                <p class="text-muted small mb-3">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $post->published_at?->translatedFormat('d F Y') }}
                                </p>
                                <p class="mb-0">{{ Str::limit(strip_tags($post->excerpt ?: $post->content), 110) }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
