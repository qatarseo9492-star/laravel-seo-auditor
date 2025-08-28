@extends('layouts.app', ['title' => 'AI Content Checker'])

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="flex items-start justify-between gap-6">
    <div>
      <h1 class="text-2xl font-bold">AI Content Checker</h1>
      <p class="text-slate-300 mt-2 max-w-2xl">
        Paste text and get a quick probability estimate of AI- vs human-written content, plus writing metrics.
      </p>
    </div>
  </div>

  <!-- Form -->
  <form id="aiForm" class="mt-6 space-y-3">
    <textarea
      name="text"
      rows="8"
      required
      placeholder="Paste your content here…"
      class="w-full px-3 py-3 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-600"
    ></textarea>

    <div class="flex items-center justify-between">
      <div class="text-xs text-slate-400">
        Tip: best with 100–2,000 words. We never store your text.
      </div>
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
    </div>
  </form>

  <!-- Error -->
  <div id="errorBox" class="mt-4 hidden">
    <div class="rounded-xl border border-red-400/30 bg-red-500/10 text-red-200 px-4 py-3 text-sm">
      <strong class="font-semibold">Analysis failed:</strong>
      <span id="errorMsg">Unknown error</span>
    </div>
  </div>

  <!-- Results -->
  <div id="aiResult" class="mt-8 hidden">
    <div class="grid md:grid-cols-2 gap-6">
      <div class="p-6 rounded-2xl bg-white border">
        <p class="text-xs text-slate-500">AI Probability</p>
        <div class="mt-2 flex items-end gap-4">
          <p id="aiPct" class="text-4xl font-extrabold">—%</p>
          <p class="text-slate-500">vs <span id="humanPct" class="font-semibold text-slate-900">—%</span> human</p>
        </div>
        <div class="mt-3">
          <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden">
            <div id="aiBar" class="h-2 bg-sky rounded-full" style="width:0%"></div>
          </div>
          <div class="h-2 w-full bg-slate-200 rounded-full overflow-hidden mt-2">
            <div id="humanBar" class="h-2 bg-emerald-500 rounded-full" style="width:0%"></div>
          </div>
        </div>
        <p class="text-xs text-slate-500 mt-3">
          This is a heuristic signal, not a definitive classifier. Use alongside editorial judgment.
        </p>
      </div>

      <div class="p-6 rounded-2xl bg-white border">
        <p class="text-xs text-slate-500">Writing Metrics</p>
        <div class="mt-3 grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
          <div class="rounded-xl border p-3 bg-slate-50 text-center">
            <div class="text-slate-500 text-xs">Word Count</div>
            <div id="m_wc" class="text-xl font-semibold">—</div>
          </div>
          <div class="rounded-xl border p-3 bg-slate-50 text-center">
            <div class="text-slate-500 text-xs">Avg Sent Len</div>
            <div id="m_avg" class="text-xl font-semibold">—</div>
          </div>
          <div class="rounded-xl border p-3 bg-slate-50 text-center">
            <div class="text-slate-500 text-xs">TTR</div>
            <div id="m_ttr" class="text-xl font-semibold">—</div>
          </div>
          <div class="rounded-xl border p-3 bg-slate-50 text-center">
            <div class="text-slate-500 text-xs">Punct Variety</div>
            <div id="m_pv" class="text-xl font-semibold">—</div>
          </div>
          <div class="rounded-xl border p-3 bg-slate-50 text-center">
            <div class="text-slate-500 text-xs">Stopword Rate</div>
            <div id="m_sw" class="text-xl font-semibold">—</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  const aiForm   = document.getElementById('aiForm');
  const resultEl = document.getElementById('aiResult');
  const errBox   = document.getElementById('errorBox');
  const errMsg   = document.getElementById('errorMsg');
  const btn      = document.getElementById('submitBtn');
  const spinner  = document.getElementById('spinner');
  const btnText  = document.getElementById('btnText');

  const els = {
    aiPct:   document.getElementById('aiPct'),
    humanPct:document.getElementById('humanPct'),
    aiBar:   document.getElementById('aiBar'),
    humanBar:document.getElementById('humanBar'),
    m_wc:    document.getElementById('m_wc'),
    m_avg:   document.getElementById('m_avg'),
    m_ttr:   document.getElementById('m_ttr'),
    m_pv:    document.getElementById('m_pv'),
    m_sw:    document.getElementById('m_sw'),
  };

  function setLoading(on){
    btn.disabled = on;
    spinner.classList.toggle('hidden', !on);
    btnText.textContent = on ? 'Analyzing…' : 'Analyze';
  }
  function showError(m){ errMsg.textContent = m || 'Analysis failed'; errBox.classList.remove('hidden'); }
  function hideError(){ errBox.classList.add('hidden'); }

  aiForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError(); setLoading(true);
    try{
      const fd = new FormData(aiForm);
      const text = (fd.get('text') || '').trim();
      const res = await fetch('/api/ai-check', {
        method:'POST',
        headers:{ 'Accept':'application/json','Content-Type':'application/json','X-Requested-With':'XMLHttpRequest' },
        body: JSON.stringify({ text })
      });
      if(!res.ok){
        let msg = 'HTTP ' + res.status + ' ' + res.statusText;
        try{ const j = await res.json(); if(j?.error) msg = j.error; } catch(_){}
        showError(msg); setLoading(false); return;
      }
      const data = await res.json();
      if(!data?.ok){ showError(data?.error || 'Analysis failed'); setLoading(false); return; }

      const ai    = Number(data.ai_probability_percent  ?? 0);
      const human = Number(data.human_probability_percent ?? 0);
      els.aiPct.textContent    = ai + '%';
      els.humanPct.textContent = human + '%';
      els.aiBar.style.width    = Math.max(0, Math.min(100, ai)) + '%';
      els.humanBar.style.width = Math.max(0, Math.min(100, human)) + '%';

      els.m_wc.textContent  = data.metrics?.wc ?? '—';
      els.m_avg.textContent = data.metrics?.avgSentLen ?? '—';
      els.m_ttr.textContent = data.metrics?.ttr ?? '—';
      els.m_pv.textContent  = data.metrics?.punctVariety ?? '—';
      els.m_sw.textContent  = data.metrics?.stopwordRate ?? '—';

      resultEl.classList.remove('hidden');
    }catch(err){
      showError(err?.message || 'Network error');
    }finally{
      setLoading(false);
    }
  });
</script>
@endsection
