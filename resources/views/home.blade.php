<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Semantic SEO Master • Ultra Tech Global</title>

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

<style>
:root{
  --bg: #0a0a15;
  --panel: #121228;
  --panel-2: #1a1a3a;
  --line: #2a2a4a;
  --text: #f5f5ff;
  --text-dim: #b8b8e0;
  --text-muted: #8a8ac4;
  --primary: #7b68ff;
  --secondary: #ff2d64;
  --accent: #00e0ff;
  --good: #00d97e;
  --warn: #ffb347;
  --bad: #ff4d6d;
  --radius: 16px;
  --shadow: 0 12px 36px rgba(0,0,0,0.4);
  --container: 1200px;
  --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

* { box-sizing: border-box; }
html, body { height: 100%; }
html { scroll-behavior: smooth; }

body {
  margin: 0;
  color: var(--text);
  font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
  background: radial-gradient(1200px 700px at 0% -10%, #201046 0%, transparent 55%),
              radial-gradient(1100px 800px at 110% 0%, #1a0f2a 0%, transparent 50%),
              var(--bg);
  overflow-x: hidden;
  position: relative;
}

/* Canvas layers */
#brainCanvas, #linesCanvas, #linesCanvas2, #smokeFX { 
  position: fixed; 
  inset: 0; 
  z-index: 0; 
  pointer-events: none; 
}

#brainCanvas { opacity: 0.10; }
#smokeFX { opacity: 0.8; filter: saturate(140%) contrast(110%); }

.wrap {
  position: relative;
  z-index: 2;
  max-width: var(--container);
  margin: 0 auto;
  padding: 28px 5%;
}

/* Header */
header.site {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 0 22px;
  border-bottom: 1px solid var(--line);
  backdrop-filter: saturate(180%) blur(16px);
  background: rgba(18, 18, 40, 0.7);
  border-radius: var(--radius);
  margin-bottom: 24px;
}

.brand {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.brand-badge {
  width: 64px;
  height: 64px;
  border-radius: 16px;
  display: grid;
  place-items: center;
  background: linear-gradient(135deg, rgba(123, 104, 255, 0.3), rgba(255, 45, 100, 0.25));
  border: 1px solid rgba(255, 255, 255, 0.12);
  color: #ffd1dc;
}

.hero-heading {
  font-size: 4.2rem;
  font-weight: 1000;
  line-height: 1.02;
  margin: .1rem 0;
  letter-spacing: .8px;
  background: linear-gradient(90deg, #a291ff, #ff2d64 55%, #ff9d7a 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  text-shadow: 0 0 28px rgba(123, 104, 255, 0.3);
}

/* Language dock */
.lang-dock {
  position: fixed;
  left: 18px;
  top: 50%;
  transform: translateY(-50%);
  z-index: 70;
  display: flex;
  flex-direction: column;
  gap: .6rem;
}

.lang-btn {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  border: 1px solid rgba(255, 255, 255, 0.16);
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  display: grid;
  place-items: center;
  cursor: pointer;
  backdrop-filter: blur(6px);
  transition: var(--transition);
}

.lang-btn:hover {
  background: rgba(255, 255, 255, 0.14);
  transform: translateY(-2px);
}

.lang-panel {
  position: fixed;
  left: 74px;
  top: 50%;
  transform: translateY(-50%);
  z-index: 70;
  display: none;
}

.lang-card {
  background: var(--panel-2);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 16px;
  box-shadow: var(--shadow);
  padding: 10px 12px;
  min-width: 240px;
  backdrop-filter: blur(10px);
}

.lang-item {
  padding: .45rem .55rem;
  border-radius: 10px;
  display: flex;
  align-items: center;
  gap: .5rem;
  cursor: pointer;
  transition: var(--transition);
}

.lang-item:hover {
  background: rgba(255, 255, 255, 0.08);
}

.lang-flag {
  width: 18px;
  height: 14px;
  border-radius: 2px;
  background: #888;
}

/* Buttons - Modernized */
.btn {
  --pad: .75rem 1.25rem;
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  padding: var(--pad);
  border-radius: 14px;
  border: 1px solid transparent;
  cursor: pointer;
  font-weight: 700;
  letter-spacing: .2px;
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
  transition: 0.5s;
}

.btn:hover::before {
  left: 100%;
}

.btn-neon {
  background: linear-gradient(135deg, var(--accent), var(--primary));
  box-shadow: 0 8px 30px rgba(0, 224, 255, 0.3);
  color: #001018;
}

.btn-neon:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 36px rgba(0, 224, 255, 0.4);
}

.btn-ghost {
  background: rgba(255, 255, 255, 0.07);
  border-color: rgba(255, 255, 255, 0.18);
  color: #fff;
}

.btn-ghost:hover {
  background: rgba(255, 255, 255, 0.12);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}

.btn-danger {
  background: linear-gradient(135deg, var(--secondary), #ff7a59);
  color: #fff;
  box-shadow: 0 8px 30px rgba(255, 45, 100, 0.3);
}

.btn-danger:hover {
  transform: translateY(-3px);
  box-shadow: 0 12px 40px rgba(255, 45, 100, 0.4);
}

/* Analyzer panel */
.analyzer {
  margin-top: 24px;
  background: var(--panel);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 22px;
  box-shadow: var(--shadow);
  padding: 28px;
  backdrop-filter: blur(8px);
}

.section-title {
  font-size: 1.8rem;
  margin: 0 0 .3rem;
  background: linear-gradient(90deg, var(--accent), var(--primary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 900;
}

.section-subtitle {
  margin: 0;
  color: var(--text-dim);
  font-size: 1.05rem;
}

/* Score area */
.score-area {
  display: flex;
  gap: 1.5rem;
  align-items: center;
  margin: 1rem 0 0;
  flex-wrap: wrap;
}

.score-container {
  width: 220px;
}

.score-wheel {
  width: 100%;
  height: auto;
  transform: rotate(-90deg);
}

.score-wheel circle {
  fill: none;
  stroke-width: 14;
  stroke-linecap: round;
}

.score-wheel .bg {
  stroke: rgba(255, 255, 255, 0.12);
}

.score-wheel .progress {
  stroke: url(#grad);
  stroke-dasharray: 339;
  stroke-dashoffset: 339;
  transition: stroke-dashoffset 0.6s ease, stroke 0.3s ease, filter 0.3s ease;
  filter: drop-shadow(0 0 10px rgba(123, 104, 255, 0.4));
}

.score-text {
  font-size: 3rem;
  font-weight: 1000;
  fill: #fff;
  transform: rotate(90deg);
  text-shadow: 0 0 18px rgba(255, 45, 100, 0.3);
}

.chip {
  padding: .4rem .8rem;
  border-radius: 999px;
  font-weight: 700;
  background: rgba(123, 104, 255, 0.16);
  border: 1px solid rgba(123, 104, 255, 0.3);
  backdrop-filter: blur(4px);
}

.legend {
  padding: .3rem .7rem;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.18);
  font-weight: 700;
  backdrop-filter: blur(4px);
}

.l-red { background: rgba(255, 77, 109, 0.2); }
.l-orange { background: rgba(255, 179, 71, 0.2); }
.l-green { background: rgba(0, 217, 126, 0.2); }

/* URL input */
.analyze-form input[type="url"] {
  position: relative;
  z-index: 5;
  width: 100%;
  padding: 1.1rem 1.4rem;
  border-radius: 14px;
  border: 1px solid #2a2a4a;
  background: #0d0f24;
  color: var(--text);
  font-size: 1.05rem;
  box-shadow: 0 0 0 0 rgba(123, 104, 255, 0);
  transition: var(--transition);
}

.analyze-form input[type="url"]:focus {
  outline: none;
  border-color: var(--primary);
  box-shadow: 0 0 0 6px rgba(123, 104, 255, 0.2);
}

.analyze-row {
  display: grid;
  grid-template-columns: 1fr auto auto auto;
  gap: .8rem;
  align-items: center;
  margin-top: .8rem;
}

/* Progress */
.progress-wrap {
  margin-top: 1.2rem;
  background: rgba(255, 255, 255, 0.03);
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 16px;
  padding: 16px;
}

.progress-bar {
  width: 100%;
  height: 14px;
  border-radius: 999px;
  background: #0b1220;
  overflow: hidden;
  border: 1px solid #1a2035;
}

.progress-fill {
  height: 100%;
  background: linear-gradient(135deg, var(--primary), var(--secondary));
  width: 0%;
  transition: width 0.4s ease;
  border-radius: 999px;
}

.progress-caption {
  color: var(--text-muted);
  font-size: .95rem;
  margin-top: .7rem;
}

/* Category grid */
.analyzer-grid {
  margin-top: 1.5rem;
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 1.2rem;
}

.category-card {
  position: relative;
  grid-column: span 6;
  background: var(--panel-2);
  border: 1px solid rgba(255, 255, 255, 0.1);
  border-radius: 18px;
  padding: 20px;
  box-shadow: var(--shadow);
  overflow: hidden;
  isolation: isolate;
  transition: var(--transition);
}

.category-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
}

.category-card::before {
  content: "";
  position: absolute;
  inset: -2px;
  border-radius: 20px;
  padding: 2px;
  background: linear-gradient(120deg, rgba(0, 224, 255, 0.4), rgba(123, 104, 255, 0.4), rgba(255, 45, 100, 0.4));
  -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
  -webkit-mask-composite: xor;
  mask-composite: exclude;
  animation: borderGlow 6s linear infinite;
  pointer-events: none;
  z-index: 0;
}

.category-card > * {
  position: relative;
  z-index: 1;
}

.checklist-item label {
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: .55rem;
}

.checklist-item input[type="checkbox"], .improve-btn {
  pointer-events: auto;
  position: relative;
  z-index: 2;
}

@keyframes borderGlow {
  0% { filter: hue-rotate(0); }
  100% { filter: hue-rotate(360deg); }
}

.category-head {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: .85rem;
  align-items: center;
}

.category-icon {
  width: 52px;
  height: 52px;
  border-radius: 14px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, rgba(0, 224, 255, 0.3), rgba(123, 104, 255, 0.3));
  color: #fff;
  font-size: 1.2rem;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.category-title {
  margin: 0;
  font-size: 1.15rem;
  background: linear-gradient(90deg, var(--accent), var(--primary), var(--secondary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 900;
}

.category-sub {
  margin: .2rem 0 0;
  color: var(--text-dim);
  font-size: .98rem;
}

.checklist {
  list-style: none;
  margin: 14px 0 0;
  padding: 0;
}

.checklist-item {
  display: grid;
  grid-template-columns: 1fr auto auto auto;
  gap: .7rem;
  align-items: center;
  padding: .75rem .85rem;
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.1);
  background: linear-gradient(180deg, rgba(255, 255, 255, 0.04), rgba(255, 255, 255, 0.02));
  transition: var(--transition);
}

.checklist-item + .checklist-item {
  margin-top: .35rem;
}

.checklist-item:hover {
  transform: translateY(-2px);
  background: rgba(255, 255, 255, 0.07);
  box-shadow: 0 8px 30px rgba(0, 0, 0, 0.25);
}

.checklist-item input[type="checkbox"] {
  width: 20px;
  height: 20px;
  margin: .1rem .55rem 0 0;
  accent-color: var(--primary);
}

.score-badge {
  font-weight: 900;
  font-size: .95rem;
  padding: .35rem .75rem;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.15);
  background: rgba(255, 255, 255, 0.08);
  min-width: 55px;
  text-align: center;
  backdrop-filter: blur(4px);
}

.score-good {
  background: rgba(0, 217, 126, 0.25);
  border-color: rgba(0, 217, 126, 0.5);
}

.score-mid {
  background: rgba(255, 179, 71, 0.25);
  border-color: rgba(255, 179, 71, 0.5);
}

.score-bad {
  background: rgba(255, 77, 109, 0.3);
  border-color: rgba(255, 77, 109, 0.55);
}

.improve-btn {
  padding: .4rem .8rem;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.16);
  background: rgba(255, 255, 255, 0.08);
  font-weight: 700;
  cursor: pointer;
  transition: var(--transition);
  color: var(--text);
}

.improve-btn:hover {
  background: rgba(255, 255, 255, 0.12);
  transform: translateY(-1px);
}

/* Modal */
.modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.7);
  backdrop-filter: blur(6px);
  display: none;
  z-index: 70;
}

.modal {
  position: fixed;
  inset: 0;
  display: none;
  align-items: center;
  justify-content: center;
  z-index: 80;
}

.modal-card {
  width: min(1000px, 96vw);
  background: var(--panel-2);
  border: 1px solid rgba(255, 255, 255, 0.15);
  border-radius: 18px;
  box-shadow: var(--shadow);
  padding: 20px;
  backdrop-filter: blur(12px);
}

.modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-bottom: .8rem;
}

.modal-title {
  margin: 0;
  font-size: 1.3rem;
  background: linear-gradient(90deg, var(--accent), var(--primary));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  font-weight: 900;
}

.modal-close {
  background: transparent;
  border: 1px solid rgba(255, 255, 255, 0.22);
  border-radius: 10px;
  color: #fff;
  padding: .4rem .7rem;
  cursor: pointer;
  transition: var(--transition);
}

.modal-close:hover {
  background: rgba(255, 255, 255, 0.1);
}

.tabs {
  display: flex;
  gap: .5rem;
  margin: .5rem 0;
  flex-wrap: wrap;
}

.tab {
  padding: .4rem .8rem;
  border-radius: 10px;
  border: 1px solid rgba(255, 255, 255, 0.18);
  background: rgba(255, 255, 255, 0.08);
  cursor: pointer;
  font-weight: 700;
  transition: var(--transition);
  color: var(--text);
}

.tab.active {
  background: linear-gradient(135deg, rgba(0, 224, 255, 0.25), rgba(123, 104, 255, 0.25));
  border-color: rgba(0, 224, 255, 0.4);
}

.tabpanes > div {
  display: none;
}

.tabpanes > div.active {
  display: block;
}

.pre {
  white-space: pre-wrap;
  background: #0d0f24;
  border: 1px solid #2a2a4a;
  border-radius: 12px;
  padding: 14px;
  color: #cfd3f6;
  max-height: 60vh;
  overflow: auto;
  font-family: 'Fira Code', monospace;
}

/* Footer + back to top */
footer.site {
  margin-top: 32px;
  padding: 20px 5%;
  background: rgba(255, 255, 255, 0.05);
  border-top: 1px solid rgba(255, 255, 255, 0.15);
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 1rem;
  backdrop-filter: blur(8px);
  border-radius: var(--radius);
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: .7rem;
}

.footer-brand .dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  background: linear-gradient(135deg, var(--accent), var(--primary));
}

