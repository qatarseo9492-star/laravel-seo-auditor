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
            brand: { 500:'#8b5cf6', 600:'#7c3aed', 700:'#6d28d9' },
            hotpink:'#ff66c4', lemon:'#ffd166', aqua:'#06d6a0', sky:'#60a5fa', flame:'#fb7185'
          },
          boxShadow: { soft:'0 12px 50px rgba(0,0,0,.18)' },
          animation: {
            float:'float 6s ease-in-out infinite',
            float2:'float 7s ease-in-out infinite',
            float3:'float 8s ease-in-out infinite',
          },
          keyframes: {
            float:{ '0%,100%':{ transform:'translateY(-6px)' }, '50%':{ transform:'translateY(6px)' } },
            float2:{ '0%,100%':{ transform:'translateY(-10px) translateX(-6px)' }, '50%':{ transform:'translateY(10px) translateX(6px)' } },
            float3:{ '0%,100%':{ transform:'translateY(-8px) translateX(6px)' }, '50%':{ transform:'translateY(8px) translateX(-6px)' } },
          }
        }
      }
    }
  </script>

  <style>
    /* Rich, colorful background similar vibe to the Pinterest reference */
    body {
      background:
        radial-gradient(1200px 700px at -10% -10%, rgba(255,102,196,.35), transparent 60%),
        radial-gradient(1000px 600px at 110% 0%, rgba(96,165,250,.35), transparent 60%),
        radial-gradient(1400px 900px at 50% 120%, rgba(6,214,160,.28), transparent 65%),
        linear-gradient(120deg, #0b1020, #0b0f1a 50%, #0a0c18);
    }
    /* Glass card */
    .glass { background: linear-gradient(180deg, rgba(15,23,42,.65), rgba(2,6,23,.55)); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(8px); }
    /* Canvas sits behind */
    #bgCanvas { position: fixed; inset: 0; z-index: -1; width:100%; height:100%; pointer-events:none; }
  </style>
</head>
<body class="text-slate-100 antialiased overflow-x-hidden">

  <!-- Animated tech lines canvas -->
  <canvas id="bgCanvas"></canvas>

  <!-- NAV -->
  <header class="sticky top-0 z-40 border-b border-white/10 bg-black/40 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="h-16 flex items-center justify-between">
        <a href="/" class="flex items-center gap-3 group">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-sky-500 shadow-soft">
            <!-- colorful logo spark -->
            <svg class="h-5 w-5 text-white transition-transform group-hover:rotate-12" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v3m0 12v3m9-9h-3M6 12H3m14.95 5.657-2.121-2.121M7.172 7.172 5.05 5.05m12.728 0-2.122 2.122M7.172 16.828 5.05 18.95"/>
            </svg>
          </span>
          <div class="leading-tight">
            <div class="font-bold tracking-tight">Semantic SEO Master</div>
            <div class="text-xs text-slate-300">Analyzer 2.0</div>
          </div>
        </a>

        <nav class="hidden md:flex items-center gap-6 text-sm">
          <!-- Tools require auth — clicking as guest goes to login -->
          @guest
            <a href="{{ route('login') }}" class="hover:text-white text-slate-200">Semantic Analyzer</a>
            <a href="{{ route('login') }}" class="hover:text-white text-slate-200">AI Content Checker</a>
            <a href="{{ route('login') }}" class="hover:text-white text-slate-200">Topic Cluster</a>
          @else
            <a href="{{ route('semantic.analyzer') }}" class="hover:text-white text-slate-200">Semantic Analyzer</a>
            <a href="{{ route('ai.checker') }}" class="hover:text-white text-slate-200">AI Content Checker</a>
            <a href="{{ route('topic.cluster') }}" class="hover:text-white text-slate-200">Topic Cluster</a>
          @endguest
          <a href="#about" class="hover:text-white text-slate-200">About</a>
          <a href="#features" class="hover:text-white text-slate-200">Features</a>
        </nav>

        <div class="flex items-center gap-2">
          @guest
            <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg text-sm text-slate-100 hover:bg-white/10">Log in</a>
            <a href="{{ route('register') }}" class="px-3 py-2 rounded-lg text-sm bg-gradient-to-r from-hotpink to-sky-500 hover:from-flame hover:to-lemon text-white shadow-soft">Sign up</a>
          @else
            <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm text-slate-100 hover:bg-white/10">Dashboard</a>
          @endguest
        </div>
      </div>
    </div>
  </header>

  <!-- HERO -->
  <section class="relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">
      <div class="grid lg:grid-cols-2 gap-12 items-center">
        <div>
          <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight">
            A <span class="bg-gradient-to-r from-hotpink via-lemon to-aqua bg-clip-text text-transparent">colorful</span> way to build content that ranks.
          </h1>
          <p class="mt-4 text-slate-200/90 max-w-2xl">
            Semantic SEO Master Analyzer 2.0 reveals entities, topics, intent, and technical signals—so you can publish content that wins clusters, not just keywords.
          </p>
          <div class="mt-6 flex flex-wrap gap-3">
            @guest
              <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold shadow-soft hover:opacity-90">Create account</a>
              <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Sign in to use tools</a>
            @else
              <a href="{{ route('semantic.analyzer') }}" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold shadow-soft hover:opacity-90">Open Analyzer</a>
              <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Dashboard</a>
            @endguest
          </div>
        </div>

        <!-- Animated colorful icons cluster -->
        <div class="relative min-h-[300px]">
          <div class="absolute -inset-8 -z-10 blur-3xl rounded-3xl opacity-70 bg-gradient-to-br from-hotpink/40 via-sky-500/30 to-aqua/30"></div>

          <!-- Icon 1 -->
          <div class="absolute left-6 top-2 animate-float">
            <div class="h-16 w-16 rounded-2xl bg-gradient-to-br from-hotpink to-lemon grid place-items-center shadow-soft">
              <svg class="h-8 w-8 text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2a10 10 0 100 20 10 10 0 000-20Zm1 11.59V7h-2v8h6v-2h-4z"/>
              </svg>
            </div>
          </div>
          <!-- Icon 2 -->
          <div class="absolute right-10 top-14 animate-float2">
            <div class="h-14 w-14 rounded-2xl bg-gradient-to-br from-sky-500 to-brand-500 grid place-items-center shadow-soft rotate-6">
              <svg class="h-7 w-7 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 3l4 8H8l4-8Zm0 18a6 6 0 100-12 6 6 0 000 12Z"/>
              </svg>
            </div>
          </div>
          <!-- Icon 3 -->
          <div class="absolute left-24 bottom-6 animate-float3">
            <div class="h-20 w-20 rounded-3xl bg-gradient-to-br from-aqua to-sky-500 grid place-items-center shadow-soft -rotate-3">
              <svg class="h-9 w-9 text-slate-900" viewBox="0 0 24 24" fill="currentColor">
                <path d="M3 12l7-9 7 9-7 9-7-9Zm7 0l7-9 4 5-7 9-4-5Z"/>
              </svg>
            </div>
          </div>
          <!-- Icon 4 -->
          <div class="absolute right-24 bottom-0 animate-float">
            <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-flame to-hotpink grid place-items-center shadow-soft">
              <svg class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 6l-7 7h4v5h6v-5h4l-7-7z"/>
              </svg>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ABOUT: colorful boxes -->
  <section id="about" class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold tracking-tight">About <span class="text-lemon">Semantic SEO</span></h2>
      <div class="mt-6 grid md:grid-cols-3 gap-6">
        <article class="p-6 rounded-2xl glass shadow-soft border border-white/10">
          <h3 class="text-xl font-semibold text-white">Concepts over Keywords</h3>
          <p class="mt-2 text-slate-200/90 text-sm leading-relaxed">
            Search is semantic. It understands relationships between entities, questions, and subtopics—beyond exact matches.
          </p>
        </article>
        <article class="p-6 rounded-2xl glass shadow-soft border border-white/10">
          <h3 class="text-xl font-semibold text-white">Signals that Matter</h3>
          <p class="mt-2 text-slate-200/90 text-sm leading-relaxed">
            Headings, anchors, schema, and internal links build a topic graph that clarifies relevance and intent.
          </p>
        </article>
        <article class="p-6 rounded-2xl glass shadow-soft border border-white/10">
          <h3 class="text-xl font-semibold text-white">Coverage & Authority</h3>
          <p class="mt-2 text-slate-200/90 text-sm leading-relaxed">
            Cover clusters fully, answer adjacent questions, and earn authority that lifts every page in the hub.
          </p>
        </article>
      </div>
    </div>
  </section>

  <!-- FEATURES: icon boxes -->
  <section id="features" class="pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold tracking-tight mb-6">What you get</h2>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
          $features = [
            ['title'=>'Entity & Topic Core','desc'=>'TF-IDF, NER, keyphrases & topic cloud.','grad'=>'from-hotpink to-lemon'],
            ['title'=>'Structure & Links','desc'=>'Heading map, skipped levels, anchor context.','grad'=>'from-sky-400 to-brand-500'],
            ['title'=>'Technical SEO','desc'=>'Meta checks, text/HTML ratio, schema presence.','grad'=>'from-aqua to-sky-500'],
            ['title'=>'Intent Classifier','desc'=>'Informational, commercial, navigational, transactional.','grad'=>'from-lemon to-hotpink'],
            ['title'=>'Coverage Score','desc'=>'Compare against topic cluster & find gaps.','grad'=>'from-brand-500 to-sky-500'],
            ['title'=>'Actionable Recs','desc'=>'Prioritized strengths, warnings, and critical gaps.','grad'=>'from-flame to-hotpink'],
          ];
        @endphp
        @foreach($features as $f)
          <div class="p-6 rounded-2xl glass border border-white/10 shadow-soft">
            <div class="h-10 w-10 rounded-lg bg-gradient-to-br {{ $f['grad'] }} grid place-items-center mb-3 animate-pulse"></div>
            <h3 class="font-semibold text-white">{{ $f['title'] }}</h3>
            <p class="text-sm text-slate-200/90 mt-1">{{ $f['desc'] }}</p>
          </div>
        @endforeach
      </div>

      <div class="mt-10 flex flex-wrap gap-3">
        @guest
          <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold shadow-soft hover:opacity-90">Create account to use tools</a>
          <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Sign in</a>
        @else
          <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold shadow-soft hover:opacity-90">Go to Dashboard</a>
        @endguest
      </div>
    </div>
  </section>

  <footer class="border-t border-white/10 py-8 text-center text-sm text-slate-300">
    © {{ date('Y') }} Semantic SEO Master Analyzer 2.0 — All rights reserved.
  </footer>

  <!-- Tech-lines animation (parallax with mouse) -->
  <script>
    (() => {
      const c = document.getElementById('bgCanvas');
      if (!c) return;
      const ctx = c.getContext('2d');
      let dpr = Math.min(2, window.devicePixelRatio || 1);
      let w = 0, h = 0, t = 0, mx = .5, my = .5;

      function size() {
        w = c.clientWidth; h = c.clientHeight;
        c.width = Math.floor(w * dpr); c.height = Math.floor(h * dpr);
        ctx.setTransform(dpr,0,0,dpr,0,0);
      }
      function lerp(a,b,x){ return a+(b-a)*x; }
      function draw() {
        t += 0.016;
        ctx.clearRect(0,0,w,h);
        const lines = Math.max(12, Math.floor(h/55));
        const sp = h/(lines-1);
        const amp = lerp(10, 44, my);
        const freq = 0.012;
        const speed = lerp(0.6,1.4,mx);

        for (let i=0;i<lines;i++){
          const y0 = i*sp;
          const g = ctx.createLinearGradient(0,y0-20,w,y0+20);
          g.addColorStop(0,'rgba(255,102,196,0.25)');
          g.addColorStop(0.5,'rgba(96,165,250,0.45)');
          g.addColorStop(1,'rgba(6,214,160,0.25)');
          ctx.strokeStyle = g;
          ctx.lineWidth = 2 + Math.sin((i/lines)*Math.PI)*1.4;
          ctx.beginPath();
          for (let x=-30;x<=w+30;x+=70){
            const y = y0 + Math.sin((x + t*70*speed + i*8)*freq)*amp + Math.cos((x*freq*.8)+t*2)*(mx*10);
            if (x===-30) ctx.moveTo(x,y); else ctx.lineTo(x,y);
          }
          ctx.stroke();
        }
        requestAnimationFrame(draw);
      }

      window.addEventListener('resize', size, {passive:true});
      window.addEventListener('pointermove', e => {
        const r = c.getBoundingClientRect();
        mx = Math.max(0, Math.min(1, (e.clientX - r.left)/r.width));
        my = Math.max(0, Math.min(1, (e.clientY - r.top)/r.height));
      }, {passive:true});

      size(); draw();
    })();
  </script>
</body>
</html>
