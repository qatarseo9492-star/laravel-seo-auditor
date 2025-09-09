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
    --outline: #ffffff1a;
    

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

  .card{border-radius:18px;padding:18px;background:var(--card);border:1px solid var(--outline);box-shadow:0 10px 30px rgba(0,0,0,.35)}
  .cat-card{border-radius:16px;padding:16px;background:var(--card-2);border:1px solid var(--outline)}
  .ground-slab{border-radius:22px;padding:20px;background:#1B1B1B;border:1px solid var(--outline);margin-top:20px;box-shadow:0 10px 40px rgba(0,0,0,.4)}

  .pill{padding:5px 10px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid #ffffff29;background:#ffffff14;color:var(--ink)}
  .chip{padding:6px 8px;border-radius:12px;font-weight:800;display:inline-flex;align-items:center;gap:6px;border:1px solid #ffffff24;color:#eef2ff;font-size:12px;background:#171717}
  .chip i{font-style:normal}
  .chip.good{background:linear-gradient(135deg,#0f2d1f,#0d3b2a);border-color:#22c55e72;box-shadow:0 0 24px rgba(34,197,94,.25)}
  .chip.warn{background:linear-gradient(135deg,#2a1f06,#3f2c07);border-color:#f59e0b72;box-shadow:0 0 24px rgba(245,158,11,.2)}
  .chip.bad{background:linear-gradient(135deg,#2e1010,#4a1616);border-color:#ef444472;box-shadow:0 0 24px rgba(239,68,68,.2)}

  .btn{padding:10px 14px;border-radius:12px;font-weight:900;border:1px solid #ffffff22;color:#0b1020;font-size:13px; cursor: pointer; transition: all .2s ease;}
  .btn:hover{filter: brightness(1.2); transform: translateY(-1px);}
  .btn:active{transform: translateY(0px);}
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
  
  /* ===================== */
  /* === Icon Upgrades === */
  /* ===================== */
  .c-icon {
    width: 24px;
    height: 24px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }
  .c-icon.spin { animation: spin 10s linear infinite; }
  .c-icon.float { animation: bob 4s ease-in-out infinite; }
  .c-icon.pulse { animation: pulse 2.5s ease-in-out infinite; }
  
  @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
  @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.08); opacity: 0.85; } }
  
  .tsi-grid, .cae-grid {display:grid;grid-template-columns: 240px 1fr;gap: 16px;align-items: flex-start;}
  @media (max-width: 920px){.tsi-grid, .cae-grid {grid-template-columns:1fr}}

  /* Info Items Grid */
  .tsi-info-grid, .cae-info-grid {display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
  @media (max-width:500px){.tsi-info-grid, .cae-info-grid {grid-template-columns:1fr}}
  .tsi-info-item, .cae-info-item {
    border-radius:14px;
    padding:14px;
    background:#1E1E1E;
    border:1px solid var(--outline);
    box-shadow:0 8px 24px rgba(0,0,0,.3);
  }
  .tsi-info-icon, .cae-info-icon {width:32px;height:32px;display:grid;place-items:center;border-radius:8px;background:linear-gradient(135deg,#23234a,#182e3a);border:1px solid #2e2e2e}
  .tsi-info-icon svg, .cae-info-icon svg {width:18px;height:18px}
  .tsi-info-header, .cae-info-header {display:flex;align-items:center;gap:10px;margin-bottom:8px}
  .tsi-info-title, .cae-info-title {font-weight:800;color:var(--ink)}
  .tsi-info-item p, .cae-info-item p {font-size:12px;color:#aab3c2;margin:0 0 10px}
  .tsi-tags, .cae-tags {display:flex;flex-wrap:wrap;gap:6px}
  
  .tsi-info-item ul { list-style: none; padding-left: 0; margin: 0; display: flex; flex-direction: column; gap: 8px; }
  .tsi-info-item li { font-size: 13px; line-height: 1.5; }
  .tsi-info-item code { background: #111; padding: 2px 6px; border-radius: 6px; font-size: 12px; color: var(--pink-1); }

  .site-map-container { background: #111; border-radius: 12px; padding: 12px; font-size: 12px; line-height: 1.6; max-height: 200px; overflow-y: auto;}
  .site-map-container ul { padding-left: 16px; margin: 0; }
  .site-map-container li { list-style-type: '— '; }

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

  /* =================================================== */
  /* === 🤖 NEW STYLES for AI-Powered Features 🤖 === */
  /* =================================================== */
  .ai-card {
    background: linear-gradient(145deg, #0c212b, #0c1a2e);
    border: 1px solid var(--blue-1);
    border-radius: 20px;
    padding: 16px;
    margin-top: 24px;
    box-shadow: 0 0 32px rgba(0, 198, 255, 0.3), inset 0 0 12px rgba(0, 0, 0, 0.5);
  }
  .ai-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 16px;
  }
  .ai-item {
    background: rgba(5, 20, 25, 0.7);
    border: 1px solid rgba(0, 198, 255, 0.3);
    border-radius: 14px;
    padding: 14px;
    backdrop-filter: blur(4px);
    display: flex; flex-direction: column; gap: 10px;
  }
  .ai-item-title {
    font-weight: 800; font-size: 15px;
    background: linear-gradient(90deg, var(--blue-1), var(--green-1));
    -webkit-background-clip: text; background-clip: text; color: transparent;
    margin-bottom: 4px; display:flex; align-items:center; gap: 8px;
  }
  .ai-item p {
      font-size: 13px; color: #c3e6ff; margin: 0;
  }
  .ai-input-row { display: flex; gap: 8px; margin-top: auto; }
  .ai-input-row input {
      flex-grow: 1;
      background: #081220;
      border: 1px solid #1c3d52;
      border-radius: 10px;
      padding: 8px 12px;
      color: var(--ink);
      font-size: 13px;
  }
   .ai-input-row input:focus { outline: none; border-color: var(--blue-1); box-shadow: 0 0 8px var(--blue-1);}
  .ai-result-box {
      background: #081220;
      border: 1px solid #1c3d52;
      border-radius: 10px;
      padding: 12px;
      font-size: 13px;
      color: #e0f2fe;
      min-height: 80px;
      white-space: pre-wrap;
      overflow-y: auto;
  }

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
  
  /* ADDED FOR UPGRADE */
  .upgraded-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 12px;
      margin-top: 16px;
      border-top: 1px solid var(--outline);
      padding-top: 16px;
  }

  .onpage-card {
      background: linear-gradient(145deg, #0c212b, #0c1a2e);
      border: 1px solid var(--blue-1);
      border-radius: 20px;
      padding: 16px;
      margin-top: 24px;
      box-shadow: 0 0 32px rgba(0, 198, 255, 0.3), inset 0 0 12px rgba(0, 0, 0, 0.5);
  }
  .onpage-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 12px;
  }
  .onpage-item {
      background: rgba(5, 20, 25, 0.7);
      border: 1px solid rgba(0, 198, 255, 0.3);
      border-radius: 14px;
      padding: 14px;
  }
  .onpage-item-title {
      font-weight: 800;
      font-size: 15px;
      background: linear-gradient(90deg, var(--blue-1), var(--green-1));
      -webkit-background-clip: text;
      background-clip: text;
      color: transparent;
      margin-bottom: 12px;
      display: flex;
      align-items: center;
      gap: 8px;
  }

  /* ============================================================ */
  /* === ✨ NEW STYLES for AI Readability & Humanizer ✨ === */
  /* ============================================================ */
  #aiCheckResult {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    gap: 8px;
    height: 295px; /* Match textarea + button height */
    transition: background-color .4s ease;
  }
  .ai-result-header {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
  }
  .badge-icon-wrapper {
    width: 64px;
    height: 64px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .badge-icon {
    width: 100%;
    height: 100%;
    animation: badge-pop-in .5s cubic-bezier(0.25, 1, 0.5, 1) forwards;
  }
  @keyframes badge-pop-in {
    from { transform: scale(0.5); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
  }
  .badge-icon .glow {
    animation: badge-glow 3s ease-in-out infinite;
  }
  .badge-icon .spin-slow {
      animation: spin 20s linear infinite;
  }
  @keyframes badge-glow {
    0%, 100% { filter: brightness(1); }
    50% { filter: brightness(1.3); }
  }

  .score-display {
    text-align: center;
  }
  .score-display strong {
    font-size: 14px;
    color: var(--sub);
  }
  .score-display .score-value {
    font-size: 28px;
    font-weight: 900;
    line-height: 1;
    display: block;
    margin-top: 4px;
    animation: score-slide-in .6s .2s cubic-bezier(0.25, 1, 0.5, 1) backwards;
  }
  @keyframes score-slide-in {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
  }
  .score-value.success { color: var(--green-1); text-shadow: 0 0 12px var(--green-1);}
  .score-value.warning { color: var(--yellow-1); text-shadow: 0 0 12px var(--yellow-1);}
  .score-value.danger { color: var(--red-1); text-shadow: 0 0 12px var(--red-1);}

  .recommendation {
    font-size: 14px;
    color: var(--ink);
    margin: 12px 0;
    max-width: 90%;
    animation: fade-in .6s .4s backwards;
  }

  #aiCheckResult .suggestions-wrapper {
    text-align: left;
    width: 100%;
    overflow-y: auto;
    padding: 0 8px;
  }
  
  @keyframes fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
  }
  
</style>

<script defer>
/* This is the complete, combined script. It includes all of your original logic
   plus the new logic for the 16 upgraded features, all integrated correctly. */
(function(){
  const init = () => {
    const $ = s=>document.querySelector(s);

    /* ============== Element refs (Original + New) ============== */
    const mw=$('#mw'), mwRing=$('#mwRing'), mwNum=$('#mwNum');
    const overallBar=$('#overallBar'), overallFill=$('#overallFill'), overallPct=$('#overallPct');
    const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'), chipHuman=$('#chipHuman'), chipAI=$('#chipAI');

    const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'), pasteBtn=$('#pasteBtn'),
          importBtn=$('#importBtn'), importFile=$('#importFile'), printBtn=$('#printBtn'),
          resetBtn=$('#resetBtn'), exportBtn=$('#exportBtn');

    const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap');
    
    const chipHttp=$('#chipHttp'), chipCanon=$('#chipCanon'), chipRobots=$('#chipRobots'), chipViewport=$('#chipViewport'),
          chipH=$('#chipH'), chipIntChip=$('#chipInt'), chipSchema=$('#chipSchema'), chipAuto=$('#chipAuto');

    const errorBox = $('#errorBox');
    const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
          mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
          mTips=$('#improveTips'), mLink=$('#improveSearch');

    const speedOverviewBar = $('#speedOverviewBar'), speedOverviewText = $('#speedOverviewText');
    const mobileScoreVal = $('#mobileScoreVal'), mobileScoreCircle = $('#mobileScoreCircle');
    const desktopScoreVal = $('#desktopScoreVal'), desktopScoreCircle = $('#desktopScoreCircle');
    const mobileLcp = $('#mobileLcp'), mobileInp = $('#mobileInp'), mobileCls = $('#mobileCls');
    const desktopLcp = $('#desktopLcp'), desktopInp = $('#desktopInp'), desktopCls = $('#desktopCls');
    const speedOpportunitiesList = $('#speedOpportunitiesList');
    
    const mwTSI = $('#mwTSI'), ringTSI = $('#ringTSI'), numTSI = $('#numTSI');
    const tsiInternalLinks = $('#tsiInternalLinks'), tsiUrlClarityScore = $('#tsiUrlClarityScore');
    const tsiUrlSuggestion = $('#tsiUrlSuggestion'), tsiMetaTitle = $('#tsiMetaTitle');
    const tsiMetaDescription = $('#tsiMetaDescription'), tsiAltTexts = $('#tsiAltTexts');
    const tsiSiteMap = $('#tsiSiteMap'), tsiSuggestionsList = $('#tsiSuggestionsList');
    
    const kiSemanticResearch = $('#kiSemanticResearch'), kiIntentClassification = $('#kiIntentClassification');
    const kiRelatedTerms = $('#kiRelatedTerms'), kiCompetitorGaps = $('#kiCompetitorGaps');
    const kiLongTail = $('#kiLongTail');

    const mwCAE = $('#mwCAE'), ringCAE = $('#ringCAE'), numCAE = $('#numCAE');
    const caeTopicClusters = $('#caeTopicClusters'), caeEntities = $('#caeEntities');
    const caeKeywords = $('#caeKeywords'), caeRelevanceScore = $('#caeRelevanceScore');
    const caeRelevanceBar = $('#caeRelevanceBar'), caeIntent = $('#caeIntent');
    
    const aiBriefInput = $('#aiBriefInput'), aiBriefBtn = $('#aiBriefBtn'), aiBriefResult = $('#aiBriefResult');
    
    const aiCheckTextarea = $('#aiCheckTextarea'), aiCheckBtn = $('#aiCheckBtn'), aiCheckResult = $('#aiCheckResult');


    /* Helpers (Unchanged) */
    const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
    const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
    const bandIcon=s=>s>=80?'✅':(s>=60?'🟧':'🔴');
    function setChip(el,label,value,score){ if(!el)return; el.classList.remove('good','warn','bad'); const b=bandName(score); el.classList.add(b); el.innerHTML=`<i>${bandIcon(score)}</i><span>${label}: ${value}</span>`; };
    const showError=(msg,detail)=>{ errorBox.style.display='block'; errorBox.textContent=msg+(detail?`: ${detail}`:''); };
    const clearError=()=>{ errorBox.style.display='none'; errorBox.textContent=''; };
    
    const handleApiError = (toolName, error) => {
        let message = error.message || 'An unknown error occurred.';
        const jsonStringMatch = message.match(/(\{.*\})/);
        if (jsonStringMatch && jsonStringMatch[1]) {
            try {
                const errorJson = JSON.parse(jsonStringMatch[1]);
                message = errorJson.error || errorJson.message || jsonStringMatch[1];
            } catch (e) { /* Not valid JSON */ }
        }
        showError(`${toolName} analysis failed`, message);
        return null;
    };

    const CATS=[{name:'User Signals & Experience',icon:'📱',checks:['Mobile-friendly, responsive layout','Optimized speed (compression, lazy-load)','Core Web Vitals passing (LCP/INP/CLS)','Clear CTAs and next steps','Accessible basics (alt text, contrast)']},{name:'Entities & Context',icon:'🧩',checks:['sameAs/Organization details present','Valid schema markup (Article/FAQ/Product)','Related entities covered with context','Primary entity clearly defined','Organization contact/about page visible']},{name:'Structure & Architecture',icon:'🏗️',checks:['Logical H2/H3 headings & topic clusters','Internal links to hub/related pages','Clean, descriptive URL slug','Breadcrumbs enabled (+ schema)','XML sitemap logical structure']},{name:'Content Quality',icon:'🧠',checks:['E-E-A-T signals (author, date, expertise)','Unique value vs. top competitors','Facts & citations up to date','Helpful media (images/video) w/ captions','Up-to-date examples & screenshots']},{name:'Content & Keywords',icon:'📝',checks:['Define search intent & primary topic','Map target & related keywords (synonyms/PAA)','H1 includes primary topic naturally','Integrate FAQs / questions with answers','Readable, NLP-friendly language']},{name:'Technical Elements',icon:'⚙️',checks:['Title tag (≈50–60 chars) w/ primary keyword','Meta description (≈140–160 chars) + CTA','Canonical tag set correctly','Indexable & listed in XML sitemap','Robots directives valid']}];
    const KB={'Mobile-friendly, responsive layout':{why:'Most traffic is mobile; poor UX kills engagement.',tips:['Responsive breakpoints & fluid grids.','Tap targets ≥44px.','Avoid horizontal scroll.'],link:'https://search.google.com/test/mobile-friendly'},'Optimized speed (compression, lazy-load)':{why:'Speed affects abandonment and CWV.',tips:['Use WebP/AVIF.','HTTP/2 + CDN caching.','Lazy-load below-the-fold.'],link:'https://web.dev/fast/'},'Core Web Vitals passing (LCP/INP/CLS)':{why:'Passing CWV improves experience & stability.',tips:['Preload hero image.','Minimize long JS tasks.','Reserve media space.'],link:'https://web.dev/vitals/'},'Clear CTAs and next steps':{why:'Clarity increases conversions and task completion.',tips:['One primary CTA per view.','Action verbs + benefit.','Explain what happens next.'],link:'https://www.nngroup.com/articles/call-to-action-buttons/'},'Accessible basics (alt text, contrast)':{why:'Accessibility broadens reach and reduces risk.',tips:['Alt text on images.','Contrast ratio ≥4.5:1.','Keyboard focus states.'],link:'https://www.w3.org/WAI/standards-guidelines/wcag/'},'sameAs/Organization details present':{why:'Entity grounding disambiguates your brand.',tips:['Organization JSON-LD.','sameAs links to profiles.','NAP consistency.'],link:'https://schema.org/Organization'},'Valid schema markup (Article/FAQ/Product)':{why:'Structured data unlocks rich results.',tips:['Validate with Rich Results Test.','Mark up visible content only.','Keep to supported types.'],link:'https://search.google.com/test/rich-results'},'Related entities covered with context':{why:'Covering related entities builds topical depth.',tips:['Mention related concepts.','Explain relationships.','Link to references.'],link:'https://developers.google.com/knowledge-graph'},'Primary entity clearly defined':{why:'A single main entity clarifies page purpose.',tips:['Define at the top.','Use consistent naming.','Add schema about it.'],link:'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data'},'Organization contact/about page visible':{why:'Trust & contact clarity support E-E-A-T.',tips:['Add /about and /contact.','Link from header/footer.','Show address & email.'],link:'https://developers.google.com/search/docs/fundamentals/creating-helpful-content'},'Logical H2/H3 headings & topic clusters':{why:'Hierarchy helps skimming and indexing.',tips:['Group subtopics under H2.','Use H3 for steps/examples.','Keep sections concise.'],link:'https://moz.com/learn/seo/site-structure'},'Internal links to hub/related pages':{why:'Internal links distribute authority & context.',tips:['Link to 3–5 relevant hubs.','Descriptive anchors.','Further reading section.'],link:'https://ahrefs.com/blog/internal-links/'},'Clean, descriptive URL slug':{why:'Readable slugs improve CTR & clarity.',tips:['3–5 meaningful words.','Hyphens & lowercase.','Avoid query strings.'],link:'https://developers.google.com/search/docs/crawling-indexing/url-structure'},'Breadcrumbs enabled (+ schema)':{why:'Breadcrumbs clarify location & show in SERP.',tips:['Visible breadcrumbs.','BreadcrumbList JSON-LD.','Keep depth logical.'],link:'https://developers.google.com/search/docs/appearance/structured-data/breadcrumb'},'XML sitemap logical structure':{why:'Sitemap accelerates discovery & updates.',tips:['Include canonical URLs.','Segment large sites.','Reference in robots.txt.'],link:'https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview'},'E-E-A-T signals (author, date, expertise)':{why:'Trust signals reduce bounce & build credibility.',tips:['Author bio + credentials.','Last updated date.','Editorial policy page.'],link:'https://developers.google.com/search/blog/2022/08/helpful-content-update'},'Unique value vs. top competitors':{why:'Differentiation is necessary to rank & retain.',tips:['Original data/examples.','Pros/cons & criteria.','Why your approach is better.'],link:'https://backlinko.com/seo-techniques'},'Facts & citations up to date':{why:'Freshness + accuracy boosts trust.',tips:['Cite primary sources.','Update stats ≤12 months.','Prefer canonical/DOI links.'],link:'https://scholar.google.com/'},'Helpful media (images/video) w/ captions':{why:'Media improves comprehension & dwell time.',tips:['Add 3–6 figures.','Descriptive captions.','Compress + lazy-load.'],link:'https://web.dev/optimize-lcp/'},'Up-to-date examples & screenshots':{why:'Current visuals reflect product reality.',tips:['Refresh UI shots.','Date your examples.','Replace deprecated flows.'],link:'https://www.nngroup.com/articles/guidelines-for-screenshots/'},'Define search intent & primary topic':{why:'Matching intent drives relevance & time on page.',tips:['State outcome early.','Align format to intent.','Use concrete examples.'],link:'https://ahrefs.com/blog/search-intent/'},'Map target & related keywords (synonyms/PAA)':{why:'Variants improve recall & completeness.',tips:['List 6–12 variants.','5–10 PAA questions.','Answer PAA in 40–60 words.'],link:'https://developers.google.com/search/docs/fundamentals/seo-starter-guide'},'H1 includes primary topic naturally':{why:'Clear topic helps users and algorithms.',tips:['One H1 per page.','Topic near the start.','Be descriptive.'],link:'https://web.dev/learn/html/semantics/#headings'},'Integrate FAQs / questions with answers':{why:'Captures long-tail & can earn rich results.',tips:['Pick 3–6 questions.','Answer briefly.','Add FAQPage JSON-LD.'],link:'https://developers.google.com/search/docs/appearance/structured-data/faqpage'},'Readable, NLP-friendly language':{why:'Plain, direct writing improves comprehension.',tips:['≤20 words/sentence.','Active voice.','Define jargon on first use.'],link:'https://www.plainlanguage.gov/guidelines/'},'Title tag (≈50–60 chars) w/ primary keyword':{why:'Title remains the strongest on-page signal.',tips:['50–60 chars.','Primary topic first.','Avoid duplication.'],link:'https://moz.com/learn/seo/title-tag'},'Meta description (≈140–160 chars) + CTA':{why:'Meta drives CTR which correlates with rankings.',tips:['140–160 chars.','Benefit + CTA.','Match intent.'],link:'https://moz.com/learn/seo/meta-description'},'Canonical tag set correctly':{why:'Avoid duplicates; consolidate signals.',tips:['One canonical.','Absolute URL.','No conflicting canonicals.'],link:'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls'},'Indexable & listed in XML sitemap':{why:'Indexation is prerequisite to ranking.',tips:['No noindex.','Include in sitemap.','Submit in Search Console.'],link:'https://developers.google.com/search/docs/crawling-indexing/overview'},'Robots directives valid':{why:'Avoid accidental noindex/nofollow.',tips:['robots meta allows indexing.','robots.txt not blocking.','Use directives consistently.'],link:'https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag'}};
    function clamp01num(n){return Math.max(0,Math.min(100,Number(n)||0))}
    function scoreChecklist(label,data,url,targetKw=''){const qs=data.quick_stats||{};const cs=data.content_structure||{};const ps=data.page_signals||{};const r=data.readability||{};const h1=(cs.headings&&cs.headings.H1?cs.headings.H1.length:0)||0;const h2=(cs.headings&&cs.headings.H2?cs.headings.H2.length:0)||0;const h3=(cs.headings&&cs.headings.H3?cs.headings.H3.length:0)||0;const title=(cs.title||'');const meta=(cs.meta_description||'');const internal=Number(qs.internal_links||0);const external=Number(qs.external_links||0);const schemaTypes=new Set((data.page_signals?.schema_types)||[]);const robots=(data.page_signals?.robots||'').toLowerCase();const hasFAQ=schemaTypes.has('FAQPage');const hasArticle=schemaTypes.has('Article')||schemaTypes.has('NewsArticle')||schemaTypes.has('BlogPosting');const urlPath=(()=>{try{return new URL(url).pathname;}catch{return '/';}})();const slugScore=(()=>{const hasQuery=url.includes('?');const segs=urlPath.split('/').filter(Boolean);const words=segs.join('-').split('-').filter(Boolean);if(hasQuery)return 55;if(segs.length>6)return 60;if(words.some(w=>w.length>24))return 65;return 85;})();switch(label){case'Mobile-friendly, responsive layout':return ps.has_viewport?88:58;case'Optimized speed (compression, lazy-load)':return 60;case'Core Web Vitals passing (LCP/INP/CLS)':return 60;case'Clear CTAs and next steps':return meta.length>=140&&/learn|get|try|start|buy|sign|download|contact/i.test(meta)?80:60;case'Accessible basics (alt text, contrast)':return (data.images_alt_count||0)>=3?82:((data.images_alt_count||0)>=1?68:48);case'sameAs/Organization details present':return ps.has_org_sameas?90:55;case'Valid schema markup (Article/FAQ/Product)':return (hasArticle||hasFAQ||schemaTypes.has('Product'))?85:(schemaTypes.size>0?70:50);case'Related entities covered with context':return external>=2?72:60;case'Primary entity clearly defined':return ps.has_main_entity?85:(h1>0?72:58);case'Organization contact/about page visible':return 60;case'Logical H2/H3 headings & topic clusters':return (h2>=3&&h3>=2)?85:(h2>=2?70:55);case'Internal links to hub/related pages':return internal>=5?85:(internal>=2?65:45);case'Clean, descriptive URL slug':return slugScore;case'Breadcrumbs enabled (+ schema)':return ps.has_breadcrumbs?85:55;case'XML sitemap logical structure':return 60;case'E-E-A-T signals (author, date, expertise)':return ps.has_org_sameas?75:65;case'Unique value vs. top competitors':return 60;case'Facts & citations up to date':return external>=2?78:58;case'Helpful media (images/video) w/ captions':return (data.images_alt_count||0)>=3?82:58;case'Up-to-date examples & screenshots':return 60;case'Define search intent & primary topic':return (title&&h1>0)?78:60;case'Map target & related keywords (synonyms/PAA)':{const kw=(targetKw||'').trim();if(!kw)return 60;const found=(title.toLowerCase().includes(kw.toLowerCase())||(cs.headings?.H1||[]).join(' || ').toLowerCase().includes(kw.toLowerCase()));return found?80:62}case'H1 includes primary topic naturally':{const kw=(targetKw||'').trim();if(h1===0)return 45;if(!kw)return 72;const found=(cs.headings?.H1||[]).some(h=>h.toLowerCase().includes(kw.toLowerCase()));return found?84:72}case'Integrate FAQs / questions with answers':return hasFAQ?85:(/(faq|questions?)/i.test((cs.headings?.H2||[]).join(' ')+' '+(cs.headings?.H3||[]).join(' '))?70:55);case'Readable, NLP-friendly language':return clamp01num(r.score||0);case'Title tag (≈50–60 chars) w/ primary keyword':{const len=(title||'').length;return (len>=50&&len<=60)?88:(len?68:45)}case'Meta description (≈140–160 chars) + CTA':{const len=(meta||'').length;const hasCTA=/learn|get|try|start|buy|sign|download|contact/i.test(meta||'');return (len>=140&&len<=160)?(hasCTA?90:82):(len?65:48)}case'Canonical tag set correctly':return ps.canonical?85:55;case'Indexable & listed in XML sitemap':return robots.includes('noindex')?20:80;case'Robots directives valid':return (robots&&/(noindex|none)/.test(robots))?45:75;}return 60}
    function renderCategories(data,url,targetKw){const catsEl=document.querySelector('#cats');if(!catsEl)return;catsEl.innerHTML='';let autoGood=0;CATS.forEach(cat=>{const rows=cat.checks.map(lbl=>{const s=scoreChecklist(lbl,data,url,targetKw);const fill=s>=80?'fill-green':(s>=60?'fill-orange':'fill-red');const pill=s>=80?'score-pill--green':s>=60?'score-pill--orange':'score-pill--red';if(s>=80)autoGood++;return {label:lbl,score:s,fill,pill,bandTxt:(s>=80?'Good (≥80)':s>=60?'Needs work (60–79)':'Low (<60)')};});const total=rows.length;const passed=rows.filter(r=>r.score>=80).length;const pct=Math.round((passed/Math.max(1,total))*100);const card=document.createElement('div');card.className='cat-card';card.innerHTML=`<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px"><div style="display:flex;align-items:center;gap:8px"><div class="king" style="width:34px;height:34px">${cat.icon}</div><div><div class="t-grad" style="font-size:16px;font-weight:900">${cat.name}</div><div style="font-size:12px;color:#b6c2cf">Keep improving</div></div></div><div class="pill">${passed} / ${total}</div></div><div class="progress" style="margin-bottom:8px"><span style="width:${pct}%"></span></div><div class="space-y-2" id="list"></div>`;const list=card.querySelector('#list');rows.forEach(row=>{const dot=row.score>=80?'#10b981':row.score>=60?'#f59e0b':'#ef4444';const el=document.createElement('div');el.className='check';el.innerHTML=`<div style="display:flex;align-items:center;gap:8px"><span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:${dot}"></span><div class="font-semibold" style="font-size:13px">${row.label}</div></div><div style="display:flex;align-items:center;gap:6px"><span class="score-pill ${row.pill}">${row.score}</span><button class="improve-btn ${row.fill}" type="button">Improve</button></div>`;el.querySelector('.improve-btn').addEventListener('click',()=>{const kb=KB[row.label]||{why:'This item impacts relevance and UX.',tips:['Aim for ≥80 and re-run the analyzer.'],link:'https://www.google.com'};mTitle.textContent=row.label;mCat.textContent=cat.name;mScore.textContent=row.score;mBand.textContent=row.bandTxt;mBand.className='pill '+(row.score>=80?'score-pill--green':row.score>=60?'score-pill--orange':'score-pill--red');mWhy.textContent=kb.why;mTips.innerHTML='';(kb.tips||[]).forEach(t=>{const li=document.createElement('li');li.textContent=t;mTips.appendChild(li)});mLink.href=kb.link||('https://www.google.com/search?q='+encodeURIComponent(row.label)+' best practices');if(typeof modal.showModal==='function')modal.showModal();else modal.setAttribute('open','')});list.appendChild(el)});catsEl.appendChild(card)});if(chipAuto)chipAuto.textContent=autoGood;}

    /* API Calls */
    async function callApi(endpoint, data) {
        const res = await fetch(endpoint, {
            method: 'POST',
            headers: {'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
            body: JSON.stringify(data)
        });
        if (!res.ok) {
            const errorText = await res.text();
            throw new Error(`API Error at ${endpoint} (${res.status}): ${errorText.slice(0, 800)}`);
        }
        return res.json();
    }
    
    const callAnalyzer = (url) => callApi('/api/semantic-analyze', { url });
    const callTechnicalSeoApi = (url) => callApi('/api/technical-seo-analyze', { url });
    const callKeywordApi = (url) => callApi('/api/keyword-analyze', { url });
    const callContentEngineApi = (url) => callApi('/api/content-engine-analyze', { url });
    const callPSI = (url) => callApi('/semantic-analyzer/psi', { url });
    const callOpenAiApi = (task, prompt, url = null) => callApi('/api/openai-request', { task, prompt, url });

    function setRunning(isOn){if(!analyzeBtn)return;analyzeBtn.disabled=isOn;analyzeBtn.innerHTML=isOn?'<span class="c-icon spin">⚙️</span> Analyzing...':'🔍 Analyze'}
    
    function setWheel(elRing, elNum, container, score, prefix){
        if (!elRing || !elNum || !container) return;
        const b=bandName(score);
        container.classList.remove('good','warn','bad');
        container.classList.add(b);
        elRing.style.setProperty('--v',score);
        elNum.textContent=(prefix?prefix+' ':'')+score+'%';
    }
    
    function setSpeedCircle(circleEl, score) {
        if (!circleEl) return;
        const r = circleEl.r.baseVal.value;
        const circumference = 2 * Math.PI * r;
        const offset = circumference - (score / 100) * circumference;
        circleEl.style.strokeDasharray = `${circumference} ${circumference}`;
        circleEl.style.strokeDashoffset = offset;
        const color = score >= 80 ? 'var(--green-1)' : score >= 60 ? 'var(--yellow-1)' : 'var(--red-1)';
        circleEl.style.stroke = color;
    }


    /* ===== Analyze Button Logic (ORIGINAL + UPGRADED) ===== */
    analyzeBtn?.addEventListener('click', async e=>{
      e.preventDefault();
      clearError();
      const url=(urlInput.value||'').trim();
      if(!url){showError('Please enter a URL.');return;}
      
      try{
        setRunning(true);
        
        // --- Reset UIs (Original) ---
        [kiSemanticResearch, kiIntentClassification, kiRelatedTerms, kiCompetitorGaps, kiLongTail, caeTopicClusters, caeEntities, caeKeywords, caeIntent].forEach(el => {
            if(el) el.innerHTML = '<span class="chip">Analyzing...</span>';
        });

        // --- Fire all ORIGINAL API calls in parallel ---
        const [data, tsiData, kiData, caeData, psiData] = await Promise.all([
            callAnalyzer(url),
            callTechnicalSeoApi(url).catch(err => handleApiError('Technical SEO', err)),
            callKeywordApi(url).catch(err => handleApiError('Keyword Intelligence', err)),
            callContentEngineApi(url).catch(err => handleApiError('Content Engine', err)),
            callPSI(url).catch(err => {
                handleApiError('PageSpeed Insights', err);
                if(speedOverviewText) speedOverviewText.textContent = `⚠️ PageSpeed Insights analysis failed.`;
                return null;
            })
        ]);

        if(!data || data.error) throw new Error(data?.error || 'Local data parsing failed');
        window.__lastData={...data,url};

        const score=clamp01(data.overall_score||0);
        setWheel(mwRing, mwNum, mw, score, '');
        if(overallFill) overallFill.style.width=score+'%';
        if(overallPct) overallPct.textContent=score+'%';
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
        
        if (data.content_structure) {
            if(titleVal) titleVal.textContent = data.content_structure.title || 'Not Found';
            if(metaVal) metaVal.textContent = data.content_structure.meta_description || 'Not Found';
            const hs = data.content_structure.headings || {};
            if(chipH) chipH.textContent = `H1:${(hs.H1||[]).length} • H2:${(hs.H2||[]).length} • H3:${(hs.H3||[]).length}`;
            if(headingMap) {
                headingMap.innerHTML = ''; 
                ['H1', 'H2', 'H3', 'H4'].forEach(lvl => {
                    const arr = hs[lvl];
                    if(!arr || !arr.length) return;
                    const box = document.createElement('div');
                    box.className = 'cat-card'; 
                    box.innerHTML = `<div class="cat-card-title uppercase">${lvl} (${arr.length})</div><div style="display:flex; flex-direction:column; gap: 4px; margin-top: 8px;">` + arr.map(t => `<div style="font-size:13px; line-height:1.4;">• ${t}</div>`).join('') + `</div>`;
                    headingMap.appendChild(box);
                });
            }
        }
        
        if(chipHttp) chipHttp.textContent='200';
        if(chipCanon) chipCanon.textContent=(data.page_signals?.canonical||'—')||'—';
        if(chipRobots) chipRobots.textContent=(data.page_signals?.robots||'—')||'—';
        if(chipViewport) chipViewport.textContent=data.page_signals?.has_viewport?'yes':'—';
        if(chipIntChip) chipIntChip.textContent=data.quick_stats?.internal_links??0;
        if(chipSchema) chipSchema.textContent=(data.page_signals?.schema_types||[]).length;
        
        if (tsiData) {
            const tsi = tsiData;
            setWheel(ringTSI, numTSI, mwTSI, tsi.score || 0, '');
            if(tsiInternalLinks) tsiInternalLinks.innerHTML = (tsi.internal_linking||[]).map(l => `<li>${l.text} with anchor: <code>${l.anchor}</code></li>`).join('') || '<li>No suggestions.</li>';
            if(tsiUrlClarityScore) tsiUrlClarityScore.textContent = `${tsi.url_structure?.clarity_score || 'N/A'}/100`;
            if(tsiUrlSuggestion) tsiUrlSuggestion.textContent = tsi.url_structure?.suggestion || 'N/A';
            if(tsiMetaTitle) tsiMetaTitle.textContent = tsi.meta_optimization?.title || 'N/A';
            if(tsiMetaDescription) tsiMetaDescription.textContent = tsi.meta_optimization?.description || 'N/A';
            if(tsiAltTexts) tsiAltTexts.innerHTML = (tsi.alt_text_suggestions||[]).map(a => `<li><code>${a.image_src}</code> → "${a.suggestion}"</li>`).join('') || '<li>No suggestions.</li>';
            if(tsiSiteMap) tsiSiteMap.innerHTML = tsi.site_structure_map || 'N/A';
            if(tsiSuggestionsList) tsiSuggestionsList.innerHTML = (tsi.suggestions||[]).map(s => `<li class="${s.type}">${s.text}</li>`).join('') || '<li>No suggestions.</li>';
        }
        
        if(kiData) {
            const ki = kiData;
            if(kiSemanticResearch) kiSemanticResearch.innerHTML = (ki.semantic_research||[]).map(k => `<span class="chip">${k}</span>`).join('') || '<span class="chip">No data</span>';
            if(kiIntentClassification) kiIntentClassification.innerHTML = (ki.intent_classification||[]).map(k => `<span class="chip ${k.intent?.toLowerCase().includes('trans') ? 'intent-trans' : (k.intent?.toLowerCase().includes('nav') ? 'intent-nav' : 'intent-info')}">${k.keyword || ''} <i>(${k.intent || 'N/A'})</i></span>`).join('') || '<span class="chip">No data</span>';
            if(kiRelatedTerms) kiRelatedTerms.innerHTML = (ki.related_terms||[]).map(k => `<span class="chip">${k}</span>`).join('') || '<span class="chip">No data</span>';
            if(kiCompetitorGaps) kiCompetitorGaps.innerHTML = (ki.competitor_gaps||[]).map(k => `<div class="ki-list-item">• ${k}</div>`).join('') || '<div class="ki-list-item">No gaps found.</div>';
            if(kiLongTail) kiLongTail.innerHTML = (ki.long_tail_suggestions||[]).map(k => `<div class="ki-list-item">• ${k}</div>`).join('') || '<div class="ki-list-item">No suggestions.</div>';
        }
        
        if(caeData) {
            const cae = caeData;
            setWheel(ringCAE, numCAE, mwCAE, cae.score || 0, '');
            if(caeTopicClusters) caeTopicClusters.innerHTML = (cae.topic_clusters||[]).map(t => `<span class="chip">${t}</span>`).join('');
            if(caeEntities) caeEntities.innerHTML = (cae.entities||[]).map(e => `<span class="chip">${e.term} <span class="pill">${e.type}</span></span>`).join('');
            if(caeKeywords) caeKeywords.innerHTML = (cae.semantic_keywords||[]).map(k => `<span class="chip">${k}</span>`).join('');
            const relScore = clamp01(cae.relevance_score || 0);
            if(caeRelevanceScore) caeRelevanceScore.textContent = `${relScore}%`;
            if(caeRelevanceBar) caeRelevanceBar.style.width = `${relScore}%`;
            if(caeIntent) caeIntent.innerHTML = `<span class="chip good">${cae.context_intent || 'N/A'}</span>`;
        }

        renderCategories(data,url,'');

        if(psiData) {
            const mobile = psiData.mobile || {};
            const desktop = psiData.desktop || {};
            const overallScore = Math.round(((mobile.score || 0) + (desktop.score || 0)) / 2);
            const speedBadge = $('#speedBadge');
            if(speedBadge){ speedBadge.textContent = bandName(overallScore).charAt(0).toUpperCase() + bandName(overallScore).slice(1); speedBadge.className = 'speed-badge ' + bandName(overallScore); }
            if(speedOverviewBar) speedOverviewBar.style.width = overallScore + '%';
            if(speedOverviewText) speedOverviewText.textContent = `Overall performance is ${bandName(overallScore)}. Mobile: ${mobile.score}, Desktop: ${desktop.score}.`;
            if(mobileScoreVal) mobileScoreVal.textContent = mobile.score || 0;
            setSpeedCircle(mobileScoreCircle, mobile.score || 0);
            if(desktopScoreVal) desktopScoreVal.textContent = desktop.score || 0;
            setSpeedCircle(desktopScoreCircle, desktop.score || 0);
            if(mobileLcp) mobileLcp.textContent = mobile.lcp_s ? `${mobile.lcp_s.toFixed(2)}s` : 'N/A';
            if(mobileInp) mobileInp.textContent = mobile.inp_ms ? `${mobile.inp_ms}ms` : 'N/A';
            if(mobileCls) mobileCls.textContent = mobile.cls ? mobile.cls.toFixed(3) : 'N/A';
            if(desktopLcp) desktopLcp.textContent = desktop.lcp_s ? `${desktop.lcp_s.toFixed(2)}s` : 'N/A';
            if(desktopInp) desktopInp.textContent = desktop.inp_ms ? `${desktop.inp_ms}ms` : 'N/A';
            if(desktopCls) desktopCls.textContent = desktop.cls ? desktop.cls.toFixed(3) : 'N/A';
            if(speedOpportunitiesList) speedOpportunitiesList.innerHTML = (psiData.opportunities||[]).length > 0 ? (psiData.opportunities||[]).map(tip => `<li>${tip}</li>`).join('') : '<li>No specific opportunities found. Great job!</li>';
        }
        
        /* ================================================= */
        /* === NEW LOGIC FOR UPGRADED FEATURES ADDED HERE === */
        /* ================================================= */
        const allNewTasks = [
            { task: 'topic_coverage', elementId: 'topicCoverageResult', type: 'text' },
            { task: 'intent_alignment', elementId: 'intentAlignmentResult', type: 'text' },
            { task: 'snippet_readiness', elementId: 'snippetReadinessResult', type: 'text' },
            { task: 'question_mining', elementId: 'questionMiningResult', type: 'text' },
            { task: 'heading_hierarchy', elementId: 'headingHierarchyResult', type: 'text' },
            { task: 'readability_simplification', elementId: 'readabilitySimplificationResult', type: 'text' },
            { task: 'semantic_variants', elementId: 'semanticVariantsResult', type: 'text' },
            { task: 'eeat_signals', elementId: 'eeatSignalsResult', type: 'text' },
            { task: 'internal_links', elementId: 'internalLinksResult', type: 'text' },
            { task: 'tables_checklists', elementId: 'tablesChecklistsResult', type: 'html' },
            { task: 'content_freshness', elementId: 'contentFreshnessResult', type: 'text' },
            { task: 'cannibalization_check', elementId: 'cannibalizationCheckResult', type: 'text' },
            { task: 'ux_impact', elementId: 'uxImpactResult', type: 'text' },
            { task: 'title_meta_rewrite', elementId: 'titleMetaRewriteResult', type: 'json', formatter: data => (data.suggestions || []).map((s, i) => `<p><strong>Option ${i+1}:</strong><br><strong>Title:</strong> ${s.title}<br><strong>Meta:</strong> ${s.meta}</p>`).join('') || 'No suggestions.' },
            { task: 'image_seo', elementId: 'imageSeoResult', type: 'json', formatter: data => { let html = `<p><strong>Hero Image Present:</strong> ${data.hero_image_present ? 'Yes' : 'No'}</p>`; if (data.alt_text_suggestions && data.alt_text_suggestions.length > 0) { html += '<strong>Alt Text Suggestions:</strong><ul>' + data.alt_text_suggestions.map(s => `<li><code>${s.image_src}</code>: "${s.suggestion}"</li>`).join('') + '</ul>'; } return html; }},
            { task: 'schema_picker', elementId: 'schemaPickerResult', type: 'json', formatter: data => data.json_ld ? `<p><strong>Recommended Schema:</strong> ${data.schema_type}</p><pre><code>${JSON.stringify(data.json_ld, null, 2)}</code></pre>` : 'No schema suggestion.' },
        ];
        
        allNewTasks.forEach(item => { const el = $(`#${item.elementId}`); if(el) el.innerHTML = 'Analyzing...'; });

        const newPromises = allNewTasks.map(item => callApi('/api/openai-request', {task: item.task, url}).then(data => ({ ...item, data })).catch(error => ({ ...item, error })));
        const newResults = await Promise.all(newPromises);

        newResults.forEach(result => {
            const el = $(`#${result.elementId}`);
            if (!el) return;
            if (result.error) { el.textContent = `Error: ${result.error.message}`; } 
            else if (result.type === 'json') { el.innerHTML = result.formatter(result.data); }
            else { const content = result.data.content || 'No suggestions found.'; el.innerHTML = result.type === 'html' ? content : content.replace(/</g, "&lt;").replace(/>/g, "&gt;"); }
        });

      }catch(err){
        console.error(err);
        let message = err.message || 'An unknown error occurred.';
        const jsonStringMatch = message.match(/(\{.*\})/);
        if (jsonStringMatch && jsonStringMatch[1]) {
            try {
                const errorJson = JSON.parse(jsonStringMatch[1]);
                message = errorJson.error || errorJson.message || jsonStringMatch[1];
            } catch (e) {
                 const apiErrorMatch = message.match(/API Error at .*?: (.*)/);
                if(apiErrorMatch && apiErrorMatch[1]) { message = apiErrorMatch[1]; }
            }
        }
        showError('A critical error occurred during analysis', message);
      }finally{
        setRunning(false);
      }
    });

    aiBriefBtn?.addEventListener('click', async () => {
        const prompt = aiBriefInput.value.trim(); const url = urlInput.value.trim();
        if (!prompt) { aiBriefResult.textContent = 'Please enter a topic or keyword.'; return; }
        aiBriefBtn.disabled = true; aiBriefBtn.textContent = 'Generating...'; aiBriefResult.textContent = 'AI is crafting your brief...';
        try { const result = await callOpenAiApi('brief', prompt, url); aiBriefResult.textContent = result.content || 'No content returned from AI.';
        } catch (error) { aiBriefResult.textContent = `Error: ${error.message}`;
        } finally { aiBriefBtn.disabled = false; aiBriefBtn.textContent = 'Generate'; }
    });

    aiCheckBtn?.addEventListener('click', async () => {
        const text = aiCheckTextarea.value.trim();
        if (text.length < 50) {
            aiCheckResult.innerHTML = '<p style="color: var(--orange-1);">Please enter at least 50 characters to analyze.</p>';
            return;
        }

        aiCheckBtn.disabled = true;
        aiCheckBtn.innerHTML = '<span class="c-icon spin">🤖</span> Analyzing...';
        aiCheckResult.innerHTML = '<p>Checking AI score and generating suggestions...</p>';

        try {
            const result = await callApi('/api/ai-content-check', { text });
            if (result.error) throw new Error(result.error);

            const { human_score, suggestions, recommendation, badge_type, google_search_url } = result;

            const badgeSvgs = {
                success: `
                    <svg class="badge-icon success" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="glow" d="M32 58C46.3594 58 58 46.3594 58 32C58 17.6406 46.3594 6 32 6C17.6406 6 6 17.6406 6 32C6 46.3594 17.6406 58 32 58Z" stroke="url(#paint0_linear_success)" stroke-width="4"/>
                        <path d="M22 32.5L28.5 39L43 24" stroke="var(--green-1)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        <defs><linearGradient id="paint0_linear_success" x1="32" y1="6" x2="32" y2="58" gradientUnits="userSpaceOnUse"><stop stop-color="${'var(--green-2)'}"/><stop offset="1" stop-color="${'var(--green-1)'}"/></linearGradient></defs>
                    </svg>`,
                warning: `
                    <svg class="badge-icon warning" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="glow" d="M32 58C46.3594 58 58 46.3594 58 32C58 17.6406 46.3594 6 32 6C17.6406 6 6 17.6406 6 32C6 46.3594 17.6406 58 32 58Z" stroke="url(#paint0_linear_warning)" stroke-width="4"/>
                        <path d="M32 23V36" stroke="var(--yellow-1)" stroke-width="4" stroke-linecap="round"/><path d="M32 43V44" stroke="var(--yellow-1)" stroke-width="5" stroke-linecap="round"/>
                        <defs><linearGradient id="paint0_linear_warning" x1="32" y1="6" x2="32" y2="58" gradientUnits="userSpaceOnUse"><stop stop-color="${'var(--orange-1)'}"/><stop offset="1" stop-color="${'var(--yellow-1)'}"/></linearGradient></defs>
                    </svg>`,
                danger: `
                     <svg class="badge-icon danger" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path class="glow" d="M32 58C46.3594 58 58 46.3594 58 32C58 17.6406 46.3594 6 32 6C17.6406 6 6 17.6406 6 32C6 46.3594 17.6406 58 32 58Z" stroke="url(#paint0_linear_danger)" stroke-width="4"/>
                        <path d="M40 24L24 40" stroke="var(--red-1)" stroke-width="4" stroke-linecap="round"/><path d="M24 24L40 40" stroke="var(--red-1)" stroke-width="4" stroke-linecap="round"/>
                        <defs><linearGradient id="paint0_linear_danger" x1="32" y1="6" x2="32" y2="58" gradientUnits="userSpaceOnUse"><stop stop-color="${'var(--pink-1)'}"/><stop offset="1" stop-color="${'var(--red-1)'}"/></linearGradient></defs>
                    </svg>`
            };
            
            let suggestionsHtml = '';
            if (badge_type !== 'success' && suggestions) {
                suggestionsHtml = `
                    <div class="suggestions-wrapper">
                        <h4 style="color: var(--blue-1); margin: 10px 0 8px;">💡 AI Suggestions to Improve:</h4>
                        <div style="white-space: pre-wrap; font-size: 13px; line-height: 1.6;">${suggestions}</div>
                    </div>`;
            }

            let googleLinkHtml = '';
            if (badge_type !== 'success') {
                googleLinkHtml = `<a href="${google_search_url}" target="_blank" class="btn btn-blue" style="margin-top: 15px; display: inline-block;">Search Google for more tips</a>`;
            }

            aiCheckResult.innerHTML = `
                <div class="ai-result-header">
                    <div class="badge-icon-wrapper">${badgeSvgs[badge_type] || ''}</div>
                    <div class="score-display">
                        <strong>Human-like Score</strong>
                        <span class="score-value ${badge_type}">${human_score}%</span>
                    </div>
                </div>
                <p class="recommendation">${recommendation}</p>
                ${suggestionsHtml}
                ${googleLinkHtml}
            `;

        } catch (error) {
            let message = error.message || 'An unknown error occurred.';
            aiCheckResult.innerHTML = `<p style="color: var(--red-1);">Error: ${message}</p>`;
        } finally {
            aiCheckBtn.disabled = false;
            aiCheckBtn.innerHTML = '🤖 Check Content';
        }
    });

    pasteBtn && pasteBtn.addEventListener('click', async e => { e.preventDefault(); try{const t=await navigator.clipboard.readText(); if(t) urlInput.value=t.trim();}catch{} });
    importBtn && importBtn.addEventListener('click',()=>importFile.click());
    importFile && importFile.addEventListener('change',e=>{const f=e.target.files?.[0];if(!f)return;const r=new FileReader();r.onload=()=>{try{const j=JSON.parse(String(r.result||'{}'));if(j.url)urlInput.value=j.url;alert('Imported JSON. Click Analyze to run.')}catch{alert('Invalid JSON file.')}};r.readAsText(f)});
    printBtn && printBtn.addEventListener('click',()=>window.print());
    resetBtn && resetBtn.addEventListener('click',()=>location.reload());
    exportBtn && exportBtn.addEventListener('click',()=>{if(!window.__lastData){alert('Run an analysis first.');return;}const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='semantic-report.json';a.click();URL.revokeObjectURL(a.href)});
  };

  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init, { once: true }); } else { init(); }
})();
</script>
@endpush

@section('content')

<section class="maxw px-4 pb-10">

  <div class="title-wrap">
    <div class="king">
        <svg class="c-icon float" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5 13.18V17.18L12 21L19 17.18V13.18L12 17L5 13.18ZM12 3L19 6.82V10.82L12 15L5 10.82V6.82L12 3Z" stroke="url(#crown-gradient)" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><defs><linearGradient id="crown-gradient" x1="5" y1="3" x2="19" y2="21" gradientUnits="userSpaceOnUse"><stop stop-color="var(--blue-1)"/><stop offset="1" stop-color="var(--pink-1)"/></linearGradient></defs></svg>
    </div>
    <div style="text-align:center">
      <div class="t-grad" style="font-size:26px;line-height:1.1;">Semantic SEO Master Analyzer</div>
      <div class="byline">By <span class="shoail">Shoail Kahoker</span></div>
    </div>
  </div>

  <div class="legend"><span class="badge g">Green ≥ 80</span><span class="badge o">Orange 60–79</span><span class="badge r">Red &lt; 60</span></div>

  <div style="display:grid;grid-template-columns:230px 1fr;gap:16px;align-items:center;margin-top:10px">
    <div style="display:grid;place-items:center;border-radius:16px;padding:8px;">
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
  
  <!-- AI Content Checker & Humanizer -->
    <div class="ai-card" id="aiContentCheckerCard">
        <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:16px;">
            <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">AI Readability & Humanizer</h3>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items: start;">
            <div>
                <textarea id="aiCheckTextarea" placeholder="Paste your text here to check its AI score and get suggestions to make it more human-like... (Min: 50 characters)" style="width: 100%; height: 250px; background: #081220; border: 1px solid #1c3d52; border-radius: 10px; padding: 12px; color: var(--ink); font-size: 13px; resize: vertical;"></textarea>
                <button id="aiCheckBtn" class="btn btn-green" style="width: 100%; margin-top: 10px;">🤖 Check Content</button>
            </div>
            <div>
                <div id="aiCheckResult" class="ai-result-box">
                     <div style="display:flex; flex-direction: column; align-items:center; justify-content:center; height:100%; color: var(--sub); opacity: 0.7;">
                        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 48px; height: 48px; margin-bottom: 8px;"><path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20ZM12 6C11.17 6 10.5 6.67 10.5 7.5C10.5 8.33 11.17 9 12 9C12.83 9 13.5 8.33 13.5 7.5C13.5 6.67 12.83 6 12 6ZM7 12C7 14.76 9.24 17 12 17C14.76 17 17 14.76 17 12H7Z" fill="currentColor"/></svg>
                        Results will appear here...
                     </div>
                </div>
            </div>
        </div>
    </div>

  <!-- Content Analysis Engine (Original Layout with Upgrades) -->
  <div class="cae-card" id="contentAnalysisEngineCard">
    <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:16px;"><h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Content Analysis Engine</h3></div>
    <div class="cae-grid">
      <div style="display:grid;place-items:center;padding:10px"><div class="mw" id="mwCAE"><div class="mw-ring" id="ringCAE" style="--v:0"></div><div class="mw-center" id="numCAE">0%</div></div></div>
      <div class="cae-info-grid">
        <div class="cae-info-item"><div class="cae-info-header"><div class="cae-info-icon"><span class="c-icon spin">🧩</span></div><span class="cae-info-title">Topic Clustering Analysis</span></div><div class="cae-tags" id="caeTopicClusters"></div></div>
        <div class="cae-info-item"><div class="cae-info-header"><div class="cae-info-icon"><span class="c-icon pulse">🏢</span></div><span class="cae-info-title">Entity Recognition</span></div><div class="cae-tags" id="caeEntities"></div></div>
        <div class="cae-info-item"><div class="cae-info-header"><div class="cae-info-icon"><span class="c-icon spin">🔍</span></div><span class="cae-info-title">Semantic Keyword Discovery</span></div><div class="cae-tags" id="caeKeywords"></div></div>
        <div class="cae-info-item"><div class="cae-info-header"><div class="cae-info-icon"><span class="c-icon pulse">🎯</span></div><span class="cae-info-title">Content Relevance & Intent</span></div><div class="cae-relevance-bar"><span id="caeRelevanceBar" style="width:0%"></span></div><div id="caeIntent" style="margin-top:8px"></div></div>
      </div>
    </div>
    {{-- UPGRADED SECTION --}}
    <div class="upgraded-grid">
        <div class="cae-info-item"><div class="cae-info-header"><span class="c-icon spin">🗺️</span><span class="cae-info-title">Topic Coverage & Entity Gaps</span></div><div id="topicCoverageResult" class="ai-result-box"></div></div>
        <div class="cae-info-item"><div class="cae-info-header"><span class="c-icon pulse">🧭</span><span class="cae-info-title">Search Intent Match</span></div><div id="intentAlignmentResult" class="ai-result-box"></div></div>
        <div class="cae-info-item"><div class="cae-info-header"><span class="c-icon pulse">🏆</span><span class="cae-info-title">Featured Snippet Readiness</span></div><div id="snippetReadinessResult" class="ai-result-box"></div></div>
        <div class="cae-info-item"><div class="cae-info-header"><span class="c-icon spin">📖</span><span class="cae-info-title">Readability & Simplification</span></div><div id="readabilitySimplificationResult" class="ai-result-box"></div></div>
    </div>
  </div>

  <!-- Technical SEO Integration (Original Layout with Upgrades) -->
  <div class="tsi-card" id="technicalSeoCard">
     <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:16px;"><h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Technical SEO Integration</h3></div>
     <div class="tsi-grid">
      <div style="display:grid;place-items:center;padding:10px"><div class="mw" id="mwTSI"><div class="mw-ring" id="ringTSI" style="--v:0"></div><div class="mw-center" id="numTSI">0%</div></div></div>
      <div class="tsi-info-grid">
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon float">🔗</span><span class="tsi-info-title">Internal Linking</span></div><ul id="tsiInternalLinks"></ul></div>
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon float">🌐</span><span class="tsi-info-title">URL Structure</span></div><p>Clarity Score: <strong id="tsiUrlClarityScore">—</strong></p><p id="tsiUrlSuggestion"></p></div>
        <div class="tsi-info-item" style="grid-column: span 2;"><div class="tsi-info-header"><span class="c-icon float">📰</span><span class="tsi-info-title">Meta Tags</span></div><p><strong>Title:</strong> <span id="tsiMetaTitle">—</span></p><p><strong>Description:</strong> <span id="tsiMetaDescription">—</span></p></div>
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon float">🖼️</span><span class="tsi-info-title">Alt Texts</span></div><ul id="tsiAltTexts"></ul></div>
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon float">🗺️</span><span class="tsi-info-title">Site Map</span></div><div class="site-map-container" id="tsiSiteMap"></div></div>
      </div>
    </div>
    <div class="tsi-suggestions"><h4 class="flex items-center gap-2">💡 Technical SEO Suggestions</h4><ul id="tsiSuggestionsList"></ul></div>
    {{-- UPGRADED SECTION --}}
    <div class="upgraded-grid">
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon spin">🔗</span><span class="tsi-info-title">Internal Link Opportunities</span></div><div id="internalLinksResult" class="ai-result-box"></div></div>
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon pulse">🖼️</span><span class="tsi-info-title">Media & Image SEO</span></div><div id="imageSeoResult" class="ai-result-box"></div></div>
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon pulse">⚡</span><span class="tsi-info-title">UX that Impacts Rankings</span></div><div id="uxImpactResult" class="ai-result-box"></div></div>
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon spin">🔄</span><span class="tsi-info-title">Cannibalization Signals</span></div><div id="cannibalizationCheckResult" class="ai-result-box"></div></div>
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon pulse">⏳</span><span class="tsi-info-title">Content Freshness</span></div><div id="contentFreshnessResult" class="ai-result-box"></div></div>
        <div class="tsi-info-item"><div class="tsi-info-header"><span class="c-icon spin">⭐</span><span class="tsi-info-title">E-E-A-T Signals</span></div><div id="eeatSignalsResult" class="ai-result-box"></div></div>
    </div>
  </div>

  <!-- Keyword Intelligence (Original Layout with Upgrades) -->
  <div class="ki-card" id="keywordIntelligenceCard">
    <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:16px;"><h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">Keyword Intelligence</h3></div>
    <div class="ki-grid">
        <div class="ki-item"><h4 class="ki-item-title"><span class="c-icon pulse">🧠</span>Semantic Keyword Research</h4><div id="kiSemanticResearch" class="ki-tags"></div></div>
        <div class="ki-item"><h4 class="ki-item-title"><span class="c-icon pulse">🎯</span>Keyword Intent Classification</h4><div id="kiIntentClassification" class="ki-tags"></div></div>
        <div class="ki-item"><h4 class="ki-item-title"><span class="c-icon pulse">🗺️</span>Related Terms Mapping</h4><div id="kiRelatedTerms" class="ki-tags"></div></div>
        <div class="ki-item"><h4 class="ki-item-title"><span class="c-icon pulse">📊</span>Competitor Keyword Gap Analysis</h4><div id="kiCompetitorGaps" class="ki-list"></div></div>
        <div class="ki-item" style="grid-column: 1 / -1;"><h4 class="ki-item-title"><span class="c-icon pulse">🔑</span>Long-tail Semantic Suggestions</h4><div id="kiLongTail" class="ki-list"></div></div>
    </div>
     {{-- UPGRADED SECTION --}}
    <div class="upgraded-grid">
        <div class="ki-item"><h4 class="ki-item-title"><span class="c-icon spin">❓</span>Questions to Add (PAA & Forums)</h4><div id="questionMiningResult" class="ai-result-box"></div></div>
        <div class="ki-item"><h4 class="ki-item-title"><span class="c-icon pulse">🌿</span>Semantic Variants (No Stuffing)</h4><div id="semanticVariantsResult" class="ai-result-box"></div></div>
    </div>
  </div>
  
  <!-- On-Page Optimization Tools (This is a new card for some of the new features) -->
  <div class="onpage-card" id="onPageOptimizationCard">
    <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:16px;">
        <h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">On-Page Optimization Tools</h3>
    </div>
    <div class="onpage-grid">
        <div class="onpage-item"><h4 class="onpage-item-title"><span class="c-icon pulse">✍️</span>Title & Meta Rewriter</h4><div id="titleMetaRewriteResult" class="ai-result-box"></div></div>
        <div class="onpage-item"><h4 class="onpage-item-title"><span class="c-icon spin">🧐</span>Heading Hierarchy Auditor</h4><div id="headingHierarchyResult" class="ai-result-box"></div></div>
        <div class="onpage-item"><h4 class="onpage-item-title"><span class="c-icon pulse">📋</span>Tables, Checklists & Examples</h4><div id="tablesChecklistsResult" class="ai-result-box"></div></div>
        <div class="onpage-item"><h4 class="onpage-item-title"><span class="c-icon spin">🏗️</span>Schema Smart-Picker</h4><div id="schemaPickerResult" class="ai-result-box"></div></div>
    </div>
  </div>

  <!-- AI-Powered Features (Original Interactive Layout - UNTOUCHED) -->
  <div class="ai-card" id="aiPoweredFeaturesCard">
      <div style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:16px;"><h3 class="t-grad" style="font-weight:900;margin:0; font-size: 22px;">AI-Powered Features</h3></div>
      <div class="ai-grid">
          <div class="ai-item">
              <h4 class="ai-item-title"><span class="c-icon spin">📝</span>Content Brief Generation</h4><p>AI-generated semantic content briefs based on your target keyword.</p>
              <div class="ai-input-row"><input id="aiBriefInput" type="text" placeholder="Enter target keyword..."><button id="aiBriefBtn" class="btn btn-blue">Generate</button></div>
              <div id="aiBriefResult" class="ai-result-box">Brief will appear here...</div>
          </div>
          <div class="ai-item">
              <h4 class="ai-item-title"><span class="c-icon pulse">💡</span>Automated Content Suggestions</h4><p>Real-time content improvement recommendations for the analyzed URL.</p>
              <div class="ai-input-row"><button class="btn btn-green" style="width:100%">Get Suggestions</button></div>
              <div class="ai-result-box">Suggestions will appear here...</div>
          </div>
          <div class="ai-item">
              <h4 class="ai-item-title"><span class="c-icon spin">🕵️</span>Competitor Content Analysis</h4><p>Deep dive into competitor semantic strategies. (Uses analyzed URL)</p>
               <div class="ai-input-row"><input type="url" placeholder="Enter competitor URL..."><button class="btn btn-orange">Analyze</button></div>
              <div class="ai-result-box">Analysis will appear here...</div>
          </div>
           <div class="ai-item">
              <h4 class="ai-item-title"><span class="c-icon pulse">📈</span>Trend Prediction</h4><p>Forecast emerging semantic trends in your niche based on a keyword.</p>
               <div class="ai-input-row"><input type="text" placeholder="Enter topic or niche..."><button class="btn btn-purple">Predict</button></div>
              <div class="ai-result-box">Trends will appear here...</div>
          </div>
      </div>
  </div>

  <!-- Meta Info Layout (Original - UNTOUCHED) -->
  <div class="card meta-card" style="margin-top:24px;">
    <div style="display:flex; align-items:center; gap:10px; margin-bottom:12px;"><h3 class="t-grad">Meta & Heading Structure</h3></div>
    <div class="space-y-3">
        <div class="cat-card"><div class="cat-card-title">Title Tag</div><div id="titleVal" style="color:var(--ink); margin-top:4px;"></div></div>
        <div class="cat-card"><div class="cat-card-title">Meta Description</div><div id="metaVal" style="color:var(--ink); margin-top:4px;"></div></div>
        <div class="cat-card"><div class="cat-card-title">Heading Map (H1-H4)</div><div id="headingMap" class="space-y-2" style="margin-top:8px;"></div></div>
    </div>
  </div>

  <!-- Site Speed & Core Web Vitals (Original - UNTOUCHED) -->
  <div class="speed-card-new" id="speedCard">
    <div class="speed-header"><div class="speed-title"><span class="c-icon spin">🚀</span> Site Speed & Core Web Vitals</div><span id="speedBadge" class="speed-badge">Checking...</span></div>
    <div class="speed-overview-bar"><div id="speedOverviewBar"></div></div>
    <p class="speed-overview-text" id="speedOverviewText">Overview not available yet.</p>
    <div class="speed-grid">
        <div class="speed-device-card">
            <div class="speed-device-header"><span>📱</span> Mobile</div>
            <div style="display:flex; align-items:center; gap: 16px; margin-top:12px;">
                <div class="speed-device-score"><svg><circle class="track" cx="30" cy="30" r="26"></circle><circle id="mobileScoreCircle" class="progress" cx="30" cy="30" r="26"></circle></svg><div id="mobileScoreVal" class="speed-device-score-val">0</div></div>
                <div class="speed-device-metrics"><div class="speed-device-metric"><span>LCP</span><strong id="mobileLcp"></strong></div><div class="speed-device-metric"><span>INP</span><strong id="mobileInp"></strong></div><div class="speed-device-metric"><span>CLS</span><strong id="mobileCls"></strong></div></div>
            </div>
        </div>
        <div class="speed-device-card">
            <div class="speed-device-header"><span>💻</span> Desktop</div>
             <div style="display:flex; align-items:center; gap: 16px; margin-top:12px;">
                <div class="speed-device-score"><svg><circle class="track" cx="30" cy="30" r="26"></circle><circle id="desktopScoreCircle" class="progress" cx="30" cy="30" r="26"></circle></svg><div id="desktopScoreVal" class="speed-device-score-val">0</div></div>
                <div class="speed-device-metrics"><div class="speed-device-metric"><span>LCP</span><strong id="desktopLcp"></strong></div><div class="speed-device-metric"><span>INP</span><strong id="desktopInp"></strong></div><div class="speed-device-metric"><span>CLS</span><strong id="desktopCls"></strong></div></div>
            </div>
        </div>
    </div>
    <div class="speed-opportunities"><div class="speed-opportunities-title">🚀 Opportunities</div><ul id="speedOpportunitiesList"><li>Run analysis to see opportunities.</li></ul></div>
  </div>

  <!-- Ground Slab (Original - UNTOUCHED) -->
  <div class="ground-slab">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px"><div class="king"><span class="c-icon float">🧭</span></div><div><div class="t-grad">Semantic SEO Ground</div><div style="font-size:12px;color:#b6c2cf">Six categories • Five checks each • Click “Improve” for guidance</div></div></div>
    <div id="cats" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px"></div>
  </div>

  <!-- Modal Dialog (Original - UNTOUCHED) -->
  <dialog id="improveModal" class="rounded-2xl p-0 w-[min(680px,95vw)]" style="border:none;border-radius:16px">
    <div class="card">
      <div style="display:flex;align-items:start;justify-content:space-between;gap:10px"><h4 id="improveTitle" class="t-grad">Improve</h4><form method="dialog"><button class="pill">Close</button></form></div>
      <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:8px">
        <div class="card"><div style="font-size:12px;color:#94a3b8">Category</div><div id="improveCategory" style="font-weight:700">—</div></div>
        <div class="card"><div style="font-size:12px;color:#94a3b8">Score</div><div style="display:flex;align-items:center;gap:8px;margin-top:6px"><span id="improveScore" class="score-pill">—</span><span id="improveBand" class="pill">—</span></div></div>
        <a id="improveSearch" target="_blank" class="card" style="text-align:center;display:flex;align-items:center;justify-content:center;text-decoration:none"><span style="font-size:13px;color:var(--ink)">Search guidance</span></a>
      </div>
      <div style="margin-top:10px"><div style="font-size:12px;color:#94a3b8">Why this matters</div><p id="improveWhy" style="font-size:14px;color:var(--ink);margin-top:6px">—</p></div>
      <div style="margin-top:10px"><div style="font-size:12px;color:#94a3b8">How to improve</div><ul id="improveTips" style="margin-top:8px;padding-left:18px;display:grid;gap:6px;font-size:14px;color:var(--ink)"></ul></div>
    </div>
  </dialog>

</section>
@endsection
