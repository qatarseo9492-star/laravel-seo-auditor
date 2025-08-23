<!DOCTYPE html>
<html lang="{{ str_replace('_','-',app()->getLocale()) }}" data-lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Semantic SEO Master • Ultra Tech Global')</title>

  <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
  <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32.png') }}">
  <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16.png') }}">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#070812; --panel:#0e1024; --panel-2:#12143a; --line:#1b1f3a;
      --text:#f3f4ff; --text-dim:#b9bce2; --text-muted:#96a0c9;
      --primary:#8d69ff; --secondary:#ff3b5c; --accent:#36e6ff;
      --good:#22c55e; --warn:#f59e0b; --bad:#ef4444;
      --radius:18px; --shadow:0 12px 40px rgba(0,0,0,.55);
      --container:1180px;
    }
    *{box-sizing:border-box} html,body{height:100%}
    html{scroll-behavior:smooth}
    body{
      margin:0; color:var(--text);
      font-family:Inter, ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto;
      background:
        radial-gradient(1200px 700px at 0% -10%, #1b0f3c 0%, transparent 55%),
        radial-gradient(1100px 800px at 110% 0%, #0f0f2b 0%, transparent 50%),
        var(--bg);
      overflow-x:hidden;
    }
    .wrap{position:relative;z-index:3;max-width:var(--container);margin:0 auto;padding:28px 5%}
  </style>

  @stack('styles')
</head>
<body>
  <canvas id="brainCanvas"></canvas>
  <canvas id="linesCanvas"></canvas>
  <canvas id="linesCanvas2"></canvas>
  <canvas id="smokeFX" aria-hidden="true"></canvas>

  <div class="wrap">
    @yield('content')
  </div>

  @stack('scripts')
</body>
</html>
