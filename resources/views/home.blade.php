<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semantic SEO Master Checklist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --secondary: #10b981;
            --accent: #f59e0b;
            --warning: #ef4444;
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
            line-height: 1.6;
            min-height: 100vh;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        /* Header */
        header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-weight: 800;
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 1rem;
        }
        
        .logo i {
            margin-right: 0.5rem;
            font-size: 2rem;
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
            margin: 0 auto 2rem;
        }
        
        .progress-container {
            background: white;
            border-radius: var(--radius);
            padding: 1.5rem;
            box-shadow: var(--shadow);
            margin-bottom: 2rem;
        }
        
        .progress-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .progress-title {
            font-weight: 600;
            color: var(--dark);
        }
        
        .progress-percentage {
            font-weight: 700;
            color: var(--primary);
            font-size: 1.2rem;
        }
        
        .progress-bar {
            height: 10px;
            background: #e5e7eb;
            border-radius: 5px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: 5px;
            width: 0%;
            transition: width 1s ease;
        }
        
        /* Checklist Sections */
        .checklist-sections {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .section-card {
            background: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1.5rem;
            transition: var(--transition);
        }
        
        .section-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }
        
        .section-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .section-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }
        
        .section-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
        }
        
        .checklist {
            list-style: none;
        }
        
        .checklist-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 1rem;
            padding: 0.8rem;
            border-radius: var(--radius);
            background: #f9fafb;
            transition: var(--transition);
        }
        
        .checklist-item:hover {
            background: #f3f4f6;
        }
        
        .checklist-item input[type="checkbox"] {
            margin-right: 1rem;
            margin-top: 0.2rem;
            width: 20px;
            height: 20px;
            cursor: pointer;
        }
        
        .checklist-label {
            flex: 1;
        }
        
        .checklist-text {
            font-weight: 500;
            margin-bottom: 0.3rem;
            color: var(--dark);
        }
        
        .checklist-desc {
            font-size: 0.85rem;
            color: var(--text-light);
        }
        
        /* Actions */
        .actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
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
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary);
            color: var(--primary);
        }
        
        .btn-outline:hover {
            background: var(--primary);
            color: white;
        }
        
        .btn i {
            margin-right: 0.5rem;
        }
        
        /* Footer */
        footer {
            text-align: center;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 1px solid #e5e7eb;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1.5rem;
            }
            
            .page-title {
                font-size: 2rem;
            }
            
            .checklist-sections {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="logo">
                <i class="fas fa-search"></i>SemanticSEO
            </div>
            <h1 class="page-title">Semantic SEO Master Checklist</h1>
            <p class="page-subtitle">A comprehensive checklist to ensure your content is fully optimized for semantic search and achieves maximum visibility</p>
            
            <div class="progress-container">
                <div class="progress-header">
                    <div class="progress-title">Checklist Completion</div>
                    <div class="progress-percentage" id="progress-percentage">0%</div>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill"></div>
                </div>
            </div>
        </header>
        
        <div class="checklist-sections">
            <!-- Content & Keywords Section -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h2 class="section-title">Content & Keywords</h2>
                </div>
                
                <ul class="checklist">
                    <li class="checklist-item">
                        <input type="checkbox" id="item-1" class="checklist-checkbox">
                        <label for="item-1" class="checklist-label">
                            <div class="checklist-text">Main Topic</div>
                            <div class="checklist-desc">A single, well-defined core topic is established for the page</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-2" class="checklist-checkbox">
                        <label for="item-2" class="checklist-label">
                            <div class="checklist-text">Semantic Keywords</div>
                            <div class="checklist-desc">5-10 conceptually related keywords (not just synonyms) identified and used</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-3" class="checklist-checkbox">
                        <label for="item-3" class="checklist-label">
                            <div class="checklist-text">LSI Keywords & Synonyms</div>
                            <div class="checklist-desc">Keywords from "People Also Ask" and "Related Searches" naturally integrated</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-4" class="checklist-checkbox">
                        <label for="item-4" class="checklist-label">
                            <div class="checklist-text">Related Entities & Concepts</div>
                            <div class="checklist-desc">Key people, places, organizations, products, and ideas mentioned and explained</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-5" class="checklist-checkbox">
                        <label for="item-5" class="checklist-label">
                            <div class="checklist-text">Long-Tail Variations</div>
                            <div class="checklist-desc">Specific, longer keyword phrases (question-based and conversational) included</div>
                        </label>
                    </li>
                </ul>
            </div>
            
            <!-- Technical Elements Section -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h2 class="section-title">Technical Elements</h2>
                </div>
                
                <ul class="checklist">
                    <li class="checklist-item">
                        <input type="checkbox" id="item-6" class="checklist-checkbox">
                        <label for="item-6" class="checklist-label">
                            <div class="checklist-text">Schema Markup</div>
                            <div class="checklist-desc">Relevant structured data (Article, FAQPage, HowTo) implemented via JSON-LD</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-7" class="checklist-checkbox">
                        <label for="item-7" class="checklist-label">
                            <div class="checklist-text">Heading Structure (H1-H6)</div>
                            <div class="checklist-desc">One unique H1 tag, H2s for major sections, logical hierarchy with H3s-H6s</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-8" class="checklist-checkbox">
                        <label for="item-8" class="checklist-label">
                            <div class="checklist-text">Meta Title & Description</div>
                            <div class="checklist-desc">Title under 60 chars with primary keyword, meta description under 160 chars with CTA</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-9" class="checklist-checkbox">
                        <label for="item-9" class="checklist-label">
                            <div class="checklist-text">URL Optimization</div>
                            <div class="checklist-desc">URL is short, readable, includes primary keyword, uses hyphens to separate words</div>
                        </label>
                    </li>
                </ul>
            </div>
            
            <!-- Content Quality Section -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h2 class="section-title">Content Quality</h2>
                </div>
                
                <ul class="checklist">
                    <li class="checklist-item">
                        <input type="checkbox" id="item-10" class="checklist-checkbox">
                        <label for="item-10" class="checklist-label">
                            <div class="checklist-text">Search Intent Alignment</div>
                            <div class="checklist-desc">Content type matches user's goal (to learn, to buy, to find)</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-11" class="checklist-checkbox">
                        <label for="item-11" class="checklist-label">
                            <div class="checklist-text">Comprehensive Coverage</div>
                            <div class="checklist-desc">Content is a definitive resource covering all common questions and subtopics</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-12" class="checklist-checkbox">
                        <label for="item-12" class="checklist-label">
                            <div class="checklist-text">Natural Language Flow</div>
                            <div class="checklist-desc">Written conversationally for humans, avoiding awkward keyword stuffing</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-13" class="checklist-checkbox">
                        <label for="item-13" class="checklist-label">
                            <div class="checklist-text">Question-Answer Format</div>
                            <div class="checklist-desc">FAQ section or content that answers "who, what, when, where, why, how" questions</div>
                        </label>
                    </li>
                </ul>
            </div>
            
            <!-- Structure & Architecture Section -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <h2 class="section-title">Structure & Architecture</h2>
                </div>
                
                <ul class="checklist">
                    <li class="checklist-item">
                        <input type="checkbox" id="item-14" class="checklist-checkbox">
                        <label for="item-14" class="checklist-label">
                            <div class="checklist-text">Topic Clusters</div>
                            <div class="checklist-desc">Page is part of a hub-and-spoke model (pillar page with cluster content)</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-15" class="checklist-checkbox">
                        <label for="item-15" class="checklist-label">
                            <div class="checklist-text">Internal Linking</div>
                            <div class="checklist-desc">Contextual anchors with descriptive text, links to cornerstone content</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-16" class="checklist-checkbox">
                        <label for="item-16" class="checklist-label">
                            <div class="checklist-text">Content Hierarchy</div>
                            <div class="checklist-desc">Information organized from broad to specific, easy to scan and understand</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-17" class="checklist-checkbox">
                        <label for="item-17" class="checklist-label">
                            <div class="checklist-text">Related Content Connections</div>
                            <div class="checklist-desc">"Further Reading" or "You Might Also Like" sections suggest relevant content</div>
                        </label>
                    </li>
                </ul>
            </div>
            
            <!-- User Signals & Experience Section -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <h2 class="section-title">User Signals & Experience</h2>
                </div>
                
                <ul class="checklist">
                    <li class="checklist-item">
                        <input type="checkbox" id="item-18" class="checklist-checkbox">
                        <label for="item-18" class="checklist-label">
                            <div class="checklist-text">Readability Score</div>
                            <div class="checklist-desc">Content scores well on readability tools (aim for Grade 6-8)</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-19" class="checklist-checkbox">
                        <label for="item-19" class="checklist-label">
                            <div class="checklist-text">Mobile Optimization</div>
                            <div class="checklist-desc">Page is fully responsive and displays correctly on all mobile devices</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-20" class="checklist-checkbox">
                        <label for="item-20" class="checklist-label">
                            <div class="checklist-text">Page Load Speed</div>
                            <div class="checklist-desc">Page loads quickly (aim for under 3 seconds)</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-21" class="checklist-checkbox">
                        <label for="item-21" class="checklist-label">
                            <div class="checklist-text">Multimedia & Alt Text</div>
                            <div class="checklist-desc">Images, videos, infographics used with descriptive alt text</div>
                        </label>
                    </li>
                </ul>
            </div>
            
            <!-- Entities & Context Section -->
            <div class="section-card">
                <div class="section-header">
                    <div class="section-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h2 class="section-title">Entities & Context</h2>
                </div>
                
                <ul class="checklist">
                    <li class="checklist-item">
                        <input type="checkbox" id="item-22" class="checklist-checkbox">
                        <label for="item-22" class="checklist-label">
                            <div class="checklist-text">People, Places, Organizations</div>
                            <div class="checklist-desc">Relevant proper nouns identified and contextually defined</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-23" class="checklist-checkbox">
                        <label for="item-23" class="checklist-label">
                            <div class="checklist-text">Industry Terminology</div>
                            <div class="checklist-desc">Key jargon used appropriately and often defined for broader audience</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-24" class="checklist-checkbox">
                        <label for="item-24" class="checklist-label">
                            <div class="checklist-text">Co-occurring Terms</div>
                            <div class="checklist-desc">Terms that statistically appear together around this topic are naturally present</div>
                        </label>
                    </li>
                    
                    <li class="checklist-item">
                        <input type="checkbox" id="item-25" class="checklist-checkbox">
                        <label for="item-25" class="checklist-label">
                            <div class="checklist-text">Topical Authority Signals</div>
                            <div class="checklist-desc">Content demonstrates E-E-A-T by citing sources, using data, showing experience</div>
                        </label>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="actions">
            <button class="btn btn-primary" id="save-btn">
                <i class="fas fa-save"></i> Save Progress
            </button>
            <button class="btn btn-outline" id="reset-btn">
                <i class="fas fa-redo"></i> Reset Checklist
            </button>
            <button class="btn btn-outline" id="print-btn">
                <i class="fas fa-print"></i> Print Checklist
            </button>
        </div>
        
        <footer>
            <p>Semantic SEO Master Checklist | Designed for content creators and SEO professionals</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('.checklist-checkbox');
            const progressFill = document.getElementById('progress-fill');
            const progressPercentage = document.getElementById('progress-percentage');
            const saveBtn = document.getElementById('save-btn');
            const resetBtn = document.getElementById('reset-btn');
            const printBtn = document.getElementById('print-btn');
            
            // Total number of checklist items
            const totalItems = checkboxes.length;
            
            // Load saved progress from localStorage
            function loadProgress() {
                checkboxes.forEach(checkbox => {
                    const isChecked = localStorage.getItem(checkbox.id) === 'true';
                    checkbox.checked = isChecked;
                });
                updateProgress();
            }
            
            // Save progress to localStorage
            function saveProgress() {
                checkboxes.forEach(checkbox => {
                    localStorage.setItem(checkbox.id, checkbox.checked);
                });
                
                // Show save confirmation
                const originalText = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="fas fa-check"></i> Progress Saved!';
                
                setTimeout(() => {
                    saveBtn.innerHTML = originalText;
                }, 2000);
            }
            
            // Update progress bar and percentage
            function updateProgress() {
                const checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
                const percentage = Math.round((checkedCount / totalItems) * 100);
                
                progressFill.style.width = `${percentage}%`;
                progressPercentage.textContent = `${percentage}%`;
            }
            
            // Reset all checkboxes
            function resetChecklist() {
                if (confirm('Are you sure you want to reset the checklist? This will clear all your progress.')) {
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = false;
                        localStorage.removeItem(checkbox.id);
                    });
                    updateProgress();
                }
            }
            
            // Print the checklist
            function printChecklist() {
                window.print();
            }
            
            // Add event listeners
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    updateProgress();
                    saveProgress();
                });
            });
            
            saveBtn.addEventListener('click', saveProgress);
            resetBtn.addEventListener('click', resetChecklist);
            printBtn.addEventListener('click', printChecklist);
            
            // Initialize
            loadProgress();
        });
    </script>
</body>
</html>
