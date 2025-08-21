<!DOCTYPE html>
<html>
<head>
  <title>SEO Auditor</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="mb-4">SEO Auditor</h1>

    <form method="GET" action="{{ route('seo.audit') }}" class="mb-4">
        <input type="url" name="url" class="form-control mb-2" placeholder="https://example.com" required>
        <button class="btn btn-primary">Audit</button>
    </form>

    @if($result)
        <div class="card p-3">
            <h3>Results for {{ $result['url'] }}</h3>
            <p><b>Title:</b> {{ $result['title'] ?? 'N/A' }}</p>
            <p><b>Description:</b> {{ $result['description'] ?? 'N/A' }}</p>
            <p><b>Word Count:</b> {{ $result['word_count'] }}</p>
            
            <h5>H1:</h5>
            <ul>
                @foreach($result['h1'] as $h)
                    <li>{{ $h }}</li>
                @endforeach
            </ul>

            <h5>H2:</h5>
            <ul>
                @foreach($result['h2'] as $h)
                    <li>{{ $h }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

</body>
</html>
