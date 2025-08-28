<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Semantic SEO Master Analyzer 2.0</title>

  <!-- Tailwind (CDN) -->
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
          boxShadow: { soft: '0 10px 40px rgba(0,0,0,.15)' }
        }
      }
    }
  </script>

  <style>
    /* Subtle gradient glow behind canvas */
    .bg-neon {
      background:
        radial-gradient(1200px 600px at 10% -10%, rgba(139,92,246,.25), transparent 60%),
        radial-gradient(900px 500px at 110% 10%, rgba(79,70,229,.25), transparent 60%),
        radial-gradient(900px 900px at 50% 120%, rgba(14,165,233,.20), transparent 60%),
        #0b0f1a;
    }
    /* Glass card effect */
    .glass {
      background: linear-gradient(180deg, rgba(15,23,42,.65), rgba(2,6,23,.55));
      border: 1px solid rgba(255,255,255,.08);
      backdrop-filter: blur(8px);
    }
  </style>
</head>
<body class="text-slate-100 bg-neon min-h-screen relative overflow-x-hidden">

  <!-- Animated tech-lines canvas -->
  <div class="pointer-events-none absolute inset-0 -z-10">
    <canvas id="techLines" class="w-full h-full"></canvas>
  </div>

  <!-- Header / Nav -->
  <header class="sticky top-0 z-40 border-b border-white/10 bg-black/30 backdrop-blur-md">
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
          <!-- Tools require auth; send guests to login -->
          @auth
            <a href="/semantic-analyzer" class="hover:text-white text-slate-300">Semantic Analyzer</a>
            <a href="/ai-content-checker" class="hover:text-white text-slate-300">AI Content Checker</a>
            <a href="/topic-cluster" class="hover:text-white text-slate-300">Topic Cluster</a>
          @else
            <a href="/login" class="hover:text-white text-slate-300">Semantic Analyzer</a>
            <a href="/login" class="hover:text-white text-slate-300">AI Content Checker</a>
            <a href="/login" class="hover:text-white text-slate-300">Topic Cluster</a>
          @endauth
          <a href="#about" class="hover:text-white text-slate-300">About</a>
          <a href="#features" class="hover:text-white text-slate-300">Features</a>
        </nav>

        <div class="flex items-center gap-2">
          @guest
            <a href="/login" class="px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-white/5">Log in</a>
            <a href="/register" class="px-3 py-2 rounded-lg text-sm bg-gradient-to-r from-brand-500 to-indigo-500 hover:from-brand-400 hover:to-indigo-400 text-white shadow-soft">Sign up</a>
          @else
            <a href="/dashboard" class="px-3 py-2 rounded-lg text-sm text-slate-200 hover:bg-white/5">Dashboard</a>
          @endguest
        </div>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div>
          <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight tracking-tight">
            Build content that <span class="bg-gradient-to-r from-brand-400 to-indigo-300 bg-clip-text text-transparent">earns rankings</span>.
          </h1>
          <p class="mt-4 text-slate-300 max-w-2xl">
            Semantic SEO Master Analyzer 2.0 transforms unstructured pages into structured insight. It surfaces entities, topics, and intent, validates technical signals, and reveals the gaps that keep you off page one.
          </p>
          <div class="mt-6 flex flex-wrap gap-3">
            @guest
              <a href="/register" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-medium shadow-soft hover:opacity-90">Get started — free</a>
              <a href="/login" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Sign in</a>
            @else
              <a href="/semantic-analyzer" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-medium shadow-soft hover:opacity-90">Open Semantic Analyzer</a>
              <a href="/dashboard" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Go to Dashboard</a>
            @endguest
          </div>
          <dl class="mt-8 grid grid-cols-2 gap-6 text-sm text-slate-300">
            <div class="p-4 rounded-xl glass">
              <dt class="font-semibold text-white">Entity Intelligence</dt>
              <dd>Extract key entities & topics with TF-IDF, NER, and keyphrase scoring.</dd>
            </div>
            <div class="p-4 rounded-xl glass">
              <dt class="font-semibold text-white">Structure & Signals</dt>
              <dd>Headings, internal/external links, meta, schema hints & ratios.</dd>
            </div>
          </dl>
        </div>

        <!-- Visual card stack -->
        <div class="relative">
          <div class="absolute -inset-6 -z-10 bg-gradient-to-br from-brand-500/20 via-indigo-500/10 to-cyan-400/10 blur-2xl rounded-3xl"></div>
          <div class="rotate-3 translate-y-4 rounded-2xl border border-white/10 glass p-5 shadow-soft">
            <div class="text-xs text-slate-300">Overview</div>
            <div class="mt-2 text-2xl font-bold">Semantic Snapshot</div>
            <ul class="mt-4 space-y-2 text-sm text-slate-300">
              <li>• Primary Topics & Entities</li>
              <li>• Heading Hierarchy Map</li>
              <li>• Link Context Summary</li>
            </ul>
          </div>
          <div class="-rotate-2 -translate-y-6 ml-10 mt-5 rounded-2xl border border-white/10 glass p-5 shadow-soft">
            <div class="text-xs text-slate-300">Insights</div>
            <div class="mt-2 text-2xl font-bold">Actionable Recs</div>
            <ul class="mt-4 space-y-2 text-sm text-slate-300">
              <li>• Missing sections & questions</li>
              <li>• Title & meta improvements</li>
              <li>• Schema & coverage gaps</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ABOUT (rich copy in stylish boxes) -->
  <section id="about" class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold tracking-tight">What is <span class="text-brand-300">Semantic SEO</span>?</h2>

      <div class="mt-6 grid lg:grid-cols-3 gap-6">
        <article class="p-6 rounded-2xl glass">
          <h3 class="text-xl font-semibold">From Keywords → Concepts</h3>
          <p class="mt-2 text-slate-300 text-sm leading-relaxed">
            Semantic SEO focuses on meanings, not just exact-match terms. It models how topics, entities, and relationships form a knowledge layer that search engines use to interpret relevance and intent.
          </p>
        </article>

        <article class="p-6 rounded-2xl glass">
          <h3 class="text-xl font-semibold">Signals Search Understands</h3>
          <p class="mt-2 text-slate-300 text-sm leading-relaxed">
            Well-structured headings, descriptive anchors, consistent entities, and valid schema mark up your content for discovery. These signals anchor your page within a topic graph—boosting topical authority.
          </p>
        </article>

        <article class="p-6 rounded-2xl glass">
          <h3 class="text-xl font-semibold">Coverage & Intent Fit</h3>
          <p class="mt-2 text-slate-300 text-sm leading-relaxed">
            Semantic SEO rewards completeness. By covering subtopics, answering adjacent questions, and matching the searcher’s intent, you reduce ambiguity and increase the likelihood of ranking for clusters—not just a term.
          </p>
        </article>
      </div>
    </div>
  </section>

  <!-- FEATURES GRID -->
  <section id="features" class="pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold tracking-tight mb-6">What you’ll get with Analyzer 2.0</h2>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="p-6 rounded-2xl glass">
          <div class="text-sm text-brand-300">Semantic Core</div>
          <h3 class="font-semibold mt-1">Entities & Topic Cloud</h3>
          <p class="text-sm text-slate-300 mt-2">TF-IDF weighting, NER, and keyphrase extraction surface the core concepts your content communicates.</p>
        </div>

        <div class="p-6 rounded-2xl glass">
          <div class="text-sm text-brand-300">Structure</div>
          <h3 class="font-semibold mt-1">Headings & Link Context</h3>
          <p class="text-sm text-slate-300 mt-2">Map heading hierarchy, spot skipped levels, and analyze internal/external links with anchor semantics.</p>
        </div>

        <div class="p-6 rounded-2xl glass">
          <div class="text-sm text-brand-300">Technical SEO</div>
          <h3 class="font-semibold mt-1">Meta & Schema Checks</h3>
          <p class="text-sm text-slate-300 mt-2">Title/meta length, presence audits, text-to-HTML ratio, and structured data detection.</p>
        </div>

        <div class="p-6 rounded-2xl glass">
          <div class="text-sm text-brand-300">Intent</div>
          <h3 class="font-semibold mt-1">Search Intent Classifier</h3>
          <p class="text-sm text-slate-300 mt-2">Assess informational, commercial, navigational, or transactional fit to align with user journeys.</p>
        </div>

        <div class="p-6 rounded-2xl glass">
          <div class="text-sm text-brand-300">Competitive</div>
          <h3 class="font-semibold mt-1">Content Gap Analysis</h3>
          <p class="text-sm text-slate-300 mt-2">Compare coverage vs. competitors to identify missing entities, FAQs, and subtopics worth adding.</p>
        </div>

        <div class="p-6 rounded-2xl glass">
          <div class="text-sm text-brand-300">Insights</div>
          <h3 class="font-semibold mt-1">Actionable Recommendations</h3>
          <p class="text-sm text-slate-300 mt-2">Prioritized fixes & opportunities—from headings to schema—to improve clarity and topical authority.</p>
        </div>
      </div>

      <div class="mt-10 flex flex-wrap gap-3">
        @guest
          <a href="/register" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-medium shadow-soft hover:opacity-90">Create account to use tools</a>
          <a href="/login" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Sign in</a>
        @else
          <a href="/semantic-analyzer" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-medium shadow-soft hover:opacity-90">Open Semantic Analyzer</a>
          <a href="/dashboard" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Dashboard</a>
        @endguest
      </div>
    </div>
  </section>

  <footer class="border-t border-white/10 py-8 text-center text-sm text-slate-400">
    © {{ date('Y') }} Semantic SEO Master Analyzer 2.0 — All rights reserved.
  </footer>

  <!-- Canvas Animation: BIG “tech lines” that dance with the mouse -->
  <script>
    (function(){
      const canvas = document.getElementById('techLines');
      if (!canvas) return;
      const ctx = canvas.getContext('2d');
      let w, h, dpr;
      let time = 0;
      let mouseX = 0.5, mouseY = 0.5;
      let paused = false;

      const setup = () => {
        dpr = Math.max(1, Math.min(2, window.devicePixelRatio || 1));
        w = canvas.clientWidth;
        h = canvas.clientHeight;
        canvas.width = Math.floor(w * dpr);
        canvas.height = Math.floor(h * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        ctx.lineCap = 'round';
      };

      const lerp = (a,b,t)=>a+(b-a)*t;
      const clamp = (n,min,max)=>Math.max(min,Math.min(max,n));

      // Draw thick neon diagonal wave lines, responsive to pointer
      const draw = (t) => {
        if (paused) return;
        time += 0.016;
        ctx.clearRect(0,0,w,h);

        // background subtle vignette
        const grd = ctx.createLinearGradient(0,0,w,h);
        grd.addColorStop(0,'rgba(124,58,237,0.06)');
        grd.addColorStop(1,'rgba(14,165,233,0.04)');
        ctx.fillStyle = grd;
        ctx.fillRect(0,0,w,h);

        const lines = Math.max(10, Math.floor(h / 60)); // big, spaced lines
        const spacing = h / (lines-1);
        const baseAmp = lerp(12, 48, mouseY); // bigger waves when mouse low
        const speed = lerp(0.6, 1.4, mouseX);
        const freq = 0.012;
        const seg = 80; // segment length

        for (let i=0;i<lines;i++){
          const y0 = i*spacing;
          // neon stroke
          const g = ctx.createLinearGradient(0, y0-20, w, y0+20);
          g.addColorStop(0, 'rgba(139,92,246,0.35)');
          g.addColorStop(0.5, 'rgba(99,102,241,0.55)');
          g.addColorStop(1, 'rgba(34,211,238,0.35)');
          ctx.strokeStyle = g;

          ctx.lineWidth = lerp(1.6, 3.2, Math.sin((i/lines)*Math.PI)); // “bigger lines”
          ctx.beginPath();
          for (let x=-40; x<=w+40; x+=seg){
            const phase = (i*0.9) + (time*60*speed);
            const offset = Math.sin((x+phase)*freq) * baseAmp;
            const mouseWobble = Math.cos((x*freq*0.8) + time*2) * (mouseX*10);
            const y = y0 + offset + mouseWobble;
            if (x===-40) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
          }
          ctx.stroke();
        }

        // subtle glows crossing
        ctx.globalCompositeOperation = 'lighter';
        ctx.fillStyle = 'rgba(99,102,241,0.04)';
        ctx.beginPath();
        ctx.ellipse(w*mouseX, h*mouseY, 220, 120, 0, 0, Math.PI*2);
        ctx.fill();
        ctx.globalCompositeOperation = 'source-over';

        requestAnimationFrame(draw);
      };

      const onMove = (e) => {
        const r = canvas.getBoundingClientRect();
        const x = (e.clientX - r.left) / r.width;
        const y = (e.clientY - r.top) / r.height;
        mouseX = clamp(x,0,1);
        mouseY = clamp(y,0,1);
      };

      // resize & RDM
      window.addEventListener('resize', () => { setup(); }, {passive:true});
      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        paused = true;
      }

      setup();
      requestAnimationFrame(draw);
      window.addEventListener('pointermove', onMove, {passive:true});
      window.addEventListener('pointerdown', onMove, {passive:true});
    })();
  </script>
</body>
</html>
