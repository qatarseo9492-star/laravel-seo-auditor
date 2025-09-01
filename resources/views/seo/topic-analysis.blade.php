<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Topic Cluster Identification & Mapping</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <script src="https://cdn.lordicon.com/lordicon.js"></script>

  <style>
    :root{
      --ink:#eaf1ff; --muted:#a9b6df; --bg:#0a0b12;
      --panel: rgba(255,255,255,.06); --border: rgba(255,255,255,.14);
      --accent1:#7c4dff; --accent2:#00e5ff; --accent3:#ff4dd2; --accent4:#33ffaa;
    }
    *{box-sizing:border-box}
    html,body{margin:0;padding:0}
    body{background:var(--bg); color:var(--ink); font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial; overflow-x:hidden}

    /* === Background inspired by reference (CSS-only, no images) === */
    .bg{position:fixed; inset:0; z-index:-3;
      background:
        radial-gradient(1000px 580px at 8% -5%, rgba(124,77,255,.20), transparent 60%),
        radial-gradient(1000px 520px at 92% 0%, rgba(0,229,255,.18), transparent 60%),
        radial-gradient(900px 520px at 50% 110%, rgba(255,77,210,.14), transparent 60%),
        linear-gradient(180deg, #0a0b16 0%, #070811 100%);
      filter:saturate(110%);
    }
    .mesh,.mesh:before{
      content:""; position:fixed; inset:-10% -10%; z-index:-2;
      background:
        conic-gradient(from 0deg at 20% 30%, rgba(124,77,255,.14), transparent 68%),
        conic-gradient(from 135deg at 75% 25%, rgba(0,229,255,.12), transparent 70%),
        conic-gradient(from 260deg at 60% 80%, rgba(255,77,210,.12), transparent 70%);
      filter: blur(40px);
      mix-blend-mode: screen;
      animation: drift 28s ease-in-out infinite alternate;
    }
    .mesh:before{animation-duration:36s; transform: scale(1.1)}
    .grid-lines{
      position:fixed; inset:0; z-index:-1; pointer-events:none; opacity:.06;
      background-image:
        linear-gradient(transparent 95%, rgba(255,255,255,.5) 96%),
        linear-gradient(90deg, transparent 95%, rgba(255,255,255,.5) 96%);
      background-size: 56px 56px, 56px 56px;
      mask: radial-gradient(1200px 600px at 50% 5%, #000 55%, transparent 75%);
    }
    @keyframes drift{0%{transform:translate(-1%,-1%) scale(1.03)}100%{transform:translate(1%,1%) scale(1.07)}}

    /* === Layout */
    .container{max-width:1200px;margin:0 auto;padding:24px 18px 40px}
    .page-title{display:flex;align-items:center;gap:.8rem;margin:10px 0 12px 0}
    .badge{width:44px;height:44px;border-radius:14px;display:grid;place-items:center;color:#061018;
      background:linear-gradient(135deg,var(--accent1),var(--accent2));box-shadow:0 10px 30px rgba(0,229,255,.28)}
    .title h1{margin:0;font-size:1.75rem;letter-spacing:.2px}
    .title p{margin:2px 0 0 0;opacity:.75}

    .grid{display:grid;grid-template-columns: 1.2fr .8fr;gap:16px;margin-top:10px}
    @media (max-width: 960px){ .grid{grid-template-columns: 1fr} }

    .card{border:1px solid var(--border);border-radius:18px;background:var(--panel);backdrop-filter: blur(8px);box-shadow:0 18px 50px rgba(0,0,0,.45)}
    .card .head{display:flex;align-items:center;gap:.6rem;padding:16px 16px 8px 16px}
    .card .head h2{margin:0;font-size:1.15rem}
    .card .body{padding:16px}

    /* form */
    .form{display:grid; gap:12px}
    .field{position:relative}
    textarea, input[type="number"]{
      width:100%; border-radius:14px; border:1px solid var(--border);
      background: rgba(255,255,255,.06); color:var(--ink);
      padding: 14px 14px; font-size:1rem; outline:none;
      transition: .18s ease;
    }
    textarea:focus, input[type="number"]:focus{border-color:#a8b7ff; box-shadow:0 0 0 3px rgba(124,77,255,.25)}
    .hint{opacity:.75;font-size:.92rem;margin-top:4px}
    .meta{display:flex;align-items:center;gap:10px;font-size:.92rem;opacity:.8;margin-top:4px}

    .controls{display:flex;flex-wrap:wrap;gap:10px;margin-top:10px}
    .btn{
      display:inline-flex;align-items:center;gap:.55rem;padding:12px 14px;border-radius:14px;border:1px solid var(--border);
      background: rgba(255,255,255,.08); color:var(--ink); text-decoration:none; cursor:pointer;
    }
    .btn.primary{border-color:transparent;background:linear-gradient(135deg,var(--accent3),var(--accent2));color:#061018;box-shadow:0 14px 36px rgba(255,77,210,.28)}
    .btn.success{border-color:transparent;background:linear-gradient(135deg,var(--accent4),var(--accent2));color:#061018;box-shadow:0 14px 36px rgba(51,255,170,.28)}
    .btn:hover{transform:translateY(-1px)}

    /* features */
    .feat-list{display:grid;gap:10px;padding:0 14px 16px 14px}
    .feat{display:flex;gap:.8rem;padding:12px;border-radius:14px;border:1px solid var(--border);background:rgba(255,255,255,.04)}
    .feat .ico{width:42px;height:42px;border-radius:12px;display:grid;place-items:center;color:#061018;
      background:linear-gradient(135deg,var(--accent1),var(--accent2))}
    .feat h3{margin:0 0 4px 0;font-size:1.02rem}
    .feat p{margin:0;opacity:.8}

    .foot-note{opacity:.7;font-size:.9rem;padding:12px 16px;border-top:1px dashed rgba(255,255,255,.14)}

    /* tiny label badges */
    .lb{display:inline-flex;gap:.4rem;align-items:center;border:1px solid var(--border);border-radius:999px;padding:.2rem .55rem;opacity:.9}
  </style>
</head>
<body>
  <div class="bg"></div>
  <div class="mesh"></div>
  <div class="grid-lines"></div>

  <div class="container">
    <div class="page-title">
      <div class="badge"><i class="fa-solid fa-diagram-project"></i></div>
      <div class="title">
        <h1>Topic Cluster Identification & Mapping</h1>
        <p>Paste your URLs → generate clean, human-friendly clusters ready for internal linking.</p>
      </div>
    </div>

    <div class="grid">
      <!-- FORM CARD -->
      <div class="card">
        <div class="head">
          <lord-icon src="https://cdn.lordicon.com/wmwqvixz.json" trigger="loop" delay="1800" style="width:28px;height:28px"></lord-icon>
          <h2>Input URLs</h2>
          <span class="lb"><i class="fa-regular fa-file-lines"></i> <span id="urlCount">0 URLs</span></span>
        </div>
        <div class="body">
          <form class="form" method="POST" action="{{ route('seo.topic-clusters.store') }}">
            @csrf
            <textarea name="urls" id="urls" rows="10" placeholder="https://example.com/page-1
https://example.com/blog/post-2
https://example.com/guide/topic-3" required>{{ old('urls') }}</textarea>
            @error('urls') <div class="err" style="color:#ffd2dc">{{ $message }}</div> @enderror

            <div class="meta">
              <span class="lb"><i class="fa-solid fa-hashtag"></i> One URL per line</span>
              <span class="lb"><i class="fa-solid fa-gauge"></i> Up to ~150 URLs recommended</span>
              <span class="lb"><i class="fa-solid fa-wand-magic-sparkles"></i> We auto-trim & dedupe</span>
            </div>

            <div class="field" style="margin-top:6px">
              <label for="num_clusters" style="display:block;margin:0 0 6px 2px;opacity:.85">Number of clusters</label>
              <input type="number" id="num_clusters" name="num_clusters" min="2" max="12" value="{{ old('num_clusters', 5) }}">
            </div>

            <div class="controls">
              <button type="submit" class="btn primary"><i class="fa-solid fa-wand-magic-sparkles"></i> Generate clusters</button>
              <button type="button" id="pasteDemo" class="btn"><i class="fa-solid fa-paste"></i> Paste demo set</button>
              <button type="button" id="clearAll" class="btn"><i class="fa-solid fa-eraser"></i> Clear</button>
            </div>
          </form>

          <div class="foot-note">
            Tip: Include a mix of cornerstone pages and best-performing articles to get meaningful clusters.
          </div>
        </div>
      </div>

      <!-- FEATURES / HOW IT WORKS -->
      <div class="card">
        <div class="head">
          <lord-icon src="https://cdn.lordicon.com/pbbsmkso.json" trigger="loop" delay="2000" style="width:28px;height:28px"></lord-icon>
          <h2>How it works</h2>
        </div>
        <div class="feat-list">
          <div class="feat">
            <div class="ico"><i class="fa-solid fa-cloud-arrow-down"></i></div>
            <div>
              <h3>Fetch & clean text</h3>
              <p>We grab each page, strip HTML, and condense content so clustering stays fast and efficient.</p>
            </div>
          </div>
          <div class="feat">
            <div class="ico" style="background:linear-gradient(135deg,var(--accent3),var(--accent2))"><i class="fa-solid fa-diagram-project"></i></div>
            <div>
              <h3>AI-based clustering</h3>
              <p>OpenAI groups related pages into clear clusters with names, descriptions, keywords, and member URLs.</p>
            </div>
          </div>
          <div class="feat">
            <div class="ico" style="background:linear-gradient(135deg,var(--accent4),var(--accent2))"><i class="fa-solid fa-link"></i></div>
            <div>
              <h3>Interlinking ready</h3>
              <p>Use clusters to plan internal links and pillar → cluster structures for topical authority.</p>
            </div>
          </div>
          <div class="feat">
            <div class="ico" style="background:linear-gradient(135deg,#ffb74d,#ff4dd2)"><i class="fa-solid fa-shield-check"></i></div>
            <div>
              <h3>Cached results</h3>
              <p>The system caches analysis for identical URL sets so you save credits and time.</p>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <script>
    // Count URLs live
    const ta = document.getElementById('urls');
    const out = document.getElementById('urlCount');
    const updateCount = () => {
      const lines = (ta.value || '').split(/\\r?\\n/).map(s => s.trim()).filter(Boolean);
      out.textContent = lines.length + (lines.length === 1 ? ' URL' : ' URLs');
    };
    ta && (ta.addEventListener('input', updateCount), updateCount());

    // Demo paste
    const demo = [
      'https://example.com/',
      'https://example.com/blog/seo-basics',
      'https://example.com/blog/keyword-research',
      'https://example.com/blog/topic-clusters',
      'https://example.com/guides/internal-linking',
      'https://example.com/tools/seo-analyzer',
      'https://example.com/blog/content-templates',
      'https://example.com/blog/how-to-optimize-titles',
      'https://example.com/blog/how-to-boost-topical-authority',
      'https://example.com/blog/technical-seo-checklist'
    ];
    document.getElementById('pasteDemo')?.addEventListener('click', () => {
      ta.value = demo.join('\\n');
      updateCount();
      ta.focus();
    });

    // Clear
    document.getElementById('clearAll')?.addEventListener('click', () => {
      ta.value = ''; updateCount(); ta.focus();
    });
  </script>
</body>
</html>
