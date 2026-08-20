@php
    $appName = $appName ?? \App\Models\Profil::namaLembaga();
    $appLogoUrl = $appLogoUrl ?? \App\Models\Profil::logoUrl();
    $jatuhTempo = optional(session('profil'))->jatuh_tempo ?? null;
@endphp

<div class="container-fluid py-1 px-3">
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex flex-wrap" id="navbar">
        <ul class="navbar-nav d-flex align-items-center justify-content-end ms-auto order-0 order-md-2">
            <li class="nav-item px-2 d-flex align-items-center">
                <button type="button" class="lp-sidenav-burger" id="lpSidenavBurger" aria-label="Buka menu" aria-expanded="false">
                    <span class="material-symbols-rounded">menu</span>
                </button>
            </li>

            @if(session('msg') && $jatuhTempo && now()->day == (int) $jatuhTempo)
                <button type="button"
                    onclick="window.open('/app/system/generate-tunggakan/{{ time() }}', '_blank'); return false;"
                    class="btn btn-danger">
                    Buat Tunggakan
                </button>
            @endif

            <li class="nav-item px-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0">
                    <span class="material-symbols-rounded fixed-plugin-button-nav">settings</span>
                </a>
            </li>

            @php
                $contactMsgRoute = \Illuminate\Support\Facades\Route::has('app.admin-landing.contact-messages')
                    ? route('app.admin-landing.contact-messages')
                    : '#';
            @endphp
            <li class="nav-item dropdown pe-3 d-flex align-items-center">
                <a href="{{ $contactMsgRoute }}"
                   class="nav-link text-body p-0 position-relative"
                   id="lpBellNotif"
                   data-bs-toggle="dropdown"
                   aria-expanded="false">
                    <span class="material-symbols-rounded">notifications</span>
                    @if(($unreadContactCount ?? 0) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                              style="font-size:.6rem;padding:.25rem .4rem;line-height:1;">
                            {{ $unreadContactCount > 99 ? '99+' : $unreadContactCount }}
                        </span>
                    @endif
                </a>
                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4" style="min-width:320px;max-width:360px;">
                    <li class="d-flex justify-content-between align-items-center px-2 mb-2">
                        <span class="fw-semibold small">Pesan Masuk Landing</span>
                        @if(($unreadContactCount ?? 0) > 0)
                            <span class="badge bg-danger rounded-pill">{{ $unreadContactCount }} belum dibaca</span>
                        @endif
                    </li>
                    @forelse(($recentContactMessages ?? collect()) as $msg)
                        <li class="mb-1">
                            <a class="dropdown-item border-radius-md d-flex align-items-start py-2"
                               href="{{ $contactMsgRoute }}">
                                <span class="material-symbols-rounded me-2 text-primary" style="font-size:20px;">mail</span>
                                <span class="flex-grow-1">
                                    <span class="d-block fw-semibold small text-truncate" style="max-width:240px;">
                                        {{ $msg->name ?: 'Anonim' }}
                                    </span>
                                    <span class="d-block text-muted small text-truncate" style="max-width:240px;">
                                        {{ $msg->subject ?: '(tanpa subjek)' }}
                                    </span>
                                    <span class="d-block text-muted" style="font-size:.7rem;">
                                        {{ optional($msg->created_at)->diffForHumans() }}
                                    </span>
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="mb-2">
                            <span class="dropdown-item border-radius-md d-flex align-items-center text-muted">
                                <span class="material-symbols-rounded me-2">info</span>
                                <span class="small">Tidak ada pesan masuk.</span>
                            </span>
                        </li>
                    @endforelse
                    @if(($unreadContactCount ?? 0) > 0)
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item border-radius-md text-center small fw-semibold text-primary"
                               href="{{ $contactMsgRoute }}">
                                Lihat semua pesan
                            </a>
                        </li>
                    @endif
                </ul>
            </li>

            <li class="nav-item dropdown pe-3 d-flex align-items-center" data-bs-auto-close="outside">
                @auth
                    @php $avatar = auth()->user()->foto ? \App\Models\Profil::tenantStorageUrl('users/' . auth()->user()->foto) : null; @endphp
                @endauth
                <a href="javascript:;" class="nav-link text-body p-0" data-bs-toggle="dropdown">
                    @auth
                        @if($avatar)
                            <img src="{{ $avatar }}" alt="avatar" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.08)">
                        @else
                            @php
                                $words = preg_split('/\s+/', trim(auth()->user()->nama ?? 'U'));
                                $initials = strtoupper(implode('', array_map(fn($w) => substr($w, 0, 1), array_slice($words, 0, 2))));
                                if ($initials === '') $initials = 'U';
                                $palette = ['#6366f1','#0ea5e9','#10b981','#f59e0b','#ef4444','#8b5cf6','#14b8a6'];
                                $color = $palette[crc32($initials) % count($palette)];
                            @endphp
                            <span class="d-inline-flex justify-content-center align-items-center rounded-circle text-white fw-bold" style="width:36px;height:36px;background:{{ $color }};font-size:13px;border:2px solid #fff;box-shadow:0 0 0 1px rgba(0,0,0,.08)">{{ $initials }}</span>
                        @endif
                    @else
                        <span class="material-symbols-rounded">account_circle</span>
                    @endauth
                </a>
                @auth
                    <a href="javascript:;" class="d-none d-sm-inline-block ms-2 fw-bold text-body" data-bs-toggle="dropdown">{{ auth()->user()->nama ?? auth()->user()->username }}</a>
                @endauth
                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4">
                    <li class="mb-2">
                        <a class="dropdown-item border-radius-md d-flex align-items-center" href="/app/profile">
                            <span class="material-symbols-rounded me-2">person</span>
                            <span>Profil</span>
                        </a>
                    </li>
                    <li class="mb-2">
                        <a class="dropdown-item border-radius-md d-flex align-items-center"
                           href="javascript:void(0)"
                           id="btnDukunganTeknis">
                            <span class="material-symbols-rounded me-2">support</span>
                            <span>Dukungan Teknis</span>
                        </a>
                    </li>
                    <li>
                        <form id="formLogout" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="button"
                                class="dropdown-item border-radius-md d-flex align-items-center btn-logout">
                                <span class="material-symbols-rounded me-2">logout</span>
                                <span>Keluar</span>
                            </button>
                        </form>
                    </li>
                </ul>
            </li>
        </ul>

        <div class="ms-3 w-100 w-md-auto order-1 order-md-0 text-center text-md-start mt-4 mt-lg-0">
            <h3 class="mb-0 h4 fw-bold">{{ $title }}</h3>
            <p class="mb-0 text-muted">{{ $appName }}</p>
        </div>
    </div>
</div>
