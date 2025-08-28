@extends('layouts.app', ['title' => 'Semantic SEO Master Analyzer 2.0'])

@section('content')
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
  <div class="flex items-start justify-between gap-6">
    <div>
      <h1 class="text-2xl font-bold">Semantic SEO Master Analyzer 2.0</h1>
      <p class="text-slate-300 mt-2 max-w-2xl">
        Enter a URL (and optional target keyword). The analyzer fetches the page and returns a structured report with score and recommendations.
      </p>
    </div>
  </div>

  <!-- Form -->
  <form id="semanticForm" class="mt-6 grid md:grid-cols-[1fr,300px] gap-3 items-start">
    <input
      name="url"
      type="url"
      required
      placeholder="https://example.com/article"
      class="w-full px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-600"
    />
    <div class="grid grid-cols-[1fr,auto] gap-3">
      <input
        name="target_keyword"
        type="text"
        placeholder="Target keyword (optional)"
        class="w-full px-3 py-2 rounded-xl bg-white text-slate-900 border border-slate-200 focus:outline-none focus:ring-2 focus:ring-brand-600"
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
        <p class="text-4xl font-extrabold">
          <span id="internal">0</span>/<span id="external">0</span>
        </p>
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
  const elForm  = document.getElementById('semanticForm');
  const elWrap  = document.getElementById('semanticResult');
  const errBox  = document.getElementById('errorBox');
  const errMsg  = document.getElementById('errorMsg');
  const btn     = document.getElementById('submitBtn');
  const spinner = document.getElementById('spinner');
  const btnText = document.getElementById('btnText');

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

  function setLoading(on) {
    btn.disabled = on;
    spinner.classList.toggle('hidden', !on);
    btnText.textContent = on ? 'Analyzing…' : 'Analyze';
  }

  function showError(msg) {
    errMsg.textContent = msg || 'Analysis failed';
    errBox.classList.remove('hidden');
  }

  function hideError() {
    errBox.classList.add('hidden');
  }

  elForm.addEventListener('submit', async (e) => {
    e.preventDefault();
    hideError();
    setLoading(true);

    try {
      const fd = new FormData(elForm);
      const payload = {
        url: (fd.get('url') || '').trim(),
        target_keyword: (fd.get('target_keyword') || '').trim()
      };

      const res = await fetch('/api/semantic-analyze', {
        method: 'POST',
        headers: {
          'Accept':'application/json',
          'Content-Type':'application/json',
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(payload)
      });

      // Handle non-2xx quickly
      if (!res.ok) {
        let msg = 'HTTP ' + res.status + ' ' + res.statusText;
        try {
          const j = await res.json();
          if (j && j.error) msg = j.error;
        } catch(_) {}
        showError(msg);
        setLoading(false);
        return;
      }

      const data = await res.json();
      if (!data || !data.ok) {
        showError((data && data.error) || 'Analysis failed');
        setLoading(false);
        return;
      }

      elWrap.classList.remove('hidden');

      // Top metrics
      els.score.textContent = data.overall_score ?? '—';
      const score = Number(data.overall_score || 0);
      els.bar.style.width = Math.max(0, Math.min(100, score)) + '%';
      els.bar.className = 'h-2 rounded-full ' + (score>=80? 'bg-green-500' : score>=60? 'bg-amber-500':'bg-red-500');

      els.readability.textContent = data.content_structure?.readability_flesch ?? '—';
      els.internal.textContent = data.technical_seo?.links?.internal ?? 0;
      els.external.textContent = data.technical_seo?.links?.external ?? 0;

      // Structure
      els.title.textContent = data.content_structure?.title || '—';
      els.meta.textContent  = data.content_structure?.meta_description || '—';
      els.ratio.textContent = data.content_structure?.text_to_html_ratio ?? '—';
      const imgMissing = data.technical_seo?.image_alt_missing ?? 0;
      const imgCount   = data.technical_seo?.image_count ?? 0;
      els.imgAlt.textContent = `${imgMissing} / ${imgCount}`;

      // Headings
      els.map.innerHTML = '';
      const headings = data.content_structure?.headings || {};
      Object.entries(headings).forEach(([level, arr]) => {
        if (!arr || !arr.length) return;
        const div = document.createElement('div');
        div.innerHTML =
          `<div class="text-xs uppercase text-slate-500">${level}</div>` +
          arr.map(t => `<div class="pl-3">• ${t}</div>`).join('');
        els.map.appendChild(div);
      });

      // Recommendations
      els.recs.innerHTML = '';
      (data.recommendations || []).forEach(r => {
        const li = document.createElement('li');
        const sev = (r.severity || '').toLowerCase();
        const badge =
          sev === 'critical' ? 'bg-red-100 text-red-700' :
          sev === 'warning'  ? 'bg-amber-100 text-amber-700' :
                               'bg-slate-100 text-slate-700';
        li.innerHTML = `<span class="px-2 py-0.5 rounded ${badge} mr-2">${r.severity || 'Info'}</span>${r.text || ''}`;
        els.recs.appendChild(li);
      });

      // Anchors
      els.anchors.innerHTML = '';
      (data.technical_seo?.links?.anchors || []).slice(0, 100).forEach(a => {
        const p = document.createElement('p');
        const type = a.type || 'link';
        const text = a.text || '(no text)';
        const href = a.href || '';
        p.textContent = `[${type}] ${text} → ${href}`;
        els.anchors.appendChild(p);
      });

    } catch (err) {
      showError(err?.message || 'Network error');
    } finally {
      setLoading(false);
    }
  });
</script>
@endsection
