@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')
 <meta name="csrf-token" content="{{ csrf_token() }}">
@push('head')
<style>
  /* ===================== Neon + Multicolor Theme Palette ===================== */
  :root{
    --bg-1:#1A1A1A;
    --bg-2:#262626;
    --card:#1F1F1F;
    --card-2:#1C1C1C;
    --ink:#EAEAEA;
    --sub:#BFC7CF;
    

    /* Neon wheel palette */
    --blue-1:#00C6FF;
    --blue-2:#0072FF;
    --green-1:#00FF8A;
    --green-2:#00FFC6;
    --yellow-1:#FFD700;
    --orange-1:#FFA500;
    --red-1:#FF4500;
    --pink-1:#FF1493;
    --purple-1:#8A2BE2;
  }

  /* =============== Base page styles (colors only changed) =============== */
  html,body{
  background:#01051a !important;
  }
  .maxw{max-width:1150px;margin:0 auto;padding:8px;}

  .title-wrap{display:flex;align-items:center;gap:14px;justify-content:center;margin-top:14px}
  .king{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:#1E1E1E;border:1px solid #FFFFFF1F;box-shadow:0 0 24px #000 inset}
  .t-grad{
    background:linear-gradient(90deg,
      var(--blue-1),var(--blue-2),
      var(--green-1),var(--green-2),
      var(--yellow-1),var(--orange-1),
      var(--red-1),var(--pink-1),var(--purple-1),var(--blue-1));
    -webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900;
    background-size: 400% 100%;
    animation: rainbowSlide 8s linear infinite;
  }
  .byline{font-size:14px;color:var(--sub)}
  .shoail{display:inline-block;background:linear-gradient(90deg,var(--blue-1),var(--pink-1),var(--purple-1),var(--green-2),var(--orange-1));-webkit-background-clip:text;background-clip:text;color:transparent;background-size:400% 100%;animation:rainbowSlide 6s linear infinite,bob 3s ease-in-out infinite}
  @keyframes rainbowSlide{to{background-position:400% 50%}} @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  .legend{display:flex;gap:10px;justify-content:center;margin:10px 0 6px}
  .legend .badge{padding:6px 10px;border-radius:9999px;font-weight:800;border:1px solid #ffffff2a;font-size:12px}
  .legend .g{background:#0f2d1f;color:#baf7d9;border-color:#10b98166}
  .legend .o{background:#2f2508;color:#fde68a;border-color:#f59e0b66}
  .legend .r{background:#331111;color:#fecaca;border-color:#ef444466}

  .card{border-radius:18px;padding:18px;background:var(--card);border:1px solid #000000;box-shadow:0 10px 30px rgba(0,0,0,.35)}
  .cat-card{border-radius:16px;padding:16px;background:var(--card-2);border:1px solid var(--outline)}
  .ground-slab{border-radius:22px;padding:20px;background:#1B1B1B;border:1px solid var(--outline);margin-top:20px;box-shadow:0 10px 40px rgba(0,0,0,.4)}

  .pill{padding:5px 10px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid #ffffff29;background:#ffffff14;color:var(--ink)}
  .chip{padding:6px 8px;border-radius:12px;font-weight:800;display:inline-flex;align-items:center;gap:6px;border:1px solid #ffffff24;color:#eef2ff;font-size:12px;background:#171717}
  .chip i{font-style:normal}
  .chip.good{background:linear-gradient(135deg,#0f2d1f,#0d3b2a);border-color:#22c55e72;box-shadow:0 0 24px rgba(34,197,94,.25)}
  .chip.warn{background:linear-gradient(135deg,#2a1f06,#3f2c07);border-color:#f59e0b72;box-shadow:0 0 24px rgba(245,158,11,.2)}
  .chip.bad{background:linear-gradient(135deg,#2e1010,#4a1616);border-color:#ef444472;box-shadow:0 0 24px rgba(239,68,68,.2)}

  .btn{padding:10px 14px;border-radius:12px;font-weight:900;border:1px solid #ffffff22;color:#0b1020;font-size:13px}
  .btn-green{background:linear-gradient(90deg,var(--green-1),var(--green-2))}
  .btn-blue{background:linear-gradient(90deg,var(--blue-1),var(--blue-2))}
  .btn-orange{background:linear-gradient(90deg,var(--yellow-1),var(--orange-1));color:#2b1600}
  .btn-purple{background:linear-gradient(90deg,var(--pink-1),var(--purple-1));color:#19041a}
  .url-row{display:flex;align-items:center;gap:10px;border:1px solid var(--outline);background:#181818;border-radius:12px;padding:8px 10px}
  .url-row input{background:transparent;border:none;outline:none;color:var(--ink);width:100%}
  .url-row .paste{padding:6px 10px;border-radius:10px;border:1px solid #ffffff26;background:#232323;color:var(--ink)}

  .analyze-wrap{border-radius:16px;background:#161616;border:1px solid var(--outline);padding:12px;box-shadow:0 0 0 1px #000 inset}
  
  /* ======================================= */
  /* === 🎨 NEW NEON-TECH SCORE WHEELS 🎨 === */
  /* ======================================= */
  .mw {
    --v: 0;
    --size: 200px;
    --track-width: 14px;
    --progress-percent: calc(var(--v) * 1%);

    width: var(--size);
    height: var(--size);
    position: relative;
    transition: filter .4s ease;
  }
  
  .mw-ring {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: radial-gradient(circle at center, rgba(10,12,30,0.8), rgba(0,0,0,0.9));
    box-shadow:
      inset 0 0 4px 1px rgba(0,0,0,0.8),
      0 0 0 1px rgba(255,255,255,0.05);
  }

  .mw-ring::before {
    content: "";
    position: absolute;
    inset: 0px;
    border-radius: 50%;
    background: conic-gradient(from -90deg,
        var(--blue-1) 0%, var(--green-1) 25%,
        var(--yellow-1) 50%, var(--red-1) 75%,
        var(--purple-1) 90%, var(--blue-1) 100%);
    -webkit-mask-image: 
        conic-gradient(from -90deg, #000 var(--progress-percent), transparent calc(var(--progress-percent) + 0.1%)),
        radial-gradient(farthest-side, transparent calc(100% - var(--track-width)), #000 calc(100% - var(--track-width)));
    -webkit-mask-composite: source-in;
     mask-image: 
        conic-gradient(from -90deg, #000 var(--progress-percent), transparent calc(var(--progress-percent) + 0.1%)),
        radial-gradient(farthest-side, transparent calc(100% - var(--track-width)), #000 calc(100% - var(--track-width)));
     mask-composite: intersect;
     animation: rainbowSlide 4s linear infinite;
     background-size: 200% 100%;
  }
  
  .mw-ring::after {
    content: '';
    position: absolute;
    inset: calc(var(--track-width) - 4px);
    border-radius: 50%;
    background: radial-gradient(circle at center, rgba(255,255,255,0.02), transparent 70%);
    box-shadow: inset 0 2px 8px rgba(0,0,0,0.5);
  }

  .mw-center {
    position: absolute;
    inset: 0;
    display: grid;
    place-items: center;
    font-size: calc(var(--size) * 0.24);
    font-weight: 900;
    color: #fff;
    text-shadow: 0 0 12px rgba(255,255,255,0.3);
  }
  
  .mw.good { filter: drop-shadow(0 0 10px var(--green-1)) drop-shadow(0 0 20px var(--green-1)); }
  .mw.warn { filter: drop-shadow(0 0 10px var(--orange-1)) drop-shadow(0 0 20px var(--orange-1)); }
  .mw.bad { filter: drop-shadow(0 0 10px var(--red-1)) drop-shadow(0 0 20px var(--red-1)); }

  .mw-sm {
    --size: 170px;
    --track-width: 12px;
  }
  
  .waterbox{position:relative;height:16px;border-radius:9999px;overflow:hidden;border:1px solid var(--outline);background:#151515}
  .waterbox .fill{
    position:absolute;
    inset:0;
    width:0%;
    transition:width .9s ease;
    background: linear-gradient(90deg, var(--red-1), var(--orange-1), var(--yellow-1), var(--green-1), var(--blue-1), var(--purple-1));
    background-size: 300% 100%;
    animation: rainbowSlide 5s linear infinite;
  }
  .waterbox .label{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:var(--ink);font-size:11px}

  .progress{width:100%;height:10px;border-radius:9999px;background:#222;overflow:hidden;border:1px solid var(--outline)}
  .progress>span{display:block;height:100%;border-radius:9999px;background:linear-gradient(90deg,var(--red-1),var(--yellow-1),var(--green-1));transition:width .5s ease}

  .check{display:flex;align-items:center;justify-content:space-between;border-radius:12px;padding:10px 12px;border:1px solid var(--outline);background:#191919}
  .score-pill{padding:3px 7px;border-radius:10px;font-weight:800;background:#222;border:1px solid #ffffff22;color:var(--ink);font-size:12px}
  .score-pill--green{background:linear-gradient(135deg,#113d2a,#0f3325);border-color:#10b98166;color:#bbf7d0}
  .score-pill--orange{background:linear-gradient(135deg,#3d2e11,#33270f);border-color:#f59e0b66;color:#fde68a}
  .score-pill--red{background:linear-gradient(135deg,#3d111f,#331016);border-color:#ef444466;color:#fecaca}

  .improve-btn{padding:6px 9px;border-radius:10px;color:#0b1020;font-weight:800;border:1px solid transparent;transition:transform .08s ease;font-size:12px}
  .improve-btn:active{transform:translateY(1px)}
  .fill-green {background:linear-gradient(135deg,var(--green-1),var(--green-2));color:#05240f}
  .fill-orange{background:linear-gradient(135deg,var(--yellow-1),var(--orange-1));color:#3a2400}
  .fill-red   {background:linear-gradient(135deg,var(--red-1),var(--pink-1));color:#2f0606}
  .outline-green{border-color:#22c55edd!important;box-shadow:0 0 0 2px #22c55e8c inset,0 0 16px #22c55e55}
  .outline-orange{border-color:#f59e0bdd!important;box-shadow:0 0 0 2px #f59e0b8c inset,0 0 16px #f59e0b55}
  .outline-red{border-color:#ef4444dd!important;box-shadow:0 0 0 2px #ef44448c inset,0 0 16px #ef444455}

  dialog[open]{display:block} dialog::backdrop{background:rgba(0,0,0,.6)}
  #improveModal .card{background:#1B1B1B;border:1px solid var(--outline)}
  #improveModal .card .card{background:#1A1A1A;border-color:var(--outline)}

  #errorBox{display:none;margin-top:10px;border:1px solid #ef444466;background:#331111;color:#fecaca;border-radius:12px;padding:10px;white-space:pre-wrap;font-size:12px}

  /* ============================================= */
  /* === 🎨 NEW STYLES for Site Speed Section 🎨 === */
  /* ============================================= */
    .speed-card-new {
        background: #0D1120;
        border: 1px solid #2A3659;
        border-radius: 16px;
        padding: 16px;
        margin-top: 24px;
    }
    .speed-header { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .speed-title { display: flex; align-items: center; gap: 10px; font-weight: 800; color: var(--ink); }
    .speed-badge { font-size: 11px; padding: 4px 8px; border-radius: 999px; font-weight: 700; }
    .speed-badge.good { background: #10B9811A; color: #6EE7B7; border: 1px solid #10B981; }
    .speed-badge.warn { background: #F59E0B1A; color: #FBBF24; border: 1px solid #F59E0B; }
    .speed-badge.bad { background: #EF44441A; color: #F87171; border: 1px solid #EF4444; }
    .speed-overview-bar { height: 6px; background: #1F2937; border-radius: 999px; margin-top: 8px; }
    .speed-overview-bar > div { height: 100%; width: 0%; border-radius: 999px; transition: width 0.8s ease; background: linear-gradient(90deg, var(--red-1), var(--yellow-1), var(--green-1)); }
    .speed-overview-text { font-size: 12px; color: var(--sub); margin-top: 4px; }

    .speed-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px; }
    @media (max-width: 600px) { .speed-grid { grid-template-columns: 1fr; } }
    .speed-device-card { background: #111827; border-radius: 12px; padding: 12px; border: 1px solid #1F2937; }
    .speed-device-header { display: flex; align-items: center; gap: 8px; font-weight: 700; }
    .speed-device-score {
        width: 60px; height: 60px;
        position: relative;
    }
    .speed-device-score-val { position: absolute; inset: 0; display: grid; place-items: center; font-size: 18px; font-weight: 800; }
    .speed-device-score svg { width: 100%; height: 100%; transform: rotate(-90deg); }
    .speed-device-score circle { fill: none; stroke-width: 6; }
    .speed-device-score .track { stroke: #374151; }
    .speed-device-score .progress { stroke-linecap: round; transition: stroke-dashoffset 0.8s ease; }
    .speed-device-metrics { display: grid; gap: 8px; font-size: 12px; flex-grow:1; }
    .speed-device-metric { display: flex; justify-content: space-between; align-items: center; }
    .speed-opportunities { background: #111827; border: 1px solid #F59E0B; border-radius: 12px; padding: 12px; margin-top: 16px; }
    .speed-opportunities-title { display: flex; align-items: center; gap: 8px; color: #FBBF24; font-weight: 700; margin-bottom: 8px; }
    .speed-opportunities ul { list-style: none; margin: 0; padding: 0; display: grid; gap: 6px; font-size: 12px; color: var(--sub); }
  
  /* ===================== Content Optimization (Futuristic) ===================== */
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.7; transform: scale(1.1); } }
  .animated-icon {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      animation: spin 8s linear infinite;
  }
  .animated-icon.pulse {
      animation: pulse 3s ease-in-out infinite, spin 12s linear infinite reverse;
  }

  .co-card {
    --co-bg: #191919;
    --co-border: var(--outline);

    border-radius: 20px;
    background: var(--co-bg);
    border: 1px solid var(--co-border);
    padding: 16px;
    margin-top: 12px;
    background-image:
      radial-gradient(circle at 10% 10%, rgba(138,43,226,.12), transparent 40%),
      radial-gradient(circle at 90% 80%, rgba(0,198,255,.12), transparent 50%);
  }
  
  .co-grid, .tsi-grid, .cae-grid {display:grid;grid-template-columns: 240px 1fr;gap: 16px;align-items: flex-start;}
  @media (max-width: 920px){.co-grid, .tsi-grid, .cae-grid {grid-template-columns:1fr}}

  /* Info Items Grid */
  .co-info-grid, .tsi-info-grid, .cae-info-grid {display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
  @media (max-width:500px){.co-info-grid, .tsi-info-grid, .cae-info-grid {grid-template-columns:1fr}}
  .co-info-item, .tsi-info-item, .cae-info-item {
    border-radius:14px;
    padding:14px;
    background:#1E1E1E;
    border:1px solid var(--outline);
    box-shadow:0 8px 24px rgba(0,0,0,.3);
  }
  .co-info-icon, .tsi-info-icon, .cae-info-icon {width:32px;height:32px;display:grid;place-items:center;border-radius:8px;background:linear-gradient(135deg,#23234a,#182e3a);border:1px solid #2e2e2e}
  .co-info-icon svg, .tsi-info-icon svg, .cae-info-icon svg {width:18px;height:18px}
  .co-info-header, .tsi-info-header, .cae-info-header {display:flex;align-items:center;gap:10px;margin-bottom:8px}
  .co-info-title, .tsi-info-title, .cae-info-title {font-weight:800;color:var(--ink)}
  .co-info-item p, .tsi-info-item p, .cae-info-item p {font-size:12px;color:#aab3c2;margin:0 0 10px}
  .co-tags, .tsi-tags, .cae-tags {display:flex;flex-wrap:wrap;gap:6px}
  
  .tsi-info-item ul { list-style: none; padding-left: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
  .tsi-info-item li { font-size: 13px; line-height: 1.5; }
  .tsi-info-item code { background: #111; padding: 2px 6px; border-radius: 6px; font-size: 12px; color: var(--pink-1); }

  .site-map-container { background: #111; border-radius: 12px; padding: 12px; font-size: 12px; line-height: 1.6; max-height: 200px; overflow-y: auto;}
  .site-map-container ul { padding-left: 16px; margin: 0; }
  .site-map-container li { list-style-type: '— '; }

  /* === Badges & Tips for Content Optimization === */
  .co-badge{display:inline-flex;align-items:center;gap:8px;border-radius:999px;padding:6px 10px;font-weight:700;font-size:12px;letter-spacing:.2px;border:1px solid var(--outline);background:#1a1a1a;color:#dbe7ff}
  .co-badge.small{padding:4px 8px;font-size:11px}
  .co-badge.good{background:rgba(0,255,138,.12);border-color:rgba(0,255,138,.35);color:#86efac}
  .co-badge.warn{background:rgba(255,215,0,.12);border-color:rgba(255,165,0,.35);color:#facc15}
  .co-badge.bad{background:rgba(255,20,147,.12);border-color:rgba(138,43,226,.35);color:#fda4af}
  .co-tips{display:flex;flex-direction:column;gap:8px;margin-top:8px}
  .co-tips .tip{border-left:3px solid #2a3b66;padding-left:10px;color:#cdd6ef;font-size:12px}


  /* ========================================================== */
  /* === 🎨 NEW STYLES for Technical SEO Integration 🎨 === */
  /* ========================================================== */
  .tsi-card {
    background: linear-gradient(145deg, #0d0f2b, #1f0c2e);
    border: 1px solid var(--purple-1);
    border-radius: 20px;
    padding: 16px;
    margin-top: 24px;
    box-shadow: 0 0 32px rgba(138, 43, 226, 0.4), inset 0 0 12px rgba(0, 0, 0, 0.5);
    background-image: 
      radial-gradient(circle at 100% 0%, rgba(0,198,255,.15), transparent 30%),
      radial-gradient(circle at 0% 100%, rgba(138,43,226,.15), transparent 30%);
  }
  .tsi-info-item {
    background: rgba(8, 5, 20, 0.7);
    border: 1px solid rgba(138, 43, 226, 0.3);
    backdrop-filter: blur(4px);
    transition: transform 0.2s ease, border-color 0.2s ease;
  }
  .tsi-info-item:hover {
    transform: translateY(-3px);
    border-color: rgba(0, 198, 255, 0.5);
  }
  .tsi-info-title {
    background: linear-gradient(90deg, var(--blue-1), var(--pink-1));
    -webkit-background-clip: text; background-clip: text; color: transparent;
    font-size: 15px;
  }
  .tsi-suggestions {
    border: 1px solid transparent;
    border-radius: 14px;
    padding:14px; margin-top:16px;
    background-image: 
        linear-gradient(to right, #1a0f2b, #1a0f2b), 
        linear-gradient(90deg, var(--pink-1), var(--purple-1));
    box-shadow: 0 0 24px rgba(255, 20, 147, 0.3);
  }
  .tsi-suggestions h4 {
    margin:0 0 10px; font-weight:900;
    display:flex; align-items:center; gap:8px;
    background: linear-gradient(90deg, var(--pink-1), var(--yellow-1));
    -webkit-background-clip: text; background-clip: text; color: transparent;
  }
  .tsi-suggestions ul { margin:0; padding-left:0; list-style:none; display:grid; gap:8px;}
  .tsi-suggestions li { color: #e2d8ff; font-size:13px; padding-left:10px; position:relative; }
  .tsi-suggestions li::before { content: '💡'; position: absolute; left: -10px; top:0; }
  
  
  /* =============================================== */
  /* === 🎨 NEW STYLES for Keyword Intelligence 🎨 === */
  /* =============================================== */
  .ki-card {
    background: linear-gradient(145deg, #2b210c, #2e0c1f);
    border: 1px solid var(--orange-1);
    border-radius: 20px;
    padding: 16px;
    margin-top: 24px;
    box-shadow: 0 0 32px rgba(255, 165, 0, 0.3), inset 0 0 12px rgba(0, 0, 0, 0.5);
  }
  .ki-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 16px;
  }
  .ki-item {
    background: rgba(25, 15, 5, 0.7);
    border: 1px solid rgba(255, 165, 0, 0.3);
    border-radius: 14px;
    padding: 14px;
    backdrop-filter: blur(4px);
  }
  .ki-item-title {
    font-weight: 800; font-size: 15px;
    background: linear-gradient(90deg, var(--yellow-1), var(--pink-1));
    -webkit-background-clip: text; background-clip: text; color: transparent;
    margin-bottom: 12px; display:flex; align-items:center; gap: 8px;
  }
  .ki-tags { display: flex; flex-wrap: wrap; gap: 8px; }
  .ki-tags .chip {
    background: #2f2508; border-color: #f59e0b72; color: #fde68a;
  }
  .ki-tags .chip.intent-info { background: #112d3d; border-color: #00c6ff72; color: #cffcff;}
  .ki-tags .chip.intent-trans { background: #2f1d08; border-color: #ff8c0072; color: #ffefd5;}
  .ki-tags .chip.intent-nav { background: #231a33; border-color: #8a2be272; color: #e9d5ff;}
  .ki-list { display: flex; flex-direction: column; gap: 6px; font-size: 13px; color: #ffedd5;}

  /* ======================================================= */
  /* === 🚀 NEW STYLES for Content Analysis Engine 🚀 === */
  /* ======================================================= */
  .cae-card {
    background: linear-gradient(145deg, #0d0f2b, #1f0c2e);
    border: 1px solid var(--blue-1);
    border-radius: 20px;
    padding: 16px;
    margin-top: 24px;
    box-shadow: 0 0 32px rgba(0, 198, 255, 0.4), inset 0 0 12px rgba(0, 0, 0, 0.5);
  }
  .cae-info-item {
    background: rgba(5, 15, 25, 0.7);
    border: 1px solid rgba(0, 198, 255, 0.3);
    backdrop-filter: blur(4px);
  }
  .cae-info-title {
    background: linear-gradient(90deg, var(--blue-1), var(--green-2));
    -webkit-background-clip: text; background-clip: text; color: transparent;
  }
  .cae-relevance-bar {
      height: 12px;
      border-radius: 999px;
      background: rgba(0,0,0,0.3);
      border: 1px solid rgba(255,255,255,0.1);
  }
  .cae-relevance-bar > span {
      display:block; height:100%; width:0%;
      border-radius: 999px;
      background: linear-gradient(90deg, var(--blue-1), var(--green-1));
      box-shadow: 0 0 8px var(--green-1);
      transition: width 0.9s ease;
  }
  
  /* =================================================== */
  /* === 🎨 NEW STYLES for Meta & Heading Section 🎨 === */
  /* =================================================== */
  .meta-card {
      background: #10122e;
      border: 1px solid #2a2f5a;
  }
  .meta-card .cat-card {
      background: #14173a;
      border-color: #2a2f5a;
  }
  .meta-card .t-grad {
      font-size: 20px;
  }
  .meta-card .cat-card-title {
      background: linear-gradient(90deg, var(--green-1), var(--blue-1));
      -webkit-background-clip: text; background-clip: text; color: transparent;
      font-weight: 800;
  }


</style>

<script defer>
(function(){
  // ✅ REWRITTEN FOR ULTIMATE ROBUSTNESS
  const $ = s => document.querySelector(s);
  
  // --- Global State & Element Refs ---
  const el = {
    errorBox: $('#errorBox'),
    analyzeBtn: $('#analyzeBtn'),
    urlInput: $('#urlInput'),
    // Main Score
    mw: $('#mw'), mwRing: $('#mwRing'), mwNum: $('#mwNum'),
    chipOverall: $('#chipOverall'),
    // Content Optimization Score
    mwContent: $('#mwContent'), ringContent: $('#ringContent'), numContent: $('#numContent'),
    coTopicCoverageText: $('#coTopicCoverageText'),
    coTopicCoverageProgress: $('#coTopicCoverageProgress'),
    coContentGapsTags: $('#coContentGapsTags'),
    coSchemaTags: $('#coSchemaTags'),
    coIntentTag: $('#coIntentTag'),
    coGradeTag: $('#coGradeTag'),
    // ... (add other element refs as needed)
  };

  // --- Helper Functions ---
  const clamp01 = n => Math.max(0, Math.min(100, Number(n) || 0));
  const bandName = s => s >= 80 ? 'good' : (s >= 60 ? 'warn' : 'bad');
  const setWheel = (ring, num, container, score) => {
    if (!ring || !num || !container) return;
    score = clamp01(score);
    container.classList.remove('good', 'warn', 'bad');
    container.classList.add(bandName(score));
    ring.style.setProperty('--v', score);
    num.textContent = score + '%';
  };
  const setChip = (chipEl, label, value, score) => {
    if (!chipEl) return;
    chipEl.classList.remove('good', 'warn', 'bad');
    chipEl.classList.add(bandName(score));
    chipEl.innerHTML = `<i>${score >= 80 ? '✅' : (score >= 60 ? '🟧' : '🔴')}</i><span>${label}: ${value}</span>`;
  };
  const showError = (msg, detail) => {
      let finalMessage = msg;
      if (detail && detail !== msg) finalMessage += `

${detail}`;
      el.errorBox.style.display = 'block';
      el.errorBox.textContent = finalMessage;
  };
  const clearError = () => { el.errorBox.style.display = 'none'; el.errorBox.textContent = ''; };
  const setRunning = (isOn) => {
      el.analyzeBtn.disabled = isOn;
      el.analyzeBtn.style.opacity = isOn ? 0.6 : 1;
      el.analyzeBtn.textContent = isOn ? 'Analyzing…' : '🔍 Analyze';
  };

  // --- API Call Function ---
  async function callApi(endpoint, url) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    try {
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: {'Accept':'application/json', 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken},
            body: JSON.stringify({ url, _token: csrfToken })
        });
        if (!res.ok) {
            const errorBody = await res.json().catch(() => ({ error: `The server returned an HTTP ${res.status} error.` }));
            const detail = errorBody.error || errorBody.detail || errorBody.message || JSON.stringify(errorBody);
            throw new Error(detail);
        }
        return res.json();
    } catch (err) {
        throw new Error(`A network or parsing error occurred: ${err.message}`);
    }
  }

  // --- UI Population Functions ---
  function populateContentOptimizationUI(coResult) {
      if (coResult.status === 'fulfilled' && coResult.value && coResult.value.content_optimization && typeof coResult.value.content_optimization.nlp_score === 'number') {
          const co = coResult.value.content_optimization;
          const score = clamp01(co.nlp_score);
          
          setWheel(el.mwRing, el.mwNum, el.mw, score);
          setChip(el.chipOverall, 'Overall', `${score} / 100`, score);
          setWheel(el.ringContent, el.numContent, el.mwContent, score);
          
          // ... (rest of population logic) ...
      } else {
          // Handle failure case
          setWheel(el.mwRing, el.mwNum, el.mw, 0);
          el.mwNum.textContent = 'N/A';
          setChip(el.chipOverall, 'Overall', 'N/A', 0);
          setWheel(el.ringContent, el.numContent, el.mwContent, 0);
          el.numContent.textContent = 'N/A';
          if(coResult.status === 'rejected') {
              showError('Content Optimization Failed.', coResult.reason.message);
          } else {
              showError('Content Optimization Failed.', 'The AI returned an incomplete or invalid data structure for the score.');
          }
      }
  }
  
  // ... (you would have similar populate functions for TSI, Keywords, etc.)

  // --- Main Event Listener ---
  el.analyzeBtn?.addEventListener('click', async e => {
      e.preventDefault();
      clearError();
      const url = el.urlInput.value.trim();
      if (!url) { showError('Please enter a URL.'); return; }
      
      try {
          setRunning(true);
          
          // ✅ THE FIX: Using Promise.allSettled to handle individual failures gracefully.
          const results = await Promise.allSettled([
              callApi('{{ route("semantic.analyze") }}', url),
              callApi('{{ route("api.content-optimization") }}', url),
              callApi('{{ route("api.technical-seo") }}', url),
              callApi('{{ route("api.keyword-analyze") }}', url),
              callApi('{{ route("api.content-engine") }}', url),
              callApi('{{ route("semantic.psi") }}', url)
          ]);

          const [localDataResult, coDataResult, tsiDataResult, kiDataResult, caeDataResult, psiDataResult] = results;

          if (localDataResult.status === 'rejected') {
              throw new Error(localDataResult.reason.message);
          }
          if (!localDataResult.value.ok) {
               throw new Error(localDataResult.value.error || 'Local data parsing failed');
          }
          
          const localData = localDataResult.value;
          // ... (populate local data UI)

          // Populate each section individually, handling its specific result
          populateContentOptimizationUI(coDataResult);
          // populateTechnicalSeoUI(tsiDataResult); 
          // ... and so on for other sections

      } catch (err) {
          console.error(err);
          showError('Analysis Failed.', String(err.message || 'An unknown error occurred.'));
      } finally {
          setRunning(false);
      }
  });

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

  <div style="display:grid;grid-template-columns:230px 1fr;gap:16px;align-items:center;margin-top:10px">
    <div style="display:grid;place-items:center;border-radius:16px;padding:8px;">
      <!-- Overall Score Wheel - Updated -->
      <div class="mw warn" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-center" id="mwNum">0%</div>
      </div>
    </div>
    <div class="space-y-2">
      <div style="display:flex;flex-wrap:wrap;gap:6px">
        <span id="chipOverall" class="chip warn"><i>🟧</i><span>Overall: 0 /100</span></span>
        <span id="chipContent" class="chip warn"><i>🟧</i><span>Content: —</span></span>
        <span id="chipWriter"  class="chip"><i>🟧</i><span>Writer: —</span></span>
        <span id="chipHuman"   class="chip"><i>🟧</i><span>Human-like: — %</span></span>
        <span id="chipAI"      class="chip"><i>🟧</i><span>AI-like: — %</span></span>
      </div>
      <div id="overallBar" class="waterbox warn">
        <div class="fill" id="overallFill" style="width:0%"></div>
        <div class="label"><span id="overallPct">0%</span></div>
      </div>
    </div>
  </div>

  <div class="analyze-wrap" style="margin-top:12px;">
    <div class="url-row">
      <span style="opacity:.75">🌐</span>
      <input id="urlInput" name="url" type="url" placeholder="https://example.com/page" />
      <button id="pasteBtn" type="button" class="paste">Paste</button>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-top:10px">
      <label style="display:flex;align-items:center;gap:8px;font-size:12px">
        <input id="autoCheck" type="checkbox" class="accent-emerald-400" checked/> Auto-apply checkmarks (≥ 80)
      </label>
      <div style="flex:1"></div>
      <input id="importFile" type="file" accept="application/json" style="display:none"/>
      <button id="importBtn" type="button" class="btn btn-purple">⇪ Import</button>
      <button id="analyzeBtn" type="button" class="btn btn-green">🔍 Analyze</button>
      <button id="printBtn"   type="button" class="btn btn-blue">🖨️ Print</button>
      <button id="resetBtn"   type="button" class="btn btn-orange">↻ Reset</button>
      <button id="exportBtn"  type="button" class="btn btn-purple">⬇︎ Export</button>
    </div>
    <div id="errorBox"></div>

    <div id="statusChips" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px">
      <div class="chip" id="chipHttpWrap"><span class="t-grad">HTTP:</span>&nbsp;<span id="chipHttp">—</span></div>
      <div class="chip" id="chipTitleWrap"><span class="t-grad">Title:</span>&nbsp;<span id="chipTitle">—</span></div>
      <div class="chip" id="chipMetaWrap"><span class="t-grad">Meta desc:</span>&nbsp;<span id="chipMeta">—</span></div>
      <div class="chip"><span class="t-grad">Canonical:</span>&nbsp;<span id="chipCanon">—</span></div>
      <div class="chip"><span class="t-grad">Robots:</span>&nbsp;<span id="chipRobots">—</span></div>
      <div class="chip"><span class="t-grad">Viewport:</span>&nbsp;<span id="chipViewport">—</span></div>
      <div class="chip"><span class="t-grad">H1/H2/H3:</span>&nbsp;<span id="chipH">—</span></div>
      <div class="chip"><span class="t-grad">Internal links:</span>&nbsp;<span id="chipInt">—</span></div>
      <div class="chip"><span class="t-grad">Schema:</span>&nbsp;<span id="chipSchema">—</span></div>
      <div class="chip"><span class="t-grad">Auto-checked:</span>&nbsp;<span id="chipAuto">0</span></div>
    </div>
  </div>

  <!-- Content Optimization -->
  <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-top:24px;">
    <span class="animated-icon" style="color:var(--green-1);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Z"></path><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"></path></svg>
    </span>
    <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Content Optimization</h3>
    <span class="animated-icon pulse" style="color:var(--pink-1);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
    </span>
  </div>

<div class="co-card" id="contentOptimizationCard">
    <div class="co-grid">
      <div style="display:grid;place-items:center;padding:10px">
        <div class="mw warn" id="mwContent">
          <div class="mw-ring" id="ringContent" style="--v:0"></div>
          <div class="mw-center" id="numContent">0%</div>
        </div>
      </div>

      <div class="co-info-grid">
        <div class="co-info-item">
          <div class="co-info-header">
            <div class="co-info-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>
            <span class="co-info-title">Topic Coverage</span>
          </div>
          <p id="coTopicCoverageText">Run analysis to get data.</p>
          <div class="progress"><span id="coTopicCoverageProgress" style="width:0%;"></span></div>
        </div>

        <div class="co-info-item">
          <div class="co-info-header">
            <div class="co-info-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/></svg></div>
            <span class="co-info-title">Content Gaps</span>
          </div>
          <div class="co-tags" id="coContentGapsTags"></div>
        </div>
    
        <div class="co-info-item">
          <div class="co-info-header">
            <div class="co-info-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M9.4 16.6L4.8 12l4.6-4.6L8 6l-6 6 6 6 1.4-1.4zm5.2 0l4.6-4.6-4.6-4.6L16 6l6 6-6 6-1.4-1.4z"/></svg></div>
            <span class="co-info-title">Schema Suggestions</span>
          </div>
         <div class="co-tags" id="coSchemaTags"></div>
      </div>
    
      <div class="co-info-item">
        <div class="co-info-header">
          <div class="co-info-icon"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M3.5 18.49l6-6.01 4 4L22 6.92l-1.41-1.41-7.09 7.97-4-4L2 16.99z"/></svg></div>
          <span class="co-info-title">Readability & Intent</span>
        </div>
         <div class="co-tags">
          <span id="coIntentTag" class="chip">Intent: —</span>
          <span id="coGradeTag" class="chip">Grade Level: —</span>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- NEW: Content Analysis Engine -->
<div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-top:24px;">
    <span class="animated-icon" style="color:var(--blue-1);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20.94c1.5 0 2.75 1.25 2.75 2.75S13.5 26.44 12 26.44 9.25 25.19 9.25 23.69s1.25-2.75 2.75-2.75z"></path><path d="M12 2.56c1.5 0 2.75 1.25 2.75 2.75S13.5 8.06 12 8.06 9.25 6.81 9.25 5.31s1.25-2.75 2.75-2.75z"></path></svg>
    </span>
    <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Content Analysis Engine</h3>
    <span class="animated-icon pulse" style="color:var(--purple-1);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.21 17.5c1.1 0 2 0.9 2 2s-0.9 2-2 2-2-0.9-2-2 0.9-2 2-2z"></path><path d="M19.79 6.5c1.1 0 2 0.9 2 2s-0.9 2-2 2-2-0.9-2-2 0.9-2 2-2z"></path></svg>
    </span>
</div>
<div class="cae-card" id="contentAnalysisEngineCard">
    <div class="cae-grid">
      <div style="display:grid;place-items:center;padding:10px">
        <div class="mw" id="mwCAE">
          <div class="mw-ring" id="ringCAE" style="--v:0"></div>
          <div class="mw-center" id="numCAE">0%</div>
        </div>
      </div>
      <div class="cae-info-grid">
        <div class="cae-info-item">
          <div class="cae-info-header"><div class="cae-info-icon animated-icon" style="color:var(--blue-1);">🧩</div><span class="cae-info-title">Topic Clustering Analysis</span></div>
          <div class="cae-tags" id="caeTopicClusters"></div>
        </div>
        <div class="cae-info-item">
          <div class="cae-info-header"><div class="cae-info-icon animated-icon pulse" style="color:var(--green-1);">🏢</div><span class="cae-info-title">Entity Recognition</span></div>
          <div class="cae-tags" id="caeEntities"></div>
        </div>
        <div class="cae-info-item">
          <div class="cae-info-header"><div class="cae-info-icon animated-icon" style="color:var(--yellow-1);">🔍</div><span class="cae-info-title">Semantic Keyword Discovery</span></div>
          <div class="cae-tags" id="caeKeywords"></div>
        </div>
        <div class="cae-info-item">
          <div class="cae-info-header"><div class="cae-info-icon animated-icon pulse" style="color:var(--pink-1);">🎯</div><span class="cae-info-title">Content Relevance & Intent</span></div>
          <div class="sp-row"><div>Relevance Score</div><div class="sp-val" id="caeRelevanceScore">—</div></div>
          <div class="cae-relevance-bar"><span id="caeRelevanceBar" style="width:0%"></span></div>
          <div id="caeIntent" style="margin-top:8px"></div>
        </div>
      </div>
    </div>
</div>

<!-- Technical SEO Integration -->
<div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-top:24px;">
    <span class="animated-icon pulse" style="color:var(--purple-1);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m18 16 4-4-4-4"></path><path d="m6 8-4 4 4 4"></path><path d="m14.5 4-5 16"></path></svg>
    </span>
    <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Technical SEO Integration</h3>
</div>
<div class="tsi-card" id="technicalSeoCard">
    <div class="tsi-grid">
      <div style="display:grid;place-items:center;padding:10px">
        <div class="mw" id="mwTSI">
          <div class="mw-ring" id="ringTSI" style="--v:0"></div>
          <div class="mw-center" id="numTSI">0%</div>
        </div>
      </div>
      <div class="tsi-info-grid">
        <div class="tsi-info-item">
            <div class="tsi-info-header"><span class="animated-icon" style="color:var(--blue-1);">🔗</span><span class="tsi-info-title">Internal Linking Optimization</span></div>
            <ul id="tsiInternalLinks"></ul>
        </div>
        <div class="tsi-info-item">
            <div class="tsi-info-header"><span class="animated-icon" style="color:var(--green-1);">🌐</span><span class="tsi-info-title">URL Structure Analysis</span></div>
            <p style="margin:0;">Clarity Score: <strong id="tsiUrlClarityScore">—</strong></p>
            <p id="tsiUrlSuggestion" style="margin-top:4px;"></p>
        </div>
        <div class="tsi-info-item" style="grid-column: span 2;">
            <div class="tsi-info-header"><span class="animated-icon pulse" style="color:var(--yellow-1);">📰</span><span class="tsi-info-title">Meta Tags Optimization</span></div>
            <p><strong>Title:</strong> <span id="tsiMetaTitle">—</span></p>
            <p><strong>Description:</strong> <span id="tsiMetaDescription">—</span></p>
        </div>
        <div class="tsi-info-item">
            <div class="tsi-info-header"><span class="animated-icon" style="color:var(--orange-1);">🖼️</span><span class="tsi-info-title">Image Alt Text Suggestions</span></div>
            <ul id="tsiAltTexts"></ul>
        </div>
        <div class="tsi-info-item">
            <div class="tsi-info-header"><span class="animated-icon pulse" style="color:var(--pink-1);">🗺️</span><span class="tsi-info-title">Site Structure Mapping</span></div>
            <div class="site-map-container" id="tsiSiteMap"></div>
        </div>
      </div>
    </div>
    <div class="tsi-suggestions">
        <h4><span class="animated-icon">💡</span> Technical SEO Suggestions</h4>
        <ul id="tsiSuggestionsList"></ul>
    </div>
</div>

<!-- Keyword Intelligence -->
<div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-top:24px;">
    <span class="animated-icon" style="color:var(--yellow-1);">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
    </span>
    <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Keyword Intelligence</h3>
</div>
<div class="ki-card" id="keywordIntelligenceCard">
    <div class="ki-grid">
        <div class="ki-item">
            <h4 class="ki-item-title"><span class="animated-icon pulse" style="color:var(--yellow-1);">🧠</span>Semantic Keyword Research</h4>
            <div id="kiSemanticResearch" class="ki-tags"></div>
        </div>
        <div class="ki-item">
            <h4 class="ki-item-title"><span class="animated-icon" style="color:var(--orange-1);">🎯</span>Keyword Intent Classification</h4>
            <div id="kiIntentClassification" class="ki-tags"></div>
        </div>
        <div class="ki-item">
            <h4 class="ki-item-title"><span class="animated-icon pulse" style="color:var(--red-1);">🗺️</span>Related Terms Mapping</h4>
            <div id="kiRelatedTerms" class="ki-tags"></div>
        </div>
        <div class="ki-item">
            <h4 class="ki-item-title"><span class="animated-icon" style="color:var(--pink-1);">📊</span>Competitor Keyword Gap Analysis</h4>
            <div id="kiCompetitorGaps" class="ki-list"></div>
        </div>
        <div class="ki-item" style="grid-column: 1 / -1;">
             <h4 class="ki-item-title"><span class="animated-icon pulse" style="color:var(--purple-1);">🔑</span>Long-tail Semantic Suggestions</h4>
            <div id="kiLongTail" class="ki-list"></div>
        </div>
    </div>
</div>

<!-- Meta Info Layout -->
<div class="card meta-card" style="margin-top:24px;">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
        <span class="animated-icon pulse" style="color:var(--blue-1);">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.72"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.72-1.72"></path></svg>
        </span>
        <h3 class="t-grad">Meta & Heading Structure</h3>
    </div>
    <div class="space-y-3">
        <div class="cat-card">
            <div class="cat-card-title">Title Tag</div>
            <div id="titleVal" style="color:var(--ink); margin-top:4px;"></div>
        </div>
        <div class="cat-card">
            <div class="cat-card-title">Meta Description</div>
            <div id="metaVal" style="color:var(--ink); margin-top:4px;"></div>
        </div>
        <div class="cat-card">
            <div class="cat-card-title">Heading Map (H1-H4)</div>
            <div id="headingMap" class="space-y-2" style="margin-top:8px;"></div>
        </div>
    </div>
</div>

<!-- Site Speed & Core Web Vitals (NEW LAYOUT) -->
<div class="speed-card-new" id="speedCard">
    <div class="speed-header">
        <div class="speed-title">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"></path></svg>
            Site Speed & Core Web Vitals
        </div>
        <span id="speedBadge" class="speed-badge">Checking...</span>
    </div>
    <div class="speed-overview-bar"><div id="speedOverviewBar"></div></div>
    <p class="speed-overview-text" id="speedOverviewText">Overview not available yet.</p>
    
    <div class="speed-grid">
        <!-- Mobile -->
        <div class="speed-device-card">
            <div class="speed-device-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17 1.01L7 1c-1.1 0-2 .9-2 2v18c0 1.1.9 2 2 2h10c1.1 0 2-.9 2-2V3c0-1.1-.9-1.99-2-1.99zM17 19H7V5h10v14z"></path></svg>
                Mobile
            </div>
            <div style="display:flex; align-items:center; gap: 16px; margin-top:12px;">
                <div class="speed-device-score">
                    <svg><circle class="track" cx="30" cy="30" r="26"></circle><circle id="mobileScoreCircle" class="progress" cx="30" cy="30" r="26"></circle></svg>
                    <div id="mobileScoreVal" class="speed-device-score-val">0</div>
                </div>
                <div class="speed-device-metrics">
                    <div class="speed-device-metric"><span>LCP</span><strong id="mobileLcp"></strong></div>
                    <div class="speed-device-metric"><span>INP</span><strong id="mobileInp"></strong></div>
                    <div class="speed-device-metric"><span>CLS</span><strong id="mobileCls"></strong></div>
                </div>
            </div>
        </div>
        <!-- Desktop -->
        <div class="speed-device-card">
            <div class="speed-device-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"></path></svg>
                Desktop
            </div>
             <div style="display:flex; align-items:center; gap: 16px; margin-top:12px;">
                <div class="speed-device-score">
                    <svg><circle class="track" cx="30" cy="30" r="26"></circle><circle id="desktopScoreCircle" class="progress" cx="30" cy="30" r="26"></circle></svg>
                    <div id="desktopScoreVal" class="speed-device-score-val">0</div>
                </div>
                <div class="speed-device-metrics">
                    <div class="speed-device-metric"><span>LCP</span><strong id="desktopLcp"></strong></div>
                    <div class="speed-device-metric"><span>INP</span><strong id="desktopInp"></strong></div>
                    <div class="speed-device-metric"><span>CLS</span><strong id="desktopCls"></strong></div>
                </div>
            </div>
        </div>
    </div>
    <div class="speed-opportunities">
        <div class="speed-opportunities-title">🚀 Opportunities</div>
        <ul id="speedOpportunitiesList"><li>Run analysis to see opportunities.</li></ul>
    </div>
</div>


  <div class="ground-slab">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
      <div class="king">🧭</div>
      <div>
        <div class="t-grad" style="font-weight:900;font-size:18px">Semantic SEO Ground</div>
        <div style="font-size:12px;color:#b6c2cf">Six categories • Five checks each • Click “Improve” for guidance</div>
      </div>
    </div>
    <div id="cats" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px"></div>
  </div>

  <dialog id="improveModal" class="rounded-2xl p-0 w-[min(680px,95vw)]" style="border:none;border-radius:16px">
    <div class="card">
      <div style="display:flex;align-items:start;justify-content:space-between;gap:10px">
        <h4 id="improveTitle" class="t-grad" style="font-weight:900;margin:0">Improve</h4>
        <form method="dialog"><button class="pill">Close</button></form>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:8px">
        <div class="card"><div style="font-size:12px;color:#94a3b8">Category</div><div id="improveCategory" style="font-weight:700">—</div></div>
        <div class="card">
          <div style="font-size:12px;color:#94a3b8">Score</div>
          <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
            <span id="improveScore" class="score-pill">—</span>
            <span id="improveBand" class="pill">—</span>
          </div>
        </div>
        <a id="improveSearch" target="_blank" class="card" style="text-align:center;display:flex;align-items:center;justify-content:center;background:linear-gradient(90deg,#ff149326,#00c6ff26);border:1px solid #ffffff22;text-decoration:none">
          <span style="font-size:13px;color:var(--ink)">Search guidance</span>
        </a>
      </div>
      <div style="margin-top:10px">
        <div style="font-size:12px;color:#94a3b8">Why this matters</div>
        <p id="improveWhy" style="font-size:14px;color:var(--ink);margin-top:6px">—</p>
      </div>
      <div style="margin-top:10px">
        <div style="font-size:12px;color:#94a3b8">How to improve</div>
        <ul id="improveTips" style="margin-top:8px;padding-left:18px;display:grid;gap:6px;font-size:14px;color:var(--ink)"></ul>
      </div>
    </div>
  </dialog>

</section>
@endsection
