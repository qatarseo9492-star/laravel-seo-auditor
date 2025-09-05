@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* =====================
     Neon–Dark Design System (scoped to this page)
     ===================== */
  :root{
    --app-bg:#03041c;
    --app-fg:#e5e7eb;
    --muted:#a3a3a3;
    --border-weak:rgba(255,255,255,.08);
    --border-strong:rgba(255,255,255,.14);
    --pill-bg:rgba(255,255,255,.06);
    --pill-br:rgba(255,255,255,.12);

    --good: #22c55e;
    --warn: #f59e0b;
    --bad:  #ef4444;

    --grad-1: linear-gradient(90deg,#8b5cf6 0%,#ec4899 30%,#f59e0b 60%,#22c55e 100%);
    --grad-2: linear-gradient(135deg,#00d2ff 0%,#3a7bd5 50%,#9333ea 100%);

    --card-bg: rgba(255,255,255,.03);
    --glow: 0 0 0 .5px rgba(255,255,255,.12), 0 8px 24px rgba(44, 0, 120, .35);
  }

  html,body{ background:var(--app-bg); color:var(--app-fg); }
  .container{ max-width:1200px; margin:0 auto; padding:24px; }

  .skip-link{ position:absolute; left:-9999px; }
  .skip-link:focus{ left:12px; top:12px; background:#111827; color:#fff; padding:8px 12px; border-radius:8px; z-index:1000; }

  /* Header / Toolbar */
  .topbar{ display:flex; gap:12px; align-items:center; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; }
  .urlbox{ display:flex; gap:10px; align-items:center; flex:1 1 520px; background:var(--card-bg); border:1px solid var(--border-weak); padding:10px 12px; border-radius:12px; box-shadow: var(--glow); }
  .urlbox input[type=url]{ flex:1; background:transparent; color:var(--app-fg); border:none; outline:none; font-size:14px; }
  .btn{ border:none; background:var(--grad-2); color:#fff; padding:10px 14px; border-radius:12px; cursor:pointer; box-shadow: var(--glow); transition: transform .15s ease, filter .2s ease; }
  .btn:hover{ transform: translateY(-1px); filter: brightness(1.05); }
  .btn.secondary{ background:var(--card-bg); color:var(--app-fg); border:1px solid var(--border-weak); }
  .toolbar{ display:flex; gap:8px; flex-wrap:wrap; }
  .quota{ background:var(--pill-bg); border:1px solid var(--pill-br); padding:6px 10px; border-radius:999px; font-size:12px; color:#cbd5e1; display:flex; align-items:center; gap:6px; }

  /* Hero Score Card */
  .hero{ display:grid; grid-template-columns: 320px 1fr; gap:18px; margin:18px 0 10px; }
  @media (max-width: 980px){ .hero{ grid-template-columns:1fr; } }

  .card{ background:var(--card-bg); border:1px solid var(--border-weak); border-radius:16px; padding:16px; box-shadow: var(--glow); }
  .card h3{ margin:0 0 10px; font-size:16px; font-weight:600; letter-spacing:.3px; color:#e5e7eb; }

  .ring{ position:relative; width:100%; aspect-ratio:1/1; border-radius:50%; display:grid; place-items:center; background:
    conic-gradient(#3f3f46 0deg, #3f3f46 360deg); }
  .ring__inner{ position:absolute; inset:10%; background:var(--app-bg); border-radius:50%; display:grid; place-items:center; }
  .ring__score{ font-size:40px; font-weight:800; }
  .ring__label{ font-size:12px; color:var(--muted); margin-top:4px; }

  .legend{ display:flex; align-items:center; gap:10px; margin:6px 0 12px; }
  .legend .title{ font-weight:800; font-size:18px; background: var(--grad-1); -webkit-background-clip:text; background-clip:text; color:transparent; filter: drop-shadow(0 0 12px rgba(147,51,234,.35)); display:flex; align-items:center; gap:8px; }
  .legend .title .icon{ width:18px; height:18px; border-radius:6px; background: var(--grad-1); box-shadow: 0 0 24px rgba(236,72,153,.45); animation: orb 4s linear infinite; }
  @keyframes orb{ 0%{ transform: rotate(0) } 100%{ transform: rotate(360deg) } }

  .chips{ display:flex; flex-wrap:wrap; gap:8px; }
  .chip{ display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; background:var(--pill-bg); border:1px solid var(--pill-br); font-size:12px; color:#e5e7eb; }
  .chip .dot{ width:8px; height:8px; border-radius:999px; background:#6b7280; box-shadow:0 0 12px rgba(255,255,255,.14); }
  .chip.good .dot{ background:var(--good) }
  .chip.warn .dot{ background:var(--warn) }
  .chip.bad  .dot{ background:var(--bad) }

  /* Grid */
  .grid{ display:grid; grid-template-columns: 1.1fr .9fr; gap:18px; }
  @media (max-width: 1080px){ .grid{ grid-template-columns: 1fr; } }

  /* Progress Bars */
  .bar{ height:10px; background:#0b0730; border-radius:999px; overflow:hidden; border:1px solid var(--border-strong); }
  .bar__fill{ height:100%; width:0%; background: linear-gradient(90deg,#22c55e,#16a34a); transition: width 1s ease; }
  .bar__fill.warn{ background: linear-gradient(90deg,#f59e0b,#d97706); }
  .bar__fill.bad{ background: linear-gradient(90deg,#ef4444,#dc2626); }

  /* Tabs */
  .tabs{ display:flex; gap:8px; flex-wrap:wrap; padding:6px; background:var(--card-bg); border:1px solid var(--border-weak); border-radius:12px; }
  .tab-btn{ background:transparent; border:none; color:#cbd5e1; padding:8px 10px; border-radius:10px; cursor:pointer; }
  .tab-btn[aria-selected=\"true\"]{ background:rgba(255,255,255,.06); color:#fff; border:1px solid var(--border-strong); box-shadow: var(--glow); }
  .tabpanel{ display:none; }
  .tabpanel.active{ display:block; }

  /* Suggestion (Fix) Cards */
  .fix{ display:grid; grid-template-columns: 10px 1fr auto; gap:12px; padding:12px; border:1px solid var(--border-weak); border-radius:12px; background:rgba(255,255,255,.02); }
  .fix+.fix{ margin-top:10px; }
  .fix .stripe{ border-radius:999px; }
  .fix .body h4{ margin:0 0 6px; font-size:14px; }
  .fix .body p{ margin:0 0 6px; color:#cbd5e1; font-size:13px; }
  .fix .actions{ display:flex; gap:6px; align-items:start; }
  .tag{ display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:4px 8px; border-radius:999px; background:var(--pill-bg); border:1px solid var(--pill-br); color:#d1d5db; }

  /* SERP Preview */
  .serp{ border:1px solid var(--border-weak); border-radius:12px; padding:12px; background:rgba(255,255,255,.02); }
  .serp .toggle{ display:flex; gap:8px; margin-bottom:10px; }
  .serp .toggle button{ background:transparent; border:1px solid var(--border-weak); color:#cbd5e1; padding:6px 8px; border-radius:8px; cursor:pointer; }
  .serp .toggle button.active{ background:rgba(255,255,255,.06); color:#fff; box-shadow: var(--glow); }
  .serp .viewport{ max-width:600px; border:1px dashed var(--border-weak); border-radius:10px; padding:10px 12px; }
  .serp.mobile .viewport{ max-width:360px; }
  .serp .t{ color:#1e90ff; font-size:18px; line-height:1.3; margin:2px 0 4px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
  .serp .u{ color:#22c55e; font-size:12px; margin:0 0 4px; }
  .serp .d{ color:#9ca3af; font-size:13px; line-height:1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  .serp .counts{ font-size:11px; color:#9ca3af; margin-top:6px; }

  /* Headings Map */
  .hmap{ display:block; }
  .hitem{ padding:8px 10px; border:1px solid var(--border-weak); background:rgba(255,255,255,.02); border-radius:10px; margin-bottom:6px; }
  .hitem .label{ font-size:11px; color:#94a3b8; margin-right:8px; }
  .hitem.bad{ border-color: rgba(239,68,68,.5); background: rgba(239,68,68,.06); }

  /* Entities Table */
  table.entities{ width:100%; border-collapse:collapse; }
  table.entities th, table.entities td{ padding:8px 10px; border-bottom:1px solid var(--border-weak); text-align:left; font-size:13px; }
  table.entities th{ color:#cbd5e1; font-weight:600; }
  table.entities td .copy{ background:transparent; border:1px solid var(--border-weak); color:#cbd5e1; padding:4px 8px; border-radius:8px; cursor:pointer; }

  /* Skeletons */
  .skeleton{ position:relative; overflow:hidden; background:#15132f; border-radius:8px; }
  .skeleton::after{ content:\"\"; position:absolute; inset:0; background: linear-gradient(90deg, transparent, rgba(255,255,255,.06), transparent); animation: shimmer 1.4s infinite; transform: translateX(-100%); }
  @keyframes shimmer{ 0%{ transform: translateX(-100%) } 100%{ transform: translateX(100%) } }

  /* Focus visibility */
  :focus-visible{ outline: 2px solid #60a5fa; outline-offset: 2px; border-radius:6px; }

  /* RTL support */
  [dir=\"rtl\"] .legend .title{ letter-spacing:0; }
</style>
@endpush

@section('content')
<a href=\"#main\" class=\"skip-link\">Skip to content</a>
<div class=\"container\">

  <!-- Topbar & Actions -->
  <div class=\"topbar\">
    <form class=\"urlbox\" method=\"GET\" action=\"{{ url()->current() }}\" role=\"search\">
      <input type=\"url\" name=\"url\" value=\"{{ request('url') }}\" placeholder=\"Paste a URL to analyze...\" aria-label=\"URL to analyze\">
      <button type=\"submit\" class=\"btn\" id=\"analyzeBtn\" aria-label=\"Analyze\">Analyze</button>
    </form>

    <div class=\"toolbar\">
      <div class=\"quota\" title=\"Daily quota\">
        @php
          $used = $quotaUsed ?? 0; $limit = $quotaLimit ?? 200;
        @endphp
        <span>🧮 {{ $used }}/{{ $limit }} today</span>
      </div>
      <button class=\"btn secondary\" onclick=\"window.dispatchEvent(new CustomEvent('export-request',{detail:'pdf'}))\">Export PDF</button>
      <button class=\"btn secondary\" onclick=\"window.dispatchEvent(new CustomEvent('export-request',{detail:'json'}))\">Export JSON</button>
      <button class=\"btn secondary\" onclick=\"window.dispatchEvent(new CustomEvent('share-link'))\">Share Link</button>
      <button class=\"btn\" onclick=\"location.reload()\">Refresh</button>
    </div>
  </div>

  <!-- Hero: Overall + Category chips -->
  @php
    $score = $overall_score ?? null;
    $cats  = $categoryScores ?? [];
    $defaults = ['Content','Links','Schema','Readability','Performance','Technical','A11y'];
  @endphp
  <section class=\"hero\" aria-labelledby=\"heroLabel\">
    <div class=\"card\">
      <div class=\"legend\">
        <div class=\"title\"><span class=\"icon\" aria-hidden=\"true\"></span> <span id=\"heroLabel\">Content Optimization</span></div>
      </div>
      <div class=\"ring\" id=\"overallRing\" aria-label=\"Overall score\" role=\"img\" data-score=\"{{ $score ?? 0 }}\">
        <div class=\"ring__inner\">
          <div class=\"ring__content\" style=\"text-align:center\">
            <div class=\"ring__score\" id=\"overallScore\">{{ $score ?? '—' }}</div>
            <div class=\"ring__label\">Overall score</div>
          </div>
        </div>
      </div>
    </div>

    <div class=\"card\">
      <h3>Categories</h3>
      <div class=\"chips\">
        @foreach($defaults as $cat)
          @php $v = $cats[$cat] ?? null;
               $state = is_null($v) ? '' : ($v>=85 ? 'good' : ($v>=50 ? 'warn':'bad')); @endphp
          <div class=\"chip {{ $state }}\" data-score=\"{{ $v ?? '' }}\" aria-label=\"{{ $cat }} score {{ $v ?? 'pending' }}\">
            <span class=\"dot\"></span>
            <span>{{ $cat }}</span>
            <strong>{{ $v !== null ? $v : '—' }}</strong>
          </div>
        @endforeach
      </div>

      <div style=\"margin-top:14px\">
        @foreach($defaults as $cat)
          @php $v = $cats[$cat] ?? null; $cls = is_null($v) ? '' : ($v>=85 ? '' : ($v>=50 ? 'warn':'bad')); @endphp
          <div style=\"display:flex; align-items:center; gap:10px; margin:8px 0\" aria-label=\"{{ $cat }} progress\">
            <div style=\"width:130px; color:#cbd5e1; font-size:12px\">{{ $cat }}</div>
            <div class=\"bar\" style=\"flex:1\">
              <div class=\"bar__fill {{ $cls }}\" data-progress=\"{{ $v ?? 0 }}\"></div>
            </div>
            <div style=\"width:30px; text-align:right; font-size:12px\">{{ $v !== null ? $v.'%' : '—' }}</div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Main Grid -->
  <section id=\"main\" class=\"grid\" aria-label=\"Analyzer details\">

    <!-- Left: High‑impact Fixes + SERP Preview -->
    <div class=\"left\">
      <div class=\"card\">
        <div class=\"legend\">
          <div class=\"title\"><span class=\"icon\" aria-hidden=\"true\"></span> High‑Impact Fixes</div>
        </div>

        @php $fixes = $suggestions ?? []; @endphp
        @if(empty($fixes))
          <div class=\"skeleton\" style=\"height:64px; margin-bottom:8px\"></div>
          <div class=\"skeleton\" style=\"height:64px; margin-bottom:8px\"></div>
          <div class=\"skeleton\" style=\"height:64px;\"></div>
        @else
          @foreach($fixes as $f)
            @php
              $sev = strtolower($f['severity'] ?? 'medium');
              $color = $sev==='high' ? 'var(--bad)' : ($sev==='low' ? 'var(--good)' : 'var(--warn)');
            @endphp
            <article class=\"fix\" aria-live=\"polite\">
              <div class=\"stripe\" style=\"background:{{ $color }}\"></div>
              <div class=\"body\">
                <h4>{{ $f['title'] ?? 'Suggested improvement' }}</h4>
                @if(!empty($f['why']))<p><strong>Why:</strong> {{ $f['why'] }}</p>@endif
                @if(!empty($f['how']))<p><strong>Fix:</strong> <span class=\"fix-text\">{{ $f['how'] }}</span></p>@endif
                @if(!empty($f['snippet']))<pre style=\"margin:6px 0; white-space:pre-wrap; color:#9ca3af\">{{ $f['snippet'] }}</pre>@endif
                <div class=\"tags\" style=\"margin-top:6px; display:flex; gap:6px; flex-wrap:wrap\">
                  <span class=\"tag\">Category: {{ $f['category'] ?? 'General' }}</span>
                  <span class=\"tag\">Severity: {{ ucfirst($sev) }}</span>
                </div>
              </div>
              <div class=\"actions\">
                <button class=\"btn secondary copy-fix\" title=\"Copy fix\">Copy</button>
              </div>
            </article>
          @endforeach
        @endif
      </div>

      <div class=\"card\" style=\"margin-top:16px\">
        <div class=\"legend\">
          <div class=\"title\"><span class=\"icon\" aria-hidden=\"true\"></span> SERP Snippet Preview</div>
        </div>

        @php
          $serp = $serp ?? [
            'title' => $metaTitle ?? null,
            'description' => $metaDescription ?? null,
            'url' => $pageUrl ?? request('url'),
          ];
        @endphp

        <div class=\"serp\" id=\"serp\">
          <div class=\"toggle\" role=\"tablist\" aria-label=\"SERP device mode\">
            <button type=\"button\" class=\"serp-mode active\" data-mode=\"desktop\" aria-selected=\"true\">Desktop</button>
            <button type=\"button\" class=\"serp-mode\" data-mode=\"mobile\" aria-selected=\"false\">Mobile</button>
          </div>
          <div class=\"viewport\">
            <div class=\"t\" id=\"serpTitle\" title=\"{{ $serp['title'] ?? '—' }}\">{{ $serp['title'] ?? '—' }}</div>
            <div class=\"u\" id=\"serpUrl\">{{ $serp['url'] ?? '—' }}</div>
            <div class=\"d\" id=\"serpDesc\">{{ $serp['description'] ?? '—' }}</div>
            <div class=\"counts\" id=\"serpCounts\"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: Tabs (Content, Links, Schema, Readability, Performance, Technical, A11y) -->
    <div class=\"right\">
      <div class=\"tabs\" role=\"tablist\" aria-label=\"Details tabs\">
        @php $tabNames = ['Content','Links','Schema','Readability','Performance','Technical','A11y']; @endphp
        @foreach($tabNames as $i=>$t)
          <button class=\"tab-btn\" role=\"tab\" aria-selected=\"{{ $i===0 ? 'true':'false' }}\" aria-controls=\"panel-{{ $t }}\" id=\"tab-{{ $t }}\">{{ $t }}</button>
        @endforeach
      </div>

      <!-- Content Panel -->
      <div class=\"card tabpanel active\" role=\"tabpanel\" id=\"panel-Content\" aria-labelledby=\"tab-Content\">
        <h3>Headings Map</h3>
        @php $headings = $headings ?? []; @endphp
        @if(empty($headings))
          <div class=\"skeleton\" style=\"height:32px; margin-bottom:6px\"></div>
          <div class=\"skeleton\" style=\"height:32px; margin-bottom:6px\"></div>
          <div class=\"skeleton\" style=\"height:32px;\"></div>
        @else
          <div class=\"hmap\">
            @foreach($headings as $h)
              @php
                $lvl = $h['level'] ?? 2;
                $bad = $h['bad'] ?? false;
                $text= $h['text'] ?? '';
              @endphp
              <div class=\"hitem {{ $bad ? 'bad':'' }}\">
                <span class=\"label\">H{{ $lvl }}</span>
                <span>{{ $text }}</span>
              </div>
            @endforeach
          </div>
        @endif

        <h3 style=\"margin-top:14px\">Topic Coverage</h3>
        @php $coverage = $cats['Content'] ?? null; @endphp
        <div class=\"bar\" aria-label=\"Topic coverage\">
          @php $cls = is_null($coverage) ? '' : ($coverage>=85 ? '' : ($coverage>=50 ? 'warn':'bad')); @endphp
          <div class=\"bar__fill {{ $cls }}\" data-progress=\"{{ $coverage ?? 0 }}\"></div>
        </div>
      </div>

      <!-- Links Panel -->
      <div class=\"card tabpanel\" role=\"tabpanel\" id=\"panel-Links\" aria-labelledby=\"tab-Links\">
        <h3>Internal Linking Suggestions</h3>
        @php $linkSugs = $linkSuggestions ?? []; @endphp
        @if(empty($linkSugs))
          <p style=\"color:#9ca3af\">No suggestions detected or not analyzed yet.</p>
        @else
          @foreach($linkSugs as $s)
            <div class=\"fix\">
              <div class=\"stripe\" style=\"background:var(--warn)\"></div>
              <div class=\"body\">
                <h4>Add internal link to <em>{{ $s['target'] }}</em></h4>
                <p><strong>Anchor:</strong> {{ $s['anchor'] }}</p>
                <p><strong>Reason:</strong> {{ $s['reason'] }}</p>
              </div>
              <div class=\"actions\">
                <button class=\"btn secondary\" onclick=\"navigator.clipboard.writeText('{{ $s['anchor'] ?? '' }}')\">Copy Anchor</button>
              </div>
            </div>
          @endforeach
        @endif
      </div>

      <!-- Schema Panel -->
      <div class=\"card tabpanel\" role=\"tabpanel\" id=\"panel-Schema\" aria-labelledby=\"tab-Schema\">
        <h3>Detected Schema</h3>
        @php $schema = $schema ?? []; @endphp
        @if(empty($schema))
          <p style=\"color:#9ca3af\">No JSON‑LD detected.</p>
        @else
          <ul style=\"margin:0; padding-left:18px\">
            @foreach($schema as $type => $node)
              <li><strong>{{ $type }}</strong> — keys: {{ implode(', ', array_keys((array)$node)) }}</li>
            @endforeach
          </ul>
        @endif>

        <h3 style=\"margin-top:14px\">Schema Opportunities</h3>
        <div class=\"fix\">
          <div class=\"stripe\" style=\"background:var(--good)\"></div>
          <div class=\"body\">
            <h4>Add FAQ</h4>
            <p><strong>Why:</strong> Can earn indented FAQs and expand SERP real estate.</p>
            <p><strong>Fix:</strong> Add <code>FAQPage</code> JSON‑LD with 2–5 Q/A; ensure content is visible on page.</p>
          </div>
          <div class=\"actions\">
            <button class=\"btn secondary\" onclick=\"window.dispatchEvent(new CustomEvent('open-faq-builder'))\">Open FAQ Builder</button>
          </div>
        </div>
      </div>

      <!-- Readability Panel -->
      <div class=\"card tabpanel\" role=\"tabpanel\" id=\"panel-Readability\" aria-labelledby=\"tab-Readability\">
        @php $read = $readability ?? []; @endphp
        <h3>Readability & Tone</h3>
        <div style=\"display:grid; grid-template-columns: repeat(3,1fr); gap:10px\">
          <div class=\"card\" style=\"padding:10px\">
            <div style=\"font-size:12px; color:#9ca3af\">Grade Level</div>
            <div style=\"font-size:20px; font-weight:700\">{{ $read['grade'] ?? '—' }}</div>
          </div>
          <div class=\"card\" style=\"padding:10px\">
            <div style=\"font-size:12px; color:#9ca3af\">Passive Voice</div>
            <div style=\"font-size:20px; font-weight:700\">{{ $read['passive'] ?? '—' }}%</div>
          </div>
          <div class=\"card\" style=\"padding:10px\">
            <div style=\"font-size:12px; color:#9ca3af\">Long Sentences</div>
            <div style=\"font-size:20px; font-weight:700\">{{ $read['long'] ?? '—' }}</div>
          </div>
        </div>

        <div style=\"margin-top:12px\">
          <div class=\"fix\">
            <div class=\"stripe\" style=\"background:var(--warn)\"></div>
            <div class=\"body\">
              <h4>Make it simpler</h4>
              <p><strong>Why:</strong> Readers scan; simpler sentences improve dwell time and conversions.</p>
              <p><strong>Fix:</strong> Shorten sentences to 20–25 words, use active voice, replace jargon with simple words.</p>
            </div>
            <div class=\"actions\"><button class=\"btn secondary copy-fix\">Copy</button></div>
          </div>
        </div>
      </div>

      <!-- Performance Panel -->
      <div class=\"card tabpanel\" role=\"tabpanel\" id=\"panel-Performance\" aria-labelledby=\"tab-Performance\">
        @php $cwv = $cwv ?? []; @endphp
        <h3>Core Web Vitals (snapshot)</h3>
        <div style=\"display:grid; grid-template-columns: repeat(3,1fr); gap:10px\">
          @php
            $lcp = $cwv['LCP'] ?? null; $cls = $cwv['CLS'] ?? null; $inp = $cwv['INP'] ?? null;
          @endphp
          <div class=\"card\" style=\"padding:10px\">
            <div style=\"font-size:12px; color:#9ca3af\">LCP</div>
            <div style=\"font-size:20px; font-weight:700\">{{ $lcp !== null ? $lcp.'s' : '—' }}</div>
          </div>
          <div class=\"card\" style=\"padding:10px\">
            <div style=\"font-size:12px; color:#9ca3af\">CLS</div>
            <div style=\"font-size:20px; font-weight:700\">{{ $cls !== null ? $cls : '—' }}</div>
          </div>
          <div class=\"card\" style=\"padding:10px\">
            <div style=\"font-size:12px; color:#9ca3af\">INP</div>
            <div style=\"font-size:20px; font-weight:700\">{{ $inp !== null ? $inp.'ms' : '—' }}</div>
          </div>
        </div>

        <div style=\"margin-top:12px\">
          <div class=\"fix\">
            <div class=\"stripe\" style=\"background:var(--bad)\"></div>
            <div class=\"body\">
              <h4>Optimize images & fonts</h4>
              <p><strong>Why:</strong> Heavy hero media and blocking fonts hurt LCP/INP.</p>
              <p><strong>Fix:</strong> Use <code>loading=\"lazy\"</code>, compress images (WebP/AVIF), add <code>font-display:swap</code>, inline critical CSS.</p>
            </div>
            <div class=\"actions\"><button class=\"btn secondary copy-fix\">Copy</button></div>
          </div>
        </div>
      </div>

      <!-- Technical Panel -->
      <div class=\"card tabpanel\" role=\"tabpanel\" id=\"panel-Technical\" aria-labelledby=\"tab-Technical\">
        <h3>Technical Checks</h3>
        @php $tech = $technical ?? []; @endphp
        <ul style=\"margin:0; padding-left:18px\">
          <li>Indexability: <strong>{{ $tech['indexable'] ?? '—' }}</strong></li>
          <li>Canonical: <strong>{{ $tech['canonical'] ?? '—' }}</strong></li>
          <li>Robots: <strong>{{ $tech['robots'] ?? '—' }}</strong></li>
          <li>Hreflang: <strong>{{ $tech['hreflang'] ?? '—' }}</strong></li>
          <li>Sitemap: <strong>{{ $tech['sitemap'] ?? '—' }}</strong></li>
        </ul>
      </div>

      <!-- A11y Panel -->
      <div class=\"card tabpanel\" role=\"tabpanel\" id=\"panel-A11y\" aria-labelledby=\"tab-A11y\">
        <h3>Accessibility</h3>
        @php $a11y = $a11y ?? []; @endphp
        @if(empty($a11y))
          <p style=\"color:#9ca3af\">No issues detected or not analyzed yet.</p>
        @else
          <ul style=\"margin:0; padding-left:18px\">
            @foreach($a11y as $issue)
              <li>{{ $issue }}</li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </section>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const clamp = (v,min,max)=>Math.max(min,Math.min(max,Number(v||0)));

  // Paint overall ring
  const ring = document.getElementById('overallRing');
  if(ring){
    const v = clamp(ring.dataset.score,0,100);
    ring.style.background = `conic-gradient(${v>=85?'#22c55e':(v>=50?'#f59e0b':'#ef4444')} ${v*3.6}deg, #3f3f46 0)`;
    const scoreEl = document.getElementById('overallScore');
    if(scoreEl && (scoreEl.textContent.trim()==='—' || scoreEl.textContent.trim()==='')) scoreEl.textContent = v;
  }

  // Animate bars
  document.querySelectorAll('.bar__fill').forEach(el=>{
    const v = clamp(el.dataset.progress,0,100);
    requestAnimationFrame(()=>{ el.style.width = v + '%'; });
  });

  // Chips state
  document.querySelectorAll('.chip[data-score]').forEach(ch=>{
    const v = clamp(ch.dataset.score,0,100);
    ch.classList.remove('good','warn','bad');
    ch.classList.add(v>=85?'good':(v>=50?'warn':'bad'));
  });

  // Tabs
  const tabBtns = Array.from(document.querySelectorAll('.tab-btn'));
  const panels = Array.from(document.querySelectorAll('.tabpanel'));
  tabBtns.forEach(btn=>btn.addEventListener('click',()=>{
    tabBtns.forEach(b=>b.setAttribute('aria-selected','false'));
    btn.setAttribute('aria-selected','true');
    panels.forEach(p=>p.classList.remove('active'));
    const id = btn.id.replace('tab-','panel-');
    document.getElementById(id)?.classList.add('active');
  }));

  // Copy fix
  const copy = (txt)=>navigator.clipboard?.writeText(txt).then(()=>{
    const toast = document.createElement('div');
    toast.textContent = 'Copied';
    Object.assign(toast.style,{position:'fixed',bottom:'16px',left:'50%',transform:'translateX(-50%)',background:'rgba(0,0,0,.7)',color:'#fff',padding:'8px 12px',borderRadius:'10px',zIndex:9999});
    document.body.appendChild(toast); setTimeout(()=>toast.remove(),1000);
  }):alert('Copied');
  document.querySelectorAll('.copy-fix').forEach(btn=>btn.addEventListener('click',e=>{
    const fix = e.currentTarget.closest('.fix'); const t = fix?.querySelector('.fix-text')?.textContent || '';
    copy(t.trim());
  }));

  // SERP preview
  const serp = document.getElementById('serp');
  const counts = document.getElementById('serpCounts');
  function updateCounts(){
    const t = document.getElementById('serpTitle')?.textContent||'';
    const d = document.getElementById('serpDesc')?.textContent||'';
    counts.textContent = `Title: ${t.length} chars • Description: ${d.length} chars`;
  }
  updateCounts();
  serp?.querySelectorAll('.serp-mode').forEach(b=>b.addEventListener('click',()=>{
    serp.classList.toggle('mobile', b.dataset.mode==='mobile');
    serp.querySelectorAll('.serp-mode').forEach(x=>x.classList.remove('active'));
    b.classList.add('active');
    serp.querySelectorAll('.serp-mode').forEach(x=>x.setAttribute('aria-selected', x===b ? 'true':'false'));
  }));

  // Stepper (visual only; ready for live events)
  const analyzeBtn = document.getElementById('analyzeBtn');
  analyzeBtn?.addEventListener('click',()=>{
    // Optional: hook into Echo events here for real-time updates
  });
})();
</script>
@endpush
