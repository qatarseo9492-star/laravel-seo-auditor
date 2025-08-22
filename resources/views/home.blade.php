<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semantic SEO Checker | Advanced SEO Analysis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #10b981;
            --accent: #f59e0b;
            --dark: #111827;
            --darker: #0f172a;
            --light: #f9fafb;
            --text: #1f2937;
            --text-light: #6b7280;
            --transition: all 0.3s ease;
            --radius: 12px;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
        }
        
        /* Header & Navigation */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary);
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
            color: var(--text);
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
            background: var(--primary);
            transition: var(--transition);
        }
        
        .nav-links a:hover, .nav-links a.active {
            color: var(--primary);
        }
        
        .nav-links a:hover:after, .nav-links a.active:after {
            width: 100%;
        }
        
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.7rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 6px rgba(79, 70, 229, 0.2);
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px rgba(79, 70, 229, 0.3);
        }
        
        /* Hero Section */
        .hero {
            padding: 8rem 5% 5rem;
            text-align: center;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .hero-content {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--dark);
            line-height: 1.2;
        }
        
        .hero-title span {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--text-light);
            margin-bottom: 2.5rem;
        }
        
        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 4rem;
        }
        
        .hero-btn {
            padding: 1rem 2rem;
            font-size: 1rem;
        }
        
        .hero-image {
            max-width: 900px;
            margin: 0 auto;
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-hover);
        }
        
        .hero-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Stats Section */
        .stats {
            background: white;
            padding: 4rem 5%;
            margin: 4rem auto;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            max-width: 1200px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
        }
        
        .stat-item {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: var(--text-light);
            font-size: 0.95rem;
        }
        
        /* Features Section */
        .features {
            padding: 5rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 4rem;
        }
        
        .section-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .section-subtitle {
            color: var(--text-light);
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
            background: white;
            padding: 2.5rem;
            border-radius: var(--radius);
            transition: var(--transition);
            box-shadow: var(--shadow);
            text-align: center;
            border-top: 4px solid var(--primary);
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary);
            display: inline-flex;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            background: rgba(99, 102, 241, 0.1);
        }
        
        .feature-title {
            font-size: 1.4rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .feature-desc {
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }
        
        .feature-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary);
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
        
        /* Semantic SEO Section */
        .semantic-section {
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 100%);
            padding: 6rem 5%;
            color: white;
            margin: 5rem 0;
        }
        
        .semantic-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
        }
        
        .semantic-text {
            padding-right: 2rem;
        }
        
        .semantic-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
        }
        
        .semantic-desc {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            opacity: 0.9;
        }
        
        .semantic-list {
            list-style: none;
            margin-bottom: 2rem;
        }
        
        .semantic-list li {
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
        }
        
        .semantic-list li i {
            color: var(--secondary);
            margin-right: 0.8rem;
            font-size: 1.2rem;
        }
        
        .semantic-image {
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-hover);
        }
        
        .semantic-image img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* CTA Section */
        .cta {
            padding: 6rem 5%;
            text-align: center;
            background: white;
            max-width: 1200px;
            margin: 0 auto;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }
        
        .cta-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: var(--dark);
        }
        
        .cta-text {
            color: var(--text-light);
            margin-bottom: 2.5rem;
            font-size: 1.1rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }
        
        /* Footer */
        footer {
            background: var(--darker);
            color: white;
            padding: 5rem 5% 2rem;
            margin-top: 5rem;
        }
        
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
        }
        
        .footer-brand {
            margin-bottom: 1.5rem;
        }
        
        .footer-logo {
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .footer-desc {
            opacity: 0.8;
            line-height: 1.7;
            margin-bottom: 1.5rem;
        }
        
        .footer-heading {
            font-size: 1.2rem;
            margin-bottom: 1.5rem;
            color: white;
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-link {
            margin-bottom: 0.8rem;
        }
        
        .footer-link a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: var(--transition);
        }
        
        .footer-link a:hover {
            color: white;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 3rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-title {
                font-size: 2.8rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .semantic-content {
                grid-template-columns: 1fr;
                gap: 3rem;
            }
            
            .semantic-text {
                padding-right: 0;
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
                font-size: 2rem;
            }
            
            .cta-title {
                font-size: 2rem;
            }
            
            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
        
        /* Mobile menu button */
        .menu-toggle {
            display: none;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius);
            width: 50px;
            height: 50px;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            cursor: pointer;
        }
        
        @media (max-width: 992px) {
            .menu-toggle {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <!-- Header & Navigation -->
    <header>
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
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Advanced <span>Semantic SEO</span> Analysis</h1>
            <p class="hero-subtitle">Unlock the power of semantic search with our AI-driven SEO platform. Analyze content meaning, context, and relationships to dominate search engine rankings.</p>
            
            <div class="hero-actions">
                <a href="#features" class="btn btn-primary hero-btn">Explore Tools</a>
                <a href="#" class="btn btn-outline hero-btn">Live Demo</a>
            </div>
        </div>
        
        <div class="hero-image">
            <img src="https://images.unsplash.com/photo-1516387938699-a93567ec168e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80" alt="SEO Analysis Dashboard">
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats">
        <div class="stats-grid">
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
            <div class="stat-item">
                <div class="stat-number">127</div>
                <div class="stat-label">SEO Factors Checked</div>
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
                <h3 class="feature-title">Semantic SEO Audit</h3>
                <p class="feature-desc">Comprehensive technical SEO analysis with semantic understanding to identify issues and optimization opportunities.</p>
                <a href="#" class="feature-link">Run Audit <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h3 class="feature-title">Semantic Keyword Analysis</h3>
                <p class="feature-desc">Advanced keyword research that understands semantic relationships and contextual meaning.</p>
                <a href="#" class="feature-link">Analyze Keywords <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="feature-title">Content Optimization</h3>
                <p class="feature-desc">AI-powered content analysis that provides semantic improvement suggestions.</p>
                <a href="#" class="feature-link">Optimize Content <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Semantic SEO Section -->
    <section class="semantic-section">
        <div class="semantic-content">
            <div class="semantic-text">
                <h2 class="semantic-title">Understanding Semantic SEO</h2>
                <p class="semantic-desc">Semantic SEO goes beyond traditional keyword matching to understand the meaning and context behind search queries and content.</p>
                
                <ul class="semantic-list">
                    <li><i class="fas fa-check-circle"></i> Entity-based content analysis</li>
                    <li><i class="fas fa-check-circle"></i> Topic clustering and authority building</li>
                    <li><i class="fas fa-check-circle"></i> Natural language processing</li>
                    <li><i class="fas fa-check-circle"></i> Contextual understanding of search intent</li>
                    <li><i class="fas fa-check-circle"></i> Knowledge graph optimization</li>
                </ul>
                
                <a href="#" class="btn btn-primary">Learn More About Semantic SEO</a>
            </div>
            
            <div class="semantic-image">
                <img src="https://images.unsplash.com/photo-1533750349088-cd871a92f312?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=800&q=80" alt="Semantic SEO Visualization">
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <h2 class="cta-title">Ready to Master Semantic SEO?</h2>
        <p class="cta-text">Join thousands of marketers and website owners who use SemanticSEO to improve their search engine rankings and drive more organic traffic.</p>
        
        <div class="cta-buttons">
            <a href="#" class="btn btn-primary">Get Started For Free</a>
            <a href="#" class="btn btn-outline">Schedule a Demo</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div>
                <div class="footer-brand">
                    <a href="#" class="footer-logo">
                        <i class="fas fa-search"></i>SemanticSEO
                    </a>
                    <p class="footer-desc">Advanced semantic SEO analysis tools to help you improve your search engine rankings, drive more traffic, and grow your business.</p>
                </div>
            </div>
            
            <div>
                <h3 class="footer-heading">Product</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#">Features</a></li>
                    <li class="footer-link"><a href="#">Pricing</a></li>
                    <li class="footer-link"><a href="#">Use Cases</a></li>
                    <li class="footer-link"><a href="#">Testimonials</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="footer-heading">Resources</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#">Blog</a></li>
                    <li class="footer-link"><a href="#">Guides</a></li>
                    <li class="footer-link"><a href="#">Webinars</a></li>
                    <li class="footer-link"><a href="#">API Documentation</a></li>
                </ul>
            </div>
            
            <div>
                <h3 class="footer-heading">Company</h3>
                <ul class="footer-links">
                    <li class="footer-link"><a href="#">About Us</a></li>
                    <li class="footer-link"><a href="#">Careers</a></li>
                    <li class="footer-link"><a href="#">Privacy Policy</a></li>
                    <li class="footer-link"><a href="#">Terms of Service</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2023 SemanticSEO. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Simple animation for stats
        document.addEventListener('DOMContentLoaded', function() {
            const statElements = document.querySelectorAll('.stat-number');
            const statsSection = document.querySelector('.stats');
            
            const options = {
                root: null,
                threshold: 0.5,
                rootMargin: '0px'
            };
            
            const observer = new IntersectionObserver(function(entries, observer) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        statElements.forEach(stat => {
                            const target = +stat.innerText.replace(',', '').replace('%', '');
                            const increment = Math.ceil(target / 100);
                            let current = 0;
                            
                            const timer = setInterval(() => {
                                current += increment;
                                if (current > target) {
                                    stat.innerText = stat.innerText.includes('%') ? target + '%' : target.toLocaleString();
                                    clearInterval(timer);
                                } else {
                                    stat.innerText = stat.innerText.includes('%') ? current + '%' : current.toLocaleString();
                                }
                            }, 20);
                        });
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, options);
            
            observer.observe(statsSection);
        });
    </script>
</body>
</html>
