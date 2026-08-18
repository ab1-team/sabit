@extends('layouts.tenant.base')

@section('style')
    @include('admin-landing._gaya')
@endsection

@section('content')
<div class="px-2 py-2">
    @if (session('success'))
        <div class="alert alert-success py-2 small mb-3">{{ session('success') }}</div>
    @endif

    {{-- Hero banner ringkas --}}
    <div class="lp-admin-hero mb-3">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="lp-admin-hero-icon">
                    <span class="material-symbols-rounded">language</span>
                </div>
                <div>
                    <div class="lp-page-eyebrow" style="color:rgba(255,255,255,.65);">Landing Page</div>
                    <div class="fw-bold text-white" style="font-size:1.05rem;">Kelola Website Publik Sekolah</div>
                </div>
            </div>
            @if ($landingUrl)
                <a href="{{ $landingUrl }}" target="_blank" rel="noopener" class="lp-admin-hero-btn">
                    <span class="material-symbols-rounded" style="font-size:18px;">open_in_new</span>
                    Lihat Website
                </a>
            @endif
        </div>
    </div>

    @php
        $statItems = [
            ['label' => 'Berita', 'value' => $stats['posts'], 'icon' => 'article', 'from' => '#0ea5e9', 'to' => '#0c7fbb'],
            ['label' => 'Galeri', 'value' => $stats['galleries'], 'icon' => 'photo_library', 'from' => '#f59e0b', 'to' => '#d97706'],
            ['label' => 'Video', 'value' => $stats['videos'], 'icon' => 'play_circle', 'from' => '#ef4444', 'to' => '#c73838'],
            ['label' => 'Pengumuman', 'value' => $stats['announcements'], 'icon' => 'campaign', 'from' => '#ef4444', 'to' => '#c73838'],
            ['label' => 'Pesan', 'value' => $stats['unread_messages'], 'icon' => 'mail', 'from' => '#ec4899', 'to' => '#be185d', 'badge' => $stats['unread_messages'] > 0],
        ];
    @endphp
    <div class="row g-2 mb-3">
        @foreach ($statItems as $s)
            <div class="col-6 col-md-3 col-lg">
                <div class="lp-admin-stat">
                    <div class="lp-admin-stat-icon" style="background: linear-gradient(135deg, {{ $s['from'] }}, {{ $s['to'] }});">
                        <span class="material-symbols-rounded">{{ $s['icon'] }}</span>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <div class="fw-bold lh-1" style="font-size:1.15rem;">
                            {{ $s['value'] }}
                            @if (!empty($s['badge']))
                                <span class="badge bg-danger ms-1" style="font-size:.6rem;">baru</span>
                            @endif
                        </div>
                        <div class="text-muted small text-truncate">{{ $s['label'] }}</div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="card mb-3">
        <div class="card-body p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <div>
                    <h6 class="mb-0 fw-bold">Menu Pengelolaan</h6>
                    <div class="text-muted small">Akses cepat ke semua bagian landing page.</div>
                </div>
            </div>
            <div class="row g-2">
