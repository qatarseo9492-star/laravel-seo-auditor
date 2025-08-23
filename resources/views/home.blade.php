{{-- resources/views/home.blade.php --}}
@extends('layouts.app')

@section('title', 'Semantic SEO Master • Ultra Tech Global')
@section('hero', 'Semantic SEO Master Analyzer')

@push('head')
  <style>
    .analyzer{margin-top:20px;background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03));
      border:1px solid rgba(255,255,255,.1);border-radius:22px;box-shadow:var(--shadow);padding:22px}
    .section-title{font-size:1.65rem;margin:0 0 .3rem}
    .section-subtitle{margin:0;color:var(--text-dim)}
    .score-area{display:grid;grid-template-columns:260px 1fr;gap:1.1rem;align-items:center;margin-top:.6rem}
    .badges{display:flex;gap:.5rem;flex-wrap:wrap}
    .analyze-box{margin-top:12px;background:rgba(255,255,255,.03);border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:14px}
    .analyze-form input[type="url"]{
      width:100%; padding:1rem 1.2rem; border-radius:14px; border:1px solid #1b1b35; background:#0b0d21; color:var(--text);
      box-shadow:0 0 0 0 rgba(141,105,255,.0); transition:.25s;
    }
    .analyze-form input[type="url"]:focus{ outline:none; border-color:#5a47ff; box-shadow:0 0 0 6px rgba(141,105,255,.18); }
    .analyze-row{display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center;margin-top:.55rem}
    .progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px}
    .progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
    .progress-fill{height:100%;background:linear-gradient(135deg,#8d69ff,#ff3b5c);width:0%;transition:width .35s ease}
    .progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}
    .analyzer-grid{margin-top:1.1rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
    .category-card{position:relative;grid-column:span 6;background:linear-gradient(180deg,rgba(255,255,255,.05),rgba(255,255,255,.03));
      border:1px solid rgba(255,255,255,.08);border-radius:18px;padding:16px;box-shadow:var(--shadow);
      overflow:hidden;isolation:isolate;}
    .category-card::before{content:"";position:absolute;inset:-2px;border-radius:18px;padding:2px;
      background:conic-gradient(from 180deg, rgba(54,230,255,.35), rgba(141,105,255,.35), rgba(255,182,72,.30), rgba(255,59,92,.30), rgba(34,197,94,.30), rgba(54,230,255,.35));
      -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
      -webkit-mask-composite:xor;mask-composite:exclude;animation:borderGlow 7s linear infinite; pointer-events:none;}
    @keyframes borderGlow{0%{filter:hue-rotate(0)}100%{filter:hue-rotate(360deg)}}
    .category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
    .category-icon{width:46px;height:46px;border-radius:12px;display:inline-flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#36e6ff33,#8d69ff33);color:#fff;font-size:1.1rem;border:1px solid rgba(255,255,255,.18)}
    .category-title{margin:0;font-size:1.08rem;background:linear-gradient(90deg,#36e6ff,#8d69ff,#ff3b5c);-webkit-background-clip:text;-webkit-text-fill-color:transparent;font-weight:900}
    .category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.96rem}
    .checklist{list-style:none;margin:10px 0 0;padding:0}
    .checklist-item{--accent: rgba(255,255,255,.12); position:relative; display:grid;grid-template-columns:1fr auto auto auto;gap:.6rem;align-items:center; padding:.75rem .8rem .75rem 1rem;border-radius:14px; border:1px solid rgba(255,255,255,.10); background:linear-gradient(180deg,rgba(255,255,255,.035),rgba(255,255,255,.03)); overflow:hidden;}
    .checklist-item + .checklist-item{margin-top:.28rem}
    .checklist-item::after{content:""; position:absolute; left:0; top:0; bottom:0; width:6px; background:var(--accent); box-shadow:0 0 20px var(--accent); transition:.25s;}
    .checklist-item:hover{transform:translateY(-2px);box-shadow:0 10px 34px rgba(0,0,0,.28);border-color:rgba(255,255,255,.16)}
    .checklist-item label { cursor:pointer; display:inline-flex; align-items:center; gap:.6rem; }
    .checklist-item input[type="checkbox"]{appearance:none;width:42px;height:24px;border-radius:999px;background:#2a2a46;border:1px solid rgba(255,255,255,.18);position:relative;cursor:pointer;outline:none;transition:.2s}
    .checklist-item input[type="checkbox"]::after{content:"";position:absolute;width:18px;height:18px;border-radius:50%;background:#cfd3f6;top:2.5px;left:2.5px;transition:.2s;box-shadow:0 2px 10px rgba(0,0,0,.3)}
    .checklist-item input[type="checkbox"]:checked{background:linear-gradient(135deg,#36e6ff,#8d69ff)}
    .checklist-item input[type="checkbox"]:checked::after{left:21px;background:#0a1222}
    .score-badge{font-weight:900;font-size:.95rem;padding:.3rem .65rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);min-width:52px;text-align:center}
    .score-good{background:rgba(34,197,94,.22); border-color:rgba(34,197,94,.45)}
    .score-mid{ background:rgba(245,158,11,.22); border-color:rgba(245,158,11,.45)}
    .score-bad{ background:rgba(239,68,68,.24); border-color:rgba(239,68,68,.5)}
    .improve-btn{padding:.4rem .75rem;border-radius:999px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.06);font-weight:900;cursor:pointer}
    .improve-btn:hover{background:rgba(255,255,255,.1)}
    @media (max-width:992px){ .category-card{grid-column:span 12} .score-area{grid-template-columns:1fr} }
  </style>
@endpush

@section('content')
  <section class="analyzer" id="analyzer">
    <h2 class="section-title" data-i="analyze_title">Analyze a URL</h2>
    <p class="section-subtitle" data-i="legend_line">
      The wheel fills with your overall score.
      <span class="chip" style="background:rgba(34,197,94,.18)">Green ≥ 80</span>
      <span class="chip" style="background:rgba(245,158,11,.18)">Orange 60–79</span>
      <span class="chip" style="background:rgba(239,68,68,.18)">Red &lt; 60</span>
    </p>

    <div class="score-area">
      {{-- Reusable wheel component --}}
      <x-score-wheel id="overall" :value="0" :size="260" ticks="20" />

      <div style="display:flex;flex-direction:column;gap:.6rem">
        <div class="badges">
          <span class="chip">Overall: <b id="overallScoreInline">0</b>/100</span>
          <span class="chip" id="aiBadge">Writer: <b>—</b></span>
          <button id="viewAIText" class="btn btn-neon" style="--pad:.5rem .8rem"><i class="fa-solid fa-robot"></i> Evidence</button>
        </div>
        <div class="badges">
          <button id="viewHumanBtn" class="btn btn-ghost" style="--pad:.4rem .7rem"><i class="fa-solid fa-user"></i> Human‑like: <b id="humanPct">—</b>%</button>
          <button id="viewAIBtn" class="btn btn-ghost" style="--pad:.4rem .7rem"><i class="fa-solid fa-microchip"></i> AI‑like: <b id="aiPct">—</b>%</button>
        </div>
      </div>
    </div>

    <div class="analyze-box">
      <form id="analyzeForm" class="analyze-form" onsubmit="return false;">
        <label for="analyzeUrl" style="display:block;font-weight:800;margin-bottom:.35rem" data-i="page_url">Page URL</label>
        <input id="analyzeUrl" name="url" type="url" inputmode="url" autocomplete="url" placeholder="https://example.com/page or example.com/page" />
        <div class="analyze-row">
          <div style="display:flex;align-items:center;gap:.6rem">
            <label style="display:inline-flex;align-items:center;gap:.45rem;cursor:pointer">
              <input id="autoApply" type="checkbox" checked style="accent-color:var(--primary)"> <span data-i="auto_check">Auto‑apply checkmarks (≥ 80)</span>
            </label>
          </div>
          <button id="analyzeBtn" class="btn btn-danger" type="button"><i class="fa-solid fa-magnifying-glass"></i> <span data-i="analyze">Analyze</span></button>
          <button class="btn btn-neon" id="printChecklist" type="button"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
          <button class="btn btn-ghost" id="resetChecklist" type="button"><i class="fa-solid fa-rotate"></i> <span data-i="reset">Reset</span></button>
        </div>
        <div id="analyzeStatus" style="margin-top:.45rem;color:var(--text-dim)"></div>
      </form>

      <div id="analyzeReport" style="margin-top:.9rem;display:none">
        <div style="display:flex;flex-wrap:wrap;gap:.5rem">
          <span class="chip">HTTP: <b id="rStatus">—</b></span>
          <span class="chip">Title: <b id="rTitleLen">—</b></span>
          <span class="chip">Meta desc: <b id="rMetaLen">—</b></span>
          <span class="chip">Canonical: <b id="rCanonical">—</b></span>
          <span class="chip">Robots: <b id="rRobots">—</b></span>
          <span class="chip">Viewport: <b id="rViewport">—</b></span>
          <span class="chip">H1/H2/H3: <b id="rHeadings">—</b></span>
          <span class="chip">Internal links: <b id="rInternal">—</b></span>
          <span class="chip">Schema: <b id="rSchema">—</b></span>
          <span class="chip">Auto‑checked: <b id="rAutoCount">—</b></span>
        </div>
      </div>
    </div>

    <div class="progress-wrap">
      <div class="progress-bar"><div class="progress-fill" id="progressBar"></div></div>
      <div id="progressCaption" class="progress-caption">0 of 25 items completed</div>
    </div>

    {{-- Checklist --}}
    @php $labels = [
      1=>'Define search intent & primary topic',
      2=>'Map target & related keywords (synonyms/PAA)',
      3=>'H1 includes primary topic naturally',
      4=>'Integrate FAQs / questions with answers',
      5=>'Readable, NLP‑friendly language',
      6=>'Title tag (≈50–60 chars) w/ primary keyword',
      7=>'Meta description (≈140–160 chars) + CTA',
      8=>'Canonical tag set correctly',
      9=>'Indexable & listed in XML sitemap',
      10=>'E‑E‑A‑T signals (author, date, expertise)',
      11=>'Unique value vs. top competitors',
      12=>'Facts & citations up to date',
      13=>'Helpful media (images/video) w/ captions',
      14=>'Logical H2/H3 headings & topic clusters',
      15=>'Internal links to hub/related pages',
      16=>'Clean, descriptive URL slug',
      17=>'Breadcrumbs enabled (+ schema)',
      18=>'Mobile‑friendly, responsive layout',
      19=>'Optimized speed (compression, lazy‑load)',
      20=>'Core Web Vitals passing (LCP/INP/CLS)',
      21=>'Clear CTAs and next steps',
      22=>'Primary entity clearly defined',
      23=>'Related entities covered with context',
      24=>'Valid schema markup (Article/FAQ/Product)',
      25=>'sameAs/Organization details present'
    ]; @endphp

    <div class="analyzer-grid">
      @foreach ([
        ['Content & Keywords',1,5,'fa-pen-nib'],
        ['Technical Elements',6,9,'fa-code'],
        ['Content Quality',10,13,'fa-star'],
        ['Structure & Architecture',14,17,'fa-sitemap'],
        ['User Signals & Experience',18,21,'fa-user-check'],
        ['Entities & Context',22,25,'fa-database'],
      ] as $c)
        <article class="category-card">
          <header class="category-head">
            <span class="category-icon"><i class="fas {{ $c[3] }}"></i></span>
            <div>
              <h3 class="category-title">{{ $c[0] }}</h3>
              <p class="category-sub">—</p>
            </div>
            <span class="chip"><span class="checked-count">0</span>/<span class="total-count">{{ $c[2]-$c[1]+1 }}</span></span>
          </header>
          <ul class="checklist">
            @for($i=$c[1];$i<=$c[2];$i++)
              <li class="checklist-item" id="row-{{ $i }}" data-score="">
                <label>
                  <input type="checkbox" id="ck-{{ $i }}">
                  <span>{{ $labels[$i] }}</span>
                </label>
                <span class="score-badge" id="sc-{{ $i }}">—</span>
                <button class="improve-btn" type="button" data-id="ck-{{ $i }}">Improve</button>
              </li>
            @endfor
          </ul>
        </article>
      @endforeach
    </div>
  </section>

  {{-- Modal --}}
  <div class="modal-backdrop" id="modalBackdrop"></div>
  <div class="modal" id="tipModal" role="dialog" aria-modal="true" aria-labelledby="modalTitle" style="display:none">
    <div class="modal-card">
      <div class="modal-header">
        <h3 class="modal-title" id="modalTitle">Improve</h3>
        <button class="modal-close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
      </div>
      <div class="tabs">
        <button class="tab active" data-tab="tipsTab"><i class="fa-solid fa-lightbulb"></i> Tips</button>
        <button class="tab" data-tab="examplesTab"><i class="fa-brands fa-google"></i> Examples</button>
        <button class="tab" data-tab="humanTab"><i class="fa-solid fa-user"></i> Human‑like</button>
        <button class="tab" data-tab="aiTab"><i class="fa-solid fa-microchip"></i> AI‑like</button>
        <button class="tab" data-tab="fullTab"><i class="fa-solid fa-file-lines"></i> Full Text</button>
      </div>
      <div class="tabpanes">
        <div id="tipsTab" class="active"><ul id="modalList"></ul></div>
        <div id="examplesTab"><div class="pre" id="examplesPre">—</div></div>
        <div id="humanTab"><div class="pre" id="humanSnippetsPre">Run Analyze to view human‑like snippets.</div></div>
        <div id="aiTab"><div class="pre" id="aiSnippetsPre">Run Analyze to view AI‑like snippets.</div></div>
        <div id="fullTab"><div class="pre" id="fullTextPre">Run Analyze to load full text.</div></div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  /* --- Minimal i18n for this page --- */
  const I18N = { en:{analyze_title:"Analyze a URL", page_url:"Page URL", analyze:"Analyze", print:"Print", reset:"Reset", auto_check:"Auto‑apply checkmarks (≥ 80)"} };
  (function(){
    const dockBtn=document.getElementById('langOpen'), panel=document.getElementById('langPanel'), card=document.getElementById('langCard');
    function fill(){ card.innerHTML=''; [['en','English']].forEach(([c,l])=>{ const d=document.createElement('div'); d.className='lang-item'; d.dataset.code=c; d.innerHTML=`<span class="lang-flag" style="width:18px;height:14px;border-radius:2px;background:#888"></span><strong>${l}</strong>`; card.appendChild(d); }); }
    function apply(){ /* default EN already in DOM */ }
    dockBtn.addEventListener('click', ()=> panel.style.display = panel.style.display==='block' ? 'none' : 'block');
    panel.addEventListener('click', ()=> panel.style.display='none');
    fill(); apply();
  })();

  /* --- Checklist storage & pills --- */
  (function(){
    const STORAGE_KEY='semanticSeoChecklistV7';
    const total=25;
    const boxes=()=>Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]'));
    const bar=document.getElementById('progressBar');
    const caption=document.getElementById('progressCaption');

    function setRowAccent(rowEl, score){
      if(!rowEl) return;
      let col='rgba(255,255,255,.12)';
      if(typeof score==='number'){
        if(score>=80) col='rgba(34,197,94,.6)';
        else if(score>=60) col='rgba(245,158,11,.6)';
        else col='rgba(239,68,68,.6)';
        rowEl.dataset.score=String(score);
      } else { rowEl.dataset.score=''; }
      rowEl.style.setProperty('--accent', col);
    }
    function updateCats(){
      document.querySelectorAll('.category-card').forEach(card=>{
        const all=card.querySelectorAll('input[type="checkbox"]');
        const done=card.querySelectorAll('input[type="checkbox"]:checked');
        card.querySelector('.checked-count').textContent=done.length;
        card.querySelector('.total-count').textContent=all.length;
      });
    }
    function update(){
      const checked=boxes().filter(cb=>cb.checked).length;
      bar.style.width=((checked/total)*100)+'%';
      caption.textContent=`${checked} of ${total} items completed`;
      updateCats();
    }
    function load(){
      try{const saved=JSON.parse(localStorage.getItem(STORAGE_KEY)||'[]'); boxes().forEach(cb=>cb.checked=saved.includes(cb.id));}catch(e){}
      update();
    }
    function save(){
      const ids=boxes().filter(cb=>cb.checked).map(cb=>cb.id);
      localStorage.setItem(STORAGE_KEY, JSON.stringify(ids));
    }
    document.addEventListener('change', e=>{ if(e.target.matches('#analyzer input[type="checkbox"]')){ update(); save(); }});
    document.getElementById('resetChecklist').addEventListener('click', ()=>{
      if(!confirm('Reset the checklist?')) return;
      localStorage.removeItem(STORAGE_KEY);
      boxes().forEach(cb=>cb.checked=false);
      for(let i=1;i<=25;i++){ setScoreBadge(i,null); setRowAccent(document.getElementById('row-'+i), null); }
      setScoreWheel('overall', 0); update();
    });
    document.getElementById('printChecklist').addEventListener('click', ()=>window.print());
    document.getElementById('printTop').addEventListener('click', ()=>window.print());

    window.setScoreBadge=(num,score)=>{
      const pill=document.getElementById('sc-'+num), row=document.getElementById('row-'+num);
      if(!pill) return;
      pill.className='score-badge';
      if(score==null || Number.isNaN(score)){ pill.textContent='—'; setRowAccent(row,null); return; }
      const s=Math.round(score);
      pill.textContent=s;
      if(s>=80) pill.classList.add('score-good'); else if(s>=60) pill.classList.add('score-mid'); else pill.classList.add('score-bad');
      setRowAccent(row,s);
    };

    load();
  })();

  /* --- Modal (tips/evidence panes) --- */
  (function(){
    const $=s=>document.querySelector(s), $$=s=>Array.from(document.querySelectorAll(s));
    const backdrop=$('#modalBackdrop'), modal=$('#tipModal'), closeBtn=$('#modalClose');
    const panes={ tipsTab:$('#tipsTab'), examplesTab:$('#examplesTab'), humanTab:$('#humanTab'), aiTab:$('#aiTab'), fullTab:$('#fullTab') };
    const tabs=$$('.tab');
    function openM(){backdrop.style.display='block'; modal.style.display='flex';}
    function closeM(){backdrop.style.display='none'; modal.style.display='none';}
    closeBtn.addEventListener('click',closeM); backdrop.addEventListener('click',closeM); document.addEventListener('keydown',e=>{if(e.key==='Escape') closeM();});
    tabs.forEach(t=>t.addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); Object.values(panes).forEach(p=>p.classList.remove('active')); t.classList.add('active'); panes[t.dataset.tab].classList.add('active'); }));
    document.addEventListener('click', e=>{
      const btn=e.target.closest('.improve-btn'); if(!btn) return;
      const id=btn.dataset.id; const label=(document.getElementById(id)?.parentElement?.querySelector('span')?.textContent||'Improve').trim();
      document.getElementById('modalTitle').textContent='Improve: '+label;
      const tipsList=document.getElementById('modalList');
      const tips=(window.__lastSuggestions && Array.isArray(window.__lastSuggestions[id])) ? window.__lastSuggestions[id] : ['Run Analyze to generate contextual suggestions.'];
      tipsList.innerHTML=''; tips.forEach(t=>{const li=document.createElement('li'); li.textContent=t; tipsList.appendChild(li);});
      tabs.forEach(x=>x.classList.remove('active')); panes.tipsTab.classList.add('active'); document.querySelector('[data-tab="tipsTab"]').classList.add('active');
      Object.values(panes).forEach(p=>p.classList.remove('active')); panes.tipsTab.classList.add('active');
      openM();
    });
    document.getElementById('viewAIText').addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="aiTab"]').classList.add('active'); Object.values(panes).forEach(p=>p.classList.remove('active')); panes.aiTab.classList.add('active'); openM();});
    document.getElementById('viewHumanBtn').addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="humanTab"]').classList.add('active'); Object.values(panes).forEach(p=>p.classList.remove('active')); panes.humanTab.classList.add('active'); openM();});
    document.getElementById('viewAIBtn').addEventListener('click',()=>{tabs.forEach(x=>x.classList.remove('active')); document.querySelector('[data-tab="aiTab"]').classList.add('active'); Object.values(panes).forEach(p=>p.classList.remove('active')); panes.aiTab.classList.add('active'); openM();});
    window.__setAIData=function(ai){
      document.getElementById('aiSnippetsPre').textContent=(ai?.ai_sentences||[]).join('\n\n')||'No AI‑like snippets detected.';
      document.getElementById('humanSnippetsPre').textContent=(ai?.human_sentences||[]).join('\n\n')||'No human‑like snippets isolated.';
      document.getElementById('fullTextPre').textContent=ai?.full_text||'No text captured.';
      document.getElementById('aiPct').textContent=typeof ai?.ai_pct==='number'?ai.ai_pct:'—';
      document.getElementById('humanPct').textContent=typeof ai?.human_pct==='number'?ai.human_pct:'—';
    }
  })();

  /* --- Analyze flow: call your route and update wheel --- */
  function normalizeUrl(u){ if(!u) return ''; u=u.trim(); if(!/^https?:\/\//i.test(u)) u='https://'+u.replace(/^\/+/, ''); try{ new URL(u);}catch(e){} return u; }

  (function(){
    const $=s=>document.querySelector(s);
    function setChecked(id,on){ const el=document.getElementById(id); if(el){ el.checked=!!on; el.classList.toggle('autoPulse', !!on); } }

    document.getElementById('analyzeForm').addEventListener('submit', e=>{ e.preventDefault(); document.getElementById('analyzeBtn').click(); });
    document.getElementById('analyzeBtn').addEventListener('click', analyze);

    async function analyze(){
      const url=normalizeUrl($('#analyzeUrl').value);
      const status=$('#analyzeStatus'), btn=$('#analyzeBtn'), report=$('#analyzeReport');
      if(!url){ status.textContent='Please enter a URL.'; return; }
      status.textContent='Analyzing…'; btn.disabled=true; btn.innerHTML='<i class="fa-solid fa-spinner fa-spin"></i> Analyzing';

      try{
        const resp=await fetch('{{ route('analyze.json') }}', {
          method:'POST',
          headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},
          body:JSON.stringify({url})
        });
        const data=await resp.json();
        if(!data.ok) throw new Error(data.error||'Failed');

        // meta chips
        $('#rStatus').textContent=data.status;
        $('#rTitleLen').textContent=(data.title||'').length;
        $('#rMetaLen').textContent=data.meta_description_len;
        $('#rCanonical').textContent=data.canonical?'Yes':'No';
        $('#rRobots').textContent=data.robots||'—';
        $('#rViewport').textContent=data.viewport?'Yes':'No';
        $('#rHeadings').textContent=`${data.counts.h1}/${data.counts.h2}/${data.counts.h3}`;
        $('#rInternal').textContent=data.counts.internal_links;
        $('#rSchema').textContent=(data.schema.found_types||[]).slice(0,6).join(', ')||'—';
        report.style.display='block';

        // suggestions blob for modal
        window.__lastSuggestions = (data.suggestions && typeof data.suggestions==='object') ? data.suggestions : {};

        // per‑item pills + auto‑check
        let autoCount=0;
        for(let i=1;i<=25;i++){
          const key='ck-'+i, row=document.getElementById('row-'+i);
          const score=(data.scores && typeof data.scores[key]==='number') ? data.scores[key] : null;
          setScoreBadge(i, score);
          if($('#autoApply').checked){
            if(typeof score==='number' && score>=80){ setChecked(key,true); autoCount++; }
            else { setChecked(key,false); }
          }
        }
        $('#rAutoCount').textContent=autoCount.toString();
        document.dispatchEvent(new Event('change')); // update progress bar

        // 🌟 update the wheel via global API
        const overall = (typeof data.overall_score==='number') ? data.overall_score : 0;
        setScoreWheel('overall', overall); // <— single call, as requested
        document.getElementById('overallScoreInline').textContent = Math.round(overall);

        // AI/Human badge
        const ai=data.ai_detection||{};
        const labelMap={likely_human:'Likely Human', mixed:'Mixed', likely_ai:'Likely AI', unknown:'Unknown'};
        const parts=[`<b>${labelMap[ai.label]||'Unknown'}</b>`];
        if(typeof ai.human_pct==='number') parts.push(`Human ${ai.human_pct}%`);
        if(typeof ai.ai_pct==='number')    parts.push(`AI ${ai.ai_pct}%`);
        document.getElementById('aiBadge').innerHTML='Writer: '+parts.join(' • ');
        window.__setAIData(ai);

        status.textContent = overall>=80 ? 'Great! You passed—keep going.' : (overall<60 ? 'Score is low — optimize and re‑Analyze.' : 'Solid! Improve a few items to hit green.');
        setTimeout(()=> status.textContent='', 4200);

      }catch(e){
        $('#analyzeStatus').textContent='Error: '+e.message;
      }finally{
        btn.disabled=false; btn.innerHTML='<i class="fa-solid fa-magnifying-glass"></i> Analyze';
      }
    }
  })();
</script>
@endpush
