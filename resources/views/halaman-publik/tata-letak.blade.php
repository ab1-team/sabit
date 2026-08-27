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
    <link href="https://fonts.googleapis.com/icon?family=Material+Symbols+Rounded" rel="stylesheet">

    <style>
        :root {
            --lp-primary: {{ $setting->activeThemeButtonColor() ?? '#2563eb' }};
            --lp-primary-rgb: {{ $setting->themePrimaryRgb() ?? '37, 99, 235' }};
            --lp-primary-dark: {{ $setting->themePrimaryDark() ?? '#1d4ed8' }};
            --lp-primary-soft: {{ $setting->themePrimarySoft() ?? '#dbeafe' }};
            --lp-accent: #f59e0b;
            --lp-accent-soft: #fef3c7;
            --lp-accent-2: #06b6d4;
            --lp-pink: #ec4899;
            --lp-green: #10b981;
            --lp-purple: #8b5cf6;
            --lp-bg: #f6f8fb;
            --lp-text: {{ $setting->activeThemeTextColor() ?? '#0f172a' }};
            --lp-muted: #64748b;
            --lp-card-bg: rgba(255, 255, 255, 0.7);
            --lp-card-border: rgba(255, 255, 255, 0.5);
            --lp-shadow: 0 14px 44px -10px rgba(15, 23, 42, 0.14);
            --lp-shadow-strong: 0 28px 64px -16px rgba(var(--lp-primary-rgb), 0.35);
            --lp-ease: cubic-bezier(0.22, 1, 0.36, 1);

            /* ===== Design tokens (standarisasi) ===== */
            --lp-radius-sm: 12px;
            --lp-radius-md: 16px;
            --lp-radius-lg: 20px;
            --lp-radius-xl: 24px;
            --lp-navbar-max: 1200px;
            --lp-card-pad-y: 1.5rem;
            --lp-card-pad-x: 1.5rem;
            --lp-card-pad: 1.5rem;
            --lp-card-pad-lg: 1.75rem;
            --lp-gap: 1rem;     /* 16px */
            --lp-gap-md: 1.25rem; /* 20px */
            --lp-gap-lg: 1.5rem;  /* 24px */
        }

        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 110px; }
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            color: var(--lp-text);
            background: #ffffff;
            -webkit-font-smoothing: antialiased;
            overflow-x: clip;
            overflow-wrap: break-word;
            max-width: 100vw;
        }
        html { max-width: 100vw; overflow-x: clip; }
        img, video, svg { max-width: 100%; }
        img { height: auto; }
        iframe { max-width: 100%; }
        a { text-decoration: none; }
        table { max-width: 100%; }

        /* Halaman dalam: konten tidak tertutup navbar tetap */
        body.lp-inner > section:first-of-type {
            padding-top: 7.5rem;
        }

        /* ============= Scroll progress bar ============= */
        .lp-scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0;
            background: linear-gradient(90deg, var(--lp-primary), var(--lp-accent-2));
            z-index: 9999;
            transition: width 0.08s linear;
            box-shadow: 0 0 12px rgba(var(--lp-primary-rgb), 0.6);
        }

        /* ============= Reveal on scroll (lightweight) ============= */
        .lp-reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity 0.7s var(--lp-ease), transform 0.7s var(--lp-ease);
            will-change: opacity, transform;
        }
        /* Cegah translateX reveal menimbulkan horizontal overflow di HP */
        .lp-reveal[data-from="left"],
        .lp-reveal[data-from="right"] {
            overflow: hidden;
            max-width: 100%;
        }
        .lp-reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .lp-reveal[data-from="left"] { transform: translateX(-40px); }
        .lp-reveal[data-from="left"].is-visible { transform: translateX(0); }
        .lp-reveal[data-from="right"] { transform: translateX(40px); }
        .lp-reveal[data-from="right"].is-visible { transform: translateX(0); }
        .lp-reveal[data-from="zoom"] { transform: scale(0.92); }
        .lp-reveal[data-from="zoom"].is-visible { transform: scale(1); }
        .lp-reveal[data-from="flip"] { transform: perspective(800px) rotateX(10deg); }
        .lp-reveal[data-from="flip"].is-visible { transform: perspective(800px) rotateX(0); }
        .lp-reveal[data-delay="1"] { transition-delay: 0.08s; }
        .lp-reveal[data-delay="2"] { transition-delay: 0.16s; }
        .lp-reveal[data-delay="3"] { transition-delay: 0.24s; }
        .lp-reveal[data-delay="4"] { transition-delay: 0.32s; }
        .lp-reveal[data-delay="5"] { transition-delay: 0.40s; }
        .lp-reveal[data-delay="6"] { transition-delay: 0.48s; }
        @media (prefers-reduced-motion: reduce) {
            .lp-reveal, .lp-reveal[data-from] {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
            html { scroll-behavior: auto; }
        }

        /* ============= Floating pill navbar ============= */
        .lp-navbar-wrap {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1100;
            padding: 16px 16px 0;
            pointer-events: none;
            transition: top 0.3s var(--lp-ease);
        }
        .lp-navbar-inner {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            max-width: calc(var(--lp-navbar-max) + 32px);
            margin: 0 auto;
            position: relative;
            width: 100%;
            pointer-events: none;
        }
        /* Saat modal sambutan dibuka, blur navbar seperti konten lain di belakang backdrop.
           ::before adalah pill background; di-blur juga agar konsisten. */
        body.lp-welcome-modal-open .lp-navbar-inner,
        body.lp-welcome-modal-open .lp-navbar-inner::before {
            filter: blur(6px) saturate(120%);
            opacity: 0.55;
        }
        body.lp-welcome-modal-open .lp-navbar-inner,
        body.lp-welcome-modal-open .lp-navbar-inner * {
            pointer-events: none !important;
        }
        body.lp-welcome-modal-open #lpScrollTopBtn {
            filter: blur(6px) saturate(120%);
            opacity: 0.45;
            pointer-events: none !important;
        }
        /* Body-scroll-lock + visual lock konten di belakang modal sambutan.
           Konten diblur, non-interaktif (no scroll, no click) supaya fokus penuh ke modal. */
        html.lp-welcome-modal-open,
        body.lp-welcome-modal-open {
            overflow: hidden !important;
            overscroll-behavior: contain;
            touch-action: none;
        }
        body.lp-welcome-modal-open > *:not(.modal):not(.modal-backdrop):not(.lp-navbar-wrap):not(.lp-scroll-top-wrap) {
            filter: blur(6px) saturate(120%);
            opacity: 0.6;
            pointer-events: none !important;
            user-select: none;
        }
        .lp-navbar-inner > .lp-navbar,
        .lp-navbar-inner > .lp-cta-desktop,
        .lp-navbar-wrap > .lp-nav-menu > .lp-nav-menu-body { pointer-events: auto; }
        /* Pill background utama menutupi seluruh isi (brand + menu + CTA)
           agar tampak sebagai 1 kartu utuh. Pseudo di inner, bukan di wrap.
           Di halaman beranda (body.lp-home), navbar dibuat transparan agar
           menyatu dengan hero tanpa terlihat membungkus konten. */
        .lp-navbar-inner::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: saturate(180%) blur(18px);
            -webkit-backdrop-filter: saturate(180%) blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.7);
            box-shadow: 0 8px 32px rgba(15, 23, 42, 0.1);
            pointer-events: none;
            z-index: -1;
            transition: opacity 0.3s var(--lp-ease);
        }
        /* Halaman beranda: sembunyikan pill, hanya teks saja */
        body.lp-home .lp-navbar-inner::before {
            opacity: 0;
        }
        /* Tapi saat scrolled, tetap munculkan pill di beranda (sticky bar) */
        body.lp-home .lp-navbar-inner.is-scrolled::before {
            opacity: 1;
        }
        /* Beranda: di mode scrolled, teks brand & link tetap terbaca di atas hero gradient */
        body.lp-home .lp-navbar-inner:not(.is-scrolled) .lp-brand,
        body.lp-home .lp-navbar-inner:not(.is-scrolled) .lp-nav-link {
            color: #ffffff;
        }
        body.lp-home .lp-navbar-inner:not(.is-scrolled) .lp-nav-link.active {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.18);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45);
        }
        @media (max-width: 991.98px) {
            .lp-navbar-inner::before {
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                background: rgba(255, 255, 255, 0.96);
            }
            /* Halaman beranda + mobile: navbar juga transparan di top,
               pill muncul saat scrolled atau drawer dibuka */
            body.lp-home .lp-navbar-inner::before {
                opacity: 0;
            }
            body.lp-home .lp-navbar-inner.is-scrolled::before,
            body.lp-home .lp-navbar-inner.is-drawer-open::before {
                opacity: 1;
            }
            /* Brand & link color putih di top, darken saat scrolled/drawer */
            body.lp-home .lp-navbar-inner:not(.is-scrolled):not(.is-drawer-open) .lp-brand,
            body.lp-home .lp-navbar-inner:not(.is-scrolled):not(.is-drawer-open) .lp-nav-link {
                color: #ffffff;
            }
            body.lp-home .lp-navbar-inner:not(.is-scrolled):not(.is-drawer-open) .lp-nav-link.active {
                color: #ffffff;
                background: rgba(255, 255, 255, 0.18);
                box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.45);
            }
            /* Tombol toggle (hamburger) ikut putih di top, gelap saat scrolled/drawer */
            body.lp-home .lp-navbar-inner:not(.is-scrolled):not(.is-drawer-open) .lp-nav-toggler {
                color: #ffffff;
            }
        }
        /* Saat drawer open: sembunyikan pill agar drawer yang mengambil alih. */
        .lp-navbar-inner.is-drawer-open::before,
        .lp-navbar-inner.is-drawer-open > .lp-navbar,
        .lp-navbar-inner.is-drawer-open > .lp-cta-desktop,
        .lp-navbar-wrap.is-drawer-open > .lp-nav-menu > .lp-nav-menu-body {
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s var(--lp-ease), visibility 0s linear 0.2s;
        }
        .lp-navbar {
            pointer-events: auto;
            background: transparent;
            border: 0;
            border-radius: 999px;
            padding: 0.4rem 0.6rem 0.4rem 1.25rem;
            box-shadow: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            width: auto;
            flex: 0 0 auto;
            transition: all 0.4s var(--lp-ease);
            position: relative;
        }
        .lp-navbar.is-page .lp-brand,
        .lp-navbar-wrap:has(.lp-navbar.is-page) .lp-brand { color: var(--lp-primary); }
        .lp-navbar.is-page .lp-nav-link,
        .lp-navbar-wrap:has(.lp-navbar.is-page) .lp-nav-link { color: #475569; }
        .lp-navbar.is-page .lp-nav-link:hover,
        .lp-navbar-wrap:has(.lp-navbar.is-page) .lp-nav-link:hover {
            color: var(--lp-primary);
            background: rgba(var(--lp-primary-rgb), 0.06);
        }
        .lp-navbar.is-page .lp-nav-link.active,
        .lp-navbar-wrap:has(.lp-navbar.is-page) .lp-nav-link.active {
            color: #fff;
            background: var(--lp-primary);
            box-shadow: 0 6px 18px rgba(var(--lp-primary-rgb), 0.35);
        }
        .lp-navbar.is-page .lp-cta {
            color: #fff;
        }
        .lp-navbar.is-scrolled {
            padding: 0.4rem 0.4rem 0.4rem 1.25rem;
        }
        .lp-brand {
            font-weight: 800;
            color: #ffffff;
            font-size: 1.1rem;
            letter-spacing: -0.01em;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            min-width: 0;
            flex: 0 0 auto;
            white-space: nowrap;
            transition: transform 0.25s var(--lp-ease), color 0.3s var(--lp-ease);
        }
        .lp-navbar.is-scrolled .lp-brand,
        .lp-navbar-wrap.is-scrolled .lp-brand { color: var(--lp-primary); }
        .lp-brand:hover { transform: scale(1.03); }
        .lp-brand img { height: 28px; width: auto; flex-shrink: 0; }
        .lp-brand span {
            white-space: nowrap;
            max-width: none;
        }
        .lp-nav-menu {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            margin-left: auto;
            margin-right: 0.5rem;
        }
        .lp-nav-link {
            color: #ffffff;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.5rem 0.95rem;
            border-radius: 999px;
            position: relative;
            transition: color 0.2s ease, background 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }
        .lp-nav-link:hover { color: #fff; background: rgba(255, 255, 255, 0.15); }
        .lp-nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.2);
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.35);
        }
        .lp-navbar.is-scrolled .lp-nav-link,
        .lp-navbar-wrap.is-scrolled .lp-nav-link {
            color: #475569;
        }
        .lp-navbar.is-scrolled .lp-nav-link:hover,
        .lp-navbar-wrap.is-scrolled .lp-nav-link:hover {
            color: var(--lp-primary);
            background: rgba(var(--lp-primary-rgb), 0.06);
        }
        .lp-navbar.is-scrolled .lp-nav-link.active,
        .lp-navbar-wrap.is-scrolled .lp-nav-link.active {
            color: #fff;
            background: var(--lp-primary);
            box-shadow: 0 6px 18px rgba(var(--lp-primary-rgb), 0.35);
        }
        .lp-nav-link .bi-chevron-down {
            font-size: 0.7rem;
            transition: transform 0.25s var(--lp-ease);
        }
        .lp-nav-item.dropdown:hover .lp-nav-link .bi-chevron-down { transform: rotate(180deg); }
        .lp-nav-item.dropdown { position: relative; }
        .lp-dropdown {
            position: absolute;
            top: calc(100% + 12px);
            left: 50%;
            transform: translateX(-50%) translateY(8px);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 18px;
            padding: 0.5rem;
            min-width: 220px;
            box-shadow: 0 16px 48px rgba(15, 23, 42, 0.12);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.25s var(--lp-ease), transform 0.25s var(--lp-ease), visibility 0.25s;
        }
        .lp-nav-item.dropdown:hover .lp-dropdown,
        .lp-nav-item.dropdown:focus-within .lp-dropdown {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }
        .lp-dropdown a {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 0.6rem 0.85rem;
            border-radius: 12px;
            color: #334155;
            font-size: 0.9rem;
            font-weight: 500;
            transition: background 0.2s ease, color 0.2s ease, transform 0.2s var(--lp-ease);
        }
        .lp-dropdown a:hover {
            background: rgba(var(--lp-primary-rgb), 0.08);
            color: var(--lp-primary);
            transform: translateX(3px);
        }
        .lp-dropdown a.active {
            background: rgba(var(--lp-primary-rgb), 0.1);
            color: var(--lp-primary);
            font-weight: 600;
        }
        .lp-dropdown a.active i { color: var(--lp-primary); }
        .lp-dropdown a i { color: var(--lp-accent-2); font-size: 1rem; }

        .lp-cta {
            background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
            color: #fff;
            border: 0;
            font-weight: 600;
            padding: 0.55rem 1.25rem;
            border-radius: 999px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            white-space: nowrap;
            font-size: 0.9rem;
        }
        .lp-cta:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(var(--lp-primary-rgb), 0.4);
        }

        .lp-nav-toggler {
            display: none;
            background: transparent;
            border: 0;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.4rem;
            transition: background 0.2s ease, color 0.2s ease;
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
            z-index: 2;
            position: relative;
        }
        .lp-navbar.is-scrolled .lp-nav-toggler,
        .lp-navbar.is-page .lp-nav-toggler { color: var(--lp-primary); }
        .lp-nav-toggler:hover { background: rgba(var(--lp-primary-rgb), 0.08); }
        .lp-nav-toggler:focus { box-shadow: none; outline: none; }
        .lp-nav-toggler:focus-visible {
            outline: 2px solid var(--lp-primary);
            outline-offset: 2px;
        }

        /* ============= Mobile drawer (slide dari kanan) ============= */
        .lp-nav-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.55);
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s var(--lp-ease);
            z-index: 1070;
            pointer-events: none;
        }
        .lp-nav-backdrop.is-open {
            opacity: 1;
            visibility: visible;
            pointer-events: auto;
        }
        body.lp-nav-open {
            overflow: hidden !important;
            overscroll-behavior: contain;
            touch-action: none;
        }
        /* Kunci <html> juga untuk konsistensi & cegah scroll bounce di mobile */
        html.lp-nav-open {
            overflow: hidden !important;
            overscroll-behavior: contain;
            touch-action: none;
        }
        /* Konten di belakang drawer mobile: non-interaktif supaya tidak bisa di-tap/diklik */
        body.lp-nav-open > *:not(.lp-navbar-wrap):not(.lp-nav-menu):not(.lp-nav-backdrop) {
            pointer-events: none !important;
            user-select: none;
        }

        /* Drawer mobile: default hidden, hanya muncul di mobile via media query */
        .lp-nav-menu {
            display: none;
            visibility: hidden;
        }

        @media (max-width: 991.98px) {
            .lp-nav-toggler { display: inline-flex; flex-shrink: 0; margin-left: auto; }
            .lp-navbar { flex: 1 1 auto; width: 100%; }
            .lp-navbar .lp-brand { flex: 0 0 auto; min-width: 0; }
            .lp-nav-desktop-menu { display: none !important; }
            .lp-navbar-inner > .lp-cta-desktop { display: none !important; }

            .lp-nav-menu {
                position: fixed !important;
                top: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                left: auto !important;
                width: min(320px, 86vw);
                max-width: 360px;
                min-height: 100vh;
                min-height: 100dvh;
                flex-direction: column;
                align-items: stretch;
                justify-content: flex-start;
                background: #ffffff;
                box-shadow: -20px 0 60px -10px rgba(15, 23, 42, 0.25);
                gap: 0;
                padding: 0;
                margin: 0;
                transform: translateX(110%);
                -webkit-transform: translateX(110%);
                transition: transform 0.35s var(--lp-ease), visibility 0s linear 0.35s, opacity 0.2s var(--lp-ease);
                -webkit-transition: transform 0.35s var(--lp-ease), visibility 0s linear 0.35s, opacity 0.2s var(--lp-ease);
                z-index: 1200;
                overflow-y: auto;
                overflow-x: hidden;
                -webkit-overflow-scrolling: touch;
                display: flex !important;
                pointer-events: none;
                visibility: hidden;
                opacity: 0;
                will-change: transform;
                touch-action: manipulation;
                -webkit-tap-highlight-color: transparent;
            }
            .lp-nav-menu.is-open {
                transform: translateX(0) !important;
                -webkit-transform: translateX(0) !important;
                visibility: visible !important;
                opacity: 1 !important;
                pointer-events: auto !important;
                transition: transform 0.35s var(--lp-ease), visibility 0s linear 0s, opacity 0.2s var(--lp-ease);
                -webkit-transition: transform 0.35s var(--lp-ease), visibility 0s linear 0s, opacity 0.2s var(--lp-ease);
            }
            .lp-nav-menu.is-open *,
            .lp-nav-menu.is-open a,
            .lp-nav-menu.is-open button {
                pointer-events: auto !important;
                visibility: visible !important;
            }
            .lp-nav-menu.is-open .lp-nav-menu-body,
            .lp-nav-menu.is-open .lp-nav-menu-header,
            .lp-nav-menu.is-open .lp-nav-menu-footer {
                display: flex !important;
                flex-direction: column;
                visibility: visible !important;
                opacity: 1 !important;
            }
            .lp-nav-menu.is-open .lp-nav-menu-header { flex-direction: row; }
            .lp-nav-menu.is-open .lp-nav-menu-footer { flex-direction: column; }

            /* Header drawer (brand + tombol tutup) */
            .lp-nav-menu-header {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                padding: 1rem 1.25rem;
                border-bottom: 1px solid rgba(15, 23, 42, 0.06);
                position: sticky;
                top: 0;
                background: #ffffff;
                z-index: 2;
            }
            .lp-nav-menu-header .lp-brand {
                color: var(--lp-primary);
                font-size: 1rem;
                max-width: 70%;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .lp-nav-close {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                border: 0;
                background: rgba(var(--lp-primary-rgb), 0.08);
                color: var(--lp-primary);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                font-size: 1.15rem;
                flex-shrink: 0;
                cursor: pointer;
                -webkit-tap-highlight-color: transparent;
                transition: background 0.2s ease;
            }
            .lp-nav-close:hover { background: rgba(var(--lp-primary-rgb), 0.15); }

            .lp-nav-menu-body {
                display: flex !important;
                flex-direction: column;
                align-items: stretch;
                padding: 0.75rem 1rem 1rem;
                flex: 1 1 auto;
                width: 100%;
                min-height: 200px;
                background: transparent;
                border: 0;
                border-radius: 0;
                opacity: 1 !important;
                visibility: visible !important;
            }

            .lp-nav-menu-footer {
                display: block !important;
                padding: 1rem;
                border-top: 1px solid rgba(15, 23, 42, 0.06);
                background: #f8fafc;
            }
            .lp-nav-menu-footer .lp-cta-mobile {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                width: 100%;
                background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
                color: #fff;
                font-weight: 700;
                padding: 0.85rem 1.25rem;
                border-radius: 999px;
                border: 0;
                text-align: center;
                transition: transform 0.2s ease, box-shadow 0.2s ease;
            }
            .lp-nav-menu-footer .lp-cta-mobile:hover {
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 10px 24px rgba(var(--lp-primary-rgb), 0.4);
            }

            .lp-nav-item {
                display: block !important;
                width: 100%;
                border-bottom: 1px solid rgba(15, 23, 42, 0.05);
            }
            .lp-nav-item:last-child { border-bottom: 0; }

            .lp-nav-link {
                display: flex !important;
                padding: 0.95rem 0.75rem;
                color: var(--lp-text);
                font-weight: 600;
                font-size: 0.98rem;
                border-radius: 0;
                width: 100%;
                justify-content: space-between;
                align-items: center;
            }
            .lp-nav-link:hover {
                color: var(--lp-primary);
                background: rgba(var(--lp-primary-rgb), 0.04);
            }
            .lp-nav-link.active {
                color: var(--lp-primary);
                background: rgba(var(--lp-primary-rgb), 0.08);
            }
            .lp-nav-link .bi-chevron-down {
                color: var(--lp-muted);
            }

            .lp-dropdown {
                position: static;
                transform: none;
                box-shadow: none;
                background: rgba(var(--lp-primary-rgb), 0.04);
                border-radius: 0;
                margin: 0;
                opacity: 1;
                visibility: visible;
                max-height: 0;
                overflow: hidden;
                padding: 0;
                min-width: 0;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
                border: 0;
                transition: max-height 0.3s var(--lp-ease);
            }
            .lp-nav-item.dropdown.is-open .lp-dropdown {
                max-height: 600px;
                padding: 0.25rem 0 0.5rem;
            }
            .lp-nav-item.dropdown:hover .lp-dropdown { transform: none; }
            .lp-dropdown a {
                padding: 0.7rem 0.75rem 0.7rem 1.75rem;
                border-radius: 0;
                color: #475569;
                font-size: 0.92rem;
                font-weight: 500;
            }
            .lp-dropdown a:hover {
                background: rgba(var(--lp-primary-rgb), 0.06);
                color: var(--lp-primary);
                transform: none;
            }
            .lp-dropdown a.active {
                background: rgba(var(--lp-primary-rgb), 0.08);
                color: var(--lp-primary);
                font-weight: 600;
            }
            .lp-navbar .lp-cta,
            .lp-cta-desktop { display: none !important; }
        }

        /* Reset struktur drawer di desktop: drawer mobile tersembunyi by default
           (sudah di-set di rule global di atas). Di desktop, menu inline
           di-render via .lp-nav-desktop-menu yang berada di dalam .lp-navbar. */
        @media (min-width: 992px) {
            .lp-nav-toggler { display: none; }

            .lp-navbar-inner {
                padding-left: max(28px, env(safe-area-inset-left, 28px));
                padding-right: max(28px, env(safe-area-inset-right, 28px));
                gap: 0;
            }

            /* Drawer mobile tetap hidden di desktop (extra safety) */
            .lp-nav-menu {
                display: none !important;
                visibility: hidden !important;
            }

            /* Menu inline di navbar: flex row 1 baris, transparan, menyatu pill. */
            .lp-nav-desktop-menu {
                display: flex;
                align-items: center;
                gap: 0.25rem;
                flex: 0 0 auto;
                min-width: 0;
                order: 1;
                flex-wrap: nowrap;
                justify-content: flex-end;
                margin-left: auto;
                pointer-events: auto;
                background: transparent;
                border: 0;
                border-radius: 999px;
                padding: 0.4rem 0.5rem;
                transition: all 0.4s var(--lp-ease);
            }
            .lp-navbar {
                order: 0;
                flex: 1 1 auto;
                width: 100%;
                min-width: 0;
                max-width: none;
            }
            .lp-navbar > .lp-brand { flex: 0 0 auto; margin-right: 1rem; }
            .lp-navbar-inner > .lp-cta-desktop { order: 2; flex: 0 0 auto; margin-left: 0.5rem; }

            .lp-nav-desktop-menu .lp-nav-item {
                display: inline-flex;
                align-items: center;
                border-bottom: 0;
                position: relative;
                width: auto;
                flex: 0 1 auto;
                min-width: 0;
            }
            .lp-nav-desktop-menu .lp-nav-link {
                flex-shrink: 1;
                min-width: 0;
                padding: 0.45rem 0.75rem;
                font-size: 0.85rem;
                white-space: nowrap;
            }
            .lp-nav-desktop-menu .lp-nav-link span {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                max-width: 100%;
            }
            .lp-nav-desktop-menu .lp-nav-item.dropdown { position: relative; }
            .lp-navbar .lp-brand {
                flex: 0 1 auto;
                min-width: 0;
                max-width: 100%;
                padding-left: 0.5rem;
            }
            .lp-navbar .lp-brand span {
                white-space: nowrap;
                max-width: none;
                min-width: 0;
            }
            .lp-cta-desktop { display: inline-flex; flex-shrink: 0; }
        }

        /* ============= Hero ============= */
        .lp-hero {
            position: relative;
            min-height: 100svh;
            min-height: 100dvh;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: #fff;
            overflow: hidden;
            isolation: isolate;
        }
        .lp-hero-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
            z-index: 0;
            transform: scale(1.05);
            animation: lp-hero-zoom 20s ease-in-out infinite alternate;
        }
        @keyframes lp-hero-zoom {
            from { transform: scale(1.05); }
            to { transform: scale(1.15); }
        }
        @media (prefers-reduced-motion: reduce) {
            .lp-hero-bg { animation: none; transform: scale(1); }
        }
        .lp-hero-bg::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(0, 0, 0, 0.35) 0%, rgba(0, 0, 0, 0.55) 100%);
        }
        .lp-hero-bg::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 30%, rgba(255, 255, 255, 0.08) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(255, 255, 255, 0.06) 0%, transparent 40%),
                linear-gradient(180deg, transparent 60%, rgba(0, 0, 0, 0.25) 100%);
        }
        .lp-hero-content {
            position: relative;
            z-index: 1;
            padding: clamp(5rem, 12vh, 7rem) 0 clamp(2rem, 6vh, 4rem);
            text-align: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        .lp-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: #fff;
            padding: 0.5rem 1.1rem;
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.25);
            margin-bottom: 1.5rem;
            letter-spacing: 0.05em;
        }
        .lp-pill .dot {
            width: 8px; height: 8px;
            background: var(--lp-accent);
            border-radius: 50%;
            box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
            animation: lp-pulse 2s ease-in-out infinite;
        }
        @keyframes lp-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7); }
            50% { box-shadow: 0 0 0 8px rgba(245, 158, 11, 0); }
        }
        @media (prefers-reduced-motion: reduce) {
            .lp-pill .dot { animation: none; }
        }
        .lp-hero h1 {
            font-size: clamp(2.25rem, 5.5vw, 3.75rem);
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 1.25rem;
            letter-spacing: -0.025em;
            text-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
        }
        .lp-hero p.lead {
            font-size: clamp(1rem, 1.6vw, 1.15rem);
            max-width: 640px;
            margin: 0 auto 2.25rem;
            opacity: 0.95;
            line-height: 1.6;
        }
        .lp-hero-actions {
            display: flex;
            gap: 0.85rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .lp-btn-light {
            background: #fff;
            color: var(--lp-primary);
            font-weight: 600;
            padding: 0.75rem 1.75rem;
            border-radius: 999px;
            border: 0;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            text-align: center;
        }
        .lp-btn-light:hover {
            color: var(--lp-primary);
            transform: translateY(-2px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
        }
        .lp-btn-outline-light {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: #fff;
            font-weight: 600;
            padding: 0.75rem 1.75rem;
            border-radius: 999px;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            text-align: center;
        }
        .lp-btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            transform: translateY(-2px);
        }

        /* ============= Sections ============= */
        section.lp-section { padding: 4rem 0; position: relative; }
        section.lp-section.lp-section-sm { padding: 3rem 0; }
        section.lp-section + section.lp-section { padding-top: 4rem; }
        .lp-bg-soft { background: #ffffff; }

        /* ============= Section dividers ============= */
        /* Pemisah halus antar-section: gradient line + diamond center.
           Ditampilkan otomatis di antara section.lp-section pada halaman home.
           Tidak menambah markup, tidak kaku, responsif. */
        .lp-section + .lp-section::before {
            content: "";
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            width: min(360px, 60%);
            height: 18px;
            background-image:
                radial-gradient(circle at 50% 50%, var(--lp-primary) 0 3px, transparent 3px),
                linear-gradient(90deg, transparent 0%, rgba(15,23,42,.08) 18%, rgba(15,23,42,.18) 50%, rgba(15,23,42,.08) 82%, transparent 100%);
            background-repeat: no-repeat;
            background-size: 7px 7px, 100% 1px;
            background-position: center, center;
            opacity: .85;
            pointer-events: none;
        }
        .lp-section + .lp-section.lp-bg-soft::before,
        .lp-bg-soft + .lp-section::before {
            background-image:
                radial-gradient(circle at 50% 50%, var(--lp-primary) 0 3px, transparent 3px),
                linear-gradient(90deg, transparent 0%, rgba(37,99,235,.22) 18%, rgba(37,99,235,.45) 50%, rgba(37,99,235,.22) 82%, transparent 100%);
        }
        @media (max-width: 575.98px) {
            .lp-section + .lp-section::before { width: 78%; height: 14px; }
        }
        @media (prefers-reduced-motion: reduce) {
            .lp-section + .lp-section::before { opacity: .55; }
        }
        .lp-section-title {
            font-weight: 800;
            font-size: clamp(1.65rem, 2.8vw, 2.1rem);
            margin-bottom: 0.6rem;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .lp-section-eyebrow {
            display: inline-block;
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--lp-muted);
            text-transform: uppercase;
            letter-spacing: 0.12em;
            margin-bottom: 0.75rem;
        }
        .lp-section-sub {
            color: var(--lp-muted);
            max-width: 600px;
            margin: 0 auto;
            font-size: 1.02rem;
            line-height: 1.6;
        }
        /* Header section (eyebrow+title+sub) → jarak konsisten ke konten. */
        .lp-section-head { margin-bottom: 3rem; }
        .lp-section-head.lp-section-head-sm { margin-bottom: 2rem; }
        @media (max-width: 767.98px) {
            .lp-section-head { margin-bottom: 2rem; }
        }

        /* ============= Card (shared) ============= */
        .lp-glass {
            background: #ffffff;
            border: 0;
            border-radius: var(--lp-radius-lg);
            box-shadow: var(--lp-shadow);
            transition: transform 0.3s var(--lp-ease), box-shadow 0.3s var(--lp-ease);
        }
        .lp-glass:hover {
            transform: translateY(-6px);
            box-shadow: var(--lp-shadow-strong);
        }

        /* Standar card: padding konsisten & radius konsisten.
           Pakai .lp-card di seluruh view (ganti inline style + override ad-hoc). */
        .lp-card {
            padding: var(--lp-card-pad-lg);
            border-radius: var(--lp-radius-lg);
        }
        .lp-card-sm { padding: var(--lp-card-pad); }
        .lp-card-lg { padding: 2rem; }

        /* ============= Stats ============= */
        .lp-stat-card {
            padding: var(--lp-card-pad-lg);
            display: flex;
            align-items: center;
            gap: 1.1rem;
            border-radius: var(--lp-radius-lg);
        }
        .lp-stat-icon {
            width: 58px; height: 58px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
            transition: transform 0.4s var(--lp-ease);
        }
        .lp-stat-card:hover .lp-stat-icon { transform: rotate(-8deg) scale(1.08); }
        .lp-stat-icon.is-blue { background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.15), rgba(var(--lp-primary-rgb), 0.3)); color: var(--lp-primary); }
        .lp-stat-icon.is-green { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
        .lp-stat-icon.is-amber { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
        .lp-stat-value {
            font-size: 1.85rem;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 0.25rem;
            background: linear-gradient(135deg, var(--lp-text), var(--lp-primary));
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .lp-stat-label { color: var(--lp-muted); font-size: 0.88rem; font-weight: 500; }

        /* ============= Sambutan ============= */
        .lp-welcome-img {
            border-radius: var(--lp-radius-xl);
            overflow: hidden;
            position: relative;
            box-shadow: 0 30px 60px -20px rgba(15, 23, 42, 0.2);
            aspect-ratio: 1 / 1;
        }
        .lp-welcome-img img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .lp-welcome-img::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 50%, rgba(15, 23, 42, 0.5) 100%);
        }
        .lp-welcome-quote {
            position: absolute;
            bottom: 24px;
            left: 24px;
            right: 24px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
            z-index: 1;
        }
        .lp-welcome-quote .bi { font-size: 1.5rem; color: var(--lp-primary); }
        .lp-welcome-quote span { font-size: 0.9rem; color: var(--lp-text); font-weight: 600; }
        .lp-divider {
            width: 56px; height: 4px; border-radius: 2px;
            background: linear-gradient(90deg, var(--lp-primary), var(--lp-accent-2));
            margin-bottom: 1.25rem;
        }
        .lp-text-muted-soft { color: var(--lp-muted); line-height: 1.75; font-size: 0.98rem; }

        /* ============= Sambutan: actions + tombol Baca Lengkap ============= */
        .lp-welcome-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem 1.5rem;
            margin-top: 1.5rem;
        }
        .lp-welcome-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .lp-welcome-readmore {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.15rem;
            border: 0;
            border-radius: 9999px;
            background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
            color: #fff;
            font-weight: 600;
            font-size: 0.92rem;
            letter-spacing: 0.01em;
            cursor: pointer;
            box-shadow: 0 12px 26px rgba(var(--lp-primary-rgb), 0.32);
            transition: transform 0.25s var(--lp-ease, cubic-bezier(0.4, 0, 0.2, 1)),
                        box-shadow 0.25s var(--lp-ease, cubic-bezier(0.4, 0, 0.2, 1));
        }
        .lp-welcome-readmore:hover,
        .lp-welcome-readmore:focus-visible {
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(var(--lp-primary-rgb), 0.42);
            outline: 0;
        }
        .lp-welcome-readmore i { transition: transform 0.25s var(--lp-ease, cubic-bezier(0.4, 0, 0.2, 1)); }
        .lp-welcome-readmore:hover i { transform: translateX(3px); }
        .lp-welcome-readmore-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 22px;
            height: 22px;
            padding: 0 0.45rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.22);
            color: #fff;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1;
        }

        /* ============= Sambutan: modal popup ============= */
        .lp-welcome-modal .modal-content {
            border: 0;
            border-radius: var(--lp-radius-xl, 20px);
            overflow: hidden;
            background: #fff;
            box-shadow: 0 30px 80px rgba(15, 23, 42, 0.25);
        }
        .lp-welcome-modal-head {
            position: relative;
            padding: 1.5rem 4.75rem 1rem 1.5rem;
            border-bottom: 1px solid rgba(15, 23, 42, 0.06);
            background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.06), rgba(255, 255, 255, 0));
        }
        .lp-welcome-modal-head-text { display: flex; flex-direction: column; gap: 0.25rem; }
        .lp-welcome-modal-title {
            font-weight: 700;
            color: var(--lp-text, #0f172a);
            margin: 0;
        }
        .lp-welcome-modal-close {
            position: absolute;
            top: 14px;
            right: 14px;
            width: 38px;
            height: 38px;
            border-radius: 9999px;
            border: 0;
            background: #f1f5f9;
            color: #334155;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.05rem;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.25s ease, color 0.2s ease;
            z-index: 5;
        }
        .lp-welcome-modal-close:hover,
        .lp-welcome-modal-close:focus-visible {
            background: #e2e8f0;
            color: #0f172a;
            outline: 0;
        }
        .lp-welcome-modal-close:hover { transform: rotate(90deg); }
        .lp-welcome-modal-body {
            padding: 1.5rem;
            background: #fff;
            color: var(--lp-text, #0f172a);
        }
        .lp-welcome-modal-avatar {
            width: 56px;
            height: 56px;
            border-radius: 9999px;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.15);
        }
        .lp-welcome-modal-quote {
            margin: 1.25rem 0 0;
            padding: 1rem 1.25rem;
            background: rgba(var(--lp-primary-rgb), 0.06);
            border-left: 4px solid var(--lp-primary);
            border-radius: 12px;
            display: flex;
            align-items: flex-start;
            gap: 0.6rem;
            font-style: italic;
            color: var(--lp-text, #0f172a);
        }
        .lp-welcome-modal-quote .bi { font-size: 1.4rem; color: var(--lp-primary); flex-shrink: 0; }
        .lp-welcome-modal-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(15, 23, 42, 0.12), transparent);
            margin: 1.25rem 0;
        }
        .lp-welcome-modal-paragraphs p {
            color: var(--lp-muted, #475569);
            line-height: 1.8;
            font-size: 0.98rem;
            margin-bottom: 1rem;
        }
        .lp-welcome-modal-paragraphs p:last-child { margin-bottom: 0; }

        @media (max-width: 575.98px) {
            /* Saat modal-fullscreen-sm-down aktif: hilangkan lengkung pojok & shadow
               supaya benar-benar full-screen kotak. */
            .lp-welcome-modal .modal-content {
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            .lp-welcome-modal-head {
                padding: 1.15rem 4rem 0.85rem 1.15rem;
                border-radius: 0;
            }
            .lp-welcome-modal-close { top: 12px; right: 12px; }
            .lp-welcome-modal-body { padding: 1.15rem; }
            .lp-welcome-actions { gap: 0.75rem; }
        }

        /* ============= Keunggulan ============= */
        .lp-feature-card {
            padding: var(--lp-card-pad-lg);
            height: 100%;
            position: relative;
            overflow: hidden;
            border-radius: var(--lp-radius-lg);
        }
        .lp-feature-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--lp-primary), var(--lp-accent-2));
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.4s var(--lp-ease);
        }
        .lp-feature-card:hover::before { transform: scaleX(1); }
        .lp-feature-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 1.1rem;
            transition: transform 0.4s var(--lp-ease);
        }
        .lp-feature-card:hover .lp-feature-icon { transform: scale(1.1) rotate(-5deg); }
        .lp-feature-icon.is-blue { background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.15), rgba(var(--lp-primary-rgb), 0.3)); color: var(--lp-primary); }
        .lp-feature-icon.is-green { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
        .lp-feature-icon.is-amber { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
        .lp-feature-icon.is-pink { background: linear-gradient(135deg, #fce7f3, #fbcfe8); color: #db2777; }
        .lp-feature-icon.is-purple { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: #7c3aed; }
        .lp-feature-icon.is-cyan { background: linear-gradient(135deg, #cffafe, #a5f3fc); color: #0891b2; }
        .lp-feature-title { font-weight: 700; font-size: 1.1rem; margin-bottom: 0.6rem; }
        .lp-feature-desc { color: var(--lp-muted); font-size: 0.92rem; line-height: 1.65; margin: 0; }

        /* ============= Program ============= */
        .lp-program-card {
            background: #ffffff;
            border: 0;
            border-radius: var(--lp-radius-lg);
            overflow: hidden;
            transition: transform 0.3s var(--lp-ease), box-shadow 0.3s var(--lp-ease);
            height: 100%;
            box-shadow: var(--lp-shadow);
        }
        .lp-program-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 30px 60px -16px rgba(var(--lp-primary-rgb), 0.35);
        }
        .lp-program-card .lp-thumb {
            position: relative;
            overflow: hidden;
            aspect-ratio: 16/10;
        }
        .lp-program-card .lp-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            transition: transform 0.6s var(--lp-ease);
        }
        .lp-program-card:hover .lp-thumb img { transform: scale(1.08); }
        .lp-program-card .lp-thumb::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 60%, rgba(15, 23, 42, 0.3) 100%);
        }
        .lp-program-card .lp-body { padding: 1.5rem 1.5rem 1.75rem; }
        .lp-program-card .lp-tag {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            background: rgba(var(--lp-primary-rgb), 0.1);
            color: var(--lp-primary);
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            margin-bottom: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .lp-program-card h5 { font-weight: 700; font-size: 1.1rem; margin-bottom: 0.4rem; }
        .lp-program-card p { color: var(--lp-muted); font-size: 0.9rem; margin: 0; line-height: 1.6; }
        .lp-program-card h5 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 2.64em;
        }
        .lp-program-card .lp-body .lp-link-soft {
            font-size: 0.85rem;
            text-decoration: none;
            color: var(--lp-primary);
            font-weight: 600;
        }
        .lp-program-card .lp-body .lp-link-soft:hover {
            gap: 0.5rem;
        }
        .lp-program-card .lp-post-meta {
            color: var(--lp-muted);
            font-size: 0.8rem;
            margin: 0;
        }

        /* ============= Events ============= */
        .lp-event-card {
            padding: var(--lp-card-pad-lg);
            display: flex;
            gap: 1.25rem;
            align-items: flex-start;
            border-radius: var(--lp-radius-lg);
        }
        .lp-event-date {
            flex-shrink: 0;
            width: 72px;
            text-align: center;
            background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
            color: #fff;
            border-radius: 16px;
            padding: 0.75rem 0.5rem;
            box-shadow: 0 8px 20px rgba(var(--lp-primary-rgb), 0.25);
        }
        .lp-event-date .day {
            font-size: 1.6rem;
            font-weight: 800;
            line-height: 1;
            display: block;
        }
        .lp-event-date .month {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 0.2rem;
            display: block;
            opacity: 0.9;
        }
        .lp-event-body { flex: 1; min-width: 0; }
        .lp-event-title {
            font-weight: 700;
            font-size: 1.02rem;
            margin-bottom: 0.5rem;
            color: var(--lp-text);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
        }
        .lp-event-meta {
            color: var(--lp-muted);
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-top: 0.35rem;
            min-width: 0;
        }
        .lp-event-meta span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            min-width: 0;
        }

        /* ============= Announcement ============= */
        .lp-ann-card {
            padding: var(--lp-card-pad-lg);
            position: relative;
            border-left: 4px solid var(--lp-accent) !important;
            border-radius: var(--lp-radius-lg);
        }
        .lp-ann-card .lp-ann-date {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            color: var(--lp-muted);
            font-size: 0.78rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }
        .lp-ann-card .lp-ann-title {
            font-weight: 700;
            font-size: 1.05rem;
            line-height: 1.4;
            margin: 0 0 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .lp-ann-card .lp-ann-excerpt {
            color: var(--lp-muted);
            font-size: 0.88rem;
            line-height: 1.55;
            margin: 0 0 0.75rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .lp-ann-card .lp-link-soft {
            font-size: 0.85rem;
            text-decoration: none;
            color: var(--lp-primary);
            font-weight: 600;
        }

        /* ============= Gallery ============= */
        .lp-gallery-item {
            position: relative;
            border-radius: var(--lp-radius-lg);
            overflow: hidden;
            aspect-ratio: 1;
            box-shadow: var(--lp-shadow);
        }
        .lp-media-card {
            padding: 0;
            overflow: hidden;
            border-radius: var(--lp-radius-lg);
        }
        .lp-media-card .lp-media-body { padding: 1.25rem 1.5rem 1.5rem; }
        .lp-gallery-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s var(--lp-ease);
        }
        .lp-gallery-item:hover img { transform: scale(1.1); }
        .lp-gallery-item::after {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, transparent 50%, rgba(15, 23, 42, 0.6) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .lp-gallery-item:hover::after { opacity: 1; }
        .lp-gallery-item .lp-gallery-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            color: #fff;
            font-weight: 600;
            font-size: 0.9rem;
            z-index: 1;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.3s var(--lp-ease);
        }
        .lp-gallery-item:hover .lp-gallery-overlay {
            transform: translateY(0);
            opacity: 1;
        }

        /* ============= Footer ============= */
        footer.lp-footer {
            position: relative;
            background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 100%);
            color: #cbd5e1;
            padding: 4rem 0 1.5rem;
            font-size: 0.92rem;
            overflow: hidden;
        }
        footer.lp-footer::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        }
        footer.lp-footer h5, footer.lp-footer h6 { color: #fff; font-weight: 700; margin-bottom: 1rem; }
        footer.lp-footer .lp-brand {
            flex: none;
            flex-wrap: wrap;
            white-space: normal;
        }
        footer.lp-footer .lp-brand span { max-width: none; white-space: normal; }
        footer.lp-footer a { color: #cbd5e1; transition: color 0.2s ease; }
        footer.lp-footer a:hover { color: #fff; }
        .lp-social {
            display: flex;
            gap: 0.6rem;
            margin-top: 1rem;
        }
        .lp-social a {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            transition: all 0.25s var(--lp-ease);
        }
        .lp-social a:hover {
            background: linear-gradient(135deg, var(--lp-primary), var(--lp-accent-2));
            transform: translateY(-3px);
        }
        .lp-footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            font-size: 0.85rem;
            color: #94a3b8;
        }

        /* ============= Utilities ============= */
        .lp-link-soft {
            color: var(--lp-text);
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: underline;
            text-decoration-color: rgba(var(--lp-primary-rgb), 0.3);
            text-underline-offset: 3px;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            transition: gap 0.2s var(--lp-ease), color 0.2s ease;
        }
        .lp-link-soft:hover { color: var(--lp-primary-dark); gap: 0.5rem; text-decoration-color: var(--lp-primary); }

        /* ============= Jenjang Pendidikan ============= */
        .lp-jenjang-card {
            padding: 2rem var(--lp-card-pad-lg);
            text-align: center;
            position: relative;
            overflow: hidden;
            height: 100%;
            border-radius: var(--lp-radius-lg);
        }
        .lp-jenjang-card::before {
            content: "";
            position: absolute;
            top: -40px;
            right: -40px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            opacity: 0.1;
            transition: transform 0.5s var(--lp-ease);
        }
        .lp-jenjang-card:hover::before { transform: scale(1.6); }
        .lp-jenjang-card.tk::before { background: var(--lp-pink); }
        .lp-jenjang-card.sd::before { background: var(--lp-primary); }
        .lp-jenjang-card.smp::before { background: var(--lp-green); }
        .lp-jenjang-card.sma::before { background: var(--lp-purple); }
        .lp-jenjang-icon {
            width: 84px;
            height: 84px;
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin-bottom: 1.25rem;
            position: relative;
            transition: transform 0.4s var(--lp-ease);
        }
        .lp-jenjang-card:hover .lp-jenjang-icon {
            transform: translateY(-6px) rotate(-6deg);
        }
        .lp-jenjang-card.tk .lp-jenjang-icon { background: linear-gradient(135deg, #fce7f3, #fbcfe8); color: var(--lp-pink); }
        .lp-jenjang-card.sd .lp-jenjang-icon { background: linear-gradient(135deg, rgba(var(--lp-primary-rgb), 0.15), rgba(var(--lp-primary-rgb), 0.3)); color: var(--lp-primary); }
        .lp-jenjang-card.smp .lp-jenjang-icon { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: var(--lp-green); }
        .lp-jenjang-card.sma .lp-jenjang-icon { background: linear-gradient(135deg, #ede9fe, #ddd6fe); color: var(--lp-purple); }
        .lp-jenjang-age {
            display: inline-block;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            margin-bottom: 0.75rem;
            letter-spacing: 0.05em;
        }
        .lp-jenjang-card.tk .lp-jenjang-age { background: var(--lp-pink); color: #fff; }
        .lp-jenjang-card.sd .lp-jenjang-age { background: var(--lp-primary); color: #fff; }
        .lp-jenjang-card.smp .lp-jenjang-age { background: var(--lp-green); color: #fff; }
        .lp-jenjang-card.sma .lp-jenjang-age { background: var(--lp-purple); color: #fff; }
        .lp-jenjang-title { font-weight: 800; font-size: 1.2rem; margin-bottom: 0.5rem; }
        .lp-jenjang-desc { color: var(--lp-muted); font-size: 0.9rem; line-height: 1.6; margin: 0; }

        /* ============= CTA Strip ============= */
        .lp-cta-strip {
            position: relative;
            background: linear-gradient(135deg, var(--lp-primary) 0%, var(--lp-purple) 50%, var(--lp-pink) 100%);
            border-radius: 28px;
            padding: 3.5rem 2.5rem;
            color: #fff;
            overflow: hidden;
            box-shadow: 0 30px 60px -20px rgba(var(--lp-primary-rgb), 0.4);
        }
        .lp-cta-strip::before {
            content: "";
            position: absolute;
            top: -100px;
            right: -100px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
        }
        .lp-cta-strip::after {
            content: "";
            position: absolute;
            bottom: -80px;
            left: -80px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.08);
        }
        .lp-cta-strip > * { position: relative; z-index: 1; }
        .lp-cta-strip h3 {
            font-weight: 800;
            font-size: clamp(1.5rem, 2.6vw, 2rem);
            line-height: 1.2;
            margin-bottom: 0.75rem;
            letter-spacing: -0.02em;
        }
        .lp-cta-strip p { opacity: 0.95; font-size: 1.05rem; margin-bottom: 0; max-width: 520px; }
        .lp-cta-btn {
            background: #fff;
            color: var(--lp-primary);
            font-weight: 700;
            padding: 0.85rem 2rem;
            border-radius: 999px;
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-align: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            white-space: nowrap;
        }
        .lp-cta-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
            color: var(--lp-primary);
        }
        .lp-cta-btn-outline {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            color: #fff;
            font-weight: 600;
            padding: 0.85rem 1.75rem;
            border-radius: 999px;
            border: 1.5px solid rgba(255, 255, 255, 0.4);
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-align: center;
        }
        .lp-cta-btn-outline:hover {
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            transform: translateY(-3px);
        }

        /* Eyebrow kecil di atas judul — membuat CTA lebih terstruktur */
        .lp-cta-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            padding: .4rem .85rem;
            border-radius: 999px;
            margin-bottom: 1rem;
        }
        .lp-cta-eyebrow .bi { color: var(--lp-accent); }

        /* Poin-poin singkat di bawah paragraf — memecah dinding teks panjang */
        .lp-cta-points {
            list-style: none;
            padding: 0;
            margin: 1.1rem 0 0;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: .65rem 1rem;
            max-width: 560px;
        }
        .lp-cta-points li {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            color: rgba(255, 255, 255, 0.95);
            font-size: .88rem;
            font-weight: 500;
            line-height: 1.3;
        }
        .lp-cta-points .bi {
            color: var(--lp-accent);
            font-size: 1.05rem;
            flex: 0 0 auto;
        }

        /* Wrapper tombol: stack rapi dengan gap konsisten, center di mobile */
        .lp-cta-actions {
            display: inline-flex;
            flex-direction: column;
            align-items: stretch;
            gap: .65rem;
            width: 100%;
            max-width: 320px;
            margin-left: auto;
        }
        .lp-cta-actions .lp-cta-btn,
        .lp-cta-actions .lp-cta-btn-outline {
            justify-content: center;
            width: 100%;
        }
        .lp-cta-actions .lp-cta-btn {
            gap: .65rem;
            padding: .95rem 1.75rem;
            font-size: 1rem;
        }
        .lp-cta-actions .lp-cta-btn .bi {
            transition: transform .2s var(--lp-ease);
        }
        .lp-cta-actions .lp-cta-btn:hover .bi {
            transform: translateX(4px);
        }
        .lp-cta-meta {
            margin-top: .35rem;
            text-align: center;
            font-size: .78rem;
            color: rgba(255, 255, 255, 0.85);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .35rem;
        }
        .lp-cta-meta .bi { color: var(--lp-accent); }
        .lp-cta-registration {
            margin-top: 1rem;
            font-size: 0.9rem;
            line-height: 1.65;
            opacity: 0.92;
            max-width: 560px;
        }

        /* Responsive: tengah-tengah di tablet/mobile */
        @media (max-width: 991.98px) {
            .lp-cta-actions { margin: 0 auto; }
            .lp-cta-points { grid-template-columns: repeat(3, minmax(0, 1fr)); max-width: 100%; }
        }
        @media (max-width: 575.98px) {
            .lp-cta-points { grid-template-columns: 1fr 1fr; }
            .lp-cta-points li:nth-child(3) { grid-column: 1 / -1; }
        }

        /* ============= Floating badge ============= */
        .lp-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.85rem;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 999px;
            font-size: 0.78rem;
            font-weight: 600;
            color: #fff;
        }
        .lp-badge .bi { color: var(--lp-accent); }

        /* ============= Responsive (standar breakpoint) =============
           xs:  ≤575.98px  (HP kecil)
           sm:  576–767.98 (HP besar / phablet)
           md:  768–991.98 (tablet portrait)
           lg:  992–1199.98 (tablet landscape / laptop kecil)
           xl:  1200–1399.98 (laptop)
           xxl: ≥1400px    (desktop besar)
        ============================================================== */

        /* ===== XL : laptop 1200–1399 ===== */
        @media (max-width: 1399.98px) {
            :root { --lp-navbar-max: 1140px; }
            .lp-section-title { font-size: clamp(1.55rem, 2.6vw, 1.95rem); }
        }

        /* ===== LG : tablet landscape / laptop kecil 992–1199 ===== */
        @media (max-width: 1199.98px) {
            :root { --lp-navbar-max: 960px; }
            .lp-nav-link { padding: 0.4rem 0.65rem; font-size: 0.8rem; }
            .lp-cta { padding: 0.5rem 1rem; font-size: 0.85rem; }
            .lp-brand { font-size: 0.92rem; }
            .lp-brand span { max-width: none; }
            .lp-navbar { max-width: none; }
            .lp-nav-desktop-menu { gap: 0.15rem; padding: 0.4rem 0.25rem; }
            .lp-nav-desktop-menu .lp-nav-link { padding: 0.4rem 0.55rem; font-size: 0.78rem; }
            section.lp-section { padding: 3.5rem 0; }
            .lp-hero-content { padding: clamp(5rem, 12vh, 8rem) 0 clamp(2rem, 6vh, 5rem); }
            .lp-stat-card { padding: 1.35rem; }
            .lp-stat-value { font-size: 1.65rem; }
            .lp-cta-strip { padding: 3rem 2rem; }
            .lp-event-card { padding: 1.25rem; }
        }

        /* ===== MD : tablet portrait 768–991 ===== */
        @media (max-width: 991.98px) {
            section.lp-section { padding: 3rem 0; }
            section.lp-section + section.lp-section { padding-top: 3rem; }
            body.lp-inner > section:first-of-type { padding-top: 6.75rem; }
            .lp-hero-content { padding: clamp(4.5rem, 10vh, 7rem) 0 clamp(2rem, 6vh, 4rem); }
            .lp-hero h1 { font-size: clamp(1.95rem, 5vw, 2.75rem); }
            .lp-hero p.lead { font-size: 1rem; max-width: 580px; }
            .lp-stat-card { padding: 1.25rem; gap: 0.9rem; }
            .lp-stat-icon { width: 50px; height: 50px; font-size: 1.3rem; }
            .lp-stat-value { font-size: 1.55rem; }
            .lp-welcome-img { aspect-ratio: 4 / 3; max-width: 380px; margin: 0 auto; }
            .lp-section-head { margin-bottom: 2rem; }
            /* Cegah nama sekolah panjang melebar di HP/tablet */
            .lp-brand span {
                max-width: 50vw;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            /* Pastikan container tidak pernah lebih lebar dari viewport */
            .container { max-width: 100%; }
            .lp-cta-strip { padding: 2.5rem 1.75rem; border-radius: 24px; text-align: center; }
            .lp-cta-strip h3 { font-size: 1.6rem; }
            .lp-cta-strip p,
            .lp-cta-registration { margin-left: auto; margin-right: auto; }
            .lp-program-card .lp-body { padding: 1.35rem 1.35rem 1.6rem; }
            .lp-event-card { padding: 1.1rem; gap: 1.1rem; }
            .lp-event-date { width: 64px; }
            .lp-footer { padding: 3.25rem 0 1.25rem; }
        }

        /* ===== SM : HP besar 576–767 ===== */
        @media (max-width: 767.98px) {
            .container { padding-left: 1rem; padding-right: 1rem; }
            section.lp-section { padding: 2.5rem 0; }
            section.lp-section + section.lp-section { padding-top: 2.5rem; }
            html { scroll-padding-top: 88px; }
            body.lp-inner > section:first-of-type { padding-top: 6.25rem; }
            .lp-hero-content { padding: clamp(4rem, 9vh, 6.5rem) 0 clamp(1.75rem, 5vh, 3.5rem); }
            .lp-hero h1 { font-size: clamp(1.85rem, 6.5vw, 2.4rem); }
            .lp-hero p.lead { font-size: 0.98rem; margin-bottom: 1.75rem; }
            .lp-hero-actions { gap: 0.6rem; }
            .lp-btn-light, .lp-btn-outline-light {
                padding: 0.7rem 1.35rem;
                font-size: 0.9rem;
            }
            .lp-stat-card { padding: 1.1rem; gap: 0.85rem; }
            .lp-stat-icon { width: 46px; height: 46px; font-size: 1.2rem; border-radius: 12px; }
            .lp-stat-value { font-size: 1.4rem; }
            .lp-stat-label { font-size: 0.8rem; }
            .lp-welcome-img { aspect-ratio: 4 / 3; max-width: 360px; margin: 0 auto; }
            .lp-welcome-quote { left: 16px; right: 16px; bottom: 16px; padding: 0.85rem 1rem; }
            .lp-cta-strip { padding: 2.25rem 1.5rem; border-radius: 22px; }
            .lp-cta-strip h3 { font-size: 1.4rem; }
            .lp-cta-strip p { font-size: 0.98rem; }
            .lp-cta-actions { max-width: 100%; }
            .lp-card, .lp-card-sm, .lp-stat-card, .lp-feature-card, .lp-ann-card { padding: 1.15rem; }
            .lp-card-lg { padding: 1.35rem; }
            .lp-jenjang-card { padding: 1.5rem 1.15rem; }
            .lp-welcome-quote span { font-size: 0.82rem; }
            .lp-cta-points { grid-template-columns: 1fr 1fr; gap: 0.5rem 0.75rem; }
            .lp-cta-points li:nth-child(3) { grid-column: 1 / -1; }
            .lp-section-head { margin-bottom: 1.75rem; }
            .lp-program-card .lp-body { padding: 1.25rem 1.25rem 1.5rem; }
            .lp-program-card h5 { font-size: 1rem; }
            .lp-event-card { padding: 1rem; gap: 1rem; }
            .lp-event-date { width: 60px; padding: 0.6rem 0.4rem; }
            .lp-event-date .day { font-size: 1.35rem; }
            .lp-event-title { font-size: 0.95rem; }
            .lp-event-meta { font-size: 0.8rem; }
            .lp-gallery-item { border-radius: 14px; }
            .lp-footer { padding: 3rem 0 1.25rem; }
            .lp-section-divider { width: 70%; }
        }

        /* ===== XS : HP kecil ≤575 ===== */
        @media (max-width: 575.98px) {
            .container { padding-left: 0.85rem; padding-right: 0.85rem; }
            .lp-brand { font-size: 0.95rem; gap: 0.35rem; }
            .lp-brand img { height: 24px; }
            .lp-nav-toggler { width: 38px; height: 38px; font-size: 1.15rem; }
            html { scroll-padding-top: 80px; }
            body.lp-inner > section:first-of-type { padding-top: 5.75rem; }
            .lp-hero-content { padding: clamp(3.5rem, 8vh, 5.75rem) 0 clamp(1.5rem, 4vh, 2.75rem); }
            .lp-hero h1 { font-size: 1.75rem; margin-bottom: 1rem; }
            .lp-hero p.lead { font-size: 0.92rem; margin-bottom: 1.5rem; }
            .lp-hero-actions { flex-direction: column; align-items: center; width: auto; max-width: 270px; margin: 0 auto; gap: 0.5rem; }
            .lp-hero-actions .lp-btn-light,
            .lp-hero-actions .lp-btn-outline-light {
                width: 100%;
                padding: 0.6rem 1.25rem;
                font-size: 0.88rem;
            }
            .lp-badge { font-size: 0.72rem; padding: 0.35rem 0.7rem; }
            .lp-section-title { font-size: 1.4rem; }
            .lp-section-eyebrow { font-size: 0.72rem; }
            .lp-cta-strip { padding: 1.75rem 1.15rem; border-radius: 18px; }
            .lp-cta-strip h3 { font-size: 1.25rem; }
            .lp-cta-btn, .lp-cta-btn-outline { padding: 0.65rem 1.15rem; font-size: 0.88rem; white-space: normal; max-width: 240px; margin-left: auto; margin-right: auto; }
            .lp-cta-registration { font-size: 0.85rem; }
            .lp-program-card h5 { min-height: 0; }
            .lp-welcome-quote { flex-direction: column; align-items: flex-start; gap: 0.35rem; }
            .lp-cta-points { grid-template-columns: 1fr; }
            .lp-cta-points li:nth-child(3) { grid-column: auto; }
            .lp-event-card { padding: 0.85rem; gap: 0.85rem; }
            .lp-event-date { width: 56px; padding: 0.55rem 0.35rem; }
            .lp-event-date .day { font-size: 1.25rem; }
            .lp-event-date .month { font-size: 0.65rem; }
            .lp-welcome-quote { font-size: 0.85rem; padding: 0.75rem 0.85rem; }
            .lp-welcome-quote .bi { font-size: 1.25rem; }
            .lp-footer { padding: 2.5rem 0 1rem; font-size: 0.88rem; }
            .lp-footer h5, .lp-footer h6 { font-size: 1rem; }
            .lp-social a { width: 36px; height: 36px; }
            .lp-footer-bottom { justify-content: center !important; text-align: center; }
        }

        /* ===== XXL : desktop besar ≥1400 ===== */
        @media (min-width: 1400px) {
            :root { --lp-navbar-max: 1320px; }
            .container { max-width: 1320px; }
            section.lp-section { padding: 5rem 0; }
            .lp-hero-content { padding: clamp(6rem, 13vh, 10rem) 0 clamp(3rem, 7vh, 7rem); }
            .lp-section-title { font-size: 2.25rem; }
        }

        /* ===== HP: background hero full layar (110lvh untuk menutupi celah putih di bawah) ===== */
        @media (max-width: 991.98px) {
            .lp-hero {
                min-height: 100vh;
                min-height: 100dvh;
                min-height: 110lvh;
                width: 100%;
                max-width: 100vw;
            }
            .lp-hero-bg {
                position: absolute;
                inset: 0;
                width: 100%;
                height: 100%;
                min-height: 100vh;
                min-height: 100dvh;
                min-height: 110lvh;
                background-size: cover;
                background-position: center center;
                background-repeat: no-repeat;
            }
            /* Lock container Bootstrap ke viewport HP agar tidak overflow */
            .container { max-width: 100vw; }
            /* Pastikan section tidak melebar di luar viewport */
            section.lp-section { max-width: 100vw; overflow: hidden; }
        }

        /* ===== Orientasi landscape HP (tinggi <600): hero tidak 100vh ===== */
        @media (max-height: 600px) and (orientation: landscape) {
            .lp-hero { min-height: 0; padding: 5.5rem 0 2.5rem; }
            .lp-hero-content { padding: 0; }
        }

        /* ===== Safe area iOS (notch) ===== */
        @supports (padding: max(0px)) {
            .lp-footer { padding-bottom: max(1.5rem, env(safe-area-inset-bottom)); }
            .lp-nav-menu { padding-bottom: env(safe-area-inset-bottom); }
        }

        @media (hover: none) {
            .lp-glass:hover,
            .lp-program-card:hover,
            .lp-feature-card:hover,
            .lp-jenjang-card:hover,
            .lp-cta:hover,
            .lp-btn-light:hover,
            .lp-btn-outline-light:hover,
            .lp-cta-btn:hover,
            .lp-cta-btn-outline:hover {
                transform: none;
            }
            .lp-gallery-item::after { opacity: 1; }
            .lp-gallery-item .lp-gallery-overlay {
                transform: none;
                opacity: 1;
            }
        }
    </style>

    @yield('style')
</head>
<body class="{{ request()->routeIs('halaman-publik.beranda') ? 'lp-home' : 'lp-inner' }}">
<div class="lp-scroll-progress" id="lpScrollProgress"></div>

<div class="lp-nav-backdrop" id="lpNavBackdrop" aria-hidden="true"></div>

@php
    $currentPath = trim(request()->path(), '/');
    $lpMenuPath = function (string $url) {
        $url = trim($url);
        if ($url === '' || str_starts_with($url, '#') || str_starts_with(strtolower($url), 'http')) {
            return null;
        }
        $parsed = parse_url($url, PHP_URL_PATH);
        $path = $parsed !== null ? $parsed : $url;
        return trim($path, '/');
    };

    // Deteksi apakah menu dinamis sudah punya link 'Berita' (path 'berita').
    // Kalau belum, kita sisipkan link hardcoded agar selalu tersedia,
    // sekaligus menghindari duplikat jika menu sudah menyediakannya.
    $headerMenus = $menus['header'] ?? collect();
    $hasBeritaMenu = $headerMenus->contains(function ($m) use ($lpMenuPath) {
        $p = $lpMenuPath($m->url);
        return $p === 'berita';
    }) || $headerMenus->contains(function ($m) use ($lpMenuPath) {
        return $m->child_items->contains(function ($c) use ($lpMenuPath) {
            return $lpMenuPath($c->url) === 'berita';
        });
    });
    $beritaActive = $currentPath === 'berita' || str_starts_with($currentPath, 'berita/');
@endphp

{{-- Drawer mobile: dirender terpisah di akhir navbar-wrap agar tidak
     bergantung pada `display: contents` (yang flaw-nya beragam di browser).
     Desktop menggunakan menu inline di .lp-nav-desktop-menu. --}}
<div class="lp-navbar-wrap">
    <div class="lp-navbar-inner">
        <nav class="lp-navbar {{ request()->routeIs('halaman-publik.beranda') ? '' : 'is-page' }}" id="lpNavbar">
            <a class="lp-brand" href="{{ route('halaman-publik.beranda') }}">
                @if ($setting->logo)
                    <img src="{{ Storage::disk('public')->url('landing/' . $setting->logo) }}" alt="">
                @endif
                <span>{{ $setting->school_name ?? 'Sekolah' }}</span>
            </a>

            <div class="lp-nav-desktop-menu">
                @if (! $hasBeritaMenu)
                    <div class="lp-nav-item">
                        <a class="lp-nav-link {{ $beritaActive ? 'active' : '' }}" href="{{ route('halaman-publik.daftar-artikel') }}">
                            <span>Berita</span>
                        </a>
                    </div>
                @endif
                @foreach ($menus['header'] ?? [] as $item)
                    @php
                        $itemPath = $lpMenuPath($item->url);
                        $childActive = $item->child_items->contains(function ($child) use ($lpMenuPath, $currentPath) {
                            $childPath = $lpMenuPath($child->url);
                            return $childPath !== null && $childPath === $currentPath;
                        });
                        $isActive = ($itemPath !== null && $itemPath === $currentPath) || $childActive;
                    @endphp
                    @if ($item->child_items->isNotEmpty())
                        <div class="lp-nav-item dropdown" data-dropdown>
                            <a class="lp-nav-link {{ $isActive ? 'active' : '' }}" href="{{ $item->url }}" data-dropdown-toggle>
                                <span>{{ $item->title }}</span>
                                <i class="bi bi-chevron-down"></i>
                            </a>
                            <div class="lp-dropdown">
                                @foreach ($item->child_items as $child)
                                    @php
                                        $childPath = $lpMenuPath($child->url);
                                        $isChildActive = $childPath !== null && $childPath === $currentPath;
                                    @endphp
                                    <a href="{{ $child->url }}" class="{{ $isChildActive ? 'active' : '' }}">
                                        <i class="bi bi-arrow-right-short"></i>
                                        {{ $child->title }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="lp-nav-item">
                            <a class="lp-nav-link {{ $isActive ? 'active' : '' }}" href="{{ $item->url }}">{{ $item->title }}</a>
                        </div>
                    @endif
                @endforeach
            </div>

            <button class="lp-nav-toggler" id="lpNavToggle" type="button" aria-label="Buka menu" aria-controls="lpNavMenu" aria-expanded="false">
                <i class="bi bi-list" id="lpNavToggleIcon"></i>
            </button>
        </nav>

        <a href="{{ tenant()?->adminUrl() ?: '#' }}" class="lp-cta lp-cta-desktop" target="_blank" rel="noopener noreferrer">SabIT</a>
    </div>
</div>

@yield('content')

<footer class="lp-footer" id="kontak">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-5 col-sm-12">
                <h5 class="lp-brand mb-2" style="color:#fff;">
                    @if ($setting->logo)
                        <img src="{{ Storage::disk('public')->url('landing/' . $setting->logo) }}" alt="">
                    @endif
                    {{ $setting->school_name ?? 'Sekolah' }}
                </h5>
                <p class="mb-3" style="opacity:.85;">{{ $setting->tagline ?? 'Membentuk Pemimpin Masa Depan.' }}</p>
                @if ($setting->address)
                    <p class="mb-2"><i class="bi bi-geo-alt me-2" style="color:var(--lp-accent);"></i>{{ $setting->address }}</p>
                @endif
                @if ($setting->phone)
                    <p class="mb-2"><i class="bi bi-telephone me-2" style="color:var(--lp-accent);"></i>{{ $setting->phone }}</p>
                @endif
                @if ($setting->email)
                    <p class="mb-2"><i class="bi bi-envelope me-2" style="color:var(--lp-accent);"></i>{{ $setting->email }}</p>
                @endif
                @if ($setting->whatsapp)
                    <p class="mb-0"><i class="bi bi-whatsapp me-2" style="color:#22c55e;"></i>{{ $setting->whatsapp }}</p>
                @endif

                @if ($setting->facebook || $setting->instagram || $setting->youtube || $setting->tiktok)
                    <div class="lp-social">
                        @if ($setting->facebook)
                            <a href="{{ $setting->facebook }}" target="_blank" rel="noopener"><i class="bi bi-facebook"></i></a>
                        @endif
                        @if ($setting->instagram)
                            <a href="{{ $setting->instagram }}" target="_blank" rel="noopener"><i class="bi bi-instagram"></i></a>
                        @endif
                        @if ($setting->youtube)
                            <a href="{{ $setting->youtube }}" target="_blank" rel="noopener"><i class="bi bi-youtube"></i></a>
                        @endif
                        @if ($setting->tiktok)
                            <a href="{{ $setting->tiktok }}" target="_blank" rel="noopener"><i class="bi bi-tiktok"></i></a>
                        @endif
                    </div>
                @endif
            </div>

            <div class="col-lg-3 col-sm-4">
                <h6>Tautan Cepat</h6>
                <ul class="list-unstyled m-0">
                    <li class="mb-2"><a href="{{ route('halaman-publik.profil') }}">Tentang Kami</a></li>
                    <li class="mb-2"><a href="{{ route('halaman-publik.daftar-artikel') }}">Berita</a></li>
                    <li class="mb-2"><a href="{{ route('halaman-publik.pengumuman') }}">Pengumuman</a></li>
                    <li class="mb-2"><a href="{{ route('halaman-publik.galeri') }}">Galeri</a></li>
                    <li class="mb-2"><a href="{{ route('halaman-publik.ppdb') }}">PPDB</a></li>
                    <li class="mb-2"><a href="{{ route('halaman-publik.kontak') }}">Kontak</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-sm-8">
                <h6>Hubungi Kami</h6>
                <p class="mb-2" style="opacity:.85;">Punya pertanyaan? Tim kami siap membantu Anda.</p>
                <a href="{{ route('halaman-publik.kontak') }}" class="lp-link-soft" style="color:#fff;">
                    Kirim Pesan <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <div class="lp-footer-bottom d-flex flex-wrap justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} {{ $setting->school_name ?? '' }}. Hak cipta dilindungi.</span>
            @if ($adminUrl = tenant()?->adminUrl())
                <a href="{{ $adminUrl }}">Masuk Admin</a>
            @endif
        </div>
    </div>
</footer>

{{-- Drawer mobile: dirender terpisah di akhir DOM, hidden di desktop,
     tampil di mobile (≤991.98px). Tidak bergantung pada display:contents. --}}
<div class="lp-nav-menu" id="lpNavMenu" aria-hidden="true">
    <div class="lp-nav-menu-header">
        <a class="lp-brand" href="{{ route('halaman-publik.beranda') }}">
            @if ($setting->logo)
                <img src="{{ Storage::disk('public')->url('landing/' . $setting->logo) }}" alt="">
            @endif
            <span>{{ $setting->school_name ?? 'Sekolah' }}</span>
        </a>
        <button class="lp-nav-close" id="lpNavClose" type="button" aria-label="Tutup menu">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <div class="lp-nav-menu-body">
        @if (! $hasBeritaMenu)
            <div class="lp-nav-item">
                <a class="lp-nav-link {{ $beritaActive ? 'active' : '' }}" href="{{ route('halaman-publik.daftar-artikel') }}">
                    <span>Berita</span>
                </a>
            </div>
        @endif
        @foreach ($menus['header'] ?? [] as $item)
            @php
                $itemPath = $lpMenuPath($item->url);
                $childActive = $item->child_items->contains(function ($child) use ($lpMenuPath, $currentPath) {
                    $childPath = $lpMenuPath($child->url);
                    return $childPath !== null && $childPath === $currentPath;
                });
                $isActive = ($itemPath !== null && $itemPath === $currentPath) || $childActive;
            @endphp
            @if ($item->child_items->isNotEmpty())
                <div class="lp-nav-item dropdown" data-dropdown>
                    <a class="lp-nav-link {{ $isActive ? 'active' : '' }}" href="{{ $item->url }}" data-dropdown-toggle>
                        <span>{{ $item->title }}</span>
                        <i class="bi bi-chevron-down"></i>
                    </a>
                    <div class="lp-dropdown">
                        @foreach ($item->child_items as $child)
                            @php
                                $childPath = $lpMenuPath($child->url);
                                $isChildActive = $childPath !== null && $childPath === $currentPath;
                            @endphp
                            <a href="{{ $child->url }}" class="{{ $isChildActive ? 'active' : '' }}">
                                <i class="bi bi-arrow-right-short"></i>
                                {{ $child->title }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="lp-nav-item">
                    <a class="lp-nav-link {{ $isActive ? 'active' : '' }}" href="{{ $item->url }}">{{ $item->title }}</a>
                </div>
            @endif
        @endforeach
    </div>

    <div class="lp-nav-menu-footer">
        <a href="{{ tenant()?->adminUrl() ?: '#' }}" class="lp-cta-mobile" target="_blank" rel="noopener noreferrer">
            <i class="bi bi-pencil-square"></i>
            <span>SabIT</span>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function () {
    'use strict';

    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // 1) Scroll progress + navbar shrink
    var bar = document.getElementById('lpScrollProgress');
    var nav = document.getElementById('lpNavbar');
    var navbarWrap = document.querySelector('.lp-navbar-wrap');
    var ticking = false;

    function onScroll() {
        var doc = document.documentElement;
        var h = doc.scrollHeight - doc.clientHeight;
        var pct = h > 0 ? (window.scrollY / h) * 100 : 0;
        if (bar) bar.style.width = pct + '%';
        var scrolled = window.scrollY > 20;
        if (nav) nav.classList.toggle('is-scrolled', scrolled);
        if (navbarWrap) navbarWrap.classList.toggle('is-scrolled', scrolled);
        var inner = navbarWrap ? navbarWrap.querySelector('.lp-navbar-inner') : null;
        if (inner) inner.classList.toggle('is-scrolled', scrolled);
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(onScroll);
            ticking = true;
        }
    }, { passive: true });
    window.addEventListener('resize', onScroll);
    onScroll();

    // 2) Reveal on scroll (IntersectionObserver, single instance)
    var revealEls = document.querySelectorAll('.lp-reveal');
    if (!reduceMotion && 'IntersectionObserver' in window) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    io.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -50px 0px' });

        revealEls.forEach(function (el) { io.observe(el); });
    } else {
        revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    }

    // 3) Mobile drawer (slide dari kanan)
    var navToggle = document.getElementById('lpNavToggle');
    var navMenu = document.getElementById('lpNavMenu');
    var navBackdrop = document.getElementById('lpNavBackdrop');
    var navClose = document.getElementById('lpNavClose');

    // Simpan posisi scroll supaya tidak lompat ke atas saat body di-lock.
    var drawerScrollY = 0;

    function openDrawer() {
        if (!navMenu) return;
        drawerScrollY = window.scrollY || window.pageYOffset || 0;
        navMenu.classList.add('is-open');
        navMenu.setAttribute('aria-hidden', 'false');
        if (navBackdrop) navBackdrop.classList.add('is-open');
        if (navToggle) navToggle.setAttribute('aria-expanded', 'true');
        if (navbarWrap) {
            navbarWrap.classList.add('is-drawer-open');
            var inner = navbarWrap.querySelector('.lp-navbar-inner');
            if (inner) inner.classList.add('is-drawer-open');
        }
        document.documentElement.classList.add('lp-nav-open');
        document.body.classList.add('lp-nav-open');
    }
    function closeDrawer() {
        if (!navMenu) return;
        navMenu.classList.remove('is-open');
        navMenu.setAttribute('aria-hidden', 'true');
        if (navBackdrop) navBackdrop.classList.remove('is-open');
        if (navToggle) navToggle.setAttribute('aria-expanded', 'false');
        if (navbarWrap) {
            navbarWrap.classList.remove('is-drawer-open');
            var inner = navbarWrap.querySelector('.lp-navbar-inner');
            if (inner) inner.classList.remove('is-drawer-open');
        }
        document.documentElement.classList.remove('lp-nav-open');
        document.body.classList.remove('lp-nav-open');
        // Kembalikan posisi scroll setelah lock dilepas
        window.scrollTo(0, drawerScrollY);
    }
    function isMobileViewport() {
        return window.matchMedia && window.matchMedia('(max-width: 991.98px)').matches;
    }

    // Toggle drawer — HANYA satu sumber handler, tanpa inline onclick di markup.
    if (navToggle) {
        navToggle.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            if (navMenu && navMenu.classList.contains('is-open')) closeDrawer();
            else openDrawer();
        });
    }
    if (navClose) {
        navClose.addEventListener('click', function (e) {
            e.preventDefault();
            closeDrawer();
        });
    }
    if (navBackdrop) {
        navBackdrop.addEventListener('click', function (e) {
            e.preventDefault();
            closeDrawer();
        });
    }

    // Klik link di drawer: biarkan navigasi default terjadi (tidak ada listener
    // yang memblokir). Drawer akan ter-reset otomatis setelah navigasi oleh
    // pageshow listener di bawah.

    // Fix: CTA di drawer (mis. .lp-cta-mobile) kadang terpicu 2x di mobile
    // karena event bubble ke capture-phase listener lain. Hentikan propagasi
    // dan biarkan navigasi default terjadi normal.
    document.querySelectorAll('.lp-nav-menu .lp-cta-mobile').forEach(function (cta) {
        cta.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    });

    // Mobile dropdown (submenu accordion) di dalam drawer.
    // Desktop tetap pakai CSS :hover/:focus-within.
    document.querySelectorAll('[data-dropdown-toggle]').forEach(function (toggle) {
        toggle.addEventListener('click', function (e) {
            if (!isMobileViewport()) return;
            var item = toggle.closest('[data-dropdown]');
            if (!item) return;
            // Cegah navigasi ke parent (parent url) saat toggle submenu.
            e.preventDefault();
            var open = item.classList.toggle('is-open');
            toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    });

    // Klik di luar drawer (untuk menutup drawer saat klik area konten)
    // Pakai 'click' di capture phase agar fire sebelum handler lain.
    document.addEventListener('click', function (e) {
        if (!navMenu || !navMenu.classList.contains('is-open')) return;
        if (!isMobileViewport()) return;
        if (navMenu.contains(e.target)) return;
        if (navToggle && navToggle.contains(e.target)) return;
        if (navClose && navClose.contains(e.target)) return;
        if (navBackdrop && navBackdrop.contains(e.target)) return;
        closeDrawer();
    }, true);

    // ESC menutup drawer
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && navMenu && navMenu.classList.contains('is-open')) {
            closeDrawer();
        }
    });

    // Saat resize ke desktop, bersihkan state drawer
    window.addEventListener('resize', function () {
        if (window.innerWidth >= 992) {
            closeDrawer();
        }
    });

    // Reset state setelah navigasi (pageshow fires setelah load, termasuk bfcache)
    window.addEventListener('pageshow', function () {
        closeDrawer();
    });

    // 5) Smooth scroll for anchor links (tidak termasuk link dalam drawer)
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
        if (navMenu && navMenu.contains(link)) return;
        link.addEventListener('click', function (e) {
            var id = link.getAttribute('href');
            if (id.length < 2) return;
            var target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            var top = target.getBoundingClientRect().top + window.scrollY - 100;
            window.scrollTo({ top: top, behavior: reduceMotion ? 'auto' : 'smooth' });
        });
    });
})();
</script>

<button id="lpScrollTopBtn" type="button" aria-label="Kembali ke atas" title="Kembali ke atas"
    style="position:fixed;right:20px;bottom:20px;z-index:1080;width:48px;height:48px;border-radius:9999px;border:0;background:linear-gradient(135deg,var(--lp-primary),var(--lp-accent-2));color:#fff;display:none;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 10px 24px rgba(var(--lp-primary-rgb),.4);transition:transform .2s ease,opacity .2s ease,box-shadow .2s ease;">
    <i class="bi bi-arrow-up" style="font-size:1.25rem;line-height:1;"></i>
</button>

<script>
(function () {
    var btn = document.getElementById('lpScrollTopBtn');
    if (!btn) return;
    var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    function updateBtn() {
        if (window.scrollY > 300) {
            btn.style.display = 'flex';
            btn.style.opacity = '1';
        } else {
            btn.style.opacity = '0';
            setTimeout(function () {
                if (window.scrollY <= 300) btn.style.display = 'none';
            }, 200);
        }
    }

    window.addEventListener('scroll', updateBtn, { passive: true });
    updateBtn();

    btn.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });

    btn.addEventListener('mouseenter', function () {
        btn.style.transform = 'translateY(-3px)';
        btn.style.boxShadow = '0 14px 30px rgba(var(--lp-primary-rgb),.55)';
    });
    btn.addEventListener('mouseleave', function () {
        btn.style.transform = 'translateY(0)';
        btn.style.boxShadow = '0 10px 24px rgba(var(--lp-primary-rgb),.4)';
    });
})();
</script>
@yield('script')
</body>
</html>
