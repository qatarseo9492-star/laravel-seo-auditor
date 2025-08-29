@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  html,body{background:#04021c!important;color:#e5e7eb}
  .maxw{max-width:1150px;margin:0 auto}
  .title-wrap{display:flex;align-items:center;gap:14px;justify-content:center;margin-top:14px}
  .king{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:#101018;border:1px solid #ffffff24}
  .t-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900}
  .byline{font-size:14px;color:#cbd5e1}
  .shoail{display:inline-block;background:linear-gradient(90deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;background-size:400% 100%;animation:rainbowSlide 6s linear infinite,bob 3s ease-in-out infinite}
  @keyframes rainbowSlide{to{background-position:100% 50%}} @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  .legend{display:flex;gap:10px;justify-content:center;margin:10px 0 6px}
  .legend .badge{padding:6px 10px;border-radius:9999px;font-weight:800;border:1px solid #ffffff2a;font-size:12px}
  .legend .g{background:#063f2c;color:#a7f3d0;border-color:#10b98166}
  .legend .o{background:#3b2a05;color:#fde68a;border-color:#f59e0b66}
  .legend .r{background:#3a0b0b;color:#fecaca;border-color:#ef444466}

  .card{border-radius:18px;padding:18px;background:#0a0a14;border:1px solid #ffffff1c}
  .cat-card{border-radius:16px;padding:16px;background:#111E2F;border:1px solid #ffffff1c}
  .ground-slab{border-radius:22px;padding:20px;background:#0D0E1E;border:1px solid #ffffff1c;margin-top:20px}

  .pill{padding:5px 10px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid #ffffff29;background:#ffffff14;color:#e5e7eb}
  .chip{padding:6px 8px;border-radius:12px;font-weight:800;display:inline-flex;align-items:center;gap:6px;border:1px solid #ffffff24;color:#eef2ff;font-size:12px}
  .chip i{font-style:normal}
  .chip.good{background:linear-gradient(135deg,#22c55e45,#10b98122);border-color:#22c55e72}
  .chip.warn{background:linear-gradient(135deg,#f59e0b45,#facc1522);border-color:#f59e0b72}
  .chip.bad{background:linear-gradient(135deg,#ef444445,#f8717122);border-color:#ef444472}

  .btn{padding:10px 14px;border-radius:12px;font-weight:900;border:1px solid #ffffff22;color:#0b1020;font-size:13px}
  .btn-green{background:#22c55e}.btn-blue{background:#3b82f6}.btn-orange{background:#f59e0b}.btn-purple{background:linear-gradient(90deg,#a78bfa,#f472b6);color:#19041a}
  .url-row{display:flex;align-items:center;gap:10px;border:1px solid #ffffff24;background:#0b0b12;border-radius:12px;padding:8px 10px}
  .url-row input{background:transparent;border:none;outline:none;color:#e5e7eb;width:100%}
  .url-row .paste{padding:6px 10px;border-radius:10px;border:1px solid #ffffff26;background:#ffffff10;color:#e5e7eb}

  .analyze-wrap{border-radius:16px;background:#020114;border:1px solid #ffffff20;box-shadow:inset 0 0 0 1px #ffffff0a;padding:12px}

  /* Wheels (overall + readability + speed) */
  .mw{--v:0;--ring:#f59e0b;--p:0;width:200px;height:200px;position:relative}
  .mw-ring{position:absolute;inset:0;border-radius:50%;background:conic-gradient(var(--ring) calc(var(--v)*1%),#ffffff14 0);-webkit-mask:radial-gradient(circle 76px,transparent 72px,#000 72px);mask:radial-gradient(circle 76px,transparent 72px,#000 72px)}
  .mw-fill{position:absolute;inset:18px;border-radius:50%;overflow:hidden;background:#000}
  .mw-fill::after{content:"";position:absolute;left:0;right:0;height:100%;top:calc(100% - var(--p)*1%);transition:top .9s ease;background:var(--fill,linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%));-webkit-mask:radial-gradient(105px 16px at 50% 0,#0000 98%,#000 100%);mask:radial-gradient(105px 16px at 50% 0,#0000 98%,#000 100%)}
  .mw.good{--ring:#22c55e;--fill:linear-gradient(to top,#16a34a 0%,#22c55e 60%,#86efac 100%)} .mw.warn{--ring:#f59e0b;--fill:linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%)} .mw.bad{--ring:#ef4444;--fill:linear-gradient(to top,#ef4444 0%,#f87171 60%,#fecaca 100%)}
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:34px;font-weight:900;color:#fff;text-shadow:0 6px 22px rgba(0,0,0,.45)}
  .mw-sm{width:170px;height:170px}.mw-sm .mw-ring{-webkit-mask:radial-gradient(circle 64px,transparent 60px,#000 60px);mask:radial-gradient(circle 64px,transparent 60px,#000 60px)} .mw-sm .mw-fill{inset:14px}.mw-sm .mw-center{font-size:28px}

  .waterbox{position:relative;height:16px;border-radius:9999px;overflow:hidden;border:1px solid #ffffff22;background:#0b0b12}
  .waterbox .fill{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .waterbox.good .fill{background:linear-gradient(90deg,#16a34a,#22c55e,#86efac)} .waterbox.warn .fill{background:linear-gradient(90deg,#f59e0b,#fbbf24,#fde68a)} .waterbox.bad .fill{background:linear-gradient(90deg,#ef4444,#f87171,#fecaca)}
  .waterbox .label{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e5e7eb;font-size:11px}

  .progress{width:100%;height:10px;border-radius:9999px;background:#ffffff14;overflow:hidden;border:1px solid #ffffff1a}
  .progress>span{display:block;height:100%;border-radius:9999px;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e);transition:width .5s ease}

  .check{display:flex;align-items:center;justify-content:space-between;border-radius:12px;padding:10px 12px;border:1px solid #ffffff1a;background:#0F1A29}
  .score-pill{padding:3px 7px;border-radius:10px;font-weight:800;background:#ffffff14;border:1px solid #ffffff22;color:#e5e7eb;font-size:12px}
  .score-pill--green{background:#10b9812e;border-color:#10b98166;color:#bbf7d0}.score-pill--orange{background:#f59e0b2e;border-color:#f59e0b66;color:#fde68a}.score-pill--red{background:#ef44442e;border-color:#ef444466;color:#fecaca}
  .improve-btn{padding:6px 9px;border-radius:10px;color:#0b1020;font-weight:800;border:1px solid transparent;transition:transform .08s ease;font-size:12px}
  .improve-btn:active{transform:translateY(1px)}
  .fill-green {background:linear-gradient(135deg,#16a34a,#22c55e,#86efac);color:#05240f}
  .fill-orange{background:linear-gradient(135deg,#f59e0b,#fbbf24,#fde68a);color:#3a2400}
  .fill-red   {background:linear-gradient(135deg,#ef4444,#f87171,#fecaca);color:#2f0606}
  .outline-green{border-color:#22c55edd!important;box-shadow:0 0 0 2px #22c55e8c inset,0 0 16px #22c55e55}
  .outline-orange{border-color:#f59e0bdd!important;box-shadow:0 0 0 2px #f59e0b8c inset,0 0 16px #f59e0b55}
  .outline-red{border-color:#ef4444dd!important;box-shadow:0 0 0 2px #ef44448c inset,0 0 16px #ef444455}

  dialog[open]{display:block} dialog::backdrop{background:rgba(0,0,0,.6)}
  #improveModal .card{background:#0D0E1E;border:1px solid #1b2640}
  #improveModal .card .card{background:#111E2F;border-color:#ffffff1c}

  #errorBox{display:none;margin-top:10px;border:1px solid #ef444466;background:#3a0b0b;color:#fecaca;border-radius:12px;padding:10px;white-space:pre-wrap;font-size:12px}

  /* ===================== Readability (with requested layout) ===================== */
  .read-card{position:relative;border-radius:20px;background:#05210c;border:2px solid #1f6f46;padding:16px;box-shadow:none}
  .rb-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
  .rb-title{display:flex;align-items:center;gap:10px}
  .rb-title .ico{width:36px;height:36px;display:grid;place-items:center;border-radius:10px;background:linear-gradient(135deg,#22d3ee33,#a78bfa33);border:1px solid #ffffff22}
  .rb-legend{font-size:12px;color:#aab3c2}
  .rb-grid{display:grid;grid-template-columns:220px 1fr;gap:12px;margin-top:10px}
  @media (max-width:920px){.rb-grid{grid-template-columns:1fr}}
  .rb-tiles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
  @media (max-width:920px){.rb-tiles{grid-template-columns:1fr}}
  .rb-tile{background:#0f1830;border:1px solid #21325c;border-radius:14px;padding:12px}
  .rb-row{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#b6c2cf;margin:8px 0 6px}
  .rb-val{color:#e5e7eb;font-weight:800}
  .rb-meter{height:10px;border-radius:9999px;background:#0c1226;border:1px solid #1b2b51;overflow:hidden}
  .rb-meter>span{display:block;height:100%;width:0%;transition:width .9s ease;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e)}
  .rb-fixes{background:#0f1830;border:1px solid #21325c;border-radius:14px;padding:14px;margin-top:12px}
  .rb-fixes h4{margin:0 0 8px 0;font-weight:900}
  .rb-fixes ul{margin:0;padding-left:18px}
  .rb-fixes li{margin:6px 0}
  .rb-banner{margin-top:12px;border-radius:14px;padding:12px;font-weight:800}
  .rb-banner.good{background:#05240f;border:1px solid #126f3f;color:#a7f3d0}
  .rb-banner.warn{background:#3b2a05;border:1px solid #9a6a10;color:#fde68a}
  .rb-banner.bad{background:#3a0b0b;border:1px solid #8a1a1a;color:#fecaca}

  /* ===================== Speed & Core Web Vitals (NEW) ===================== */
  .speed-card{position:relative;border-radius:20px;background:#0b0f1f;border:1px solid #17203e;padding:16px;margin-top:16px}
  .sp-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
  .sp-title{display:flex;align-items:center;gap:10px}
  .sp-title .ico{width:36px;height:36px;display:grid;place-items:center;border-radius:10px;background:linear-gradient(135deg,#22d3ee33,#a78bfa33);border:1px solid #ffffff22}
  .sp-tabs{display:flex;gap:6px}
  .sp-tab{padding:6px 10px;border-radius:9999px;border:1px solid #2a3a63;background:#0f1830;color:#dbeafe;font-weight:800;cursor:pointer}
  .sp-tab.active{background:#1d2b4a;border-color:#3a57a1}
  .sp-grid{display:grid;grid-template-columns:220px 1fr;gap:12px;margin-top:10px}
  @media (max-width:920px){.sp-grid{grid-template-columns:1fr}}
  .sp-tiles{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px}
  @media (max-width:920px){.sp-tiles{grid-template-columns:1fr}}
  .sp-tile{background:#0f1830;border:1px solid #21325c;border-radius:14px;padding:12px}
  .sp-row{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#b6c2cf;margin:8px 0 6px}
  .sp-val{color:#e5e7eb;font-weight:800}
  .sp-bar{height:10px;border-radius:9999px;background:#0c1226;border:1px solid #1b2b51;overflow:hidden}
  .sp-bar>span{display:block;height:100%;width:0%;transition:width .9s ease;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e)}
  .sp-fixes{background:#0f1830;border:1px solid #21325c;border-radius:14px;padding:14px;margin-top:12px}
  .sp-fixes h4{margin:0 0 8px 0;font-weight:900}
  .sp-fixes ul{margin:0;padding-left:18px}
  .sp-fixes li{margin:6px 0}
</style>
@endpush

@section('content')
<section class="maxw px-4 pb-10">

  <!-- Title -->
  <div class="title-wrap">
    <div class="king">👑</div>
    <div style="text-align:center">
      <div class="t-grad" style="font-size:26px;line-height:1.1;">Semantic SEO Master Analyzer</div>
      <div class="byline">By <span class="shoail">Shoail Kahoker</span></div>
    </div>
  </div>

  <!-- Legend -->
  <div class="legend"><span class="badge g">Green ≥ 80</span><span class="badge o">Orange 60–79</span><span class="badge r">Red &lt; 60</span></div>

  <!-- Wheel + chips -->
  <div style="display:grid;grid-template-columns:230px 1fr;gap:16px;align-items:center;margin-top:10px">
    <div style="display:grid;place-items:center;border-radius:16px;padding:8px;background:#090916;border:1px solid #ffffff12">
      <div class="mw warn" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-fill" id="mwFill" style="--p:0"></div>
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

  <!-- Analyze toolbar -->
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

    <!-- Status chips -->
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

  <!-- Quick Stats -->
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Quick Stats</h3>
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px">
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Readability (Flesch)</div><div id="statFlesch" style="font-size:20px;font-weight:800">—</div><div id="statGrade" style="font-size:12px;color:#94a3b8">—</div></div>
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Links (int / ext)</div><div style="font-size:20px;font-weight:800"><span id="statInt">0</span> / <span id="statExt">0</span></div></div>
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Text/HTML Ratio</div><div id="statRatio" style="font-size:20px;font-weight:800">—</div></div>
    </div>
  </div>

  <!-- Readability Insights -->
  <div class="read-card" id="readabilityCard" style="margin-top:16px">
    <div class="rb-head">
      <div class="rb-title">
        <div class="ico">📚</div>
        <div>
          <div class="t-grad" style="font-weight:900;">Readability Insights</div>
          <div class="rb-legend" id="rbLegend">Multilingual analysis — English, العربية, Português</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:6px">
        <span id="readBadge" class="pill">—</span>
        <span id="gradeBadge" class="pill">Grade —</span>
      </div>
    </div>
    <div class="rb-grid">
      <div style="display:grid;place-items:center;border-radius:16px;padding:8px;background:#090916;border:1px solid #ffffff12">
        <div class="mw mw-sm warn" id="readMw">
          <div class="mw-ring" id="readRing" style="--v:0"></div>
          <div class="mw-fill" id="readFill" style="--p:0"></div>
          <div class="mw-center" id="readNum">0%</div>
        </div>
      </div>
      <div class="rb-tiles">
        <div class="rb-tile"><div class="rb-row"><div>😊 Flesch Reading Ease</div><div class="rb-val" id="rbFleschVal">—</div></div><div class="rb-meter"><span id="rbFleschFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🧾 Avg Sentence Length</div><div class="rb-val" id="rbASLVal">—</div></div><div class="rb-meter"><span id="rbASLFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🔤 Words</div><div class="rb-val" id="rbWordsVal">—</div></div><div class="rb-meter"><span id="rbWordsFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🅰️ Syllables / Word</div><div class="rb-val" id="rbSyllVal">—</div></div><div class="rb-meter"><span id="rbSyllFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🔀 Lexical Diversity (TTR)</div><div class="rb-val" id="rbTTRVal">—</div></div><div class="rb-meter"><span id="rbTTRFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>♻️ Repetition (tri-gram)</div><div class="rb-val" id="rbTriVal">—</div></div><div class="rb-meter"><span id="rbTriFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div># Digits / 100 words</div><div class="rb-val" id="rbDigitsVal">—</div></div><div class="rb-meter"><span id="rbDigitsFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🗣️ Passive voice</div><div class="rb-val" id="rbPassiveVal">—</div></div><div class="rb-meter"><span id="rbPassiveFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>✨ Simple words</div><div class="rb-val" id="rbSimpleVal">—</div></div><div class="rb-meter"><span id="rbSimpleFill" style="width:0%"></span></div></div>
      </div>
    </div>
    <div class="rb-fixes"><h4>💡 Simple Fixes</h4><ul id="rbFixes"><li>Run an analysis to see targeted suggestions.</li></ul></div>
    <div id="rbBanner" class="rb-banner warn">Readability score helps you target Grade 7–9 for most audiences.</div>
  </div>

  <!-- ===================== Site Speed & Core Web Vitals (NEW) ===================== -->
  <div class="speed-card" id="speedCard">
    <div class="sp-head">
      <div class="sp-title">
        <div class="ico">⚡</div>
        <div>
          <div class="t-grad" style="font-weight:900;">Site Speed & Core Web Vitals</div>
          <div class="byline" style="font-size:12px;">Powered by Google PageSpeed Insights</div>
        </div>
      </div>
      <div class="sp-tabs">
        <button class="sp-tab active" id="tabMobile">📱 Mobile</button>
        <button class="sp-tab" id="tabDesktop">🖥️ Desktop</button>
      </div>
    </div>

    <div class="sp-grid">
      <!-- Speed wheel -->
      <div style="display:grid;place-items:center;border-radius:16px;padding:8px;background:#090916;border:1px solid #ffffff12">
        <div class="mw mw-sm warn" id="speedMw">
          <div class="mw-ring" id="speedRing" style="--v:0"></div>
          <div class="mw-fill" id="speedFill" style="--p:0"></div>
          <div class="mw-center" id="speedNum">0</div>
        </div>
        <div class="byline" id="speedDeviceLabel" style="margin-top:6px">Mobile Performance</div>
      </div>

      <!-- Metric tiles -->
      <div class="sp-tiles">
        <div class="sp-tile">
          <div class="sp-row"><div>⏱️ LCP (s)</div><div class="sp-val" id="spLcpVal">—</div></div>
          <div class="sp-bar"><span id="spLcpFill" style="width:0%"></span></div>
        </div>
        <div class="sp-tile">
          <div class="sp-row"><div>📦 CLS</div><div class="sp-val" id="spClsVal">—</div></div>
          <div class="sp-bar"><span id="spClsFill" style="width:0%"></span></div>
        </div>
        <div class="sp-tile">
          <div class="sp-row"><div>🖱️ INP (ms)</div><div class="sp-val" id="spInpVal">—</div></div>
          <div class="sp-bar"><span id="spInpFill" style="width:0%"></span></div>
        </div>
        <div class="sp-tile">
          <div class="sp-row"><div>🌐 TTFB (ms)</div><div class="sp-val" id="spTtfbVal">—</div></div>
          <div class="sp-bar"><span id="spTtfbFill" style="width:0%"></span></div>
        </div>
      </div>
    </div>

    <div class="sp-fixes">
      <h4>💡 Speed Suggestions</h4>
      <ul id="spFixes">
        <li>Run an analysis to fetch PageSpeed data and see targeted speed tips.</li>
      </ul>
    </div>
  </div>
  <!-- ===================== /Speed & CWV ===================== -->

  <!-- Content Structure -->
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Content Structure</h3>
    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px">
      <div class="card">
        <div style="font-size:12px;color:#b6c2cf">Title</div>
        <div id="titleVal" style="font-weight:600">—</div>
        <div style="font-size:12px;color:#b6c2cf;margin-top:10px">Meta Description</div>
        <div id="metaVal" style="color:#e5e7eb">—</div>
      </div>
      <div class="card">
        <div style="font-size:12px;color:#b6c2cf;margin-bottom:6px">Heading Map</div>
        <div id="headingMap" class="text-sm space-y-2"></div>
      </div>
    </div>
  </div>

  <!-- Recommendations -->
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Recommendations</h3>
    <div id="recs" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px"></div>
  </div>

  <!-- Semantic SEO Ground -->
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

  <!-- Improve Modal -->
  <dialog id="improveModal" class="rounded-2xl p-0 w:[min(680px,95vw)]" style="border:none;border-radius:16px">
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
        <a id="improveSearch" target="_blank" class="card" style="text-align:center;display:flex;align-items:center;justify-content:center;background:linear-gradient(90deg,#f472b626,#22d3ee26);border:1px solid #ffffff22;text-decoration:none">
          <span style="font-size:13px;color:#e5e7eb">Search guidance</span>
        </a>
      </div>
      <div style="margin-top:10px">
        <div style="font-size:12px;color:#94a3b8">Why this matters</div>
        <p id="improveWhy" style="font-size:14px;color:#e5e7eb;margin-top:6px">—</p>
      </div>
      <div style="margin-top:10px">
        <div style="font-size:12px;color:#94a3b8">How to improve</div>
        <ul id="improveTips" style="margin-top:8px;padding-left:18px;display:grid;gap:6px;font-size:14px;color:#e5e7eb"></ul>
      </div>
    </div>
  </dialog>

</section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const $ = s=>document.querySelector(s);

  /* Elements */
  const mw=$('#mw'), mwRing=$('#mwRing'), mwFill=$('#mwFill'), mwNum=$('#mwNum');
  const overallBar=$('#overallBar'), overallFill=$('#overallFill'), overallPct=$('#overallPct');
  const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'), chipHuman=$('#chipHuman'), chipAI=$('#chipAI');

  const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'),
        importBtn=$('#importBtn'), importFile=$('#importFile'), printBtn=$('#printBtn'),
        resetBtn=$('#resetBtn'), exportBtn=$('#exportBtn');

  const statF=$('#statFlesch'), statG=$('#statGrade'), statInt=$('#statInt'), statExt=$('#statExt'), statRatio=$('#statRatio');
  const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap'), recsEl=$('#recs'), catsEl=$('#cats');

  const chipHttp=$('#chipHttp'), chipTitle=$('#chipTitle'), chipMeta=$('#chipMeta'),
        chipCanon=$('#chipCanon'), chipRobots=$('#chipRobots'), chipViewport=$('#chipViewport'),
        chipH=$('#chipH'), chipIntChip=$('#chipInt'), chipSchema=$('#chipSchema'), chipAuto=$('#chipAuto');

  const errorBox = $('#errorBox');

  const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
        mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
        mTips=$('#improveTips'), mLink=$('#improveSearch');

  /* Readability UI (new) */
  const readMw=$('#readMw'), readRing=$('#readRing'), readFill=$('#readFill'), readNum=$('#readNum');
  const readBadge=$('#readBadge'), gradeBadge=$('#gradeBadge'), rbLegend=$('#rbLegend');
  const rbFleschVal=$('#rbFleschVal'), rbFleschFill=$('#rbFleschFill');
  const rbASLVal=$('#rbASLVal'), rbASLFill=$('#rbASLFill');
  const rbWordsVal=$('#rbWordsVal'), rbWordsFill=$('#rbWordsFill');
  const rbSyllVal=$('#rbSyllVal'), rbSyllFill=$('#rbSyllFill');
  const rbTTRVal=$('#rbTTRVal'), rbTTRFill=$('#rbTTRFill');
  const rbTriVal=$('#rbTriVal'), rbTriFill=$('#rbTriFill');
  const rbDigitsVal=$('#rbDigitsVal'), rbDigitsFill=$('#rbDigitsFill');
  const rbPassiveVal=$('#rbPassiveVal'), rbPassiveFill=$('#rbPassiveFill');
  const rbSimpleVal=$('#rbSimpleVal'), rbSimpleFill=$('#rbSimpleFill');
  const rbFixes=$('#rbFixes'), rbBanner=$('#rbBanner');

  /* Speed UI (new) */
  const tabMobile=$('#tabMobile'), tabDesktop=$('#tabDesktop');
  const speedMw=$('#speedMw'), speedRing=$('#speedRing'), speedFill=$('#speedFill'), speedNum=$('#speedNum'), speedDeviceLabel=$('#speedDeviceLabel');
  const spLcpVal=$('#spLcpVal'), spClsVal=$('#spClsVal'), spInpVal=$('#spInpVal'), spTtfbVal=$('#spTtfbVal');
  const spLcpFill=$('#spLcpFill'), spClsFill=$('#spClsFill'), spInpFill=$('#spInpFill'), spTtfbFill=$('#spTtfbFill');
  const spFixes=$('#spFixes');

  /* Helpers */
  const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
  const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
  const bandIcon=s=>s>=80?'✅':(s>=60?'🟧':'🔴');
  const fillBy=s=>s>=80?'fill-green':(s>=60?'fill-orange':'fill-red');
  const outlineBy=s=>s>=80?'outline-green':(s>=60?'outline-orange':'outline-red');

  function setChip(el,label,value,score){
    if(!el)return;
    el.classList.remove('good','warn','bad');
    const b=bandName(score);
    el.classList.add(b);
    el.innerHTML=`<i>${bandIcon(score)}</i><span>${label}: ${value}</span>`;
  }
  function showError(msg, detail) {
    errorBox.style.display = 'block';
    errorBox.textContent = msg + (detail ? "\n\n" + detail : '');
  }
  function clearError(){ errorBox.style.display='none'; errorBox.textContent=''; }

  /* Category defs + KB (unchanged) */
  const CATS = [
    { name:'User Signals & Experience', icon:'📱', checks:[
      'Mobile-friendly, responsive layout','Optimized speed (compression, lazy-load)','Core Web Vitals passing (LCP/INP/CLS)','Clear CTAs and next steps','Accessible basics (alt text, contrast)']},
    { name:'Entities & Context', icon:'🧩', checks:[
      'sameAs/Organization details present','Valid schema markup (Article/FAQ/Product)','Related entities covered with context','Primary entity clearly defined','Organization contact/about page visible']},
    { name:'Structure & Architecture', icon:'🏗️', checks:[
      'Logical H2/H3 headings & topic clusters','Internal links to hub/related pages','Clean, descriptive URL slug','Breadcrumbs enabled (+ schema)','XML sitemap logical structure']},
    { name:'Content Quality', icon:'🧠', checks:[
      'E-E-A-T signals (author, date, expertise)','Unique value vs. top competitors','Facts & citations up to date','Helpful media (images/video) w/ captions','Up-to-date examples & screenshots']},
    { name:'Content & Keywords', icon:'📝', checks:[
      'Define search intent & primary topic','Map target & related keywords (synonyms/PAA)','H1 includes primary topic naturally','Integrate FAQs / questions with answers','Readable, NLP-friendly language']},
    { name:'Technical Elements', icon:'⚙️', checks:[
      'Title tag (≈50–60 chars) w/ primary keyword','Meta description (≈140–160 chars) + CTA','Canonical tag set correctly','Indexable & listed in XML sitemap','Robots directives valid']},
  ];
  const KB = {
    'Mobile-friendly, responsive layout': {why:'Most traffic is mobile; poor UX kills engagement.', tips:['Responsive breakpoints & fluid grids.','Tap targets ≥44px.','Avoid horizontal scroll.'], link:'https://search.google.com/test/mobile-friendly'},
    'Optimized speed (compression, lazy-load)': {why:'Speed affects abandonment and CWV.', tips:['Use WebP/AVIF.','HTTP/2 + CDN caching.','Lazy-load below-the-fold.'], link:'https://web.dev/fast/'},
    'Core Web Vitals passing (LCP/INP/CLS)': {why:'Passing CWV improves experience & stability.', tips:['Preload hero image.','Minimize long JS tasks.','Reserve media space.'], link:'https://web.dev/vitals/'},
    'Clear CTAs and next steps': {why:'Clarity increases conversions and task completion.', tips:['One primary CTA per view.','Action verbs + benefit.','Explain what happens next.'], link:'https://www.nngroup.com/articles/call-to-action-buttons/'},
    'Accessible basics (alt text, contrast)': {why:'Accessibility broadens reach and reduces risk.', tips:['Alt text on images.','Contrast ratio ≥4.5:1.','Keyboard focus states.'], link:'https://www.w3.org/WAI/standards-guidelines/wcag/'},
    // ... (rest of KB same as before)
  };

  /* Checklist scoring (unchanged) */
  function scoreChecklist(label, data, url, targetKw=''){
    const qs = data.quick_stats||{};
    const cs = data.content_structure||{};
    const ps = data.page_signals||{};
    const r  = data.readability||{};
    const h1 = (cs.headings&&cs.headings.H1?cs.headings.H1.length:0)||0;
    const h2 = (cs.headings&&cs.headings.H2?cs.headings.H2.length:0)||0;
    const h3 = (cs.headings&&cs.headings.H3?cs.headings.H3.length:0)||0;
    const title = (cs.title||'');
    const meta  = (cs.meta_description||'');
    const internal = Number(qs.internal_links||0);
    const external = Number(qs.external_links||0);
    const schemaTypes = new Set((ps?.schema_types)||[]);
    const robots = (ps?.robots||'').toLowerCase();
    const hasFAQ = schemaTypes.has('FAQPage');
    const hasArticle = schemaTypes.has('Article') || schemaTypes.has('NewsArticle') || schemaTypes.has('BlogPosting');
    const urlPath = (()=>{ try { return new URL(url).pathname; } catch { return '/'; } })();
    const slugScore = (()=>{ const hasQuery = url.includes('?'); const segs = urlPath.split('/').filter(Boolean); const words = segs.join('-').split('-').filter(Boolean); if (hasQuery) return 55; if (segs.length>6) return 60; if (words.some(w=>w.length>24)) return 65; return 85; })();

    switch(label){
      case 'Mobile-friendly, responsive layout': return ps.has_viewport ? 88 : 58;
      case 'Optimized speed (compression, lazy-load)': return 60;
      case 'Core Web Vitals passing (LCP/INP/CLS)':     return 60;
      case 'Clear CTAs and next steps':                 return meta.length>=140 && /learn|get|try|start|buy|sign|download|contact/i.test(meta) ? 80 : 60;
      case 'Accessible basics (alt text, contrast)':    return ( (typeof window.__imgAltCount==='number' ? window.__imgAltCount : 0) || 0 ) >= 3 ? 82 : 58;

      case 'sameAs/Organization details present':       return ps.has_org_sameas ? 90 : 55;
      case 'Valid schema markup (Article/FAQ/Product)': return (hasArticle || hasFAQ || schemaTypes.has('Product')) ? 85 : (schemaTypes.size>0?70:50);
      case 'Related entities covered with context':     return external>=2 ? 72 : 60;
      case 'Primary entity clearly defined':            return ps.has_main_entity ? 85 : (h1>0 ? 72 : 58);
      case 'Organization contact/about page visible':   return 60;

      case 'Logical H2/H3 headings & topic clusters':   return (h2>=3 && h3>=2) ? 85 : (h2>=2 ? 70 : 55);
      case 'Internal links to hub/related pages':       return internal>=5 ? 85 : (internal>=2 ? 65 : 45);
      case 'Clean, descriptive URL slug':               return slugScore;
      case 'Breadcrumbs enabled (+ schema)':            return ps.has_breadcrumbs ? 85 : 55;
      case 'XML sitemap logical structure':             return 60;

      case 'E-E-A-T signals (author, date, expertise)': return ps.has_org_sameas ? 75 : 65;
      case 'Unique value vs. top competitors':          return 60;
      case 'Facts & citations up to date':              return external>=2 ? 78 : 58;
      case 'Helpful media (images/video) w/ captions':  return 60;
      case 'Up-to-date examples & screenshots':         return 60;

      case 'Define search intent & primary topic':      return (title && h1>0) ? 78 : 60;
      case 'Map target & related keywords (synonyms/PAA)': {
        const kw = ''.trim(); if (!kw) return 60;
        const found = (title.toLowerCase().includes(kw.toLowerCase()) || (cs.headings?.H1||[]).join(' || ').toLowerCase().includes(kw.toLowerCase()));
        return found ? 80 : 62;
      }
      case 'H1 includes primary topic naturally': {
        const kw = ''.trim();
        if (h1===0) return 45;
        if (!kw) return 72;
        const found = (cs.headings?.H1||[]).some(h=>h.toLowerCase().includes(kw.toLowerCase()));
        return found ? 84 : 72;
      }
      case 'Integrate FAQs / questions with answers':   return hasFAQ ? 85 : (/(faq|questions?)/i.test((cs.headings?.H2||[]).join(' ') + ' ' + (cs.headings?.H3||[]).join(' ')) ? 70 : 55);
      case 'Readable, NLP-friendly language':           return clamp01(r.score||0);

      case 'Title tag (≈50–60 chars) w/ primary keyword': {
        const len = (title||'').length;
        return (len>=50 && len<=60) ? 88 : (len ? 68 : 45);
      }
      case 'Meta description (≈140–160 chars) + CTA': {
        const len = (meta||'').length;
        const hasCTA = /learn|get|try|start|buy|sign|download|contact/i.test(meta||'');
        return (len>=140 && len<=160) ? (hasCTA?90:82) : (len ? 65 : 48);
      }
      case 'Canonical tag set correctly':               return ps.canonical ? 85 : 55;
      case 'Indexable & listed in XML sitemap':         return robots.includes('noindex') ? 20 : 80;
      case 'Robots directives valid':                   return (robots && /(noindex|none)/.test(robots)) ? 45 : 75;
    }
    return 60;
  }

  function renderCategories(data, url, targetKw){
    catsEl.innerHTML='';
    let autoGood=0;
    CATS.forEach(cat=>{
      const rows = cat.checks.map(lbl=>{
        const s = scoreChecklist(lbl, data, url, targetKw);
        const fill = fillBy(s), outline = outlineBy(s);
        const pill = s>=80 ? 'score-pill--green' : s>=60 ? 'score-pill--orange' : 'score-pill--red';
        if (s>=80) autoGood++;
        return {label:lbl, score:s, fill, outline, pill, bandTxt:(s>=80?'Good (≥80)':s>=60?'Needs work (60–79)':'Low (<60)')};
      });

      const total = rows.length;
      const passed = rows.filter(r=>r.score>=80).length;
      const pct = Math.round((passed/Math.max(1,total))*100);

      const card=document.createElement('div'); card.className='cat-card';
      card.innerHTML=`<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
          <div style="display:flex;align-items:center;gap:8px">
            <div class="king" style="width:34px;height:34px">${cat.icon}</div>
            <div>
              <div class="t-grad" style="font-size:16px;font-weight:900">${cat.name}</div>
              <div style="font-size:12px;color:#b6c2cf">Keep improving</div>
            </div>
          </div>
          <div class="pill">${passed} / ${total}</div>
        </div>
        <div class="progress" style="margin-bottom:8px"><span style="width:${pct}%"></span></div>
        <div class="space-y-2" id="list"></div>`;
      const list = card.querySelector('#list');

      rows.forEach(row=>{
        const dot  = row.score>=80 ? '#10b981' : row.score>=60 ? '#f59e0b' : '#ef4444';
        const el=document.createElement('div'); el.className='check';
        el.innerHTML = `
          <div style="display:flex;align-items:center;gap:8px">
            <span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:${dot}"></span>
            <div class="font-semibold" style="font-size:13px">${row.label}</div>
          </div>
          <div style="display:flex;align-items:center;gap:6px">
            <span class="score-pill ${row.pill}">${row.score}</span>
            <button class="improve-btn ${row.fill} ${row.outline}" type="button">Improve</button>
          </div>`;
        el.querySelector('.improve-btn').addEventListener('click',()=>{
          const kb = KB[row.label] || {why:'This item impacts relevance and UX.', tips:['Aim for ≥80 and re-run the analyzer.'], link:'https://www.google.com'};
          mTitle.textContent = row.label;
          mCat.textContent   = cat.name;
          mScore.textContent = row.score;
          mBand.textContent  = row.bandTxt;
          mBand.className    = 'pill '+(row.score>=80?'score-pill--green':row.score>=60?'score-pill--orange':'score-pill--red');
          mWhy.textContent   = kb.why;
          mTips.innerHTML = '';
          (kb.tips||[]).forEach(t=>{ const li=document.createElement('li'); li.textContent=t; mTips.appendChild(li); });
          mLink.href = kb.link || ('https://www.google.com/search?q='+encodeURIComponent(row.label+' best practices'));
          if(typeof modal.showModal==='function') modal.showModal(); else modal.setAttribute('open','');
        });
        list.appendChild(el);
      });

      catsEl.appendChild(card);
    });

    chipAuto.textContent = autoGood;
  }

  /* --- Robust analyzer call --- */
  async function fetchWithTimeout(url, opts={}, timeout=20000){
    const ctrl = new AbortController(); const t = setTimeout(()=>ctrl.abort(), timeout);
    try{ return await fetch(url, {...opts, signal: ctrl.signal}); } finally{ clearTimeout(t); }
  }
  async function callAnalyzer(url){
    const payload = JSON.stringify({url, target_keyword:''});
    const headers = {'Accept':'application/json','Content-Type':'application/json'};
    const csrf = '{{ csrf_token() }}';
    try{
      const res = await fetchWithTimeout('/semantic-analyzer/analyze', {method:'POST', headers:{...headers,'X-CSRF-TOKEN':csrf}, body:payload});
      if (res.ok) return res.json();
      const txt = await res.text();
      if (res.status>=500) throw new Error(`HTTP ${res.status}\n${txt?.slice(0,800)}`);
    }catch(e){ console.warn('Web route error:', e); }
    const res2 = await fetchWithTimeout('/api/semantic-analyze', {method:'POST', headers, body:payload});
    if (res2.ok) return res2.json();
    const txt2 = await res2.text();
    throw new Error(`HTTP ${res2.status}\n${txt2?.slice(0,800)}`);
  }

  function setRunning(isOn){
    if(!analyzeBtn)return;
    analyzeBtn.disabled = isOn;
    analyzeBtn.style.opacity = isOn ? .6 : 1;
    analyzeBtn.textContent = isOn ? 'Analyzing…' : '🔍 Analyze';
  }

  /* ===== Readability helpers ===== */
  const pct = (v,min,max,invert=false)=>{
    if(v===null||v===undefined||isNaN(v)) return 0;
    let p=(v-min)/Math.max(1,(max-min))*100;
    p=Math.max(0,Math.min(100,p));
    return invert?100-p:p;
  };
  function setMeter(fillEl,valEl,value,display,range, invert=false){
    if(!fillEl||!valEl) return;
    valEl.textContent = (value===null||value===undefined||Number.isNaN(value)) ? '—' : display;
    const w = pct(Number(value), range[0], range[1], invert);
    fillEl.style.width = w+'%';
  }
  function buildFixes(r){
    const fixes=[];
    if(typeof r.avg_sentence_len==='number' && r.avg_sentence_len>20) fixes.push('Break long sentences into 12–16 words.');
    if(typeof r.simple_words_ratio==='number' && r.simple_words_ratio<80) fixes.push('Prefer shorter, simpler words (use clearer synonyms).');
    if(typeof r.passive_ratio==='number' && r.passive_ratio>15) fixes.push('Reduce passive voice; rewrite in active voice.');
    if(typeof r.repetition_trigram==='number' && r.repetition_trigram>10) fixes.push('Trim repeated phrases; vary wording and examples.');
    if(typeof r.digits_per_100w==='number' && r.digits_per_100w>10) fixes.push('Reduce numeric density; round or group numbers where possible.');
    if(fixes.length<3){
      fixes.push('Add headings and bullets to chunk information.');
      fixes.push('Use image captions to explain visuals succinctly.');
      fixes.push('Front-load key points; keep paragraphs 2–4 lines.');
    }
    rbFixes.innerHTML = fixes.slice(0,5).map(x=>`<li>✅ ${x}</li>`).join('');
  }

  /* ===== SPEED & CWV helpers ===== */
  // scoring: map metric value to 0..100 where 100 is "good"
  const invScore = (value, good, poor)=>{
    if(value==null||isNaN(value)) return 0;
    const v=Number(value);
    if (v<=good) return 100;
    if (v>=poor) return 0;
    return Math.round((poor - v) / (poor - good) * 100);
  };
  const fmtS = ms => (ms==null||isNaN(ms))?'—':(Math.round(ms/100)/10).toFixed(1); // seconds w/ 1 dp
  const fmtMs = ms => (ms==null||isNaN(ms))?'—':Math.round(ms);
  const fmtCls = x  => (x==null||isNaN(x))?'—':Number(x).toFixed(2);

  function setSpeedWheel(score){
    const band = bandName(score);
    speedMw.classList.remove('good','warn','bad'); speedMw.classList.add(band);
    speedRing.style.setProperty('--v', score); speedFill.style.setProperty('--p', score);
    speedNum.textContent = score;
  }
  function setBar(fillEl, valEl, valueDisplay, score){
    fillEl.style.width = Math.max(0,Math.min(100,score)) + '%';
    valEl.textContent = valueDisplay;
  }

  function speedSuggestions(metrics){
    const fixes=[];
    // LCP
    if(metrics.lcpScore<80){
      fixes.push('Optimize LCP: compress & resize hero image (WebP/AVIF), inline critical CSS, <link rel="preload"> key resources.');
      fixes.push('Remove render-blocking CSS/JS above the fold; defer non-critical scripts.');
    }
    // CLS
    if(metrics.clsScore<80){
      fixes.push('Reduce CLS: always reserve space for media (width/height or CSS aspect-ratio).');
      fixes.push('Use font-display: swap and avoid late-injected banners or popups.');
    }
    // INP
    if(metrics.inpScore<80){
      fixes.push('Lower INP: break up long tasks (<50ms), avoid heavy synchronous work on input.');
      fixes.push('Minimize third-party scripts; throttle expensive listeners and reflows.');
    }
    // TTFB
    if(metrics.ttfbScore<80){
      fixes.push('Improve TTFB: enable server caching/edge caching (CDN), optimize database queries.');
      fixes.push('Use HTTP/2+, keep-alive, and preconnect to critical origins.');
    }
    if(!fixes.length){
      fixes.push('Great job! Your Core Web Vitals look healthy. Keep monitoring after major changes.');
    }
    spFixes.innerHTML = fixes.slice(0,8).map(x=>`<li>✅ ${x}</li>`).join('');
  }

  function renderSpeed(device, data){
    // device: 'mobile'|'desktop'; data: PSI payload for that strategy
    if(!data) return;

    const perfScore = Math.round((data.lighthouseResult?.categories?.performance?.score||0)*100);

    // Lighthouse audit fallbacks
    const audits = data.lighthouseResult?.audits || {};
    const lcpMs = audits['largest-contentful-paint']?.numericValue ?? audits['metrics']?.details?.items?.[0]?.lcp ?? null;
    const cls   = audits['cumulative-layout-shift']?.numericValue ?? audits['metrics']?.details?.items?.[0]?.cls ?? null;
    const inpMs = (audits['interaction-to-next-paint']?.numericValue ??
                  audits['experimental-interaction-to-next-paint']?.numericValue) ?? null;
    const ttfbMs = (audits['time-to-first-byte']?.numericValue ??
                   audits['server-response-time']?.numericValue) ?? null;

    // Scores (0..100, higher=better)
    const lcpScore  = invScore(lcpMs, 2500, 4000);
    const clsScore  = invScore(cls, 0.10, 0.25);
    const inpScore  = invScore(inpMs, 200, 500);
    const ttfbScore = invScore(ttfbMs, 800, 1800);

    // Wheel
    setSpeedWheel(perfScore);
    speedDeviceLabel.textContent = (device==='mobile'?'Mobile':'Desktop') + ' Performance';

    // Bars
    setBar(spLcpFill, spLcpVal, fmtS(lcpMs/1000)+'s', lcpScore);
    setBar(spClsFill, spClsVal, fmtCls(cls),         clsScore);
    setBar(spInpFill, spInpVal, fmtMs(inpMs)+'ms',   inpScore);
    setBar(spTtfbFill,spTtfbVal,fmtMs(ttfbMs)+'ms',  ttfbScore);

    speedSuggestions({lcpScore,clsScore,inpScore,ttfbScore});
  }

  /* === PSI fetch via Laravel proxy === */
  async function callPSI(url){
    const csrf='{{ csrf_token() }}';
    const res = await fetch('/semantic-analyzer/psi', {
      method:'POST',
      headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':csrf},
      body: JSON.stringify({url})
    });
    if(!res.ok){
      const t=await res.text(); throw new Error(`PSI HTTP ${res.status}\n${t?.slice(0,800)}`);
    }
    return res.json(); // { mobile: {...}, desktop: {...} }
  }

  /* Paste/import/print/reset/export */
  $('#pasteBtn')?.addEventListener('click',async e=>{e.preventDefault();try{const t=await navigator.clipboard.readText();if(t)urlInput.value=t.trim()}catch{}})
  $('#importBtn')?.addEventListener('click',()=>importFile.click());
  $('#importFile')?.addEventListener('change',e=>{const f=e.target.files?.[0];if(!f)return;const r=new FileReader();r.onload=()=>{try{const j=JSON.parse(String(r.result||'{}'));if(j.url)urlInput.value=j.url;alert('Imported JSON. Click Analyze to run.')}catch{alert('Invalid JSON file.')}};r.readAsText(f)})
  $('#printBtn')?.addEventListener('click',()=>window.print());
  $('#resetBtn')?.addEventListener('click',()=>location.reload());
  $('#exportBtn')?.addEventListener('click',()=>{if(!window.__lastData){alert('Run an analysis first.');return;}const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='semantic-report.json';a.click();URL.revokeObjectURL(a.href)})

  /* Analyze */
  async function runAnalyze(){
    clearError();
    const url=(urlInput.value||'').trim();
    if(!url){showError('Please enter a URL.');return;}
    try{
      setRunning(true);

      // Reset overall
      mwRing?.style.setProperty('--v',0); mwFill?.style.setProperty('--p',0); mw?.classList.remove('good','warn','bad'); mw?.classList.add('warn'); mwNum.textContent='0%';
      overallBar?.classList.remove('good','warn','bad'); overallBar?.classList.add('warn'); overallFill.style.width='0%'; overallPct.textContent='0%';

      // Reset Readability
      readRing?.style.setProperty('--v',0); readFill?.style.setProperty('--p',0); readMw?.classList.remove('good','warn','bad'); readMw?.classList.add('warn'); readNum.textContent='0%';
      [rbFleschFill,rbASLFill,rbWordsFill,rbSyllFill,rbTTRFill,rbTriFill,rbDigitsFill,rbPassiveFill,rbSimpleFill].forEach(el=>{ if(el) el.style.width='0%'; });
      [rbFleschVal,rbASLVal,rbWordsVal,rbSyllVal,rbTTRVal,rbTriVal,rbDigitsVal,rbPassiveVal,rbSimpleVal].forEach(el=>{ if(el) el.textContent='—'; });
      readBadge.textContent='—'; gradeBadge.textContent='Grade —'; rbBanner.className='rb-banner warn'; rbBanner.textContent='Readability score helps you target Grade 7–9 for most audiences.'; rbFixes.innerHTML='<li>Run an analysis to see targeted suggestions.</li>';

      // Reset Speed
      speedRing?.style.setProperty('--v',0); speedFill?.style.setProperty('--p',0); speedMw?.classList.remove('good','warn','bad'); speedMw?.classList.add('warn'); speedNum.textContent='0';
      [spLcpFill,spClsFill,spInpFill,spTtfbFill].forEach(el=>el.style.width='0%');
      [spLcpVal,spClsVal,spInpVal,spTtfbVal].forEach(el=>el.textContent='—');
      spFixes.innerHTML = '<li>Run an analysis to fetch PageSpeed data and see targeted speed tips.</li>';

      // Main analyzer first
      const data=await callAnalyzer(url);
      if(!data||data.error) throw new Error(data?.error||'Unknown error');
      window.__lastData = {...data, url};

      /* Overall */
      const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
      const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
      const score = clamp01(data.overall_score||0), band=bandName(score);
      mw?.classList.remove('good','warn','bad'); mw?.classList.add(band);
      mwRing?.style.setProperty('--v',score); mwFill?.style.setProperty('--p',score);
      mwNum.textContent=score+'%';
      overallBar?.classList.remove('good','warn','bad'); overallBar?.classList.add(band);
      overallFill.style.width=score+'%'; overallPct.textContent=score+'%';
      const setChip=(el,label,value,score)=>{ if(!el)return; el.classList.remove('good','warn','bad'); const b=bandName(score); el.classList.add(b); el.innerHTML=`<i>${score>=80?'✅':(score>=60?'🟧':'🔴')}</i><span>${label}: ${value}</span>`; };
      const cmap={}; (data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
      const contentScore = Math.round(([cmap['Content & Keywords'], cmap['Content Quality']].filter(v=>typeof v==='number').reduce((a,b)=>a+b,0))/2 || 0);
      setChip(chipOverall,'Overall',`${score} /100`,score);
      setChip(chipContent,'Content',`${contentScore} /100`,contentScore);

      /* Readability */
      const r = data.readability||{};
      const rs = clamp01(r.score||0);
      const rBand = bandName(rs);
      readMw?.classList.remove('good','warn','bad'); readMw?.classList.add(rBand);
      readRing?.style.setProperty('--v',rs); readFill?.style.setProperty('--p',rs);
      readNum.textContent = rs+'%';
      const badgeTxt = rs>=80 ? 'Very Easy To Read' : (rs>=60 ? 'Good — Needs More Improvement' : 'Needs Improvement in Content');
      readBadge.textContent = badgeTxt;
      readBadge.className = 'pill ' + (rs>=80?'score-pill--green':rs>=60?'score-pill--orange':'score-pill--red');
      const grade = (typeof r.grade==='number') ? r.grade : null;
      gradeBadge.textContent = 'Grade ' + (grade ?? '—');
      statF.textContent = (r.flesch ?? '—'); statG.textContent = 'Grade ' + (grade ?? '—');
      rbLegend.textContent = (r.language==='non-latin' ? 'Non-Latin content (LIX-based) — العربية/others supported' : 'Latin-like content — English & similar');
      setMeter(rbFleschFill, rbFleschVal, r.flesch, (r.flesch??'—'), [0,100], false);
      setMeter(rbASLFill,    rbASLVal,    r.avg_sentence_len, (r.avg_sentence_len??'—'), [10,30], true);
      setMeter(rbWordsFill,  rbWordsVal,  r.word_count, (r.word_count??'—'), [0,2000], false);
      setMeter(rbSyllFill,   rbSyllVal,   r.avg_syllables_per_word, (r.avg_syllables_per_word??'—'), [1.2,2.2], true);
      setMeter(rbTTRFill,    rbTTRVal,    r.ttr, ((r.ttr!=null?r.ttr+'%':'—')), [0,100], false);
      setMeter(rbTriFill,    rbTriVal,    r.repetition_trigram, ((r.repetition_trigram!=null?r.repetition_trigram+'%':'—')), [0,20], true);
      setMeter(rbDigitsFill, rbDigitsVal, r.digits_per_100w, (r.digits_per_100w??'—'), [0,20], true);
      setMeter(rbPassiveFill,rbPassiveVal,r.passive_ratio, ((r.passive_ratio!=null?r.passive_ratio+'%':'—')), [0,30], true);
      setMeter(rbSimpleFill, rbSimpleVal, r.simple_words_ratio, ((r.simple_words_ratio!=null?r.simple_words_ratio+'%':'—')), [60,100], false);
      buildFixes(r);

      // ====== PageSpeed Insights (Mobile + Desktop) ======
      const psi = await callPSI(url);
      window.__lastData.psi = psi;

      // default render: Mobile
      renderSpeed('mobile', psi.mobile);

      // Toggle buttons
      tabMobile.addEventListener('click', ()=>{
        tabMobile.classList.add('active'); tabDesktop.classList.remove('active');
        renderSpeed('mobile', psi.mobile);
      });
      tabDesktop.addEventListener('click', ()=>{
        tabDesktop.classList.add('active'); tabMobile.classList.remove('active');
        renderSpeed('desktop', psi.desktop);
      });

      /* Structure */
      titleVal.textContent=data.content_structure?.title||'—';
      metaVal.textContent=data.content_structure?.meta_description||'—';
      const hs=data.content_structure?.headings||{};
      chipH.textContent=`H1:${(hs.H1||[]).length} • H2:${(hs.H2||[]).length} • H3:${(hs.H3||[]).length}`;
      headingMap.innerHTML='';
      Object.entries(hs).forEach(([lvl,arr])=>{
        if(!arr||!arr.length)return;
        const box=document.createElement('div'); box.className='card';
        box.innerHTML=`<div style="font-size:12px;color:#b6c2cf;margin-bottom:6px" class="uppercase">${lvl}</div>`+arr.map(t=>`<div>• ${t}</div>`).join('');
        headingMap.appendChild(box);
      });

      /* Status chips */
      chipHttp.textContent='200';
      chipCanon.textContent=(data.page_signals?.canonical||'—')||'—';
      chipRobots.textContent=(data.page_signals?.robots||'—')||'—';
      chipViewport.textContent=data.page_signals?.has_viewport ? 'yes' : '—';
      chipIntChip.textContent=data.quick_stats?.internal_links??0;
      chipSchema.textContent=(data.page_signals?.schema_types||[]).length;

      /* Recommendations */
      recsEl.innerHTML='';
      (data.recommendations||[]).forEach(rec=>{
        const d=document.createElement('div'); d.className='card';
        d.innerHTML=`<span class="pill" style="margin-right:6px">${rec.severity}</span>${rec.text}`;
        recsEl.appendChild(d);
      });

      /* Semantic Ground */
      renderCategories(data, url, '');

    }catch(err){
      console.error(err);
      showError('Analyze failed.', String(err.message||err));
    }finally{
      setRunning(false);
    }
  }

  analyzeBtn?.addEventListener('click', e=>{ e.preventDefault(); runAnalyze(); });
  urlInput?.addEventListener('keydown', e=>{ if(e.key==='Enter'){ e.preventDefault(); runAnalyze(); } });

  /* backdrop close */
  $('#improveModal')?.addEventListener('click',e=>{
    const modal=e.currentTarget;
    const r=modal.getBoundingClientRect();
    const inside=(e.clientX>=r.left&&e.clientX<=r.right&&e.clientY>=r.top&&e.clientY<=r.bottom);
    if(!inside){ if(typeof modal.close==='function')modal.close(); else modal.removeAttribute('open'); }
  });
});
</script>
@endpush
