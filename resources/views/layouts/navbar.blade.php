@php
    $jatuhTempo = session('profil')->jatuh_tempo ?? null;
@endphp

<div class="container-fluid py-1 px-3">
    <div class="collapse navbar-collapse mt-sm-0 mt-2 me-md-0 me-sm-4 d-flex flex-wrap" id="navbar">
        <ul class="navbar-nav d-flex align-items-center justify-content-end ms-auto order-0 order-md-2">
            <li class="nav-item d-xl-none ps-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" id="iconNavbarSidenav">
                    <div class="sidenav-toggler-inner">
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                        <i class="sidenav-toggler-line"></i>
                    </div>
                </a>
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

            <li class="nav-item dropdown pe-3 d-flex align-items-center">
                <a href="javascript:;" class="nav-link text-body p-0" data-bs-toggle="dropdown">
                    <span class="material-symbols-rounded">notifications</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end px-2 py-3 me-sm-n4">
                    <li class="mb-2">
                        <a class="dropdown-item border-radius-md d-flex align-items-center" href="/app/profile">
                            <span class="material-symbols-rounded me-2">info</span>
                            <span>Belum ada notifikasi</span>
                        </a>
                    </li>
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
