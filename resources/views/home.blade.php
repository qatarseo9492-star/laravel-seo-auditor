{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Semantic SEO Master • Ultra Tech Global</title>

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

/* Canvas decor */
#linesCanvas, #linesCanvas2, #brainCanvas, #smokeFX { position:fixed; inset:0; z-index:0; pointer-events:none; }
#brainCanvas{opacity:.10}
#smokeFX{ opacity:1; filter:saturate(115%) contrast(105%); }

/* Layout */
.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 22px;border-bottom:1px solid var(--line);backdrop-filter:saturate(140%) blur(10px);background:rgba(15,16,34,.35)}
.brand{display:flex;align-items:center;gap:1rem}
.brand-badge{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(155,92,255,.3),rgba(255,32,69,.25));border:1px solid rgba(255,255,255,.08); color:#ffd1dc}
.hero-heading{font-size:4.2rem;font-weight:1000;line-height:1.02;margin:.1rem 0;letter-spacing:.8px;background:linear-gradient(90deg,#b892ff,#ff2045 55%,#ff8a5b 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;text-shadow:0 0 28px rgba(155,92,255,.25)}

/* Language dock */
.lang-dock{position:fixed;left:18px;top:50%;transform:translateY(-50%);z-index:70;display:flex;flex-direction:column;gap:.6rem}
.lang-btn{width:48px;height:48px;border-radius:12px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);color:#fff;display:grid;place-items:center;cursor:pointer;backdrop-filter:blur(6px)}
.lang-btn:hover{background:rgba(255,255,255,.1)}
.lang-panel{position:fixed;left:74px;top:50%;transform:translateY(-50%);z-index:70;display:none}
.lang-card{background:var(--panel-2);border:1px solid rgba(255,255,255,.12);border-radius:16px;box-shadow:var(--shadow);padding:10px 12px;min-width:240px}
.lang-item{padding:.45rem .55rem;border-radius:10px;display:flex;align-items:center;gap:.5rem;cursor:pointer}
.lang-item:hover{background:rgba(255,255,255,.06)}
.lang-flag{width:18px;height:14px;border-radius:2px;background:#888}

/* Buttons */
.btn{--pad:.75rem 1.05rem;display:inline-flex;align-items:center;gap:.5rem;padding:var(--pad);border-radius:14px;border:1px solid transparent;cursor:pointer;font-weight:800;letter-spacing:.2px;transition:.2s}
.btn-neon{background:linear-gradient(135deg,#3de2ff,#9b5cff);box-shadow:0 8px 30px rgba(61,226,255,.25);color:#001018}
.btn-neon:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(61,226,255,.35)}
.btn-ghost{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.16);color:#fff}
.btn-ghost:hover{background:rgba(255,255,255,.08);transform:translateY(-2px)}
.btn-danger{background:linear-gradient(135deg,#ff2045,#ff7a59);color:#fff;box-shadow:0 8px 30px rgba(255,32,69,.25)}
.btn-danger:hover{transform:translateY(-2px);box-shadow:0 12px 40px rgba(255,32,69,.35)}

/* Analyzer panel */
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem}
.section-subtitle{margin:0;color:var(--text-dim)}

/* Score wheel + chips */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-wheel{width:100%;height:auto;transform:rotate(-90deg)}
.score-wheel circle{fill:none;stroke-width:14;stroke-linecap:round}
.score-wheel .bg{stroke:rgba(255,255,255,.12)}
.score-wheel .progress{
  stroke:url(#grad);
  stroke-dasharray:339; stroke-dashoffset:339;
  transition:stroke-dashoffset .6s ease,stroke .3s ease,filter .3s ease;
  filter:drop-shadow(0 0 10px rgba(155,92,255,.35))
}
.score-text{font-size:clamp(2.2rem, 4.2vw, 3.1rem);font-weight:1000;fill:#fff;transform:rotate(90deg);text-shadow:0 0 18px rgba(255,32,69,.25)}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28);display:inline-flex;align-items:center;gap:.5rem}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)} .l-orange{background:rgba(245,158,11,.18)} .l-green{background:rgba(34,197,94,.18)}

/* Traffic-light chip states */
.chip-good{background:rgba(34,197,94,.18)!important;border-color:rgba(34,197,94,.45)!important}
.chip-mid{background:rgba(245,158,11,.18)!important;border-color:rgba(245,158,11,.45)!important}
.chip-bad{background:rgba(239,68,68,.18)!important;border-color:rgba(239,68,68,.5)!important}

/* Icon tints */
.ico{width:1.1em;text-align:center}
.ico-green{color:var(--good)}
.ico-orange{color:var(--warn)}
.ico-red{color:var(--bad)}
.ico-cyan{color:var(--accent)}
.ico-purple{color:#9b5cff}

/* URL input + animated label */
.analyze-form input[type="url"]{
  width:100%; padding:1rem 1.2rem; border-radius:16px;
  border:1px solid #1b1b35; background:#0b0d21; color:var(--text); transition:.25s;
}
.analyze-form input[type="url"]:focus{ outline:none; border-color:#5942ff; box-shadow:0 0 0 6px rgba(155,92,255,.15); }
.url-label{display:inline-block;font-weight:900;margin-bottom:.35rem;position:relative}
.url-label.animating{
  background:linear-gradient(90deg,#fff,#9b5cff,#3de2ff,#fff);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  animation:shine 1.2s linear infinite;
}
.url-label.animating::after{
  content:""; position:absolute; left:0; right:0; bottom:-4px; height:3px;
  background:linear-gradient(90deg,#3de2ff,#9b5cff,#ff2045,#3de2ff);
  filter:blur(.3px); border-radius:999px; animation:underlineWave 1.6s ease-in-out infinite;
}
@keyframes shine{0%{background-position:0%}100%{background-position:200%}}
@keyframes underlineWave{0%,100%{transform:translateX(0)}50%{transform:translateX(8px)}}

.analyze-row{display:grid;grid-template-columns:1fr auto auto auto auto;gap:.6rem;align-items:center;margin-top:.5rem}

/* NEW: Water progress bar */
.water-wrap{margin-top:.8rem;display:none}
.waterbar{
  position:relative; height:64px; border-radius:18px; overflow:hidden;
  background:linear-gradient(180deg,#0b0d21,#0b0d21); border:1px solid rgba(255,255,255,.1);
}
.water-svg{position:absolute; inset:0; width:100%; height:100%}
.water-mask-rect{transition:all .25s ease-out}
.water-overlay{position:absolute; inset:0; pointer-events:none; background:
  radial-gradient(120px 60px at 20% -20%, rgba(255,255,255,.18), transparent 60%),
  linear-gradient(0deg, rgba(255,255,255,.05), transparent 40%, transparent 60%, rgba(255,255,255,.06));
  mix-blend-mode:screen;
}
.water-pct{
  position:absolute; inset:0; display:grid; place-items:center; font-weight:1000;
  font-size:1.05rem; text-shadow:0 1px 0 rgba(0,0,0,.45); letter-spacing:.4px;
}
.wave1{animation:waveX 7s linear infinite}
.wave2{animation:waveX 10s linear infinite reverse; opacity:.7}
@keyframes waveX{0%{transform:translateX(0)}100%{transform:translateX(-600px)}}

/* Progress (checklist completion) */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
.progress-fill{height:100%;background:linear-gradient(135deg,#9b5cff,#ff2045);width:0%;transition:width .35s ease}
.progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}

/* Category grid (unchanged) */
.analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
.category-card{position:relative;grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px;box-shadow:var(--shadow);overflow:hidden; isolation:isolate;}
.category-card::before{content:"";position:absolute;inset:-2px;border-radius:18px;padding:2px;background:linear-gradient(120deg,rgba(61,226,255,.4),rgba(155,92,255,.4),rgba(255,32,69,.4));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;animation:borderGlow 6s linear infinite; pointer-events:none; z-index:0;}
.category-card > *{position:relative; z-index:1;}
.checklist-item label { cursor:pointer; display:inline-flex; align-items:center; gap:.55rem; }
.checklist-item input[type="checkbox"], .improve-btn { pointer-events:auto; position:relative; z-index:2; }
@keyframes borderGlow{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(360deg)}}
.category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
.category-icon{width:48px;height:48px;border-radius:14px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3de2ff33,#9b5cff33);color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,.18)}
.category-title{margin:0;font-size:1.08rem;background:linear-gradient(90deg,#3de2ff,#9b5cff,#ff2045);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900}
.category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}
.checklist{list-style:none;margin:10px 0 0;padding:0}
.checklist-item{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.65rem .7rem;border-radius:14px;border:1px solid rgba(255,255,255,.08);background:linear-gradient(180deg,rgba(255,255,255,.03),rgba(255,255,255,.02))}
.checklist-item + .checklist-item{margin-top:.28rem}
.checklist-item:hover{transform:translateY(-2px);background:rgba(255,255,255,.05);box-shadow:0 8px 30px rgba(0,0,0,.25)}
.checklist-item input[type="checkbox"]{width:18px;height:18px;margin:.1rem .55rem 0 0;accent-color:var(--primary)}
.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22); border-color:rgba(22,193,114,.45)}
.score-mid{ background:rgba(245,158,11,.22); border-color:rgba(245,158,11,.45)}
.score-bad{ background:rgba(239,68,68,.24); border-color:rgba(239,68,68,.5)}
.improve-btn{padding:.35rem .7rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);font-weight:900;cursor:pointer}
.improve-btn:hover{background:rgba(255,255,255,.1)}

/* Footer + back to top */
footer.site{ margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)}
.footer-brand{display:flex;align-items:center;gap:.6rem}
.footer-brand .dot{width:8px;height:8px;border-radius:50%;background:linear-gradient(135deg,#3de2ff,#9b5cff)}
.footer-links a{color:var(--text-dim);margin-left:.9rem}
.footer-links a:hover{color:#fff;text-decoration:underline}
#backTop{position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none}
#backTop:hover{background:rgba(255,255,255,.12)}

@media (max-width:992px){
  .category-card{grid-column:span 12}
  .hero-heading{font-size:2.7rem}
  .score-container{width:190px}
  footer.site{flex-direction:column;align-items:flex-start}
}
@media print{#linesCanvas,#linesCanvas2,#brainCanvas,#smokeFX,.modal-backdrop,.modal,header.site,#backTop,.lang-dock,.lang-panel{display:none!important}}
/* Modal */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.5);display:none;z-index:95}
.modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:100}
.modal-card{width:min(980px,95vw);max-height:85vh;overflow:auto;background:var(--panel-2);border:1px solid rgba(255,255,255,.14);border-radius:18px;box-shadow:var(--shadow);padding:16px}
.modal-header{display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.12);padding-bottom:8px;margin-bottom:8px}
.modal-title{margin:0;font-weight:900}
.modal-close{background:transparent;border:1px solid rgba(255,255,255,.18);border-radius:10px;color:#fff;padding:.35rem .6rem;cursor:pointer}

.tabs{display:flex;gap:.5rem;border-bottom:1px solid rgba(255,255,255,.12);padding-bottom:.3rem;margin-bottom:.5rem}
.tab{padding:.4rem .7rem;border:1px solid rgba(255,255,255,.14);border-bottom:none;border-radius:12px 12px 0 0;background:rgba(255,255,255,.05);cursor:pointer;font-weight:900}
.tab.active{background:linear-gradient(135deg,#3de2ff33,#9b5cff33)}
.tabpanes > div{display:none;padding:.6rem .2rem}
.tabpanes > div.active{display:block}
.pre{white-space:pre-wrap;background:#0b0d21;border:1px solid rgba(255,255,255,.08);border-radius:12px;padding:10px}
</style>
</head>
<body>
<canvas id="brainCanvas" aria-hidden="true"></canvas>
<canvas id="linesCanvas" aria-hidden="true"></canvas>
<canvas id="linesCanvas2" aria-hidden="true"></canvas>
<canvas id="smokeFX" aria-hidden="true"></canvas>

<!-- gradients for score wheel -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#9b5cff"/><stop offset="100%" stop-color="#ff2045"/></linearGradient>
    <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/></linearGradient>
    <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/></linearGradient>
    <linearGradient id="gradBad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/></linearGradient>
  </defs>
</svg>

<!-- Language Dock -->
<div class="lang-dock">
  <button class="lang-btn" id="langOpen" title="Language"><i class="fa-solid fa-globe"></i></button>
</div>
<div class="lang-panel" id="langPanel"><div class="lang-card" id="langCard"></div></div>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge" aria-hidden="true"><i class="fa-solid fa-brain"></i></div>
      <div><div class="hero-heading" data-i="title">Semantic SEO Master Analyzer</div></div>
    </div>
    <div style="display:flex;gap:.5rem">
      <button class="btn btn-ghost" id="printTop"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
    </div>
  </header>

  <main class="analyzer" id="analyzer" role="main" aria-label="Semantic SEO Analyzer">
    <h2 class="section-title" data-i="analyze_title">Analyze a URL</h2>
    <p class="section-subtitle" data-i="legend_line">
      The wheel fills with your overall score. <span class="legend l-green">Green ≥ 80</span> <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Red &lt; 60</span>
    </p>

    <div class="score-area">
      <div class="score-container">
        <svg class="score-wheel" viewBox="0 0 120 120" aria-label="Overall score">
          <circle class="bg" cx="60" cy="60" r="54"/>
          <circle class="progress" cx="60" cy="60" r="54" style="transform: rotate(-90deg); transform-origin: 50% 50%;"/>
          <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="score-text" id="overallScore" aria-live="polite">0%</text>
        </svg>
      </div>

      <div style="display:flex;flex-direction:column;gap:.5rem">
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <span class="chip" id="overallChip"><i class="fa-solid fa-gauge-high ico"></i> <span data-i="overall">Overall</span>: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="contentScoreChip"><i class="fa-solid fa-file-lines ico"></i> Content: <b id="contentScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge" title="AI/Human detection summary"><i class="fa-solid fa-user-check ico ico-green"></i> Writer: <b>—</b></span>
          <button id="viewAIText" class="btn btn-neon" style="--pad:.5rem .8rem"><i class="fa-solid fa-robot"></i> Evidence</button>
        </div>
      </div>
    </div>

    <div class="analyze-box" style="margin-top:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px">
      <form id="analyzeForm" class="analyze-form" onsubmit="return false;" aria-label="Analyze form">
        <label id="pageUrlLabel" for="analyzeUrl" class="url-label" data-i="page_url">Page URL</label>
        <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" aria-describedby="analyzeStatus" />
        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> <span data-i="auto_check">Auto-apply checkmarks (≥ 70)</span>
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-danger"><i class="fa-solid fa-magnifying-glass"></i> <span data-i="analyze">Analyze</span></button>
          <button class="btn btn-neon" id="printChecklist"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
          <button class="btn btn-ghost" id="resetChecklist"><i class="fa-solid fa-rotate"></i> <span data-i="reset">Reset</span></button>
          <button class="btn btn-ghost" id="exportChecklist" title="Export checklist JSON"><i class="fa-solid fa-file-export"></i> Export</button>
          <button class="btn btn-ghost" id="importChecklist" title="Import checklist JSON"><i class="fa-solid fa-file-import"></i> Import</button>
          <input type="file" id="importFile" accept="application/json" style="display:none">
        </div>

        <!-- NEW: Water progress (hidden until Analyze starts) -->
        <div class="water-wrap" id="waterWrap" aria-hidden="true">
          <div class="waterbar" id="waterBar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
            <svg class="water-svg" viewBox="0 0 600 200" preserveAspectRatio="none">
              <defs>
                <linearGradient id="waterGrad" x1="0" y1="0" x2="1" y2="1">
                  <stop offset="0%" stop-color="#3de2ff"/><stop offset="100%" stop-color="#9b5cff"/>
                </linearGradient>
                <clipPath id="roundClip"><rect x="1" y="1" width="598" height="198" rx="18" ry="18"/></clipPath>
                <clipPath id="fillClip">
                  <!-- y of this rect is raised/lowered by JS -->
                  <rect id="waterClipRect" class="water-mask-rect" x="0" y="200" width="600" height="200"/>
                </clipPath>
                <path id="wave" d="M0 120
                  Q 50 90 100 120 T 200 120 T 300 120 T 400 120 T 500 120 T 600 120
                  V 220 H 0 Z"/>
              </defs>

              <!-- Base rounded shape -->
              <g clip-path="url(#roundClip)">
                <rect x="0" y="0" width="600" height="200" fill="#0b0d21"/>
                <!-- Water fill (masked by height) -->
                <g clip-path="url(#fillClip)">
                  <!-- Two tiled waves moving horizontally -->
                  <g class="wave1" fill="url(#waterGrad)" opacity=".95">
                    <use href="#wave" x="0"/><use href="#wave" x="600"/>
                  </g>
                  <g class="wave2" fill="url(#waterGrad)" opacity=".65">
                    <use href="#wave" x="0" y="8"/><use href="#wave" x="600" y="8"/>
                  </g>
                </g>
                <!-- subtle glass overlay -->
                <rect x="0" y="0" width="600" height="200" fill="transparent"/>
              </g>
            </svg>
            <div class="water-overlay"></div>
            <div class="water-pct"><span id="waterPct">0%</span></div>
          </div>
          <div id="analyzeStatus" style="margin-top:.4rem;color:var(--text-dim)" aria-live="polite"></div>
        </div>
        <!-- /water -->
      </form>
    </div>

    <!-- Progress (checklist completion) -->
    <div class="progress-wrap">
      <div class="progress-bar"><div class="progress-fill" id="progressBar"></div></div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    <!-- Categories / checklist (unchanged content) -->
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
        <article class="category-card" style="background-image:{{ $c[4] }}; background-blend-mode: lighten;">
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

<!-- Modal (unchanged) -->
<div class="modal-backdrop" id="modalBackdrop" aria-hidden="true"></div>
<div class="modal" id="tipModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle">Improve</h3>
      <button class="modal-close" id="modalClose" aria-label="Close dialog"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="tabs" role="tablist">
      <button class="tab active" data-tab="tipsTab" role="tab"><i class="fa-solid fa-lightbulb"></i> Tips</button>
      <button class="tab" data-tab="examplesTab" role="tab"><i class="fa-brands fa-google"></i> Examples (Google)</button>
      <button class="tab" data-tab="humanTab" role="tab"><i class="fa-solid fa-user"></i> Human-like</button>
      <button class="tab" data-tab="aiTab" role="tab"><i class="fa-solid fa-microchip"></i> AI-like</button>
      <button class="tab" data-tab="fullTab" role="tab"><i class="fa-solid fa-file-lines"></i> Full Text</button>
    </div>
    <div class="tabpanes">
      <div id="tipsTab" class="active"><ul id="modalList"></ul></div>
      <div id="examplesTab"><div class="pre" id="examplesPre">—</div></div>
      <div id="humanTab"><div class="pre" id="humanSnippetsPre">Run Analyze to view human-like snippets.</div></div>
      <div id="aiTab"><div class="pre" id="aiSnippetsPre">Run Analyze to view AI-like snippets.</div></div>
      <div id="fullTab"><div class="pre" id="fullTextPre">Run Analyze to load full text.</div></div>
    </div>
  </div>
</div>

<script>
/* i18n (trimmed) */
const I18N = {
  en:{title:"Semantic SEO Master Analyzer", analyze_title:"Analyze a URL", legend_line:"The wheel fills with your overall score. <span class='legend l-green'>Green ≥ 80</span> <span class='legend l-orange'>Orange 60–79</span> <span class='legend l-red'>Red &lt; 60</span>", overall:"Overall", page_url:"Page URL", analyze:"Analyze", print:"Print", reset:"Reset", auto_check:"Auto-apply checkmarks (≥ 70)"},
};
const LANGS = [["en","English"]];
(function(){
  const dockBtn = document.getElementById('langOpen');
  const panel = document.getElementById('langPanel');
  const card = document.getElementById('langCard');
  function fill(){ card.innerHTML=''; LANGS.forEach(([code,label])=>{ const div=document.createElement('div'); div.className='lang-item'; div.dataset.code=code; div.innerHTML=`<span class="lang-flag" style="background:#888"></span><strong>${label}</strong>`; card.appendChild(div); }); }
  function apply(code){
    const d=I18N[code]||I18N.en;
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
  }
  dockBtn.addEventListener('click', ()=> panel.style.display = panel.style.display==='block' ? 'none' : 'block');
  panel.addEventListener('click', (e)=>{ const it=e.target.closest('.lang-item'); if(!it) return; apply(it.dataset.code); panel.style.display='none'; });
  fill(); apply(localStorage.getItem('lang')||'en');
})();

/* Background canvas (trim) */
(function(){
  const bc = document.getElementById('brainCanvas'), bctx = bc.getContext('2d');
  let bw, bh, pts=[]; function rs(){bw= bc.width = innerWidth; bh= bc.height = innerHeight; pts = Array.from({length:80},()=>({x:Math.random()*bw,y:Math.random()*bh,vx:(Math.random()-.5)*.4,vy:(Math.random()-.5)*.4}))}
  addEventListener('resize',rs,{passive:true}); rs();
  (function loop(){ bctx.clearRect(0,0,bw,bh); for(const p of pts){ p.x+=p.vx; p.y+=p.vy; if(p.x<0||p.x>bw) p.vx*=-1; if(p.y<0||p.y>bh) p.vy*=-1; }
    for(let i=0;i<pts.length;i++){ for(let j=i+1;j<pts.length;j++){ const a=pts[i],b=pts[j]; const d=Math.hypot(a.x-b.x,a.y-b.y); if(d<140){ const al=(1-d/140)*0.45; bctx.strokeStyle=`rgba(157,92,255,${al})`; bctx.beginPath(); bctx.moveTo(a.x,a.y); bctx.lineTo(b.x,b.y); bctx.stroke(); } } } requestAnimationFrame(loop); })();
})();

/* Back to Top */
(function(){
  const btn = document.getElementById('backTop'); const link = document.getElementById('toTopLink');
  function onScroll(){ btn.style.display = window.scrollY>300 ? 'grid' : 'none'; }
  addEventListener('scroll', onScroll, {passive:true}); onScroll();
  const goTop = e => { e && e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); };
  btn.addEventListener('click', goTop); link.addEventListener('click', goTop);
})();

/* Chip tone helpers (content thresholds: >80 green / 60-80 orange / <60 red) */
function setChipTone(el, value, {mode='overall'} = {}){
  if (!el) return;
  el.classList.remove('chip-good','chip-mid','chip-bad');
  const ico = el.querySelector('i.ico'); if (ico) ico.classList.remove('ico-green','ico-orange','ico-red','ico-purple');
  const v = Number(value); if (Number.isNaN(v)) return;
  if (mode === 'content') {
    if (v > 80){ el.classList.add('chip-good'); if (ico) ico.classList.add('ico-green'); }
    else if (v >= 60){ el.classList.add('chip-mid'); if (ico) ico.classList.add('ico-orange'); }
    else { el.classList.add('chip-bad'); if (ico) ico.classList.add('ico-red'); }
  } else {
    if (v >= 80){ el.classList.add('chip-good'); if (ico) ico.classList.add('ico-green'); }
    else if (v >= 60){ el.classList.add('chip-mid'); if (ico) ico.classList.add('ico-orange'); }
    else { el.classList.add('chip-bad'); if (ico) ico.classList.add('ico-red'); }
  }
}

/* Score wheel */
const WHEEL = { circumference: 339, circle: null, text: null };
function setScoreWheel(value){
  if (!WHEEL.circle) { WHEEL.circle = document.querySelector('.score-wheel .progress'); WHEEL.text = document.getElementById('overallScore'); }
  const v = Math.max(0, Math.min(100, Number(value)));
  const offset = WHEEL.circumference - (v/100) * WHEEL.circumference;
  WHEEL.circle.style.strokeDashoffset = offset;
  if (v >= 80)      WHEEL.circle.style.stroke = 'url(#gradGood)';
  else if (v >= 60) WHEEL.circle.style.stroke = 'url(#gradMid)';
  else              WHEEL.circle.style.stroke = 'url(#gradBad)';
  WHEEL.text.textContent = Math.round(v) + '%';
  document.getElementById('overallScoreInline').textContent = Math.round(v);
  setChipTone(document.getElementById('overallChip'), v, {mode:'overall'});
}

/* Checklist + scoring (unchanged core) */
(function () {
  const STORAGE_KEY = 'semanticSeoChecklistV6';
  const total = 25;
  const boxes = () => Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
  const bar = document.getElementById('progressBar');
  const caption = document.getElementById('progressCaption');
  const contentScoreInline = document.getElementById('contentScoreInline');
  let lastAnalyzed = 0;

  function contentScore(){ const checked = boxes().filter(cb=>cb.checked).length; return Math.round((checked/total)*100); }
  function overallScoreBlended(){
    const cs = contentScore();
    if (cs===100) return 100;
    return Math.round( Math.max(lastAnalyzed, (lastAnalyzed*0.6 + cs*0.4)) );
  }
  function updateCats(){
    document.querySelectorAll('.category-card').forEach(card=>{
      const all = card.querySelectorAll('input[type="checkbox"]');
      const done = card.querySelectorAll('input[type="checkbox"]:checked');
      card.querySelector('.checked-count').textContent = done.length;
      card.querySelector('.total-count').textContent = all.length;
    });
  }
  function update(){
    const checked = boxes().filter(cb=>cb.checked).length;
    bar.style.width = ((checked/total)*100)+'%';
    caption.textContent = `${checked} of ${total} items completed`;
    updateCats();
    const cs = contentScore(); contentScoreInline.textContent = cs;
    setChipTone(document.getElementById('contentScoreChip'), cs, {mode:'content'});
    setScoreWheel( overallScoreBlended() );
  }
  function load(){ try{const saved = JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); boxes().forEach(cb=>cb.checked = saved.includes(cb.id));}catch(e){} update(); }
  function save(){ const ids = boxes().filter(cb=>cb.checked).map(cb=>cb.id); localStorage.setItem(STORAGE_KEY, JSON.stringify(ids)); }
  document.addEventListener('change', (e)=>{ if(e.target.matches('#analyzer input[type="checkbox"]')){ update(); save(); }});
  document.getElementById('resetChecklist').addEventListener('click', ()=>{ if(!confirm('Reset the checklist?')) return; localStorage.removeItem(STORAGE_KEY); boxes().forEach(cb=>cb.checked=false); for(let i=1;i<=25;i++){ setScoreBadge(i,null);} lastAnalyzed=0; setScoreWheel(0); update(); });
  document.getElementById('printChecklist').addEventListener('click', ()=> window.print());
  document.getElementById('printTop').addEventListener('click', ()=> window.print());
  document.getElementById('exportChecklist').addEventListener('click', (e)=>{
    e.preventDefault();
    const data = { checked: boxes().filter(cb=>cb.checked).map(cb=>cb.id), ts: Date.now(), v: 1 };
    const blob = new Blob([JSON.stringify(data,null,2)], {type:'application/json'});
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download='semantic-seo-checklist.json'; a.click(); URL.revokeObjectURL(a.href);
  });
  document.getElementById('importChecklist').addEventListener('click', (e)=>{ e.preventDefault(); document.getElementById('importFile').click(); });
  document.getElementById('importFile').addEventListener('change', async (e)=>{
    const file = e.target.files?.[0]; if(!file) return;
    try { const text = await file.text(); const json = JSON.parse(text||'{}'); boxes().forEach(cb=> cb.checked = (json.checked||[]).includes(cb.id)); save(); update(); } catch(err){ alert('Invalid JSON'); }
    e.target.value='';
  });

  window.setScoreBadge = (num,score)=>{ const el=document.getElementById('sc-'+num); if(!el) return; el.className='score-badge'; if(score==null){el.textContent='—';return;} el.textContent=score; if(score>=80) el.classList.add('score-good'); else if(score>=60) el.classList.add('score-mid'); else el.classList.add('score-bad'); };
  window.__setAnalyzedScore = function(v){ lastAnalyzed = Math.max(0, Math.min(100, +v||0)); setScoreWheel( overallScoreBlended() ); }
  window.__getContentScore = contentScore;
  window.__updateChecklist = update;
  load();
})();

/* Modal + Improve & AI panes (trimmed) */
(function(){
  const $ = s=>document.querySelector(s);
  const $$ = s=>Array.from(document.querySelectorAll(s));
  const backdrop = $('#modalBackdrop'), modal = $('#tipModal'), closeBtn = $('#modalClose');
  const panes = { tipsTab: $('#tipsTab'), examplesTab: $('#examplesTab'), humanTab: $('#humanTab'), aiTab: $('#aiTab'), fullTab: $('#fullTab') };
  const tabs = $$('.tab');
  function openModal(){ backdrop.style.display='block'; modal.style.display='flex'; }
  function closeModal(){ backdrop.style.display='none'; modal.style.display='none'; }
  closeBtn.addEventListener('click', closeModal); backdrop.addEventListener('click', closeModal);
  document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeModal(); });
  tabs.forEach(t=> t.addEventListener('click', ()=>{ tabs.forEach(x=>x.classList.remove('active')); Object.values(panes).forEach(p=>p.classList.remove('active')); t.classList.add('active'); panes[t.dataset.tab].classList.add('active'); }));
  document.getElementById('checklistGrid').addEventListener('click', (e)=>{
    const btn = e.target.closest('.improve-btn'); if(!btn) return;
    const id = btn.dataset.id;
    const idx = parseInt(id.split('-')[1],10);
    const labelEl = btn.parentElement.querySelector('label span');
    const label = labelEl ? labelEl.textContent.trim() : `Item ${idx}`;
    const tips = (window.__lastSuggestions||{})[id] || ['Run Analyze to get fresh tips for this item.'];
    const ul = document.getElementById('modalList'); ul.innerHTML=''; tips.forEach(t=>{ const li=document.createElement('li'); li.textContent=t; ul.appendChild(li); });
    const q = encodeURIComponent(label + ' SEO examples');
    document.getElementById('examplesPre').innerHTML = `Open examples:\nhttps://www.google.com/search?q=${q}\n\nTry site: operators with top competitors.`;
    tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="tipsTab"]').classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.tipsTab.classList.add('active'); openModal();
  });
  document.getElementById('viewAIText').addEventListener('click', ()=> switchTo('aiTab'));
  document.getElementById('viewHumanBtn').addEventListener('click', ()=> switchTo('humanTab'));
  document.getElementById('viewAIBtn').addEventListener('click', ()=> switchTo('aiTab'));
  function switchTo(key){ tabs.forEach(x=>x.classList.remove('active')); document.querySelector(`[data-tab="${key}"]`).classList.add('active'); Object.values(panes).forEach(p=>p.classList.remove('active')); panes[key].classList.add('active'); openModal(); }
  window.__setAIData = function(ai){
    const aiSn = ai?.ai_sentences || [];
    const huSn = ai?.human_sentences || [];
    document.getElementById('aiSnippetsPre').textContent = aiSn.length ? aiSn.join('\n\n') : 'No AI-like snippets detected.';
    document.getElementById('humanSnippetsPre').textContent = huSn.length ? huSn.join('\n\n') : 'No human-like snippets isolated.';
    document.getElementById('fullTextPre').textContent = ai?.full_text || 'No text captured.';
    document.getElementById('aiPct').textContent = (typeof ai?.ai_pct==='number') ? ai.ai_pct : '—';
    document.getElementById('humanPct').textContent = (typeof ai?.human_pct==='number') ? ai.human_pct : '—';
  }
})();

/* URL normalize */
function normalizeUrl(u){ if(!u) return ''; u = u.trim(); if (!/^https?:\/\//i.test(u)) u = 'https://' + u.replace(/^\/+/, ''); try { new URL(u); } catch(e){} return u; }

/* NEW: Water progress controller */
const Water = (function(){
  const wrap = document.getElementById('waterWrap');
  const bar  = document.getElementById('waterBar');
  const rect = document.getElementById('waterClipRect');
  const pct  = document.getElementById('waterPct');
  const label= document.getElementById('pageUrlLabel');
  let prog = 0, intv = null;
  const H = 200; // viewBox height
  function show(){ wrap.style.display='block'; }
  function hide(){ wrap.style.display='none'; }
  function set(v){
    prog = Math.max(0, Math.min(100, v));
    const y = H - (H * (prog/100));
    rect.setAttribute('y', String(y));
    bar.setAttribute('aria-valuenow', Math.round(prog));
    pct.textContent = Math.round(prog) + '%';
  }
  function start(){
    show(); set(0);
    label.classList.add('animating');
    clearInterval(intv);
    intv = setInterval(()=>{ if (prog < 90) set(prog + 1.2); }, 50);
  }
  function finish(){
    clearInterval(intv);
    const step = ()=>{ if (prog >= 100){ setTimeout(()=>{ label.classList.remove('animating'); }, 300); return; }
      set(prog + Math.max(1.5, (100-prog)*0.12)); requestAnimationFrame(step); };
    requestAnimationFrame(step);
  }
  function reset(){ clearInterval(intv); set(0); hide(); label.classList.remove('animating'); }
  return { start, finish, reset, set, show, hide };
})();

/* Analyze flow */
(function(){
  const $ = s => document.querySelector(s);
  document.getElementById('copyQuick').addEventListener?.('click', async ()=>{
    const bits = [
      `HTTP: ${document.getElementById('rStatus')?.textContent||''}`,
      `Title: ${document.getElementById('rTitleLen')?.textContent||''}`,
      `Meta: ${document.getElementById('rMetaLen')?.textContent||''}`,
      `Canon: ${document.getElementById('rCanonical')?.textContent||''}`,
      `Robots: ${document.getElementById('rRobots')?.textContent||''}`,
      `H1/H2/H3: ${document.getElementById('rHeadings')?.textContent||''}`,
      `Internal: ${document.getElementById('rInternal')?.textContent||''}`,
      `Schema: ${document.getElementById('rSchema')?.textContent||''}`,
      `Overall: ${document.getElementById('overallScoreInline')?.textContent||''}/100`,
      `Content: ${document.getElementById('contentScoreInline')?.textContent||''}/100`,
      `AI: ${document.getElementById('aiPct')?.textContent || '—'}% / Human: ${document.getElementById('humanPct')?.textContent || '—'}%`
    ];
    try { await navigator.clipboard.writeText(bits.join('\n')); alert('Report copied!'); } catch(e){ alert('Could not copy'); }
  });

  document.getElementById('analyzeForm').addEventListener('submit', (e)=>{ e.preventDefault(); document.getElementById('analyzeBtn').click(); });
  document.getElementById('analyzeBtn').addEventListener('click', analyze);

  async function analyze(){
    const url = normalizeUrl($('#analyzeUrl').value);
    const status = $('#analyzeStatus'); const btn = $('#analyzeBtn'); const report = $('#analyzeReport');
    if (!url){ status.textContent = 'Please enter a URL.'; Water.reset(); return; }
    status.textContent = 'Analyzing…';
    btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Analyzing';
    Water.start();

    try{
      const resp = await fetch('{{ route('analyze.json') }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({ url })
      });
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || 'Failed');

      // fill quick chips
      $('#rStatus')?.textContent = data.status;
      $('#rTitleLen')?.textContent = (data.title || '').length;
      $('#rMetaLen')?.textContent = data.meta_description_len;
      $('#rCanonical')?.textContent = data.canonical ? 'Yes' : 'No';
      $('#rRobots')?.textContent = data.robots || '—';
      $('#rViewport')?.textContent = data.viewport ? 'Yes' : 'No';
      $('#rHeadings')?.textContent = `${data.counts.h1}/${data.counts.h2}/${data.counts.h3}`;
      $('#rInternal')?.textContent = data.counts.internal_links;
      const types = (data.schema.found_types || []).slice(0,6).join(', ') || '—';
      $('#rSchema')?.textContent = types;
      $('#rAutoCount')?.textContent = (data.auto_check_ids||[]).length;
      report && (report.style.display='block');

      // per-item scores
      window.__lastSuggestions = data.suggestions || {};
      for (let i=1;i<=25;i++){ const key='ck-'+i; window.setScoreBadge && setScoreBadge(i, data.scores?.[key]); }

      // writer chip
      const ai = data.ai_detection || {};
      const badge = document.getElementById('aiBadge');
      if (badge){
        badge.classList.remove('chip-good','chip-mid','chip-bad');
        const labelMap={likely_human:'Likely Human', mixed:'Mixed', likely_ai:'Likely AI'};
        const iconMap={likely_human:'fa-user-check', mixed:'fa-shuffle', likely_ai:'fa-robot'};
        const colorMap={likely_human:'ico-green', mixed:'ico-orange', likely_ai:'ico-red'};
        const chipMap={likely_human:'chip-good', mixed:'chip-mid', likely_ai:'chip-bad'};
        const key = (ai.label||'').toLowerCase();
        const label = labelMap[key] || 'Unknown';
        const icon  = iconMap[key] || 'fa-user';
        const icoC  = colorMap[key] || 'ico-purple';
        const chipC = chipMap[key];
        const conf = (typeof ai.likelihood==='number') ? ` (${ai.likelihood}%)` : '';
        const aiStr = (typeof ai.ai_pct==='number') ? ` — ${ai.ai_pct}% AI-like` : '';
        const humanStr = (typeof ai.human_pct==='number') ? ` — ${ai.human_pct}% Human` : '';
        badge.innerHTML = `<i class="fa-solid ${icon} ico ${icoC}"></i> Writer: <b>${label}${conf}${aiStr}${humanStr}</b>`;
        if (chipC) badge.classList.add(chipC);
        badge.title = (ai.reasons||[]).join(' • ');
        document.getElementById('aiPct').textContent = (typeof ai.ai_pct==='number') ? ai.ai_pct : '—';
        document.getElementById('humanPct').textContent = (typeof ai.human_pct==='number') ? ai.human_pct : '—';
        window.__setAIData && window.__setAIData(ai);
      }

      // blend overall score baseline
      const backendOverall = typeof data.overall_score === 'number' ? data.overall_score : 0;
      window.__setAnalyzedScore && window.__setAnalyzedScore(backendOverall);

      // Auto-apply checks then recompute UI
      if ($('#autoApply')?.checked) {
        const all = document.querySelectorAll('#analyzer input[type="checkbox"]');
        all.forEach(cb => cb.checked = false);
        (data.auto_check_ids||[]).forEach(id => { const el=document.getElementById(id); if(el) el.checked=true; });
      }
      window.__updateChecklist && window.__updateChecklist();

      // repaint content chip
      const csNow = window.__getContentScore ? window.__getContentScore() : 0;
      document.getElementById('contentScoreInline').textContent = csNow;
      setChipTone(document.getElementById('contentScoreChip'), csNow, {mode:'content'});

      // done
      Water.finish();
      const wheel = parseInt(document.getElementById('overallScoreInline').textContent||'0',10);
      status.textContent = wheel>=80 ? 'Great! You passed—keep going.' : (wheel<60 ? 'Score is low — optimize and re-Analyze.' : 'Solid! Improve a few items to hit green.');
      setTimeout(()=> status.textContent = '', 4200);
    } catch(e){
      document.getElementById('analyzeStatus').textContent = 'Error: '+e.message;
      Water.finish();
    } finally {
      btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Analyze';
    }
  }
})();
</script>

<!-- Procedural Smoke (WebGL2) -->
<script>
(function(){
  const canvas = document.getElementById('smokeFX'); if (!canvas) return;
  const dpr = Math.min(2, window.devicePixelRatio || 1);
  let gl = null, vw=0, vh=0, start=performance.now();
  function resize(){ vw = canvas.clientWidth  = innerWidth; vh = canvas.clientHeight = innerHeight; canvas.width  = Math.floor(vw * dpr); canvas.height = Math.floor(vh * dpr); if (gl) gl.viewport(0,0,canvas.width,canvas.height); }
  addEventListener('resize', resize, {passive:true}); resize();
  try { gl = canvas.getContext('webgl2', { alpha:true, antialias:false, depth:false, stencil:false }); } catch(e){}
  if (!gl) return;
  const vs=`#version 300 es
  precision highp float; const vec2 v[3]=vec2[3](vec2(-1.,-1.),vec2(3.,-1.),vec2(-1.,3.));
  out vec2 uv; void main(){ vec2 p=v[gl_VertexID]; uv=.5*(p+1.); gl_Position=vec4(p,0,1); }`;
  const fs=`#version 300 es
  precision highp float; in vec2 uv; out vec4 o; uniform vec2 r; uniform float t,a;
  float h(vec2 p){ return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453); }
  float n(vec2 p){ vec2 i=floor(p), f=fract(p); float A=h(i),B=h(i+vec2(1,0)),C=h(i+vec2(0,1)),D=h(i+vec2(1,1));
    vec2 u=f*f*(3.-2.*f); return mix(A,B,u.x)+(C-A)*u.y*(1.-u.x)+(D-B)*u.x*u.y; }
  float f(vec2 p){ float v=0., s=.5; mat2 m=mat2(1.6,1.2,-1.2,1.6); for(int i=0;i<5;i++){ v+=s*n(p); p=m*p; s*=.5; } return v; }
  void main(){ vec2 p=(uv-.5)*vec2(a,1.); float q=f(p*1.6+vec2(t*.4,-t*.3)); float d=smoothstep(.35,.95,q); vec3 c=mix(vec3(.24,.88,1.),vec3(.61,.36,1.),uv.x); o=vec4(c*d,.75*d); }`;
  function sh(s,t){const o=gl.createShader(t);gl.shaderSource(o,s);gl.compileShader(o);return o;}
  const prog=gl.createProgram(); gl.attachShader(prog,sh(vs,gl.VERTEX_SHADER)); gl.attachShader(prog,sh(fs,gl.FRAGMENT_SHADER)); gl.linkProgram(prog);
  const ur=gl.getUniformLocation(prog,'r'), ut=gl.getUniformLocation(prog,'t'), ua=gl.getUniformLocation(prog,'a');
  function draw(now){ gl.useProgram(prog); gl.uniform2f(ur,canvas.width,canvas.height); gl.uniform1f(ut,(now-start)*1e-3); gl.uniform1f(ua,canvas.width/Math.max(1.,canvas.height)); gl.drawArrays(gl.TRIANGLES,0,3); requestAnimationFrame(draw); }
  requestAnimationFrame(draw);
})();
</script>
</body>
</html>
