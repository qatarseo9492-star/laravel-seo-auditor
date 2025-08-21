@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
/* ========== GLOBAL ========== */
body {
  background: #0b0b10;
  color: #eee;
  font-family: 'Inter', 'Poppins', sans-serif;
  overflow-x: hidden;
}
:root {
  --purple: #bb86fc;
  --blue: #2196f3;
  --teal: #03dac6;
  --dark-bg: #111119;
}

/* ========== NAVBAR ========== */
.navbar {
  background: rgba(15,15,20,0.7);
  backdrop-filter: blur(12px);
  border-bottom: 2px solid transparent;
  border-image: linear-gradient(90deg,var(--purple),var(--blue),var(--teal)) 1;
  transition: background 0.3s, border .3s;
}
.navbar.scrolled { background: rgba(10,10,15,0.95); }
.navbar-brand {
  font-weight: 800; font-size: 1.6rem;
  background: linear-gradient(90deg,var(--purple),var(--teal));
  -webkit-background-clip: text; -webkit-text-fill-color: transparent;
}
.nav-link {
  font-weight: 500; color:#ccc !important; margin: 0 12px; position:relative;
}
.nav-link::after {
  content:''; position:absolute; bottom:-6px; left:50%; width:0; height:2px;
  background:linear-gradient(90deg,var(--purple),var(--teal));
  transition: width .3s, left .3s;
}
.nav-link:hover { color: var(--purple)!important; }
.nav-link:hover::after { width:100%; left:0; }

/* ========== HERO ========== */
.hero {
  min-height: 95vh;
  display:flex; flex-direction:column;
  justify-content:center; align-items:center;
  text-align:center; padding:0 20px;
  background: radial-gradient(circle at 20% 30%,rgba(187,134,252,0.2),transparent 65%),
              radial-gradient(circle at 80% 10%,rgba(3,218,198,0.2),transparent 65%),
              #0b0b10;
  position: relative;
}
.hero h1 {
  font-size: 3.8rem;
  font-weight: 800;
  background: linear-gradient(90deg,var(--purple),var(--blue),var(--teal));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  line-height: 1.2;
  animation: fadeDown 1s ease;
}
.hero p {
  max-width: 650px; margin-top:20px; color:#aaa; font-size:1.2rem;
  animation: fadeUp 1.3s ease;
}
.btn-hero {
  margin-top:35px; padding:14px 40px; font-weight:600;
  border-radius:40px; font-size:1rem; color:#fff;
  background: linear-gradient(90deg,var(--purple),var(--teal));
  box-shadow:0px 6px 18px rgba(123,97,255,0.6);
  transition:.35s ease;
}
.btn-hero:hover { transform:scale(1.05) translateY(-4px);
  box-shadow:0px 12px 24px rgba(3,218,198,0.6); }

/* Floating glow */
.hero::before,.hero::after{
  content:""; position:absolute; width:250px;height:250px; border-radius:50%;
  background:radial-gradient(circle,var(--purple),transparent 70%);
  filter:blur(120px); opacity:0.3; animation: float 14s infinite;
}
.hero::after{ right:10%;top:15%; background:radial-gradient(circle,var(--teal),transparent 70%);
  animation-delay:-6s; }

/* Animations */
@keyframes fadeDown{from{opacity:0;transform:translateY(-40px);}to{opacity:1;transform:translateY(0);}}
@keyframes fadeUp{from{opacity:0;transform:translateY(40px);}to{opacity:1;transform:translateY(0);}}
@keyframes float{0%,100%{transform:translateY(0);}50%{transform:translateY(-40px);}}

