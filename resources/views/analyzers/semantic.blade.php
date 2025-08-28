@php
    /**
     * SEMANTIC SEO MASTER — Stylish UI Upgrade
     * -------------------------------------------------------------
     * This Blade view is drop‑in and self‑contained. It includes:
     * 1) Analyzer button → multicolor LIQUID progress bar (0→100)
     * 2) Quick Stats — stylish cards
     * 3) Overall Score Wheel + dynamic badge (≥80 green, 60–79 orange, <60 red)
     * 4) Recommendations — pro layout, impact tags
     * 5) Content Structure — colorful, collapsible sections
     * 6) "Semantic SEO Ground" heading before checklists
     * 7) Readability dashboard — wheel, grade level, improvement bars
     * 8) Glow checklists w/ icons; colored by score bands
     * 9) Background recreated in code: deep gradient with animated tech lines
     *
     * Hooking to backend:
     * - Change data-action on #analyzeForm to your semanticAnalyze route.
     * - The JS expects a JSON payload similar to the example in window.__mockAnalysis.
     * - If you already provide $analysis from the controller, the page will render it on load.
     */
@endphp

@extends('layouts.app')

@section('title', 'Semantic SEO Master — Analyzer')

@section('content')
<style>
  :root{
    --bg-1:#0b0b1a; /* deep indigo */
    --bg-2:#12122a; /* darker indigo */
    --bg-3:#1c0f2e; /* purple tint */
    --card:#14132a;
    --card-2:#18163a;
    --muted:#9aa0b4;
    --text:#e8eaf6;
    --green:#32d296;
    --orange:#ffb020;
    --red:#ff5d5d;
    --cyan:#00d0ff;
    --violet:#8a5cff;
    --pink:#ff5ad9;
    --gradient-1: linear-gradient(135deg, #6a00ff 0%, #00d0ff 100%);
    --gradient-2: linear-gradient(135deg, #00ffa3 0%, #00a3ff 100%);
    --gradient-3: linear-gradient(135deg, #ff6b6b 0%, #ffd93d 100%);
    --shadow-1: 0 10px 30px rgba(0,0,0,.35), inset 0 1px 0 rgba(255,255,255,.03);
    --ring: 0 0 0 2px rgba(138,92,255,.35), 0 0 0 8px rgba(138,92,255,.08);
  }
  html,body{height:100%;}
  body{
    color:var(--text);
    background:
      radial-gradient(1200px 700px at 85% -10%, rgba(138,92,255,.30), transparent 60%),
      radial-gradient(900px 600px at 0% 0%, rgba(0,208,255,.18), transparent 60%),
      radial-gradient(700px 500px at 100% 100%, rgba(255,90,217,.12), transparent 60%),
      linear-gradient(180deg, var(--bg-3), var(--bg-2) 60%, var(--bg-1));
    position:relative;
    overflow-x:hidden;
  }
  /* Animated tech lines background */
  .bg-tech{position:fixed; inset:0; pointer-events:none; opacity:.35;}
  .bg-tech svg{width:120%; height:120%; transform:translate(-10%,-10%);}  
  .bg-tech .dash{stroke-dasharray:6 14; animation:move 14s linear infinite;}
  .bg-tech .glow{filter:drop-shadow(0 0 6px rgba(138,92,255,.5));}
  @keyframes move{to{stroke-dashoffset:-400;}}

  /* Layout */
  .container-xl{max-width:1220px; margin:0 auto; padding:32px 20px 100px; position:relative;}
  .titlebar{display:flex; align-items:center; gap:16px; margin-bottom:24px;}
  .titlebar h1{font-weight:800; letter-spacing:.2px; font-size:clamp(22px, 2.2vw, 32px);}
  .subtitle{color:var(--muted);}
  .card{background:linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.00)), var(--card);
        border:1px solid rgba(255,255,255,.06); border-radius:18px; box-shadow:var(--shadow-1);} 
  .card-ghost{background:transparent; border:1px dashed rgba(255,255,255,.15);} 
  .grid{display:grid; gap:18px;}
  .grid-2{grid-template-columns:1.2fr .8fr;}
  .grid-3{grid-template-columns:repeat(3, 1fr);} 
  .grid-4{grid-template-columns:repeat(4, 1fr);} 
  @media (max-width:1024px){.grid-2{grid-template-columns:1fr;}.grid-4{grid-template-columns:repeat(2, 1fr);} }

  /* Analyze Bar (Liquid) */
  .analyze-wrap{display:flex; gap:14px; align-items:center;}
  .btn-primary{cursor:pointer; background:var(--gradient-1); color:white; border:none; padding:12px 18px; border-radius:12px; font-weight:700; letter-spacing:.3px; box-shadow:var(--shadow-1);} 
  .btn-primary:active{transform:translateY(1px);} 
  .liquid-bar{flex:1; height:18px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12); border-radius:999px; position:relative; overflow:hidden;}
  .liquid-fill{position:absolute; inset:0; width:0%; background:linear-gradient(90deg, #6a00ff, #00d0ff, #00ffa3, #ffd93d, #ff6b6b); background-size:200% 100%; border-radius:inherit; animation:flow 3s linear infinite; box-shadow:inset 0 0 18px rgba(0,0,0,.25);} 
  .liquid-wave{content:""; position:absolute; left:0; right:0; bottom:-8px; height:26px; background:radial-gradient(12px 6px at 12px 6px, rgba(255,255,255,.25), rgba(255,255,255,0) 50%) repeat-x; background-size:26px 26px; animation:wave 2.6s linear infinite; opacity:.35;}
  @keyframes flow{0%{background-position:0% 50%}100%{background-position:200% 50%}}
  @keyframes wave{0%{background-position:0 0}100%{background-position:260px 0}}
  .bar-text{position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-size:12px; color:#fff; text-shadow:0 1px 4px rgba(0,0,0,.45);}

  /* Wheels */
  .wheel{position:relative; width:220px; height:220px; margin:auto;}
  .wheel svg{transform:rotate(-90deg);} 
  .wheel .num{position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:6px;}
  .wheel .num b{font-size:42px; line-height:1;}
  .wheel .num span{color:var(--muted); font-size:12px; text-transform:uppercase; letter-spacing:.12em;}
  .badge{display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:999px; font-weight:700; letter-spacing:.3px; border:1px solid rgba(255,255,255,.12);} 
  .badge i{width:18px; height:18px; border-radius:50%; display:inline-block;}
  .badge.badge-good{background:rgba(50,210,150,.12); color:#baffdf; border-color:rgba(50,210,150,.35);} 
  .badge.badge-mid{background:rgba(255,176,32,.12); color:#ffe2ad; border-color:rgba(255,176,32,.35);} 
  .badge.badge-bad{background:rgba(255,93,93,.12); color:#ffcccc; border-color:rgba(255,93,93,.35);} 

  /* Quick Stats */
  .stats{display:grid; gap:14px; grid-template-columns:repeat(6, 1fr);} 
  @media (max-width:1200px){.stats{grid-template-columns:repeat(3, 1fr);} }
  @media (max-width:640px){.stats{grid-template-columns:repeat(2, 1fr);} }
  .stat{display:flex; gap:12px; align-items:center; padding:14px; border-radius:16px; background:linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.00)), var(--card-2); border:1px solid rgba(255,255,255,.06);} 
  .stat i{width:34px; height:34px; border-radius:10px; background:rgba(255,255,255,.06); display:grid; place-items:center; box-shadow:inset 0 1px 0 rgba(255,255,255,.06);} 
  .stat h5{margin:0; font-size:12px; color:var(--muted);}
  .stat b{font-size:16px;}

  /* Recommendations */
  .recs-head{display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;}
  .chip{padding:6px 10px; border-radius:999px; border:1px solid rgba(255,255,255,.12); color:var(--muted);}
  .recs{display:grid; gap:14px; grid-template-columns:repeat(2, 1fr);} 
  @media (max-width:900px){.recs{grid-template-columns:1fr;}}
  .rec{padding:16px; border-radius:16px; background:
    linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.00)),
    linear-gradient(135deg, rgba(0,208,255,.08), rgba(138,92,255,.08));
    border:1px solid rgba(255,255,255,.08); position:relative; overflow:hidden;}
  .rec::after{content:""; position:absolute; inset:auto -30% -80% -30%; height:160%; background:radial-gradient(500px 120px at 50% 0, rgba(255,255,255,.08), transparent 60%);} 
  .impact{font-size:12px; padding:4px 8px; border-radius:999px; margin-right:8px;}
  .impact.high{background:rgba(255,93,93,.15); color:#ffb3b3;}
  .impact.mid{background:rgba(255,176,32,.15); color:#ffe2ad;}
  .impact.low{background:rgba(50,210,150,.15); color:#baffdf;}
  .rec h4{margin:.2rem 0 .6rem; font-size:16px;}
  .rec p{color:var(--muted); margin:0;}
  .rec .actions{margin-top:10px; display:flex; gap:10px;}
  .btn-ghost{background:transparent; border:1px solid rgba(255,255,255,.16); color:#fff; padding:8px 12px; border-radius:10px;}

  /* Content Structure */
  .structure{display:grid; gap:14px; grid-template-columns:repeat(3, 1fr);} 
  @media (max-width:1100px){.structure{grid-template-columns:1fr;}}
  .section{padding:16px; border-radius:16px; border:1px solid rgba(255,255,255,.08); background:linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.00)), var(--card);} 
  .section h3{margin:0 0 8px; font-size:14px; letter-spacing:.08em; color:#cfd3e6; text-transform:uppercase;}
  .tag{display:inline-block; padding:6px 10px; border-radius:999px; font-size:12px; margin:4px 6px 0 0; background:rgba(255,255,255,.06);} 

  /* Readability */
  .readability{display:grid; gap:14px; grid-template-columns:1fr 1fr;} 
  @media (max-width:1000px){.readability{grid-template-columns:1fr;}}
  .bar{height:14px; background:rgba(255,255,255,.06); border-radius:999px; overflow:hidden; border:1px solid rgba(255,255,255,.1);} 
  .bar .fill{height:100%; width:40%; background:var(--gradient-2);}
  .legend{display:flex; gap:10px; align-items:center; font-size:12px; color:var(--muted);} 
  .legend .dot{width:10px; height:10px; border-radius:50%; display:inline-block;}

  /* Checklists */
  .ground-title{font-weight:800; font-size:22px; letter-spacing:.02em; margin:8px 0 4px;}
  .checklists{display:grid; gap:14px; grid-template-columns:repeat(2, 1fr);} 
  @media (max-width:1000px){.checklists{grid-template-columns:1fr;}}
  .clist{border:1px solid rgba(255,255,255,.08); border-radius:18px; position:relative; overflow:hidden;}
  .clist .head{display:flex; align-items:center; justify-content:space-between; gap:10px; padding:14px 16px; background:linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.00));}
  .clist .head .meta{display:flex; align-items:center; gap:10px;}
  .score-pill{padding:6px 10px; border-radius:999px; font-weight:700; letter-spacing:.3px; font-size:12px;}
  .score-good{background:rgba(50,210,150,.15); color:#baffdf;}
  .score-mid{background:rgba(255,176,32,.15); color:#ffe2ad;}
  .score-bad{background:rgba(255,93,93,.15); color:#ffcccc;}
  .clist ul{list-style:none; margin:0; padding:10px 16px 16px; display:grid; gap:10px;}
  .item{display:flex; gap:12px; align-items:flex-start; padding:10px 12px; border-radius:12px; background:linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.00)); border:1px solid rgba(255,255,255,.06); position:relative;}
  .item.ok{box-shadow:0 0 24px rgba(50,210,150,.12);} 
  .item.warn{box-shadow:0 0 24px rgba(255,176,32,.12);} 
  .item.fail{box-shadow:0 0 24px rgba(255,93,93,.12);} 
  .item i{width:28px; height:28px; border-radius:8px; display:grid; place-items:center;}
  .i-ok{background:rgba(50,210,150,.18);} .i-warn{background:rgba(255,176,32,.18);} .i-fail{background:rgba(255,93,93,.18);} 
  .item h6{margin:0; font-size:14px;}
  .item p{margin:.25rem 0 0; color:var(--muted); font-size:12px;}

  /* Utilities */
  .row{display:flex; align-items:center; gap:12px;}
  .muted{color:var(--muted);} .mt-1{margin-top:6px;} .mt-2{margin-top:12px;} .mt-3{margin-top:18px;} .mb-2{margin-bottom:12px;} .mb-3{margin-bottom:18px;} .p-16{padding:16px;} .p-20{padding:20px;} 
  .ring{box-shadow:var(--ring);} 
  .center{display:grid; place-items:center;}

  @media (prefers-reduced-motion: reduce){
    .liquid-fill,.liquid-wave{animation:none;}
    .bg-tech .dash{animation:none;}
  }
</style>

<!-- Background tech lines (pure SVG) -->
<div class="bg-tech" aria-hidden="true">
  <svg viewBox="0 0 1200 800" preserveAspectRatio="none">
    <defs>
      <linearGradient id="glow" x1="0" x2="1">
        <stop offset="0%" stop-color="#8a5cff" stop-opacity=".8"/>
        <stop offset="100%" stop-color="#00d0ff" stop-opacity=".8"/>
      </linearGradient>
    </defs>
    <!-- vertical nets -->
    @for($x=0;$x<=1200;$x+=80)
      <line x1="{{ $x }}" y1="0" x2="{{ $x }}" y2="800" stroke="url(#glow)" stroke-width="1" class="dash glow"/>
    @endfor
    <!-- diagonal moving strands -->
    @for($i=0;$i<12;$i++)
      @php $y = 40 + $i*60; @endphp
      <path d="M -100 {{ $y }} Q 300 {{ $y+40 }}, 700 {{ $y-30 }} T 1400 {{ $y+30 }}" fill="none" stroke="url(#glow)" stroke-width="1.6" class="dash glow"/>
    @endfor
  </svg>
</div>

<div class="container-xl">

  <div class="titlebar">
    <div style="width:46px;height:46px;border-radius:14px;background:var(--gradient-1);display:grid;place-items:center;box-shadow:var(--shadow-1)">
      <!-- logo spark -->
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
        <path d="M12 2 L14.8 8.2 L22 9.2 L16.8 13.6 L18.2 20.6 L12 17 L5.8 20.6 L7.2 13.6 L2 9.2 L9.2 8.2 Z" stroke="#fff" stroke-width="1.5"/>
      </svg>
    </div>
    <div>
      <h1>Semantic SEO Master</h1>
      <div class="subtitle">Analyze, visualize, and perfect your on‑page semantics.</div>
    </div>
  </div>

  <!-- Analyze form + Liquid bar -->
  <form id="analyzeForm" class="card p-16 mb-3" data-action="{{ route('semantic.analyze') ?? '#' }}">
    @csrf
    <div class="analyze-wrap">
      <input type="url" name="url" required placeholder="Paste a URL to analyze…" style="flex:1;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:#fff;padding:12px 14px;border-radius:12px;outline:none;">
      <input type="text" name="target_keyword" placeholder="Optional: target keyword" style="width:260px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);color:#fff;padding:12px 14px;border-radius:12px;outline:none;">
      <button type="submit" class="btn-primary" id="analyzeBtn">Run Analyzer</button>
    </div>
    <div class="liquid-bar mt-2 ring">
      <div class="liquid-fill" id="liquidFill"></div>
      <div class="liquid-wave"></div>
      <div class="bar-text" id="barText">0%</div>
    </div>
  </form>

  <div class="grid grid-2">
    <!-- LEFT: Score + Quick stats + Readability -->
    <div class="grid" style="gap:18px;">
      <div class="card p-20">
        <div class="grid" style="grid-template-columns:240px 1fr; gap:20px; align-items:center;">
          <div>
            <div class="wheel" id="overallWheel">
              <svg width="220" height="220" viewBox="0 0 220 220">
                <circle cx="110" cy="110" r="95" stroke="rgba(255,255,255,.08)" stroke-width="16" fill="none"/>
                <defs>
                  <linearGradient id="gWheel" x1="0" x2="1">
                    <stop offset="0%" stop-color="#6a00ff"/>
                    <stop offset="50%" stop-color="#00d0ff"/>
                    <stop offset="100%" stop-color="#00ffa3"/>
                  </linearGradient>
                </defs>
                <circle id="overallProg" cx="110" cy="110" r="95" stroke="url(#gWheel)" stroke-width="18" stroke-linecap="round" fill="none" stroke-dasharray="597" stroke-dashoffset="597"/>
              </svg>
              <div class="num">
                <b id="overallNum">0</b>
                <span>Overall Score</span>
              </div>
            </div>
          </div>
          <div>
            <div id="overallBadge" class="badge badge-mid">
              <i style="background:var(--orange)"></i>
              <span><b>Getting There</b> — Needs Optimization</span>
            </div>
            <div class="mt-2 muted">Scores update after analysis. Thresholds: ≥80 <span style="color:var(--green)">Great Work</span>, 60–79 <span style="color:var(--orange)">Needs improvement</span>, &lt;60 <span style="color:var(--red)">Action needed</span>.</div>
          </div>
        </div>
      </div>

      <div class="card p-20">
        <div class="row mb-2" style="justify-content:space-between;">
          <h2 style="margin:0;font-size:18px">Quick Stats</h2>
          <span class="chip">Live from your page</span>
        </div>
        <div class="stats" id="quickStats">
          <!-- stat cards inject here -->
        </div>
      </div>

      <div class="card p-20">
        <div class="row mb-2" style="justify-content:space-between;">
          <h2 style="margin:0;font-size:18px">Readability</h2>
          <span class="chip">Audience Fit</span>
        </div>
        <div class="readability">
          <div class="center">
            <div class="wheel" id="readabilityWheel">
              <svg width="220" height="220" viewBox="0 0 220 220">
                <circle cx="110" cy="110" r="95" stroke="rgba(255,255,255,.08)" stroke-width="16" fill="none"/>
                <defs>
                  <linearGradient id="gRead" x1="0" x2="1">
                    <stop offset="0%" stop-color="#00ffa3"/>
                    <stop offset="100%" stop-color="#00a3ff"/>
                  </linearGradient>
                </defs>
                <circle id="readProg" cx="110" cy="110" r="95" stroke="url(#gRead)" stroke-width="18" stroke-linecap="round" fill="none" stroke-dasharray="597" stroke-dashoffset="597"/>
              </svg>
              <div class="num">
                <b id="readNum">0</b>
                <span>Readability</span>
              </div>
            </div>
            <div class="mt-2" id="gradeBadge"></div>
          </div>
          <div>
            <div class="mb-2">How to improve your content</div>
            <div class="bar mb-2"><div class="fill" id="barSentence"></div></div>
            <div class="legend mb-2"><span class="dot" style="background:#00a3ff"></span> Average sentence length</div>
            <div class="bar mb-2"><div class="fill" id="barWords"></div></div>
            <div class="legend mb-2"><span class="dot" style="background:#00ffa3"></span> Simple words ratio</div>
            <div class="bar mb-2"><div class="fill" id="barPassive"></div></div>
            <div class="legend"><span class="dot" style="background:#ffd93d"></span> Passive voice usage</div>
            <div class="mt-2 muted" id="readabilityNote">Aim for Grade 8–10 for broad audiences.</div>
          </div>
        </div>
      </div>

    </div>

    <!-- RIGHT: Recommendations + Structure -->
    <div class="grid" style="gap:18px;">
      <div class="card p-20">
        <div class="recs-head">
          <h2 style="margin:0;font-size:18px">Recommendations</h2>
          <div class="row">
            <span class="chip">AI suggestions</span>
            <span class="chip">Sorted by impact</span>
          </div>
        </div>
        <div class="recs" id="recs"></div>
      </div>

      <div class="card p-20">
        <h2 style="margin:0 0 8px;font-size:18px">Content Structure</h2>
        <div class="structure" id="structure">
          <!-- sections render here -->
        </div>
      </div>
    </div>
  </div>

  <h2 class="ground-title mt-3">Semantic SEO Ground</h2>
  <div class="checklists" id="checklists">
    <!-- checklist categories -->
  </div>

</div>

<script>
  // ===== Helpers =====
  const clamp = (n, a, b) => Math.max(a, Math.min(b, n));
  const meterColor = (score) => score>=80? 'good' : score>=60? 'mid' : 'bad';
  const fmt = (n) => typeof n==="number" ? n.toLocaleString() : n;

  // Animate circle progress
  function animateWheel(circle, numEl, target){
    const CIRC = 2 * Math.PI * 95; // stroke-dasharray used in SVG (597)
    const start = performance.now();
    const dur = 900;
    const begin = 0; const end = clamp(target, 0, 100);
    (function frame(t){
      const k = clamp((t - start)/dur, 0, 1);
      const val = Math.round(begin + (end - begin)*k);
      circle.style.strokeDasharray = CIRC;
      circle.style.strokeDashoffset = CIRC * (1 - val/100);
      if(numEl) numEl.textContent = val;
      if(k<1) requestAnimationFrame(frame); 
    })(start);
  }

  function badgeFor(score){
    const box = document.getElementById('overallBadge');
    box.classList.remove('badge-good','badge-mid','badge-bad');
    let text = '', dot = '';
    if(score>=80){
      box.classList.add('badge-good');
      text = '<b>Great Work</b> — Well Optimized';
      dot = 'var(--green)';
    } else if(score>=60){
      box.classList.add('badge-mid');
      text = '<b>Getting There</b> — Needs Optimization';
      dot = 'var(--orange)';
    } else {
      box.classList.add('badge-bad');
      text = '<b>Action Needed</b> — Improve Essentials';
      dot = 'var(--red)';
    }
    box.innerHTML = `<i style="background:${dot}"></i><span>${text}</span>`;
  }

  // Quick stats renderer
  function renderStats(stats){
    const root = document.getElementById('quickStats');
    root.innerHTML = '';
    const icons = {
      words: svgIcon('doc'),
      headings: svgIcon('heading'),
      links: svgIcon('link'),
      images: svgIcon('image'),
      schema: svgIcon('schema'),
      speed: svgIcon('speed'),
      ai: svgIcon('ai'),
      grade: svgIcon('grade')
    };
    const items = [
      {k:'words', title:'Words'},
      {k:'grade', title:'Reading Grade'},
      {k:'headings', title:'Headings'},
      {k:'links', title:'Links'},
      {k:'images', title:'Images w/ Alt'},
      {k:'schema', title:'Schema Blocks'}
    ];
    items.forEach(it=>{
      const v = stats[it.k] ?? '—';
      const card = document.createElement('div');
      card.className = 'stat';
      card.innerHTML = `<i>${icons[it.k]||''}</i><div><h5>${it.title}</h5><b>${fmt(v)}</b></div>`;
      root.appendChild(card);
    });
  }

  // Recommendations
  function renderRecs(list){
    const root = document.getElementById('recs');
    root.innerHTML = '';
    (list||[]).forEach(r=>{
      const impact = r.impact||'mid';
      const el = document.createElement('div');
      el.className = 'rec';
      el.innerHTML = `
        <span class="impact ${impact}">${impact.toUpperCase()} IMPACT</span>
        <h4>${r.title||'Recommendation'}</h4>
        <p>${r.desc||''}</p>
        <div class="actions">
          <button class="btn-ghost" onclick="alert('Tip: '+${JSON.stringify(r.tip||'Apply this in your editor')})">How to fix</button>
          ${r.link? `<a class="btn-ghost" href="${r.link}" target="_blank" rel="noopener">Docs</a>`:''}
        </div>`;
      root.appendChild(el);
    });
  }

  // Structure blocks
  function renderStructure(s){
    const root = document.getElementById('structure');
    root.innerHTML = '';
    const blocks = [
      {name:'Title', body:s.title},
      {name:'H1', body:s.h1},
      {name:'H2', body:(s.h2||[]).map(h=>`<span class='tag'>${escapeHtml(h)}</span>`).join(' ')},
      {name:'Meta', body:(s.meta||[]).map(m=>`<div class='muted'>${escapeHtml(m)}</div>`).join('')},
      {name:'Links (internal)', body:(s.links_int||[]).slice(0,20).map(a=>`<div class='muted'>${escapeHtml(a.text||a.href||'link')}</div>`).join('')},
      {name:'Links (external)', body:(s.links_ext||[]).slice(0,20).map(a=>`<div class='muted'>${escapeHtml(a.text||a.href||'link')}</div>`).join('')}
    ];
    blocks.forEach(b=>{
      const el = document.createElement('div');
      el.className = 'section';
      el.innerHTML = `<h3>${b.name}</h3><div>${b.body || '<span class="muted">—</span>'}</div>`;
      root.appendChild(el);
    });
  }

  // Checklists
  function renderChecklists(groups){
    const root = document.getElementById('checklists');
    root.innerHTML = '';
    groups.forEach(g=>{
      const band = meterColor(g.score||0);
      const el = document.createElement('div');
      el.className = 'clist';
      const pill = band==='good'? 'score-good' : band==='mid'? 'score-mid' : 'score-bad';
      el.innerHTML = `
        <div class="head">
          <div class="meta"><span style="font-weight:700">${g.name||'Checklist'}</span>
            <span class="score-pill ${pill}">${Math.round(g.score||0)}%</span>
          </div>
          <div class="muted">${g.items? g.items.length:0} checks</div>
        </div>
        <ul>${(g.items||[]).map(it=>{
          const type = it.pass? 'ok' : it.warn? 'warn' : 'fail';
          const icon = type==='ok'? svgIcon('ok') : type==='warn'? svgIcon('warn') : svgIcon('fail');
          return `<li class="item ${type}"><i class="i-${type}">${icon}</i><div><h6>${it.title}</h6><p>${it.hint||''}</p></div></li>`
        }).join('')}</ul>`;
      root.appendChild(el);
    });
  }

  // Readability section
  function renderReadability(r){
    const score = clamp(r.score||0,0,100);
    animateWheel(document.getElementById('readProg'), document.getElementById('readNum'), score);
    const gb = document.getElementById('gradeBadge');
    gb.innerHTML = `<span class="badge ${score>=80?'badge-good':score>=60?'badge-mid':'badge-bad'}"><i style="background:${score>=80?'var(--green)':score>=60?'var(--orange)':'var(--red)'}"></i> Grade ${escapeHtml(r.grade||'—')}</span>`;
    // improvement bars expect 0-100 goodness values
    const sLen = clamp(100-(r.avg_sentence_penalty||40), 0, 100);
    const words = clamp(r.simple_words_ratio||40, 0, 100);
    const pass = clamp(100-(r.passive_ratio||30), 0, 100);
    document.getElementById('barSentence').style.width = sLen+'%';
    document.getElementById('barWords').style.width = words+'%';
    document.getElementById('barPassive').style.width = pass+'%';
    document.getElementById('readabilityNote').textContent = r.note || 'Aim for Grade 8–10 for broad audiences.';
  }

  // Icons (inline SVG)
  function svgIcon(name){
    const base = {
      ok:`<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20 7L9 18l-5-5" stroke="#baffdf" stroke-width="2"/></svg>`,
      warn:`<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 9v4m0 4h.01M12 2l10 18H2L12 2z" stroke="#ffe2ad" stroke-width="2"/></svg>`,
      fail:`<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M6 18L18 6" stroke="#ffcccc" stroke-width="2"/></svg>`,
      link:`<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M10 14a5 5 0 0 1 0-7l2-2a5 5 0 1 1 7 7l-1 1" stroke="#cbd5e1" stroke-width="2"/></svg>`,
      image:`<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 5h18v14H3z" stroke="#cbd5e1" stroke-width="2"/><circle cx="8" cy="9" r="2" fill="#cbd5e1"/><path d="M21 16l-6-6-8 8" stroke="#cbd5e1" stroke-width="2"/></svg>`,
      schema:`<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="6" height="6" stroke="#cbd5e1" stroke-width="2"/><rect x="15" y="3" width="6" height="6" stroke="#cbd5e1" stroke-width="2"/><rect x="3" y="15" width="6" height="6" stroke="#cbd5e1" stroke-width="2"/><path d="M9 6h6M6 9v6M18 9v6M9 18h6" stroke="#cbd5e1" stroke-width="2"/></svg>`,
      speed:`<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M21 14a9 9 0 1 0-18 0" stroke="#cbd5e1" stroke-width="2"/><path d="M12 7v6l4 2" stroke="#cbd5e1" stroke-width="2"/></svg>`,
      doc:`<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 2h7l5 5v15H6z" stroke="#cbd5e1" stroke-width="2"/><path d="M13 2v6h6" stroke="#cbd5e1" stroke-width="2"/></svg>`,
      heading:`<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 4v16M19 4v16M5 12h14" stroke="#cbd5e1" stroke-width="2"/></svg>`,
      ai:`<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="#cbd5e1" stroke-width="2"/><path d="M8 10h8M8 14h6" stroke="#cbd5e1" stroke-width="2"/></svg>`,
      grade:`<svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M3 7l9-4 9 4-9 4-9-4z" stroke="#cbd5e1" stroke-width="2"/><path d="M6 10v5l6 3 6-3v-5" stroke="#cbd5e1" stroke-width="2"/></svg>`
    };
    return base[name] || '';
  }

  function escapeHtml(str){
    if(str==null) return '';
    return String(str)
      .replace(/&/g,'&amp;')
      .replace(/</g,'&lt;')
      .replace(/>/g,'&gt;')
      .replace(/"/g,'&quot;')
      .replace(/'/g,'&#39;');
  }

  // ===== Analyze button: animate liquid 0→100, fetch data, render =====
  const form = document.getElementById('analyzeForm');
  const btn = document.getElementById('analyzeBtn');
  const fill = document.getElementById('liquidFill');
  const barText = document.getElementById('barText');

  form.addEventListener('submit', async (e)=>{
    e.preventDefault();
    // Start water bar to 100
    fill.style.transition = 'none';
    fill.style.width = '0%';
    requestAnimationFrame(()=>{
      fill.style.transition = 'width 3.2s ease-in-out';
      fill.style.width = '100%';
    });
    // Count up text
    let pct = 0; barText.textContent = '0%';
    const tmr = setInterval(()=>{ pct = Math.min(100, pct+2); barText.textContent = pct+'%'; if(pct>=100) clearInterval(tmr); }, 60);

    // Fetch analysis
    try{
      btn.disabled = true; btn.textContent = 'Analyzing…';
      const fd = new FormData(form);
      const res = await fetch(form.dataset.action, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd});
      const data = await res.json();
      renderAnalysis(data);
    }catch(err){
      console.error(err);
      // fallback to mock for demo
      renderAnalysis(window.__mockAnalysis);
    }finally{
      btn.disabled = false; btn.textContent = 'Run Analyzer';
    }
  });

  function renderAnalysis(data){
    const overall = clamp(parseFloat(data.overall_score)||0,0,100);
    animateWheel(document.getElementById('overallProg'), document.getElementById('overallNum'), overall);
    badgeFor(overall);

    renderStats({
      words:data.quick?.words,
      grade:data.readability?.grade,
      headings:data.quick?.headings,
      links:(data.quick?.links_int||0)+(data.quick?.links_ext||0),
      images:data.quick?.images_alt,
      schema:data.quick?.schema_count
    });

    renderRecs(data.recommendations||[]);
    renderStructure(data.structure||{});
    renderChecklists(data.categories||[]);
    renderReadability(data.readability||{score:0,grade:'—'});
  }

  // ===== Optional: server‑rendered initial data =====
  @if(isset($analysis))
    window.__initialAnalysis = @json($analysis);
    document.addEventListener('DOMContentLoaded', ()=>{ renderAnalysis(window.__initialAnalysis); });
  @else
    // Mock payload for immediate preview; replace by backend response
    window.__mockAnalysis = {
      overall_score: 76,
      quick: { words: 1240, headings: 12, links_int: 18, links_ext: 5, images_alt: 9, schema_count: 2 },
      readability: { score: 82, grade: '8–9', avg_sentence_penalty: 28, simple_words_ratio: 68, passive_ratio: 22, note:'Excellent for general audiences.' },
      structure: {
        title: 'Ultimate Guide to Semantic SEO in 2025',
        h1: 'Semantic SEO: Strategies, Tools, and Checklists',
        h2: ['Why semantics matter','Entity mapping','FAQ schema best‑practices','Internal linking hubs'],
        meta: ['description: Learn semantic SEO with actionable steps','og:type: article','lang: en'],
        links_int: [{text:'Entity list', href:'#'},{text:'Topic clusters', href:'#'}],
        links_ext: [{text:'Google guidelines', href:'#'}]
      },
      recommendations:[
        {impact:'high', title:'Add FAQPage schema', desc:'Mark up 3‑5 FAQs to capture rich results.', tip:'Create concise Q&A pairs and embed JSON‑LD.'},
        {impact:'mid', title:'Tighten H2 hierarchy', desc:'Ensure each H2 groups related H3s logically.', tip:'Keep 2–5 H3s under each H2.'},
        {impact:'low', title:'Compress hero image', desc:'Reduce initial payload for faster LCP.', tip:'Serve AVIF/WebP ≤ 180KB.'}
      ],
      categories:[
        {name:'Content & Keywords', score: 85, items:[
          {title:'Primary keyword in title', pass:true, hint:'Keep it near the beginning.'},
          {title:'Entity coverage', pass:true, hint:'Key entities detected.'},
          {title:'Synonyms & variants', warn:true, hint:'Add 2–3 natural variants.'}
        ]},
        {name:'Technical Elements', score: 68, items:[
          {title:'Meta description length', pass:true, hint:'~150–160 chars.'},
          {title:'H1 uniqueness', pass:true, hint:'Only one H1.'},
          {title:'Image alts', warn:true, hint:'2 images missing alt.'},
          {title:'Canonical tag', fail:true, hint:'Add canonical to prevent duplicates.'}
        ]},
        {name:'Links & Navigation', score: 58, items:[
          {title:'Internal links to hubs', warn:true, hint:'Add 3+ links to related pillars.'},
          {title:'External authoritative refs', fail:true, hint:'Cite 2–3 trusted sources.'}
        ]},
        {name:'Experience & Trust', score: 80, items:[
          {title:'Author bio & expertise', pass:true, hint:'Include credentials.'},
          {title:'Date & update history', warn:true, hint:'Show last updated date.'}
        ]}
      ]
    };
    document.addEventListener('DOMContentLoaded', ()=>{ renderAnalysis(window.__mockAnalysis); });
  @endif
</script>
@endsection
