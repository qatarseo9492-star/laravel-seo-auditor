{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Semantic SEO Master • Ultra Tech Global</title>

<!-- Favicon (optional; remove if not present in /public) -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">

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

/* ---------- Lines canvases (bottom) ---------- */
#linesCanvas, #linesCanvas2 { position:fixed; inset:0; z-index:0; pointer-events:none; }

/* ---------- SMOKE + CLOUDS (visible layer above lines, below content) ---------- */
.bg-smoke{
  position:fixed; inset:0; z-index:1;
  pointer-events:none; overflow:hidden;
  will-change:transform, opacity, filter;
}
.blob{
  position:absolute; border-radius:50%;
  filter:blur(95px); mix-blend-mode:screen;
  animation:float 36s linear infinite;
  will-change:transform, filter, opacity;
}
/* multi‑color blobs */
.blob.cyan   { background:radial-gradient(closest-side,rgba(61,226,255,.34),rgba(61,226,255,0) 70%) }
.blob.purple { background:radial-gradient(closest-side,rgba(155,92,255,.38),rgba(155,92,255,0) 70%) }
.blob.red    { background:radial-gradient(closest-side,rgba(255,32,69,.30), rgba(255,32,69,0) 70%) }
.blob.orange { background:radial-gradient(closest-side,rgba(255,182,72,.30),rgba(255,182,72,0) 70%) }
.blob.teal   { background:radial-gradient(closest-side,rgba(34,197,94,.30), rgba(34,197,94,0) 70%) }
/* positions/sizes */
.b1{top:-18%;left:-15%;width:60vmax;height:60vmax}
.b2{bottom:-22%;right:-10%;width:62vmax;height:62vmax;animation-direction:reverse;animation-duration:30s}
.b3{top:10%;right:15%;width:50vmax;height:50vmax;animation-duration:28s}
.b4{bottom:12%;left:25%;width:48vmax;height:48vmax;animation-duration:40s}
.b5{bottom:0%;right:0%;width:40vmax;height:40vmax;animation-duration:52s}

.clouds { position:absolute; right:-6vmax; bottom:-6vmax; width:80vmax; height:60vmax; pointer-events:none; }
.clouds .c { position:absolute; border-radius:50%; filter:blur(42px); opacity:.95; mix-blend-mode:screen; }
.clouds .cyan   { background:radial-gradient(closest-side, rgba(61,226,255,.9),  rgba(61,226,255,0) 75%); }
.clouds .purple { background:radial-gradient(closest-side, rgba(155,92,255,.85), rgba(155,92,255,0) 75%); }
.clouds .red    { background:radial-gradient(closest-side, rgba(255,32,69,.80),  rgba(255,32,69,0) 75%); }
.clouds .orange { background:radial-gradient(closest-side, rgba(255,182,72,.85), rgba(255,182,72,0) 75%); }
.clouds .teal   { background:radial-gradient(closest-side, rgba(34,197,94,.82),  rgba(34,197,94,0) 75%); }
.c1{ width:50vmax;height:28vmax; right:0;     bottom:0;   animation:cloud 40s ease-in-out infinite; }
.c2{ width:46vmax;height:26vmax; right:6vmax; bottom:2vmax; animation:cloud 46s ease-in-out infinite reverse; }
.c3{ width:42vmax;height:24vmax; right:10vmax;bottom:3vmax; animation:cloud 52s ease-in-out infinite; }
.c4{ width:38vmax;height:22vmax; right:14vmax;bottom:5vmax; animation:cloud 58s ease-in-out infinite reverse; }
.c5{ width:34vmax;height:20vmax; right:18vmax;bottom:7vmax; animation:cloud 62s ease-in-out infinite; }

@keyframes float{0%{transform:translate3d(0,0,0)}50%{transform:translate3d(-6%,7%,0)}100%{transform:translate3d(0,0,0)}}
@keyframes cloud{0%{transform:translate3d(0,0,0)}50%{transform:translate3d(-3%,-4%,0)}100%{transform:translate3d(0,0,0)}}

/* content above smoke */
.wrap{position:relative; z-index:2; max-width:var(--container); margin:0 auto; padding:28px 5%}

/* ---------- Header ---------- */
header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 22px;border-bottom:1px solid var(--line);backdrop-filter:saturate(140%) blur(10px);background:rgba(15,16,34,.35)}
.brand{display:flex;align-items:center;gap:1rem}
.brand-badge{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(155,92,255,.3),rgba(255,32,69,.25));border:1px solid rgba(255,255,255,.08); color:#ffd1dc}
.hero-heading{font-size:3.7rem;font-weight:1000;line-height:1.02;margin:.1rem 0;letter-spacing:.8px;background:linear-gradient(90deg,#b892ff,#ff2045 55%,#ff8a5b 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent;text-shadow:0 0 28px rgba(155,92,255,.25)}

.btn{--pad:.75rem 1.05rem;display:inline-flex;align-items:center;gap:.5rem;padding:var(--pad);border-radius:14px;border:1px solid transparent;cursor:pointer;font-weight:800;letter-spacing:.2px;transition:.2s}
.btn-neon{background:linear-gradient(135deg,#3de2ff,#9b5cff);box-shadow:0 8px 30px rgba(61,226,255,.25);color:#001018}
.btn-neon:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(61,226,255,.35)}
.btn-ghost{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.16);color:#fff}
.btn-ghost:hover{background:rgba(255,255,255,.08);transform:translateY(-2px)}
.btn-danger{background:linear-gradient(135deg,#ff2045,#ff7a59);color:#fff;box-shadow:0 8px 30px rgba(255,32,69,.25)}
.btn-danger:hover{transform:translateY(-2px);box-shadow:0 12px 40px rgba(255,32,69,.35)}

/* ---------- Analyzer panel ---------- */
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}
.section-title{font-size:1.6rem;margin:0 0 .3rem} .section-subtitle{margin:0;color:var(--text-dim)}

/* ---------- SCORE WHEEL ---------- */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:240px}
.score-wheel{width:100%;height:auto;transform:rotate(-90deg)}
.score-wheel circle{fill:none;stroke-width:14;stroke-linecap:round}
.score-wheel .bg{stroke:rgba(255,255,255,.12)}
.score-wheel .progress{
  stroke:url(#gradSmoke);
  stroke-dasharray:339; stroke-dashoffset:339;
  transition:stroke-dashoffset .6s ease,stroke .25s ease,filter .25s ease;
  filter:drop-shadow(0 0 12px rgba(155,92,255,.28)) drop-shadow(0 0 20px rgba(255,32,69,.18));
}
.score-text{
  font-size:3.2rem;font-weight:1000;fill:#fff;transform:rotate(90deg);
  text-shadow:0 0 18px rgba(255,32,69,.25), 0 0 24px rgba(61,226,255,.18);
}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28)}

/* ---------- URL input ---------- */
.analyze-form input[type="url"]{
  position:relative; z-index:5; width:100%; padding:1rem 1.2rem; border-radius:14px;
  border:1px solid #1b1b35; background:#0b0d21; color:var(--text);
  box-shadow:0 0 0 0 rgba(155,92,255,.0); transition:.25s;
}
.analyze-form input[type="url"]:focus{ outline:none; border-color:#5942ff; box-shadow:0 0 0 6px rgba(155,92,255,.15); }
.analyze-row{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;margin-top:.5rem}

/* ---------- Progress ---------- */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
.progress-fill{height:100%;background:linear-gradient(135deg,#9b5cff,#ff2045);width:0%;transition:width .35s ease}
.progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}

/* ---------- CHECKLIST (new look) ---------- */
.analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
.category-card{
  position:relative;grid-column:span 6;background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.03));
  border:1px solid rgba(255,255,255,.08);border-radius:18px;padding:16px;box-shadow:var(--shadow);
  overflow:hidden;isolation:isolate;
}
.category-card::before{
  content:"";position:absolute;inset:-2px;border-radius:18px;padding:2px;
  background:conic-gradient(from 180deg, rgba(61,226,255,.35), rgba(155,92,255,.35), rgba(255,182,72,.30), rgba(255,32,69,.30), rgba(34,197,94,.30), rgba(61,226,255,.35));
  -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
  -webkit-mask-composite:xor;mask-composite:exclude;animation:borderGlow 7s linear infinite;
  pointer-events:none; z-index:0;
}
@keyframes borderGlow{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(360deg)}}
.category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
.category-icon{
  width:46px;height:46px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;
  background:linear-gradient(135deg,#3de2ff33,#9b5cff33);color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,.18)
}
.category-title{
  margin:0;font-size:1.08rem;background:linear-gradient(90deg,#3de2ff,#9b5cff,#ff2045);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900
}
.category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}

/* Items */
.checklist{list-style:none;margin:10px 0 0;padding:0}
.checklist-item{
  display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;
  padding:.75rem .8rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);
  background:linear-gradient(180deg,rgba(255,255,255,.035),rgba(255,255,255,.03));
  position:relative; overflow:hidden;
}
.checklist-item + .checklist-item{margin-top:.28rem}
.checklist-item::before{
  content:""; position:absolute; inset:-2px; border-radius:16px;
  background:conic-gradient(from 200deg, rgba(61,226,255,.20), rgba(155,92,255,.20), rgba(255,32,69,.18), rgba(255,182,72,.18), rgba(34,197,94,.18), rgba(61,226,255,.20));
  opacity:.0; transition:.25s; filter:blur(14px); z-index:0;
}
.checklist-item:hover::before{ opacity:.55 }
.checklist-item:hover{
  transform:translateY(-2px);
  box-shadow:0 10px 34px rgba(0,0,0,.28);
  border-color:rgba(255,255,255,.16);
}
.checklist-item > *{ position:relative; z-index:1; }

/* Modern toggle */
.checklist-item input[type="checkbox"]{
  appearance:none; width:38px;height:22px;border-radius:999px; background:#2a2a46;
  border:1px solid rgba(255,255,255,.18); position:relative; outline:none; transition:.2s; cursor:pointer;
}
.checklist-item input[type="checkbox"]::after{
  content:""; position:absolute; width:16px;height:16px; border-radius:50%; background:#cfd3f6; top:2px; left:2px; transition:.2s;
  box-shadow:0 2px 10px rgba(0,0,0,.3);
}
.checklist-item input[type="checkbox"]:checked{ background:linear-gradient(135deg,#3de2ff,#9b5cff); }
.checklist-item input[type="checkbox"]:checked::after{ left:20px; background:#0a1222; }

.score-badge{
  font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);
  background:rgba(255,255,255,.06);min-width:52px;text-align:center;
}
.score-good{background:rgba(22,193,114,.22); border-color:rgba(22,193,114,.45)}
.score-mid{ background:rgba(245,158,11,.22); border-color:rgba(245,158,11,.45)}
.score-bad{ background:rgba(239,68,68,.24); border-color:rgba(239,68,68,.5)}
.improve-btn{padding:.4rem .75rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);font-weight:900;cursor:pointer}
.improve-btn:hover{background:rgba(255,255,255,.1)}

/* ---------- Modal ---------- */
.modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);display:none;z-index:9000}
.modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:9010}
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

