<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Shared SEO Report</title>
  <style>
    body{font-family:Inter,system-ui,sans-serif;background:#0b0d14;color:#eef;margin:0}
    .wrap{max-width:900px;margin:30px auto;padding:0 16px}
    .card{background:#12152a;border:1px solid #1b1f3a;border-radius:14px;padding:14px}
    .chip{display:inline-block;background:#1e2350;border:1px solid #2a3276;border-radius:999px;padding:4px 10px;margin-right:8px}
    pre{white-space:pre-wrap;background:#0b0d21;border:1px solid #1b1b35;border-radius:12px;padding:12px;color:#cfd3f6}
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Shared SEO Report</h1>
    <div class="card">
      <span class="chip">Overall: <b>{{ $data['overall'] ?? 0 }}/100</b></span>
      <span class="chip">Content: <b>{{ $data['content'] ?? 0 }}/100</b></span>
    </div>
    <h3>Scores</h3>
    <pre>{{ json_encode($data['scores'] ?? [], JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
    <p style="color:#889">ID: {{ $id }}</p>
  </div>
</body>
</html>
