{{-- resources/views/home.blade.php — v2025-08-25r2 (performance + a11y + stability upgrades) --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, UX, speed, and Core Web Vitals with colorful, clear insights.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
  $analyzeJsonUrl = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $analyzeUrl     = Route::has('analyze')      ? route('analyze')      : url('analyze');
  $psiProxyUrl    = Route::has('psi.proxy')    ? route('psi.proxy')    : url('api/psi'); // server proxy keeps API key hidden
@endphp

<title>{{ $metaTitle }}</title>
<link rel="canonical" href="{{ $canonical }}">
<meta name="description" content="{{ $metaDescription }}">
<meta name="robots" content="index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ request()->fullUrl() }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css?v=2" rel="stylesheet" integrity="sha384-3u1V6KJY0m5C2VbHnH9uD2tD4x0wXw4sKQ2l4uJ6c1ZVh+q0m1F9vJmQkO5F7yq8" crossorigin="anonymous"/>

<style>
:root{ --bg:#07080e;--panel:#0f1022;--panel-2:#141433;--text:#f0effa;--text-dim:#b6b3d6;--good:#22c55e;--warn:#f59e0b;--bad:#ef4444;--accent:#3de2ff;--accent2:#9b5cff;--radius:18px;--shadow:0 10px 40px rgba(0,0,0,.55);--container:1200px;--hue:0deg }
*{box-sizing:border-box}html,body{height:100%}html{scroll-behavior:smooth}
body{margin:0;color:var(--text);font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,Helvetica,sans-serif;background:
  radial-gradient(1200px 700px at 0% -10%,#201046 0%,transparent 55%),
  radial-gradient(1100px 800px at 110% 0%,#1a0f2a 0%,transparent 50%),var(--bg);overflow-x:hidden}
/* keep your remaining styles from previous version here (unchanged) */
</style>
</head>
<body>

<canvas id="linesCanvas" aria-hidden="true"></canvas>
<canvas id="smokeCanvas" aria-hidden="true"></canvas>

<script>
window.SEMSEO = window.SEMSEO || {};
window.SEMSEO.ENDPOINTS = { analyzeJson:@json($analyzeJsonUrl), analyze:@json($analyzeUrl), psi:@json($psiProxyUrl) };
window.SEMSEO.SMOKE_HUE_PERIOD_MS = 1000000000;
window.SEMSEO.READY = false; window.SEMSEO.BUSY = false; window.SEMSEO.QUEUE = 0;
function SEMSEO_go(){ if(window.SEMSEO.READY && typeof analyze==='function'){ analyze(); } else { window.SEMSEO.QUEUE++; const s=document.getElementById('analyzeStatus'); if(s) s.textContent='Initializing…'; } }
</script>

<div class="wrap">
  <header class="site">
    <div class="brand">
      <div class="brand-badge" aria-hidden="true"><i class="fa-solid fa-brain"></i></div>
      <div>
        <div class="hero-heading">Semantic SEO Master Analyzer</div>
        <div class="hero-sub">Analyze URLs, get scores & colorful insights</div>
      </div>
    </div>
    <div class="header-actions">
      <button class="btn btn-print" id="printTop" type="button"><i class="fa-solid fa-print"></i> Print</button>
    </div>
  </header>

  <main class="analyzer" id="analyzer" role="main">
    <h2 class="section-title">Analyze a URL</h2>
    <p class="section-subtitle">
      Wheel + water bars fill with your scores.
      <span class="legend l-green">Green ≥ 80</span>
      <span class="legend l-orange">Orange 60–79</span>
      <span class="legend l-red">Red &lt; 60</span>
    </p>

    <!-- Keep your gauge & controls from previous file here -->

<!-- JS upgrade pack (functions, debounce, timeout, PSI resilience) -->
<script>
// ... keep the upgraded JS helpers and analyze() implementation from my message here ...
</script>

<footer class="site">
  <div><strong>Semantic SEO Master</strong></div>
  <div class="footer-links">
    <a href="#analyzer">Analyzer</a>
    <a href="#" id="toTopLink">Back to top</a>
  </div>
</footer>

<button id="backTop" title="Back to top" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

</body>
</html>
