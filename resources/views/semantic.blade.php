@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@section('content')
<style>
  /* ----- Brand palette (elegant/dark) ----- */
  :root{
    --bg:#0b0f1a;
    --panel:#0f172a;
    --muted:#1e293b;
    --text:#e5e7eb;
    --sub:#a8b2c1;
    --good:#10b981;   /* green */
    --warn:#f59e0b;   /* orange */
    --bad:#ef4444;    /* red */
    --accent:#8b5cf6; /* violet */
    --accent2:#60a5fa;/* sky */
  }
  body{ background: radial-gradient(1400px 900px at 20% -10%, #1b3a4b33, transparent 60%), linear-gradient(180deg, #0b0f1a, #0b1324); }
  .card{ background: linear-gradient(180deg, rgba(15,23,42,.9), rgba(11,17,28,.92)); border:1px solid rgba(255,255,255,.08); }
  .soft { box-shadow: 0 18px 60px rgba(0,0,0,.25); }
  .chip  { font-size:.7rem; border:1px solid rgba(255,255,255,.12); background:rgba(255,255,255,.06); }
  .btn-pulse { transition: transform .2s ease; }
  .btn-pulse:hover { transform: translateY(-1px); }
  /* ----- Wheel ----- */
  .wheel{
    width: 220px; height: 220px; border-radius: 50%;
    display:grid; place-items:center;
    position:relative;
    background: conic-gradient(var(--accent) 0% 0%, #1f2937 0% 100%); /* will be overridden in JS */
  }
  .wheel::after{
    content:""; position:absolute; inset:14px; border-radius:50%;
    background: linear-gradient(180deg, #0b1120, #0b1728);
    box-shadow: inset 0 0 0 1px rgba(255,255,255,.06);
  }
  .score-number{ position:relative; font-weight:800; font-size:2.8rem; letter-spacing:-.02em; }
  /* ----- Bars ----- */
  .bar{ height:10px; border-radius:999px; background:#1f2a3a; overflow:hidden; }
  .bar>i{ display:block; height:100%; border-radius:999px; }
  /* ----- Checklist ----- */
  .tick-good{ color:var(--good); }
  .tick-warn{ color:var(--warn); }
  .tick-bad { color:var(--bad);  }
  .pill{
    font-size:.72rem; padding:.15rem .5rem; border-radius:.5rem;
    border:1px solid rgba(255,255,255,.15); background: rgba(255,255,255,.05);
  }
  .pill.good { border-color:rgba(16,185,129,.35); color:#b7ffe2; }
  .pill.warn { border-color:rgba(245,158,11,.35); color:#ffe9b2; }
  .pill.bad  { border-color:rgba(239,68,68,.35); color:#ffd2d2; }
  /* ----- Modal ----- */
  .modal-bg{ position:fixed; inset:0; background:rgba(0,0,0,.6); display:none; align-items:center; justify-content:center; z-index:80; }
  .modal-bg.show{ display:flex; }
</style>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-slate-100">
  <!-- Header -->
  <div class="flex items-center justify-between gap-4">
    <div>
      <h1 class="text-3xl font-extrabold tracking-tight">Semantic SEO Master Analyzer <span class="text-indigo-300">2.0</span></h1>
      <p class="text-slate-300 mt-1">Paste a URL, get an accurate score with prioritized fixes.</p>
    </div>
    <div class="hidden md:flex gap-2">
      <span class="chip px-2 py-1 rounded">Content</span>
      <span class="chip px-2 py-1 rounded">Structure</span>
      <span class="chip px-2 py-1 rounded">Technical</span>
      <span class="chip px-2 py-1 rounded">UX & Performance</span>
    </div>
  </div>

  <!-- Input -->
  <form id="semanticForm" class="mt-6 grid lg:grid-cols-[1fr,320px] gap-4">
    <div class="card rounded-2xl p-3 soft">
      <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl grid place-items-center bg-gradient-to-br from-indigo-500 to-sky-500">
          <svg class="h-5 w-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12h18M3 6h18M3 18h18"/></svg>
        </div>
        <input name="url" type="url" required placeholder="https://example.com/article"
               class="bg-transparent w-full outline-none text-slate-100 placeholder-slate-400">
      </div>
      <div class="mt-3 flex items-center gap-3">
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)"
               class="w-full bg-transparent outline-none text-slate-100 placeholder-slate-400 border border-white/10 rounded-xl px-3 py-2">
        <button class="btn-pulse px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-500 via-fuchsia-500 to-sky-500 text-white font-semibold shadow soft" type="submit">
          Analyze URL
        </button>
      </div>
      <p class="text-xs text-slate-400 mt-2">We fetch the page and compute Content, Structure, Technical and UX signals. No login needed here.</p>
    </div>

    <!-- Score Panel -->
    <div class="card rounded-2xl p-5 soft grid grid-cols-[auto,1fr] gap-4 items-center">
      <div class="wheel" id="wheel">
        <span class="score-number" id="scoreNum">—</span>
      </div>
      <div>
        <div class="text-slate-300 text-sm">Overall Score</div>
        <div id="badge" class="mt-1 text-lg font-semibold"></div>

        <div class="mt-4 grid grid-cols-2 gap-3">
          <div>
            <div class="flex justify-between text-xs text-slate-300"><span>Content</span><span id="sc-content">—</span></div>
            <div class="bar mt-1"><i id="bar-content"></i></div>
          </div>
          <div>
            <div class="flex justify-between text-xs text-slate-300"><span>Structure</span><span id="sc-structure">—</span></div>
            <div class="bar mt-1"><i id="bar-structure"></i></div>
          </div>
          <div>
            <div class="flex justify-between text-xs text-slate-300"><span>Technical</span><span id="sc-technical">—</span></div>
            <div class="bar mt-1"><i id="bar-technical"></i></div>
          </div>
          <div>
            <div class="flex justify-between text-xs text-slate-300"><span>UX & Perf</span><span id="sc-ux">—</span></div>
            <div class="bar mt-1"><i id="bar-ux"></i></div>
          </div>
        </div>
      </div>
    </div>
  </form>

  <!-- Grids -->
  <div id="resultWrap" class="mt-8 hidden">
    <!-- Quick Stats -->
    <div class="grid lg:grid-cols-3 gap-6">
      <div class="card rounded-2xl p-6 soft">
        <h3 class="font-semibold mb-4">Quick Stats</h3>
        <div class="grid grid-cols-2 gap-3 text-sm">
          <div class="rounded-xl p-3 bg-white/5 border border-white/10">
            <div class="text-slate-400 text-xs">Title length</div>
            <div class="font-semibold" id="stat-title">—</div>
          </div>
          <div class="rounded-xl p-3 bg-white/5 border border-white/10">
            <div class="text-slate-400 text-xs">Meta length</div>
            <div class="font-semibold" id="stat-meta">—</div>
          </div>
          <div class="rounded-xl p-3 bg-white/5 border border-white/10">
            <div class="text-slate-400 text-xs">Links (int./ext.)</div>
            <div class="font-semibold"><span id="stat-int">0</span>/<span id="stat-ext">0</span></div>
          </div>
          <div class="rounded-xl p-3 bg-white/5 border border-white/10">
            <div class="text-slate-400 text-xs">Images w/o ALT</div>
            <div class="font-semibold"><span id="stat-alt-miss">0</span> / <span id="stat-img-total">0</span></div>
          </div>
          <div class="rounded-xl p-3 bg-white/5 border border-white/10">
            <div class="text-slate-400 text-xs">Text/HTML ratio</div>
            <div class="font-semibold"><span id="stat-ratio">—</span>%</div>
          </div>
          <div class="rounded-xl p-3 bg-white/5 border border-white/10">
            <div class="text-slate-400 text-xs">Assets (JS/CSS/IMG)</div>
            <div class="font-semibold"><span id="stat-js">0</span>/<span id="stat-css">0</span>/<span id="stat-img">0</span></div>
          </div>
        </div>
      </div>

      <!-- Readability -->
      <div class="card rounded-2xl p-6 soft">
        <div class="flex items-center justify-between">
          <h3 class="font-semibold">Readability</h3>
          <button data-improve="readability" class="improve btn-pulse px-3 py-1.5 rounded-lg bg-gradient-to-r from-emerald-500 to-teal-500 text-sm">Improve</button>
        </div>
        <div class="mt-5 grid grid-cols-[auto,1fr] gap-4 items-center">
          <div class="wheel" id="wheel-read">
            <span class="score-number text-2xl" id="readNum">—</span>
          </div>
          <div>
            <div class="text-slate-300 text-sm">Flesch Reading Ease</div>
            <div class="text-slate-400 text-xs mt-1">Higher is easier (0–100).</div>
            <div class="mt-4">
              <div class="flex justify-between text-xs text-slate-300"><span>Score</span><span id="readScore">—</span></div>
              <div class="bar mt-1"><i id="bar-read"></i></div>
            </div>
          </div>
        </div>
      </div>

      <!-- Heading Map -->
      <div class="card rounded-2xl p-6 soft">
        <h3 class="font-semibold">Heading Map</h3>
        <div id="headingMap" class="mt-3 space-y-3 text-sm"></div>
      </div>
    </div>

    <!-- Checklist -->
    <div class="card rounded-2xl p-6 soft mt-8">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Checklist & Fixes</h3>
        <span class="text-xs text-slate-400">Green ≥ 80 • Orange 60–79 • Red &lt; 60</span>
      </div>
      <div id="checksWrap" class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-4"></div>
    </div>

    <!-- Anchors -->
    <div class="card rounded-2xl p-6 soft mt-8">
      <h3 class="font-semibold">Anchor Text (first 60)</h3>
      <div id="anchors" class="mt-3 grid md:grid-cols-2 gap-2 text-sm text-slate-300"></div>
    </div>
  </div>

  <!-- Improve Modal -->
  <div id="modal" class="modal-bg">
    <div class="card rounded-2xl p-6 soft max-w-lg w-[92%]">
      <div class="flex items-start justify-between">
        <div>
          <div class="text-sm text-slate-300">Improve</div>
          <h4 id="m-title" class="text-xl font-semibold"></h4>
        </div>
        <button id="m-close" class="text-slate-300 hover:text-white">&times;</button>
      </div>
      <div class="mt-4 space-y-3 text-sm">
        <div class="text-slate-300">Current</div>
        <div id="m-value" class="text-slate-200"></div>
        <div class="text-slate-300 mt-4">Recommendation</div>
        <div id="m-advice" class="text-slate-200"></div>
      </div>
      <div class="mt-6 flex gap-2">
        <a id="m-google" target="_blank"
           class="px-3 py-1.5 rounded-lg bg-gradient-to-r from-sky-500 to-indigo-500 text-white text-sm btn-pulse">Google it</a>
        <button id="m-ok" class="px-3 py-1.5 rounded-lg border border-white/10 text-sm">Close</button>
      </div>
    </div>
  </div>
</section>

<script>
(function(){
  const $ = s => document.querySelector(s);
  const els = {
    form: $('#semanticForm'),
    wrap: $('#resultWrap'),
    wheel: $('#wheel'),
    scoreNum: $('#scoreNum'),
    badge: $('#badge'),
    bars: {
      content: $('#bar-content'), structure: $('#bar-structure'),
      technical: $('#bar-technical'), ux: $('#bar-ux')
    },
    scLbl: {
      content: $('#sc-content'), structure: $('#sc-structure'),
      technical: $('#sc-technical'), ux: $('#sc-ux')
    },
    stats: {
      title: $('#stat-title'), meta: $('#stat-meta'),
      int: $('#stat-int'), ext: $('#stat-ext'),
      altMiss: $('#stat-alt-miss'), imgTotal: $('#stat-img-total'),
      ratio: $('#stat-ratio'), js: $('#stat-js'), css: $('#stat-css'), img: $('#stat-img')
    },
    read: {
      wheel: $('#wheel-read'), num: $('#readNum'), bar: $('#bar-read'), lbl: $('#readScore')
    },
    map: $('#headingMap'),
    checks: $('#checksWrap'),
    anchors: $('#anchors'),
    modal: $('#modal'), mTitle: $('#m-title'), mAdvice: $('#m-advice'),
    mValue: $('#m-value'), mGoogle: $('#m-google'),
    mClose: $('#m-close'), mOk: $('#m-ok')
  };

  let checksById = {};

  function setBar(el, n){
    const c = colorFor(n);
    el.style.width = (n||0)+'%';
    el.style.background = `linear-gradient(90deg, ${c} 0%, ${c} 100%)`;
  }
  function colorFor(n){
    if (n >= 80) return 'var(--good)';
    if (n >= 60) return 'var(--warn)';
    return 'var(--bad)';
  }
  function setWheel(el, n){
    const p = Math.max(0, Math.min(100, n||0));
    let grad;
    if (p <= 60) {
      grad = `conic-gradient(var(--bad) 0% ${p}%, #1f2937 ${p}% 100%)`;
    } else if (p <= 80) {
      grad = `conic-gradient(var(--bad) 0% 60%, var(--warn) 60% ${p}%, #1f2937 ${p}% 100%)`;
    } else {
      grad = `conic-gradient(var(--bad) 0% 60%, var(--warn) 60% 80%, var(--good) 80% ${p}%, #1f2937 ${p}% 100%)`;
    }
    el.style.background = grad;
  }
  function setBadge(n, text){
    const cls = n>=80?'text-emerald-300':(n>=60?'text-amber-300':'text-rose-300');
    els.badge.className = 'mt-1 text-lg font-semibold '+cls;
    els.badge.textContent = text || (n>=80?'Great Work — Well Optimized':(n>=60?'Needs Optimization':'Needs Significant Optimization'));
  }
  function pillClass(n){ return n>=80?'pill good':(n>=60?'pill warn':'pill bad'); }

  function openModal(check){
    els.mTitle.textContent = check.label;
    // Pretty-print value when possible
    let v = '';
    if (check.value==null) { v = '—'; }
    else if (typeof check.value==='object'){ v = JSON.stringify(check.value, null, 2); }
    else v = String(check.value);
    els.mValue.textContent = v;
    els.mAdvice.textContent = check.advice || 'No advice available.';
    els.mGoogle.href = 'https://www.google.com/search?q=' + encodeURIComponent('How to improve ' + check.label + ' SEO');
    els.modal.classList.add('show');
  }
  ['mClose','mOk'].forEach(id => els[id].addEventListener('click', ()=> els.modal.classList.remove('show')));
  els.modal.addEventListener('click', e=>{ if (e.target === els.modal) els.modal.classList.remove('show'); });

  function renderHeadings(h){
    const out = [];
    for (const level of ['h1','h2','h3','h4','h5','h6']){
      const arr = h[level]||[];
      if (!arr.length) continue;
      out.push(`
        <div class="rounded-lg border border-white/10 p-3 bg-white/5">
          <div class="text-xs uppercase tracking-wide text-slate-400">${level}</div>
          <div class="mt-2 space-y-1">${arr.map(t=>`<div class="pl-2">• ${escapeHtml(t)}</div>`).join('')}</div>
        </div>
      `);
    }
    els.map.innerHTML = out.join('');
  }

  function renderAnchors(list){
    els.anchors.innerHTML = (list||[]).slice(0,60).map(a=>`
      <div class="rounded-lg border border-white/10 p-2 bg-white/5">
        <span class="text-xs ${a.type==='external'?'text-sky-300':'text-emerald-300'} mr-2">[${a.type}]</span>
        <span class="text-slate-200">${escapeHtml(a.text || '(no text)')}</span>
        <div class="text-xs text-slate-400 truncate">${escapeHtml(a.href)}</div>
      </div>
    `).join('');
  }

  function renderChecks(checks){
    checksById = {};
    // Group by category
    const groups = {content:[],structure:[],technical:[],ux:[]};
    for (const c of checks){ groups[c.category].push(c); checksById[c.id]=c; }
    // order for display
    const order = [
      ['content','Content & Keywords'],
      ['structure','Structure & Architecture'],
      ['technical','Technical Elements'],
      ['ux','User Signals & Experience']
    ];
    const nodes = [];
    for (const [key, title] of order){
      const items = groups[key]||[];
      if (!items.length) continue;
      nodes.push(`
        <div class="rounded-xl border border-white/10 p-4 bg-white/5">
          <div class="flex items-center justify-between mb-2">
            <h4 class="font-semibold">${title}</h4>
            <span class="chip px-2 py-0.5 rounded">${items.length} checks</span>
          </div>
          <div class="divide-y divide-white/10">
            ${items.map(it=>{
              const color = it.score>=80?'tick-good':(it.score>=60?'tick-warn':'tick-bad');
              return `
                <div class="py-2 flex items-center gap-2 justify-between">
                  <div class="flex items-center gap-2">
                    <svg class="h-5 w-5 ${color}" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                      <path stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    <div class="text-sm">${it.label}</div>
                  </div>
                  <div class="flex items-center gap-2">
                    <span class="${pillClass(it.score)}">${it.score}</span>
                    <button class="improve px-2 py-1 rounded-lg border border-white/10 text-xs hover:bg-white/10" data-improve="${it.id}">
                      Improve
                    </button>
                  </div>
                </div>
              `;
            }).join('')}
          </div>
        </div>
      `);
    }
    els.checks.innerHTML = nodes.join('');
    // bind improve buttons
    els.checks.querySelectorAll('.improve').forEach(btn=>{
      btn.addEventListener('click', ()=>{
        const id = btn.getAttribute('data-improve');
        const ck = checksById[id]; if (ck) openModal(ck);
      });
    });
    // also bind the dedicated Readability Improve button
    document.querySelectorAll('button[data-improve="readability"]').forEach(b=>{
      b.onclick = ()=> checksById.readability && openModal(checksById.readability);
    });
  }

  function escapeHtml(s){ return (s||'').toString().replace(/[&<>"]/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c])); }

  // ---------- Submit ----------
  els.form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(els.form);
    const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') };
    try{
      const res = await fetch('/api/semantic-analyze', {
        method:'POST', headers:{'Accept':'application/json','Content-Type':'application/json'},
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (!data.ok) { alert(data.error || 'Analysis failed'); return; }

      // Wheel + badge
      setWheel(els.wheel, data.overall_score);
      els.scoreNum.textContent = data.overall_score;
      setBadge(data.overall_score, data.badge?.text);

      // Category bars
      const cs = data.categories || {};
      for (const k of ['content','structure','technical','ux']){
        els.scLbl[k].textContent = (cs[k]??0);
        setBar(els.bars[k], cs[k]??0);
      }

      // Quick stats
      const f = data.facts || {};
      els.stats.title.textContent = (f.title||'').length + ' chars';
      els.stats.meta.textContent  = (f.meta_desc||'').length + ' chars';
      els.stats.int.textContent   = f.links?.internal ?? 0;
      els.stats.ext.textContent   = f.links?.external ?? 0;
      els.stats.altMiss.textContent = f.image_alt_missing ?? 0;
      els.stats.imgTotal.textContent = f.image_count ?? 0;
      els.stats.ratio.textContent  = f.text_to_html_ratio ?? '—';
      els.stats.js.textContent     = f.assets?.scripts ?? 0;
      els.stats.css.textContent    = f.assets?.styles ?? 0;
      els.stats.img.textContent    = f.assets?.images ?? 0;

      // Readability
      const r = (data.checks||[]).find(c=>c.id==='readability');
      const rScore = r?.score ?? f.flesch ?? 0;
      setWheel(els.read.wheel, rScore);
      els.read.num.textContent = rScore;
      els.read.lbl.textContent = rScore;
      setBar(els.read.bar, rScore);

      // Heading map & anchors
      renderHeadings(f.headings || {});
      renderAnchors((f.links && f.links.anchors) || []);

      // Checklist
      renderChecks(data.checks || []);

      els.wrap.classList.remove('hidden');
      // scroll into view on mobile
      els.wrap.scrollIntoView({behavior:'smooth', block:'start'});
    }catch(err){
      console.error(err);
      alert('Network or server error.');
    }
  });
})();
</script>
@endsection