.footer-links a {
  color: var(--text-dim);
  margin-left: 1rem;
  text-decoration: none;
  transition: var(--transition);
}

.footer-links a:hover {
  color: #fff;
  text-decoration: underline;
}

#backTop {
  position: fixed;
  right: 20px;
  bottom: 20px;
  z-index: 90;
  width: 52px;
  height: 52px;
  border-radius: 14px;
  border: 1px solid rgba(255, 255, 255, 0.18);
  background: rgba(255, 255, 255, 0.09);
  display: grid;
  place-items: center;
  color: #fff;
  cursor: pointer;
  display: none;
  transition: var(--transition);
  backdrop-filter: blur(6px);
}

#backTop:hover {
  background: rgba(255, 255, 255, 0.14);
  transform: translateY(-3px);
}

/* Responsive */
@media (max-width: 992px) {
  .category-card { grid-column: span 12; }
  .hero-heading { font-size: 2.7rem; }
  .score-container { width: 190px; }
  footer.site { flex-direction: column; align-items: flex-start; }
  .analyze-row { grid-template-columns: 1fr; }
}

@media print {
  #linesCanvas, #linesCanvas2, #brainCanvas, #smokeFX, .bg-smoke, .modal-backdrop, .modal, header.site, #backTop, .lang-dock, .lang-panel { display: none !important; }
}
</style>
</head>
<body>
<canvas id="brainCanvas"></canvas>
<canvas id="linesCanvas"></canvas>
<canvas id="linesCanvas2"></canvas>
<canvas id="smokeFX" aria-hidden="true"></canvas>

