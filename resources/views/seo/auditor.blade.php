@extends('layouts.app')

@section('content')

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
                        <a href="{{ $result['url'] }}" target="_blank" class="text-white">{{ $result['url'] }}</a>
                    </h4>
                </div>

                <div class="card-body p-4">

                    {{-- SEO Score with Chart --}}
                    <h5 class="fw-bold">Overall SEO Score</h5>
                    <div class="d-flex align-items-center justify-content-center my-4">
                        {{-- Keep fixed size to stop over-scaling --}}
                        <canvas id="seoScoreChart" width="200" height="200"></canvas>
                    </div>

                    {{-- Accordion with details --}}
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
                                    <p><strong>Word Count:</strong> {{ $result['word_count'] }}</p>
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
                                        @forelse($result['h1'] as $tag)
                                            <li>{{ $tag }}</li>
                                        @empty
                                            <li>No H1 tags found</li>
                                        @endforelse
                                    </ul>
                                    <h6>H2 Tags</h6>
                                    <ul>
                                        @forelse($result['h2'] as $tag)
                                            <li>{{ $tag }}</li>
                                        @empty
                                            <li>No H2 tags found</li>
                                        @endforelse
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
                                        @foreach($result['recommendations'] as $rec)
                                            <li class="list-group-item">{{ $rec }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-4">
                        <a href="{{ url('/') }}" class="btn btn-secondary">⬅ Back to Home</a>
                        <a href="{{ url('/seo-audit?url=' . urlencode($result['url']) . '&download=pdf') }}" 
                           class="btn btn-danger">📄 Download PDF Report</a>
                    </div>

                </div>
            </div>
        @endif
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    @if(isset($result) && !isset($result['error']))
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
            responsive: false,            // stop over-scaling
            maintainAspectRatio: false,   // keep fixed
            cutout: '85%',                // makes donut slimmer
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
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
                ctx.fillText(score + '/100', width / 2, height / 2);
            }
        }]
    });
    @endif
});
</script>

@endsection
