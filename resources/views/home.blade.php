{{-- resources/views/home.blade.php — v2025-08-24za (adds: Readability, Entities, Animated Human-vs-AI, PSI Speed via proxy, theme & threshold) --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, UX, and speed, with water-fill scoring, auto-checklist, and AI/Human signals.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
  $analyzeJsonUrl = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl     = Route::has('analyze')      ? route('analyze')      : url('analyze');
  // PSI proxy endpoint (server will inject key from .env). If missing, UI will gracefully inform.
  $psiRunUrl      = Route::has('psi.run') ? route('psi.run') : url('/api/psi');
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
/* === Core Theme === */
:root{
  --bg:#07080e;--panel:#0f1022;--panel-2:#141433;--text:#f0effa;--text-dim:#b6b3d6;--good:#22c55e;--warn:#f59e0b;--bad:#ef4444;--accent:#3de2ff;
  --radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55);--container:1200px;--hue:0deg;
  --grad-a:linear-gradient(135deg,#3de2ff,#9b5cff);
  --grad-b:linear-gradient(135deg,#22c55e,#16a34a);
  --grad-c:linear-gradient(135deg,#f59e0b,#fb923c);
  --grad-d:linear-gradient(135deg,#ef4444,#b91c1c);
}
[data-theme="light"]{
  --bg:#f6f7fb;--panel:#ffffff;--panel-2:#f5f7ff;--text:#0b0d21;--text-dim:#3b3f66;
  --shadow:0 8px 32px rgba(12,18,38,.12);
}
*{box-sizing:border-box}html,body{height:100%}html{scroll-behavior:smooth}
body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,Segoe UI,Roboto;background:
  radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),
  radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),var(--bg);overflow-x:hidden}
#linesCanvas,#smokeCanvas{position:fixed;inset:0;pointer-events:none;z-index:0}
#linesCanvas{opacity:.55}
#smokeCanvas{opacity:.9;mix-blend-mode:screen}
.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
header.site{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:14px 0 22px;border-bottom:1px solid rgba(255,255,255,.08)}
.brand{display:flex;align-items:center;gap:.8rem;min-width:0}
.brand-badge{width:48px;height:48px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(157,92,255,.3),rgba(61,226,255,.3));border:1px solid rgba(255,255,255,.18);color:#fff;font-size:1.08rem;box-shadow:0 8px 22px rgba(0,0,0,.28)}
.hero-heading{font-weight:1000;letter-spacing:.4px;font-size:clamp(1.4rem,3.2vw,2rem)}
.hero-sub{color:var(--text-dim);font-size:.95rem}
.btn{display:inline-flex;align-items:center;gap:.55rem;cursor:pointer;padding:.6rem .95rem;border-radius:14px;border:1px solid rgba(255,255,255,.16);color:#fff;font-weight:900;letter-spacing:.2px;position:relative;overflow:hidden;box-shadow:var(--shadow);background:rgba(255,255,255,.06)}
.btn::after{content:"";position:absolute;inset:-2px;border-radius:inherit;opacity:.0;background:linear-gradient(120deg,transparent,rgba(255,255,255,.22),transparent 60%);transform:translateX(-120%);transition:opacity .2s}
.btn:hover::after{opacity:1;animation:btnSweep 2.6s linear infinite}
@keyframes btnSweep{0%{transform:translateX(-120%)}100%{transform:translateX(120%)}}
.btn-analyze{background:var(--grad-b);border-color:#20d391}
.btn-print{background:linear-gradient(135deg,#3b82f6,#6366f1);border-color:#5b77ef}
.btn-reset{background:linear-gradient(135deg,#f59e0b,#f97316);border-color:#f59e0b}
.btn-export{background:linear-gradient(135deg,#a855f7,#ec4899);border-color:#c26cf2}
.btn-ghost{background:rgba(255,255,255,.06)}
.btn:disabled{opacity:.6;cursor:not-allowed}
.toggle{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.16);border-radius:14px;padding:.4rem .6rem;display:inline-flex;gap:.5rem;align-items:center;color:#fff;font-weight:800}
.toggle input{accent-color:#9b5cff}
.range{appearance:none;width:180px;height:10px;border-radius:999px;background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e);outline:none}
.range::-webkit-slider-thumb{appearance:none;width:18px;height:18px;border-radius:50%;background:#fff;border:2px solid #9b5cff;box-shadow:0 4px 12px rgba(0,0,0,.35);cursor:pointer}
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem}.section-subtitle{margin:0;color:var(--text-dim)}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)}.l-orange{background:rgba(245,158,11,.18)}.l-green{background:rgba(34,197,94,.18)}
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-gauge{position:relative;width:100%;aspect-ratio:1/1}.gauge-svg{width:100%;height:auto;display:block}
.score-mask-rect{transition:all .6s cubic-bezier(.22,1,.36,1)}
.score-wave1{animation:scoreWave 8s linear infinite}.score-wave2{animation:scoreWave 11s linear infinite reverse}
@keyframes scoreWave{from{transform:translateX(0)}to{transform:translateX(-210px)}}
.score-text{font-size:clamp(2.2rem,4.2vw,3.1rem);font-weight:1000;fill:#fff;text-shadow:0 0 18px rgba(255,32,69,.25)}
.multiHueFast{filter:hue-rotate(var(--hue)) saturate(140%);will-change:filter}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.chip-good{background:rgba(34,197,94,.18)!important;border-color:rgba(34,197,94,.45)!important}
.chip-mid{background:rgba(245,158,11,.18)!important;border-color:rgba(245,158,11,.45)!important}
.chip-bad{background:rgba(239,68,68,.18)!important;border-color:rgba(239,68,68,.5)!important}
.ico{width:1.1em;text-align:center}.ico-green{color:var(--good)}.ico-orange{color:var(--warn)}.ico-red{color:var(--bad)}.ico-cyan{color:var(--accent)}.ico-purple{color:#9b5cff}
.url-field{position:relative;border-radius:16px;background:#0b0d21;border:1px solid #1b1b35;box-shadow:inset 0 0 0 1px rgba(255,255,255,.02),0 12px 32px rgba(0,0,0,.32);padding:10px 110px 10px 46px;transition:.25s;overflow:hidden;isolation:isolate}
.url-field:focus-within{border-color:#5942ff;box-shadow:0 0 0 6px rgba(155,92,255,.15),inset 0 0 0 1px rgba(93,65,255,.28)}
.url-field .url-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9aa0c3;font-size:1rem;opacity:.95}
.url-field input{all:unset;color:var(--text);width:100%;font-size:1rem;letter-spacing:.2px}
.url-field .url-mini{position:absolute;top:50%;transform:translateY(-50%);border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;border-radius:10px;padding:.35rem .6rem;font-weight:900;cursor:pointer;transition:.15s}
.url-field .url-mini:hover{background:rgba(255,255,255,.12)}.url-field .url-clear{right:60px;width:36px;height:32px;display:grid;place-items:center}.url-field #pasteUrl{right:12px}
.url-field .url-border{content:"";position:absolute;inset:-2px;border-radius:inherit;padding:2px;background:conic-gradient(from 0deg,#3de2ff,#9b5cff,#ff2045,#f59e0b,#3de2ff);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;opacity:.55;pointer-events:none;filter:hue-rotate(var(--hue))}
.analyze-row{display:grid;grid-template-columns:1fr auto auto auto auto auto;gap:.6rem;align-items:center;margin-top:.6rem}
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
.comp-water{position:relative;height:52px;border-radius:16px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.comp-svg{position:absolute;inset:0;width:100%;height:100%;z-index:1}.comp-overlay{position:absolute;inset:0;background:radial-gradient(120px 50px at 15% -25%,rgba(255,255,255,.16),transparent 55%),linear-gradient(180deg,rgba(255,255,255,.08),transparent 35%,rgba(255,255,255,.06));pointer-events:none;mix-blend-mode:screen;z-index:3}
.comp-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1rem;z-index:4;text-shadow:0 1px 0 rgba(0,0,0,.45)}
.comp-wave1{animation:waveX 8s linear infinite}.comp-wave2{animation:waveX 12s linear infinite reverse}

/* Category cards (unchanged base) */
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
.cat-water-pct{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:.8rem;color:rgba(255,255,255,.9);text-shadow:0 1px 0 rgba(0,0,0,.55);pointer-events:none}
.checklist{list-style:none;margin:10px 0 0;padding:0}
.checklist-item{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.7rem .75rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02)),radial-gradient(100% 120% at 0% 0%,rgba(61,226,255,.06),transparent 30%),radial-gradient(120% 100% at 100% 0%,rgba(155,92,255,.05),transparent 35%);transition:box-shadow .25s,background .25s,transform .12s}
.checklist-item+.checklist-item{margin-top:.28rem}.checklist-item:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.25)}
.sev-good{background:linear-gradient(180deg,rgba(34,197,94,.14),rgba(34,197,94,.08));border-color:rgba(34,197,94,.45)}
.sev-mid{background:linear-gradient(180deg,rgba(245,158,11,.16),rgba(245,158,11,.08));border-color:rgba(245,158,11,.45)}
.sev-bad{background:linear-gradient(180deg,rgba(239,68,68,.16),rgba(239,68,68,.10));border-color:rgba(239,68,68,.55)}
.checklist-item input[type="checkbox"]{appearance:none;-webkit-appearance:none;outline:none;width:22px;height:22px;border-radius:8px;background:#0b1220;border:2px solid #2a2f4d;position:relative;display:inline-grid;place-items:center;transition:.18s;box-shadow:inset 0 0 0 0 rgba(99,102,241,.0)}
.checklist-item input[type="checkbox"]:hover{border-color:#4c5399;box-shadow:0 0 0 4px rgba(99,102,241,.12)}
.checklist-item input[type="checkbox"]::after{content:"";width:7px;height:12px;border:3px solid transparent;border-left:0;border-top:0;transform:rotate(45deg) scale(.7);transition:.18s}
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

/* New Panels: Detection (new look), Readability, Entities, Speed */
.panel{margin-top:14px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px}
.panel-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.55rem}
.panel-head h4{margin:0;font-size:1.08rem}
.subtext{color:var(--text-dim);font-size:.92rem}
.kpi-row{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.kpi{grid-column:span 3;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:.7rem .8rem}
.kpi-title{font-weight:800;color:var(--text-dim);font-size:.86rem}
.kpi-value{font-weight:1000;font-size:1.25rem;margin-top:.2rem}

/* Animated Human vs AI dual bar */
.dualbar{position:relative;height:20px;border-radius:999px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.12);box-shadow:inset 0 0 0 1px rgba(255,255,255,.03)}
.dualbar-fill-human,.dualbar-fill-ai{position:absolute;top:0;bottom:0;left:0;width:50%}
.dualbar-fill-human{background:linear-gradient(90deg,#22c55e,#3de2ff);animation:slideHuman 6s linear infinite}
.dualbar-fill-ai{background:linear-gradient(90deg,#ef4444,#f59e0b);right:0;left:auto;animation:slideAI 6s linear infinite reverse}
@keyframes slideHuman{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(360deg)}}
@keyframes slideAI{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(-360deg)}}
.dualbar-caps{display:flex;justify-content:space-between;align-items:center;margin-top:.4rem;font-weight:900}
.dualcap{display:inline-flex;align-items:center;gap:.4rem}
.dualcap .badge{padding:.2rem .5rem;border-radius:999px;font-size:.82rem;border:1px solid rgba(255,255,255,.16)}
.badge-human{background:rgba(34,197,94,.18)}
.badge-ai{background:rgba(239,68,68,.18)}
.spark{display:inline-block;width:6px;height:6px;border-radius:999px;background:#fff;box-shadow:0 0 10px rgba(255,255,255,.65)}

/* Readability heat bar */
.heatbar{position:relative;height:10px;border-radius:999px;background:linear-gradient(90deg,#22c55e,#f59e0b,#ef4444);overflow:hidden}
.heatbar-thumb{position:absolute;top:-4px;width:18px;height:18px;border-radius:999px;background:#fff;border:2px solid #9b5cff;box-shadow:0 6px 14px rgba(0,0,0,.35)}

/* Entities layout */
.entity-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.entity-col{grid-column:span 6;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:.7rem .8rem}
.entity-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:.4rem}
.entity-chips{display:flex;flex-wrap:wrap;gap:.4rem}
.entity-chip{display:inline-flex;align-items:center;gap:.4rem;padding:.35rem .55rem;border:1px solid rgba(255,255,255,.16);border-radius:999px;background:rgba(255,255,255,.06);font-weight:800}
.entity-ring{width:8px;height:8px;border-radius:999px;background:#22c55e;box-shadow:0 0 10px rgba(34,197,94,.65)}
.entity-ring.mid{background:#f59e0b}
.entity-ring.low{background:#ef4444}
.entity-actions{margin-top:.5rem;display:flex;gap:.5rem;flex-wrap:wrap}

/* Speed panel */
.speed-wrap{display:grid;grid-template-columns:repeat(12,1fr);gap:.8rem}
.speed-col{grid-column:span 6;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:.75rem}
.score-tile{display:flex;align-items:center;gap:.6rem;background:#0b0d21;border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:.5rem .6rem}
.score-dot{width:12px;height:12px;border-radius:999px}
.score-dot.good{background:#22c55e}.score-dot.mid{background:#f59e0b}.score-dot.bad{background:#ef4444}
.audit-list{list-style:none;margin:.5rem 0 0;padding:0;display:grid;gap:.35rem}
.audit-item{display:flex;align-items:flex-start;gap:.5rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:10px;padding:.45rem .55rem}
.audit-item .how{color:var(--text-dim);font-size:.88rem}
.note{margin-top:.4rem;color:var(--text-dim);font-size:.9rem}

/* Responsive */
@media (max-width:992px){
  .category-card{grid-column:span 12}.score-container{width:190px}.analyze-row{grid-template-columns:1fr auto auto auto}
  .kpi{grid-column:span 4}.entity-col{grid-column:span 12}.speed-col{grid-column:span 12}
}
@media (max-width:768px){
  .wrap{padding:18px 4%}
  header.site{flex-direction:column;align-items:flex-start;gap:.6rem}
  .score-area{flex-direction:column;align-items:flex-start;gap:.8rem}
  .score-container{width:170px}
  .analyze-row{grid-template-columns:1fr}
  .analyze-row .btn,.toggle{width:100%;justify-content:center}
  .share-dock{top:auto;bottom:10px;right:50%;transform:translateX(50%);flex-direction:row;padding:.35rem .45rem;border-radius:999px;gap:.4rem;background:rgba(10,12,28,.55)}
  .checklist-item{grid-template-columns:1fr auto auto}
  .checklist-item .improve-btn{grid-column:1/-1;justify-self:flex-start;margin-top:.25rem}
}
@media (max-width:480px){.score-container{width:150px}.category-icon{width:40px;height:40px}.category-title{font-size:1rem}}
@media (prefers-reduced-motion: reduce){
  .score-wave1,.score-wave2,.wave1,.wave2,.cat-wave1,.cat-wave2,.comp-wave1,.comp-wave2,.dualbar-fill-human,.dualbar-fill-ai{animation:none!important}
  .multiHue,.multiHueFast{filter:none!important}
}
@media print{.share-dock,#backTop,#linesCanvas,#smokeCanvas{display:none!important}}
</style>
</head>
<body>

<!-- Background canvases -->
<canvas id="linesCanvas"></canvas>
<canvas id="smokeCanvas"></canvas>

<script>
  /* ===== Stable globals to kill race conditions ===== */
  window.SEMSEO = window.SEMSEO || {};
  window.SEMSEO.ENDPOINTS = {
    analyzeJson: @json($analyzeJsonUrl),
    analyze: @json($analyzeUrl),
    psi: @json($psiRunUrl)
  };
  window.SEMSEO.SMOKE_HUE_PERIOD_MS = 1000000000;
  window.SEMSEO.READY = false;
  window.SEMSEO.BUSY = false;
  window.SEMSEO.QUEUE = 0;
  window.SEMSEO.AUTO_THRESH = 80; // default, user-adjustable

  function SEMSEO_go(){
    if (window.SEMSEO.READY && typeof analyze === 'function') {
      analyze();
    } else {
      window.SEMSEO.QUEUE++;
      const s=document.getElementById('analyzeStatus');
      if(s) s.textContent='Initializing…';
    }
  }
</script>

<!-- Share dock -->
<div class="share-dock" aria-label="Share" style="position:fixed;right:16px;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;gap:.5rem;z-index:85;background:rgba(10,12,28,.35);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:.5rem;backdrop-filter:blur(8px)">
  <a id="shareFb" class="share-btn share-fb" target="_blank" rel="noopener nofollow" style="width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;color:#fff;cursor:pointer;text-decoration:none;background:linear-gradient(135deg,#1877F2,#1e90ff)"><i class="fa-brands fa-facebook-f"></i></a>
  <a id="shareX"  class="share-btn share-x"  target="_blank" rel="noopener nofollow" style="width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;color:#fff;cursor:pointer;text-decoration:none;background:linear-gradient(135deg,#111,#333)"><i class="fa-brands fa-x-twitter"></i></a>
  <a id="shareLn" class="share-btn share-ln" target="_blank" rel="noopener nofollow" style="width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;color:#fff;cursor:pointer;text-decoration:none;background:linear-gradient(135deg,#0a66c2,#1a8cd8)"><i class="fa-brands fa-linkedin-in"></i></a>
  <a id="shareWa" class="share-btn share-wa" target="_blank" rel="noopener nofollow" style="width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;color:#fff;cursor:pointer;text-decoration:none;background:linear-gradient(135deg,#25D366,#128C7E)"><i class="fa-brands fa-whatsapp"></i></a>
  <a id="shareEm" class="share-btn share-em" target="_blank" rel="noopener" style="width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;color:#fff;cursor:pointer;text-decoration:none;background:linear-gradient(135deg,#ef4444,#b91c1c)"><i class="fa-solid fa-envelope"></i></a>
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
    <div class="header-actions" style="display:flex;gap:.5rem;flex-wrap:wrap">
      <label class="toggle" title="Auto-check threshold">
        <i class="fa-solid fa-sliders"></i>
        <span>Auto-tick ≥ <b id="autoThreshVal">80</b></span>
        <input id="autoThreshold" type="range" class="range" min="60" max="95" value="80" />
      </label>
      <label class="toggle" title="Toggle theme">
        <i class="fa-solid fa-circle-half-stroke"></i>
        <span id="themeLabel">Dark</span>
        <input id="themeToggle" type="checkbox"/>
      </label>
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
            <circle id="ringArc" cx="100" cy="100" r="95" fill="none" stroke="url(#ringGrad)" stroke-width="6" stroke-linecap="round" filter="url(#ringGlow)" opacity=".95" transform="rotate(-90 100 100)"/>
            <g clip-path="url(#scoreCircleClip)">
              <rect x="0" y="0" width="200" height="200" fill="#0b0d21"/>
              <g clip-path="url(#scoreFillClip)">
                <g class="score-wave1 multiHueFast">
                  <use href="#scoreWavePath" x="0" fill="url(#scoreGrad)"/><use href="#scoreWavePath" x="210" fill="url(#scoreGrad)"/>
                </g>
                <g class="score-wave2 multiHueFast" opacity=".85">
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
          <span class="chip" id="aiBadge" title="Writer detection"><i class="fa-solid fa-user-check ico ico-green"></i> Writer: <b>—</b></span>
          <button id="viewHumanBtn" class="btn btn-ghost"><i class="fa-solid fa-user ico ico-green"></i> Human-like: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost"><i class="fa-solid fa-robot ico ico-red"></i> AI-like: <b id="aiPct">—</b>%</button>
          <button id="copyQuick" class="btn btn-ghost"><i class="fa-regular fa-copy ico ico-cyan"></i> Copy report</button>
        </div>
        <small style="color:var(--text-dim)">Local ensemble + heuristics ensure scores always render—even if backend returns nothing.</small>
      </div>
    </div>

    <div class="analyze-box panel">
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
          <div style="display:flex;align-items:center;gap:.6rem;flex-wrap:wrap">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer" title="Auto-apply checkmarks when score ≥ threshold">
              <input id="autoApply" type="checkbox" checked style="accent-color:#9b5cff">
              <span>Auto-apply checkmarks (≥ <b id="autoApplyVal">80</b>)</span>
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
          <button class="btn btn-ghost" id="runSpeed" type="button" title="Run PageSpeed Insights via server proxy"><i class="fa-solid fa-gauge"></i> Run Speed Test</button>
        </div>

        <!-- Progress water bar -->
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

        <!-- Meta report chips -->
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

    <!-- Ultra Content Detection (New Look) -->
    <section id="detectorPanel" class="panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-wave-square ico ico-purple"></i>
        <h4>Ultra Content Detection</h4>
      </div>

      <!-- Animated Human vs AI dual bar -->
      <div style="margin-bottom:.6rem">
        <div class="dualbar" aria-label="Human vs AI">
          <div class="dualbar-fill-human" id="barHuman" style="width:50%"></div>
          <div class="dualbar-fill-ai" id="barAI" style="width:50%"></div>
        </div>
        <div class="dualbar-caps">
          <div class="dualcap"><span class="spark"></span> <span class="badge badge-human"><i class="fa-solid fa-user"></i> Human</span> <b id="dualHuman">—%</b></div>
          <div class="dualcap"><b id="dualConf">Conf: —%</b></div>
          <div class="dualcap"><span class="badge badge-ai"><i class="fa-solid fa-robot"></i> AI</span> <b id="dualAI">—%</b></div>
        </div>
        <div class="subtext">Higher detector bar = more AI-like signal. Mix of stylometry and heuristics.</div>
      </div>

      <div class="det-grid" id="detGrid" style="display:grid;grid-template-columns:repeat(12,1fr);gap:.5rem"></div>
    </section>

    <!-- Readability Insights -->
    <section id="readabilityPanel" class="panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-book-open ico ico-cyan"></i>
        <h4>Readability Insights</h4>
      </div>
      <div class="kpi-row">
        <div class="kpi"><div class="kpi-title">Flesch Reading Ease</div><div class="kpi-value" id="kFlesch">—</div></div>
        <div class="kpi"><div class="kpi-title">Gunning Fog</div><div class="kpi-value" id="kFog">—</div></div>
        <div class="kpi"><div class="kpi-title">SMOG</div><div class="kpi-value" id="kSmog">—</div></div>
        <div class="kpi"><div class="kpi-title">Dale–Chall</div><div class="kpi-value" id="kDale">—</div></div>

        <div class="kpi"><div class="kpi-title">Reading Time</div><div class="kpi-value" id="kTime">—</div></div>
        <div class="kpi"><div class="kpi-title">Words</div><div class="kpi-value" id="kWords">—</div></div>
        <div class="kpi"><div class="kpi-title">Sentences</div><div class="kpi-value" id="kSents">—</div></div>
        <div class="kpi"><div class="kpi-title">Avg Sentence Len</div><div class="kpi-value" id="kAvgSent">—</div></div>
      </div>
      <div style="margin-top:.6rem">
        <div class="kpi-title">Long Sentence Heat</div>
        <div class="heatbar"><div id="heatThumb" class="heatbar-thumb" style="left:0%"></div></div>
        <div class="note">Tip: Keep most sentences under 28 words and vary length for rhythm.</div>
      </div>
    </section>

    <!-- Entity Extraction (Semantic SEO) -->
    <section id="entityPanel" class="panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-database ico ico-purple"></i>
        <h4>Entities & Topics</h4>
      </div>
      <div class="subtext">Detected entities grouped by type with confidence rings. Copy JSON-LD stubs for quick schema work.</div>
      <div class="entity-grid" style="margin-top:.5rem">
        <div class="entity-col">
          <div class="entity-head"><strong>People</strong><small class="subtext" id="countPeople">0</small></div>
          <div class="entity-chips" id="chipsPeople"></div>
        </div>
        <div class="entity-col">
          <div class="entity-head"><strong>Organizations</strong><small class="subtext" id="countOrgs">0</small></div>
          <div class="entity-chips" id="chipsOrgs"></div>
        </div>
        <div class="entity-col">
          <div class="entity-head"><strong>Places</strong><small class="subtext" id="countPlaces">0</small></div>
          <div class="entity-chips" id="chipsPlaces"></div>
        </div>
        <div class="entity-col">
          <div class="entity-head"><strong>Topics</strong><small class="subtext" id="countTopics">0</small></div>
          <div class="entity-chips" id="chipsTopics"></div>
        </div>
      </div>
      <div class="entity-actions">
        <button id="copyArticleJSONLD" class="btn btn-ghost"><i class="fa-solid fa-code"></i> Copy Article JSON-LD (about)</button>
        <button id="copyOrgJSONLD" class="btn btn-ghost"><i class="fa-solid fa-building"></i> Copy Organization JSON-LD</button>
      </div>
    </section>

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
              <g class="comp-wave1 multiHue"><use href="#compWave" x="0" fill="url(#compGrad)"/><use href="#compWave" x="600" fill="url(#compGrad)"/></g>
              <g class="comp-wave2 multiHue" opacity=".75"><use href="#compWave" x="0" y="6" fill="url(#compGrad)"/><use href="#compWave" x="600" y="6" fill="url(#compGrad)"/></g>
            </g>
          </g>
        </svg>
        <div class="comp-overlay"></div>
        <div class="comp-pct"><span id="compPct">0%</span></div>
      </div>
      <div id="progressCaption" class="progress-caption" style="color:var(--text-dim)">0 of 25 items completed</div>
    </div>

    <!-- Checklist Cards (unchanged structure) -->
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

    <!-- Speed (PageSpeed via proxy) -->
    <section id="speedPanel" class="panel" style="display:none">
      <div class="panel-head">
        <i class="fa-solid fa-gauge-high ico ico-green"></i>
        <h4>Site Speed & Core Web Vitals</h4>
      </div>
      <div class="speed-wrap">
        <div class="speed-col">
          <div class="score-tile"><span class="score-dot" id="mDot"></span><strong>Mobile Performance</strong> <span id="mScore" style="margin-left:auto;font-weight:1000">—</span></div>
          <ul class="audit-list" id="mAudits"></ul>
        </div>
        <div class="speed-col">
          <div class="score-tile"><span class="score-dot" id="dDot"></span><strong>Desktop Performance</strong> <span id="dScore" style="margin-left:auto;font-weight:1000">—</span></div>
          <ul class="audit-list" id="dAudits"></ul>
        </div>
      </div>
      <div class="note" id="speedNote">Uses server proxy /api/psi to keep your Google key hidden.</div>
    </section>

  </main>
</div>

<footer class="site" style="margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)">
  <div><strong>Semantic SEO Master</strong></div>
  <div class="footer-links">
    <a href="#analyzer">Analyzer</a>
    <a href="#" id="toTopLink">Back to top</a>
  </div>
</footer>

<button id="backTop" title="Back to top" aria-label="Back to top" style="position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none"><i class="fa-solid fa-arrow-up"></i></button>

<!-- A) Analyze + core logic -->
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

  /* === Auto-tick by item scores (with adjustable threshold) === */
  function autoTickByScores(map){
    var autoCount=0;
    var TH = window.SEMSEO && window.SEMSEO.AUTO_THRESH ? Number(window.SEMSEO.AUTO_THRESH) : 80;
    for(var i=1;i<=25;i++){
      var scVal=Number((map && map[i]!==undefined)? map[i] : NaN);
      var badge=document.getElementById('sc-'+i);
      var cb=document.getElementById('ck-'+i);
      var row=cb ? cb.closest('.checklist-item') : null;
      if (!badge) continue;
      if (!isNaN(scVal)) {
        badge.textContent = Math.round(scVal);
        badgeTone(badge, scVal);
        if (document.getElementById('autoApply') && document.getElementById('autoApply').checked && scVal>=TH) {
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

  /* === Water progress === */
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

  /* ===================== Fetch helpers ===================== */
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

  async function fetchRawHtml(url){
    try{
      const r=await fetch('https://api.allorigins.win/raw?url='+encodeURIComponent(url),{cache:'no-store'});
      if(r.ok){ const html=await r.text(); if(html && html.length>200) return html; }
    }catch(_){}
    return '';
  }

  async function fetchReadableText(url){
    try{
      const httpsR = await fetch('https://r.jina.ai/http/'+url.replace(/^https?:\/\//,''));
      if(httpsR.ok){ const t = await httpsR.text(); if(t && t.length>200) return t; }
    }catch(e){}
    try{
      const altR = await fetch('https://r.jina.ai/'+url);
      if(altR.ok){ const t = await altR.text(); if(t && t.length>200) return t; }
    }catch(e){}
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
      return { titleLen: title?title.length:null, metaLen: metaDesc?metaDesc.length:null, canonical, robots, viewport, headings:(h1+'/'+h2+'/'+h3), internalLinks:internal, schema: schema?'yes':'no', sampleText: sample, pageTitle:title };
    }catch(_){ return {}; }
  }

  function mergeMeta(into, add){
    if(!into) into={};
    var keys=['titleLen','metaLen','canonical','robots','viewport','headings','internalLinks','schema','sampleText','pageTitle'];
    keys.forEach(function(k){
      if((into[k]===undefined || into[k]===null || into[k]==='—' || into[k]==='' ) && add && add[k]!==undefined && add[k]!==null){
        into[k]=add[k];
      }
    });
    return into;
  }

  /* ===================== Readability & Detection ===================== */
  function clamp(v,min,max){ return v<min?min:(v>max?max:v); }
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
  function _stats(text){
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
    return { text, sentences:sents.length||1, words:tokens, flesch:_flesch(text), cov, longRatio, triRepeatRatio: triT?triR/triT:0, TTR, hapaxRatio: types?hapax/types:0, avgWordLen:avgLen, digitsPer100:digits, avgSentLen:mean||0 };
  }
  function detectUltra(text){
    var s=_stats(text||'');
    if (s.words < 40){ var aiQuick = clamp(70 - s.words*0.8, 20, 70); return { humanPct: 100-aiQuick, aiPct: aiQuick, confidence: 46, detectors: [] , _s:s }; }
    var ai=10; var covT=0.45; if(s.cov<covT) ai+=clamp((covT-s.cov)/covT,0,1)*25; var ttrT=0.45; if(s.TTR<ttrT) ai+=clamp((ttrT-s.TTR)/ttrT,0,1)*18;
    var conf = clamp(50 + Math.min(45, Math.log((s.words||1)+1)*7), 45, 95);
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

  /* === Readability metrics suite === */
  function computeReadability(text){
    var s = _stats(text||'');
    // approximate formulas (client-safe)
    var complexWords = (text.match(/\b[A-Za-z]{7,}\b/g)||[]).length; // proxy for complex words
    var fog = clamp(0.4 * ( (s.words/(s.sentences||1)) + 100*(complexWords/(s.words||1)) ), 2, 25); // bounded
    var smog = clamp(1.0430*Math.sqrt(complexWords*30/(s.sentences||1)) + 3.1291, 1, 20);
    var dale = clamp(0.1579*(100*(complexWords/(s.words||1))) + 0.0496*(s.words/(s.sentences||1)), 1, 20);
    var timeMin = Math.max(1, Math.round(s.words/200)); // 200 wpm
    return {
      flesch: Math.round(s.flesch),
      fog: Math.round(fog*10)/10,
      smog: Math.round(smog*10)/10,
      dale: Math.round(dale*10)/10,
      words: s.words, sentences: s.sentences, avgSent: Math.round((s.avgSentLen||0)*10)/10,
      longRatio: s.longRatio, time: timeMin
    };
  }
  function renderReadability(r){
    var pnl = document.getElementById('readabilityPanel'); if(!pnl) return;
    pnl.style.display='block';
    setText('kFlesch', isFinite(r.flesch)? r.flesch:'—');
    setText('kFog', r.fog || '—'); setText('kSmog', r.smog || '—'); setText('kDale', r.dale || '—');
    setText('kTime', r.time+' min'); setText('kWords', r.words); setText('kSents', r.sentences); setText('kAvgSent', r.avgSent);
    var thumb=document.getElementById('heatThumb'); if(thumb){ thumb.style.left = clamp(r.longRatio*100,0,100)+'%'; }
  }

  /* === Entity extraction (heuristic) === */
  function extractEntities(text, pageTitle){
    var clean=(text||'').replace(/\s+/g,' ');
    var tokens=(clean.match(/[A-Za-z\u00C0-\u024f][A-Za-z\u00C0-\u024f'\-]+/g)||[]);
    var freq=Object.create(null);
    tokens.forEach(function(t){ var k=t.toLowerCase(); if(k.length>2) freq[k]=(freq[k]||0)+1; });
    // Simple proper-noun capture (capitalized sequences)
    var caps=(clean.match(/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+){0,3})\b/g)||[]);
    var people=[], orgs=[], places=[];
    caps.forEach(function(c){
      if(/(Inc|Ltd|LLC|Company|Corporation|Corp)\.?$/i.test(c)) orgs.push(c);
      else if (/(City|Town|Province|Country|Valley|Lake|River|Park|Street|Road)$/i.test(c)) places.push(c);
      else if (/\s/.test(c)) people.push(c);
    });
    // Topics: top frequent nouns-ish (fallback)
    var stop = new Set(['the','and','for','with','from','that','this','you','your','are','was','were','have','has','had','not','but','our','their','they','them','his','her','its','can','will','would','could','should','how','what','when','where','why','which','who','whom','into','onto','about','over','under','after','before','more','most','less','least','very','than','then','also']);
    var top = Object.keys(freq).filter(k=>!stop.has(k)).sort((a,b)=>freq[b]-freq[a]).slice(0,24).map(function(k){
      return {name:k, conf: Math.min(1, Math.log(1+freq[k])/Math.log(10))};
    });

    // uniqueness & trimming
    function uniq(arr){ var seen=new Set(); return arr.filter(x=>{var t=x.trim(); if(!t || seen.has(t.toLowerCase())) return false; seen.add(t.toLowerCase()); return true;}); }
    people=uniq(people).slice(0,12).map(n=>({name:n,conf:.7}));
    orgs=uniq(orgs).slice(0,12).map(n=>({name:n,conf:.65}));
    places=uniq(places).slice(0,12).map(n=>({name:n,conf:.6}));
    var topics=top.map(t=>({name:t.name,conf:t.conf}));

    // include page title as topic
    if (pageTitle && pageTitle.length>0){ topics.unshift({name:pageTitle, conf:.9}); }

    return {people, orgs, places, topics};
  }
  function chipHTML(e){
    var ring='entity-ring'+(e.conf>=.75?'':' '+(e.conf>=.5?'mid':'low'));
    return '<span class="entity-chip"><span class="'+ring+'"></span>'+e.name+'</span>';
  }
  function renderEntities(ents){
    var pnl=document.getElementById('entityPanel'); if(!pnl) return;
    pnl.style.display='block';
    function fill(id,arr,countId){ var el=document.getElementById(id); if(!el) return; el.innerHTML = (arr||[]).map(chipHTML).join(''); var c=document.getElementById(countId); if(c) c.textContent = (arr||[]).length; }
    fill('chipsPeople', ents.people, 'countPeople');
    fill('chipsOrgs', ents.orgs, 'countOrgs');
    fill('chipsPlaces', ents.places, 'countPlaces');
    fill('chipsTopics', ents.topics, 'countTopics');

    function jsonLD_Article(){
      var about = (ents.people.concat(ents.orgs).concat(ents.places).concat(ents.topics)).slice(0,20).map(e=>({ "@type":"Thing", "name": e.name }));
      return JSON.stringify({ "@context":"https://schema.org", "@type":"Article", "about": about }, null, 2);
    }
    function jsonLD_Org(){
      var org = ents.orgs[0]?.name || "Your Organization";
      return JSON.stringify({
        "@context":"https://schema.org","@type":"Organization","name":org,
        "sameAs":[ "https://twitter.com/yourorg","https://www.linkedin.com/company/yourorg" ]
      }, null, 2);
    }
    function copy(txt){ navigator.clipboard && navigator.clipboard.writeText(txt); }

    var btnA=document.getElementById('copyArticleJSONLD'); if(btnA){ btnA.onclick=function(){ copy(jsonLD_Article()); btnA.textContent='Copied Article JSON-LD!'; setTimeout(()=>btnA.innerHTML='<i class="fa-solid fa-code"></i> Copy Article JSON-LD (about)',1400); }; }
    var btnO=document.getElementById('copyOrgJSONLD'); if(btnO){ btnO.onclick=function(){ copy(jsonLD_Org()); btnO.textContent='Copied Organization JSON-LD!'; setTimeout(()=>btnO.innerHTML='<i class="fa-solid fa-building"></i> Copy Organization JSON-LD',1400); }; }
  }

  /* === Build sample from backend === */
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
    var s = (ensemble && ensemble._s) ? ensemble._s : _stats(sample||'');
    if (needItems) data.itemScores = deriveItemScoresFromSignals(s);
    if (needContent || needOverall){
      var sums = deriveSummaryScoresFromItems(data.itemScores||{});
      if (needContent) data.contentScore = sums.contentScore;
      if (needOverall) data.overall = sums.overall;
    }
    return data;
  }

  function renderDetectors(res){
    var grid = document.getElementById('detGrid'); var panel=document.getElementById('detectorPanel');
    if(!grid || !panel) return; panel.style.display='block'; grid.innerHTML='';
    (res.detectors||[{key:'stylometry',label:'Stylometry',ai:res.aiPct||0}]).forEach(function(d){
      var id='det-'+d.key; var wrap=document.createElement('div');
      wrap.className='det-item'; wrap.style.cssText='grid-column:span 6;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.55rem .6rem';
      wrap.innerHTML =
        '<div style="display:grid;grid-template-columns:1fr auto;gap:.5rem;align-items:center">'+
          '<div class="det-label" style="font-weight:800;color:var(--text-dim)">'+d.label+'</div>'+
          '<div class="det-score" id="'+id+'-score" style="font-weight:1000">'+(d.ai||0)+'</div>'+
        '</div>'+
        '<div class="det-bar" style="margin-top:.4rem;position:relative;height:14px;border-radius:10px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)">'+
          '<div class="det-fill" id="'+id+'-fill" style="position:absolute;left:0;top:0;bottom:0;width:'+(clamp(d.ai||0,0,100))+'%;background:linear-gradient(90deg,#ef4444,#f59e0b)"></div>'+
        '</div>';
      grid.appendChild(wrap);
    });
  }

  function applyDetection(humanPct, aiPct, confidence, breakdown){
    // Status chip
    var writer = (isFinite(humanPct) && isFinite(aiPct) && humanPct>=aiPct) ? 'Likely Human' : 'AI-like';
    var badge = document.getElementById('aiBadge'); if (badge){ var b=badge.querySelector('b'); if(b) b.textContent = writer; badge.title = 'Confidence: ' + (confidence? confidence+'%':'—'); }
    var hp = document.getElementById('humanPct'), ap = document.getElementById('aiPct');
    if(hp) hp.textContent = isFinite(humanPct)? Math.round(humanPct) : '—';
    if(ap) ap.textContent = isFinite(aiPct)?    Math.round(aiPct)   : '—';

    // Animated dual bar
    var hbar=document.getElementById('barHuman'), abar=document.getElementById('barAI');
    if(hbar && abar){
      var h = clamp(Math.round(humanPct||0),0,100), a = clamp(100-h,0,100);
      hbar.style.width = h+'%';
      abar.style.width = a+'%';
      setText('dualHuman', h+'%'); setText('dualAI', (100-h)+'%'); setText('dualConf', 'Conf: '+(isFinite(confidence)? Math.round(confidence):'—')+'%');
    }
    if (breakdown && breakdown.detectors){ renderDetectors(breakdown); } else { document.getElementById('detectorPanel').style.display='block'; }
  }

  /* ===================== SPEED (PSI via server proxy) ===================== */
  async function fetchPSI(url, strategy){
    // requires server proxy at window.SEMSEO.ENDPOINTS.psi
    if(!window.SEMSEO.ENDPOINTS.psi){ throw new Error('PSI endpoint missing'); }
    const u = new URL(window.SEMSEO.ENDPOINTS.psi, location.origin);
    u.searchParams.set('url', url);
    u.searchParams.set('strategy', strategy);
    const r = await fetch(u.toString(), { headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'} });
    if(!r.ok) throw new Error('PSI '+strategy+' failed');
    return r.json();
  }
  function scoreTone(el,score){
    el.classList.remove('good','mid','bad');
    el.classList.add(score>=90?'good':(score>=50?'mid':'bad'));
  }
  function bucketAudits(audits){
    // map some common PSI audits to buckets with tips
    const tips={
      'render-blocking-resources':'Eliminate render-blocking resources (use <link rel="preload"> for critical CSS, defer non-critical JS).',
      'uses-responsive-images':'Serve responsive images (srcset/sizes) and correct dimensions.',
      'uses-optimized-images':'Compress & modern formats (AVIF/WebP).',
      'offscreen-images':'Lazy-load offscreen images.',
      'uses-text-compression':'Enable Brotli/Gzip on HTML/CSS/JS.',
      'unminified-css':'Minify CSS; purge unused rules.',
      'unminified-javascript':'Minify and split JS; defer where possible.',
      'total-blocking-time':'Reduce JS main-thread work; code split and use async.',
      'dom-size':'Trim DOM nodes; simplify templates.',
      'server-response-time':'Improve TTFB (caching, faster hosting, DB optimizations).',
      'third-party-summary':'Limit third-party scripts; load async/defer.',
      'layout-shift-elements':'Reserve space for images/ads; avoid late-loading UI shifts.'
    };
    const order=['render-blocking-resources','uses-responsive-images','uses-optimized-images','offscreen-images','uses-text-compression','unminified-css','unminified-javascript','third-party-summary','server-response-time','total-blocking-time','layout-shift-elements','dom-size'];
    return order.map(id=>({id, title:(audits[id]?.title||id), score:(audits[id]?.score), displayValue:(audits[id]?.displayValue), how: tips[id]}))
                .filter(x=>audits[x.id] && (audits[x.id].score<1));
  }
  function renderPSI(data,label){
    var panel=document.getElementById('speedPanel'); if(!panel) return; panel.style.display='block';
    const lr = data?.lighthouseResult; if(!lr) return;
    const perf = Math.round((lr.categories?.performance?.score||0)*100);
    const audits = lr.audits||{};
    // choose col
    const scoreId = (label==='Mobile')?'mScore':'dScore';
    const dotId   = (label==='Mobile')?'mDot':'dDot';
    const listId  = (label==='Mobile')?'mAudits':'dAudits';
    setText(scoreId, isFinite(perf)? perf : '—');
    scoreTone(document.getElementById(dotId), perf);
    // list issues
    const items = bucketAudits(audits);
    const ul=document.getElementById(listId); if(ul){ ul.innerHTML = items.slice(0,9).map(it=>{
      const val = audits[it.id]?.displayValue ? ' — '+audits[it.id].displayValue : '';
      return `<li class="audit-item"><i class="fa-solid fa-wrench"></i><div><strong>${it.title}</strong><div class="how">${it.how}${val? `<em>${val}</em>`:''}</div></div></li>`;
    }).join(''); }
  }
  async function runPageSpeed(url){
    var panel=document.getElementById('speedPanel'); if(panel) panel.style.display='block';
    var note=document.getElementById('speedNote');
    try{
      const [m,d] = await Promise.allSettled([fetchPSI(url,'mobile'), fetchPSI(url,'desktop')]);
      if(m.status==='fulfilled') renderPSI(m.value,'Mobile'); else if(note){ note.textContent='Mobile PSI failed (check proxy/API key).'; }
      if(d.status==='fulfilled') renderPSI(d.value,'Desktop'); else if(note){ note.textContent = (note.textContent? note.textContent+' ' : '') + 'Desktop PSI failed.'; }
      if(note && !note.textContent) note.textContent='Parsed via server proxy.';
    }catch(e){
      if(note) note.textContent='Speed test unavailable (missing PSI proxy).';
    }
  }
  window.runPageSpeed = runPageSpeed;

  /* ===================== Main analyze() ===================== */
  async function analyze(){
    if (window.SEMSEO.BUSY) return;
    window.SEMSEO.BUSY = true;

    var input = document.getElementById('analyzeUrl');
    var url = normalizeUrl(input ? input.value : '');
    if (!url) { if(input) input.focus(); window.SEMSEO.BUSY=false; return; }

    if (window.Water) window.Water.start();
    var statusEl = document.getElementById('analyzeStatus');
    if (statusEl) statusEl.textContent = 'Fetching & analyzing…';
    var report = document.getElementById('analyzeReport'); if (report) report.style.display = 'none';
    var detPanel = document.getElementById('detectorPanel'); if(detPanel) detPanel.style.display='none';
    var readPanel = document.getElementById('readabilityPanel'); if(readPanel) readPanel.style.display='none';
    var entPanel = document.getElementById('entityPanel'); if(entPanel) entPanel.style.display='none';

    // 1) Backend if present
    var {ok,data} = await fetchBackend(url);
    if(!data) data = {};

    // 2) Build sample
    var sample = buildSampleFromData(data);

    // 3) Raw HTML meta & better sample
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

    // Meta chips
    setText('rStatus',    data.httpStatus ? data.httpStatus : '200?');
    setText('rTitleLen',  (data.titleLen   !== undefined && data.titleLen !== null) ? data.titleLen   : '—');
    setText('rMetaLen',   (data.metaLen    !== undefined && data.metaLen  !== null) ? data.metaLen    : '—');
    setText('rCanonical', data.canonical  ? data.canonical  : '—');
    setText('rRobots',    data.robots     ? data.robots     : '—');
    setText('rViewport',  data.viewport   ? data.viewport   : '—');
    setText('rHeadings',  data.headings   ? data.headings   : '—');
    setText('rInternal',  (data.internalLinks!==undefined && data.internalLinks!==null) ? data.internalLinks : '—');
    setText('rSchema',    data.schema     ? data.schema     : '—');

    // Detection display (new look)
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

    // Checklist scores + autotick
    window.autoTickByScores(data.itemScores || {});

    // Readability + Entities
    if (sample && sample.length>30){
      renderReadability(computeReadability(sample));
      renderEntities(extractEntities(sample, data.pageTitle||''));
    }

    if (window.Water) window.Water.finish();
    if (statusEl) statusEl.textContent = 'Analysis complete';
    if (report) report.style.display = 'block';

    window.SEMSEO.BUSY = false;
    if (window.SEMSEO.QUEUE > 0){ window.SEMSEO.QUEUE = 0; }
  }
  window.analyze = analyze;

  /* ===================== Events & UI ===================== */
  document.addEventListener('DOMContentLoaded', function(){
    try{
      // Buttons
      var btn = document.getElementById('analyzeBtn');
      if (btn){ btn.addEventListener('click', function(e){ e.preventDefault(); analyze(); }); }
      var input = document.getElementById('analyzeUrl');
      if (input){ input.addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); analyze(); }}); }
      var clr = document.getElementById('clearUrl'); if(clr && input){ clr.onclick=function(){ input.value=''; input.focus(); }; }
      var pst = document.getElementById('pasteUrl'); if(pst && input && navigator.clipboard){ pst.onclick=async function(){ try{ var t=await navigator.clipboard.readText(); if(t){ input.value=t.trim(); } }catch(e){} }; }

      // Run Speed
      var runSpeed=document.getElementById('runSpeed');
      if(runSpeed){ runSpeed.addEventListener('click', function(){
        var u = normalizeUrl(document.getElementById('analyzeUrl')?.value||'');
        if(!u){ document.getElementById('analyzeUrl').focus(); return; }
        runPageSpeed(u);
      }); }

      // Threshold slider
      var th = document.getElementById('autoThreshold'), thV=document.getElementById('autoThreshVal'), apV=document.getElementById('autoApplyVal');
      if(th){ th.addEventListener('input', function(){ window.SEMSEO.AUTO_THRESH=Number(th.value); if(thV) thV.textContent=th.value; if(apV) apV.textContent=th.value; }); }

      // Theme toggle
      var themeT=document.getElementById('themeToggle'), themeL=document.getElementById('themeLabel');
      var savedTheme = localStorage.getItem('semseo-theme')||'dark';
      if(savedTheme==='light'){ document.documentElement.setAttribute('data-theme','light'); if(themeT) themeT.checked=true; if(themeL) themeL.textContent='Light'; }
      if(themeT){ themeT.addEventListener('change', function(){
        if(themeT.checked){ document.documentElement.setAttribute('data-theme','light'); themeL.textContent='Light'; localStorage.setItem('semseo-theme','light'); }
        else { document.documentElement.setAttribute('data-theme',''); themeL.textContent='Dark'; localStorage.setItem('semseo-theme','dark'); }
      }); }

      // share links
      (function(){
        var url = encodeURIComponent(location.href), title = encodeURIComponent(document.title);
        var fb = document.getElementById('shareFb'), x = document.getElementById('shareX'), ln = document.getElementById('shareLn'), wa = document.getElementById('shareWa'), em = document.getElementById('shareEm');
        if(fb) fb.href = 'https://www.facebook.com/sharer/sharer.php?u='+url;
        if(x)  x.href  = 'https://twitter.com/intent/tweet?text='+title+'&url='+url;
        if(ln) ln.href = 'https://www.linkedin.com/sharing/share-offsite/?url='+url;
        if(wa) wa.href = 'https://wa.me/?text='+title+'%20'+url;
        if(em) em.href = 'mailto:?subject='+title+'&body='+url;
      })();

      // reset/export/import/print/back-to-top (as before)
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
          var detPanel=document.getElementById('detectorPanel'); if(detPanel){ detPanel.style.display='none'; }
          var readPanel=document.getElementById('readabilityPanel'); if(readPanel){ readPanel.style.display='none'; }
          var entPanel=document.getElementById('entityPanel'); if(entPanel){ entPanel.style.display='none'; }
          var speedPanel=document.getElementById('speedPanel'); if(speedPanel){ speedPanel.style.display='none'; }
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

      // mark ready and flush queued early clicks
      window.SEMSEO.READY = true;
      if (window.SEMSEO.QUEUE>0){ window.SEMSEO.QUEUE=0; analyze(); }
    }catch(err){
      var s=document.getElementById('analyzeStatus'); if(s) s.textContent='Boot error: '+err.message;
    }
  });

})();
</script>

<!-- C) Background: tech lines + smoke -->
<script>
try{
  // Tech diagonal lines
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

  // Colorful smoke
  (function(){
    var c=document.getElementById('smokeCanvas'); if(!c) return; var ctx=c.getContext('2d');
    var dpr=Math.min(2,window.devicePixelRatio||1), blobs=[], last=performance.now();
    var PERIOD = window.SEMSEO && window.SEMSEO.SMOKE_HUE_PERIOD_MS ? window.SEMSEO.SMOKE_HUE_PERIOD_MS : 1000000000;
    function resize(){
      c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0);
      var W=window.innerWidth, H=window.innerHeight;
      var N = 76;
      blobs=new Array(N).fill(0).map(function(_,i){
        var px = W*0.65 + Math.random()*W*0.45;
        var py = H*0.65 + Math.random()*H*0.45;
        var r  = 120 + Math.random()*260;
        var speed = 0.18 + Math.random()*0.22;
        return {
          x:px, y:py, r:r,
          vx: -speed*(0.6+Math.random()*0.8),
          vy: -speed*(0.6+Math.random()*0.8),
          baseHue: (i*37)%360,
          alpha: .26 + .20*Math.random()
        };
      });
      last=performance.now();
    }
    function draw(now){
      var W=window.innerWidth, H=window.innerHeight;
      ctx.clearRect(0,0,W,H);
      ctx.globalCompositeOperation='screen';
      var dt = now - last; last = now;
      for(var i=0;i<blobs.length;i++){
        var b=blobs[i];
        b.x += b.vx * dt; b.y += b.vy * dt;
        if(b.x < -360 || b.y < -360){ b.x = W + Math.random()*260; b.y = H + Math.random()*260; }
        var hue = (b.baseHue + (now % PERIOD) * (360/PERIOD)) % 360;
        var g=ctx.createRadialGradient(b.x,b.y,0,b.x,b.y,b.r);
        g.addColorStop(0,'hsla('+hue+',88%,68%,'+b.alpha+')');
        g.addColorStop(1,'hsla('+((hue+70)%360)+',88%,50%,0)');
        ctx.fillStyle=g; ctx.beginPath(); ctx.arc(b.x,b.y,b.r,0,Math.PI*2); ctx.fill();
      }
      requestAnimationFrame(draw);
    }
    window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
  })();
} catch(e){ var s=document.getElementById('analyzeStatus'); if(s) s.textContent='JS (smoke) error: '+e.message; }
</script>

<!-- D) Error sink -->
<script>
window.addEventListener('error', function(e){
  var s=document.getElementById('analyzeStatus');
  if (s) s.textContent = 'JavaScript error: ' + (e && e.message ? e.message : e);
});
</script>

</body>
</html>
