<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semantic SEO Checker - Advanced Website Analysis</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --accent: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --warning: #f9c74f;
            --danger: #f94144;
            --gray: #6c757d;
            --dark-bg: #0f0f23;
            --dark-panel: #1a1a2e;
            --dark-text: #e2e2e2;
            --dark-border: #2d2d4d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark-text);
            background-color: var(--dark-bg);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(67, 97, 238, 0.15) 0%, transparent 20%),
                        radial-gradient(circle at 90% 80%, rgba(247, 37, 133, 0.15) 0%, transparent 20%);
            z-index: -1;
        }
        
        .container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header Styles */
        header {
            background: rgba(26, 26, 46, 0.8);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--dark-border);
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            color: white;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 10px;
        }
        
        .nav-links {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }
        
        .nav-links a {
            color: var(--dark-text);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
            padding: 8px 12px;
            border-radius: 8px;
        }
        
        .nav-links a:hover {
            color: var(--primary);
            background: rgba(255, 255, 255, 0.05);
        }
        
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        /* Hero Section */
        .hero {
            padding: 5rem 0;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at center, rgba(67, 97, 238, 0.1) 0%, transparent 70%);
            z-index: -1;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2.5rem;
            color: var(--gray);
        }
        
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(67, 97, 238, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 1px solid var(--dark-border);
            backdrop-filter: blur(10px);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-3px);
        }
        
        /* Features Section */
        .features {
            padding: 5rem 0;
            background: rgba(26, 26, 46, 0.5);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            margin: 2rem auto;
            border: 1px solid var(--dark-border);
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title h2 {
            font-size: 2.5rem;
            color: white;
            margin-bottom: 1rem;
        }
        
        .section-title p {
            color: var(--gray);
            max-width: 700px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .feature-card {
            background: var(--dark-panel);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid var(--dark-border);
            position: relative;
            overflow: hidden;
        }
        
        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
        }
        
        .feature-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 1.8rem;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .feature-card h3 {
            margin-bottom: 1rem;
            color: white;
            font-size: 1.4rem;
        }
        
        .feature-card p {
            color: var(--gray);
        }
        
        /* Analyzer Section */
        .analyzer {
            padding: 3rem;
            background: var(--dark-panel);
            border-radius: 20px;
            margin: 3rem auto;
            border: 1px solid var(--dark-border);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .analyzer-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .analyzer-header h2 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: white;
        }
        
        .analyzer-header p {
            color: var(--gray);
        }
        
        .analyzer-form {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        
        .url-input {
            flex: 1;
            min-width: 300px;
            padding: 1rem 1.5rem;
            border: none;
            border-radius: 50px;
            font-size: 1rem;
            background: rgba(255, 255, 255, 0.05);
            color: white;
            border: 1px solid var(--dark-border);
        }
        
        .url-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        /* Results Section */
        .results {
            background: var(--dark-panel);
            border-radius: 20px;
            padding: 2rem;
            margin: 3rem auto;
            border: 1px solid var(--dark-border);
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        
        .results-header h2 {
            color: white;
            font-size: 1.8rem;
        }
        
        .score-display {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 1rem 1.5rem;
            border-radius: 50px;
            border: 1px solid var(--dark-border);
        }
        
        .score-circle {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: conic-gradient(var(--primary) 0% 75%, var(--dark-border) 75% 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .score-circle::before {
            content: '';
            position: absolute;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--dark-panel);
        }
        
        .score-value {
            position: relative;
            z-index: 1;
            color: white;
            font-weight: 700;
            font-size: 1.2rem;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        
        .metric-card {
            background: rgba(255, 255, 255, 0.03);
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--dark-border);
            transition: transform 0.3s;
        }
        
        .metric-card:hover {
            transform: translateY(-5px);
        }
        
        .metric-header {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1rem;
        }
        
        .metric-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }
        
        .metric-title {
            color: white;
            font-weight: 600;
        }
        
        .metric-value {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
        }
        
        .metric-description {
            color: var(--gray);
            font-size: 0.9rem;
        }
        
        /* CTA Section */
        .cta {
            padding: 5rem 0;
            text-align: center;
            background: linear-gradient(135deg, var(--secondary) 0%, var(--primary) 100%);
            color: white;
            border-radius: 20px;
            margin: 3rem auto;
        }
        
        .cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }
        
        .cta p {
            max-width: 700px;
            margin: 0 auto 2rem;
            opacity: 0.9;
            font-size: 1.2rem;
        }
        
        .btn-light {
            background: white;
            color: var(--primary);
            font-weight: 600;
        }
        
        .btn-light:hover {
            background: rgba(255, 255, 255, 0.9);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
        }
        
        /* Footer */
        footer {
            background: var(--dark-panel);
            color: white;
            padding: 3rem 0 2rem;
            border-top: 1px solid var(--dark-border);
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-column h3 {
            margin-bottom: 1.5rem;
            font-size: 1.2rem;
            color: white;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-links li {
            margin-bottom: 0.8rem;
        }
        
        .footer-links a {
            color: var(--gray);
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-links a:hover {
            color: var(--primary);
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .social-links a {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.3s;
        }
        
        .social-links a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }
        
        .copyright {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid var(--dark-border);
            color: var(--gray);
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero h1 {
                font-size: 2.8rem;
            }
            
            .nav-links {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: var(--dark-panel);
                flex-direction: column;
                padding: 1rem;
                box-shadow: 0 5px 15px rgba(0,0,0,0.1);
                border-top: 1px solid var(--dark-border);
            }
            
            .nav-links.active {
                display: flex;
            }
            
            .mobile-menu-btn {
                display: block;
            }
            
            .analyzer-form {
                flex-direction: column;
            }
            
            .url-input {
                min-width: auto;
            }
        }
        
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .results-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .score-display {
                width: 100%;
                justify-content: center;
            }
        }
        
        @media (max-width: 576px) {
            .hero-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .feature-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <span>SemanticSEO<span style="color: var(--primary);">Checker</span></span>
                </div>
                <ul class="nav-links">
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Features</a></li>
                    <li><a href="#">Pricing</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contact</a></li>
                </ul>
                <button class="mobile-menu-btn">
                    <i class="fas fa-bars"></i>
                </button>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Advanced Semantic SEO Analysis for Higher Rankings</h1>
                <p>Uncover hidden opportunities with our AI-powered semantic analysis tool that goes beyond traditional SEO checkers</p>
                <div class="hero-buttons">
                    <a href="#analyzer" class="btn btn-primary">Analyze Your Site</a>
                    <a href="#features" class="btn btn-secondary">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="features">
        <div class="container">
            <div class="section-title">
                <h2>Powerful SEO Analysis Features</h2>
                <p>Our tool provides comprehensive insights to improve your website's semantic SEO and overall search visibility</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-brain"></i>
                    </div>
                    <h3>Semantic Analysis</h3>
                    <p>Understand how search engines interpret your content's meaning and context</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h3>Keyword Mapping</h3>
                    <p>Discover related keywords and entities to create comprehensive content</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Content Scoring</h3>
                    <p>Get actionable insights to improve your content's quality and relevance</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-robot"></i>
                    </div>
                    <h3>AI Detection</h3>
                    <p>Identify AI-generated content and optimize for human readability</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3>Mobile Optimization</h3>
                    <p>Check your site's mobile responsiveness and Core Web Vitals</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Detailed Reports</h3>
                    <p>Export comprehensive SEO reports with prioritized recommendations</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Analyzer Section -->
    <section id="analyzer" class="analyzer">
        <div class="container">
            <div class="analyzer-header">
                <h2>SEO Analysis Tool</h2>
                <p>Enter your website URL to get a comprehensive semantic SEO analysis</p>
            </div>
            <form class="analyzer-form">
                <input type="url" class="url-input" placeholder="https://example.com" required>
                <button type="submit" class="btn btn-primary">Analyze Website</button>
            </form>
            <div class="results">
                <div class="results-header">
                    <h2>Analysis Results</h2>
                    <div class="score-display">
                        <div class="score-circle">
                            <span class="score-value">75</span>
                        </div>
                        <div>
                            <div>Overall Score</div>
                            <div style="color: var(--primary); font-weight: 600;">Good</div>
                        </div>
                    </div>
                </div>
                <div class="metrics-grid">
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <h3 class="metric-title">SEO Health</h3>
                        </div>
                        <div class="metric-value">82%</div>
                        <p class="metric-description">Your basic SEO elements are well optimized</p>
                    </div>
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-icon">
                                <i class="fas fa-font"></i>
                            </div>
                            <h3 class="metric-title">Content Quality</h3>
                        </div>
                        <div class="metric-value">68%</div>
                        <p class="metric-description">Could improve semantic richness</p>
                    </div>
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-icon">
                                <i class="fas fa-tachometer-alt"></i>
                            </div>
                            <h3 class="metric-title">Page Speed</h3>
                        </div>
                        <div class="metric-value">91%</div>
                        <p class="metric-description">Excellent loading performance</p>
                    </div>
                    <div class="metric-card">
                        <div class="metric-header">
                            <div class="metric-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <h3 class="metric-title">Mobile Friendly</h3>
                        </div>
                        <div class="metric-value">79%</div>
                        <p class="metric-description">Good but could be improved</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Ready to Improve Your SEO?</h2>
            <p>Join thousands of marketers who use our semantic SEO analyzer to boost their search rankings</p>
            <a href="#analyzer" class="btn btn-light">Get Started Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div class="footer-column">
                    <h3>SemanticSEO Checker</h3>
                    <p>The advanced SEO analysis tool that helps you understand and improve your website's semantic structure.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#"><i class="fab fa-github"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Product</h3>
                    <ul class="footer-links">
                        <li><a href="#">Features</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">Use Cases</a></li>
                        <li><a href="#">Testimonials</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Resources</h3>
                    <ul class="footer-links">
                        <li><a href="#">Blog</a></li>
                        <li><a href="#">Documentation</a></li>
                        <li><a href="#">API</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Company</h3>
                    <ul class="footer-links">
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>&copy; 2023 SemanticSEO Checker. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.querySelector('.mobile-menu-btn').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                
                const targetId = this.getAttribute('href');
                if (targetId === '#') return;
                
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 80,
                        behavior: 'smooth'
                    });
                    
                    // Close mobile menu if open
                    document.querySelector('.nav-links').classList.remove('active');
                }
            });
        });
        
        // Form submission
        document.querySelector('.analyzer-form').addEventListener('submit', function(e) {
            e.preventDefault();
            alert('Analysis would start here. This is a frontend demo.');
        });
    </script>
</body>
</html>
