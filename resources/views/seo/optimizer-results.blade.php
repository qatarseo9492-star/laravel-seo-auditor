<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Topic Cluster Results</title>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <script src="https://cdn.lordicon.com/lordicon.js"></script>
  <style>
    :root{
      --ink:#eaf1ff; --muted:#a9b6df; --bg:#0a0b12;
      --panel: rgba(255,255,255,.06); --border: rgba(255,255,255,.14);
      --accent1:#7c4dff; --accent2:#00e5ff; --accent3:#ff4dd2; --accent4:#33ffaa;
    }
    *{box-sizing:border-box}
    html,body{margin:0;padding:0}
    body{background:var(--bg); color:var(--ink); font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial; overflow-x:hidden}
    .bg{position:fixed; inset:0; z-index:-1;
      background:
        radial-gradient(1000px 580px at 8% -5%, rgba(124,77,255,.20), transparent 60%),
        radial-gradient(1000px 520px at 92% 0%, rgba(0,229,255,.18), transparent 60%),
        radial-gradient(900px 520px at 50% 110%, rgba(255,77,210,.14), transparent 60%),
        linear-gradient(180deg, #0a0b16 0%, #070811 100%);
    }
    .container{max-width:1200px;margin:0 auto;padding:24px 18px 40px}
    .title{display:flex;align-items:center;gap:.8rem;margin:10px 0 14px 0}
    .badge{width:44px;height:44px;border-radius:14px;display:grid;place-items:center;color:#061018;
      background:linear-gradient(135deg,var(--accent1),var(--accent2));box-shadow:0 10px 30px rgba(0,229,255,.28)}
    h1{margin:0;font-size:1.65rem}

    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:14px}
    .card{border:1px solid var(--border);border-radius:18px;background:var(--panel);backdrop-filter: blur(8px);box-shadow:0 18px 50px rgba(0,0,0,.45)}
    .card .hd{display:flex;align-items:center;gap:.6rem;padding:14px 14px 8px 14px}
    .card .bd{padding:14px}
    .kw{display:flex;flex-wrap:wrap;gap:8px;margin:6px 0 8px 0}
    .chip{font-size:.9rem;border:1px solid var(--border);padding:.25rem .6rem;border-radius:999px;opacity:.92}
    .links{display:grid;gap:6px;margin-top:6px}
    .links a{color:#a3c7ff;text-decoration:none;word-break:break-all}
    .links a:hover{text-decoration:underline}

    .footer{opacity:.75;margin-top:16px;font-size:.9rem}
    .meta{margin-top:8px}
  </style>
</head>
<body>
  <div class="bg"></div>
  @php $clusters = data_get($analysis->analysis_result, 'clusters', []); @endphp

  <div class="container">
    <div class="title">
      <div class="badge"><i class="fa-solid fa-diagram-project"></i></div>
      <div>
        <h1>Topic Cluster Results</h1>
        <div class="meta">Analyzed <strong>{{ count((array) $analysis->urls_list) }}</strong> URLs</div>
      </div>
    </div>

    <div class="grid">
      @forelse($clusters as $cluster)
        <div class="card">
          <div class="hd">
            <lord-icon src="https://cdn.lordicon.com/pxecicbf.json" trigger="loop" delay="2000" style="width:28px;height:28px"></lord-icon>
            <div>
              <div style="font-weight:700">{{ data_get($cluster, 'name', 'Untitled Cluster') }}</div>
              <div style="opacity:.8">{{ data_get($cluster, 'description') }}</div>
            </div>
          </div>
          <div class="bd">
            <div class="kw">
              @foreach((array) data_get($cluster, 'top_keywords', []) as $kw)
                <span class="chip">{{ $kw }}</span>
              @endforeach
            </div>
            <div class="links">
              @foreach((array) data_get($cluster, 'member_urls', []) as $u)
                <a href="{{ $u }}" target="_blank" rel="noopener"><i class="fa-solid fa-link"></i> {{ $u }}</a>
              @endforeach
            </div>
          </div>
        </div>
      @empty
        <div class="card" style="grid-column:1/-1">
          <div class="bd">No clusters were returned. Please try again with more URLs.</div>
        </div>
      @endforelse
    </div>

    <div class="footer">
      <a href="{{ route('seo.topic-clusters.create') }}" style="color:#a3c7ff;text-decoration:none"><i class="fa-solid fa-rotate-left"></i> Run another analysis</a>
    </div>

    <div class="footer">
      <details>
        <summary>OpenAI metadata</summary>
        <pre style="white-space:pre-wrap;background:rgba(255,255,255,.06);border:1px solid var(--border);border-radius:12px;padding:10px;overflow:auto">{{ json_encode($analysis->openai_metadata, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
      </details>
    </div>
  </div>
</body>
</html>
@extends('layouts.app')

@section('title', 'Content Optimizer Results - Jagoowala')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-4 text-gradient">⚡ Content Optimizer Results</h1>

    <p class="text-center text-muted">
        Here are the optimization suggestions for your content:
    </p>

    {{-- Example check for results --}}
    @if(isset($results) && count($results))
        <div class="card bg-dark text-light shadow mt-4">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($results as $suggestion)
                        <li class="list-group-item bg-transparent text-light border-secondary">
                            {{ $loop->iteration }}. {{ $suggestion }}
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <div class="alert alert-info mt-4 text-center">
            No optimizer results available yet.
        </div>
    @endif
</div>
@endsection
