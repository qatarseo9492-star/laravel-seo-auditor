<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width,initial-scale=1"/>
  <title>@yield('title','Semantic SEO')</title>

  <script src="https://cdn.tailwindcss.com"></script>

  <style>
    /* Hide any legacy canvases */
    canvas#bgCanvas, canvas#techCanvas { display:none !important; }

    /* Dark neon base + subtle radial lights */
    body{
      background:
        radial-gradient(80vw 60vh at 20% 20%, rgba(255,46,136,.14), transparent 60%),
        radial-gradient(80vw 60vh at 80% 30%, rgba(0,97,255,.14), transparent 60%),
        radial-gradient(90vw 70vh at 50% 85%, rgba(131,0,255,.12), transparent 70%),
        linear-gradient(180deg,#0a0f2a,#090e1f 55%,#070a1a) !important;
      background-attachment: fixed;
    }

    /* Fixed neon waves behind content (no animation) */
    #neon-bg{
      position:fixed; inset:0; z-index:-1; pointer-events:none;
      background:
        url("data:image/svg+xml;utf8,<?xml version='1.0' encoding='UTF-8'?><svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 1600 900' preserveAspectRatio='none'><defs><linearGradient id='g1' x1='0' y1='0' x2='1' y2='0'><stop offset='0%' stop-color='%23ff2e88'/><stop offset='50%' stop-color='%238300ff'/><stop offset='100%' stop-color='%23006bff'/></linearGradient><filter id='b'><feGaussianBlur stdDeviation='25'/></filter></defs><rect width='1600' height='900' fill='none'/><path d='M0,650 C260,520 370,520 630,650 C900,790 1040,780 1250,650 C1440,540 1540,540 1600,580 L1600,900 L0,900 Z' fill='url(%23g1)' filter='url(%23b)' opacity='.85'/><path d='M0,350 C240,480 380,480 640,350 C910,210 1050,220 1260,350 C1450,460 1540,460 1600,420 L1600,0 L0,0 Z' fill='url(%23g1)' filter='url(%23b)' opacity='.75'/></svg>")
        center / cover no-repeat;
      mix-blend-mode: screen;
    }

    .glass { background: rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(10px); }
  </style>
  @stack('head')
</head>
<body class="text-slate-100 antialiased">
  <div id="neon-bg" aria-hidden="true"></div>

  <header class="sticky top-0 z-40 bg-black/30 backdrop-blur border-b border-white/10">
    <div class="max-w-7xl mx-auto px-4 h-14 flex items-center justify-between">
      <a href="{{ route('home') }}" class="font-semibold">Semantic SEO</a>
      <nav class="hidden md:flex gap-6 text-sm">
        <a href="{{ route('semantic') }}" class="hover:text-white">Semantic Analyzer</a>
        <a href="{{ route('aiChecker') }}" class="hover:text-white">AI Content Checker</a>
        <a href="{{ route('topicCluster') }}" class="hover:text-white">Topic Cluster</a>
      </nav>
      <div>
        @auth
          <a href="{{ route('dashboard') }}" class="px-3 py-1 rounded-lg bg-white/10">Dashboard</a>
        @else
          <a href="{{ route('login') }}" class="px-3 py-1 rounded-lg bg-white/10">Login</a>
        @endauth
      </div>
    </div>
  </header>

  <main class="min-h-[calc(100vh-3.5rem)]">
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>
