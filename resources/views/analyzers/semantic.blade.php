@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* =====================
     Semantic Analyzer UI — Namespaced (no global overrides)
     Wrapper: #sa-root
     ===================== */
  #sa-root {
    --sa-app-bg:#03041c;
    --sa-app-fg:#e5e7eb;
    --sa-muted:#a3a3a3;
    --sa-border-weak:rgba(255,255,255,.08);
    --sa-border-strong:rgba(255,255,255,.14);
    --sa-pill-bg:rgba(255,255,255,.06);
    --sa-pill-br:rgba(255,255,255,.12);

    --sa-good:#22c55e;
    --sa-warn:#f59e0b;
    --sa-bad:#ef4444;

    --sa-grad-1: linear-gradient(90deg,#8b5cf6 0%,#ec4899 30%,#f59e0b 60%,#22c55e 100%);
    --sa-grad-2: linear-gradient(135deg,#00d2ff 0%,#3a7bd5 50%,#9333ea 100%);

    --sa-card-bg: rgba(255,255,255,.03);
    --sa-glow: 0 0 0 .5px rgba(255,255,255,.12), 0 8px 24px rgba(44, 0, 120, .35);
  }
  #sa-root .sa-container{ max-width:1200px; margin:0 auto; padding:24px; color:var(--sa-app-fg); }
  #sa-root .sa-surface{ background:var(--sa-app-bg); border-radius:16px; padding:16px; }

  #sa-root .sa-skip{ position:absolute; left:-9999px; }
  #sa-root .sa-skip:focus{ left:12px; top:12px; background:#111827; color:#fff; padding:8px 12px; border-radius:8px; z-index:1000; }

  /* Header / Toolbar */
  #sa-root .sa-topbar{ display:flex; gap:12px; align-items:center; justify-content:space-between; margin-bottom:18px; flex-wrap:wrap; }
  #sa-root .sa-urlbox{ display:flex; gap:10px; align-items:center; flex:1 1 520px; background:var(--sa-card-bg); border:1px solid var(--sa-border-weak); padding:10px 12px; border-radius:12px; box-shadow: var(--sa-glow); }
  #sa-root .sa-urlbox input[type=url]{ flex:1; background:transparent; color:var(--sa-app-fg); border:none; outline:none; font-size:14px; }
  #sa-root .sa-btn{ border:none; background:var(--sa-grad-2); color:#fff; padding:10px 14px; border-radius:12px; cursor:pointer; box-shadow: var(--sa-glow); transition: transform .15s ease, filter .2s ease; }
  #sa-root .sa-btn:hover{ transform: translateY(-1px); filter: brightness(1.05); }
  #sa-root .sa-btn.sa-secondary{ background:var(--sa-card-bg); color:var(--sa-app-fg); border:1px solid var(--sa-border-weak); }
  #sa-root .sa-toolbar{ display:flex; gap:8px; flex-wrap:wrap; }
  #sa-root .sa-quota{ background:var(--sa-pill-bg); border:1px solid var(--sa-pill-br); padding:6px 10px; border-radius:999px; font-size:12px; color:#cbd5e1; display:flex; align-items:center; gap:6px; }

  /* Hero Score Card */
  #sa-root .sa-hero{ display:grid; grid-template-columns: 320px 1fr; gap:18px; margin:18px 0 10px; }
  @media (max-width: 980px){ #sa-root .sa-hero{ grid-template-columns:1fr; } }

  #sa-root .sa-card{ background:var(--sa-card-bg); border:1px solid var(--sa-border-weak); border-radius:16px; padding:16px; box-shadow: var(--sa-glow); }
  #sa-root .sa-card h3{ margin:0 0 10px; font-size:16px; font-weight:600; letter-spacing:.3px; color:#e5e7eb; }

  #sa-root .sa-ring{ position:relative; width:100%; aspect-ratio:1/1; border-radius:50%; display:grid; place-items:center; background: conic-gradient(#3f3f46 0deg, #3f3f46 360deg); }
  #sa-root .sa-ring__inner{ position:absolute; inset:10%; background:var(--sa-app-bg); border-radius:50%; display:grid; place-items:center; }
  #sa-root .sa-ring__score{ font-size:40px; font-weight:800; }
  #sa-root .sa-ring__label{ font-size:12px; color:var(--sa-muted); margin-top:4px; }

  #sa-root .sa-legend{ display:flex; align-items:center; gap:10px; margin:6px 0 12px; }
  #sa-root .sa-title{ font-weight:800; font-size:18px; background: var(--sa-grad-1); -webkit-background-clip:text; background-clip:text; color:transparent; filter: drop-shadow(0 0 12px rgba(147,51,234,.35)); display:flex; align-items:center; gap:8px; }
  #sa-root .sa-icon{ width:18px; height:18px; border-radius:6px; background: var(--sa-grad-1); box-shadow: 0 0 24px rgba(236,72,153,.45); animation: sa-orb 4s linear infinite; }
  @keyframes sa-orb{ 0%{ transform: rotate(0) } 100%{ transform: rotate(360deg) } }

  #sa-root .sa-chips{ display:flex; flex-wrap:wrap; gap:8px; }
  #sa-root .sa-chip{ display:inline-flex; align-items:center; gap:8px; padding:6px 10px; border-radius:999px; background:var(--sa-pill-bg); border:1px solid var(--sa-pill-br); font-size:12px; color:#e5e7eb; }
  #sa-root .sa-chip .sa-dot{ width:8px; height:8px; border-radius:999px; background:#6b7280; box-shadow:0 0 12px rgba(255,255,255,.14); }
  #sa-root .sa-chip.sa-good .sa-dot{ background:var(--sa-good) }
  #sa-root .sa-chip.sa-warn .sa-dot{ background:var(--sa-warn) }
  #sa-root .sa-chip.sa-bad  .sa-dot{ background:var(--sa-bad) }

  /* Grid */
  #sa-root .sa-grid{ display:grid; grid-template-columns: 1.1fr .9fr; gap:18px; }
  @media (max-width: 1080px){ #sa-root .sa-grid{ grid-template-columns: 1fr; } }

  /* Progress Bars */
  #sa-root .sa-bar{ height:10px; background:#0b0730; border-radius:999px; overflow:hidden; border:1px solid var(--sa-border-strong); }
  #sa-root .sa-bar__fill{ height:100%; width:0%; background: linear-gradient(90deg,#22c55e,#16a34a); transition: width 1s ease; }
  #sa-root .sa-bar__fill.sa-warn{ background: linear-gradient(90deg,#f59e0b,#d97706); }
  #sa-root .sa-bar__fill.sa-bad{ background: linear-gradient(90deg,#ef4444,#dc2626); }

  /* Tabs */
  #sa-root .sa-tabs{ display:flex; gap:8px; flex-wrap:wrap; padding:6px; background:var(--sa-card-bg); border:1px solid var(--sa-border-weak); border-radius:12px; }
  #sa-root .sa-tab-btn{ background:transparent; border:none; color:#cbd5e1; padding:8px 10px; border-radius:10px; cursor:pointer; }
  #sa-root .sa-tab-btn[aria-selected=\"true\"]{ background:rgba(255,255,255,.06); color:#fff; border:1px solid var(--sa-border-strong); box-shadow: var(--sa-glow); }
  #sa-root .sa-tabpanel{ display:none; }
  #sa-root .sa-tabpanel.sa-active{ display:block; }

  /* Suggestion (Fix) Cards */
  #sa-root .sa-fix{ display:grid; grid-template-columns: 10px 1fr auto; gap:12px; padding:12px; border:1px solid var(--sa-border-weak); border-radius:12px; background:rgba(255,255,255,.02); }
  #sa-root .sa-fix+.sa-fix{ margin-top:10px; }
  #sa-root .sa-fix .sa-stripe{ border-radius:999px; }
  #sa-root .sa-fix .sa-body h4{ margin:0 0 6px; font-size:14px; }
  #sa-root .sa-fix .sa-body p{ margin:0 0 6px; color:#cbd5e1; font-size:13px; }
  #sa-root .sa-fix .sa-actions{ display:flex; gap:6px; align-items:start; }
  #sa-root .sa-tag{ display:inline-flex; align-items:center; gap:6px; font-size:12px; padding:4px 8px; border-radius:999px; background:var(--sa-pill-bg); border:1px solid var(--sa-pill-br); color:#d1d5db; }

  /* SERP Preview */
  #sa-root .sa-serp{ border:1px solid var(--sa-border-weak); border-radius:12px; padding:12px; background:rgba(255,255,255,.02); }
  #sa-root .sa-serp .sa-toggle{ display:flex; gap:8px; margin-bottom:10px; }
  #sa-root .sa-serp .sa-toggle button{ background:transparent; border:1px solid var(--sa-border-weak); color:#cbd5e1; padding:6px 8px; border-radius:8px; cursor:pointer; }
  #sa-root .sa-serp .sa-toggle button.sa-active{ background:rgba(255,255,255,.06); color:#fff; box-shadow: var(--sa-glow); }
  #sa-root .sa-serp .sa-viewport{ max-width:600px; border:1px dashed var(--sa-border-weak); border-radius:10px; padding:10px 12px; }
  #sa-root .sa-serp.sa-mobile .sa-viewport{ max-width:360px; }
  #sa-root .sa-serp .sa-t{ color:#1e90ff; font-size:18px; line-height:1.3; margin:2px 0 4px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden; }
  #sa-root .sa-serp .sa-u{ color:var(--sa-good); font-size:12px; margin:0 0 4px; }
  #sa-root .sa-serp .sa-d{ color:#9ca3af; font-size:13px; line-height:1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
  #sa-root .sa-serp .sa-counts{ font-size:11px; color:#9ca3af; margin-top:6px; }

  /* Headings Map */
  #sa-root .sa-hmap{ display:block; }
  #sa-root .sa-hitem{ padding:8px 10px; border:1px solid var(--sa-border-weak); background:rgba(255,255,255,.02); border-radius:10px; margin-bottom:6px; }
  #sa-root .sa-hitem .sa-label{ font-size:11px; color:#94a3b8; margin-right:8px; }
  #sa-root .sa-hitem.sa-bad{ border-color: rgba(239,68,68,.5); background: rgba(239,68,68,.06); }

  /* Entities Table */
  #sa-root table.sa-entities{ width:100%; border-collapse:collapse; }
  #sa-root table.sa-entities th, #sa-root table.sa-entities td{ padding:8px 10px; border-bottom:1px solid var(--sa-border-weak); text-align:left; font-size:13px; }
  #sa-root table.sa-entities th{ color:#cbd5e1; font-weight:600; }
  #sa-root table.sa-entities td .sa-copy{ background:transparent; border:1px solid var(--sa-border-weak); color:#cbd5e1; padding:4px 8px; border-radius:8px; cursor:pointer; }

  /* Skeletons */
  #sa-root .sa-skeleton{ position:relative; overflow:hidden; background:#15132f; border-radius:8px; }
  #sa-root .sa-skeleton::after{ content:\"\"; position:absolute; inset:0; background: linear-gradient(90deg, transparent, rgba(255,255,255,.06), transparent); animation: sa-shimmer 1.4s infinite; transform: translateX(-100%); }
  @keyframes sa-shimmer{ 0%{ transform: translateX(-100%) } 100%{ transform: translateX(100%) } }

  /* Focus visibility */
  #sa-root :focus-visible{ outline: 2px solid #60a5fa; outline-offset: 2px; border-radius:6px; }

  /* RTL support */
  [dir=\"rtl\"] #sa-root .sa-title{ letter-spacing:0; }
</style>
@endpush

@section('content')
<div id=\"sa-root\">
  <a href=\"#sa-main\" class=\"sa-skip\">Skip to content</a>
  <div class=\"sa-container\">
    <div class=\"sa-surface\">
      <!-- Topbar & Actions -->
      <div class=\"sa-topbar\">
        <form class=\"sa-urlbox\" method=\"GET\" action=\"{{ url()->current() }}\" role=\"search\">
          <input type=\"url\" name=\"url\" value=\"{{ request('url') }}\" placeholder=\"Paste a URL to analyze...\" aria-label=\"URL to analyze\">
          <button type=\"submit\" class=\"sa-btn\" id=\"sa-analyzeBtn\" aria-label=\"Analyze\">Analyze</button>
        </form>

        <div class=\"sa-toolbar\">
          <div class=\"sa-quota\" title=\"Daily quota\">
            @php
              $used = $quotaUsed ?? 0; $limit = $quotaLimit ?? 200;
            @endphp
            <span>🧮 {{ $used }}/{{ $limit }} today</span>
          </div>
          <button class=\"sa-btn sa-secondary\" onclick=\"window.dispatchEvent(new CustomEvent('sa-export-request',{detail:'pdf'}))\">Export PDF</button>
          <button class=\"sa-btn sa-secondary\" onclick=\"window.dispatchEvent(new CustomEvent('sa-export-request',{detail:'json'}))\">Export JSON</button>
          <button class=\"sa-btn sa-secondary\" onclick=\"window.dispatchEvent(new CustomEvent('sa-share-link'))\">Share Link</button>
          <button class=\"sa-btn\" onclick=\"location.reload()\">Refresh</button>
        </div>
      </div>

      <!-- Hero: Overall + Category chips -->
      @php
        $score = $overall_score ?? null;
        $cats  = $categoryScores ?? [];
        $defaults = ['Content','Links','Schema','Readability','Performance','Technical','A11y'];
      @endphp
      <section class=\"sa-hero\" aria-labelledby=\"sa-heroLabel\">
        <div class=\"sa-card\">
          <div class=\"sa-legend\">
            <div class=\"sa-title\"><span class=\"sa-icon\" aria-hidden=\"true\"></span> <span id=\"sa-heroLabel\">Content Optimization</span></div>
          </div>
          <div class=\"sa-ring\" id=\"sa-overallRing\" aria-label=\"Overall score\" role=\"img\" data-score=\"{{ $score ?? 0 }}\">
            <div class=\"sa-ring__inner\">
              <div class=\"sa-ring__content\" style=\"text-align:center\">
                <div class=\"sa-ring__score\" id=\"sa-overallScore\">{{ $score ?? '—' }}</div>
                <div class=\"sa-ring__label\">Overall score</div>
              </div>
            </div>
          </div>
        </div>

        <div class=\"sa-card\">
          <h3>Categories</h3>
          <div class=\"sa-chips\">
            @foreach($defaults as $cat)
              @php $v = $cats[$cat] ?? null;
                   $state = is_null($v) ? '' : ($v>=85 ? 'sa-good' : ($v>=50 ? 'sa-warn':'sa-bad')); @endphp
              <div class=\"sa-chip {{ $state }}\" data-score=\"{{ $v ?? '' }}\" aria-label=\"{{ $cat }} score {{ $v ?? 'pending' }}\">
                <span class=\"sa-dot\"></span>
                <span>{{ $cat }}</span>
                <strong>{{ $v !== null ? $v : '—' }}</strong>
              </div>
            @endforeach
          </div>

          <div style=\"margin-top:14px\">
            @foreach($defaults as $cat)
              @php $v = $cats[$cat] ?? null; $cls = is_null($v) ? '' : ($v>=85 ? '' : ($v>=50 ? 'sa-warn':'sa-bad')); @endphp
              <div style=\"display:flex; align-items:center; gap:10px; margin:8px 0\" aria-label=\"{{ $cat }} progress\">
                <div style=\"width:130px; color:#cbd5e1; font-size:12px\">{{ $cat }}</div>
                <div class=\"sa-bar\" style=\"flex:1\">
                  <div class=\"sa-bar__fill {{ $cls }}\" data-progress=\"{{ $v ?? 0 }}\"></div>
                </div>
                <div style=\"width:30px; text-align:right; font-size:12px\">{{ $v !== null ? $v.'%' : '—' }}</div>
              </div>
            @endforeach
          </div>
        </div>
      </section>

      <!-- Main Grid -->
      <section id=\"sa-main\" class=\"sa-grid\" aria-label=\"Analyzer details\">

        <!-- Left: High‑impact Fixes + SERP Preview -->
        <div class=\"sa-left\">
          <div class=\"sa-card\">
            <div class=\"sa-legend\">
              <div class=\"sa-title\"><span class=\"sa-icon\" aria-hidden=\"true\"></span> High‑Impact Fixes</div>
            </div>

            @php $fixes = $suggestions ?? []; @endphp
            @if(empty($fixes))
              <div class=\"sa-skeleton\" style=\"height:64px; margin-bottom:8px\"></div>
              <div class=\"sa-skeleton\" style=\"height:64px; margin-bottom:8px\"></div>
              <div class=\"sa-skeleton\" style=\"height:64px;\"></div>
            @else
              @foreach($fixes as $f)
                @php
                  $sev = strtolower($f['severity'] ?? 'medium');
                  $color = $sev==='high' ? 'var(--sa-bad)' : ($sev==='low' ? 'var(--sa-good)' : 'var(--sa-warn)');
                @endphp
                <article class=\"sa-fix\" aria-live=\"polite\">
                  <div class=\"sa-stripe\" style=\"background:{{ $color }}\"></div>
                  <div class=\"sa-body\">
                    <h4>{{ $f['title'] ?? 'Suggested improvement' }}</h4>
                    @if(!empty($f['why']))<p><strong>Why:</strong> {{ $f['why'] }}</p>@endif
                    @if(!empty($f['how']))<p><strong>Fix:</strong> <span class=\"sa-fix-text\">{{ $f['how'] }}</span></p>@endif
                    @if(!empty($f['snippet']))<pre style=\"margin:6px 0; white-space:pre-wrap; color:#9ca3af\">{{ $f['snippet'] }}</pre>@endif
                    <div class=\"sa-tags\" style=\"margin-top:6px; display:flex; gap:6px; flex-wrap:wrap\">
                      <span class=\"sa-tag\">Category: {{ $f['category'] ?? 'General' }}</span>
                      <span class=\"sa-tag\">Severity: {{ ucfirst($sev) }}</span>
                    </div>
                  </div>
                  <div class=\"sa-actions\">
                    <button class=\"sa-btn sa-secondary sa-copy-fix\" title=\"Copy fix\">Copy</button>
                  </div>
                </article>
              @endforeach
            @endif
          </div>

          <div class=\"sa-card\" style=\"margin-top:16px\">
            <div class=\"sa-legend\">
              <div class=\"sa-title\"><span class=\"sa-icon\" aria-hidden=\"true\"></span> SERP Snippet Preview</div>
            </div>

            @php
              $serp = $serp ?? [
                'title' => $metaTitle ?? null,
                'description' => $metaDescription ?? null,
                'url' => $pageUrl ?? request('url'),
              ];
            @endphp

            <div class=\"sa-serp\" id=\"sa-serp\">
              <div class=\"sa-toggle\" role=\"tablist\" aria-label=\"SERP device mode\">
                <button type=\"button\" class=\"sa-mode sa-active\" data-mode=\"desktop\" aria-selected=\"true\">Desktop</button>
                <button type=\"button\" class=\"sa-mode\" data-mode=\"mobile\" aria-selected=\"false\">Mobile</button>
              </div>
              <div class=\"sa-viewport\">
                <div class=\"sa-t\" id=\"sa-serpTitle\" title=\"{{ $serp['title'] ?? '—' }}\">{{ $serp['title'] ?? '—' }}</div>
                <div class=\"sa-u\" id=\"sa-serpUrl\">{{ $serp['url'] ?? '—' }}</div>
                <div class=\"sa-d\" id=\"sa-serpDesc\">{{ $serp['description'] ?? '—' }}</div>
                <div class=\"sa-counts\" id=\"sa-serpCounts\"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Right: Tabs (Content, Links, Schema, Readability, Performance, Technical, A11y) -->
        <div class=\"sa-right\">
          <div class=\"sa-tabs\" role=\"tablist\" aria-label=\"Details tabs\">
            @php $tabNames = ['Content','Links','Schema','Readability','Performance','Technical','A11y']; @endphp
            @foreach($tabNames as $i=>$t)
              <button class=\"sa-tab-btn\" role=\"tab\" aria-selected=\"{{ $i===0 ? 'true':'false' }}\" aria-controls=\"sa-panel-{{ $t }}\" id=\"sa-tab-{{ $t }}\">{{ $t }}</button>
            @endforeach
          </div>

          <!-- Content Panel -->
          <div class=\"sa-card sa-tabpanel sa-active\" role=\"tabpanel\" id=\"sa-panel-Content\" aria-labelledby=\"sa-tab-Content\">
            <h3>Headings Map</h3>
            @php $headings = $headings ?? []; @endphp
            @if(empty($headings))
              <div class=\"sa-skeleton\" style=\"height:32px; margin-bottom:6px\"></div>
              <div class=\"sa-skeleton\" style=\"height:32px; margin-bottom:6px\"></div>
              <div class=\"sa-skeleton\" style=\"height:32px;\"></div>
            @else
              <div class=\"sa-hmap\">
                @foreach($headings as $h)
                  @php
                    $lvl = $h['level'] ?? 2;
                    $bad = $h['bad'] ?? false;
                    $text= $h['text'] ?? '';
                  @endphp
                  <div class=\"sa-hitem {{ $bad ? 'sa-bad':'' }}\">
                    <span class=\"sa-label\">H{{ $lvl }}</span>
                    <span>{{ $text }}</span>
                  </div>
                @endforeach
              </div>
            @endif

            <h3 style=\"margin-top:14px\">Topic Coverage</h3>
            @php $coverage = $cats['Content'] ?? null; @endphp
            <div class=\"sa-bar\" aria-label=\"Topic coverage\">
              @php $cls = is_null($coverage) ? '' : ($coverage>=85 ? '' : ($coverage>=50 ? 'sa-warn':'sa-bad')); @endphp
              <div class=\"sa-bar__fill {{ $cls }}\" data-progress=\"{{ $coverage ?? 0 }}\"></div>
            </div>
          </div>

          <!-- Links Panel -->
          <div class=\"sa-card sa-tabpanel\" role=\"tabpanel\" id=\"sa-panel-Links\" aria-labelledby=\"sa-tab-Links\">
            <h3>Internal Linking Suggestions</h3>
            @php $linkSugs = $linkSuggestions ?? []; @endphp
            @if(empty($linkSugs))
              <p style=\"color:#9ca3af\">No suggestions detected or not analyzed yet.</p>
            @else
              @foreach($linkSugs as $s)
                <div class=\"sa-fix\">
                  <div class=\"sa-stripe\" style=\"background:var(--sa-warn)\"></div>
                  <div class=\"sa-body\">
                    <h4>Add internal link to <em>{{ $s['target'] }}</em></h4>
                    <p><strong>Anchor:</strong> {{ $s['anchor'] }}</p>
                    <p><strong>Reason:</strong> {{ $s['reason'] }}</p>
                  </div>
                  <div class=\"sa-actions\">
                    <button class=\"sa-btn sa-secondary\" onclick=\"navigator.clipboard.writeText('{{ $s['anchor'] ?? '' }}')\">Copy Anchor</button>
                  </div>
                </div>
              @endforeach
            @endif
          </div>

          <!-- Schema Panel -->
          <div class=\"sa-card sa-tabpanel\" role=\"tabpanel\" id=\"sa-panel-Schema\" aria-labelledby=\"sa-tab-Schema\">
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
            @endif

            <h3 style=\"margin-top:14px\">Schema Opportunities</h3>
            <div class=\"sa-fix\">
              <div class=\"sa-stripe\" style=\"background:var(--sa-good)\"></div>
              <div class=\"sa-body\">
                <h4>Add FAQ</h4>
                <p><strong>Why:</strong> Can earn indented FAQs and expand SERP real estate.</p>
                <p><strong>Fix:</strong> Add <code>FAQPage</code> JSON‑LD with 2–5 Q/A; ensure content is visible on page.</p>
              </div>
              <div class=\"sa-actions\">
                <button class=\"sa-btn sa-secondary\" onclick=\"window.dispatchEvent(new CustomEvent('sa-open-faq-builder'))\">Open FAQ Builder</button>
              </div>
            </div>
          </div>

          <!-- Readability Panel -->
          <div class=\"sa-card sa-tabpanel\" role=\"tabpanel\" id=\"sa-panel-Readability\" aria-labelledby=\"sa-tab-Readability\">
            @php $read = $readability ?? []; @endphp
            <h3>Readability & Tone</h3>
            <div style=\"display:grid; grid-template-columns: repeat(3,1fr); gap:10px\">
              <div class=\"sa-card\" style=\"padding:10px\">
                <div style=\"font-size:12px; color:#9ca3af\">Grade Level</div>
                <div style=\"font-size:20px; font-weight:700\">{{ $read['grade'] ?? '—' }}</div>
              </div>
              <div class=\"sa-card\" style=\"padding:10px\">
                <div style=\"font-size:12px; color:#9ca3af\">Passive Voice</div>
                <div style=\"font-size:20px; font-weight:700\">{{ $read['passive'] ?? '—' }}%</div>
              </div>
              <div class=\"sa-card\" style=\"padding:10px\">
                <div style=\"font-size:12px; color:#9ca3af\">Long Sentences</div>
                <div style=\"font-size:20px; font-weight:700\">{{ $read['long'] ?? '—' }}</div>
              </div>
            </div>

            <div style=\"margin-top:12px\">
              <div class=\"sa-fix\">
                <div class=\"sa-stripe\" style=\"background:var(--sa-warn)\"></div>
                <div class=\"sa-body\">
                  <h4>Make it simpler</h4>
                  <p><strong>Why:</strong> Readers scan; simpler sentences improve dwell time and conversions.</p>
                  <p><strong>Fix:</strong> Shorten sentences to 20–25 words, use active voice, replace jargon with simple words.</p>
                </div>
                <div class=\"sa-actions\"><button class=\"sa-btn sa-secondary sa-copy-fix\">Copy</button></div>
              </div>
            </div>
          </div>

          <!-- Performance Panel -->
          <div class=\"sa-card sa-tabpanel\" role=\"tabpanel\" id=\"sa-panel-Performance\" aria-labelledby=\"sa-tab-Performance\">
            @php $cwv = $cwv ?? []; @endphp
            <h3>Core Web Vitals (snapshot)</h3>
            <div style=\"display:grid; grid-template-columns: repeat(3,1fr); gap:10px\">
              @php
                $lcp = $cwv['LCP'] ?? null; $cls = $cwv['CLS'] ?? null; $inp = $cwv['INP'] ?? null;
              @endphp
              <div class=\"sa-card\" style=\"padding:10px\">
                <div style=\"font-size:12px; color:#9ca3af\">LCP</div>
                <div style=\"font-size:20px; font-weight:700\">{{ $lcp !== null ? $lcp.'s' : '—' }}</div>
              </div>
              <div class=\"sa-card\" style=\"padding:10px\">
                <div style=\"font-size:12px; color:#9ca3af\">CLS</div>
                <div style=\"font-size:20px; font-weight:700\">{{ $cls !== null ? $cls : '—' }}</div>
              </div>
              <div class=\"sa-card\" style=\"padding:10px\">
                <div style=\"font-size:12px; color:#9ca3af\">INP</div>
                <div style=\"font-size:20px; font-weight:700\">{{ $inp !== null ? $inp.'ms' : '—' }}</div>
              </div>
            </div>

            <div style=\"margin-top:12px\">
              <div class=\"sa-fix\">
                <div class=\"sa-stripe\" style=\"background:var(--sa-bad)\"></div>
                <div class=\"sa-body\">
                  <h4>Optimize images & fonts</h4>
                  <p><strong>Why:</strong> Heavy hero media and blocking fonts hurt LCP/INP.</p>
                  <p><strong>Fix:</strong> Use <code>loading=\"lazy\"</code>, compress images (WebP/AVIF), add <code>font-display:swap</code>, inline critical CSS.</p>
                </div>
                <div class=\"sa-actions\"><button class=\"sa-btn sa-secondary sa-copy-fix\">Copy</button></div>
              </div>
            </div>
          </div>

          <!-- Technical Panel -->
          <div class=\"sa-card sa-tabpanel\" role=\"tabpanel\" id=\"sa-panel-Technical\" aria-labelledby=\"sa-tab-Technical\">
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
          <div class=\"sa-card sa-tabpanel\" role=\"tabpanel\" id=\"sa-panel-A11y\" aria-labelledby=\"sa-tab-A11y\">
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
  </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
  const root = document.getElementById('sa-root');
  if(!root) return;
  const clamp = (v,min,max)=>Math.max(min,Math.min(max,Number(v||0)));

  // Paint overall ring
  const ring = root.querySelector('#sa-overallRing');
  if(ring){
    const v = clamp(ring.dataset.score,0,100);
    ring.style.background = `conic-gradient(${v>=85?'#22c55e':(v>=50?'#f59e0b':'#ef4444')} ${v*3.6}deg, #3f3f46 0)`;
    const scoreEl = root.querySelector('#sa-overallScore');
    if(scoreEl && (scoreEl.textContent.trim()==='—' || scoreEl.textContent.trim()==='')) scoreEl.textContent = v;
  }

  // Animate bars
  root.querySelectorAll('.sa-bar__fill').forEach(el=>{
    const v = clamp(el.dataset.progress,0,100);
    requestAnimationFrame(()=>{ el.style.width = v + '%'; });
  });

  // Chips state
  root.querySelectorAll('.sa-chip[data-score]').forEach(ch=>{
    const v = clamp(ch.dataset.score,0,100);
    ch.classList.remove('sa-good','sa-warn','sa-bad');
    ch.classList.add(v>=85?'sa-good':(v>=50?'sa-warn':'sa-bad'));
  });

  // Tabs
  const tabBtns = Array.from(root.querySelectorAll('.sa-tab-btn'));
  const panels = Array.from(root.querySelectorAll('.sa-tabpanel'));
  tabBtns.forEach(btn=>btn.addEventListener('click',()=>{
    tabBtns.forEach(b=>b.setAttribute('aria-selected','false'));
    btn.setAttribute('aria-selected','true');
    panels.forEach(p=>p.classList.remove('sa-active'));
    const id = btn.id.replace('sa-tab-','sa-panel-');
    root.querySelector('#'+id)?.classList.add('sa-active');
  }));

  // Copy fix
  const copy = (txt)=>navigator.clipboard?.writeText(txt).then(()=>{
    const toast = document.createElement('div');
    toast.textContent = 'Copied';
    Object.assign(toast.style,{position:'fixed',bottom:'16px',left:'50%',transform:'translateX(-50%)',background:'rgba(0,0,0,.7)',color:'#fff',padding:'8px 12px',borderRadius:'10px',zIndex:9999});
    document.body.appendChild(toast); setTimeout(()=>toast.remove(),1000);
  }):alert('Copied');
  root.querySelectorAll('.sa-copy-fix').forEach(btn=>btn.addEventListener('click',e=>{
    const fix = e.currentTarget.closest('.sa-fix'); const t = fix?.querySelector('.sa-fix-text')?.textContent || '';
    copy(t.trim());
  }));

  // SERP preview
  const serp = root.querySelector('#sa-serp');
  const counts = root.querySelector('#sa-serpCounts');
  function updateCounts(){
    const t = root.querySelector('#sa-serpTitle')?.textContent||'';
    const d = root.querySelector('#sa-serpDesc')?.textContent||'';
    counts.textContent = `Title: ${t.length} chars • Description: ${d.length} chars`;
  }
  if(counts) updateCounts();
  serp?.querySelectorAll('.sa-mode').forEach(b=>b.addEventListener('click',()=>{
    serp.classList.toggle('sa-mobile', b.dataset.mode==='mobile');
    serp.querySelectorAll('.sa-mode').forEach(x=>x.classList.remove('sa-active'));
    b.addEventListener; b.classList.add('sa-active');
    serp.querySelectorAll('.sa-mode').forEach(x=>x.setAttribute('aria-selected', x===b ? 'true':'false'));
  }));

  // Stepper hook (visual only)
  const analyzeBtn = root.querySelector('#sa-analyzeBtn');
  analyzeBtn?.addEventListener('click',()=>{
    // Hook into Echo events for real-time updates if available
  });
})();
</script>
@endpush
