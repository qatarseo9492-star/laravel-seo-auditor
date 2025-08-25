{{-- resources/views/home.blade.php — v2025-08-25 + P0 Semantic SEO (Topic Coverage, Schema, Links, E-E-A-T, Snippet, Answer Target, Image ALT, Guardrails) --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, UX, speed, and Core Web Vitals with colorful, clear insights.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();

  $analyzeJsonUrl = \Illuminate\Support\Facades\Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl     = \Illuminate\Support\Facades\Route::has('analyze')      ? route('analyze')      : url('analyze');
  $psiProxyUrl    = \Illuminate\Support\Facades\Route::has('psi.proxy')    ? route('psi.proxy')    : url('api/psi');

  // Detector backend (supports either name)
  $detectUrl = \Illuminate\Support\Facades\Route::has('detect')      ? route('detect')
             : (\Illuminate\Support\Facades\Route::has('api.detect') ? route('api.detect') : url('api/detect'));
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
  content:"00c"; /* Font Awesome check */
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
.echip .sal{opacity:.9;font-weight:900}
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

/* ==== P0: Topic Coverage (minimal, matches style) ==== */
.topics{margin-top:14px;background:linear-gradient(135deg,rgba(59,130,246,.10),rgba(34,197,94,.10));border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:16px}
.topics-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem}
.topics-head h4{margin:0;font-size:1.08rem}
.topics-meta{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.5rem}
.topics-chip{display:inline-flex;align-items:center;gap:.45rem;padding:.35rem .7rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);font-weight:900;background:rgba(255,255,255,.05)}
.topics-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.topics-card{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.7rem}
.topics-list{display:flex;flex-wrap:wrap;gap:.4rem}
.tchip{display:inline-flex;align-items:center;gap:.35rem;padding:.32rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);font-weight:800}

/* ==== P0: Schema Builder ==== */
.schema{margin-top:14px;background:linear-gradient(135deg,rgba(155,92,255,.10),rgba(61,226,255,.10));border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:16px}
.schema-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem}
.schema-head h4{margin:0;font-size:1.08rem}
.schema-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.schema-card{grid-column:span 6;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.7rem}
.schema-card header{display:flex;align-items:center;justify-content:space-between;gap:.5rem;margin-bottom:.4rem}
.schema-code{max-height:220px;overflow:auto;background:#0b0d21;border:1px solid rgba(255,255,255,.1);border-radius:10px;padding:.6rem;font:12px/1.45 ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;white-space:pre}
.copy-btn{border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);border-radius:10px;padding:.35rem .6rem;font-weight:900;cursor:pointer}
.copy-btn:hover{background:rgba(255,255,255,.12)}
@media (max-width:768px){.schema-card{grid-column:span 12}}

/* ==== P0: Internal Link Suggestions ==== */
.links{margin-top:14px;background:linear-gradient(135deg,rgba(96,165,250,.10),rgba(236,72,153,.10));border:1px solid rgba(255,255,255,.1);border-radius:16px;padding:16px}
.links-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem}
.links-head h4{margin:0;font-size:1.08rem}
.links-table{width:100%;border-collapse:separate;border-spacing:0 .5rem}
.links-table th{font-weight:900;text-align:left;color:var(--text-dim)}
.links-row{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:12px;overflow:hidden}
.links-row td{padding:.55rem .6rem}

/* ==== Snippet preview ==== */
.snippet{margin-top:.6rem;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.6rem}
.snip-title{color:#8ab4f8;font-weight:900;margin:0 0 .15rem}
.snip-url{color:#93c18c;font-size:.9rem;margin:0 0 .25rem}
.snip-desc{color:#c8cbd9}

/* Small helpers */
.hide{display:none!important}

/* ==== Readability: Good Badge ==== */
.read-good-badge{
  position:absolute; top:10px; right:10px; z-index:3;
  display:flex; align-items:center; gap:.6rem;
  padding:.6rem .9rem; border-radius:14px;
  background:linear-gradient(135deg, rgba(34,197,94,.22), rgba(61,226,255,.22));
  border:1px solid rgba(255,255,255,.22);
  box-shadow:0 18px 40px rgba(0,0,0,.35), inset 0 0 0 1px rgba(255,255,255,.08);
  color:#fff; backdrop-filter:blur(6px);
  transform:translateY(-8px) scale(.98); opacity:0; pointer-events:none;
  transition:transform .35s cubic-bezier(.22,1,.36,1), opacity .35s ease;
}
.read-good-badge.show{ transform:translateY(0) scale(1); opacity:1; pointer-events:auto; }
.read-good-badge i{
  width:36px;height:36px;border-radius:12px; display:grid; place-items:center;
  background:conic-gradient(from 0deg, var(--read-ac1), var(--read-ac2), var(--read-ac3), var(--read-ac1));
  box-shadow:0 8px 20px rgba(0,0,0,.28), inset 0 0 0 1px rgba(255,255,255,.12);
  text-shadow:0 1px 0 rgba(0,0,0,.45);
}
.read-good-badge .txt{display:flex; flex-direction:column; line-height:1.05}
.read-good-badge .txt strong{font-weight:900; font-size:.96rem; letter-spacing:.2px}
.read-good-badge .txt span{opacity:.9; font-size:.82rem}

/* sparkly diagonal shine that loops */
.read-good-badge .badge-shine{
  position:absolute; inset:0; pointer-events:none; mix-blend-mode:screen;
  background:linear-gradient(120deg, transparent, rgba(255,255,255,.22), transparent 60%);
  transform:translateX(-120%); animation:badgeSheen 4.5s linear infinite;
  border-radius:14px;
}
@keyframes badgeSheen{to{transform:translateX(120%)}}

/* Confetti burst on first show */
@keyframes confettiFall{
  0%{transform:translateY(-10px) rotate(0deg); opacity:0}
  30%{opacity:1}
  100%{transform:translateY(24px) rotate(360deg); opacity:0}
}
.read-good-badge.show::after{
  content:""; position:absolute; inset:-8px; pointer-events:none;
  background:
    radial-gradient(2px 2px at 20% 30%, rgba(255,255,255,.9), transparent 55%),
    radial-gradient(2px 2px at 60% 10%, rgba(255,215,0,.9), transparent 55%),
    radial-gradient(2px 2px at 80% 60%, rgba(61,226,255,.9), transparent 55%),
    radial-gradient(2px 2px at 35% 80%, rgba(34,197,94,.9), transparent 55%);
  animation:confettiFall 1.1s ease-out 1;
}

/* ==== Readability: Icon Luxe upgrade (overrides) ==== */
.read-head .ico{
  position:relative; overflow:hidden;
  background:conic-gradient(from 0deg, var(--read-ac1), var(--read-ac2), var(--read-ac3), var(--read-ac1));
  animation:spinGradHead 14s linear infinite;
}
.read-head .ico::after{
  content:""; position:absolute; inset:0; border-radius:12px; pointer-events:none;
  background:radial-gradient(60% 80% at 30% 20%, rgba(255,255,255,.22), transparent 60%);
  mix-blend-mode:screen;
}
@keyframes spinGradHead{to{transform:rotate(360deg)}}

/* Icon pill: layered gradient, glow ring, micro-particles */
.read-card .metric i{
  position:relative; isolation:isolate; overflow:hidden;
  width:42px; height:42px; border-radius:14px;
  background:linear-gradient(135deg, rgba(255,255,255,.06), rgba(255,255,255,.02)),
             conic-gradient(from 0deg, var(--read-ac1), var(--read-ac2), var(--read-ac3), var(--read-ac1));
  animation:spinGrad 9s linear infinite;
  box-shadow:
    0 10px 24px rgba(0,0,0,.35),
    inset 0 0 0 1px rgba(255,255,255,.14),
    0 0 0 3px rgba(61,226,255,.08);
  text-shadow:0 1px 0 rgba(0,0,0,.5);
}
.read-card .metric i::before{
  /* glow ring */
  content:""; position:absolute; inset:-1px; border-radius:16px;
  background:conic-gradient(from 0deg, var(--read-ac1), var(--read-ac2), var(--read-ac3), var(--read-ac1));
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude;
  padding:1px; opacity:.55; filter:blur(.2px);
  animation:iconRing 6s linear infinite;
}
.read-card .metric i::after{
  /* sparkles */
  content:""; position:absolute; inset:-20%; pointer-events:none;
  background:
    radial-gradient(1.5px 1.5px at 25% 35%, rgba(255,255,255,.9), transparent 55%),
    radial-gradient(1.5px 1.5px at 65% 20%, rgba(255,255,255,.8), transparent 55%),
    radial-gradient(1.5px 1.5px at 40% 75%, rgba(255,255,255,.75), transparent 55%);
  opacity:.5; animation:iconDust 18s linear infinite;
}
@keyframes iconRing{to{transform:rotate(360deg)}}
@keyframes iconDust{to{transform:translate3d(-6%, -5%,0)}}

.read-card:hover .metric i{ transform:translateZ(14px) scale(1.08) rotate(0.5deg) }
.read-card:hover .metric i::before{ opacity:.85 }

/* Per-card thematic hue tweaks for variety */
.read-grid .read-card:nth-child(1) .metric i{filter:hue-rotate(0deg) saturate(115%)}
.read-grid .read-card:nth-child(2) .metric i{filter:hue-rotate(35deg) saturate(120%)}
.read-grid .read-card:nth-child(3) .metric i{filter:hue-rotate(85deg) saturate(120%)}
.read-grid .read-card:nth-child(4) .metric i{filter:hue-rotate(145deg) saturate(120%)}
.read-grid .read-card:nth-child(5) .metric i{filter:hue-rotate(195deg) saturate(120%)}
.read-grid .read-card:nth-child(6) .metric i{filter:hue-rotate(255deg) saturate(120%)}
.read-grid .read-card:nth-child(7) .metric i{filter:hue-rotate(305deg) saturate(120%)}

/* Optional: subtle pulse when value > 80% (CSS-only heuristic with data-value attr if present) */
.read-card[data-value^="8"], .read-card[data-value^="9"], .read-card[data-value="100"]{
  animation:cardPulse 2.4s ease-in-out infinite;
}
@keyframes cardPulse{
  0%,100%{box-shadow:0 12px 36px rgba(0,0,0,.32)}
  50%{box-shadow:0 16px 42px rgba(0,0,0,.38), 0 0 0 6px rgba(61,226,255,.08)}
}

/* Reduced motion safety */
@media (prefers-reduced-motion: reduce){
  .read-card .metric i,
  .read-head .ico,
  .read-card .metric i::before,
  .read-card .metric i::after{animation:none}
}

/* ==== Human vs AI Content (Ensemble) — ULTRA PRO restyle ==== */
#detectorPanel{
  position:relative; isolation:isolate; overflow:hidden;
  border:1px solid var(--read-border);
  border-radius:18px; padding:16px;
  background:
    radial-gradient(1000px 260px at -10% 0%, rgba(99,102,241,.16), transparent 55%),
    radial-gradient(900px 320px at 110% 20%, rgba(45,212,191,.14), transparent 60%),
    linear-gradient(135deg, rgba(16,24,48,.55), rgba(18,20,40,.72));
  box-shadow:0 20px 60px rgba(0,0,0,.40), inset 0 1px 0 rgba(255,255,255,.06);
  backdrop-filter:blur(8px);
}
#detectorPanel .hvai-head{
  display:flex; align-items:center; gap:.6rem; margin-bottom:.6rem;
}
#detectorPanel .hvai-head i{
  width:34px;height:34px;border-radius:12px;display:grid;place-items:center;color:#fff;
  background:conic-gradient(from 0deg,#22c55e,#3de2ff,#9b5cff,#22c55e);
  box-shadow:0 8px 18px rgba(0,0,0,.28), inset 0 0 0 1px rgba(255,255,255,.12);
  animation:spinGrad 14s linear infinite;
}
.hvai-meta{display:flex;flex-wrap:wrap;gap:.5rem;margin:.4rem 0 .5rem}
.hvai-chip{
  display:inline-flex;align-items:center;gap:.45rem;
  padding:.4rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.22);
  background:linear-gradient(135deg, rgba(99,102,241,.18), rgba(61,226,255,.18));
  font-weight:700;color:#fff;
}
.hvai-chip i{opacity:.9}

