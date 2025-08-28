<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Semantic SEO Master Analyzer 2.0</title>

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            hotpink:'#ff66c4', lemon:'#ffd166', aqua:'#06d6a0', sky:'#60a5fa', flame:'#fb7185',
            brand:{ 500:'#8b5cf6', 600:'#7c3aed', 700:'#6d28d9' }
          },
          boxShadow: { soft:'0 14px 60px rgba(0,0,0,.22)' },
          animation: { float:'float 7s ease-in-out infinite' },
          keyframes: {
            float:{ '0%,100%':{ transform:'translateY(-6px)' }, '50%':{ transform:'translateY(6px)' } }
          }
        }
      }
    }
  </script>

  <!-- Alpine for the profile dropdown -->
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    /* Colorful background (rich gradients) */
    body{
      background:
        radial-gradient(1200px 700px at -10% -10%, rgba(255,102,196,.32), transparent 60%),
        radial-gradient(1000px 600px at 110% 0%, rgba(96,165,250,.30), transparent 60%),
        radial-gradient(1400px 900px at 50% 120%, rgba(6,214,160,.22), transparent 65%),
        linear-gradient(120deg, #0b1020, #0b0f1a 50%, #0a0c18);
    }
    .glass{ background: linear-gradient(180deg, rgba(15,23,42,.65), rgba(2,6,23,.55)); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(10px); }
    .chip{ font-size:.7rem; padding:.2rem .5rem; border-radius:.5rem; border:1px solid rgba(255,255,255,.18); background: rgba(255,255,255,.06); }
    #bgCanvas{ position:fixed; inset:0; z-index:-1; width:100%; height:100%; pointer-events:none; }
    [x-cloak]{ display:none !important; }
  </style>
</head>
<body class="text-slate-100 antialiased overflow-x-hidden">
  <canvas id="bgCanvas"></canvas>

  <!-- NAV -->
  <header class="sticky top-0 z-40 border-b border-white/10 bg-black/35 backdrop-blur-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="flex items-center gap-3 group">
          <span class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-hotpink to-sky shadow-soft">
            <svg class="h-5 w-5 text-white transition-transform group-hover:rotate-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v3m0 12v3m9-9h-3M6 12H3m14.95 5.657-2.121-2.121M7.172 7.172 5.05 5.05m12.728 0-2.122 2.122M7.172 16.828 5.05 18.95"/>
            </svg>
          </span>
          <div class="leading-tight">
            <div class="font-bold tracking-tight">Semantic SEO Master</div>
            <div class="text-xs text-slate-300">Analyzer 2.0</div>
          </div>
        </a>

        <!-- Use smart alias routes that auto-redirect guests to login -->
        <nav class="hidden md:flex items-center gap-6 text-sm">
          <a href="{{ route('semantic') }}" class="hover:text-white">Semantic Analyzer</a>
          <a href="{{ route('aiChecker') }}" class="hover:text-white">AI Content Checker</a>
          <a href="{{ route('topicCluster') }}" class="hover:text-white">Topic Cluster</a>
          <a href="#about" class="hover:text-white">About</a>
          <a href="#features" class="hover:text-white">Features</a>
        </nav>

        <div class="flex items-center gap-2">
          @guest
            <a href="{{ route('login') }}" class="px-3 py-2 rounded-lg text-sm hover:bg-white/10">Log in</a>
            <a href="{{ route('register') }}" class="px-3 py-2 rounded-lg text-sm bg-gradient-to-r from-hotpink to-sky text-white shadow-soft">Sign up</a>
          @else
            <!-- Profile dropdown -->
            <div x-data="{open:false}" class="relative" x-cloak>
              <button @click="open=!open" class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-white/10">
                @php
                  $u = auth()->user();
                  $avatar = $u->avatar_path ? asset('storage/'.$u->avatar_path) : null;
                  $initials = strtoupper(mb_substr($u->name ?? 'U',0,1));
                @endphp
                @if($avatar)
                  <img src="{{ $avatar }}" alt="{{ $u->name }} avatar" class="h-8 w-8 rounded-full object-cover border border-white/20">
                @else
                  <div class="h-8 w-8 rounded-full grid place-items-center bg-gradient-to-br from-brand-500 to-sky">
                    <span class="text-xs font-bold">{{ $initials }}</span>
                  </div>
                @endif
                <span class="text-sm hidden sm:inline">{{ $u->name }}</span>
                <svg class="h-4 w-4 text-slate-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><path stroke-width="1.5" d="M6 9l6 6 6-6"/></svg>
              </button>
              <div x-show="open" @click.away="open=false" x-transition
                   class="absolute right-0 mt-2 w-56 glass rounded-xl p-2 shadow-soft border border-white/10">
                @if(\Illuminate\Support\Facades\Route::has('profile.edit'))
                  <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-white/10">Profile & Settings</a>
                @else
                  <a href="/profile" class="block px-3 py-2 text-sm rounded-lg hover:bg-white/10">Profile & Settings</a>
                @endif
                <a href="{{ route('dashboard') }}" class="block px-3 py-2 text-sm rounded-lg hover:bg-white/10">Dashboard</a>
                <form method="POST" action="{{ route('logout') }}" class="mt-1">
                  @csrf
                  <button type="submit" class="w-full text-left px-3 py-2 text-sm rounded-lg hover:bg-white/10 text-red-300">Log out</button>
                </form>
              </div>
            </div>
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
          <span class="chip">Powerful • Colorful • Insightful</span>
          <h1 class="text-4xl sm:text-5xl font-extrabold leading-tight mt-3">
            Publish content that <span class="bg-gradient-to-r from-hotpink via-lemon to-aqua bg-clip-text text-transparent">wins</span> topic clusters.
          </h1>
          <p class="mt-4 text-slate-200/90 max-w-2xl">
            Semantic SEO Master Analyzer 2.0 turns pages into structured knowledge: entities, topics, intent, links, schema, and gaps—served as actionable steps.
          </p>
          <div class="mt-6 flex flex-wrap gap-3">
            @guest
              <a href="{{ route('register') }}" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold shadow-soft hover:opacity-90">Create account</a>
              <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Sign in to use tools</a>
            @else
              <a href="{{ route('dashboard') }}" class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold shadow-soft hover:opacity-90">Open Dashboard</a>
              <a href="{{ route('semantic') }}" class="px-4 py-2 rounded-lg glass hover:bg-white/5">Semantic Analyzer</a>
            @endguest
          </div>
        </div>

        <!-- Animated hero card -->
        <div class="relative">
          <div class="absolute -inset-8 -z-10 blur-3xl rounded-3xl opacity-70 bg-gradient-to-br from-hotpink/35 via-sky/30 to-aqua/30"></div>
          <div class="glass rounded-3xl p-6 border border-white/10 shadow-soft animate-float">
            <div class="flex items-center justify-between">
              <h3 class="text-xl font-semibold">Semantic Snapshot</h3>
              <span class="chip">Real-time</span>
            </div>
            <div class="grid sm:grid-cols-3 gap-4 mt-4 text-sm">
              <div class="rounded-xl p-4 bg-white/5 border border-white/10">
                <div class="text-lemon font-semibold">Entities</div>
                <div class="text-slate-300 mt-1">Topical graph, NER, keyphrases</div>
              </div>
              <div class="rounded-xl p-4 bg-white/5 border border-white/10">
                <div class="text-sky font-semibold">Structure</div>
                <div class="text-slate-300 mt-1">Headings, links, anchors</div>
              </div>
              <div class="rounded-xl p-4 bg-white/5 border border-white/10">
                <div class="text-hotpink font-semibold">Tech SEO</div>
                <div class="text-slate-300 mt-1">Meta, schema, ratios</div>
              </div>
            </div>
            <div class="mt-4 text-slate-300 text-xs">Includes coverage score, intent match, and prioritized recommendations.</div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ABOUT -->
  <section id="about" class="py-12 sm:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-3xl font-bold tracking-tight">About <span class="text-lemon">Semantic SEO</span></h2>
      <div class="mt-6 grid md:grid-cols-3 gap-6">
        <article class="p-6 rounded-2xl glass border border-white/10 shadow-soft">
          <div class="text-hotpink font-semibold text-sm">Concepts over Keywords</div>
          <h3 class="text-xl font-semibold text-white mt-1">Model meanings, not strings</h3>
          <p class="mt-2 text-slate-200/90 text-sm">
            Build topical authority by covering entities and relationships. Answer adjacent questions and align with searcher intent.
          </p>
        </article>
        <article class="p-6 rounded-2xl glass border border-white/10 shadow-soft">
          <div class="text-sky font-semibold text-sm">Signals that Matter</div>
          <h3 class="text-xl font-semibold text-white mt-1">Structure & context</h3>
          <p class="mt-2 text-slate-200/90 text-sm">
            Heading hierarchy, descriptive anchors, internal linking, and valid schema clarify your page within the topic graph.
          </p>
        </article>
        <article class="p-6 rounded-2xl glass border border-white/10 shadow-soft">
          <div class="text-aqua font-semibold text-sm">Coverage & Gaps</div>
          <h3 class="text-xl font-semibold text-white mt-1">Complete the cluster</h3>
          <p class="mt-2 text-slate-200/90 text-sm">
            Compare semantic coverage against competing pages to identify missing subtopics and sections worth adding.
          </p>
        </article>
      </div>
    </div>
  </section>

  <!-- FEATURES GRID -->
  <section id="features" class="pb-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <h2 class="text-2xl font-bold tracking-tight mb-6">Feature highlights</h2>
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @php
          $features = [
            ['c'=>'from-hotpink to-lemon','t'=>'Entity & Topic Core','d'=>'TF-IDF, NER, keyphrases, topic cloud.'],
            ['c'=>'from-sky to-brand-500','t'=>'Structure & Links','d'=>'Heading map, skipped levels, anchor semantics.'],
            ['c'=>'from-aqua to-sky','t'=>'Technical SEO','d'=>'Meta checks, text/HTML ratio, schema presence.'],
            ['c'=>'from-lemon to-hotpink','t'=>'Intent Classifier','d'=>'Informational / commercial / navigational / transactional.'],
            ['c'=>'from-brand-500 to-sky','t'=>'Coverage Score','d'=>'Compare against cluster to find gaps.'],
            ['c'=>'from-flame to-hotpink','t'=>'Actionable Recs','d'=>'Prioritized strengths, warnings, and critical gaps.'],
          ];
        @endphp
        @foreach($features as $f)
          <div class="p-6 rounded-2xl glass border border-white/10 shadow-soft">
            <div class="h-10 w-10 rounded-lg bg-gradient-to-br {{ $f['c'] }} mb-3"></div>
            <h3 class="font-semibold text-white">{{ $f['t'] }}</h3>
            <p class="text-sm text-slate-200/90 mt-1">{{ $f['d'] }}</p>
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

  <!-- Tech-lines animation -->
  <script>
    (() => {
      const c = document.getElementById('bgCanvas');
      if (!c) return;
      const ctx = c.getContext('2d');
      let dpr = Math.min(2, window.devicePixelRatio || 1);
      let w=0,h=0,t=0,mx=.5,my=.5;
      function size(){ w=c.clientWidth; h=c.clientHeight; c.width=w*dpr; c.height=h*dpr; ctx.setTransform(dpr,0,0,dpr,0,0); }
      function lerp(a,b,x){ return a+(b-a)*x; }
      function draw(){
        t+=0.016; ctx.clearRect(0,0,w,h);
        const lines=Math.max(12,Math.floor(h/55)); const sp=h/(lines-1); const amp=lerp(10,44,my); const freq=0.012; const speed=lerp(.6,1.4,mx);
        for(let i=0;i<lines;i++){
          const y0=i*sp;
          const g=ctx.createLinearGradient(0,y0-20,w,y0+20);
          g.addColorStop(0,'rgba(255,102,196,0.25)'); g.addColorStop(.5,'rgba(96,165,250,0.45)'); g.addColorStop(1,'rgba(6,214,160,0.25)');
          ctx.strokeStyle=g; ctx.lineWidth=2+Math.sin((i/lines)*Math.PI)*1.4; ctx.beginPath();
          for(let x=-30;x<=w+30;x+=70){
            const y=y0 + Math.sin((x+t*70*speed+i*8)*freq)*amp + Math.cos((x*freq*.8)+t*2)*(mx*10);
            if(x===-30) ctx.moveTo(x,y); else ctx.lineTo(x,y);
          } ctx.stroke();
        } requestAnimationFrame(draw);
      }
      window.addEventListener('resize', size, {passive:true});
      window.addEventListener('pointermove', e=>{ const r=c.getBoundingClientRect(); mx=(e.clientX-r.left)/r.width; my=(e.clientY-r.top)/r.height; }, {passive:true});
      size(); draw();
    })();
  </script>
</body>
</html>
