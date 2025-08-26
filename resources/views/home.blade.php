{{-- resources/views/home.blade.php — v2025-08-25 (Human-vs-AI first; upgraded Readability; Entities & Topics; PSI auto-start; colorful, responsive) --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  $metaTitle       = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, UX, speed, and Core Web Vitals with colorful, clear insights.';
  $metaImage       = asset('og-image.png');
  $canonical       = url()->current();

  // Use fully-qualified facade in Blade to avoid "use ... inside function" fatal error
  $analyzeJsonUrl  = \Illuminate\Support\Facades\Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl      = \Illuminate\Support\Facades\Route::has('analyze')      ? route('analyze')      : url('analyze');
  $psiProxyUrl     = \Illuminate\Support\Facades\Route::has('psi.proxy')    ? route('psi.proxy')    : url('api/psi'); // server proxy keeps API key hidden
  // NEW: backend detector endpoint or fallback
  $detectUrl       = \Illuminate\Support\Facades\Route::has('detect')       ? route('detect')       : url('api/detect');
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
:root{
  --bg:#07080e;--panel:#0f1022;--panel-2:#141433;--text:#f0effa;--text-dim:#b6b3d6;
  --good:#22c55e;--warn:#f59e0b;--bad:#ef4444;--accent:#3de2ff;--accent2:#9b5cff;
  --radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55);--container:1200px;--hue:0deg
}
*{box-sizing:border-box}html,body{height:100%}html{scroll-behavior:smooth}
body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,Segoe UI,Roboto;background:
  radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),
  radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),var(--bg);overflow-x:hidden}
