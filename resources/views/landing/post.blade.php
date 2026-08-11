@extends('landing.layout')

@section('title', $post->title . ' — ' . $setting->school_name)

@section('content')
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                @if ($post->category)
                    <span class="badge bg-secondary mb-2">{{ $post->category }}</span>
                @endif

                <h2 class="mb-2">{{ $post->title }}</h2>

                <p class="text-muted small mb-4">
                    @if ($post->published_at)
                        {{ $post->published_at->translatedFormat('d F Y') }}
                    @endif
                    &middot; {{ $post->views }} kali dilihat
                </p>

                @if ($post->image)
                    <img src="{{ Storage::disk('public')->url('landing/' . $post->image) }}"
                         class="img-fluid rounded mb-4" alt="">
                @endif

                <div class="lp-content">
                    {!! $post->content !!}
                </div>

                @if ($post->tags)
                    <div class="mt-4">
                        @foreach (explode(',', $post->tags) as $tag)
                            <span class="badge bg-light text-dark">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                @endif

                <a href="{{ route('landing.posts') }}" class="btn btn-outline-primary mt-4">
                    Kembali ke Berita
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
