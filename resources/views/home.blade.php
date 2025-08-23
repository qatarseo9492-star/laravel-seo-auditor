{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Semantic SEO Master • Ultra Tech Global</title>

<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
:root{
  --bg:#08080f; --panel:#0f1022; --panel-2:#141433; --line:#1e1a33;
  --text:#f0effa; --text-dim:#b6b3d6; --text-muted:#9aa0c3;
  --primary:#9b5cff; --secondary:#ff2045; --accent:#3de2ff;
  --good:#16c172; --warn:#f59e0b; --bad:#ef4444;
  --radius:18px; --shadow:0 10px 40px rgba(0,0,0,.55);
  --container:1200px;
}
*{box-sizing:border-box} html,body{height:100%}
html{scroll-behavior:smooth}
body{
  margin:0; color:var(--text);
  font-family:Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto;
  background:
    radial-gradient(1200px 700px at 0% -10%, #201046 0%, transparent 55%),
    radial-gradient(1100px 800px at 110% 0%, #1a0f2a 0%, transparent 50%),
    var(--bg);
  overflow-x:hidden;
}

/* BG FX */
#linesCanvas,#linesCanvas2{position:fixed;inset:0;z-index:0;pointer-events:none;opacity:.85}
#smokeFX{position:fixed;inset:0;z-index:1;pointer-events:none;mix-blend-mode:screen;filter:saturate(115%) contrast(105%)}

/* Layout */
.wrap{position:relative;z-index:3;max-width:var(--container);margin:0 auto;padding:28px 5%}

/* Header */
header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 22px;border-bottom:1px solid var(--line);backdrop-filter:saturate(140%) blur(10px);background:rgba(15,16,34,.35)}
.brand{display:flex;align-items:center;gap:1rem}
.brand-badge{width:64px;height:64px;border-radius:16px;display:grid;place-items:center;background:linear-gradient(135deg,rgba(155,92,255,.3),rgba(255,32,69,.25));border:1px solid rgba(255,255,255,.08); color:#ffd1dc}
.hero-heading{font-size:3.3rem;font-weight:1000;line-height:1.02;margin:.1rem 0;letter-spacing:.6px;background:linear-gradient(90deg,#b892ff,#ff2045 55%,#ff8a5b 100%);-webkit-background-clip:text;-webkit-text-fill-color:transparent}

/* Chips & buttons */
.badge{display:inline-flex;align-items:center;gap:.5rem;padding:.5rem .8rem;border-radius:999px;font-weight:900;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06)}
.badge i{opacity:.9}
.badge-green{background:rgba(22,193,114,.18);border-color:rgba(22,193,114,.42)}
.badge-orange{background:rgba(245,158,11,.20);border-color:rgba(245,158,11,.45)}
.badge-red{background:rgba(239,68,68,.22);border-color:rgba(239,68,68,.50)}
.btn{--pad:.75rem 1.05rem;display:inline-flex;align-items:center;gap:.5rem;padding:var(--pad);border-radius:14px;border:1px solid transparent;cursor:pointer;font-weight:800;letter-spacing:.2px;transition:.2s}
.btn-gradient{background:linear-gradient(135deg,#ff2045,#ff7a59);box-shadow:0 10px 26px rgba(255,32,69,.35)}
.btn-gradient:hover{transform:translateY(-2px);box-shadow:0 14px 38px rgba(255,32,69,.45)}
.btn-neon{background:linear-gradient(135deg,#3de2ff,#9b5cff);box-shadow:0 8px 30px rgba(61,226,255,.30);color:#02131e}
.btn-neon:hover{transform:translateY(-2px);box-shadow:0 12px 36px rgba(61,226,255,.40)}
.btn-ghost{background:rgba(255,255,255,.05);border-color:rgba(255,255,255,.16);color:#fff}
.btn-ghost:hover{background:rgba(255,255,255,.08);transform:translateY(-2px)}

/* Analyzer container */
.analyzer{margin-top:24px;background:var(--panel);border:1px solid rgba(255,255,255,.08);border-radius:22px;box-shadow:var(--shadow);padding:24px}

/* ===== New Stylish Score Wheel ===== */
.score-hero{position:relative;margin:0 0 8px;border-radius:22px;padding:18px;background:
  radial-gradient(140% 200% at 10% 0%, rgba(157,92,255,.10), transparent 55%),
  radial-gradient(140% 160% at 90% 100%, rgba(61,226,255,.10), transparent 50%),
  linear-gradient(180deg, rgba(255,255,255,.05), rgba(255,255,255,.02));
border:1px solid rgba(255,255,255,.08);overflow:hidden}
.score-hero::before{/* soft animated rim */
  content:"";position:absolute;inset:-2px;padding:2px;border-radius:24px;
  background:conic-gradient(from 0deg, #3de2ff55, #9b5cff55, #ff7a5955, #16c17255, #3de2ff55);
  -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
  -webkit-mask-composite:xor;mask-composite:exclude;animation:rim 10s linear infinite;
}
@keyframes rim{to{transform:rotate(360deg)}}
.score-grid{display:grid;grid-template-columns:auto 1fr;gap:18px;align-items:center}
.wheel-wrap{position:relative;width:250px;aspect-ratio:1}
.wheel{width:100%;height:100%}
.wheel .track{stroke:rgba(255,255,255,.16);stroke-width:16;fill:none;filter:url(#innerShadow)}
.wheel .ticks{stroke:rgba(255,255,255,.18);stroke-width:2;stroke-dasharray:2 10;stroke-linecap:round;fill:none;opacity:.8}
.wheel .progress{stroke:url(#wheelGrad);stroke-width:16;stroke-linecap:round;fill:none;stroke-dasharray:339;stroke-dashoffset:339;filter:url(#glow)}
.wheel .halo{fill:none;stroke:rgba(255,255,255,.10);stroke-width:28;filter:url(#blurHalo)}
.wheel-center{position:absolute;inset:50% auto auto 50%;transform:translate(-50%,-50%);width:70%;height:70%;border-radius:50%;
  backdrop-filter:blur(6px) saturate(140%);background:radial-gradient(120% 140% at 50% 0%, rgba(255,255,255,.14), rgba(255,255,255,.02));
  border:1px solid rgba(255,255,255,.14);box-shadow:inset 0 0 40px rgba(0,0,0,.35);}
.wheel-score{position:absolute;inset:50% auto auto 50%;transform:translate(-50%,-50%);font-size:3rem;font-weight:1000}
.wheel-label{position:absolute;left:50%;top:60%;transform:translate(-50%,0);opacity:.8;font-weight:800;letter-spacing:.4px}

/* URL card */
.url-card{margin-top:12px;background:
  radial-gradient(160% 180% at 100% 0%, rgba(157,92,255,.10), transparent 45%),
  radial-gradient(150% 180% at 0% 100%, rgba(61,226,255,.10), transparent 45%),
  rgba(255,255,255,.03);
border:1px solid rgba(255,255,255,.10);border-radius:18px;padding:14px;box-shadow:0 18px 44px rgba(0,0,0,.35)}
.input-wrap{position:relative}
.input-wrap .icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:28px;height:28px;display:grid;place-items:center;border-radius:10px;background:linear-gradient(135deg,#3de2ff33,#9b5cff33);border:1px solid rgba(255,255,255,.16);color:#dce6ff}
.analyze-form input[type="url"]{width:100%;padding:1rem 1.2rem 1rem 52px;border-radius:14px;border:1px solid #1b1b35;background:#0b0d21;color:var(--text);box-shadow:0 0 0 0 rgba(155,92,255,.0);transition:.25s}
.analyze-form input[type="url"]:focus{outline:none;border-color:#5942ff;box-shadow:0 0 0 6px rgba(155,92,255,.15)}
.analyze-row{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;margin-top:.5rem}

/* Quick report chips */
.report-chips{display:flex;flex-wrap:wrap;gap:.6rem;margin-top:.8rem}
.report-chips .chip{padding:.45rem .75rem;border-radius:999px;font-weight:900;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12)}
.chip{padding:.25rem .6rem;border-radius:999px;font-weight:800;background:rgba(155,92,255,.14);border:1px solid rgba(155,92,255,.28)}

/* Progress + checklist (unchanged visuals) */
.progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
.progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
.progress-fill{height:100%;background:linear-gradient(135deg,#9b5cff,#ff2045);width:0%;transition:width .35s ease}
.progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}
.analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
.category-card{position:relative;grid-column:span 6;background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.03));border:1px solid rgba(255,255,255,.08);border-radius:18px;padding:16px;box-shadow:var(--shadow);overflow:hidden}
.category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
.category-icon{width:46px;height:46px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#3de2ff33,#9b5cff33);color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,.18)}
.category-title{margin:0;font-size:1.08rem;background:linear-gradient(90deg,#3de2ff,#9b5cff,#ff2045);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900}
.category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}
.checklist{list-style:none;margin:10px 0 0;padding:0}
.checklist-item{--accent: rgba(255,255,255,.12);position:relative;display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;padding:.75rem .8rem .75rem 1rem;border-radius:14px;border:1px solid rgba(255,255,255,.10);background:linear-gradient(180deg,rgba(255,255,255,.035),rgba(255,255,255,.03))}
.checklist-item + .checklist-item{margin-top:.28rem}
.checklist-item::after{content:"";position:absolute;left:0;top:0;bottom:0;width:6px;background:var(--accent);box-shadow:0 0 20px var(--accent)}
.score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
.score-good{background:rgba(22,193,114,.22); border-color:rgba(22,193,114,.45)}
.score-mid{ background:rgba(245,158,11,.22); border-color:rgba(245,158,11,.45)}
.score-bad{ background:rgba(239,68,68,.24); border-color:rgba(239,68,68,.5)}
.improve-btn{padding:.4rem .75rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);font-weight:900;cursor:pointer}
.improve-btn:hover{background:rgba(255,255,255,.1)}

/* Footer */
footer.site{ margin-top:28px;padding:18px 5%;background:rgba(255,255,255,.04);border-top:1px solid rgba(255,255,255,.12);display:flex;align-items:center;justify-content:space-between;gap:1rem;backdrop-filter:blur(6px)}
.footer-links a{color:var(--text-dim);margin-left:.9rem}
.footer-links a:hover{color:#fff;text-decoration:underline}
#backTop{position:fixed;right:18px;bottom:18px;z-index:90;width:48px;height:48px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(255,255,255,.07);display:grid;place-items:center;color:#fff;cursor:pointer;display:none}
#backTop:hover{background:rgba(255,255,255,.12)}
@media (max-width:992px){.analyzer-grid .category-card{grid-column:span 12}.wheel-wrap{width:200px}.hero-heading{font-size:2.4rem}}
@media print{#linesCanvas,#linesCanvas2,#smokeFX,header.site,#backTop{display:none!important}}
</style>
</head>
<body>
<canvas id="linesCanvas"></canvas>
<canvas id="linesCanvas2"></canvas>
<canvas id="smokeFX" aria-hidden="true"></canvas>

<!-- SVG defs for wheel -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <linearGradient id="gradGreen" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/>
    </linearGradient>
    <linearGradient id="gradOrange" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/>
    </linearGradient>
    <linearGradient id="gradRed" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/>
    </linearGradient>
    <radialGradient id="halo" r="65%">
      <stop offset="60%" stop-color="rgba(255,255,255,0)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,.12)"/>
    </radialGradient>
    <filter id="glow">
      <feGaussianBlur stdDeviation="2.2" result="b"/><feMerge>
        <feMergeNode in="b"/><feMergeNode in="SourceGraphic"/>
      </feMerge>
    </filter>
    <filter id="innerShadow">
      <feOffset dx="0" dy="1"/><feGaussianBlur stdDeviation="1.5" result="o"/>
      <feComposite in="o" in2="SourceAlpha" operator="arithmetic" k2="-1" k3="1" />
      <feColorMatrix type="matrix" values="0 0 0 0 0   0 0 0 0 0   0 0 0 0 0   0 0 0 .45 0"/>
      <feComposite in2="SourceGraphic" operator="over"/>
    </filter>
    <filter id="blurHalo"><feGaussianBlur stdDeviation="6"/></filter>
  </defs>
</svg>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
      <div><div class="hero-heading">Semantic SEO Master Analyzer</div></div>
    </div>
    <div style="display:flex;gap:.5rem">
      <button class="btn btn-ghost" id="printTop"><i class="fa-solid fa-print"></i> Print</button>
    </div>
  </header>

  <section class="analyzer" id="analyzer">
    <div class="score-hero">
      <div class="score-grid">
        <!-- Wheel -->
        <div class="wheel-wrap">
          <svg class="wheel" viewBox="0 0 120 120" role="img" aria-label="Overall score">
            <!-- outer subtle halo -->
            <circle class="halo" cx="60" cy="60" r="54" stroke="url(#halo)"></circle>
            <!-- tick ring -->
            <circle class="ticks" cx="60" cy="60" r="54" stroke-dasharray="1 8" transform="rotate(-90 60 60)"/>
            <!-- base track -->
            <circle class="track" cx="60" cy="60" r="54"/>
            <!-- progress arc (stroke set in JS via grad) -->
            <circle class="progress" id="wheelProgress" cx="60" cy="60" r="54" transform="rotate(-90 60 60)"/>
          </svg>
          <div class="wheel-center" aria-hidden="true"></div>
          <div id="overallScore" class="wheel-score">0%</div>
          <div class="wheel-label">Overall</div>
        </div>

        <!-- Badges + URL -->
        <div>
          <div style="display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:.55rem">
            <span id="overallChip" class="badge"><i class="fa-solid fa-gauge"></i> Overall: <b id="overallScoreInline">0</b>/100</span>
            <span id="writerChip" class="badge badge-green"><i class="fa-solid fa-user-check"></i> Writer: <b>Likely Human</b></span>
            <button id="viewAIText" class="btn btn-neon" style="--pad:.45rem .8rem"><i class="fa-solid fa-robot"></i> Evidence</button>
            <span class="badge"><i class="fa-solid fa-user"></i> Human‑like: <b id="humanPct">—</b>%</span>
            <span class="badge"><i class="fa-solid fa-microchip"></i> AI‑like: <b id="aiPct">—</b>%</span>
          </div>

          <!-- URL card -->
          <div class="url-card">
            <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
              <label for="analyzeUrl" style="display:block;font-weight:900;margin-bottom:.45rem">Page URL</label>
              <div class="input-wrap">
                <span class="icon"><i class="fa-solid fa-link"></i></span>
                <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="Paste a page URL… e.g. https://example.com/guide"/>
              </div>
              <div class="analyze-row">
                <label style="display:inline-flex;align-items:center;gap:.5rem;cursor:pointer">
                  <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> Auto‑apply checkmarks (≥ 80)
                </label>
                <button id="analyzeBtn" class="btn btn-gradient" type="button"><i class="fa-solid fa-magnifying-glass"></i> Analyze</button>
                <button class="btn btn-neon" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> Print</button>
                <button class="btn btn-ghost" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> Reset</button>
              </div>
              <div id="analyzeStatus" style="margin-top:.6rem;color:var(--text-dim)"></div>
            </form>

            <div id="analyzeReport" style="display:none">
              <div class="report-chips">
                <span class="chip">HTTP: <b id="rStatus">—</b></span>
                <span class="chip">Title: <b id="rTitleLen">—</b></span>
                <span class="chip">Meta desc: <b id="rMetaLen">—</b></span>
                <span class="chip">Canonical: <b id="rCanonical">—</b></span>
                <span class="chip">Robots: <b id="rRobots">—</b></span>
                <span class="chip">Viewport: <b id="rViewport">—</b></span>
                <span class="chip">H1/H2/H3: <b id="rHeadings">—</b></span>
                <span class="chip">Internal links: <b id="rInternal">—</b></span>
                <span class="chip">Schema: <b id="rSchema">—</b></span>
                <span class="chip">Auto‑checked: <b id="rAutoCount">—</b></span>
              </div>
            </div>
          </div>
        </div>
      </div><!-- /grid -->
    </div>

    <!-- Progress + Checklist (same as before; omitted for brevity) -->
    <div class="progress-wrap">
      <div class="progress-bar"><div class="progress-fill" id="progressBar"></div></div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    {{-- === Checklist === --}}
    <div class="analyzer-grid">
      @php $labels = [
        1=>'Define search intent & primary topic',
        2=>'Map target & related keywords (synonyms/PAA)',
        3=>'H1 includes primary topic naturally',
        4=>'Integrate FAQs / questions with answers',
        5=>'Readable, NLP‑friendly language',
        6=>'Title tag (≈50–60 chars) w/ primary keyword',
        7=>'Meta description (≈140–160 chars) + CTA',
        8=>'Canonical tag set correctly',
        9=>'Indexable & listed in XML sitemap',
        10=>'E‑E‑A‑T signals (author, date, expertise)',
        11=>'Unique value vs. top competitors',
        12=>'Facts & citations up to date',
        13=>'Helpful media (images/video) w/ captions',
        14=>'Logical H2/H3 headings & topic clusters',
        15=>'Internal links to hub/related pages',
        16=>'Clean, descriptive URL slug',
        17=>'Breadcrumbs enabled (+ schema)',
        18=>'Mobile‑friendly, responsive layout',
        19=>'Optimized speed (compression, lazy‑load)',
        20=>'Core Web Vitals passing (LCP/INP/CLS)',
        21=>'Clear CTAs and next steps',
        22=>'Primary entity clearly defined',
        23=>'Related entities covered with context',
        24=>'Valid schema markup (Article/FAQ/Product)',
        25=>'sameAs/Organization details present'
      ]; @endphp
      @foreach ([
        ['Content & Keywords',1,5,'fa-pen-nib'],
        ['Technical Elements',6,9,'fa-code'],
        ['Content Quality',10,13,'fa-star'],
        ['Structure & Architecture',14,17,'fa-sitemap'],
        ['User Signals & Experience',18,21,'fa-user-check'],
        ['Entities & Context',22,25,'fa-database'],
      ] as $c)
        <article class="category-card">
          <header class="category-head">
            <span class="category-icon"><i class="fas {{ $c[3] }}"></i></span>
            <div>
              <h3 class="category-title">{{ $c[0] }}</h3>
              <p class="category-sub">—</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">{{ $c[2]-$c[1]+1 }}</span></span>
          </header>
          <ul class="checklist">
            @for($i=$c[1];$i<=$c[2];$i++)
              <li class="checklist-item" id="row-{{ $i }}" data-score="">
                <label><input type="checkbox" id="ck-{{ $i }}"> <span>{{ $labels[$i] }}</span></label>
                <span class="score-badge" id="sc-{{ $i }}">—</span>
                <button class="improve-btn" type="button" data-id="ck-{{ $i }}">Improve</button>
              </li>
            @endfor
          </ul>
        </article>
      @endforeach
    </div>
  </section>
</div>

<footer class="site">
  <div class="footer-links">
    <a href="#analyzer">Analyzer</a>
    <a href="#" id="toTopLink">Back to top</a>
  </div>
  <div class="footer-links">
    <a href="#">Privacy</a>
    <a href="#">Terms</a>
  </div>
</footer>
<button id="backTop" title="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

<!-- Simple modal for evidence -->
<div id="evidenceModal" style="display:none"></div>

<script>
/* Background lines */
(function(){
  function layer(id,count,maxDist,colorFn,vel=1){
    const c=document.getElementById(id),ctx=c.getContext('2d'); let w,h,nodes=[],mouse={x:-9999,y:-9999};
    function resize(){ w=c.width=innerWidth; h=c.height=innerHeight; nodes=Array.from({length:count},()=>({x:Math.random()*w,y:Math.random()*h,vx:(Math.random()-.5)*vel,vy:(Math.random()-.5)*vel})) }
    addEventListener('resize',resize,{passive:true}); resize();
    addEventListener('mousemove', e=>{mouse.x=e.clientX;mouse.y=e.clientY},{passive:true});
    (function loop(){
      ctx.clearRect(0,0,w,h);
      for(const n of nodes){
        const dx=mouse.x-n.x, dy=mouse.y-n.y, d=Math.hypot(dx,dy), a=d<maxDist?(1-d/maxDist)*.9:0;
        n.vx+=(dx/d||0)*a*.18; n.vy+=(dy/d||0)*a*.18; n.vx*=.97; n.vy*=.97; n.x+=n.vx; n.y+=n.vy;
        if(n.x<0||n.x>w) n.vx*=-1; if(n.y<0||n.y>h) n.vy*=-1;
      }
      for(let i=0;i<nodes.length;i++) for(let j=i+1;j<nodes.length;j++){
        const A=nodes[i],B=nodes[j], d=Math.hypot(A.x-B.x, A.y-B.y);
        if(d<maxDist){ const al=(1-d/maxDist)*.65; ctx.strokeStyle=colorFn(al); ctx.lineWidth=1; ctx.beginPath(); ctx.moveTo(A.x,A.y); ctx.lineTo(B.x,B.y); ctx.stroke(); }
      }
      requestAnimationFrame(loop);
    })();
  }
  layer('linesCanvas',140,130,a=>`rgba(61,226,255,${a})`,1.1);
  layer('linesCanvas2',110,120,a=>`rgba(255,32,69,${a*0.55})`,0.9);
})();

/* Subtle smoke (WebGL2) */
(function(){
  const canvas=document.getElementById('smokeFX'); if(!canvas) return;
  let gl; try{ gl=canvas.getContext('webgl2',{alpha:true,antialias:false,depth:false,stencil:false}); }catch(e){}
  const dpr=Math.min(2,devicePixelRatio||1);
  function resize(){ canvas.width=innerWidth*dpr; canvas.height=innerHeight*dpr; canvas.style.width=innerWidth+'px'; canvas.style.height=innerHeight+'px'; if(gl) gl.viewport(0,0,canvas.width,canvas.height); }
  addEventListener('resize',resize,{passive:true}); resize();
  if(!gl) return;
  const vs=`#version 300 es
  precision highp float; const vec2 V[3]=vec2[3](vec2(-1.,-1.),vec2(3.,-1.),vec2(-1.,3.));
  out vec2 uv; void main(){ vec2 p=V[gl_VertexID]; uv=.5*(p+1.); gl_Position=vec4(p,0,1); }`;
  const fs=`#version 300 es
  precision highp float; in vec2 uv; out vec4 o; uniform float t;
  float h(vec2 p){ return fract(sin(dot(p,vec2(127.1,311.7)))*43758.5453); }
  float n(vec2 p){ vec2 i=floor(p), f=fract(p); float a=h(i), b=h(i+vec2(1,0)), c=h(i+vec2(0,1)), d=h(i+vec2(1,1));
    vec2 u=f*f*(3.-2.*f); return mix(a,b,u.x)+(c-a)*u.y*(1.-u.x)+(d-b)*u.x*u.y; }
  float f(vec2 p){ float v=0., s=.5; mat2 m=mat2(1.6,1.2,-1.2,1.6); for(int i=0;i<5;i++){ v+=s*n(p); p=m*p; s*=.5; } return v; }
  void main(){
    vec2 p=(uv-vec2(1.,1.)) * vec2(2.2,2.0);
    p.x += t*.12; p.y += t*.09;
    float q=f(p*1.6);
    float d=smoothstep(.30,.95,q);
    vec3 base=mix(vec3(.24,.88,1.),vec3(.96,.31,.41),uv.x);
    vec3 c=mix(base, vec3(1.,.72,.28), 0.35*uv.y);
    o=vec4(c*d, .65*d);
  }`;
  function sh(src,type){const s=gl.createShader(type); gl.shaderSource(s,src); gl.compileShader(s); return s;}
  const prog=gl.createProgram(); gl.attachShader(prog,sh(vs,gl.VERTEX_SHADER)); gl.attachShader(prog,sh(fs,gl.FRAGMENT_SHADER)); gl.linkProgram(prog);
  const ut=gl.getUniformLocation(prog,'t');
  (function loop(t){ requestAnimationFrame(loop); gl.useProgram(prog); gl.uniform1f(ut,t*1e-3); gl.drawArrays(gl.TRIANGLES,0,3); })(performance.now());
})();

/* Back to top */
(function(){const b=document.getElementById('backTop');const l=document.getElementById('toTopLink');function s(){b.style.display=scrollY>300?'grid':'none'}addEventListener('scroll',s,{passive:true});s();[b,l].forEach(el=>el&&el.addEventListener('click',e=>{e.preventDefault();scrollTo({top:0,behavior:'smooth'})}))})();

/* ===== Wheel logic (green ≥80, orange 61–79, red ≤60) ===== */
const CIRC=339;
function paintWheel(v){
  const arc=document.getElementById('wheelProgress');
  const n=Math.max(0,Math.min(100,Number(v)||0));
  const off=CIRC - (n/100)*CIRC;
  arc.style.strokeDashoffset=off;
  // color rules
  let grad = 'url(#gradRed)';
  if(n>=80) grad = 'url(#gradGreen)';
  else if(n>60) grad = 'url(#gradOrange)'; // exactly 60 -> red
  arc.setAttribute('stroke', grad);
  document.getElementById('overallScore').textContent=Math.round(n)+'%';
  document.getElementById('overallScoreInline').textContent=Math.round(n);
  // chip color class
  const chip=document.getElementById('overallChip');
  chip.classList.remove('badge-green','badge-orange','badge-red');
  chip.classList.add(n>=80?'badge-green':(n>60?'badge-orange':'badge-red'));
}

/* Checklist storage, score pills, progress */
(function(){
  const STORAGE_KEY='semanticSeoChecklistV7';
  const total=25;
  const boxes=()=>Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
  const bar=document.getElementById('progressBar');
  const caption=document.getElementById('progressCaption');

  function setRowAccent(rowEl, score){
    if(!rowEl) return;
    let col='rgba(255,255,255,.12)';
    if(typeof score==='number'){
      if(score>=80) col='rgba(34,197,94,.6)';
      else if(score>=60) col='rgba(245,158,11,.6)';
      else col='rgba(239,68,68,.6)';
      rowEl.dataset.score=String(score);
    } else rowEl.dataset.score='';
    rowEl.style.setProperty('--accent', col);
  }

  function updateCats(){
    document.querySelectorAll('.category-card').forEach(card=>{
      const all=card.querySelectorAll('input[type="checkbox"]');
      const done=card.querySelectorAll('input[type="checkbox"]:checked');
      card.querySelector('.checked-count').textContent=done.length;
      card.querySelector('.total-count').textContent=all.length;
    });
  }
  function update(){
    const checked=boxes().filter(cb=>cb.checked).length;
    bar.style.width=((checked/total)*100)+'%';
    caption.textContent=`${checked} of ${total} items completed`;
    updateCats();
  }
  function load(){
    try{const saved=JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); boxes().forEach(cb=>cb.checked=saved.includes(cb.id));}catch(e){}
    update();
  }
  function save(){
    const ids=boxes().filter(cb=>cb.checked).map(cb=>cb.id);
    localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
  }
  document.addEventListener('change', e=>{ if(e.target.matches('#analyzer input[type="checkbox"]')){ update(); save(); }});
  document.getElementById('resetChecklist').addEventListener('click', ()=>{
    if(!confirm('Reset the checklist?')) return;
    localStorage.removeItem(STORAGE_KEY);
    boxes().forEach(cb=>cb.checked=false);
    for(let i=1;i<=25;i++){ setScoreBadge(i,null); setRowAccent(document.getElementById('row-'+i), null); }
    paintWheel(0); update();
  });
  document.getElementById('printChecklist').addEventListener('click', ()=>window.print());
  document.getElementById('printTop').addEventListener('click', ()=>window.print());

  // expose
  window.setScoreBadge=(num,score)=>{
    const pill=document.getElementById('sc-'+num), row=document.getElementById('row-'+num);
    if(!pill) return;
    pill.className='score-badge';
    if(score==null || Number.isNaN(score)){ pill.textContent='—'; setRowAccent(row,null); return; }
    const s=Math.round(score);
    pill.textContent=s;
    if(s>=80) pill.classList.add('score-good'); else if(s>=60) pill.classList.add('score-mid'); else pill.classList.add('score-bad');
    setRowAccent(row,s);
  };
  load();
})();

/* Analyze hookup */
function normalizeUrl(u){ if(!u) return ''; u=u.trim(); if(!/^https?:\/\//i.test(u)) u='https://'+u.replace(/^\/+/, ''); try{ new URL(u);}catch(e){} return u; }

(function(){
  const $=s=>document.querySelector(s);
  function setChecked(id,on){ const el=document.getElementById(id); if(el){ el.checked=!!on; el.classList.toggle('autoPulse', !!on); } }

  document.getElementById('analyzeForm').addEventListener('submit', e=>{ e.preventDefault(); document.getElementById('analyzeBtn').click(); });
  document.getElementById('analyzeBtn').addEventListener('click', analyze);

  window.__setAIData=function(ai){
    document.getElementById('aiPct').textContent=typeof ai?.ai_pct==='number'?ai.ai_pct:'—';
    document.getElementById('humanPct').textContent=typeof ai?.human_pct==='number'?ai.human_pct:'—';
    // Writer chip color (Likely Human/Mixed/Likely AI)
    const writer=document.getElementById('writerChip');
    writer.classList.remove('badge-green','badge-orange','badge-red');
    const map={likely_human:['badge-green','Likely Human'],mixed:['badge-orange','Mixed'],likely_ai:['badge-red','Likely AI'],unknown:['','Unknown']};
    const t=map[ai?.label||'unknown']; if(t){ if(t[0]) writer.classList.add(t[0]); writer.innerHTML=`<i class="fa-solid fa-user-check"></i> Writer: <b>${t[1]}</b>`; }
  };

  async function analyze(){
    const url=normalizeUrl($('#analyzeUrl').value);
    const status=$('#analyzeStatus'), btn=$('#analyzeBtn'), report=$('#analyzeReport');
    if(!url){ status.textContent='Please enter a URL.'; return; }
    status.textContent='Analyzing…'; btn.disabled=true; btn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Analyzing';

    try{
      const resp=await fetch('{{ route('analyze.json') }}', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
        body:JSON.stringify({url})
      });
      const data=await resp.json();
      if(!data.ok) throw new Error(data.error||'Failed');

      // quick chips
      $('#rStatus').textContent=data.status;
      $('#rTitleLen').textContent=(data.title||'').length;
      $('#rMetaLen').textContent=data.meta_description_len;
      $('#rCanonical').textContent=data.canonical?'Yes':'No';
      $('#rRobots').textContent=data.robots||'—';
      $('#rViewport').textContent=data.viewport?'Yes':'No';
      $('#rHeadings').textContent=`${data.counts.h1}/${data.counts.h2}/${data.counts.h3}`;
      $('#rInternal').textContent=data.counts.internal_links;
      $('#rSchema').textContent=(data.schema.found_types||[]).slice(0,6).join(', ')||'—';
      report.style.display='block';

      // per-item scores + auto-select
      let autoCount=0;
      for(let i=1;i<=25;i++){
        const key='ck-'+i, row=document.getElementById('row-'+i);
        const score=(data.scores && typeof data.scores[key]==='number') ? data.scores[key] : null;
        setScoreBadge(i, score);
        if(row) row.title = (score==null?'No score':`Score: ${Math.round(score)}`);
        if($('#autoApply').checked){
          if(typeof score==='number' && score>=80){ setChecked(key,true); autoCount++; }
          else { setChecked(key,false); }
        }
      }
      document.getElementById('rAutoCount').textContent = autoCount.toString();
      document.dispatchEvent(new Event('change'));

      // overall wheel color + % (rules: ≥80 green, 61–79 orange, ≤60 red)
      paintWheel(typeof data.overall_score==='number'?data.overall_score:0);

      // writer/human/ai
      window.__setAIData(data.ai_detection||{});

      // status message
      const n=parseInt(document.getElementById('overallScoreInline').textContent||'0',10);
      status.textContent = n>=80 ? 'Great! You passed — keep going.' : (n<=60 ? 'Score is low — optimize and re‑Analyze.' : 'Solid! Improve a few items to hit green.');
      setTimeout(()=> status.textContent='', 4200);

    }catch(e){
      status.textContent='Error: '+e.message;
    }finally{
      btn.disabled=false; btn.innerHTML='<i class="fa-solid fa-magnifying-glass"></i> Analyze';
    }
  }

  // initial paint (0%)
  paintWheel(0);
})();
</script>
</body>
</html>
