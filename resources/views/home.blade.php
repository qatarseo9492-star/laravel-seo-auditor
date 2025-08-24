{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  $siteName = config('app.name', 'Semantic SEO Master');
  $metaTitle = $metaTitle ?? 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = $metaDescription ?? 'Analyze any URL for content quality, entities, technical SEO, and UX signals, with water-fill scoring, auto-checklist, and AI/Human signals.';
  $metaImage = $metaImage ?? asset('og-image.png');
  $canonical = $canonical ?? url()->current();
  $twitterHandle = '@UltraTechGlobal';
@endphp

<title>{{ $metaTitle }}</title>

<link rel="canonical" href="{{ $canonical }}">
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<link rel="alternate" hreflang="en" href="{{ $canonical }}"/>
<meta name="keywords" content="SEO analyzer, semantic SEO, content score, technical SEO, entities, E-E-A-T, schema, Core Web Vitals">
<meta name="author" content="Ultra Tech Global">
<meta name="publisher" content="Ultra Tech Global">
<meta name="theme-color" content="#0f1022">
<meta name="application-name" content="{{ $siteName }}">

<link rel="manifest" href="{{ asset('site.webmanifest') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="msapplication-TileColor" content="#0f1022">

<meta property="og:locale" content="en_US">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:site_name" content="{{ $siteName }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="{{ $twitterHandle }}">
<meta name="twitter:creator" content="{{ $twitterHandle }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">

<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
:root{
  --bg:#07080e; --panel:#0f1022; --panel-2:#141433; --line:#1e1a33;
  --text:#f0effa; --text-dim:#b6b3d6; --text-muted:#9aa0c3;
  --primary:#9b5cff; --secondary:#ff2045; --accent:#3de2ff;
  --good:#22c55e; --warn:#f59e0b; --bad:#ef4444;
  --radius:18px; --shadow:0 10px 40px rgba(0,0,0,.55);
  --container:1200px;
  --hue: 0deg;
}
*{box-sizing:border-box} html,body{height:100%}
html{scroll-behavior:smooth}
body{
  margin:0; color:var(--text);
  font-family:Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto;
  background: radial-gradient(1200px 700px at 0% -10%, #201046 0%, transparent 55%),
              radial-gradient(1100px 800px at 110% 0%, #1a0f2a 0%, transparent 50%),
              var(--bg);
  overflow-x:hidden;
}

/* --- Cloudy background smoke layers (visible) --- */
#linesCanvas, #brainCanvas{ position:fixed; inset:0; pointer-events:none; z-index:0; }
#linesCanvas{ opacity:.35; }
#brainCanvas{ opacity:.28; }

/* Layout */
.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}

/* Header */
header.site{
  display:flex; align-items:center; justify-content:space-between;
  gap:1rem; padding:14px 0 22px; border-bottom:1px solid rgba(255,255,255,.08);
}
.brand{ display:flex; align-items:center; gap:.8rem; min-width:0 }
.brand-badge{
  width:48px; height:48px; border-radius:12px; display:grid; place-items:center;
  background:linear-gradient(135deg, rgba(157,92,255,.25), rgba(61,226,255,.25));
  border:1px solid rgba(255,255,255,.14); color:#fff; font-size:1.05rem;
  box-shadow:0 8px 22px rgba(0,0,0,.28);
}
.brand-info{ display:flex; flex-direction:column; min-width:0 }
.hero-heading{ font-weight:1000; letter-spacing:.4px; font-size:clamp(1.4rem,3.2vw,2rem) }
.hero-sub{ color:var(--text-dim); font-size:.95rem }
.header-actions{ display:flex; gap:.5rem; align-items:center }

/* Buttons */
.btn{
  display:inline-flex; align-items:center; gap:.55rem; cursor:pointer;
  padding:.6rem .95rem; border-radius:14px; border:1px solid rgba(255,255,255,.16);
  color:#fff; font-weight:900; letter-spacing:.2px; position:relative; overflow:hidden;
  box-shadow:0 10px 28px rgba(0,0,0,.25);
}
.btn::after{
  content:""; position:absolute; inset:-2px; border-radius:inherit; opacity:.0;
  background:linear-gradient(120deg, transparent, rgba(255,255,255,.22), transparent 60%);
  transform:translateX(-120%); transition:opacity .2s ease;
}
.btn:hover::after{ opacity:1; animation:btnSweep 2.6s linear infinite; }
@keyframes btnSweep{ 0%{transform:translateX(-120%)} 100%{transform:translateX(120%)} }

.btn-analyze{ background:linear-gradient(135deg, #10b981, #22c55e); border-color:#20d391; }
.btn-print  { background:linear-gradient(135deg, #3b82f6, #6366f1); border-color:#5b77ef; }
.btn-reset  { background:linear-gradient(135deg, #f59e0b, #f97316); border-color:#f59e0b; }
.btn-export { background:linear-gradient(135deg, #a855f7, #ec4899); border-color:#c26cf2; }
.btn-ghost  { background:rgba(255,255,255,.06); }
.btn:disabled{ opacity:.6; cursor:not-allowed }

.btn-analyze i{ color:#03291a }
.btn-print i{ color:#051735 }
.btn-reset i{ color:#2a1403 }
.btn-export i{ color:#2a0730 }

/* Analyzer shell */
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem}
.section-subtitle{margin:0;color:var(--text-dim)}

/* SCORE GAUGE */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-gauge{position:relative;width:100%;aspect-ratio:1/1}
.gauge-svg{width:100%;height:auto;display:block}
.score-mask-rect{transition:all .6s cubic-bezier(.22,1,.36,1)}
.score-wave1{animation:scoreWave 8s linear infinite}
.score-wave2{animation:scoreWave 11s linear infinite reverse}
@keyframes scoreWave{from{transform:translateX(0)}to{transform:translateX(-210px)}}
.score-text{font-size:clamp(2.2rem, 4.2vw, 3.1rem);font-weight:1000;fill:#fff;text-shadow:0 0 18px rgba(255,32,69,.25)}
.multiHueFast{ filter:hue-rotate(var(--hue)) saturate(140%); will-change:filter; }

/* Chips */
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)} .l-orange{background:rgba(245,158,11,.18)} .l-green{background:rgba(34,197,94,.18)}
.chip-good{background:rgba(34,197,94,.18)!important;border-color:rgba(34,197,94,.45)!important}
.chip-mid{background:rgba(245,158,11,.18)!important;border-color:rgba(245,158,11,.45)!important}
.chip-bad{background:rgba(239,68,68,.18)!important;border-color:rgba(239,68,68,.5)!important}
.ico{width:1.1em;text-align:center}
.ico-green{color:var(--good)} .ico-orange{color:var(--warn)} .ico-red{color:var(--bad)} .ico-cyan{color:var(--accent)} .ico-purple{color:#9b5cff}

/* URL input */
.url-field{
  position:relative; border-radius:16px; background:#0b0d21; border:1px solid #1b1b35;
  box-shadow:inset 0 0 0 1px rgba(255,255,255,.02), 0 12px 32px rgba(0,0,0,.32);
  padding:10px 110px 10px 46px; transition:.25s ease; overflow:hidden; isolation:isolate;
}
.url-field:focus-within{
  border-color:#5942ff; box-shadow:0 0 0 6px rgba(155,92,255,.15), inset 0 0 0 1px rgba(93,65,255,.28);
}
.url-field .url-icon{ position:absolute; left:14px; top:50%; transform:translateY(-50%); color:#9aa0c3; font-size:1rem; opacity:.95; }
.url-field input{ all:unset; color:var(--text); width:100%; font-size:1rem; letter-spacing:.2px; }
.url-field .url-mini{
  position:absolute; top:50%; transform:translateY(-50%);
  border:1px solid rgba(255,255,255,.16); background:rgba(255,255,255,.06); color:#fff;
  border-radius:10px; padding:.35rem .6rem; font-weight:900; cursor:pointer; transition:.15s;
}
.url-field .url-mini:hover{ background:rgba(255,255,255,.12) }
.url-field .url-clear{ right:60px; width:36px; height:32px; display:grid; place-items:center; }
.url-field #pasteUrl{ right:12px; }
.url-field .url-border{
  content:""; position:absolute; inset:-2px; border-radius:inherit; padding:2px;
  background:conic-gradient(from 0deg, #3de2ff, #9b5cff, #ff2045, #f59e0b, #3de2ff);
  -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite:xor; mask-composite:exclude; opacity:.55; pointer-events:none;
  filter:hue-rotate(var(--hue));
}

/* Form row */
.analyze-row{display:grid;grid-template-columns:1fr auto auto auto auto;gap:.6rem;align-items:center;margin-top:.6rem}

/* Analyze water progress */
.water-wrap{margin-top:.8rem;display:none}
.waterbar{position:relative; height:64px; border-radius:18px; overflow:hidden;background:#0b0d21; border:1px solid rgba(255,255,255,.1)}
.water-svg{position:absolute; inset:0; width:100%; height:100%; z-index:1;}
.water-mask-rect{transition:all .25s ease-out}
.water-overlay{position:absolute; inset:0; pointer-events:none; background:
  radial-gradient(120px 60px at 20% -20%, rgba(255,255,255,.18), transparent 60%),
  linear-gradient(0deg, rgba(255,255,255,.05), transparent 40%, transparent 60%, rgba(255,255,255,.06));
  mix-blend-mode:screen; z-index:2}
.water-pct{position:absolute; inset:0; display:grid; place-items:center; font-weight:1000; font-size:1.05rem; text-shadow:0 1px 0 rgba(0,0,0,.45); letter-spacing:.4px; z-index:4}
.wave1{animation:waveX 7s linear infinite}
.wave2{animation:waveX 10s linear infinite reverse; opacity:.7}
@keyframes waveX{0%{transform:translateX(0)}100%{transform:translateX(-600px)}}
.multiHue{filter:hue-rotate(var(--hue)) saturate(140%); will-change:filter;}
#waterSmoke{ position:absolute; inset:0; pointer-events:none; z-index:3; mix-blend-mode:screen; }

/* Completion bar */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.comp-water{position:relative; height:52px; border-radius:16px; overflow:hidden; background:#0b0d21; border:1px solid rgba(255,255,255,.1); }
.comp-svg{position:absolute; inset:0; width:100%; height:100%; z-index:1;}
.comp-overlay{position:absolute; inset:0; background:
  radial-gradient(120px 50px at 15% -25%, rgba(255,255,255,.16), transparent 55%),
  linear-gradient(180deg, rgba(255,255,255,.08), transparent 35%, rgba(255,255,255,.06));
  pointer-events:none; mix-blend-mode:screen; z-index:3;}
.comp-pct{position:absolute; inset:0; display:grid; place-items:center; font-weight:1000; font-size:1rem; z-index:4; text-shadow:0 1px 0 rgba(0,0,0,.45); }
#compSmoke{ position:absolute; inset:0; pointer-events:none; z-index:2; mix-blend-mode:screen; }
.comp-wave1{animation:waveX 8s linear infinite}
.comp-wave2{animation:waveX 12s linear infinite reverse}
.progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.55rem}

/* Category grid & cards + heading water */
.analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
.category-card{position:relative;grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px;box-shadow:var(--shadow);overflow:hidden; isolation:isolate;}
.category-card::before{content:"";position:absolute;inset:-2px;border-radius:18px;padding:2px;background:linear-gradient(120deg,rgba(61,226,255,.4),rgba(155,92,255,.4),rgba(255,32,69,.4));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;animation:borderGlow 6s linear infinite; pointer-events:none; z-index:0;}
.category-card > *{position:relative; z-index:1;}
@keyframes borderGlow{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(360deg)}}

.category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
.category-icon{width:48px;height:48px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3de2ff33,#9b5cff33);color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,.18)}
.category-title{margin:0;font-size:1.08rem;background:linear-gradient(90deg,#3de2ff,#9b5cff,#ff2045);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900}
.category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}

.cat-water{grid-column:1/-1; margin-top:.55rem; position:relative; height:22px;}
.cat-svg{display:block; width:100%; height:22px;}
.cat-wave1{animation:catWave 7s linear infinite}
.cat-wave2{animation:catWave 10s linear infinite reverse}
@keyframes catWave{from{transform:translateX(0)}to{transform:translateX(-640px)}}
.cat-water-pct{position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-weight:900; font-size:.8rem; color:rgba(255,255,255,.9); text-shadow:0 1px 0 rgba(0,0,0,.55); pointer-events:none}
.cat-smoke{position:absolute; left:0; right:0; bottom:0; height:26px; pointer-events:none; z-index:3; mix-blend-mode:screen;}

/* Checklist */
.checklist{list-style:none;margin:10px 0 0;padding:0}
.checklist-item{
  display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;
  padding:.7rem .75rem;border-radius:14px;
  border:1px solid rgba(255,255,255,.10);
  background:
    linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02)),
    radial-gradient(100% 120% at 0% 0%, rgba(61,226,255,.06), transparent 30%),
    radial-gradient(120% 100% at 100% 0%, rgba(155,92,255,.05), transparent 35%);
  transition: box-shadow .25s ease, background .25s ease, transform .12s ease;
}
.checklist-item + .checklist-item{margin-top:.28rem}
.checklist-item:hover{transform:translateY(-2px);box-shadow:0 8px 30px rgba(0,0,0,.25)}
.checklist-item label { cursor:pointer; display:inline-flex; align-items:center; gap:.55rem; }

.sev-good{ background:linear-gradient(180deg, rgba(34,197,94,.14), rgba(34,197,94,.08)); border-color: rgba(34,197,94,.45); }
.sev-mid { background:linear-gradient(180deg, rgba(245,158,11,.16), rgba(245,158,11,.08)); border-color: rgba(245,158,11,.45); }
.sev-bad { background:linear-gradient(180deg, rgba(239,68,68,.16), rgba(239,68,68,.10)); border-color: rgba(239,68,68,.55); }

/* Custom checkbox with animated gradient tick */
.checklist-item input[type="checkbox"]{
  appearance:none; -webkit-appearance:none; outline:none;
  width:22px;height:22px;border-radius:8px;
  background:#0b1220; border:2px solid #2a2f4d;
  position:relative; display:inline-grid; place-items:center;
  transition:.18s ease-in-out; box-shadow:inset 0 0 0 0 rgba(99,102,241,.0);
}
.checklist-item input[type="checkbox"]:hover{
  border-color:#4c5399; box-shadow:0 0 0 4px rgba(99,102,241,.12);
}
.checklist-item input[type="checkbox"]::after{
  content:""; width:7px;height:12px; border:3px solid transparent;
  border-left:0;border-top:0; transform:rotate(45deg) scale(.7);
  transition:.18s ease-in-out;
}
.checklist-item input[type="checkbox"]:checked{
  border-color:transparent;
  background:linear-gradient(135deg,#22c55e,#3de2ff,#9b5cff);
  background-size:200% 200%; animation:tickHue 2s linear infinite;
  box-shadow:0 6px 18px rgba(61,226,255,.25), inset 0 0 0 2px rgba(255,255,255,.25);
}
.checklist-item input[type="checkbox"]:checked::after{
  border-color:#fff; filter:drop-shadow(0 1px 0 rgba(0,0,0,.4));
  transform:rotate(45deg) scale(1);
}
@keyframes tickHue{0%{background-position:0% 50%}100%{background-position:200% 50%}}

/* Score badges + Improve button */
.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22); border-color:rgba(22,193,114,.45)}
.score-mid{ background:rgba(245,158,11,.22); border-color:rgba(245,158,11,.45)}
.score-bad{ background:rgba(239,68,68,.24); border-color:rgba(239,68,68,.5)}
.improve-btn{
  position:relative; overflow:hidden;
  padding:.45rem .8rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);
  background:linear-gradient(135deg,rgba(255,255,255,.06),rgba(255,255,255,.02));
  font-weight:900; cursor:pointer; transition:.2s; isolation:isolate; min-width:88px;
}
.improve-btn:hover{ transform:translateY(-1px); background:rgba(255,255,255,.1); }
.improve-btn::before{
  content:""; position:absolute; inset:-2px; border-radius:inherit; z-index:0;
  background:linear-gradient(120deg, transparent 0%, rgba(255,255,255,.18) 45%, transparent 50%, transparent 100%);
  transform:translateX(-120%); animation:btnSheen 3.2s linear infinite;
}
@keyframes btnSheen{ 0%{transform:translateX(-120%)} 60%{transform:translateX(120%)} 100%{transform:translateX(120%)} }
.flash-row{animation:rowFlash 900ms ease-out}
@keyframes rowFlash{
  0%{ background:linear-gradient(90deg,rgba(61,226,255,.20),rgba(155,92,255,.16)); }
  100%{ background:
        linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02)),
        radial-gradient(100% 120% at 0% 0%, rgba(61,226,255,.06), transparent 30%),
        radial-gradient(120% 100% at 100% 0%, rgba(155,92,255,.05), transparent 35%); }
}

/* Floating Share Dock */
.share-dock{
  position:fixed; right:16px; top:50%; transform:translateY(-50%);
  display:flex; flex-direction:column; gap:.5rem; z-index:85;
  background:rgba(10,12,28,.35); border:1px solid rgba(255,255,255,.12); border-radius:14px; padding:.5rem;
  backdrop-filter:blur(8px)
}
.share-btn{
  width:42px;height:42px;border-radius:12px;border:1px solid rgba(255,255,255,.16);
  display:grid;place-items:center;color:#fff; cursor:pointer; text-decoration:none; position:relative; overflow:hidden;
  transition:transform .15s ease, box-shadow .15s ease;
}
.share-btn:hover{ transform:translateY(-2px); box-shadow:0 10px 24px rgba(0,0,0,.35) }
.share-fb{ background:linear-gradient(135deg,#1877F2,#1e90ff) }
.share-x { background:linear-gradient(135deg,#111,#333) }
.share-ln{ background:linear-gradient(135deg,#0a66c2,#1a8cd8) }
.share-wa{ background:linear-gradient(135deg,#25D366,#128C7E) }
.share-em{ background:linear-gradient(135deg,#ef4444,#b91c1c) }

/* Footer */
footer.site{ margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)}
.footer-brand{display:flex;align-items:center;gap:.6rem}
.footer-brand .dot{width:8px;height:8px;border-radius:50%;background:linear-gradient(135deg,#3de2ff,#9b5cff)}
.footer-links a{color:var(--text-dim);margin-left:.9rem}
.footer-links a:hover{color:#fff;text-decoration:underline}
#backTop{position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none}
#backTop:hover{background:rgba(255,255,255,.12)}

/* Language panel */
.lang-dock{ position:fixed; left:18px; top:50%; transform:translateY(-50%); z-index:80; display:flex; flex-direction:column; gap:.6rem }
.lang-panel{ position:fixed; left:18px; top:50%; transform:translate(-6px,-50%); z-index:78; display:none; }
.lang-card{
  width:240px; max-height:70vh; overflow:auto; padding:.6rem;
  background:rgba(10,12,28,.65); border:1px solid rgba(255,255,255,.14);
  border-radius:14px; box-shadow:0 18px 60px rgba(0,0,0,.5); backdrop-filter:blur(10px);
}
.lang-item{ display:flex; align-items:center; gap:.55rem; padding:.46rem .55rem; border-radius:10px; cursor:pointer; border:1px solid transparent; color:#fff; }
.lang-item:hover{ background:rgba(255,255,255,.06); border-color:rgba(255,255,255,.12) }
.lang-item.active{ background:linear-gradient(135deg,#3de2ff33,#9b5cff33); border-color:rgba(255,255,255,.22) }
.lang-flag{ width:24px;height:24px;border-radius:50%;display:grid;place-items:center;background:rgba(255,255,255,.12);font-size:13px; }

/* Mobile */
@media (max-width:992px){
  .category-card{grid-column:span 12}
  .score-container{width:190px}
  footer.site{flex-direction:column;align-items:flex-start}
  .analyze-row{ grid-template-columns:1fr auto auto; grid-row-gap:.5rem; }
}
@media (max-width:768px){
  .wrap{padding:18px 4%}
  header.site{ flex-direction:column; align-items:flex-start; gap:.6rem }
  .score-area{flex-direction:column;align-items:flex-start;gap:.8rem}
  .score-container{width:170px}
  .analyze-row{ grid-template-columns:1fr; }
  .analyze-row .btn{ width:100%; justify-content:center; }
  .share-dock{ top:auto; bottom:10px; right:50%; transform:translateX(50%); flex-direction:row; padding:.35rem .45rem; border-radius:999px; gap:.4rem; background:rgba(10,12,28,.55); }
  .share-btn{ width:44px;height:44px;border-radius:999px }
  .share-native{ display:grid; }
  .checklist-item{ grid-template-columns:1fr auto auto; }
  .checklist-item .improve-btn{ grid-column: 1 / -1; justify-self:flex-start; margin-top:.25rem; }
}
@media (max-width:480px){
  .score-container{width:150px}
  .category-icon{width:40px;height:40px}
  .category-title{font-size:1rem}
}

/* Reduced motion / print */
@media (prefers-reduced-motion: reduce){
  .score-wave1,.score-wave2,.wave1,.wave2,.cat-wave1,.cat-wave2,.comp-wave1,.comp-wave2{ animation:none !important }
  .multiHue,.multiHueFast{ filter:none !important }
}
@media print{.modal-backdrop,.modal,.lang-dock,.share-dock,#backTop,#linesCanvas,#brainCanvas{display:none!important}}
</style>
</head>
<body>

<!-- Cloudy background canvases -->
<canvas id="linesCanvas"></canvas>
<canvas id="brainCanvas"></canvas>

<!-- SVG defs -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#9b5cff"/><stop offset="100%" stop-color="#ff2045"/></linearGradient>
    <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/></linearGradient>
    <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/></linearGradient>
    <linearGradient id="gradBad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/></linearGradient>
  </defs>
</svg>

<!-- Language Dock + Panel -->
<div class="lang-dock">
  <button class="btn btn-ghost" id="langOpen" title="Language" style="width:48px;height:48px;border-radius:12px;"><i class="fa-solid fa-globe"></i></button>
</div>
<div class="lang-panel" id="langPanel"><div class="lang-card" id="langCard"></div></div>

<!-- Share Dock -->
<div class="share-dock" id="shareDock" aria-label="Share">
  <a id="shareFb" class="share-btn share-fb" aria-label="Share on Facebook" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-facebook-f"></i></a>
  <a id="shareX"  class="share-btn share-x"  aria-label="Share on X" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-x-twitter"></i></a>
  <a id="shareLn" class="share-btn share-ln" aria-label="Share on LinkedIn" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-linkedin-in"></i></a>
  <a id="shareWa" class="share-btn share-wa" aria-label="Share on WhatsApp" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-whatsapp"></i></a>
  <a id="shareEm" class="share-btn share-em" aria-label="Share via Email" target="_blank" rel="noopener"><i class="fa-solid fa-envelope"></i></a>
  <button id="shareNative" class="share-btn share-x share-native" aria-label="Share"><i class="fa-solid fa-share-nodes"></i></button>
</div>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge" aria-hidden="true"><i class="fa-solid fa-brain"></i></div>
      <div class="brand-info">
        <div class="hero-heading" data-i="title">Semantic SEO Master Analyzer</div>
        <div class="hero-sub">Analyze URLs, get scores & suggestions</div>
      </div>
    </div>
    <div class="header-actions">
      <button class="btn btn-print" id="printTop"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
    </div>
  </header>

  <main class="analyzer" id="analyzer" role="main" aria-label="Semantic SEO Analyzer">
    <h2 class="section-title" data-i="analyze_title">Analyze a URL</h2>
    <p class="section-subtitle" data-i="legend_line">
      The wheel fills with your overall score. <span class="legend l-green">Green ≥ 80</span> <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Red &lt; 60</span>
    </p>

    <div class="score-area">
      <div class="score-container">
        <!-- Gauge -->
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
                <feGaussianBlur stdDeviation="2.4" result="b"/>
                <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
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
          <span class="chip" id="overallChip"><i class="fa-solid fa-gauge-high ico"></i> <span data-i="overall">Overall</span>: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="contentScoreChip"><i class="fa-solid fa-file-lines ico"></i> Content: <b id="contentScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge" title="AI/Human detection summary"><i class="fa-solid fa-user-check ico ico-green"></i> Writer: <b>—</b></span>
          <button id="viewHumanBtn" class="btn btn-ghost"><i class="fa-solid fa-user ico ico-green"></i> Human-like: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost"><i class="fa-solid fa-microchip ico ico-red"></i> AI-like: <b id="aiPct">—</b>%</button>
          <button id="copyQuick" class="btn btn-ghost"><i class="fa-regular fa-copy ico ico-cyan"></i> Copy report</button>
          <button id="viewAIText" class="btn btn-ghost"><i class="fa-solid fa-robot"></i> Evidence</button>
        </div>
      </div>
    </div>

    <div class="analyze-box" style="margin-top:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px">
      <form id="analyzeForm" class="analyze-form" onsubmit="return false;" aria-label="Analyze form">
        <label id="pageUrlLabel" for="analyzeUrl" class="url-label" data-i="page_url" style="display:inline-block;font-weight:900;margin-bottom:.35rem">Page URL</label>

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
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)">
              <span data-i="auto_check">Auto-apply checkmarks (≥ 80)</span>
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-analyze"><i class="fa-solid fa-magnifying-glass"></i> <span data-i="analyze">Analyze</span></button>
          <button class="btn btn-print" id="printChecklist"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
          <button class="btn btn-reset" id="resetChecklist"><i class="fa-solid fa-rotate"></i> <span data-i="reset">Reset</span></button>
          <button class="btn btn-export" id="exportChecklist" title="Export checklist JSON"><i class="fa-solid fa-file-export"></i> Export</button>
          <button class="btn btn-export" id="importChecklist" title="Import checklist JSON"><i class="fa-solid fa-file-import"></i> Import</button>
          <input type="file" id="importFile" accept="application/json" style="display:none">
        </div>

        <!-- Analyze water progress -->
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
            <canvas id="waterSmoke"></canvas>
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

    <!-- Completion -->
    <div class="progress-wrap">
      <div class="comp-water" id="compWater" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
        <svg class="comp-svg" viewBox="0 0 600 140" preserveAspectRatio="none">
          <defs>
            <clipPath id="compRound"><rect x="1" y="1" width="598" height="138" rx="14" ry="14"/></clipPath>
            <clipPath id="compFillClip"><rect id="compClipRect" x="0" y="0" width="0" height="140"/></clipPath>
            <linearGradient id="compGrad" x1="0" y1="0" x2="1" y2="1">
              <stop id="compStop1" offset="0%" stop-color="#3de2ff"/>
              <stop id="compStop2" offset="100%" stop-color="#9b5cff"/>
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
        <canvas id="compSmoke"></canvas>
        <div class="comp-overlay"></div>
        <div class="comp-pct"><span id="compPct">0%</span></div>
      </div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    <!-- Checklist -->
    <div class="analyzer-grid" id="checklistGrid">
      @php $labels = [
        1=>'Define search intent & primary topic',
        2=>'Map target & related keywords (synonyms/PAA)',
        3=>'H1 includes primary topic naturally',
        4=>'Integrate FAQs / questions with answers',
        5=>'Readable, NLP-friendly language',
        6=>'Title tag (≈50–60 chars) w/ primary keyword',
        7=>'Meta description (≈140–160 chars) + CTA',
        8=>'Canonical tag set correctly',
        9=>'Indexable & listed in XML sitemap',
        10=>'E-E-A-T signals (author, date, expertise)',
        11=>'Unique value vs. top competitors',
        12=>'Facts & citations up to date',
        13=>'Helpful media (images/video) w/ captions',
        14=>'Logical H2/H3 headings & topic clusters',
        15=>'Internal links to hub/related pages',
        16=>'Clean, descriptive URL slug',
        17=>'Breadcrumbs enabled (+ schema)',
        18=>'Mobile-friendly, responsive layout',
        19=>'Optimized speed (compression, lazy-load)',
        20=>'Core Web Vitals passing (LCP/INP/CLS)',
        21=>'Clear CTAs and next steps',
        22=>'Primary entity clearly defined',
        23=>'Related entities covered with context',
        24=>'Valid schema markup (Article/FAQ/Product)',
        25=>'sameAs/Organization details present'
      ]; @endphp

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
                        <!-- FIXED: catGrad reference -->
                        <use href="#catWave-{{ $loop->index }}" x="0" fill="url(#catGrad-{{ $loop->index }})"/>
                        <use href="#catWave-{{ $loop->index }}" x="640" fill="url(#catGrad-{{ $loop->index }})"/>
                      </g>
                      <g class="cat-wave2" opacity=".85">
                        <!-- FIXED: catGrad reference -->
                        <use href="#catWave-{{ $loop->index }}" x="0" y="3" fill="url(#catGrad-{{ $loop->index }})"/>
                        <use href="#catWave-{{ $loop->index }}" x="640" y="3" fill="url(#catGrad-{{ $loop->index }})"/>
                      </g>
                    </g>
                  </g>
                </svg>
                <canvas class="cat-smoke" id="catSmoke-{{ $loop->index }}"></canvas>
                <div class="cat-water-pct" id="catPct-{{ $loop->index }}">0/0 • 0%</div>
              </div>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">{{ $c[2]-$c[1]+1 }}</span></span>
          </header>
          <ul class="checklist">
            @for($i=$c[1];$i<=$c[2];$i++)
              <li class="checklist-item">
                <label>
                  <input type="checkbox" id="ck-{{ $i }}" aria-label="{{ $labels[$i] }}">
                  <span>{{ $labels[$i] }}</span>
                </label>
                <span class="score-badge" id="sc-{{ $i }}">—</span>
                <button class="improve-btn" data-id="ck-{{ $i }}" aria-haspopup="dialog">Improve</button>
              </li>
            @endfor
          </ul>
        </article>
      @endforeach
    </div>
  </main>
</div>

<footer class="site" role="contentinfo">
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

<button id="backTop" title="Back to top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

<!-- Simple modal shell -->
<div class="modal-backdrop" id="modalBackdrop" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:98"></div>
<div class="modal" id="tipModal" style="display:none;position:fixed;inset:0;z-index:99;align-items:center;justify-content:center">
  <div class="modal-card" style="width:min(860px,95vw);max-height:80vh;overflow:auto;background:#0f1022;border:1px solid rgba(255,255,255,.14);border-radius:16px;box-shadow:0 30px 80px rgba(0,0,0,.55)">
    <div class="modal-header" style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid rgba(255,255,255,.12)">
      <h3 class="modal-title" id="modalTitle" style="margin:0">Improve</h3>
      <button class="modal-close" id="modalClose" aria-label="Close dialog" style="border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);border-radius:10px;padding:.4rem .6rem;cursor:pointer;color:#fff"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="tabs" role="tablist" style="display:flex;gap:.4rem;padding:.55rem .6rem;border-bottom:1px dashed rgba(255,255,255,.12)">
      <button class="tab active" data-tab="tipsTab" role="tab" style="font-weight:900;border:1px solid rgba(255,255,255,.14);border-radius:10px;background:rgba(255,255,255,.06);padding:.4rem .6rem;color:#fff"><i class="fa-solid fa-lightbulb"></i> Tips</button>
      <button class="tab" data-tab="examplesTab" role="tab" style="font-weight:900;border:1px solid rgba(255,255,255,.14);border-radius:10px;background:rgba(255,255,255,.06);padding:.4rem .6rem;color:#fff"><i class="fa-brands fa-google"></i> Examples</button>
      <button class="tab" data-tab="humanTab" role="tab" style="font-weight:900;border:1px solid rgba(255,255,255,.14);border-radius:10px;background:rgba(255,255,255,.06);padding:.4rem .6rem;color:#fff"><i class="fa-solid fa-user"></i> Human-like</button>
      <button class="tab" data-tab="aiTab" role="tab" style="font-weight:900;border:1px solid rgba(255,255,255,.14);border-radius:10px;background:rgba(255,255,255,.06);padding:.4rem .6rem;color:#fff"><i class="fa-solid fa-microchip"></i> AI-like</button>
      <button class="tab" data-tab="fullTab" role="tab" style="font-weight:900;border:1px solid rgba(255,255,255,.14);border-radius:10px;background:rgba(255,255,255,.06);padding:.4rem .6rem;color:#fff"><i class="fa-solid fa-file-lines"></i> Full Text</button>
    </div>
    <div class="tabpanes" style="padding:12px">
      <div id="tipsTab" class="active"><ul id="modalList"></ul></div>
      <div id="examplesTab"><div class="pre" id="examplesPre">—</div></div>
      <div id="humanTab"><div class="pre" id="humanSnippetsPre">Run Analyze to view human-like snippets.</div></div>
      <div id="aiTab"><div class="pre" id="aiSnippetsPre">Run Analyze to view AI-like snippets.</div></div>
      <div id="fullTab"><div class="pre" id="fullTextPre">Run Analyze to load full text.</div></div>
    </div>
  </div>
</div>

<script>
/* -------- Global hue drift for multi-color water -------- */
(function(){ const root=document.documentElement; let start=performance.now(); function frame(now){ const angle=((now-start)/4)%360; root.style.setProperty('--hue', angle + 'deg'); requestAnimationFrame(frame);} requestAnimationFrame(frame); })();

/* ------------------------ Language (10) + auto-detect ------------------------ */
const I18N = {
  en:{title:"Semantic SEO Master Analyzer", analyze_title:"Analyze a URL", legend_line:"The wheel fills with your overall score. <span class='legend l-green'>Green ≥ 80</span> <span class='legend l-orange'>Orange 60–79</span> <span class='legend l-red'>Red &lt; 60</span>", overall:"Overall", page_url:"Page URL", analyze:"Analyze", print:"Print", reset:"Reset", auto_check:"Auto-apply checkmarks (≥ 80)"},
  ur:{title:"سیمینٹک SEO ماسٹر اینالائزر", analyze_title:"ایک URL کا تجزیہ کریں", legend_line:"پہیّہ آپ کے مجموعی اسکور سے بھرے گا۔ <span class='legend l-green'>سبز ≥ 80</span> <span class='legend l-orange'>نارنجی 60–79</span> <span class='legend l-red'>سرخ &lt; 60</span>", overall:"مجموعی", page_url:"صفحہ URL", analyze:"تجزیہ کریں", print:"پرنٹ", reset:"ری سیٹ", auto_check:"خودکار چیک (≥ 80)"},
  ar:{title:"مُحلّل السيو الدلالي", analyze_title:"حلّل عنوان URL", legend_line:"تمتلئ العجلة بالنتيجة الكلية. <span class='legend l-green'>أخضر ≥ 80</span> <span class='legend l-orange'>برتقالي 60–79</span> <span class='legend l-red'>أحمر &lt; 60</span>", overall:"الإجمالي", page_url:"رابط الصفحة", analyze:"تحليل", print:"طباعة", reset:"إعادة ضبط", auto_check:"اختيار تلقائي (≥ 80)"},
  hi:{title:"सिमैंटिक SEO मास्टर एनालाइज़र", analyze_title:"URL का विश्लेषण करें", legend_line:"पहिया आपके कुल स्कोर से भरता है। <span class='legend l-green'>हरा ≥ 80</span> <span class='legend l-orange'>नारंगी 60–79</span> <span class='legend l-red'>लाल &lt; 60</span>", overall:"कुल", page_url:"पेज URL", analyze:"विश्लेषण", print:"प्रिंट", reset:"रीसेट", auto_check:"ऑटो-टिक (≥ 80)"},
  fr:{title:"Analyseur SEO Sémantique", analyze_title:"Analyser une URL", legend_line:"La jauge se remplit selon votre score global. <span class='legend l-green'>Vert ≥ 80</span> <span class='legend l-orange'>Orange 60–79</span> <span class='legend l-red'>Rouge &lt; 60</span>", overall:"Global", page_url:"URL de la page", analyze:"Analyser", print:"Imprimer", reset:"Réinitialiser", auto_check:"Valider automatiquement (≥ 80)"},
  es:{title:"Analizador SEO Semántico", analyze_title:"Analizar una URL", legend_line:"La rueda se llena con tu puntaje global. <span class='legend l-green'>Verde ≥ 80</span> <span class='legend l-orange'>Naranja 60–79</span> <span class='legend l-red'>Rojo &lt; 60</span>", overall:"Global", page_url:"URL de la página", analyze:"Analizar", print:"Imprimir", reset:"Reiniciar", auto_check:"Marcar automáticamente (≥ 80)"},
  de:{title:"Semantischer SEO-Analyzer", analyze_title:"URL analysieren", legend_line:"Das Rad füllt sich mit Ihrem Gesamtscore. <span class='legend l-green'>Grün ≥ 80</span> <span class='legend l-orange'>Orange 60–79</span> <span class='legend l-red'>Rot &lt; 60</span>", overall:"Gesamt", page_url:"Seiten-URL", analyze:"Analysieren", print:"Drucken", reset:"Zurücksetzen", auto_check:"Automatisch abhaken (≥ 80)"},
  "zh-CN":{title:"语义SEO大师分析器", analyze_title:"分析 URL", legend_line:"仪表会按总体得分填充。<span class='legend l-green'>绿色 ≥ 80</span> <span class='legend l-orange'>橙色 60–79</span> <span class='legend l-red'>红色 &lt; 60</span>", overall:"总体", page_url:"页面 URL", analyze:"分析", print:"打印", reset:"重置", auto_check:"自动勾选 (≥ 80)"},
  ja:{title:"セマンティックSEOマスター解析", analyze_title:"URL を解析", legend_line:"ホイールは総合スコアで満たされます。<span class='legend l-green'>緑 ≥ 80</span> <span class='legend l-orange'>橙 60–79</span> <span class='legend l-red'>赤 &lt; 60</span>", overall:"総合", page_url:"ページURL", analyze:"解析", print:"印刷", reset:"リセット", auto_check:"自動チェック (≥ 80)"},
  ru:{title:"Семантический SEO-анализатор", analyze_title:"Анализировать URL", legend_line:"Шкала заполняется вашим общим баллом. <span class='legend l-green'>Зелёный ≥ 80</span> <span class='legend l-orange'>Оранжевый 60–79</span> <span class='legend l-red'>Красный &lt; 60</span>", overall:"Итог", page_url:"URL страницы", analyze:"Анализ", print:"Печать", reset:"Сброс", auto_check:"Отмечать автоматически (≥ 80)"}
};
const LANGS = [
  ["en","English","🇺🇸"],["ur","اردو","🇵🇰"],["ar","العربية","🇸🇦"],["hi","हिन्दी","🇮🇳"],
  ["fr","Français","🇫🇷"],["es","Español","🇪🇸"],["de","Deutsch","🇩🇪"],["zh-CN","简体中文","🇨🇳"],
  ["ja","日本語","🇯🇵"],["ru","Русский","🇷🇺"]
];
(function(){
  const dockBtn = document.getElementById('langOpen');
  const panel = document.getElementById('langPanel');
  const card = document.getElementById('langCard');

  function bestMatch(){
    const supported = new Set(LANGS.map(x=>x[0].toLowerCase()));
    const prefs = (navigator.languages && navigator.languages.length ? navigator.languages : [navigator.language || 'en']).map(x=>x.toLowerCase());
    for (let p of prefs){ if (supported.has(p)) return p; }
    for (let p of prefs){
      const base = p.split('-')[0];
      for (const c of supported){ if (c === base || c.startsWith(base)) return c; }
      if (base==='zh') return 'zh-CN';
    }
    return 'en';
  }
  function fillList(activeCode){
    card.innerHTML='';
    LANGS.forEach(([code,label,flag])=>{
      const div=document.createElement('div');
      div.className='lang-item' + (code===activeCode ? ' active' : '');
      div.dataset.code=code;
      div.innerHTML=`<span class="lang-flag">${flag||'🌐'}</span><strong>${label}</strong>`;
      card.appendChild(div);
    });
  }
  function apply(code){
    code = code || 'en';
    const d = I18N[code] || I18N.en;
    document.documentElement.setAttribute('lang', code);
    document.querySelector('[data-i="title"]').textContent=d.title;
    document.querySelector('[data-i="analyze_title"]').textContent=d.analyze_title;
    document.querySelector('[data-i="legend_line"]').innerHTML=d.legend_line;
    document.querySelectorAll('[data-i="overall"]').forEach(n=> n.textContent=d.overall);
    document.querySelector('[data-i="page_url"]').textContent=d.page_url;
    document.querySelectorAll('[data-i="analyze"]').forEach(n=> n.textContent=d.analyze);
    document.querySelectorAll('[data-i="print"]').forEach(n=> n.textContent=d.print);
    document.querySelectorAll('[data-i="reset"]').forEach(n=> n.textContent=d.reset);
    document.querySelectorAll('[data-i="auto_check"]').forEach(n=> n.textContent=d.auto_check);
    localStorage.setItem('lang', code);
    fillList(code);
  }
  dockBtn.addEventListener('click', ()=>{ panel.style.display = panel.style.display==='block' ? 'none' : 'block'; });
  document.addEventListener('click', (e)=>{ if (!panel.contains(e.target) && !dockBtn.contains(e.target)) panel.style.display='none'; });
  panel.addEventListener('click', (e)=>{ const it = e.target.closest('.lang-item'); if(!it) return; apply(it.dataset.code); panel.style.display='none'; });

  const initial = localStorage.getItem('lang') || bestMatch();
  fillList(initial); apply(initial);
})();

/* ------------------------ Helpers ------------------------ */
function normalizeUrl(u){ if(!u) return ''; u = u.trim(); if (!/^https?:\/\//i.test(u)) u = 'https://' + u.replace(/^\/+/, ''); try { new URL(u); } catch(e){} return u; }
function setText(id, val){ const el = document.getElementById(id); if (el) el.textContent = val; return el; }
function setChipTone(el, value){
  if (!el) return;
  el.classList.remove('chip-good','chip-mid','chip-bad');
  const ico = el.querySelector('i.ico'); if (ico) ico.classList.remove('ico-green','ico-orange','ico-red','ico-purple');
  const v = Number(value); if (Number.isNaN(v)) return;
  if (v >= 80){ el.classList.add('chip-good'); if (ico) ico.classList.add('ico-green'); }
  else if (v >= 60){ el.classList.add('chip-mid'); if (ico) ico.classList.add('ico-orange'); }
  else { el.classList.add('chip-bad'); if (ico) ico.classList.add('ico-red'); }
}

/* Gauge */
const GAUGE = { rect:null, stop1:null, stop2:null, r1:null, r2:null, arc:null, text:null, H:200, CIRC: 2*Math.PI*95 };
function setScoreWheel(value){
  if (!GAUGE.rect){
    GAUGE.rect  = document.getElementById('scoreClipRect');
    GAUGE.stop1 = document.getElementById('scoreStop1');
    GAUGE.stop2 = document.getElementById('scoreStop2');
    GAUGE.r1    = document.getElementById('ringStop1');
    GAUGE.r2    = document.getElementById('ringStop2');
    GAUGE.arc   = document.getElementById('ringArc');
    GAUGE.text  = document.getElementById('overallScore');
    if (GAUGE.arc){
      GAUGE.arc.style.strokeDasharray = GAUGE.CIRC.toFixed(2);
      GAUGE.arc.style.strokeDashoffset = GAUGE.CIRC.toFixed(2);
    }
  }
  const v = Math.max(0, Math.min(100, Number(value)||0));
  const y = GAUGE.H - (GAUGE.H * (v/100));
  GAUGE.rect && GAUGE.rect.setAttribute('y', String(y));
  GAUGE.text && (GAUGE.text.textContent = Math.round(v) + '%');

  let c1, c2;
  if (v >= 80){ c1='#22c55e'; c2='#16a34a'; }
  else if (v >= 60){ c1='#f59e0b'; c2='#fb923c'; }
  else { c1='#ef4444'; c2='#b91c1c'; }
  GAUGE.stop1 && GAUGE.stop1.setAttribute('stop-color', c1);
  GAUGE.stop2 && GAUGE.stop2.setAttribute('stop-color', c2);
  GAUGE.r1 && GAUGE.r1.setAttribute('stop-color', c1);
  GAUGE.r2 && GAUGE.r2.setAttribute('stop-color', c2);

  if (GAUGE.arc){
    const offset = GAUGE.CIRC * (1 - (v/100));
    GAUGE.arc.style.strokeDashoffset = offset.toFixed(2);
  }

  setText('overallScoreInline', Math.round(v));
  setChipTone(document.getElementById('overallChip'), v);
}

/* Smoky water overlays for category + completion */
const CatSmoke = (function(){
  const canvases = [];
  function attachAll(){
    document.querySelectorAll('.cat-smoke').forEach((cv, i)=>{
      const ctx = cv.getContext('2d');
      const o = { el: cv, ctx, parts: [], ratio: 0, dpr: Math.min(2, window.devicePixelRatio||1) };
      resizeOne(o); canvases[i] = o;
    });
  }
  function resizeOne(o){
    const w = o.el.clientWidth || 600, h = o.el.clientHeight || 26;
    o.el.width = Math.max(1, Math.floor(w * o.dpr));
    o.el.height = Math.max(1, Math.floor(h * o.dpr));
    o.ctx.setTransform(o.dpr,0,0,o.dpr,0,0);
  }
  function spawn(o, n=2){
    const W = o.el.clientWidth || 600; const H = o.el.clientHeight || 26;
    const span = Math.max(8, W * o.ratio); const baseY = H * 0.55;
    for(let i=0;i<n;i++){
      const x = Math.random() * span;
      o.parts.push({ x, y: baseY + (Math.random()*2 - 1), vx:(Math.random()-.5)*.30, vy: -(.25 + Math.random()*.45),
        life:1, decay: .03 + Math.random()*.05, r: 1.2 + Math.random()*2.6, hue: 180 + Math.random()*120 });
    }
  }
  function tick(){
    for(const o of canvases){
      if (!o) continue; const W = o.el.clientWidth || 600, H = o.el.clientHeight || 26;
      o.ctx.clearRect(0,0,W,H); o.ctx.globalCompositeOperation = 'lighter';
      const intensity = 40 * (0.25 + 0.75 * o.ratio); if (o.parts.length < intensity) spawn(o, 2);
      for(const p of o.parts){
        p.x += p.vx; p.y += p.vy; p.vy -= 0.0018; p.life -= p.decay; const a = Math.max(0, p.life);
        const g = o.ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r);
        g.addColorStop(0, `hsla(${p.hue}, 80%, 70%, ${0.28*a})`); g.addColorStop(1, `hsla(${(p.hue+60)%360}, 90%, 55%, 0)`);
        o.ctx.fillStyle = g; o.ctx.beginPath(); o.ctx.arc(p.x, p.y, p.r, 0, Math.PI*2); o.ctx.fill();
      }
      o.parts = o.parts.filter(p => p.life > 0 && p.y > -10);
    }
    requestAnimationFrame(tick);
  }
  window.addEventListener('resize', ()=> canvases.forEach(o=>o&&resizeOne(o)), {passive:true});
  attachAll(); requestAnimationFrame(tick);
  return { setPct(i, pct){ const o=canvases[i]; if(!o) return; o.ratio=Math.max(0,Math.min(1,(pct||0)/100)); } };
})();
const CompSmoke = (function(){
  const cv = document.getElementById('compSmoke'); const ctx = cv?.getContext('2d');
  if (!cv || !ctx) return { setPct(){} };
  let ratio=0, dpr=Math.min(2, window.devicePixelRatio||1), parts=[];
  function resize(){ const w=cv.clientWidth||600, h=cv.clientHeight||140; cv.width=Math.floor(w*dpr); cv.height=Math.floor(h*dpr); ctx.setTransform(dpr,0,0,dpr,0,0); }
  function spawn(n=4){
    const W=cv.clientWidth||600, H=cv.clientHeight||140, span = Math.max(12, W*ratio), baseY=H*0.55;
    for(let i=0;i<n;i++){
      const x = Math.random()*span;
      parts.push({x,y:baseY+(Math.random()*2-1),vx:(Math.random()-.5)*.35,vy:-(.25+Math.random()*.45),
        life:1,decay:.02+Math.random()*.04,r:1.6+Math.random()*3.2,hue:160+Math.random()*140});
    }
  }
  function loop(){
    const W=cv.clientWidth||600, H=cv.clientHeight||140;
    ctx.clearRect(0,0,W,H); ctx.globalCompositeOperation='lighter';
    if (parts.length < 200*ratio) spawn(6);
    for(const p of parts){
      p.x+=p.vx; p.y+=p.vy; p.vy-=0.002; p.life-=p.decay; const a=Math.max(0,p.life);
      const g=ctx.createRadialGradient(p.x,p.y,0,p.x,p.y,p.r);
      g.addColorStop(0,`hsla(${p.hue},80%,70%,${0.3*a})`); g.addColorStop(1,`hsla(${(p.hue+60)%360},90%,55%,0)`);
      ctx.fillStyle=g; ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2); ctx.fill();
    }
    parts = parts.filter(p=>p.life>0 && p.y>-20);
    requestAnimationFrame(loop);
  }
  window.addEventListener('resize', resize, {passive:true}); resize(); requestAnimationFrame(loop);
  return { setPct(p){ ratio=Math.max(0,Math.min(1,p/100)); } };
})();

/* Water progress controller (+ smoke) */
const WaterSmoke = (function(){
  const canvas = document.getElementById('waterSmoke');
  const ctx = canvas ? canvas.getContext('2d') : null;
  let running = false, particles = [], frameId = 0;
  let levelRatioTop = 1;
  function cssW(){ return canvas?.clientWidth || canvas?.parentElement?.clientWidth || 0; }
  function cssH(){ return canvas?.clientHeight || canvas?.parentElement?.clientHeight || 0; }
  function resize(){ if(!canvas || !ctx) return; const dpr = Math.min(2, window.devicePixelRatio||1); const w = cssW(), h = cssH(); canvas.width = Math.max(1, Math.floor(w * dpr)); canvas.height = Math.max(1, Math.floor(h * dpr)); ctx.setTransform(dpr,0,0,dpr,0,0); }
  function spawn(n=6){
    if(!canvas) return; const y = cssH() * levelRatioTop;
    for(let i=0;i<n;i++){ const x = Math.random()*cssW(); const jitter=(Math.random()*4-2); const speed = 0.4 + Math.random()*0.8;
      particles.push({ x, y:y+jitter, vx:(Math.random()-.5)*.35, vy:-speed, life:1, decay:0.008+Math.random()*0.02, r:2+Math.random()*6, hue:(180+Math.random()*120) });
    }
  }
  function tick(){
    if(!running || !ctx || !canvas){ cancelAnimationFrame(frameId); return; }
    frameId = requestAnimationFrame(tick);
    ctx.clearRect(0,0,cssW(),cssH()); ctx.globalCompositeOperation = 'lighter';
    if (particles.length < 180) spawn(6);
    for (const p of particles){
      p.x += p.vx; p.y += p.vy; p.vy -= 0.005; p.life -= p.decay;
      const a = Math.max(0, p.life);
      const g = ctx.createRadialGradient(p.x, p.y, 0, p.x, p.y, p.r);
      g.addColorStop(0, `hsla(${p.hue}, 80%, 70%, ${0.35*a})`);
      g.addColorStop(1, `hsla(${(p.hue+60)%360}, 90%, 55%, ${0.05*a})`);
      ctx.fillStyle = g; ctx.beginPath(); ctx.arc(p.x, p.y, p.r, 0, Math.PI*2); ctx.fill();
    }
    particles = particles.filter(p=> p.life>0 && p.y > -20);
  }
  function start(){ if(!canvas||!ctx) return; running=true; resize(); cancelAnimationFrame(frameId); frameId = requestAnimationFrame(tick); }
  function stop(){ running=false; cancelAnimationFrame(frameId); particles.length = 0; if(ctx) ctx.clearRect(0,0,cssW(),cssH()); }
  function setLevel(topRatio){ levelRatioTop = Math.max(0, Math.min(1, topRatio)); }
  window.addEventListener('resize', ()=> running && resize(), {passive:true});
  return { start, stop, setLevel };
})();
const Water = (function(){
  const wrap = document.getElementById('waterWrap');
  const bar  = document.getElementById('waterBar');
  const rect = document.getElementById('waterClipRect');
  const pct  = document.getElementById('waterPct');
  const label= document.getElementById('pageUrlLabel');
  let prog = 0, intv = null;
  const H = 200;
  function show(){ wrap.style.display='block'; }
  function hide(){ wrap.style.display='none'; }
  function set(v){
    prog = Math.max(0, Math.min(100, v));
    const y = H - (H * (prog/100));
    rect.setAttribute('y', String(y));
    bar.setAttribute('aria-valuenow', Math.round(prog));
    pct.textContent = Math.round(prog) + '%';
    if (WaterSmoke && typeof WaterSmoke.setLevel === 'function') WaterSmoke.setLevel(y / H);
  }
  function start(){
    show(); set(0);
    label.classList.add('animating');
    if (WaterSmoke && WaterSmoke.start) WaterSmoke.start();
    clearInterval(intv);
    intv = setInterval(()=>{ if (prog < 90) set(prog + 1.2); }, 50);
  }
  function finish(){
    clearInterval(intv);
    const step = ()=>{ if (prog >= 100){
        setTimeout(()=>{ label.classList.remove('animating'); }, 300);
        setTimeout(()=>{ if (WaterSmoke && WaterSmoke.stop) WaterSmoke.stop(); }, 800);
        return;
      }
      set(prog + Math.max(1.5, (100-prog)*0.12));
      requestAnimationFrame(step);
    };
    requestAnimationFrame(step);
  }
  function reset(){
    clearInterval(intv); set(0); hide(); label.classList.remove('animating');
    if (WaterSmoke && WaterSmoke.stop) WaterSmoke.stop();
  }
  return { start, finish, reset, set, show, hide };
})();

/* ------------------------ Share links + back to top ------------------------ */
(function(){
  const url = encodeURIComponent(location.href);
  const title = encodeURIComponent(document.title);
  const s = id => document.getElementById(id);
  s('shareFb').href = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
  s('shareX').href  = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
  s('shareLn').href = `https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}`;
  s('shareWa').href = `https://api.whatsapp.com/send?text=${title}%20${url}`;
  s('shareEm').href = `mailto:?subject=${title}&body=${url}`;
  const nat = s('shareNative');
  if(nat){ nat.addEventListener('click', async ()=>{ try{ if(navigator.share){ await navigator.share({ title: document.title, url: location.href }); } else { window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`,'_blank'); } }catch(e){} }); }
})();
(function(){
  const btn = document.getElementById('backTop'); const link = document.getElementById('toTopLink');
  function onScroll(){ btn.style.display = window.scrollY>300 ? 'grid' : 'none'; }
  addEventListener('scroll', onScroll, {passive:true}); onScroll();
  const goTop = e => { e && e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); };
  btn.addEventListener('click', goTop); link.addEventListener('click', goTop);
})();

/* ------------------------ Analyze + Checklist glue ------------------------ */
const ANALYZE_URLS = ["{{ url('/analyze-json') }}", "{{ url('/analyze') }}"]; // try alias (GET/ANY) first
const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

const CAT_RANGES = [
  [1,5], [6,9], [10,13], [14,17], [18,21], [22,25]
];

function badgeTone(el, score){
  el.classList.remove('score-good','score-mid','score-bad');
  if (score>=80) el.classList.add('score-good');
  else if (score>=60) el.classList.add('score-mid');
  else el.classList.add('score-bad');
}
function updateCategoryBars(){
  document.querySelectorAll('.category-card').forEach((card, idx)=>{
    const total = parseInt(card.querySelector('.total-count').textContent,10)||0;
    const checked = card.querySelectorAll('input[type="checkbox"]:checked').length;
    const pct = total ? Math.round(checked/total*100) : 0;
    card.querySelector('.checked-count').textContent = checked;
    const fillRect = document.getElementById(`catFillRect-${idx}`); if (fillRect) fillRect.setAttribute('width', String(6*pct));
    const pctEl = document.getElementById(`catPct-${idx}`); if (pctEl) pctEl.textContent = `${checked}/${total} • ${pct}%`;
    if (typeof CatSmoke?.setPct === 'function') CatSmoke.setPct(idx, pct);
  });

  // Completion bar
  const totalAll = 25;
  const checkedAll = document.querySelectorAll('.checklist input[type="checkbox"]:checked').length;
  const pctAll = Math.round(checkedAll/totalAll*100);
  const compRect = document.getElementById('compClipRect'); if (compRect) compRect.setAttribute('width', String(6*pctAll));
  document.getElementById('compPct').textContent = pctAll + '%';
  document.getElementById('progressCaption').textContent = `${checkedAll} of ${totalAll} items completed`;
  if (typeof CompSmoke?.setPct === 'function') CompSmoke.setPct(pctAll);
}

function autoTickByScores(scoresMap){
  let autoCount = 0;
  for (let i=1;i<=25;i++){
    const score = Number(scoresMap?.[i] ?? scoresMap?.[String(i)] ?? scoresMap?.[`item${i}`] ?? -1);
    const cb = document.getElementById(`ck-${i}`);
    const badge = document.getElementById(`sc-${i}`);
    if (badge && !Number.isNaN(score) && score>=0){
      badge.textContent = Math.round(score);
      badgeTone(badge, score);
      if (score >= 80) cb?.closest('.checklist-item')?.classList.add('sev-good');
      else if (score >= 60) cb?.closest('.checklist-item')?.classList.add('sev-mid');
      else cb?.closest('.checklist-item')?.classList.add('sev-bad');
    }
    if (!cb) continue;
    const should = score>=80 && document.getElementById('autoApply').checked;
    const prev = cb.checked;
    if (should && !prev){
      cb.checked = true;
      cb.closest('.checklist-item')?.classList.add('flash-row');
      setTimeout(()=> cb.closest('.checklist-item')?.classList.remove('flash-row'), 700);
      autoCount++;
    }
  }
  setText('rAutoCount', autoCount);
  updateCategoryBars();
}

/* Hardened analyzer: try GET/ANY alias, then POST/GET main */
async function analyze(){
  const input = document.getElementById('analyzeUrl');
  let url = normalizeUrl(input.value);
  if (!url) { input.focus(); return; }

  Water.start();
  document.getElementById('analyzeStatus').textContent = 'Fetching & analyzing…';
  document.getElementById('analyzeReport').style.display = 'none';

  let data = null, ok = false, status = 0, text = '', lastErr = '';

  // 1) GET /analyze-json
  try{
    const qs = new URLSearchParams({ url }).toString();
    const res = await fetch(`{{ url('/analyze-json') }}?${qs}`, { method:'GET', headers:{ 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }});
    status = res.status; text = await res.text();
    try{ data = JSON.parse(text); }catch(e){ data = null; }
    if (res.ok && data){ ok = true; }
  }catch(e){ lastErr = 'GET /analyze-json failed'; }

  // 2) POST /analyze (CSRF)
  if (!ok){
    try{
      const res = await fetch(`{{ url('/analyze') }}`, {
        method:'POST',
        headers:{
          'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ url, _token: CSRF })
      });
      status = res.status; text = await res.text();
      try{ data = JSON.parse(text); }catch(e){ data = null; }
      if (res.ok && data){ ok = true; }
    }catch(e){ lastErr = 'POST /analyze failed'; }
  }

  // 3) GET /analyze (fallback)
  if (!ok){
    try{
      const qs = new URLSearchParams({ url }).toString();
      const res = await fetch(`{{ url('/analyze') }}?${qs}`, { method:'GET', headers:{ 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }});
      status = res.status; text = await res.text();
      try{ data = JSON.parse(text); }catch(e){ data = null; }
      if (res.ok && data){ ok = true; }
    }catch(e){ lastErr = 'GET /analyze failed'; }
  }

  if (!ok || !data){
    console.error('Analyze failed', status, lastErr, text?.slice(0,300));
    Water.finish();
    document.getElementById('analyzeStatus').textContent = 'Could not analyze this URL. Check routes and try again.';
    return;
  }

  const overall = Number(data.overall ?? 0);
  const contentScore = Number(data.contentScore ?? 0);
  const humanPct = Number(data.humanPct ?? 0);
  const aiPct    = Number(data.aiPct ?? 0);
  const writer = humanPct>=aiPct ? 'Likely Human' : 'AI-like';

  setScoreWheel(overall||0);
  setText('contentScoreInline', Math.round(contentScore||0));
  setChipTone(document.getElementById('contentScoreChip'), contentScore||0);
  const badge = document.getElementById('aiBadge'); if (badge){ badge.querySelector('b').textContent = writer; }
  setText('humanPct', Math.round(humanPct||0));
  setText('aiPct', Math.round(aiPct||0));

  setText('rStatus', data.httpStatus ?? '—');
  setText('rTitleLen', data.titleLen ?? '—');
  setText('rMetaLen', data.metaLen ?? '—');
  setText('rCanonical', data.canonical ?? '—');
  setText('rRobots', data.robots ?? '—');
  setText('rViewport', data.viewport ?? '—');
  setText('rHeadings', data.headings ?? '—');
  setText('rInternal', data.internalLinks ?? '—');
  setText('rSchema', data.schema ?? '—');

  autoTickByScores(data.itemScores || {});
  Water.finish();
  document.getElementById('analyzeStatus').textContent = 'Analysis complete';
  document.getElementById('analyzeReport').style.display = 'block';
}

/* Wire controls */
(function(){
  const input = document.getElementById('analyzeUrl');
  const pasteBtn = document.getElementById('pasteUrl');
  const clearBtn = document.getElementById('clearUrl');
  pasteBtn.addEventListener('click', async ()=>{ try{ const txt = await navigator.clipboard.readText(); if (txt){ input.value = txt.trim(); input.dispatchEvent(new Event('input')); } }catch(e){} });
  clearBtn.addEventListener('click', ()=>{ input.value=''; input.focus(); input.dispatchEvent(new Event('input')); });
  input.addEventListener('keydown', (e)=>{ if(e.key==='Enter'){ e.preventDefault(); analyze(); }});
  document.getElementById('analyzeBtn').addEventListener('click', analyze);

  document.getElementById('resetChecklist').addEventListener('click', ()=>{
    document.querySelectorAll('.checklist input[type="checkbox"]').forEach(cb=> cb.checked=false);
    document.querySelectorAll('.score-badge').forEach(b=>{ b.textContent='—'; b.classList.remove('score-good','score-mid','score-bad'); });
    updateCategoryBars();
    setScoreWheel(0);
    setText('contentScoreInline', 0); setChipTone(document.getElementById('contentScoreChip'), 0);
    setText('humanPct','—'); setText('aiPct','—');
    document.getElementById('aiBadge')?.querySelector('b')?.textContent = '—';
    Water.reset();
  });
  document.getElementById('printTop').addEventListener('click', ()=> window.print());
  document.getElementById('printChecklist').addEventListener('click', ()=> window.print());

  // Export / Import
  const exportBtn = document.getElementById('exportChecklist');
  const importBtn = document.getElementById('importChecklist');
  const importFile= document.getElementById('importFile');
  exportBtn.addEventListener('click', ()=>{
    const payload = { checked:[], scores:{} };
    for(let i=1;i<=25;i++){
      const cb = document.getElementById(`ck-${i}`);
      const sc = document.getElementById(`sc-${i}`);
      if (cb?.checked) payload.checked.push(i);
      const s = parseInt(sc?.textContent||'NaN',10);
      if (!Number.isNaN(s)) payload.scores[i]=s;
    }
    const blob = new Blob([JSON.stringify(payload,null,2)],{type:'application/json'});
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob); a.download = 'checklist.json'; a.click();
    URL.revokeObjectURL(a.href);
  });
  importBtn.addEventListener('click', ()=> importFile.click());
  importFile.addEventListener('change', async ()=>{
    const file = importFile.files[0]; if (!file) return;
    try{
      const txt = await file.text();
      const data = JSON.parse(txt);
      for(let i=1;i<=25;i++){
        const cb = document.getElementById(`ck-${i}`); if (cb) cb.checked = (data.checked||[]).includes(i);
        const sc = document.getElementById(`sc-${i}`); const val = data.scores?.[i];
        if (sc && typeof val==='number'){ sc.textContent = val; badgeTone(sc, val); }
      }
      updateCategoryBars();
    }catch(e){ alert('Invalid JSON'); }
  });

  // Improve modal
  const modal = document.getElementById('tipModal');
  const backdrop = document.getElementById('modalBackdrop');
  function openModal(){ backdrop.style.display='block'; modal.style.display='flex'; }
  function closeModal(){ backdrop.style.display='none'; modal.style.display='none'; }
  document.getElementById('modalClose').addEventListener('click', closeModal);
  backdrop.addEventListener('click', closeModal);
  document.querySelectorAll('.improve-btn').forEach(btn=>{
    btn.addEventListener('click', (e)=>{ e.preventDefault(); openModal(); });
  });

  // Initial
  updateCategoryBars();
})();

/* --------- Cloudy background rendering --------- */
(function(){
  // Lines layer
  const c = document.getElementById('linesCanvas');
  if (!c) return;
  const ctx = c.getContext('2d');
  let dpr = Math.min(2, window.devicePixelRatio||1);
  function resize(){
    c.width = Math.floor(innerWidth * dpr);
    c.height= Math.floor(innerHeight * dpr);
    ctx.setTransform(dpr,0,0,dpr,0,0);
  }
  function draw(t){
    ctx.clearRect(0,0,innerWidth,innerHeight);
    const w = innerWidth, h = innerHeight;
    const rows = 12, spacing = Math.max(60, h/rows);
    for(let i=-2;i<rows+2;i++){
      const y = i*spacing + (t*0.02 % spacing);
      const g = ctx.createLinearGradient(0,y,w,y+80);
      g.addColorStop(0, 'rgba(61,226,255,0.03)');
      g.addColorStop(0.5, 'rgba(155,92,255,0.05)');
      g.addColorStop(1, 'rgba(255,32,69,0.03)');
      ctx.strokeStyle = g;
      ctx.lineWidth = 1.2;
      ctx.beginPath();
      ctx.moveTo(-100, y);
      ctx.lineTo(w+100, y+80);
      ctx.stroke();
    }
    requestAnimationFrame(draw);
  }
  addEventListener('resize', resize, {passive:true});
  resize(); requestAnimationFrame(draw);
})();
(function(){
  // Fog/brain layer
  const c = document.getElementById('brainCanvas');
  if (!c) return;
  const ctx = c.getContext('2d');
  let dpr = Math.min(2, window.devicePixelRatio||1);
  let blobs = [];
  function resize(){
    c.width = Math.floor(innerWidth * dpr);
    c.height= Math.floor(innerHeight * dpr);
    ctx.setTransform(dpr,0,0,dpr,0,0);
    blobs = Array.from({length: 36}).map(()=> ({
      x: Math.random()*innerWidth,
      y: Math.random()*innerHeight,
      r: 40 + Math.random()*120,
      vx:(Math.random()-.5)*.2,
      vy:(Math.random()-.5)*.2,
      hue: 180+Math.random()*140,
      a: .08+.08*Math.random()
    }));
  }
  function draw(){
    ctx.clearRect(0,0,innerWidth,innerHeight);
    ctx.globalCompositeOperation = 'lighter';
    for(const b of blobs){
      b.x += b.vx; b.y += b.vy;
      if (b.x < -200) b.x = innerWidth+200; if (b.x > innerWidth+200) b.x = -200;
      if (b.y < -200) b.y = innerHeight+200; if (b.y > innerHeight+200) b.y = -200;
      const g = ctx.createRadialGradient(b.x,b.y,0,b.x,b.y,b.r);
      g.addColorStop(0, `hsla(${b.hue}, 85%, 65%, ${b.a})`);
      g.addColorStop(1, `hsla(${(b.hue+60)%360}, 85%, 55%, 0)`);
      ctx.fillStyle = g;
      ctx.beginPath(); ctx.arc(b.x,b.y,b.r,0,Math.PI*2); ctx.fill();
    }
    requestAnimationFrame(draw);
  }
  addEventListener('resize', resize, {passive:true});
  resize(); requestAnimationFrame(draw);
})();
</script>
</body>
</html>
