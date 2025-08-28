@extends('layouts.app', ['title' => 'Semantic SEO Master Analyzer 2.0'])

@section('content')
<style>
  /* ========== Fresh, premium palette (purple-teal + neon accents) ========== */
  :root{
    --bg:#0b0f19;         /* page base */
    --bg2:#0e1424;        /* panels */
    --card:#121933;
    --edge:rgba(255,255,255,.08);

    --ink:#e6e9f2;        /* primary text */
    --muted:#b8bfd6;      /* secondary text */

    --good:#18d39e;       /* >= 80 */
    --warn:#ffb020;       /* 60–79 */
    --bad:#ef5868;        /* < 60 */
    --na:#9aa0b9;

    --brand1:#7c3aed;     /* purple */
    --brand2:#06b6d4;     /* cyan */
    --brand3:#22d3ee;     /* teal */
    --accent:#ffe44d;     /* neon */

    --gradA:linear-gradient(135deg,#151a33, #0f1731 45%, #0c1a2f);
    --gradB:linear-gradient(135deg, rgba(124,58,237,.25), rgba(6,182,212,.15));
    --gradBtn:linear-gradient(135deg,#7c3aed 0%,#06b6d4 60%,#22d3ee 100%);
    --gradHead:linear-gradient(90deg, #7c3aed, #22d3ee);
  }
  body{ background: radial-gradient(1000px 700px at -10% -10%, rgba(124,58,237,.18), transparent 55%), radial-gradient(900px 600px at 110% -10%, rgba(6,182,212,.18), transparent 55%), var(--bg); }

  .wrap{ max-width: 1200px; margin-inline:auto; padding-inline:1.25rem; }
  .glass { background: var(--gradA); border:1px solid var(--edge); border-radius: 18px; }
  .card  { background: var(--bg2);  border:1px solid var(--edge); border-radius: 16px; }
  .shadow-soft{ box-shadow: 0 30px 80px rgba(0,0,0,.35); }

  .title{ color:var(--ink); font-weight:900; letter-spacing:-.02em; }
  .lead { color:var(--muted); }

  .btn{ display:inline-flex; align-items:center; gap:.5rem; padding:.8rem 1.1rem; border-radius: 16px; font-weight:900; border:1px solid transparent; transition:.18s ease; }
  .btn:disabled{ opacity:.6; cursor:not-allowed; }
  .btn-brand{ color:#0a0f1c; background: var(--gradBtn); border-color: rgba(255,255,255,.22); }
  .btn-brand:hover{ filter: brightness(1.08); }
  .btn-ghost{ color:var(--ink); background: rgba(255,255,255,.06); border:1px solid var(--edge); }
  .btn-ghost:hover{ background: rgba(255,255,255,.10); }

  .field{
    width:100%; background:#fff; color:#0b1020; border:1px solid #d6d9e8; border-radius: 14px; padding:.8rem .95rem; outline:none;
  }
  .field:focus{ border-color:#7c3aed; box-shadow:0 0 0 4px rgba(124,58,237,.16); }

  /* Score wheel */
  .badge{ display:inline-flex; align-items:center; gap:.5rem; padding:.32rem .8rem; border-radius:9999px; border:1px solid; font-weight:800; font-size:.78rem; }
  .good{ background:rgba(24,211,158,.15); color:var(--good); border-color:rgba(24,211,158,.35); }
  .warn{ background:rgba(255,176,32,.14); color:var(--warn); border-color:rgba(255,176,32,.35); }
  .bad { background:rgba(239,88,104,.15); color:var(--bad);   border-color:rgba(239,88,104,.35); }
  .na  { background:rgba(154,160,185,.15); color:var(--na);   border-color:rgba(154,160,185,.35); }

  /* Category header & list */
  .cat-head{
    display:flex; align-items:center; justify-content:space-between; gap:1rem;
    padding: .9rem 1rem; border-radius: 14px; color:#fff; background: var(--gradHead); border:1px solid rgba(255,255,255,.18);
  }
  .cat-title{ font-weight:900; letter-spacing:.01em; }
  .item{
    display:grid; grid-template-columns: 1fr auto; gap:.6rem; align-items:center;
    padding:.9rem 1rem; border-radius: 12px; border:1px solid var(--edge); background:var(--bg2);
  }
  .bar{ height:8px; background:rgba(255,255,255,.08); border-radius:12px; overflow:hidden; }
  .bar > i{ display:block; height:100%; width:0%; border-radius:12px; background: linear-gradient(90deg, var(--bad), var(--warn), var(--good)); }
  .score-pill{ font-weight:900; padding:.25rem .55rem; border-radius: 10px; border:1px solid var(--edge); color:var(--ink); min-width:3.2rem; text-align:center; }
  .score-good{ background:rgba(24,211,158,.1); color:var(--good); border-color:rgba(24,211,158,.35);}
  .score-warn{ background:rgba(255,176,32,.1); color:var(--warn); border-color:rgba(255,176,32,.35);}
  .score-bad { background:rgba(239,88,104,.1); color:var(--bad);  border-color:rgba(239,88,104,.35);}
  .score-na  { background:rgba(154,160,185,.1); color:var(--na);  border-color:rgba(154,160,185,.35);}

  .sub{ color:var(--muted); font-size:.75rem; }

  /* Modal */
  .backdrop{ position:fixed; inset:0; background:rgba(4,8,18,.6); backdrop-filter: blur(6px); display:none; z-index:60; }
  .backdrop.show{ display:flex; align-items:center; justify-content:center; }
  .modal{ width:min(94vw,820px); background:var(--bg2); border:1px solid var(--edge); border-radius:18px; padding:1rem; color:var(--ink); }
  .modal h3{ font-weight:900; }
  .modal .col{ background: var(--gradA); border:1px solid var(--edge); border-radius:12px; padding: .75rem; }
</style>

<section class="wrap py-10">
  <!-- HERO -->
  <div class="flex items-start justify-between gap-6">
    <div>
      <div class="text-xs tracking-[.25em] text-[#a9b7ff] font-bold">SEMANTIC ANALYZER • V2.0</div>
      <h1 class="title text-3xl md:text-4xl mt-2">Professional audit, vivid scoring & actionable fixes</h1>
      <p class="lead mt-2 max-w-2xl">Analyze a URL (optional keyword). We’ll compute structure, links, schema, readability and map them to a checklist with per-item scores.</p>
    </div>
  </div>

  <!-- INPUT STRIP -->
  <div class="glass shadow-soft mt-8 p-6">
    <form id="semanticForm" class="grid md:grid-cols-[1fr,260px,auto] gap-3">
      <input class="field" name="url" type="url" required placeholder="https://example.com/article" />
      <input class="field" name="target_keyword" id="kwInput" type="text" placeholder="Primary keyword (optional)" />
      <button id="submitBtn" class="btn btn-brand" type="submit">
        <svg id="spin" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" class="opacity-30"></circle>
          <path d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z" fill="currentColor" class="opacity-80"></path>
        </svg>
        <span id="btnText">Analyze URL</span>
      </button>
    </form>

    <div id="errBox" class="mt-4 hidden">
      <div class="card p-3 border border-[rgba(255,80,80,.35)] text-[#ffd7db]">Analysis failed: <span id="errMsg">Unknown error</span></div>
    </div>
  </div>

  <!-- RESULTS -->
  <div id="result" class="mt-10 hidden">
    <div class="grid lg:grid-cols-[340px,1fr] gap-6">
      <!-- Score wheel -->
      <div class="glass shadow-soft p-6 flex flex-col items-center justify-center">
        <div class="relative">
          <svg id="wheel" width="260" height="260" viewBox="0 0 260 260" aria-label="Overall score">
            <defs>
              <linearGradient id="wheelGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%"   stop-color="#ef5868"/>
                <stop offset="40%"  stop-color="#ffb020"/>
                <stop offset="85%"  stop-color="#18d39e"/>
                <stop offset="100%" stop-color="#23f0c7"/>
              </linearGradient>
            </defs>
            <circle cx="130" cy="130" r="105" stroke="rgba(255,255,255,.12)" stroke-width="20" fill="none"/>
            <circle id="arc" cx="130" cy="130" r="105" stroke="url(#wheelGrad)" stroke-width="20" fill="none"
                    stroke-linecap="round" stroke-dasharray="659.73" stroke-dashoffset="659.73"
                    transform="rotate(-90 130 130)"/>
            <text id="scoreTxt" x="130" y="120" text-anchor="middle" dominant-baseline="middle"
                  font-size="48" font-weight="900" fill="#fff">—</text>
            <text x="130" y="150" text-anchor="middle" dominant-baseline="middle" font-size="12" fill="#cbd5e1" style="letter-spacing:.22em;">OVERALL</text>
          </svg>
        </div>
        <div id="overallBadge" class="mt-3"></div>
      </div>

      <!-- Quick stats -->
      <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="card p-5">
          <div class="sub">Readability (Flesch)</div>
          <div id="readability" class="text-3xl font-extrabold text-white mt-1">—</div>
          <div class="bar mt-3"><i id="readBar"></i></div>
          <div id="readHint" class="mt-2 text-sm"></div>
        </div>
        <div class="card p-5">
          <div class="sub">Text / HTML Ratio</div>
          <div id="ratio" class="text-3xl font-extrabold text-white mt-1">—%</div>
          <div class="mt-2 text-xs text-[var(--muted)]">More text content generally scores higher.</div>
        </div>
        <div class="card p-5">
          <div class="sub">Links</div>
          <div class="mt-1 text-white text-xl"><b id="internal">0</b> internal • <b id="external">0</b> external</div>
          <div class="mt-2 text-xs text-[var(--muted)]">Use descriptive anchors to strengthen topical clusters.</div>
        </div>
        <div class="card p-5 sm:col-span-2 lg:col-span-3">
          <div class="sub">Meta</div>
          <div class="grid md:grid-cols-2 gap-3 mt-2">
            <div><span class="text-[var(--muted)]">Title:</span> <span id="ttl" class="text-white">—</span></div>
            <div><span class="text-[var(--muted)]">Description:</span> <span id="desc" class="text-white/90">—</span></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Headings -->
    <div class="glass shadow-soft mt-10 p-6">
      <div class="flex items-center justify-between">
        <h3 class="text-white font-bold">Heading Map</h3>
        <span class="badge na">Hierarchy • Context</span>
      </div>
      <div id="headingMap" class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-3"></div>
      <div class="card p-4 mt-4 text-sm">
        <div class="sub">Images</div>
        <div class="text-white mt-1">Missing alt: <span id="imgAlt">—</span></div>
      </div>
    </div>

    <!-- NEW: Categories & Checklist (stylish) -->
    <div id="cats" class="mt-10 grid lg:grid-cols-2 gap-6"></div>

    <!-- Analyzer recs & anchors -->
    <div class="grid lg:grid-cols-2 gap-6 mt-10">
      <div class="glass shadow-soft p-6">
        <h3 class="text-white font-bold">Analyzer Recommendations</h3>
        <ul id="recs" class="mt-3 space-y-2 text-sm text-[var(--ink)]/90"></ul>
      </div>
      <div class="glass shadow-soft p-6">
        <h3 class="text-white font-bold">Anchor Text (Top 100)</h3>
        <div id="anchors" class="mt-3 text-sm text-[var(--muted)] space-y-1 max-h-[300px] overflow-auto pr-2"></div>
      </div>
    </div>
  </div>
</section>

<!-- IMPROVE MODAL -->
<div id="backdrop" class="backdrop">
  <div class="modal">
    <div class="flex items-center justify-between">
      <h3 id="mTitle" class="text-lg">Improve</h3>
      <button id="mClose" class="btn btn-ghost" style="padding:.35rem .6rem">✕</button>
    </div>
    <div class="grid md:grid-cols-2 gap-6 mt-4">
      <div class="col">
        <div class="sub">Google Ideas</div>
        <ul id="gIdeas" class="mt-2 text-sm space-y-1"></ul>
        <div id="gLinks" class="mt-3 text-xs"></div>
      </div>
      <div class="col">
        <div class="sub">Pro Tips</div>
        <ul id="proTips" class="mt-2 text-sm space-y-1"></ul>
      </div>
    </div>
    <div class="mt-5 text-right">
      <button id="mOk" class="btn btn-brand">Got it</button>
    </div>
  </div>
</div>

<script>
  /* ---------- Helpers ---------- */
  const $ = (s, r=document)=>r.querySelector(s);
  const esc = s => (''+s).replace(/[&<>"']/g,m=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;' }[m]));

  const form = $('#semanticForm'), result = $('#result');
  const btn = $('#submitBtn'), spin = $('#spin'), btnText = $('#btnText');
  const errBox = $('#errBox'), errMsg = $('#errMsg');

  const PERIM = 2*Math.PI*105;
  const arc = $('#arc'), scoreTxt = $('#scoreTxt'), overallBadge = $('#overallBadge');
  const readOut = $('#readability'), readBar = $('#readBar'), readHint = $('#readHint');
  const ratio = $('#ratio'), internal = $('#internal'), external = $('#external'), ttl = $('#ttl'), desc = $('#desc');
  const headingMap = $('#headingMap'), imgAlt = $('#imgAlt');
  const cats = $('#cats'), recs = $('#recs'), anchors = $('#anchors');
  let ctx = {}, lastData = null;

  function setLoading(v){ btn.disabled=v; spin.classList.toggle('hidden', !v); btnText.textContent = v?'Analyzing…':'Analyze URL'; }
  function showError(m){ errMsg.textContent=m||'Analysis failed'; errBox.classList.remove('hidden'); }
  function hideError(){ errBox.classList.add('hidden'); }

  function setWheel(score){
    const s = Math.max(0, Math.min(100, Number(score||0)));
    scoreTxt.textContent = s;
    arc.setAttribute('stroke-dashoffset', (PERIM*(1-s/100)).toFixed(2));
    if (s>=80)      overallBadge.innerHTML = `<span class="badge good">🌟 Great Work — Well Optimized</span>`;
    else if (s>=60) overallBadge.innerHTML = `<span class="badge warn">⚠️ Need to optimize content</span>`;
    else            overallBadge.innerHTML = `<span class="badge bad">⛳ Need to optimize content</span>`;
  }
  function setReadability(val){
    const s = Math.max(0, Math.min(100, Number(val||0)));
    readOut.textContent = s;
    readBar.style.width = s + '%';
    readHint.innerHTML = s>=60
      ? `<span class="badge good">Readable for general audiences</span>`
      : s>=50
        ? `<span class="badge warn">Acceptable but could be clearer</span>`
        : `<span class="badge bad">Hard to read — simplify</span>`;
  }

  function renderHeadings(hs){
    headingMap.innerHTML=''; const order=['h1','h2','h3','h4','h5','h6'];
    order.forEach(k=>{
      const arr = hs?.[k]||[]; if(!arr.length) return;
      const card = document.createElement('div'); card.className='card p-4';
      card.innerHTML = `<div class="sub">${k.toUpperCase()}</div><div class="mt-2 space-y-1">${arr.map(t=>`<div class="text-white/90">• <span class="px-2 py-0.5 rounded bg-white/5 border border-[var(--edge)]">${esc(t)}</span></div>`).join('')}</div>`;
      headingMap.appendChild(card);
    });
  }

  /* ---------- Checklist model with per-item scores ---------- */
  const Model = [
    {
      key:'content',
      title:'1. Content & Keywords',
      items:[
        { id:'intent', label:'Define search intent & primary topic',
          score:d => d.intent_coverage?.search_intent ? 100 : 40,
          tips:(d,c)=>[
            'Match format & angle to page-1 SERP (guide vs comparison vs product).',
            'State the primary topic and outcome in intro & H1.'
          ],
          queries:c=>[
            `${c.kw||''} intent`,
            `${c.kw||''} vs alternatives`,
            `${c.kw||''} outline`
          ]},
        { id:'keywords', label:'Map target & related keywords (synonyms/PAA)',
          score:d => {
            const n = (d.semantic_core?.topic_cloud||[]).length;
            return Math.max(20, Math.min(100, n*12)); // 0.. >=5 terms = 60+ ; >=8 => 96
          },
          tips:()=>['Add related entities, synonyms & PAA questions to sections.','Cover “people also ask” queries explicitly.'],
          queries:c=>[
            `${c.kw||''} people also ask`,
            `${c.kw||''} related topics`
          ]},
        { id:'h1', label:'H1 includes primary topic naturally',
          score:(d,c)=>{
            const h1=(d.content_structure?.headings?.h1||[])[0]||'';
            if(!h1) return 40;
            if(!(c.kw||'').trim()) return 80;
            return h1.toLowerCase().includes((c.kw||'').toLowerCase()) ? 100 : 65;
          },
          tips:()=>['Use exactly one H1; include the main topic naturally.'],
          queries:c=>[
            `${c.kw||''} best title examples`,
            `${c.kw||''} h1 examples`
          ]},
        { id:'faq', label:'Integrate FAQs / questions with answers',
          score:d => {
            const h2 = (d.content_structure?.headings?.h2||[]).join(' ').toLowerCase();
            const hasQ = h2.includes('faq') || h2.includes('question') || (d.content_structure?.headings?.h3||[]).some(h=>/\?$/.test(h));
            return hasQ ? 90 : 50;
          },
          tips:()=>['Add 4–6 FAQs that mirror PAA; mark up with FAQPage schema if relevant.'],
          queries:c=>[
            `${c.kw||''} faq`,
            `${c.kw||''} questions`
          ]},
        { id:'nlp', label:'Readable, NLP-friendly language',
          score:d => Number(d.content_structure?.readability_flesch||0),
          tips:()=>['Short sentences & paragraphs; active voice; define jargon; add transitions.'],
          queries:c=>[
            `${c.kw||''} readability`,
            `${c.kw||''} simple explanation`
          ]},
      ]
    },
    {
      key:'tech',
      title:'2. Technical Elements',
      items:[
        { id:'title', label:'Title tag (≈50–60 chars) w/ primary keyword',
          score:d=>{
            const len=(d.content_structure?.title||'').trim().length;
            if(!len) return 20;
            const ideal=55, span=20;
            const diff=Math.abs(len-ideal);
            return Math.max(40, Math.round(100 - (diff/span)*40));
          },
          tips:()=>['Aim ~50–60 chars; lead with the primary keyword; add benefit or number.'],
          queries:c=>[
            `${c.kw||''} title examples`,
            `compelling title formula`
          ]},
        { id:'meta', label:'Meta description (≈140–160 chars) + CTA',
          score:d=>{
            const len=(d.content_structure?.meta_description||'').trim().length;
            if(!len) return 30;
            const ideal=155, span=50;
            const diff=Math.abs(len-ideal);
            return Math.max(45, Math.round(100 - (diff/span)*45));
          },
          tips:()=>['Write ~150–160 chars; include value + CTA; naturally weave primary/secondary terms.'],
          queries:c=>[
            `${c.kw||''} meta description examples`,
            `high ctr meta description`
          ]},
        { id:'schema', label:'Valid schema markup (Article/FAQ/Product)',
          score:d=>{
            const s=d.technical_seo?.structured_data||{};
            return (s.json_ld||s.microdata||s.rdfa) ? 95 : 50;
          },
          tips:()=>['Add JSON-LD for Article/FAQ/HowTo/Product; validate in Rich Results Test.'],
          queries:c=>[
            `${c.kw||''} schema markup`,
            `article schema json-ld example`
          ]},
        { id:'ratio', label:'Healthy text/HTML ratio',
          score:d=>{
            const r=Number(d.content_structure?.text_to_html_ratio||0); // %
            if(!r) return 40;
            if(r>=25) return 95;
            if(r>=18) return 85;
            if(r>=12) return 70;
            if(r>=8)  return 55;
            return 40;
          },
          tips:()=>['Reduce boilerplate; add substantial body copy; avoid heavy inline markup.'],
          queries:()=>[`improve text to html ratio`]}
      ]
    },
    {
      key:'quality',
      title:'3. Content Quality',
      items:[
        { id:'media', label:'Helpful media (images/video) w/ captions',
          score:d=>{
            const c = Number(d.technical_seo?.image_count||0);
            const miss = Number(d.technical_seo?.image_alt_missing||0);
            if(c===0) return 45;
            const base = Math.min(100, 60 + c*10);
            const penalty = Math.min(30, miss*8);
            return Math.max(40, base - penalty);
          },
          tips:()=>['Add annotated images/video; compress; add descriptive filenames and alt text.'],
          queries:c=>[
            `${c.kw||''} diagram`,
            `${c.kw||''} infographic`
          ]},
        { id:'internalLinks', label:'Internal links to hub/related pages',
          score:d=>{
            const n=Number(d.technical_seo?.links?.internal||0);
            return Math.max(40, Math.min(100, 50 + n*12));
          },
          tips:()=>['Link to hub + related support pages using descriptive anchors; add breadcrumb trail.'],
          queries:c=>[
            `${c.kw||''} site:{{ request()->getHost() }}`,
            `topic cluster internal linking`
          ]},
        { id:'externalLinks', label:'External links to authority',
          score:d=>{
            const n=Number(d.technical_seo?.links?.external||0);
            return n>0 ? 85 : 55;
          },
          tips:()=>['Cite authoritative sources; link with context; avoid nofollow for editorial citations.'],
          queries:c=>[
            `${c.kw||''} research`,
            `${c.kw||''} statistics`
          ]},
        { id:'uniqueness', label:'Unique value vs. top competitors',
          score:d=>{
            /* heuristic: longer title + decent ratio => better */
            const t=(d.content_structure?.title||'').length, r=Number(d.content_structure?.text_to_html_ratio||0);
            return Math.max(40, Math.min(100, Math.round((t/60*30) + (r/25*70))));
          },
          tips:()=>['Add original data, examples, comparisons, and POV; synthesize beyond summaries.'],
          queries:c=>[
            `${c.kw||''} case study`,
            `${c.kw||''} data`
          ]},
      ]
    },
    {
      key:'structure',
      title:'4. Structure & Architecture',
      items:[
        { id:'hierarchy', label:'Logical H2/H3 structure (no level skips)',
          score:d=> d.content_structure?.skipped_levels===false ? 95 : 65,
          tips:()=>['Avoid jumping H1→H3; nest topics logically under H2/H3.'],
          queries:c=>[
            `${c.kw||''} outline h2 h3`,
            `content outline best practices`
          ]},
        { id:'slug', label:'Clean, descriptive URL slug',
          score:(_d,c)=>{
            const u=(c.url||'').split('?')[0];
            const ok = /https?:\/\/[^\/]+\/([^?#]{1,80})$/.test(u);
            return ok ? 90 : 60;
          },
          tips:()=>['Keep slug short, readable and keyword-descriptive; avoid params.'],
          queries:c=>[
            `${c.kw||''} url slug`,
            `seo friendly url best practices`
          ]},
        { id:'breadcrumbs', label:'Breadcrumbs (+ schema)',
          score:_=>70,
          tips:()=>['Add visible breadcrumbs and BreadcrumbList JSON-LD.'],
          queries:()=>[
            `breadcrumb schema json-ld example`
          ]},
      ]
    },
    {
      key:'entities',
      title:'5. Entities & Context',
      items:[
        { id:'primaryEntity', label:'Primary entity clearly defined',
          score:d=>{
            const n=(d.semantic_core?.primary_topics||[]).length;
            return n>0? 95 : 55;
          },
          tips:()=>['Declare primary entity early; keep term consistent; use supporting facts.'],
          queries:c=>[
            `${c.kw||''} entity`,
            `named entity ${c.kw||''}`
          ]},
        { id:'relatedEntities', label:'Related entities covered with context',
          score:d=>{
            const n=(d.semantic_core?.topic_cloud||[]).length;
            return Math.max(40, Math.min(100, 50+n*6));
          },
          tips:()=>['Introduce related entities and explain their relationships to the main topic.'],
          queries:c=>[
            `${c.kw||''} related entities`,
            `${c.kw||''} knowledge graph`
          ]},
        { id:'schemaPresence', label:'Appropriate schema present',
          score:d=>{
            const s=d.technical_seo?.structured_data||{};
            return (s.json_ld||s.microdata||s.rdfa) ? 95 : 60;
          },
          tips:()=>['Add Article/FAQ/HowTo/Product JSON-LD; validate for errors.'],
          queries:c=>[
            `${c.kw||''} article schema`,
            `${c.kw||''} faq schema`
          ]},
      ]
    }
  ];

  function colorClass(n){
    if (n==='—') return 'score-na';
    if (n>=80) return 'score-good';
    if (n>=60) return 'score-warn';
    return 'score-bad';
  }

  function renderCategories(data){
    cats.innerHTML='';
    Model.forEach(group=>{
      const sec = document.createElement('section');
      sec.className = 'glass shadow-soft p-6';
      sec.innerHTML = `<div class="cat-head"><div class="cat-title">${esc(group.title)}</div><div class="text-xs">Auto-scored from your page</div></div>`;
      const list = document.createElement('div'); list.className='mt-4 space-y-3';

      group.items.forEach(it=>{
        let score='—';
        try{ score = Math.round(it.score(data, ctx)); if(!isFinite(score)) score='—'; }catch(_){ score='—'; }
        const li = document.createElement('div'); li.className='item';
        li.innerHTML = `
          <div>
            <div class="text-[var(--ink)] font-semibold">${esc(it.label)}</div>
            <div class="bar mt-2"><i style="width:${score==='—'?0:score}%;"></i></div>
          </div>
          <div class="flex items-center gap-2">
            <span class="score-pill ${colorClass(score)}">${score==='—'?'—':score}</span>
            <button class="btn btn-ghost text-xs" data-improve="${group.key}:${it.id}">Improve</button>
          </div>
        `;
        list.appendChild(li);
      });

      sec.appendChild(list);
      cats.appendChild(sec);
    });
  }

  /* ---------- Improve modal with Google ideas ---------- */
  const backdrop = $('#backdrop'), mTitle = $('#mTitle'), gIdeas = $('#gIdeas'), gLinks = $('#gLinks'), proTips = $('#proTips');
  $('#mClose').addEventListener('click', ()=>backdrop.classList.remove('show'));
  $('#mOk').addEventListener('click', ()=>backdrop.classList.remove('show'));
  backdrop.addEventListener('click', e=>{ if(e.target===backdrop) backdrop.classList.remove('show'); });

  document.addEventListener('click', async (e)=>{
    const b = e.target.closest('button[data-improve]'); if(!b) return;
    const [gKey, iKey] = b.getAttribute('data-improve').split(':');
    const group = Model.find(x=>x.key===gKey); if(!group) return;
    const item  = group.items.find(x=>x.id===iKey); if(!item) return;

    mTitle.textContent = item.label;
    proTips.innerHTML  = (item.tips(lastData, ctx)||[]).map(t=>`<li>• ${esc(t)}</li>`).join('') || '<li>No tips available.</li>';

    // Build Google queries
    const queries = (item.queries? item.queries(ctx):[]) .filter(Boolean);
    gLinks.innerHTML = queries.map(q=>`<a target="_blank" rel="noopener" href="https://www.google.com/search?q=${encodeURIComponent(q)}" class="text-[#a8e4ff] underline mr-3">Search: ${esc(q)}</a>`).join('');

    // Try Google Autocomplete (gracefully fallback)
    gIdeas.innerHTML = '<li class="text-[var(--muted)]">Fetching ideas…</li>';
    const ideas = await googleSuggest(queries[0] || (ctx.kw||''));
    if (ideas.length){
      gIdeas.innerHTML = ideas.slice(0,8).map(i=>`<li>• ${esc(i)}</li>`).join('');
    } else {
      gIdeas.innerHTML = `<li class="text-[var(--muted)]">No live suggestions. Use the search links above or apply the Pro Tips.</li>`;
    }

    backdrop.classList.add('show');
  });

  async function googleSuggest(q){
    if(!q) return [];
    try{
      const url = 'https://suggestqueries.google.com/complete/search?client=firefox&q='+encodeURIComponent(q);
      const r = await fetch(url, { method:'GET' });
      const j = await r.json(); // format: [q, [suggestion1, suggestion2, ...]]
      return Array.isArray(j?.[1]) ? j[1] : [];
    }catch(_){ return []; }
  }

  /* ---------- Submit ---------- */
  form.addEventListener('submit', async (e)=>{
    e.preventDefault(); hideError(); setLoading(true);
    try{
      const fd = new FormData(form);
      ctx = { url:(fd.get('url')||'').trim(), kw:(fd.get('target_keyword')||'').trim() };

      const res = await fetch('/api/semantic-analyze', {
        method:'POST',
        headers:{ 'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify({ url: ctx.url, target_keyword: ctx.kw })
      });
      if(!res.ok){
        let m = `HTTP ${res.status}`;
        try{ const j=await res.json(); if(j?.error) m=j.error; }catch(_){}
        showError(m); setLoading(false); return;
      }
      const data = await res.json();
      if(!data?.ok){ showError(data?.error||'Analysis failed'); setLoading(false); return; }
      lastData = data;

      // Show panel
      result.classList.remove('hidden');

      // Overall + Stats
      setWheel(data.overall_score);
      const r = Number(data.content_structure?.readability_flesch||0); setReadability(r);

      ratio.textContent = (data.content_structure?.text_to_html_ratio ?? '—') + (data.content_structure?.text_to_html_ratio ? '%' : '');
      internal.textContent = data.technical_seo?.links?.internal ?? 0;
      external.textContent = data.technical_seo?.links?.external ?? 0;
      ttl.textContent = data.content_structure?.title || '—';
      desc.textContent = data.content_structure?.meta_description || '—';
      imgAlt.textContent = `${data.technical_seo?.image_alt_missing ?? 0} / ${data.technical_seo?.image_count ?? 0}`;

      renderHeadings(data.content_structure?.headings || {});
      renderCategories(data);

      // Recs
      recs.innerHTML = '';
      (data.recommendations||[]).forEach(r=>{
        const sev=(r.severity||'').toLowerCase();
        const c = sev==='critical' ? 'bad' : sev==='warning' ? 'warn' : 'na';
        const li=document.createElement('li');
        li.innerHTML = `<span class="badge ${c} mr-2">${esc(r.severity||'Info')}</span>${esc(r.text||'')}`;
        recs.appendChild(li);
      });

      // Anchors
      anchors.innerHTML='';
      (data.technical_seo?.links?.anchors||[]).slice(0,100).forEach(a=>{
        const p=document.createElement('p');
        p.textContent = `[${a.type||'link'}] ${a.text||'(no text)'} → ${a.href||''}`;
        anchors.appendChild(p);
      });

    }catch(err){
      console.error(err); showError(err?.message||'Network error');
    }finally{ setLoading(false); }
  });
</script>
@endsection
