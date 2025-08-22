@extends('layouts.app')

@section('title', 'Jagoowala - SEO Suite')

@section('content')
<style>
/* Light modern theme inspired by Pico demo */
body {
  background:#f9fafc;
  font-family:'Inter','Poppins',sans-serif;
  color:#333;
}
.hero {
  padding:120px 20px;
  text-align:center;
}
.hero h1 {
  font-size:3rem; font-weight:800;
  background:linear-gradient(90deg,#6a0dad,#ff8500);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.hero p {
  margin-top:20px; font-size:1.2rem; color:#555; max-width:600px; margin:auto;
}
.btn-cta {
  margin-top:30px; padding:14px 38px; border-radius:40px;
  font-weight:600; color:#fff; background:linear-gradient(90deg,#6a0dad,#ff8500);
  border:none; transition:.3s;
}
.btn-cta:hover { transform:scale(1.05); }

.section {
  padding:80px 0;
}
.section h2 {
  text-align:center; font-weight:700; margin-bottom:50px;
}

.feature {
  background:#fff; border-radius:16px; padding:40px; text-align:center;
  box-shadow:0 8px 20px rgba(0,0,0,0.05); transition:.3s;
}
.feature:hover { transform:translateY(-8px); box-shadow:0 12px 25px rgba(0,0,0,0.08); }
.feature-icon { font-size:2.5rem; margin-bottom:18px; color:#6a0dad; }
.feature h4 { font-size:1.3rem; font-weight:600; margin-bottom:15px; }

/* Footer */
.site-footer {
  background:#0d0017; color:#eee; text-align:center;
  padding:25px 15px; margin-top:80px;
}
.site-footer .footer-text { font-size:.9rem; color:#ddd; }
.footer-brand {
  font-weight:600;
  background:linear-gradient(90deg,#ff8500,#6a0dad);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
}
</style>

{{-- HERO --}}
<div class="hero">
  <h1>Semantic SEO Suite by Jagoowala</h1>
  <p>Supercharge your website with AI‑powered SEO Audit, Keyword Analysis & Content Optimization Tools.</p>
  <a href="#features" class="btn btn-cta">Start Optimizing</a>
</div>

{{-- FEATURES --}}
<section id="features" class="section container">
  <h2>🚀 Our Tools</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature">
        <div class="feature-icon">📝</div>
        <h4>SEO Audit</h4>
        <p>Detect technical SEO issues like broken links, metadata, indexing, and performance bottlenecks.</p>
        <a href="{{ route('seo.audit.index') }}" class="btn btn-sm btn-outline-primary mt-3">Run Audit</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature">
        <div class="feature-icon">🔑</div>
        <h4>Keyword Analyzer</h4>
        <p>Analyze keyword performance & find semantic keyword opportunities to outrank competitors.</p>
        <a href="{{ route('seo.keyword.index') }}" class="btn btn-sm btn-outline-primary mt-3">Analyze Keywords</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature">
        <div class="feature-icon">⚡</div>
        <h4>Content Optimizer</h4>
        <p>AI suggestions for improving titles, metadata, headings, and semantic content quality.</p>
        <a href="{{ route('seo.optimizer.index') }}" class="btn btn-sm btn-outline-primary mt-3">Optimize Now</a>
      </div>
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="section text-center bg-light">
  <div class="container">
    <h2>Ready to Rise in Rankings?</h2>
    <p>Upgrade your SEO strategy with Jagoowala’s intelligent tools — faster, smarter, better.</p>
    <a href="#features" class="btn btn-cta">Explore All Tools</a>
  </div>
</section>

{{-- FOOTER --}}
<footer class="site-footer">
  <p class="footer-text">
    ♥️♥️ Crafted by <span class="footer-brand">Jagoowala</span> ♥️♥️  
    © 2025 SEO Audit Tool • Crafted with ❤️ in Laravel + Bootstrap.  
    ♥️♥️ Crafted by <span class="footer-brand">Jagoowala</span> ♥️♥️
  </p>
</footer>
@endsection
