<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard • Semantic SEO</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme:{ extend:{ colors:{ brand:{500:'#8b5cf6'} } } }
    }
  </script>
  <style>
    body{ background: radial-gradient(800px 500px at 0% 0%, rgba(139,92,246,.25), transparent 60%), radial-gradient(800px 600px at 100% 0%, rgba(96,165,250,.25), transparent 60%), #0b0f1a;}
    .glass{ background: linear-gradient(180deg, rgba(15,23,42,.65), rgba(2,6,23,.55)); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(8px); }
  </style>
</head>
<body class="text-slate-100 antialiased">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Welcome, {{ auth()->user()->name ?? 'User' }}</h1>
      <a href="{{ url('/') }}" class="text-sm text-slate-300 hover:text-white">← Back to site</a>
    </div>

    <div class="grid md:grid-cols-3 gap-6 mt-8">
      <a href="{{ route('semantic.analyzer') }}" class="p-6 rounded-2xl glass hover:bg-white/5 transition border border-white/10">
        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-fuchsia-500 to-rose-400 mb-3"></div>
        <h3 class="font-semibold">Semantic SEO Master Analyzer 2.0</h3>
        <p class="text-sm text-slate-300 mt-1">Deep semantic & technical analysis.</p>
      </a>
      <a href="{{ route('ai.checker') }}" class="p-6 rounded-2xl glass hover:bg-white/5 transition border border-white/10">
        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-sky-500 to-indigo-500 mb-3"></div>
        <h3 class="font-semibold">AI Content Checker</h3>
        <p class="text-sm text-slate-300 mt-1">Estimate AI-likeness with signals.</p>
      </a>
      <a href="{{ route('topic.cluster') }}" class="p-6 rounded-2xl glass hover:bg-white/5 transition border border-white/10">
        <div class="h-10 w-10 rounded-lg bg-gradient-to-br from-emerald-400 to-cyan-400 mb-3"></div>
        <h3 class="font-semibold">Topic Cluster Identification</h3>
        <p class="text-sm text-slate-300 mt-1">TF-IDF clusters for content hubs.</p>
      </a>
    </div>
  </div>
</body>
</html>
