{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @php
    $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
    $metaDesc  = 'Analyze any URL for content quality, entities, readability, and site speed with colorful, easy panels. Auto PageSpeed Insights with hidden API key.';
  @endphp

  <title>{{ $metaTitle }}</title>
  <meta name="description" content="{{ $metaDesc }}"/>

  <meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
  <meta property="og:title" content="{{ $metaTitle }}">
  <meta property="og:description" content="{{ $metaDesc }}">
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ request()->fullUrl() }}">
  <meta property="og:image" content="{{ asset('og-image.png') }}">
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="{{ $metaTitle }}">
  <meta name="twitter:description" content="{{ $metaDesc }}">
  <meta name="twitter:image" content="{{ asset('og-image.png') }}">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#07080e;--panel:#0f1022;--panel2:#15153a;
      --text:#f0effa;--dim:#b6b3d6;
      --good:#22c55e;--warn:#f59e0b;--bad:#ef4444;
      --c1:#3de2ff;--c2:#9b5cff;--c3:#ff2045;
      --radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55)
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;color:var(--text);font-family:Inter,ui-sans-serif,system-ui,Segoe UI,Roboto;
      background:
        radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),
        radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),
        var(--bg);
      overflow-x:hidden;
    }
    .wrap{max-width:1200px;margin:0 auto;padding:24px 5%}
    header.site{
      display:flex;align-items:center;justify-content:space-between;gap:1rem;
      padding:14px 0 20px;border-bottom:1px solid rgba(255,255,255,.08)
    }
    .brand{display:flex;align-items:center;gap:.9rem}
    .brand-badge{width:52px;height:52px;border-radius:14px;display:grid;place-items:center;
      background:linear-gradient(135deg,rgba(157,92,255,.3),rgba(61,226,255,.3));
      border:1px solid rgba(255,255,255,.18);font-size:1.2rem}
    .brand h1{margin:0;font-size:clamp(1.3rem,3.2vw,1.8rem)}
    .brand p{margin:.15rem 0 0;color:var(--dim)}
    .btn{display:inline-flex;align-items:center;gap:.55rem;padding:.55rem .9rem;border-radius:14px;
      border:1px solid rgba(255,255,255,.14);color:#fff;cursor:pointer;background:rgba(255,255,255,.06)}
    .btn:hover{background:rgba(255,255,255,.12)}
    .btn-primary{background:linear-gradient(135deg,#10b981,#22c55e);border-color:#20d391}
    .btn-mini{padding:.35rem .65rem;border-radius:10px;font-weight:800}
    .analyzer{margin-top:18px;background:var(--panel);border:1px solid rgba(255,255,255,.08);
      border-radius:20px;box-shadow:var(--shadow);padding:18px}
    .field{position:relative;border-radius:14px;background:#0b0d21;border:1px solid #20223d;padding:10px 120px 10px 44px}
    .field i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9aa0c3}
    .field input{all:unset;width:100%;color:#fff}
    .field .btn-mini{position:absolute;right:10px;top:50%;transform:translateY(-50%)}
    .status{color:var(--dim);margin-top:.45rem}
    .row{display:flex;flex-wrap:wrap;gap:.5rem}
    .chip{
      display:inline-flex;align-items:center;gap:.4rem;padding:.3rem .6rem;border-radius:999px;
      background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.14);font-weight:800
    }
    .badge{display:inline-flex;align-items:center;gap:.4rem;padding:.3rem .6rem;border-radius:999px;
      border:1px solid rgba(255,255,255,.14);background:linear-gradient(135deg,rgba(61,226,255,.2),rgba(155,92,255,.2));font-weight:900}
    .grid{display:grid;grid-template-columns:repeat(12,1fr);gap:1rem;margin-top:1rem}
    .card{grid-column:span 12;background:var(--panel2);border:1px solid rgba(255,255,255,.08);
      border-radius:16px;padding:16px;box-shadow:var(--shadow)}
    .card h3{margin:0 0 .4rem;font-size:1.05rem}
    .pill{display:inline-flex;align-items:center;gap:.4rem;padding:.25rem .6rem;border-radius:999px;
      border:1px solid rgba(255,255,255,.14);background:linear-gradient(135deg,rgba(61,226,255,.25),rgba(155,92,255,.25));font-weight:900}
    .sub{color:var(--dim);margin:.15rem 0 .6rem}
    .kpi{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center;
      padding:.65rem .75rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);
      background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.02))}
    .bar{position:relative;height:14px;border-radius:10px;overflow:hidden;background:#0b0d21;border:1px solid rgba(255,255,255,.1)}
    .fill{position:absolute;left:0;top:0;bottom:0;width:0;transition:width .6s cubic-bezier(.22,1,.36,1);
      background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e)}
    .ok{background:rgba(34,197,94,.2);border-color:rgba(34,197,94,.4)}
    .mid{background:rgba(245,158,11,.2);border-color:rgba(245,158,11,.4)}
    .no{background:rgba(239,68,68,.2);border-color:rgba(239,68,68,.45)}
    .list{display:grid;gap:.5rem}
    .mono{font-family:ui-monospace, SFMono-Regular, Menlo, Consolas, monospace}

    @media (min-width:900px){
      .span-6{grid-column:span 6}
      .span-4{grid-column:span 4}
    }
    @media print{.field,.btn,.status{display:none}}
  </style>
