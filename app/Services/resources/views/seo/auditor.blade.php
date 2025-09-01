<!DOCTYPE html>
<html>
<head>
    <title>SEO Audit Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-5">

    <div class="container">
        <h1 class="mb-4">SEO Audit Tool</h1>

        {{-- Form to enter URL --}}
        <form method="GET" action="{{ url('/seo-audit') }}" class="mb-4">
            <div class="input-group">
                <input type="url" name="url" class="form-control" placeholder="Enter a website URL..." required>
                <button type="submit" class="btn btn-primary">Analyze</button>
            </div>
        </form>

        @if(isset($result['error']))
            <div class="alert alert-danger">{{ $result['error'] }}</div>
        @elseif(isset($result))
            <div class="card">
                <div class="card-body">
                    <h5>Analyzed URL: <a href="{{ $result['url'] }}" target="_blank">{{ $result['url'] }}</a></h5>
                    <p><strong>Title:</strong> {{ $result['title'] ?? 'N/A' }}</p>
                    <p><strong>Description:</strong> {{ $result['description'] ?? 'N/A' }}</p>
                    <p><strong>Word Count:</strong> {{ $result['word_count'] }}</p>
                    <p><strong>SEO Score:</strong> {{ $result['score'] }}/100</p>

                    <h6>H1 Tags</h6>
                    <ul>
                        @forelse($result['h1'] as $tag)
                            <li>{{ $tag }}</li>
                        @empty
                            <li>None found</li>
                        @endforelse
                    </ul>

                    <h6>H2 Tags</h6>
                    <ul>
                        @forelse($result['h2'] as $tag)
                            <li>{{ $tag }}</li>
                        @empty
                            <li>None found</li>
                        @endforelse
                    </ul>

                    <h6>Recommendations</h6>
                    <ul>
                        @foreach($result['recommendations'] as $rec)
                            <li>{{ $rec }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
    </div>
</body>
</html>
