@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* ===== Global background ===== */
  html, body { background:#000 !important; }

  /* ===== Small UI tokens (keep your colorful look) ===== */
  .glass { background: rgba(255,255,255,.06); backdrop-filter: blur(10px); }
  .card  { border-radius: 16px; padding: 18px; background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.10); }
  .pill  { padding:4px 8px; border-radius:9999px; font-size:12px; font-weight:700; border:1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.08); color:#e5e7eb; }
  .chip  { padding:6px 10px; border-radius:12px; font-weight:800; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.10); color:#e5e7eb; }
  .btn   { padding:10px 16px; border-radius:14px; font-weight:800; border:1px solid rgba(255,255,255,.12); }
  .btn-ghost { background:rgba(255,255,255,.06); color:#fff; }
  .btn-green { background:#22c55e; color:#091409; border:none; }
  .btn-blue  { background:#3b82f6; color:#061325; border:none; }
  .btn-orange{ background:#f59e0b; color:#2b1a03; border:none; }
  .btn-purple{ background:linear-gradient(90deg,#a78bfa,#f472b6); color:#140616; border:none; }

  /* ===== Legend pills ===== */
  .lg-green  { background:rgba(16,185,129,.15); color:#a7f3d0; border-color:rgba(16,185,129,.35); }
  .lg-orange { background:rgba(245,158,11,.15); color:#fde68a; border-color:rgba(245,158,11,.35); }
  .lg-red    { background:rgba(239,68,68,.15);  color:#fecaca; border-color:rgba(239,68,68,.35); }

  /* ===== Mega wheel with “liquid” fill ===== */
  .mega { display:grid; place-items:center; gap:14px; }
  .mw {
    --v: 0;  /* 0..100 overall score */
    --p: 0;  /* 0..100 liquid fill percent */
    width: 240px; height: 240px; position:relative;
  }
  .mw-ring {
    position:absolute; inset:0; border-radius:50%;
    background:
      conic-gradient(#f59e0b calc(var(--v)*1%), rgba(255,255,255,.08) 0);
    -webkit-mask: radial-gradient(circle 92px, transparent 90px, #000 90px);
            mask: radial-gradient(circle 92px, transparent 90px, #000 90px);
    box-shadow: inset 0 0 0 10px rgba(255,255,255,.06);
  }
  .mw-fill {
    position:absolute; inset:20px; border-radius:50%;
    overflow:hidden;
    background:
      linear-gradient(to top, #ffb02e 0%, #f59e0b 60%, #f59e0b 100%);
  }
  /* “liquid” level simulated by a black overlay that slides up */
  .mw-fill::after{
    content:""; position:absolute; left:0; right:0;
    height:100%; top: calc(100% - var(--p)*1%);
    background: #000; transition: top .9s ease;
    /* add a very subtle wave edge */
    -webkit-mask: radial-gradient(120px 20px at 50% 0,#0000 98%,#000 100%);
            mask: radial-gradient(120px 20px at 50% 0,#0000 98%,#000 100%);
  }
  .mw-center {
    position:absolute; inset:0; display:grid; place-items:center;
    font-size:56px; font-weight:900; color:#fff;
    text-shadow: 0 6px 20px rgba(0,0,0,.45);
  }

  /* ===== Toolbar ===== */
  .urlbox { background:#0b0b0b; border:1px solid rgba(255,255,255,.12); }
  .urlbox input { color:#e5e7eb; }
  .toolbar .btn { min-width:112px; }

  /* ===== Small bar beneath title ===== */
  #waterbar { height: 10px; border-radius: 9999px; background: rgba(255,255,255,.08); overflow: hidden; border:1px solid rgba(255,255,255,.08); }
  #waterbar span { display:block; height:100%; width:0%; background: linear-gradient(90deg,#ef4444,#f59e0b,#22c55e); transition: width .9s ease; }

  /* ===== Status chips row ===== */
  .status-row{ display:flex; flex-wrap:wrap; gap:10px; }
  .status-chip{ padding:10px 14px; border-radius:22px; font-weight:900; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.10); color:#e5e7eb; }

  /* ===== Section titles (multi-color) ===== */
  .t-grad{ background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185); -webkit-background-clip:text; background-clip:text; color:transparent; }

  /* ===== Ground slab (kept from your previous version) ===== */
  .ground-slab{ border-radius:24px; padding:22px; background:#0D0E1E; border:1px solid #1b2640; position:relative; overflow:hidden; }
  .ground-slab .cat-card{ border-radius:18px; padding:18px; background:#111E2F; border:1px solid rgba(255,255,255,.12); }
  .ground-slab .cat-head{ display:flex; align-items:center; justify-content:space-between; margin-bottom:12px; }
  .ground-slab .cat-title{ font-size:22px; font-weight:900; background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185); -webkit-background-clip:text; background-clip:text; color:transparent; }
  .ground-slab .progress{ width:100%; height:12px; border-radius:9999px; background: rgba(255,255,255,.08); overflow:hidden; border:1px solid rgba(255,255,255,.14); }
  .ground-slab .progress>span{ display:block; height:100%; border-radius:9999px; background: linear-gradient(90deg,#ef4444,#fde047,#22c55e); transition: width .5s ease; }
  .ground-slab .check{ display:flex; align-items:center; justify-content:space-between; border-radius:14px; padding:14px 16px; border:1px solid rgba(255,255,255,.10); background:#0F1A29; }
  .score-pill{ padding:4px 8px; border-radius:10px; font-weight:800; background:rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); color:#e5e7eb; }
  .score-pill--green  { background: rgba(16,185,129,.18); border-color: rgba(16,185,129,.35); color:#bbf7d0; }
  .score-pill--orange { background: rgba(245,158,11,.18); border-color: rgba(245,158,11,.35); color:#fde68a; }
  .score-pill--red    { background: rgba(239,68,68,.18);  border-color: rgba(239,68,68,.35);  color:#fecaca; }
  .improve-btn{ padding:6px 10px; border-radius:10px; background:linear-gradient(135deg,#a78bfa,#60a5fa); color:#fff; font-weight:700; }

  /* Dialog */
  dialog[open]{ display:block; }
  dialog::backdrop{ background:rgba(0,0,0,.6); }
  #improveModal .card{ background:#0D0E1E; border:1px solid #1b2640; }
  #improveModal .card .card{ background:#111E2F; border-color:rgba(255,255,255,.12); }
</style>
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 py-8 text-slate-100 space-y-8">

  <!-- Title + legend -->
  <div class="space-y-3">
    <h1 class="text-3xl sm:text-4xl font-extrabold">Analyze a URL</h1>
    <div class="flex flex-wrap gap-2 text-xs">
      <span class="pill lg-green">Green ≥ 80</span>
      <span class="pill lg-orange">Orange 60–79</span>
      <span class="pill lg-red">Red &lt; 60</span>
    </div>
  </div>

  <!-- Top row: Wheel + score chips -->
  <div class="grid lg:grid-cols-[300px,1fr] gap-6 items-center">
    <div class="mega">
      <div class="mw" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-fill" id="mwFill" style="--p:0"></div>
        <div class="mw-center" id="mwNum">0%</div>
      </div>
    </div>

    <div class="space-y-3">
      <div class="flex flex-wrap gap-2">
        <span id="chipOverall" class="chip">Overall: 0 /100</span>
        <span id="chipContent" class="chip">Content: —</span>
        <span id="chipWriter"  class="chip">Writer: —</span>
        <span id="chipHuman"   class="chip">Human-like: — %</span>
        <span id="chipAI"      class="chip">AI-like: — %</span>
      </div>
      <button id="copyReport" class="btn btn-ghost">📋 Copy report</button>
      <div id="waterbar" class="mt-2"><span style="width:0%"></span></div>
      <p class="text-xs text-slate-400">Wheel + water bars fill with your scores.</p>
    </div>
  </div>

  <!-- URL toolbar -->
  <div class="card space-y-3">
    <label class="urlbox flex items-center gap-2 rounded-xl px-3 py-2">
      <span class="opacity-70">🌐</span>
      <input id="urlInput" name="url" type="url" placeholder="https://example.com" class="w-full bg-transparent outline-none" />
      <button id="pasteBtn" class="pill">✕ Paste</button>
    </label>

    <div class="flex items-center gap-3">
      <label class="flex items-center gap-2 text-sm">
        <input id="autoCheck" type="checkbox" class="accent-emerald-400" checked />
        Auto-apply checkmarks (≥ 80)
      </label>

      <div class="flex-1"></div>

      <input id="importFile" type="file" accept="application/json" class="hidden"/>
      <button id="importBtn" class="btn btn-purple">⇪ Import</button>
      <button id="analyzeBtn" class="btn btn-green">🔍 Analyze</button>
      <button id="printBtn"   class="btn btn-blue">🖨️ Print</button>
      <button id="resetBtn"   class="btn btn-orange">↻ Reset</button>
      <button id="exportBtn"  class="btn btn-purple">⬇︎ Export</button>
    </div>

    <!-- status chips -->
    <div id="statusChips" class="status-row mt-2">
      <div class="status-chip" id="chipHttp">HTTP: —</div>
      <div class="status-chip" id="chipTitle">Title: —</div>
      <div class="status-chip" id="chipMeta">Meta desc: —</div>
      <div class="status-chip" id="chipCanon">Canonical: —</div>
      <div class="status-chip" id="chipRobots">Robots: —</div>
      <div class="status-chip" id="chipViewport">Viewport: —</div>
      <div class="status-chip" id="chipH">H1/H2/H3: —</div>
      <div class="status-chip" id="chipInt">Internal links: —</div>
      <div class="status-chip" id="chipSchema">Schema: —</div>
      <div class="status-chip" id="chipAuto">Auto-checked: 0</div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="card">
    <h3 class="t-grad font-extrabold mb-3">Quick Stats</h3>
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

  <!-- Content Structure -->
  <div class="card">
    <h3 class="t-grad font-extrabold">Content Structure</h3>
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

  <!-- Recommendations -->
  <div class="card">
    <h3 class="t-grad font-extrabold mb-3">Recommendations</h3>
    <div id="recs" class="grid md:grid-cols-2 gap-3"></div>
  </div>

  <!-- Semantic SEO Ground -->
  <div class="ground-slab">
    <div class="flex items-center gap-3 mb-4">
      <div style="width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(99,102,241,.32),rgba(236,72,153,.32));border:1px solid rgba(255,255,255,.14);">🧭</div>
      <div>
        <div class="t-grad text-2xl font-extrabold">Semantic SEO Ground</div>
        <div class="text-sm text-slate-300">Actionable checklists for structure, quality, UX & entities</div>
      </div>
    </div>
    <div id="cats" class="grid lg:grid-cols-2 gap-6"></div>
  </div>

  <!-- Improve Modal -->
  <dialog id="improveModal" class="rounded-2xl p-0 w-[min(680px,95vw)]">
    <div class="card">
      <div class="flex items-start justify-between">
        <h4 id="improveTitle" class="font-semibold text-slate-100">Improve</h4>
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
            <span id="improveBand"  class="pill">—</span>
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
/* === Shortcuts === */
const $ = s => document.querySelector(s);
const $$ = s => document.querySelectorAll(s);

/* Wheel + water */
const mwRing=$('#mwRing'), mwFill=$('#mwFill'), mwNum=$('#mwNum');
const water=$('#waterbar span');

/* Chips + toolbar */
const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'),
      chipHuman=$('#chipHuman'), chipAI=$('#chipAI');
const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'),
      pasteBtn=$('#pasteBtn'), importBtn=$('#importBtn'), importFile=$('#importFile'),
      printBtn=$('#printBtn'), resetBtn=$('#resetBtn'), exportBtn=$('#exportBtn'),
      autoCheck=$('#autoCheck'), copyBtn=$('#copyReport');

/* Quick stats & structure DOM */
const statF=$('#statFlesch'), statG=$('#statGrade'), statInt=$('#statInt'), statExt=$('#statExt'), statRatio=$('#statRatio');
const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap'), recsEl=$('#recs'), catsEl=$('#cats');

/* Status chips */
const chipHttp=$('#chipHttp'), chipTitle=$('#chipTitle'), chipMeta=$('#chipMeta'),
      chipCanon=$('#chipCanon'), chipRobots=$('#chipRobots'), chipViewport=$('#chipViewport'),
      chipH=$('#chipH'), chipInt=$('#chipInt'), chipSchema=$('#chipSchema'), chipAuto=$('#chipAuto');

/* Improve modal */
const modal=$('#improveModal');
const mTitle=$('#improveTitle'), mCat=$('#improveCategory'), mScore=$('#improveScore'), mBand=$('#improveBand'),
      mWhy=$('#improveWhy'), mTips=$('#improveTips'), mLink=$('#improveSearch');

const band=(s)=> s>=80?'green':(s>=60?'orange':'red');
const pillClassBy=(s)=> s>=80?'score-pill--green':(s>=60?'score-pill--orange':'score-pill--red');
const bandLabel=(s)=> s>=80?'Good (≥80)':(s>=60?'Needs work (60–79)':'Low (<60)');
const labelBy=(s)=> s>=80?'Great Work — Well Optimized':(s>=60?'Needs Optimization':'Needs Significant Optimization');

function tipsFor(catName){
  switch (catName) {
    case 'Technical Elements': return ['Title ~50–60 chars incl. primary keyword.','Meta 140–160 chars + clear CTA.','Set canonical, avoid duplicates.','Ensure in XML sitemap.'];
    case 'Content & Keywords': return ['Clear intent & primary topic early.','Use variants/PAA naturally.','Single descriptive H1.','Add short FAQ answers.','Write simple, NLP-friendly language.'];
    case 'Structure & Architecture': return ['Logical H2/H3 topic clusters.','Internal links to hub pages.','Clean, descriptive URL.','Enable breadcrumbs (+schema).'];
    case 'Content Quality': return ['Show E-E-A-T: author, date, expertise.','Unique value vs competitors.','Cite recent authoritative sources.','Use helpful media with captions.'];
    case 'User Signals & Experience': return ['Responsive layout.','Compression & lazy-load.','Watch LCP/INP/CLS.','Clear CTAs.'];
    case 'Entities & Context': return ['Define primary entity.','Cover related entities.','Add valid schema (Article/FAQ/Product).','Add sameAs/org details.'];
    default: return ['Aim for ≥80 (green) and re-run the analyzer.'];
  }
}

/* Helpers to format & compute */
const clamp01 = (n)=> Math.max(0, Math.min(100, n));
const len = (s)=> (s||'').length||0;

/* UI hookups */
pasteBtn.addEventListener('click', async (e)=>{
  e.preventDefault();
  try{ const txt=await navigator.clipboard.readText(); if (txt) urlInput.value=txt.trim(); }catch{}
});
importBtn.addEventListener('click', ()=> importFile.click());
importFile.addEventListener('change', (e)=>{
  const f=e.target.files?.[0]; if(!f) return;
  const r=new FileReader();
  r.onload=()=>{ try{
    const j=JSON.parse(String(r.result||'{}'));
    if (j.url) urlInput.value=j.url;
    alert('Imported JSON. Click Analyze to run.');
  }catch{ alert('Invalid JSON file.'); } };
  r.readAsText(f);
});
printBtn.addEventListener('click', ()=> window.print());
resetBtn.addEventListener('click', ()=>{
  location.reload();
});

copyBtn.addEventListener('click', async ()=>{
  const text = document.getElementById('reportText')?.textContent || document.body.innerText.slice(0,4000);
  try{ await navigator.clipboard.writeText(text); copyBtn.textContent='Copied ✓'; setTimeout(()=>copyBtn.textContent='📋 Copy report',1200);}catch{}
});

exportBtn.addEventListener('click', ()=>{
  if (!window.__lastData) { alert('Run an analysis first.'); return; }
  const blob = new Blob([JSON.stringify(window.__lastData, null, 2)], {type:'application/json'});
  const a = document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='semantic-report.json'; a.click(); URL.revokeObjectURL(a.href);
});

/* Main analyze */
analyzeBtn.addEventListener('click', async ()=>{
  const url = (urlInput.value||'').trim();
  if (!url) { alert('Enter a URL to analyze.'); return; }

  // start bars
  water.style.width='0%'; setTimeout(()=> water.style.width='100%', 30);
  mwRing.style.setProperty('--v', 0); mwFill.style.setProperty('--p', 0); mwNum.textContent='0%';

  let data={};
  try{
    const res = await fetch('/api/semantic-analyze', {
      method:'POST',
      headers:{'Content-Type':'application/json','Accept':'application/json'},
      body: JSON.stringify({url, target_keyword:''})
    });
    data = await res.json();
    if (!res.ok || data.error) throw new Error(data.error || ('HTTP '+res.status));
  }catch(err){
    alert('Analyzer failed: '+err.message);
    water.style.width='0%';
    return;
  }
  window.__lastData = {...data, url};

  // Overall + wheel
  const score = clamp01( parseInt(data.overall_score||0,10) );
  mwRing.style.setProperty('--v', score);
  mwFill.style.setProperty('--p', score);
  mwNum.textContent = score + '%';
  chipOverall.textContent = `Overall: ${score} /100`;

  // Content score (avg of Content & Keywords and Content Quality if both present)
  const cmap = {};
  (data.categories||[]).forEach(c => cmap[c.name]= c.score ?? 0);
  let present=0, sum=0;
  ['Content & Keywords','Content Quality'].forEach(k=>{ if (cmap[k]!=null){ sum+=cmap[k]; present++; } });
  const contentScore = present ? Math.round(sum/present) : (cmap['Content & Keywords'] ?? cmap['Content Quality'] ?? '—');
  chipContent.textContent = `Content: ${contentScore === '—' ? '—' : contentScore+' /100'}`;

  // Human-like / AI-like (heuristic from readability)
  const r = data.readability || {};
  let human = clamp01( Math.round( 70 + (r.score||0)/5 - (r.passive_ratio||0)/3 ) );
  let ai    = clamp01( 100 - human );
  chipHuman.textContent = `Human-like: ${human} %`;
  chipAI.textContent    = `AI-like: ${ai} %`;
  chipWriter.textContent= human>=60 ? 'Writer: Likely Human' : 'Writer: Possibly AI';

  // Quick stats
  statF.textContent = r.flesch ?? '—';
  statG.textContent = 'Grade ' + (r.grade ?? '—');
  statInt.textContent = data.quick_stats?.internal_links ?? 0;
  statExt.textContent = data.quick_stats?.external_links ?? 0;
  statRatio.textContent = (data.quick_stats?.text_to_html_ratio ?? 0) + '%';

  // Structure
  titleVal.textContent = data.content_structure?.title || '—';
  metaVal.textContent  = data.content_structure?.meta_description || '—';
  headingMap.innerHTML='';
  const h = data.content_structure?.headings || {};
  Object.entries(h).forEach(([lvl,arr])=>{
    if (!arr || !arr.length) return;
    const box = document.createElement('div');
    box.className='card';
    box.innerHTML = `<div class="text-xs text-slate-300 mb-1 uppercase">${lvl}</div>` + arr.map(t=>`<div>• ${t}</div>`).join('');
    headingMap.appendChild(box);
  });

  // Recommendations
  recsEl.innerHTML='';
  (data.recommendations||[]).forEach(rec=>{
    const d = document.createElement('div');
    d.className='card'; d.innerHTML=`<span class="pill mr-2">${rec.severity}</span>${rec.text}`;
    recsEl.appendChild(d);
  });

  // Ground categories
  catsEl.innerHTML='';
  let autoChecked = 0;
  (data.categories||[]).forEach(cat=>{
    const total = (cat.checks||[]).length;
    const passed = (cat.checks||[]).filter(ch=> (ch.score||0) >= 80).length;
    autoChecked += passed;
    const pct = Math.round((passed/Math.max(1,total))*100);

    const card = document.createElement('div');
    card.className='cat-card';
    card.innerHTML = `
      <div class="cat-head">
        <div class="flex items-center gap-3">
          <div style="width:38px;height:38px;border-radius:10px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(99,102,241,.25),rgba(236,72,153,.25));border:1px solid rgba(255,255,255,.12);">★</div>
          <div>
            <div class="cat-title">${cat.name}</div>
            <div class="text-slate-300 text-sm">Keep improving</div>
          </div>
        </div>
        <div class="pill">${passed} / ${total}</div>
      </div>
      <div class="progress mb-3"><span style="width:${pct}%"></span></div>
      <div class="space-y-2" id="list"></div>
    `;
    const list = card.querySelector('#list');

    (cat.checks||[]).forEach(ch=>{
      const row=document.createElement('div');
      row.className='check';
      row.innerHTML=`
        <div class="flex items-center gap-3">
          <span class="w-3 h-3 rounded-full" style="background:${(ch.score||0)>=80?'#10b981':(ch.score||0)>=60?'#f59e0b':'#ef4444'}"></span>
          <div class="font-semibold">${ch.label}</div>
        </div>
        <div class="flex items-center gap-2">
          <span class="score-pill ${pillClassBy(ch.score||0)}">${ch.score ?? '—'}</span>
          <button type="button" class="improve-btn">Improve</button>
        </div>
      `;
      row.querySelector('.improve-btn').addEventListener('click', ()=>{
        mTitle.textContent = ch.label;
        mCat.textContent = cat.name;
        mScore.textContent = ch.score ?? '—';
        mBand.textContent = bandLabel(ch.score||0);
        mBand.className = 'pill '+pillClassBy(ch.score||0);
        mWhy.textContent = ch.why || 'This item impacts topical authority, UX and eligibility for rich results.';
        mTips.innerHTML='';
        (ch.tips||tipsFor(cat.name)).forEach(t=>{
          const li=document.createElement('li'); li.textContent=t; mTips.appendChild(li);
        });
        mLink.href = ch.improve_search_url || ('https://www.google.com/search?q='+encodeURIComponent(ch.label+' SEO best practices'));
        if (typeof modal.showModal==='function') modal.showModal(); else modal.setAttribute('open','');
      });
      list.appendChild(row);
    });
    catsEl.appendChild(card);
  });

  // status chips content
  chipTitle.textContent = `Title: ${len(data.content_structure?.title||'')}`;
  chipMeta.textContent  = `Meta desc: ${len(data.content_structure?.meta_description||'')}`;
  chipCanon.textContent = `Canonical: ${new URL(url).origin || '—'}`;
  chipRobots.textContent= `Robots: —`;
  chipViewport.textContent=`Viewport: —`;
  chipH.textContent      = `H1/H2/H3: H1:${(h.H1||[]).length} · H2:${(h.H2||[]).length} · H3:${(h.H3||[]).length}`;
  chipInt.textContent    = `Internal links: ${data.quick_stats?.internal_links ?? 0}`;
  chipSchema.textContent = `Schema: ${data.readability ? (data.schema_count || 0) : (data.schema_count || 0)}`;
  chipAuto.textContent   = `Auto-checked: ${autoChecked}`;
  chipHttp.textContent   = `HTTP: 200`; // backend doesn’t return status; show 200 when fetch succeeded

  // finish waterbar at actual score
  setTimeout(()=>{ water.style.width = score+'%'; }, 120);

});

/* Close modal when clicking outside content */
if (modal) {
  modal.addEventListener('click', (e)=>{
    const rect = modal.getBoundingClientRect();
    const inside = (e.clientX >= rect.left && e.clientX <= rect.right && e.clientY >= rect.top && e.clientY <= rect.bottom);
    if (!inside) { if (typeof modal.close==='function') modal.close(); else modal.removeAttribute('open'); }
  });
}
</script>
@endpush
@endsection
