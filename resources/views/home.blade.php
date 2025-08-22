{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Semantic SEO Master • Dark Purple + Red Smoke</title>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#0b0a10;
      --panel:#0f0f1b;
      --panel-2:#151428;
      --line:#231f33;
      --text:#ecebf6;
      --text-dim:#a9a5c6;
      --text-muted:#9da0bf;
      --primary:#8b5cf6;     /* purple */
      --secondary:#ef4444;   /* red accent */
      --accent:#22d3ee;      /* cyan pinch */
      --good:#10b981;
      --warn:#f59e0b;
      --bad:#ef4444;

      --radius:16px;
      --shadow:0 8px 28px rgba(0,0,0,.5);
      --shadow-hover:0 14px 40px rgba(0,0,0,.6);
      --transition:.25s ease;
      --container:1200px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Noto Sans","Helvetica Neue",Arial;
      color:var(--text);
      background: radial-gradient(1200px 700px at 0% -10%, #1a1133 0%, transparent 55%),
                  radial-gradient(1100px 800px at 120% 0%, #1a0f22 0%, transparent 50%),
                  var(--bg);
      overflow-x:hidden; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }
    a{color:var(--accent);text-decoration:none}
    a:hover{opacity:.9}

    /* ====== Purple + Red Smoke Effect ====== */
    .bg-smoke{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
    .bg-smoke .blob{
      position:absolute; width:60vmax; height:60vmax; border-radius:50%;
      filter: blur(70px); mix-blend-mode: screen; will-change: transform;
      animation: float 36s linear infinite;
    }
    .blob.purple{ background: radial-gradient(closest-side, rgba(139,92,246,.35), rgba(139,92,246,0) 70%); }
    .blob.red   { background: radial-gradient(closest-side, rgba(239,68,68,.28), rgba(239,68,68,0) 70%); }
    .blob.cyan  { background: radial-gradient(closest-side, rgba(34,211,238,.18), rgba(34,211,238,0) 70%); }

    .b1{top:-18%; left:-15%}
    .b2{bottom:-22%; right:-10%; animation-direction:reverse; animation-duration:30s}
    .b3{top:10%; right:15%; animation-duration:28s}
    .b4{bottom:10%; left:25%; animation-duration:40s}

    @keyframes float{
      0%   { transform: translate3d(0,0,0) rotate(0deg) }
      25%  { transform: translate3d(7%, -5%, 0) rotate(50deg) }
      50%  { transform: translate3d(-6%, 7%, 0) rotate(110deg) }
      75%  { transform: translate3d(-10%, -8%, 0) rotate(170deg) }
      100% { transform: translate3d(0,0,0) rotate(360deg) }
    }
    .veil{
      position:fixed;inset:0;z-index:1;pointer-events:none;
      background: radial-gradient(1200px 800px at 0% 0%, rgba(139,92,246,.08), transparent 50%),
                  radial-gradient(900px 700px at 100% 10%, rgba(239,68,68,.06), transparent 45%);
    }

    /* Layout */
    .wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
    header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 24px;border-bottom:1px solid var(--line)}
    .brand{display:flex;align-items:center;gap:.75rem}
    .brand-badge{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg, rgba(139,92,246,.2), rgba(239,68,68,.18));border:1px solid rgba(255,255,255,.06); color:#fca5a5}
    .brand h1{font-size:1.12rem;margin:0}
    .brand small{display:block;color:var(--text-dim)}
    .nav-actions{display:flex;gap:.5rem}
    .btn{border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.03);color:var(--text);padding:.6rem .9rem;font-weight:600;cursor:pointer;transition:var(--transition)}
    .btn:hover{transform:translateY(-1px);background:rgba(255,255,255,.06)}
    .btn-primary{background:linear-gradient(135deg,var(--primary),#6d28d9);border-color:transparent}
    .btn-danger{background:linear-gradient(135deg,#b91c1c,#ef4444);border-color:transparent}

    /* Hero */
    .hero{display:grid;grid-template-columns:1.1fr .9fr;gap:1.5rem;align-items:center;margin:34px 0 18px}
    .hero-card{background:linear-gradient(180deg, rgba(139,92,246,.10), rgba(239,68,68,.07));border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:28px;box-shadow:var(--shadow)}
    .hero h2{margin:0 0 .6rem;font-size:1.9rem}
    .hero p{margin:0;color:var(--text-muted)}
    .hero-badge{display:inline-flex;gap:.5rem;align-items:center;font-weight:700;color:#fca5a5}
    .stat{display:flex;gap:1rem;margin-top:1rem;color:var(--text-dim)}
    .stat b{color:var(--text)}
    .side{background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:24px;box-shadow:var(--shadow)}

    /* Analyzer */
    .analyzer{margin-top:22px;background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:20px;box-shadow:var(--shadow);padding:28px}
    .section-title{font-size:1.5rem;margin:0 0 .3rem}
    .section-subtitle{margin:0;color:var(--text-dim)}

    .analyze-form input[type="url"]{
      width:100%;padding:.7rem .9rem;border-radius:12px;border:1px solid var(--line);
      background:#0b0d1d;color:var(--text)
    }
    .analyze-form input[type="url"]::placeholder{color:#70738f}

    .progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px;position:relative}
    .progress-meta{display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:.5rem}
    .progress-label{font-weight:700;color:var(--text)}
    .progress-percent{font-weight:900;color:#c4b5fd}
    .overall-score{font-weight:900;padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.12); background:rgba(139,92,246,.12)}
    .progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
    .progress-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--secondary));width:0%;transition:width .35s ease}
    .progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}
    .save-toast{position:absolute;right:10px;top:-12px;transform:translateY(-100%);background:rgba(16,185,129,.18);color:#a7f3d0;border:1px solid rgba(134,239,172,.4);padding:.35rem .6rem;border-radius:999px;font-weight:700;box-shadow:var(--shadow)}

    .analyzer-actions{display:flex;gap:.6rem;margin-top:.8rem;justify-content:flex-end}

    .analyzer-grid{margin-top:1.2rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
    .category-card{grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.06);border-top:3px solid var(--primary);border-radius:16px;padding:14px;transition:var(--transition);box-shadow:var(--shadow)}
    .category-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-hover)}
    .category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
    .category-icon{width:42px;height:42px;border-radius:50%;background:rgba(139,92,246,.18);color:#c4b5fd;display:inline-flex;align-items:center;justify-content:center}
    .category-title{margin:0;font-size:1.05rem}
    .category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.95rem}
    .chip{padding:.2rem .55rem;border-radius:999px;font-weight:800;font-size:.85rem;background:rgba(139,92,246,.12);color:#c7d2fe;border:1px solid rgba(139,92,246,.25)}

    .checklist{list-style:none;margin:10px 0 0;padding:0}
    .checklist-item{display:grid;grid-template-columns:1fr auto auto;gap:.5rem;align-items:center;padding:.55rem .5rem;border-radius:10px;transition:var(--transition)}
    .checklist-item + .checklist-item{margin-top:.15rem}
    .checklist-item:hover{background:rgba(255,255,255,.03)}
    .checklist-item label{display:inline-flex;align-items:start;gap:.5rem;cursor:pointer}
    .checklist-item input[type="checkbox"]{width:18px;height:18px;margin:.1rem .55rem 0 0;accent-color:var(--primary)}
    .checklist-item span{line-height:1.4}

    .score-badge{
      font-weight:800;font-size:.82rem;padding:.2rem .5rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);
      background:rgba(255,255,255,.06); color:#fff; min-width:40px; text-align:center
    }
    .score-good{background:rgba(16,185,129,.2); border-color:rgba(16,185,129,.4)}
    .score-mid{ background:rgba(245,158,11,.2); border-color:rgba(245,158,11,.4)}
    .score-bad{ background:rgba(239,68,68,.22); border-color:rgba(239,68,68,.45)}

    .info{background:transparent;border:none;color:var(--text-dim);cursor:help;padding:.2rem .35rem;border-radius:8px}
    .info:hover{color:#fca5a5;background:rgba(239,68,68,.12)}

    .info[data-tooltip]{position:relative}
    .info[data-tooltip]:hover::after{
      content:attr(data-tooltip);
      position:absolute;top:-8px;right:110%;min-width:220px;
      background:#0b1220;border:1px solid #182235;color:#e5e7eb;font-size:.86rem;line-height:1.25;padding:.55rem .65rem;border-radius:8px;box-shadow:var(--shadow-hover);white-space:normal;z-index:10
    }
    .info[data-tooltip]:hover::before{
      content:"";position:absolute;top:3px;right:calc(110% - 6px);
      border:6px solid transparent;border-left-color:#182235
    }

    footer{margin:28px 0 10px;color:var(--text-dim);text-align:center}

    @media (max-width:992px){ .hero{grid-template-columns:1fr} .category-card{grid-column:span 12} }
    @media (max-width:768px){ .analyzer-actions{justify-content:stretch} }

    /* Print cleanly */
    @media print{
      body{background:#fff;color:#111}
      header.site,.hero,footer,.bg-smoke,.veil{display:none!important}
      .analyzer{background:#fff;border:none;box-shadow:none;margin:0;padding:0}
      .category-card{border:1px solid #ddd}
      .info{display:none!important}
      .progress-wrap{border:1px solid #ddd;background:#fafafa}
    }
  </style>
</head>
<body>
  <!-- Purple + Red smoke layers -->
  <div class="bg-smoke">
    <span class="blob purple b1"></span>
    <span class="blob red b2"></span>
    <span class="blob purple b3"></span>
    <span class="blob red b4"></span>
  </div>
  <div class="veil"></div>

  <div class="wrap">
    <header class="site">
      <div class="brand">
        <div class="brand-badge"><i class="fa-solid fa-share-nodes"></i></div>
        <div>
          <h1>Semantic SEO Master</h1>
          <small>Dark • Purple + Red Smoke • Laravel</small>
        </div>
      </div>
      <div class="nav-actions">
        <button class="btn btn-primary" id="printTop"><i class="fa-solid fa-print"></i>&nbsp;Print</button>
      </div>
    </header>

    <!-- Hero -->
    <section class="hero">
      <div class="hero-card">
        <span class="hero-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> URL Analyzer + Scoring</span>
        <h2>Analyze any URL to auto‑complete and score the full 25‑point Semantic SEO checklist.</h2>
        <p>Heuristic scoring per item (0–100), automatic checkmarks (≥ 70), overall score, progress, and auto‑save.</p>
        <div class="stat">
          <div><b>25</b> items</div>
          <div><b>6</b> categories</div>
          <div><b>Auto‑save</b> enabled</div>
        </div>
      </div>
      <aside class="side">
        <h3 style="margin-top:0">What’s inside</h3>
        <ul style="padding-left:1.2rem;margin:0;color:var(--text-dim)">
          <li>Per‑item score badges + auto‑check</li>
          <li>Overall score + progress %</li>
          <li>Reset & Print controls</li>
          <li>Responsive dark UI</li>
        </ul>
      </aside>
    </section>

    <!-- Analyzer -->
    <section class="analyzer" id="analyzer">
      <div class="section-header analyzer-header">
        <h2 class="section-title">Semantic SEO Master Analyzer</h2>
        <p class="section-subtitle">Paste a URL, get scores, and auto‑tick the checklist. You can still tweak manually.</p>

        <!-- URL Analyzer -->
        <div class="analyze-box" style="margin-top:18px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px">
          <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
            <label for="analyzeUrl" style="display:block;font-weight:700;margin-bottom:.35rem">Analyze a URL</label>
            <div style="display:grid;grid-template-columns:1fr auto;gap:.6rem;align-items:center">
              <input id="analyzeUrl" type="url" required placeholder="https://example.com/page">
              <button id="analyzeBtn" class="btn btn-danger" style="min-width:150px"><i class="fa-solid fa-magnifying-glass"></i>&nbsp;Analyze</button>
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;margin-top:.6rem">
              <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
                <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> Auto‑apply checkmarks (≥ 70)
              </label>
              <span id="analyzeStatus" style="color:var(--text-dim)"></span>
            </div>
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
              <span class="chip">Auto‑checked: <b id="rAutoCount">0</b></span>
            </div>
          </div>
        </div>

        <!-- Progress -->
        <div class="progress-wrap" aria-label="Checklist progress">
          <div class="progress-meta">
            <span class="progress-label"><i class="fas fa-rocket"></i> Overall Progress</span>
            <span class="progress-percent" id="progressPercent">0%</span>
            <span class="overall-score">Overall Score: <span id="overallScore">0</span></span>
          </div>
          <div class="progress-bar">
            <div class="progress-fill" id="progressBar" style="width:0%"></div>
          </div>
          <div class="progress-caption" id="progressCaption">0 of 25 items completed</div>
          <div class="save-toast" id="saveToast" role="status" aria-live="polite" hidden>
            <i class="fas fa-check-circle"></i> Saved
          </div>
        </div>

        <div class="analyzer-actions">
          <button class="btn" id="resetChecklist"><i class="fas fa-rotate"></i>&nbsp;Reset</button>
          <button class="btn btn-primary" id="printChecklist"><i class="fas fa-print"></i>&nbsp;Print</button>
        </div>
      </div>

      <!-- Categories / 25 items -->
      <div class="analyzer-grid">
        <!-- Content & Keywords (5) -->
        <article class="category-card" data-category="contentKeywords">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-pen-nib"></i></span>
            <div>
              <h3 class="category-title">Content &amp; Keywords</h3>
              <p class="category-sub">Intent‑aligned, discoverable content</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">5</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-1"  data-category="contentKeywords"><span>Define search intent & primary topic</span></label><span class="score-badge" id="sc-1">—</span><button class="info" data-tooltip="Title/H1 coherence + intent cues"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-2"  data-category="contentKeywords"><span>Map target & related keywords (synonyms/PAA)</span></label><span class="score-badge" id="sc-2">—</span><button class="info" data-tooltip="Questions in subheads + diversity"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-3"  data-category="contentKeywords"><span>H1 includes primary topic naturally</span></label><span class="score-badge" id="sc-3">—</span><button class="info" data-tooltip="Title↔H1 similarity & length"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-4"  data-category="contentKeywords"><span>Integrate FAQs / questions with answers</span></label><span class="score-badge" id="sc-4">—</span><button class="info" data-tooltip="FAQ schema + Qs in content"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-5"  data-category="contentKeywords"><span>Readable, NLP‑friendly language</span></label><span class="score-badge" id="sc-5">—</span><button class="info" data-tooltip="Avg sentence length"></button></li>
          </ul>
        </article>

        <!-- Technical Elements (4) -->
        <article class="category-card" data-category="technical">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-code"></i></span>
            <div>
              <h3 class="category-title">Technical Elements</h3>
              <p class="category-sub">Crawlability & SERP‑readiness</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-6"  data-category="technical"><span>Title tag (≈50–60 chars) w/ primary keyword</span></label><span class="score-badge" id="sc-6">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-7"  data-category="technical"><span>Meta description (≈140–160 chars) + CTA</span></label><span class="score-badge" id="sc-7">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-8"  data-category="technical"><span>Canonical tag set correctly</span></label><span class="score-badge" id="sc-8">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-9"  data-category="technical"><span>Indexable & listed in XML sitemap</span></label><span class="score-badge" id="sc-9">—</span><button class="info"></button></li>
          </ul>
        </article>

        <!-- Content Quality (4) -->
        <article class="category-card" data-category="quality">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-star"></i></span>
            <div>
              <h3 class="category-title">Content Quality</h3>
              <p class="category-sub">Credible, current, uniquely useful</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-10" data-category="quality"><span>E‑E‑A‑T signals (author, date, expertise)</span></label><span class="score-badge" id="sc-10">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-11" data-category="quality"><span>Unique value vs. top competitors</span></label><span class="score-badge" id="sc-11">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-12" data-category="quality"><span>Facts & citations up to date</span></label><span class="score-badge" id="sc-12">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-13" data-category="quality"><span>Helpful media (images/video) w/ captions</span></label><span class="score-badge" id="sc-13">—</span><button class="info"></button></li>
          </ul>
        </article>

        <!-- Structure & Architecture (4) -->
        <article class="category-card" data-category="structure">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-sitemap"></i></span>
            <div>
              <h3 class="category-title">Structure &amp; Architecture</h3>
              <p class="category-sub">Clear hierarchy and clusters</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-14" data-category="structure"><span>Logical H2/H3 headings & topic clusters</span></label><span class="score-badge" id="sc-14">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-15" data-category="structure"><span>Internal links to hub/related pages</span></label><span class="score-badge" id="sc-15">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-16" data-category="structure"><span>Clean, descriptive URL slug</span></label><span class="score-badge" id="sc-16">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-17" data-category="structure"><span>Breadcrumbs enabled (+ schema)</span></label><span class="score-badge" id="sc-17">—</span><button class="info"></button></li>
          </ul>
        </article>

        <!-- User Signals & Experience (4) -->
        <article class="category-card" data-category="ux">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-user-check"></i></span>
            <div>
              <h3 class="category-title">User Signals &amp; Experience</h3>
              <p class="category-sub">Better signals through UX</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-18" data-category="ux"><span>Mobile‑friendly, responsive layout</span></label><span class="score-badge" id="sc-18">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-19" data-category="ux"><span>Optimized speed (compression, lazy‑load)</span></label><span class="score-badge" id="sc-19">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-20" data-category="ux"><span>Core Web Vitals passing (LCP/INP/CLS)</span></label><span class="score-badge" id="sc-20">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-21" data-category="ux"><span>Clear CTAs and next steps</span></label><span class="score-badge" id="sc-21">—</span><button class="info"></button></li>
          </ul>
        </article>

        <!-- Entities & Context (4) -->
        <article class="category-card" data-category="entities">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-database"></i></span>
            <div>
              <h3 class="category-title">Entities &amp; Context</h3>
              <p class="category-sub">Knowledge‑aligned content</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-22" data-category="entities"><span>Primary entity clearly defined</span></label><span class="score-badge" id="sc-22">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-23" data-category="entities"><span>Related entities covered with context</span></label><span class="score-badge" id="sc-23">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-24" data-category="entities"><span>Valid schema markup (Article/FAQ/Product)</span></label><span class="score-badge" id="sc-24">—</span><button class="info"></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-25" data-category="entities"><span>sameAs/Organization details present</span></label><span class="score-badge" id="sc-25">—</span><button class="info"></button></li>
          </ul>
        </article>
      </div>
    </section>

    <footer>© {{ date('Y') }} Semantic SEO Master • Dark Purple + Red Smoke</footer>
  </div>

  <script>
    // Print
    document.getElementById('printTop').addEventListener('click', () => window.print());

    /* === Checklist Logic (progress + localStorage) === */
    (function () {
      const STORAGE_KEY = 'semanticSeoChecklistV2';
      const totalItems = 25;

      const checkboxes = () => Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
      const progressBar = document.getElementById('progressBar');
      const progressPercent = document.getElementById('progressPercent');
      const progressCaption = document.getElementById('progressCaption');
      const saveToast = document.getElementById('saveToast');

      const updateCategoryChips = () => {
        document.querySelectorAll('.category-card').forEach(card => {
          const cat = card.getAttribute('data-category');
          const all = card.querySelectorAll('input[data-category="'+cat+'"]');
          const done = card.querySelectorAll('input[data-category="'+cat+'"]:checked');
          card.querySelector('.checked-count').textContent = done.length;
          card.querySelector('.total-count').textContent = all.length;
        });
      };

      const updateProgress = () => {
        const checked = checkboxes().filter(cb => cb.checked).length;
        const pct = Math.round((checked / totalItems) * 100);
        progressBar.style.width = pct + '%';
        progressPercent.textContent = pct + '%';
        progressCaption.textContent = checked + ' of ' + totalItems + ' items completed';
        updateCategoryChips();
      };

      const loadState = () => {
        try {
          const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
          checkboxes().forEach(cb => { cb.checked = saved.includes(cb.id); });
        } catch (e) { console.warn('Could not parse saved checklist state.', e); }
        updateProgress();
      };

      const saveState = () => {
        const ids = checkboxes().filter(cb => cb.checked).map(cb => cb.id);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
        saveToast.hidden = false;
        clearTimeout(saveState._t);
        saveState._t = setTimeout(() => saveToast.hidden = true, 1200);
      };

      const reset = () => {
        if (!confirm('Reset the checklist? This will clear all progress.')) return;
        localStorage.removeItem(STORAGE_KEY);
        checkboxes().forEach(cb => cb.checked = false);
        // also clear scores shown
        for (let i=1;i<=25;i++){ setScoreBadge(i, null); }
        document.getElementById('overallScore').textContent = '0';
        updateProgress();
      };

      const printPage = () => window.print();

      document.addEventListener('change', (e) => {
        if (e.target.matches('#analyzer input[type="checkbox"]')) {
          updateProgress();
          saveState();
        }
      });
      document.getElementById('resetChecklist').addEventListener('click', reset);
      document.getElementById('printChecklist').addEventListener('click', printPage);

      // score badge painter
      window.setScoreBadge = (num, score) => {
        const el = document.getElementById('sc-'+num);
        if (!el) return;
        el.classList.remove('score-good','score-mid','score-bad');
        if (score===null || score===undefined){ el.textContent='—'; return; }
        el.textContent = score;
        if (score >= 80) el.classList.add('score-good');
        else if (score >= 60) el.classList.add('score-mid');
        else el.classList.add('score-bad');
      };

      loadState();
    })();

    /* === URL Analyzer Integration (auto-check + scores) === */
    (function(){
      const $ = s => document.querySelector(s);
      const setChecked = (id, on) => { const el = document.getElementById(id); if (el) el.checked = !!on; };

      async function analyze() {
        const url = $('#analyzeUrl').value.trim();
        const status = $('#analyzeStatus');
        const btn = $('#analyzeBtn');
        const report = $('#analyzeReport');
        if (!url) return;

        status.textContent = 'Analyzing…';
        btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>&nbsp;Analyzing';

        try {
          const resp = await fetch('{{ route('analyze.json') }}', {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
            body: JSON.stringify({ url })
          });
          const data = await resp.json();
          if (!data.ok) throw new Error(data.error || 'Failed');

          // Quick report
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
          $('#overallScore').textContent = data.overall_score ?? '—';
          report.style.display = 'block';

          // Paint per-item scores
          if (data.scores){
            for (let i=1;i<=25;i++){
              const key = 'ck-'+i;
              const sc = data.scores[key];
              setScoreBadge(i, sc);
            }
          }

          // Auto-apply checks for ALL items by threshold
          const threshold = 70;
          const autoIds = data.auto_check_ids || [];
          if ($('#autoApply').checked) {
            // First uncheck everything; then check as per threshold
            for (let i=1;i<=25;i++){ setChecked('ck-'+i, false); }
            autoIds.forEach(id => setChecked(id, true));
            document.dispatchEvent(new Event('change')); // triggers progress + save
          }

          status.textContent = 'Done';
          setTimeout(()=> status.textContent = '', 1500);
        } catch (e) {
          status.textContent = 'Error: ' + e.message;
        } finally {
          btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i>&nbsp;Analyze';
        }
      }

      document.getElementById('analyzeBtn').addEventListener('click', analyze);
    })();
  </script>
</body>
</html>
