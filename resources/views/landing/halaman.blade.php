@extends('landing.tata-letak')

@section('title', $page->title . ' — ' . $setting->school_name)

@section('style')
<style>
    .lp-page-article {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-xl);
        padding: 2.5rem;
        box-shadow: 0 10px 40px -10px rgba(15, 23, 42, 0.08);
    }
    .lp-page-article h1 { font-weight: 800; letter-spacing: -0.02em; }
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
        background: rgba(79, 70, 229, 0.05);
        border-radius: var(--lp-radius-sm);
        margin: 1.5rem 0;
        font-style: italic;
    }
    @media (max-width: 767.98px) {
        .lp-page-article { padding: 1.5rem; border-radius: var(--lp-radius-lg); }
    }
</style>
@endsection

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <article class="lp-page-article lp-reveal" data-from="zoom">
                    <h1>{!! $page->title !!}</h1>

                    @if ($page->image)
                        <div class="lp-cover">
                            <img src="{{ Storage::disk('public')->url('landing/' . $page->image) }}" alt="{{ $page->title }}">
                        </div>
                    @endif

                    <div class="lp-content">
                        {!! $page->content !!}
                    </div>
                </article>
            </div>
        </div>
    </div>
</section>
@endsection
