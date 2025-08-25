{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle       = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, UX and speed. Human vs AI, Readability, Entities & Topics, and PageSpeed in one colorful dashboard.';
  $metaImage       = asset('og-image.png');
  $canonical       = url()->current();
  $analyzeJsonUrl  = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl      = Route::has('analyze')      ? route('analyze')      : url('analyze');
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
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)}.l-orange{background:rgba(245,158,11,.18)}.l-green{background:rgba(34,197,94,.18)}
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
.water-pct{position:absolute;inset:0;display:grid;place-items:center;font-weight:1000;font-size:1.05rem;z-index:4}
.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22);border-color:rgba(22,193,114,.45)}
.score-mid{background:rgba(245,158,11,.22);border-color:rgba(245,158,11,.45)}
.score-bad{background:rgba(239,68,68,.24);border-color:rgba(239,68,68,.5)}
.detector{margin-top:14px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px;box-shadow:var(--shadow)}
.det-head{display:flex;align-items:center;gap:.6rem;margin-bottom:.4rem}
.det-head h4{margin:0;font-size:1.05rem}
.det-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.6rem}
.det-item{grid-column:span 6;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:.6rem}
.det-row{display:grid;grid-template-columns:1fr auto;gap:.5rem;align-items:center}
.det-label{font-weight:800;color:var(--text-dim)}
.det-score{font-weight:1000}
.det-bar{margin-top:.4rem;position:relative;height:14px;border-radius:10px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
.det-fill{position:absolute;left:0;top:0;bottom:0;width:0;background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e);transition:width .35s ease}

/* --- NEW: Readability & Entities cards --- */
.kpi-grid{display:grid;grid-template-columns:repeat(12,1fr);gap:.7rem;margin-top:.2rem}
.kpi{grid-column:span 3;background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:14px;padding:.7rem;display:grid;gap:.25rem}
.kpi .kpi-name{font-weight:900;color:var(--text-dim);display:flex;align-items:center;gap:.45rem}
.kpi .kpi-val{font-weight:1000;font-size:1.15rem}
.kpi.good{background:rgba(34,197,94,.18)}.kpi.mid{background:rgba(245,158,11,.18)}.kpi.bad{background:rgba(239,68,68,.18)}
.tag-grid{display:flex;flex-wrap:wrap;gap:.35rem;margin-top:.35rem}
.tag{display:inline-flex;align-items:center;gap:.35rem;padding:.25rem .55rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800;background:rgba(255,255,255,.06)}
.tag.soft{background:rgba(61,226,255,.10);border-color:rgba(61,226,255,.25)}
.group-card{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);border-radius:14px;padding:.65rem}
.group-title{margin:0 0 .35rem;font-weight:900;display:flex;align-items:center;gap:.5rem}
.group-note{color:var(--text-dim);font-size:.9rem;margin-top:.25rem}

