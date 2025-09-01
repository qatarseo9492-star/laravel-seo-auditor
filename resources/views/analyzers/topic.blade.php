@extends('layouts.app', ['title' => 'Topic Cluster Identification'])

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="flex items-start justify-between gap-6">
    <div>
      <h1 class="text-2xl font-bold">Topic Cluster Identification</h1>
      <p class="text-slate-300 mt-2 max-w-2xl">
        Fetch a page, extract key terms, and group them into lightweight topic clusters to guide structure and coverage.
      </p>
    </div>
  </div>

  <!-- Form -->
  <form id="topicForm" class="mt-6 grid md:grid-cols-[1fr,180px,auto] gap-3 items-start">
    <input
      name="url"
      type="url"
      required
      placeholder="https://example.com/article"
      class="w-full px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-600"
    />
    <input
      name="top_k"
      type="number"
      min="5"
      max="50"
      value="20"
      class="px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-600"
      title="How many top terms to extract"
    />
    <button
      id="submitBtn"
      class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700 disabled:opacity-60 disabled:cursor-not-allowed"
      type="submit">
      <span class="inline-flex items-center gap-2">
        <svg id="spinner" class="hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v3a5 5 0 00-5 5H4z"/>
        </svg>
        <span id="btnText">Analyze</span>
      </span>
    </button>
  </form>

  <!-- Error -->
  <div id="errorBox" class="mt-4 hidden">
    <div class="rounded-xl border border-red-400/30 bg-red-500/10 text-red-200 px-4 py-3 text-sm">
      <strong class="font-semibold">Analysis failed:</strong>
      <span id="errorMsg">Unknown error</span>
    </div>
  </div>

  <!-- Results -->
  <div id="topicResult" class="mt-8 hidden">
    <div class="grid lg:grid-cols-3 gap-6">
      <!-- Top terms -->
      <div class="lg:col-span-1 p-6 rounded-2xl bg-white border">
        <h3 class="font-semibold">Top Terms</h3>
        <div id="terms" class="mt-3 flex flex-wrap gap-2 text-sm"></div>
      </div>

      <!-- Clusters -->
      <div class="lg:col-span-2 p-6 rounded-2xl bg-white border">
        <h3 class="font-semibold">Clusters</h3>
        <div id="clusters" class="mt-4 grid sm:grid-cols-2 gap-4"></div>
        <p class="text-xs text-slate-500 mt-4">
          Use clusters as section prompts (H2/H3). Cover strongest stems and add illustrative examples, FAQs, and internal links.
        </p>
      </div>
    </div>
  </div>
</section>

<script>
  const topicForm = document.getElementById('topicForm');
  const resultEl  = document.getElementById('topicResult');
  const errBox    = document.getElementById('errorBox');
  const errMsg    = document.getElementById('errorMsg');
  const btn       = document.getElementById('submitBtn');
  const spinner   = document.getElementById('spinner');
  const btnText   = document.getElementById('btnText');

  const els = {
    terms:    document.getElementById('terms'),
    clusters: document.getElementById('clusters')
  };

  function setLoading(on){
    btn.disabled = on;
    spinner.classList.toggle('hidden', !on);
    btnText.textContent = on ? 'Analyzing…' : 'Analyze';
  }
  function showError(m){ errMsg.textContent = m || 'Analysis failed'; errBox.classList.remove('hidden'); }
  function hideError(){ errBox.classList.add('hidden'); }

  topicForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError(); setLoading(true);

    try{
      const fd = new FormData(topicForm);
      const payload = {
        url:   (fd.get('url') || '').trim(),
        top_k: Number(fd.get('top_k') || 20)
      };

      const res = await fetch('/api/topic-cluster', {
        method:'POST',
        headers:{ 'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify(payload)
      });

      if(!res.ok){
        let msg = 'HTTP ' + res.status + ' ' + res.statusText;
        try{ const j = await res.json(); if(j?.error) msg = j.error; } catch(_){}
        showError(msg); setLoading(false); return;
      }

      const data = await res.json();
      if(!data?.ok){
        showError(data?.error || 'Analysis failed');
        setLoading(false); return;
      }

      // Top terms
      els.terms.innerHTML = '';
      const terms = data.top_terms || {};
      const sorted = Object.entries(terms).sort((a,b)=>b[1]-a[1]);
      sorted.forEach(([term,count])=>{
        const span = document.createElement('span');
        span.className = 'inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-100 border text-slate-700';
        span.innerHTML = `<strong>${term}</strong><span class="text-xs text-slate-500">×${count}</span>`;
        els.terms.appendChild(span);
      });

      // Clusters
      els.clusters.innerHTML = '';
      (data.clusters || []).forEach(c => {
        const card = document.createElement('div');
        card.className = 'rounded-xl border p-4 bg-slate-50';
        const stems = (c.stems || []).map(s => `<span class="px-2 py-1 rounded bg-white border mr-1 text-xs">${s.term}<span class="text-slate-400">×${s.count}</span></span>`).join(' ');
        card.innerHTML = `
          <div class="flex items-center justify-between">
            <div class="font-semibold">${(c.label || '').toUpperCase()}</div>
            <div class="text-xs text-slate-500">Weight: ${c.weight ?? 0}</div>
          </div>
          <div class="mt-2">${stems || '<span class="text-slate-400 text-sm">No stems found</span>'}</div>
        `;
        els.clusters.appendChild(card);
      });

      resultEl.classList.remove('hidden');
    }catch(err){
      showError(err?.message || 'Network error');
    }finally{
      setLoading(false);
    }
  });
</script>
@endsection