</head>
<body>
<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
      <div>
        <h1>Semantic SEO Master Analyzer</h1>
        <p>Human vs AI • Readability • Entities • Site Speed (API key hidden)</p>
      </div>
    </div>
    <div class="row">
      <button id="printBtn" class="btn"><i class="fa-solid fa-print"></i> Print</button>
    </div>
  </header>

  <main class="analyzer" id="analyzer" role="main">
    <h2 style="margin:0 0 .4rem">Analyze a URL</h2>
    <p class="sub">Enter a page URL and click Analyze. Site Speed auto-runs after analysis.</p>

    <div class="field">
      <i class="fa-solid fa-globe"></i>
      <input id="analyzeUrl" type="url" inputmode="url" placeholder="https://example.com/page"/>
      <button id="analyzeBtn" class="btn btn-mini btn-primary">
        <i class="fa-solid fa-magnifying-glass"></i> Analyze
      </button>
    </div>
    <div id="analyzeStatus" class="status">Idle</div>

    <!-- Summary chips -->
    <div class="row" style="margin-top:.7rem">
      <span class="badge" id="chipOverall"><i class="fa-solid fa-gauge-high"></i> Overall: <b id="overallVal">—</b></span>
      <span class="badge" id="chipContent"><i class="fa-solid fa-file-lines"></i> Content: <b id="contentVal">—</b></span>
      <span class="chip" id="chipMeta"><i class="fa-solid fa-tag"></i> Title: <b id="titleLen">—</b> • Meta: <b id="metaLen">—</b></span>
      <span class="chip" id="chipLinks"><i class="fa-solid fa-link"></i> Internal: <b id="internalLinks">—</b></span>
    </div>

    <!-- ORDER: Human vs AI → Readability → Entities → Site Speed -->

    <!-- 1) Human vs AI -->
    <section class="card" id="panelHumanAI">
      <h3><span class="pill"><i class="fa-solid fa-user-robot"></i> Human vs AI</span></h3>
      <p class="sub">Animated bar: left = Human-like, right = AI-like. “Confidence” implied by bar fill.</p>
      <div class="kpi">
        <div><i class="fa-solid fa-user-check" style="color:var(--good)"></i> Human-like</div>
        <div class="bar"><div id="humanFill" class="fill" style="background:linear-gradient(90deg,#22c55e,#10b981)"></div></div>
        <div><b id="humanPct">—</b>%</div>
      </div>
      <div class="kpi" style="margin-top:.5rem">
        <div><i class="fa-solid fa-microchip" style="color:#e11d48"></i> AI-like</div>
        <div class="bar"><div id="aiFill" class="fill" style="background:linear-gradient(90deg,#f43f5e,#a21caf)"></div></div>
        <div><b id="aiPct">—</b>%</div>
      </div>
    </section>

    <!-- 2) Readability Insights -->
    <section class="card" id="panelReadability">
      <h3><span class="pill"><i class="fa-solid fa-book-open-reader"></i> Readability Insights</span></h3>
      <p class="sub">Keep copy skimmable for most readers. <b>Target ≈ Grade 7</b>.</p>
      <div class="grid">
        <div class="span-6 kpi">
          <div><i class="fa-solid fa-font"></i> Words / Sentence</div>
          <div class="bar"><div id="wpsFill" class="fill"></div></div>
          <div><b id="wpsVal">—</b></div>
        </div>
        <div class="span-6 kpi">
          <div><i class="fa-solid fa-paragraph"></i> Estimated Grade</div>
          <div class="bar"><div id="gradeFill" class="fill"></div></div>
          <div><b id="gradeVal">—</b></div>
        </div>
      </div>
      <div class="list" style="margin-top:.6rem">
        <div class="chip"><i class="fa-solid fa-wand-magic-sparkles"></i> Use short sentences & headings.</div>
        <div class="chip"><i class="fa-solid fa-diagram-project"></i> Bullet lists for steps & features.</div>
        <div class="chip"><i class="fa-solid fa-language"></i> Prefer common words; avoid jargon.</div>
      </div>
    </section>

    <!-- 3) Entities & Topics -->
    <section class="card" id="panelEntities">
      <h3><span class="pill"><i class="fa-solid fa-database"></i> Entities & Topics</span></h3>
      <p class="sub">Suggested coverage for semantic depth (software, APKs, games, people, orgs…)</p>
      <div id="entityWrap" class="row"></div>
    </section>

    <!-- 4) Site Speed & Core Web Vitals (auto-start) -->
    <section class="card" id="panelSpeed">
      <h3><span class="pill"><i class="fa-solid fa-gauge-simple-high"></i> Site Speed & Core Web Vitals</span></h3>
      <p class="sub">Server-side proxy hides your Google API key. Runs automatically after analysis.</p>

      <div class="grid">
        <div class="span-4 kpi">
          <div><i class="fa-solid fa-bolt"></i> Performance</div>
          <div class="bar"><div id="psiPerf" class="fill"></div></div>
          <div><b id="psiPerfVal">—</b></div>
        </div>
        <div class="span-4 kpi">
          <div><i class="fa-solid fa-mobile-screen"></i> LCP (s)</div>
          <div class="bar"><div id="psiLcp" class="fill"></div></div>
          <div><b id="psiLcpVal">—</b></div>
        </div>
        <div class="span-4 kpi">
          <div><i class="fa-solid fa-person-running"></i> INP (ms)</div>
          <div class="bar"><div id="psiInp" class="fill"></div></div>
          <div><b id="psiInpVal">—</b></div>
        </div>
      </div>

      <div class="list" style="margin-top:.7rem" id="psiAdvice">
        <div class="chip"><i class="fa-solid fa-lightbulb"></i> Suggestions will appear here after the test.</div>
      </div>

      <div id="psiStatus" class="status">Speed test pending…</div>
    </section>
  </main>
