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
  margin:0;
}
:root {
  --purple:#5a189a;
  --orange:#ff8500;
  --dark:#111119;
}

/* ===== Cloud Effect Background ===== */
.cloud-scene {
  position: fixed;
  top:0; left:0; width:100%; height:100vh;
  background: radial-gradient(ellipse at bottom left,
     rgba(123, 31, 162, 0.55) 0%,    /* Deep purple start */
     rgba(179, 136, 255, 0.35) 35%,  /* Light violet spreading */
     rgba(33, 0, 60, 0.3) 70%,       /* Dark indigo blend */
     rgba(10, 10, 15, 1) 100%        /* Dark edges */
  );
  background-repeat:no-repeat;
  background-size:cover;
  z-index:0;
  overflow:hidden;
}
.cloud-scene::before, .cloud-scene::after {
  content:"";
  position:absolute; inset:0;
  background:url('https://i.ibb.co/vz2wTZF/fog-texture.png'); /* Transparent fog texture */
  background-size:cover;
  opacity:0.15;
  animation: drift 120s linear infinite;
}
.cloud-scene::after {
  opacity:0.25;
  animation-duration:180s;
}
@keyframes drift {
  0% { background-position: 0 0; }
  100% { background-position: 1000px -1000px; }
}

/* ===== Navbar ===== */
.navbar {
  background: var(--purple) !important;
  border-bottom:2px solid var(--orange);
  box-shadow:0 2px 10px rgba(0,0,0,.6);
  position:relative; z-index:3;
}
.navbar-brand {
  font-weight:800;font-size:1.6rem;
  color:var(--orange) !important;
}
.nav-link {
  font-weight:500;color:#f0e6ff !important;margin:0 12px;
  transition:color .3s;
}
.nav-link:hover { color:var(--orange)!important; }

/* ===== Hero ===== */
.hero {
  min-height:90vh;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  text-align:center;position:relative;z-index:2;padding:0 20px;
}
.hero h1 {
  font-size:3.5rem;font-weight:800;
  background:linear-gradient(90deg,var(--orange),#ffb347);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.hero p {margin-top:20px;color:#ccc;max-width:640px;}
.btn-hero {
  margin-top:30px;padding:14px 36px;border-radius:40px;
  font-weight:600;font-size:1rem;color:#fff;border:none;
  background:linear-gradient(90deg,var(--orange),#ffba66);
  box-shadow:0 6px 18px rgba(255,133,0,.5);
  transition:.3s;
}
.btn-hero:hover {
  transform:scale(1.05) translateY(-3px);
  box-shadow:0 10px 24px rgba(255,179,71,.6);
}

/* ===== Features ===== */
.features {
  background:var(--dark);padding:100px 0;position:relative;z-index:2;
}
.features h2 {
  text-align:center;margin-bottom:60px;font-size:2.4rem;font-weight:700;
  background:linear-gradient(90deg,var(--orange),var(--purple));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.feature-box {
  background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);
  border-radius:20px;padding:40px 25px;text-align:center;color:#fff;
  transition:.3s;
}
.feature-box:hover { transform:translateY(-8px); box-shadow:0 10px 20px rgba(0,0,0,0.6);}
.feature-icon {font-size:2.6rem;margin-bottom:15px;}
.feature-title {font-size:1.3rem;font-weight:600;margin-bottom:12px;}
.feature-text {font-size:.95rem;color:#ddd;}

.btn-tool {
  margin-top:20px;padding:10px 26px;border:none;
  border-radius:24px;font-weight:600;
  background:linear-gradient(90deg,var(--purple),var(--orange));
  color:#fff;transition:.3s;
}
.btn-tool:hover {transform:scale(1.05);box-shadow:0 0 18px rgba(255,133,0,.6);}

/* ===== CTA ===== */
.cta {
  max-width:950px;margin:80px auto;padding:70px 20px;
  border-radius:20px;text-align:center;color:#fff;
  background:linear-gradient(120deg,var(--purple),var(--orange));
}
.cta h3{font-size:2rem;font-weight:700;}
.cta p{max-width:600px;margin:15px auto 25px;}
.cta .btn-light{padding:12px 28px;border-radius:28px;font-weight:600;}
</style>

{{-- CLOUD BACKGROUND --}}
<div class="cloud-scene"></div>

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
        <p class="feature-text">Find technical SEO issues, broken links & site errors.</p>
        <a href="{{ route('seo.audit.index') }}" class="btn-tool">Run Audit</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box">
        <div class="feature-icon">🔑</div>
        <h4 class="feature-title">Keyword Analyzer</h4>
        <p class="feature-text">Discover semantic variations & keyword opportunities.</p>
        <a href="{{ route('seo.keyword.index') }}" class="btn-tool">Analyze</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box">
        <div class="feature-icon">⚡</div>
        <h4 class="feature-title">Content Optimizer</h4>
        <p class="feature-text">AI improvements for headings, metadata & flow.</p>
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

@endsection
