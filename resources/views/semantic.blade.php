@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@section('content')
<style>
  :root{
    /* Coolors palette */
    --c1:#006466; --c2:#065a60; --c3:#0b525b; --c4:#144552; --c5:#1b3a4b;
    --c6:#212f45; --c7:#272640; --c8:#312244; --c9:#3e1f47; --c10:#4d194d;

    --ink:#0b1220; --muted:#9aa4b2; --card:#111827; --glass:rgba(255,255,255,.06);
  }
  .bg-app{ background: radial-gradient(1000px 700px at 20% -10%, rgba(77,25,77,.18), transparent 55%),
                         radial-gradient(900px 600px at 110% 10%, rgba(0,100,102,.18), transparent 55%),
                         linear-gradient(180deg, var(--c7), var(--c8) 55%, var(--c9)); }
  .glass { background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.03)); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(8px); }
  .soft-shadow{ box-shadow: 0 10px 40px rgba(0,0,0,.35); }
  .pill{ font-size:.7rem; padding:.22rem .55rem; border-radius:.5rem; border:1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.06); }

  /* Score wheel */
  .wheel{ --v:0; width:170px; height:170px; border-radius:50%;
    background:
      conic-gradient(#22c55e calc(var(--v)*1%), transparent 0),
      conic-gradient(#f59e0b 0 0),
      conic-gradient(#ef4444 0 0);
    position:relative;
  }
  .wheel::before{ content:""; position:absolute; inset:8px; border-radius:50%; background:linear-gradient(180deg, #0f172a, #0b1325); border:1px solid rgba(255,255,255,.06); }
  .wheel.badge-green{ background:
      conic-gradient(#22c55e calc(var(--v)*1%), rgba(34,197,94,.15) 0);
  }
  .wheel.badge-orange{ background:
      conic-gradient(#f59e0b calc(var(--v)*1%), rgba(245,158,11,.15) 0);
  }
  .wheel.badge-red{ background:
      conic-gradient(#ef4444 calc(var(--v)*1%), rgba(239,68,68,.15) 0);
  }
  .wheel .num{ position:absolute; inset:0; display:grid; place-items:center; font-weight:900; font-size:2.8rem; letter-spacing:-1px; }
  .score-chip{ font-size:.70rem; padding:.2rem .5rem; border-radius:.5rem; }
  .score-green{ color:#22c55e; background:rgba(34,197,94,.1); border:1px solid rgba(34,197,94,.25); }
  .score-orange{ color:#f59e0b; background:rgba(245,158,11,.1); border:1px solid rgba(245,158,11,.25); }
  .score-red{ color:#ef4444; background:rgba(239,68,68,.1); border:1px solid rgba(239,68,68,.25); }
  .score-neutral{ color:#93c5fd; background:rgba(59,130,246,.08); border:1px solid rgba(59,130,246,.2); }

  .btn-cta{ background: linear-gradient(135deg, #38bdf8, #8b5cf6); color:#fff; }
  .btn-cta:hover{ filter:brightness(1.05); }

  .btn-analyze{
    --tw-ring-color: rgba(56,189,248,.35);
    background: linear-gradient(135deg, #06b6d4, #22c55e);
    color:#051016; font-weight:700; border:1px solid rgba(255,255,255,.15);
  }
  .btn-analyze:hover{ filter:brightness(1.05); }
</style>

<div class="bg-app min-h-screen text-slate-100">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

    <!-- Page Head -->
    <div class="flex items-center justify-between">
      <div>
        <span class="pill">Analyzer 2.0</span>
        <h1 class="mt-3 text-4xl sm:text-5xl font-extrabold tracking-tight">Semantic SEO Master</h1>
        <p class="mt-2 text-slate-300">Analyze any page’s semantic structure, technical elements, and content quality—then get precise improvements.</p>
      </div>
    </div>

    <!-- Analyze Bar -->
    <form id="semanticForm" class="mt-8 glass rounded-2xl soft-shadow">
      <div class="grid md:grid-cols-[1fr,320px,150px] gap-2 p-2">
        <div class="flex items-center gap-2 bg-black/30 rounded-xl px-3 py-2 border border-white/10">
          <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.4" d="m21 21-4.3-4.3M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16Z"/></svg>
          <input name="url" type="url" required placeholder="Paste a URL (e.g. https://example.com/page)" class="w-full bg-transparent outline-none placeholder:text-slate-400">
        </div>
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)" class="w-full bg-black/30 rounded-xl px-3 py-2 border border-white/10 outline-none placeholder:text-slate-400">
        <button id="btnAnalyze" class="btn-analyze rounded-xl px-4 py-2 flex items-center justify-center gap-2">
          <span class="hidden sm:inline">Analyze URL</span>
          <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.8" d="M5 12h14M12 5l7 7-7 7"/></svg>
        </button>
      </div>
      <p class="px-4 pb-4 text-xs text-slate-400">We’ll fetch the page and compute a detailed semantic report.</p>
    </form>

    <!-- Results -->
    <section id="results" class="mt-10 hidden space-y-8">

      <!-- Score + Quick stats -->
      <div class="grid lg:grid-cols-[260px,1fr] gap-6">
        <div class="glass rounded-2xl p-6 border border-white/10 soft-shadow flex flex-col items-center justify-center">
          <div id="wheel" class="wheel badge-red" style="--v:0;">
            <div class="num" id="scoreNum">0</div>
          </div>
          <div id="wheelBadge" class="mt-4 text-center text-sm text-slate-300">—</div>
        </div>

        <div class="glass rounded-2xl p-6 border border-white/10 soft-shadow">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Quick Stats</h3>
            <span id="readabilityChip" class="score-chip score-neutral">Readability —</span>
          </div>
          <div class="mt-4 grid sm:grid-cols-3 lg:grid-cols-6 gap-3 text-sm">
            <div class="rounded-xl bg-black/30 border border-white/10 p-4">
              <div class="text-slate-400">Words</div>
              <div id="statWords" class="text-2xl font-bold">—</div>
            </div>
            <div class="rounded-xl bg-black/30 border border-white/10 p-4">
              <div class="text-slate-400">Images</div>
              <div id="statImages" class="text-2xl font-bold">—</div>
            </div>
            <div class="rounded-xl bg-black/30 border border-white/10 p-4">
              <div class="text-slate-400">Internal</div>
              <div id="statInternal" class="text-2xl font-bold">—</div>
            </div>
            <div class="rounded-xl bg-black/30 border border-white/10 p-4">
              <div class="text-slate-400">External</div>
              <div id="statExternal" class="text-2xl font-bold">—</div>
            </div>
            <div class="rounded-xl bg-black/30 border border-white/10 p-4">
              <div class="text-slate-400">Readability</div>
              <div id="statRead" class="text-2xl font-bold">—</div>
            </div>
            <div class="rounded-xl bg-black/30 border border-white/10 p-4">
              <div class="text-slate-400">Text/HTML %</div>
              <div id="statRatio" class="text-2xl font-bold">—</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Structure -->
      <div class="grid lg:grid-cols-2 gap-6">
        <div class="glass rounded-2xl p-6 border border-white/10 soft-shadow">
          <h3 class="text-lg font-semibold">Content Structure</h3>
          <dl class="mt-4 space-y-2 text-sm">
            <div><dt class="text-slate-400">Title</dt><dd id="titleVal" class="font-medium text-slate-100">—</dd></div>
            <div><dt class="text-slate-400">Meta description</dt><dd id="metaVal" class="font-medium text-slate-100">—</dd></div>
          </dl>
          <div class="mt-5">
            <div class="text-sm text-slate-400 mb-2">Heading Map</div>
            <div id="headingMap" class="grid sm:grid-cols-2 gap-2"></div>
          </div>
        </div>

        <div class="glass rounded-2xl p-6 border border-white/10 soft-shadow">
          <h3 class="text-lg font-semibold">Recommendations</h3>
          <ul id="recs" class="mt-3 space-y-2 text-sm"></ul>
          <div class="mt-6 text-xs text-slate-400">Severity legend: <span class="text-red-300">Critical</span> · <span class="text-amber-300">Warning</span> · <span class="text-slate-300">Info</span></div>
        </div>
      </div>

      <!-- Categories & Checklists -->
      <div id="categoriesWrap" class="grid md:grid-cols-2 xl:grid-cols-3 gap-6"></div>

    </section>
  </div>
</div>

<!-- Improve Modal -->
<div id="improveModal" class="hidden fixed inset-0 z-50">
  <div class="absolute inset-0 bg-black/60"></div>
  <div class="relative max-w-xl mx-auto mt-28 glass rounded-2xl border border-white/10 p-6 soft-shadow">
    <div class="flex items-start justify-between gap-4">
      <h4 id="improveTitle" class="text-lg font-semibold">Improve</h4>
      <button id="improveClose" class="text-slate-300 hover:text-white">
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.6" d="M6 6l12 12M6 18L18 6"/></svg>
      </button>
    </div>
    <p id="improveAdvice" class="mt-3 text-slate-200 text-sm">—</p>
    <div class="mt-5">
      <a id="improveLink" href="#" target="_blank" rel="nofollow noopener" class="btn-cta inline-flex items-center gap-2 px-4 py-2 rounded-xl">
        Search on Google
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-width="1.7" d="M7 17L17 7M9 7h8v8"/></svg>
      </a>
    </div>
  </div>
</div>

<script>
  const $ = s => document.querySelector(s);
  const elForm = $('#semanticForm');
  const btnAnalyze = $('#btnAnalyze');
  const section = $('#results');
  const elWheel = $('#wheel');
  const elScore = $('#scoreNum');
  const elBadge = $('#wheelBadge');

  const stat = {
    words: $('#statWords'), images: $('#statImages'),
    internal: $('#statInternal'), external: $('#statExternal'),
    read: $('#statRead'), ratio: $('#statRatio'),
    readChip: $('#readabilityChip')
  };

  const structure = { title: $('#titleVal'), meta: $('#metaVal'), map: $('#headingMap'), recs: $('#recs') };
  const catsWrap = $('#categoriesWrap');

  function chipColor(score){
    if(score==null) return 'score-neutral';
    if(score>=80) return 'score-green';
    if(score>=60) return 'score-orange';
    return 'score-red';
  }
  function wheelColor(score){
    if(score>=80) return 'badge-green';
    if(score>=60) return 'badge-orange';
    return 'badge-red';
  }
  function headingBlock(level, arr){
    const bg = 'bg-black/30';
    const border = 'border border-white/10';
    return `<div class="rounded-xl ${bg} ${border} p-3">
      <div class="text-xs uppercase text-slate-400">${level}</div>
      <div class="mt-1 space-y-1">${(arr||[]).map(t=>`<div class="text-sm">• ${t}</div>`).join('')}</div>
    </div>`;
  }

  function openModal(title, advice, url){
    $('#improveTitle').textContent = title;
    $('#improveAdvice').textContent = advice;
    $('#improveLink').href = url || '#';
    $('#improveModal').classList.remove('hidden');
  }
  $('#improveClose').addEventListener('click', ()=> $('#improveModal').classList.add('hidden'));
  $('#improveModal').addEventListener('click', (e)=>{ if(e.target.id==='improveModal') e.currentTarget.classList.add('hidden'); });

  elForm.addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(elForm);
    const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') || null };
    btnAnalyze.disabled = true; btnAnalyze.classList.add('opacity-60');
    try{
      const res = await fetch('/api/semantic-analyze', {
        method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'},
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if(!data.ok){ alert(data.error || 'Analysis failed'); return; }
      // Show section
      section.classList.remove('hidden');

      // Wheel + badge
      const s = parseInt(data.overall_score||0,10);
      elWheel.style.setProperty('--v', s);
      elWheel.classList.remove('badge-red','badge-orange','badge-green');
      elWheel.classList.add(wheelColor(s));
      elScore.textContent = s;
      elBadge.textContent = (data.wheel && data.wheel.label) ? data.wheel.label
                         : (s>=80 ? 'Great Work — Well Optimized' : (s>=60 ? 'Needs Optimization' : 'Needs Significant Optimization'));

      // Stats
      stat.words.textContent   = (data.quick_stats?.word_count ?? '—');
      stat.images.textContent  = (data.quick_stats?.image_count ?? '—');
      stat.internal.textContent= (data.quick_stats?.internal_links ?? '—');
      stat.external.textContent= (data.quick_stats?.external_links ?? '—');
      const read = (data.quick_stats?.readability_flesch ?? null);
      stat.read.textContent    = (read ?? '—');
      stat.ratio.textContent   = (data.quick_stats?.text_to_html_ratio ?? '—');
      stat.readChip.textContent= `Readability ${read ?? '—'}`;
      stat.readChip.className  = `score-chip ${chipColor(read)}`;

      // Structure
      structure.title.textContent = data.content_structure?.title || '—';
      structure.meta.textContent  = data.content_structure?.meta_description || '—';
      const hs = data.content_structure?.headings || {};
      structure.map.innerHTML =
        headingBlock('H1', hs.h1) + headingBlock('H2', hs.h2) + headingBlock('H3', hs.h3) +
        headingBlock('H4', hs.h4) + headingBlock('H5', hs.h5) + headingBlock('H6', hs.h6);

      // Recs
      structure.recs.innerHTML = (data.recommendations||[]).map(r=>{
        const color = r.severity==='Critical' ? 'text-red-300' : (r.severity==='Warning' ? 'text-amber-300' : 'text-slate-300');
        return `<li class="rounded-lg bg-black/30 border border-white/10 px-3 py-2">
          <span class="${color} font-medium mr-2">${r.severity}</span>${r.text}
        </li>`;
      }).join('');

      // Categories
      catsWrap.innerHTML = '';
      (data.categories||[]).forEach(cat=>{
        const catScore = cat.score;
        const catColor = chipColor(catScore).replace('score-','');
        const headColor =
          catColor==='green' ? 'from-emerald-500/25 to-emerald-500/5' :
          catColor==='orange'? 'from-amber-500/25 to-amber-500/5' :
          catColor==='red'   ? 'from-rose-500/25 to-rose-500/5' : 'from-sky-500/20 to-sky-500/5';
        const catCard = document.createElement('div');
        catCard.className = 'glass rounded-2xl p-5 border border-white/10 soft-shadow flex flex-col';

        catCard.innerHTML = `
          <div class="rounded-xl bg-gradient-to-br ${headColor} border border-white/10 p-4 flex items-center justify-between">
            <div class="font-semibold">${cat.name}</div>
            <span class="score-chip ${chipColor(catScore)}">${catScore ?? '—'}</span>
          </div>
          <div class="mt-3 space-y-2" />
        `;
        const listWrap = catCard.querySelector('div.space-y-2');
        (cat.checks||[]).forEach(ch=>{
          const c = chipColor(ch.score);
          const passIcon = ch.pass===true ? '✅' : (ch.pass===false ? '⚠️' : '•');
          const row = document.createElement('div');
          row.className = 'rounded-xl bg-black/30 border border-white/10 p-3';
          row.innerHTML = `
            <div class="flex items-center justify-between gap-3">
              <div class="flex items-center gap-2 text-sm">
                <span class="opacity-90">${passIcon}</span>
                <span class="font-medium">${ch.label}</span>
              </div>
              <div class="flex items-center gap-2">
                <span class="score-chip ${c}">${ch.score ?? '—'}</span>
                <button data-title="${ch.label}" data-advice="${(ch.advice||'').replace(/"/g,'&quot;')}" data-url="${ch.improve_search_url||'#'}"
                  class="px-3 py-1 rounded-lg btn-cta text-xs">Improve</button>
              </div>
            </div>
          `;
          listWrap.appendChild(row);
        });
        catsWrap.appendChild(catCard);
      });

      // Delegate: Improve buttons
      catsWrap.querySelectorAll('button.btn-cta').forEach(b=>{
        b.addEventListener('click', (e)=>{
          const t = e.currentTarget;
          openModal(t.dataset.title || 'Improve', t.dataset.advice || 'Suggestions not available.', t.dataset.url || '#');
        });
      });

    }catch(err){
      console.error(err);
      alert('Network or server error.');
    }finally{
      btnAnalyze.disabled = false; btnAnalyze.classList.remove('opacity-60');
    }
  });
</script>
@endsection
