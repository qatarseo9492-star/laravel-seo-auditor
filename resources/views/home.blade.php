@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
/* ===== Base ===== */
body {
  background: linear-gradient(160deg, #14021f, #1c0530, #0d0017); /* Dark purple gradient background */
  color:#eee;
  font-family:'Inter','Poppins',sans-serif;
  overflow-x:hidden;
  margin:0;
}
:root {
  --purple:#5a189a;
  --orange:#ff8500;
  --blue:#2196f3;
  --teal:#03dac6;
  --dark:#111119;
}

/* ===== Navbar ===== */
.navbar {
  background:#1a092f !important; /* dark purple */
  border-bottom:2px solid var(--orange);
  box-shadow:0 3px 12px rgba(0,0,0,.6);
  position:relative; z-index:10;
}
.navbar-brand {
  font-weight:800;font-size:1.6rem;
  color:var(--orange) !important;
}
.nav-link {
  font-weight:500;color:#e0d7f5 !important;margin:0 12px;
  transition:color .3s, text-shadow .3s;
}
.nav-link:hover, .nav-link.active {
  color:var(--orange)!important;
  text-shadow:0 0 8px var(--purple);
}

/* ===== Hero ===== */
.hero {
  min-height:92vh;
  display:flex;flex-direction:column;align-items:center;justify-content:center;
  text-align:center;position:relative;z-index:2;padding:0 20px;
}
.hero h1 {
  font-size:3.6rem;font-weight:800;
  background:linear-gradient(90deg,var(--orange),#ffb347,var(--purple));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.hero p {margin-top:20px;color:#ccc;max-width:640px;}
.btn-hero {
  margin-top:30px;padding:14px 36px;border-radius:40px;
  font-weight:600;font-size:1rem;color:#fff;border:none;
  background:linear-gradient(90deg,var(--purple),var(--orange));
  box-shadow:0 6px 18px rgba(255,133,0,.5);
  transition:.3s;
}
.btn-hero:hover {
  transform:scale(1.05) translateY(-3px);
  box-shadow:0 10px 30px rgba(255,133,0,.6);
}

/* ===== Features Section ===== */
.features {
  padding:100px 0;position:relative;z-index:2;
}
.features h2 {
  text-align:center;margin-bottom:60px;font-size:2.4rem;font-weight:700;
  background:linear-gradient(90deg,var(--orange),var(--purple));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.feature-box {
  background:linear-gradient(160deg,#220a33,#12051d);
  border:2px solid rgba(255,133,0,.5);
  border-radius:20px;
  padding:40px 25px;text-align:center;color:#fff;
  transition:.3s;box-shadow:0 0 15px rgba(0,0,0,.4);
}
.feature-box:hover { transform:translateY(-8px); box-shadow:0 0 28px rgba(255,133,0,.6);}
.feature-icon {font-size:2.6rem;margin-bottom:15px;}
.feature-title {font-size:1.3rem;font-weight:600;margin-bottom:12px;}
.feature-text {font-size:.95rem;color:#ccc;}

.feature-box.audit { border-color:var(--purple);}
.feature-box.keyword { border-color:var(--blue);}
.feature-box.optimizer { border-color:var(--orange);}

.btn-tool {
  margin-top:20px;padding:10px 26px;border:none;
  border-radius:24px;font-weight:600;
  background:linear-gradient(90deg,var(--purple),var(--orange));
  color:#fff;transition:.35s;
}
.btn-tool:hover {
  transform:scale(1.07);
  box-shadow:0 0 22px rgba(255,133,0,.65);
}

/* ===== CTA ===== */
.cta {
  max-width:950px;margin:80px auto;padding:70px 20px;
  border-radius:20px;text-align:center;color:#fff;
  background:linear-gradient(120deg,var(--purple),var(--orange));
  box-shadow:0 0 40px rgba(255,133,0,0.4);
}
.cta h3{font-size:2rem;font-weight:700;}
.cta p{max-width:600px;margin:15px auto 25px;}
.cta .btn-light{
  padding:12px 28px;border-radius:28px;font-weight:600;
  background:#fff;color:#333;border:none;
  transition:.3s;
}
.cta .btn-light:hover{
  background:var(--orange);
  color:#fff;
  box-shadow:0 0 20px rgba(255,133,0,.6);
}
</style>

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
        <p class="feature-text">Find technical SEO issues, broken links & errors quickly.</p>
        <a href="{{ route('seo.audit.index') }}" class="btn-tool">Run Audit</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box keyword h-100">
        <div class="feature-icon">🔑</div>
        <h4 class="feature-title">Keyword Analyzer</h4>
        <p class="feature-text">Analyze density, discover semantic gaps & competitor strengths.</p>
        <a href="{{ route('seo.keyword.index') }}" class="btn-tool">Analyze</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature-box optimizer h-100">
        <div class="feature-icon">⚡</div>
        <h4 class="feature-title">Content Optimizer</h4>
        <p class="feature-text">AI suggestions for headings, metadata & content flow boosts.</p>
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