/* responsive tweaks */
@media (max-width:992px){.analyze-row{grid-template-columns:1fr auto auto}.kpi{grid-column:span 6}.det-item{grid-column:span 12}}
@media (max-width:768px){.wrap{padding:18px 4%}.analyze-row{grid-template-columns:1fr}.kpi{grid-column:span 12}}
@media print{#linesCanvas,#smokeCanvas{display:none}}
</style>
</head>
<body>

<canvas id="linesCanvas"></canvas>
<canvas id="smokeCanvas"></canvas>

<script>
/* tiny global to queue Analyze clicks until handlers ready */
window.SEMSEO = window.SEMSEO || {};
window.SEMSEO.ENDPOINTS = { analyzeJson: @json($analyzeJsonUrl), analyze: @json($analyzeUrl) };
window.SEMSEO.READY=false; window.SEMSEO.BUSY=false; window.SEMSEO.QUEUE=0;
function SEMSEO_go(){ if(window.SEMSEO.READY && typeof analyze==='function'){ analyze(); } else { window.SEMSEO.QUEUE++; } }
</script>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
      <div>
        <div class="hero-heading">Semantic SEO Master Analyzer</div>
        <div class="hero-sub">Human vs AI • Readability • Entities • PageSpeed</div>
      </div>
    </div>
    <button class="btn btn-print" id="printTop"><i class="fa-solid fa-print"></i> Print</button>
  </header>

  <main class="analyzer">
    <h2 class="section-title">Analyze a URL</h2>
    <p class="section-subtitle">
      Enter any page and get a full colorful report. <span class="legend l-green">Green</span> is great, <span class="legend l-orange">Orange</span> needs attention, <span class="legend l-red">Red</span> is poor.
    </p>

    <!-- URL + actions -->
    <form id="analyzeForm" onsubmit="event.preventDefault(); analyze(); return false;">
      <div class="url-field" id="urlField" style="margin-top:.5rem">
        <i class="fa-solid fa-globe url-icon"></i>
        <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page">
        <button type="button" class="url-mini url-clear" id="clearUrl" title="Clear"><i class="fa-solid fa-xmark"></i></button>
        <button type="button" class="url-mini" id="pasteUrl" title="Paste">Paste</button>
        <span class="url-border" aria-hidden="true"></span>
      </div>

      <div class="analyze-row">
        <label style="display:inline-flex;align-items:center;gap:.45rem">
          <input id="autoApply" type="checkbox" checked style="accent-color:#9b5cff">
          <span>Auto-check items with score ≥ 80</span>
        </label>
        <button id="analyzeBtn" type="button" onclick="SEMSEO_go()" class="btn btn-analyze">
          <i class="fa-solid fa-magnifying-glass"></i> Analyze
        </button>
        <button class="btn btn-print" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> Print</button>
        <button class="btn btn-reset" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> Reset</button>
        <button class="btn btn-export" id="exportChecklist" type="button"><i class="fa-solid fa-file-export"></i> Export</button>
        <button class="btn btn-export" id="importChecklist" type="button"><i class="fa-solid fa-file-import"></i> Import</button>
        <input type="file" id="importFile" accept="application/json" style="display:none">
      </div>

      <!-- progress water -->
      <div class="water-wrap" id="waterWrap">
        <div class="waterbar" id="waterBar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
          <svg class="water-svg" viewBox="0 0 600 200" preserveAspectRatio="none">
            <defs>
              <linearGradient id="waterGrad" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#3de2ff"/><stop offset="100%" stop-color="#9b5cff"/></linearGradient>
              <clipPath id="roundClip"><rect x="1" y="1" width="598" height="198" rx="18" ry="18"/></clipPath>
              <clipPath id="fillClip"><rect id="waterClipRect" x="0" y="200" width="600" height="200"/></clipPath>
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
          <div class="water-pct"><span id="waterPct">0%</span></div>
        </div>
        <div id="analyzeStatus" style="margin-top:.4rem;color:var(--text-dim)" aria-live="polite"></div>
      </div>

      <!-- quick meta chips -->
      <div id="analyzeReport" style="margin-top:.9rem;display:none">
        <div style="display:flex;flex-wrap:wrap;gap:.5rem">
          <span class="chip">HTTP: <b id="rStatus">—</b></span>
          <span class="chip">Title: <b id="rTitleLen">—</b></span>
          <span class="chip">Meta: <b id="rMetaLen">—</b></span>
          <span class="chip">Canonical: <b id="rCanonical">—</b></span>
          <span class="chip">Robots: <b id="rRobots">—</b></span>
          <span class="chip">Viewport: <b id="rViewport">—</b></span>
          <span class="chip">H1/H2/H3: <b id="rHeadings">—</b></span>
          <span class="chip">Internal: <b id="rInternal">—</b></span>
          <span class="chip">Schema: <b id="rSchema">—</b></span>
          <span class="chip">Auto-checked: <b id="rAutoCount">—</b></span>
        </div>
      </div>
    </form>

    <!-- 1) Human vs AI -->
    <section id="detectorPanel" class="detector" style="display:none">
      <div class="det-head">
        <i class="fa-solid fa-wave-square"></i>
        <h4>Ultra Content Detection (Ensemble)</h4>
      </div>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.4rem">
        <span class="chip"><i class="fa-solid fa-user-check"></i> Human: <b id="humanPct">—</b>%</span>
        <span class="chip"><i class="fa-solid fa-microchip"></i> AI: <b id="aiPct">—</b>%</span>
        <span class="chip"><i class="fa-solid fa-shield-halved"></i> Confidence: <b id="detConfidence">—</b>%</span>
      </div>
      <div class="det-grid" id="detGrid"></div>
    </section>

    <!-- 2) Readability Insights -->
    <section id="readPanel" class="detector" style="display:none">
      <div class="det-head">
        <i class="fa-solid fa-book-open-reader"></i>
        <h4>Readability Insights</h4>
      </div>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.4rem">
        <span class="chip"><i class="fa-solid fa-school-flag"></i> Target: <b>Easy to read • Grade ≤ 7</b></span>
        <span class="chip" id="readBadge"><i class="fa-solid fa-gauge-high"></i> Score: <b id="readEase">—</b></span>
        <span class="chip" id="gradeBadge"><i class="fa-solid fa-graduation-cap"></i> Grade: <b id="gradeLevel">—</b></span>
        <span class="chip"><i class="fa-solid fa-hourglass-half"></i> Reading time: <b id="readTime">—</b></span>
      </div>
      <div class="kpi-grid">
        <div class="kpi" id="kpi-sent">
          <div class="kpi-name"><i class="fa-solid fa-paragraph"></i> Avg sentence length</div>
          <div class="kpi-val" id="kpi-sent-val">—</div>
        </div>
        <div class="kpi" id="kpi-words">
          <div class="kpi-name"><i class="fa-solid fa-file-lines"></i> Word count</div>
          <div class="kpi-val" id="kpi-words-val">—</div>
        </div>
        <div class="kpi" id="kpi-complex">
          <div class="kpi-name"><i class="fa-solid fa-brain"></i> Complex words (3+ syll)</div>
          <div class="kpi-val" id="kpi-complex-val">—</div>
        </div>
        <div class="kpi" id="kpi-passive">
          <div class="kpi-name"><i class="fa-solid fa-traffic-light"></i> Passive voice (est.)</div>
          <div class="kpi-val" id="kpi-passive-val">—</div>
        </div>
      </div>
      <div class="group-card" style="margin-top:.6rem">
        <h5 class="group-title"><i class="fa-solid fa-wand-magic-sparkles"></i> Suggestions</h5>
        <ul id="readSuggest" style="margin:.15rem 0 0 .9rem"></ul>
        <div class="group-note">Suggestions are tailored to the current text sample; aim for Grade ≤ 7 for broad audiences.</div>
      </div>
    </section>

    <!-- 3) Entities & Topics -->
    <section id="entityPanel" class="detector" style="display:none">
      <div class="det-head">
        <i class="fa-solid fa-database"></i>
        <h4>Entities & Topics</h4>
      </div>

      <div class="kpi-grid">
        <div class="kpi" id="kpi-entities">
          <div class="kpi-name"><i class="fa-solid fa-hashtag"></i> Entities detected</div>
          <div class="kpi-val" id="kpi-entities-val">—</div>
        </div>
        <div class="kpi" id="kpi-coverage">
          <div class="kpi-name"><i class="fa-solid fa-layer-group"></i> Category coverage</div>
          <div class="kpi-val" id="kpi-coverage-val">—</div>
        </div>
        <div class="kpi" id="kpi-keyphr">
          <div class="kpi-name"><i class="fa-solid fa-key"></i> Notable keyphrases</div>
          <div class="kpi-val" id="kpi-keyphr-val">—</div>
        </div>
        <div class="kpi" id="kpi-uniq">
          <div class="kpi-name"><i class="fa-solid fa-bolt"></i> Variety (unique ratio)</div>
          <div class="kpi-val" id="kpi-uniq-val">—</div>
        </div>
      </div>

      <div class="det-grid" style="margin-top:.6rem">
        <div class="det-item" style="grid-column:span 6">
          <h5 class="group-title"><i class="fa-solid fa-user"></i> People</h5>
          <div class="tag-grid" id="ent-people"></div>
        </div>
        <div class="det-item" style="grid-column:span 6">
          <h5 class="group-title"><i class="fa-solid fa-building"></i> Organizations</h5>
          <div class="tag-grid" id="ent-orgs"></div>
        </div>
        <div class="det-item" style="grid-column:span 6">
          <h5 class="group-title"><i class="fa-solid fa-location-dot"></i> Places</h5>
          <div class="tag-grid" id="ent-places"></div>
        </div>
        <div class="det-item" style="grid-column:span 6">
          <h5 class="group-title"><i class="fa-solid fa-microchip"></i> Technologies / Topics</h5>
          <div class="tag-grid" id="ent-tech"></div>
        </div>
        <div class="det-item" style="grid-column:span 6">
          <h5 class="group-title"><i class="fa-solid fa-robot"></i> Software / APK</h5>
          <div class="tag-grid" id="ent-software"></div>
          <div class="group-note">Looks for terms like APK, app, version codes, OS names, download intent, etc.</div>
        </div>
        <div class="det-item" style="grid-column:span 6">
          <h5 class="group-title"><i class="fa-solid fa-gamepad"></i> Games</h5>
          <div class="tag-grid" id="ent-games"></div>
        </div>
      </div>
    </section>

    <!-- 4) Site Speed & Core Web Vitals -->
    <section id="psiPanel" class="detector" style="display:none">
      <div class="det-head">
        <i class="fa-solid fa-gauge-high"></i>
        <h4>Site Speed & Core Web Vitals</h4>
      </div>
      <div style="display:flex;gap:.5rem;flex-wrap:wrap;margin-bottom:.4rem">
        <span class="chip">Strategy: <b id="psiStrategy">—</b></span>
        <span class="chip" id="perfChip">Performance: <b id="psiPerf">—</b></span>
        <span class="chip">FCP: <b id="psiFcp">—</b></span>
        <span class="chip">LCP: <b id="psiLcp">—</b></span>
        <span class="chip">INP: <b id="psiInp">—</b></span>
        <span class="chip">CLS: <b id="psiCls">—</b></span>
      </div>
      <div class="det-grid" id="psiOpps"></div>
      <div class="group-note" style="margin-top:.35rem">Uses your server-side proxy; API key stays hidden.</div>
    </section>

  </main>
