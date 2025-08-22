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
    .brand{display:flex;align-items:center;gap:.75rem}
    .brand-badge{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg, rgba(139,92,246,.2), rgba(239,68,68,.18));border:1px solid rgba(255,255,255,.06); color:#fca5a5}
    .brand small{display:block;color:var(--text-dim)}
    .btn{border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.03);color:var(--text);padding:.6rem .9rem;font-weight:600;cursor:pointer;transition:var(--transition)}
    .btn:hover{transform:translateY(-1px);background:rgba(255,255,255,.06)}
    .btn-primary{background:linear-gradient(135deg,var(--primary),#6d28d9);border-color:transparent}
    .btn-danger{background:linear-gradient(135deg,#b91c1c,#ef4444);border-color:transparent}

    /* Beautiful hero heading */
    .hero-heading{
      font-size:3rem; font-weight:900; text-align:center; line-height:1.05; margin:.2rem 0 .35rem;
      letter-spacing:.5px;
      background: linear-gradient(90deg, #c445ff, #ff0044 60%, #ff7a59 100%);
      -webkit-background-clip:text; -webkit-text-fill-color:transparent;
      text-shadow: 0 0 22px rgba(196,69,255,.18);
    }
    .hero-heading span{ filter:drop-shadow(0 10px 20px rgba(196,69,255,.15)); }
    .hero-sub{ text-align:center; color:var(--text-dim); margin:0 0 1.2rem }

    .hero{display:grid;grid-template-columns:1.1fr .9fr;gap:1.5rem;align-items:center;margin:12px 0 18px}
    .hero-card{background:linear-gradient(180deg, rgba(139,92,246,.10), rgba(239,68,68,.07));border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:28px;box-shadow:var(--shadow)}
    .side{background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:24px;box-shadow:var(--shadow)}

    .analyzer{margin-top:22px;background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:20px;box-shadow:var(--shadow);padding:28px}
    .section-title{font-size:1.5rem;margin:0 0 .3rem} .section-subtitle{margin:0;color:var(--text-dim)}

    .analyze-form input[type="url"]{width:100%;padding:.7rem .9rem;border-radius:12px;border:1px solid var(--line);background:#0b0d1d;color:var(--text)}
    .analyze-form input[type="url"]::placeholder{color:#70738f}

    /* Score wheel */
    .score-area{display:flex;gap:1rem;align-items:center;justify-content:center;margin:1rem 0 0}
    .score-container { width: 180px; }
    .score-wheel { width: 100%; height: auto; transform: rotate(-90deg); }
    .score-wheel circle { fill: none; stroke-width: 12; stroke-linecap: round; }
    .score-wheel .bg { stroke: rgba(255,255,255,.1); }
    .score-wheel .progress {
      stroke: url(#grad);
      stroke-dasharray: 339; /* 2πr where r=54 */
      stroke-dashoffset: 339;
      transition: stroke-dashoffset .6s ease;
      filter: drop-shadow(0 0 8px rgba(196,69,255,.35));
    }
    .score-text {
      font-size: 2.2rem; font-weight: 900; fill: #fff; transform: rotate(90deg);
      text-shadow: 0 0 14px rgba(255,0,68,.25);
    }

    /* Progress bar under wheel */
    .progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px;position:relative}
    .progress-meta{display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:.5rem}
    .progress-percent{font-weight:900;color:#c4b5fd}
    .overall-chip{font-weight:900;padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.12); background:rgba(139,92,246,.12)}
    .progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
    .progress-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--secondary));width:0%;transition:width .35s ease}
    .progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}
    .save-toast{position:absolute;right:10px;top:-12px;transform:translateY(-100%);background:rgba(16,185,129,.18);color:#a7f3d0;border:1px solid rgba(134,239,172,.4);padding:.35rem .6rem;border-radius:999px;font-weight:700;box-shadow:var(--shadow)}

    /* Category cards + Beautiful checklist items */
    .analyzer-grid{margin-top:1.2rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
    .category-card{grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.06);border-top:3px solid var(--primary);border-radius:16px;padding:14px;transition:var(--transition);box-shadow:var(--shadow)}
    .category-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-hover)}
    .category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
    .category-icon{width:42px;height:42px;border-radius:50%;background:rgba(139,92,246,.18);color:#c4b5fd;display:inline-flex;align-items:center;justify-content:center}
    .category-title{margin:0;font-size:1.08rem} .category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.95rem}
    .chip{padding:.2rem .55rem;border-radius:999px;font-weight:800;font-size:.85rem;background:rgba(139,92,246,.12);color:#c7d2fe;border:1px solid rgba(139,92,246,.25)}

    .checklist{list-style:none;margin:10px 0 0;padding:0}
    .checklist-item{
      display:grid;grid-template-columns:1fr auto auto;gap:.5rem;align-items:center;
      padding:.6rem .6rem;border-radius:14px;border:1px solid rgba(255,255,255,.08);
      background:linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02));
      transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .checklist-item + .checklist-item{margin-top:.25rem}
    .checklist-item:hover{transform:translateY(-2px);background:rgba(255,255,255,.05);box-shadow:0 8px 30px rgba(0,0,0,.25)}
    .checklist-item label{display:flex;align-items:flex-start;gap:.65rem;cursor:pointer}
    .checklist-item input[type="checkbox"]{width:18px;height:18px;margin:.1rem .55rem 0 0;accent-color:var(--primary)}
    .score-badge{font-weight:800;font-size:.9rem;padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;min-width:44px;text-align:center}
    .score-good{background:rgba(16,185,129,.2); border-color:rgba(16,185,129,.4)}
    .score-mid{ background:rgba(245,158,11,.2); border-color:rgba(245,158,11,.4)}
    .score-bad{ background:rgba(239,68,68,.22); border-color:rgba(239,68,68,.45)}

    .info{background:transparent;border:none;color:var(--text-dim);cursor:help;padding:.2rem .35rem;border-radius:8px}
    .info:hover{color:#fca5a5;background:rgba(239,68,68,.12)}

    /* Tips (How to reach 100) */
    .tip summary{cursor:pointer;color:#c7d2fe}
    .tip[open] summary{color:#fca5a5}
    .tip ul{margin:.35rem 0 0 .5rem;padding-left:1rem;color:var(--text-muted)}
    .tip ul li{margin:.2rem 0}

    footer{margin:28px 0 10px;color:var(--text-dim);text-align:center}
    @media (max-width:992px){ .hero{grid-template-columns:1fr} .category-card{grid-column:span 12} .score-container{width:160px} .hero-heading{font-size:2.3rem} }
    @media (prefers-reduced-motion: reduce){ .blob{animation:none} }
    @media print{header.site,.hero,footer,.bg-smoke,.veil{display:none!important} .analyzer{background:#fff;border:none;box-shadow:none;margin:0;padding:0} .category-card{border:1px solid #ddd}}
  </style>
</head>
<body>
  <!-- gradient defs for score wheel -->
  <svg width="0" height="0" aria-hidden="true" focusable="false">
    <defs>
      <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
        <stop offset="0%" stop-color="#c445ff"/>
        <stop offset="100%" stop-color="#ff0044"/>
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
        <div class="brand-badge"><i class="fa-solid fa-share-nodes"></i></div>
        <div>
          <div class="hero-heading"><span>Semantic SEO</span> Master Analyzer</div>
          <small>Dark • Purple + Red Smoke • Laravel</small>
        </div>
      </div>
      <button class="btn btn-primary" id="printTop"><i class="fa-solid fa-print"></i>&nbsp;Print</button>
    </header>

    <section class="hero">
      <div class="hero-card">
        <p class="hero-sub">Analyze any URL, auto-check 25 SEO factors, and see your score rise in real time.</p>
        <div class="score-area">
          <div class="score-container">
            <svg class="score-wheel" viewBox="0 0 120 120" role="img" aria-label="Overall score">
              <circle class="bg" cx="60" cy="60" r="54"/>
              <circle class="progress" cx="60" cy="60" r="54"/>
              <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="score-text" id="overallScore">0</text>
            </svg>
          </div>
          <div>
            <p style="margin:.25rem 0 .5rem;color:#c7d2fe;font-weight:800">Overall Score</p>
            <p style="margin:0;color:var(--text-dim);max-width:26ch">This wheel fills as your page passes checks and after URL analysis.</p>
          </div>
        </div>
      </div>
      <aside class="side">
        <h3 style="margin-top:0">What’s inside</h3>
        <ul style="padding-left:1.2rem;margin:0;color:var(--text-dim)">
          <li>Per‑item scores + “How to reach 100” tips</li>
          <li>Animated circular score wheel</li>
          <li>Auto‑check (≥ 70) & Progress saving</li>
          <li>Responsive dark UI (purple + red smoke)</li>
        </ul>
      </aside>
    </section>

    <section class="analyzer" id="analyzer">
      <h2 class="section-title">Semantic SEO Master Analyzer</h2>
      <p class="section-subtitle">Paste a URL, get scores and concrete steps to reach 100 for every item.</p>

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
            <span class="chip">Auto‑checked: <b id="rAutoCount">—</b></span>
          </div>
        </div>
      </div>

      <!-- Progress -->
      <div class="progress-wrap" aria-label="Checklist progress">
        <div class="progress-meta">
          <span class="progress-percent" id="progressPercent">0%</span>
          <span class="overall-chip">Overall Score: <span id="overallScoreInline">0</span></span>
        </div>
        <div class="progress-bar"><div class="progress-fill" id="progressBar" style="width:0%"></div></div>
        <div class="progress-caption" id="progressCaption">0 of 25 items completed</div>
        <div class="save-toast" id="saveToast" role="status" aria-live="polite" hidden><i class="fas fa-check-circle"></i> Saved</div>
        <div style="display:flex;gap:.6rem;margin-top:.8rem;justify-content:flex-end">
          <button class="btn" id="resetChecklist"><i class="fas fa-rotate"></i>&nbsp;Reset</button>
          <button class="btn btn-primary" id="printChecklist"><i class="fas fa-print"></i>&nbsp;Print</button>
        </div>
      </div>

      <!-- Categories / 25 items with improved checklist styling -->
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
              <details class="tip" id="tip-{{ $i }}" hidden><summary>How to reach 100</summary><ul></ul></details>
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
              <details class="tip" id="tip-{{ $i }}" hidden><summary>How to reach 100</summary><ul></ul></details>
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
              <details class="tip" id="tip-{{ $i }}" hidden><summary>How to reach 100</summary><ul></ul></details>
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
              <details class="tip" id="tip-{{ $i }}" hidden><summary>How to reach 100</summary><ul></ul></details>
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
              <details class="tip" id="tip-{{ $i }}" hidden><summary>How to reach 100</summary><ul></ul></details>
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
              <details class="tip" id="tip-{{ $i }}" hidden><summary>How to reach 100</summary><ul></ul></details>
            </li>
            @endfor
          </ul>
        </article>
      </div>
    </section>

    <footer>© {{ date('Y') }} Semantic SEO Master • Dark Purple + Red Smoke</footer>
  </div>

  <script>
    // Print
    document.getElementById('printTop').addEventListener('click', () => window.print());

    /* ===== Score wheel control ===== */
    const wheelCircumference = 339; // 2πr for r=54
    function setScoreWheel(value){ // 0..100
      const circle = document.querySelector('.score-wheel .progress');
      const text   = document.getElementById('overallScore');
      const offset = wheelCircumference - (Math.max(0, Math.min(100, value))/100) * wheelCircumference;
      circle.style.strokeDashoffset = offset;
      text.textContent = Math.round(value);
      document.getElementById('overallScoreInline').textContent = Math.round(value);
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
        // Wheel rises with checks too: blend analyzed overall with completion %
        const checked = checkboxes().filter(cb => cb.checked).length;
        const pct = (checked / totalItems) * 100;
        // Blend 65% analyzer score + 35% completion progress (tweakable)
        return (lastAnalyzedScore * 0.65) + (pct * 0.35);
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
        for (let i=1;i<=25;i++){ setScoreBadge(i, null); clearTips(i); }
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
      window.clearTips = (num) => {
        const box = document.getElementById('tip-'+num); if (!box) return;
        const ul = box.querySelector('ul'); if (ul) ul.innerHTML = '';
        box.hidden = true;
      };

      // Expose setter for analyzer to update the base score
      window.__setAnalyzedScore = function (v) {
        lastAnalyzedScore = Math.max(0, Math.min(100, parseFloat(v) || 0));
        setScoreWheel(blendedScore());
        document.getElementById('overallScoreInline').textContent = Math.round(lastAnalyzedScore);
      }

      loadState();
    })();

    /* ===== URL Analyzer: scores + tips + auto-check + wheel update ===== */
    (function(){
      const $ = s => document.querySelector(s);
      const setChecked = (id, on) => { const el = document.getElementById(id); if (el) el.checked = !!on; };
      const fillTips = (num, tips) => {
        const box = document.getElementById('tip-'+num);
        if (!box) return;
        const ul = box.querySelector('ul'); ul.innerHTML = '';
        (tips||[]).forEach(t => { const li = document.createElement('li'); li.textContent = t; ul.appendChild(li); });
        const scText = document.getElementById('sc-'+num)?.textContent || '—';
        if ((tips||[]).length && scText !== '100') box.hidden = false; else box.hidden = true;
      };

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

          // paint scores + tips
          for (let i=1;i<=25;i++){
            const key = 'ck-'+i;
            setScoreBadge(i, data.scores?.[key]);
            fillTips(i, data.suggestions?.[key]);
          }

          // base overall score from analyzer
          const overall = typeof data.overall_score === 'number' ? data.overall_score : 0;
          window.__setAnalyzedScore(overall);

          // auto-check by threshold
          if ($('#autoApply').checked) {
            for (let i=1;i<=25;i++) setChecked('ck-'+i, false);
            (data.auto_check_ids||[]).forEach(id => setChecked(id, true));
            document.dispatchEvent(new Event('change')); // progress + save + wheel blend
          }

          status.textContent = 'Done — apply the tips and Analyze again to reach 100.';
          setTimeout(()=> status.textContent = '', 3000);
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