/* ========== FEATURES ========== */
.features { padding:100px 0; background: var(--dark-bg); }
.features h2 {
  text-align:center; font-size:2.3rem; font-weight:700; margin-bottom:60px;
  background:linear-gradient(90deg,var(--purple),var(--teal));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
}
.feature-box {
  border-radius: 22px; padding:40px 25px; text-align:center;
  background: rgba(255,255,255,0.03);
  backdrop-filter: blur(10px);
  transition: all .4s ease; overflow:hidden;
  border:2px solid transparent;
}
.feature-icon {
  font-size:2.8rem; margin-bottom:20px; transition:.4s;
}
.feature-title { font-size:1.3rem; font-weight:600; color:#fff; margin-bottom:15px;}
.feature-text { color:#aaa; font-size:.95rem; }

/* Hover Glow Effect */
.feature-box:hover { transform:translateY(-12px) scale(1.02); }

/* --- Custom Themed Boxes --- */
.feature-box.audit {
  border-color: rgba(187,134,252,0.4);
  box-shadow: 0 0 20px rgba(187,134,252,0.2);
}
.feature-box.audit:hover {
  box-shadow: 0 0 35px rgba(187,134,252,0.6);
}
.feature-box.audit .feature-icon { color: var(--purple); }

.feature-box.keyword {
  border-color: rgba(33,150,243,0.4);
  box-shadow: 0 0 20px rgba(33,150,243,0.2);
}
.feature-box.keyword:hover {
  box-shadow: 0 0 35px rgba(33,150,243,0.6);
}
.feature-box.keyword .feature-icon { color: var(--blue); }

.feature-box.optimizer {
  border-color: rgba(3,218,198,0.4);
  box-shadow: 0 0 20px rgba(3,218,198,0.2);
}
.feature-box.optimizer:hover {
  box-shadow: 0 0 35px rgba(3,218,198,0.6);
}
.feature-box.optimizer .feature-icon { color: var(--teal); }

/* ========== CTA ========== */
.cta {
  max-width:1000px; margin:80px auto; padding:80px 20px;
  border-radius:22px; text-align:center; color:#fff;
  background:linear-gradient(120deg,var(--purple),var(--blue),var(--teal));
  box-shadow:0 12px 36px rgba(123,97,255,0.5);
}
.cta h3 { font-size:2rem; font-weight:700; }
.cta p { margin:15px auto 25px; max-width:640px; }
.cta .btn-light {
  padding:12px 32px; border-radius:30px; font-weight:600;
}

/* FOOTER */
footer { background:#0a0a0f; padding:25px 0; color:#888; text-align:center;}
footer a{ color:var(--purple); text-decoration:none;}
</style>

{{-- HERO --}}
<div class="hero">
  <h1>⚡ Semantic SEO Checker</h1>
  <p>AI‑powered tools to audit, analyze & optimize your site with precision-semantic insights.</p>
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
        <p class="feature-text">Smart audits to find technical SEO issues, crawl errors & performance blockers instantly.</p>
        <a href="{{ route('seo.audit.index') }}" class="btn btn-outline-light btn-sm mt-3">Run Audit</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box keyword h-100">
        <div class="feature-icon">🔑</div>
        <h4 class="feature-title">Keyword Analyzer</h4>
        <p class="feature-text">Analyze density, discover semantic variations & gain a strategic ranking advantage.</p>
        <a href="{{ route('seo.keyword.index') }}" class="btn btn-outline-light btn-sm mt-3">Analyze</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box optimizer h-100">
        <div class="feature-icon">⚡</div>
        <h4 class="feature-title">Content Optimizer</h4>
        <p class="feature-text">AI‑recommendations for titles, metadata & structure to boost engagement & rankings.</p>
        <a href="{{ route('seo.optimizer.index') }}" class="btn btn-outline-light btn-sm mt-3">Optimize</a>
      </div>
    </div>
  </div>
</div>

{{-- CTA --}}
<div class="cta">
  <h3>Ready to Dominate Search Rankings?</h3>
  <p>Supercharge your website’s SEO with smart semantic analysis & optimization built for growth.</p>
  <a href="#features" class="btn btn-light">Get Started Now</a>
</div>

{{-- FOOTER --}}
<footer>
  <p>© {{ date('Y') }} Semantic SEO Checker. All Rights Reserved · <a href="#">Privacy</a></p>
</footer>

{{-- JS --}}
<script>
window.addEventListener('scroll', () => {
  document.querySelector('.navbar').classList.toggle('scrolled', window.scrollY>20);
});
</script>
@endsection
