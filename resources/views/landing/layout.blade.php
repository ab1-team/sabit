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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --lp-primary: #1e5bff;
            --lp-primary-dark: #1a4cd8;
            --lp-accent: #ffc233;
            --lp-bg: #f6f8fb;
            --lp-text: #1f2937;
            --lp-muted: #6b7280;
        }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 90px; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: var(--lp-text);
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
        }
        a { text-decoration: none; }

        /* ===== Custom Modern Scrollbar ===== */
        html { scrollbar-width: thin; scrollbar-color: #93c5fd #f1f5f9; }
        ::-webkit-scrollbar { width: 10px; height: 10px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #60a5fa, #2563eb);
            border-radius: 999px;
            border: 2px solid #f1f5f9;
        }
        ::-webkit-scrollbar-thumb:hover { background: linear-gradient(180deg, #3b82f6, #1d4ed8); }
        ::-webkit-scrollbar-corner { background: #f1f5f9; }

        /* ===== Scroll progress bar (top of page) ===== */
        .lp-scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0;
            background: linear-gradient(90deg, var(--lp-primary), #60a5fa);
            z-index: 1080;
            transition: width .08s linear;
            box-shadow: 0 0 8px rgba(30,91,255,.5);
        }

        /* ===== Reveal on scroll ===== */
        .lp-reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .8s cubic-bezier(.22,.61,.36,1), transform .8s cubic-bezier(.22,.61,.36,1);
            will-change: opacity, transform;
        }
        .lp-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .lp-reveal[data-delay="1"] { transition-delay: .08s; }
        .lp-reveal[data-delay="2"] { transition-delay: .16s; }
        .lp-reveal[data-delay="3"] { transition-delay: .24s; }
        .lp-reveal[data-delay="4"] { transition-delay: .32s; }
        .lp-reveal[data-delay="5"] { transition-delay: .40s; }
        .lp-reveal[data-delay="6"] { transition-delay: .48s; }
        .lp-reveal[data-from="left"] { transform: translateX(-36px); }
        .lp-reveal[data-from="left"].is-visible { transform: translateX(0); }
        .lp-reveal[data-from="right"] { transform: translateX(36px); }
        .lp-reveal[data-from="right"].is-visible { transform: translateX(0); }
        .lp-reveal[data-from="zoom"] { transform: scale(.92); }
        .lp-reveal[data-from="zoom"].is-visible { transform: scale(1); }

        @media (prefers-reduced-motion: reduce) {
            .lp-reveal, .lp-reveal[data-from] { opacity: 1 !important; transform: none !important; transition: none !important; }
            html { scroll-behavior: auto; }
        }

        /* ===== Navbar shrink on scroll ===== */
        .lp-navbar { transition: padding .25s ease, box-shadow .25s ease, background .25s ease; }
        .lp-navbar.is-scrolled { padding: .55rem 0; box-shadow: 0 6px 20px rgba(15,23,42,.08); background: rgba(255,255,255,.92); backdrop-filter: blur(8px); }
        .lp-navbar.is-scrolled .lp-brand { font-size: 1.2rem; }

        /* ===== Navbar ===== */
        .lp-navbar {
            background: #ffffff;
            box-shadow: 0 1px 0 rgba(15,23,42,.06);
            padding: .9rem 0;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .lp-brand {
            font-weight: 800;
            color: var(--lp-primary);
            font-size: 1.35rem;
            letter-spacing: -.01em;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .lp-brand img { height: 32px; }
        .lp-nav-link {
            color: #475569;
            font-weight: 500;
            padding: .35rem 0 !important;
            margin: 0 .85rem;
            position: relative;
            transition: color .2s ease;
        }
        .lp-nav-link:hover { color: var(--lp-primary); }
        .lp-nav-link.active {
            color: var(--lp-primary);
            font-weight: 600;
        }
        .lp-nav-link.active::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: -6px;
            height: 2px;
            background: var(--lp-primary);
            border-radius: 2px;
        }
        .lp-btn-primary {
            background: var(--lp-primary);
            border-color: var(--lp-primary);
            color: #fff;
            font-weight: 600;
            padding: .55rem 1.4rem;
            border-radius: 999px;
            transition: all .2s ease;
        }
        .lp-btn-primary:hover {
            background: var(--lp-primary-dark);
            border-color: var(--lp-primary-dark);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(30,91,255,.3);
        }
        .lp-navbar .navbar-toggler {
            border: 0;
            padding: .25rem .5rem;
        }
        .lp-navbar .navbar-toggler:focus { box-shadow: none; }

        /* ===== Hero ===== */
        .lp-hero {
            position: relative;
            min-height: 560px;
            display: flex;
            align-items: center;
            color: #fff;
            overflow: hidden;
        }
        .lp-hero-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            z-index: 0;
        }
        .lp-hero-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(15,23,42,.45) 0%, rgba(15,23,42,.6) 100%);
        }
        .lp-hero-content {
            position: relative;
            z-index: 1;
            padding: 5rem 0;
            text-align: center;
        }
        .lp-pill {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(6px);
            color: #fff;
            padding: .45rem 1rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,.25);
            margin-bottom: 1.25rem;
        }
        .lp-pill .dot {
            width: 8px; height: 8px;
            background: var(--lp-accent);
            border-radius: 50%;
        }
        .lp-hero h1 {
            font-size: clamp(2rem, 5vw, 3.25rem);
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 1rem;
            letter-spacing: -.02em;
        }
        .lp-hero p.lead {
            font-size: clamp(.95rem, 1.6vw, 1.05rem);
            max-width: 620px;
            margin: 0 auto 2rem;
            opacity: .92;
        }
        .lp-hero-actions {
            display: flex;
            gap: .75rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .lp-btn-light {
            background: #fff;
            color: var(--lp-primary);
            font-weight: 600;
            padding: .65rem 1.6rem;
            border-radius: 999px;
            border: 0;
            transition: all .2s ease;
        }
        .lp-btn-light:hover { background: #f1f5f9; color: var(--lp-primary); transform: translateY(-1px); }
        .lp-btn-outline-light {
            background: transparent;
            color: #fff;
            font-weight: 600;
            padding: .65rem 1.6rem;
            border-radius: 999px;
            border: 1.5px solid rgba(255,255,255,.6);
            transition: all .2s ease;
        }
        .lp-btn-outline-light:hover { background: rgba(255,255,255,.12); color: #fff; }

        /* ===== Sections ===== */
        section.lp-section { padding: 5rem 0; }
        .lp-bg-soft { background: var(--lp-bg); }
        .lp-section-title {
            font-weight: 700;
            font-size: clamp(1.5rem, 2.6vw, 1.85rem);
            margin-bottom: .5rem;
            letter-spacing: -.01em;
        }
        .lp-section-sub {
            color: var(--lp-muted);
            max-width: 640px;
            margin: 0 auto;
        }

        /* ===== Stats ===== */
        .lp-stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(15,23,42,.04);
            border: 1px solid rgba(15,23,42,.04);
            transition: transform .2s ease, box-shadow .2s ease;
            height: 100%;
        }
        .lp-stat-card:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(15,23,42,.06); }
        .lp-stat-icon {
            width: 52px; height: 52px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.35rem;
            flex-shrink: 0;
        }
        .lp-stat-icon.is-blue { background: #e8f0ff; color: var(--lp-primary); }
        .lp-stat-icon.is-green { background: #e6f6ec; color: #16a34a; }
        .lp-stat-icon.is-amber { background: #fff4d6; color: #d97706; }
        .lp-stat-value { font-size: 1.6rem; font-weight: 800; line-height: 1; margin-bottom: .15rem; color: var(--lp-text); }
        .lp-stat-label { color: var(--lp-muted); font-size: .85rem; font-weight: 500; }

        /* ===== Sambutan ===== */
        .lp-welcome-img {
            border-radius: 18px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 20px 40px rgba(15,23,42,.08);
        }
        .lp-welcome-img img { width: 100%; height: 100%; object-fit: cover; display: block; min-height: 340px; }
        .lp-welcome-quote {
            position: absolute;
            bottom: -22px;
            right: -22px;
            background: #fff;
            border-radius: 14px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: .65rem;
            box-shadow: 0 8px 24px rgba(15,23,42,.1);
            max-width: 280px;
        }
        .lp-welcome-quote .bi { font-size: 1.4rem; color: var(--lp-primary); }
        .lp-welcome-quote span { font-size: .82rem; color: var(--lp-text); font-weight: 500; }
        .lp-divider {
            width: 48px; height: 3px; border-radius: 2px;
            background: var(--lp-primary); margin-bottom: 1rem;
        }
        .lp-text-muted-soft { color: var(--lp-muted); line-height: 1.7; }

        /* ===== Keunggulan ===== */
        .lp-feature-card {
            background: #fff;
            border-radius: 16px;
            padding: 1.5rem;
            height: 100%;
            border: 1px solid rgba(15,23,42,.05);
            transition: all .2s ease;
            box-shadow: 0 1px 3px rgba(15,23,42,.03);
        }
        .lp-feature-card:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(15,23,42,.06); }
        .lp-feature-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            margin-bottom: 1rem;
        }
        .lp-feature-icon.is-blue { background: #e8f0ff; color: var(--lp-primary); }
        .lp-feature-icon.is-green { background: #e6f6ec; color: #16a34a; }
        .lp-feature-icon.is-amber { background: #fff4d6; color: #d97706; }
        .lp-feature-title { font-weight: 700; font-size: 1.05rem; margin-bottom: .5rem; }
        .lp-feature-desc { color: var(--lp-muted); font-size: .9rem; line-height: 1.6; margin: 0; }

        /* ===== Program Unggulan ===== */
        .lp-program-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(15,23,42,.05);
            transition: all .2s ease;
            height: 100%;
        }
        .lp-program-card:hover { transform: translateY(-3px); box-shadow: 0 14px 30px rgba(15,23,42,.08); }
        .lp-program-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            display: block;
        }
        .lp-program-card .lp-body { padding: 1.25rem 1.25rem 1.5rem; }
        .lp-program-card .lp-tag {
            display: inline-block;
            font-size: .72rem;
            font-weight: 600;
            background: #e8f0ff;
            color: var(--lp-primary);
            padding: .25rem .65rem;
            border-radius: 6px;
            margin-bottom: .65rem;
            text-transform: capitalize;
        }
        .lp-program-card h5 { font-weight: 700; font-size: 1.05rem; margin-bottom: .35rem; }
        .lp-program-card p { color: var(--lp-muted); font-size: .85rem; margin: 0; line-height: 1.55; }

        /* ===== Footer ===== */
        footer.lp-footer {
            background: var(--lp-bg);
            padding: 3rem 0 1.5rem;
            color: #475569;
            font-size: .9rem;
        }
        footer.lp-footer h6 { color: var(--lp-text); font-weight: 700; margin-bottom: 1rem; }
        footer.lp-footer a { color: #475569; transition: color .15s ease; }
        footer.lp-footer a:hover { color: var(--lp-primary); }
        .lp-footer-bottom {
            border-top: 1px solid rgba(15,23,42,.08);
            margin-top: 2rem;
            padding-top: 1.25rem;
            font-size: .82rem;
            color: var(--lp-muted);
        }

        /* ===== Utilities ===== */
        .lp-link-soft { color: var(--lp-primary); font-weight: 600; font-size: .9rem; }
        .lp-link-soft:hover { color: var(--lp-primary-dark); }

        /* ===== Responsive ===== */
        @media (max-width: 991.98px) {
            .lp-navbar .navbar-collapse {
                padding-top: 1rem;
            }
            .lp-nav-link { margin: .25rem 0; }
            .lp-nav-link.active::after { display: none; }
            .lp-btn-primary { width: 100%; margin-top: .5rem; }
            .lp-welcome-quote { right: 16px; bottom: 16px; }
        }
        @media (max-width: 767.98px) {
            section.lp-section { padding: 3.5rem 0; }
            .lp-hero { min-height: 480px; }
            .lp-hero-content { padding: 4rem 0 3rem; }
            .lp-welcome-quote {
                position: static;
                margin: 1rem auto 0;
                max-width: 100%;
            }
        }
    </style>

    @yield('style')
</head>
<body>

<nav class="lp-navbar navbar navbar-expand-lg">
    <div class="container">
        <a class="lp-brand navbar-brand p-0" href="{{ route('landing.home') }}">
            @if ($setting->logo)
                <img src="{{ Storage::disk('public')->url('landing/' . $setting->logo) }}" alt="">
            @else
                <i class="bi bi-mortarboard-fill"></i>
            @endif
            <span>{{ $setting->school_name ?? 'Elite Elementary' }}</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#lpNav"
                aria-controls="lpNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="lpNav">
            <ul class="navbar-nav ms-auto align-items-lg-center">
                @php
                    $currentPath = request()->path();
                    $homeUrl = route('landing.home');
                @endphp
                <li class="nav-item">
                    <a class="nav-link lp-nav-link {{ request()->routeIs('landing.home') ? 'active' : '' }}"
                       href="{{ $homeUrl }}">Home</a>
                </li>
                @foreach ($menus['header'] ?? [] as $item)
                    @if ($item->child_items->isNotEmpty())
                        <li class="nav-item dropdown">
                            <a class="nav-link lp-nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
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
                            <a class="nav-link lp-nav-link" href="{{ $item->url }}">{{ $item->title }}</a>
                        </li>
                    @endif
                @endforeach
            </ul>
            <a href="#daftar" class="btn lp-btn-primary ms-lg-3 mt-2 mt-lg-0">Daftar PPDB</a>
        </div>
    </div>
</nav>

@yield('content')

<div class="lp-scroll-progress" id="lpScrollProgress"></div>

<footer class="lp-footer" id="kontak">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5 col-md-12">
                <h5 class="lp-brand mb-2">{{ $setting->school_name ?? 'Elite Elementary' }}</h5>
                <p class="mb-2">{{ $setting->tagline ?? 'Nurturing Future Leaders.' }}</p>
                @if ($setting->address)
                    <p class="mb-1"><i class="bi bi-geo-alt me-2 text-primary"></i>{{ $setting->address }}</p>
                @endif
                @if ($setting->phone)
                    <p class="mb-1"><i class="bi bi-telephone me-2 text-primary"></i>{{ $setting->phone }}</p>
                @endif
                @if ($setting->email)
                    <p class="mb-0"><i class="bi bi-envelope me-2 text-primary"></i>{{ $setting->email }}</p>
                @endif
            </div>

            <div class="col-lg-3 col-md-4">
                <h6>Quick Links</h6>
                <ul class="list-unstyled m-0">
                    <li class="mb-2"><a href="#tentang">About Us</a></li>
                    <li class="mb-2"><a href="#daftar">PPDB FAQ</a></li>
                    <li class="mb-2"><a href="#kontak">Location</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-md-8">
                <h6>Legal</h6>
                <ul class="list-unstyled m-0">
                    <li class="mb-2"><a href="#">Privacy Policy</a></li>
                    <li class="mb-2"><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>

        <div class="lp-footer-bottom d-flex flex-wrap justify-content-between">
            <span>&copy; {{ date('Y') }} {{ $setting->school_name ?? 'Elite Elementary School' }}. {{ $setting->tagline ?? 'Nurturing Future Leaders.' }}</span>
            @if ($adminUrl = tenant()?->adminUrl())
                <a href="{{ $adminUrl }}">Login Admin</a>
            @endif
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    // 1) Scroll progress bar
    var bar = document.getElementById('lpScrollProgress');
    var nav = document.querySelector('.lp-navbar');

    function onScroll() {
        var doc = document.documentElement;
        var h = doc.scrollHeight - doc.clientHeight;
        var pct = h > 0 ? (window.scrollY / h) * 100 : 0;
        if (bar) bar.style.width = pct + '%';
        if (nav) nav.classList.toggle('is-scrolled', window.scrollY > 20);
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll);
    onScroll();

    // 2) Reveal on scroll (IntersectionObserver)
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (!reduceMotion && 'IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -60px 0px' });

        document.querySelectorAll('.lp-reveal').forEach(function (el) { io.observe(el); });
    } else {
        document.querySelectorAll('.lp-reveal').forEach(function (el) { el.classList.add('is-visible'); });
    }

    // 3) Anchor click → smooth scroll (works alongside html { scroll-behavior: smooth })
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        link.addEventListener('click', function (e) {
            var id = link.getAttribute('href');
            if (id.length < 2) return;
            var target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            var top = target.getBoundingClientRect().top + window.scrollY - 80;
            window.scrollTo({ top: top, behavior: 'smooth' });
            var collapse = document.querySelector('.navbar-collapse.show');
            if (collapse && window.bootstrap) {
                new bootstrap.Collapse(collapse, { toggle: false }).hide();
            }
        });
    });
})();
</script>
@yield('script')
</body>
</html>
