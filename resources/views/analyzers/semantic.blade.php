@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer')

@push('head')
<style>
  :root{
    --bg:#06021f; --ink:#e8eaf6; --muted:#a9b0c9; --line:rgba(255,255,255,.10);
    --chip:rgba(255,255,255,.06); --panel:#101628;
    --green:#22c55e; --orange:#f59e0b; --red:#ef4444;
  }
  html,body{background:var(--bg)!important;color:var(--ink)}
  .container{max-width:1180px;margin:0 auto;padding:24px 16px}

  /* ---------- Heading (centered) ---------- */
  .brand{display:flex;flex-direction:column;align-items:center;gap:8px;margin-bottom:8px;text-align:center}
  .crown{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(99,102,241,.30),rgba(236,72,153,.30));border:1px solid var(--line);font-size:20px}
  .title{font-weight:900;font-size:34px;letter-spacing:.2px;margin:0;line-height:1.15}
  .tgrad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent}
  .by{font-size:15px;color:var(--muted)}
  .by .name{display:inline-block;background:linear-gradient(90deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);background-size:300% 100%;-webkit-background-clip:text;background-clip:text;color:transparent;animation:slide 6s linear infinite}
  @keyframes slide{to{background-position:100% 0}}

  /* Legend with solid backgrounds */
  .legend{display:flex;gap:10px;margin:12px 0 22px;justify-content:center}
  .legend .badge{padding:8px 14px;border-radius:9999px;font-weight:900;font-size:13px;border:1px solid var(--line)}
  .legend .g{background:#34d399;color:#03140a}
  .legend .o{background:#fbbf24;color:#2b1600}
  .legend .r{background:#f87171;color:#2a0707}

  /* ---------- Top row: wheel left, pills+water right ---------- */
  .toprow{display:grid;grid-template-columns:340px 1fr;gap:24px;align-items:center}
  @media(max-width:980px){.toprow{grid-template-columns:1fr;gap:14px}}

  /* Wheel (compact fill-from-bottom) */
  .wheelwrap{width:320px;max-width:100%}
  .wheel{--v:0;--ring:var(--orange);--p:0;width:100%;aspect-ratio:1/1;position:relative}
  .w-ring{position:absolute;inset:0;border-radius:50%;
    background:conic-gradient(var(--ring) calc(var(--v)*1%),rgba(255,255,255,.08) 0);
    -webkit-mask:radial-gradient(circle 61% at 50% 50%,transparent 60%,#000 61%);
            mask:radial-gradient(circle 61% at 50% 50%,transparent 60%,#000 61%);
    box-shadow:inset 0 0 0 14px rgba(255,255,255,.08)}
  .w-fill{position:absolute;inset:24px;border-radius:50%;overflow:hidden;background:#000}
  .w-fill::after{
    content:"";position:absolute;left:0;right:0;height:100%;
    top:calc(100% - var(--p)*1%);transition:top .9s ease;
    background:var(--fill,linear-gradient(to top,var(--orange) 0%,#fbbf24 60%,#fde68a 100%));
    -webkit-mask:radial-gradient(160px 24px at 50% 0,#0000 98%,#000 100%);mask:radial-gradient(160px 24px at 50% 0,#0000 98%,#000 100%)}
  .wheel.good{--ring:var(--green);--fill:linear-gradient(to top,var(--green) 0%,#22c55e 60%,#86efac 100%)}
  .wheel.warn{--ring:var(--orange)}
  .wheel.bad {--ring:var(--red);--fill:linear-gradient(to top,var(--red) 0%,#f87171 60%,#fecaca 100%)}
  .w-num{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;font-size:56px;color:#fff}

  /* Pills (reduced size) */
  .pills{display:flex;flex-wrap:wrap;gap:10px}
  .pill{display:flex;align-items:center;gap:8px;font-weight:900;font-size:13px;padding:10px 12px;border-radius:12px;background:var(--chip);border:1px solid var(--line);color:#eef2ff}
  .pill.good{background:linear-gradient(135deg,rgba(34,197,94,.28),rgba(16,185,129,.12));color:#ecfff4}
  .pill.warn{background:linear-gradient(135deg,rgba(245,158,11,.28),rgba(250,204,21,.12));color:#fff8e9}
  .pill.bad{background:linear-gradient(135deg,rgba(239,68,68,.28),rgba(248,113,113,.12));color:#ffecec}
  .txG{color:#a7f3d0}.txO{color:#fde68a}.txR{color:#fecaca}

  /* Water bar under pills */
  .water{position:relative;height:16px;border-radius:9999px;overflow:hidden;border:1px solid var(--line);background:#0b0b0b;margin-top:12px}
  .water .f{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .water.good .f{background:linear-gradient(90deg,var(--green),#4ade80,#86efac)}
  .water.warn .f{background:linear-gradient(90deg,var(--orange),#fbbf24,#fde68a)}
  .water.bad  .f{background:linear-gradient(90deg,var(--red),#f87171,#fecaca)}
  .water .lbl{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;font-size:12px;color:#e7e9f0}

  /* ---------- Analyzer panel (pure black) ---------- */
  .panel{background:var(--panel);border:1px solid var(--line);border-radius:18px;padding:16px}
  .panel.analyzer{background:#000;border-color:#222}
  .urlbox{position:relative;display:flex;align-items:center;gap:10px;border:1px solid #222;background:#060606;padding:12px 14px;border-radius:14px}
  .urlbox input{flex:1;background:transparent;border:none;outline:none;color:#fff;font-weight:600}
  .urlbox:focus-within{box-shadow:0 0 0 2px #6ee7ff1f,0 0 0 1px #1f2937;border-color:#2b2b2b}
  .paste{padding:6px 10px;border-radius:10px;border:1px solid #333;background:#121212;color:#d9def2;font-weight:900}

  .btn{padding:10px 14px;border-radius:12px;border:1px solid #2b2b2b;font-weight:900;font-size:14px;letter-spacing:.2px}
  .btn.g{background:var(--green);color:#07140b;box-shadow:0 6px 20px rgba(34,197,94,.25)}
  .btn.b{background:#3b82f6;color:#06142e;box-shadow:0 6px 20px rgba(59,130,246,.25)}
  .btn.o{background:var(--orange);color:#2e1800;box-shadow:0 6px 20px rgba(245,158,11,.25)}
  .btn.p{background:linear-gradient(90deg,#a78bfa,#f472b6);color:#1a0320;box-shadow:0 6px 20px rgba(167,139,250,.25)}

  /* File chooser (styled) */
  .filewrap{display:flex;align-items:center;gap:10px}
  .filebtn{padding:8px 12px;border-radius:10px;border:1px solid #2b2b2b;background:#121212;color:#dbeafe;font-weight:900;cursor:pointer}
  #fileName{color:var(--muted);font-size:13px}

  /* Analyzer meta chips */
  .chips{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
  .chip{padding:10px 14px;border-radius:9999px;background:#0b0b0b;border:1px solid #222;font-weight:900}
  .lbl-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185);-webkit-background-clip:text;background-clip:text;color:transparent}

  /* ---------- Ground (slight zoom) ---------- */
  .ground{background:#0D0E1E;border:1px solid var(--line);border-radius:18px;padding:16px;margin-top:18px;transform:scale(1.01)}
  .ghead{display:flex;align-items:center;gap:10px;margin-bottom:10px}
  .gicon{width:36px;height:36px;border-radius:10px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(99,102,241,.28),rgba(236,72,153,.28));border:1px solid var(--line)}
  .gtitle{font-weight:900;font-size:18px}
  .cats{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  @media(max-width:1024px){.cats{grid-template-columns:1fr}}
  .ccard{background:#111E2F;border:1px solid var(--line);border-radius:14px;padding:12px}
  .cTop{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}
  .ct{font-weight:800;font-size:16px;background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185);-webkit-background-clip:text;background-clip:text;color:transparent}
  .progress{width:100%;height:8px;border-radius:9999px;background:rgba(255,255,255,.08);overflow:hidden;border:1px solid var(--line);margin:6px 0 8px}
  .progress>span{display:block;height:100%;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e);width:0%}
  .row{display:flex;align-items:center;justify-content:space-between;border:1px solid var(--line);background:#0f1a2a;border-radius:10px;padding:10px;margin-top:6px}
  .score{padding:2px 7px;border-radius:8px;font-weight:900;font-size:12px;background:rgba(255,255,255,.08);border:1px solid var(--line)}
  .sG{background:rgba(34,197,94,.18);border-color:rgba(34,197,94,.45);color:#caffe1}
  .sO{background:rgba(245,158,11,.18);border-color:rgba(245,158,11,.45);color:#fff4c6}
  .sR{background:rgba(239,68,68,.18);border-color:rgba(239,68,68,.45);color:#ffd7d7}
  .imp{padding:6px 10px;border-radius:9px;font-weight:900;border:1px solid transparent;font-size:12px}
  .fG{background:linear-gradient(135deg,#16a34a,#22c55e,#86efac);color:#05260e;border-color:#16a34a}
  .fO{background:linear-gradient(135deg,#f59e0b,#fbbf24,#fde68a);color:#3a2400;border-color:#d97706}
  .fR{background:linear-gradient(135deg,#ef4444,#f87171,#fecaca);color:#2f0a0a;border-color:#dc2626}

  /* ---------- Improve modal (higher contrast to read) ---------- */
  dialog[open]{display:block}
  dialog::backdrop{background:rgba(0,0,0,.6)}
  .modal{background:#0b0b0b;border:1px solid #2b2b2b;border-radius:14px;padding:14px;color:#f3f4f6}
  .modal .panel{background:#0e0e0e;border-color:#2b2b2b}
</style>
@endpush

@section('content')
<div class="container">

  <!-- Centered heading -->
  <div class="brand">
    <div class="crown">👑</div>
    <h1 class="title"><span class="tgrad">Semantic SEO Master Analyzer</span></h1>
    <div class="by">By <span class="name">Shoail Kahoker</span></div>
  </div>

  <!-- Legend -->
  <div class="legend">
    <span class="badge g">Green ≥ 80</span>
    <span class="badge o">Orange 60–79</span>
    <span class="badge r">Red &lt; 60</span>
  </div>

  <!-- Top row -->
  <div class="toprow">
    <div class="wheelwrap">
      <div class="wheel warn" id="wheel">
        <div class="w-ring" id="wRing" style="--v:0"></div>
        <div class="w-fill" id="wFill" style="--p:0"></div>
        <div class="w-num" id="wNum">0%</div>
      </div>
    </div>

    <div>
      <div class="pills">
        <div id="pillOverall" class="pill warn">✅ Overall: <span id="overallVal" class="txO">0 /100</span></div>
        <div id="pillContent" class="pill warn">📝 Content: <span id="contentVal" class="txO">—</span></div>
        <div id="pillWriter"  class="pill warn">✍️ Writer: <span id="writerVal" class="txO">—</span></div>
        <div id="pillHuman"   class="pill warn">🧑 Human-like: <span id="humanVal" class="txO">— %</span></div>
        <div id="pillAI"      class="pill warn">🤖 AI-like: <span id="aiVal" class="txO">— %</span></div>
      </div>
      <div id="bar" class="water warn">
        <div class="f" id="barFill" style="width:0%"></div>
        <div class="lbl" id="barLbl">0%</div>
      </div>
      <div style="color:var(--muted);font-size:13px;margin-top:6px">Wheel + water bars fill with your scores (colors: green ≥80, orange 60–79, red &lt;60).</div>
    </div>
  </div>

  <!-- Analyzer panel (black background) -->
  <div class="panel analyzer" style="margin-top:18px">
    <div class="urlbox">
      <span>🌐</span>
      <input id="urlInput" type="url" placeholder="https://example.com/page">
      <button class="paste" id="pasteBtn">✕ Paste</button>
    </div>

    <div style="display:flex;gap:12px;align-items:center;margin-top:12px;flex-wrap:wrap">
      <label style="display:flex;align-items:center;gap:6px;font-size:13px"><input id="autoCheck" type="checkbox" checked> Auto-apply checkmarks (≥ 80)</label>

      <!-- styled chooser -->
      <div class="filewrap">
        <input id="importFile" type="file" accept="application/json" style="display:none">
        <label for="importFile" class="filebtn">Choose a file</label>
        <span id="fileName">No file chosen</span>
      </div>

      <!-- buttons -->
      <div style="flex:1"></div>
      <button class="btn p" id="importBtn">⬆ Import</button>
      <button class="btn g" id="analyzeBtn">🔍 Analyze</button>
      <button class="btn b" id="printBtn">🖨 Print</button>
      <button class="btn o" id="resetBtn">↻ Reset</button>
      <button class="btn p" id="exportBtn">⬇ Export</button>
    </div>

    <div class="chips">
      <div class="chip"><span class="lbl-grad">HTTP:</span>&nbsp;<span id="cHttp">—</span></div>
      <div class="chip"><span class="lbl-grad">Title:</span>&nbsp;<span id="cTitle">—</span></div>
      <div class="chip"><span class="lbl-grad">Meta desc:</span>&nbsp;<span id="cMeta">—</span></div>
      <div class="chip"><span class="lbl-grad">Canonical:</span>&nbsp;<span id="cCanon">—</span></div>
      <div class="chip"><span class="lbl-grad">Robots:</span>&nbsp;<span id="cRobots">—</span></div>
      <div class="chip"><span class="lbl-grad">Viewport:</span>&nbsp;<span id="cViewport">—</span></div>
      <div class="chip"><span class="lbl-grad">H1/H2/H3:</span>&nbsp;<span id="cH">—</span></div>
      <div class="chip"><span class="lbl-grad">Internal links:</span>&nbsp;<span id="cInt">—</span></div>
      <div class="chip"><span class="lbl-grad">Schema:</span>&nbsp;<span id="cSchema">—</span></div>
      <div class="chip"><span class="lbl-grad">Auto-checked:</span>&nbsp;<span id="cAuto">0</span></div>
    </div>
  </div>

  <!-- Quick stats -->
  <div class="panel" style="margin-top:12px">
    <h3 style="margin:0 0 8px 0">Quick Stats</h3>
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px">
      <div class="panel" style="padding:10px"><div style="color:var(--muted);font-size:12px">Readability (Flesch)</div><div id="statF" style="font-weight:900;font-size:20px">—</div><div id="statG" style="color:var(--muted);font-size:12px">—</div></div>
      <div class="panel" style="padding:10px"><div style="color:var(--muted);font-size:12px">Links (int / ext)</div><div style="font-weight:900;font-size:20px"><span id="qInt">0</span> / <span id="qExt">0</span></div></div>
      <div class="panel" style="padding:10px"><div style="color:var(--muted);font-size:12px">Text/HTML Ratio</div><div id="qRatio" style="font-weight:900;font-size:20px">—</div></div>
    </div>
  </div>

  <!-- Content Structure -->
  <div class="panel" style="margin-top:12px">
    <h3 style="margin:0 0 8px 0">Content Structure</h3>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
      <div class="panel" style="padding:10px"><div style="color:var(--muted);font-size:12px">Title</div><div id="titleVal" style="font-weight:800">—</div><div style="color:var(--muted);font-size:12px;margin-top:8px">Meta Description</div><div id="metaVal">—</div></div>
      <div class="panel" style="padding:10px"><div style="color:var(--muted);font-size:12px">Heading Map</div><div id="headingMap" style="margin-top:6px"></div></div>
    </div>
  </div>

  <!-- Recommendations -->
  <div class="panel" style="margin-top:12px">
    <h3 style="margin:0 0 8px 0">Recommendations</h3>
    <div id="recs" style="display:grid;grid-template-columns:1fr 1fr;gap:10px"></div>
  </div>

  <!-- Semantic SEO Ground -->
  <div class="ground">
    <div class="ghead">
      <div class="gicon">🧭</div>
      <div>
        <div class="gtitle tgrad">Semantic SEO Ground</div>
        <div style="color:var(--muted);font-size:12px">Actionable checklists for structure, quality, UX & entities</div>
      </div>
    </div>
    <div id="cats" class="cats"></div>
  </div>

  <!-- Improve Modal -->
  <dialog id="improveModal" class="modal">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
      <div style="font-weight:900" id="mTitle">Improve</div>
      <form method="dialog"><button class="btn">Close</button></form>
    </div>
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:8px">
      <div class="panel" style="padding:10px"><div style="color:var(--muted);font-size:12px">Category</div><div id="mCat" style="font-weight:800">—</div></div>
      <div class="panel" style="padding:10px">
        <div style="color:var(--muted);font-size:12px">Score</div>
        <div style="display:flex;align-items:center;gap:6px;margin-top:6px">
          <span id="mScore" class="score">—</span>
          <span id="mBand" class="chip">—</span>
        </div>
      </div>
      <a id="mSearch" target="_blank" class="panel" style="padding:10px;text-align:center">Search guidance</a>
    </div>
    <div style="margin-top:8px"><div style="color:var(--muted);font-size:12px">Why this matters</div><p id="mWhy" style="margin:4px 0 0 0;line-height:1.55">—</p></div>
    <div style="margin-top:8px"><div style="color:var(--muted);font-size:12px">How to improve</div><ul id="mTips" style="margin:6px 0 0 18px;line-height:1.55"></ul></div>
  </dialog>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const $ = s => document.querySelector(s);

  /* Wheel + pills */
  const wheel=$('#wheel'), wRing=$('#wRing'), wFill=$('#wFill'), wNum=$('#wNum');
  const pillOv=$('#pillOverall'), ovVal=$('#overallVal');
  const pillCt=$('#pillContent'), ctVal=$('#contentVal');
  const pillWr=$('#pillWriter'),  wrVal=$('#writerVal');
  const pillHu=$('#pillHuman'),   huVal=$('#humanVal');
  const pillAi=$('#pillAI'),      aiVal=$('#aiVal');
  const bar=$('#bar'), barFill=$('#barFill'), barLbl=$('#barLbl');

  /* URL & buttons */
  const urlInput=$('#urlInput'), pasteBtn=$('#pasteBtn'), analyzeBtn=$('#analyzeBtn'),
        printBtn=$('#printBtn'), resetBtn=$('#resetBtn'), exportBtn=$('#exportBtn'),
        importBtn=$('#importBtn'), importFile=$('#importFile'), fileName=$('#fileName');

  /* Stats/structure */
  const statF=$('#statF'), statG=$('#statG'), qInt=$('#qInt'), qExt=$('#qExt'), qRatio=$('#qRatio');
  const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap'), recsEl=$('#recs');

  /* Chips */
  const cHttp=$('#cHttp'), cTitle=$('#cTitle'), cMeta=$('#cMeta'), cCanon=$('#cCanon'), cRobots=$('#cRobots'), cViewport=$('#cViewport'), cH=$('#cH'), cInt=$('#cInt'), cSchema=$('#cSchema'), cAuto=$('#cAuto');

  /* Ground & modal */
  const catsEl=$('#cats');
  const modal=$('#improveModal'), mTitle=$('#mTitle'), mCat=$('#mCat'), mScore=$('#mScore'), mBand=$('#mBand'), mWhy=$('#mWhy'), mTips=$('#mTips'), mSearch=$('#mSearch');

  const band = s => s>=80 ? 'good' : (s>=60 ? 'warn' : 'bad');
  const clsText = s => s>=80 ? 'txG' : (s>=60 ? 'txO' : 'txR');
  const sPill = s => s>=80 ? 'sG' : (s>=60 ? 'sO' : 'sR');
  const fBtn  = s => s>=80 ? 'fG'  : (s>=60 ? 'fO'  : 'fR');
  const label = s => s>=80 ? 'Good (≥80)' : (s>=60 ? 'Needs work (60–79)' : 'Low (<60)');
  const clamp = n => Math.max(0,Math.min(100,Number(n)||0));
  function setBand(el,score){ if(!el)return; el.classList.remove('good','warn','bad'); el.classList.add(band(score)); }

  function setRunning(on){
    analyzeBtn.disabled=on; analyzeBtn.style.opacity=on?.6:1; analyzeBtn.textContent=on?'Analyzing…':'🔍 Analyze';
  }

  /* paste & file */
  pasteBtn.addEventListener('click', async ()=>{ try{const t=await navigator.clipboard.readText(); if(t) urlInput.value=t.trim();}catch{} });
  importBtn.addEventListener('click',()=>importFile.click());
  importFile.addEventListener('change',e=>{
    const f=e.target.files?.[0]; fileName.textContent=f?f.name:'No file chosen';
    if(!f) return;
    const r=new FileReader(); r.onload=()=>{ try{const j=JSON.parse(String(r.result||'{}')); if(j.url) urlInput.value=j.url; alert('Imported. Click Analyze.'); }catch{ alert('Invalid JSON'); } };
    r.readAsText(f);
  });
  printBtn.addEventListener('click',()=>window.print());
  resetBtn.addEventListener('click',()=>location.reload());
  exportBtn.addEventListener('click',()=>{
    if(!window.__lastData){alert('Run an analysis first.');return;}
    const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});
    const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='semantic-report.json'; a.click(); URL.revokeObjectURL(a.href);
  });

  async function callAnalyzer(url){
    const headers={'Accept':'application/json','Content-Type':'application/json'};
    let res=await fetch('/api/semantic-analyze',{method:'POST',headers,body:JSON.stringify({url,target_keyword:''})});
    if(res.ok) return res.json();
    if([404,405,419].includes(res.status)){
      res=await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url,target_keyword:''})});
      if(res.ok) return res.json();
    }
    let msg=`HTTP ${res.status}`; try{const j=await res.json(); if(j?.error) msg+=' – '+j.error;}catch{}; throw new Error(msg);
  }

  analyzeBtn.addEventListener('click', async ()=>{
    const url=(urlInput.value||'').trim(); if(!url){alert('Enter a URL.');return;}
    try{
      setRunning(true);
      wRing.style.setProperty('--v',0); wFill.style.setProperty('--p',0); wNum.textContent='0%'; barFill.style.width='0%'; barLbl.textContent='0%';

      const data=await callAnalyzer(url);
      if(!data || data.error) throw new Error(data?.error||'Unknown error');
      window.__lastData={...data,url};

      /* Overall */
      const s=clamp(data.overall_score||0);
      wheel.classList.remove('good','warn','bad'); wheel.classList.add(band(s));
      setBand(pillOv,s); ovVal.className=clsText(s); ovVal.textContent=`${s} /100`;
      wRing.style.setProperty('--v',s); wFill.style.setProperty('--p',s); wNum.textContent=s+'%';
      bar.classList.remove('good','warn','bad'); bar.classList.add(band(s)); barFill.style.width=s+'%'; barLbl.textContent=s+'%';

      /* Content average */
      const cmap={}; (data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
      const cScore=Math.round(((cmap['Content & Keywords']??0)+(cmap['Content Quality']??0))/2);
      setBand(pillCt,cScore); ctVal.className=clsText(cScore); ctVal.textContent=`${cScore} /100`;

      /* Writer / likeness */
      const r=data.readability||{}, human=clamp(Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3)), ai=100-human;
      setBand(pillWr,human); wrVal.className=clsText(human); wrVal.textContent=human>=60?'Likely Human':'Possibly AI';
      setBand(pillHu,human); huVal.className=clsText(human); huVal.textContent=human+' %';
      setBand(pillAi,ai);    aiVal.className=clsText(ai);    aiVal.textContent=ai+' %';

      /* Stats */
      statF.textContent=r.flesch??'—'; statG.textContent='Grade '+(r.grade??'—');
      qInt.textContent=data.quick_stats?.internal_links??0; qExt.textContent=data.quick_stats?.external_links??0;
      qRatio.textContent=(data.quick_stats?.text_to_html_ratio??0)+'%';

      /* Structure */
      titleVal.textContent=data.content_structure?.title||'—';
      metaVal.textContent=data.content_structure?.meta_description||'—';
      const hs=data.content_structure?.headings||{}; headingMap.innerHTML='';
      Object.entries(hs).forEach(([lvl,arr])=>{
        if(!arr?.length) return;
        const box=document.createElement('div'); box.className='panel'; box.style.padding='8px';
        box.innerHTML='<div style="color:var(--muted);font-size:12px;margin-bottom:6px">'+lvl+'</div>'+arr.map(t=>'• '+t).join('<br>');
        headingMap.appendChild(box);
      });

      /* Recommendations */
      recsEl.innerHTML='';
      (data.recommendations||[]).forEach(rec=>{
        const d=document.createElement('div'); d.className='panel'; d.style.padding='8px';
        d.innerHTML='<span class="badge o" style="padding:4px 8px;border-radius:999px;border:1px solid var(--line);margin-right:6px">'+rec.severity+'</span>'+rec.text;
        recsEl.appendChild(d);
      });

      /* Chips */
      cTitle.textContent=(data.content_structure?.title||'').length||0;
      cMeta.textContent=(data.content_structure?.meta_description||'').length||0;
      try{cCanon.textContent=new URL(url).origin}catch{cCanon.textContent='—'}
      cRobots.textContent='—'; cViewport.textContent='—';
      cH.textContent=`H1:${(hs.H1||[]).length} • H2:${(hs.H2||[]).length} • H3:${(hs.H3||[]).length}`;
      cInt.textContent=data.quick_stats?.internal_links??0; cSchema.textContent=data.schema_count??0;
      cAuto.textContent=(data.categories||[]).flatMap(c=>c.checks||[]).filter(x=>(x.score||0)>=80).length;
      cHttp.textContent='200';

      /* Ground: categories + checklist */
      catsEl.innerHTML='';
      (data.categories||[]).forEach(cat=>{
        const total=(cat.checks||[]).length;
        const passed=(cat.checks||[]).filter(x=>(x.score||0)>=80).length;
        const pct=Math.round(passed/Math.max(1,total)*100);
        const card=document.createElement('div'); card.className='ccard';
        card.innerHTML=`<div class="cTop"><div class="ct">${cat.name}</div><div class="chip">${passed} / ${total}</div></div>
                        <div class="progress"><span style="width:${pct}%"></span></div>
                        <div class="list"></div>`;
        const list=card.querySelector('.list');

        (cat.checks||[]).forEach(item=>{
          const sc=item.score??0;
          const r=document.createElement('div'); r.className='row';
          r.innerHTML=`<div style="display:flex;align-items:center;gap:8px"><span style="width:8px;height:8px;border-radius:50%;background:${sc>=80?'var(--green)':(sc>=60?'var(--orange)':'var(--red)')}"></span><div style="font-weight:700">${item.label}</div></div>
                       <div style="display:flex;align-items:center;gap:6px">
                         <span class="score ${sPill(sc)}">${sc}</span>
                         <button class="imp ${fBtn(sc)}">Improve</button>
                       </div>`;
          r.querySelector('.imp').addEventListener('click',()=>{
            mTitle.textContent=item.label; mCat.textContent=cat.name; mScore.textContent=sc; mScore.className='score '+sPill(sc);
            mBand.textContent=label(sc); mBand.className='chip '+(sc>=80?'txG':(sc>=60?'txO':'txR'));
            mWhy.textContent=item.why||'This affects topical authority, UX, and rich-result eligibility.';
            mTips.innerHTML=''; (item.tips||['Aim for ≥80 (green) and re-run the analyzer.']).forEach(t=>{const li=document.createElement('li');li.textContent=t;mTips.appendChild(li)});
            mSearch.href=item.improve_search_url || ('https://www.google.com/search?q='+encodeURIComponent(item.label+' SEO best practices'));
            if(modal.showModal) modal.showModal(); else modal.setAttribute('open','');
          });
          list.appendChild(r);
        });

        catsEl.appendChild(card);
      });

    }catch(err){
      console.error(err); alert('Analyze failed: '+err.message);
    }finally{
      setRunning(false);
    }
  });

  /* Close modal on backdrop click */
  modal?.addEventListener('click',e=>{
    const r=modal.getBoundingClientRect();
    if(!(e.clientX>=r.left&&e.clientX<=r.right&&e.clientY>=r.top&&e.clientY<=r.bottom)){
      if(modal.close) modal.close(); else modal.removeAttribute('open');
    }
  });
});
</script>
@endpush
