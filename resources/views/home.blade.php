<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Semantic SEO Master Analyzer 2.0 — Home</title>

  <!-- Inter + Tailwind (CDN) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] },
          colors: {
            brand: {
              50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',
              500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'
            }
          }
        }
      }
    }
  </script>
  <style>html{scroll-behavior:smooth}</style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">

  <!-- Top Menu Bar (above header) -->
  <nav class="w-full border-b border-slate-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="h-14 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 5v10h-2V7h2zm-1 12a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5z"/></svg>
          <span>Semantic SEO Master Analyzer 2.0</span>
        </a>

        <div class="hidden md:flex items-center gap-6">
          <a href="{{ url('/semantic-analyzer') }}" class="hover:text-brand-700">Semantic Analyzer</a>
          <a href="{{ url('/ai-content-checker') }}" class="hover:text-brand-700">AI Content Checker</a>
          <a href="{{ url('/topic-cluster') }}" class="hover:text-brand-700">Topic Analysis</a>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ url('/login') }}" class="text-sm px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-100">Login</a>
          <a href="{{ url('/register') }}" class="text-sm px-3 py-1.5 rounded-lg bg-brand-600 text-white hover:bg-brand-700">Signup</a>
        </div>
      </div>
    </div>
  </nav>

  <!-- Header / Hero -->
  <header class="bg-gradient-to-br from-white to-slate-100 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid md:grid-cols-2 gap-10 items-center">
        <div>
          <h1 class="text-3xl sm:text-4xl font-extrabold leading-tight">
            Audit smarter. Write better. Rank higher.
          </h1>
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

      <!-- Quick “Instant Check” (fixes non-working analyzer button complaints) -->
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

  <footer class="border-t mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-sm text-slate-600">
      <p>© {{ date('Y') }} Semantic SEO Master Analyzer 2.0 · Built for speed, clarity and action.</p>
    </div>
  </footer>

  <script>
    // Instant check (home hero form) — uses the same API signature as the analyzer page
    const instForm = document.getElementById('instantSemantic');
    const instWrap = document.getElementById('instantResult');
    const instScore = document.getElementById('instScore');
    const instFlesch = document.getElementById('instFlesch');
    const instInt = document.getElementById('instInt');
    const instExt = document.getElementById('instExt');

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
      instFlesch.textContent = data.content_structure.readability_flesch;
      instInt.textContent = data.technical_seo.links.internal;
      instExt.textContent = data.technical_seo.links.external;
    });
  </script>
</body>
</html>
