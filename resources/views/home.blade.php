{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Base href makes all relative URLs (including fetch('analyze')) resolve even if the app lives in a sub-folder --}}
<base href="{{ request()->getSchemeAndHttpHost() }}{{ request()->getBaseUrl() }}/">

@php
  $siteName = config('app.name', 'Semantic SEO Master');
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, and UX signals, with water-fill scoring, auto-checklist, and AI/Human signals.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
@endphp

<title>{{ $metaTitle }}</title>
<link rel="canonical" href="{{ $canonical }}">
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
/* ---- (all your existing styles kept; unchanged) ---- */
:root{--bg:#07080e;--panel:#0f1022;--panel-2:#141433;--text:#f0effa;--text-dim:#b6b3d6;--good:#22c55e;--warn:#f59e0b;--bad:#ef4444;--accent:#3de2ff;--radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55);--container:1200px;--hue:0deg}
*{box-sizing:border-box}html,body{height:100%}html{scroll-behavior:smooth}
body{margin:0;color:var(--text);font-family:Inter,ui-sans-serif,-apple-system,Segoe UI,Roboto;background:radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),var(--bg);overflow-x:hidden}
#linesCanvas,#brainCanvas{position:fixed;inset:0;pointer-events:none;z-index:0}#linesCanvas{opacity:.35}#brainCanvas{opacity:.28}
.wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
/* header, buttons, analyzer, gauge, water bars, checklist, share dock, responsive rules — unchanged from previous working version */
... /* (For brevity: keep the same CSS blocks you already had; nothing removed) */
</style>
</head>
<body>

<!-- Cloudy background canvases -->
<canvas id="linesCanvas"></canvas>
<canvas id="brainCanvas"></canvas>

<!-- Social dock (unchanged) -->
<div class="share-dock" id="shareDock" aria-label="Share">
  <a id="shareFb" class="share-btn share-fb" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-facebook-f"></i></a>
  <a id="shareX"  class="share-btn share-x"  target="_blank" rel="noopener nofollow"><i class="fa-brands fa-x-twitter"></i></a>
  <a id="shareLn" class="share-btn share-ln" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-linkedin-in"></i></a>
  <a id="shareWa" class="share-btn share-wa" target="_blank" rel="noopener nofollow"><i class="fa-brands fa-whatsapp"></i></a>
  <a id="shareEm" class="share-btn share-em" target="_blank" rel="noopener"><i class="fa-solid fa-envelope"></i></a>
</div>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge" aria-hidden="true"><i class="fa-solid fa-brain"></i></div>
      <div>
        <div class="hero-heading">Semantic SEO Master Analyzer</div>
        <div class="hero-sub">Analyze URLs, get scores & suggestions</div>
      </div>
    </div>
    <div class="header-actions">
      <button class="btn btn-print" id="printTop"><i class="fa-solid fa-print"></i> Print</button>
    </div>
  </header>

  <main class="analyzer" id="analyzer" role="main">
    <h2 class="section-title">Analyze a URL</h2>
    <p class="section-subtitle">
      The wheel fills with your overall score.
      <span class="legend l-green">Green ≥ 80</span>
      <span class="legend l-orange">Orange 60–79</span>
      <span class="legend l-red">Red &lt; 60</span>
    </p>

    <div class="score-area">
      <!-- (Overall water gauge & chips — unchanged) -->
      ... 
    </div>

    <div class="analyze-box" style="margin-top:12px;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px">
      <form id="analyzeForm" onsubmit="event.preventDefault(); analyze(); return false;">
        <label for="analyzeUrl" style="display:inline-block;font-weight:900;margin-bottom:.35rem">Page URL</label>
        <div class="url-field" id="urlField">
          <i class="fa-solid fa-globe url-icon"></i>
          <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" aria-describedby="analyzeStatus"/>
          <button type="button" class="url-mini url-clear" id="clearUrl" title="Clear"><i class="fa-solid fa-xmark"></i></button>
          <button type="button" class="url-mini" id="pasteUrl" title="Paste">Paste</button>
          <span class="url-border" aria-hidden="true"></span>
        </div>

        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:#9b5cff">
              <span>Auto-apply checkmarks (≥ 80)</span>
            </label>
          </div>

          <!-- Important: type="button" + inline fallback -->
          <button id="analyzeBtn" type="button" onclick="try{ analyze(); }catch(e){ console.error(e); }" class="btn btn-analyze">
            <i class="fa-solid fa-magnifying-glass"></i> Analyze
          </button>

          <button class="btn btn-print" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> Print</button>
          <button class="btn btn-reset" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> Reset</button>
          <button class="btn btn-export" id="exportChecklist" type="button" title="Export checklist JSON"><i class="fa-solid fa-file-export"></i> Export</button>
          <button class="btn btn-export" id="importChecklist" type="button" title="Import checklist JSON"><i class="fa-solid fa-file-import"></i> Import</button>
          <input type="file" id="importFile" accept="application/json" style="display:none">
        </div>

        <!-- Analyze water progress -->
        <div class="water-wrap" id="waterWrap" aria-hidden="true">
          <!-- (water progress SVG — unchanged) -->
          ...
          <div id="analyzeStatus" style="margin-top:.4rem;color:var(--text-dim)" aria-live="polite"></div>
        </div>

        <div id="analyzeReport" style="margin-top:.9rem;display:none">
          <!-- (chips summary — unchanged) -->
          ...
        </div>
      </form>
    </div>

    <!-- Completion bar & Checklist — unchanged -->
    ...
  </main>
