<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Semantic SEO Master — Analyzer</title>
  <!--
    Stylish Layout v4 (Blade-ready, no external deps)
    -----------------------------------------------------------------
    • Clean, modern, accessible dark UI with subtle glass cards
    • Gradient accent, neon badges, conic score ring
    • Zero heavy animations (only lightweight transitions)
    • Works even if $report is missing (shows placeholders)

    EXPECTED DATA SHAPE (from Controller)
    $report = [
      'overall_score' => 82,            // 0–100
      'wheel_label'   => 'Great Work — Well Optimized',
      'quick_stats'   => [
          'words' => 1432,
          'images' => 51,
          'internal' => 184,
          'external' => 6,
          'readability' => 0,          // 0–100 Flesch-like or your metric
          'text_html_ratio' => 1.17,   // % or ratio
      ],
      'structure' => [
          'title' => 'Page Title…',
          'meta_description' => 'Meta description…',
          'headings' => [
            'h1' => ['Main H1…'],
            'h2' => ['Section', 'Another'],
            'h3' => ['Subsection'],
            'h4' => [],
          ],
      ],
      'recommendations' => [
         ['severity' => 'warning', 'text' => 'Add alt text to 14 images'],
         ['severity' => 'info',    'text' => 'Increase main content depth'],
      ],
      'categories' => [
         [
           'name' => 'Content & Keywords', 'score' => 25,
           'items' => [
             ['state' => 'warn', 'label' => 'H1/Title includes primary topic'],
             ['state' => 'warn', 'label' => 'Primary keyword in first paragraph'],
             ['state' => 'fail', 'label' => 'Readable, NLP-friendly language'],
             ['state' => 'fail', 'label' => 'Includes FAQs / questions with answers'],
           ]
         ],
         [
           'name' => 'Technical Elements', 'score' => 94,
           'items' => [
             ['state' => 'pass', 'label' => 'Title tag length ≈50–60 chars', 'value' => 100],
             ['state' => 'warn', 'label' => 'Meta description ≈140–160 chars', 'value' => 70],
             ['state' => 'pass', 'label' => 'Page indexable (no noindex)', 'value' => 100],
             ['state' => 'pass', 'label' => 'Canonical tag set correctly', 'value' => 100],
             ['state' => 'pass', 'label' => 'OpenGraph meta present', 'value' => 100],
           ]
         ],
         [
           'name' => 'Content Quality', 'score' => 80,
           'items' => [
             ['state' => 'pass', 'label' => 'Cites authoritative sources (external links)', 'value' => 100],
             ['state' => 'warn', 'label' => 'Clear author/date (E‑E‑A‑T hint)', 'value' => 60],
           ]
         ],
         [
           'name' => 'Structure & Architecture', 'score' => 88, 'items' => []
         ],
         [
           'name' => 'User Signals & Experience', 'score' => 82, 'items' => []
         ],
         [
           'name' => 'Entities & Context', 'score' => 53, 'items' => []
         ],
      ],
    ];
  -->
  <style>
    :root{
      --bg: #0e0b14;            /* deep plum */
      --bg-2:#141126;
      --card:#1a1630ef;         /* glassy card */
      --card-2:#1f1a39f2;
      --text:#EDEAF7;
      --muted:#B6AFD0;
      --line:#2a2248;
      --accent:#5cf6d4;         /* mint */
      --accent-2:#6aa8ff;       /* azure */
      --accent-3:#b26dff;       /* violet */
      --success:#36d399;
      --warning:#f9c846;
      --danger:#ff6b6b;
      --chip:#241d41;
      --shadow: 0 8px 30px rgba(0,0,0,.35);
      --radius: 18px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;
      background:
        radial-gradient(1200px 600px at -10% -10%, #251a43 0%, transparent 60%),
        radial-gradient(1000px 500px at 110% -10%, #1f3155 0%, transparent 60%),
        linear-gradient(180deg, var(--bg) 0%, var(--bg-2) 100%);
      color:var(--text);
      font: 15px/1.45 ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica, Arial, "Apple Color Emoji","Segoe UI Emoji";
      letter-spacing:.2px;
    }
    a{color:var(--accent-2); text-decoration:none}
    .wrap{max-width:1200px;margin:0 auto;padding:32px 20px 80px}

    /* Header */
    .nav{
      position:sticky;top:0;z-index:30;backdrop-filter:saturate(1.1) blur(10px);
      background:linear-gradient(180deg, rgba(18,14,31,.85), rgba(18,14,31,.65));
      border-bottom:1px solid var(--line);
    }
    .nav-inner{max-width:1200px;margin:0 auto;display:flex;align-items:center;gap:16px;padding:14px 20px}
    .brand{display:flex;align-items:center;gap:12px;font-weight:700}
    .logo{width:28px;height:28px;border-radius:9px;background:conic-gradient(from 210deg, var(--accent) 0 120deg, var(--accent-2) 120deg 240deg, var(--accent-3) 240deg 360deg); box-shadow:0 0 0 2px #fff1 inset}
    .tabs{margin-left:auto;display:flex;gap:8px}
    .tab{padding:9px 12px;border-radius:12px;background:transparent;border:1px solid var(--line);color:var(--muted)}
    .tab.active{background:linear-gradient(180deg,#2a2248,#1f1a39);color:var(--text);border-color:#3a2f61}

    /* Hero */
    .hero{display:flex;flex-direction:column;gap:14px;padding:30px 0 8px}
    .kicker{opacity:.8;font-weight:600;color:var(--muted)}
    .title{font-size:clamp(32px, 4.2vw, 54px);line-height:1.07;margin:0;font-weight:800}
    .subtitle{opacity:.9;max-width:820px}

    /* Analyze form */
    .form{display:grid;grid-template-columns:1fr minmax(280px, 340px) auto;gap:12px;align-items:center;margin-top:10px}
    .field{display:flex;align-items:center;gap:10px;padding:14px 14px;border-radius:14px;background:var(--card);border:1px solid var(--line)}
    .field input{all:unset;flex:1;color:var(--text)}
    .submit{border:0;cursor:pointer;font-weight:750;letter-spacing:.2px;padding:14px 22px;border-radius:14px;transition:.18s ease;display:inline-flex;align-items:center;gap:10px}
    .submit{
      background-image:linear-gradient(135deg, var(--accent) 0%, var(--accent-2) 48%, var(--accent-3) 100%);
      color:#0b0a12; box-shadow:var(--shadow)
    }
    .submit:hover{transform:translateY(-1px)}
    .submit:active{transform:translateY(0)}
    .tip{opacity:.7;font-size:13px;margin-top:8px}

    /* Score + Quick stats */
    .panel{display:grid;grid-template-columns:340px 1fr;gap:18px;margin-top:24px}
    .card{background:var(--card);border:1px solid var(--line);border-radius:var(--radius);box-shadow:var(--shadow)}
    .card .pad{padding:18px}

    .ring-box{display:flex;align-items:center;justify-content:center;min-height:220px}
    .ring{--score: var(--score-val, 0); width:220px; height:220px; border-radius:50%; display:grid; place-items:center; position:relative; background:
      conic-gradient(var(--accent) calc(var(--score)*1%), #2a2248 0);
      box-shadow:0 0 0 12px #141126 inset, 0 0 40px rgba(92,246,212,.15);
    }
    .ring::after{content:""; position:absolute; inset:10px; border-radius:50%; background:#0f0c1d}
    .ring .val{position:relative;font-size:56px;font-weight:800;}
    .ring .caption{position:absolute; bottom:-36px; left:50%; transform:translateX(-50%); font-size:14px; color:var(--muted)}

    .stats{display:grid;grid-template-columns:repeat(6, minmax(0,1fr)); gap:12px}
    .chip{background:var(--chip); border:1px solid #32285a; border-radius:12px; padding:12px 12px}
    .chip .k{opacity:.7; font-size:12px}
    .chip .v{font-weight:700; font-size:18px}
    .chip.bad{outline:1px solid var(--danger)}

    /* Category grid */
    .grid{display:grid;grid-template-columns:repeat(3, 1fr); gap:16px; margin-top:18px}
    .cat{background:var(--card-2); border:1px solid var(--line); border-radius:var(--radius); overflow:hidden}
    .cat-h{display:flex; align-items:center; justify-content:space-between; gap:10px; padding:14px 16px; background:linear-gradient(180deg, #241d41, #1e1836)}
    .cat-name{font-weight:700}
    .badge{padding:6px 10px; border-radius:999px; font-weight:700; font-size:12px; border:1px solid #3a2f61; background:#170f30}
    .badge.success{color:var(--success); border-color:rgba(54,211,153,.4)}
    .badge.warn{color:var(--warning); border-color:rgba(249,200,70,.35)}
    .badge.fail{color:var(--danger); border-color:rgba(255,107,107,.35)}

    .list{display:flex; flex-direction:column; gap:8px; padding:12px 14px 16px}
    .row{display:flex; align-items:center; gap:10px; justify-content:space-between; padding:12px; background:#1a1433; border:1px solid #2d2450; border-radius:12px}
    .row-left{display:flex; align-items:center; gap:10px}
    .dot{width:10px; height:10px; border-radius:50%}
    .dot.pass{background:var(--success)}
    .dot.warn{background:var(--warning)}
    .dot.fail{background:var(--danger)}
    .improve{padding:8px 10px; font-weight:700; border-radius:10px; border:1px solid #3a2f61; background:#150f2a; color:var(--text); cursor:pointer; transition:.18s}
    .improve:hover{transform:translateY(-1px)}

    /* Structure + Recs */
    .two{display:grid; grid-template-columns:1.1fr .9fr; gap:16px; margin-top:18px}
    .hmap{display:grid; grid-template-columns:repeat(2, 1fr); gap:12px}
    .hcol .h{opacity:.8; font-weight:700; margin:8px 0 6px}
    .pill{display:block; padding:10px 12px; border:1px solid #2f2554; border-radius:12px; background:#160f2d; margin:6px 0}

    .recs{display:flex; flex-direction:column; gap:10px}
    .rec{display:flex; justify-content:space-between; align-items:center; gap:10px; border:1px solid #2c234e; background:#160f2d; padding:12px; border-radius:12px}
    .sev{font-weight:800; font-size:12px; padding:5px 10px; border-radius:999px}
    .sev.warning{background:rgba(249,200,70,.12); color:var(--warning); border:1px solid rgba(249,200,70,.35)}
    .sev.critical{background:rgba(255,107,107,.12); color:var(--danger); border:1px solid rgba(255,107,107,.35)}
    .sev.info{background:rgba(106,168,255,.12); color:var(--accent-2); border:1px solid rgba(106,168,255,.35)}

    /* Utilities */
    .muted{color:var(--muted)}
    .sr{position:absolute;left:-9999px}

    /* Responsive */
    @media (max-width: 1100px){
      .panel{grid-template-columns:1fr}
      .stats{grid-template-columns:repeat(3, minmax(0,1fr))}
      .form{grid-template-columns:1fr 1fr auto}
    }
    @media (max-width: 760px){
      .form{grid-template-columns:1fr; gap:10px}
      .grid{grid-template-columns:1fr}
      .two{grid-template-columns:1fr}
      .stats{grid-template-columns:repeat(2, minmax(0,1fr))}
    }
  </style>
</head>
<body>
  <!-- Top Nav -->
  <header class="nav">
    <div class="nav-inner">
      <div class="brand">
        <div class="logo" aria-hidden="true"></div>
        <div>Semantic <span style="color:var(--accent)">SEO</span></div>
      </div>
      <nav class="tabs">
        <a class="tab active" href="#">Semantic Analyzer</a>
        <a class="tab" href="#">AI Content Checker</a>
        <a class="tab" href="#">Topic Cluster</a>
      </nav>
    </div>
  </header>

  <main class="wrap">
    <!-- Hero -->
    <div class="hero">
      <div class="kicker">Analyzer 2.0</div>
      <h1 class="title">Semantic SEO Master</h1>
      <p class="subtitle">Analyze any page’s semantic structure, technical elements, and content quality—then get precise improvements.</p>

      <!-- Analyze Form -->
      <form class="form" action="{{ route('semantic.analyze') }}" method="post">
        @csrf
        <label class="field">
          <span class="sr">URL</span>
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M10.59 13.41a2 2 0 0 0 2.82 0l3.18-3.18a2 2 0 0 0-2.82-2.82l-.88.88" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
          <input type="url" name="url" placeholder="https://example.com/page" value="{{ old('url') }}" required>
        </label>
        <label class="field">
          <span class="sr">Target keyword</span>
          <input type="text" name="target_keyword" placeholder="Target keyword (optional)" value="{{ old('target_keyword') }}">
        </label>
        <button class="submit" type="submit">
          Analyze URL
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7" stroke="#0b0a12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </form>
      <div class="tip">We’ll fetch the page and compute a detailed semantic report.</div>
    </div>

    <!-- Score + Quick Stats -->
    <section class="panel">
      <article class="card ring-box pad" aria-label="Overall Score">
        @php
          $score = (int)($report['overall_score'] ?? 0);
          $label = $report['wheel_label'] ?? '—';
          $scoreColor = $score >= 80 ? 'var(--success)' : ($score >= 60 ? 'var(--warning)' : 'var(--danger)');
        @endphp
        <div class="ring" style="--score-val: {{ $score }}; --accent: {{ $scoreColor }}">
          <div class="val">{{ $score }}</div>
          <div class="caption">{{ $label }}</div>
        </div>
      </article>

      <article class="card pad">
        <h3 style="margin:2px 0 12px">Quick Stats</h3>
        @php $qs = $report['quick_stats'] ?? []; @endphp
        <div class="stats">
          <div class="chip"><div class="k">Words</div><div class="v">{{ $qs['words'] ?? '—' }}</div></div>
          <div class="chip"><div class="k">Images</div><div class="v">{{ $qs['images'] ?? '—' }}</div></div>
          <div class="chip"><div class="k">Internal</div><div class="v">{{ $qs['internal'] ?? '—' }}</div></div>
          <div class="chip"><div class="k">External</div><div class="v">{{ $qs['external'] ?? '—' }}</div></div>
          <div class="chip {{ (($qs['readability'] ?? 100) <= 30) ? 'bad' : '' }}"><div class="k">Readability</div><div class="v">{{ $qs['readability'] ?? '—' }}</div></div>
          <div class="chip"><div class="k">Text/HTML %</div><div class="v">{{ $qs['text_html_ratio'] ?? '—' }}</div></div>
        </div>
      </article>
    </section>

    <!-- Categories -->
    <section class="grid">
      @php $cats = $report['categories'] ?? []; @endphp
      @forelse ($cats as $cat)
        @php
          $cScore = (int)($cat['score'] ?? 0);
          $badgeClass = $cScore >= 80 ? 'success' : ($cScore >= 60 ? 'warn' : 'fail');
          $items = $cat['items'] ?? [];
        @endphp
        <article class="cat">
          <header class="cat-h">
            <div class="cat-name">{{ $cat['name'] ?? 'Category' }}</div>
            <div class="badge {{ $badgeClass }}">{{ $cScore }}</div>
          </header>
          <div class="list">
            @forelse ($items as $it)
              @php $state = $it['state'] ?? 'pass'; @endphp
              <div class="row">
                <div class="row-left">
                  <span class="dot {{ $state }}" aria-hidden="true"></span>
                  <div>{{ $it['label'] ?? 'Item' }}</div>
                </div>
                <div style="display:flex; align-items:center; gap:8px">
                  @if(isset($it['value']))<span class="muted">{{ $it['value'] }}</span>@endif
                  <button class="improve" type="button">Improve</button>
                </div>
              </div>
            @empty
              <div class="muted" style="padding:4px 8px">No checks available.</div>
            @endforelse
          </div>
        </article>
      @empty
        <article class="cat"><header class="cat-h"><div class="cat-name">Content & Keywords</div><div class="badge fail">25</div></header><div class="list"><div class="row"><div class="row-left"><span class="dot warn"></span><div>H1/Title includes primary topic</div></div><button class="improve" type="button">Improve</button></div><div class="row"><div class="row-left"><span class="dot warn"></span><div>Primary keyword in first paragraph</div></div><button class="improve" type="button">Improve</button></div><div class="row"><div class="row-left"><span class="dot fail"></span><div>Readable, NLP‑friendly language</div></div><button class="improve" type="button">Improve</button></div><div class="row"><div class="row-left"><span class="dot fail"></span><div>Includes FAQs / questions with answers</div></div><button class="improve" type="button">Improve</button></div></div></article>
        <article class="cat"><header class="cat-h"><div class="cat-name">Technical Elements</div><div class="badge success">94</div></header><div class="list"><div class="row"><div class="row-left"><span class="dot pass"></span><div>Title tag length ≈50–60 chars</div></div><span class="muted">100</span></div><div class="row"><div class="row-left"><span class="dot warn"></span><div>Meta description ≈140–160 chars</div></div><span class="muted">70</span></div><div class="row"><div class="row-left"><span class="dot pass"></span><div>Page indexable (no noindex)</div></div><span class="muted">100</span></div><div class="row"><div class="row-left"><span class="dot pass"></span><div>Canonical tag set correctly</div></div><span class="muted">100</span></div><div class="row"><div class="row-left"><span class="dot pass"></span><div>OpenGraph meta present</div></div><span class="muted">100</span></div></div></article>
        <article class="cat"><header class="cat-h"><div class="cat-name">Content Quality</div><div class="badge warn">80</div></header><div class="list"><div class="row"><div class="row-left"><span class="dot pass"></span><div>Cites authoritative sources (external links)</div></div><span class="muted">100</span></div><div class="row"><div class="row-left"><span class="dot warn"></span><div>Clear author/date (E‑E‑A‑T hint)</div></div><span class="muted">60</span></div></div></article>
      @endforelse
    </section>

    <!-- Structure & Recommendations -->
    <section class="two">
      <article class="card pad">
        <h3 style="margin:2px 0 12px">Content Structure</h3>
        @php $st = $report['structure'] ?? []; @endphp
        <div class="pill"><strong class="muted">Title</strong><div>{{ $st['title'] ?? '—' }}</div></div>
        <div class="pill"><strong class="muted">Meta description</strong><div>{{ $st['meta_description'] ?? '—' }}</div></div>
        <div class="hmap">
          @php $h = $st['headings'] ?? []; @endphp
          <div class="hcol">
            <div class="h">H1</div>
            @foreach(($h['h1'] ?? []) as $x)
              <div class="pill">• {{ $x }}</div>
            @endforeach
          </div>
          <div class="hcol">
            <div class="h">H2</div>
            @foreach(($h['h2'] ?? []) as $x)
              <div class="pill">• {{ $x }}</div>
            @endforeach
          </div>
          <div class="hcol">
            <div class="h">H3</div>
            @foreach(($h['h3'] ?? []) as $x)
              <div class="pill">• {{ $x }}</div>
            @endforeach
          </div>
          <div class="hcol">
            <div class="h">H4</div>
            @foreach(($h['h4'] ?? []) as $x)
              <div class="pill">• {{ $x }}</div>
            @endforeach
          </div>
        </div>
      </article>

      <article class="card pad">
        <h3 style="margin:2px 0 12px">Recommendations</h3>
        <div class="recs">
          @php $recs = $report['recommendations'] ?? []; @endphp
          @forelse ($recs as $r)
            @php
              $sev = strtolower($r['severity'] ?? 'info');
              $sevClass = in_array($sev, ['critical','warning','info']) ? $sev : 'info';
            @endphp
            <div class="rec">
              <span class="sev {{ $sevClass }}">{{ ucfirst($sevClass) }}</span>
              <div>{{ $r['text'] ?? '' }}</div>
            </div>
          @empty
            <div class="muted">No recommendations yet.</div>
          @endforelse
        </div>
        <div class="muted" style="margin-top:10px; font-size:12px">Severity legend: <strong style="color:var(--danger)">Critical</strong> · <strong style="color:var(--warning)">Warning</strong> · <strong style="color:var(--accent-2)">Info</strong></div>
      </article>
    </section>
  </main>

  <script>
    // Accessible improvement buttons could open detailed modals.
    // Hook your existing JS here. Keeping empty to avoid heavy scripts.

    // Example: prevent double-submission
    const form = document.querySelector('.form');
    if(form){
      form.addEventListener('submit', (e)=>{
        const btn = form.querySelector('button[type="submit"]');
        if(btn){ btn.disabled = true; btn.style.opacity = .8; btn.textContent = 'Analyzing…'; }
      });
    }
  </script>
</body>
</html>
