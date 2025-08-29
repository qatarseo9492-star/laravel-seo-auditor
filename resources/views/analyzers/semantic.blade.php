@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* ---- Base ------------------------------------------------- */
  html,body{background:#04021c!important;color:#e5e7eb}
  .maxw{max-width:1150px;margin:0 auto}
  .title-wrap{display:flex;align-items:center;gap:14px;justify-content:center;margin-top:14px}
  .king{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:#101018;border:1px solid #ffffff24}
  .t-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900}
  .byline{font-size:14px;color:#cbd5e1}
  .shoail{display:inline-block;background:linear-gradient(90deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;background-size:400% 100%;animation:rainbowSlide 6s linear infinite,bob 3s ease-in-out infinite}
  @keyframes rainbowSlide{to{background-position:100% 50%}} @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  /* Legend */
  .legend{display:flex;gap:10px;justify-content:center;margin:10px 0 6px}
  .legend .badge{padding:6px 10px;border-radius:9999px;font-weight:800;border:1px solid #ffffff2a;font-size:12px}
  .legend .g{background:#063f2c;color:#a7f3d0;border-color:#10b98166}
  .legend .o{background:#3b2a05;color:#fde68a;border-color:#f59e0b66}
  .legend .r{background:#3a0b0b;color:#fecaca;border-color:#ef444466}

  /* Cards */
  .card{border-radius:18px;padding:18px;background:#0a0a14;border:1px solid #ffffff1c}
  .cat-card{border-radius:16px;padding:16px;background:#111E2F;border:1px solid #ffffff1c}
  .ground-slab{border-radius:22px;padding:20px;background:#0D0E1E;border:1px solid #ffffff1c;margin-top:20px}

  /* Chips / pills / buttons (compact) */
  .pill{padding:5px 10px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid #ffffff29;background:#ffffff14;color:#e5e7eb}
  .chip{padding:6px 8px;border-radius:12px;font-weight:800;display:inline-flex;align-items:center;gap:6px;border:1px solid #ffffff24;color:#eef2ff;font-size:12px}
  .chip i{font-style:normal}
  .chip.good{background:linear-gradient(135deg,#22c55e45,#10b98122);border-color:#22c55e72}
  .chip.warn{background:linear-gradient(135deg,#f59e0b45,#facc1522);border-color:#f59e0b72}
  .chip.bad{background:linear-gradient(135deg,#ef444445,#f8717122);border-color:#ef444472}

  .btn{padding:10px 14px;border-radius:12px;font-weight:900;border:1px solid #ffffff22;color:#0b1020;font-size:13px}
  .btn-green{background:#22c55e}.btn-blue{background:#3b82f6}.btn-orange{background:#f59e0b}.btn-purple{background:linear-gradient(90deg,#a78bfa,#f472b6);color:#19041a}
  .url-row{display:flex;align-items:center;gap:10px;border:1px solid #ffffff24;background:#0b0b12;border-radius:12px;padding:8px 10px}
  .url-row input{background:transparent;border:none;outline:none;color:#e5e7eb;width:100%}
  .url-row .paste{padding:6px 10px;border-radius:10px;border:1px solid #ffffff26;background:#ffffff10;color:#e5e7eb}

  .analyze-wrap{border-radius:16px;background:#020114;border:1px solid #ffffff20;box-shadow:inset 0 0 0 1px #ffffff0a;padding:12px}

  /* Score wheel (small) */
  .mw{--v:0;--ring:#f59e0b;--p:0;width:200px;height:200px;position:relative}
  .mw-ring{position:absolute;inset:0;border-radius:50%;background:conic-gradient(var(--ring) calc(var(--v)*1%),#ffffff14 0);-webkit-mask:radial-gradient(circle 76px,transparent 72px,#000 72px);mask:radial-gradient(circle 76px,transparent 72px,#000 72px)}
  .mw-fill{position:absolute;inset:18px;border-radius:50%;overflow:hidden;background:#000}
  .mw-fill::after{content:"";position:absolute;left:0;right:0;height:100%;top:calc(100% - var(--p)*1%);transition:top .9s ease;background:var(--fill,linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%));-webkit-mask:radial-gradient(105px 16px at 50% 0,#0000 98%,#000 100%);mask:radial-gradient(105px 16px at 50% 0,#0000 98%,#000 100%)}
  .mw.good{--ring:#22c55e;--fill:linear-gradient(to top,#16a34a 0%,#22c55e 60%,#86efac 100%)} .mw.warn{--ring:#f59e0b;--fill:linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%)} .mw.bad{--ring:#ef4444;--fill:linear-gradient(to top,#ef4444 0%,#f87171 60%,#fecaca 100%)}
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:34px;font-weight:900;color:#fff;text-shadow:0 6px 22px rgba(0,0,0,.45)}

  /* Water bar */
  .waterbox{position:relative;height:16px;border-radius:9999px;overflow:hidden;border:1px solid #ffffff22;background:#0b0b12}
  .waterbox .fill{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .waterbox.good .fill{background:linear-gradient(90deg,#16a34a,#22c55e,#86efac)} .waterbox.warn .fill{background:linear-gradient(90deg,#f59e0b,#fbbf24,#fde68a)} .waterbox.bad .fill{background:linear-gradient(90deg,#ef4444,#f87171,#fecaca)}
  .waterbox .label{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e5e7eb;font-size:11px}

  /* Progress & check rows */
  .progress{width:100%;height:10px;border-radius:9999px;background:#ffffff14;overflow:hidden;border:1px solid #ffffff1a}
  .progress>span{display:block;height:100%;border-radius:9999px;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e);transition:width .5s ease}
  .check{display:flex;align-items:center;justify-content:space-between;border-radius:12px;padding:10px 12px;border:1px solid #ffffff1a;background:#0F1A29}
  .score-pill{padding:3px 7px;border-radius:10px;font-weight:800;background:#ffffff14;border:1px solid #ffffff22;color:#e5e7eb;font-size:12px}
  .score-pill--green{background:#10b9812e;border-color:#10b98166;color:#bbf7d0}.score-pill--orange{background:#f59e0b2e;border-color:#f59e0b66;color:#fde68a}.score-pill--red{background:#ef44442e;border-color:#ef444466;color:#fecaca}
  .improve-btn{padding:6px 9px;border-radius:10px;color:#0b1020;font-weight:800;border:1px solid transparent;transition:transform .08s ease;font-size:12px}
  .improve-btn:active{transform:translateY(1px)}
  .fill-green {background:linear-gradient(135deg,#16a34a,#22c55e,#86efac);color:#05240f}
  .fill-orange{background:linear-gradient(135deg,#f59e0b,#fbbf24,#fde68a);color:#3a2400}
  .fill-red   {background:linear-gradient(135deg,#ef4444,#f87171,#fecaca);color:#2f0606}
  .outline-green{border-color:#22c55edd!important;box-shadow:0 0 0 2px #22c55e8c inset,0 0 16px #22c55e55}
  .outline-orange{border-color:#f59e0bdd!important;box-shadow:0 0 0 2px #f59e0b8c inset,0 0 16px #f59e0b55}
  .outline-red{border-color:#ef4444dd!important;box-shadow:0 0 0 2px #ef44448c inset,0 0 16px #ef444455}

  /* Modal */
  dialog[open]{display:block} dialog::backdrop{background:rgba(0,0,0,.6)}
  #improveModal .card{background:#0D0E1E;border:1px solid #1b2640}
  #improveModal .card .card{background:#111E2F;border-color:#ffffff1c}

  /* Error box */
  #errorBox{display:none;margin-top:10px;border:1px solid #ef444466;background:#3a0b0b;color:#fecaca;border-radius:12px;padding:10px;white-space:pre-wrap;font-size:12px}
</style>
@endpush

@section('content')
<section class="maxw px-4 pb-10">

  {{-- Title --}}
  <div class="title-wrap">
    <div class="king">👑</div>
    <div style="text-align:center">
      <div class="t-grad" style="font-size:26px;line-height:1.1;">Semantic SEO Master Analyzer</div>
      <div class="byline">By <span class="shoail">Shoail Kahoker</span></div>
    </div>
  </div>

  {{-- Legend --}}
  <div class="legend">
    <span class="badge g">Green ≥ 80</span><span class="badge o">Orange 60–79</span><span class="badge r">Red &lt; 60</span>
  </div>

  {{-- Wheel + chips (small) --}}
  <div style="display:grid;grid-template-columns:230px 1fr;gap:16px;align-items:center;margin-top:10px">
    <div style="display:grid;place-items:center;border-radius:16px;padding:8px;background:#090916;border:1px solid #ffffff12">
      <div class="mw warn" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-fill" id="mwFill" style="--p:0"></div>
        <div class="mw-center" id="mwNum">0%</div>
      </div>
    </div>
    <div class="space-y-2">
      <div style="display:flex;flex-wrap:wrap;gap:6px">
        <span id="chipOverall" class="chip warn"><i>🟧</i><span>Overall: 0 /100</span></span>
        <span id="chipContent" class="chip warn"><i>🟧</i><span>Content: —</span></span>
        <span id="chipWriter"  class="chip"><i>🟧</i><span>Writer: —</span></span>
        <span id="chipHuman"   class="chip"><i>🟧</i><span>Human-like: — %</span></span>
        <span id="chipAI"      class="chip"><i>🟧</i><span>AI-like: — %</span></span>
      </div>
      <div id="overallBar" class="waterbox warn">
        <div class="fill" id="overallFill" style="width:0%"></div>
        <div class="label"><span id="overallPct">0%</span></div>
      </div>
    </div>
  </div>

  {{-- Analyze toolbar (under wheel) --}}
  <div class="analyze-wrap" style="margin-top:12px;">
    <div class="url-row">
      <span style="opacity:.75">🌐</span>
      <input id="urlInput" name="url" type="url" placeholder="https://example.com/page" />
      <button id="pasteBtn" type="button" class="paste">Paste</button>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-top:10px">
      <label style="display:flex;align-items:center;gap:8px;font-size:12px">
        <input id="autoCheck" type="checkbox" class="accent-emerald-400" checked/> Auto-apply checkmarks (≥ 80)
      </label>
      <div style="flex:1"></div>
      <input id="importFile" type="file" accept="application/json" style="display:none"/>
      <button id="importBtn" type="button" class="btn btn-purple">⇪ Import</button>
      <button id="analyzeBtn" type="button" class="btn btn-green">🔍 Analyze</button>
      <button id="printBtn"   type="button" class="btn btn-blue">🖨️ Print</button>
      <button id="resetBtn"   type="button" class="btn btn-orange">↻ Reset</button>
      <button id="exportBtn"  type="button" class="btn btn-purple">⬇︎ Export</button>
    </div>

    <div id="errorBox"></div>

    <div id="statusChips" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px">
      <div class="chip" id="chipHttpWrap"><span class="t-grad">HTTP:</span>&nbsp;<span id="chipHttp">—</span></div>
      <div class="chip" id="chipTitleWrap"><span class="t-grad">Title:</span>&nbsp;<span id="chipTitle">—</span></div>
      <div class="chip" id="chipMetaWrap"><span class="t-grad">Meta desc:</span>&nbsp;<span id="chipMeta">—</span></div>
      <div class="chip" id="chipCanonWrap"><span class="t-grad">Canonical:</span>&nbsp;<span id="chipCanon">—</span></div>
      <div class="chip" id="chipRobotsWrap"><span class="t-grad">Robots:</span>&nbsp;<span id="chipRobots">—</span></div>
      <div class="chip" id="chipViewportWrap"><span class="t-grad">Viewport:</span>&nbsp;<span id="chipViewport">—</span></div>
      <div class="chip"><span class="t-grad">H1/H2/H3:</span>&nbsp;<span id="chipH">—</span></div>
      <div class="chip"><span class="t-grad">Internal links:</span>&nbsp;<span id="chipInt">—</span></div>
      <div class="chip"><span class="t-grad">Schema:</span>&nbsp;<span id="chipSchema">—</span></div>
      <div class="chip"><span class="t-grad">Auto-checked:</span>&nbsp;<span id="chipAuto">0</span></div>
    </div>
  </div>

  {{-- Quick Stats --}}
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Quick Stats</h3>
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px">
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Readability (Flesch)</div><div id="statFlesch" style="font-size:20px;font-weight:800">—</div><div id="statGrade" style="font-size:12px;color:#94a3b8">—</div></div>
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Links (int / ext)</div><div style="font-size:20px;font-weight:800"><span id="statInt">0</span> / <span id="statExt">0</span></div></div>
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Text/HTML Ratio</div><div id="statRatio" style="font-size:20px;font-weight:800">—</div></div>
    </div>
  </div>

  {{-- Content Structure --}}
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Content Structure</h3>
    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px">
      <div class="card">
        <div style="font-size:12px;color:#b6c2cf">Title</div>
        <div id="titleVal" style="font-weight:600">—</div>
        <div style="font-size:12px;color:#b6c2cf;margin-top:10px">Meta Description</div>
        <div id="metaVal" style="color:#e5e7eb">—</div>
      </div>
      <div class="card">
        <div style="font-size:12px;color:#b6c2cf;margin-bottom:6px">Heading Map</div>
        <div id="headingMap" class="text-sm space-y-2"></div>
      </div>
    </div>
  </div>

  {{-- Recommendations --}}
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Recommendations</h3>
    <div id="recs" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px"></div>
  </div>

  {{-- Semantic SEO Ground --}}
  <div class="ground-slab">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
      <div class="king">🧭</div>
      <div>
        <div class="t-grad" style="font-weight:900;font-size:18px">Semantic SEO Ground</div>
        <div style="font-size:12px;color:#b6c2cf">Actionable checklists for structure, quality, UX & entities</div>
      </div>
    </div>
    <div id="cats" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px"></div>
  </div>

  {{-- Improve Modal --}}
  <dialog id="improveModal" class="rounded-2xl p-0 w-[min(680px,95vw)]" style="border:none;border-radius:16px">
    <div class="card">
      <div style="display:flex;align-items:start;justify-content:space-between;gap:10px">
        <h4 id="improveTitle" class="t-grad" style="font-weight:900;margin:0">Improve</h4>
        <form method="dialog"><button class="pill">Close</button></form>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:8px">
        <div class="card"><div style="font-size:12px;color:#94a3b8">Category</div><div id="improveCategory" style="font-weight:700">—</div></div>
        <div class="card">
          <div style="font-size:12px;color:#94a3b8">Score</div>
          <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
            <span id="improveScore" class="score-pill">—</span>
            <span id="improveBand" class="pill">—</span>
          </div>
        </div>
        <a id="improveSearch" target="_blank" class="card" style="text-align:center;display:flex;align-items:center;justify-content:center;background:linear-gradient(90deg,#f472b626,#22d3ee26);border:1px solid #ffffff22;text-decoration:none">
          <span style="font-size:13px;color:#e5e7eb">Search guidance</span>
        </a>
      </div>
      <div style="margin-top:10px">
        <div style="font-size:12px;color:#94a3b8">Why this matters</div>
        <p id="improveWhy" style="font-size:14px;color:#e5e7eb;margin-top:6px">—</p>
      </div>
      <div style="margin-top:10px">
        <div style="font-size:12px;color:#94a3b8">How to improve</div>
        <ul id="improveTips" style="margin-top:8px;padding-left:18px;display:grid;gap:6px;font-size:14px;color:#e5e7eb"></ul>
      </div>
    </div>
  </dialog>

</section>
@endsection

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
  const chipHttpWrap=$('#chipHttpWrap'), chipTitleWrap=$('#chipTitleWrap'), chipMetaWrap=$('#chipMetaWrap');

  const errorBox = $('#errorBox');

  const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
        mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
        mTips=$('#improveTips'), mLink=$('#improveSearch');

  /* Helpers */
  const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
  const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
  const bandIcon=s=>s>=80?'✅':(s>=60?'🟧':'🔴');
  const pillClassBy=s=>s>=80?'score-pill--green':(s>=60?'score-pill--orange':'score-pill--red');
  const fillBy=s=>s>=80?'fill-green':(s>=60?'fill-orange':'fill-red');
  const outlineBy=s=>s>=80?'outline-green':(s>=60?'outline-orange':'outline-red');
  const bandLabel=s=>s>=80?'Good (≥80)':(s>=60?'Needs work (60–79)':'Low (<60)');

  function setChip(el,label,value,score){
    if(!el)return;
    el.classList.remove('good','warn','bad');
    const b=bandName(score);
    el.classList.add(b);
    el.innerHTML=`<i>${bandIcon(score)}</i><span>${label}: ${value}</span>`;
  }

  function showError(msg, detail) {
    errorBox.style.display = 'block';
    errorBox.textContent = msg + (detail ? "\n\n" + detail : '');
  }
  function clearError(){ errorBox.style.display='none'; errorBox.textContent=''; }

  /* Paste/import/print/reset/export */
  pasteBtn?.addEventListener('click',async e=>{e.preventDefault();try{const t=await navigator.clipboard.readText();if(t)urlInput.value=t.trim()}catch{}})
  importBtn?.addEventListener('click',()=>importFile.click());
  importFile?.addEventListener('change',e=>{const f=e.target.files?.[0];if(!f)return;const r=new FileReader();r.onload=()=>{try{const j=JSON.parse(String(r.result||'{}'));if(j.url)urlInput.value=j.url;alert('Imported JSON. Click Analyze to run.')}catch{alert('Invalid JSON file.')}};r.readAsText(f)})
  printBtn?.addEventListener('click',()=>window.print());
  resetBtn?.addEventListener('click',()=>location.reload());
  exportBtn?.addEventListener('click',()=>{if(!window.__lastData){alert('Run an analysis first.');return;}const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='semantic-report.json';a.click();URL.revokeObjectURL(a.href)})

  /* ---- Knowledge base for Improve modal ---- */
  const KB = {
    // User Signals & Experience
    'Mobile-friendly, responsive layout': {why:'Most traffic is mobile; poor UX kills engagement.', tips:['Responsive breakpoints & fluid grids.','Tap targets ≥44px.','Avoid horizontal scroll.'], link:'https://search.google.com/test/mobile-friendly'},
    'Optimized speed (compression, lazy-load)': {why:'Speed affects abandonment and Core Web Vitals.', tips:['Use modern formats (WebP/AVIF).','HTTP/2 + caching/CDN.','Lazy-load below-the-fold media.'], link:'https://web.dev/fast/'},
    'Core Web Vitals passing (LCP/INP/CLS)': {why:'Passing CWV improves experience and stability.', tips:['Preload hero image.','Minimize long JS tasks.','Reserve image/video space.'], link:'https://web.dev/vitals/'},
    'Clear CTAs and next steps': {why:'Clarity increases conversions and task completion.', tips:['One primary CTA per view.','Action verbs + benefit.','Explain what happens next.'], link:'https://www.nngroup.com/articles/call-to-action-buttons/'},

    // Entities & Context
    'sameAs/Organization details present': {why:'Entity grounding disambiguates your brand.', tips:['Organization JSON-LD.','Include sameAs links.','Ensure NAP consistency.'], link:'https://schema.org/Organization'},
    'Valid schema markup (Article/FAQ/Product)': {why:'Structured data unlocks rich results.', tips:['Validate with Rich Results Test.','Only mark visible content.','Keep to supported types.'], link:'https://search.google.com/test/rich-results'},
    'Related entities covered with context': {why:'Covering related entities builds topical depth.', tips:['Mention core related concepts.','Explain relationships.','Link to reference pages.'], link:'https://developers.google.com/knowledge-graph'},
    'Primary entity clearly defined': {why:'A single main entity clarifies page purpose.', tips:['Define at the top.','Use consistent naming.','Add schema about it.'], link:'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data'},

    // Structure & Architecture
    'Logical H2/H3 headings & topic clusters': {why:'Hierarchy helps skimming and indexing.', tips:['Group related subtopics under H2.','Use H3 for steps/examples.','Keep sections 150–300 words.'], link:'https://moz.com/learn/seo/site-structure'},
    'Internal links to hub/related pages': {why:'Internal links distribute authority & context.', tips:['Link to 3–5 relevant hubs.','Use descriptive anchors.','Add “Further reading”.'], link:'https://ahrefs.com/blog/internal-links/'},
    'Clean, descriptive URL slug': {why:'Readable slugs improve CTR and clarity.', tips:['3–5 meaningful words.','Hyphens & lowercase.','Avoid dates unless needed.'], link:'https://developers.google.com/search/docs/crawling-indexing/url-structure'},
    'Breadcrumbs enabled (+ schema)': {why:'Breadcrumbs clarify location and may show in SERP.', tips:['Visible breadcrumbs.','BreadcrumbList JSON-LD.','Keep depth logical.'], link:'https://developers.google.com/search/docs/appearance/structured-data/breadcrumb'},

    // Content Quality
    'E-E-A-T signals (author, date, expertise)': {why:'Trust signals reduce bounce and build credibility.', tips:['Author bio + credentials.','Last updated date.','Editorial policy page.'], link:'https://developers.google.com/search/blog/2022/08/helpful-content-update'},
    'Unique value vs. top competitors': {why:'Differentiation is necessary to rank and retain.', tips:['Original data/examples.','Pros/cons & decision criteria.','Why your approach is better.'], link:'https://backlinko.com/seo-techniques'},
    'Facts & citations up to date': {why:'Freshness + accuracy boosts trust.', tips:['Cite primary sources.','Update stats ≤12 months.','Prefer canonical/DOI links.'], link:'https://scholar.google.com/'},
    'Helpful media (images/video) w/ captions': {why:'Media breaks walls of text and aids clarity.', tips:['Add 3–6 figures.','Descriptive captions.','Compress + lazy-load.'], link:'https://web.dev/optimize-lcp/'},

    // Content & Keywords
    'Define search intent & primary topic': {why:'Matching intent drives relevance & dwell time.', tips:['State the outcome early.','Align format to intent.','Use concrete examples.'], link:'https://ahrefs.com/blog/search-intent/'},
    'Map target & related keywords (synonyms/PAA)': {why:'Variants improve recall and completeness.', tips:['List 6–12 variants.','5–10 PAA questions.','Answer PAA in 40–60 words.'], link:'https://developers.google.com/search/docs/fundamentals/seo-starter-guide'},
    'H1 includes primary topic naturally': {why:'Clear page topic helps users and algorithms.', tips:['One H1 per page.','Put topic near the start.','Be descriptive, not clickbait.'], link:'https://web.dev/learn/html/semantics/#headings'},
    'Integrate FAQs / questions with answers': {why:'Captures long-tail & can earn rich results.', tips:['Pick 3–6 questions.','Answer briefly.','Add FAQPage JSON-LD.'], link:'https://developers.google.com/search/docs/appearance/structured-data/faqpage'},
    'Readable, NLP-friendly language': {why:'Plain, direct writing improves comprehension.', tips:['≤20 words/sentence.','Active voice.','Define jargon on first use.'], link:'https://www.plainlanguage.gov/guidelines/'},

    // Technical Elements
    'Title tag (≈50–60 chars) w/ primary keyword': {why:'Title remains the strongest on-page signal.', tips:['50–60 chars.','Primary topic first.','Avoid truncation & duplication.'], link:'https://moz.com/learn/seo/title-tag'},
    'Meta description (≈140–160 chars) + CTA': {why:'Meta drives CTR which correlates with rankings.', tips:['140–160 chars.','Benefit + soft CTA.','Match intent.'], link:'https://moz.com/learn/seo/meta-description'},
    'Canonical tag set correctly': {why:'Avoid duplicates and consolidate signals.', tips:['One canonical.','Point to preferred URL.','No conflicting canonicals.'], link:'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls'},
    'Indexable & listed in XML sitemap': {why:'Indexation is prerequisite to ranking.', tips:['Not blocked by robots.','In XML sitemap.','Submit in Search Console.'], link:'https://developers.google.com/search/docs/crawling-indexing/overview'},
  };

  /* EXACT default categories you requested */
  const DEFAULT_CATS = [
    { name:'User Signals & Experience', checks:[
      {label:'Mobile-friendly, responsive layout'},
      {label:'Optimized speed (compression, lazy-load)'},
      {label:'Core Web Vitals passing (LCP/INP/CLS)'},
      {label:'Clear CTAs and next steps'}
    ]},
    { name:'Entities & Context', checks:[  // (Your second block content)
      {label:'sameAs/Organization details present'},
      {label:'Valid schema markup (Article/FAQ/Product)'},
      {label:'Related entities covered with context'},
      {label:'Primary entity clearly defined'}
    ]},
    { name:'Structure & Architecture', checks:[
      {label:'Logical H2/H3 headings & topic clusters'},
      {label:'Internal links to hub/related pages'},
      {label:'Clean, descriptive URL slug'},
      {label:'Breadcrumbs enabled (+ schema)'}
    ]},
    { name:'Content Quality', checks:[
      {label:'E-E-A-T signals (author, date, expertise)'},
      {label:'Unique value vs. top competitors'},
      {label:'Facts & citations up to date'},
      {label:'Helpful media (images/video) w/ captions'}
    ]},
    { name:'Content & Keywords', checks:[
      {label:'Define search intent & primary topic'},
      {label:'Map target & related keywords (synonyms/PAA)'},
      {label:'H1 includes primary topic naturally'},
      {label:'Integrate FAQs / questions with answers'},
      {label:'Readable, NLP-friendly language'}
    ]},
    { name:'Technical Elements', checks:[
      {label:'Title tag (≈50–60 chars) w/ primary keyword'},
      {label:'Meta description (≈140–160 chars) + CTA'},
      {label:'Canonical tag set correctly'},
      {label:'Indexable & listed in XML sitemap'}
    ]},
  ];

  /* Merge helper: ensure API result contains ALL default checks */
  function ensureAllDefaults(apiCats){
    const byName = new Map((apiCats||[]).map(c=>[c.name,c]));
    DEFAULT_CATS.forEach(def=>{
      if(!byName.has(def.name)){
        // missing category entirely -> add default
        byName.set(def.name, JSON.parse(JSON.stringify(def)));
      }else{
        // merge checks: add any missing labels
        const existing = byName.get(def.name);
        existing.checks = existing.checks || [];
        const have = new Set(existing.checks.map(x=>x.label));
        def.checks.forEach(ch=>{
          if(!have.has(ch.label)){
            existing.checks.push({label: ch.label, score: ch.score ?? undefined});
          }
        });
      }
    });
    return Array.from(byName.values());
  }

  function enrichCheck(check, catName){
    const kb = KB[check.label] || {};
    return {
      ...check,
      why: check.why || kb.why || 'This factor influences relevance, UX, and eligibility for rich results.',
      tips: (check.tips && check.tips.length ? check.tips : (kb.tips || ['Aim for ≥80 (green) and re-run the analyzer.'])),
      improve_search_url: check.improve_search_url || kb.link || ('https://www.google.com/search?q='+encodeURIComponent(check.label+' best practices')),
      _cat: catName
    };
  }

  /* API call with fallback */
  async function callAnalyzer(url){
    const headers={'Accept':'application/json','Content-Type':'application/json'};
    let res=await fetch('/api/semantic-analyze',{method:'POST',headers,body:JSON.stringify({url,target_keyword:''})});
    if(res.ok)return res.json();
    if([404,405,419].includes(res.status)){
      res=await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url,target_keyword:''})});
      if(res.ok)return res.json();
    }
    const txt=await res.text();
    throw new Error(`HTTP ${res.status}\n${txt?.slice(0,800)}`);
  }

  function setRunning(isOn){
    if(!analyzeBtn)return;
    analyzeBtn.disabled = isOn;
    analyzeBtn.style.opacity = isOn ? .6 : 1;
    analyzeBtn.textContent = isOn ? 'Analyzing…' : '🔍 Analyze';
  }

  /* Analyze click */
  analyzeBtn?.addEventListener('click', async e=>{
    e.preventDefault();
    clearError();
    const url=(urlInput.value||'').trim();
    if(!url){showError('Please enter a URL.');return;}
    try{
      setRunning(true);

      // reset visuals
      mwRing?.style.setProperty('--v',0); mwFill?.style.setProperty('--p',0); mw?.classList.remove('good','warn','bad'); mw?.classList.add('warn');
      if(mwNum)mwNum.textContent='0%';
      if(overallFill)overallFill.style.width='0%'; if(overallPct)overallPct.textContent='0%';
      chipOverall.classList.remove('good','warn','bad'); chipOverall.classList.add('warn');

      const data=await callAnalyzer(url);
      if(!data || data.error) throw new Error(data?.error || 'Unknown error');
      window.__lastData = {...data, url};

      /* Overall & chips */
      const score = clamp01(data.overall_score||0), band=bandName(score);
      mw?.classList.remove('good','warn','bad'); mw?.classList.add(band);
      mwRing?.style.setProperty('--v',score); mwFill?.style.setProperty('--p',score);
      if(mwNum)mwNum.textContent=score+'%';
      overallBar?.classList.remove('good','warn','bad'); overallBar?.classList.add(band);
      overallFill.style.width=score+'%'; overallPct.textContent=score+'%';
      setChip(chipOverall,'Overall',`${score} /100`,score);

      /* Content score = avg(Content & Keywords, Content Quality) */
      const cmap={}; (data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
      const contentScore = Math.round(([cmap['Content & Keywords'], cmap['Content Quality']].filter(v=>typeof v==='number').reduce((a,b)=>a+b,0))/2 || 0);
      setChip(chipContent,'Content',`${contentScore} /100`,contentScore);

      /* Writer/Human/AI (heuristic) */
      const r=data.readability||{};
      const human = clamp01(Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3));
      const ai    = clamp01(100-human);
      setChip(chipWriter,'Writer', human>=60?'Likely Human':'Possibly AI', human);
      setChip(chipHuman,'Human-like', `${human} %`, human);
      setChip(chipAI, 'AI-like', `${ai} %`, 100-human);

      /* Quick stats */
      statF.textContent=r.flesch??'—';
      statG.textContent='Grade '+(r.grade??'—');
      statInt.textContent=data.quick_stats?.internal_links??0;
      statExt.textContent=data.quick_stats?.external_links??0;
      statRatio.textContent=(data.quick_stats?.text_to_html_ratio??0)+'%';

      /* Structure */
      const title = data.content_structure?.title || '';
      const meta  = data.content_structure?.meta_description || '';
      titleVal.textContent=title||'—';
      metaVal.textContent=meta||'—';

      const hs=data.content_structure?.headings||{};
      chipH.textContent=`H1:${(hs.H1||[]).length} • H2:${(hs.H2||[]).length} • H3:${(hs.H3||[]).length}`;
      headingMap.innerHTML='';
      Object.entries(hs).forEach(([lvl,arr])=>{
        if(!arr||!arr.length)return;
        const box=document.createElement('div'); box.className='card';
        box.innerHTML=`<div style="font-size:12px;color:#b6c2cf;margin-bottom:6px" class="uppercase">${lvl}</div>`+arr.map(t=>`<div>• ${t}</div>`).join('');
        headingMap.appendChild(box);
      });

      /* Status chips coloring */
      function setBandChip(wrapperEl, score){
        if(!wrapperEl) return;
        wrapperEl.classList.remove('good','warn','bad');
        wrapperEl.classList.add(bandName(score));
      }
      chipHttp.textContent='200'; setBandChip(chipHttpWrap, 90);
      const tl = title.length; chipTitle.textContent = tl ? (tl+' chars') : '—'; setBandChip(chipTitleWrap, (tl>=50&&tl<=60)?85:(tl?65:40));
      const ml = meta.length;  chipMeta.textContent  = ml ? (ml+' chars') : '—'; setBandChip(chipMetaWrap, (ml>=140&&ml<=160)?85:(ml?65:40));
      try{chipCanon.textContent=new URL(url).origin}catch{chipCanon.textContent='—'}
      chipRobots.textContent='—'; chipViewport.textContent='—';
      chipIntChip.textContent=data.quick_stats?.internal_links??0;
      chipSchema.textContent=(data.schema_count ?? (data.quick_stats?.schema_count ?? '—'));
      chipAuto.textContent=(data.categories||[]).flatMap(c=>c.checks||[]).filter(x=>(x.score||0)>=80).length;

      /* Recommendations */
      recsEl.innerHTML='';
      (data.recommendations||[]).forEach(rec=>{
        const d=document.createElement('div'); d.className='card';
        d.innerHTML=`<span class="pill" style="margin-right:6px">${rec.severity}</span>${rec.text}`;
        recsEl.appendChild(d);
      });

      /* Categories + checks: ensure every requested item exists */
      let cats = ensureAllDefaults(data.categories||[]);
      // Enrich + render
      catsEl.innerHTML='';
      cats.forEach(cat=>{
        const ck = (cat.checks||[]).map(c=>enrichCheck(c, cat.name));
        const total = ck.length;
        const passed = ck.filter(x=>(x.score||0)>=80).length;
        const pct = Math.round((passed/Math.max(1,total))*100);

        const card=document.createElement('div'); card.className='cat-card';
        card.innerHTML=`<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
            <div style="display:flex;align-items:center;gap:8px">
              <div class="king" style="width:34px;height:34px">★</div>
              <div>
                <div class="t-grad" style="font-size:16px;font-weight:900">${cat.name}</div>
                <div style="font-size:12px;color:#b6c2cf">Keep improving</div>
              </div>
            </div>
            <div class="pill">${passed} / ${total}</div>
          </div>
          <div class="progress" style="margin-bottom:8px"><span style="width:${pct}%"></span></div>
          <div class="space-y-2" id="list"></div>`;
        const list = card.querySelector('#list');

        ck.forEach(ch=>{
          const s = Number(ch.score ?? 60);
          const fill = fillBy(s), outline = outlineBy(s);
          const pill = s>=80 ? 'score-pill--green' : s>=60 ? 'score-pill--orange' : 'score-pill--red';
          const dot  = s>=80 ? '#10b981' : s>=60 ? '#f59e0b' : '#ef4444';

          const row=document.createElement('div'); row.className='check';
          row.innerHTML = `
            <div style="display:flex;align-items:center;gap:8px">
              <span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:${dot}"></span>
              <div class="font-semibold" style="font-size:13px">${ch.label}</div>
            </div>
            <div style="display:flex;align-items:center;gap:6px">
              <span class="score-pill ${pill}">${isFinite(s)?s:'—'}</span>
              <button class="improve-btn ${fill} ${outline}" type="button">Improve</button>
            </div>`;
          row.querySelector('.improve-btn').addEventListener('click',()=>{
            mTitle.textContent = ch.label;
            mCat.textContent   = ch._cat || cat.name;
            mScore.textContent = isFinite(s)?s:'—';
            mBand.textContent  = bandLabel(s);
            mBand.className    = 'pill '+(s>=80?'score-pill--green':s>=60?'score-pill--orange':'score-pill--red');
            mWhy.textContent   = ch.why;
            mTips.innerHTML = '';
            (ch.tips||[]).forEach(t=>{
              const li=document.createElement('li'); li.textContent=t; mTips.appendChild(li);
            });
            mLink.href = ch.improve_search_url || ('https://www.google.com/search?q='+encodeURIComponent(ch.label+' best practices'));
            if(typeof modal.showModal==='function') modal.showModal(); else modal.setAttribute('open','');
          });

          list.appendChild(row);
        });

        catsEl.appendChild(card);
      });

    }catch(err){
      console.error(err);
      showError('Analyze failed.', String(err.message||err));
    }finally{
      setRunning(false);
    }
  });

  /* Close modal on backdrop click */
  modal?.addEventListener('click',e=>{
    const r=modal.getBoundingClientRect();
    const inside=(e.clientX>=r.left&&e.clientX<=r.right&&e.clientY>=r.top&&e.clientY<=r.bottom);
    if(!inside){ if(typeof modal.close==='function')modal.close(); else modal.removeAttribute('open'); }
  });
});
</script>
@endpush
