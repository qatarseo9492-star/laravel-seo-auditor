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
