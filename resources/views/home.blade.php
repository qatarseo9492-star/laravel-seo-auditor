{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, and UX signals, with water-fill scoring, auto-checklist, and AI/Human signals.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();

  // Endpoints: use named routes if present, else safe fallbacks
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

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
:root{--bg:#07080e;--panel:#0f1022;--panel-2:#141433;--text:#f0effa;--text-dim:#b6b3d6;--good:#22c55e;--warn:#f59e0b;--bad:#ef4444;--accent:#3de2ff;--radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55);--container:1200px;--hue:0deg}
*{box-sizing:border-box}html,body{height:100%}html{scroll-behavior:smooth}
body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,Segoe UI,Roboto;background:radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),var(--bg);overflow-x:hidden}
/* cloud canvases behind everything, never block clicks */
#linesCanvas,#smokeCanvas{position:fixed;inset:0;pointer-events:none;z-index:0}
#linesCanvas{opacity:.38}#smokeCanvas{opacity:.42}
.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}

/* Header */
header.site{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:14px 0 22px;border-bottom:1px solid rgba(255,255,255,.08)}
.brand{display:flex;align-items:center;gap:.8rem;min-width:0}
.brand-badge{width:48px;height:48px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(157,92,255,.25),rgba(61,226,255,.25));border:1px solid rgba(255,255,255,.14);color:#fff;font-size:1.05rem;box-shadow:0 8px 22px rgba(0,0,0,.28)}
.hero-heading{font-weight:1000;letter-spacing:.4px;font-size:clamp(1.4rem,3.2vw,2rem)}
.hero-sub{color:var(--text-dim);font-size:.95rem}