</div>

{{-- Named endpoints from Laravel --}}
<script>
window.SEMSEO = window.SEMSEO || {};
window.SEMSEO.ENDPOINTS = {
  analyzeJson: @json(route('analyze.json')),
  analyze:     @json(route('analyze')),
  psiProxy:    @json(route('psi.proxy')),
};
</script>

<script>
/* ===== Utilities ===== */
const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
function normalizeUrl(u){
  if(!u) return ''; u = (u+'').trim();
  if(!/^https?:\/\//i.test(u)) u = 'https://' + u.replace(/^\/+/, '');
  try { new URL(u); return u; } catch { return ''; }
}
function setText(id, v){ const el = document.getElementById(id); if(el) el.textContent = v; }
function setFill(id, pct){
  const el = document.getElementById(id); if(!el) return;
  const p = Math.max(0, Math.min(100, Math.round(pct||0)));
  el.style.width = p + '%';
}

/* ===== PageSpeed Insights (psi-proxy) ===== */
async function runPSI(pageUrl){
  const endpoint = (window.SEMSEO?.ENDPOINTS?.psiProxy) || '/psi-proxy';
  const url = normalizeUrl(pageUrl);
  if(!url){ setText('psiStatus','PSI skipped: invalid URL'); return; }

  const qs = new URLSearchParams({ u: url, strategy:'mobile', locale:'en' });
  ['performance','accessibility','best-practices','seo','pwa'].forEach(c => qs.append('category', c));

  setText('psiStatus','Running PageSpeed Insights…');

  let res;
  try{
    res = await fetch(`${endpoint}?${qs.toString()}`, { headers:{'Accept':'application/json'} });
  }catch(e){
    console.error(e); setText('psiStatus','PSI network error.'); return;
  }
  let data;
  try{ data = await res.json(); }catch{ setText('psiStatus','Invalid PSI JSON.'); return; }

  if(!res.ok){
    setText('psiStatus', `PSI error: ${data?.error || 'Request failed'}`); return;
  }

  setText('psiStatus','Speed results loaded.');

  const lh = data?.lighthouseResult;
  const perfScore = Math.round((lh?.categories?.performance?.score || 0) * 100);
  setFill('psiPerf', perfScore);
  setText('psiPerfVal', isFinite(perfScore) ? perfScore : '—');

  // Field data (CWV) if present; fallback to lab values
  const metrics = (data?.loadingExperience?.metrics) || {};
  const lcpMsField = metrics?.LARGEST_CONTENTFUL_PAINT_MS?.percentile;
  const inpMsField = metrics?.INTERACTION_TO_NEXT_PAINT?.percentile
                  || metrics?.EXPERIMENTAL_INTERACTION_TO_NEXT_PAINT?.percentile;

  const lcpSec = lcpMsField ? (lcpMsField/1000).toFixed(2)
                 : (lh?.audits?.['largest-contentful-paint']?.numericValue ? (lh.audits['largest-contentful-paint'].numericValue/1000).toFixed(2) : null);
  const inpMs  = inpMsField ? Math.round(inpMsField)
                 : (lh?.audits?.['interactive']?.numericValue ? Math.round(lh.audits['interactive'].numericValue) : null);

  if(lcpSec){ setText('psiLcpVal', lcpSec); setFill('psiLcp', Math.max(0, Math.min(100, 100 - ((lcpSec-2.5)*25)))) }
  if(inpMs){  setText('psiInpVal', inpMs);  setFill('psiInp',  Math.max(0, Math.min(100, 100 - ((inpMs-200)/8)))) }

  // Suggestions (basic demo)
  const advice = [];
  if(perfScore < 90) advice.push('<i class="fa-solid fa-image"></i> Compress/resize hero images; serve WebP/AVIF.');
  if(lcpSec && lcpSec > 2.5) advice.push('<i class="fa-solid fa-paint-roller"></i> Improve LCP: inline critical CSS; defer non-critical JS.');
  if(inpMs && inpMs > 200) advice.push('<i class="fa-solid fa-hand-pointer"></i> Improve INP: reduce long tasks; split bundles; web workers.');

  const adv = document.getElementById('psiAdvice');
  if(adv){
    adv.innerHTML = advice.length
      ? advice.map(x => `<div class="chip">${x}</div>`).join('')
      : '<div class="chip"><i class="fa-solid fa-sparkles"></i> Looks good! Minor tweaks only.</div>';
  }
}

/* ===== Analyze flow ===== */
async function analyze(){
  const input = document.getElementById('analyzeUrl');
  const raw   = input?.value || '';
  const url   = normalizeUrl(raw);
  if(!url){ setText('analyzeStatus','Please enter a valid URL.'); input?.focus(); return; }
  window.SEMSEO.currentUrl = url;

  setText('analyzeStatus','Fetching & analyzing…');

  const endpoint = (window.SEMSEO?.ENDPOINTS?.analyzeJson) || '/analyze-json';
  const qs = new URLSearchParams({ url }).toString();

  let res, data;
  try{
    res = await fetch(`${endpoint}?${qs}`, { headers:{'Accept':'application/json'} });
    data = await res.json();
  }catch(e){
    setText('analyzeStatus','Network/JSON error.'); console.error(e); return;
  }

  if(!res.ok){
    setText('analyzeStatus', data?.error ? `Error: ${data.error}` : 'Analyze failed.');
    return;
  }

  // Summary
  setText('overallVal', data?.overall ?? '—');
  setText('contentVal', data?.contentScore ?? '—');
  setText('titleLen', data?.titleLen ?? '—');
  setText('metaLen', data?.metaLen ?? '—');
  setText('internalLinks', data?.internalLinks ?? '—');

  // Human vs AI
  const human = Number(data?.humanPct ?? NaN);
  const ai    = Number(data?.aiPct ?? NaN);
  if(isFinite(human)){ setText('humanPct', human); setFill('humanFill', human); }
  if(isFinite(ai)){    setText('aiPct', ai);       setFill('aiFill', ai); }

  // Readability (approx derivation from contentScore → proxy WPS & grade)
  const cs = Number(data?.contentScore || 0);
  const wps = (cs>=85)? 17 : (cs>=70)? 20 : (cs>=55)? 24 : 28; // rough proxy
  setText('wpsVal', wps);
  setFill('wpsFill', Math.max(0, Math.min(100, 100 - ((wps-18)*6))));
  const grade = Math.max(3, Math.min(14, Math.round(3 + (wps-12)*0.6 )));
  setText('gradeVal', `Grade ${grade}`);
  setFill('gradeFill', Math.max(0, Math.min(100, 100 - ((grade-7)*10))));

  // Entities & Topics (colorful suggestions)
  const entityWrap = document.getElementById('entityWrap');
  if(entityWrap){
    const chips = [
      { icon:'fa-brands fa-android', label:'APK / Android build', tone:'ok' },
      { icon:'fa-solid fa-gamepad',  label:'Games / Genres',      tone:'mid'},
      { icon:'fa-solid fa-screwdriver-wrench', label:'Software / Tools', tone:'ok' },
      { icon:'fa-solid fa-user-tie', label:'People / Authors',    tone:'mid'},
      { icon:'fa-solid fa-building', label:'Organizations',       tone:'ok' },
      { icon:'fa-solid fa-tags',     label:'Related Keywords',    tone:'ok' },
      { icon:'fa-solid fa-diagram-project', label:'Topic Clusters', tone:'mid'},
    ];
    entityWrap.innerHTML = chips.map(c => `
      <span class="chip ${c.tone}"><i class="${c.icon}"></i> ${c.label}</span>
    `).join('');
  }

  setText('analyzeStatus','Analysis complete. Running speed test…');

  // Auto start PageSpeed Insights for the same URL
  runPSI(window.SEMSEO.currentUrl).catch(console.error);
}

/* ===== Wire up UI ===== */
document.addEventListener('DOMContentLoaded', () => {
  document.getElementById('analyzeBtn')?.addEventListener('click', (e) => { e.preventDefault(); analyze(); });
  document.getElementById('analyzeUrl')?.addEventListener('keydown', (e) => { if(e.key==='Enter'){ e.preventDefault(); analyze(); }});
  document.getElementById('printBtn')?.addEventListener('click', () => window.print());
});
</script>
</body>
</html>
