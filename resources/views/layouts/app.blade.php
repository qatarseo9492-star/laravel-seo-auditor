<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Semantic SEO')</title>

  <style>
    html,body{background:#06021f;color:#e5e7eb;font-family:ui-sans-serif,system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial}
    a{color:inherit;text-decoration:none}
    .container{max-width:1200px;margin:0 auto;padding:0 16px}
    .nav{position:sticky;top:0;z-index:40;background:#06021f;border-bottom:1px solid rgba(255,255,255,.08)}
    .navbar{height:60px;display:flex;align-items:center;justify-content:space-between;gap:16px}
    .brand{display:flex;align-items:center;gap:10px;font-weight:900}
    .pill{padding:6px 12px;border-radius:9999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06)}
    .t-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent}
    /* Avatar ring (crisp, no blur, no bleed) */
    .avatar-wrap{position:relative;display:inline-grid;place-items:center}
    .avatar-ring{
      position:absolute;inset:-3px;border-radius:9999px;
      background:conic-gradient(#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e,#67e8f9);
      -webkit-mask:
        radial-gradient(farthest-side,#0000 calc(100% - 3.5px),#000 calc(100% - 3.5px)) content-box,
        linear-gradient(#000 0 0);  /* outer keep */
      -webkit-mask-composite: xor; mask-composite: exclude;
      padding:3.5px; /* ring width */
    }
    .avatar{width:32px;height:32px;border-radius:9999px;object-fit:cover;display:block;background:#111827}
    .avatar-fallback{
      width:32px;height:32px;border-radius:9999px;display:grid;place-items:center;
      background:#1f2937;color:#e5e7eb;font-weight:900
    }
    @media (max-width:720px){ .hide-sm{display:none} }
  </style>

  @stack('head')
</head>
<body>
  <header class="nav">
    <div class="container navbar">
      <a href="{{ route('home') }}" class="brand">
        <span style="font-size:20px">👑</span>
        <span class="t-grad">Semantic SEO</span>
      </a>

      <nav class="hide-sm" style="display:flex;gap:16px;align-items:center">
        <a href="{{ route('semantic.analyzer') }}" class="pill">Semantic Analyzer</a>
        <a href="{{ route('ai.checker') }}" class="pill">AI Content Checker</a>
        <a href="{{ route('topic.cluster') }}" class="pill">Topic Cluster</a>
      </nav>

      <div style="display:flex;align-items:center;gap:10px">
        @auth
          <a href="{{ route('dashboard') }}" class="pill hide-sm">Dashboard</a>
          <a href="{{ route('profile.edit') }}" title="Profile">
            <span class="avatar-wrap">
              <span class="avatar-ring" aria-hidden="true"></span>
              @php
                $u = auth()->user();
                $cand = $u?->profile_photo_url ?? $u?->avatar_url ?? $u?->avatar ?? null;
                if ($cand && str_starts_with($cand,'http')) { $avatarUrl = $cand; }
                elseif ($cand) { try { $avatarUrl = \Illuminate\Support\Facades\Storage::url($cand); } catch (\Throwable $e) { $avatarUrl = $cand; } }
                else { $avatarUrl = null; }
                $initials = $u ? mb_strtoupper(mb_substr($u->name ?? ($u->email ?? 'U'),0,1,'UTF-8')) : 'U';
              @endphp
              @if($avatarUrl)
                <img class="avatar" src="{{ $avatarUrl }}" alt="Avatar">
              @else
                <span class="avatar-fallback">{{ $initials }}</span>
              @endif
            </span>
          </a>
          <form action="{{ route('logout') }}" method="post">@csrf
            <button class="pill" style="background:#ef4444;color:#140404">Logout</button>
          </form>
        @else
          <a href="{{ route('login') }}" class="pill">Login</a>
          <a href="{{ route('register') }}" class="pill">Register</a>
        @endauth
      </div>
    </div>
  </header>

  <main class="container" style="padding:24px 16px">
    @yield('content')
  </main>

  @stack('scripts')
</body>
</html>
