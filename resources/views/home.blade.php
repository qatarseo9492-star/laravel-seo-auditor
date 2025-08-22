{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-dir="ltr">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Semantic SEO Master • Ultra Tech Dark</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
:root{
  --bg:#07070e; --panel:#0f1022; --panel-2:#141433; --line:#1e1a33;
  --text:#f0effa; --text-dim:#b6b3d6; --text-muted:#9aa0c3;
  --primary:#9b5cff; --secondary:#ff2045; --accent:#3de2ff;
  --good:#16c172; --warn:#f59e0b; --bad:#ef4444;
  --radius:18px; --shadow:0 10px 40px rgba(0,0,0,.55);
  --container:1200px; --grad1:linear-gradient(135deg,#9b5cff,#ff2045);
}
*{box-sizing:border-box} html,body{height:100%}
body{
  margin:0; color:var(--text);
  font-family:Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto;
  background: radial-gradient(1200px 700px at 0% -10%, #1a0f38 0%, transparent 55%),
              radial-gradient(1100px 800px at 110% 0%, #120a22 0%, transparent 50%),
              var(--bg);
  overflow-x:hidden;
}

/* Dancing mouse lines background */
#mouseLines, #brainCanvas{position:fixed;inset:0;z-index:0;pointer-events:none}
#mouseLines{opacity:.22}
#brainCanvas{opacity:.13}

/* Purple + red smoke blobs */
.bg-smoke{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
.blob{position:absolute;width:60vmax;height:60vmax;border-radius:50%;filter:blur(80px);mix-blend-mode:screen;animation:float 36s linear infinite}
.blob.p{background:radial-gradient(closest-side,rgba(155,92,255,.35),rgba(155,92,255,0) 70%)}
.blob.r{background:radial-gradient(closest-side,rgba(255,32,69,.28),rgba(255,32,69,0) 70%)}
.b1{top:-18%;left:-15%}.b2{bottom:-22%;right:-10%;animation-direction:reverse;animation-duration:30s}
.b3{top:10%;right:15%;animation-duration:28s}.b4{bottom:10%;left:25%;animation-duration:40s}
@keyframes float{0%{transform:translate3d(0,0,0)}50%{transform:translate3d(-6%,7%,0)}100%{transform:translate3d(0,0,0)}}

.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 22px;border-bottom:1px solid var(--line);backdrop-filter:saturate(140%) blur(10px);background:rgba(15,16,34,.35)}
.brand{display:flex;align-items:center;gap:1rem}
.brand-badge{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(155,92,255,.3),rgba(255,32,69,.25));border:1px solid rgba(255,255,255,.08); color:#ffd1dc}

.hero-heading{
  font-size:4.2rem;font-weight:1000;line-height:1.02;margin:.1rem 0;
  letter-spacing:.8px; background:linear-gradient(90deg,#b892ff,#ff2045 55%,#ff8a5b 100%);
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  text-shadow:0 0 28px rgba(155,92,255,.25);
}

/* Buttons */
.btn{--pad:.75rem 1.05rem;display:inline-flex;align-items:center;gap:.5rem;padding:var(--pad);border-radius:14px;border:1px solid transparent;cursor:pointer;font-weight:800;letter-spacing:.2px;transition:.2s;user-select:none}
.btn-neon{background:linear-gradient(135deg,#3de2ff,#9b5cff);box-shadow:0 8px 30px rgba(61,226,255,.25);color:#001018}
.btn-neon:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(61,226,255,.35)}
.btn-ghost{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.16);color:#fff}
.btn-ghost:hover{background:rgba(255,255,255,.08);transform:translateY(-2px)}
.btn-danger{background:linear-gradient(135deg,#ff2045,#ff7a59);color:#fff;box-shadow:0 8px 30px rgba(255,32,69,.25)}
.btn-danger:hover{transform:translateY(-2px);box-shadow:0 12px 40px rgba(255,32,69,.35)}

/* Stylish URL card */
.url-card{margin-top:14px;padding:14px;border-radius:16px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);position:relative;overflow:hidden}
.url-card::before{content:"";position:absolute;inset:-1px;border-radius:18px;padding:1px;background:linear-gradient(120deg,rgba(61,226,255,.4),rgba(155,92,255,.4),rgba(255,32,69,.4));-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite:xor;mask-composite:exclude;animation:borderGlow 6s linear infinite}
@keyframes borderGlow{0%{filter:hue-rotate(0deg)}100%{filter:hue-rotate(360deg)}}
.analyze-form input[type="url"]{width:100%;padding:1rem 1.1rem;border-radius:14px;border:1px solid #1b1b35;background:#0b0d21;color:var(--text);outline:none;transition:.2s}
.analyze-form input[type="url"]:focus{box-shadow:0 0 0 3px rgba(61,226,255,.25), 0 10px 30px rgba(61,226,255,.15);border-color:#2a2f59}
.analyze-row{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;margin-top:.6rem}

/* Wheel */
.score-area{display:flex;gap:1.2rem;align-items:center;justify-content:flex-start;margin:.6rem 0 0}
.score-container{width:220px}
.score-wheel{width:100%;height:auto;transform:rotate(-90deg)}
.score-wheel circle{fill:none;stroke-width:14;stroke-linecap:round}
.score-wheel .bg{stroke:rgba(255,255,255,.12)}
.score-wheel .progress{stroke:url(#grad);stroke-dasharray:339;stroke-dashoffset:339;transition:stroke-dashoffset .6s ease,stroke .3s ease,filter .3s ease;filter:drop-shadow(0 0 10px rgba(155,92,255,.35))}
.score-text{font-size:3rem;font-weight:1000;fill:#fff;transform:rotate(90deg);text-shadow:0 0 18px rgba(255,32,69,.25)}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28)}
.legend{padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.16);font-weight:800}
.l-red{background:rgba(239,68,68,.18)} .l-orange{background:rgba(245,158,11,.18)} .l-green{background:rgba(34,197,94,.18)}

/* Category grid + colorful headers */
.analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
.category-card{position:relative;grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:16px;box-shadow:var(--shadow);overflow:hidden}
.category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center;margin-bottom:.25rem}
.category-icon{width:46px;height:46px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;color:#fff}
.icon-purple{background:linear-gradient(135deg,#7c4dff,#b892ff)}
.icon-cyan{background:linear-gradient(135deg,#00d4ff,#3de2ff)}
.icon-amber{background:linear-gradient(135deg,#f59e0b,#ffb74d)}
.icon-rose{background:linear-gradient(135deg,#ff416c,#ff4b2b)}
.icon-lime{background:linear-gradient(135deg,#84cc16,#22c55e)}
.icon-blue{background:linear-gradient(135deg,#2563eb,#60a5fa)}
.category-title{margin:0;font-size:1.05rem;letter-spacing:.3px}
.category-title span{background:linear-gradient(90deg,#b892ff,#ff7a59);-webkit-background-clip:text;-webkit-text-fill-color:transparent}
.category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}
.card-bar{height:3px;border-radius:10px;background:linear-gradient(90deg,var(--primary),var(--secondary));opacity:.75;margin-top:.4rem}

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

/* Progress */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
.progress-fill{height:100%;background:var(--grad1);width:0%;transition:width .35s ease}
.progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}

/* Modal */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);display:none;z-index:70}
.modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:80}
.modal-card{width:min(900px,94vw);background:var(--panel-2);border:1px solid rgba(255,255,255,.12);border-radius:18px;box-shadow:var(--shadow);padding:18px}
.modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem}
.modal-title{margin:0;font-size:1.2rem}
.modal-close{background:transparent;border:1px solid rgba(255,255,255,.2);border-radius:10px;color:#fff;padding:.35rem .6rem;cursor:pointer}
.tabs{display:flex;gap:.4rem;margin:.4rem 0}
.tab{padding:.35rem .7rem;border-radius:10px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.06);cursor:pointer;font-weight:800}
.tab.active{background:linear-gradient(135deg,#3de2ff22,#9b5cff22);border-color:#3de2ff66}
.tabpanes > div{display:none}
.tabpanes > div.active{display:block}
.pre{white-space:pre-wrap;background:#0b0d21;border:1px solid #1b1b35;border-radius:12px;padding:12px;color:#cfd3f6;max-height:60vh;overflow:auto}

/* Language dock (left) */
.lang-dock{position:fixed;left:12px;top:50%;transform:translateY(-50%);z-index:90}
.lang-toggle{width:46px;height:46px;border-radius:50%;display:grid;place-items:center;background:linear-gradient(135deg,#3de2ff,#9b5cff);box-shadow:0 8px 30px rgba(61,226,255,.25);border:1px solid rgba(255,255,255,.2);cursor:pointer}
.lang-panel{margin-top:8px;width:220px;max-height:65vh;overflow:auto;border-radius:14px;background:rgba(15,16,34,.9);border:1px solid rgba(255,255,255,.12);display:none;padding:8px}
.lang-item{display:flex;align-items:center;gap:.5rem;padding:.4rem .5rem;border-radius:10px;cursor:pointer}
.lang-item:hover{background:rgba(255,255,255,.08)}
.lang-item.active{background:linear-gradient(135deg,#3de2ff22,#9b5cff22);border:1px solid #3de2ff66}

/* Back to top */
#backTop{position:fixed;right:18px;bottom:22px;z-index:95;display:none}
#backTop .btn{border-radius:50%;width:48px;height:48px;padding:0;justify-content:center}

/* Footer (professional bar) */
footer.site{
  margin-top:28px; position:relative; z-index:2;
  background:rgba(15,16,34,.6); border-top:1px solid rgba(255,255,255,.08);
}
.footer-inner{
  max-width:var(--container); margin:0 auto; padding:14px 5%;
  display:flex; align-items:center; justify-content:space-between; gap:1rem;
}
.footer-brand{display:flex;align-items:center;gap:.6rem}
.footer-brand .dot{width:10px;height:10px;border-radius:50%;background:linear-gradient(135deg,#3de2ff,#9b5cff);box-shadow:0 0 14px rgba(61,226,255,.6)}
.footer-links{display:flex;gap:1rem;flex-wrap:wrap}
.footer-links a{color:var(--text-dim)} .footer-links a:hover{color:#fff}

@media (max-width:992px){ .category-card{grid-column:span 12} .hero-heading{font-size:2.7rem} .score-container{width:190px} }
@media print{#mouseLines,#brainCanvas,.bg-smoke,.modal-backdrop,.modal,header.site,.lang-dock,#backTop{display:none!important}}
</style>
</head>
<body>
<canvas id="mouseLines"></canvas>
<canvas id="brainCanvas"></canvas>
<div class="bg-smoke">
  <span class="blob p b1"></span><span class="blob r b2"></span>
  <span class="blob p b3"></span><span class="blob r b4"></span>
</div>

<!-- gradients -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#9b5cff"/><stop offset="100%" stop-color="#ff2045"/>
    </linearGradient>
    <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/>
    </linearGradient>
    <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/>
    </linearGradient>
  </defs>
</svg>

<!-- Language Dock -->
<aside class="lang-dock" aria-label="Language Switcher">
  <div class="lang-toggle" id="langToggle" title="Change language">
    <i class="fa-solid fa-globe"></i>
  </div>
  <div class="lang-panel" id="langPanel">
    <!-- items injected by JS -->
  </div>
</aside>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
      <div>
        <div class="hero-heading" data-i18n="title">Semantic SEO Master Analyzer</div>
      </div>
    </div>
    <div style="display:flex;gap:.5rem">
      <button class="btn btn-ghost" id="printTop"><i class="fa-solid fa-print"></i> <span data-i18n="print">Print</span></button>
    </div>
  </header>

  <section class="analyzer" id="analyzer">
    <h2 class="section-title" data-i18n="analyzeTitle">Analyze a URL</h2>
    <p class="section-subtitle" data-i18n="legendText">The wheel fills with your overall score. <span class="legend l-green">Green ≥ 80</span> <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Red &lt; 60</span></p>

    <div class="score-area">
      <div class="score-container">
        <svg class="score-wheel" viewBox="0 0 120 120" aria-label="Overall score">
          <circle class="bg" cx="60" cy="60" r="54"/>
          <circle class="progress" cx="60" cy="60" r="54"/>
          <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="score-text" id="overallScore">0</text>
        </svg>
      </div>
      <div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <span class="chip"><span data-i18n="overall">Overall</span>: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge">Writer: <b>—</b></span>
          <button id="viewAIText" class="btn btn-neon" style="--pad:.5rem .8rem"><i class="fa-solid fa-robot"></i> <span data-i18n="viewAiText">View AI‑like text</span></button>
        </div>
      </div>
    </div>

    <!-- Stylish URL Card -->
    <div class="url-card">
      <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
        <label for="analyzeUrl" style="display:block;font-weight:800;margin-bottom:.35rem" data-i18n="pageUrl">Page URL</label>
        <input id="analyzeUrl" type="url" required placeholder="https://example.com/page">
        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> <span data-i18n="autoApply">Auto‑apply checkmarks (≥ 70)</span>
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-danger"><i class="fa-solid fa-magnifying-glass"></i> <span data-i18n="analyze">Analyze</span></button>
          <button class="btn btn-neon" id="printChecklist"><i class="fa-solid fa-print"></i> <span data-i18n="print">Print</span></button>
          <button class="btn btn-ghost" id="resetChecklist"><i class="fa-solid fa-rotate"></i> <span data-i18n="reset">Reset</span></button>
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
      <div style="display:flex;align-items:center;justify-content:space-between;gap:.6rem;flex-wrap:wrap">
        <span class="chip" id="progressPercent">0%</span>
        <span><span data-i18n="overall">Overall</span> Score: <b id="overallScoreChip">0</b></span>
      </div>
      <div class="progress-bar"><div class="progress-fill" id="progressBar"></div></div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    <!-- Category grid with colorful icons + bars -->
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
        ['Content & Keywords','Intent‑aligned, discoverable content','fa-pen-nib','icon-purple',1,5],
        ['Technical Elements','Crawlability & SERP‑readiness','fa-code','icon-cyan',6,9],
        ['Content Quality','Credible, current, uniquely useful','fa-star','icon-amber',10,13],
        ['Structure & Architecture','Clear hierarchy and clusters','fa-sitemap','icon-rose',14,17],
        ['User Signals & Experience','Better signals through UX','fa-user-check','icon-lime',18,21],
        ['Entities & Context','Knowledge‑aligned content','fa-database','icon-blue',22,25],
      ] as $c)
        <article class="category-card" data-category="{{ Str::slug($c[0]) }}">
          <header class="category-head">
            <span class="category-icon {{ $c[3] }}"><i class="fas {{ $c[2] }}"></i></span>
            <div>
              <h3 class="category-title"><span>{{ $c[0] }}</span></h3>
              <p class="category-sub">{{ $c[1] }}</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">{{ $c[5]-$c[4]+1 }}</span></span>
          </header>
          <div class="card-bar"></div>
          <ul class="checklist">
            @for($i=$c[4];$i<=$c[5];$i++)
              <li class="checklist-item">
                <label>
                  <input type="checkbox" id="ck-{{ $i }}" data-category="{{ Str::slug($c[0]) }}">
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

<!-- Back to top -->
<div id="backTop"><button class="btn btn-neon" title="Back to top"><i class="fa-solid fa-arrow-up"></i></button></div>

<!-- Modal (Improve + AI text + Google examples) -->
<div class="modal-backdrop" id="modalBackdrop"></div>
<div class="modal" id="tipModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle">Improve</h3>
      <button class="modal-close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="tabs">
      <button class="tab active" data-tab="tipsTab"><i class="fa-solid fa-lightbulb"></i> Tips</button>
      <button class="tab" data-tab="examplesTab"><i class="fa-brands fa-google"></i> Google Examples</button>
      <button class="tab" data-tab="aiTab"><i class="fa-solid fa-robot"></i> AI‑like Snippets</button>
      <button class="tab" data-tab="fullTab"><i class="fa-solid fa-file-lines"></i> Full Text</button>
    </div>
    <div class="tabpanes">
      <div id="tipsTab" class="active"><ul id="modalList"></ul></div>
      <div id="examplesTab"><div class="pre" id="examplesPre">Analyze a URL to see SERP query ideas.</div></div>
      <div id="aiTab"><div class="pre" id="aiSnippetsPre">Run Analyze to view AI‑like snippets.</div></div>
      <div id="fullTab"><div class="pre" id="fullTextPre">Run Analyze to load full text.</div></div>
    </div>
  </div>
</div>

<!-- Pass/Fail FX from previous version -->
<canvas id="flowerFX"></canvas>
<div id="sadFX" style="display:none;align-items:center;justify-content:center;position:fixed;inset:0;z-index:85"><i class="fa-solid fa-face-frown-open" style="font-size:70px;color:#ff7a7a;text-shadow:0 6px 30px rgba(255,0,0,.35)"></i></div>

<script>
/* Dancing Mouse Lines */
(function(){
  const c = document.getElementById('mouseLines'); const ctx = c.getContext('2d');
  let w,h,pts=[],mouse={x:innerWidth/2,y:innerHeight/2};
  function resize(){w=c.width=innerWidth; h=c.height=innerHeight; pts = Array.from({length:60},()=>({x:Math.random()*w,y:Math.random()*h,vx:(Math.random()-.5)*.6,vy:(Math.random()-.5)*.6}))}
  addEventListener('resize',resize,{passive:true}); resize();
  addEventListener('pointermove', e=>{ mouse.x = e.clientX; mouse.y = e.clientY; }, {passive:true});
  function step(){
    ctx.clearRect(0,0,w,h);
    for(const p of pts){
      const dx = mouse.x - p.x, dy = mouse.y - p.y; const d = Math.hypot(dx,dy) || 1;
      const force = Math.min(180, 14000/(d*d));
      p.vx += (dx/d)*force*0.002; p.vy += (dy/d)*force*0.002;
      p.vx*=0.97; p.vy*=0.97; p.x+=p.vx; p.y+=p.vy;
      if(p.x<0||p.x>w) p.vx*=-1; if(p.y<0||p.y>h) p.vy*=-1;
    }
    ctx.lineWidth = 1.2;
    for(let i=0;i<pts.length;i++){
      const a=pts[i];
      for(let j=i+1;j<pts.length;j++){
        const b=pts[j]; const dx=a.x-b.x, dy=a.y-b.y; const d=Math.hypot(dx,dy);
        if(d<130){
          const alpha=(1-d/130)*0.7;
          const hue = (260 + (a.x/w)*100) % 360;
          ctx.strokeStyle = `hsla(${hue},90%,70%,${alpha})`;
          ctx.beginPath();
          const mx=(a.x+b.x)/2, my=(a.y+b.y)/2;
          ctx.moveTo(a.x,a.y);
          ctx.quadraticCurveTo(mx+(mouse.x-mx)*0.05, my+(mouse.y-my)*0.05, b.x,b.y);
          ctx.stroke();
        }
      }
    }
    requestAnimationFrame(step);
  }
  step();
})();

/* Brain network (subtle) */
(function(){
  const c = document.getElementById('brainCanvas'); const ctx = c.getContext('2d');
  let w,h,pts=[]; function resize(){w=c.width=innerWidth; h=c.height=innerHeight; pts=Array.from({length:70},()=>({x:Math.random()*w,y:Math.random()*h,vx:(Math.random()-.5)*.3,vy:(Math.random()-.5)*.3}))}
  addEventListener('resize',resize,{passive:true}); resize();
  function step(){ctx.clearRect(0,0,w,h); for(const p of pts){p.x+=p.vx; p.y+=p.vy; if(p.x<0||p.x>w) p.vx*=-1; if(p.y<0||p.y>h) p.vy*=-1;}
    for(let i=0;i<pts.length;i++){for(let j=i+1;j<pts.length;j++){const a=pts[i],b=pts[j]; const d=Math.hypot(a.x-b.x,a.y-b.y); if(d<140){const alpha=(1-d/140)*0.4; ctx.strokeStyle=`rgba(157,92,255,${alpha})`; ctx.beginPath(); ctx.moveTo(a.x,a.y); ctx.lineTo(b.x,b.y); ctx.stroke();}}}
    requestAnimationFrame(step);
  } step();
})();

/* Score wheel + pass/fail */
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
  document.getElementById('overallScoreChip').textContent = Math.round(v);
}
setScoreWheel(0);

/* Confetti flowers + sad */
(function(){
  const canvas = document.getElementById('flowerFX'); const ctx = canvas.getContext('2d');
  function resize(){canvas.width=innerWidth; canvas.height=innerHeight;}
  addEventListener('resize',resize,{passive:true}); resize();
  let petals=[];
  function burst(){
    petals = Array.from({length:140},()=>({
      x:innerWidth/2,y:innerHeight/3,
      vx:(Math.random()-0.5)*6, vy:(Math.random()-0.8)*7-2,
      r: 8+Math.random()*8, rot: Math.random()*Math.PI, vr:(Math.random()-.5)*.2,
      hue: Math.random()<.5? 330 + Math.random()*30 : 290 + Math.random()*40
    }));
    canvas.style.display='block';
    loop();
    setTimeout(()=> canvas.style.display='none', 1800);
  }
  function petal(x,y,r,rot,hue){
    ctx.save(); ctx.translate(x,y); ctx.rotate(rot);
    const g = ctx.createLinearGradient(-r,0,r,0); g.addColorStop(0,`hsla(${hue},90%,70%,.9)`); g.addColorStop(1,`hsla(${hue-20},90%,60%,.9)`);
    ctx.fillStyle=g; ctx.beginPath(); ctx.moveTo(0,0); ctx.bezierCurveTo(-r,-r, r,-r, 0,0); ctx.bezierCurveTo(-r,r, r,r, 0,0); ctx.fill(); ctx.restore();
  }
  let raf; function loop(){
    cancelAnimationFrame(raf);
    ctx.clearRect(0,0,canvas.width,canvas.height);
    petals.forEach(p=>{
      p.x+=p.vx; p.y+=p.vy; p.vy+=0.12; p.rot+=p.vr;
      petal(p.x,p.y,p.r,p.rot,p.hue);
    });
    raf=requestAnimationFrame(loop);
  }
  window.__celebrate = burst;
})();
function showSad(on){ const el = document.getElementById('sadFX'); el.style.display = on ? 'flex' : 'none'; setTimeout(()=> el.style.display='none', 1600); }

/* Checklist progress + localStorage + blending */
(function () {
  const STORAGE_KEY = 'semanticSeoChecklistV4';
  const totalItems = 25;
  const boxes = () => Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
  const progressBar = document.getElementById('progressBar');
  const percent = document.getElementById('progressPercent');
  const caption = document.getElementById('progressCaption');
  let lastAnalyzed = 0;

  function blended(){ const checked = boxes().filter(cb=>cb.checked).length; const pct = (checked/totalItems)*100; return (lastAnalyzed*0.7)+(pct*0.3); }
  function updateCats(){
    document.querySelectorAll('.category-card').forEach(card=>{
      const cat = card.getAttribute('data-category');
      const all = card.querySelectorAll('input[data-category="'+cat+'"]');
      const done = card.querySelectorAll('input[data-category="'+cat+'"]:checked');
      card.querySelector('.checked-count').textContent = done.length;
      card.querySelector('.total-count').textContent = all.length;
    });
  }
  function update(){
    const checked = boxes().filter(cb=>cb.checked).length;
    const pct = Math.round((checked/totalItems)*100);
    progressBar.style.width = pct+'%';
    percent.textContent = pct+'%';
    caption.textContent = checked+' of '+totalItems+' items completed';
    updateCats();
    setScoreWheel(blended());
  }
  function load(){ try{ const saved = JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); boxes().forEach(cb=> cb.checked = saved.includes(cb.id)); }catch(e){} update(); }
  function save(){ const ids = boxes().filter(cb=>cb.checked).map(cb=>cb.id); localStorage.setItem(STORAGE_KEY, JSON.stringify(ids)); }

  document.addEventListener('change', (e)=>{ if(e.target.matches('#analyzer input[type="checkbox"]')){ update(); save(); }});
  document.getElementById('resetChecklist').addEventListener('click', ()=>{ if(!confirm('Reset the checklist?')) return; localStorage.removeItem(STORAGE_KEY); boxes().forEach(cb=>cb.checked=false); for(let i=1;i<=25;i++){ setScoreBadge(i,null); } lastAnalyzed=0; setScoreWheel(0); update(); });
  document.getElementById('printChecklist').addEventListener('click', ()=> window.print());
  document.getElementById('printTop').addEventListener('click', ()=> window.print());
  window.setScoreBadge = (num,score)=>{ const el=document.getElementById('sc-'+num); if(!el) return; el.className='score-badge'; if(score==null){el.textContent='—';return;} el.textContent=score; if(score>=80) el.classList.add('score-good'); else if(score>=60) el.classList.add('score-mid'); else el.classList.add('score-bad'); };
  window.__setAnalyzedScore = function(v){ lastAnalyzed = Math.max(0, Math.min(100, +v||0)); setScoreWheel(blended()); if (lastAnalyzed>=80) { window.__celebrate(); } if (lastAnalyzed<60) { showSad(true); } }
  load();
})();

/* Modal + Improve + Google examples + AI text */
(function(){
  const $ = s=>document.querySelector(s);
  const $$ = s=>Array.from(document.querySelectorAll(s));
  const backdrop = $('#modalBackdrop');
  const modal = $('#tipModal');
  const closeBtn = $('#modalClose');
  const title = $('#modalTitle');
  const tipsList = $('#modalList');
  const tabs = $$('.tab');
  const panes = { tipsTab: $('#tipsTab'), aiTab: $('#aiTab'), fullTab: $('#fullTab'), examplesTab: $('#examplesTab') };

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

  // Improve button -> open Tips tab + inject examples
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.improve-btn');
    if (!btn) return;
    const id = btn.getAttribute('data-id');
    title.textContent = 'Improve: '+labelFor(id);
    tipsList.innerHTML = '';
    const tips = (window.__lastSuggestions && window.__lastSuggestions[id]) ? window.__lastSuggestions[id] : ['Analyze the URL first to generate suggestions.'];
    tips.forEach(t=>{ const li=document.createElement('li'); li.textContent=t; tipsList.appendChild(li); });
    // Examples
    $('#examplesPre').textContent = (window.__exampleQueries && window.__exampleQueries[id]) ? window.__exampleQueries[id].join('\n') : 'Analyze a URL to see SERP query ideas.';
    // default tab
    tabs.forEach(x=>x.classList.remove('active')); tabs[0].classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.tipsTab.classList.add('active');
    openModal();
  });

  document.getElementById('viewAIText').addEventListener('click', ()=>{
    title.textContent = 'AI‑like Content Detection';
    tipsList.innerHTML = '';
    tabs.forEach(x=>x.classList.remove('active')); tabs[2].classList.add('active'); // AI tab index
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.aiTab.classList.add('active');
    openModal();
  });

  // expose setters
  window.__setAIData = function(ai){
    const sn = ai?.ai_snippets || [];
    document.getElementById('aiSnippetsPre').textContent = sn.length ? sn.join('\n\n') : 'No AI‑like snippets detected.';
    document.getElementById('fullTextPre').textContent = ai?.full_text || 'No text captured.';
  }
})();
</script>

<script>
/* Analyze handler + Google example queries + back-to-top + language switcher */
(function(){
  const $ = s => document.querySelector(s);
  const setChecked = (id, on) => { const el = document.getElementById(id); if (el) el.checked = !!on; };

  // language i18n
  const i18n = {
    en: { title:'Semantic SEO Master Analyzer', print:'Print', analyzeTitle:'Analyze a URL', legendText:'The wheel fills with your overall score. <span class="legend l-green">Green ≥ 80</span> <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Red &lt; 60</span>', overall:'Overall', viewAiText:'View AI‑like text', pageUrl:'Page URL', analyze:'Analyze', reset:'Reset', autoApply:'Auto‑apply checkmarks (≥ 70)' },
    es: { title:'Analizador Maestro de SEO Semántico', print:'Imprimir', analyzeTitle:'Analizar una URL', legendText:'La rueda se llena con tu puntuación total. <span class="legend l-green">Verde ≥ 80</span> <span class="legend l-orange">Naranja 60–79</span> <span class="legend l-red">Rojo &lt; 60</span>', overall:'Total', viewAiText:'Ver texto tipo IA', pageUrl:'URL de la página', analyze:'Analizar', reset:'Restablecer', autoApply:'Auto‑marcar (≥ 70)' },
    fr: { title:'Analyseur Maître SEO Sémantique', print:'Imprimer', analyzeTitle:'Analyser une URL', legendText:'La roue se remplit avec votre score global. <span class="legend l-green">Vert ≥ 80</span> <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Rouge &lt; 60</span>', overall:'Global', viewAiText:'Voir texte IA', pageUrl:'URL de la page', analyze:'Analyser', reset:'Réinitialiser', autoApply:'Cochage auto (≥ 70)' },
    de: { title:'Semantischer SEO Master-Analyzer', print:'Drucken', analyzeTitle:'URL analysieren', legendText:'Das Rad füllt sich mit deinem Gesamtscore. <span class="legend l-green">Grün ≥ 80</span> <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Rot &lt; 60</span>', overall:'Gesamt', viewAiText:'KI‑ähnlichen Text', pageUrl:'Seiten‑URL', analyze:'Analysieren', reset:'Zurücksetzen', autoApply:'Auto‑Häkchen (≥ 70)' },
    it: { title:'Analizzatore Maestro SEO Semantico', print:'Stampa', analyzeTitle:'Analizza un URL', legendText:'La ruota si riempie con il punteggio totale. <span class="legend l-green">Verde ≥ 80</span> <span class="legend l-orange">Arancione 60–79</span> <span class="legend l-red">Rosso &lt; 60</span>', overall:'Totale', viewAiText:'Vedi testo IA', pageUrl:'URL della pagina', analyze:'Analizza', reset:'Reimposta', autoApply:'Spunta auto (≥ 70)' },
    pt: { title:'Analisador Mestre de SEO Semântico', print:'Imprimir', analyzeTitle:'Analisar uma URL', legendText:'A roda preenche com sua pontuação geral. <span class="legend l-green">Verde ≥ 80</span> <span class="legend l-orange">Laranja 60–79</span> <span class="legend l-red">Vermelho &lt; 60</span>', overall:'Geral', viewAiText:'Ver texto IA', pageUrl:'URL da página', analyze:'Analisar', reset:'Redefinir', autoApply:'Marcação automática (≥ 70)' },
    ar: { title:'محلل السيو الدلالي المتقدم', print:'طباعة', analyzeTitle:'حلّل رابطًا', legendText:'تمتلئ العجلة بالنتيجة الإجمالية. <span class="legend l-green">أخضر ≥ 80</span> <span class="legend l-orange">برتقالي 60–79</span> <span class="legend l-red">أحمر &lt; 60</span>', overall:'الإجمالي', viewAiText:'عرض نص شبيه بالذكاء الاصطناعي', pageUrl:'عنوان الصفحة', analyze:'تحليل', reset:'إعادة ضبط', autoApply:'وضع العلامات تلقائيًا (≥ 70)' },
    hi: { title:'सेमांटिक SEO मास्टर एनालाइज़र', print:'प्रिंट', analyzeTitle:'URL का विश्लेषण करें', legendText:'पहिया आपके कुल स्कोर से भरता है. <span class="legend l-green">हरा ≥ 80</span> <span class="legend l-orange">नारंगी 60–79</span> <span class="legend l-red">लाल &lt; 60</span>', overall:'कुल', viewAiText:'AI‑जैसा टेक्स्ट', pageUrl:'पेज URL', analyze:'विश्लेषण', reset:'रीसेट', autoApply:'ऑटो‑चेक (≥ 70)' },
    ur: { title:'سیمنٹک SEO ماسٹر اینالائزر', print:'پرنٹ', analyzeTitle:'URL کا تجزیہ کریں', legendText:'وہیل آپ کے مجموعی اسکور سے بھر جاتی ہے۔ <span class="legend l-green">سبز ≥ 80</span> <span class="legend l-orange">نارنجی 60–79</span> <span class="legend l-red">سرخ &lt; 60</span>', overall:'مجموعی', viewAiText:'AI جیسا متن', pageUrl:'پیج URL', analyze:'تجزیہ', reset:'ری سیٹ', autoApply:'خودکار چیک (≥ 70)' },
    zh: { title:'语义SEO大师分析器', print:'打印', analyzeTitle:'分析 URL', legendText:'轮盘会根据整体分数填充。<span class="legend l-green">绿 ≥ 80</span> <span class="legend l-orange">橙 60–79</span> <span class="legend l-red">红 &lt; 60</span>', overall:'总分', viewAiText:'查看类AI文本', pageUrl:'页面 URL', analyze:'分析', reset:'重置', autoApply:'自动勾选 (≥ 70)' },
    tr: { title:'Anlamsal SEO Usta Analizör', print:'Yazdır', analyzeTitle:'Bir URL analiz et', legendText:'Teker genel puanla dolar. <span class="legend l-green">Yeşil ≥ 80</span> <span class="legend l-orange">Turuncu 60–79</span> <span class="legend l-red">Kırmızı &lt; 60</span>', overall:'Genel', viewAiText:'Yapay zekâ benzeri metin', pageUrl:'Sayfa URL’si', analyze:'Analiz Et', reset:'Sıfırla', autoApply:'Otomatik işaretle (≥ 70)' }
  };
  const langs = [
    ['en','English'],['es','Español'],['fr','Français'],['de','Deutsch'],['it','Italiano'],
    ['pt','Português'],['ar','العربية'],['hi','हिन्दी'],['ur','اردو'],['zh','中文'],['tr','Türkçe']
  ];

  function applyLang(code){
    const t = i18n[code] || i18n.en;
    document.querySelector('[data-i18n="title"]').innerHTML = t.title;
    document.querySelector('[data-i18n="print"]').textContent = t.print;
    document.querySelector('[data-i18n="analyzeTitle"]').textContent = t.analyzeTitle;
    document.querySelector('[data-i18n="legendText"]').innerHTML = t.legendText;
    document.querySelectorAll('[data-i18n="overall"]').forEach(el=> el.textContent = t.overall);
    document.querySelector('[data-i18n="viewAiText"]').textContent = t.viewAiText;
    document.querySelector('[data-i18n="pageUrl"]').textContent = t.pageUrl;
    document.querySelector('[data-i18n="analyze"]').textContent = t.analyze;
    document.querySelector('[data-i18n="reset"]').textContent = t.reset;
    document.querySelector('[data-i18n="autoApply"]').textContent = t.autoApply;
    // RTL handling
    const isRTL = (code==='ar' || code==='ur');
    document.documentElement.setAttribute('dir', isRTL ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', code);
  }

  // Build language panel
  (function buildLangDock(){
    const panel = document.getElementById('langPanel');
    langs.forEach(([code,label])=>{
      const div = document.createElement('div');
      div.className = 'lang-item'; div.dataset.code = code; div.innerHTML = `<i class="fa-solid fa-language"></i> ${label}`;
      panel.appendChild(div);
    });
    const setActive = (code)=>{ panel.querySelectorAll('.lang-item').forEach(x=>x.classList.toggle('active', x.dataset.code===code)); };
    let open=false; document.getElementById('langToggle').addEventListener('click', ()=>{ open=!open; panel.style.display = open ? 'block':'none'; });
    panel.addEventListener('click', (e)=>{
      const item = e.target.closest('.lang-item'); if (!item) return;
      applyLang(item.dataset.code); setActive(item.dataset.code);
    });
    // default
    applyLang('en'); setActive('en');
  })();

  // Back to top
  const back = document.getElementById('backTop');
  addEventListener('scroll', ()=>{ back.style.display = window.scrollY > 600 ? 'block' : 'none'; }, {passive:true});
  back.addEventListener('click', ()=> window.scrollTo({top:0, behavior:'smooth'}));

  // Analyze
  document.getElementById('analyzeBtn').addEventListener('click', analyze);
  async function analyze(){
    const url = $('#analyzeUrl').value.trim(); const status = $('#analyzeStatus'); const btn = $('#analyzeBtn'); const report = $('#analyzeReport');
    if (!url) return;
    status.textContent = 'Analyzing…'; btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ' + (document.querySelector('[data-i18n="analyze"]').textContent || 'Analyze');

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

      // wheel base score
      const overall = typeof data.overall_score === 'number' ? data.overall_score : 0;
      window.__setAnalyzedScore(overall);

      // AI badge
      const ai = data.ai_detection || {};
      const badge = document.getElementById('aiBadge');
      const labelMap={likely_human:'Likely Human', mixed:'Mixed', likely_ai:'Likely AI'};
      const label = labelMap[ai.label] || 'Unknown';
      const conf = typeof ai.likelihood==='number' ? `(${ai.likelihood}%)` : '';
      const pct = typeof ai.ai_pct==='number' ? ` — ${ai.ai_pct}% AI‑like` : '';
      badge.innerHTML = `Writer: <b>${label} ${conf}${pct}</b>`;
      if (ai.label==='likely_ai') badge.style.background='rgba(239,68,68,.22)';
      else if (ai.label==='mixed') badge.style.background='rgba(245,158,11,.22)';
      else badge.style.background='rgba(22,193,114,.22)';
      badge.title = (ai.reasons||[]).join(' • ');
      window.__setAIData(ai);

      // auto-check
      if ($('#autoApply').checked) {
        for (let i=1;i<=25;i++) setChecked('ck-'+i, false);
        (data.auto_check_ids||[]).forEach(id => setChecked(id, true));
        document.dispatchEvent(new Event('change')); // progress + wheel
      }

      // Build Google example queries (no external calls; just smart templates)
      const topic = (data.title || '').split('|')[0].trim() || 'your topic';
      const kw = topic.toLowerCase().split(' ').slice(0,5).join(' ');
      const makeEx = (id) => {
        const exBase = [
          `people also ask ${kw}`,
          `site:.gov ${kw} statistics 2025`,
          `${kw} vs alternative`,
          `how to ${kw}`,
          `${kw} price OR cost`,
          `intitle:${kw} filetype:pdf`
        ];
        switch(id){
          case 'ck-2': return [`${kw} synonyms`, `${kw} related terms`, `${kw} "people also ask"`, `${kw} topic cluster`];
          case 'ck-4': return [`${kw} questions`, `${kw} FAQ`, `${kw} common issues`, `"why" "${kw}"`];
          case 'ck-11': return [`${kw} case study`, `${kw} dataset`, `${kw} examples`, `${kw} template`];
          case 'ck-12': return [`site:wikipedia.org ${kw}`, `site:who.int ${kw}`, `site:nih.gov ${kw}`, `${kw} 2025 report`];
          case 'ck-15': return [`site:yourdomain.com ${kw}`, `${kw} related topic site:yourdomain.com`];
          case 'ck-20': return [`pagespeed insights ${data.url}`, `lighthouse ${data.url}`];
          default: return exBase;
        }
      };
      window.__exampleQueries = {};
      for (let i=1;i<=25;i++){ window.__exampleQueries['ck-'+i] = makeEx('ck-'+i); }

      // pass/fail text
      if (overall >= 80) {
        $('#analyzeStatus').innerHTML = '<i class="fa-solid fa-champagne-glasses"></i> Great! You passed. Enjoy the flowers and keep it up!';
      } else if (overall < 60) {
        $('#analyzeStatus').innerHTML = '<i class="fa-solid fa-face-sad-tear"></i> Score is low — optimize the content and re‑Analyze.';
      } else {
        $('#analyzeStatus').textContent = 'Solid! Improve the suggestions to push into green.';
      }
      setTimeout(()=> $('#analyzeStatus').textContent = '', 4200);
    } catch(e){
      $('#analyzeStatus').textContent = 'Error: '+e.message;
    } finally {
      btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> ' + (document.querySelector('[data-i18n="analyze"]').textContent || 'Analyze');
    }
  }
})();
</script>

<footer class="site">
  <div class="footer-inner">
    <div class="footer-brand"><span class="dot"></span><strong>Semantic SEO Master</strong></div>
    <nav class="footer-links">
      <a href="#" onclick="window.scrollTo({top:0,behavior:'smooth'})">Back to top</a>
      <a href="#">Privacy</a>
      <a href="#">Terms</a>
      <a href="#">Support</a>
    </nav>
  </div>
</footer>
</body>
</html>
