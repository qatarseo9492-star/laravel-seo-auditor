@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* =============== Base page styles (New Stylish Redesign) =============== */
  :root {
    --bg-dark-1: #1A1A1A;
    --bg-dark-2: #1F1F1F;
    --bg-dark-3: #262626;
    --border-color: #333333;
    --glow-purple: rgba(161, 82, 242, 0.5);
    --glow-cyan: rgba(15, 248, 246, 0.5);
    --glow-green: rgba(43, 250, 106, 0.5);
    --glow-yellow: rgba(255, 219, 70, 0.5);
    --glow-pink: rgba(255, 72, 122, 0.5);
    --primary-green: #2BFA6A;
    --primary-yellow: #FFDB46;
    --primary-pink: #FF487A;
  }
  
  html,body{background:var(--bg-dark-1)!important;color:#e5e7eb; font-family: sans-serif;}
  .maxw{max-width:1150px;margin:0 auto;border:1px solid var(--border-color);border-radius:18px;padding:8px; box-shadow: 0 0 40px rgba(161, 82, 242, 0.15);}

  .title-wrap{display:flex;align-items:center;gap:14px;justify-content:center;margin-top:14px}
  .king{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:var(--bg-dark-2);border:1px solid var(--border-color)}
  .t-grad{background:linear-gradient(90deg, #0FF8F6, #A152F2, #FF487A, #FFDB46, #2BFA6A);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900}
  .byline{font-size:14px;color:#cbd5e1}
  .shoail{display:inline-block;background:linear-gradient(90deg, #0FF8F6, #A152F2, #FF487A, #FFDB46, #2BFA6A);-webkit-background-clip:text;background-clip:text;color:transparent;background-size:400% 100%;animation:rainbowSlide 6s linear infinite,bob 3s ease-in-out infinite}
  @keyframes rainbowSlide{to{background-position:100% 50%}} @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  .legend{display:flex;gap:10px;justify-content:center;margin:10px 0 6px}
  .legend .badge{padding:6px 10px;border-radius:9999px;font-weight:800;border:1px solid #ffffff2a;font-size:12px}
  .legend .g{background:rgba(43,250,106,.1);color:var(--primary-green);border-color:rgba(43,250,106,.4)}
  .legend .o{background:rgba(255,219,70,.1);color:var(--primary-yellow);border-color:rgba(255,219,70,.4)}
  .legend .r{background:rgba(255,72,122,.1);color:var(--primary-pink);border-color:rgba(255,72,122,.4)}

  .card, .ground-slab, .analyze-wrap {
    border-radius:18px; padding:18px; background:var(--bg-dark-2); border:1px solid var(--border-color);
    box-shadow: 0 0 20px rgba(0,0,0,.3), 0 0 25px var(--glow-purple-trans, rgba(161, 82, 242, 0));
    transition: box-shadow 0.3s ease;
  }
  .card:hover, .ground-slab:hover, .analyze-wrap:hover { --glow-purple-trans: rgba(161, 82, 242, 0.1); }
  .ground-slab { margin-top: 20px; }
  .analyze-wrap { padding:12px; }

  .btn{padding:10px 14px;border-radius:12px;font-weight:900;border:1px solid transparent;color:#1A1A1A;font-size:13px;box-shadow: 0 0 12px rgba(0,0,0,.5)}
  .btn-green{background:#2BFA6A}.btn-blue{background:#1173F3}.btn-orange{background:#FFDB46}.btn-purple{background:linear-gradient(90deg,#FF487A,#A152F2);color:#fff}
  .url-row{display:flex;align-items:center;gap:10px;border:1px solid var(--border-color);background:var(--bg-dark-1);border-radius:12px;padding:8px 10px}
  .url-row input{background:transparent;border:none;outline:none;color:#e5e7eb;width:100%}
  .url-row .paste{padding:6px 10px;border-radius:10px;border:1px solid #333333;background:rgba(255,255,255,0.05);color:#e5e7eb}

  /* ===================== Overall Score Wheel (Redesigned) ===================== */
  .mw{--v:0;width:200px;height:200px;position:relative;filter:drop-shadow(0 10px 24px rgba(0,0,0,.35))}
  .mw-ring{position:absolute;inset:0;border-radius:50%;
    background: conic-gradient(from -90deg, #FF487A, #FFDB46, #2BFA6A, #0FF8F6, #A152F2);
    -webkit-mask: conic-gradient(from -90deg,#000 calc(var(--v)*1%), #0000 0), radial-gradient(circle 76px,transparent 72px,#000 72px);
    mask: conic-gradient(from -90deg,#000 calc(var(--v)*1%), #0000 0), radial-gradient(circle 76px,transparent 72px,#000 72px);
  }
  .mw-fill{ display: none; } /* Removed liquid fill as requested */
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:44px;font-weight:900;color:#fff;text-shadow:0 0 20px var(--glow-cyan)}
  .mw-center span { font-size: 24px; color: #aaa; margin-left: 2px; }
  .mw.good .mw-ring { filter: drop-shadow(0 0 10px var(--primary-green)); }
  .mw.warn .mw-ring { filter: drop-shadow(0 0 10px var(--primary-yellow)); }
  .mw.bad .mw-ring { filter: drop-shadow(0 0 10px var(--primary-pink)); }

  .waterbox{position:relative;height:16px;border-radius:9999px;overflow:hidden;border:1px solid var(--border-color);background:var(--bg-dark-1)}
  .waterbox .fill{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .waterbox.good .fill{background:linear-gradient(90deg,var(--primary-green), #8affb1)}
  .waterbox.warn .fill{background:linear-gradient(90deg,var(--primary-yellow), #ffeb9b)}
  .waterbox.bad .fill{background:linear-gradient(90deg,var(--primary-pink), #ff9bbd)}
  .waterbox .label{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e5e7eb;font-size:11px}

  /* Animated Icon & Section Heading Style */
  @keyframes icon-pulse { 0%, 100% { transform: scale(1); filter: drop-shadow(0 0 3px var(--glow-color)); } 50% { transform: scale(1.1); filter: drop-shadow(0 0 8px var(--glow-color)); } }
  .section-header { display: flex; align-items: center; gap: 12px; margin: 0 0 12px; }
  .section-header .icon { --glow-color: var(--glow-purple); animation: icon-pulse 4s ease-in-out infinite; }
  .section-header .icon svg { width: 24px; height: 24px; }
  .section-header h3 { margin: 0; font-weight: 900; }

  /* ===================== Content Optimization (Redesigned) ===================== */
  .co-card { --glow-purple-trans: rgba(161, 82, 242, 0.2); }
  .co-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 16px; align-items: start; }
  .co-info-item { background: var(--bg-dark-3); border: 1px solid var(--border-color); border-radius: 14px; padding: 14px; }
  .co-info-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
  .co-info-title { font-weight: 800; color: #e5e7eb; }
  .progress{width:100%;height:10px;border-radius:9999px;background:var(--bg-dark-1);overflow:hidden;border:1px solid var(--border-color)}
  .progress>span{display:block;height:100%;border-radius:9999px;background:linear-gradient(90deg,var(--primary-pink),var(--primary-yellow),var(--primary-green));transition:width .5s ease}
  
  /* ===================== NEW: Meta Info Layout ===================== */
  .meta-info-card { --glow-purple-trans: rgba(15, 248, 246, 0.15); }
  .meta-item { border: 1px solid var(--border-color); background: var(--bg-dark-3); padding: 12px; border-radius: 12px; margin-bottom: 10px; }
  .meta-item-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
  .meta-item-header .icon { animation: icon-pulse 3s ease-in-out infinite; }
  .meta-item-header .tag { font-size: 12px; font-weight: 800; padding: 3px 8px; border-radius: 7px; color: #fff; }
  .meta-item-header .h1 { background: var(--primary-pink); }
  .meta-item-header .h2 { background: var(--primary-yellow); color: var(--bg-dark-1); }
  .meta-item-header .h3 { background: var(--primary-green); color: var(--bg-dark-1); }
  .meta-item-header .h4 { background: #1173F3; }
  .meta-content { color: #d1d5db; }
  .meta-title, .meta-desc { padding: 10px; color: #e5e7eb; font-weight: 600; }

  /* ===================== Site Speed (Redesigned) ===================== */
  .speed-card { --glow-purple-trans: rgba(43, 250, 106, 0.15); }
  .speed-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 14px; }
  .speed-tile { background: var(--bg-dark-3); border: 1px solid var(--border-color); border-radius: 14px; padding: 12px; }
  .speed-row { display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #a6c5cf; margin: 6px 0; }
  .speed-val { color: #e5e7eb; font-weight: 800; }
  .speed-meter { height: 12px; border-radius: 9999px; background: var(--bg-dark-1); border: 1px solid var(--border-color); overflow: hidden; position: relative; }
  .speed-meter>span { display: block; height: 100%; width: 0%; transition: width .9s ease; background: linear-gradient(90deg, var(--primary-pink), var(--primary-yellow), var(--primary-green)); }
  .speed-suggestions { margin-top: 16px; background: var(--bg-dark-3); border: 1px solid var(--border-color); border-radius: 14px; padding: 14px; }
  .speed-suggestions h4 { margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px; font-weight: 800; }
  .speed-suggestions ul { margin: 0; padding-left: 0; list-style: none; display: grid; gap: 8px; }
  .speed-suggestions li { padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600; border-left: 3px solid; }
  .speed-suggestions li.good { background: rgba(43,250,106,.1); border-color: var(--primary-green); color: #c1ffda; }
  .speed-suggestions li.warn { background: rgba(255,219,70,.1); border-color: var(--primary-yellow); color: #ffeea8; }
  .speed-suggestions li.bad { background: rgba(255,72,122,.1); border-color: var(--primary-pink); color: #ffc5d6; }
  
  /* ===================== Semantic SEO Ground (Accordion Redesign) ===================== */
  .seo-ground-card { --glow-purple-trans: rgba(161, 82, 242, 0.2); }
  .accordion-item { border-bottom: 1px solid var(--border-color); }
  .accordion-item:last-child { border-bottom: none; }
  .accordion-header { display: flex; justify-content: space-between; align-items: center; padding: 15px; cursor: pointer; background: var(--bg-dark-2); transition: background 0.2s ease; }
  .accordion-header:hover { background: var(--bg-dark-3); }
  .accordion-title { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 16px; }
  .accordion-title .icon { font-size: 20px; }
  .accordion-toggle { font-size: 20px; transition: transform 0.3s ease; }
  .accordion-item.active .accordion-toggle { transform: rotate(45deg); }
  .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.3s ease-out; background: var(--bg-dark-1); }
  .accordion-content-inner { padding: 15px; }
  .check{display:flex;align-items:center;justify-content:space-between;border-radius:12px;padding:10px 12px;border:1px solid var(--border-color);background:var(--bg-dark-3); margin-bottom: 8px;}
  .score-pill{padding:3px 7px;border-radius:10px;font-weight:800;background:rgba(255,255,255,0.1);border:1px solid #ffffff22;color:#e5e7eb;font-size:12px}
  .score-pill--green{background:rgba(43,250,106,.15);border-color:rgba(43,250,106,.4);color:#9cffd1}
  .score-pill--orange{background:rgba(255,219,70,.15);border-color:rgba(255,219,70,.4);color:#ffeea8}
  .score-pill--red{background:rgba(255,72,122,.15);border-color:rgba(255,72,122,.4);color:#ffc5d6}

</style>

<script defer>
(function(){
  const init = () => {
    const $ = s=>document.querySelector(s);
    
    /* ============== Element refs ============== */
    const mw=$('#mw'), mwRing=$('#mwRing'), mwNum=$('#mwNum');
    const overallBar=$('#overallBar'), overallFill=$('#overallFill'), overallPct=$('#overallPct');
    const analyzeBtn=$('#analyzeBtn');
    
    // Meta Info refs
    const metaTitleEl = $('#metaTitle'), metaDescEl = $('#metaDesc'), headingMapEl = $('#headingMap');

    // Speed UI refs
    const lcpVal=$('#lcpVal'), lcpBar=$('#lcpBar'), lcpMeter=$('#lcpMeter');
    const clsVal=$('#clsVal'), clsBar=$('#clsBar'), clsMeter=$('#clsMeter');
    const inpVal=$('#inpVal'), inpBar=$('#inpBar'), inpMeter=$('#inpMeter');
    const ttfbVal=$('#ttfbVal'), ttfbBar=$('#ttfbBar'), ttfbMeter=$('#ttfbMeter');
    const psiFixes=$('#psiFixes');
    
    // Content Optimization refs
    const coCard = $('#contentOptimizationCard');
    let coElements = {};
    if (coCard) {
      coElements.coTopicCoverageText = coCard.querySelector('#coTopicCoverageText');
      coElements.coTopicCoverageProgress = coCard.querySelector('#coTopicCoverageProgress');
    }

    /* Helpers (unchanged) */
    const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
    const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
    function setChip(el,label,value,score){ if(!el)return; el.classList.remove('good','warn','bad'); const b=bandName(score); el.classList.add(b); el.innerHTML=`<span>${label}: ${value}</span>`; };
    
    /* API calls (unchanged) */
    async function callAnalyzer(url){const headers={'Accept':'application/json','Content-Type':'application/json'};let res=await fetch('/api/semantic-analyze',{method:'POST',headers,body:JSON.stringify({url,target_keyword:''})});if(res.ok)return res.json();if([404,405,419].includes(res.status)){res=await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url,target_keyword:''})});if(res.ok)return res.json()}const txt=await res.text();throw new Error(`HTTP ${res.status}\n${txt?.slice(0,800)}`)}
    async function callPSI(url){const res=await fetch('/semantic-analyzer/psi',{method:'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url})});const text=await res.text();let json={};try{json=JSON.parse(text)}catch{throw new Error(`PSI: invalid JSON\n${text?.slice(0,400)}`)}if(json.ok===false){throw new Error(json.error||json.message||'PSI unavailable')}if(!res.ok){throw new Error(json.error||json.message||`PSI HTTP ${res.status}`)}return json}
    function setRunning(isOn){if(!analyzeBtn)return;analyzeBtn.disabled=isOn;analyzeBtn.style.opacity=isOn?.6:1;analyzeBtn.textContent=isOn?'Analyzing…':'🔍 Analyze'}

    /* Speed helpers (unchanged) */
    const scoreFromBounds=(val,good,poor)=>{if(val==null||isNaN(val))return 0;if(val<=good)return 100;if(val>=poor)return 0;return Math.round(100*(1-((val-good)/(poor-good))))};
    function setSpMeter(barEl,valEl,raw,score,fmt,meterWrap){if(!valEl||!barEl)return;valEl.textContent=raw==null?'—':(fmt?fmt(raw):raw);barEl.style.width=clamp01(score)+'%';if(meterWrap){meterWrap.classList.remove('good','warn','bad');meterWrap.classList.add(bandName(score));}}

    /* ===== Analyze ===== */
    analyzeBtn?.addEventListener('click', async e=>{
      e.preventDefault();
      setRunning(true);
      const url=(document.querySelector('#urlInput').value||'').trim();
      if(!url){ setRunning(false); return; }

      try {
        const data = await callAnalyzer(url);
        if(!data||data.error) throw new Error(data?.error||'Unknown error');
        
        // Overall Score
        const score=clamp01(data.overall_score||0), bname=bandName(score);
        mw?.classList.remove('good','warn','bad');mw?.classList.add(bname);
        mwRing?.style.setProperty('--v',score);
        mwNum.innerHTML=`${score}<span>%</span>`;
        
        overallBar?.classList.remove('good','warn','bad');overallBar?.classList.add(bname);
        if(overallFill) overallFill.style.width=score+'%';
        if(overallPct) overallPct.textContent=score+'%';

        // Content Opt
        if(data.content_optimization && coElements.coTopicCoverageText) {
            const co = data.content_optimization;
            coElements.coTopicCoverageText.innerHTML = `Covers <strong>${co.topic_coverage.covered} of ${co.topic_coverage.total}</strong> key topics.`;
            coElements.coTopicCoverageProgress.style.width = co.topic_coverage.percentage + '%';
        }
        
        // NEW Meta Info Layout
        const cs = data.content_structure || {};
        metaTitleEl.textContent = cs.title || '—';
        metaDescEl.textContent = cs.meta_description || '—';
        headingMapEl.innerHTML = ''; // Clear previous
        const headings = cs.headings || {};
        ['H1','H2','H3','H4'].forEach(level => {
            if(headings[level] && headings[level].length) {
                const icon = `<div class="icon" style="--glow-color: var(--glow-pink)">...</div>`; // simplified
                headings[level].forEach(text => {
                    const el = document.createElement('div');
                    el.className = 'meta-item';
                    el.innerHTML = `
                        <div class="meta-item-header">
                            <span class="tag ${level.toLowerCase()}">${level}</span>
                        </div>
                        <div class="meta-content">${text}</div>`;
                    headingMapEl.appendChild(el);
                });
            }
        });
        
        // Speed
        const psi=await callPSI(url);
        const mobile=psi.mobile||{};
        const pick=(...vals)=>{for(const v of vals){const n=Number(v);if(v!==undefined&&v!==null&&!Number.isNaN(n))return n}return null};
        const lcpSeconds=pick(mobile.lcp_s,psi.lcp_s,psi.metrics?.lcp_s);
        const cls=pick(mobile.cls,psi.cls,psi.metrics?.cls);
        const inp=pick(mobile.inp_ms,psi.inp_ms,psi.metrics?.inp_ms);
        const ttfb=pick(mobile.ttfb_ms,psi.ttfb_ms,psi.metrics?.ttfb_ms);
        const sLCP=scoreFromBounds(lcpSeconds,2.5,6.0), sCLS=scoreFromBounds(cls,0.10,0.25), sINP=scoreFromBounds(inp,200,500), sTTFB=scoreFromBounds(ttfb,800,1800);
        setSpMeter(lcpBar,lcpVal,lcpSeconds,sLCP,v=>v!=null?`${v.toFixed(2)} s`:'—',lcpMeter);
        setSpMeter(clsBar,clsVal,cls,sCLS,v=>v!=null?`${v.toFixed(3)}`:'—',clsMeter);
        setSpMeter(inpBar,inpVal,inp,sINP,v=>v!=null?`${Math.round(v)} ms`:'—',inpMeter);
        setSpMeter(ttfbBar,ttfbVal,ttfb,sTTFB,v=>v!=null?`${Math.round(v)} ms`:'—',ttfbMeter);

        const tips=[];
        if(lcpSeconds > 2.5) tips.push({sev: 'bad', text:'Improve LCP: preload hero image, compress images.'});
        if(cls > 0.1) tips.push({sev: 'bad', text:'Reduce CLS: set width/height on images/media.'});
        if(inp > 200) tips.push({sev: 'warn', text:'Lower INP: break up long tasks, defer non-critical JS.'});
        if(ttfb > 800) tips.push({sev: 'warn', text:'Reduce TTFB: enable caching/CDN, optimize server.'});
        if(!tips.length) tips.push({sev: 'good', text:'Great job! Performance metrics look good.'})
        psiFixes.innerHTML=tips.map(t=>`<li class="${t.sev}">✅ ${t.text}</li>`).join('');

      } catch(err) {
        console.error(err);
      } finally {
        setRunning(false);
      }
    });

    // Accordion logic for Semantic SEO Ground
    const seoGround = $('#seoGround');
    if (seoGround) {
        seoGround.addEventListener('click', function(e){
            const header = e.target.closest('.accordion-header');
            if (!header) return;
            const item = header.parentElement;
            if (item.classList.contains('active')) {
                item.classList.remove('active');
            } else {
                seoGround.querySelectorAll('.accordion-item').forEach(i => i.classList.remove('active'));
                item.classList.add('active');
            }
        });
    }

  };
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init, { once: true }); } else { init(); }
})();
</script>
@endpush

@section('content')
<section class="maxw px-4 pb-10">

  <div class="title-wrap">
    <div class="king">👑</div>
    <div style="text-align:center">
      <div class="t-grad" style="font-size:26px;line-height:1.1;">Semantic SEO Master Analyzer</div>
      <div class="byline">By <span class="shoail">Shoail Kahoker</span></div>
    </div>
  </div>

  <div class="legend"><span class="badge g">Green ≥ 80</span><span class="badge o">Orange 60–79</span><span class="badge r">Red &lt; 60</span></div>

  <div style="display:grid; grid-template-columns: 250px 1fr; gap: 20px; align-items: center; margin-top: 10px;">
    <div class="card" style="display:grid;place-items:center;padding:8px; --glow-purple-trans: rgba(15, 248, 246, 0.2);">
      <div class="mw warn" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-fill" id="mwFill" style="--p:0"></div>
        <div class="mw-center" id="mwNum">0<span>%</span></div>
      </div>
    </div>
    <div class="analyze-wrap">
      <div class="url-row">
        <span style="opacity:.75">🌐</span>
        <input id="urlInput" name="url" type="url" placeholder="https://example.com/page" />
        <button id="pasteBtn" type="button" class="paste">Paste</button>
      </div>
      <div style="display:flex;align-items:center;gap:10px;margin-top:10px; flex-wrap: wrap;">
        <label style="display:flex;align-items:center;gap:8px;font-size:12px">
          <input id="autoCheck" type="checkbox" checked/> Auto-apply checkmarks (≥ 80)
        </label>
        <div style="flex:1"></div>
        <input id="importFile" type="file" accept="application/json" style="display:none"/>
        <button id="importBtn" type="button" class="btn btn-purple">⇪ Import</button>
        <button id="analyzeBtn" type="button" class="btn btn-green">🔍 Analyze</button>
        <button id="printBtn"   type="button" class="btn btn-blue">🖨️ Print</button>
        <button id="resetBtn"   type="button" class="btn btn-orange">↻ Reset</button>
        <button id="exportBtn"  type="button" class="btn btn-purple">⬇︎ Export</button>
      </div>
    </div>
  </div>

  <div class="card co-card" id="contentOptimizationCard" style="margin-top:20px;">
    <div class="section-header">
        <span class="icon" style="--glow-color: var(--glow-purple);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"></path><path d="m9 12 2 2 4-4"></path></svg>
        </span>
        <h3 class="t-grad">Content Optimization</h3>
    </div>
    <div class="co-grid">
        <div class="co-info-item">
            <div class="co-info-header"><span class="co-info-title">Topic Coverage</span></div>
            <p id="coTopicCoverageText">Run analysis to see topic coverage.</p>
            <div class="progress" style="margin-bottom: 0;"><span id="coTopicCoverageProgress" style="width:0%;"></span></div>
        </div>
        </div>
  </div>

  <div class="card meta-info-card" style="margin-top:20px;">
    <div class="section-header">
        <span class="icon" style="--glow-color: var(--glow-cyan);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
        </span>
        <h3>Meta & Heading Info</h3>
    </div>
    <div class="meta-item">
        <div class="meta-item-header"><strong style="color: #0FF8F6;">Title</strong></div>
        <div id="metaTitle" class="meta-title">—</div>
    </div>
    <div class="meta-item">
        <div class="meta-item-header"><strong style="color: #A152F2;">Meta Description</strong></div>
        <div id="metaDesc" class="meta-desc">—</div>
    </div>
    <div id="headingMap"></div>
  </div>

  <div class="card speed-card" id="speedCard" style="margin-top:20px;">
    <div class="section-header">
        <span class="icon" style="--glow-color: var(--glow-green);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        </span>
        <h3>Site Speed & Core Web Vitals</h3>
    </div>
    <div class="speed-grid">
      <div class="speed-tile"><div class="speed-row"><div>🏁 LCP (s)</div><div class="speed-val" id="lcpVal">—</div></div><div class="speed-meter" id="lcpMeter"><span id="lcpBar" style="width:0%"></span></div></div>
      <div class="speed-tile"><div class="speed-row"><div>📦 CLS</div><div class="speed-val" id="clsVal">—</div></div><div class="speed-meter" id="clsMeter"><span id="clsBar" style="width:0%"></span></div></div>
      <div class="speed-tile"><div class="speed-row"><div>⚡ INP (ms)</div><div class="speed-val" id="inpVal">—</div></div><div class="speed-meter" id="inpMeter"><span id="inpBar" style="width:0%"></span></div></div>
      <div class="speed-tile"><div class="speed-row"><div>⏱️ TTFB (ms)</div><div class="speed-val" id="ttfbVal">—</div></div><div class="speed-meter" id="ttfbMeter"><span id="ttfbBar" style="width:0%"></span></div></div>
    </div>
    <div class="speed-suggestions">
      <h4>💡 Speed Suggestions</h4>
      <ul id="psiFixes"><li>Run Analyze to fetch PSI data.</li></ul>
    </div>
  </div>

  <div class="ground-slab seo-ground-card" style="padding: 0;">
    <div class="section-header" style="padding: 18px 18px 0;">
      <span class="icon" style="--glow-color: var(--glow-purple);">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
      </span>
      <h3>Semantic SEO Ground</h3>
    </div>
    <div id="seoGround" class="accordion">
      </div>
  </div>

</section>
@endsection
