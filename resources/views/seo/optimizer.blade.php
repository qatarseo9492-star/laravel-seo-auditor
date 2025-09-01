@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">

            {{-- Keyword Optimizer Form --}}
            <div class="card shadow mb-4 border-0">
                <div class="card-body p-4">
                    <h2 class="text-primary">🚀 Keyword Optimization Tool</h2>
                    <p class="text-muted">Enter a post URL and a target keyword. We will analyze the page and give you actionable optimization suggestions for better SEO performance.</p>

                    <form method="POST" action="{{ url('/seo-optimizer/analyze') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Post URL</label>
                            <input type="url" name="url" class="form-control"
                                   placeholder="https://example.com/blog-post" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Target Keyword</label>
                            <input type="text" name="keyword" class="form-control"
                                   placeholder="best coffee beans" required>
                        </div>
                        <button type="submit" class="btn btn-gradient">🔎 Analyze</button>
                    </form>
                </div>
            </div>

            {{-- Validation Error Display --}}
            @if($errors->any())
                <div class="alert alert-danger shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            {{-- Analysis Results --}}
            @if(!empty($analysis))
                <div class="card shadow border-0 mt-4">
                    <div class="card-header bg-gradient text-white">
                        <h4 class="mb-0">📊 Analysis Results</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>🔗 URL:</strong> {{ $analysis['url'] }}</p>
                        <p><strong>🎯 Target Keyword:</strong> {{ $analysis['keyword'] }}</p>
                        <hr>
                        <p><strong>📌 Title Tag:</strong> {{ $analysis['title'] ?: 'N/A' }}</p>
                        <p><strong>📌 H1 Heading:</strong> {{ $analysis['h1'] ?: 'N/A' }}</p>
                        <p><strong>📌 Meta Description:</strong> {{ $analysis['description'] ?: 'N/A' }}</p>
                        <p><strong>📌 Keyword Occurrences in Body:</strong> {{ $analysis['body_count'] }}</p>
                        
                        <h5 class="mt-3">✅ Relevance Score: <span class="text-success">{{ $analysis['score'] }}/100</span></h5>

                        <h6 class="mt-3 fw-bold">💡 Optimization Recommendations:</h6>
                        <ul>
                            @foreach($analysis['recommendations'] as $rec)
                                <li>{{ $rec }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>
@endsection