#linesCanvas,#smokeCanvas{position:fixed;inset:0;pointer-events:none;z-index:0}
#linesCanvas{opacity:.55}#smokeCanvas{opacity:.9;mix-blend-mode:screen}
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
.btn-analyze{ position:relative; z-index:99; background:linear-gradient(135deg,#10b981,#22c55e);border-color:#20d391}
.btn-print{background:linear-gradient(135deg,#3b82f6,#6366f1);border-color:#5b77ef}
.btn-reset{background:linear-gradient(135deg,#f59e0b,#f97316);border-color:#f59e0b}
.btn-export{background:linear-gradient(135deg,#a855f7,#ec4899);border-color:#c26cf2}
.btn-ghost{background:rgba(255,255,255,.06)} .btn:disabled{opacity:.6;cursor:not-allowed}
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem}.section-subtitle{margin:0;color:var(--text-dim)}
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-gauge{position:relative;width:100%;aspect-ratio:1/1}.gauge-svg{width:100%;height:auto;display:block}
.score-mask-rect{transition:all .6s cubic-bezier(.22,1,.36,1)}
.score-wave1{animation:scoreWave 8s linear infinite}.score-wave2{animation:scoreWave 11s linear infinite reverse}
@keyframes scoreWave{from{transform:translateX(0)}to{transform:translateX(-210px)}}
.score-text{font-size:clamp(2.2rem,4.2vw,3.1rem);font-weight:1000;fill:#fff;text-shadow:0 0 18px rgba(255,32,69,.25)}
.multiHueFast{filter:hue-rotate(var(--hue)) saturate(140%);will-change:filter}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)}.l-orange{background:rgba(245,158,11,.18)}.l-green{background:rgba(34,197,94,.18)}
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
.analyze-row{display:grid;grid-template-columns:1fr auto auto auto auto;gap:.6rem;align-items:center;margin-top:.6rem}
.water-wrap{margin-top:.8rem;display:none}
.waterbar{position:relative;height:64px;border-radius:18px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.water-svg{position:absolute;inset:0;width:100%;height:100%;z-index:1}
.water-mask-rect{transition:all .25s ease-out}
.water-overlay{ pointer-events:none; /* global non-blocking overlays */position:absolute;inset:0;pointer-events:none;background:radial-gradient(120px 60px at 20% -20%,rgba(255,255,255,.18),transparent 60%),linear-gradient(0deg,rgba(255,255,255,.05),transparent 40%,transparent 60%,rgba(255,255,255,.06));mix-blend-mode:screen;z-index:2}
.water-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1.05rem;text-shadow:0 1px 0 rgba(0,0,0,.45);letter-spacing:.4px;z-index:4}
.wave1{animation:waveX 7s linear infinite}.wave2{animation:waveX 10s linear infinite reverse;opacity:.7}
@keyframes waveX{0%{transform:translateX(0)}100%{transform:translateX(-600px)}}
.multiHue{filter:hue-rotate(var(--hue)) saturate(140%);will-change:filter}
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.comp-water{position:relative;height:52px;border-radius:16px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.comp-svg{position:absolute;inset:0;width:100%;height:100%;z-index:1}.comp-overlay{position:absolute;inset:0;background:radial-gradient(120px 50px at 15% -25%,rgba(255,255,255,.16),transparent 55%),linear-gradient(180deg,rgba(255,255,255,.08),transparent 35%,rgba(255,255,255,.06));pointer-events:none;mix-blend-mode:screen;z-index:3}
.comp-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1rem;z-index:4;text-shadow:0 1px 0 rgba(0,0,0,.45)}
.comp-wave1{animation:waveX 8s linear infinite}.comp-wave2{animation:waveX 12s linear infinite reverse}
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
.checklist-item{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.7rem .75rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);background:linear-gradient(180deg,rgba(255,255,255,.04),rgba(255,255,255,.02)),radial-gradient(100% 120% at 0% 0%,rgba(61,226,255,.06),transparent 30%),radial-gradient(120% 100% at 100% 0%,rgba(155,92,255,.05),transparent 35%);transition:box-shadow .25s,background .25s,transform .12s}
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
@keyframes tickHue{0%{background-position:0% 50%}100%{background-position:200% 50%}}
.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22);border-color:rgba(22,193,114,.45)}
.score-mid{background:rgba(245,158,11,.22);border-color:rgba(245,158,11,.45)}
.score-bad{background:rgba(239,68,68,.24);border-color:rgba(239,68,68,.5)}
.improve-btn{position:relative;overflow:hidden;padding:.45rem .8rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:linear-gradient(135deg,rgba(255,255,255,.06),rgba(255,255,255,.02));font-weight:900;cursor:pointer;transition:.2s;isolation:isolate;min-width:88px}
.improve-btn:hover{transform:translateY(-1px);background:rgba(255,255,255,.1)}
.improve-btn::before{content:"";position:absolute;inset:-2px;border-radius:inherit;z-index:0;background:linear-gradient(120deg,transparent 0%,rgba(255,255,255,.18) 45%,transparent 50%,transparent 100%);transform:translateX(-120%);animation:btnSheen 3.2s linear infinite}
@keyframes btnSheen{0%{transform:translateX(-120%)}60%{transform:translateX(120%)}100%{transform:translateX(120%)}}
.share-dock{position:fixed;right:16px;top:50%;transform:translateY(-50%);display:flex;flex-direction:column;gap:.5rem;z-index:85;background:rgba(10,12,28,.35);border:1px solid rgba(255,255,255,.12);border-radius:14px;padding:.5rem;backdrop-filter:blur(8px)}
.share-btn{width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);display:grid;place-items:center;color:#fff;cursor:pointer;text-decoration:none;position:relative;overflow:hidden;transition:transform .15s,box-shadow .15s}
.share-btn:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(0,0,0,.35)}
.share-fb{background:linear-gradient(135deg,#1877F2,#1e90ff)}.share-x{background:linear-gradient(135deg,#111,#333)}.share-ln{background:linear-gradient(135deg,#0a66c2,#1a8cd8)}.share-wa{background:linear-gradient(135deg,#25D366,#128C7E)}.share-em{background:linear-gradient(135deg,#ef4444,#b91c1c)}
footer.site{margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)}
#backTop{position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none}
#backTop:hover{background:rgba(255,255,255,.12)}
@media (max-width:992px){.category-card{grid-column:span 12}.score-container{width:190px}.analyze-row{grid-template-columns:1fr auto auto}.det-item{grid-column:span 12}}
@media (max-width:768px){.wrap{padding:18px 4%}header.site{flex-direction:column;align-items:flex-start;gap:.6rem}.score-area{flex-direction:column;align-items:flex-start;gap:.8rem}.score-container{width:170px}.analyze-row{grid-template-columns:1fr}.analyze-row .btn{width:100%;justify-content:center}.share-dock{top:auto;bottom:10px;right:50%;transform:translateX(50%);flex-direction:row;padding:.35rem .45rem;border-radius:999px;gap:.4rem;background:rgba(10,12,28,.55)}.share-btn{width:44px;height:44px;border-radius:999px}.checklist-item{grid-template-columns:1fr auto auto}.checklist-item .improve-btn{grid-column:1/-1;justify-self:flex-start;margin-top:.25rem}}
@media (max-width:480px){.score-container{width:150px}.category-icon{width:40px;height:40px}.category-title{font-size:1rem}}
@media (prefers-reduced-motion: reduce){.score-wave1,.score-wave2,.wave1,.wave2,.cat-wave1,.cat-wave2,.comp-wave1,.comp-wave2{animation:none!important}.multiHue,.multiHueFast{filter:none!important}}
@media print{.share-dock,#backTop,#linesCanvas,#smokeCanvas{display:none!important}}

/* ==== Human vs AI (Ensemble) — upgraded ==== */
.hvai{margin-top:14px;background:linear-gradient(135deg,rgba(60,220,255,.06),rgba(155,92,255,.06));border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:14px}
.hvai-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem}
.hvai-head h4{margin:0;font-size:1.08rem}
.hvai-meta{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.6rem}
.hvai-chip{display:inline-flex;align-items:center;gap:.45rem;padding:.35rem .7rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);font-weight:900;background:rgba(255,255,255,.05)}

.hvai-bar{position:relative;display:grid;grid-template-columns:1fr 1fr;gap:.6rem;margin-bottom:.6rem}
.hvai-track{position:relative;height:18px;border-radius:999px;background:#0b0d21;border:1px solid rgba(255,255,255,.12);overflow:hidden}
.hvai-fill{position:absolute;top:0;bottom:0;width:0;transition:width .6s cubic-bezier(.22,1,.36,1)}
.hvai-fill.human{left:0;background:linear-gradient(90deg,#22c55e,#3de2ff)}
.hvai-fill.ai{right:0;background:linear-gradient(270deg,#ef4444,#f59e0b)}
.hvai-label{display:flex;justify-content:space-between;font-size:.9rem;color:var(--text-dim);font-weight:900}

.det-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.5rem}
.det-item{grid-column:span 6;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.55rem .6rem}
.det-row{display:grid;grid-template-columns:1fr auto;gap:.5rem;align-items:center}
.det-label{font-weight:800;color:var(--text-dim)}
.det-score{font-weight:1000}
.det-bar{margin-top:.35rem;position:relative;height:14px;border-radius:10px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.det-fill{position:absolute;left:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e);transition:width .35s ease}


/* --- FIX: Prevent HVAI banner/toast or gauge from overlapping content (2025-08-25) --- */
.hvai{ 
  display:grid; 
  grid-template-areas:
    "head"
    "meta"
    "banner"
    "bars"
    "grid"
    "note";
}
.hvai-head{ grid-area: head; }
.hvai-meta{ grid-area: meta; }
.hvai-banner{ grid-area: banner; }
.hvai-bar{ grid-area: bars; }
.det-grid{ grid-area: grid; }
#detNote{ grid-area: note; }

.hvai-banner{
  display:none;
  margin:.4rem 0 .6rem;
  padding:.6rem .8rem;
  border-radius:12px;
  border:1px solid rgba(255,255,255,.12);
  background:linear-gradient(135deg, rgba(61,226,255,.10), rgba(155,92,255,.10));
  font-weight:900;
}
.hvai-banner.show{ display:block; }

/* If external detector injects .toast/.banner as absolute overlay, normalize it into flow */
.hvai > .toast, .hvai > .banner, .hvai [data-toast], .hvai [data-banner]{
  position: static !important;
  inset: auto !important;
  display:block !important;
  margin:.4rem 0 .6rem !important;
  z-index:auto !important;
}

/* If a donut/ring gauge exists on the right, ensure it wraps instead of overlapping */
.hvai .gauge, .hvai .ring, .hvai .donut, .hvai svg[viewBox*="200 200"]{
  max-width:320px;
  width:100%;
  margin-left:auto;
}
@media (max-width:900px){
  .hvai .gauge, .hvai .ring, .hvai .donut, .hvai svg[viewBox*="200 200"]{
    margin:0 auto;
  }
}
/* --- /FIX --- */

/* === HVAI Neo Theme (with Multilingual) ================================= */
:root{
  --hvai-bg: radial-gradient(1200px 600px at 85% -10%, rgba(155,92,255,.25), transparent 60%),
             radial-gradient(1000px 480px at 10% -20%, rgba(2,204,255,.20), transparent 60%);
  --hvai-card: rgba(17, 20, 31, .66);
  --hvai-border: rgba(255,255,255,.08);
  --hvai-head: #e2ccff;
  --hvai-sub: #96f0ff;
  --hvai-human: #22c55e;  /* green */
  --hvai-ai: #ff7a59;     /* orange */
  --hvai-neutral: #a7b0c0;
  --hvai-chip: rgba(255,255,255,.06);
}

.hvai{
  border:1px solid var(--hvai-border);
  background: var(--hvai-bg);
  border-radius: 18px;
  padding: 14px;
  backdrop-filter: blur(6px);
  box-shadow: 0 6px 24px rgba(0,0,0,.25) inset, 0 8px 24px rgba(0,0,0,.25);
}
.hvai-head{
  display:flex; align-items:center; gap:.6rem;
  color:var(--hvai-head); font-weight:900; letter-spacing:.2px;
  font-size: clamp(1.05rem, 1vw + .9rem, 1.35rem);
}
.hvai-head .icon{
  width:22px; height:22px; opacity:.95; flex:0 0 auto;
  filter: drop-shadow(0 0 10px rgba(226,204,255,.25));
}
.hvai-sub{ color:var(--hvai-sub); font-size:.85rem; opacity:.9; margin:.2rem 0 .3rem; display:inline-block; }

.hvai-meta{ display:flex; flex-wrap:wrap; align-items:center; gap:.6rem 1rem; margin-top:.5rem; }
.hvai-chip{ display:inline-flex; align-items:center; gap:.45rem; padding:.35rem .6rem; border-radius:999px; background:var(--hvai-chip);
  border:1px solid var(--hvai-border); color:#e7eef7; font-weight:700; }
.hvai-chip .icon{ width:16px; height:16px; opacity:.9; }

/* Bars keep your existing classes but refresh colors */
.hvai-fill.human{ background: linear-gradient(90deg, var(--hvai-human), #29f59d); box-shadow: 0 0 28px rgba(34,197,94,.35); }
.hvai-fill.ai{ background: linear-gradient(270deg, #ff7a59, #ffb15e); box-shadow: 0 0 28px rgba(255,122,89,.35); }

.hvai-label{ display:flex; align-items:center; justify-content:space-between; gap:1rem; font-weight:800; color:#eaf2ff; }
.hvai-label .lab{ display:flex; align-items:center; gap:.5rem; }
.hvai-label .lab .icon{ width:18px; height:18px; }

.hvai .ring .ai-arc{ stroke:var(--hvai-ai) !important; }
.hvai .ring .human-arc{ stroke:var(--hvai-human) !important; }
.hvai .ring text{ fill:#eaf2ff !important; font-weight:900; }

/* Language row */
.hvai .lang-row{ display:flex; align-items:center; gap:.6rem; margin:.6rem 0 .2rem; }
.hvai .lang-row select{
  background:var(--hvai-card); color:#eaf2ff; border:1px solid var(--hvai-border);
  padding:.4rem .6rem; border-radius:10px; font-weight:700;
}
.hvai .lang-row .icon{ width:18px; height:18px; }

/* RTL helper for AR/UR */
.hvai[dir="rtl"] .hvai-label{ flex-direction: row-reverse; }
.hvai[dir="rtl"] .hvai-label .lab{ flex-direction: row-reverse; }
.hvai[dir="rtl"] .hvai-meta{ justify-content:flex-end; }

/* small badge */
.badge-beta{ display:inline-flex; align-items:center; gap:.35rem; padding:.2rem .5rem; border-radius:10px;
  background:rgba(2,204,255,.12); border:1px solid var(--hvai-border); color:#aaf7ff; font-weight:800; font-size:.72rem; }
/* ====================================================================== */

/* ==== Readability (ULTRA PRO restyle) ==== */
:root{
  --read-ac1:#22c55e; /* emerald */
  --read-ac2:#3de2ff; /* cyan */
  --read-ac3:#9b5cff; /* purple */
  --read-warn:#f59e0b; /* amber */
  --read-bad:#ef4444;  /* red  */
  --read-surface:rgba(255,255,255,.06);
  --read-border:rgba(255,255,255,.14);
  --read-soft:rgba(255,255,255,.08);
  --read-dim:rgba(255,255,255,.62);
}

/* container with animated halo + soft particles */
.readability{
  margin-top:14px; position:relative; isolation:isolate; overflow:hidden;
  background:
    radial-gradient(1200px 300px at -10% 0%, rgba(157,78,221,.18), transparent 40%),
    radial-gradient(900px 320px at 110% 20%, rgba(45,212,191,.16), transparent 50%),
    linear-gradient(135deg, rgba(16,24,48,.55), rgba(18,20,40,.72));
  border:1px solid var(--read-border);
  border-radius:18px; padding:18px;
  box-shadow:0 20px 60px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.06);
  backdrop-filter:blur(8px);
}
.readability::before{
  /* conic glow border sweep */
  content:""; position:absolute; inset:-1px; border-radius:20px; padding:1px;
  background:conic-gradient(from 0deg, #3de2ff, #22c55e, #9b5cff, #f59e0b, #ff2045, #3de2ff);
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude;
  opacity:.25; pointer-events:none; animation:readGlow 16s linear infinite;
}
.readability::after{
  /* tiny floating sparkles */
  content:""; position:absolute; inset:-20%;
  background:
    radial-gradient(2px 2px at 10% 30%, rgba(255,255,255,.35) 40%, transparent 45%),
    radial-gradient(2px 2px at 30% 70%, rgba(255,255,255,.30) 40%, transparent 45%),
    radial-gradient(2px 2px at 70% 20%, rgba(255,255,255,.28) 40%, transparent 45%),
    radial-gradient(2px 2px at 85% 80%, rgba(255,255,255,.32) 40%, transparent 45%),
    radial-gradient(2px 2px at 55% 45%, rgba(255,255,255,.26) 40%, transparent 45%);
  filter:blur(.2px); opacity:.45; animation:floatDots 28s linear infinite;
  pointer-events:none;
}
@keyframes readGlow{to{transform:rotate(360deg)}}
@keyframes floatDots{to{transform:translate3d(-8%, -6%, 0)}}

.read-head{display:flex;align-items:center;gap:.7rem;margin-bottom:.8rem}
.read-head h4{margin:0;font-size:1.18rem;letter-spacing:.2px}
.read-head .ico{
  width:34px;height:34px; border-radius:12px; display:grid;place-items:center;
  background:linear-gradient(135deg,var(--read-ac1),var(--read-ac2));
  color:#fff; text-shadow:0 1px 0 rgba(0,0,0,.35);
  box-shadow:0 8px 18px rgba(61,226,255,.3), inset 0 0 0 1px rgba(255,255,255,.12);
}

.read-summary{
  display:grid;grid-template-columns:auto 1fr;gap:.65rem;align-items:center;
  margin:.35rem 0 .7rem;
}
.read-caption{color:var(--read-dim)}

/* status chip with gentle pulse */
.read-chip{
  display:inline-flex;align-items:center;gap:.55rem;
  padding:.46rem .9rem;border-radius:999px;border:1px solid rgba(255,255,255,.22);
  font-weight:900; letter-spacing:.2px;
  background:linear-gradient(135deg, rgba(34,197,94,.18), rgba(61,226,255,.18));
  box-shadow:inset 0 0 0 1px rgba(255,255,255,.06), 0 8px 20px rgba(0,0,0,.25);
  position:relative; overflow:hidden;
}
.read-chip::after{
  content:""; position:absolute; inset:0; mix-blend-mode:screen;
  background:radial-gradient(120px 40px at 0% 0%, rgba(255,255,255,.18), transparent 60%);
  animation:chipShine 6s ease-in-out infinite;
}
@keyframes chipShine{50%{transform:translateX(40%)}}

/* bad & mid variants without touching your JS/HTML */
.read-chip.bad{background:linear-gradient(135deg, rgba(239,68,68,.22), rgba(245,158,11,.20))}
.read-chip.mid{background:linear-gradient(135deg, rgba(245,158,11,.22), rgba(61,226,255,.20))}

/* responsive, masonry-like grid */
.read-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit, minmax(260px,1fr));
  gap:.8rem; perspective:1000px;
}
.read-card{
  background:linear-gradient(180deg,var(--read-surface),rgba(255,255,255,.03));
  border:1px solid var(--read-border);
  border-radius:16px; padding:.85rem;
  box-shadow:0 12px 36px rgba(0,0,0,.32);
  backdrop-filter:blur(6px);
  transform-style:preserve-3d;
  transition:transform .2s ease, box-shadow .25s ease, border-color .2s ease;
}
.read-card:hover{
  transform:translateY(-3px) rotateX(.6deg);
  box-shadow:0 18px 48px rgba(0,0,0,.42);
  border-color:rgba(255,255,255,.22);
}

/* metric row with animated icon pills */
.read-card .metric{display:flex;align-items:center;justify-content:space-between;font-weight:900}
.read-card .metric i{
  display:inline-grid;place-items:center;
  width:38px;height:38px;border-radius:12px;margin-right:.55rem;color:#fff;
  text-shadow:0 1px 0 rgba(0,0,0,.45);
  background:conic-gradient(from 0deg, var(--read-ac1), var(--read-ac2), var(--read-ac3), var(--read-ac1));
  animation:spinGrad 10s linear infinite;
  box-shadow:0 8px 20px rgba(0,0,0,.28), inset 0 0 0 1px rgba(255,255,255,.12);
  transition:transform .18s ease;
}
.read-card:hover .metric i{ transform:translateZ(12px) scale(1.06) }
@keyframes spinGrad{to{transform:rotate(360deg)}}

/* per-card palettes (no HTML change) */
.read-grid .read-card:nth-child(2) .metric i{filter:hue-rotate(40deg)}
.read-grid .read-card:nth-child(3) .metric i{filter:hue-rotate(90deg)}
.read-grid .read-card:nth-child(4) .metric i{filter:hue-rotate(150deg)}
.read-grid .read-card:nth-child(5) .metric i{filter:hue-rotate(200deg)}
.read-grid .read-card:nth-child(6) .metric i{filter:hue-rotate(260deg)}
.read-grid .read-card:nth-child(7) .metric i{filter:hue-rotate(310deg)}

/* progress meter with glossy sweep + end bubble */
.meter{
  margin-top:.55rem;height:12px;border-radius:10px; position:relative; overflow:hidden;
  background:linear-gradient(180deg,#0b0d21,#0b0d21);
  border:1px solid var(--read-border);
  box-shadow:inset 0 0 0 1px rgba(255,255,255,.04);
}
.meter::before{
  /* subtle stripes in the track */
  content:""; position:absolute; inset:0; opacity:.35;
  background:repeating-linear-gradient(45deg, rgba(255,255,255,.06) 0 10px, transparent 10px 20px);
  pointer-events:none;
}
.meter > span{
  position:absolute;left:0;top:0;bottom:0;width:0%;
  background:linear-gradient(90deg, var(--read-ac1), var(--read-ac2), var(--read-ac3));
  box-shadow:inset 0 0 0 1px rgba(255,255,255,.12), 0 10px 26px rgba(61,226,255,.28);
  transition:width .55s cubic-bezier(.22,1,.36,1);
}
.meter > span::after{
  /* glossy sweep */
  content:""; position:absolute; inset:0;
  background:linear-gradient(120deg,transparent,rgba(255,255,255,.18),transparent 60%);
  mix-blend-mode:screen; transform:translateX(-120%); animation:meterSheen 3s linear infinite;
}
.meter > span::before{
  /* glowing end bubble that tracks width (no JS needed) */
  content:""; position:absolute; top:50%; right:-7px; transform:translateY(-50%);
  width:18px;height:18px;border-radius:50%;
  background:radial-gradient(circle at 30% 30%, #fff, rgba(255,255,255,.1) 45%);
  box-shadow:0 0 0 3px rgba(61,226,255,.25), 0 0 30px rgba(61,226,255,.45);
}
@keyframes meterSheen{to{transform:translateX(120%)}}

/* suggestions with neon ticks and connecting stems */
.read-suggest{
  margin-top:.8rem;background:rgba(255,255,255,.05);
  border:1px solid var(--read-border);border-radius:14px;padding:.8rem;
  box-shadow:0 10px 28px rgba(0,0,0,.28);
}
.read-suggest .title{font-weight:900;margin-bottom:.4rem;display:flex;align-items:center;gap:.45rem}
.read-suggest ul{margin:.2rem 0 0;padding-left:1.1rem}
.read-suggest li{
  margin:.28rem 0; list-style:none; position:relative; padding-left:1.2rem; color:rgba(255,255,255,.9);
}
.read-suggest li::before{
  content:""; position:absolute; left:0; top:.12rem; width:18px;height:18px;border-radius:6px;
  background:linear-gradient(135deg,var(--read-ac1),var(--read-ac2));
  box-shadow:0 6px 14px rgba(0,0,0,.25);
}
.read-suggest li::after{
  content:"\f00c"; /* Font Awesome check */
  font-family:"Font Awesome 6 Free"; font-weight:900;
  position:absolute; left:3px; top:-1px; font-size:.76rem; color:#fff; text-shadow:0 1px 0 rgba(0,0,0,.35);
}

/* plain info block with lively gradient */
.read-plain{
  margin-top:.8rem;border:1px solid var(--read-border);border-radius:14px;padding:.8rem;
  background:linear-gradient(135deg, rgba(34,197,94,.14), rgba(61,226,255,.14));
  box-shadow:0 10px 28px rgba(0,0,0,.24);
}
.read-plain .title{font-weight:900;margin-bottom:.3rem;display:flex;align-items:center;gap:.45rem}

/* prefers-reduced-motion friendly */
@media (prefers-reduced-motion:reduce){
  .readability::before,.readability::after,.read-card .metric i,
  .meter > span::after{animation:none}
}

/* small screens */
@media (max-width:900px){
  .read-summary{grid-template-columns:1fr}
}
/* ==== Entities & Topics (colorful) ==== */
.entities{margin-top:14px;background:linear-gradient(135deg,rgba(76,29,149,.18),rgba(14,165,233,.18));border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px}
.entities-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.5rem}
.entities-head h4{margin:0;font-size:1.08rem}
.entity-groups{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.entity-card{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.7rem}
.entity-title{display:flex;align-items:center;gap:.45rem;font-weight:900;margin-bottom:.4rem}
.entity-chips{display:flex;flex-wrap:wrap;gap:.4rem}
.echip{display:inline-flex;align-items:center;gap:.35rem;padding:.32rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);font-weight:800}
.echip.sw{background:linear-gradient(135deg,#06b6d4,#8b5cf6)}
.echip.apk{background:linear-gradient(135deg,#22c55e,#3b82f6)}
.echip.game{background:linear-gradient(135deg,#f97316,#ef4444)}
.echip.person{background:linear-gradient(135deg,#9b5cff,#ff2045)}
.echip.org{background:linear-gradient(135deg,#3de2ff,#22c55e)}
.echip.place{background:linear-gradient(135deg,#fde047,#60a5fa)}
.echip.misc{background:linear-gradient(135deg,#94a3b8,#64748b)}
@media (max-width:768px){.entity-card{grid-column:span 12}}

/* ==== Site Speed & CWV (colorful, end) ==== */
.psi{margin-top:14px;background:linear-gradient(135deg,rgba(34,197,94,.10),rgba(59,130,246,.10));border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:16px}
.psi-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem}
.psi-head h4{margin:0;font-size:1.08rem}
.psi-meta{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.5rem}
.psi-chip{display:inline-flex;align-items:center;gap:.45rem;padding:.35rem .7rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);font-weight:900;background:rgba(255,255,255,.05)}
.psi-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.psi-card{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.7rem}
.psi-card .metric{display:flex;align-items:center;justify-content:space-between;font-weight:900}
.psi-meter{margin-top:.45rem;height:12px;border-radius:10px;background:#0b0d21;border:1px solid rgba(255,255,255,.12);overflow:hidden;position:relative}
.psi-meter>span{position:absolute;left:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#22c55e,#3de2ff);transition:width .45s ease}
.psi-issues{margin-top:.6rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.6rem}
.psi-issues .title{font-weight:900;margin-bottom:.3rem;display:flex;align-items:center;gap:.4rem}
.psi-issues ul{margin:.2rem 0 0;padding-left:1rem}
.psi-issues li{margin:.22rem 0}
@media (max-width:768px){.psi-card{grid-column:span 12}}


/* === Site Speed & Core Web Vitals — Neo Card (scoped) ====================== */
.psi .speed-card{
  border:1px solid rgba(255,255,255,.08);
  border-radius:18px; padding:16px;
  background: radial-gradient(900px 420px at 90% -10%, rgba(0,255,171,.14), transparent 60%),
              radial-gradient(900px 420px at 0% -20%, rgba(2,204,255,.10), transparent 55%);
  box-shadow: 0 8px 24px rgba(0,0,0,.25), inset 0 0 0 1px rgba(255,255,255,.04);
  backdrop-filter: blur(6px);
}
.psi .speed-head{ display:flex; align-items:center; gap:.6rem; color:#bdffe7; font-weight:900; font-size: clamp(1.05rem, 1vw + .9rem, 1.35rem); }
.psi .speed-head .icon{ width:22px; height:22px; filter: drop-shadow(0 0 10px rgba(0,255,171,.25)); }
.psi .speed-sub{ color:#a6f7ff; font-size:.86rem; opacity:.9; }

.psi .speed-grid{ display:grid; grid-template-columns: repeat(auto-fit, minmax(220px,1fr)); gap:12px; margin-top:10px; }
.psi .metric{ border:1px solid rgba(255,255,255,.08); border-radius:14px; padding:12px; background: rgba(15,18,30,.55); }
.psi .metric .top{ display:flex; align-items:center; justify-content:space-between; gap:.6rem; }
.psi .metric .lab{ display:flex; align-items:center; gap:.5rem; font-weight:800; color:#eaf2ff; }
.psi .metric .lab .icon{ width:18px; height:18px; }
.psi .metric .val{ font-weight:900; font-size:1.1rem; color:#fff; }
.psi .badge{ padding:.2rem .45rem; border-radius:8px; font-weight:800; font-size:.72rem; }
.psi .badge.fast{ background:rgba(0,255,171,.16); color:#b2ffe9; border:1px solid rgba(0,255,171,.28); }
.psi .badge.ok{ background:rgba(255,196,0,.12); color:#ffe8a3; border:1px solid rgba(255,196,0,.25); }
.psi .badge.slow{ background:rgba(255,95,95,.12); color:#ffc9c9; border:1px solid rgba(255,95,95,.25); }

/* tiny bar for each metric */
.psi .mini{ height:10px; border-radius:10px; background:rgba(255,255,255,.08); overflow:hidden; margin-top:8px; }
.psi .mini > i{ display:block; height:100%; border-radius:10px; background:linear-gradient(90deg, #00ffaa, #02ccff); }
/* ======================================================================== */



/* Keep background overlays visible (but non-interactive) */
.water-overlay, .water-svg, .comp-overlay{
  pointer-events: none;
  z-index: 0;
}


/* Heavy multicolor smoke + tech lines */
.page-bg{
  position: fixed;
  inset: 0;
  z-index: -1;
  pointer-events: none;
}
/* layered 'smoke' using multiple radial gradients */
body::before, body::after{
  content: "";
  position: fixed;
  inset: -10% -10% auto -10%;
  height: 80%;
  z-index: -2;
  pointer-events: none;
  background:
    radial-gradient(600px 300px at 10% 10%, rgba(155,92,255,.30), transparent 60%),
    radial-gradient(650px 320px at 90% 15%, rgba(2,204,255,.26), transparent 60%),
    radial-gradient(700px 340px at 50% -10%, rgba(0,255,171,.22), transparent 60%),
    radial-gradient(500px 260px at 20% 80%, rgba(255,165,0,.12), transparent 70%);
  filter: blur(10px);
}
body::after{
  inset: auto -10% -10% -10%;
  height: 70%;
  background:
    radial-gradient(700px 340px at 80% 80%, rgba(155,92,255,.22), transparent 60%),
    radial-gradient(600px 300px at 20% 70%, rgba(2,204,255,.18), transparent 60%),
    radial-gradient(650px 320px at 60% 90%, rgba(0,255,171,.18), transparent 60%);
  filter: blur(12px);
}
/* subtle tech lines overlay */
.bg-lines{
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  opacity: .35;
  mix-blend-mode: screen;
}


/* Non-blocking overlays fix (do not intercept clicks) */
.water-pct, .comp-pct, .bg-lines, .water-svg, .comp-svg {
  pointer-events: none !important;
}

</style>
</head>
<body>

<div class="page-bg" aria-hidden="true">
  <svg class="bg-lines" viewBox="0 0 1200 600" preserveAspectRatio="none">
    <defs>
      <linearGradient id="glow" x1="0" x2="1" y1="0" y2="1">
        <stop offset="0%" stop-color="#9b5cff" stop-opacity="0.35"/>
        <stop offset="50%" stop-color="#02ccff" stop-opacity="0.25"/>
        <stop offset="100%" stop-color="#00ffab" stop-opacity="0.25"/>
      </linearGradient>
    </defs>
    <g stroke="url(#glow)" stroke-width="1">
      <path d="M0,520 C250,420 450,580 700,480 C950,380 1000,560 1200,480" fill="none"/>
      <path d="M0,420 C250,320 450,480 700,380 C950,280 1000,460 1200,380" fill="none"/>
      <path d="M0,320 C250,220 450,380 700,280 C950,180 1000,360 1200,280" fill="none"/>
    </g>
  </svg>
</div>


<!-- Background canvases -->
<canvas id="linesCanvas"></canvas>
<canvas id="smokeCanvas"></canvas>
















<script>
(function(){
  var __origAnalyze = (typeof window.analyze === 'function') ? window.analyze : null;
  window.analyze = function(){
    try{ if (__origAnalyze) __origAnalyze(); }catch(_){}
    try{ if (typeof window.SEMSEO_go === 'function') window.SEMSEO_go(); }catch(_){}
    // PSI has been removed, call only if present
    try{
      if (typeof window.runPSI === 'function'){
        var input = document.getElementById('analyzeUrl') || document.querySelector('input[type="url"], input[name*="url"]');
        var val = (input && input.value) ? input.value.trim() : '';
        window.runPSI(val);
      }
    }catch(_){}
    return false;
  };
})();
</script>

</body>
</html>
