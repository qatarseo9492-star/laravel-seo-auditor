@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
/* ===== Base ===== */
body {
  background:#0b0b10;
  color:#eee;
  font-family:'Inter','Poppins',sans-serif;
  overflow-x:hidden;
  position: relative;
}
:root {
  --purple:#bb86fc;
  --blue:#2196f3;
  --teal:#03dac6;
  --dark:#111119;
}

/* ===== SMOKE Layer ===== */
.smoke {
  position: fixed;
  bottom: 0; left: 0;
  width: 100%; height: 100%;
  overflow: hidden;
  pointer-events: none;
  z-index: 0;
}
.smoke span {
  position: absolute;
  bottom: -300px; left: -200px;
  width: 600px; height: 600px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(187,134,252,0.25), transparent 70%);
  filter: blur(100px);
  animation: rise 60s linear infinite;
  opacity: 0.4;
}
/* Different colors + delays for variation */
.smoke span:nth-child(2) {
  background: radial-gradient(circle, rgba(33,150,243,0.25), transparent 70%);
  animation-delay: 10s;
  left: -100px;
}
.smoke span:nth-child(3) {
  background: radial-gradient(circle, rgba(3,218,198,0.25), transparent 70%);
  animation-delay: 20s;
  left: -150px;
}
.smoke span:nth-child(4) {
  background: radial-gradient(circle, rgba(255,97,246,0.25), transparent 70%);
  animation-delay: 30s;
  left: -50px;
}

/* The smoke drifts upward and spreads */
@keyframes rise {
  0%   { transform: translate(0,0) scale(1); opacity: .5; }
  50%  { transform: translate(300px,-400px) scale(1.3); opacity: .35; }
  100% { transform: translate(600px,-900px) scale(1.6); opacity: 0; }
}

/* ===== Hero (example) ===== */
.hero {
  min-height:90vh;
  display:flex;align-items:center;justify-content:center;
  flex-direction:column;text-align:center;
  position: relative; z-index: 1;
}
.hero h1 {
  font-size:3.5rem;font-weight:800;
  background:linear-gradient(90deg,var(--purple),var(--blue),var(--teal));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.hero p {
  margin-top:20px;
  color:#aaa;
  max-width:640px;
}
.btn-hero {
  margin-top:30px;padding:14px 36px;border-radius:40px;
  font-weight:600;font-size:1rem;color:#fff;
  background:linear-gradient(90deg,var(--purple),var(--teal));
  border:none;
  box-shadow:0px 6px 18px rgba(123,97,255,.5);
  transition:.3s;
}
.btn-hero:hover{transform:scale(1.05) translateY(-3px);box-shadow:0 10px 25px rgba(3,218,198,.6);}

/* ===== Features Section (simplified) ===== */
.features {
  background:var(--dark);
  padding:100px 0;
  position: relative; z-index: 1;
}
.features h2 {
  text-align:center;margin-bottom:60px;font-size:2.4rem;font-weight:700;
  background:linear-gradient(90deg, var(--purple), var(--teal));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.feature-box {
  background:rgba(255,255,255,.05);
  border:1px solid rgba(255,255,255,.1);
  border-radius:18px;
  text-align:center;padding:40px 25px;color:#fff;
  transition:.3s;
}
.feature-box:hover { transform:translateY(-10px); box-shadow:0 10px 25px rgba(0,0,0,0.6); }

/* ===== CTA ===== */
.cta {
  max-width:900px;margin:80px auto;padding:70px 20px;
  border-radius:20px;text-align:center;color:#fff;
  background:linear-gradient(120deg,var(--purple),var(--blue),var(--teal));
}
.cta .btn-light{border-radius:30px;padding:12px 32px;font-weight:600;}

/* ===== Footer ===== */
footer { background:#0a0a0f; padding:20px 0;text-align:center;font-size:.9rem;color:#888;}
footer a{color:var(--purple);}
</style>

{{-- SMOKE EFFECT --}}
<div class="smoke">
  <span></span>
  <span></span>
  <span></span>
  <span></span>
</div>

{{-- HERO --}}
<div class="hero">
  <h1>⚡ Semantic SEO Checker</h1>
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
        <p>Fix technical issues, speed problems, and optimize site health.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box">
        <div class="feature-icon">🔑</div>
        <h4 class="feature-title">Keyword Analyzer</h4>
        <p>Discover hidden keyword opportunities & semantic coverage gaps.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box">
        <div class="feature-icon">⚡</div>
        <h4 class="feature-title">Content Optimizer</h4>
        <p>AI suggests titles, metadata & structure improvements.</p>
      </div>
    </div>
  </div>
</div>

{{-- CTA --}}
<div class="cta">
  <h3>Supercharge Your SEO Today</h3>
  <p>Unlock AI‑powered analysis & optimization designed for growth.</p>
  <a href="#features" class="btn btn-light">Get Started</a>
</div>

{{-- FOOTER --}}
<footer>
  <p>© {{ date('Y') }} Semantic SEO Checker · All Rights Reserved · <a href="#">Privacy Policy</a></p>
</footer>
@endsection
