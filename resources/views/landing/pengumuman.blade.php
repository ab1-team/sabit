@extends('landing.tata-letak')

@section('title', 'Pengumuman — ' . $setting->school_name)

@section('style')
<style>
    .lp-ann-list {
        position: relative;
        padding-left: 28px;
    }
    .lp-ann-list::before {
        content: "";
        position: absolute;
        left: 8px;
        top: 12px;
        bottom: 12px;
        width: 2px;
        background: linear-gradient(180deg, var(--lp-primary), var(--lp-accent-2));
        border-radius: 2px;
    }
    .lp-ann-item {
        position: relative;
        padding: var(--lp-card-pad) var(--lp-card-pad-lg);
        margin-bottom: 1rem;
        border-radius: var(--lp-radius-lg);
    }
    .lp-ann-item::before {
        content: "";
        position: absolute;
        left: -24px;
        top: 1.85rem;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #fff;
        border: 3px solid var(--lp-primary);
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.15);
    }
</style>
@endsection

@section('content')
<section class="lp-section">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Info Terkini</span>
            <h2 class="lp-section-title">Pengumuman</h2>
            <p class="lp-section-sub">Informasi terbaru dari sekolah untuk orang tua dan siswa.</p>
        </div>

        @if ($announcements->isEmpty())
            <div class="text-center text-muted py-5 lp-reveal" data-from="zoom">
                <i class="bi bi-megaphone" style="font-size:3rem; opacity:.3;"></i>
                <p class="mt-3 mb-0">Belum ada pengumuman.</p>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="lp-ann-list">
                        @foreach ($announcements as $i => $item)
                            <div class="lp-glass lp-ann-item lp-reveal" data-from="left" data-delay="{{ (($i % 3) + 1) }}">
                                <div class="d-flex flex-wrap justify-content-between align-items-start mb-2">
                                    <h5 class="mb-0 fw-bold">{{ $item->title }}</h5>
                                    @if ($item->published_at)
                                        <small class="text-muted">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $item->published_at->translatedFormat('d F Y') }}
                                        </small>
                                    @endif
                                </div>
                                <div class="text-muted">{!! $item->content !!}</div>
                                @if ($item->file)
                                    <a href="{{ Storage::disk('public')->url('landing/' . $item->file) }}"
                                       class="lp-link-soft mt-3 d-inline-flex" target="_blank" rel="noopener">
                                        <i class="bi bi-paperclip"></i> Unduh Lampiran
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $announcements->links() }}
            </div>
        @endif
    </div>
</section>
@endsection
