\
<!-- resources/views/home.blade.php (Full Ultra v11) -->
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Semantic SEO Checker — Analyzer</title>

  <!-- Laravel CSRF + Analyze endpoint (primary + fallback) -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="analyze-endpoint" content="{{ route('ajax.analyze.url') }}">

  <!-- Icons / fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <script src="https://cdn.lordicon.com/lordicon.js"></script>

  <style>
    :root{
      --bg:#080a12; --panel:rgba(255,255,255,.06); --ink:#eaf1ff; --muted:#9fb1d9; --border:rgba(255,255,255,.14);
      --a1:#7c4dff; --a2:#00e5ff; --a3:#ff4dd2; --good:#22c55e; --mid:#f59e0b; --bad:#ef4444;
    }
    *{box-sizing:border-box} html,body{margin:0;padding:0}
    body{background:var(--bg);color:var(--ink);font-family:Inter,ui-sans-serif,system-ui,Arial;overflow-x:hidden}

    /* Ambient background */
    .bg{
      position:fixed; inset:0; z-index:-1;
      background:
        radial-gradient(900px 520px at 10% -10%, rgba(124,77,255,.22), transparent 60%),
        radial-gradient(900px 520px at 90% -10%, rgba(0,229,255,.18), transparent 60%),
        radial-gradient(900px 520px at 50% 110%, rgba(255,77,210,.14), transparent 60%),
        linear-gradient(180deg, #0a0b16 0%, #070811 100%);
    }

    /* Superbar (sticky menu) */
    .superbar{position:sticky;top:0;background:rgba(8,10,18,.65);backdrop-filter:blur(10px);border-bottom:1px solid var(--border);z-index:50}
    .superbar .wrap{max-width:1200px;margin:0 auto;padding:10px 14px;display:flex;align-items:center;gap:12px}
    .brand{display:flex;align-items:center;gap:.7rem;font-weight:800;letter-spacing:.25px}
    .brand .logo{width:38px;height:38px;border-radius:12px;display:grid;place-items:center;
      background:linear-gradient(135deg,var(--a1),var(--a2));box-shadow:0 10px 26px rgba(0,229,255,.28);color:#061018}
    .brand .title{font-size:1.05rem}
    .nav{display:flex;gap:8px;flex:1;justify-content:center;flex-wrap:wrap}
    .nav a{display:inline-flex;align-items:center;gap:.55rem;padding:10px 12px;border-radius:12px;text-decoration:none;
      color:var(--ink);border:1px solid var(--border);background:var(--panel);transition:.18s ease}
    .nav a:hover{border-color:transparent;background:linear-gradient(135deg,rgba(124,77,255,.22),rgba(0,229,255,.18));transform:translateY(-1px)}
    .auth{display:flex;gap:8px;align-items:center}
    .btn{display:inline-flex;align-items:center;gap:.55rem;padding:10px 12px;border-radius:12px;border:1px solid var(--border);text-decoration:none;color:var(--ink);background:var(--panel)}
    .btn.primary{border-color:transparent;background:linear-gradient(135deg,var(--a3),var(--a2));color:#061018;box-shadow:0 14px 36px rgba(255,77,210,.28)}
    .btn.warn{border-color:#3a1111;background:linear-gradient(135deg,#ef4444,#f59e0b);color:#1a0b0b}

    /* Page grid */
    .container{max-width:1200px;margin:0 auto;padding:20px 14px 56px}
    .hero{display:grid;grid-template-columns:1.1fr .9fr;gap:16px;margin-top:14px}
    @media (max-width: 980px){ .hero{grid-template-columns:1fr} }
    .card{border:1px solid var(--border);border-radius:18px;background:var(--panel);backdrop-filter: blur(8px);box-shadow:0 18px 50px rgba(0,0,0,.45)}
    .card .hd{display:flex;align-items:center;gap:.6rem;padding:14px 14px 8px 14px}
    .card .sub{color:var(--muted);font-size:.92rem}
    .card .bd{padding:14px}

    /* Input row */
    .row{display:flex;gap:10px;flex-wrap:wrap}
    input[type="text"]{flex:1;min-width:280px;border-radius:14px;border:1px solid var(--border);background:var(--panel);color:var(--ink);padding:12px 14px;outline:none}
    input[type="text"]::placeholder{color:#c7d3ef66}

    /* Score wheel */
    .wheel{width:220px;height:220px;margin:0 auto;position:relative}
    .wheel svg{width:100%;height:100%}
    .wheel .num{position:absolute;inset:0;display:grid;place-items:center;font-size:38px;font-weight:800}

    /* Categories grid */
    .cat-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:12px}
    @media (max-width: 1100px){ .cat-grid{grid-template-columns:repeat(2,1fr)} }
    @media (max-width: 720px){ .cat-grid{grid-template-columns:1fr} }
    .category-card .category-sub{opacity:.8}
    .checklist{list-style:none;margin:10px 0 0;padding:0;display:grid;gap:6px}
    .checklist-item{border:1px dashed var(--border);border-radius:12px;padding:8px 10px;display:flex;align-items:center;gap:8px}
    .checklist-item.sev-good{border-color:rgba(34,197,94,.6);background:rgba(34,197,94,.06)}
    .checklist-item.sev-mid{border-color:rgba(245,158,11,.6);background:rgba(245,158,11,.06)}
    .checklist-item.sev-bad{border-color:rgba(239,68,68,.6);background:rgba(239,68,68,.06)}
    .badge{min-width:34px;height:28px;border-radius:10px;display:grid;place-items:center;font-weight:700;border:1px solid var(--border);background:var(--panel)}
    .badge.score-good{background:rgba(34,197,94,.15);border-color:rgba(34,197,94,.45)}
    .badge.score-mid{background:rgba(245,158,11,.15);border-color:rgba(245,158,11,.45)}
    .badge.score-bad{background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.45)}

    /* Status HUD */
    .analyze-hud{position:fixed;right:14px;bottom:14px;background:rgba(20,24,36,.9);color:var(--ink);
      border:1px solid rgba(255,255,255,.12);border-radius:12px;padding:10px 12px;z-index:9999;
      box-shadow:0 10px 30px rgba(0,0,0,.4);font:500 14px/1.4 Inter,ui-sans-serif,system-ui,Arial}
    .analyze-hud .dot{display:inline-block;width:8px;height:8px;border-radius:50%;margin-right:8px;background:#7c4dff;vertical-align:baseline}
  </style>
</head>
<body>
<div class="bg"></div>

<script>window.APP = { loggedIn: @auth true @else false @endauth };</script>

<!-- Superbar (main menu above hero) -->
<div class="superbar">
  <div class="wrap">
    <div class="brand">
      <div class="logo"><i class="fa-solid fa-sparkles"></i></div>
      <div class="title">Semantic SEO Checker</div>
    </div>
    <nav class="nav">
      <a href="{{ route('home') }}"><i class="fa-solid fa-magnifying-glass-chart"></i> Semantic SEO Analyzer</a>
      <a href="{{ route('seo.topic-clusters.create') }}"><i class="fa-solid fa-diagram-project"></i> Topic Cluster Identification</a>
    </nav>
    <div class="auth">
      @auth
        <a class="btn" href="{{ route('dashboard') }}"><i class="fa-solid fa-gauge"></i> Dashboard</a>
        <a class="btn" href="{{ route('account.show') }}"><i class="fa-solid fa-user-gear"></i> Account</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline">
          @csrf
          <button class="btn warn" type="submit"><i class="fa-solid fa-power-off"></i> Logout</button>
        </form>
      @else
        <a class="btn" href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
        <a class="btn primary" href="{{ route('register') }}"><i class="fa-solid fa-user-plus"></i> Signup</a>
      @endauth
    </div>
  </div>
</div>

<div class="container">
  <!-- HERO: Analyze & Score -->
  <div class="hero">
    <div class="card">
      <div class="hd">
        <lord-icon src="https://cdn.lordicon.com/kkvxgpti.json" trigger="loop" delay="1500" style="width:28px;height:28px"></lord-icon>
        <div>Analyze a URL</div>
        <div class="sub" style="margin-left:auto;color:var(--muted)">Server-side fetch • CORS-safe</div>
      </div>
      <div class="bd">
        <div class="row">
          <input id="analyzeUrl" type="text" placeholder="https://example.com/article" autocomplete="off">
          <button id="analyzeBtn" class="btn primary"><i class="fa-solid fa-wand-magic-sparkles"></i> Analyze</button>
        </div>
        <div style="opacity:.8;margin-top:8px">We extract key signals (title, meta, headings, content length, links, images, schema) and update your checklist + score.</div>
      </div>
    </div>

    <div class="card">
      <div class="hd">
        <lord-icon src="https://cdn.lordicon.com/gqdnbnwt.json" trigger="loop" delay="1600" style="width:28px;height:28px"></lord-icon>
        <div>Content Score</div>
      </div>
      <div class="bd">
        <div class="wheel">
          <svg viewBox="0 0 120 120">
            <defs>
              <linearGradient id="g" x1="0" x2="1" y1="0" y2="0">
                <stop offset="0%" stop-color="#ff4dd2"/><stop offset="100%" stop-color="#00e5ff"/>
              </linearGradient>
            </defs>
            <circle cx="60" cy="60" r="50" stroke="rgba(255,255,255,.12)" stroke-width="12" fill="none"/>
            <circle id="wheelArc" cx="60" cy="60" r="50" stroke="url(#g)" stroke-width="12" fill="none"
                    stroke-linecap="round" stroke-dasharray="0 314" transform="rotate(-90 60 60)"/>
          </svg>
          <div class="num"><span id="contentScoreInline">0</span>%</div>
        </div>
      </div>
    </div>
  </div>

  <!-- CATEGORIES / CHECKLIST -->
  <div class="cat-grid">
    <!-- Category 0 -->
    <div class="card category-card">
      <div class="hd"><div>On‑Page Basics</div><div style="margin-left:auto" id="catPct-0">0/6 • 0%</div></div>
      <div class="bd">
        <ul class="checklist">
          <li class="checklist-item"><input id="ck-1" type="checkbox"><span class="badge" id="sc-1">—</span> Unique, descriptive title tag (30–65 chars)</li>
          <li class="checklist-item"><input id="ck-2" type="checkbox"><span class="badge" id="sc-2">—</span> Meta description (80–160 chars)</li>
          <li class="checklist-item"><input id="ck-3" type="checkbox"><span class="badge" id="sc-3">—</span> Single H1, matches intent</li>
          <li class="checklist-item"><input id="ck-4" type="checkbox"><span class="badge" id="sc-4">—</span> Helpful H2 subheadings</li>
          <li class="checklist-item"><input id="ck-5" type="checkbox"><span class="badge" id="sc-5">—</span> Word count fits topic depth</li>
          <li class="checklist-item"><input id="ck-6" type="checkbox"><span class="badge" id="sc-6">—</span> Clear, readable structure</li>
        </ul>
      </div>
    </div>
    <!-- Category 1 -->
    <div class="card category-card">
      <div class="hd"><div>Links & Structure</div><div style="margin-left:auto" id="catPct-1">0/6 • 0%</div></div>
      <div class="bd">
        <ul class="checklist">
          <li class="checklist-item"><input id="ck-7" type="checkbox"><span class="badge" id="sc-7">—</span> Internal links (≥ 3)</li>
          <li class="checklist-item"><input id="ck-8" type="checkbox"><span class="badge" id="sc-8">—</span> External citations (≥ 1)</li>
          <li class="checklist-item"><input id="ck-9" type="checkbox"><span class="badge" id="sc-9">—</span> Canonical tag present</li>
          <li class="checklist-item"><input id="ck-10" type="checkbox"><span class="badge" id="sc-10">—</span> No duplicate titles/H1s</li>
          <li class="checklist-item"><input id="ck-11" type="checkbox"><span class="badge" id="sc-11">—</span> Logical heading hierarchy</li>
          <li class="checklist-item"><input id="ck-12" type="checkbox"><span class="badge" id="sc-12">—</span> Relevant anchor text</li>
        </ul>
      </div>
    </div>
    <!-- Category 2 -->
    <div class="card category-card">
      <div class="hd"><div>Media & Schema</div><div style="margin-left:auto" id="catPct-2">0/6 • 0%</div></div>
      <div class="bd">
        <ul class="checklist">
          <li class="checklist-item"><input id="ck-13" type="checkbox"><span class="badge" id="sc-13">—</span> At least one relevant image</li>
          <li class="checklist-item"><input id="ck-14" type="checkbox"><span class="badge" id="sc-14">—</span> Alt text on images</li>
          <li class="checklist-item"><input id="ck-15" type="checkbox"><span class="badge" id="sc-15">—</span> JSON‑LD structured data</li>
          <li class="checklist-item"><input id="ck-16" type="checkbox"><span class="badge" id="sc-16">—</span> Media supports content</li>
          <li class="checklist-item"><input id="ck-17" type="checkbox"><span class="badge" id="sc-17">—</span> Mobile‑friendly layout</li>
          <li class="checklist-item"><input id="ck-18" type="checkbox"><span class="badge" id="sc-18">—</span> Performance optimized</li>
        </ul>
      </div>
    </div>
    <!-- Category 3 -->
    <div class="card category-card">
      <div class="hd"><div>Trust & UX</div><div style="margin-left:auto" id="catPct-3">0/6 • 0%</div></div>
      <div class="bd">
        <ul class="checklist">
          <li class="checklist-item"><input id="ck-19" type="checkbox"><span class="badge" id="sc-19">—</span> Author or brand credibility</li>
          <li class="checklist-item"><input id="ck-20" type="checkbox"><span class="badge" id="sc-20">—</span> Contact/links to about page</li>
          <li class="checklist-item"><input id="ck-21" type="checkbox"><span class="badge" id="sc-21">—</span> Ads not disruptive</li>
          <li class="checklist-item"><input id="ck-22" type="checkbox"><span class="badge" id="sc-22">—</span> Secure (HTTPS)</li>
          <li class="checklist-item"><input id="ck-23" type="checkbox"><span class="badge" id="sc-23">—</span> No intrusive interstitials</li>
          <li class="checklist-item"><input id="ck-24" type="checkbox"><span class="badge" id="sc-24">—</span> Accessibility basics</li>
        </ul>
      </div>
    </div>
    <!-- Category 4 -->
    <div class="card category-card">
      <div class="hd"><div>Topical Signals</div><div style="margin-left:auto" id="catPct-4">0/6 • 0%</div></div>
      <div class="bd">
        <ul class="checklist">
          <li class="checklist-item"><input id="ck-25" type="checkbox"><span class="badge" id="sc-25">—</span> Strong topical focus & keywords</li>
          <li class="checklist-item"><input id="ck-26" type="checkbox"><span class="badge" id="sc-26">—</span> Entities mentioned where relevant</li>
          <li class="checklist-item"><input id="ck-27" type="checkbox"><span class="badge" id="sc-27">—</span> Satisfies search intent</li>
          <li class="checklist-item"><input id="ck-28" type="checkbox"><span class="badge" id="sc-28">—</span> Helpful outbound references</li>
          <li class="checklist-item"><input id="ck-29" type="checkbox"><span class="badge" id="sc-29">—</span> Freshness / updated timestamps</li>
          <li class="checklist-item"><input id="ck-30" type="checkbox"><span class="badge" id="sc-30">—</span> Clear next steps / UX path</li>
        </ul>
      </div>
    </div>
  </div>
</div>

<!-- Status HUD -->
<div id="analyzeStatus" class="analyze-hud" style="display:none"><span class="dot"></span><span class="t">Ready</span></div>

<script>
// ---------- Utilities ----------
function q(sel,ctx){ try{ return (ctx||document).querySelector(sel); }catch(e){ return null; } }
function qa(sel,ctx){ try{ return Array.prototype.slice.call((ctx||document).querySelectorAll(sel)); }catch(e){ return []; } }
function setText(id,val){ var el=document.getElementById(id); if(el) el.textContent=String(val); }
function normalizeUrl(v){ v=(v||'').trim(); if(!v) return ''; if(!/^https?:\/\//i.test(v)) v='https://'+v; return v; }

// HUD
window.AnalyzeHUD = (function(){
  var box = q('#analyzeStatus'); var text = box ? q('.t', box) : null;
  return { show: function(msg){ if(!box) return; box.style.display='block'; if(text) text.textContent=msg; },
           hide: function(){ if(!box) return; box.style.display='none'; } };
})();

// Score wheel
window.setScoreWheel = function(value){
  try {
    value = Math.max(0, Math.min(100, parseInt(value||0,10)));
    setText('contentScoreInline', value);
    var arc = document.getElementById('wheelArc');
    var C = 2*Math.PI*50; // circumference for r=50
    var dash = Math.round(C * (value/100));
    if (arc) arc.setAttribute('stroke-dasharray', dash + ' ' + (C-dash));
  } catch(e){}
};

// Recompute category meters from checkboxes
function updateCategoryBars(){
  try{
    var cards=[].slice.call(document.querySelectorAll('.category-card'));
    cards.forEach(function(card,idx){
      var inputs=[].slice.call(card.querySelectorAll('.checklist input[type="checkbox"]'));
      var t=inputs.length, done=inputs.filter(function(c){ return c && c.checked; }).length;
      var pct=t?Math.round(done*100/t):0;
      var pctEl=document.getElementById('catPct-'+idx); if(pctEl) pctEl.textContent = done+'/'+t+' • '+pct+'%';
    });
  }catch(e){}
}

// ---------- Analyze flow ----------
async function analyze(){
  // require login if gated
  if (window.APP && window.APP.loggedIn === false) {
    AnalyzeHUD.show('Please login to analyze.'); return;
  }

  AnalyzeHUD.show('Analyzing…');
  var input = document.getElementById('analyzeUrl');
  var url = normalizeUrl(input ? input.value : '');
  if(!url){ AnalyzeHUD.show('Please enter a valid URL'); return; }

  // endpoint from meta (safer than hardcoding)
  var meta = q('meta[name="analyze-endpoint"]');
  var ANALYZE_URL = (meta && meta.content) ? meta.content : '{{ route('ajax.analyze.url') }}';

  const csrf = q('meta[name="csrf-token"]')?.getAttribute('content') || '';
  let data = null;
  try {
    const resp = await fetch(ANALYZE_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf },
      credentials: 'same-origin',
      body: JSON.stringify({ url })
    });
    const ctype = resp.headers.get('Content-Type') || '';
    if (!resp.ok) {
      throw new Error(resp.status === 401 ? 'Please login to analyze.' :
                      resp.status === 419 ? 'Session expired. Refresh and login again.' :
                      'Analyze failed ('+resp.status+').');
    }
    if (ctype.indexOf('application/json') === -1) {
      throw new Error('Please login to analyze this URL.');
    }
    data = await resp.json();
  } catch (e) {
    AnalyzeHUD.show((e && e.message) ? e.message : 'Failed to analyze URL'); return;
  }

  try {
    // Apply item scores to first 30 checklist items (expand as needed)
    if (data && data.itemScores) {
      Object.keys(data.itemScores).forEach(function(k){
        var idx = parseInt(k,10);
        var sc = data.itemScores[k];
        var badge = document.getElementById('sc-'+idx);
        var row = document.getElementById('ck-'+idx)?.closest('.checklist-item');
        if (badge && !isNaN(sc)) {
          badge.textContent = sc;
          badge.classList.remove('score-good','score-mid','score-bad');
          if (sc >= 80) badge.classList.add('score-good');
          else if (sc >= 60) badge.classList.add('score-mid');
          else badge.classList.add('score-bad');
        }
        if (row && !isNaN(sc)) {
          row.classList.remove('sev-good','sev-mid','sev-bad');
          if (sc >= 80) row.classList.add('sev-good');
          else if (sc >= 60) row.classList.add('sev-mid');
          else row.classList.add('sev-bad');
          var cb = row.querySelector('input[type="checkbox"]');
          if (cb && sc >= 80) cb.checked = true;
        }
      });
    }
    // Update wheel (prefer overall, fallback to contentScore)
    if (typeof setScoreWheel === 'function') {
      var ov = parseInt((data && data.overall),10);
      var cs = parseInt((data && data.contentScore),10);
      setScoreWheel(!isNaN(ov) ? ov : (!isNaN(cs) ? cs : 0));
    }
    if (typeof updateCategoryBars === 'function') updateCategoryBars();
    AnalyzeHUD.show('Done'); setTimeout(function(){ AnalyzeHUD.hide(); }, 1200);
  } catch(e){
    AnalyzeHUD.show('Applied with warnings'); setTimeout(function(){ AnalyzeHUD.hide(); }, 1500);
  }
}

// Bind analyze to button + Enter key, also provide SEMSEO_go alias
(function(){
  function go(e){ if(e && e.preventDefault) e.preventDefault(); analyze(); }
  function bind(){
    var btn = document.getElementById('analyzeBtn');
    if (btn){
      btn.addEventListener('click', go);
      if (btn.tagName === 'BUTTON' && btn.type !== 'button') btn.type = 'button';
    }
    var input = document.getElementById('analyzeUrl');
    if (input){
      input.addEventListener('keydown', function(e){ if(e.key === 'Enter'){ e.preventDefault(); analyze(); } });
    }
    window.SEMSEO_go = go;
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bind); else bind();
})();

// Live recompute when ticking items (keeps meters in sync)
document.addEventListener('change', function(e){
  const cb = e.target && e.target.closest && e.target.closest('.checklist input[type="checkbox"]');
  if(!cb) return;
  if (typeof updateCategoryBars === 'function') { try{ updateCategoryBars(); }catch(e){} }
}, true);

// First paint
document.addEventListener('DOMContentLoaded', function(){ try{ updateCategoryBars(); }catch(e){} }, { once:true });
</script>

</body>
</html>
