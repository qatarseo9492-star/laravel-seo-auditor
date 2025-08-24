{{-- resources/views/home.blade.php — v2025-08-24 ultra+speed-auto+bottom-layout --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, UX & performance (CrUX + PSI).';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
  $analyzeJsonUrl = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl     = Route::has('analyze')      ? route('analyze')      : url('analyze');
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
:root{--bg:#07080e;--panel:#0f1022;--panel-2:#141433;--text:#f0effa;--text-dim:#b6b3d6;--good:#22c55e;--warn:#f59e0b;--bad:#ef4444;--accent:#3de2ff;--radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55);--container:1200px;--hue:0deg}
*{box-sizing:border-box}html,body{height:100%}html{scroll-behavior:smooth}
body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,Segoe UI,Roboto;background:radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),var(--bg);overflow-x:hidden}
#linesCanvas,#smokeCanvas{position:fixed;inset:0;pointer-events:none;z-index:0}
#linesCanvas{opacity:.55}
#smokeCanvas{opacity:.9;mix-blend-mode:screen}
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
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)}.l-orange{background:rgba(245,158,11,.18)}.l-green{background:rgba(34,197,94,.18)}
.chip-good{background:rgba(34,197,94,.18)!important;border-color:rgba(34,197,94,.45)!important}
.chip-mid{background:rgba(245,158,11,.18)!important;border-color:rgba(245,158,11,.45)!important}
.chip-bad{background:rgba(239,68,68,.18)!important;border-color:rgba(239,68,68,.5)!important}
.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22);border-color:rgba(22,193,114,.45)}
.score-mid{background:rgba(245,158,11,.22);border-color:rgba(245,158,11,.45)}
.score-bad{background:rgba(239,68,68,.24);border-color:rgba(239,68,68,.5)}
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem}.section-subtitle{margin:0;color:var(--text-dim)}
/* Gauge */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-gauge{position:relative;width:100%;aspect-ratio:1/1}
.gauge-svg{width:100%;height:auto;display:block}
.score-mask-rect{transition:all .6s cubic-bezier(.22,1,.36,1)}
.score-wave1{animation:scoreWave 8s linear infinite}.score-wave2{animation:scoreWave 11s linear infinite reverse}
@keyframes scoreWave{from{transform:translateX(0)}to{transform:translateX(-210px)}}
.score-text{font-size:clamp(2.2rem,4.2vw,3.1rem);font-weight:1000;fill:#fff;text-shadow:0 0 18px rgba(255,32,69,.25)}
.multiHueFast{filter:hue-rotate(var(--hue)) saturate(140%);will-change:filter}
/* URL */
.url-field{position:relative;border-radius:16px;background:#0b0d21;border:1px solid #1b1b35;box-shadow:inset 0 0 0 1px rgba(255,255,255,.02),0 12px 32px rgba(0,0,0,.32);padding:10px 110px 10px 46px;transition:.25s;overflow:hidden;isolation:isolate}
.url-field:focus-within{border-color:#5942ff;box-shadow:0 0 0 6px rgba(155,92,255,.15),inset 0 0 0 1px rgba(93,65,255,.28)}
.url-field .url-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9aa0c3;font-size:1rem;opacity:.95}
.url-field input{all:unset;color:var(--text);width:100%;font-size:1rem;letter-spacing:.2px}
.url-field .url-mini{position:absolute;top:50%;transform:translateY(-50%);border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;border-radius:10px;padding:.35rem .6rem;font-weight:900;cursor:pointer;transition:.15s}
.url-field .url-mini:hover{background:rgba(255,255,255,.12)}.url-field .url-clear{right:60px;width:36px;height:32px;display:grid;place-items:center}.url-field #pasteUrl{right:12px}
.url-field .url-border{content:"";position:absolute;inset:-2px;border-radius:inherit;padding:2px;background:conic-gradient(from 0deg,#3de2ff,#9b5cff,#ff2045,#f59e0b,#3de2ff);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.55;pointer-events:none;filter:hue-rotate(var(--hue))}
/* Panels */
.detector,.speed-panel,.insights-panel{margin-top:14px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px}
/* Readability upgraded */
.panel-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.6rem}
.panel-head h4{margin:0;font-size:1.08rem}
.kpi-row{display:grid;grid-template-columns:repeat(12,1fr);gap:.55rem}
.kpi{grid-column:span 3;background:linear-gradient(180deg,rgba(255,255,255,.08),rgba(255,255,255,.04));border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:.6rem;position:relative;overflow:hidden}
.kpi .label{font-weight:800;color:var(--text-dim);display:flex;align-items:center;gap:.4rem} 
.kpi .value{font-weight:1000;font-size:1.05rem;margin-top:.2rem}
.kpi:after{content:"";position:absolute;inset:auto -20% -30% -20%;height:46px;background:linear-gradient(90deg,rgba(61,226,255,.22),rgba(155,92,255,.22));filter:blur(18px)}
.kpi.good{background:linear-gradient(180deg,rgba(34,197,94,.19),rgba(34,197,94,.08));border-color:rgba(34,197,94,.38)}
.kpi.mid{background:linear-gradient(180deg,rgba(245,158,11,.18),rgba(245,158,11,.08));border-color:rgba(245,158,11,.38)}
.kpi.bad{background:linear-gradient(180deg,rgba(239,68,68,.18),rgba(239,68,68,.08));border-color:rgba(239,68,68,.42)}
.read-help{margin-top:.6rem;color:var(--text-dim)}
.read-help ul{margin:.35rem 0 0; padding-left:1.1rem}
/* Entities colorful */
.entity-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.55rem}
.entity-col{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.6rem}
.entity-col h5{margin:.15rem 0 .4rem;font-size:.95rem;color:var(--text-dim);display:flex;gap:.45rem;align-items:center}
.entity-chip{display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .55rem;margin:.2rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);font-weight:800}
.entity-chip i{opacity:.85}
.entity-chip[data-type="people"]{background:rgba(34,197,94,.15);border-color:rgba(34,197,94,.35)}
.entity-chip[data-type="org"]{background:rgba(155,92,255,.15);border-color:rgba(155,92,255,.35)}
.entity-chip[data-type="place"]{background:rgba(59,130,246,.15);border-color:rgba(59,130,246,.35)}
.entity-chip[data-type="software"]{background:rgba(61,226,255,.15);border-color:rgba(61,226,255,.35)}
.entity-chip[data-type="game"]{background:rgba(244,114,182,.18);border-color:rgba(244,114,182,.35)}
.entity-chip[data-type="other"]{background:rgba(255,255,255,.08);border-color:rgba(255,255,255,.16)}
/* Human vs AI bar */
.hvai{margin-top:.5rem;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.6rem}
.hvai-rail{position:relative;height:18px;border-radius:10px;background:#0b0d21;overflow:hidden;border:1px solid rgba(255,255,255,.12)}
.hvai-human,.hvai-ai{position:absolute;top:0;bottom:0;width:0}
.hvai-human{left:0;background:linear-gradient(90deg,#22c55e,#3de2ff)}
.hvai-ai{right:0;background:linear-gradient(90deg,#ef4444,#f59e0b)}
.hvai-legend{display:flex;align-items:center;justify-content:space-between;margin-top:.45rem;font-weight:900}
.hvai-legend .tag{display:inline-flex;align-items:center;gap:.35rem}
/* Speed panels */
.speed-toolbar{display:flex;flex-wrap:wrap;gap:.5rem;margin:.5rem 0}
.speed-badge{padding:.25rem .5rem;border-radius:999px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.06);font-weight:900}
.speed-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.speed-card{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.6rem}
.speed-bar{position:relative;height:14px;border-radius:10px;background:#0b0d21;border:1px solid rgba(255,255,255,.12);overflow:hidden;margin-top:.35rem}
.speed-fill{position:absolute;left:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e)}
.speed-fill.good{background:linear-gradient(90deg,#16a34a,#22c55e)}
.speed-fill.mid{background:linear-gradient(90deg,#f59e0b,#fbbf24)}
.speed-fill.bad{background:linear-gradient(90deg,#ef4444,#b91c1c)}
.speed-score{font-weight:1000;font-size:1.15rem}
.op-list{margin:.4rem 0 0;padding-left:1rem}
.op-list li{margin:.2rem 0;color:var(--text-dim);font-size:.95rem}
.sug-list{margin:.4rem 0 0;padding-left:1rem}
.sug-list li{margin:.22rem 0}
.sug-list .good{color:#16a34a}.sug-list .mid{color:#f59e0b}.sug-list .bad{color:#ef4444}
/* Progress/checklist */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.comp-water{position:relative;height:52px;border-radius:16px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
/* Share/Footer/BackToTop */
.share-dock{position:fixed;right:16px;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;gap:.5rem;z-index:85;background:rgba(10,12,28,.35);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:.5rem;backdrop-filter:blur(8px)}
.share-btn{width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;color:#fff;cursor:pointer;text-decoration:none;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s}
.share-btn:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(0,0,0,.35)}
.share-fb{background:linear-gradient(135deg,#1877F2,#1e90ff)}.share-x{background:linear-gradient(135deg,#111,#333)}.share-ln{background:linear-gradient(135deg,#0a66c2,#1a8cd8)}.share-wa{background:linear-gradient(135deg,#25D366,#128C7E)}.share-em{background:linear-gradient(135deg,#ef4444,#b91c1c)}
footer.site{margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)}
#backTop{position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none}
#backTop:hover{background:rgba(255,255,255,.12)}
/* Responsive */
@media (max-width:992px){
  .category-card{grid-column:span 12}
  .score-container{width:190px}
  .kpi{grid-column:span 4}
  .entity-col{grid-column:span 12}
  .speed-card{grid-column:span 12}
}
@media (max-width:768px){
  .wrap{padding:18px 4%}
  header.site{flex-direction:column;align-items:flex-start;gap:.6rem}
  .score-area{flex-direction:column;align-items:flex-start;gap:.8rem}
  .score-container{width:170px}
  .analyzer-grid{grid-template-columns:repeat(12,1fr)}
  .share-dock{top:auto;bottom:10px;right:50%;transform:translateX(50%);flex-direction:row;padding:.35rem .45rem;border-radius:999px;gap:.4rem;background:rgba(10,12,28,.55)}
  .share-btn{width:44px;height:44px;border-radius:999px}
}
@media (prefers-reduced-motion: reduce){
  .score-wave1,.score-wave2{animation:none!important}
}
@media print{.share-dock,#backTop,#linesCanvas,#smokeCanvas{display:none!important}}
</style>
</head>
<body>

<!-- Background canvases -->
<canvas id="linesCanvas"></canvas>
<canvas id="smokeCanvas"></canvas>

<script>
  window.SEMSEO = window.SEMSEO || {};
  window.SEMSEO.ENDPOINTS = {
    analyzeJson: @json($analyzeJsonUrl),
    analyze:     @json($analyzeUrl),
    crux:        @json(route('crux.url')),
    psi:         @json(route('psi.run'))
  };
  window.SEMSEO.READY = false;
  window.SEMSEO.BUSY = false;
  window.SEMSEO.QUEUE = 0;
  function SEMSEO_go(){
    if (window.SEMSEO.READY && typeof analyze === 'function') analyze();
    else { window.SEMSEO.QUEUE++; const s=document.getElementById('analyzeStatus'); if(s) s.textContent='Initializing…'; }
  }
</script>

<!-- Share dock -->
<div class="share-dock" aria-label="Share">
  <a id="shareFb" class="share-btn share-fb" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-facebook-f"></i></a>
  <a id="shareX"  class="share-btn share-x"  target="_blank" rel="noopener nofollow"><i class="fa-brands fa-x-twitter"></i></a>
  <a id="shareLn" class="share-btn share-ln" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-linkedin-in"></i></a>
  <a id="shareWa" class="share-btn share-wa" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-whatsapp"></i></a>
  <a id="shareEm" class="share-btn share-em" target="_blank" rel="noopener"><i class="fa-solid fa-envelope"></i></a>
</div>

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
        <div class="score-gauge">
          <svg class="gauge-svg" viewBox="0 0 200 200" aria-label="Overall score gauge">
            <defs>
              <clipPath id="scoreCircleClip"><circle cx="100" cy="100" r="88"/></clipPath>
              <clipPath id="scoreFillClip"><rect id="scoreClipRect" class="score-mask-rect" x="0" y="200" width="200" height="200"/></clipPath>
              <linearGradient id="scoreGrad" x1="0" y1="0" x2="1" y2="1"><stop id="scoreStop1" offset="0%" stop-color="#22c55e"/><stop id="scoreStop2" offset="100%" stop-color="#16a34a"/></linearGradient>
              <linearGradient id="ringGrad" x1="0" y1="0" x2="1" y2="1"><stop id="ringStop1" offset="0%" stop-color="#22c55e"/><stop id="ringStop2" offset="100%" stop-color="#16a34a"/></linearGradient>
              <filter id="ringGlow" x="-50%" y="-50%" width="200%" height="200%"><feGaussianBlur stdDeviation="2.4" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>
              <path id="scoreWavePath" d="M0 110 Q 15 90 30 110 T 60 110 T 90 110 T 120 110 T 150 110 T 180 110 T 210 110 V 220 H 0 Z"/>
            </defs>
            <circle cx="100" cy="100" r="96" fill="rgba(255,255,255,.06)" stroke="rgba(255,255,255,.12)" stroke-width="2"/>
            <circle id="ringTrack" cx="100" cy="100" r="95" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="6" transform="rotate(-90 100 100)"/>
            <circle id="ringArc" cx="100" cy="100" r="95" fill="none" stroke="url(#ringGrad)" stroke-width="6" stroke-linecap="round" filter="url(#ringGlow)" opacity=".95" transform="rotate(-90 100 100)"/>
            <g clip-path="url(#scoreCircleClip)">
              <rect x="0" y="0" width="200" height="200" fill="#0b0d21"/>
              <g clip-path="url(#scoreFillClip)">
                <g class="score-wave1 multiHueFast"><use href="#scoreWavePath" x="0" fill="url(#scoreGrad)"/><use href="#scoreWavePath" x="210" fill="url(#scoreGrad)"/></g>
                <g class="score-wave2 multiHueFast" opacity=".85"><use href="#scoreWavePath" x="0" y="6" fill="url(#scoreGrad)"/><use href="#scoreWavePath" x="210" y="6" fill="url(#scoreGrad)"/></g>
              </g>
            </g>
            <text id="overallScore" x="100" y="106" text-anchor="middle" dominant-baseline="middle" class="score-text">0%</text>
          </svg>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:.5rem">
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <span class="chip" id="overallChip"><i class="fa-solid fa-gauge-high"></i> Overall: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="contentScoreChip"><i class="fa-solid fa-file-lines"></i> Content: <b id="contentScoreInline">0</b>/100</span>

        <span class="chip" id="aiBadge" title="Confidence"><i class="fa-solid fa-user-check ico ico-green"></i> Writer: <b>—</b></span>
        <button id="viewHumanBtn" class="btn btn-ghost"><i class="fa-solid fa-user ico ico-green"></i> Human content: <b id="humanPct">—</b>%</button>
        <button id="viewAIBtn" class="btn btn-ghost"><i class="fa-solid fa-robot ico ico-red"></i> AI content: <b id="aiPct">—</b>%</button>

        <button id="copyQuick" class="btn btn-ghost"><i class="fa-regular fa-copy ico ico-cyan"></i> Copy report</button>
        </div>
        <small style="color:var(--text-dim)">If the backend returns no scores, a local ensemble + heuristics derive stable scores so the UI always reflects reality.</small>
      </div>
    </div>

    <div class="analyze-box" style="margin-top:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px">
      <form id="analyzeForm" onsubmit="event.preventDefault(); analyze(); return false;">
        <label for="analyzeUrl" style="display:inline-block;font-weight:900;margin-bottom:.35rem">Page URL</label>
        <div class="url-field" id="urlField">
          <i class="fa-solid fa-globe url-icon"></i>
          <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" aria-describedby="analyzeStatus"/>
          <button type="button" class="url-mini url-clear" id="clearUrl" title="Clear"><i class="fa-solid fa-xmark"></i></button>
          <button type="button" class="url-mini" id="pasteUrl" title="Paste">Paste</button>
          <span class="url-border" aria-hidden="true"></span>
        </div>

        <div class="analyze-row" style="display:grid;grid-template-columns:1fr auto auto auto auto;gap:.6rem;align-items:center;margin-top:.6rem">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:#9b5cff">
              <span>Auto-apply checkmarks (≥ 80)</span>
            </label>
          </div>

          <button id="analyzeBtn" type="button" onclick="SEMSEO_go()" class="btn btn-analyze">
            <i class="fa-solid fa-magnifying-glass"></i> Analyze
          </button>

          <button class="btn btn-print" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> Print</button>
          <button class="btn btn-reset" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> Reset</button>
          <button class="btn btn-export" id="exportChecklist" type="button" title="Export checklist JSON"><i class="fa-solid fa-file-export"></i> Export</button>
          <button class="btn btn-export" id="importChecklist" type="button" title="Import checklist JSON"><i class="fa-solid fa-file-import"></i> Import</button>
          <input type="file" id="importFile" accept="application/json" style="display:none">
        </div>

        <div class="water-wrap" id="waterWrap" aria-hidden="true">
          <div class="waterbar" id="waterBar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <svg class="water-svg" viewBox="0 0 600 200" preserveAspectRatio="none">
              <defs>
                <linearGradient id="waterGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#3de2ff"/><stop offset="100%" stop-color="#9b5cff"/></linearGradient>
                <clipPath id="roundClip"><rect x="1" y="1" width="598" height="198" rx="18" ry="18"/></clipPath>
                <clipPath id="fillClip"><rect id="waterClipRect" class="score-mask-rect" x="0" y="200" width="600" height="200"/></clipPath>
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
            <span class="chip">Auto-checked: <b id="rAutoCount">—</b></span>
          </div>
        </div>
      </form>
    </div>

    <!-- Detector -->
    <section id="detectorPanel" class="insights-panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-wave-square ico ico-purple"></i>
        <h4>Ultra Content Detection (Ensemble)</h4>
      </div>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.4rem">
        <span class="chip"><i class="fa-solid fa-shield-halved ico"></i> Confidence: <b id="detConfidence">—</b>%</span>
        <span class="chip"><i class="fa-solid fa-circle-info ico"></i> Higher bar = more <b>AI-like</b> for that detector</span>
      </div>
      <div class="hvai">
        <div class="hvai-rail">
          <div id="hvaiHuman" class="hvai-human"></div>
          <div id="hvaiAI" class="hvai-ai"></div>
        </div>
        <div class="hvai-legend">
          <span class="tag"><i class="fa-solid fa-user"></i> Human content: <b id="hvaiHumanVal">—</b>%</span>
          <span class="tag"><i class="fa-solid fa-robot"></i> AI content: <b id="hvaiAIVal">—</b>%</span>
        </div>
      </div>
      <div class="det-grid" id="detGrid" style="display:grid;grid-template-columns:repeat(12,1fr);gap:.5rem;margin-top:.6rem"></div>
      <div class="det-note" id="detNote" style="margin-top:.35rem;color:var(--text-dim);font-size:.85rem">Local ensemble activates if the backend provides no text/percentages.</div>
    </section>

    <!-- Readability (upgraded) -->
    <section id="readabilityPanel" class="insights-panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-book-open"></i>
        <h4>Readability Insights</h4>
      </div>
      <div class="kpi-row" id="readKpis">
        <div class="kpi" id="kpi-flesch"><div class="label"><i class="fa-solid fa-pen-to-square"></i> Flesch Reading Ease</div><div class="value" id="riFlesch">—</div></div>
        <div class="kpi" id="kpi-words"><div class="label"><i class="fa-solid fa-text-width"></i> Word Count</div><div class="value" id="riWords">—</div></div>
        <div class="kpi" id="kpi-ttr"><div class="label"><i class="fa-solid fa-fingerprint"></i> Type/Token Ratio</div><div class="value" id="riTTR">—</div></div>
        <div class="kpi" id="kpi-rep"><div class="label"><i class="fa-solid fa-repeat"></i> Repetition (lower is better)</div><div class="value" id="riRep">—</div></div>
        <div class="kpi" id="kpi-long"><div class="label"><i class="fa-solid fa-paragraph"></i> Long Sentences %</div><div class="value" id="riLong">—</div></div>
        <div class="kpi" id="kpi-len"><div class="label"><i class="fa-solid fa-italic"></i> Avg Word Length</div><div class="value" id="riLen">—</div></div>
        <div class="kpi" id="kpi-dig"><div class="label"><i class="fa-solid fa-hashtag"></i> Digits per 100 words</div><div class="value" id="riDigits">—</div></div>
      </div>
      <div class="read-help">
        <b>What this means</b>
        <ul>
          <li><b>Flesch ≥ 60</b> reads well for the web. Break long sentences and prefer everyday words.</li>
          <li><b>Type/Token Ratio</b> gauges vocabulary variety—diverse but not overly complex is best.</li>
          <li><b>Repetition</b> shows overused phrasing—reword and add examples.</li>
        </ul>
      </div>
    </section>

    <!-- Entities & Topics (colorful, with software/games) -->
    <section id="entitiesPanel" class="insights-panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-database"></i>
        <h4>Entities & Topics</h4>
      </div>
      <div class="entity-grid">
        <div class="entity-col"><h5><i class="fa-solid fa-user"></i> People</h5><div id="entPeople"></div></div>
        <div class="entity-col"><h5><i class="fa-solid fa-building"></i> Organizations</h5><div id="entOrgs"></div></div>
        <div class="entity-col"><h5><i class="fa-solid fa-location-dot"></i> Locations</h5><div id="entPlaces"></div></div>
        <div class="entity-col"><h5><i class="fa-solid fa-mobile-screen-button"></i> Software / Apps / APK</h5><div id="entSoftware"></div></div>
        <div class="entity-col"><h5><i class="fa-solid fa-gamepad"></i> Games / Products</h5><div id="entGames"></div></div>
        <div class="entity-col"><h5><i class="fa-solid fa-tags"></i> Topics / Other</h5><div id="entOther"></div></div>
      </div>
      <small style="color:var(--text-dim)">Heuristic, on-device extraction for demo; backend NER can enhance precision.</small>
    </section>

    <!-- Speed Panel (Top / Compact) -->
    <section id="speedPanelTop" class="speed-panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-gauge-high"></i>
        <h4>Site Speed & Core Web Vitals (Compact)</h4>
      </div>
      <div class="speed-toolbar">
        <span class="speed-badge">Mode: <b id="speed-mode-Top">—</b></span>
        <label class="chip" style="cursor:pointer">
          <input id="speedStrategyMobileTop" type="radio" name="speedStrategyTop" value="mobile" checked style="accent-color:#9b5cff;margin-right:.35rem"> Mobile
        </label>
        <label class="chip" style="cursor:pointer">
          <input id="speedStrategyDesktopTop" type="radio" name="speedStrategyTop" value="desktop" style="accent-color:#9b5cff;margin-right:.35rem"> Desktop
        </label>
        <button id="runSpeedTop" class="btn btn-ghost"><i class="fa-solid fa-bolt"></i> Run Speed Test</button>
      </div>
      <div class="speed-grid">
        <div class="speed-card">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:.4rem">
            <div><b>Core Web Vitals (p75)</b> <small id="cruxTypeTop" style="color:var(--text-dim)"></small></div>
            <div class="speed-score">LCP: <span id="cwvLcpTop">—</span> • INP: <span id="cwvInpTop">—</span> • CLS: <span id="cwvClsTop">—</span></div>
          </div>
          <div style="margin-top:.4rem">
            <div>Largest Contentful Paint</div>
            <div class="speed-bar"><div id="bar-lcp-Top" class="speed-fill"></div></div>
          </div>
          <div style="margin-top:.4rem">
            <div>Interaction to Next Paint</div>
            <div class="speed-bar"><div id="bar-inp-Top" class="speed-fill"></div></div>
          </div>
          <div style="margin-top:.4rem">
            <div>Cumulative Layout Shift</div>
            <div class="speed-bar"><div id="bar-cls-Top" class="speed-fill"></div></div>
          </div>
        </div>

        <div class="speed-card">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:.4rem">
            <div><b>Lighthouse Categories</b> <small id="psiStampTop" style="color:var(--text-dim)"></small></div>
            <div class="speed-score">Perf <span id="sc-perf-Top">—</span> • SEO <span id="sc-seo-Top">—</span> • Acc <span id="sc-acc-Top">—</span> • Best <span id="sc-best-Top">—</span></div>
          </div>
          <ul class="op-list" id="psiOppsTop"></ul>
        </div>
      </div>
    </section>

    <!-- Enhanced Speed Panel (Bottom / Detailed) -->
    <section id="speedPanelBottom" class="speed-panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-gauge"></i>
        <h4>Site Speed & Core Web Vitals — Detailed Summary</h4>
      </div>
      <div class="speed-toolbar">
        <span class="speed-badge">Mode: <b id="speed-mode-Bottom">—</b></span>
        <label class="chip" style="cursor:pointer">
          <input id="speedStrategyMobileBottom" type="radio" name="speedStrategyBottom" value="mobile" checked style="accent-color:#9b5cff;margin-right:.35rem"> Mobile
        </label>
        <label class="chip" style="cursor:pointer">
          <input id="speedStrategyDesktopBottom" type="radio" name="speedStrategyBottom" value="desktop" style="accent-color:#9b5cff;margin-right:.35rem"> Desktop
        </label>
        <button id="runSpeedBottom" class="btn btn-ghost"><i class="fa-solid fa-rotate"></i> Refresh</button>
      </div>
      <div class="speed-grid">
        <div class="speed-card">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:.4rem">
            <div><b>Core Web Vitals (p75)</b> <small id="cruxTypeBottom" style="color:var(--text-dim)"></small></div>
            <div class="speed-score">LCP: <span id="cwvLcpBottom">—</span> • INP: <span id="cwvInpBottom">—</span> • CLS: <span id="cwvClsBottom">—</span></div>
          </div>
          <div style="margin-top:.4rem">
            <div>Largest Contentful Paint</div>
            <div class="speed-bar"><div id="bar-lcp-Bottom" class="speed-fill"></div></div>
          </div>
          <div style="margin-top:.4rem">
            <div>Interaction to Next Paint</div>
            <div class="speed-bar"><div id="bar-inp-Bottom" class="speed-fill"></div></div>
          </div>
          <div style="margin-top:.4rem">
            <div>Cumulative Layout Shift</div>
            <div class="speed-bar"><div id="bar-cls-Bottom" class="speed-fill"></div></div>
          </div>
        </div>

        <div class="speed-card">
          <div style="display:flex;align-items:center;justify-content:space-between;gap:.4rem">
            <div><b>Lighthouse Categories</b> <small id="psiStampBottom" style="color:var(--text-dim)"></small></div>
            <div class="speed-score">Perf <span id="sc-perf-Bottom">—</span> • SEO <span id="sc-seo-Bottom">—</span> • Acc <span id="sc-acc-Bottom">—</span> • Best <span id="sc-best-Bottom">—</span></div>
          </div>
          <ul class="op-list" id="psiOppsBottom"></ul>
          <div style="margin-top:.6rem"><b>Actionable Suggestions</b></div>
          <ul class="sug-list" id="psiSugsBottom"></ul>
        </div>
      </div>
    </section>

    {{-- Checklist (unchanged core) --}}
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
        <svg class="comp-svg" viewBox="0 0 600 140" preserveAspectRatio="none" style="position:absolute;inset:0;width:100%;height:100%;z-index:1">
          <defs>
            <clipPath id="compRound"><rect x="1" y="1" width="598" height="138" rx="14" ry="14"/></clipPath>
            <clipPath id="compFillClip"><rect id="compClipRect" x="0" y="0" width="0" height="140"/></clipPath>
            <linearGradient id="compGrad" x1="0" y1="0" x2="1" y2="1"><stop id="compStop1" offset="0%" stop-color="#3de2ff"/><stop id="compStop2" offset="100%" stop-color="#9b5cff"/></linearGradient>
            <path id="compWave" d="M0 80 Q 50 60 100 80 T 200 80 T 300 80 T 400 80 T 500 80 T 600 80 V 160 H 0 Z"/>
          </defs>
          <g clip-path="url(#compRound)">
            <rect x="0" y="0" width="600" height="140" fill="#0b0d21"/>
            <g clip-path="url(#compFillClip)">
              <g class="comp-wave1 multiHue"><use href="#compWave" x="0" fill="url(#compGrad)"/><use href="#compWave" x="600" fill="url(#compGrad)"/></g>
              <g class="comp-wave2 multiHue" opacity=".75"><use href="#compWave" x="0" y="6" fill="url(#compGrad)"/><use href="#compWave" x="600" y="6" fill="url(#compGrad)"/></g>
            </g>
          </g>
        </svg>
        <div class="comp-pct" style="position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1rem;z-index:4;text-shadow:0 1px 0 rgba(0,0,0,.45)"><span id="compPct">0%</span></div>
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
              <div class="cat-water" id="catWater-{{ $loop->index }}" style="grid-column:1/-1;margin-top:.55rem;position:relative;height:22px">
                <svg class="cat-svg" viewBox="0 0 600 24" preserveAspectRatio="none" style="display:block;width:100%;height:22px">
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
                <div class="cat-water-pct" id="catPct-{{ $loop->index }}" style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.8rem;color:rgba(255,255,255,.9);text-shadow:0 1px 0 rgba(0,0,0,.55)">0/0 • 0%</div>
              </div>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">{{ $c[2]-$c[1]+1 }}</span></span>
          </header>
          <ul class="checklist" style="list-style:none;margin:10px 0 0;padding:0">
            @for($i=$c[1];$i<=$c[2];$i++)
              <li class="checklist-item" style="display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.7rem .75rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02)),radial-gradient(100% 120% at 0% 0%,rgba(61,226,255,.06),transparent 30%),radial-gradient(120% 100% at 100% 0%,rgba(155,92,255,.05),transparent 35%);transition:box-shadow .25s,background .25s,transform .12s">
                <label><input type="checkbox" id="ck-{{ $i }}"> <span>{{ $labels[$i] }}</span></label>
                <span class="score-badge" id="sc-{{ $i }}">—</span>
                <button class="btn btn-ghost improve-btn" type="button" data-id="ck-{{ $i }}">Improve</button>
              </li>
            @endfor
          </ul>
        </article>
      @endforeach
    </div>
  </main>
</div>

<footer class="site">
  <div><strong>Semantic SEO Master</strong></div>
  <div class="footer-links">
    <a href="#analyzer">Analyzer</a>
    <a href="#" id="toTopLink">Back to top</a>
  </div>
</footer>

<button id="backTop" title="Back to top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

<!-- ======================= A) Core Logic (Analyze + Panels) ======================= -->
<script>
(function(){
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  const setText=(id,val)=>{ const el=document.getElementById(id); if(el) el.textContent=val; return el; };
  const setChipTone=(el,v)=>{ if(!el) return; el.classList.remove('chip-good','chip-mid','chip-bad'); const n=Number(v)||0; el.classList.add(n>=80?'chip-good':(n>=60?'chip-mid':'chip-bad')); };
  const badgeTone=(el,v)=>{ if(!el) return; el.classList.remove('score-good','score-mid','score-bad'); el.classList.add(v>=80?'score-good':(v>=60?'score-mid':'score-bad')); };

  /* Gauge */
  const GAUGE={rect:null,stop1:null,stop2:null,r1:null,r2:null,arc:null,text:null,H:200,CIRC:2*Math.PI*95};
  window.setScoreWheel=function(value){
    if(!GAUGE.rect){
      GAUGE.rect=document.getElementById('scoreClipRect'); GAUGE.stop1=document.getElementById('scoreStop1'); GAUGE.stop2=document.getElementById('scoreStop2');
      GAUGE.r1=document.getElementById('ringStop1'); GAUGE.r2=document.getElementById('ringStop2'); GAUGE.arc=document.getElementById('ringArc'); GAUGE.text=document.getElementById('overallScore');
      if(GAUGE.arc){ GAUGE.arc.style.strokeDasharray=GAUGE.CIRC.toFixed(2); GAUGE.arc.style.strokeDashoffset=GAUGE.CIRC.toFixed(2); }
    }
    const v=Math.max(0,Math.min(100,Number(value)||0));
    const y=GAUGE.H-(GAUGE.H*(v/100));
    if(GAUGE.rect) GAUGE.rect.setAttribute('y',String(y));
    if(GAUGE.text) GAUGE.text.textContent=Math.round(v)+'%';
    let c1,c2; if(v>=80){c1='#22c55e';c2='#16a34a'} else if(v>=60){c1='#f59e0b';c2='#fb923c'} else {c1='#ef4444';c2='#b91c1c'}
    if(GAUGE.stop1) GAUGE.stop1.setAttribute('stop-color',c1); if(GAUGE.stop2) GAUGE.stop2.setAttribute('stop-color',c2);
    if(GAUGE.r1) GAUGE.r1.setAttribute('stop-color',c1); if(GAUGE.r2) GAUGE.r2.setAttribute('stop-color',c2);
    if(GAUGE.arc){ const offset=GAUGE.CIRC*(1-(v/100)); GAUGE.arc.style.strokeDashoffset=offset.toFixed(2); }
    setText('overallScoreInline',Math.round(v)); setChipTone(document.getElementById('overallChip'),v);
  };

  /* Checklist completion */
  function updateCategoryBars(){
    const cards=[...document.querySelectorAll('.category-card')];
    let total=0, checked=0;
    cards.forEach((card,idx)=>{
      const items=[...card.querySelectorAll('.checklist-item')];
      const t=items.length, done=items.filter(li=>{ const c=li.querySelector('input'); return c && c.checked; }).length;
      total+=t; checked+=done;
      const pct=t?Math.round(done*100/t):0;
      const fill=document.getElementById('catFillRect-'+idx); if(fill) fill.setAttribute('width', String(6*pct));
      const pctEl=document.getElementById('catPct-'+idx); if(pctEl) pctEl.textContent = done+'/'+t+' • '+pct+'%';
      const sub=card.querySelector('.category-sub'); if(sub) sub.textContent = pct>=80?'Great progress':'Keep improving';
      const cnt=card.querySelector('.checked-count'); if(cnt) cnt.textContent = done;
      const stop1=document.getElementById('catStop1-'+idx), stop2=document.getElementById('catStop2-'+idx);
      const c1=pct>=80?'#22c55e':(pct>=60?'#f59e0b':'#ef4444'); const c2=pct>=80?'#16a34a':(pct>=60?'#fb923c':'#b91c1c');
      if(stop1) stop1.setAttribute('stop-color',c1); if(stop2) stop2.setAttribute('stop-color',c2);
    });
    const pctAll = total? Math.round(checked*100/total) : 0;
    const comp=document.getElementById('compClipRect'); if(comp) comp.setAttribute('width', String(6*pctAll));
    setText('compPct', pctAll + '%'); setText('progressCaption', checked+' of '+total+' items completed');
  }
  window.updateCategoryBars = updateCategoryBars;

  /* Auto-tick */
  window.autoTickByScores=function(map){
    let autoCount=0;
    for(let i=1;i<=25;i++){
      const scVal=Number((map && map[i]!==undefined)? map[i] : NaN);
      const badge=document.getElementById('sc-'+i);
      const cb=document.getElementById('ck-'+i);
      const row=cb ? cb.closest('.checklist-item') : null;
      if (!badge) continue;
      if (!isNaN(scVal)) {
        badge.textContent = Math.round(scVal);
        badgeTone(badge, scVal);
        if (document.getElementById('autoApply')?.checked && scVal>=80) {
          if (cb && !cb.checked) { cb.checked=true; autoCount++; }
          row?.classList.remove('sev-mid','sev-bad'); row?.classList.add('sev-good');
        } else if (scVal>=60) { row?.classList.remove('sev-bad','sev-good'); row?.classList.add('sev-mid'); }
        else { row?.classList.remove('sev-mid','sev-good'); row?.classList.add('sev-bad'); }
      } else { badge.textContent='—'; badge.classList.remove('score-good','score-mid','score-bad'); }
    }
    setText('rAutoCount', autoCount);
    updateCategoryBars();
  };

  /* Water */
  const Water=(function(){
    const clipId=()=>document.getElementById('waterClipRect');
    const pctId=()=>document.getElementById('waterPct');
    const wrap=()=>document.getElementById('waterWrap');
    let t=null, value=0;
    function show(){ const w=wrap(); if(w) w.style.display='block'; }
    function hide(){ const w=wrap(); if(w) w.style.display='none'; }
    function set(v){ value=Math.max(0,Math.min(100,v)); const y=200 - (200*value/100); const clip=clipId(); if(clip) clip.setAttribute('y', String(y)); const p=pctId(); if(p) p.textContent = Math.round(value) + '%'; }
    return {
      start(){ show(); set(0); if(t) clearInterval(t); t=setInterval(()=>{ if(value<88) set(value+2); }, 80); },
      finish(){ if(t) clearInterval(t); setTimeout(()=>{ set(100); }, 150); setTimeout(()=>{ hide(); }, 800); },
      reset(){ if(t) clearInterval(t); set(0); hide(); }
    };
  })();
  window.Water=Water;

  /* Fetch helpers */
  function normalizeUrl(u){ if(!u) return ''; u=u.trim(); if(/^https?:\/\//i.test(u)){ try{ new URL(u); return u; }catch(e){ return ''} } const g='https://'+u.replace(/^\/+/,''); try{ new URL(g); return g; }catch(e){ return ''} }
  async function fetchBackend(url){
    let data=null, ok=false, status=0, text='';
    const qs=new URLSearchParams({url}).toString();
    try{ const r1=await fetch((window.SEMSEO.ENDPOINTS.analyzeJson||'analyze-json')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); status=r1.status; text=await r1.text(); try{ data=JSON.parse(text);}catch(_){} if(r1.ok&&data) ok=true; }catch(_){}
    if(!ok){ try{ const r2=await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze'),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body:JSON.stringify({url,_token:CSRF})}); status=r2.status; text=await r2.text(); try{ data=JSON.parse(text);}catch(_){} if(r2.ok&&data) ok=true; }catch(_){}
    if(!ok){ try{ const r3=await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); status=r3.status; text=await r3.text(); try{ data=JSON.parse(text);}catch(_){} if(r3.ok&&data) ok=true; }catch(_){}
    return {ok,data,status};
  }
  async function fetchRawHtml(url){ try{ const r=await fetch('https://api.allorigins.win/raw?url='+encodeURIComponent(url),{cache:'no-store'}); if(r.ok){ const html=await r.text(); if(html && html.length>200) return html; } }catch(_){ } return ''; }
  async function fetchReadableText(url){
    try{ const httpsR = await fetch('https://r.jina.ai/http/'+url.replace(/^https?:\/\//,'')); if(httpsR.ok){ const t = await httpsR.text(); if(t && t.length>200) return t; } }catch(e){}
    try{ const altR = await fetch('https://r.jina.ai/'+url); if(altR.ok){ const t = await altR.text(); if(t && t.length>200) return t; } }catch(e){}
    return '';
  }
  function extractMetaFromHtml(html, baseUrl){
    try{
      const d=(new DOMParser()).parseFromString(html,'text/html');
      const q=(s,a)=>{const el=d.querySelector(s); return el?(a?el.getAttribute(a)||'':(el.textContent||'')) : '';};
      const title=(q('title')||'').trim();
      const metaDesc=(q('meta[name="description"]','content')||'').trim();
      const canonical=(q('link[rel="canonical"]','href')||'').trim()||baseUrl;
      const robots=(q('meta[name="robots"]','content')||'').trim()||'n/a';
      const viewport=(q('meta[name="viewport"]','content')||'').trim()||'n/a';
      const h1=d.querySelectorAll('h1').length, h2=d.querySelectorAll('h2').length, h3=d.querySelectorAll('h3').length;
      let origin=''; try{ origin=new URL(baseUrl).origin; }catch(_){}
      let internal=0; d.querySelectorAll('a[href]').forEach(a=>{ try{ const u=new URL(a.getAttribute('href'), baseUrl); if(!origin || u.origin===origin) internal++; }catch(_){} });
      const schema = !!(d.querySelector('script[type="application/ld+json"]') || d.querySelector('[itemscope],[itemtype*="schema.org"]'));
      let main=d.querySelector('article,main,[role="main"]'); let sample=main?(main.textContent||''):'';
      if(!sample){ sample=[...d.querySelectorAll('p')].slice(0,12).map(p=>p.textContent).join('\n\n'); }
      sample=(sample||'').replace(/\s{2,}/g,' ').trim();
      return { titleLen: title?title.length:null, metaLen: metaDesc?metaDesc.length:null, canonical, robots, viewport, headings:(h1+'/'+h2+'/'+h3), internalLinks:internal, schema: schema?'yes':'no', sampleText: sample };
    }catch(_){ return {}; }
  }
  function mergeMeta(into, add){
    if(!into) into={};
    ['titleLen','metaLen','canonical','robots','viewport','headings','internalLinks','schema','sampleText'].forEach(k=>{
      if((into[k]==null || into[k]==='—' || into[k]==='') && add && add[k]!=null) into[k]=add[k];
    });
    return into;
  }

  /* Stylometry & readability core */
  const clamp=(v,min,max)=> v<min?min:(v>max?max:v);
  function _countSyllables(word){ const w=(word||'').toLowerCase().replace(/[^a-z]/g,''); if(!w) return 0; let m=(w.match(/[aeiouy]+/g)||[]).length; if(/(ed|es)$/.test(w)) m--; if(/^y/.test(w)) m--; return Math.max(1,m); }
  function _flesch(text){
    const sents = (text.match(/[.!?]+/g)||[]).length || 1;
    const words = (text.match(/[A-Za-z\u00C0-\u024f']+/g)||[]); const wN = words.length||1;
    let syll = 0; for(let i=0;i<words.length;i++) syll += _countSyllables(words[i]);
    return clamp(206.835 - 1.015*(wN/sents) - 84.6*(syll/wN), -20, 120);
  }
  function _prep(text){
    text=(text||'')+''; text=text.replace(/\u00A0/g,' ').replace(/\s+/g,' ').trim();
    const wordRe=/[A-Za-z\u00C0-\u024f0-9']+/g; const words=(text.match(wordRe)||[]).map(w=>w.toLowerCase());
    const sents=text.split(/(?<=[.!?])\s+|\n+(?=\S)/g).filter(Boolean); const tokens=words.length||1;
    const freq=Object.create(null); words.forEach(w=>{freq[w]=(freq[w]||0)+1;});
    const types=Object.keys(freq).length, hapax=Object.values(freq).filter(v=>v===1).length;
    const lens=sents.map(s=>(s.match(wordRe)||[]).length).filter(v=>v>0);
    const mean=lens.reduce((a,b)=>a+b,0)/(lens.length||1);
    const variance=lens.reduce((a,b)=>a+Math.pow(b-mean,2),0)/(lens.length||1);
    const cov=mean?Math.sqrt(variance)/mean:0;
    let tri={}, triT=0, triR=0; for(let i=0;i<tokens-2;i++){ const g=words[i]+' '+words[i+1]+' '+words[i+2]; tri[g]=(tri[g]||0)+1; triT++; } for(const kk in tri){ if(tri[kk]>1) triR+=tri[kk]-1; }
    const digits=(text.match(/\d/g)||[]).length*100/(tokens||1);
    const avgLen=tokens? (words.join('').length/tokens):0;
    const longRatio=(lens.filter(L=>L>=28).length)/(lens.length||1);
    const TTR=types/(tokens||1);
    return { text, wordCount:tokens, flesch:_flesch(text), cov, longRatio, triRepeatRatio: triT?triR/triT:0, TTR, hapaxRatio: types?hapax/types:0, avgWordLen:avgLen, digitsPer100:digits };
  }
  function detectUltra(text){
    const s=_prep(text||'');
    if (s.wordCount < 40){ const aiQuick = clamp(70 - s.wordCount*0.8, 20, 70); return { humanPct: 100-aiQuick, aiPct: aiQuick, confidence: 46, detectors: [] , _s:s }; }
    let ai=10; const covT=0.45; if(s.cov<covT) ai+=clamp((covT-s.cov)/covT,0,1)*25; const ttrT=0.45; if(s.TTR<ttrT) ai+=clamp((ttrT-s.TTR)/ttrT,0,1)*18;
    const conf = clamp(50 + Math.min(45, Math.log((s.wordCount||1)+1)*7), 45, 95);
    return { humanPct: 100-clamp(Math.round(ai),0,100), aiPct: clamp(Math.round(ai),0,100), confidence: conf, detectors: [{key:'stylometry',label:'Stylometry',ai:clamp(Math.round(ai),0,100),w:1}], _s:s };
  }
  function deriveItemScoresFromSignals(s){
    const pct=x=>clamp(Math.round(x),0,100);
    const band=(x,l,h)=>{ if (x<=l) return 0; if (x>=h) return 100; return (x-l)*100/(h-l); };
    const read=pct(band(s.flesch,35,75)), rep=pct(100*(1 - s.triRepeatRatio)), ttr=pct(band(s.TTR,0.30,0.65)), longS=pct(band(1-s.longRatio, 0.6, 0.95)), avgLen=pct(band(s.avgWordLen,4.2,5.8)), digits=pct(100*(1 - s.digitsPer100/20));
    const i=[];
    i[1]=pct(.5*read+.5*ttr); i[2]=pct(.6*ttr+.4*avgLen); i[3]=pct(.4*ttr+.6*read); i[4]=pct(.7*read+.3*rep); i[5]=pct(.5*read+.5*avgLen);
    i[6]=pct(.4*ttr+.6*read); i[7]=pct(.4*read+.6*rep); i[8]=pct(.6*rep+.4*digits); i[9]=pct(.6*avgLen+.4*digits); i[10]=pct(.6*avgLen+.4*ttr);
    i[11]=pct(.5*ttr+.5*rep); i[12]=pct(.6*rep+.4*digits); i[13]=pct(.6*read+.4*rep); i[14]=pct(.6*read+.4*ttr); i[15]=pct(.5*ttr+.5*read);
    i[16]=pct(.6*digits+.4*read); i[17]=pct(.5*avgLen+.5*ttr); i[18]=pct(.5*read+.5*longS); i[19]=pct(.6*rep+.4*avgLen); i[20]=pct(.5*longS+.5*avgLen);
    i[21]=pct(.7*read+.3*ttr); i[22]=pct(.6*ttr+.4*avgLen); i[23]=pct(.6*ttr+.4*avgLen); i[24]=pct(.6*avgLen+.4*ttr); i[25]=pct(.6*ttr+.4*digits);
    const map={}; for(let k=1;k<=25;k++) map[k]=i[k]; return map;
  }
  function deriveSummaryScoresFromItems(itemMap){
    const all=[]; for(let i=1;i<=25;i++){ if(isFinite(itemMap[i])) all.push(itemMap[i]); }
    const avg=a=> a.length? Math.round(a.reduce((x,y)=>x+y,0)/a.length) : 0;
    return { contentScore: avg(all.slice(0,13)), overall: avg(all) };
  }
  function buildSampleFromData(data){
    const parts = [];
    ['textSample','extractedText','plainText','body','sample','content','text'].forEach(k=>{ if(typeof data?.[k]==='string' && data[k].length>0) parts.push(data[k]); });
    ['title','meta','description','ogDescription','firstParagraph','snippet','h1','h2','h3'].forEach(k=>{
      const v = data?.[k];
      if (typeof v === 'string' && v.trim()) parts.push(v);
      if (Array.isArray(v)) parts.push(v.join('. '));
    });
    const txt = parts.join('\n\n').replace(/\s{2,}/g,' ').trim();
    return txt.length>140000 ? txt.slice(0,140000) : txt;
  }
  function ensureScoresExist(data, sample, ensemble){
    const needItems = !data.itemScores || Object.keys(data.itemScores).length===0;
    const needContent = typeof data.contentScore!=='number' || isNaN(data.contentScore);
    const needOverall = typeof data.overall!=='number' || isNaN(data.overall);
    const s = (ensemble && ensemble._s) ? ensemble._s : _prep(sample||'');
    if (needItems) data.itemScores = deriveItemScoresFromSignals(s);
    if (needContent || needOverall){
      const sums = deriveSummaryScoresFromItems(data.itemScores||{});
      if (needContent) data.contentScore = sums.contentScore;
      if (needOverall) data.overall = sums.overall;
    }
    return data;
  }

  /* Detector + HVAI bar */
  function renderDetectors(res){
    const panel = document.getElementById('detectorPanel'); if(!panel) return;
    const grid = document.getElementById('detGrid'); const confEl = document.getElementById('detConfidence');
    if(confEl) confEl.textContent = isFinite(res.confidence)? Math.round(res.confidence): '—';
    panel.style.display = 'block'; if(!grid) return; grid.innerHTML = '';
    (res.detectors||[{key:'stylometry',label:'Stylometry',ai:res.aiPct||0}]).forEach(d=>{
      const id='det-'+d.key; const wrap=document.createElement('div');
      wrap.className='det-item'; wrap.style.cssText='grid-column:span 6;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.55rem .6rem';
      wrap.innerHTML =
        '<div style="display:grid;grid-template-columns:1fr auto;gap:.5rem;align-items:center"><div class="det-label" style="font-weight:800;color:var(--text-dim)">'+d.label+'</div><div class="det-score" id="'+id+'-score">'+(d.ai||0)+'</div></div>'+
        '<div class="det-bar" style="margin-top:.35rem;position:relative;height:14px;border-radius:10px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)"><div class="det-fill" id="'+id+'-fill" style="position:absolute;left:0;top:0;bottom:0;width:'+(clamp(d.ai||0,0,100))+'%;background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e);transition:width .35s ease"></div></div>';
      grid.appendChild(wrap);
    });
  }
  function applyDetection(humanPct, aiPct, confidence, breakdown){
    const writer = (isFinite(humanPct) && isFinite(aiPct) && humanPct>=aiPct) ? 'Likely Human' : 'AI-like';
    const badge = document.getElementById('aiBadge'); if (badge){ const b=badge.querySelector('b'); if(b) b.textContent = writer; badge.title = 'Confidence: ' + (confidence? confidence+'%':'—'); }
    const hp = document.getElementById('humanPct'), ap = document.getElementById('aiPct');
    if(hp) hp.textContent = isFinite(humanPct)? Math.round(humanPct) : '—';
    if(ap) ap.textContent = isFinite(aiPct)? Math.round(aiPct)   : '—';
    const H=document.getElementById('hvaiHuman'), A=document.getElementById('hvaiAI'), HV=document.getElementById('hvaiHumanVal'), AV=document.getElementById('hvaiAIVal');
    if(H && A){
      const h=clamp(Math.round(humanPct||0),0,100), a=clamp(Math.round(aiPct||0),0,100);
      H.style.transition='width .6s cubic-bezier(.22,1,.36,1)'; A.style.transition='width .6s cubic-bezier(.22,1,.36,1)';
      H.style.width = h+'%'; A.style.width = a+'%';
      if(HV) HV.textContent = h; if(AV) AV.textContent = a;
    }
    if (breakdown && breakdown.detectors){ renderDetectors(breakdown); }
    document.getElementById('detectorPanel').style.display='block';
  }

  /* Readability UI */
  function renderReadability(s){
    const show=()=>{ const p=document.getElementById('readabilityPanel'); if(p) p.style.display='block'; };
    const setKpi=(id,val,band)=>{
      const box=document.getElementById(id);
      if(!box) return;
      const vEl = box.querySelector('.value'); if(vEl) vEl.textContent = val;
      box.classList.remove('good','mid','bad');
      if(!band) return;
      const num = parseFloat((val+'').replace(/[^\d.-]/g,'')); if(isNaN(num)) return;
      box.classList.add(num>=band.good? 'good' : (num>=band.mid? 'mid' : 'bad'));
    };
    setText('riFlesch', Math.round(s.flesch)); setKpi('kpi-flesch', Math.round(s.flesch), {good:60, mid:45});
    setText('riWords', s.wordCount); setKpi('kpi-words', s.wordCount, {good:800, mid:400});
    setText('riTTR', (s.TTR*100).toFixed(1)+'%'); setKpi('kpi-ttr', (s.TTR*100).toFixed(1), {good:45, mid:35});
    setText('riRep', (s.triRepeatRatio*100).toFixed(1)+'%'); setKpi('kpi-rep', (s.triRepeatRatio*100).toFixed(1), {good:96, mid:90}); // inverted
    setText('riLong', (s.longRatio*100).toFixed(1)+'%'); setKpi('kpi-long', (s.longRatio*100).toFixed(1), {good:10, mid:20});
    setText('riLen', s.avgWordLen.toFixed(2)); setKpi('kpi-len', s.avgWordLen.toFixed(2), {good:5.5, mid:5.0});
    setText('riDigits', s.digitsPer100.toFixed(1)); setKpi('kpi-dig', s.digitsPer100.toFixed(1), {good:4, mid:8});
    show();
  }

  /* Entities (colorful + software/games) */
  function extractEntitiesHeuristics(text){
    const ents={people:[], orgs:[], places:[], software:[], games:[], other:[]};
    const proper=/\b([A-Z][a-zA-Z0-9]+(?:\s+[A-Z][a-zA-Z0-9]+){0,3})\b/g;
    const lowerTokens=(text||'').split(/\s+/);
    const freq=Object.create(null);
    let m; while((m=proper.exec(text))!==null){ const k=m[1].trim(); if(k.length<2) continue; freq[k]=(freq[k]||0)+1; }
    const top=Object.entries(freq).filter(([k,v])=>v>=1).sort((a,b)=>b[1]-a[1]).slice(0,60).map(([k])=>k);

    const orgHint=/\b(Inc|LLC|Ltd|Corporation|Corp|Company|Ltd\.|PLC|GmbH|S\.?A\.?|University|Institute|Press|Council|Agency|Studio|Labs)\b/;
    const placeHint=/\b(Street|St\.|Road|Rd\.|Avenue|Ave\.|City|Country|Province|State|Pakistan|India|USA|UK|Europe|Asia|Tokyo|London|Paris|Dubai)\b/;
    const softHints=['Android','iOS','Windows','macOS','Linux','APK','App','Software','Browser','Chrome','Firefox','Edge','Safari','Photoshop','Figma','Visual Studio','Xcode'];
    const gameHints=['Game','PlayStation','Xbox','Nintendo','Steam','Epic','Mobile','PUBG','Fortnite','Minecraft','Roblox','FIFA','Valorant','CS','GTA'];

    const has = (name, arr)=> arr.some(h=> new RegExp('\\b'+h+'\\b','i').test(name));
    const looksApk = (tok)=> /\.apk$/i.test(tok) || /v\d+(\.\d+)+/i.test(tok);
    const versioned = (name)=> /\b(v|version)\s*\d+(\.\d+)+/i.test(name);

    top.forEach(name=>{
      if (/\b(Mr|Mrs|Ms|Dr|Prof)\b/i.test(name) || (name.split(' ').length>=2 && !orgHint.test(name) && !placeHint.test(name))) ents.people.push(name);
      else if (orgHint.test(name)) ents.orgs.push(name);
      else if (placeHint.test(name)) ents.places.push(name);
      else if (has(name, softHints) || versioned(name)) ents.software.push(name);
      else if (has(name, gameHints)) ents.games.push(name);
      else ents.other.push(name);
    });

    // From lowercase tokens: APKs or explicit .apk filenames
    lowerTokens.forEach(t=>{
      if(looksApk(t)) ents.software.push(t);
      if(/(apk|android\.app)/i.test(t)) ents.software.push(t);
    });

    const uniq=a=>[...new Set(a)].slice(0,18);
    return { 
      people:uniq(ents.people), orgs:uniq(ents.orgs), places:uniq(ents.places),
      software:uniq(ents.software), games:uniq(ents.games), other:uniq(ents.other)
    };
  }
  function renderEntities(ents){
    const mount=(id,arr,icon,type)=>{ const el=document.getElementById(id); if(!el) return; el.innerHTML=''; (arr||[]).forEach(v=>{ const s=document.createElement('span'); s.className='entity-chip'; s.setAttribute('data-type', type); s.innerHTML=`<i class="${icon}"></i> ${v}`; el.appendChild(s); }); };
    mount('entPeople', ents.people, 'fa-solid fa-user', 'people');
    mount('entOrgs', ents.orgs, 'fa-solid fa-building', 'org');
    mount('entPlaces', ents.places, 'fa-solid fa-location-dot', 'place');
    mount('entSoftware', ents.software, 'fa-solid fa-mobile-screen-button', 'software');
    mount('entGames', ents.games, 'fa-solid fa-gamepad', 'game');
    mount('entOther', ents.other, 'fa-solid fa-tag', 'other');
    const p=document.getElementById('entitiesPanel'); if(p) p.style.display='block';
  }

  /* --------- Speed Test (CrUX + PSI) with Dual Panels (Top & Bottom) --------- */
  function pickStrategy(suffix, fallback='mobile'){
    const mob=document.getElementById('speedStrategyMobile'+suffix), desk=document.getElementById('speedStrategyDesktop'+suffix);
    if(mob?.checked) return 'mobile';
    if(desk?.checked) return 'desktop';
    return fallback;
  }

  async function runSpeedForPanel(auditedUrl, suffix, forceStrategy){ // suffix: 'Top' or 'Bottom'
    const panelId = suffix==='Top' ? 'speedPanelTop' : 'speedPanelBottom';
    const panel = document.getElementById(panelId); if(panel) panel.style.display='block';
    const strategy = forceStrategy || pickStrategy(suffix, 'mobile');

    // FAST: CrUX
    try{
      const r1 = await fetch(`${window.SEMSEO.ENDPOINTS.crux}?url=${encodeURIComponent(auditedUrl)}`);
      const j1 = await r1.json();
      if(j1.ok){
        setText('cruxType'+suffix, `(${j1.type==='url'?'URL':'Origin'}-level p75)`);
        renderCruxVitals(j1, suffix);
        setText('speed-mode-'+suffix, 'FAST');
      }
    }catch(e){ console.warn('CrUX error', e); }

    // FULL: PSI
    try{
      const r2 = await fetch(`${window.SEMSEO.ENDPOINTS.psi}?url=${encodeURIComponent(auditedUrl)}&strategy=${strategy}`);
      const j2 = await r2.json();
      if(j2.ok){
        renderPsiAudit(j2, suffix);
        setText('speed-mode-'+suffix, j2.cached ? 'CACHED' : 'LIVE');
      } else {
        speedError(j2.error || 'PSI failed', suffix);
      }
    }catch(e){ speedError(e.message, suffix); }
  }

  const ms=v=> v==null? '—' : (Math.round(+v)+' ms');

  function renderCruxVitals(data, suffix){
    const x=data.p75 || {};
    setText('cwvLcp'+suffix, x.LCP!=null? ms(x.LCP) : '—');
    setText('cwvInp'+suffix, x.INP!=null? ms(x.INP) : '—');
    const clsVal = (x.CLS>=1 ? x.CLS/100 : x.CLS);
    setText('cwvCls'+suffix, clsVal!=null? (+(clsVal)).toFixed(3) : '—');
    setBar('bar-lcp-'+suffix, +x.LCP, {good:2500, needs:4000}, true);
    setBar('bar-inp-'+suffix, +x.INP, {good:200,  needs:500 }, true);
    setBar('bar-cls-'+suffix, +(clsVal), {good:0.1, needs:0.25}, false);
  }

  function renderPsiAudit(data, suffix){
    const lr = data.lighthouseResult || {};
    const cats = lr.categories || {};
    setText('psiStamp'+suffix, data.analysisUTCTimestamp ? `• ${new Date(data.analysisUTCTimestamp).toLocaleString()}` : '');
    setScore('sc-perf-'+suffix, Math.round((cats.performance?.score||0)*100));
    setScore('sc-seo-'+suffix,  Math.round((cats.seo?.score||0)*100));
    setScore('sc-acc-'+suffix,  Math.round((cats.accessibility?.score||0)*100));
    setScore('sc-best-'+suffix, Math.round((cats['best-practices']?.score||0)*100));
    hydrateOpportunities(lr.audits || {}, suffix);
    hydrateSuggestions(lr.audits || {}, suffix);
  }

  function setBar(id, value, th, lowerIsBetter){
    const el=document.getElementById(id); if(!el) return;
    el.classList.remove('good','mid','bad');
    if(value==null || isNaN(value)){ el.style.width='0%'; return; }
    let good=false, mid=false;
    if(lowerIsBetter){
      good = value <= th.good;
      mid  = value > th.good && value <= th.needs;
    }else{
      good = value >= th.good;
      mid  = value >= th.mid && value < th.good;
    }
    el.classList.add(good?'good':(mid?'mid':'bad'));
    let pct;
    if(lowerIsBetter){
      if (value <= th.good) pct = 100;
      else if (value >= th.needs) pct = 5;
      else pct = Math.round(100 - (value - th.good) * 95 / (th.needs - th.good));
    } else {
      pct = Math.round((value / th.good) * 100); if(pct>100) pct=100;
    }
    el.style.width = Math.max(5, Math.min(100, pct)) + '%';
  }
  function setScore(id, v){
    const el=document.getElementById(id); if(!el) return;
    el.textContent = isFinite(v) ? v : '—';
    el.classList.remove('good','mid','bad');
    el.classList.add(v>=90?'good':(v>=50?'mid':'bad'));
  }

  function hydrateOpportunities(audits, suffix){
    const ul=document.getElementById('psiOpps'+suffix); if(!ul) return; ul.innerHTML='';
    const keys=['render-blocking-resources','unminified-css','unminified-javascript','unused-css-rules','unused-javascript','uses-optimized-images','uses-text-compression','uses-responsive-images','uses-rel-preload','uses-rel-preconnect','server-response-time'];
    keys.forEach(k=>{
      const a=audits[k]; if(!a) return;
      const li=document.createElement('li'); li.innerHTML = `<b>${a.title||k}</b> — <span>${a.displayValue||a.description||''}</span>`;
      ul.appendChild(li);
    });
  }

  function hydrateSuggestions(audits, suffix){
    const ul=document.getElementById('psiSugs'+suffix); if(!ul) return; ul.innerHTML='';
    const add=(severity, icon, text)=>{ const li=document.createElement('li'); li.className=severity; li.innerHTML=`<i class="fa-solid ${icon}"></i> ${text}`; ul.appendChild(li); };

    const tbt = audits['total-blocking-time']?.numericValue;
    if(tbt>200) add('bad','fa-stopwatch','High Total Blocking Time: split bundles (code-splitting), defer non-critical JS, and hydrate on interaction.');

    const lcp = audits['largest-contentful-paint']?.numericValue;
    if(lcp>2500) add('mid','fa-image','Slow LCP: serve hero image in next-gen format (AVIF/WebP), add width/height, and preload the hero image.');

    const fcp = audits['first-contentful-paint']?.numericValue;
    if(fcp>1800) add('mid','fa-sparkles','Improve FCP: inline critical CSS, preload key fonts (display=swap), and move render-blocking scripts.');

    if(audits['uses-text-compression']?.score<1) add('mid','fa-file-zipper','Enable gzip/Brotli for HTML/CSS/JS and set proper cache headers.');

    if(audits['server-response-time']?.score<1) add('bad','fa-server','Reduce TTFB: add caching (page/opcode), upgrade PHP version, enable HTTP/2+HTTP/3, and use CDN.');

    if(audits['uses-optimized-images']?.score<1 || audits['modern-image-formats']?.score<1) add('mid','fa-images','Optimize images: convert to AVIF/WebP, resize to display size, and lazy-load below-the-fold.');

    if(audits['unused-javascript']?.score<1) add('mid','fa-scissors','Reduce unused JS: remove legacy libraries, tree-shake, and load features conditionally.');

    if(audits['uses-rel-preconnect']?.score<1) add('good','fa-link','Add <link rel="preconnect"> to critical origins (CDN, fonts, analytics).');

    if(ul.children.length===0) add('good','fa-thumbs-up','Looking good! Minor refinements may still improve scores.');
  }

  function speedError(msg, suffix){
    const ul=document.getElementById('psiOpps'+suffix); if(ul){ const li=document.createElement('li'); li.textContent = 'Error: '+msg; ul.appendChild(li); }
    const su=document.getElementById('psiSugs'+suffix); if(su){ const li=document.createElement('li'); li.textContent = 'Error: '+msg; su.appendChild(li); }
  }

  /* ----------------- Main Analyze flow ----------------- */
  async function analyze(){
    if (window.SEMSEO.BUSY) return;
    window.SEMSEO.BUSY = true;

    const input = document.getElementById('analyzeUrl');
    let url = normalizeUrl(input ? input.value : '');
    if (!url) { input?.focus(); window.SEMSEO.BUSY=false; return; }

    window.Water?.start();
    const statusEl = document.getElementById('analyzeStatus');
    if (statusEl) statusEl.textContent = 'Fetching & analyzing…';
    const report = document.getElementById('analyzeReport'); if (report) report.style.display = 'none';
    ['detectorPanel','readabilityPanel','entitiesPanel','speedPanelTop','speedPanelBottom'].forEach(id=>{ const el=document.getElementById(id); if(el) el.style.display='none'; });

    let {ok,data} = await fetchBackend(url); if(!data) data={};

    let sample = buildSampleFromData(data);
    try{ const raw = await fetchRawHtml(url); if(raw){ const meta = extractMetaFromHtml(raw, url); data = mergeMeta(data, meta); if((!sample||sample.length<200)&&meta.sampleText) sample=meta.sampleText; } }catch(_){}
    if ((!sample || sample.length < 200)){
      if (statusEl) statusEl.textContent = 'Getting readable text…';
      try{ const read = await fetchReadableText(url); if (read && read.length>200) sample = read; }catch(_){}
    }

    const ensemble = sample && sample.length>30 ? detectUltra(sample) : null;
    data = ensureScoresExist(data, sample, ensemble);

    const overall = Number(data.overall || 0);
    const contentScore = Number(data.contentScore || 0);
    window.setScoreWheel(overall||0);
    setText('contentScoreInline', Math.round(contentScore||0));
    setChipTone(document.getElementById('contentScoreChip'), contentScore||0);

    setText('rStatus',    data.httpStatus ? data.httpStatus : '200?');
    setText('rTitleLen',  (data.titleLen   ?? '—'));
    setText('rMetaLen',   (data.metaLen    ?? '—'));
    setText('rCanonical', (data.canonical  || '—'));
    setText('rRobots',    (data.robots     || '—'));
    setText('rViewport',  (data.viewport   || '—'));
    setText('rHeadings',  (data.headings   || '—'));
    setText('rInternal',  (data.internalLinks ?? '—'));
    setText('rSchema',    (data.schema     || '—'));

    const hp = (typeof data.humanPct==='number')? data.humanPct : NaN;
    const ap = (typeof data.aiPct==='number')? data.aiPct : NaN;
    const backendConf = (typeof data.confidence==='number')? data.confidence : null;
    if (isFinite(hp) && isFinite(ap) && backendConf && backendConf>=65){
      applyDetection(hp, ap, backendConf, ensemble || null);
    } else if (ensemble){
      applyDetection(ensemble.humanPct, ensemble.aiPct, ensemble.confidence, ensemble);
    } else if (isFinite(hp) && isFinite(ap)){
      applyDetection(hp, ap, backendConf || 60, null);
    } else {
      applyDetection(NaN, NaN, null, null);
    }

    window.autoTickByScores(data.itemScores || {});
    const s = (ensemble && ensemble._s) ? ensemble._s : _prep(sample||'');
    renderReadability(s);
    try{ const ents=extractEntitiesHeuristics(sample||''); renderEntities(ents); }catch(e){}

    // Show panels before speed to ensure layout order
    document.getElementById('readabilityPanel').style.display='block';
    document.getElementById('entitiesPanel').style.display='block';
    document.getElementById('speedPanelTop').style.display='block';
    document.getElementById('speedPanelBottom').style.display='block';

    // AUTO-START: run speed tests for both panels (mobile strategy default)
    runSpeedForPanel(url, 'Top', 'mobile');
    runSpeedForPanel(url, 'Bottom', 'mobile');

    window.Water?.finish();
    if (statusEl) statusEl.textContent = 'Analysis complete';
    if (report) report.style.display = 'block';
    window.SEMSEO.BUSY = false;
    if (window.SEMSEO.QUEUE > 0){ window.SEMSEO.QUEUE = 0; }
  }
  window.analyze = analyze;

  /* Events */
  document.addEventListener('DOMContentLoaded', function(){
    try{
      document.getElementById('analyzeBtn')?.addEventListener('click', e=>{ e.preventDefault(); analyze(); });
      const input = document.getElementById('analyzeUrl');
      input?.addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); analyze(); }});
      document.getElementById('clearUrl')?.addEventListener('click', ()=>{ if(input){ input.value=''; input.focus(); } });
      if (navigator.clipboard){ document.getElementById('pasteUrl')?.addEventListener('click', async ()=>{ try{ const t=await navigator.clipboard.readText(); if(t) input.value=t.trim(); }catch(e){} }); }

      // Manual speed runs
      document.getElementById('runSpeedTop')?.addEventListener('click', ()=>{
        const v = document.getElementById('analyzeUrl')?.value || '';
        const url = (v && /^https?:\/\//i.test(v))? v : ('https://'+v.replace(/^\/+/,''));
        if(!url){ alert('Enter a valid URL first'); return; }
        runSpeedForPanel(url,'Top');
      });
      document.getElementById('runSpeedBottom')?.addEventListener('click', ()=>{
        const v = document.getElementById('analyzeUrl')?.value || '';
        const url = (v && /^https?:\/\//i.test(v))? v : ('https://'+v.replace(/^\/+/,''));
        if(!url){ alert('Enter a valid URL first'); return; }
        runSpeedForPanel(url,'Bottom');
      });

      window.SEMSEO.READY = true;
      if (window.SEMSEO.QUEUE>0){ window.SEMSEO.QUEUE=0; analyze(); }
    }catch(err){
      const s=document.getElementById('analyzeStatus'); if(s) s.textContent='Boot error: '+err.message;
    }
  });

})();
</script>

<!-- ======================= B) Non-critical UI / Background ======================= -->
<script>
try{
  // Hue drift
  (function(){ const root=document.documentElement; const start=performance.now(); function frame(now){ root.style.setProperty('--hue', (((now-start)/4)%360) + 'deg'); requestAnimationFrame(frame);} requestAnimationFrame(frame); })();

  // Share links
  (function(){
    const url = encodeURIComponent(location.href), title = encodeURIComponent(document.title);
    const fb = document.getElementById('shareFb'), x = document.getElementById('shareX'), ln = document.getElementById('shareLn'), wa = document.getElementById('shareWa'), em = document.getElementById('shareEm');
    if(fb) fb.href = 'https://www.facebook.com/sharer/sharer.php?u='+url;
    if(x)  x.href  = 'https://twitter.com/intent/tweet?text='+title+'&url='+url;
    if(ln) ln.href = 'https://www.linkedin.com/sharing/share-offsite/?url='+url;
    if(wa) wa.href = 'https://wa.me/?text='+title+'%20'+url;
    if(em) em.href = 'mailto:?subject='+title+'&body='+url;
  })();

  // Reset/Export/Import/Print/Back to top
  (function(){
    const updateBars=()=> window.updateCategoryBars && window.updateCategoryBars();
    document.getElementById('resetChecklist')?.addEventListener('click', function(){
      document.querySelectorAll('.checklist input[type="checkbox"]').forEach(cb=>cb.checked=false);
      document.querySelectorAll('.score-badge').forEach(b=>{ b.textContent='—'; b.classList.remove('score-good','score-mid','score-bad'); });
      updateBars(); window.setScoreWheel?.(0);
      ['contentScoreInline','humanPct','aiPct'].forEach(id=>{ const el=document.getElementById(id); if(el) el.textContent = id==='contentScoreInline'?'0':'—'; });
      document.getElementById('contentScoreChip')?.classList.remove('chip-good','chip-mid','chip-bad');
      const badge=document.getElementById('aiBadge'); const b=badge?.querySelector('b'); if(b) b.textContent='—';
      ['detectorPanel','readabilityPanel','entitiesPanel','speedPanelTop','speedPanelBottom'].forEach(id=>{ const el=document.getElementById(id); if(el) el.style.display='none'; });
      window.Water?.reset();
    });

    document.getElementById('exportChecklist')?.addEventListener('click', function(){
      const payload = { checked:[], scores:{} };
      for(let i=1;i<=25;i++){
        const cb=document.getElementById('ck-'+i), sc=document.getElementById('sc-'+i);
        if (cb && cb.checked) payload.checked.push(i);
        const s = parseInt(sc ? sc.textContent : 'NaN',10); if (!isNaN(s)) payload.scores[i]=s;
      }
      const blob=new Blob([JSON.stringify(payload,null,2)],{type:'application/json'});
      const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='checklist.json'; a.click(); URL.revokeObjectURL(a.href);
    });
    const importFile=document.getElementById('importFile');
    document.getElementById('importChecklist')?.addEventListener('click', ()=> importFile?.click());
    importFile?.addEventListener('change', function(){
      const file = importFile.files[0]; if (!file) return;
      const fr = new FileReader();
      fr.onload = function(){ try{
        const data = JSON.parse(fr.result);
        for(let i=1;i<=25;i++){
          const cb=document.getElementById('ck-'+i); if (cb) cb.checked=(data.checked||[]).includes(i);
          const sc=document.getElementById('sc-'+i); const val=data.scores ? data.scores[i] : undefined;
          if (sc && typeof val==='number'){ sc.textContent=val; (window.badgeTone||function(){ })(sc,val); }
        }
        updateBars();
      }catch(e){ alert('Invalid JSON'); } };
      fr.readAsText(file);
    });

    document.getElementById('printTop')?.addEventListener('click', ()=> window.print());
    document.getElementById('printChecklist')?.addEventListener('click', ()=> window.print());

    const toTop=document.getElementById('toTopLink'), backTop=document.getElementById('backTop');
    toTop?.addEventListener('click', e=>{ e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'});});
    window.addEventListener('scroll', ()=>{ if(backTop) backTop.style.display = (window.scrollY>500)?'grid':'none'; });
  })();

  // Background effects
  (function(){
    const c=document.getElementById('linesCanvas'); if(!c) return; const ctx=c.getContext('2d'); const dpr=Math.min(2,window.devicePixelRatio||1);
    function resize(){ c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0) }
    function draw(t){ ctx.clearRect(0,0,window.innerWidth,window.innerHeight); const w=window.innerWidth,h=window.innerHeight,rows=16,spacing=Math.max(54,h/rows);
      for(let i=-2;i<rows+2;i++){ const y=i*spacing+((t*0.025)%spacing); const g=ctx.createLinearGradient(0,y,w,y+90);
        g.addColorStop(0,'rgba(61,226,255,0.14)'); g.addColorStop(0.5,'rgba(155,92,255,0.16)'); g.addColorStop(1,'rgba(255,32,69,0.14)');
        ctx.strokeStyle=g; ctx.lineWidth=1.5; ctx.beginPath(); ctx.moveTo(-120,y); ctx.lineTo(w+120,y+90); ctx.stroke(); }
      requestAnimationFrame(draw);
    }
    window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
  })();
  (function(){
    const c=document.getElementById('smokeCanvas'); if(!c) return; const ctx=c.getContext('2d');
    const dpr=Math.min(2,window.devicePixelRatio||1); let blobs=[], last=performance.now(), PERIOD=1000000000;
    function resize(){
      c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0);
      const W=window.innerWidth, H=window.innerHeight, N=76;
      blobs=new Array(N).fill(0).map((_,i)=>{
        const px = W*0.65 + Math.random()*W*0.45, py = H*0.65 + Math.random()*H*0.45, r  = 120 + Math.random()*260, speed = 0.18 + Math.random()*0.22;
        return { x:px, y:py, r:r, vx: -speed*(0.6+Math.random()*0.8), vy: -speed*(0.6+Math.random()*0.8), baseHue: (i*37)%360, alpha: .26 + .20*Math.random() };
      });
      last=performance.now();
    }
    function draw(now){
      const W=window.innerWidth, H=window.innerHeight; ctx.clearRect(0,0,W,H); ctx.globalCompositeOperation='screen';
      const dt = now - last; last = now;
      for(const b of blobs){
        b.x += b.vx * dt; b.y += b.vy * dt;
        if(b.x < -360 || b.y < -360){ b.x = W + Math.random()*260; b.y = H + Math.random()*260; }
        const hue = (b.baseHue + (now % PERIOD) * (360/PERIOD)) % 360;
        const g=ctx.createRadialGradient(b.x,b.y,0,b.x,b.y,b.r);
        g.addColorStop(0,'hsla('+hue+',88%,68%,'+b.alpha+')'); g.addColorStop(1,'hsla('+((hue+70)%360)+',88%,50%,0)');
        ctx.fillStyle=g; ctx.beginPath(); ctx.arc(b.x,b.y,b.r,0,Math.PI*2); ctx.fill();
      }
      requestAnimationFrame(draw);
    }
    window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
  })();

} catch(e){ const s=document.getElementById('analyzeStatus'); if(s) s.textContent='UI error: '+e.message; }
</script>

<!-- Error sink -->
<script>
window.addEventListener('error', function(e){
  const s=document.getElementById('analyzeStatus');
  if (s) s.textContent = 'JavaScript error: ' + (e && e.message ? e.message : e);
});
</script>

</body>
</html>
