@extends('layouts.app')

@section('content')
<style>
    .hero-section {
        background: linear-gradient(135deg, #007bff, #6f42c1);
        color: white;
        padding: 80px 20px;
        border-radius: 10px;
        text-align: center;
        margin-bottom: 50px;
    }
    .hero-section h1 {
        font-weight: 700;
        font-size: 2.8rem;
    }
    .hero-section p {
        font-size: 1.2rem;
        opacity: 0.9;
    }
    .tool-card {
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0px 5px 20px rgba(0,0,0,0.1);
        background: #ffffff;
    }
    .tool-card:hover {
        transform: translateY(-8px);
        box-shadow: 0px 8px 25px rgba(0,0,0,0.15);
    }
    .tool-icon {
        font-size: 50px;
        margin-bottom: 20px;
        color: #007bff;
    }
    .btn-gradient {
        background: linear-gradient(135deg, #007bff, #6f42c1);
        border: none;
        color: white;
        font-weight: 600;
        border-radius: 25px;
        padding: 10px 24px;
        transition: 0.3s;
    }
    .btn-gradient:hover {
        opacity: 0.9;
        color: white;
    }
</style>

<div class="container">
    {{-- Hero Section --}}
    <div class="hero-section">
        <h1>Welcome to SEO Tools Suite 🚀</h1>
        <p>Analyze, optimize, and improve your website’s SEO with our simple but powerful tools.</p>
    </div>

    {{-- Tools Section --}}
    <div class="row text-center">
        {{-- SEO Auditor --}}
        <div class="col-md-4 mb-4">
            <div class="card tool-card p-4 h-100">
                <div class="tool-icon">📊</div>
                <h4 class="fw-bold mb-3">SEO Auditor</h4>
                <p class="text-muted">Run a full-page audit and get detailed insights into your website’s SEO health.</p>
                <a href="{{ url('/seo-audit') }}" class="btn btn-gradient mt-3">Use Tool</a>
            </div>
        </div>

        {{-- Keyword Analyzer --}}
        <div class="col-md-4 mb-4">
            <div class="card tool-card p-4 h-100">
                <div class="tool-icon">🔑</div>
                <h4 class="fw-bold mb-3">Keyword Analyzer</h4>
                <p class="text-muted">Analyze how well your content is optimized for specific keywords and discover usage gaps.</p>
                <a href="{{ url('/seo-keyword-analysis') }}" class="btn btn-gradient mt-3">Use Tool</a>
            </div>
        </div>

        {{-- Content Optimizer --}}
        <div class="col-md-4 mb-4">
            <div class="card tool-card p-4 h-100">
                <div class="tool-icon">🚀</div>
                <h4 class="fw-bold mb-3">Content Optimizer</h4>
                <p class="text-muted">Input a URL and keyword to get actionable suggestions to better optimize your content for rankings.</p>
                <a href="{{ url('/seo-optimizer') }}" class="btn btn-gradient mt-3">Use Tool</a>
            </div>
        </div>
    </div>
</div>
@endsection
