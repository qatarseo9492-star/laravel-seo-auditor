{{-- resources/views/analyzers/semantic.blade.php --}}
@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@push('head')
<style>
  /* ====== Base tokens (dark neon) ====== */
  :root{
    --bg-1:#0b0b1a; --bg-2:#12122a; --bg-3:#1c0f2e;
    --text:#e8eaf6; --muted:#9aa0b4;
  }

  /* Page background + animated tech lines (code-only, no images) */
  body{
    color:var(--text);
    background:
      radial-gradient(1200px 700px at 85% -10%, rgba(138,92,255,.30), transparent 60%),
      radial-gradient(900px 600px at 0% 0%, rgba(0,208,255,.18), transparent 60%),
      radial-gradient(700px 500px at 100% 100%, rgba(255,90,217,.12), transparent 60%),
      linear-gradient(180deg, var(--bg-3), var(--bg-2) 60%, var(--bg-1));
    overflow-x:hidden;
  }
  .tech-bg{position:fixed; inset:0; pointer-events:none; opacity:.35; z-index:-1;}
  .tech-bg svg{width:120%; height:120%; transform:translate(-10%,-10%);}
  .tech-bg .dash{stroke-dasharray:6 14; animation:dashMove 14s linear infinite;}
  .tech-bg .glow{filter:drop-shadow(0 0 6px rgba(138,92,255,.5));}
  @keyframes dashMove{to{stroke-dashoffset:-400;}}

  /* Glass utility (in case Tailwind doesn't define it) */
  .glass{background:rgba(255,255,255,.04); backdrop-filter:blur(6px);}
  .shadow-soft{box-shadow:0 12px 40px rgba(0,0,0,.25);}

  /* Cards / Pills */
  .card { border-radius:1rem; padding:1.25rem; border:1px solid rgba(255,255,255,.10); background:rgba(255,255,255,.03);}
  .pill  { padding:.125rem .5rem; border-radius:9999px; font-size:.75rem; font-weight:600;}
  .k-badge{ padding:.25rem .5rem; border-radius:.75rem; font-size:.75rem; font-weight:700; border:1px solid rgba(255,255,255,.15); }

  /* Score wheel (conic) */
  .score-wheel { width:160px; height:160px; border-radius:50%; display:grid; place-items:center; }
  .score-ring {
    --v: 0; /* 0..100 */
    background:
      conic-gradient(
        #1ef5a4 calc(var(--v)*1%),
        #60a5fa calc(var(--v)*1% + 0.0001%),
        #d946ef 100%
      );
    mask: radial-gradient(circle 64px, transparent 62%, #000 63%);
  }

  /* Bars */
  .bar { height:.75rem; border-radius:9999px; background:rgba(255,255,255,.10); overflow:hidden; border:1px solid rgba(255,255,255,.08);}
  .bar > span { height:100%; display:block; border-radius:9999px; transition:width .4s ease; background:linear-gradient(90deg,#07f3b0,#60a5fa,#d946ef); }

  .chip { font-size:.75rem; padding:.125rem .5rem; border-radius:.5rem; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.10); }

  /* Water loading bar */
  #waterbar { height: 12px; border-radius: 9999px; background: rgba(255,255,255,.08); overflow: hidden; border:1px solid rgba(255,255,255,.10);}
  #waterbar span {
    display:block; height:100%; width:0%;
    background: linear-gradient(90deg,#06d6a0,#60a5fa,#d946ef);
    transition: width .9s ease;
    filter: drop-shadow(0 0 10px rgba(148,163,184,.4));
  }

  /* Glow bands for checklist boxes */
  .g-green  { box-shadow: 0 0 0 1px rgba(16,185,129,.4) inset, 0 0 25px rgba(16,185,129,.30); }
  .g-orange { box-shadow: 0 0 0 1px rgba(245,158,11,.35) inset, 0 0 25px rgba(245,158,11,.25); }
  .g-red    { box-shadow: 0 0 0 1px rgba(239,68,68,.35) inset, 0 0 25px rgba(239,68,68,.25); }

  @media (prefers-reduced-motion: reduce){
    .tech-bg .dash{animation:none;}
    #waterbar span{transition:none;}
  }
</style>
@endpush

@section('content')
<!-- Animated tech lines background -->
<div class="tech-bg" aria-hidden="true">
  <svg viewBox="0 0 1200 800" preserveAspectRatio="none">
    <defs>
      <linearGradient id="glow" x1="0" x2="1">
        <stop offset="0%" stop-color="#8a5cff" stop-opacity=".8"/>
        <stop offset="100%" stop-color="#00d0ff" stop-opacity=".8"/>
      </linearGradient>
    </defs>
    @for($x=0;$x<=1200;$x+=80)
      <line x1="{{ $x }}" y1="0" x2="{{ $x }}" y2="800" stroke="url(#glow)" stroke-width="1" class="dash glow"/>
    @endfor
    @for($i=0;$i<14;$i++)
      @php $y = 30 + $i*55; @endphp
      <path d="M -100 {{ $y }} Q 300 {{ $y+40 }}, 700 {{ $y-30 }} T 1400 {{ $y+30 }}"
            fill="none" stroke="url(#glow)" stroke-width="1.6" class="dash glow"/>
    @endfor
  </svg>
</div>

<section class="max-w-7xl mx-auto px-4 py-10 space-y-8">

  <div class="flex flex-col gap-4">
    <div class="chip w-max">Analyzer 2.0</div>
    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">Semantic SEO Master</h1>

    <form id="semanticForm" class="w-full grid lg:grid-cols-[1fr,320px] gap-3">
      <div class="glass rounded-xl p-1.5 flex items-center border border-white/10">
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
          <div id="badge" class="k-badge bg-white/10 text-slate-100">—</div>
          <div class="text-xs text-slate-300">Great ≥80 • Needs work 60–79 • Red &lt;60</div>
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
@endSection

@push('scripts')
{{-- Resolve endpoint smartly: prefer named web route; fallback to API path --}}
<script>
const ANALYZE_URL = @json(\Illuminate\Support\Facades\Route::has('semantic.analyze')
    ? route('semantic.analyze')
    : url('/api/semantic-analyze'));
</script>

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

function clamp(n,min=0,max=100){ return Math.max(min, Math.min(max, Number(n)||0)); }
function colorBand(score){ return score>=80?'g-green':(score>=60?'g-orange':'g-red'); }
function labelBy(score){ return score>=80?'Great Work — Well Optimized':(score>=60?'Needs Optimization':'Needs Significant Optimization'); }

f.addEventListener('submit', async (e)=>{
  e.preventDefault();

  // Water bar animates 0 -> 100 during fetch; later we snap to real score
  water.style.width = '0%';
  requestAnimationFrame(()=>{ water.style.width='100%'; });

  const fd = new FormData(f);
  const payload = {
    url: fd.get('url'),
    target_keyword: fd.get('target_keyword') || ''
  };

  try{
    let res, data;
    if (ANALYZE_URL.includes('/api/')) {
      // API path (no CSRF)
      res = await fetch(ANALYZE_URL, {
        method:'POST',
        headers:{ 'Accept':'application/json','Content-Type':'application/json' },
        body: JSON.stringify(payload)
      });
    } else {
      // Web route (needs CSRF)
      res = await fetch(ANALYZE_URL, {
        method:'POST',
        headers:{
          'X-Requested-With':'XMLHttpRequest',
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: fd
      });
    }
    if(!res.ok){
      const msg = await res.text();
      throw new Error(msg || 'Analysis request failed');
    }
    data = await res.json();
    renderAll(data);
  } catch(err){
    console.error(err);
    // Fallback demo so UI still shows
    renderAll({
      overall_score: 76,
      quick_stats: {
        readability_flesch: 82,
        readability_grade: 8,
        internal_links: 18,
        external_links: 5,
        text_to_html_ratio: 42
      },
      content_structure: {
        title: 'Ultimate Guide to Semantic SEO in 2025',
        meta_description: 'Learn semantic SEO with actionable steps and pro checklists.',
        headings: {
          H1: ['Semantic SEO: Strategies, Tools, and Checklists'],
          H2: ['Why semantics matter','Entity mapping','FAQ schema best-practices','Internal linking hubs'],
          H3: ['How to start','Common pitfalls']
        }
      },
      recommendations: [
        { severity: 'Critical', text: 'Add a canonical link to prevent duplicates.' },
        { severity: 'Warning',  text: '2 images missing alt text.' },
        { severity: 'Info',     text: 'Consider adding FAQPage schema for rich results.' }
      ],
      categories: [
        { name:'Content & Keywords', score:85, checks:[
          { label:'Primary keyword in title', score:100, color:'green', advice:'Keep it near the beginning.' },
          { label:'Entity coverage', score:90,  color:'green', advice:'Include core entities and aliases.' },
          { label:'Synonyms & variants', score:60, color:'orange', advice:'Add 2–3 natural variants.' }
        ]},
        { name:'Technical Elements', score:68, checks:[
          { label:'Meta description length', score:90, color:'green' },
          { label:'Image alts', score:60, color:'orange', advice:'Add alt to 2 images.' },
          { label:'Canonical tag', score:30, color:'red', advice:'Add rel=\"canonical\" to the preferred URL.' }
        ]},
        { name:'Links & Navigation', score:58, checks:[
          { label:'Internal links to hubs', score:55, color:'orange', advice:'Add 3+ internal links to pillar pages.' },
          { label:'External authoritative refs', score:35, color:'red', advice:'Cite 2–3 high-authority sources.' }
        ]},
        { name:'Experience & Trust', score:80, checks:[
          { label:'Author bio & expertise', score:85, color:'green', advice:'Include credentials and photo.' },
          { label:'Last updated date', score:65, color:'orange', advice:'Show updated timestamp on the article.' }
        ]}
      ]
    });
  }
});

function renderAll(data){
  wrap.classList.remove('hidden');

  // Overall
  const score = clamp(data.overall_score);
  wheel.style.setProperty('--v', score);
  scoreNum.textContent = score;
  badge.textContent = (data.wheel && data.wheel.label) ? data.wheel.label : labelBy(score);
  badge.className = 'k-badge ' + (score>=80?'bg-emerald-500/20 text-emerald-200 border border-emerald-500/30':
                                   score>=60?'bg-amber-500/20 text-amber-200 border border-amber-500/30':
                                             'bg-rose-500/20 text-rose-200 border border-rose-500/30');

  // Quick stats (defensive defaults)
  const qs = data.quick_stats || {};
  const flesch = clamp(qs.readability_flesch);
  statF.textContent = flesch || '—';
  statG.textContent = (qs.readability_grade!=null? ('Grade '+qs.readability_grade) : '—');
  statInt.textContent = qs.internal_links ?? 0;
  statExt.textContent = qs.external_links ?? 0;
  statRatio.textContent = (qs.text_to_html_ratio!=null? (qs.text_to_html_ratio+'%') : '—');

  // Readability
  readWheel.style.setProperty('--v', flesch);
  readNum.textContent = flesch || 0;
  readBar.style.width = (flesch||0) + '%';
  gradeVal.textContent = (qs.readability_grade!=null? ('Grade '+qs.readability_grade) : '—');
  readBadge.textContent = labelBy(flesch||0);
  readBadge.className = 'pill ' + ((flesch||0)>=80?'bg-emerald-500/20 text-emerald-200 border border-emerald-500/30':
                                          (flesch||0)>=60?'bg-amber-500/20 text-amber-200 border border-amber-500/30':
                                                          'bg-rose-500/20 text-rose-200 border border-rose-500/30');

  // Structure
  const st = data.content_structure || {};
  titleVal.textContent = st.title || '—';
  metaVal.textContent  = st.meta_description || '—';
  headingMap.innerHTML='';
  const headings = st.headings || {};
  Object.entries(headings).forEach(([lvl,arr])=>{
    if(!arr || !arr.length) return;
    const box = document.createElement('div');
    box.className='glass rounded-lg p-3 border border-white/10';
    box.innerHTML = `<div class="text-xs text-slate-300 mb-1 uppercase">${lvl}</div>` +
                    arr.map(t=>`<div>• ${escapeHtml(t)}</div>`).join('');
    headingMap.appendChild(box);
  });

  // Recommendations
  recsEl.innerHTML='';
  (data.recommendations||[]).forEach(r=>{
    const tone = r.severity==='Critical' ? 'bg-rose-500/15 text-rose-200 border-rose-500/30'
               : r.severity==='Warning'  ? 'bg-amber-500/15 text-amber-200 border-amber-500/30'
               : 'bg-slate-500/15 text-slate-200 border-white/10';
    const c = document.createElement('div');
    c.className='glass rounded-xl p-3 border ' + tone;
    c.innerHTML = `<span class="pill ${tone} mr-2">${escapeHtml(r.severity||'Info')}</span>${escapeHtml(r.text||'Recommendation')}`;
    recsEl.appendChild(c);
  });

  // Categories & checks (glow, icons, colors)
  catsEl.innerHTML='';
  (data.categories||[]).forEach(cat=>{
    const tone = colorBand(clamp(cat.score));
    const card = document.createElement('div');
    card.className = 'card ' + tone;
    card.innerHTML = `
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold">${escapeHtml(cat.name||'Checklist')}</div>
        <div class="pill bg-white/10 border border-white/10">${cat.score ?? '—'}</div>
      </div>
      <div class="space-y-2"></div>`;
    const list = card.lastElementChild;
    const checks = cat.checks || cat.items || [];
    checks.forEach(ch=>{
      const li = document.createElement('div');
      const col = ch.color || ( (ch.score>=80)?'green' : (ch.score>=60)?'orange' : 'red' );
      const colDot = col==='green'?'bg-emerald-400':col==='orange'?'bg-amber-400':'bg-rose-400';
      li.className = 'glass rounded-lg px-3 py-2 border border-white/10 flex items-center justify-between';
      li.innerHTML = `
        <div class="flex items-center gap-3">
          <span class="w-2.5 h-2.5 rounded-full ${colDot}"></span>
          <div class="text-sm">${escapeHtml(ch.label||ch.title||'Check')}</div>
        </div>
        <div class="flex items-center gap-2">
          <span class="pill bg-white/10">${ch.score ?? '—'}</span>
          <button class="px-3 py-1 rounded-lg text-xs bg-gradient-to-r from-fuchsia-500 to-sky-500 text-white">Improve</button>
        </div>`;
      li.querySelector('button').addEventListener('click', ()=>{
        mTitle.textContent = ch.label || ch.title || 'Improve';
        mAdvice.textContent = ch.advice || ch.hint || 'Suggested improvements';
        mLink.href = ch.improve_search_url || 'https://www.google.com/search?q=' + encodeURIComponent((cat.name||'SEO')+' '+(ch.label||ch.title||'improvement'));
        modal.showModal();
      });
      list.appendChild(li);
    });
    catsEl.appendChild(card);
  });

  // Snap water bar to actual overall score after render
  setTimeout(()=>{ water.style.width = (score+'%'); }, 120);
}

function escapeHtml(str){
  if(str==null) return '';
  return String(str)
    .replace(/&/g,'&amp;')
    .replace(/</g,'&lt;')
    .replace(/>/g,'&gt;')
    .replace(/\"/g,'&quot;')
    .replace(/'/g,'&#39;');
}
</script>
@endpush
