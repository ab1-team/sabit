@extends('landing.tata-letak')

@section('title', $post->title . ' — ' . $setting->school_name)

@section('style')
<style>
    .lp-article {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-xl);
        padding: 2.5rem;
        box-shadow: 0 10px 40px -10px rgba(15, 23, 42, 0.08);
    }
    .lp-article h1 { font-weight: 800; letter-spacing: -0.02em; line-height: 1.2; }
    .lp-cover {
        border-radius: var(--lp-radius-lg);
        overflow: hidden;
        margin: 1.5rem 0 2rem;
        box-shadow: 0 16px 40px -16px rgba(15, 23, 42, 0.15);
    }
    .lp-cover img { width: 100%; display: block; }
    .lp-content { font-size: 1.02rem; line-height: 1.8; color: #334155; }
    .lp-content p { margin-bottom: 1.1rem; }
    .lp-content h2, .lp-content h3 { margin-top: 2rem; margin-bottom: 1rem; font-weight: 700; }
    .lp-content img { max-width: 100%; border-radius: var(--lp-radius-sm); margin: 1rem 0; }
    .lp-content blockquote {
        border-left: 4px solid var(--lp-primary);
        padding: 0.75rem 1.25rem;
        background: rgba(var(--lp-primary-rgb), 0.05);
        border-radius: var(--lp-radius-sm);
        margin: 1.5rem 0;
        font-style: italic;
    }
    .lp-tag-pill {
        display: inline-block;
        padding: 0.3rem 0.75rem;
        background: rgba(var(--lp-primary-rgb), 0.1);
        color: var(--lp-primary);
        border-radius: 999px;
        font-size: 0.82rem;
        font-weight: 500;
        margin-right: 0.4rem;
    }
    @media (max-width: 767.98px) {
        .lp-article { padding: 1.5rem; border-radius: var(--lp-radius-lg); }
    }
</style>
@endsection

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <article class="lp-article lp-reveal" data-from="zoom">
                    <h1>{{ $post->title }}</h1>

                    <p class="text-muted small mb-4 d-flex flex-wrap align-items-center gap-2">
                        @if ($post->category)
                            <span class="lp-tag">{{ $post->category }}</span>
                        @endif
                        @if ($post->published_at)
                            <span><i class="bi bi-calendar3 me-1"></i>{{ $post->published_at->translatedFormat('d F Y') }}</span>
                            <span class="mx-1">·</span>
                        @endif
                        <span><i class="bi bi-eye me-1"></i>{{ $post->views }} kali dilihat</span>
                    </p>

                    @if ($post->image)
                        <div class="lp-cover">
                            <img src="{{ Storage::disk('public')->url('landing/' . $post->image) }}" alt="{{ $post->title }}">
                        </div>
                    @endif

                    <div class="lp-content">
                        {!! $post->content !!}
                    </div>

                    @if ($post->tags)
                        <div class="mt-4 pt-3" style="border-top: 1px solid rgba(15,23,42,0.08);">
                            @foreach (explode(',', $post->tags) as $tag)
                                <span class="lp-tag-pill">#{{ trim($tag) }}</span>
                            @endforeach
                        </div>
                    @endif

                    <a href="{{ route('landing.daftar-artikel') }}" class="lp-link-soft mt-4 d-inline-flex">
                        <i class="bi bi-arrow-left"></i> Kembali ke Berita
                    </a>
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
