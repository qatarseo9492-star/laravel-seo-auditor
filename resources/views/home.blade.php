<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Semantic SEO Master Analyzer 2.0</title>

  <!-- Tailwind via CDN (simple + no build step needed) -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50:'#f5f3ff',100:'#ede9fe',200:'#ddd6fe',300:'#c4b5fd',400:'#a78bfa',
              500:'#8b5cf6',600:'#7c3aed',700:'#6d28d9',800:'#5b21b6',900:'#4c1d95'
            }
          },
          boxShadow: {
            soft: '0 8px 30px rgba(0,0,0,.08)'
          },
          backdropBlur: { xs: '2px' }
        }
      }
    }
  </script>

  <!-- Minimal icons (Heroicons inline SVG used below) -->
</head>
<body class="bg-slate-950 text-slate-100 antialiased">

  <!-- Top Bar -->
  <header class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-md border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-3">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-indigo-500 shadow-soft">
            <!-- spark icon -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v3m0 12v3m9-9h-3M6 12H3m14.95 5.657-2.121-2.121M7.172 7.172 5.05 5.05m12.728 0-2.122 2.122M7.172 16.828 5.05 18.95"/>
            </svg>
          </span>
          <div class="leading-tight">
            <div class="font-bold tracking-tight">Semantic SEO Master</div>
            <div class="text-xs text-slate-400">Analyzer 2.0</div>
          </div>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm">
          <a href="#semantic" class="hover:text-white text-slate-300">Semantic Analyzer</a>
          <a href="#ai-check" class="hover:text-white text-slate-300">AI Content Checker</a>
          <a href="#topic-cluster" class="hover:text-white text-slate-300">Topic Cluster</a>
          <a href="/semantic-analyzer" class="hover:text-white text-slate-300">Full App</a>
        </nav>

        <div class="flex items-center gap-2">
          <a href="/login" class="px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-white/5">Log in</a>
          <a href="/register" class="px-3 py-2 rounded-lg text-sm bg-gradient-to-r from-brand-500 to-indigo-500 hover:from-brand-400 hover:to-indigo-400 text-white shadow-soft">Sign up</a>
        </div>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="relative overflow-hidden">
    <div class="absolute inset-0 -z-10 bg-[radial-gradient(ellipse_at_top,rgba(139,92,246,0.25),transparent_60%)]"></div>
    <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-gradient-to-br from-brand-600/30 to-indigo-600/30 blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
      <div class="grid lg:grid-cols-2 gap-10 items-center">
        <div>
          <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight tracking-tight">
            Ship content that ranks.<br>
            <span class="bg-gradient-to-r from-brand-400 to-indigo-400 bg-clip-text text-transparent">Semantic SEO Analyzer 2.0</span>
          </h1>
          <p class="mt-4 text-slate-300 max-w-2xl">
            Instantly audit structure, entities, intent, and technical signals. Identify content gaps, cluster topics, and validate AI-likeness in seconds.
          </p>
          <div class="mt-6 flex flex-wrap gap-3">
            <a href="/semantic-analyzer" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-medium shadow-soft hover:opacity-90">Open Semantic Analyzer</a>
            <a href="/ai-content-checker" class="px-4 py-2 rounded-lg bg-slate-800/60 border border-white/10 hover:bg-slate-800">Try AI Checker</a>
            <a href="/topic-cluster" class="px-4 py-2 rounded-lg bg-slate-800/60 border border-white/10 hover:bg-slate-800">Topic Clusters</a>
          </div>

          <dl class="mt-8 grid grid-cols-2 gap-6 text-sm text-slate-300">
            <div class="p-4 rounded-xl bg-slate-900/40 border border-white/10">
              <dt class="font-semibold text-white">Entity & Topic Core</dt>
              <dd>TF-IDF, NER, keyphrases, semantic coverage.</dd>
            </div>
            <div class="p-4 rounded-xl bg-slate-900/40 border border-white/10">
              <dt class="font-semibold text-white">Structure & Tech SEO</dt>
              <dd>Headings, ratios, links, schema hints.</dd>
            </div>
          </dl>
        </div>

        <!-- Quick Semantic Analyzer Widget -->
        <div id="quick-widget" class="rounded-2xl p-6 bg-slate-900/50 border border-white/10 shadow-soft backdrop-blur-xs">
          <h3 class="text-lg font-semibold">Quick Audit</h3>
          <p class="text-sm text-slate-300 mb-4">Run a lightweight analysis right from the homepage.</p>

          <form id="semanticForm" class="space-y-3">
            <div>
              <label class="text-xs text-slate-400">URL</label>
              <input name="url" type="url" required placeholder="https://example.com/article"
                     class="mt-1 w-full rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-brand-500" />
            </div>
            <div>
              <label class="text-xs text-slate-400">Target keyword (optional)</label>
              <input name="target_keyword" type="text" placeholder="e.g., semantic seo"
                     class="mt-1 w-full rounded-lg bg-slate-800/70 border border-white/10 px-3 py-2 outline-none focus:ring-2 focus:ring-brand-500" />
            </div>
            <button type="submit" id="semanticBtn"
              class="w-full inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2.5 bg-gradient-to-r from-brand-500 to-indigo-500 hover:from-brand-400 hover:to-indigo-400 text-white font-medium shadow-soft">
              <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6l4 2" />
              </svg>
              Analyze
            </button>
          </form>

          <div id="semanticResult" class="mt-4 text-sm hidden">
            <!-- results render here -->
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Three Analyzers -->
  <section class="py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold tracking-tight mb-6">Analyze, detect, and cluster — beautifully</h2>
      <div class="grid md:grid-cols-3 gap-6">
        <!-- Card 1 -->
        <a href="/semantic-analyzer" id="semantic" class="block group rounded-2xl p-6 bg-slate-900/50 border border-white/10 hover:border-brand-500/60 hover:bg-slate-900/70 transition shadow-soft">
          <div class="flex items-center justify-between">
            <h3 class="font-semibold">Semantic SEO Master Analyzer 2.0</h3>
            <span class="text-xs px-2 py-1 rounded bg-brand-500/20 text-brand-200 border border-brand-500/40">Pro</span>
          </div>
          <p class="mt-2 text-slate-300 text-sm">Entities, topics, structure, readability, links, schema & recommendations.</p>
          <div class="mt-4 text-brand-300 text-sm group-hover:translate-x-1 transition">
            Open →
          </div>
        </a>

        <!-- Card 2 -->
        <a href="/ai-content-checker" id="ai-check" class="block group rounded-2xl p-6 bg-slate-900/50 border border-white/10 hover:border-brand-500/60 hover:bg-slate-900/70 transition shadow-soft">
          <h3 class="font-semibold">AI Content Checker</h3>
          <p class="mt-2 text-slate-300 text-sm">Heuristics that estimate AI-likeness with linguistic signals.</p>
          <div class="mt-4 text-brand-300 text-sm group-hover:translate-x-1 transition">
            Try →
          </div>
        </a>

        <!-- Card 3 -->
        <a href="/topic-cluster" id="topic-cluster" class="block group rounded-2xl p-6 bg-slate-900/50 border border-white/10 hover:border-brand-500/60 hover:bg-slate-900/70 transition shadow-soft">
          <h3 class="font-semibold">Topic Cluster Identification</h3>
          <p class="mt-2 text-slate-300 text-sm">TF-IDF terms grouped into clusters for content hubs & briefs.</p>
          <div class="mt-4 text-brand-300 text-sm group-hover:translate-x-1 transition">
            Explore →
          </div>
        </a>
      </div>
    </div>
  </section>

  <!-- Feature Bullets -->
  <section class="pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="grid md:grid-cols-3 gap-6">
        <div class="rounded-2xl p-6 bg-slate-900/40 border border-white/10">
          <h4 class="font-semibold mb-2">Data Acquisition</h4>
          <ul class="text-sm text-slate-300 space-y-1">
            <li>• UA spoofing & redirects</li>
            <li>• Readability extraction</li>
            <li>• JSON-LD & meta parsing</li>
          </ul>
        </div>
        <div class="rounded-2xl p-6 bg-slate-900/40 border border-white/10">
          <h4 class="font-semibold mb-2">Semantic & NLP</h4>
          <ul class="text-sm text-slate-300 space-y-1">
            <li>• Tokenization, lemmatization</li>
            <li>• NER & keyphrase extraction</li>
            <li>• Topic clustering</li>
          </ul>
        </div>
        <div class="rounded-2xl p-6 bg-slate-900/40 border border-white/10">
          <h4 class="font-semibold mb-2">Insights & Tech SEO</h4>
          <ul class="text-sm text-slate-300 space-y-1">
            <li>• Coverage & gaps</li>
            <li>• Headings, ratios, links</li>
            <li>• Actionable recs</li>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <footer class="border-t border-white/10 py-8 text-center text-sm text-slate-400">
    © {{ date('Y') }} Semantic SEO Master Analyzer 2.0 — All rights reserved.
  </footer>

  <!-- Minimal JS for the quick widget -->
  <script>
    async function safeJson(res){
      const text = await res.text();
      try { return [res.ok, JSON.parse(text)]; }
      catch { return [res.ok, { ok:false, error:`HTTP ${res.status} — ${text.slice(0,200)}` }]; }
    }

    const form = document.getElementById('semanticForm');
    const btn  = document.getElementById('semanticBtn');
    const out  = document.getElementById('semanticResult');

    form?.addEventListener('submit', async (e) => {
      e.preventDefault();
      out.classList.remove('hidden');
      out.innerHTML = `<div class="p-3 rounded-lg bg-slate-800/60 border border-white/10">Analyzing…</div>`;
      btn.disabled = true; btn.classList.add('opacity-70','cursor-not-allowed');

      const fd = new FormData(form);
      const payload = {
        url: fd.get('url'),
        target_keyword: (fd.get('target_keyword')||'').trim()
      };

      try {
        const res = await fetch('/api/semantic-analyze', {
          method:'POST',
          headers:{ 'Accept':'application/json','Content-Type':'application/json' },
          body: JSON.stringify(payload)
        });
        const [ok, data] = await safeJson(res);
        if (!ok || !data.ok) throw new Error(data.error || `HTTP ${res.status}`);

        // Render compact summary
        const h = data.content_structure || {};
        const tech = data.technical_seo || { links:{internal:0,external:0} };
        const recs = (data.recommendations||[]).slice(0,4).map(r => `<li><span class="text-xs px-2 py-0.5 rounded bg-white/5 border border-white/10 mr-2">${r.severity}</span>${r.text}</li>`).join('') || '<li>No recommendations</li>';

        out.innerHTML = `
          <div class="rounded-xl border border-white/10 bg-slate-900/50 p-4">
            <div class="flex flex-wrap items-center gap-4">
              <div class="text-3xl font-extrabold">${data.overall_score ?? '—'}</div>
              <div class="text-slate-300">Overall Score</div>
            </div>
            <div class="grid md:grid-cols-2 gap-4 mt-4 text-sm">
              <div class="space-y-1">
                <div><span class="text-slate-400">Title:</span> <span class="text-white">${h.title || '—'}</span></div>
                <div><span class="text-slate-400">Meta:</span> <span class="text-slate-200">${h.meta_description || '—'}</span></div>
                <div class="text-slate-300">Readability: <span class="text-white">${h.readability_flesch ?? '—'}</span> · Text/HTML: <span class="text-white">${h.text_to_html_ratio ?? '—'}%</span></div>
              </div>
              <div class="space-y-1">
                <div class="text-slate-300">Links — Internal: <span class="text-white">${tech.links?.internal ?? 0}</span>, External: <span class="text-white">${tech.links?.external ?? 0}</span></div>
                <div class="text-slate-300">Schema: <span class="text-white">${(tech.structured_data?.json_ld||tech.structured_data?.microdata||tech.structured_data?.rdfa) ? 'Detected' : 'Missing'}</span></div>
              </div>
            </div>
            <div class="mt-4">
              <div class="font-semibold mb-2">Top Recommendations</div>
              <ul class="space-y-1 text-slate-200">${recs}</ul>
            </div>
          </div>
        `;
      } catch (err) {
        out.innerHTML = `<div class="p-3 rounded-lg bg-red-500/10 border border-red-500/30 text-red-200">❌ ${err.message || 'Analysis failed'}</div>`;
      } finally {
        btn.disabled = false; btn.classList.remove('opacity-70','cursor-not-allowed');
      }
    });
  </script>
</body>
</html>
