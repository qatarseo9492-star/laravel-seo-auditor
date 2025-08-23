<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semantic SEO Master Analyzer</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #08080f;
            --panel: rgba(15, 16, 34, 0.85);
            --panel-2: rgba(20, 20, 51, 0.9);
            --line: rgba(255, 255, 255, 0.08);
            --text: #f0effa;
            --text-dim: #b6b3d6;
            --text-muted: #9aa0c3;
            --primary: #9b5cff;
            --primary-glow: rgba(155, 92, 255, 0.4);
            --secondary: #ff2045;
            --secondary-glow: rgba(255, 32, 69, 0.4);
            --accent: #3de2ff;
            --accent-glow: rgba(61, 226, 255, 0.4);
            --good: #16c172;
            --warn: #f59e0b;
            --bad: #ef4444;
            --radius: 16px;
            --radius-lg: 24px;
            --shadow: 0 10px 40px rgba(0, 0, 0, 0.55);
            --shadow-glow: 0 0 20px rgba(155, 92, 255, 0.3);
            --container: 1200px;
            --transition: all 0.3s ease;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html, body {
            height: 100%;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            color: var(--text);
            font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            background: 
                radial-gradient(1200px 700px at 0% -10%, rgba(32, 16, 70, 0.8) 0%, transparent 55%),
                radial-gradient(1100px 800px at 110% 0%, rgba(26, 15, 42, 0.7) 0%, transparent 50%),
                var(--bg);
            overflow-x: hidden;
            line-height: 1.6;
        }

        /* Background effects */
        #brainCanvas, #linesCanvas, #linesCanvas2 {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
            opacity: 0.8;
        }

        #smokeFX {
            position: fixed;
            inset: 0;
            z-index: 1;
            pointer-events: none;
            filter: saturate(115%) contrast(105%);
        }

        /* Layout */
        .wrap {
            position: relative;
            z-index: 3;
            max-width: var(--container);
            margin: 0 auto;
            padding: 32px 5%;
        }

        /* Header */
        header.site {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            border-bottom: 1px solid var(--line);
            backdrop-filter: saturate(180%) blur(16px);
            background: rgba(15, 16, 34, 0.4);
            border-radius: var(--radius);
            margin-bottom: 24px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .brand-badge {
            width: 64px;
            height: 64px;
            border-radius: 16px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(155, 92, 255, 0.3), rgba(255, 32, 69, 0.25));
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffd1dc;
            box-shadow: var(--shadow-glow);
        }

        .hero-heading {
            font-size: 3.2rem;
            font-weight: 900;
            line-height: 1.1;
            margin: 0.1rem 0;
            letter-spacing: -0.5px;
            background: linear-gradient(90deg, #b892ff, #ff2045 55%, #ff8a5b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 0 28px rgba(155, 92, 255, 0.25);
        }

        /* Language dock */
        .lang-dock {
            position: fixed;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 70;
            display: flex;
            flex-direction: column;
            gap: 0.6rem;
        }

        .lang-btn {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            display: grid;
            place-items: center;
            cursor: pointer;
            backdrop-filter: blur(6px);
            transition: var(--transition);
        }

        .lang-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .lang-panel {
            position: fixed;
            left: 74px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 70;
            display: none;
        }

        .lang-card {
            background: var(--panel-2);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            box-shadow: var(--shadow);
            padding: 10px 12px;
            min-width: 240px;
            backdrop-filter: blur(10px);
        }

        .lang-item {
            padding: 0.45rem 0.55rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .lang-item:hover {
            background: rgba(255, 255, 255, 0.06);
        }

        .lang-flag {
            width: 18px;
            height: 14px;
            border-radius: 2px;
            background: #888;
        }

        /* Buttons */
        .btn {
            --pad: 0.75rem 1.25rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: var(--pad);
            border-radius: 12px;
            border: 1px solid transparent;
            cursor: pointer;
            font-weight: 600;
            letter-spacing: 0.2px;
            transition: var(--transition);
            font-size: 0.95rem;
        }

        .btn-neon {
            background: linear-gradient(135deg, #3de2ff, #9b5cff);
            box-shadow: 0 8px 30px rgba(61, 226, 255, 0.25);
            color: #001018;
        }

        .btn-neon:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(61, 226, 255, 0.35);
        }

        .btn-ghost {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.16);
            color: #fff;
        }

        .btn-ghost:hover {
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff2045, #ff7a59);
            color: #fff;
            box-shadow: 0 8px 30px rgba(255, 32, 69, 0.25);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(255, 32, 69, 0.35);
        }

        /* Analyzer panel */
        .analyzer {
            margin-top: 24px;
            background: var(--panel);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow);
            padding: 28px;
            backdrop-filter: blur(10px);
        }

        .section-title {
            font-size: 1.8rem;
            margin: 0 0 0.3rem;
            font-weight: 800;
            background: linear-gradient(90deg, #3de2ff, #9b5cff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .section-subtitle {
            margin: 0;
            color: var(--text-dim);
            font-size: 1rem;
        }

        /* Score area */
        .score-area {
            display: flex;
            gap: 1.8rem;
            align-items: center;
            margin: 1rem 0 0;
            flex-wrap: wrap;
        }

        .score-container {
            width: 240px;
            position: relative;
        }

        .score-wheel {
            width: 100%;
            height: auto;
            transform: rotate(-90deg);
            filter: drop-shadow(0 0 15px rgba(155, 92, 255, 0.3));
        }

        .score-wheel circle {
            fill: none;
            stroke-width: 14;
            stroke-linecap: round;
        }

        .score-wheel .bg {
            stroke: rgba(255, 255, 255, 0.12);
        }

        .score-wheel .progress {
            stroke: url(#gradBad);
            stroke-dasharray: 339;
            stroke-dashoffset: 339;
            transition: stroke-dashoffset 0.6s ease, stroke 0.25s ease, filter 0.25s ease;
        }

        .score-text {
            font-size: 3.1rem;
            font-weight: 1000;
            fill: #fff;
            transform: rotate(90deg);
            text-shadow: 0 0 18px rgba(255, 32, 69, 0.25);
        }

        .score-info {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            flex: 1;
            min-width: 300px;
        }

        .score-chips {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .chip {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 20px;
            padding: 0.5rem 0.9rem;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .score-actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        /* URL input */
        .analyze-box {
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius);
            padding: 20px;
        }

        .analyze-form input[type="url"] {
            width: 100%;
            padding: 1rem 1.2rem;
            border-radius: 12px;
            border: 1px solid rgba(27, 27, 53, 0.6);
            background: rgba(11, 13, 33, 0.7);
            color: var(--text);
            font-size: 1rem;
            transition: var(--transition);
            box-shadow: 0 0 0 0 rgba(155, 92, 255, 0);
        }

        .analyze-form input[type="url"]:focus {
            outline: none;
            border-color: #5942ff;
            box-shadow: 0 0 0 4px rgba(155, 92, 255, 0.15);
        }

        .analyze-row {
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 0.8rem;
            align-items: center;
            margin-top: 0.8rem;
        }

        /* Progress bar */
        .progress-wrap {
            margin-top: 1.5rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: var(--radius);
            padding: 16px;
        }

        .progress-bar {
            width: 100%;
            height: 12px;
            border-radius: 999px;
            background: rgba(11, 18, 32, 0.7);
            overflow: hidden;
            border: 1px solid rgba(16, 24, 38, 0.5);
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #9b5cff, #ff2045);
            width: 0%;
            transition: width 0.35s ease;
            border-radius: 999px;
        }

        .progress-caption {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-top: 0.5rem;
            display: flex;
            justify-content: space-between;
        }

        /* Category grid */
        .analyzer-grid {
            margin-top: 1.8rem;
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 1.5rem;
        }

        /* Category cards */
        .category-card {
            position: relative;
            grid-column: span 6;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.03));
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
            overflow: hidden;
            isolation: isolate;
            transition: var(--transition);
            backdrop-filter: blur(8px);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .category-card::before {
            content: "";
            position: absolute;
            inset: -2px;
            border-radius: var(--radius);
            padding: 2px;
            background: conic-gradient(from 180deg, rgba(61, 226, 255, 0.35), rgba(155, 92, 255, 0.35), rgba(255, 182, 72, 0.3), rgba(255, 32, 69, 0.3), rgba(34, 197, 94, 0.3), rgba(61, 226, 255, 0.35));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            animation: borderGlow 7s linear infinite;
            pointer-events: none;
        }

        @keyframes borderGlow {
            0% { filter: hue-rotate(0); }
            100% { filter: hue-rotate(360deg); }
        }

        .category-head {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 0.75rem;
            align-items: center;
            margin-bottom: 1rem;
        }

        .category-icon {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, rgba(61, 226, 255, 0.2), rgba(155, 92, 255, 0.2));
            color: #fff;
            font-size: 1.1rem;
            border: 1px solid rgba(255, 255, 255, 0.18);
        }

        .category-title {
            margin: 0;
            font-size: 1.15rem;
            background: linear-gradient(90deg, #3de2ff, #9b5cff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .category-sub {
            margin: 0.15rem 0 0;
            color: var(--text-dim);
            font-size: 0.95rem;
        }

        .checklist {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        /* Checklist items */
        .checklist-item {
            --accent: rgba(255, 255, 255, 0.12);
            position: relative;
            display: grid;
            grid-template-columns: 1fr auto auto auto;
            gap: 0.8rem;
            align-items: center;
            padding: 0.9rem 1rem 0.9rem 1.2rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.035), rgba(255, 255, 255, 0.03));
            overflow: hidden;
            transition: var(--transition);
            margin-bottom: 0.5rem;
        }

        .checklist-item::after {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: var(--accent);
            box-shadow: 0 0 20px var(--accent);
            transition: 0.25s;
        }

        .checklist-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 34px rgba(0, 0, 0, 0.28);
            border-color: rgba(255, 255, 255, 0.16);
        }

        .checklist-item label {
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 0.95rem;
        }

        .checklist-item .autoPulse {
            animation: selPulse 0.8s ease;
        }

        @keyframes selPulse {
            0% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
            70% { box-shadow: 0 0 0 12px rgba(34, 197, 94, 0.18); }
            100% { box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
        }

        /* Toggle switch */
        .checklist-item input[type="checkbox"] {
            appearance: none;
            width: 42px;
            height: 24px;
            border-radius: 999px;
            background: #2a2a46;
            border: 1px solid rgba(255, 255, 255, 0.18);
            position: relative;
            cursor: pointer;
            outline: none;
            transition: 0.2s;
        }

        .checklist-item input[type="checkbox"]::after {
            content: "";
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #cfd3f6;
            top: 2.5px;
            left: 2.5px;
            transition: 0.2s;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .checklist-item input[type="checkbox"]:checked {
            background: linear-gradient(135deg, #3de2ff, #9b5cff);
        }

        .checklist-item input[type="checkbox"]:checked::after {
            left: 21px;
            background: #0a1222;
        }

        /* Score badge */
        .score-badge {
            font-weight: 700;
            font-size: 0.9rem;
            padding: 0.3rem 0.65rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.06);
            min-width: 52px;
            text-align: center;
            transition: var(--transition);
        }

        .score-good {
            background: rgba(22, 193, 114, 0.22);
            border-color: rgba(22, 193, 114, 0.45);
        }

        .score-mid {
            background: rgba(245, 158, 11, 0.22);
            border-color: rgba(245, 158, 11, 0.45);
        }

        .score-bad {
            background: rgba(239, 68, 68, 0.24);
            border-color: rgba(239, 68, 68, 0.5);
        }

        .improve-btn {
            padding: 0.4rem 0.75rem;
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.06);
            font-weight: 600;
            cursor: pointer;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .improve-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        /* Report chips */
        .report-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .report-chip {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 0.5rem 0.9rem;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .report-chip b {
            color: #fff;
            font-weight: 600;
        }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.65);
            backdrop-filter: blur(4px);
            display: none;
            z-index: 9000;
        }

        .modal {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9010;
        }

        .modal-card {
            width: min(1000px, 96vw);
            background: var(--panel-2);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 24px;
            backdrop-filter: blur(10px);
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .modal-title {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 700;
            background: linear-gradient(90deg, #3de2ff, #9b5cff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }

        .modal-close {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: #fff;
            padding: 0.35rem 0.6rem;
            cursor: pointer;
            transition: var(--transition);
        }

        .modal-close:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .tabs {
            display: flex;
            gap: 0.4rem;
            margin: 0.8rem 0;
            flex-wrap: wrap;
        }

        .tab {
            padding: 0.5rem 0.9rem;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.06);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .tab.active {
            background: linear-gradient(135deg, rgba(61, 226, 255, 0.22), rgba(155, 92, 255, 0.22));
            border-color: rgba(61, 226, 255, 0.4);
        }

        .tabpanes {
            flex: 1;
            overflow: auto;
        }

        .tabpanes > div {
            display: none;
            height: 100%;
        }

        .tabpanes > div.active {
            display: block;
        }

        .pre {
            white-space: pre-wrap;
            background: rgba(11, 13, 33, 0.7);
            border: 1px solid rgba(27, 27, 53, 0.5);
            border-radius: 12px;
            padding: 16px;
            color: #cfd3f6;
            max-height: 60vh;
            overflow: auto;
            font-family: 'SF Mono', Monaco, Consolas, 'Roboto Mono', monospace;
            font-size: 0.9rem;
            line-height: 1.5;
        }

        #modalList {
            list-style: none;
            padding: 0;
        }

        #modalList li {
            padding: 0.8rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        #modalList li:last-child {
            border-bottom: none;
        }

        /* Footer */
        footer.site {
            margin-top: 40px;
            padding: 20px 5%;
            background: rgba(255, 255, 255, 0.04);
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            backdrop-filter: blur(6px);
            border-radius: var(--radius) var(--radius) 0 0;
        }

        .footer-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .footer-brand .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3de2ff, #9b5cff);
        }

        .footer-links a {
            color: var(--text-dim);
            margin-left: 0.9rem;
            text-decoration: none;
            transition: var(--transition);
        }

        .footer-links a:hover {
            color: #fff;
            text-decoration: underline;
        }

        /* Back to top */
        #backTop {
            position: fixed;
            right: 24px;
            bottom: 24px;
            z-index: 90;
            width: 50px;
            height: 50px;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(255, 255, 255, 0.07);
            display: grid;
            place-items: center;
            color: #fff;
            cursor: pointer;
            display: none;
            transition: var(--transition);
            backdrop-filter: blur(6px);
        }

        #backTop:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-3px);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .analyzer-grid {
                gap: 1.2rem;
            }
        }

        @media (max-width: 992px) {
            .category-card {
                grid-column: span 12;
            }
            
            .hero-heading {
                font-size: 2.5rem;
            }
            
            .score-container {
                width: 200px;
            }
            
            .analyze-row {
                grid-template-columns: 1fr;
                gap: 0.8rem;
            }
            
            footer.site {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .score-info {
                min-width: auto;
            }
            
            .checklist-item {
                grid-template-columns: 1fr auto;
                grid-template-areas: 
                    "content content"
                    "score actions";
                gap: 0.6rem;
            }
            
            .checklist-item label {
                grid-area: content;
            }
            
            .score-badge {
                grid-area: score;
                justify-self: start;
            }
            
            .improve-btn {
                grid-area: actions;
                justify-self: end;
            }
        }

        @media (max-width: 768px) {
            .wrap {
                padding: 20px 4%;
            }
            
            .analyzer {
                padding: 20px;
            }
            
            .score-area {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.2rem;
            }
            
            .section-title {
                font-size: 1.6rem;
            }
            
            .modal-card {
                padding: 16px;
            }
            
            .tabs {
                overflow-x: auto;
                padding-bottom: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .hero-heading {
                font-size: 2rem;
            }
            
            .brand-badge {
                width: 50px;
                height: 50px;
            }
            
            .score-container {
                width: 180px;
            }
            
            .checklist-item {
                padding: 0.8rem;
            }
        }

        @media print {
            #linesCanvas, #linesCanvas2, #brainCanvas, #smokeFX, .modal-backdrop, .modal, header.site, #backTop, .lang-dock, .lang-panel {
                display: none !important;
            }
            
            .analyzer {
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            .category-card::before {
                display: none;
            }
        }
    </style>
</head>
<body>
    <canvas id="brainCanvas"></canvas>
    <canvas id="linesCanvas"></canvas>
    <canvas id="linesCanvas2"></canvas>
    <canvas id="smokeFX" aria-hidden="true"></canvas>

    <!-- gradients for score wheel -->
    <svg width="0" height="0" aria-hidden="true">
        <defs>
            <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/></linearGradient>
            <linearGradient id="gradMid"  x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/></linearGradient>
            <linearGradient id="gradBad"  x1="0%" y1="0%" x2="100%"><stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/></linearGradient>
        </defs>
    </svg>

    <!-- Language Dock -->
    <div class="lang-dock">
        <button class="lang-btn" id="langOpen" title="Language"><i class="fa-solid fa-globe"></i></button>
    </div>
    <div class="lang-panel" id="langPanel"><div class="lang-card" id="langCard"></div></div>

    <div class="wrap">
        <header class="site">
            <div class="brand">
                <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
                <div><div class="hero-heading" data-i="title">Semantic SEO Master Analyzer</div></div>
            </div>
            <div style="display:flex;gap:.5rem">
                <button class="btn btn-ghost" id="printTop"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
            </div>
        </header>

        <section class="analyzer" id="analyzer">
            <h2 class="section-title" data-i="analyze_title">Analyze a URL</h2>
            <p class="section-subtitle" data-i="legend_line">
                The wheel fills with your overall score.
                <span class="chip" style="background:rgba(34,197,94,.18)">Green ≥ 80</span>
                <span class="chip" style="background:rgba(245,158,11,.18)">Orange 60–79</span>
                <span class="chip" style="background:rgba(239,68,68,.18)">Red &lt; 60</span>
            </p>

            <div class="score-area">
                <div class="score-container">
                    <svg class="score-wheel" viewBox="0 0 120 120" aria-label="Overall score">
                        <circle class="bg" cx="60" cy="60" r="54"/>
                        <circle class="progress" cx="60" cy="60" r="54"/>
                        <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" class="score-text" id="overallScore">0%</text>
                    </svg>
                </div>
                <div class="score-info">
                    <div class="score-chips">
                        <span class="chip">Overall: <b id="overallScoreInline">0</b>/100</span>
                        <span class="chip" id="aiBadge">Writer: <b>—</b></span>
                    </div>
                    <div class="score-actions">
                        <button id="viewAIText" class="btn btn-neon"><i class="fa-solid fa-robot"></i> Evidence</button>
                        <button id="viewHumanBtn" class="btn btn-ghost"><i class="fa-solid fa-user"></i> Human‑like: <b id="humanPct">—</b>%</button>
                        <button id="viewRawBtn" class="btn btn-ghost"><i class="fa-solid fa-code"></i> Raw Data</button>
                    </div>
                </div>
            </div>

            <div class="analyze-box">
                <div class="analyze-form">
                    <input type="url" id="urlInput" placeholder="https://example.com" value="" data-i="placeholder"/>
                    <div class="analyze-row">
                        <button id="analyzeBtn" class="btn btn-neon"><i class="fa-solid fa-magnifying-glass"></i> <span data-i="analyze">Analyze</span></button>
                        <button id="resetBtn" class="btn btn-ghost"><i class="fa-solid fa-rotate-left"></i> <span data-i="reset">Reset</span></button>
                        <button id="printBtn" class="btn btn-ghost"><i class="fa-solid fa-print"></i> <span data-i="print">Print</span></button>
                    </div>
                </div>
            </div>

            <div class="progress-wrap" id="progressWrap" style="display:none">
                <div class="progress-bar"><div class="progress-fill" id="progressFill"></div></div>
                <div class="progress-caption"><span data-i="analyzing">Analyzing</span> <span id="progressText">0%</span></div>
            </div>

            <div class="analyzer-grid" id="analyzerGrid">
                <!-- Categories will be generated here -->
            </div>
        </section>

        <footer class="site">
            <div class="footer-brand">
                <div class="dot"></div>
                <span data-i="footer">Semantic SEO Master Analyzer by Ultra Tech Global</span>
            </div>
            <div class="footer-links">
                <a href="#" data-i="privacy">Privacy</a>
                <a href="#" data-i="terms">Terms</a>
            </div>
        </footer>
    </div>

    <!-- Back to top -->
    <div id="backTop"><i class="fa-solid fa-chevron-up"></i></div>

    <!-- Modal -->
    <div class="modal-backdrop" id="modalBackdrop"></div>
    <div class="modal" id="modal">
        <div class="modal-card">
            <div class="modal-header">
                <h3 class="modal-title" id="modalTitle">Modal Title</h3>
                <button class="modal-close" id="modalClose"><i class="fa-solid fa-xmark"></i></button>
            </div>
            <div class="tabs" id="modalTabs"></div>
            <div class="tabpanes" id="modalPanes"></div>
        </div>
    </div>

    <script>
        // Language data
        const langData = {
            en: {
                title: "Semantic SEO Master Analyzer",
                analyze_title: "Analyze a URL",
                legend_line: "The wheel fills with your overall score.",
                placeholder: "https://example.com",
                analyze: "Analyze",
                reset: "Reset",
                print: "Print",
                analyzing: "Analyzing",
                footer: "Semantic SEO Master Analyzer by Ultra Tech Global",
                privacy: "Privacy",
                terms: "Terms",
            },
        };

        // DOM elements
        const elements = {
            urlInput: document.getElementById('urlInput'),
            analyzeBtn: document.getElementById('analyzeBtn'),
            resetBtn: document.getElementById('resetBtn'),
            printBtn: document.getElementById('printBtn'),
            printTop: document.getElementById('printTop'),
            progressWrap: document.getElementById('progressWrap'),
            progressFill: document.getElementById('progressFill'),
            progressText: document.getElementById('progressText'),
            analyzerGrid: document.getElementById('analyzerGrid'),
            overallScore: document.getElementById('overallScore'),
            overallScoreInline: document.getElementById('overallScoreInline'),
            aiBadge: document.getElementById('aiBadge'),
            humanPct: document.getElementById('humanPct'),
            viewAIText: document.getElementById('viewAIText'),
            viewHumanBtn: document.getElementById('viewHumanBtn'),
            viewRawBtn: document.getElementById('viewRawBtn'),
            backTop: document.getElementById('backTop'),
            modalBackdrop: document.getElementById('modalBackdrop'),
            modal: document.getElementById('modal'),
            modalTitle: document.getElementById('modalTitle'),
            modalClose: document.getElementById('modalClose'),
            modalTabs: document.getElementById('modalTabs'),
            modalPanes: document.getElementById('modalPanes'),
            langOpen: document.getElementById('langOpen'),
            langPanel: document.getElementById('langPanel'),
            langCard: document.getElementById('langCard')
        };

        // State
        let currentScore = 0;
        let currentLang = 'en';
        let analysisData = null;

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            initLanguage();
            initEventListeners();
            initCanvas();
            initCategories();
        });

        // Language functions
        function initLanguage() {
            // Populate language panel
            const languages = [
                { code: 'en', name: 'English', flag: '🇺🇸' },
                { code: 'es', name: 'Español', flag: '🇪🇸' },
                { code: 'fr', name: 'Français', flag: '🇫🇷' },
                { code: 'de', name: 'Deutsch', flag: '🇩🇪' },
                { code: 'it', name: 'Italiano', flag: '🇮🇹' },
                { code: 'pt', name: 'Português', flag: '🇵🇹' },
                { code: 'ru', name: 'Русский', flag: '🇷🇺' },
                { code: 'ja', name: '日本語', flag: '🇯🇵' },
                { code: 'zh', name: '中文', flag: '🇨🇳' },
                { code: 'ar', name: 'العربية', flag: '🇸🇦' }
            ];
            
            languages.forEach(lang => {
                const item = document.createElement('div');
                item.className = 'lang-item';
                item.innerHTML = `<span class="lang-flag">${lang.flag}</span> ${lang.name}`;
                item.addEventListener('click', () => setLanguage(lang.code));
                elements.langCard.appendChild(item);
            });
            
            // Toggle language panel
            elements.langOpen.addEventListener('click', (e) => {
                e.stopPropagation();
                elements.langPanel.style.display = elements.langPanel.style.display === 'block' ? 'none' : 'block';
            });
            
            document.addEventListener('click', (e) => {
                if (!elements.langPanel.contains(e.target) && e.target !== elements.langOpen) {
                    elements.langPanel.style.display = 'none';
                }
            });
        }

        function setLanguage(lang) {
            currentLang = lang;
            elements.langPanel.style.display = 'none';
            
            // Update all elements with data-i attribute
            document.querySelectorAll('[data-i]').forEach(el => {
                const key = el.getAttribute('data-i');
                if (langData[lang] && langData[lang][key]) {
                    el.textContent = langData[lang][key];
                }
            });
        }

        // Event listeners
        function initEventListeners() {
            elements.analyzeBtn.addEventListener('click', analyzeURL);
            elements.resetBtn.addEventListener('click', resetForm);
            elements.printBtn.addEventListener('click', () => window.print());
            elements.printTop.addEventListener('click', () => window.print());
            elements.modalClose.addEventListener('click', closeModal);
            elements.modalBackdrop.addEventListener('click', closeModal);
            elements.viewAIText.addEventListener('click', showAIText);
            elements.viewHumanBtn.addEventListener('click', showHumanAnalysis);
            elements.viewRawBtn.addEventListener('click', showRawData);
            
            // Back to top
            window.addEventListener('scroll', () => {
                elements.backTop.style.display = window.scrollY > 300 ? 'grid' : 'none';
            });
            
            elements.backTop.addEventListener('click', () => {
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
            
            // Enter key to analyze
            elements.urlInput.addEventListener('keypress', (e) => {
                if (e.key === 'Enter') analyzeURL();
            });
        }

        // Analysis functions
        function analyzeURL() {
            const url = elements.urlInput.value.trim();
            if (!url) return;
            
            // Show progress
            elements.progressWrap.style.display = 'block';
            updateProgress(10);
            
            // Simulate analysis (replace with actual API calls)
            setTimeout(() => updateProgress(30), 500);
            setTimeout(() => updateProgress(60), 1000);
            setTimeout(() => updateProgress(90), 1500);
            setTimeout(() => {
                updateProgress(100);
                
                // Simulate results
                const score = Math.floor(Math.random() * 100);
                updateScore(score);
                
                // Show results
                updateCategoryScores();
                
                // Hide progress after a delay
                setTimeout(() => {
                    elements.progressWrap.style.display = 'none';
                }, 500);
            }, 2000);
        }

        function updateProgress(percent) {
            elements.progressFill.style.width = `${percent}%`;
            elements.progressText.textContent = `${percent}%`;
        }

        function updateScore(score) {
            currentScore = score;
            
            // Update score display
            elements.overallScore.textContent = `${score}%`;
            elements.overallScoreInline.textContent = score;
            
            // Update score wheel
            const progressCircle = document.querySelector('.score-wheel .progress');
            const circumference = 2 * Math.PI * 54;
            const offset = circumference - (score / 100) * circumference;
            progressCircle.style.strokeDashoffset = offset;
            
            // Set color based on score
            if (score >= 80) {
                progressCircle.style.stroke = 'url(#gradGood)';
                progressCircle.style.filter = 'drop-shadow(0 0 8px rgba(34, 197, 94, 0.5))';
            } else if (score >= 60) {
                progressCircle.style.stroke = 'url(#gradMid)';
                progressCircle.style.filter = 'drop-shadow(0 0 8px rgba(245, 158, 11, 0.5))';
            } else {
                progressCircle.style.stroke = 'url(#gradBad)';
                progressCircle.style.filter = 'drop-shadow(0 0 8px rgba(239, 68, 68, 0.5))';
            }
            
            // Update AI badge
            const aiScore = Math.floor(Math.random() * 100);
            const humanScore = 100 - aiScore;
            elements.aiBadge.innerHTML = `Writer: <b>${aiScore >= 50 ? 'AI' : 'Human'}</b>`;
            elements.humanPct.textContent = humanScore;
        }

        function resetForm() {
            elements.urlInput.value = '';
            updateScore(0);
            resetCategories();
            elements.progressWrap.style.display = 'none';
        }

        // Category functions
        function initCategories() {
            const categories = [
                {
                    id: 'content',
                    title: 'Content Quality',
                    subtitle: 'Relevance, depth, and structure',
                    icon: 'fa-file-lines',
                    items: [
                        { id: 'content_relevance', label: 'Content Relevance', score: 0 },
                        { id: 'content_depth', label: 'Content Depth', score: 0 },
                        { id: 'content_freshness', label: 'Content Freshness', score: 0 },
                        { id: 'content_structure', label: 'Content Structure', score: 0 }
                    ]
                },
                {
                    id: 'semantic',
                    title: 'Semantic SEO',
                    subtitle: 'Topic coverage and relevance',
                    icon: 'fa-diagram-project',
                    items: [
                        { id: 'topic_coverage', label: 'Topic Coverage', score: 0 },
                        { id: 'latent_topics', label: 'Latent Topics', score: 0 },
                        { id: 'semantic_relevance', label: 'Semantic Relevance', score: 0 },
                        { id: 'entity_recognition', label: 'Entity Recognition', score: 0 }
                    ]
                },
                {
                    id: 'technical',
                    title: 'Technical SEO',
                    subtitle: 'Site structure and performance',
                    icon: 'fa-gears',
                    items: [
                        { id: 'site_speed', label: 'Site Speed', score: 0 },
                        { id: 'mobile_friendly', label: 'Mobile Friendly', score: 0 },
                        { id: 'crawlability', label: 'Crawlability', score: 0 },
                        { id: 'indexability', label: 'Indexability', score: 0 }
                    ]
                },
                {
                    id: 'user',
                    title: 'User Experience',
                    subtitle: 'Engagement and satisfaction',
                    icon: 'fa-user-group',
                    items: [
                        { id: 'engagement', label: 'Engagement', score: 0 },
                        { id: 'satisfaction', label: 'Satisfaction', score: 0 },
                        { id: 'accessibility', label: 'Accessibility', score: 0 },
                        { id: 'usability', label: 'Usability', score: 0 }
                    ]
                }
            ];
            
            // Generate category cards
            categories.forEach(category => {
                const card = document.createElement('div');
                card.className = 'category-card';
                card.id = `category-${category.id}`;
                
                card.innerHTML = `
                    <div class="category-head">
                        <div class="category-icon"><i class="fa-solid ${category.icon}"></i></div>
                        <div>
                            <h3 class="category-title">${category.title}</h3>
                            <p class="category-sub">${category.subtitle}</p>
                        </div>
                        <div class="score-badge" id="${category.id}-score">0%</div>
                    </div>
                    <ul class="checklist" id="${category.id}-items"></ul>
                `;
                
                elements.analyzerGrid.appendChild(card);
                
                // Generate checklist items
                const list = document.getElementById(`${category.id}-items`);
                category.items.forEach(item => {
                    const li = document.createElement('li');
                    li.className = 'checklist-item';
                    li.innerHTML = `
                        <label>
                            <input type="checkbox" id="${item.id}" disabled>
                            <span>${item.label}</span>
                        </label>
                        <div class="score-badge score-bad" id="${item.id}-score">0%</div>
                        <button class="improve-btn" data-item="${item.id}"><i class="fa-solid fa-wand-magic-sparkles"></i> Improve</button>
                    `;
                    list.appendChild(li);
                    
                    // Add event listener for improve button
                    li.querySelector('.improve-btn').addEventListener('click', () => {
                        showImprovementTips(item.id);
                    });
                });
            });
        }

        function updateCategoryScores() {
            // Update each category with random scores for demo
            const categories = ['content', 'semantic', 'technical', 'user'];
            
            categories.forEach(category => {
                const categoryScore = Math.floor(Math.random() * 100);
                document.getElementById(`${category}-score`).textContent = `${categoryScore}%`;
                
                // Update score badge color
                const badge = document.getElementById(`${category}-score`);
                badge.className = 'score-badge ' + getScoreClass(categoryScore);
                
                // Update individual items
                const items = document.querySelectorAll(`#${category}-items .checklist-item`);
                items.forEach(item => {
                    const itemScore = Math.floor(Math.random() * 100);
                    const scoreBadge = item.querySelector('.score-badge');
                    scoreBadge.textContent = `${itemScore}%`;
                    scoreBadge.className = 'score-badge ' + getScoreClass(itemScore);
                    
                    // Update checkbox based on score
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = itemScore >= 60;
                    
                    if (itemScore >= 60) {
                        checkbox.classList.add('autoPulse');
                        setTimeout(() => checkbox.classList.remove('autoPulse'), 1000);
                    }
                });
            });
        }

        function resetCategories() {
            // Reset all category scores to 0
            const categories = ['content', 'semantic', 'technical', 'user'];
            
            categories.forEach(category => {
                document.getElementById(`${category}-score`).textContent = '0%';
                document.getElementById(`${category}-score`).className = 'score-badge';
                
                // Reset individual items
                const items = document.querySelectorAll(`#${category}-items .checklist-item`);
                items.forEach(item => {
                    const scoreBadge = item.querySelector('.score-badge');
                    scoreBadge.textContent = '0%';
                    scoreBadge.className = 'score-badge';
                    
                    const checkbox = item.querySelector('input[type="checkbox"]');
                    checkbox.checked = false;
                });
            });
        }

        function getScoreClass(score) {
            if (score >= 80) return 'score-good';
            if (score >= 60) return 'score-mid';
            return 'score-bad';
        }

        // Modal functions
        function showImprovementTips(itemId) {
            const tips = {
                content_relevance: "Improve content relevance by focusing on user intent and ensuring your content directly addresses search queries.",
                content_depth: "Add more comprehensive information, examples, case studies, and data to make your content more authoritative.",
                content_freshness: "Update your content regularly with new information, statistics, and examples to keep it current.",
                content_structure: "Use clear headings, subheadings, bullet points, and short paragraphs to improve readability.",
                topic_coverage: "Cover related subtopics and answer common questions to establish topical authority.",
                latent_topics: "Include semantically related terms and concepts that search engines associate with your main topic.",
                semantic_relevance: "Ensure all content elements (text, images, videos) are contextually relevant to the main topic.",
                entity_recognition: "Mention relevant people, places, organizations, and concepts to help search engines understand context.",
                site_speed: "Optimize images, minimize code, leverage browser caching, and use a CDN to improve loading times.",
                mobile_friendly: "Ensure your site uses responsive design and is fully functional on all mobile devices.",
                crawlability: "Fix broken links, create a logical site structure, and use a robots.txt file to guide search engines.",
                indexability: "Use proper meta tags, avoid duplicate content, and ensure important pages aren't blocked from indexing.",
                engagement: "Create interactive elements, clear calls-to-action, and compelling content to keep users engaged.",
                satisfaction: "Focus on solving user problems completely and efficiently to increase satisfaction.",
                accessibility: "Follow WCAG guidelines, ensure proper color contrast, and provide text alternatives for non-text content.",
                usability: "Simplify navigation, reduce cognitive load, and make important information easy to find."
            };
            
            openModal('Improvement Tips', `<p>${tips[itemId] || 'No specific tips available for this item.'}</p>`);
        }

        function showAIText() {
            openModal('AI Detection Evidence', '<p>Detailed analysis of AI detection factors would appear here.</p>');
        }

        function showHumanAnalysis() {
            openModal('Human-like Content Analysis', '<p>Detailed analysis of human-like content factors would appear here.</p>');
        }

        function showRawData() {
            openModal('Raw Analysis Data', '<pre>Raw JSON data from the analysis would appear here.</pre>');
        }

        function openModal(title, content, tabs = null) {
            elements.modalTitle.textContent = title;
            
            if (tabs) {
                elements.modalTabs.innerHTML = '';
                elements.modalPanes.innerHTML = '';
                
                tabs.forEach((tab, index) => {
                    const tabEl = document.createElement('button');
                    tabEl.className = `tab ${index === 0 ? 'active' : ''}`;
                    tabEl.textContent = tab.label;
                    tabEl.addEventListener('click', () => switchTab(index));
                    elements.modalTabs.appendChild(tabEl);
                    
                    const pane = document.createElement('div');
                    pane.className = `tabpane ${index === 0 ? 'active' : ''}`;
                    pane.innerHTML = tab.content;
                    elements.modalPanes.appendChild(pane);
                });
            } else {
                elements.modalTabs.innerHTML = '';
                elements.modalPanes.innerHTML = '<div class="tabpane active">' + content + '</div>';
            }
            
            elements.modalBackdrop.style.display = 'block';
            elements.modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal() {
            elements.modalBackdrop.style.display = 'none';
            elements.modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function switchTab(index) {
            // Deactivate all tabs and panes
            document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.tabpane').forEach(pane => pane.classList.remove('active'));
            
            // Activate selected tab and pane
            document.querySelectorAll('.tab')[index].classList.add('active');
            document.querySelectorAll('.tabpane')[index].classList.add('active');
        }

        // Canvas animations
        function initCanvas() {
            const brainCanvas = document.getElementById('brainCanvas');
            const linesCanvas = document.getElementById('linesCanvas');
            const linesCanvas2 = document.getElementById('linesCanvas2');
            const smokeCanvas = document.getElementById('smokeFX');
            
            // Set canvas sizes
            function resizeCanvases() {
                [brainCanvas, linesCanvas, linesCanvas2, smokeCanvas].forEach(canvas => {
                    canvas.width = window.innerWidth;
                    canvas.height = window.innerHeight;
                });
            }
            
            resizeCanvases();
            window.addEventListener('resize', resizeCanvases);
            
            // Simple brain network animation
            const brainCtx = brainCanvas.getContext('2d');
            const nodes = [];
            
            // Create nodes
            for (let i = 0; i < 30; i++) {
                nodes.push({
                    x: Math.random() * brainCanvas.width,
                    y: Math.random() * brainCanvas.height,
                    radius: Math.random() * 2 + 1,
                    vx: (Math.random() - 0.5) * 0.4,
                    vy: (Math.random() - 0.5) * 0.4
                });
            }
            
            function drawBrainNetwork() {
                brainCtx.clearRect(0, 0, brainCanvas.width, brainCanvas.height);
                
                // Draw connections
                brainCtx.strokeStyle = 'rgba(155, 92, 255, 0.15)';
                brainCtx.lineWidth = 0.5;
                
                for (let i = 0; i < nodes.length; i++) {
                    for (let j = i + 1; j < nodes.length; j++) {
                        const dx = nodes[i].x - nodes[j].x;
                        const dy = nodes[i].y - nodes[j].y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        
                        if (dist < 150) {
                            brainCtx.globalAlpha = 0.15 * (1 - dist / 150);
                            brainCtx.beginPath();
                            brainCctx.moveTo(nodes[i].x, nodes[i].y);
                            brainCtx.lineTo(nodes[j].x, nodes[j].y);
                            brainCtx.stroke();
                        }
                    }
                }
                
                // Draw nodes
                brainCtx.globalAlpha = 0.5;
                nodes.forEach(node => {
                    brainCtx.beginPath();
                    brainCtx.arc(node.x, node.y, node.radius, 0, Math.PI * 2);
                    brainCtx.fillStyle = '#9b5cff';
                    brainCtx.fill();
                    
                    // Move nodes
                    node.x += node.vx;
                    node.y += node.vy;
                    
                    // Bounce off edges
                    if (node.x < 0 || node.x > brainCanvas.width) node.vx *= -1;
                    if (node.y < 0 || node.y > brainCanvas.height) node.vy *= -1;
                });
                
                requestAnimationFrame(drawBrainNetwork);
            }
            
            drawBrainNetwork();
            
            // Line animations
            const lineCtx = linesCanvas.getContext('2d');
            const lineCtx2 = linesCanvas2.getContext('2d');
            
            function drawLines(ctx, color, speed) {
                ctx.clearRect(0, 0, ctx.canvas.width, ctx.canvas.height);
                
                const time = Date.now() * 0.001;
                const centerX = ctx.canvas.width / 2;
                const centerY = ctx.canvas.height / 2;
                
                ctx.strokeStyle = color;
                ctx.lineWidth = 1;
                ctx.beginPath();
                
                for (let i = 0; i < 50; i++) {
                    const angle = time * 0.2 + i * 0.3;
                    const x = centerX + Math.cos(angle) * (100 + i * 5);
                    const y = centerY + Math.sin(angle) * (80 + i * 4);
                    
                    if (i === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        ctx.lineTo(x, y);
                    }
                }
                
                ctx.stroke();
                requestAnimationFrame(() => drawLines(ctx, color, speed));
            }
            
            drawLines(lineCtx, 'rgba(61, 226, 255, 0.1)', 0.3);
            drawLines(lineCtx2, 'rgba(255, 32, 69, 0.08)', 0.5);
            
            // Smoke effect
            const smokeCtx = smokeCanvas.getContext('2d');
            const smokeParticles = [];
            
            for (let i = 0; i < 20; i++) {
                smokeParticles.push({
                    x: Math.random() * smokeCanvas.width,
                    y: smokeCanvas.height + Math.random() * 100,
                    size: Math.random() * 50 + 20,
                    speed: Math.random() * 0.5 + 0.2,
                    opacity: Math.random() * 0.05 + 0.02
                });
            }
            
            function drawSmoke() {
                smokeCtx.clearRect(0, 0, smokeCanvas.width, smokeCanvas.height);
                
                smokeParticles.forEach(particle => {
                    particle.y -= particle.speed;
                    particle.x += (Math.random() - 0.5) * 0.8;
                    particle.opacity *= 0.99;
                    
                    if (particle.y < -100 || particle.opacity < 0.005) {
                        particle.y = smokeCanvas.height + Math.random() * 100;
                        particle.x = Math.random() * smokeCanvas.width;
                        particle.opacity = Math.random() * 0.05 + 0.02;
                    }
                    
                    const gradient = smokeCtx.createRadialGradient(
                        particle.x, particle.y, 0,
                        particle.x, particle.y, particle.size
                    );
                    
                    gradient.addColorStop(0, `rgba(61, 226, 255, ${particle.opacity})`);
                    gradient.addColorStop(1, `rgba(61, 226, 255, 0)`);
                    
                    smokeCtx.globalCompositeOperation = 'lighter';
                    smokeCtx.beginPath();
                    smokeCtx.arc(particle.x, particle.y, particle.size, 0, Math.PI * 2);
                    smokeCtx.fillStyle = gradient;
                    smokeCtx.fill();
                });
                
                requestAnimationFrame(drawSmoke);
            }
            
            drawSmoke();
        }
    </script>
</body>
</html>
