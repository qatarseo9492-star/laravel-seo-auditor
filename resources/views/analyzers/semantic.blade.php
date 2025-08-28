{{-- resources/views/analyzers/semantic.blade.php --}}
@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@section('content')
  {{-- Tailwind config extensions just for this page --}}
  <script>
    window.tailwind = window.tailwind || {};
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            // Elegant, trendy palette
            grape:  { 500:'#6d28d9', 600:'#5b21b6' },
            orchid: { 500:'#7c3aed' },
            plum:   { 500:'#4c1d95' },
            blush:  { 500:'#ec4899' },
            amber:  { 500:'#f59e0b' },
            mint:   { 500:'#10b981' },
            coal:   { 900:'#0b1020' },
          },
          boxShadow: {
            soft: '0 10px 30px rgba(0,0,0,.12)',
            glass:'inset 0 1px 0 rgba(255,255,255,.12), 0 8px 24px rgba(2,6,23,.18)'
          },
          borderRadius: { xl2:'1.25rem' }
        }
      }
    }
  </script>

  <style>
    /* Page surface (no animated bg) */
    .surface {
      background:
        radial-gradient(1200px 800px at 120% -10%, rgba(124,58,237,.15), transparent 60%),
        radial-gradient(1000px 700px at -20% 120%, rgba(236,72,153,.10), transparent 60%),
        linear-gradient(160deg, #0b1020 0%, #101426 40%, #0b1020 100%);
    }
    .glass {
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.02));
      backdrop-filter: blur(10px);
    }
    /* Score wheel styling */
    .gauge text { font-variant-numeric: tabular-nums; }
    .chip { font-size:.7rem; padding:.2rem .55rem; border-radius:999px; background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.15); }
    .btn-primary {
      @apply inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-orchid-500 to-blush-500 text-white font-medium shadow-soft hover:opacity-95;
    }
    .btn-ghost {
      @apply inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white/5 text-white hover:bg-white/10 border border-white/10;
    }
    .stat {
      @apply rounded-xl2 glass border border-white/10 p-5 shadow-glass;
    }
    .card {
      @apply rounded-xl2 bg-white border border-slate-200 shadow-soft;
    }
    .badge-pass { @apply bg-green-100 text-green-700; }
    .badge-warn { @apply bg-amber-100 text-amber-700; }
    .badge-fail { @apply bg-rose-100 text-rose-700; }
  </style>

  <div class="surface text-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">

      {{-- Header --}}
      <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-6">
        <div>
          <div class="chip">Analyzer 2.0</div>
          <h1 class="mt-2 text-3xl sm:text-4xl font-extrabold tracking-tight">Semantic SEO Master</h1>
          <p class="mt-2 text-slate-300 max-w-2xl">Analyze any page’s semantic structure, technical elements, and content quality—then get precise improvements.</p>
        </div>
        {{-- Elegant input group --}}
        <form id="semanticForm" class="w-full lg:w-[52%]">
          <div class="flex gap-2 items-stretch glass rounded-xl2 p-2 border border-white/10">
            <div class="flex-1">
              <input name="url" type="url" required
                     placeholder="Paste a URL (e.g. https://example.com/article)"
                     class="w-full bg-transparent text-white placeholder:text-slate-400 px-3 py-2 rounded-lg outline-none">
            </div>
            <div class="hidden md:block w-px bg-white/10"></div>
            <input name="target_keyword" type="text"
                   placeholder="Target keyword (optional)"
                   class="hidden md:block bg-transparent text-white placeholder:text-slate-400 px-3 py-2 rounded-lg outline-none">
            <button class="btn-primary" type="submit" id="analyzeBtn">
              <svg class="w-4 h-4" viewBox="0 0 24 24" stroke="currentColor" fill="none"><path stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35M5 11a6 6 0 1 0 12 0A6 6 0 0 0 5 11Z"/></svg>
              Analyze URL
            </button>
          </div>
          <p class="text-xs text-slate-400 mt-2">We’ll fetch the page and compute a detailed semantic report.</p>
        </form>
      </div>

      {{-- RESULTS --}}
      <section id="semanticResult" class="mt-10 hidden">

        {{-- Row: Wheel + Readability + Quick Stats --}}
        <div class="grid lg:grid-cols-3 gap-6">
          {{-- Score wheel --}}
          <div class="stat">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold">Overall Score</h3>
              <span id="badge" class="hidden text-xs px-2 py-0.5 rounded"></span>
            </div>
            <div class="mt-6 grid place-items-center">
              <svg class="gauge" viewBox="0 0 120 120" width="220" height="220" aria-label="Overall score gauge">
                <defs>
                  <linearGradient id="gradScore" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#ef4444"/>
                    <stop offset="50%" stop-color="#f59e0b"/>
                    <stop offset="100%" stop-color="#10b981"/>
                  </linearGradient>
                </defs>
                <!-- track -->
                <circle cx="60" cy="60" r="48" stroke="#e5e7eb" stroke-width="12" fill="none" />
                <!-- progress -->
                <circle id="gaugeArc" cx="60" cy="60" r="48" stroke="url(#gradScore)" stroke-width="12" fill="none"
                        stroke-linecap="round" transform="rotate(-90 60 60)"
                        stroke-dasharray="301.59" stroke-dashoffset="301.59"/>
                <!-- label -->
                <text id="overallScore" x="60" y="65" text-anchor="middle" font-size="28" class="fill-slate-900 font-bold">0</text>
                <text x="60" y="82" text-anchor="middle" font-size="10" class="fill-slate-500">/100</text>
              </svg>
            </div>
          </div>

          {{-- Readability --}}
          <div class="stat">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold">Readability (Flesch)</h3>
              <button class="improve-top btn-ghost text-sm" type="button" data-topic="Content Quality">
                Improve
              </button>
            </div>
            <div class="mt-5">
              <div class="h-3 w-full bg-white/10 rounded">
                <div id="readBar" class="h-3 rounded" style="width:0%"></div>
              </div>
              <div class="mt-3 flex items-baseline gap-2">
                <span id="readability" class="text-4xl font-extrabold">—</span>
                <span class="text-xs text-slate-300">Higher is easier (0–100)</span>
              </div>
              <p id="readNote" class="mt-2 text-sm text-slate-300"></p>
            </div>
          </div>

          {{-- Quick stats --}}
          <div class="stat">
            <h3 class="font-semibold">Quick Stats</h3>
            <div class="mt-5 grid grid-cols-2 gap-4 text-sm">
              <div class="rounded-lg bg-white/5 border border-white/10 p-3">
                <div class="text-slate-300">Text / HTML</div>
                <div id="ratioVal" class="text-xl font-bold">—%</div>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-3">
                <div class="text-slate-300">Images (alt missing)</div>
                <div class="text-xl font-bold"><span id="imgAlt">—</span></div>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-3">
                <div class="text-slate-300">Links</div>
                <div class="text-xl font-bold"><span id="internal">0</span> int · <span id="external">0</span> ext</div>
              </div>
              <div class="rounded-lg bg-white/5 border border-white/10 p-3">
                <div class="text-slate-300">Canonical</div>
                <div id="canonVal" class="text-xl font-bold">—</div>
              </div>
            </div>
          </div>
        </div>

        {{-- Row: Heading map + Meta --}}
        <div class="grid lg:grid-cols-2 gap-6 mt-6">
          <div class="card">
            <div class="p-6">
              <h3 class="font-semibold">Heading Map</h3>
              <div id="headingMap" class="mt-4 text-slate-700 space-y-3"></div>
            </div>
          </div>
          <div class="card">
            <div class="p-6">
              <h3 class="font-semibold">Meta</h3>
              <div class="mt-4 text-sm text-slate-700">
                <p><span class="font-medium">Title:</span> <span id="titleVal">—</span></p>
                <p class="mt-2"><span class="font-medium">Meta description:</span> <span id="metaVal">—</span></p>
              </div>
            </div>
          </div>
        </div>

        {{-- Checklist --}}
        <div class="card mt-6">
          <div class="p-6">
            <div class="flex items-center justify-between">
              <h3 class="font-semibold">Optimization Checklist</h3>
              <span class="text-xs text-slate-500">Green ≥ 80 • Orange 60–79 • Red &lt; 60</span>
            </div>
            <div id="checklistWrap" class="mt-5 grid lg:grid-cols-2 gap-6"></div>
          </div>
        </div>
      </section>
    </div>
  </div>

  {{-- Improve Modal --}}
  <div id="improveModal" class="fixed inset-0 hidden items-center justify-center bg-black/50 p-4 z-50">
    <div class="w-full max-w-lg rounded-2xl bg-white shadow-xl">
      <div class="p-4 border-b flex items-center justify-between">
        <h4 id="impTitle" class="font-semibold">Improve</h4>
        <button id="impClose" class="p-2 text-slate-500 hover:text-slate-800">✕</button>
      </div>
      <div class="p-5 space-y-4">
        <ul id="impTips" class="list-disc pl-5 text-sm text-slate-700"></ul>
        <div>
          <div class="text-xs uppercase text-slate-500 mb-1">Explore on Google</div>
          <div id="impLinks" class="flex flex-wrap gap-2"></div>
        </div>
      </div>
      <div class="p-4 border-t text-right">
        <button id="impOK" class="px-3 py-2 rounded bg-slate-900 text-white text-sm">Close</button>
      </div>
    </div>
  </div>

  {{-- Page script --}}
  <script>
    const elForm = document.getElementById('semanticForm');
    const btnAnalyze = document.getElementById('analyzeBtn');
    const elWrap = document.getElementById('semanticResult');
    const els = {
      gauge: document.getElementById('gaugeArc'),
      score: document.getElementById('overallScore'),
      badge: document.getElementById('badge'),
      read:  document.getElementById('readability'),
      readBar: document.getElementById('readBar'),
      readNote: document.getElementById('readNote'),
      ratio: document.getElementById('ratioVal'),
      imgAlt: document.getElementById('imgAlt'),
      internal: document.getElementById('internal'),
      external: document.getElementById('external'),
      canon: document.getElementById('canonVal'),
      title: document.getElementById('titleVal'),
      meta: document.getElementById('metaVal'),
      map: document.getElementById('headingMap'),
      checklist: document.getElementById('checklistWrap'),
      modal: document.getElementById('improveModal'),
      impTitle: document.getElementById('impTitle'),
      impTips: document.getElementById('impTips'),
      impLinks: document.getElementById('impLinks'),
      impClose: document.getElementById('impClose'),
      impOK: document.getElementById('impOK')
    };
    const CIRC = 2 * Math.PI * 48; // 301.59

    function setWheel(score){
      score = Math.max(0, Math.min(100, Number(score)||0));
      const offset = CIRC * (1 - score/100);
      els.gauge.setAttribute('stroke-dasharray', CIRC.toFixed(2));
      els.gauge.setAttribute('stroke-dashoffset', offset.toFixed(2));
      els.score.textContent = Math.round(score);
      // Badge
      if (score >= 80) {
        els.badge.textContent = 'Great Work — Well Optimized';
        els.badge.className = 'text-xs px-2 py-0.5 rounded badge-pass';
        els.badge.classList.remove('hidden');
      } else if (score < 60) {
        els.badge.textContent = 'Needs optimization';
        els.badge.className = 'text-xs px-2 py-0.5 rounded badge-fail';
        els.badge.classList.remove('hidden');
      } else {
        els.badge.textContent = 'Needs optimization';
        els.badge.className = 'text-xs px-2 py-0.5 rounded badge-warn';
        els.badge.classList.remove('hidden');
      }
    }
    function barColor(score){
      return score>=80?'bg-green-500':(score>=60?'bg-amber-500':'bg-rose-500');
    }
    function openImprove(pack){
      els.impTitle.textContent = `Improve — ${pack.topic || 'Checklist'}`;
      els.impTips.innerHTML = '';
      (pack.tips||[]).forEach(t=>{
        const li=document.createElement('li'); li.textContent=t; els.impTips.appendChild(li);
      });
      els.impLinks.innerHTML='';
      (pack.google||[]).forEach(link=>{
        const a=document.createElement('a');
        a.href=link; a.target='_blank';
        a.className='text-xs px-2 py-1 rounded bg-slate-900 text-white hover:opacity-90';
        try { a.textContent = decodeURIComponent(new URL(link).searchParams.get('q')||'Google'); }
        catch { a.textContent = 'Google'; }
        els.impLinks.appendChild(a);
      });
      els.modal.classList.remove('hidden');
    }
    [els.impClose, els.impOK].forEach(b=>b.addEventListener('click',()=>els.modal.classList.add('hidden')));

    // Fallback pack if server didn't send improve data
    function fallbackImprove(topic){
      return {
        topic,
        tips: [
          'Tighten headings; add missing subtopics/FAQs.',
          'Strengthen internal links with descriptive anchors.',
          'Ensure Title (~50–60) + meta (~150–160) are compelling.'
        ],
        google: [
          'https://www.google.com/search?q=heading%20hierarchy%20best%20practices',
          'https://www.google.com/search?q=write%20compelling%20meta%20description',
          'https://www.google.com/search?q=internal%20linking%20strategy%20seo'
        ]
      }
    }

    // Analyze submit
    elForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      // loading state
      const prev = btnAnalyze.innerHTML;
      btnAnalyze.disabled = true;
      btnAnalyze.innerHTML = `<svg class="w-4 h-4 animate-spin" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2" class="opacity-25"/><path d="M12 2a10 10 0 0 1 10 10" stroke-width="2" class="opacity-75"/></svg> Analyzing…`;

      try {
        const fd = new FormData(elForm);
        const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') };
        const res = await fetch('/api/semantic-analyze', {
          method:'POST', headers:{'Accept':'application/json','Content-Type':'application/json'},
          body: JSON.stringify(payload)
        });
        const data = await res.json();

        if (!data.ok) {
          alert(data.error || 'Analysis failed');
          return;
        }
        elWrap.classList.remove('hidden');

        // Wheel & badge
        setWheel(data.overall_score ?? 0);

        // Readability
        const fk = data.content_structure?.readability_flesch ?? data.readability_flesch ?? 0;
        els.read.textContent = fk;
        els.readBar.style.width = Math.min(100, Math.max(0, fk)) + '%';
        els.readBar.className = 'h-3 rounded '+barColor(fk);
        els.readNote.textContent = fk>=60 ? 'Easy to read—great job.' : (fk>=40 ? 'Readable, but could be clearer.' : 'Hard to read—shorten sentences, simplify wording.');

        // Quick stats
        els.internal.textContent = data.technical_seo?.links?.internal ?? 0;
        els.external.textContent = data.technical_seo?.links?.external ?? 0;
        const ratio = data.content_structure?.text_to_html_ratio ?? data.text_html_ratio ?? 0;
        els.ratio.textContent = `${ratio}%`;
        const miss = data.technical_seo?.image_alt_missing ?? 0;
        const total= data.technical_seo?.image_count ?? 0;
        els.imgAlt.textContent = `${miss} / ${total}`;

        const canon = data.canonical ?? data.content_structure?.canonical;
        els.canon.textContent = canon?.present ? (canon.matches ? 'Self' : 'Different URL') : 'Missing';

        // Meta
        els.title.textContent = data.content_structure?.title || '—';
        els.meta.textContent  = data.content_structure?.meta_description || '—';

        // Heading map
        const hm = data.content_structure?.headings || {};
        els.map.innerHTML='';
        Object.entries(hm).forEach(([level,arr])=>{
          if(!arr?.length) return;
          const block = document.createElement('div');
          block.innerHTML = `
            <div class="text-xs uppercase tracking-wide text-slate-500 mb-1">${level}</div>
            <div class="grid gap-2">
              ${arr.map(t => `<div class="px-3 py-2 rounded-lg border bg-slate-50">${t}</div>`).join('')}
            </div>`;
          els.map.appendChild(block);
        });

        // Checklist
        els.checklist.innerHTML='';
        const list = data.checklist || [];
        list.forEach(block=>{
          const state = block.status==='pass' ? 'badge-pass' : (block.status==='warn'?'badge-warn':'badge-fail');
          const bar  = `
            <div class="h-2 w-full bg-slate-200 rounded">
              <div class="h-2 rounded ${barColor(block.score)}" style="width:${block.score}%"></div>
            </div>`;
          const checks = (block.checks||[]).map(c=>{
            const dot = c.ok ? 'bg-green-500' : (c.score>=60?'bg-amber-500':'bg-rose-500');
            return `<li class="flex items-center gap-2">
              <span class="h-2.5 w-2.5 rounded-full ${dot}"></span>
              <span class="flex-1">${c.label}</span>
              <span class="text-xs font-semibold">${Math.round(c.score)}</span>
            </li>`;
          }).join('');
          const card = document.createElement('div');
          card.className = 'card p-5';
          card.innerHTML = `
            <div class="flex items-center justify-between">
              <h4 class="font-semibold">${block.category}</h4>
              <span class="text-xs px-2 py-0.5 rounded ${state}">${block.status?.toUpperCase() || ''}</span>
            </div>
            <div class="mt-3">${bar}</div>
            <ul class="mt-3 space-y-2 text-sm">${checks}</ul>
            <div class="mt-4">
              <button class="btn-primary improve-btn" type="button">Improve</button>
            </div>`;
          card.querySelector('.improve-btn').addEventListener('click', ()=>{
            openImprove(block.improve || fallbackImprove(block.category));
          });
          els.checklist.appendChild(card);
        });

        // Top Improve button (Readability card)
        document.querySelectorAll('.improve-top').forEach(btn=>{
          btn.onclick = ()=>{
            const topic = btn.getAttribute('data-topic') || 'Content Quality';
            const pack = (list.find(b=>b.category===topic)?.improve) || fallbackImprove(topic);
            openImprove(pack);
          };
        });

      } catch (e) {
        alert('Network or server error.');
      } finally {
        btnAnalyze.disabled = false;
        btnAnalyze.innerHTML = prev;
      }
    });
  </script>
@endsection
