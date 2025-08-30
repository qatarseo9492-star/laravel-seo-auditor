@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  html,body{background:#03041c!important;color:#e5e7eb}
  .maxw{max-width:1150px;margin:0 auto;border:1px solid #154f2e;border-radius:18px;padding:8px}

  .title-wrap{display:flex;align-items:center;gap:14px;justify-content:center;margin-top:14px}
  .king{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:#101018;border:1px solid #ffffff24}
  .t-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900}
  .byline{font-size:14px;color:#cbd5e1}
  .shoail{display:inline-block;background:linear-gradient(90deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;background-size:400% 100%;animation:rainbowSlide 6s linear infinite,bob 3s ease-in-out infinite}
  @keyframes rainbowSlide{to{background-position:100% 50%}} @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  .legend{display:flex;gap:10px;justify-content:center;margin:10px 0 6px}
  .legend .badge{padding:6px 10px;border-radius:9999px;font-weight:800;border:1px solid #ffffff2a;font-size:12px}
  .legend .g{background:#063f2c;color:#a7f3d0;border-color:#10b98166}
  .legend .o{background:#3b2a05;color:#fde68a;border-color:#f59e0b66}
  .legend .r{background:#3a0b0b;color:#fecaca;border-color:#ef444466}

  .analyze-wrap{border-radius:16px;background:#020114;border:1px solid #ffffff20;padding:12px}
  .url-row{display:flex;align-items:center;gap:10px;border:1px solid #ffffff24;background:#0b0b12;border-radius:12px;padding:8px 10px}
  .url-row input{background:transparent;border:none;outline:none;color:#e5e7eb;width:100%}
  .url-row .paste{padding:6px 10px;border-radius:10px;border:1px solid #ffffff26;background:#ffffff10;color:#e5e7eb}
  .btn{padding:10px 14px;border-radius:12px;font-weight:900;border:1px solid #ffffff22;color:#0b1020;font-size:13px}
  .btn-green{background:#22c55e}.btn-blue{background:#3b82f6}.btn-orange{background:#f59e0b}.btn-purple{background:linear-gradient(90deg,#a78bfa,#f472b6);color:#19041a}
  #errorBox{display:none;margin-top:10px;border:1px solid #ef444466;background:#3a0b0b;color:#fecaca;border-radius:12px;padding:10px;white-space:pre-wrap;font-size:12px}

  /* ===== Universal PSI-style wheels (multicolor + glow) ===== */
  .mw{ --size:150px; --th:16px; --v:0; --p:0;
       width:var(--size); height:var(--size); position:relative; border-radius:50%;
       filter:drop-shadow(0 8px 24px rgba(0,0,0,.35));
  }
  .mw::before{
    content:""; position:absolute; inset:0; border-radius:50%;
    background:conic-gradient(#0f1b20 0 360deg);
    -webkit-mask:radial-gradient(circle calc(var(--size)/2 - var(--th)),transparent calc(var(--size)/2 - var(--th) - 1px),#000 0);
            mask:radial-gradient(circle calc(var(--size)/2 - var(--th)),transparent calc(var(--size)/2 - var(--th) - 1px),#000 0);
    box-shadow:0 0 0 1px #0d262c inset;
  }
  .mw-ring{
    position:absolute; inset:0; border-radius:50%;
    background:conic-gradient(from -90deg,#ef4444 0%,#f59e0b 40%,#facc15 55%,#84cc16 70%,#22c55e 100%);
    -webkit-mask:
      conic-gradient(from -90deg,#000 calc(var(--v)*1%), transparent 0),
      radial-gradient(circle calc(var(--size)/2 - var(--th)), transparent calc(var(--size)/2 - var(--th) - 1px), #000 0);
            mask:
      conic-gradient(from -90deg,#000 calc(var(--v)*1%), transparent 0),
      radial-gradient(circle calc(var(--size)/2 - var(--th)), transparent calc(var(--size)/2 - var(--th) - 1px), #000 0);
    box-shadow:0 0 0 1px #0e2c31 inset, 0 0 36px rgba(34,197,94,.20), 0 0 64px rgba(34,197,94,.14);
  }
  .mw-fill{position:absolute;inset:calc(var(--th) + 6px);border-radius:50%;background:radial-gradient(120% 100% at 30% 25%,#0c1d23,#091219 60%,#070b12)}
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:28px;font-weight:900;color:#fff;text-shadow:0 8px 24px rgba(0,0,0,.45)}
  .mw.good{filter:drop-shadow(0 0 12px rgba(34,197,94,.35)) drop-shadow(0 0 50px rgba(34,197,94,.25))}
  .mw.warn{filter:drop-shadow(0 0 12px rgba(245,158,11,.35)) drop-shadow(0 0 50px rgba(245,158,11,.25))}
  .mw.bad {filter:drop-shadow(0 0 12px rgba(239,68,68,.35))  drop-shadow(0 0 50px rgba(239,68,68,.25))}
  .mw-xs{--size:132px;--th:14px}
  .mw-xxs{--size:110px;--th:12px}
  .mw-xxs .mw-center{font-size:22px}

  /* ===== Site Speed & CWV ===== */
  .speed-card{border-radius:20px;background:#0b0f1f;border:1px solid #173a2a;padding:16px;margin-top:16px}
  .sp-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
  .sp-title{display:flex;align-items:center;gap:10px}
  .sp-title .ico{width:36px;height:36px;display:grid;place-items:center;border-radius:10px;background:linear-gradient(135deg,#34d39933,#22d3ee33);border:1px solid #1a4c34}
  .sp-note{font-size:12px;color:#a9d3be}

  /* Big wheels centered above bars */
  .sp-wheels{display:flex;justify-content:center;align-items:center;gap:18px;margin-top:12px;flex-wrap:wrap}
  .wheel-card{display:grid;place-items:center;border-radius:18px;padding:14px;background:radial-gradient(120% 120% at 20% 10%,#0a2a27,#07161a);border:1px solid #12373f;box-shadow:0 0 0 1px #0b2a2f inset,0 8px 28px rgba(0,0,0,.35);width:230px}
  .wheel-label{font-size:12px;color:#a6c5cf;margin-top:6px}

  /* NEW: mini 4 score wheels row */
  .sp-mini{display:flex;justify-content:center;align-items:center;gap:14px;margin-top:14px;flex-wrap:wrap}
  .mini-card{width:150px;display:grid;place-items:center;border-radius:14px;padding:10px;background:radial-gradient(120% 120% at 15% 10%,#0b1f25,#09161c);border:1px solid #12313a;box-shadow:0 0 0 1px #0a2a31 inset}
  .mini-label{font-size:12px;color:#9fb9c3;margin-top:6px}

  .sp-grid{display:grid;grid-template-columns:1fr;gap:14px;margin-top:14px}
  .sp-tile{background:#0e1a22;border:1px solid #1d3641;border-radius:14px;padding:12px}
  .sp-row{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#a6c5cf;margin:6px 0}
  .sp-val{color:#e5e7eb;font-weight:800}
  .sp-meter{height:12px;border-radius:9999px;background:#0b1417;border:1px solid #16414e;overflow:hidden;position:relative}
  .sp-meter>span{display:block;height:100%;width:0%;transition:width .9s ease;background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e)}
  .sp-meter::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(45deg,#ffffff0a 0 8px,#ffffff06 8px 16px);pointer-events:none}
  .sp-meter.good{box-shadow:0 0 0 1px #1b5e2f inset,0 0 24px #22c55e33}
  .sp-meter.warn{box-shadow:0 0 0 1px #8a5a12 inset,0 0 24px #f59e0b33}
  .sp-meter.bad {box-shadow:0 0 0 1px #6f1616 inset,0 0 24px #ef444433}

  .sp-fixes{background:#0e1a22;border:1px solid #1d3641;border-radius:14px;padding:14px;margin-top:12px}
  .sp-fixes h4{margin:0 0 8px 0;font-weight:900}
  .sp-fixes ul{margin:0;padding-left:18px}
  .sp-fixes li{margin:6px 0}

  .pill{padding:5px 10px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid #ffffff29;background:#ffffff14;color:#e5e7eb}
  .score-pill--green{background:#10b9812e;border-color:#10b98166;color:#bbf7d0}
  .score-pill--orange{background:#f59e0b2e;border-color:#f59e0b66;color:#fde68a}
  .score-pill--red{background:#ef44442e;border-color:#ef444466;color:#fecaca}
</style>

<script defer>
(function(){
  const init = () => {
    const $ = s=>document.querySelector(s);

    /* controls */
    const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'), errorBox=$('#errorBox');
    const clearError=()=>{errorBox.style.display='none';errorBox.textContent=''}; 
    const showError=(m,d)=>{errorBox.style.display='block';errorBox.textContent=m+(d?`\n\n${d}`:'')};

    /* big wheels */
    const mwMobile=$('#mwMobile'), ringMobile=$('#ringMobile'), fillMobile=$('#fillMobile'), numMobile=$('#numMobile');
    const mwDesktop=$('#mwDesktop'), ringDesktop=$('#ringDesktop'), fillDesktop=$('#fillDesktop'), numDesktop=$('#numDesktop');

    /* mini wheels (4 categories) */
    const mwPerf=$('#mwPerf'), ringPerf=$('#ringPerf'), fillPerf=$('#fillPerf'), numPerf=$('#numPerf');
    const mwAcc=$('#mwAcc'), ringAcc=$('#ringAcc'), fillAcc=$('#fillAcc'), numAcc=$('#numAcc');
    const mwBest=$('#mwBest'), ringBest=$('#ringBest'), fillBest=$('#fillBest'), numBest=$('#numBest');
    const mwSEO=$('#mwSEO'), ringSEO=$('#ringSEO'), fillSEO=$('#fillSEO'), numSEO=$('#numSEO');

    /* bars */
    const psiStatus=$('#psiStatus'), psiFixes=$('#psiFixes');
    const lcpVal=$('#lcpVal'), lcpBar=$('#lcpBar'), lcpMeter=$('#lcpMeter');
    const clsVal=$('#clsVal'), clsBar=$('#clsBar'), clsMeter=$('#clsMeter');
    const inpVal=$('#inpVal'), inpBar=$('#inpBar'), inpMeter=$('#inpMeter');
    const ttfbVal=$('#ttfbVal'), ttfbBar=$('#ttfbBar'), ttfbMeter=$('#ttfbMeter');

    const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
    const band = s => s>=80?'good':(s>=60?'warn':'bad');

    function setWheel(elRing, elFill, elNum, container, score, prefix){
      container.classList.remove('good','warn','bad'); container.classList.add(band(score));
      elRing.style.setProperty('--v',score); elFill.style.setProperty('--p',score);
      elNum.textContent = (prefix?prefix+' ':'') + score + '%';
    }
    function setWheelNumberOnly(elRing, elFill, elNum, container, score){
      container.classList.remove('good','warn','bad'); container.classList.add(band(score));
      elRing.style.setProperty('--v',score); elFill.style.setProperty('--p',score);
      elNum.textContent = String(score);
    }
    function setSpMeter(barEl, valEl, raw, score, fmt, meterWrap){
      valEl.textContent = raw==null ? '—' : (fmt ? fmt(raw) : raw);
      barEl.style.width = clamp01(score) + '%';
      if(meterWrap){ meterWrap.classList.remove('good','warn','bad'); meterWrap.classList.add(band(score)); }
    }
    const scoreFromBounds=(val,good,poor)=>{
      if(val==null||isNaN(val)) return 0;
      if(val<=good) return 100;
      if(val>=poor) return 0;
      return Math.round(100 * (1 - ((val - good) / (poor - good))));
    };

    /* endpoints (unchanged) */
    async function callAnalyzer(url){
      const headers={'Accept':'application/json','Content-Type':'application/json'};
      let res=await fetch('/api/semantic-analyze',{method:'POST',headers,body:JSON.stringify({url,target_keyword:''})});
      if(res.ok) return res.json();
      if([404,405,419].includes(res.status)){
        res=await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url,target_keyword:''})});
        if(res.ok) return res.json();
      }
      const txt=await res.text(); throw new Error(`HTTP ${res.status}\n${txt?.slice(0,800)}`);
    }
    async function callPSI(url){
      const res=await fetch('/semantic-analyzer/psi',{method:'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url})});
      const text=await res.text();
      let json={}; try{ json=JSON.parse(text); }catch{ throw new Error(`PSI: invalid JSON\n${text?.slice(0,400)}`) }
      if(json.ok===false) throw new Error(json.error||json.message||'PSI unavailable');
      if(!res.ok) throw new Error(json.error||json.message||`PSI HTTP ${res.status}`);
      return json;
    }

    /* robust metric reading */
    const toNum=v=> (typeof v==='number'?v : (typeof v==='string' && v.trim() && !isNaN(+v)? +v : null));
    function firstNum(...vals){ for(const v of vals){ const n=toNum(v); if(n!=null) return n; } return null; }
    function deepFindCategoryScore(root, token){
      if(!root||typeof root!=='object') return null;
      let out=null;
      const walk=o=>{
        if(!o||out!==null||typeof o!=='object') return;
        for(const k in o){
          const v=o[k];
          const key=(k||'').toLowerCase();
          if((key.includes(token)) && v && typeof v==='object' && ('score' in v)){
            const n=toNum(v.score); if(n!=null){ out=n; return; }
          }
          walk(v);
        }
      };
      walk(root); return out;
    }
    function getCategoryScore(psi, token){
      // Prefer desktop -> mobile -> root
      let s = deepFindCategoryScore(psi?.desktop,'categories') ?? deepFindCategoryScore(psi?.desktop, token);
      if(s==null) s = deepFindCategoryScore(psi?.mobile,'categories') ?? deepFindCategoryScore(psi?.mobile, token);
      if(s==null) s = deepFindCategoryScore(psi, 'categories') ?? deepFindCategoryScore(psi, token);
      if(s!=null && s<=1) s = s*100;
      return s!=null ? Math.round(s) : null;
    }

    async function runAnalyze(e){
      e?.preventDefault?.();
      clearError();
      const url=(urlInput.value||'').trim();
      if(!url){ showError('Please enter a URL.'); return; }

      psiStatus.textContent='Checking…';
      [ringMobile,ringDesktop].forEach(el=>el.style.setProperty('--v',0));
      [fillMobile,fillDesktop].forEach(el=>el.style.setProperty('--p',0));
      [mwMobile,mwDesktop].forEach(c=>{c.classList.remove('good','warn','bad');c.classList.add('warn')});
      numMobile.textContent='M 0%'; numDesktop.textContent='D 0%';

      [ringPerf,ringAcc,ringBest,ringSEO].forEach(el=>el.style.setProperty('--v',0));
      [fillPerf,fillAcc,fillBest,fillSEO].forEach(el=>el.style.setProperty('--p',0));
      [mwPerf,mwAcc,mwBest,mwSEO].forEach(c=>{c.classList.remove('good','warn','bad');c.classList.add('warn')});
      [numPerf,numAcc,numBest,numSEO].forEach(el=>el.textContent='—');

      [lcpBar,clsBar,inpBar,ttfbBar].forEach(el=>el.style.width='0%');
      [lcpVal,clsVal,inpVal,ttfbVal].forEach(el=>el.textContent='—');
      psiFixes.innerHTML='<li>Fetching PageSpeed data…</li>';

      try{
        await callAnalyzer(url);

        const psi=await callPSI(url);
        const mobile=psi.mobile||{}, desktop=psi.desktop||{};

        const mScore=clamp01(Math.round(mobile.score  ?? mobile.performance ?? 0));
        const dScore=clamp01(Math.round(desktop.score ?? desktop.performance ?? 0));
        setWheel(ringMobile,fillMobile,numMobile,mwMobile,mScore,'M');
        setWheel(ringDesktop,fillDesktop,numDesktop,mwDesktop,dScore,'D');

        // mini 4 wheels (prefer Lighthouse desktop categories)
        const perf = getCategoryScore(psi,'performance') ?? dScore ?? mScore;
        const acc  = getCategoryScore(psi,'accessibility');
        const best = getCategoryScore(psi,'best-practices') ?? getCategoryScore(psi,'best_practices');
        const seo  = getCategoryScore(psi,'seo');

        if(perf!=null) setWheelNumberOnly(ringPerf,fillPerf,numPerf,mwPerf,clamp01(perf));
        if(acc!=null)  setWheelNumberOnly(ringAcc,fillAcc,numAcc,mwAcc,clamp01(acc));
        if(best!=null) setWheelNumberOnly(ringBest,fillBest,numBest,mwBest,clamp01(best));
        if(seo!=null)  setWheelNumberOnly(ringSEO,fillSEO,numSEO,mwSEO,clamp01(seo));

        // metrics
        const lcpSeconds = firstNum(mobile.lcp_s, desktop.lcp_s, psi.lcp_s, psi.metrics?.lcp_s) ?? (firstNum(mobile.lcp, desktop.lcp, psi.lcp, psi.metrics?.lcp)/1000 || null);
        const cls        = firstNum(mobile.cls, desktop.cls, psi.cls, psi.metrics?.cls);
        const inp        = firstNum(mobile.inp_ms, desktop.inp_ms, psi.inp_ms, psi.metrics?.inp_ms);
        const ttfb       = firstNum(mobile.ttfb_ms, desktop.ttfb_ms, psi.ttfb_ms, psi.metrics?.ttfb_ms, psi.ttfb);

        const sLCP  = (v=>v==null?0:scoreFromBounds(v,2.5,6.0))(lcpSeconds);
        const sCLS  = (v=>v==null?0:scoreFromBounds(v,0.10,0.25))(cls);
        const sINP  = (v=>v==null?0:scoreFromBounds(v,200,500))(inp);
        const sTTFB = (v=>v==null?0:scoreFromBounds(v,800,1800))(ttfb);

        setSpMeter(lcpBar, lcpVal,   lcpSeconds, sLCP,  v => v!=null ? v.toFixed(2)+' s' : '—', lcpMeter);
        setSpMeter(clsBar, clsVal,   cls,        sCLS,  v => v!=null ? v.toFixed(3)      : '—', clsMeter);
        setSpMeter(inpBar, inpVal,   inp,        sINP,  v => v!=null ? Math.round(v)+' ms' : '—', inpMeter);
        setSpMeter(ttfbBar, ttfbVal, ttfb,       sTTFB, v => v!=null ? Math.round(v)+' ms' : '—', ttfbMeter);

        const tips=[];
        if(lcpSeconds!=null && lcpSeconds>2.5) tips.push('Improve LCP: preload hero image, inline critical CSS, compress hero media (AVIF/WebP).');
        if(cls!=null && cls>0.1) tips.push('Reduce CLS: reserve dimensions for images/ads; avoid layout shifts from late-loading UI.');
        if(inp!=null && inp>200) tips.push('Lower INP: split long JS tasks; defer non-critical; limit third-party scripts.');
        if(ttfb!=null && ttfb>800) tips.push('Reduce TTFB: CDN + caching, HTTP/2 or HTTP/3, server optimizations.');
        if(!tips.length) tips.push('Great job! Keep images optimized and JS lean to maintain fast performance.');
        psiFixes.innerHTML=tips.map(t=>`<li>✅ ${t}</li>`).join('');

        const topBand=(mScore>=80&&dScore>=80)?'good':((mScore>=60||dScore>=60)?'warn':'bad');
        psiStatus.className='pill '+(topBand==='good'?'score-pill--green':topBand==='warn'?'score-pill--orange':'score-pill--red');
        psiStatus.textContent=topBand==='good'?'🎉 Excellent Speed':topBand==='warn'?'OK':'Needs Work';

      }catch(err){
        console.error(err);
        psiStatus.textContent='Unavailable';
        psiFixes.innerHTML=`<li>⚠️ ${String(err.message||err)}</li>`;
      }
    }

    analyzeBtn?.addEventListener('click', runAnalyze);
    window.__SemAnalyze = runAnalyze;
  };

  if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',init,{once:true});}
  else{init();}
})();
</script>
@endpush

@section('content')
<section class="maxw px-4 pb-10">
  <!-- Title -->
  <div class="title-wrap">
    <div class="king">👑</div>
    <div style="text-align:center">
      <div class="t-grad" style="font-size:26px;line-height:1.1;">Semantic SEO Master Analyzer</div>
      <div class="byline">By <span class="shoail">Shoail Kahoker</span></div>
    </div>
  </div>

  <!-- Legend -->
  <div class="legend"><span class="badge g">Green ≥ 80</span><span class="badge o">Orange 60–79</span><span class="badge r">Red &lt; 60</span></div>

  <!-- Analyze -->
  <div class="analyze-wrap" style="margin-top:12px;">
    <div class="url-row">
      <span style="opacity:.75">🌐</span>
      <input id="urlInput" name="url" type="url" placeholder="https://example.com/page" />
      <button id="pasteBtn" type="button" class="paste" onclick="navigator.clipboard.readText().then(t=>{if(t)document.getElementById('urlInput').value=t.trim()}).catch(()=>{})">Paste</button>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-top:10px">
      <label style="display:flex;align-items:center;gap:8px;font-size:12px">
        <input id="autoCheck" type="checkbox" checked/> Auto-apply checkmarks (≥ 80)
      </label>
      <div style="flex:1"></div>
      <button id="analyzeBtn" type="button" class="btn btn-green" onclick="return window.__SemAnalyze && window.__SemAnalyze(event)">🔍 Analyze</button>
    </div>
    <div id="errorBox"></div>
  </div>

  <!-- ===== Site Speed & Core Web Vitals ===== -->
  <div class="speed-card" id="speedCard">
    <div class="sp-head">
      <div class="sp-title">
        <div class="ico">⚡</div>
        <div>
          <div class="t-grad" style="font-weight:900;">Site Speed & Core Web Vitals</div>
          <div class="sp-note">Uses PageSpeed Insights (Mobile + Desktop)</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:6px"><span id="psiStatus" class="pill">Waiting…</span></div>
    </div>

    <!-- Big wheels centered -->
    <div class="sp-wheels">
      <div class="wheel-card">
        <div class="mw mw-xs warn" id="mwMobile">
          <div class="mw-ring" id="ringMobile" style="--v:0"></div>
          <div class="mw-fill" id="fillMobile" style="--p:0"></div>
          <div class="mw-center" id="numMobile">M 0%</div>
        </div>
        <div class="wheel-label">Mobile</div>
      </div>
      <div class="wheel-card">
        <div class="mw mw-xs warn" id="mwDesktop">
          <div class="mw-ring" id="ringDesktop" style="--v:0"></div>
          <div class="mw-fill" id="fillDesktop" style="--p:0"></div>
          <div class="mw-center" id="numDesktop">D 0%</div>
        </div>
        <div class="wheel-label">Desktop</div>
      </div>
    </div>

    <!-- NEW: four small category wheels -->
    <div class="sp-mini">
      <div class="mini-card">
        <div class="mw mw-xxs warn" id="mwPerf">
          <div class="mw-ring" id="ringPerf"></div>
          <div class="mw-fill" id="fillPerf"></div>
          <div class="mw-center" id="numPerf">—</div>
        </div>
        <div class="mini-label">Performance</div>
      </div>
      <div class="mini-card">
        <div class="mw mw-xxs warn" id="mwAcc">
          <div class="mw-ring" id="ringAcc"></div>
          <div class="mw-fill" id="fillAcc"></div>
          <div class="mw-center" id="numAcc">—</div>
        </div>
        <div class="mini-label">Accessibility</div>
      </div>
      <div class="mini-card">
        <div class="mw mw-xxs warn" id="mwBest">
          <div class="mw-ring" id="ringBest"></div>
          <div class="mw-fill" id="fillBest"></div>
          <div class="mw-center" id="numBest">—</div>
        </div>
        <div class="mini-label">Best Practices</div>
      </div>
      <div class="mini-card">
        <div class="mw mw-xxs warn" id="mwSEO">
          <div class="mw-ring" id="ringSEO"></div>
          <div class="mw-fill" id="fillSEO"></div>
          <div class="mw-center" id="numSEO">—</div>
        </div>
        <div class="mini-label">SEO</div>
      </div>
    </div>

    <!-- Metric bars -->
    <div class="sp-grid">
      <div class="sp-tile">
        <div class="sp-row"><div>🏁 LCP (s)</div><div class="sp-val" id="lcpVal">—</div></div>
        <div class="sp-meter" id="lcpMeter"><span id="lcpBar" style="width:0%"></span></div>
      </div>
      <div class="sp-tile">
        <div class="sp-row"><div>📦 CLS</div><div class="sp-val" id="clsVal">—</div></div>
        <div class="sp-meter" id="clsMeter"><span id="clsBar" style="width:0%"></span></div>
      </div>
      <div class="sp-tile">
        <div class="sp-row"><div>⚡ INP (ms)</div><div class="sp-val" id="inpVal">—</div></div>
        <div class="sp-meter" id="inpMeter"><span id="inpBar" style="width:0%"></span></div>
      </div>
      <div class="sp-tile">
        <div class="sp-row"><div>⏱️ TTFB (ms)</div><div class="sp-val" id="ttfbVal">—</div></div>
        <div class="sp-meter" id="ttfbMeter"><span id="ttfbBar" style="width:0%"></span></div>
      </div>
    </div>

    <div class="sp-fixes">
      <h4>💡 Speed Suggestions</h4>
      <ul id="psiFixes"><li>Run Analyze to fetch PSI data.</li></ul>
    </div>
  </div>
  <!-- ===== /Site Speed & CWV ===== -->
</section>
@endsection
