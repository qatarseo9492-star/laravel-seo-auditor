@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
    /* ===== GENERAL DARK THEME ===== */
    body {
        background-color: #0d0d0d;
        color: #e0e0e0;
        font-family: 'Poppins', sans-serif;
        overflow-x: hidden;
    }

    /* ===== NAVBAR ===== */
    .navbar {
        background: rgba(20, 20, 20, 0.6);
        backdrop-filter: blur(12px);
        transition: background 0.3s, box-shadow 0.3s;
    }
    .navbar.scrolled {
        background: #111 !important;
        box-shadow: 0px 3px 12px rgba(0, 0, 0, 0.6);
    }
    .navbar-brand {
        font-weight: 700;
        font-size: 1.4rem;
        color: #bb86fc !important;
        letter-spacing: 1px;
    }
    .nav-link {
        color: #e0e0e0 !important;
        margin: 0 10px;
        position: relative;
        font-weight: 500;
        transition: color 0.3s;
    }
    .nav-link::after {
        content: '';
        position: absolute;
        bottom: -5px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 2px;
        background: #bb86fc;
        transition: width 0.3s ease;
    }
    .nav-link:hover::after {
        width: 100%;
    }
    .nav-link:hover {
        color: #bb86fc !important;
    }

    /* ===== HERO SECTION ===== */
    .hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        background: radial-gradient(circle at 20% 20%, rgba(98,0,234,0.2), transparent 60%),
                    radial-gradient(circle at 80% 10%, rgba(187,134,252,0.15), transparent 50%),
                    linear-gradient(135deg, #0f0f0f, #1b1b1b);
        position: relative;
        overflow: hidden;
    }
    .hero h1 {
        font-size: 3.5rem;
        font-weight: 700;
        background: linear-gradient(45deg, #bb86fc, #6200ea);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
        animation: fadeDown 1.2s ease;
    }
    .hero p {
        font-size: 1.2rem;
        color: #aaa;
        max-width: 600px;
        animation: fadeUp 1.5s ease;
    }
    .btn-custom {
        margin-top: 35px;
        padding: 14px 36px;
        font-size: 1rem;
        font-weight: 600;
        border-radius: 30px;
        background: linear-gradient(90deg, #6200ea, #bb86fc);
        color: #fff;
        transition: all 0.35s ease;
        text-decoration: none;
        box-shadow: 0px 6px 15px rgba(98,0,234,0.5);
    }
    .btn-custom:hover {
        transform: translateY(-4px) scale(1.05);
        box-shadow: 0px 8px 20px rgba(98,0,234,0.7);
    }

    @keyframes fadeDown {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ===== FEATURES SECTION ===== */
    .features {
        background-color: #121212;
        padding: 100px 0;
    }
    .feature-box {
        background: rgba(40, 40, 40, 0.75);
        border-radius: 20px;
        padding: 40px 25px;
        transition: all 0.4s ease;
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .feature-box:hover {
        transform: translateY(-12px);
        background: rgba(50, 50, 50, 0.85);
        box-shadow: 0px 8px 20px rgba(0,0,0,0.6);
    }
    .feature-icon {
        font-size: 3rem;
        color: #bb86fc;
        margin-bottom: 20px;
    }
    .feature-box h4 {
        color: #fff;
        font-weight: 600;
        margin-bottom: 15px;
    }
    .feature-box p {
        color: #aaa;
        font-size: 0.95rem;
    }
    .btn-outline-light {
        border-radius: 20px;
        padding: 6px 18px;
    }

    /* ===== FOOTER ===== */
    footer {
        background: #0b0b0b;
        padding: 25px 0;
        text-align: center;
        font-size: 0.9rem;
        color: #777;
    }
    footer a {
        color: #bb86fc;
        text-decoration: none;
    }
</style>


{{-- ===== HERO SECTION ===== --}}
<div class="hero d-flex flex-column justify-content-center align-items-center text-center">
    <h1>Semantic SEO Checker 🚀</h1>
    <p>Advanced Tools to Audit, Analyze & Optimize your website for peak search performance.</p>
    <a href="#tools" class="btn-custom">Explore Tools</a>
</div>


{{-- ===== FEATURES SECTION ===== --}}
<div id="tools" class="features container text-center">
    <div class="row g-4">
        <!-- SEO Audit -->
        <div class="col-md-4">
            <div class="feature-box shadow-lg h-100">
                <div class="feature-icon">📝</div>
                <h4>SEO Audit</h4>
                <p>Comprehensive technical & on‑page SEO audits to uncover critical issues.</p>
                <a href="{{ route('seo.audit.index') }}" class="btn btn-outline-light btn-sm mt-3">Try Now</a>
            </div>
        </div>

        <!-- Keyword Analyzer -->
        <div class="col-md-4">
            <div class="feature-box shadow-lg h-100">
                <div class="feature-icon">🔑</div>
                <h4>Keyword Analysis</h4>
                <p>Analyze keyword density & discover optimization opportunities.</p>
                <a href="{{ route('seo.keyword.index') }}" class="btn btn-outline-light btn-sm mt-3">Analyze</a>
            </div>
        </div>

        <!-- Content Optimizer -->
        <div class="col-md-4">
            <div class="feature-box shadow-lg h-100">
                <div class="feature-icon">⚡</div>
                <h4>Content Optimizer</h4>
                <p>Get AI‑driven recommendations to enhance your content and dominate rankings.</p>
                <a href="{{ route('seo.optimizer.index') }}" class="btn btn-outline-light btn-sm mt-3">Optimize</a>
            </div>
        </div>
    </div>
</div>


{{-- ===== FOOTER ===== --}}
<footer>
    <p>© {{ date('Y') }} Semantic SEO Checker. All rights reserved. | <a href="#">Privacy Policy</a></p>
</footer>


{{-- ===== JS FOR NAVBAR SCROLL EFFECT ===== --}}
<script>
    window.addEventListener('scroll', function () {
        const navbar = document.querySelector('.navbar');
        navbar.classList.toggle('scrolled', window.scrollY > 30);
    });
</script>
@endsection
