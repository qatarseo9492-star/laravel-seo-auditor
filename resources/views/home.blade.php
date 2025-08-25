{{-- resources/views/home.blade.php — v2025-08-24ab (Speed panel moved right below Entities; PSI auto-start fix) --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, UX signals, and site speed with stylish, responsive insights.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
  $analyzeJsonUrl = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl     = Route::has('analyze')      ? route('analyze')      : url('analyze');
  $psiProxyUrl    = Route::has('pagespeed.proxy') ? route('pagespeed.proxy') : url('api/pagespeed');
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
/* === Theme === */
:root{
  --bg:#07080e;--panel:#0f1022;--panel-2:#141433;--text:#f0effa;--text-dim:#b6b3d6;
  --good:#22c55e;--warn:#f59e0b;--bad:#ef4444;--accent:#3de2ff;--accent-2:#9b5cff;
  --radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55);--container:1200px;--hue:0deg
}
*{box-sizing:border-box}html,body{height:100%}html{scroll-behavior:smooth}
body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,Segoe UI,Roboto;background:
radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),
radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),
var(--bg);overflow-x:hidden}

a{color:#8ad4ff}

#linesCanvas,#smokeCanvas{position:fixed;inset:0;pointer-events:none;z-index:0}
#linesCanvas{opacity:.45}#smokeCanvas{opacity:.9;mix-blend-mode:screen}
.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
header.site{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:14px 0 22px;border-bottom:1px solid rgba(255,255,255,.08)}
.brand{display:flex;align-items:center;gap:.8rem;min-width:0}
.brand-badge{width:48px;height:48px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(157,92,255,.3),rgba(61,226,255,.3));border:1px solid rgba(255,255,255,.18);color:#fff;font-size:1.08rem;box-shadow:0 8px 22px rgba(0,0,0,.28)}
.hero-heading{font-weight:1000;letter-spacing:.4px;font-size:clamp(1.4rem,3.2vw,2rem)}
.hero-sub{color:var(--text-dim);font-size:.95rem}
.btn{display:inline-flex;align-items:center;gap:.55rem;cursor:pointer;padding:.6rem .95rem;border-radius:14px;border:1px solid rgba(255,255,255,.16);color:#fff;font-weight:900;letter-spacing:.2px;position:relative;overflow:hidden;box-shadow:var(--shadow)}
.btn::after{content:"";position:absolute;inset:-2px;border-radius:inherit;opacity:.0;background:linear-gradient(120deg,transparent,rgba(255,255,255,.22),transparent 60%);transform:translateX(-120%);transition:opacity .2s}
.btn:hover::after{opacity:1;animation:btnSweep 2.6s linear infinite}
@keyframes btnSweep{0%{transform:translateX(-120%)}100%{transform:translateX(120%)}}
.btn-analyze{background:linear-gradient(135deg,#10b981,#22c55e);border-color:#20d391}
.btn-print{background:linear-gradient(135deg,#3b82f6,#6366f1);border-color:#5b77ef}
.btn-reset{background:linear-gradient(135deg,#f59e0b,#f97316);border-color:#f59e0b}
.btn-export{background:linear-gradient(135deg,#a855f7,#ec4899);border-color:#c26cf2}
.btn-ghost{background:rgba(255,255,255,.06)} .btn:disabled{opacity:.6;cursor:not-allowed}

.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem}.section-subtitle{margin:0;color:var(--text-dim)}

.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)}.l-orange{background:rgba(245,158,11,.18)}.l-green{background:rgba(34,197,94,.18)}

/* Score gauge + chips */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-gauge{position:relative;width:100%;aspect-ratio:1/1}.gauge-svg{width:100%;height:auto;display:block}
.score-mask-rect{transition:all .6s cubic-bezier(.22,1,.36,1)}
.score-wave1{animation:scoreWave 8s linear infinite}.score-wave2{animation:scoreWave 11s linear infinite reverse}
@keyframes scoreWave{from{transform:translateX(0)}to{transform:translateX(-210px)}}
.score-text{font-size:clamp(2.2rem,4.2vw,3.1rem);font-weight:1000;fill:#fff;text-shadow:0 0 18px rgba(255,32,69,.25)}
.multiHueFast{filter:hue-rotate(var(--hue)) saturate(140%);will-change:filter}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.ico{width:1.1em;text-align:center}.ico-green{color:var(--good)}.ico-orange{color:var(--warn)}.ico-red{color:var(--bad)}.ico-cyan{color:var(--accent)}.ico-purple{color:#9b5cff}

/* URL field */
.url-field{position:relative;border-radius:16px;background:#0b0d21;border:1px solid #1b1b35;box-shadow:inset 0 0 0 1px rgba(255,255,255,.02),0 12px 32px rgba(0,0,0,.32);padding:10px 110px 10px 46px;transition:.25s;overflow:hidden;isolation:isolate}
.url-field:focus-within{border-color:#5942ff;box-shadow:0 0 0 6px rgba(155,92,255,.15),inset 0 0 0 1px rgba(93,65,255,.28)}
.url-field .url-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9aa0c3;font-size:1rem;opacity:.95}
.url-field input{all:unset;color:var(--text);width:100%;font-size:1rem;letter-spacing:.2px}
.url-field .url-mini{position:absolute;top:50%;transform:translateY(-50%);border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;border-radius:10px;padding:.35rem .6rem;font-weight:900;cursor:pointer;transition:.15s}
.url-field .url-mini:hover{background:rgba(255,255,255,.12)}.url-field .url-clear{right:60px;width:36px;height:32px;display:grid;place-items:center}.url-field #pasteUrl{right:12px}
.url-field .url-border{content:"";position:absolute;inset:-2px;border-radius:inherit;padding:2px;background:conic-gradient(from 0deg,#3de2ff,#9b5cff,#ff2045,#f59e0b,#3de2ff);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.55;pointer-events:none;filter:hue-rotate(var(--hue))}

/* Waterbars + comp bar */
.water-wrap{margin-top:.8rem;display:none}
.waterbar{position:relative;height:64px;border-radius:18px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.water-svg{position:absolute;inset:0;width:100%;height:100%;z-index:1}
.water-mask-rect{transition:all .25s ease-out}
.water-overlay{position:absolute;inset:0;pointer-events:none;background:radial-gradient(120px 60px at 20% -20%,rgba(255,255,255,.18),transparent 60%),linear-gradient(0deg,rgba(255,255,255,.05),transparent 40%,transparent 60%,rgba(255,255,255,.06));mix-blend-mode:screen;z-index:2}
.water-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1.05rem;text-shadow:0 1px 0 rgba(0,0,0,.45);letter-spacing:.4px;z-index:4}
.wave1{animation:waveX 7s linear infinite}.wave2{animation:waveX 10s linear infinite reverse;opacity:.7}
@keyframes waveX{0%{transform:translateX(0)}100%{transform:translateX(-600px)}}
.multiHue{filter:hue-rotate(var(--hue)) saturate(140%);will-change:filter}
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}

/* Panels */
.panel{margin-top:16px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:18px;padding:16px;box-shadow:var(--shadow)}
.panel-header{display:flex;align-items:center;gap:.65rem;margin-bottom:.5rem}
.panel-title{margin:0;font-size:1.15rem;font-weight:1000;background:linear-gradient(90deg,#3de2ff,#a78bfa,#ff58a6);-webkit-background-clip:text;-webkit-text-fill-color:transparent}

/* Human vs AI */
.hvai-wrap{display:grid;grid-template-columns:repeat(12,1fr);gap:.75rem}
.hvai-card{grid-column:span 12;background:linear-gradient(145deg,rgba(61,226,255,.08),rgba(155,92,255,.08));border:1px dashed rgba(255,255,255,.14);border-radius:14px;padding:12px}
.hvai-meter{position:relative;height:20px;border-radius:999px;background:#0b0d21;border:1px solid rgba(255,255,255,.12);overflow:hidden}
.hvai-human,.hvai-ai{position:absolute;top:0;bottom:0;width:50%;transition:width .6s cubic-bezier(.22,1,.36,1)}
.hvai-human{left:0;background:linear-gradient(90deg,#22c55e,#3de2ff)}
.hvai-ai{right:0;background:linear-gradient(90deg,#ef4444,#f59e0b)}
.hvai-labels{display:flex;justify-content:space-between;font-weight:900;margin-top:.35rem}
.det-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.5rem;margin-top:.5rem}
.det-item{grid-column:span 6;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.55rem .6rem}
.det-row{display:grid;grid-template-columns:1fr auto;gap:.5rem;align-items:center}
.det-label{font-weight:800;color:var(--text-dim)}
.det-score{font-weight:1000}
.det-bar{margin-top:.35rem;height:12px;border-radius:10px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1);position:relative}
.det-fill-human{position:absolute;left:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#22c55e,#3de2ff);transition:width .35s ease}
.det-fill-ai{position:absolute;right:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#ef4444,#f59e0b);transition:width .35s ease}

/* Readability */
.read-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.75rem}
.read-card{grid-column:span 6;background:linear-gradient(160deg,rgba(56,189,248,.10),rgba(168,85,247,.08));border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:.75rem}
.read-row{display:grid;grid-template-columns:auto 1fr auto;gap:.5rem;align-items:center}
.read-bar{height:12px;border-radius:999px;background:#0b0d21;border:1px solid rgba(255,255,255,.12);overflow:hidden}
.read-fill{height:100%;width:0;background:linear-gradient(90deg,#22c55e,#3de2ff);transition:width .4s ease}
.badge-mini{font-weight:900;padding:.18rem .45rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);font-size:.8rem}

/* Entities & Topics */
.ent-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.75rem}
.ent-card{grid-column:span 6;background:linear-gradient(160deg,rgba(250,204,21,.10),rgba(99,102,241,.10));border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:.75rem}
.tag{display:inline-flex;align-items:center;gap:.35rem;margin:.24rem;padding:.35rem .55rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);font-weight:800;white-space:nowrap}

/* Site Speed & CWV */
.speed-wrap{display:grid;grid-template-columns:repeat(12,1fr);gap:.75rem}
.speed-card{grid-column:span 6;background:linear-gradient(145deg,rgba(34,197,94,.08),rgba(59,130,246,.08));border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:.75rem}
.kpi{display:flex;align-items:center;justify-content:space-between;padding:.5rem;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.05);margin:.35rem 0}
.kpi .left{display:flex;align-items:center;gap:.45rem}
.kpi i{font-size:1rem}
.kpi.good{border-color:rgba(34,197,94,.45);background:rgba(34,197,94,.12)}
.kpi.mid{border-color:rgba(245,158,11,.45);background:rgba(245,158,11,.12)}
.kpi.bad{border-color:rgba(239,68,68,.55);background:rgba(239,68,68,.12)}
.skel{height:12px;border-radius:8px;background:linear-gradient(90deg,rgba(255,255,255,.06),rgba(255,255,255,.12),rgba(255,255,255,.06));animation:sk 1.2s infinite;background-size:200% 100%}
@keyframes sk{0%{background-position:0 0}100%{background-position:200% 0}}

/* Checklist (unchanged) */
.analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
.category-card{position:relative;grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px;box-shadow:var(--shadow);overflow:hidden;isolation:isolate}
.category-card::before{content:"";position:absolute;inset:-2px;border-radius:18px;padding:2px;background:linear-gradient(120deg,rgba(61,226,255,.4),rgba(155,92,255,.4),rgba(255,32,69,.4));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;animation:borderGlow 6s linear infinite;pointer-events:none;z-index:0}
@keyframes borderGlow{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(360deg)}}
.category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
.category-icon{width:48px;height:48px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3de2ff33,#9b5cff33);color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,.18)}
.category-title{margin:0;font-size:1.08rem;background:linear-gradient(90deg,#3de2ff,#9b5cff,#ff2045);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900}
.category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}
.checklist{list-style:none;margin:10px 0 0;padding:0}
.checklist-item{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.7rem .75rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);background:
linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02)),
radial-gradient(100% 120% at 0% 0%,rgba(61,226,255,.06),transparent 30%),
radial-gradient(120% 100% at 100% 0%,rgba(155,92,255,.05),transparent 35%);transition:box-shadow .25s,background .25s,transform .12s}
.checklist-item+.checklist-item{margin-top:.28rem}.checklist-item:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.25)}
.checklist-item label{cursor:pointer;display:inline-flex;align-items:center;gap:.55rem}
.sev-good{background:linear-gradient(180deg,rgba(34,197,94,.14),rgba(34,197,94,.08));border-color:rgba(34,197,94,.45)}
.sev-mid{background:linear-gradient(180deg,rgba(245,158,11,.16),rgba(245,158,11,.08));border-color:rgba(245,158,11,.45)}
.sev-bad{background:linear-gradient(180deg,rgba(239,68,68,.16),rgba(239,68,68,.10));border-color:rgba(239,68,68,.55)}
.checklist-item input[type="checkbox"]{appearance:none;-webkit-appearance:none;outline:none;width:22px;height:22px;border-radius:8px;background:#0b1220;border:2px solid #2a2f4d;position:relative;display:inline-grid;place-items:center;transition:.18s;box-shadow:inset 0 0 0 0 rgba(99,102,241,.0)}
.checklist-item input[type="checkbox"]:hover{border-color:#4c5399;box-shadow:0 0 0 4px rgba(99,102,241,.12)}
.checklist-item input[type="checkbox"]::after{content:"";width:7px;height:12px;border:3px solid transparent;border-left:0;border-top:0;transform:rotate(45deg) scale(.7);transition:.18s}
.checklist-item input[type="checkbox"]:checked{border-color:transparent;background:linear-gradient(135deg,#22c55e,#3de2ff,#9b5cff);background-size:200% 200%;animation:tickHue 2s linear infinite;box-shadow:0 6px 18px rgba(61,226,255,.25),inset 0 0 0 2px rgba(255,255,255,.25)}
.checklist-item input[type="checkbox"]:checked::after{border-color:#fff;filter:drop-shadow(0 1px 0 rgba(0,0,0,.4));transform:rotate(45deg) scale(1)}
.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22);border-color:rgba(22,193,114,.45)}
.score-mid{background:rgba(245,158,11,.22);border-color:rgba(245,158,11,.45)}
.score-bad{background:rgba(239,68,68,.24);border-color:rgba(239,68,68,.5)}

/* Responsive */
@media (max-width:992px){.category-card{grid-column:span 12}.score-container{width:190px}}
@media (max-width:768px){
  .wrap{padding:18px 4%}header.site{flex-direction:column;align-items:flex-start;gap:.6rem}
  .score-area{flex-direction:column;align-items:flex-start;gap:.8rem}.score-container{width:170px}
  .hvai-card,.read-card,.ent-card,.speed-card{grid-column:span 12}
}
@media (prefers-reduced-motion: reduce){
  .score-wave1,.score-wave2,.wave1,.wave2{animation:none!important}.multiHue,.multiHueFast{filter:none!important}
}
@media print{#linesCanvas,#smokeCanvas,.share-dock,#backTop{display:none!important}}
</style>
</head>
<body>

<!-- Background canvases -->
<canvas id="linesCanvas"></canvas><canvas id="smokeCanvas"></canvas>

<script>
  /* Global init */
  window.SEMSEO = window.SEMSEO || {};
  window.SEMSEO.ENDPOINTS = {
    analyzeJson: @json($analyzeJsonUrl),
    analyze: @json($analyzeUrl),
    psi: @json($psiProxyUrl)
  };
  window.SEMSEO.READY = false;
  window.SEMSEO.BUSY = false;
</script>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge" aria-hidden="true"><i class="fa-solid fa-brain"></i></div>
      <div>
        <div class="hero-heading">Semantic SEO Master Analyzer</div>
        <div class="hero-sub">Human vs AI → Readability → Entities → Site Speed (colorful, responsive)</div>
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
      <span class="legend l-orange">60–79</span>
      <span class="legend l-red">&lt; 60</span>
    </p>

    <!-- Score header -->
    <div class="score-area">
      <div class="score-container">
        <!-- Circular water score -->
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
          <span class="chip" id="overallChip"><i class="fa-solid fa-gauge-high ico"></i> Overall: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="contentScoreChip"><i class="fa-solid fa-file-lines ico"></i> Content: <b id="contentScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge" title="Content signature"><i class="fa-solid fa-user-astronaut ico ico-green"></i> Writer: <b>—</b></span>
          <button id="viewHumanBtn" class="btn btn-ghost"><i class="fa-solid fa-user ico ico-green"></i> Human-like: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost"><i class="fa-solid fa-robot ico ico-red"></i> AI-like: <b id="aiPct">—</b>%</button>
          <button id="copyQuick" class="btn btn-ghost"><i class="fa-regular fa-copy ico ico-cyan"></i> Copy report</button>
        </div>
        <small style="color:var(--text-dim)">Local ensemble ensures scores even if backend returns no data.</small>
      </div>
    </div>

    <!-- URL form -->
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
          <button id="analyzeBtn" type="button" class="btn btn-analyze"><i class="fa-solid fa-magnifying-glass"></i> Analyze</button>
          <button class="btn btn-print" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> Print</button>
          <button class="btn btn-reset" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> Reset</button>
          <button class="btn btn-export" id="exportChecklist" type="button" title="Export checklist JSON"><i class="fa-solid fa-file-export"></i> Export</button>
          <button class="btn btn-export" id="importChecklist" type="button" title="Import checklist JSON"><i class="fa-solid fa-file-import"></i> Import</button>
          <input type="file" id="importFile" accept="application/json" style="display:none">
        </div>

        <!-- Progress (water) -->
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
                  <g class="wave1 multiHue"><use href="#wave" x="0" fill="url(#waterGrad)"/><use href="#wave" x="600" fill="url(#waterGrad)"/></g>
                  <g class="wave2 multiHue" opacity=".65"><use href="#wave" x="0" y="8" fill="url(#waterGrad)"/><use href="#wave" x="600" y="8" fill="url(#waterGrad)"/></g>
                </g>
              </g>
            </svg>
            <div class="water-overlay"></div>
            <div class="water-pct"><span id="waterPct">0%</span></div>
          </div>
          <div id="analyzeStatus" style="margin-top:.4rem;color:var(--text-dim)" aria-live="polite"></div>
        </div>

        <!-- Meta recap -->
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

    <!-- ===== ORDERED PANELS ===== -->

    <!-- 1) Human vs AI -->
    <section id="hvaiPanel" class="panel">
      <div class="panel-header">
        <i class="fa-solid fa-scale-balanced ico ico-purple"></i>
        <h3 class="panel-title">Human vs AI Content Signals</h3>
      </div>
      <div class="hvai-wrap">
        <div class="hvai-card">
          <div class="hvai-meter" aria-label="Human vs AI bar">
            <div id="hvaiHuman" class="hvai-human" style="width:50%"></div>
            <div id="hvaiAI" class="hvai-ai" style="width:50%"></div>
          </div>
          <div class="hvai-labels">
            <span><i class="fa-solid fa-user ico ico-green"></i> Human-like: <b id="hvaiHumanPct">—</b>%</span>
            <span><i class="fa-solid fa-robot ico ico-red"></i> AI-like: <b id="hvaiAIPct">—</b>%</span>
          </div>
        </div>
      </div>
      <div class="chip" style="margin-top:.5rem"><i class="fa-solid fa-shield-halved ico"></i> Confidence: <b id="detConfidence">—</b>%</div>
      <div class="chip" style="margin-top:.5rem"><i class="fa-solid fa-circle-info ico"></i> Bars show <b>Human</b> vs <b>AI</b> tendency per detector</div>
      <div class="det-grid" id="detGrid"></div>
      <div class="det-note" id="detNote" style="color:var(--text-dim);margin-top:.35rem">Local ensemble activates if the backend provides no text/percentages.</div>
    </section>

    <!-- 2) Readability -->
    <section id="readPanel" class="panel">
      <div class="panel-header">
        <i class="fa-solid fa-book-open-reader ico ico-cyan"></i>
        <h3 class="panel-title">Readability Insights</h3>
      </div>
      <div class="read-grid" id="readGrid"></div>
      <div style="margin-top:.5rem;color:var(--text-dim)">Aim for clear sentences, varied vocabulary, and limited repetition.</div>
    </section>

    <!-- 3) Entities & Topics -->
    <section id="entitiesPanel" class="panel">
      <div class="panel-header">
        <i class="fa-solid fa-tags ico" style="color:#f59e0b"></i>
        <h3 class="panel-title">Entities & Topics</h3>
      </div>
      <div class="ent-grid" id="entitiesGrid"></div>
      <div style="margin-top:.5rem;color:var(--text-dim)">Click a tag to open a search (Google / Wikipedia).</div>
    </section>

    <!-- 4) Site Speed & Core Web Vitals (NOW directly below Entities) -->
    <section id="speedPanel" class="panel">
      <div class="panel-header">
        <i class="fa-solid fa-gauge-high ico" style="color:#22c55e"></i>
        <h3 class="panel-title">Site Speed & Core Web Vitals</h3>
      </div>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.5rem">
        <button id="runMobile" class="btn btn-ghost"><i class="fa-solid fa-mobile-screen-button"></i> Run Mobile</button>
        <button id="runDesktop" class="btn btn-ghost"><i class="fa-solid fa-desktop"></i> Run Desktop</button>
        <button id="runBoth" class="btn btn-ghost"><i class="fa-solid fa-repeat"></i> Re-test Both</button>
        <span class="chip"><i class="fa-regular fa-clock ico"></i> Last test: <b id="psiTime">—</b></span>
      </div>
      <div class="speed-wrap">
        <div class="speed-card">
          <div class="chip"><i class="fa-solid fa-mobile-screen-button"></i> Mobile</div>
          <div id="mobilePerf" class="kpi"><div class="left"><i class="fa-solid fa-bolt"></i> Performance</div><b>—</b></div>
          <div id="mobileLCP" class="kpi"><div class="left"><i class="fa-regular fa-image"></i> LCP</div><b>—</b></div>
          <div id="mobileINP" class="kpi"><div class="left"><i class="fa-solid fa-hand-pointer"></i> INP</div><b>—</b></div>
          <div id="mobileCLS" class="kpi"><div class="left"><i class="fa-solid fa-border-all"></i> CLS</div><b>—</b></div>
          <div id="mobileHints" style="margin-top:.4rem"></div>
        </div>
        <div class="speed-card">
          <div class="chip"><i class="fa-solid fa-desktop"></i> Desktop</div>
          <div id="desktopPerf" class="kpi"><div class="left"><i class="fa-solid fa-bolt"></i> Performance</div><b>—</b></div>
          <div id="desktopLCP" class="kpi"><div class="left"><i class="fa-regular fa-image"></i> LCP</div><b>—</b></div>
          <div id="desktopINP" class="kpi"><div class="left"><i class="fa-solid fa-hand-pointer"></i> INP</div><b>—</b></div>
          <div id="desktopCLS" class="kpi"><div class="left"><i class="fa-solid fa-border-all"></i> CLS</div><b>—</b></div>
          <div id="desktopHints" style="margin-top:.4rem"></div>
        </div>
      </div>
      <div style="margin-top:.6rem;color:var(--text-dim)">
        Tips come from Lighthouse audits: optimize images (AVIF/WebP), defer non-critical JS, enable compression, and reduce layout shifts with reserved space.
      </div>
    </section>

    <!-- Checklist is now AFTER Speed panel -->
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
      <div class="comp-water" id="compWater" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="position:relative;height:52px;border-radius:16px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)">
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
        <div class="comp-overlay" style="position:absolute;inset:0;background:radial-gradient(120px 50px at 15% -25%,rgba(255,255,255,.16),transparent 55%),linear-gradient(180deg,rgba(255,255,255,.08),transparent 35%,rgba(255,255,255,.06));pointer-events:none;mix-blend-mode:screen;z-index:3"></div>
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
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">{{ $c[2]-$c[1]+1 }}</span></span>
          </header>
          <ul class="checklist">
            @for($i=$c[1];$i<=$c[2];$i++)
              <li class="checklist-item">
                <label><input type="checkbox" id="ck-{{ $i }}"><span>{{ $labels[$i] }}</span></label>
                <span class="score-badge" id="sc-{{ $i }}">—</span>
                <button class="improve-btn" type="button" data-id="ck-{{ $i }}">Improve</button>
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
    <a href="#hvaiPanel">Human vs AI</a>
    <a href="#readPanel">Readability</a>
    <a href="#entitiesPanel">Entities</a>
    <a href="#speedPanel">Speed</a>
  </div>
</footer>

<button id="backTop" title="Back to top" aria-label="Back to top" style="position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none"><i class="fa-solid fa-arrow-up"></i></button>

<!-- ======== SCRIPTS ======== -->
<script>
(function(){
  var CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
  function setText(id,val){ var el=document.getElementById(id); if(el){ el.textContent=val; } return el; }
  function setHTML(id,html){ var el=document.getElementById(id); if(el){ el.innerHTML = html; } return el; }
  function setChipTone(el, v){ if(!el) return; el.classList.remove('chip-good','chip-mid','chip-bad'); var n=Number(v)||0; el.classList.add(n>=80?'chip-good':(n>=60?'chip-mid':'chip-bad')); }
  function badgeTone(el, v){ if(!el) return; el.classList.remove('score-good','score-mid','score-bad'); el.classList.add(v>=80?'score-good':(v>=60?'score-mid':'score-bad')); }
  function clamp(v,min,max){ return v<min?min:(v>max?max:v); }

  /* ===== Gauge ===== */
  var GAUGE={rect:null,stop1:null,stop2:null,r1:null,r2:null,arc:null,text:null,H:200,CIRC:2*Math.PI*95};
  window.setScoreWheel = function(value){
    if(!GAUGE.rect){
      GAUGE.rect=document.getElementById('scoreClipRect'); GAUGE.stop1=document.getElementById('scoreStop1'); GAUGE.stop2=document.getElementById('scoreStop2');
      GAUGE.r1=document.getElementById('ringStop1'); GAUGE.r2=document.getElementById('ringStop2'); GAUGE.arc=document.getElementById('ringArc'); GAUGE.text=document.getElementById('overallScore');
      if(GAUGE.arc){ GAUGE.arc.style.strokeDasharray=GAUGE.CIRC.toFixed(2); GAUGE.arc.style.strokeDashoffset=GAUGE.CIRC.toFixed(2); }
    }
    var v=Math.max(0,Math.min(100,Number(value)||0));
    var y=GAUGE.H-(GAUGE.H*(v/100));
    GAUGE.rect?.setAttribute('y',String(y));
    GAUGE.text && (GAUGE.text.textContent=Math.round(v)+'%');

    var c1,c2; if(v>=80){c1='#22c55e';c2='#16a34a'} else if(v>=60){c1='#f59e0b';c2='#fb923c'} else {c1='#ef4444';c2='#b91c1c'}
    GAUGE.stop1?.setAttribute('stop-color',c1); GAUGE.stop2?.setAttribute('stop-color',c2);
    GAUGE.r1?.setAttribute('stop-color',c1); GAUGE.r2?.setAttribute('stop-color',c2);
    if(GAUGE.arc){ var offset=GAUGE.CIRC*(1-(v/100)); GAUGE.arc.style.strokeDashoffset=offset.toFixed(2); }
    setText('overallScoreInline',Math.round(v)); setChipTone(document.getElementById('overallChip'),v);
  };

  /* ===== Category completion ===== */
  function updateCategoryBars(){
    var cards=[].slice.call(document.querySelectorAll('.category-card'));
    var total=0, checked=0;
    cards.forEach(function(card){
      var items=[].slice.call(card.querySelectorAll('.checklist-item'));
      total+=items.length; checked+=items.filter(li => li.querySelector('input')?.checked).length;
    });
    var pctAll = total? Math.round(checked*100/total) : 0;
    document.getElementById('compClipRect')?.setAttribute('width', String(6*pctAll));
    setText('compPct', pctAll + '%'); setText('progressCaption', checked+' of '+total+' items completed');
  }
  window.updateCategoryBars = updateCategoryBars;

  /* ===== Water progress ===== */
  var Water=(function(){
    var t=null, value=0;
    function show(){ var w=document.getElementById('waterWrap'); if(w) w.style.display='block'; }
    function hide(){ var w=document.getElementById('waterWrap'); if(w) w.style.display='none'; }
    function set(v){ value=Math.max(0,Math.min(100,v)); var y=200 - (200*value/100); var clip=document.getElementById('waterClipRect'); clip&&clip.setAttribute('y', String(y)); var p=document.getElementById('waterPct'); p&&(p.textContent = Math.round(value) + '%'); }
    return {
      start:function(){ show(); set(0); if(t) clearInterval(t); t=setInterval(function(){ if(value<88) set(value+2); }, 80); },
      finish:function(){ if(t) clearInterval(t); setTimeout(function(){ set(100); }, 150); setTimeout(function(){ hide(); }, 800); },
      reset:function(){ if(t) clearInterval(t); set(0); hide(); }
    };
  })();
  window.Water = Water;

  /* ===== Text stats / detection / entities / readability ===== */
  function _countSyllables(word){ var w=(word||'').toLowerCase().replace(/[^a-z]/g,''); if(!w) return 0; var m=(w.match(/[aeiouy]+/g)||[]).length; if(/(ed|es)$/.test(w)) m--; if(/^y/.test(w)) m--; return Math.max(1,m); }
  function _flesch(text){ var sents=(text.match(/[.!?]+/g)||[]).length||1; var words=(text.match(/[A-Za-z\u00C0-\u024f']+/g)||[]); var wN=words.length||1; var syll=0; for(var i=0;i<words.length;i++){ syll+=_countSyllables(words[i]); } return clamp(206.835 - 1.015*(wN/sents) - 84.6*(syll/wN), -20, 120); }
  function _prep(text){
    text=(text||'')+''; text=text.replace(/\u00A0/g,' ').replace(/\s+/g,' ').trim();
    var wordRe=/[A-Za-z\u00C0-\u024f0-9']+/g; var words=(text.match(wordRe)||[]).map(w=>w.toLowerCase());
    var sents=text.split(/(?<=[.!?])\s+|\n+(?=\S)/g).filter(Boolean); var tokens=words.length||1;
    var freq=Object.create(null); words.forEach(w=>{freq[w]=(freq[w]||0)+1;});
    var types=Object.keys(freq).length, hapax=0; for(var k in freq){ if(freq[k]===1) hapax++; }
    var lens=sents.map(s=>(s.match(wordRe)||[]).length).filter(v=>v>0);
    var mean=lens.reduce((a,b)=>a+b,0)/(lens.length||1);
    var variance=lens.reduce((a,b)=>a+Math.pow(b-mean,2),0)/(lens.length||1);
    var cov=mean?Math.sqrt(variance)/mean:0;
    var tri={}, triT=0, triR=0; for(var i=0;i<tokens-2;i++){ var g=words[i]+' '+words[i+1]+' '+words[i+2]; tri[g]=(tri[g]||0)+1; triT++; } for(var kk in tri){ if(tri[kk]>1) triR+=tri[kk]-1; }
    var digits=(text.match(/\d/g)||[]).length*100/(tokens||1);
    var avgLen=tokens? (words.join('').length/tokens):0;
    var longRatio=(lens.filter(L=>L>=28).length)/(lens.length||1);
    var TTR=types/(tokens||1);
    return { text, wordCount:tokens, flesch:_flesch(text), cov, longRatio, triRepeatRatio: triT?triR/triT:0, TTR, hapaxRatio: types?hapax/types:0, avgWordLen:avgLen, digitsPer100:digits };
  }
  function detectUltra(text){
    var s=_prep(text||''); if (s.wordCount < 40){ var aiQuick = clamp(70 - s.wordCount*0.8, 20, 70); return { humanPct: 100-aiQuick, aiPct: aiQuick, confidence: 46, detectors: [] , _s:s }; }
    var ai=10; var covT=0.45; if(s.cov<covT) ai+=clamp((covT-s.cov)/covT,0,1)*25; var ttrT=0.45; if(s.TTR<ttrT) ai+=clamp((ttrT-s.TTR)/ttrT,0,1)*18;
    var conf = clamp(50 + Math.min(45, Math.log((s.wordCount||1)+1)*7), 45, 95);
    return { humanPct: 100-clamp(Math.round(ai),0,100), aiPct: clamp(Math.round(ai),0,100), confidence: conf, detectors: [{key:'stylometry',label:'Stylometry',ai:clamp(Math.round(ai),0,100),w:1}], _s:s };
  }
  function extractEntities(sample){
    var text=(sample||'').replace(/\s+/g,' ').trim(); var result={ People:[], Orgs:[], Places:[], Software:[], Games:[], Other:[] }; if(!text) return result;
    var caps = text.match(/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+){0,3})\b/g) || []; var freq={}; caps.forEach(t=>{freq[t]=(freq[t]||0)+1;});
    var top=Object.keys(freq).sort((a,b)=>freq[b]-freq[a]).slice(0,80);
    top.forEach(t=>{
      if (/\b(inc|ltd|corp|llc|gmbh|company|studio)\b/i.test(t)) result.Orgs.push(t);
      else if (/\b(city|town|province|state|country|park|river|lake)\b/i.test(t)) result.Places.push(t);
      else if (/\b(software|suite|editor|studio|cloud|api|sdk|framework|wordpress|laravel|android|ios|windows|linux|mac)\b/i.test(t)) result.Software.push(t);
      else if (/\b(game|games|studios)\b/i.test(t)) result.Games.push(t);
      else if (/\s/.test(t)) result.People.push(t);
      else result.Other.push(t);
    });
    var apkHits=(text.match(/\bapk\b|\b\.apk\b/ig)||[]).length; if(apkHits>0 && !result.Software.includes('APK')) result.Software.unshift('APK');
    function uniq(arr){ var s=new Set(); var out=[]; arr.forEach(v=>{var k=v.trim(); if(!s.has(k) && k.length>1){ s.add(k); out.push(k); }}); return out.slice(0,20); }
    Object.keys(result).forEach(k=>{ result[k]=uniq(result[k]); });
    return result;
  }

  /* Readability UI */
  function gradeFromFlesch(f){ if (f>=90) return {label:'Very Easy', hint:'Great for all audiences', pct:100}; if (f>=70) return {label:'Easy', hint:'Good web reading', pct:88}; if (f>=60) return {label:'Fairly Easy', hint:'Still fine', pct:78}; if (f>=50) return {label:'Plain', hint:'OK. Keep sentences short', pct:66}; if (f>=30) return {label:'Fairly Difficult', hint:'Use simpler words', pct:48}; return {label:'Difficult', hint:'Shorten sentences, simplify vocab', pct:34}; }
  function pctBand(x,l,h){ if (x<=l) return 0; if (x>=h) return 100; return Math.round((x-l)*100/(h-l)); }
  function renderReadability(s){
    var grid = document.getElementById('readGrid'); if(!grid) return; grid.innerHTML='';
    var items=[];
    var g=gradeFromFlesch(s.flesch); items.push({ icon:'fa-book', label:'Flesch Score', value: Math.round(s.flesch), badge:g.label, pct: clamp(g.pct,0,100), hint:g.hint });
    items.push({ icon:'fa-random', label:'Sentence Variation (CoV inverse)', value: (1-s.cov).toFixed(2), pct: clamp(Math.round((1-s.cov)*100),0,100), hint:'Balance sentence lengths' });
    items.push({ icon:'fa-font', label:'Type-Token Ratio (TTR)', value: s.TTR.toFixed(2), pct: pctBand(s.TTR,0.30,0.65), hint:'Vary vocabulary' });
    items.push({ icon:'fa-align-left', label:'Long Sentences', value: Math.round(s.longRatio*100)+'%', pct: clamp(Math.round((1-s.longRatio)*100),0,100), hint:'Split sentences ≥28 words' });
    items.push({ icon:'fa-text-height', label:'Avg Word Length', value: s.avgWordLen.toFixed(2), pct: pctBand(6 - s.avgWordLen, 0.2, 2.0), hint:'Prefer shorter words' });
    items.push({ icon:'fa-hashtag', label:'Digits per 100 words', value: s.digitsPer100.toFixed(1), pct: clamp(Math.round(100 - s.digitsPer100*5),0,100), hint:'Reduce numeric noise' });
    items.push({ icon:'fa-layer-group', label:'Trigram Repetition', value: (s.triRepeatRatio*100).toFixed(1)+'%', pct: clamp(Math.round(100 - s.triRepeatRatio*100),0,100), hint:'Avoid repetitive phrasing' });
    items.push({ icon:'fa-file-alt', label:'Word Count', value: s.wordCount, pct: clamp(Math.round(Math.min(1, s.wordCount/1200)*100),0,100), hint:'Target 800–1500 words (topic dependent)' });
    items.forEach(function(it){
      var card=document.createElement('div'); card.className='read-card';
      card.innerHTML='<div class="read-row"><div class="left"><i class="fa '+it.icon+'"></i> <b>'+it.label+'</b></div><div class="read-bar"><div class="read-fill" style="width:'+it.pct+'%"></div></div><div class="badge-mini">'+it.value+'</div></div><div style="color:var(--text-dim);margin-top:.3rem"><i class="fa-regular fa-lightbulb"></i> '+it.hint+'</div>';
      grid.appendChild(card);
    });
  }

  /* Entities UI */
  function tagHTML(txt){
    var g = encodeURIComponent(txt), w = encodeURIComponent(txt.replace(/\s+/g,'_'));
    return '<span class="tag"><i class="fa-solid fa-tag"></i>'+txt+
      ' <a href="https://www.google.com/search?q='+g+'" target="_blank" rel="noopener" title="Google"><i class="fa-brands fa-google"></i></a>'+
      ' <a href="https://en.wikipedia.org/wiki/'+w+'" target="_blank" rel="noopener" title="Wikipedia"><i class="fa-brands fa-wikipedia-w"></i></a>'+
    '</span>';
  }
  function renderEntities(ents){
    var grid=document.getElementById('entitiesGrid'); if(!grid) return; grid.innerHTML='';
    var groups=[{k:'People',icon:'fa-user',color:'#22c55e'},{k:'Orgs',icon:'fa-building',color:'#60a5fa'},{k:'Places',icon:'fa-location-dot',color:'#f59e0b'},{k:'Software',icon:'fa-code',color:'#a78bfa'},{k:'Games',icon:'fa-gamepad',color:'#fb7185'},{k:'Other',icon:'fa-shapes',color:'#94a3b8'}];
    groups.forEach(function(g){
      var list=ents[g.k]||[]; if(!list.length) return;
      var card=document.createElement('div'); card.className='ent-card';
      card.innerHTML='<div class="chip" style="border-color:'+g.color+';background:color-mix(in srgb,'+g.color+' 18%, transparent)"><i class="fa '+g.icon+'"></i> '+g.k+'</div>';
      var wrap=document.createElement('div'); list.forEach(t=>{ wrap.innerHTML += tagHTML(t); }); card.appendChild(wrap); grid.appendChild(card);
    });
  }

  /* PSI helpers */
  function kpiTone(el, val, type){
    var good=false, mid=false;
    if(type==='perf'){ good=val>=90; mid=val>=50 && val<90; }
    if(type==='lcp'){ good=val<2500; mid=val<4000 && val>=2500; }
    if(type==='inp'){ good=val<200; mid=val<500 && val>=200; }
    if(type==='cls'){ good=val<0.1; mid=val<0.25 && val>=0.1; }
    el.classList.remove('good','mid','bad'); el.classList.add(good?'good':(mid?'mid':'bad'));
  }
  function fmtMs(x){ return Math.round(x) + ' ms'; }
  function fmtScore(x){ return Math.round((x||0)*100); }

  async function runPSI(url, strategy){
    var ep=(window.SEMSEO.ENDPOINTS.psi||'/api/pagespeed')+'?url='+encodeURIComponent(url)+'&strategy='+encodeURIComponent(strategy||'mobile');
    try{
      var r=await fetch(ep,{headers:{'Accept':'application/json'}});
      var j=await r.json(); if(!r.ok){ throw new Error((j && j.error)? j.error : 'PSI failed'); }
      return j;
    }catch(e){ return { error: e.message||String(e) }; }
  }
  function applyPSIToSide(j, side){
    var perf=document.getElementById(side+'Perf'), lcp=document.getElementById(side+'LCP'), inp=document.getElementById(side+'INP'), cls=document.getElementById(side+'CLS'), hints=document.getElementById(side+'Hints');
    [perf,lcp,inp,cls].forEach(el=>{ if(el){ el.querySelector('b').textContent='—'; el.classList.remove('good','mid','bad'); }});
    if(!j || j.error){ setHTML(side+'Hints','<div class="chip" style="border-color:#ef4444;background:rgba(239,68,68,.15)"><i class="fa-solid fa-circle-exclamation"></i> '+(j?.error||'Error')+'</div>'); return; }
    var lh=j.lighthouseResult||{}, score=(lh.categories&&lh.categories.performance&&lh.categories.performance.score)||0, audits=lh.audits||{};
    var LCP=(audits['largest-contentful-paint']&&audits['largest-contentful-paint'].numericValue)||null;
    var INP=(audits['interaction-to-next-paint']&&audits['interaction-to-next-paint'].numericValue)||null;
    var CLS=(audits['cumulative-layout-shift']&&audits['cumulative-layout-shift'].numericValue)||null;
    var le=j.loadingExperience||j.originLoadingExperience||{}, m=le.metrics||{};
    if(!LCP && m.LARGEST_CONTENTFUL_PAINT_MS){ LCP=m.LARGEST_CONTENTFUL_PAINT_MS.percentile; }
    if(!INP && (m.INTERACTION_TO_NEXT_PAINT || m.FIRST_INPUT_DELAY_MS)){ INP=(m.INTERACTION_TO_NEXT_PAINT&&m.INTERACTION_TO_NEXT_PAINT.percentile)||(m.FIRST_INPUT_DELAY_MS&&m.FIRST_INPUT_DELAY_MS.percentile)||null; }
    if(!CLS && m.CUMULATIVE_LAYOUT_SHIFT_SCORE){ CLS=m.CUMULATIVE_LAYOUT_SHIFT_SCORE.percentile/100; }
    perf.querySelector('b').textContent=fmtScore(score); kpiTone(perf, Math.round(score*100), 'perf');
    if(LCP!=null){ lcp.querySelector('b').textContent=fmtMs(LCP); kpiTone(lcp, LCP, 'lcp'); }
    if(INP!=null){ inp.querySelector('b').textContent=fmtMs(INP); kpiTone(inp, INP, 'inp'); }
    if(CLS!=null){ cls.querySelector('b').textContent=(Math.round(CLS*1000)/1000).toFixed(3); kpiTone(cls, CLS, 'cls'); }
    var tips=[]; function pushIf(code,label,suggest){ var a=audits[code]; if(a && a.score<0.9){ tips.push('<div class="chip"><i class="fa-regular fa-lightbulb"></i> '+label+': <b>'+suggest+'</b></div>'); } }
    pushIf('unused-javascript','Reduce JS','Code-split & remove unused JS');
    pushIf('render-blocking-resources','Eliminate render-blocking','Preload critical CSS/JS');
    pushIf('uses-optimized-images','Optimize images','Use AVIF/WebP + proper sizes');
    pushIf('server-response-time','Server response','Cache + faster hosting');
    pushIf('total-blocking-time','Blocking time','Defer non-critical scripts');
    pushIf('cumulative-layout-shift','Layout shifts','Reserve media space & avoid late ads');
    hints.innerHTML=tips.join(' ')||'<div class="chip"><i class="fa-solid fa-circle-check" style="color:#22c55e"></i> No major suggestions.</div>';
  }
  async function runPageSpeedBoth(url){
    setText('psiTime', new Date().toLocaleTimeString());
    ['mobile','desktop'].forEach(side=>{
      ['Perf','LCP','INP','CLS'].forEach(k=>{
        var el=document.getElementById(side+k); if(el){ el.querySelector('b').innerHTML='<span class="skel" style="display:inline-block;min-width:80px"></span>'; }
      });
      setHTML(side+'Hints','<div class="skel" style="height:16px;width:70%;border-radius:10px"></div>');
    });
    var [m,d]=await Promise.all([runPSI(url,'mobile'), runPSI(url,'desktop')]);
    applyPSIToSide(m,'mobile'); applyPSIToSide(d,'desktop');
  }

  /* Merge/normalize helpers */
  function normalizeUrl(u){ if(!u) return ''; u=u.trim(); if(/^https?:\/\//i.test(u)){ try{ new URL(u); return u; }catch(e){ return ''; } } var guess='https://'+u.replace(/^\/+/, ''); try{ new URL(guess); return guess; }catch(e){ return ''; } }
  async function fetchBackend(url){
    let data=null, ok=false, status=0, text='';
    const qs=new URLSearchParams({url}).toString();
    try{ const r1=await fetch((window.SEMSEO.ENDPOINTS.analyzeJson||'analyze-json')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); status=r1.status; text=await r1.text(); try{ data=JSON.parse(text);}catch(_){ } if(r1.ok && data) ok=true;}catch(_){}
    if(!ok){ try{ const r2=await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze'),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body:JSON.stringify({url,_token:CSRF})}); status=r2.status; text=await r2.text(); try{ data=JSON.parse(text);}catch(_){ } if(r2.ok && data) ok=true;}catch(_){}
    if(!ok){ try{ const r3=await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); status=r3.status; text=await r3.text(); try{ data=JSON.parse(text);}catch(_){ } if(r3.ok && data) ok=true;}catch(_){}
    return {ok,data,status};
  }
  async function fetchRawHtml(url){ try{ const r=await fetch('https://api.allorigins.win/raw?url='+encodeURIComponent(url),{cache:'no-store'}); if(r.ok){ const html=await r.text(); if(html && html.length>200) return html; } }catch(_){ } return ''; }
  async function fetchReadableText(url){
    try{ const httpsR=await fetch('https://r.jina.ai/http/'+url.replace(/^https?:\/\//,'')); if(httpsR.ok){ const t=await httpsR.text(); if(t && t.length>200) return t; } }catch(_){}
    try{ const altR=await fetch('https://r.jina.ai/'+url); if(altR.ok){ const t=await altR.text(); if(t && t.length>200) return t; } }catch(_){}
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
    ['titleLen','metaLen','canonical','robots','viewport','headings','internalLinks','schema','sampleText'].forEach(function(k){
      if((into[k]===undefined || into[k]===null || into[k]==='—' || into[k]==='' ) && add && add[k]!==undefined && add[k]!==null) into[k]=add[k];
    });
    return into;
  }
  function deriveItemScoresFromSignals(s){
    function pct(x){ return clamp(Math.round(x),0,100); } function band(x,l,h){ if (x<=l) return 0; if (x>=h) return 100; return (x-l)*100/(h-l); }
    var read=pct(band(s.flesch,35,75)), rep=pct(100*(1 - s.triRepeatRatio)), ttr=pct(band(s.TTR,0.30,0.65)), longS=pct(band(1-s.longRatio, 0.6, 0.95)), avgLen=pct(band(s.avgWordLen,4.2,5.8)), digits=pct(100*(1 - s.digitsPer100/20));
    var i=[]; i[1]=pct(.5*read+.5*ttr); i[2]=pct(.6*ttr+.4*avgLen); i[3]=pct(.4*ttr+.6*read); i[4]=pct(.7*read+.3*rep); i[5]=pct(.5*read+.5*avgLen);
    i[6]=pct(.4*ttr+.6*read); i[7]=pct(.4*read+.6*rep); i[8]=pct(.6*rep+.4*digits); i[9]=pct(.6*avgLen+.4*digits); i[10]=pct(.6*avgLen+.4*ttr);
    i[11]=pct(.5*ttr+.5*rep); i[12]=pct(.6*rep+.4*digits); i[13]=pct(.6*read+.4*rep); i[14]=pct(.6*read+.4*ttr); i[15]=pct(.5*ttr+.5*read);
    i[16]=pct(.6*digits+.4*read); i[17]=pct(.5*avgLen+.5*ttr); i[18]=pct(.5*read+.5*longS); i[19]=pct(.6*rep+.4*avgLen); i[20]=pct(.5*longS+.5*avgLen);
    i[21]=pct(.7*read+.3*ttr); i[22]=pct(.6*ttr+.4*avgLen); i[23]=pct(.6*ttr+.4*avgLen); i[24]=pct(.6*avgLen+.4*ttr); i[25]=pct(.6*ttr+.4*digits);
    var map={}; for(var k=1;k<=25;k++){ map[k]=i[k]; } return map;
  }
  function deriveSummaryScoresFromItems(itemMap){
    var all=[]; for(var i=1;i<=25;i++){ if(isFinite(itemMap[i])) all.push(itemMap[i]); }
    var avg = a => a.length? Math.round(a.reduce((x,y)=>x+y,0)/a.length) : 0;
    return { contentScore: avg(all.slice(0,13)), overall: avg(all) };
  }
  function buildSampleFromData(data){
    var parts=[]; ['textSample','extractedText','plainText','body','sample','content','text'].forEach(k=>{ if(typeof data?.[k]==='string' && data[k].length>0) parts.push(data[k]); });
    ['title','meta','description','ogDescription','firstParagraph','snippet','h1','h2','h3'].forEach(function(k){ var v=data?.[k]; if (typeof v === 'string' && v.trim()) parts.push(v); if (Array.isArray(v)) parts.push(v.join('. ')); });
    var txt=parts.join('\n\n').replace(/\s{2,}/g,' ').trim(); return txt.length>140000 ? txt.slice(0,140000) : txt;
  }
  function ensureScoresExist(data, sample, ensemble){
    var needItems=!data.itemScores || Object.keys(data.itemScores).length===0;
    var needContent=typeof data.contentScore!=='number' || isNaN(data.contentScore);
    var needOverall=typeof data.overall!=='number' || isNaN(data.overall);
    var s=(ensemble && ensemble._s)? ensemble._s : _prep(sample||'');
    if(needItems) data.itemScores=deriveItemScoresFromSignals(s);
    if(needContent || needOverall){ var sums=deriveSummaryScoresFromItems(data.itemScores||{}); if(needContent) data.contentScore=sums.contentScore; if(needOverall) data.overall=sums.overall; }
    return data;
  }

  /* Human vs AI UI */
  function renderDetectors(res){
    var grid=document.getElementById('detGrid'), confEl=document.getElementById('detConfidence');
    if(confEl) confEl.textContent = isFinite(res.confidence)? Math.round(res.confidence): '—';
    if(!grid) return; grid.innerHTML='';
    (res.detectors||[{key:'stylometry',label:'Stylometry',ai:res.aiPct||0}]).forEach(function(d){
      var ai=clamp(d.ai||0,0,100), human=clamp(100-ai,0,100); var wrap=document.createElement('div'); wrap.className='det-item';
      wrap.innerHTML='<div class="det-row"><div class="det-label"><i class="fa-solid fa-wave-square"></i> '+d.label+'</div><div class="det-score">'+human+'% H / '+ai+'% AI</div></div><div class="det-bar"><div class="det-fill-human" style="width:'+human+'%"></div><div class="det-fill-ai" style="width:'+ai+'%"></div></div>';
      grid.appendChild(wrap);
    });
  }
  function applyDetection(humanPct, aiPct, confidence, breakdown){
    var hp=isFinite(humanPct)? Math.round(humanPct) : NaN; var ap=isFinite(aiPct)? Math.round(aiPct) : NaN;
    if(isFinite(hp)){ setText('humanPct', hp); setText('hvaiHumanPct', hp); document.getElementById('hvaiHuman').style.width=hp+'%'; }
    if(isFinite(ap)){ setText('aiPct', ap); setText('hvaiAIPct', ap); document.getElementById('hvaiAI').style.width=ap+'%'; }
    var writer=(isFinite(hp)&&isFinite(ap)&&hp>=ap)? 'Likely Human' : 'AI-like';
    var badge=document.getElementById('aiBadge'); if(badge){ var b=badge.querySelector('b'); if(b) b.textContent=writer; badge.title='Confidence: ' + (confidence? confidence+'%':'—'); }
    if(breakdown && breakdown.detectors){ renderDetectors(breakdown); } else { document.getElementById('detGrid').innerHTML=''; }
  }

  /* Checklist auto tick */
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
        if (document.getElementById('autoApply')?.checked && scVal>=80) {
          if (cb && !cb.checked) { cb.checked=true; autoCount++; }
          row?.classList.remove('sev-mid','sev-bad'); row?.classList.add('sev-good');
        } else if (scVal>=60) { row?.classList.remove('sev-bad','sev-good'); row?.classList.add('sev-mid'); }
        else { row?.classList.remove('sev-mid','sev-good'); row?.classList.add('sev-bad'); }
      } else {
        badge.textContent='—'; badge.classList.remove('score-good','score-mid','score-bad');
      }
    }
    setText('rAutoCount', autoCount);
    updateCategoryBars();
  }
  window.autoTickByScores = autoTickByScores;

  /* Analyze (SAFE VERSION) */
  async function analyze(){
    if (window.SEMSEO.BUSY) return;
    window.SEMSEO.BUSY = true;

    var statusEl = document.getElementById('analyzeStatus');
    try{
      var input = document.getElementById('analyzeUrl');
      var url   = normalizeUrl(input ? input.value : '');
      if(!url){ input?.focus(); return; }

      try{ localStorage.setItem('last_url', url); }catch(_){}
      window.Water?.start();
      statusEl && (statusEl.textContent='Fetching & analyzing…');

      var rep0 = document.getElementById('analyzeReport');
      if (rep0 && rep0.style) rep0.style.display = 'none';

      var {ok,data} = await fetchBackend(url); if(!data) data = {};
      var sample = buildSampleFromData(data);

      try{
        var raw = await fetchRawHtml(url);
        if(raw){
          var meta = extractMetaFromHtml(raw, url);
          data = mergeMeta(data, meta);
          if((!sample || sample.length<200) && meta.sampleText) sample = meta.sampleText;
        }
      }catch(_){}

      if((!sample || sample.length<200)){
        try{
          var read = await fetchReadableText(url);
          if(read && read.length>200) sample = read;
        }catch(_){}
      }

      var ensemble = (sample && sample.length>30) ? detectUltra(sample) : null;
      data = ensureScoresExist(data, sample, ensemble);

      var overall = Number(data.overall||0), contentScore = Number(data.contentScore||0);
      window.setScoreWheel(overall||0);
      setText('contentScoreInline', Math.round(contentScore||0));
      setChipTone(document.getElementById('contentScoreChip'), contentScore||0);

      setText('rStatus',   data.httpStatus ? data.httpStatus : '200?');
      setText('rTitleLen', (data.titleLen!==undefined && data.titleLen!==null)? data.titleLen : '—');
      setText('rMetaLen',  (data.metaLen !==undefined && data.metaLen !==null)? data.metaLen  : '—');
      setText('rCanonical',data.canonical ? data.canonical : '—');
      setText('rRobots',   data.robots    ? data.robots    : '—');
      setText('rViewport', data.viewport  ? data.viewport  : '—');
      setText('rHeadings', data.headings  ? data.headings  : '—');
      setText('rInternal', (data.internalLinks!==undefined && data.internalLinks!==null)? data.internalLinks : '—');
      setText('rSchema',   data.schema    ? data.schema    : '—');

      var hp = (typeof data.humanPct==='number')? data.humanPct : NaN;
      var ap = (typeof data.aiPct==='number')? data.aiPct : NaN;
      var backendConf = (typeof data.confidence==='number')? data.confidence : null;

      if (isFinite(hp) && isFinite(ap) && backendConf && backendConf>=65){
        applyDetection(hp,ap,backendConf,ensemble||null);
      } else if (ensemble){
        applyDetection(ensemble.humanPct,ensemble.aiPct,ensemble.confidence,ensemble);
      } else if (isFinite(hp) && isFinite(ap)){
        applyDetection(hp,ap,backendConf||60,null);
      } else {
        applyDetection(NaN,NaN,null,null);
      }

      var s = (ensemble && ensemble._s)? ensemble._s : _prep(sample||'');
      renderReadability(s);
      var ents = extractEntities(sample||'');
      renderEntities(ents);

      window.autoTickByScores(data.itemScores||{});

      window.Water?.finish();
      statusEl && (statusEl.textContent='Analysis complete');
      var rep = document.getElementById('analyzeReport'); if(rep) rep.style.display='block';

      // PSI auto-run (unchanged)
      setTimeout(function(){
        var ep = window.SEMSEO.ENDPOINTS.psi || '/api/pagespeed';
        if (ep && url) { runPageSpeedBoth(url); }
      }, 60);

    } catch(err){
      statusEl && (statusEl.textContent = 'Analyze error: ' + (err && err.message ? err.message : err));
      try{ window.Water?.reset(); }catch(_){}
    } finally {
      window.SEMSEO.BUSY = false; // always release the lock
    }
  }
  window.analyze = analyze;

  /* Events & UX */
  document.addEventListener('DOMContentLoaded', function(){
    try{
      document.getElementById('analyzeBtn')?.addEventListener('click', function(e){ e.preventDefault(); analyze(); });
      var input=document.getElementById('analyzeUrl'); input?.addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); analyze(); }});
      document.getElementById('clearUrl')?.addEventListener('click', function(){ if(input){ input.value=''; input.focus(); } });
      if (navigator.clipboard){ document.getElementById('pasteUrl')?.addEventListener('click', async function(){ try{ var t=await navigator.clipboard.readText(); if(t && input){ input.value=t.trim(); } }catch(e){} }); }

      document.getElementById('runMobile')?.addEventListener('click', async function(){ var u=normalizeUrl(document.getElementById('analyzeUrl').value||''); if(!u) return; setText('psiTime', new Date().toLocaleTimeString()); var m=await runPSI(u,'mobile'); applyPSIToSide(m,'mobile'); });
      document.getElementById('runDesktop')?.addEventListener('click', async function(){ var u=normalizeUrl(document.getElementById('analyzeUrl').value||''); if(!u) return; setText('psiTime', new Date().toLocaleTimeString()); var d=await runPSI(u,'desktop'); applyPSIToSide(d,'desktop'); });
      document.getElementById('runBoth')?.addEventListener('click', async function(){ var u=normalizeUrl(document.getElementById('analyzeUrl').value||''); if(!u) return; runPageSpeedBoth(u); });

      document.getElementById('resetChecklist')?.addEventListener('click', function(){
        Array.prototype.forEach.call(document.querySelectorAll('.checklist input[type="checkbox"]'), cb=>cb.checked=false);
        Array.prototype.forEach.call(document.querySelectorAll('.score-badge'), b=>{ b.textContent='—'; b.classList.remove('score-good','score-mid','score-bad'); });
        updateCategoryBars(); window.setScoreWheel?.(0);
        ['contentScoreInline','humanPct','aiPct'].forEach(id=>{ var el=document.getElementById(id); if(el) el.textContent='—'; });
        document.getElementById('aiBadge')?.querySelector('b') && (document.getElementById('aiBadge').querySelector('b').textContent='—');
        document.getElementById('detGrid').innerHTML=''; document.getElementById('readGrid').innerHTML=''; document.getElementById('entitiesGrid').innerHTML='';
        window.Water?.reset();
      });
      document.getElementById('exportChecklist')?.addEventListener('click', function(){
        var payload={ checked:[], scores:{} };
        for(var i=1;i<=25;i++){
          var cb=document.getElementById('ck-'+i), sc=document.getElementById('sc-'+i);
          if(cb && cb.checked) payload.checked.push(i);
          var s=parseInt(sc ? sc.textContent : 'NaN',10); if(!isNaN(s)) payload.scores[i]=s;
        }
        var blob=new Blob([JSON.stringify(payload,null,2)],{type:'application/json'}); var a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='checklist.json'; a.click(); URL.revokeObjectURL(a.href);
      });
      document.getElementById('importChecklist')?.addEventListener('click', function(){ document.getElementById('importFile')?.click(); });
      document.getElementById('importFile')?.addEventListener('change', function(){
        var file=this.files?.[0]; if(!file) return;
        var fr=new FileReader(); fr.onload=function(){ try{
          var data=JSON.parse(fr.result);
          for(var i=1;i<=25;i++){
            var cb=document.getElementById('ck-'+i); if(cb) cb.checked=(data.checked||[]).includes(i);
            var sc=document.getElementById('sc-'+i); var val=data.scores ? data.scores[i] : undefined;
            if(sc && typeof val==='number'){ sc.textContent=val; (window.badgeTone||function(){ })(sc,val); }
          }
          updateCategoryBars();
        }catch(e){ alert('Invalid JSON'); } }; fr.readAsText(file);
      });

      document.getElementById('printTop')?.addEventListener('click', function(){ window.print(); });
      document.getElementById('printChecklist')?.addEventListener('click', function(){ window.print(); });

      var backTop=document.getElementById('backTop'); window.addEventListener('scroll', function(){ if(backTop) backTop.style.display=(window.scrollY>500)?'grid':'none'; }); backTop?.addEventListener('click', function(){ window.scrollTo({top:0,behavior:'smooth'}); });

      try{ var last=localStorage.getItem('last_url'); if(last && document.getElementById('analyzeUrl')) document.getElementById('analyzeUrl').value=last; }catch(_){}

      window.SEMSEO.READY = true;
    }catch(err){
      var s=document.getElementById('analyzeStatus'); if(s) s.textContent='Boot error: '+err.message;
    }
  });
})();
</script>

<!-- Failsafe binder so Analyze always works -->
<script>
(function(){
  var btn = document.getElementById('analyzeBtn');
  if (btn && !btn.__semseoBound) {
    btn.addEventListener('click', function(e){
      e.preventDefault();
      if (window.analyze) window.analyze();
    });
    btn.__semseoBound = true;
  }
})();
</script>

<!-- Background FX -->
<script>
try{
  (function(){
    var c=document.getElementById('linesCanvas'); if(!c) return; var ctx=c.getContext('2d'); var dpr=Math.min(2,window.devicePixelRatio||1);
    function resize(){ c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0) }
    function draw(t){ ctx.clearRect(0,0,window.innerWidth,window.innerHeight); var w=window.innerWidth,h=window.innerHeight,rows=16,spacing=Math.max(54,h/rows);
      for(var i=-2;i<rows+2;i++){ var y=i*spacing+((t*0.025)%spacing); var g=ctx.createLinearGradient(0,y,w,y+90);
        g.addColorStop(0,'rgba(61,226,255,0.14)'); g.addColorStop(0.5,'rgba(155,92,255,0.16)'); g.addColorStop(1,'rgba(255,32,69,0.14)');
        ctx.strokeStyle=g; ctx.lineWidth=1.5; ctx.beginPath(); ctx.moveTo(-120,y); ctx.lineTo(w+120,y+90); ctx.stroke(); }
      requestAnimationFrame(draw);
    }
    window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
  })();

  (function(){
    var c=document.getElementById('smokeCanvas'); if(!c) return; var ctx=c.getContext('2d');
    var dpr=Math.min(2,window.devicePixelRatio||1), blobs=[], last=performance.now();
    function resize(){
      c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0);
      var W=window.innerWidth, H=window.innerHeight; var N=64;
      blobs=new Array(N).fill(0).map(function(_,i){
        var px=W*0.65 + Math.random()*W*0.45, py=H*0.65 + Math.random()*H*0.45, r=120+Math.random()*260, speed=0.18+Math.random()*0.22;
        return { x:px, y:py, r:r, vx:-speed*(0.6+Math.random()*0.8), vy:-speed*(0.6+Math.random()*0.8), baseHue:(i*37)%360, alpha:.22+.22*Math.random() };
      });
      last=performance.now();
    }
    function draw(now){
      var W=window.innerWidth, H=window.innerHeight; ctx.clearRect(0,0,W,H); ctx.globalCompositeOperation='screen';
      var dt=now-last; last=now;
      for(var i=0;i<blobs.length;i++){
        var b=blobs[i]; b.x+=b.vx*dt; b.y+=b.vy*dt; if(b.x<-360 || b.y<-360){ b.x=W+Math.random()*260; b.y=H+Math.random()*260; }
        var hue=(b.baseHue + (now % 1000000000) * (360/1000000000)) % 360;
        var g=ctx.createRadialGradient(b.x,b.y,0,b.x,b.y,b.r); g.addColorStop(0,'hsla('+hue+',88%,68%,'+b.alpha+')'); g.addColorStop(1,'hsla('+((hue+70)%360)+',88%,50%,0)'); ctx.fillStyle=g; ctx.beginPath(); ctx.arc(b.x,b.y,b.r,0,Math.PI*2); ctx.fill();
      }
      requestAnimationFrame(draw);
    }
    window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
  })();
} catch(e){ var s=document.getElementById('analyzeStatus'); if(s) s.textContent='FX error: '+e.message; }
</script>

<!-- Error sink -->
<script>
window.addEventListener('error', function(e){
  var s=document.getElementById('analyzeStatus');
  if (s) s.textContent = 'JavaScript error: ' + (e && e.message ? e.message : e);
});
</script>
</body>
</html>
