{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Semantic SEO Master • Ultra Tech Global</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
:root{
  --bg:#08080f; --panel:#0f1022; --panel-2:#141433; --line:#1e1a33;
  --text:#f0effa; --text-dim:#b6b3d6; --text-muted:#9aa0c3;
  --primary:#9b5cff; --secondary:#ff2045; --accent:#3de2ff;
  --good:#16c172; --warn:#f59e0b; --bad:#ef4444;
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

/* --- Canvas layers --- */
#linesCanvas, #linesCanvas2, #brainCanvas { position:fixed; inset:0; z-index:0; pointer-events:none; }
#brainCanvas{opacity:.10}

/* --- NEW: Procedural Cloud Smoke canvas --- */
#smokeFX{
  position:fixed; inset:0; z-index:0; pointer-events:none;
  opacity:1; filter:saturate(115%) contrast(105%);
}

/* Hide the previous decorative blobs/clouds to avoid double effects (keep DOM for fallback if you like) */
.bg-smoke .blob, .clouds { display:none !important; }

/* --- (Old) Cloudy smoke (kept but hidden by rule above) --- */
.bg-smoke{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
.blob{position:absolute;border-radius:50%;filter:blur(90px);mix-blend-mode:screen;animation:float 36s linear infinite}
.blob.p{background:radial-gradient(closest-side,rgba(155,92,255,.38),rgba(155,92,255,0) 70%)}
.blob.r{background:radial-gradient(closest-side,rgba(255,32,69,.34),rgba(255,32,69,0) 70%)}
.b1{top:-18%;left:-15%;width:60vmax;height:60vmax}
.b2{bottom:-22%;right:-10%;width:62vmax;height:62vmax;animation-direction:reverse;animation-duration:30s}
.b3{top:10%;right:15%;width:50vmax;height:50vmax;animation-duration:28s}
.b4{bottom:10%;left:25%;width:48vmax;height:48vmax;animation-duration:40s}

/* (Old) cloud cluster */
.clouds { position:absolute; right:-6vmax; bottom:-6vmax; width:80vmax; height:60vmax; pointer-events:none; }
.clouds .c { position:absolute; border-radius:50%; filter:blur(40px); opacity:.95; mix-blend-mode:screen; }
.clouds .c.cyan   { background:radial-gradient(closest-side, rgba(61,226,255,.85), rgba(61,226,255,0) 75%); }
.clouds .c.purple { background:radial-gradient(closest-side, rgba(155,92,255,.80), rgba(155,92,255,0) 75%); }
.clouds .c.orange { background:radial-gradient(closest-side, rgba(255,182,72,.80), rgba(255,182,72,0) 75%); }
.clouds .c.teal   { background:radial-gradient(closest-side, rgba(34,197,94,.78), rgba(34,197,94,0) 75%); }
.clouds .c1{ width:50vmax;height:28vmax; right:0; bottom:0; animation:cloud 40s ease-in-out infinite; }
.clouds .c2{ width:46vmax;height:26vmax; right:6vmax; bottom:2vmax; animation:cloud 46s ease-in-out infinite reverse; }
.clouds .c3{ width:42vmax;height:24vmax; right:10vmax; bottom:3vmax; animation:cloud 52s ease-in-out infinite; }
.clouds .c4{ width:38vmax;height:22vmax; right:14vmax; bottom:5vmax; animation:cloud 58s ease-in-out infinite reverse; }
@keyframes float{0%{transform:translate3d(0,0,0)}50%{transform:translate3d(-6%,7%,0)}100%{transform:translate3d(0,0,0)}}
@keyframes cloud{0%{transform:translate3d(0,0,0)}50%{transform:translate3d(-3%,-4%,0)}100%{transform:translate3d(0,0,0)}}

.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}

/* Header */
header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 22px;border-bottom:1px solid var(--line);backdrop-filter:saturate(140%) blur(10px);background:rgba(15,16,34,.35)}
.brand{display:flex;align-items:center;gap:1rem}
.brand-badge{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(155,92,255,.3),rgba(255,32,69,.25));border:1px solid rgba(255,255,255,.08); color:#ffd1dc}
.hero-heading{font-size:4.2rem;font-weight:1000;line-height:1.02;margin:.1rem 0;letter-spacing:.8px;background:linear-gradient(90deg,#b892ff,#ff2045 55%,#ff8a5b 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;text-shadow:0 0 28px rgba(155,92,255,.25)}

/* Language dock, buttons same as before… */
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
.section-title{font-size:1.6rem;margin:0 0 .3rem} .section-subtitle{margin:0;color:var(--text-dim)}

/* Wheel row + Content score chip */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-wheel{width:100%;height:auto;transform:rotate(-90deg)}
.score-wheel circle{fill:none;stroke-width:14;stroke-linecap:round}
.score-wheel .bg{stroke:rgba(255,255,255,.12)}
.score-wheel .progress{stroke:url(#grad);stroke-dasharray:339;stroke-dashoffset:339;transition:stroke-dashoffset .6s ease,stroke .3s ease,filter .3s ease;filter:drop-shadow(0 0 10px rgba(155,92,255,.35))}
.score-text{font-size:3rem;font-weight:1000;fill:#fff;transform:rotate(90deg);text-shadow:0 0 18px rgba(255,32,69,.25)}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28)}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)} .l-orange{background:rgba(245,158,11,.18)} .l-green{background:rgba(34,197,94,.18)}

/* URL input */
.analyze-form input[type="url"]{
  position:relative; z-index:5; width:100%; padding:1rem 1.2rem; border-radius:14px;
  border:1px solid #1b1b35; background:#0b0d21; color:var(--text);
  box-shadow:0 0 0 0 rgba(155,92,255,.0); transition:.25s;
}
.analyze-form input[type="url"]:focus{ outline:none; border-color:#5942ff; box-shadow:0 0 0 6px rgba(155,92,255,.15); }
.analyze-row{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;margin-top:.5rem}

/* Progress */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
.progress-fill{height:100%;background:linear-gradient(135deg,#9b5cff,#ff2045);width:0%;transition:width .35s ease}
.progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}

/* Category grid */
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

/* Modal */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);display:none;z-index:70}
.modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:80}
.modal-card{width:min(1000px,96vw);background:var(--panel-2);border:1px solid rgba(255,255,255,.12);border-radius:16px;box-shadow:var(--shadow);padding:16px}
.modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem}
.modal-title{margin:0;font-size:1.2rem}
.modal-close{background:transparent;border:1px solid rgba(255,255,255,.2);border-radius:10px;color:#fff;padding:.35rem .6rem;cursor:pointer}
.tabs{display:flex;gap:.4rem;margin:.4rem 0;flex-wrap:wrap}
.tab{padding:.35rem .7rem;border-radius:10px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);cursor:pointer;font-weight:800}
.tab.active{background:linear-gradient(135deg,#3de2ff22,#9b5cff22);border-color:#3de2ff66}
.tabpanes > div{display:none}
.tabpanes > div.active{display:block}
.pre{white-space:pre-wrap;background:#0b0d21;border:1px solid #1b1b35;border-radius:12px;padding:12px;color:#cfd3f6;max-height:60vh;overflow:auto}

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
@media print{#linesCanvas,#linesCanvas2,#brainCanvas,#smokeFX,.bg-smoke,.modal-backdrop,.modal,header.site,#backTop,.lang-dock,.lang-panel{display:none!important}}
</style>
</head>
<body>
<canvas id="brainCanvas"></canvas>
<canvas id="linesCanvas"></canvas>
<canvas id="linesCanvas2"></canvas>

<!-- NEW: WebGL smoke canvas -->
<canvas id="smokeFX" aria-hidden="true"></canvas>

<div class="bg-smoke">
  <span class="blob p b1"></span>
  <span class="blob r b2"></span>
  <span class="blob p b3"></span>
  <span class="blob r b4"></span>
  <!-- Cloud cluster (kept for fallback styling but hidden by CSS) -->
  <div class="clouds">
    <span class="c cyan   c1"></span>
    <span class="c purple c2"></span>
    <span class="c orange c3"></span>
    <span class="c teal   c4"></span>
  </div>
</div>

<!-- gradients for score wheel -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#9b5cff"/><stop offset="100%" stop-color="#ff2045"/></linearGradient>
    <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/></linearGradient>
    <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/></linearGradient>
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
      <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
      <div><div class="hero-heading" data-i="title">Semantic SEO Master Analyzer</div></div>
    </div>
    <div style="display:flex;gap:.5rem">
      <button class="btn btn-ghost" id="printTop"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
    </div>
  </header>

  <section class="analyzer" id="analyzer">
    <h2 class="section-title" data-i="analyze_title">Analyze a URL</h2>
    <p class="section-subtitle" data-i="legend_line">
      The wheel fills with your overall score. <span class="legend l-green">Green ≥ 80</span> <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Red &lt; 60</span>
    </p>

    <div class="score-area">
      <div class="score-container">
        <svg class="score-wheel" viewBox="0 0 120 120" aria-label="Overall score">
          <circle class="bg" cx="60" cy="60" r="54"/>
          <circle class="progress" cx="60" cy="60" r="54"/>
          <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="score-text" id="overallScore">0</text>
        </svg>
      </div>
      <div style="display:flex;flex-direction:column;gap:.5rem">
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <span class="chip"><span data-i="overall">Overall</span>: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="contentScoreChip">Content: <b id="contentScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge">Writer: <b>—</b></span>
          <button id="viewAIText" class="btn btn-neon" style="--pad:.5rem .8rem"><i class="fa-solid fa-robot"></i> Evidence</button>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <button id="viewHumanBtn" class="btn btn-ghost" style="--pad:.4rem .7rem"><i class="fa-solid fa-user"></i> Human‑like: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost" style="--pad:.4rem .7rem"><i class="fa-solid fa-microchip"></i> AI‑like: <b id="aiPct">—</b>%</button>
        </div>
      </div>
    </div>

    <div class="analyze-box" style="margin-top:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px">
      <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
        <label for="analyzeUrl" style="display:block;font-weight:800;margin-bottom:.35rem" data-i="page_url">Page URL</label>
        <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" />
        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> <span data-i="auto_check">Auto‑apply checkmarks (≥ 70)</span>
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-danger"><i class="fa-solid fa-magnifying-glass"></i> <span data-i="analyze">Analyze</span></button>
          <button class="btn btn-neon" id="printChecklist"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
          <button class="btn btn-ghost" id="resetChecklist"><i class="fa-solid fa-rotate"></i> <span data-i="reset">Reset</span></button>
        </div>
        <div id="analyzeStatus" style="margin-top:.4rem;color:var(--text-dim)"></div>
      </form>

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
          <span class="chip">Auto‑checked: <b id="rAutoCount">—</b></span>
        </div>
      </div>
    </div>

    <!-- Progress -->
    <div class="progress-wrap">
      <div class="progress-bar"><div class="progress-fill" id="progressBar"></div></div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    <!-- Categories / checklist (unchanged structure) -->
    <div class="analyzer-grid">
      @php $labels = [
        1=>'Define search intent & primary topic',
        2=>'Map target & related keywords (synonyms/PAA)',
        3=>'H1 includes primary topic naturally',
        4=>'Integrate FAQs / questions with answers',
        5=>'Readable, NLP‑friendly language',
        6=>'Title tag (≈50–60 chars) w/ primary keyword',
        7=>'Meta description (≈140–160 chars) + CTA',
        8=>'Canonical tag set correctly',
        9=>'Indexable & listed in XML sitemap',
        10=>'E‑E‑A‑T signals (author, date, expertise)',
        11=>'Unique value vs. top competitors',
        12=>'Facts & citations up to date',
        13=>'Helpful media (images/video) w/ captions',
        14=>'Logical H2/H3 headings & topic clusters',
        15=>'Internal links to hub/related pages',
        16=>'Clean, descriptive URL slug',
        17=>'Breadcrumbs enabled (+ schema)',
        18=>'Mobile‑friendly, responsive layout',
        19=>'Optimized speed (compression, lazy‑load)',
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
            <span class="category-icon"><i class="fas {{ $c[3] }}"></i></span>
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
                  <input type="checkbox" id="ck-{{ $i }}">
                  <span>{{ $labels[$i] }}</span>
                </label>
                <span class="score-badge" id="sc-{{ $i }}">—</span>
                <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
              </li>
            @endfor
          </ul>
        </article>
      @endforeach
    </div>
  </section>
</div>

<footer class="site">
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

<button id="backTop" title="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

<!-- Modal -->
<div class="modal-backdrop" id="modalBackdrop"></div>
<div class="modal" id="tipModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle">Improve</h3>
      <button class="modal-close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="tabs">
      <button class="tab active" data-tab="tipsTab"><i class="fa-solid fa-lightbulb"></i> Tips</button>
      <button class="tab" data-tab="examplesTab"><i class="fa-brands fa-google"></i> Examples (Google)</button>
      <button class="tab" data-tab="humanTab"><i class="fa-solid fa-user"></i> Human‑like</button>
      <button class="tab" data-tab="aiTab"><i class="fa-solid fa-microchip"></i> AI‑like</button>
      <button class="tab" data-tab="fullTab"><i class="fa-solid fa-file-lines"></i> Full Text</button>
    </div>
    <div class="tabpanes">
      <div id="tipsTab" class="active"><ul id="modalList"></ul></div>
      <div id="examplesTab"><div class="pre" id="examplesPre">—</div></div>
      <div id="humanTab"><div class="pre" id="humanSnippetsPre">Run Analyze to view human‑like snippets.</div></div>
      <div id="aiTab"><div class="pre" id="aiSnippetsPre">Run Analyze to view AI‑like snippets.</div></div>
      <div id="fullTab"><div class="pre" id="fullTextPre">Run Analyze to load full text.</div></div>
    </div>
  </div>
</div>

<script>
/* ---------- i18n (same 10 languages as before) ---------- */
const I18N = {
  en:{title:"Semantic SEO Master Analyzer", analyze_title:"Analyze a URL", legend_line:"The wheel fills with your overall score. <span class='legend l-green'>Green ≥ 80</span> <span class='legend l-orange'>Orange 60–79</span> <span class='legend l-red'>Red &lt; 60</span>", overall:"Overall", page_url:"Page URL", analyze:"Analyze", print:"Print", reset:"Reset", auto_check:"Auto‑apply checkmarks (≥ 70)"},
  es:{title:"Analizador Maestro de SEO Semántico", analyze_title:"Analiza una URL", legend_line:"La rueda se llena con tu puntuación general. <span class='legend l-green'>Verde ≥ 80</span> <span class='legend l-orange'>Naranja 60–79</span> <span class='legend l-red'>Rojo &lt; 60</span>", overall:"Total", page_url:"URL de la página", analyze:"Analizar", print:"Imprimir", reset:"Restablecer", auto_check:"Aplicar automáticamente (≥ 70)"},
  fr:{title:"Analyseur Maître SEO Sémantique", analyze_title:"Analyser une URL", legend_line:"La ruota si riempie con il punteggio complessivo. <span class='legend l-green'>Verde ≥ 80</span> <span class='legend l-orange'>Arancione 60–79</span> <span class='legend l-red'>Rosso &lt; 60</span>", overall:"Global", page_url:"URL de la page", analyze:"Analyser", print:"Imprimer", reset:"Réinitialiser", auto_check:"Cocher automatiquement (≥ 70)"},
  de:{title:"Semantischer SEO Meister‑Analyzer", analyze_title:"URL analysieren", legend_line:"Das Rad füllt sich mit Ihrem Gesamtscore. <span class='legend l-green'>Grün ≥ 80</span> <span class='legend l-orange'>Orange 60–79</span> <span class='legend l-red'>Rot &lt; 60</span>", overall:"Gesamt", page_url:"Seiten‑URL", analyze:"Analysieren", print:"Drucken", reset:"Zurücksetzen", auto_check:"Automatisch anwenden (≥ 70)"},
  it:{title:"Analizzatore Maestro SEO Semantico", analyze_title:"Analizza un URL", legend_line:"La ruota si riempie con il punteggio complessivo. <span class='legend l-green'>Verde ≥ 80</span> <span class='legend l-orange'>Arancione 60–79</span> <span class='legend l-red'>Rosso &lt; 60</span>", overall:"Totale", page_url:"URL della pagina", analyze:"Analizza", print:"Stampa", reset:"Reimposta", auto_check:"Applica automaticamente (≥ 70)"},
  pt:{title:"Analisador Mestre de SEO Semântico", analyze_title:"Analisar uma URL", legend_line:"A roda preenche com sua pontuação geral. <span class='legend l-green'>Verde ≥ 80</span> <span class='legend l-orange'>Laranja 60–79</span> <span class='legend l-red'>Vermelho &lt; 60</span>", overall:"Geral", page_url:"URL da página", analyze:"Analisar", print:"Imprimir", reset:"Reiniciar", auto_check:"Aplicar automaticamente (≥ 70)"},
  tr:{title:"Anlamsal SEO Usta Analizörü", analyze_title:"Bir URL analiz et", legend_line:"Teker genel skorla dolar. <span class='legend l-green'>Yeşil ≥ 80</span> <span class='legend l-orange'>Turuncu 60–79</span> <span class='legend l-red'>Kırmızı &lt; 60</span>", overall:"Genel", page_url:"Sayfa URL'si", analyze:"Analiz Et", print:"Yazdır", reset:"Sıfırla", auto_check:"Otomatik işaretle (≥ 70)"},
  ar:{title:"محلل SEO الدلالي المتقدم", analyze_title:"حلّل رابط URL", legend_line:"تمتلئ العجلة بدرجتك الإجمالية. <span class='legend l-green'>أخضر ≥ 80</span> <span class='legend l-orange'>برتقالي 60–79</span> <span class='legend l-red'>أحمر &lt; 60</span>", overall:"الإجمالي", page_url:"رابط الصفحة", analyze:"تحليل", print:"طباعة", reset:"إعادة ضبط", auto_check:"تفعيل تلقائي (≥ 70)"},
  ru:{title:"Мастер‑анализатор Семантического SEO", analyze_title:"Анализ URL", legend_line:"Колесо заполняется вашим общим баллом. <span class='legend l-green'>Зелёный ≥ 80</span> <span class='legend л-orange'>Оранжевый 60–79</span> <span class='legend л-red'>Красный &lt; 60</span>", overall:"Итог", page_url:"URL страницы", analyze:"Анализ", print:"Печать", reset:"Сброс", auto_check:"Авто‑отметки (≥ 70)"},
  ur:{title:"سیمنٹک SEO ماسٹر اینالائزر", analyze_title:"یو آر ایل تجزیہ کریں", legend_line:"پہیہ آپ کے مجموعی اسکور سے بھر جاتا ہے۔ <span class='legend l-green'>سبز ≥ 80</span> <span class='legend l-orange'>نارنجی 60–79</span> <span class='legend l-red'>سرخ &lt; 60</span>", overall:"مجموعی", page_url:"صفحہ کا یو آر ایل", analyze:"تجزیہ", print:"پرنٹ", reset:"ری سیٹ", auto_check:"≥ 70 خودکار چیک"}
};
const LANGS = [["en","English"],["es","Español"],["fr","Français"],["de","Deutsch"],["it","Italiano"],["pt","Português"],["tr","Türkçe"],["ar","العربية"],["ru","Русский"],["ur","اردو"]];
(function(){
  const dockBtn = document.getElementById('langOpen');
  const panel = document.getElementById('langPanel');
  const card = document.getElementById('langCard');
  function fill(){ card.innerHTML=''; LANGS.forEach(([code,label])=>{ const div=document.createElement('div'); div.className='lang-item'; div.dataset.code=code; div.innerHTML=`<span class="lang-flag" style="background:linear-gradient(135deg,#${(Math.random()*0xffffff|0).toString(16).padStart(6,'0')},#${(Math.random()*0xffffff|0).toString(16).padStart(6,'0')})"></span><strong>${label}</strong>`; card.appendChild(div); }); }
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

/* ---------- Brain + Dancing lines (2 layers) ---------- */
(function(){
  const bc = document.getElementById('brainCanvas'), bctx = bc.getContext('2d');
  let bw, bh, bpts=[]; function bResize(){bw= bc.width = innerWidth; bh= bc.height = innerHeight; bpts = Array.from({length:90},()=>({x:Math.random()*bw,y:Math.random()*bh,vx:(Math.random()-.5)*.4,vy:(Math.random()-.5)*.4}))}
  addEventListener('resize',bResize,{passive:true}); bResize();
  (function step(){
    bctx.clearRect(0,0,bw,bh);
    for(const p of bpts){ p.x+=p.vx; p.y+=p.vy; if(p.x<0||p.x>bw) p.vx*=-1; if(p.y<0||p.y>bh) p.vy*=-1; }
    for(let i=0;i<bpts.length;i++){ for(let j=i+1;j<bpts.length;j++){ const a=bpts[i],b=bpts[j]; const d=Math.hypot(a.x-b.x,a.y-b.y); if(d<140){ const alpha=(1-d/140)*0.45; bctx.strokeStyle=`rgba(157,92,255,${alpha})`; bctx.lineWidth=1; bctx.beginPath(); bctx.moveTo(a.x,a.y); bctx.lineTo(b.x,b.y); bctx.stroke(); } } }
    requestAnimationFrame(step);
  })();

  function runLayer(canvasId, count, maxDist, colorFn, vel=1){
    const c = document.getElementById(canvasId), ctx = c.getContext('2d');
    let w, h, nodes=[], mouse={x:-9999,y:-9999};
    function resize(){ w = c.width = innerWidth; h = c.height = innerHeight; nodes = Array.from({length:count},()=>({x:Math.random()*w,y:Math.random()*h,vx:(Math.random()-.5)*vel,vy:(Math.random()-.5)*vel})); }
    addEventListener('resize',resize,{passive:true}); resize();
    addEventListener('mousemove', e=>{mouse.x=e.clientX; mouse.y=e.clientY;},{passive:true});
    (function loop(){
      ctx.clearRect(0,0,w,h);
      for(const n of nodes){
        const dx = mouse.x - n.x, dy = mouse.y - n.y, dist = Math.hypot(dx,dy);
        const attract = dist<maxDist ? (1 - dist/maxDist) * 0.9 : 0;
        n.vx += (dx/dist||0) * attract * 0.18; n.vy += (dy/dist||0) * attract * 0.18;
        n.vx*=0.97; n.vy*=0.97; n.x+=n.vx; n.y+=n.vy;
        if(n.x<0||n.x>w) n.vx*=-1; if(n.y<0||n.y>h) n.vy*=-1;
      }
      for(let i=0;i<nodes.length;i++){
        for(let j=i+1;j<nodes.length;j++){
          const a=nodes[i], b=nodes[j];
          const d=Math.hypot(a.x-b.x,a.y-b.y);
          if(d<maxDist){
            const alpha = (1 - d/maxDist)*0.65;
            ctx.strokeStyle = colorFn(alpha);
            ctx.lineWidth = 1;
            ctx.beginPath(); ctx.moveTo(a.x,a.y); ctx.lineTo(b.x,b.y); ctx.stroke();
          }
        }
      }
      requestAnimationFrame(loop);
    })();
  }
  runLayer('linesCanvas', 140, 130, a=>`rgba(61,226,255,${a})`, 1.1);
  runLayer('linesCanvas2', 110, 120, a=>`rgba(255,32,69,${a*0.6})`, 0.9);
})();

/* ---------- Back to Top ---------- */
(function(){
  const btn = document.getElementById('backTop'); const link = document.getElementById('toTopLink');
  function onScroll(){ btn.style.display = window.scrollY>300 ? 'grid' : 'none'; }
  addEventListener('scroll', onScroll, {passive:true}); onScroll();
  const goTop = e => { e && e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); };
  btn.addEventListener('click', goTop); link.addEventListener('click', goTop);
})();

/* ---------- Score wheel helpers ---------- */
const WHEEL = { circumference: 339, circle: null, text: null };
function setScoreWheel(value){
  if (!WHEEL.circle) { WHEEL.circle = document.querySelector('.score-wheel .progress'); WHEEL.text = document.getElementById('overallScore'); }
  const v = Math.max(0, Math.min(100, value));
  const offset = WHEEL.circumference - (v/100) * WHEEL.circumference;
  WHEEL.circle.style.strokeDashoffset = offset;
  if (v >= 80) WHEEL.circle.setAttribute('stroke','url(#gradGood)');
  else if (v >= 60) WHEEL.circle.setAttribute('stroke','url(#gradMid)');
  else WHEEL.circle.setAttribute('stroke','url(#grad)');
  WHEEL.text.textContent = Math.round(v);
  document.getElementById('overallScoreInline').textContent = Math.round(v);
}

/* ---------- Checklist + scoring (fixed to allow 100) ---------- */
(function () {
  const STORAGE_KEY = 'semanticSeoChecklistV5';
  const total = 25;
  const boxes = () => Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
  const bar = document.getElementById('progressBar');
  const caption = document.getElementById('progressCaption');
  const contentChip = document.getElementById('contentScoreInline');
  let lastAnalyzed = 0;

  function contentScore(){
    const checked = boxes().filter(cb=>cb.checked).length;
    return Math.round((checked/total)*100);
  }
  function overallScoreBlended(){
    const cs = contentScore();
    const allChecked = cs===100;
    if (allChecked) return 100; // ✅ requested behavior
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
    const cs = contentScore(); contentChip.textContent = cs;
    setScoreWheel( overallScoreBlended() );
  }
  function load(){
    try{const saved = JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); boxes().forEach(cb=>cb.checked = saved.includes(cb.id));}catch(e){}
    update();
  }
  function save(){
    const ids = boxes().filter(cb=>cb.checked).map(cb=>cb.id);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
  }
  document.addEventListener('change', (e)=>{ if(e.target.matches('#analyzer input[type="checkbox"]')){ update(); save(); }});
  document.getElementById('resetChecklist').addEventListener('click', ()=>{ if(!confirm('Reset the checklist?')) return; localStorage.removeItem(STORAGE_KEY); boxes().forEach(cb=>cb.checked=false); for(let i=1;i<=25;i++){ setScoreBadge(i,null);} lastAnalyzed=0; setScoreWheel(0); update(); });
  document.getElementById('printChecklist').addEventListener('click', ()=> window.print());
  document.getElementById('printTop').addEventListener('click', ()=> window.print());
  window.setScoreBadge = (num,score)=>{ const el=document.getElementById('sc-'+num); if(!el) return; el.className='score-badge'; if(score==null){el.textContent='—';return;} el.textContent=score; if(score>=80) el.classList.add('score-good'); else if(score>=60) el.classList.add('score-mid'); else el.classList.add('score-bad'); };
  window.__setAnalyzedScore = function(v){ lastAnalyzed = Math.max(0, Math.min(100, +v||0)); setScoreWheel( overallScoreBlended() ); }
  window.__getContentScore = contentScore;
  load();
})();

/* ---------- Modal + examples + AI/Human panes ---------- */
(function(){
  const $ = s=>document.querySelector(s);
  const $$ = s=>Array.from(document.querySelectorAll(s));
  const backdrop = $('#modalBackdrop'), modal = $('#tipModal'), closeBtn = $('#modalClose');
  const title = $('#modalTitle'), tipsList = $('#modalList');
  const panes = { tipsTab: $('#tipsTab'), examplesTab: $('#examplesTab'), humanTab: $('#humanTab'), aiTab: $('#aiTab'), fullTab: $('#fullTab') };
  const tabs = $$('.tab');

  const GOOGLE_EXAMPLES = {
    'ck-1':['intitle:"{topic}" OR "{topic} guide"','"{topic}" beginner checklist'],
    'ck-2':['{topic} "people also ask"','site:reddit.com {topic} best OR vs'],
    'ck-6':['intitle:"{topic}" 55..65 chars'],
    'ck-7':['"{topic}" meta description examples'],
    'ck-11':['"{topic}" filetype:pdf data','"{topic}" case study'],
    'ck-12':['site:.gov "{topic}" statistics','site:wikipedia.org "{entity}"'],
    'ck-14':['"{topic}" outline H2'],
    'ck-15':['site:yourdomain.com "{topic}"'],
    'ck-19':['PageSpeed Insights','web.dev/measure'],
    'ck-22':['"{entity}" definition site:wikipedia.org'],
    'ck-24':['"FAQPage" JSON‑LD','"Article schema" JSON‑LD']
  };

  function openModal(){ backdrop.style.display='block'; modal.style.display='flex'; }
  function closeModal(){ backdrop.style.display='none'; modal.style.display='none'; }
  closeBtn.addEventListener('click', closeModal); backdrop.addEventListener('click', closeModal);
  document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeModal(); });

  tabs.forEach(t=> t.addEventListener('click', ()=>{
    tabs.forEach(x=>x.classList.remove('active'));
    Object.values(panes).forEach(p=>p.classList.remove('active'));
    t.classList.add('active'); panes[t.dataset.tab].classList.add('active');
  }));

  function labelFor(id){
    const input = document.getElementById(id);
    if (!input) return id;
    const span = input.parentElement?.querySelector('span');
    return span ? span.textContent.trim() : id;
  }

  document.addEventListener('click', function(e){
    const btn = e.target.closest('.improve-btn');
    if (!btn) return;
    e.preventDefault();
    const id = btn.getAttribute('data-id');
    title.textContent = 'Improve: '+labelFor(id);
    tipsList.innerHTML = '';
    const tips = (window.__lastSuggestions && window.__lastSuggestions[id]) ? window.__lastSuggestions[id] : ['Analyze the URL first to generate contextual suggestions.'];
    tips.forEach(t=>{ const li=document.createElement('li'); li.textContent=t; tipsList.appendChild(li); });

    document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="tipsTab"]').classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.tipsTab.classList.add('active');

    openModal();
  }, { capture:true });

  // AI/Human quick-open buttons
  document.getElementById('viewAIText').addEventListener('click', ()=>{
    title.textContent = 'Evidence & Full Text';
    tabs.forEach(x=>x.classList.remove('active')); panes.aiTab.classList.add('active'); document.querySelector('[data-tab="aiTab"]').classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.aiTab.classList.add('active');
    openModal();
  });
  document.getElementById('viewHumanBtn').addEventListener('click', ()=>{
    title.textContent = 'Human‑like Sentences';
    tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="humanTab"]').classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.humanTab.classList.add('active');
    openModal();
  });
  document.getElementById('viewAIBtn').addEventListener('click', ()=>{
    title.textContent = 'AI‑like Sentences';
    tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="aiTab"]').classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.aiTab.classList.add('active');
    openModal();
  });

  window.__setAIData = function(ai){
    const aiSn = ai?.ai_sentences || [];
    const huSn = ai?.human_sentences || [];
    document.getElementById('aiSnippetsPre').textContent = aiSn.length ? aiSn.join('\n\n') : 'No AI‑like snippets detected.';
    document.getElementById('humanSnippetsPre').textContent = huSn.length ? huSn.join('\n\n') : 'No human‑like snippets isolated.';
    document.getElementById('fullTextPre').textContent = ai?.full_text || 'No text captured.';
    document.getElementById('aiPct').textContent = typeof ai?.ai_pct==='number' ? ai.ai_pct : '—';
    document.getElementById('humanPct').textContent = typeof ai?.human_pct==='number' ? ai.human_pct : '—';
  }
})();

/* ---------- URL normalization + Analyze ---------- */
function normalizeUrl(u){
  if(!u) return '';
  u = u.trim();
  if (!/^https?:\/\//i.test(u)) u = 'https://' + u.replace(/^\/+/, '');
  try { new URL(u); } catch(e){ /* backend will validate */ }
  return u;
}
(function(){
  const $ = s => document.querySelector(s);
  const setChecked = (id, on) => { const el = document.getElementById(id); if (el) el.checked = !!on; };

  document.getElementById('analyzeForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    document.getElementById('analyzeBtn').click();
  });

  document.getElementById('analyzeBtn').addEventListener('click', analyze);

  async function analyze(){
    const raw = $('#analyzeUrl').value;
    const url = normalizeUrl(raw);
    const status = $('#analyzeStatus'); const btn = $('#analyzeBtn'); const report = $('#analyzeReport');
    if (!url){ status.textContent = 'Please enter a URL.'; return; }
    status.textContent = 'Analyzing…'; btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Analyzing';

    try{
      const resp = await fetch('{{ route('analyze.json') }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({ url })
      });
      const data = await resp.json();
      if (!data.ok) throw new Error(data.error || 'Failed');

      // chips
      $('#rStatus').textContent = data.status;
      $('#rTitleLen').textContent = (data.title || '').length;
      $('#rMetaLen').textContent = data.meta_description_len;
      $('#rCanonical').textContent = data.canonical ? 'Yes' : 'No';
      $('#rRobots').textContent = data.robots || '—';
      $('#rViewport').textContent = data.viewport ? 'Yes' : 'No';
      $('#rHeadings').textContent = `${data.counts.h1}/${data.counts.h2}/${data.counts.h3}`;
      $('#rInternal').textContent = data.counts.internal_links;
      const types = (data.schema.found_types || []).slice(0,6).join(', ') || '—';
      $('#rSchema').textContent = types;
      $('#rAutoCount').textContent = (data.auto_check_ids||[]).length;
      report.style.display='block';

      // scores
      window.__lastSuggestions = data.suggestions || {};
      for (let i=1;i<=25;i++){ const key='ck-'+i; setScoreBadge(i, data.scores?.[key]); }

      // numeric scores
      const backendOverall = typeof data.overall_score === 'number' ? data.overall_score : 0;
      window.__setAnalyzedScore(backendOverall); // feeds blended wheel
      document.getElementById('contentScoreInline').textContent = window.__getContentScore();

      // AI/Human
      const ai = data.ai_detection || {};
      const badge = document.getElementById('aiBadge');
      const labelMap={likely_human:'Likely Human', mixed:'Mixed', likely_ai:'Likely AI'};
      const label = labelMap[ai.label] || 'Unknown';
      const conf = typeof ai.likelihood==='number' ? `(${ai.likelihood}%)` : '';
      const pct = typeof ai.ai_pct==='number' ? ` — ${ai.ai_pct}% AI‑like` : '';
      badge.innerHTML = `Writer: <b>${label} ${conf}${pct}</b>`;
      badge.title = (ai.reasons||[]).join(' • ');
      window.__setAIData(ai);

      // auto-check
      if ($('#autoApply').checked) {
        for (let i=1;i<=25;i++) setChecked('ck-'+i, false);
        (data.auto_check_ids||[]).forEach(id => setChecked(id, true));
        document.dispatchEvent(new Event('change'));
      }

      // status line
      const wheel = parseInt(document.getElementById('overallScoreInline').textContent||'0',10);
      status.textContent = wheel>=80 ? 'Great! You passed—keep going.' : (wheel<60 ? 'Score is low — optimize and re‑Analyze.' : 'Solid! Improve a few items to hit green.');
      setTimeout(()=> status.textContent = '', 4200);
    } catch(e){
      status.textContent = 'Error: '+e.message;
    } finally {
      btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Analyze';
    }
  }
})();
</script>

<!-- ========== Procedural Colorful Smoke (WebGL2 shader + 2D fallback) ========== -->
<script>
(function(){
  const canvas = document.getElementById('smokeFX');
  if (!canvas) return;

  const dpr = Math.min(2, window.devicePixelRatio || 1);
  let gl = null, vw=0, vh=0, start=performance.now(), raf=0;

  function resize(){
    vw = canvas.clientWidth  = window.innerWidth;
    vh = canvas.clientHeight = window.innerHeight;
    canvas.width  = Math.floor(vw * dpr);
    canvas.height = Math.floor(vh * dpr);
    if (gl) gl.viewport(0,0,canvas.width,canvas.height);
  }
  addEventListener('resize', resize, {passive:true});
  resize();

  const mouse = { x: vw*0.92, y: vh*0.88, t: 0 };
  addEventListener('mousemove', e=>{ mouse.x=e.clientX; mouse.y=e.clientY; mouse.t=performance.now(); }, {passive:true});

  try { gl = canvas.getContext('webgl2', { alpha:true, antialias:false, depth:false, stencil:false, powerPreference:'high-performance' }); } catch(e){}

  if (gl) {
    const vert = `#version 300 es
    precision highp float;
    const vec2 verts[3] = vec2[3](
      vec2(-1.0,-1.0), vec2(3.0,-1.0), vec2(-1.0,3.0)
    );
    out vec2 vUv;
    void main(){
      vec2 p = verts[gl_VertexID];
      vUv = 0.5*(p+1.0);
      gl_Position = vec4(p,0.0,1.0);
    }`;

    const frag = `#version 300 es
    precision highp float;
    in vec2 vUv; out vec4 fragColor;
    uniform vec2 u_res, u_mouse;
    uniform float u_time, u_aspect;

    float hash(vec2 p){ return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453); }
    float noise(vec2 p){
      vec2 i=floor(p), f=fract(p);
      float a=hash(i), b=hash(i+vec2(1,0)), c=hash(i+vec2(0,1)), d=hash(i+vec2(1,1));
      vec2 u=f*f*(3.0-2.0*f);
      return mix(a,b,u.x)+(c-a)*u.y*(1.0-u.x)+(d-b)*u.x*u.y;
    }
    float fbm(vec2 p){
      float v=0.0, a=0.5; mat2 m=mat2(1.6,1.2,-1.2,1.6);
      for(int i=0;i<5;i++){ v+=a*noise(p); p=m*p; a*=0.5; } return v;
    }
    vec2 curl(vec2 p){
      float e=0.003;
      float n1=fbm(p+vec2(0,e)), n2=fbm(p-vec2(0,e));
      float n3=fbm(p+vec2(e,0)), n4=fbm(p-vec2(e,0));
      float dx=(n1-n2)/(2.0*e), dy=(n3-n4)/(2.0*e);
      return vec2(dy,-dx);
    }
    float emitter(vec2 uv){
      vec2 pr=vec2(1.02,1.02);
      vec2 d=uv-pr;
      float r=length(d*vec2(u_aspect,1.0));
      float base=smoothstep(0.45,0.0,r);
      vec2 mm=u_mouse/u_res;
      float mr=distance(uv,mm);
      float mouseBoost=smoothstep(0.35,0.0,mr)*0.15;
      return clamp(base+mouseBoost,0.0,1.0);
    }
    vec3 palette(float t){
      vec3 c1=vec3(0.24,0.88,1.00); // cyan
      vec3 c2=vec3(0.61,0.36,1.00); // purple
      vec3 c3=vec3(1.00,0.71,0.28); // orange
      vec3 c4=vec3(0.21,0.77,0.45); // teal
      vec3 a=mix(c1,c2,smoothstep(0.0,0.33,t));
      vec3 b=mix(c3,c4,smoothstep(0.33,1.0,t));
      return mix(a,b,smoothstep(0.25,0.85,t));
    }
    void main(){
      vec2 uv=vUv;
      vec2 p=(uv-0.5)*vec2(u_aspect,1.0);
      float t=u_time*0.06;
      vec2 flow=curl(p*1.6+vec2(t*0.8,-t*0.6))*0.6;

      float d1=fbm(p*1.8+flow*1.2+vec2(t*0.9,-t*0.5));
      float d2=fbm(p*0.9-flow*0.6+vec2(-t*0.4,t*0.7));
      float density=smoothstep(0.35,0.95,d1*0.65+d2*0.45);

      float e=emitter(uv);
      density=clamp(density+e*0.85,0.0,1.0);

      float vign=smoothstep(1.25,0.15,length((uv-vec2(0.0))*vec2(u_aspect,1.0)));
      density*=vign;

      float hueBand=fbm(p*0.8+vec2(t*0.2,t*0.25));
      vec3 col=palette(hueBand);

      vec3 smoke=col*density*0.95;
      float fog=smoothstep(0.0,1.0,fbm(p*0.6+vec2(-t*0.25,t*0.18)))*0.12;
      smoke+=fog*col;

      float alpha=clamp(density*0.95,0.0,1.0);
      fragColor=vec4(smoke,alpha);
    }`;

    function compile(src, type){
      const s=gl.createShader(type);
      gl.shaderSource(s,src); gl.compileShader(s);
      if(!gl.getShaderParameter(s,gl.COMPILE_STATUS)){ console.warn(gl.getShaderInfoLog(s)); gl.deleteShader(s); return null; }
      return s;
    }
    function makeProgram(vsSrc, fsSrc){
      const vs=compile(vsSrc,gl.VERTEX_SHADER), fs=compile(fsSrc,gl.FRAGMENT_SHADER);
      const pr=gl.createProgram(); gl.attachShader(pr,vs); gl.attachShader(pr,fs); gl.linkProgram(pr);
      if(!gl.getProgramParameter(pr,gl.LINK_STATUS)){ console.warn(gl.getProgramInfoLog(pr)); return null; }
      gl.deleteShader(vs); gl.deleteShader(fs); return pr;
    }

    const prog = makeProgram(vert, frag);
    gl.useProgram(prog);
    const u_res=gl.getUniformLocation(prog,'u_res');
    const u_mouse=gl.getUniformLocation(prog,'u_mouse');
    const u_time=gl.getUniformLocation(prog,'u_time');
    const u_aspect=gl.getUniformLocation(prog,'u_aspect');

    function draw(now){
      const t=(now-start)*0.001;
      gl.viewport(0,0,canvas.width,canvas.height);
      gl.disable(gl.DEPTH_TEST); gl.disable(gl.BLEND);
      gl.clearColor(0,0,0,0); gl.clear(gl.COLOR_BUFFER_BIT);

      gl.useProgram(prog);
      gl.uniform2f(u_res, canvas.width, canvas.height);
      gl.uniform2f(u_mouse, mouse.x * dpr, (vh - mouse.y) * dpr);
      gl.uniform1f(u_time, t);
      gl.uniform1f(u_aspect, canvas.width / Math.max(1.0, canvas.height));

      gl.drawArrays(gl.TRIANGLES, 0, 3);
      raf = requestAnimationFrame(draw);
    }
    raf = requestAnimationFrame(draw);
    return;
  }

  // ---- 2D Fallback (if WebGL2 not supported) ----
  const ctx = canvas.getContext('2d');
  const N = 220;
  const parts = new Array(N).fill(0).map(()=>({
    x: vw - Math.random()*vw*0.25,
    y: vh - Math.random()*vh*0.25,
    r: 40 + Math.random()*80,
    a: 0.15 + Math.random()*0.25,
    hue: Math.random(),
    vx: -0.3 - Math.random()*0.4,
    vy: -0.2 - Math.random()*0.3,
  }));
  function hsl(h,s,l,a){ h=(h%1+1)%1; const c=(1-Math.abs(2*l-1))*s, x=c*(1-Math.abs((h*6)%2-1)), m=l-c/2; let r=0,g=0,b=0;
    if(h<1/6){r=c;g=x}else if(h<2/6){r=x;g=c}else if(h<3/6){g=c;b=x}else if(h<4/6){g=x;b=c}else if(h<5/6){r=x;b=c}else{r=c;b=x}
    return `rgba(${Math.round((r+m)*255)},${Math.round((g+m)*255)},${Math.round((b+m)*255)},${a})`; }
  function loop(){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    ctx.globalCompositeOperation='lighter';
    for(const p of parts){
      const dx=(vw*0.98 - p.x), dy=(vh*0.98 - p.y);
      p.vx += dx*0.00002; p.vy += dy*0.00002;
      p.x += p.vx; p.y += p.vy;
      if(p.x < -200) p.x = vw+100;
      if(p.y < -200) p.y = vh+100;
      const col = hsl(p.hue,0.9,0.6,p.a);
      const g = ctx.createRadialGradient(p.x,p.y,0,p.x,p.y,p.r);
      g.addColorStop(0,col); g.addColorStop(1,'rgba(0,0,0,0)');
      ctx.fillStyle=g; ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2); ctx.fill();
      p.hue += 0.0008; if(p.hue>1) p.hue-=1;
    }
    requestAnimationFrame(loop);
  }
  loop();
})();
</script>
</body>
</html>
