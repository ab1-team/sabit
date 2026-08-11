@extends('landing.tata-letak')

@section('title', ($setting->school_name ?? 'Sekolah') . ' — ' . ($setting->tagline ?? 'Sekolah Dasar Terbaik'))

@php
    $hero = $slides->first();
    $heroImage = $hero && $hero->image
        ? Storage::disk('public')->url('landing/' . $hero->image)
        : 'https://images.unsplash.com/photo-1580582932707-520aed937b7b?auto=format&fit=crop&w=1600&q=80';

    $programs = $posts->take(3);
@endphp

@section('content')

{{-- ===== Hero ===== --}}
<section class="lp-hero">
    <div class="lp-hero-bg" style="background-image:url('{{ $heroImage }}')"></div>
    <div class="container lp-hero-content">
        <h1 class="lp-reveal" data-from="zoom">{{ $hero->title ?? 'Mendidik dengan Hati, Meraih Prestasi' }}</h1>
        <p class="lead lp-reveal" data-delay="2">
            {{ $hero->subtitle ?? 'Membangun generasi yang berkarakter, cerdas, dan siap menghadapi tantangan masa depan melalui pembelajaran yang inspiratif dan bermakna.' }}
        </p>
        <div class="lp-hero-actions lp-reveal" data-delay="3">
            @if ($hero && $hero->button_url && $hero->button_url !== '#daftar')
                <a href="{{ $hero->button_url }}" class="lp-btn-light">{{ $hero->button_text ?? 'Daftar PPDB' }} <i class="bi bi-arrow-right"></i></a>
            @else
                <a href="{{ route('landing.ppdb') }}" class="lp-btn-light">Daftar PPDB 2026/2027 <i class="bi bi-arrow-right"></i></a>
            @endif
            <a href="{{ tenant()?->adminUrl() ?: '#' }}" class="lp-btn-outline-light">SabIT</a>
        </div>
        <div class="lp-reveal mt-4 d-flex flex-wrap justify-content-center gap-2" data-delay="4">
            <span class="lp-badge"><i class="bi bi-patch-check-fill"></i> Terakreditasi A</span>
            <span class="lp-badge"><i class="bi bi-trophy-fill"></i> 50+ Prestasi 2025</span>
        </div>
    </div>
</section>

{{-- ===== Stats ===== --}}
<section class="lp-section lp-bg-soft" id="stats">
    <div class="container">
        <div class="row g-3 g-lg-4">
            <div class="col-md-4">
                <div class="lp-glass lp-stat-card lp-reveal" data-from="zoom" data-delay="1">
                    <div class="lp-stat-icon is-blue"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="lp-stat-value">1.200+</div>
                        <div class="lp-stat-label">Siswa Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lp-glass lp-stat-card lp-reveal" data-from="zoom" data-delay="2">
                    <div class="lp-stat-icon is-green"><i class="bi bi-mortarboard-fill"></i></div>
                    <div>
                        <div class="lp-stat-value">85+</div>
                        <div class="lp-stat-label">Guru &amp; Staf Pengajar</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lp-glass lp-stat-card lp-reveal" data-from="zoom" data-delay="3">
                    <div class="lp-stat-icon is-amber"><i class="bi bi-trophy-fill"></i></div>
                    <div>
                        <div class="lp-stat-value">120+</div>
                        <div class="lp-stat-label">Prestasi Diraih</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== Sambutan Kepala Sekolah ===== --}}
<section class="lp-section" id="profil">
    <div class="container">
        <div class="row align-items-center g-4 g-lg-5">
            <div class="col-lg-5">
                <div class="lp-welcome-img lp-reveal" data-from="left">
                    <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=900&q=80"
                         alt="Kepala Sekolah">
                    <div class="lp-welcome-quote">
                        <i class="bi bi-quote"></i>
                        <span>"Mendidik dengan Hati, Membentuk dengan Karakter."</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 lp-reveal" data-from="right">
                <span class="lp-section-eyebrow">Sambutan</span>
                <h2 class="lp-section-title">Sambutan Kepala Sekolah</h2>
                <div class="lp-divider"></div>
                <p class="lp-text-muted-soft">
                    Selamat datang di {{ $setting->school_name ?? 'Sekolah' }}. Kami berkomitmen
                    untuk memberikan pengalaman belajar terbaik bagi putra-putri Anda. Di era digital ini, kami
                    memadukan kurikulum nasional dengan standar internasional untuk membentuk karakter yang kuat
                    dan pemikiran yang kritis.
                </p>
                <p class="lp-text-muted-soft">
                    Lingkungan belajar kami dirancang untuk menumbuhkan kreativitas, kolaborasi, dan kemandirian.
                    Bersama-sama, mari kita wujudkan potensi maksimal setiap anak.
                </p>
                <div class="mt-4 d-flex align-items-center gap-3">
                    <div class="lp-stat-icon is-blue" style="width:48px; height:48px; font-size:1.1rem;">
                        <i class="bi bi-person-badge"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-dark">Dr. Budi Santoso, M.Pd.</div>
                        <div class="text-muted small">Kepala Sekolah, {{ $setting->school_name ?? 'Sekolah' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== Jenjang Pendidikan ===== --}}
