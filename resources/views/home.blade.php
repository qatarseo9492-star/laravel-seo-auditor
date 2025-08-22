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
        
        /* NEW: Semantic SEO Master Analyzer Styles */
        .analyzer-section {
            padding: 5rem 5%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .analyzer-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .progress-container {
            background: white;
            border-radius: var(--radius);
            padding: 2rem;
            margin-bottom: 3rem;
            box-shadow: var(--shadow);
            text-align: center;
        }
        
        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .progress-percentage {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }
        
        .progress-text {
            color: var(--text-light);
        }
        
        .progress-bar-container {
            height: 12px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 10px;
            transition: width 0.5s ease;
        }
        
        .progress-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .category-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 2rem;
            justify-content: center;
        }
        
        .category-tab {
            padding: 0.8rem 1.5rem;
            background: white;
            border-radius: var(--radius);
            cursor: pointer;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
            border: 2px solid transparent;
        }
        
        .category-tab.active {
            background: var(--primary);
            color: white;
        }
        
        .category-tab:hover:not(.active) {
            border-color: var(--primary);
        }
        
        .checklist-category {
            display: none;
            background: white;
            border-radius: var(--radius);
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow);
        }
        
        .checklist-category.active {
            display: block;
        }
        
        .category-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .category-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            color: var(--primary);
            font-size: 1.5rem;
        }
        
        .category-title {
            font-size: 1.5rem;
            font-weight: 700;
        }
        
        .checklist-items {
            list-style: none;
        }
        
        .checklist-item {
            padding: 1rem 0;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: flex-start;
        }
        
        .checklist-item:last-child {
            border-bottom: none;
        }
        
        .checklist-item input {
            margin-right: 1rem;
            margin-top: 0.3rem;
            cursor: pointer;
            width: 20px;
            height: 20px;
        }
        
        .checklist-item label {
            flex: 1;
            cursor: pointer;
            font-weight: 500;
        }
        
        .checklist-item-desc {
            color: var(--text-light);
            margin-top: 0.5rem;
            font-size: 0.9rem;
            padding-left: 2rem;
        }
        
        .save-confirmation {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--secondary);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            display: none;
            align-items: center;
            gap: 0.5rem;
        }
        
        .save-confirmation.show {
            display: flex;
            animation: fadeInOut 3s forwards;
        }
        
        @keyframes fadeInOut {
            0% { opacity: 0; transform: translateY(20px); }
            20% { opacity: 1; transform: translateY(0); }
            80% { opacity: 1; transform: translateY(0); }
            100% { opacity: 0; transform: translateY(20px); }
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
            
            .category-tabs {
                overflow-x: auto;
                justify-content: flex-start;
                padding-bottom: 0.5rem;
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
            
            .progress-actions {
                flex-direction: column;
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
                <li><a href="#features">Features</a></li>
                <li><a href="#analyzer">Analyzer</a></li>
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
                <a href="#analyzer" class="btn btn-outline hero-btn">Try Analyzer</a>
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
                <a href="#analyzer" class="feature-link">Run Audit <i class="fas fa-arrow-right"></i></a>
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

    <!-- NEW: Semantic SEO Master Analyzer Section -->
    <section class="analyzer-section" id="analyzer">
        <div class="section-header">
            <h2 class="section-title">Semantic SEO Master Analyzer</h2>
            <p class="section-subtitle">Complete this comprehensive checklist to optimize your website for semantic search and improve your search engine rankings.</p>
        </div>
        
        <div class="progress-container">
            <div class="progress-info">
                <div class="progress-percentage" id="progressPercentage">0%</div>
                <div class="progress-text">Complete</div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar" style="width: 0%"></div>
            </div>
            <div class="progress-actions">
                <button class="btn btn-primary" id="saveProgress">Save Progress</button>
                <button class="btn btn-outline" id="resetProgress">Reset Checklist</button>
                <button class="btn btn-outline" id="printChecklist">Print Checklist</button>
            </div>
        </div>
        
        <div class="category-tabs" id="categoryTabs">
            <div class="category-tab active" data-category="content">Content & Keywords</div>
            <div class="category-tab" data-category="technical">Technical Elements</div>
            <div class="category-tab" data-category="quality">Content Quality</div>
            <div class="category-tab" data-category="structure">Structure & Architecture</div>
            <div class="category-tab" data-category="user">User Signals & Experience</div>
            <div class="category-tab" data-category="entities">Entities & Context</div>
        </div>
        
        <div class="checklist-category active" id="contentCategory">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h3 class="category-title">Content & Keywords</h3>
            </div>
            <ul class="checklist-items">
                <li class="checklist-item">
                    <input type="checkbox" id="item1" data-category="content">
                    <label for="item1">Target topic clusters instead of individual keywords</label>
                    <div class="checklist-item-desc">Create content around pillar pages and supporting cluster content to establish topical authority.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item2" data-category="content">
                    <label for="item2">Use semantic keywords naturally throughout content</label>
                    <div class="checklist-item-desc">Include related terms and concepts that help search engines understand context.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item3" data-category="content">
                    <label for="item3">Implement latent semantic indexing (LSI) keywords</label>
                    <div class="checklist-item-desc">Include conceptually related terms that frequently appear together with your target keywords.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item4" data-category="content">
                    <label for="item4">Create comprehensive content covering all aspects of a topic</label>
                    <div class="checklist-item-desc">Address all possible questions and subtopics to become the definitive resource.</div>
                </li>
            </ul>
        </div>
        
        <div class="checklist-category" id="technicalCategory">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-cogs"></i>
                </div>
                <h3 class="category-title">Technical Elements</h3>
            </div>
            <ul class="checklist-items">
                <li class="checklist-item">
                    <input type="checkbox" id="item5" data-category="technical">
                    <label for="item5">Implement schema markup (JSON-LD)</label>
                    <div class="checklist-item-desc">Add structured data to help search engines understand your content's context and meaning.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item6" data-category="technical">
                    <label for="item6">Optimize site speed and performance</label>
                    <div class="checklist-item-desc">Fast-loading sites provide better user experience and are favored by search engines.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item7" data-category="technical">
                    <label for="item7">Ensure mobile responsiveness</label>
                    <div class="checklist-item-desc">Your site must work perfectly on all devices to meet modern SEO standards.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item8" data-category="technical">
                    <label for="item8">Fix crawl errors and broken links</label>
                    <div class="checklist-item-desc">Regularly audit your site to ensure search engines can properly index your content.</div>
                </li>
            </ul>
        </div>
        
        <div class="checklist-category" id="qualityCategory">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3 class="category-title">Content Quality</h3>
            </div>
            <ul class="checklist-items">
                <li class="checklist-item">
                    <input type="checkbox" id="item9" data-category="quality">
                    <label for="item9">Write for users first, search engines second</label>
                    <div class="checklist-item-desc">Create content that genuinely helps and engages your audience.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item10" data-category="quality">
                    <label for="item10">Establish E-E-A-T (Experience, Expertise, Authoritativeness, Trustworthiness)</label>
                    <div class="checklist-item-desc">Demonstrate why you're a credible source on the topic.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item11" data-category="quality">
                    <label for="item11">Update content regularly to maintain freshness</label>
                    <div class="checklist-item-desc">Search engines favor current, up-to-date information.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item12" data-category="quality">
                    <label for="item12">Ensure content is original and not duplicated elsewhere</label>
                    <div class="checklist-item-desc">Unique content provides more value and performs better in search results.</div>
                </li>
            </ul>
        </div>
        
        <div class="checklist-category" id="structureCategory">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-sitemap"></i>
                </div>
                <h3 class="category-title">Structure & Architecture</h3>
            </div>
            <ul class="checklist-items">
                <li class="checklist-item">
                    <input type="checkbox" id="item13" data-category="structure">
                    <label for="item13">Create logical content silos</label>
                    <div class="checklist-item-desc">Organize related content together to strengthen topical relevance.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item14" data-category="structure">
                    <label for="item14">Implement breadcrumb navigation</label>
                    <div class="checklist-item-desc">Help users and search engines understand your site structure.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item15" data-category="structure">
                    <label for="item15">Optimize internal linking with semantic context</label>
                    <div class="checklist-item-desc">Use descriptive anchor text that provides context about the linked content.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item16" data-category="structure">
                    <label for="item16">Create a comprehensive FAQ section</label>
                    <div class="checklist-item-desc">Address common questions to capture featured snippet opportunities.</div>
                </li>
            </ul>
        </div>
        
        <div class="checklist-category" id="userCategory">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="category-title">User Signals & Experience</h3>
            </div>
            <ul class="checklist-items">
                <li class="checklist-item">
                    <input type="checkbox" id="item17" data-category="user">
                    <label for="item17">Reduce bounce rate with engaging content</label>
                    <div class="checklist-item-desc">Create content that encourages users to stay longer and explore your site.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item18" data-category="user">
                    <label for="item18">Optimize for featured snippets</label>
                    <div class="checklist-item-desc">Structure content to directly answer common questions.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item19" data-category="user">
                    <label for="item19">Improve dwell time with comprehensive content</label>
                    <div class="checklist-item-desc">Create in-depth content that keeps users engaged.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item20" data-category="user">
                    <label for="item20">Encourage social sharing and engagement</label>
                    <div class="checklist-item-desc">Social signals can indirectly impact search rankings.</div>
                </li>
            </ul>
        </div>
        
        <div class="checklist-category" id="entitiesCategory">
            <div class="category-header">
                <div class="category-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h3 class="category-title">Entities & Context</h3>
            </div>
            <ul class="checklist-items">
                <li class="checklist-item">
                    <input type="checkbox" id="item21" data-category="entities">
                    <label for="item21">Establish entity relationships in content</label>
                    <div class="checklist-item-desc">Clearly connect people, places, and concepts to help search engines understand context.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item22" data-category="entities">
                    <label for="item22">Build authority on specific entities</label>
                    <div class="checklist-item-desc">Become a recognized source for specific topics, people, or concepts.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item23" data-category="entities">
                    <label for="item23">Use natural language and conversational content</label>
                    <div class="checklist-item-desc">Write how people speak to align with voice search and natural language processing.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item24" data-category="entities">
                    <label for="item24">Leverage knowledge graph optimization techniques</label>
                    <div class="checklist-item-desc">Ensure your content connects to the broader network of information.</div>
                </li>
                <li class="checklist-item">
                    <input type="checkbox" id="item25" data-category="entities">
                    <label for="item25">Create content that answers related questions</label>
                    <div class="checklist-item-desc">Anticipate and answer follow-up questions to become a comprehensive resource.</div>
                </li>
            </ul>
        </div>
        
        <div class="save-confirmation" id="saveConfirmation">
            <i class="fas fa-check-circle"></i>
            <span>Progress saved successfully!</span>
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
            
            // NEW: Semantic SEO Master Analyzer functionality
            const checklistItems = document.querySelectorAll('.checklist-item input');
            const progressBar = document.getElementById('progressBar');
            const progressPercentage = document.getElementById('progressPercentage');
            const saveProgressBtn = document.getElementById('saveProgress');
            const resetProgressBtn = document.getElementById('resetProgress');
            const printChecklistBtn = document.getElementById('printChecklist');
            const saveConfirmation = document.getElementById('saveConfirmation');
            const categoryTabs = document.querySelectorAll('.category-tab');
            
            // Load saved progress from localStorage
            function loadProgress() {
                const savedProgress = localStorage.getItem('seoChecklistProgress');
                if (savedProgress) {
                    const progress = JSON.parse(savedProgress);
                    checklistItems.forEach(item => {
                        if (progress[item.id]) {
                            item.checked = true;
                        }
                    });
                    updateProgress();
                }
            }
            
            // Update progress bar and percentage
            function updateProgress() {
                const totalItems = checklistItems.length;
                const checkedItems = document.querySelectorAll('.checklist-item input:checked').length;
                const percentage = Math.round((checkedItems / totalItems) * 100);
                
                progressBar.style.width = `${percentage}%`;
                progressPercentage.textContent = `${percentage}%`;
            }
            
            // Save progress to localStorage
            function saveProgress() {
                const progress = {};
                checklistItems.forEach(item => {
                    progress[item.id] = item.checked;
                });
                
                localStorage.setItem('seoChecklistProgress', JSON.stringify(progress));
                
                // Show confirmation message
                saveConfirmation.classList.add('show');
                setTimeout(() => {
                    saveConfirmation.classList.remove('show');
                }, 3000);
            }
            
            // Reset all checkboxes
            function resetProgress() {
                if (confirm('Are you sure you want to reset your progress? This cannot be undone.')) {
                    checklistItems.forEach(item => {
                        item.checked = false;
                    });
                    localStorage.removeItem('seoChecklistProgress');
                    updateProgress();
                }
            }
            
            // Print checklist
            function printChecklist() {
                window.print();
            }
            
            // Switch between categories
            function switchCategory(category) {
                // Hide all categories
                document.querySelectorAll('.checklist-category').forEach(cat => {
                    cat.classList.remove('active');
                });
                
                // Show selected category
                document.getElementById(`${category}Category`).classList.add('active');
                
                // Update active tab
                categoryTabs.forEach(tab => {
                    tab.classList.remove('active');
                    if (tab.getAttribute('data-category') === category) {
                        tab.classList.add('active');
                    }
                });
            }
            
            // Event listeners
            checklistItems.forEach(item => {
                item.addEventListener('change', updateProgress);
            });
            
            saveProgressBtn.addEventListener('click', saveProgress);
            resetProgressBtn.addEventListener('click', resetProgress);
            printChecklistBtn.addEventListener('click', printChecklist);
            
            categoryTabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    switchCategory(tab.getAttribute('data-category'));
                });
            });
            
            // Initialize
            loadProgress();
        });
    </script>
</body>
</html>
