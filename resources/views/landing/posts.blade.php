@extends('landing.layout')

@section('title', 'Berita — ' . $setting->school_name)

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Berita</h2>

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
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
