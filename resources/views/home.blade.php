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
.btn-analyze{background:linear-gradient(135deg,#10b981,#22c55e);border-color:#20d391}
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

  /* Status banner & rewrite badge */
  .hvai .status{margin:6px 0 10px 0; font-weight:800; padding:10px 12px; border-radius:12px; display:inline-flex; align-items:center; gap:10px; border:1px solid rgba(255,255,255,.08); background:rgba(255,255,255,.04)}
  .hvai .status.neutral{color:#cbd5ff}
  .hvai .status.good{background:linear-gradient(90deg, rgba(0,255,180,.10), rgba(0,200,255,.10)); color:#8bffd6; border-color:rgba(0,255,200,.25)}
  .hvai .status.warn{background:linear-gradient(90deg, rgba(255,200,0,.10), rgba(255,120,0,.10)); color:#ffd37a; border-color:rgba(255,180,0,.25)}
  .hvai .status.bad{background:linear-gradient(90deg, rgba(255,0,120,.12), rgba(255,0,0,.10)); color:#ff9ab6; border-color:rgba(255,60,120,.25)}
  .hvai .rewrite-badge{display:none; margin-left:8px; padding:6px 10px; border-radius:999px; font-weight:900; letter-spacing:.02em; background:#ff1f5b; color:white; box-shadow:0 0 16px rgba(255,31,91,.45)}
  .hvai .status.bad .rewrite-badge{display:inline-flex}

  /* Animated colorful icon for each bar label */
  .hvai .label{display:flex; align-items:center; gap:8px; justify-content:space-between}
  .hvai .label .ico{width:12px; height:12px; border-radius:50%; background:conic-gradient(#ff6a00, #ffd300, #2ad1a3, #1aa6ff, #9659ff, #ff6a00); animation: spin 6s linear infinite; box-shadow:0 0 10px rgba(255,255,255,.25)}
  .hvai .label .num{font-weight:900; opacity:.9}
  @keyframes spin{to{transform:rotate(1turn)}}

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


/* color states */
.burn-wheel.good{ --ring:#22c55e; --ring2:#16a34a; --glow:rgba(34,197,94,.6); }
.burn-wheel.mid{  --ring:#f59e0b; --ring2:#d97706; --glow:rgba(245,158,11,.6); }
.burn-wheel.bad{  --ring:#ef4444; --ring2:#dc2626; --glow:rgba(239,68,68,.6); }


/* ===== HVAI v2 Stylish Gauge ===== */
.card.glassy{ background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08); border-radius: 18px; backdrop-filter: blur(8px); box-shadow: 0 8px 30px rgba(0,0,0,.25) inset, 0 8px 20px rgba(0,0,0,.28); }
.hvai-v2{ padding: 18px 16px; margin: 12px 0 6px; }
.hvai-v2-head{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin: 2px 4px 10px; }
.hvai-v2-head .badge-model{ display:inline-flex; align-items:center; gap:8px; font-weight:700; color:#a6f7ff; background:linear-gradient(90deg, rgba(22,163,74,.18), rgba(99,102,241,.18)); border:1px solid rgba(166,247,255,.25); padding:6px 10px; border-radius:999px; letter-spacing:.2px; }
.hvai-v2-head .badge-model .dot{ width:9px; height:9px; border-radius:50%; background:radial-gradient(#a6f7ff,#60a5fa); box-shadow:0 0 8px #60a5fa; animation:pulseDot 2.6s ease-in-out infinite; }
.hvai-v2-head .model-note{ color:#9fb6ff; font-size:.85rem; opacity:.9 }
@keyframes pulseDot{ 0%,100%{ transform:scale(1); opacity:.9 } 50%{ transform:scale(1.25); opacity:1 } }

.neon-gauge{ position:relative; width:240px; height:240px; margin: 8px auto 0; }
.neon-gauge .g{ transform: rotate(-90deg); width:100%; height:100%; }
.neon-gauge .track{ stroke: rgba(255,255,255,.08); }
.neon-gauge .prog{ stroke-dasharray: 302; stroke-dashoffset: 302; transition: stroke-dashoffset .9s cubic-bezier(.22,.9,.26,1), stroke .2s; }
.neon-gauge .prog.good{ stroke: url(#gradGood); }
.neon-gauge .prog.mid{ stroke: url(#gradMid); }
.neon-gauge .prog.bad{ stroke: url(#gradBad); }

.neon-gauge .center{ position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; pointer-events:none; }
.neon-gauge .score{ color:#eaf7ff; font-weight:900; text-shadow: 0 2px 10px rgba(0,0,0,.45); letter-spacing:.6px; }
.neon-gauge .score span{ font-size:54px; }
.neon-gauge .score small{ font-size:16px; opacity:.85; }
.neon-gauge .msg{ margin-top:6px; font-weight:700; font-size:.95rem; color:#a6f7ff; text-shadow: 0 2px 8px rgba(0,0,0,.5); }

.hvai-v2-meta{ display:flex; gap:8px; justify-content:center; margin: 12px 0 4px; flex-wrap:wrap; }
.hvai-v2-meta .chip{ background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.09); color:#e5edff; padding:6px 10px; border-radius:999px; font-size:.85rem; }
.hvai-v2-meta .chip.human b{ color:#86efac }
.hvai-v2-meta .chip.ai b{ color:#fca5a5 }
.hvai-v2-meta .chip.conf b{ color:#fef08a }

/* Confetti */
.neon-gauge .confetti{ position:absolute; inset:0; pointer-events:none; overflow:hidden; }
.neon-gauge .confetti i{ position:absolute; width:6px; height:10px; transform: translate(-50%,-50%); border-radius:2px; opacity:0; animation: conf 1.6s ease-out forwards; }
@keyframes conf{ 0%{ opacity:1; } 100%{ opacity:0; transform: translate(-50%,-90vh) rotate(220deg); } }

/* Animated colorful heading icon */
.hvai .hvai-head .ico{ background: linear-gradient(135deg,#60a5fa,#a78bfa,#34d399,#f59e0b); -webkit-background-clip: text; background-clip: text; color: transparent; filter: drop-shadow(0 0 10px rgba(99,102,241,.5)); animation: spinPulse 8s linear infinite; }
@keyframes spinPulse{ 0%{ transform: rotate(0deg);} 100%{ transform: rotate(360deg);} }


    /* DEBUG badge to confirm the updated view is live */
    .hvai-version-badge{
      position: fixed;
      right: 10px;
      bottom: 10px;
      z-index: 9999;
      padding: 8px 12px;
      font-size: 12px;
      font-weight: 700;
      border-radius: 999px;
      color: #fff;
      background: linear-gradient(90deg, #8b5cf6, #06b6d4, #22c55e, #f59e0b, #ef4444);
      background-size: 300% 100%;
      animation: badgeShift 2s linear infinite;
      box-shadow: 0 8px 22px rgba(0,0,0,.45);
      border: 1px solid rgba(255,255,255,.35);
    }
    @keyframes badgeShift { 0%{ background-position:0% 0 } 100%{ background-position:100% 0 } }

  </style>

<style>
/* Fallback colorful panel */
.hvai-v2.card.glassy{background: radial-gradient(1200px 500px at -10% -20%, rgba(96,165,250,.08), transparent 55%),
radial-gradient(950px 480px at 110% -10%, rgba(167,139,250,.08), transparent 60%),
radial-gradient(700px 520px at 30% 120%, rgba(52,211,153,.06), transparent 60%),
rgba(255,255,255,.035);border:1px solid rgba(166,247,255,.10)}

    /* DEBUG badge to confirm the updated view is live */
    .hvai-version-badge{
      position: fixed;
      right: 10px;
      bottom: 10px;
      z-index: 9999;
      padding: 8px 12px;
      font-size: 12px;
      font-weight: 700;
      border-radius: 999px;
      color: #fff;
      background: linear-gradient(90deg, #8b5cf6, #06b6d4, #22c55e, #f59e0b, #ef4444);
      background-size: 300% 100%;
      animation: badgeShift 2s linear infinite;
      box-shadow: 0 8px 22px rgba(0,0,0,.45);
      border: 1px solid rgba(255,255,255,.35);
    }
    @keyframes badgeShift { 0%{ background-position:0% 0 } 100%{ background-position:100% 0 } }

  </style>


<!-- ===== New Neo‑Glass Aurora Skin (global) ===== -->
<style>
:root {
  /* Base */
  --bg: #0c1022;
  --panel: rgba(18, 22, 46, 0.85);
  --card: rgba(18, 22, 46, 0.92);
  --text: #eaf1ff;
  --text-dim: #a7b6ff;
  --border: rgba(255,255,255,.12);
  --shadow: 0 20px 60px rgba(0,0,0,.45);

  /* Accents (Aurora) */
  --acc-1: #7c5cff;
  --acc-2: #22c3f7;
  --acc-3: #a3ff7f;
  --acc-warn: #ffb257;
  --acc-bad:  #ff6b6b;
  --acc-good: #39e58c;

  /* Rounding & spacing */
  --radius: 16px;
  --chip-radius: 12px;
  --pad: 16px;
}
body { background:
    radial-gradient(1200px 600px at 80% -10%, rgba(124,92,255,.14), transparent 60%),
    radial-gradient(1000px 600px at -10% 20%, rgba(34,195,247,.12), transparent 60%),
    radial-gradient(900px 500px at 50% 120%, rgba(163,255,127,.10), transparent 60%),
    var(--bg);
  color: var(--text);
}
.card, .panel, .cl-card {
  background: var(--card);
  border: 1px solid var(--border);
  border-radius: var(--radius);
  box-shadow: var(--shadow);
  backdrop-filter: blur(6px);
}
h2.section-title, .cl-title {
  letter-spacing: .2px;
  background: linear-gradient(90deg, var(--acc-1), var(--acc-2));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}
.improve-btn {
  display: inline-flex; align-items: center; gap: .45rem;
  padding: .45rem .7rem;
  font-weight: 700; font-size: 13px;
  color: #111;
  background: linear-gradient(135deg, var(--acc-2), var(--acc-1));
  border-radius: var(--chip-radius);
  border: 0; cursor: pointer;
  transition: transform .12s ease, box-shadow .12s ease, filter .12s ease;
  box-shadow: 0 6px 18px rgba(124,92,255,.22);
}
.improve-btn:hover { transform: translateY(-1px); filter: brightness(1.05); }
.improve-btn:active { transform: translateY(0); }
.score-badge {
  display:inline-flex; align-items:center; justify-content:center;
  min-width: 42px; height: 28px; padding: 0 .5rem;
  font-weight: 800; font-size: 13px;
  border-radius: 999px; border: 1px solid var(--border);
  background: rgba(255,255,255,.06);
}
.score-badge[data-score^="8"], .score-badge[data-score^="9"] { outline: 2px solid var(--acc-good); }
.score-badge[data-score^="6"], .score-badge[data-score^="7"] { outline: 2px solid var(--acc-warn); }
.score-badge[data-score^="4"], .score-badge[data-score^="5"], .score-badge[data-score^="3"] { outline: 2px solid var(--acc-bad); }
.checklist li input[type="checkbox"] {
  width: 18px; height: 18px; border-radius: 6px;
  border: 1px solid var(--border); background: rgba(255,255,255,.05);
}
.checklist li input[type="checkbox"]:checked {
  background: linear-gradient(135deg, var(--acc-3), var(--acc-2));
  border-color: transparent;
}
.cl-modal .cl-sub { color: var(--text-dim); }
.cl-close { border-color: var(--border); }
.card--aurora, .aurora-border { position: relative; }
.card--aurora::before, .aurora-border::before {
  content:""; position:absolute; inset: -1px;
  border-radius: inherit; pointer-events:none;
  background: linear-gradient(90deg, rgba(124,92,255,.55), rgba(34,195,247,.55), rgba(163,255,127,.55));
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor; mask-composite: exclude;
  padding: 1px;
  border-radius: calc(var(--radius) + 1px);
}
</style>
<!-- ===== /New Neo‑Glass Aurora Skin ===== -->

</head>
<body>

<!-- Background canvases -->
<canvas id="linesCanvas"></canvas>
<canvas id="smokeCanvas"></canvas>

<script>
  window.SEMSEO = window.SEMSEO || {};
  window.SEMSEO.ENDPOINTS = {
    analyzeJson: @json($analyzeJsonUrl),
    analyze: @json($analyzeUrl),
    psi: @json($psiProxyUrl), // server proxy; API key stays hidden
    // NEW: backend detector endpoint (works even with no API keys; local server ensemble)
    detect: @json($detectUrl)
  };
  window.SEMSEO.SMOKE_HUE_PERIOD_MS = 1000000000;
  window.SEMSEO.READY = false;
  window.SEMSEO.BUSY = false;
  window.SEMSEO.QUEUE = 0;
  function SEMSEO_go(){
    if (window.SEMSEO.READY && typeof analyze === 'function') { analyze(); }
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
        <div class="hero-sub">Analyze URLs, get scores & colorful insights</div>
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
          <span class="chip" id="aiBadge" title="Detection summary"><i class="fa-solid fa-user-check ico ico-green"></i> Writer: <b>—</b></span>
          <button id="viewHumanBtn" class="btn btn-ghost"><i class="fa-solid fa-user ico ico-green"></i> Human-like: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost"><i class="fa-solid fa-robot ico ico-red"></i> AI-like: <b id="aiPct">—</b>%</button>
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

        <div class="analyze-row">
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

    

<!-- 1) HUMAN vs AI Content (Ensemble) — v2025-08-26 • v5 wheel + tech lines + animated icon -->

<!-- HUMAN vs AI Content (Ensemble) — v17 CLEAN -->

<!-- HUMAN vs AI Content (Ensemble) — v19 FEATHER (fast) -->

<!-- HUMAN vs AI Content (Ensemble) — v20 PRISM (multicolor) -->

<!-- HUMAN vs AI Content (Ensemble) — v22 PRISM (scoped, full) -->

<!-- HUMAN vs AI Content (Ensemble) — v26 PRISM (forcefix) -->
<section id="hvai" class="hvai hvai-v26" aria-label="Human vs AI Content (Ensemble)">
  <style>
  .hvai.hvai-v26{position:relative; isolation:isolate; padding:28px; border-radius:16px; background:rgba(8,10,18,.55); overflow:hidden; content-visibility:auto; contain:layout paint style}
  .hvai.hvai-v26 *{box-sizing:border-box}
  .hvai.hvai-v26 .tech{position:absolute; inset:-2px; z-index:0; pointer-events:none;
    background:
      linear-gradient(135deg, rgba(0,255,255,.10) 1px, transparent 1px) 0 0/18px 18px,
      linear-gradient(45deg, rgba(255,0,180,.08) 1px, transparent 1px) 0 0/20px 20px,
      radial-gradient(800px 600px at 85% 10%, rgba(67,169,255,.08), transparent 60%),
      radial-gradient(700px 500px at 8% 95%, rgba(238,99,255,.08), transparent 60%);
  }
  .hvai.hvai-v26 .grid{position:relative; z-index:1; display:grid; gap:24px; grid-template-columns:1fr minmax(220px, clamp(220px, 24vw, 320px)); align-items:center}
  @media (max-width:1100px){ .hvai.hvai-v26 .grid{grid-template-columns:1fr} .hvai.hvai-v26 .wheel{order:-1; margin:6px auto 14px} }
  .hvai.hvai-v26 .wheel{ display:flex; align-items:center; justify-content:center; }

  .hvai.hvai-v26 .title{display:flex; align-items:center; gap:12px; margin:0 0 8px}
  .hvai.hvai-v26 .title .txt{font:800 clamp(22px,2.6vw,34px)/1.15 system-ui,-apple-system,Segoe UI,Roboto,Inter,Arial}
  .hvai.hvai-v26 .title .txt .rainbow{background:linear-gradient(90deg,#6bf,#9f6,#fb6,#9af,#6bf); background-size:300% 100%; -webkit-background-clip:text; background-clip:text; color:transparent; animation: rainbowShift 7s linear infinite}
  @keyframes rainbowShift{0%{background-position:0% 50%}100%{background-position:100% 50%}}

  .hvai.hvai-v26 .status{margin:6px 0 12px}
  .hvai.hvai-v26 .badge{display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.10)}
  .hvai.hvai-v26 .status .status-badge{display:inline-flex; align-items:center; gap:10px; padding:10px 14px; border-radius:999px; font-weight:900; letter-spacing:.01em; border:1px solid rgba(255,255,255,.12)}
  .hvai.hvai-v26 .status.good .status-badge{background:linear-gradient(90deg,#00ffb380,#00e0ff80); border-color:#00ffd5}
  .hvai.hvai-v26 .status.warn .status-badge{background:linear-gradient(90deg,#ffd86b80,#ff9d3f80); border-color:#ffb347}
  .hvai.hvai-v26 .status.bad  .status-badge{background:linear-gradient(90deg,#ff6aa580,#ff494980); border-color:#ff4d6d}

  .hvai.hvai-v26 .row2{display:flex; gap:18px; flex-wrap:wrap}
  .hvai.hvai-v26 .bar{flex:1 1 360px; background:#141724; border-radius:14px; padding:12px 14px; border:1px solid rgba(255,255,255,.06); min-width:260px}
  .hvai.hvai-v26 .bar .label{display:flex; align-items:center; justify-content:space-between; gap:8px; font-weight:800}
  .hvai.hvai-v26 .bar .ico{width:12px; height:12px; border-radius:50%; background:conic-gradient(#ff6a00,#ffd300,#2ad1a3,#1aa6ff,#9659ff,#ff6a00)}
  .hvai.hvai-v26 .bar .num{font-weight:900; color:#e9efff; font-variant-numeric:tabular-nums}
  .hvai.hvai-v26 .track{height:14px; border-radius:999px; background:rgba(255,255,255,.08); overflow:hidden; margin-top:8px}
  .hvai.hvai-v26 .fill{height:100%; width:0%; background:linear-gradient(90deg,#ff6a00,#ffd300,#2ad1a3,#1aa6ff,#9659ff)}

  /* Improve panel */
  .hvai.hvai-v26 .improve{margin-top:16px; padding:14px; border-radius:14px; background:linear-gradient(180deg, rgba(0,255,200,.06), rgba(0,140,255,.05)); border:1px solid rgba(255,255,255,.10)}
  .hvai.hvai-v26 .improve.hidden{display:none}
  .hvai.hvai-v26 .improve-head{font:800 15px/1.2 Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial; margin-bottom:8px}
  .hvai.hvai-v26 .improve-list{margin:0; padding-left:16px}
  .hvai.hvai-v26 .improve-list li{margin:6px 0; line-height:1.35}

  /* Prism wheel (compact) */
  .prism-wheel{position:relative; width:var(--wheel-size, clamp(200px, 22vw, 300px)); aspect-ratio:1; margin:0 auto}
  .prism-wheel .track{position:absolute; inset:0; border-radius:50%; background: radial-gradient(circle at 50% 50%, rgba(255,255,255,.12) 0, rgba(255,255,255,.12) calc(50% - var(--thick,14px)), transparent calc(50% - var(--thick,14px)), transparent 100%)}
  .prism-wheel .arc{position:absolute; inset:0; border-radius:50%;
    background: conic-gradient(from -90deg, #ff6a00 0%, #ffb700 10%, #ffd300 20%, #96e21a 30%, #2ad1a3 40%, #1aa6ff 50%, #5a6bff 60%, #9659ff 70%, #ff6aff 80%, #ff6a00 100%);
    -webkit-mask: radial-gradient(farthest-side, transparent calc(100% - var(--thick,14px)), #000 calc(100% - var(--thick,14px))), conic-gradient(from -90deg, #000 0 calc(var(--p)*1%), transparent calc(var(--p)*1%));
            mask: radial-gradient(farthest-side, transparent calc(100% - var(--thick,14px)), #000 calc(100% - var(--thick,14px))), conic-gradient(from -90deg, #000 0 calc(var(--p)*1%), transparent calc(var(--p)*1%));
  }
  .prism-wheel .pointer{position:absolute; inset:0; transform: rotate(calc(var(--p)*3.6deg)); transform-origin:50% 50%}
  .prism-wheel .pointer::after{content:""; position:absolute; left:50%; top: calc(var(--thick,14px)/2); transform: translateX(-50%); width:12px; height:12px; border-radius:50%; background:#00f5c4; box-shadow:0 0 0 4px rgba(0,245,196,.15)}
  .prism-wheel .center{position:absolute; inset:calc(var(--thick,14px) + 16px); border-radius:50%; display:grid; place-items:center; background: radial-gradient(120px 120px at 60% 40%, rgba(255,255,255,.06), rgba(10,12,20,.55))}
  .prism-wheel .kv{display:grid; gap:6px; text-align:center}
  .prism-wheel .kv .row{display:flex; align-items:center; gap:6px; justify-content:center; font: 700 13px/1.2 Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial; color:#eaf1ff}
  .prism-wheel .kv .val{font-weight:900; font-size:17px}
  .prism-wheel .kv .dot{width:8px; height:8px; border-radius:50%}
  .prism-wheel .kv .dot.ai{background:#7c5bff}
  .prism-wheel .kv .dot.h{background:#00f5c4}

  /* Hide any legacy wheel nodes inside .wheel */
  .hvai.hvai-v26 .wheel > *:not(.prism-wheel){ display:none !important; }
  </style>

  <div class="tech" aria-hidden="true"></div>

  <div class="grid">
    <div class="left">
      <div class="title"><div class="txt"><span class="rainbow">Human vs AI Content (Ensemble)</span></div></div>

      <div id="hvaiStatus" class="status neutral" aria-live="polite">
        <span class="status-badge"><span class="ico">⌛</span><span class="txt">Waiting for analysis…</span></span>
      </div>

      <div class="badges">
        <div class="badge">🛡️ <strong>Confidence:</strong> <span id="hvaiConf">0</span>%</div>
        <div class="badge">ℹ️ Higher bar = more AI-like</div>
      </div>

      <div class="row2" id="hvaiBars">
        <div class="bar" data-key="humanLike"><div class="label"><span class="ico"></span> Human-like <span class="num" id="hvaiValHumanBar">0</span></div><div class="track"><div class="fill" id="hvaiBarHuman" style="width:0%"></div></div></div>
        <div class="bar" data-key="lexical"><div class="label"><span class="ico"></span> Lexical Diversity <span class="num" id="hvaiValLex">0</span></div><div class="track"><div class="fill" id="hvaiBarLex" style="width:0%"></div></div></div>
        <div class="bar" data-key="burst"><div class="label"><span class="ico"></span> Burstiness <span class="num" id="hvaiValBurst">0</span></div><div class="track"><div class="fill" id="hvaiBarBurst" style="width:0%"></div></div></div>
        <div class="bar" data-key="digits"><div class="label"><span class="ico"></span> Digits Density <span class="num" id="hvaiValDigits">0</span></div><div class="track"><div class="fill" id="hvaiBarDigits" style="width:0%"></div></div></div>
        <div class="bar" data-key="repetition"><div class="label"><span class="ico"></span> Repetition (3‑gram) <span class="num" id="hvaiValRep">0</span></div><div class="track"><div class="fill" id="hvaiBarRep" style="width:0%"></div></div></div>
        <div class="bar" data-key="entropy"><div class="label"><span class="ico"></span> Character Entropy <span class="num" id="hvaiValEnt">0</span></div><div class="track"><div class="fill" id="hvaiBarEnt" style="width:0%"></div></div></div>
      </div>

      <div id="hvaiImprove" class="improve hidden" aria-live="polite">
        <div class="improve-head">How to raise your score</div>
        <ul class="improve-list" id="hvaiImproveList"></ul>
      </div>

      <small>Source: local ensemble (no external APIs).</small>
    </div>

    <div class="wheel">
      <div class="prism-wheel" id="prismWheel" style="--p:0; --thick:14px;">
        <div class="track"></div><div class="arc"></div><div class="pointer"></div>
        <div class="center"><div class="kv">
          <div class="row"><span class="dot ai"></span> AI‑like <span class="val"><span id="hvaiAIVal">0</span>%</span></div>
          <div class="row"><span class="dot h"></span> Human‑like <span class="val"><span id="hvaiHumanVal">0</span>%</span></div>
        </div></div>
      </div>
    </div>
  </div>

  <script>
  (function(){
    console.log('HVAI v26 loaded');
    var confLocked=false;
    function clamp(v){ return Math.max(0,Math.min(100,Math.round(v||0))); }
    function setBadge(h){ var st=document.getElementById('hvaiStatus'); if(!st) return; var txt=st.querySelector('.txt'); var ico=st.querySelector('.ico'); st.classList.remove('neutral','good','warn','bad'); if(h>=80){ st.classList.add('good'); if(txt) txt.textContent='Great Work'; if(ico) ico.textContent='✅'; } else if(h>=60){ st.classList.add('warn'); if(txt) txt.textContent='Need More Hard work on content'; if(ico) ico.textContent='🟡'; } else { st.classList.add('bad'); if(txt) txt.textContent='Needs Rewrite the Content'; if(ico) ico.textContent='✍️'; } }
    function setConfidence(v){ var el=document.getElementById('hvaiConf'); if(el) el.textContent=clamp(v); }
    function deriveConfFromAI(pAI){ return 60 + (Math.abs(50 - pAI) / 50) * 30; }
    function updateBars(subs){ if(!subs) return; var map=[['hvaiBarHuman','hvaiValHumanBar', subs.humanLike],['hvaiBarLex','hvaiValLex', subs.lexical],['hvaiBarBurst','hvaiValBurst', subs.burst],['hvaiBarDigits','hvaiValDigits', subs.digits],['hvaiBarRep','hvaiValRep', subs.repetition],['hvaiBarEnt','hvaiValEnt', subs.entropy],]; map.forEach(function(r){ var f=document.getElementById(r[0]); var n=document.getElementById(r[1]); var v=clamp(r[2]); if(f) f.style.width=v+'%'; if(n) n.textContent=v; }); }
    function deriveSubs(pAI){ var h=100-pAI, c=v=>Math.max(0,Math.min(100,Math.round(v))); return { humanLike:c(h), lexical:c(35+h*0.55), burst:c(25+h*0.7), digits:c(10+(100-h)*0.3), repetition:c(60-h*0.4), entropy:c(35+h*0.45) }; }
    function showImprovements(subs){ var box=document.getElementById('hvaiImprove'); var ul=document.getElementById('hvaiImproveList'); if(!box||!ul) return; var tips=[], h=subs.humanLike||0; function add(t){ if(t) tips.push(t); } if(h<80) add('Overall Human-like is ' + h + '%. Aim for ≥ 80 by applying the tips below.'); if(subs.lexical<75) add('Lexical Diversity is low ('+subs.lexical+'%). Add domain-specific terms and vary phrasing.'); if(subs.burst<70) add('Burstiness is low ('+subs.burst+'%). Mix short and long sentences.'); if(subs.repetition<70) add('Repetition (3‑gram) is high ('+subs.repetition+'%). Rephrase repeated chunks.'); if(subs.entropy<70) add('Character Entropy is low ('+subs.entropy+'%). Add varied punctuation and specific names.'); if(subs.digits>80) add('Digits Density is high ('+subs.digits+'%). Remove unnecessary numbers.'); if(tips.length<=1 && h<80) add('Add concrete examples and personal/brand perspective.'); ul.innerHTML=''; tips.slice(0,8).forEach(function(t){ var li=document.createElement('li'); li.textContent=t; ul.appendChild(li); }); box.classList.toggle('hidden', !(h<80 || tips.length>0)); }
    window.updateHVAIScore=function(pAI){ var p=clamp(pAI), h=100-p; var wheel=document.getElementById('prismWheel'); if(wheel) wheel.style.setProperty('--p', p); var a=document.getElementById('hvaiAIVal'); if(a) a.textContent=p; var b=document.getElementById('hvaiHumanVal'); if(b) b.textContent=h; setBadge(h); if(!confLocked) setConfidence(deriveConfFromAI(p)); var subs=deriveSubs(p); updateBars(subs); showImprovements(subs); };
    function detectUltra(text){ text=(text||'').replace(/\s+/g,' ').trim(); var len=text.length; if(len<40) return {ai:0,conf:60,subs:deriveSubs(0)}; var s=text.split(/(?<=[.!?])\s+/).filter(Boolean); var t=(text.toLowerCase().match(/[a-zA-ZÀ-ÿ0-9']+/g)||[]); var types=new Set(t); var ttr=types.size/Math.max(1,t.length), ttrS=(1-Math.abs(0.52-Math.min(0.95,ttr))/0.52)*100; var tri={}, repS; for(let i=0;i<t.length-2;i++){let g=t.slice(i,i+3).join(' '); tri[g]=(tri[g]||0)+1;} var repR=Object.values(tri).filter(v=>v>1).length/Math.max(1,Object.keys(tri).length); repS=(1-Math.min(0.6,repR)/0.6)*100; var sl=s.map(x=>(x.match(/\\w+/g)||[]).length), avg=sl.reduce((a,b)=>a+b,0)/Math.max(1,s.length); var sd=Math.sqrt(sl.reduce((a,b)=>a+Math.pow(b-avg,2),0)/Math.max(1,s.length)); var cov=avg?sd/avg:0; var burstS=Math.min(1,cov/0.8)*100; var freq={},H=0,N=0; for(let ch of text){ if(ch<' '||ch>'~') continue; freq[ch]=(freq[ch]||0)+1; N++; } for(let k in freq){ let p=freq[k]/N; H+=-p*Math.log2(p); } var entS=(1-Math.abs(3.8-Math.min(6,H))/3.8)*100; var digits=(text.match(/\\d/g)||[]).length/Math.max(1,len); var digS=(1-Math.max(0,0.06-digits)/0.06)*100; function syl(w){return Math.max(1,(w.match(/[aeiouy]+/gi)||[]).length-(w.match(/(?:e|ed|es)\\b/gi)||[]).length+(w.match(/le\\b/gi)?1:0));} var words=t.length||1, syls=t.reduce((a,w)=>a+syl(w),0); var FRE=206.835-(1.015*(words/Math.max(1,s.length)))-(84.6*(syls/words)); var readS=(1-Math.abs(60-Math.max(0,Math.min(100,FRE)))/60)*100; var human=ttrS*.18 + repS*.15 + burstS*.18 + entS*.12 + digS*.10 + readS*.15; var ai=Math.max(0,Math.min(100,100-human)); var subs={ humanLike:Math.round(100-ai), lexical:Math.round(ttrS), burst:Math.round(burstS), digits:Math.round(digS), repetition:Math.round(repS), entropy:Math.round(entS) }; var varSignals=[ttrS,repS,burstS,entS,digS,readS], mean=varSignals.reduce((a,b)=>a+b,0)/varSignals.length; var variance=varSignals.reduce((a,b)=>a+Math.pow(b-mean,2),0)/varSignals.length; var conf=Math.max(50,Math.min(98,60+Math.log10(len+1)*8+Math.sqrt(variance)/10)); return {ai:Math.round(ai), conf:Math.round(conf), subs}; }
    window.hvaiCompute=function(text){ try{ var r=detectUltra(text||''); confLocked=true; updateHVAIScore(r.ai); updateBars(r.subs); setConfidence(r.conf); showImprovements(r.subs); return r; }catch(e){ console.warn('hvaiCompute error',e); return null; } };

    // Force-inject Prism wheel markup if old wheel is present
    (function ensureWheel(){
      var wrap=document.querySelector('#hvai.hvai-v26 .wheel');
      if(!wrap) return;
      var hasPrism = !!wrap.querySelector('.prism-wheel');
      if(!hasPrism){
        wrap.innerHTML = '<div class="prism-wheel" id="prismWheel" style="--p:0; --thick:14px;"><div class="track"></div><div class="arc"></div><div class="pointer"></div><div class="center"><div class="kv"><div class="row"><span class="dot ai"></span> AI‑like <span class="val"><span id="hvaiAIVal">0</span>%</span></div><div class="row"><span class="dot h"></span> Human‑like <span class="val"><span id="hvaiHumanVal">0</span>%</span></div></div></div></div>';
      }
    })();

    // init
    updateHVAIScore(0);
  })();
  </script>
</section>








    <!-- 2) READABILITY INSIGHTS (Upgraded) -->
    <section class="readability" id="readabilityPanel" style="display:none">
      <div class="read-head">
        <i class="fa-solid fa-book-open-reader ico ico-cyan"></i>
        <h4>Readability Insights</h4>
      </div>
      <div class="read-summary">
        <span class="read-chip" id="readChip">
          <i class="fa-solid fa-graduation-cap"></i>
          <span id="readGradeChip">Grade —</span>
        </span>
        <div class="read-caption" id="readSummary">We’ll estimate the reading level and show what to fix.</div>
      </div>

      <div class="read-grid">
        <div class="read-card">
          <div class="metric"><span><i class="fa-solid fa-face-smile"></i> Flesch Reading Ease</span><b id="mFlesch">—</b></div>
          <div class="meter"><span id="mFleschBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span><i class="fa-solid fa-align-left"></i> Avg Sentence Length</span><b id="mASL">—</b></div>
          <div class="meter"><span id="mASLBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span><i class="fa-solid fa-font"></i> Words</span><b id="mWords">—</b></div>
          <div class="meter"><span id="mWordsBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span><i class="fa-solid fa-language"></i> Syllables / Word</span><b id="mSPW">—</b></div>
          <div class="meter"><span id="mSPWBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span><i class="fa-solid fa-shuffle"></i> Lexical Diversity (TTR)</span><b id="mTTR">—</b></div>
          <div class="meter"><span id="mTTRBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span><i class="fa-solid fa-repeat"></i> Repetition (tri-gram)</span><b id="mRep">—</b></div>
          <div class="meter"><span id="mRepBar"></span></div>
        </div>
        <div class="read-card">
          <div class="metric"><span><i class="fa-solid fa-hashtag"></i> Digits / 100 words</span><b id="mDigits">—</b></div>
          <div class="meter"><span id="mDigitsBar"></span></div>
        </div>
      </div>

      <div class="read-suggest">
        <div class="title"><i class="fa-solid fa-lightbulb"></i> Simple Fixes</div>
        <ul id="readSuggest"></ul>
      </div>

      <div class="read-plain">
        <div class="title"><i class="fa-solid fa-child-reaching"></i> Easy to read (Grade 7)</div>
        <div id="readPlain">We’ll write a friendly one-line summary here.</div>
      </div>
    </section>

    <!-- 3) ENTITIES & TOPICS (Upgraded) -->
    <section class="entities" id="entitiesPanel" style="display:none">
      <div class="entities-head">
        <i class="fa-solid fa-database ico ico-cyan"></i>
        <h4>Entities & Topics</h4>
      </div>
      <div class="entity-groups">
        <div class="entity-card">
          <div class="entity-title"><i class="fa-solid fa-person"></i> People</div>
          <div class="entity-chips" id="entPeople"></div>
        </div>
        <div class="entity-card">
          <div class="entity-title"><i class="fa-solid fa-building"></i> Organizations</div>
          <div class="entity-chips" id="entOrgs"></div>
        </div>
        <div class="entity-card">
          <div class="entity-title"><i class="fa-solid fa-location-dot"></i> Places</div>
          <div class="entity-chips" id="entPlaces"></div>
        </div>
        <div class="entity-card">
          <div class="entity-title"><i class="fa-solid fa-tags"></i> Topics</div>
          <div class="entity-chips" id="entTopics"></div>
        </div>
        <div class="entity-card">
          <div class="entity-title"><i class="fa-solid fa-microchip"></i> Software / APK</div>
          <div class="entity-chips" id="entSoftware"></div>
        </div>
        <div class="entity-card">
          <div class="entity-title"><i class="fa-solid fa-gamepad"></i> Games</div>
          <div class="entity-chips" id="entGames"></div>
        </div>
      </div>
    </section>

    <!-- 4) SITE SPEED & CORE WEB VITALS (End) -->
    <section class="psi" id="psiPanel" style="display:none">
      <div class="psi-head">
        <i class="fa-solid fa-gauge-simple-high ico ico-cyan"></i>
        <h4>Site Speed & Core Web Vitals</h4>
      </div>
      <div class="psi-meta">
        <span class="psi-chip"><i class="fa-solid fa-mobile-screen-button"></i> Strategy: <b id="psiStrategy">mobile</b></span>
        <span class="psi-chip"><i class="fa-solid fa-chart-simple"></i> Performance: <b id="psiPerf">—</b></span>
      </div>

      <div class="psi-grid">
        <div class="psi-card">
          <div class="metric"><span><i class="fa-solid fa-stopwatch-20"></i> LCP (s)</span><b id="psiLcp">—</b></div>
          <div class="psi-meter"><span id="psiLcpBar"></span></div>
        </div>
        <div class="psi-card">
          <div class="metric"><span><i class="fa-solid fa-arrow-pointer"></i> INP (ms)</span><b id="psiInp">—</b></div>
          <div class="psi-meter"><span id="psiInpBar"></span></div>
        </div>
        <div class="psi-card">
          <div class="metric"><span><i class="fa-solid fa-expand"></i> CLS</span><b id="psiCls">—</b></div>
          <div class="psi-meter"><span id="psiClsBar"></span></div>
        </div>
        <div class="psi-card">
          <div class="metric"><span><i class="fa-solid fa-rocket"></i> TTFB (ms)</span><b id="psiTtfb">—</b></div>
          <div class="psi-meter"><span id="psiTtfbBar"></span></div>
        </div>
      </div>

      <div class="psi-issues">
        <div class="title"><i class="fa-solid fa-screwdriver-wrench"></i> How to Improve</div>
        <ul id="psiAdvice"></ul>
      </div>
      <div id="psiNote" style="color:var(--text-dim);margin-top:.4rem"></div>
    </section>

    <!-- Checklist categories (unchanged) -->
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

  // NEW: backend multi-detector (works with or without API keys; server may compute local ensemble)
  async function fetchDetect(text, url){
    try{
      const r = await fetch((window.SEMSEO.ENDPOINTS.detect || '/api/detect'),{
        method:'POST',
        headers:{
          'Accept':'application/json',
          'Content-Type':'application/json',
          'X-Requested-With':'XMLHttpRequest',
          'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ text, url })
      });
      if(!r.ok) return null;
      const j = await r.json();
      return (j && j.ok) ? j : null;
    }catch(_){ return null; }
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

  /* ===================== Stylometry & Readability ===================== */
  function clamp(v,min,max){ return v<min?min:(v>max?max:v); }
  function _countSyllables(word){
    var w=(word||'').toLowerCase().replace(/[^a-z]/g,''); if(!w) return 0;
    var m=(w.match(/[aeiouy]+/g)||[]).length; if(/(ed|es)$/.test(w)) m--; if(/^y/.test(w)) m--; return Math.max(1,m);
  }
  function _syllableStats(text){
    var wordRe=/[A-Za-z\u00C0-\u024f']+/g;
    var words=(text.match(wordRe)||[]);
    var syll=0;
    for(var i=0;i<words.length;i++){ syll += _countSyllables(words[i]); }
    var spw = words.length ? (syll/words.length) : 0;
    return { syllables: syll, spw: spw, words: words.length };
  }
  function _flesch(text){
    var sents = (text.match(/[.!?]+/g)||[]).length || 1;
    var words = (text.match(/[A-Za-z\u00C0-\u024f']+/g)||[]); var wN = words.length||1;
    var syll = 0; for(var i=0;i<words.length;i++){ syll += _countSyllables(words[i]); }
    return clamp(206.835 - 1.015*(wN/sents) - 84.6*(syll/wN), -20, 120);
  }
  function _fkGradeLevel(text){
    var sents = (text.match(/[.!?]+/g)||[]).length || 1;
    var st = _syllableStats(text);
    var words = st.words || 1;
    var grade = 0.39 * (words / sents) + 11.8 * (st.spw || 0) - 15.59;
    return Math.max(0, Math.min(18, grade));
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
    var asl=mean||0;
    return { text, wordCount:tokens, flesch:_flesch(text), cov, longRatio, triRepeatRatio: triT?triR/triT:0, TTR, hapaxRatio: types?hapax/types:0, avgWordLen:avgLen, digitsPer100:digits, asl: asl };
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

  
  
/* === HVAI v2 Gauge logic === */
(function(){
  function clamp(n,min=0,max=100){ return Math.max(min, Math.min(max, n||0)); }

  function computeEnsembleV2(res){
    const human = Number(res?.humanPct ?? 0);
    const ai    = Number(res?.aiPct ?? (res?.aiLikePct ?? 0));
    const sty   = Number(res?.stylometry ?? 50); // neutral
    const score = 0.7*human + 0.2*(100 - ai) + 0.1*(100 - Math.min(100, Math.max(0, sty)));
    return clamp(Math.round(score));
  }

  function paintGauge(score){
    const circ = 2*Math.PI*48;
    const s = clamp(score);
    const prog = document.querySelector('.neon-gauge .prog');
    const num  = document.getElementById('hvaiScore');
    const msg  = document.getElementById('hvaiMsg');
    if (!prog) return;
    prog.style.strokeDasharray = String(circ);
    prog.style.strokeDashoffset = String(circ - (circ*s/100));
    prog.classList.remove('good','mid','bad');
    if (s >= 80){ prog.classList.add('good'); msg && (msg.textContent = 'Great work — looks human!'); celebrate(); }
    else if (s >= 60){ prog.classList.add('mid'); msg && (msg.textContent = 'Pretty close — a few tweaks.'); }
    else { prog.classList.add('bad'); msg && (msg.textContent = 'Red zone — sounds AI-ish.'); }
    if (num) num.textContent = s;
  }

  function celebrate(){
    const root = document.querySelector('.neon-gauge .confetti');
    if (!root) return;
    root.innerHTML = '';
    const colors = ['#22c55e','#a78bfa','#60a5fa','#f59e0b','#f43f5e','#34d399'];
    for(let i=0;i<24;i++){
      const p = document.createElement('i');
      const x = Math.random()*100, y = 50+Math.random()*10;
      p.style.left = x+'%'; p.style.top = y+'%';
      p.style.background = colors[Math.floor(Math.random()*colors.length)];
      p.style.transform += ` rotate(${Math.floor(Math.random()*360)}deg)`;
      p.style.animationDelay = (Math.random()*0.3)+'s';
      root.appendChild(p);
    }
    setTimeout(()=>{ root.innerHTML=''; }, 2000);
  }

  window.HVAI_V2 = {
    compute: computeEnsembleV2,
    paint: paintGauge,
    update: function(res){
      try{
        const s = computeEnsembleV2(res||{});
        paintGauge(s);
        const h = document.getElementById('metaHuman'); if(h) h.textContent = ((res?.humanPct??0)|0)+'%';
        const a = document.getElementById('metaAI');    if(a) a.textContent = ((res?.aiPct??res?.aiLikePct??0)|0)+'%';
        const c = document.getElementById('metaConf');  if(c) c.textContent = (res?.confidence? Math.round(res.confidence*100):'—');
      }catch(e){}
    }
  };
})();

/* === Human vs AI rendering === */
  function renderDetectors(res){
  try{ if (window.HVAI_V2) window.HVAI_V2.update(res); }catch(_){}
    var grid = document.getElementById('detGrid'); var confEl = document.getElementById('detConfidence');
    if(confEl) confEl.textContent = isFinite(res.confidence)? Math.round(res.confidence): '—';
    var hv = document.getElementById('hvaiHumanVal'), av=document.getElementById('hvaiAIVal');
    var hf = document.getElementById('hvaiHumanFill'), af=document.getElementById('hvaiAIFill');
    if(hv) hv.textContent = isFinite(res.humanPct)? Math.round(res.humanPct)+'%':'—%';
    if(av) av.textContent = isFinite(res.aiPct)? Math.round(res.aiPct)+'%':'—%';
    if(hf) hf.style.width = Math.max(0, Math.min(100, res.humanPct||0)) + '%';
    if(af) af.style.width = Math.max(0, Math.min(100, res.aiPct||0)) + '%';
    if (window.setHVAIScore) window.setHVAIScore(Math.round(res.humanPct||0));

    var panel = document.getElementById('detectorPanel'); if(panel) panel.style.display='block';
    if(!grid) return; grid.innerHTML = '';
    (res.detectors||[{key:'stylometry',label:'Stylometry',ai:res.aiPct||0}]).forEach(function(d){
      var id='det-'+d.key; var wrap=document.createElement('div');
      wrap.className='det-item'; wrap.innerHTML =
        '<div class="det-row"><div class="det-label">'+d.label+'</div><div class="det-score" id="'+id+'-score">'+(d.ai||0)+'</div></div>'+
        '<div class="det-bar"><div class="det-fill" id="'+id+'-fill" style="width:'+(clamp(d.ai||0,0,100))+'%"></div></div>';
      grid.appendChild(wrap);
    });
  }
  function applyDetection(humanPct, aiPct, confidence, breakdown){
    var writer = (isFinite(humanPct) && isFinite(aiPct) && humanPct>=aiPct) ? 'Likely Human' : 'AI-like';
    var badge = document.getElementById('aiBadge'); if (badge){ var b=badge.querySelector('b'); if(b) b.textContent = writer; badge.title = 'Confidence: ' + (confidence? confidence+'%':'—'); }
    var hp = document.getElementById('humanPct'), ap = document.getElementById('aiPct');
    if(hp) hp.textContent = isFinite(humanPct)? Math.round(humanPct) : '—';
    if(ap) ap.textContent = isFinite(aiPct)?    Math.round(aiPct)   : '—';
    var res = {humanPct:humanPct, aiPct:aiPct, confidence:confidence, detectors:(breakdown && breakdown.detectors)||[{key:'stylometry',label:'Stylometry',ai:aiPct||0}]};
    renderDetectors(res);
  }

  /* === Readability rendering === */
  function renderReadability(s){
    var p = document.getElementById('readabilityPanel'); if(!p) return;
    var text = s.text || '';
    var grade = _fkGradeLevel(text), gradeInt=Math.round(grade);
    var syl = _syllableStats(text);
    var ease = s.flesch;
    var chip = document.getElementById('readChip');
    var chipText = document.getElementById('readGradeChip');
    if (chipText) chipText.textContent = 'Grade ' + gradeInt;
    if (chip){ chip.classList.remove('bad','mid'); if (gradeInt<=8){} else if (gradeInt<=10) chip.classList.add('mid'); else chip.classList.add('bad'); }
    var sum = document.getElementById('readSummary');
    if (sum){
      if (gradeInt <= 8) sum.textContent = 'Easy for most readers (middle school). Great for broad audiences.';
      else if (gradeInt <= 10) sum.textContent = 'Readable for teens. Consider simpler words & shorter sentences.';
      else sum.textContent = 'Complex reading level. Use shorter sentences and simpler vocabulary.';
    }
    function bar(id, v, max){ var el=document.getElementById(id); if(!el) return; el.style.width = Math.max(0, Math.min(100, (v/max)*100)) + '%'; }
    setText('mFlesch', Math.round(ease));
    setText('mWords', s.wordCount);
    setText('mASL', s.asl ? s.asl.toFixed(1) : '—');
    setText('mTTR', s.TTR ? (s.TTR*100).toFixed(0)+'%' : '—');
    setText('mRep', s.triRepeatRatio ? Math.round(s.triRepeatRatio*100)+'%' : '—');
    setText('mDigits', s.digitsPer100 ? Math.round(s.digitsPer100) : 0);
    setText('mSPW', syl.spw ? syl.spw.toFixed(2) : '—');
    bar('mFleschBar', Math.max(0, Math.min(100, ease)), 100);
    bar('mWordsBar', Math.min(s.wordCount, 4000), 4000);
    bar('mASLBar', Math.max(0, 30 - (s.asl||0)), 30);
    bar('mTTRBar', Math.max(0, Math.min(1, s.TTR||0)), 1);
    bar('mRepBar', Math.max(0, 1 - (s.triRepeatRatio||0)), 1);
    bar('mDigitsBar', Math.max(0, Math.min(20, 20 - (s.digitsPer100||0))), 20);
    bar('mSPWBar', Math.max(0, Math.min(1.8, 1.8 - (syl.spw||0))), 1.8);
    var fixes = [];
    if ((s.asl||0) > 20) fixes.push('Break long sentences into 12–16 words.');
    if ((syl.spw||0) > 1.60) fixes.push('Prefer shorter words (use simpler synonyms).');
    if ((s.TTR||0) < 0.35) fixes.push('Use more varied vocabulary (avoid repeating the same words).');
    if ((s.triRepeatRatio||0) > 0.10) fixes.push('Remove repeated phrases; keep each idea unique.');
    if ((s.digitsPer100||0) > 10) fixes.push('Reduce numeric density; round or group numbers where possible.');
    if (ease < 60 && fixes.length === 0) fixes.push('Aim for shorter sentences and simpler vocabulary to improve readability.');
    var list = document.getElementById('readSuggest');
    if (list){ list.innerHTML = fixes.length ? fixes.map(f=>`<li>${f}</li>`).join('') : '<li>Looks good! Keep sentences concise and headings clear.</li>'; }
    var plain = document.getElementById('readPlain');
    if (plain){
      if (gradeInt <= 7){
        plain.textContent = 'This page is easy to read for a Grade-7 reader: short sentences, common words, and clear ideas.';
      } else if (gradeInt <= 9){
        plain.textContent = 'Almost Grade-7 friendly. To make it easier, use shorter sentences and everyday words.';
      } else {
        plain.textContent = 'Currently above Grade-7 level. Try smaller sentences, simpler words, and fewer complex clauses.';
      }
    }
    p.style.display='block';
  }

  /* === Entities & Topics extraction === */
  function extractEntities(text){
    var res = {people:[], orgs:[], places:[], topics:[], software:[], games:[]};
    var clean=(text||'').replace(/\s+/g,' ');
    // naive capitalized tokens as candidates
    var cand = (clean.match(/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+){0,3})\b/g) || []).slice(0, 800);
    var stop = new Set(['The','A','An','This','That','And','Or','Of','In','On','To','For','By','With','Your','Our','You','We','It','At','From','As','Be','Is','Are','Was','Were','Not']);
    var uniq={};
    cand.forEach(function(c){ if(stop.has(c)) return; var k=c.trim(); if(k.length<2||k.length>48) return; uniq[k]=1; });
    var uniqList = Object.keys(uniq).slice(0,120);

    // very light heuristics
    uniqList.forEach(function(n){
      if (/\b(Inc|LLC|Ltd|Corporation|Company|Corp|Studio|Labs|University|College)\b/.test(n)) res.orgs.push(n);
      else if (/\b(City|Town|Province|State|Country|Park|River|Lake|Valley|Mountain)\b/.test(n)) res.places.push(n);
      else if (/\b(Mr|Mrs|Ms|Dr|Prof)\b/.test(n) || n.split(' ').length>=2) res.people.push(n);
      else res.topics.push(n);
    });

    // software / apk / games (keyword probes)
    var low = clean.toLowerCase();
    var swTerms = (low.match(/\b(software|app|application|android|ios|windows|mac|linux|apk|exe|download|install|update|version)\b/g) || []);
    if (swTerms.length){ // pick key tokens with dots or version-like
      var soft = (clean.match(/\b([A-Z][A-Za-z0-9\.\-\+]{2,})\b/g) || []).filter(x=>/\b(Android|iOS|Windows|Mac|Linux|Pro|Studio|Editor|App|SDK|Tool)\b/.test(x) || /v?\d+\.\d+/.test(x));
      res.software = Array.from(new Set(soft)).slice(0,20);
    }
    if (/\bapk\b/i.test(low) || /\.apk\b/i.test(low)){ res.software.push('APK'); }
    var games = (clean.match(/\b([A-Z][A-Za-z0-9\-\s]{2,} (?:Game|Games|Edition|Remastered|Online))\b/g) || []);
    if (games.length) res.games = Array.from(new Set(games)).slice(0,20);

    // clamp lists
    res.people = res.people.slice(0,20);
    res.orgs = res.orgs.slice(0,20);
    res.places = res.places.slice(0,20);
    res.topics = res.topics.slice(0,24);
    return res;
  }
  function chipify(list, cls){
    if(!list || !list.length) return '<span class="echip misc"><i class="fa-solid fa-circle-minus"></i> none</span>';
    return list.map(v=>`<span class="echip ${cls||'misc'}"><i class="fa-solid fa-tag"></i> ${v}</span>`).join(' ');
  }
  function renderEntitiesTopics(sample){
    var p = document.getElementById('entitiesPanel'); if(!p) return;
    var ex = extractEntities(sample||'');
    var m = (id, html)=>{ var el=document.getElementById(id); if(el) el.innerHTML=html; };
    m('entPeople', chipify(ex.people,'person'));
    m('entOrgs', chipify(ex.orgs,'org'));
    m('entPlaces', chipify(ex.places,'place'));
    m('entTopics', chipify(ex.topics,'misc'));
    m('entSoftware', chipify(ex.software,'sw'));
    m('entGames', chipify(ex.games,'game'));
    p.style.display='block';
  }

  /* === PSI (Site Speed) via server proxy === */
  async function startSiteSpeed(url, strategy='mobile'){
    var panel = document.getElementById('psiPanel'); if(!panel) return;
    panel.style.display='block';
    setText('psiStrategy', strategy);
    setText('psiPerf','—'); setText('psiLcp','—'); setText('psiInp','—'); setText('psiCls','—'); setText('psiTtfb','—');
    ['psiLcpBar','psiInpBar','psiClsBar','psiTtfbBar'].forEach(id=>{ var el=document.getElementById(id); if(el) el.style.width='0%'; });
    var note = document.getElementById('psiNote'); if(note) note.textContent='Running PageSpeed Insights…';
    var advice = document.getElementById('psiAdvice'); if(advice) advice.innerHTML='';

    try{
      const q = new URLSearchParams({url, strategy}).toString();
      const r = await fetch((window.SEMSEO.ENDPOINTS.psi||'/api/psi')+'?'+q, {headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}});
      const j = await r.json();
      if(!j || j.ok===false){ throw new Error(j && j.error ? j.error : 'PSI proxy error'); }
      const lhr = j.data?.lighthouseResult || {};
      const audits = lhr.audits || {};
      const catPerf = lhr.categories?.performance?.score;
      const perfScore = typeof catPerf==='number' ? Math.round(catPerf*100) : '—';
      setText('psiPerf', perfScore);

      function barPct(id, val, goodMax, clampMax){
        var el=document.getElementById(id); if(!el) return;
        var v = Math.max(0, Math.min(clampMax, val||0));
        var pct = Math.max(0, Math.min(100, (v/goodMax)*100));
        el.style.width = pct + '%';
      }
      // LCP in seconds
      var lcp = audits['largest-contentful-paint']?.numericValue; // ms
      var inp = audits['interactive']?.numericValue; // ms (fallback), or 'experimental-interaction-to-next-paint'
      var inpAlt = audits['experimental-interaction-to-next-paint']?.numericValue;
      if (inpAlt) inp = inpAlt;
      var cls = audits['cumulative-layout-shift']?.numericValue;
      var ttfb = audits['server-response-time']?.numericValue || audits['time-to-first-byte']?.numericValue;

      if (typeof lcp==='number'){ setText('psiLcp', (lcp/1000).toFixed(2)); barPct('psiLcpBar', (lcp/1000), 2.5, 6); }
      if (typeof inp==='number'){ setText('psiInp', Math.round(inp)); barPct('psiInpBar', inp, 200, 600); }
      if (typeof cls==='number'){ setText('psiCls', cls.toFixed(3)); barPct('psiClsBar', cls, 0.1, 0.4); }
      if (typeof ttfb==='number'){ setText('psiTtfb', Math.round(ttfb)); barPct('psiTtfbBar', ttfb, 800, 2500); }

      // Advice list (simple heuristics)
      var tips=[];
      if (lcp>2500) tips.push('Optimize hero image (compress, proper size, lazy-load below-the-fold).');
      if (inp>200) tips.push('Reduce main-thread work (code-split, defer non-critical JS).');
      if (cls>0.1) tips.push('Reserve space for images/ads; avoid late-loading fonts without fallback.');
      if (ttfb>800) tips.push('Improve server response (caching, CDN, database/index tuning).');
      if (!tips.length) tips.push('Looks good! Keep images optimized, JS lean, and layout stable.');
      if (advice) advice.innerHTML = tips.map(t=>`<li>${t}</li>`).join('');

      if (note) note.textContent = 'Results from Google PageSpeed Insights (via secure server proxy).';
    }catch(e){
      if (note) note.textContent = 'PSI error: ' + (e && e.message ? e.message : e);
    }
  }

  /* ===================== ANALYZE ===================== */
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
    var entPanel = document.getElementById('entitiesPanel'); if(entPanel) entPanel.style.display='none';
    var psiPanel = document.getElementById('psiPanel'); if(psiPanel) psiPanel.style.display='none';

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

    // 5) Local detection (prep) + try backend detector FIRST
    var ensemble = sample && sample.length>30 ? detectUltra(sample) : null;
    var backendDetect = null;
    if (sample && sample.length > 30) {
      backendDetect = await fetchDetect(sample, url);
    }

    // 6) Scores -> guarantee + UI
    data = ensureScoresExist(data, sample, ensemble);

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

    // Detection (prefer backend multi-detector; fallback to local)
    var detNote = document.getElementById('detNote');
    if (backendDetect) {
      applyDetection(backendDetect.humanPct, backendDetect.aiPct, backendDetect.confidence, backendDetect);
      if (detNote) detNote.textContent = 'Source: backend multi-detector (ZeroGPT/GPTZero/OriginalityAI if configured; otherwise local on server).';
    } else if (ensemble) {
      applyDetection(ensemble.humanPct, ensemble.aiPct, ensemble.confidence, ensemble);
      if (detNote) detNote.textContent = 'Source: local ensemble (no external APIs).';
    } else {
      var hp = (typeof data.humanPct==='number')? data.humanPct : NaN;
      var ap = (typeof data.aiPct==='number')? data.aiPct : NaN;
      var backendConf = (typeof data.confidence==='number')? data.confidence : 60;
      if (isFinite(hp) && isFinite(ap)) {
        applyDetection(hp, ap, backendConf, null);
        if (detNote) detNote.textContent = 'Source: backend (partial)'; 
      }
    }

    // Readability + Entities
    var S = (ensemble && ensemble._s) ? ensemble._s : _prep(sample||'');
    renderReadability(S);
    renderEntitiesTopics(sample||'');

    // Checklist scores + autotick
    window.autoTickByScores(data.itemScores || {});

    if (window.Water) window.Water.finish();
    if (statusEl) statusEl.textContent = 'Analysis complete';
    if (report) report.style.display = 'block';

    // Auto-start PSI at the end
    startSiteSpeed(url,'mobile');

    window.SEMSEO.BUSY = false;
    if (window.SEMSEO.QUEUE > 0){ window.SEMSEO.QUEUE = 0; }
  }
  window.analyze = analyze;

  // Events
  document.addEventListener('DOMContentLoaded', function(){
    try{
      var btn = document.getElementById('analyzeBtn');
      if (btn){ btn.addEventListener('click', function(e){ e.preventDefault(); analyze(); }); }
      var input = document.getElementById('analyzeUrl');
      if (input){ input.addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); analyze(); }}); }
      var clr = document.getElementById('clearUrl'); if(clr && input){ clr.onclick=function(){ input.value=''; input.focus(); }; }
      var pst = document.getElementById('pasteUrl'); if(pst && input && navigator.clipboard){ pst.onclick=async function(){ try{ var t=await navigator.clipboard.readText(); if(t){ input.value=t.trim(); } }catch(e){} }; }

      window.SEMSEO.READY = true;
      if (window.SEMSEO.QUEUE>0){ window.SEMSEO.QUEUE=0; analyze(); }
    }catch(err){
      var s=document.getElementById('analyzeStatus'); if(s) s.textContent='Boot error: '+err.message;
    }
  });

})();
</script>

<!-- B) Non-critical UI -->
<script>
try{
  // Hue drift
  (function(){ var root=document.documentElement; var start=performance.now(); function frame(now){ root.style.setProperty('--hue', (((now-start)/4)%360) + 'deg'); requestAnimationFrame(frame);} requestAnimationFrame(frame); })();

  // Share links
  (function(){
    var url = encodeURIComponent(location.href), title = encodeURIComponent(document.title);
    var fb = document.getElementById('shareFb'), x = document.getElementById('shareX'), ln = document.getElementById('shareLn'), wa = document.getElementById('shareWa'), em = document.getElementById('shareEm');
    if(fb) fb.href = 'https://www.facebook.com/sharer/sharer.php?u='+url;
    if(x)  x.href  = 'https://twitter.com/intent/tweet?text='+title+'&url='+url;
    if(ln) ln.href = 'https://www.linkedin.com/sharing/share-offsite/?url='+url;
    if(wa) wa.href = 'https://wa.me/?text='+title+'%20'+url;
    if(em) em.href = 'mailto:?subject='+title+'&body='+url;
  })();

  // Reset / Export / Import / Print / UI misc
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
      var entPanel=document.getElementById('entitiesPanel'); if(entPanel){ entPanel.style.display='none'; }
      var psiPanel=document.getElementById('psiPanel'); if(psiPanel){ psiPanel.style.display='none'; }
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

<!-- C) Background: tech lines + smoke -->
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

<script>
/* === HVAI v2: i18n + Suggestions === */
(function(){
  const I18N = {
    en: {
      great: "Great work — looks human!",
      mid: "Pretty close — a few tweaks.",
      bad: "Red zone — sounds AI-ish.",
      suggestions: "Suggestions",
      keep: "Keep going — you’re on the right track.",
      s_ai_high: ["Vary sentence length and rhythm.","Add concrete details: dates, names, domain terms.","Trim generic fillers and hedge words.","Use idioms or locally flavored phrases."],
      s_style_flat: ["Break long paragraphs into 2–3 lines.","Add a brief story or example.","Mix short and long sentences for cadence."],
      s_repetitive: ["Replace repeated words with synonyms.","Swap predictable transitions for fresher ones."],
    },
    pt: {
      great: "Ótimo trabalho — parece humano!",
      mid: "Quase lá — alguns ajustes.",
      bad: "Zona vermelha — soa artificial.",
      suggestions: "Sugestões",
      keep: "Continue — está no caminho certo.",
      s_ai_high: ["Varie o comprimento das frases e o ritmo.","Inclua detalhes concretos: datas, nomes e termos do domínio.","Remova preenchimentos genéricos.","Use expressões locais/idiomáticas."],
      s_style_flat: ["Divida parágrafos longos.","Adicione um exemplo curto ou história.","Alterne frases curtas e longas."],
      s_repetitive: ["Troque repetições por sinônimos.","Evite conectivos previsíveis."],
    },
    ar: {
      great: "عمل رائع — يبدو بشريًا!",
      mid: "قريب جدًا — بعض اللمسات فقط.",
      bad: "منطقة حمراء — يبدو آليًا.",
      suggestions: "اقتراحات",
      keep: "استمر — أنت على المسار الصحيح.",
      s_ai_high: ["نوِّع طول الجمل وإيقاعها.","أضِف تفاصيل ملموسة: تواريخ، أسماء، مصطلحات.","احذف العبارات العامة المتكررة.","استخدم تعابير محلية دارجة."],
      s_style_flat: ["قسّم الفقرات الطويلة.","أدرج مثالًا قصيرًا أو قصة.","نوِّع بين الجمل القصيرة والطويلة."],
      s_repetitive: ["استبدل الكلمات المكررة بمرادفات.","غيّر أدوات الربط المتوقعة."],
    }
  };

  function setPanelDir(lang){
    const hvai = document.querySelector('.hvai');
    if (!hvai) return;
    hvai.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
  }

  function suggest(res, lang){
    const L = I18N[lang] || I18N.en;
    const arr = [];
    const ai = Number(res?.aiPct ?? 0);
    const human = Number(res?.humanPct ?? 0);
    const style = Number(res?.stylometry ?? 50);
    if (ai > 40) arr.push(...L.s_ai_high.slice(0,2));
    if (style > 55 || style < 25) arr.push(...L.s_style_flat.slice(0,2));
    if (human < 70) arr.push(...L.s_repetitive.slice(0,2));
    if (!arr.length) arr.push(L.keep);
    return { title: L.suggestions, items: arr.slice(0,4) };
  }

  // Re-wrap HVAI_V2 update so text is localized and suggestions generated
  function wrapHVAI(){
    if (!window.HVAI_V2) return;
    const _compute = window.HVAI_V2.compute;
    const _paint   = window.HVAI_V2.paint;
    const _update  = window.HVAI_V2.update;
    window.HVAI_V2.update = function(res){
      window.__lastDet = res;
      const sel  = document.getElementById('hvaiLang');
      const lang = sel ? sel.value : 'en';
      setPanelDir(lang);

      // compute + paint (but replace message string to localized)
      const score = _compute ? _compute(res||{}) : 0;
      if (_paint) _paint(score);
      const L = I18N[lang] || I18N.en;
      const msg = document.getElementById('hvaiMsg');
      if (msg){
        msg.textContent = (score >= 80 ? L.great : score >= 60 ? L.mid : L.bad);
      }

      // meta chips
      try{
        const h = document.getElementById('metaHuman'); if(h) h.textContent = ((res?.humanPct??0)|0)+'%';
        const a = document.getElementById('metaAI');    if(a) a.textContent = ((res?.aiPct??res?.aiLikePct??0)|0)+'%';
        const c = document.getElementById('metaConf');  if(c) c.textContent = (res?.confidence? Math.round(res.confidence*100):'—');
      }catch(e){}

      // suggestions
      const box = suggest(res||{}, lang);
      const t = document.getElementById('hvaiSugTitle'); if (t) t.textContent = box.title;
      const list = document.getElementById('hvaiSugList');
      if (list){ list.innerHTML = box.items.map(x=>`<li>${x}</li>`).join(''); }

      // call original for any downstream work
      if (_update) try{ _update(res); }catch(e){}
    };
  }

  document.addEventListener('DOMContentLoaded', wrapHVAI);
})();
</script>
  


<!-- Checklist Suggest Patch -->
<style>

/* checklist-suggest-patch-v1.css */
.gc-suggest{
  background: rgba(20,23,36,.98);
  border: 1px solid rgba(255,255,255,.12);
  border-radius: 10px;
  box-shadow: 0 8px 22px rgba(0,0,0,.45);
  max-height: 320px;
  overflow: auto;
  padding: 6px;
}
.gc-suggest__item{
  padding: 8px 10px;
  border-radius: 8px;
  cursor: pointer;
  font: 500 14px/1.3 Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial;
  color: #EAF1FF;
}
.gc-suggest__item:hover,
.gc-suggest__item.is-active{
  background: linear-gradient(90deg, #00ffc822, #6bf7ff22);
}

</style>
<script>

/*! checklist-suggest-patch-v1
 * Lightweight Google Suggest hookup for a single input (Checklist Improve).
 * - Renders dropdown in <body> (portal) to bypass overflow/z-index clipping.
 * - Debounced fetch from suggestqueries.google.com (Firefox client = JSON).
 * - Keyboard support: ↑/↓ navigate, Enter pick, Esc close.
 * - Autodetects the input using common selectors, but you can call attachSuggest(input) yourself.
 */
(function(){
  const S = Object.freeze({
    MAX: 8,
    HL: document.documentElement.lang || 'en',
    CLASS: 'gc-suggest',
    ITEM: 'gc-suggest__item',
    ACTIVE: 'is-active'
  });

  function debounce(fn, ms){ let t; return function(...a){ clearTimeout(t); t = setTimeout(()=>fn.apply(this,a), ms); }; }

  // Create dropdown attached to <body>
  function createPortal(){
    const el = document.createElement('div');
    el.className = S.CLASS;
    el.setAttribute('role','listbox');
    el.style.position = 'absolute';
    el.style.display = 'none';
    el.style.zIndex = '99999';
    document.body.appendChild(el);
    return el;
  }

  // Position the portal under the input
  function place(portal, input){
    const r = input.getBoundingClientRect();
    portal.style.left = Math.round(r.left + window.scrollX) + 'px';
    portal.style.top  = Math.round(r.bottom + window.scrollY) + 'px';
    portal.style.minWidth = Math.round(r.width) + 'px';
  }

  async function fetchSuggest(q){
    if(!q || !q.trim()){ return []; }
    const url = 'https://suggestqueries.google.com/complete/search?client=firefox&hl=' + encodeURIComponent(S.HL) + '&q=' + encodeURIComponent(q.trim());
    try{
      const rs = await fetch(url, { mode:'cors', cache:'no-store' });
      const data = await rs.json();
      return Array.isArray(data) && Array.isArray(data[1]) ? data[1].slice(0,S.MAX) : [];
    }catch(e){
      // Network/CORS/CSP: fail silently
      return [];
    }
  }

  function renderList(portal, items, onPick){
    portal.innerHTML = '';
    items.forEach((t, i)=>{
      const li = document.createElement('div');
      li.className = S.ITEM;
      li.setAttribute('role','option');
      li.setAttribute('id', 'gc-opt-' + i);
      li.textContent = t;
      li.addEventListener('mousedown', (ev)=>{ ev.preventDefault(); onPick(t); hide(portal); });
      portal.appendChild(li);
    });
    portal.style.display = items.length ? 'block' : 'none';
  }

  function hide(portal){ portal.style.display='none'; portal.innerHTML=''; activeIndex=-1; }

  let portal = null, activeIndex = -1;

  function attachSuggest(input){
    if(!input) return;
    if(!portal) portal = createPortal();

    const deb = debounce(async function(){
      const q = input.value;
      const items = await fetchSuggest(q);
      place(portal, input);
      renderList(portal, items, (text)=>{
        input.value = text;
        input.dispatchEvent(new Event('input', {bubbles:true}));
      });
    }, 180);

    input.setAttribute('autocomplete','off');
    input.setAttribute('aria-controls','gc-suggest');
    input.addEventListener('input', deb);
    input.addEventListener('focus', ()=>{ place(portal, input); deb(); });
    window.addEventListener('resize', ()=> portal && portal.style.display!=='none' && place(portal, input));
    window.addEventListener('scroll', ()=> portal && portal.style.display!=='none' && place(portal, input), true);

    input.addEventListener('keydown', (e)=>{
      const items = portal.querySelectorAll('.' + S.ITEM);
      if(!items.length) return;
      if(e.key === 'ArrowDown'){ e.preventDefault(); activeIndex = (activeIndex+1) % items.length; updateActive(items); }
      else if(e.key === 'ArrowUp'){ e.preventDefault(); activeIndex = (activeIndex-1+items.length) % items.length; updateActive(items); }
      else if(e.key === 'Enter'){ if(activeIndex>=0){ e.preventDefault(); items[activeIndex].dispatchEvent(new Event('mousedown')); } }
      else if(e.key === 'Escape'){ hide(portal); }
    });

    document.addEventListener('click', (ev)=>{
      if(!portal || ev.target===portal || portal.contains(ev.target) || ev.target===input) return;
      hide(portal);
    });
  }

  function updateActive(items){
    items.forEach((el,i)=> el.classList.toggle(S.ACTIVE, i===activeIndex));
    if(activeIndex>=0){
      const el = items[activeIndex];
      el.scrollIntoView({ block:'nearest' });
    }
  }

  // Auto attach using likely selectors
  const candidates = [
    '#checklistImprove', '#checklist-improve', '#improveChecklist', '#improveChecklistInput',
    '[data-role="checklist-improve"]', 'input[placeholder*="improve" i]', 'input[placeholder*="checklist" i]'
  ];
  let input = null;
  for(const sel of candidates){ input = document.querySelector(sel); if(input) break; }
  if(input){ attachSuggest(input); }

  // expose global attach if needed
  window.attachChecklistSuggest = attachSuggest;
})();

</script>


<!-- ===== Checklist Improve Modal (lightweight, no deps) ===== -->
<style>
  .cl-modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9999}
  .cl-modal.open{display:flex}
  .cl-back{position:absolute;inset:0;background:rgba(4,8,22,.6);backdrop-filter:blur(4px)}
  .cl-card{position:relative;max-width:780px;width:92%;background:linear-gradient(180deg,rgba(15,18,38,.96),rgba(15,18,38,.98));
    border:1px solid rgba(255,255,255,.12);box-shadow:0 20px 60px rgba(0,0,0,.45);border-radius:16px;padding:20px}
  .cl-title{font:800 18px/1.2 Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial;color:#eaf1ff;margin:0 0 10px}
  .cl-sub{font:500 13px/1.35 Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial;color:#9fb2ff;margin:0 0 14px}
  .cl-list{margin:0;padding-left:1.05rem;max-height:46vh;overflow:auto}
  .cl-list li{margin:.35rem 0;color:#eaf1ff}
  .cl-kicker{display:inline-flex;align-items:center;gap:.5rem;margin:.25rem 0 .65rem;color:#c2cffd;font-weight:700}
  .cl-close{position:absolute;top:10px;right:10px;border:1px solid rgba(255,255,255,.15);background:transparent;color:#eaf1ff;
    font:700 12px/1 Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial;padding:.4rem .55rem;border-radius:10px;cursor:pointer}
  .cl-close:hover{background:rgba(255,255,255,.08)}
</style>
<div id="clModal" class="cl-modal" aria-hidden="true" role="dialog" aria-label="Checklist improvement tips">
  <div class="cl-back" data-close="1"></div>
  <div class="cl-card" role="document">
    <button class="cl-close" type="button" data-close="1">Close ✕</button>
    <h3 class="cl-title" id="clTitle">Improve</h3>
    <div class="cl-sub" id="clScoreRow"></div>
    <ul class="cl-list" id="clList"></ul>
  </div>
</div>
<script>
(function(){
  // Category tips map (1..25). Keep concise, practical actions.
  const CAT_TIPS = {
    1: ['State the user intent in first 100 words', 'Answer the main question above the fold', 'Use one clear primary topic per page', 'Link to a hub page for this topic'],
    2: ['Research related terms (PAA, autosuggest, synonyms)', 'Add 3–5 supportive subheadings with related phrases', 'Include long-tail questions users ask', 'Avoid keyword stuffing—use natural wording'],
    3: ['Use one H1 only', 'Put the primary topic early in H1', 'Keep H1 ~45–70 chars and human-readable', 'Match H1 & page purpose'],
    4: ['Add 3–6 FAQ Q&As that reflect search intent', 'Answer each in 1–3 short sentences', 'Mark up with FAQPage schema if appropriate', 'Place FAQs near the end or in a sidebar'],
    5: ['Break text with short paragraphs (2–4 lines)', 'Use bullets, tables, and images with captions', 'Write in simple language and active voice', 'Add a TL;DR summary box'],
    6: ['Keep title 50–60 chars with target keyword', 'Add benefit or number (e.g., 7 tips)', 'Avoid truncation and clickbait', 'Make each title unique site‑wide'],
    7: ['Write a 140–160 char meta description', 'Include keyword + benefit + CTA', 'Avoid duplication across pages', 'Reflect the page’s actual content'],
    8: ['Add a canonical tag to preferred URL', 'Avoid self‑referencing canonicals if not needed', 'Fix duplicate parameter pages', 'Ensure only one canonical per page'],
    9: ['Include page in XML sitemap', 'Return 200 OK and allow indexing', 'Avoid noindex on important pages', 'Submit sitemap in Search Console'],
    10:['Show author name & bio with expertise', 'Add date/updated stamp', 'Cite trustworthy sources', 'Include About/Contact/Editorial policy links'],
    11:['Explain what’s unique vs. competitors', 'Add original insights, data, or examples', 'Use fresh screenshots or media', 'Cut thin or repetitive sections'],
    12:['Verify facts and dates; link citations', 'Use up‑to‑date sources (last 12–24 months)', 'Quote statistics precisely', 'Avoid dead or low‑quality links'],
    13:['Add at least 1–3 images or a short video', 'Compress and lazy‑load media', 'Use descriptive alt text and captions', 'Place media near related text'],
    14:['Organize H2/H3 logically into clusters', 'One idea per section; avoid orphan headings', 'Use table of contents for long pages', 'Cross‑link cluster pages'],
    15:['Add contextual internal links to hubs', 'Link from hubs back to this page', 'Use descriptive, natural anchor text', 'Fix orphan pages (no internal links)'],
    16:['Make slug short and readable (e.g., /topic-guide)', 'Avoid dates/IDs unless needed', 'Use hyphens, no stopwords when possible', 'Keep lowercase & canonicalized'],
    17:['Add breadcrumb navigation', 'Implement BreadcrumbList schema', 'Reflect logical site hierarchy', 'Show breadcrumbs above the H1'],
    18:['Use responsive layout & fluid images', 'Tap targets ≥ 44px on mobile', 'Avoid horizontal scroll', 'Test at 360–414px wide'],
    19:['Compress images (WebP/AVIF), minify CSS/JS', 'Defer non‑critical JS; inline critical CSS', 'Enable HTTP caching & CDN', 'Lazy‑load below‑the‑fold media'],
    20:['LCP: optimize hero image & server TTFB', 'INP: reduce JS work and long tasks', 'CLS: set width/height for media & fonts', 'Monitor with PSI + field data'],
    21:['Add a primary CTA matching intent', 'Use descriptive button labels', 'Place CTAs at top + end', 'Link to next step (signup, contact, guide)'],
    22:['Declare the primary entity (name/type)', 'Use consistent naming across the page', 'Link to the entity’s official page', 'Add schema (e.g., Organization, Person)'],
    23:['Mention related entities with short context', 'Link to authoritative sources', 'Avoid keyword lists—use sentences', 'Use a glossary or hover cards if needed'],
    24:['Add the right schema (Article/FAQ/Product)', 'Validate in Rich Results Test', 'Avoid conflicting or duplicate types', 'Keep JSON‑LD up to date'],
    25:['Show sameAs and Organization details', 'Add logo, address, phone (NAP)', 'Link to social profiles', 'Ensure footer/company page is complete']
  };

  function openModal(catIndex, anchorBtn){
    const m = document.getElementById('clModal');
    const title = document.getElementById('clTitle');
    const scoreRow = document.getElementById('clScoreRow');
    const list = document.getElementById('clList');
    if(!m || !list) return;

    // Use label text from the same <li>
    let labelText = '';
    const li = anchorBtn ? anchorBtn.closest('li') : null;
    if(li){
      const lbl = li.querySelector('label span');
      if(lbl) labelText = lbl.textContent.trim();
    }
    if(!labelText) labelText = 'Checklist improvement';

    // Pull numeric index and score if available
    let idx = parseInt((anchorBtn && anchorBtn.getAttribute('data-id') || '').replace(/[^0-9]/g,''), 10);
    if(!idx || !(idx in CAT_TIPS)) idx = Object.keys(CAT_TIPS)[0];

    // Score badge content
    let score = '—';
    if(li){
      const badge = li.querySelector('.score-badge');
      if(badge) score = badge.textContent.trim();
    }
    title.textContent = 'Improve: ' + labelText;
    scoreRow.textContent = (score !== '—') ? ('Current score: ' + score) : '';

    // Build the list
    const tips = CAT_TIPS[idx] || ['No tips available for this item yet.'];
    list.innerHTML = tips.map(t => '<li>'+ t +'</li>').join('');

    // Open
    m.classList.add('open');
    m.setAttribute('aria-hidden','false');

    // Focus management
    const closeBtn = m.querySelector('[data-close]');
    setTimeout(()=>{ if(closeBtn) closeBtn.focus(); }, 0);
  }

  function closeModal(){
    const m = document.getElementById('clModal');
    if(!m) return;
    m.classList.remove('open');
    m.setAttribute('aria-hidden','true');
  }

  // Click binding (event delegation)
  document.addEventListener('click', function(e){
    const btn = e.target.closest && e.target.closest('.improve-btn');
    if(btn){ e.preventDefault(); openModal(null, btn); return; }
    if(e.target && e.target.getAttribute && e.target.getAttribute('data-close')==='1'){ closeModal(); }
  });
  // ESC to close
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });
})();
</script>
<!-- ===== /Checklist Improve Modal ===== -->


<!-- ===== Unique Aurora Liquid Wheel + Ambient Glow + Stickers ===== -->
<style>
  :root { --fx-spin: 0deg; --fx-h: 120; }
  body.fx-ambient {
    background:
      radial-gradient(1200px 600px at 80% -10%, hsl(var(--fx-h) 90% 60% / .10), transparent 60%),
      radial-gradient(900px 500px at 20% 110%, hsl(calc(var(--fx-h) + 70) 90% 60% / .10), transparent 60%),
      #0c1022;
    transition: background 400ms ease;
  }
  .fx-wheel { position: relative; isolation: isolate; filter: drop-shadow(0 12px 40px rgba(0,0,0,.45)); }
  .fx-wheel .wheel-halo{
    position:absolute; inset:-18px; z-index:-1; border-radius:50%;
    background:
      conic-gradient(from var(--fx-spin),
        hsl(var(--fx-h) 92% 62% / .65),
        hsl(calc(var(--fx-h) + 45) 92% 60% / .65),
        hsl(calc(var(--fx-h) + 90) 92% 58% / .65),
        hsl(var(--fx-h) 92% 62% / .65));
    -webkit-mask: radial-gradient(farthest-side, rgba(0,0,0,0) 62%, #000 63%);
            mask: radial-gradient(farthest-side, rgba(0,0,0,0) 62%, #000 63%);
    filter: blur(18px) saturate(1.2);
    opacity:.85; pointer-events:none;
  }
  .aurora-title{ position:relative; display:inline-block;
    background: linear-gradient(90deg, hsl(var(--fx-h) 90% 65%), hsl(calc(var(--fx-h)+50) 90% 65%));
    -webkit-background-clip:text; background-clip:text; color:transparent; }
  .aurora-title::after{ content:""; position:absolute; left:0; right:0; bottom:-8px; height:2px; border-radius:999px;
    background: linear-gradient(90deg, hsl(var(--fx-h) 90% 65%), hsl(calc(var(--fx-h)+50) 90% 65%)); opacity:.6; }
  .fx-sticker{ position: fixed; z-index: 99999; font-size: 22px; pointer-events:none;
    transform: translate(-50%, -50%) scale(.9); animation: sticker-pop 900ms cubic-bezier(.2,.8,.2,1) forwards; }
  @keyframes sticker-pop{
    0%{ opacity:0; transform: translate(-50%,-30%) scale(.7) rotate(-8deg); }
    30%{ opacity:1; transform: translate(-50%,-50%) scale(1) rotate(0deg); }
    70%{ opacity:1; transform: translate(-50%,-70%) scale(1.02) rotate(2deg); }
    100%{ opacity:0; transform: translate(-50%,-90%) scale(.96) rotate(6deg); }
  }
</style>
<script>
(function(){
  const clamp = (n,min,max)=> Math.max(min, Math.min(max,n));
  const pick = (qs)=> document.querySelector(qs);
  function detectScore(){
    const el =
      pick('[data-total-score]') ||
      pick('.score-wheel[data-score]') ||
      pick('.score-wheel .score-value') ||
      pick('.main-score') ||
      pick('.score .value') ||
      pick('.score-badge[data-score]');
    let t = '';
    if(!el) return 0;
    t = el.dataset.totalScore || el.getAttribute('data-score') || el.textContent || '0';
    const m = String(t).match(/\d{1,3}/);
    return clamp(parseInt(m ? m[0] : '0', 10)||0, 0, 100);
  }
  const scoreToHue = s => Math.round(120 * (s/100));
  function startSpin(){
    let angle = 0;
    setInterval(()=> {
      angle = (angle + 18) % 360;
      document.documentElement.style.setProperty('--fx-spin', angle + 'deg');
    }, 1000);
  }
  function skinWheel(){
    const wheel =
      pick('.score-wheel') ||
      pick('[data-total-score]') ||
      pick('.main-score') ||
      pick('.score');
    if(!wheel) return;
    if(!wheel.classList.contains('fx-wheel')){
      wheel.classList.add('fx-wheel');
      const halo = document.createElement('span');
      halo.className = 'wheel-halo';
      wheel.appendChild(halo);
    }
    document.body.classList.add('fx-ambient');
    const score = detectScore();
    document.documentElement.style.setProperty('--fx-h', scoreToHue(score));
  }
  function sticker(x, y, emoji){
    const s = document.createElement('div');
    s.className = 'fx-sticker';
    s.textContent = emoji;
    s.style.left = x + 'px'; s.style.top = y + 'px';
    document.body.appendChild(s);
    setTimeout(()=> s.remove(), 950);
  }
  function bindStickers(){
    document.addEventListener('change', (e)=>{
      const cb = e.target.closest && e.target.closest('.checklist input[type="checkbox"]');
      if(cb && cb.checked){
        const r = cb.getBoundingClientRect();
        sticker(r.left + r.width/2, r.top + window.scrollY, ['✨','✅','📈','🌈'][Math.floor(Math.random()*4)]);
      }
    });
  }
  function watchScore(){
    let last = detectScore();
    setInterval(()=>{
      const now = detectScore();
      if(now !== last){
        document.documentElement.style.setProperty('--fx-h', Math.round(120 * (now/100)));
        last = now;
      }
    }, 1500);
  }
  skinWheel();
  bindStickers();
  startSpin();
  watchScore();
})();
</script>
<!-- ===== /Unique Aurora Liquid Wheel block ===== -->


<!-- ===== Multicolor Animated Category Icons (SVG sprite + auto-attach) ===== -->
<style>
  .cat-ico{ width:22px; height:22px; margin-right:.55rem; vertical-align:-3px;
    filter: drop-shadow(0 6px 16px rgba(124,92,255,.22));
    animation: catHue 12s linear infinite; }
  @keyframes catHue { to { filter: hue-rotate(360deg) drop-shadow(0 6px 16px rgba(124,92,255,.22)); } }
  .cat-ico .line{ fill:none; stroke:url(#grad-aurora-ico); stroke-width:2; stroke-linecap:round; stroke-linejoin:round;
    stroke-dasharray:120; stroke-dashoffset:360; animation: catDash 14s linear infinite; }
  @keyframes catDash { to { stroke-dashoffset:0; } }
  .cat-ico .fill{ fill:url(#grad-aurora-ico); }
  .cat-ico.spin{ animation: catHue 12s linear infinite, catSpin 10s linear infinite; }
  @keyframes catSpin { to { transform: rotate(360deg); } }
</style>

<svg aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden">
  <defs>
    <linearGradient id="grad-aurora-ico" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%"   stop-color="#7c5cff"/>
      <stop offset="50%"  stop-color="#22c3f7"/>
      <stop offset="100%" stop-color="#a3ff7f"/>
    </linearGradient>
  </defs>

  <!-- Document / Content -->
  <symbol id="ico-doc" viewBox="0 0 24 24">
    <path class="line" d="M6 3h7l5 5v13H6zM13 3v6h6" />
    <path class="line" d="M8 12h8M8 16h8M8 20h8" />
  </symbol>

  <!-- Key / Keywords -->
  <symbol id="ico-key" viewBox="0 0 24 24">
    <circle class="line" cx="7" cy="12" r="3"/>
    <path class="line" d="M10 12h6l3-3m-3 3l3 3" />
  </symbol>

  <!-- Meta / Tag -->
  <symbol id="ico-tag" viewBox="0 0 24 24">
    <path class="line" d="M3 12l9-9h6l3 3v6l-9 9L3 12z" />
    <circle class="line" cx="16" cy="8" r="1.6" />
  </symbol>

  <!-- Canonical / Link+Check -->
  <symbol id="ico-canon" viewBox="0 0 24 24">
    <path class="line" d="M9 12a3 3 0 0 1 3-3h3a3 3 0 1 1 0 6h-3" />
    <path class="line" d="M15 12a3 3 0 0 1-3 3H9a3 3 0 1 1 0-6h1" />
    <path class="line" d="M17 4l2 2 3-3" />
  </symbol>

  <!-- Sitemap / Graph -->
  <symbol id="ico-sitemap" viewBox="0 0 24 24">
    <rect class="line" x="10" y="3" width="4" height="3" rx="1"/>
    <rect class="line" x="3" y="18" width="5" height="3" rx="1"/>
    <rect class="line" x="16" y="18" width="5" height="3" rx="1"/>
    <path class="line" d="M12 6v5M12 11h-6v4M12 11h6v4" />
  </symbol>

  <!-- Shield / E-E-A-T -->
  <symbol id="ico-shield" viewBox="0 0 24 24">
    <path class="line" d="M12 3l7 3v5c0 5-3.5 8-7 10-3.5-2-7-5-7-10V6l7-3z" />
    <path class="line" d="M9.5 12l1.8 1.8 3.2-3.2" />
  </symbol>

  <!-- Link -->
  <symbol id="ico-link" viewBox="0 0 24 24">
    <path class="line" d="M10 14L8 16a4 4 0 0 1-6-6l2-2" />
    <path class="line" d="M14 10l2-2a4 4 0 0 1 6 6l-2 2" />
    <path class="line" d="M8 12h8" />
  </symbol>

  <!-- Media / Image -->
  <symbol id="ico-media" viewBox="0 0 24 24">
    <rect class="line" x="3" y="5" width="18" height="14" rx="2" />
    <circle class="line" cx="9" cy="9" r="2" />
    <path class="line" d="M3 17l5-5 4 4 3-3 6 6" />
  </symbol>

  <!-- Structure / List -->
  <symbol id="ico-structure" viewBox="0 0 24 24">
    <path class="line" d="M9 6h12M9 12h12M9 18h12" />
    <circle class="line" cx="5" cy="6" r="1.2" />
    <circle class="line" cx="5" cy="12" r="1.2" />
    <circle class="line" cx="5" cy="18" r="1.2" />
  </symbol>

  <!-- Mobile -->
  <symbol id="ico-mobile" viewBox="0 0 24 24">
    <rect class="line" x="7" y="3" width="10" height="18" rx="2"/>
    <circle class="line" cx="12" cy="17" r="1"/>
  </symbol>

  <!-- Speed / Gauge -->
  <symbol id="ico-speed" viewBox="0 0 24 24">
    <path class="line" d="M4 15a8 8 0 1 1 16 0" />
    <path class="line" d="M12 15l4-4" />
    <path class="line" d="M6 19h12" />
  </symbol>

  <!-- UX / Pointer -->
  <symbol id="ico-ux" viewBox="0 0 24 24">
    <path class="line" d="M6 4l8 8-4 1 3 6 2-1 2 4" />
  </symbol>

  <!-- CTA / Target -->
  <symbol id="ico-cta" viewBox="0 0 24 24">
    <circle class="line" cx="12" cy="12" r="8" />
    <circle class="line" cx="12" cy="12" r="4" />
    <circle class="line" cx="12" cy="12" r="1" />
  </symbol>

  <!-- Schema / Graph nodes -->
  <symbol id="ico-schema" viewBox="0 0 24 24">
    <circle class="line" cx="5" cy="12" r="2"/>
    <circle class="line" cx="12" cy="5" r="2"/>
    <circle class="line" cx="19" cy="12" r="2"/>
    <circle class="line" cx="12" cy="19" r="2"/>
    <path class="line" d="M7 12h10M12 7v10M7 11L11 7M17 11l-4-4M7 13l4 4M17 13l-4 4"/>
  </symbol>

  <!-- Breadcrumbs -->
  <symbol id="ico-breadcrumb" viewBox="0 0 24 24">
    <path class="line" d="M5 6l5 6-5 6M14 6l5 6-5 6"/>
  </symbol>

  <!-- URL / Slug -->
  <symbol id="ico-url" viewBox="0 0 24 24">
    <path class="line" d="M7 12a5 5 0 0 1 5-5h1" />
    <path class="line" d="M17 12a5 5 0 0 1-5 5h-1" />
    <path class="line" d="M8 12h8" />
  </symbol>

  <!-- Entity / Atom -->
  <symbol id="ico-entity" viewBox="0 0 24 24">
    <circle class="line" cx="12" cy="12" r="2"/>
    <ellipse class="line" cx="12" cy="12" rx="8" ry="4"/>
    <ellipse class="line" cx="12" cy="12" rx="4" ry="8" transform="rotate(60 12 12)"/>
    <ellipse class="line" cx="12" cy="12" rx="4" ry="8" transform="rotate(-60 12 12)"/>
  </symbol>

  <!-- Star (default) -->
  <symbol id="ico-star" viewBox="0 0 24 24">
    <path class="line" d="M12 3l2.8 5.7L21 10l-4.5 4.2L17.6 21 12 17.8 6.4 21l1.1-6.8L3 10l6.2-1.3L12 3z"/>
  </symbol>
</svg>

<script>
(function(){
  // Keywords -> symbol id
  const ICONS = [
    {k:['content','intent','readability','text'], id:'ico-doc'},
    {k:['keyword','semantic','terms'], id:'ico-key'},
    {k:['meta','description','title','tag'], id:'ico-tag'},
    {k:['canonical','duplicate'], id:'ico-canon'},
    {k:['sitemap','index'], id:'ico-sitemap'},
    {k:['eat','e-e-a-t','trust','expertise','author'], id:'ico-shield'},
    {k:['link','internal','external','backlink'], id:'ico-link'},
    {k:['image','media','video'], id:'ico-media'},
    {k:['structure','heading','h1','h2','outline'], id:'ico-structure'},
    {k:['mobile','responsive'], id:'ico-mobile'},
    {k:['speed','performance','vitals','core web vitals','lcp','cls','inp'], id:'ico-speed'},
    {k:['ux','usability','accessibility'], id:'ico-ux'},
    {k:['cta','conversion','button'], id:'ico-cta'},
    {k:['schema','json-ld','rich result'], id:'ico-schema'},
    {k:['breadcrumb'], id:'ico-breadcrumb'},
    {k:['url','slug'], id:'ico-url'},
    {k:['entity','entities','topic'], id:'ico-entity'},
  ];
  const pickIcon = (label)=>{
    const t = (label||'').toLowerCase();
    for(const m of ICONS){
      if(m.k.some(w => t.includes(w))) return m.id;
    }
    return 'ico-star';
  };

  // Candidate headers for categories (robust across your templates)
  const headers = Array.from(document.querySelectorAll(
    '.checklist h3, .checklist .category-title, .checklist .group-title, .checklist [data-cat-title], .checklist .section-title, .checklist .title, .checklist .header'
  ));

  // If no explicit checklist container, fall back to any h3 with data-category
  if(headers.length === 0){
    document.querySelectorAll('h3,[data-category],[data-cat]').forEach(h => headers.push(h));
  }

  headers.forEach((h, idx)=>{
    if(h.querySelector('svg.cat-ico')) return; // already has one
    // Hide any old <i> icon fonts if present
    h.querySelectorAll('i').forEach(i=> i.style.display='none');

    const label = h.textContent.trim();
    const sym = pickIcon(label);
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('class','cat-ico');
    svg.setAttribute('viewBox','0 0 24 24');
    svg.setAttribute('aria-hidden','true');

    const use = document.createElementNS('http://www.w3.org/2000/svg','use');
    use.setAttributeNS('http://www.w3.org/1999/xlink','xlink:href','#'+sym);
    // Newer browsers support href without xlink:
    use.setAttribute('href','#'+sym);
    svg.appendChild(use);

    // Insert at the very start
    h.insertBefore(svg, h.firstChild);
  });
})();
</script>
<!-- ===== /Animated Category Icons ===== -->


<!-- ===== Burning Fire Score Wheel for Checklist Categories ===== -->
<style>
  /* Make headers align icon + wheel + text nicely */
  .checklist h3, .checklist .category-title, .checklist .group-title, .checklist [data-cat-title], .checklist .section-title, .checklist .title, .checklist .header{
    display:flex; align-items:center; gap:.65rem;
  }

  .cat-wheel{
    --size: 48px;
    --thick: 6px;
    --pct: 0;
    --ang: calc(3.6deg * var(--pct));
    position:relative; width:var(--size); height:var(--size);
    border-radius:50%; display:inline-grid; place-items:center;
    background:
      radial-gradient(farthest-side, rgba(255,255,255,.06) calc(99% - var(--thick)), transparent calc(100% - var(--thick))),
      conic-gradient(from -90deg, rgba(255,255,255,.08) var(--ang), rgba(255,255,255,.02) 0 360deg);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.12), 0 10px 24px rgba(0,0,0,.35);
    overflow:visible;
  }
  .cat-wheel .val{
    position:relative; z-index:3; font:800 12.5px/1.1 Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial;
    color:#fff; text-shadow:0 2px 10px rgba(0,0,0,.6);
  }
  /* Fire fill (masked to progress arc) */
  .cat-wheel .fire{
    position:absolute; inset:0; border-radius:50%; z-index:2;
    background:
      conic-gradient(
        from var(--spin, -90deg),
        #ff3d00 0%, #ff6a00 10%, #ffae00 20%, #ffd200 30%,
        #ff3d00 40%, #ff6a00 50%, #ffae00 60%, #ffd200 70%,
        #ff3d00 80%, #ff6a00 90%, #ffae00 100%);
    -webkit-mask:
      radial-gradient(farthest-side, transparent calc(100% - var(--thick)), #000 calc(100% - var(--thick) + .5px)),
      conic-gradient(#000 0deg var(--ang), transparent var(--ang) 360deg);
            mask:
      radial-gradient(farthest-side, transparent calc(100% - var(--thick)), #000 calc(100% - var(--thick) + .5px)),
      conic-gradient(#000 0deg var(--ang), transparent var(--ang) 360deg);
    filter: saturate(1.35) brightness(1.05);
    animation: fireSpin 8s linear infinite, fireFlicker 1.8s ease-in-out infinite;
  }
  /* Soft glowing halo following the filled arc */
  .cat-wheel::before{
    content:""; position:absolute; inset:-8px; border-radius:50%; z-index:1;
    background:
      conic-gradient(from var(--spin, -90deg),
        rgba(255,77,0,.65), rgba(255,154,0,.65), rgba(255,210,0,.60), rgba(255,77,0,.65));
    -webkit-mask:
      radial-gradient(farthest-side, transparent calc(100% - var(--thick) - 8px), #000 calc(100% - var(--thick) - 7px)),
      conic-gradient(#000 0deg var(--ang), transparent var(--ang) 360deg);
            mask:
      radial-gradient(farthest-side, transparent calc(100% - var(--thick) - 8px), #000 calc(100% - var(--thick) - 7px)),
      conic-gradient(#000 0deg var(--ang), transparent var(--ang) 360deg);
    filter: blur(12px) saturate(1.4) brightness(1.15);
    opacity:.85;
    animation: fireSpin 10s linear infinite, fireFlicker 2.2s ease-in-out infinite;
  }
  @keyframes fireSpin { to { transform: rotate(360deg); } }
  @keyframes fireFlicker {
    0%{ filter: saturate(1.2) brightness(1.0); }
    50%{ filter: saturate(1.45) brightness(1.15); }
    100%{ filter: saturate(1.25) brightness(1.0); }
  }
</style>
<script>
(function(){
  function findCategoryHeaders(){
    return Array.from(document.querySelectorAll(
      '.checklist h3, .checklist .category-title, .checklist .group-title, .checklist [data-cat-title], .checklist .section-title, .checklist .title, .checklist .header'
    ));
  }
  function categoryRootFromHeader(h){
    // Go up to a reasonable container for this category
    return h.closest('.card, .panel, .category, .group, section, .checklist') || h.parentElement;
  }
  function computeCategoryPct(root){
    const inputs = root.querySelectorAll('input[type="checkbox"]');
    const total = inputs.length;
    const checked = Array.from(inputs).filter(i=> i.checked).length;
    const pct = total ? Math.round((checked/total) * 100) : 0;
    return {pct, total, checked};
  }
  function ensureWheelOnHeader(h){
    if(h.querySelector('.cat-wheel')) return h.querySelector('.cat-wheel');
    const wheel = document.createElement('span');
    wheel.className = 'cat-wheel';
    wheel.innerHTML = '<span class="fire"></span><b class="val">0%</b>';
    // Insert as very first element (before icon, if any)
    h.insertBefore(wheel, h.firstChild);
    return wheel;
  }
  function updateWheel(wheel, pct){
    wheel.style.setProperty('--pct', pct);
    const val = wheel.querySelector('.val');
    if(val) val.textContent = pct + '%';
  }
  function refreshAll(){
    findCategoryHeaders().forEach(h=>{
      const root = categoryRootFromHeader(h);
      const {pct} = computeCategoryPct(root);
      const wheel = ensureWheelOnHeader(h);
      updateWheel(wheel, pct);
    });
  }
  // Initial
  refreshAll();
  // On changes inside checklist
  document.addEventListener('change', function(e){
    if(e.target && e.target.matches('.checklist input[type="checkbox"]')){
      // refresh the category where it changed
      const h = (e.target.closest('.card, .panel, .category, .group, section, .checklist') || document).querySelector('.category-title, h3, .group-title, [data-cat-title], .section-title, .title, .header');
      if(h){
        const root = h.closest('.card, .panel, .category, .group, section, .checklist') || h.parentElement;
        const {pct} = (function(root){
          const inputs = root.querySelectorAll('input[type="checkbox"]');
          const total = inputs.length;
          const checked = Array.from(inputs).filter(i=> i.checked).length;
          return {pct: total ? Math.round((checked/total) * 100) : 0};
        })(root);
        const wheel = ensureWheelOnHeader(h);
        updateWheel(wheel, pct);
      } else {
        refreshAll();
      }
    }
  });
  // Optional: recalc every few seconds if content is dynamic
  setInterval(refreshAll, 5000);
})();
</script>
<!-- ===== /Burning Fire Score Wheel ===== -->

</body>
</html>
