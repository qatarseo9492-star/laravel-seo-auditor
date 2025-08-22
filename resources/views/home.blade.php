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

/* --- Cloudy smoke (bottom-right visible) --- */
.bg-smoke{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
.blob{position:absolute;border-radius:50%;filter:blur(90px);mix-blend-mode:screen;animation:float 36s linear infinite}
.blob.p{background:radial-gradient(closest-side,rgba(155,92,255,.38),rgba(155,92,255,0) 70%)}
.blob.r{background:radial-gradient(closest-side,rgba(255,32,69,.34),rgba(255,32,69,0) 70%)}
.b1{top:-18%;left:-15%;width:60vmax;height:60vmax}
.b2{bottom:-22%;right:-10%;width:62vmax;height:62vmax;animation-direction:reverse;animation-duration:30s}
.b3{top:10%;right:15%;width:50vmax;height:50vmax;animation-duration:28s}
.b4{bottom:10%;left:25%;width:48vmax;height:48vmax;animation-duration:40s}

/* NEW: cloud cluster */
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

/* Wheel row */
.score-area{display:flex;gap:1.2rem;align-items:center;margin:.6rem 0 0;flex-wrap:wrap}
.score-container{width:220px}
.score-wheel{width:100%;height:auto;transform:rotate(-90deg)}
.score-wheel circle{fill:none;stroke-width:14;stroke-linecap:round}
.score-wheel .bg{stroke:rgba(255,255,255,.12)}
.score-wheel .progress{stroke:url(#gradBad);stroke-dasharray:339;stroke-dashoffset:339;transition:stroke-dashoffset .6s ease,stroke .3s ease,filter .3s ease;filter:drop-shadow(0 0 10px rgba(155,92,255,.35))}
.score-text{font-size:3rem;font-weight:1000;fill:#ffffff;transform:rotate(90deg);filter:drop-shadow(0 0 6px rgba(0,0,0,.35))}
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
@media print{#linesCanvas,#linesCanvas2,#brainCanvas,.bg-smoke,.modal-backdrop,.modal,header.site,#backTop{display:none!important}}

/* ---------- Roses Fireworks (celebration) ---------- */
#celebrate{position:fixed;inset:0;pointer-events:none;z-index:120;overflow:hidden;display:none}
.rose{
  position:absolute; font-size:28px; will-change:transform, opacity;
  filter: drop-shadow(0 6px 10px rgba(255,32,69,.45));
  animation: burst 1.6s ease-out forwards;
}
@keyframes burst{
  0%{ transform:translate(var(--sx,0), var(--sy,0)) scale(.4) rotate(0); opacity:0 }
  12%{ opacity:1 }
  70%{ transform:translate(var(--tx,0), var(--ty,0)) scale(1) rotate(var(--rot,45deg)); opacity:1 }
  100%{ transform:translate(var(--tx,0), var(--ty,0)) scale(.9) rotate(var(--rot,45deg)); opacity:0 }
}
</style>
</head>
<body>
<canvas id="brainCanvas"></canvas>
<canvas id="linesCanvas"></canvas>
<canvas id="linesCanvas2"></canvas>

<div class="bg-smoke">
  <span class="blob p b1"></span>
  <span class="blob r b2"></span>
  <span class="blob p b3"></span>
  <span class="blob r b4"></span>
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
    <linearGradient id="gradBad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/></linearGradient>
  </defs>
</svg>

<!-- celebration layer -->
<div id="celebrate"></div>

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
      The wheel fills with your overall score. <span class="legend l-green">Green ≥ 80</span>
      <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Red &lt; 60</span>
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
          <span class="chip"><span>Overall</span>: <b id="overallScoreInline">0</b>/100</span>
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
        <label for="analyzeUrl" style="display:block;font-weight:800;margin-bottom:.35rem">Page URL</label>
        <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" />
        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> Auto‑apply checkmarks (≥ 70)
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-danger"><i class="fa-solid fa-magnifying-glass"></i> Analyze</button>
          <button class="btn btn-neon" id="printChecklist"><i class="fa-solid fa-print"></i> Print</button>
          <button class="btn btn-ghost" id="resetChecklist"><i class="fa-solid fa-rotate"></i> Reset</button>
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

    <div class="progress-wrap">
      <div class="progress-bar"><div class="progress-fill" id="progressBar"></div></div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    {{-- Checklist categories (short demo – keep your existing 25 items or paste them here) --}}
    <div class="analyzer-grid" id="grid">
      @for($i=1;$i<=25;$i++)
      <article class="category-card">
        <header class="category-head">
          <span class="category-icon"><i class="fas fa-star"></i></span>
          <div><h3 class="category-title">Checklist Item {{ $i }}</h3><p class="category-sub">—</p></div>
          <span class="chip"><span class="checked-count">0</span>/<span class="total-count">1</span></span>
        </header>
        <ul class="checklist">
          <li class="checklist-item">
            <label>
              <input type="checkbox" id="ck-{{ $i }}">
              <span>Item {{ $i }} description…</span>
            </label>
            <span class="score-badge" id="sc-{{ $i }}">—</span>
            <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
          </li>
        </ul>
      </article>
      @endfor
    </div>
  </section>
</div>

<footer class="site">
  <div class="footer-brand"><span class="dot"></span><strong>Semantic SEO Master</strong></div>
  <div class="footer-links">
    <a href="#analyzer" id="toTopLinkA">Analyzer</a>
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
/* ---------- Back to Top ---------- */
(function(){
  const btn = document.getElementById('backTop'); const link = document.getElementById('toTopLink'); const linkA = document.getElementById('toTopLinkA');
  function onScroll(){ btn.style.display = window.scrollY>300 ? 'grid' : 'none'; }
  addEventListener('scroll', onScroll, {passive:true}); onScroll();
  const goTop = e => { e && e.preventDefault(); window.scrollTo({top:0,behavior:'smooth'}); };
  btn.addEventListener('click', goTop); link.addEventListener('click', goTop); linkA.addEventListener('click', goTop);
})();

/* ---------- Dancing lines (simple) ---------- */
(function(){
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
          const d=Math.hypot(a.x-b.x,b.y-a.y);
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

/* ---------- Score wheel helpers (with thresholds) ---------- */
const WHEEL = { circumference: 339, circle: null, text: null, last: 0, celebrated: false };
function setScoreWheel(value){
  if (!WHEEL.circle) {
    WHEEL.circle = document.querySelector('.score-wheel .progress');
    WHEEL.text   = document.getElementById('overallScore');
  }
  const v = Math.max(0, Math.min(100, value));
  const offset = WHEEL.circumference - (v/100) * WHEEL.circumference;
  WHEEL.circle.style.strokeDashoffset = offset;

  // Color thresholds
  if (v >= 80) {
    WHEEL.circle.setAttribute('stroke','url(#gradGood)');
  } else if (v >= 60) {
    WHEEL.circle.setAttribute('stroke','url(#gradMid)');
  } else {
    WHEEL.circle.setAttribute('stroke','url(#gradBad)');
  }

  // Number inside
  const n = Math.round(v);
  WHEEL.text.textContent = n;
  document.getElementById('overallScoreInline').textContent = n;

  // Trigger roses once when crossing 80+
  if (WHEEL.last < 80 && v >= 80 && !WHEEL.celebrated) {
    celebrateRoses();
    WHEEL.celebrated = true;
  }
  if (v < 80) WHEEL.celebrated = false; // allow re-celebrate if user drops then re-achieves
  WHEEL.last = v;
}

/* ---------- Checklist + scoring UI skeleton ---------- */
(function () {
  const STORAGE_KEY = 'semanticSeoChecklistV6'; // bumped to avoid old all-checked saves
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
    if (allChecked) return 100;
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
    try{
      const savedRaw = localStorage.getItem(STORAGE_KEY) || '[]';
      const saved = JSON.parse(savedRaw);
      if (Array.isArray(saved) && saved.length >= 25) {
        localStorage.removeItem(STORAGE_KEY); // wipe suspicious all-checked state
      } else {
        boxes().forEach(cb=>cb.checked = Array.isArray(saved) && saved.includes(cb.id));
      }
    }catch(e){}
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
  window.__setAnalyzedScore = function(v){ lastAnalyzed = Math.max(0, Math.min(100, +v||0)); setScoreWheel( overallScoreBlended() ); }
  window.__getContentScore = contentScore;
  load();
})();

/* ---------- Modal + panes ---------- */
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
    title.textContent = 'Improve: '+labelFor(id);
    tipsList.innerHTML = '';
    const tips = (window.__lastSuggestions && window.__lastSuggestions[id]) ? window.__lastSuggestions[id] : ['Analyze the URL first to generate contextual suggestions.'];
    tips.forEach(t=>{ const li=document.createElement('li'); li.textContent=t; tipsList.appendChild(li); });

    document.querySelectorAll('.tab').forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="tipsTab"]').classList.add('active');
    Object.values(panes).forEach(p=>p.classList.remove('active')); panes.tipsTab.classList.add('active');

    openModal();
  }, { capture:true });

  document.getElementById('viewAIText').addEventListener('click', ()=>{
    title.textContent = 'Evidence & Full Text';
    tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="aiTab"]').classList.add('active');
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

    // FIX: show both percentages clearly
    const badge = document.getElementById('aiBadge');
    const labelMap = { likely_human: 'Likely Human', mixed: 'Mixed', likely_ai: 'Likely AI', unknown:'Unknown' };
    const label = labelMap[ai?.label] || 'Unknown';
    const humanPct = (typeof ai?.human_pct === 'number') ? ai.human_pct : null;
    const aiPct    = (typeof ai?.ai_pct === 'number') ? ai.ai_pct : null;
    let parts = [`<b>${label}</b>`];
    if (humanPct !== null) parts.push(`Human ${humanPct}%`);
    if (aiPct !== null)    parts.push(`AI ${aiPct}%`);
    badge.innerHTML = `Writer: ${parts.join(' • ')}`;
    badge.title = (ai?.reasons || []).join(' • ');

    // update small chips too
    document.getElementById('aiPct').textContent = (aiPct !== null) ? aiPct : '—';
    document.getElementById('humanPct').textContent = (humanPct !== null) ? humanPct : '—';
  }
})();

/* ---------- URL normalization + Analyze ---------- */
function normalizeUrl(u){
  if(!u) return '';
  u = u.trim();
  if (!/^https?:\/\//i.test(u)) u = 'https://' + u.replace(/^\/+/, '');
  try { new URL(u); } catch(e){ /* backend validates */ }
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

      // scores per item
      window.__lastSuggestions = data.suggestions || {};
      for (let i=1;i<=25;i++){ const key='ck-'+i; setScoreBadge(i, data.scores?.[key]); }

      // numeric scores
      const backendOverall = typeof data.overall_score === 'number' ? data.overall_score : 0;
      window.__setAnalyzedScore(backendOverall); // feeds blended wheel
      document.getElementById('contentScoreInline').textContent = window.__getContentScore();

      // AI/Human panes + badge (fixed)
      window.__setAIData(data.ai_detection || {});

      // auto-check (conservative; only server-verified)
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

/* ---------- Roses celebration ---------- */
function celebrateRoses(){
  const layer = document.getElementById('celebrate');
  layer.innerHTML = '';
  layer.style.display = 'block';

  const ROSES = ['🌹','🥀','🌺']; // red roses and floral
  const bursts = 14; // number of bursts across screen
  const petalsPer = 14;

  const w = window.innerWidth, h = window.innerHeight;

  for (let b=0; b<bursts; b++){
    const cx = Math.random()*w*0.9 + w*0.05;
    const cy = Math.random()*h*0.7 + h*0.15;
    const emoji = ROSES[Math.floor(Math.random()*ROSES.length)];
    for (let i=0;i<petalsPer;i++){
      const span = document.createElement('span');
      span.className='rose';
      span.textContent = emoji;
      const angle = (Math.PI*2) * (i/petalsPer) + Math.random()*0.4;
      const radius = 80 + Math.random()*120;
      const tx = Math.cos(angle) * radius;
      const ty = Math.sin(angle) * radius;
      span.style.left = (cx) + 'px';
      span.style.top  = (cy) + 'px';
      span.style.setProperty('--tx', tx+'px');
      span.style.setProperty('--ty', ty+'px');
      span.style.setProperty('--rot', (Math.random()*360)+'deg');
      span.style.animationDelay = (Math.random()*0.15)+'s';
      layer.appendChild(span);
    }
  }
  // fade out the layer after animations end
  setTimeout(()=>{ layer.style.display='none'; layer.innerHTML=''; }, 1800);
}
</script>
</body>
</html>
