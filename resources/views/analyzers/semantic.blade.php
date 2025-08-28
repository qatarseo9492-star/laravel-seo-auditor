@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@push('head')
<style>
  .card { @apply glass rounded-2xl p-5; }
  .pill  { @apply px-2 py-0.5 rounded-full text-xs font-medium; }
  .k-badge{ @apply px-2 py-1 rounded-lg text-xs font-semibold; }
  .score-wheel { width:160px; height:160px; border-radius:50%; display:grid; place-items:center; }
  .score-ring {
    --v: 0; /* 0..100 */
    background:
      conic-gradient(
        #1ef5a4 calc(var(--v)*1%), 
        #ffcf5a calc(var(--v)*1% + 0.0001%), 
        #ff6b6b 100%
      );
    mask: radial-gradient(circle 64px, transparent 62%, #000 63%);
  }
  .shadow-soft { box-shadow: 0 12px 40px rgba(0,0,0,.25); }
  .bar { @apply h-3 rounded-full bg-white/10 overflow-hidden; }
  .bar > span { @apply h-full block rounded-full transition-all; background: linear-gradient(90deg,#07f3b0,#60a5fa,#d946ef); }
  .chip { @apply text-xs px-2 py-0.5 rounded-md bg-white/10 border border-white/10; }

  /* Water loading bar */
  #waterbar { height: 12px; border-radius: 9999px; background: rgba(255,255,255,.08); overflow: hidden; }
  #waterbar span {
    display:block; height:100%; width:0%;
    background: linear-gradient(90deg,#06d6a0,#60a5fa,#d946ef);
    transition: width .8s ease, filter .3s ease;
    filter: drop-shadow(0 0 10px rgba(148,163,184,.4));
  }

  /* glow by color */
  .g-green  { box-shadow: 0 0 0 1px rgba(16,185,129,.4) inset, 0 0 25px rgba(16,185,129,.35); }
  .g-orange { box-shadow: 0 0 0 1px rgba(245,158,11,.35) inset, 0 0 25px rgba(245,158,11,.30); }
  .g-red    { box-shadow: 0 0 0 1px rgba(239,68,68,.35) inset, 0 0 25px rgba(239,68,68,.30); }
</style>
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 py-10 space-y-8">

  <div class="flex flex-col gap-4">
    <div class="chip w-max">Analyzer 2.0</div>
    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Semantic SEO Master</h1>

    <form id="semanticForm" class="w-full grid lg:grid-cols-[1fr,320px] gap-3">
      <div class="glass rounded-xl p-1.5 flex items-center">
        <input name="url" type="url" required placeholder="Paste a URL (e.g. https://example.com/page)"
               class="w-full bg-transparent px-3 py-2 outline-none text-slate-100 placeholder:text-slate-400">
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)"
               class="w-72 max-w-[50%] bg-transparent px-3 py-2 outline-none text-slate-100 placeholder:text-slate-400 hidden md:block">
      </div>
      <button class="rounded-xl shadow-soft bg-gradient-to-r from-fuchsia-500 via-indigo-500 to-sky-500 hover:opacity-95 px-5 text-white font-semibold">
        Analyze URL
      </button>
    </form>

    <!-- water bar -->
    <div id="waterbar" class="shadow-soft"><span style="width:0%"></span></div>
    <p class="text-xs text-slate-300">Tip: we fetch the page server-side and compute a detailed semantic report.</p>
  </div>

  <!-- Results -->
  <div id="resultWrap" class="space-y-8 hidden">

    <!-- top row: wheel + quick stats -->
    <div class="grid lg:grid-cols-3 gap-6">
      <div class="card flex items-center gap-5">
        <div class="score-wheel score-ring shadow-soft" id="wheel" style="--v:0">
          <div class="text-center">
            <div id="scoreNum" class="text-4xl font-extrabold">0</div>
            <div class="text-xs text-slate-300">Overall</div>
          </div>
        </div>
        <div class="space-y-2">
          <div id="badge" class="k-badge bg-white/10 border border-white/10 text-slate-100">—</div>
          <div class="text-xs text-slate-300">Great ≥80 • Needs work 60–79 • Red <60</div>
        </div>
      </div>

      <div class="card lg:col-span-2">
        <h3 class="font-semibold mb-3">Quick Stats</h3>
        <div class="grid sm:grid-cols-3 gap-4 text-sm">
          <div class="glass rounded-xl p-4 border border-white/10">
            <div class="text-slate-300 text-xs">Readability (Flesch)</div>
            <div id="statFlesch" class="text-2xl font-bold">—</div>
            <div id="statGrade" class="text-xs text-slate-400">—</div>
          </div>
          <div class="glass rounded-xl p-4 border border-white/10">
            <div class="text-slate-300 text-xs">Links (int / ext)</div>
            <div class="text-2xl font-bold"><span id="statInt">0</span> / <span id="statExt">0</span></div>
          </div>
          <div class="glass rounded-xl p-4 border border-white/10">
            <div class="text-slate-300 text-xs">Text/HTML Ratio</div>
            <div id="statRatio" class="text-2xl font-bold">—</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Readability -->
    <div class="card">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Readability</h3>
        <span id="readBadge" class="pill bg-white/10 border border-white/10">—</span>
      </div>
      <div class="grid md:grid-cols-2 gap-6 mt-4">
        <div class="flex items-center gap-6">
          <div class="score-wheel score-ring shadow-soft" id="readWheel" style="--v:0">
            <div class="text-center">
              <div id="readNum" class="text-3xl font-extrabold">0</div>
              <div class="text-xs text-slate-300">Flesch</div>
            </div>
          </div>
          <div class="flex-1 space-y-3">
            <div>
              <div class="text-xs text-slate-300 mb-1">Overall</div>
              <div class="bar"><span id="readBar" style="width:0%"></span></div>
            </div>
            <div class="text-xs text-slate-300">Grade level: <span id="gradeVal">—</span> (approx. school grade needed to understand)</div>
          </div>
        </div>
        <div class="glass rounded-xl p-4 border border-white/10">
          <div class="text-xs text-slate-300">How to improve</div>
          <ul class="mt-2 list-disc pl-5 text-sm text-slate-200 space-y-1">
            <li>Use shorter sentences and active voice.</li>
            <li>Prefer common words over jargon; define terms.</li>
            <li>Break up long paragraphs with sub-headings and bullets.</li>
            <li>Add images or examples to clarify complex ideas.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Recommendations -->
    <div class="card">
      <h3 class="font-semibold mb-3">Recommendations</h3>
      <div id="recs" class="grid md:grid-cols-2 gap-3"></div>
    </div>

    <!-- Content Structure -->
    <div class="card">
      <h3 class="font-semibold">Content Structure</h3>
      <div class="grid md:grid-cols-2 gap-6 mt-4">
        <div class="glass rounded-xl p-4 border border-white/10">
          <div class="text-xs text-slate-300">Title</div>
          <div id="titleVal" class="font-semibold text-slate-100">—</div>
          <div class="text-xs text-slate-300 mt-3">Meta Description</div>
          <div id="metaVal" class="text-slate-200">—</div>
        </div>
        <div class="glass rounded-xl p-4 border border-white/10">
          <div class="text-xs text-slate-300 mb-2">Heading Map</div>
          <div id="headingMap" class="text-sm space-y-2"></div>
        </div>
      </div>
    </div>

    <!-- Checklists -->
    <div class="space-y-4">
      <h3 class="text-xl font-bold">Semantic SEO Ground</h3>
      <div id="cats" class="grid lg:grid-cols-2 gap-6"></div>
    </div>
  </div>

  <!-- Improve Modal -->
  <dialog id="improveModal" class="backdrop:bg-black/60 rounded-2xl p-0 w-[min(560px,95vw)]">
    <div class="glass rounded-2xl p-5 border border-white/10">
      <div class="flex items-start justify-between">
        <h4 id="improveTitle" class="font-semibold">Improve</h4>
        <form method="dialog"><button class="pill bg-white/10">Close</button></form>
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

function colorBy(score){ return score>=80?'g-green':(score>=60?'g-orange':'g-red'); }
function labelBy(score){ return score>=80?'Great Work — Well Optimized':(score>=60?'Needs Optimization':'Needs Significant Optimization'); }

f.addEventListener('submit', async (e)=>{
  e.preventDefault();
  // water bar animates 0 -> 100 while loading
  water.style.width = '0%';
  setTimeout(()=>water.style.width='100%', 30);

  const fd = new FormData(f);
  const payload = {
    url: fd.get('url'),
    target_keyword: fd.get('target_keyword') || ''
  };

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
    badge.className = 'k-badge ' + (score>=80?'bg-emerald-500/20 text-emerald-200 border-emerald-500/30':
                                     score>=60?'bg-amber-500/20 text-amber-200 border-amber-500/30':
                                               'bg-rose-500/20 text-rose-200 border-rose-500/30');

    // Quick stats
    statF.textContent = data.quick_stats.readability_flesch;
    statG.textContent = 'Grade ' + data.quick_stats.readability_grade;
    statInt.textContent = data.quick_stats.internal_links;
    statExt.textContent = data.quick_stats.external_links;
    statRatio.textContent = data.quick_stats.text_to_html_ratio + '%';

    // Readability
    const rf = data.quick_stats.readability_flesch;
    readWheel.style.setProperty('--v', rf);
    readNum.textContent = rf;
    readBar.style.width = rf + '%';
    gradeVal.textContent = 'Grade ' + data.quick_stats.readability_grade;
    readBadge.textContent = labelBy(rf);
    readBadge.className = 'pill ' + (rf>=80?'bg-emerald-500/20 text-emerald-200':
                                           rf>=60?'bg-amber-500/20 text-amber-200':
                                                   'bg-rose-500/20 text-rose-200');

    // Structure
    titleVal.textContent = data.content_structure.title || '—';
    metaVal.textContent  = data.content_structure.meta_description || '—';
    headingMap.innerHTML='';
    Object.entries(data.content_structure.headings||{}).forEach(([lvl,arr])=>{
      if(!arr || !arr.length) return;
      const box = document.createElement('div');
      box.className='glass rounded-lg p-3 border border-white/10';
      box.innerHTML = `<div class="text-xs text-slate-300 mb-1 uppercase">${lvl}</div>` +
                      arr.map(t=>`<div>• ${t}</div>`).join('');
      headingMap.appendChild(box);
    });

    // Recommendations
    recsEl.innerHTML='';
    (data.recommendations||[]).forEach(r=>{
      const c = document.createElement('div');
      const tone = r.severity==='Critical' ? 'bg-rose-500/15 text-rose-200 border-rose-500/30'
                 : r.severity==='Warning' ? 'bg-amber-500/15 text-amber-200 border-amber-500/30'
                 : 'bg-slate-500/15 text-slate-200 border-white/10';
      c.className='glass rounded-xl p-3 border ' + tone;
      c.innerHTML = `<span class="pill ${tone} mr-2">${r.severity}</span>${r.text}`;
      recsEl.appendChild(c);
    });

    // Categories & checks
    catsEl.innerHTML='';
    (data.categories||[]).forEach(cat=>{
      const card = document.createElement('div');
      const tone = colorBy(cat.score??60);
      card.className = 'card ' + tone;
      card.innerHTML = `
        <div class="flex items-center justify-between mb-3">
          <div class="font-semibold">${cat.name}</div>
          <div class="pill bg-white/10 border border-white/10">${cat.score ?? '—'}</div>
        </div>
        <div class="space-y-2"></div>`;
      const list = card.lastElementChild;
      (cat.checks||[]).forEach(ch=>{
        const li = document.createElement('div');
        li.className = 'glass rounded-lg px-3 py-2 border border-white/10 flex items-center justify-between';
        li.innerHTML = `
          <div class="flex items-center gap-3">
            <span class="w-2.5 h-2.5 rounded-full ${ch.color==='green'?'bg-emerald-400':ch.color==='orange'?'bg-amber-400':'bg-rose-400'}"></span>
            <div class="text-sm">${ch.label}</div>
          </div>
          <div class="flex items-center gap-2">
            <span class="pill bg-white/10">${ch.score ?? '—'}</span>
            <button class="px-3 py-1 rounded-lg text-xs bg-gradient-to-r from-fuchsia-500 to-sky-500">Improve</button>
          </div>`;
        li.querySelector('button').addEventListener('click', ()=>{
          mTitle.textContent = ch.label;
          mAdvice.textContent = ch.advice || 'Suggested improvements';
          mLink.href = ch.improve_search_url || 'https://www.google.com/';
          modal.showModal();
        });
        list.appendChild(li);
      });
      catsEl.appendChild(card);
    });

    // end: snap water bar to actual score
    setTimeout(()=>{ water.style.width = (score+'%'); }, 150);
  } catch(err){
    alert('Fetch error. Check routes/api.php and storage/logs/laravel.log');
    water.style.width='0%';
  }
});
</script>
@endpush
@endsection