/* ---------- Footer + back to top ---------- */
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
  .score-container{width:200px}
  footer.site{flex-direction:column;align-items:flex-start}
}
@media print{#linesCanvas,#linesCanvas2,.bg-smoke,.modal-backdrop,.modal,header.site,#backTop{display:none!important}}
</style>
</head>
<body>
<canvas id="linesCanvas"></canvas>
<canvas id="linesCanvas2"></canvas>

<!-- Smoke + Clouds (all colors) -->
<div class="bg-smoke">
  <span class="blob cyan   b1"></span>
  <span class="blob purple b2"></span>
  <span class="blob red    b3"></span>
  <span class="blob orange b4"></span>
  <span class="blob teal   b5"></span>
  <div class="clouds">
    <span class="c cyan   c1"></span>
    <span class="c purple c2"></span>
    <span class="c red    c3"></span>
    <span class="c orange c4"></span>
    <span class="c teal   c5"></span>
  </div>
</div>

<!-- Gradients for score wheel -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <!-- smoke multicolor base -->
    <linearGradient id="gradSmoke" x1="0%" y1="0%" x2="100%">
      <stop offset="0%"  stop-color="#3de2ff"/>
      <stop offset="25%" stop-color="#9b5cff"/>
      <stop offset="55%" stop-color="#ff2045"/>
      <stop offset="80%" stop-color="#ffb648"/>
      <stop offset="100%" stop-color="#22c55e"/>
    </linearGradient>
    <!-- thresholds -->
    <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%">
      <stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/>
    </linearGradient>
    <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%">
      <stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/>
    </linearGradient>
    <linearGradient id="gradBad" x1="0%" y1="0%" x2="100%">
      <stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/>
    </linearGradient>
  </defs>
</svg>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
      <div><div class="hero-heading">Semantic SEO Master Analyzer</div></div>
    </div>
    <div style="display:flex;gap:.5rem">
      <button class="btn btn-ghost" id="printTop"><i class="fa-solid fa-print"></i> Print</button>
    </div>
  </header>

  <section class="analyzer" id="analyzer">
    <h2 class="section-title">Analyze a URL</h2>
    <p class="section-subtitle">
      The wheel fills with your overall score. <span class="chip" style="background:rgba(34,197,94,.18)">Green ≥ 80</span> <span class="chip" style="background:rgba(245,158,11,.18)">Orange 60–79</span> <span class="chip" style="background:rgba(239,68,68,.18)">Red &lt; 60</span>
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
          <span class="chip">Overall: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge">Writer: <b>—</b></span>
        </div>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap">
          <span class="chip">Human <b id="humanPct">—</b>%</span>
          <span class="chip">AI <b id="aiPct">—</b>%</span>
        </div>
      </div>
    </div>

    <div class="analyze-box" style="margin-top:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px">
      <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
        <label for="analyzeUrl" style="display:block;font-weight:800;margin-bottom:.35rem">Page URL</label>
        <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" />
        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> Auto‑apply checkmarks (≥ 80)
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-danger" type="button"><i class="fa-solid fa-magnifying-glass"></i> Analyze</button>
          <button class="btn btn-neon" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> Print</button>
          <button class="btn btn-ghost" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> Reset</button>
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

    <!-- Categories / checklist -->
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
        ['Content & Keywords',1,5,'fa-pen-nib'],
        ['Technical Elements',6,9,'fa-code'],
        ['Content Quality',10,13,'fa-star'],
        ['Structure & Architecture',14,17,'fa-sitemap'],
        ['User Signals & Experience',18,21,'fa-user-check'],
        ['Entities & Context',22,25,'fa-database'],
      ] as $c)
        <article class="category-card">
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
                <button class="improve-btn" type="button" data-id="ck-{{ $i }}">Improve</button>
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
      <button class="tab" data-tab="examplesTab"><i class="fa-brands fa-google"></i> Examples</button>
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
/* ---------- Smoke "breathing" pulse ---------- */
(function(){
  const smoke = document.querySelector('.bg-smoke');
  if(!smoke) return;
  let t=0;
  function loop(){
    t += 0.008;
    const op = 0.85 + Math.sin(t)*0.08;         // 0.77–0.93
    const sc = 1.00 + Math.cos(t*0.6)*0.02;     // 0.98–1.02
    smoke.style.opacity = op.toFixed(3);
    smoke.style.transform = `scale(${sc.toFixed(3)})`;
    requestAnimationFrame(loop);
  }
  loop();
})();

