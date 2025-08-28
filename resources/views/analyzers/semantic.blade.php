@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@section('content')
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <h1 class="text-2xl font-bold">Semantic SEO Master Analyzer 2.0</h1>
    <p class="text-slate-600 mt-2">Enter a URL (and optional target keyword). The analyzer fetches the page and returns a structured report with score and recommendations.</p>

    <form id="semanticForm" class="mt-6 grid md:grid-cols-[1fr,280px] gap-3">
      <input name="url" type="url" required placeholder="https://example.com/article" class="w-full px-3 py-2 rounded-xl border" />
      <div class="grid grid-cols-[1fr,auto] gap-3">
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)" class="w-full px-3 py-2 rounded-xl border" />
        <button class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700" type="submit">Analyze</button>
      </div>
    </form>

    <div id="semanticResult" class="mt-8 hidden">
      <div class="grid md:grid-cols-3 gap-6">
        <div class="p-6 rounded-2xl bg-white border">
          <p class="text-xs text-slate-500">Overall Score</p>
          <p id="overallScore" class="text-4xl font-extrabold">—</p>
          <div class="mt-2 h-2 rounded-full bg-slate-200">
            <div id="scoreBar" class="h-2 rounded-full" style="width:0%"></div>
          </div>
        </div>
        <div class="p-6 rounded-2xl bg-white border">
          <p class="text-xs text-slate-500">Readability (Flesch)</p>
          <p id="readability" class="text-4xl font-extrabold">—</p>
          <p class="text-xs text-slate-500 mt-2">Higher is easier (0–100)</p>
        </div>
        <div class="p-6 rounded-2xl bg-white border">
          <p class="text-xs text-slate-500">Links</p>
          <p class="text-4xl font-extrabold"><span id="internal">0</span>/<span id="external">0</span></p>
          <p class="text-xs text-slate-500 mt-2">internal / external</p>
        </div>
      </div>

      <div class="mt-8 grid lg:grid-cols-2 gap-6">
        <div class="p-6 rounded-2xl bg-white border">
          <h3 class="font-semibold">Content Structure</h3>
          <div class="mt-3 text-sm">
            <p><span class="font-medium">Title:</span> <span id="titleVal">—</span></p>
            <p class="mt-1"><span class="font-medium">Meta description:</span> <span id="metaVal">—</span></p>
            <p class="mt-1"><span class="font-medium">Text/HTML ratio:</span> <span id="ratioVal">—</span>%</p>
            <p class="mt-1"><span class="font-medium">Images missing alt:</span> <span id="imgAlt">—</span></p>
          </div>
          <div class="mt-4">
            <details class="rounded-xl border p-4 bg-slate-50">
              <summary class="cursor-pointer font-medium">Heading Map</summary>
              <div id="headingMap" class="mt-2 text-sm space-y-2"></div>
            </details>
          </div>
        </div>
        <div class="p-6 rounded-2xl bg-white border">
          <h3 class="font-semibold">Recommendations</h3>
          <ul id="recs" class="mt-3 space-y-2 text-sm"></ul>
          <div class="mt-6">
            <details class="rounded-xl border p-4 bg-slate-50">
              <summary class="cursor-pointer font-medium">Anchors</summary>
              <div id="anchors" class="mt-2 text-sm space-y-2"></div>
            </details>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script>
    const elForm = document.getElementById('semanticForm');
    const elWrap = document.getElementById('semanticResult');
    const els = {
      score: document.getElementById('overallScore'),
      bar: document.getElementById('scoreBar'),
      readability: document.getElementById('readability'),
      internal: document.getElementById('internal'),
      external: document.getElementById('external'),
      title: document.getElementById('titleVal'),
      meta: document.getElementById('metaVal'),
      ratio: document.getElementById('ratioVal'),
      imgAlt: document.getElementById('imgAlt'),
      map: document.getElementById('headingMap'),
      recs: document.getElementById('recs'),
      anchors: document.getElementById('anchors')
    };

    elForm.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(elForm);
      const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') };
      const res = await fetch('/api/semantic-analyze', {
        method: 'POST', headers: { 'Accept':'application/json','Content-Type':'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (!data.ok) { alert(data.error || 'Analysis failed'); return; }
      elWrap.classList.remove('hidden');
      els.score.textContent = data.overall_score;
      els.bar.style.width = data.overall_score + '%';
      els.bar.className = 'h-2 rounded-full ' + (data.overall_score>=80? 'bg-green-500' : data.overall_score>=60? 'bg-amber-500':'bg-red-500');
      els.readability.textContent = data.content_structure.readability_flesch;
      els.internal.textContent = data.technical_seo.links.internal;
      els.external.textContent = data.technical_seo.links.external;
      els.title.textContent = data.content_structure.title || '—';
      els.meta.textContent = data.content_structure.meta_description || '—';
      els.ratio.textContent = data.content_structure.text_to_html_ratio;
      els.imgAlt.textContent = data.technical_seo.image_alt_missing + ' / ' + data.technical_seo.image_count;

      // Headings
      els.map.innerHTML = '';
      Object.entries(data.content_structure.headings).forEach(([level,arr])=>{
        if (!arr || !arr.length) return;
        const div = document.createElement('div');
        div.innerHTML = `<div class="text-xs uppercase text-slate-500">${level}</div>` +
                        arr.map(t=>`<div class="pl-3">• ${t}</div>`).join('');
        els.map.appendChild(div);
      });

      // Recs
      els.recs.innerHTML = '';
      data.recommendations.forEach(r=>{
        const li = document.createElement('li');
        const badge = r.severity === 'Critical' ? 'bg-red-100 text-red-700' : r.severity==='Warning'? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700';
        li.innerHTML = `<span class="px-2 py-0.5 rounded ${badge} mr-2">${r.severity}</span>${r.text}`;
        els.recs.appendChild(li);
      });

      // Anchors
      els.anchors.innerHTML = '';
      data.technical_seo.links.anchors.slice(0,100).forEach(a=>{
        const p = document.createElement('p');
        p.textContent = `[${a.type}] ${a.text || '(no text)'} → ${a.href}`;
        els.anchors.appendChild(p);
      });
    });
  </script>
@endsection
