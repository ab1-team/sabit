@extends('halaman-publik.tata-letak')

@section('title', ($setting->school_name ?? 'Sekolah') . ' — ' . ($setting->tagline ?? 'Sekolah Dasar Terbaik'))

@php
    $hero = $slides->first();

    $heroTitle = $hero->title ?? ($setting->school_name ?? 'Sekolah');
    $heroSubtitle = $hero->subtitle ?? '';
    $heroButtonText = $hero->button_text ?? 'Profil Sekolah';
    $heroButtonUrl = $hero->button_url ?? null;

    if ($setting->hasThemeBackground()) {
        $heroImage = $setting->heroBackgroundUrl();
    } elseif ($hero && $hero->image) {
        $heroImage = Storage::disk('public')->url('landing/' . $hero->image);
    } else {
        $heroImage = $setting->heroBackgroundUrl();
    }

    $programs = $posts->take(3);

    // Sambutan Kepala Sekolah
    $welcome = $setting->welcomeData();

    // Hero badges: array of {icon, text} dari lp_pengaturan.hero_badges JSON.
    // Filter item yang punya text tidak kosong.
    $rawBadges = $setting->hero_badges;
    if (!is_array($rawBadges)) { $rawBadges = []; }
    $heroBadges = array_values(array_filter($rawBadges, function ($b) {
        return is_array($b) && trim((string) ($b['text'] ?? '')) !== '';
    }));

    // Stats: array of {icon, color, value, label}. Hanya tampilkan yang punya
    // value & label tidak kosong, max 3.
    $rawStats = $setting->stats;
    if (!is_array($rawStats)) { $rawStats = []; }
    $stats = array_values(array_filter($rawStats, function ($s) {
        return is_array($s)
            && trim((string) ($s['value'] ?? '')) !== ''
            && trim((string) ($s['label'] ?? '')) !== '';
    }));
    if (count($stats) > 3) { $stats = array_slice($stats, 0, 3); }

    // CTA PPDB section data
    $ppdbCta = $setting->ppdbCtaData();
    $ppdbCtaActive = $ppdbCta['is_active'] ?? true;
    $year = date('Y');
    $nextYear = $year + 1;
    $ppdbTitle = str_replace(['{{year}}'], [$year . '/' . $nextYear], $ppdbCta['title'] ?? '');
    $ppdbParagraph = str_replace(['{{school}}'], $setting->school_name ?? 'sekolah kami', $ppdbCta['paragraph'] ?? '');
    $ppdbRegistration = $ppdbCta['registration'] ?? '';
@endphp

@section('content')

{{-- ===== Hero ===== --}}
<section class="lp-hero">
    <div class="lp-hero-bg" style="background-image:url('{{ $heroImage }}')"></div>
    <div class="container lp-hero-content">
        <h1 class="lp-reveal" data-from="zoom">{{ $heroTitle }}</h1>
        <p class="lead lp-reveal" data-delay="2">
            {{ $heroSubtitle }}
        </p>
        <div class="lp-hero-actions lp-reveal" data-delay="3">
            @if ($heroButtonUrl)
                <a href="{{ $heroButtonUrl }}" class="lp-btn-light">{{ $heroButtonText }} <i class="bi bi-arrow-right"></i></a>
            @endif
            <a href="{{ route('halaman-publik.profil') }}" class="lp-btn-outline-light">Profil Sekolah</a>
        </div>
        @if (!empty($heroBadges))
            <div class="lp-reveal mt-4 d-flex flex-wrap justify-content-center gap-2" data-delay="4">
                @foreach ($heroBadges as $b)
                    <span class="lp-badge">
                        <i class="bi {{ $b['icon'] ?? 'bi-patch-check-fill' }}"></i>
                        {{ $b['text'] }}
                    </span>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- ===== Sambutan Kepala Sekolah ===== --}}
@php
    $welcomeParagraphs = $welcome['paragraphs'] ?? [];
    $welcomePreview = array_slice($welcomeParagraphs, 0, 2);
    $welcomeHasMore = count($welcomeParagraphs) > count($welcomePreview);
