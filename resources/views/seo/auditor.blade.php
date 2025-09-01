@extends('layouts.app')

@section('content')

{{-- Dark Mode Toggle --}}
<div class="text-center mb-4">
    <button id="darkModeToggle" class="btn btn-outline-dark">🌙 Toggle Dark Mode</button>
</div>

<div class="row justify-content-center">
    <div class="col-lg-10">

        {{-- SEO Audit Form --}}
        <div class="card shadow-lg mb-4 border-0">
            <div class="card-body p-4">
                <h2 class="mb-3 text-primary">SEO Audit Tool</h2>
                <p class="text-muted">Enter a website URL to analyze its SEO performance.</p>

                <form method="GET" action="{{ url('/seo-audit') }}">
                    <div class="input-group input-group-lg">
                        <input type="url" name="url" class="form-control" placeholder="https://example.com"
                               value="{{ request('url') }}" required>
                        <button type="submit" class="btn btn-gradient">Analyze</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Show Error --}}
        @if(isset($result['error']))
            <div class="alert alert-danger shadow-sm">{{ $result['error'] }}</div>
        @endif

        {{-- Results Section --}}
        @if(isset($result) && !isset($result['error']))
            <div class="card shadow-lg border-0">
                <div class="card-header bg-gradient text-white">
                    <h4 class="mb-0">SEO Report for 
                        <a href="{{ $result['url'] ?? '#' }}" target="_blank" class="text-white">
                            {{ $result['url'] ?? 'N/A' }}
                        </a>
                    </h4>
                </div>

                <div class="card-body p-4">

                    {{-- SEO Score with Chart --}}
                    <h5 class="fw-bold">Overall SEO Score</h5>
                    <div class="d-flex align-items-center justify-content-center my-4">
                        <canvas id="seoScoreChart" width="200" height="200"></canvas>
                    </div>

                    {{-- Accordion --}}
                    <div class="accordion" id="seoReportAccordion">

                        {{-- Meta Info --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingMeta">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#collapseMeta" aria-expanded="true">
                                    Meta Information
                                </button>
                            </h2>
                            <div id="collapseMeta" class="accordion-collapse collapse show"
                                 data-bs-parent="#seoReportAccordion">
                                <div class="accordion-body">
                                    <p><strong>Title:</strong> {{ $result['title'] ?? 'N/A' }}</p>
                                    <p><strong>Description:</strong> {{ $result['description'] ?? 'N/A' }}</p>
                                    <p><strong>Word Count:</strong> {{ $result['word_count'] ?? 0 }}</p>
                                </div>
                            </div>
                        </div>

                        {{-- Headings --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingHeadings">
                                <button class="accordion-button collapsed" type="button" 
                                        data-bs-toggle="collapse" data-bs-target="#collapseHeadings">
                                    Headings (H1 & H2)
                                </button>
                            </h2>
                            <div id="collapseHeadings" class="accordion-collapse collapse" 
                                 data-bs-parent="#seoReportAccordion">
                                <div class="accordion-body">
                                    <h6>H1 Tags</h6>
                                    <ul>
                                        @if(!empty($result['h1']))
                                            @foreach($result['h1'] as $tag)
                                                <li>{{ $tag }}</li>
                                            @endforeach
                                        @else
                                            <li>No H1 tags found</li>
                                        @endif
                                    </ul>
                                    <h6>H2 Tags</h6>
                                    <ul>
                                        @if(!empty($result['h2']))
                                            @foreach($result['h2'] as $tag)
                                                <li>{{ $tag }}</li>
                                            @endforeach
                                        @else
                                            <li>No H2 tags found</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Recommendations --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingRecs">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseRecs">
                                    Recommendations
                                </button>
                            </h2>
                            <div id="collapseRecs" class="accordion-collapse collapse" 
                                 data-bs-parent="#seoReportAccordion">
                                <div class="accordion-body">
                                    <ul class="list-group list-group-flush">
                                        @if(!empty($result['recommendations']))
                                            @foreach($result['recommendations'] as $rec)
                                                <li class="list-group-item">{{ $rec }}</li>
                                            @endforeach
                                        @else
                                            <li class="list-group-item">No recommendations found.</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                        {{-- Advanced SEO Strategy --}}
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingAdvanced">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#collapseAdvanced">
                                    Advanced SEO Strategy
                                </button>
                            </h2>
                            <div id="collapseAdvanced" class="accordion-collapse collapse"
                                 data-bs-parent="#seoReportAccordion">
                                <div class="accordion-body">

                                    <p><strong>Primary Keyword:</strong> {{ $result['primary_keyword'] ?? 'Not detected' }}</p>
                                    <p><strong>Search Intent:</strong> {{ $result['search_intent'] ?? 'N/A' }}</p>

                                    <h6>Related Topics & Entities</h6>
                                    <ul>
                                        @if(!empty($result['related_topics']))
                                            @foreach($result['related_topics'] as $topic)
                                                <li>{{ $topic }}</li>
                                            @endforeach
                                        @else
                                            <li>No related topics found.</li>
                                        @endif
                                    </ul>

                                    <h6>Competitor Content Gaps</h6>
                                    <ul>
                                        @if(!empty($result['competitor_gaps']))
                                            @foreach($result['competitor_gaps'] as $gap)
                                                <li>{{ $gap }}</li>
                                            @endforeach
                                        @else
                                            <li>No competitor gaps identified.</li>
                                        @endif
                                    </ul>

                                    <h6>Suggested Content Outline</h6>
                                    <p><strong>H1:</strong> {{ $result['content_outline']['h1'] ?? 'N/A' }}</p>
                                    @if(!empty($result['content_outline']['h2']))
                                        <ul>
                                            @foreach(($result['content_outline']['h2'] ?? []) as $h2)
                                                <li><strong>H2:</strong> {{ $h2 }}</li>
                                                @if(!empty($result['content_outline']['h3']))
                                                    <ul>
                                                        @foreach(($result['content_outline']['h3'] ?? []) as $h3)
                                                            <li><em>H3:</em> {{ $h3 }}</li>
                                                        @endforeach
                                                    </ul>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @else
                                        <p>No suggested content outline available.</p>
                                    @endif

                                    <h6>SEO Action Checklist</h6>
                                    <ul class="list-group list-group-flush">
                                        @if(!empty($result['seo_actions']))
                                            @foreach($result['seo_actions'] as $action)
                                                <li class="list-group-item">✅ {{ $action }}</li>
                                            @endforeach
                                        @else
                                            <li class="list-group-item">No checklist generated.</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-secondary">⬅ Back to Home</a>
                        <a href="{{ url('/seo-audit?url=' . urlencode($result['url'] ?? '') . '&download=pdf') }}" 
                           class="btn btn-danger">📄 Download PDF Report</a>
                    </div>

                </div>
            </div>
        @endif
    </div>
</div>

{{-- Dark Mode Styles --}}
<style>
    body.dark-mode { background:#121212; color:#f1f1f1; }
    body.dark-mode .card { background:#1e1e1e; color:#f1f1f1; }
    body.dark-mode .navbar { background: linear-gradient(90deg,#222,#444); }
    body.dark-mode footer { background:#1e1e1e; border-top:1px solid #333; color:#aaa; }
    body.dark-mode .btn-outline-dark { border-color:#ccc; color:#ccc; }
    body.dark-mode .btn-outline-dark:hover { background:#333; color:#fff; }
</style>

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Dark Mode Toggle
    const toggleBtn = document.getElementById("darkModeToggle");
    const body = document.body;
    if (localStorage.getItem("theme") === "dark") {
        body.classList.add("dark-mode");
        toggleBtn.textContent = "☀️ Toggle Light Mode";
    }
    toggleBtn.addEventListener("click", () => {
        body.classList.toggle("dark-mode");
        if (body.classList.contains("dark-mode")) {
            localStorage.setItem("theme", "dark");
            toggleBtn.textContent = "☀️ Toggle Light Mode";
        } else {
            localStorage.setItem("theme", "light");
            toggleBtn.textContent = "🌙 Toggle Dark Mode";
        }
    });

    // Chart.js with slim donut and center number
    @if(isset($result) && isset($result['score']))
    const score = {{ $result['score'] }};
    const remaining = 100 - score;
    const ctx = document.getElementById('seoScoreChart').getContext('2d');

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [score, remaining],
                backgroundColor: [
                    score >= 70 ? '#28a745' : (score >= 40 ? '#ffc107' : '#dc3545'),
                    '#e1e1e1'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: false,
            maintainAspectRatio: false,
            cutout: '85%',
            plugins: { legend: { display: false }, tooltip: { enabled: false } }
        },
        plugins: [{
            id: 'centerText',
            afterDraw(chart) {
                const { ctx, chartArea: { width, height } } = chart;
                ctx.save();
                ctx.font = 'bold 20px sans-serif';
                ctx.fillStyle = score >= 70 ? '#28a745' : (score >= 40 ? '#ffc107' : '#dc3545');
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(score + '/100', width/2, height/2);
            }
        }]
    });
    @endif
});
</script>

@endsection