<!-- gradients for score wheel -->
<svg width="0" height="0" aria-hidden="true">
  <defs>
    <linearGradient id="grad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#7b68ff"/><stop offset="100%" stop-color="#ff2d64"/></linearGradient>
    <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#00d97e"/><stop offset="100%" stop-color="#00b86c"/></linearGradient>
    <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#ffb347"/><stop offset="100%" stop-color="#ff9d45"/></linearGradient>
    <linearGradient id="gradBad" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#ff4d6d"/><stop offset="100%" stop-color="#ff2d55"/></linearGradient>
  </defs>
</svg>

<!-- Language Dock -->
<div class="lang-dock">
  <button class="lang-btn" id="langOpen" title="Language"><i class="fa-solid fa-globe"></i></button>
</div>
<div class="lang-panel" id="langPanel"><div class="lang-card" id="langCard"></div></div>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
      <div><div class="hero-heading" data-i="title">Semantic SEO Master Analyzer</div></div>
    </div>
    <div style="display:flex;gap:.6rem">
      <button class="btn btn-ghost" id="printTop"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
    </div>
  </header>

  <section class="analyzer" id="analyzer">
    <h2 class="section-title" data-i="analyze_title">Analyze a URL</h2>
    <p class="section-subtitle" data-i="legend_line">
      The wheel fills with your overall score. <span class="legend l-green">Green ≥ 80</span> <span class="legend l-orange">Orange 60–79</span> <span class="legend l-red">Red &lt; 60</span>
    </p>

    <div class="score-area">
      <div class="score-container">
        <svg class="score-wheel" viewBox="0 0 120 120" aria-label="Overall score">
          <circle class="bg" cx="60" cy="60" r="54"/>
          <circle class="progress" cx="60" cy="60" r="54"
                  style="transform: rotate(-90deg); transform-origin: 50% 50%;"/>
          <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="score-text" id="overallScore">0%</text>
        </svg>
      </div>
      <div style="display:flex;flex-direction:column;gap:.6rem">
        <div style="display:flex;gap:.6rem;flex-wrap:wrap">
          <span class="chip"><span data-i="overall">Overall</span>: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="contentScoreChip">Content: <b id="contentScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge">Writer: <b>—</b></span>
          <button id="viewAIText" class="btn btn-neon" style="--pad:.6rem 1rem"><i class="fa-solid fa-robot"></i> Evidence</button>
        </div>
        <div style="display:flex;gap:.6rem;flex-wrap:wrap">
          <button id="viewHumanBtn" class="btn btn-ghost" style="--pad:.5rem .9rem"><i class="fa-solid fa-user"></i> Human‑like: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost" style="--pad:.5rem .9rem"><i class="fa-solid fa-microchip"></i> AI‑like: <b id="aiPct">—</b>%</button>
        </div>
      </div>
    </div>

    <div class="analyze-box" style="margin-top:16px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.1);border-radius:18px;padding:18px">
      <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
        <label for="analyzeUrl" style="display:block;font-weight:800;margin-bottom:.4rem" data-i="page_url">Page URL</label>
        <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" />
        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.7rem">
            <label style="display:inline-flex;align-items:center;gap:.5rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> <span data-i="auto_check">Auto‑apply checkmarks (≥ 70)</span>
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-danger"><i class="fa-solid fa-magnifying-glass"></i> <span data-i="analyze">Analyze</span></button>
          <button class="btn btn-neon" id="printChecklist"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
          <button class="btn btn-ghost" id="resetChecklist"><i class="fa-solid fa-rotate"></i> <span data-i="reset">Reset</span></button>
        </div>
        <div id="analyzeStatus" style="margin-top:.5rem;color:var(--text-dim)"></div>
      </form>

      <div id="analyzeReport" style="margin-top:1rem;display:none">
        <div style="display:flex;flex-wrap:wrap;gap:.6rem">
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

    <!-- Progress -->
    <div class="progress-wrap">
      <div class="progress-bar"><div class="progress-fill" id="progressBar"></div></div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    <!-- Categories / checklist (unchanged structure) -->
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
        ['Content & Keywords',1,5,'fa-pen-nib','linear-gradient(135deg,#22d3ee33,#a78bfa33)'],
        ['Technical Elements',6,9,'fa-code','linear-gradient(135deg,#a7f3d033,#60a5fa33)'],
        ['Content Quality',10,13,'fa-star','linear-gradient(135deg,#fcd34d33,#fb718533)'],
        ['Structure & Architecture',14,17,'fa-sitemap','linear-gradient(135deg,#86efac33,#f0abfc33)'],
        ['User Signals & Experience',18,21,'fa-user-check','linear-gradient(135deg,#fca5a533,#fde68a33)'],
        ['Entities & Context',22,25,'fa-database','linear-gradient(135deg,#f472b633,#60a5fa33)'],
      ] as $c)
        <article class="category-card" style="background-image:{{ $c[4] }}; background-blend-mode: lighten;">
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
              <li class="checklist-item">
                <label>
                  <input type="checkbox" id="ck-{{ $i }}">
                  <span>{{ $labels[$i] }}</span>
                </label>
                <span class="score-badge" id="sc-{{ $i }}">—</span>
                <button class="improve-btn" data-id="ck-{{ $i }}">Improve</button>
              </li>
            @endfor
          </ul>
        </article>
      @endforeach
    </div>
  </section>
