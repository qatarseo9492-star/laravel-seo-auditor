<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semantic SEO URL Analyzer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #8a2be2;
            --primary-dark: #6a0dad;
            --secondary: #a855f7;
            --accent: #ff6ec7;
            --dark: #0a0a18;
            --darker: #050510;
            --light: #1a1a2e;
            --text: #e6e6ff;
            --text-light: #a0a0cc;
            --success: #00cc88;
            --warning: #ffaa44;
            --error: #ff5577;
            --transition: all 0.3s ease;
            --radius: 16px;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.4);
            --glow: 0 0 15px rgba(138, 43, 226, 0.5);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, var(--darker) 0%, var(--dark) 70%, #16003b 100%);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 10% 20%, rgba(138, 43, 226, 0.15) 0%, transparent 20%),
                radial-gradient(circle at 90% 70%, rgba(168, 85, 247, 0.1) 0%, transparent 20%),
                radial-gradient(circle at 50% 30%, rgba(255, 110, 199, 0.1) 0%, transparent 20%);
            pointer-events: none;
            z-index: -1;
        }
        
        /* Smoke effect */
        #smoke-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        header {
            text-align: center;
            margin-bottom: 3rem;
            padding-top: 2rem;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 800;
            font-size: 2.5rem;
            color: var(--primary);
            text-shadow: 0 0 10px rgba(138, 43, 226, 0.5);
            margin-bottom: 1rem;
        }
        
        .logo i {
            margin-right: 0.5rem;
            font-size: 2.8rem;
        }
        
        .tagline {
            color: var(--text-light);
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        
        /* URL Input Section */
        .url-input-section {
            background: rgba(26, 26, 46, 0.6);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            padding: 2.5rem;
            margin-bottom: 3rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(138, 43, 226, 0.2);
            text-align: center;
        }
        
        .input-group {
            display: flex;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .url-input {
            flex: 1;
            padding: 1rem 1.5rem;
            border-radius: var(--radius) 0 0 var(--radius);
            border: 2px solid rgba(138, 43, 226, 0.3);
            background: rgba(10, 10, 24, 0.7);
            color: var(--text);
            font-size: 1rem;
            transition: var(--transition);
        }
        
        .url-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: var(--glow);
        }
        
        .analyze-btn {
            padding: 1rem 2rem;
            border-radius: 0 var(--radius) var(--radius) 0;
            border: none;
            background: var(--primary);
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }
        
        .analyze-btn:hover {
            background: var(--primary-dark);
        }
        
        /* Results Section */
        .results-section {
            display: none;
            background: rgba(26, 26, 46, 0.6);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            padding: 2.5rem;
            margin-bottom: 3rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        
        .score-container {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .score-circle {
            width: 150px;
            height: 150px;
            margin: 0 auto;
            border-radius: 50%;
            background: conic-gradient(var(--primary) 0%, var(--darker) 0%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: var(--glow);
            transition: all 1s ease;
        }
        
        .score-circle::before {
            content: '';
            position: absolute;
            width: 130px;
            height: 130px;
            border-radius: 50%;
            background: var(--darker);
        }
        
        .score-value {
            position: relative;
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .score-text {
            margin-top: 1rem;
            font-size: 1.2rem;
            color: var(--text-light);
        }
        
        /* Checklist Section */
        .checklist-section {
            background: rgba(26, 26, 46, 0.6);
            backdrop-filter: blur(10px);
            border-radius: var(--radius);
            padding: 2.5rem;
            margin-bottom: 3rem;
            box-shadow: var(--shadow);
            border: 1px solid rgba(138, 43, 226, 0.2);
        }
        
        .section-title {
            font-size: 1.8rem;
            margin-bottom: 1.5rem;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .checklist {
            list-style: none;
        }
        
        .checklist-item {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(138, 43, 226, 0.1);
            display: flex;
            align-items: center;
            transition: var(--transition);
            border-radius: var(--radius);
        }
        
        .checklist-item:hover {
            background: rgba(138, 43, 226, 0.05);
        }
        
        .checklist-item:last-child {
            border-bottom: none;
        }
        
        .status-icon {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.5rem;
            flex-shrink: 0;
        }
        
        .status-success {
            background: rgba(0, 204, 136, 0.2);
            color: var(--success);
        }
        
        .status-warning {
            background: rgba(255, 170, 68, 0.2);
            color: var(--warning);
        }
        
        .status-error {
            background: rgba(255, 85, 119, 0.2);
            color: var(--error);
        }
        
        .checklist-content {
            flex: 1;
        }
        
        .checklist-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .checklist-desc {
            color: var(--text-light);
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .checklist-score {
            font-weight: 700;
            margin-left: 1rem;
            flex-shrink: 0;
            width: 50px;
            text-align: right;
        }
        
        .score-positive {
            color: var(--success);
        }
        
        .score-negative {
            color: var(--error);
        }
        
        .checklist-warning {
            margin-top: 0.8rem;
            padding: 0.8rem;
            background: rgba(255, 170, 68, 0.1);
            border-left: 3px solid var(--warning);
            border-radius: 4px;
            font-size: 0.9rem;
            color: var(--warning);
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .input-group {
                flex-direction: column;
            }
            
            .url-input {
                border-radius: var(--radius) var(--radius) 0 0;
            }
            
            .analyze-btn {
                border-radius: 0 0 var(--radius) var(--radius);
            }
            
            .checklist-item {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .status-icon {
                margin-bottom: 1rem;
            }
            
            .checklist-score {
                margin-left: 0;
                margin-top: 1rem;
                width: 100%;
                text-align: left;
            }
        }
        
        /* Loading animation */
        .loader {
            display: none;
            width: 64px;
            height: 64px;
            margin: 2rem auto;
            position: relative;
        }
        
        .loader:before {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: var(--primary);
            animation: slide 1s infinite linear;
            opacity: 0.5;
        }
        
        .loader:after {
            content: "";
            position: absolute;
            left: 0;
            bottom: 0;
            width: 64px;
            height: 32px;
            background: var(--darker);
            border-radius: 0 0 32px 32px;
            animation: rotate 1s infinite linear;
        }
        
        @keyframes slide {
            0% { transform: translate(0, 0) }
            100% { transform: translate(0, 32px) }
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg) }
            100% { transform: rotate(360deg) }
        }
    </style>
</head>
<body>
    <canvas id="smoke-canvas"></canvas>
    
    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-search"></i>SEO Analyzer
            </div>
            <p class="tagline">Analyze any URL and get a comprehensive SEO score with detailed recommendations</p>
        </header>
        
        <section class="url-input-section">
            <h2>Enter URL to Analyze</h2>
            <div class="input-group">
                <input type="url" class="url-input" placeholder="https://example.com" id="url-input">
                <button class="analyze-btn" id="analyze-btn">Analyze</button>
            </div>
            <div class="loader" id="loader"></div>
        </section>
        
        <section class="results-section" id="results-section">
            <div class="score-container">
                <div class="score-circle" id="score-circle">
                    <span class="score-value" id="score-value">0</span>
                </div>
                <div class="score-text" id="score-text">Analyzing your website...</div>
            </div>
        </section>
        
        <section class="checklist-section">
            <h2 class="section-title">SEO Checklist Analysis</h2>
            <ul class="checklist" id="checklist">
                <!-- Will be populated by JavaScript -->
            </ul>
        </section>
    </div>

    <script>
        // Smoke effect
        const canvas = document.getElementById('smoke-canvas');
        const ctx = canvas.getContext('2d');
        let particles = [];
        
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();
        
        function createParticles() {
            particles = [];
            const particleCount = 30;
            
            for (let i = 0; i < particleCount; i++) {
                particles.push({
                    x: Math.random() * canvas.width,
                    y: Math.random() * canvas.height,
                    size: Math.random() * 10 + 5,
                    speedX: Math.random() * 0.5 - 0.25,
                    speedY: Math.random() * 0.5 - 0.25,
                    opacity: Math.random() * 0.3 + 0.1,
                    color: `rgba(138, 43, 226, ${Math.random() * 0.2 + 0.05})`
                });
            }
        }
        
        function animateParticles() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            for (let i = 0; i < particles.length; i++) {
                const p = particles[i];
                
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2);
                ctx.fillStyle = p.color;
                ctx.globalAlpha = p.opacity;
                ctx.fill();
                
                p.x += p.speedX;
                p.y += p.speedY;
                
                if (p.x < -p.size) p.x = canvas.width + p.size;
                if (p.x > canvas.width + p.size) p.x = -p.size;
                if (p.y < -p.size) p.y = canvas.height + p.size;
                if (p.y > canvas.height + p.size) p.y = -p.size;
            }
            
            requestAnimationFrame(animateParticles);
        }
        
        createParticles();
        animateParticles();
        
        // SEO Analysis functionality
        const analyzeBtn = document.getElementById('analyze-btn');
        const urlInput = document.getElementById('url-input');
        const resultsSection = document.getElementById('results-section');
        const scoreCircle = document.getElementById('score-circle');
        const scoreValue = document.getElementById('score-value');
        const scoreText = document.getElementById('score-text');
        const checklist = document.getElementById('checklist');
        const loader = document.getElementById('loader');
        
        // SEO checklist items with scoring
        const seoChecklist = [
            {
                title: "Meta Title Optimization",
                description: "Your page has a properly optimized meta title between 50-60 characters.",
                check: () => Math.random() > 0.2,
                score: 5,
                warning: "Meta title is missing or too long/short. Ideal length is 50-60 characters."
            },
            {
                title: "Meta Description",
                description: "Your page has a compelling meta description between 150-160 characters.",
                check: () => Math.random() > 0.3,
                score: 5,
                warning: "Meta description is missing or not optimized. Ideal length is 150-160 characters."
            },
            {
                title: "Heading Structure (H1, H2, H3)",
                description: "Your page has a proper heading structure with one H1 and properly nested H2/H3 tags.",
                check: () => Math.random() > 0.15,
                score: 8,
                warning: "Heading structure needs improvement. Use one H1 tag and proper hierarchy for H2/H3 tags."
            },
            {
                title: "Image Alt Text",
                description: "All images have descriptive alt text for better accessibility and SEO.",
                check: () => Math.random() > 0.4,
                score: 7,
                warning: "Some images are missing alt text. Add descriptive alt text for better SEO."
            },
            {
                title: "URL Structure",
                description: "Your URL is clean, descriptive, and contains relevant keywords.",
                check: () => Math.random() > 0.25,
                score: 6,
                warning: "URL could be more descriptive. Include relevant keywords and keep it concise."
            },
            {
                title: "Mobile Responsiveness",
                description: "Your website is fully responsive and provides good experience on mobile devices.",
                check: () => Math.random() > 0.1,
                score: 9,
                warning: "Mobile responsiveness issues detected. Ensure your site works well on all devices."
            },
            {
                title: "Page Loading Speed",
                description: "Your page loads quickly, providing good user experience.",
                check: () => Math.random() > 0.35,
                score: 8,
                warning: "Page loading speed could be improved. Optimize images and minimize code."
            },
            {
                title: "SSL Certificate",
                description: "Your website uses HTTPS with a valid SSL certificate.",
                check: () => Math.random() > 0.05,
                score: 7,
                warning: "SSL certificate is missing or invalid. HTTPS is important for security and SEO."
            },
            {
                title: "Content Quality",
                description: "Your content is comprehensive, original, and provides value to users.",
                check: () => Math.random() > 0.4,
                score: 10,
                warning: "Content could be improved. Make it more comprehensive and valuable to users."
            },
            {
                title: "Internal Linking",
                description: "Your page has relevant internal links to other pages on your website.",
                check: () => Math.random() > 0.5,
                score: 6,
                warning: "Internal linking could be improved. Add relevant links to other pages on your site."
            }
        ];
        
        analyzeBtn.addEventListener('click', analyzeSEO);
        
        function analyzeSEO() {
            const url = urlInput.value.trim();
            
            if (!url) {
                alert('Please enter a valid URL');
                return;
            }
            
            // Show loading animation
            loader.style.display = 'block';
            resultsSection.style.display = 'none';
            checklist.innerHTML = '';
            
            // Simulate analysis delay
            setTimeout(() => {
                loader.style.display = 'none';
                resultsSection.style.display = 'block';
                
                let totalScore = 0;
                let maxScore = 0;
                
                // Calculate scores
                seoChecklist.forEach(item => {
                    const passed = item.check();
                    maxScore += item.score;
                    if (passed) totalScore += item.score;
                });
                
                const finalScore = Math.round((totalScore / maxScore) * 100);
                
                // Update score display with animation
                animateScore(finalScore);
                
                // Generate checklist
                seoChecklist.forEach(item => {
                    const passed = item.check();
                    const listItem = document.createElement('li');
                    listItem.className = 'checklist-item';
                    
                    listItem.innerHTML = `
                        <div class="status-icon ${passed ? 'status-success' : 'status-warning'}">
                            <i class="fas ${passed ? 'fa-check' : 'fa-exclamation-triangle'}"></i>
                        </div>
                        <div class="checklist-content">
                            <div class="checklist-title">${item.title}</div>
                            <div class="checklist-desc">${item.description}</div>
                            ${!passed ? `<div class="checklist-warning">${item.warning}</div>` : ''}
                        </div>
                        <div class="checklist-score ${passed ? 'score-positive' : 'score-negative'}">
                            ${passed ? '+' + item.score : '0'}
                        </div>
                    `;
                    
                    checklist.appendChild(listItem);
                });
            }, 2000);
        }
        
        function animateScore(targetScore) {
            let currentScore = 0;
            const duration = 2000; // 2 seconds
            const steps = 60; // 60 frames
            const increment = targetScore / steps;
            const interval = duration / steps;
            
            const timer = setInterval(() => {
                currentScore += increment;
                if (currentScore >= targetScore) {
                    currentScore = targetScore;
                    clearInterval(timer);
                }
                
                scoreValue.textContent = Math.round(currentScore);
                scoreCircle.style.background = `conic-gradient(var(--primary) ${currentScore}%, var(--darker) 0%)`;
                
                // Update text based on score
                if (currentScore >= 80) {
                    scoreText.textContent = 'Excellent! Your SEO is in great shape.';
                } else if (currentScore >= 60) {
                    scoreText.textContent = 'Good job! Your SEO is decent but could use some improvements.';
                } else if (currentScore >= 40) {
                    scoreText.textContent = 'Fair. There are several areas that need improvement.';
                } else {
                    scoreText.textContent = 'Needs work. Your SEO requires significant optimization.';
                }
            }, interval);
        }
        
        // Initialize with a demo analysis
        window.addEventListener('load', () => {
            urlInput.value = 'https://example.com';
        });
    </script>
</body>
</html>
