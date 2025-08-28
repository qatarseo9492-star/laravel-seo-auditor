@extends('layouts.app')
@section('title','Topic Cluster Identification')

@section('content')
  <section class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold">Topic Cluster Identification</h1>
    <p class="text-slate-600 mt-2">Extract top terms and group them into lightweight clusters. Use as a quick guide for outlines and internal links.</p>

    <form id="topicForm" class="mt-6 space-y-3">
      <input name="url" type="url" placeholder="https://example.com (optional)" class="w-full px-3 py-2 rounded-xl border" />
      <textarea name="text" rows="8" placeholder="Paste content here (optional if URL provided)" class="w-full px-3 py-2 rounded-xl border"></textarea>
      <div class="flex items-center gap-3">
        <input name="top_k" type="number" min="5" max="50" value="20" class="w-24 px-3 py-2 rounded-xl border" />
        <button class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700" type="submit">Identify</button>
      </div>
    </form>

    <div id="topicResult" class="mt-8 hidden">
      <div class="grid lg:grid-cols-2 gap-6">
        <div class="p-6 rounded-2xl bg-white border">
          <h3 class="font-semibold">Top Terms</h3>
          <div id="topTerms" class="mt-3 text-sm grid grid-cols-2 gap-2"></div>
        </div>
        <div class="p-6 rounded-2xl bg-white border">
          <h3 class="font-semibold">Clusters</h3>
          <div id="clusters" class="mt-3 text-sm space-y-3"></div>
        </div>
      </div>
    </div>
  </section>
  <script>
    const topicForm = document.getElementById('topicForm');
    const topicWrap = document.getElementById('topicResult');
    const topTerms  = document.getElementById('topTerms');
    const clusters  = document.getElementById('clusters');

    topicForm.addEventListener('submit', async (e)=>{
      e.preventDefault();
      const fd = new FormData(topicForm);
      const payload = { url: fd.get('url'), text: fd.get('text'), top_k: Number(fd.get('top_k')||20) };
      const res = await fetch('/api/topic-cluster', { method:'POST', headers:{'Accept':'application/json','Content-Type':'application/json'}, body: JSON.stringify(payload)});
      const data = await res.json();
      if (!data.ok) { alert(data.error || 'Identification failed'); return; }
      topicWrap.classList.remove('hidden');

      // Top terms
      topTerms.innerHTML = '';
      Object.entries(data.top_terms).forEach(([term,count])=>{
        const div = document.createElement('div');
        div.className = 'p-2 rounded-lg bg-slate-50 border flex items-center justify-between';
        div.innerHTML = `<span>${term}</span><span class="text-slate-500">${count}</span>`;
        topTerms.appendChild(div);
      });

      // Clusters
      clusters.innerHTML = '';
      data.clusters.forEach(c=>{
        const card = document.createElement('div');
        card.className = 'p-3 rounded-lg bg-slate-50 border';
        card.innerHTML = `<div class="text-xs text-slate-500">Cluster</div><div class="font-medium">${c.label}</div>`+
                         `<div class="mt-2 grid grid-cols-2 gap-2">`+
                         c.stems.map(s=>`<div class='px-2 py-1 rounded bg-white border'>${s.term} <span class='text-slate-500'>(${s.count})</span></div>`).join('')+
                         `</div>`;
        clusters.appendChild(card);
      });
    });
  </script>
@endsection
