@extends('layouts.app')

@section('title', 'Jagoowala - SEO Suite')

@section('content')
<style>
/* ===== Pico Dark Theme Inspired ===== */
body {
  background: #0d0d0f;
  color: #e8e8e8;
  font-family: 'Inter','Poppins',sans-serif;
  margin:0;
  overflow-x:hidden;
}
:root {
  --primary:#6366F1; /* indigo accent */
  --secondary:#9333EA; /* purple accent */
  --orange:#F97316;
}

/* Hero */
.hero {
  padding:140px 20px 120px;
  text-align:center;
}
.hero h1 {
  font-size:3rem; font-weight:800; line-height:1.2;
  background:linear-gradient(90deg,var(--primary),var(--orange));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.hero p {
  margin-top:20px; font-size:1.2rem; color:#aaa; max-width:640px; margin-left:auto;margin-right:auto;
}
.btn-cta {
  margin-top:35px; padding:14px 40px;
  border-radius:40px; font-weight:600; color:#fff;
  background:linear-gradient(90deg,var(--primary),var(--secondary));
  border:none; transition:.3s;
}
.btn-cta:hover { transform:scale(1.05); opacity:.9; }

/* Section */
.section {
  padding:100px 0;
}
.section h2 {
  text-align:center; font-weight:700; margin-bottom:60px;
}

/* Feature Cards */
.feature {
  background:#16161a;
  border-radius:16px;
  padding:40px;
  text-align:center;
  box-shadow:0 8px 22px rgba(0,0,0,.5);
  transition:.3s;
}
.feature:hover {
  transform:translateY(-8px);
  box-shadow:0 12px 28px rgba(0,0,0,.65);
}
.feature-icon {
  font-size:2.5rem;
  margin-bottom:20px;
  color:var(--primary);
}
.feature h4 {
  font-size:1.3rem; font-weight:600; margin-bottom:15px;
}
.feature p { color:#bbb; }

/* CTA Section */
.section.cta {
  background:linear-gradient(90deg,#111112,#16161a);
  text-align:center;
  border-radius:18px;
  padding:80px 30px;
}
.section.cta h3 {
  font-size:2rem; font-weight:700;
}
.section.cta p { color:#ccc; margin:20px auto 25px; max-width:600px; }

/* Footer */
.site-footer {
  background:#080808;
  border-top:1px solid #222;
  padding:25px 15px;
  margin-top:100px;
  text-align:center;
}
.site-footer .footer-text {
  font-size:0.9rem;
  color:#aaa;
}
.site-footer .footer-brand {
  font-weight:600;
  background:linear-gradient(90deg,var(--orange),var(--secondary));
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
</style>

{{-- HERO --}}
<div class="hero">
  <h1>Semantic SEO Suite by Jagoowala</h1>
  <p>Supercharge your website ranking with AI‑powered Audit, Keyword Analysis & Content Optimization tools.</p>
  <a href="#features" class="btn btn-cta">Explore Tools</a>
</div>

{{-- FEATURE SECTION --}}
<section id="features" class="section container">
  <h2>🚀 Our Tools</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature">
        <div class="feature-icon">📝</div>
        <h4>SEO Audit</h4>
        <p>Detect technical SEO issues like broken links, metadata gaps, and performance bottlenecks.</p>
        <a href="{{ route('seo.audit.index') }}" class="btn btn-sm btn-outline-light mt-3">Run Audit</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature">
        <div class="feature-icon">🔑</div>
        <h4>Keyword Analyzer</h4>
        <p>Deep keyword analysis with semantic matches and competitor strengths.</p>
        <a href="{{ route('seo.keyword.index') }}" class="btn btn-sm btn-outline-light mt-3">Analyze</a>
      </div>
    </div>
    <div class="col-md-4">
      <div class="feature">
        <div class="feature-icon">⚡</div>
        <h4>Content Optimizer</h4>
        <p>AI recommendations to optimize metadata, headings, and semantic structure.</p>
        <a href="{{ route('seo.optimizer.index') }}" class="btn btn-sm btn-outline-light mt-3">Optimize</a>
      </div>
    </div>
  </div>
</section>

{{-- CTA --}}
<section class="section cta container">
  <h3>Ready to Climb Google Rankings?</h3>
  <p>Empower your website with Jagoowala’s advanced SEO tools — crafted for speed and precision.</p>
  <a href="#features" class="btn btn-cta">Get Started</a>
</section>

{{-- FOOTER --}}
<footer class="site-footer">
  <p class="footer-text">
    ♥️♥️ Crafted by <span class="footer-brand">Jagoowala</span> ♥️♥️ <br>
    © 2025 SEO Audit Tool • Crafted with ❤️ in Laravel + Bootstrap. <br>
    ♥️♥️ Crafted by <span class="footer-brand">Jagoowala</span> ♥️♥️
  </p>
</footer>
@endsection
