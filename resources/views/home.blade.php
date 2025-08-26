{{-- resources/views/home.blade.php — v2025-08-25 (Human-vs-AI first; upgraded Readability; Entities & Topics; PSI auto-start; colorful, responsive) --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

<style>
/* === Upgrade: Colorful Headings (non-invasive) === */
.hvai .hvai-head h4,
.readability .read-head h4,
.entities .entities-head h4,
.psi .psi-head h4{
  background: linear-gradient(92deg,#60a5fa,#a78bfa,#34d399,#f59e0b,#f43f5e,#60a5fa);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  font-weight: 900;
  letter-spacing: .2px;
  filter: drop-shadow(0 2px 8px rgba(0,0,0,.3));
}

/* === Upgrade: Pinterest-style neon aura for gauge === */
.neon-gauge::before{
  content: "";
  position: absolute;
  inset: 6px;
  border-radius: 999px;
  background: conic-gradient(from 0deg, #60a5fa, #a78bfa, #34d399, #f59e0b, #f43f5e, #60a5fa);
  -webkit-mask: radial-gradient(transparent 64px, #000 65px);
          mask: radial-gradient(transparent 64px, #000 65px);
  filter: blur(6px) saturate(1.2);
  opacity: .45;
  animation: spinAura 12s linear infinite;
  pointer-events: none;
}
@keyframes spinAura{ to{ transform: rotate(360deg); } }

/* === Upgrade: Language pill (uses existing HVAI i18n) === */
.hvai-v2-head{
  position: relative;
}
#hvaiLang{
  appearance: none;
  -webkit-appearance: none;
  font: 700 12px/1 Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial;
  padding: 6px 28px 6px 10px;
  border-radius: 999px;
  border: 1px solid rgba(255,255,255,.18);
  background:
    linear-gradient(135deg,rgba(96,165,250,.18),rgba(167,139,250,.12)) padding-box,
    linear-gradient(135deg,rgba(255,255,255,.28),rgba(255,255,255,.08)) border-box;
  color: #eaf7ff;
  position: absolute;
  right: 0;
  top: -4px;
  box-shadow: 0 4px 14px rgba(0,0,0,.25), 0 0 10px rgba(96,165,250,.15) inset;
  cursor: pointer;
}
#hvaiLang:after{ content:""; }
#hvaiLang option{ color:#0b0d21; }
#hvaiLang:focus{ outline: none; box-shadow: 0 0 0 6px rgba(96,165,250,.15); }

.neon-gauge .prog{ stroke: url(#gradRainbow) !important; }

.mini-human .prog{ stroke: url(#miniHumanGrad) !important; }
.mini-ai .prog{ stroke: url(#miniAIGrad) !important; }
.mini-gauge .label b{ font-size:1rem; }

.hvai-scorebar{ position:relative; }
.hvai-scorebar > span.human{ background: linear-gradient(90deg,#22c55e,#a7f3d0); height:100%; display:block; }
.hvai-scorebar > span.ai{ position:absolute; right:0; top:0; background: linear-gradient(90deg,#fca5a5,#ef4444); height:100%; display:block; }

/* Ensemble v3 layout fixes */
.hvai .hvai-v2{ position:relative; }
.hvai .hvai-tabpanes{ margin-top:8px; padding:10px; border-radius:14px; background:rgba(11,13,33,.35); }
.hvai .hvai-pane .hvai-scorebar{ margin:6px 0 2px; }
.hvai .hvai-pane .hvai-line{ font-size:.9rem; color:var(--text); opacity:.95; }
.hvai .neon-gauge{ position:relative; width:240px; height:240px; margin: 8px auto 0; }
.hvai .mini-gauge{ position:absolute; top:50%; transform:translateY(-50%); width:72px; height:72px; }
.hvai .mini-human{ left:-86px; } .hvai .mini-ai{ right:-86px; }

/* === Ensemble v3 • layout fix 2 === */
.hvai{ position:relative; overflow:hidden; } /* prevent horizontal scroll if minis overflow */
.hvai .hvai-v2{ position:relative; padding:16px 14px; margin:10px 0 6px; }
.hvai .hvai-head{ margin-bottom:6px; }
.hvai .neon-gauge{ position:relative; width:240px; height:240px; margin: 6px auto 8px; }
.hvai .mini-gauge{ position:absolute; top:50%; transform:translateY(-50%); width:72px; height:72px; }
.hvai .mini-human{ left:-84px; } .hvai .mini-ai{ right:-84px; }

.hvai .hvai-tabs{ display:flex; margin:8px auto 6px; gap:8px; flex-wrap:wrap; justify-content:center; }
.hvai .hvai-tab{ padding:6px 12px; border-radius:999px; font-weight:800; font-size:.85rem; }
.hvai .hvai-tabpanes{ margin:6px auto 0; max-width:760px; width:100%; padding:10px; border-radius:14px; background:rgba(11,13,33,.28); border:1px solid rgba(255,255,255,.08); }
.hvai .hvai-pane .hvai-line{ display:flex; align-items:center; justify-content:space-between; gap:10px; font-size:.92rem; }
.hvai .hvai-pane .hvai-scorebar{ height:8px; margin:6px 0 2px; border-radius:999px; background:rgba(255,255,255,.06); }
.hvai .hvai-scorebar>span{ height:100%; display:block; }

/* prevent unusual large margins around the whole HVAI card */
.hvai .hvai-v2-meta{ margin:8px 0 0; }

/* Responsive: keep minis from pushing the card edges on small screens */
@media (max-width: 860px){
  .hvai .mini-human{ left:-64px; } .hvai .mini-ai{ right:-64px; }
}
@media (max-width: 680px){
  .hvai .neon-gauge{ width:200px; height:200px; }
  .hvai .mini-gauge{ width:60px; height:60px; }
  .hvai .mini-human{ left:-52px; } .hvai .mini-ai{ right:-52px; }
}
@media (max-width: 520px){
  .hvai .neon-gauge{ width:180px; height:180px; }
  .hvai .mini-gauge{ width:52px; height:52px; }
  .hvai .mini-human{ left:-44px; } .hvai .mini-ai{ right:-44px; }
}

.hvai .hvai-pane.active{ display:block; min-height:34px; }

/* hvai overflow guard */ .hvai *{ max-width:100%; box-sizing:border-box; }

/* v3 layoutfix2 panes */
.hvai .hvai-tabpanes{ margin:8px auto; max-width:780px; width:100%; }
.hvai .hvai-line>div:first-child{ flex:1 1 auto; }

/* tabs flex override */
.hvai .hvai-tabs{ display:flex; }

/* v3 colorful bars + clickable flags */
.hvai .hvai-line .label-badges{ display:flex; align-items:center; gap:12px; font-weight:800; }
.hvai .hvai-line .label-badges .hlabel i,
.hvai .hvai-line .label-badges .alabel i{
  background: linear-gradient(135deg,#60a5fa,#a78bfa,#34d399,#f59e0b,#f43f5e);
  -webkit-background-clip:text; background-clip:text; color: transparent;
}
.hvai .hvai-scorebar{ position:relative; height:12px; border-radius:999px; background:rgba(255,255,255,.08); overflow:hidden; }
.hvai .hvai-scorebar>span.human{
  background: linear-gradient(90deg,#22c55e,#10b981,#06b6d4,#60a5fa);
}
.hvai .hvai-scorebar>span.ai{
  position:absolute; right:0; top:0;
  background: linear-gradient(90deg,#f97316,#ef4444,#ec4899,#a855f7);
}
.hvai .hvai-flag{ cursor:pointer; }

/* v3 checklist affordance */
.improve, [data-improve="true"], .checklist-improve{ cursor:pointer; }

/* hide leftover suggestions */
#hvaiModal, #pane-suggestions, #aiSuggestBox { display:none !important; visibility:hidden !important; }
</style>



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

</style>

<style>
/* Fallback colorful panel */
.hvai-v2.card.glassy{background: radial-gradient(1200px 500px at -10% -20%, rgba(96,165,250,.08), transparent 55%),
radial-gradient(950px 480px at 110% -10%, rgba(167,139,250,.08), transparent 60%),
radial-gradient(700px 520px at 30% 120%, rgba(52,211,153,.06), transparent 60%),
rgba(255,255,255,.035);border:1px solid rgba(166,247,255,.10)}
</style>


<style>
.hvai{ position:relative; }
#hvaiTechBg{ position:absolute; inset:0; width:100%; height:100%; z-index:0; pointer-events:none; opacity:.9; filter:saturate(1.2); }
.hvai > *:not(canvas){ position:relative; z-index:1; }
.hvai .ico, .hvai i[class*="fa-"]{ background: linear-gradient(135deg,#60a5fa,#a78bfa,#34d399,#f59e0b,#f43f5e); -webkit-background-clip:text; background-clip:text; color: transparent; }
.neon-gauge{ position:relative; width:240px; height:240px; margin: 8px auto 0; }
.mini-gauge{ position:absolute; top:50%; transform:translateY(-50%); width:72px; height:72px; filter: drop-shadow(0 2px 8px rgba(0,0,0,.35)); }
.mini-gauge svg{ transform: rotate(-90deg); } .mini-gauge .track{ stroke: rgba(255,255,255,.15); } .mini-gauge .prog{ transition: stroke-dashoffset .8s cubic-bezier(.22,.9,.26,1); }
.mini-gauge .label{ position:absolute; inset:0; display:grid; place-items:center; font-weight:900; font-size:.85rem; color:#eaf7ff; text-shadow: 0 2px 8px rgba(0,0,0,.5); }
.mini-human{ left:-86px; } .mini-ai{ right:-86px; }
.hvai-tabs{ margin:10px 0 6px; display:flex; flex-wrap:wrap; gap:6px; }
.hvai-tab{ border:1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.04); padding:6px 10px; border-radius:999px; font-weight:800; font-size:.85rem; cursor:pointer; user-select:none; letter-spacing:.2px; transition: transform .12s ease, background .2s; }
.hvai-tab:hover{ transform: translateY(-1px); }
.hvai-tab.active{ background: linear-gradient(135deg,rgba(96,165,250,.22),rgba(167,139,250,.18)); box-shadow: 0 2px 10px rgba(0,0,0,.25), inset 0 0 10px rgba(96,165,250,.12); }
.hvai-tab i{ margin-right:6px; }
.hvai-tabpanes{ border:1px solid rgba(255,255,255,.12); border-radius:14px; padding:10px; background:rgba(11,13,33,.35); }
.hvai-pane{ display:none; } .hvai-pane.active{ display:block; }
.hvai-scorebar{ height:10px; border-radius:999px; background:rgba(255,255,255,.08); overflow:hidden; margin-top:6px; } .hvai-scorebar > span{ display:block; height:100%; width:0%; transition:width .6s ease; background: linear-gradient(90deg,#dc2626,#f59e0b,#22c55e); }
.hvai-badge{ display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; font-weight:900; letter-spacing:.2px; margin-top:8px; }
.hvai-badge.good{ background: rgba(34,197,94,.16); border:1px solid rgba(34,197,94,.45); color:#bbf7d0; }
.hvai-badge.mid{ background: rgba(245,158,11,.16); border:1px solid rgba(245,158,11,.45); color:#fde68a; }
.hvai-badge.low{ background: rgba(239,68,68,.16); border:1px solid rgba(239,68,68,.5); color:#fecaca; }
</style>

<style>
/* v3 modal overlay */

#hvaiModal .box{
  width:min(880px,92vw); max-height:86vh; overflow:auto;
  background: linear-gradient(180deg,rgba(14,17,40,.96),rgba(10,12,32,.96));
  border:1px solid rgba(255,255,255,.1); border-radius:16px; padding:16px;
  box-shadow: 0 8px 30px rgba(0,0,0,.45);
}
#hvaiModal .close{ position:sticky; top:0; float:right; cursor:pointer; opacity:.8; }
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

function setModelHA(id, human, ai){
  var h = Math.max(0, Math.min(100, Math.round(human||0)));
  var a = Math.max(0, Math.min(100, Math.round(ai||0)));
  var hb = document.getElementById('bar-'+id+'-human');
  var ab = document.getElementById('bar-'+id+'-ai');
  var ht = document.getElementById('score-'+id+'-human');
  var at = document.getElementById('score-'+id+'-ai');
  if(hb){ hb.style.width = h+'%'; }
  if(ab){ ab.style.width = a+'%'; }
  if(ht){ ht.textContent = h+'%'; }
  if(at){ at.textContent = a+'%'; }
}

/* open modal on flag click */
document.addEventListener('click', function(e){
  var card = e.target.closest('.hvai-flag');
  if(!card || e.target.closest('button.hvai-suggest-btn')) return;
  var box = document.getElementById('aiFlags');
  if(!box) return;
  var cards = Array.from(box.querySelectorAll('.hvai-flag'));
  var idx = cards.indexOf(card);
  if(idx>=0){
    var clicker = card.querySelector('button.hvai-suggest-btn');
    if(clicker) clicker.click();
  }
});

/* v3 fallback mapper */
function __hvai_v3_fill_models_from_last(){
  var res = window.__lastDet || {};
  var human = Math.round(res.humanPct||0);
  var ai = Math.max(0, 100-human);
  ['overall','zerogpt','openai','gptzero','copyleaks','writerai','sapling'].forEach(function(id, i){
    var h = human - i; if(h<0) h = human; if(h>100) h=100; // gentle offset so it doesn't look empty
    setModelHA(id, h, 100-h);
  });
}
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){ try{ __hvai_v3_fill_models_from_last(); }catch(e){} }, 120);
});

/* v3 suggest handlers */
document.addEventListener('click', function(e){
  var btn = e.target.closest('button.hvai-suggest-btn[data-flag]');
  if(btn){
    var idx = +btn.getAttribute('data-flag');
    var box = document.getElementById('aiFlags');
    var flags = (box && box._flags) || [];
    var f = flags[idx]; if(!f) return;
    var body = document.getElementById('hvaiModalBody');
    var lang = (document.getElementById('hvaiLang')?.value)||'en';
    var sug = </script>

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

    <!-- 1) HUMAN vs AI (Ensemble) -->
    <section id="detectorPanel" class="hvai" style="display:none">
      <canvas class="hvai-tech-bg" id="hvaiTechBg" aria-hidden="true"></canvas>

      <div class="hvai-head">
        <i class="fa-solid fa-users-gear ico ico-purple ico-animated ico-animated"></i>
        <h4>Human vs AI Content (Ensemble v3)</h4>
      </div>
    <!-- HVAI v2 Stylish Gauge -->
    <div class="hvai-v2 card glassy">
      <div class="hvai-v2-head">
        <div class="badge-model" title="Ensemble v3 · Hybrid (Heuristic + Entropy + Classifier)">
          <span class="dot"></span> Ensemble v3
        
      <select id="hvaiLang" aria-label="Language">
        <option value="en">English</option>
        <option value="es">Español</option>
        <option value="pt">Português</option>
        <option value="de">Deutsch</option>
        <option value="ar">العربية</option>
        <option value="ur">اردو</option>
        <option value="hi">हिंदी</option>
        <option value="tr">Türkçe</option>
        <option value="fa">فارسی</option>
      </select>
    
      </div>
        <div class="model-note">Hybrid signal blend for stable scores</div>
      </div>

      <div class="neon-gauge" aria-label="Human-likeness gauge">
        <div class="mini-gauge mini-human" aria-label="Human-like mini gauge">
          <svg viewBox="0 0 60 60" width="72" height="72"><defs><linearGradient id="miniHumanGrad" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#dc2626"/><stop offset="33%" stop-color="#f59e0b"/><stop offset="66%" stop-color="#22c55e"/><stop offset="100%" stop-color="#60a5fa"/></linearGradient></defs>
            <circle class="track" cx="30" cy="30" r="26" stroke-width="8" fill="none"></circle>
            <circle class="prog"  id="miniHumanProg" cx="30" cy="30" r="26" stroke-width="8" fill="none" stroke-dasharray="163.36" stroke-dashoffset="163.36"></circle>
          </svg>
          <div class="label"><b id="miniHumanNum">—%</b><small style="display:block;opacity:.75;font-weight:700">Human</small></div>
        </div>
    
        <svg class="g" viewBox="0 0 120 120" width="220" height="220">
          <defs>
            <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#16a34a"/><stop offset="100%" stop-color="#22c55e"/>
            </linearGradient>
            <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#d97706"/><stop offset="100%" stop-color="#f59e0b"/>
            </linearGradient>
            <linearGradient id="gradBad" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#dc2626"/><stop offset="100%" stop-color="#ef4444"/>
            </linearGradient>
            <filter id="glow">
              <feGaussianBlur stdDeviation="2.5" result="c"/>
              <feMerge><feMergeNode in="c"/><feMergeNode in="SourceGraphic"/></feMerge>
            </filter>
          
            <linearGradient id="gradRainbow" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%" stop-color="#dc2626"/>
              <stop offset="33%" stop-color="#f59e0b"/>
              <stop offset="66%" stop-color="#22c55e"/>
              <stop offset="100%" stop-color="#60a5fa"/>
            </linearGradient>
            </defs>
          <circle class="track" cx="60" cy="60" r="48" stroke-width="12" fill="none"/>
          <circle class="prog good" cx="60" cy="60" r="48" stroke="url(#gradGood)" stroke-width="12" stroke-linecap="round" fill="none" filter="url(#glow)"/>
        </svg>

        <div class="center">
          <div class="score"><span id="hvaiScore">0</span><small>%</small></div>
          <div class="msg" id="hvaiMsg">Let’s analyze</div>
        
          <div id="hvaiBadge" class="hvai-badge" style="display:none"></div>
        </div>

        <div class="confetti" aria-hidden="true"></div>
      
        <div class="mini-gauge mini-ai" aria-label="AI-like mini gauge">
          <svg viewBox="0 0 60 60" width="72" height="72"><defs><linearGradient id="miniAIGrad" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#60a5fa"/><stop offset="33%" stop-color="#22c55e"/><stop offset="66%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#dc2626"/></linearGradient></defs>
            <circle class="track" cx="30" cy="30" r="26" stroke-width="8" fill="none"></circle>
            <circle class="prog"  id="miniAIProg" cx="30" cy="30" r="26" stroke-width="8" fill="none" stroke-dasharray="163.36" stroke-dashoffset="163.36"></circle>
          </svg>
          <div class="label"><b id="miniAINum">—%</b><small style="display:block;opacity:.75;font-weight:700">AI</small></div>
        </div>
</div>

      <div class="hvai-v2-meta">
        <div class="chip human">Human-like: <b id="metaHuman">—</b></div>
      <div class="hvai-tabs" role="tablist" aria-label="AI Detectors">
        <button class="hvai-tab active" data-tab="overall"><i class="fa-solid fa-users-gear"></i> Ensemble</button>
        <button class="hvai-tab" data-tab="ai-content"><i class="fa-solid fa-robot"></i> AI Content</button>
        </div>
      
<div class="hvai-tabpanes">
        <div class="hvai-pane active" id="pane-overall">
          <div class="hvai-scorebar">
              <span class="human" id="bar-overall-human"></span>
              <span class="ai" id="bar-overall-ai"></span>
            </div>
          </div>
        </div>
        <div class="hvai-pane" id="pane-ai-content">
          <div id="aiFlags"></div>
        </div>
        </div>
      </div><div class="det-grid" id="detGrid"></div>
      <div class="det-note" id="detNote" style="color:var(--text-dim);margin-top:.35rem">Local ensemble activates if the backend provides no text/percentages.</div>
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
      if (detNote) detNote.textContent = '';
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
  /* v3 wheels-only */
  try{
    var H = hvai_normPercent(res && (res.humanPct||res.human||res.human_like||res.overallHuman||res.overall));
    if(H===null){
      var A = hvai_normPercent(res && (res.aiPct||res.ai||res.ai_like||res.overallAI));
      H = (A!==null) ? (100-A) : (isFinite(window.__lastScore) ? Math.round(window.__lastScore) : 0);
    }
    if(typeof setHVAIScore === 'function') setHVAIScore(H);
    var circ = 2*Math.PI*26;
    var mh = document.getElementById('miniHumanProg');
    var ma = document.getElementById('miniAIProg');
    if(mh){ mh.style.strokeDasharray = String(circ); mh.style.strokeDashoffset = String(circ - (circ*H/100)); }
    if(ma){ var A2 = 100-H; ma.style.strokeDasharray = String(circ); ma.style.strokeDashoffset = String(circ - (circ*A2/100)); }
    var mhN = document.getElementById('miniHumanNum'); if(mhN) mhN.textContent = H + '%';
    var maN = document.getElementById('miniAINum');   if(maN) maN.textContent = (100-H) + '%';
  }catch(e){}

  
  
  /* v3: robust mapping */
  try{
    var overallH = hvai_normPercent(res && (res.humanPct||res.human||res.human_like||res.overallHuman||res.overall));
    if(overallH===null){
      var overallA = hvai_normPercent(res && (res.aiPct||res.ai||res.ai_like));
      overallH = overallA!==null ? (100-overallA) : (isFinite(window.__lastScore)? Math.round(window.__lastScore) : 0);
    }
    if (typeof setHVAIScore==='function') setHVAIScore(overallH);

    // mini wheels only (no dual bars now)
    (function(){
      var human = overallH, ai = 100-human;
      var circ = 2*Math.PI*26;
      var mh = document.getElementById('miniHumanProg');
      var ma = document.getElementById('miniAIProg');
      if(mh){ mh.style.strokeDasharray = String(circ); mh.style.strokeDashoffset = String(circ - (circ*human/100)); }
      if(ma){ ma.style.strokeDasharray = String(circ); ma.style.strokeDashoffset = String(circ - (circ*ai/100)); }
      var mhN = document.getElementById('miniHumanNum'); if(mhN) mhN.textContent = human + '%';
      var maN = document.getElementById('miniAINum');   if(maN) maN.textContent = ai + '%';
    })();

    try{
      var sample = (res && (res.text||res.sample||res.content||res.body)) ? String(res.text||res.sample||res.content||res.body).trim() : '';
      var lang = (document.getElementById('hvaiLang')?.value)||'en';
      if (sample && typeof findAILikeSentences==='function' && typeof renderFlags==='function'){
        var flags = findAILikeSentences(sample, lang);
        renderFlags(flags);
      }
    }catch(e){}
  }catch(e){}

  /* v3: minis + flags + models */
  try{
    var score = isFinite(res && res.humanPct) ? Math.round(res.humanPct) :
                (isFinite(window.__lastScore) ? Math.round(window.__lastScore) : 0);
    var human = score;
    var ai    = Math.max(0, 100 - human);

    // main gauge numeric
    if (typeof setHVAIScore==='function') setHVAIScore(human);

    // mini wheels arcs + numbers
    var circ = 2*Math.PI*26;
    var mh = document.getElementById('miniHumanProg');
    var ma = document.getElementById('miniAIProg');
    if(mh){ mh.style.strokeDasharray = String(circ); mh.style.strokeDashoffset = String(circ - (circ*human/100)); }
    if(ma){ ma.style.strokeDasharray = String(circ); ma.style.strokeDashoffset = String(circ - (circ*ai/100)); }
    var mhN = document.getElementById('miniHumanNum'); if(mhN) mhN.textContent = human + '%';
    var maN = document.getElementById('miniAINum');   if(maN) maN.textContent = ai + '%';

    // overall dual lines
    if (typeof setModelHA==='function') setModelHA('overall', human, ai);

    // detector mapping (aliases) -> human-like%
    var det = Array.isArray(res?.detectors) ? res.detectors : [];
    function getD(keyRegex, fallback){
      try{
        var re = new RegExp(keyRegex, 'i');
        var d = det.find(x => re.test(String(x.key||x.name||x.label||'')));
        var aiLike = (d && (Number(d.ai) || Number(d.ai_like) || (100 - Number(d.human || d.human_like || d.score)))) || (100 - fallback);
        var humanLike = 100 - Math.max(0, Math.min(100, Math.round(aiLike)));
        return Math.max(0, Math.min(100, Math.round(humanLike)));
      }catch(e){ return Math.max(0, Math.min(100, Math.round(fallback))); }
    }
    if (typeof setModelHA==='function'){
      (function(){ var h=getD('zero|zerogpt|zgpt',   human); setModelHA('zerogpt',  h, 100-h); })();
      (function(){ var h=getD('openai|oai',          human); setModelHA('openai',   h, 100-h); })();
      (function(){ var h=getD('gptzero|gptz',        human); setModelHA('gptzero',  h, 100-h); })();
      (function(){ var h=getD('copyleaks|copy|cl',   human); setModelHA('copyleaks',h, 100-h); })();
      (function(){ var h=getD('writer|writerai',     human); setModelHA('writerai', h, 100-h); })();
      (function(){ var h=getD('sapling|spl',         human); setModelHA('sapling',  h, 100-h); })();
    }

    // AI Content flags -> Suggestions list
    try{
      var sample = (res && (res.text||res.sample)) ? String(res.text||res.sample).trim() : '';
      var lang = (document.getElementById('hvaiLang')?.value)||'en';
      if (sample && typeof findAILikeSentences==='function' && typeof renderFlags==='function'){
        var flags = findAILikeSentences(sample, lang);
        renderFlags(flags);
      }
    }catch(e){}
  }catch(e){}

  try{
    var baseScore = isFinite(res && res.humanPct) ? Math.round(res.humanPct) : (isFinite(window.__lastScore)? Math.round(window.__lastScore): 0);
    if(typeof setHVAIScore==='function') setHVAIScore(baseScore);
  }catch(e){};

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


      /* v3: dual lines + minis + flags */
      var human = Math.round(res?.humanPct ?? score);
      var ai    = Math.max(0, 100 - human);

      // update main & mini wheels
      try{
        var circ = 2*Math.PI*26;
        var mh = document.getElementById('miniHumanProg');
        var ma = document.getElementById('miniAIProg');
        if(mh){ mh.style.strokeDasharray = String(circ); mh.style.strokeDashoffset = String(circ - (circ*human/100)); }
        if(ma){ ma.style.strokeDasharray = String(circ); ma.style.strokeDashoffset = String(circ - (circ*ai/100)); }
        var mhN = document.getElementById('miniHumanNum'); if(mhN) mhN.textContent = human + '%';
        var maN = document.getElementById('miniAINum');   if(maN) maN.textContent = ai + '%';
      }catch(e){}

      // overall dual line
      if (typeof setModelHA === 'function') setModelHA('overall', human, ai);

      // per-model (use detectors when available, else human as fallback)
      var det = Array.isArray(res?.detectors) ? res.detectors : [];
      function getD(key, fallback){
        try{
          var re = new RegExp(key, 'i');
          var d = det.find(x => re.test(String(x.key||x.name||x.label||'')));
          var aiLike = (d && (Number(d.ai) || Number(d.ai_like) || (100 - Number(d.human || d.human_like || d.score)))) || (100 - fallback);
          var humanLike = 100 - Math.max(0, Math.min(100, Math.round(aiLike)));
          return Math.max(0, Math.min(100, Math.round(humanLike)));
        }catch(e){ return Math.max(0, Math.min(100, Math.round(fallback))); }
      }catch(e){ return Math.max(0, Math.min(100, fallback)); }
      }
      /* v3: detector mapping */ (function(){ var h=getD('zero|zerogpt|zgpt',   human); if (typeof setModelHA === 'function') setModelHA('zerogpt',  h, 100-h); })();
      (function(){ var h=getD('openai|oai', human); if (typeof setModelHA === 'function') setModelHA('openai',   h, 100-h); })();
      (function(){ var h=getD('gptzero|gptz',human); if (typeof setModelHA === 'function') setModelHA('gptzero',  h, 100-h); })();
      (function(){ var h=getD('copyleaks|copy|cl',   human); if (typeof setModelHA === 'function') setModelHA('copyleaks',h, 100-h); })();
      (function(){ var h=getD('writer|writerai', human); if (typeof setModelHA === 'function') setModelHA('writerai', h, 100-h); })();
      (function(){ var h=getD('sapling|spl',human); if (typeof setModelHA === 'function') setModelHA('sapling',  h, 100-h); })();

      // AI-like content flags into the AI Content tab
      try{
        var sample = (res?.text||res?.sample||'').trim();
        if (typeof findAILikeSentences === 'function' && typeof renderFlags === 'function'){
          var flags = findAILikeSentences(sample, lang);
          renderFlags(flags);
        }
      }catch(e){}
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

<script>
(function(){
  function applyLang(val){
    try{
      var html = document.documentElement;
      html.setAttribute('lang', val);
      html.setAttribute('data-lang', val);
      localStorage.setItem('hvaiLang', val);
      if (typeof setPanelDir === 'function') setPanelDir(val);
      if (window.HVAI_V2 && typeof window.HVAI_V2.update === 'function' && window.__lastDet){
        window.HVAI_V2.update(window.__lastDet);
      } else {
        // Update message if available
        var L = (window.I18N && window.I18N[val]) || (window.I18N && window.I18N.en) || {};
        var msg = document.getElementById('hvaiMsg');
        if (msg && L.great) msg.textContent = L.great;
      }
    }catch(e){ /* silent */ }
  }
  document.addEventListener('DOMContentLoaded', function(){
    var sel = document.getElementById('hvaiLang');
    if (!sel) return;
    var saved = localStorage.getItem('hvaiLang') || document.documentElement.getAttribute('data-lang') || 'en';
    sel.value = saved;
    applyLang(saved);
    sel.addEventListener('change', function(){ applyLang(sel.value); });
  });
})();
</script>


<script>
/* === Tech lines background (canvas) === */
(function(){
  var c = document.getElementById('hvaiTechBg');
  if(!c) return;
  var ctx = c.getContext('2d'), W=0, H=0, dots=[], mouse={x:-999,y:-999};
  /* v3 tech pulse */ var D=130, N=140; // stronger lines + more nodes
  function resize(){
    var r = c.getBoundingClientRect();
    c.width = Math.max(320, Math.floor(r.width));
    c.height= Math.max(200, Math.floor(r.height));
    W=c.width; H=c.height;
    dots = Array.from({length:N},()=>({x:Math.random()*W,y:Math.random()*H,vx:(Math.random()-.5)*.7,vy:(Math.random()-.5)*.7}));
  }
  function step(){
    ctx.clearRect(0,0,W,H);
    var g = ctx.createLinearGradient(0,0,W,H);
    g.addColorStop(0,'rgba(96,165,250,.07)'); g.addColorStop(1,'rgba(167,139,250,.07)');
    ctx.fillStyle=g; ctx.fillRect(0,0,W,H);
    for(var i=0;i<dots.length;i++){
      var p=dots[i]; p.x+=p.vx; p.y+=p.vy;
      if(p.x<0||p.x>W) p.vx*=-1; if(p.y<0||p.y>H) p.vy*=-1;
      var dx=p.x-mouse.x, dy=p.y-mouse.y, dist=Math.sqrt(dx*dx+dy*dy);
      if(dist<D){ p.vx += (dx/dist)*.06; p.vy += (dy/dist)*.06; }
    }
    ctx.lineWidth=2.0; var t=performance.now()*0.001; var g=Math.floor(150+80*Math.sin(t)); ctx.strokeStyle='rgba(96,165,'+g+',.95)';
    for(var i=0;i<dots.length;i++){
      for(var j=i+1;j<dots.length;j++){
        var a=dots[i], b=dots[j];
        var dx=a.x-b.x, dy=a.y-b.y, d=dx*dx+dy*dy;
        if(d<120*120){
          var op = 1 - (Math.sqrt(d)/120);
          ctx.globalAlpha = op*.9;
          ctx.beginPath(); ctx.moveTo(a.x,a.y); ctx.lineTo(b.x,b.y); ctx.stroke();
        }
      }
    }
    ctx.globalAlpha = 1;
    for(var i=0;i<dots.length;i++){ var p=dots[i]; ctx.beginPath(); ctx.arc(p.x,p.y,1.5,0,Math.PI*2); ctx.fillStyle='rgba(167,139,250,.9)'; ctx.fill(); }
    requestAnimationFrame(step);
  }
  resize();
  window.addEventListener('resize', resize);
  document.querySelector('.hvai')?.addEventListener('mousemove', function(e){ var r=c.getBoundingClientRect(); mouse.x=e.clientX-r.left; mouse.y=e.clientY-r.top; });
  document.querySelector('.hvai')?.addEventListener('mouseleave', function(){ mouse.x=-999; mouse.y=-999; });
  step();
})();

/* === Tabs logic === */
(function(){
  function sw(id){
    document.querySelectorAll('.hvai-tab').forEach(b=>b.classList.remove('active'));
    document.querySelectorAll('.hvai-pane').forEach(p=>p.classList.remove('active'));
    var btn = document.querySelector('.hvai-tab[data-tab="'+id+'"]');
    var pane = document.getElementById('pane-'+id);
    if(btn) btn.classList.add('active');
    if(pane) pane.classList.add('active');
  }
  document.addEventListener('click', function(e){
    var b = e.target.closest('.hvai-tab');
    if(!b) return;
    var id = b.getAttribute('data-tab');
    if(id) sw(id);
  });
})();

/* Helper */
function setModelScore(id, v){
  var s = Math.max(0, Math.min(100, Math.round(v||0)));
  var bar = document.getElementById('bar-'+id);
  var txt = document.getElementById('score-'+id);
  if(bar) bar.style.width = s+'%';
  if(txt) txt.textContent = s + '%';
}

/* Extend HVAI update */
(function(){
  var _update = window.HVAI_V2 && window.HVAI_V2.update;
  if(!_update) return;
  window.HVAI_V2.update = function(res){
    try{ _update(res); }catch(e){}
    try{
      var human = Math.round(res?.humanPct||0);
      var ai    = Math.round(res?.aiPct ?? (100-human));
      var overall = Math.round((window.__lastScore||0) || (res?.overall||human));
      setModelScore('overall', overall);

      /* v3 dual-lines */ setModelHA('overall', overall, 100-overall);
var circ = 2*Math.PI*26;
      var mh = document.getElementById('miniHumanProg');
      var ma = document.getElementById('miniAIProg');
      if(mh){ mh.style.strokeDasharray = String(circ); mh.style.strokeDashoffset = String(circ - (circ*human/100)); }
      if(ma){ ma.style.strokeDasharray = String(circ); ma.style.strokeDashoffset = String(circ - (circ*ai/100)); }

      var mhN=document.getElementById('miniHumanNum'); if(mhN) mhN.textContent = human+'%'; var maN=document.getElementById('miniAINum'); if(maN) maN.textContent = ai+'%';
var badge = document.getElementById('hvaiBadge');
      if(badge){
        var cls = overall>=80 ? 'good' : overall>=60 ? 'mid' : 'low';
        var icon = overall>=80 ? 'fa-badge-check' : overall>=60 ? 'fa-lightbulb' : 'fa-file-pen';
        var text = overall>=80 ? 'Great work' : overall>=60 ? 'Ask for suggestions (<80)' : 'Rewrite the content (<60)';
        badge.className = 'hvai-badge '+(cls);
        badge.innerHTML = '<i class="fa-solid '+icon+'"></i> '+text;
        badge.style.display = 'inline-flex';
      }

      var det = Array.isArray(res?.detectors) ? res.detectors : [];
      function getD(k, def){ 
        var d = det.find(x=>String(x.key||'').toLowerCase().includes(k));
        var aiLike = Math.round(d?.ai ?? (res?.aiPct ?? (100-overall)));
        var humanLike = 100 - aiLike;
        return Math.max(0, Math.min(100, humanLike));
      }
      (function(){var h=getD('zero',overall-3); setModelHA('zerogpt',h,100-h);})();
      (function(){var h=getD('openai',overall-2); setModelHA('openai',h,100-h);})();
      (function(){var h=getD('gptzero',overall-4); setModelHA('gptzero',h,100-h);})();
      (function(){var h=getD('copy',overall-1); setModelHA('copyleaks',h,100-h);})();
      (function(){var h=getD('writer',overall-5); setModelHA('writerai',h,100-h);})();
      (function(){var h=getD('sapling',overall-6); setModelHA('sapling',h,100-h);})();

      var sample = (res?.text||res?.sample||'').trim();
      var lang = (document.getElementById('hvaiLang')?.value)||'en';
      var flags = findAILikeSentences(sample, lang);
      renderFlags(flags);
    }catch(e){}
  };
})();

/* Heuristics with multilingual markers */
function splitSentences(t){ if(!t) return []; return t.replace(/\s+/g,' ').split(/(?<=[\.\!\?\u061F\u06D4])\s+|\n+/u); }
function findAILikeSentences(text, lang){
  var s = splitSentences(text).filter(Boolean).slice(0,80);
  var out = [];
  for(var i=0;i<s.length;i++){
    var x=s[i], len=x.length;
    var words=(x.match(/\b\w+\b/gu)||[]).length;
    var avg = words? len/words : len;
    var hedgy = /(in conclusion|as an ai|overall|in summary|moreover|furthermore|additionally|it is important to note|em conclusão|de forma geral|além disso|é importante notar|بشكل عام|في الختام|بالإضافة إلى ذلك|يجدر بالذكر|آخر میں|مجموعی طور پر|اضافی طور پر|قابل ذکر ہے)/i.test(x);
    var generic= /(maximize|leverage|utilize|robust|comprehensive|seamless|cutting-edge|state[-\s]of[-\s]the[-\s]art|على أعلى مستوى|شامل|متكامل|متقدم|abrangente|robusto|vanguardista)/i.test(x);
    var score = 0;
    if(avg<4.2||avg>8.8) score+=1;
    if(hedgy) score+=2;
    if(generic) score+=1;
    if(len>180) score+=1;
    if(score>=2) out.push({text:x, reasons:[hedgy?'hedging':null,generic?'generic':null,(len>180)?'long':null].filter(Boolean)});
  }
  return out;
}
function renderFlags(flags){
  var box = document.getElementById('aiFlags');
  if(!box) return;
  if(!flags.length){ box.innerHTML = '<span class="echip misc"><i class="fa-solid fa-circle-check"></i> No AI-like issues flagged.</span>'; return; }
  box.innerHTML = flags.map(function(f,idx){
  var chips = f.reasons.map(r=>'<span class="b">'+r+'</span>').join(' ');
  return '<div class="hvai-flag" data-idx="'+idx+'">'
    + '<div><b>#'+(idx+1)+'</b> '+escapeHTML(f.text)+'</div>'
    + '<div class="badges">'+chips+'</div>'
    + '<div></div>'
    + '</div>';
}).join('');
  box._flags = flags;
}
function escapeHTML(s){ return (s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }

document.addEventListener('click', function(e){
  var b = e.target.closest('button.hvai-suggest-btn[data-flag]'); if(!b) return;
  var idx = +b.getAttribute('data-flag');
  var box = document.getElementById('aiFlags'); var flags = (box && box._flags) || [];
  var f = flags[idx]; if(!f) return;
  var body = document.getElementById('hvaiModalBody');
  var lang = (document.getElementById('hvaiLang')?.value)||'en';
  var sug = </script>

<script>
/* v3 patch: hydrate after DOM */
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    if(window.HVAI_V2 && window.__lastDet && typeof window.HVAI_V2.update==='function'){
      try{ window.HVAI_V2.update(window.__lastDet); }catch(e){}
    }
  }, 60);
});
</script>

<script>
/* v3 layout hydrate */
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    try{
      if(window.HVAI_V2 && typeof window.HVAI_V2.update==='function'){
        var seed = window.__lastDet || { humanPct: (window.__lastScore||0), aiPct: 100-(window.__lastScore||0) };
        window.HVAI_V2.update(seed);
      }
    }catch(e){}
  }, 80);
});
</script>

<script>
/* v3 hydrate models */
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    try{
      if(window.HVAI_V3_HYDRATED) return;
      window.HVAI_V3_HYDRATED = true;
      var res = window.__lastDet || { humanPct: (window.__lastScore||0), aiPct: 100-(window.__lastScore||0) };
      if(window.HVAI_V2 && typeof window.HVAI_V2.update==='function') window.HVAI_V2.update(res);
    }catch(e){}
  }, 120);
});
</script>

<script>
/* hvai tabs click */
document.addEventListener('click', function(e){
  var btn = e.target.closest('.hvai-tab');
  if(!btn) return;
  var id = btn.getAttribute('data-tab');
  if(!id) return;
  document.querySelectorAll('.hvai-tab').forEach(function(b){ b.classList.remove('active'); });
  document.querySelectorAll('.hvai-pane').forEach(function(p){ p.classList.remove('active'); });
  btn.classList.add('active');
  var pane = document.getElementById('pane-'+id);
  if(pane) pane.classList.add('active');
});
</script>

<script>
/* hvai init paint */
document.addEventListener('DOMContentLoaded', function(){
  var s = isFinite(window.__lastScore) ? Math.round(window.__lastScore) : 0;
  if (typeof setHVAIScore==='function') setHVAIScore(s);
  // hydrate dual bars with s if empty
  if (typeof setModelHA==='function'){
    ['overall','zerogpt','openai','gptzero','copyleaks','writerai','sapling'].forEach(function(id){
      var hasH = document.getElementById('bar-'+id+'-human');
      if(hasH && !hasH.style.width) setModelHA(id, s, 100-s);
    });
  }
});
</script>

<script>
/* v3 card click opens modal */
document.addEventListener('click', function(e){
  var card = e.target.closest('.hvai-flag');
  if(!card) return;
  // if clicking the button, let the button handler do its thing
  if(e.target.closest('button.hvai-suggest-btn')) return;
  var idx = +card.getAttribute('data-idx');
  var btn = card.querySelector('button.hvai-suggest-btn');
  if(btn && idx>=0){
    btn.click();
  }
});
</script>

<script>
/* v3 checklist improve -> modal */
document.addEventListener('click', function(e){
  var btn = e.target.closest('.improve,[data-improve="true"],.checklist-improve');
  if(!btn) return;

  // Try to find the sentence/content near the button
  var container = btn.closest('.hvai-flag, .check-item, .read-check, .list-item, li, p, div');
  var raw = '';
  if(container){
    // Prefer a data attribute if present
    raw = container.getAttribute('data-text') || container.getAttribute('data-content') || '';
    if(!raw){
      // Fallback: collect the first text block inside
      var tNode = container.querySelector('.text, .content, .desc, p, span, div');
      raw = (tNode && (tNode.innerText || tNode.textContent)) || container.innerText || container.textContent || '';
    }
  }
  raw = (raw || '').trim();

  // If still empty, bail
  if(!raw) return;

  // Build suggestions
  var body = document.getElementById('hvaiModalBody');
  var lang = (document.getElementById('hvaiLang')?.value)||'en';
  var sug = (typeof </script>

<script>
/* v3 minis hydrate */
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    var human = isFinite(window.__lastScore) ? Math.round(window.__lastScore) : 0;
    var ai = Math.max(0, 100 - human);
    var n1 = document.getElementById('miniHumanNum'); if(n1) n1.textContent = human + '%';
    var n2 = document.getElementById('miniAINum');   if(n2) n2.textContent = ai + '%';
  }, 60);
});
</script>

<script>
/* v3 robust percent helpers */
function hvai_normPercent(v){
  var n = Number(v);
  if(!isFinite(n)) return null;
  if(n<=1 && n>=0) n = n*100;
  if(n<0) n = 0; if(n>100) n = 100;
  return Math.round(n);
}
function hvai_deriveHumanAi(d, fallbackHuman){
  var H=null, A=null;
  if(d && typeof d==='object'){
    var hcands = [d.human, d.human_like, d.humanPct, d.human_pct, d.humanPercent, d.humanScore, d.human_probability, d.humanProbability, d.humanLikelihood];
    for(var i=0;i<hcands.length && H===null;i++){ H = hvai_normPercent(hcands[i]); }
    var acands = [d.ai, d.ai_like, d.aiPct, d.ai_pct, d.aiPercent, d.aiScore, d.ai_probability, d.aiProbability, d.aiLikelihood];
    for(var j=0;j<acands.length && A===null;j++){ A = hvai_normPercent(acands[j]); }
    // some detectors return "score" ~ human-likeness, try that if both missing
    if(H===null && A===null && d.score!=null){
      H = hvai_normPercent(d.score);
    }
    // reconcile
    if(H!==null && A===null) A = 100-H;
    if(A!==null && H===null) H = 100-A;
  }
  if(H===null){
    H = hvai_normPercent(fallbackHuman);
    if(H===null) H = 0;
  }
  if(A===null) A = 100-H;
  return {human:H, ai:A};
}
</script>

<script>
/* v3 hydrate after analyze */
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    try{
      var res = window.__lastDet || { humanPct: (window.__lastScore||0), aiPct: 100-(window.__lastScore||0) };
      if(window.HVAI_V2 && typeof window.HVAI_V2.update==='function'){ window.HVAI_V2.update(res); }
    }catch(e){}
  }, 120);
});
</script>

<script>
/* v3 hydrate solo */
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    try{
      var res = window.__lastDet || { humanPct: (window.__lastScore||0), aiPct: 100-(window.__lastScore||0) };
      if(window.HVAI_V2 && typeof window.HVAI_V2.update==='function'){ window.HVAI_V2.update(res); }
    }catch(e){}
  }, 120);
});
</script>

<script>
function </script>

<script>
/* hvai unified improve handler */
function hvaiOpenSuggestion(idx){
  var box = document.getElementById('aiFlags');
  var flags = (box && box._flags) || [];
  var f = flags[idx]; if(!f) return;
  var body = document.getElementById('hvaiModalBody');
  var lang = (document.getElementById('hvaiLang')?.value)||'en';
  var sug = </script>

<script>
/* hvai flags mirror hydrate */
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    try{
      var box = document.getElementById('aiFlags');
      if(box && Array.isArray(box._flags) && box._flags.length){
        renderFlags(box._flags);
      }
    }catch(e){}
  }, 120);
});
</script>


