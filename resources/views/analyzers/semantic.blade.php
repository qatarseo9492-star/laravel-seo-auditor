@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')
 <meta name="csrf-token" content="{{ csrf_token() }}">
 <script src="{{ asset('js/technical-seo.client.js') }}" defer></script>
 <link rel="stylesheet" href="{{ asset('css/techseo-neon.css') }}">
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
  .maxw{max-width:1150px;margin:0 auto;border:1px solid var(--outline);border-radius:18px;padding:8px;background:linear-gradient(0deg,rgba(255,255,255,0.02),rgba(255,255,255,0.01))}

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

  .card{border-radius:18px;padding:18px;background:var(--card);border:1px solid var(--outline);box-shadow:0 10px 30px rgba(0,0,0,.35)}
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
  .speed-card {
    background: linear-gradient(145deg, #0c2b2a, #0c1a2e);
    border: 1px solid var(--green-2);
    border-radius: 20px;
    padding: 16px;
    margin-top: 24px;
    box-shadow: 0 0 32px rgba(0, 255, 198, 0.3), inset 0 0 12px rgba(0, 0, 0, 0.5);
  }
  .sp-head .ico {
    width: 40px; height: 40px;
    background: linear-gradient(135deg, rgba(0, 255, 138, 0.2), rgba(0, 198, 255, 0.2));
    border: 1px solid var(--green-1);
    border-radius: 12px;
    color: var(--green-1);
    font-size: 20px;
  }
  .sp-wheels { gap: 24px; }
  .wheel-card {
    background: rgba(8, 20, 20, 0.7);
    border-color: rgba(0, 255, 138, 0.3);
  }
  .sp-bars-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-top: 20px; }
  .sp-tile {
    background: rgba(8, 20, 20, 0.7);
    border: 1px solid rgba(0, 255, 138, 0.3);
    border-radius: 14px;
    padding: 12px;
  }
  .sp-meter {
    height: 14px;
    background: rgba(0,0,0,0.4);
    border-color: rgba(255,255,255,0.1);
    position: relative;
    overflow: visible;
  }
  .sp-meter > span {
    background: linear-gradient(90deg, var(--red-1), var(--orange-1), var(--yellow-1) 50%, var(--green-1) 100%);
    box-shadow: 0 0 8px var(--green-1);
    position: relative;
  }
  .sp-meter > span::after {
    content: '';
    position: absolute;
    right: 0; top: -3px;
    width: 4px; height: 20px;
    background: #fff;
    border-radius: 2px;
    box-shadow: 0 0 10px #fff, 0 0 20px #fff;
  }
  .sp-fixes {
    background: linear-gradient(135deg, #0d3b2a, #112d3d);
    border: 1px solid var(--green-2);
    box-shadow: 0 0 24px rgba(0, 255, 198, 0.25);
  }
  .sp-fixes h4 { color: var(--green-1); }
  .sp-fixes li { border-left-color: var(--green-1); color: #c0fdee; }
  
  /* ===================== Content Optimization (Futuristic) ===================== */
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.7; transform: scale(1.1); } }
  .animated-icon {
      display: inline-block;
      animation: spin 6s linear infinite;
      color: var(--blue-1);
  }
  .animated-icon.pulse {
      animation: pulse 3s ease-in-out infinite, spin 10s linear infinite reverse;
      color: var(--pink-1);
  }

  .co-card {
    --co-bg: #191919;
    --co-border: var(--outline);

    border-radius: 20px;
    background: var(--co-bg);
    border: 1px solid var(--co-border);
    padding: 16px;
    margin-top: 12px; /* Adjusted margin */
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
  .tsi-info-header .animated-icon { color: var(--blue-1); }
  .tsi-info-title {
    background: linear-gradient(90deg, var(--blue-1), var(--pink-1));
    -webkit-background-clip: text; background-clip: text; color: transparent;
    font-size: 15px;
  }
  .tsi-suggestions {
    border: 1px solid transparent;
    background-image: 
        linear-gradient(to right, #1a0f2b, #1a0f2b), 
        linear-gradient(90deg, var(--pink-1), var(--purple-1));
    box-shadow: 0 0 24px rgba(255, 20, 147, 0.3);
  }
  .tsi-suggestions h4 {
    background: linear-gradient(90deg, var(--pink-1), var(--yellow-1));
    -webkit-background-clip: text; background-clip: text; color: transparent;
  }
  .tsi-suggestions li { color: #e2d8ff; }
  .tsi-suggestions li::before { content: '💡'; position: absolute; left: -10px; }
  
  
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
/* Robust init so the Analyze button always works even if scripts load after DOM ready */
(function(){
  const init = () => {
    const $ = s=>document.querySelector(s);

    /* ============== Element refs ============== */
    const mw=$('#mw'), mwRing=$('#mwRing'), mwNum=$('#mwNum');
    const overallBar=$('#overallBar'), overallFill=$('#overallFill'), overallPct=$('#overallPct');
    const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'), chipHuman=$('#chipHuman'), chipAI=$('#chipAI');

    const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'), pasteBtn=$('#pasteBtn'),
          importBtn=$('#importBtn'), importFile=$('#importFile'), printBtn=$('#printBtn'),
          resetBtn=$('#resetBtn'), exportBtn=$('#exportBtn');

    // NOTE: statF, statG etc are removed as their layout is gone.
    const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap'), recsEl=$('#recs'), catsEl=$('#cats');

    const chipHttp=$('#chipHttp'), chipTitle=$('#chipTitle'), chipMeta=$('#chipMeta'),
          chipCanon=$('#chipCanon'), chipRobots=$('#chipRobots'), chipViewport=$('#chipViewport'),
          chipH=$('#chipH'), chipIntChip=$('#chipInt'), chipSchema=$('#chipSchema'), chipAuto=$('#chipAuto');

    const errorBox = $('#errorBox');

    const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
          mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
          mTips=$('#improveTips'), mLink=$('#improveSearch');

    /* Readability UI is removed */

    /* Speed UI */
    const mwMobile=$('#mwMobile'), ringMobile=$('#ringMobile'), numMobile=$('#numMobile');
    const mwDesktop=$('#mwDesktop'), ringDesktop=$('#ringDesktop'), numDesktop=$('#numDesktop');
    const lcpVal=$('#lcpVal'), lcpBar=$('#lcpBar'), lcpMeter=$('#lcpMeter');
    const clsVal=$('#clsVal'), clsBar=$('#clsBar'), clsMeter=$('#clsMeter');
    const inpVal=$('#inpVal'), inpBar=$('#inpBar'), inpMeter=$('#inpMeter');
    const ttfbVal=$('#ttfbVal'), ttfbBar=$('#ttfbBar'), ttfbMeter=$('#ttfbMeter');
    const psiStatus=$('#psiStatus'), psiFixes=$('#psiFixes');

    /* --- Content Optimization UI refs --- */
    const mwContent=$('#mwContent'), ringContent=$('#ringContent'), numContent=$('#numContent');
    const coCard = $('#contentOptimizationCard');
    if (coCard) {
        const coTopicCoverageText = coCard.querySelector('#coTopicCoverageText');
        const coTopicCoverageProgress = coCard.querySelector('#coTopicCoverageProgress');
        const coContentGapsText = coCard.querySelector('#coContentGapsText');
        const coContentGapsTags = coCard.querySelector('#coContentGapsTags');
        const coSchemaTags = coCard.querySelector('#coSchemaTags');
        const coIntentTag = coCard.querySelector('#coIntentTag');
        const coGradeTag = coCard.querySelector('#coGradeTag');
        window.__coElements = { coTopicCoverageText, coTopicCoverageProgress, coContentGapsText, coContentGapsTags, coSchemaTags, coIntentTag, coGradeTag };
    }
    
    /* --- Technical SEO Integration UI refs --- */
    const mwTSI = $('#mwTSI'), ringTSI = $('#ringTSI'), numTSI = $('#numTSI');
    const tsiInternalLinks = $('#tsiInternalLinks');
    const tsiUrlClarityScore = $('#tsiUrlClarityScore');
    const tsiUrlSuggestion = $('#tsiUrlSuggestion');
    const tsiMetaTitle = $('#tsiMetaTitle');
    const tsiMetaDescription = $('#tsiMetaDescription');
    const tsiAltTexts = $('#tsiAltTexts');
    const tsiSiteMap = $('#tsiSiteMap');
    const tsiSuggestionsList = $('#tsiSuggestionsList');
    
    /* --- Keyword Intelligence UI refs --- */
    const kiSemanticResearch = $('#kiSemanticResearch');
    const kiIntentClassification = $('#kiIntentClassification');
    const kiRelatedTerms = $('#kiRelatedTerms');
    const kiCompetitorGaps = $('#kiCompetitorGaps');
    const kiLongTail = $('#kiLongTail');

    /* --- NEW: Content Analysis Engine UI refs --- */
    const mwCAE = $('#mwCAE'), ringCAE = $('#ringCAE'), numCAE = $('#numCAE');
    const caeTopicClusters = $('#caeTopicClusters');
    const caeEntities = $('#caeEntities');
    const caeKeywords = $('#caeKeywords');
    const caeRelevanceScore = $('#caeRelevanceScore');
    const caeRelevanceBar = $('#caeRelevanceBar');
    const caeIntent = $('#caeIntent');


    /* Helpers */
    const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
    const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
    const bandIcon=s=>s>=80?'✅':(s>=60?'🟧':'🔴');
    function setChip(el,label,value,score){ if(!el)return; el.classList.remove('good','warn','bad'); const b=bandName(score); el.classList.add(b); el.innerHTML=`<i>${bandIcon(score)}</i><span>${label}: ${value}</span>`; };
    const showError=(msg,detail)=>{ errorBox.style.display='block'; errorBox.textContent=msg+(detail?`

${detail}`:''); };
    const clearError=()=>{ errorBox.style.display='none'; errorBox.textContent=''; };

    /* ===== Category/KB/scoring (unchanged logic) ===== */
    const CATS=[{name:'User Signals & Experience',icon:'📱',checks:['Mobile-friendly, responsive layout','Optimized speed (compression, lazy-load)','Core Web Vitals passing (LCP/INP/CLS)','Clear CTAs and next steps','Accessible basics (alt text, contrast)']},{name:'Entities & Context',icon:'🧩',checks:['sameAs/Organization details present','Valid schema markup (Article/FAQ/Product)','Related entities covered with context','Primary entity clearly defined','Organization contact/about page visible']},{name:'Structure & Architecture',icon:'🏗️',checks:['Logical H2/H3 headings & topic clusters','Internal links to hub/related pages','Clean, descriptive URL slug','Breadcrumbs enabled (+ schema)','XML sitemap logical structure']},{name:'Content Quality',icon:'🧠',checks:['E-E-A-T signals (author, date, expertise)','Unique value vs. top competitors','Facts & citations up to date','Helpful media (images/video) w/ captions','Up-to-date examples & screenshots']},{name:'Content & Keywords',icon:'📝',checks:['Define search intent & primary topic','Map target & related keywords (synonyms/PAA)','H1 includes primary topic naturally','Integrate FAQs / questions with answers','Readable, NLP-friendly language']},{name:'Technical Elements',icon:'⚙️',checks:['Title tag (≈50–60 chars) w/ primary keyword','Meta description (≈140–160 chars) + CTA','Canonical tag set correctly','Indexable & listed in XML sitemap','Robots directives valid']}];

    const KB={'Mobile-friendly, responsive layout':{why:'Most traffic is mobile; poor UX kills engagement.',tips:['Responsive breakpoints & fluid grids.','Tap targets ≥44px.','Avoid horizontal scroll.'],link:'https://search.google.com/test/mobile-friendly'},'Optimized speed (compression, lazy-load)':{why:'Speed affects abandonment and CWV.',tips:['Use WebP/AVIF.','HTTP/2 + CDN caching.','Lazy-load below-the-fold.'],link:'https://web.dev/fast/'},'Core Web Vitals passing (LCP/INP/CLS)':{why:'Passing CWV improves experience & stability.',tips:['Preload hero image.','Minimize long JS tasks.','Reserve media space.'],link:'https://web.dev/vitals/'},'Clear CTAs and next steps':{why:'Clarity increases conversions and task completion.',tips:['One primary CTA per view.','Action verbs + benefit.','Explain what happens next.'],link:'https://www.nngroup.com/articles/call-to-action-buttons/'},'Accessible basics (alt text, contrast)':{why:'Accessibility broadens reach and reduces risk.',tips:['Alt text on images.','Contrast ratio ≥4.5:1.','Keyboard focus states.'],link:'https://www.w3.org/WAI/standards-guidelines/wcag/'},'sameAs/Organization details present':{why:'Entity grounding disambiguates your brand.',tips:['Organization JSON-LD.','sameAs links to profiles.','NAP consistency.'],link:'https://schema.org/Organization'},'Valid schema markup (Article/FAQ/Product)':{why:'Structured data unlocks rich results.',tips:['Validate with Rich Results Test.','Mark up visible content only.','Keep to supported types.'],link:'https://search.google.com/test/rich-results'},'Related entities covered with context':{why:'Covering related entities builds topical depth.',tips:['Mention related concepts.','Explain relationships.','Link to references.'],link:'https://developers.google.com/knowledge-graph'},'Primary entity clearly defined':{why:'A single main entity clarifies page purpose.',tips:['Define at the top.','Use consistent naming.','Add schema about it.'],link:'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data'},'Organization contact/about page visible':{why:'Trust & contact clarity support E-E-A-T.',tips:['Add /about and /contact.','Link from header/footer.','Show address & email.'],link:'https://developers.google.com/search/docs/fundamentals/creating-helpful-content'},'Logical H2/H3 headings & topic clusters':{why:'Hierarchy helps skimming and indexing.',tips:['Group subtopics under H2.','Use H3 for steps/examples.','Keep sections concise.'],link:'https://moz.com/learn/seo/site-structure'},'Internal links to hub/related pages':{why:'Internal links distribute authority & context.',tips:['Link to 3–5 relevant hubs.','Descriptive anchors.','Further reading section.'],link:'https://ahrefs.com/blog/internal-links/'},'Clean, descriptive URL slug':{why:'Readable slugs improve CTR & clarity.',tips:['3–5 meaningful words.','Hyphens & lowercase.','Avoid query strings.'],link:'https://developers.google.com/search/docs/crawling-indexing/url-structure'},'Breadcrumbs enabled (+ schema)':{why:'Breadcrumbs clarify location & show in SERP.',tips:['Visible breadcrumbs.','BreadcrumbList JSON-LD.','Keep depth logical.'],link:'https://developers.google.com/search/docs/appearance/structured-data/breadcrumb'},'XML sitemap logical structure':{why:'Sitemap accelerates discovery & updates.',tips:['Include canonical URLs.','Segment large sites.','Reference in robots.txt.'],link:'https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview'},'E-E-A-T signals (author, date, expertise)':{why:'Trust signals reduce bounce & build credibility.',tips:['Author bio + credentials.','Last updated date.','Editorial policy page.'],link:'https://developers.google.com/search/blog/2022/08/helpful-content-update'},'Unique value vs. top competitors':{why:'Differentiation is necessary to rank & retain.',tips:['Original data/examples.','Pros/cons & criteria.','Why your approach is better.'],link:'https://backlinko.com/seo-techniques'},'Facts & citations up to date':{why:'Freshness + accuracy boosts trust.',tips:['Cite primary sources.','Update stats ≤12 months.','Prefer canonical/DOI links.'],link:'https://scholar.google.com/'},'Helpful media (images/video) w/ captions':{why:'Media improves comprehension & dwell time.',tips:['Add 3–6 figures.','Descriptive captions.','Compress + lazy-load.'],link:'https://web.dev/optimize-lcp/'},'Up-to-date examples & screenshots':{why:'Current visuals reflect product reality.',tips:['Refresh UI shots.','Date your examples.','Replace deprecated flows.'],link:'https://www.nngroup.com/articles/guidelines-for-screenshots/'},'Define search intent & primary topic':{why:'Matching intent drives relevance & time on page.',tips:['State outcome early.','Align format to intent.','Use concrete examples.'],link:'https://ahrefs.com/blog/search-intent/'},'Map target & related keywords (synonyms/PAA)':{why:'Variants improve recall & completeness.',tips:['List 6–12 variants.','5–10 PAA questions.','Answer PAA in 40–60 words.'],link:'https://developers.google.com/search/docs/fundamentals/seo-starter-guide'},'H1 includes primary topic naturally':{why:'Clear topic helps users and algorithms.',tips:['One H1 per page.','Topic near the start.','Be descriptive.'],link:'https://web.dev/learn/html/semantics/#headings'},'Integrate FAQs / questions with answers':{why:'Captures long-tail & can earn rich results.',tips:['Pick 3–6 questions.','Answer briefly.','Add FAQPage JSON-LD.'],link:'https://developers.google.com/search/docs/appearance/structured-data/faqpage'},'Readable, NLP-friendly language':{why:'Plain, direct writing improves comprehension.',tips:['≤20 words/sentence.','Active voice.','Define jargon on first use.'],link:'https://www.plainlanguage.gov/guidelines/'},'Title tag (≈50–60 chars) w/ primary keyword':{why:'Title remains the strongest on-page signal.',tips:['50–60 chars.','Primary topic first.','Avoid duplication.'],link:'https://moz.com/learn/seo/title-tag'},'Meta description (≈140–160 chars) + CTA':{why:'Meta drives CTR which correlates with rankings.',tips:['140–160 chars.','Benefit + CTA.','Match intent.'],link:'https://moz.com/learn/seo/meta-description'},'Canonical tag set correctly':{why:'Avoid duplicates; consolidate signals.',tips:['One canonical.','Absolute URL.','No conflicting canonicals.'],link:'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls'},'Indexable & listed in XML sitemap':{why:'Indexation is prerequisite to ranking.',tips:['No noindex.','Include in sitemap.','Submit in Search Console.'],link:'https://developers.google.com/search/docs/crawling-indexing/overview'},'Robots directives valid':{why:'Avoid accidental noindex/nofollow.',tips:['robots meta allows indexing.','robots.txt not blocking.','Use directives consistently.'],link:'https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag'}};

    function clamp01num(n){return Math.max(0,Math.min(100,Number(n)||0))}
    function scoreChecklist(label,data,url,targetKw=''){const qs=data.quick_stats||{};const cs=data.content_structure||{};const ps=data.page_signals||{};const r=data.readability||{};const h1=(cs.headings&&cs.headings.H1?cs.headings.H1.length:0)||0;const h2=(cs.headings&&cs.headings.H2?cs.headings.H2.length:0)||0;const h3=(cs.headings&&cs.headings.H3?cs.headings.H3.length:0)||0;const title=(cs.title||'');const meta=(cs.meta_description||'');const internal=Number(qs.internal_links||0);const external=Number(qs.external_links||0);const schemaTypes=new Set((data.page_signals?.schema_types)||[]);const robots=(data.page_signals?.robots||'').toLowerCase();const hasFAQ=schemaTypes.has('FAQPage');const hasArticle=schemaTypes.has('Article')||schemaTypes.has('NewsArticle')||schemaTypes.has('BlogPosting');const urlPath=(()=>{try{return new URL(url).pathname;}catch{return '/';}})();const slugScore=(()=>{const hasQuery=url.includes('?');const segs=urlPath.split('/').filter(Boolean);const words=segs.join('-').split('-').filter(Boolean);if(hasQuery)return 55;if(segs.length>6)return 60;if(words.some(w=>w.length>24))return 65;return 85;})();switch(label){case'Mobile-friendly, responsive layout':return ps.has_viewport?88:58;case'Optimized speed (compression, lazy-load)':return 60;case'Core Web Vitals passing (LCP/INP/CLS)':return 60;case'Clear CTAs and next steps':return meta.length>=140&&/learn|get|try|start|buy|sign|download|contact/i.test(meta)?80:60;case'Accessible basics (alt text, contrast)':return (data.images_alt_count||0)>=3?82:((data.images_alt_count||0)>=1?68:48);case'sameAs/Organization details present':return ps.has_org_sameas?90:55;case'Valid schema markup (Article/FAQ/Product)':return (hasArticle||hasFAQ||schemaTypes.has('Product'))?85:(schemaTypes.size>0?70:50);case'Related entities covered with context':return external>=2?72:60;case'Primary entity clearly defined':return ps.has_main_entity?85:(h1>0?72:58);case'Organization contact/about page visible':return 60;case'Logical H2/H3 headings & topic clusters':return (h2>=3&&h3>=2)?85:(h2>=2?70:55);case'Internal links to hub/related pages':return internal>=5?85:(internal>=2?65:45);case'Clean, descriptive URL slug':return slugScore;case'Breadcrumbs enabled (+ schema)':return ps.has_breadcrumbs?85:55;case'XML sitemap logical structure':return 60;case'E-E-A-T signals (author, date, expertise)':return ps.has_org_sameas?75:65;case'Unique value vs. top competitors':return 60;case'Facts & citations up to date':return external>=2?78:58;case'Helpful media (images/video) w/ captions':return (data.images_alt_count||0)>=3?82:58;case'Up-to-date examples & screenshots':return 60;case'Define search intent & primary topic':return (title&&h1>0)?78:60;case'Map target & related keywords (synonyms/PAA)':{const kw=(targetKw||'').trim();if(!kw)return 60;const found=(title.toLowerCase().includes(kw.toLowerCase())||(cs.headings?.H1||[]).join(' || ').toLowerCase().includes(kw.toLowerCase()));return found?80:62}case'H1 includes primary topic naturally':{const kw=(targetKw||'').trim();if(h1===0)return 45;if(!kw)return 72;const found=(cs.headings?.H1||[]).some(h=>h.toLowerCase().includes(kw.toLowerCase()));return found?84:72}case'Integrate FAQs / questions with answers':return hasFAQ?85:(/(faq|questions?)/i.test((cs.headings?.H2||[]).join(' ')+' '+(cs.headings?.H3||[]).join(' '))?70:55);case'Readable, NLP-friendly language':return clamp01num(r.score||0);case'Title tag (≈50–60 chars) w/ primary keyword':{const len=(title||'').length;return (len>=50&&len<=60)?88:(len?68:45)}case'Meta description (≈140–160 chars) + CTA':{const len=(meta||'').length;const hasCTA=/learn|get|try|start|buy|sign|download|contact/i.test(meta||'');return (len>=140&&len<=160)?(hasCTA?90:82):(len?65:48)}case'Canonical tag set correctly':return ps.canonical?85:55;case'Indexable & listed in XML sitemap':return robots.includes('noindex')?20:80;case'Robots directives valid':return (robots&&/(noindex|none)/.test(robots))?45:75;}return 60}

    function renderCategories(data,url,targetKw){const catsEl=document.querySelector('#cats');catsEl.innerHTML='';let autoGood=0;CATS.forEach(cat=>{const rows=cat.checks.map(lbl=>{const s=scoreChecklist(lbl,data,url,targetKw);const fill=s>=80?'fill-green':(s>=60?'fill-orange':'fill-red');const pill=s>=80?'score-pill--green':s>=60?'score-pill--orange':'score-pill--red';if(s>=80)autoGood++;return {label:lbl,score:s,fill,pill,bandTxt:(s>=80?'Good (≥80)':s>=60?'Needs work (60–79)':'Low (<60)')};});const total=rows.length;const passed=rows.filter(r=>r.score>=80).length;const pct=Math.round((passed/Math.max(1,total))*100);const card=document.createElement('div');card.className='cat-card';card.innerHTML=`<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px"><div style="display:flex;align-items:center;gap:8px"><div class="king" style="width:34px;height:34px">${cat.icon}</div><div><div class="t-grad" style="font-size:16px;font-weight:900">${cat.name}</div><div style="font-size:12px;color:#b6c2cf">Keep improving</div></div></div><div class="pill">${passed} / ${total}</div></div><div class="progress" style="margin-bottom:8px"><span style="width:${pct}%"></span></div><div class="space-y-2" id="list"></div>`;const list=card.querySelector('#list');rows.forEach(row=>{const dot=row.score>=80?'#10b981':row.score>=60?'#f59e0b':'#ef4444';const el=document.createElement('div');el.className='check';el.innerHTML=`<div style="display:flex;align-items:center;gap:8px"><span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:${dot}"></span><div class="font-semibold" style="font-size:13px">${row.label}</div></div><div style="display:flex;align-items:center;gap:6px"><span class="score-pill ${row.pill}">${row.score}</span><button class="improve-btn ${row.fill}" type="button">Improve</button></div>`;el.querySelector('.improve-btn').addEventListener('click',()=>{const kb=KB[row.label]||{why:'This item impacts relevance and UX.',tips:['Aim for ≥80 and re-run the analyzer.'],link:'https://www.google.com'};mTitle.textContent=row.label;mCat.textContent=cat.name;mScore.textContent=row.score;mBand.textContent=row.bandTxt;mBand.className='pill '+(row.score>=80?'score-pill--green':row.score>=60?'score-pill--orange':'score-pill--red');mWhy.textContent=kb.why;mTips.innerHTML='';(kb.tips||[]).forEach(t=>{const li=document.createElement('li');li.textContent=t;mTips.appendChild(li)});mLink.href=kb.link||('https://www.google.com/search?q='+encodeURIComponent(row.label+' best practices'));if(typeof modal.showModal==='function')modal.showModal();else modal.setAttribute('open','')});list.appendChild(el)});catsEl.appendChild(card)});chipAuto.textContent=autoGood;}

    /* API Calls */
    async function callApi(endpoint, url) {
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify({ url })
        });
        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`API Error at ${endpoint} (${res.status}): ${errorText.slice(0, 800)}`);
        }
        return res.json();
    }
    
    // Specific API call wrappers
    const callAnalyzer = (url) => callApi('/api/semantic-analyze', url);
    const callTechnicalSeoApi = (url) => callApi('/api/technical-seo-analyze', url);
    const callKeywordApi = (url) => callApi('/api/keyword-analyze', url);
    const callContentEngineApi = (url) => callApi('/api/content-engine-analyze', url);
    const callPSI = (url) => callApi('/semantic-analyzer/psi', url);


    function setRunning(isOn){if(!analyzeBtn)return;analyzeBtn.disabled=isOn;analyzeBtn.style.opacity=isOn?.6:1;analyzeBtn.textContent=isOn?'Analyzing…':'🔍 Analyze'}
    
    /* Speed helpers */
    const band = s => s>=80?'good':(s>=60?'warn':'bad');
    const scoreFromBounds=(val,good,poor)=>{if(val==null||isNaN(val))return 0;if(val<=good)return 100;if(val>=poor)return 0;return Math.round(100*(1-((val-good)/(poor-good))))};
    function setWheel(elRing, elNum, container, score, prefix){
        if (!elRing || !elNum || !container) return; // Guard clause
        const b=band(score);
        container.classList.remove('good','warn','bad');
        container.classList.add(b);
        elRing.style.setProperty('--v',score);
        elNum.textContent=(prefix?prefix+' ':'')+score+'%';
    }
    function setSpMeter(barEl,valEl,raw,score,fmt,meterWrap){valEl.textContent=raw==null?'—':(fmt?fmt(raw):raw);barEl.style.width=clamp01(score)+'%';if(meterWrap){meterWrap.classList.remove('good','warn','bad');meterWrap.classList.add(band(score));}}

    /* ===== Analyze ===== */
    $('#analyzeBtn')?.addEventListener('click', async e=>{
      e.preventDefault();
      clearError();
      const url=(urlInput.value||'').trim();
      if(!url){showError('Please enter a URL.');return;}
      try{
        setRunning(true);
        
        // --- Reset UIs before fetching ---
        [kiSemanticResearch, kiIntentClassification, kiRelatedTerms, kiCompetitorGaps, kiLongTail, caeTopicClusters, caeEntities, caeKeywords, caeIntent].forEach(el => {
            if(el) el.innerHTML = '<span class="chip">Analyzing...</span>';
        });

        // --- Fire all API calls in parallel ---
        const [data, tsiData, kiData, caeData, psiData] = await Promise.all([
            callAnalyzer(url).catch(err => { showError('Content analysis failed.', err.message); return null; }),
            callTechnicalSeoApi(url).catch(err => { showError('Tech SEO analysis failed.', err.message); return null; }),
            callKeywordApi(url).catch(err => { showError('Keyword analysis failed.', err.message); return null; }),
            callContentEngineApi(url).catch(err => { showError('Content Engine analysis failed.', err.message); return null; }),
            callPSI(url).catch(err => {
                psiStatus.textContent='Unavailable';
                psiFixes.innerHTML=`<li>⚠️ ${String(err.message||err)}. Make sure PSI key is set server-side.</li>`;
                return null;
            })
        ]);

        if(!data||data.error) throw new Error(data?.error||'Local data parsing failed');

        window.__lastData={...data,url};

        const score=clamp01(data.overall_score||0);
        setWheel(mwRing, mwNum, mw, score, '');
        
        overallFill.style.width=score+'%';
        overallPct.textContent=score+'%';
        setChip(chipOverall,'Overall',`${score} /100`,score);

        const cmap={};(data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
        const contentScore=Math.round(([cmap['Content & Keywords'],cmap['Content Quality']].filter(v=>typeof v==='number').reduce((a,b)=>a+b,0))/2||0);
        setChip(chipContent,'Content',`${contentScore} /100`,contentScore);

        const r=data.readability||{};
        const human=clamp01(Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3));
        const ai=clamp01(100-human);
        setChip(chipWriter,'Writer',human>=60?'Likely Human':'Possibly AI',human);
        setChip(chipHuman,'Human-like',`${human} %`,human);
        setChip(chipAI,'AI-like',`${ai} %`,100-human);
        
        // --- Meta & Heading population logic ---
        if (data.content_structure) {
            titleVal.textContent = data.content_structure.title || 'Not Found';
            metaVal.textContent = data.content_structure.meta_description || 'Not Found';
            
            const hs = data.content_structure.headings || {};
            chipH.textContent = `H1:${(hs.H1||[]).length} • H2:${(hs.H2||[]).length} • H3:${(hs.H3||[]).length}`;
            headingMap.innerHTML = ''; 
            
            const allowedHeadings = ['H1', 'H2', 'H3', 'H4'];
            let hasHeadings = false;
            
            Object.entries(hs).forEach(([lvl, arr]) => {
                if (!arr || !arr.length || !allowedHeadings.includes(lvl)) return;
                hasHeadings = true;
                const box = document.createElement('div');
                box.className = 'cat-card'; 
                box.innerHTML = `<div class="cat-card-title uppercase">${lvl} (${arr.length})</div><div style="display:flex; flex-direction:column; gap: 4px; margin-top: 8px;">` + arr.map(t => `<div style="font-size:13px; line-height:1.4;">• ${t}</div>`).join('') + `</div>`;
                headingMap.appendChild(box);
            });

            if (!hasHeadings) {
                headingMap.innerHTML = `<p style="font-size:13px; color:#94a3b8;">No H1-H4 headings were found on the analyzed page.</p>`;
            }
        }
        
        chipHttp.textContent='200';
        chipCanon.textContent=(data.page_signals?.canonical||'—')||'—';
        chipRobots.textContent=(data.page_signals?.robots||'—')||'—';
        chipViewport.textContent=data.page_signals?.has_viewport?'yes':'—';
        chipIntChip.textContent=data.quick_stats?.internal_links??0;
        chipSchema.textContent=(data.page_signals?.schema_types||[]).length;
        
        // --- POPULATE CONTENT OPTIMIZATION ---
        if (data.content_optimization && window.__coElements) {
            const co = data.content_optimization;
            const { coTopicCoverageText, coTopicCoverageProgress, coContentGapsText, coContentGapsTags, coSchemaTags, coIntentTag, coGradeTag } = window.__coElements;

            setWheel(ringContent, numContent, mwContent, clamp01(co.nlp_score || 0), '');

            if (co.topic_coverage) {
                coTopicCoverageText.innerHTML = `Covers <strong>${co.topic_coverage.covered} of ${co.topic_coverage.total}</strong> key topics.`;
                coTopicCoverageProgress.style.width = co.topic_coverage.percentage + '%';
            }
            if (co.content_gaps?.missing_topics) {
                coContentGapsTags.innerHTML = co.content_gaps.missing_topics.map(topic => `<span class="chip ${topic.severity}"><i>${topic.severity==='bad'?'🔴':'🟧'}</i><span>${topic.term}</span></span>`).join('');
            }
             if (co.schema_suggestions) {
                coSchemaTags.innerHTML = co.schema_suggestions.map(schema => `<span class="chip good"><i>✅</i><span>${schema}</span></span>`).join('');
            }
            if (co.readability_intent) {
                coIntentTag.innerHTML = `Intent: ${co.readability_intent.intent}`;
                coGradeTag.innerHTML = `Grade Level: ${co.readability_intent.grade_level}`;
            }
        }
        
        // --- POPULATE TECHNICAL SEO INTEGRATION ---
        if (tsiData) {
            const tsi = tsiData;
            setWheel(ringTSI, numTSI, mwTSI, tsi.score || 0, '');
            tsiInternalLinks.innerHTML = (tsi.internal_linking || []).map(l => `<li>${l.text} with anchor: <code>${l.anchor}</code></li>`).join('') || '<li>No suggestions.</li>';
            tsiUrlClarityScore.textContent = `${tsi.url_structure?.clarity_score || 'N/A'}/100`;
            tsiUrlSuggestion.textContent = tsi.url_structure?.suggestion || 'N/A';
            tsiMetaTitle.textContent = tsi.meta_optimization?.title || 'N/A';
            tsiMetaDescription.textContent = tsi.meta_optimization?.description || 'N/A';
            tsiAltTexts.innerHTML = (tsi.alt_text_suggestions || []).map(a => `<li><code>${a.image_src}</code> → "${a.suggestion}"</li>`).join('') || '<li>No suggestions.</li>';
            tsiSiteMap.innerHTML = tsi.site_structure_map || 'N/A';
            tsiSuggestionsList.innerHTML = (tsi.suggestions || []).map(s => `<li class="${s.type}">${s.text}</li>`).join('') || '<li>No suggestions.</li>';
        }
        
        // --- POPULATE KEYWORD INTELLIGENCE ---
        if(kiData) {
            const ki = kiData;
            kiSemanticResearch.innerHTML = (ki.semantic_research || []).map(k => `<span class="chip">${k}</span>`).join('') || '<span class="chip">No data</span>';
            kiIntentClassification.innerHTML = (ki.intent_classification || []).map(k => {
                let intentClass = 'intent-info';
                if (k.intent.toLowerCase().includes('trans')) intentClass = 'intent-trans';
                if (k.intent.toLowerCase().includes('nav')) intentClass = 'intent-nav';
                return `<span class="chip ${intentClass}">${k.keyword} <i>(${k.intent})</i></span>`;
            }).join('') || '<span class="chip">No data</span>';
            kiRelatedTerms.innerHTML = (ki.related_terms || []).map(k => `<span class="chip">${k}</span>`).join('') || '<span class="chip">No data</span>';
            kiCompetitorGaps.innerHTML = (ki.competitor_gaps || []).map(k => `<div class="ki-list-item">• ${k}</div>`).join('') || '<div class="ki-list-item">No gaps found.</div>';
            kiLongTail.innerHTML = (ki.long_tail_suggestions || []).map(k => `<div class="ki-list-item">• ${k}</div>`).join('') || '<div class="ki-list-item">No suggestions.</div>';
        }
        
        // --- POPULATE CONTENT ANALYSIS ENGINE ---
        if(caeData) {
            const cae = caeData;
            setWheel(ringCAE, numCAE, mwCAE, cae.score || 0, '');
            caeTopicClusters.innerHTML = (cae.topic_clusters || []).map(t => `<span class="chip">${t}</span>`).join('');
            caeEntities.innerHTML = (cae.entities || []).map(e => `<span class="chip">${e.term} <span class="pill">${e.type}</span></span>`).join('');
            caeKeywords.innerHTML = (cae.semantic_keywords || []).map(k => `<span class="chip">${k}</span>`).join('');
            const relScore = clamp01(cae.relevance_score || 0);
            caeRelevanceScore.textContent = `${relScore}%`;
            caeRelevanceBar.style.width = `${relScore}%`;
            caeIntent.innerHTML = `<span class="chip good">${cae.context_intent || 'N/A'}</span>`;
        }

        renderCategories(data,url,'');

        // --- POPULATE PSI / SPEED ---
        if(psiData) {
            const mobile=psiData.mobile||{};const desktop=psiData.desktop||{};
            const mScore=clamp01(Math.round(mobile.score??mobile.performance??0));
            const dScore=clamp01(Math.round(desktop.score??desktop.performance??0));
            setWheel(ringMobile, numMobile, mwMobile, mScore, 'M');
            setWheel(ringDesktop, numDesktop, mwDesktop, dScore, 'D');

            const pick=(...vals)=>{for(const v of vals){const n=Number(v);if(v!==undefined&&v!==null&&!Number.isNaN(n))return n}return null};
            const lcpSeconds=(()=>{const sec=pick(mobile.lcp_s,desktop.lcp_s,psiData.lcp_s,psiData.metrics?.lcp_s);if(sec!==null)return sec;const ms=pick(mobile.lcp,desktop.lcp,psiData.lcp,psiData.metrics?.lcp);return ms!==null?ms/1000:null})();
            const cls=pick(mobile.cls,desktop.cls,psiData.cls,psiData.metrics?.cls);
            const inp=pick(mobile.inp_ms,desktop.inp_ms,psiData.inp_ms,psiData.metrics?.inp_ms,mobile.inp,desktop.inp,psiData.inp);
            const ttfb=pick(mobile.ttfb_ms,desktop.ttfb_ms,psiData.ttfb_ms,psiData.metrics?.ttfb_ms,psiData.ttfb);

            const sLCP=scoreFromBounds(lcpSeconds,2.5,6.0);
            const sCLS=scoreFromBounds(cls,0.10,0.25);
            const sINP=scoreFromBounds(inp,200,500);
            const sTTFB=scoreFromBounds(ttfb,800,1800);

            setSpMeter(lcpBar,lcpVal,lcpSeconds,sLCP,v=>v!=null?`${v.toFixed(2)} s`:'—',lcpMeter);
            setSpMeter(clsBar,clsVal,cls,sCLS,v=>v!=null?`${v.toFixed(3)}`:'—',clsMeter);
            setSpMeter(inpBar,inpVal,inp,sINP,v=>v!=null?`${Math.round(v)} ms`:'—',inpMeter);
            setSpMeter(ttfbBar,ttfbVal,ttfb,sTTFB,v=>v!=null?`${Math.round(v)} ms`:'—',ttfbMeter);

            const tips=[];
            if(lcpSeconds!=null&&lcpSeconds>2.5)tips.push('Improve LCP: preload hero image, compress images (AVIF/WebP), inline critical CSS.');
            if(cls!=null&&cls>0.1)tips.push('Reduce CLS: always set width/height on images/media; avoid layout shifts from ads and embeds.');
            if(inp!=null&&inp>200)tips.push('Lower INP: break up long tasks, defer non-critical JS, reduce third-party scripts.');
            if(ttfb!=null&&ttfb>800)tips.push('Reduce TTFB: enable caching/CDN, optimize server, use HTTP/2 or HTTP/3.');
            if(!tips.length){tips.push('Great job! Keep images optimized and JS lean to maintain fast performance.')}
            psiFixes.innerHTML=tips.map(t=>`<li>✅ ${t}</li>`).join('');

            const topBand=(mScore>=80&&dScore>=80)?'good':((mScore>=60||dScore>=60)?'warn':'bad');
            psiStatus.className='pill '+(topBand==='good'?'score-pill--green':topBand==='warn'?'score-pill--orange':'score-pill--red');
            psiStatus.textContent=topBand==='good'?'🎉 Excellent Speed':topBand==='warn'?'OK':'Needs Work';
        }
      }catch(err){
        console.error(err);
        showError('Analyze failed.',String(err.message||err));
      }finally{
        setRunning(false);
      }
    });

    // Utility buttons
    pasteBtn && pasteBtn.addEventListener('click', async e => { e.preventDefault(); try{const t=await navigator.clipboard.readText(); if(t) urlInput.value=t.trim();}catch{} });
    importBtn && importBtn.addEventListener('click',()=>importFile.click());
    importFile && importFile.addEventListener('change',e=>{const f=e.target.files?.[0];if(!f)return;const r=new FileReader();r.onload=()=>{try{const j=JSON.parse(String(r.result||'{}'));if(j.url)urlInput.value=j.url;alert('Imported JSON. Click Analyze to run.')}catch{alert('Invalid JSON file.')}};r.readAsText(f)});
    printBtn && printBtn.addEventListener('click',()=>window.print());
    resetBtn && resetBtn.addEventListener('click',()=>location.reload());
    exportBtn && exportBtn.addEventListener('click',()=>{if(!window.__lastData){alert('Run an analysis first.');return;}const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='semantic-report.json';a.click();URL.revokeObjectURL(a.href)});
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else { init(); }
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
    <svg class="animated-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a10 10 0 1 0 10 10A10 10 0 0 0 12 2Z"></path><path d="M12 18a6 6 0 1 0 0-12 6 6 0 0 0 0 12Z"></path></svg>
    <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Content Optimization</h3>
    <svg class="animated-icon pulse" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
  </div>

<div class="co-card" id="contentOptimizationCard">
    <div class="co-grid">
      <!-- Content Optimization Score Wheel -->
      <div style="display:grid;place-items:center;padding:10px">
        <div class="mw warn" id="mwContent">
          <div class="mw-ring" id="ringContent" style="--v:0"></div>
          <div class="mw-center" id="numContent">0%</div>
        </div>
      </div>

      <div class="co-info-grid">
        <div class="co-info-item">
          <div class="co-info-header">
            <div class="co-info-icon"><svg viewBox="0 0 24 24"><path fill="currentColor" d="M12 22a10 10 0 1 0-8.49-4.88"></path></svg></div>
            <span class="co-info-title">Topic Coverage</span>
          </div>
          <p id="coTopicCoverageText">Run analysis to get data.</p>
          <div class="progress"><span id="coTopicCoverageProgress" style="width:0%;"></span></div>
        </div>

        <div class="co-info-item">
          <div class="co-info-header">
            <div class="co-info-icon"><svg viewBox="0 0 24 24"><path fill="currentColor" d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7..."></path></svg></div>
            <span class="co-info-title">Content Gaps</span>
          </div>
          <div class="co-tags" id="coContentGapsTags"></div>
        </div>
    
        <div class="co-info-item">
          <div class="co-info-header">
            <div class="co-info-icon"><svg viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"></polyline></svg></div>
            <span class="co-info-title">Schema Suggestions</span>
          </div>
         <div class="co-tags" id="coSchemaTags"></div>
      </div>
    
      <div class="co-info-item">
        <div class="co-info-header">
          <div class="co-info-icon"><svg viewBox="0 0 24 24"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path></svg></div>
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
    <svg class="animated-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 20.94c1.5 0 2.75 1.25 2.75 2.75S13.5 26.44 12 26.44 9.25 25.19 9.25 23.69s1.25-2.75 2.75-2.75z"></path><path d="M12 2.56c1.5 0 2.75 1.25 2.75 2.75S13.5 8.06 12 8.06 9.25 6.81 9.25 5.31s1.25-2.75 2.75-2.75z"></path></svg>
    <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Content Analysis Engine</h3>
    <svg class="animated-icon pulse" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M4.21 17.5c1.1 0 2 0.9 2 2s-0.9 2-2 2-2-0.9-2-2 0.9-2 2-2z"></path><path d="M19.79 6.5c1.1 0 2 0.9 2 2s-0.9 2-2 2-2-0.9-2-2 0.9-2 2-2z"></path></svg>
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
          <div class="cae-info-header"><div class="cae-info-icon">🧩</div><span class="cae-info-title">Topic Clustering Analysis</span></div>
          <div class="cae-tags" id="caeTopicClusters"></div>
        </div>
        <div class="cae-info-item">
          <div class="cae-info-header"><div class="cae-info-icon">🏢</div><span class="cae-info-title">Entity Recognition</span></div>
          <div class="cae-tags" id="caeEntities"></div>
        </div>
        <div class="cae-info-item">
          <div class="cae-info-header"><div class="cae-info-icon">🔍</div><span class="cae-info-title">Semantic Keyword Discovery</span></div>
          <div class="cae-tags" id="caeKeywords"></div>
        </div>
        <div class="cae-info-item">
          <div class="cae-info-header"><div class="cae-info-icon">🎯</div><span class="cae-info-title">Content Relevance & Intent</span></div>
          <div class="sp-row"><div>Relevance Score</div><div class="sp-val" id="caeRelevanceScore">—</div></div>
          <div class="cae-relevance-bar"><span id="caeRelevanceBar" style="width:0%"></span></div>
          <div id="caeIntent" style="margin-top:8px"></div>
        </div>
      </div>
    </div>
</div>

<!-- Technical SEO Integration -->
<div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-top:24px;">
    <svg class="animated-icon pulse" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m18 16 4-4-4-4"></path><path d="m6 8-4 4 4 4"></path><path d="m14.5 4-5 16"></path></svg>
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
            <div class="tsi-info-header"><div class="tsi-info-icon animated-icon">🔗</div><span class="tsi-info-title">Internal Linking Optimization</span></div>
            <ul id="tsiInternalLinks"></ul>
        </div>
        <div class="tsi-info-item">
            <div class="tsi-info-header"><div class="tsi-info-icon animated-icon">🌐</div><span class="tsi-info-title">URL Structure Analysis</span></div>
            <p>Clarity Score: <strong id="tsiUrlClarityScore">—</strong></p>
            <p id="tsiUrlSuggestion"></p>
        </div>
        <div class="tsi-info-item" style="grid-column: span 2;">
            <div class="tsi-info-header"><div class="tsi-info-icon animated-icon pulse">📰</div><span class="tsi-info-title">Meta Tags Optimization</span></div>
            <p><strong>Title:</strong> <span id="tsiMetaTitle">—</span></p>
            <p><strong>Description:</strong> <span id="tsiMetaDescription">—</span></p>
        </div>
        <div class="tsi-info-item">
            <div class="tsi-info-header"><div class="tsi-info-icon animated-icon">🖼️</div><span class="tsi-info-title">Image Alt Text Suggestions</span></div>
            <ul id="tsiAltTexts"></ul>
        </div>
        <div class="tsi-info-item">
            <div class="tsi-info-header"><div class="tsi-info-icon animated-icon pulse">🗺️</div><span class="tsi-info-title">Site Structure Mapping</span></div>
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
    <svg class="animated-icon" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22a10 10 0 1 0-8.49-4.88"></path><path d="M12 2a10 10 0 1 0 8.49 4.88"></path></svg>
    <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Keyword Intelligence</h3>
</div>
<div class="ki-card" id="keywordIntelligenceCard">
    <div class="ki-grid">
        <div class="ki-item">
            <h4 class="ki-item-title"><span class="animated-icon pulse">🧠</span>Semantic Keyword Research</h4>
            <div id="kiSemanticResearch" class="ki-tags"></div>
        </div>
        <div class="ki-item">
            <h4 class="ki-item-title"><span class="animated-icon">🎯</span>Keyword Intent Classification</h4>
            <div id="kiIntentClassification" class="ki-tags"></div>
        </div>
        <div class="ki-item">
            <h4 class="ki-item-title"><span class="animated-icon pulse">🗺️</span>Related Terms Mapping</h4>
            <div id="kiRelatedTerms" class="ki-tags"></div>
        </div>
        <div class="ki-item">
            <h4 class="ki-item-title"><span class="animated-icon">📊</span>Competitor Keyword Gap Analysis</h4>
            <div id="kiCompetitorGaps" class="ki-list"></div>
        </div>
        <div class="ki-item" style="grid-column: span 2;">
             <h4 class="ki-item-title"><span class="animated-icon pulse">🔑</span>Long-tail Semantic Suggestions</h4>
            <div id="kiLongTail" class="ki-list"></div>
        </div>
    </div>
</div>

<!-- Meta Info Layout -->
<div class="card meta-card" style="margin-top:24px;">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;">
        <svg class="animated-icon pulse" width="24" height="24" viewBox="0 0 24 24"><path fill="currentColor" d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.72"></path></svg>
        <h3 class="t-grad">Meta & Heading Structure</h3>
    </div>
    <div class="space-y-3">
        <div class="cat-card">
            <div class="cat-card-title">Title Tag</div>
            <div id="titleVal"></div>
        </div>
        <div class="cat-card">
            <div class="cat-card-title">Meta Description</div>
            <div id="metaVal"></div>
        </div>
        <div class="cat-card">
            <div class="cat-card-title">Heading Map (H1-H4)</div>
            <div id="headingMap" class="space-y-2"></div>
        </div>
    </div>
</div>

<!-- Site Speed -->
<div class="speed-card" id="speedCard">
    <div class="sp-head">
      <div class="sp-title">
        <div class="ico">⚡</div>
        <div>
          <div class="t-grad">Site Speed & Core Web Vitals</div>
          <div class="sp-note">Uses PageSpeed Insights (Mobile + Desktop)</div>
        </div>
      </div>
      <span id="psiStatus" class="pill">Waiting…</span>
    </div>
    <div class="sp-wheels">
      <div class="wheel-card">
        <div class="mw mw-sm warn" id="mwMobile"><div class="mw-ring" id="ringMobile"></div><div class="mw-center" id="numMobile">M 0%</div></div>
        <div class="wheel-label">Mobile</div>
      </div>
      <div class="wheel-card">
        <div class="mw mw-sm warn" id="mwDesktop"><div class="mw-ring" id="ringDesktop"></div><div class="mw-center" id="numDesktop">D 0%</div></div>
        <div class="wheel-label">Desktop</div>
      </div>
    </div>
    <div class="sp-bars-grid">
      <div class="sp-tile"><div class="sp-row"><div>🏁 LCP</div><div class="sp-val" id="lcpVal"></div></div><div class="sp-meter" id="lcpMeter"><span id="lcpBar"></span></div></div>
      <div class="sp-tile"><div class="sp-row"><div>📦 CLS</div><div class="sp-val" id="clsVal"></div></div><div class="sp-meter" id="clsMeter"><span id="clsBar"></span></div></div>
      <div class="sp-tile"><div class="sp-row"><div>⚡ INP</div><div class="sp-val" id="inpVal"></div></div><div class="sp-meter" id="inpMeter"><span id="inpBar"></span></div></div>
      <div class="sp-tile"><div class="sp-row"><div>⏱️ TTFB</div><div class="sp-val" id="ttfbVal"></div></div><div class="sp-meter" id="ttfbMeter"><span id="ttfbBar"></span></div></div>
    </div>
    <div class="sp-fixes">
      <h4>💡 Speed Suggestions</h4>
      <ul id="psiFixes"></ul>
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
