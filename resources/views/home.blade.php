{{-- resources/views/home.blade.php — v2025-08-25 (Human vs AI Ensemble upgraded; Analyze wired) --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, readability, entities, technical SEO, Core Web Vitals — with Human vs AI ensemble.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
  $analyzeJsonUrl = Route::has('analyze.json') ? route('analyze.json') : url('analyze.json');
  $analyzeUrl     = Route::has('analyze')      ? route('analyze')      : url('analyze');
  $psiProxyUrl    = Route::has('psi.proxy')    ? route('psi.proxy')    : url('api/psi');
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

<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>
<link rel="stylesheet" href="{{ asset('css/ensemble.css') }}"/>

<style>
/* Page chrome (keeps your dark tech look) */
:root{
  --bg:#0B0E14; --ink:#EAF2FF; --ink-2:#9FB4D8; --line:#1A2333;
  --accent:#7C4DFF; --accent-2:#00E5FF; --ok:#18C37E; --warn:#FFB020; --bad:#FF5C5C;
}
html,body{background:#070A10;color:var(--ink);font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif; line-height:1.6;}
.container{max-width:1100px;margin:28px auto;padding:0 16px;}
.header{display:flex;gap:14px;align-items:center;margin-bottom:12px;}
.header .brand{font-weight:800; letter-spacing:.3px; font-size:clamp(1.1rem,2.2vw,1.4rem)}
.header .sub{color:var(--ink-2);font-size:.95rem}

.card{background:linear-gradient(180deg,#0C0F17,#0D111A);border:1px solid rgba(255,255,255,.06);box-shadow:0 12px 40px rgba(0,0,0,.35);border-radius:16px;overflow:hidden}
.card .head{display:flex;justify-content:space-between;align-items:center;padding:14px 16px;border-bottom:1px solid rgba(255,255,255,.06);background: radial-gradient(1200px 250px at 20% -20%, rgba(124,77,255,.18), transparent 60%);}
.card .head h2{margin:0;font-size:1rem;letter-spacing:.3px}
.card .body{padding:16px}
.grid{display:grid;gap:16px}
.grid-2{grid-template-columns:2fr 1fr}
@media(max-width:980px){.grid-2{grid-template-columns:1fr}}

.input{display:flex; gap:10px; align-items:center}
.input input[type=url]{flex:1;background:#0B0E14;border:1px solid rgba(255,255,255,.1);color:var(--ink);border-radius:12px;padding:12px 14px;outline:none}
.btn{appearance:none;border:none;border-radius:12px;padding:12px 16px;font-weight:700;cursor:pointer;background:linear-gradient(90deg,var(--accent),var(--accent-2));color:#00131f;box-shadow:0 6px 20px rgba(0,229,255,.18)}
.btn:disabled{opacity:.6;filter:grayscale(.3);cursor:not-allowed}
.badge{font-weight:700;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.08)}

.section-title{font-weight:800; letter-spacing:.3px; margin: 18px 0 8px}
.kv{display:grid;grid-template-columns: 1fr 1fr; gap:10px}
.kv .row{display:flex; justify-content:space-between; gap:10px; background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.06); padding:8px 10px; border-radius:10px}
.kv .key{color:var(--ink-2)}
.kv .val{font-weight:700}

.tip{color:var(--ink-2); font-size:.95rem}
</style>
</head>
<body>
  <div class="container">
    <div class="header">
      <i class="fa-solid fa-microchip"></i>
      <div>
        <div class="brand">Semantic SEO Master</div>
        <div class="sub">Analyze any URL • Human vs AI Ensemble • Entities • Core Web Vitals</div>
      </div>
    </div>

    <div class="card">
      <div class="head">
        <h2><i class="fa-solid fa-link"></i> Analyze a URL</h2>
        <span class="badge" id="status-badge">Idle</span>
      </div>
      <div class="body">
        <div class="input">
          <input id="url-input" type="url" placeholder="https://example.com/article" value="">
          <button id="analyze-btn" class="btn"><i class="fa-solid fa-sparkles"></i> Analyze</button>
        </div>
        <div class="tip" style="margin-top:8px">We’ll fetch the page, extract main content, run readability & entity extraction, ask PSI via proxy, and compute an **ensemble** human-vs‑AI score.</div>
      </div>
    </div>

    {{-- HUMAN vs AI ENSEMBLE --}}
    @include('components.human-ai-ensemble')

    {{-- READABILITY + BASICS --}}
    <div class="card" style="margin-top:16px">
      <div class="head"><h2><i class="fa-solid fa-book-open-reader"></i> Readability & Basics</h2></div>
      <div class="body grid">
        <div class="kv">
          <div class="row"><div class="key">Title</div><div class="val" id="kv-title">—</div></div>
          <div class="row"><div class="key">Meta Description</div><div class="val" id="kv-desc">—</div></div>
          <div class="row"><div class="key">Canonical</div><div class="val" id="kv-canon">—</div></div>
          <div class="row"><div class="key">HTTP</div><div class="val" id="kv-http">—</div></div>
          <div class="row"><div class="key">Words</div><div class="val" id="kv-words">—</div></div>
          <div class="row"><div class="key">Readability</div><div class="val" id="kv-read">—</div></div>
        </div>
      </div>
    </div>

    {{-- ENTITIES --}}
    <div class="card" style="margin-top:16px">
      <div class="head"><h2><i class="fa-solid fa-diagram-project"></i> Entities</h2></div>
      <div class="body">
        <div id="entities" class="grid" style="grid-template-columns:repeat(auto-fill,minmax(180px,1fr))"></div>
      </div>
    </div>

    {{-- SITE SPEED / PSI --}}
    <div class="card" style="margin-top:16px">
      <div class="head"><h2><i class="fa-solid fa-gauge-high"></i> Core Web Vitals (PSI)</h2></div>
      <div class="body kv" id="psi-kv">
        <div class="row"><div class="key">LCP</div><div class="val" id="kv-lcp">—</div></div>
        <div class="row"><div class="key">INP</div><div class="val" id="kv-inp">—</div></div>
        <div class="row"><div class="key">CLS</div><div class="val" id="kv-cls">—</div></div>
        <div class="row"><div class="key">Performance</div><div class="val" id="kv-perf">—</div></div>
      </div>
    </div>

    <div style="height:40px"></div>
  </div>

<script>
const analyzeJsonUrl = @json($analyzeJsonUrl);
const psiProxyUrl = @json($psiProxyUrl);

function setBadge(txt){ document.getElementById('status-badge').textContent = txt }

async function postJSON(url, payload){
  const res = await fetch(url, {
    method:'POST',
    headers:{
      'Content-Type':'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify(payload||{})
  })
  if (!res.ok) throw new Error('Network error')
  return await res.json()
}

function renderBasics(b){
  const get = k => (b && b[k]) ? b[k] : '—'
  document.getElementById('kv-title').textContent = get('title')
  document.getElementById('kv-desc').textContent = get('metaDescription')
  document.getElementById('kv-canon').textContent = get('canonical')
  document.getElementById('kv-http').textContent = get('httpStatus')
  document.getElementById('kv-words').textContent = get('wordCount')
  document.getElementById('kv-read').textContent = get('readability')
}

function renderEntities(items){
  const box = document.getElementById('entities'); box.innerHTML = ''
  ;(items||[]).forEach(e => {
    const div = document.createElement('div')
    div.className='row'
    div.innerHTML = `<div class="key">${e.type||'Entity'}</div><div class="val">${e.name||e.text||'—'}</div>`
    box.appendChild(div)
  })
}

function renderPSI(psi){
  const get = k => (psi && psi[k] != null) ? psi[k] : '—'
  document.getElementById('kv-lcp').textContent  = get('lcp')
  document.getElementById('kv-inp').textContent  = get('inp')
  document.getElementById('kv-cls').textContent  = get('cls')
  document.getElementById('kv-perf').textContent = get('performance')
}

async function getPSI(url){
  try{
    const data = await postJSON(psiProxyUrl, { url })
    renderPSI(data)
  }catch(e){ console.warn('PSI failed', e) }
}

async function analyze(url){
  setBadge('Analyzing…')
  try{
    const data = await postJSON(analyzeJsonUrl, { url })
    // Adapt to your backend shape. Expecting:
    // { basics:{title,metaDescription,canonical,httpStatus,wordCount,readability},
    //   entities:[{name,type}], ensemble:{human,confidence,models:[{name,humanProb,aiProb}]},
    //   textSample:'...' }
    renderBasics(data.basics||{})
    renderEntities(data.entities||[])
    // Ensemble hookup
    window.setEnsembleScores?.({
      human: data.ensemble?.human ?? undefined, // if undefined, JS computes from models
      ai: data.ensemble?.ai ?? undefined,
      confidence: data.ensemble?.confidence ?? undefined,
      models: data.ensemble?.models ?? [],
      textSample: data.textSample || ''
    })
    // PSI (optional)
    getPSI(url)
    setBadge('Done')
  }catch(err){
    console.error(err)
    setBadge('Error')
    alert('Analyzer not ready — please try again.')
  }
}

document.getElementById('analyze-btn').addEventListener('click', () => {
  const url = document.getElementById('url-input').value.trim()
  if (!url) return alert('Please enter a URL')
  analyze(url)
})
</script>

<script defer src="{{ asset('js/ensemble.js') }}"></script>
</body>
</html>
