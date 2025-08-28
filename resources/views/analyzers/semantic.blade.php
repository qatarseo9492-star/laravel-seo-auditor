@extends('layouts.app', ['title' => 'Semantic SEO Master Analyzer 2.0'])

@section('content')
<style>
  /* ===== Background: gradients + tech lines ===== */
  body {
    background:
      radial-gradient(1200px 700px at -10% -10%, rgba(255,102,196,.32), transparent 60%),
      radial-gradient(1000px 600px at 110% 0%, rgba(96,165,250,.30), transparent 60%),
      radial-gradient(1400px 900px at 50% 120%, rgba(6,214,160,.22), transparent 65%),
      linear-gradient(120deg, #0b1020, #0b0f1a 50%, #0a0c18);
  }
  #bgLines {
    position: fixed; inset: 0; z-index: 0; pointer-events: none;
  }
  .glass {
    background: linear-gradient(180deg, rgba(15,23,42,.65), rgba(2,6,23,.55));
    border: 1px solid rgba(255,255,255,.08);
    backdrop-filter: blur(10px);
  }
  .card { background: #0f172a; border:1px solid rgba(255,255,255,.08); }
  .shadow-soft { box-shadow: 0 16px 60px rgba(0,0,0,.28); }
  .chip { font-size:.7rem; padding:.2rem .5rem; border-radius:.5rem; border:1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.06); }

  /* Buttons (no @apply so it works without build step) */
  .btn { padding:.5rem 1rem; border-radius:.75rem; font-weight:600; transition:.2s ease; border:1px solid transparent; }
  .btn-brand { color:#fff; background-image:linear-gradient(135deg,#ff66c4,#60a5fa); }
  .btn-brand:hover { filter: brightness(1.05); }
  .btn-ghost { color:#e5e7eb; background: rgba(255,255,255,.06); border-color: rgba(255,255,255,.12); }
  .btn-ghost:hover { background: rgba(255,255,255,.1); }

  .badge-good, .badge-warn, .badge-bad {
    display:inline-flex; align-items:center; gap:.5rem; padding:.25rem .75rem;
    border-radius:9999px; font-size:.75rem; font-weight:600; border:1px solid;
  }
  .badge-good { background: rgba(16,185,129,.15); color:#34d399; border-color:rgba(16,185,129,.35); }
  .badge-warn { background: rgba(245,158,11,.15); color:#f59e0b; border-color:rgba(245,158,11,.35); }
  .badge-bad  { background: rgba(239,68,68,.15);  color:#ef4444; border-color:rgba(239,68,68,.35); }

  /* Modal */
  .modal-backdrop { position:fixed; inset:0; background:rgba(2,6,23,.6); backdrop-filter: blur(6px); display:none; z-index:50; }
  .modal { max-width: 36rem; width: 94vw; }
  .modal.show { display:flex; align-items:center; justify-content:center; }
</style>

<canvas id="bgLines"></canvas>

<section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="flex items-start justify-between gap-6">
    <div>
      <span class="chip">Semantic SEO • Analyzer 2.0</span>
      <h1 class="text-3xl md:text-4xl font-extrabold leading-tight mt-3 text-white">
        Audit pages with a <span class="bg-gradient-to-r from-[#ff66c4] via-[#ffd166] to-[#06d6a0] bg-clip-text text-transparent">colorful score wheel</span> + actionable checklists
      </h1>
      <p class="text-slate-300 mt-3 max-w-2xl">
        Paste a URL (and optional primary keyword). We’ll fetch the page, analyze structure, entities, links, schema, and more—then tell you exactly how to improve.
      </p>
    </div>
  </div>

  <!-- ===== Pre-Analysis (helper, optional) ===== -->
  <div class="mt-8 grid lg:grid-cols-3 gap-6">
    <div class="p-6 rounded-2xl glass shadow-soft">
      <h3 class="font-semibold text-white">Pre-Analysis & Goal Definition</h3>
      <form id="goalForm" class="mt-4 space-y-3 text-sm">
        <div>
          <label class="block text-slate-300 mb-1">Primary Keyword</label>
          <input id="primaryKeyword" type="text" placeholder="e.g., best drip coffee maker"
                 class="w-full px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-slate-300 mb-1">Secondary Keywords (comma-separated)</label>
          <input id="secondaryKeywords" type="text" placeholder="maker with grinder, clean drip coffee..."
                 class="w-full px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
          <label class="block text-slate-300 mb-1">Search Intent</label>
          <select id="intent" class="w-full px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Select…</option>
            <option>Informational</option>
            <option>Commercial Investigation</option>
            <option>Navigational</option>
            <option>Transactional</option>
          </select>
        </div>
      </form>
      <p class="text-xs text-slate-400 mt-3">Tip: these help you (and your team) align the audit with ranking goals.</p>
    </div>

    <!-- ===== Input Card ===== -->
    <div class="p-6 rounded-2xl glass shadow-soft lg:col-span-2">
      <h3 class="font-semibold text-white">Analyze a URL</h3>
      <form id="semanticForm" class="mt-4 grid md:grid-cols-[1fr,230px,auto] gap-3">
        <input name="url" type="url" required placeholder="https://example.com/article"
               class="w-full px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <input name="target_keyword" id="targetKeywordInput" type="text" placeholder="Primary keyword (optional)"
               class="px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
        <button id="submitBtn" class="btn btn-brand" type="submit">
          <span class="inline-flex items-center gap-2">
            <svg id="spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/>
            </svg>
            <span id="btnText">Analyze</span>
          </span>
        </button>
      </form>

      <div id="errorBox" class="mt-4 hidden">
        <div class="rounded-xl border border-red-400/30 bg-red-500/10 text-red-200 px-4 py-3 text-sm">
          <strong class="font-semibold">Analysis failed:</strong>
          <span id="errorMsg">Unknown error</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ===== Score + Summary ===== -->
  <div id="semanticResult" class="mt-10 hidden">
    <div class="grid lg:grid-cols-3 gap-6 items-stretch">
      <!-- Score Wheel -->
      <div class="p-6 rounded-2xl glass shadow-soft flex flex-col items-center justify-center">
        <div class="relative">
          <svg id="scoreWheel" width="220" height="220" viewBox="0 0 220 220">
            <defs>
              <linearGradient id="gradMulti" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"   stop-color="#ff66c4"/>
                <stop offset="35%"  stop-color="#ffd166"/>
                <stop offset="65%"  stop-color="#06d6a0"/>
                <stop offset="100%" stop-color="#60a5fa"/>
              </linearGradient>
            </defs>
            <circle cx="110" cy="110" r="90" stroke="rgba(255,255,255,.15)" stroke-width="16" fill="none"/>
            <circle id="progressArc" cx="110" cy="110" r="90"
                    stroke="url(#gradMulti)" stroke-width="16" fill="none"
                    stroke-linecap="round" stroke-dasharray="565.48" stroke-dashoffset="565.48"
                    transform="rotate(-90 110 110)"/>
            <text id="scoreText" x="110" y="110" dominant-baseline="middle" text-anchor="middle"
                  font-size="44" font-weight="800" fill="#fff">—</text>
          </svg>
          <div id="badge" class="mt-4 text-center"></div>
        </div>
        <p class="mt-4 text-slate-300 text-sm text-center max-w-xs">
          Overall score from analyzer API. Improve checklist items to push this higher.
        </p>
      </div>

      <!-- Quick Stats -->
      <div class="p-6 rounded-2xl glass shadow-soft">
        <h3 class="font-semibold text-white">Quick Stats</h3>
        <div class="mt-4 grid sm:grid-cols-2 gap-3 text-sm">
          <div class="rounded-xl card p-4">
            <div class="text-slate-400 text-xs">Readability (Flesch)</div>
            <div id="readability" class="text-2xl font-semibold text-white mt-1">—</div>
          </div>
          <div class="rounded-xl card p-4">
            <div class="text-slate-400 text-xs">Text / HTML Ratio</div>
            <div id="ratioVal" class="text-2xl font-semibold text-white mt-1">—%</div>
          </div>
          <div class="rounded-xl card p-4">
            <div class="text-slate-400 text-xs">Internal Links</div>
            <div class="text-2xl font-semibold text-white mt-1"><span id="internal">0</span></div>
          </div>
          <div class="rounded-xl card p-4">
            <div class="text-slate-400 text-xs">External Links</div>
            <div class="text-2xl font-semibold text-white mt-1"><span id="external">0</span></div>
          </div>
        </div>
        <div class="mt-4 rounded-xl card p-4 text-sm">
            <div class="text-slate-400 text-xs">Meta</div>
            <p class="mt-1"><span class="text-slate-300">Title:</span> <span id="titleVal" class="text-white">—</span></p>
            <p class="mt-1"><span class="text-slate-300">Description:</span> <span id="metaVal" class="text-white/90">—</span></p>
        </div>
      </div>

      <!-- Heading Map -->
      <div class="p-6 rounded-2xl glass shadow-soft">
        <h3 class="font-semibold text-white">Heading Map</h3>
        <div id="headingMap" class="mt-3 text-sm space-y-2 text-slate-300"></div>
        <div class="mt-4 rounded-xl card p-4 text-sm">
          <div class="text-slate-400 text-xs">Images</div>
          <p class="mt-1"><span class="text-slate-300">Missing alt:</span> <span id="imgAlt" class="text-white">—</span></p>
        </div>
      </div>
    </div>

    <!-- ===== Checklist (auto-tick + “Improve” modal) ===== -->
    <div class="mt-10">
      <div class="flex items-center justify-between gap-3">
        <h3 class="text-xl font-bold text-white">Optimization Checklist</h3>
        <div class="text-sm text-slate-300">
          <span>Checklist Progress</span>
          <div class="h-2 w-48 bg-white/10 rounded-full overflow-hidden inline-block align-middle ml-3">
            <div id="checkProgress" class="h-2 bg-gradient-to-r from-[#06d6a0] via-[#ffd166] to-[#ff66c4]" style="width:0%"></div>
          </div>
          <span id="checkPoints" class="ml-2 text-white font-semibold">0/100</span>
        </div>
      </div>

      <div id="checklist" class="mt-4 grid md:grid-cols-2 xl:grid-cols-3 gap-4"></div>
    </div>

    <!-- ===== Recommendations (from API) ===== -->
    <div class="mt-10 grid lg:grid-cols-2 gap-6">
      <div class="p-6 rounded-2xl glass shadow-soft">
        <h3 class="font-semibold text-white">Analyzer Recommendations</h3>
        <ul id="recs" class="mt-3 space-y-2 text-sm text-slate-200"></ul>
      </div>
      <div class="p-6 rounded-2xl glass shadow-soft">
        <h3 class="font-semibold text-white">Anchor Text (Top 100)</h3>
        <div id="anchors" class="mt-3 text-sm text-slate-300 space-y-1 max-h-[280px] overflow-auto pr-2"></div>
      </div>
    </div>
  </div>
</section>

<!-- ===== Improve Modal ===== -->
<div id="modalBackdrop" class="modal-backdrop">
  <div class="modal glass rounded-2xl p-6 shadow-soft border border-white/10 text-slate-100">
    <div class="flex items-center justify-between">
      <h3 id="modalTitle" class="text-lg font-semibold">Improve</h3>
      <button id="modalClose" class="btn btn-ghost" style="padding:.25rem .5rem">✕</button>
    </div>
    <div id="modalBody" class="mt-3 text-sm space-y-2"></div>
    <div class="mt-5 text-right">
      <button id="modalOk" class="btn btn-brand">Got it</button>
    </div>
  </div>
</div>

<script>
  // ===== Small DOM helpers (FIXED: define $) =====
  const $  = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

  const elForm   = $('#semanticForm');
  const elWrap   = $('#semanticResult');
  const errBox   = $('#errorBox');
  const errMsg   = $('#errorMsg');
  const btn      = $('#submitBtn');
  const spinner  = $('#spinner');
  const btnText  = $('#btnText');
  const scoreText= $('#scoreText');
  const progressArc = $('#progressArc');
  const badge    = $('#badge');
  const checkBox = $('#checklist');
  const checkProgress = $('#checkProgress');
  const checkPoints = $('#checkPoints');

  const els = {
    readability: $('#readability'),
    ratio: $('#ratioVal'),
    internal: $('#internal'),
    external: $('#external'),
    title: $('#titleVal'),
    meta: $('#metaVal'),
    map: $('#headingMap'),
    imgAlt: $('#imgAlt'),
    recs: $('#recs'),
    anchors: $('#anchors'),
  };

  function setLoading(on) {
    btn.disabled = on;
    spinner.classList.toggle('hidden', !on);
    btnText.textContent = on ? 'Analyzing…' : 'Analyze';
  }
  function showError(msg) { errMsg.textContent = msg || 'Analysis failed'; errBox.classList.remove('hidden'); }
  function hideError() { errBox.classList.add('hidden'); }

  // ===== Score wheel anim =====
  const PERIM = 2 * Math.PI * 90; // r=90
  function setWheel(score) {
    const clamped = Math.max(0, Math.min(100, Number(score||0)));
    scoreText.textContent = clamped;
    const dash = PERIM * (1 - clamped/100);
    progressArc.setAttribute('stroke-dashoffset', dash.toFixed(2));
    if (clamped >= 80) {
      badge.innerHTML = `<span class="badge-good">🌟 Great Work — Well Optimized</span>`;
    } else if (clamped >= 60) {
      badge.innerHTML = `<span class="badge-warn">⚠️ Need to optimize content</span>`;
    } else {
      badge.innerHTML = `<span class="badge-bad">⛳ Need to optimize content</span>`;
    }
  }

  // ===== Checklist (100 pts total) =====
  const checklistModel = [
    { id:'title_len', label:'Title tag length (50–60 chars)', weight:8,
      compute: (d) => { const len = (d.content_structure?.title || '').trim().length; return (len>=50 && len<=60) ? 1 : 0; },
      improve: () => ['Aim for ~50–60 characters.','Lead with the primary keyword.','Use compelling phrasing (numbers, brackets, benefits).']
    },
    { id:'meta_len', label:'Meta description present (120–160 chars)', weight:6,
      compute: (d) => { const len = (d.content_structure?.meta_description || '').trim().length; return (len>=120 && len<=160) ? 1 : 0; },
      improve: () => ['Write ~150–160 chars and include primary + secondary keywords.','Treat it like ad copy with value prop + CTA.']
    },
    { id:'h1_single', label:'Single H1 present', weight:6,
      compute: (d) => { const h1 = d.content_structure?.headings?.h1 || []; return (Array.isArray(h1) && h1.length===1) ? 1 : 0; },
      improve: () => ['Ensure exactly one H1.','Keep it close (not identical) to the Title tag.','Include the primary topic naturally.']
    },
    { id:'headings_order', label:'Logical heading hierarchy (no skips)', weight:6,
      compute: (d) => d.content_structure?.skipped_levels === false ? 1 : 0,
      improve: () => ['Follow H1 → H2 → H3… (avoid H1 → H3 jumps).','Use H2 for main sections and H3 for sub-sections.']
    },
    { id:'keyword_title', label:'Primary keyword in Title', weight:6,
      compute: (d) => d.target_keyword?.in_title ? 1 : 0,
      improve: () => ['Place primary keyword near the front of the Title.','Keep wording natural and click-worthy.']
    },
    { id:'keyword_meta', label:'Primary keyword in Meta description', weight:4,
      compute: (d) => d.target_keyword?.in_meta ? 1 : 0,
      improve: () => ['Mention primary keyword once, naturally.','Add a benefit-oriented call to action.']
    },
    { id:'keyword_usage', label:'Keyword appears in content', weight:6,
      compute: (d) => (d.target_keyword?.occurrences||0) > 0 ? 1 : 0,
      improve: () => ['Use the primary keyword in the first paragraph.','Sprinkle related terms (synonyms, entities) throughout.']
    },
    { id:'readability', label:'Readability Flesch ≥ 50', weight:6,
      compute: (d) => (d.content_structure?.readability_flesch||0) >= 50 ? 1 : 0,
      improve: () => ['Shorter sentences and paragraphs.','Use plain, active language and bullets.']
    },
    { id:'text_ratio', label:'Text/HTML ratio ≥ 10%', weight:4,
      compute: (d) => (d.content_structure?.text_to_html_ratio||0) >= 10 ? 1 : 0,
      improve: () => ['Reduce excessive markup and inline scripts.','Add explanatory copy where thin.']
    },
    { id:'img_alts', label:'Images have alt text (no missing)', weight:6,
      compute: (d) => (d.technical_seo?.image_alt_missing||0) === 0 ? 1 : 0,
      improve: (d) => [`${d.technical_seo?.image_alt_missing||0} images missing alt.`,`Use concise, descriptive alt text; include entities only if relevant.`]
    },
    { id:'internal_links', label:'≥ 3 internal links', weight:6,
      compute: (d) => (d.technical_seo?.links?.internal||0) >= 3 ? 1 : 0,
      improve: () => ['Link to hub/cluster pages with descriptive anchors.','Add “next steps” links to deepen topical coverage.']
    },
    { id:'external_links', label:'≥ 1 authoritative external link', weight:4,
      compute: (d) => (d.technical_seo?.links?.external||0) >= 1 ? 1 : 0,
      improve: () => ['Cite reputable sources (standards, research, gov, .edu).','Use precise anchor text.']
    },
    { id:'schema', label:'Structured data present (JSON-LD / Microdata / RDFa)', weight:10,
      compute: (d) => (d.technical_seo?.structured_data?.json_ld || d.technical_seo?.structured_data?.microdata || d.technical_seo?.structured_data?.rdfa) ? 1 : 0,
      improve: () => ['Add appropriate schema (Article, FAQ, Product, Breadcrumb).','Validate with Google Rich Results Test.']
    },
    { id:'intent', label:'Search intent alignment', weight:10,
      compute: (d) => d.intent_coverage?.search_intent ? 1 : 0,
      improve: () => ['Match competitor formats (guide vs product vs comparison).','Cover PAA questions and add clear CTAs.']
    },
    { id:'coverage', label:'Semantic coverage / topic cluster addressed', weight:16,
      compute: (d) => {
        if (typeof d.intent_coverage?.semantic_coverage_score === 'number') {
          return d.intent_coverage.semantic_coverage_score >= 60 ? 1 : 0;
        }
        return (Array.isArray(d.semantic_core?.topic_cloud) && d.semantic_core.topic_cloud.length>0) ? 1 : 0;
      },
      improve: () => ['Add missing entities/subtopics vs top ranking pages.','Include FAQs, comparisons, examples to broaden coverage.']
    },
  ];

  function renderChecklist(data) {
    checkBox.innerHTML = '';
    let gained = 0;
    checklistModel.forEach(item => {
      const ok = !!item.compute(data, data?.target_keyword?.target || '');
      if (ok) gained += item.weight;

      const stateBadge = ok
        ? `<span class="badge-good">✓ ${item.weight} pts</span>`
        : `<span class="badge-warn">• 0/${item.weight} pts</span>`;

      const card = document.createElement('div');
      card.className = 'p-4 rounded-2xl glass border border-white/10';
      card.innerHTML = `
        <div class="flex items-start justify-between gap-3">
          <div>
            <div class="text-white font-medium">${item.label}</div>
            <div class="mt-1">${stateBadge}</div>
          </div>
          <button class="btn btn-ghost text-xs" data-improve="${item.id}">Improve</button>
        </div>
      `;
      checkBox.appendChild(card);
    });

    const pct = Math.round((gained/100)*100);
    checkProgress.style.width = pct + '%';
    checkPoints.textContent = `${gained}/100`;

    // Improve buttons
    $$('[data-improve]').forEach(b => b.addEventListener('click', () => openImprove(b.getAttribute('data-improve'), data)));
  }

  // ===== Improve modal =====
  const modalBackdrop = $('#modalBackdrop');
  const modalTitle = $('#modalTitle');
  const modalBody = $('#modalBody');
  $('#modalClose').addEventListener('click', closeImprove);
  $('#modalOk').addEventListener('click', closeImprove);
  modalBackdrop.addEventListener('click', (e)=>{ if(e.target===modalBackdrop) closeImprove(); });

  function openImprove(id, data) {
    const item = checklistModel.find(x=>x.id===id);
    if (!item) return;
    modalTitle.textContent = item.label;
    const tips = (typeof item.improve === 'function') ? item.improve(data) : (item.improve || []);
    modalBody.innerHTML = `
      <div class="text-slate-300">Suggestions to raise your score:</div>
      <ul class="mt-2 list-disc pl-5 space-y-1">
        ${(tips||[]).map(t=>`<li>${escapeHTML(t)}</li>`).join('')}
      </ul>
    `;
    modalBackdrop.classList.add('show');
  }
  function closeImprove(){ modalBackdrop.classList.remove('show'); }
  function escapeHTML(s){ return (s||'').replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }

  // ===== Submit handler (FIXED: now binds correctly) =====
  elForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError(); setLoading(true);

    // Fill target keyword from Pre-Analysis if empty
    const pk = ($('#primaryKeyword')?.value || '').trim();
    const tkInput = $('#targetKeywordInput');
    if (tkInput && !tkInput.value.trim() && pk) tkInput.value = pk;

    try {
      const fd = new FormData(elForm);
      const payload = {
        url: (fd.get('url') || '').trim(),
        target_keyword: (fd.get('target_keyword') || '').trim()
      };

      const res = await fetch('/api/semantic-analyze', {
        method: 'POST',
        headers: { 'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify(payload)
      });

      if (!res.ok) {
        let msg = 'HTTP ' + res.status + ' ' + res.statusText;
        try { const j = await res.json(); if (j?.error) msg = j.error; } catch(_){}
        showError(msg); setLoading(false); return;
      }

      const data = await res.json();
      if (!data?.ok) { showError(data?.error || 'Analysis failed'); setLoading(false); return; }

      // Show results
      elWrap.classList.remove('hidden');

      // Wheel + badge
      setWheel(data.overall_score);

      // Quick stats
      els.readability.textContent = data.content_structure?.readability_flesch ?? '—';
      els.ratio.textContent = data.content_structure?.text_to_html_ratio ?? '—';
      els.internal.textContent = data.technical_seo?.links?.internal ?? 0;
      els.external.textContent = data.technical_seo?.links?.external ?? 0;
      els.title.textContent = data.content_structure?.title || '—';
      els.meta.textContent  = data.content_structure?.meta_description || '—';
      els.imgAlt.textContent = `${data.technical_seo?.image_alt_missing ?? 0} / ${data.technical_seo?.image_count ?? 0}`;

      // Headings
      els.map.innerHTML = '';
      const headings = data.content_structure?.headings || {};
      Object.entries(headings).forEach(([level,arr])=>{
        if (!arr || !arr.length) return;
        const div = document.createElement('div');
        div.innerHTML = `<div class="text-xs uppercase text-slate-400">${level}</div>` +
                        arr.map(t=>`<div class="pl-3 text-slate-200">• ${escapeHTML(t)}</div>`).join('');
        els.map.appendChild(div);
      });

      // Recommendations
      els.recs.innerHTML = '';
      (data.recommendations || []).forEach(r=>{
        const sev = (r.severity||'').toLowerCase();
        const c =
          sev==='critical' ? 'badge-bad' :
          sev==='warning'  ? 'badge-warn' : 'chip';
        const li = document.createElement('li');
        li.innerHTML = `<span class="${c} mr-2">${escapeHTML(r.severity||'Info')}</span>${escapeHTML(r.text||'')}`;
        els.recs.appendChild(li);
      });

      // Anchors
      els.anchors.innerHTML = '';
      (data.technical_seo?.links?.anchors || []).slice(0,100).forEach(a=>{
        const p = document.createElement('p');
        p.textContent = `[${a.type||'link'}] ${a.text || '(no text)'} → ${a.href||''}`;
        els.anchors.appendChild(p);
      });

      // Checklist
      renderChecklist(data);

    } catch (err) {
      console.error(err);
      showError(err?.message || 'Network error');
    } finally {
      setLoading(false);
    }
  });

  // ===== Tech lines that dance with the mouse =====
  (() => {
    const c = document.getElementById('bgLines');
    if (!c) return;
    const ctx = c.getContext('2d');
    let dpr = Math.min(2, window.devicePixelRatio || 1);
    let w=0,h=0,t=0,mx=.5,my=.5;
    function size(){ w=c.clientWidth; h=c.clientHeight; c.width=w*dpr; c.height=h*dpr; ctx.setTransform(dpr,0,0,dpr,0,0); }
    function lerp(a,b,x){ return a+(b-a)*x; }
    function draw(){
      t+=0.016; ctx.clearRect(0,0,w,h);
      const lines=Math.max(14,Math.floor(h/50)); const sp=h/(lines-1); const amp=lerp(12,48,my); const freq=0.012; const speed=lerp(.6,1.4,mx);
      for(let i=0;i<lines;i++){
        const y0=i*sp;
        const g=ctx.createLinearGradient(0,y0-20,w,y0+20);
        g.addColorStop(0,'rgba(255,102,196,0.25)'); g.addColorStop(.5,'rgba(96,165,250,0.45)'); g.addColorStop(1,'rgba(6,214,160,0.25)');
        ctx.strokeStyle=g; ctx.lineWidth=2+Math.sin((i/lines)*Math.PI)*1.4; ctx.beginPath();
        for(let x=-30;x<=w+30;x+=70){
          const y=y0 + Math.sin((x+t*70*speed+i*8)*freq)*amp + Math.cos((x*freq*.8)+t*2)*(mx*10);
          if(x===-30) ctx.moveTo(x,y); else ctx.lineTo(x,y);
        } ctx.stroke();
      } requestAnimationFrame(draw);
    }
    window.addEventListener('resize', size, {passive:true});
    window.addEventListener('pointermove', e=>{ const r=c.getBoundingClientRect(); mx=(e.clientX-r.left)/r.width; my=(e.clientY-r.top)/r.height; }, {passive:true});
    size(); draw();
  })();
</script>
@endsection
