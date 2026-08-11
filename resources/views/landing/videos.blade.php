@extends('landing.layout')

@section('title', 'Video — ' . $setting->school_name)

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Video</h2>

        @if ($videos->isEmpty())
            <p class="text-muted">Belum ada video.</p>
        @else
            <div class="row g-4">
                @foreach ($videos as $video)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $video->youtube_url }}"
                                        title="{{ $video->title }}"
                                        allowfullscreen loading="lazy"></iframe>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title mb-1">{{ $video->title }}</h6>
                                @if ($video->description)
                                    <p class="card-text text-muted small mb-0">
                                        {{ \Illuminate\Support\Str::limit(strip_tags($video->description), 100) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