@endphp
<section class="lp-section" id="profil">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-5">
                <div class="lp-welcome-img lp-reveal" data-from="left">
                    <img src="{{ $welcome['photo'] }}"
                         alt="Kepala Sekolah">
                    @if (!empty($welcome['quote']))
                        <div class="lp-welcome-quote">
                            <i class="bi bi-quote"></i>
                            <span>"{{ $welcome['quote'] }}"</span>
                        </div>
                    @endif
                </div>
            </div>
            <div class="col-lg-7 lp-reveal" data-from="right">
                <span class="lp-section-eyebrow">Sambutan</span>
                <h2 class="lp-section-title">Sambutan Kepala Sekolah</h2>
                <div class="lp-divider"></div>
                @forelse ($welcomePreview as $p)
                    <p class="lp-text-muted-soft">{{ $p }}</p>
                @empty
                    <p class="lp-text-muted-soft">Belum ada teks sambutan.</p>
                @endforelse

                <div class="lp-welcome-actions">
                    @if ($welcomeHasMore)
                        <button type="button" class="lp-welcome-readmore" data-bs-toggle="modal" data-bs-target="#lpWelcomeModal">
                            <span>Baca Sambutan Lengkap</span>
                            @if (count($welcomeParagraphs) > 2)
                                <span class="lp-welcome-readmore-badge">+{{ count($welcomeParagraphs) - 2 }}</span>
                            @endif
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    @endif
                    <div class="lp-welcome-meta">
                        <div class="lp-stat-icon is-blue" style="width:48px; height:48px; font-size:1.1rem;">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-dark">{{ $welcome['head_name'] }}</div>
                            <div class="text-muted small">{{ $welcome['head_role'] }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Modal popup Sambutan Lengkap --}}
@if ($welcomeHasMore)
<div class="modal fade lp-welcome-modal" id="lpWelcomeModal" tabindex="-1" aria-hidden="true" aria-labelledby="lpWelcomeModalTitle">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg modal-fullscreen-sm-down">
        <div class="modal-content lp-welcome-modal-content">
            <button type="button" class="lp-welcome-modal-close" data-bs-dismiss="modal" aria-label="Tutup">
                <i class="bi bi-x-lg"></i>
            </button>
            <div class="modal-header lp-welcome-modal-head">
                <div class="lp-welcome-modal-head-text">
                    <span class="lp-section-eyebrow">Sambutan</span>
                    <h5 class="modal-title lp-welcome-modal-title" id="lpWelcomeModalTitle">Sambutan Kepala Sekolah</h5>
                </div>
            </div>
            <div class="modal-body lp-welcome-modal-body">
                <div class="lp-welcome-modal-profile d-flex align-items-center gap-3">
                    <img src="{{ $welcome['photo'] }}" alt="{{ $welcome['head_name'] }}" class="lp-welcome-modal-avatar">
                    <div class="flex-grow-1">
                        <div class="fw-bold text-dark">{{ $welcome['head_name'] }}</div>
                        <div class="text-muted small">{{ $welcome['head_role'] }}</div>
                    </div>
                </div>
                @if (!empty($welcome['quote']))
                    <blockquote class="lp-welcome-modal-quote">
                        <i class="bi bi-quote"></i>
                        <span>{{ $welcome['quote'] }}</span>
                    </blockquote>
                @endif
                <div class="lp-welcome-modal-divider"></div>
                <div class="lp-welcome-modal-paragraphs">
                    @foreach ($welcomeParagraphs as $p)
                        <p>{{ $p }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ===== Statistik (data tenant dari lp_pengaturan.stats) ===== --}}
@if (!empty($stats))
<section class="lp-section lp-bg-soft" id="statistik">
    <div class="container">
        <div class="row g-3 g-lg-4">
            @foreach ($stats as $i => $s)
                @php
                    $color = in_array($s['color'] ?? '', ['blue','green','amber','pink','purple','cyan'], true)
                        ? $s['color']
                        : 'blue';
                    $icon = $s['icon'] ?? 'bi-people-fill';
                @endphp
                <div class="col-md-4">
                    <div class="lp-glass lp-stat-card lp-reveal h-100" data-from="zoom" data-delay="{{ $i + 1 }}">
                        <div class="lp-stat-icon is-{{ $color }}">
                            <i class="bi {{ $icon }}"></i>
                        </div>
                        <div>
                            <div class="lp-stat-value">{{ $s['value'] }}</div>
                            <div class="lp-stat-label">{{ $s['label'] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== Program Unggulan (Berita terbaru dipromosikan) ===== --}}
