@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">🔑 Keyword Analyzer</h4>
                </div>
                <div class="card-body">
                    
                    {{-- Show validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Analyzer Form --}}
                    <form action="{{ route('seo.keyword.analyze') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="content" class="form-label fw-bold">Paste Your Content</label>
                            <textarea 
                                name="content" 
                                id="content" 
                                rows="8" 
                                class="form-control @error('content') is-invalid @enderror"
                                placeholder="Paste article or webpage content here..." 
                                required>{{ old('content') }}</textarea>
                            @error('content')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="keyword" class="form-label fw-bold">Target Keyword</label>
                            <input 
                                type="text" 
                                name="keyword" 
                                id="keyword" 
                                class="form-control @error('keyword') is-invalid @enderror"
                                value="{{ old('keyword') }}" 
                                placeholder="e.g. SEO, keyword research, backlinks"
                                required>
                            @error('keyword')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary px-4">🔍 Analyze</button>
                        <a href="{{ route('home') }}" class="btn btn-secondary ms-2">← Back Home</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
