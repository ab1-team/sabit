@extends('landing.layout')

@section('title', ($setting->school_name ?? 'Elite Elementary') . ' — ' . ($setting->tagline ?? 'Sekolah Dasar Terbaik'))

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
        <span class="lp-pill"><span class="dot"></span>SEKOLAH DASAR TERBAIK</span>
        <h1>{{ $hero->title ?? 'Mencetak Generasi Cerdas & Berkarakter' }}</h1>
        <p class="lead">
            {{ $hero->subtitle ?? 'Membangun fondasi kuat untuk masa depan gemilang melalui pendekatan holistik, fasilitas modern, dan lingkungan yang menginspirasi.' }}
        </p>
        <div class="lp-hero-actions">
            @if ($hero && $hero->button_url)
                <a href="{{ $hero->button_url }}" class="lp-btn-light">{{ $hero->button_text ?? 'Daftar PPDB' }} <i class="bi bi-arrow-right ms-1"></i></a>
            @else
                <a href="#daftar" class="lp-btn-light">Daftar PPDB <i class="bi bi-arrow-right ms-1"></i></a>
            @endif
            <a href="#profil" class="lp-btn-outline-light">Profil Sekolah</a>
        </div>
    </div>
</section>

{{-- ===== Stats ===== --}}
<section class="lp-section lp-bg-soft">
    <div class="container">
        <div class="row g-3 g-lg-4">
            <div class="col-md-4">
                <div class="lp-stat-card lp-reveal" data-from="zoom" data-delay="1">
                    <div class="lp-stat-icon is-blue"><i class="bi bi-people-fill"></i></div>
                    <div>
                        <div class="lp-stat-value">1200+</div>
                        <div class="lp-stat-label">Siswa Aktif</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lp-stat-card lp-reveal" data-from="zoom" data-delay="2">
                    <div class="lp-stat-icon is-green"><i class="bi bi-mortarboard-fill"></i></div>
                    <div>
                        <div class="lp-stat-value">150+</div>
                        <div class="lp-stat-label">Guru Berpengalaman</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="lp-stat-card lp-reveal" data-from="zoom" data-delay="3">
                    <div class="lp-stat-icon is-amber"><i class="bi bi-laptop"></i></div>
                    <div>
                        <div class="lp-stat-value">45+</div>
                        <div class="lp-stat-label">Kelas Modern</div>
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
                        <span>"Mendidik dengan Hati."</span>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 lp-reveal" data-from="right">
                <h2 class="lp-section-title">Sambutan Kepala Sekolah</h2>
                <div class="lp-divider"></div>
                <p class="lp-text-muted-soft">
                    Selamat datang di {{ $setting->school_name ?? 'Elite Elementary School' }}. Kami berkomitmen
                    untuk memberikan pengalaman belajar terbaik bagi putra-putri Anda. Diera digital ini, kami
                    memadukan kurikulum nasional dengan standar internasional untuk membentuk karakter yang kuat
                    dan pemikiran yang kritis.
                </p>
                <p class="lp-text-muted-soft">
                    Lingkungan belajar kami dirancang untuk menumbuhkan kreativitas, kolaborasi, dan kemandirian.
                    Bersama-sama, mari kita wujudkan potensi maksimal setiap anak.
                </p>
                <div class="mt-4">
                    <div class="fw-bold text-dark">Dr. Budi Santoso, M.Pd.</div>
                    <div class="text-muted small">Kepala Sekolah, Elite Elementary</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== Keunggulan ===== --}}
