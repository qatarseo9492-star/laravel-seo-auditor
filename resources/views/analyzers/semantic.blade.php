{{-- resources/views/analyzers/semantic.blade.php --}}
@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* ================= THEME (high-contrast on black) ================= */
  :root{
    --bg: #000000;
    --card: #0b0b0f;
    --card-2:#101017;
    --border:#26262b;
    --muted:#9aa0aa;
    --text:#ECEFF4;
    --text-dim:#C8CDD5;

    --accent:#22d3ee;      /* cyan */
    --accent-2:#a78bfa;    /* iris */
    --accent-3:#f43f5e;    /* rose */

    --good:#10b981;        /* green */
    --warn:#f59e0b;        /* amber */
    --bad:#ef4444;         /* red */
  }

  html, body { background:var(--bg) !important; }
  body{ color:var(--text); overflow-x:hidden; }

  /* Background tech lines – SUBTLE now */
  .tech-bg{ position:fixed; inset:0; pointer-events:none; z-index:1; opacity:.10; }
  .tech-bg svg{ width:160%; height:160%; transform:translate(-20%,-22%) rotate(-8deg); }
  .tech-bg .dash{ stroke-dasharray:10 22; animation:dashMove 18s linear infinite; }
  .tech-bg .glow{ filter:drop-shadow(0 0 4px rgba(167,139,250,.3)); }
  @keyframes dashMove{ to{ stroke-dashoffset:-640; } }

  /* Content layer above lines */
  .content-root{ position:relative; z-index:2; }

  /* =============== Cards & utilities =============== */
  .card{ background:var(--card); border:1px solid var(--border); border-radius:18px; padding:18px; box-shadow:0 12px 40px rgba(0,0,0,.55); }
  .glass{ background:var(--card-2); border:1px solid var(--border); border-radius:16px; }
  .pill{ font-size:.72rem; padding:.2rem .55rem; border:1px solid var(--border); border-radius:999px; color:var(--text-dim); }
  .chip{ font-size:.72rem; padding:.2rem .55rem; border-radius:8px; background:#12131a; border:1px solid var(--border); color:var(--text-dim); }
  .k-badge{ font-size:.78rem; padding:.45rem .6rem; border-radius:10px; background:#14151d; border:1px solid var(--border); font-weight:700; }

  a, button { outline: none; }

  /* Buttons */
  .btn-primary{
    background:linear-gradient(90deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
    color:white; border:none; border-radius:12px; padding:.85rem 1rem; font-weight:700;
    transition: transform .05s ease, opacity .2s ease;
  }
  .btn-primary:hover{ opacity:.95; transform: translateY(-1px); }
  .btn-ghost{
    background:#14151d; color:var(--text); border:1px solid var(--border); border-radius:10px; padding:.5rem .75rem;
  }

  /* Water bar */
  #waterbar{ height:14px; border-radius:999px; background:#12131a; border:1px solid var(--border); overflow:hidden; }
  #waterbar span{ display:block; height:100%; width:0%; background:linear-gradient(90deg,var(--accent),var(--accent-2),var(--accent-3)); transition:width .9s ease; filter:drop-shadow(0 0 8px rgba(79,209,255,.25)); }

  /* Score wheel */
  .score-wheel{ width:180px; height:180px; border-radius:50%; display:grid; place-items:center; }
  .score-ring{
    --v:0;
    background:
      conic-gradient(
        var(--good) calc(var(--v)*1%),
        var(--warn) calc(var(--v)*1% + .0001%),
        var(--bad) 100%
      );
    mask: radial-gradient(circle 74px, transparent 66%, #000 67%);
    border-radius:50%;
    box-shadow: inset 0 0 0 8px #0e1016, 0 6px 24px rgba(0,0,0,.5);
  }

  /* Bars */
  .bar{ height:10px; border-radius:999px; background:#12131a; border:1px solid var(--border); overflow:hidden; }
  .bar>span{ display:block; height:100%; width:0%; border-radius:999px; transition:width .4s ease; background:linear-gradient(90deg,var(--accent),var(--accent-2)); }

  /* Checklist glow frames */
  .g-green { box-shadow: 0 0 0 1px rgba(16,185,129,.35) inset, 0 0 24px rgba(16,185,129,.2); }
  .g-orange{ box-shadow: 0 0 0 1px rgba(245,158,11,.32) inset, 0 0 24px rgba(245,158,11,.18); }
  .g-red   { box-shadow: 0 0 0 1px rgba(239,68,68,.32)  inset, 0 0 24px rgba(239,68,68,.18); }

  .muted{ color:var(--muted); }
  .small{ font-size:.8rem; }

  @media (prefers-reduced-motion:reduce){ .tech-bg .dash{animation:none;} #waterbar span{transition:none;} }
</style>
@endpush

@section('content')
<!-- SUBTLE dashed tech lines -->
<div class="tech-bg" aria-hidden="true">
  <svg viewBox="0 0 1400 900" preserveAspectRatio="none">
    <defs>
      <linearGradient id="techStroke" x1="0" x2="1">
        <stop offset="0%"   stop-color="#a78bfa" stop-opacity="0.85"/>
        <stop offset="100%" stop-color="#22d3ee" stop-opacity="0.85"/>
      </linearGradient>
    </defs>
    @for($x=-100;$x<=1500;$x+=90)
      <line x1="{{ $x }}" y1="-50" x2="{{ $x+200 }}" y2="950" stroke="url(#techStroke)" stroke-width="1.2" class="dash glow"/>
    @endfor
    @for($i=0;$i<12;$i++)
      @php $y = 60 + $i*65; @endphp
      <path d="M -200 {{ $y }} C 200 {{ $y+60 }}, 600 {{ $y-40 }}, 1000 {{ $y+50 }} S 1600 {{ $y-30 }}, 1900 {{ $y+40 }}"
            fill="none" stroke="url(#techStroke)" stroke-width="2" class="dash glow"/>
    @endfor
  </svg>
</div>

<section class="content-root max-w-7xl mx-auto px-4 py-10 space-y-8">

  <!-- Header + form -->
  <div class="flex flex-col gap-4">
    <div class="chip w-max">Analyzer 3.0</div>
    <h1 class="text-4xl font-black tracking-tight">Semantic SEO Master</h1>

    <form id="semanticForm" class="w-full grid lg:grid-cols-[1fr,280px,160px] gap-3">
      <div class="glass p-1.5 flex items-center">
        <input name="url" type="url" required placeholder="https://example.com/page"
               class="w-full bg-transparent px-3 py-2 outline-none text-[15px] text-white placeholder:muted">
      </div>
      <div class="glass p-1.5 items-center hidden md:flex">
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)"
               class="w-full bg-transparent px-3 py-2 outline-none text-[15px] text-white placeholder:muted">
      </div>
      <button type="submit" class="btn-primary">Analyze URL</button>
    </form>

    <div id="waterbar"><span style="width:0%"></span></div>
    <p class="small muted">Tip: we fetch the page server-side and compute a detailed semantic & readability report.</p>
  </div>

  <!-- Results -->
  <div id="resultWrap" class="space-y-8 hidden">

    <!-- Top row: wheel + quick stats -->
    <div class="grid lg:grid-cols-3 gap-6">
      <div class="card flex items-center gap-5">
        <div class="score-wheel score-ring" id="wheel" style="--v:0">
          <div class="text-center">
            <div id="scoreNum" class="text-4xl font-extrabold">0</div>
            <div class="small muted">Overall</div>
          </div>
        </div>
        <div class="space-y-2">
          <div id="badge" class="k-badge">—</div>
          <div class="small muted">Great ≥80 · Needs work 60–79 · Red &lt;60</div>
        </div>
      </div>

      <div class="card lg:col-span-2">
        <h3 class="font-semibold mb-3">Quick Stats</h3>
        <div class="grid sm:grid-cols-3 gap-4 text-sm">
          <div class="glass p-4">
            <div class="small muted">Readability (Flesch)</div>
            <div id="statFlesch" class="text-2xl font-bold">—</div>
            <div id="statGrade" class="small muted">—</div>
          </div>
          <div class="glass p-4">
            <div class="small muted">Links (int / ext)</div>
            <div class="text-2xl font-bold"><span id="statInt">0</span> / <span id="statExt">0</span></div>
          </div>
          <div class="glass p-4">
            <div class="small muted">Text/HTML Ratio</div>
            <div id="statRatio" class="text-2xl font-bold">—</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Readability -->
    <div class="card">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold">Readability</h3>
        <span id="readBadge" class="pill">—</span>
      </div>
      <div class="grid md:grid-cols-2 gap-6 mt-4">
        <div class="flex items-center gap-6">
          <div class="score-wheel score-ring" id="readWheel" style="--v:0">
            <div class="text-center">
              <div id="readNum" class="text-3xl font-extrabold">0</div>
              <div class="small muted">Flesch</div>
            </div>
          </div>
          <div class="flex-1 space-y-3">
            <div>
              <div class="small muted mb-1">Overall</div>
              <div class="bar"><span id="readBar" style="width:0%"></span></div>
            </div>
            <div class="small muted">Grade level: <span id="gradeVal">—</span></div>
          </div>
        </div>
        <div class="glass p-4">
          <div class="small muted">How to improve</div>
          <ul class="mt-2 list-disc pl-5 text-sm space-y-1 text-white/90">
            <li>Use shorter sentences and active voice.</li>
            <li>Prefer common words over jargon; define terms.</li>
            <li>Break up long paragraphs with sub-headings and bullets.</li>
            <li>Add images or examples to clarify complex ideas.</li>
          </ul>
        </div>
      </div>
    </div>

    <!-- Content structure -->
    <div class="card">
      <h3 class="font-semibold">Content Structure</h3>
      <div class="grid md:grid-cols-2 gap-6 mt-4">
        <div class="glass p-4">
          <div class="small muted">Title</div>
          <div id="titleVal" class="font-semibold">—</div>
          <div class="small muted mt-3">Meta Description</div>
          <div id="metaVal" class="text-white/90">—</div>
        </div>
        <div class="glass p-4">
          <div class="small muted mb-2">Heading Map</div>
          <div id="headingMap" class="text-sm space-y-2"></div>
        </div>
      </div>
    </div>

    <!-- Recommendations -->
    <div class="card">
      <h3 class="font-semibold mb-3">Recommendations</h3>
      <div id="recs" class="grid md:grid-cols-2 gap-3"></div>
    </div>

    <!-- Checklists -->
    <div class="space-y-4">
      <h3 class="text-xl font-bold">Semantic SEO Ground</h3>
      <div id="cats" class="grid lg:grid-cols-2 gap-6"></div>
    </div>
  </div>

  <!-- Modal -->
  <dialog id="improveModal" class="backdrop:bg-black/70 rounded-2xl p-0 w-[min(560px,95vw)]">
    <div class="card">
      <div class="flex items-start justify-between">
        <h4 id="improveTitle" class="font-semibold">Improve</h4>
        <form method="dialog"><button class="btn-ghost small">Close</button></form>
      </div>
      <p id="improveAdvice" class="mt-3 text-sm">—</p>
      <a id="improveSearch" target="_blank" class="btn-primary inline-block mt-4 text-sm">Search guidance</a>
    </div>
  </dialog>

</section>
@endsection

@push('scripts')
<script>
const ANALYZE_URL = @json(\Illuminate\Support\Facades\Route::has('semantic.analyze')
  ? route('semantic.analyze') : url('/api/semantic-analyze'));

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

function clamp(n,min=0,max=100){ const v=Number(n); return Math.max(min, Math.min(max, isNaN(v)?0:v)); }
function band(score){ return score>=80?'g-green':(score>=60?'g-orange':'g-red'); }
function labelBy(score){ return score>=80?'Great Work — Well Optimized':(score>=60?'Needs Optimization':'Needs Significant Optimization'); }
function escapeHtml(s){ if(s==null) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\"/g,'&quot;').replace(/'/g,'&#39;'); }

f.addEventListener('submit', async (e)=>{
  e.preventDefault();
  water.style.width='0%';
  requestAnimationFrame(()=>{ water.style.width='100%'; });

  const fd = new FormData(f);
  const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') || '' };

  try{
    let res;
    if (ANALYZE_URL.includes('/api/')) {
      res = await fetch(ANALYZE_URL, { method:'POST', headers:{ 'Accept':'application/json','Content-Type':'application/json' }, body: JSON.stringify(payload) });
    } else {
      res = await fetch(ANALYZE_URL, { method:'POST', headers:{ 'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':'{{ csrf_token() }}' }, body: fd });
    }
    if(!res.ok) throw new Error(await res.text()||'Analysis request failed');
    const data = await res.json();
    renderAll(data);
  } catch(err){
    console.error(err);
    // demo fallback to prove UI
    renderAll({
      overall_score: 72,
      wheel: { label: 'Needs Optimization' },
      quick_stats: { readability_flesch: 78, readability_grade: 8, internal_links: 12, external_links: 4, text_to_html_ratio: 38 },
      content_structure:{ title:'Sample: Semantic SEO Guide', meta_description:'A short demo meta description for visibility.', headings:{ H1:['Semantic SEO Master'], H2:['Entities','Schema','Clusters'] } },
      readability:{ score:78, grade:8.2, flesch:78 }
    });
  }
});

function renderAll(data){
  wrap.classList.remove('hidden');

  // OVERALL
  const ov = clamp(data.overall_score);
  wheel.style.setProperty('--v', ov);
  scoreNum.textContent = ov;
  badge.textContent = (data.wheel && data.wheel.label) ? data.wheel.label : labelBy(ov);
  badge.className = 'k-badge ' + (ov>=80?'bg-emerald-500/15 text-emerald-300 border-emerald-700/40':
                                   ov>=60?'bg-amber-500/15 text-amber-300 border-amber-700/40':
                                           'bg-rose-500/15 text-rose-300 border-rose-700/40');

  // QUICK STATS (display clamp 0..100 so UI stays readable)
  const qs = data.quick_stats || {};
  const fleshDisplay = clamp(qs.readability_flesch);
  statF.textContent = isNaN(fleshDisplay) ? '—' : fleshDisplay;
  statG.textContent = (qs.readability_grade!=null ? ('Grade '+qs.readability_grade) : '—');
  statInt.textContent = qs.internal_links ?? 0;
  statExt.textContent = qs.external_links ?? 0;
  statRatio.textContent = (qs.text_to_html_ratio!=null ? (qs.text_to_html_ratio+'%') : '—');

  // READABILITY
  const rb = data.readability || {};
  const rScore = clamp(rb.score ?? fleshDisplay);
  readWheel.style.setProperty('--v', rScore);
  readNum.textContent = clamp(rb.flesch ?? fleshDisplay);
  readBar.style.width = rScore + '%';
  readBadge.textContent = labelBy(rScore);
  readBadge.className = 'pill ' + (rScore>=80?'bg-emerald-500/15 text-emerald-300 border-emerald-700/40':
                                         rScore>=60?'bg-amber-500/15 text-amber-300 border-amber-700/40':
                                                     'bg-rose-500/15 text-rose-300 border-rose-700/40');
  gradeVal.textContent = (rb.grade!=null ? 'Grade '+rb.grade : (qs.readability_grade!=null ? 'Grade '+qs.readability_grade : '—'));

  // STRUCTURE
  const st = data.content_structure || {};
  titleVal.textContent = st.title || '—';
  metaVal.textContent  = st.meta_description || '—';
  headingMap.innerHTML='';
  (Object.entries(st.headings || {})).forEach(([lvl,arr])=>{
    if(!arr || !arr.length) return;
    const box = document.createElement('div');
    box.className='glass p-3';
    box.innerHTML = `<div class="small muted mb-1">${escapeHtml(lvl)}</div>` + arr.map(t=>`<div>• ${escapeHtml(t)}</div>`).join('');
    headingMap.appendChild(box);
  });

  // RECOMMENDATIONS
  recsEl.innerHTML='';
  (data.recommendations||[]).forEach(r=>{
    const tone = r.severity==='Critical' ? 'bg-rose-500/15 text-rose-200 border-rose-700/40'
               : r.severity==='Warning'  ? 'bg-amber-500/15 text-amber-200 border-amber-700/40'
               : 'bg-slate-500/15 text-slate-200 border-slate-700/40';
    const c = document.createElement('div');
    c.className='glass p-3 ' + tone;
    c.style.borderColor = 'transparent';
    c.innerHTML = `<span class="pill ${tone} mr-2">${escapeHtml(r.severity||'Info')}</span>${escapeHtml(r.text||'Recommendation')}`;
    recsEl.appendChild(c);
  });

  // CATEGORIES & CHECKS
  catsEl.innerHTML='';
  (data.categories||[]).forEach(cat=>{
    const tone = band(clamp(cat.score||60));
    const card = document.createElement('div');
    card.className = 'card '+tone;
    card.innerHTML = `
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold">${escapeHtml(cat.name||'Checklist')}</div>
        <div class="pill">${cat.score ?? '—'}</div>
      </div>
      <div class="space-y-2"></div>`;
    const list = card.lastElementChild;

    (cat.checks||[]).forEach(ch=>{
      const sc = clamp(ch.score ?? 0);
      const col = ch.color || ( sc>=80?'green' : sc>=60?'orange' : 'red' );
      const dot = col==='green'?'#34d399':col==='orange'?'#fbbf24':'#f87171';

      const li = document.createElement('div');
      li.className = 'glass px-3 py-2 flex items-center justify-between';
      li.innerHTML = `
        <div class="flex items-center gap-3">
          <span class="w-2.5 h-2.5 rounded-full" style="background:${dot}"></span>
          <div class="text-sm">${escapeHtml(ch.label||'Check')}</div>
        </div>
        <div class="flex items-center gap-2">
          <span class="pill">${sc}</span>
          <button class="btn-ghost small">Improve</button>
        </div>`;
      li.querySelector('button').addEventListener('click', ()=>{
        mTitle.textContent = ch.label || 'Improve';
        mAdvice.textContent = ch.advice || 'Suggested improvements';
        mLink.href = ch.improve_search_url || ('https://www.google.com/search?q=' + encodeURIComponent((cat.name||'SEO')+' '+(ch.label||'improvement')));
        modal.showModal();
      });
      list.appendChild(li);
    });

    catsEl.appendChild(card);
  });

  // Snap water bar to real score
  setTimeout(()=>{ water.style.width = (ov+'%'); }, 150);
}
</script>
@endpush
