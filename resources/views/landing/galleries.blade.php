@extends('landing.layout')

@section('title', 'Galeri — ' . $setting->school_name)

@section('content')
<section class="py-5">
    <div class="container">
        <h2 class="mb-4">Galeri</h2>

        @if ($albums->isNotEmpty())
            <div class="mb-4 d-flex flex-wrap gap-2">
                <a href="{{ route('landing.galleries') }}"
                   class="btn btn-sm {{ $album ? 'btn-outline-primary' : 'btn-primary' }}">Semua</a>
                @foreach ($albums as $item)
                    <a href="{{ route('landing.galleries', ['album' => $item]) }}"
                       class="btn btn-sm {{ $album === $item ? 'btn-primary' : 'btn-outline-primary' }}">
                        {{ $item }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($galleries->isEmpty())
            <p class="text-muted">Belum ada foto.</p>
        @else
            <div class="row g-3">
                @foreach ($galleries as $item)
                    <div class="col-6 col-md-4 col-lg-3">
                        @if ($item->image)
                            <img src="{{ Storage::disk('public')->url('landing/' . $item->image) }}"
                                 class="img-fluid rounded lp-card-img w-100" alt="{{ $item->title }}">
                        @else
                            <div class="lp-placeholder rounded">Tanpa gambar</div>
                        @endif
                        <div class="small mt-2 fw-semibold">{{ $item->title }}</div>
                        @if ($item->album)
                            <div class="small text-muted">{{ $item->album }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