<section class="lp-section lp-bg-soft" id="jenjang">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Jenjang Pendidikan</span>
            <h2 class="lp-section-title">Tumbuh Bersama di Setiap Jenjang</h2>
            <p class="lp-section-sub">Pendidikan terpadu dari usia dini hingga sekolah menengah atas dengan kurikulum yang adaptif.</p>
        </div>

        <div class="row g-3 g-lg-4">
            <div class="col-md-6 col-lg-3">
                <div class="lp-glass lp-jenjang-card tk lp-reveal h-100" data-from="zoom" data-delay="1">
                    <div class="lp-jenjang-icon"><i class="bi bi-emoji-smile-fill"></i></div>
                    <span class="lp-jenjang-age">USIA 4–6 TAHUN</span>
                    <h5 class="lp-jenjang-title">TK / PAUD</h5>
                    <p class="lp-jenjang-desc">Pembelajaran bermain sambil belajar dengan pendekatan tematik untuk menumbuhkan rasa ingin tahu.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="lp-glass lp-jenjang-card sd lp-reveal h-100" data-from="zoom" data-delay="2">
                    <div class="lp-jenjang-icon"><i class="bi bi-pencil-square"></i></div>
                    <span class="lp-jenjang-age">KELAS 1–6</span>
                    <h5 class="lp-jenjang-title">Sekolah Dasar</h5>
                    <p class="lp-jenjang-desc">Fondasi akademik yang kuat dengan literasi, numerasi, dan pengembangan karakter.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="lp-glass lp-jenjang-card smp lp-reveal h-100" data-from="zoom" data-delay="3">
                    <div class="lp-jenjang-icon"><i class="bi bi-backpack3-fill"></i></div>
                    <span class="lp-jenjang-age">KELAS 7–9</span>
                    <h5 class="lp-jenjang-title">SMP</h5>
                    <p class="lp-jenjang-desc">Pendidikan menengah dengan eksplorasi minat, berpikir kritis, dan kepemimpinan.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="lp-glass lp-jenjang-card sma lp-reveal h-100" data-from="zoom" data-delay="4">
                    <div class="lp-jenjang-icon"><i class="bi bi-mortarboard-fill"></i></div>
                    <span class="lp-jenjang-age">KELAS 10–12</span>
                    <h5 class="lp-jenjang-title">SMA</h5>
                    <p class="lp-jenjang-desc">Persiapan masuk perguruan tinggi terbaik dengan program akselerasi &amp; bimbingan karir.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== Keunggulan ===== --}}