@if ($programs->isNotEmpty())
<section class="lp-section lp-bg-soft" id="program">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end lp-section-head lp-section-head-sm">
            <div class="lp-reveal" data-from="left">
                <span class="lp-section-eyebrow">Program</span>
                <h2 class="lp-section-title mb-2">Berita &amp; Program Unggulan</h2>
                <p class="text-muted mb-0">Informasi kegiatan dan program terbaru dari sekolah kami.</p>
            </div>
            <a href="{{ route('halaman-publik.daftar-artikel') }}" class="lp-link-soft lp-reveal" data-from="right">Lihat Semua <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="row g-3 g-lg-4">
            @foreach ($programs as $i => $post)
                @php
                    $title = $post->title;
                    $cat = $post->category;
                    $desc = \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 110);
                    $img = $post->image ? Storage::disk('public')->url('landing/' . $post->image) : null;
                @endphp
                <div class="col-md-6 col-lg-4 d-flex">
                    <div class="lp-program-card lp-reveal h-100 w-100 d-flex flex-column" data-from="zoom" data-delay="{{ $i + 1 }}">
                        @if ($img)
                            <a href="{{ route('halaman-publik.artikel', $post->slug) }}" class="lp-thumb">
                                <img src="{{ $img }}" alt="{{ $title }}" loading="lazy">
                            </a>
                        @endif
                        <div class="lp-body d-flex flex-column flex-grow-1">
                            @if ($cat)
                                <span class="lp-tag">{{ $cat }}</span>
                            @endif
                            <h5><a href="{{ route('halaman-publik.artikel', $post->slug) }}" class="text-dark">{{ $title }}</a></h5>
                            @if ($desc)
                                <p class="flex-grow-1">{{ $desc }}</p>
                            @endif
                            <a href="{{ route('halaman-publik.artikel', $post->slug) }}" class="lp-link-soft mt-2">
                                Baca selengkapnya <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== Events ===== --}}
@if ($events->isNotEmpty())
<section class="lp-section" id="events">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end lp-section-head lp-section-head-sm">
            <div class="lp-reveal" data-from="left">
                <span class="lp-section-eyebrow">Agenda</span>
                <h2 class="lp-section-title mb-0">Acara Mendatang</h2>
            </div>
        </div>

        <div class="row g-3 g-lg-4">
            @foreach ($events as $i => $event)
                <div class="col-md-6 col-lg-6 d-flex">
                    <div class="lp-glass lp-event-card lp-reveal h-100 w-100" data-from="zoom" data-delay="{{ (($i % 3) + 1) }}">
                        <div class="lp-event-date">
                            <span class="day">{{ $event->start_date?->format('d') }}</span>
                            <span class="month">{{ $event->start_date?->translatedFormat('M') }}</span>
                        </div>
                        <div class="lp-event-body">
                            <h5 class="lp-event-title">{{ $event->title }}</h5>
                            @if ($event->location)
                                <div class="lp-event-meta">
                                    <i class="bi bi-geo-alt"></i>
                                    <span>{{ $event->location }}</span>
                                </div>
                            @endif
                            @if ($event->start_time)
                                <div class="lp-event-meta">
                                    <i class="bi bi-clock"></i>
                                    <span>{{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} WIB</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== Galeri ===== --}}
@if ($galleries->isNotEmpty())
<section class="lp-section lp-bg-soft" id="galeri">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center lp-section-head lp-section-head-sm">
            <div class="lp-reveal" data-from="left">
                <span class="lp-section-eyebrow">Momen</span>
                <h2 class="lp-section-title mb-0">Galeri Kegiatan</h2>
            </div>
            <a href="{{ route('halaman-publik.galeri') }}" class="lp-link-soft lp-reveal" data-from="right">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @foreach ($galleries as $i => $item)
                <div class="col-6 col-md-3">
                    <a href="{{ route('halaman-publik.galeri') }}" class="lp-gallery-item lp-reveal d-block" data-from="zoom" data-delay="{{ (($i % 4) + 1) }}">
                        @if ($item->image)
                            <img src="{{ Storage::disk('public')->url('landing/' . $item->image) }}"
                                 alt="{{ $item->title }}" loading="lazy">
                            <div class="lp-gallery-overlay">{{ $item->title }}</div>
                        @else
                            <div class="bg-light w-100 h-100"></div>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== Pengumuman ===== --}}
