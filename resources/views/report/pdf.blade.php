<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>SEO Report</title>
  <style>
    body{font-family:DejaVu Sans, sans-serif; color:#111}
    h1{font-size:22px;margin:0 0 10px}
    h2{font-size:16px;margin:20px 0 8px}
    table{width:100%;border-collapse:collapse;margin:8px 0}
    th,td{border:1px solid #ddd;padding:6px 8px;font-size:12px}
    .badge{display:inline-block;padding:3px 8px;border-radius:12px;font-size:12px}
    .g{background:#e7f7ee} .o{background:#fff4e6} .r{background:#fde8e8}
  </style>
</head>
<body>
  <h1>Semantic SEO Master – Report</h1>
  <p><strong>URL:</strong> {{ $url }}</p>
  <p><strong>Overall:</strong> {{ $overall }}/100 — <strong>Content:</strong> {{ $content }}/100</p>

  <h2>Checklist Scores</h2>
  <table>
    <thead><tr><th>Item</th><th>Score</th></tr></thead>
    <tbody>
    @foreach(($scores ?? []) as $id => $sc)
      <tr>
        <td>{{ $id }}</td>
        <td><span class="badge {{ $sc>=80?'g':($sc>=60?'o':'r') }}">{{ $sc }}</span></td>
      </tr>
    @endforeach
    </tbody>
  </table>

  <h2>AI vs Human</h2>
  <p><strong>Label:</strong> {{ $ai['label'] ?? '—' }} |
     <strong>AI‑like:</strong> {{ $ai['ai_pct'] ?? '—' }}% |
     <strong>Human‑like:</strong> {{ $ai['human_pct'] ?? '—' }}%</p>

  <h2>Technical & On‑page</h2>
  <table>
    <tbody>
      @foreach(($report ?? []) as $k=>$v)
        <tr><td>{{ $k }}</td><td>{{ is_array($v)? json_encode($v, JSON_UNESCAPED_SLASHES) : $v }}</td></tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
