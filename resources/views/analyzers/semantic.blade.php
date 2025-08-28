@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@section('content')
<style>
  /* Page theme + utilities */
  :root{
    --bg-1:#0b1220;--bg-2:#0f1630;--bg-3:#121631;
    --brand:#7c3aed; --brand2:#22d3ee; --ok:#10b981; --warn:#f59e0b; --bad:#ef4444;
    --muted:#a1a1aa; --panel:#111827; --panelBorder:rgba(255,255,255,.08);
  }
  body{ background: radial-gradient(1200px 600px at 110% -10%, rgba(34,211,238,.22), transparent 65%),
                 radial-gradient(800px 600px at -10% 120%, rgba(124,58,237,.18), transparent 65%),
                 linear-gradient(180deg, var(--bg-1), var(--bg-2)); }
  .glass{ background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.04)); border:1px solid var(--panelBorder); backdrop-filter: blur(10px); }
  .chip{ font-size:.7rem; padding:.25rem .5rem; border-radius:.5rem; border:1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.06); }
  .title-grad{ background:linear-gradient(90deg,#22d3ee,#7c3aed 45%,#f43f5e 80%); -webkit-background-clip:text; background-clip:text; color:transparent; }
  .shadow-soft{ box-shadow: 0 14px 60px rgba(0,0,0,.22); }

  /* Score wheel */
  .wheel svg{ overflow:visible; }
  .wheel .track{ stroke: rgba(255,255,255,.12); }
  .wheel .bar{ stroke-linecap: round; filter: drop-shadow(0 4px 14px rgba(124,58,237,.35)); }

  /* Water bar canvas wraps */
  .water-wrap{ position:relative; overflow:hidden; border-radius:16px; border:1px solid var(--panelBorder); }
  .water-wrap canvas{ display:block; width:100%; height:100%; }
  .water-overlay{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-weight:800; letter-spacing:.5px; }

  /* Checklist */
  .check-item{ display:flex; align-items:flex-start; gap:.75rem; padding:.6rem .8rem; border-radius:14px; border:1px solid var(--panelBorder); background:rgba(255,255,255,.04); }
  .check-score{ min-width:46px; padding:.25rem .5rem; font-weight:700; border-radius:10px; text-align:center; font-size:.8rem; }
  .glow-ok{ box-shadow:0 0 0 4px rgba(16,185,129,.10), 0 0 18px rgba(16,185,129,.35) inset; }
  .glow-warn{ box-shadow:0 0 0 4px rgba(245,158,11,.10), 0 0 18px rgba(245,158,11,.35) inset; }
  .glow-bad{ box-shadow:0 0 0 4px rgba(239,68,68,.10), 0 0 18px rgba(239,68,68,.35) inset; }
  .cat-head{ font-weight:700; letter-spacing:.3px; }

  /* Modal */
  .modal{ position:fixed; inset:0; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,.55); backdrop-filter: blur(6px); z-index:60; }
  .modal.open{ display:flex; }
  .modal-card{ width:min(680px,92vw); border-radius:20px; padding:1rem; }
  .modal a{ color:#60a5fa; text-decoration:underline; }

  /* Background tech lines canvas */
  #techCanvas{ position:fixed; inset:0; z-index:-1; pointer-events:none; opacity:.65; }
</style>

<canvas id="techCanvas"></canvas>

<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-slate-100">
  <div class="flex items-center justify-between gap-4 mb-6">
    <div>
      <div class="chip">Analyzer</div>
      <h1 class="text-2xl sm:text-3xl font-extrabold mt-2">
        <span class="title-grad">Semantic SEO Master Analyzer 2.0</span>
      </h1>
      <p class="text-slate-300/90 mt-1">Analyze any URL for content, structure, technical signals, and entity context. Get a beautiful score wheel, quick stats, and actionable improvements.</p>
    </div>
  </div>

  <!-- Analyze form -->
  <form id="semanticForm" class="glass rounded-2xl p-4 sm:p-5 shadow-soft">
    <div class="grid gap-3 md:grid-cols-[1fr,280px]">
      <div class="grid gap-3 sm:grid-cols-[1fr,240px]">
        <input name="url" type="url" required placeholder="https://example.com/article"
               class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/50">
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)"
               class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-400/50">
      </div>
      <div>
        <button id="analyzeBtn" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-400 via-fuchsia-500 to-violet-600 hover:from-cyan-300 hover:via-fuchsia-400 hover:to-violet-500 text-white font-semibold shadow-soft transition-all active:scale-[.99]" type="submit">
          Analyze URL
        </button>
      </div>
    </div>
    <!-- Water bar loader -->
    <div id="waterWrap" class="water-wrap h-20 mt-4 hidden">
      <canvas id="waterCanvas"></canvas>
      <div class="water-overlay text-xl"><span id="waterLabel">Loading… 0%</span></div>
    </div>
  </form>

  <!-- Results -->
  <div id="resultWrap" class="mt-8 hidden space-y-8">

    <!-- Top: Score wheel + badge and Quick Stats -->
    <div class="grid lg:grid-cols-3 gap-6">
      <!-- Wheel -->
      <div class="glass rounded-2xl p-6 shadow-soft">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-lg">Overall Score</h3>
          <span id="wheelBadge" class="hidden px-3 py-1 rounded-full text-xs font-bold bg-emerald-400/15 text-emerald-300 border border-emerald-400/30">Great Work — Well Optimized</span>
        </div>
        <div class="grid grid-cols-[220px,1fr] gap-6 mt-4 items-center">
          <div class="wheel">
            <svg id="wheelSvg" width="220" height="220" viewBox="0 0 220 220">
              <defs>
                <linearGradient id="gradWheel" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%"   stop-color="#22d3ee"/>
                  <stop offset="50%"  stop-color="#7c3aed"/>
                  <stop offset="100%" stop-color="#f43f5e"/>
                </linearGradient>
              </defs>
              <g transform="translate(110,110)">
                <circle class="track" r="92" fill="none" stroke-width="18"/>
                <circle id="wheelBar" class="bar" r="92" fill="none" stroke="url(#gradWheel)" stroke-width="18"
                        stroke-dasharray="577" stroke-dashoffset="577" transform="rotate(-90)"/>
                <text id="wheelText" x="0" y="8" text-anchor="middle" font-size="42" font-weight="800" fill="white">0</text>
                <text id="wheelSub" x="0" y="38" text-anchor="middle" font-size="12" fill="rgba(255,255,255,.65)">Score</text>
              </g>
            </svg>
          </div>
          <div class="text-sm">
            <div class="flex items-center gap-2">
              <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-400"></span> ≥ 80 Excellent
            </div>
            <div class="flex items-center gap-2 mt-1">
              <span class="inline-block h-2.5 w-2.5 rounded-full bg-amber-400"></span> 60–79 Needs Optimization
            </div>
            <div class="flex items-center gap-2 mt-1">
              <span class="inline-block h-2.5 w-2.5 rounded-full bg-rose-400"></span> &lt; 60 Needs Significant Work
            </div>
            <p id="wheelLabel" class="mt-4 text-slate-300/90">—</p>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="lg:col-span-2 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="glass rounded-2xl p-5 shadow-soft">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-slate-300/80">Readability</p>
              <div class="text-3xl font-extrabold" id="qsReadability">—</div>
            </div>
            <div class="h-10 w-10 rounded-xl grid place-items-center bg-emerald-500/15 border border-emerald-400/30 text-emerald-300">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 5h18M3 12h18M3 19h18"/></svg>
            </div>
          </div>
          <div id="qsGrade" class="mt-1 text-xs text-slate-300/80">— grade band</div>
        </div>

        <div class="glass rounded-2xl p-5 shadow-soft">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-slate-300/80">Words</p>
              <div class="text-3xl font-extrabold" id="qsWords">—</div>
            </div>
            <div class="h-10 w-10 rounded-xl grid place-items-center bg-cyan-500/15 border border-cyan-400/30 text-cyan-300">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
            </div>
          </div>
          <div class="mt-1 text-xs text-slate-300/80">Word count</div>
        </div>

        <div class="glass rounded-2xl p-5 shadow-soft">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-slate-300/80">Links</p>
              <div class="text-3xl font-extrabold"><span id="qsInt">0</span>/<span id="qsExt">0</span></div>
            </div>
            <div class="h-10 w-10 rounded-xl grid place-items-center bg-fuchsia-500/15 border border-fuchsia-400/30 text-fuchsia-300">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"/><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"/></svg>
            </div>
          </div>
          <div class="mt-1 text-xs text-slate-300/80">internal / external</div>
        </div>
      </div>
    </div>

    <!-- Recommendations -->
    <div class="glass rounded-2xl p-6 shadow-soft">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-lg">Recommendations</h3>
      </div>
      <div id="recsList" class="mt-4 grid md:grid-cols-2 xl:grid-cols-3 gap-3"></div>
    </div>

    <!-- Content Structure -->
    <div class="grid lg:grid-cols-3 gap-6">
      <div class="glass rounded-2xl p-6 shadow-soft lg:col-span-2">
        <h3 class="font-bold text-lg">Content Structure</h3>
        <div class="grid sm:grid-cols-2 gap-4 mt-4">
          <div class="rounded-xl border border-white/10 bg-white/5 p-4">
            <div class="text-xs text-slate-300/80">Title</div>
            <div id="csTitle" class="mt-1 font-semibold">—</div>
          </div>
          <div class="rounded-xl border border-white/10 bg-white/5 p-4">
            <div class="text-xs text-slate-300/80">Meta description</div>
            <div id="csMeta" class="mt-1 text-slate-100">—</div>
          </div>
        </div>
        <div class="mt-4">
          <h4 class="text-sm font-semibold text-slate-200/90">Heading Map</h4>
          <div id="headingMap" class="mt-2 grid sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>
        </div>
      </div>

      <!-- Readability card -->
      <div class="glass rounded-2xl p-6 shadow-soft">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-lg">Readability</h3>
          <span id="readBadge" class="hidden px-2.5 py-1 rounded-full text-[11px] font-bold border">Badge</span>
        </div>
        <div class="grid grid-cols-[140px,1fr] gap-4 mt-3 items-center">
          <div class="wheel">
            <svg id="readWheel" width="140" height="140" viewBox="0 0 220 220">
              <defs>
                <linearGradient id="gradRead" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%"   stop-color="#34d399"/>
                  <stop offset="50%"  stop-color="#f59e0b"/>
                  <stop offset="100%" stop-color="#ef4444"/>
                </linearGradient>
              </defs>
              <g transform="translate(110,110) scale(.75)">
                <circle class="track" r="92" fill="none" stroke-width="18"/>
                <circle id="readBar" class="bar" r="92" fill="none" stroke="url(#gradRead)" stroke-width="18"
                        stroke-dasharray="577" stroke-dashoffset="577" transform="rotate(-90)"/>
                <text id="readText" x="0" y="8" text-anchor="middle" font-size="40" font-weight="800" fill="white">—</text>
                <text x="0" y="34" text-anchor="middle" font-size="12" fill="rgba(255,255,255,.65)">Flesch</text>
              </g>
            </svg>
          </div>
          <div class="text-sm">
            <div class="flex items-center justify-between">
              <span class="text-slate-300/80">Grade Level</span>
              <span id="readGrade" class="font-semibold">—</span>
            </div>
            <div class="mt-3">
              <div class="h-2 w-full rounded-full bg-white/10">
                <div id="readBarInline" class="h-2 rounded-full" style="width:0%; background:linear-gradient(90deg,#ef4444,#f59e0b,#34d399);"></div>
              </div>
              <div id="readHelp" class="text-xs text-slate-300/80 mt-2">—</div>
              <button id="readImproveBtn" class="mt-3 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/15 border border-white/15 text-xs">How to improve</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Checklists -->
    <div>
      <h3 class="text-xl font-extrabold mb-3">Semantic SEO Ground</h3>
      <div id="catsWrap" class="grid lg:grid-cols-2 xl:grid-cols-3 gap-6"></div>
    </div>

  </div>
</section>

<!-- Improve Modal -->
<div id="improveModal" class="modal">
  <div class="modal-card glass shadow-soft border border-white/10">
    <div class="p-4 sm:p-5">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="chip">Improve</div>
          <h4 id="improveTitle" class="mt-2 font-bold text-lg">Improvement tips</h4>
        </div>
        <button id="modalClose" class="p-2 rounded-lg hover:bg-white/10">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <p id="improveAdvice" class="mt-3 text-slate-200/90"></p>
      <div class="mt-4 flex items-center gap-3">
        <a id="improveLink" href="#" target="_blank" rel="noopener" class="px-3 py-2 rounded-lg bg-gradient-to-r from-cyan-400 to-fuchsia-500 text-slate-900 font-semibold">Google tips</a>
        <button id="modalOk" class="px-3 py-2 rounded-lg bg-white/10 border border-white/15">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
/* ---------- helpers ---------- */
const $ = s=>document.querySelector(s);
const $$= s=>document.querySelectorAll(s);
const clamp=(n,min,max)=>Math.max(min,Math.min(n,max));
const fmtInt=n=>new Intl.NumberFormat().format(n);
const colorByScore = s => s>=80 ? '#10b981' : s>=60 ? '#f59e0b' : '#ef4444';

function gradeBandFromFlesch(f){
  if (f>=90) return '5th grade (Very easy)';
  if (f>=80) return '6th grade (Easy)';
  if (f>=70) return '7th grade (Fairly easy)';
  if (f>=60) return '8–9th grade (Standard)';
  if (f>=50) return '10–12th (Fairly difficult)';
  if (f>=30) return 'College (Difficult)';
  return 'College graduate (Very difficult)';
}

/* ---------- Tech lines background (canvas) ---------- */
(() => {
  const c = document.getElementById('techCanvas'); if(!c) return;
  const ctx = c.getContext('2d');
  let w=0,h=0,t=0,dpr=Math.min(2,window.devicePixelRatio||1);
  function size(){ w=c.clientWidth; h=c.clientHeight; c.width=w*dpr; c.height=h*dpr; ctx.setTransform(dpr,0,0,dpr,0,0); }
  function draw(){
    t+=0.012; ctx.clearRect(0,0,w,h);
    const layers = [
      {color:'rgba(34,211,238,.28)', amp:12, gap:68, speed:1.0, lw:1.6},
      {color:'rgba(124,58,237,.22)', amp:18, gap:82, speed:0.8, lw:1.2},
      {color:'rgba(244,63,94,.18)',  amp:26, gap:96, speed:0.6, lw:1.1},
    ];
    layers.forEach((L, i) => {
      for(let y=i*20; y<h+60; y+=L.gap){
        ctx.beginPath(); ctx.lineWidth=L.lw; ctx.strokeStyle=L.color;
        for(let x=-40; x<=w+40; x+=36){
          const yy = y + Math.sin((x+t*60*L.speed)*0.015 + i)*L.amp + Math.cos((x*0.008)+t*(1.2-i*0.3))*4;
          if(x===-40) ctx.moveTo(x,yy); else ctx.lineTo(x,yy);
        }
        ctx.stroke();
      }
    });
    requestAnimationFrame(draw);
  }
  window.addEventListener('resize', size, {passive:true});
  size(); draw();
})();

/* ---------- Water bar loader ---------- */
const water = {
  init(){
    this.wrap = document.getElementById('waterWrap');
    this.c    = document.getElementById('waterCanvas');
    this.ctx  = this.c.getContext('2d');
    this.label= document.getElementById('waterLabel');
    this.progress = 0; this.target = 100; this.anim=null;
    this.resize();
    window.addEventListener('resize', ()=>this.resize(), {passive:true});
  },
  show(){ this.wrap.classList.remove('hidden'); this.progress=0; this.target=95; this.animate(); },
  settleTo(v){ this.target = clamp(v,0,100); },
  hide(){ this.wrap.classList.add('hidden'); cancelAnimationFrame(this.anim); },
  resize(){
    const r = this.wrap.getBoundingClientRect();
    const dpr = Math.min(2, window.devicePixelRatio||1);
    this.c.width  = r.width*dpr; this.c.height = r.height*dpr;
    this.ctx.setTransform(dpr,0,0,dpr,0,0);
  },
  animate(){
    const ctx=this.ctx, w=this.c.width/(window.devicePixelRatio||1), h=this.c.height/(window.devicePixelRatio||1);
    this.progress += (this.target - this.progress)*0.05;
    const pct = this.progress;
    // bg
    ctx.clearRect(0,0,w,h);
    ctx.fillStyle = 'rgba(255,255,255,.04)'; ctx.fillRect(0,0,w,h);

    // wave clip
    const level = h*(1 - pct/100);
    const g = ctx.createLinearGradient(0,0,w,h);
    g.addColorStop(0,'#22d3ee'); g.addColorStop(.5,'#7c3aed'); g.addColorStop(1,'#f43f5e');
    ctx.fillStyle=g;
    ctx.beginPath();
    const t = performance.now()/500;
    for(let x=0;x<=w;x+=4){
      const y = level + Math.sin(x*0.03 + t)*6 + Math.cos(x*0.012 + t*1.7)*3;
      if(x===0) ctx.moveTo(x,y);
      else ctx.lineTo(x,y);
    }
    ctx.lineTo(w,h); ctx.lineTo(0,h); ctx.closePath(); ctx.fill();

    // label
    this.label.textContent = `Analyzing… ${Math.round(pct)}%`;
    if (Math.abs(this.target - this.progress) > .2) { this.anim = requestAnimationFrame(()=>this.animate()); }
    else { if (this.target>=100) this.label.textContent = 'Finalizing…'; }
  }
};

/* ---------- Score wheels ---------- */
function animateWheel(svgBar, textNode, target){
  const length = 2*Math.PI*92; // stroke-dasharray used in SVG
  const dur = 850;
  const start = performance.now();
  function step(now){
    const p = clamp((now-start)/dur,0,1);
    const val = Math.round(p*target);
    svgBar.style.strokeDashoffset = (length*(1 - val/100)).toFixed(2);
    if (textNode) textNode.textContent = val;
    if (p<1) requestAnimationFrame(step);
  }
  requestAnimationFrame(step);
}

/* ---------- Improve modal ---------- */
const modal = {
  el: null, title:null, advice:null, link:null,
  open(data){
    this.title.textContent = data.title || 'Improvement tips';
    this.advice.textContent = data.advice || 'Add more detail for better relevance.';
    this.link.href = data.url || '#';
    this.el.classList.add('open');
  },
  close(){ this.el.classList.remove('open'); }
};

/* ---------- DOM refs ---------- */
const elForm = document.getElementById('semanticForm');
const resultWrap = document.getElementById('resultWrap');

const wheelBar   = document.getElementById('wheelBar');
const wheelText  = document.getElementById('wheelText');
const wheelLabel = document.getElementById('wheelLabel');
const wheelBadge = document.getElementById('wheelBadge');

const qsReadability = document.getElementById('qsReadability');
const qsGrade = document.getElementById('qsGrade');
const qsWords = document.getElementById('qsWords');
const qsInt   = document.getElementById('qsInt');
const qsExt   = document.getElementById('qsExt');

const recsList = document.getElementById('recsList');

const csTitle = document.getElementById('csTitle');
const csMeta  = document.getElementById('csMeta');
const headingMap = document.getElementById('headingMap');

const readBar   = document.getElementById('readBar');
const readText  = document.getElementById('readText');
const readGrade = document.getElementById('readGrade');
const readBarInline = document.getElementById('readBarInline');
const readBadge = document.getElementById('readBadge');
const readImproveBtn = document.getElementById('readImproveBtn');

const catsWrap = document.getElementById('catsWrap');

/* ---------- Bind modal ---------- */
modal.el = document.getElementById('improveModal');
modal.title = document.getElementById('improveTitle');
modal.advice= document.getElementById('improveAdvice');
modal.link  = document.getElementById('improveLink');
document.getElementById('modalClose').onclick = ()=>modal.close();
document.getElementById('modalOk').onclick    = ()=>modal.close();

/* ---------- Submit ---------- */
water.init();

elForm.addEventListener('submit', async (e) => {
  e.preventDefault();
  // start loader
  water.show();

  // animate wheel to 0 (reset)
  wheelBar.style.strokeDashoffset = 577;
  wheelText.textContent = '0';
  wheelBadge.classList.add('hidden');

  const fd = new FormData(elForm);
  const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') };

  let data;
  try{
    const res = await fetch('/api/semantic-analyze', {
      method: 'POST',
      headers: {'Accept':'application/json','Content-Type':'application/json'},
      body: JSON.stringify(payload)
    });
    data = await res.json();
  }catch(err){
    water.hide();
    alert('Request failed. Please try again.');
    return;
  }

  if (!data || !data.ok) {
    water.hide();
    alert((data && data.error) || 'Analysis failed.');
    return;
  }

  // settle loader to final score then hide
  water.settleTo(100);
  setTimeout(()=>water.hide(), 600);

  // show results
  resultWrap.classList.remove('hidden');

  /* ---------- Fill top widgets ---------- */
  const score = clamp(parseInt(data.overall_score||0),0,100);
  animateWheel(wheelBar, wheelText, score);
  wheelLabel.textContent = data.wheel?.label || (score>=80 ? 'Great Work — Well Optimized' : score>=60 ? 'Needs Optimization' : 'Needs Significant Optimization');

  // badge style
  if (score>=80){
    wheelBadge.textContent = 'Great Work — Well Optimized';
    wheelBadge.className = 'px-3 py-1 rounded-full text-xs font-bold border border-emerald-400/30 bg-emerald-400/15 text-emerald-300';
    wheelBadge.classList.remove('hidden');
  } else if (score>=60){
    wheelBadge.textContent = 'Needs Optimization';
    wheelBadge.className = 'px-3 py-1 rounded-full text-xs font-bold border border-amber-400/30 bg-amber-400/15 text-amber-300';
    wheelBadge.classList.remove('hidden');
  } else {
    wheelBadge.textContent = 'Needs Significant Work';
    wheelBadge.className = 'px-3 py-1 rounded-full text-xs font-bold border border-rose-400/30 bg-rose-400/15 text-rose-300';
    wheelBadge.classList.remove('hidden');
  }

  // Quick stats
  const qs = data.quick_stats || {};
  const flesch = clamp(parseInt(qs.readability_flesch||0),0,100);
  qsReadability.textContent = flesch;
  qsGrade.textContent = gradeBandFromFlesch(flesch);
  qsWords.textContent = fmtInt(qs.word_count||0);
  qsInt.textContent   = fmtInt(qs.internal_links||0);
  qsExt.textContent   = fmtInt(qs.external_links||0);

  // Readability wheel + bar + badge
  animateWheel(readBar, readText, flesch);
  readBarInline.style.width = flesch+'%';
  const rbColor = colorByScore(flesch);
  readBarInline.style.background = `linear-gradient(90deg, #ef4444, #f59e0b, #10b981)`;
  readGrade.textContent = gradeBandFromFlesch(flesch);
  if (flesch>=80){
    readBadge.textContent = 'Easy to read';
    readBadge.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold border border-emerald-400/30 bg-emerald-400/15 text-emerald-300';
    readBadge.classList.remove('hidden');
  } else if (flesch>=60){
    readBadge.textContent = 'Standard';
    readBadge.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold border border-amber-400/30 bg-amber-400/15 text-amber-300';
    readBadge.classList.remove('hidden');
  } else {
    readBadge.textContent = 'Hard to read';
    readBadge.className = 'px-2.5 py-1 rounded-full text-[11px] font-bold border border-rose-400/30 bg-rose-400/15 text-rose-300';
    readBadge.classList.remove('hidden');
  }
  document.getElementById('readHelp').textContent =
    flesch>=80 ? 'Nice! Keep sentences short and direct to maintain clarity.' :
    flesch>=60 ? 'Trim longer sentences, use active voice, and prefer simple words.' :
                 'Break up dense paragraphs, define jargon, and shorten sentences.';

  readImproveBtn.onclick = () => modal.open({
    title: 'Improve Readability',
    advice: 'Use shorter sentences (≤20 words), active voice, headings and lists. Replace complex words with simpler synonyms. Add images/captions to break text.',
    url: 'https://www.google.com/search?q=improve+readability+content+seo'
  });

  // Content structure
  const cs = data.content_structure || {};
  csTitle.textContent = cs.title || '—';
  csMeta.textContent  = cs.meta_description || '—';

  // Heading map chips
  headingMap.innerHTML = '';
  const hs = cs.headings || {};
  Object.keys(hs).forEach(level=>{
    const arr = hs[level]||[];
    if(!arr.length) return;
    const card = document.createElement('div');
    card.className = 'rounded-xl border border-white/10 bg-white/5 p-3';
    const head = document.createElement('div');
    head.className = 'text-xs uppercase tracking-wide text-slate-300/80 mb-2';
    head.textContent = level;
    card.appendChild(head);
    arr.slice(0,12).forEach(t=>{
      const chip = document.createElement('div');
      chip.className = 'inline-block mr-1 mb-1 px-2 py-1 rounded-md text-xs bg-white/10 border border-white/10';
      chip.textContent = t;
      card.appendChild(chip);
    });
    if(arr.length>12){
      const more = document.createElement('div');
      more.className = 'mt-1 text-[11px] text-slate-400';
      more.textContent = `+${arr.length-12} more…`;
      card.appendChild(more);
    }
    headingMap.appendChild(card);
  });

  // Recommendations
  recsList.innerHTML = '';
  (data.recommendations || []).forEach(r=>{
    const card = document.createElement('div');
    const color = r.severity==='Critical' ? 'rose' : (r.severity==='Warning' ? 'amber' : 'sky');
    card.className = 'rounded-xl p-4 border shadow-sm bg-white/5 border-white/10';
    card.innerHTML = `
      <div class="flex items-start gap-2">
        <span class="mt-1 inline-flex h-2.5 w-2.5 rounded-full bg-${color}-400"></span>
        <div>
          <div class="text-sm font-semibold">${r.text}</div>
          <div class="text-[11px] mt-1 text-slate-300/80">Severity: ${r.severity}</div>
        </div>
      </div>`;
    recsList.appendChild(card);
  });

  // Categories & checks
  catsWrap.innerHTML = '';
  (data.categories || []).forEach(cat=>{
    const box = document.createElement('div');
    const ccol = cat.color==='green' ? 'from-emerald-400/20 to-transparent border-emerald-400/30'
                : cat.color==='orange' ? 'from-amber-400/20 to-transparent border-amber-400/30'
                : cat.color==='red' ? 'from-rose-400/20 to-transparent border-rose-400/30'
                : 'from-slate-300/20 to-transparent border-white/10';
    box.className = `rounded-2xl p-5 shadow-soft border glass bg-gradient-to-br ${ccol}`;
    box.innerHTML = `
      <div class="flex items-center justify-between">
        <div class="cat-head">${cat.name}</div>
        <div class="text-xs px-2 py-1 rounded-md border border-white/15 bg-white/10">${cat.score ?? '—'}</div>
      </div>
      <div class="mt-3 space-y-2" data-checks></div>
    `;
    const list = box.querySelector('[data-checks]');
    (cat.checks||[]).forEach(ch=>{
      const color = ch.score==null ? 'slate' : (ch.score>=80 ? 'emerald' : ch.score>=60 ? 'amber' : 'rose');
      const glow  = ch.score==null ? '' : (ch.score>=80 ? 'glow-ok' : ch.score>=60 ? 'glow-warn' : 'glow-bad');
      const row = document.createElement('div');
      row.className = `check-item ${glow}`;
      row.innerHTML = `
        <div class="check-score border border-white/15 bg-white/10">${ch.score ?? '—'}</div>
        <div class="flex-1">
          <div class="font-medium text-sm">${ch.label}</div>
          <div class="text-xs text-slate-300/80 mt-0.5">${ch.pass===null ? '—' : (ch.pass ? 'Pass' : 'Needs work')}</div>
        </div>
        <div class="flex items-center gap-2">
          <button class="px-2.5 py-1.5 rounded-lg text-xs border border-white/15 bg-white/10 hover:bg-white/15" data-improve>Improve</button>
        </div>
      `;
      row.querySelector('[data-improve]').onclick = () => modal.open({
        title: ch.label,
        advice: ch.advice || 'Consider improving this item for better topical relevance.',
        url: ch.improve_search_url || 'https://www.google.com'
      });
      list.appendChild(row);
    });
    catsWrap.appendChild(box);
  });
});
</script>
@endsection
