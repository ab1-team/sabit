<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', $setting->school_name ?? 'Sekolah')</title>

    <meta name="description" content="{{ $setting->meta_description }}">
    <meta name="keywords" content="{{ $setting->meta_keywords }}">

    @if ($setting->favicon)
        <link rel="icon" href="{{ Storage::disk('public')->url('landing/' . $setting->favicon) }}">
    @endif

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body { font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif; }
        .lp-hero {
            min-height: 420px;
            background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%);
            color: #fff;
            display: flex;
            align-items: center;
        }
        .lp-hero-img { background-size: cover; background-position: center; }
        .lp-hero-img::before {
            content: "";
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,.45);
        }
        .lp-card-img { height: 180px; object-fit: cover; }
        .lp-placeholder {
            height: 180px;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: .875rem;
        }
    </style>

    @yield('style')
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('landing.home') }}">
            @if ($setting->logo)
                <img src="{{ Storage::disk('public')->url('landing/' . $setting->logo) }}"
                     alt="" height="36" class="bg-white rounded p-1">
            @endif
            <span>{{ $setting->school_name }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#lpNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="lpNav">
            <ul class="navbar-nav ms-auto">
                @foreach ($menus['header'] ?? [] as $item)
                    @if ($item->child_items->isNotEmpty())
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                {{ $item->title }}
                            </a>
                            <ul class="dropdown-menu">
                                @foreach ($item->child_items as $child)
                                    <li><a class="dropdown-item" href="{{ $child->url }}">{{ $child->title }}</a></li>
                                @endforeach
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ $item->url }}">{{ $item->title }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
</nav>

@yield('content')

<footer class="bg-dark text-light mt-5 pt-5 pb-4">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5>{{ $setting->school_name }}</h5>
                <p class="text-white-50 mb-2">{{ $setting->tagline }}</p>
                @if ($setting->address)
                    <p class="text-white-50 small mb-1">{{ $setting->address }}</p>
                @endif
                @if ($setting->phone)
                    <p class="text-white-50 small mb-1">Telp: {{ $setting->phone }}</p>
                @endif
                @if ($setting->email)
                    <p class="text-white-50 small mb-0">Email: {{ $setting->email }}</p>
                @endif
            </div>

            <div class="col-md-5">
                <h6 class="text-uppercase small">Tautan</h6>
                <div class="row">
                    @foreach ($menus['footer'] ?? [] as $item)
                        <div class="col-6 mb-2">
                            <a href="{{ $item->url }}" class="text-white-50 text-decoration-none small">
                                {{ $item->title }}
                            </a>
                            @if ($item->child_items->isNotEmpty())
                                <ul class="list-unstyled mt-1 mb-0">
                                    @foreach ($item->child_items as $child)
                                        <li>
                                            <a href="{{ $child->url }}"
                                               class="text-white-50 text-decoration-none small">
                                                {{ $child->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="col-md-3">
                <h6 class="text-uppercase small">Media Sosial</h6>
                <ul class="list-unstyled">
                    @foreach (['facebook' => 'Facebook', 'instagram' => 'Instagram', 'youtube' => 'YouTube', 'tiktok' => 'TikTok'] as $field => $label)
                        @if ($setting->{$field})
                            <li>
                                <a href="{{ $setting->{$field} }}" target="_blank" rel="noopener"
                                   class="text-white-50 text-decoration-none small">{{ $label }}</a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

        <hr class="border-secondary">

        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <span class="text-white-50 small">
                &copy; {{ date('Y') }} {{ $setting->school_name }}
            </span>
            @if ($adminUrl = tenant()?->adminUrl())
                <a href="{{ $adminUrl }}" class="text-white-50 small text-decoration-none">Login Admin</a>
            @endif
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@yield('script')
</body>
</html>
