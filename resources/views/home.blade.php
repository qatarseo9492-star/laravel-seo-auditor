@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
    /* === Dark Theme Styles === */
    body {
        background-color: #121212;
        color: #e0e0e0;
        font-family: 'Poppins', sans-serif;
    }

    .hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        text-align: center;
        background: linear-gradient(135deg, #1f1f1f 0%, #0d0d0d 100%);
        overflow: hidden;
        position: relative;
    }

    .hero h1 {
        font-size: 3rem;
        font-weight: 700;
        color: #ffffff;
        animation: fadeInDown 1s ease;
    }

    .hero p {
        font-size: 1.2rem;
        margin-top: 15px;
        color: #bbbbbb;
        animation: fadeInUp 1.2s ease;
    }

    .btn-custom {
        margin-top: 30px;
        padding: 12px 30px;
        background-color: #6200ea;
        color: #fff;
        border-radius: 30px;
        font-weight: bold;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-custom:hover {
        background-color: #3700b3;
        transform: scale(1.08);
    }

    /* === Floating Circles Background Animations === */
    .circle {
        position: absolute;
        border-radius: 50%;
        background: rgba(98, 0, 234, 0.2);
        animation: move 20s linear infinite;
        z-index: 0;
    }

    .circle:nth-child(1) {
        width: 200px;
        height: 200px;
        left: 10%;
        top: 15%;
    }

    .circle:nth-child(2) {
        width: 150px;
        height: 150px;
        right: 20%;
        bottom: 20%;
        background: rgba(255, 255, 255, 0.15);
    }

    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(50px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes move {
        0% { transform: translateY(0) translateX(0); }
        50% { transform: translateY(-50px) translateX(20px); }
        100% { transform: translateY(0) translateX(0); }
    }

    /* === Features Section === */
    .features {
        background-color: #1e1e1e;
        padding: 80px 0;
        z-index: 2;
        position: relative;
    }

    .feature-box {
        background: #2a2a2a;
        border-radius: 15px;
        padding: 30px;
        transition: transform 0.3s, background 0.3s;
        height: 100%;
    }

    .feature-box:hover {
        transform: translateY(-10px);
        background: #333;
    }

    .feature-icon {
        font-size: 2.5rem;
        color: #bb86fc;
        margin-bottom: 15px;
    }
</style>

{{-- === Hero Section === --}}
<div class="hero">
    <div class="circle"></div>
    <div class="circle"></div>

    <h1>Welcome to Semantic SEO Checker 🚀</h1>
    <p>Modern SEO Tools to Analyze, Optimize & Dominate Search Engines</p>
    <a href="#tools" class="btn-custom">Explore Tools</a>
</div>

{{-- === Features Section === --}}
<div id="tools" class="features container text-center">
    <div class="row">
        <!-- SEO Audit -->
        <div class="col-md-4 mb-4">
            <div class="feature-box shadow-lg">
                <div class="feature-icon">📝</div>
                <h4>SEO Audit</h4>
                <p>Check your site’s technical SEO health and uncover critical issues.</p>
                <a href="{{ route('seo.audit.index') }}" class="btn btn-outline-light btn-sm mt-2">Try Now</a>
            </div>
        </div>

        <!-- Keyword Analyzer -->
        <div class="col-md-4 mb-4">
            <div class="feature-box shadow-lg">
                <div class="feature-icon">🔑</div>
                <h4>Keyword Analysis</h4>
                <p>Find the best keywords and measure density with semantic analysis.</p>
                <a href="{{ route('seo.keyword.index') }}" class="btn btn-outline-light btn-sm mt-2">Analyze</a>
            </div>
        </div>

        <!-- Content Optimizer -->
        <div class="col-md-4 mb-4">
            <div class="feature-box shadow-lg">
                <div class="feature-icon">⚡</div>
                <h4>Content Optimizer</h4>
                <p>Optimize your content with recommendations for better rankings.</p>
                <a href="{{ route('seo.optimizer.index') }}" class="btn btn-outline-light btn-sm mt-2">Optimize</a>
            </div>
        </div>
    </div>
</div>
@endsection
