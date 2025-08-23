{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Semantic SEO Master • Ultra Tech Global</title>

<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
:root{
  --bg:#08080f; --panel:#0f1022; --panel-2:#141433;
  --text:#f0effa; --text-dim:#b6b3d6;
  --primary:#9b5cff; --secondary:#ff2045; --accent:#3de2ff;
  --good:#16c172; --warn:#f59e0b; --bad:#ef4444;
  --radius:18px; --shadow:0 10px 40px rgba(0,0,0,.55);
}
body{
  margin:0; color:var(--text);
  font-family:Inter,system-ui,sans-serif;
  background: radial-gradient(1200px 700px at 0% -10%, #201046 0%, transparent 55%),
              radial-gradient(1100px 800px at 110% 0%, #1a0f2a 0%, transparent 50%),
              var(--bg);
  overflow-x:hidden;
}
.wrap{position:relative;z-index:3;max-width:1200px;margin:0 auto;padding:28px 5%}
header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 22px;border-bottom:1px solid #1e1a33;background:rgba(15,16,34,.35);backdrop-filter:blur(8px)}
.hero-heading{font-size:2.5rem;font-weight:1000;background:linear-gradient(90deg,#b892ff,#ff2045,#ff8a5b);-webkit-background-clip:text;-webkit-text-fill-color:transparent}

.analyzer{margin-top:24px;background:var(--panel);border-radius:22px;box-shadow:var(--shadow);padding:24px}

/* ---- Wheel ---- */
.score-container{ width: clamp(180px, 38vw, 260px); margin:auto }
.score-wheel{width:100%;height:auto}
.score-wheel circle{fill:none;stroke-width:16;stroke-linecap:round}
.score-wheel .bg{stroke:rgba(255,255,255,.12)}
.score-wheel .progress{stroke:url(#gradRed);stroke-dasharray:339;stroke-dashoffset:339;filter:drop-shadow(0 0 10px rgba(155,92,255,.35))}
.score-wheel .progress2{stroke:url(#gradSweep);stroke-width:16;stroke-linecap:round;fill:none;stroke-dasharray:339;stroke-dashoffset:339;opacity:.35}
.score-wheel .spark{stroke:#fff;stroke-width:2.2;stroke-dasharray:1 338;animation:orbit 3.6s linear infinite;filter:drop-shadow(0 0 6px rgba(255,255,255,.55))}
@keyframes orbit{to{stroke-dashoffset:-339}}
.score-text{
  font-size:clamp(1.6rem,6vw,3.2rem);
  font-weight:1000;fill:#fff;text-anchor:middle;dominant-baseline:middle;
}
.score-text.green{fill:#22c55e}
.score-text.orange{fill:#f59e0b}
.score-text.red{fill:#ef4444}

/* ---- Buttons ---- */
.btn{padding:.6rem 1rem;border-radius:14px;font-weight:800;cursor:pointer;transition:.2s}
.btn-danger{background:linear-gradient(135deg,#ff2045,#ff7a59);color:#fff;box-shadow:0 10px 26px rgba(255,32,69,.35)}
.btn-danger:hover{transform:translateY(-2px);box-shadow:0 14px 36px rgba(255,32,69,.45)}
.btn-neon{background:linear-gradient(135deg,#3de2ff,#9b5cff);color:#001018;box-shadow:0 10px 26px rgba(61,226,255,.35)}
.btn-neon:hover{transform:translateY(-2px);box-shadow:0 14px 36px rgba(61,226,255,.45)}
.btn-ghost{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.15);color:#fff}
.btn-ghost:hover{background:rgba(255,255,255,.08)}

/* ---- Chips ---- */
.badge{display:inline-flex;align-items:center;gap:.35rem;padding:.4rem .7rem;border-radius:999px;font-weight:800;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.14)}
.badge-green{background:rgba(22,193,114,.22);color:#22c55e}
.badge-orange{background:rgba(245,158,11,.22);color:#f59e0b}
.badge-red{background:rgba(239,68,68,.24);color:#ef4444}
</style>
</head>
<body>
<canvas id="smokeFX"></canvas>

<!-- gradients for score wheel -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <linearGradient id="gradGreen"><stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/></linearGradient>
    <linearGradient id="gradOrange"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/></linearGradient>
    <linearGradient id="gradRed"><stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/></linearGradient>
    <linearGradient id="gradSweep" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#6ee7ff"/><stop offset="50%" stop-color="#f472b6"/><stop offset="100%" stop-color="#22c55e"/>
      <animateTransform attributeName="gradientTransform" type="rotate" from="0 .5 .5" to="360 .5 .5" dur="14s" repeatCount="indefinite"/>
    </linearGradient>
  </defs>
</svg>

<div class="wrap">
  <header class="site">
    <div class="hero-heading">Semantic SEO Master Analyzer</div>
  </header>

  <section class="analyzer" id="analyzer">
    <div class="score-container">
      <svg class="score-wheel" viewBox="0 0 120 120">
        <circle class="bg" cx="60" cy="60" r="54"/>
        <circle class="progress2" cx="60" cy="60" r="54" transform="rotate(-90 60 60)"/>
        <circle class="progress" id="wheelProgress" cx="60" cy="60" r="54" transform="rotate(-90 60 60)"/>
        <circle class="spark" cx="60" cy="60" r="54" transform="rotate(-90 60 60)"/>
        <text x="60" y="65" class="score-text red" id="overallScore">0%</text>
      </svg>
    </div>

    <div style="margin-top:1rem;display:flex;gap:.6rem;flex-wrap:wrap">
      <span id="overallChip" class="badge badge-red"><i class="fa-solid fa-gauge"></i> Overall: <b id="overallScoreInline">0</b>/100</span>
      <span id="aiBadge" class="badge"><i class="fa-solid fa-user-pen"></i> Writer: <b>—</b></span>
      <span class="badge badge-green"><i class="fa-solid fa-user"></i> Human-like: <b id="humanPct">—</b>%</span>
      <span class="badge badge-orange"><i class="fa-solid fa-microchip"></i> AI-like: <b id="aiPct">—</b>%</span>
    </div>

    <form id="analyzeForm" style="margin-top:1rem" onsubmit="return false;">
      <input id="analyzeUrl" type="url" placeholder="Paste a URL e.g. https://example.com/page"
             style="width:100%;padding:1rem;border-radius:14px;border:1px solid #1b1b35;background:#0b0d21;color:#fff"/>
      <div style="display:flex;gap:.6rem;margin-top:.6rem">
        <button id="analyzeBtn" class="btn btn-danger" type="button"><i class="fa-solid fa-magnifying-glass"></i> Analyze</button>
        <button id="printChecklist" class="btn btn-neon" type="button"><i class="fa-solid fa-print"></i> Print</button>
        <button id="resetChecklist" class="btn btn-ghost" type="button"><i class="fa-solid fa-rotate"></i> Reset</button>
      </div>
      <div id="analyzeStatus" style="margin-top:.6rem;color:var(--text-dim)"></div>
    </form>
  </section>
</div>

<script>
const CIRC=339;
function paintWheel(v){
  const n=Math.max(0,Math.min(100,Number(v)||0));
  const off=CIRC - (n/100)*CIRC;
  const arc=document.getElementById('wheelProgress');
  const deco=document.querySelector('.progress2');
  const txt=document.getElementById('overallScore');
  arc.style.strokeDashoffset=off; deco.style.strokeDashoffset=off;
  let grad='url(#gradRed)', cls='red';
  if(n>=80){grad='url(#gradGreen)'; cls='green';}
  else if(n>60){grad='url(#gradOrange)'; cls='orange';}
  arc.setAttribute('stroke',grad);
  txt.textContent=Math.round(n)+'%';
  txt.classList.remove('green','orange','red'); txt.classList.add(cls);
  document.getElementById('overallScoreInline').textContent=Math.round(n);
  const chip=document.getElementById('overallChip');
  chip.className='badge badge-'+cls;
}
document.getElementById('analyzeBtn').addEventListener('click',()=>{ paintWheel(Math.random()*100); });
</script>
</body>
</html>
