@extends('layouts.app')

@section('title', 'Jagoowala - SEO Audit Tool')

@section('content')
<style>
/* ===== Base Dark Purple Background ===== */
body {
  background: radial-gradient(circle at top, #200030 0%, #12021c 40%, #0a0012 100%);
  color:#eee;
  font-family:'Inter','Poppins',sans-serif;
  overflow-x:hidden;
  margin:0;
}
:root {
  --purple:#6a0dad;
  --orange:#ff8500;
  --blue:#2196f3;
  --dark:#0d0017;
}

/* ===== Smoke Effect ===== */
.smoke {
  position:fixed;
  top:0; left:0; width:100%; height:100%;
  pointer-events:none; overflow:hidden;
  z-index:0;
}
.smoke span {
  position:absolute;
  width:800px; height:800px;
  border-radius:50%;
  background:radial-gradient(circle, rgba(106,13,173,0.35) 0%, rgba(0,0,0,0) 70%);
  filter:blur(120px);
  animation: drift 100s linear infinite;
}
.smoke span:nth-child(1){ bottom:-300px; left:-200px; animation-delay:0s;}
.smoke span:nth-child(2){ bottom:-250px; left:300px; background:radial-gradient(circle, rgba(255,133,0,0.25) 0%, rgba(0,0,0,0) 70%); animation-delay:20s;}
.smoke span:nth-child(3){ top:-200px; left:500px; background:radial-gradient(circle, rgba(33,150,243,0.25) 0%, rgba(0,0,0,0) 70%); animation-delay:40s;}
.smoke span:nth-child(4){ top:100px; right:-250px; background:radial-gradient(circle, rgba(187,134,252,0.3) 0%, rgba(0,0,0,0) 70%); animation-delay:60s;}

@keyframes drift {
  0% { transform: translate(0,0) scale(1); opacity:.4;}
  50%{ transform: translate(400px,-400px) scale(1.3); opacity:.25;}
  100%{ transform: translate(800px,-900px) scale(1.6); opacity:0;}
}

/* ===== Navbar ===== */
.navbar {
  background:#1a092f !important;
  border-bottom:2px solid var(--orange);
  box-shadow:0 3px 12px rgba(0,0,0,.6);
  z-index:5;
}
.navbar-brand {
  font-weight:800; font-size:1.6rem;
  color:var(--orange) !important;
}
.nav-link {
  font-weight:500; color:#e0d7f5 !important; margin:0 12px;
  transition:color .3s, text-shadow .3s;
}
.nav-link:hover, .nav-link.active {
  color:var(--orange)!important;
  text-shadow:0 0 8px var(--purple);
}

/* ===== Hero ===== */
.hero {
  min-height:90vh; display:flex;align-items:center;justify-content:center;
  flex-direction:column; text-align:center; z-index:2; position:relative;
  padding:0 20px;
}
.hero h1 {
  font-size:3.6rem; font-weight:800;
  background:linear-gradient(90deg,var(--orange),#ffb347,var(--purple));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.hero p {margin-top:20px;color:#ccc;max-width:640px;}
.btn-hero {
  margin-top:30px;padding:14px 38px;border-radius:40px;
  font-weight:600;font-size:1rem;color:#fff;border:none;
  background:linear-gradient(90deg,var(--purple),var(--orange));
  box-shadow:0 6px 18px rgba(255,133,0,.5);
  transition:.3s;
}
.btn-hero:hover {
  transform:scale(1.07) translateY(-3px);
  box-shadow:0 10px 28px rgba(255,133,0,.6);
}

/* ===== Features ===== */
.features {
  padding:100px 0;
  position:relative; z-index:2;
}
.features h2 {
  text-align:center; margin-bottom:60px; font-size:2.6rem; font-weight:800;
  background:linear-gradient(90deg,var(--orange),var(--purple),var(--orange));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.features h2::after {
  content:""; display:block; width:60px; height:4px; margin:15px auto 0;
  border-radius:2px; background:linear-gradient(90deg,var(--orange),var(--purple));
}
.feature-box {
  background:linear-gradient(145deg,#1b0a28,#0e0512);
  border-radius:20px;
  padding:45px 28px; text-align:center; color:#fff;
  box-shadow:0 0 18px rgba(0,0,0,.5);
  position:relative;
  transition:.4s; border:2px solid transparent;
}
.feature-box::before {
  content:""; position:absolute; inset:-2px;
  border-radius:22px;
  background:linear-gradient(120deg,var(--purple),var(--orange),var(--blue));
  z-index:-1; opacity:.4;
}
.feature-box:hover {
  transform: translateY(-12px) scale(1.03);
  box-shadow:0 0 30px rgba(255,133,0,.6), inset 0 0 18px rgba(106,13,173,.5);
}
.feature-icon { font-size:3rem; margin-bottom:18px; }
.feature-title { font-size:1.4rem; font-weight:700; margin-bottom:14px; }
.feature-text { font-size:.95rem; color:#ccc; margin-bottom:20px; }

/* Buttons */
.btn-tool {
  padding:12px 30px; border-radius:30px; font-weight:600;
  background:linear-gradient(90deg,var(--purple),var(--orange));
  border:none; color:#fff; transition:.3s;
  box-shadow:0 0 15px rgba(90,24,154,0.4);
}
.btn-tool:hover {
  transform:scale(1.08);
  box-shadow:0 0 25px rgba(255,133,0,.65), inset 0 0 18px rgba(106,13,173,.5);
}

/* ===== CTA ===== */
.cta {
  max-width:950px;margin:90px auto;padding:70px 20px;
  border-radius:20px;text-align:center;color:#fff;
  background:linear-gradient(120deg,var(--purple),var(--orange));
  box-shadow:0 0 35px rgba(255,133,0,0.4);
}
.cta h3{font-size:2rem;font-weight:700;}
.cta p{max-width:600px;margin:15px auto 25px;}
.cta .btn-light{
  padding:12px 30px;border-radius:28px;font-weight:600;
  background:#fff;color:#333;border:none;transition:.3s;
}
.cta .btn-light:hover{
  background:var(--orange);color:#fff;
  box-shadow:0 0 18px rgba(255,133,0,.6);
}

/* ===== Footer ===== */
.site-footer {
  background:#080010; /* darker than main background */
  border-top:2px solid var(--purple);
  text-align:center;
  padding:20px;
  margin-top:60px;
  font-family:'Poppins',sans-serif;
}
.site-footer .footer-text {
  font-size:0.95rem;
  color:#d3c9e6;
  line-height:1.6;
}
.footer-brand {
  font-weight:700;
  background:linear-gradient(90deg, #ff8500, #ffb347, #9b4dff);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.footer-highlight {
  color:#ffb347;
  font-weight:600;
}
</style>

{{-- SMOKE EFFECT --}}
<div class="smoke">
  <span></span><span></span><span></span><span></span>
</div>

{{-- HERO --}}
<div class="hero">
  <h1>⚡ Jagoowala</h1>
  <p>AI‑powered tools to audit, analyze & optimize your site with semantic precision.</p>
  <a href="#features" class="btn-hero">Explore Tools</a>
</div>

{{-- FEATURES --}}
<div id="features" class="features container">
  <h2>⚡ Our SEO Tools</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-box">
        <div class="feature-icon">📝</div>
        <h4 class="feature-title">SEO Audit</h4>
        <p class="feature-text">Identify technical SEO issues, broken links, and performance gaps.</p>
        <a href="{{ route('seo.audit.index') }}" class="btn-tool">Run Audit</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box">
        <div class="feature-icon">🔑</div>
        <h4 class="feature-title">Keyword Analyzer</h4>
        <p class="feature-text">Analyze density, semantic matches & competitor keyword strengths.</p>
        <a href="{{ route('seo.keyword.index') }}" class="btn-tool">Analyze</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box">
        <div class="feature-icon">⚡</div>
        <h4 class="feature-title">Content Optimizer</h4>
        <p class="feature-text">AI-powered improvement suggestions for headings, metadata & flow.</p>
        <a href="{{ route('seo.optimizer.index') }}" class="btn-tool">Optimize</a>
      </div>
    </div>
  </div>
</div>

{{-- CTA --}}
<div class="cta">
  <h3>Ready to Dominate SEO Rankings?</h3>
  <p>Supercharge your SEO workflow with smart semantic analysis & optimization tools.</p>
  <a href="#features" class="btn btn-light">Get Started</a>
</div>

{{-- FOOTER --}}
<footer class="site-footer">
  <p class="footer-text">
    ♥️♥️ Crafted by <span class="footer-brand">Jagoowala</span> ♥️♥️ 
    © 2025 <span class="footer-highlight">SEO Audit Tool</span> • Crafted with ❤️ in Laravel + Bootstrap. 
    ♥️♥️ Crafted by <span class="footer-brand">Jagoowala</span> ♥️♥️
  </p>
</footer>

@endsection
