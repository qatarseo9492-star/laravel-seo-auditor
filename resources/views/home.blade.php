@extends('layouts.app')
@section('title','Home — Semantic SEO Master Analyzer 2.0')

@section('content')
  <!-- Header / Hero -->
  <header class="bg-gradient-to-br from-white to-slate-100 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <div>
          <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight">Audit smarter. Write better. Rank higher.</h1>
          <p class="mt-4 text-slate-700">
            A modern toolkit: <span class="font-semibold">Semantic Analyzer</span>,
            <span class="font-semibold">AI Content Checker</span> and
            <span class="font-semibold">Topic Cluster Identification</span> — fast, clean and actionable.
          </p>
          <div class="mt-6 flex gap-3">
            <a href="{{ url('/semantic-analyzer') }}" class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700">Open Semantic Analyzer</a>
            <a href="{{ url('/ai-content-checker') }}" class="px-4 py-2 rounded-xl border border-slate-300 hover:bg-slate-100">AI Checker</a>
          </div>
        </div>
        <div class="md:pl-8">
          <div class="grid grid-cols-3 gap-4">
            <div class="p-5 rounded-2xl bg-white border shadow-sm">
              <p class="text-xs text-slate-500">Overall Score</p>
              <p class="text-3xl font-bold">0–100</p>
              <p class="text-xs text-slate-500 mt-2">Simple weighted model</p>
            </div>
            <div class="p-5 rounded-2xl bg-white border shadow-sm">
              <p class="text-xs text-slate-500">Readability</p>
              <p class="text-3xl font-bold">Flesch</p>
              <p class="text-xs text-slate-500 mt-2">Fast, approximate</p>
            </div>
            <div class="p-5 rounded-2xl bg-white border shadow-sm">
              <p class="text-xs text-slate-500">Structured Data</p>
              <p class="text-3xl font-bold">JSON-LD</p>
              <p class="text-xs text-slate-500 mt-2">Detect presence</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Instant Semantic Check (quick win) -->
      <div class="mt-10 p-5 rounded-2xl bg-white border">
        <form id="instantSemantic" class="grid md:grid-cols-[1fr,280px] gap-3">
          <input name="url" type="url" required placeholder="https://example.com/article" class="w-full px-3 py-2 rounded-xl border" />
          <div class="grid grid-cols-[1fr,auto] gap-3">
            <input name="target_keyword" type="text" placeholder="Target keyword (optional)" class="w-full px-3 py-2 rounded-xl border" />
            <button class="px-4 py-2 rounded-xl bg-brand-600 text-white hover:bg-brand-700" type="submit">Analyze</button>
          </div>
        </form>

        <div id="instantResult" class="mt-6 hidden">
          <div class="grid sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-slate-50 border">
              <p class="text-xs text-slate-500">Overall Score</p>
              <p id="instScore" class="text-3xl font-extrabold">—</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border">
              <p class="text-xs text-slate-500">Flesch</p>
              <p id="instFlesch" class="text-3xl font-extrabold">—</p>
            </div>
            <div class="p-4 rounded-xl bg-slate-50 border">
              <p class="text-xs text-slate-500">Links (int/ext)</p>
              <p class="text-3xl font-extrabold"><span id="instInt">0</span>/<span id="instExt">0</span></p>
            </div>
          </div>
          <p class="mt-4 text-sm text-slate-600">Full details on the <a class="text-brand-700 underline" href="{{ url('/semantic-analyzer') }}">Semantic Analyzer</a> page.</p>
        </div>
      </div>
    </div>
  </header>

  <!-- Feature Tiles -->
  <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="grid md:grid-cols-3 gap-6">
      <a href="{{ url('/semantic-analyzer') }}" class="block p-6 rounded-2xl bg-white border hover:shadow-md transition">
        <h3 class="font-semibold text-lg">Semantic SEO Master Analyzer 2.0</h3>
        <p class="mt-2 text-sm text-slate-600">URL audit, headings, links, images, structured data and more — with an overall score & prioritized fixes.</p>
      </a>
      <a href="{{ url('/ai-content-checker') }}" class="block p-6 rounded-2xl bg-white border hover:shadow-md transition">
        <h3 class="font-semibold text-lg">AI Content Checker</h3>
        <p class="mt-2 text-sm text-slate-600">Heuristic signals (TTR, sentence length, punctuation variety) to estimate AI vs. human-like writing.</p>
      </a>
      <a href="{{ url('/topic-cluster') }}" class="block p-6 rounded-2xl bg-white border hover:shadow-md transition">
        <h3 class="font-semibold text-lg">Topic Cluster Identification</h3>
        <p class="mt-2 text-sm text-slate-600">Extract top terms and group them into lightweight clusters to guide outlines and internal linking.</p>
      </a>
    </div>
  </section>

  <script>
    // Instant check (home hero form)
    const instForm  = document.getElementById('instantSemantic');
    const instWrap  = document.getElementById('instantResult');
    const instScore = document.getElementById('instScore');
    const instFlesch= document.getElementById('instFlesch');
    const instInt   = document.getElementById('instInt');
    const instExt   = document.getElementById('instExt');

    instForm?.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(instForm);
      const payload = { url: fd.get('url'), target_keyword: fd.get('target_keyword') };
      const res = await fetch('/api/semantic-analyze', {
        method: 'POST',
        headers: { 'Accept':'application/json','Content-Type':'application/json' },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (!data.ok) { alert(data.error || 'Analysis failed'); return; }
      instWrap.classList.remove('hidden');
      instScore.textContent = data.overall_score;
      instFlesch.textContent= data.content_structure.readability_flesch;
      instInt.textContent   = data.technical_seo.links.internal;
      instExt.textContent   = data.technical_seo.links.external;
    });
  </script>
@endsection
