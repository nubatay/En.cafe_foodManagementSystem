@extends('layouts.customer')

@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    /* ── Design Tokens ── */
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
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Jost', sans-serif !important;
        background: var(--warm-white) !important;
        color: var(--ink) !important;
        min-height: 100vh;
    }

    /* ── Keyframes ── */
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
        50%       { opacity: 0.4; }
    }
    @keyframes tickIn {
        0%   { transform: scale(0) rotate(-15deg); opacity: 0; }
        70%  { transform: scale(1.15) rotate(4deg); opacity: 1; }
        100% { transform: scale(1) rotate(0deg); opacity: 1; }
    }

    /* ── Background Grid ── */
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

    /* ── Warm Blob ── */
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
        padding: 3rem 1.25rem 5rem;
    }

    /* ── Logo ── */
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
        margin-bottom: 2rem;
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

    /* ── Staff Instruction Card ── */
    .staff-card {
        width: 100%;
        max-width: 400px;
        background: var(--white);
        border: 1.5px dashed rgba(201,146,26,0.3);
        border-radius: 16px;
        padding: 1.75rem;
        text-align: center;
        margin-bottom: 1.5rem;
        animation: fadeUp 0.7s ease 0.1s both;
    }
    .staff-eyebrow {
        font-size: 0.62rem;
        font-weight: 600;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--ink-mid);
        opacity: 0.4;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .staff-eyebrow::before,
    .staff-eyebrow::after {
        content: '';
        width: 20px;
        height: 1px;
        background: currentColor;
        flex-shrink: 0;
    }
    .staff-icon-wrap {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: rgba(201,146,26,0.08);
        border: 1px solid rgba(201,146,26,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }
    .staff-icon-wrap i {
        font-size: 1.2rem;
        color: var(--gold);
    }
    .staff-title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--ink);
        margin-bottom: 1.1rem;
        line-height: 1.2;
    }
    .cmd-row {
        background: rgba(26,16,8,0.03);
        border: 1px solid var(--ink-faint);
        border-radius: 8px;
        padding: 0.6rem 0.9rem;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        text-align: left;
    }
    .cmd-row:last-of-type { margin-bottom: 1rem; }
    .cmd-mono {
        font-family: 'Courier New', monospace;
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--crimson);
        letter-spacing: 0.03em;
    }
    .cmd-mono .cmd-var { color: var(--gold); }
    .cmd-sub {
        font-size: 0.66rem;
        color: var(--ink-mid);
        opacity: 0.55;
        margin-top: 2px;
        line-height: 1.4;
    }
    .cmd-badge {
        flex-shrink: 0;
        padding: 2px 9px;
        border-radius: 999px;
        font-size: 0.58rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }
    .badge-assign { background: rgba(176,16,16,0.08); color: var(--crimson); }
    .badge-reset  { background: rgba(201,146,26,0.12); color: var(--gold); }
    .staff-footnote {
        font-size: 0.63rem;
        color: var(--ink-mid);
        opacity: 0.4;
        line-height: 1.7;
        letter-spacing: 0.03em;
    }
    .staff-footnote code {
        font-family: 'Courier New', monospace;
        font-size: 0.63rem;
        opacity: 0.85;
    }

    /* ── Animated Logo Watermark ── */
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
    .logo-animated-wrap:hover .logo-letter { color: var(--crimson); }
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

    {{-- ── Logo ── --}}
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

        {{-- ── Welcome Card ── --}}
        <div class="welcome-card">
            <div class="welcome-eyebrow">Welcome</div>
            <div class="welcome-title">Good to have you <em>here.</em></div>
            <p class="welcome-text">Browse our menu and place your order whenever you're ready. We'll bring everything right to your table.</p>
        </div>

    @else

        {{-- ── Staff Instructions Card ── --}}
        <div class="staff-card">
            <div class="staff-eyebrow">Staff Instructions</div>

            <div class="staff-icon-wrap">
                <i class="bi bi-link-45deg"></i>
            </div>

            <div class="staff-title">Table URL Commands</div>

            {{-- Assign a table --}}
            <div class="cmd-row">
                <div>
                    <div class="cmd-mono">table/<span class="cmd-var">{n}</span>/start</div>
                    <div class="cmd-sub">Replace <strong style="font-weight:600;">{n}</strong> with the table number</div>
                </div>
                <span class="cmd-badge badge-assign">Assign</span>
            </div>

            {{-- Reset the table --}}
            <div class="cmd-row">
                <div>
                    <div class="cmd-mono">table/reset</div>
                    <div class="cmd-sub">Clears the active table session</div>
                </div>
                <span class="cmd-badge badge-reset">Reset</span>
            </div>

            <div class="staff-footnote">
                Enter the path directly in the browser address bar<br>
                e.g. &nbsp;<code>encafe.com/table/5/start</code>
            </div>
        </div>

    @endif

    {{-- ── Browse Menu Button (always visible) ── --}}
    <div class="action-area">
        <a href="{{ route('menu.index') }}" class="btn-encafe btn-primary-ec">
            <i class="bi bi-cup-hot-fill"></i>
            Browse Menu
        </a>
    </div>

    {{-- ── Animated Logo Watermark ── --}}
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