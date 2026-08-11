@extends('landing.tata-letak')

@section('title', 'Video — ' . $setting->school_name)

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Multimedia</span>
            <h2 class="lp-section-title">Video</h2>
            <p class="lp-section-sub">Liputan dan dokumentasi kegiatan sekolah dalam video.</p>
        </div>

        @if ($videos->isEmpty())
            <div class="text-center text-muted py-5 lp-reveal" data-from="zoom">
                <i class="bi bi-camera-video" style="font-size:3rem; opacity:.3;"></i>
                <p class="mt-3 mb-0">Belum ada video.</p>
            </div>
        @else
            <div class="row g-3 g-lg-4">
                @foreach ($videos as $i => $video)
                    <div class="col-md-6 col-lg-4">
                        <div class="lp-glass lp-media-card lp-reveal h-100" data-from="zoom" data-delay="{{ (($i % 3) + 1) }}">
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $video->youtube_url }}"
                                        title="{{ $video->title }}"
                                        allowfullscreen loading="lazy"
                                        style="border:0;"></iframe>
                            </div>
                            <div class="lp-media-body">
                                <h6 class="fw-bold mb-2">{{ $video->title }}</h6>
                                @if ($video->description)
                                    <p class="text-muted small mb-0">
                                        {{ Str::limit(strip_tags($video->description), 100) }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $videos->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