</div>

<!-- ===== JS: analysis + rendering ===== -->
<script>
(function(){
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  /* ---------- helpers ---------- */
  const clamp=(v,a,b)=>v<a?a:(v>b?b:v);
  const setText=(id,val)=>{const el=document.getElementById(id); if(el) el.textContent=val; return el;};
  const tone=(el,v)=>{ if(!el) return; el.classList.remove('good','mid','bad'); const n=+v||0; el.classList.add(n>=80?'good':(n>=60?'mid':'bad')); };
  const chipTone=(el,v)=>{ if(!el) return; el.classList.remove('chip-good','chip-mid','chip-bad'); const n=+v||0; el.classList.add(n>=80?'chip-good':(n>=60?'chip-mid':'chip-bad')); };

  /* ---------- readability core ---------- */
  function countSyllables(word){
    const w=(word||'').toLowerCase().replace(/[^a-z]/g,''); if(!w) return 0;
    let m=(w.match(/[aeiouy]+/g)||[]).length; if(/(ed|es)$/.test(w)) m--; if(/^y/.test(w)) m--; return Math.max(1,m);
  }
  function prep(text){
    text=(text||'').replace(/\u00A0/g,' ').replace(/\s+/g,' ').trim();
    const sentSplit = text.split(/(?<=[.!?])\s+|\n+(?=\S)/g).filter(Boolean);
    const words = (text.match(/[A-Za-z\u00C0-\u024f']+/g)||[]);
    const wc=words.length||1, sc=sentSplit.length||1;
    let syll=0, complex=0;
    for(const w of words){ const s=countSyllables(w); syll+=s; if(s>=3) complex++; }
    const fre = clamp(206.835 - 1.015*(wc/sc) - 84.6*(syll/wc), -20, 120);
    const grade = clamp(0.39*(wc/sc)+11.8*(syll/wc)-15.59, 0, 18);
    const passive = (text.match(/\b(?:is|are|was|were|be|been|being|am)\s+\w+(?:ed|en)\b/gi)||[]).length;
    const timeMin = wc/200; // 200 wpm
    return {
      text, wc, sc, syll, complex, passiveHits:passive,
      fre:Math.round(fre), grade:Math.round(grade*10)/10,
      avgSent:Math.round((wc/sc)*10)/10,
      complexPct: Math.round((complex*100)/wc),
      passivePct: Math.round((passive*100)/sc),
      readTime: timeMin<1 ? Math.round(timeMin*60)+'s' : (Math.round(timeMin*10)/10)+'m'
    };
  }
  function renderReadability(r){
    const panel=document.getElementById('readPanel'); if(!panel) return;
    panel.style.display='block';
    setText('readEase', r.fre);
    setText('gradeLevel', r.grade);
    setText('readTime', r.readTime);
    setText('kpi-sent-val', r.avgSent+' words');
    setText('kpi-words-val', r.wc);
    setText('kpi-complex-val', r.complexPct+'%');
    setText('kpi-passive-val', r.passivePct+'%');

    chipTone(document.getElementById('readBadge'), r.fre);
    const gradeTone = r.grade<=7?85:(r.grade<=10?70:40);
    chipTone(document.getElementById('gradeBadge'), gradeTone);

    const setK = (id,val)=>{const k=document.getElementById(id); if(k){ k.classList.remove('good','mid','bad'); k.classList.add(val);} };
    setK('kpi-sent', r.avgSent<=18?'good':(r.avgSent<=25?'mid':'bad'));
    setK('kpi-complex', r.complexPct<=12?'good':(r.complexPct<=20?'mid':'bad'));
    setK('kpi-passive', r.passivePct<=8?'good':(r.passivePct<=15?'mid':'bad'));

    const s = [];
    if (r.grade>7) s.push('Use shorter sentences and simpler words to reach Grade ≤ 7.');
    if (r.avgSent>20) s.push('Split long sentences into 2–3 shorter ones.');
    if (r.complexPct>15) s.push('Replace complex words (3+ syllables) with simpler alternatives.');
    if (r.passivePct>10) s.push('Prefer active voice (e.g., “We shipped the update” vs “The update was shipped”).');
    if (r.wc<400) s.push('Add more substance: explain “why” and “how” with examples.');
    if (s.length===0) s.push('Nice! Your text is easy to understand. Maintain clear structure and concrete examples.');
    const ul=document.getElementById('readSuggest'); ul.innerHTML=''; s.forEach(t=>{ const li=document.createElement('li'); li.textContent=t; ul.appendChild(li); });
  }

  /* ---------- entities extraction ---------- */
  const stop = new Set(['The','A','An','Of','And','For','On','In','To','At','By','From','With','As','Or','vs','Vs']);
  function dedupeTop(arr, n=30){
    const map=new Map(); for(const a of arr){ const k=a.trim(); if(!k || k.length<2) continue; map.set(k,(map.get(k)||0)+1); }
    return Array.from(map.entries()).sort((a,b)=>b[1]-a[1]).slice(0,n).map(x=>x[0]);
  }
  function extractEntities(text){
    const t=(text||'').replace(/\s+/g,' ').trim();
    const words=t.split(/\s+/);
    // keyphrases (simple): frequent bigrams/trigrams with capitalization or nouns
    const keyphr=[], bigrams={};
    for(let i=0;i<words.length-1;i++){
      const bg=(words[i]+' '+words[i+1]).replace(/[^A-Za-z0-9\-\+\. ]/g,'').trim();
      if(bg.split(' ').every(w=>w.length>2)) bigrams[bg]=(bigrams[bg]||0)+1;
    }
    Object.entries(bigrams).forEach(([k,v])=>{ if(v>=2) keyphr.push(k); });

    // proper-noun like entities
    const cand = (t.match(/\b([A-Z][a-z]+(?:\s+[A-Z][a-z0-9\.\-]+){0,3})\b/g)||[])
      .filter(x=>!stop.has(x.split(' ')[0]));
    // categories
    const people=[], orgs=[], places=[], tech=[], software=[], games=[];
    cand.forEach(c=>{
      if(/\b(Inc|LLC|Ltd|Corp|Company|University|College|Institute|Foundation|Labs)\b/.test(c)) orgs.push(c);
      else if(/\b(City|County|State|Province|Nation|Kingdom|Republic)\b/.test(c)) places.push(c);
      else if(/\b(SEO|Laravel|PageSpeed|Core Web Vitals|JavaScript|PHP|MySQL|Android|iOS|Windows|Google|Microsoft|Amazon|OpenAI)\b/.test(c)) tech.push(c);
      else if(/\b([A-Z][a-z]+ [A-Z][a-z]+)\b/.test(c)) people.push(c);
      else tech.push(c);
    });
    // software/apk/games via keywords
    const softRe = /\b(apk|\.apk|android app|ios app|software|installer|setup|v?\d+\.\d+(\.\d+)?|download|windows|macos|linux)\b/i;
    const gameRe = /\b(game|steam|xbox|playstation|nintendo|rpg|mmo|battle royale|mobile game)\b/i;
    const lines = t.split(/(?<=[.!?])\s+/);
    lines.forEach(L=>{
      if(softRe.test(L)){
        const m=(L.match(/\b[A-Z][A-Za-z0-9\-\_]{2,}(?:\s+[A-Z0-9][A-Za-z0-9\-\_]{1,}){0,2}\b/g)||[]);
        m.forEach(x=>software.push(x));
      }
      if(gameRe.test(L)){
        const m=(L.match(/\b[A-Z][A-Za-z0-9\-\_]{2,}(?:\s+[A-Z0-9][A-Za-z0-9\-\_]{1,}){0,2}\b/g)||[]);
        m.forEach(x=>games.push(x));
      }
    });

    return {
      people: dedupeTop(people,20),
      orgs: dedupeTop(orgs,20),
      places: dedupeTop(places,20),
      tech: dedupeTop(tech,25),
      software: dedupeTop(software,20),
      games: dedupeTop(games,20),
      keyphr: dedupeTop(keyphr,12)
    };
  }
  function renderEntities(e){
    const panel=document.getElementById('entityPanel'); if(!panel) return;
    panel.style.display='block';
    const fill=(id,arr)=>{const el=document.getElementById(id); if(!el) return; el.innerHTML=''; (arr&&arr.length?arr:['—']).forEach(t=>{ const s=document.createElement('span'); s.className='tag soft'; s.innerHTML='<i class="fa-solid fa-tag"></i> '+t; el.appendChild(s); });};
    fill('ent-people', e.people);
    fill('ent-orgs', e.orgs);
    fill('ent-places', e.places);
    fill('ent-tech', e.tech);
    fill('ent-software', e.software);
    fill('ent-games', e.games);
    setText('kpi-entities-val', (e.people.length+e.orgs.length+e.places.length+e.tech.length+e.software.length+e.games.length));
    const covered = ['people','orgs','places','tech','software','games'].filter(k=>e[k].length>0).length;
    setText('kpi-coverage-val', covered+'/6');
    setText('kpi-keyphr-val', e.keyphr.slice(0,5).join(' • ') || '—');
    const uniq = (new Set([].concat(e.people,e.orgs,e.places,e.tech,e.software,e.games))).size;
    const total = (e.people.length+e.orgs.length+e.places.length+e.tech.length+e.software.length+e.games.length) || 1;
    setText('kpi-uniq-val', Math.round((uniq*100)/total)+'%');
  }

  /* ---------- backend + psi helpers (unchanged) ---------- */
  function normalizeUrl(u){ if(!u) return ''; u=u.trim(); if(/^https?:\/\//i.test(u)){ try{ new URL(u); return u; }catch(e){ return ''; } } const guess='https://'+u.replace(/^\/+/,''); try{ new URL(guess); return guess; }catch(e){ return ''; } }
  async function fetchBackend(url){
    let data=null, ok=false, status=0, text='';
    const qs=new URLSearchParams({url}).toString();
    try{ const r1=await fetch((window.SEMSEO.ENDPOINTS.analyzeJson||'analyze-json')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); status=r1.status; text=await r1.text(); try{ data=JSON.parse(text);}catch{} if(r1.ok&&data) ok=true; }catch{}
    if(!ok){ try{ const r2=await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze'),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body:JSON.stringify({url,_token:CSRF})}); status=r2.status; text=await r2.text(); try{ data=JSON.parse(text);}catch{} if(r2.ok&&data) ok=true; }catch{} }
    if(!ok){ try{ const r3=await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); status=r3.status; text=await r3.text(); try{ data=JSON.parse(text);}catch{} if(r3.ok&&data) ok=true; }catch{} }
    return {ok,data,status};
  }
  async function fetchRawHtml(url){ try{ const r=await fetch('https://api.allorigins.win/raw?url='+encodeURIComponent(url),{cache:'no-store'}); if(r.ok){ const html=await r.text(); if(html && html.length>200) return html; } }catch{} return ''; }
  async function fetchReadableText(url){
    try{ const a=await fetch('https://r.jina.ai/http/'+url.replace(/^https?:\/\//,'')); if(a.ok){const t=await a.text(); if(t && t.length>200) return t;} }catch{}
    try{ const b=await fetch('https://r.jina.ai/'+url); if(b.ok){const t=await b.text(); if(t && t.length>200) return t;} }catch{}
    return '';
  }
  function extractMetaFromHtml(html, base){
    try{
      const d=(new DOMParser()).parseFromString(html,'text/html');
      const q=(s,a)=>{const el=d.querySelector(s); return el?(a?el.getAttribute(a)||'':(el.textContent||'')) : '';};
      const title=(q('title')||'').trim(), metaDesc=(q('meta[name="description"]','content')||'').trim();
      const canonical=(q('link[rel="canonical"]','href')||'').trim()||base, robots=(q('meta[name="robots"]','content')||'').trim()||'n/a';
      const viewport=(q('meta[name="viewport"]','content')||'').trim()||'n/a';
      const h1=d.querySelectorAll('h1').length, h2=d.querySelectorAll('h2').length, h3=d.querySelectorAll('h3').length;
      let origin=''; try{ origin=new URL(base).origin; }catch{}
      let internal=0; d.querySelectorAll('a[href]').forEach(a=>{ try{ const u=new URL(a.getAttribute('href'), base); if(!origin || u.origin===origin) internal++; }catch{} });
      const schema = !!(d.querySelector('script[type="application/ld+json"]') || d.querySelector('[itemscope],[itemtype*="schema.org"]'));
      let main=d.querySelector('article,main,[role="main"]'); let sample=main? (main.textContent||''): '';
      if(!sample){ sample=[].slice.call(d.querySelectorAll('p')).slice(0,12).map(p=>p.textContent).join('\n\n'); }
      sample=(sample||'').replace(/\s{2,}/g,' ').trim();
      return { titleLen:title?title.length:null, metaLen:metaDesc?metaDesc.length:null, canonical, robots, viewport, headings:(h1+'/'+h2+'/'+h3), internalLinks:internal, schema: schema?'yes':'no', sampleText: sample };
    }catch{ return {}; }
  }
  function mergeMeta(into, add){
    if(!into) into={}; const keys=['titleLen','metaLen','canonical','robots','viewport','headings','internalLinks','schema','sampleText'];
    keys.forEach(k=>{ if((into[k]===undefined||into[k]===null||into[k]==='—'||into[k]==='') && add && add[k]!==undefined && add[k]!==null){ into[k]=add[k]; } });
    return into;
  }

  /* ---------- Human/AI render (short) ---------- */
  function renderDetectors(res){
    const grid=document.getElementById('detGrid'); const panel=document.getElementById('detectorPanel');
    if(!panel||!grid) return; panel.style.display='block'; grid.innerHTML='';
    (res.detectors||[{key:'stylometry',label:'Stylometry',ai:res.aiPct||0}]).forEach(d=>{
      const id='det-'+d.key, wrap=document.createElement('div'); wrap.className='det-item';
      wrap.innerHTML='<div class="det-row"><div class="det-label">'+d.label+'</div><div class="det-score">'+(d.ai||0)+'</div></div><div class="det-bar"><div class="det-fill" style="width:'+clamp(d.ai||0,0,100)+'%"></div></div>';
      grid.appendChild(wrap);
    });
  }
  function applyDetection(humanPct, aiPct, confidence, breakdown){
    const panel=document.getElementById('detectorPanel'); if(!panel) return;
    panel.style.display='block';
    setText('humanPct', isFinite(humanPct)?Math.round(humanPct):'—');
    setText('aiPct', isFinite(aiPct)?Math.round(aiPct):'—');
    setText('detConfidence', isFinite(confidence)?Math.round(confidence):'—');
    if(breakdown && breakdown.detectors) renderDetectors(breakdown);
  }

  /* ---------- PSI (unchanged render) ---------- */
  function chipScore(el, s){ if(!el) return; el.classList.remove('chip-good','chip-mid','chip-bad'); const n=isFinite(s)?Math.round(s*100):0; el.classList.add(n>=80?'chip-good':(n>=60?'chip-mid':'chip-bad')); }
  function renderPSI(data,strategy){
    const panel=document.getElementById('psiPanel'); if(!panel) return; panel.style.display='block';
    setText('psiStrategy', strategy||'—');
    const lr=data.lighthouseResult||{}, audits=lr.audits||{}, cats=lr.categories||{};
    setText('psiPerf', isFinite(cats.performance?.score)?Math.round(cats.performance.score*100):'—');
    chipScore(document.getElementById('perfChip'), cats.performance?.score);
    setText('psiFcp', audits['first-contentful-paint']?.displayValue||'—');
    setText('psiLcp', audits['largest-contentful-paint']?.displayValue||'—');
    setText('psiInp', audits['interactive']?.displayValue || audits['experimental-interaction-to-next-paint']?.displayValue || '—');
    setText('psiCls', audits['cumulative-layout-shift']?.displayValue || (audits['cumulative-layout-shift']?.numericValue?.toFixed?.(3)) || '—');
    const grid=document.getElementById('psiOpps'); grid.innerHTML='';
    Object.keys(audits).filter(k=>audits[k]?.details?.type==='opportunity').slice(0,6).forEach(k=>{
      const a=audits[k]; const card=document.createElement('div'); card.className='det-item';
      card.innerHTML='<div class="det-row"><div class="det-label">'+(a.title||k)+'</div><div class="det-score">'+(a.details?.overallSavingsMs?Math.round(a.details.overallSavingsMs)+' ms':'')+'</div></div><div class="group-note">'+(a.description||'')+'</div>';
      grid.appendChild(card);
    });
  }
  async function runPSI(pageUrl){
    try{
      const url=normalizeUrl(pageUrl || document.getElementById('analyzeUrl')?.value||''); if(!url) return;
      const strategy = (window.innerWidth<768)?'mobile':'desktop';
      setText('analyzeStatus','Running PageSpeed Insights…');
      const r=await fetch('/psi-proxy?u='+encodeURIComponent(url)+'&strategy='+strategy,{headers:{'Accept':'application/json'},cache:'no-store'});
      const data=await r.json(); if(!r.ok) throw new Error((data && data.error) ? data.error : 'PSI error');
      renderPSI(data,strategy);
      setText('analyzeStatus','Analysis complete (incl. PSI).');
    }catch(e){ setText('analyzeStatus', e.message||'PSI error'); }
  }

  /* ---------- main analyze() ---------- */
  async function analyze(){
    if(window.SEMSEO.BUSY) return; window.SEMSEO.BUSY=true;
    const input=document.getElementById('analyzeUrl'); const url=normalizeUrl(input?.value||''); if(!url){ input?.focus(); window.SEMSEO.BUSY=false; return; }

    document.getElementById('waterWrap').style.display='block'; setText('analyzeStatus','Fetching & analyzing…');
    document.getElementById('detectorPanel').style.display='none';
    document.getElementById('readPanel').style.display='none';
    document.getElementById('entityPanel').style.display='none';
    document.getElementById('psiPanel').style.display='none';
    document.getElementById('analyzeReport').style.display='none';

    // backend -> html -> reader
    let {data}=await fetchBackend(url); data=data||{};
    let sample='';
    if(data.textSample||data.extractedText||data.sample) sample = (data.textSample||data.extractedText||data.sample||'');
    try{
      const raw=await fetchRawHtml(url);
      if(raw){
        data = mergeMeta(data, extractMetaFromHtml(raw, url));
        if(!sample && data.sampleText) sample = data.sampleText;
      }
    }catch{}
    if(!sample || sample.length<200){
      try{ const rd = await fetchReadableText(url); if(rd && rd.length>200) sample = rd; }catch{}
    }

    // meta chips
    document.getElementById('analyzeReport').style.display='block';
    setText('rStatus', data.httpStatus||'—'); setText('rTitleLen', data.titleLen??'—'); setText('rMetaLen', data.metaLen??'—');
    setText('rCanonical', data.canonical||'—'); setText('rRobots', data.robots||'—'); setText('rViewport', data.viewport||'—');
    setText('rHeadings', data.headings||'—'); setText('rInternal', data.internalLinks??'—'); setText('rSchema', data.schema||'—');

    // Human vs AI (local fallback)
    const sObj = prep(sample||'');
    const ai = clamp(10 + (sObj.avgSent<18?0:10) + (sObj.complexPct>15?15:0) + (sObj.passivePct>10?10:0), 0, 100);
    const human = clamp(100-ai, 0, 100);
    applyDetection(data.humanPct??human, data.aiPct??ai, data.confidence??(50+Math.min(45, Math.log((sObj.wc||1)+1)*7)), {detectors:[{key:'stylometry',label:'Stylometry',ai:ai}]});

    // Readability
    renderReadability(sObj);

    // Entities
    renderEntities(extractEntities(sample||''));

    // PSI auto-run
    await runPSI(url);

    setText('analyzeStatus','Analysis complete');
    document.getElementById('waterWrap').style.display='none';
    window.SEMSEO.BUSY=false;
  }
  window.analyze = analyze;

  /* ---------- boot ---------- */
  document.addEventListener('DOMContentLoaded', ()=>{
    const btn=document.getElementById('analyzeBtn'); if(btn) btn.addEventListener('click', e=>{e.preventDefault(); analyze();});
    const input=document.getElementById('analyzeUrl'); if(input) input.addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); analyze(); } });
    const clr=document.getElementById('clearUrl'); if(clr && input) clr.onclick=()=>{ input.value=''; input.focus(); };
    const pst=document.getElementById('pasteUrl'); if(pst && input && navigator.clipboard) pst.onclick=async()=>{ try{ const t=await navigator.clipboard.readText(); if(t) input.value=t.trim(); }catch{} };
    window.SEMSEO.READY=true; if(window.SEMSEO.QUEUE>0){ window.SEMSEO.QUEUE=0; analyze(); }
  });
})();
</script>

</body>
</html>
