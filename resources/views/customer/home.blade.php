@extends('layouts.customer')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    /* ── Design Tokens (matches landing page) ── */
    :root {
        --crimson:      #B01010;
        --crimson-dark: #8a0c0c;
        --gold:         #C9921A;
        --gold-light:   #F8B803;
        --ink:          #1a1008;
        --ink-mid:      #5a4535;
        --ink-faint:    #e8ddd5;
        --warm-white:   #FDFCFB;
        --white:        #ffffff;
    }

    /* ── Reset & Base ── */
    *, *::before, *::after { box-sizing: border-box; }

    body {
        font-family: 'Jost', sans-serif !important;
        background: var(--warm-white) !important;
        color: var(--ink) !important;
        min-height: 100vh;
    }

    /* ── Animations ── */
    @keyframes steam {
        0%   { transform: translateY(0) scaleX(1); opacity: 0.95; }
        50%  { transform: translateY(-16px) scaleX(1.2); opacity: 0.45; }
        100% { transform: translateY(-34px) scaleX(0.65); opacity: 0; }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(24px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeIn {
        from { opacity: 0; }
        to   { opacity: 1; }
    }
    @keyframes pulseRing {
        0%, 100% { transform: scale(1); opacity: 1; }
        50%       { transform: scale(1.08); opacity: 0.7; }
    }
    @keyframes shimmerGold {
        0%, 100% { opacity: 1; }
        50%       { opacity: 0.55; }
    }

    /* ── Background grid texture (same as landing) ── */
    .page-bg {
        position: fixed;
        inset: 0;
        background-image:
            linear-gradient(rgba(201,146,26,0.035) 1px, transparent 1px),
            linear-gradient(90deg, rgba(201,146,26,0.035) 1px, transparent 1px);
        background-size: 60px 60px;
        pointer-events: none;
        z-index: 0;
    }

    /* Warm radial blob (bottom-right) */
    .page-blob {
        position: fixed;
        bottom: -120px;
        right: -120px;
        width: 480px;
        height: 480px;
        border-radius: 50%;
        background: radial-gradient(circle at 38% 40%,
            rgba(248,184,3,0.09) 0%,
            rgba(176,16,16,0.05) 55%,
            transparent 78%);
        pointer-events: none;
        z-index: 0;
    }

    /* ── Page Shell ── */
    .page-shell {
        position: relative;
        z-index: 1;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding-top: 3rem;
    }

    /* ── Logo image (top) ── */
    .logo-img-wrap {
        margin-bottom: 2rem;
        animation: fadeIn 0.7s ease both;
    }

    .logo-img-wrap img {
        height: 56px;
        width: auto;
        object-fit: contain;
        display: block;
        margin: 0 auto;
    }

    /* ── Table Badge ── */
    .table-section {
        text-align: center;
        margin-bottom: 2rem;
        animation: fadeUp 0.7s ease 0.1s both;
    }

    .table-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--white);
        border: 1px solid var(--ink-faint);
        border-radius: 999px;
        padding: 4px 16px 4px 12px;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--ink-mid);
        margin-bottom: 0.6rem;
    }

    .table-chip-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--gold);
        animation: shimmerGold 2s ease-in-out infinite;
        flex-shrink: 0;
    }

    .table-number {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: clamp(5.5rem, 18vw, 8rem);
        font-weight: 700;
        color: var(--crimson);
        line-height: 1;
        letter-spacing: -0.04em;
        display: block;
    }

    .table-label {
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: var(--ink-mid);
        opacity: 0.7;
        margin-top: 0.25rem;
    }

    /* ── Welcome Card ── */
    .welcome-card {
        width: 100%;
        max-width: 400px;
        background: var(--white);
        border: 1px solid var(--ink-faint);
        border-radius: 16px;
        padding: 2rem 1.75rem;
        text-align: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 20px rgba(26,16,8,0.05);
        animation: fadeUp 0.7s ease 0.15s both;
    }

    .welcome-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 0.65rem;
    }
    .welcome-eyebrow::before,
    .welcome-eyebrow::after {
        content: '';
        width: 16px;
        height: 1.5px;
        background: var(--gold);
        flex-shrink: 0;
    }

    .welcome-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 0.5rem;
        line-height: 1.15;
    }

    .welcome-title em {
        font-style: italic;
        color: var(--crimson);
    }

    .welcome-text {
        font-size: 0.85rem;
        font-weight: 300;
        color: var(--ink-mid);
        line-height: 1.75;
    }

    /* ── Action Buttons ── */
    .action-area {
        width: 100%;
        max-width: 400px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        animation: fadeUp 0.7s ease 0.22s both;
    }

    .btn-encafe {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 0.9rem 1.5rem;
        border-radius: 8px;
        font-family: 'Jost', sans-serif;
        font-size: 0.82rem;
        font-weight: 600;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: all 0.18s ease;
    }

    .btn-primary-ec {
        background: var(--crimson);
        color: var(--white);
        border: 1.5px solid var(--crimson);
    }
    .btn-primary-ec:hover {
        background: var(--crimson-dark);
        border-color: var(--crimson-dark);
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(176,16,16,0.25);
        color: var(--white);
        text-decoration: none;
    }

    .btn-outline-ec {
        background: var(--white);
        color: var(--ink);
        border: 1.5px solid var(--ink-faint);
    }
    .btn-outline-ec:hover {
        border-color: var(--crimson);
        color: var(--crimson);
        transform: translateY(-1px);
        text-decoration: none;
    }

    /* ── Animated logo (bottom watermark) ── */
    .logo-animated-wrap {
        margin-top: 0.25rem;
        text-align: center;
        animation: fadeIn 1s ease 0.4s both;
    }

    .logo-wordmark {
        display: inline-flex;
        align-items: flex-end;
        line-height: 1;
        gap: 0;
    }

    .logo-letter {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 2.6rem;
        font-weight: 700;
        color: rgba(176,16,16,0.18);
        letter-spacing: -0.02em;
        user-select: none;
        transition: color 0.3s;
    }

    .logo-animated-wrap:hover .logo-letter {
        color: var(--crimson);
    }

    .dot-wrap {
        position: relative;
        display: inline-flex;
        align-items: flex-end;
    }

    .steam-group {
        position: absolute;
        bottom: calc(100% - 8px);
        left: 50%;
        transform: translateX(-50%);
        display: flex;
        gap: 3px;
        align-items: flex-end;
        pointer-events: none;
    }

    .wisp {
        border-radius: 2px 2px 4px 4px;
        background: linear-gradient(to top,
            var(--gold-light) 0%,
            var(--gold) 40%,
            rgba(201,146,26,0.2) 78%,
            transparent 100%);
        transform-origin: bottom center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .logo-animated-wrap:hover .wisp { opacity: 1; }

    .wisp:nth-child(1) { width: 3px; height: 18px; animation: steam 2.2s ease-in-out 0s infinite; }
    .wisp:nth-child(2) { width: 4px; height: 25px; animation: steam 2.2s ease-in-out 0.38s infinite; }
    .wisp:nth-child(3) { width: 3px; height: 15px; animation: steam 2.2s ease-in-out 0.76s infinite; }

    /* ── Check-in Notice ── */
    .checkin-card {
        width: 100%;
        max-width: 400px;
        background: var(--white);
        border: 1.5px dashed var(--ink-faint);
        border-radius: 16px;
        padding: 2rem 1.75rem;
        text-align: center;
        animation: fadeUp 0.7s ease 0.1s both;
    }

    .checkin-icon-wrap {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: rgba(176,16,16,0.07);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        animation: pulseRing 2.2s ease-in-out infinite;
        border: 1px solid rgba(176,16,16,0.1);
    }

    .checkin-icon-wrap i {
        font-size: 1.4rem;
        color: var(--crimson);
    }

    .checkin-eyebrow {
        font-size: 0.68rem;
        font-weight: 600;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--gold);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }
    .checkin-eyebrow::before,
    .checkin-eyebrow::after {
        content: '';
        width: 14px;
        height: 1.5px;
        background: var(--gold);
        flex-shrink: 0;
    }

    .checkin-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 0.5rem;
    }

    .checkin-body {
        font-size: 0.83rem;
        font-weight: 300;
        color: var(--ink-mid);
        line-height: 1.7;
    }

    /* ── Footer Brand ── */
    .footer-brand {
        position: fixed;
        bottom: 1.5rem;
        left: 0; right: 0;
        text-align: center;
        font-size: 0.65rem;
        font-weight: 600;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgba(90,69,53,0.25);
        z-index: 1;
        pointer-events: none;
    }
