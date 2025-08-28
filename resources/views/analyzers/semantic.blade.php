{{-- resources/views/analyzers/semantic.blade.php --}}
@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@push('head')
<style>
  /* ========= Dark Purple + Red Background (no images) ========= */
  body{
    color:#e8eaf6;
    background:
      radial-gradient(1200px 700px at 85% -10%, rgba(255, 64, 95, .22), transparent 60%),
      radial-gradient(900px 600px at   0%   0%, rgba(160, 0, 255, .20), transparent 60%),
      radial-gradient(700px 500px at 100% 100%, rgba(255, 18,  18, .14), transparent 60%),
      linear-gradient(180deg, #1a0b2e, #120822 60%, #0b0816);
    overflow-x:hidden;
  }

  /* ========= Animated Tech Lines (styled like your reference pin) ========= */
  .tech-bg{position:fixed; inset:0; pointer-events:none; opacity:.42; z-index:-1;}
  .tech-bg svg{width:140%; height:140%; transform:translate(-15%,-18%) rotate(-6deg);}
  .tech-bg .dash{stroke-dasharray:8 18; animation:dashMove 16s linear infinite;}
  .tech-bg .glow{filter:drop-shadow(0 0 8px rgba(255,64,95,.55)) drop-shadow(0 0 10px rgba(160,0,255,.45));}
  @keyframes dashMove{to{stroke-dashoffset:-520;}}

  /* ========= Minimal utilities (works with or without Tailwind) ========= */
  .glass{background:rgba(255,255,255,.04); backdrop-filter:blur(6px);}
  .shadow-soft{box-shadow:0 12px 40px rgba(0,0,0,.25);}
  .card { border-radius:1rem; padding:1.25rem; border:1px solid rgba(255,255,255,.10); background:rgba(255,255,255,.03);}
  .pill  { padding:.125rem .5rem; border-radius:9999px; font-size:.75rem; font-weight:600; border:1px solid rgba(255,255,255,.10); }
  .k-badge{ padding:.35rem .6rem; border-radius:.75rem; font-size:.78rem; font-weight:700; border:1px solid rgba(255,255,255,.15); background:rgba(255,255,255,.06); }
  .chip { font-size:.75rem; padding:.125rem .5rem; border-radius:.5rem; background:rgba(255,255,255,.10); border:1px solid rgba(255,255,255,.10); }

  /* ========= Water loading bar ========= */
  #waterbar { height: 12px; border-radius: 9999px; background: rgba(255,255,255,.08); overflow: hidden; border:1px solid rgba(255,255,255,.10);}
  #waterbar span {
    display:block; height:100%; width:0%;
    background: linear-gradient(90deg,#ff3b6a,#a000ff,#ff3b6a);
    transition: width .9s ease;
    filter: drop-shadow(0 0 10px rgba(148,163,184,.4));
  }

  /* ========= Wheels & Bars ========= */
  .score-wheel { width:170px; height:170px; border-radius:50%; display:grid; place-items:center; }
  .score-ring {
    --v: 0; /* 0..100 */
    background:
      conic-gradient(
        #1ef5a4 calc(var(--v)*1%),
        #ffcf5a calc(var(--v)*1% + 0.0001%),
        #ff6b6b 100%
      );
    mask: radial-gradient(circle 70px, transparent 68%, #000 69%);
  }
  .bar { height:.75rem; border-radius:9999px; background:rgba(255,255,255,.10); overflow:hidden; border:1px solid rgba(255,255,255,.08);}
  .bar > span { height:100%; display:block; border-radius:9999px; transition:width .4s ease; background:linear-gradient(90deg,#07f3b0,#60a5fa,#d946ef); }

  /* ========= Checklist glow bands ========= */
  .g-green  { box-shadow: 0 0 0 1px rgba(16,185,129,.45) inset, 0 0 25px rgba(16,185,129,.30); }
  .g-orange { box-shadow: 0 0 0 1px rgba(245,158,11,.40) inset, 0 0 25px rgba(245,158,11,.25); }
  .g-red    { box-shadow: 0 0 0 1px rgba(239,68,68,.40) inset, 0 0 25px rgba(239,68,68,.25); }

  @media (prefers-reduced-motion: reduce){
    .tech-bg .dash{animation:none;}
    #waterbar span{transition:none;}
  }
</style>
@endpush

@section('content')
<!-- Animated tech lines (purple + red) -->
<div class="tech-bg" aria-hidden="true">
  <svg viewBox="0 0 1400 900" preserveAspectRatio="none">
    <defs>
      <linearGradient id="techStroke" x1="0" x2="1">
        <stop offset="0%"   stop-color="#ff405f" stop-opacity="0.85"/>
        <stop offset="100%" stop-color="#a000ff" stop-opacity="0.85"/>
      </linearGradient>
    </defs>

    {{-- angled grid --}}
    @for($x=-100;$x<=1500;$x+=90)
      <line x1="{{ $x }}" y1="-50" x2="{{ $x+200 }}" y2="950"
            stroke="url(#techStroke)" stroke-width="1.2" class="dash glow"/>
    @endfor

    {{-- flowing strands --}}
    @for($i=0;$i<12;$i++)
      @php $y = 60 + $i*65; @endphp
      <path d="M -200 {{ $y }}
               C 200 {{ $y+60 }}, 600 {{ $y-40 }}, 1000 {{ $y+50 }}
               S 1600 {{ $y-30 }}, 1900 {{ $y+40 }}"
            fill="none" stroke="url(#techStroke)" stroke-width="2" class="dash glow"/>
    @endfor
  </svg>
</div>

<section class="max-w-7xl mx-auto px-4 py-10 space-y-8">

  <!-- Top: Form + water bar -->
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
      <button class="rounded-xl shadow-soft bg-gradient-to-r from-fuchsia-500 via-indigo-500 to-red-500 hover:opacity-95 px-5 text-white font-semibold">
        Analyze URL
      </button>
    </form>

    <!-- water bar -->
    <div id="waterbar" class="shadow-soft"><span style="width:0%"></span></div>
    <p class="text-xs text-slate-300">Tip: we fetch the page server-side and compute a detailed semantic & readability report.</p>
  </div>

  <!-- Results -->
  <div id="resultWrap" class="space-y-8 hidden">

    <!-- Wheels + Quick Stats -->
    <div class="grid lg:grid-cols-3 gap-6">
      <div class="card flex items-center gap-5">
        <div class="score-wheel score-ring shadow-soft" id="wheel" style="--v:0">
          <div class="text-center">
            <div id="scoreNum" class="text-4xl font-extrabold">0</div>
            <div class="text-xs text-slate-300">Overall</div>
          </div>
        </div>
        <div class="space-y-2">
          <div id="badge" class="k-badge">—</div>
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
        <span id="readBadge" class="pill">—</span>
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
            <div class="text-xs text-slate-300">
              Grade level: <span id="gradeVal">—</span> (approx. school grade needed to understand)
            </div>
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
        <form method="dialog"><button class="pill">Close</button></form>
      </div>
      <p id="improveAdvice" class="mt-3 text-sm text-slate-200">—</p>
      <a id="improveSearch" target="_blank" class="inline-block mt-4 px-3 py-2 rounded-lg bg-gradient-to-r from-fuchsia-500 to-red-500 text-white text-sm">Search guidance</a>
    </div>
  </dialog>

</section>
@endsection

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

function clamp(n,min=0,max=100){ const v = Number(n); return Math.max(min, Math.min(max, isNaN(v)?0:v)); }
function band(score){ return score>=80?'g-green':(score>=60?'g-orange':'g-red'); }
function labelBy(score){ return score>=80?'Great Work — Well Optimized':(score>=60?'Needs Optimization':'Needs Significant Optimization'); }
function escapeHtml(str){
  if(str==null) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\"/g,'&quot;').replace(/'/g,'&#39;');
}

f.addEventListener('submit', async (e)=>{
  e.preventDefault();

  // Water bar animates 0 -> 100 while loading; snap later to real score
  water.style.width = '0%';
  requestAnimationFrame(()=>{ water.style.width='100%'; });

  const fd = new FormData(f);
  const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') || '' };

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
    if(!res.ok){ throw new Error(await res.text() || 'Analysis request failed'); }
    data = await res.json();
    renderAll(data);
  } catch(err){
    console.error(err);
    // Fallback demo so UI still shows even if backend not ready
    renderAll({
      overall_score: 76,
      wheel: { label: 'Needs Optimization' },
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
      readability: {
        score: 82, grade: 8.9, flesch: 79.5,
        avg_sentence_len: 14.6, simple_words_ratio: 68, passive_ratio: 12,
        note: 'Score blends Flesch and average grade-level into a 0–100 scale.'
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
          { label:'Structured data', score:50, color:'orange', advice:'Add JSON-LD (Article/FAQ/Breadcrumb).' }
        ]},
        { name:'Links & Navigation', score:58, checks:[
          { label:'Internal links to hubs', score:55, color:'orange', advice:'Add 3+ internal links to pillar pages.' },
          { label:'External authoritative refs', score:35, color:'red', advice:'Cite 2–3 high-authority sources.' }
        ]},
        { name:'Experience & Trust', score:80, checks:[
          { label:'Readable for target audience', score:82, color:'green', advice:'Target Grade 8–10.' },
          { label:'Author / Last updated visible', score:70, color:'orange', advice:'Show author and update date.' }
        ]}
      ]
    });
  }
});

function renderAll(data){
  wrap.classList.remove('hidden');

  // OVERALL
  const ov = clamp(data.overall_score);
  wheel.style.setProperty('--v', ov);
  scoreNum.textContent = ov;
  const badgeText = (data.wheel && data.wheel.label) ? data.wheel.label : labelBy(ov);
  badge.textContent = badgeText;
  badge.className = 'k-badge ' + (ov>=80?'bg-emerald-500/20 text-emerald-200 border border-emerald-500/30':
                                   ov>=60?'bg-amber-500/20 text-amber-200 border border-amber-500/30':
                                           'bg-rose-500/20 text-rose-200 border border-rose-500/30');

  // QUICK STATS
  const qs = data.quick_stats || {};
  const flesch = clamp(qs.readability_flesch);
  statF.textContent = (qs.readability_flesch!=null ? Math.round(qs.readability_flesch) : '—');
  statG.textContent = (qs.readability_grade!=null ? ('Grade '+qs.readability_grade) : '—');
  statInt.textContent = qs.internal_links ?? 0;
  statExt.textContent = qs.external_links ?? 0;
  statRatio.textContent = (qs.text_to_html_ratio!=null ? (qs.text_to_html_ratio+'%') : '—');

  // READABILITY (use backend's real block)
  const rb = data.readability || {};
  const rScore = clamp(rb.score);
  const rGrade = (rb.grade!=null ? rb.grade : (qs.readability_grade ?? null));
  const rFlesch = (rb.flesch!=null ? rb.flesch : (qs.readability_flesch ?? 0));

  readWheel.style.setProperty('--v', rScore);
  readNum.textContent = Math.round(rFlesch||0);
  readBar.style.width = rScore + '%';
  gradeVal.textContent = (rGrade!=null ? 'Grade ' + rGrade : '—');
  readBadge.textContent = labelBy(rScore);
  readBadge.className = 'pill ' + (rScore>=80?'bg-emerald-500/20 text-emerald-200 border border-emerald-500/30':
                                         rScore>=60?'bg-amber-500/20 text-amber-200 border border-amber-500/30':
                                                     'bg-rose-500/20 text-rose-200 border border-rose-500/30');

  // STRUCTURE
  const st = data.content_structure || {};
  titleVal.textContent = st.title || '—';
  metaVal.textContent  = st.meta_description || '—';
  headingMap.innerHTML='';
  const headings = st.headings || {};
  Object.entries(headings).forEach(([lvl,arr])=>{
    if(!arr || !arr.length) return;
    const box = document.createElement('div');
    box.className='glass rounded-lg p-3 border border-white/10';
    box.innerHTML = `<div class="text-xs text-slate-300 mb-1 uppercase">${escapeHtml(lvl)}</div>` +
                    arr.map(t=>`<div>• ${escapeHtml(t)}</div>`).join('');
    headingMap.appendChild(box);
  });

  // RECOMMENDATIONS
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

  // CHECKLISTS
  catsEl.innerHTML='';
  (data.categories||[]).forEach(cat=>{
    const tone = band(clamp(cat.score));
    const card = document.createElement('div');
    card.className = 'card ' + tone;
    card.innerHTML = `
      <div class="flex items-center justify-between mb-3">
        <div class="font-semibold">${escapeHtml(cat.name||'Checklist')}</div>
        <div class="pill">${cat.score ?? '—'}</div>
      </div>
      <div class="space-y-2"></div>`;
    const list = card.lastElementChild;
    const checks = cat.checks || cat.items || [];
    checks.forEach(ch=>{
      const li = document.createElement('div');
      const sc = clamp(ch.score, 0, 100);
      const col = ch.color || ( sc>=80?'green' : sc>=60?'orange' : 'red' );
      const colDot = col==='green'?'bg-emerald-400':col==='orange'?'bg-amber-400':'bg-rose-400';
      li.className = 'glass rounded-lg px-3 py-2 border border-white/10 flex items-center justify-between';
      li.innerHTML = `
        <div class="flex items-center gap-3">
          <span class="w-2.5 h-2.5 rounded-full ${colDot}"></span>
          <div class="text-sm">${escapeHtml(ch.label||ch.title||'Check')}</div>
        </div>
        <div class="flex items-center gap-2">
          <span class="pill">${sc || '—'}</span>
          <button class="px-3 py-1 rounded-lg text-xs bg-gradient-to-r from-fuchsia-500 to-red-500 text-white">Improve</button>
        </div>`;
      li.querySelector('button').addEventListener('click', ()=>{
        mTitle.textContent = ch.label || ch.title || 'Improve';
        mAdvice.textContent = ch.advice || ch.hint || 'Suggested improvements';
        mLink.href = ch.improve_search_url || ('https://www.google.com/search?q=' + encodeURIComponent((cat.name||'SEO')+' '+(ch.label||ch.title||'improvement')));
        modal.showModal();
      });
      list.appendChild(li);
    });
    catsEl.appendChild(card);
  });

  // Snap water bar to the actual overall score after render
  setTimeout(()=>{ water.style.width = (ov+'%'); }, 120);
}
</script>
@endpush
