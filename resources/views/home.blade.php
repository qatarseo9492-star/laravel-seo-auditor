@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
/* ===== BASE ===== */
body {
  background: #0b0b10;
  color: #eee;
  font-family: 'Inter', 'Poppins', sans-serif;
  overflow-x: hidden;
  position: relative;
}

/* ===== SMOKE EFFECT ===== */
.smoke {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  overflow: hidden;
  z-index: 0; /* behind content */
  pointer-events: none;
}

.smoke span {
  position: absolute;
  width: 800px;
  height: 800px;
  background: radial-gradient(circle,
      rgba(187,134,252,0.15),
      rgba(33,150,243,0.12),
      rgba(3,218,198,0.1),
      transparent 70%);
  border-radius: 50%;
  filter: blur(120px);
  animation: drift 60s infinite linear;
  mix-blend-mode: screen;
  opacity: 0.45;
}

/* Multiple smoke layers with different colors & positions */
.smoke span:nth-child(1) {
  top: 100%; left: -20%;
  animation-delay: 0s;
  background: radial-gradient(circle,
      rgba(155,89,182,0.2),
      transparent 70%);
}
.smoke span:nth-child(2) {
  top: 120%; left: 30%;
  animation-delay: 5s;
  background: radial-gradient(circle,
      rgba(33,150,243,0.2),
      transparent 70%);
}
.smoke span:nth-child(3) {
  top: 140%; left: 70%;
  animation-delay: 10s;
  background: radial-gradient(circle,
      rgba(3,218,198,0.2),
      transparent 70%);
}
.smoke span:nth-child(4) {
  top: 130%; left: 50%;
  animation-delay: 20s;
  background: radial-gradient(circle,
      rgba(255,97,246,0.25),
      transparent 70%);
}

/* Smoke Movement */
@keyframes drift {
  0%   { transform: translateY(0) scale(1); opacity: 0.3; }
  25%  { opacity: 0.6; }
  50%  { transform: translateY(-100%) scale(1.2); opacity: 0.4; }
  75%  { opacity: 0.65; }
  100% { transform: translateY(-200%) scale(1.5); opacity: 0; }
}

/* ===== HERO ===== */
.hero {
  min-height: 90vh;
  display: flex; flex-direction: column; justify-content: center;
  align-items: center; text-align: center;
  position: relative; z-index: 1;
}
.hero h1 {
  font-size: 3.8rem;
  font-weight: 800;
  background: linear-gradient(90deg, #bb86fc, #2196f3, #03dac6);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
.hero p { max-width: 650px; margin-top: 15px; color: #aaa; font-size: 1.2rem; }
.btn-hero {
  margin-top: 30px; padding: 14px 38px;
  border: none; border-radius: 40px;
  font-weight: 600; font-size: 1rem;
  color: #fff;
  background: linear-gradient(90deg, #bb86fc, #03dac6);
  box-shadow: 0 8px 20px rgba(155,89,182,.5);
  transition: all .3s ease;
}
.btn-hero:hover {
  transform: scale(1.05) translateY(-3px);
  box-shadow: 0 10px 25px rgba(33,150,243,.6);
}

/* ===== SEO TOOL BOXES (keep previous "fire border" or gradient inner colors) ===== */
.features { padding: 100px 0; background: #111119; position: relative; z-index: 1; }
.features h2 {
  text-align: center; font-size: 2.3rem; font-weight: 700; margin-bottom: 60px;
  background: linear-gradient(90deg,#bb86fc,#03dac6);
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.feature-box {
  border-radius: 18px; padding: 40px 25px; text-align: center;
  color: #fff; position: relative; overflow: hidden;
}
.feature-box.audit { background: linear-gradient(165deg,#2e1437,#4a1f63); }
.feature-box.keyword { background: linear-gradient(165deg,#0f2e45,#205a8d); }
.feature-box.optimizer { background: linear-gradient(165deg,#0b3b33,#118472); }

.feature-icon { font-size: 2.8rem; margin-bottom: 20px; }
.feature-title { font-size: 1.3rem; font-weight: 600; margin-bottom: 12px; }
.feature-text { font-size: .95rem; color: #ddd; }

/* Themed Buttons */
.btn-tool { border: none; padding: 10px 24px; border-radius: 28px; font-weight: 600; }
.btn-audit { background: linear-gradient(90deg,#9b59b6,#e0aaff); color: #fff; }
.btn-keyword { background: linear-gradient(90deg,#2196f3,#64b5f6); color: #fff; }
.btn-optimizer { background: linear-gradient(90deg,#03dac6,#1de9b6); color: #0b0b0b; }

/* ===== CTA ===== */
.cta {
  max-width: 950px; margin: 80px auto; padding: 70px 20px;
  border-radius: 20px; background: linear-gradient(120deg,#bb86fc,#2196f3,#03dac6);
  text-align: center; color: #fff;
}
.cta h3 { font-size: 2rem; font-weight: 700; }
.cta p { max-width: 600px; margin: 15px auto 25px; }
.cta .btn-light { border-radius: 30px; padding: 12px 28px; font-weight: 600; }

/* Footer */
footer { background:#0a0a0f;padding:25px 0;text-align:center;font-size:.9rem;color:#888;}
footer a {color:#bb86fc;text-decoration:none;}
</style>

{{-- SMOKE Overlay --}}
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
      <div class="feature-box audit h-100">
        <div class="feature-icon">📝</div>
        <h4 class="feature-title">SEO Audit</h4>
        <p class="feature-text">Find technical SEO issues, broken links & performance gaps quickly.</p>
        <a href="{{ route('seo.audit.index') }}" class="btn-tool btn-audit">Run Audit</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box keyword h-100">
        <div class="feature-icon">🔑</div>
        <h4 class="feature-title">Keyword Analyzer</h4>
        <p class="feature-text">Analyze density, discover semantic variations & gain ranking advantage.</p>
        <a href="{{ route('seo.keyword.index') }}" class="btn-tool btn-keyword">Analyze</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box optimizer h-100">
        <div class="feature-icon">⚡</div>
        <h4 class="feature-title">Content Optimizer</h4>
        <p class="feature-text">AI suggestions for headlines, metadata & flow to boost search visibility.</p>
        <a href="{{ route('seo.optimizer.index') }}" class="btn-tool btn-optimizer">Optimize</a>
      </div>
    </div>
  </div>
</div>

{{-- CTA --}}
<div class="cta">
  <h3>Ready to Dominate SEO Rankings?</h3>
  <p>Supercharge your SEO workflow with powerful semantic analysis & optimization tools.</p>
  <a href="#features" class="btn btn-light">Get Started</a>
</div>

{{-- FOOTER --}}
<footer>
  <p>© {{ date('Y') }} Semantic SEO Checker · All Rights Reserved · <a href="#">Privacy</a></p>
</footer>
@endsection
