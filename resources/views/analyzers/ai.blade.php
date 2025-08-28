@extends('layouts.app')
@section('title','AI Content Checker')

@section('content')
  <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold">AI Content Checker</h1>
    <p class="text-slate-600 mt-2">Paste text or provide a URL to estimate AI-likeness vs human-like writing using offline stylistic signals.</p>

    <form id="aiForm" class="mt-6 space-y-3">
      <input name="url" type="url" placeholder="https://example.com (optional)" class="w-full px-3 py-2 rounded-xl border" />
      <textarea name="text" rows="8" placeholder="Paste your content here (optional if URL provided)" class="w-full px-3 py-2 rounded-xl border"></textarea>
      <button class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700" type="submit">Check</button>
    </form>

    <div id="aiResult" class="mt-8 hidden p-6 rounded-2xl bg-white border">
      <div class="grid md:grid-cols-2 gap-6">
        <div>
          <p class="text-xs text-slate-500">AI Probability</p>
          <p id="aiPct" class="text-4xl font-extrabold">—</p>
        </div>
        <div>
          <p class="text-xs text-slate-500">Human Probability</p>
          <p id="humanPct" class="text-4xl font-extrabold">—</p>
        </div>
      </div>
      <div class="mt-6">
        <h3 class="font-semibold">Signals</h3>
        <div id="aiSignals" class="mt-2 text-sm grid sm:grid-cols-2 gap-2"></div>
      </div>
    </div>
  </section>
  <script>
    const aiForm = document.getElementById('aiForm');
    const aiWrap = document.getElementById('aiResult');
    const aiPct = document.getElementById('aiPct');
    const humanPct = document.getElementById('humanPct');
    const aiSignals = document.getElementById('aiSignals');

    aiForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const fd = new FormData(aiForm);
      const payload = { url: fd.get('url'), text: fd.get('text') };
      const res = await fetch('/api/ai-check', { method:'POST', headers:{'Accept':'application/json','Content-Type':'application/json'}, body: JSON.stringify(payload)});
      const data = await res.json();
      if (!data.ok) { alert(data.error || 'Check failed'); return; }
      aiWrap.classList.remove('hidden');
      aiPct.textContent = data.ai_probability_percent + '%';
      humanPct.textContent = data.human_probability_percent + '%';
      aiSignals.innerHTML = '';
      const map = { ttr:'Type-Token Ratio', avgSentLen:'Avg sentence length', punctVariety:'Punctuation variety', stopwordRate:'Stop-word rate', wc:'Word count' };
      Object.entries(data.metrics).forEach(([k,v])=>{
        const div = document.createElement('div');
        div.className = 'p-3 rounded-lg bg-slate-50 border';
        div.innerHTML = `<div class="text-xs text-slate-500">${map[k]||k}</div><div class="font-medium">${typeof v === 'number' ? (v.toFixed ? v.toFixed(3) : v) : v}</div>`;
        aiSignals.appendChild(div);
      });
    });
  </script>
@endsection
