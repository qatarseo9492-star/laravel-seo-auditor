<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semantic SEO Analyzer | Content Optimization Tool</title>
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
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: sticky;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 0;
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
        
        /* Main Content */
        .main-content {
            padding: 3rem 0;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .page-subtitle {
            color: var(--text-light);
            font-size: 1.1rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Analyzer Tool */
        .analyzer-tool {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2.5rem;
            margin-bottom: 3rem;
        }
        
        .tool-tabs {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 2rem;
        }
        
        .tab {
            padding: 1rem 1.5rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--text-light);
            border-bottom: 3px solid transparent;
            transition: var(--transition);
        }
        
        .tab.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: var(--radius);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.9rem 1.8rem;
            border-radius: var(--radius);
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 1rem;
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
        
        .btn i {
            margin-right: 0.5rem;
        }
        
        /* Results Section */
        .results {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 2.5rem;
            margin-bottom: 3rem;
            display: none;
        }
        
        .results-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .results-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        .score-display {
            text-align: center;
            padding: 2rem;
            background: #f9fafb;
            border-radius: var(--radius);
            margin-bottom: 2rem;
        }
        
        .score-value {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .score-label {
            color: var(--text-light);
            font-size: 1.1rem;
        }
        
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .result-card {
            background: #f9fafb;
            border-radius: var(--radius);
            padding: 1.5rem;
            border-left: 4px solid var(--primary);
        }
        
        .result-card.good {
            border-left-color: var(--secondary);
        }
        
        .result-card.warning {
            border-left-color: var(--accent);
        }
        
        .result-card.error {
            border-left-color: #ef4444;
        }
        
        .result-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .result-title i {
            margin-right: 0.5rem;
        }
        
        .result-desc {
            color: var(--text-light);
            font-size: 0.95rem;
        }
        
        .keyword-list {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .keyword-header {
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .keyword-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
        
        .keyword-tag {
            background: #eef2ff;
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .keyword-tag.primary {
            background: var(--primary);
            color: white;
        }
        
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
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
        
        /* Features Section */
        .features {
            padding: 4rem 0;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-title {
            font-size: 2rem;
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
            padding: 2rem;
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
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: var(--dark);
        }
        
        .feature-desc {
            color: var(--text-light);
            margin-bottom: 1.5rem;
        }
        
        /* Footer */
        footer {
            background: var(--darker);
            color: white;
            padding: 3rem 0 1.5rem;
            margin-top: 4rem;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .footer-brand {
            margin-bottom: 1.5rem;
        }
        
        .footer-logo {
            font-size: 1.5rem;
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
            font-size: 1.1rem;
            margin-bottom: 1.2rem;
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
            padding-top: 2rem;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .page-title {
                font-size: 2rem;
            }
            
            .analyzer-tool, .results {
                padding: 1.5rem;
            }
            
            .tool-tabs {
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .score-value {
                font-size: 2.5rem;
            }
            
            .results-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header>
        <div class="container">
            <nav class="navbar">
                <a href="#" class="logo">
                    <i class="fas fa-search"></i>SemanticSEO
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-title">Semantic SEO Analyzer</h1>
                <p class="page-subtitle">Analyze your content URL and keywords to optimize for semantic SEO and improve search engine rankings</p>
            </div>
            
            <!-- Analyzer Tool -->
            <div class="analyzer-tool">
                <div class="tool-tabs">
                    <div class="tab active" data-tab="url">URL Analysis</div>
                    <div class="tab" data-tab="keyword">Keyword Analysis</div>
                    <div class="tab" data-tab="content">Content Analysis</div>
                </div>
                
                <div class="tab-content active" id="url-tab">
                    <div class="form-group">
                        <label for="url">Enter URL to Analyze</label>
                        <input type="url" id="url" class="form-control" placeholder="https://example.com/blog/post-title">
                    </div>
                    
                    <div class="form-group">
                        <label for="focus-keyword">Primary Keyword (Optional)</label>
                        <input type="text" id="focus-keyword" class="form-control" placeholder="Enter primary keyword">
                    </div>
                    
                    <button class="btn btn-primary" id="analyze-url">
                        <i class="fas fa-chart-line"></i> Analyze URL
                    </button>
                </div>
                
                <div class="tab-content" id="keyword-tab">
                    <div class="form-group">
                        <label for="keywords">Enter Keywords (comma separated)</label>
                        <input type="text" id="keywords" class="form-control" placeholder="seo tips, search optimization, semantic seo">
                    </div>
                    
                    <div class="form-group">
                        <label for="content-type">Content Type</label>
                        <select id="content-type" class="form-control">
                            <option value="blog">Blog Post</option>
                            <option value="product">Product Page</option>
                            <option value="landing">Landing Page</option>
                            <option value="article">Article</option>
                        </select>
                    </div>
                    
                    <button class="btn btn-primary" id="analyze-keywords">
                        <i class="fas fa-key"></i> Analyze Keywords
                    </button>
                </div>
                
                <div class="tab-content" id="content-tab">
                    <div class="form-group">
                        <label for="content">Paste Your Content</label>
                        <textarea id="content" class="form-control" placeholder="Paste your article content here..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="content-keyword">Primary Keyword</label>
                        <input type="text" id="content-keyword" class="form-control" placeholder="Enter primary keyword">
                    </div>
                    
                    <button class="btn btn-primary" id="analyze-content">
                        <i class="fas fa-file-alt"></i> Analyze Content
                    </button>
                </div>
            </div>
            
            <!-- Results Section -->
            <div class="results" id="results">
                <div class="results-header">
                    <h2 class="results-title">Analysis Results</h2>
                    <div class="result-meta">Analyzed: <span id="analysis-time"></span></div>
                </div>
                
                <div class="score-display">
                    <div class="score-value" id="score-value">82</div>
                    <div class="score-label">Semantic SEO Score</div>
                </div>
                
                <div class="results-grid">
                    <div class="result-card good">
                        <h3 class="result-title"><i class="fas fa-check-circle"></i> Keyword Usage</h3>
                        <p class="result-desc">Your primary keyword appears 12 times with good distribution throughout the content.</p>
                    </div>
                    
                    <div class="result-card good">
                        <h3 class="result-title"><i class="fas fa-check-circle"></i> Semantic Relevance</h3>
                        <p class="result-desc">Your content shows strong semantic relationships with related terms and concepts.</p>
                    </div>
                    
                    <div class="result-card warning">
                        <h3 class="result-title"><i class="fas fa-exclamation-triangle"></i> Content Length</h3>
                        <p class="result-desc">Your content could be more comprehensive. Consider adding 200-300 more words.</p>
                    </div>
                    
                    <div class="result-card">
                        <h3 class="result-title"><i class="fas fa-info-circle"></i> Readability</h3>
                        <p class="result-desc">Content is fairly easy to read with a Flesch-Kincaid grade level of 8.2.</p>
                    </div>
                    
                    <div class="result-card error">
                        <h3 class="result-title"><i class="fas fa-times-circle"></i> Headings Structure</h3>
                        <p class="result-desc">Your H2 and H3 headings need better keyword integration and semantic structure.</p>
                    </div>
                    
                    <div class="result-card">
                        <h3 class="result-title"><i class="fas fa-info-circle"></i> Entity Coverage</h3>
                        <p class="result-desc">You've covered 7 out of 10 important entities related to your topic.</p>
                    </div>
                </div>
                
                <div class="keyword-list">
                    <h3 class="keyword-header">Recommended Related Keywords</h3>
                    <div class="keyword-tags">
                        <span class="keyword-tag primary">semantic search</span>
                        <span class="keyword-tag">entity seo</span>
                        <span class="keyword-tag">topic clusters</span>
                        <span class="keyword-tag">latent semantic indexing</span>
                        <span class="keyword-tag">natural language processing</span>
                        <span class="keyword-tag">search intent</span>
                        <span class="keyword-tag">knowledge graph</span>
                    </div>
                </div>
                
                <div class="actions">
                    <button class="btn btn-outline" id="export-pdf">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </button>
                    <button class="btn btn-primary" id="new-analysis">
                        <i class="fas fa-redo"></i> New Analysis
                    </button>
                </div>
            </div>
            
            <!-- Features Section -->
            <div class="features">
                <div class="section-header">
                    <h2 class="section-title">How Semantic SEO Analysis Works</h2>
                    <p class="section-subtitle">Our advanced algorithms analyze multiple factors to provide comprehensive SEO recommendations</p>
                </div>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-brain"></i>
                        </div>
                        <h3 class="feature-title">Semantic Analysis</h3>
                        <p class="feature-desc">Understands content meaning and context beyond simple keyword matching.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h3 class="feature-title">Topic Modeling</h3>
                        <p class="feature-desc">Identifies related topics and concepts to improve content comprehensiveness.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="feature-title">Keyword Optimization</h3>
                        <p class="feature-desc">Analyzes keyword usage, density, and distribution for optimal SEO value.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-lightbulb"></i>
                        </div>
                        <h3 class="feature-title">Content Recommendations</h3>
                        <p class="feature-desc">Provides actionable suggestions to improve your content for both users and search engines.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div>
                    <div class="footer-brand">
                        <a href="#" class="footer-logo">
                            <i class="fas fa-search"></i>SemanticSEO
                        </a>
                        <p class="footer-desc">Advanced semantic SEO analysis tools to help you improve your search engine rankings and drive more organic traffic.</p>
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
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab functionality
            const tabs = document.querySelectorAll('.tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const tabId = tab.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    tab.classList.add('active');
                    document.getElementById(`${tabId}-tab`).classList.add('active');
                });
            });
            
            // Analyze buttons functionality
            const analyzeUrlBtn = document.getElementById('analyze-url');
            const analyzeKeywordsBtn = document.getElementById('analyze-keywords');
            const analyzeContentBtn = document.getElementById('analyze-content');
            const resultsSection = document.getElementById('results');
            const analysisTime = document.getElementById('analysis-time');
            const scoreValue = document.getElementById('score-value');
            const newAnalysisBtn = document.getElementById('new-analysis');
            
            // Set current date and time
            const now = new Date();
            analysisTime.textContent = now.toLocaleString();
            
            // Analyze URL
            analyzeUrlBtn.addEventListener('click', () => {
                const url = document.getElementById('url').value;
                if (!url) {
                    alert('Please enter a URL to analyze');
                    return;
                }
                
                // Simulate analysis
                simulateAnalysis();
            });
            
            // Analyze Keywords
            analyzeKeywordsBtn.addEventListener('click', () => {
                const keywords = document.getElementById('keywords').value;
                if (!keywords) {
                    alert('Please enter keywords to analyze');
                    return;
                }
                
                // Simulate analysis
                simulateAnalysis();
            });
            
            // Analyze Content
            analyzeContentBtn.addEventListener('click', () => {
                const content = document.getElementById('content').value;
                if (!content) {
                    alert('Please enter content to analyze');
                    return;
                }
                
                // Simulate analysis
                simulateAnalysis();
            });
            
            // New Analysis
            newAnalysisBtn.addEventListener('click', () => {
                resultsSection.style.display = 'none';
                
                // Clear form fields
                document.getElementById('url').value = '';
                document.getElementById('focus-keyword').value = '';
                document.getElementById('keywords').value = '';
                document.getElementById('content').value = '';
                document.getElementById('content-keyword').value = '';
            });
            
            // Simulate analysis process
            function simulateAnalysis() {
                analyzeUrlBtn.disabled = true;
                analyzeKeywordsBtn.disabled = true;
                analyzeContentBtn.disabled = true;
                
                analyzeUrlBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
                
                // Random score between 65-95
                const randomScore = Math.floor(Math.random() * 30) + 65;
                
                setTimeout(() => {
                    resultsSection.style.display = 'block';
                    scoreValue.textContent = randomScore;
                    
                    // Scroll to results
                    resultsSection.scrollIntoView({ behavior: 'smooth' });
                    
                    // Reset buttons
                    analyzeUrlBtn.disabled = false;
                    analyzeKeywordsBtn.disabled = false;
                    analyzeContentBtn.disabled = false;
                    analyzeUrlBtn.innerHTML = '<i class="fas fa-chart-line"></i> Analyze URL';
                }, 2000);
            }
        });
    </script>
</body>
</html>
