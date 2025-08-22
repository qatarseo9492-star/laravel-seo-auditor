{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Semantic SEO Master Analyzer</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      margin:0;
      font-family:sans-serif;
      background: #0a0015;
      color:#fff;
      overflow-x:hidden;
    }
    header {
      text-align:center;
      padding:2rem 1rem;
    }
    header h1 {
      font-size:3rem;
      font-weight:800;
      background:linear-gradient(90deg,#9b5cff,#ff2045);
      -webkit-background-clip:text;
      -webkit-text-fill-color:transparent;
    }

    /* wheel */
    .score-wheel {
      width:200px;height:200px;
      margin:2rem auto;
      position:relative;
    }
    .score-wheel svg {
      transform:rotate(-90deg);
      width:100%;height:100%;
    }
    .score-text {
      font-size:3rem;
      font-weight:1000;
      fill:#ffffff;
      filter:drop-shadow(0 0 6px rgba(0,0,0,.35))
    }
    .circle-bg {
      fill:none; stroke:#333; stroke-width:15;
    }
    .progress {
      fill:none; stroke:url(#grad); stroke-width:15;
      stroke-linecap:round;
      stroke-dasharray: 565;
      stroke-dashoffset: 565;
      transition:stroke-dashoffset 1s ease, stroke .5s ease;
    }

    /* checklist grid */
    .grid {display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:1rem;}
    .card {
      background:rgba(255,255,255,.05);
      border-radius:1rem;
      padding:1rem;
      transition:.3s;
    }
    .card:hover {background:rgba(255,255,255,.1);}
    .card h3 {margin:0 0 .5rem;font-size:1.2rem;}

    /* clouds effect background */
    .clouds {
      position:fixed; bottom:0; right:0;
      width:100%; height:100%;
      pointer-events:none;
      z-index:-1;
      background:url('https://i.ibb.co/3h9YBfd/clouds.png') repeat-x bottom;
      animation:cloudmove 60s linear infinite;
      opacity:0.35;
    }
    @keyframes cloudmove {
      from {background-position-x:0;}
      to {background-position-x:-2000px;}
    }

    /* AI badge */
    #aiBadge{margin-top:1rem;font-size:1.1rem;}
    #aiBadge b{color:#ff79c6}

    .chip{display:inline-block;padding:.2rem .5rem;border-radius:.5rem;background:#222;margin:.2rem;}
  </style>
</head>
<body>

<div class="clouds"></div>

<header>
  <h1>Semantic SEO Master Analyzer</h1>
</header>

<div class="score-wheel">
  <svg viewBox="0 0 200 200">
    <circle class="circle-bg" cx="100" cy="100" r="90"/>
    <circle class="progress" cx="100" cy="100" r="90" stroke-dasharray="565" stroke-dashoffset="565"/>
    <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="score-text" id="overallScore">0</text>
  </svg>
</div>
<p style="text-align:center">Overall: <span id="overallScoreInline">0</span>/100</p>

<div id="aiBadge" style="text-align:center"></div>
<div style="text-align:center">
  <span class="chip">Human <span id="humanPct">—</span>%</span>
  <span class="chip">AI <span id="aiPct">—</span>%</span>
</div>

<section class="grid" style="padding:2rem;">
  <div class="card"><h3>Content & Keywords</h3>
    <ul>
      <li><input type="checkbox" id="ck-1"> Search Intent</li>
      <li><input type="checkbox" id="ck-2"> Keyword Map</li>
      <li><input type="checkbox" id="ck-3"> H1 Quality</li>
      <li><input type="checkbox" id="ck-4"> FAQ Schema</li>
    </ul>
  </div>
  <div class="card"><h3>Technical Elements</h3>
    <ul>
      <li><input type="checkbox" id="ck-6"> Title length</li>
      <li><input type="checkbox" id="ck-7"> Meta description</li>
      <li><input type="checkbox" id="ck-8"> Canonical</li>
      <li><input type="checkbox" id="ck-9"> Indexable</li>
    </ul>
  </div>
  <!-- ... repeat for all other categories/cards ... -->
</section>

<svg width="0" height="0" aria-hidden="true">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#9b5cff"/><stop offset="100%" stop-color="#ff2045"/></linearGradient>
    <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/></linearGradient>
    <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/></linearGradient>
    <linearGradient id="gradBad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/></linearGradient>
  </defs>
</svg>

<script>
const WHEEL = { circumference: 565, circle: null, text: null };

function setScoreWheel(value){
  if (!WHEEL.circle) {
    WHEEL.circle = document.querySelector('.score-wheel .progress');
    WHEEL.text   = document.getElementById('overallScore');
  }
  const v = Math.max(0, Math.min(100, value));
  const offset = WHEEL.circumference - (v/100) * WHEEL.circumference;
  WHEEL.circle.style.strokeDashoffset = offset;

  if (v >= 80) {
    WHEEL.circle.setAttribute('stroke','url(#gradGood)');
  } else if (v >= 60) {
    WHEEL.circle.setAttribute('stroke','url(#gradMid)');
  } else {
    WHEEL.circle.setAttribute('stroke','url(#gradBad)');
  }

  const n = Math.round(v);
  WHEEL.text.textContent = n;
  document.getElementById('overallScoreInline').textContent = n;
}

// Example analyze success handler
function updateFromData(data){
  // score
  setScoreWheel(data.overall_score||0);

  // AI badge fix
  const ai = data.ai_detection || {};
  const badge = document.getElementById('aiBadge');
  const labelMap = { likely_human:'Likely Human', mixed:'Mixed', likely_ai:'Likely AI', unknown:'Unknown' };
  const label = labelMap[ai.label] || 'Unknown';
  const humanPct = (typeof ai.human_pct==='number') ? ai.human_pct : null;
  const aiPct    = (typeof ai.ai_pct==='number') ? ai.ai_pct : null;
  let parts = [`<b>${label}</b>`];
  if (humanPct!==null) parts.push(`Human ${humanPct}%`);
  if (aiPct!==null) parts.push(`AI ${aiPct}%`);
  badge.innerHTML = `Writer: ${parts.join(' • ')}`;
  badge.title = (ai.reasons||[]).join(' • ');
  if(document.getElementById('humanPct')) document.getElementById('humanPct').textContent = humanPct!==null?humanPct:'—';
  if(document.getElementById('aiPct')) document.getElementById('aiPct').textContent = aiPct!==null?aiPct:'—';
}
</script>
</body>
</html>
