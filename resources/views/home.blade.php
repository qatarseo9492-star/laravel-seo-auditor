@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
/* ===== Global ===== */
body {
    background:#0b0b10;
    color:#eee;
    font-family:'Inter','Poppins',sans-serif;
}
:root {
    --purple:#9b59b6;
    --blue:#2196f3;
    --teal:#03dac6;
    --dark:#111119;
}

/* ===== Navbar ===== */
.navbar {
    background:rgba(15,15,25,.7);
    backdrop-filter:blur(12px);
    border-bottom:2px solid transparent;
    border-image:linear-gradient(90deg,var(--purple),var(--blue),var(--teal)) 1;
}
.navbar.scrolled{ background:rgba(10,10,15,0.95);}
.navbar-brand{
    font-weight:800;font-size:1.6rem;
    background:linear-gradient(90deg,var(--purple),var(--teal));
    -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.nav-link{color:#ccc!important;position:relative;margin:0 12px;}
.nav-link::after{content:'';position:absolute;bottom:-5px;left:50%;width:0;height:2px;
 background:linear-gradient(90deg,var(--purple),var(--blue));transition:.3s;}
.nav-link:hover{color:var(--purple)!important;}
.nav-link:hover::after{left:0;width:100%;}

/* ===== Hero ===== */
.hero{
  min-height:90vh;display:flex;flex-direction:column;text-align:center;
  align-items:center;justify-content:center;padding:0 20px;
  background:linear-gradient(135deg,#0f0f10,#151520);
}
.hero h1{
  font-size:3.5rem;font-weight:800;
  background:linear-gradient(90deg,var(--purple),var(--blue),var(--teal));
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.hero p{max-width:620px;color:#aaa;margin-top:20px;}
.btn-hero{
  margin-top:30px;padding:14px 36px;border-radius:40px;font-weight:600;
  background:linear-gradient(90deg,var(--purple),var(--blue));
  color:#fff;box-shadow:0 6px 14px rgba(155,89,182,.4);border:none;transition:.3s;
}
.btn-hero:hover{transform:translateY(-3px) scale(1.05);box-shadow:0 8px 20px rgba(33,150,243,.5);}

/* ===== Features Section ===== */
.features{padding:100px 0;background:var(--dark);}
.features h2{
   text-align:center;margin-bottom:60px;font-size:2.4rem;font-weight:700;
   background:linear-gradient(90deg,var(--purple),var(--teal));
   -webkit-background-clip:text;-webkit-text-fill-color:transparent;
}
.feature-box{
  border-radius:18px;padding:45px 30px;text-align:center;color:#fff;
  transition:transform .4s,box-shadow .4s;
}
.feature-icon{font-size:2.8rem;margin-bottom:20px;}
.feature-title{font-size:1.3rem;font-weight:600;margin-bottom:14px;}
.feature-text{color:#eee;font-size:.95rem;}

/* Unique colors inside each box */
.feature-box.audit{
  background:linear-gradient(160deg,#2e1437,#4a1f63);
}
.feature-box.keyword{
  background:linear-gradient(160deg,#0f2e45,#205a8d);
}
.feature-box.optimizer{
  background:linear-gradient(160deg,#0b3b33,#118472);
}

/* Hover Glow */
.feature-box:hover{transform:translateY(-10px) scale(1.02);}
.feature-box.audit:hover{box-shadow:0 15px 30px rgba(155,89,182,.6);}
.feature-box.keyword:hover{box-shadow:0 15px 30px rgba(33,150,243,.6);}
.feature-box.optimizer:hover{box-shadow:0 15px 30px rgba(3,218,198,.6);}

/* Buttons inside cards */
.btn-tool{
  margin-top:20px;border:none;padding:10px 26px;border-radius:25px;
  font-weight:600;transition:.3s;
}
.btn-audit{background:linear-gradient(90deg,#9b59b6,#bb86fc);color:#fff;}
.btn-audit:hover{box-shadow:0 0 18px rgba(155,89,182,.9);}
.btn-keyword{background:linear-gradient(90deg,#2196f3,#42a5f5);color:#fff;}
.btn-keyword:hover{box-shadow:0 0 18px rgba(33,150,243,.9);}
.btn-optimizer{background:linear-gradient(90deg,#03dac6,#1de9b6);color:#0b0b0b;}
.btn-optimizer:hover{box-shadow:0 0 18px rgba(3,218,198,.9);}

/* ===== CTA ===== */
.cta{
  max-width:950px;margin:90px auto;padding:80px 20px;text-align:center;
  border-radius:20px;color:#fff;
  background:linear-gradient(120deg,var(--purple),var(--blue),var(--teal));
  box-shadow:0 12px 30px rgba(0,0,0,0.6);
}
.cta h3{font-size:2rem;font-weight:700;}
.cta p{margin:15px auto 25px;max-width:600px;}
.cta .btn-light{padding:12px 32px;border-radius:30px;font-weight:600;}

/* ===== Footer ===== */
footer{background:#0a0a0f;padding:25px 0;text-align:center;font-size:.9rem;color:#888;}
footer a{color:var(--purple);text-decoration:none;}
</style>

{{-- HERO --}}
<div class="hero">
  <h1>⚡ Semantic SEO Checker</h1>
  <p>Advanced AI‑powered tools to audit, analyze & optimize your site with precision semantic insights.</p>
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
        <p class="feature-text">Deep scans reveal technical SEO issues, broken links and speed bottlenecks instantly.</p>
        <a href="{{ route('seo.audit.index') }}" class="btn btn-tool btn-audit">Run Audit</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="feature-box keyword h-100">
        <div class="feature-icon">🔑</div>
        <h4 class="feature-title">Keyword Analyzer</h4>
        <p class="feature-text">Discover semantic keyword opportunities & densities to outrank your competitors.</p>
        <a href="{{ route('seo.keyword.index') }}" class="btn btn-tool btn-keyword">Analyze</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="feature-box optimizer h-100">
        <div class="feature-icon">⚡</div>
        <h4 class="feature-title">Content Optimizer</h4>
        <p class="feature-text">AI recommendations refine headlines, metadata and content flow for higher visibility.</p>
        <a href="{{ route('seo.optimizer.index') }}" class="btn btn-tool btn-optimizer">Optimize</a>
      </div>
    </div>

  </div>
</div>

{{-- CTA --}}
<div class="cta">
  <h3>Ready to Elevate Your SEO?</h3>
  <p>Supercharge your rankings with cutting‑edge semantic analysis and optimization tools.</p>
  <a href="#features" class="btn btn-light">Get Started Now</a>
</div>

{{-- Footer --}}
<footer>
  <p>© {{ date('Y') }} Semantic SEO Checker · All Rights Reserved · <a href="#">Privacy Policy</a></p>
</footer>

<script>
window.addEventListener('scroll',()=>{document.querySelector('.navbar').classList.toggle('scrolled',window.scrollY>20);});
</script>
@endsection
