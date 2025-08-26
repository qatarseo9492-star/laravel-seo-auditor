<?php
/**
 * Human vs AI Content (Ensemble) — Stylish Multilingual v4
 * Drop-in Blade/PHP view.
 * - Multilingual UI (EN, UR, DE, ES) with client-side switch + RTL for Urdu.
 * - New gradient UI, colorful headings, fresh inline SVG icons.
 * - Pinterest-like gradient score wheel (SVG + conic background) with smooth animation.
 * - No external JS libraries required. Icons are inline SVGs.
 *
 * Server variables (optional):
 *   $ensembleScore  (0-100) final ensemble score
 *   $aiPercent      (0-100) AI content percentage
 *   $humanPercent   (0-100) Human content percentage
 *   $votes          (array|string) e.g., ['GPTZero'=>'Human', 'Originality'=>'AI', ...]
 */
?>
<?php
  $ensemble = isset($ensembleScore) ? (int)$ensembleScore : 72;
  $aiP = isset($aiPercent) ? (int)$aiPercent : 48;
  $humanP = isset($humanPercent) ? (int)$humanPercent : (100 - $aiP);
  $votesData = isset($votes) ? $votes : [
    'GPTZero'      => 'Human',
    'Originality'  => 'AI',
    'ContentScale' => 'Human',
    'Writer.com'   => 'Human',
    'Sapling'      => 'AI'
  ];
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Human vs AI Content (Ensemble) — Stylish Multilingual v4</title>
  <style>
    :root{
      --bg-1:#0f1021;
      --bg-2:#1b1d38;
      --glass:rgba(255,255,255,.06);
      --glass-strong:rgba(255,255,255,.10);
      --txt:#e8eaf6;
      --muted:#c7c9d9;
      --chip:#14162c;
      --ok:#22c55e;
      --warn:#f59e0b;
      --bad:#ef4444;
      --ring-track:rgba(255,255,255,.12);
      --ring-size: 220px;
      --ring-stroke: 18;
      --shadow: 0 10px 30px rgba(0,0,0,.35);
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;background: radial-gradient(1200px 1200px at 20% 10%, #3b82f6 0%, transparent 45%),
                         radial-gradient(900px 900px at 90% 30%, #f43f5e 0%, transparent 45%),
                         radial-gradient(1100px 1100px at 30% 90%, #22c55e 0%, transparent 45%),
                         linear-gradient(135deg,var(--bg-1),var(--bg-2));
      color:var(--txt); font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
      min-height:100%; position:relative; overflow-x:hidden;
    }
    /* animated color drift */
    body::before{
      content:""; position:absolute; inset:-20%; z-index:-1;
      background: conic-gradient(from 0deg, #60a5fa, #f472b6, #f59e0b, #34d399, #60a5fa);
      filter: blur(120px) saturate(120%);
      animation: spin 16s linear infinite;
      opacity:.22;
    }
    @keyframes spin{to{transform:rotate(360deg)}}

    .container{max-width:1200px;margin:40px auto 80px;padding:0 20px}
    .header{
      display:flex; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:22px
    }
    .title{
      font-size:clamp(22px,3vw,36px); line-height:1.1; font-weight:800;
      background: linear-gradient(90deg,#a78bfa,#f472b6,#60a5fa,#34d399);
      -webkit-background-clip:text; background-clip:text; color:transparent;
      letter-spacing:.3px; display:flex; align-items:center; gap:10px
    }
    .subtitle{color:var(--muted); margin-top:4px}
    .header-tools{margin-left:auto; display:flex; gap:10px; align-items:center; flex-wrap:wrap}
    .select, .btn{
      appearance:none; background:var(--glass); color:var(--txt);
      border:1px solid rgba(255,255,255,.08); border-radius:14px;
      padding:10px 14px; font-weight:600; cursor:pointer; box-shadow:var(--shadow);
    }
    .btn:hover, .select:hover{background:var(--glass-strong)}
    .grid{display:grid; grid-template-columns: 1.1fr .9fr; gap:20px}
    @media (max-width:980px){ .grid{grid-template-columns:1fr} }
    .card{
      background:linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03));
      border:1px solid rgba(255,255,255,.08); border-radius:22px; padding:18px; box-shadow:var(--shadow);
    }
    .section-title{
      display:flex; align-items:center; gap:10px; font-weight:800; letter-spacing:.3px;
      font-size:clamp(16px,2vw,20px);
      background:linear-gradient(90deg,#f59e0b,#f43f5e,#8b5cf6);
      -webkit-background-clip:text; background-clip:text; color:transparent;
      margin:0 0 14px 0;
    }
    .chips{display:flex; flex-wrap:wrap; gap:8px}
    .chip{
      display:inline-flex; align-items:center; gap:8px; padding:8px 12px;
      background:linear-gradient(180deg, rgba(255,255,255,.10), rgba(255,255,255,.04));
      border:1px solid rgba(255,255,255,.10); border-radius:999px; font-weight:600
    }
    .chip svg{opacity:.95}
    .muted{color:var(--muted)}

    /* WHEEL */
    .wheel-wrap{
      display:grid; place-items:center; padding:22px;
      background: radial-gradient(600px 600px at 15% 10%, rgba(96,165,250,.18), transparent 45%),
                  radial-gradient(700px 700px at 85% 30%, rgba(244,63,94,.18), transparent 45%),
                  linear-gradient(180deg, rgba(255,255,255,.04), rgba(255,255,255,.02));
      border:1px solid rgba(255,255,255,.08); border-radius:22px;
    }
    .wheel{
      position:relative; width:var(--ring-size); height:var(--ring-size);
      display:grid; place-items:center;
      filter: drop-shadow(0 30px 24px rgba(0,0,0,.35));
    }
    /* conic rainbow backdrop inside the ring */
    .wheel .backdrop{
      position:absolute; inset:0; border-radius:50%;
      background: conic-gradient(from 0deg, #06b6d4, #3b82f6, #a78bfa, #f472b6, #f59e0b, #34d399, #06b6d4);
      animation: spin 6s linear infinite;
      mask: radial-gradient(farthest-side, transparent calc(50% - var(--ring-stroke)*1px - 8px), #000 calc(50% - var(--ring-stroke)*1px - 6px), #000 60%, transparent 62%);
      opacity:.75;
    }
    .wheel svg{position:relative; display:block}
    .wheel .center{
      position:absolute; inset:0; display:grid; place-items:center; text-align:center; pointer-events:none;
    }
    .center .big{
      font-size:clamp(34px,5vw,46px); font-weight:900; letter-spacing:.5px
    }
    .center .label{color:var(--muted); font-weight:700; letter-spacing:.5px; margin-top:2px}
    .legend{
      display:flex; gap:14px; justify-content:center; margin-top:14px; flex-wrap:wrap
    }
    .legend .dot{width:10px; height:10px; border-radius:50%}
    .legend .ai{background:#ef4444}.legend .human{background:#22c55e}
    .legend .value{font-weight:800}

    /* vote list */
    .votes{display:grid; gap:10px}
    .vote-item{display:flex; align-items:center; justify-content:space-between; gap:10px; padding:10px 12px; background:var(--glass); border:1px solid rgba(255,255,255,.08); border-radius:14px}
    .vote-item b{letter-spacing:.3px}
    .badge{
      padding:6px 10px; border-radius:999px; font-weight:800; letter-spacing:.3px
    }
    .is-human{background:rgba(34,197,94,.12); color:#34d399; border:1px solid rgba(34,197,94,.35)}
    .is-ai{background:rgba(239,68,68,.12); color:#ef4444; border:1px solid rgba(239,68,68,.35)}

    .footer{
      margin-top:18px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; justify-content:space-between
    }

    /* RTL support */
    [dir="rtl"] .header{flex-direction:row-reverse}
    [dir="rtl"] .header-tools{margin-left:0; margin-right:auto}
    [dir="rtl"] .vote-item{flex-direction:row-reverse}
  </style>
</head>
<body>
  <div class="container" id="app" data-score="<?php echo $ensemble; ?>" data-ai="<?php echo $aiP; ?>" data-human="<?php echo $humanP; ?>">
    <div class="header">
      <div class="title">
        <!-- Inline Icon: Human vs AI -->
        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M15 7a4 4 0 1 0-6 0"></path>
          <path d="M3 21a7 7 0 0 1 18 0"></path>
          <circle cx="18" cy="6" r="3"></circle>
          <path d="M18 9v3"></path>
        </svg>
        <span data-i18n="title">Human vs AI Content (Ensemble)</span>
      </div>
      <div class="subtitle muted" data-i18n="subtitle">Multimodel detection with colorful insights</div>

      <div class="header-tools">
        <label for="lang" class="muted" data-i18n="language">Language</label>
        <select id="lang" class="select" aria-label="Language">
          <option value="en">English</option>
          <option value="ur">اردو</option>
          <option value="de">Deutsch</option>
          <option value="es">Español</option>
        </select>
        <button class="btn" id="printBtn">
          <!-- printer icon -->
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M6 9V2h12v7"></path><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><path d="M6 14h12v8H6z"></path>
          </svg>
          <span data-i18n="print">Print</span>
        </button>
      </div>
    </div>

    <div class="grid">
      <!-- LEFT: Score Wheel + legend -->
      <div class="card wheel-wrap">
        <h3 class="section-title">
          <!-- gauge icon -->
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10 21a8 8 0 1 1 4 0"></path><path d="M10 21l2-2 2 2"></path><path d="M12 14l4-4"></path>
          </svg>
          <span data-i18n="ensembleScore">Ensemble Score</span>
        </h3>
        <div class="wheel" id="ensembleWheel" role="img" aria-label="Ensemble score">
          <div class="backdrop" aria-hidden="true"></div>
          <svg id="wheelSvg" width="100%" height="100%" viewBox="0 0 200 200">
            <defs>
              <linearGradient id="rainbow" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#06b6d4"/><stop offset="16%" stop-color="#3b82f6"/>
                <stop offset="33%" stop-color="#a78bfa"/><stop offset="50%" stop-color="#f472b6"/>
                <stop offset="66%" stop-color="#f59e0b"/><stop offset="83%" stop-color="#34d399"/>
                <stop offset="100%" stop-color="#06b6d4"/>
              </linearGradient>
              <filter id="glow" x="-50%" y="-50%" width="200%" height="200%">
                <feGaussianBlur stdDeviation="3" result="coloredBlur"/>
                <feMerge>
                  <feMergeNode in="coloredBlur"/>
                  <feMergeNode in="SourceGraphic"/>
                </feMerge>
              </filter>
            </defs>
            <?php
              $cx = 100; $cy = 100; $r = 80; // inner radius
              $circ = 2 * M_PI * $r;
              $stroke = 16;
            ?>
            <!-- track -->
            <circle cx="<?php echo $cx; ?>" cy="<?php echo $cy; ?>" r="<?php echo $r; ?>" stroke="var(--ring-track)"
                    stroke-width="<?php echo $stroke; ?>" fill="none" />
            <!-- progress (dynamic dash) -->
            <circle id="progressArc" cx="<?php echo $cx; ?>" cy="<?php echo $cy; ?>" r="<?php echo $r; ?>"
                    stroke="url(#rainbow)" stroke-width="<?php echo $stroke; ?>" fill="none"
                    stroke-linecap="round"
                    transform="rotate(-90 <?php echo $cx; ?> <?php echo $cy; ?>)"
                    filter="url(#glow)"
                    stroke-dasharray="<?php echo $circ; ?>"
                    stroke-dashoffset="<?php echo $circ; ?>" />
            <!-- outer thin ring for accent -->
            <circle cx="<?php echo $cx; ?>" cy="<?php echo $cy; ?>" r="<?php echo $r + 14; ?>"
                    stroke="url(#rainbow)" stroke-opacity=".25" stroke-width="2" fill="none"
                    transform="rotate(90 <?php echo $cx; ?> <?php echo $cy; ?>)"/>
          </svg>
          <div class="center">
            <div class="big" id="scoreText"><?php echo $ensemble; ?>%</div>
            <div class="label"><span data-i18n="confidence">Confidence</span></div>
          </div>
        </div>

        <div class="legend">
          <div style="display:flex;align-items:center;gap:8px">
            <span class="dot ai"></span>
            <span class="muted" data-i18n="ai">AI</span> <span class="value" id="aiVal"><?php echo $aiP; ?>%</span>
          </div>
          <div style="display:flex;align-items:center;gap:8px">
            <span class="dot human"></span>
            <span class="muted" data-i18n="human">Human</span> <span class="value" id="humanVal"><?php echo $humanP; ?>%</span>
          </div>
        </div>
      </div>

      <!-- RIGHT: Insights + votes -->
      <div class="card">
        <h3 class="section-title">
          <!-- sparkles icon -->
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M12 3l2.4 4.8L20 10l-5.6 2.2L12 17l-2.4-4.8L4 10l5.6-2.2L12 3z"></path>
            <path d="M5 19l1 2 2 1-2 1-1 2-1-2-2-1 2-1 1-2zM18 19l.8 1.6L21 22l-2.2.9L18 25l-.8-2.1L15 22l2.2-1.4L18 19z"></path>
          </svg>
          <span data-i18n="colorfulInsights">Colorful Insights</span>
        </h3>

        <div class="chips" style="margin-bottom:12px">
          <div class="chip"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 1 1 7 7M14 7a5 5 0 1 0-7 7"></path></svg> <span data-i18n="style">Style</span></div>
          <div class="chip"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 12h16M4 6h16M4 18h16"></path></svg> <span data-i18n="readability">Readability</span></div>
          <div class="chip"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline></svg> <span data-i18n="seo">SEO</span></div>
          <div class="chip"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="9"></circle><path d="M3.6 9h16.8M3.6 15h16.8M12 3.6v16.8"></path></svg> <span data-i18n="semantics">Semantics</span></div>
        </div>

        <div class="votes">
          <?php foreach($votesData as $tool => $verdict): ?>
            <?php $isHuman = strtolower($verdict)==='human'; ?>
            <div class="vote-item">
              <b><?php echo htmlspecialchars($tool); ?></b>
              <span class="badge <?php echo $isHuman? 'is-human' : 'is-ai'; ?>">
                <?php echo $isHuman ? 'Human' : 'AI'; ?>
              </span>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="footer">
          <div class="muted" data-i18n="note">Tip: Use the language switch for instant translation. Urdu uses RTL layout.</div>
          <div style="display:flex; gap:8px">
            <button class="btn" id="copyBtn">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
              <span data-i18n="copySummary">Copy Summary</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    (function(){
      const elApp = document.getElementById('app');
      const score = Number(elApp.dataset.score || 0);
      const ai = Number(elApp.dataset.ai || 0);
      const human = Number(elApp.dataset.human || (100-ai));

      const circ = 2 * Math.PI * 80; // must match r in SVG
      const offset = Math.max(0, circ - (circ * score / 100));
      const arc = document.getElementById('progressArc');
      const scoreText = document.getElementById('scoreText');
      const aiVal = document.getElementById('aiVal');
      const humanVal = document.getElementById('humanVal');
      arc.style.strokeDashoffset = offset.toFixed(2);
      scoreText.textContent = score + '%';
      aiVal.textContent = ai + '%';
      humanVal.textContent = human + '%';

      // Language
      const STRINGS = {
        en:{
          title:"Human vs AI Content (Ensemble)",
          subtitle:"Multimodel detection with colorful insights",
          language:"Language",
          print:"Print",
          ensembleScore:"Ensemble Score",
          confidence:"Confidence",
          ai:"AI",
          human:"Human",
          colorfulInsights:"Colorful Insights",
          style:"Style",
          readability:"Readability",
          seo:"SEO",
          semantics:"Semantics",
          note:"Tip: Use the language switch for instant translation. Urdu uses RTL layout.",
          copySummary:"Copy Summary"
        },
        ur:{
          title:"ہیومن بمقابلہ اے آئی مواد (اینسمبل)",
          subtitle:"رنگین بصیرت کے ساتھ ملٹی ماڈل ڈیٹیکشن",
          language:"زبان",
          print:"پرنٹ",
          ensembleScore:"اینسمبل اسکور",
          confidence:"اعتماد",
          ai:"اے آئی",
          human:"ہیومن",
          colorfulInsights:"رنگین بصیرت",
          style:"انداز",
          readability:"قابلِ مطالعہٴ",
          seo:"ایس ای او",
          semantics:"سیمانٹکس",
          note:"اشارہ: زبان سوئچ استعمال کریں۔ اردو کے لیے دائیں سے بائیں ترتیب فعال ہے۔",
          copySummary:"خلاصہ کاپی کریں"
        },
        de:{
          title:"Mensch vs. KI-Inhalt (Ensemble)",
          subtitle:"Multimodale Erkennung mit farbigen Einblicken",
          language:"Sprache",
          print:"Drucken",
          ensembleScore:"Ensemble‑Wert",
          confidence:"Konfidenz",
          ai:"KI",
          human:"Mensch",
          colorfulInsights:"Farbige Einblicke",
          style:"Stil",
          readability:"Lesbarkeit",
          seo:"SEO",
          semantics:"Semantik",
          note:"Tipp: Sprache oben wechseln. Für Urdu gilt RTL‑Layout.",
          copySummary:"Zusammenfassung kopieren"
        },
        es:{
          title:"Contenido Humano vs IA (Ensemble)",
          subtitle:"Detección multimodelo con ideas coloridas",
          language:"Idioma",
          print:"Imprimir",
          ensembleScore:"Puntuación del Ensemble",
          confidence:"Confianza",
          ai:"IA",
          human:"Humano",
          colorfulInsights:"Ideas coloridas",
          style:"Estilo",
          readability:"Legibilidad",
          seo:"SEO",
          semantics:"Semántica",
          note:"Consejo: Cambia el idioma arriba. Urdu usa diseño RTL.",
          copySummary:"Copiar resumen"
        }
      };

      const langSel = document.getElementById('lang');
      const saved = localStorage.getItem('hvai_lang') || 'en';
      if(STRINGS[saved]) langSel.value = saved;
      applyLang(langSel.value);
      langSel.addEventListener('change', e => applyLang(e.target.value));

      function applyLang(code){
        const map = STRINGS[code] || STRINGS.en;
        document.querySelectorAll('[data-i18n]').forEach(el=>{
          const key = el.getAttribute('data-i18n');
          if(map[key]) el.textContent = map[key];
        });
        document.documentElement.setAttribute('lang', code);
        document.documentElement.setAttribute('dir', code==='ur' ? 'rtl':'ltr');
        localStorage.setItem('hvai_lang', code);
      }

      // Copy summary (score + ai/human)
      document.getElementById('copyBtn')?.addEventListener('click', async ()=>{
        const text = `${STRINGS[langSel.value].ensembleScore}: ${score}% • ${STRINGS[langSel.value].ai}: ${ai}% • ${STRINGS[langSel.value].human}: ${human}%`;
        try{
          await navigator.clipboard.writeText(text);
          flash('Copied!');
        }catch(e){ flash('Copied'); }
      });

      // Print
      document.getElementById('printBtn')?.addEventListener('click', ()=> window.print());

      function flash(msg){
        const n = document.createElement('div');
        n.textContent = msg;
        Object.assign(n.style,{
          position:'fixed', left:'50%', top:'18px', transform:'translateX(-50%)',
          background:'rgba(0,0,0,.7)', color:'#fff', padding:'8px 12px',
          borderRadius:'10px', fontWeight:'800', zIndex:9999, boxShadow:'0 8px 20px rgba(0,0,0,.35)'
        });
        document.body.appendChild(n);
        setTimeout(()=> n.remove(), 1200);
      }
    })();
  </script>
</body>
</html>