@if ($announcements->isNotEmpty())
<section class="lp-section" id="pengumuman">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center lp-section-head lp-section-head-sm">
            <div class="lp-reveal" data-from="left">
                <span class="lp-section-eyebrow">Info Terkini</span>
                <h2 class="lp-section-title mb-0">Pengumuman</h2>
            </div>
            <a href="{{ route('halaman-publik.pengumuman') }}" class="lp-link-soft lp-reveal" data-from="right">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @foreach ($announcements as $i => $item)
                <div class="col-md-6 col-lg-4 d-flex">
                    <div class="lp-glass lp-ann-card lp-reveal h-100 w-100 d-flex flex-column" data-from="zoom" data-delay="{{ (($i % 3) + 1) }}">
                        <div class="lp-ann-date">
                            <i class="bi bi-calendar-event"></i>
                            {{ $item->published_at?->translatedFormat('d F Y') }}
                        </div>
                        <h5 class="lp-ann-title">{{ $item->title }}</h5>
                        @if (!empty($item->excerpt))
                            <p class="lp-ann-excerpt">{{ \Illuminate\Support\Str::limit(strip_tags($item->excerpt), 90) }}</p>
                        @endif
                        <a href="{{ route('halaman-publik.pengumuman') }}" class="lp-link-soft mt-auto pt-2">
                            Baca selengkapnya <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== CTA PPDB ===== --}}
@if ($ppdbCtaActive)
<section class="lp-section" id="daftar">
    <div class="container">
        <div class="lp-cta-strip lp-reveal" data-from="zoom">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="lp-cta-eyebrow">
                        <i class="bi bi-megaphone-fill"></i> PPDB {{ $year }}/{{ $nextYear }}
                    </span>
                    <h3>{{ $ppdbTitle }}</h3>
                    <p>{{ $ppdbParagraph }}</p>
                    <div class="lp-cta-registration">
                        {!! nl2br(e($ppdbRegistration)) !!}
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="lp-cta-actions">
                        <a href="{{ route('halaman-publik.ppdb') }}" class="lp-cta-btn">
                            <i class="bi bi-pencil-square"></i>
                            <span>Formulir Pendaftaran</span>
                        </a>
                        <a href="{{ route('halaman-publik.kontak') }}" class="lp-cta-btn-outline">
                            <i class="bi bi-telephone"></i>
                            <span>Kontak</span>
                        </a>
                        <div class="lp-cta-meta">
                            <i class="bi bi-shield-check"></i> Gratis konsultasi sebelum daftar
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var modalEl = document.getElementById('lpWelcomeModal');
    if (!modalEl || typeof bootstrap === 'undefined') return;
    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);

    // Simpan posisi scroll supaya tidak lompat saat body di-lock.
    var scrollY = 0;

    function lockBody() {
        scrollY = window.scrollY || window.pageYOffset || 0;
        document.documentElement.classList.add('lp-welcome-modal-open');
        document.body.classList.add('lp-welcome-modal-open');
    }
    function unlockBody() {
        document.documentElement.classList.remove('lp-welcome-modal-open');
        document.body.classList.remove('lp-welcome-modal-open');
        // Kembalikan posisi scroll setelah lock dilepas
        window.scrollTo(0, scrollY);
    }

    modalEl.addEventListener('show.bs.modal', lockBody);
    modalEl.addEventListener('shown.bs.modal', lockBody);
    modalEl.addEventListener('hide.bs.modal', function () {
        // Cegah scroll restoration bawaan Bootstrap yang kadang konflik.
    });
    modalEl.addEventListener('hidden.bs.modal', unlockBody);
});
</script>
@endsection