/* ---------- Dancing lines (2 layers) ---------- */
(function(){
  function runLayer(id, count, maxDist, colorFn, vel=1){
    const c = document.getElementById(id), ctx = c.getContext('2d');
    let w,h,nodes=[],mouse={x:-9999,y:-9999};
    function resize(){ w=c.width=innerWidth; h=c.height=innerHeight; nodes=Array.from({length:count},()=>({x:Math.random()*w,y:Math.random()*h,vx:(Math.random()-.5)*vel,vy:(Math.random()-.5)*vel}))}
    addEventListener('resize',resize,{passive:true}); resize();
    addEventListener('mousemove',e=>{mouse.x=e.clientX; mouse.y=e.clientY},{passive:true});
    (function loop(){
      ctx.clearRect(0,0,w,h);
      for(const n of nodes){
        const dx=mouse.x-n.x, dy=mouse.y-n.y, d=Math.hypot(dx,dy);
        const attract=d<maxDist?(1-d/maxDist)*.9:0;
        n.vx+=(dx/d||0)*attract*.18; n.vy+=(dy/d||0)*attract*.18;
        n.vx*=.97; n.vy*=.97; n.x+=n.vx; n.y+=n.vy;
        if(n.x<0||n.x>w) n.vx*=-1; if(n.y<0||n.y>h) n.vy*=-1;
      }
      for(let i=0;i<nodes.length;i++){
        for(let j=i+1;j<nodes.length;j++){
          const a=nodes[i], b=nodes[j], d=Math.hypot(a.x-b.x,a.y-b.y);
          if(d<maxDist){ const alpha=(1-d/maxDist)*.65; ctx.strokeStyle=colorFn(alpha); ctx.lineWidth=1; ctx.beginPath(); ctx.moveTo(a.x,a.y); ctx.lineTo(b.x,b.y); ctx.stroke();}
        }
      }
      requestAnimationFrame(loop);
    })();
  }
  runLayer('linesCanvas', 140, 130, a=>`rgba(61,226,255,${a})`, 1.1); // cyan
  runLayer('linesCanvas2', 110, 120, a=>`rgba(255,32,69,${a*0.6})`, 0.9); // red
})();