</style>

<!-- Background layers -->
<div class="page-bg"></div>
<div class="page-blob"></div>

<div class="page-shell">

    {{-- ── Logo Image ── --}}
    <div class="logo-img-wrap">
        <img src="{{ asset('images/logo.png') }}" alt="En.cafe Logo">
    </div>

    @if(session('table_number'))

        {{-- ── Table Number ── --}}
        <div class="table-section">
            <div class="table-chip">
                <span class="table-chip-dot"></span>
                Table
            </div>
            <span class="table-number">{{ session('table_number') }}</span>
            <div class="table-label">You're all set</div>
        </div>
        

        {{-- ── CTA Buttons ── --}}
        <div class="action-area">
            <a href="{{ route('menu.index') }}" class="btn-encafe btn-primary-ec">
                <i class="bi bi-cup-hot-fill"></i>
                Browse Menu
            </a>
        </div>

    @endif

    {{-- ── Animated logo watermark ── --}}
    <div class="logo-animated-wrap" title="En.cafe">
        <div class="logo-wordmark">
            <span class="logo-letter">En</span>
            <span class="dot-wrap">
                <div class="steam-group">
                    <div class="wisp"></div>
                    <div class="wisp"></div>
                    <div class="wisp"></div>
                </div>
                <span class="logo-letter">.</span>
            </span>
            <span class="logo-letter">cafe</span>
        </div>
    </div>

</div>

<div class="footer-brand">En.cafe &mdash; Crafted with passion</div>

@endsection