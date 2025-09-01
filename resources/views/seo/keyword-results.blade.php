@extends('layouts.app')

@section('title', 'Keyword Analyzer Results - Jagoowala')

@section('content')
<div class="container py-5">
    <h1 class="text-center text-gradient">🔑 Keyword Analyzer Results</h1>
    <p class="text-center text-muted mb-4">
        Your analyzed keyword performance and density data.
    </p>

    {{-- Example: show results passed from controller --}}
    @if(isset($results) && count($results))
        <div class="card bg-dark text-light shadow">
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    @foreach($results as $keyword => $score)
                        <li class="list-group-item d-flex justify-content-between align-items-center bg-transparent text-light border-secondary">
                            {{ $keyword }}
                            <span class="badge bg-gradient px-3 py-2">
                                {{ $score }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @else
        <div class="alert alert-info mt-4 text-center">
            No keyword results to display yet.
        </div>
    @endif
</div>
@endsection
