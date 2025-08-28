@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@push('head')
<style>
  /* Page background ONLY (pure black) */
  html, body { background:#000000 !important; }

  /* Shared tokens (kept colorful UI) */
  .card { border-radius: 16px; padding: 20px; background: rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(10px); }
  .pill  { padding:4px 8px; border-radius:9999px; font-size:12px; font-weight:600; border:1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.06); color:#e5e7eb; }
  .k-badge{ padding:6px 8px; border-radius:10px; font-size:12px; font-weight:700; border:1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.06); color:#e5e7eb; }
  .shadow-soft { box-shadow: 0 12px 40px rgba(0,0,0,.25); }
  .bar { height:12px; border-radius:9999px; background: rgba(255,255,255,.08); overflow:hidden; }
  .bar > span { display:block; height:100%; border-radius:9999px; transition: width .5s ease; background: linear-gradient(90deg,#22c55e,#60a5fa,#d946ef); }
  .chip { font-size:12px; padding:4px 8px; border-radius:8px; background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.10); color:#cbd5e1; }

  /* Water loading bar */
  #waterbar { height: 10px; border-radius: 9999px; background: rgba(255,255,255,.08); overflow: hidden; }
  #waterbar span { display:block; height:100%; width:0%; background: linear-gradient(90deg,#ef4444,#f59e0b,#22c55e); transition: width .8s ease; filter: drop-shadow(0 0 10px rgba(148,163,184,.4)); }

  /* Score wheels */
  .score-wheel { width:180px; height:180px; display:grid; place-items:center; position:relative; }
  .score-wheel .ring {
    --v: 0;
    width: 100%; height: 100%; border-radius: 50%;
    background: conic-gradient(#22c55e calc(var(--v)*1%), #f59e0b calc(var(--v)*1% + .0001%), #ef4444 100%);
    -webkit-mask: radial-gradient(circle 60px, transparent 58%, #000 60%);
            mask: radial-gradient(circle 60px, transparent 58%, #000 60%);
    box-shadow: inset 0 0 0 6px rgba(255,255,255,.06);
  }
  .score-wheel .center {
    position:absolute; inset:0; display:grid; place-items:center;
    font-weight:800; font-size:46px;
    background: linear-gradient(90deg,#67e8f9,#c084fc,#fb7185);
    -webkit-background-clip:text; background-clip:text; color:transparent;
  }

  /* === Semantic SEO Ground — slab with your colors === */
  .ground-slab{
    border-radius: 24px;
    padding: 22px;
    background:#0D0E1E;                 /* section bg */
    border: 1px solid #1b2640;
    position: relative;
    overflow: hidden;
  }
  /* keep subtle diagonal lines inside the slab only */
  .ground-slab::after{
    content:"";
    position:absolute; inset:0; pointer-events:none;
    background:repeating-linear-gradient(-65deg, rgba(255,255,255,.04) 0 2px, transparent 2px 28px);
    opacity:.25;
  }
  .ground-head{ display:flex; align-items:center; gap:14px; margin-bottom:16px; position:relative; z-index:1; }
  .gh-icon{
    width:42px; height:42px; border-radius:12px;
    display:grid; place-items:center; font-weight:800; color:#fff;
    background: linear-gradient(135deg, rgba(99,102,241,.32), rgba(236,72,153,.32));
    border:1px solid rgba(255,255,255,.14);
    box-shadow: 0 6px 22px rgba(0,0,0,.35) inset, 0 6px 24px rgba(99,102,241,.18);
  }
  .gh-title{
    font-size: clamp(20px, 3.6vw, 28px);
    font-weight: 900; letter-spacing:.2px; line-height:1.1;
    background: linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185);
    -webkit-background-clip:text; background-clip:text; color:transparent;
  }
  .gh-sub{ font-size: 14px; color:#cbd5e1; }

  /* Category cards (boxes) */
  .ground-slab .cat-card{
    border-radius: 18px; padding: 18px; position:relative;
    background:#111E2F;                 /* box bg */
    border:1px solid rgba(255,255,255,.12);
    box-shadow: 0 12px 30px rgba(0,0,0,.25);
  }
  .ground-slab .cat-card::before{
    content:""; position:absolute; inset:0; pointer-events:none;
    background: repeating-linear-gradient(-65deg, rgba(255,255,255,.06) 0 2px, transparent 2px 28px);
    opacity:.20; border-radius: inherit;
  }
  .ground-slab .cat-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom: 12px; }
  .ground-slab .cat-title{
    font-size: 22px; font-weight:900;
    background: linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185);
    -webkit-background-clip:text; background-clip:text; color:transparent;
  }
  .ground-slab .cat-badge{ font-size:12px; font-weight:700; padding:6px 8px; border-radius:10px; background: rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.12); color:#e5e7eb; }
  .ground-slab .cat-icon { width:42px; height:42px; border-radius:12px; display:grid; place-items:center; font-weight:800;
    background: linear-gradient(135deg,rgba(99,102,241,.25),rgba(236,72,153,.25)); border: 1px solid rgba(255,255,255,.12); color:#fff; }
  .ground-slab .progress{ width:100%; height: 12px; border-radius:9999px; background: rgba(255,255,255,.08); overflow:hidden; border:1px solid rgba(255,255,255,.14); }
  .ground-slab .progress > span{ display:block; height:100%; border-radius:9999px; background: linear-gradient(90deg,#ef4444,#fde047,#22c55e); transition: width .5s ease; }

  /* Checklist rows */
  .ground-slab .check{ display:flex; align-items:center; justify-content:space-between; border-radius:14px; padding:14px 16px; border:1px solid rgba(255,255,255,.10); background:#0F1A29; }
  .ground-slab .check + .check { margin-top:8px; }
  .ground-slab .check .left { display:flex; align-items:center; gap:10px; }
  .ground-slab .check .text{ color:#f8fafc; font-weight:700; font-size: 18px; }
  .dot { width:10px; height:10px; border-radius:9999px; }
  .dot.green{  background:#10b981; box-shadow:0 0 10px rgba(16,185,129,.6); }
  .dot.orange{ background:#f59e0b; box-shadow:0 0 10px rgba(245,158,11,.6); }
  .dot.red{    background:#ef4444; box-shadow:0 0 10px rgba(239,68,68,.6); }
  .score-pill { font-size:12px; font-weight:800; padding:4px 8px; border-radius:10px; background: rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); color:#e5e7eb; }
  .improve-btn { font-size:12px; font-weight:700; padding:6px 10px; border-radius:10px; background: linear-gradient(135deg,#a78bfa,#60a5fa); color:white; box-shadow: 0 8px 16px rgba(96,165,250,.25); }

  /* Score-pill band classes */
  .score-pill--green  { background: rgba(16,185,129,.18); border-color: rgba(16,185,129,.35); color:#bbf7d0; }
  .score-pill--orange { background: rgba(245,158,11,.18); border-color: rgba(245,158,11,.35); color:#fde68a; }
  .score-pill--red    { background: rgba(239,68,68,.18);  border-color: rgba(239,68,68,.35);  color:#fecaca; }

  /* Dialog defaults & backdrop + modal palette */
  dialog[open] { display: block; }
  dialog::backdrop { background: rgba(0,0,0,.6); }
  #improveModal .card{ background:#0D0E1E; border:1px solid #1b2640; }
  #improveModal .card .card{ background:#111E2F; border-color:rgba(255,255,255,.12); }
</style>
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 py-10 space-y-8 text-slate-100">

  <div class="flex flex-col gap-4">
    <div class="chip w-max">Analyzer 2.0</div>
    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Semantic SEO Master</h1>

    <form id="semanticForm" class="w-full grid lg:grid-cols-[1fr,320px] gap-3">
      <div class="card flex items-center gap-2">
        <input name="url" type="url" required placeholder="Paste a URL (e.g. https://example.com/page)"
               class="w-full bg-transparent px-3 py-2 outline-none text-slate-100 placeholder:text-slate-400">
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)"
               class="w-72 max-w-[50%] bg-transparent px-3 py-2 outline-none text-slate-100 placeholder:text-slate-400 hidden md:block">
      </div>
      <button class="rounded-xl shadow-soft bg-gradient-to-r from-fuchsia-500 via-indigo-500 to-sky-500 hover:opacity-95 px-5 text-white font-semibold">
        Analyze URL
      </button>
    </form>

    <div id="waterbar" class="shadow-soft"><span style="width:0%"></span></div>
    <p class="text-xs text-slate-400">We fetch the page server-side and compute a semantic & readability report.</p>
  </div>

  <div id="resultWrap" class="space-y-8 hidden">
    <!-- Overall + Quick Stats -->
    <div class="grid lg:grid-cols-3 gap-6">
      <div class="card flex items-center gap-6">
        <div class="score-wheel">
          <div id="wheel" class="ring" style="--v:0"></div>
          <div id="scoreNum" class="center">0</div>
        </div>
        <div class="space-y-2">
          <div id="badge" class="k-badge">—</div>
          <div class="text-xs text-slate-400">Great ≥80 • Needs work 60–79 • Red &lt;60</div>
        </div>
      </div>

      <div class="card lg:col-span-2">
        <h3 class="font-semibold mb-3" style="background:linear-gradient(90deg,#67e8f9,#c084fc,#fb7185);-webkit-background-clip:text;background-clip:text;color:transparent;">Quick Stats</h3>
        <div class="grid sm:grid-cols-3 gap-4 text-sm">
          <div class="card">
            <div class="text-slate-300 text-xs">Readability (Flesch)</div>
            <div id="statFlesch" class="text-2xl font-bold">—</div>
            <div id="statGrade" class="text-xs text-slate-400">—</div>
          </div>
          <div class="card">
            <div class="text-slate-300 text-xs">Links (int / ext)</div>
            <div class="text-2xl font-bold"><span id="statInt">0</span> / <span id="statExt">0</span></div>
          </div>
          <div class="card">
            <div class="text-slate-300 text-xs">Text/HTML Ratio</div>
            <div id="statRatio" class="text-2xl font-bold">—</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Readability -->
    <div class="card">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold" style="background:linear-gradient(90deg,#67e8f9,#c084fc,#fb7185);-webkit-background-clip:text;background-clip:text;color:transparent;">Readability</h3>
        <span id="readBadge" class="pill">—</span>
      </div>
      <div class="grid md:grid-cols-2 gap-6 mt-4">
        <div class="flex items-center gap-6">
          <div class="score-wheel">
            <div id="readWheel" class="ring" style="--v:0"></div>
            <div id="readNum" class="center" style="font-size:40px">0</div>
          </div>
          <div class="flex-1 space-y-3">
            <div>
              <div class="text-xs text-slate-300 mb-1">Overall</div>
              <div class="bar"><span id="readBar" style="width:0%"></span></div>
            </div>
            <div class="text-xs text-slate-300">Grade level: <span id="gradeVal">—</span></div>
          </div>
        </div>
        <div class="card">
          <div class="text-xs text-slate-300">How to improve</div>
          <ul class="mt-2 list-disc pl-5 text-sm text-slate-200 space-y-1">
            <li>Use shorter sentences and active voice.</li>
            <li>Prefer common words; define jargon.</li>
            <li>Break up long paragraphs with headings and bullets.</li>
            <li>Add examples or visuals for complex ideas.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Recommendations -->
    <div class="card">
      <h3 class="font-semibold mb-3" style="background:linear-gradient(90deg,#67e8f9,#c084fc,#fb7185);-webkit-background-clip:text;background-clip:text;color:transparent;">Recommendations</h3>
      <div id="recs" class="grid md:grid-cols-2 gap-3"></div>
    </div>

    <!-- Content Structure -->
    <div class="card">
      <h3 class="font-semibold" style="background:linear-gradient(90deg,#67e8f9,#c084fc,#fb7185);-webkit-background-clip:text;background-clip:text;color:transparent;">Content Structure</h3>
      <div class="grid md:grid-cols-2 gap-6 mt-4">
        <div class="card">
          <div class="text-xs text-slate-300">Title</div>
          <div id="titleVal" class="font-semibold text-slate-100">—</div>
          <div class="text-xs text-slate-300 mt-3">Meta Description</div>
          <div id="metaVal" class="text-slate-200">—</div>
        </div>
        <div class="card">
          <div class="text-xs text-slate-300 mb-2">Heading Map</div>
          <div id="headingMap" class="text-sm space-y-2"></div>
        </div>
      </div>
    </div>

    <!-- Semantic SEO Ground (icon + multicolor heading, readable slab) -->
    <div class="ground-slab">
      <div class="ground-head">
        <div class="gh-icon">🧭</div>
        <div>
          <div class="gh-title">Semantic SEO Ground</div>
          <div class="gh-sub">Actionable checklists for structure, quality, UX & entities</div>
        </div>
      </div>

      <div id="cats" class="grid lg:grid-cols-2 gap-6 relative z-10"></div>
    </div>
  </div>

  <!-- Improve Modal -->
  <dialog id="improveModal" class="backdrop:bg-black/60 rounded-2xl p-0 w-[min(680px,95vw)]">
    <div class="card">
      <div class="flex items-start justify-between">
        <div>
          <h4 id="improveTitle" class="font-semibold text-slate-100">Improve</h4>
          <div class="text-xs text-slate-400 mt-1">Checklist details & tips to raise your score</div>
        </div>
        <form method="dialog"><button class="pill">Close</button></form>
      </div>

      <div class="grid md:grid-cols-3 gap-3 mt-4">
        <div class="card">
          <div class="text-xs text-slate-400">Category</div>
          <div id="improveCategory" class="font-semibold">—</div>
        </div>
        <div class="card">
          <div class="text-xs text-slate-400">Score</div>
          <div class="flex items-center gap-2 mt-1">
            <span id="improveScore" class="score-pill">—</span>
            <span id="improveBand" class="pill">—</span>
          </div>
        </div>
        <a id="improveSearch" target="_blank"
           class="card hover:opacity-90 transition text-center flex items-center justify-center bg-gradient-to-r from-fuchsia-500/20 to-sky-500/20 border border-white/10">
          <span class="text-sm text-slate-200">Search guidance</span>
        </a>
      </div>

      <div class="mt-4">
        <div class="text-xs text-slate-400">Why this matters</div>
        <p id="improveWhy" class="text-sm text-slate-200 mt-1">—</p>
        <p id="improveAdvice" class="text-xs text-slate-400 mt-2">—</p>
      </div>

      <div class="mt-4">
        <div class="text-xs text-slate-400">How to improve</div>
        <ul id="improveTips" class="mt-2 list-disc pl-5 text-sm text-slate-200 space-y-1"></ul>
      </div>
    </div>
  </dialog>

</section>

@push('scripts')
<script>
const f = document.getElementById('semanticForm');
const water = document.querySelector('#waterbar span');
const wrap = document.getElementById('resultWrap');

const wheel = document.getElementById('wheel');
const scoreNum = document.getElementById('scoreNum');
const badge = document.getElementById('badge');

const statF = document.getElementById('statFlesch');
const statG = document.getElementById('statGrade');
const statInt = document.getElementById('statInt');
const statExt = document.getElementById('statExt');
const statRatio = document.getElementById('statRatio');

const readWheel = document.getElementById('readWheel');
const readNum = document.getElementById('readNum');
const readBar = document.getElementById('readBar');
const readBadge = document.getElementById('readBadge');
const gradeVal = document.getElementById('gradeVal');

const headingMap = document.getElementById('headingMap');
const titleVal = document.getElementById('titleVal');
const metaVal = document.getElementById('metaVal');
const recsEl = document.getElementById('recs');
const catsEl = document.getElementById('cats');

const modal = document.getElementById('improveModal');
const mTitle = document.getElementById('improveTitle');
const mAdvice= document.getElementById('improveAdvice');
const mLink  = document.getElementById('improveSearch');

const band = (s) => (s>=80 ? 'green' : (s>=60 ? 'orange' : 'red'));
const bandLabel = (s) => (s>=80 ? 'Good (≥80)' : (s>=60 ? 'Needs work (60–79)' : 'Low (<60)'));
const pillClassBy = (s) => s>=80 ? 'score-pill--green' : s>=60 ? 'score-pill--orange' : 'score-pill--red';

function tipsFor(catName, label){
  const base = ['Aim for ≥80 (green) and re-run the analyzer after changes.'];
  switch (catName) {
    case 'Technical Elements':
      return [
        'Keep title ~50–60 chars with the primary keyword.',
        'Write a 140–160 char meta description with a clear CTA.',
        'Verify canonical is set; avoid duplicates.',
        'Ensure the page exists in your XML sitemap.'
      ].concat(base);
    case 'Content & Keywords':
      return [
        'State search intent and the primary topic in the intro.',
        'Use natural keyword variants and PAA-style questions.',
        'Single, descriptive H1 including the topic.',
        'Add an FAQ block with concise answers.',
        'Prefer clear, NLP-friendly language.'
      ].concat(base);
    case 'Structure & Architecture':
      return ['Logical H2/H3 topic clusters.','Internal links to hub/related pages.','Clean, descriptive URLs.','Enable breadcrumbs (+ schema).'].concat(base);
    case 'Content Quality':
      return ['Show E-E-A-T (author, date, expertise).','Demonstrate unique value vs competitors.','Cite recent, authoritative sources.','Use helpful media with captions.'].concat(base);
    case 'User Signals & Experience':
      return ['Responsive layout for mobile.','Optimize speed (compression, lazy-load).','Watch Core Web Vitals (LCP/INP/CLS).','Make CTAs obvious.'].concat(base);
    case 'Entities & Context':
      return ['Define a clear primary entity.','Cover related entities with context.','Add valid schema (Article/FAQ/Product).','Add sameAs/organization details.'].concat(base);
    default: return base;
  }
}

function labelBy(score){ return score>=80?'Great Work — Well Optimized':(score>=60?'Needs Optimization':'Needs Significant Optimization'); }

f.addEventListener('submit', async (e)=>{
  e.preventDefault();
  water.style.width = '0%';
  setTimeout(()=>water.style.width='100%', 30);

  const fd = new FormData(f);
  const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') || '' };

  try{
    const res = await fetch('/api/semantic-analyze', {
      method:'POST',
      headers:{ 'Accept':'application/json','Content-Type':'application/json' },
      body: JSON.stringify(payload)
    });
    let data = {};
    try { data = await res.json(); } catch(e){}
    if(!res.ok || (data && data.error)){
      alert((data && data.error) || ('Analysis failed (HTTP '+res.status+')'));
      water.style.width='0%';
      return;
    }

    wrap.classList.remove('hidden');

    // Overall
    const score = data.overall_score||0;
    wheel.style.setProperty('--v', score);
    scoreNum.textContent = score;
    badge.textContent = (data.wheel && data.wheel.label) || labelBy(score);

    // Quick stats
    statF.textContent = data.quick_stats?.readability_flesch ?? '—';
    statG.textContent = 'Grade ' + (data.quick_stats?.readability_grade ?? '—');
    statInt.textContent = data.quick_stats?.internal_links ?? 0;
    statExt.textContent = data.quick_stats?.external_links ?? 0;
    statRatio.textContent = (data.quick_stats?.text_to_html_ratio ?? 0) + '%';

    // Readability (wheel uses 0–100)
    const rf = Math.max(0, Math.min(100, data.readability?.score ?? 0));
    readWheel.style.setProperty('--v', rf);
    readNum.textContent = Math.round(rf);
    readBar.style.width = rf + '%';
    gradeVal.textContent = 'Grade ' + (data.readability?.grade ?? '—');
    readBadge.textContent = labelBy(rf);

    // Structure
    titleVal.textContent = data.content_structure?.title || '—';
    metaVal.textContent  = data.content_structure?.meta_description || '—';
    headingMap.innerHTML='';
    Object.entries(data.content_structure?.headings || {}).forEach(([lvl,arr])=>{
      if(!arr || !arr.length) return;
      const box = document.createElement('div');
      box.className='card';
      box.innerHTML = `<div class="text-xs text-slate-300 mb-1 uppercase">${lvl}</div>` + arr.map(t=>`<div>• ${t}</div>`).join('');
      headingMap.appendChild(box);
    });

    // Recommendations
    recsEl.innerHTML='';
    (data.recommendations||[]).forEach(r=>{
      const c = document.createElement('div');
      c.className='card';
      c.innerHTML = `<span class="pill mr-2">${r.severity}</span>${r.text}`;
      recsEl.appendChild(c);
    });

    // Categories
    catsEl.innerHTML='';
    (data.categories||[]).forEach(cat=>{
      const total = (cat.checks||[]).length;
      const passed = (cat.checks||[]).filter(ch => (ch.score||0) >= 80).length;
      const pct = Math.round((passed/Math.max(1,total))*100);

      const card = document.createElement('div');
      card.className = 'cat-card';

      card.innerHTML = `
        <div class="cat-head">
          <div class="flex items-center gap-3">
            <div class="cat-icon">${cat.icon||'★'}</div>
            <div>
              <div class="cat-title">${cat.name||'Category'}</div>
              <div class="text-slate-300 text-sm">Keep improving</div>
            </div>
          </div>
          <div class="cat-badge">${passed} / ${total}</div>
        </div>
        <div class="progress mb-3"><span style="width:${pct}%"></span></div>
        <div class="space-y-2" id="list"></div>
      `;

      const list = card.querySelector('#list');
      (cat.checks||[]).forEach(ch=>{
        const color = band(ch.score ?? 0);
        const row = document.createElement('div');
        row.className = 'check';
        row.innerHTML = `
          <div class="left">
            <span class="dot ${color}"></span>
            <div class="text text-slate-100">${ch.label}</div>
          </div>
          <div class="flex items-center gap-2">
            <span class="score-pill"></span>
            <button type="button" class="improve-btn">Improve</button>
          </div>
        `;

        // score pill color/value
        const pill = row.querySelector('.score-pill');
        const sVal = ch.score ?? 0;
        pill.textContent = (sVal || sVal===0) ? sVal : '—';
        pill.classList.remove('score-pill--green','score-pill--orange','score-pill--red');
        pill.classList.add(pillClassBy(sVal));

        // Improve popup
        row.querySelector('.improve-btn').addEventListener('click', ()=>{
          const s = ch.score ?? 0;
          mTitle.textContent = ch.label;
          document.getElementById('improveCategory').textContent = cat.name || '—';
          document.getElementById('improveScore').textContent = s;

          const bandEl = document.getElementById('improveBand');
          bandEl.textContent = bandLabel(s);
          bandEl.className = 'pill ' + (s>=80 ? 'score-pill--green' : s>=60 ? 'score-pill--orange' : 'score-pill--red');

          document.getElementById('improveWhy').textContent =
            ch.why || 'This checklist influences topical authority, UX, and eligibility for rich results.';
          const tips = ch.tips || tipsFor(cat.name, ch.label);
          const ul = document.getElementById('improveTips'); ul.innerHTML = '';
          tips.forEach(t => { const li = document.createElement('li'); li.textContent = t; ul.appendChild(li); });

          if (mAdvice) mAdvice.textContent = ch.advice || 'Follow the tips below to raise this score.';
          mLink.href = ch.improve_search_url || ('https://www.google.com/search?q='+encodeURIComponent(ch.label+' SEO best practices'));

          if (modal && typeof modal.showModal === 'function') modal.showModal();
          else if (modal) modal.setAttribute('open','');
        });

        list.appendChild(row);
      });

      catsEl.appendChild(card);
    });

    // finish waterbar at actual score
    setTimeout(()=>{ water.style.width = (score+'%'); }, 150);
  } catch(err){
    alert('Request error. Check storage/logs/laravel.log');
    water.style.width='0%';
  }
});

// click outside to close modal
if (modal) {
  modal.addEventListener('click', (e)=>{
    const rect = modal.getBoundingClientRect();
    const inside = (e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom);
    if (!inside) {
      if (typeof modal.close === 'function') modal.close();
      else modal.removeAttribute('open');
    }
  });
}
</script>
@endpush
@endsection
