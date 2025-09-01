@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* =============== Base page styles (New Stylish Redesign - V3 FINAL) =============== */
  :root {
    --bg-dark-1: #1A1A1A;
    --bg-dark-2: #1F1F1F;
    --bg-dark-3: #262626;
    --border-color: #333333;
    --glow-purple: rgba(161, 82, 242, 0.5);
    --glow-cyan: rgba(15, 248, 246, 0.5);
    --glow-green: rgba(43, 250, 106, 0.5);
    --glow-yellow: rgba(255, 219, 70, 0.5);
    --glow-pink: rgba(255, 72, 122, 0.5);
    --primary-green: #2BFA6A;
    --primary-yellow: #FFDB46;
    --primary-pink: #FF487A;
    --primary-blue: #1173F3;
    --primary-cyan: #0FF8F6;
    --primary-purple: #A152F2;
  }
  
  html,body{background:var(--bg-dark-1)!important;color:#e5e7eb; font-family: sans-serif;}
  /* AGGRESSIVE BACKGROUND FIX: This targets any potential wrapper div from layouts/app.blade.php */
  body > div, body > main, body > div > main, body > div > div { background: var(--bg-dark-1) !important; }

  .maxw{max-width:1150px;margin:0 auto;border:1px solid var(--border-color);border-radius:18px;padding:8px; box-shadow: 0 0 40px rgba(161, 82, 242, 0.15);}

  .card, .ground-slab, .analyze-wrap {
    border-radius:18px; padding:18px; background:var(--bg-dark-2); border:1px solid var(--border-color);
    box-shadow: 0 0 20px rgba(0,0,0,.3), 0 0 25px var(--glow-purple-trans, rgba(161, 82, 242, 0));
    transition: box-shadow 0.3s ease;
  }
  .card:hover, .ground-slab:hover, .analyze-wrap:hover { --glow-purple-trans: rgba(161, 82, 242, 0.1); }
  .ground-slab { margin-top: 20px; }
  .analyze-wrap { padding:12px; }

  .title-wrap{display:flex;align-items:center;gap:14px;justify-content:center;margin-top:14px}
  .king{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:var(--bg-dark-2);border:1px solid var(--border-color)}
  .t-grad{background:linear-gradient(90deg, var(--primary-cyan), var(--primary-purple), var(--primary-pink), var(--primary-yellow), var(--primary-green));-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900}
  
  .byline{font-size:14px;color:#cbd5e1}
  .shoail{display:inline-block;background:linear-gradient(90deg, #22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;background-size:400% 100%;animation:rainbowSlide 6s linear infinite,bob 3s ease-in-out infinite}
  @keyframes rainbowSlide{to{background-position:100% 50%}} @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  /* Animated Icon & Section Heading Style */
  @keyframes icon-pulse { 0%, 100% { transform: scale(1); filter: drop-shadow(0 0 3px var(--glow-color)); } 50% { transform: scale(1.1); filter: drop-shadow(0 0 8px var(--glow-color)); } }
  .section-header { display: flex; align-items: center; gap: 12px; margin: 0 0 16px; }
  .section-header .icon { --glow-color: var(--glow-purple); animation: icon-pulse 4s ease-in-out infinite; }
  .section-header .icon svg { width: 24px; height: 24px; }
  .section-header h3 { margin: 0; font-weight: 900; font-size: 20px; }

  /* ===================== Overall Score Wheel & Toolbar ===================== */
  .mw{--v:0;width:200px;height:200px;position:relative;filter:drop-shadow(0 10px 24px rgba(0,0,0,.35))}
  .mw-ring{position:absolute;inset:0;border-radius:50%;
    background: conic-gradient(from -90deg, var(--primary-pink), var(--primary-yellow), var(--primary-green), var(--primary-cyan), var(--primary-purple));
    -webkit-mask: conic-gradient(from -90deg,#000 calc(var(--v)*1%), #0000 0), radial-gradient(circle 76px,transparent 72px,#000 72px);
    mask: conic-gradient(from -90deg,#000 calc(var(--v)*1%), #0000 0), radial-gradient(circle 76px,transparent 72px,#000 72px);
  }
  .mw-fill{ display: none; }
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:44px;font-weight:900;color:#fff;text-shadow:0 0 20px var(--glow-cyan)}
  .mw-center span { font-size: 24px; color: #aaa; margin-left: 2px; }
  .mw.good .mw-ring { filter: drop-shadow(0 0 10px var(--primary-green)); }
  .mw.warn .mw-ring { filter: drop-shadow(0 0 10px var(--primary-yellow)); }
  .mw.bad .mw-ring { filter: drop-shadow(0 0 10px var(--primary-pink)); }
  .mw-sm { width: 150px; height: 150px; }
  .mw-sm .mw-ring {
    -webkit-mask: conic-gradient(from -90deg,#000 calc(var(--v)*1%),#0000 0),radial-gradient(circle 58px,transparent 54px,#000 54px);
    mask: conic-gradient(from -90deg,#000 calc(var(--v)*1%),#0000 0),radial-gradient(circle 58px,transparent 54px,#000 54px);
  }
  .mw-sm .mw-center { font-size: 34px; }
  .mw-sm .mw-center span { font-size: 18px; }
  
  .chip{padding:6px 8px;border-radius:12px;font-weight:800;display:inline-flex;align-items:center;gap:6px;border:1px solid #ffffff24;color:#eef2ff;font-size:12px}
  .chip i{font-style:normal}
  .chip.good{background:linear-gradient(135deg,rgba(43,250,106,.25),rgba(43,250,106,.1));border-color:rgba(43,250,106,.5)}
  .chip.warn{background:linear-gradient(135deg,rgba(255,219,70,.25),rgba(255,219,70,.1));border-color:rgba(255,219,70,.5)}
  .chip.bad{background:linear-gradient(135deg,rgba(255,72,122,.25),rgba(255,72,122,.1));border-color:rgba(255,72,122,.5)}
  .waterbox{position:relative;height:16px;border-radius:9999px;overflow:hidden;border:1px solid var(--border-color);background:var(--bg-dark-1)}
  .waterbox .fill{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .waterbox.good .fill{background:linear-gradient(90deg,var(--primary-green), #8affb1)}
  .waterbox.warn .fill{background:linear-gradient(90deg,var(--primary-yellow), #ffeb9b)}
  .waterbox.bad .fill{background:linear-gradient(90deg,var(--primary-pink), #ff9bbd)}
  .waterbox .label{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e5e7eb;font-size:11px}
  .url-row{display:flex;align-items:center;gap:10px;border:1px solid var(--border-color);background:var(--bg-dark-1);border-radius:12px;padding:8px 10px}
  .url-row input{background:transparent;border:none;outline:none;color:#e5e7eb;width:100%}
  .url-row .paste{padding:6px 10px;border-radius:10px;border:1px solid #333333;background:rgba(255,255,255,0.05);color:#e5e7eb}
  .btn{padding:10px 14px;border-radius:12px;font-weight:900;border:1px solid transparent;color:#1A1A1A;font-size:13px;box-shadow: 0 0 12px rgba(0,0,0,.5)}
  .btn-green{background:var(--primary-green)}.btn-blue{background:var(--primary-blue)}.btn-orange{background:var(--primary-yellow)}.btn-purple{background:linear-gradient(90deg,var(--primary-pink),var(--primary-purple));color:#fff}

  /* ===================== Content Optimization (Restored & Redesigned) ===================== */
  .co-card { --glow-purple-trans: rgba(161, 82, 242, 0.2); }
  .co-grid { display: grid; grid-template-columns: 240px 1fr; gap: 16px; align-items: center; }
  @media (max-width: 920px) { .co-grid { grid-template-columns: 1fr; } }
  .co-meter-wrap { display: grid; place-items: center; padding: 10px; }
  .co-meter { width: 200px; height: 200px; position: relative; display: grid; place-items: center; }
  .co-meter-bg { position: absolute; inset: 0; background: conic-gradient(var(--bg-dark-3) 0deg 270deg, var(--bg-dark-2) 270deg 360deg); border-radius: 50%; box-shadow: 0 0 0 1px #333, 0 0 0 5px var(--bg-dark-1), 0 0 0 6px #333; }
  .co-meter-progress { position: absolute; inset: 0; border-radius: 50%; --v: 0; background: conic-gradient(from -135deg, var(--primary-purple) 0deg, var(--primary-cyan) 90deg, transparent 90deg); -webkit-mask: conic-gradient(from -135deg, #000 0deg, #000 calc(var(--v) * 2.7deg), transparent calc(var(--v) * 2.7deg + 1deg)); mask: conic-gradient(from -135deg, #000 0deg, #000 calc(var(--v) * 2.7deg), transparent calc(var(--v) * 2.7deg + 1deg)); transform: rotate(180deg); transition: --v 1s ease-in-out; filter: drop-shadow(0 0 8px var(--primary-cyan)) drop-shadow(0 0 12px var(--primary-purple)); }
  .co-meter-inner { position: relative; width: 150px; height: 150px; border-radius: 50%; background: linear-gradient(135deg, var(--bg-dark-2), var(--bg-dark-1)); display: grid; place-items: center; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,.3), 0 0 0 1px #333 inset; }
  .co-meter-score { font-size: 44px; font-weight: 900; line-height: 1; color: #fff; text-shadow: 0 0 10px var(--primary-cyan); }
  .co-meter-label { font-size: 12px; color: #aab3c2; margin-top: 4px; }
  .co-info-grid { display: grid; grid-template-columns: 1fr; gap: 12px; }
  .co-info-item { border-radius: 14px; padding: 14px; background: rgba(26, 26, 26, 0.7); border: 1px solid var(--border-color); box-shadow: 0 8px 24px rgba(0,0,0,.3); }
  .co-info-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
  .co-info-title { font-weight: 800; color: #e5e7eb; }
  .co-badge{display:inline-flex;align-items:center;gap:8px;border-radius:999px;padding:6px 10px;font-weight:700;font-size:12px;border:1px solid var(--border-color);background:var(--bg-dark-3);color:#dbe7ff}
  .co-badge.good{background:rgba(43,250,106,.12);border-color:rgba(43,250,106,.35);color:#b1ffce}
  .co-badge.warn{background:rgba(255,219,70,.12);border-color:rgba(255,219,70,.35);color:#ffeca5}
  .co-badge.bad{background:rgba(255,72,122,.12);border-color:rgba(255,72,122,.35);color:#ffc5d6}
  .co-tips{display:flex;flex-direction:column;gap:8px;margin-top:8px}
  .co-tips .tip{border-left:3px solid var(--border-color);padding-left:10px;color:#cdd6ef;font-size:12px}
  .progress{width:100%;height:10px;border-radius:9999px;background:var(--bg-dark-1);overflow:hidden;border:1px solid var(--border-color)}
  .progress>span{display:block;height:100%;border-radius:9999px;background:linear-gradient(90deg,var(--primary-pink),var(--primary-yellow),var(--primary-green));transition:width .5s ease}

  /* ===================== Meta Info Layout ===================== */
  .meta-info-card { --glow-purple-trans: rgba(15, 248, 246, 0.15); }
  .meta-item { border: 1px solid var(--border-color); background: var(--bg-dark-3); padding: 12px; border-radius: 12px; margin-bottom: 10px; }
  .meta-item-header { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
  .meta-item-header .tag { font-size: 12px; font-weight: 800; padding: 3px 8px; border-radius: 7px; color: #fff; }
  .meta-item-header .h1 { background: var(--primary-pink); }
  .meta-item-header .h2 { background: var(--primary-yellow); color: var(--bg-dark-1); }
  .meta-item-header .h3 { background: var(--primary-green); color: var(--bg-dark-1); }
  .meta-item-header .h4 { background: var(--primary-blue); }
  .meta-content { color: #d1d5db; word-break: break-word; }
  .meta-title, .meta-desc { padding: 10px; color: #e5e7eb; font-weight: 600; }

  /* ===================== Site Speed (Restored & Redesigned) ===================== */
  .speed-card { --glow-purple-trans: rgba(43, 250, 106, 0.15); }
  .sp-wheels{display:flex;justify-content:center;align-items:center;gap:18px;margin-bottom:16px;flex-wrap:wrap}
  .wheel-card{display:grid;place-items:center;border-radius:16px;padding:10px;background:var(--bg-dark-3);border:1px solid var(--border-color);position:relative;box-shadow:0 8px 28px rgba(0,0,0,.35);width:180px}
  .wheel-label{font-size:12px;color:#a6c5cf;margin-top:6px}
  .speed-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 14px; }
  .speed-tile { background: var(--bg-dark-3); border: 1px solid var(--border-color); border-radius: 14px; padding: 12px; }
  .speed-row { display: flex; align-items: center; justify-content: space-between; font-size: 12px; color: #a6c5cf; margin: 6px 0; }
  .speed-val { color: #e5e7eb; font-weight: 800; }
  .speed-meter { height: 12px; border-radius: 9999px; background: var(--bg-dark-1); border: 1px solid var(--border-color); overflow: hidden; position: relative; }
  .speed-meter>span { display: block; height: 100%; width: 0%; transition: width .9s ease; background: linear-gradient(90deg, var(--primary-pink), var(--primary-yellow), var(--primary-green)); }
  .speed-suggestions { margin-top: 16px; background: var(--bg-dark-3); border: 1px solid var(--border-color); border-radius: 14px; padding: 14px; }
  .speed-suggestions h4 { margin: 0 0 10px 0; display: flex; align-items: center; gap: 8px; font-weight: 800; }
  .speed-suggestions ul { margin: 0; padding-left: 0; list-style: none; display: grid; gap: 8px; }
  .speed-suggestions li { padding: 10px; border-radius: 10px; font-size: 13px; font-weight: 600; border-left: 3px solid; }
  .speed-suggestions li.good { background: rgba(43,250,106,.1); border-color: var(--primary-green); color: #c1ffda; }
  .speed-suggestions li.warn { background: rgba(255,219,70,.1); border-color: var(--primary-yellow); color: #ffeea8; }
  .speed-suggestions li.bad { background: rgba(255,72,122,.1); border-color: var(--primary-pink); color: #ffc5d6; }
  
  /* ===================== Semantic SEO Ground (Accordion Redesign) ===================== */
  .seo-ground-card { --glow-purple-trans: rgba(161, 82, 242, 0.2); }
  .accordion-item { border-bottom: 1px solid var(--border-color); }
  .accordion-item:last-child { border-bottom: none; }
  .accordion-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 18px; cursor: pointer; background: var(--bg-dark-2); transition: background 0.2s ease; }
  .accordion-header:hover { background: var(--bg-dark-3); }
  .accordion-title { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 16px; }
  .accordion-title .icon { font-size: 20px; }
  .accordion-toggle { font-size: 24px; transition: transform 0.3s ease; line-height: 1; }
  .accordion-item.active .accordion-toggle { transform: rotate(45deg); }
  .accordion-content { max-height: 0; overflow: hidden; transition: max-height 0.4s ease-out; background: var(--bg-dark-1); }
  .accordion-content-inner { padding: 18px; }
  .check{display:flex;align-items:center;justify-content:space-between;border-radius:12px;padding:10px 12px;border:1px solid var(--border-color);background:var(--bg-dark-3); margin-bottom: 8px;}
  .score-pill{padding:3px 7px;border-radius:10px;font-weight:800;background:rgba(255,255,255,0.1);border:1px solid #ffffff22;color:#e5e7eb;font-size:12px}
  .score-pill--green{background:rgba(43,250,106,.15);border-color:rgba(43,250,106,.4);color:#9cffd1}
  .score-pill--orange{background:rgba(255,219,70,.15);border-color:rgba(255,219,70,.4);color:#ffeea8}
  .score-pill--red{background:rgba(255,72,122,.15);border-color:rgba(255,72,122,.4);color:#ffc5d6}
  .improve-btn{padding:6px 9px;border-radius:10px;color:var(--bg-dark-1);font-weight:800;border:1px solid transparent;transition:transform .08s ease;font-size:12px}
  .improve-btn:active{transform:translateY(1px)}
  .fill-green {background:var(--primary-green);color:#003d14}
  .fill-orange{background:var(--primary-yellow);color:#4d3c00}
  .fill-red {background:var(--primary-pink);color:#4d0016}
  
  /* Modal Styles */
  dialog[open]{display:block} dialog::backdrop{background:rgba(0,0,0,.6)}
  #improveModal .card{background:var(--bg-dark-1);border:1px solid var(--border-color)}
  #improveModal .card .card{background:var(--bg-dark-2);border-color:var(--border-color)}
</style>
<script defer>
(function(){
  const init = () => {
    const $ = s=>document.querySelector(s);
    
    /* ============== Element Refs ============== */
    const mw=$('#mw'), mwRing=$('#mwRing'), mwNum=$('#mwNum');
    const analyzeBtn=$('#analyzeBtn');
    const urlInput=$('#urlInput');
    
    // Top-level summary refs
    const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'), chipHuman=$('#chipHuman'), chipAI=$('#chipAI');
    const overallBar=$('#overallBar'), overallFill=$('#overallFill'), overallPct=$('#overallPct');

    // Content Opt refs
    const coMeterProgress = $('#coMeterProgress'), coMeterScore = $('#coMeterScore');
    const coNlpBadge = $('#coNlpBadge'), nlpTips = $('#nlpTips');
    
    // Meta Info refs
    const metaTitleEl = $('#metaTitle'), metaDescEl = $('#metaDesc'), headingMapEl = $('#headingMap');

    // Speed UI refs
    const mwMobile=$('#mwMobile'), ringMobile=$('#ringMobile'), numMobile=$('#numMobile');
    const mwDesktop=$('#mwDesktop'), ringDesktop=$('#ringDesktop'), numDesktop=$('#numDesktop');
    const lcpVal=$('#lcpVal'), lcpBar=$('#lcpBar'), lcpMeter=$('#lcpMeter');
    const clsVal=$('#clsVal'), clsBar=$('#clsBar'), clsMeter=$('#clsMeter');
    const inpVal=$('#inpVal'), inpBar=$('#inpBar'), inpMeter=$('#inpMeter');
    const ttfbVal=$('#ttfbVal'), ttfbBar=$('#ttfbBar'), ttfbMeter=$('#ttfbMeter');
    const psiFixes=$('#psiFixes');
    
    // Modal refs
    const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
          mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
          mTips=$('#improveTips'), mLink=$('#improveSearch');

    /* Helpers */
    const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
    const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
    const bandIcon=s=>s>=80?'✅':(s>=60?'🟧':'🔴');
    function setChip(el,label,value,score){ if(!el)return; el.classList.remove('good','warn','bad'); const b=bandName(score); el.classList.add(b); el.innerHTML=`<i>${bandIcon(score)}</i><span>${label}: ${value}</span>`; };
    const setRunning=(isOn)=>{if(!analyzeBtn)return;analyzeBtn.disabled=isOn;analyzeBtn.style.opacity=isOn?.6:1;analyzeBtn.textContent=isOn?'Analyzing…':'🔍 Analyze'};
    const scoreFromBounds=(val,good,poor)=>{if(val==null||isNaN(val))return 0;if(val<=good)return 100;if(val>=poor)return 0;return Math.round(100*(1-((val-good)/(poor-good))))};
    function setWheel(elRing,elNum,container,score){const b=bandName(score);if(!container) return; container.classList.remove('good','warn','bad');container.classList.add(b);if(elRing)elRing.style.setProperty('--v',score);if(elNum)elNum.innerHTML=`${score}<span>%</span>`;}
    function setSpMeter(barEl,valEl,raw,score,fmt,meterWrap){if(!valEl||!barEl)return;valEl.textContent=raw==null?'—':(fmt?fmt(raw):raw);barEl.style.width=clamp01(score)+'%';if(meterWrap){meterWrap.classList.remove('good','warn','bad');meterWrap.classList.add(bandName(score));}}

    /* KB and Scoring Data */
    const CATS=[{name:'User Signals & Experience',icon:'📱',checks:['Mobile-friendly, responsive layout','Optimized speed (compression, lazy-load)','Core Web Vitals passing (LCP/INP/CLS)','Clear CTAs and next steps','Accessible basics (alt text, contrast)']},{name:'Entities & Context',icon:'🧩',checks:['sameAs/Organization details present','Valid schema markup (Article/FAQ/Product)','Related entities covered with context','Primary entity clearly defined','Organization contact/about page visible']},{name:'Structure & Architecture',icon:'🏗️',checks:['Logical H2/H3 headings & topic clusters','Internal links to hub/related pages','Clean, descriptive URL slug','Breadcrumbs enabled (+ schema)','XML sitemap logical structure']},{name:'Content Quality',icon:'🧠',checks:['E-E-A-T signals (author, date, expertise)','Unique value vs. top competitors','Facts & citations up to date','Helpful media (images/video) w/ captions','Up-to-date examples & screenshots']},{name:'Content & Keywords',icon:'📝',checks:['Define search intent & primary topic','Map target & related keywords (synonyms/PAA)','H1 includes primary topic naturally','Integrate FAQs / questions with answers','Readable, NLP-friendly language']},{name:'Technical Elements',icon:'⚙️',checks:['Title tag (≈50–60 chars) w/ primary keyword','Meta description (≈140–160 chars) + CTA','Canonical tag set correctly','Indexable & listed in XML sitemap','Robots directives valid']}];
    const KB={'Mobile-friendly, responsive layout':{why:'Most traffic is mobile; poor UX kills engagement.',tips:['Use responsive breakpoints & fluid grids.','Ensure tap targets are at least 44px.','Avoid horizontal scroll on mobile.'],link:'https://search.google.com/test/mobile-friendly'},'Optimized speed (compression, lazy-load)':{why:'Speed affects user abandonment and Core Web Vitals.',tips:['Compress images with modern formats like WebP/AVIF.','Utilize HTTP/2 and a CDN for caching.','Lazy-load images and videos below the fold.'],link:'https://web.dev/fast/'},'Core Web Vitals passing (LCP/INP/CLS)':{why:'Passing CWV is a known signal for better user experience and can influence rankings.',tips:['Preload the Largest Contentful Paint (LCP) image.','Minimize long JavaScript tasks to improve Interaction to Next Paint (INP).','Reserve space for images and ads to prevent Cumulative Layout Shift (CLS).'],link:'https://web.dev/vitals/'},'Clear CTAs and next steps':{why:'Clarity increases conversions and task completion rates.',tips:['Use one primary Call-to-Action (CTA) per view.','Write action-oriented button text (e.g., "Get Started").','Explain what happens after the user clicks.'],link:'https://www.nngroup.com/articles/call-to-action-buttons/'},'Accessible basics (alt text, contrast)':{why:'Accessibility broadens your audience and is a legal and ethical best practice.',tips:['Provide descriptive alt text for all meaningful images.','Ensure text-to-background contrast ratio is at least 4.5:1.','Implement clear keyboard focus states for all interactive elements.'],link:'https://www.w3.org/WAI/standards-guidelines/wcag/'},'sameAs/Organization details present':{why:'`sameAs` schema helps search engines disambiguate your brand from others.',tips:['Use Organization schema with `sameAs` links to social media and official profiles.','Ensure Name, Address, and Phone (NAP) consistency across the web.'],link:'https://schema.org/Organization'},'Valid schema markup (Article/FAQ/Product)':{why:'Structured data can unlock rich results in SERPs, improving visibility and CTR.',tips:['Use the Rich Results Test to validate your schema.','Only mark up content that is visible to the user on the page.','Stick to schema types supported by Google for rich results.'],link:'https://search.google.com/test/rich-results'},'Related entities covered with context':{why:'Covering related topics and entities demonstrates topical depth and expertise.',tips:['Mention related concepts and explain their relationship to the main topic.','Link out to authoritative sources and references.'],link:'https://developers.google.com/knowledge-graph'},'Primary entity clearly defined':{why:'A single, clear main entity helps search engines understand the page\'s primary purpose.',tips:['Define the primary topic at the beginning of the content.','Use consistent naming for the entity throughout the page.','Add specific schema (e.g., `mainEntityOfPage`) to declare it.'],link:'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data'},'Organization contact/about page visible':{why:'Clear contact and about pages support the E-E-A-T (Experience, Expertise, Authoritativeness, Trust) framework.',tips:['Create dedicated `/about` and `/contact` pages.','Link to these pages from your site header and/or footer.','Include a physical address, email, and phone number.'],link:'https://developers.google.com/search/docs/fundamentals/creating-helpful-content'},'Logical H2/H3 headings & topic clusters':{why:'A logical heading hierarchy helps users skim content and helps search engines understand its structure.',tips:['Group related subtopics under a single H2.','Use H3s for more granular points like steps or examples within an H2 section.','Keep paragraphs and sections concise.'],link:'https://moz.com/learn/seo/site-structure'},'Internal links to hub/related pages':{why:'Internal links distribute PageRank and provide context to search engines.',tips:['Link to 3–5 relevant hub pages or cornerstone content.','Use descriptive, keyword-rich anchor text.','Add a "Further Reading" section for related articles.'],link:'https://ahrefs.com/blog/internal-links/'},'Clean, descriptive URL slug':{why:'Readable URLs improve user experience, CTR, and provide a small ranking signal.',tips:['Use 3–5 meaningful words that describe the page content.','Separate words with hyphens and use all lowercase.','Avoid long, cryptic query strings.'],link:'https://developers.google.com/search/docs/crawling-indexing/url-structure'},'Breadcrumbs enabled (+ schema)':{why:'Breadcrumbs clarify the user\'s location on your site and can appear in SERPs.',tips:['Ensure breadcrumbs are visible on the page.','Implement `BreadcrumbList` schema for rich results.','Keep the navigation depth logical.'],link:'https://developers.google.com/search/docs/appearance/structured-data/breadcrumb'},'XML sitemap logical structure':{why:'A well-structured sitemap helps search engines discover and index your content more efficiently.',tips:['Only include canonical URLs in your sitemap.','For large sites, segment sitemaps into smaller, logical groups.','Reference your sitemap location in your `robots.txt` file.'],link:'https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview'},'E-E-A-T signals (author, date, expertise)':{why:'Trust signals are crucial for convincing users and search engines of your credibility.',tips:['Include an author bio with credentials and links to their work.','Display a "Last updated" date on your content.','Create an editorial policy or "About Us" page detailing your expertise.'],link:'https://developers.google.com/search/blog/2022/08/helpful-content-update'},'Unique value vs. top competitors':{why:'To rank, your content must be better or different than what already exists.',tips:['Provide original research, data, or examples.','Offer a unique perspective or a more comprehensive guide.','Clearly explain why your approach or solution is superior.'],link:'https://backlinko.com/seo-techniques'},'Facts & citations up to date':{why:'Freshness and accuracy are key trust signals.',tips:['Cite primary sources whenever possible.','Update statistics and data points that are older than 12-18 months.','Link to reputable, authoritative sources.'],link:'https://scholar.google.com/'},'Helpful media (images/video) w/ captions':{why:'Media improves comprehension, engagement, and dwell time.',tips:['Include at least 3-5 relevant images or a video.','Write descriptive captions for all media.','Compress and lazy-load media to maintain page speed.'],link:'https://web.dev/optimize-lcp/'},'Up-to-date examples & screenshots':{why:'Current visuals are critical for tutorials and product-related content.',tips:['Refresh screenshots of user interfaces to reflect the current version.','Date your examples to provide context.','Remove or update examples of deprecated processes.'],link:'https://www.nngroup.com/articles/guidelines-for-screenshots/'},'Define search intent & primary topic':{why:'Matching user search intent is the most critical factor for relevance.',tips:['State the primary outcome or answer early in the content.','Align your content format (e.g., listicle, guide, review) with the intent.','Use concrete examples and step-by-step instructions.'],link:'https://ahrefs.com/blog/search-intent/'},'Map target & related keywords (synonyms/PAA)':{why:'Using semantic variations helps capture a wider range of queries.',tips:['Include 6–12 keyword variations and synonyms.','Answer 5–10 "People Also Ask" (PAA) questions from Google.','Structure PAA answers concisely (40–60 words) for featured snippets.'],link:'https://developers.google.com/search/docs/fundamentals/seo-starter-guide'},'H1 includes primary topic naturally':{why:'The H1 is a strong signal of the page\'s main topic.',tips:['Use only one H1 tag per page.','Place your primary topic near the beginning of the H1.','Make it descriptive and compelling for users.'],link:'https://web.dev/learn/html/semantics/#headings'},'Integrate FAQs / questions with answers':{why:'FAQs capture long-tail search traffic and can earn rich results.',tips:['Choose 3–6 highly relevant questions for your topic.','Provide brief, direct answers.','Implement `FAQPage` schema to be eligible for rich results.'],link:'https://developers.google.com/search/docs/appearance/structured-data/faqpage'},'Readable, NLP-friendly language':{why:'Plain language improves comprehension for all users and is easier for algorithms to process.',tips:['Keep average sentence length below 20 words.','Prefer active voice over passive voice.','Define jargon on its first use.'],link:'https://www.plainlanguage.gov/guidelines/'},'Title tag (≈50–60 chars) w/ primary keyword':{why:'The title tag is one of the most important on-page SEO signals.',tips:['Keep titles between 50–60 characters to avoid truncation.','Place the primary keyword at the beginning.','Avoid duplicating title tags across your site.'],link:'https://moz.com/learn/seo/title-tag'},'Meta description (≈140–160 chars) + CTA':{why:'A compelling meta description drives clicks from the SERP.',tips:['Write descriptions between 140–160 characters.','Include a benefit and a call-to-action (CTA).','Ensure it aligns with the user\'s search intent.'],link:'https://moz.com/learn/seo/meta-description'},'Canonical tag set correctly':{why:'The canonical tag prevents duplicate content issues by consolidating ranking signals.',tips:['Use one canonical tag per page.','Use absolute, not relative, URLs.','Ensure it points to the correct master version of the page.'],link:'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls'},'Indexable & listed in XML sitemap':{why:'A page must be indexable to appear in search results.',tips:['Ensure there is no `noindex` directive in the meta tags or headers.','Include the URL in your XML sitemap.','Submit your sitemap in Google Search Console.'],link:'https://developers.google.com/search/docs/crawling-indexing/overview'},'Robots directives valid':{why:'Incorrect robots directives can prevent search engines from crawling or indexing important content.',tips:['Check your `robots` meta tag to ensure it allows indexing (`index, follow`).','Verify your `robots.txt` file is not blocking important resources.','Use directives consistently to avoid conflicting signals.'],link:'https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag'}};
    function scoreChecklist(label,data,url,targetKw=''){const qs=data.quick_stats||{};const cs=data.content_structure||{};const ps=data.page_signals||{};const r=data.readability||{};const h1=(cs.headings&&cs.headings.H1?cs.headings.H1.length:0)||0;const h2=(cs.headings&&cs.headings.H2?cs.headings.H2.length:0)||0;const h3=(cs.headings&&cs.headings.H3?cs.headings.H3.length:0)||0;const title=(cs.title||'');const meta=(cs.meta_description||'');const internal=Number(qs.internal_links||0);const external=Number(qs.external_links||0);const schemaTypes=new Set((data.page_signals?.schema_types)||[]);const robots=(data.page_signals?.robots||'').toLowerCase();const hasFAQ=schemaTypes.has('FAQPage');const hasArticle=schemaTypes.has('Article')||schemaTypes.has('NewsArticle')||schemaTypes.has('BlogPosting');const urlPath=(()=>{try{return new URL(url).pathname;}catch{return '/';}})();const slugScore=(()=>{const hasQuery=url.includes('?');const segs=urlPath.split('/').filter(Boolean);const words=segs.join('-').split('-').filter(Boolean);if(hasQuery)return 55;if(segs.length>6)return 60;if(words.some(w=>w.length>24))return 65;return 85;})();switch(label){case'Mobile-friendly, responsive layout':return ps.has_viewport?88:58;case'Optimized speed (compression, lazy-load)':return 60;case'Core Web Vitals passing (LCP/INP/CLS)':return 60;case'Clear CTAs and next steps':return meta.length>=140&&/learn|get|try|start|buy|sign|download|contact/i.test(meta)?80:60;case'Accessible basics (alt text, contrast)':return (data.images_alt_count||0)>=3?82:((data.images_alt_count||0)>=1?68:48);case'sameAs/Organization details present':return ps.has_org_sameas?90:55;case'Valid schema markup (Article/FAQ/Product)':return (hasArticle||hasFAQ||schemaTypes.has('Product'))?85:(schemaTypes.size>0?70:50);case'Related entities covered with context':return external>=2?72:60;case'Primary entity clearly defined':return ps.has_main_entity?85:(h1>0?72:58);case'Organization contact/about page visible':return 60;case'Logical H2/H3 headings & topic clusters':return (h2>=3&&h3>=2)?85:(h2>=2?70:55);case'Internal links to hub/related pages':return internal>=5?85:(internal>=2?65:45);case'Clean, descriptive URL slug':return slugScore;case'Breadcrumbs enabled (+ schema)':return ps.has_breadcrumbs?85:55;case'XML sitemap logical structure':return 60;case'E-E-A-T signals (author, date, expertise)':return ps.has_org_sameas?75:65;case'Unique value vs. top competitors':return 60;case'Facts & citations up to date':return external>=2?78:58;case'Helpful media (images/video) w/ captions':return (data.images_alt_count||0)>=3?82:58;case'Up-to-date examples & screenshots':return 60;case'Define search intent & primary topic':return (title&&h1>0)?78:60;case'Map target & related keywords (synonyms/PAA)':{const kw=(targetKw||'').trim();if(!kw)return 60;const found=(title.toLowerCase().includes(kw.toLowerCase())||(cs.headings?.H1||[]).join(' || ').toLowerCase().includes(kw.toLowerCase()));return found?80:62}case'H1 includes primary topic naturally':{const kw=(targetKw||'').trim();if(h1===0)return 45;if(!kw)return 72;const found=(cs.headings?.H1||[]).some(h=>h.toLowerCase().includes(kw.toLowerCase()));return found?84:72}case'Integrate FAQs / questions with answers':return hasFAQ?85:(/(faq|questions?)/i.test((cs.headings?.H2||[]).join(' ')+' '+(cs.headings?.H3||[]).join(' '))?70:55);case'Readable, NLP-friendly language':return clamp01(r.score||0);case'Title tag (≈50–60 chars) w/ primary keyword':{const len=(title||'').length;return (len>=50&&len<=60)?88:(len?68:45)}case'Meta description (≈140–160 chars) + CTA':{const len=(meta||'').length;const hasCTA=/learn|get|try|start|buy|sign|download|contact/i.test(meta||'');return (len>=140&&len<=160)?(hasCTA?90:82):(len?65:48)}case'Canonical tag set correctly':return ps.canonical?85:55;case'Indexable & listed in XML sitemap':return robots.includes('noindex')?20:80;case'Robots directives valid':return (robots&&/(noindex|none)/.test(robots))?45:75;}return 60}
    async function callAPI(endpoint, url) { try { const res = await fetch(endpoint, { method: 'POST', headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ url }) }); if (!res.ok) { const txt = await res.text(); throw new Error(`HTTP ${res.status}: ${txt.slice(0, 200)}`); } return res.json(); } catch (e) { console.error(`Failed to call ${endpoint}`, e); throw e; } }
    async function callAnalyzer(url) { return callAPI('/semantic-analyzer/analyze', url); }
    async function callPSI(url) { return callAPI('/semantic-analyzer/psi', url); }
    
    /* ===== Main Analyze Function ===== */
    analyzeBtn?.addEventListener('click', async e=>{
      e.preventDefault();
      setRunning(true);
      if(!urlInput.value.trim()){ setRunning(false); return; }

      try {
        const data = await callAnalyzer(urlInput.value);
        if(!data||data.error) throw new Error(data?.error||'Unknown error');
        
        // Overall Score & Chips
        const score=clamp01(data.overall_score||0), bname=bandName(score);
        setWheel(mwRing, mwNum, mw, score);
        setChip(chipOverall,'Overall', `${score} /100`, score);
        const cmap={};(data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
        const contentScore=Math.round(([cmap['Content & Keywords'],cmap['Content Quality']].filter(v=>typeof v==='number').reduce((a,b)=>a+b,0))/2||0);
        setChip(chipContent,'Content', `${contentScore} /100`, contentScore);
        const r=data.readability||{};
        const human=clamp01(Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3));
        const ai=clamp01(100-human);
        setChip(chipWriter,'Writer',human>=60?'Likely Human':'Possibly AI',human);
        setChip(chipHuman,'Human-like', `${human}%`,human);
        setChip(chipAI,'AI-like', `${ai}%`,100-human);
        if(overallBar) { overallBar.className = 'waterbox ' + bname; overallFill.style.width=score+'%'; overallPct.textContent=score+'%'; }

        // Content Opt Score & Tips
        if(data.content_optimization) {
            const co = data.content_optimization;
            if (co.nlp_score != null) {
                if(coMeterScore) coMeterScore.textContent = co.nlp_score;
                if(coMeterProgress) coMeterProgress.style.setProperty('--v', co.nlp_score);
                const badgeText = n=> n>=80?'Excellent':(n>=60?'Good':'Needs Work');
                const badgeClass = n=> n>=80?'good':(n>=60?'warn':'bad');
                if(coNlpBadge) { coNlpBadge.textContent = badgeText(co.nlp_score); coNlpBadge.className = 'co-badge ' + badgeClass(co.nlp_score); }
                if(nlpTips) {
                    nlpTips.innerHTML = ''; 
                    if (co.nlp_score < 60) nlpTips.innerHTML += '<div class="tip">Re-outline with clear H2/H3s around user intents.</div><div class="tip">Add definitions, comparisons, and checklists.</div>';
                    else if (co.nlp_score < 80) nlpTips.innerHTML += '<div class="tip">Expand sections with examples, data, or steps.</div><div class="tip">Ensure each H2 targets a distinct search sub-intent.</div>';
                    else nlpTips.innerHTML += '<div class="tip">Strong semantic coverage. Add a concise TL;DR for skimmers.</div>';
                }
            }
        }
        
        // Meta Info Layout
        const cs = data.content_structure || {};
        if(metaTitleEl) metaTitleEl.textContent = cs.title || '—';
        if(metaDescEl) metaDescEl.textContent = cs.meta_description || '—';
        if(headingMapEl) {
            headingMapEl.innerHTML = '';
            const headings = cs.headings || {};
            ['H1','H2','H3','H4'].forEach(level => {
                if(headings[level] && headings[level].length) {
                    headings[level].forEach(text => {
                        const el = document.createElement('div');
                        el.className = 'meta-item';
                        el.innerHTML = `<div class="meta-item-header"><span class="tag ${level.toLowerCase()}">${level}</span></div><div class="meta-content">${text}</div>`;
                        headingMapEl.appendChild(el);
                    });
                }
            });
        }
        
        // SEO Ground Accordion
        renderAccordion(data, urlInput.value);

        // Site Speed
        const psi = await callPSI(urlInput.value);
        const mobile=psi.mobile||{}; const desktop=psi.desktop||{};
        const mScore=clamp01(Math.round(mobile.score??0));
        const dScore=clamp01(Math.round(desktop.score??0));
        setWheel(ringMobile,numMobile,mwMobile,mScore);
        setWheel(ringDesktop,numDesktop,mwDesktop,dScore);
        
        const pick=(...vals)=>{for(const v of vals){const n=Number(v);if(v!==undefined&&v!==null&&!Number.isNaN(n))return n}return null};
        const lcpSeconds=pick(mobile.lcp_s,desktop.lcp_s,psi.lcp_s), cls=pick(mobile.cls,desktop.cls,psi.cls), inp=pick(mobile.inp_ms,desktop.inp_ms,psi.inp_ms), ttfb=pick(mobile.ttfb_ms,desktop.ttfb_ms,psi.ttfb);
        const sLCP=scoreFromBounds(lcpSeconds,2.5,6.0), sCLS=scoreFromBounds(cls,0.10,0.25), sINP=scoreFromBounds(inp,200,500), sTTFB=scoreFromBounds(ttfb,800,1800);
        setSpMeter(lcpBar,lcpVal,lcpSeconds,sLCP,v=>v!=null?`${v.toFixed(2)} s`:'—',lcpMeter);
        setSpMeter(clsBar,clsVal,cls,sCLS,v=>v!=null?`${v.toFixed(3)}`:'—',clsMeter);
        setSpMeter(inpBar,inpVal,inp,sINP,v=>v!=null?`${Math.round(v)} ms`:'—',inpMeter);
        setSpMeter(ttfbBar,ttfbVal,ttfb,sTTFB,v=>v!=null?`${Math.round(v)} ms`:'—',ttfbMeter);

        const tips=[];
        if(lcpSeconds > 2.5) tips.push({sev: 'bad', text:'Improve LCP: preload hero image, compress images.'});
        if(cls > 0.1) tips.push({sev: 'bad', text:'Reduce CLS: set width/height on images/media.'});
        if(inp > 200) tips.push({sev: 'warn', text:'Lower INP: break up long tasks, defer non-critical JS.'});
        if(ttfb > 800) tips.push({sev: 'warn', text:'Reduce TTFB: enable caching/CDN, optimize server.'});
        if(!tips.length) tips.push({sev: 'good', text:'Great job! Performance metrics look good.'})
        if(psiFixes) psiFixes.innerHTML=tips.map(t=>`<li class="${t.sev}">✅ ${t.text}</li>`).join('');

      } catch(err) {
        console.error(err);
      } finally {
        setRunning(false);
      }
    });

    /* ===== Build SEO Ground Accordion ===== */
    function renderAccordion(data, url) {
        const seoGround = $('#seoGround');
        if (!seoGround) return;
        seoGround.innerHTML = ''; 

        CATS.forEach(cat => {
            const item = document.createElement('div');
            item.className = 'accordion-item';
            let contentHTML = '';
            cat.checks.forEach(lbl => {
                const s = scoreChecklist(lbl, data, url);
                const fill = s >= 80 ? 'fill-green' : (s >= 60 ? 'fill-orange' : 'fill-red');
                const pill = s >= 80 ? 'score-pill--green' : s >= 60 ? 'score-pill--orange' : 'score-pill--red';
                const dot = s >= 80 ? '#2BFA6A' : s >= 60 ? '#FFDB46' : '#FF487A';
                contentHTML += `<div class="check" data-label="${lbl}" data-cat="${cat.name}"><div style="display:flex;align-items:center;gap:8px"><span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:${dot}"></span><div style="font-size:13px">${lbl}</div></div><div style="display:flex;align-items:center;gap:6px"><span class="score-pill ${pill}">${s}</span><button class="improve-btn ${fill}" type="button">Improve</button></div></div>`;
            });
            item.innerHTML = `<div class="accordion-header"><div class="accordion-title"><span class="icon">${cat.icon}</span> ${cat.name}</div><div class="accordion-toggle">+</div></div><div class="accordion-content"><div class="accordion-content-inner">${contentHTML}</div></div>`;
            seoGround.appendChild(item);
        });
    }

    // Accordion & Modal Click Listeners
    $('#seoGround')?.addEventListener('click', function(e){
        const header = e.target.closest('.accordion-header');
        if (header) {
            const item = header.parentElement;
            const content = header.nextElementSibling;
            if (item.classList.contains('active')) {
                item.classList.remove('active');
                content.style.maxHeight = null;
            } else {
                this.querySelectorAll('.accordion-item').forEach(i => { i.classList.remove('active'); i.querySelector('.accordion-content').style.maxHeight = null; });
                item.classList.add('active');
                content.style.maxHeight = content.scrollHeight + "px";
            }
            return;
        }
        const improveBtn = e.target.closest('.improve-btn');
        if (improveBtn) {
            const checkEl = improveBtn.closest('.check');
            const label = checkEl.dataset.label; const catName = checkEl.dataset.cat; const score = checkEl.querySelector('.score-pill').textContent;
            const bandTxt = score >= 80 ? 'Good (≥80)' : score >= 60 ? 'Needs work (60–79)' : 'Low (<60)';
            const pillClass = score >= 80 ? 'score-pill--green' : score >= 60 ? 'score-pill--orange' : 'score-pill--red';
            const kb = KB[label] || {why:'This item impacts relevance and UX.',tips:['Aim for ≥80 and re-run the analyzer.'],link:'https://www.google.com'};
            if(mTitle) mTitle.textContent = label;
            if(mCat) mCat.textContent = catName;
            if(mScore) mScore.textContent = score;
            if(mBand) { mBand.textContent = bandTxt; mBand.className = 'pill ' + pillClass; }
            if(mWhy) mWhy.textContent = kb.why;
            if(mTips) { mTips.innerHTML = ''; (kb.tips||[]).forEach(t=>{const li=document.createElement('li');li.textContent=t;mTips.appendChild(li)}); }
            if(mLink) mLink.href = kb.link || ('https://www.google.com/search?q='+encodeURIComponent(label+' best practices'));
            if(typeof modal.showModal==='function') modal.showModal(); else modal.setAttribute('open','');
        }
    });
  };
  if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', init, { once: true }); } else { init(); }
})();
</script>
@endpush

@section('content')
<section class="maxw px-4 pb-10">

  <div class="title-wrap">
    <div class="king">👑</div>
    <div style="text-align:center">
      <div class="t-grad" style="font-size:26px;line-height:1.1;">Semantic SEO Master Analyzer</div>
      <div class="byline">By <span class="shoail">Shoail Kahoker</span></div>
    </div>
  </div>

  <div style="display:grid; grid-template-columns: 250px 1fr; gap: 20px; align-items: start; margin-top: 10px;">
    <div class="card" style="display:grid;place-items:center;padding:8px; --glow-purple-trans: rgba(15, 248, 246, 0.2);">
      <div class="mw" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-center" id="mwNum">0<span>%</span></div>
      </div>
    </div>
    <div style="display: grid; gap: 12px;">
       <div class="card" style="padding: 12px;">
            <div style="display:flex;flex-wrap:wrap;gap:6px">
                <span id="chipOverall" class="chip"><i>-</i><span>Overall: —</span></span>
                <span id="chipContent" class="chip"><i>-</i><span>Content: —</span></span>
                <span id="chipWriter"  class="chip"><i>-</i><span>Writer: —</span></span>
                <span id="chipHuman"   class="chip"><i>-</i><span>Human-like: —</span></span>
                <span id="chipAI"      class="chip"><i>-</i><span>AI-like: —</span></span>
            </div>
            <div id="overallBar" class="waterbox" style="margin-top: 10px;">
                <div class="fill" id="overallFill" style="width:0%"></div>
                <div class="label"><span id="overallPct">0%</span></div>
            </div>
       </div>
        <div class="analyze-wrap">
            <div class="url-row">
                <span style="opacity:.75">🌐</span>
                <input id="urlInput" name="url" type="url" placeholder="https://example.com/page" />
                <button id="pasteBtn" type="button" class="paste">Paste</button>
            </div>
            <div style="display:flex;align-items:center;gap:10px;margin-top:10px; flex-wrap: wrap;">
                <div style="flex:1"></div>
                <input id="importFile" type="file" accept="application/json" style="display:none"/>
                <button id="importBtn" type="button" class="btn btn-purple">⇪ Import</button>
                <button id="analyzeBtn" type="button" class="btn btn-green">🔍 Analyze</button>
                <button id="printBtn"   type="button" class="btn btn-blue">🖨️ Print</button>
                <button id="resetBtn"   type="button" class="btn btn-orange">↻ Reset</button>
                <button id="exportBtn"  type="button" class="btn btn-purple">⬇︎ Export</button>
            </div>
        </div>
    </div>
  </div>

  <div class="card co-card" id="contentOptimizationCard" style="margin-top:20px;">
    <div class="section-header">
        <span class="icon" style="--glow-color: var(--glow-purple);">
             <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-brain-circuit"><path d="M12 5a3 3 0 1 0-5.997.125 4 4 0 0 0-2.5 3.765A3 3 0 0 0 5 15a3 3 0 1 0 5.997-.125 4 4 0 0 0 2.5-3.765A3 3 0 0 0 12 5Z"/><path d="M12 15a2.5 2.5 0 0 0-2.5 2.5v.5a2.5 2.5 0 0 0 5 0v-.5A2.5 2.5 0 0 0 12 15Z"/><path d="M17 15.5a2.5 2.5 0 0 1 5 0v.5a2.5 2.5 0 0 1-5 0Z"/><path d="M14.5 8.5a2.5 2.5 0 0 0 5 0v-.5a2.5 2.5 0 0 0-5 0Z"/><path d="M6 3.5v-2"/><path d="M12 3.5v-2"/><path d="M18 3.5v-2"/><path d="M4.5 12.5h-2"/><path d="M19.5 12.5h-2"/><path d="M12 20.5v2"/><path d="m4.037 6.16-1.133-1.32"/><path d="m19.963 6.16 1.133-1.32"/><path d="m19.963 17.84-1.133 1.32"/><path d="m4.037 17.84 1.133 1.32"/></svg>
        </span>
        <h3 class="t-grad">Content Optimization</h3>
    </div>
    <div class="co-grid">
      <div class="co-meter-wrap">
        <div class="co-meter">
          <div class="co-meter-bg"></div>
          <div class="co-meter-progress" id="coMeterProgress" style="--v: 0;"></div>
          <div class="co-meter-inner">
            <div>
              <div class="co-meter-score" id="coMeterScore">0</div>
              <div class="co-meter-label">NLP Score</div>
            </div>
          </div>
        </div>
        <div style="margin-top:10px; display:flex; flex-direction: column; gap:10px; align-items:center">
          <span id="coNlpBadge" class="co-badge warn">Needs Work</span>
          <div id="nlpTips" class="co-tips"><div class="tip">Run analysis to get tips.</div></div>
        </div>
      </div>
       <div class="co-info-grid">
         <div class="co-info-item">
            <div class="co-info-header"><span class="co-info-title">More Insights Coming Soon...</span></div>
            <p>This area will be populated with more detailed content gap analysis and schema suggestions in a future update.</p>
        </div>
      </div>
    </div>
  </div>

  <div class="card meta-info-card" style="margin-top:20px;">
    <div class="section-header">
        <span class="icon" style="--glow-color: var(--glow-cyan);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline></svg>
        </span>
        <h3>Meta & Heading Info</h3>
    </div>
    <div class="meta-item">
        <div class="meta-item-header"><strong style="color: var(--primary-cyan);">Title</strong></div>
        <div id="metaTitle" class="meta-title">—</div>
    </div>
    <div class="meta-item">
        <div class="meta-item-header"><strong style="color: var(--primary-purple);">Meta Description</strong></div>
        <div id="metaDesc" class="meta-desc">—</div>
    </div>
    <div id="headingMap"></div>
  </div>

  <div class="card speed-card" id="speedCard" style="margin-top:20px;">
    <div class="section-header">
        <span class="icon" style="--glow-color: var(--glow-green);">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        </span>
        <h3>Site Speed & Core Web Vitals</h3>
    </div>
    <div class="sp-wheels">
      <div class="wheel-card">
        <div class="mw mw-sm" id="mwMobile">
          <div class="mw-ring" id="ringMobile" style="--v:0"></div>
          <div class="mw-center" id="numMobile">0<span>%</span></div>
        </div>
        <div class="wheel-label">Mobile</div>
      </div>
      <div class="wheel-card">
        <div class="mw mw-sm" id="mwDesktop">
          <div class="mw-ring" id="ringDesktop" style="--v:0"></div>
          <div class="mw-center" id="numDesktop">0<span>%</span></div>
        </div>
        <div class="wheel-label">Desktop</div>
      </div>
    </div>
    <div class="speed-grid">
      <div class="speed-tile"><div class="speed-row"><div>🏁 LCP (s)</div><div class="speed-val" id="lcpVal">—</div></div><div class="speed-meter" id="lcpMeter"><span id="lcpBar" style="width:0%"></span></div></div>
      <div class="speed-tile"><div class="speed-row"><div>📦 CLS</div><div class="speed-val" id="clsVal">—</div></div><div class="speed-meter" id="clsMeter"><span id="clsBar" style="width:0%"></span></div></div>
      <div class="speed-tile"><div class="speed-row"><div>⚡ INP (ms)</div><div class="speed-val" id="inpVal">—</div></div><div class="speed-meter" id="inpMeter"><span id="inpBar" style="width:0%"></span></div></div>
      <div class="speed-tile"><div class="speed-row"><div>⏱️ TTFB (ms)</div><div class="speed-val" id="ttfbVal">—</div></div><div class="speed-meter" id="ttfbMeter"><span id="ttfbBar" style="width:0%"></span></div></div>
    </div>
    <div class="speed-suggestions">
      <h4>💡 Speed Suggestions</h4>
      <ul id="psiFixes"><li>Run Analyze to fetch PSI data.</li></ul>
    </div>
  </div>

  <div class="ground-slab seo-ground-card" style="padding: 0; margin-top: 20px;">
    <div class="section-header" style="padding: 18px 18px 0;">
      <span class="icon" style="--glow-color: var(--glow-purple);">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
      </span>
      <h3>Semantic SEO Ground</h3>
    </div>
    <div id="seoGround" class="accordion">
      </div>
  </div>

  <dialog id="improveModal" class="rounded-2xl p-0 w-[min(680px,95vw)]" style="border:none;border-radius:16px; background: transparent;">
    <div class="card">
      <div style="display:flex;align-items:start;justify-content:space-between;gap:10px">
        <h4 id="improveTitle" class="t-grad" style="font-weight:900;margin:0">Improve</h4>
        <form method="dialog"><button>X</button></form>
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px;margin-top:8px">
        <div class="card"><div style="font-size:12px;color:#94a3b8">Category</div><div id="improveCategory" style="font-weight:700">—</div></div>
        <div class="card">
          <div style="font-size:12px;color:#94a3b8">Score</div>
          <div style="display:flex;align-items:center;gap:8px;margin-top:6px">
            <span id="improveScore" class="score-pill">—</span>
            <span id="improveBand" class="pill">—</span>
          </div>
        </div>
        <a id="improveSearch" target="_blank" class="card" style="text-align:center;display:flex;align-items:center;justify-content:center;background:linear-gradient(90deg,rgba(255,72,122,.15),rgba(15,248,246,.15));border:1px solid #333;text-decoration:none">
          <span style="font-size:13px;color:#e5e7eb">Search guidance</span>
        </a>
      </div>
      <div style="margin-top:10px">
        <div style="font-size:12px;color:#94a3b8">Why this matters</div>
        <p id="improveWhy" style="font-size:14px;color:#e5e7eb;margin-top:6px">—</p>
      </div>
      <div style="margin-top:10px">
        <div style="font-size:12px;color:#94a3b8">How to improve</div>
        <ul id="improveTips" style="margin-top:8px;padding-left:18px;display:grid;gap:6px;font-size:14px;color:#e5e7eb"></ul>
      </div>
    </div>
  </dialog>

</section>
@endsection