/* ---------- Back to Top ---------- */
(function(){
  const btn=document.getElementById('backTop'), link=document.getElementById('toTopLink');
  function onScroll(){ btn.style.display = window.scrollY>300 ? 'grid' : 'none'; }
  addEventListener('scroll', onScroll, {passive:true}); onScroll();
  const goTop = e => { e && e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); };
  btn.addEventListener('click', goTop); if(link) link.addEventListener('click', goTop);
})();

/* ---------- SCORE wheel (threshold colors + number inside) ---------- */
const WHEEL = { circumference: 339, circle: null, text: null };
function setScoreWheel(value){
  if (!WHEEL.circle) { WHEEL.circle = document.querySelector('.score-wheel .progress'); WHEEL.text = document.getElementById('overallScore'); }
  const v = Math.max(0, Math.min(100, value));
  const offset = WHEEL.circumference - (v/100) * WHEEL.circumference;
  WHEEL.circle.style.strokeDashoffset = offset;

  // Threshold colors
  if (v >= 80)      WHEEL.circle.setAttribute('stroke','url(#gradGood)');
  else if (v >= 60) WHEEL.circle.setAttribute('stroke','url(#gradMid)');
  else              WHEEL.circle.setAttribute('stroke','url(#gradBad)');

  // Score inside
  const n = Math.round(v);
  WHEEL.text.textContent = n;
  document.getElementById('overallScoreInline').textContent = n;
}

