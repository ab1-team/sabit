@extends('landing.tata-letak')

@section('title', 'Profil Sekolah — ' . ($setting->school_name ?? 'Sekolah'))

@section('style')
<style>
    /* ============= Profil Page (sidebar + content) ============= */
    .lp-profile {
        background: linear-gradient(180deg, #f6f8fb 0%, #ffffff 60%);
        min-height: 100vh;
        padding: 7.5rem 0 4rem;
    }

    .lp-profile-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 1.75rem;
        align-items: start;
    }
    @media (max-width: 991.98px) {
        .lp-profile-grid { grid-template-columns: 1fr; }
        .lp-side { position: static; }
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
    }
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
        .lp-vm-grid { grid-template-columns: 1fr; }
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

    /* ---------- Struktur Organisasi (tree-like) ---------- */
    .lp-org {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.06);
        border-radius: var(--lp-radius-lg);
        padding: 1.85rem 1.5rem 2.25rem;
        box-shadow: 0 10px 28px -16px rgba(15, 23, 42, 0.1);
    }
    .lp-org-row {
        display: flex;
        justify-content: center;
        gap: 1rem;
        flex-wrap: wrap;
        position: relative;
    }
    .lp-org-row + .lp-org-row { margin-top: 2rem; }
    .lp-org-row.is-top::before { display: none; }

    .lp-org-card {
        background: #ffffff;
        border: 1px solid rgba(15, 23, 42, 0.08);
        border-radius: var(--lp-radius-md);
        padding: 0.95rem 1rem;
        text-align: center;
        min-width: 180px;
        max-width: 220px;
        flex: 1;
        box-shadow: 0 6px 18px -10px rgba(15, 23, 42, 0.12);
        transition: transform 0.25s var(--lp-ease), box-shadow 0.25s var(--lp-ease);
        position: relative;
    }
    .lp-org-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px -10px rgba(15, 23, 42, 0.15);
    }
    .lp-org-card .avatar {
        width: 64px;
        height: 64px;
        margin: 0 auto 0.7rem;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.15), rgba(var(--lp-primary-rgb), 0.3));
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-size: 1.5rem;
        color: var(--lp-primary);
        font-weight: 700;
    }
    .lp-org-card .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .lp-org-card .avatar .lp-org-initials {
        font-size: 1.15rem;
        font-weight: 700;
        line-height: 1;
    }
    .lp-org-card .avatar .lp-org-photo { display: block; }
    .lp-org-card.is-lead .avatar { background: linear-gradient(135deg, #fde68a, #fcd34d); color: #b45309; }
    .lp-org-card.is-lead { border-color: rgba(217, 119, 6, 0.25); }
    .lp-org-name {
        font-weight: 700;
        font-size: 0.95rem;
        color: var(--lp-text);
        margin-bottom: 0.15rem;
    }
    .lp-org-role {
        display: inline-block;
        font-size: 0.74rem;
        font-weight: 600;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        background: rgba(var(--lp-primary-rgb), 0.08);
        color: var(--lp-primary);
    }
    .lp-org-card.is-lead .lp-org-role { background: rgba(217, 119, 6, 0.12); color: #b45309; }

    /* Connector lines between rows */
    .lp-org-row:not(.is-top) .lp-org-card::before {
        content: "";
        position: absolute;
        top: -1rem;
        left: 50%;
        transform: translateX(-50%);
        width: 2px;
        height: 1rem;
        background: #cbd5e1;
    }

    /* When row has 3+ cards, draw horizontal connector */
    .lp-org-row.multi::after {
        content: "";
        position: absolute;
        top: -1rem;
        left: 16.66%;
        right: 16.66%;
        height: 2px;
        background: #cbd5e1;
    }
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
        $parts = preg_split('/<h3[^>]*>\s*Misi\s*<\/h3>/i', $raw, 2);
        $visiHtml = $parts[0] ?? '';
        $misiHtml = $parts[1] ?? '';
        $visiHtml = preg_replace('/<h3[^>]*>.*?<\/h3>/i', '', $visiHtml, 1);
        $visi = trim(strip_tags($visiHtml)) ?: $visiFallback;

        if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $misiHtml, $m)) {
            $parsed = array_map(fn($x) => trim(strip_tags($x)), $m[1]);
            if (!empty($parsed)) $misi = $parsed;
        }
    };

    if ($visiMisiSection && $visiMisiSection->is_active && $visiMisiSection->content) {
        $parseVisiMisiHtml($visiMisiSection->content);
    } elseif ($pageVisiMisi && $pageVisiMisi->content) {
        $parseVisiMisiHtml($pageVisiMisi->content);
    }

    // ---- Struktur Organisasi: dari DB, fallback statis ----
    $strukturLeads = $strukturItems->where('is_lead', true)->values();
    $strukturMembers = $strukturItems->where('is_lead', false)->values();
    $hasDbStruktur = $strukturItems->isNotEmpty();
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

                <a href="{{ route('landing.kontak') }}" class="lp-side-cta">
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
                                <div class="lp-hero-body">
                                    {!! $overviewSection->content !!}
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

                {{-- Struktur Organisasi --}}
                <div class="lp-section-title-row" id="struktur">
                    <i class="bi bi-diagram-3-fill"></i>
                    <h2>Struktur Organisasi</h2>
                </div>

                @if ($hasDbStruktur)
                    <div class="lp-org">
                        @if ($strukturLeads->isNotEmpty())
                            <div class="lp-org-row is-top">
                                @foreach ($strukturLeads as $p)
                                    <div class="lp-org-card is-lead">
                                        <div class="avatar">
                                            @if ($p->photoUrl())
                                                <img src="{{ $p->photoUrl() }}" alt="{{ $p->name }}" class="lp-org-photo">
                                            @else
                                                <span class="lp-org-initials">{{ $p->initials() }}</span>
                                            @endif
                                        </div>
                                        <div class="lp-org-name">{{ $p->name }}</div>
                                        <span class="lp-org-role">{{ $p->role }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if ($strukturMembers->isNotEmpty())
                            <div class="lp-org-row multi">
                                @foreach ($strukturMembers as $p)
                                    <div class="lp-org-card">
                                        <div class="avatar">
                                            @if ($p->photoUrl())
                                                <img src="{{ $p->photoUrl() }}" alt="{{ $p->name }}" class="lp-org-photo">
                                            @else
                                                <span class="lp-org-initials">{{ $p->initials() }}</span>
                                            @endif
                                        </div>
                                        <div class="lp-org-name">{{ $p->name }}</div>
                                        <span class="lp-org-role">{{ $p->role }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @else
                    {{-- Fallback statis (4 anggota default) --}}
                    @php $struktur = [
                        ['name' => 'Dr. Sarah Wijaya',  'role' => 'Kepala Sekolah',  'lead' => true],
                        ['name' => 'Bpk. Budi Santoso', 'role' => 'Wakil Kurikulum', 'lead' => false],
                        ['name' => 'Ibu Rina Pertiwi',  'role' => 'Wakil Kesiswaan', 'lead' => false],
                        ['name' => 'Bpk. Ahmad Fauzi',  'role' => 'Wakil Sarpras',   'lead' => false],
                    ]; @endphp
                    <div class="lp-org">
                        <div class="lp-org-row is-top">
                            @foreach (array_slice($struktur, 0, 1) as $p)
                                <div class="lp-org-card is-lead">
                                    <div class="avatar">
                                        <span class="lp-org-initials">{{ mb_substr($p['name'], 0, 2) }}</span>
                                    </div>
                                    <div class="lp-org-name">{{ $p['name'] }}</div>
                                    <span class="lp-org-role">{{ $p['role'] }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="lp-org-row multi">
                            @foreach (array_slice($struktur, 1) as $p)
                                <div class="lp-org-card">
                                    <div class="avatar">
                                        <span class="lp-org-initials">{{ mb_substr($p['name'], 0, 2) }}</span>
                                    </div>
                                    <div class="lp-org-name">{{ $p['name'] }}</div>
                                    <span class="lp-org-role">{{ $p['role'] }}</span>
                                </div>
                            @endforeach
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
                            <div class="lp-vm-text">{!! $sejarahSection->content !!}</div>
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
                            <div class="lp-vm-text">{!! $akreditasiSection->content !!}</div>
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
