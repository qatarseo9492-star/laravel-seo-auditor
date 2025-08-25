{{-- resources/views/home.blade.php — Human vs AI (Ensemble) • colorful upgrade --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @php
    use Illuminate\Support\Facades\Route;
    $metaTitle       = 'Semantic SEO Master • Ultra Tech Global';
    $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, readability, and Core Web Vitals — with colorful Human vs AI ensemble scoring.';
    $metaImage       = asset('og-image.png');
    $canonical       = url()->current();
    $analyzeJsonUrl  = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
    $analyzeUrl      = Route::has('analyze')      ? route('analyze')      : url('analyze');
    $psiProxyUrl     = Route::has('psi.proxy')    ? route('psi.proxy')    : url('api/psi'); // keep key private
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

  <!-- Fonts & Icons -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    /* ======= THEME ======= */
    :root{
      --bg: #0b0b13;
      --panel: #121226;
      --card: #151633;
      --muted: #9aa5b3;
      --text: #f2f5f9;
      --ring-track: #23243f;

      /* brand accents */
      --grad-1: linear-gradient(135deg,#6a5af9 0%, #b372f8 50%, #f38bff 100%);
      --grad-2: linear-gradient(135deg,#00d2ff 0%, #3a7bd5 100%);
      --grad-3: linear-gradient(135deg,#00ffa3 0%, #00d476 50%, #2ae077 100%);
      --grad-4: linear-gradient(135deg,#ff6a88 0%, #ff99ac 100%);
      --yellow: #ffd166;
      --green:  #2ee59d;
      --red:    #ff6b9a;
      --orange: #ff9f40;
      --blue:   #4cc9f0;
      --violet: #a78bfa;
      --shadow: 0 10px 30px rgba(0,0,0,.35);
    }

    *{ box-sizing: border-box }
    html,body{ margin:0; padding:0; background:radial-gradient(1200px 600px at 10% -10%,#1a1440 0,#0b0b13 60%), var(--bg); color:var(--text); font: 15px/1.55 system-ui, -apple-system, Segoe UI, Roboto, Inter, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji"; }

    a{ color:#b5c6ff; text-decoration:none }
    a:hover{ text-decoration:underline }

    .container{ max-width:1140px; margin:0 auto; padding:32px 16px 60px }

    header.app{
      position: sticky; top:0; z-index: 30;
      backdrop-filter: blur(10px);
      background: linear-gradient(180deg, rgba(11,11,19,.8), rgba(11,11,19,.35));
      border-bottom: 1px solid rgba(255,255,255,.06);
    }
    header .wrap{ max-width:1140px; margin:0 auto; padding:14px 16px; display:flex; align-items:center; gap:14px }
    .brand{ display:flex; align-items:center; gap:10px; font-weight:800; letter-spacing:.2px }
    .brand .logo{
      width:34px; height:34px; border-radius:12px; background:var(--grad-1); box-shadow: var(--shadow);
      display:grid; place-items:center;
    }
    .brand .logo i{ color:white }
    .brand .title{ font-size:16px }

    .panel{
      margin-top:20px; background:linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border: 1px solid rgba(255,255,255,.08); border-radius:18px; box-shadow: var(--shadow);
    }
    .panel .head{ display:flex; align-items:center; justify-content:space-between; gap:16px; padding:18px 18px 14px; border-bottom:1px dashed rgba(255,255,255,.08) }
    .panel .head h2{ margin:0; font-size:18px; letter-spacing:.2px }
    .panel .body{ padding:18px }

    /* ======= HERO SEARCH (optional) ======= */
    .search{
      display:grid; grid-template-columns:1fr auto; gap:10px; margin-top:14px;
      background:var(--panel); border:1px solid rgba(255,255,255,.06); border-radius:16px; padding:10px;
    }
    .search input{
      width:100%; background:#0f1027; color:var(--text); border:1px solid #24254a; border-radius:12px; padding:13px 14px; outline:none;
    }
    .search button{
      padding:12px 18px; border-radius:12px; border:0; cursor:pointer; color:white; background:var(--grad-2); box-shadow: var(--shadow); font-weight:700;
    }
    .search button i{ margin-right:8px }

    /* ======= ICON BUBBLES ======= */
    .icon-bubble{
      width:42px; height:42px; border-radius:14px; display:grid; place-items:center; box-shadow: var(--shadow);
    }
    .icon-bubble i{ color:white; font-size:18px }

    /* ======= CARDS ======= */
    .cards{ display:grid; grid-template-columns: repeat(12, 1fr); gap:14px }
    .card{
      grid-column: span 12;
      background: var(--card); border: 1px solid rgba(255,255,255,.08);
      border-radius:16px; padding:16px; box-shadow: var(--shadow);
    }
    @media (min-width: 900px){
      .card.span-6{ grid-column: span 6 }
      .card.span-4{ grid-column: span 4 }
      .card.span-8{ grid-column: span 8 }
      .card.span-3{ grid-column: span 3 }
    }
    .card .row{ display:flex; align-items:center; gap:12px }
    .muted{ color:var(--muted) }
    .chip{
      display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px;
      font-weight:700; font-size:12px; border:1px solid rgba(255,255,255,.1); background:rgba(255,255,255,.03)
    }
    .chip i{ font-size:13px }

    /* ======= PROGRESS RINGS ======= */
    .ring{
      --val: 0; /* 0..100 */
      --color: var(--violet);
      width:120px; aspect-ratio:1/1; border-radius:999px;
      background: conic-gradient(var(--color) calc(var(--val)*1%), var(--ring-track) 0);
      display:grid; place-items:center; position:relative; transition: .5s ease;
    }
    .ring::before{
      content:""; position:absolute; inset:8px; border-radius:999px; background: var(--card);
    }
    .ring .inner{
      position:relative; display:grid; place-items:center; text-align:center;
    }
    .ring .pct{ font-size:20px; font-weight:800 }
    .ring .lbl{ font-size:12px; color:var(--muted) }

    /* ======= DETECTOR GRID ======= */
    .detectors{ display:grid; grid-template-columns: repeat(12, 1fr); gap:12px }
    .det{
      grid-column: span 12; background:linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08); border-radius:14px; padding:12px; display:flex; align-items:center; justify-content:space-between; gap:10px;
    }
    @media(min-width:740px){ .det{ grid-column: span 6 } }
    @media(min-width:1000px){ .det{ grid-column: span 4 } }

    .det .left{ display:flex; align-items:center; gap:12px }
    .det .right{ display:flex; align-items:center; gap:10px }
    .badge{
      padding:6px 10px; border-radius:999px; font-weight:800; font-size:12px; letter-spacing:.2px; border:1px solid rgba(255,255,255,.12)
    }
    .badge.green{ background:rgba(46,229,157,.15); color:#baffde; border-color: rgba(46,229,157,.35) }
    .badge.red{ background:rgba(255,107,154,.15); color:#ffd2e0; border-color: rgba(255,107,154,.35) }
    .badge.orange{ background:rgba(255,159,64,.15); color:#ffe0c2; border-color: rgba(255,159,64,.35) }
    .badge.blue{ background:rgba(76,201,240,.15); color:#c7efff; border-color: rgba(76,201,240,.35) }

    .det .icon-bubble{ width:38px; height:38px; border-radius:12px }
    .det .score{ font-weight:800 }

    /* ======= ACCORDION ======= */
    .accordion{ border-top:1px dashed rgba(255,255,255,.08); margin-top:12px; padding-top:12px }
    .accordion summary{
      list-style:none; cursor:pointer; user-select:none;
      display:flex; align-items:center; gap:10px; padding:8px 0; color:var(--muted);
    }
    .accordion summary::-webkit-details-marker{ display:none }
    .accordion[open] summary{ color:#dfe6ff }
    .accordion .content{ color:#c8cbe0; padding:6px 0 2px }

    /* ======= TOOLTIP ======= */
    .tip{ position:relative }
    .tip:hover .tip-box{ opacity:1; transform: translateY(-6px); pointer-events:auto }
    .tip-box{
      position:absolute; bottom: calc(100% + 8px); left:50%; transform: translate(-50%, 0);
      background:#121123; color:#e7e7ff; padding:8px 10px; border-radius:8px; border:1px solid rgba(255,255,255,.08);
      white-space:nowrap; opacity:0; transition:.2s; pointer-events:none; font-size:12px; z-index:20
    }
    .tip-box::after{
      content:""; position:absolute; top:100%; left:50%; transform: translateX(-50%);
      border:7px solid transparent; border-top-color:#121123;
    }

    /* small helpers */
    .grid-3{ display:grid; grid-template-columns: repeat(3, 1fr); gap:14px }
    .verdict-chip{
      display:inline-flex; align-items:center; gap:10px; padding:10px 12px; border-radius:14px;
      background:linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02)); border:1px solid rgba(255,255,255,.12);
      font-weight:800;
    }
    .spacer{ height:6px }
  </style>
</head>

<body>
<header class="app">
  <div class="wrap">
    <div class="brand">
      <div class="logo"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
      <div class="title">Semantic SEO Master</div>
    </div>
  </div>
</header>

<main class="container">
  <!-- Optional search form (kept minimal so you can tie into your existing analyze route) -->
  <form class="search" method="GET" action="{{ $analyzeUrl }}">
    <input type="url" name="url" placeholder="Paste a URL to analyze…" value="{{ request('url') }}">
    <button type="submit"><i class="fa-solid fa-magnifying-glass"></i>Analyze</button>
  </form>

  <!-- ======================= HUMAN vs AI (ENSEMBLE) ======================= -->
  <section id="ai-ensemble" class="panel" data-endpoint="{{ $analyzeJsonUrl }}" data-url="{{ request('url') }}">
    <div class="head">
      <h2><i class="fa-solid fa-scale-balanced" style="margin-right:10px; color:#ffd166"></i>Human vs AI Content (Ensemble)</h2>
      <span class="chip tip">
        <i class="fa-solid fa-circle-info"></i> How it works
        <span class="tip-box">Shows probabilities from multiple detectors + your ensemble logic.</span>
      </span>
    </div>
    <div class="body">
      <!-- verdict -->
      <div class="row" style="justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:10px">
        <div class="row" style="gap:12px">
          <div class="icon-bubble" id="verdictIcon" style="background: var(--grad-4)"><i class="fa-solid fa-robot"></i></div>
          <div>
            <div class="verdict-chip" id="verdictChip">
              <i class="fa-solid fa-circle-half-stroke"></i> <span id="verdictLabel">Analyzing…</span>
            </div>
            <div class="spacer"></div>
            <div class="muted" id="verdictNote">We’ll blend multiple signals to estimate likelihood of AI vs Human authorship.</div>
          </div>
        </div>

        <!-- legend -->
        <div class="row" style="gap:10px; flex-wrap:wrap">
          <span class="chip" style="background:rgba(46,229,157,.15); border-color:rgba(46,229,157,.35); color:#caffea">
            <i class="fa-solid fa-user"></i> Human
          </span>
          <span class="chip" style="background:rgba(255,107,154,.15); border-color:rgba(255,107,154,.35); color:#ffd8e6">
            <i class="fa-solid fa-robot"></i> AI
          </span>
          <span class="chip" style="background:rgba(167,139,250,.15); border-color:rgba(167,139,250,.35); color:#efe6ff">
            <i class="fa-solid fa-brain"></i> Ensemble
          </span>
        </div>
      </div>

      <div class="spacer"></div>

      <!-- 3 progress rings -->
      <div class="cards">
        <div class="card span-4" style="text-align:center">
          <div class="ring" id="ringHuman" style="--val:0; --color: var(--green)">
            <div class="inner">
              <div class="pct" id="pctHuman">0%</div>
              <div class="lbl">Human probability</div>
            </div>
          </div>
        </div>
        <div class="card span-4" style="text-align:center">
          <div class="ring" id="ringAI" style="--val:0; --color: var(--red)">
            <div class="inner">
              <div class="pct" id="pctAI">0%</div>
              <div class="lbl">AI probability</div>
            </div>
          </div>
        </div>
        <div class="card span-4" style="text-align:center">
          <div class="ring" id="ringEnsemble" style="--val:0; --color: var(--violet)">
            <div class="inner">
              <div class="pct" id="pctEnsemble">0</div>
              <div class="lbl">Ensemble score</div>
            </div>
          </div>
        </div>
      </div>

      <!-- detectors -->
      <div class="card span-12" style="margin-top:14px">
        <div class="row" style="justify-content:space-between">
          <div class="row">
            <div class="icon-bubble" style="background: var(--grad-2)"><i class="fa-solid fa-shield-check"></i></div>
            <div><strong>Detectors</strong><div class="muted">Individual model signals that feed the ensemble</div></div>
          </div>
          <div class="chip tip"><i class="fa-solid fa-list-check"></i> Models
            <span class="tip-box">We’ll show whatever detectors your JSON provides.</span>
          </div>
        </div>
        <div class="detectors" id="detectorsList" style="margin-top:12px"></div>
      </div>

      <!-- rationale / notes -->
      <details class="accordion" id="rationaleBox">
        <summary><i class="fa-solid fa-comment-dots"></i> Rationale & caveats</summary>
        <div class="content" id="rationaleContent">
          Detectors can be noisy on short texts, quotes, or heavily edited content. Treat this as one signal among many (fact-checking, citations, time-series authorship, etc.).
        </div>
      </details>
    </div>
  </section>

  <!-- (Optional) other sections – keep placeholders so layout doesn’t break) -->
  <section class="panel" style="margin-top:16px">
    <div class="head"><h2><i class="fa-solid fa-book-open-reader" style="margin-right:10px; color:#4cc9f0"></i>Readability (placeholder)</h2></div>
    <div class="body muted">Hook this to your existing readability logic.</div>
  </section>

  <section class="panel" style="margin-top:16px">
    <div class="head"><h2><i class="fa-solid fa-sitemap" style="margin-right:10px; color:#a78bfa"></i>Entities (placeholder)</h2></div>
    <div class="body muted">Hook this to your existing NER/entity cards.</div>
  </section>

  <section class="panel" style="margin-top:16px">
    <div class="head"><h2><i class="fa-solid fa-gauge-high" style="margin-right:10px; color:#2ee59d"></i>Site Speed (placeholder)</h2></div>
    <div class="body muted">Connect to your PSI proxy at <code>{{ $psiProxyUrl }}</code>.</div>
  </section>
</main>

<script>
  // ===== Helpers =====
  const clampPct = v => Math.max(0, Math.min(100, Math.round(Number(v)||0)));
  const fmtPct   = v => clampPct(v) + '%';

  function verdictFrom(human, ai){
    // Simple triage: adjust thresholds to your own ensemble logic
    if (human >= 80 && ai <= 20) return {label:'Likely Human',   color:'var(--green)',  icon:'fa-user'};
    if (ai >= 80 && human <= 20) return {label:'Likely AI',      color:'var(--red)',    icon:'fa-robot'};
    if (human >= 60 && ai <= 40) return {label:'Leaning Human',  color:'var(--green)',  icon:'fa-user'};
    if (ai >= 60 && human <= 40) return {label:'Leaning AI',     color:'var(--red)',    icon:'fa-robot'};
    return {label:'Mixed / Uncertain', color:'var(--orange)', icon:'fa-circle-half-stroke'};
  }

  function colorChip(val){
    if (val >= 80) return 'red';
    if (val >= 60) return 'orange';
    return 'blue';
  }

  function detectorIcon(name=''){
    const n = (name || '').toLowerCase();
    if (n.includes('zero')) return 'fa-gauge';
    if (n.includes('original')) return 'fa-shield';
    if (n.includes('sapling')) return 'fa-seedling';
    if (n.includes('writer')) return 'fa-pen-nib';
    if (n.includes('openai')) return 'fa-sparkles';
    if (n.includes('cross') || n.includes('ensemble')) return 'fa-scale-balanced';
    return 'fa-brain';
  }

  function setRing(el, val, label){
    if (!el) return;
    const pct = clampPct(val);
    el.style.setProperty('--val', pct);
    const pctEl = el.querySelector('.pct');
    const lblEl = el.querySelector('.lbl');
    if (pctEl) pctEl.textContent = fmtPct(pct);
    if (lblEl && label) lblEl.textContent = label;
  }

  function setVerdict(v){
    const icon = document.getElementById('verdictIcon');
    const chip = document.getElementById('verdictChip');
    const lab  = document.getElementById('verdictLabel');
    if (lab) lab.textContent = v.label;
    if (icon){ icon.style.background = v.color;
      const i = icon.querySelector('i'); if (i) i.className = 'fa-solid ' + v.icon; }
  }

  function addDetectorRow(container, item){
    const name = item.name || item.model || 'Detector';
    const ai   = ('ai_pct' in item) ? clampPct(item.ai_pct)
               : ('ai' in item)     ? clampPct(item.ai)
               : ('score' in item)  ? clampPct(item.score)
               : 0;
    const human = ('human_pct' in item) ? clampPct(item.human_pct) : clampPct(100 - ai);
    const badgeClass = colorChip(ai);

    const row = document.createElement('div');
    row.className = 'det';

    row.innerHTML = `
      <div class="left">
        <div class="icon-bubble" style="background: var(--grad-1)">
          <i class="fa-solid ${detectorIcon(name)}"></i>
        </div>
        <div>
          <div style="font-weight:800">${name}</div>
          <div class="muted" style="font-size:12px">AI: ${ai}% • Human: ${human}%</div>
        </div>
      </div>
      <div class="right">
        <span class="badge ${badgeClass}">AI ${ai}%</span>
        <span class="badge green">Human ${human}%</span>
      </div>
    `;
    container.appendChild(row);
  }

  function parseEnsemble(data){
    // Defensive parsing across possible shapes
    const root = data?.ai_human_ensemble || data?.ensemble || data || {};
    let ai   = root.ai_pct   ?? root.ai_probability   ?? root.ai      ?? root.ai_score;
    let hum  = root.human_pct?? root.human_probability?? root.human   ?? root.human_score;
    if (ai == null && hum != null) ai = 100 - Number(hum);
    if (hum == null && ai != null) hum = 100 - Number(ai);

    const ensemble = root.ensemble_score ?? root.score ?? Math.max(ai||0, hum||0);
    const rationale = root.rationale || root.explanation || '';
    const classifiers = Array.isArray(root.classifiers) ? root.classifiers
                       : Array.isArray(root.models)     ? root.models
                       : Array.isArray(root.detectors)  ? root.detectors
                       : [];

    return {
      ai: clampPct(ai || 0),
      human: clampPct(hum || 0),
      ensemble: clampPct(ensemble || 0),
      rationale,
      classifiers
    };
  }

  async function hydrate(){
    const sec = document.getElementById('ai-ensemble');
    const endpoint = sec?.dataset?.endpoint;
    const urlParam = sec?.dataset?.url || (new URLSearchParams(location.search).get('url') || '').trim();

    const ringHuman    = document.getElementById('ringHuman');
    const ringAI       = document.getElementById('ringAI');
    const ringEnsemble = document.getElementById('ringEnsemble');
    const detList      = document.getElementById('detectorsList');
    const rationaleBox = document.getElementById('rationaleBox');
    const rationaleEl  = document.getElementById('rationaleContent');

    // fallback demo in case no URL / no API
    const demo = {
      ai_human_ensemble:{
        ai_pct: 34, human_pct: 66, ensemble_score: 71,
        rationale: 'Signals suggest a human-led draft with light AI edits. Longer sentences with personal deixis and idiosyncratic punctuation.',
        classifiers:[
          {name:'GPTZero', ai_pct: 28}, {name:'Originality AI', ai_pct: 42},
          {name:'Sapling', ai_pct: 24}, {name:'Ensemble Cross-Vote', ai_pct: 34}
        ]
      }
    };

    let payload = demo;
    if (endpoint && urlParam){
      try{
        const res = await fetch(`${endpoint}?url=${encodeURIComponent(urlParam)}`, {headers:{'X-Requested-With':'XMLHttpRequest'}});
        if (res.ok){
          const json = await res.json();
          if (json && Object.keys(json).length) payload = json;
        }
      }catch(e){ /* keep demo */ }
    }

    const parsed = parseEnsemble(payload);
    setRing(ringHuman, parsed.human, 'Human probability');
    setRing(ringAI, parsed.ai, 'AI probability');
    setRing(ringEnsemble, parsed.ensemble, 'Ensemble score');
    setVerdict(verdictFrom(parsed.human, parsed.ai));

    if (detList){
      detList.innerHTML = '';
      (parsed.classifiers || []).forEach(c => addDetectorRow(detList, c));
      if (!parsed.classifiers?.length){
        addDetectorRow(detList, {name:'Cross-Vote Ensemble', ai_pct: parsed.ai});
      }
    }

    if (parsed.rationale){
      if (rationaleEl) rationaleEl.textContent = parsed.rationale;
      if (rationaleBox) rationaleBox.open = false;
    }else{
      if (rationaleBox) rationaleBox.style.display = 'none';
    }
  }

  document.addEventListener('DOMContentLoaded', hydrate);
</script>
</body>
</html>
