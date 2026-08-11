@extends('landing.layout')

@section('title', 'Pengumuman — ' . $setting->school_name)

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Pengumuman</h2>

        @if ($announcements->isEmpty())
            <p class="text-muted">Belum ada pengumuman.</p>
        @else
            <div class="list-group mb-4">
                @foreach ($announcements as $item)
                    <div class="list-group-item">
                        <div class="d-flex flex-wrap justify-content-between">
                            <h6 class="mb-1">{{ $item->title }}</h6>
                            @if ($item->published_at)
                                <small class="text-muted">
                                    {{ $item->published_at->translatedFormat('d F Y') }}
                                </small>
                            @endif
                        </div>

                        <div class="small text-muted">{!! $item->content !!}</div>

                        @if ($item->file)
                            <a href="{{ Storage::disk('public')->url('landing/' . $item->file) }}"
                               class="btn btn-sm btn-outline-primary mt-2" target="_blank" rel="noopener">
                                Unduh Lampiran
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            {{ $announcements->links() }}
        @endif
    </div>
</section>
@endsection
