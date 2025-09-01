@extends('layouts.app')

@section('title', 'Keyword Analyzer - Semantic SEO Checker')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">🔑 Keyword Analyzer</h3>
                </div>
                <div class="card-body">

                    {{-- Show validation errors if any --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Analyzer form --}}
                    <form action="{{ route('seo.keyword.analyze') }}" method="POST">
                        @csrf

                        {{-- Content input --}}
                        <div class="mb-3">
                            <label for="content" class="form-label fw-bold">💬 Paste Your Content</label>
                            <textarea 
                                name="content" 
                                id="content" 
                                rows="8" 
                                class="form-control @error('content') is-invalid @enderror"
                                placeholder="Paste or type the article / webpage content here..."
                                required>{{ old('content') }}</textarea>
                            @error('content')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Keyword input --}}
                        <div class="mb-3">
                            <label for="keyword" class="form-label fw-bold">🎯 Target Keyword</label>
                            <input 
                                type="text" 
                                name="keyword" 
                                id="keyword" 
                                class="form-control @error('keyword') is-invalid @enderror"
                                value="{{ old('keyword') }}"
                                placeholder="e.g. seo, keyword research, backlinks" 
                                required>
                            @error('keyword')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Action buttons --}}
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('home') }}" class="btn btn-secondary">← Back Home</a>
                            <button type="submit" class="btn btn-primary px-4">🔍 Analyze</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
