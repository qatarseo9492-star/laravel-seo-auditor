<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ultra AI Content Detection</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3a0ca3;
            --success: #4cc9f0;
            --warning: #f72585;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --human-color: #4cc9f0;
            --ai-color: #f72585;
            --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #212529;
            line-height: 1.6;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 40px;
            padding: 30px 20px;
            background: var(--gradient);
            color: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(67, 97, 238, 0.15);
        }
        
        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }
        
        .card-title {
            font-size: 1.5rem;
            color: var(--primary);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .analysis-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }
        
        @media (max-width: 768px) {
            .analysis-section {
                grid-template-columns: 1fr;
            }
        }
        
        .result-container {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }
        
        .score-meter {
            background: white;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }
        
        .meter-title {
            font-size: 1.2rem;
            margin-bottom: 20px;
            color: var(--dark);
        }
        
        .gauge-container {
            width: 220px;
            height: 220px;
            margin: 0 auto 20px;
            position: relative;
        }
        
        .gauge {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background: conic-gradient(var(--success) 0% 30%, var(--warning) 30% 70%, var(--ai-color) 70% 100%);
            mask: radial-gradient(white 55%, transparent 56%);
            -webkit-mask: radial-gradient(white 55%, transparent 56%);
        }
        
        .gauge-needle {
            position: absolute;
            top: 0;
            left: 50%;
            transform-origin: bottom center;
            width: 4px;
            height: 105px;
            background: var(--dark);
            border-radius: 4px 4px 0 0;
            transform: translateX(-50%) rotate(0deg);
            transition: transform 1s ease-in-out;
        }
        
        .gauge-center {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            background: var(--dark);
            border-radius: 50%;
            z-index: 10;
        }
        
        .score-value {
            font-size: 2.5rem;
            font-weight: bold;
            margin: 10px 0;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .score-label {
            font-size: 1.2rem;
            color: var(--gray);
            margin-bottom: 15px;
        }
        
        .probability-bars {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        
        .probability-bar {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .bar-label {
            width: 100px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .bar-container {
            flex: 1;
            height: 20px;
            background: #e9ecef;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .bar-fill {
            height: 100%;
            border-radius: 10px;
            transition: width 1s ease-in-out;
            position: relative;
        }
        
        .bar-fill.human {
            background: var(--human-color);
        }
        
        .bar-fill.ai {
            background: var(--ai-color);
        }
        
        .bar-value {
            position: absolute;
            right: 10px;
            top: 0;
            height: 100%;
            display: flex;
            align-items: center;
            color: white;
            font-weight: 600;
            font-size: 0.85rem;
            text-shadow: 0 0 2px rgba(0, 0, 0, 0.5);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .feature-box {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease;
        }
        
        .feature-box:hover {
            transform: translateY(-3px);
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-bottom: 15px;
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        
        .feature-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--dark);
        }
        
        .feature-desc {
            font-size: 0.9rem;
            color: var(--gray);
        }
        
        .analysis-details {
            margin-top: 30px;
        }
        
        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--dark);
        }
        
        .detail-value {
            color: var(--gray);
        }
        
        .highlight {
            background: var(--gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }
        
        .input-group {
            margin-bottom: 25px;
        }
        
        .input-label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--dark);
        }
        
        .url-input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .url-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .text-center {
            text-align: center;
        }
        
        .mt-4 {
            margin-top: 40px;
        }
        
        .mb-4 {
            margin-bottom: 40px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><i class="fas fa-robot"></i> Ultra AI Content Detection</h1>
            <p>Advanced ensemble analysis to detect AI-generated content with precision</p>
        </div>
        
        <div class="card">
            <h2 class="card-title"><i class="fas fa-link"></i> Analyze Content</h2>
            <div class="input-group">
                <label class="input-label">Enter URL to analyze</label>
                <input type="text" class="url-input" placeholder="https://example.com">
            </div>
            <button class="btn"><i class="fas fa-search"></i> Analyze Content</button>
        </div>
        
        <div class="analysis-section">
            <div class="card">
                <h2 class="card-title"><i class="fas fa-tachometer-alt"></i> Content Origin Score</h2>
                <div class="score-meter">
                    <div class="meter-title">Human vs AI Probability</div>
                    <div class="gauge-container">
                        <div class="gauge"></div>
                        <div class="gauge-needle" id="needle"></div>
                        <div class="gauge-center"></div>
                    </div>
                    <div class="score-value" id="score-value">72%</div>
                    <div class="score-label">Human-written content detected</div>
                </div>
                
                <div class="probability-bars">
                    <div class="probability-bar">
                        <div class="bar-label">Human</div>
                        <div class="bar-container">
                            <div class="bar-fill human" style="width: 72%">
                                <div class="bar-value">72%</div>
                            </div>
                        </div>
                    </div>
                    <div class="probability-bar">
                        <div class="bar-label">AI</div>
                        <div class="bar-container">
                            <div class="bar-fill ai" style="width: 28%">
                                <div class="bar-value">28%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h2 class="card-title"><i class="fas fa-chart-pie"></i> Detection Factors</h2>
                <div class="analysis-details">
                    <div class="detail-item">
                        <div class="detail-label">Perplexity Score</div>
                        <div class="detail-value">High (Authentic)</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Burstiness</div>
                        <div class="detail-value">Moderate</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Semantic Coherence</div>
                        <div class="detail-value">Strong</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Stylometric Analysis</div>
                        <div class="detail-value">Human-like</div>
                    </div>
                    <div class="detail-item">
                        <div class="detail-label">Syntax Patterns</div>
                        <div class="detail-value">Natural</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2 class="card-title"><i class="fas fa-star"></i> Advanced Detection Features</h2>
            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-icon"><i class="fas fa-brain"></i></div>
                    <div class="feature-title">Ensemble Modeling</div>
                    <div class="feature-desc">Combines multiple AI detection models for higher accuracy</div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon"><i class="fas fa-language"></i></div>
                    <div class="feature-title">Semantic Analysis</div>
                    <div class="feature-desc">Examines meaning and context beyond simple patterns</div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon"><i class="fas fa-project-diagram"></i></div>
                    <div class="feature-title">Network Analysis</div>
                    <div class="feature-desc">Detects AI-generated content patterns across the web</div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon"><i class="fas fa-history"></i></div>
                    <div class="feature-title">Temporal Analysis</div>
                    <div class="feature-desc">Identifies content that appears suddenly without history</div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h2 class="card-title"><i class="fas fa-lightbulb"></i> Analysis Summary</h2>
            <p>The analyzed content shows <span class="highlight">strong indications of human authorship</span> with a confidence score of 72%. While there are some patterns that might suggest AI assistance, the overall linguistic complexity, semantic coherence, and stylistic markers align with human-generated content.</p>
            <p class="mt-4">For optimal content quality, we recommend maintaining a good balance between human creativity and AI tools where appropriate.</p>
        </div>
        
        <div class="text-center mb-4">
            <button class="btn"><i class="fas fa-download"></i> Export Full Report</button>
        </div>
    </div>

    <script>
        // Simulate analysis with random results for demonstration
        document.addEventListener('DOMContentLoaded', function() {
            // Simulate analysis after a delay
            setTimeout(() => {
                // Randomize results for demonstration
                const humanScore = Math.floor(Math.random() * 40) + 60; // 60-100%
                const aiScore = 100 - humanScore;
                
                // Update score value
                document.getElementById('score-value').textContent = `${humanScore}%`;
                
                // Update probability bars
                document.querySelector('.bar-fill.human').style.width = `${humanScore}%`;
                document.querySelector('.bar-fill.human .bar-value').textContent = `${humanScore}%`;
                document.querySelector('.bar-fill.ai').style.width = `${aiScore}%`;
                document.querySelector('.bar-fill.ai .bar-value').textContent = `${aiScore}%`;
                
                // Update gauge needle position (0deg = 0%, 180deg = 100%)
                const needleRotation = (humanScore / 100) * 180;
                document.getElementById('needle').style.transform = `translateX(-50%) rotate(${needleRotation}deg)`;
                
                // Update summary text based on score
                const summaryElement = document.querySelector('.highlight');
                if (humanScore >= 70) {
                    summaryElement.textContent = 'strong indications of human authorship';
                } else if (humanScore >= 40) {
                    summaryElement.textContent = 'mixed signals with possible AI assistance';
                } else {
                    summaryElement.textContent = 'strong indications of AI generation';
                }
            }, 1000);
        });
    </script>
</body>
</html>