<section class="lp-section" id="keunggulan">
    <div class="container">
        <div class="text-center lp-section-head lp-reveal" data-from="zoom">
            <span class="lp-section-eyebrow">Mengapa Kami</span>
            <h2 class="lp-section-title">Keunggulan Sekolah Kami</h2>
            <p class="lp-section-sub">Komitmen kami untuk memberikan pendidikan terbaik bagi setiap siswa.</p>
        </div>

        <div class="row g-3 g-lg-4">
            <div class="col-lg-6">
                <div class="lp-glass lp-feature-card lp-reveal h-100" data-from="left">
                    <div class="lp-feature-icon is-blue"><i class="bi bi-book-fill"></i></div>
                    <h5 class="lp-feature-title">Kurikulum Merdeka</h5>
                    <p class="lp-feature-desc">
                        Penerapan kurikulum merdeka dengan pembelajaran yang fleksibel, mendalam, dan menyenangkan,
                        mengembangkan potensi sesuai minat &amp; bakat siswa.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3 g-lg-4">
                    <div class="col-12">
                        <div class="lp-glass lp-feature-card lp-reveal" data-from="right" data-delay="1">
                            <div class="lp-feature-icon is-green"><i class="bi bi-shield-check"></i></div>
                            <h5 class="lp-feature-title">Pendidikan Karakter</h5>
                            <p class="lp-feature-desc">
                                Menumbuhkan nilai-nilai disiplin, integritas, dan kepemimpinan dalam setiap aspek kehidupan sekolah.
                            </p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="lp-glass lp-feature-card lp-reveal" data-from="right" data-delay="2">
                            <div class="lp-feature-icon is-amber"><i class="bi bi-pc-display"></i></div>
                            <h5 class="lp-feature-title">Digital Learning</h5>
                            <p class="lp-feature-desc">
                                Integrasi teknologi dalam pembelajaran dengan laboratorium komputer dan multimedia modern.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="lp-glass lp-feature-card lp-reveal h-100" data-from="left">
                    <div class="lp-feature-icon" style="background: linear-gradient(135deg, #fce7f3, #fbcfe8); color: var(--lp-pink);">
                        <i class="bi bi-translate"></i>
                    </div>
                    <h5 class="lp-feature-title">Lingkungan Dwibahasa</h5>
                    <p class="lp-feature-desc">
                        Penggunaan Bahasa Inggris &amp; Indonesia secara aktif dalam keseharian sekolah,
                        mempersiapkan siswa untuk komunikasi global.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3 g-lg-4">
                    <div class="col-md-6">
                        <div class="lp-glass lp-feature-card lp-reveal h-100" data-from="zoom" data-delay="1">
                            <div class="lp-feature-icon" style="background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: var(--lp-purple);">
                                <i class="bi bi-palette-fill"></i>
                            </div>
                            <h5 class="lp-feature-title">Seni &amp; Kreativitas</h5>
                            <p class="lp-feature-desc">
                                Ruang ekspresi seni musik, lukis, dan tari yang mengembangkan kreativitas anak.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="lp-glass lp-feature-card lp-reveal h-100" data-from="zoom" data-delay="2">
                            <div class="lp-feature-icon" style="background: linear-gradient(135deg, #cffafe, #a5f3fc); color: var(--lp-accent-2);">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                            <h5 class="lp-feature-title">Prestasi Gemilang</h5>
                            <p class="lp-feature-desc">
                                Ratusan prestasi akademik &amp; non-akademik tingkat kota hingga nasional.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== Program Unggulan ===== --}}
