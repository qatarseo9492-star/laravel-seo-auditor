<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title>Semantic SEO Master Analyzer</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet"/>

  <style>
    /* ===== Base ===== */
    :root{
      --bg:#0b0a13;
      --card:#131126;
      --card-2:#171533;
      --text:#f7f7fb;
      --muted:#cfd3ff;
      --ring:0 0 0 3px rgba(155,92,255,.35);
      --ring-strong:0 0 0 4px rgba(61,226,255,.33);
      --btn-border:rgba(255,255,255,.18);
      --btn-bg:rgba(255,255,255,.06);
      --accent:#9b5cff;
      --accent2:#3de2ff;
      --danger:#ff2045;
      --good:#22c55e;
      --warn:#f59e0b;
      --bad:#ef4444;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0; font-family:Inter,system-ui,Segoe UI,Roboto,Arial;
      background:radial-gradient(1200px 600px at 20% -10%, rgba(155,92,255,.25), transparent 60%),
                 radial-gradient(1000px 500px at 120% 10%, rgba(61,226,255,.22), transparent 55%),
                 var(--bg);
      color:var(--text);
    }
    .wrap{max-width:1100px;margin:0 auto;padding:28px 16px 64px}

    /* Smoke cloud background layer */
    .bg-smoke::before{
      content:""; position:fixed; inset:0; z-index:-1;
      background:
        radial-gradient(800px 420px at 70% 10%, rgba(146,81,255,.20), transparent 60%),
        radial-gradient(700px 320px at 10% 90%, rgba(61,226,255,.16), transparent 65%),
        url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="800"><defs><radialGradient id="g" cx="50%" cy="50%" r="50%"><stop offset="0%" stop-color="rgba(255,255,255,0.05)"/><stop offset="100%" stop-color="rgba(0,0,0,0)"/></radialGradient></defs><rect width="1200" height="800" fill="url(%23g)"/></svg>') repeat;
      opacity:.8; filter: blur(22px);
    }

    /* ===== Cards / Panels ===== */
    .panel{
      background:linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08);
      border-radius:18px; padding:20px; box-shadow:0 10px 30px rgba(0,0,0,.25);
      margin:18px 0;
    }
    .hero .title{font-weight:900; font-size:clamp(1.6rem, 3.2vw, 2.2rem); letter-spacing:.2px}
    .subtitle{color:var(--muted); margin:.3rem 0 0}

    .panel-head{display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:12px}
    .grid2{display:grid; grid-template-columns:1.2fr .8fr; gap:18px}
    @media (max-width: 900px){ .grid2{ grid-template-columns:1fr } }

    .label{font-weight:700; margin:8px 0}
    .input-row{display:flex; gap:10px; align-items:center}
    .row{display:flex; gap:10px; align-items:center; flex-wrap:wrap}

    /* ===== Inputs / Outlines ===== */
    .input{
      width:100%; padding:.78rem .9rem; background:rgba(255,255,255,.06);
      border:1px solid rgba(255,255,255,.18); border-radius:12px; color:var(--text);
      outline:none; transition:box-shadow .15s,border-color .15s,background .2s;
    }
    .input::placeholder{color:#acb2ff}
    .input:focus{ box-shadow:var(--ring); border-color:rgba(155,92,255,.7); background:rgba(255,255,255,.08)}
    .outline{ backdrop-filter: blur(4px) }

    /* ===== Buttons & Outlines 2.0 ===== */
    .btn{
      display:inline-flex; align-items:center; justify-content:center; gap:.55rem;
      font-weight:900; letter-spacing:.2px; cursor:pointer; user-select:none;
      padding:.6rem .95rem; border-radius:12px; border:1px solid var(--btn-border);
      background:var(--btn-bg); color:#fff; transition:transform .12s ease, box-shadow .2s, background .2s, border-color .2s;
    }
    .btn:hover{ transform:translateY(-1px); background:rgba(255,255,255,.10) }
    .btn:active{ transform:translateY(0) }
    .btn:disabled{ opacity:.6; cursor:not-allowed }
    .btn .i{ width:18px; height:18px; display:inline-block; fill:currentColor }

    .btn-solid{ background:linear-gradient(135deg,#7c4dff,#ff2045); border-color:transparent; }
    .btn-primary{ background:linear-gradient(135deg,#3de2ff33,#9b5cff33) }
    .btn-secondary{ background:linear-gradient(135deg,#a7f3d033,#60a5fa33) }
    .btn-danger{ background:linear-gradient(135deg,#ff204533,#ff8a5b33) }
    .btn-ghost{ background:transparent }
    .btn-outline{ background:transparent; border:1px solid rgba(255,255,255,.28) }
    .btn-outline-accent{ border-color:var(--accent2) }
    .btn-outline-primary{ border-color:var(--accent) }

    .btn-sm{ padding:.4rem .7rem; border-radius:10px; font-weight:800 }
    .btn-lg{ padding:.85rem 1.15rem; border-radius:14px; font-size:1.05rem }

    .btn.is-loading{ position:relative; pointer-events:none; }
    .btn.is-loading::after{
      content:""; position:absolute; inset:0; margin:auto; width:1.05em; height:1.05em;
      border-radius:50%; border:2px solid rgba(255,255,255,.55); border-top-color:transparent; animation:spin .65s linear infinite;
    }
    @keyframes spin{to{transform:rotate(360deg)}}

    /* Button group */
    .btn-group{ display:flex; flex-wrap:wrap; gap:8px }
    .seg{ display:inline-flex; border:1px solid rgba(255,255,255,.25); border-radius:12px; overflow:hidden }
    .seg button{ border:0; background:transparent; padding:.55rem .85rem; color:#fff; font-weight:800 }
    .seg button[aria-pressed="true"]{ background:rgba(155,92,255,.25) }

    /* Score card */
    .score-card{ background:var(--card); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:16px; text-align:center }
    .score-badge{ font-weight:900; font-size:clamp(2rem,4.2vw,3rem) }
    .ai-human{ display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-top:10px; font-weight:700; color:var(--muted) }
    @media (max-width:500px){ .ai-human{ grid-template-columns:1fr } }

    /* Tips */
    .tips{ margin-top:12px }
    .tip{ background:var(--card-2); border:1px solid rgba(255,255,255,.08); border-left:4px solid var(--accent2);
          padding:10px 12px; border-radius:12px; margin:8px 0; color:#dfe3ff }
    .tip small{ color:#9fb5ff }

    /* Footer social */
    .footer{ display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:22px; color:var(--muted) }
    .social{ display:flex; gap:8px }
    .icon-btn{ width:42px; height:42px; border-radius:12px; background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.15);
               display:grid; place-items:center; transition:transform .12s, filter .2s }
    .icon-btn:hover{ transform:translateY(-2px); filter:saturate(120%) }
    .tw{ color:#1DA1F2 } .fb{ color:#1877F2 } .yt{ color:#FF0000 } .ig{ color:#E1306C }

    /* Utilities */
    .mt-2{margin-top:.5rem} .mt-3{margin-top:.75rem} .mt-4{margin-top:1rem}
  </style>
</head>
<body class="bg-smoke">
  <div class="wrap">
    <header class="hero panel">
      <div>
        <h1 class="title">Semantic SEO Master Analyzer</h1>
        <p class="subtitle">Analyze any page, get instant suggestions, and track score with a live wheel.</p>
      </div>
    </header>

    <section class="panel">
      <div class="panel-head">
        <h2 style="margin:0">URL Analyzer</h2>
        <div class="row">
          <label for="lang" class="label" style="margin:0">Language</label>
          <select id="lang" class="input outline" style="width:auto">
            <option value="en">English</option>
            <option value="ur">اردو (Urdu)</option>
            <option value="es">Español</option>
            <option value="pt">Português</option>
            <option value="de">Deutsch</option>
            <option value="ar">العربية</option>
            <option value="fr">Français</option>
            <option value="tr">Türkçe</option>
            <option value="ru">Русский</option>
            <option value="hi">हिन्दी</option>
          </select>
        </div>
      </div>

      <div class="grid2">
        <div>
          <label class="label">Page URL</label>
          <div class="input-row">
            <input id="url" type="text" class="input outline" placeholder="https://example.com/page"/>
            <button id="btnAnalyze" class="btn btn-solid">
              <svg class="i" viewBox="0 0 24 24" aria-hidden="true"><path d="M11 4a7 7 0 0 1 5.657 11.314l3.514 3.515-1.414 1.414-3.515-3.514A7 7 0 1 1 11 4zm0 2a5 5 0 1 0 .001 10.001A5 5 0 0 0 11 6z"/></svg>
              Analyze
            </button>
            <button id="btnReset" class="btn btn-outline btn-outline-accent">Reset</button>
            <button id="btnPrint" class="btn btn-outline btn-outline-primary">Print</button>
            <button id="btnExport" class="btn btn-ghost">Export</button>
          </div>

          <div id="tips" class="tips mt-3"></div>
        </div>

        <div class="score-card">
          <canvas id="scoreWheel" width="220" height="220" aria-label="Score wheel" style="display:block;margin:0 auto"></canvas>
          <div class="score-badge" id="scoreBadge">—</div>
          <div class="ai-human">
            <div><strong>AI‑likelihood:</strong> <span id="aiPct">—</span>%</div>
            <div><strong>Human‑likelihood:</strong> <span id="humanPct">—</span>%</div>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <div class="seg" role="group" aria-label="View">
          <button type="button" aria-pressed="true" id="segOverview">Overview</button>
          <button type="button" aria-pressed="false" id="segTech">Technical</button>
          <button type="button" aria-pressed="false" id="segContent">Content</button>
        </div>
      </div>
    </section>

    <section class="panel">
      <h2 style="margin-top:0">Quick Actions</h2>
      <div class="btn-group">
        <button class="btn btn-primary">Fix Titles</button>
        <button class="btn btn-secondary">Compress Images</button>
        <button class="btn btn-danger">Remove Thin Pages</button>
        <button class="btn btn-outline">Rebuild Sitemap</button>
      </div>
    </section>

    <footer class="footer">
      <small>© {{ date('Y') }} Semantic SEO Master Analyzer</small>
      <div class="social">
        <a class="icon-btn tw" href="#" aria-label="Twitter">
          <svg class="i" viewBox="0 0 24 24"><path d="M22 5.8c-.7.3-1.4.5-2.2.6.8-.5 1.3-1.2 1.6-2-.8.5-1.7.9-2.7 1.1A3.9 3.9 0 0 0 12 7.8c0 .3 0 .6.1.8-3.3-.2-6.2-1.8-8.2-4.2-.3.6-.4 1.2-.4 1.9 0 1.3.7 2.5 1.8 3.2-.6 0-1.2-.2-1.7-.5 0 1.9 1.4 3.6 3.2 4-.3.1-.6.1-.9.1-.2 0-.4 0-.6-.1.4 1.7 2 3 3.8 3.1A7.9 7.9 0 0 1 2 18.4 11.2 11.2 0 0 0 8.1 20c7.3 0 11.3-6 11.3-11.3v-.5c.8-.5 1.4-1.2 1.9-2z"/></svg>
        </a>
        <a class="icon-btn fb" href="#" aria-label="Facebook">
          <svg class="i" viewBox="0 0 24 24"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7h-2.3V12h2.3V9.7c0-2.3 1.3-3.5 3.3-3.5.9 0 1.9.2 1.9.2v2.1h-1c-1 0-1.4.6-1.4 1.3V12h2.4l-.4 2.9H14v7A10 10 0 0 0 22 12z"/></svg>
        </a>
        <a class="icon-btn yt" href="#" aria-label="YouTube">
          <svg class="i" viewBox="0 0 24 24"><path d="M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.4.6a3 3 0 0 0-2.1 2.1C0 8.1 0 12 0 12s0 3.9.5 5.8a3 3 0 0 0 2.1 2.1C4.5 20.5 12 20.5 12 20.5s7.5 0 9.4-.6a3 3 0 0 0 2.1-2.1C24 15.9 24 12 24 12s0-3.9-.5-5.8zM9.8 15.5V8.5L15.5 12l-5.7 3.5z"/></svg>
        </a>
        <a class="icon-btn ig" href="#" aria-label="Instagram">
          <svg class="i" viewBox="0 0 24 24"><path d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5zm5 5a5 5 0 1 0 .001 10.001A5 5 0 0 0 12 7zm6.5-.9a1.1 1.1 0 1 0 0 2.2 1.1 1.1 0 0 0 0-2.2zM12 9a3 3 0 1 1 0 6 3 3 0 0 1 0-6z"/></svg>
        </a>
      </div>
    </footer>
  </div>

  <script>
    // ===== Language auto-select by user agent / browser
    (function(){
      const sel = document.getElementById('lang');
      const navLang = (navigator.language || navigator.userLanguage || 'en').slice(0,2).toLowerCase();
      const opts = Array.from(sel.options).map(o=>o.value);
      if (opts.includes(navLang)) sel.value = navLang;
    })();

    // ===== Score wheel
    function setScoreWheel(canvasId, value){
      const c = document.getElementById(canvasId);
      if(!c) return;
      const ctx = c.getContext('2d');
      const w = c.width, h = c.height, r = Math.min(w,h)/2 - 12;
      const cx = w/2, cy = h/2;
      ctx.clearRect(0,0,w,h);

      // Track
      ctx.lineWidth = 14;
      ctx.strokeStyle = 'rgba(255,255,255,0.15)';
      ctx.beginPath();
      ctx.arc(cx,cy,r, -Math.PI*0.75, Math.PI*0.25);
      ctx.stroke();

      // Color by score
      let col = value>=80 ? '#22c55e' : (value>=60 ? '#f59e0b' : '#ef4444');

      // Arc
      const start = -Math.PI*0.75;
      const end = start + (Math.PI*1.0) * (Math.max(0, Math.min(100, value))/100);
      const grad = ctx.createLinearGradient(0,0,w,h);
      grad.addColorStop(0, col);
      grad.addColorStop(1, '#9b5cff');
      ctx.strokeStyle = grad;
      ctx.lineWidth = 16;
      ctx.lineCap = 'round';
      ctx.beginPath();
      ctx.arc(cx,cy,r,start,end);
      ctx.stroke();

      // Glow ring
      ctx.shadowColor = col;
      ctx.shadowBlur = 16;
      ctx.beginPath();
      ctx.arc(cx,cy,r,start,end);
      ctx.stroke();
      ctx.shadowBlur = 0;
    }

    // ===== Analyze actions
    const urlInput = document.getElementById('url');
    const tipsBox  = document.getElementById('tips');
    const btnAnalyze = document.getElementById('btnAnalyze');
    const btnReset   = document.getElementById('btnReset');
    const btnPrint   = document.getElementById('btnPrint');
    const btnExport  = document.getElementById('btnExport');
    const scoreBadge = document.getElementById('scoreBadge');
    const aiPct = document.getElementById('aiPct');
    const humanPct = document.getElementById('humanPct');

    function renderTips(list){
      tipsBox.innerHTML = '';
      if(!list || !list.length){
        tipsBox.innerHTML = '<div class="tip"><strong>Great!</strong> <small>No suggestions at this time.</small></div>';
        return;
      }
      list.forEach(t=>{
        const el = document.createElement('div');
        el.className = 'tip';
        el.innerHTML = t;
        tipsBox.appendChild(el);
      });
    }

    async function analyze(){
      const url = urlInput.value.trim();
      if(!url){
        urlInput.focus(); urlInput.setAttribute('aria-invalid','true');
        urlInput.style.boxShadow = 'var(--ring-strong)';
        return;
      }
      btnAnalyze.classList.add('is-loading');
      try{
        const endpoint = "{{ route('analyze.json') }}";
        const q = new URLSearchParams({ url }).toString();
        const res = await fetch(endpoint + "?" + q, { method: "GET" });
        const data = await res.json();
        if(data && data.ok){
          const val = Number(data.score||0);
          setScoreWheel('scoreWheel', val);
          scoreBadge.textContent = val;
          aiPct.textContent = (data.ai??'—');
          humanPct.textContent = (data.human??'—');
          renderTips(data.tips||[]);
        }else{
          renderTips([data.error || 'Unexpected error']);
        }
      }catch(err){
        renderTips(['Network error. Please try again.']);
      }finally{
        btnAnalyze.classList.remove('is-loading');
      }
    }

    btnAnalyze.addEventListener('click', analyze);
    btnReset.addEventListener('click', ()=>{
      urlInput.value = '';
      tipsBox.innerHTML = '';
      setScoreWheel('scoreWheel', 0);
      scoreBadge.textContent = '—';
      aiPct.textContent = '—';
      humanPct.textContent = '—';
    });
    btnPrint.addEventListener('click', ()=> window.print());
    btnExport.addEventListener('click', ()=>{
      // Quick JSON export of current results
      const payload = {
        url: urlInput.value.trim(),
        score: document.getElementById('scoreBadge').textContent,
        ai: aiPct.textContent,
        human: humanPct.textContent,
        tips: Array.from(document.querySelectorAll('.tip')).map(x=>x.textContent.trim())
      };
      const blob = new Blob([JSON.stringify(payload,null,2)], {type:'application/json'});
      const a = document.createElement('a');
      a.href = URL.createObjectURL(blob);
      a.download = 'semantic-seo-report.json';
      a.click();
      URL.revokeObjectURL(a.href);
    });

    // Initial wheel
    setScoreWheel('scoreWheel', 0);

    // Segmented control (visual only)
    const segBtns = [document.getElementById('segOverview'), document.getElementById('segTech'), document.getElementById('segContent')];
    segBtns.forEach(b=> b.addEventListener('click', ()=>{
      segBtns.forEach(x=>x.setAttribute('aria-pressed','false'));
      b.setAttribute('aria-pressed','true');
    }));
  </script>
</body>
</html>