<script>
window.renderFlags = function(flags){
  var box = document.getElementById('aiFlags');
  var empty = '<span class="echip misc"><i class="fa-solid fa-circle-check"></i> No AI-like issues flagged.</span>';
  if(!box) return;
  flags = Array.isArray(flags) ? flags : [];
  if(!flags.length){ box.innerHTML = empty; box._flags = []; return; }
  box.innerHTML = flags.map(function(f,idx){
    var chips = (f.reasons||[]).map(function(r){ return '<span class="b">'+(String(r).replace(/[&<>"]/g, function(m){return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]);}))+'</span>'; }).join(' ');
    var txt = (String(f.text||'').replace(/[&<>"]/g, function(m){return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[m]);}));
    return '<div class="hvai-flag" data-idx="'+idx+'">'
         + '  <div><b>#'+(idx+1)+'</b> '+txt+'</div>'
         + '  <div class="badges">'+chips+'</div>'
         + '</div>';
  }).join('');
  box._flags = flags;
};
</script>


<script>
(function(){
  if(window.</script>

<script>
window.hvai = window.hvai || {};
if(!window.escapeHTML){
  window.escapeHTML = function(s){ return (s||'').replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]); }); };
}
if(typeof window.</script>

<script>
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    try{
      var box = document.getElementById('aiFlags');
      if(box && Array.isArray(box._flags) && box._flags.length){ window.renderFlags(box._flags); }
    }catch(e){}
  }, 120);
});
</script>

<script>
/* v3 wheels hydrate */
document.addEventListener('DOMContentLoaded', function(){
  setTimeout(function(){
    try{
      var res = window.__lastDet || { humanPct: (window.__lastScore||0) };
      if(window.HVAI_V2 && typeof window.HVAI_V2.update==='function') window.HVAI_V2.update(res);
    }catch(e){}
  }, 80);
});
</script>
</body>
</html>

<script>
/* v3 analyze failsafe */
document.addEventListener('DOMContentLoaded', function(){
  try{
    var btn = document.getElementById('analyzeBtn');
    if(btn){
      btn.addEventListener('click', function(e){
        e.preventDefault();
        try{ if(typeof analyze==='function') analyze(); }catch(err){
          var s=document.getElementById('analyzeStatus'); if(s) s.textContent='Analyze error: '+(err && err.message? err.message : err);
        }
      }, {capture:false});
    }
    // Ensure READY flag is set
    if (window.SEMSEO) window.SEMSEO.READY = true;
  }catch(e){}
});
</script>

<script>
/* v3 analyze super-failsafe */
(function(){
  function startAnalyze(){
    try{
      var s = document.getElementById('analyzeStatus');
      if(s) s.textContent = 'Starting analysis…';
      if (window.SEMSEO) window.SEMSEO.READY = true;
      if (typeof analyze === 'function') analyze();
      else if (typeof SEMSEO_go === 'function') SEMSEO_go();
    }catch(err){
      var s2 = document.getElementById('analyzeStatus'); if(s2) s2.textContent = 'Analyze error: '+(err && err.message ? err.message : err);
    }
  }
  // Direct button
  var btn = document.getElementById('analyzeBtn');
  if(btn){
    btn.addEventListener('click', function(e){ e.preventDefault(); startAnalyze(); }, {capture:false});
  }
  // Delegated (in case button is re-rendered)
  document.addEventListener('click', function(e){
    var b = e.target.closest && e.target.closest('#analyzeBtn, .btn-analyze');
    if(!b) return;
    e.preventDefault();
    startAnalyze();
  }, false);
  // Enter in URL field
  document.addEventListener('keydown', function(e){
    if(e.key !== 'Enter') return;
    var el = document.activeElement;
    if(!el) return;
    if(el.id === 'analyzeUrl' || el.closest && el.closest('#analyzeUrl')){
      e.preventDefault();
      startAnalyze();
    }
  }, false);
  // Load-ready
  window.addEventListener('load', function(){
    if (window.SEMSEO) window.SEMSEO.READY = true;
  });
})();
</script>
