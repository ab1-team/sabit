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
                <p class="mt-3 mb-0">Belum ada berita yang dipublikasikan.</p>
                <p class="mt-3 mb-0 small text-muted">
                    Total di DB: {{ $debugCounts['total'] ?? 0 }} —
                    Published: {{ $debugCounts['published'] ?? 0 }} —
                    Draft: {{ $debugCounts['draft'] ?? 0 }} —
                    Terjadwal: {{ $debugCounts['scheduled'] ?? 0 }}.
                </p>
            </div>
        @else
            <p class="text-muted small mb-3">
                Menampilkan {{ $posts->count() }} dari {{ $posts->total() }} artikel.
            </p>
            <div class="row g-3 g-lg-4">
                @foreach ($posts as $i => $post)
                    <div class="col-md-6 col-lg-4 d-flex">
                        <div class="lp-program-card lp-reveal h-100 w-100 d-flex flex-column" data-from="zoom" data-delay="{{ (($i % 3) + 1) }}">
                            @if ($post->image)
                                <a href="{{ route('halaman-publik.artikel', $post->slug) }}" class="lp-thumb">
                                    <img src="{{ Storage::disk('public')->url('landing/' . $post->image) }}" alt="" loading="lazy">
                                </a>
                            @endif
                            <div class="lp-body d-flex flex-column flex-grow-1">
                                @if ($post->category)
                                    <span class="lp-tag">{{ $post->category }}</span>
                                @endif
                                <h5>
                                    <a href="{{ route('halaman-publik.artikel', $post->slug) }}" class="text-dark">{{ $post->title }}</a>
                                </h5>
                                <p class="lp-post-meta text-muted small mb-2">
                                    <i class="bi bi-calendar3 me-1"></i>
                                    {{ $post->published_at?->translatedFormat('d F Y') }}
                                    @if (! $post->is_published)
                                        <span class="badge text-bg-secondary ms-2">Draft</span>
                                    @elseif ($post->published_at && $post->published_at->isFuture())
                                        <span class="badge text-bg-warning ms-2">Terjadwal</span>
                                    @else
                                        <span class="badge text-bg-success ms-2">Published</span>
                                    @endif
                                </p>
                                <p class="flex-grow-1 mb-0">{{ Str::limit(strip_tags($post->excerpt ?: $post->content), 110) }}</p>
                                <a href="{{ route('halaman-publik.artikel', $post->slug) }}" class="lp-link-soft mt-2">
                                    Baca selengkapnya <i class="bi bi-arrow-right"></i>
                                </a>
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