</div>

<footer class="site">
  <div><strong>Semantic SEO Master</strong></div>
  <div class="footer-links">
    <a href="#analyzer">Analyzer</a>
    <a href="#" id="toTopLink">Back to top</a>
  </div>
</footer>

<button id="backTop" title="Back to top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

<script>
/* ---- same JS as before EXCEPT the fetch endpoints & base href fix ---- */

/* Hue drift, helpers, gauge, category bars, autoTick, Water, share, back-to-top, canvases — unchanged */
...

/* CSRF */
const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

/* Accepts example.com/page */
function normalizeUrl(u) {
  if (!u) return '';
  u = u.trim();
  if (/^https?:\/\//i.test(u)) {
    try { new URL(u); return u; } catch { return ''; }
  }
  const guess = 'https://' + u.replace(/^\/+/, '');
  try { new URL(guess); return guess; } catch { return ''; }
}

async function analyze(){
  const input = document.getElementById('analyzeUrl');
  let url = normalizeUrl(input.value);
  if (!url) { input.focus(); return; }

  Water.start();
  document.getElementById('analyzeStatus').textContent = 'Fetching & analyzing…';
  document.getElementById('analyzeReport').style.display = 'none';

  let data=null, ok=false, status=0, text='', lastErr='';
  const qs = new URLSearchParams({ url }).toString();

  /* 1) GET analyze-json (RELATIVE endpoint; base href ensures correct sub-folder) */
  try{
    const res = await fetch('analyze-json?'+qs, {
      method:'GET',
      headers:{ 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
    });
    status = res.status; text = await res.text();
    try{ data = JSON.parse(text); }catch{}
    if (res.ok && data) ok = true;
  }catch(e){ lastErr = 'GET analyze-json failed: '+e.message; }

  /* 2) POST analyze (RELATIVE) */
  if (!ok){
    try{
      const res = await fetch('analyze', {
        method:'POST',
        headers:{
          'Content-Type':'application/json',
          'Accept':'application/json',
          'X-Requested-With':'XMLHttpRequest',
          'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify({ url, _token: CSRF })
      });
      status = res.status; text = await res.text();
      try{ data = JSON.parse(text); }catch{}
      if (res.ok && data) ok = true;
    }catch(e){ lastErr = 'POST analyze failed: '+e.message; }
  }

  /* 3) GET analyze fallback (RELATIVE) */
  if (!ok){
    try{
      const res = await fetch('analyze?'+qs, {
        method:'GET',
        headers:{ 'Accept':'application/json', 'X-Requested-With':'XMLHttpRequest' }
      });
      status = res.status; text = await res.text();
      try{ data = JSON.parse(text); }catch{}
      if (res.ok && data) ok = true;
    }catch(e){ lastErr = 'GET analyze failed: '+e.message; }
  }

  if (!ok || !data){
    console.error('Analyze failed', status, lastErr, text?.slice?.(0,300));
    Water.finish();
    const msg = (text && text.length < 800 ? text : `Could not analyze this URL (status ${status}). ${lastErr || ''}`);
    document.getElementById('analyzeStatus').textContent = msg;
    return;
  }

  /* ---- map response to UI (unchanged) ---- */
  const overall = Number(data.overall ?? 0);
  const contentScore = Number(data.contentScore ?? 0);
  const humanPct = Number(data.humanPct ?? 0);
  const aiPct    = Number(data.aiPct ?? 0);
  const writer = humanPct>=aiPct ? 'Likely Human' : 'AI-like';

  setScoreWheel(overall||0);
  setText('contentScoreInline', Math.round(contentScore||0));
  setChipTone(document.getElementById('contentScoreChip'), contentScore||0);
  const badge = document.getElementById('aiBadge'); if (badge){ badge.querySelector('b').textContent = writer; }
  setText('humanPct', Math.round(humanPct||0));
  setText('aiPct', Math.round(aiPct||0));

  setText('rStatus', data.httpStatus ?? '—');
  setText('rTitleLen', data.titleLen ?? '—');
  setText('rMetaLen', data.metaLen ?? '—');
  setText('rCanonical', data.canonical ?? '—');
  setText('rRobots', data.robots ?? '—');
  setText('rViewport', data.viewport ?? '—');
  setText('rHeadings', data.headings ?? '—');
  setText('rInternal', data.internalLinks ?? '—');
  setText('rSchema', data.schema ?? '—');

  autoTickByScores(data.itemScores || {});
  Water.finish();
  document.getElementById('analyzeStatus').textContent = 'Analysis complete';
  document.getElementById('analyzeReport').style.display = 'block';
}

/* Wire up (unchanged) + show JS errors under progress bar */
(function(){
  const input = document.getElementById('analyzeUrl');
  const pasteBtn = document.getElementById('pasteUrl');
  const clearBtn = document.getElementById('clearUrl');
  const analyzeBtn = document.getElementById('analyzeBtn');

  pasteBtn?.addEventListener('click', async ()=>{ try{ const txt = await navigator.clipboard.readText(); if (txt){ input.value = txt.trim(); input.dispatchEvent(new Event('input')); } }catch(e){} });
  clearBtn?.addEventListener('click', ()=>{ input.value=''; input.focus(); input.dispatchEvent(new Event('input')); });
  input?.addEventListener('keydown', (e)=>{ if(e.key==='Enter'){ e.preventDefault(); analyze(); }});
  analyzeBtn?.addEventListener('click', (e)=>{ e.preventDefault(); analyze(); });

  document.getElementById('resetChecklist')?.addEventListener('click', ()=>{
    document.querySelectorAll('.checklist input[type="checkbox"]').forEach(cb=> cb.checked=false);
    document.querySelectorAll('.score-badge').forEach(b=>{ b.textContent='—'; b.classList.remove('score-good','score-mid','score-bad'); });
    updateCategoryBars(); setScoreWheel(0);
    setText('contentScoreInline', 0); setChipTone(document.getElementById('contentScoreChip'), 0);
    setText('humanPct','—'); setText('aiPct','—'); document.getElementById('aiBadge')?.querySelector('b')?.textContent='—';
    Water.reset();
  });

  // export/import/print bindings — unchanged
  ...

  window.addEventListener('error', e=>{
    const s=document.getElementById('analyzeStatus');
    if (s) s.textContent = 'JavaScript error: ' + (e?.message || e);
  });

  updateCategoryBars();
})();

/* Background canvases — unchanged */
...
</script>
</body>
</html>
