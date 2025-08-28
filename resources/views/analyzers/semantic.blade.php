@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* ===== Base / page ===== */
  html,body{background:#000!important;color:#e5e7eb}
  body,#app,section{overflow-x:hidden}
  .maxw{max-width:1150px;margin:0 auto}

  /* ===== Heading ===== */
  .title-wrap{display:flex;align-items:center;gap:14px;justify-content:center;margin-top:14px}
  .king{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;
        background:linear-gradient(135deg,#a78bfa33,#f472b633);border:1px solid #ffffff24}
  .t-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);
          -webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900}
  .byline{font-size:14px;color:#cbd5e1}
  .shoail{display:inline-block;background:linear-gradient(90deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);
          -webkit-background-clip:text;background-clip:text;color:transparent;background-size:400% 100%;
          animation:rainbowSlide 6s linear infinite,bob 3s ease-in-out infinite}
  @keyframes rainbowSlide{to{background-position:100% 50%}}
  @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  /* ===== Legend chips ===== */
  .legend{display:flex;gap:10px;justify-content:center;margin:10px 0 6px}
  .legend .badge{padding:8px 14px;border-radius:9999px;font-weight:800;border:1px solid #ffffff2a}
  .legend .g{background:#063f2c;color:#a7f3d0;border-color:#10b98166}
  .legend .o{background:#3b2a05;color:#fde68a;border-color:#f59e0b66}
  .legend .r{background:#3a0b0b;color:#fecaca;border-color:#ef444466}

  /* ===== Cards / containers ===== */
  .card{border-radius:18px;padding:18px;background:#0a0a0a; border:1px solid #ffffff1c}
  .cat-card{border-radius:18px;padding:18px;background:#111E2F;border:1px solid #ffffff1c}
  .ground-slab{border-radius:24px;padding:22px;background:#0D0E1E;border:1px solid #ffffff1c;margin-top:26px}
  .analyze-wrap{border-radius:18px;background:#020114;border:1px solid #ffffff20;
                box-shadow:inset 0 0 0 1px #ffffff0a,0 20px 60px rgba(2,1,20,.45)}

  /* ==== OUTLINE RESET (disables animated multi-color outline) ==== */
  .outline-chroma{position:static}
  .outline-chroma::after{content:"";display:none!important}

  /* ===== Buttons & pills ===== */
  .pill{padding:6px 12px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid #ffffff29;
        background:#ffffff14;color:#e5e7eb}
  .btn{padding:12px 18px;border-radius:14px;font-weight:900;border:1px solid #ffffff22;color:#0b1020}
  .btn-green{background:#22c55e;box-shadow:0 8px 26px rgba(34,197,94,.35)}
  .btn-blue{background:#3b82f6;box-shadow:0 8px 26px rgba(59,130,246,.35)}
  .btn-orange{background:#f59e0b;box-shadow:0 8px 26px rgba(245,158,11,.35)}
  .btn-purple{background:linear-gradient(90deg,#a78bfa,#f472b6);color:#19041a;box-shadow:0 8px 26px rgba(167,139,250,.35)}

  /* ===== Score chips (compact) ===== */
  .chip{padding:9px 12px;border-radius:14px;font-weight:800;display:inline-flex;align-items:center;
        gap:8px;border:1px solid #ffffff24;color:#eef2ff;font-size:14px}
  .chip i{font-style:normal}
  .chip.good{background:linear-gradient(135deg,#22c55e45,#10b98122);border-color:#22c55e72}
  .chip.warn{background:linear-gradient(135deg,#f59e0b45,#facc1522);border-color:#f59e0b72}
  .chip.bad{background:linear-gradient(135deg,#ef444445,#f8717122);border-color:#ef444472}

  /* ===== Score wheel (small) ===== */
  .mw{--v:0;--ring:#f59e0b;--p:0;width:220px;height:220px;position:relative}
  .mw-ring{position:absolute;inset:0;border-radius:50%;
           background:conic-gradient(var(--ring) calc(var(--v)*1%),#ffffff14 0);
           -webkit-mask:radial-gradient(circle 86px,transparent 80px,#000 80px);mask:radial-gradient(circle 86px,transparent 80px,#000 80px)}
  .mw-fill{position:absolute;inset:22px;border-radius:50%;overflow:hidden;background:#000}
  .mw-fill::after{content:"";position:absolute;left:0;right:0;height:100%;top:calc(100% - var(--p)*1%);
                  transition:top .9s ease;background:var(--fill,linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%));
                  -webkit-mask:radial-gradient(120px 18px at 50% 0,#0000 98%,#000 100%);mask:radial-gradient(120px 18px at 50% 0,#0000 98%,#000 100%)}
  .mw.good{--ring:#22c55e;--fill:linear-gradient(to top,#16a34a 0%,#22c55e 60%,#86efac 100%)}
  .mw.warn{--ring:#f59e0b;--fill:linear-gradient(to top,#f59e0b 0%,#fbbf24 60%,#fde68a 100%)}
  .mw.bad{--ring:#ef4444;--fill:linear-gradient(to top,#ef4444 0%,#f87171 60%,#fecaca 100%)}
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:42px;font-weight:900;color:#fff;text-shadow:0 6px 22px rgba(0,0,0,.45)}

  /* ===== Water bar ===== */
  .waterbox{position:relative;height:18px;border-radius:9999px;overflow:hidden;border:1px solid #ffffff22;background:#0b0b0b}
  .waterbox .fill{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .waterbox.good .fill{background:linear-gradient(90deg,#16a34a,#22c55e,#86efac)}
  .waterbox.warn .fill{background:linear-gradient(90deg,#f59e0b,#fbbf24,#fde68a)}
  .waterbox.bad .fill{background:linear-gradient(90deg,#ef4444,#f87171,#fecaca)}
  .waterbox .label{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e5e7eb;font-size:11px}

  /* ===== Checklist rows ===== */
  .check{display:flex;align-items:center;justify-content:space-between;border-radius:14px;padding:12px 14px;border:1px solid #ffffff1a;background:#0F1A29}
  .score-pill{padding:4px 8px;border-radius:10px;font-weight:800;background:#ffffff14;border:1px solid #ffffff22;color:#e5e7eb}
  .score-pill--green{background:#10b9812e;border-color:#10b98166;color:#bbf7d0}
  .score-pill--orange{background:#f59e0b2e;border-color:#f59e0b66;color:#fde68a}
  .score-pill--red{background:#ef44442e;border-color:#ef444466;color:#fecaca}
  .improve-btn{padding:6px 10px;border-radius:10px;color:#0b1020;font-weight:800;border:1px solid transparent;transition:transform .08s ease}
  .improve-btn:active{transform:translateY(1px)}
  .fill-green {background:linear-gradient(135deg,#16a34a,#22c55e,#86efac);color:#05240f}
  .fill-orange{background:linear-gradient(135deg,#f59e0b,#fbbf24,#fde68a);color:#3a2400}
  .fill-red   {background:linear-gradient(135deg,#ef4444,#f87171,#fecaca);color:#2f0606}
  .outline-green{border-color:#22c55edd!important;box-shadow:0 0 0 2px #22c55e8c inset,0 0 16px #22c55e55}
  .outline-orange{border-color:#f59e0bdd!important;box-shadow:0 0 0 2px #f59e0b8c inset,0 0 16px #f59e0b55}
  .outline-red{border-color:#ef4444dd!important;box-shadow:0 0 0 2px #ef44448c inset,0 0 16px #ef444455}

  /* ===== Inputs row ===== */
  .url-row{display:flex;align-items:center;gap:10px;border:1px solid #ffffff24;background:#0b0b0b;border-radius:14px;padding:10px 12px}
  .url-row input{background:transparent;border:none;outline:none;color:#e5e7eb;width:100%}
  .url-row .paste{padding:6px 10px;border-radius:10px;border:1px solid #ffffff26;background:#ffffff10}

  /* ===== Modal ===== */
  dialog{z-index:100}
  dialog[open]{display:block}
  dialog::backdrop{background:rgba(0,0,0,.6)}
  #improveModal .card{background:#0D0E1E;border:1px solid #1b2640}
  #improveModal .card .card{background:#111E2F;border-color:#ffffff1c}
</style>
@endpush

@section('content')
@php
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Storage;
  $u = Auth::user();
  $cand = $u?->profile_photo_url ?? $u?->avatar_url ?? $u?->avatar ?? $u?->photo ?? null;
  if ($cand && str_starts_with($cand,'http')) { $avatarUrl = $cand; }
  elseif ($cand) { try { $avatarUrl = Storage::url($cand); } catch (\Throwable $e) { $avatarUrl = $cand; } }
  else { $avatarUrl = null; }
@endphp

<section class="maxw px-4 pb-10">

  {{-- Heading --}}
  <div class="title-wrap outline-chroma" style="padding:10px 16px;border-radius:16px;">
    <div class="king">👑</div>
    <div>
      <div class="t-grad" style="font-size:28px;line-height:1.1;">Semantic SEO Master Analyzer</div>
      <div class="byline">By <span class="shoail">Shoail Kahoker</span></div>
    </div>
  </div>

  {{-- Legend --}}
  <div class="legend">
    <span class="badge g">Green ≥ 80</span>
    <span class="badge o">Orange 60–79</span>
    <span class="badge r">Red &lt; 60</span>
  </div>

  {{-- Wheel + chips --}}
  <div class="grid" style="display:grid;grid-template-columns:280px 1fr;gap:18px;align-items:center;margin-top:12px">
    <div class="outline-chroma" style="display:grid;place-items:center;border-radius:20px;padding:8px;background:#05050a;border:1px solid #ffffff12">
      <div class="mw warn" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-fill" id="mwFill" style="--p:0"></div>
        <div class="mw-center" id="mwNum">0%</div>
      </div>
    </div>
    <div class="space-y-2">
      <div class="flex flex-wrap gap-8" style="display:flex;flex-wrap:wrap;gap:10px">
        <span id="chipOverall" class="chip warn outline-chroma"><i>🟠</i><span>Overall: 0 /100</span></span>
        <span id="chipContent" class="chip warn outline-chroma"><i>🟠</i><span>Content: —</span></span>
        <span id="chipWriter"  class="chip outline-chroma"><i>🟠</i><span>Writer: —</span></span>
        <span id="chipHuman"   class="chip outline-chroma"><i>🟠</i><span>Human-like: — %</span></span>
        <span id="chipAI"      class="chip outline-chroma"><i>🟠</i><span>AI-like: — %</span></span>
      </div>
      <div id="overallBar" class="waterbox warn outline-chroma">
        <div class="fill" id="overallFill" style="width:0%"></div>
        <div class="label"><span id="overallPct">0%</span></div>
      </div>
      <p class="text-xs" style="color:#9aa5b1;font-size:12px;margin:0">Wheel + water bars fill with your scores (green ≥80, orange 60–79, red &lt;60).</p>
    </div>
  </div>

  {{-- Analyzer toolbar --}}
  <div class="analyze-wrap outline-chroma" style="margin-top:16px;padding:14px">
    <div class="url-row outline-chroma">
      <span style="opacity:.75">🌐</span>
      <input id="urlInput" name="url" type="url" placeholder="https://example.com/page" />
      <button id="pasteBtn" type="button" class="paste">Paste</button>
    </div>

    <div class="flex items-center gap-3" style="display:flex;align-items:center;gap:10px;margin-top:10px">
      <label style="display:flex;align-items:center;gap:8px;font-size:14px">
        <input id="autoCheck" type="checkbox" class="accent-emerald-400" checked/> Auto-apply checkmarks (≥ 80)
      </label>
      <div style="flex:1"></div>
      <input id="importFile" type="file" accept="application/json" class="hidden" style="display:none"/>
      <button id="importBtn" type="button" class="btn btn-purple outline-chroma">⇪ Import</button>
      <button id="analyzeBtn" type="button" class="btn btn-green outline-chroma">🔍 Analyze</button>
      <button id="printBtn"   type="button" class="btn btn-blue outline-chroma">🖨️ Print</button>
      <button id="resetBtn"   type="button" class="btn btn-orange outline-chroma">↻ Reset</button>
      <button id="exportBtn"  type="button" class="btn btn-purple outline-chroma">⬇︎ Export</button>
    </div>

    <div id="statusChips" class="flex flex-wrap gap-2 mt-2" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px">
      <div class="chip outline-chroma"><span class="t-grad">HTTP:</span>&nbsp;<span id="chipHttp">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">Title:</span>&nbsp;<span id="chipTitle">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">Meta desc:</span>&nbsp;<span id="chipMeta">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">Canonical:</span>&nbsp;<span id="chipCanon">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">Robots:</span>&nbsp;<span id="chipRobots">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">Viewport:</span>&nbsp;<span id="chipViewport">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">H1/H2/H3:</span>&nbsp;<span id="chipH">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">Internal links:</span>&nbsp;<span id="chipInt">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">Schema:</span>&nbsp;<span id="chipSchema">—</span></div>
      <div class="chip outline-chroma"><span class="t-grad">Auto-checked:</span>&nbsp;<span id="chipAuto">0</span></div>
    </div>
  </div>

  {{-- Quick Stats --}}
  <div class="card outline-chroma" style="margin-top:18px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 10px">Quick Stats</h3>
    <div class="grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px">
      <div class="card outline-chroma">
        <div style="font-size:12px;color:#b6c2cf">Readability (Flesch)</div>
        <div id="statFlesch" style="font-size:24px;font-weight:800">—</div>
        <div id="statGrade" style="font-size:12px;color:#94a3b8">—</div>
      </div>
      <div class="card outline-chroma">
        <div style="font-size:12px;color:#b6c2cf">Links (int / ext)</div>
        <div style="font-size:24px;font-weight:800"><span id="statInt">0</span> / <span id="statExt">0</span></div>
      </div>
      <div class="card outline-chroma">
        <div style="font-size:12px;color:#b6c2cf">Text/HTML Ratio</div>
        <div id="statRatio" style="font-size:24px;font-weight:800">—</div>
      </div>
    </div>
  </div>

  {{-- Content Structure --}}
  <div class="card outline-chroma" style="margin-top:18px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 10px">Content Structure</h3>
    <div class="grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px">
      <div class="card outline-chroma">
        <div style="font-size:12px;color:#b6c2cf">Title</div>
        <div id="titleVal" style="font-weight:600">—</div>
        <div style="font-size:12px;color:#b6c2cf;margin-top:12px">Meta Description</div>
        <div id="metaVal" style="color:#e5e7eb">—</div>
      </div>
      <div class="card outline-chroma">
        <div style="font-size:12px;color:#b6c2cf;margin-bottom:6px">Heading Map</div>
        <div id="headingMap" class="text-sm space-y-2"></div>
      </div>
    </div>
  </div>

  {{-- Recommendations --}}
  <div class="card outline-chroma" style="margin-top:18px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 10px">Recommendations</h3>
    <div id="recs" class="grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px"></div>
  </div>

  {{-- Semantic SEO Ground --}}
  <div class="ground-slab outline-chroma">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
      <div class="king">🧭</div>
      <div>
        <div class="t-grad" style="font-weight:900;font-size:20px">Semantic SEO Ground</div>
        <div style="font-size:13px;color:#b6c2cf">Actionable checklists for structure, quality, UX & entities</div>
      </div>
    </div>
    <div id="cats" class="grid" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px"></div>
  </div>

  {{-- Improve Modal --}}
  <dialog id="improveModal" class="rounded-2xl p-0 w-[min(680px,95vw)]" style="border:none;border-radius:16px">
    <div class="card outline-chroma">
      <div style="display:flex;align-items:start;justify-content:space-between;gap:10px">
        <h4 id="improveTitle" class="t-grad" style="font-weight:900;margin:0">Improve</h4>
        <form method="dialog"><button class="pill">Close</button></form>
      </div>
      <div class="grid" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:10px">
        <div class="card outline-chroma"><div style="font-size:12px;color:#94a3b8">Category</div><div id="improveCategory" style="font-weight:700">—</div></div>
        <div class="card outline-chroma">
          <div style="font-size:12px;color:#94a3b8">Score</div>
          <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
            <span id="improveScore" class="score-pill">—</span>
            <span id="improveBand" class="pill">—</span>
          </div>
        </div>
        <a id="improveSearch" target="_blank" class="card outline-chroma" style="text-align:center;display:flex;align-items:center;justify-content:center;background:linear-gradient(90deg,#f472b626,#22d3ee26);border:1px solid #ffffff22;text-decoration:none">
          <span style="font-size:13px;color:#e5e7eb">Search guidance</span>
        </a>
      </div>
      <div style="margin-top:12px">
        <div style="font-size:12px;color:#94a3b8">Why this matters</div>
        <p id="improveWhy" style="font-size:14px;color:#e5e7eb;margin-top:6px">—</p>
      </div>
      <div style="margin-top:12px">
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

  /* ========= Knowledge base for Improve modal ========= */
  const slug = s => String(s||'').toLowerCase().replace(/[^\p{Letter}\p{Number} ]+/gu,' ').replace(/\s+/g,' ').trim();
  const SOLUTIONS = {
    'define search intent primary topic': {why:'Clarity on search intent ensures your page satisfies what users actually want (informational, transactional, navigational).',tips:['State the primary topic in the first 1–2 sentences.','Answer the core question within the first screenful.','Add a concise TL;DR near the top.'],link:'https://ahrefs.com/blog/search-intent/'},
    'map target related keywords synonyms paa': {why:'Covering closely related terms signals topical completeness to search engines.',tips:['Collect variants from “People Also Ask” and Related searches.','Use synonyms naturally in headings and copy.','Create a small FAQ for 3–5 PAA questions.'],link:'https://developers.google.com/search/docs/fundamentals/seo-starter-guide'},
    'h1 includes primary topic naturally': {why:'A single, descriptive H1 helps users and crawlers understand the page focus.',tips:['Use exactly one H1.','Put the primary topic (or close variant) in the H1.','Keep H1 under ~70 characters.'],link:'https://web.dev/learn/html/semantics/#headings'},
    'integrate faqs questions with answers': {why:'FAQs capture long-tail queries and can win rich results (FAQPage schema).',tips:['Add 3–6 concise Q&A.','Keep answers ~40–60 words.','Add FAQPage JSON-LD.'],link:'https://developers.google.com/search/docs/appearance/structured-data/faqpage'},
    'readable nlp friendly language': {why:'Clear, simple writing boosts user engagement.',tips:['Aim for Grade 7–9.','Prefer common words; use active voice.','Break text with headings and lists.'],link:'https://hemingwayapp.com/'},
    'title tag 50 60 chars primary keyword': {why:'Well-sized titles boost CTR and avoid truncation.',tips:['Keep ≈50–60 chars; include the main term early.','Avoid ALL CAPS & stuffing.','Make it benefit-oriented.'],link:'https://moz.com/learn/seo/title-tag'},
    'meta description 140 160 chars cta': {why:'Compelling descriptions improve SERP click-through.',tips:['Write ≈140–160 chars + soft CTA.','Echo the user’s intent & value.','Avoid duplicating the title.'],link:'https://moz.com/learn/seo/meta-description'},
    'canonical tag set correctly': {why:'Canonicalization consolidates signals and prevents duplicates.',tips:['Point canonical to the preferred URL.','Avoid canonicals on noindex pages.','Ensure robots don’t block the canonical.'],link:'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls'},
    'indexable listed in xml sitemap': {why:'Indexable pages + sitemap coverage help discovery.',tips:['Return HTTP 200; not noindex; not blocked.','Include the URL in XML sitemap.','Resubmit in Search Console after updates.'],link:'https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview'},
    'logical h2 h3 headings topic clusters': {why:'A clear heading hierarchy improves scanability and parsing.',tips:['H2 for main sections, H3 for sub-points.','Each H2 introduces a distinct sub-topic.','Cluster related content and interlink hub ↔ spokes.'],link:'https://web.dev/learn/html/semantics/#headings'},
    'internal links to hub related pages': {why:'Internal links distribute authority and guide users deeper.',tips:['Add 3–5 contextual links to hub/pillar pages.','Use descriptive anchors.','Link back from hub pages to this page.'],link:'https://ahrefs.com/blog/internal-links-seo/'},
    'clean descriptive url slug': {why:'Readable slugs improve CTR and reduce ambiguity.',tips:['Use short hyphenated words.','Avoid dates/IDs unless required.','Keep stable; use 301s for changes.'],link:'https://developers.google.com/search/docs/fundamentals/creating-helpful-content#url-structure'},
    'breadcrumbs enabled schema': {why:'Breadcrumbs give users context and can show rich links.',tips:['Add breadcrumb UI and JSON-LD.','Reflect site structure.','Each crumb should resolve to a page.'],link:'https://developers.google.com/search/docs/appearance/structured-data/breadcrumb'},
    'e e a t signals author date expertise': {why:'E-E-A-T helps users and algorithms assess credibility.',tips:['Show author, credentials, last updated.','Link to an author bio with expertise.','Cite primary sources & standards.'],link:'https://developers.google.com/search/blog/2022/08/helpful-content-update'},
    'unique value vs top competitors': {why:'Differentiation raises usefulness and reduces bounces.',tips:['Add fresh data or tooling.','Summarize pros/cons or comparisons.','Use visuals (tables/screenshots).'],link:'https://backlinko.com/seo-techniques'},
    'facts citations up to date': {why:'Accuracy and freshness build trust.',tips:['Verify claims; cite authorities.','Prefer standards/peer-reviewed/gov.','Update old stats with year.'],link:'https://developers.google.com/search/docs/fundamentals/creating-helpful-content'},
    'helpful media images video captions': {why:'Captioned media improves comprehension & accessibility.',tips:['Add alt text + captions.','Compress and lazy-load.','Provide transcripts for videos.'],link:'https://web.dev/fast/#images'},
    'mobile friendly responsive layout': {why:'Mobile-first indexing makes handheld UX critical.',tips:['Test at 360–414px widths.','Tap targets ≥44px; readable fonts.','Reserve space to reduce CLS.'],link:'https://pagespeed.web.dev/'},
    'optimized speed compression lazy load': {why:'Performance impacts engagement & conversion.',tips:['Enable Brotli/Gzip; HTTP/2 or HTTP/3.','Use AVIF/WebP; responsive images.','Defer non-critical JS; preconnect/preload.'],link:'https://web.dev/fast/'},
    'core web vitals passing lcp inp cls': {why:'Passing CWV correlates with better UX and rankings.',tips:['LCP: optimize hero image; inline critical CSS.','INP: reduce JS work; split long tasks.','CLS: set width/height for media.'],link:'https://web.dev/vitals/'},
    'clear ctas next steps': {why:'Obvious next actions reduce pogo-sticking.',tips:['Primary CTA above the fold.','Verb-first labels.','Soft CTA at section ends.'],link:'https://www.nngroup.com/articles/call-to-action-buttons/'},
    'primary entity clearly defined': {why:'Disambiguation helps knowledge-graph understanding.',tips:['Introduce unique attributes early.','Link to authoritative entity page.','Match schema type to the entity.'],link:'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data'},
    'related entities covered with context': {why:'Related entities signal breadth & relationships.',tips:['Add short sub-sections for key entities.','Link to deeper pages.','Visualize relationships briefly.'],link:'https://schema.org/'},
    'valid schema markup article faq product': {why:'Valid JSON-LD unlocks rich results & semantics.',tips:['Pick one primary type.','Validate & fix warnings.','Keep schema in sync with UI.'],link:'https://search.google.com/test/rich-results'},
    'sameas organization details present': {why:'sameAs ties your brand to external profiles.',tips:['Add Organization schema with logo/url/sameAs.','Include contact/social/legal links.','Match footer details to schema.'],link:'https://developers.google.com/search/docs/appearance/structured-data/logo'}
  };

  const mw=$('#mw'), mwRing=$('#mwRing'), mwFill=$('#mwFill'), mwNum=$('#mwNum');
  const overallBar=$('#overallBar'), overallFill=$('#overallFill'), overallPct=$('#overallPct');
  const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'), chipHuman=$('#chipHuman'), chipAI=$('#chipAI');

  const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'), pasteBtn=$('#pasteBtn'),
        importBtn=$('#importBtn'), importFile=$('#importFile'), printBtn=$('#printBtn'),
        resetBtn=$('#resetBtn'), exportBtn=$('#exportBtn');

  const statF=$('#statFlesch'), statG=$('#statGrade'), statInt=$('#statInt'), statExt=$('#statExt'), statRatio=$('#statRatio');
  const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap'), recsEl=$('#recs'), catsEl=$('#cats');

  const chipHttp=$('#chipHttp'), chipTitle=$('#chipTitle'), chipMeta=$('#chipMeta'),
        chipCanon=$('#chipCanon'), chipRobots=$('#chipRobots'), chipViewport=$('#chipViewport'),
        chipH=$('#chipH'), chipIntChip=$('#chipInt'), chipSchema=$('#chipSchema'), chipAuto=$('#chipAuto');

  const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
        mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
        mTips=$('#improveTips'), mLink=$('#improveSearch');

  const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
  const bandIcon=s=>s>=80?'✅':(s>=60?'🟧':'🔴');
  const pickSolution = (label) => {
    const L = String(label||'').toLowerCase().replace(/[^\p{Letter}\p{Number} ]+/gu,' ').replace(/\s+/g,' ').trim();
    for (const k of Object.keys(SOLUTIONS)) if (L.includes(k)) return SOLUTIONS[k];
    return {why:'This checklist improves topical authority, UX, and eligibility for rich results.',
            tips:['Aim for a score ≥80 (green).','Make one change at a time and re-run the analyzer.'],
            link:'https://developers.google.com/search/docs'};
  };
  function setChip(el,label,value,score){ if(!el)return; el.classList.remove('good','warn','bad'); const b=bandName(score); el.classList.add(b); el.innerHTML=`<i>${bandIcon(score)}</i><span>${label}: ${value}</span>`; }
  function setRunning(isOn){ analyzeBtn.disabled=isOn; analyzeBtn.style.opacity=isOn?.6:1; analyzeBtn.textContent=isOn?'Analyzing…':'🔍 Analyze'; }

  async function callAnalyzer(url){
    const headers={'Accept':'application/json','Content-Type':'application/json'};
    let res=await fetch('/api/semantic-analyze',{method:'POST',headers,body:JSON.stringify({url,target_keyword:''})});
    if(res.ok)return res.json();
    if([404,405,419].includes(res.status)){
      res=await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url,target_keyword:''})});
      if(res.ok)return res.json();
    }
    let msg=`HTTP ${res.status}`;try{const j=await res.json();if(j?.error)msg+=' – '+j.error}catch{}throw new Error(msg);
  }

  analyzeBtn.addEventListener('click',async (e)=>{
    e.preventDefault();
    const url=(urlInput.value||'').trim(); if(!url){alert('Please enter a URL.');return;}
    try{
      setRunning(true);
      mwRing.style.setProperty('--v',0); mwFill.style.setProperty('--p',0); mwNum.textContent='0%';
      overallFill.style.width='0%'; overallPct.textContent='0%';

      const data=await callAnalyzer(url);
      if(!data||data.error)throw new Error(data?.error||'Unknown error');
      window.__lastData={...data,url};

      const score=Math.max(0,Math.min(100,Number(data.overall_score||0)));
      const b=bandName(score);
      mw.classList.remove('good','warn','bad'); mw.classList.add(b);
      overallBar.classList.remove('good','warn','bad'); overallBar.classList.add(b);
      mwRing.style.setProperty('--v',score); mwFill.style.setProperty('--p',score);
      mwNum.textContent=score+'%'; overallFill.style.width=score+'%'; overallPct.textContent=score+'%';
      setChip(chipOverall,'Overall',`${score} /100`,score);

      const cmap={}; (data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
      const contentScore=Math.round(([cmap['Content & Keywords'],cmap['Content Quality']].filter(v=>typeof v==='number').reduce((a,b)=>a+b,0))/2||0);
      setChip(chipContent,'Content',`${contentScore} /100`,contentScore);

      const r=data.readability||{};
      const human=Math.max(0,Math.min(100,Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3)));
      const ai=Math.max(0,Math.min(100,100-human));
      setChip(chipWriter,'Writer',human>=60?'Likely Human':'Possibly AI',human);
      setChip(chipHuman,'Human-like',`${human} %`,human);
      setChip(chipAI,'AI-like',`${ai} %`,ai);

      statF.textContent=r.flesch??'—'; statG.textContent='Grade '+(r.grade??'—');
      statInt.textContent=data.quick_stats?.internal_links??0;
      statExt.textContent=data.quick_stats?.external_links??0;
      statRatio.textContent=(data.quick_stats?.text_to_html_ratio??0)+'%';

      titleVal.textContent=data.content_structure?.title||'—';
      metaVal.textContent=data.content_structure?.meta_description||'—';
      const hs=data.content_structure?.headings||{}; headingMap.innerHTML='';
      Object.entries(hs).forEach(([lvl,arr])=>{
        if(!arr||!arr.length)return;
        const box=document.createElement('div'); box.className='card outline-chroma';
        box.innerHTML=`<div style="font-size:12px;color:#b6c2cf;margin-bottom:4px">${lvl}</div>`+arr.map(t=>`<div>• ${t}</div>`).join('');
        headingMap.appendChild(box);
      });

      recsEl.innerHTML='';
      (data.recommendations||[]).forEach(rec=>{
        const d=document.createElement('div'); d.className='card outline-chroma';
        d.innerHTML=`<span class="pill" style="margin-right:8px">${rec.severity}</span>${rec.text}`;
        recsEl.appendChild(d);
      });

      catsEl.innerHTML='';
      (data.categories||[]).forEach(cat=>{
        const total=(cat.checks||[]).length;
        const passed=(cat.checks||[]).filter(ch=>(ch.score||0)>=80).length;
        const pct=Math.round((passed/Math.max(1,total))*100);
        const card=document.createElement('div'); card.className='cat-card outline-chroma';
        card.innerHTML=`<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
            <div class="t-grad" style="font-weight:900">${cat.name}</div>
            <div class="pill">${passed} / ${total}</div></div>
            <div class="waterbox warn" style="margin-bottom:10px"><div class="fill" style="width:${pct}%"></div></div>
            <div class="space-y-2" id="list"></div>`;
        const list=card.querySelector('#list');

        (cat.checks||[]).forEach(ch=>{
          const s=ch.score||0; const color=s>=80?'#10b981':s>=60?'#f59e0b':'#ef4444';
          const row=document.createElement('div'); row.className='check outline-chroma';
          row.innerHTML=`<div style="display:flex;align-items:center;gap:10px">
                           <span class="w-3 h-3 rounded-full" style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:${color}"></span>
                           <div style="font-weight:600">${ch.label}</div>
                         </div>
                         <div style="display:flex;align-items:center;gap:8px">
                           <span class="score-pill ${s>=80?'score-pill--green':s>=60?'score-pill--orange':'score-pill--red'}">${s||'—'}</span>
                           <button class="improve-btn ${(s>=80?'fill-green':s>=60?'fill-orange':'fill-red')} ${(s>=80?'outline-green':s>=60?'outline-orange':'outline-red')}" type="button">Improve</button>
                         </div>`;
          row.querySelector('.improve-btn').addEventListener('click',()=>{
            const sol=pickSolution(ch.label);
            mTitle.textContent=ch.label; mCat.textContent=cat.name; mScore.textContent=s||'—';
            mBand.textContent=s>=80?'Good (≥80)':s>=60?'Needs work (60–79)':'Low (<60)';
            mBand.className='pill '+(s>=80?'score-pill--green':s>=60?'score-pill--orange':'score-pill--red');
            mWhy.textContent=sol.why; mTips.innerHTML='';
            (sol.tips||[]).forEach(t=>{const li=document.createElement('li');li.textContent=t;mTips.appendChild(li);});
            mLink.href=sol.link || ('https://www.google.com/search?q='+encodeURIComponent(ch.label+' SEO best practices'));
            if(typeof modal.showModal==='function') modal.showModal(); else modal.setAttribute('open','');
          });
          list.appendChild(row);
        });
        catsEl.appendChild(card);
      });

      chipTitle.textContent=(data.content_structure?.title||'').length||0;
      chipMeta.textContent=(data.content_structure?.meta_description||'').length||0;
      try{chipCanon.textContent=new URL(url).origin}catch{chipCanon.textContent='—'}
      chipRobots.textContent='—'; chipViewport.textContent='—';
      const hs2=data.content_structure?.headings||{};
      chipH.textContent=`H1:${(hs2.H1||[]).length} • H2:${(hs2.H2||[]).length} • H3:${(hs2.H3||[]).length}`;
      chipIntChip.textContent=data.quick_stats?.internal_links??0;
      chipSchema.textContent=data.schema_count??0;
      chipAuto.textContent=(data.categories||[]).flatMap(c=>c.checks||[]).filter(x=>(x.score||0)>=80).length;
      chipHttp.textContent='200';
    }catch(err){
      console.error(err); alert('Analyze failed: '+err.message);
    }finally{ setRunning(false); }
  });

  $('#improveModal')?.addEventListener('click',e=>{
    const dlg=e.currentTarget, r=dlg.getBoundingClientRect();
    const inside=(e.clientX>=r.left&&e.clientX<=r.right&&e.clientY>=r.top&&e.clientY<=r.bottom);
    if(!inside){ if(typeof dlg.close==='function')dlg.close(); else dlg.removeAttribute('open'); }
  });

  pasteBtn.addEventListener('click',async e=>{e.preventDefault();try{const t=await navigator.clipboard.readText();if(t)urlInput.value=t.trim()}catch{}})
  importBtn.addEventListener('click',()=>importFile.click());
  importFile.addEventListener('change',e=>{const f=e.target.files?.[0];if(!f)return;const r=new FileReader();r.onload=()=>{try{const j=JSON.parse(String(r.result||'{}'));if(j.url)urlInput.value=j.url;alert('Imported JSON. Click Analyze to run.')}catch{alert('Invalid JSON file.')}};r.readAsText(f)})
  printBtn.addEventListener('click',()=>window.print());
  resetBtn.addEventListener('click',()=>location.reload());
  exportBtn.addEventListener('click',()=>{if(!window.__lastData){alert('Run an analysis first.');return;}const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='semantic-report.json';a.click();URL.revokeObjectURL(a.href)})
});
</script>
@endpush
