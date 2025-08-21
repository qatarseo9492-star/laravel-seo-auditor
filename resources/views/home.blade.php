@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
body {
  background:#0b0b10;
  color:#eee;
  font-family:'Inter','Poppins',sans-serif;
  overflow-x:hidden;
  margin:0;
}
:root {
  --purple:#bb86fc;
  --blue:#2196f3;
  --teal:#03dac6;
  --dark:#111119;
}

/* ---------- STAR BACKGROUND ---------- */
.stars {
  position: fixed;
  top:0;left:0;width:100%;height:100%;
  z-index:0;pointer-events:none;
}
.star {
  position:absolute;
  width:6px;height:6px;border-radius:50%;
  background:gold;
  box-shadow:0 0 10px gold,0 0 20px orange;
  animation: twinkle 3s infinite ease-in-out;
}
@keyframes twinkle {
  0%,100%{opacity:0.3;transform:scale(.6);}
  50%{opacity:1;transform:scale(1);}
}

/* ---------- SMOKE ---------- */
.smoke span {
  position:absolute;
  bottom:-250px;left:-150px;
  width:600px;height:600px;border-radius:50%;
  filter:blur(100px);
  animation: drift 80s linear infinite;
}
.smoke span:nth-child(1){background:rgba(187,134,252,.25);}
.smoke span:nth-child(2){background:rgba(33,150,243,.25);animation-delay:20s;}
.smoke span:nth-child(3){background:rgba(3,218,198,.25);animation-delay:40s;}
.smoke span:nth-child(4){background:rgba(255,97,246,.25);animation-delay:60s;}
@keyframes drift {
  0%{transform:translate(0,0) scale(1);opacity:.4;}
  50%{transform:translate(400px,-400px) scale(1.3);opacity:.3;}
  100%{transform:translate(800px,-900px) scale(1.6);opacity:0;}
}

/* ---------- HERO ---------- */
.hero {
  min-height:90vh;display:flex;flex-direction:column;align-items:center;justify-content:center;
  text-align:center;position:relative;z-index:2;padding:0 20px;
}
.hero h1 {
  font-size:3.5rem;font-weight:800;
  background:linear-gradient(90deg,var(--purple),var(--blue),var(--teal));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.hero p {color:#aaa;max-width:640px;margin-top:20px;}
.btn-hero {
  margin-top:30px;padding:14px 36px;border-radius:40px;
  font-weight:600;font-size:1rem;color:#fff;border:none;
  background:linear-gradient(90deg,var(--purple),var(--teal));
  box-shadow:0 6px 18px rgba(155,89,182,.5);
  transition:.3s;z-index:3;
}
.btn-hero:hover {transform:scale(1.05) translateY(-3px);box-shadow:0 10px 24px rgba(33,150,243,.6);}

/* ---------- FEATURES ---------- */
.features {background:var(--dark);padding:100px 0;position:relative;z-index:2;}
.features h2 {
  text-align:center;margin-bottom:60px;font-size:2.4rem;font-weight:700;
  background:linear-gradient(90deg,var(--purple),var(--teal));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.feature-box {
  color:#fff;text-align:center;padding:40px 25px;border-radius:20px;position:relative;overflow:hidden;
  background:rgba(255,255,255,.05);
}
.feature-icon {font-size:2.5rem;margin-bottom:15px;}
.feature-title {font-size:1.25rem;font-weight:600;margin-bottom:12px;}
.feature-text {font-size:.95rem;color:#ddd;}

/* --- Fire-like rotating border --- */
.feature-box::after {
  content:""; position:absolute; inset:-2px;
  border-radius:22px;
  background: conic-gradient(from 0deg,var(--purple),var(--blue),var(--teal),var(--purple));
  padding:2px;
  -webkit-mask:linear-gradient(#fff 0 0) content-box,linear-gradient(#fff 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude;
  animation: rotateBorder 12s linear infinite;
}
@keyframes rotateBorder {to{transform:rotate(360deg);}}

/* Card color themes inside */
.feature-box.audit {background:linear-gradient(160deg,#3c1f4f,#2a1336);}
.feature-box.keyword {background:linear-gradient(160deg,#0f2e45,#144a6f);}
.feature-box.optimizer {background:linear-gradient(160deg,#0b3b33,#0f6b5f);}

/* Buttons */
.btn-tool {margin-top:20px;border-radius:25px;padding:10px 24px;font-weight:600;border:none;transition:.3s;}
.btn-audit {background:linear-gradient(90deg,#9b59b6,#e0aaff);color:#fff;}
.btn-keyword {background:linear-gradient(90deg,#2196f3,#64b5f6);color:#fff;}
.btn-optimizer {background:linear-gradient(90deg,#03dac6,#1de9b6);color:#0b0b0b;}
.btn-tool:hover {transform:scale(1.05);}

/* ---------- CTA ---------- */
.cta {
  max-width:950px;margin:90px auto;padding:70px 20px;border-radius:20px;
  text-align:center;background:linear-gradient(120deg,var(--purple),var(--blue),var(--teal));
  color:#fff;
}
.cta h3{font-size:2rem;font-weight:700;}
.cta p{max-width:600px;margin:15px auto 25px;}
.cta .btn-light{padding:12px 30px;border-radius:30px;font-weight:600;}

/* ---------- Footer ---------- */
footer {background:#0a0a0f;padding:25px 0;text-align:center;color:#888;font-size:.9rem;}
footer a{color:var(--purple);}
</style>

{{-- STARS --}}
<div class="stars">
  @for($i=0;$i<25;$i++)
    <div class="star" style="top:{{ rand(0,95) }}%;left:{{ rand(0,95) }}%;animation-delay:{{ rand(0,3) }}s;"></div>
  @endfor
</div>

{{-- SMOKE --}}
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
  <p>Supercharge your SEO workflow with semantic analysis & optimization built for growth.</p>
  <a href="#features" class="btn btn-light">Get Started</a>
</div>

{{-- FOOTER --}}
<footer>
  <p>© {{ date('Y') }} Semantic SEO Checker · All Rights Reserved · <a href="#">Privacy</a></p>
</footer>
@endsection
