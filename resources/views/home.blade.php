@extends('layouts.app')

@section('title', 'Semantic SEO Checker')

@section('content')
<style>
/* ===== GLOBAL ===== */
body {
    background: #0b0b0f;
    color: #e5e5e5;
    font-family: 'Inter', 'Poppins', sans-serif;
    overflow-x: hidden;
}

/* ===== NAVBAR ===== */
.navbar {
    background: rgba(10, 10, 15, 0.6);
    backdrop-filter: blur(14px);
    border-bottom: 2px solid transparent;
    border-image: linear-gradient(90deg, #6200ea, #bb86fc, #03dac6) 1;
    transition: all 0.3s ease;
}
.navbar.scrolled {
    background: rgba(15, 15, 25, 0.95);
}
.navbar-brand {
    font-size: 1.5rem;
    font-weight: 700;
    background: linear-gradient(45deg, #bb86fc, #03dac6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.nav-link {
    color: #ccc !important;
    font-weight: 500;
    margin: 0 12px;
    position: relative;
    transition: all 0.3s;
}
.nav-link:hover {
    color: #bb86fc !important;
}
.nav-link::after {
    content: '';
    position: absolute;
    bottom: -6px;
    left: 50%;
    width: 0%;
    height: 2px;
    background: linear-gradient(90deg, #6200ea, #bb86fc);
    transition: width 0.3s ease, left 0.3s ease;
}
.nav-link:hover::after {
    width: 100%; left: 0;
}

/* ===== HERO ===== */
.hero {
    min-height: 90vh;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background:
        radial-gradient(circle at 30% 20%, rgba(98,0,234,0.2), transparent 60%),
        radial-gradient(circle at 80% 0%, rgba(3,218,198,0.2), transparent 70%),
        linear-gradient(135deg, #0d0d11, #141419);
    text-align: center;
    padding: 0 20px;
}
.hero h1 {
    font-size: 3.8rem;
    line-height: 1.2;
    font-weight: 800;
    letter-spacing: -1px;
    background: linear-gradient(90deg, #bb86fc, #03dac6, #6200ea);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: fadeDown 1.2s ease;
}
.hero p {
    color: #aaa;
    font-size: 1.2rem;
    margin-top: 20px;
    max-width: 650px;
    animation: fadeUp 1.4s ease;
}
.btn-hero {
    margin-top: 35px;
    padding: 14px 36px;
    font-size: 1rem;
    border-radius: 40px;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(90deg, #6200ea, #bb86fc);
    border: none;
    transition: all 0.35s ease;
    box-shadow: 0px 6px 15px rgba(98,0,234,0.6);
}
.btn-hero:hover {
    transform: translateY(-4px) scale(1.05);
    box-shadow: 0px 10px 25px rgba(187,134,252, 0.7);
}

/* ===== FEATURES ===== */
.features {
    padding: 100px 0;
    background: #101018;
}
.features h2 {
    text-align: center;
    margin-bottom: 60px;
    font-weight: 700;
    font-size: 2.2rem;
    background: linear-gradient(90deg, #bb86fc, #03dac6);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Feature Card Style */
.feature-box {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(187,134,252,0.15);
    border-radius: 20px;
    padding: 40px 25px;
    text-align: center;
    backdrop-filter: blur(8px);
    transition: all 0.4s ease;
    position: relative;
    overflow: hidden;
}
.feature-box::before {
    content:"";
    position:absolute;
    top:-50%;
    left:-50%;
    width:200%;
    height:200%;
    background: radial-gradient(circle, rgba(187,134,252,0.2), transparent 60%);
    transform:rotate(25deg);
    opacity:0;
    transition:opacity .6s ease;
}
.feature-box:hover::before { opacity: 1; }
.feature-box:hover {
    transform: translateY(-12px) scale(1.03);
    border-color: rgba(187,134,252,0.4);
    box-shadow: 0px 12px 30px rgba(98,0,234,0.4);
}
.feature-icon {
    font-size: 3rem;
    display:inline-block;
    margin-bottom: 20px;
    transition: transform 0.4s ease, color 0.3s ease;
    color: #bb86fc;
}
.feature-box:hover .feature-icon {
    transform: scale(1.15) rotate(5deg);
    color: #03dac6;
}
.feature-title {
    font-size: 1.3rem;
    font-weight: 600;
    margin-bottom: 15px;
    color: #fff;
}
.feature-text {
    font-size: 0.95rem;
    color: #bbb;
}
.feature-box a {
    margin-top: 18px;
    border-radius: 25px;
    font-weight: 500;
}

/* ===== CTA ===== */
.cta-section {
    padding: 90px 20px;
    background: linear-gradient(120deg,#6200ea,#bb86fc,#03dac6);
    text-align: center;
    color: #fff;
    border-radius: 20px;
    margin: 80px auto;
    max-width: 1000px;
    box-shadow: 0px 10px 25px rgba(98,0,234,.5);
}
.cta-section h3 {
    font-size: 2rem;
    font-weight: 700;
}
.cta-section p {
    margin: 15px auto 30px;
    font-size: 1.1rem; max-width:600px;
}
.cta-section .btn-light {
    border-radius: 30px;
    padding: 12px 30px;
    font-weight: 600;
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

/* Animations */
@keyframes fadeDown {from{opacity:0;transform:translateY(-40px);}to{opacity:1;transform:translateY(0);}}
@keyframes fadeUp {from{opacity:0;transform:translateY(40px);}to{opacity:1;transform:translateY(0);}}
</style>

{{-- HERO --}}
<div class="hero">
    <h1>⚡ Semantic SEO Checker</h1>
    <p>Cutting‑edge AI tools to audit, analyze and optimize your content with precision for higher search rankings.</p>
    <a href="#tools" class="btn-hero">Explore Tools</a>
</div>

{{-- FEATURES --}}
<div id="tools" class="features container">
    <h2>Our SEO Tools</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="feature-box h-100">
                <div class="feature-icon">📝</div>
                <h4 class="feature-title">SEO Audit</h4>
                <p class="feature-text">Comprehensive technical & on‑page audits powered by AI. Detect errors before search engines do.</p>
                <a href="{{ route('seo.audit.index') }}" class="btn btn-outline-light btn-sm">Run Audit</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-box h-100">
                <div class="feature-icon">🔑</div>
                <h4 class="feature-title">Keyword Analyzer</h4>
                <p class="feature-text">Measure keyword density, uncover semantic terms & build content that ranks above competitors.</p>
                <a href="{{ route('seo.keyword.index') }}" class="btn btn-outline-light btn-sm">Analyze</a>
            </div>
        </div>
        <div class="col-md-4">
            <div class="feature-box h-100">
                <div class="feature-icon">⚡</div>
                <h4 class="feature-title">Content Optimizer</h4>
                <p class="feature-text">AI‑led semantic optimization of titles, headings & metadata to maximize engagement & clicks.</p>
                <a href="{{ route('seo.optimizer.index') }}" class="btn btn-outline-light btn-sm">Optimize</a>
            </div>
        </div>
    </div>
</div>

{{-- CALL TO ACTION --}}
<div class="cta-section">
    <h3>Ready to Dominate SEO?</h3>
    <p>Unlock semantic search power today. Start analyzing & optimizing like top SEOs and agencies worldwide.</p>
    <a href="#tools" class="btn btn-light">Get Started</a>
</div>

{{-- FOOTER --}}
<footer>
    <p>© {{ date('Y') }} Semantic SEO Checker • All Rights Reserved • <a href="#">Privacy Policy</a></p>
</footer>

<script>
window.addEventListener('scroll', () => {
  document.querySelector('.navbar').classList.toggle('scrolled', window.scrollY > 20);
});
</script>
@endsection
