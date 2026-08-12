@extends('landing.tata-letak')

@section('title', 'Galeri — ' . $setting->school_name)

@section('style')
<style>
    .lp-album-pill {
        padding: 0.5rem 1.1rem;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        color: #475569;
        font-weight: 500;
        font-size: 0.88rem;
        transition: all 0.2s ease;
        text-decoration: none;
        display: inline-block;
    }
    .lp-album-pill:hover {
        transform: translateY(-2px);
        color: var(--lp-primary);
        box-shadow: 0 6px 16px rgba(var(--lp-primary-rgb), 0.15);
    }
    .lp-album-pill.active {
        background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
        color: #fff;
        border-color: transparent;
        box-shadow: 0 8px 20px rgba(var(--lp-primary-rgb), 0.3);
    }
</style>
@endsection

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Momen</span>
            <h2 class="lp-section-title">Galeri</h2>
            <p class="lp-section-sub">Dokumentasi kegiatan &amp; momen berharga di sekolah kami.</p>
        </div>

        @if ($albums->isNotEmpty())
            <div class="text-center mb-4 d-flex flex-wrap justify-content-center gap-2 lp-reveal" data-from="zoom">
                <a href="{{ route('landing.galeri') }}"
                   class="lp-album-pill {{ $album ? '' : 'active' }}">Semua</a>
                @foreach ($albums as $item)
                    <a href="{{ route('landing.galeri', ['album' => $item]) }}"
                       class="lp-album-pill {{ $album === $item ? 'active' : '' }}">{{ $item }}</a>
                @endforeach
            </div>
        @endif

        @if ($galleries->isEmpty())
            <div class="text-center text-muted py-5 lp-reveal" data-from="zoom">
                <i class="bi bi-image" style="font-size:3rem; opacity:.3;"></i>
                <p class="mt-3 mb-0">Belum ada foto.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach ($galleries as $i => $item)
                    <div class="col-6 col-md-4 col-lg-3">
                        <a href="{{ Storage::disk('public')->url('landing/' . $item->image) }}" target="_blank" class="lp-gallery-item lp-reveal d-block" data-from="zoom" data-delay="{{ (($i % 4) + 1) }}">
                            @if ($item->image)
                                <img src="{{ Storage::disk('public')->url('landing/' . $item->image) }}" alt="{{ $item->title }}" loading="lazy">
                                <div class="lp-gallery-overlay">{{ $item->title }}</div>
                            @endif
                        </a>
                        @if ($item->album)
                            <div class="small text-muted mt-2 text-center">{{ $item->album }}</div>
                        @endif
                    </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $galleries->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
