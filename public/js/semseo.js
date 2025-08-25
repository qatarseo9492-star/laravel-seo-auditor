/* public/js/semseo.js — CSP/Rocket-Loader safe bundle */

/* ===== helpers ===== */
(function(){
  const $  = (sel,root)=> (root||document).querySelector(sel);
  const $$ = (sel,root)=> Array.from((root||document).querySelectorAll(sel));
  const CSRF = $('meta[name="csrf-token"]')?.getAttribute('content') || '';

  // endpoints from meta tags (no inline JSON)
  const EP = {
    analyzeJson: $('meta[name="semseo-analyze-json"]')?.content || '/analyze-json',
    analyze:     $('meta[name="semseo-analyze"]')?.content      || '/analyze',
    psi:         $('meta[name="semseo-psi"]')?.content          || '/api/pagespeed',
  };
  window.SEMSEO = { ENDPOINTS: EP, READY:false, BUSY:false };

  const setText = (id, val)=>{ const el = document.getElementById(id); if(el) el.textContent = val; return el; };
  const setHTML = (id, html)=>{ const el = document.getElementById(id); if(el) el.innerHTML  = html; return el; };
  const clamp   = (v,a,b)=> v<a?a:(v>b?b:v);

  function setChipTone(el, v){
    if(!el) return;
    el.classList.remove('chip-good','chip-mid','chip-bad');
    const n = Number(v)||0;
    el.classList.add(n>=80?'chip-good':(n>=60?'chip-mid':'chip-bad'));
  }
  function badgeTone(el, v){
    if(!el) return;
    el.classList.remove('score-good','score-mid','score-bad');
    el.classList.add(v>=80?'score-good':(v>=60?'score-mid':'score-bad'));
  }

  /* ====== Gauge ====== */
  const GAUGE = { H:200, CIRC:2*Math.PI*95 };
  function setScoreWheel(value){
    if(!GAUGE.rect){
      GAUGE.rect = $('#scoreClipRect');
      GAUGE.stop1= $('#scoreStop1'); GAUGE.stop2= $('#scoreStop2');
      GAUGE.r1   = $('#ringStop1');  GAUGE.r2   = $('#ringStop2');
      GAUGE.arc  = $('#ringArc');    GAUGE.text = $('#overallScore');
      if(GAUGE.arc){ GAUGE.arc.style.strokeDasharray = GAUGE.CIRC.toFixed(2); GAUGE.arc.style.strokeDashoffset = GAUGE.CIRC.toFixed(2); }
    }
    const v = clamp(Number(value)||0, 0, 100);
    const y = GAUGE.H - (GAUGE.H * (v/100));
    GAUGE.rect?.setAttribute('y', String(y));
    if (GAUGE.text) GAUGE.text.textContent = Math.round(v)+'%';
    let c1,c2; if(v>=80){c1='#22c55e';c2='#16a34a'} else if(v>=60){c1='#f59e0b';c2='#fb923c'} else {c1='#ef4444';c2='#b91c1c'}
    GAUGE.stop1?.setAttribute('stop-color',c1); GAUGE.stop2?.setAttribute('stop-color',c2);
    GAUGE.r1?.setAttribute('stop-color',c1); GAUGE.r2?.setAttribute('stop-color',c2);
    if(GAUGE.arc){ const offset=GAUGE.CIRC*(1-(v/100)); GAUGE.arc.style.strokeDashoffset=offset.toFixed(2); }
    setText('overallScoreInline', Math.round(v));
    setChipTone($('#overallChip'), v);
  }
  window.setScoreWheel = setScoreWheel;

  /* ====== Category progress ====== */
  function updateCategoryBars(){
    const cards = $$('.category-card');
    let total=0, checked=0;
    cards.forEach(card=>{
      const items = $$('.checklist-item', card);
      total += items.length;
      checked += items.filter(li => $('input', li)?.checked).length;
    });
    const pctAll = total? Math.round(checked*100/total) : 0;
    $('#compClipRect')?.setAttribute('width', String(6*pctAll));
    setText('compPct', pctAll+'%');
    setText('progressCaption', checked+' of '+total+' items completed');
  }
  window.updateCategoryBars = updateCategoryBars;

  /* ====== Water progress ====== */
  const Water = (function(){
    let t=null, value=0;
    function show(){ const w=$('#waterWrap'); if(w) w.style.display='block'; }
    function hide(){ const w=$('#waterWrap'); if(w) w.style.display='none'; }
    function set(v){ value=clamp(v,0,100); const y=200 - (200*value/100); $('#waterClipRect')?.setAttribute('y', String(y)); const p=$('#waterPct'); if(p) p.textContent=Math.round(value)+'%'; }
    return {
      start(){ show(); set(0); if(t) clearInterval(t); t=setInterval(()=>{ if(value<88) set(value+2); }, 80); },
      finish(){ if(t) clearInterval(t); setTimeout(()=>set(100),150); setTimeout(hide,800); },
      reset(){ if(t) clearInterval(t); set(0); hide(); },
    };
  })();
  window.Water = Water;

  /* ====== Text analysis helpers ====== */
  function _countSyllables(word){ const w=(word||'').toLowerCase().replace(/[^a-z]/g,''); if(!w) return 0; let m=(w.match(/[aeiouy]+/g)||[]).length; if(/(ed|es)$/.test(w)) m--; if(/^y/.test(w)) m--; return Math.max(1,m); }
  function _flesch(text){ const sents=(text.match(/[.!?]+/g)||[]).length||1; const words=(text.match(/[A-Za-z\u00C0-\u024f']+/g)||[]); const wN=words.length||1; let syll=0; for(let i=0;i<words.length;i++) syll+=_countSyllables(words[i]); return clamp(206.835 - 1.015*(wN/sents) - 84.6*(syll/wN), -20, 120); }
  function _prep(text){
    text=(text||'')+''; text=text.replace(/\u00A0/g,' ').replace(/\s+/g,' ').trim();
    const wordRe=/[A-Za-z\u00C0-\u024f0-9']+/g; const words=(text.match(wordRe)||[]).map(w=>w.toLowerCase());
    const sents=text.split(/(?<=[.!?])\s+|\n+(?=\S)/g).filter(Boolean); const tokens=words.length||1;
    const freq=Object.create(null); words.forEach(w=>{freq[w]=(freq[w]||0)+1;});
    const types=Object.keys(freq).length; let hapax=0; for(const k in freq){ if(freq[k]===1) hapax++; }
    const lens=sents.map(s=>(s.match(wordRe)||[]).length).filter(v=>v>0);
    const mean=lens.reduce((a,b)=>a+b,0)/(lens.length||1);
    const variance=lens.reduce((a,b)=>a+Math.pow(b-mean,2),0)/(lens.length||1);
    const cov=mean?Math.sqrt(variance)/mean:0;
    const tri={}, triT=(()=>{let t=0; for(let i=0;i<tokens-2;i++){ const g=words[i]+' '+words[i+1]+' '+words[i+2]; tri[g]=(tri[g]||0)+1; t++; } return t; })();
    let triR=0; for(const kk in tri){ if(tri[kk]>1) triR+=tri[kk]-1; }
    const digits=(text.match(/\d/g)||[]).length*100/(tokens||1);
    const avgLen=tokens? (words.join('').length/tokens):0;
    const longRatio=(lens.filter(L=>L>=28).length)/(lens.length||1);
    const TTR=types/(tokens||1);
    return { text, wordCount:tokens, flesch:_flesch(text), cov, longRatio, triRepeatRatio: triT?triR/triT:0, TTR, hapaxRatio: types?hapax/types:0, avgWordLen:avgLen, digitsPer100:digits };
  }
  function detectUltra(text){
    const s=_prep(text||''); if (s.wordCount < 40){ const aiQuick = clamp(70 - s.wordCount*0.8, 20, 70); return { humanPct: 100-aiQuick, aiPct: aiQuick, confidence: 46, detectors: [] , _s:s }; }
    let ai=10; const covT=0.45; if(s.cov<covT) ai+=clamp((covT-s.cov)/covT,0,1)*25; const ttrT=0.45; if(s.TTR<ttrT) ai+=clamp((ttrT-s.TTR)/ttrT,0,1)*18;
    const conf = clamp(50 + Math.min(45, Math.log((s.wordCount||1)+1)*7), 45, 95);
    return { humanPct: 100-clamp(Math.round(ai),0,100), aiPct: clamp(Math.round(ai),0,100), confidence: conf, detectors: [{key:'stylometry',label:'Stylometry',ai:clamp(Math.round(ai),0,100),w:1}], _s:s };
  }
  function extractEntities(sample){
    const text=(sample||'').replace(/\s+/g,' ').trim(); const result={ People:[], Orgs:[], Places:[], Software:[], Games:[], Other:[] }; if(!text) return result;
    const caps = text.match(/\b([A-Z][a-z]+(?:\s+[A-Z][a-z]+){0,3})\b/g) || []; const freq={}; caps.forEach(t=>{freq[t]=(freq[t]||0)+1;});
    const top=Object.keys(freq).sort((a,b)=>freq[b]-freq[a]).slice(0,80);
    top.forEach(t=>{
      if (/\b(inc|ltd|corp|llc|gmbh|company|studio)\b/i.test(t)) result.Orgs.push(t);
      else if (/\b(city|town|province|state|country|park|river|lake)\b/i.test(t)) result.Places.push(t);
      else if (/\b(software|suite|editor|studio|cloud|api|sdk|framework|wordpress|laravel|android|ios|windows|linux|mac)\b/i.test(t)) result.Software.push(t);
      else if (/\b(game|games|studios)\b/i.test(t)) result.Games.push(t);
      else if (/\s/.test(t)) result.People.push(t); else result.Other.push(t);
    });
    const apkHits=(text.match(/\bapk\b|\b\.apk\b/ig)||[]).length; if(apkHits>0 && !result.Software.includes('APK')) result.Software.unshift('APK');
    const uniq = arr => { const s=new Set(); const out=[]; arr.forEach(v=>{ const k=v.trim(); if(!s.has(k) && k.length>1){ s.add(k); out.push(k); } }); return out.slice(0,20); };
    Object.keys(result).forEach(k=> result[k]=uniq(result[k]));
    return result;
  }

  function gradeFromFlesch(f){ if (f>=90) return {label:'Very Easy', hint:'Great for all audiences', pct:100}; if (f>=70) return {label:'Easy', hint:'Good web reading', pct:88}; if (f>=60) return {label:'Fairly Easy', hint:'Still fine', pct:78}; if (f>=50) return {label:'Plain', hint:'OK. Keep sentences short', pct:66}; if (f>=30) return {label:'Fairly Difficult', hint:'Use simpler words', pct:48}; return {label:'Difficult', hint:'Shorten sentences, simplify vocab', pct:34}; }
  const pctBand=(x,l,h)=> x<=l?0:(x>=h?100:Math.round((x-l)*100/(h-l)));
  function renderReadability(s){
    const grid = $('#readGrid'); if(!grid) return; grid.innerHTML='';
    const items=[];
    const g=gradeFromFlesch(s.flesch); items.push({ icon:'fa-book', label:'Flesch Score', value: Math.round(s.flesch), pct: clamp(g.pct,0,100), hint:g.hint });
    items.push({ icon:'fa-random', label:'Sentence Variation (CoV inverse)', value: (1-s.cov).toFixed(2), pct: clamp(Math.round((1-s.cov)*100),0,100), hint:'Balance sentence lengths' });
    items.push({ icon:'fa-font', label:'Type-Token Ratio (TTR)', value: s.TTR.toFixed(2), pct: pctBand(s.TTR,0.30,0.65), hint:'Vary vocabulary' });
    items.push({ icon:'fa-align-left', label:'Long Sentences', value: Math.round(s.longRatio*100)+'%', pct: clamp(Math.round((1-s.longRatio)*100),0,100), hint:'Split sentences ≥28 words' });
    items.push({ icon:'fa-text-height', label:'Avg Word Length', value: s.avgWordLen.toFixed(2), pct: pctBand(6 - s.avgWordLen, 0.2, 2.0), hint:'Prefer shorter words' });
    items.push({ icon:'fa-hashtag', label:'Digits per 100 words', value: s.digitsPer100.toFixed(1), pct: clamp(Math.round(100 - s.digitsPer100*5),0,100), hint:'Reduce numeric noise' });
    items.push({ icon:'fa-layer-group', label:'Trigram Repetition', value: (s.triRepeatRatio*100).toFixed(1)+'%', pct: clamp(Math.round(100 - s.triRepeatRatio*100),0,100), hint:'Avoid repetitive phrasing' });
    items.push({ icon:'fa-file-alt', label:'Word Count', value: s.wordCount, pct: clamp(Math.round(Math.min(1, s.wordCount/1200)*100),0,100), hint:'Target 800–1500 words (topic dependent)' });
    items.forEach(it=>{
      const card=document.createElement('div'); card.className='read-card';
      card.innerHTML = `<div class="read-row">
        <div class="left"><i class="fa ${it.icon}"></i> <b>${it.label}</b></div>
        <div class="read-bar"><div class="read-fill" style="width:${it.pct}%"></div></div>
        <div class="badge-mini">${it.value}</div>
      </div><div style="color:var(--text-dim);margin-top:.3rem"><i class="fa-regular fa-lightbulb"></i> ${it.hint}</div>`;
      grid.appendChild(card);
    });
  }

  function tagHTML(txt){
    const g = encodeURIComponent(txt), w = encodeURIComponent(txt.replace(/\s+/g,'_'));
    return `<span class="tag"><i class="fa-solid fa-tag"></i>${txt}
      <a href="https://www.google.com/search?q=${g}" target="_blank" rel="noopener" title="Google"><i class="fa-brands fa-google"></i></a>
      <a href="https://en.wikipedia.org/wiki/${w}" target="_blank" rel="noopener" title="Wikipedia"><i class="fa-brands fa-wikipedia-w"></i></a>
    </span>`;
  }
  function renderEntities(ents){
    const grid=$('#entitiesGrid'); if(!grid) return; grid.innerHTML='';
    const groups=[{k:'People',icon:'fa-user',color:'#22c55e'},{k:'Orgs',icon:'fa-building',color:'#60a5fa'},{k:'Places',icon:'fa-location-dot',color:'#f59e0b'},{k:'Software',icon:'fa-code',color:'#a78bfa'},{k:'Games',icon:'fa-gamepad',color:'#fb7185'},{k:'Other',icon:'fa-shapes',color:'#94a3b8'}];
    groups.forEach(g=>{
      const list=ents[g.k]||[]; if(!list.length) return;
      const card=document.createElement('div'); card.className='ent-card';
      card.innerHTML=`<div class="chip" style="border-color:${g.color};background:color-mix(in srgb,${g.color} 18%, transparent)"><i class="fa ${g.icon}"></i> ${g.k}</div>`;
      const wrap=document.createElement('div'); list.forEach(t=> wrap.innerHTML += tagHTML(t)); card.appendChild(wrap); grid.appendChild(card);
    });
  }

  /* ===== PSI helpers ===== */
  function kpiTone(el, val, type){
    let good=false, mid=false;
    if(type==='perf'){ good=val>=90; mid=val>=50 && val<90; }
    if(type==='lcp'){ good=val<2500; mid=val<4000 && val>=2500; }
    if(type==='inp'){ good=val<200; mid=val<500 && val>=200; }
    if(type==='cls'){ good=val<0.1;  mid=val<0.25 && val>=0.1; }
    el.classList.remove('good','mid','bad'); el.classList.add(good?'good':(mid?'mid':'bad'));
  }
  const fmtMs    = x=> Math.round(x) + ' ms';
  const fmtScore = x=> Math.round((x||0)*100);

  async function runPSI(url, strategy){
    const base = (window.SEMSEO?.ENDPOINTS?.psi)||'/api/pagespeed';
    async function hit(param){
      const ep = `${base}?${param}=${encodeURIComponent(url)}&strategy=${encodeURIComponent(strategy||'mobile')}`;
      const r  = await fetch(ep,{headers:{'Accept':'application/json'}});
      const j  = await r.json().catch(()=> ({}));
      if(!r.ok) throw new Error((j && j.error) ? j.error : `PSI failed (${r.status})`);
      return j;
    }
    try{
      // First try ?url=
      return await hit('url');
    }catch(e){
      // If proxy expects ?u=, retry
      if(String(e.message||e).toLowerCase().includes('missing ?u')) {
        try { return await hit('u'); } catch(e2){ return { error: e2.message||String(e2) }; }
      }
      return { error: e.message||String(e) };
    }
  }
  function applyPSIToSide(j, side){
    const perf=$(`#${side}Perf`), lcp=$(`#${side}LCP`), inp=$(`#${side}INP`), cls=$(`#${side}CLS`), hints=$(`#${side}Hints`);
    [perf,lcp,inp,cls].forEach(el=>{ if(el){ $('b',el).textContent='—'; el.classList.remove('good','mid','bad'); }});
    if(!j || j.error){ setHTML(`${side}Hints`,`<div class="chip" style="border-color:#ef4444;background:rgba(239,68,68,.15)"><i class="fa-solid fa-circle-exclamation"></i> ${(j?.error)||'Error'}</div>`); return; }
    const lh=j.lighthouseResult||{}, score=(lh.categories&&lh.categories.performance&&lh.categories.performance.score)||0, audits=lh.audits||{};
    let LCP=(audits['largest-contentful-paint']&&audits['largest-contentful-paint'].numericValue)||null;
    let INP=(audits['interaction-to-next-paint']&&audits['interaction-to-next-paint'].numericValue)||null;
    let CLS=(audits['cumulative-layout-shift']&&audits['cumulative-layout-shift'].numericValue)||null;
    const le=j.loadingExperience||j.originLoadingExperience||{}, m=le.metrics||{};
    if(!LCP && m.LARGEST_CONTENTFUL_PAINT_MS){ LCP=m.LARGEST_CONTENTFUL_PAINT_MS.percentile; }
    if(!INP && (m.INTERACTION_TO_NEXT_PAINT || m.FIRST_INPUT_DELAY_MS)){ INP=(m.INTERACTION_TO_NEXT_PAINT&&m.INTERACTION_TO_NEXT_PAINT.percentile)||(m.FIRST_INPUT_DELAY_MS&&m.FIRST_INPUT_DELAY_MS.percentile)||null; }
    if(!CLS && m.CUMULATIVE_LAYOUT_SHIFT_SCORE){ CLS=m.CUMULATIVE_LAYOUT_SHIFT_SCORE.percentile/100; }
    $('b',perf).textContent=fmtScore(score); kpiTone(perf, Math.round(score*100), 'perf');
    if(LCP!=null){ $('b',lcp).textContent=fmtMs(LCP); kpiTone(lcp, LCP, 'lcp'); }
    if(INP!=null){ $('b',inp).textContent=fmtMs(INP); kpiTone(inp, INP, 'inp'); }
    if(CLS!=null){ $('b',cls).textContent=(Math.round(CLS*1000)/1000).toFixed(3); kpiTone(cls, CLS, 'cls'); }
    const tips=[]; const pushIf=(code,label,suggest)=>{ const a=audits[code]; if(a && a.score<0.9){ tips.push(`<div class="chip"><i class="fa-regular fa-lightbulb"></i> ${label}: <b>${suggest}</b></div>`);} };
    pushIf('unused-javascript','Reduce JS','Code-split & remove unused JS');
    pushIf('render-blocking-resources','Eliminate render-blocking','Preload critical CSS/JS');
    pushIf('uses-optimized-images','Optimize images','Use AVIF/WebP + proper sizes');
    pushIf('server-response-time','Server response','Cache + faster hosting');
    pushIf('total-blocking-time','Blocking time','Defer non-critical scripts');
    pushIf('cumulative-layout-shift','Layout shifts','Reserve media space & avoid late ads');
    hints.innerHTML = tips.join(' ') || '<div class="chip"><i class="fa-solid fa-circle-check" style="color:#22c55e"></i> No major suggestions.</div>';
  }
  async function runPageSpeedBoth(url){
    setText('psiTime', new Date().toLocaleTimeString());
    ['mobile','desktop'].forEach(side=>{
      ['Perf','LCP','INP','CLS'].forEach(k=>{
        const el=$(`#${side}${k}`); if(el){ $('b',el).innerHTML='<span class="skel" style="display:inline-block;min-width:80px"></span>'; }
      });
      setHTML(`${side}Hints`,'<div class="skel" style="height:16px;width:70%;border-radius:10px"></div>');
    });
    const [m,d]=await Promise.all([runPSI(url,'mobile'), runPSI(url,'desktop')]);
    applyPSIToSide(m,'mobile'); applyPSIToSide(d,'desktop');
  }

  /* ====== Merge helpers ====== */
  function normalizeUrl(u){
    if(!u) return '';
    u=u.trim();
    if(/^https?:\/\//i.test(u)){ try{ new URL(u); return u; }catch{ return ''; } }
    const guess='https://'+u.replace(/^\/+/, '');
    try{ new URL(guess); return guess; }catch{ return ''; }
  }
  async function fetchBackend(url){
    let data=null, ok=false, status=0, text='';
    const qs=new URLSearchParams({url}).toString();
    try{ const r1=await fetch((EP.analyzeJson||'/analyze-json')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); status=r1.status; text=await r1.text(); try{ data=JSON.parse(text);}catch{} if(r1.ok && data) ok=true; }catch{}
    if(!ok){ try{ const r2=await fetch((EP.analyze||'/analyze'),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},body:JSON.stringify({url,_token:CSRF})}); status=r2.status; text=await r2.text(); try{ data=JSON.parse(text);}catch{} if(r2.ok && data) ok=true; }catch{}
    if(!ok){ try{ const r3=await fetch((EP.analyze||'/analyze')+'?'+qs,{headers:{'Accept':'application/json','X-Requested-With':'XMLHttpRequest'}}); status=r3.status; text=await r3.text(); try{ data=JSON.parse(text);}catch{} if(r3.ok && data) ok=true; }catch{}
    return {ok,data,status};
  }
  async function fetchRawHtml(url){ try{ const r=await fetch('https://api.allorigins.win/raw?url='+encodeURIComponent(url),{cache:'no-store'}); if(r.ok){ const html=await r.text(); if(html && html.length>200) return html; } }catch{} return ''; }
  async function fetchReadableText(url){
    try{ const httpsR=await fetch('https://r.jina.ai/http/'+url.replace(/^https?:\/\//,'')); if(httpsR.ok){ const t=await httpsR.text(); if(t && t.length>200) return t; } }catch{}
    try{ const altR=await fetch('https://r.jina.ai/'+url); if(altR.ok){ const t=await altR.text(); if(t && t.length>200) return t; } }catch{}
    return '';
  }
  function extractMetaFromHtml(html, baseUrl){
    try{
      const d=(new DOMParser()).parseFromString(html,'text/html');
      const q=(s,a)=>{const el=d.querySelector(s);return el?(a?el.getAttribute(a)||'':(el.textContent||'')) : '';};
      const title=(q('title')||'').trim();
      const metaDesc=(q('meta[name="description"]','content')||'').trim();
      const canonical=(q('link[rel="canonical"]','href')||'').trim()||baseUrl;
      const robots=(q('meta[name="robots"]','content')||'').trim()||'n/a';
      const viewport=(q('meta[name="viewport"]','content')||'').trim()||'n/a';
      const h1=d.querySelectorAll('h1').length, h2=d.querySelectorAll('h2').length, h3=d.querySelectorAll('h3').length;
      let origin=''; try{ origin=new URL(baseUrl).origin; }catch{}
      let internal=0; d.querySelectorAll('a[href]').forEach(a=>{ try{ const u=new URL(a.getAttribute('href'), baseUrl); if(!origin || u.origin===origin) internal++; }catch{} });
      const schema = !!(d.querySelector('script[type="application/ld+json"]') || d.querySelector('[itemscope],[itemtype*="schema.org"]'));
      const main=d.querySelector('article,main,[role="main"]'); let sample=main? (main.textContent||''): '';
      if(!sample){ sample=Array.from(d.querySelectorAll('p')).slice(0,12).map(p=>p.textContent).join('\n\n'); }
      sample=(sample||'').replace(/\s{2,}/g,' ').trim();
      return { titleLen: title?title.length:null, metaLen: metaDesc?metaDesc.length:null, canonical, robots, viewport, headings:(h1+'/'+h2+'/'+h3), internalLinks:internal, schema: schema?'yes':'no', sampleText: sample };
    }catch{ return {}; }
  }
  function mergeMeta(into, add){
    if(!into) into={};
    ['titleLen','metaLen','canonical','robots','viewport','headings','internalLinks','schema','sampleText'].forEach(k=>{
      if((into[k]===undefined || into[k]===null || into[k]==='—' || into[k]==='') && add && add[k]!==undefined && add[k]!==null) into[k]=add[k];
    });
    return into;
  }
  function deriveItemScoresFromSignals(s){
    const band=(x,l,h)=> x<=l?0:(x>=h?100:(x-l)*100/(h-l)), pct=x=> clamp(Math.round(x),0,100);
    const read=pct(band(s.flesch,35,75)), rep=pct(100*(1 - s.triRepeatRatio)), ttr=pct(band(s.TTR,0.30,0.65)), longS=pct(band(1-s.longRatio, 0.6, 0.95)), avgLen=pct(band(s.avgWordLen,4.2,5.8)), digits=pct(100*(1 - s.digitsPer100/20));
    const i=[]; i[1]=pct(.5*read+.5*ttr); i[2]=pct(.6*ttr+.4*avgLen); i[3]=pct(.4*ttr+.6*read); i[4]=pct(.7*read+.3*rep); i[5]=pct(.5*read+.5*avgLen);
    i[6]=pct(.4*ttr+.6*read); i[7]=pct(.4*read+.6*rep); i[8]=pct(.6*rep+.4*digits); i[9]=pct(.6*avgLen+.4*digits); i[10]=pct(.6*avgLen+.4*ttr);
    i[11]=pct(.5*ttr+.5*rep); i[12]=pct(.6*rep+.4*digits); i[13]=pct(.6*read+.4*rep); i[14]=pct(.6*read+.4*ttr); i[15]=pct(.5*ttr+.5*read);
    i[16]=pct(.6*digits+.4*read); i[17]=pct(.5*avgLen+.5*ttr); i[18]=pct(.5*read+.5*longS); i[19]=pct(.6*rep+.4*avgLen); i[20]=pct(.5*longS+.5*avgLen);
    i[21]=pct(.7*read+.3*ttr); i[22]=pct(.6*ttr+.4*avgLen); i[23]=pct(.6*ttr+.4*avgLen); i[24]=pct(.6*avgLen+.4*ttr); i[25]=pct(.6*ttr+.4*digits);
    const map={}; for(let k=1;k<=25;k++) map[k]=i[k]; return map;
  }
  function deriveSummaryScoresFromItems(itemMap){
    const all=[]; for(let i=1;i<=25;i++){ if(isFinite(itemMap[i])) all.push(itemMap[i]); }
    const avg = a => a.length? Math.round(a.reduce((x,y)=>x+y,0)/a.length) : 0;
    return { contentScore: avg(all.slice(0,13)), overall: avg(all) };
  }
  function buildSampleFromData(data){
    const parts=[]; ['textSample','extractedText','plainText','body','sample','content','text'].forEach(k=>{ if(typeof data?.[k]==='string' && data[k].length>0) parts.push(data[k]); });
    ['title','meta','description','ogDescription','firstParagraph','snippet','h1','h2','h3'].forEach(k=>{ const v=data?.[k]; if (typeof v === 'string' && v.trim()) parts.push(v); if (Array.isArray(v)) parts.push(v.join('. ')); });
    const txt=parts.join('\n\n').replace(/\s{2,}/g,' ').trim(); return txt.length>140000 ? txt.slice(0,140000) : txt;
  }
  function ensureScoresExist(data, sample, ensemble){
    const needItems=!data.itemScores || Object.keys(data.itemScores).length===0;
    const needContent=typeof data.contentScore!=='number' || isNaN(data.contentScore);
    const needOverall=typeof data.overall!=='number' || isNaN(data.overall);
    const s=(ensemble && ensemble._s)? ensemble._s : _prep(sample||'');
    if(needItems) data.itemScores=deriveItemScoresFromSignals(s);
    if(needContent || needOverall){ const sums=deriveSummaryScoresFromItems(data.itemScores||{}); if(needContent) data.contentScore=sums.contentScore; if(needOverall) data.overall=sums.overall; }
    return data;
  }

  function renderDetectors(res){
    const grid=$('#detGrid'), confEl=$('#detConfidence');
    if(confEl) confEl.textContent = isFinite(res.confidence)? Math.round(res.confidence): '—';
    if(!grid) return; grid.innerHTML='';
    (res.detectors||[{key:'stylometry',label:'Stylometry',ai:res.aiPct||0}]).forEach(d=>{
      const ai=clamp(d.ai||0,0,100), human=clamp(100-ai,0,100);
      const wrap=document.createElement('div'); wrap.className='det-item';
      wrap.innerHTML=`<div class="det-row"><div class="det-label"><i class="fa-solid fa-wave-square"></i> ${d.label}</div><div class="det-score">${human}% H / ${ai}% AI</div></div>
      <div class="det-bar"><div class="det-fill-human" style="width:${human}%"></div><div class="det-fill-ai" style="width:${ai}%"></div></div>`;
      grid.appendChild(wrap);
    });
  }
  function applyDetection(humanPct, aiPct, confidence, breakdown){
    const hp=isFinite(humanPct)? Math.round(humanPct) : NaN;
    const ap=isFinite(aiPct)? Math.round(aiPct) : NaN;
    if(isFinite(hp)){ setText('humanPct', hp); setText('hvaiHumanPct', hp); $('#hvaiHuman').style.width=hp+'%'; }
    if(isFinite(ap)){ setText('aiPct',  ap); setText('hvaiAIPct',  ap); $('#hvaiAI').style.width=ap+'%'; }
    const writer=(isFinite(hp)&&isFinite(ap)&&hp>=ap)? 'Likely Human' : 'AI-like';
    const badge=$('#aiBadge'); if(badge && $('b',badge)) $('b',badge).textContent=writer;
    if(breakdown && breakdown.detectors){ renderDetectors(breakdown); } else { $('#detGrid').innerHTML=''; }
  }

  function autoTickByScores(map){
    let autoCount=0;
    for(let i=1;i<=25;i++){
      const scVal=Number((map && map[i]!==undefined)? map[i] : NaN);
      const badge=$(`#sc-${i}`);
      const cb   = $(`#ck-${i}`);
      const row  = cb ? cb.closest('.checklist-item') : null;
      if(!badge) continue;
      if(!isNaN(scVal)){
        badge.textContent = Math.round(scVal);
        badgeTone(badge, scVal);
        if ($('#autoApply')?.checked && scVal>=80) {
          if (cb && !cb.checked) { cb.checked=true; autoCount++; }
          row?.classList.remove('sev-mid','sev-bad'); row?.classList.add('sev-good');
        } else if (scVal>=60) { row?.classList.remove('sev-bad','sev-good'); row?.classList.add('sev-mid'); }
        else { row?.classList.remove('sev-mid','sev-good'); row?.classList.add('sev-bad'); }
      } else {
        badge.textContent='—'; badge.classList.remove('score-good','score-mid','score-bad');
      }
    }
    setText('rAutoCount', autoCount);
    updateCategoryBars();
  }
  window.autoTickByScores = autoTickByScores;

  /* ===== Analyze ===== */
  async function analyze(){
    if (window.SEMSEO.BUSY) return;
    window.SEMSEO.BUSY = true;
    const statusEl = $('#analyzeStatus');

    try{
      const input = $('#analyzeUrl');
      const url   = normalizeUrl(input ? input.value : '');
      if(!url){
        if (statusEl) statusEl.textContent = 'Please enter a valid URL (include http/https).';
        input && input.focus();
        return;
      }

      try{ localStorage.setItem('last_url', url); }catch{}
      Water?.start();
      statusEl && (statusEl.textContent='Fetching & analyzing…');
      const rep0 = $('#analyzeReport'); if(rep0 && rep0.style) rep0.style.display = 'none';

      let {ok,data} = await fetchBackend(url); if(!data) data = {};
      let sample = buildSampleFromData(data);

      try{
        const raw = await fetchRawHtml(url);
        if(raw){
          const meta = extractMetaFromHtml(raw, url);
          data = mergeMeta(data, meta);
          if((!sample || sample.length<200) && meta.sampleText) sample = meta.sampleText;
        }
      }catch{}

      if((!sample || sample.length<200)){
        try{
          const read = await fetchReadableText(url);
          if(read && read.length>200) sample = read;
        }catch{}
      }

      const ensemble = (sample && sample.length>30) ? detectUltra(sample) : null;
      data = ensureScoresExist(data, sample, ensemble);

      const overall = Number(data.overall||0), contentScore = Number(data.contentScore||0);
      setScoreWheel(overall||0);
      setText('contentScoreInline', Math.round(contentScore||0));
      setChipTone($('#contentScoreChip'), contentScore||0);

      setText('rStatus',   data.httpStatus ? data.httpStatus : '200?');
      setText('rTitleLen', (data.titleLen!==undefined && data.titleLen!==null)? data.titleLen : '—');
      setText('rMetaLen',  (data.metaLen !==undefined && data.metaLen !==null)? data.metaLen  : '—');
      setText('rCanonical',data.canonical ? data.canonical : '—');
      setText('rRobots',   data.robots    ? data.robots    : '—');
      setText('rViewport', data.viewport  ? data.viewport  : '—');
      setText('rHeadings', data.headings  ? data.headings  : '—');
      setText('rInternal', (data.internalLinks!==undefined && data.internalLinks!==null)? data.internalLinks : '—');
      setText('rSchema',   data.schema    ? data.schema    : '—');

      const hp = (typeof data.humanPct==='number')? data.humanPct : NaN;
      const ap = (typeof data.aiPct==='number')? data.aiPct : NaN;
      const backendConf = (typeof data.confidence==='number')? data.confidence : null;

      if (isFinite(hp) && isFinite(ap) && backendConf && backendConf>=65){
        applyDetection(hp,ap,backendConf,ensemble||null);
      } else if (ensemble){
        applyDetection(ensemble.humanPct,ensemble.aiPct,ensemble.confidence,ensemble);
      } else if (isFinite(hp) && isFinite(ap)){
        applyDetection(hp,ap,backendConf||60,null);
      } else {
        applyDetection(NaN,NaN,null,null);
      }

      const s = (ensemble && ensemble._s)? ensemble._s : _prep(sample||'');
      renderReadability(s);
      renderEntities(extractEntities(sample||''));
      autoTickByScores(data.itemScores||{});

      Water?.finish();
      statusEl && (statusEl.textContent='Analysis complete');
      const rep = $('#analyzeReport'); if(rep) rep.style.display='block';

      // PSI auto-run
      setTimeout(()=>{ if(EP.psi && url) runPageSpeedBoth(url); }, 60);

    } catch(err){
      statusEl && (statusEl.textContent = 'Analyze error: ' + (err && err.message ? err.message : err));
      try{ Water?.reset(); }catch{}
    } finally {
      window.SEMSEO.BUSY = false;
    }
  }
  window.analyze = analyze;

  /* ===== UI bindings ===== */
  document.addEventListener('DOMContentLoaded', ()=>{
    try{
      // Analyze button — no inline handlers
      $('#analyzeBtn')?.addEventListener('click', e=>{ e.preventDefault(); analyze(); });

      const input=$('#analyzeUrl');
      input?.addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); analyze(); }});
      $('#clearUrl')?.addEventListener('click', ()=>{ if(input){ input.value=''; input.focus(); } });
      if (navigator.clipboard){ $('#pasteUrl')?.addEventListener('click', async ()=>{ try{ const t=await navigator.clipboard.readText(); if(t && input){ input.value=t.trim(); } }catch{} }); }

      $('#runMobile') ?.addEventListener('click', async ()=>{ const u=normalizeUrl($('#analyzeUrl')?.value||''); if(!u) return; setText('psiTime', new Date().toLocaleTimeString()); const m=await runPSI(u,'mobile');  applyPSIToSide(m,'mobile'); });
      $('#runDesktop')?.addEventListener('click', async ()=>{ const u=normalizeUrl($('#analyzeUrl')?.value||''); if(!u) return; setText('psiTime', new Date().toLocaleTimeString()); const d=await runPSI(u,'desktop'); applyPSIToSide(d,'desktop'); });
      $('#runBoth')   ?.addEventListener('click', async ()=>{ const u=normalizeUrl($('#analyzeUrl')?.value||''); if(!u) return; runPageSpeedBoth(u); });

      document.addEventListener('change', e=>{
        if(e.target && e.target.matches('.checklist input[type="checkbox"]')) updateCategoryBars();
      });

      $('#resetChecklist')?.addEventListener('click', ()=>{
        $$('.checklist input[type="checkbox"]').forEach(cb=>cb.checked=false);
        $$('.score-badge').forEach(b=>{ b.textContent='—'; b.classList.remove('score-good','score-mid','score-bad'); });
        updateCategoryBars(); setScoreWheel(0);
        ['contentScoreInline','humanPct','aiPct','hvaiHumanPct','hvaiAIPct'].forEach(id=>{ const el=document.getElementById(id); if(el) el.textContent='—'; });
        const badge=$('#aiBadge'); if(badge && $('b',badge)) $('b',badge).textContent='—';
        $('#detGrid').innerHTML=''; $('#readGrid').innerHTML=''; $('#entitiesGrid').innerHTML='';
        $('#hvaiHuman').style.width='50%'; $('#hvaiAI').style.width='50%';
        Water?.reset();
      });

      $('#exportChecklist')?.addEventListener('click', ()=>{
        const payload={ checked:[], scores:{} };
        for(let i=1;i<=25;i++){
          const cb=$(`#ck-${i}`), sc=$(`#sc-${i}`);
          if(cb && cb.checked) payload.checked.push(i);
          const s=parseInt(sc ? sc.textContent : 'NaN',10); if(!isNaN(s)) payload.scores[i]=s;
        }
        const blob=new Blob([JSON.stringify(payload,null,2)],{type:'application/json'});
        const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='checklist.json'; a.click(); URL.revokeObjectURL(a.href);
      });
      $('#importChecklist')?.addEventListener('click', ()=> $('#importFile')?.click());
      $('#importFile')?.addEventListener('change', function(){
        const file=this.files?.[0]; if(!file) return;
        const fr=new FileReader(); fr.onload=function(){ try{
          const data=JSON.parse(fr.result);
          for(let i=1;i<=25;i++){
            const cb=$(`#ck-${i}`); if(cb) cb.checked=(data.checked||[]).includes(i);
            const sc=$(`#sc-${i}`); const val=data.scores ? data.scores[i] : undefined;
            if(sc && typeof val==='number'){ sc.textContent=val; badgeTone(sc,val); }
          }
          updateCategoryBars();
        }catch{ alert('Invalid JSON'); } }; fr.readAsText(file);
      });

      $('#printTop')?.addEventListener('click', ()=> window.print());
      $('#printChecklist')?.addEventListener('click', ()=> window.print());

      const backTop=$('#backTop');
      window.addEventListener('scroll', ()=>{ if(backTop) backTop.style.display=(window.scrollY>500)?'grid':'none'; }, {passive:true});
      backTop?.addEventListener('click', ()=> window.scrollTo({top:0,behavior:'smooth'}));

      try{ const last=localStorage.getItem('last_url'); if(last && $('#analyzeUrl')) $('#analyzeUrl').value=last; }catch{}
      window.SEMSEO.READY = true;
    }catch(err){
      const s=$('#analyzeStatus'); if(s) s.textContent='Boot error: '+err.message;
    }
  });

  /* ===== Background FX (moved to external file) ===== */
  window.addEventListener('DOMContentLoaded', ()=>{
    try{
      (function(){
        const c=document.getElementById('linesCanvas'); if(!c) return; const ctx=c.getContext('2d'); const dpr=Math.min(2,window.devicePixelRatio||1);
        function resize(){ c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0) }
        function draw(t){ ctx.clearRect(0,0,window.innerWidth,window.innerHeight); const w=window.innerWidth,h=window.innerHeight,rows=16,spacing=Math.max(54,h/rows);
          for(let i=-2;i<rows+2;i++){ const y=i*spacing+((t*0.025)%spacing); const g=ctx.createLinearGradient(0,y,w,y+90);
            g.addColorStop(0,'rgba(61,226,255,0.14)'); g.addColorStop(0.5,'rgba(155,92,255,0.16)'); g.addColorStop(1,'rgba(255,32,69,0.14)');
            ctx.strokeStyle=g; ctx.lineWidth=1.5; ctx.beginPath(); ctx.moveTo(-120,y); ctx.lineTo(w+120,y+90); ctx.stroke(); }
          requestAnimationFrame(draw);
        }
        window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
      })();
      (function(){
        const c=document.getElementById('smokeCanvas'); if(!c) return; const ctx=c.getContext('2d');
        const dpr=Math.min(2,window.devicePixelRatio||1); let blobs=[], last=performance.now();
        function resize(){
          c.width=Math.floor(window.innerWidth*dpr); c.height=Math.floor(window.innerHeight*dpr); ctx.setTransform(dpr,0,0,dpr,0,0);
          const W=window.innerWidth, H=window.innerHeight; const N=64;
          blobs=new Array(N).fill(0).map((_,i)=>{
            const px=W*0.65 + Math.random()*W*0.45, py=H*0.65 + Math.random()*H*0.45, r=120+Math.random()*260, speed=0.18+Math.random()*0.22;
            return { x:px, y:py, r:r, vx:-speed*(0.6+Math.random()*0.8), vy:-speed*(0.6+Math.random()*0.8), baseHue:(i*37)%360, alpha:.22+.22*Math.random() };
          });
          last=performance.now();
        }
        function draw(now){
          const W=window.innerWidth, H=window.innerHeight; ctx.clearRect(0,0,W,H); ctx.globalCompositeOperation='screen';
          const dt=now-last; last=now;
          for(let i=0;i<blobs.length;i++){
            const b=blobs[i]; b.x+=b.vx*dt; b.y+=b.vy*dt; if(b.x<-360 || b.y<-360){ b.x=W+Math.random()*260; b.y=H+Math.random()*260; }
            const hue=(b.baseHue + (now % 1000000000) * (360/1000000000)) % 360;
            const g=ctx.createRadialGradient(b.x,b.y,0,b.x,b.y,b.r); g.addColorStop(0,`hsla(${hue},88%,68%,${b.alpha})`); g.addColorStop(1,`hsla(${(hue+70)%360},88%,50%,0)`);
            ctx.fillStyle=g; ctx.beginPath(); ctx.arc(b.x,b.y,b.r,0,Math.PI*2); ctx.fill();
          }
          requestAnimationFrame(draw);
        }
        window.addEventListener('resize',resize,{passive:true}); resize(); requestAnimationFrame(draw);
      })();
    }catch(e){ const s=$('#analyzeStatus'); if(s) s.textContent='FX error: '+e.message; }
  });

  window.addEventListener('error', e=>{
    const s=document.getElementById('analyzeStatus');
    if (s) s.textContent = 'JavaScript error: ' + (e && e.message ? e.message : e);
  });
})();