.hvai-bar{display:grid;grid-template-columns:1fr 1fr; gap:.75rem}
.hvai-label{display:flex;align-items:center;gap:.4rem;justify-content:space-between;margin-bottom:.2rem}
.hvai-track{
  position:relative;height:14px;border-radius:12px;overflow:hidden;
  background:linear-gradient(180deg,#0b0d21,#0b0d21);
  border:1px solid var(--read-border); box-shadow:inset 0 0 0 1px rgba(255,255,255,.06);
}
.hvai-track::before{
  content:""; position:absolute; inset:0; pointer-events:none; opacity:.35;
  background:repeating-linear-gradient(45deg, rgba(255,255,255,.06) 0 10px, transparent 10px 20px);
}
.hvai-fill{
  position:absolute; left:0; top:0; bottom:0; width:0%;
  box-shadow:inset 0 0 0 1px rgba(255,255,255,.12), 0 12px 28px rgba(0,0,0,.28);
  transition:width .55s cubic-bezier(.22,1,.36,1);
}
.hvai-fill.human{background:linear-gradient(90deg,#22c55e,#3de2ff)}
.hvai-fill.ai{background:linear-gradient(90deg,#ef4444,#f97316)}

.hvai-badge{
  position:absolute; top:10px; right:10px; z-index:3;
  display:flex; align-items:center; gap:.6rem;
  padding:.6rem .9rem; border-radius:14px; color:#fff;
  background:linear-gradient(135deg, rgba(34,197,94,.22), rgba(61,226,255,.22));
  border:1px solid rgba(255,255,255,.22);
  box-shadow:0 18px 40px rgba(0,0,0,.35), inset 0 0 0 1px rgba(255,255,255,.08);
  transform:translateY(-8px) scale(.98); opacity:0; pointer-events:none;
  transition:transform .35s cubic-bezier(.22,1,.36,1), opacity .35s ease;
  backdrop-filter:blur(6px);
}
.hvai-badge.show{ transform:translateY(0) scale(1); opacity:1; pointer-events:auto; }
.hvai-badge i{
  width:36px;height:36px;border-radius:12px; display:grid; place-items:center;
  background:conic-gradient(from 0deg,#22c55e,#3de2ff,#9b5cff,#22c55e);
  text-shadow:0 1px 0 rgba(0,0,0,.45);
  box-shadow:0 8px 20px rgba(0,0,0,.28), inset 0 0 0 1px rgba(255,255,255,.12);
}
.hvai-badge .txt{display:flex;flex-direction:column;line-height:1.05}
.hvai-badge .txt strong{font-weight:900;font-size:.96rem;letter-spacing:.2px}
.hvai-badge .txt span{opacity:.92;font-size:.82rem}
.hvai-badge .badge-sheen{
  position:absolute; inset:0; border-radius:14px; pointer-events:none; mix-blend-mode:screen;
  background:linear-gradient(120deg,transparent,rgba(255,255,255,.22),transparent 60%);
  transform:translateX(-120%); animation:badgeSheen 4.5s linear infinite;
}

.hvai-verdict--human  .hvai-badge{ background:linear-gradient(135deg, rgba(34,197,94,.24), rgba(61,226,255,.22)); }
.hvai-verdict--mixed  .hvai-badge{ background:linear-gradient(135deg, rgba(99,102,241,.24), rgba(61,226,255,.22)); }
.hvai-verdict--ai     .hvai-badge{ background:linear-gradient(135deg, rgba(239,68,68,.26), rgba(249,115,22,.22));  }

/* tiny detector chips row */
.hvai-detectors{ display:flex; flex-wrap:wrap; gap:.35rem; margin-top:.45rem }
.hvai-detectors .det{
  display:inline-flex; align-items:center; gap:.35rem;
  padding:.28rem .5rem; border-radius:999px; font-size:.82rem;
  border:1px solid rgba(255,255,255,.22);
  background:linear-gradient(135deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
}
.hvai-detectors .det.ok{ background:linear-gradient(135deg, rgba(34,197,94,.18), rgba(61,226,255,.18)) }
.hvai-detectors .det.err{ background:linear-gradient(135deg, rgba(239,68,68,.22), rgba(249,115,22,.18)) }

@media (prefers-reduced-motion:reduce){
  #detectorPanel .hvai-head i, .hvai-badge .badge-sheen { animation:none }
}

/* ==== HVAI ULTRA NEON (gauge + panel) ==== */
#detectorPanel{
  position:relative; isolation:isolate; overflow:hidden;
  border:1px solid var(--read-border); border-radius:20px; padding:18px;
  background:
    radial-gradient(1200px 340px at -10% 0%, rgba(147,51,234,.18), transparent 55%),
    radial-gradient(1000px 360px at 110% 20%, rgba(14,165,233,.16), transparent 60%),
    linear-gradient(135deg, rgba(15,23,42,.78), rgba(10,12,26,.86));
  box-shadow:0 24px 70px rgba(0,0,0,.48), inset 0 1px 0 rgba(255,255,255,.06);
  backdrop-filter:blur(10px);
}
#detectorPanel::before{
  content:""; position:absolute; inset:-1px; border-radius:22px; padding:1px;
  background:conic-gradient(from 0deg, #22c55e, #3de2ff, #9b5cff, #ef4444, #f97316, #22c55e);
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; opacity:.22; pointer-events:none;
  animation:readGlow 18s linear infinite;
}

#detectorPanel .hvai-head i{
  width:36px;height:36px;border-radius:12px;display:grid;place-items:center;color:#fff;
  background:conic-gradient(from 0deg,#22c55e,#3de2ff,#9b5cff,#22c55e);
  box-shadow:0 10px 22px rgba(0,0,0,.35), inset 0 0 0 1px rgba(255,255,255,.12);
  animation:spinGrad 16s linear infinite;
}

.hvai-gauge{
  position:absolute; top:10px; right:12px; width:210px; height:210px;
  filter:drop-shadow(0 20px 40px rgba(0,0,0,.45));
}
@media (max-width:900px){
  .hvai-gauge{ position:relative; margin:8px auto 10px; top:auto; right:auto; }
}

.g-svg{ width:100%; height:100%; transform:rotate(-90deg) }
.g-track{ fill:none; stroke:rgba(255,255,255,.10); stroke-width:14; }
.g-track.human{ stroke-width:12 }
.g-arc{
  fill:none; stroke-linecap:round; stroke-dasharray:0 999; stroke-dashoffset:0;
  filter:drop-shadow(0 4px 16px rgba(0,0,0,.35));
}
.g-arc.human{ stroke:url(#gradHuman); stroke-width:12 }
.g-arc.ai{ stroke:url(#gradAI); stroke-width:14 }

.g-center{
  position:absolute; inset:0; display:grid; place-items:center; pointer-events:none;
  font-variant-numeric:tabular-nums;
}
.g-title{ font-size:.82rem; opacity:.8; letter-spacing:.3px; margin-top:6px }
.g-score{ font-size:1.8rem; font-weight:900; margin-top:-4px; text-shadow:0 1px 0 rgba(0,0,0,.45) }

/* Verdict badge: glassy prism */
.hvai-badge{
  position:absolute; left:12px; top:12px; z-index:3;
  display:flex; align-items:center; gap:.6rem; padding:.6rem .9rem; border-radius:14px; color:#fff;
  background:linear-gradient(135deg, rgba(34,197,94,.22), rgba(61,226,255,.22));
  border:1px solid rgba(255,255,255,.22);
  box-shadow:0 18px 44px rgba(0,0,0,.38), inset 0 0 0 1px rgba(255,255,255,.08);
  transform:translateY(-8px) scale(.98); opacity:0; pointer-events:none;
  transition:transform .35s cubic-bezier(.22,1,.36,1), opacity .35s ease;
  backdrop-filter:blur(6px);
}
.hvai-badge.show{ transform:translateY(0) scale(1); opacity:1; pointer-events:auto; }
.hvai-badge i{
  width:36px;height:36px;border-radius:12px; display:grid; place-items:center;
  background:conic-gradient(from 0deg,#22c55e,#3de2ff,#9b5cff,#22c55e);
  text-shadow:0 1px 0 rgba(0,0,0,.45);
  box-shadow:0 8px 20px rgba(0,0,0,.28), inset 0 0 0 1px rgba(255,255,255,.12);
}

.hvai-verdict--human  .hvai-badge{ background:linear-gradient(135deg, rgba(34,197,94,.24), rgba(61,226,255,.22)); }
.hvai-verdict--mixed  .hvai-badge{ background:linear-gradient(135deg, rgba(99,102,241,.26), rgba(61,226,255,.22)); }
.hvai-verdict--ai     .hvai-badge{ background:linear-gradient(135deg, rgba(239,68,68,.26), rgba(249,115,22,.24)); }

/* Bars remain for detail, but look sleeker */
.hvai-track{ height:14px; border-radius:12px; }
.hvai-fill.human{ background:linear-gradient(90deg,#22c55e,#3de2ff) }
.hvai-fill.ai{ background:linear-gradient(90deg,#ef4444,#f97316) }

/* mini detector chips */
.hvai-detectors .det{
  background:linear-gradient(135deg, rgba(255,255,255,.07), rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.22);
  border-radius:999px; padding:.3rem .6rem;
}
.hvai-detectors .det.ok{ background:linear-gradient(135deg, rgba(34,197,94,.18), rgba(61,226,255,.18)) }
.hvai-detectors .det.err{ background:linear-gradient(135deg, rgba(239,68,68,.22), rgba(249,115,22,.18)) }
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
    analyze: @json($analyzeUrl),
    psi: @json($psiProxyUrl),        // server proxy keeps API key hidden
    detect: @json($detectUrl)        // backend detector (optional)
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
            <span class="chip">Title: <b id="rTitleLen">—</b> <small id="rTitlePx" style="opacity:.8;margin-left:.25rem"></small></span>
            <span class="chip">Meta desc: <b id="rMetaLen">—</b> <small id="rMetaPx" style="opacity:.8;margin-left:.25rem"></small></span>
            <span class="chip">Canonical: <b id="rCanonical">—</b></span>
            <span class="chip">Robots: <b id="rRobots">—</b></span>
            <span class="chip">Viewport: <b id="rViewport">—</b></span>
            <span class="chip">H1/H2/H3: <b id="rHeadings">—</b></span>
            <span class="chip">Internal links: <b id="rInternal">—</b></span>
            <span class="chip">Schema: <b id="rSchema">—</b></span>
            <span class="chip">Images ALT: <b id="rAltCov">—</b></span>
            <span class="chip">Auto-checked: <b id="rAutoCount">—</b></span>
          </div>

          <div class="snippet" id="serpPreview" style="display:none">
            <div class="snip-title" id="snipTitle">Example Title</div>
            <div class="snip-url" id="snipUrl">example.com/page</div>
            <div class="snip-desc" id="snipDesc">Example description</div>
          </div>

          <div id="guardrails" class="read-suggest" style="display:none;margin-top:.6rem">
            <div class="title"><i class="fa-solid fa-shield-halved"></i> Indexability & Canonical Guardrails</div>
            <ul id="guardrailsList"></ul>
          </div>
        </div>
      </form>
    </div>

    <!-- 1) HUMAN vs AI (Ensemble) -->
    \1

  <!-- HVAI Verdict Badge -->
  <div id="hvaiBadge" class="hvai-badge" hidden>
    <div class="badge-sheen"></div>
    <i class="fa-solid fa-hexagon-nodes"></i>
    <div class="txt">
      <strong id="hvaiVerdictText">Ensemble Verdict</strong>
      <span id="hvaiVerdictHint">—</span>
    </div>
  </div>

      <div class="hvai-head">
        <i class="fa-solid fa-users-gear ico ico-purple"></i>
        <h4>Human vs AI Content (Ensemble)</h4>
      </div>
      <div class="hvai-meta">
        <span class="hvai-chip"><i class="fa-solid fa-shield-heart"></i> Confidence: <b id="detConfidence">—</b>%</span>
        <span class="hvai-chip"><i class="fa-solid fa-circle-info"></i> Higher bar = more AI-like (per detector)</span>
      </div>
      <div class="hvai-bar">
        <div>
          <div class="hvai-label"><span><i class="fa-solid fa-user"></i> Human-like</span><b id="hvaiHumanVal">—%</b></div>
          <div class="hvai-track"><div id="hvaiHumanFill" class="hvai-fill human" style="width:0%"></div></div>
        </div>
        <div>
          <div class="hvai-label"><span><i class="fa-solid fa-robot"></i> AI-like</span><b id="hvaiAIVal">—%</b></div>
          <div class="hvai-track"><div id="hvaiAIFill" class="hvai-fill ai" style="width:0%"></div></div>
        </div>
      </div>
      <div class="det-grid" id="detGrid"></div>
      <div class="det-note" id="detNote" style="color:var(--text-dim);margin-top:.35rem">Local ensemble activates if the backend provides no text/percentages.</div>
    </section>

    <!-- 2) READABILITY -->
    <section class="readability" id="readabilityPanel" style="display:none">

  <!-- Special Good Readability Badge -->
  <div id="readGoodBadge" class="read-good-badge" hidden>
    <div class="badge-shine"></div>
    <i class="fa-solid fa-trophy"></i>
    <div class="txt">
      <strong>Excellent Readability</strong>
      <span>Great job! Your content is clear and easy to read — appreciate the hard work.</span>
    </div>
  </div>

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
        <div class="title"><i class="fa-solid fa-child-reaching"></i> Answer Target (45–55 words)</div>
        <div id="readPlain">We’ll place a snippet-friendly paragraph here if found.</div>
      </div>
    </section>

    <!-- 3) ENTITIES -->
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

    <!-- 4) TOPIC COVERAGE (P0) -->
    <section class="topics" id="topicsPanel" style="display:none">
      <div class="topics-head">
        <i class="fa-solid fa-layer-group ico ico-cyan"></i>
        <h4>Topic Coverage</h4>
      </div>
      <div class="topics-meta">
        <span class="topics-chip"><i class="fa-solid fa-bullseye"></i> Primary: <b id="topicPrimary">—</b></span>
        <span class="topics-chip"><i class="fa-solid fa-list-check"></i> Coverage: <b id="topicCoveragePct">—%</b></span>
        <span class="topics-chip"><i class="fa-solid fa-circle-info"></i> Based on H1/H2/H3 + body</span>
      </div>
      <div class="topics-grid">
        <div class="topics-card">
          <div class="metric" style="display:flex;align-items:center;justify-content:space-between;font-weight:900"><span><i class="fa-solid fa-check"></i> Covered subtopics</span><b id="topicCoveredCount">0</b></div>
          <div class="topics-list" id="topicsCovered"></div>
        </div>
        <div class="topics-card">
          <div class="metric" style="display:flex;align-items:center;justify-content:space-between;font-weight:900"><span><i class="fa-solid fa-circle-exclamation"></i> Missing subtopics</span><b id="topicMissingCount">0</b></div>
          <div class="topics-list" id="topicsMissing"></div>
        </div>
      </div>
    </section>

    <!-- 5) SCHEMA BUILDER (P0) -->
    <section class="schema" id="schemaPanel" style="display:none">
      <div class="schema-head">
        <i class="fa-solid fa-code ico ico-cyan"></i>
        <h4>Schema Builder (JSON-LD)</h4>
      </div>
      <div class="schema-grid">
        <article class="schema-card">
          <header><strong>Article</strong><button class="copy-btn" data-copy="schemaArticle"><i class="fa-regular fa-copy"></i> Copy</button></header>
          <pre id="schemaArticle" class="schema-code">{}</pre>
        </article>
        <article class="schema-card">
          <header><strong>FAQ</strong><button class="copy-btn" data-copy="schemaFAQ"><i class="fa-regular fa-copy"></i> Copy</button></header>
          <pre id="schemaFAQ" class="schema-code">{}</pre>
        </article>
        <article class="schema-card">
          <header><strong>Breadcrumbs</strong><button class="copy-btn" data-copy="schemaBreadcrumbs"><i class="fa-regular fa-copy"></i> Copy</button></header>
          <pre id="schemaBreadcrumbs" class="schema-code">{}</pre>
        </article>
        <article class="schema-card">
          <header><strong>Organization</strong><button class="copy-btn" data-copy="schemaOrg"><i class="fa-regular fa-copy"></i> Copy</button></header>
          <pre id="schemaOrg" class="schema-code">{}</pre>
        </article>
        <article class="schema-card">
          <header><strong>WebSite + SearchAction</strong><button class="copy-btn" data-copy="schemaSearch"><i class="fa-regular fa-copy"></i> Copy</button></header>
          <pre id="schemaSearch" class="schema-code">{}</pre>
        </article>
      </div>
    </section>

    <!-- 6) INTERNAL LINK SUGGESTIONS (P0) -->
    <section class="links" id="linksPanel" style="display:none">
      <div class="links-head">
        <i class="fa-solid fa-link ico ico-cyan"></i>
        <h4>Internal Link Suggestions</h4>
      </div>
      <table class="links-table" id="linksTable">
        <thead><tr><th>Suggested anchor</th><th>Target (guess)</th><th>Reason</th></tr></thead>
        <tbody id="linksBody"></tbody>
      </table>
    </section>

    <!-- 7) PSI (unchanged panel, runs last) -->
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
  function clamp(v,min,max){ return v<min?min:(v>max?max:v); }

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

      // extractions for P0
      var imgs=[].slice.call(d.querySelectorAll('img'));
      var altOk=imgs.filter(img=> (img.getAttribute('alt')||'').trim().length>=3).length;
      var altCov=imgs.length? Math.round(altOk*100/imgs.length) : null;

      var author = q('meta[name="author"]','content') || q('[itemprop="author"]','content') || q('.author') || '';
      var datePub = q('meta[property="article:published_time"]','content') || q('time[datetime]','datetime') || q('meta[name="date"]','content') || '';
      var externalLinks = [].slice.call(d.querySelectorAll('a[href]')).filter(a=>{ try{ var u=new URL(a.getAttribute('href'),baseUrl); return u.origin!==origin; }catch(e){ return false; } }).length;

      return { title, metaDesc, titleLen: title?title.length:null, metaLen: metaDesc?metaDesc.length:null, canonical, robots, viewport, headings:(h1+'/'+h2+'/'+h3), internalLinks:internal, schema: schema?'yes':'no', sampleText: sample, altCov, author, datePub, externalLinks, doc: d };
    }catch(_){ return {}; }
  }

  function mergeMeta(into, add){
    if(!into) into={};
    var keys=['title','metaDesc','titleLen','metaLen','canonical','robots','viewport','headings','internalLinks','schema','sampleText','altCov','author','datePub','externalLinks','doc'];
    keys.forEach(function(k){
      if((into[k]===undefined || into[k]===null || into[k]==='—' || into[k]==='' ) && add && add[k]!==undefined && add[k]!==null){
        into[k]=add[k];
      }
    });
    return into;
  }

  /* ===================== Stylometry & Readability ===================== */
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
    return { text, wordCount:tokens, flesch:_flesch(text), cov, longRatio, triRepeatRatio: triT?triR/triT:0, TTR, hapaxRatio: types?hapax/types:0, avgWordLen:avgLen, digitsPer100:digits, asl: asl, sentences: sents };
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

  /* === Human vs AI rendering === */
  function renderDetectors(res){
    var grid = document.getElementById('detGrid'); var confEl = document.getElementById('detConfidence');
    if(confEl) confEl.textContent = isFinite(res.confidence)? Math.round(res.confidence): '—';
    var hv = document.getElementById('hvaiHumanVal'), av=document.getElementById('hvaiAIVal');
    var hf = document.getElementById('hvaiHumanFill'), af=document.getElementById('hvaiAIFill');
    if(hv) hv.textContent = isFinite(res.humanPct)? Math.round(res.humanPct)+'%':'—%';
    if(av) av.textContent = isFinite(res.aiPct)? Math.round(res.aiPct)+'%':'—%';
    if(hf) hf.style.width = Math.max(0, Math.min(100, res.humanPct||0)) + '%';
    if(af) af.style.width = Math.max(0, Math.min(100, res.aiPct||0)) + '%';

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

  /* === Readability rendering + Answer Target === */
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
    // annotate state for the badge observer
    if (chip) {
      chip.setAttribute('data-state', (gradeInt <= 8) ? 'good' : ((gradeInt <= 10) ? 'mid' : 'bad'));
    }

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

    // Simple fixes
    var fixes = [];
    if ((s.asl||0) > 20) fixes.push('Break long sentences into 12–16 words.');
    if ((syl.spw||0) > 1.60) fixes.push('Prefer shorter words (use simpler synonyms).');
    if ((s.TTR||0) < 0.35) fixes.push('Use more varied vocabulary (avoid repeating the same words).');
    if ((s.triRepeatRatio||0) > 0.10) fixes.push('Remove repeated phrases; keep each idea unique.');
    if ((s.digitsPer100||0) > 10) fixes.push('Reduce numeric density; round or group numbers where possible.');
    if (ease < 60 && fixes.length === 0) fixes.push('Aim for shorter sentences and simpler vocabulary to improve readability.');
    var list = document.getElementById('readSuggest');
    if (list){ list.innerHTML = fixes.length ? fixes.map(f=>`<li>${f}</li>`).join('') : '<li>Looks good! Keep sentences concise and headings clear.</li>'; }

    // Answer Target (45–55 words)
    var best = ''; (s.sentences||[]).forEach(function(sent){
      var wc = (sent.match(/[A-Za-z\u00C0-\u024f0-9']+/g)||[]).length;
      if (wc>=40 && wc<=60 && (!best || wc>best.split(' ').length)) best=sent.trim();
    });
    if (!best && s.sentences && s.sentences.length){
      best = s.sentences.sort((a,b)=>Math.abs(48-(a.split(' ').length))-Math.abs(48-(b.split(' ').length)))[0] || '';
    }
    var plain = document.getElementById('readPlain'); if (plain) plain.textContent = best || 'No snippet-length paragraph detected. Try a 45–55 word definition/answer paragraph near the top.';
    p.style.display='block';
  }

  /* === Entities salience & rendering === */
  function extractEntities(text, htmlDoc){
    var res = {people:[], orgs:[], places:[], topics:[], software:[], games:[], salience:{}};
    var clean=(text||'').replace(/\s+/g,' ');
    var cand = (clean.match(/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+){0,3})\b/g) || []).slice(0, 800);
    var stop = new Set(['The','A','An','This','That','And','Or','Of','In','On','To','For','By','With','Your','Our','You','We','It','At','From','As','Be','Is','Are','Was','Were','Not']);
    var uniq={};
    cand.forEach(function(c){ if(stop.has(c)) return; var k=c.trim(); if(k.length<2||k.length>48) return; uniq[k]=(uniq[k]||0)+1; });
    var uniqList = Object.keys(uniq).slice(0,120);

    // proximity weights
    var weights={}; if (htmlDoc){
      var h1 = htmlDoc.querySelector('h1')?.textContent||'';
      var headers = [].slice.call(htmlDoc.querySelectorAll('h1,h2,h3'));
      uniqList.forEach(function(n){
        var w = 0;
        if (h1.includes(n)) w += 3;
        headers.forEach(function(h){ if((h.textContent||'').includes(n)) w += 1; });
        if (htmlDoc.querySelector('a[href*="'+(n.split(' ')[0]||'')+'"]')) w += 0.5;
        weights[n] = w;
      });
    }

    uniqList.forEach(function(n){
      if (/\b(Inc|LLC|Ltd|Corporation|Company|Corp|Studio|Labs|University|College)\b/.test(n)) res.orgs.push(n);
      else if (/\b(City|Town|Province|State|Country|Park|River|Lake|Valley|Mountain)\b/.test(n)) res.places.push(n);
      else if (/\b(Mr|Mrs|Ms|Dr|Prof)\b/.test(n) || n.split(' ').length>=2) res.people.push(n);
      else res.topics.push(n);
    });

    // software / apk / games
    var low = clean.toLowerCase();
    var soft = (clean.match(/\b([A-Z][A-Za-z0-9\.\-\+]{2,})\b/g) || []).filter(x=>/\b(Android|iOS|Windows|Mac|Linux|Pro|Studio|Editor|App|SDK|Tool)\b/.test(x) || /v?\d+\.\d+/.test(x));
    if (/\bapk\b/i.test(low) || /\.apk\b/i.test(low)){ soft.push('APK'); }
    var games = (clean.match(/\b([A-Z][A-Za-z0-9\-\s]{2,} (?:Game|Games|Edition|Remastered|Online))\b/g) || []);

    res.software = Array.from(new Set(soft)).slice(0,20);
    res.games = Array.from(new Set(games)).slice(0,20);
    res.people = res.people.slice(0,20);
    res.orgs = res.orgs.slice(0,20);
    res.places = res.places.slice(0,20);
    res.topics = res.topics.slice(0,24);

    // salience score (freq + proximity emphasis)
    var sal = {};
    [].concat(res.people,res.orgs,res.places,res.topics).forEach(function(n){
      var f = uniq[n]||0, w = weights[n]||0;
      sal[n] = f*1 + w*2;
    });
    // normalize 0-100
    var vals = Object.values(sal); var max = vals.length? Math.max.apply(null, vals) : 0;
    var norm={}; Object.keys(sal).forEach(k=>{ norm[k] = max? Math.round((sal[k]/max)*100) : 0; });
    res.salience = norm;
    return res;
  }
  function chipifyWithSal(list, cls, sal){
    if(!list || !list.length) return '<span class="echip misc"><i class="fa-solid fa-circle-minus"></i> none</span>';
    var primary = list.slice().sort((a,b)=>(sal[b]||0)-(sal[a]||0))[0];
    return list.map(v=>{
      var crown = v===primary ? ' <i class="fa-solid fa-crown"></i>' : '';
      var s = sal && isFinite(sal[v]) ? `<span class="sal">${sal[v]}%</span>` : '';
      return `<span class="echip ${cls||'misc'}"><i class="fa-solid fa-tag"></i> ${v}${crown} ${s}</span>`;
    }).join(' ');
  }
  function renderEntitiesTopics(sample, htmlDoc){
    var p = document.getElementById('entitiesPanel'); if(!p) return;
    var ex = extractEntities(sample||'', htmlDoc||null);
    var m = (id, html)=>{ var el=document.getElementById(id); if(el) el.innerHTML=html; };
    m('entPeople', chipifyWithSal(ex.people,'person',ex.salience));
    m('entOrgs', chipifyWithSal(ex.orgs,'org',ex.salience));
    m('entPlaces', chipifyWithSal(ex.places,'place',ex.salience));
    m('entTopics', chipifyWithSal(ex.topics,'misc',ex.salience));
    m('entSoftware', chipifyWithSal(ex.software,'sw',ex.salience));
    m('entGames', chipifyWithSal(ex.games,'game',ex.salience));
    p.style.display='block';
    return ex;
  }

  /* === Topic Coverage === */
  function buildTopicCoverage(htmlDoc, sample){
    var res = { primary:'—', covered:[], missing:[], coveragePct:0 };
    if (!htmlDoc && !sample) return res;
    var h1 = (htmlDoc?.querySelector('h1')?.textContent||'').trim();
    var seeds = [];
    if (h1) seeds.push(h1);
    [].slice.call(htmlDoc?.querySelectorAll('h2,h3')||[]).forEach(h=>{ var t=(h.textContent||'').trim(); if(t) seeds.push(t);});
    if (!seeds.length && sample){ seeds = (sample.match(/[^\n]{14,80}/g)||[]).slice(0,8); }
    var primary = h1 || (seeds[0]||'').split(/[|–\-:•]/)[0].trim();
    var text = (sample||'').toLowerCase();
    var candidates = Array.from(new Set(seeds
      .map(s=> s.replace(/[#?«»"“”'()]+/g,'').trim())
      .filter(Boolean)
      .slice(0,18)));

    var covered=[], missing=[];
    candidates.forEach(function(c){
      var token = c.toLowerCase().split(/[|–\-:•]/)[0].trim();
      if (!token || token.length<3) return;
      if (text.indexOf(token) !== -1) covered.push(c);
      else missing.push(c);
    });
    var pct = candidates.length? Math.round(covered.length*100/candidates.length) : 0;
    res.primary= primary||'—'; res.covered=covered; res.missing=missing; res.coveragePct=pct;
    return res;
  }
  function renderTopicCoverage(tc){
    var p=document.getElementById('topicsPanel'); if(!p) return;
    setText('topicPrimary', tc.primary || '—');
    setText('topicCoveragePct', isFinite(tc.coveragePct)? tc.coveragePct : '—');
    setText('topicCoveredCount', tc.covered.length);
    setText('topicMissingCount', tc.missing.length);
    var fill = (arr)=> arr.length? arr.map(t=>`<span class="tchip"><i class="fa-solid fa-check"></i> ${t}</span>`).join(' ') : '<span class="tchip">—</span>';
    var miss = (arr)=> arr.length? arr.map(t=>`<span class="tchip"><i class="fa-solid fa-plus"></i> ${t}</span>`).join(' ') : '<span class="tchip">none</span>';
    var c=document.getElementById('topicsCovered'); if(c) c.innerHTML = fill(tc.covered);
    var m=document.getElementById('topicsMissing'); if(m) m.innerHTML = miss(tc.missing);
    p.style.display='block';
  }

  /* === Snippet width & preview (px measurement) === */
  function measurePx(txt, sizePx, family){
    try{
      var canvas = measurePx._c || (measurePx._c=document.createElement('canvas'));
      var ctx = canvas.getContext('2d');
      ctx.font = sizePx + 'px ' + (family||'Arial, Helvetica, sans-serif');
      return Math.round(ctx.measureText(txt||'').width);
    }catch(_){ return 0; }
  }
  function renderSnippetPreview(url, meta){
    var t = meta.title||'', d = meta.metaDesc||'';
    var tPx = measurePx(t, 18, 'Arial');
    var dPx = measurePx(d, 14, 'Arial');
    setText('rTitlePx', t ? `• ~${tPx}px` : '');
    setText('rMetaPx',  d ? `• ~${dPx}px` : '');
    var p = document.getElementById('serpPreview');
    if(!p) return;
    var host = ''; try{ host=new URL(url).host; }catch(_){}
    setText('snipTitle', t || 'No title found');
    setText('snipUrl', host || url);
    setText('snipDesc', d || 'No meta description found.');
    p.style.display='block';
  }

  /* === Guardrails (canonical/robots/indexability) === */
  function guardrails(meta, url){
    var issues=[];
    var canon = meta.canonical||'';
    try{
      var u = new URL(url), c = new URL(canon, url);
      if (c.origin+c.pathname !== u.origin+u.pathname) issues.push('Canonical not self-referencing (check if correct).');
    }catch(e){
      if (!canon) issues.push('Missing canonical tag.');
    }
    var robots = (meta.robots||'').toLowerCase();
    if (robots.includes('noindex')) issues.push('Robots meta contains noindex (page won’t be indexed).');
    if (robots.includes('nofollow')) issues.push('Robots meta contains nofollow (links won’t be followed).');
    if (!/width=device-width/i.test(meta.viewport||'')) issues.push('Viewport missing or not mobile friendly.');
    if ((meta.titleLen||0) > 65) issues.push('Title may truncate in SERP; aim ~50–60 chars / ~580px.');
    if ((meta.metaLen||0) > 165) issues.push('Meta description may truncate; aim ~140–160 chars.');
    return issues;
  }
  function renderGuardrails(issues){
    var box=document.getElementById('guardrails'), list=document.getElementById('guardrailsList');
    if(!box||!list) return; if(!issues || !issues.length){ box.style.display='none'; return; }
    list.innerHTML = issues.map(i=>`<li>${i}</li>`).join('');
    box.style.display='block';
  }

  /* === FAQ extractor + Schema === */
  function extractFAQ(htmlDoc, sample){
    var qas=[];
    if (htmlDoc){
      var blocks=[].slice.call(htmlDoc.querySelectorAll('h2,h3,strong'));
      blocks.forEach(function(b){
        var t=(b.textContent||'').trim();
        if(/\?$/.test(t)){
          var ans = b.nextElementSibling && /p|div|li/i.test(b.nextElementSibling.tagName) ? (b.nextElementSibling.textContent||'').trim() : '';
          if (t && ans) qas.push({q:t, a:ans});
        }
      });
    }
    if (!qas.length && sample){
      var lines = sample.split(/\n+/).filter(Boolean);
      for(var i=0;i<lines.length-1;i++){
        if (/\?$/.test(lines[i]) && lines[i+1] && lines[i+1].length>20){ qas.push({q:lines[i].trim(), a:lines[i+1].trim()}); }
        if (qas.length>=8) break;
      }
    }
    // dedupe & clamp
    var seen={}; qas = qas.filter(x=>{ var k=x.q.toLowerCase(); if(seen[k]) return false; seen[k]=1; return true; }).slice(0,10);
    return qas;
  }
  function schemaJSON(meta, url, faqList){
    var now = new Date().toISOString();
    var host=''; try{ host=(new URL(url)).origin; }catch(_){}
    var article = {
      "@context":"https://schema.org",
      "@type":"Article",
      "headline": meta.title || "",
      "description": meta.metaDesc || "",
      "datePublished": meta.datePub || now,
      "dateModified": now,
      "author": meta.author ? {"@type":"Person","name":meta.author} : undefined,
      "mainEntityOfPage": {"@type":"WebPage","@id": url}
    };
    var faqs = faqList && faqList.length ? {
      "@context":"https://schema.org",
      "@type":"FAQPage",
      "mainEntity": faqList.map(x=>({"@type":"Question","name":x.q,"acceptedAnswer":{"@type":"Answer","text":x.a}}))
    } : {};
    var crumbs = {
      "@context":"https://schema.org",
      "@type":"BreadcrumbList",
      "itemListElement":[
        {"@type":"ListItem","position":1,"name":host.replace(/^https?:\/\//,''),"item":host},
        {"@type":"ListItem","position":2,"name":(meta.title||'Page'),"item":url}
      ]
    };
    var org = {
      "@context":"https://schema.org",
      "@type":"Organization",
      "name": host.replace(/^https?:\/\//,''),
      "url": host,
      "sameAs":[]
    };
    var search = {
      "@context":"https://schema.org",
      "@type":"WebSite",
      "url": host,
      "potentialAction":{
        "@type":"SearchAction",
        "target": host+"/?s={search_term_string}",
        "query-input":"required name=search_term_string"
      }
    };
    function tidy(o){ // remove empty keys
      if (!o || typeof o!=='object') return o;
      Object.keys(o).forEach(k=>{ if(o[k]===undefined || o[k]==='' || (Array.isArray(o[k]) && !o[k].length)) delete o[k]; });
      return o;
    }
    return {
      article: JSON.stringify(tidy(article), null, 2),
      faq: Object.keys(faqs).length? JSON.stringify(faqs,null,2) : '{}',
      breadcrumbs: JSON.stringify(crumbs, null, 2),
      org: JSON.stringify(org, null, 2),
      search: JSON.stringify(search, null, 2)
    };
  }
  function renderSchemaBlocks(jsons){
    setText('schemaArticle', jsons.article||'{}');
    setText('schemaFAQ', jsons.faq||'{}');
    setText('schemaBreadcrumbs', jsons.breadcrumbs||'{}');
    setText('schemaOrg', jsons.org||'{}');
    setText('schemaSearch', jsons.search||'{}');
    var p = document.getElementById('schemaPanel'); if(p) p.style.display='block';
  }

  /* === Internal Link Suggestions === */
  function suggestInternalLinks(htmlDoc, ex, url){
    var items=[];
    if (!htmlDoc) return items;
    var origin=''; try{ origin=new URL(url).origin; }catch(_){}
    var anchors=[].slice.call(htmlDoc.querySelectorAll('a[href]')).map(a=>{
      try{ var u = new URL(a.getAttribute('href'), url); return u.origin===origin? u.pathname : null; }catch(e){ return null; }
    }).filter(Boolean);
    var uniqPaths = Array.from(new Set(anchors)).filter(p=>p!=='/' && p.length<120).slice(0,80);

    // Build candidate anchors from entities/topics
    var cands = [].concat(ex.topics||[], ex.orgs||[], ex.people||[]).slice(0,20);
    uniqPaths.slice(0,20).forEach(function(pth, i){
      var anchor = cands[i % cands.length] || pth.split('/').pop().replace(/[-_]/g,' ');
      var reason = 'Topical match via entities/topics';
      items.push({anchor: anchor, target: pth, reason: reason});
    });
    return items.slice(0,10);
  }
  function renderInternalLinks(items){
    var panel=document.getElementById('linksPanel'); var body=document.getElementById('linksBody');
    if(!panel||!body) return;
    if(!items || !items.length){ panel.style.display='none'; return; }
    body.innerHTML = items.map(x=>`<tr class="links-row"><td>${x.anchor}</td><td>${x.target}</td><td>${x.reason}</td></tr>`).join('');
    panel.style.display='block';
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
    ['detectorPanel','readabilityPanel','entitiesPanel','topicsPanel','schemaPanel','linksPanel','psiPanel'].forEach(id=>{ var el=document.getElementById(id); if(el) el.style.display='none'; });

    // 1) Backend (if present)
    var {ok,data} = await fetchBackend(url);
    if(!data) data = {};

    // 2) Build sample (from backend)
    var sample = buildSampleFromData(data);

    // 3) Try AllOrigins raw HTML (fills meta chips + better sample if needed)
    var meta={}; var raw=''; var htmlDoc=null;
    try{
      raw = await fetchRawHtml(url);
      if(raw){
        var mx = extractMetaFromHtml(raw, url);
        meta = mx; data = mergeMeta(data, mx);
        htmlDoc = mx.doc || null;
        if((!sample || sample.length<200) && mx.sampleText) sample = mx.sampleText;
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

    // === E-E-A-T heuristics boost (auto-score 10,12,24,25 a bit if detected)
    var eatBoost = 0;
    if ((meta.author||'').trim()) eatBoost += 8;         // Author found
    if ((meta.datePub||'').trim()) eatBoost += 6;        // Date found
    if ((meta.externalLinks||0) > 2) eatBoost += 6;      // Citations / outbound
    if ((meta.schema||'')==='yes') eatBoost += 6;        // Some schema present
    data.itemScores = data.itemScores || {};
    [10,12,24,25].forEach(k=>{ var base=Number(data.itemScores[k]||60); data.itemScores[k] = clamp(base + eatBoost, 0, 100); });

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
    setText('rAltCov',    (data.altCov!==undefined && data.altCov!==null) ? (data.altCov+'%') : '—');

    // Snippet simulator preview
    renderSnippetPreview(url, data);

    // Guardrails
    renderGuardrails(guardrails(data, url));

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
    }

    // Readability + Answer Target
    var S = (ensemble && ensemble._s) ? ensemble._s : _prep(sample||'');
    renderReadability(S);

    // Entities (with salience) + Topic Coverage + Internal Links
    var ex = renderEntitiesTopics(sample||'', htmlDoc||null);
    var tc = buildTopicCoverage(htmlDoc||null, sample||''); renderTopicCoverage(tc);
    var links = suggestInternalLinks(htmlDoc||null, ex||{}, url); renderInternalLinks(links);

    // FAQ + Schema
    var faqList = extractFAQ(htmlDoc||null, sample||'');
    var jsons = schemaJSON(data||{}, url, faqList);
    renderSchemaBlocks(jsons);

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

  // Copy buttons (schema + quick)
  document.addEventListener('click', function(e){
    var t = e.target.closest('[data-copy]'); if(!t) return;
    var id = t.getAttribute('data-copy'); var el = document.getElementById(id); if(!el) return;
    var txt = el.innerText || el.textContent || '';
    navigator.clipboard?.writeText(txt).then(()=>{ t.textContent='Copied!'; setTimeout(()=>{ t.innerHTML='<i class="fa-regular fa-copy"></i> Copy'; },1200); });
  });

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
      ['detectorPanel','readabilityPanel','entitiesPanel','topicsPanel','schemaPanel','linksPanel','psiPanel'].forEach(id=>{ var p=document.getElementById(id); if(p) p.style.display='none'; });
      var sPrev=document.getElementById('serpPreview'); if(sPrev) sPrev.style.display='none';
      var g=document.getElementById('guardrails'); if(g) g.style.display='none';
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
/* === Readability "Good" Trophy Badge Toggle === */
(function(){
  const container = document.getElementById('readabilityPanel');
  const chip = container && container.querySelector('#readChip');
  const badge = container && container.querySelector('#readGoodBadge');
  if(!container || !chip || !badge) return;

  const isGood = () => (chip.getAttribute('data-state') || '').toLowerCase() === 'good';

  const render = () => {
    const good = isGood();
    badge.hidden = !good;
    badge.classList.toggle('show', good);
  };

  // Initial render
  render();

  // Watch for changes to the chip
  const obs = new MutationObserver(render);
  obs.observe(chip, { attributes:true, attributeFilter:['class','data-state'], characterData:true, childList:true, subtree:true });

  // Optional helper for manual control
  window.showReadabilityCongrats = function(force){
    const good = (typeof force === 'boolean') ? force : isGood();
    badge.hidden = !good;
    badge.classList.toggle('show', good);
  };
})();
</script>


<script>
/* === Guardrails: severity mapping + all-clear badge + chip coloring === */
(function(){
  const panel = document.getElementById('guardrails');
  const list  = document.getElementById('guardrailsList');
  const badge = document.getElementById('guardBadge');
  if (!panel || !list || !badge) return;

  function classify(li){
    const t = (li.textContent || "").toLowerCase();
    // Heuristic keywords — adjust as needed
    const bad  = /(noindex|blocked by robots|disallow(ed)?|multiple canonical|duplicate|canonical loop|redirect chain|soft 404|broken|4\d\d|5\d\d)/i;
    const warn = /(missing canonical|no canonical|meta robots missing|redirect|non-200|mixed|inconsistent)/i;
    const ok   = /(self-referencing|indexable|200 ok|valid|present|ok)/i;

    if (bad.test(t))   li.dataset.sev = "error";
    else if (warn.test(t)) li.dataset.sev = "warn";
    else if (ok.test(t))   li.dataset.sev = "ok";
    else li.dataset.sev = "warn"; // default cautious
  }

  function refresh(){
    const items = Array.from(list.querySelectorAll('li'));
    items.forEach(classify);

    const hasError = items.some(li => li.dataset.sev === 'error');
    const hasWarn  = items.some(li => li.dataset.sev === 'warn');
    const visible  = panel.style.display !== 'none' && (panel.offsetParent !== null);
    const shouldShow = visible && items.length === 0; // show when no items => clean

    badge.hidden = !(shouldShow);
    badge.classList.toggle('show', shouldShow);
  }

  // Initial if already rendered
  refresh();

  // Observe list changes
  const obs = new MutationObserver(refresh);
  obs.observe(list, { childList:true, subtree:true, characterData:true });

  // Canonical/Robots chip coloring
  const canonB = document.getElementById('rCanonical');
  const robotsB = document.getElementById('rRobots');
  const canonChip = canonB && canonB.closest('.chip');
  const robotsChip = robotsB && robotsB.closest('.chip');

  function colorChip(el, val){
    if(!el) return;
    el.classList.remove('ok','warn','bad');
    const s = (val||"").toLowerCase();
    if (/noindex|nofollow|disallow|blocked/.test(s)) el.classList.add('bad');
    else if (/^https?:\/\//.test(s) || /self/.test(s) || /present|ok/.test(s)) el.classList.add('ok');
    else el.classList.add('warn');
  }

  function tick(){
    colorChip(canonChip, canonB && canonB.textContent);
    colorChip(robotsChip, robotsB && robotsB.textContent);
  }
  tick();

  const obs2 = new MutationObserver(tick);
  if (canonB) obs2.observe(canonB, { childList:true, characterData:true, subtree:true });
  if (robotsB) obs2.observe(robotsB, { childList:true, characterData:true, subtree:true });

  // Expose manual refresh if needed
  window.refreshGuardrailsUI = refresh;
})();
</script>


<script>
/* === Human vs AI Ensemble v2.2 — trimmed, weighted, calibrated === */
(function(){
  const panel = document.getElementById('detectorPanel');
  if(!panel) return;

  const humanVal = document.getElementById('hvaiHumanVal');
  const aiVal    = document.getElementById('hvaiAIVal');
  const humanFill= document.getElementById('hvaiHumanFill');
  const aiFill   = document.getElementById('hvaiAIFill');
  const conf     = document.getElementById('detConfidence');
  const badge    = document.getElementById('hvaiBadge');
  const verdictText = document.getElementById('hvaiVerdictText');
  const verdictHint = document.getElementById('hvaiVerdictHint');

  // Optional per-detector chips if present
  function upsertDetectorsRow(){
    let row = panel.querySelector('.hvai-detectors');
    if(!row){
      row = document.createElement('div');
      row.className = 'hvai-detectors';
      const meta = panel.querySelector('.hvai-meta') || panel;
      meta.appendChild(row);
    }
    return row;
  }
  const detRow = upsertDetectorsRow();

  // Helper: robust stats
  const clamp01 = x => Math.min(1, Math.max(0, x));
  function median(arr){ const a=[...arr].sort((x,y)=>x-y); const m=Math.floor(a.length/2); return a.length%2? a[m] : (a[m-1]+a[m])/2; }
  function trimmedMean(arr, p=0.15){
    if(arr.length===0) return 0;
    const a=[...arr].sort((x,y)=>x-y);
    const k=Math.floor(p*a.length);
    const b=a.slice(k, a.length-k || a.length);
    return b.reduce((s,v)=>s+v,0)/b.length;
  }
  function weightedMedian(values, weights){
    const arr = values.map((v,i)=>({v, w:weights[i]||1})).sort((a,b)=>a.v-b.v);
    const total = arr.reduce((s,o)=>s+o.w,0);
    let acc=0;
    for(const o of arr){ acc+=o.w; if(acc >= total/2) return o.v; }
    return arr.length? arr[arr.length-1].v : 0;
  }
  // Simple calibration: compress extremes a bit to reduce false 100%/0% flips
  function calibrateAI(p){
    // logistic-style squashing around 0.5; adjust gain for smoothness
    const gain = 0.8;
    return clamp01( 1/(1+Math.exp(-((p-0.5)*6*gain))) );
  }

  // Render UI with final probabilities [0..1]
  function render(finalAI, detectors){
    const aiPct = Math.round(finalAI*100);
    const humanPct = 100 - aiPct;
    aiVal && (aiVal.textContent = aiPct + '%');
    humanVal && (humanVal.textContent = humanPct + '%');
    aiFill && (aiFill.style.width = aiPct + '%');
    humanFill && (humanFill.style.width = humanPct + '%');

    // Confidence ~ agreement + number of sources
    const vals = detectors.map(d=>d.ai);
    const spread = (vals.length>1)? (Math.max(...vals)-Math.min(...vals)) : 0.5;
    const srcBonus = Math.min(1, detectors.length/4);
    const confScore = clamp01(1 - spread) * 0.7 + srcBonus * 0.3;
    conf && (conf.textContent = Math.round(confScore*100));

    // Verdict + badge
    let verdict='Mixed';
    let hint='Signals disagree — review content variety and structure.';
    let cls='hvai-verdict--mixed';
    if(finalAI <= 0.35){ verdict='Likely Human'; hint='High variation, natural phrasing, diverse sentence patterns.'; cls='hvai-verdict--human'; }
    else if(finalAI >= 0.65){ verdict='Likely AI'; hint='Low burstiness, uniform phrasing, detector agreement skewed to AI.'; cls='hvai-verdict--ai'; }
    verdictText && (verdictText.textContent = verdict);
    verdictHint && (verdictHint.textContent = hint);
    badge && (badge.hidden = false, badge.classList.add('show'));
    panel.classList.remove('hvai-verdict--human','hvai-verdict--ai','hvai-verdict--mixed');
    panel.classList.add(cls);

    // detector chips
    if(detRow){
      detRow.innerHTML = '';
      detectors.forEach(d=>{
        const el = document.createElement('span');
        el.className = 'det ' + (d.ok? 'ok': 'err');
        el.innerHTML = `<i class="fa-solid ${d.icon||'fa-wave-square'}"></i>${d.name}: <b>${Math.round(d.ai*100)}%</b>`;
        detRow.appendChild(el);
      });
    }
  }

  // Local heuristic fallback (no API): quick & noisy but better than nothing
  function localHeuristicAI(text){
    if(!text) return 0.5;
    const tokens = text.split(/\s+/).filter(Boolean);
    const sents = text.split(/[.!?]+\s+/);
    const avgLen = tokens.length / Math.max(1,sents.length);
    const uniqueFrac = new Set(tokens.map(t=>t.toLowerCase())).size / Math.max(1,tokens.length);
    const digitFrac = (text.match(/\d/g) || []).length / Math.max(1,text.length);
    const repeatPenalty = (text.match(/\b(\w+)\b(?=[\s\S]*\b\1\b)/gi) || []).length;

    let ai = 0.5;
    if(avgLen<14) ai -= 0.08; else if(avgLen>22) ai += 0.08;
    ai += (0.20 - uniqueFrac)*0.8; // lower uniqueness -> more AI-like
    ai += Math.max(0, 0.12 - digitFrac); // very few digits -> slightly AI-like
    ai += Math.min(0.12, repeatPenalty*0.01);
    return clamp01(ai);
  }

  // Compute ensemble from available detectors [{name, ai:[0..1], weight?, ok?}]
  function computeEnsemble(detectors){
    if(!detectors || detectors.length===0) return 0.5;
    const vals = detectors.map(d=>clamp01(d.ai));
    const weights = detectors.map(d=>Math.max(0.1, d.weight || 1));
    const tmean = trimmedMean(vals, 0.15);
    const wmed  = weightedMedian(vals, weights);
    const mix   = (tmean*0.55 + wmed*0.45);
    return calibrateAI(mix);
  }

  // Public hook others can call: window.updateHvai({ text, detectors: [{name, ai, weight, icon, ok}], preferLocal })
  window.updateHvai = function(input){
    const text = (input && input.text) || '';
    let detectors = (input && input.detectors) || [];
    const preferLocal = !!(input && input.preferLocal);

    // If no detectors or preferLocal, add heuristic
    if(preferLocal || detectors.length===0){
      detectors = detectors.filter(d=>!d || typeof d.ai!=='number' ? false : true);
      detectors.push({ name:'Local Heuristic', ai: localHeuristicAI(text), weight: 0.9, icon:'fa-wave-square', ok:true });
    }

    // normalize to [0..1], coerce
    detectors = detectors.map(d=>({ 
      name: d.name || 'Detector', 
      ai: clamp01(Number(d.ai||0.5)), 
      weight: Number(d.weight||1), 
      icon: d.icon, 
      ok: d.ok!==false 
    }));

    const finalAI = computeEnsemble(detectors);
    render(finalAI, detectors);
  };

  // If your backend writes to these globals, auto-render
  if(window.__hvaiBootstrap){
    try{ window.updateHvai(window.__hvaiBootstrap); }catch(e){}
  }
})();
</script>


<script>
// Back-compat bootstrap: if no one called updateHvai yet, try to infer from any existing numbers on page after load.
window.addEventListener('load', function(){
  if(typeof window.updateHvai !== 'function') return;
  const txt = (document.getElementById('contentRaw') || {}).textContent || '';
  // If bars are still zero, nudge with heuristic so UI isn’t empty
  const human = document.getElementById('hvaiHumanFill');
  const ai    = document.getElementById('hvaiAIFill');
  if(human && ai && human.style.width==='0%' && ai.style.width==='0%'){
    window.updateHvai({ text: txt, detectors: [], preferLocal: true });
  }
});
</script>


<script>
/* === Human vs AI Ensemble v2.3 — ultra-stylish + robust math + gauge === */
(function(){
  const panel = document.getElementById('detectorPanel');
  if(!panel) return;

  const sel = id => document.getElementById(id);
  const humanVal = sel('hvaiHumanVal');
  const aiVal    = sel('hvaiAIVal');
  const humanFill= sel('hvaiHumanFill');
  const aiFill   = sel('hvaiAIFill');
  const conf     = sel('detConfidence');
  const badge    = sel('hvaiBadge');
  const verdictText = sel('hvaiVerdictText');
  const verdictHint = sel('hvaiVerdictHint');

  // gauge elements
  const arcHuman = sel('hvaiHumanArc');
  const arcAI    = sel('hvaiAIArc');
  const scoreBig = sel('hvaiScoreBig');

  // ensure detector chips row exists
  function upsertDetectorsRow(){
    let row = panel.querySelector('.hvai-detectors');
    if(!row){
      row = document.createElement('div');
      row.className = 'hvai-detectors';
      const meta = panel.querySelector('.hvai-meta') || panel;
      meta.appendChild(row);
    }
    return row;
  }
  const detRow = upsertDetectorsRow();

  const clamp01 = x => Math.min(1, Math.max(0, x));
  const sum = arr => arr.reduce((s,v)=>s+v,0);
  function median(arr){ const a=[...arr].sort((x,y)=>x-y); const m=Math.floor(a.length/2); return a.length%2? a[m] : (a[m-1]+a[m])/2; }
  function trimmedMean(arr, p=0.15){ if(arr.length===0) return 0; const a=[...arr].sort((x,y)=>x-y); const k=Math.floor(p*a.length); const b=a.slice(k, a.length-k || a.length); return sum(b)/b.length; }
  function weightedMedian(values, weights){ const arr = values.map((v,i)=>({v, w:weights[i]||1})).sort((a,b)=>a.v-b.v); const total = sum(arr.map(o=>o.w)); let acc=0; for(const o of arr){ acc+=o.w; if(acc >= total/2) return o.v; } return arr.length? arr[arr.length-1].v : 0; }
  function calibrateAI(p){ const gain=0.8; return clamp01( 1/(1+Math.exp(-((p-0.5)*6*gain))) ); }

  function localHeuristicAI(text){
    if(!text) return 0.5;
    const tokens = text.split(/\s+/).filter(Boolean);
    const sents = text.split(/[.!?]+\s+/);
    const avgLen = tokens.length / Math.max(1,sents.length);
    const uniqueFrac = new Set(tokens.map(t=>t.toLowerCase())).size / Math.max(1,tokens.length);
    const digitFrac = (text.match(/\d/g) || []).length / Math.max(1,text.length);
    const repeatPenalty = (text.match(/\b(\w+)\b(?=[\s\S]*\b\1\b)/gi) || []).length;

    let ai = 0.5;
    if(avgLen<14) ai -= 0.08; else if(avgLen>22) ai += 0.08;
    ai += (0.20 - uniqueFrac)*0.8;
    ai += Math.max(0, 0.12 - digitFrac);
    ai += Math.min(0.12, repeatPenalty*0.01);
    return clamp01(ai);
  }

  function computeEnsemble(detectors){
    if(!detectors || detectors.length===0) return 0.5;
    const vals = detectors.map(d=>clamp01(d.ai));
    const weights = detectors.map(d=>Math.max(0.1, d.weight || 1));
    const tmean = trimmedMean(vals, 0.15);
    const wmed  = weightedMedian(vals, weights);
    const mix   = (tmean*0.55 + wmed*0.45);
    return calibrateAI(mix);
  }

  function updateGauge(finalAI){
    const ai = clamp01(finalAI);
    const human = 1 - ai;
    const setArc = (el, r, pct) => {
      if(!el) return;
      const c = 2 * Math.PI * r;
      const on = c * pct;
      const off = Math.max(0.0001, c - on);
      el.style.strokeDasharray = `${on} ${off}`;
      el.style.strokeDashoffset = "0";
    };
    setArc(arcAI, 86, ai);
    setArc(arcHuman, 66, human);
    if(scoreBig) scoreBig.textContent = Math.round(ai*100) + '%';
  }

  function render(finalAI, detectors){
    const aiPct = Math.round(finalAI*100);
    const humanPct = 100 - aiPct;
    aiVal && (aiVal.textContent = aiPct + '%');
    humanVal && (humanVal.textContent = humanPct + '%');
    aiFill && (aiFill.style.width = aiPct + '%');
    humanFill && (humanFill.style.width = humanPct + '%');

    // Confidence
    const vals = detectors.map(d=>d.ai);
    const spread = (vals.length>1)? (Math.max(...vals)-Math.min(...vals)) : 0.5;
    const srcBonus = Math.min(1, detectors.length/4);
    const confScore = clamp01(1 - spread) * 0.7 + srcBonus * 0.3;
    conf && (conf.textContent = Math.round(confScore*100));

    // Verdict + badge
    let verdict='Mixed';
    let hint='Signals disagree — review content variety and structure.';
    let cls='hvai-verdict--mixed';
    if(finalAI <= 0.35){ verdict='Likely Human'; hint='High variation, natural phrasing, diverse sentence patterns.'; cls='hvai-verdict--human'; }
    else if(finalAI >= 0.65){ verdict='Likely AI'; hint='Low burstiness, uniform phrasing, detector agreement skewed to AI.'; cls='hvai-verdict--ai'; }
    verdictText && (verdictText.textContent = verdict);
    verdictHint && (verdictHint.textContent = hint);
    badge && (badge.hidden = false, badge.classList.add('show'));
    panel.classList.remove('hvai-verdict--human','hvai-verdict--ai','hvai-verdict--mixed');
    panel.classList.add(cls);

    // detector chips
    const row = detRow;
    if(row){
      row.innerHTML = '';
      detectors.forEach(d=>{
        const el = document.createElement('span');
        el.className = 'det ' + (d.ok? 'ok': 'err');
        el.innerHTML = `<i class="fa-solid ${d.icon||'fa-wave-square'}"></i>${d.name}: <b>${Math.round(d.ai*100)}%</b>`;
        row.appendChild(el);
      });
    }
    // gauge
    updateGauge(finalAI);
  }

  // Override global function with v2.3
  window.updateHvai = function(input){
    const text = (input && input.text) || '';
    let detectors = (input && input.detectors) || [];
    const preferLocal = !!(input && input.preferLocal);

    if(preferLocal || detectors.length===0){
      detectors = detectors.filter(d=>d && typeof d.ai==='number');
      detectors.push({ name:'Local Heuristic', ai: localHeuristicAI(text), weight: 0.9, icon:'fa-wave-square', ok:true });
    }else{
      detectors = detectors.filter(d=>d && typeof d.ai==='number');
    }

    detectors = detectors.map(d=>({ 
      name: d.name || 'Detector', 
      ai: clamp01(Number(d.ai||0.5)), 
      weight: Number(d.weight||1), 
      icon: d.icon, 
      ok: d.ok!==false 
    }));

    const finalAI = computeEnsemble(detectors);
    render(finalAI, detectors);
  };

  // If bootstrap object exists, re-render with the new version
  if(window.__hvaiBootstrap){
    try{ window.updateHvai(window.__hvaiBootstrap); }catch(e){}
  }
})();
</script>

</body>
</html>
