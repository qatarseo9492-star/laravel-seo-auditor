{{-- resources/views/home.blade.php — Full upgraded layout --}}
<!DOCTYPE html>
<html lang="en" data-lang="en">
<head>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<meta name="csrf-token" content="{{ csrf_token() }}">

@php
  use Illuminate\Support\Facades\Route;
  $metaTitle = 'Semantic SEO Master • Ultra Tech Global';
  $metaDescription = 'Analyze any URL for content quality, entities, technical SEO, readability, and Core Web Vitals — with Human vs AI ensemble scoring.';
  $metaImage = asset('og-image.png');
  $canonical = url()->current();
  $analyzeJsonUrl = Route::has('analyze.json') ? route('analyze.json') : url('analyze-json');
  $psiProxyUrl    = Route::has('psi.proxy')    ? route('psi.proxy')    : url('api/psi');
@endphp

<title>{{ $metaTitle }}</title>
<link rel="canonical" href="{{ $canonical }}">
<meta name="description" content="{{ $metaDescription }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/ensemble.css') }}">
<style>
body{font-family:system-ui,-apple-system,Segoe UI,Roboto,sans-serif;background:#0b0d12;color:#eaf2ff;margin:0;line-height:1.5}
header{padding:20px;text-align:center}
header h1{margin:0;font-size:1.8rem;font-weight:700}
#analyze-form{display:flex;justify-content:center;gap:8px;margin:20px auto;max-width:720px}
#analyze-form input{flex:1;padding:12px;border-radius:12px;border:1px solid #333;background:#111;color:#fff}
#analyze-form button{padding:12px 20px;border:none;border-radius:12px;background:#7C4DFF;color:#fff;font-weight:600;cursor:pointer}
.panels{display:grid;gap:24px;max-width:1200px;margin:0 auto;padding:20px}
.panel{margin-bottom:20px}
</style>
</head>
<body>

<header>
  <h1>Semantic SEO Master Analyzer</h1>
</header>

<form id="analyze-form">
  <input type="text" id="url-input" placeholder="Enter a URL to analyze…" />
  <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Analyze</button>
</form>

<div class="panels">
  {{-- Human vs AI Ensemble --}}
  @include('components.human-ai-ensemble')

  {{-- Readability --}}
  <section id="readability" class="panel">
    <div class="panel-head"><div class="panel-title"><i class="fa-solid fa-book-open"></i> Readability</div></div>
    <div class="panel-body" id="readability-body">—</div>
  </section>

  {{-- Entities --}}
  <section id="entities" class="panel">
    <div class="panel-head"><div class="panel-title"><i class="fa-solid fa-lightbulb"></i> Entities</div></div>
    <div class="panel-body" id="entities-body">—</div>
  </section>

  {{-- Site Speed (PSI) --}}
  <section id="psi" class="panel">
    <div class="panel-head"><div class="panel-title"><i class="fa-solid fa-gauge-high"></i> Site Speed (Core Web Vitals)</div></div>
    <div class="panel-body" id="psi-body">—</div>
  </section>
</div>

<script defer src="{{ asset('js/ensemble.js') }}"></script>
<script>
const analyzeForm = document.getElementById('analyze-form')
const urlInput = document.getElementById('url-input')

analyzeForm.addEventListener('submit', async e=>{
  e.preventDefault()
  const url = urlInput.value.trim()
  if(!url){ alert('Enter a URL'); return }

  try {
    const resp = await fetch("{{ $analyzeJsonUrl }}", {
      method:"POST",
      headers:{
        "Content-Type":"application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
      },
      body: JSON.stringify({ url })
    })
    const data = await resp.json()

    // Readability
    document.getElementById('readability-body').textContent =
      data.basics?.readability ?? 'Not available'

    // Entities
    const ent = (data.entities||[]).map(e=>`<span class="entity">${e.name} <small>${e.type}</small></span>`).join(' ')
    document.getElementById('entities-body').innerHTML = ent || '—'

    // Ensemble
    window.setEnsembleScores?.({
      human: data.ensemble?.human,
      ai: data.ensemble?.ai,
      confidence: data.ensemble?.confidence,
      models: data.ensemble?.models,
      textSample: data.textSample || ''
    })

    // PSI
    const psi = await fetch("{{ $psiProxyUrl }}", {
      method:"POST",
      headers:{ "Content-Type":"application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ url })
    })
    const psiData = await psi.json()
    document.getElementById('psi-body').innerHTML =
      `Performance: <b>${psiData.lighthouseResult?.categories?.performance?.score*100||'—'}%</b><br>
       LCP: ${psiData.lighthouseResult?.audits['largest-contentful-paint']?.displayValue||'—'}<br>
       INP: ${psiData.lighthouseResult?.audits['interactive']?.displayValue||'—'}<br>
       CLS: ${psiData.lighthouseResult?.audits['cumulative-layout-shift']?.displayValue||'—'}`
  } catch(err){
    alert('Error analyzing: '+err)
  }
})
</script>
</body>
</html>