/* ---------- Checklist counters + storage ---------- */
(function () {
  const STORAGE_KEY = 'semanticSeoChecklistV8';
  const total = 25;
  const boxes = () => Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
  const bar = document.getElementById('progressBar');
  const caption = document.getElementById('progressCaption');

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
  document.getElementById('resetChecklist').addEventListener('click', ()=>{ if(!confirm('Reset the checklist?')) return; localStorage.removeItem(STORAGE_KEY); boxes().forEach(cb=>cb.checked=false); for(let i=1;i<=25;i++){ setScoreBadge(i,null);} setScoreWheel(0); update(); });
  document.getElementById('printChecklist').addEventListener('click', ()=> window.print());
  document.getElementById('printTop').addEventListener('click', ()=> window.print());
  window.setScoreBadge = (num,score)=>{ const el=document.getElementById('sc-'+num); if(!el) return; el.className='score-badge'; if(score==null){el.textContent='—';return;} el.textContent=score; if(score>=80) el.classList.add('score-good'); else if(score>=60) el.classList.add('score-mid'); else el.classList.add('score-bad'); };
  load();
})();

/* ---------- Modal (Improve) ---------- */
(function(){
  const $ = s=>document.querySelector(s);
  const $$ = s=>Array.from(document.querySelectorAll(s));
  const backdrop = $('#modalBackdrop'), modal = $('#tipModal'), closeBtn = $('#modalClose');
  const title = $('#modalTitle'), tipsList = $('#modalList');
  const panes = { tipsTab: $('#tipsTab'), examplesTab: $('#examplesTab'), humanTab: $('#humanTab'), aiTab: $('#aiTab'), fullTab: $('#fullTab') };
  const tabs = $$('.tab');

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
    title.textContent = 'Improve: ' + labelFor(id);
    const all = (window.__lastSuggestions && typeof window.__lastSuggestions==='object') ? window.__lastSuggestions : {};
    const tips = (id && Array.isArray(all[id]) && all[id].length) ? all[id] : ['Run Analyze first to generate contextual suggestions.'];
    tipsList.innerHTML = ''; tips.forEach(t=>{ const li=document.createElement('li'); li.textContent=t; tipsList.appendChild(li); });

    tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="tipsTab"]').classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.tipsTab.classList.add('active');

    openModal();
  });
})();

