<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semantic SEO Checker - Advanced SEO Analysis Tools</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --purple: #6a0dad;
            --orange: #ff8500;
            --blue: #2196f3;
            --teal: #03dac6;
            --dark: #111119;
            --dark-purple: #200030;
            --darker-purple: #12021c;
            --darkest-purple: #0a0012;
            --transition: all 0.3s ease;
            --radius: 12px;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: radial-gradient(circle at top, var(--dark-purple) 0%, var(--darker-purple) 40%, var(--darkest-purple) 100%);
            color: #eee;
            font-family: 'Inter', 'Poppins', sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }
        
        /* ===== Particle Background ===== */
        #particles-js {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
        }
        
        /* ===== Glassmorphism Effect ===== */
        .glass {
            background: rgba(26, 9, 47, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: var(--radius);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        /* ===== Navigation ===== */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            padding: 1.2rem 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            background: rgba(26, 9, 47, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--orange);
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.6);
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.6rem;
            color: var(--orange);
        }
        
        .logo i {
            margin-right: 0.5rem;
            font-size: 1.8rem;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
        }
        
        .nav-links li {
            margin: 0 1rem;
        }
        
        .nav-links a {
            color: #e0d7f5;
            text-decoration: none;
            font-weight: 500;
            transition: var(--transition);
            padding: 0.5rem 0;
            position: relative;
        }
        
        .nav-links a:after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background: var(--orange);
            transition: var(--transition);
        }
        
        .nav-links a:hover, .nav-links a.active {
            color: var(--orange);
        }
        
        .nav-links a:hover:after, .nav-links a.active:after {
            width: 100%;
        }
        
        .nav-actions {
            display: flex;
            align-items: center;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.75rem 1.5rem;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--orange);
            color: var(--orange);
            margin-right: 1rem;
        }
        
        .btn-outline:hover {
            background: var(--orange);
            color: #fff;
            box-shadow: 0 0 15px rgba(255, 133, 0, 0.5);
        }
        
        .btn-primary {
            background: linear-gradient(90deg, var(--purple), var(--orange));
            color: #fff;
            box-shadow: 0 6px 18px rgba(255, 133, 0, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(255, 133, 0, 0.6);
        }
        
        /* ===== Hero Section ===== */
        .hero {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            padding: 0 5%;
            margin-top: 80px;
        }
        
        .hero-content {
            text-align: center;
            max-width: 900px;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--orange), #ffb347, var(--purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            color: #ccc;
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }
        
        .hero-btn {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin-top: 3rem;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--orange);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #ccc;
            font-size: 0.9rem;
        }
        
        /* ===== Features Section ===== */
        .features {
            padding: 6rem 5%;
            position: relative;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 5rem;
        }
        
        .section-title {
            font-size: 2.8rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--orange), var(--purple), var(--orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .section-subtitle {
            color: #ccc;
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            padding: 2.5rem 2rem;
            text-align: center;
            border-radius: var(--radius);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .feature-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(145deg, #1b0a28, #0e0512);
            z-index: -1;
            border-radius: var(--radius);
        }
        
        .feature-card:after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(120deg, var(--purple), var(--orange), var(--blue));
            z-index: -2;
            border-radius: calc(var(--radius) + 2px);
            opacity: 0.4;
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.5);
        }
        
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            background: linear-gradient(90deg, var(--purple), var(--orange));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .feature-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .feature-desc {
            color: #ccc;
            margin-bottom: 2rem;
            font-size: 0.95rem;
        }
        
        .feature-link {
            display: inline-flex;
            align-items: center;
            color: var(--orange);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .feature-link i {
            margin-left: 0.5rem;
            transition: var(--transition);
        }
        
        .feature-link:hover i {
            transform: translateX(5px);
        }
        
        /* ===== Tools Showcase ===== */
        .tools {
            padding: 6rem 5%;
            background: rgba(10, 0, 18, 0.7);
        }
        
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }
        
        .tool-card {
            padding: 2rem;
            border-radius: var(--radius);
            transition: var(--transition);
            background: rgba(27, 10, 40, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .tool-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(106, 13, 173, 0.3);
            border-color: var(--purple);
        }
        
        .tool-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--orange);
        }
        
        .tool-title {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .tool-desc {
            color: #ccc;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .tool-features {
            list-style: none;
            margin-bottom: 1.5rem;
        }
        
        .tool-features li {
            padding: 0.3rem 0;
            color: #ccc;
            display: flex;
            align-items: center;
        }
        
        .tool-features li i {
            color: var(--teal);
            margin-right: 0.5rem;
            font-size: 0.8rem;
        }
        
        /* ===== CTA Section ===== */
        .cta {
            padding: 6rem 5%;
            text-align: center;
            background: linear-gradient(120deg, var(--purple), var(--orange));
            margin: 0 5%;
            border-radius: var(--radius);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        .cta:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'/%3E%3C/svg%3E");
            z-index: -1;
            opacity: 0.3;
        }
        
        .cta-content {
            max-width: 700px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        
        .cta-text {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        .btn-light {
            background: #fff;
            color: var(--purple);
            font-weight: 600;
        }
        
        .btn-light:hover {
            background: var(--light-gray);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn-dark {
            background: rgba(0, 0, 0, 0.3);
            color: #fff;
            border: 2px solid #fff;
        }
        
        .btn-dark:hover {
            background: #fff;
            color: var(--purple);
        }
        
        /* ===== Footer ===== */
        footer {
            padding: 5rem 5% 2rem;
            background: var(--darker-purple);
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 3rem;
        }
        
        .footer-brand {
            margin-bottom: 1.5rem;
        }
        
        .footer-logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--orange);
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .footer-desc {
            color: #ccc;
            font-size: 0.95rem;
            line-height: 1.7;
        }
        
        .footer-heading {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: #fff;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-heading:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--orange);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-link {
            margin-bottom: 0.8rem;
        }
        
        .footer-link a {
            color: #ccc;
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }
        
        .footer-link a i {
            margin-right: 0.5rem;
            font-size: 0.8rem;
            color: var(--teal);
        }
        
        .footer-link a:hover {
            color: var(--orange);
            padding-left: 5px;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #ccc;
            font-size: 0.9rem;
        }
        
        /* ===== Responsive Design ===== */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }
            
            .hero-stats {
                flex-wrap: wrap;
                gap: 2rem;
            }
            
            .nav-links {
                display: none;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.2rem;
            }
            
            .hero-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .cta-title {
                font-size: 2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
            }
        }
        
        /* Mobile menu button */
        .menu-toggle {
            display: none;
            background: var(--orange);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
            transition: var(--transition);
        }
        
        @media (max-width: 992px) {
            .menu-toggle {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js"></div>

    <!-- Navigation -->
    <nav class="navbar">
        <a href="#" class="logo">
            <i class="fas fa-search"></i>SemanticSEO
        </a>
        
        <ul class="nav-links">
            <li><a href="#" class="active">Home</a></li>
            <li><a href="#">Features</a></li>
            <li><a href="#">Pricing</a></li>
            <li><a href="#">Blog</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
        
        <div class="nav-actions">
            <a href="#" class="btn btn-outline">Login</a>
            <a href="#" class="btn btn-primary">Sign Up Free</a>
        </div>
        
        <button class="menu-toggle">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">AI-Powered Semantic SEO Analysis</h1>
            <p class="hero-subtitle">Unlock your website's true potential with our advanced SEO tools that analyze content semantically, identify opportunities, and provide actionable insights to dominate search rankings.</p>
            
            <div class="hero-actions">
                <a href="#features" class="btn btn-primary hero-btn">Explore Tools</a>
                <a href="#" class="btn btn-outline hero-btn">Live Demo</a>
            </div>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-number">15,892</div>
                    <div class="stat-label">Websites Analyzed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">98.7%</div>
                    <div class="stat-label">Accuracy Rate</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">2.4M</div>
                    <div class="stat-label">Keywords Processed</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="features">
        <div class="section-header">
            <h2 class="section-title">Advanced SEO Tools</h2>
            <p class="section-subtitle">Our comprehensive suite of AI-powered tools helps you analyze, optimize, and dominate search engine rankings with semantic precision.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="feature-title">SEO Audit</h3>
                <p class="feature-desc">Comprehensive technical SEO analysis to identify issues, broken links, and performance gaps that might be holding your site back.</p>
                <a href="#" class="feature-link">Run Audit <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h3 class="feature-title">Keyword Analyzer</h3>
                <p class="feature-desc">Advanced keyword research tool that analyzes density, semantic relationships, and competitor keyword strategies.</p>
                <a href="#" class="feature-link">Analyze Keywords <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="feature-title">Content Optimizer</h3>
                <p class="feature-desc">AI-powered content analysis that provides improvement suggestions for headings, metadata, and semantic flow.</p>
                <a href="#" class="feature-link">Optimize Content <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Tools Showcase Section -->
    <section class="tools">
        <div class="section-header">
            <h2 class="section-title">Complete SEO Toolkit</h2>
            <p class="section-subtitle">Everything you need to improve your search engine visibility and drive more organic traffic to your website.</p>
        </div>
        
        <div class="tools-grid">
            <div class="tool-card">
                <div class="tool-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="tool-title">Rank Tracker</h3>
                <p class="tool-desc">Monitor your keyword rankings across search engines and track your SEO progress over time.</p>
                <ul class="tool-features">
                    <li><i class="fas fa-check"></i> Daily ranking updates</li>
                    <li><i class="fas fa-check"></i> Competitor comparison</li>
                    <li><i class="fas fa-check"></i> Historical data analysis</li>
                </ul>
                <a href="#" class="btn btn-outline">Learn More</a>
            </div>
            
            <div class="tool-card">
                <div class="tool-icon">
                    <i class="fas fa-link"></i>
                </div>
                <h3 class="tool-title">Backlink Analyzer</h3>
                <p class="tool-desc">Analyze your backlink profile, find linking opportunities, and monitor your link building progress.</p>
                <ul class="tool-features">
                    <li><i class="fas fa-check"></i> Domain authority checker</li>
                    <li><i class="fas fa-check"></i> Toxic link detection</li>
                    <li><i class="fas fa-check"></i> Competitor backlink analysis</li>
                </ul>
                <a href="#" class="btn btn-outline">Learn More</a>
            </div>
            
            <div class="tool-card">
                <div class="tool-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="tool-title">Competitor Analysis</h3>
                <p class="tool-desc">Spy on your competitors' SEO strategies and discover opportunities to outrank them.</p>
                <ul class="tool-features">
                    <li><i class="fas fa-check"></i> Keyword gap analysis</li>
                    <li><i class="fas fa-check"></i> Content strategy insights</li>
                    <li><i class="fas fa-check"></i> Backlink profile comparison</li>
                </ul>
                <a href="#" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Dominate SEO Rankings?</h2>
            <p class="cta-text">Join thousands of marketers and website owners who use SemanticSEO to improve their search engine rankings and drive more organic traffic.</p>
            
            <div class="cta-buttons">
                <a href="#" class="btn btn-light">Get Started For Free</a>
                <a href="#" class="btn btn-dark">Schedule a Demo</a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-grid">
            <div>
                <div class="footer-brand">
                    <a href="#" class="footer-logo">
                        <i class="fas fa-search"></i>SemanticSEO
                    </a>
                    <p class="footer-desc">Advanced SEO analysis tools to help you improve your search engine rankings, drive more traffic, and grow your business.</p>
                </div>
            </div>
            
            <div>
                <h3 class="footer-heading">Product</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Features</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Pricing</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Use Cases</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Testimonials</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="footer-heading">Resources</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Blog</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Guides</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Webinars</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> API Documentation</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="footer-heading">Company</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> About Us</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Careers</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Privacy Policy</a></li>
                    <li class="footer-link"><a href="#"><i class="fas fa-chevron-right"></i> Terms of Service</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2023 SemanticSEO. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Initialize particles.js
        document.addEventListener('DOMContentLoaded', function() {
            particlesJS('particles-js', {
                particles: {
                    number: { value: 80, density: { enable: true, value_area: 800 } },
                    color: { value: "#6a0dad" },
                    shape: { type: "circle" },
                    opacity: { value: 0.5, random: true },
                    size: { value: 3, random: true },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: "#6a0dad",
                        opacity: 0.4,
                        width: 1
                    },
                    move: {
                        enable: true,
                        speed: 2,
                        direction: "none",
                        random: true,
                        out_mode: "out",
                        bounce: false
                    }
                },
                interactivity: {
                    detect_on: "canvas",
                    events: {
                        onhover: { enable: true, mode: "grab" },
                        onclick: { enable: true, mode: "push" },
                        resize: true
                    }
                },
                retina_detect: true
            });
        });
        
        // Simple animation for stats
        function animateValue(element, start, end, duration) {
            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }
        
        // Animate stats when in viewport
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const statElements = document.querySelectorAll('.stat-number');
                    statElements.forEach(stat => {
                        const target = +stat.innerText.replace(',', '');
                        animateValue(stat, 0, target, 2000);
                    });
                    observer.disconnect();
                }
            });
        }, { threshold: 0.5 });
        
        observer.observe(document.querySelector('.hero-stats'));
    </script>
</body>
</html>
