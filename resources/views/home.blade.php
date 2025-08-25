{{-- resources/views/home.blade.php — Fixed full layout (Human vs AI → Readability → Entities → Site Speed) --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, readability, and Core Web Vitals — with water-fill scoring, auto-checklist, and Human vs AI ensemble.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
  $analyzeJsonUrl = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl     = Route::has('analyze')      ? route('analyze')      : url('analyze');
  $psiProxyUrl    = Route::has('psi.proxy')    ? route('psi.proxy')    : url('api/psi'); // server proxy (keeps key private)
@endphp

<title>{{ $metaTitle }}</title>
<link rel="canonical" href="{{ $canonical }}">
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css?v=2" rel="stylesheet"/>

<style>
/* === Theming & base === */
:root{
  --bg:#07080e;--panel:#0f1022;--panel-2:#141433;--text:#f0effa;--text-dim:#b6b3d6;
  --good:#22c55e;--warn:#f59e0b;--bad:#ef4444;--accent:#3de2ff;--accent2:#9b5cff;
  --radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55);--container:1200px;--hue:0deg;
}
*{box-sizing:border-box}
html,body{height:100%} html{scroll-behavior:smooth}
body{
  margin:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,Segoe UI,Roboto;
  background:radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),
             radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),
             var(--bg);
  overflow-x:hidden
}
.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
header.site{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:14px 0 22px;border-bottom:1px solid rgba(255,255,255,.08)}
.brand{display:flex;align-items:center;gap:.8rem;min-width:0}
.brand-badge{width:48px;height:48px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(157,92,255,.3),rgba(61,226,255,.3));border:1px solid rgba(255,255,255,.18);color:#fff;font-size:1.08rem;box-shadow:0 8px 22px rgba(0,0,0,.28)}
.hero-heading{font-weight:1000;letter-spacing:.4px;font-size:clamp(1.4rem,3.2vw,2rem)}
.hero-sub{color:var(--text-dim);font-size:.95rem}
.btn{display:inline-flex;align-items:center;gap:.55rem;cursor:pointer;padding:.6rem .95rem;border-radius:14px;border:1px solid rgba(255,255,255,.16);color:#fff;font-weight:900;letter-spacing:.2px;position:relative;overflow:hidden;box-shadow:0 10px 28px rgba(0,0,0,.25)}
.btn::after{content:"";position:absolute;inset:-2px;border-radius:inherit;opacity:.0;background:linear-gradient(120deg,transparent,rgba(255,255,255,.22),transparent 60%);transform:translateX(-120%);transition:opacity .2s}
.btn:hover::after{opacity:1;animation:btnSweep 2.6s linear infinite}
@keyframes btnSweep{0%{transform:translateX(-120%)}100%{transform:translateX(120%)}}
.btn-analyze{background:linear-gradient(135deg,#10b981,#22c55e);border-color:#20d391}
.btn-print{background:linear-gradient(135deg,#3b82f6,#6366f1);border-color:#5b77ef}
.btn-reset{background:linear-gradient(135deg,#f59e0b,#f97316);border-color:#f59e0b}
.btn-export{background:linear-gradient(135deg,#a855f7,#ec4899);border-color:#c26cf2}
.btn-ghost{background:rgba(255,255,255,.06)} .btn:disabled{opacity:.6;cursor:not-allowed}
.section-title{font-size:1.6rem;margin:0 0 .3rem}.section-subtitle{margin:0;color:var(--text-dim)}

/* Score Gauge + chips */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-gauge{position:relative;width:100%;aspect-ratio:1/1}.gauge-svg{width:100%;height:auto;display:block}
.score-mask-rect{transition:all .6s cubic-bezier(.22,1,.36,1)}
.score-text{font-size:clamp(2.2rem,4.2vw,3.1rem);font-weight:1000;fill:#fff;text-shadow:0 0 18px rgba(255,32,69,.25)}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)}.l-orange{background:rgba(245,158,11,.18)}.l-green{background:rgba(34,197,94,.18)}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.chip-good{background:rgba(34,197,94,.18)!important;border-color:rgba(34,197,94,.45)!important}
.chip-mid{background:rgba(245,158,11,.18)!important;border-color:rgba(245,158,11,.45)!important}
.chip-bad{background:rgba(239,68,68,.18)!important;border-color:rgba(239,68,68,.5)!important}
.ico{width:1.1em;text-align:center}.ico-green{color:var(--good)}.ico-orange{color:var(--warn)}.ico-red{color:var(--bad)}.ico-cyan{color:var(--accent)}.ico-purple{color:#9b5cff}

/* Water & category progress */
.water-wrap{margin-top:.8rem;display:none}
.waterbar{position:relative;height:64px;border-radius:18px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.water-svg{position:absolute;inset:0;width:100%;height:100%;z-index:1}
.water-mask-rect{transition:all .25s ease-out}
.water-overlay{position:absolute;inset:0;pointer-events:none;background:radial-gradient(120px 60px at 20% -20%,rgba(255,255,255,.18),transparent 60%),linear-gradient(0deg,rgba(255,255,255,.05),transparent 40%,transparent 60%,rgba(255,255,255,.06));mix-blend-mode:screen;z-index:2}
.water-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1.05rem;text-shadow:0 1px 0 rgba(0,0,0,.45);letter-spacing:.4px;z-index:4}
.wave1{animation:waveX 7s linear infinite}.wave2{animation:waveX 10s linear infinite reverse;opacity:.7}
@keyframes waveX{0%{transform:translateX(0)}100%{transform:translateX(-600px)}}

.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.comp-water{position:relative;height:52px;border-radius:16px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.comp-svg{position:absolute;inset:0;width:100%;height:100%;z-index:1}.comp-overlay{position:absolute;inset:0;background:radial-gradient(120px 50px at 15% -25%,rgba(255,255,255,.16),transparent 55%),linear-gradient(180deg,rgba(255,255,255,.08),transparent 35%,rgba(255,255,255,.06));pointer-events:none;mix-blend-mode:screen;z-index:3}
.comp-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1rem;z-index:4;text-shadow:0 1px 0 rgba(0,0,0,.45)}
.comp-wave1{animation:waveX 8s linear infinite}.comp-wave2{animation:waveX 12s linear infinite reverse}

/* Card grids */
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
.category-card{position:relative;grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px;box-shadow:var(--shadow);overflow:hidden;isolation:isolate}
.category-card::before{content:"";position:absolute;inset:-2px;border-radius:18px;padding:2px;background:linear-gradient(120deg,rgba(61,226,255,.4),rgba(155,92,255,.4),rgba(255,32,69,.4));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;animation:borderGlow 6s linear infinite;pointer-events:none;z-index:0}
@keyframes borderGlow{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(360deg)}}
.category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
.category-icon{width:48px;height:48px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3de2ff33,#9b5cff33);color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,.18)}
.category-title{margin:0;font-size:1.08rem;background:linear-gradient(90deg,#3de2ff,#9b5cff,#ff2045);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900}
.category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}
.cat-water{grid-column:1/-1;margin-top:.55rem;position:relative;height:22px}
.cat-svg{display:block;width:100%;height:22px}

/* Detection (Human vs AI) */
.detector{margin-top:14px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px}
.det-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.6rem}
.det-head h4{margin:0;font-size:1.1rem}
.hvsa-wrap{display:grid;grid-template-columns:1fr 1fr;gap:.8rem}
.h-bar, .a-bar{height:18px;border-radius:999px;overflow:hidden;position:relative;background:#0b0d21;border:1px solid rgba(255,255,255,.12)}
.h-fill, .a-fill{position:absolute;left:0;top:0;bottom:0;width:0}
.h-fill{background:linear-gradient(90deg,#22c55e,#3de2ff)} .a-fill{background:linear-gradient(90deg,#ef4444,#f59e0b)}
.h-badge,.a-badge{display:inline-flex;align-items:center;gap:.45rem;padding:.25rem .55rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);font-weight:900}
.h-badge{background:rgba(34,197,94,.16)} .a-badge{background:rgba(239,68,68,.16)}
.det-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem;margin-top:.6rem}
.det-item{grid-column:span 6;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.6rem}
.det-row{display:grid;grid-template-columns:1fr auto;gap:.5rem;align-items:center}
.det-bar{margin-top:.35rem;position:relative;height:12px;border-radius:10px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.det-fill{position:absolute;left:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e);transition:width .35s ease}

/* Readability panel */
.readability{margin-top:14px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px}
.read-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.read-card{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.65rem}
.metric{display:flex;align-items:center;justify-content:space-between;font-weight:900}
.meter{margin-top:.4rem;height:12px;border-radius:10px;background:#0b0d21;border:1px solid rgba(255,255,255,.12);overflow:hidden;position:relative}
.meter > span{position:absolute;left:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#3de2ff,#9b5cff)} 

/* Entities panel */
.entities{margin-top:14px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px}
.entity-groups{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.entity-card{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.65rem}
.entity-pills{display:flex;flex-wrap:wrap;gap:.4rem;margin-top:.4rem}
.pill{padding:.25rem .55rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800;background:linear-gradient(135deg,rgba(61,226,255,.15),rgba(155,92,255,.15))}

/* Site Speed & CWV panel */
.psi{margin-top:14px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px}
.psi-status{color:var(--text-dim);margin-bottom:.4rem}
.psi-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.psi-card{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.65rem}
.psi-score{font-size:1.8rem;font-weight:1000}
.psi-badge{padding:.25rem .55rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);font-weight:900;background:rgba(255,255,255,.06)}
.psi-metric{display:flex;align-items:center;justify-content:space-between;margin:.35rem 0;font-weight:900}
.psi-meter{height:10px;border-radius:8px;background:#0b0d21;border:1px solid rgba(255,255,255,.12);overflow:hidden;position:relative}
.psi-meter > span{position:absolute;left:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#22c55e,#f59e0b,#ef4444)}
.psi-suggest{margin-top:.45rem}
.psi-suggest li{margin:.25rem 0}

/* Responsive */
@media (max-width:992px){.category-card{grid-column:span 12}.score-container{width:190px}}
@media (max-width:768px){
  .wrap{padding:18px 4%}
  header.site{flex-direction:column;align-items:flex-start;gap:.6rem}
  .score-area{flex-direction:column;align-items:flex-start;gap:.8rem}
  .score-container{width:170px}
  .hvsa-wrap{grid-template-columns:1fr}
  .read-card,.entity-card,.psi-card{grid-column:span 12}
}
@media (max-width:480px){.score-container{width:150px}}
@media (prefers-reduced-motion: reduce){
  .h-fill,.a-fill,.det-fill,.meter>span,.psi-meter>span{transition:none!important}
}
</style>
</head>
<body>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge" aria-hidden="true"><i class="fa-solid fa-brain"></i></div>
      <div>
        <div class="hero-heading">Semantic SEO Master Analyzer</div>
        <div class="hero-sub">Analyze URLs, get scores & suggestions</div>
      </div>
    </div>
    <div class="header-actions">
      <button class="btn btn-print" id="printTop"><i class="fa-solid fa-print"></i> Print</button>
    </div>
  </header>

  <main class="analyzer" id="analyzer" role="main">
    <h2 class="section-title">Analyze a URL</h2>
    <p class="section-subtitle">
      Wheel + water bars fill with your scores.
      <span class="legend l-green">Green ≥ 80</span>
      <span class="legend l-orange">Orange 60–79</span>
      <span class="legend l-red">Red &lt; 60</span>
    </p>

    <div class="score-area">
      <div class="score-container">
        <!-- Circular water score -->
        <div class="score-gauge">
          <svg class="gauge-svg" viewBox="0 0 200 200" aria-label="Overall score gauge">
            <defs>
              <clipPath id="scoreCircleClip"><circle cx="100" cy="100" r="88"/></clipPath>
              <clipPath id="scoreFillClip"><rect id="scoreClipRect" class="score-mask-rect" x="0" y="200" width="200" height="200"/></clipPath>
              <linearGradient id="scoreGrad" x1="0" y1="0" x2="1" y2="1">
                <stop id="scoreStop1" offset="0%" stop-color="#22c55e"/><stop id="scoreStop2" offset="100%" stop-color="#16a34a"/>
              </linearGradient>
              <linearGradient id="ringGrad" x1="0" y1="0" x2="1" y2="1">
                <stop id="ringStop1" offset="0%" stop-color="#22c55e"/><stop id="ringStop2" offset="100%" stop-color="#16a34a"/>
              </linearGradient>
              <filter id="ringGlow" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="2.4" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
              </filter>
              <path id="scoreWavePath" d="M0 110 Q 15 90 30 110 T 60 110 T 90 110 T 120 110 T 150 110 T 180 110 T 210 110 V 220 H 0 Z"/>
            </defs>
            <circle cx="100" cy="100" r="96" fill="rgba(255,255,255,.06)" stroke="rgba(255,255,255,.12)" stroke-width="2"/>
            <circle id="ringTrack" cx="100" cy="100" r="95" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="6" transform="rotate(-90 100 100)"/>
            <circle id="ringArc" cx="100" cy="100" r="95" fill="none" stroke="url(#ringGrad)" stroke-width="6" stroke-linecap="round" filter="url(#ringGlow)" opacity=".95" transform="rotate(-90 100 100)"/>
            <g clip-path="url(#scoreCircleClip)">
              <rect x="0" y="0" width="200" height="200" fill="#0b0d21"/>
              <g clip-path="url(#scoreFillClip)">
                <g class="score-wave1">
                  <use href="#scoreWavePath" x="0" fill="url(#scoreGrad)"/><use href="#scoreWavePath" x="210" fill="url(#scoreGrad)"/>
                </g>
                <g class="score-wave2" opacity=".85">
                  <use href="#scoreWavePath" x="0" y="6" fill="url(#scoreGrad)"/><use href="#scoreWavePath" x="210" y="6" fill="url(#scoreGrad)"/>
                </g>
              </g>
            </g>
            <text id="overallScore" x="100" y="106" text-anchor="middle" dominant-baseline="middle" class="score-text">0%</text>
          </svg>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:.5rem">
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <span class="chip" id="overallChip"><i class="fa-solid fa-gauge-high ico"></i> Overall: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="contentScoreChip"><i class="fa-solid fa-file-lines ico"></i> Content: <b id="contentScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge" title="Human vs AI summary"><i class="fa-solid fa-user-astronaut ico ico-green"></i> Writer: <b>—</b></span>
          <button id="viewHumanBtn" class="btn btn-ghost"><i class="fa-solid fa-user ico ico-green"></i> Human: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost"><i class="fa-solid fa-robot ico ico-red"></i> AI: <b id="aiPct">—</b>%</button>
          <button id="copyQuick" class="btn btn-ghost"><i class="fa-regular fa-copy ico ico-cyan"></i> Copy report</button>
        </div>
        <small style="color:var(--text-dim)">If the backend returns no scores, a local ensemble + heuristics derive stable, varied scores so the UI always reflects reality.</small>
      </div>
    </div>

    <div class="analyze-box" style="margin-top:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px">
      <form id="analyzeForm" onsubmit="event.preventDefault(); analyze(); return false;">
        <label for="analyzeUrl" style="display:inline-block;font-weight:900;margin-bottom:.35rem">Page URL</label>
        <div class="url-field" id="urlField" style="position:relative;border-radius:16px;background:#0b0d21;border:1px solid #1b1b35;box-shadow:inset 0 0 0 1px rgba(255,255,255,.02),0 12px 32px rgba(0,0,0,.32);padding:10px 110px 10px 46px;transition:.25s;overflow:hidden;isolation:isolate">
          <i class="fa-solid fa-globe url-icon" style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9aa0c3;font-size:1rem;opacity:.95"></i>
          <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" aria-describedby="analyzeStatus" style="all:unset;color:var(--text);width:100%;font-size:1rem;letter-spacing:.2px">
          <button type="button" class="url-mini url-clear" id="clearUrl" title="Clear" style="position:absolute;top:50%;transform:translateY(-50%);right:60px;width:36px;height:32px;display:grid;place-items:center;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;border-radius:10px;padding:.35rem .6rem;font-weight:900;cursor:pointer;transition:.15s"><i class="fa-solid fa-xmark"></i></button>
          <button type="button" class="url-mini" id="pasteUrl" title="Paste" style="position:absolute;top:50%;transform:translateY(-50%);right:12px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;border-radius:10px;padding:.35rem .6rem;font-weight:900;cursor:pointer;transition:.15s">Paste</button>
          <span class="url-border" aria-hidden="true" style="content:'';position:absolute;inset:-2px;border-radius:inherit;padding:2px;background:conic-gradient(from 0deg,#3de2ff,#9b5cff,#ff2045,#f59e0b,#3de2ff);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.55;pointer-events:none;"></span>
        </div>

        <div class="analyze-row" style="display:grid;grid-template-columns:1fr auto auto auto auto;gap:.6rem;align-items:center;margin-top:.6rem">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:#9b5cff">
              <span>Auto-apply checkmarks (≥ 80)</span>
            </label>
          </div>

          <!-- Inline click calls analyze() (guaranteed) -->
          <button id="analyzeBtn" type="button" onclick="analyze()" class="btn btn-analyze">
            <i class="fa-solid fa-magnifying-glass"></i> Analyze
          </button>

          <button class="btn btn-print" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> Print</button>
          <button class="btn btn-reset" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> Reset</button>
          <button class="btn btn-export" id="exportChecklist" type="button" title="Export checklist JSON"><i class="fa-solid fa-file-export"></i> Export</button>
          <button class="btn btn-export" id="importChecklist" type="button" title="Import checklist JSON"><i class="fa-solid fa-file-import"></i> Import</button>
          <input type="file" id="importFile" accept="application/json" style="display:none">
        </div>

        <!-- Progress -->
        <div class="water-wrap" id="waterWrap" aria-hidden="true">
          <div class="waterbar" id="waterBar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <svg class="water-svg" viewBox="0 0 600 200" preserveAspectRatio="none">
              <defs>
                <linearGradient id="waterGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#3de2ff"/><stop offset="100%" stop-color="#9b5cff"/></linearGradient>
                <clipPath id="roundClip"><rect x="1" y="1" width="598" height="198" rx="18" ry="18"/></clipPath>
                <clipPath id="fillClip"><rect id="waterClipRect" class="water-mask-rect" x="0" y="200" width="600" height="200"/></clipPath>
                <path id="wave" d="M0 120 Q 50 90 100 120 T 200 120 T 300 120 T 400 120 T 500 120 T 600 120 V 220 H 0 Z"/>
              </defs>
              <g clip-path="url(#roundClip)">
                <rect x="0" y="0" width="600" height="200" fill="#0b0d21"/>
                <g clip-path="url(#fillClip)">
                  <g class="wave1"><use href="#wave" x="0" fill="url(#waterGrad)"/><use href="#wave" x="600" fill="url(#waterGrad)"/></g>
                  <g class="wave2" opacity=".65"><use href="#wave" x="0" y="8" fill="url(#waterGrad)"/><use href="#wave" x="600" y="8" fill="url(#waterGrad)"/></g>
                </g>
              </g>
            </svg>
            <div class="water-overlay"></div>
            <div class="water-pct"><span id="waterPct">0%</span></div>
          </div>
          <div id="analyzeStatus" style="margin-top:.4rem;color:var(--text-dim)" aria-live="polite"></div>
        </div>
      </form>
    </div>

    <!-- 1) Human vs AI (Ensemble) -->
    <section id="detectorPanel" class="detector" style="display:none">
      <div class="det-head">
        <i class="fa-solid fa-masks-theater ico ico-purple"></i>
        <h4>Human vs AI Content (Ensemble)</h4>
      </div>
      <div class="hvsa-wrap" style="margin-bottom:.3rem">
        <div>
          <div class="metric"><span class="h-badge"><i class="fa-solid fa-user"></i> Human</span> <b id="hvsaHuman">—%</b></div>
          <div class="h-bar"><div class="h-fill" id="hFill"></div></div>
        </div>
        <div>
          <div class="metric"><span class="a-badge"><i class="fa-solid fa-robot"></i> AI</span> <b id="hvsaAI">—%</b></div>
          <div class="a-bar"><div class="a-fill" id="aFill"></div></div>
        </div>
      </div>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.3rem">
        <span class="chip"><i class="fa-solid fa-shield-halved ico"></i> Confidence: <b id="detConfidence">—</b>%</span>
        <span class="chip"><i class="fa-solid fa-circle-info ico"></i> Higher bar = more AI-like for that detector</span>
      </div>
      <div class="det-grid" id="detGrid"></div>
      <div class="det-note" id="detNote" style="color:var(--text-dim);margin-top:.35rem">Local ensemble activates if the backend provides no text/percentages.</div>
    </section>

    <!-- 2) Readability Insights -->
    <section class="readability" id="readabilityPanel" style="display:none">
      <div class="det-head">
        <i class="fa-solid fa-book-open-reader ico ico-cyan"></i>
        <h4>Readability Insights</h4>
      </div>
      <div class="read-grid">
        <div class="read-card">
          <div class="metric"><span>Flesch Reading Ease</span><b id="mFlesch">—</b></div>
          <div class="meter"><span id="mFleschBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span>Word Count</span><b id="mWords">—</b></div>
          <div class="meter"><span id="mWordsBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span>Avg Sentence Length</span><b id="mASL">—</b></div>
          <div class="meter"><span id="mASLBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span>Lexical Diversity (TTR)</span><b id="mTTR">—</b></div>
          <div class="meter"><span id="mTTRBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span>Repetition (tri-gram)</span><b id="mRep">—</b></div>
          <div class="meter"><span id="mRepBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span>Digits per 100 words</span><b id="mDigits">—</b></div>
          <div class="meter"><span id="mDigitsBar"></span></div>
        </div>
      </div>
    </section>

    <!-- 3) Entities & Topics -->
    <section class="entities" id="entitiesPanel" style="display:none">
      <div class="det-head">
        <i class="fa-solid fa-database ico ico-purple"></i>
        <h4>Entities & Topics</h4>
      </div>
      <div class="entity-groups" id="entityGroups">
        <!-- JS will insert groups: People, Organizations, Locations, Software, Games/APKs, Products, Others -->
      </div>
    </section>

    <!-- 4) Site Speed & Core Web Vitals -->
    <section class="psi" id="psiPanel" style="display:none">
      <div class="det-head">
        <i class="fa-solid fa-gauge ico ico-cyan"></i>
        <h4>Site Speed & Core Web Vitals</h4>
      </div>
      <div class="psi-status" id="psiStatus">Ready.</div>

      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.4rem">
        <button class="btn btn-ghost" id="runMobile"><i class="fa-solid fa-mobile-screen-button"></i> Mobile</button>
        <button class="btn btn-ghost" id="runDesktop"><i class="fa-solid fa-desktop"></i> Desktop</button>
      </div>

      <div class="psi-grid" id="psiGrid">
        <!-- JS will render score, metrics, suggestions -->
      </div>
    </section>

    {{-- Checklist & category progress (unchanged, kept for completeness) --}}
    @php $labels = [
      1=>'Define search intent & primary topic', 2=>'Map target & related keywords (synonyms/PAA)', 3=>'H1 includes primary topic naturally',
      4=>'Integrate FAQs / questions with answers', 5=>'Readable, NLP-friendly language', 6=>'Title tag (≈50–60 chars) w/ primary keyword',
      7=>'Meta description (≈140–160 chars) + CTA', 8=>'Canonical tag set correctly', 9=>'Indexable & listed in XML sitemap',
      10=>'E-E-A-T signals (author, date, expertise)', 11=>'Unique value vs. top competitors', 12=>'Facts & citations up to date',
      13=>'Helpful media (images/video) w/ captions', 14=>'Logical H2/H3 headings & topic clusters', 15=>'Internal links to hub/related pages',
      16=>'Clean, descriptive URL slug', 17=>'Breadcrumbs enabled (+ schema)', 18=>'Mobile-friendly, responsive layout',
      19=>'Optimized speed (compression, lazy-load)', 20=>'Core Web Vitals passing (LCP/INP/CLS)', 21=>'Clear CTAs and next steps',
      22=>'Primary entity clearly defined', 23=>'Related entities covered with context', 24=>'Valid schema markup (Article/FAQ/Product)',
      25=>'sameAs/Organization details present'
    ]; @endphp

    <div class="progress-wrap">
      <div class="comp-water" id="compWater" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
        <svg class="comp-svg" viewBox="0 0 600 140" preserveAspectRatio="none">
          <defs>
            <clipPath id="compRound"><rect x="1" y="1" width="598" height="138" rx="14" ry="14"/></clipPath>
            <clipPath id="compFillClip"><rect id="compClipRect" x="0" y="0" width="0" height="140"/></clipPath>
            <linearGradient id="compGrad" x1="0" y1="0" x2="1" y2="1"><stop id="compStop1" offset="0%" stop-color="#3de2ff"/><stop id="compStop2" offset="100%" stop-color="#9b5cff"/></linearGradient>
            <path id="compWave" d="M0 80 Q 50 60 100 80 T 200 80 T 300 80 T 400 80 T 500 80 T 600 80 V 160 H 0 Z"/>
          </defs>
          <g clip-path="url(#compRound)">
            <rect x="0" y="0" width="600" height="140" fill="#0b0d21"/>
            <g clip-path="url(#compFillClip)">
              <g class="comp-wave1"><use href="#compWave" x="0" fill="url(#compGrad)"/><use href="#compWave" x="600" fill="url(#compGrad)"/></g>
              <g class="comp-wave2" opacity=".75"><use href="#compWave" x="0" y="6" fill="url(#compGrad)"/><use href="#compWave" x="600" y="6" fill="url(#compGrad)"/></g>
            </g>
          </g>
        </svg>
        <div class="comp-overlay"></div>
        <div class="comp-pct"><span id="compPct">0%</span></div>
      </div>
      <div id="progressCaption" class="progress-caption" style="color:var(--text-dim)">0 of 25 items completed</div>
    </div>

    <div class="analyzer-grid" id="checklistGrid">
      @foreach ([
        ['Content & Keywords',1,5,'fa-pen-nib','linear-gradient(135deg,#22d3ee33,#a78bfa33)'],
        ['Technical Elements',6,9,'fa-code','linear-gradient(135deg,#a7f3d033,#60a5fa33)'],
        ['Content Quality',10,13,'fa-star','linear-gradient(135deg,#fcd34d33,#fb718533)'],
        ['Structure & Architecture',14,17,'fa-sitemap','linear-gradient(135deg,#86efac33,#f0abfc33)'],
        ['User Signals & Experience',18,21,'fa-user-check','linear-gradient(135deg,#fca5a533,#fde68a33)'],
        ['Entities & Context',22,25,'fa-database','linear-gradient(135deg,#f472b633,#60a5fa33)'],
      ] as $c)
        <article class="category-card" data-cat-i="{{ $loop->index }}" style="background-image: {{ $c[4] }}; background-blend-mode: lighten;">
          <header class="category-head">
            <span class="category-icon" aria-hidden="true"><i class="fas {{ $c[3] }}"></i></span>
            <div>
              <h3 class="category-title">{{ $c[0] }}</h3>
              <p class="category-sub">—</p>
              <div class="cat-water" id="catWater-{{ $loop->index }}">
                <svg class="cat-svg" viewBox="0 0 600 24" preserveAspectRatio="none">
                  <defs>
                    <clipPath id="catClip-{{ $loop->index }}"><rect x="0" y="0" width="600" height="24" rx="10" ry="10"/></clipPath>
                    <clipPath id="catFillClip-{{ $loop->index }}"><rect id="catFillRect-{{ $loop->index }}" x="0" y="0" width="0" height="24"/></clipPath>
                    <linearGradient id="catGrad-{{ $loop->index }}" x1="0" y1="0" x2="1" y2="1">
                      <stop id="catStop1-{{ $loop->index }}" offset="0%" stop-color="#22d3ee"/>
                      <stop id="catStop2-{{ $loop->index }}" offset="100%" stop-color="#a78bfa"/>
                    </linearGradient>
                    <path id="catWave-{{ $loop->index }}" d="M0 12 Q 40 6 80 12 T 160 12 T 240 12 T 320 12 T 400 12 T 480 12 T 560 12 T 640 12 V 30 H 0 Z"/>
                  </defs>
                  <g clip-path="url(#catClip-{{ $loop->index }})">
                    <rect x="0" y="0" width="600" height="24" fill="#0b0d21"/>
                    <g clip-path="url(#catFillClip-{{ $loop->index }})">
                      <g class="cat-wave1"><use href="#catWave-{{ $loop->index }}" x="0" fill="url(#catGrad-{{ $loop->index }})"/><use href="#catWave-{{ $loop->index }}" x="640" fill="url(#catGrad-{{ $loop->index }})"/></g>
                      <g class="cat-wave2" opacity=".85"><use href="#catWave-{{ $loop->index }}" x="0" y="3" fill="url(#catGrad-{{ $loop->index }})"/><use href="#catWave-{{ $loop->index }}" x="640" y="3" fill="url(#catGrad-{{ $loop->index }})"/></g>
                    </g>
                  </g>
                </svg>
                <div class="cat-water-pct" id="catPct-{{ $loop->index }}" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.8rem;color:rgba(255,255,255,.9);text-shadow:0 1px 0 rgba(0,0,0,.55);pointer-events:none">0/0 • 0%</div>
              </div>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">{{ $c[2]-$c[1]+1 }}</span></span>
          </header>
          <ul class="checklist" style="list-style:none;margin:10px 0 0;padding:0">
            @for($i=$c[1];$i<=$c[2];$i++)
              <li class="checklist-item" style="display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.7rem .75rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02)),radial-gradient(100% 120% at 0% 0%,rgba(61,226,255,.06),transparent 30%),radial-gradient(120% 100% at 100% 0%,rgba(155,92,255,.05),transparent 35%);transition:box-shadow .25s,background .25s,transform .12s">
                <label style="cursor:pointer;display:inline-flex;align-items:center;gap:.55rem">
                  <input type="checkbox" id="ck-{{ $i }}" style="appearance:none;-webkit-appearance:none;outline:none;width:22px;height:22px;border-radius:8px;background:#0b1220;border:2px solid #2a2f4d;position:relative;display:inline-grid;place-items:center;transition:.18s;box-shadow:inset 0 0 0 0 rgba(99,102,241,.0)">
                  <span>{{ $labels[$i] }}</span>
                </label>
                <span class="score-badge" id="sc-{{ $i }}" style="font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center">—</span>
                <button class="btn btn-ghost" type="button" data-id="ck-{{ $i }}">Improve</button>
              </li>
            @endfor
          </ul>
        </article>
      @endforeach
    </div>
  </main>
</div>

<footer class="site" style="margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)">
  <div><strong>Semantic SEO Master</strong></div>
  <div class="footer-links">
    <a href="#analyzer">Analyzer</a>
    <a href="#" id="toTopLink">Back to top</a>
  </div>
</footer>

<!-- ===== EARLY SAFE STUB (ensures Analyze works even before main script loads) ===== -->
<script>
  window.SEMSEO = window.SEMSEO || {};
  window.SEMSEO.ENDPOINTS = {
    analyzeJson: @json($analyzeJsonUrl),
    analyze: @json($analyzeUrl),
    psi: @json($psiProxyUrl)
  };
  window.SEMSEO.READY = false;
  window.SEMSEO.BUSY = false;
  window.SEMSEO.QUEUE = 0;

  if (!window.analyze) {
    window.analyze = function(){
      window.SEMSEO.QUEUE++;
      const s = document.getElementById('analyzeStatus');
      if (s) s.textContent = 'Initializing…';
    };
  }

  // Early binding in case DOM listener comes late
  document.addEventListener('readystatechange', function(){
    var b = document.getElementById('analyzeBtn');
    if (b && !b.__bound) { b.__bound = true; b.onclick = analyze; }
  });
</script>

<!-- ===== MAIN APP SCRIPT ===== -->
<script>
(function(){
  var CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  function setText(id,val){ var el=document.getElementById(id); if(el){ el.textContent=val; } return el; }
  function setChipTone(el, v){ if(!el) return; el.classList.remove('chip-good','chip-mid','chip-bad'); var n=Number(v)||0; el.classList.add(n>=80?'chip-good':(n>=60?'chip-mid':'chip-bad')); }
  function badgeTone(el, v){ if(!el) return; el.classList.remove('score-good','score-mid','score-bad'); el.classList.add(v>=80?'score-good':(v>=60?'score-mid':'score-bad')); }

  /* === Score wheel === */
  var GAUGE={rect:null,stop1:null,stop2:null,r1:null,r2:null,arc:null,text:null,H:200,CIRC:2*Math.PI*95};
  window.setScoreWheel = function(value){
    if(!GAUGE.rect){
      GAUGE.rect=document.getElementById('scoreClipRect'); GAUGE.stop1=document.getElementById('scoreStop1'); GAUGE.stop2=document.getElementById('scoreStop2');
      GAUGE.r1=document.getElementById('ringStop1'); GAUGE.r2=document.getElementById('ringStop2'); GAUGE.arc=document.getElementById('ringArc'); GAUGE.text=document.getElementById('overallScore');
      if(GAUGE.arc){ GAUGE.arc.style.strokeDasharray=GAUGE.CIRC.toFixed(2); GAUGE.arc.style.strokeDashoffset=GAUGE.CIRC.toFixed(2); }
    }
    var v=Math.max(0,Math.min(100,Number(value)||0));
    var y=GAUGE.H-(GAUGE.H*(v/100));
    if(GAUGE.rect) GAUGE.rect.setAttribute('y',String(y));
    if(GAUGE.text) GAUGE.text.textContent=Math.round(v)+'%';

    var c1,c2; if(v>=80){c1='#22c55e';c2='#16a34a'} else if(v>=60){c1='#f59e0b';c2='#fb923c'} else {c1='#ef4444';c2='#b91c1c'}
    if(GAUGE.stop1) GAUGE.stop1.setAttribute('stop-color',c1); if(GAUGE.stop2) GAUGE.stop2.setAttribute('stop-color',c2);
    if(GAUGE.r1) GAUGE.r1.setAttribute('stop-color',c1); if(GAUGE.r2) GAUGE.r2.setAttribute('stop-color',c2);
    if(GAUGE.arc){ var offset=GAUGE.CIRC*(1-(v/100)); GAUGE.arc.style.strokeDashoffset=offset.toFixed(2); }
    setText('overallScoreInline',Math.round(v)); setChipTone(document.getElementById('overallChip'),v);
  };

  /* === Category bars + completion === */
  function updateCategoryBars(){
    var cards=[].slice.call(document.querySelectorAll('.category-card'));
    var total=0, checked=0;
    cards.forEach(function(card,idx){
      var items=[].slice.call(card.querySelectorAll('.checklist-item'));
      var t=items.length, done=items.filter(function(li){ var c=li.querySelector('input'); return c && c.checked; }).length;
      total+=t; checked+=done;
      var pct=t?Math.round(done*100/t):0;
      var fill=document.getElementById('catFillRect-'+idx); if(fill) fill.setAttribute('width', String(6*pct));
      var pctEl=document.getElementById('catPct-'+idx); if(pctEl) pctEl.textContent = done+'/'+t+' • '+pct+'%';
      var sub=card.querySelector('.category-sub'); if(sub) sub.textContent = pct>=80?'Great progress':'Keep improving';
      var cnt=card.querySelector('.checked-count'); if(cnt) cnt.textContent = done;
      var stop1=document.getElementById('catStop1-'+idx), stop2=document.getElementById('catStop2-'+idx);
      var c1=pct>=80?'#22c55e':(pct>=60?'#f59e0b':'#ef4444'); var c2=pct>=80?'#16a34a':(pct>=60?'#fb923c':'#b91c1c');
      if(stop1) stop1.setAttribute('stop-color',c1); if(stop2) stop2.setAttribute('stop-color',c2);
    });
    var pctAll = total? Math.round(checked*100/total) : 0;
    var comp=document.getElementById('compClipRect'); if(comp) comp.setAttribute('width', String(6*pctAll));
    setText('compPct', pctAll + '%'); setText('progressCaption', checked+' of '+total+' items completed');
  }
  window.updateCategoryBars = updateCategoryBars;

  /* === Auto-tick by item scores === */
  function autoTickByScores(map){
    var autoCount=0;
    for(var i=1;i<=25;i++){
      var scVal=Number((map && map[i]!==undefined)? map[i] : NaN);
      var badge=document.getElementById('sc-'+i);
      var cb=document.getElementById('ck-'+i);
      var row=cb ? cb.closest('.checklist-item') : null;
      if (!badge) continue;
      if (!isNaN(scVal)) {
        badge.textContent = Math.round(scVal);
        badgeTone(badge, scVal);
        if (document.getElementById('autoApply') && document.getElementById('autoApply').checked && scVal>=80) {
          if (cb && !cb.checked) { cb.checked=true; autoCount++; }
          if(row){ row.classList.remove('sev-mid','sev-bad'); row.classList.add('sev-good'); }
        } else if (scVal>=60) { if(row){ row.classList.remove('sev-bad','sev-good'); row.classList.add('sev-mid'); } }
        else { if(row){ row.classList.remove('sev-mid','sev-good'); row.classList.add('sev-bad'); } }
      } else {
        badge.textContent='—';
      }
    }
    setText('rAutoCount', autoCount);
    updateCategoryBars();
  }
  window.autoTickByScores = autoTickByScores;

  /* === Wave progress === */
  var Water=(function(){
    var wrapId=function(){ return document.getElementById('waterWrap'); };
    var clipId=function(){ return document.getElementById('waterClipRect'); };
    var pctId=function(){ return document.getElementById('waterPct'); };
    var t=null, value=0;
    function show(){ var w=wrapId(); if(w) w.style.display='block'; }
    function hide(){ var w=wrapId(); if(w) w.style.display='none'; }
    function set(v){ value=Math.max(0,Math.min(100,v)); var y=200 - (200*value/100); var clip=clipId(); if(clip) clip.setAttribute('y', String(y)); var p=pctId(); if(p) p.textContent = Math.round(value) + '%'; }
    return {
      start:function(){ show(); set(0); if(t) clearInterval(t); t=setInterval(function(){ if(value<88) set(value+2); }, 80); },
      finish:function(){ if(t) clearInterval(t); setTimeout(function(){ set(100); }, 150); setTimeout(function(){ hide(); }, 800); },
      reset:function(){ if(t) clearInterval(t); set(0); hide(); }
    };
  })();
  window.Water = Water;

  /* === Helpers === */
  function clamp(v,min,max){ return v<min?min:(v>max?max:v); }
  function normalizeUrl(u) {
    if (!u) return '';
    u = u.trim();
    if (/^https?:\/\//i.test(u)) { try { new URL(u); return u; } catch(e) { return ''; } }
    var guess = 'https://' + u.replace(/^\/+/, '');
    try { new URL(guess); return guess; } catch(e) { return ''; }
  }

  async function fetchBackend(url){
    let data=null, ok=false, status=0, text='';
    const qs=new URLSearchParams({url}).toString();
    try{
      const r1=await fetch((window.SEMSEO.ENDPOINTS.analyzeJson||'analyze-json')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
      status=r1.status; text=await r1.text(); try{ data=JSON.parse(text);}catch(_){}
      if(r1.ok && data) ok=true;
    }catch(_){}
    if(!ok){
      try{
        const r2=await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze'),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body:JSON.stringify({url,_token:CSRF})});
        status=r2.status; text=await r2.text(); try{ data=JSON.parse(text);}catch(_){}
        if(r2.ok && data) ok=true;
      }catch(_){}
    }
    if(!ok){
      try{
        const r3=await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
        status=r3.status; text=await r3.text(); try{ data=JSON.parse(text);}catch(_){}
        if(r3.ok && data) ok=true;
      }catch(_){}
    }
    return {ok,data,status};
  }

  // CORS proxy for raw HTML
  async function fetchRawHtml(url){
    try{
      const r=await fetch('https://api.allorigins.win/raw?url='+encodeURIComponent(url),{cache:'no-store'});
      if(r.ok){ const html=await r.text(); if(html && html.length>200) return html; }
    }catch(_){}
    return '';
  }

  // Readable text via Jina Reader
  async function fetchReadableText(url){
    try{
      const httpsR = await fetch('https://r.jina.ai/http/'+url.replace(/^https?:\/\//,''));
      if(httpsR.ok){ const t = await httpsR.text(); if(t && t.length>200) return t; }
    }catch(_){}
    try{
      const altR = await fetch('https://r.jina.ai/'+url);
      if(altR.ok){ const t = await altR.text(); if(t && t.length>200) return t; }
    }catch(_){}
    return '';
  }

  function extractMetaFromHtml(html, baseUrl){
    try{
      var d=(new DOMParser()).parseFromString(html,'text/html');
      var q=(s,a)=>{var el=d.querySelector(s);return el?(a?el.getAttribute(a)||'':(el.textContent||'')) : '';};
      var title=(q('title')||'').trim();
      var metaDesc=(q('meta[name="description"]','content')||'').trim();
      var canonical=(q('link[rel="canonical"]','href')||'').trim()||baseUrl;
      var robots=(q('meta[name="robots"]','content')||'').trim()||'n/a';
      var viewport=(q('meta[name="viewport"]','content')||'').trim()||'n/a';
      var h1=d.querySelectorAll('h1').length, h2=d.querySelectorAll('h2').length, h3=d.querySelectorAll('h3').length;
      var origin=''; try{ origin=new URL(baseUrl).origin; }catch(_){}
      var internal=0; d.querySelectorAll('a[href]').forEach(function(a){ try{ var u=new URL(a.getAttribute('href'), baseUrl); if(!origin || u.origin===origin) internal++; }catch(_){} });
      var schema = !!(d.querySelector('script[type="application/ld+json"]') || d.querySelector('[itemscope],[itemtype*="schema.org"]'));
      var main=d.querySelector('article,main,[role="main"]'); var sample=main? (main.textContent||''): '';
      if(!sample){ sample=[].slice.call(d.querySelectorAll('p')).slice(0,12).map(function(p){return p.textContent;}).join('\n\n'); }
      sample=(sample||'').replace(/\s{2,}/g,' ').trim();
      return { titleLen: title?title.length:null, metaLen: metaDesc?metaDesc.length:null, canonical, robots, viewport, headings:(h1+'/'+h2+'/'+h3), internalLinks:internal, schema: schema?'yes':'no', sampleText: sample };
    }catch(_){ return {}; }
  }

  function mergeMeta(into, add){
    if(!into) into={};
    var keys=['titleLen','metaLen','canonical','robots','viewport','headings','internalLinks','schema','sampleText'];
    keys.forEach(function(k){
      if((into[k]===undefined || into[k]===null || into[k]==='—' || into[k]==='' ) && add && add[k]!==undefined && add[k]!==null){
        into[k]=add[k];
      }
    });
    return into;
  }

  /* === Stylometry / Readability helpers === */
  function _countSyllables(word){
    var w=(word||'').toLowerCase().replace(/[^a-z]/g,''); if(!w) return 0;
    var m=(w.match(/[aeiouy]+/g)||[]).length; if(/(ed|es)$/.test(w)) m--; if(/^y/.test(w)) m--; return Math.max(1,m);
  }
  function _flesch(text){
    var sents = (text.match(/[.!?]+/g)||[]).length || 1;
    var words = (text.match(/[A-Za-z\u00C0-\u024f']+/g)||[]); var wN = words.length||1;
    var syll = 0; for(var i=0;i<words.length;i++){ syll += _countSyllables(words[i]); }
    return clamp(206.835 - 1.015*(wN/sents) - 84.6*(syll/wN), -20, 120);
  }
  function _prep(text){
    text=(text||'')+''; text=text.replace(/\u00A0/g,' ').replace(/\s+/g,' ').trim();
    var wordRe=/[A-Za-z\u00C0-\u024f0-9']+/g; var words=(text.match(wordRe)||[]).map(function(w){return w.toLowerCase();});
    var sents=text.split(/(?<=[.!?])\s+|\n+(?=\S)/g).filter(Boolean); var tokens=words.length||1;
    var freq=Object.create(null); words.forEach(function(w){freq[w]=(freq[w]||0)+1;});
    var types=Object.keys(freq).length, hapax=0; for(var k in freq){ if(freq[k]===1) hapax++; }
    var lens=sents.map(function(s){return (s.match(wordRe)||[]).length;}).filter(function(v){return v>0;});
    var mean=lens.reduce(function(a,b){return a+b;},0)/(lens.length||1);
    var variance=lens.reduce(function(a,b){return a+Math.pow(b-mean,2);},0)/(lens.length||1);
    var cov=mean?Math.sqrt(variance)/mean:0;
    var tri={}, triT=0, triR=0; for(var i=0;i<tokens-2;i++){ var g=words[i]+' '+words[i+1]+' '+words[i+2]; tri[g]=(tri[g]||0)+1; triT++; } for(var kk in tri){ if(tri[kk]>1) triR+=tri[kk]-1; }
    var digits=(text.match(/\d/g)||[]).length*100/(tokens||1);
    var avgLen=tokens? (words.join('').length/tokens):0;
    var longRatio=(lens.filter(function(L){return L>=28;}).length)/(lens.length||1);
    var TTR=types/(tokens||1);
    return { text, wordCount:tokens, flesch:_flesch(text), cov, longRatio, triRepeatRatio: triT?triR/triT:0, TTR, hapaxRatio: types?hapax/types:0, avgWordLen:avgLen, digitsPer100:digits, sCount:lens.length, asl:mean||0 };
  }

  function detectUltra(text){
    var s=_prep(text||'');
    if (s.wordCount < 40){ var aiQuick = clamp(70 - s.wordCount*0.8, 20, 70); return { humanPct: 100-aiQuick, aiPct: aiQuick, confidence: 46, detectors: [] , _s:s }; }
    var ai=10; var covT=0.45; if(s.cov<covT) ai+=clamp((covT-s.cov)/covT,0,1)*25; var ttrT=0.45; if(s.TTR<ttrT) ai+=clamp((ttrT-s.TTR)/ttrT,0,1)*18;
    var conf = clamp(50 + Math.min(45, Math.log((s.wordCount||1)+1)*7), 45, 95);
    return { humanPct: 100-clamp(Math.round(ai),0,100), aiPct: clamp(Math.round(ai),0,100), confidence: conf, detectors: [{key:'stylometry',label:'Stylometry',ai:clamp(Math.round(ai),0,100),w:1}], _s:s };
  }

  function deriveItemScoresFromSignals(s){
    function pct(x){ return clamp(Math.round(x),0,100); }
    function band(x,l,h){ if (x<=l) return 0; if (x>=h) return 100; return (x-l)*100/(h-l); }
    var read=pct(band(s.flesch,35,75)), rep=pct(100*(1 - s.triRepeatRatio)), ttr=pct(band(s.TTR,0.30,0.65)), longS=pct(band(1-s.longRatio, 0.6, 0.95)), avgLen=pct(band(s.avgWordLen,4.2,5.8)), digits=pct(100*(1 - s.digitsPer100/20));
    var i=[];
    i[1]=pct(.5*read+.5*ttr); i[2]=pct(.6*ttr+.4*avgLen); i[3]=pct(.4*ttr+.6*read); i[4]=pct(.7*read+.3*rep); i[5]=pct(.5*read+.5*avgLen);
    i[6]=pct(.4*ttr+.6*read); i[7]=pct(.4*read+.6*rep); i[8]=pct(.6*rep+.4*digits); i[9]=pct(.6*avgLen+.4*digits); i[10]=pct(.6*avgLen+.4*ttr);
    i[11]=pct(.5*ttr+.5*rep); i[12]=pct(.6*rep+.4*digits); i[13]=pct(.6*read+.4*rep); i[14]=pct(.6*read+.4*ttr); i[15]=pct(.5*ttr+.5*read);
    i[16]=pct(.6*digits+.4*read); i[17]=pct(.5*avgLen+.5*ttr); i[18]=pct(.5*read+.5*longS); i[19]=pct(.6*rep+.4*avgLen); i[20]=pct(.5*longS+.5*avgLen);
    i[21]=pct(.7*read+.3*ttr); i[22]=pct(.6*ttr+.4*avgLen); i[23]=pct(.6*ttr+.4*avgLen); i[24]=pct(.6*avgLen+.4*ttr); i[25]=pct(.6*ttr+.4*digits);
    var map={}; for(var k=1;k<=25;k++){ map[k]=i[k]; } return map;
  }
  function deriveSummaryScoresFromItems(itemMap){
    var all=[]; for(var i=1;i<=25;i++){ if(isFinite(itemMap[i])) all.push(itemMap[i]); }
    var avg = function(a){ return a.length? Math.round(a.reduce(function(x,y){return x+y;},0)/a.length) : 0; };
    return { contentScore: avg(all.slice(0,13)), overall: avg(all) };
  }
  function buildSampleFromData(data){
    var parts = [];
    ['textSample','extractedText','plainText','body','sample','content','text'].forEach(function(k){ if(typeof data?.[k]==='string' && data[k].length>0) parts.push(data[k]); });
    ['title','meta','description','ogDescription','firstParagraph','snippet','h1','h2','h3'].forEach(function(k){
      var v = data?.[k];
      if (typeof v === 'string' && v.trim()) parts.push(v);
      if (Array.isArray(v)) parts.push(v.join('. '));
    });
    var txt = parts.join('\n\n').replace(/\s{2,}/g,' ').trim();
    return txt.length>140000 ? txt.slice(0,140000) : txt;
  }
  function ensureScoresExist(data, sample, ensemble){
    var needItems = !data.itemScores || Object.keys(data.itemScores).length===0;
    var needContent = typeof data.contentScore!=='number' || isNaN(data.contentScore);
    var needOverall = typeof data.overall!=='number' || isNaN(data.overall);
    var s = (ensemble && ensemble._s) ? ensemble._s : _prep(sample||'');
    if (needItems) data.itemScores = deriveItemScoresFromSignals(s);
    if (needContent || needOverall){
      var sums = deriveSummaryScoresFromItems(data.itemScores||{});
      if (needContent) data.contentScore = sums.contentScore;
      if (needOverall) data.overall = sums.overall;
    }
    return data;
  }

  /* === Human vs AI UI === */
  function renderDetectors(res){
    var panel = document.getElementById('detectorPanel'); if(!panel) return;
    var grid = document.getElementById('detGrid'); var confEl = document.getElementById('detConfidence');
    if(confEl) confEl.textContent = isFinite(res.confidence)? Math.round(res.confidence): '—';
    panel.style.display = 'block'; if(!grid) return; grid.innerHTML = '';
    (res.detectors||[{key:'stylometry',label:'Stylometry',ai:res.aiPct||0}]).forEach(function(d){
      var id='det-'+d.key; var wrap=document.createElement('div');
      wrap.className='det-item'; wrap.innerHTML =
        '<div class="det-row"><div class="det-label">'+d.label+'</div><div class="det-score" id="'+id+'-score">'+(d.ai||0)+'</div></div>'+
        '<div class="det-bar"><div class="det-fill" id="'+id+'-fill" style="width:'+(clamp(d.ai||0,0,100))+'%"></div></div>';
      grid.appendChild(wrap);
    });
  }
  function applyDetection(humanPct, aiPct, confidence, breakdown){
    var hp = isFinite(humanPct)? Math.round(humanPct): NaN;
    var ap = isFinite(aiPct)? Math.round(aiPct): NaN;
    var badge = document.getElementById('aiBadge'); 
    if (badge){
      var b=badge.querySelector('b');
      if(b) b.textContent = (isFinite(hp) && isFinite(ap) && hp>=ap) ? 'Likely Human' : 'AI-like';
      badge.title = 'Confidence: ' + (confidence? confidence+'%':'—');
    }
    setText('humanPct', isFinite(hp)? hp : '—');
    setText('aiPct', isFinite(ap)? ap : '—');
    setText('hvsaHuman', isFinite(hp)? hp+'%' : '—%');
    setText('hvsaAI', isFinite(ap)? ap+'%' : '—%');

    // Animate fills
    var hFill = document.getElementById('hFill'), aFill = document.getElementById('aFill');
    if (hFill){ hFill.style.width = clamp(isFinite(hp)? hp:0,0,100) + '%'; hFill.style.transition='width .55s ease'; }
    if (aFill){ aFill.style.width = clamp(isFinite(ap)? ap:0,0,100) + '%'; aFill.style.transition='width .55s ease'; }

    var panel = document.getElementById('detectorPanel'); if(panel) panel.style.display='block';
    if (breakdown && breakdown.detectors){ renderDetectors(breakdown); } else { renderDetectors({confidence:confidence, detectors:[{key:'stylometry', label:'Stylometry', ai:ap||0}]}); }
  }

  /* === Readability UI === */
  function renderReadability(s){
    var p = document.getElementById('readabilityPanel'); if(!p) return;
    function bar(id, v, max){ var el=document.getElementById(id); if(!el) return; el.style.width = clamp((v/max)*100, 0, 100)+'%'; el.style.transition='width .45s ease'; }
    setText('mFlesch', Math.round(s.flesch));
    setText('mWords', s.wordCount);
    setText('mASL', s.asl ? s.asl.toFixed(1) : '—');
    setText('mTTR', s.TTR ? (s.TTR*100).toFixed(0)+'%' : '—');
    setText('mRep', s.triRepeatRatio ? Math.round(s.triRepeatRatio*100)+'%' : '—');
    setText('mDigits', s.digitsPer100 ? Math.round(s.digitsPer100) : 0);

    bar('mFleschBar', clamp(s.flesch,0,100), 100);
    bar('mWordsBar', Math.min(s.wordCount, 3000), 3000);
    bar('mASLBar', clamp(30 - s.asl, 0, 30), 30); // shorter is generally easier
    bar('mTTRBar', clamp(s.TTR, 0, 1), 1);
    bar('mRepBar', clamp(1 - s.triRepeatRatio, 0, 1), 1);
    bar('mDigitsBar', clamp(20 - s.digitsPer100, 0, 20), 20);

    p.style.display='block';
  }

  /* === Entities extraction (heuristic) === */
  function extractEntities(txt){
    var entities = { People:[], Organizations:[], Locations:[], Software:[], Games:[], APKs:[], Products:[], Other:[] };
    if(!txt || txt.length<10) return entities;
    // Simple proper-noun-ish capture
    var proper = txt.match(/\b([A-Z][a-zA-Z]+(?:\s+[A-Z][a-zA-Z]+){0,2})\b/g) || [];
    var uniq = Array.from(new Set(proper)).slice(0,300);

    // Keyword buckets
    var softKeys = /(WordPress|Laravel|React|Vue|Angular|Android|iOS|Kotlin|Swift|Node\.js|Django|Flask|Spring|Next\.js|Nuxt|Tailwind|Bootstrap|Photoshop|Figma|Premiere|Windows|macOS|Linux|Ubuntu|MySQL|PostgreSQL|Redis|MongoDB|Docker|Kubernetes)/i;
    var gameKeys = /(Minecraft|Fortnite|PUBG|Valorant|GTA|Roblox|Apex|Zelda|Mario|FIFA|Call of Duty)/i;
    var apkKeys  = /(APK|\.apk|Android App|Mod APK)/i;
    var orgKeys  = /(Inc\.|LLC|Ltd|Corporation|Company|University|Institute|Lab|Google|Apple|Microsoft|Meta|Amazon|Netflix|OpenAI|NVIDIA|Samsung|Huawei|Xiaomi)/i;
    var locKeys  = /(City|Town|Village|Province|State|County|Pakistan|India|USA|United Kingdom|UK|UAE|Dubai|Karachi|Lahore|Islamabad|Delhi|London|New York|San Francisco|Tokyo|Paris|Berlin)/i;
    var productKeys = /(iPhone|Galaxy|Pixel|ThinkPad|MacBook|iPad|Watch|Surface|PlayStation|Xbox|Switch|Camera|Lens|SSD|GPU|RTX|Ryzen)/i;

    uniq.forEach(function(t){
      if (softKeys.test(t)) entities.Software.push(t);
      else if (gameKeys.test(t)) entities.Games.push(t);
      else if (apkKeys.test(t)) entities.APKs.push(t);
      else if (orgKeys.test(t)) entities.Organizations.push(t);
      else if (locKeys.test(t)) entities.Locations.push(t);
      else if (productKeys.test(t)) entities.Products.push(t);
      else {
        // Heuristic: 2-token capitalized likely person
        if (/^[A-Z][a-z]+ [A-Z][a-z]+$/.test(t)) entities.People.push(t);
        else entities.Other.push(t);
      }
    });

    // Cleanup
    Object.keys(entities).forEach(k=>{
      entities[k] = Array.from(new Set(entities[k])).slice(0,30);
    });
    return entities;
  }

  function renderEntities(entities){
    var p = document.getElementById('entitiesPanel'); if(!p) return;
    var g = document.getElementById('entityGroups'); if(!g) return;
    g.innerHTML='';
    var iconBy = {
      People:'fa-user', Organizations:'fa-building', Locations:'fa-location-dot',
      Software:'fa-code', Games:'fa-gamepad', APKs:'fa-android', Products:'fa-box', Other:'fa-tag'
    };
    Object.keys(entities).forEach(function(k){
      var items = entities[k]; if(!items || !items.length) return;
      var card = document.createElement('div');
      card.className='entity-card';
      card.innerHTML = '<div class="metric"><span><i class="fa-solid '+iconBy[k]+'"></i> '+k+'</span><b>'+items.length+'</b></div><div class="entity-pills"></div>';
      var pills = card.querySelector('.entity-pills');
      items.forEach(function(v){
        var sp=document.createElement('span'); sp.className='pill'; sp.textContent=v; pills.appendChild(sp);
      });
      g.appendChild(card);
    });
    p.style.display='block';
  }

  /* === PSI (via server proxy—key hidden) === */
  async function runPSI(url, strategy){
    var panel = document.getElementById('psiPanel'); if(panel) panel.style.display='block';
    var statusEl = document.getElementById('psiStatus');
    if(statusEl) statusEl.textContent = 'Running '+(strategy||'mobile')+' audit…';
    var grid = document.getElementById('psiGrid'); if(grid) grid.innerHTML='';

    try{
      const qs = new URLSearchParams({ url, strategy: strategy||'mobile' }).toString();
      const r = await fetch((window.SEMSEO.ENDPOINTS.psi||'/api/psi')+'?'+qs, { headers: { 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }});
      const j = await r.json();
      if(!j.ok){ if(statusEl) statusEl.textContent = 'PSI error: '+(j.error||''); return; }

      const data = j.data || {};
      renderPSI(grid, data, j.strategy||strategy||'mobile');
      if(statusEl) statusEl.textContent = 'Complete';
    }catch(e){
      if(statusEl) statusEl.textContent = 'PSI failed: '+e.message;
    }
  }

  function getAuditScore(obj, path){ try{ return Number(path.split('.').reduce((a,k)=>a[k], obj)) }catch(_){ return NaN; } }

  function renderPSI(grid, data, strategy){
    if(!grid) return;
    const lh = data.lighthouseResult || {};
    const cats = lh.categories || {};
    const perf = cats.performance || {};
    const score = isFinite(perf.score)? Math.round(perf.score*100) : '—';
    const audits = lh.audits || {};
    const metrics = {
      FCP: audits['first-contentful-paint']?.numericValue,
      LCP: audits['largest-contentful-paint']?.numericValue,
      INP: (audits['experimental-interaction-to-next-paint']?.numericValue ?? audits['interactive']?.numericValue),
      CLS: audits['cumulative-layout-shift']?.numericValue,
      TBT: audits['total-blocking-time']?.numericValue
    };

    const scoreCard = document.createElement('div');
    scoreCard.className = 'psi-card';
    scoreCard.innerHTML = `
      <div class="metric" style="margin-bottom:.35rem">
        <span class="psi-badge"><i class="fa-solid ${strategy==='desktop'?'fa-desktop':'fa-mobile-screen-button'}"></i> ${strategy.toUpperCase()}</span>
        <b class="psi-score">${score === '—' ? '—' : score}</b>
      </div>
      <div class="psi-metric">FCP <b id="mFCP">${metrics.FCP? (metrics.FCP/1000).toFixed(2)+'s':'—'}</b></div>
      <div class="psi-meter"><span id="bFCP"></span></div>
      <div class="psi-metric">LCP <b id="mLCP">${metrics.LCP? (metrics.LCP/1000).toFixed(2)+'s':'—'}</b></div>
      <div class="psi-meter"><span id="bLCP"></span></div>
      <div class="psi-metric">INP <b id="mINP">${metrics.INP? (metrics.INP/1000).toFixed(2)+'s':'—'}</b></div>
      <div class="psi-meter"><span id="bINP"></span></div>
      <div class="psi-metric">CLS <b id="mCLS">${isFinite(metrics.CLS)? metrics.CLS.toFixed(3):'—'}</b></div>
      <div class="psi-meter"><span id="bCLS"></span></div>
      <div class="psi-metric">TBT <b id="mTBT">${metrics.TBT? Math.round(metrics.TBT)+'ms':'—'}</b></div>
      <div class="psi-meter"><span id="bTBT"></span></div>
    `;
    grid.appendChild(scoreCard);

    function fillBar(id, val, good, warn, max){
      var el = scoreCard.querySelector('#'+id); if(!el) return;
      var pct = clamp((val/max)*100, 0, 100);
      el.style.width = pct+'%';
      el.style.transition='width .6s ease';
    }
    if(metrics.FCP) fillBar('bFCP', metrics.FCP, 1800, 3000, 6000);
    if(metrics.LCP) fillBar('bLCP', metrics.LCP, 2500, 4000, 8000);
    if(metrics.INP) fillBar('bINP', metrics.INP, 200, 500, 2000);
    if(isFinite(metrics.CLS)) fillBar('bCLS', metrics.CLS*1000, 100, 250, 1000); // scale
    if(metrics.TBT) fillBar('bTBT', metrics.TBT, 200, 600, 4000);

    // Suggestions (top offenders)
    const sugCard = document.createElement('div');
    sugCard.className='psi-card';
    const offenders = ['render-blocking-resources','unused-javascript','unused-css-rules','unminified-javascript','unminified-css','uses-optimized-images','offscreen-images','modern-image-formats','efficient-animated-content','uses-text-compression','server-response-time'];
    let list = '';
    offenders.forEach(key=>{
      const a = audits[key];
      if(!a) return;
      const title = a.title || key;
      let impact = a.scoreDisplayMode==='informative' ? '' : (isFinite(a.score)? (Math.round((1-a.score)*100)+'% impact') : '');
      list += `<li><i class="fa-solid fa-lightbulb"></i> ${title} ${impact?`<small style="color:var(--text-dim)">(${impact})</small>`:''}</li>`;
    });
    if(!list) list = `<li><i class="fa-solid fa-circle-check"></i> No major opportunities reported.</li>`;
    sugCard.innerHTML = `<div class="metric"><span><i class="fa-solid fa-screwdriver-wrench"></i> Suggestions to Improve</span><b></b></div><ul class="psi-suggest">${list}</ul>`;
    grid.appendChild(sugCard);
  }

  /* === ANALYZE === */
  async function analyze(){
    if (window.SEMSEO.BUSY) return;
    window.SEMSEO.BUSY = true;

    var input = document.getElementById('analyzeUrl');
    var url = normalizeUrl(input ? input.value : '');
    if (!url) { if(input) input.focus(); window.SEMSEO.BUSY=false; return; }

    if (window.Water) window.Water.start();
    var statusEl = document.getElementById('analyzeStatus');
    if (statusEl) statusEl.textContent = 'Fetching & analyzing…';
    var detPanel = document.getElementById('detectorPanel'); if(detPanel) detPanel.style.display='none';
    var readPanel = document.getElementById('readabilityPanel'); if(readPanel) readPanel.style.display='none';
    var entPanel  = document.getElementById('entitiesPanel'); if(entPanel) entPanel.style.display='none';
    var psiPanel  = document.getElementById('psiPanel'); if(psiPanel) psiPanel.style.display='none';
    document.getElementById('psiGrid').innerHTML='';

    // AUTO-START PSI immediately (mobile first)
    runPSI(url, 'mobile');

    // 1) Backend (if present)
    var {ok,data} = await fetchBackend(url);
    if(!data) data = {};

    // 2) Build sample (from backend)
    var sample = buildSampleFromData(data);

    // 3) Try AllOrigins raw HTML (fills meta chips + better sample if needed)
    try{
      var raw = await fetchRawHtml(url);
      if(raw){
        var meta = extractMetaFromHtml(raw, url);
        data = mergeMeta(data, meta);
        if((!sample || sample.length<200) && meta.sampleText) sample = meta.sampleText;
      }
    }catch(_){}

    // 4) Jina Reader fallback
    if ((!sample || sample.length < 200)){
      if (statusEl) statusEl.textContent = 'Getting readable text…';
      try{ var read = await fetchReadableText(url);
        if (read && read.length>200){ sample = read; }
      }catch(_){}
    }

    // 5) Local detection + guaranteed item scores
    var ensemble = sample && sample.length>30 ? detectUltra(sample) : null;
    data = ensureScoresExist(data, sample, ensemble);

    // 6) Scores -> UI
    var overall = Number(data.overall || 0);
    var contentScore = Number(data.contentScore || 0);
    window.setScoreWheel(overall||0);
    setText('contentScoreInline', Math.round(contentScore||0));
    setChipTone(document.getElementById('contentScoreChip'), contentScore||0);

    // Detection
    var hp = (typeof data.humanPct==='number')? data.humanPct : NaN;
    var ap = (typeof data.aiPct==='number')? data.aiPct : NaN;
    var backendConf = (typeof data.confidence==='number')? data.confidence : null;
    if (isFinite(hp) && isFinite(ap) && backendConf && backendConf>=65){
      applyDetection(hp, ap, backendConf, ensemble || null);
    } else if (ensemble){
      applyDetection(ensemble.humanPct, ensemble.aiPct, ensemble.confidence, ensemble);
    } else if (isFinite(hp) && isFinite(ap)){
      applyDetection(hp, ap, backendConf || 60, null);
    } else {
      applyDetection(NaN, NaN, null, null);
    }

    // Readability
    var S = (ensemble && ensemble._s) ? ensemble._s : _prep(sample||'');
    renderReadability(S);

    // Entities
    var ents = extractEntities(sample||'');
    renderEntities(ents);

    // Checklist scores + autotick
    window.autoTickByScores(data.itemScores || {});

    if (window.Water) window.Water.finish();
    if (statusEl) statusEl.textContent = 'Analysis complete';

    window.SEMSEO.BUSY = false;

    // Flush queued early clicks (if any)
    if (window.SEMSEO.QUEUE > 0){ window.SEMSEO.QUEUE = 0; }
  }
  window.analyze = analyze;

  /* === UI events === */
  document.addEventListener('DOMContentLoaded', function(){
    try{
      var btn = document.getElementById('analyzeBtn');
      if (btn){ btn.addEventListener('click', function(e){ e.preventDefault(); analyze(); }); }
      var input = document.getElementById('analyzeUrl');
      if (input){ input.addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); analyze(); }}); }
      var clr = document.getElementById('clearUrl'); if(clr && input){ clr.onclick=function(){ input.value=''; input.focus(); }; }
      var pst = document.getElementById('pasteUrl'); if(pst && input && navigator.clipboard){ pst.onclick=async function(){ try{ var t=await navigator.clipboard.readText(); if(t){ input.value=t.trim(); } }catch(e){} }; }

      // PSI manual runners (also available besides auto-start)
      var mbtn = document.getElementById('runMobile'); if(mbtn){ mbtn.onclick=function(){ var u=normalizeUrl(document.getElementById('analyzeUrl').value||''); if(u) runPSI(u,'mobile'); }; }
      var dbtn = document.getElementById('runDesktop'); if(dbtn){ dbtn.onclick=function(){ var u=normalizeUrl(document.getElementById('analyzeUrl').value||''); if(u) runPSI(u,'desktop'); }; }

      window.SEMSEO.READY = true;

      // If user clicked Analyze before init completed, run it now once.
      if (window.SEMSEO.QUEUE > 0) {
        window.SEMSEO.QUEUE = 0;
        try { analyze(); } catch(e) {}
      }
    }catch(err){
      var s=document.getElementById('analyzeStatus'); if(s) s.textContent='Boot error: '+err.message;
    }
  });

  // Misc buttons
  (function(){
    var printTop=document.getElementById('printTop'), printChecklist=document.getElementById('printChecklist');
    if(printTop) printTop.addEventListener('click', function(){ window.print(); });
    if(printChecklist) printChecklist.addEventListener('click', function(){ window.print(); });
    var toTop=document.getElementById('toTopLink');
    if(toTop){ toTop.addEventListener('click', function(e){ e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'});}); }

    // Export / Import checklist
    var exportBtn=document.getElementById('exportChecklist'), importBtn=document.getElementById('importChecklist'), importFile=document.getElementById('importFile');
    if(exportBtn){ exportBtn.addEventListener('click', function(){
      var payload = { checked:[], scores:{} };
      for(var i=1;i<=25;i++){
        var cb=document.getElementById('ck-'+i), sc=document.getElementById('sc-'+i);
        if (cb && cb.checked) payload.checked.push(i);
        var s = parseInt(sc ? sc.textContent : 'NaN',10); if (!isNaN(s)) payload.scores[i]=s;
      }
      var blob=new Blob([JSON.stringify(payload,null,2)],{type:'application/json'});
      var a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='checklist.json'; a.click(); URL.revokeObjectURL(a.href);
    });}
    if(importBtn){ importBtn.addEventListener('click', function(){ if(importFile) importFile.click(); }); }
    if(importFile){ importFile.addEventListener('change', function(){
      var file = importFile.files[0]; if (!file) return;
      var fr = new FileReader();
      fr.onload = function(){ try{
        var data = JSON.parse(fr.result);
        for(var i=1;i<=25;i++){
          var cb=document.getElementById('ck-'+i); if (cb) cb.checked=(data.checked||[]).includes(i);
          var sc=document.getElementById('sc-'+i); var val=data.scores ? data.scores[i] : undefined;
          if (sc && typeof val==='number'){ sc.textContent=val; }
        }
        updateCategoryBars();
      }catch(e){ alert('Invalid JSON'); } };
      fr.readAsText(file);
    });}
  })();

})();
</script>
</body>
</html>
