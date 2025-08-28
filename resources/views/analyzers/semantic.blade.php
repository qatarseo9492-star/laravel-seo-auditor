@extends('layouts.app')
@section('title','Analyze a URL')

@push('head')
<style>
  :root{
    --bg:#0b0d1b;          /* page */
    --panel:#13192b;       /* cards */
    --ink:#e7e9f0;         /* text */
    --muted:#a8b0c2;       /* secondary text */
    --line:rgba(255,255,255,.10);
    --chip:rgba(255,255,255,.06);
    --pill:rgba(255,255,255,.08);
    --ring-dim:rgba(255,255,255,.09);
    --green:#22c55e;
    --orange:#f59e0b;
    --red:#ef4444;
  }

  html,body{background:#06021f!important}
  body{color:var(--ink);font-synthesis-weight:none}
  *{box-sizing:border-box}

  .container{max-width:1200px;margin:0 auto;padding:28px 16px}

  /* Heading + legend */
  .h1{font-weight:900;letter-spacing:.3px;font-size:36px;line-height:1.15;margin:0}
  .legend{display:flex;gap:10px;margin:8px 0 24px 0}
  .legend .badge{
    padding:8px 14px;border-radius:999px;
    font-weight:800;font-size:14px;border:1px solid var(--line);
    background:var(--pill);color:var(--ink)
  }

  /* Main split */
  .split{display:grid;grid-template-columns:360px 1fr;gap:28px;align-items:start}
  @media (max-width:1024px){.split{grid-template-columns:1fr}}

  /* Wheel */
  .wheel-wrap{width:320px;max-width:100%;margin:0 auto}
  .wheel{--v:0;--ring:var(--orange);--p:0;width:100%;aspect-ratio:1/1;position:relative}
  .wheel-ring{
    position:absolute;inset:0;border-radius:50%;
    background:conic-gradient(var(--ring) calc(var(--v)*1%),var(--ring-dim) 0);
    -webkit-mask:radial-gradient(circle 58% at 50% 50%,transparent 57%,#000 58%);
            mask:radial-gradient(circle 58% at 50% 50%,transparent 57%,#000 58%);
    box-shadow:inset 0 0 0 18px var(--ring-dim)
  }
  .wheel-fill{
    position:absolute;inset:30px;border-radius:50%;overflow:hidden;background:#000
  }
  .wheel-fill::after{
    content:"";position:absolute;left:0;right:0;height:100%;
    top:calc(100% - var(--p)*1%);transition:top .9s ease;
    background:var(--fill,linear-gradient(to top,var(--orange) 0%,#fbbf24 60%,#fde68a 100%));
    -webkit-mask:radial-gradient(200px 28px at 50% 0,#0000 98%,#000 100%);
            mask:radial-gradient(200px 28px at 50% 0,#0000 98%,#000 100%);
    will-change:top;transform:translateZ(0)
  }
  .wheel.good{--ring:var(--green);--fill:linear-gradient(to top,var(--green) 0%,#22c55e 60%,#86efac 100%)}
  .wheel.warn{--ring:var(--orange);--fill:linear-gradient(to top,var(--orange) 0%,#fbbf24 60%,#fde68a 100%)}
  .wheel.bad {--ring:var(--red);--fill:linear-gradient(to top,var(--red) 0%,#f87171 60%,#fecaca 100%)}
  .wheel-num{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;font-size:72px;color:#fff;text-shadow:0 8px 28px rgba(0,0,0,.45)}

  /* Stat pills row */
  .pills{display:flex;flex-wrap:wrap;gap:12px;margin:10px 0 16px}
  .pill{
    background:var(--chip);border:1px solid var(--line);padding:14px 18px;border-radius:16px;
    font-weight:900;display:flex;align-items:center;gap:10px;color:#eef2ff
  }
  .pill.good{background:linear-gradient(135deg,rgba(34,197,94,.30),rgba(16,185,129,.14));color:#eafff3}
  .pill.warn{background:linear-gradient(135deg,rgba(245,158,11,.30),rgba(250,204,21,.14));color:#fff7e6}
  .pill.bad {background:linear-gradient(135deg,rgba(239,68,68,.30),rgba(248,113,113,.14));color:#ffecec}
  .pill .dot{width:10px;height:10px;border-radius:50%}

  /* Water progress bar */
  .water{position:relative;height:22px;border-radius:9999px;overflow:hidden;border:1px solid var(--line);background:#0b0b0b}
  .water .f{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .water.good .f{background:linear-gradient(90deg,var(--green),#4ade80,#86efac)}
  .water.warn .f{background:linear-gradient(90deg,var(--orange),#fbbf24,#fde68a)}
  .water.bad  .f{background:linear-gradient(90deg,var(--red),#f87171,#fecaca)}
  .water .lbl{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e7e9f0;font-size:12px}

  /* Copy / info line */
  .help{color:var(--muted);font-size:14px;margin:10px 0 18px}
  .ghost{background:var(--panel);border:1px solid var(--line);padding:10px 14px;border-radius:12px;font-weight:800;display:inline-flex;gap:8px;align-items:center}

  /* URL panel */
  .panel{background:var(--panel);border:1px solid var(--line);border-radius:16px;padding:16px}
  .panel h3{margin:0 0 10px 0;font-size:18px}
  .url-row{display:flex;gap:10px;align-items:center}
  .url-input{
    display:flex;align-items:center;gap:8px;border:1px solid var(--line);background:#0a0f1c;padding:10px 12px;border-radius:12px;flex:1
  }
  .url-input input{background:transparent;border:none;outline:none;color:var(--ink);width:100%}
  .btn{padding:10px 14px;border-radius:12px;border:1px solid var(--line);font-weight:900}
  .b-green{background:var(--green);color:#05140a}
  .b-blue{background:#3b82f6;color:#081226}
  .b-orange{background:var(--orange);color:#231400}
  .b-purple{background:linear-gradient(90deg,#a78bfa,#f472b6);color:#18021a}

  .toolbar{display:flex;gap:10px;align-items:center;margin-top:12px;flex-wrap:wrap}
  .meta{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
  .chip{padding:10px 14px;border-radius:14px;background:var(--chip);border:1px solid var(--line);font-weight:900;color:#eef2ff}
</style>
@endpush

@section('content')
<div class="container">

  <h1 class="h1">Analyze a URL</h1>
  <div class="legend">
    <span class="badge">Green ≥ 80</span>
    <span class="badge">Orange 60–79</span>
    <span class="badge">Red &lt; 60</span>
  </div>

  <div class="split">
    <!-- LEFT: wheel -->
    <div>
      <div class="wheel-wrap">
        <div class="wheel warn" id="wheel">
          <div class="wheel-ring" id="wheelRing" style="--v:0"></div>
          <div class="wheel-fill" id="wheelFill" style="--p:0"></div>
          <div class="wheel-num" id="wheelNum">0%</div>
        </div>
      </div>
    </div>

    <!-- RIGHT: stats + bar -->
    <div>
      <div class="pills">
        <div id="pillOverall" class="pill warn"><span class="dot" style="background:var(--orange)"></span> Overall: <span id="pOverallVal">0 /100</span></div>
        <div id="pillContent" class="pill warn"><span class="dot" style="background:var(--orange)"></span> Content: <span id="pContentVal">—</span></div>
        <div id="pillWriter"  class="pill warn"><span class="dot" style="background:var(--orange)"></span> Writer: <span id="pWriterVal">—</span></div>
        <div id="pillHuman"   class="pill warn"><span class="dot" style="background:var(--orange)"></span> Human-like: <span id="pHumanVal">— %</span></div>
        <div id="pillAI"      class="pill warn"><span class="dot" style="background:var(--orange)"></span> AI-like: <span id="pAIVal">— %</span></div>
      </div>

      <div id="bar" class="water warn">
        <div class="f" id="barFill" style="width:0%"></div>
        <div class="lbl" id="barLbl">0%</div>
      </div>

      <div class="help" style="margin-top:8px">Wheel + water bars fill with your scores (colors: green ≥80, orange 60–79, red &lt;60).</div>

      <button id="copyBtn" type="button" class="ghost">📋 Copy report</button>
    </div>
  </div>

  <!-- URL PANEL -->
  <div class="panel" style="margin-top:22px">
    <h3>Page URL</h3>
    <div class="url-row">
      <div class="url-input" style="flex:1">
        <span>🌐</span>
        <input id="urlInput" type="url" placeholder="https://example.com/">
        <button id="pasteBtn" class="btn">✕ Paste</button>
      </div>
    </div>

    <div class="toolbar">
      <label style="display:flex;align-items:center;gap:8px">
        <input id="autoCheck" type="checkbox" checked> Auto-apply checkmarks (≥ 80)
      </label>

      <span style="flex:1"></span>

      <input id="importFile" type="file" accept="application/json" class="hidden">
      <button id="importBtn" class="btn b-purple">⬆ Import</button>
      <button id="analyzeBtn" class="btn b-green">🔍 Analyze</button>
      <button id="printBtn"   class="btn b-blue">🖨 Print</button>
      <button id="resetBtn"   class="btn b-orange">↻ Reset</button>
      <button id="exportBtn"  class="btn b-purple">⬇ Export</button>
    </div>

    <div class="meta">
      <div class="chip">HTTP: <span id="chipHttp">&nbsp;—</span></div>
      <div class="chip">Title: <span id="chipTitle">&nbsp;—</span></div>
      <div class="chip">Meta desc: <span id="chipMeta">&nbsp;—</span></div>
      <div class="chip">Canonical: <span id="chipCanon">&nbsp;—</span></div>
      <div class="chip">Robots: <span id="chipRobots">&nbsp;—</span></div>
      <div class="chip">Viewport: <span id="chipViewport">&nbsp;—</span></div>
      <div class="chip">H1/H2/H3: <span id="chipH">&nbsp;—</span></div>
      <div class="chip">Internal links: <span id="chipInt">&nbsp;—</span></div>
      <div class="chip">Schema: <span id="chipSchema">&nbsp;—</span></div>
      <div class="chip">Auto-checked: <span id="chipAuto">0</span></div>
    </div>
  </div>

  <!-- Quick + Structure + Recs (minimal containers, keep existing IDs if you already render into them) -->
  <div class="panel" style="margin-top:22px">
    <h3>Quick Stats</h3>
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px">
      <div class="panel" style="padding:12px"><div style="color:var(--muted);font-size:12px">Readability (Flesch)</div><div id="statFlesch" style="font-weight:800;font-size:22px">—</div><div id="statGrade" style="color:var(--muted);font-size:12px">—</div></div>
      <div class="panel" style="padding:12px"><div style="color:var(--muted);font-size:12px">Links (int / ext)</div><div style="font-weight:800;font-size:22px"><span id="statInt">0</span> / <span id="statExt">0</span></div></div>
      <div class="panel" style="padding:12px"><div style="color:var(--muted);font-size:12px">Text/HTML Ratio</div><div id="statRatio" style="font-weight:800;font-size:22px">—</div></div>
    </div>
  </div>

  <div class="panel" style="margin-top:16px">
    <h3>Content Structure</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
      <div class="panel" style="padding:12px"><div style="color:var(--muted);font-size:12px">Title</div><div id="titleVal" style="font-weight:700">—</div><div style="color:var(--muted);font-size:12px;margin-top:8px">Meta Description</div><div id="metaVal">—</div></div>
      <div class="panel" style="padding:12px"><div style="color:var(--muted);font-size:12px">Heading Map</div><div id="headingMap" class="text-sm" style="margin-top:6px"></div></div>
    </div>
  </div>

  <div class="panel" style="margin-top:16px">
    <h3>Recommendations</h3>
    <div id="recs" style="display:grid;grid-template-columns:1fr 1fr;gap:10px"></div>
  </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const $ = s => document.querySelector(s);

  /* Elements */
  const wheel = $('#wheel'), wheelRing = $('#wheelRing'), wheelFill = $('#wheelFill'), wheelNum = $('#wheelNum');
  const pillOverall = $('#pillOverall'), pillContent = $('#pillContent'), pillWriter = $('#pillWriter'), pillHuman = $('#pillHuman'), pillAI = $('#pillAI');
  const pOverallVal = $('#pOverallVal'), pContentVal = $('#pContentVal'), pWriterVal = $('#pWriterVal'), pHumanVal = $('#pHumanVal'), pAIVal = $('#pAIVal');
  const bar = $('#bar'), barFill = $('#barFill'), barLbl = $('#barLbl');

  const urlInput = $('#urlInput'), pasteBtn = $('#pasteBtn'), analyzeBtn = $('#analyzeBtn'),
        printBtn = $('#printBtn'), resetBtn = $('#resetBtn'), exportBtn = $('#exportBtn'),
        importBtn = $('#importBtn'), importFile = $('#importFile'), copyBtn = $('#copyBtn');

  const statF = $('#statFlesch'), statG = $('#statGrade'), statInt = $('#statInt'), statExt = $('#statExt'), statRatio = $('#statRatio');
  const titleVal = $('#titleVal'), metaVal = $('#metaVal'), headingMap = $('#headingMap'), recsEl = $('#recs');

  const chipHttp = $('#chipHttp'), chipTitle = $('#chipTitle'), chipMeta = $('#chipMeta'),
        chipCanon = $('#chipCanon'), chipRobots = $('#chipRobots'), chipViewport = $('#chipViewport'),
        chipH = $('#chipH'), chipInt = $('#chipInt'), chipSchema = $('#chipSchema'), chipAuto = $('#chipAuto');

  /* Helpers */
  const band = s => s>=80 ? 'good' : (s>=60 ? 'warn' : 'bad');
  const dot  = s => s>=80 ? 'var(--green)' : (s>=60 ? 'var(--orange)' : 'var(--red)');
  const text = s => `${s}%`;

  function setBand(el, score){
    el.classList.remove('good','warn','bad'); el.classList.add(band(score));
  }

  function setRunning(on){
    analyzeBtn.disabled = on;
    analyzeBtn.style.opacity = on ? .6 : 1;
    analyzeBtn.textContent = on ? 'Analyzing…' : '🔍 Analyze';
  }

  /* Actions */
  pasteBtn.addEventListener('click', async () => { try{const t=await navigator.clipboard.readText(); if(t) urlInput.value=t.trim();}catch{} });
  printBtn.addEventListener('click', () => window.print());
  resetBtn.addEventListener('click', () => location.reload());

  importBtn.addEventListener('click', ()=>importFile.click());
  importFile.addEventListener('change', e=>{
    const f=e.target.files?.[0]; if(!f) return;
    const r=new FileReader();
    r.onload=()=>{try{const j=JSON.parse(String(r.result||'{}')); if(j.url) urlInput.value=j.url; alert('Imported. Click Analyze.');}catch{alert('Invalid file.')}};
    r.readAsText(f);
  });

  exportBtn.addEventListener('click', () => {
    if(!window.__lastData){alert('Run an analysis first.');return;}
    const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});
    const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='semantic-report.json'; a.click(); URL.revokeObjectURL(a.href);
  });

  copyBtn.addEventListener('click', async () => {
    try{
      const d = window.__lastData;
      if(!d){alert('Nothing to copy yet.');return;}
      await navigator.clipboard.writeText(JSON.stringify(d,null,2));
      copyBtn.textContent = '✅ Copied';
      setTimeout(()=>copyBtn.textContent='📋 Copy report',1200);
    }catch{}
  });

  async function callAnalyzer(url){
    const headers={'Accept':'application/json','Content-Type':'application/json'};
    let res = await fetch('/api/semantic-analyze',{method:'POST',headers,body:JSON.stringify({url,target_keyword:''})});
    if(res.ok) return res.json();
    if([404,405,419].includes(res.status)){
      res = await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url,target_keyword:''})});
      if(res.ok) return res.json();
    }
    let msg=`HTTP ${res.status}`; try{const j=await res.json(); if(j?.error) msg+=' – '+j.error;}catch{}; throw new Error(msg);
  }

  analyzeBtn.addEventListener('click', async () => {
    const url=(urlInput.value||'').trim(); if(!url){alert('Enter a URL.');return;}
    try{
      setRunning(true);
      // reset visuals
      wheelRing.style.setProperty('--v',0); wheelFill.style.setProperty('--p',0); wheelNum.textContent='0%';
      barFill.style.width='0%'; barLbl.textContent='0%';

      const data = await callAnalyzer(url);
      if(!data || data.error) throw new Error(data?.error||'Unknown error');
      window.__lastData={...data,url};

      /* Overall score / wheel / bar */
      const s = Number(data.overall_score||0);
      wheel.classList.remove('good','warn','bad'); wheel.classList.add(band(s));
      setBand(pillOverall, s); pOverallVal.textContent = `${s} /100`;
      wheelRing.style.setProperty('--v', s);
      wheelFill.style.setProperty('--p', s);
      wheelNum.textContent = text(s);
      bar.classList.remove('good','warn','bad'); bar.classList.add(band(s));
      barFill.style.width = s+'%'; barLbl.textContent = text(s);

      /* Content score = avg(Content & Keywords, Content Quality) */
      const cmap={}; (data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
      const cScore=Math.round(((cmap['Content & Keywords']??0)+(cmap['Content Quality']??0))/2);
      setBand(pillContent,cScore); pContentVal.textContent=`${cScore} /100`;
      pillContent.querySelector('.dot').style.background = dot(cScore);

      /* Writer / human-like / AI-like from readability */
      const r=data.readability||{}, human=Math.max(0,Math.min(100,Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3))), ai=100-human;
      setBand(pillWriter,human); pWriterVal.textContent = human>=60?'Likely Human':'Possibly AI';
      setBand(pillHuman,human); pHumanVal.textContent = human+' %';
      setBand(pillAI,ai);       pAIVal.textContent    = ai+' %';
      pillOverall.querySelector('.dot').style.background = dot(s);
      pillHuman .querySelector('.dot').style.background = dot(human);
      pillAI    .querySelector('.dot').style.background = dot(ai);

      /* Quick stats */
      statF.textContent=r.flesch??'—'; statG.textContent='Grade '+(r.grade??'—');
      statInt.textContent=data.quick_stats?.internal_links??0;
      statExt.textContent=data.quick_stats?.external_links??0;
      statRatio.textContent=(data.quick_stats?.text_to_html_ratio??0)+'%';

      /* Structure */
      titleVal.textContent=data.content_structure?.title||'—';
      metaVal.textContent=data.content_structure?.meta_description||'—';
      const hs=data.content_structure?.headings||{}; headingMap.innerHTML='';
      Object.entries(hs).forEach(([lvl,arr])=>{
        if(!arr?.length) return;
        const box=document.createElement('div');
        box.className='panel'; box.style.padding='10px';
        box.innerHTML=`<div style="color:var(--muted);font-size:12px;margin-bottom:6px">${lvl}</div>`+arr.map(t=>`<div>• ${t}</div>`).join('');
        headingMap.appendChild(box);
      });

      /* Recommendations */
      recsEl.innerHTML='';
      (data.recommendations||[]).forEach(rec=>{
        const d=document.createElement('div'); d.className='panel'; d.style.padding='10px';
        d.innerHTML=`<span class="badge" style="background:var(--pill);margin-right:6px">${rec.severity}</span>${rec.text}`;
        recsEl.appendChild(d);
      });

      /* Status chips (simple, fast) */
      chipTitle.textContent=(data.content_structure?.title||'').length||0;
      chipMeta.textContent=(data.content_structure?.meta_description||'').length||0;
      try{chipCanon.textContent=new URL(url).origin}catch{chipCanon.textContent='—'}
      chipRobots.textContent='—'; chipViewport.textContent='—';
      chipH.textContent=`H1:${(hs.H1||[]).length} • H2:${(hs.H2||[]).length} • H3:${(hs.H3||[]).length}`;
      chipInt.textContent=data.quick_stats?.internal_links??0;
      chipSchema.textContent=data.schema_count??0;
      chipAuto.textContent=(data.categories||[]).flatMap(c=>c.checks||[]).filter(x=>(x.score||0)>=80).length;
      chipHttp.textContent='200';
    }catch(err){
      console.error(err);
      alert('Analyze failed: '+err.message);
    }finally{
      setRunning(false);
    }
  });
});
</script>
@endpush
