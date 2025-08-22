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
      --bg:#0b0a10; --panel:#0f0f1b; --panel-2:#151428; --line:#231f33;
      --text:#ecebf6; --text-dim:#a9a5c6; --text-muted:#9da0bf;
      --primary:#8b5cf6; --secondary:#ef4444; --accent:#22d3ee;
      --good:#10b981; --warn:#f59e0b; --bad:#ef4444;
      --radius:16px; --shadow:0 8px 28px rgba(0,0,0,.5); --shadow-hover:0 14px 40px rgba(0,0,0,.6);
      --transition:.25s ease; --container:1200px;
    }
    *{box-sizing:border-box} html,body{height:100%}
    body{
      margin:0;font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Noto Sans","Helvetica Neue",Arial;
      color:var(--text);
      background: radial-gradient(1200px 700px at 0% -10%, #1a1133 0%, transparent 55%),
                  radial-gradient(1100px 800px at 120% 0%, #1a0f22 0%, transparent 50%),
                  var(--bg);
      overflow-x:hidden; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }
    a{color:var(--accent);text-decoration:none} a:hover{opacity:.9}

    /* Smoke */
    .bg-smoke{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
    .bg-smoke .blob{
      position:absolute; width:60vmax; height:60vmax; border-radius:50%;
      filter: blur(70px); mix-blend-mode: screen; will-change: transform;
      animation: float 36s linear infinite;
    }
    .blob.purple{ background: radial-gradient(closest-side, rgba(139,92,246,.35), rgba(139,92,246,0) 70%); }
    .blob.red   { background: radial-gradient(closest-side, rgba(239,68,68,.28), rgba(239,68,68,0) 70%); }
    .b1{top:-18%; left:-15%} .b2{bottom:-22%; right:-10%; animation-direction:reverse; animation-duration:30s}
    .b3{top:10%; right:15%; animation-duration:28s} .b4{bottom:10%; left:25%; animation-duration:40s}
    @keyframes float{0%{transform:translate3d(0,0,0) rotate(0)}25%{transform:translate3d(7%,-5%,0) rotate(50deg)}50%{transform:translate3d(-6%,7%,0) rotate(110deg)}75%{transform:translate3d(-10%,-8%,0) rotate(170deg)}100%{transform:translate3d(0,0,0) rotate(360deg)}}
    .veil{position:fixed;inset:0;z-index:1;pointer-events:none;background: radial-gradient(1200px 800px at 0% 0%, rgba(139,92,246,.08), transparent 50%), radial-gradient(900px 700px at 100% 10%, rgba(239,68,68,.06), transparent 45%)}

    .wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
    header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 24px;border-bottom:1px solid var(--line);backdrop-filter:saturate(140%) blur(10px);background:rgba(15,15,27,.35)}
    .brand{display:flex;align-items:center;gap:.85rem}
    .brand-badge{width:54px;height:54px;border-radius:16px;display:grid;place-items:center;background:linear-gradient(135deg, rgba(139,92,246,.25), rgba(239,68,68,.2));border:1px solid rgba(255,255,255,.08); color:#fca5a5}

    /* Bigger, more stylish heading */
    .hero-heading{
      font-size:3.8rem; font-weight:1000; line-height:1.02; margin:.1rem 0 .2rem;
      letter-spacing:.6px; text-transform: none; text-align:left;
      background: linear-gradient(90deg, #a78bfa, #ff0044 55%, #ff7a59 100%);
      -webkit-background-clip:text; -webkit-text-fill-color:transparent;
      text-shadow: 0 0 28px rgba(196,69,255,.25);
    }
    .subline{ color:var(--text-dim); margin:0; font-size:1rem }

    .analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:22px;box-shadow:var(--shadow);padding:28px}
    .section-title{font-size:1.6rem;margin:0 0 .3rem} .section-subtitle{margin:0;color:var(--text-dim)}

    .analyze-form input[type="url"]{width:100%;padding:.8rem 1rem;border-radius:14px;border:1px solid var(--line);background:#0b0d1d;color:var(--text)}
    .analyze-form input[type="url"]::placeholder{color:#70738f}

    /* Wheel */
    .score-area{display:flex;gap:1.2rem;align-items:center;justify-content:flex-start;margin:.6rem 0 0}
    .score-container { width: 200px; }
    .score-wheel { width: 100%; height: auto; transform: rotate(-90deg); }
    .score-wheel circle { fill: none; stroke-width: 14; stroke-linecap: round; }
    .score-wheel .bg { stroke: rgba(255,255,255,.12); }
    .score-wheel .progress {
      stroke: url(#grad); stroke-dasharray: 339; stroke-dashoffset: 339;
      transition: stroke-dashoffset .6s ease, stroke .3s ease, filter .3s ease;
      filter: drop-shadow(0 0 10px rgba(196,69,255,.35));
    }
    .score-text {
      font-size: 2.6rem; font-weight: 1000; fill: #fff; transform: rotate(90deg);
      text-shadow: 0 0 18px rgba(255,0,68,.25);
    }
    .score-note { margin:.2rem 0 0; color:var(--text-dim) }

    /* Progress panel */
    .progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px;position:relative}
    .progress-meta{display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:.5rem}
    .progress-percent{font-weight:900;color:#c4b5fd}
    .overall-chip{font-weight:900;padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.12); background:rgba(139,92,246,.12)}
    .progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
    .progress-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--secondary));width:0%;transition:width .35s ease}
    .progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}
    .save-toast{position:absolute;right:10px;top:-12px;transform:translateY(-100%);background:rgba(16,185,129,.18);color:#a7f3d0;border:1px solid rgba(134,239,172,.4);padding:.35rem .6rem;border-radius:999px;font-weight:700;box-shadow:var(--shadow)}

    /* Cards + checklist */
    .analyzer-grid{margin-top:1.2rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
    .category-card{grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.06);border-top:3px solid var(--primary);border-radius:16px;padding:16px;transition:var(--transition);box-shadow:var(--shadow)}
    .category-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-hover)}
    .category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
    .category-icon{width:44px;height:44px;border-radius:50%;background:rgba(139,92,246,.18);color:#c4b5fd;display:inline-flex;align-items:center;justify-content:center}
    .category-title{margin:0;font-size:1.1rem} .category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}
    .chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;font-size:.85rem;background:rgba(139,92,246,.12);color:#c7d2fe;border:1px solid rgba(139,92,246,.25)}

    .checklist{list-style:none;margin:10px 0 0;padding:0}
    .checklist-item{
      display:grid;grid-template-columns:1fr auto auto;gap:.6rem;align-items:center;
      padding:.65rem .7rem;border-radius:14px;border:1px solid rgba(255,255,255,.08);
      background:linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02));
      transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .checklist-item + .checklist-item{margin-top:.28rem}
    .checklist-item:hover{transform:translateY(-2px);background:rgba(255,255,255,.05);box-shadow:0 8px 30px rgba(0,0,0,.25)}
    .checklist-item label{display:flex;align-items:flex-start;gap:.65rem;cursor:pointer}
    .checklist-item input[type="checkbox"]{width:18px;height:18px;margin:.1rem .55rem 0 0;accent-color:var(--primary)}
    .score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;min-width:50px;text-align:center}
    .score-good{background:rgba(16,185,129,.22); border-color:rgba(16,185,129,.45)}
    .score-mid{ background:rgba(245,158,11,.22); border-color:rgba(245,158,11,.45)}
    .score-bad{ background:rgba(239,68,68,.24); border-color:rgba(239,68,68,.5)}

    .improve-btn{border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.04);color:#fff;padding:.35rem .6rem;font-weight:800;cursor:pointer}
    .improve-btn:hover{background:rgba(255,255,255,.08)}

    /* Modal */
    .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.65);backdrop-filter:blur(4px);display:none;z-index:50}
    .modal{position:fixed;inset:0;display:none;align-items:center;justify-content:center;z-index:60}
    .modal-card{width:min(760px,92vw);background:var(--panel-2);border:1px solid rgba(255,255,255,.08);border-radius:18px;box-shadow:var(--shadow-hover);padding:20px}
    .modal-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:.6rem}
    .modal-title{margin:0;font-size:1.3rem}
    .modal-close{background:transparent;border:1px solid rgba(255,255,255,.16);border-radius:10px;color:#fff;padding:.35rem .6rem;cursor:pointer}
    .modal-body p{color:var(--text-dim);margin:.35rem 0}
    .modal-body ul{margin:.5rem 0 0 1rem;color:var(--text-muted)}
    .modal-body li{margin:.25rem 0}

    footer{margin:28px 0 10px;color:var(--text-dim);text-align:center}

    @media (max-width:992px){
      .brand-badge{width:48px;height:48px}
      .hero-heading{font-size:2.7rem}
      .score-container{width:180px}
      .category-card{grid-column:span 12}
    }
    @media (prefers-reduced-motion: reduce){ .blob{animation:none} }
    @media print{header.site,.bg-smoke,.veil,.modal,.modal-backdrop{display:none!important}}
  </style>
</head>
<body>
  <!-- gradient defs for score wheel -->
  <svg width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
      <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%" stop-color="#a78bfa"/>
        <stop offset="100%" stop-color="#ff0044"/>
      </linearGradient>
      <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%" stop-color="#22c55e"/>
        <stop offset="100%" stop-color="#16a34a"/>
      </linearGradient>
      <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%" stop-color="#f59e0b"/>
        <stop offset="100%" stop-color="#fb923c"/>
      </linearGradient>
    </defs>
  </svg>

  <div class="bg-smoke">
    <span class="blob purple b1"></span><span class="blob red b2"></span>
    <span class="blob purple b3"></span><span class="blob red b4"></span>
  </div>
  <div class="veil"></div>

  <div class="wrap">
    <header class="site">
      <div class="brand">
        <div class="brand-badge"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
        <div>
          <div class="hero-heading">Semantic SEO Master Analyzer</div>
          <p class="subline">Dark • Purple + Red Smoke • Laravel</p>
        </div>
      </div>
      <button class="btn btn-primary" id="printTop"><i class="fa-solid fa-print"></i>&nbsp;Print</button>
    </header>

    <!-- No "What's inside" section by request -->

    <section class="analyzer" id="analyzer">
      <h2 class="section-title">Semantic SEO Master Analyzer</h2>
      <p class="section-subtitle">Paste a URL, get auto-scores + full suggestions. Click “Improve” to open detailed guidance.</p>

      <!-- Score wheel row -->
      <div class="score-area">
        <div class="score-container">
          <svg class="score-wheel" viewBox="0 0 120 120" role="img" aria-label="Overall score">
            <circle class="bg" cx="60" cy="60" r="54"/>
            <circle class="progress" cx="60" cy="60" r="54"/>
            <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="score-text" id="overallScore">0</text>
          </svg>
        </div>
        <div>
          <p class="score-note">This wheel fills with your overall score. Green ≥ 80, Orange 60–79, Red &lt; 60.</p>
          <div class="chip">Overall: <b id="overallScoreInline">0</b>/100</div>
          <div class="chip" id="aiBadge">Writer: <b>—</b></div>
        </div>
      </div>

      <!-- URL Analyzer -->
      <div class="analyze-box" style="margin-top:16px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px">
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
            <span class="chip">Auto‑checked: <b id="rAutoCount">—</b></span>
          </div>
        </div>
      </div>

      <!-- Progress -->
      <div class="progress-wrap" aria-label="Checklist progress">
        <div class="progress-meta">
          <span class="progress-percent" id="progressPercent">0%</span>
          <span class="overall-chip">Overall Score: <span id="overallScoreChip">0</span></span>
        </div>
        <div class="progress-bar"><div class="progress-fill" id="progressBar" style="width:0%"></div></div>
        <div class="progress-caption" id="progressCaption">0 of 25 items completed</div>
        <div class="save-toast" id="saveToast" role="status" aria-live="polite" hidden><i class="fas fa-check-circle"></i> Saved</div>
        <div style="display:flex;gap:.6rem;margin-top:.8rem;justify-content:flex-end">
          <button class="btn" id="resetChecklist"><i class="fas fa-rotate"></i>&nbsp;Reset</button>
          <button class="btn btn-primary" id="printChecklist"><i class="fas fa-print"></i>&nbsp;Print</button>
        </div>
      </div>

      <!-- Categories / 25 items (Improve button opens modal) -->
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
            @for($i=1;$i<=5;$i++)
            <li class="checklist-item">
              <label>
                <input type="checkbox" id="ck-{{ $i }}" data-category="contentKeywords">
                <span>
                  @switch($i)
                    @case(1) Define search intent & primary topic @break
                    @case(2) Map target & related keywords (synonyms/PAA) @break
                    @case(3) H1 includes primary topic naturally @break
                    @case(4) Integrate FAQs / questions with answers @break
                    @case(5) Readable, NLP‑friendly language @break
                  @endswitch
                </span>
              </label>
              <span class="score-badge" id="sc-{{ $i }}">—</span>
              <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
            </li>
            @endfor
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
            @for($i=6;$i<=9;$i++)
            <li class="checklist-item">
              <label>
                <input type="checkbox" id="ck-{{ $i }}" data-category="technical">
                <span>
                  @switch($i)
                    @case(6) Title tag (≈50–60 chars) w/ primary keyword @break
                    @case(7) Meta description (≈140–160 chars) + CTA @break
                    @case(8) Canonical tag set correctly @break
                    @case(9) Indexable & listed in XML sitemap @break
                  @endswitch
                </span>
              </label>
              <span class="score-badge" id="sc-{{ $i }}">—</span>
              <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
            </li>
            @endfor
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
            @for($i=10;$i<=13;$i++)
            <li class="checklist-item">
              <label>
                <input type="checkbox" id="ck-{{ $i }}" data-category="quality">
                <span>
                  @switch($i)
                    @case(10) E‑E‑A‑T signals (author, date, expertise) @break
                    @case(11) Unique value vs. top competitors @break
                    @case(12) Facts & citations up to date @break
                    @case(13) Helpful media (images/video) w/ captions @break
                  @endswitch
                </span>
              </label>
              <span class="score-badge" id="sc-{{ $i }}">—</span>
              <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
            </li>
            @endfor
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
            @for($i=14;$i<=17;$i++)
            <li class="checklist-item">
              <label>
                <input type="checkbox" id="ck-{{ $i }}" data-category="structure">
                <span>
                  @switch($i)
                    @case(14) Logical H2/H3 headings & topic clusters @break
                    @case(15) Internal links to hub/related pages @break
                    @case(16) Clean, descriptive URL slug @break
                    @case(17) Breadcrumbs enabled (+ schema) @break
                  @endswitch
                </span>
              </label>
              <span class="score-badge" id="sc-{{ $i }}">—</span>
              <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
            </li>
            @endfor
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
            @for($i=18;$i<=21;$i++)
            <li class="checklist-item">
              <label>
                <input type="checkbox" id="ck-{{ $i }}" data-category="ux">
                <span>
                  @switch($i)
                    @case(18) Mobile‑friendly, responsive layout @break
                    @case(19) Optimized speed (compression, lazy‑load) @break
                    @case(20) Core Web Vitals passing (LCP/INP/CLS) @break
                    @case(21) Clear CTAs and next steps @break
                  @endswitch
                </span>
              </label>
              <span class="score-badge" id="sc-{{ $i }}">—</span>
              <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
            </li>
            @endfor
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
            @for($i=22;$i<=25;$i++)
            <li class="checklist-item">
              <label>
                <input type="checkbox" id="ck-{{ $i }}" data-category="entities">
                <span>
                  @switch($i)
                    @case(22) Primary entity clearly defined @break
                    @case(23) Related entities covered with context @break
                    @case(24) Valid schema markup (Article/FAQ/Product) @break
                    @case(25) sameAs/Organization details present @break
                  @endswitch
                </span>
              </label>
              <span class="score-badge" id="sc-{{ $i }}">—</span>
              <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
            </li>
            @endfor
          </ul>
        </article>
      </div>
    </section>

    <footer>© {{ date('Y') }} Semantic SEO Master • Dark Purple + Red Smoke</footer>
  </div>

  <!-- Modal -->
  <div class="modal-backdrop" id="modalBackdrop"></div>
  <div class="modal" id="tipModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title" id="modalTitle">Improve</h3>
        <button class="modal-close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="modal-body">
        <p id="modalIntro">Follow these steps to raise this item’s score. Re‑Analyze after applying.</p>
        <ul id="modalList"></ul>
      </div>
    </div>
  </div>

  <script>
    // Print
    document.getElementById('printTop').addEventListener('click', () => window.print());

    /* ===== Score wheel control ===== */
    const WHEEL = { circumference: 339, circle: null, text: null };
    function setScoreWheel(value){ // 0..100
      if (!WHEEL.circle) {
        WHEEL.circle = document.querySelector('.score-wheel .progress');
        WHEEL.text   = document.getElementById('overallScore');
      }
      const v = Math.max(0, Math.min(100, value));
      const offset = WHEEL.circumference - (v/100) * WHEEL.circumference;
      WHEEL.circle.style.strokeDashoffset = offset;
      // Color by thresholds
      if (v >= 80) WHEEL.circle.setAttribute('stroke','url(#gradGood)');
      else if (v >= 60) WHEEL.circle.setAttribute('stroke','url(#gradMid)');
      else WHEEL.circle.setAttribute('stroke','url(#grad)');
      WHEEL.text.textContent = Math.round(v);
      document.getElementById('overallScoreInline').textContent = Math.round(v);
      document.getElementById('overallScoreChip').textContent = Math.round(v);
    }
    setScoreWheel(0);

    /* ===== Checklist: progress + localStorage + influence score ===== */
    (function () {
      const STORAGE_KEY = 'semanticSeoChecklistV2';
      const totalItems = 25;
      const checkboxes = () => Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
      const progressBar = document.getElementById('progressBar');
      const progressPercent = document.getElementById('progressPercent');
      const progressCaption = document.getElementById('progressCaption');
      const saveToast = document.getElementById('saveToast');

      let lastAnalyzedScore = 0; // server overall_score after Analyze

      const updateCategoryChips = () => {
        document.querySelectorAll('.category-card').forEach(card => {
          const cat = card.getAttribute('data-category');
          const all = card.querySelectorAll('input[data-category="'+cat+'"]');
          const done = card.querySelectorAll('input[data-category="'+cat+'"]:checked');
          card.querySelector('.checked-count').textContent = done.length;
          card.querySelector('.total-count').textContent = all.length;
        });
      };

      function blendedScore() {
        const checked = checkboxes().filter(cb => cb.checked).length;
        const pct = (checked / totalItems) * 100;
        return (lastAnalyzedScore * 0.7) + (pct * 0.3);
      }

      const updateProgress = () => {
        const checked = checkboxes().filter(cb => cb.checked).length;
        const pct = Math.round((checked / totalItems) * 100);
        progressBar.style.width = pct + '%';
        progressPercent.textContent = pct + '%';
        progressCaption.textContent = checked + ' of ' + totalItems + ' items completed';
        updateCategoryChips();
        setScoreWheel(blendedScore());
      };

      const loadState = () => {
        try {
          const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '[]');
          checkboxes().forEach(cb => { cb.checked = saved.includes(cb.id); });
        } catch (e) {}
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
        for (let i=1;i<=25;i++){ setScoreBadge(i, null); }
        lastAnalyzedScore = 0;
        setScoreWheel(0);
        updateProgress();
      };

      document.addEventListener('change', (e) => {
        if (e.target.matches('#analyzer input[type="checkbox"]')) {
          updateProgress(); saveState();
        }
      });
      document.getElementById('resetChecklist').addEventListener('click', reset);
      document.getElementById('printChecklist').addEventListener('click', () => window.print());

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

      // Expose setter for analyzer to update the base score
      window.__setAnalyzedScore = function (v) {
        lastAnalyzedScore = Math.max(0, Math.min(100, parseFloat(v) || 0));
        setScoreWheel(blendedScore());
      }

      loadState();
    })();

    /* ===== Modal logic (Improve buttons) ===== */
    (function(){
      const backdrop = document.getElementById('modalBackdrop');
      const modal = document.getElementById('tipModal');
      const title = document.getElementById('modalTitle');
      const list = document.getElementById('modalList');
      const closeBtn = document.getElementById('modalClose');

      function openModal(id, tips){
        title.textContent = 'Improve: ' + labelFor(id);
        list.innerHTML = '';
        (tips || ['Looks good—minor polishing only.']).forEach(t => {
          const li = document.createElement('li'); li.textContent = t; list.appendChild(li);
        });
        backdrop.style.display = 'block';
        modal.style.display = 'flex';
      }
      function closeModal(){
        backdrop.style.display = 'none';
        modal.style.display = 'none';
      }
      closeBtn.addEventListener('click', closeModal);
      backdrop.addEventListener('click', closeModal);
      document.addEventListener('keydown', (e)=>{ if(e.key==='Escape') closeModal(); });

      function labelFor(id){
        const el = document.querySelector('label[for="'+id+'"]');
        if (el) return el.textContent.trim();
        // fallback: try to read sibling label text
        const input = document.getElementById(id);
        if (input) {
          const span = input.parentElement?.querySelector('span');
          if (span) return span.textContent.trim();
        }
        return id;
      }

      document.addEventListener('click', (e)=>{
        const btn = e.target.closest('.improve-btn');
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        // collect tips already rendered under each item? We fill tips from last Analyze call cache.
        // We'll store suggestions in window.__lastSuggestions
        const tips = (window.__lastSuggestions && window.__lastSuggestions[id]) ? window.__lastSuggestions[id] : ['Analyze the URL first to generate suggestions.'];
        openModal(id, tips);
      });
    })();

    /* ===== URL Analyzer: scores + tips + auto-check + wheel update + AI badge ===== */
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

          // top chips
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
          report.style.display = 'block';

          // paint scores + capture tips
          window.__lastSuggestions = data.suggestions || {};
          for (let i=1;i<=25;i++){
            const key = 'ck-'+i;
            setScoreBadge(i, data.scores?.[key]);
          }

          // base overall score from analyzer
          const overall = typeof data.overall_score === 'number' ? data.overall_score : 0;
          window.__setAnalyzedScore(overall);

          // AI/Human badge
          const ai = data.ai_detection || {};
          const badge = document.getElementById('aiBadge');
          const labelMap = { likely_human: 'Likely Human', mixed: 'Mixed', likely_ai: 'Likely AI' };
          const label = labelMap[ai.label] || 'Unknown';
          const conf = (typeof ai.likelihood==='number') ? `(${ai.likelihood}%)` : '';
          badge.innerHTML = `Writer: <b>${label} ${conf}</b>`;
          if (ai.label === 'likely_ai') badge.style.background='rgba(239,68,68,.18)';
          else if (ai.label === 'mixed') badge.style.background='rgba(245,158,11,.18)';
          else badge.style.background='rgba(16,185,129,.18)';
          badge.title = (ai.reasons||[]).join(' • ');

          // auto-check by threshold
          if ($('#autoApply').checked) {
            for (let i=1;i<=25;i++) setChecked('ck-'+i, false);
            (data.auto_check_ids||[]).forEach(id => setChecked(id, true));
            document.dispatchEvent(new Event('change')); // progress + save + wheel blend
          }

          status.textContent = 'Done — click “Improve” on any item for detailed steps, then re‑Analyze.';
          setTimeout(()=> status.textContent = '', 3200);
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
