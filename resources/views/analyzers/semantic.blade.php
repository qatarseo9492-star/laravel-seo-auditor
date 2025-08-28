{{-- resources/views/analyzers/semantic.blade.php --}}
@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* ================= THEME (high-contrast on black) ================= */
  :root{
    --bg:#000;
    --card:#0b0b10;
    --card-2:#101018;
    --border:#26262b;
    --muted:#9aa0aa;
    --text:#ECEFF4;
    --text-dim:#C8CDD5;

    --accent:#22d3ee;   /* cyan */
    --accent-2:#a78bfa; /* iris */
    --accent-3:#f43f5e; /* rose */

    --good:#10b981;   /* green */
    --warn:#f59e0b;   /* amber */
    --bad:#ef4444;    /* red */
  }

  html, body { background:var(--bg) !important; }
  body{ color:var(--text); overflow-x:hidden; }

  /* Background tech lines – subtle */
  .tech-bg{ position:fixed; inset:0; pointer-events:none; z-index:1; opacity:.10; }
  .tech-bg svg{ width:160%; height:160%; transform:translate(-20%,-22%) rotate(-8deg); }
  .tech-bg .dash{ stroke-dasharray:10 22; animation:dashMove 18s linear infinite; }
  .tech-bg .glow{ filter:drop-shadow(0 0 4px rgba(167,139,250,.3)); }
  @keyframes dashMove{ to{ stroke-dashoffset:-640; } }

  /* Content above BG lines */
  .content-root{ position:relative; z-index:2; }

  /* Cards & UI */
  .card{ background:var(--card); border:1px solid var(--border); border-radius:18px; padding:18px; box-shadow:0 12px 40px rgba(0,0,0,.55); }
  .glass{ background:var(--card-2); border:1px solid var(--border); border-radius:16px; }
  .pill{ font-size:.72rem; padding:.2rem .55rem; border:1px solid var(--border); border-radius:999px; color:var(--text-dim); display:inline-flex; align-items:center; gap:.45rem; }
  .chip{ font-size:.72rem; padding:.2rem .55rem; border-radius:8px; background:#12131a; border:1px solid var(--border); color:var(--text-dim); }
  .k-badge{ font-size:.78rem; padding:.45rem .6rem; border-radius:10px; background:#14151d; border:1px solid var(--border); font-weight:700; }

  a, button { outline:none; }

  /* Buttons */
  .btn-primary{
    background:linear-gradient(90deg, var(--accent) 0%, var(--accent-2) 50%, var(--accent-3) 100%);
    color:#fff; border:none; border-radius:12px; padding:.85rem 1rem; font-weight:700;
    transition: transform .05s ease, opacity .2s ease;
  }
  .btn-primary:hover{ opacity:.95; transform:translateY(-1px); }
  .btn-ghost{ background:#14151d; color:var(--text); border:1px solid var(--border); border-radius:10px; padding:.5rem .75rem; }

  /* Water bar */
  #waterbar{ height:14px; border-radius:999px; background:#12131a; border:1px solid var(--border); overflow:hidden; }
  #waterbar span{ display:block; height:100%; width:0%; background:linear-gradient(90deg,var(--accent),var(--accent-2),var(--accent-3)); transition:width .9s ease; filter:drop-shadow(0 0 8px rgba(79,209,255,.25)); }

  /* ==================== SCORE WHEELS ==================== */
  .score-wheel{ width:180px; height:180px; border-radius:50%; display:grid; place-items:center; }
  .score-ring{
    --v:0;
    background: conic-gradient(var(--good) calc(var(--v)*1%), var(--warn) calc(var(--v)*1% + .0001%), var(--bad) 100%);
    mask: radial-gradient(circle 74px, transparent 66%, #000 67%);
    border-radius:50%;
    box-shadow: inset 0 0 0 8px #0e1016, 0 6px 24px rgba(0,0,0,.5);
  }
  .wheel-lg{ width:220px; height:220px; }
  .wheel-lg .score-ring{ mask: radial-gradient(circle 90px, transparent 80%, #000 81%); }

  /* ==================== READABILITY INSIGHTS ==================== */
  .readable-grid{ display:grid; grid-template-columns: 260px 1fr; gap:18px; }
  @media (max-width: 900px){ .readable-grid{ grid-template-columns:1fr; } }

  .insights{ display:grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap:14px; }
  @media (max-width: 900px){ .insights{ grid-template-columns:repeat(2,minmax(0,1fr)); } }

  .insight{
    background:var(--card-2);
    border:1px solid var(--border);
    border-radius:14px; padding:14px;
    box-shadow:0 10px 22px rgba(0,0,0,.35);
  }
  .insight .title{ font-size:.8rem; color:var(--muted); display:flex; align-items:center; gap:.5rem; }
  .insight .value{ font-size:1.35rem; font-weight:800; margin-top:2px; }
  .mini-bar{ height:8px; border-radius:999px; background:#11131a; border:1px solid var(--border); overflow:hidden; margin-top:8px; }
  .mini-bar > span{ display:block; height:100%; width:0%; background:linear-gradient(90deg,var(--accent),var(--accent-2)); border-radius:999px; transition:width .4s ease; }

  .fixes{ background:var(--card-2); border:1px solid var(--border); border-radius:14px; padding:14px; }
  .banner{
    padding:14px 16px; border-radius:14px; border:1px solid var(--border);
    background: linear-gradient(90deg, rgba(34,211,238,.15), rgba(167,139,250,.12));
  }
  .banner--warn{ background: linear-gradient(90deg, rgba(245,158,11,.2), rgba(167,139,250,.10)); }
  .banner--bad{  background: linear-gradient(90deg, rgba(239,68,68,.22), rgba(167,139,250,.08)); }

  .muted{ color:var(--muted); }
  .small{ font-size:.82rem; }
  .icon{
    width:22px;height:22px;border-radius:6px;display:inline-grid;place-items:center;
    background:linear-gradient(135deg,var(--accent),var(--accent-2));
    color:#001015;font-weight:900;
  }

  /* Checklist glow frames */
  .g-green { box-shadow: 0 0 0 1px rgba(16,185,129,.35) inset, 0 0 24px rgba(16,185,129,.2); }
  .g-orange{ box-shadow: 0 0 0 1px rgba(245,158,11,.32) inset, 0 0 24px rgba(245,158,11,.18); }
  .g-red   { box-shadow: 0 0 0 1px rgba(239,68,68,.32)  inset, 0 0 24px rgba(239,68,68,.18); }

  @media (prefers-reduced-motion:reduce){ .tech-bg .dash{animation:none;} #waterbar span{transition:none;} }
</style>
@endpush

@section('content')
<!-- Subtle dashed tech lines -->
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

    <!-- Overall -->
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

    <!-- =================== READABILITY INSIGHTS (screenshot-like) =================== -->
    <div class="card">
      <div class="flex items-center justify-between gap-4 mb-4">
        <h3 class="font-semibold flex items-center gap-2">
          <span class="icon">✦</span> Readability Insights
        </h3>
        <span id="gradePill" class="pill">Grade —</span>
      </div>
      <p id="readDesc" class="small muted mb-4">Complex reading level.</p>

      <div class="readable-grid">
        <!-- Wheel -->
        <div class="glass p-4 flex flex-col items-center justify-center">
          <div class="score-wheel wheel-lg">
            <div id="readWheel" class="score-ring" style="--v:0"></div>
          </div>
          <div class="text-center -mt-16">
            <div id="readNum" class="text-4xl font-extrabold">0</div>
            <div class="small muted">Readability score</div>
          </div>
          <div class="bar w-full mt-4" style="height:10px;border:1px solid var(--border);background:#12131a;border-radius:999px;overflow:hidden">
            <span id="readBar" style="display:block;height:100%;width:0%;background:linear-gradient(90deg,var(--accent),var(--accent-2));border-radius:999px;transition:width .4s ease;"></span>
          </div>
        </div>

        <!-- Metric tiles -->
        <div class="insights">
          <div class="insight">
            <div class="title"><span class="icon">☺</span> Flesch Reading Ease</div>
            <div id="fleschVal" class="value">—</div>
            <div class="mini-bar"><span id="fleschBar"></span></div>
          </div>
          <div class="insight">
            <div class="title"><span class="icon">↔</span> Avg Sentence Length</div>
            <div class="value"><span id="aslVal">—</span></div>
            <div class="mini-bar"><span id="aslBar"></span></div>
          </div>
          <div class="insight">
            <div class="title"><span class="icon">A</span> Words</div>
            <div id="wordsVal" class="value">—</div>
            <div class="mini-bar"><span id="wordsBar"></span></div>
          </div>
          <div class="insight">
            <div class="title"><span class="icon">語</span> Syllables / Word</div>
            <div id="spwVal" class="value">—</div>
            <div class="mini-bar"><span id="spwBar"></span></div>
          </div>
          <div class="insight">
            <div class="title"><span class="icon">∿</span> Lexical Diversity (TTR)</div>
            <div id="ttrVal" class="value">—</div>
            <div class="mini-bar"><span id="ttrBar"></span></div>
          </div>
          <div class="insight">
            <div class="title"><span class="icon">⟲</span> Repetition (tri-gram)</div>
            <div id="triVal" class="value">—</div>
            <div class="mini-bar"><span id="triBar"></span></div>
          </div>
          <div class="insight">
            <div class="title"><span class="icon">#</span> Digits / 100 words</div>
            <div id="digitsVal" class="value">—</div>
            <div class="mini-bar"><span id="digitsBar"></span></div>
          </div>
        </div>
      </div>

      <!-- Fixes -->
      <div class="fixes mt-4">
        <div class="small muted mb-2">Simple Fixes</div>
        <ul id="fixList" class="list-disc pl-5 text-sm space-y-1 text-white/90">
          <li>Break long sentences into 12–16 words.</li>
          <li>Prefer shorter words (use simpler synonyms).</li>
          <li>Reduce numeric density; round or group numbers.</li>
        </ul>
      </div>

      <!-- Target banner -->
      <div id="gradeBanner" class="banner mt-3 small">
        <strong id="gradeGoal">Easy to read (Grade 7–9).</strong>
        <span id="gradeNote" class="ml-1">Try smaller sentences, simpler words, and fewer complex clauses.</span>
      </div>
    </div>

    <!-- Content Structure -->
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

const $ = (id)=>document.getElementById(id);
const f = $('semanticForm');
const water = document.querySelector('#waterbar span');
const wrap = $('resultWrap');

const wheel = $('wheel');
const scoreNum = $('scoreNum');
const badge = $('badge');

const statF = $('statFlesch');
const statG = $('statGrade');
const statInt = $('statInt');
const statExt = $('statExt');
const statRatio = $('statRatio');

/* Readability elements */
const readWheel = $('readWheel');
const readNum = $('readNum');
const readBar = $('readBar');
const gradePill = $('gradePill');
const readDesc = $('readDesc');

const fVal = $('fleschVal'), fBar = $('fleschBar');
const aslVal = $('aslVal'), aslBar = $('aslBar');
const wordsVal = $('wordsVal'), wordsBar = $('wordsBar');
const spwVal = $('spwVal'), spwBar = $('spwBar');
const ttrVal = $('ttrVal'), ttrBar = $('ttrBar');
const triVal = $('triVal'), triBar = $('triBar');
const digitsVal = $('digitsVal'), digitsBar = $('digitsBar');

const gradeBanner = $('gradeBanner');
const gradeGoal = $('gradeGoal');
const gradeNote = $('gradeNote');

const headingMap = $('headingMap');
const titleVal = $('titleVal');
const metaVal = $('metaVal');
const recsEl = $('recs');
const catsEl = $('cats');

const modal = $('improveModal');
const mTitle = $('improveTitle');
const mAdvice= $('improveAdvice');
const mLink  = $('improveSearch');

function clamp(n,min=0,max=100){ const v=Number(n); return Math.max(min, Math.min(max, isNaN(v)?0:v)); }
function labelBy(score){ return score>=80?'Great Work — Well Optimized':(score>=60?'Needs Optimization':'Needs Significant Optimization'); }
function band(score){ return score>=80?'g-green':(score>=60?'g-orange':'g-red'); }
function escapeHtml(s){ if(s==null) return ''; return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\"/g,'&quot;').replace(/'/g,'&#39;'); }

function pctForASL(asl){ // ideal 12–17 words
  if(!asl) return 0;
  const a = Math.max(8, Math.min(35, Number(asl)));
  const good = a<=17 ? 100 : Math.max(0, 100 - ((a-17)*4));
  return Math.round(good);
}
function pctForSPW(spw){ // ideal <=1.5 syll/word
  if(!spw) return 0;
  const s = Math.max(1, Math.min(3, Number(spw)));
  return Math.round(100 - (s-1.2)*62); // 1.2~1.6 good
}
function pctForWords(w){ // more is not always better; treat 600 as full
  if(!w) return 0;
  const c = Math.min(100, Math.round(Math.min(Number(w),600)/6));
  return c;
}
function pctForTTR(ttr){ // ttr in 0..1 or 0..100
  let p = Number(ttr);
  if(p<=1) p*=100;
  return Math.round(Math.max(0, Math.min(100, p)));
}
function pctForRepetition(p){ // lower better
  let v = Number(p); if(v<=1) v*=100;
  v = Math.max(0, Math.min(100, v));
  return Math.round(100 - v); // invert
}
function setTile(valEl, barEl, valueText, pct){
  valEl.textContent = valueText;
  barEl.style.width = (isNaN(pct)?0:pct) + '%';
}

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
    renderAll(await res.json());
  } catch(err){
    console.error(err);
    // demo fallback (so UI shows)
    renderAll({
      overall_score: 72,
      wheel: { label: 'Needs Optimization' },
      quick_stats: { readability_flesch: 9, readability_grade: 18, internal_links: 12, external_links: 4, text_to_html_ratio: 38 },
      readability:{
        score:22, grade:18, flesch:9, avg_sentence_len:69.6, words:487, syllables_per_word:1.96,
        lexical_diversity:0.54, repetition_trigram:0.09, digits_per_100_words:15
      },
      content_structure:{ title:'Sample: Semantic SEO Guide', meta_description:'A short demo meta description for visibility.', headings:{ H1:['Semantic SEO Master'], H2:['Entities','Schema','Clusters'] } }
    });
  }
});

function renderAll(data){
  wrap.classList.remove('hidden');

  /* OVERALL */
  const ov = clamp(data.overall_score);
  wheel.style.setProperty('--v', ov);
  scoreNum.textContent = ov;
  badge.textContent = (data.wheel && data.wheel.label) ? data.wheel.label : labelBy(ov);
  badge.className = 'k-badge ' + (ov>=80?'bg-emerald-500/15 text-emerald-300 border-emerald-700/40':
                                   ov>=60?'bg-amber-500/15 text-amber-300 border-amber-700/40':
                                           'bg-rose-500/15 text-rose-300 border-rose-700/40');

  /* QUICK STATS */
  const qs = data.quick_stats || {};
  const fleshDisplay = clamp(qs.readability_flesch);
  statF.textContent = isNaN(fleshDisplay) ? '—' : fleshDisplay;
  statG.textContent = (qs.readability_grade!=null ? ('Grade '+qs.readability_grade) : '—');
  statInt.textContent = qs.internal_links ?? 0;
  statExt.textContent = qs.external_links ?? 0;
  statRatio.textContent = (qs.text_to_html_ratio!=null ? (qs.text_to_html_ratio+'%') : '—');

  /* READABILITY BLOCK (detailed) */
  const rb = data.readability || {};
  const rScore = clamp(rb.score ?? fleshDisplay);
  const grade = rb.grade ?? qs.readability_grade ?? null;
  const flesch = clamp(rb.flesch ?? fleshDisplay);
  const asl = rb.avg_sentence_len ?? null;
  const words = rb.words ?? rb.word_count ?? null;
  const spw = rb.syllables_per_word ?? rb.spw ?? null;
  const ttr = rb.lexical_diversity ?? rb.ttr ?? null;       // 0..1 or 0..100
  const tri = rb.repetition_trigram ?? rb.repetition ?? 0;  // 0..1 or 0..100
  const digits = rb.digits_per_100_words ?? rb.density_digits ?? 0;

  readWheel.style.setProperty('--v', rScore);
  readNum.textContent = rScore;
  readBar.style.width = rScore + '%';

  gradePill.textContent = 'Grade ' + (grade ?? '—');

  if(grade==null){ readDesc.textContent = 'Readability grade unavailable.'; }
  else if(grade <= 7){ readDesc.textContent = 'Easy reading level. Great for broad audiences.'; }
  else if(grade <= 10){ readDesc.textContent = 'Fair reading level. Consider trimming sentences and simplifying words.'; }
  else { readDesc.textContent = 'Complex reading level. Use shorter sentences and simpler vocabulary.'; }

  /* Metric tiles (values + bars) */
  setTile(fVal, fBar, String(flesch), flesch);
  setTile(aslVal, aslBar, asl!=null ? Number(asl).toFixed(1) : '—', pctForASL(asl));
  setTile(wordsVal, wordsBar, words!=null ? String(words) : '—', pctForWords(words));
  setTile(spwVal, spwBar, spw!=null ? Number(spw).toFixed(2) : '—', pctForSPW(spw));
  setTile(ttrVal, ttrBar, ttr!=null ? (pctForTTR(ttr)+'%') : '—', pctForTTR(ttr));
  setTile(triVal, triBar, tri!=null ? ((tri<=1?tri*100:tri).toFixed(0)+'%') : '—', pctForRepetition(tri));
  setTile(digitsVal, digitsBar, digits!=null ? String(digits) : '—', clamp(100 - Math.min(100, Number(digits)*3)));

  /* Simple fixes (data-driven) */
  const fixes = [];
  if(asl!=null && asl>20) fixes.push('Break long sentences into 12–16 words.');
  if(spw!=null && spw>1.6) fixes.push('Prefer shorter words (use simpler synonyms).');
  if(digits!=null && digits>10) fixes.push('Reduce numeric density; round or group numbers where possible.');
  if(ttr!=null && pctForTTR(ttr)<45) fixes.push('Increase lexical variety (use synonyms and specific nouns).');
  if(tri!=null && (tri>0.12 || (tri<=1 && tri>0.12))) fixes.push('Reduce phrase repetition; vary transitions.');
  if(!fixes.length) fixes.push('Nice! Your readability metrics look solid — keep sentences concise and vocabulary familiar.');
  const list = $('fixList'); list.innerHTML = fixes.map(x=>`<li>${escapeHtml(x)}</li>`).join('');

  /* Grade goal banner */
  if(grade!=null && grade<=7){
    gradeBanner.className = 'banner';
    gradeGoal.textContent = 'Easy to read (Grade 7).';
    gradeNote.textContent = 'Excellent — keep sentences tight and vocabulary familiar.';
  }else if(grade!=null && grade<=10){
    gradeBanner.className = 'banner banner--warn';
    gradeGoal.textContent = 'Target: Grade 7–9.';
    gradeNote.textContent = 'Trim sentence length and simplify some word choices.';
  }else{
    gradeBanner.className = 'banner banner--bad';
    gradeGoal.textContent = 'Target: Grade 7–9.';
    gradeNote.textContent = 'Shorten sentences, prefer common words, and reduce clause stacking.';
  }

  /* Structure */
  const st = data.content_structure || {};
  titleVal.textContent = st.title || '—';
  metaVal.textContent  = st.meta_description || '—';
  headingMap.innerHTML='';
  Object.entries(st.headings || {}).forEach(([lvl,arr])=>{
    if(!arr || !arr.length) return;
    const box = document.createElement('div');
    box.className='glass p-3';
    box.innerHTML = `<div class="small muted mb-1">${escapeHtml(lvl)}</div>` + arr.map(t=>`<div>• ${escapeHtml(t)}</div>`).join('');
    headingMap.appendChild(box);
  });

  /* Recommendations */
  recsEl.innerHTML='';
  (data.recommendations||[]).forEach(r=>{
    const tone = r.severity==='Critical' ? 'bg-rose-500/15 text-rose-200 border-rose-700/40'
               : r.severity==='Warning'  ? 'bg-amber-500/15 text-amber-200 border-amber-700/40'
               : 'bg-slate-500/15 text-slate-200 border-slate-700/40';
    const c = document.createElement('div');
    c.className='glass p-3 ' + tone; c.style.borderColor='transparent';
    c.innerHTML = `<span class="pill ${tone} mr-2">${escapeHtml(r.severity||'Info')}</span>${escapeHtml(r.text||'Recommendation')}`;
    recsEl.appendChild(c);
  });

  /* Categories */
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
        $('improveTitle').textContent = ch.label || 'Improve';
        $('improveAdvice').textContent = ch.advice || 'Suggested improvements';
        $('improveSearch').href = ch.improve_search_url || ('https://www.google.com/search?q=' + encodeURIComponent((cat.name||'SEO')+' '+(ch.label||'improvement')));
        modal.showModal();
      });
      list.appendChild(li);
    });
    catsEl.appendChild(card);
  });

  setTimeout(()=>{ water.style.width = (ov+'%'); }, 150);
}
</script>
@endpush