/* Buttons */
.btn{display:inline-flex;align-items:center;gap:.55rem;cursor:pointer;padding:.6rem .95rem;border-radius:14px;border:1px solid rgba(255,255,255,.16);color:#fff;font-weight:900;letter-spacing:.2px;position:relative;overflow:hidden;box-shadow:0 10px 28px rgba(0,0,0,.25)}
.btn::after{content:"";position:absolute;inset:-2px;border-radius:inherit;opacity:.0;background:linear-gradient(120deg,transparent,rgba(255,255,255,.22),transparent 60%);transform:translateX(-120%);transition:opacity .2s ease}
.btn:hover::after{opacity:1;animation:btnSweep 2.6s linear infinite}
@keyframes btnSweep{0%{transform:translateX(-120%)}100%{transform:translateX(120%)}}
.btn-analyze{background:linear-gradient(135deg,#10b981,#22c55e);border-color:#20d391}
.btn-print{background:linear-gradient(135deg,#3b82f6,#6366f1);border-color:#5b77ef}
.btn-reset{background:linear-gradient(135deg,#f59e0b,#f97316);border-color:#f59e0b}
.btn-export{background:linear-gradient(135deg,#a855f7,#ec4899);border-color:#c26cf2}
.btn-ghost{background:rgba(255,255,255,.06)}
.btn:disabled{opacity:.6;cursor:not-allowed}

/* Analyzer panel */
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem}.section-subtitle{margin:0;color:var(--text-dim)}
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}

/* Gauge */
.score-container{width:220px}
.score-gauge{position:relative;width:100%;aspect-ratio:1/1}.gauge-svg{width:100%;height:auto;display:block}
.score-mask-rect{transition:all .6s cubic-bezier(.22,1,.36,1)}
.score-wave1{animation:scoreWave 8s linear infinite}.score-wave2{animation:scoreWave 11s linear infinite reverse}
@keyframes scoreWave{from{transform:translateX(0)}to{transform:translateX(-210px)}}
.score-text{font-size:clamp(2.2rem,4.2vw,3.1rem);font-weight:1000;fill:#fff;text-shadow:0 0 18px rgba(255,32,69,.25)}
.multiHueFast{filter:hue-rotate(var(--hue)) saturate(140%);will-change:filter}

/* Chips & legends */
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)}.l-orange{background:rgba(245,158,11,.18)}.l-green{background:rgba(34,197,94,.18)}
.chip-good{background:rgba(34,197,94,.18)!important;border-color:rgba(34,197,94,.45)!important}
.chip-mid{background:rgba(245,158,11,.18)!important;border-color:rgba(245,158,11,.45)!important}
.chip-bad{background:rgba(239,68,68,.18)!important;border-color:rgba(239,68,68,.5)!important}
.ico{width:1.1em;text-align:center}.ico-green{color:var(--good)}.ico-orange{color:var(--warn)}.ico-red{color:var(--bad)}.ico-cyan{color:var(--accent)}.ico-purple{color:#9b5cff}

/* URL field */
.url-field{position:relative;border-radius:16px;background:#0b0d21;border:1px solid #1b1b35;box-shadow:inset 0 0 0 1px rgba(255,255,255,.02),0 12px 32px rgba(0,0,0,.32);padding:10px 110px 10px 46px;transition:.25s ease;overflow:hidden;isolation:isolate}
.url-field:focus-within{border-color:#5942ff;box-shadow:0 0 0 6px rgba(155,92,255,.15),inset 0 0 0 1px rgba(93,65,255,.28)}
.url-field .url-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9aa0c3;font-size:1rem;opacity:.95}
.url-field input{all:unset;color:var(--text);width:100%;font-size:1rem;letter-spacing:.2px}
.url-field .url-mini{position:absolute;top:50%;transform:translateY(-50%);border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;border-radius:10px;padding:.35rem .6rem;font-weight:900;cursor:pointer;transition:.15s}
.url-field .url-mini:hover{background:rgba(255,255,255,.12)}.url-field .url-clear{right:60px;width:36px;height:32px;display:grid;place-items:center}.url-field #pasteUrl{right:12px}
.url-field .url-border{content:"";position:absolute;inset:-2px;border-radius:inherit;padding:2px;background:conic-gradient(from 0deg,#3de2ff,#9b5cff,#ff2045,#f59e0b,#3de2ff);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.55;pointer-events:none;filter:hue-rotate(var(--hue))}

.analyze-row{display:grid;grid-template-columns:1fr auto auto auto auto;gap:.6rem;align-items:center;margin-top:.6rem}

/* Analyze water progress */
.water-wrap{margin-top:.8rem;display:none}
.waterbar{position:relative;height:64px;border-radius:18px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.water-svg{position:absolute;inset:0;width:100%;height:100%;z-index:1}
.water-mask-rect{transition:all .25s ease-out}
.water-overlay{position:absolute;inset:0;pointer-events:none;background:radial-gradient(120px 60px at 20% -20%,rgba(255,255,255,.18),transparent 60%),linear-gradient(0deg,rgba(255,255,255,.05),transparent 40%,transparent 60%,rgba(255,255,255,.06));mix-blend-mode:screen;z-index:2}
.water-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1.05rem;text-shadow:0 1px 0 rgba(0,0,0,.45);letter-spacing:.4px;z-index:4}
.wave1{animation:waveX 7s linear infinite}.wave2{animation:waveX 10s linear infinite reverse;opacity:.7}
@keyframes waveX{0%{transform:translateX(0)}100%{transform:translateX(-600px)}}
.multiHue{filter:hue-rotate(var(--hue)) saturate(140%);will-change:filter}

/* Completion */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.comp-water{position:relative;height:52px;border-radius:16px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.comp-svg{position:absolute;inset:0;width:100%;height:100%;z-index:1}.comp-overlay{position:absolute;inset:0;background:radial-gradient(120px 50px at 15% -25%,rgba(255,255,255,.16),transparent 55%),linear-gradient(180deg,rgba(255,255,255,.08),transparent 35%,rgba(255,255,255,.06));pointer-events:none;mix-blend-mode:screen;z-index:3}
.comp-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1rem;z-index:4;text-shadow:0 1px 0 rgba(0,0,0,.45)}
.comp-wave1{animation:waveX 8s linear infinite}.comp-wave2{animation:waveX 12s linear infinite reverse}

/* Cards & checklist */
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
.cat-wave1{animation:catWave 7s linear infinite}.cat-wave2{animation:catWave 10s linear infinite reverse}
@keyframes catWave{from{transform:translateX(0)}to{transform:translateX(-640px)}}
.cat-water-pct{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.8rem;color:rgba(255,255,255,.9);text-shadow:0 1px 0 rgba(0,0,0,.55);pointer-events:none}

.checklist{list-style:none;margin:10px 0 0;padding:0}
.checklist-item{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.7rem .75rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02)),radial-gradient(100% 120% at 0% 0%,rgba(61,226,255,.06),transparent 30%),radial-gradient(120% 100% at 100% 0%,rgba(155,92,255,.05),transparent 35%);transition:box-shadow .25s ease,background .25s ease,transform .12s ease}
.checklist-item+.checklist-item{margin-top:.28rem}.checklist-item:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.25)}
.checklist-item label{cursor:pointer;display:inline-flex;align-items:center;gap:.55rem}

.sev-good{background:linear-gradient(180deg,rgba(34,197,94,.14),rgba(34,197,94,.08));border-color:rgba(34,197,94,.45)}
.sev-mid{background:linear-gradient(180deg,rgba(245,158,11,.16),rgba(245,158,11,.08));border-color:rgba(245,158,11,.45)}
.sev-bad{background:linear-gradient(180deg,rgba(239,68,68,.16),rgba(239,68,68,.10));border-color:rgba(239,68,68,.55)}

.checklist-item input[type="checkbox"]{appearance:none;-webkit-appearance:none;outline:none;width:22px;height:22px;border-radius:8px;background:#0b1220;border:2px solid #2a2f4d;position:relative;display:inline-grid;place-items:center;transition:.18s ease-in-out;box-shadow:inset 0 0 0 0 rgba(99,102,241,.0)}
.checklist-item input[type="checkbox"]:hover{border-color:#4c5399;box-shadow:0 0 0 4px rgba(99,102,241,.12)}
.checklist-item input[type="checkbox"]::after{content:"";width:7px;height:12px;border:3px solid transparent;border-left:0;border-top:0;transform:rotate(45deg) scale(.7);transition:.18s ease-in-out}
.checklist-item input[type="checkbox"]:checked{border-color:transparent;background:linear-gradient(135deg,#22c55e,#3de2ff,#9b5cff);background-size:200% 200%;animation:tickHue 2s linear infinite;box-shadow:0 6px 18px rgba(61,226,255,.25),inset 0 0 0 2px rgba(255,255,255,.25)}
.checklist-item input[type="checkbox"]:checked::after{border-color:#fff;filter:drop-shadow(0 1px 0 rgba(0,0,0,.4));transform:rotate(45deg) scale(1)}
@keyframes tickHue{0%{background-position:0% 50%}100%{background-position:200% 50%}}

.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22);border-color:rgba(22,193,114,.45)}
.score-mid{background:rgba(245,158,11,.22);border-color:rgba(245,158,11,.45)}
.score-bad{background:rgba(239,68,68,.24);border-color:rgba(239,68,68,.5)}

.improve-btn{position:relative;overflow:hidden;padding:.45rem .8rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:linear-gradient(135deg,rgba(255,255,255,.06),rgba(255,255,255,.02));font-weight:900;cursor:pointer;transition:.2s;isolation:isolate;min-width:88px}
.improve-btn:hover{transform:translateY(-1px);background:rgba(255,255,255,.1)}
.improve-btn::before{content:"";position:absolute;inset:-2px;border-radius:inherit;z-index:0;background:linear-gradient(120deg,transparent 0%,rgba(255,255,255,.18) 45%,transparent 50%,transparent 100%);transform:translateX(-120%);animation:btnSheen 3.2s linear infinite}
@keyframes btnSheen{0%{transform:translateX(-120%)}60%{transform:translateX(120%)}100%{transform:translateX(120%)}}

/* Social dock */
.share-dock{position:fixed;right:16px;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;gap:.5rem;z-index:85;background:rgba(10,12,28,.35);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:.5rem;backdrop-filter:blur(8px)}
.share-btn{width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;color:#fff;cursor:pointer;text-decoration:none;position:relative;overflow:hidden;transition:transform .15s ease,box-shadow .15s ease}
.share-btn:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(0,0,0,.35)}
.share-fb{background:linear-gradient(135deg,#1877F2,#1e90ff)}.share-x{background:linear-gradient(135deg,#111,#333)}.share-ln{background:linear-gradient(135deg,#0a66c2,#1a8cd8)}.share-wa{background:linear-gradient(135deg,#25D366,#128C7E)}.share-em{background:linear-gradient(135deg,#ef4444,#b91c1c)}

footer.site{margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)}
#backTop{position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none}
#backTop:hover{background:rgba(255,255,255,.12)}

/* Responsive */
@media (max-width:992px){.category-card{grid-column:span 12}.score-container{width:190px}.analyze-row{grid-template-columns:1fr auto auto}}
@media (max-width:768px){.wrap{padding:18px 4%}header.site{flex-direction:column;align-items:flex-start;gap:.6rem}.score-area{flex-direction:column;align-items:flex-start;gap:.8rem}.score-container{width:170px}.analyze-row{grid-template-columns:1fr}.analyze-row .btn{width:100%;justify-content:center}.share-dock{top:auto;bottom:10px;right:50%;transform:translateX(50%);flex-direction:row;padding:.35rem .45rem;border-radius:999px;gap:.4rem;background:rgba(10,12,28,.55)}.share-btn{width:44px;height:44px;border-radius:999px}.checklist-item{grid-template-columns:1fr auto auto}.checklist-item .improve-btn{grid-column:1/-1;justify-self:flex-start;margin-top:.25rem}}
@media (max-width:480px){.score-container{width:150px}.category-icon{width:40px;height:40px}.category-title{font-size:1rem}}
@media (prefers-reduced-motion: reduce){.score-wave1,.score-wave2,.wave1,.wave2,.cat-wave1,.cat-wave2,.comp-wave1,.comp-wave2{animation:none!important}.multiHue,.multiHueFast{filter:none!important}}
@media print{.share-dock,#backTop,#linesCanvas,#smokeCanvas{display:none!important}}
</style>
</head>
<body>

<!-- Background canvases -->
<canvas id="linesCanvas"></canvas>
<canvas id="smokeCanvas"></canvas>

<!-- SAFETY: expose endpoints & a minimal fallback click handler EARLY -->
<script>
  window.SEMSEO = window.SEMSEO || {};
  window.SEMSEO.ENDPOINTS = {
    analyzeJson: @json($analyzeJsonUrl),
    analyze: @json($analyzeUrl)
  };
  function SEMSEO_go(){ try { if (typeof analyze === 'function') { analyze(); } else { alert('Analyzer not ready — please wait a moment and click again.'); } } catch(e){ alert('JS error: '+ e.message); } }
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
      The wheel fills with your overall score.
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
                <stop id="scoreStop1" offset="0%" stop-color="#22c55e"/>
                <stop id="scoreStop2" offset="100%" stop-color="#16a34a"/>
              </linearGradient>
              <linearGradient id="ringGrad" x1="0" y1="0" x2="1" y2="1">
                <stop id="ringStop1" offset="0%" stop-color="#22c55e"/>
                <stop id="ringStop2" offset="100%" stop-color="#16a34a"/>
              </linearGradient>
              <filter id="ringGlow" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="2.4" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
              </filter>
              <path id="scoreWavePath" d="M0 110 Q 15 90 30 110 T 60 110 T 90 110 T 120 110 T 150 110 T 180 110 T 210 110 V 220 H 0 Z"/>
            </defs>
            <circle cx="100" cy="100" r="96" fill="rgba(255,255,255,.06)" stroke="rgba(255,255,255,.12)" stroke-width="2"/>
            <circle id="ringTrack" cx="100" cy="100" r="95" fill="none" stroke="rgba(255,255,255,.12)" stroke-width="6" transform="rotate(-90 100 100)"/>
            <circle id="ringArc" cx="100" cy="100" r="95" fill="none" stroke="url(#ringGrad)" stroke-width="6" stroke-linecap="round" filter="url(#ringGlow)" opacity=".95" transform="rotate(-90 100 100)" />
            <g clip-path="url(#scoreCircleClip)">
              <rect x="0" y="0" width="200" height="200" fill="#0b0d21"/>
              <g clip-path="url(#scoreFillClip)">
                <g class="score-wave1 multiHueFast">
                  <use href="#scoreWavePath" x="0" fill="url(#scoreGrad)"/>
                  <use href="#scoreWavePath" x="210" fill="url(#scoreGrad)"/>
                </g>
                <g class="score-wave2 multiHueFast" opacity=".85">
                  <use href="#scoreWavePath" x="0" y="6" fill="url(#scoreGrad)"/>
                  <use href="#scoreWavePath" x="210" y="6" fill="url(#scoreGrad)"/>
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
          <span class="chip" id="aiBadge" title="AI/Human detection summary"><i class="fa-solid fa-user-check ico ico-green"></i> Writer: <b>—</b></span>
          <button id="viewHumanBtn" class="btn btn-ghost"><i class="fa-solid fa-user ico ico-green"></i> Human-like: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost"><i class="fa-solid fa-microchip ico ico-red"></i> AI-like: <b id="aiPct">—</b>%</button>
          <button id="copyQuick" class="btn btn-ghost"><i class="fa-regular fa-copy ico ico-cyan"></i> Copy report</button>
        </div>
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

        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:#9b5cff">
              <span>Auto-apply checkmarks (≥ 80)</span>
            </label>
          </div>

          <!-- Safe inline fallback -->
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
                <linearGradient id="waterGrad" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#3de2ff"/><stop offset="100%" stop-color="#9b5cff"/>
                </linearGradient>
                <clipPath id="roundClip"><rect x="1" y="1" width="598" height="198" rx="18" ry="18"/></clipPath>
                <clipPath id="fillClip"><rect id="waterClipRect" class="water-mask-rect" x="0" y="200" width="600" height="200"/></clipPath>
                <path id="wave" d="M0 120 Q 50 90 100 120 T 200 120 T 300 120 T 400 120 T 500 120 T 600 120 V 220 H 0 Z"/>
              </defs>
              <g clip-path="url(#roundClip)">
                <rect x="0" y="0" width="600" height="200" fill="#0b0d21"/>
                <g clip-path="url(#fillClip)">
                  <g class="wave1 multiHue">
                    <use href="#wave" x="0" fill="url(#waterGrad)"/><use href="#wave" x="600" fill="url(#waterGrad)"/>
                  </g>
                  <g class="wave2 multiHue" opacity=".65">
                    <use href="#wave" x="0" y="8" fill="url(#waterGrad)"/><use href="#wave" x="600" y="8" fill="url(#waterGrad)"/>
                  </g>
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

    <div class="progress-wrap">
      <div class="comp-water" id="compWater" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
        <svg class="comp-svg" viewBox="0 0 600 140" preserveAspectRatio="none">
          <defs>
            <clipPath id="compRound"><rect x="1" y="1" width="598" height="138" rx="14" ry="14"/></clipPath>
            <clipPath id="compFillClip"><rect id="compClipRect" x="0" y="0" width="0" height="140"/></clipPath>
            <linearGradient id="compGrad" x1="0" y1="0" x2="1" y2="1">
              <stop id="compStop1" offset="0%" stop-color="#3de2ff"/><stop id="compStop2" offset="100%" stop-color="#9b5cff"/>
            </linearGradient>
            <path id="compWave" d="M0 80 Q 50 60 100 80 T 200 80 T 300 80 T 400 80 T 500 80 T 600 80 V 160 H 0 Z"/>
          </defs>
          <g clip-path="url(#compRound)">
            <rect x="0" y="0" width="600" height="140" fill="#0b0d21"/>
            <g clip-path="url(#compFillClip)">
              <g class="comp-wave1 multiHue">
                <use href="#compWave" x="0" fill="url(#compGrad)"/><use href="#compWave" x="600" fill="url(#compGrad)"/>
              </g>
              <g class="comp-wave2 multiHue" opacity=".75">
                <use href="#compWave" x="0" y="6" fill="url(#compGrad)"/><use href="#compWave" x="600" y="6" fill="url(#compGrad)"/>
              </g>
            </g>
          </g>
        </svg>
        <div class="comp-overlay"></div>
        <div class="comp-pct"><span id="compPct">0%</span></div>
      </div>
      <div id="progressCaption" class="progress-caption" style="color:var(--text-dim)">0 of 25 items completed</div>
    </div>

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
                      <g class="cat-wave1">
                        <use href="#catWave-{{ $loop->index }}" x="0" fill="url(#catGrad-{{ $loop->index }})"/>
                        <use href="#catWave-{{ $loop->index }}" x="640" fill="url(#catGrad-{{ $loop->index }})"/>
                      </g>
                      <g class="cat-wave2" opacity=".85">
                        <use href="#catWave-{{ $loop->index }}" x="0" y="3" fill="url(#catGrad-{{ $loop->index }})"/>
                        <use href="#catWave-{{ $loop->index }}" x="640" y="3" fill="url(#catGrad-{{ $loop->index }})"/>
                      </g>
                    </g>
                  </g>
                </svg>
                <div class="cat-water-pct" id="catPct-{{ $loop->index }}">0/0 • 0%</div>
              </div>
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

<footer class="site">
  <div><strong>Semantic SEO Master</strong></div>
  <div class="footer-links">
    <a href="#analyzer">Analyzer</a>
    <a href="#" id="toTopLink">Back to top</a>
  </div>
</footer>

<button id="backTop" title="Back to top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

<!-- A) Analyze + core logic FIRST (so it's always defined) -->
<script>
(function(){
  var CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : '';
  function setText(id,val){ var el=document.getElementById(id); if(el){ el.textContent=val; } return el; }
  function setChipTone(el, v){ if(!el) return; el.classList.remove('chip-good','chip-mid','chip-bad'); var n=Number(v)||0; el.classList.add(n>=80?'chip-good':(n>=60?'chip-mid':'chip-bad')); }
  function badgeTone(el, v){ if(!el) return; el.classList.remove('score-good','score-mid','score-bad'); el.classList.add(v>=80?'score-good':(v>=60?'score-mid':'score-bad')); }

  // Gauge
  var GAUGE={rect:null,stop1:null,stop2:null,r1:null,r2:null,arc:null,text:null,H:200,CIRC:2*Math.PI*95};
  window.setScoreWheel = function(value){
    if(!GAUGE.rect){
      GAUGE.rect=document.getElementById('scoreClipRect'); GAUGE.stop1=document.getElementById('scoreStop1'); GAUGE.stop2=document.getElementById('scoreStop2');
      GAUGE.r1=document.getElementById('ringStop1'); GAUGE.r2=document.getElementById('ringStop2');
      GAUGE.arc=document.getElementById('ringArc'); GAUGE.text=document.getElementById('overallScore');
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

  function autoTickByScores(map){
    var autoCount=0;
    for(var i=1;i<=25;i++){
      var scVal=Number((map && map[i]!==undefined)? map[i] : NaN);
      var badge=document.getElementById('sc-'+i);
      var cb=document.getElementById('ck-'+i);
      var row=cb ? cb.closest('.checklist-item') : null;
      if (!badge) continue;
      if (!isNaN(scVal)) {
        badge.textContent = scVal; badgeTone(badge, scVal);
        if (document.getElementById('autoApply') && document.getElementById('autoApply').checked && scVal>=80) {
          if (cb && !cb.checked) { cb.checked=true; autoCount++; }
          if(row){ row.classList.remove('sev-mid','sev-bad'); row.classList.add('sev-good'); }
        } else if (scVal>=60) { if(row){ row.classList.remove('sev-bad','sev-good'); row.classList.add('sev-mid'); } }
        else { if(row){ row.classList.remove('sev-mid','sev-good'); row.classList.add('sev-bad'); } }
      } else {
        badge.textContent='—'; badge.classList.remove('score-good','score-mid','score-bad');
      }
    }
    setText('rAutoCount', autoCount);
    updateCategoryBars();
  }
  window.autoTickByScores = autoTickByScores;

  // Water progress
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

  function normalizeUrl(u) {
    if (!u) return '';
    u = u.trim();
    if (/^https?:\/\//i.test(u)) { try { new URL(u); return u; } catch(e) { return ''; } }
    var guess = 'https://' + u.replace(/^\/+/, '');
    try { new URL(guess); return guess; } catch(e) { return ''; }
  }

  // --- THE analyzer() FUNCTION (always defined) ---
  window.analyze = async function(){
    var input = document.getElementById('analyzeUrl');
    var url = normalizeUrl(input ? input.value : '');
    if (!url) { if(input) input.focus(); return; }

    Water.start();
    var statusEl = document.getElementById('analyzeStatus');
    if (statusEl) statusEl.textContent = 'Fetching & analyzing…';
    var report = document.getElementById('analyzeReport'); if (report) report.style.display = 'none';

    var data=null, ok=false, status=0, text='', lastErr='';
    var qs = new URLSearchParams({ url: url }).toString();

    try{
      var res1 = await fetch((window.SEMSEO.ENDPOINTS.analyzeJson||'analyze-json') + '?' + qs, {
        method:'GET', headers:{ 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }
      });
      status = res1.status; text = await res1.text();
      try{ data = JSON.parse(text); }catch(e){}
      if (res1.ok && data) ok = true;
    }catch(e){ lastErr = 'GET analyze-json failed: '+e.message; }

    if (!ok){
      try{
        var res2 = await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze'), {
          method:'POST',
          headers:{ 'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': CSRF },
          body: JSON.stringify({ url: url, _token: CSRF })
        });
        status = res2.status; text = await res2.text();
        try{ data = JSON.parse(text); }catch(e){}
        if (res2.ok && data) ok = true;
      }catch(e){ lastErr = 'POST analyze failed: '+e.message; }
    }

    if (!ok){
      try{
        var res3 = await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze') + '?' + qs, {
          method:'GET', headers:{ 'Accept':'application/json','X-Requested-With':'XMLHttpRequest' }
        });
        status = res3.status; text = await res3.text();
        try{ data = JSON.parse(text); }catch(e){}
        if (res3.ok && data) ok = true;
      }catch(e){ lastErr = 'GET analyze failed: '+e.message; }
    }

    if (!ok || !data){
      console.log('Analyze failed', status, lastErr, (text && text.slice ? text.slice(0,300) : ''));
      Water.finish();
      if (statusEl) statusEl.textContent = (text && text.length < 400 ? text : ('Could not analyze this URL (status '+status+'). '+ (lastErr||'')));
      return;
    }

    var overall = Number(data.overall || 0);
    var contentScore = Number(data.contentScore || 0);
    var humanPct = Number(data.humanPct || 0);
    var aiPct    = Number(data.aiPct || 0);
    var writer = humanPct>=aiPct ? 'Likely Human' : 'AI-like';

    window.setScoreWheel(overall||0);
    setText('contentScoreInline', Math.round(contentScore||0));
    setChipTone(document.getElementById('contentScoreChip'), contentScore||0);
    var badge = document.getElementById('aiBadge'); if (badge){ var b=badge.querySelector('b'); if(b) b.textContent = writer; }
    setText('humanPct', Math.round(humanPct||0));
    setText('aiPct', Math.round(aiPct||0));

    setText('rStatus',    data.httpStatus ? data.httpStatus : '—');
    setText('rTitleLen',  data.titleLen   ? data.titleLen   : '—');
    setText('rMetaLen',   data.metaLen    ? data.metaLen    : '—');
    setText('rCanonical', data.canonical  ? data.canonical  : '—');
    setText('rRobots',    data.robots     ? data.robots     : '—');
    setText('rViewport',  data.viewport   ? data.viewport   : '—');
    setText('rHeadings',  data.headings   ? data.headings   : '—');
    setText('rInternal',  data.internalLinks ? data.internalLinks : '—');
    setText('rSchema',    data.schema     ? data.schema     : '—');

    window.autoTickByScores(data.itemScores || {});
    Water.finish();
    if (statusEl) statusEl.textContent = 'Analysis complete';
    if (report) report.style.display = 'block';
  };

  // Minimal wire-up so the button works even if later scripts fail
  document.addEventListener('DOMContentLoaded', function(){
    var btn = document.getElementById('analyzeBtn');
    if (btn){ btn.addEventListener('click', function(e){ e.preventDefault(); window.analyze(); }); }
    var input = document.getElementById('analyzeUrl');
    if (input){ input.addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); window.analyze(); }}); }
  });

})();
</script>

<!-- B) Non-critical UI (wrapped so errors never block analyzer) -->
<script>
try{
  // Hue drift for multi-color water
  (function(){ var root=document.documentElement; var start=performance.now(); function frame(now){ root.style.setProperty('--hue', (((now-start)/4)%360) + 'deg'); requestAnimationFrame(frame);} requestAnimationFrame(frame); })();

  // Share links + UX
  (function(){
    var url = encodeURIComponent(location.href), title = encodeURIComponent(document.title);
    var fb = document.getElementById('shareFb'), x = document.getElementById('shareX'), ln = document.getElementById('shareLn'), wa = document.getElementById('shareWa'), em = document.getElementById('shareEm');
    if(fb) fb.href = 'https://www.facebook.com/sharer/sharer.php?u='+url;
    if(x)  x.href  = 'https://twitter.com/intent/tweet?text='+title+'&url='+url;
    if(ln) ln.href = 'https://www.linkedin.com/sharing/share-offsite/?url='+url;
    if(wa) wa.href = 'https://wa.me/?text='+title+'%20'+url;
    if(em) em.href = 'mailto:?subject='+title+'&body='+url;
  })();

  // Reset / Export / Import / Print
  (function(){
    function updateCategoryBars(){ if (window.updateCategoryBars) window.updateCategoryBars(); }
    var resetBtn=document.getElementById('resetChecklist');
    if(resetBtn){ resetBtn.addEventListener('click', function(){
      Array.prototype.forEach.call(document.querySelectorAll('.checklist input[type="checkbox"]'), function(cb){ cb.checked=false; });
      Array.prototype.forEach.call(document.querySelectorAll('.score-badge'), function(b){ b.textContent='—'; b.classList.remove('score-good','score-mid','score-bad'); });
      updateCategoryBars();
      if (window.setScoreWheel) window.setScoreWheel(0);
      var el;
      el=document.getElementById('contentScoreInline'); if(el) el.textContent='0';
      var chip=document.getElementById('contentScoreChip'); if(chip){ chip.classList.remove('chip-good','chip-mid','chip-bad'); chip.classList.add('chip-bad'); }
      el=document.getElementById('humanPct'); if(el) el.textContent='—';
      el=document.getElementById('aiPct'); if(el) el.textContent='—';
      var badge=document.getElementById('aiBadge'); if(badge){ var b=badge.querySelector('b'); if(b) b.textContent='—'; }
      if (window.Water) window.Water.reset();
    });}

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
          if (sc && typeof val==='number'){ sc.textContent=val; (window.badgeTone||function(){ })(sc,val); }
        }
        updateCategoryBars();
      }catch(e){ alert('Invalid JSON'); } };
      fr.readAsText(file);
    });}

    var printTop=document.getElementById('printTop'), printChecklist=document.getElementById('printChecklist');
    if(printTop) printTop.addEventListener('click', function(){ window.print(); });
    if(printChecklist) printChecklist.addEventListener('click', function(){ window.print(); });

    var toTop=document.getElementById('toTopLink'), backTop=document.getElementById('backTop');
    if(toTop){ toTop.addEventListener('click', function(e){ e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'});}); }
    window.addEventListener('scroll', function(){ if(backTop) backTop.style.display = (window.scrollY>500)?'grid':'none'; });
  })();

} catch(e){ var s=document.getElementById('analyzeStatus'); if(s) s.textContent='JS (UI) error: '+e.message; }
</script>

<!-- C) Background smoke (isolated so failures never affect analyze) -->
<script>
try{
  // lines
  (function(){
    var c=document.getElementById('linesCanvas'); if(!c) return; var ctx=c.getContext('2d'); var dpr=Math.min(2,window.devicePixelRatio||1);
    function resize(){ c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0) }
    function draw(t){ ctx.clearRect(0,0,window.innerWidth,window.innerHeight); var w=window.innerWidth,h=window.innerHeight,rows=12,spacing=Math.max(60,h/rows);
      for(var i=-2;i<rows+2;i++){ var y=i*spacing+((t*0.02)%spacing); var g=ctx.createLinearGradient(0,y,w,y+80);
        g.addColorStop(0,'rgba(61,226,255,0.06)'); g.addColorStop(0.5,'rgba(155,92,255,0.08)'); g.addColorStop(1,'rgba(255,32,69,0.06)');
        ctx.strokeStyle=g; ctx.lineWidth=1.2; ctx.beginPath(); ctx.moveTo(-100,y); ctx.lineTo(w+100,y+80); ctx.stroke(); }
      requestAnimationFrame(draw);
    }
    window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
  })();

  // smoke blobs
  (function(){
    var c=document.getElementById('smokeCanvas'); if(!c) return; var ctx=c.getContext('2d'); var dpr=Math.min(2,window.devicePixelRatio||1), blobs=[];
    function resize(){ c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0);
      blobs=Array.apply(null,Array(42)).map(function(){ return {x:Math.random()*window.innerWidth,y:Math.random()*window.innerHeight,r:60+Math.random()*160,vx:(Math.random()-.5)*.18,vy:(Math.random()-.5)*.18,hue:180+Math.random()*140,a:.12+.10*Math.random()}; });
    }
    function draw(){ ctx.clearRect(0,0,window.innerWidth,window.innerHeight); ctx.globalCompositeOperation='lighter';
      for(var i=0;i<blobs.length;i++){ var b=blobs[i]; b.x+=b.vx; b.y+=b.vy;
        if(b.x<-220)b.x=window.innerWidth+220; if(b.x>window.innerWidth+220)b.x=-220; if(b.y<-220)b.y=window.innerHeight+220; if(b.y>window.innerHeight+220)b.y=-220;
        var g=ctx.createRadialGradient(b.x,b.y,0,b.x,b.y,b.r); g.addColorStop(0,'hsla('+b.hue+',85%,65%,'+b.a+')'); g.addColorStop(1,'hsla('+((b.hue+60)%360)+',85%,55%,0)');
        ctx.fillStyle=g; ctx.beginPath(); ctx.arc(b.x,b.y,b.r,0,Math.PI*2); ctx.fill();
      } requestAnimationFrame(draw);
    }
    window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
  })();
} catch(e){ var s=document.getElementById('analyzeStatus'); if(s) s.textContent='JS (smoke) error: '+e.message; }
</script>

<!-- D) Final tiny error sink -->
<script>
window.addEventListener('error', function(e){
  var s=document.getElementById('analyzeStatus');
  if (s) s.textContent = 'JavaScript error: ' + (e && e.message ? e.message : e);
});
</script>

</body>
</html>