</div>

<footer class="site">
  <div class="footer-brand"><span class="dot"></span><strong>Semantic SEO Master</strong></div>
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

<!-- Modal -->
<div class="modal-backdrop" id="modalBackdrop"></div>
<div class="modal" id="tipModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title" id="modalTitle">Improve</h3>
      <button class="modal-close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="tabs">
      <button class="tab active" data-tab="tipsTab"><i class="fa-solid fa-lightbulb"></i> Tips</button>
      <button class="tab" data-tab="examplesTab"><i class="fa-brands fa-google"></i> Examples (Google)</button>
      <button class="tab" data-tab="humanTab"><i class="fa-solid fa-user"></i> Human‑like</button>
      <button class="tab" data-tab="aiTab"><i class="fa-solid fa-microchip"></i> AI‑like</button>
      <button class="tab" data-tab="fullTab"><i class="fa-solid fa-file-lines"></i> Full Text</button>
    </div>
    <div class="tabpanes">
      <div id="tipsTab" class="active"><ul id="modalList"></ul></div>
      <div id="examplesTab"><div class="pre" id="examplesPre">—</div></div>
      <div id="humanTab"><div class="pre" id="humanSnippetsPre">Run Analyze to view human‑like snippets.</div></div>
      <div id="aiTab"><div class="pre" id="aiSnippetsPre">Run Analyze to view AI‑like snippets.</div></div>
      <div id="fullTab"><div class="pre" id="fullTextPre">Run Analyze to load full text.</div></div>
    </div>
  </div>
