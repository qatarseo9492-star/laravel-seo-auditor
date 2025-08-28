@extends('layouts.app', ['title' => 'Semantic SEO Master Analyzer 2.0'])

@section('content')
<style>
  /* ===== Brand palette (Coolors) ===== */
  :root{
    --c1:#006466; --c2:#065a60; --c3:#0b525b; --c4:#144552; --c5:#1b3a4b;
    --c6:#212f45; --c7:#272640; --c8:#312244; --c9:#3e1f47; --c10:#4d194d;
  }

  /* Page background (static, no animation) */
  body{
    background:
      radial-gradient(1200px 800px at -10% -10%, rgba(0,100,102,.30), transparent 60%),
      radial-gradient(900px 600px at 110% 0%, rgba(49,34,68,.28), transparent 60%),
      linear-gradient(135deg, var(--c6), var(--c7) 35%, var(--c8));
  }

  .glass { background: linear-gradient(180deg, rgba(17,24,39,.55), rgba(9,12,24,.55)); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(8px); }
  .card  { background: rgba(18,22,40,.75); border:1px solid rgba(255,255,255,.08); }
  .shadow-soft { box-shadow: 0 16px 60px rgba(0,0,0,.28); }
  .kicker { letter-spacing:.14em; font-size:.7rem; text-transform:uppercase; color:#98c1d9; }

  /* Buttons */
  .btn { padding:.65rem 1rem; border-radius:1rem; font-weight:800; border:1px solid transparent; transition:.2s ease; }
  .btn:hover { filter:brightness(1.06); }
  .btn-brand {
    color:#fff;
    background-image:linear-gradient(135deg,var(--c10),var(--c9),var(--c4));
    border-color:rgba(255,255,255,.12);
  }
  .btn-ghost {
    color:#e5e7eb; background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12);
  }
  .btn-ghost:hover { background: rgba(255,255,255,.1); }

  /* Inputs */
  .field {
    background:#fff; color:#0f172a; border:1px solid #cbd5e1; border-radius: 14px; padding:.7rem .9rem;
    outline:none; width:100%;
  }
  .field:focus{ box-shadow:0 0 0 3px rgba(99,102,241,.25); border-color:#818cf8; }

  /* Score wheel */
  .badge-good, .badge-warn, .badge-bad, .badge-na{
    display:inline-flex; align-items:center; gap:.5rem; padding:.25rem .75rem;
    border-radius:9999px; font-size:.75rem; font-weight:800; border:1px solid;
  }
  .badge-good{ background:rgba(16,185,129,.14); color:#10b981; border-color:rgba(16,185,129,.35); }
  .badge-warn{ background:rgba(245,158,11,.14); color:#f59e0b; border-color:rgba(245,158,11,.35); }
  .badge-bad { background:rgba(239,68,68,.14);  color:#ef4444; border-color:rgba(239,68,68,.35); }
  .badge-na  { background:rgba(148,163,184,.14);color:#cbd5e1; border-color:rgba(148,163,184,.35); }

  .chip { font-size:.7rem; padding:.2rem .5rem; border-radius:.5rem; border:1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.06); }

  /* Category ribbons */
  .ribbon{ padding:.4rem .9rem; border-radius:9999px; display:inline-flex; align-items:center; gap:.5rem; font-weight:900; font-size:.8rem; color:#fff; }
  .rib-ck{ background: linear-gradient(90deg,var(--c10),var(--c9)); }
  .rib-te{ background: linear-gradient(90deg,var(--c3),var(--c2)); }
  .rib-cq{ background: linear-gradient(90deg,var(--c1),var(--c4)); }
  .rib-sa{ background: linear-gradient(90deg,var(--c5),var(--c6)); }
  .rib-ux{ background: linear-gradient(90deg,var(--c7),var(--c8)); }
  .rib-ec{ background: linear-gradient(90deg,var(--c4),var(--c10)); }

  /* Quick stats cards */
  .qs { position:relative; border-radius: 1rem; overflow:hidden; }
  .qs:before{
    content:""; position:absolute; inset:0; border-top:3px solid; border-image:linear-gradient(90deg,var(--c10),var(--c4)) 1;
    opacity:.75;
  }

  /* Heading chips */
  .hchip{ display:inline-block; padding:.25rem .6rem; border-radius:.6rem; background: rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); }

  /* Modal */
  .modal-backdrop { position:fixed; inset:0; background:rgba(2,6,23,.6); backdrop-filter: blur(6px); display:none; z-index:50; }
  .modal { max-width: 42rem; width: 94vw; }
  .modal.show { display:flex; align-items:center; justify-content:center; }
</style>

<section class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <!-- HERO -->
  <div class="flex items-start justify-between gap-6">
    <div>
      <span class="kicker">Semantic SEO • Analyzer 2.0</span>
      <h1 class="text-3xl md:text-4xl font-extrabold leading-tight mt-1 text-white">
        Pro analysis with multi-color score wheel & actionable checklists
      </h1>
      <p class="text-slate-300 mt-3 max-w-2xl">
        Paste a URL (and optional keyword). We’ll analyze structure, links, schema & coverage—then show exactly what to improve.
      </p>
    </div>
  </div>

  <!-- INPUT -->
  <div class="mt-8 p-6 rounded-2xl glass shadow-soft">
    <h3 class="font-semibold text-white">Analyze a URL</h3>
    <form id="semanticForm" class="mt-4 grid md:grid-cols-[1fr,260px,auto] gap-3">
      <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" d="M10 21a9 9 0 1 1 9-9"/></svg>
        </span>
        <input name="url" type="url" required placeholder="https://example.com/article" class="field pl-9" />
      </div>
      <input name="target_keyword" id="targetKeywordInput" type="text" placeholder="Primary keyword (optional)" class="field" />
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

  <!-- RESULTS -->
  <div id="semanticResult" class="mt-10 hidden">
    <!-- SCORE + QUICK STATS -->
    <div class="grid lg:grid-cols-[340px,1fr] gap-6 items-stretch">
      <!-- Overall Score Wheel -->
      <div class="p-6 rounded-2xl glass shadow-soft flex flex-col items-center justify-center">
        <div class="relative">
          <svg id="scoreWheel" width="260" height="260" viewBox="0 0 260 260">
            <defs>
              <linearGradient id="gradWheel" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#4d194d"/>
                <stop offset="25%" stop-color="#3e1f47"/>
                <stop offset="50%" stop-color="#312244"/>
                <stop offset="75%" stop-color="#0b525b"/>
                <stop offset="100%" stop-color="#065a60"/>
              </linearGradient>
            </defs>
            <circle cx="130" cy="130" r="105" stroke="rgba(255,255,255,.12)" stroke-width="20" fill="none"/>
            <circle id="progressArc" cx="130" cy="130" r="105"
                    stroke="url(#gradWheel)" stroke-width="20" fill="none"
                    stroke-linecap="round" stroke-dasharray="659.73" stroke-dashoffset="659.73"
                    transform="rotate(-90 130 130)"/>
            <text id="scoreText" x="130" y="120" dominant-baseline="middle" text-anchor="middle"
                  font-size="48" font-weight="900" fill="#fff">—</text>
            <text x="130" y="150" dominant-baseline="middle" text-anchor="middle"
                  font-size="12" fill="#cbd5e1" style="letter-spacing:.22em;">OVERALL</text>
          </svg>
        </div>
        <div id="badge" class="mt-3"></div>
      </div>

      <!-- Quick Stats (new layout) -->
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="qs card p-5">
          <div class="text-slate-400 text-xs">Readability (Flesch)</div>
          <div id="readability" class="text-3xl font-extrabold text-white mt-1">—</div>
          <div class="mt-3 h-2 bg-white/10 rounded-full overflow-hidden"><div id="readBar" class="h-2" style="width:0%; background:linear-gradient(90deg,#ef4444,#f59e0b,#10b981)"></div></div>
          <div id="readBadge" class="mt-2 text-sm"></div>
        </div>
        <div class="qs card p-5">
          <div class="text-slate-400 text-xs">Text / HTML Ratio</div>
          <div id="ratioVal" class="text-3xl font-extrabold text-white mt-1">—%</div>
          <div class="mt-2 chip">Higher suggests richer content</div>
        </div>
        <div class="qs card p-5">
          <div class="text-slate-400 text-xs">Internal Links</div>
          <div class="text-3xl font-extrabold text-white mt-1"><span id="internal">0</span></div>
          <div class="mt-2 chip">Build topical clusters</div>
        </div>
        <div class="qs card p-5">
          <div class="text-slate-400 text-xs">External Links</div>
          <div class="text-3xl font-extrabold text-white mt-1"><span id="external">0</span></div>
          <div class="mt-2 chip">Cite authority</div>
        </div>
        <div class="qs card p-5 sm:col-span-2 lg:col-span-3">
          <div class="text-slate-400 text-xs">Meta</div>
          <div class="grid md:grid-cols-2 gap-3 mt-2">
            <div><span class="text-slate-300">Title:</span> <span id="titleVal" class="text-white">—</span></div>
            <div><span class="text-slate-300">Description:</span> <span id="metaVal" class="text-white/90">—</span></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Heading Map (new layout) -->
    <div class="mt-10 p-6 rounded-2xl glass shadow-soft">
      <div class="flex items-center justify-between">
        <h3 class="font-semibold text-white">Heading Map</h3>
        <span class="chip">Hierarchy • Context</span>
      </div>
      <div id="headingMap" class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-3"></div>
      <div class="mt-4 rounded-xl card p-4 text-sm">
        <div class="text-slate-400 text-xs">Images</div>
        <p class="mt-1"><span class="text-slate-300">Missing alt:</span> <span id="imgAlt" class="text-white">—</span></p>
      </div>
    </div>

    <!-- CHECKLIST (your requested structure) -->
    <div class="mt-10 space-y-8" id="checklistWrap">
      <!-- injected by JS -->
    </div>

    <!-- Analyzer Recommendations & Anchors -->
    <div class="mt-10 grid lg:grid-cols-2 gap-6">
      <div class="p-6 rounded-2xl glass shadow-soft">
        <h3 class="font-semibold text-white">Analyzer Recommendations</h3>
        <ul id="recs" class="mt-3 space-y-2 text-sm text-slate-200"></ul>
      </div>
      <div class="p-6 rounded-2xl glass shadow-soft">
        <h3 class="font-semibold text-white">Anchor Text (Top 100)</h3>
        <div id="anchors" class="mt-3 text-sm text-slate-300 space-y-1 max-h-[300px] overflow-auto pr-2"></div>
      </div>
    </div>
  </div>
</section>

<!-- IMPROVE MODAL -->
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
  const $  = (s, r=document)=>r.querySelector(s);
  const $$ = (s, r=document)=>Array.from(r.querySelectorAll(s));

  const elForm = $('#semanticForm');
  const elWrap = $('#semanticResult');
  const errorBox = $('#errorBox'); const errorMsg = $('#errorMsg');
  const btn = $('#submitBtn'); const spinner = $('#spinner'); const btnText = $('#btnText');

  const scoreText = $('#scoreText'); const progressArc = $('#progressArc'); const badge = $('#badge');
  const readBar = $('#readBar'); const readBadge = $('#readBadge');
  const checklistWrap = $('#checklistWrap');

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

  function setLoading(on){ btn.disabled=on; spinner.classList.toggle('hidden',!on); btnText.textContent = on ? 'Analyzing…' : 'Analyze'; }
  function showError(msg){ errorMsg.textContent = msg || 'Analysis failed'; errorBox.classList.remove('hidden'); }
  function hideError(){ errorBox.classList.add('hidden'); }

  /* ===== Wheels ===== */
  const PERIM = 2 * Math.PI * 105; // r=105
  function setWheel(elArc, elText, score){
    const s = Math.max(0, Math.min(100, Number(score||0)));
    elText.textContent = s;
    elArc.setAttribute('stroke-dashoffset', (PERIM * (1 - s/100)).toFixed(2));
  }
  function setOverall(score){
    setWheel(progressArc, scoreText, score);
    if (score >= 80)      badge.innerHTML = `<span class="badge-good">🌟 Great Work — Well Optimized</span>`;
    else if (score >= 60) badge.innerHTML = `<span class="badge-warn">⚠️ Need to optimize content</span>`;
    else                  badge.innerHTML = `<span class="badge-bad">⛳ Need to optimize content</span>`;
  }
  function setReadability(s){
    s = Math.max(0, Math.min(100, Number(s||0)));
    els.readability.textContent = s;
    readBar.style.width = s + '%';
    readBadge.innerHTML = s >= 60
      ? `<span class="badge-good">Readable for general audiences</span>`
      : (s >= 50
          ? `<span class="badge-warn">Acceptable but could be clearer</span>`
          : `<span class="badge-bad">Hard to read — simplify</span>`);
  }

  /* ===== Heading Map (new layout) ===== */
  function renderHeadingMap(headings){
    els.map.innerHTML = '';
    const order = ['h1','h2','h3','h4','h5','h6'];
    order.forEach(level=>{
      const arr = headings?.[level] || [];
      if (!arr.length) return;
      const card = document.createElement('div');
      card.className = 'card p-4 rounded-xl';
      const title = level.toUpperCase();
      card.innerHTML = `<div class="text-xs text-slate-400">${title}</div>`;
      const box = document.createElement('div'); box.className = 'mt-2 space-y-1';
      arr.forEach(t=>{
        const p = document.createElement('div');
        p.innerHTML = `<span class="hchip">${escapeHTML(t)}</span>`;
        box.appendChild(p);
      });
      card.appendChild(box);
      els.map.appendChild(card);
    });
  }

  /* ===== Checklist model (matches your requested categories) ===== */
  const LIST = [
    { catKey:'ck',  catTitle:'1. Content & Keywords', rib:'rib-ck', items:[
      { id:'ck_intent',        label:'Define search intent & primary topic',
        compute:d=> !!(d.intent_coverage?.search_intent), improve:()=>[
          'Match format to top SERP results (guide vs product vs comparison).',
          'State the primary topic clearly in intro & H1.'
        ]},
      { id:'ck_map',           label:'Map target & related keywords (synonyms/PAA)',
        compute:d=> Array.isArray(d.semantic_core?.topic_cloud) && d.semantic_core.topic_cloud.length>0,
        improve:()=>['Add related entities, synonyms & PAA questions to sections.']},
      { id:'ck_h1_kw',         label:'H1 includes primary topic naturally',
        compute:(d,ctx)=> {
          const h1 = (d.content_structure?.headings?.h1||[])[0] || '';
          const kw = (ctx.kw||'').toLowerCase().trim();
          return kw ? h1.toLowerCase().includes(kw) : !!h1;
        },
        improve:()=>['Use a single H1 and include the primary topic naturally.']},
      { id:'ck_faq',           label:'Integrate FAQs / questions with answers',
        compute:_=> 'na', improve:()=>['Add an FAQ section; cover PAA-like questions.']},
      { id:'ck_nlp',           label:'Readable, NLP-friendly language',
        compute:d=> {
          const r = Number(d.content_structure?.readability_flesch||0);
          return r>=60 ? true : (r>=50 ? 'warn' : false);
        },
        improve:()=>['Short sentences & paragraphs; prefer active voice; define terms briefly.']},
    ]},

    { catKey:'te',  catTitle:'2. Technical Elements', rib:'rib-te', items:[
      { id:'te_title_len',     label:'Title tag (≈50–60 chars) w/ primary keyword',
        compute:d=>{
          const len = (d.content_structure?.title||'').trim().length;
          return (len>=50 && len<=60) ? true : (len>=35 && len<=70 ? 'warn' : false);
        },
        improve:()=>['Aim ~50–60 chars. Lead with the primary keyword.']},
      { id:'te_meta_len',      label:'Meta description (≈140–160 chars) + CTA',
        compute:d=>{
          const len = (d.content_structure?.meta_description||'').trim().length;
          return (len>=140 && len<=160) ? true : (len>=110 && len<=180 ? 'warn' : false);
        },
        improve:()=>['Write ~150–160 chars; add benefit + CTA; include primary/secondary keywords.']},
      { id:'te_canonical',     label:'Canonical tag set correctly',
        compute:_=>'na', improve:()=>['Add a self-referencing canonical tag.']},
      { id:'te_sitemap',       label:'Indexable & listed in XML sitemap',
        compute:_=>'na', improve:()=>['Ensure page is indexable and present in sitemap.xml.']},
    ]},

    { catKey:'cq',  catTitle:'3. Content Quality', rib:'rib-cq', items:[
      { id:'cq_eat',           label:'E-E-A-T signals (author, date, expertise)',
        compute:_=>'na', improve:()=>['Show author bio, updated date, credentials, and references.']},
      { id:'cq_unique',        label:'Unique value vs. top competitors',
        compute:_=>'na', improve:()=>['Add original data, examples, comparisons & practical insights.']},
      { id:'cq_facts',         label:'Facts & citations up to date',
        compute:_=>'na', improve:()=>['Refresh stats; cite reputable sources.']},
      { id:'cq_media',         label:'Helpful media (images/video) w/ captions',
        compute:d=> (d.technical_seo?.image_count||0)>0 ? true : 'warn',
        improve:()=>['Add annotated images/video with descriptive captions and alt text.']},
    ]},

    { catKey:'sa',  catTitle:'4. Structure & Architecture', rib:'rib-sa', items:[
      { id:'sa_headings',      label:'Logical H2/H3 headings & topic clusters',
        compute:d=> d.content_structure?.skipped_levels === false ? true : 'warn',
        improve:()=>['Avoid heading level jumps; group related subtopics under clear H2/H3.']},
      { id:'sa_internal',      label:'Internal links to hub/related pages',
        compute:d=> (d.technical_seo?.links?.internal||0) >= 3 ? true : 'warn',
        improve:()=>['Link to cluster hub & related support pages with descriptive anchors.']},
      { id:'sa_slug',          label:'Clean, descriptive URL slug',
        compute:(d,ctx)=>{
          const u = (ctx.url||'').split('?')[0];
          return /[^\s]{10,}/.test(u) ? true : 'warn';
        },
        improve:()=>['Keep slug human-readable, short, and keyword-descriptive.']},
      { id:'sa_breadcrumbs',   label:'Breadcrumbs enabled (+ schema)',
        compute:_=>'na', improve:()=>['Add BreadcrumbList schema and visible breadcrumbs.']},
    ]},

    { catKey:'ux',  catTitle:'5. User Signals & Experience', rib:'rib-ux', items:[
      { id:'ux_mobile',        label:'Mobile-friendly, responsive layout',
        compute:_=>'na', improve:()=>['Verify mobile usability in GSC; fix tap targets & viewport issues.']},
      { id:'ux_speed',         label:'Optimized speed (compression, lazy-load)',
        compute:_=>'na', improve:()=>['Compress images, enable caching, defer non-critical JS.']},
      { id:'ux_webvitals',     label:'Core Web Vitals passing (LCP/INP/CLS)',
        compute:_=>'na', improve:()=>['Use PSI/Lighthouse to diagnose and improve LCP/INP/CLS.']},
      { id:'ux_cta',           label:'Clear CTAs and next steps',
        compute:_=>'na', improve:()=>['Add contextual CTAs guiding users to the next action.']},
    ]},

    { catKey:'ec',  catTitle:'6. Entities & Context', rib:'rib-ec', items:[
      { id:'ec_primary',       label:'Primary entity clearly defined',
        compute:d=> Array.isArray(d.semantic_core?.primary_topics) && d.semantic_core.primary_topics.length>0,
        improve:()=>['State primary entity early; keep terminology consistent.']},
      { id:'ec_related',       label:'Related entities covered with context',
        compute:d=> Array.isArray(d.semantic_core?.topic_cloud) && d.semantic_core.topic_cloud.length>0,
        improve:()=>['Cover related entities and their relationships in body & headings.']},
      { id:'ec_schema',        label:'Valid schema markup (Article/FAQ/Product)',
        compute:d=> (d.technical_seo?.structured_data?.json_ld || d.technical_seo?.structured_data?.microdata || d.technical_seo?.structured_data?.rdfa) ? true : 'warn',
        improve:()=>['Add JSON-LD for Article/FAQ/etc and validate in Rich Results Test.']},
      { id:'ec_sameas',        label:'sameAs/Organization details present',
        compute:_=>'na', improve:()=>['Add Organization schema with sameAs links to authoritative profiles.']},
    ]},
  ];

  function statusBadge(status){
    if (status===true) return `<span class="badge-good">OK</span>`;
    if (status==='warn') return `<span class="badge-warn">Needs Work</span>`;
    if (status===false) return `<span class="badge-bad">Needs Work</span>`;
    return `<span class="badge-na">N/A</span>`;
  }

  function renderChecklist(data, ctx){
    checklistWrap.innerHTML = '';
    LIST.forEach(group=>{
      const sec = document.createElement('section');
      sec.className = 'p-6 rounded-2xl glass shadow-soft';
      sec.innerHTML = `
        <div class="flex items-center justify-between gap-3">
          <span class="ribbon ${group.rib}">${group.catTitle}</span>
          <span class="chip">Auto-scored where detectable</span>
        </div>
      `;
      const ul = document.createElement('ul');
      ul.className = 'mt-4 space-y-3 text-slate-200';
      group.items.forEach(item=>{
        let st;
        try { st = item.compute(data, ctx); } catch(_){ st='na'; }
        const li = document.createElement('li');
        li.className = 'rounded-xl card p-3 border border-white/10';
        li.innerHTML = `
          <div class="flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
              <div class="h-2.5 w-2.5 rounded-full" style="background:${st===true?'#10b981':st==='warn'?'#f59e0b':st===false?'#ef4444':'#94a3b8'}"></div>
              <span>${escapeHTML(item.label)}</span>
            </div>
            <div class="flex items-center gap-2">
              ${statusBadge(st)}
              <button class="btn btn-ghost text-xs" data-improve="${item.id}">Improve</button>
            </div>
          </div>
        `;
        ul.appendChild(li);
      });
      sec.appendChild(ul);
      checklistWrap.appendChild(sec);
    });
  }

  /* ===== Improve modal ===== */
  const modalBackdrop = $('#modalBackdrop');
  const modalTitle = $('#modalTitle');
  const modalBody = $('#modalBody');
  $('#modalClose').addEventListener('click', closeImprove);
  $('#modalOk').addEventListener('click', closeImprove);
  modalBackdrop.addEventListener('click', (e)=>{ if(e.target===modalBackdrop) closeImprove(); });
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('[data-improve]'); if(!btn) return;
    const id = btn.getAttribute('data-improve');
    openImprove(id, window.__lastData || {}, window.__ctx || {});
  });
  function openImprove(id, data, ctx){
    const item = LIST.flatMap(g=>g.items).find(x=>x.id===id);
    if(!item) return;
    modalTitle.textContent = item.label || 'Improve';
    const tips = (typeof item.improve==='function') ? item.improve(data, ctx) : (item.improve || []);
    modalBody.innerHTML = `
      <div class="text-slate-300">Suggestions:</div>
      <ul class="mt-2 list-disc pl-5 space-y-1">
        ${(tips||[]).map(t=>`<li>${escapeHTML(t)}</li>`).join('')}
      </ul>
    `;
    modalBackdrop.classList.add('show');
  }
  function closeImprove(){ modalBackdrop.classList.remove('show'); }

  /* ===== Submit & bind data ===== */
  elForm.addEventListener('submit', async (e)=>{
    e.preventDefault(); hideError(); setLoading(true);
    try{
      const fd = new FormData(elForm);
      const ctx = { url: (fd.get('url')||'').trim(), kw:(fd.get('target_keyword')||'').trim() };
      window.__ctx = ctx;

      const res = await fetch('/api/semantic-analyze', {
        method:'POST',
        headers:{ 'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify({ url: ctx.url, target_keyword: ctx.kw })
      });

      if(!res.ok){
        let msg = `HTTP ${res.status}`;
        try{ const j = await res.json(); if(j?.error) msg = j.error; }catch(_){}
        showError(msg); setLoading(false); return;
      }

      const data = await res.json();
      if(!data?.ok){ showError(data?.error || 'Analysis failed'); setLoading(false); return; }

      window.__lastData = data;
      elWrap.classList.remove('hidden');

      // Overall + Readability
      setOverall(data.overall_score);
      const r = Number(data.content_structure?.readability_flesch||0); setReadability(r);

      // Stats
      els.ratio.textContent = data.content_structure?.text_to_html_ratio ?? '—';
      els.internal.textContent = data.technical_seo?.links?.internal ?? 0;
      els.external.textContent = data.technical_seo?.links?.external ?? 0;
      els.title.textContent = data.content_structure?.title || '—';
      els.meta.textContent  = data.content_structure?.meta_description || '—';
      els.imgAlt.textContent = `${data.technical_seo?.image_alt_missing ?? 0} / ${data.technical_seo?.image_count ?? 0}`;

      // Headings
      renderHeadingMap(data.content_structure?.headings || {});

      // Recommendations
      els.recs.innerHTML = '';
      (data.recommendations||[]).forEach(r=>{
        const sev = (r.severity||'').toLowerCase();
        const c = sev==='critical' ? 'badge-bad' : sev==='warning' ? 'badge-warn' : 'chip';
        const li = document.createElement('li');
        li.innerHTML = `<span class="${c} mr-2">${escapeHTML(r.severity||'Info')}</span>${escapeHTML(r.text||'')}`;
        els.recs.appendChild(li);
      });

      // Anchors
      els.anchors.innerHTML = '';
      (data.technical_seo?.links?.anchors||[]).slice(0,100).forEach(a=>{
        const p = document.createElement('p');
        p.textContent = `[${a.type||'link'}] ${a.text||'(no text)'} → ${a.href||''}`;
        els.anchors.appendChild(p);
      });

      // Checklist
      renderChecklist(data, ctx);

    }catch(err){
      console.error(err); showError(err?.message || 'Network error');
    }finally{ setLoading(false); }
  });

  function escapeHTML(s){ return (''+s).replace(/[&<>"']/g, m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m])); }
</script>
@endsection
