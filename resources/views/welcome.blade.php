<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>En.cafe — Where Every Sip Tells a Story</title>

    <link rel="icon" type="image/png" href="{{ asset('images/logos.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --crimson: #B01010;
            --crimson-dark: #8a0c0c;
            --gold: #C9921A;
            --gold-light: #F8B803;
            --ink: #1a1008;
            --ink-mid: #5a4535;
            --ink-faint: #e8ddd5;
            --white: #ffffff;
        }

        html, body { height: 100%; }

        body {
            font-family: 'Jost', sans-serif;
            background: var(--white);
            color: var(--ink);
            overflow-x: hidden;
        }

        /* ── STEAM ── */
        @keyframes steam {
            0%   { transform: translateY(0) scaleX(1); opacity: 0.95; }
            50%  { transform: translateY(-20px) scaleX(1.2); opacity: 0.45; }
            100% { transform: translateY(-44px) scaleX(0.65); opacity: 0; }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(28px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* ── NAV ── */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 3rem;
            height: 68px;
            background: rgba(255,255,255,0.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--ink-faint);
        }

        .nav-logo-img {
            height: 42px;
            width: auto;
            display: block;
            object-fit: contain;
        }

        /* Shown only if image fails to load */
        .nav-logo-fallback {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--crimson);
            letter-spacing: -0.01em;
            display: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-btn {
            font-family: 'Jost', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1.4rem;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            transition: all 0.18s;
        }

        .nav-btn-outline {
            color: var(--crimson);
            background: transparent;
            border: 1.5px solid var(--crimson);
        }
        .nav-btn-outline:hover { background: var(--crimson); color: white; }

        .nav-btn-fill {
            background: var(--crimson);
            color: white;
            border: 1.5px solid var(--crimson);
        }
        .nav-btn-fill:hover { background: var(--crimson-dark); border-color: var(--crimson-dark); }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
            background: var(--white);
            padding-top: 68px;
        }

        /* subtle grid texture */
        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(201,146,26,0.035) 1px, transparent 1px),
                linear-gradient(90deg, rgba(201,146,26,0.035) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        /* decorative blob */
        .hero-blob {
            position: absolute;
            right: -100px;
            top: 50%;
            transform: translateY(-50%);
            width: 640px;
            height: 640px;
            border-radius: 50%;
            background: radial-gradient(circle at 38% 40%,
                rgba(248,184,3,0.11) 0%,
                rgba(176,16,16,0.055) 55%,
                transparent 78%);
            border: 1px solid rgba(201,146,26,0.1);
            pointer-events: none;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 3rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            width: 100%;
        }

        /* LEFT */
        .hero-left {
            animation: fadeUp 0.85s cubic-bezier(0.22,1,0.36,1) both;
        }

        .hero-title {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(2.8rem, 5vw, 4.6rem);
            font-weight: 700;
            line-height: 1.06;
            color: var(--ink);
            margin-bottom: 1.4rem;
            letter-spacing: -0.015em;
        }

        .hero-title em {
            font-style: italic;
            color: var(--crimson);
        }

        .hero-sub {
            font-size: 1rem;
            font-weight: 300;
            color: var(--ink-mid);
            line-height: 1.85;
            max-width: 400px;
        }

        /* RIGHT */
        .hero-right {
            display: flex;
            align-items: center;
            justify-content: center;
            animation: fadeIn 1.1s ease 0.25s both;
        }

        .logo-display {
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .logo-glow {
            position: absolute;
            inset: -50px;
            background: radial-gradient(ellipse at center,
                rgba(248,184,3,0.1) 0%,
                rgba(176,16,16,0.04) 55%,
                transparent 72%);
            border-radius: 50%;
            pointer-events: none;
        }

        .logo-wordmark {
            position: relative;
            display: flex;
            align-items: flex-end;
            gap: 0;
            line-height: 1;
        }

        .logo-text {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(5rem, 9vw, 8rem);
            font-weight: 700;
            color: var(--crimson);
            letter-spacing: -0.02em;
            user-select: none;
        }

        .dot-wrap {
            position: relative;
            display: inline-flex;
            align-items: flex-end;
        }

        .steam-group {
            position: absolute;
            bottom: calc(100% - 14px);
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 4px;
            align-items: flex-end;
            pointer-events: none;
        }

        .wisp {
            border-radius: 3px 3px 6px 6px;
            background: linear-gradient(to top,
                var(--gold-light) 0%,
                var(--gold) 40%,
                rgba(201,146,26,0.25) 78%,
                transparent 100%);
            transform-origin: bottom center;
        }

        .wisp:nth-child(1) { width: 5px; height: 36px; animation: steam 2.2s ease-in-out 0s infinite; }
        .wisp:nth-child(2) { width: 6px; height: 50px; animation: steam 2.2s ease-in-out 0.38s infinite; }
        .wisp:nth-child(3) { width: 5px; height: 32px; animation: steam 2.2s ease-in-out 0.76s infinite; }

        /* ── RESPONSIVE ── */
        @media (max-width: 860px) {
            nav { padding: 0 1.5rem; }

            .hero-inner {
                grid-template-columns: 1fr;
                text-align: center;
                padding: 3rem 1.5rem 4rem;
                gap: 3rem;
            }

            .hero-sub { margin: 0 auto; }
            .hero-blob { right: -260px; }
            .logo-text { font-size: clamp(3.5rem, 14vw, 6rem); }
        }

        @media (max-width: 480px) {
            .nav-btn { padding: 0.45rem 0.9rem; font-size: 0.72rem; }
        }
    </style>
</head>
<body>

<!-- ═══ NAV ═══ -->
<nav>
    {{-- Replace {{ asset('images/logo.png') }} with your actual logo path --}}
    <img
        src="{{ asset('images/logo.png') }}"
        alt="En.cafe Logo"
        class="nav-logo-img sidebar-logo"
        onerror="this.style.display='none';document.querySelector('.nav-logo-fallback').style.display='block';"
    >
    {{-- Fallback text if image doesn't load --}}
    <span class="nav-logo-fallback">En.cafe</span>

    <div class="nav-links">
        <a href="{{ route('login') }}" class="nav-btn nav-btn-outline">Log In</a>
        <a href="{{ route('register') }}" class="nav-btn nav-btn-fill">Register</a>
    </div>
</nav>

<!-- ═══ HERO ═══ -->
<section class="hero">
    <div class="hero-blob"></div>

    <div class="hero-inner">

        <!-- Left: headline + paragraph -->
        <div class="hero-left">
            <h1 class="hero-title">
                Where every sip<br>
                <em>tells a story.</em>
            </h1>
            <p class="hero-sub">
                From single-origin pour-overs to velvety espresso blends —
                En.cafe is your daily ritual, elevated. Crafted with passion,
                served with warmth.
            </p>
        </div>

        <!-- Right: animated En.cafe logo -->
        <div class="hero-right">
            <div class="logo-display">
                <div class="logo-glow"></div>
                <div class="logo-wordmark">
                    <span class="logo-text">En</span>
                    <span class="dot-wrap">
                        <div class="steam-group">
                            <div class="wisp"></div>
                            <div class="wisp"></div>
                            <div class="wisp"></div>
                        </div>
                        <span class="logo-text">.</span>
                    </span>
                    <span class="logo-text">cafe</span>
                </div>
            </div>
        </div>

    </div>
</section>

</body>
</html>