@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Advanced Home')

@section('content')
<style>
/* ===== Global Styles ===== */
body {
    background-color: #0b0b0f;
    color: #e5e5e5;
    font-family: 'Poppins', sans-serif;
}

/* ===== NAVBAR ===== */
.navbar {
    background: rgba(20,20,25,0.55);
    backdrop-filter: blur(12px);
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(255,255,255,0.08);  /* outline */
}
.navbar.scrolled {
    background: rgba(15,15,20,0.95);
    box-shadow: 0 3px 12px rgba(0,0,0,0.7);
}
.navbar-brand {
    font-weight: 700;
    font-size: 1.5rem;
    background: linear-gradient(45deg, #bb86fc, #6200ea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.nav-link {
    color: #ddd !important;
    font-weight: 500;
    margin: 0 10px;
    position: relative;
    transition: all 0.3s;
}
.nav-link:hover {
    color: #bb86fc !important;
}
.nav-link::after {
    content: '';
    position: absolute;
    bottom: -5px;
    left: 50%;
    transform: translateX(-50%);
    background: #bb86fc;
    width: 0;
    height: 2px;
    transition: width 0.3s ease;
}
.nav-link:hover::after {
    width: 100%;
}

/* ===== HERO ===== */
.hero {
    min-height: 100vh;
    background: radial-gradient(circle at 20% 20%, rgba(98,0,234,0.15), transparent 60%),
                radial-gradient(circle at 80% 0%, rgba(187,134,252,0.15), transparent 70%),
                linear-gradient(135deg,#0d0d11,#141419);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    padding: 0 20px;
}
.hero h1 {
    font-size: 3.5rem;
    font-weight: 700;
    line-height: 1.2;
    background: linear-gradient(45deg, #bb86fc, #6200ea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: fadeDown 1s ease;
}
.hero p {
    font-size: 1.2rem;
    color: #aaa;
    margin-top: 20px;
    max-width: 650px;
    animation: fadeUp 1.5s ease;
}
.btn-primary-custom {
    margin-top: 35px;
    padding: 14px 36px;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 40px;
    background: linear-gradient(90deg, #6200ea, #bb86fc);
    color: #fff;
    border: none;
    transition: all .35s ease;
    text-decoration: none;
    box-shadow: 0 6px 15px rgba(98, 0, 234, 0.45);
}
.btn-primary-custom:hover {
    transform: translateY(-5px) scale(1.05);
    box-shadow: 0 10px 22px rgba(98,0,234,0.65);
}

/* Animations */
@keyframes fadeDown { from {opacity:0;transform:translateY(-50px);} to {opacity:1;transform:translateY(0);} }
@keyframes fadeUp { from {opacity:0;transform:translateY(50px);} to {opacity:1;transform:translateY(0);} }

/* ===== FEATURES Section ===== */
.features {
    background:#12121a;
    padding:100px 0;
}
.features h2 {
    text-align:center;
    margin-bottom:60px;
    font-weight:600;
    font-size:2rem;
    color:#fff;
}

/* Refined Feature Boxes */
.feature-box {
    background: linear-gradient(145deg, rgba(40,40,50,0.9), rgba(25,25,35,0.9));
    border-radius: 20px;
    padding: 40px 25px;
    transition: all 0.4s ease;
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,0.08);
    position: relative;
    overflow: hidden;
}
.feature-box::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(187,134,252,0.15), transparent 60%);
    transform: rotate(25deg);
    opacity: 0;
    transition: opacity 0.5s;
}
.feature-box:hover::before {
    opacity: 1;
}
.feature-box:hover {
    transform: translateY(-12px) scale(1.03);
    border: 1px solid rgba(187,134,252,0.35);
    box-shadow: 0 12px 30px rgba(98,0,234,0.4);
}
.feature-icon {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #bb86fc;
    transition: transform 0.4s ease;
}
.feature-box:hover .feature-icon {
    transform: scale(1.15) rotate(8deg);
    color: #d6a8ff;
}
.feature-title {
    color:#fff;
    font-weight:600;
    margin-bottom:15px;
}
.feature-text {
    color:#bbb;
    font-size:0.95rem;
}

/* ===== CTA ===== */
.cta-section {
    padding: 100px 20px;
    background: linear-gradient(90deg,#6200ea,#bb86fc);
    text-align: center;
    color: #fff;
    border-radius: 20px;
    margin: 80px auto;
    max-width: 1000px;
    box-shadow: 0px 10px 25px rgba(98,0,234,.5);
}
.cta-section h3 { font-weight: 700; font-size: 2rem; }
.cta-section p { margin: 15px 0 25px; font-size: 1.1rem; }
.cta-section .btn-light {
    border-radius: 30px;
    padding: 10px 28px;
}

/* ===== FOOTER ===== */
footer {
    background:#0a0a0d;
    padding:25px 0;
    text-align:center;
    font-size:.9rem;
    color:#888;
}
footer a {color:#bb86fc; text-decoration:none;}
</style>

{{-- Hero Section --}}
<div class="hero">
    <h1>🚀 Semantic SEO Checker</h1>
    <p>Advanced AI‑powered tools to Audit, Analyze, and Optimize your website for peak visibility and search rankings.</p>
    <a href="#tools" class="btn-primary-custom">Explore Tools</a>
</div>

{{-- Features --}}
<div id="tools" class="features container">
    <h2>⚡ Our SEO Tools</h2>
    <div class="row g-4">
        <!-- SEO Audit -->
        <div class="col-md-4">
            <div class="feature-box h-100 text-center p-4">
                <div class="feature-icon">📝</div>
                <h4 class="feature-title">SEO Audit</h4>
                <p class="feature-text">AI‑driven audits to detect technical SEO issues, broken links, and performance gaps.</p>
                <a href="{{ route('seo.audit.index') }}" class="btn btn-outline-light btn-sm mt-3">Run Audit</a>
            </div>
        </div>
        <!-- Keyword Analyzer -->
        <div class="col-md-4">
            <div class="feature-box h-100 text-center p-4">
                <div class="feature-icon">🔑</div>
                <h4 class="feature-title">Keyword Analyzer</h4>
                <p class="feature-text">Discover opportunities and analyze keyword density, relevance, and semantic variations.</p>
                <a href="{{ route('seo.keyword.index') }}" class="btn btn-outline-light btn-sm mt-3">Analyze</a>
            </div>
        </div>
        <!-- Optimizer -->
        <div class="col-md-4">
            <div class="feature-box h-100 text-center p-4">
                <div class="feature-icon">⚡</div>
                <h4 class="feature-title">Content Optimizer</h4>
                <p class="feature-text">Get semantic optimization tips for headings, metadata, and content flow for better rankings.</p>
                <a href="{{ route('seo.optimizer.index') }}" class="btn btn-outline-light btn-sm mt-3">Optimize</a>
            </div>
        </div>
    </div>
</div>

{{-- Call To Action --}}
<div class="cta-section">
    <h3>Ready to Supercharge Your SEO?</h3>
    <p>Join the future of semantic search optimization and get higher rankings today.</p>
    <a href="#tools" class="btn btn-light">Start Now</a>
</div>

{{-- Footer --}}
<footer>
    <p>© {{ date('Y') }} Semantic SEO Checker · All Rights Reserved · <a href="#">Privacy Policy</a></p>
</footer>

{{-- JS Navbar scroll --}}
<script>
window.addEventListener('scroll', () => {
  document.querySelector('.navbar').classList.toggle('scrolled', window.scrollY > 20);
});
</script>
@endsection