<section class="lp-section lp-bg-soft" id="program">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end lp-section-head lp-section-head-sm">
            <div class="lp-reveal" data-from="left">
                <span class="lp-section-eyebrow">Program</span>
                <h2 class="lp-section-title mb-2">Program Unggulan</h2>
                <p class="text-muted mb-0">Mengembangkan minat dan bakat siswa di luar akademik formal.</p>
            </div>
            <a href="{{ route('landing.daftar-artikel') }}" class="lp-link-soft lp-reveal" data-from="right">Lihat Semua Program <i class="bi bi-arrow-right"></i></a>
        </div>

        <div class="row g-3 g-lg-4">
            @forelse ($programs as $i => $post)
                @php
                    $cats = ['STEM','Seni','Teknologi'];
                    $titles = ['Klub Eksplorasi Sains','Studio Seni Kreatif','Coding untuk Anak'];
                    $descs = [
                        'Eksperimen interaktif dan proyek sains sederhana untuk menumbuhkan rasa ingin tahu.',
                        'Pengembangan bakat seni rupa, musik, dan teater dalam fasilitas yang modern.',
                        'Pengenalan logika pemrograman dasar melalui permainan dan visual interaktif.',
                    ];
                    $imgs = [
                        'https://images.unsplash.com/photo-1587654780291-39c9404d746b?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=900&q=80',
                        'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=900&q=80',
                    ];
                    $cat = $post->category ?? $cats[$i % 3];
                    $title = $post->title ?? $titles[$i % 3];
                    $desc = \Illuminate\Support\Str::limit(strip_tags($post->excerpt ?: $post->content), 110) ?: $descs[$i % 3];
                    $img = $post->image
                        ? Storage::disk('public')->url('landing/' . $post->image)
                        : $imgs[$i % 3];
                @endphp
                <div class="col-md-4">
                    <div class="lp-program-card lp-reveal" data-from="zoom" data-delay="{{ $i + 1 }}">
                        <a href="{{ route('landing.artikel', $post->slug) }}" class="lp-thumb">
                            <img src="{{ $img }}" alt="{{ $title }}" loading="lazy">
                        </a>
                        <div class="lp-body">
                            <span class="lp-tag">{{ $cat }}</span>
                            <h5><a href="{{ route('landing.artikel', $post->slug) }}" class="text-dark">{{ $title }}</a></h5>
                            <p>{{ $desc }}</p>
                        </div>
                    </div>
                </div>
            @empty
                @php
                    $fallback = [
                        ['cat'=>'STEM','title'=>'Klub Eksplorasi Sains','desc'=>'Eksperimen interaktif dan proyek sains sederhana untuk menumbuhkan rasa ingin tahu.','img'=>'https://images.unsplash.com/photo-1587654780291-39c9404d746b?auto=format&fit=crop&w=900&q=80'],
                        ['cat'=>'Seni','title'=>'Studio Seni Kreatif','desc'=>'Pengembangan bakat seni rupa, musik, dan teater dalam fasilitas yang modern.','img'=>'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=900&q=80'],
                        ['cat'=>'Teknologi','title'=>'Coding untuk Anak','desc'=>'Pengenalan logika pemrograman dasar melalui permainan dan visual interaktif.','img'=>'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=900&q=80'],
                    ];
                @endphp
                @foreach ($fallback as $i => $f)
                    <div class="col-md-4">
                        <div class="lp-program-card lp-reveal" data-from="zoom" data-delay="{{ $i + 1 }}">
                            <div class="lp-thumb">
                                <img src="{{ $f['img'] }}" alt="{{ $f['title'] }}" loading="lazy">
                            </div>
                            <div class="lp-body">
                                <span class="lp-tag">{{ $f['cat'] }}</span>
                                <h5>{{ $f['title'] }}</h5>
                                <p>{{ $f['desc'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforelse
        </div>
    </div>
</section>

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
                <div class="col-md-6">
                    <div class="lp-glass lp-event-card lp-reveal" data-from="zoom" data-delay="{{ (($i % 3) + 1) }}">
                        <div class="lp-event-date">
                            <span class="day">{{ $event->start_date?->format('d') }}</span>
                            <span class="month">{{ $event->start_date?->translatedFormat('M') }}</span>
                        </div>
                        <div>
                            <h5 class="lp-event-title">{{ $event->title }}</h5>
                            @if ($event->location)
                                <div class="lp-event-meta">
                                    <i class="bi bi-geo-alt"></i>
                                    {{ $event->location }}
                                </div>
                            @endif
                            @if ($event->start_time)
                                <div class="lp-event-meta">
                                    <i class="bi bi-clock"></i>
                                    {{ \Carbon\Carbon::parse($event->start_time)->format('H:i') }} WIB
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
            <a href="{{ route('landing.galeri') }}" class="lp-link-soft lp-reveal" data-from="right">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @foreach ($galleries as $i => $item)
                <div class="col-6 col-md-3">
                    <a href="{{ route('landing.galeri') }}" class="lp-gallery-item lp-reveal d-block" data-from="zoom" data-delay="{{ (($i % 4) + 1) }}">
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
            <a href="{{ route('landing.pengumuman') }}" class="lp-link-soft lp-reveal" data-from="right">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="row g-3">
            @foreach ($announcements as $i => $item)
                <div class="col-md-6 col-lg-4">
                    <div class="lp-glass lp-ann-card lp-reveal" data-from="zoom" data-delay="{{ (($i % 3) + 1) }}">
                        <div class="lp-ann-date">
                            <i class="bi bi-calendar-event"></i>
                            {{ $item->published_at?->translatedFormat('d F Y') }}
                        </div>
                        <h5>{{ $item->title }}</h5>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ===== CTA PPDB ===== --}}
<section class="lp-section" id="daftar">
    <div class="container">
        <div class="lp-cta-strip lp-reveal" data-from="zoom">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h3>Penerimaan Peserta Didik Baru 2026/2027 Telah Dibuka!</h3>
                    <p>Daftarkan putra-putri Anda sekarang dan jadilah bagian dari keluarga besar {{ $setting->school_name ?? 'sekolah kami' }}. Kuota terbatas untuk setiap jenjang.</p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="{{ route('landing.kontak') }}" class="lp-cta-btn">
                        <i class="bi bi-pencil-square"></i> Daftar Sekarang
                    </a>
                    <div class="mt-2">
                        <a href="{{ route('landing.kontak') }}" class="lp-cta-btn-outline">
                            <i class="bi bi-telephone"></i> Hubungi Kami
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
