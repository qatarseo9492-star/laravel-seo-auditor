<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Semantic SEO Master Analyzer 2.0' }}</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <style>
    /* subtle animated background */
    .bg-grid {
      background-image:
        radial-gradient(rgba(255,255,255,0.08) 1px, transparent 1px),
        radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
      background-size: 20px 20px, 40px 40px;
      background-position: 0 0, 10px 10px;
    }
  </style>
</head>
<body class="min-h-screen bg-slate-900 text-slate-100 bg-grid">

  <!-- Top Nav (appears on ALL pages) -->
  <header x-data="{ open:false, userMenu:false }" class="sticky top-0 z-50 bg-slate-900/70 backdrop-blur border-b border-white/10">
    <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <a href="{{ route('home') }}" class="flex items-center gap-2 group">
          <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-tr from-fuchsia-500 via-cyan-400 to-emerald-400 shadow-lg"></span>
          <span class="font-semibold text-lg tracking-tight">
            Semantic <span class="text-cyan-300">SEO</span>
          </span>
        </a>
        <div class="hidden md:flex items-center gap-1 ml-6">
          <a href="{{ route('semantic') }}"
             class="px-3 py-2 rounded-lg text-sm hover:bg-white/5 {{ request()->routeIs('semantic*') ? 'bg-white/10 text-white' : 'text-slate-300' }}">
             Semantic Analyzer
          </a>
          <a href="{{ route('aiChecker') }}"
             class="px-3 py-2 rounded-lg text-sm hover:bg-white/5 {{ request()->routeIs('ai*') ? 'bg-white/10 text-white' : 'text-slate-300' }}">
             AI Content Checker
          </a>
          <a href="{{ route('topicCluster') }}"
             class="px-3 py-2 rounded-lg text-sm hover:bg-white/5 {{ request()->routeIs('topic*') ? 'bg-white/10 text-white' : 'text-slate-300' }}">
             Topic Cluster
          </a>
        </div>
      </div>

      <div class="flex items-center gap-3">
        @auth
          @php
            $email = strtolower(trim(auth()->user()->email));
            $gravatar = 'https://www.gravatar.com/avatar/'.md5($email).'?s=80&d=identicon';
            $avatar = auth()->user()->avatar_path
              ? asset('storage/'.auth()->user()->avatar_path)
              : $gravatar;
          @endphp
          <a href="{{ route('dashboard') }}"
             class="hidden sm:inline-flex px-3 py-2 text-sm rounded-lg bg-white/10 hover:bg-white/20">
            Dashboard
          </a>

          <div class="relative">
            <button @click="userMenu = !userMenu" @click.outside="userMenu=false"
              class="flex items-center gap-2 rounded-full outline-none focus:ring-2 ring-cyan-400/60">
              <img src="{{ $avatar }}" alt="Avatar" class="h-9 w-9 rounded-full object-cover border border-white/20">
              <svg class="h-4 w-4 text-slate-300" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.187l3.71-3.955a.75.75 0 111.08 1.04l-4.24 4.52a.75.75 0 01-1.08 0l-4.24-4.52a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
              </svg>
            </button>
            <div x-show="userMenu" x-transition
                 class="absolute right-0 mt-2 w-56 rounded-xl bg-slate-800 border border-white/10 shadow-xl overflow-hidden">
              <div class="px-4 py-3 text-sm">
                <div class="font-medium">{{ auth()->user()->name }}</div>
                <div class="text-slate-400 truncate">{{ auth()->user()->email }}</div>
              </div>
              <div class="border-t border-white/10"></div>
              <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm hover:bg-white/5">Profile</a>
              <a href="{{ route('dashboard') }}" class="block px-4 py-2 text-sm hover:bg-white/5">Dashboard</a>
              <form method="POST" action="{{ route('logout') }}" class="border-t border-white/10">
                @csrf
                <button class="w-full text-left px-4 py-2 text-sm text-red-300 hover:bg-red-500/10">Logout</button>
              </form>
            </div>
          </div>
        @endauth

        @guest
          <a href="{{ route('login') }}"
             class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-white text-slate-900 font-semibold text-sm">
            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
               d="M15 3h4a2 2 0 012 2v4M21 3l-7 7M7 7h3a4 4 0 014 4v6a2 2 0 01-2 2H7a4 4 0 01-4-4v-4a2 2 0 012-2h2"/>
            </svg>
            Login
          </a>
          <a href="{{ route('register') }}"
             class="hidden sm:inline-flex px-3 py-2 rounded-lg bg-white/10 hover:bg-white/20 text-sm">
            Register
          </a>
        @endguest

        <button class="md:hidden p-2 rounded-lg hover:bg-white/10" @click="open = !open" aria-label="Open menu">
          <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </nav>

    <!-- Mobile menu -->
    <div x-show="open" x-transition class="md:hidden border-t border-white/10">
      <div class="px-4 py-2 space-y-1">
        <a href="{{ route('semantic') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5">Semantic Analyzer</a>
        <a href="{{ route('aiChecker') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5">AI Content Checker</a>
        <a href="{{ route('topicCluster') }}" class="block px-3 py-2 rounded-lg hover:bg-white/5">Topic Cluster</a>
      </div>
    </div>
  </header>

  <!-- Page -->
  <main class="min-h-[calc(100vh-4rem)]">
    @yield('content')
  </main>

  <footer class="border-t border-white/10 py-8 text-center text-sm text-slate-400">
    © {{ date('Y') }} Semantic SEO Master Analyzer 2.0
  </footer>
</body>
</html>
