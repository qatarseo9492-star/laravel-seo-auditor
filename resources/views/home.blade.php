@extends('layouts.app')

@section('content')
<!-- AOS Animation Library -->
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
    /* Hero Section */
    .hero {
        background: linear-gradient(135deg, #6f42c1, #007bff, #00b894);
        background-size: 300% 300%;
        animation: gradientBG 12s ease infinite;
        color: white;
        text-align: center;
        padding: 100px 20px;
        border-radius: 15px;
        margin-bottom: 60px;
        position: relative;
        overflow: hidden;
    }
    @keyframes gradientBG {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    .hero h1 {
        font-size: 3rem;
        font-weight: 800;
        letter-spacing: -1px;
    }
    .hero p {
        font-size: 1.3rem;
        margin-top: 15px;
        opacity: 0.9;
    }
    .cta-btn {
        margin-top: 25px;
        font-size: 1.2rem;
        padding: 12px 30px;
        border-radius: 30px;
        background: linear-gradient(135deg, #ff416c, #ff4b2b);
        border: none;
        color: white;
        transition: 0.4s;
    }
    .cta-btn:hover {
        background: linear-gradient(135deg, #ff4b2b, #ff416c);
        transform: scale(1.06);
    }

    /* Floating keyword cloud */
    .keywords-cloud {
        margin-top: 30px;
        font-size: 1.1rem;
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 15px;
    }
    .keywords-cloud span {
        background: rgba(255,255,255,0.2);
        padding: 8px 15px;
        border-radius: 20px;
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-12px); }
    }

    /* Tool Cards Section */
    .tool-card {
        background: white;
        border-radius: 18px;
        padding: 40px 25px;
        transition: all 0.4s ease;
        height: 100%;
        position: relative;
    }
    .tool-card:hover {
        transform: translateY(-10px) scale(1.03);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .tool-icon {
        font-size: 3rem;
        margin-bottom: 20px;
        animation: bounce 2s infinite;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    .btn-tool {
        margin-top: 20px;
        padding: 10px 24px;
        border-radius: 25px;
        font-weight: 600;
        background: linear-gradient(135deg, #007bff, #6f42c1);
        border: none;
        color: white;
        transition: 0.3s;
    }
    .btn-tool:hover {
        opacity: 0.85;
        transform: translateY(-2px);
    }

    /* About section */
    .about-section {
        background: #f8f9fa;
        padding: 60px 20px;
        text-align: center;
        border-radius: 15px;
        margin-top: 80px;
    }
    .about-section h2 {
        font-weight: 700;
        margin-bottom: 20px;
    }
    .about-section p {
        max-width: 800px;
        margin: 0 auto;
        font-size: 1.1rem;
        opacity: 0.9;
    }
</style>

<div class="container">

    {{-- Hero Section --}}
    <div class="hero shadow-lg" data-aos="zoom-in" data-aos-duration="1200">
        <h1>🚀 Boost Your Rankings Like Never Before</h1>
        <p>Our SEO Tools Suite gives you <strong>clarity, speed, and results</strong>.  
        Say goodbye to clutter — focus on insights that matter.</p>
        <a href="#tools" class="btn cta-btn">Explore Tools</a>

        <div class="keywords-cloud mt-4">
            <span>#SEO</span>
            <span>#Rank1</span>
            <span>#Traffic</span>
            <span>#Backlinks</span>
            <span>#Keywords</span>
            <span>#Growth</span>
            <span>#Optimization</span>
        </div>
    </div>

    {{-- Tools Section --}}
    <div id="tools" class="row text-center mt-5">
        {{-- SEO Auditor --}}
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="100">
            <div class="tool-card">
                <div class="tool-icon">📊</div>
                <h4 class="fw-bold">SEO Auditor</h4>
                <p>Audit full web pages with technical SEO checks, headings, titles, meta tags, and structure analysis.</p>
                <a href="{{ url('/seo-audit') }}" class="btn btn-tool">Try Auditor</a>
            </div>
        </div>

        {{-- Keyword Analyzer --}}
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="300">
            <div class="tool-card">
                <div class="tool-icon">🔑</div>
                <h4 class="fw-bold">Keyword Analyzer</h4>
                <p>Examine how well optimized your content is for target keywords and find keyword gaps instantly.</p>
                <a href="{{ url('/seo-keyword-analysis') }}" class="btn btn-tool">Try Analyzer</a>
            </div>
        </div>

        {{-- Content Optimizer --}}
        <div class="col-md-4 mb-4" data-aos="fade-up" data-aos-delay="500">
            <div class="tool-card">
                <div class="tool-icon">📈</div>
                <h4 class="fw-bold">Content Optimizer</h4>
                <p>Input a URL & keyword → receive powerful optimization suggestions to improve rankings and relevance.</p>
                <a href="{{ url('/seo-optimizer') }}" class="btn btn-tool">Optimize Now</a>
            </div>
        </div>
    </div>

    {{-- About Section --}}
    <div class="about-section mt-5" data-aos="fade-up" data-aos-duration="1000">
        <h2>Why Choose Us Over SEMrush & Ahrefs?</h2>
        <p>Unlike heavy, overwhelming SEO platforms, our toolkit is simple, elegant,  
           and built for action. 💡 Get laser‑focused insights — no fluff, no distractions —  
           so you can rank faster and grow smarter.</p>
    </div>
</div>

<!-- AOS JS -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init({
      once: true,
      duration: 1000,
  });
</script>
@endsection
