@extends('halaman-publik.tata-letak')

@section('title', 'Profil Sekolah — ' . ($setting->school_name ?? 'Sekolah'))

@section('style')
<style>
    /* ============= Profil Page (sidebar + content) ============= */
    .lp-profile {
        background: linear-gradient(180deg, #f6f8fb 0%, #ffffff 60%);
        min-height: 100vh;
        padding-bottom: 4rem;
    }

    .lp-profile-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 1.75rem;
        align-items: start;
    }
    @media (max-width: 991.98px) {
        .lp-profile { padding-bottom: 3rem; }
        .lp-profile-grid { grid-template-columns: 1fr; gap: 1.25rem; }
        .lp-side,
        .lp-side-head,
        .lp-side-nav,
        .lp-side-cta { display: none; }
        .lp-hero-card { padding: 1.35rem 1.25rem; }
    }

    /* ---------- Sidebar ---------- */
    .lp-side {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-lg);
        box-shadow: 0 10px 30px -12px rgba(15, 23, 42, 0.1);
        padding: 1.5rem 1.25rem;
        position: sticky;
        top: 110px;
    }
    .lp-side-head {
        text-align: center;
        padding-bottom: 1.25rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #eef2f7;
    }
    .lp-side-logo {
        width: 64px;
        height: 64px;
        margin: 0 auto 0.85rem;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.15), rgba(var(--lp-primary-rgb), 0.3));
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        box-shadow: 0 8px 20px rgba(var(--lp-primary-rgb), 0.18);
    }
    .lp-side-logo img { width: 100%; height: 100%; object-fit: cover; }
    .lp-side-logo i { font-size: 1.75rem; color: var(--lp-primary); }
    .lp-side-title {
        font-weight: 800;
        font-size: 1.02rem;
        line-height: 1.3;
        margin: 0 0 .25rem;
        color: var(--lp-text);
    }
    .lp-side-sub {
        font-size: 0.78rem;
        color: var(--lp-muted);
        font-weight: 500;
    }

    .lp-side-nav {
        display: flex;
        flex-direction: column;
        gap: 0.35rem;
        margin-bottom: 1.25rem;
    }
    .lp-side-link {
        display: flex;
        align-items: center;
        gap: 0.7rem;
        padding: 0.65rem 0.85rem;
        border-radius: 12px;
        font-size: 0.92rem;
        font-weight: 500;
        color: #475569;
        transition: background 0.2s ease, color 0.2s ease;
    }
    .lp-side-link i { font-size: 1rem; color: #94a3b8; transition: color 0.2s ease; }
    .lp-side-link:hover { background: rgba(var(--lp-primary-rgb), 0.06); color: var(--lp-primary); }
    .lp-side-link:hover i { color: var(--lp-primary); }
    .lp-side-link.is-active {
        background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
        color: #fff;
        box-shadow: 0 8px 18px rgba(var(--lp-primary-rgb), 0.3);
    }
    .lp-side-link.is-active i { color: #fff; }

    .lp-side-cta {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.75rem 0.9rem;
        background: rgba(var(--lp-primary-rgb), 0.06);
        color: var(--lp-primary);
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.88rem;
        transition: background 0.2s ease, transform 0.2s ease;
    }
    .lp-side-cta i { font-size: 1rem; }
    .lp-side-cta:hover {
        background: rgba(var(--lp-primary-rgb), 0.12);
        color: var(--lp-primary);
        transform: translateY(-1px);
    }

    /* ---------- Hero Card ---------- */
    .lp-hero-card {
        background: linear-gradient(135deg, #f1f5ff 0%, #ecfeff 100%);
        border: 1px solid rgba(15, 23, 42, 0.05);
        border-radius: var(--lp-radius-xl);
        padding: 1.75rem 1.85rem;
        box-shadow: 0 14px 36px -16px rgba(15, 23, 42, 0.1);
        position: relative;
        overflow: hidden;
    }
    .lp-hero-card::before {
        content: "";
        position: absolute;
        top: -60px;
        right: -60px;
        width: 180px;
        height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(var(--lp-primary-rgb), 0.12), transparent 70%);
    }
    .lp-hero-card::after {
        content: "";
        position: absolute;
        bottom: -80px;
        left: -40px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.1), transparent 70%);
    }
    .lp-hero-card > * { position: relative; z-index: 1; }
    .lp-hero-card h1 {
        font-size: clamp(1.4rem, 2.6vw, 1.85rem);
        font-weight: 800;
        color: var(--lp-text);
        line-height: 1.2;
        margin-bottom: 0.85rem;
        letter-spacing: -0.02em;
    }
    .lp-hero-card .lp-hero-body {
        color: #334155;
        font-size: 0.98rem;
        line-height: 1.7;
        max-width: 720px;
        text-align: justify;
        text-justify: inter-word;
        hyphens: auto;
    }
    .lp-hero-card .lp-hero-body p { margin: 0 0 .85rem; }
    .lp-hero-card .lp-hero-body p:last-child { margin-bottom: 0; }
    .lp-hero-card .lp-hero-body ul,
    .lp-hero-card .lp-hero-body ol { margin: .35rem 0 .85rem; padding-left: 1.25rem; }
    .lp-hero-card .lp-hero-body li { margin-bottom: .35rem; }
    .lp-hero-card .lp-hero-body strong { font-weight: 700; color: #0f172a; }
    .lp-hero-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 0.6rem;
        margin-top: 1.1rem;
    }
    .lp-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.4rem 0.85rem;
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        color: #0f172a;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.04);
    }
    .lp-hero-badge i { color: var(--lp-primary); font-size: 0.95rem; }
    .lp-hero-badge.is-green i { color: #059669; }

    /* ---------- Section Heading ---------- */
    .lp-section-title-row {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 2.25rem 0 1rem;
        color: var(--lp-text);
    }
    .lp-section-title-row i { font-size: 1.4rem; color: var(--lp-muted); }
    .lp-section-title-row h2 {
        font-weight: 800;
        font-size: 1.35rem;
        margin: 0;
        letter-spacing: -0.01em;
    }

    /* ---------- Visi & Misi cards ---------- */
    .lp-vm-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
    }
    @media (max-width: 767.98px) {
        .lp-profile { padding-bottom: 2.5rem; }
        .lp-vm-grid { grid-template-columns: 1fr; }
        .lp-vm-card { padding: 1.2rem 1.15rem; }
        .lp-section-title-row { margin: 1.75rem 0 0.85rem; }
        .lp-section-title-row h2 { font-size: 1.15rem; }
        .lp-hero-card h1 { font-size: 1.35rem; }
    }
    .lp-vm-card {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-lg);
        padding: 1.5rem 1.6rem;
        box-shadow: 0 10px 28px -16px rgba(15, 23, 42, 0.1);
        position: relative;
        overflow: hidden;
    }
    .lp-vm-card::before {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    .lp-vm-card.is-visi::before { background: linear-gradient(180deg, var(--lp-primary), var(--lp-accent-2)); }
    .lp-vm-card.is-misi::before  { background: linear-gradient(180deg, var(--lp-accent), #f97316); }
    .lp-vm-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 0.85rem;
    }
    .lp-vm-card.is-visi .lp-vm-icon { background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.15), rgba(var(--lp-primary-rgb), 0.3)); color: var(--lp-primary); }
    .lp-vm-card.is-misi  .lp-vm-icon { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
    .lp-vm-card h3 {
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 0.6rem;
    }
    .lp-vm-card.is-visi h3 { color: var(--lp-text); }
    .lp-vm-card.is-misi  h3 { color: #d97706; }
    .lp-vm-text {
        color: #334155;
        font-size: 0.94rem;
        line-height: 1.7;
    }
    .lp-vm-text ol, .lp-vm-text ul {
        padding-left: 1.15rem;
        margin: 0;
    }
    .lp-vm-text ol li, .lp-vm-text ul li {
        margin-bottom: 0.45rem;
    }
    .lp-vm-text p:last-child { margin-bottom: 0; }
</style>
@endsection

@section('content')

@php
    $schoolName = $setting->school_name ?? 'Elite Elementary';
    $logoUrl = $setting->logo
        ? Storage::disk('public')->url('landing/' . $setting->logo)
        : null;

    // ---- Visi & Misi: preferensi lp_profile_sections, fallback ke lp_pages / statis ----
    $visiFallback = 'Menjadi institusi pendidikan dasar terdepan yang menghasilkan generasi berkarakter unggul, berwawasan global, dan berakhlak mulia.';
    $misiFallback = [
        'Menyelenggarakan pembelajaran aktif, inovatif, efektif, dan menyenangkan.',
        'Menumbuhkan penghayatan nilai keagamaan, budaya, dan karakter.',
        'Mengembangkan potensi peserta didik secara optimal sesuai bakat dan minat.',
        'Membangun lingkungan sekolah yang aman, nyaman, dan inklusif.',
    ];

    $visi = $visiFallback;
    $misi = $misiFallback;

    $parseVisiMisiHtml = function (string $html) use (&$visi, &$misi, $visiFallback, $misiFallback) {
        $raw = strip_tags($html, '<p><br><strong><em><h3><ul><ol><li>');

        // 1) Pisahkan blok Visi vs Misi jika ada heading <h3>Misi</h3>.
        //    Visi = semua sebelum heading itu (heading Visi dihapus).
        //    Misi = semua setelahnya.
        if (preg_split('/<h3[^>]*>\s*Misi\s*<\/h3>/i', $raw, 2, PREG_SPLIT_NO_EMPTY) !== false) {
            $parts = preg_split('/<h3[^>]*>\s*Misi\s*<\/h3>/i', $raw, 2);
            if (count($parts) === 2) {
                $visiHtml = preg_replace('/<h3[^>]*>.*?<\/h3>/i', '', $parts[0], 1);
                $visiText = trim(preg_replace('/\s+/', ' ', strip_tags($visiHtml)));
                if ($visiText !== '') $visi = $visiText;
                $misiHtml = $parts[1];
            }
        }

        // 2) Ambil item misi dari <li> / <ol> / <ul>.
        $parsedMisi = [];
        if (!empty($misiHtml) && preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $misiHtml, $m)) {
            foreach ($m[1] as $li) {
                $txt = trim(preg_replace('/\s+/', ' ', strip_tags($li)));
                if ($txt !== '') $parsedMisi[] = $txt;
            }
        }

        // 3) Fallback: jika tidak ada <li>, ambil paragraf bernomor "1.", "2.", dst di dalam blok misi.
        if (empty($parsedMisi) && !empty($misiHtml)) {
            foreach (preg_split('/<br\s*\/?>|\<\/p\>\s*\<p[^>]*\>/i', strip_tags($misiHtml, '<br><p>')) as $line) {
                $line = trim(preg_replace('/\s+/', ' ', $line));
                if ($line !== '') $parsedMisi[] = $line;
            }
        }

        if (!empty($parsedMisi)) $misi = array_values(array_unique($parsedMisi));
    };

    if ($visiMisiSection && $visiMisiSection->is_active && $visiMisiSection->content) {
        $parseVisiMisiHtml($visiMisiSection->content);
    } elseif ($pageVisiMisi && $pageVisiMisi->content) {
        $parseVisiMisiHtml($pageVisiMisi->content);
    }
@endphp

<section class="lp-profile">
    <div class="container">
        <div class="lp-profile-grid">

            {{-- ============= SIDEBAR ============= --}}
            <aside class="lp-side">
                <div class="lp-side-head">
                    <div class="lp-side-logo">
                        @if ($logoUrl)
                            <img src="{{ $logoUrl }}" alt="{{ $schoolName }}">
                        @else
                            <i class="bi bi-mortarboard-fill"></i>
                        @endif
                    </div>
                    <h5 class="lp-side-title">Profil Sekolah</h5>
                    <div class="lp-side-sub">Navigasi Internal</div>
                </div>

                <nav class="lp-side-nav" id="lp-profile-side-nav">
                    @if (!$overviewSection || $overviewSection->is_active)
                        <a href="#overview" class="lp-side-link is-active" data-section="overview">
                            <i class="bi bi-eye-fill"></i> Tinjauan
                        </a>
                    @endif
                    @if (!$sejarahSection || $sejarahSection->is_active)
                        <a href="#history" class="lp-side-link {{ (!$overviewSection || $overviewSection->is_active) ? '' : 'is-active' }}" data-section="history">
                            <i class="bi bi-clock-history"></i> Sejarah
                        </a>
                    @endif
                    @if (!$visiMisiSection || $visiMisiSection->is_active)
                        <a href="#visi-misi" class="lp-side-link" data-section="visi-misi">
                            <i class="bi bi-bullseye"></i> Visi &amp; Misi
                        </a>
                    @endif
                    @if (!$akreditasiSection || $akreditasiSection->is_active)
                        <a href="#akreditasi" class="lp-side-link" data-section="akreditasi">
                            <i class="bi bi-patch-check-fill"></i> Akreditasi
                        </a>
                    @endif
                    @if ($fasilitasItems->isNotEmpty())
                        <a href="#fasilitas" class="lp-side-link" data-section="fasilitas">
                            <i class="bi bi-building"></i> Fasilitas
                        </a>
                    @endif
                </nav>

                <a href="{{ route('halaman-publik.kontak') }}" class="lp-side-cta">
                    <i class="bi bi-chat-dots-fill"></i> Hubungi Admin
                </a>
            </aside>

            {{-- ============= CONTENT ============= --}}
            <main>
                {{-- Hero / Overview --}}
                @if (!$overviewSection || $overviewSection->is_active)
                    <div class="lp-hero-card" id="overview">
                        @if ($overviewSection && $overviewSection->is_active)
                            <h1>{{ $overviewSection->title ?: 'Profil Sekolah' }}</h1>
                            @if ($overviewSection->subtitle)
                                <div class="lp-hero-body">
                                    <p class="lp-text-muted-soft">{{ $overviewSection->subtitle }}</p>
                                </div>
                            @endif
                            @if ($overviewSection->content)
                                @php
                                    $rawContent = $overviewSection->content;
                                    $hasBlockTags = (bool) preg_match('/<(p|br|div|ul|ol|h[1-6])\b/i', $rawContent);
                                    $renderedContent = $hasBlockTags
                                        ? $rawContent
                                        : '<p>' . nl2br(e($rawContent)) . '</p>';
                                @endphp
                                <div class="lp-hero-body">
                                    {!! $renderedContent !!}
                                </div>
                            @endif
                            @if ($overviewSection->badge_text || $overviewSection->badge_extra)
                                <div class="lp-hero-badges">
                                    @if ($overviewSection->badge_text)
                                        <span class="lp-hero-badge is-green">
                                            <i class="bi bi-patch-check-fill"></i> {{ $overviewSection->badge_text }}
                                        </span>
                                    @endif
                                    @if ($overviewSection->badge_extra)
                                        <span class="lp-hero-badge">
                                            <i class="bi bi-hash"></i> {{ $overviewSection->extra_label ?: 'NPSN' }}: {{ $overviewSection->badge_extra }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        @else
                            <h1>Selamat Datang di {{ $schoolName }}</h1>
                            <div class="lp-hero-body">
                                <p class="lp-text-muted-soft">Konten tinjauan belum diatur. Silakan aktifkan section <strong>Tinjauan</strong> di menu <em>Landing Page &raquo; Profil</em>.</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Visi & Misi --}}
                @if (!$visiMisiSection || $visiMisiSection->is_active)
                    <div class="lp-section-title-row" id="visi-misi">
                        <i class="bi bi-bullseye"></i>
                        <h2>{{ $visiMisiSection && $visiMisiSection->is_active && $visiMisiSection->title ? $visiMisiSection->title : 'Visi & Misi' }}</h2>
                    </div>

                    <div class="lp-vm-grid">
                        <div class="lp-vm-card is-visi">
                            <div class="lp-vm-icon"><i class="bi bi-eye-fill"></i></div>
                            <h3>Visi Kami</h3>
                            <div class="lp-vm-text">
                                <p>{{ $visi }}</p>
                            </div>
                        </div>
                        <div class="lp-vm-card is-misi">
                            <div class="lp-vm-icon"><i class="bi bi-flag-fill"></i></div>
                            <h3>Misi Kami</h3>
                            <div class="lp-vm-text">
                                <ol>
                                    @foreach ($misi as $m)
                                        <li>{{ $m }}</li>
                                    @endforeach
                                </ol>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Sejarah --}}
                @if (!$sejarahSection || $sejarahSection->is_active)
                    <div class="lp-section-title-row" id="history">
                        <i class="bi bi-clock-history"></i>
                        <h2>{{ $sejarahSection && $sejarahSection->is_active ? $sejarahSection->title : 'Sejarah' }}</h2>
                    </div>

                    <div class="lp-hero-card" style="padding: 1.5rem 1.85rem;">
                        @if ($sejarahSection && $sejarahSection->is_active && $sejarahSection->content)
                            @php
                                $rawSejarah = $sejarahSection->content;
                                $hasBlockTagsSejarah = (bool) preg_match('/<(p|br|div|ul|ol|h[1-6])\b/i', $rawSejarah);
                                $renderedSejarah = $hasBlockTagsSejarah
                                    ? $rawSejarah
                                    : '<p>' . nl2br(e($rawSejarah)) . '</p>';
                            @endphp
                            <div class="lp-vm-text">{!! $renderedSejarah !!}</div>
                        @else
                            <p class="lp-text-muted-soft" style="margin:0; font-size:0.96rem;">
                                Didirikan sejak tahun 1995, {{ $schoolName }} telah berkembang menjadi lembaga
                                pendidikan yang dipercaya masyarakat. Perjalanan panjang ini ditandai dengan
                                berbagai inovasi pembelajaran dan pencapaian prestasi di tingkat kota, provinsi,
                                hingga nasional.
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Akreditasi --}}
                @if (!$akreditasiSection || $akreditasiSection->is_active)
                    <div class="lp-section-title-row" id="akreditasi">
                        <i class="bi bi-patch-check-fill"></i>
                        <h2>{{ $akreditasiSection && $akreditasiSection->is_active ? $akreditasiSection->title : 'Akreditasi' }}</h2>
                    </div>

                    <div class="lp-vm-card is-visi" style="padding: 1.5rem 1.6rem;">
                        <div class="lp-vm-icon"><i class="bi bi-award-fill"></i></div>
                        <h3>{{ $akreditasiSection && $akreditasiSection->is_active && $akreditasiSection->badge_text ? $akreditasiSection->badge_text : 'Terakreditasi A' }}</h3>
                        @if ($akreditasiSection && $akreditasiSection->is_active && $akreditasiSection->content)
                            @php
                                $rawAkred = $akreditasiSection->content;
                                $hasBlockTagsAkred = (bool) preg_match('/<(p|br|div|ul|ol|h[1-6])\b/i', $rawAkred);
                                $renderedAkred = $hasBlockTagsAkred
                                    ? $rawAkred
                                    : '<p>' . nl2br(e($rawAkred)) . '</p>';
                            @endphp
                            <div class="lp-vm-text">{!! $renderedAkred !!}</div>
                        @else
                            <p class="lp-text-muted-soft" style="margin:0; font-size:0.94rem;">
                                Status akreditasi A (Sangat Baik) diberikan oleh BAN-SM, mencerminkan
                                komitmen kami terhadap mutu pendidikan, manajemen sekolah, dan
                                pencapaian lulusan yang berkualitas.
                            </p>
                        @endif
                    </div>
                @endif

                {{-- Fasilitas --}}
                <div class="lp-section-title-row" id="fasilitas">
                    <i class="bi bi-building"></i>
                    <h2>Fasilitas</h2>
                </div>

                @if ($fasilitasItems->isNotEmpty())
                    @php $fasColors = ['is-visi', 'is-misi']; @endphp
                    <div class="lp-vm-grid">
                        @foreach ($fasilitasItems as $i => $f)
                            <div class="lp-vm-card {{ $fasColors[$i % 2] }}">
                                <div class="lp-vm-icon"><i class="bi {{ $f->iconClass() }}"></i></div>
                                <h3>{{ $f->title }}</h3>
                                <p class="lp-vm-text">{{ $f->description }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    {{-- Seharusnya tidak pernah kosong karena controller menyisipkan
                       default seragam dengan halaman admin. Fallback ini hanya
                       pengaman bila tabel lp_fasilitas benar-benar kosong. --}}
                    <div class="lp-vm-grid">
                        <div class="lp-vm-card is-visi">
                            <div class="lp-vm-icon"><i class="bi bi-easel"></i></div>
                            <h3>Ruang Kelas Modern</h3>
                            <p class="lp-vm-text">Ruang belajar nyaman dengan pendingin ruangan, proyektor,
                            dan akses internet untuk mendukung pembelajaran digital.</p>
                        </div>
                        <div class="lp-vm-card is-misi">
                            <div class="lp-vm-icon"><i class="bi bi-cpu"></i></div>
                            <h3>Laboratorium &amp; Perpustakaan</h3>
                            <p class="lp-vm-text">Laboratorium IPA, komputer, dan perpustakaan digital
                            dengan koleksi buku yang lengkap untuk mendukung eksplorasi siswa.</p>
                        </div>
                    </div>
                @endif

            </main>
        </div>
    </div>
</section>

@endsection

@section('script')
<script>
(function () {
    // Identik dengan pola scroll-spy halaman PPDB
    var sideLinks = document.querySelectorAll('#lp-profile-side-nav .lp-side-link[data-section]');
    var sectionIds = ['overview', 'history', 'visi-misi', 'akreditasi', 'fasilitas'];
    var sections = sectionIds
        .map(function (id) { return document.getElementById(id); })
        .filter(Boolean);

    function setActive(id) {
        sideLinks.forEach(function (a) {
            a.classList.toggle('is-active', a.getAttribute('data-section') === id);
        });
    }

    sideLinks.forEach(function (a) {
        a.addEventListener('click', function (e) {
            var id = a.getAttribute('data-section');
            var el = document.getElementById(id);
            if (!el) return;
            e.preventDefault();
            var top = el.getBoundingClientRect().top + window.scrollY - 110;
            window.scrollTo({ top: top, behavior: 'smooth' });
            history.replaceState(null, '', '#' + id);
            setActive(id);
        });
    });

    // Auto-highlight section yang sedang terlihat saat scroll
    if ('IntersectionObserver' in window && sections.length) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    setActive(entry.target.id);
                }
            });
        }, { rootMargin: '-30% 0px -55% 0px' });

        sections.forEach(function (el) { io.observe(el); });
    }

    // Set active sesuai hash saat pertama load
    var hash = window.location.hash.replace('#', '');
    if (hash && sectionIds.indexOf(hash) !== -1) {
        setActive(hash);
    }
})();
</script>
@endsection
