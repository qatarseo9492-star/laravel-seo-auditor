{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Semantic SEO Master • Dark</title>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#0a0a12;
      --panel:#0e0f1a;
      --panel-2:#111323;
      --line:#1d2033;
      --text:#e7e9f3;
      --text-dim:#a6acc7;
      --text-muted:#98a1c0;
      --primary:#8b5cf6;   /* purple */
      --secondary:#22d3ee; /* cyan */
      --accent:#16a34a;    /* success */
      --danger:#ef4444;

      --radius:16px;
      --shadow:0 6px 24px rgba(0,0,0,.45);
      --shadow-hover:0 12px 34px rgba(0,0,0,.55);
      --transition:.25s ease;
      --container:1200px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Noto Sans","Helvetica Neue",Arial;
      color:var(--text);
      background: radial-gradient(1200px 800px at 10% -10%, #12122a 0%, transparent 60%),
                  radial-gradient(1000px 700px at 120% 10%, #0f1030 0%, transparent 55%),
                  var(--bg);
      overflow-x:hidden;
      -webkit-font-smoothing:antialiased;
      -moz-osx-font-smoothing:grayscale;
    }
    a{color:var(--secondary);text-decoration:none}
    a:hover{opacity:.9}

    /* ====== Purple Smoke Effect (dark) ====== */
    .bg-smoke{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
    .bg-smoke .blob{
      position:absolute; width:60vmax; height:60vmax; border-radius:50%;
      background: radial-gradient(closest-side, rgba(139,92,246,.35), rgba(139,92,246,0) 70%);
      filter: blur(60px);
      mix-blend-mode: screen;
      animation: float 30s linear infinite;
      will-change: transform;
    }
    .bg-smoke .b1{top:-15%; left:-15%; animation-duration:36s; opacity:.75}
    .bg-smoke .b2{
      bottom:-20%; right:-10%;
      background: radial-gradient(closest-side, rgba(168,85,247,.32), rgba(168,85,247,0) 70%);
      animation-duration:32s; animation-direction: reverse; opacity:.6
    }
    .bg-smoke .b3{
      top:10%; right:20%;
      background: radial-gradient(closest-side, rgba(232,121,249,.26), rgba(232,121,249,0) 70%);
      animation-duration:28s; opacity:.55
    }
    @keyframes float{
      0%   { transform: translate3d(0,0,0) rotate(0deg) }
      25%  { transform: translate3d(6%, -4%, 0) rotate(45deg) }
      50%  { transform: translate3d(-4%, 6%, 0) rotate(95deg) }
      75%  { transform: translate3d(-8%, -6%, 0) rotate(160deg) }
      100% { transform: translate3d(0,0,0) rotate(360deg) }
    }
    /* A soft gradient veil so smoke blends gently */
    .veil{
      position:fixed;inset:0;z-index:1;pointer-events:none;
      background: radial-gradient(1200px 800px at 0% 0%, rgba(139,92,246,.06), transparent 50%),
                  radial-gradient(900px 700px at 100% 10%, rgba(34,211,238,.05), transparent 45%);
    }

    /* Layout */
    .wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
    header.site{
      display:flex;align-items:center;justify-content:space-between;
      padding:14px 0 24px;border-bottom:1px solid var(--line)
    }
    .brand{display:flex;align-items:center;gap:.75rem}
    .brand-badge{
      width:44px;height:44px;border-radius:12px;display:grid;place-items:center;
      background:linear-gradient(135deg, rgba(139,92,246,.2), rgba(34,211,238,.18));
      border:1px solid rgba(255,255,255,.06); color:#d6bcfa
    }
    .brand h1{font-size:1.12rem;margin:0}
    .brand small{display:block;color:var(--text-dim)}
    .nav-actions{display:flex;gap:.5rem}
    .btn{
      border-radius:12px;border:1px solid var(--line);
      background:rgba(255,255,255,.03);color:var(--text);
      padding:.6rem .9rem;font-weight:600;cursor:pointer;transition:var(--transition)
    }
    .btn:hover{transform:translateY(-1px);background:rgba(255,255,255,.06)}
    .btn-primary{background:linear-gradient(135deg,var(--primary),#6d28d9);border-color:transparent}
    .btn-outline{background:transparent}
    .btn-danger{background:linear-gradient(135deg,#dc2626,#ef4444);border-color:transparent}

    /* Hero */
    .hero{
      display:grid;grid-template-columns:1.1fr .9fr;gap:1.5rem;align-items:center;margin:34px 0 18px;
    }
    .hero-card{
      background:linear-gradient(180deg, rgba(139,92,246,.10), rgba(34,211,238,.06));
      border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:28px;box-shadow:var(--shadow)
    }
    .hero h2{margin:0 0 .6rem;font-size:1.9rem}
    .hero p{margin:0;color:var(--text-muted)}
    .hero-badge{display:inline-flex;gap:.5rem;align-items:center;font-weight:700;color:#c4b5fd}
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
    .analyze-form input[type="url"]::placeholder{color:#6b7280}

    .progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px;position:relative}
    .progress-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem}
    .progress-label{font-weight:700;color:var(--text)}
    .progress-percent{font-weight:900;color:#a78bfa}
    .progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
    .progress-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--secondary));width:0%;transition:width .35s ease}
    .progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}
    .save-toast{position:absolute;right:10px;top:-12px;transform:translateY(-100%);background:rgba(22,163,74,.15);color:#86efac;border:1px solid rgba(134,239,172,.35);padding:.35rem .6rem;border-radius:999px;font-weight:700;box-shadow:var(--shadow)}

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
    .checklist-item{display:grid;grid-template-columns:1fr auto;gap:.5rem;align-items:start;padding:.55rem .5rem;border-radius:10px;transition:var(--transition)}
    .checklist-item + .checklist-item{margin-top:.15rem}
    .checklist-item:hover{background:rgba(255,255,255,.03)}
    .checklist-item input[type="checkbox"]{width:18px;height:18px;margin:.1rem .55rem 0 0;accent-color:var(--primary)}
    .checklist-item label{display:inline-flex;align-items:start;gap:.5rem;cursor:pointer}
    .checklist-item span{line-height:1.4}
    .info{background:transparent;border:none;color:var(--text-dim);cursor:help;padding:.2rem .35rem;border-radius:8px}
    .info:hover{color:#a78bfa;background:rgba(167,139,250,.15)}

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
  <!-- Purple smoke layers -->
  <div class="bg-smoke">
    <span class="blob b1"></span>
    <span class="blob b2"></span>
    <span class="blob b3"></span>
  </div>
  <div class="veil"></div>

  <div class="wrap">
    <header class="site">
      <div class="brand">
        <div class="brand-badge"><i class="fa-solid fa-share-nodes"></i></div>
        <div>
          <h1>Semantic SEO Master</h1>
          <small>Dark + Purple Smoke • Laravel Blade</small>
        </div>
      </div>
      <div class="nav-actions">
        <button class="btn">Docs</button>
        <button class="btn">Contact</button>
        <button class="btn btn-primary" id="printTop"><i class="fa-solid fa-print"></i>&nbsp;Print</button>
      </div>
    </header>

    <!-- Hero -->
    <section class="hero">
      <div class="hero-card">
        <span class="hero-badge"><i class="fa-solid fa-wand-magic-sparkles"></i> Semantic SEO Master Analyzer</span>
        <h2>Analyze any URL and auto‑complete your semantic SEO checklist.</h2>
        <p>25 items across 6 categories. Your progress saves locally. Print anytime.</p>
        <div class="stat">
          <div><b>25</b> checklist items</div>
          <div><b>6</b> categories</div>
          <div><b>Auto‑save</b> enabled</div>
        </div>
      </div>
      <aside class="side">
        <h3 style="margin-top:0">What’s inside</h3>
        <ul style="padding-left:1.2rem;margin:0;color:var(--text-dim)">
          <li>URL Analyzer (title/meta/canonical/robots/schema/CTAs/links…)</li>
          <li>Auto‑check supported items</li>
          <li>Progress bar + category chips</li>
          <li>Reset & Print controls</li>
          <li>Responsive dark UI</li>
        </ul>
      </aside>
    </section>

    <!-- Analyzer -->
    <section class="analyzer" id="analyzer">
      <div class="section-header analyzer-header">
        <h2 class="section-title">Semantic SEO Master Analyzer</h2>
        <p class="section-subtitle">Work through the 25‑point checklist. Use the URL Analyzer to auto‑tick what’s detected.</p>

        <!-- URL Analyzer -->
        <div class="analyze-box" style="margin-top:18px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px">
          <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
            <label for="analyzeUrl" style="display:block;font-weight:700;margin-bottom:.35rem">Analyze a URL</label>
            <div style="display:grid;grid-template-columns:1fr auto;gap:.6rem;align-items:center">
              <input id="analyzeUrl" type="url" required placeholder="https://example.com/page">
              <button id="analyzeBtn" class="btn btn-primary" style="min-width:150px"><i class="fa-solid fa-magnifying-glass"></i>&nbsp;Analyze</button>
            </div>
            <div style="display:flex;align-items:center;gap:.6rem;margin-top:.6rem">
              <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
                <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> Auto‑apply checkmarks
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
          <button class="btn btn-outline" id="resetChecklist"><i class="fas fa-rotate"></i>&nbsp;Reset</button>
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
              <p class="category-sub">Make intent‑aligned, discoverable content</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">5</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-1" data-category="contentKeywords"><span>Define search intent & primary topic</span></label><button class="info" data-tooltip="Clarify informational vs. transactional intent and the core question to answer."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-2" data-category="contentKeywords"><span>Map target & related keywords (synonyms/PAA)</span></label><button class="info" data-tooltip="Collect related terms, synonyms, & People Also Ask questions to cover topic depth."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-3" data-category="contentKeywords"><span>H1 includes primary topic naturally</span></label><button class="info" data-tooltip="Keep H1 human, descriptive, and close to the main query."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-4" data-category="contentKeywords"><span>Integrate FAQs / questions with answers</span></label><button class="info" data-tooltip="Answer common sub‑questions inline or in an FAQ block."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-5" data-category="contentKeywords"><span>Readable, NLP‑friendly language</span></label><button class="info" data-tooltip="Short sentences, plain wording, and natural phrasing improve comprehension & NLP parsing."><i class="fas fa-circle-info"></i></button></li>
          </ul>
        </article>

        <!-- Technical Elements (4) -->
        <article class="category-card" data-category="technical">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-code"></i></span>
            <div>
              <h3 class="category-title">Technical Elements</h3>
              <p class="category-sub">Ensure crawlability & SERP‑readiness</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-6" data-category="technical"><span>Title tag (≈50–60 chars) w/ primary keyword</span></label><button class="info" data-tooltip="Front‑load key terms, keep unique per page, avoid truncation."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-7" data-category="technical"><span>Meta description (≈140–160 chars) + CTA</span></label><button class="info" data-tooltip="Describe benefit, add action verb; unique & enticing."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-8" data-category="technical"><span>Canonical tag set correctly</span></label><button class="info" data-tooltip="Resolve duplicates and parameterized URLs with a preferred canonical."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-9" data-category="technical"><span>Indexable & listed in XML sitemap</span></label><button class="info" data-tooltip="No unintended noindex; ensure inclusion in XML sitemap submitted to GSC."><i class="fas fa-circle-info"></i></button></li>
          </ul>
        </article>

        <!-- Content Quality (4) -->
        <article class="category-card" data-category="quality">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-star"></i></span>
            <div>
              <h3 class="category-title">Content Quality</h3>
              <p class="category-sub">Be credible, current, & uniquely useful</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-10" data-category="quality"><span>E‑E‑A‑T signals (author, date, expertise)</span></label><button class="info" data-tooltip="Show real author, credentials, reviewed/updated date, and about/contact pages."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-11" data-category="quality"><span>Unique value vs. top competitors</span></label><button class="info" data-tooltip="Add data, templates, examples, or tools competitors lack."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-12" data-category="quality"><span>Facts & citations up to date</span></label><button class="info" data-tooltip="Link out to original sources and refresh outdated stats."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-13" data-category="quality"><span>Helpful media (images/video) w/ captions</span></label><button class="info" data-tooltip="Use descriptive captions, alt text, and correct licensing."><i class="fas fa-circle-info"></i></button></li>
          </ul>
        </article>

        <!-- Structure & Architecture (4) -->
        <article class="category-card" data-category="structure">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-sitemap"></i></span>
            <div>
              <h3 class="category-title">Structure &amp; Architecture</h3>
              <p class="category-sub">Create clear hierarchy and clusters</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-14" data-category="structure"><span>Logical H2/H3 headings & topic clusters</span></label><button class="info" data-tooltip="Each section answers a sub‑topic; cluster child pages link back to hubs."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-15" data-category="structure"><span>Internal links to hub/related pages</span></label><button class="info" data-tooltip="Use descriptive anchors; link both up (hub) and sideways (siblings)."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-16" data-category="structure"><span>Clean, descriptive URL slug</span></label><button class="info" data-tooltip="Short, lowercase, hyphenated; reflect the topic intent."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-17" data-category="structure"><span>Breadcrumbs enabled (+ schema)</span></label><button class="info" data-tooltip="Improve navigation and SERP breadcrumbs with structured data."><i class="fas fa-circle-info"></i></button></li>
          </ul>
        </article>

        <!-- User Signals & Experience (4) -->
        <article class="category-card" data-category="ux">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-user-check"></i></span>
            <div>
              <h3 class="category-title">User Signals &amp; Experience</h3>
              <p class="category-sub">Delight users to earn better signals</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-18" data-category="ux"><span>Mobile‑friendly, responsive layout</span></label><button class="info" data-tooltip="Test across breakpoints; avoid layout shifts."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-19" data-category="ux"><span>Optimized speed (compression, lazy‑load)</span></label><button class="info" data-tooltip="Compress images, defer non‑critical JS, and lazy‑load below‑the‑fold media."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-20" data-category="ux"><span>Core Web Vitals passing (LCP/INP/CLS)</span></label><button class="info" data-tooltip="Aim for green thresholds; monitor in CrUX/Field data."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-21" data-category="ux"><span>Clear CTAs and next steps</span></label><button class="info" data-tooltip="Provide obvious actions: sign up, read next, download, contact, etc."><i class="fas fa-circle-info"></i></button></li>
          </ul>
        </article>

        <!-- Entities & Context (4) -->
        <article class="category-card" data-category="entities">
          <header class="category-head">
            <span class="category-icon"><i class="fas fa-database"></i></span>
            <div>
              <h3 class="category-title">Entities &amp; Context</h3>
              <p class="category-sub">Align with knowledge graphs & meaning</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">4</span></span>
          </header>
          <ul class="checklist">
            <li class="checklist-item"><label><input type="checkbox" id="ck-22" data-category="entities"><span>Primary entity clearly defined</span></label><button class="info" data-tooltip="Identify the main ‘thing’ and define it early."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-23" data-category="entities"><span>Related entities covered with context</span></label><button class="info" data-tooltip="Mention and relate secondary entities to build topical completeness."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-24" data-category="entities"><span>Valid schema markup (Article/FAQ/Product)</span></label><button class="info" data-tooltip="Validate with Rich Results Test; keep properties accurate & consistent."><i class="fas fa-circle-info"></i></button></li>
            <li class="checklist-item"><label><input type="checkbox" id="ck-25" data-category="entities"><span>sameAs/Organization details present</span></label><button class="info" data-tooltip="Link brand to official profiles; add Organization schema."><i class="fas fa-circle-info"></i></button></li>
          </ul>
        </article>
      </div>
    </section>

    <footer>© {{ date('Y') }} Semantic SEO Master • Dark + Purple Smoke</footer>
  </div>

  <script>
    // Print
    document.getElementById('printTop').addEventListener('click', () => window.print());

    /* === Checklist Logic (progress + localStorage) === */
    (function () {
      const STORAGE_KEY = 'semanticSeoChecklistV1';
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
      loadState();
    })();

    /* === URL Analyzer Integration === */
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
          const autoIds = data.auto_check_ids || [];
          $('#rAutoCount').textContent = autoIds.length;
          report.style.display = 'block';

          if ($('#autoApply').checked) {
            autoIds.forEach(id => setChecked(id, true));
            document.dispatchEvent(new Event('change'));
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