/* ---------- URL normalization + Analyze (auto-select ≥80) ---------- */
function normalizeUrl(u){
  if(!u) return '';
  u = u.trim();
  if (!/^https?:\/\//i.test(u)) u = 'https://' + u.replace(/^\/+/, '');
  try { new URL(u); } catch(e){ /* allow; backend validates */ }
  return u;
}
(function(){
  const $ = s => document.querySelector(s);

  document.getElementById('analyzeForm').addEventListener('submit', (e)=>{
    e.preventDefault();
    document.getElementById('analyzeBtn').click();
  });
  document.getElementById('analyzeBtn').addEventListener('click', analyze);

  async function analyze(){
    const url = normalizeUrl($('#analyzeUrl').value);
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

      // Suggestions store
      window.__lastSuggestions = (data && typeof data.suggestions==='object' && data.suggestions) ? data.suggestions : {};

      // Chips
      document.getElementById('rStatus').textContent = data.status;
      document.getElementById('rTitleLen').textContent = (data.title || '').length;
      document.getElementById('rMetaLen').textContent = data.meta_description_len;
      document.getElementById('rCanonical').textContent = data.canonical ? 'Yes' : 'No';
      document.getElementById('rRobots').textContent = data.robots || '—';
      document.getElementById('rViewport').textContent = data.viewport ? 'Yes' : 'No';
      document.getElementById('rHeadings').textContent = `${data.counts.h1}/${data.counts.h2}/${data.counts.h3}`;
      document.getElementById('rInternal').textContent = data.counts.internal_links;
      const types = (data.schema.found_types || []).slice(0,6).join(', ') || '—';
      document.getElementById('rSchema').textContent = types;
      report.style.display='block';

      // Per‑item scores + auto‑select when score ≥ 80 (only those)
      let autoCount=0;
      for (let i=1;i<=25;i++){
        const key='ck-'+i;
        const score = (data.scores && typeof data.scores[key]==='number') ? data.scores[key] : null;
        setScoreBadge(i, score);
        const cb = document.getElementById(key);
        if (cb && document.getElementById('autoApply').checked && typeof score==='number' && score>=80){
          cb.checked = true; autoCount++;
        }
      }
      document.getElementById('rAutoCount').textContent = autoCount.toString();
      document.dispatchEvent(new Event('change')); // refresh progress

      // Overall wheel color + inner number
      const overall = typeof data.overall_score === 'number' ? data.overall_score : 0;
      setScoreWheel(overall);

      // AI/Human badge (both percents)
      const ai = data.ai_detection || {};
      const labelMap = { likely_human:'Likely Human', mixed:'Mixed', likely_ai:'Likely AI', unknown:'Unknown' };
      const label = labelMap[ai.label] || 'Unknown';
      const humanPct = (typeof ai.human_pct==='number') ? ai.human_pct : null;
      const aiPct    = (typeof ai.ai_pct==='number') ? ai.ai_pct : null;
      const parts = [`<b>${label}</b>`];
      if (humanPct!==null) parts.push(`Human ${humanPct}%`);
      if (aiPct!==null)    parts.push(`AI ${aiPct}%`);
      document.getElementById('aiBadge').innerHTML = `Writer: ${parts.join(' • ')}`;
      document.getElementById('humanPct').textContent = humanPct!==null?humanPct:'—';
      document.getElementById('aiPct').textContent = aiPct!==null?aiPct:'—';
      document.getElementById('aiSnippetsPre').textContent = (ai.ai_sentences||[]).slice(0,20).join('\n\n') || 'No AI‑like snippets detected.';
      document.getElementById('humanSnippetsPre').textContent = (ai.human_sentences||[]).slice(0,20).join('\n\n') || 'No human‑like snippets isolated.';
      document.getElementById('fullTextPre').textContent = ai.full_text || 'No text captured.';

      // Status line
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
</body>
</html>
