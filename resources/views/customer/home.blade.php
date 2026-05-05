@extends('layouts.customer')

@section('content')
<style>
    :root {
        --primary: #DC2626;
        --accent: #FACC15;
        --dark: #7F1D1D;
        --bg-warm: #FFF7ED;
        --text-dark: #1F2937;
        --muted: #6B7280;
        --border-light: rgba(127, 29, 29, 0.12);
    }

    body {
        background-color: var(--bg-warm) !important;
        color: var(--text-dark);
        font-family: 'Inter', system-ui, sans-serif;
    }

    /* ── Logo Background Watermark ── */
    .logo-bg {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -52%);
        width: min(90vw, 520px);
        height: min(90vw, 520px);
        object-fit: contain;
        opacity: 0.07;
        pointer-events: none;
        z-index: 0;
    }

    .page-content {
        position: relative;
        z-index: 1;
        padding-top: 3.5rem;
    }

    /* ── Brand ── */
    .brand-section {
        text-align: center;
        margin-bottom: 2rem;
    }

    .brand-name {
        display: block;
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--dark);
        letter-spacing: -0.5px;
    }

    /* ── Table ── */
    .table-focus {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .table-chip {
        display: inline-block;
        background: rgba(255, 255, 255, 0.85);
        border: 1px solid var(--border-light);
        border-radius: 999px;
        padding: 5px 18px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 2.5px;
        text-transform: uppercase;
        color: var(--dark);
        margin-bottom: 0.25rem;
    }

    .table-number-huge {
        display: block;
        font-size: 6.5rem;
        font-weight: 900;
        color: var(--dark);
        line-height: 1;
        letter-spacing: -5px;
    }

    /* ── Welcome Card ── */
    .welcome-card {
        max-width: 420px;
        margin: 1.5rem auto 2rem;
        padding: 2rem 1.5rem;
        text-align: center;
        background: rgba(255,255,255,0.9);
        border: 1px solid var(--border-light);
        border-radius: 20px;
    }

    .welcome-title {
        font-size: 1.4rem;
        font-weight: 800;
        color: var(--dark);
        margin-bottom: 0.75rem;
        letter-spacing: 1px;
    }

    .welcome-text {
        font-size: 0.9rem;
        color: var(--muted);
        line-height: 1.6;
    }

    /* ── Buttons ── */
    .action-area {
        max-width: 380px;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .btn-custom-xl {
        padding: 1.1rem 1.5rem;
        border-radius: 16px;
        font-weight: 700;
        font-size: 1rem;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        border: none;
        text-decoration: none;
        cursor: pointer;
    }

    .btn-main {
        background-color: var(--primary);
        color: white;
    }

    .btn-main:hover {
        background-color: var(--dark);
        color: white;
        transform: translateY(-1px);
    }

    .btn-secondary-outline {
        background-color: rgba(255, 255, 255, 0.85);
        color: var(--text-dark);
        border: 1px solid var(--border-light);
    }

    .btn-secondary-outline:hover {
        border-color: var(--dark);
        color: var(--dark);
    }

    /* ── Check-in ── */
    .checkin-notice {
        text-align: center;
        max-width: 360px;
        margin: 0 auto;
        padding: 2.5rem 2rem;
        border-radius: 24px;
        background: rgba(250, 204, 21, 0.07);
        border: 1.5px dashed var(--accent);
    }

    .pulse-wrap {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: rgba(220, 38, 38, 0.08);
        margin: 0 auto 1.25rem;
        animation: pulse-anim 2s infinite;
    }

    .pulse-wrap i {
        font-size: 1.5rem;
        color: var(--primary);
    }

    @keyframes pulse-anim {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.75; }
    }

    .checkin-title {
        font-size: 1.05rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
    }

    .checkin-body {
        font-size: 0.875rem;
        color: var(--muted);
    }

    /* ── Footer ── */
    .footer-brand {
        position: fixed;
        bottom: 1.75rem;
        left: 0;
        right: 0;
        text-align: center;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: rgba(127, 29, 29, 0.3);
        z-index: 1;
    }
</style>

<img src="{{ asset('images/logo.png') }}" class="logo-bg" alt="">

<div class="container page-content">

    <div class="brand-section">
        <span class="brand-name">En.cafe</span>
    </div>

    @if(session('table_number'))

        {{-- Table Number --}}
        <div class="table-focus">
            <span class="table-chip">Now Serving Table</span>
            <span class="table-number-huge">{{ session('table_number') }}</span>
        </div>

       

        {{-- Buttons --}}
        <div class="action-area">
            <a href="{{ route('menu.index') }}" class="btn-custom-xl btn-main">
                <i class="bi bi-cup-hot-fill"></i>
                Browse Menu
            </a>

        </div>

    @else

        {{-- Check-in --}}
        <div class="checkin-notice">
            <div class="pulse-wrap">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <p class="checkin-title">Check-in required</p>
            <p class="checkin-body">
                Please let staff know you're here to activate your table.
            </p>
        </div>

    @endif

  

</div>
@endsection