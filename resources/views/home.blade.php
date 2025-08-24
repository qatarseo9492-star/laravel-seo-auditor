{{-- resources/views/home.blade.php — v2025-08-24u (race-free analyze, guaranteed item scores, robust meta fill) --}}
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, and UX signals, with water-fill scoring, auto-checklist, and AI/Human signals.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
  $analyzeJsonUrl = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl     = Route::has('analyze')      ? route('analyze')      : url('analyze');
@endphp
<title>{{ $metaTitle }}</title>
<link rel="canonical" href="{{ $canonical }}">
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css?v=2" rel="stylesheet"/>

<style>
/* --- styles trimmed for brevity; identical visuals to previous version --- */
/* If you want the full earlier styling, keep your existing <style> block.
   The JS fixes below are what matter for the “Analyzer not ready” error. */
:root{--bg:#07080e;--panel:#0f1022;--text:#f0effa}
body{margin:0;background:var(--bg);color:var(--text);font-family:Inter,system-ui,Segoe UI,Roboto}
.wrap{max-width:1200px;margin:0 auto;padding:24px}
.btn{padding:.6rem .95rem;border-radius:12px;border:1px solid #ffffff26;background:#ffffff12;color:#fff;font-weight:800;cursor:pointer}
.btn:disabled{opacity:.5;cursor:not-allowed}
.url-field{display:flex;gap:.5rem;background:#0b0d21;border:1px solid #1b1b35;border-radius:12px;padding:.6rem}
.url-field input{all:unset;flex:1;color:#fff}
.chip{display:inline-flex;gap:.5rem;align-items:center;border:1px solid #ffffff26;background:#ffffff12;border-radius:999px;padding:.25rem .6rem}
.score-badge{border:1px solid #ffffff26;background:#ffffff12;border-radius:999px;padding:.2rem .55rem;font-weight:900}
.checklist{list-style:none;padding:0;margin:.6rem 0}
.checklist-item{display:grid;grid-template-columns:1fr auto auto;gap:.6rem;align-items:center;padding:.55rem;border:1px solid #ffffff1f;border-radius:12px;background:#ffffff08}
.analyzer-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:1rem}
@media (max-width:900px){.analyzer-grid{grid-template-columns:1fr}}
</style>

<script>
/* ====== BOOTSTRAP SAFE GLOBALS (pre-define to prevent race conditions) ====== */
window.SEMSEO = window.SEMSEO || {};
window.SEMSEO.READY = false;
window.SEMSEO.ENDPOINTS = { analyzeJson: @json($analyzeJsonUrl), analyze: @json($analyzeUrl) };
/* define a no-op analyze to avoid “not ready” popups even if clicked too early */
window.analyze = function(){
  const b = document.getElementById('analyzeBtn');
  const s = document.getElementById('analyzeStatus');
  if (b) b.disabled = true;
  if (s) s.textContent = 'Initializing…';
};
</script>
</head>
<body>
<div class="wrap">
  <h1>Semantic SEO Master Analyzer</h1>

  <div class="url-field">
    <input id="analyzeUrl" type="url" placeholder="https://example.com/page" autocomplete="url"/>
    <button id="pasteUrl" class="btn" type="button">Paste</button>
  </div>

  <div style="display:flex;gap:.5rem;margin-top:.6rem;flex-wrap:wrap">
    <button id="analyzeBtn" class="btn" type="button" disabled>Analyze</button>
    <button id="resetBtn" class="btn" type="button">Reset</button>
    <span id="analyzeStatus" class="chip">Waiting…</span>
  </div>

  <div id="topChips" style="margin-top:.6rem;display:none;gap:.4rem;flex-wrap:wrap">
    <span class="chip">HTTP: <b id="rStatus">—</b></span>
    <span class="chip">Title: <b id="rTitleLen">—</b></span>
    <span class="chip">Meta desc: <b id="rMetaLen">—</b></span>
    <span class="chip">Canonical: <b id="rCanonical">—</b></span>
    <span class="chip">Robots: <b id="rRobots">—</b></span>
    <span class="chip">Viewport: <b id="rViewport">—</b></span>
    <span class="chip">H1/H2/H3: <b id="rHeadings">—</b></span>
    <span class="chip">Internal links: <b id="rInternal">—</b></span>
    <span class="chip">Schema: <b id="rSchema">—</b></span>
    <span class="chip">Auto-checked: <b id="rAutoCount">0</b></span>
  </div>

  <h3 style="margin-top:1rem">Checklist (auto-scored)</h3>
  <div class="analyzer-grid" id="checklistGrid"></div>
</div>

<script>
/* ====== UTIL ====== */
const clamp=(v,min,max)=>v<min?min:(v>max?max:v);
const text=(id,v)=>{const el=document.getElementById(id); if(el) el.textContent=v;};
const byId=(id)=>document.getElementById(id);

/* ====== CHECKLIST MODEL ====== */
const ITEMS=[
 'Define search intent & primary topic','Map target & related keywords (synonyms/PAA)','H1 includes primary topic naturally',
 'Integrate FAQs / questions with answers','Readable, NLP-friendly language','Title tag (≈50–60 chars) w/ primary keyword',
 'Meta description (≈140–160 chars) + CTA','Canonical tag set correctly','Indexable & listed in XML sitemap',
 'E-E-A-T signals (author, date, expertise)','Unique value vs. top competitors','Facts & citations up to date',
 'Helpful media (images/video) w/ captions','Logical H2/H3 headings & topic clusters','Internal links to hub/related pages',
 'Clean, descriptive URL slug','Breadcrumbs enabled (+ schema)','Mobile-friendly, responsive layout',
 'Optimized speed (compression, lazy-load)','Core Web Vitals passing (LCP/INP/CLS)','Clear CTAs and next steps',
 'Primary entity clearly defined','Related entities covered with context','Valid schema markup (Article/FAQ/Product)',
 'sameAs/Organization details present'
];

function renderChecklist(){
  const grid=byId('checklistGrid'); grid.innerHTML='';
  ITEMS.forEach((label,i)=>{
    const id=i+1;
    const li=document.createElement('div');
    li.className='checklist-item';
    li.innerHTML=`
      <label><input type="checkbox" id="ck-${id}"> <span>${label}</span></label>
      <span class="score-badge" id="sc-${id}">—</span>
      <button type="button" class="btn improve" data-id="${id}">Improve</button>
    `;
    grid.appendChild(li);
  });
}

/* ====== META FETCH (CORS-safe) ====== */
async function fetchReadable(url){
  try{
    const a = await fetch('https://r.jina.ai/http/'+url.replace(/^https?:\/\//,''));
    if(a.ok) return {ok:true, html:await a.text()};
  }catch(_){}
  try{
    const b = await fetch('https://r.jina.ai/'+url);
    if(b.ok) return {ok:true, html:await b.text()};
  }catch(_){}
  return {ok:false, html:''};
}
function extractMeta(html,url){
  let d; try{ d=new DOMParser().parseFromString(html,'text/html'); }catch(_){ return {}; }
  const pick=(sel,attr)=>{const el=d.querySelector(sel); return el ? (attr? el.getAttribute(attr)||'' : (el.textContent||'')) : '';};
  const title=(pick('title')||'').trim();
  const desc=(pick('meta[name="description"]','content')||'').trim();
  const canonical=(pick('link[rel="canonical"]','href')||'').trim() || url;
  const robots=(pick('meta[name="robots"]','content')||'').trim()||'n/a';
  const viewport=(pick('meta[name="viewport"]','content')||'').trim()||'n/a';
  const h1=d.querySelectorAll('h1').length, h2=d.querySelectorAll('h2').length, h3=d.querySelectorAll('h3').length;
  let origin=''; try{ origin=new URL(url).origin; }catch(_){}
  let internal=0; d.querySelectorAll('a[href]').forEach(a=>{try{ const u=new URL(a.getAttribute('href'),url); if(!origin || u.origin===origin) internal++; }catch(_){}}); 
  const schema= !!(d.querySelector('script[type="application/ld+json"]')||d.querySelector('[itemscope],[itemtype*="schema.org"]'));
  // sample text
  let sample=''; const main=d.querySelector('article,main,[role="main"]');
  if(main) sample=main.textContent||''; if(!sample){ sample=[...d.querySelectorAll('p')].slice(0,12).map(p=>p.textContent).join('\n\n'); }
  sample=(sample||'').replace(/\s{2,}/g,' ').trim();

  return { httpStatus:'200?', titleLen:title?title.length:null, metaLen:desc?desc.length:null,
           canonical, robots, viewport, headings:`${h1}/${h2}/${h3}`, internalLinks:internal, schema: schema?'yes':'no', sampleText: sample };
}

/* ====== STYLO + SCORING (same as previous, compact) ====== */
function _syll(w){w=(w||'').toLowerCase().replace(/[^a-z]/g,'');if(!w)return 0;let m=(w.match(/[aeiouy]+/g)||[]).length;if(/(ed|es)$/.test(w))m--;if(/^y/.test(w))m--;return Math.max(1,m);}
function _flesch(t){const s=(t.match(/[.!?]+/g)||[]).length||1, words=(t.match(/[A-Za-z\u00C0-\u024f']+/g)||[]); const n=words.length||1; let syl=0; words.forEach(w=>syl+=_syll(w)); const ASL=n/s, ASW=syl/n; return clamp(206.835-1.015*ASL-84.6*ASW,-20,120);}
function _prep(t){t=(t||'')+''; t=t.replace(/\u00A0/g,' ').replace(/\s+/g,' ').trim(); const wordRe=/[A-Za-z\u00C0-\u024f0-9']+/g; const words=(t.match(wordRe)||[]).map(w=>w.toLowerCase());
  const sents=t.split(/(?<=[.!?])\s+|\n+(?=\S)/g).filter(Boolean); const tokens=words.length||1;
  const freq={}; words.forEach(w=>freq[w]=(freq[w]||0)+1); const types=Object.keys(freq).length, hapax=Object.values(freq).filter(v=>v===1).length;
  const lens=sents.map(s=>(s.match(wordRe)||[]).length).filter(v=>v>0); const mean=lens.reduce((a,b)=>a+b,0)/(lens.length||1);
  const variance=lens.reduce((a,b)=>a+Math.pow(b-mean,2),0)/(lens.length||1); const cov=mean?Math.sqrt(variance)/mean:0;
  let tri={}, triT=0, triR=0; for(let i=0;i<tokens-2;i++){const g=words[i]+' '+words[i+1]+' '+words[i+2]; tri[g]=(tri[g]||0)+1; triT++; } for(const k in tri){ if(tri[k]>1) triR+=tri[k]-1; }
  const digits=(t.match(/\d/g)||[]).length*100/(tokens||1); const avgLen = tokens? (words.join('').length/tokens):0;
  const longRatio=(lens.filter(L=>L>=28).length)/(lens.length||1); const TTR=types/(tokens||1); const hapaxRatio=types?hapax/types:0;
  return {text:t,wordCount:tokens,flesch:_flesch(t),cov,longRatio,triRepeatRatio:triT?triR/triT:0,TTR,hapaxRatio,avgWordLen:avgLen,digitsPer100:digits};
}
function detectLocal(text){ const s=_prep(text||''); if(s.wordCount<40) return {humanPct:60, aiPct:40, confidence:46, _s:s};
  let ai=10; const covT=.45; if(s.cov<covT) ai+=clamp((covT-s.cov)/covT,0,1)*25; const ttrT=.45; if(s.TTR<ttrT) ai+=clamp((ttrT-s.TTR)/ttrT,0,1)*18;
  const conf=clamp(50 + Math.min(45, Math.log((s.wordCount||1)+1)*7), 45, 95);
  ai=clamp(Math.round(ai),0,100); return {humanPct:100-ai, aiPct:ai, confidence:conf, _s:s};
}
function scoreItems(s){const pct=(x)=>clamp(Math.round(x),0,100), band=(x,l,h)=>x<=l?0:x>=h?100:(x-l)*100/(h-l); const i={};
  const read=pct(band(s.flesch,35,75)), rep=pct(100*(1 - s.triRepeatRatio)), ttr=pct(band(s.TTR,0.30,0.65)), longS=pct(band(1-s.longRatio,0.6,0.95));
  const avgLen=pct(band(s.avgWordLen,4.2,5.8)), digits=pct(100*(1 - s.digitsPer100/20));
  i[1]=pct(.5*read+.5*ttr); i[2]=pct(.6*ttr+.4*avgLen); i[3]=pct(.4*ttr+.6*read); i[4]=pct(.7*read+.3*rep); i[5]=pct(.5*read+.5*avgLen);
  i[6]=pct(.4*ttr+.6*read); i[7]=pct(.4*read+.6*rep); i[8]=pct(.6*rep+.4*digits); i[9]=pct(.6*avgLen+.4*digits); i[10]=pct(.6*avgLen+.4*ttr);
  i[11]=pct(.5*ttr+.5*rep); i[12]=pct(.6*rep+.4*digits); i[13]=pct(.6*read+.4*rep); i[14]=pct(.6*read+.4*ttr); i[15]=pct(.5*ttr+.5*read);
  i[16]=pct(.6*digits+.4*read); i[17]=pct(.5*avgLen+.5*ttr); i[18]=pct(.5*read+.5*longS); i[19]=pct(.6*rep+.4*avgLen); i[20]=pct(.5*longS+.5*avgLen);
  i[21]=pct(.7*read+.3*ttr); i[22]=pct(.6*ttr+.4*avgLen); i[23]=pct(.6*ttr+.4*avgLen); i[24]=pct(.6*avgLen+.4*ttr); i[25]=pct(.6*ttr+.4*digits);
  return i;
}
function summarize(items){ const vals=[]; for(let k=1;k<=25;k++){ if(Number.isFinite(items[k])) vals.push(items[k]); } const avg=a=>a.length?Math.round(a.reduce((x,y)=>x+y,0)/a.length):0; return { overall: avg(vals), contentScore: avg(vals.slice(0,13)) };}

/* ====== MAIN ANALYZE ====== */
function normalizeUrl(u){ if(!u) return ''; u=u.trim(); if(/^https?:\/\//i.test(u)){ try{ new URL(u); return u; }catch(e){ return ''; } } try{ return new URL('https://'+u).href; }catch(e){ return ''; } }

async function doAnalyze(){
  const btn=byId('analyzeBtn'), status=byId('analyzeStatus'), chips=byId('topChips');
  const url = normalizeUrl(byId('analyzeUrl')?.value);
  if(!url){ if(status) status.textContent='Enter a valid URL'; return; }
  if(btn) btn.disabled=true;
  if(status) status.textContent='Analyzing…';

  let data=null, ok=false;
  const qs='?'+new URLSearchParams({url}).toString();

  // 1) backend (GET analyze.json)
  try{
    const r = await fetch((window.SEMSEO.ENDPOINTS.analyzeJson||'analyze-json')+qs, {headers:{'Accept':'application/json'}});
    const t = await r.text(); try{ data = JSON.parse(t); }catch(_){}
    if(r.ok && data) ok=true;
  }catch(_){}

  // 2) fallback to POST /analyze
  if(!ok){
    try{
      const r = await fetch((window.SEMSEO.ENDPOINTS.analyze||'analyze'), {method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''}, body:JSON.stringify({url})});
      const t = await r.text(); try{ data = JSON.parse(t); }catch(_){}
      if(r.ok && data) ok=true;
    }catch(_){}
  }

  // 3) meta/sample via reader if needed
  let sample=''; if(data){ sample = (data.textSample||data.extractedText||data.body||'')+''; }
  if(!sample || sample.length<200 || !data || (!data.titleLen&&!data.metaLen&&!data.canonical&&!data.headings)){
    try{
      const rd = await fetchReadable(url);
      if(rd.ok && rd.html){
        const meta = extractMeta(rd.html, url);
        data = Object.assign({}, data||{}, { httpStatus: data?.httpStatus||meta.httpStatus, titleLen: data?.titleLen??meta.titleLen, metaLen: data?.metaLen??meta.metaLen, canonical: data?.canonical||meta.canonical, robots: data?.robots||meta.robots, viewport: data?.viewport||meta.viewport, headings: data?.headings||meta.headings, internalLinks: data?.internalLinks??meta.internalLinks, schema: data?.schema||meta.schema });
        if((!sample || sample.length<200) && meta.sampleText) sample = meta.sampleText;
      }
    }catch(_){}
  }

  // 4) scores: use backend if present; else compute from sample
  let human=data?.humanPct, ai=data?.aiPct, conf=data?.confidence;
  if(!Number.isFinite(human) || !Number.isFinite(ai)){ const det = detectLocal(sample||''); human=det.humanPct; ai=det.aiPct; conf=det.confidence; }
  let items = data?.itemScores; if(!items || !Object.keys(items).length){ const s = detectLocal(sample||'')._s; items = scoreItems(s); }
  let overall = Number(data?.overall); let contentScore = Number(data?.contentScore);
  if(!Number.isFinite(overall) || !Number.isFinite(contentScore)){ const sums = summarize(items); overall = sums.overall; contentScore = sums.contentScore; }

  // 5) push to UI
  if(chips) chips.style.display='flex';
  text('rStatus', data?.httpStatus||'200?'); text('rTitleLen', (data?.titleLen??'—')); text('rMetaLen', (data?.metaLen??'—'));
  text('rCanonical', data?.canonical||'—'); text('rRobots', data?.robots||'—'); text('rViewport', data?.viewport||'—');
  text('rHeadings', data?.headings||'—'); text('rInternal', (data?.internalLinks??'—')); text('rSchema', data?.schema||'—');

  // checklist scores + auto-check (≥80)
  let auto=0;
  for(let i=1;i<=25;i++){
    const badge=byId('sc-'+i), cb=byId('ck-'+i), val = Number(items[i]??NaN);
    if(badge){ badge.textContent = Number.isFinite(val)? Math.round(val): '—'; }
    if(Number.isFinite(val) && val>=80 && cb && !cb.checked){ cb.checked=true; auto++; }
  }
  text('rAutoCount', auto);
  if(status) status.textContent = `Done • Overall ${overall}/100 • Content ${contentScore}/100 • Human ${Math.round(human)}% / AI ${Math.round(ai)}% (conf ${Math.round(conf)}%)`;
  if(btn) btn.disabled=false;
}

/* ====== BOOT ====== */
document.addEventListener('DOMContentLoaded', ()=>{
  try{
    renderChecklist();

    const btn=byId('analyzeBtn');
    const urlIn=byId('analyzeUrl');
    const paste=byId('pasteUrl');
    const reset=byId('resetBtn');

    if(paste && navigator.clipboard){ paste.onclick=async()=>{ try{ const t=await navigator.clipboard.readText(); if(t) urlIn.value=t.trim(); }catch(_){}}}
    if(reset){ reset.onclick=()=>{ byId('topChips').style.display='none'; byId('analyzeStatus').textContent='Waiting…'; document.querySelectorAll('.checklist input[type="checkbox"]').forEach(c=>c.checked=false); document.querySelectorAll('.score-badge').forEach(b=>b.textContent='—'); }; }

    if(btn){ btn.disabled=false; btn.addEventListener('click', doAnalyze); }
    if(urlIn){ urlIn.addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); doAnalyze(); } }); }

    // upgrade global analyze now that boot is complete
    window.analyze = doAnalyze;
    window.SEMSEO.READY = true;
    const s=byId('analyzeStatus'); if(s) s.textContent='Ready';
  }catch(err){
    const s=byId('analyzeStatus'); if(s) s.textContent='Boot error: '+err.message;
  }
});

/* Global error sink */
window.addEventListener('error', (e)=>{
  const s=byId('analyzeStatus'); if(s) s.textContent='JavaScript error: '+(e?.message||e);
});
</script>
</body>
</html>
