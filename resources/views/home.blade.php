@extends('layouts.app')

@section('title', 'Semantic SEO Checker - Home')

@section('content')
<style>
/* Global */
body {
  background: #0b0b10;
  color: #eee;
  font-family: 'Inter', 'Poppins', sans-serif;
  position: relative;
  overflow-x: hidden;
}

/* ======= CLOUD OVERLAY ======= */
.clouds {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  pointer-events: none;
  background: url('https://i.postimg.cc/65VQ6NLN/clouds.png'); /* transparent PNG clouds */
  background-repeat: repeat-x;
  background-size: cover;
  opacity: 0.15;
  animation: moveClouds 120s linear infinite;
  z-index: 2; /* sits above smoke/stars but below content */
}
@keyframes moveClouds {
  from {background-position: 0 0;}
  to {background-position: -2000px 0;}
}

/* ======= FIRE EFFECT NAVBAR ======= */
.navbar {
  background: rgba(15,15,25,0.7);
  backdrop-filter: blur(12px);
  position: relative;
  z-index: 5;
}
/* Fire glowing border at bottom */
.navbar::after {
  content: "";
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 4px;
  background: linear-gradient(90deg, #ff4500, #ffae00, #ff0000);
  background-size: 400% 100%;
  animation: fireFlow 5s linear infinite;
  filter: blur(2px);
}
@keyframes fireFlow {
  0% { background-position: 0% 50%; }
  100% { background-position: 400% 50%; }
}

/* ======= FIRE CURSOR TRAIL (optional if you want fire with mouse) ======= */
.cursor-fire {
  position: fixed;
  width: 20px; height: 20px;
  border-radius: 50%;
  background: radial-gradient(circle, rgba(255,140,0,0.9) 0%, rgba(255,69,0,0.5) 60%, transparent 80%);
  pointer-events: none;
  transform: translate(-50%, -50%);
  z-index: 10000;
  mix-blend-mode: screen;
  animation: flicker 0.3s infinite alternate;
}
@keyframes flicker {
  from { transform: translate(-50%, -50%) scale(0.8); opacity: 0.8; }
  to { transform: translate(-50%, -50%) scale(1.2); opacity: 1; }
}
/* ===================== HERO ===================== */
.hero {
  min-height: 90vh;
  display: flex; flex-direction: column; align-items: center; justify-content: center;
  text-align:center;
  position: relative; z-index: 3;
}
.hero h1 {
  font-size: 3.8rem; font-weight: 800;
  background:linear-gradient(90deg, #bb86fc, #2196f3, #03dac6);
  -webkit-background-clip:text;
  -webkit-text-fill-color:transparent;
}
.hero p { max-width:640px; color:#aaa; margin-top:20px; }
.btn-hero { margin-top:30px;padding:14px 38px;border-radius:40px;
  background:linear-gradient(90deg,#bb86fc,#03dac6); color:#fff;border:none;
  box-shadow:0 6px 16px rgba(3,218,198,.5); transition:.3s;}
.btn-hero:hover { transform:translateY(-3px) scale(1.05);box-shadow:0 8px 24px rgba(33,150,243,.6);}

/* Example Features (you already have styled ones) */
.features {background:#111119; padding:100px 0; position: relative; z-index: 3;}
.features h2 {text-align:center;margin-bottom:50px;font-size:2.2rem;background:linear-gradient(90deg,#bb86fc,#03dac6);
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;}
.feature-box {
  background:rgba(255,255,255,.05);padding:35px 20px;border-radius:20px;text-align:center;color:#fff;
  border:1px solid rgba(255,255,255,.1);transition:.3s;
}
.feature-box:hover{transform:translateY(-10px);box-shadow:0 10px 20px rgba(0,0,0,.6);}
</style>


{{-- CLOUD OVERLAY --}}
<div class="clouds"></div>

{{-- HERO --}}
<div class="hero">
  <h1>⚡ Semantic SEO Checker</h1>
  <p>AI‑powered tools to audit, analyze & optimize your site with style.</p>
  <a href="#features" class="btn-hero">Explore Tools</a>
</div>

{{-- FEATURES --}}
<div id="features" class="features container">
  <h2>⚡ Our SEO Tools</h2>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="feature-box">📝<h4>SEO Audit</h4><p>Scan your site for issues.</p></div>
    </div>
    <div class="col-md-4">
      <div class="feature-box">🔑<h4>Keyword Analyzer</h4><p>Analyze keyword density.</p></div>
    </div>
    <div class="col-md-4">
      <div class="feature-box">⚡<h4>Content Optimizer</h4><p>Optimize structure & metadata.</p></div>
    </div>
  </div>
</div>

{{-- FOOTER --}}
<footer style="background:#0a0a0f;padding:25px 0;text-align:center;">
  <p>© {{ date('Y') }} Semantic SEO Checker · All Rights Reserved</p>
</footer>

{{-- FIRE CURSOR SCRIPT (OPTIONAL) --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
   const fire = document.createElement("div");
   fire.classList.add("cursor-fire");
   document.body.appendChild(fire);

   document.addEventListener("mousemove", e => {
      fire.style.left = e.pageX+"px";
      fire.style.top = e.pageY+"px";
   });
});
</script>
@endsection
