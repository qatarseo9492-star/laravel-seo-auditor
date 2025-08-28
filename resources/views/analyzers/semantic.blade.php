@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* App background */
  html,body{background:#06021f!important;color:#e5e7eb}

  /* Simple utilities */
  .glass{background:rgba(255,255,255,.06);backdrop-filter:blur(10px)}
  .card{border-radius:18px;padding:18px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.10)}
  .pill{padding:6px 12px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.08);color:#e5e7eb}
  .t-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent}

  /* Multi-color glow outline (FASTER: ~1s) */
  .glow-anim{position:relative}
  .glow-anim::before{
    content:"";position:absolute;inset:-2px;border-radius:inherit;
    background:conic-gradient(from 0deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e,#67e8f9);
    filter:blur(10px);opacity:.35;z-index:-1;animation:spinGlow 1s linear infinite;
  }
  @keyframes spinGlow{to{transform:rotate(360deg)}}

  /* Header special: Shoail Kahoker rainbow + gentle dance */
  .rainbow-dance{display:inline-block;background:linear-gradient(90deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);background-size:400% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;animation:rainbowSlide 6s linear infinite,bob 2.6s ease-in-out infinite}
  @keyframes rainbowSlide{0%{background-position:0% 50%}100%{background-position:100% 50%}}
  @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  /* Analyze toolbar */
  .analyze-wrap{border-radius:18px;background:#020114;border:1px solid #1b2640;box-shadow:inset 0 0 0 1px rgba(255,255,255,.04),0 20px 60px rgba(2,1,20,.45)}
  .btn{padding:12px 18px;border-radius:14px;font-weight:900;border:1px solid rgba(255,255,255,.12);color:#0b1020}
  .btn-green{background:#22c55e;box-shadow:0 8px 26px rgba(34,197,94,.35)}
  .btn-blue{background:#3b82f6;box-shadow:0 8px 26px rgba(59,130,246,.35)}
  .btn-orange{background:#f59e0b;box-shadow:0 8px 26px rgba(245,158,11,.35)}
  .btn-purple{background:linear-gradient(90deg,#a78bfa,#f472b6);color:#19041a;box-shadow:0 8px 26px rgba(167,139,250,.35)}
  .lg-green{background:rgba(16,185,129,.15);color:#a7f3d0;border-color:rgba(16,185,129,.35)}
  .lg-orange{background:rgba(245,158,11,.15);color:#fde68a;border-color:rgba(245,158,11,.35)}
  .lg-red{background:rgba(239,68,68,.15);color:#fecaca;border-color:rgba(239,68,68,.35)}

  /* Score chips with icons */
  .chip{padding:12px 16px;border-radius:16px;font-weight:900;display:inline-flex;align-items:center;gap:10px;border:1px solid rgba(255,255,255,.14);color:#eef2ff}
  .chip i{font-style:normal;font-size:18px}
  .chip.good{background:linear-gradient(135deg,rgba(34,197,94,.35),rgba(16,185,129,.18));border-color:rgba(34,197,94,.45);color:#eafff3}
  .chip.warn{background:linear-gradient(135deg,rgba(245,158,11,.35),rgba(250,204,21,.18));border-color:rgba(245,158,11,.45);color:#fff7e6}
  .chip.bad{background:linear-gradient(135deg,rgba(239,68,68,.35),rgba(248,113,113,.18));border-color:rgba(239,68,68,.45);color:#ffecec}

  /* Wheel with bottom fill */
  .mega{display:grid;place-items:center;gap:12px}
  .mw{--v:0;--ring:#f59e0b;--p:0;width:280px;height:280px;position:relative}
  .mw-ring{position:absolute;inset:0;border-radius:50%;background:conic-gradient(var(--ring) calc(var(--v)*1%),rgba(255,255,255,.08) 0);-webkit-mask:radial-gradient(circle 108px,transparent 100px,#000 100px);mask:radial-gradient(circle 108px,transparent 100px,#000 100px);box-shadow:inset 0 0 0 14px rgba(255,255,255,.06)}
  .mw-fill{position:absolute;inset:26px;border-radius:50%;overflow:hidden;background:#000}
  .mw-fill::after{content:"";position:absolute;left:0;right:0;height:100%;top:calc(100% - var(--p)*1%);transition:top .9s ease;background:var(--fill,linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%));-webkit-mask:radial-gradient(140px 22px at 50% 0,#0000 98%,#000 100%);mask:radial-gradient(140px 22px at 50% 0,#0000 98%,#000 100%)}
  .mw.good{--ring:#22c55e;--fill:linear-gradient(to top,#16a34a 0%,#22c55e 60%,#86efac 100%)}
  .mw.warn{--ring:#f59e0b;--fill:linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%)}
  .mw.bad{--ring:#ef4444;--fill:linear-gradient(to top,#ef4444 0%,#f87171 60%,#fecaca 100%)}
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:64px;font-weight:900;color:#fff;text-shadow:0 6px 22px rgba(0,0,0,.45)}

  /* Overall water bar */
  .waterbox{position:relative;height:22px;border-radius:9999px;overflow:hidden;border:1px solid rgba(255,255,255,.12);background:#0b0b0b}
  .waterbox .fill{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .waterbox.good .fill{background:linear-gradient(90deg,#16a34a,#22c55e,#86efac)}
  .waterbox.warn .fill{background:linear-gradient(90deg,#f59e0b,#fbbf24,#fde68a)}
  .waterbox.bad .fill{background:linear-gradient(90deg,#ef4444,#f87171,#fecaca)}
  .waterbox .label{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e5e7eb;font-size:12px;text-shadow:0 2px 10px rgba(0,0,0,.45)}

  /* Status chips row */
  .status-row{display:flex;flex-wrap:wrap;gap:12px}
  .status-chip{padding:12px 18px;border-radius:26px;font-weight:900;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.10);color:#eef2ff}
  .status-chip .k{margin-right:6px}

  /* Ground area */
  .ground-slab{border-radius:24px;padding:22px;background:#0D0E1E;border:1px solid #1b2640}
  .cat-card{border-radius:18px;padding:18px;background:#111E2F;border:1px solid rgba(255,255,255,.12)}
  .cat-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:12px}
  .cat-title{font-size:22px;font-weight:900;background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185);-webkit-background-clip:text;background-clip:text;color:transparent}
  .progress{width:100%;height:12px;border-radius:9999px;background:rgba(255,255,255,.08);overflow:hidden;border:1px solid rgba(255,255,255,.14)}
  .progress>span{display:block;height:100%;border-radius:9999px;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e);transition:width .5s ease}
  .check{display:flex;align-items:center;justify-content:space-between;border-radius:14px;padding:14px 16px;border:1px solid rgba(255,255,255,.10);background:#0F1A29}
  .score-pill{padding:4px 8px;border-radius:10px;font-weight:800;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:#e5e7eb}
  .score-pill--green{background:rgba(16,185,129,.18);border-color:rgba(16,185,129,.35);color:#bbf7d0}
  .score-pill--orange{background:rgba(245,158,11,.18);border-color:rgba(245,158,11,.35);color:#fde68a}
  .score-pill--red{background:rgba(239,68,68,.18);border-color:rgba(239,68,68,.35);color:#fecaca}

  /* Improve button: fill color by band (NEW) + outline by band */
  .improve-btn{padding:6px 10px;border-radius:10px;color:#0b1020;font-weight:800;border:1px solid transparent;transition:transform .08s ease}
  .improve-btn:active{transform:translateY(1px)}
  .fill-green {background:linear-gradient(135deg,#16a34a,#22c55e,#86efac);color:#05240f}
  .fill-orange{background:linear-gradient(135deg,#f59e0b,#fbbf24,#fde68a);color:#3a2400}
  .fill-red   {background:linear-gradient(135deg,#ef4444,#f87171,#fecaca);color:#2f0606}
  .outline-green{border-color:rgba(34,197,94,.85)!important;box-shadow:0 0 0 2px rgba(34,197,94,.55) inset,0 0 16px rgba(34,197,94,.25)}
  .outline-orange{border-color:rgba(245,158,11,.85)!important;box-shadow:0 0 0 2px rgba(245,158,11,.55) inset,0 0 16px rgba(245,158,11,.25)}
  .outline-red{border-color:rgba(239,68,68,.85)!important;box-shadow:0 0 0 2px rgba(239,68,68,.55) inset,0 0 16px rgba(239,68,68,.25)}

  /* Modal */
  dialog[open]{display:block}
  dialog::backdrop{background:rgba(0,0,0,.6)}
  #improveModal .card{background:#0D0E1E;border:1px solid #1b2640}
  #improveModal .card .card{background:#111E2F;border-color:rgba(255,255,255,.12)}
</style>
@endpush

@section('content')
<section class="max-w-7xl mx-auto px-4 py-8 space-y-8">

  <!-- Header -->
  <div class="flex items-center justify-between flex-wrap gap-4 glow-anim">
    <div class="flex items-center gap-3">
      <div class="glow-anim" style="width:46px;height:46px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(99,102,241,.32),rgba(236,72,153,.32));border:1px solid rgba(255,255,255,.14)">👑</div>
      <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold">
        <span class="t-grad">Semantic SEO Master Analyzer</span>
        <span class="text-slate-300 text-base md:text-lg">&nbsp;By&nbsp;<span class="rainbow-dance">Shoail Kahoker</span></span>
      </h1>
    </div>
    <div class="flex flex-wrap gap-2">
      <span class="pill lg-green glow-anim">Green ≥ 80</span>
      <span class="pill lg-orange glow-anim">Orange 60–79</span>
      <span class="pill lg-red glow-anim">Red &lt; 60</span>
    </div>
  </div>

  <!-- Wheel + chips -->
  <div class="grid lg:grid-cols-[340px,1fr] gap-6 items-center">
    <div class="mega glow-anim">
      <div class="mw warn glow-anim" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-fill" id="mwFill" style="--p:0"></div>
        <div class="mw-center" id="mwNum">0%</div>
      </div>
    </div>
    <div class="space-y-3">
      <div class="flex flex-wrap gap-3">
        <span id="chipOverall" class="chip warn glow-anim"><i>🟠</i><span>Overall: 0 /100</span></span>
        <span id="chipContent" class="chip warn glow-anim"><i>🟠</i><span>Content: —</span></span>
        <span id="chipWriter"  class="chip glow-anim"><i>🟠</i><span>Writer: —</span></span>
        <span id="chipHuman"   class="chip glow-anim"><i>🟠</i><span>Human-like: — %</span></span>
        <span id="chipAI"      class="chip glow-anim"><i>🟠</i><span>AI-like: — %</span></span>
      </div>
      <div id="overallBar" class="waterbox warn glow-anim">
        <div class="fill" id="overallFill" style="width:0%"></div>
        <div class="label"><span id="overallPct">0%</span></div>
      </div>
      <p class="text-xs text-slate-400">Wheel + water bars fill with your scores (colors: green ≥80, orange 60–79, red &lt;60).</p>
    </div>
  </div>

  <!-- Analyze toolbar -->
  <div class="analyze-wrap p-4 space-y-3 glow-anim">
    <label class="flex items-center gap-2 rounded-xl px-3 py-2 glow-anim" style="background:#0b0b0b;border:1px solid rgba(255,255,255,.12)">
      <span class="opacity-70">🌐</span>
      <input id="urlInput" name="url" type="url" placeholder="https://example.com" class="w-full bg-transparent outline-none text-slate-100 placeholder:text-slate-400"/>
      <button id="pasteBtn" type="button" class="pill">✕ Paste</button>
    </label>

    <div class="flex items-center gap-3">
      <label class="flex items-center gap-2 text-sm">
        <input id="autoCheck" type="checkbox" class="accent-emerald-400" checked/> Auto-apply checkmarks (≥ 80)
      </label>
      <div class="flex-1"></div>
      <input id="importFile" type="file" accept="application/json" class="hidden"/>
      <button id="importBtn" type="button" class="btn btn-purple glow-anim">⇪ Import</button>
      <button id="analyzeBtn" type="button" class="btn btn-green glow-anim">🔍 Analyze</button>
      <button id="printBtn"   type="button" class="btn btn-blue glow-anim">🖨️ Print</button>
      <button id="resetBtn"   type="button" class="btn btn-orange glow-anim">↻ Reset</button>
      <button id="exportBtn"  type="button" class="btn btn-purple glow-anim">⬇︎ Export</button>
    </div>

    <div id="statusChips" class="status-row mt-2">
      <div class="status-chip glow-anim"><span class="k t-grad">HTTP:</span> <span id="chipHttp">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">Title:</span> <span id="chipTitle">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">Meta desc:</span> <span id="chipMeta">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">Canonical:</span> <span id="chipCanon">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">Robots:</span> <span id="chipRobots">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">Viewport:</span> <span id="chipViewport">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">H1/H2/H3:</span> <span id="chipH">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">Internal links:</span> <span id="chipInt">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">Schema:</span> <span id="chipSchema">—</span></div>
      <div class="status-chip glow-anim"><span class="k t-grad">Auto-checked:</span> <span id="chipAuto">0</span></div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="card glow-anim">
    <h3 class="t-grad font-extrabold mb-3">Quick Stats</h3>
    <div class="grid sm:grid-cols-3 gap-4 text-sm">
      <div class="card glow-anim"><div class="text-slate-300 text-xs">Readability (Flesch)</div><div id="statFlesch" class="text-2xl font-bold">—</div><div id="statGrade" class="text-xs text-slate-400">—</div></div>
      <div class="card glow-anim"><div class="text-slate-300 text-xs">Links (int / ext)</div><div class="text-2xl font-bold"><span id="statInt">0</span> / <span id="statExt">0</span></div></div>
      <div class="card glow-anim"><div class="text-slate-300 text-xs">Text/HTML Ratio</div><div id="statRatio" class="text-2xl font-bold">—</div></div>
    </div>
  </div>

  <!-- Content Structure -->
  <div class="card glow-anim">
    <h3 class="t-grad font-extrabold">Content Structure</h3>
    <div class="grid md:grid-cols-2 gap-6 mt-4">
      <div class="card glow-anim"><div class="text-xs text-slate-300">Title</div><div id="titleVal" class="font-semibold text-slate-100">—</div><div class="text-xs text-slate-300 mt-3">Meta Description</div><div id="metaVal" class="text-slate-200">—</div></div>
      <div class="card glow-anim"><div class="text-xs text-slate-300 mb-2">Heading Map</div><div id="headingMap" class="text-sm space-y-2"></div></div>
    </div>
  </div>

  <!-- Recommendations -->
  <div class="card glow-anim">
    <h3 class="t-grad font-extrabold mb-3">Recommendations</h3>
    <div id="recs" class="grid md:grid-cols-2 gap-3"></div>
  </div>

  <!-- Semantic SEO Ground -->
  <div class="ground-slab glow-anim">
    <div class="flex items-center gap-3 mb-4">
      <div class="glow-anim" style="width:42px;height:42px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(99,102,241,.32),rgba(236,72,153,.32));border:1px solid rgba(255,255,255,.14)">🧭</div>
      <div>
        <div class="t-grad text-2xl font-extrabold">Semantic SEO Ground</div>
        <div class="text-sm text-slate-300">Actionable checklists for structure, quality, UX & entities</div>
      </div>
    </div>
    <div id="cats" class="grid lg:grid-cols-2 gap-6"></div>
  </div>

  <!-- Improve Modal -->
  <dialog id="improveModal" class="rounded-2xl p-0 w-[min(680px,95vw)]">
    <div class="card glow-anim">
      <div class="flex items-start justify-between">
        <h4 id="improveTitle" class="font-semibold text-slate-100">Improve</h4>
        <form method="dialog"><button class="pill">Close</button></form>
      </div>
      <div class="grid md:grid-cols-3 gap-3 mt-4">
        <div class="card glow-anim"><div class="text-xs text-slate-400">Category</div><div id="improveCategory" class="font-semibold">—</div></div>
        <div class="card glow-anim">
          <div class="text-xs text-slate-400">Score</div>
          <div class="flex items-center gap-2 mt-1"><span id="improveScore" class="score-pill">—</span><span id="improveBand" class="pill">—</span></div>
        </div>
        <a id="improveSearch" target="_blank" class="card hover:opacity-90 transition text-center flex items-center justify-center bg-gradient-to-r from-fuchsia-500/20 to-sky-500/20 border border-white/10 glow-anim"><span class="text-sm text-slate-200">Search guidance</span></a>
      </div>
      <div class="mt-4"><div class="text-xs text-slate-400">Why this matters</div><p id="improveWhy" class="text-sm text-slate-200 mt-1">—</p></div>
      <div class="mt-4"><div class="text-xs text-slate-400">How to improve</div><ul id="improveTips" class="mt-2 list-disc pl-5 text-sm text-slate-200 space-y-1"></ul></div>
    </div>
  </dialog>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const $ = s=>document.querySelector(s);

  /* Elements */
  const mw=$('#mw'), mwRing=$('#mwRing'), mwFill=$('#mwFill'), mwNum=$('#mwNum');
  const overallBar=$('#overallBar'), overallFill=$('#overallFill'), overallPct=$('#overallPct');
  const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'), chipHuman=$('#chipHuman'), chipAI=$('#chipAI');

  const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'), pasteBtn=$('#pasteBtn'),
        importBtn=$('#importBtn'), importFile=$('#importFile'), printBtn=$('#printBtn'),
        resetBtn=$('#resetBtn'), exportBtn=$('#exportBtn');

  const statF=$('#statFlesch'), statG=$('#statGrade'), statInt=$('#statInt'), statExt=$('#statExt'), statRatio=$('#statRatio');
  const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap'), recsEl=$('#recs'), catsEl=$('#cats');

  const chipHttp=$('#chipHttp'), chipTitle=$('#chipTitle'), chipMeta=$('#chipMeta'),
        chipCanon=$('#chipCanon'), chipRobots=$('#chipRobots'), chipViewport=$('#chipViewport'),
        chipH=$('#chipH'), chipIntChip=$('#chipInt'), chipSchema=$('#chipSchema'), chipAuto=$('#chipAuto');

  const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
        mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
        mTips=$('#improveTips'), mLink=$('#improveSearch');

  /* Helpers */
  const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
  const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
  const bandIcon=s=>s>=80?'🟢':(s>=60?'🟠':'🔴');
  const pillClassBy=s=>s>=80?'score-pill--green':(s>=60?'score-pill--orange':'score-pill--red');
  const outlineBy=s=>s>=80?'outline-green':(s>=60?'outline-orange':'outline-red');
  const fillBy=s=>s>=80?'fill-green':(s>=60?'fill-orange':'fill-red'); // NEW: fill color selector
  const bandLabel=s=>s>=80?'Good (≥80)':(s>=60?'Needs work (60–79)':'Low (<60)');

  function tipsFor(cat){
    switch(cat){
      case 'Technical Elements':return['Title ~50–60 chars incl. primary keyword.','Meta 140–160 chars + clear CTA.','Set canonical, avoid duplicates.','Ensure in XML sitemap.'];
      case 'Content & Keywords':return['Clear intent & primary topic early.','Use variants/PAA naturally.','Single descriptive H1.','Add short FAQ answers.','Write simple, NLP-friendly language.'];
      case 'Structure & Architecture':return['Logical H2/H3 topic clusters.','Internal links to hub pages.','Clean, descriptive URL.','Enable breadcrumbs (+schema).'];
      case 'Content Quality':return['Show E-E-A-T: author, date, expertise.','Unique value vs competitors.','Cite recent authoritative sources.','Use helpful media with captions.'];
      case 'User Signals & Experience':return['Responsive layout.','Compression & lazy-load.','Watch LCP/INP/CLS.','Clear CTAs.'];
      case 'Entities & Context':return['Define primary entity.','Cover related entities.','Add valid schema (Article/FAQ/Product).','Add sameAs/org details.'];
      default:return['Aim for ≥80 (green) and re-run the analyzer.'];
    }
  }

  function setChip(el,label,value,score){
    if(!el)return; el.classList.remove('good','warn','bad'); const b=bandName(score);
    el.classList.add(b); el.innerHTML=`<i>${bandIcon(score)}</i><span>${label}: ${value}</span>`;
  }

  /* Clipboard + basic actions */
  pasteBtn?.addEventListener('click',async e=>{e.preventDefault();try{const t=await navigator.clipboard.readText();if(t)urlInput.value=t.trim()}catch{}})
  importBtn?.addEventListener('click',()=>importFile.click());
  importFile?.addEventListener('change',e=>{const f=e.target.files?.[0];if(!f)return;const r=new FileReader();r.onload=()=>{try{const j=JSON.parse(String(r.result||'{}'));if(j.url)urlInput.value=j.url;alert('Imported JSON. Click Analyze to run.')}catch{alert('Invalid JSON file.')}};r.readAsText(f)})
  printBtn?.addEventListener('click',()=>window.print());
  resetBtn?.addEventListener('click',()=>location.reload());
  exportBtn?.addEventListener('click',()=>{if(!window.__lastData){alert('Run an analysis first.');return;}const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='semantic-report.json';a.click();URL.revokeObjectURL(a.href)})

  /* API call with fallback */
  async function callAnalyzer(url){
    const headers={'Accept':'application/json','Content-Type':'application/json'};
    let res=await fetch('/api/semantic-analyze',{method:'POST',headers,body:JSON.stringify({url,target_keyword:''})});
    if(res.ok)return res.json();
    if([404,405,419].includes(res.status)){
      res=await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url,target_keyword:''})});
      if(res.ok)return res.json();
    }
    let msg=`HTTP ${res.status}`;try{const j=await res.json();if(j?.error)msg+=' – '+j.error}catch{}throw new Error(msg);
  }

  function setRunning(b){if(!analyzeBtn)return;analyzeBtn.disabled=b;analyzeBtn.style.opacity=b?.6:1;analyzeBtn.textContent=b?'Analyzing…':'🔍 Analyze'}

  analyzeBtn?.addEventListener('click',async e=>{
    e.preventDefault();
    const url=(urlInput.value||'').trim(); if(!url){alert('Please enter a URL.');return;}
    try{
      setRunning(true);
      mwRing?.style.setProperty('--v',0); mwFill?.style.setProperty('--p',0); if(mwNum)mwNum.textContent='0%';
      if(overallFill)overallFill.style.width='0%'; if(overallPct)overallPct.textContent='0%';

      const data=await callAnalyzer(url); if(!data||data.error)throw new Error(data?.error||'Unknown error'); window.__lastData={...data,url};

      /* Overall & chips */
      const score=clamp01(data.overall_score||0), b=bandName(score);
      mw?.classList.remove('good','warn','bad'); mw?.classList.add(b);
      overallBar?.classList.remove('good','warn','bad'); overallBar?.classList.add(b);
      chipOverall?.classList.add(b);
      mwRing?.style.setProperty('--v',score); mwFill?.style.setProperty('--p',score);
      if(mwNum)mwNum.textContent=score+'%'; if(overallFill)overallFill.style.width=score+'%'; if(overallPct)overallPct.textContent=score+'%';
      setChip(chipOverall,'Overall',`${score} /100`,score);

      /* Content score = avg of Content & Keywords and Content Quality */
      const cmap={}; (data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
      const contentScore=Math.round(([cmap['Content & Keywords'],cmap['Content Quality']].filter(v=>typeof v==='number').reduce((a,b)=>a+b,0))/2||0);
      setChip(chipContent,'Content',`${contentScore} /100`,contentScore);

      const r=data.readability||{}, human=clamp01(Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3)), ai=clamp01(100-human);
      setChip(chipWriter,'Writer',human>=60?'Likely Human':'Possibly AI',human);
      setChip(chipHuman,'Human-like',`${human} %`,human);
      setChip(chipAI,'AI-like',`${ai} %`,ai);

      /* Quick stats */
      statF.textContent=r.flesch??'—'; statG.textContent='Grade '+(r.grade??'—');
      statInt.textContent=data.quick_stats?.internal_links??0; statExt.textContent=data.quick_stats?.external_links??0; statRatio.textContent=(data.quick_stats?.text_to_html_ratio??0)+'%';

      /* Structure */
      titleVal.textContent=data.content_structure?.title||'—'; metaVal.textContent=data.content_structure?.meta_description||'—';
      const hs=data.content_structure?.headings||{}; headingMap.innerHTML=''; Object.entries(hs).forEach(([lvl,arr])=>{if(!arr||!arr.length)return;const box=document.createElement('div');box.className='card glow-anim';box.innerHTML=`<div class="text-xs text-slate-300 mb-1 uppercase">${lvl}</div>`+arr.map(t=>`<div>• ${t}</div>`).join('');headingMap.appendChild(box);});

      /* Recommendations */
      recsEl.innerHTML=''; (data.recommendations||[]).forEach(rec=>{const d=document.createElement('div');d.className='card glow-anim';d.innerHTML=`<span class="pill mr-2">${rec.severity}</span>${rec.text}`;recsEl.appendChild(d);});

      /* Ground with Improve button banded FILL + OUTLINE (NEW) */
      catsEl.innerHTML=''; (data.categories||[]).forEach(cat=>{
        const total=(cat.checks||[]).length, passed=(cat.checks||[]).filter(ch=>(ch.score||0)>=80).length, pct=Math.round((passed/Math.max(1,total))*100);
        const card=document.createElement('div'); card.className='cat-card glow-anim';
        card.innerHTML=`<div class="cat-head"><div class="flex items-center gap-3">
            <div class="glow-anim" style="width:38px;height:38px;border-radius:10px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(99,102,241,.25),rgba(236,72,153,.25));border:1px solid rgba(255,255,255,.12)">★</div>
            <div><div class="cat-title">${cat.name}</div><div class="text-slate-300 text-sm">Keep improving</div></div></div>
            <div class="pill">${passed} / ${total}</div></div>
            <div class="progress mb-3"><span style="width:${pct}%"></span></div>
            <div class="space-y-2" id="list"></div>`;
        const list=card.querySelector('#list');
        (cat.checks||[]).forEach(ch=>{
          const outline=outlineBy(ch.score||0), fill=fillBy(ch.score||0);
          const color=(ch.score||0)>=80?'#10b981':(ch.score||0)>=60?'#f59e0b':'#ef4444';
          const row=document.createElement('div'); row.className='check glow-anim';
          row.innerHTML=`<div class="flex items-center gap-3"><span class="w-3 h-3 rounded-full" style="background:${color}"></span><div class="font-semibold">${ch.label}</div></div>
                         <div class="flex items-center gap-2"><span class="score-pill ${pillClassBy(ch.score||0)}">${ch.score??'—'}</span>
                         <button class="improve-btn ${fill} ${outline}" type="button">Improve</button></div>`;
          row.querySelector('.improve-btn').addEventListener('click',()=>{
            mTitle.textContent=ch.label; mCat.textContent=cat.name; mScore.textContent=ch.score??'—';
            mBand.textContent=bandLabel(ch.score||0); mBand.className='pill '+pillClassBy(ch.score||0);
            mWhy.textContent=ch.why||'This item affects topical authority, UX, and rich-result eligibility.';
            mTips.innerHTML=''; (ch.tips||tipsFor(cat.name)).forEach(t=>{const li=document.createElement('li');li.textContent=t;mTips.appendChild(li);});
            mLink.href=ch.improve_search_url||('https://www.google.com/search?q='+encodeURIComponent(ch.label+' SEO best practices'));
            if(typeof modal.showModal==='function')modal.showModal();else modal.setAttribute('open','');
          });
          list.appendChild(row);
        });
        catsEl.appendChild(card);
      });

      /* Status chips */
      chipTitle.textContent=(data.content_structure?.title||'').length||0;
      chipMeta.textContent=(data.content_structure?.meta_description||'').length||0;
      try{chipCanon.textContent=new URL(url).origin}catch{chipCanon.textContent='—'}
      chipRobots.textContent='—'; chipViewport.textContent='—';
      chipH.textContent=`H1:${(hs.H1||[]).length} • H2:${(hs.H2||[]).length} • H3:${(hs.H3||[]).length}`;
      chipIntChip.textContent=data.quick_stats?.internal_links??0;
      chipSchema.textContent=data.schema_count??0;
      chipAuto.textContent=(data.categories||[]).flatMap(c=>c.checks||[]).filter(x=>(x.score||0)>=80).length;
      chipHttp.textContent='200';
    }catch(err){console.error(err);alert('Analyze failed: '+err.message)}finally{setRunning(false)}
  });

  /* Close modal by backdrop click */
  const modal=$('#improveModal');
  modal?.addEventListener('click',e=>{const r=modal.getBoundingClientRect();const inside=(e.clientX>=r.left&&e.clientX<=r.right&&e.clientY>=r.top&&e.clientY<=r.bottom);if(!inside){if(typeof modal.close==='function')modal.close();else modal.removeAttribute('open');}})
});
</script>
@endpush
@endsection
