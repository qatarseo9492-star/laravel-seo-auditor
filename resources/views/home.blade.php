{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Semantic SEO Master • Ultra Tech Global</title>

<!-- Favicons -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
:root{
  --bg:#08080f; --panel:#0f1022; --panel-2:#141433; --line:#1e1a33;
  --text:#f0effa; --text-dim:#b6b3d6; --text-muted:#9aa0c3;
  --primary:#9b5cff; --secondary:#ff2045; --accent:#3de2ff;
  --good:#16c172; --warn:#f59e0b; --bad:#ef4444;
  --radius:18px; --shadow:0 10px 40px rgba(0,0,0,.55);
  --container:1200px;
}
*{box-sizing:border-box} html,body{height:100%}
html{scroll-behavior:smooth}
body{
  margin:0; color:var(--text);
  font-family:Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto;
  background:
    radial-gradient(1200px 700px at 0% -10%, #201046 0%, transparent 55%),
    radial-gradient(1100px 800px at 110% 0%, #1a0f2a 0%, transparent 50%),
    var(--bg);
  overflow-x:hidden;
}

/* Background canvases */
#brainCanvas,#linesCanvas,#linesCanvas2{position:fixed;inset:0;z-index:0;pointer-events:none;opacity:.9}
#smokeFX{position:fixed;inset:0;z-index:1;pointer-events:none;filter:saturate(115%) contrast(105%)}

/* Content wrapper above effects */
.wrap{position:relative;z-index:3;max-width:var(--container);margin:0 auto;padding:28px 5%}

/* Header */
header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 22px;border-bottom:1px solid var(--line);backdrop-filter:saturate(140%) blur(10px);background:rgba(15,16,34,.35)}
.brand{display:flex;align-items:center;gap:1rem}
.brand-badge{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(155,92,255,.3),rgba(255,32,69,.25));border:1px solid rgba(255,255,255,.08);color:#ffd1dc}
.hero-heading{font-size:3.7rem;font-weight:1000;line-height:1.02;margin:.1rem 0;letter-spacing:.8px;background:linear-gradient(90deg,#b892ff,#ff2045 55%,#ff8a5b 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;text-shadow:0 0 28px rgba(155,92,255,.25)}

/* Language (minimal) */
.lang-dock{position:fixed;left:18px;top:50%;transform:translateY(-50%);z-index:70;display:flex;flex-direction:column;gap:.6rem}
.lang-btn{width:48px;height:48px;border-radius:12px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;display:grid;place-items:center;cursor:pointer;backdrop-filter:blur(6px)}
.lang-btn:hover{background:rgba(255,255,255,.1)}
.lang-panel{position:fixed;left:74px;top:50%;transform:translateY(-50%);z-index:70;display:none}
.lang-card{background:var(--panel-2);border:1px solid rgba(255,255,255,.12);border-radius:16px;box-shadow:var(--shadow);padding:10px 12px;min-width:240px}
.lang-item{padding:.45rem .55rem;border-radius:10px;display:flex;align-items:center;gap:.5rem;cursor:pointer}
.lang-item:hover{background:rgba(255,255,255,.06)}
.lang-flag{width:18px;height:14px;border-radius:2px;background:#888}

/* Buttons (special effects) */
.btn{--pad:.75rem 1.05rem;display:inline-flex;align-items:center;gap:.5rem;padding:var(--pad);border-radius:14px;border:1px solid transparent;cursor:pointer;font-weight:800;letter-spacing:.2px;transition:.18s transform,.25s box-shadow}
.btn-neon{background:linear-gradient(135deg,#3de2ff,#9b5cff);box-shadow:0 8px 26px rgba(61,226,255,.25);color:#001018}
.btn-neon:hover{transform:translateY(-2px) scale(1.02);box-shadow:0 12px 38px rgba(61,226,255,.36)}
.btn-neon:active{transform:translateY(0) scale(.99)}
.btn-ghost{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.16);color:#fff;box-shadow:0 4px 16px rgba(0,0,0,.25)}
.btn-ghost:hover{background:rgba(255,255,255,.08);transform:translateY(-2px);box-shadow:0 10px 30px rgba(0,0,0,.35)}
.btn-danger{background:linear-gradient(135deg,#ff2045,#ff7a59);color:#fff;box-shadow:0 8px 26px rgba(255,32,69,.25)}
.btn-danger:hover{transform:translateY(-2px) scale(1.02);box-shadow:0 12px 40px rgba(255,32,69,.35)}

/* Analyzer panel */
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem} .section-subtitle{margin:0;color:var(--text-dim)}

/* WHEEL – container + decorative layers */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.wheel-wrap{position:relative;width:260px;aspect-ratio:1/1;display:grid;place-items:center}
.wheel-rainbow{position:absolute;inset:12% 12%;border-radius:50%;filter:blur(14px) saturate(140%) contrast(110%);background:
  conic-gradient(from 0deg, #6ee7ff, #60a5fa, #a78bfa, #f472b6, #fb7185, #f59e0b, #22c55e, #6ee7ff);
  animation:spin 18s linear infinite}
@keyframes spin{to{transform:rotate(360deg)}}
.wheel{position:absolute;inset:0;padding:18px}
.wheel .track{stroke:rgba(255,255,255,.12);stroke-width:14;fill:none;filter:url(#innerShadow)}
.wheel .ticks{stroke:rgba(255,255,255,.16);stroke-width:2;fill:none;stroke-dasharray:1 7}
.wheel .halo{stroke:url(#halo);stroke-width:12;fill:none;opacity:.45;filter:url(#blurHalo)}
/* main progress (threshold color set from JS) */
.wheel .progress{stroke:url(#gradRed);stroke-width:16;stroke-linecap:round;fill:none;stroke-dasharray:339;stroke-dashoffset:339;filter:url(#glow)}
/* decorative rainbow arc that follows the same length */
.wheel .progress2{stroke:url(#gradSweep);stroke-width:16;stroke-linecap:round;fill:none;stroke-dasharray:339;stroke-dashoffset:339;opacity:.35;filter:url(#glow)}
/* tiny white spark orbiting */
.wheel .spark{stroke:#fff;stroke-width:2.2;fill:none;opacity:.9;stroke-dasharray:1 338;animation:orbit 3.6s linear infinite;filter:drop-shadow(0 0 6px rgba(255,255,255,.55))}
@keyframes orbit{to{stroke-dashoffset:-339}}
.wheel-center{position:absolute;width:42%;height:42%;border-radius:50%;background:radial-gradient(closest-side,rgba(255,255,255,.12),transparent 70%);filter:blur(8px)}
.wheel-score{position:absolute;font-size:3.2rem;font-weight:1000;text-shadow:0 0 18px rgba(255,255,255,.15)}
.wheel-score.green{color:#22c55e}
.wheel-score.orange{color:#f59e0b}
.wheel-score.red{color:#ef4444}
.wheel-label{position:absolute;bottom:12%;font-weight:800;color:var(--text-muted);letter-spacing:.4px}

/* Score chips */
.chip{padding:.28rem .65rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28)}
.badge{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:900}
.badge-green{background:rgba(34,197,94,.18);border-color:rgba(22,163,74,.4)}
.badge-orange{background:rgba(245,158,11,.18);border-color:rgba(245,158,11,.45)}
.badge-red{background:rgba(239,68,68,.20);border-color:rgba(185,28,28,.45)}

/* Writer badges */
.writer-badge{display:inline-flex;align-items:center;gap:.45rem;padding:.28rem .6rem;border-radius:12px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);font-weight:900}
.writer-badge .h-icon{color:#22c55e}
.writer-badge .a-icon{color:#fb7185}

/* URL input */
.analyze-form input[type="url"]{
  width:100%; padding:1rem 1.2rem; border-radius:14px; border:1px solid #1b1b35; background:#0b0d21; color:var(--text);
  box-shadow:0 0 0 0 rgba(155,92,255,.0); transition:.25s;
}
.analyze-form input[type="url"]:focus{ outline:none; border-color:#5942ff; box-shadow:0 0 0 6px rgba(155,92,255,.15); }
.analyze-row{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;margin-top:.5rem}

/* Progress */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
.progress-fill{height:100%;background:linear-gradient(135deg,#9b5cff,#ff2045);width:0%;transition:width .35s ease}
.progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}

/* Category grid + stylish checklist */
.analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
.category-card{
  position:relative;grid-column:span 6;background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.08);border-radius:18px;padding:16px;box-shadow:var(--shadow);
  overflow:hidden;isolation:isolate;
}
.category-card::before{
  content:"";position:absolute;inset:-2px;border-radius:18px;padding:2px;
  background:conic-gradient(from 180deg, rgba(61,226,255,.35), rgba(155,92,255,.35), rgba(255,182,72,.30), rgba(255,32,69,.30), rgba(34,197,94,.30), rgba(61,226,255,.35));
  -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
  -webkit-mask-composite:xor;mask-composite:exclude;animation:borderGlow 7s linear infinite; pointer-events:none;
}
@keyframes borderGlow{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(360deg)}}
.category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
.category-icon{width:46px;height:46px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3de2ff33,#9b5cff33);color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,.18)}
.category-title{margin:0;font-size:1.08rem;background:linear-gradient(90deg,#3de2ff,#9b5cff,#ff2045);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900}
.category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}
.checklist{list-style:none;margin:10px 0 0;padding:0}
.checklist-item{--accent: rgba(255,255,255,.12);position:relative;display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.75rem .8rem .75rem 1rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);background:linear-gradient(180deg,rgba(255,255,255,.035),rgba(255,255,255,.03));overflow:hidden}
.checklist-item + .checklist-item{margin-top:.28rem}
.checklist-item::after{content:""; position:absolute; left:0; top:0; bottom:0; width:6px; background:var(--accent); box-shadow:0 0 20px var(--accent); transition:.25s}
.checklist-item:hover{transform:translateY(-2px);box-shadow:0 10px 34px rgba(0,0,0,.28);border-color:rgba(255,255,255,.16)}
.checklist-item label { cursor:pointer; display:inline-flex; align-items:center; gap:.6rem; }
.checklist-item .autoPulse{ animation:selPulse .8s ease; }
@keyframes selPulse{0%{box-shadow:0 0 0 0 rgba(34,197,94,.0)}70%{box-shadow:0 0 0 12px rgba(34,197,94,.18)}100%{box-shadow:0 0 0 0 rgba(34,197,94,.0)}}

/* Toggle switch */
.checklist-item input[type="checkbox"]{appearance:none;width:42px;height:24px;border-radius:999px;background:#2a2a46;border:1px solid rgba(255,255,255,.18);position:relative;cursor:pointer;outline:none;transition:.2s}
.checklist-item input[type="checkbox"]::after{content:"";position:absolute;width:18px;height:18px;border-radius:50%;background:#cfd3f6;top:2.5px;left:2.5px;transition:.2s;box-shadow:0 2px 10px rgba(0,0,0,.3)}
.checklist-item input[type="checkbox"]:checked{background:linear-gradient(135deg,#3de2ff,#9b5cff)}
.checklist-item input[type="checkbox"]:checked::after{left:21px;background:#0a1222}

/* Score pills */
.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22); border-color:rgba(22,193,114,.45)}
.score-mid{ background:rgba(245,158,11,.22); border-color:rgba(245,158,11,.45)}
.score-bad{ background:rgba(239,68,68,.24); border-color:rgba(239,68,68,.5)}

.improve-btn{padding:.4rem .75rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);font-weight:900;cursor:pointer}
.improve-btn:hover{background:rgba(255,255,255,.1)}

/* Modal */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);display:none;z-index:9000}
.modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9010}
.modal-card{width:min(1000px,96vw);background:var(--panel-2);border:1px solid rgba(255,255,255,.12);border-radius:16px;box-shadow:var(--shadow);padding:16px}
.modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem}
.modal-title{margin:0;font-size:1.2rem}
.modal-close{background:transparent;border:1px solid rgba(255,255,255,.2);border-radius:10px;color:#fff;padding:.35rem .6rem;cursor:pointer}
.tabs{display:flex;gap:.4rem;margin:.4rem 0;flex-wrap:wrap}
.tab{padding:.35rem .7rem;border-radius:10px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);cursor:pointer;font-weight:800}
.tab.active{background:linear-gradient(135deg,#3de2ff22,#9b5cff22);border-color:#3de2ff66}
.tabpanes > div{display:none}
.tabpanes > div.active{display:block}
.pre{white-space:pre-wrap;background:#0b0d21;border:1px solid #1b1b35;border-radius:12px;padding:12px;color:#cfd3f6;max-height:60vh;overflow:auto}

/* Footer + back to top */
footer.site{ margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)}
.footer-brand{display:flex;align-items:center;gap:.6rem}
.footer-brand .dot{width:8px;height:8px;border-radius:50%;background:linear-gradient(135deg,#3de2ff,#9b5cff)}
.footer-links a{color:var(--text-dim);margin-left:.9rem}
.footer-links a:hover{color:#fff;text-decoration:underline}
#backTop{position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none}
#backTop:hover{background:rgba(255,255,255,.12)}

@media (max-width:992px){
  .category-card{grid-column:span 12}
  .hero-heading{font-size:2.7rem}
  .wheel-wrap{width:220px}
  footer.site{flex-direction:column;align-items:flex-start}
}
@media print{#linesCanvas,#linesCanvas2,#brainCanvas,#smokeFX,.modal-backdrop,.modal,header.site,#backTop,.lang-dock,.lang-panel{display:none!important}}
</style>
</head>
<body>
<canvas id="brainCanvas"></canvas>
<canvas id="linesCanvas"></canvas>
<canvas id="linesCanvas2"></canvas>
<canvas id="smokeFX" aria-hidden="true"></canvas>

<!-- hidden defs for wheel gradients/filters -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <!-- thresholds -->
    <linearGradient id="gradGreen" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/>
    </linearGradient>
    <linearGradient id="gradOrange" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/>
    </linearGradient>
    <linearGradient id="gradRed" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/>
    </linearGradient>

    <!-- rotating sweep -->
    <linearGradient id="gradSweep" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%"   stop-color="#6ee7ff"/>
      <stop offset="16%"  stop-color="#60a5fa"/>
      <stop offset="33%"  stop-color="#a78bfa"/>
      <stop offset="50%"  stop-color="#f472b6"/>
      <stop offset="66%"  stop-color="#fb7185"/>
      <stop offset="82%"  stop-color="#f59e0b"/>
      <stop offset="100%" stop-color="#22c55e"/>
      <animateTransform attributeName="gradientTransform" type="rotate" from="0 0.5 0.5" to="360 0.5 0.5" dur="14s" repeatCount="indefinite"/>
    </linearGradient>

    <!-- halo + filters -->
    <radialGradient id="halo" r="65%">
      <stop offset="60%" stop-color="rgba(255,255,255,0)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,.12)"/>
    </radialGradient>
    <filter id="glow"><feGaussianBlur stdDeviation="2.2" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
    <filter id="innerShadow"><feOffset dy="1"/><feGaussianBlur stdDeviation="1.5" result="o"/><feComposite in="o" in2="SourceAlpha" operator="arithmetic" k2="-1" k3="1"/><feColorMatrix type="matrix" values="0 0 0 0 0  0 0 0 0 0  0 0 0 0 0  0 0 0 .45 0"/><feComposite in2="SourceGraphic" operator="over"/></filter>
    <filter id="blurHalo"><feGaussianBlur stdDeviation="6"/></filter>
  </defs>
</svg>

<!-- Language Dock -->
<div class="lang-dock">
  <button class="lang-btn" id="langOpen" title="Language"><i class="fa-solid fa-globe"></i></button>
</div>
<div class="lang-panel" id="langPanel"><div class="lang-card" id="langCard"></div></div>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
      <div><div class="hero-heading" data-i="title">Semantic SEO Master Analyzer</div></div>
    </div>
    <div style="display:flex;gap:.5rem">
      <button class="btn btn-ghost" id="printTop"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
    </div>
  </header>

  <section class="analyzer" id="analyzer">
    <h2 class="section-title" data-i="analyze_title">Analyze a URL</h2>
    <p class="section-subtitle" data-i="legend_line">
      The wheel fills with your overall score.
      <span class="badge badge-green">Green ≥ 80</span>
      <span class="badge badge-orange">Orange 61–79</span>
      <span class="badge badge-red">Red ≤ 60</span>
    </p>

    <div class="score-area">
      <!-- WHEEL -->
      <div class="wheel-wrap">
        <div class="wheel-rainbow" aria-hidden="true"></div>
        <svg class="wheel" viewBox="0 0 120 120" role="img" aria-label="Overall score">
          <circle class="halo" cx="60" cy="60" r="54" stroke="url(#halo)"></circle>
          <circle class="ticks" cx="60" cy="60" r="54" transform="rotate(-90 60 60)"/>
          <circle class="track" cx="60" cy="60" r="54"/>
          <circle class="progress2" cx="60" cy="60" r="54" transform="rotate(-90 60 60)"/>
          <circle class="progress" id="wheelProgress" cx="60" cy="60" r="54" transform="rotate(-90 60 60)"/>
          <circle class="spark" cx="60" cy="60" r="54" transform="rotate(-90 60 60)"/>
        </svg>
        <div class="wheel-center" aria-hidden="true"></div>
        <div id="overallScore" class="wheel-score red">0%</div>
        <div class="wheel-label">Overall</div>
      </div>

      <!-- Right side chips -->
      <div style="display:flex;flex-direction:column;gap:.6rem">
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;align-items:center">
          <span id="overallChip" class="badge badge-red"><i class="fa-solid fa-gauge-high"></i> Score: <b id="overallScoreInline">0</b>/100</span>
          <span id="writerBadge" class="writer-badge">
            <i class="fa-solid fa-user h-icon"></i> <span id="humanPct">—</span>% &nbsp;&nbsp;
            <i class="fa-solid fa-robot a-icon"></i> <span id="aiPct">—</span>% &nbsp;&nbsp;
            <span id="aiLabel">Writer: —</span>
          </span>
          <button id="viewAIText" class="btn btn-neon" style="--pad:.5rem .8rem"><i class="fa-solid fa-magnifying-glass-chart"></i> Evidence</button>
        </div>

        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <button id="viewHumanBtn" class="btn btn-ghost" style="--pad:.4rem .7rem"><i class="fa-solid fa-user"></i> Human‑like</button>
          <button id="viewAIBtn" class="btn btn-ghost" style="--pad:.4rem .7rem"><i class="fa-solid fa-microchip"></i> AI‑like</button>
        </div>
      </div>
    </div>

    <div class="analyze-box" style="margin-top:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px">
      <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
        <label for="analyzeUrl" style="display:block;font-weight:800;margin-bottom:.35rem" data-i="page_url">Page URL</label>
        <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" />
        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> <span data-i="auto_check">Auto‑apply checkmarks (≥ 80)</span>
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-danger" type="button"><i class="fa-solid fa-magnifying-glass"></i> <span data-i="analyze">Analyze</span></button>
          <button class="btn btn-neon" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
          <button class="btn btn-ghost" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> <span data-i="reset">Reset</span></button>
        </div>
        <div id="analyzeStatus" style="margin-top:.4rem;color:var(--text-dim)"></div>
      </form>

      <div id="analyzeReport" style="margin-top:.9rem;display:none">
        <div style="display:flex;flex-wrap:wrap;gap:.5rem">
          <span class="chip">HTTP: <b id="rStatus">—</b></span>
          <span class="chip">Title: <b id="rTitleLen">—</b></span>
          <span class="chip">Meta desc: <b id="rMetaLen">—</b></span>
          <span class="chip">Canonical: <b id="rCanonical">—</b></span>
          <span class="chip">Robots: <b id="rRobots">—</b></span>
          <span class="chip">Viewport: <b id="rViewport">—</b></span>
          <span class="chip">H1/H2/H3: <b id="rHeadings">—</b></span>
          <span class="chip">Internal links: <b id="rInternal">—</b></span>
          <span class="chip">Schema: <b id="rSchema">—</b></span>
          <span class="chip">Auto‑checked: <b id="rAutoCount">—</b></span>
        </div>
      </div>
    </div>

    <div class="progress-wrap">
      <div class="progress-bar"><div class="progress-fill" id="progressBar"></div></div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    <!-- Checklist -->
    <div class="analyzer-grid">
      @php $labels = [
        1=>'Define search intent & primary topic',
        2=>'Map target & related keywords (synonyms/PAA)',
        3=>'H1 includes primary topic naturally',
        4=>'Integrate FAQs / questions with answers',
        5=>'Readable, NLP‑friendly language',
        6=>'Title tag (≈50–60 chars) w/ primary keyword',
        7=>'Meta description (≈140–160 chars) + CTA',
        8=>'Canonical tag set correctly',
        9=>'Indexable & listed in XML sitemap',
        10=>'E‑E‑A‑T signals (author, date, expertise)',
        11=>'Unique value vs. top competitors',
        12=>'Facts & citations up to date',
        13=>'Helpful media (images/video) w/ captions',
        14=>'Logical H2/H3 headings & topic clusters',
        15=>'Internal links to hub/related pages',
        16=>'Clean, descriptive URL slug',
        17=>'Breadcrumbs enabled (+ schema)',
        18=>'Mobile‑friendly, responsive layout',
        19=>'Optimized speed (compression, lazy‑load)',
        20=>'Core Web Vitals passing (LCP/INP/CLS)',
        21=>'Clear CTAs and next steps',
        22=>'Primary entity clearly defined',
        23=>'Related entities covered with context',
        24=>'Valid schema markup (Article/FAQ/Product)',
        25=>'sameAs/Organization details present'
      ]; @endphp

      @foreach ([
        ['Content & Keywords',1,5,'fa-pen-nib'],
        ['Technical Elements',6,9,'fa-code'],
        ['Content Quality',10,13,'fa-star'],
        ['Structure & Architecture',14,17,'fa-sitemap'],
        ['User Signals & Experience',18,21,'fa-user-check'],
        ['Entities & Context',22,25,'fa-database'],
      ] as $c)
        <article class="category-card">
          <header class="category-head">
            <span class="category-icon"><i class="fas {{ $c[3] }}"></i></span>
            <div>
              <h3 class="category-title">{{ $c[0] }}</h3>
              <p class="category-sub">—</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">{{ $c[2]-$c[1]+1 }}</span></span>
          </header>
          <ul class="checklist">
            @for($i=$c[1];$i<=$c[2];$i++)
              <li class="checklist-item" id="row-{{ $i }}" data-score="">
                <label>
                  <input type="checkbox" id="ck-{{ $i }}">
                  <span>{{ $labels[$i] }}</span>
                </label>
                <span class="score-badge" id="sc-{{ $i }}">—</span>
                <button class="improve-btn" type="button" data-id="ck-{{ $i }}">Improve</button>
              </li>
            @endfor
          </ul>
        </article>
      @endforeach
    </div>
  </section>
</div>

<footer class="site">
  <div class="footer-brand"><span class="dot"></span><strong>Semantic SEO Master</strong></div>
  <div class="footer-links">
    <a href="#analyzer">Analyzer</a>
    <a href="#" id="toTopLink">Back to top</a>
  </div>
  <div class="footer-links">
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
  </div>
</footer>

<button id="backTop" title="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

<!-- Modal -->
<div class="modal-backdrop" id="modalBackdrop"></div>
<div class="modal" id="tipModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle">Improve</h3>
      <button class="modal-close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="tabs">
      <button class="tab active" data-tab="tipsTab"><i class="fa-solid fa-lightbulb"></i> Tips</button>
      <button class="tab" data-tab="examplesTab"><i class="fa-brands fa-google"></i> Examples</button>
      <button class="tab" data-tab="humanTab"><i class="fa-solid fa-user"></i> Human‑like</button>
      <button class="tab" data-tab="aiTab"><i class="fa-solid fa-microchip"></i> AI‑like</button>
      <button class="tab" data-tab="fullTab"><i class="fa-solid fa-file-lines"></i> Full Text</button>
    </div>
    <div class="tabpanes">
      <div id="tipsTab" class="active"><ul id="modalList"></ul></div>
      <div id="examplesTab"><div class="pre" id="examplesPre">—</div></div>
      <div id="humanTab"><div class="pre" id="humanSnippetsPre">Run Analyze to view human‑like snippets.</div></div>
      <div id="aiTab"><div class="pre" id="aiSnippetsPre">Run Analyze to view AI‑like snippets.</div></div>
      <div id="fullTab"><div class="pre" id="fullTextPre">Run Analyze to load full text.</div></div>
    </div>
  </div>
</div>

<script>
/* ---- Simple i18n (en only here) ---- */
const I18N = { en:{title:"Semantic SEO Master Analyzer", analyze_title:"Analyze a URL", legend_line:"The wheel fills with your overall score. <span class='badge badge-green'>Green ≥ 80</span> <span class='badge badge-orange'>Orange 61–79</span> <span class='badge badge-red'>Red ≤ 60</span>", overall:"Overall", page_url:"Page URL", analyze:"Analyze", print:"Print", reset:"Reset", auto_check:"Auto‑apply checkmarks (≥ 80)"} };
const LANGS = [["en","English"]];
(function(){
  const dockBtn=document.getElementById('langOpen'), panel=document.getElementById('langPanel'), card=document.getElementById('langCard');
  function fill(){ card.innerHTML=''; LANGS.forEach(([c,l])=>{ const d=document.createElement('div'); d.className='lang-item'; d.dataset.code=c; d.innerHTML=`<span class="lang-flag"></span><strong>${l}</strong>`; card.appendChild(d); }); }
  function apply(code){ const d=I18N[code]||I18N.en; document.querySelector('[data-i="title"]').textContent=d.title; document.querySelector('[data-i="analyze_title"]').textContent=d.analyze_title; document.querySelector('[data-i="legend_line"]').innerHTML=d.legend_line; document.querySelector('[data-i="page_url"]').textContent=d.page_url; document.querySelectorAll('[data-i="analyze"]').forEach(n=>n.textContent=d.analyze); document.querySelectorAll('[data-i="print"]').forEach(n=>n.textContent=d.print); document.querySelectorAll('[data-i="reset"]').forEach(n=>n.textContent=d.reset); document.querySelectorAll('[data-i="auto_check"]').forEach(n=>n.textContent=d.auto_check); }
  dockBtn.addEventListener('click', ()=> panel.style.display = panel.style.display==='block' ? 'none' : 'block');
  panel.addEventListener('click', e=>{ const it=e.target.closest('.lang-item'); if(!it) return; apply(it.dataset.code); panel.style.display='none'; });
  fill(); apply('en');
})();

/* ---- Dancing lines background ---- */
(function(){
  function layer(id,count,maxDist,colorFn,vel=1){
    const c=document.getElementById(id),ctx=c.getContext('2d'); let w,h,nodes=[],mouse={x:-9999,y:-9999};
    function resize(){ w=c.width=innerWidth; h=c.height=innerHeight; nodes=Array.from({length:count},()=>({x:Math.random()*w,y:Math.random()*h,vx:(Math.random()-.5)*vel,vy:(Math.random()-.5)*vel})) }
    addEventListener('resize',resize,{passive:true}); resize();
    addEventListener('mousemove', e=>{mouse.x=e.clientX;mouse.y=e.clientY},{passive:true});
    (function loop(){
      ctx.clearRect(0,0,w,h);
      for(const n of nodes){
        const dx=mouse.x-n.x, dy=mouse.y-n.y, d=Math.hypot(dx,dy), a=d<maxDist?(1-d/maxDist)*.9:0;
        n.vx+=(dx/d||0)*a*.18; n.vy+=(dy/d||0)*a*.18; n.vx*=.97; n.vy*=.97; n.x+=n.vx; n.y+=n.vy;
        if(n.x<0||n.x>w) n.vx*=-1; if(n.y<0||n.y>h) n.vy*=-1;
      }
      for(let i=0;i<nodes.length;i++) for(let j=i+1;j<nodes.length;j++){
        const A=nodes[i],B=nodes[j], d=Math.hypot(A.x-B.x,A.y-B.y);
        if(d<maxDist){ const al=(1-d/maxDist)*.65; ctx.strokeStyle=colorFn(al); ctx.lineWidth=1; ctx.beginPath(); ctx.moveTo(A.x,A.y); ctx.lineTo(B.x,B.y); ctx.stroke(); }
      }
      requestAnimationFrame(loop);
    })();
  }
  layer('linesCanvas',140,130,a=>`rgba(61,226,255,${a})`,1.1);
  layer('linesCanvas2',110,120,a=>`rgba(255,32,69,${a*0.6})`,0.9);
})();

/* ---- Procedural multi‑color smoke (WebGL2) ---- */
(function(){
  const canvas=document.getElementById('smokeFX'); if(!canvas) return;
  let gl; try{ gl=canvas.getContext('webgl2',{alpha:true,antialias:false,depth:false,stencil:false}); }catch(e){}
  const dpr=Math.min(2,devicePixelRatio||1); function resize(){ canvas.width=innerWidth*dpr; canvas.height=innerHeight*dpr; canvas.style.width=innerWidth+'px'; canvas.style.height=innerHeight+'px'; if(gl) gl.viewport(0,0,canvas.width,canvas.height); } addEventListener('resize',resize,{passive:true}); resize();
  if(!gl) return;
  const vs=`#version 300 es
  precision highp float; const vec2 V[3]=vec2[3](vec2(-1.,-1.),vec2(3.,-1.),vec2(-1.,3.));
  out vec2 uv; void main(){ vec2 p=V[gl_VertexID]; uv=.5*(p+1.); gl_Position=vec4(p,0,1); }`;
  const fs=`#version 300 es
  precision highp float; in vec2 uv; out vec4 o; uniform float t;
  float h(vec2 p){ return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453); }
  float n(vec2 p){ vec2 i=floor(p), f=fract(p); float a=h(i), b=h(i+vec2(1,0)), c=h(i+vec2(0,1)), d=h(i+vec2(1,1));
    vec2 u=f*f*(3.-2.*f); return mix(a,b,u.x)+(c-a)*u.y*(1.-u.x)+(d-b)*u.x*u.y; }
  float f(vec2 p){ float v=0., s=.5; mat2 m=mat2(1.6,1.2,-1.2,1.6); for(int i=0;i<5;i++){ v+=s*n(p); p=m*p; s*=.5; } return v; }
  void main(){
    vec2 p=(uv-vec2(1.,1.)) * vec2(2.2, 2.0);
    p.x += t*.15; p.y += t*.10;
    float q=f(p*1.6);
    float d=smoothstep(.30,.95,q);
    vec3 base=mix(vec3(.24,.88,1.),vec3(.96,.31,.41),uv.x);
    vec3 c=mix(base, vec3(1.,.72,.28), 0.35*uv.y);
    o=vec4(c*d, .72*d);
  }`;
  function sh(src,type){const s=gl.createShader(type); gl.shaderSource(s,src); gl.compileShader(s); return s;}
  const prog=gl.createProgram(); gl.attachShader(prog,sh(vs,gl.VERTEX_SHADER)); gl.attachShader(prog,sh(fs,gl_FRAGMENT_SHADER||35632)); gl.linkProgram(prog);
  const ut=gl.getUniformLocation(prog,'t');
  (function loop(t){ requestAnimationFrame(loop); gl.useProgram(prog); gl.uniform1f(ut,t*1e-3); gl.drawArrays(gl.TRIANGLES,0,3); })(performance.now());
})();

/* ---- Back to Top ---- */
(function(){
  const btn=document.getElementById('backTop'), link=document.getElementById('toTopLink');
  function onScroll(){ btn.style.display = window.scrollY>300 ? 'grid' : 'none'; }
  addEventListener('scroll', onScroll, {passive:true}); onScroll();
  const goTop = e => { e && e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); };
  btn.addEventListener('click', goTop); if(link) link.addEventListener('click', goTop);
})();

/* ---- WHEEL paint (threshold colors + decorative sweep sync) ---- */
const CIRC=339;
function paintWheel(v){
  const n=Math.max(0,Math.min(100,Number(v)||0));
  const off=CIRC - (n/100)*CIRC;

  const arc  = document.getElementById('wheelProgress');
  const deco = document.querySelector('.wheel .progress2');
  const txt  = document.getElementById('overallScore');

  arc.style.strokeDashoffset  = off;
  if (deco) deco.style.strokeDashoffset = off;

  let grad='url(#gradRed)', cls='red';
  if(n>=80){ grad='url(#gradGreen)'; cls='green'; }
  else if(n>60){ grad='url(#gradOrange)'; cls='orange'; }
  arc.setAttribute('stroke',grad);

  txt.textContent=Math.round(n)+'%';
  txt.classList.remove('green','orange','red'); txt.classList.add(cls);

  const chip=document.getElementById('overallChip');
  chip.classList.remove('badge-green','badge-orange','badge-red');
  chip.classList.add(n>=80?'badge-green':(n>60?'badge-orange':'badge-red'));

  document.getElementById('overallScoreInline').textContent=Math.round(n);
}
// keep backward compatibility if old code calls setScoreWheel
const setScoreWheel = paintWheel;

/* ---- Checklist storage & UI sync ---- */
(function(){
  const STORAGE_KEY='semanticSeoChecklistV7';
  const total=25;
  const boxes=()=>Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
  const bar=document.getElementById('progressBar');
  const caption=document.getElementById('progressCaption');

  function setRowAccent(rowEl, score){
    if(!rowEl) return;
    let col='rgba(255,255,255,.12)';
    if(typeof score==='number'){
      if(score>=80) col='rgba(34,197,94,.6)';
      else if(score>60) col='rgba(245,158,11,.6)';
      else col='rgba(239,68,68,.6)';
      rowEl.dataset.score=String(score);
    } else {
      rowEl.dataset.score='';
    }
    rowEl.style.setProperty('--accent', col);
  }

  function updateCats(){
    document.querySelectorAll('.category-card').forEach(card=>{
      const all=card.querySelectorAll('input[type="checkbox"]');
      const done=card.querySelectorAll('input[type="checkbox"]:checked');
      card.querySelector('.checked-count').textContent=done.length;
      card.querySelector('.total-count').textContent=all.length;
    });
  }
  function update(){
    const checked=boxes().filter(cb=>cb.checked).length;
    bar.style.width=((checked/total)*100)+'%';
    caption.textContent=`${checked} of ${total} items completed`;
    updateCats();
  }
  function load(){
    try{const saved=JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); boxes().forEach(cb=>cb.checked=saved.includes(cb.id));}catch(e){}
    update();
  }
  function save(){
    const ids=boxes().filter(cb=>cb.checked).map(cb=>cb.id);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
  }
  document.addEventListener('change', e=>{ if(e.target.matches('#analyzer input[type="checkbox"]')){ update(); save(); }});
  document.getElementById('resetChecklist').addEventListener('click', ()=>{
    if(!confirm('Reset the checklist?')) return;
    localStorage.removeItem(STORAGE_KEY);
    boxes().forEach(cb=>cb.checked=false);
    for(let i=1;i<=25;i++){ setScoreBadge(i,null); setRowAccent(document.getElementById('row-'+i), null); }
    paintWheel(0); update();
  });
  document.getElementById('printChecklist').addEventListener('click', ()=>window.print());
  document.getElementById('printTop').addEventListener('click', ()=>window.print());

  // Expose helpers
  window.setScoreBadge=(num,score)=>{
    const pill=document.getElementById('sc-'+num), row=document.getElementById('row-'+num);
    if(!pill) return;
    pill.className='score-badge'; // reset
    if(score==null || Number.isNaN(score)){ pill.textContent='—'; setRowAccent(row,null); return; }
    const s=Math.round(score);
    pill.textContent=s;
    if(s>=80) pill.classList.add('score-good'); else if(s>60) pill.classList.add('score-mid'); else pill.classList.add('score-bad');
    setRowAccent(row,s);
  };

  load();
})();

/* ---- Modal ---- */
(function(){
  const $=s=>document.querySelector(s), $$=s=>Array.from(document.querySelectorAll(s));
  const backdrop=$('#modalBackdrop'), modal=$('#tipModal'), closeBtn=$('#modalClose');
  const panes={ tipsTab:$('#tipsTab'), examplesTab:$('#examplesTab'), humanTab:$('#humanTab'), aiTab:$('#aiTab'), fullTab:$('#fullTab') };
  const tabs=$$('.tab');
  function openM(){backdrop.style.display='block'; modal.style.display='flex';}
  function closeM(){backdrop.style.display='none'; modal.style.display='none';}
  closeBtn.addEventListener('click',closeM); backdrop.addEventListener('click',closeM); document.addEventListener('keydown',e=>{if(e.key==='Escape') closeM();});
  tabs.forEach(t=>t.addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); Object.values(panes).forEach(p=>p.classList.remove('active')); t.classList.add('active'); panes[t.dataset.tab].classList.add('active'); }));
  document.addEventListener('click', e=>{
    const btn=e.target.closest('.improve-btn'); if(!btn) return;
    const id=btn.dataset.id; const label=(document.getElementById(id)?.parentElement?.querySelector('span')?.textContent||'Improve').trim();
    document.getElementById('modalTitle').textContent='Improve: '+label;
    const tipsList=document.getElementById('modalList');
    const tips=(window.__lastSuggestions && Array.isArray(window.__lastSuggestions[id])) ? window.__lastSuggestions[id] : ['Run Analyze to generate contextual suggestions.'];
    tipsList.innerHTML=''; tips.forEach(t=>{const li=document.createElement('li'); li.textContent=t; tipsList.appendChild(li);});
    tabs.forEach(x=>x.classList.remove('active')); panes.tipsTab.classList.add('active'); document.querySelector('[data-tab="tipsTab"]').classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.tipsTab.classList.add('active');
    openM();
  });
  document.getElementById('viewAIText').addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="aiTab"]').classList.add('active'); Object.values(panes).forEach(p=>p.classList.remove('active')); panes.aiTab.classList.add('active'); openM();});
  document.getElementById('viewHumanBtn').addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="humanTab"]').classList.add('active'); Object.values(panes).forEach(p=>p.classList.remove('active')); panes.humanTab.classList.add('active'); openM();});
  document.getElementById('viewAIBtn').addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="aiTab"]').classList.add('active'); Object.values(panes).forEach(p=>p.classList.remove('active')); panes.aiTab.classList.add('active'); openM();});
  window.__setAIData=function(ai){
    document.getElementById('aiSnippetsPre').textContent=(ai?.ai_sentences||[]).join('\n\n')||'No AI‑like snippets detected.';
    document.getElementById('humanSnippetsPre').textContent=(ai?.human_sentences||[]).join('\n\n')||'No human‑like snippets isolated.';
    document.getElementById('fullTextPre').textContent=ai?.full_text||'No text captured.';
    const humanPct=typeof ai?.human_pct==='number'?ai.human_pct:'—';
    const aiPct=typeof ai?.ai_pct==='number'?ai.ai_pct:'—';
    document.getElementById('humanPct').textContent=humanPct;
    document.getElementById('aiPct').textContent=aiPct;
  }
})();

/* ---- Analyze: wire scores to UI & auto-select ≥80 ---- */
function normalizeUrl(u){ if(!u) return ''; u=u.trim(); if(!/^https?:\/\//i.test(u)) u='https://'+u.replace(/^\/+/, ''); try{ new URL(u);}catch(e){} return u; }

(function(){
  const $=s=>document.querySelector(s);
  function setChecked(id,on){ const el=document.getElementById(id); if(el){ el.checked=!!on; el.classList.toggle('autoPulse', !!on); } }

  document.getElementById('analyzeForm').addEventListener('submit', e=>{ e.preventDefault(); document.getElementById('analyzeBtn').click(); });
  document.getElementById('analyzeBtn').addEventListener('click', analyze);

  async function analyze(){
    const url=normalizeUrl($('#analyzeUrl').value);
    const status=$('#analyzeStatus'), btn=$('#analyzeBtn'), report=$('#analyzeReport');
    if(!url){ status.textContent='Please enter a URL.'; return; }
    status.textContent='Analyzing…'; btn.disabled=true; btn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Analyzing';

    try{
      const resp=await fetch('{{ route('analyze.json') }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body:JSON.stringify({url})
      });
      const data=await resp.json();
      if(!data.ok) throw new Error(data.error||'Failed');

      // Meta chips
      $('#rStatus').textContent=data.status;
      $('#rTitleLen').textContent=(data.title||'').length;
      $('#rMetaLen').textContent=data.meta_description_len;
      $('#rCanonical').textContent=data.canonical?'Yes':'No';
      $('#rRobots').textContent=data.robots||'—';
      $('#rViewport').textContent=data.viewport?'Yes':'No';
      $('#rHeadings').textContent=`${data.counts.h1}/${data.counts.h2}/${data.counts.h3}`;
      $('#rInternal').textContent=data.counts.internal_links;
      $('#rSchema').textContent=(data.schema.found_types||[]).slice(0,6).join(', ')||'—';
      report.style.display='block';

      // Suggestions blob saved for modal
      window.__lastSuggestions = (data.suggestions && typeof data.suggestions==='object') ? data.suggestions : {};

      // Per-item scores + accents + auto-select
      let autoCount=0;
      for(let i=1;i<=25;i++){
        const key='ck-'+i, row=document.getElementById('row-'+i);
        const score=(data.scores && typeof data.scores[key]==='number') ? data.scores[key] : null;
        setScoreBadge(i, score);
        if(row) row.title = (score==null?'No score':`Score: ${Math.round(score)}`);

        if($('#autoApply').checked){
          if(typeof score==='number' && score>=80){ setChecked(key,true); autoCount++; }
          else { setChecked(key,false); }
        }
      }
      $('#rAutoCount').textContent=String(autoCount);
      document.dispatchEvent(new Event('change')); // update progress

      // Overall wheel color + number color
      const overall = (typeof data.overall_score==='number') ? data.overall_score : 0;
      paintWheel(overall);

      // Writer badge
      const ai=data.ai_detection||{};
      const labelMap={likely_human:'Likely Human', mixed:'Mixed', likely_ai:'Likely AI', unknown:'Unknown'};
      const label=labelMap[ai.label]||'Unknown';
      document.getElementById('aiLabel').textContent='Writer: '+label;
      window.__setAIData(ai);

      // Status line
      const wheel=parseInt(document.getElementById('overallScoreInline').textContent||'0',10);
      status.textContent = wheel>=80 ? 'Great! You passed—keep going.' : (wheel<=60 ? 'Score is low — optimize and re‑Analyze.' : 'Solid! Improve a few items to hit green.');
      setTimeout(()=> status.textContent='', 4200);

    }catch(e){
      $('#analyzeStatus').textContent='Error: '+e.message;
    }finally{
      btn.disabled=false; btn.innerHTML='<i class="fa-solid fa-magnifying-glass"></i> Analyze';
    }
  }
})();
</script>
</body>
</html>