@php
                $menus = [
                    ['route' => 'app.admin-landing.pengaturan', 'icon' => 'settings', 'title' => 'Pengaturan Website', 'sub' => 'Hero, identitas, kontak, sambutan, warna, background, SEO', 'color' => '#37d17c'],
                    ['route' => 'app.admin-landing.ppdb-cta', 'icon' => 'how_to_reg', 'title' => 'PPDB', 'sub' => 'CTA, persyaratan, alur, jadwal, FAQ', 'color' => '#f97316'],
                    ['route' => 'app.admin-landing.profile-sections', 'icon' => 'account_balance', 'title' => 'Profil', 'sub' => 'Section profil, struktur, fasilitas', 'color' => '#0ea5e9'],
                    ['route' => 'app.admin-landing.posts', 'icon' => 'article', 'title' => 'Berita', 'sub' => 'Artikel & program', 'color' => '#f59e0b'],
                    ['route' => 'app.admin-landing.galleries', 'icon' => 'photo_library', 'title' => 'Galeri', 'sub' => 'Album foto', 'color' => '#8b5cf6'],
                    ['route' => 'app.admin-landing.videos', 'icon' => 'play_circle', 'title' => 'Video', 'sub' => 'Video YouTube', 'color' => '#ef4444'],
                    ['route' => 'app.admin-landing.announcements', 'icon' => 'campaign', 'title' => 'Pengumuman', 'sub' => 'Info sekolah', 'color' => '#ef4444'],
                    ['route' => 'app.admin-landing.contact-messages', 'icon' => 'contact_mail', 'title' => 'Kontak', 'sub' => 'Pesan masuk', 'color' => '#10b981'],
                ];
            @endphp
                @foreach ($menus as $m)
                    <div class="col-12 col-md-6 col-lg-4">
                        <a href="{{ route($m['route']) }}" class="lp-admin-menu text-decoration-none">
                            <div class="lp-admin-menu-icon" style="background: linear-gradient(135deg, {{ $m['color'] }}22, {{ $m['color'] }}11); color: {{ $m['color'] }};">
                                <span class="material-symbols-rounded">{{ $m['icon'] }}</span>
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-bold text-dark lh-1" style="font-size:.9rem;">{{ $m['title'] }}</div>
                                <div class="text-muted text-truncate" style="font-size:.75rem;">{{ $m['sub'] }}</div>
                            </div>
                            <span class="material-symbols-rounded text-muted ms-1" style="font-size:18px;">chevron_right</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
    .min-w-0 { min-width: 0; }

    .lp-admin-hero {
        background: linear-gradient(135deg, #1f2937 0%, #37d17c 100%);
        border-radius: .75rem;
        padding: 1.1rem 1.25rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.10), 0 2px 4px -1px rgba(0,0,0,.06);
    }
    .lp-admin-hero-icon {
        width: 48px; height: 48px;
        border-radius: .75rem;
        background: rgba(255,255,255,.15);
        display: inline-flex; align-items: center; justify-content: center;
        flex: 0 0 auto;
    }
    .lp-admin-hero-icon .material-symbols-rounded {
        color: #fff; font-size: 26px;
    }
    .lp-admin-hero-btn {
        background: rgba(255,255,255,.15);
        color: #fff;
        border: 1px solid rgba(255,255,255,.25);
        padding: .5rem 1rem;
        border-radius: .6rem;
        font-size: .85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        text-decoration: none;
        transition: background .2s ease;
    }
    .lp-admin-hero-btn:hover {
        background: rgba(255,255,255,.25);
        color: #fff;
    }

    .lp-admin-stat {
        display: flex;
        align-items: center;
        gap: .65rem;
        background: #fff;
        border-radius: .75rem;
        padding: .65rem .85rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,.10), 0 2px 4px -1px rgba(0,0,0,.06);
        height: 100%;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .lp-admin-stat:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px -5px rgba(0,0,0,.12) !important;
    }
    .lp-admin-stat-icon {
        width: 38px; height: 38px;
        border-radius: .6rem;
        display: inline-flex; align-items: center; justify-content: center;
        flex: 0 0 auto;
    }
    .lp-admin-stat-icon .material-symbols-rounded {
        color: #fff; font-size: 22px;
    }

    .lp-admin-menu {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .65rem .85rem;
        border-radius: .65rem;
        background: #f8fafc;
        border: 1px solid #e9ecef;
        transition: all .15s ease;
        height: 100%;
    }
    .lp-admin-menu:hover {
        background: #fff;
        border-color: #cbd5e1;
        transform: translateX(2px);
        box-shadow: 0 4px 12px rgba(15,23,42,.06);
    }
    .lp-admin-menu-icon {
        width: 38px; height: 38px;
        border-radius: .6rem;
        display: inline-flex; align-items: center; justify-content: center;
        flex: 0 0 auto;
    }
    .lp-admin-menu-icon .material-symbols-rounded {
        font-size: 22px;
    }
</style>
@endsection
