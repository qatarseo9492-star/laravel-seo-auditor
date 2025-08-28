@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* ===== Page frame (no Tailwind required) ===== */
  html,body{background:#06021f!important}
  .wrap{max-width:1120px;margin:0 auto;padding:24px 16px}
  .row{display:flex;gap:24px;align-items:center}
  .col{flex:1 1 0}
  .stack{display:flex;flex-direction:column;gap:16px}
  .stack-lg{display:flex;flex-direction:column;gap:24px}
  .chips{display:flex;flex-wrap:wrap;gap:12px}
  .grid3{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px}
  .grid2{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
  @media (max-width:980px){ .row{flex-direction:column} .grid3{grid-template-columns:1fr} .grid2{grid-template-columns:1fr} }

  /* ===== Tokens ===== */
  .card{border-radius:18px;padding:18px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.10)}
  .pill{padding:6px 12px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.08);color:#e5e7eb}
  .btn{padding:8px 14px;border-radius:12px;border:1px solid rgba(255,255,255,.15);font-weight:800;cursor:pointer;background:rgba(255,255,255,.08);color:#e5e7eb}
  .input{width:100%;padding:10px 12px;border-radius:12px;border:1px solid rgba(255,255,255,.15);background:rgba(255,255,255,.06);color:#e5e7eb;outline:none}
  .muted{color:#9aa4b2;font-size:12px}
  .title-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent}
  .name-dance{display:inline-block;background:linear-gradient(90deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);background-size:400% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;animation:rg 6s linear infinite,up 2.6s ease-in-out infinite}
  @keyframes rg{to{background-position:100% 50%}} @keyframes up{50%{transform:translateY(-2px)}}

  /* ===== Wheel & progress ===== */
  .mw{--v:0;--ring:#f59e0b;--p:0;width:260px;height:260px;position:relative}
  .mw-ring{position:absolute;inset:0;border-radius:50%;background:conic-gradient(var(--ring) calc(var(--v)*1%),rgba(255,255,255,.08) 0);-webkit-mask:radial-gradient(circle 100px,transparent 92px,#000 92px);mask:radial-gradient(circle 100px,transparent 92px,#000 92px)}
  .mw-fill{position:absolute;inset:22px;border-radius:50%;overflow:hidden;background:#000}
  .mw-fill::after{content:"";position:absolute;left:0;right:0;height:100%;top:calc(100% - var(--p)*1%);transition:top .9s ease;background:var(--fill,linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%));-webkit-mask:radial-gradient(125px 20px at 50% 0,#0000 98%,#000 100%)}
  .mw.good{--ring:#22c55e;--fill:linear-gradient(to top,#16a34a 0%,#22c55e 60%,#86efac 100%)}
  .mw.warn{--ring:#f59e0b;--fill:linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%)}
  .mw.bad{--ring:#ef4444;--fill:linear-gradient(to top,#ef4444 0%,#f87171 60%,#fecaca 100%)}
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:54px;font-weight:900;color:#fff;text-shadow:0 6px 22px rgba(0,0,0,.45)}
  .bar{position:relative;height:22px;border-radius:9999px;overflow:hidden;border:1px solid rgba(255,255,255,.12);background:#0b0b0b}
  .bar > span{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .bar.good > span{background:linear-gradient(90deg,#16a34a,#22c55e,#86efac)}
  .bar.warn > span{background:linear-gradient(90deg,#f59e0b,#fbbf24,#fde68a)}
  .bar.bad  > span{background:linear-gradient(90deg,#ef4444,#f87171,#fecaca)}
  .bar .lbl{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e5e7eb;font-size:12px}

  /* ===== Chips ===== */
  .chip{padding:12px 16px;border-radius:16px;font-weight:900;display:inline-flex;align-items:center;gap:10px;border:1px solid rgba(255,255,255,.14);color:#eef2ff}
  .chip i{font-style:normal;font-size:18px}
  .chip.good{background:linear-gradient(135deg,rgba(34,197,94,.35),rgba(16,185,129,.18));border-color:rgba(34,197,94,.45);color:#eafff3}
  .chip.warn{background:linear-gradient(135deg,rgba(245,158,11,.35),rgba(250,204,21,.18));border-color:rgba(245,158,11,.45);color:#fff7e6}
  .chip.bad {background:linear-gradient(135deg,rgba(239,68,68,.35),rgba(248,113,113,.18));border-color:rgba(239,68,68,.45);color:#ffecec}

  /* ===== Ground & checks ===== */
  .slab{border-radius:24px;padding:22px;background:#0D0E1E;border:1px solid #1b2640}
  .cat{border-radius:18px;padding:18px;background:#111E2F;border:1px solid rgba(255,255,255,.12)}
  .check{display:flex;align-items:center;justify-content:space-between;border-radius:14px;padding:14px 16px;border:1px solid rgba(255,255,255,.10);background:#0F1A29}
  .score{padding:4px 8px;border-radius:10px;font-weight:800;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);color:#e5e7eb}
  .score.g{background:rgba(16,185,129,.18);border-color:rgba(16,185,129,.35);color:#bbf7d0}
  .score.o{background:rgba(245,158,11,.18);border-color:rgba(245,158,11,.35);color:#fde68a}
  .score.r{background:rgba(239,68,68,.18);border-color:rgba(239,68,68,.35);color:#fecaca}
  .btn-i{padding:6px 10px;border-radius:10px;color:#0b1020;font-weight:800;border:1px solid transparent}
  .gfill{background:linear-gradient(135deg,#16a34a,#22c55e,#86efac)} .gout{border-color:rgba(34,197,94,.85)!important}
  .ofill{background:linear-gradient(135deg,#f59e0b,#fbbf24,#fde68a)} .oout{border-color:rgba(245,158,11,.85)!important}
  .rfill{background:linear-gradient(135deg,#ef4444,#f87171,#fecaca)} .rout{border-color:rgba(239,68,68,.85)!important}
</style>
@endpush

@section('content')
<div class="wrap stack-lg">

  <!-- Heading -->
  <div class="stack">
    <h1 style="font-size:28px;font-weight:900">
      <span class="title-grad">Semantic SEO Master Analyzer</span>
      <span class="muted"> &nbsp;By <span class="name-dance">Shoail Kahoker</span></span>
    </h1>
    <div class="chips">
      <span class="pill" style="background:rgba(34,197,94,.15);color:#a7f3d0">Green ≥ 80</span>
      <span class="pill" style="background:rgba(245,158,11,.15);color:#fde68a">Orange 60–79</span>
      <span class="pill" style="background:rgba(239,68,68,.15);color:#fecaca">Red &lt; 60</span>
    </div>
  </div>

  <!-- Hero: wheel + chips + water -->
  <div class="row">
    <div class="col" style="display:grid;place-items:center">
      <div class="mw warn" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-fill" id="mwFill" style="--p:0"></div>
        <div class="mw-center" id="mwNum">0%</div>
      </div>
    </div>
    <div class="col stack">
      <div class="chips">
        <span id="chipOverall" class="chip warn"><i>🟠</i><span>Overall: 0 /100</span></span>
        <span id="chipContent" class="chip warn"><i>🟠</i><span>Content: —</span></span>
        <span id="chipWriter"  class="chip warn"><i>🟠</i><span>Writer: —</span></span>
        <span id="chipHuman"   class="chip warn"><i>🟠</i><span>Human-like: — %</span></span>
        <span id="chipAI"      class="chip warn"><i>🟠</i><span>AI-like: — %</span></span>
      </div>
      <div id="overallBar" class="bar warn">
        <span id="overallFill"></span>
        <div class="lbl"><span id="overallPct">0%</span></div>
      </div>
      <p class="muted">Wheel + water bars fill with your scores (green ≥80, orange 60–79, red &lt;60).</p>
    </div>
  </div>

  <!-- Analyze controls -->
  <div class="card">
    <div class="row" style="gap:10px;align-items:center">
      <span>🌐</span>
      <input id="urlInput" type="url" placeholder="https://example.com/page" class="input">
      <button id="pasteBtn" type="button" class="btn">Paste</button>
      <button id="analyzeBtn" type="button" class="btn" style="background:#22c55e;color:#04170d">Analyze</button>
    </div>
  </div>

  <!-- Quick stats -->
  <div class="card stack">
    <h3 class="title-grad" style="font-weight:900">Quick Stats</h3>
    <div class="grid3">
      <div class="card"><div class="muted">Readability (Flesch)</div><div id="statFlesch" style="font-size:22px;font-weight:900">—</div><div id="statGrade" class="muted">—</div></div>
      <div class="card"><div class="muted">Links (int / ext)</div><div style="font-size:22px;font-weight:900"><span id="statInt">0</span> / <span id="statExt">0</span></div></div>
      <div class="card"><div class="muted">Text/HTML Ratio</div><div id="statRatio" style="font-size:22px;font-weight:900">—</div></div>
    </div>
  </div>

  <!-- Content structure -->
  <div class="card stack">
    <h3 class="title-grad" style="font-weight:900">Content Structure</h3>
    <div class="grid2">
      <div class="card">
        <div class="muted">Title</div><div id="titleVal" style="font-weight:700">—</div>
        <div class="muted" style="margin-top:8px">Meta Description</div><div id="metaVal">—</div>
      </div>
      <div class="card">
        <div class="muted" style="margin-bottom:8px">Heading Map</div>
        <div id="headingMap" style="display:flex;flex-direction:column;gap:8px"></div>
      </div>
    </div>
  </div>

  <!-- Recommendations -->
  <div class="card stack">
    <h3 class="title-grad" style="font-weight:900">Recommendations</h3>
    <div id="recs" class="grid2"></div>
  </div>

  <!-- Ground -->
  <div class="slab stack">
    <div class="row" style="align-items:center;gap:12px">
      <div class="pill">🧭</div>
      <div>
        <div class="title-grad" style="font-size:22px;font-weight:900">Semantic SEO Ground</div>
        <div class="muted">Actionable checklists for structure, quality, UX & entities</div>
      </div>
    </div>
    <div id="cats" class="grid2"></div>
  </div>

  <!-- Improve Modal -->
  <dialog id="improveModal" class="rounded-2xl p-0" style="width:min(680px,95vw);border:0;background:transparent">
    <div class="card">
      <div class="row" style="justify-content:space-between;align-items:flex-start">
        <h4 id="improveTitle" style="font-weight:700">Improve</h4>
        <form method="dialog"><button class="pill">Close</button></form>
      </div>
      <div class="grid3" style="margin-top:12px">
        <div class="card"><div class="muted">Category</div><div id="improveCategory" style="font-weight:700">—</div></div>
        <div class="card">
          <div class="muted">Score</div>
          <div class="row" style="gap:8px;margin-top:6px"><span id="improveScore" class="score">—</span><span id="improveBand" class="pill">—</span></div>
        </div>
        <a id="improveSearch" target="_blank" class="card" style="display:flex;align-items:center;justify-content:center;text-align:center;background:linear-gradient(90deg,rgba(217,70,239,.15),rgba(14,165,233,.15))">Search guidance</a>
      </div>
      <div class="stack" style="margin-top:12px">
        <div><div class="muted">Why this matters</div><p id="improveWhy">—</p></div>
        <div><div class="muted">How to improve</div><ul id="improveTips" style="margin-top:6px;padding-left:18px;display:flex;flex-direction:column;gap:6px"></ul></div>
      </div>
    </div>
  </dialog>

</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const $=s=>document.querySelector(s);

  /* Elements */
  const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'), pasteBtn=$('#pasteBtn');
  const mw=$('#mw'), mwRing=$('#mwRing'), mwFill=$('#mwFill'), mwNum=$('#mwNum');
  const bar=$('#overallBar'), barFill=$('#overallFill'), barPct=$('#overallPct');
  const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'), chipHuman=$('#chipHuman'), chipAI=$('#chipAI');
  const statF=$('#statFlesch'), statG=$('#statGrade'), statInt=$('#statInt'), statExt=$('#statExt'), statRatio=$('#statRatio');
  const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap'), recsEl=$('#recs'), catsEl=$('#cats');
  const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
        mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
        mTips=$('#improveTips'), mLink=$('#improveSearch');

  /* Helpers */
  const clamp=n=>Math.max(0,Math.min(100,Number(n)||0));
  const band=(s)=>s>=80?'good':(s>=60?'warn':'bad');
  const icon=(s)=>s>=80?'🟢':(s>=60?'🟠':'🔴');
  const tag=(s)=>s>=80?'g':(s>=60?'o':'r');
  function setChip(el,label,value,score){ el.className='chip '+band(score); el.innerHTML=`<i>${icon(score)}</i><span>${label}: ${value}</span>`; }
  function running(on){ analyzeBtn.disabled=on; analyzeBtn.textContent=on?'Analyzing…':'Analyze'; analyzeBtn.style.opacity=on?.65:1; }

  pasteBtn?.addEventListener('click',async()=>{ try{ const t=await navigator.clipboard.readText(); if(t) urlInput.value=t.trim(); }catch{} });

  async function callAnalyzer(url){
    const headers={'Accept':'application/json','Content-Type':'application/json'};
    let r = await fetch('/api/semantic-analyze',{method:'POST',headers,credentials:'same-origin',body:JSON.stringify({url,target_keyword:''})});
    if(r.ok) return r.json();
    const tok=document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')||'';
    r = await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':tok},credentials:'same-origin',body:JSON.stringify({url,target_keyword:''})});
    if(r.ok) return r.json();
    let msg=`HTTP ${r.status}`; try{const j=await r.json(); if(j?.error) msg+=' – '+j.error;}catch{}
    throw new Error(msg);
  }

  analyzeBtn?.addEventListener('click', async ()=>{
    const url=(urlInput.value||'').trim(); if(!url){ alert('Enter a URL'); return; }
    try{
      running(true);
      // reset visuals
      mw.className='mw warn'; mwRing.style.setProperty('--v',0); mwFill.style.setProperty('--p',0); mwNum.textContent='0%';
      bar.className='bar warn'; barFill.style.width='0%'; barPct.textContent='0%';

      const data=await callAnalyzer(url);
      if(!data||data.error) throw new Error(data?.error||'Unknown error');

      // overall
      const s=clamp(data.overall_score||0), b=band(s);
      mw.className='mw '+b; bar.className='bar '+b;
      mwRing.style.setProperty('--v',s); mwFill.style.setProperty('--p',s); mwNum.textContent=s+'%';
      barFill.style.width=s+'%'; barPct.textContent=s+'%';
      setChip(chipOverall,'Overall',`${s} /100`,s);

      // content = average of 2 cats
      const cmap={}; (data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
      const cont=Math.round(([cmap['Content & Keywords'],cmap['Content Quality']].filter(v=>typeof v==='number').reduce((a,b)=>a+b,0))/2 || 0);
      setChip(chipContent,'Content',`${cont} /100`,cont);

      // writer / human / ai signals (heuristic)
      const r=data.readability||{}, human=Math.max(0,Math.min(100,Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3))), ai=Math.max(0,Math.min(100,100-human));
      setChip(chipWriter,'Writer',human>=60?'Likely Human':'Possibly AI',human);
      setChip(chipHuman,'Human-like',`${human} %`,human);
      setChip(chipAI,'AI-like',`${ai} %`,ai);

      // quick stats
      statF.textContent=r.flesch??'—'; statG.textContent='Grade '+(r.grade??'—');
      statInt.textContent=data.quick_stats?.internal_links??0; statExt.textContent=data.quick_stats?.external_links??0;
      statRatio.textContent=(data.quick_stats?.text_to_html_ratio??0)+'%';

      // structure
      titleVal.textContent=data.content_structure?.title||'—';
      metaVal.textContent=data.content_structure?.meta_description||'—';
      const hs=data.content_structure?.headings||{}; headingMap.innerHTML='';
      Object.entries(hs).forEach(([lvl,arr])=>{
        if(!arr||!arr.length)return;
        const box=document.createElement('div'); box.className='card';
        box.innerHTML = `<div class="muted" style="text-transform:uppercase;margin-bottom:6px">${lvl}</div>` + arr.map(t=>`<div>• ${t}</div>`).join('');
        headingMap.appendChild(box);
      });

      // recommendations
      recsEl.innerHTML=''; (data.recommendations||[]).forEach(rec=>{
        const d=document.createElement('div'); d.className='card';
        d.innerHTML = `<span class="pill" style="margin-right:8px">${rec.severity}</span>${rec.text}`;
        recsEl.appendChild(d);
      });

      // ground
      const tipsDefault=(name)=>({
        'Technical Elements':['Title ~50–60 chars incl. primary keyword.','Meta 140–160 chars + CTA.','Set canonical, avoid duplicates.','Ensure in XML sitemap.'],
        'Content & Keywords':['Define intent & primary topic early.','Use variants/PAA naturally.','Single descriptive H1.','Add short FAQ answers.','Write simple, NLP-friendly language.'],
        'Structure & Architecture':['Logical H2/H3 topic clusters.','Internal links to hub pages.','Clean descriptive URL.','Enable breadcrumbs (+schema).'],
        'Content Quality':['Show E-E-A-T (author/date/expertise).','Unique value vs competitors.','Cite recent sources.','Helpful media + captions.'],
        'User Signals & Experience':['Responsive layout.','Compression & lazy-load.','Good LCP/INP/CLS.','Clear CTAs.'],
        'Entities & Context':['Primary entity defined.','Related entities covered.','Valid schema (Article/FAQ/Product).','sameAs/Organization present.']
      }[name]||['Raise score to ≥80 (green) and re-run.']);

      catsEl.innerHTML='';
      (data.categories||[]).forEach(cat=>{
        const total=(cat.checks||[]).length; const pass=(cat.checks||[]).filter(x=>(x.score||0)>=80).length;
        const pct=Math.round((pass/Math.max(1,total))*100);
        const el=document.createElement('div'); el.className='cat';
        el.innerHTML=`<div class="row" style="justify-content:space-between;margin-bottom:10px">
            <div class="title-grad" style="font-weight:900;font-size:18px">${cat.name}</div>
            <div class="pill">${pass} / ${total}</div>
          </div>
          <div style="width:100%;height:10px;border-radius:9999px;background:rgba(255,255,255,.08);overflow:hidden;border:1px solid rgba(255,255,255,.14);margin-bottom:10px">
            <span style="display:block;height:100%;width:${pct}%;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e)"></span>
          </div>
          <div class="stack" id="L"></div>`;
        const list=el.querySelector('#L');
        (cat.checks||[]).forEach(ch=>{
          const s=clamp(ch.score??0), tg=tag(s);
          const row=document.createElement('div'); row.className='check';
          row.innerHTML=`<div class="row" style="gap:8px;align-items:center">
                           <span style="width:10px;height:10px;border-radius:9999px;background:${tg==='g'?'#10b981':tg==='o'?'#f59e0b':'#ef4444'};display:inline-block"></span>
                           <div style="font-weight:600">${ch.label}</div>
                         </div>
                         <div class="row" style="gap:8px;align-items:center">
                           <span class="score ${tg}">${s}</span>
                           <button class="btn-i ${tg==='g'?'gfill gout':tg==='o'?'ofill oout':'rfill rout'}">Improve</button>
                         </div>`;
          row.querySelector('button').addEventListener('click',()=>{
            mTitle.textContent=ch.label; mCat.textContent=cat.name; mScore.textContent=s;
            mBand.textContent=s>=80?'Good (≥80)':s>=60?'Needs work (60–79)':'Low (<60)';
            mBand.className='pill ' + (s>=80?'score g':s>=60?'score o':'score r');
            mWhy.textContent=ch.why||'This impacts topical authority, UX and rich-result eligibility.';
            mTips.innerHTML=''; (ch.tips||tipsDefault(cat.name)).forEach(t=>{const li=document.createElement('li'); li.textContent=t; mTips.appendChild(li);});
            mLink.href=ch.improve_search_url||('https://www.google.com/search?q='+encodeURIComponent(ch.label+' SEO best practices'));
            modal.showModal ? modal.showModal() : modal.setAttribute('open','');
          });
          list.appendChild(row);
        });
        catsEl.appendChild(el);
      });

    }catch(err){ console.error(err); alert('Analyze failed: '+err.message); }
    finally{ running(false); }
  });

  // Close modal if click outside
  modal?.addEventListener('click',e=>{
    const r=modal.getBoundingClientRect();
    if(!(e.clientX>=r.left&&e.clientX<=r.right&&e.clientY>=r.top&&e.clientY<=r.bottom)){
      modal.close ? modal.close() : modal.removeAttribute('open');
    }
  });
});
</script>
@endpush
@endsection
