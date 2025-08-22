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
            --primary: #8a2be2;
            --primary-dark: #6a0dad;
            --secondary: #03dac6;
            --accent: #ff8500;
            --dark: #0f0b1d;
            --darker: #0a0715;
            --darkest: #050310;
            --text: #e6e6e6;
            --text-secondary: #a0a0a0;
            --transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            --radius: 16px;
            --glass: rgba(20, 15, 35, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, var(--darkest) 0%, var(--darker) 50%, var(--dark) 100%);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
            min-height: 100vh;
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
            background: var(--glass);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: var(--radius);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow);
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
            background: rgba(15, 11, 29, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(138, 43, 226, 0.3);
        }
        
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--text);
            transition: var(--transition);
        }
        
        .logo span {
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .logo i {
            margin-right: 0.5rem;
            font-size: 2rem;
            color: var(--secondary);
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
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            transition: var(--transition);
        }
        
        .nav-links a:hover, .nav-links a.active {
            color: var(--secondary);
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
            padding: 0.8rem 1.8rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }
        
        .btn:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary), var(--primary));
            background-size: 200% 100%;
            transition: var(--transition);
            z-index: -1;
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--text);
        }
        
        .btn-outline:hover {
            color: #fff;
            box-shadow: 0 0 20px rgba(138, 43, 226, 0.5);
            transform: translateY(-3px);
        }
        
        .btn-outline:hover:before {
            background-position: 100% 0;
        }
        
        .btn-primary {
            color: #fff;
            box-shadow: 0 6px 18px rgba(138, 43, 226, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(138, 43, 226, 0.6);
        }
        
        .btn-primary:hover:before {
            background-position: 100% 0;
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
            z-index: 2;
        }
        
        .hero-content {
            text-align: center;
            max-width: 950px;
            z-index: 2;
        }
        
        .hero-title {
            font-size: 4rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--secondary), var(--accent), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
            text-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        }
        
        .hero-subtitle {
            font-size: 1.3rem;
            color: var(--text-secondary);
            margin-bottom: 2.5rem;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .hero-actions {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
            margin-bottom: 4rem;
        }
        
        .hero-btn {
            padding: 1.2rem 2.8rem;
            font-size: 1.1rem;
        }
        
        .hero-stats {
            display: flex;
            justify-content: center;
            gap: 4rem;
            margin-top: 3rem;
            flex-wrap: wrap;
        }
        
        .stat-item {
            text-align: center;
            padding: 1.5rem;
            border-radius: var(--radius);
            background: rgba(10, 7, 21, 0.6);
            border: 1px solid rgba(138, 43, 226, 0.2);
            min-width: 180px;
            transition: var(--transition);
        }
        
        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(138, 43, 226, 0.3);
            border-color: var(--primary);
        }
        
        .stat-number {
            font-size: 2.8rem;
            font-weight: 800;
            color: var(--secondary);
            margin-bottom: 0.5rem;
            display: block;
        }
        
        .stat-label {
            color: var(--text-secondary);
            font-size: 0.95rem;
        }
        
        /* ===== Features Section ===== */
        .features {
            padding: 8rem 5%;
            position: relative;
            z-index: 2;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 6rem;
        }
        
        .section-title {
            font-size: 3.2rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .section-subtitle {
            color: var(--text-secondary);
            font-size: 1.2rem;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2.5rem;
        }
        
        .feature-card {
            padding: 2.5rem;
            border-radius: var(--radius);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            z-index: 1;
            background: rgba(15, 11, 29, 0.7);
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        
        .feature-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(138, 43, 226, 0.1), rgba(3, 218, 198, 0.1));
            z-index: -1;
            border-radius: var(--radius);
            opacity: 0;
            transition: var(--transition);
        }
        
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            border-color: var(--primary);
        }
        
        .feature-card:hover:before {
            opacity: 1;
        }
        
        .feature-icon {
            font-size: 3.5rem;
            margin-bottom: 1.5rem;
            display: inline-block;
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .feature-title {
            font-size: 1.6rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--text);
        }
        
        .feature-desc {
            color: var(--text-secondary);
            margin-bottom: 2rem;
            font-size: 1rem;
            line-height: 1.7;
        }
        
        .feature-link {
            display: inline-flex;
            align-items: center;
            color: var(--secondary);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
        }
        
        .feature-link i {
            margin-left: 0.5rem;
            transition: var(--transition);
        }
        
        .feature-link:hover {
            color: var(--primary);
        }
        
        .feature-link:hover i {
            transform: translateX(5px);
        }
        
        /* ===== Semantic SEO Section ===== */
        .semantic-features {
            padding: 8rem 5%;
            background: rgba(10, 7, 21, 0.7);
            position: relative;
        }
        
        .semantic-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            margin-top: 5rem;
        }
        
        .semantic-card {
            padding: 2.5rem;
            border-radius: var(--radius);
            transition: var(--transition);
            background: rgba(15, 11, 29, 0.7);
            border: 1px solid rgba(138, 43, 226, 0.2);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .semantic-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--secondary), var(--primary));
            transition: var(--transition);
        }
        
        .semantic-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        
        .semantic-card:hover:before {
            width: 100%;
            opacity: 0.1;
        }
        
        .semantic-icon {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            color: var(--secondary);
            display: inline-flex;
            width: 80px;
            height: 80px;
            border-radius: 50%;
            align-items: center;
            justify-content: center;
            background: rgba(3, 218, 198, 0.1);
        }
        
        .semantic-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text);
        }
        
        .semantic-desc {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.7;
        }
        
        /* ===== Tools Showcase ===== */
        .tools {
            padding: 8rem 5%;
            position: relative;
        }
        
        .tools-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2.5rem;
            margin-top: 5rem;
        }
        
        .tool-card {
            padding: 2.5rem;
            border-radius: var(--radius);
            transition: var(--transition);
            background: rgba(15, 11, 29, 0.7);
            border: 1px solid rgba(138, 43, 226, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .tool-card:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(to right, var(--secondary), var(--primary));
            transition: var(--transition);
        }
        
        .tool-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }
        
        .tool-card:hover:after {
            height: 8px;
        }
        
        .tool-icon {
            font-size: 2.8rem;
            margin-bottom: 1.5rem;
            color: var(--secondary);
        }
        
        .tool-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--text);
        }
        
        .tool-desc {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            font-size: 1rem;
            line-height: 1.7;
        }
        
        .tool-features {
            list-style: none;
            margin-bottom: 2rem;
        }
        
        .tool-features li {
            padding: 0.5rem 0;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
        }
        
        .tool-features li i {
            color: var(--secondary);
            margin-right: 0.8rem;
            font-size: 0.9rem;
        }
        
        /* ===== CTA Section ===== */
        .cta {
            padding: 8rem 5%;
            text-align: center;
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
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
            max-width: 800px;
            margin: 0 auto;
            position: relative;
            z-index: 2;
        }
        
        .cta-title {
            font-size: 3rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            color: #fff;
        }
        
        .cta-text {
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 2.5rem;
            font-size: 1.2rem;
            line-height: 1.7;
        }
        
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }
        
        .btn-light {
            background: #fff;
            color: var(--primary);
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
            color: var(--primary);
        }
        
        /* ===== Footer ===== */
        footer {
            padding: 6rem 5% 3rem;
            background: var(--darker);
            position: relative;
            z-index: 2;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 3rem;
            margin-bottom: 4rem;
        }
        
        .footer-brand {
            margin-bottom: 2rem;
        }
        
        .footer-logo {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text);
            text-decoration: none;
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
        }
        
        .footer-logo span {
            background: linear-gradient(90deg, var(--secondary), var(--primary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .footer-desc {
            color: var(--text-secondary);
            font-size: 1rem;
            line-height: 1.7;
            max-width: 300px;
        }
        
        .footer-heading {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            color: #fff;
            position: relative;
            padding-bottom: 0.8rem;
        }
        
        .footer-heading:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 40px;
            height: 3px;
            background: var(--secondary);
        }
        
        .footer-links {
            list-style: none;
        }
        
        .footer-link {
            margin-bottom: 1rem;
        }
        
        .footer-link a {
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
            display: flex;
            align-items: center;
        }
        
        .footer-link a i {
            margin-right: 0.8rem;
            font-size: 0.9rem;
            color: var(--secondary);
        }
        
        .footer-link a:hover {
            color: var(--secondary);
            padding-left: 5px;
        }
        
        .footer-bottom {
            text-align: center;
            padding-top: 3rem;
            margin-top: 3rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            font-size: 0.95rem;
        }
        
        /* ===== Responsive Design ===== */
        @media (max-width: 1200px) {
            .hero-title {
                font-size: 3.5rem;
            }
            
            .section-title {
                font-size: 2.8rem;
            }
        }
        
        @media (max-width: 992px) {
            .hero-title {
                font-size: 3rem;
            }
            
            .hero-stats {
                gap: 2rem;
            }
            
            .nav-links {
                display: none;
            }
            
            .section-title {
                font-size: 2.5rem;
            }
            
            .cta-title {
                font-size: 2.5rem;
            }
        }
        
        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-actions {
                flex-direction: column;
                align-items: center;
            }
            
            .hero-stats {
                flex-direction: column;
                align-items: center;
            }
            
            .stat-item {
                width: 100%;
                max-width: 300px;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .cta-title {
                font-size: 2.2rem;
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
            background: var(--primary);
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
        
        /* Animation keyframes */
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
        
        .floating {
            animation: float 5s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <!-- Particles Background -->
    <div id="particles-js"></div>

    <!-- Navigation -->
    <nav class="navbar">
        <a href="#" class="logo">
            <i class="fas fa-search"></i><span>Semantic</span>SEO
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
            <h1 class="hero-title">Advanced Semantic SEO Analysis</h1>
            <p class="hero-subtitle">Unlock the power of semantic search with our AI-driven SEO platform. Analyze content meaning, context, and relationships to dominate search engine rankings.</p>
            
            <div class="hero-actions">
                <a href="#features" class="btn btn-primary hero-btn">Explore Tools</a>
                <a href="#" class="btn btn-outline hero-btn">Live Demo</a>
            </div>
            
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-number" data-count="15892">0</span>
                    <div class="stat-label">Websites Analyzed</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="98.7">0</span>
                    <div class="stat-label">Accuracy Rate</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="2.4">0</span>
                    <div class="stat-label">M Keywords Processed</div>
                </div>
                <div class="stat-item">
                    <span class="stat-number" data-count="127">0</span>
                    <div class="stat-label">SEO Factors Checked</div>
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
                <h3 class="feature-title">Semantic SEO Audit</h3>
                <p class="feature-desc">Comprehensive technical SEO analysis with semantic understanding to identify issues, content gaps, and optimization opportunities based on meaning and context.</p>
                <a href="#" class="feature-link">Run Audit <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-key"></i>
                </div>
                <h3 class="feature-title">Semantic Keyword Analysis</h3>
                <p class="feature-desc">Advanced keyword research that understands semantic relationships, topic clusters, and contextual meaning to build comprehensive content strategies.</p>
                <a href="#" class="feature-link">Analyze Keywords <i class="fas fa-arrow-right"></i></a>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bolt"></i>
                </div>
                <h3 class="feature-title">Content Optimization</h3>
                <p class="feature-desc">AI-powered content analysis that provides semantic improvement suggestions for topical authority, context, and meaning-based optimization.</p>
                <a href="#" class="feature-link">Optimize Content <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Semantic SEO Features -->
    <section class="semantic-features">
        <div class="section-header">
            <h2 class="section-title">Semantic SEO Capabilities</h2>
            <p class="section-subtitle">Go beyond traditional SEO with our advanced semantic analysis features that understand content meaning and context.</p>
        </div>
        
        <div class="semantic-grid">
            <div class="semantic-card">
                <div class="semantic-icon">
                    <i class="fas fa-brain"></i>
                </div>
                <h3 class="semantic-title">Entity Recognition</h3>
                <p class="semantic-desc">Identify and analyze entities in your content to improve context understanding and semantic relevance for search engines.</p>
            </div>
            
            <div class="semantic-card">
                <div class="semantic-icon">
                    <i class="fas fa-project-diagram"></i>
                </div>
                <h3 class="semantic-title">Topic Clustering</h3>
                <p class="semantic-desc">Group related content into topic clusters to build topical authority and improve semantic understanding of your website.</p>
            </div>
            
            <div class="semantic-card">
                <div class="semantic-icon">
                    <i class="fas fa-link"></i>
                </div>
                <h3 class="semantic-title">Semantic Relationships</h3>
                <p class="semantic-desc">Analyze and optimize the semantic relationships between content pieces to improve context and meaning signals.</p>
            </div>
            
            <div class="semantic-card">
                <div class="semantic-icon">
                    <i class="fas fa-chart-network"></i>
                </div>
                <h3 class="semantic-title">Knowledge Graph Optimization</h3>
                <p class="semantic-desc">Optimize your content for knowledge graph integration and improve visibility in semantic search results.</p>
            </div>
            
            <div class="semantic-card">
                <div class="semantic-icon">
                    <i class="fas fa-language"></i>
                </div>
                <h3 class="semantic-title">Natural Language Processing</h3>
                <p class="semantic-desc">Leverage advanced NLP techniques to analyze content meaning, sentiment, and context for better SEO outcomes.</p>
            </div>
            
            <div class="semantic-card">
                <div class="semantic-icon">
                    <i class="fas fa-robot"></i>
                </div>
                <h3 class="semantic-title">AI-Powered Insights</h3>
                <p class="semantic-desc">Get actionable insights powered by machine learning algorithms that understand search intent and content meaning.</p>
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
                <p class="tool-desc">Monitor your keyword rankings across search engines and track your SEO progress over time with semantic insights.</p>
                <ul class="tool-features">
                    <li><i class="fas fa-check"></i> Semantic ranking factors</li>
                    <li><i class="fas fa-check"></i> Competitor comparison</li>
                    <li><i class="fas fa-check"></i> Historical data analysis</li>
                    <li><i class="fas fa-check"></i> SERP feature tracking</li>
                </ul>
                <a href="#" class="btn btn-outline">Learn More</a>
            </div>
            
            <div class="tool-card">
                <div class="tool-icon">
                    <i class="fas fa-link"></i>
                </div>
                <h3 class="tool-title">Semantic Backlink Analysis</h3>
                <p class="tool-desc">Analyze your backlink profile with semantic understanding to identify quality links and topical relevance.</p>
                <ul class="tool-features">
                    <li><i class="fas fa-check"></i> Topical relevance scoring</li>
                    <li><i class="fas fa-check"></i> Toxic link detection</li>
                    <li><i class="fas fa-check"></i> Competitor backlink analysis</li>
                    <li><i class="fas fa-check"></i> Link context analysis</li>
                </ul>
                <a href="#" class="btn btn-outline">Learn More</a>
            </div>
            
            <div class="tool-card">
                <div class="tool-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="tool-title">Semantic Competitor Analysis</h3>
                <p class="tool-desc">Spy on your competitors' SEO strategies with semantic understanding to discover opportunities to outrank them.</p>
                <ul class="tool-features">
                    <li><i class="fas fa-check"></i> Semantic keyword gap analysis</li>
                    <li><i class="fas fa-check"></i> Content strategy insights</li>
                    <li><i class="fas fa-check"></i> Backlink profile comparison</li>
                    <li><i class="fas fa-check"></i> Topical authority mapping</li>
                </ul>
                <a href="#" class="btn btn-outline">Learn More</a>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Master Semantic SEO?</h2>
            <p class="cta-text">Join thousands of marketers and website owners who use SemanticSEO to improve their search engine rankings and drive more organic traffic with advanced semantic analysis.</p>
            
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
                        <i class="fas fa-search"></i><span>Semantic</span>SEO
                    </a>
                    <p class="footer-desc">Advanced semantic SEO analysis tools to help you improve your search engine rankings, drive more traffic, and grow your business.</p>
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
                    number: { value: 100, density: { enable: true, value_area: 800 } },
                    color: { value: "#8a2be2" },
                    shape: { type: "circle" },
                    opacity: { value: 0.5, random: true },
                    size: { value: 3, random: true },
                    line_linked: {
                        enable: true,
                        distance: 150,
                        color: "#8a2be2",
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
            
            // Animate stats
            const statElements = document.querySelectorAll('.stat-number');
            const statsSection = document.querySelector('.hero-stats');
            
            const options = {
                root: null,
                threshold: 0.5,
                rootMargin: '0px'
            };
            
            const observer = new IntersectionObserver(function(entries, observer) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        statElements.forEach(stat => {
                            const target = parseFloat(stat.getAttribute('data-count'));
                            const increment = target / 100;
                            let current = 0;
                            
                            const timer = setInterval(() => {
                                current += increment;
                                if (current >= target) {
                                    stat.innerText = target % 1 === 0 ? Math.floor(target) : target.toFixed(1);
                                    clearInterval(timer);
                                } else {
                                    stat.innerText = current % 1 === 0 ? Math.floor(current) : current.toFixed(1);
                                }
                            }, 20);
                        });
                        
                        observer.unobserve(entry.target);
                    }
                });
            }, options);
            
            observer.observe(statsSection);
        });
        
        // Add floating animation to elements
        document.querySelectorAll('.feature-card, .semantic-card, .tool-card').forEach(el => {
            el.style.animationDelay = `${Math.random() * 0.5}s`;
            el.classList.add('floating');
        });
    </script>
</body>
</html>