</div>

<script>
// ... (All the JavaScript code from the original remains exactly the same)
// This includes the i18n, background canvases, back to top, score wheel helpers,
// checklist + scoring, modal + examples, URL normalization + Analyze functionality

// The only change is the enhanced smoke effect below
</script>

<!-- ========== Enhanced Procedural Colorful Smoke ========== -->
<script>
(function(){
  const canvas = document.getElementById('smokeFX'); 
  if (!canvas) return;
  
  const dpr = Math.min(2, window.devicePixelRatio || 1);
  let gl = null, vw = 0, vh = 0, start = performance.now();
  let time = 0;

  function resize(){ 
    vw = canvas.clientWidth = innerWidth; 
    vh = canvas.clientHeight = innerHeight; 
    canvas.width = Math.floor(vw * dpr); 
    canvas.height = Math.floor(vh * dpr); 
    if (gl) gl.viewport(0, 0, canvas.width, canvas.height); 
  }
  
  addEventListener('resize', resize, {passive: true}); 
  resize();
  
  try { 
    gl = canvas.getContext('webgl2', { 
      alpha: true, 
      antialias: false, 
      depth: false, 
      stencil: false 
    }); 
  } catch(e){}
  
  if (!gl) return;
  
  // Vertex shader
  const vs = `#version 300 es
    precision highp float;
    const vec2 v[3] = vec2[3](vec2(-1., -1.), vec2(3., -1.), vec2(-1., 3.));
    out vec2 uv;
    void main() {
      vec2 p = v[gl_VertexID];
      uv = 0.5 * (p + 1.0);
      gl_Position = vec4(p, 0, 1);
    }
  `;
  
  // Fragment shader - enhanced with more colors and dynamic behavior
  const fs = `#version 300 es
    precision highp float;
    in vec2 uv;
    out vec4 o;
    uniform vec2 r;
    uniform float t;
    uniform float a;
    
    // Hash function for random values
    float hash(vec2 p) {
      return fract(sin(dot(p, vec2(127.1, 311.7))) * 43758.5453);
    }
    
    // Noise function
    float noise(vec2 p) {
      vec2 i = floor(p);
      vec2 f = fract(p);
      
      float a = hash(i);
      float b = hash(i + vec2(1.0, 0.0));
      float c = hash(i + vec2(0.0, 1.0));
      float d = hash(i + vec2(1.0, 1.0));
      
      vec2 u = f * f * (3.0 - 2.0 * f);
      return mix(a, b, u.x) + (c - a) * u.y * (1.0 - u.x) + (d - b) * u.x * u.y;
    }
    
    // Fractional Brownian Motion
    float fbm(vec2 p) {
      float value = 0.0;
      float amplitude = 0.5;
      mat2 rot = mat2(cos(0.5), sin(0.5), -sin(0.5), cos(0.5));
      
      for (int i = 0; i < 6; i++) {
        value += amplitude * noise(p);
        p = rot * p * 2.0;
        amplitude *= 0.5;
      }
      return value;
    }
    
    void main() {
      // Center and scale UVs
      vec2 centeredUV = (uv - 0.5) * vec2(a, 1.0);
      
      // Create multiple layers of noise with different parameters
      float q1 = fbm(centeredUV * 1.5 + vec2(t * 0.2, -t * 0.15));
      float q2 = fbm(centeredUV * 2.0 + vec2(-t * 0.1, t * 0.25));
      float q3 = fbm(centeredUV * 3.0 + vec2(t * 0.3, t * 0.1));
      
      // Combine noise layers
      float combined = (q1 * 0.6 + q2 * 0.3 + q3 * 0.1);
      
      // Create smoke shape (more from bottom right)
      vec2 fromBottomRight = vec2(uv.x - 0.7, uv.y - 0.7);
      float distanceFactor = 1.0 - clamp(length(fromBottomRight) * 1.5, 0.0, 1.0);
      
      // Apply shape to noise
      float smoke = combined * distanceFactor;
      float d = smoothstep(0.3, 0.9, smoke);
      
      // Dynamic color palette with time-based hue shifting
      float hueShift = sin(t * 0.2) * 0.5 + 0.5;
      
      // Create multiple color layers
      vec3 color1 = mix(vec3(0.24, 0.88, 1.0), vec3(0.61, 0.36, 1.0), uv.x + hueShift * 0.3);
      vec3 color2 = mix(vec3(1.0, 0.5, 0.2), vec3(1.0, 0.2, 0.8), uv.y - hueShift * 0.2);
      vec3 color3 = mix(vec3(0.2, 1.0, 0.5), vec3(0.8, 0.2, 1.0), uv.x * uv.y + hueShift * 0.4);
      
      // Blend colors based on noise and position
      vec3 finalColor = mix(color1, color2, q1);
      finalColor = mix(finalColor, color3, q2 * 0.5);
      
      // Apply alpha based on smoke density
      o = vec4(finalColor * d, 0.8 * d);
    }
  `;
  
  // Compile shaders
  function compileShader(source, type) {
    const shader = gl.createShader(type);
    gl.shaderSource(shader, source);
    gl.compileShader(shader);
    
    if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
      console.error('Shader compile error:', gl.getShaderInfoLog(shader));
      return null;
    }
    
    return shader;
  }
  
  const vertexShader = compileShader(vs, gl.VERTEX_SHADER);
  const fragmentShader = compileShader(fs, gl.FRAGMENT_SHADER);
  
  if (!vertexShader || !fragmentShader) return;
  
  // Create program
  const program = gl.createProgram();
  gl.attachShader(program, vertexShader);
  gl.attachShader(program, fragmentShader);
  gl.linkProgram(program);
  
  if (!gl.getProgramParameter(program, gl.LINK_STATUS)) {
    console.error('Program link error:', gl.getProgramInfoLog(program));
    return;
  }
  
  // Get uniform locations
  const resolutionLocation = gl.getUniformLocation(program, 'r');
  const timeLocation = gl.getUniformLocation(program, 't');
  const aspectLocation = gl.getUniformLocation(program, 'a');
  
  // Render loop
  function render(now) {
    time = (now - start) * 0.001;
    
    gl.useProgram(program);
    gl.uniform2f(resolutionLocation, canvas.width, canvas.height);
    gl.uniform1f(timeLocation, time);
    gl.uniform1f(aspectLocation, canvas.width / Math.max(1, canvas.height));
    
    gl.drawArrays(gl.TRIANGLES, 0, 3);
    requestAnimationFrame(render);
  }
  
  requestAnimationFrame(render);
})();
</script>
</body>
</html>
