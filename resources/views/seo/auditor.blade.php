@extends('layouts.app')

@section('content')

<div class="text-center mb-5">
    {{-- Hero Section --}}
    <h1 class="display-4 fw-bold text-primary">SEO Audit Tool 🚀</h1>
    <p class="lead text-muted">Instantly analyze any website and get actionable SEO recommendations to boost rankings.</p>
    <a href="{{ url('/seo-audit') }}" class="btn btn-lg btn-gradient mt-3">🔍 Start Your Free Audit</a>
</div>

{{-- Dark Mode Switch --}}
<div class="text-center mb-5">
    <button id="darkModeToggle" class="btn btn-outline-dark">🌙 Toggle Dark Mode</button>
</div>

{{-- Features Section --}}
<div class="row text-center mt-5">
    <div class="col-md-4 mb-4">
        <div class="card p-4 h-100 shadow-sm border-0 feature-card">
            <div class="mb-3">
                <span class="fs-1">📊</span>
            </div>
            <h5 class="fw-bold">Detailed SEO Analysis</h5>
            <p class="text-muted">Check titles, meta descriptions, headings, and word count to ensure your content is optimized.</p>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card p-4 h-100 shadow-sm border-0 feature-card">
            <div class="mb-3">
                <span class="fs-1">⚡</span>
            </div>
            <h5 class="fw-bold">Instant Results</h5>
            <p class="text-muted">Get your SEO score within seconds with easy-to-understand recommendations.</p>
        </div>
    </div>

    <div class="col-md-4 mb-4">
        <div class="card p-4 h-100 shadow-sm border-0 feature-card">
            <div class="mb-3">
                <span class="fs-1">📄</span>
            </div>
            <h5 class="fw-bold">Export Reports</h5>
            <p class="text-muted">Download a professional PDF SEO report to share with clients or your team.</p>
        </div>
    </div>
</div>

{{-- Call-to-Action --}}
<div class="text-center mt-5">
    <h2 class="fw-bold">Ready to Improve Your SEO?</h2>
    <p class="text-muted">Start analyzing your website now and get actionable recommendations today.</p>
    <a href="{{ url('/seo-audit') }}" class="btn btn-lg btn-gradient">🚀 Run SEO Audit Now</a>
</div>

{{-- Dark Mode Styles --}}
<style>
    body.dark-mode {
        background: #121212;
        color: #f1f1f1;
    }
    body.dark-mode .card {
        background: #1e1e1e;
        color: #f1f1f1;
        border: 1px solid #333;
    }
    body.dark-mode .navbar {
        background: linear-gradient(90deg, #222, #444);
    }
    body.dark-mode footer {
        background: #1e1e1e;
        border-top: 1px solid #333;
        color: #aaa;
    }
    body.dark-mode .btn-outline-dark {
        border-color: #ccc;
        color: #ccc;
    }
    body.dark-mode .btn-outline-dark:hover {
        background: #333;
        color: #fff;
    }
</style>

{{-- JavaScript Toggle --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toggleBtn = document.getElementById("darkModeToggle");
        const body = document.body;

        // Load stored theme preference
        if (localStorage.getItem("theme") === "dark") {
            body.classList.add("dark-mode");
            toggleBtn.textContent = "☀️ Toggle Light Mode";
        }

        toggleBtn.addEventListener("click", function () {
            body.classList.toggle("dark-mode");

            if (body.classList.contains("dark-mode")) {
                localStorage.setItem("theme", "dark");
                toggleBtn.textContent = "☀️ Toggle Light Mode";
            } else {
                localStorage.setItem("theme", "light");
                toggleBtn.textContent = "🌙 Toggle Dark Mode";
            }
        });
    });
</script>

@endsection