<section class="lp-section lp-bg-soft" id="keunggulan">
    <div class="container">
        <div class="text-center mb-5 lp-reveal" data-from="zoom">
            <h2 class="lp-section-title">Keunggulan Kami</h2>
            <p class="lp-section-sub">
                Mengapa {{ $setting->school_name ?? 'Elite Elementary' }} adalah pilihan tepat untuk masa depan anak Anda.
            </p>
        </div>

        <div class="row g-3 g-lg-4">
            <div class="col-lg-6">
                <div class="lp-feature-card h-100 lp-reveal" data-from="left">
                    <div class="lp-feature-icon is-blue"><i class="bi bi-book-fill"></i></div>
                    <h5 class="lp-feature-title">Literasi Kuat</h5>
                    <p class="lp-feature-desc">
                        Program literasi terpadu kami menanamkan kecintaan membaca sejak dini,
                        memperluas wawasan, dan memperkuat kemampuan analitis siswa melalui
                        perpustakaan modern dan program membaca harian.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3 g-lg-4">
                    <div class="col-12">
                        <div class="lp-feature-card lp-reveal" data-from="right" data-delay="1">
                            <div class="lp-feature-icon is-green"><i class="bi bi-shield-check"></i></div>
                            <h5 class="lp-feature-title">Pendidikan Karakter</h5>
                            <p class="lp-feature-desc">
                                Menumbuhkan nilai-nilai disiplin dan integritas dalam setiap aspek kehidupan sekolah.
                            </p>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="lp-feature-card lp-reveal" data-from="right" data-delay="2">
                            <div class="lp-feature-icon is-amber"><i class="bi bi-pc-display"></i></div>
                            <h5 class="lp-feature-title">Digital Learning</h5>
                            <p class="lp-feature-desc">
                                Integrasi teknologi dalam proses pembelajaran yang membuat siswa siap menghadapi era digital.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="lp-feature-card h-100 lp-reveal" data-from="left">
                    <div class="lp-feature-icon is-blue"><i class="bi bi-translate"></i></div>
                    <h5 class="lp-feature-title">Bilingual Environment</h5>
                    <p class="lp-feature-desc">
                        Penggunaan Bahasa Inggris dan Bahasa Indonesia secara aktif dalam
                        keseharian sekolah, mempersiapkan siswa untuk komunikasi global.
                    </p>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="row g-3 g-lg-4">
                    <div class="col-md-6">
                        <div class="lp-feature-card lp-reveal" data-from="zoom" data-delay="1">
                            <div class="lp-feature-icon is-green"><i class="bi bi-palette-fill"></i></div>
                            <h5 class="lp-feature-title">Creative Arts</h5>
                            <p class="lp-feature-desc">
                                Ruang ekspresi seni musik, lukis, dan tari yang mengembangkan kreativitas.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="lp-feature-card lp-reveal" data-from="zoom" data-delay="2">
                            <div class="lp-feature-icon is-amber"><i class="bi bi-trophy-fill"></i></div>
                            <h5 class="lp-feature-title">Achievement</h5>
                            <p class="lp-feature-desc">
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ===== Program Unggulan ===== --}}
<section class="lp-section" id="program">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end mb-4">
            <div>
                <h2 class="lp-section-title mb-2">Program Unggulan</h2>
                <p class="text-muted mb-0">Mendukung minat dan bakat siswa di luar akademik formal.</p>
            </div>
            <a href="{{ route('landing.posts') }}" class="lp-link-soft">Lihat Semua Program <i class="bi bi-arrow-right ms-1"></i></a>
        </div>

        <div class="row g-3 g-lg-4">
            @forelse ($programs as $i => $post)
                @php
                    $cats = ['STEM','Arts','Tech'];
                    $titles = ['Science Explorer Club','Creative Arts Studio','Coding for Kids'];
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
                    <div class="lp-program-card">
                        <img src="{{ $img }}" alt="{{ $title }}">
                        <div class="lp-body">
                            <span class="lp-tag">{{ $cat }}</span>
                            <h5>{{ $title }}</h5>
                            <p>{{ $desc }}</p>
                        </div>
                    </div>
                </div>
            @empty
                @php
                    $fallback = [
                        ['cat'=>'STEM','title'=>'Science Explorer Club','desc'=>'Eksperimen interaktif dan proyek sains sederhana untuk menumbuhkan rasa ingin tahu.','img'=>'https://images.unsplash.com/photo-1587654780291-39c9404d746b?auto=format&fit=crop&w=900&q=80'],
                        ['cat'=>'Arts','title'=>'Creative Arts Studio','desc'=>'Pengembangan bakat seni rupa, musik, dan teater dalam fasilitas yang modern.','img'=>'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?auto=format&fit=crop&w=900&q=80'],
                        ['cat'=>'Tech','title'=>'Coding for Kids','desc'=>'Pengenalan logika pemrograman dasar melalui permainan dan visual interaktif.','img'=>'https://images.unsplash.com/photo-1517694712202-14dd9538aa97?auto=format&fit=crop&w=900&q=80'],
                    ];
                @endphp
                @foreach ($fallback as $f)
                    <div class="col-md-4">
                        <div class="lp-program-card">
                            <img src="{{ $f['img'] }}" alt="{{ $f['title'] }}">
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

@if ($announcements->isNotEmpty())
<section class="lp-section lp-bg-soft">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="lp-section-title mb-0">Pengumuman</h2>
            <a href="{{ route('landing.announcements') }}" class="lp-link-soft">Lihat semua <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            @foreach ($announcements as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="lp-feature-card">
                        <div class="d-flex align-items-center gap-2 text-muted small mb-2">
                            <i class="bi bi-calendar-event"></i>
                            {{ $item->published_at?->translatedFormat('d F Y') }}
                        </div>
                        <h5 class="lp-feature-title mb-0">{{ $item->title }}</h5>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if ($galleries->isNotEmpty())
<section class="lp-section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h2 class="lp-section-title mb-0">Galeri</h2>
            <a href="{{ route('landing.galleries') }}" class="lp-link-soft">Lihat semua <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="row g-3">
            @foreach ($galleries as $item)
                <div class="col-6 col-md-3">
                    <a href="{{ route('landing.galleries') }}" class="d-block">
                        @if ($item->image)
                            <img src="{{ Storage::disk('public')->url('landing/' . $item->image) }}"
                                 class="img-fluid rounded-3 w-100" style="height:170px; object-fit:cover;" alt="{{ $item->title }}">
                        @else
                            <div class="rounded-3 bg-light" style="height:170px;"></div>
                        @endif
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@endsection
