@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@push('head')
<style>
  /* ===== Page tech background ===== */
  .bg-tech {
    position: fixed; inset: 0;
    background:
      radial-gradient(1200px 600px at 20% -10%, rgba(255,255,255,.05), transparent),
      repeating-linear-gradient(115deg, transparent 0 18px, rgba(236,72,153,.25) 18px 19px),
      #000; /* pure black */
    pointer-events:none; z-index:-1;
  }

  /* ===== Shared tokens ===== */
  .card { border-radius: 16px; padding: 20px; background: rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(10px); }
  .pill  { padding:4px 8px; border-radius:9999px; font-size:12px; font-weight:600; border:1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.06); }
  .k-badge{ padding:6px 8px; border-radius:10px; font-size:12px; font-weight:700; border:1px solid rgba(255,255,255,.12); background: rgba(255,255,255,.06); }
  .shadow-soft { box-shadow: 0 12px 40px rgba(0,0,0,.25); }
  .bar { height:12px; border-radius:9999px; background: rgba(255,255,255,.08); overflow:hidden; }
  .bar > span { display:block; height:100%; border-radius:9999px; transition: width .5s ease; background: linear-gradient(90deg,#22c55e,#60a5fa,#d946ef); }
  .chip { font-size:12px; padding:4px 8px; border-radius:8px; background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.10); }

  /* ===== Water loading bar ===== */
  #waterbar { height: 10px; border-radius: 9999px; background: rgba(255,255,255,.08); overflow: hidden; }
  #waterbar span { display:block; height:100%; width:0%; background: linear-gradient(90deg,#ef4444,#f59e0b,#22c55e); transition: width .8s ease; filter: drop-shadow(0 0 10px rgba(148,163,184,.4)); }

  /* ===== Wheels ===== */
  .score-wheel { width:180px; height:180px; display:grid; place-items:center; position:relative; }
  .score-wheel .ring {
    --v: 0;
    width: 100%; height: 100%; border-radius: 50%;
    background:
      conic-gradient(#22c55e calc(var(--v)*1%), #f59e0b calc(var(--v)*1% + .0001%), #ef4444 100%);
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

  /* ===== Semantic SEO Ground — slab background & header ===== */
  .ground-slab{
    border-radius: 24px;
    padding: 22px;
    background:
      radial-gradient(1200px 600px at -10% -20%, rgba(120,119,198,.14), transparent 55%),
      radial-gradient(900px 500px at 120% 120%, rgba(236,72,153,.12), transparent 60%),
      linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01)),
      #0b0b10; /* deep, readable base */
    border: 1px solid rgba(255,255,255,.10);
    position: relative;
    overflow: hidden;
  }
  .ground-slab::after{
    content:"";
    position:absolute; inset:0; pointer-events:none;
    background:
      repeating-linear-gradient(
        -65deg,
        rgba(255,255,255,.05) 0 2px,
        transparent 2px 26px
      );
    opacity:.35;
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

  /* ===== Category cards inside the slab (more contrast + lines) ===== */
  .ground-slab .cat-card{
    border-radius: 18px; padding: 18px; position:relative;
    background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
    border:1px solid rgba(255,255,255,.12);
    box-shadow: 0 12px 30px rgba(0,0,0,.25);
  }
  .ground-slab .cat-card::before{
    content:""; position:absolute; inset:0; pointer-events:none;
    background: repeating-linear-gradient(-65deg, rgba(255,255,255,.06) 0 2px, transparent 2px 28px);
    opacity:.25; border-radius: inherit;
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

  /* Checklist rows — larger, clearer */
  .ground-slab .check{ display:flex; align-items:center; justify-content:space-between; border-radius:14px; padding:14px 16px; border:1px solid rgba(255,255,255,.10);
           background: linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02)); }
  .ground-slab .check + .check { margin-top:8px; }
  .ground-slab .check .left { display:flex; align-items:center; gap:10px; }
  .ground-slab .check .text{ color:#f8fafc; font-weight:700; font-size: 18px; }
  .dot { width:10px; height:10px; border-radius:9999px; }
  .dot.green{ background:#10b981; box-shadow:0 0 10px rgba(16,185,129,.6); }
  .dot.orange{ background:#f59e0b; box-shadow:0 0 10px rgba(245,158,11,.6); }
  .dot.red{ background:#ef4444; box-shadow:0 0 10px rgba(239,68,68,.6); }
  .score-pill { font-size:12px; font-weight:800; padding:4px 8px; border-radius:10px; background: rgba(255,255,255,.08); border:1px solid rgba(255,255,255,.12); color:#e5e7eb; }
  .improve-btn { font-size:12px; font-weight:700; padding:6px 10px; border-radius:10px; background: linear-gradient(135deg,#a78bfa,#60a5fa); color:white; box-shadow: 0 8px 16px rgba(96,165,250,.25); }
</style>
@endpush

@section('content')
<div class="bg-tech"></div>

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
    <p class="text-xs text-slate-400">Tip: we fetch the page server-side and compute a detailed semantic & readability report.</p>
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
  <dialog id="improveModal" class="backdrop:bg-black/60 rounded-2xl p-0 w-[min(560px,95vw)]">
    <div class="card">
      <div class="flex items-start justify-between">
        <h4 id="improveTitle" class="font-semibold">Improve</h4>
        <form method="dialog"><button class="pill">Close</button></form>
      </div>
      <p id="improveAdvice" class="mt-3 text-sm text-slate-200">—</p>
      <a id="improveSearch" target="_blank" class="inline-block mt-4 px-3 py-2 rounded-lg bg-gradient-to-r from-fuchsia-500 to-sky-500 text-white text-sm">Search guidance</a>
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
    const data = await res.json();
    if(!data.ok){ alert(data.error || 'Analysis failed'); water.style.width='0%'; return; }

    wrap.classList.remove('hidden');

    // Overall
    const score = data.overall_score||0;
    wheel.style.setProperty('--v', score);
    scoreNum.textContent = score;
    badge.textContent = data.wheel?.label || labelBy(score);

    // Quick stats
    statF.textContent = data.quick_stats.readability_flesch;
    statG.textContent = 'Grade ' + data.quick_stats.readability_grade;
    statInt.textContent = data.quick_stats.internal_links;
    statExt.textContent = data.quick_stats.external_links;
    statRatio.textContent = data.quick_stats.text_to_html_ratio + '%';

    // Readability (FRE clamped to 0–100 for wheel)
    const rf = Math.max(0, Math.min(100, data.readability.score||0));
    readWheel.style.setProperty('--v', rf);
    readNum.textContent = Math.round(rf);
    readBar.style.width = rf + '%';
    gradeVal.textContent = 'Grade ' + data.readability.grade;
    readBadge.textContent = labelBy(rf);

    // Structure
    titleVal.textContent = data.content_structure.title || '—';
    metaVal.textContent  = data.content_structure.meta_description || '—';
    headingMap.innerHTML='';
    Object.entries(data.content_structure.headings||{}).forEach(([lvl,arr])=>{
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

    // Categories (large cards with icon + progress inside slab)
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
        const color = ch.color==='green'?'green':(ch.color==='orange'?'orange':'red');
        const row = document.createElement('div');
        row.className = 'check';
        row.innerHTML = `
          <div class="left">
            <span class="dot ${color}"></span>
            <div class="text text-slate-100">${ch.label}</div>
          </div>
          <div class="flex items-center gap-2">
            <span class="score-pill">${ch.score ?? '—'}</span>
            <button class="improve-btn">Improve</button>
          </div>
        `;
        row.querySelector('.improve-btn').addEventListener('click', ()=>{
          mTitle.textContent = ch.label;
          mAdvice.textContent = ch.advice || 'Suggested improvements.';
          mLink.href = ch.improve_search_url || 'https://www.google.com';
          modal.showModal();
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
</script>
@endpush
@endsection
