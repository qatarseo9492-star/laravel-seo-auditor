@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">

        {{-- Form --}}
        <div class="card shadow mb-4 border-0">
            <div class="card-body p-4">
                <h2 class="text-primary">Keyword Optimization Tool</h2>
                <p class="text-muted">Enter a post URL and your target keyword to evaluate & optimize for SEO performance.</p>

                <form method="POST" action="{{ url('/seo-optimizer/analyze') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Post URL</label>
                        <input type="url" name="url" class="form-control" placeholder="https://example.com/post" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Keyword</label>
                        <input type="text" name="keyword" class="form-control" placeholder="best coffee beans" required>
                    </div>
                    <button type="submit" class="btn btn-gradient">Analyze</button>
                </form>
            </div>
        </div>

        {{-- Error --}}
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        {{-- Results --}}
        @if(!empty($analysis))
            <div class="card shadow border-0">
                <div class="card-header bg-gradient text-white">
                    <h4 class="mb-0">Analysis Results</h4>
                </div>
                <div class="card-body">

                    <p><strong>URL:</strong> {{ $analysis['url'] }}</p>
                    <p><strong>Keyword:</strong> {{ $analysis['keyword'] }}</p>
                    <p><strong>Title:</strong> {{ $analysis['title'] }}</p>
                    <p><strong>H1:</strong> {{ $analysis['h1'] }}</p>
                    <p><strong>Meta Description:</strong> {{ $analysis['description'] }}</p>
                    <p><strong>Keyword Occurrences in Body:</strong> {{ $analysis['body_count'] }}</p>
                    <h5 class="mt-3">Relevance Score: {{ $analysis['score'] }}/100</h5>

                    <h6 class="mt-3">Optimization Recommendations:</h6>
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
@endsection
