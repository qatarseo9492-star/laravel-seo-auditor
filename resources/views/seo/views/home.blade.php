@extends('layouts.app')

@section('content')
<div class="text-center mt-5">
    <h1 class="mb-4">Welcome to the SEO Audit Tool 🚀</h1>
    <p class="lead">Analyze any website instantly and get a professional SEO report with recommendations.</p>
    
    <a href="{{ url('/seo-audit') }}" class="btn btn-lg btn-primary mt-4">Start an Audit</a>
</div>
@endsection
