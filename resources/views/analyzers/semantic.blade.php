@extends('layouts.app')
@section('title','Semantic SEO Master — Analyzer')

@push('head')
<style>
  /* =============== Base page styles (unchanged) =============== */
  html,body{background:#03041c!important;color:#e5e7eb}
  .maxw{max-width:1150px;margin:0 auto;border:1px solid #154f2e;border-radius:18px;padding:8px}

  .title-wrap{display:flex;align-items:center;gap:14px;justify-content:center;margin-top:14px}
  .king{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:#101018;border:1px solid #ffffff24}
  .t-grad{background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;font-weight:900}
  .byline{font-size:14px;color:#cbd5e1}
  .shoail{display:inline-block;background:linear-gradient(90deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e);-webkit-background-clip:text;background-clip:text;color:transparent;background-size:400% 100%;animation:rainbowSlide 6s linear infinite,bob 3s ease-in-out infinite}
  @keyframes rainbowSlide{to{background-position:100% 50%}} @keyframes bob{0%,100%{transform:translateY(0)}50%{transform:translateY(-2px)}}

  .legend{display:flex;gap:10px;justify-content:center;margin:10px 0 6px}
  .legend .badge{padding:6px 10px;border-radius:9999px;font-weight:800;border:1px solid #ffffff2a;font-size:12px}
  .legend .g{background:#063f2c;color:#a7f3d0;border-color:#10b98166}
  .legend .o{background:#3b2a05;color:#fde68a;border-color:#f59e0b66}
  .legend .r{background:#3a0b0b;color:#fecaca;border-color:#ef444466}

  .card{border-radius:18px;padding:18px;background:#0a0a14;border:1px solid #ffffff1c}
  .cat-card{border-radius:16px;padding:16px;background:#111E2F;border:1px solid #ffffff1c}
  .ground-slab{border-radius:22px;padding:20px;background:#0D0E1E;border:1px solid #ffffff1c;margin-top:20px}

  .pill{padding:5px 10px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid #ffffff29;background:#ffffff14;color:#e5e7eb}
  .chip{padding:6px 8px;border-radius:12px;font-weight:800;display:inline-flex;align-items:center;gap:6px;border:1px solid #ffffff24;color:#eef2ff;font-size:12px}
  .chip i{font-style:normal}
  .chip.good{background:linear-gradient(135deg,#22c55e45,#10b98122);border-color:#22c55e72}
  .chip.warn{background:linear-gradient(135deg,#f59e0b45,#facc1522);border-color:#f59e0b72}
  .chip.bad{background:linear-gradient(135deg,#ef444445,#f8717122);border-color:#ef444472}

  .btn{padding:10px 14px;border-radius:12px;font-weight:900;border:1px solid #ffffff22;color:#0b1020;font-size:13px}
  .btn-green{background:#22c55e}.btn-blue{background:#3b82f6}.btn-orange{background:#f59e0b}.btn-purple{background:linear-gradient(90deg,#a78bfa,#f472b6);color:#19041a}
  .url-row{display:flex;align-items:center;gap:10px;border:1px solid #ffffff24;background:#0b0b12;border-radius:12px;padding:8px 10px}
  .url-row input{background:transparent;border:none;outline:none;color:#e5e7eb;width:100%}
  .url-row .paste{padding:6px 10px;border-radius:10px;border:1px solid #ffffff26;background:#ffffff10;color:#e5e7eb}

  .analyze-wrap{border-radius:16px;background:#020114;border:1px solid #ffffff20;padding:12px}

  /* ===================== Wheels (overall + readability + speed) ===================== */
  .mw{--v:0;--p:0;--ring:#22c55e;width:200px;height:200px;position:relative;filter:drop-shadow(0 10px 24px rgba(0,0,0,.35))}
  .mw-ring{position:absolute;inset:0;border-radius:50%;
    background:
      conic-gradient(from -90deg,
        #ef4444 0deg,
        #f59e0b 60deg,
        #eab308 120deg,
        #22c55e 180deg,
        #10b981 240deg,
        #22c55e 300deg,
        #06b6d4 330deg,
        #a78bfa 360deg);
    -webkit-mask:
      conic-gradient(from -90deg,#000 calc(var(--v)*1%), #0000 0),
      radial-gradient(circle 76px,transparent 72px,#000 72px);
    mask:
      conic-gradient(from -90deg,#000 calc(var(--v)*1%), #0000 0),
      radial-gradient(circle 76px,transparent 72px,#000 72px);
    box-shadow:
      0 0 0 6px #0d1f24 inset,
      0 0 28px rgba(34,197,94,.25),
      0 0 60px rgba(34,197,94,.15);
  }
  .mw-fill{position:absolute;inset:18px;border-radius:50%;overflow:hidden;background:#000}
  .mw-fill::after{content:"";position:absolute;left:0;right:0;height:100%;top:calc(100% - var(--p)*1%);transition:top .9s ease;
    background:linear-gradient(to top,#0ea5e9 0%,#22c55e 40%,#84cc16 60%,#facc15 85%,#f97316 100%);
    -webkit-mask:radial-gradient(105px 16px at 50% 0,#0000 98%,#000 100%);mask:radial-gradient(105px 16px at 50% 0,#0000 98%,#000 100%)}
  .mw-center{position:absolute;inset:0;display:grid;place-items:center;font-size:34px;font-weight:900;color:#fff;text-shadow:0 6px 22px rgba(0,0,0,.45)}
  .mw.good {filter:drop-shadow(0 0 10px rgba(34,197,94,.35)) drop-shadow(0 0 50px rgba(34,197,94,.25))}
  .mw.warn {filter:drop-shadow(0 0 10px rgba(245,158,11,.35)) drop-shadow(0 0 50px rgba(245,158,11,.25))}
  .mw.bad  {filter:drop-shadow(0 0 10px rgba(239,68,68,.35))  drop-shadow(0 0 50px rgba(239,68,68,.25))}
  .mw-sm{width:170px;height:170px}
  .mw-sm .mw-ring{-webkit-mask:
      conic-gradient(from -90deg,#000 calc(var(--v)*1%), #0000 0),
      radial-gradient(circle 64px,transparent 60px,#000 60px);
    mask:
      conic-gradient(from -90deg,#000 calc(var(--v)*1%), #0000 0),
      radial-gradient(circle 64px,transparent 60px,#000 60px)}
  .mw-sm .mw-fill{inset:14px}
  .mw-sm .mw-center{font-size:28px}

  .waterbox{position:relative;height:16px;border-radius:9999px;overflow:hidden;border:1px solid #ffffff22;background:#0b0b12}
  .waterbox .fill{position:absolute;inset:0;width:0%;transition:width .9s ease}
  .waterbox.good .fill{background:linear-gradient(90deg,#16a34a,#22c55e,#86efac)}
  .waterbox.warn .fill{background:linear-gradient(90deg,#f59e0b,#fbbf24,#fde68a)}
  .waterbox.bad  .fill{background:linear-gradient(90deg,#ef4444,#f87171,#fecaca)}
  .waterbox .label{position:absolute;inset:0;display:grid;place-items:center;font-weight:900;color:#e5e7eb;font-size:11px}

  .progress{width:100%;height:10px;border-radius:9999px;background:#ffffff14;overflow:hidden;border:1px solid #ffffff1a}
  .progress>span{display:block;height:100%;border-radius:9999px;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e);transition:width .5s ease}

  .check{display:flex;align-items:center;justify-content:space-between;border-radius:12px;padding:10px 12px;border:1px solid #ffffff1a;background:#0F1A29}
  .score-pill{padding:3px 7px;border-radius:10px;font-weight:800;background:#ffffff14;border:1px solid #ffffff22;color:#e5e7eb;font-size:12px}
  .score-pill--green{background:#10b9812e;border-color:#10b98166;color:#bbf7d0}
  .score-pill--orange{background:#f59e0b2e;border-color:#f59e0b66;color:#fde68a}
  .score-pill--red{background:#ef44442e;border-color:#ef444466;color:#fecaca}

  .improve-btn{padding:6px 9px;border-radius:10px;color:#0b1020;font-weight:800;border:1px solid transparent;transition:transform .08s ease;font-size:12px}
  .improve-btn:active{transform:translateY(1px)}
  .fill-green {background:linear-gradient(135deg,#16a34a,#22c55e,#86efac);color:#05240f}
  .fill-orange{background:linear-gradient(135deg,#f59e0b,#fbbf24,#fde68a);color:#3a2400}
  .fill-red   {background:linear-gradient(135deg,#ef4444,#f87171,#fecaca);color:#2f0606}
  .outline-green{border-color:#22c55edd!important;box-shadow:0 0 0 2px #22c55e8c inset,0 0 16px #22c55e55}
  .outline-orange{border-color:#f59e0bdd!important;box-shadow:0 0 0 2px #f59e0b8c inset,0 0 16px #f59e0b55}
  .outline-red{border-color:#ef4444dd!important;box-shadow:0 0 0 2px #ef44448c inset,0 0 16px #ef444455}

  dialog[open]{display:block} dialog::backdrop{background:rgba(0,0,0,.6)}
  #improveModal .card{background:#0D0E1E;border:1px solid #1b2640}
  #improveModal .card .card{background:#111E2F;border-color:#ffffff1c}

  #errorBox{display:none;margin-top:10px;border:1px solid #ef444466;background:#3a0b0b;color:#fecaca;border-radius:12px;padding:10px;white-space:pre-wrap;font-size:12px}

  /* ===================== Readability ===================== */
  .read-card{border-radius:20px;background:#0b0f1f;border:1px solid #17203e;padding:16px}
  .rb-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
  .rb-title{display:flex;align-items:center;gap:10px}
  .rb-title .ico{width:36px;height:36px;display:grid;place-items:center;border-radius:10px;background:linear-gradient(135deg,#22d3ee33,#a78bfa33);border:1px solid #ffffff22}
  .rb-legend{font-size:12px;color:#aab3c2}
  .rb-grid{display:grid;grid-template-columns:220px 1fr;gap:12px;margin-top:10px}
  @media (max-width:920px){.rb-grid{grid-template-columns:1fr}}
  .rb-tiles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px}
  @media (max-width:920px){.rb-tiles{grid-template-columns:1fr}}
  .rb-tile{background:#0f1830;border:1px solid #21325c;border-radius:14px;padding:12px}
  .rb-row{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#b6c2cf;margin:8px 0 6px}
  .rb-val{color:#e5e7eb;font-weight:800}
  .rb-meter{height:10px;border-radius:9999px;background:#0c1226;border:1px solid #1b2b51;overflow:hidden}
  .rb-meter>span{display:block;height:100%;width:0%;transition:width .9s ease;background:linear-gradient(90deg,#ef4444,#fde047,#22c55e)}

  /* Readability "Simple Fixes" — colorful */
  .rb-fixes{background:linear-gradient(135deg,#0ea5e966,#a78bfa33), radial-gradient(120% 120% at 10% 10%,#22d3ee22,transparent 60%);border:1px solid #1f3f7a;border-radius:14px;padding:14px;margin-top:12px;box-shadow:0 0 0 1px #1c2e57 inset,0 12px 32px rgba(0,0,0,.35)}
  .rb-fixes h4{margin:0 0 8px 0;font-weight:900}
  .rb-fixes ul{margin:0;padding-left:0;display:grid;gap:8px}
  .rb-fixes li{list-style:none;border:1px solid #2a3e83;background:linear-gradient(90deg,#1e40af33,#22d3ee22,#a78bfa22);padding:10px 12px;border-radius:12px;font-weight:700;color:#dbeafe;box-shadow:0 0 0 1px #1e3a8a inset}

  /* Readability banner */
  .rb-banner{margin-top:12px;border-radius:14px;padding:12px;font-weight:900;box-shadow:0 0 0 1px transparent inset,0 14px 32px rgba(0,0,0,.25)}
  .rb-banner.good{background:linear-gradient(90deg,#05240f,#0f5132);border:1px solid #126f3f;box-shadow:0 0 0 2px #126f3f66 inset,0 0 42px #22c55e33;color:#a7f3d0}
  .rb-banner.warn{background:linear-gradient(90deg,#3b2a05,#7a5d0d);border:1px solid #9a6a10;box-shadow:0 0 0 2px #9a6a1066 inset,0 0 42px #f59e0b33;color:#fde68a}
  .rb-banner.bad{ background:linear-gradient(90deg,#3a0b0b,#6f1d1d);border:1px solid #8a1a1a;box-shadow:0 0 0 2px #8a1a1a66 inset,0 0 42px #ef444433;color:#fecaca}

  /* ===================== Site Speed & CWV ===================== */
  .speed-card{border-radius:20px;background:#0b0f1f;border:1px solid #173a2a;padding:16px;margin-top:16px}
  .sp-head{display:flex;align-items:center;justify-content:space-between;gap:10px}
  .sp-title{display:flex;align-items:center;gap:10px}
  .sp-title .ico{width:36px;height:36px;display:grid;place-items:center;border-radius:10px;background:linear-gradient(135deg,#34d39933,#22d3ee33);border:1px solid #1a4c34}
  .sp-note{font-size:12px;color:#a9d3be}

  /* Wheels row CENTERED (above bars) */
  .sp-wheels{display:flex;justify-content:center;align-items:center;gap:18px;margin-top:12px;flex-wrap:wrap}
  .wheel-card{display:grid;place-items:center;border-radius:16px;padding:10px;background:#07161a;border:1px solid #12373f;position:relative;box-shadow:0 0 0 1px #0b2a2f inset,0 8px 28px rgba(0,0,0,.35);width:220px}
  .wheel-label{font-size:12px;color:#a6c5cf;margin-top:6px}

  /* Bars */
  .sp-grid{display:grid;grid-template-columns:1fr;gap:14px;margin-top:10px}
  .sp-tile{background:#0e1a22;border:1px solid #1d3641;border-radius:14px;padding:12px}
  .sp-row{display:flex;align-items:center;justify-content:space-between;font-size:12px;color:#a6c5cf;margin:6px 0}
  .sp-val{color:#e5e7eb;font-weight:800}
  .sp-meter{height:12px;border-radius:9999px;background:#0b1417;border:1px solid #16414e;overflow:hidden;position:relative}
  .sp-meter>span{display:block;height:100%;width:0%;transition:width .9s ease;background:linear-gradient(90deg,#ef4444,#f59e0b,#22c55e)}
  .sp-meter::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(45deg,#ffffff0a 0 8px,#ffffff06 8px 16px);pointer-events:none}
  .sp-meter.good{box-shadow:0 0 0 1px #1b5e2f inset,0 0 24px #22c55e33}
  .sp-meter.warn{box-shadow:0 0 0 1px #8a5a12 inset,0 0 24px #f59e0b33}
  .sp-meter.bad {box-shadow:0 0 0 1px #6f1616 inset,0 0 24px #ef444433}
</style>

<script defer>
/* Robust init so the Analyze button always works even if scripts load after DOM ready */
(function(){
  const init = () => {
    const $ = s=>document.querySelector(s);

    /* ============== Element refs ============== */
    const mw=$('#mw'), mwRing=$('#mwRing'), mwFill=$('#mwFill'), mwNum=$('#mwNum');
    const overallBar=$('#overallBar'), overallFill=$('#overallFill'), overallPct=$('#overallPct');
    const chipOverall=$('#chipOverall'), chipContent=$('#chipContent'), chipWriter=$('#chipWriter'), chipHuman=$('#chipHuman'), chipAI=$('#chipAI');

    const urlInput=$('#urlInput'), analyzeBtn=$('#analyzeBtn'), pasteBtn=$('#pasteBtn'),
          importBtn=$('#importBtn'), importFile=$('#importFile'), printBtn=$('#printBtn'),
          resetBtn=$('#resetBtn'), exportBtn=$('#exportBtn');

    const statF=$('#statFlesch'), statG=$('#statGrade'), statInt=$('#statInt'), statExt=$('#statExt'), statRatio=$('#statRatio');
    const titleVal=$('#titleVal'), metaVal=$('#metaVal'), headingMap=$('#headingMap'), recsEl=$('#recs'), catsEl=$('#cats');

    const chipHttp=$('#chipHttp'), chipTitle=$('#chipTitle'), chipMeta=$('#chipMeta'),
          chipCanon=$('#chipCanon'), chipRobots=$('#chipRobots'), chipViewport=$('#chipViewport'),
          chipH=$('#chipH'), chipIntChip=$('#chipInt'), chipSchema=$('#chipSchema'), chipAuto=$('#chipAuto');

    const errorBox = $('#errorBox');

    const modal=$('#improveModal'), mTitle=$('#improveTitle'), mCat=$('#improveCategory'),
          mScore=$('#improveScore'), mBand=$('#improveBand'), mWhy=$('#improveWhy'),
          mTips=$('#improveTips'), mLink=$('#improveSearch');

    /* Readability UI */
    const readMw=$('#readMw'), readRing=$('#readRing'), readFill=$('#readFill'), readNum=$('#readNum');
    const readBadge=$('#readBadge'), gradeBadge=$('#gradeBadge'), rbLegend=$('#rbLegend');
    const rbFleschVal=$('#rbFleschVal'), rbFleschFill=$('#rbFleschFill');
    const rbASLVal=$('#rbASLVal'), rbASLFill=$('#rbASLFill');
    const rbWordsVal=$('#rbWordsVal'), rbWordsFill=$('#rbWordsFill');
    const rbSyllVal=$('#rbSyllVal'), rbSyllFill=$('#rbSyllFill');
    const rbTTRVal=$('#rbTTRVal'), rbTTRFill=$('#rbTTRFill');
    const rbTriVal=$('#rbTriVal'), rbTriFill=$('#rbTriFill');
    const rbDigitsVal=$('#rbDigitsVal'), rbDigitsFill=$('#rbDigitsFill');
    const rbPassiveVal=$('#rbPassiveVal'), rbPassiveFill=$('#rbPassiveFill');
    const rbSimpleVal=$('#rbSimpleVal'), rbSimpleFill=$('#rbSimpleFill');
    const rbFixes=$('#rbFixes'), rbBanner=$('#rbBanner');

    /* Speed UI */
    const mwMobile=$('#mwMobile'), ringMobile=$('#ringMobile'), fillMobile=$('#fillMobile'), numMobile=$('#numMobile');
    const mwDesktop=$('#mwDesktop'), ringDesktop=$('#ringDesktop'), fillDesktop=$('#fillDesktop'), numDesktop=$('#numDesktop');
    const lcpVal=$('#lcpVal'), lcpBar=$('#lcpBar'), lcpMeter=$('#lcpMeter');
    const clsVal=$('#clsVal'), clsBar=$('#clsBar'), clsMeter=$('#clsMeter');
    const inpVal=$('#inpVal'), inpBar=$('#inpBar'), inpMeter=$('#inpMeter');
    const ttfbVal=$('#ttfbVal'), ttfbBar=$('#ttfbBar'), ttfbMeter=$('#ttfbMeter');
    const psiStatus=$('#psiStatus'), psiFixes=$('#psiFixes');

    /* Helpers */
    const clamp01=n=>Math.max(0,Math.min(100,Number(n)||0));
    const bandName=s=>s>=80?'good':(s>=60?'warn':'bad');
    const bandIcon=s=>s>=80?'✅':(s>=60?'🟧':'🔴');
    function setChip(el,label,value,score){ if(!el)return; el.classList.remove('good','warn','bad'); const b=bandName(score); el.classList.add(b); el.innerHTML=`<i>${bandIcon(score)}</i><span>${label}: ${value}</span>`; };
    const showError=(msg,detail)=>{ errorBox.style.display='block'; errorBox.textContent=msg+(detail?`\n\n${detail}`:''); };
    const clearError=()=>{ errorBox.style.display='none'; errorBox.textContent=''; };

    /* ===== Category/KB/scoring (unchanged logic) ===== */
    const CATS=[{name:'User Signals & Experience',icon:'📱',checks:['Mobile-friendly, responsive layout','Optimized speed (compression, lazy-load)','Core Web Vitals passing (LCP/INP/CLS)','Clear CTAs and next steps','Accessible basics (alt text, contrast)']},{name:'Entities & Context',icon:'🧩',checks:['sameAs/Organization details present','Valid schema markup (Article/FAQ/Product)','Related entities covered with context','Primary entity clearly defined','Organization contact/about page visible']},{name:'Structure & Architecture',icon:'🏗️',checks:['Logical H2/H3 headings & topic clusters','Internal links to hub/related pages','Clean, descriptive URL slug','Breadcrumbs enabled (+ schema)','XML sitemap logical structure']},{name:'Content Quality',icon:'🧠',checks:['E-E-A-T signals (author, date, expertise)','Unique value vs. top competitors','Facts & citations up to date','Helpful media (images/video) w/ captions','Up-to-date examples & screenshots']},{name:'Content & Keywords',icon:'📝',checks:['Define search intent & primary topic','Map target & related keywords (synonyms/PAA)','H1 includes primary topic naturally','Integrate FAQs / questions with answers','Readable, NLP-friendly language']},{name:'Technical Elements',icon:'⚙️',checks:['Title tag (≈50–60 chars) w/ primary keyword','Meta description (≈140–160 chars) + CTA','Canonical tag set correctly','Indexable & listed in XML sitemap','Robots directives valid']}];
    const KB={'Mobile-friendly, responsive layout':{why:'Most traffic is mobile; poor UX kills engagement.',tips:['Responsive breakpoints & fluid grids.','Tap targets ≥44px.','Avoid horizontal scroll.'],link:'https://search.google.com/test/mobile-friendly'},'Optimized speed (compression, lazy-load)':{why:'Speed affects abandonment and CWV.',tips:['Use WebP/AVIF.','HTTP/2 + CDN caching.','Lazy-load below-the-fold.'],link:'https://web.dev/fast/'},'Core Web Vitals passing (LCP/INP/CLS)':{why:'Passing CWV improves experience & stability.',tips:['Preload hero image.','Minimize long JS tasks.','Reserve media space.'],link:'https://web.dev/vitals/'},'Clear CTAs and next steps':{why:'Clarity increases conversions and task completion.',tips:['One primary CTA per view.','Action verbs + benefit.','Explain what happens next.'],link:'https://www.nngroup.com/articles/call-to-action-buttons/'},'Accessible basics (alt text, contrast)':{why:'Accessibility broadens reach and reduces risk.',tips:['Alt text on images.','Contrast ratio ≥4.5:1.','Keyboard focus states.'],link:'https://www.w3.org/WAI/standards-guidelines/wcag/'},'sameAs/Organization details present':{why:'Entity grounding disambiguates your brand.',tips:['Organization JSON-LD.','sameAs links to profiles.','NAP consistency.'],link:'https://schema.org/Organization'},'Valid schema markup (Article/FAQ/Product)':{why:'Structured data unlocks rich results.',tips:['Validate with Rich Results Test.','Mark up visible content only.','Keep to supported types.'],link:'https://search.google.com/test/rich-results'},'Related entities covered with context':{why:'Covering related entities builds topical depth.',tips:['Mention related concepts.','Explain relationships.','Link to references.'],link:'https://developers.google.com/knowledge-graph'},'Primary entity clearly defined':{why:'A single main entity clarifies page purpose.',tips:['Define at the top.','Use consistent naming.','Add schema about it.'],link:'https://developers.google.com/search/docs/appearance/structured-data/intro-structured-data'},'Organization contact/about page visible':{why:'Trust & contact clarity support E-E-A-T.',tips:['Add /about and /contact.','Link from header/footer.','Show address & email.'],link:'https://developers.google.com/search/docs/fundamentals/creating-helpful-content'},'Logical H2/H3 headings & topic clusters':{why:'Hierarchy helps skimming and indexing.',tips:['Group subtopics under H2.','Use H3 for steps/examples.','Keep sections concise.'],link:'https://moz.com/learn/seo/site-structure'},'Internal links to hub/related pages':{why:'Internal links distribute authority & context.',tips:['Link to 3–5 relevant hubs.','Descriptive anchors.','Further reading section.'],link:'https://ahrefs.com/blog/internal-links/'},'Clean, descriptive URL slug':{why:'Readable slugs improve CTR & clarity.',tips:['3–5 meaningful words.','Hyphens & lowercase.','Avoid query strings.'],link:'https://developers.google.com/search/docs/crawling-indexing/url-structure'},'Breadcrumbs enabled (+ schema)':{why:'Breadcrumbs clarify location & show in SERP.',tips:['Visible breadcrumbs.','BreadcrumbList JSON-LD.','Keep depth logical.'],link:'https://developers.google.com/search/docs/appearance/structured-data/breadcrumb'},'XML sitemap logical structure':{why:'Sitemap accelerates discovery & updates.',tips:['Include canonical URLs.','Segment large sites.','Reference in robots.txt.'],link:'https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview'},'E-E-A-T signals (author, date, expertise)':{why:'Trust signals reduce bounce & build credibility.',tips:['Author bio + credentials.','Last updated date.','Editorial policy page.'],link:'https://developers.google.com/search/blog/2022/08/helpful-content-update'},'Unique value vs. top competitors':{why:'Differentiation is necessary to rank & retain.',tips:['Original data/examples.','Pros/cons & criteria.','Why your approach is better.'],link:'https://backlinko.com/seo-techniques'},'Facts & citations up to date':{why:'Freshness + accuracy boosts trust.',tips:['Cite primary sources.','Update stats ≤12 months.','Prefer canonical/DOI links.'],link:'https://scholar.google.com/'},'Helpful media (images/video) w/ captions':{why:'Media improves comprehension & dwell time.',tips:['Add 3–6 figures.','Descriptive captions.','Compress + lazy-load.'],link:'https://web.dev/optimize-lcp/'},'Up-to-date examples & screenshots':{why:'Current visuals reflect product reality.',tips:['Refresh UI shots.','Date your examples.','Replace deprecated flows.'],link:'https://www.nngroup.com/articles/guidelines-for-screenshots/'},'Define search intent & primary topic':{why:'Matching intent drives relevance & time on page.',tips:['State outcome early.','Align format to intent.','Use concrete examples.'],link:'https://ahrefs.com/blog/search-intent/'},'Map target & related keywords (synonyms/PAA)':{why:'Variants improve recall & completeness.',tips:['List 6–12 variants.','5–10 PAA questions.','Answer PAA in 40–60 words.'],link:'https://developers.google.com/search/docs/fundamentals/seo-starter-guide'},'H1 includes primary topic naturally':{why:'Clear topic helps users and algorithms.',tips:['One H1 per page.','Topic near the start.','Be descriptive.'],link:'https://web.dev/learn/html/semantics/#headings'},'Integrate FAQs / questions with answers':{why:'Captures long-tail & can earn rich results.',tips:['Pick 3–6 questions.','Answer briefly.','Add FAQPage JSON-LD.'],link:'https://developers.google.com/search/docs/appearance/structured-data/faqpage'},'Readable, NLP-friendly language':{why:'Plain, direct writing improves comprehension.',tips:['≤20 words/sentence.','Active voice.','Define jargon on first use.'],link:'https://www.plainlanguage.gov/guidelines/'},'Title tag (≈50–60 chars) w/ primary keyword':{why:'Title remains the strongest on-page signal.',tips:['50–60 chars.','Primary topic first.','Avoid duplication.'],link:'https://moz.com/learn/seo/title-tag'},'Meta description (≈140–160 chars) + CTA':{why:'Meta drives CTR which correlates with rankings.',tips:['140–160 chars.','Benefit + CTA.','Match intent.'],link:'https://moz.com/learn/seo/meta-description'},'Canonical tag set correctly':{why:'Avoid duplicates; consolidate signals.',tips:['One canonical.','Absolute URL.','No conflicting canonicals.'],link:'https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls'},'Indexable & listed in XML sitemap':{why:'Indexation is prerequisite to ranking.',tips:['No noindex.','Include in sitemap.','Submit in Search Console.'],link:'https://developers.google.com/search/docs/crawling-indexing/overview'},'Robots directives valid':{why:'Avoid accidental noindex/nofollow.',tips:['robots meta allows indexing.','robots.txt not blocking.','Use directives consistently.'],link:'https://developers.google.com/search/docs/crawling-indexing/robots-meta-tag'}};

    function clamp01num(n){return Math.max(0,Math.min(100,Number(n)||0))}
    function scoreChecklist(label,data,url,targetKw=''){const qs=data.quick_stats||{};const cs=data.content_structure||{};const ps=data.page_signals||{};const r=data.readability||{};const h1=(cs.headings&&cs.headings.H1?cs.headings.H1.length:0)||0;const h2=(cs.headings&&cs.headings.H2?cs.headings.H2.length:0)||0;const h3=(cs.headings&&cs.headings.H3?cs.headings.H3.length:0)||0;const title=(cs.title||'');const meta=(cs.meta_description||'');const internal=Number(qs.internal_links||0);const external=Number(qs.external_links||0);const schemaTypes=new Set((data.page_signals?.schema_types)||[]);const robots=(data.page_signals?.robots||'').toLowerCase();const hasFAQ=schemaTypes.has('FAQPage');const hasArticle=schemaTypes.has('Article')||schemaTypes.has('NewsArticle')||schemaTypes.has('BlogPosting');const urlPath=(()=>{try{return new URL(url).pathname;}catch{return '/';}})();const slugScore=(()=>{const hasQuery=url.includes('?');const segs=urlPath.split('/').filter(Boolean);const words=segs.join('-').split('-').filter(Boolean);if(hasQuery)return 55;if(segs.length>6)return 60;if(words.some(w=>w.length>24))return 65;return 85;})();switch(label){case'Mobile-friendly, responsive layout':return ps.has_viewport?88:58;case'Optimized speed (compression, lazy-load)':return 60;case'Core Web Vitals passing (LCP/INP/CLS)':return 60;case'Clear CTAs and next steps':return meta.length>=140&&/learn|get|try|start|buy|sign|download|contact/i.test(meta)?80:60;case'Accessible basics (alt text, contrast)':return (data.images_alt_count||0)>=3?82:((data.images_alt_count||0)>=1?68:48);case'sameAs/Organization details present':return ps.has_org_sameas?90:55;case'Valid schema markup (Article/FAQ/Product)':return (hasArticle||hasFAQ||schemaTypes.has('Product'))?85:(schemaTypes.size>0?70:50);case'Related entities covered with context':return external>=2?72:60;case'Primary entity clearly defined':return ps.has_main_entity?85:(h1>0?72:58);case'Organization contact/about page visible':return 60;case'Logical H2/H3 headings & topic clusters':return (h2>=3&&h3>=2)?85:(h2>=2?70:55);case'Internal links to hub/related pages':return internal>=5?85:(internal>=2?65:45);case'Clean, descriptive URL slug':return slugScore;case'Breadcrumbs enabled (+ schema)':return ps.has_breadcrumbs?85:55;case'XML sitemap logical structure':return 60;case'E-E-A-T signals (author, date, expertise)':return ps.has_org_sameas?75:65;case'Unique value vs. top competitors':return 60;case'Facts & citations up to date':return external>=2?78:58;case'Helpful media (images/video) w/ captions':return (data.images_alt_count||0)>=3?82:58;case'Up-to-date examples & screenshots':return 60;case'Define search intent & primary topic':return (title&&h1>0)?78:60;case'Map target & related keywords (synonyms/PAA)':{const kw=(targetKw||'').trim();if(!kw)return 60;const found=(title.toLowerCase().includes(kw.toLowerCase())||(cs.headings?.H1||[]).join(' || ').toLowerCase().includes(kw.toLowerCase()));return found?80:62}case'H1 includes primary topic naturally':{const kw=(targetKw||'').trim();if(h1===0)return 45;if(!kw)return 72;const found=(cs.headings?.H1||[]).some(h=>h.toLowerCase().includes(kw.toLowerCase()));return found?84:72}case'Integrate FAQs / questions with answers':return hasFAQ?85:(/(faq|questions?)/i.test((cs.headings?.H2||[]).join(' ')+' '+(cs.headings?.H3||[]).join(' '))?70:55);case'Readable, NLP-friendly language':return clamp01num(r.score||0);case'Title tag (≈50–60 chars) w/ primary keyword':{const len=(title||'').length;return (len>=50&&len<=60)?88:(len?68:45)}case'Meta description (≈140–160 chars) + CTA':{const len=(meta||'').length;const hasCTA=/learn|get|try|start|buy|sign|download|contact/i.test(meta||'');return (len>=140&&len<=160)?(hasCTA?90:82):(len?65:48)}case'Canonical tag set correctly':return ps.canonical?85:55;case'Indexable & listed in XML sitemap':return robots.includes('noindex')?20:80;case'Robots directives valid':return (robots&&/(noindex|none)/.test(robots))?45:75;}return 60}

    function renderCategories(data,url,targetKw){const catsEl=document.querySelector('#cats');catsEl.innerHTML='';let autoGood=0;CATS.forEach(cat=>{const rows=cat.checks.map(lbl=>{const s=scoreChecklist(lbl,data,url,targetKw);const fill=s>=80?'fill-green':(s>=60?'fill-orange':'fill-red');const pill=s>=80?'score-pill--green':s>=60?'score-pill--orange':'score-pill--red';if(s>=80)autoGood++;return {label:lbl,score:s,fill,pill,bandTxt:(s>=80?'Good (≥80)':s>=60?'Needs work (60–79)':'Low (<60)')};});const total=rows.length;const passed=rows.filter(r=>r.score>=80).length;const pct=Math.round((passed/Math.max(1,total))*100);const card=document.createElement('div');card.className='cat-card';card.innerHTML=`<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px"><div style="display:flex;align-items:center;gap:8px"><div class="king" style="width:34px;height:34px">${cat.icon}</div><div><div class="t-grad" style="font-size:16px;font-weight:900">${cat.name}</div><div style="font-size:12px;color:#b6c2cf">Keep improving</div></div></div><div class="pill">${passed} / ${total}</div></div><div class="progress" style="margin-bottom:8px"><span style="width:${pct}%"></span></div><div class="space-y-2" id="list"></div>`;const list=card.querySelector('#list');rows.forEach(row=>{const dot=row.score>=80?'#10b981':row.score>=60?'#f59e0b':'#ef4444';const el=document.createElement('div');el.className='check';el.innerHTML=`<div style="display:flex;align-items:center;gap:8px"><span style="display:inline-block;width:10px;height:10px;border-radius:9999px;background:${dot}"></span><div class="font-semibold" style="font-size:13px">${row.label}</div></div><div style="display:flex;align-items:center;gap:6px"><span class="score-pill ${row.pill}">${row.score}</span><button class="improve-btn ${row.fill}" type="button">Improve</button></div>`;el.querySelector('.improve-btn').addEventListener('click',()=>{const kb=KB[row.label]||{why:'This item impacts relevance and UX.',tips:['Aim for ≥80 and re-run the analyzer.'],link:'https://www.google.com'};mTitle.textContent=row.label;mCat.textContent=cat.name;mScore.textContent=row.score;mBand.textContent=row.bandTxt;mBand.className='pill '+(row.score>=80?'score-pill--green':row.score>=60?'score-pill--orange':'score-pill--red');mWhy.textContent=kb.why;mTips.innerHTML='';(kb.tips||[]).forEach(t=>{const li=document.createElement('li');li.textContent=t;mTips.appendChild(li)});mLink.href=kb.link||('https://www.google.com/search?q='+encodeURIComponent(row.label+' best practices'));if(typeof modal.showModal==='function')modal.showModal();else modal.setAttribute('open','')});list.appendChild(el)});catsEl.appendChild(card)});chipAuto.textContent=autoGood;}

    /* API (unchanged) */
    async function callAnalyzer(url){const headers={'Accept':'application/json','Content-Type':'application/json'};let res=await fetch('/api/semantic-analyze',{method:'POST',headers,body:JSON.stringify({url,target_keyword:''})});if(res.ok)return res.json();if([404,405,419].includes(res.status)){res=await fetch('/semantic-analyzer/analyze',{method:'POST',headers:{...headers,'X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url,target_keyword:''})});if(res.ok)return res.json()}const txt=await res.text();throw new Error(`HTTP ${res.status}\n${txt?.slice(0,800)}`)}
    async function callPSI(url){const res=await fetch('/semantic-analyzer/psi',{method:'POST',headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},body:JSON.stringify({url})});const text=await res.text();let json={};try{json=JSON.parse(text)}catch{throw new Error(`PSI: invalid JSON\n${text?.slice(0,400)}`)}if(json.ok===false){throw new Error(json.error||json.message||'PSI unavailable')}if(!res.ok){throw new Error(json.error||json.message||`PSI HTTP ${res.status}`)}return json}
    function setRunning(isOn){if(!analyzeBtn)return;analyzeBtn.disabled=isOn;analyzeBtn.style.opacity=isOn?.6:1;analyzeBtn.textContent=isOn?'Analyzing…':'🔍 Analyze'}

    /* Readability meters */
    const pct=(v,min,max,invert=false)=>{if(v===null||v===undefined||isNaN(v))return 0;let p=(v-min)/Math.max(1,(max-min))*100;p=Math.max(0,Math.min(100,p));return invert?100-p:p};
    function setMeter(fillEl,valEl,value,display,range,invert=false){if(!fillEl||!valEl)return;valEl.textContent=(value===null||value===undefined||Number.isNaN(value))?'—':display;fillEl.style.width=pct(Number(value),range[0],range[1],invert)+'%'}

    function buildFixes(r){const fixes=[];if(typeof r.avg_sentence_len==='number'&&r.avg_sentence_len>20)fixes.push('Break long sentences into 12–16 words.');if(typeof r.simple_words_ratio==='number'&&r.simple_words_ratio<80)fixes.push('Prefer shorter, simpler words (use clearer synonyms).');if(typeof r.passive_ratio==='number'&&r.passive_ratio>15)fixes.push('Reduce passive voice; rewrite in active voice.');if(typeof r.repetition_trigram==='number'&&r.repetition_trigram>10)fixes.push('Trim repeated phrases; vary wording and examples.');if(typeof r.digits_per_100w==='number'&&r.digits_per_100w>10)fixes.push('Reduce numeric density; round or group numbers where possible.');if(fixes.length<3){fixes.push('Add headings and bullets to chunk information.');fixes.push('Use image captions to explain visuals succinctly.');fixes.push('Front-load key points; keep paragraphs 2–4 lines.')}rbFixes.innerHTML=fixes.slice(0,5).map(x=>`<li>✅ ${x}</li>`).join('')}

    /* Speed helpers */
    const band = s => s>=80?'good':(s>=60?'warn':'bad');
    const scoreFromBounds=(val,good,poor)=>{if(val==null||isNaN(val))return 0;if(val<=good)return 100;if(val>=poor)return 0;return Math.round(100*(1-((val-good)/(poor-good))))};
    function setWheel(elRing,elFill,elNum,container,score,prefix){const b=band(score);container.classList.remove('good','warn','bad');container.classList.add(b);elRing.style.setProperty('--v',score);elFill.style.setProperty('--p',score);elNum.textContent=(prefix?prefix+' ':'')+score+'%'}
    function setSpMeter(barEl,valEl,raw,score,fmt,meterWrap){valEl.textContent=raw==null?'—':(fmt?fmt(raw):raw);barEl.style.width=clamp01(score)+'%';if(meterWrap){meterWrap.classList.remove('good','warn','bad');meterWrap.classList.add(band(score));}}

    /* ===== Analyze ===== */
    $('#analyzeBtn')?.addEventListener('click', async e=>{
      e.preventDefault();
      clearError();
      const url=(urlInput.value||'').trim();
      if(!url){showError('Please enter a URL.');return;}
      try{
        setRunning(true);

        // reset speed
        psiStatus.textContent='Checking…';
        [ringMobile,ringDesktop].forEach(el=>el.style.setProperty('--v',0));
        [fillMobile,fillDesktop].forEach(el=>el.style.setProperty('--p',0));
        [mwMobile,mwDesktop].forEach(c=>{c.classList.remove('good','warn','bad');c.classList.add('warn')});
        numMobile.textContent='M 0%';numDesktop.textContent='D 0%';
        [lcpBar,clsBar,inpBar,ttfbBar].forEach(el=>el.style.width='0%');
        [lcpVal,clsVal,inpVal,ttfbVal].forEach(el=>el.textContent='—');
        psiFixes.innerHTML='<li>Fetching PageSpeed data…</li>';

        const data=await callAnalyzer(url);
        if(!data||data.error) throw new Error(data?.error||'Unknown error');
        window.__lastData={...data,url};

        const score=clamp01(data.overall_score||0), bname=bandName(score);
        mw?.classList.remove('good','warn','bad');mw?.classList.add(bname);
        mwRing?.style.setProperty('--v',score);mwFill?.style.setProperty('--p',score);
        mwNum.textContent=score+'%';
        overallBar?.classList.remove('good','warn','bad');overallBar?.classList.add(bname);
        overallFill.style.width=score+'%';overallPct.textContent=score+'%';
        setChip(chipOverall,'Overall',`${score} /100`,score);

        const cmap={};(data.categories||[]).forEach(c=>cmap[c.name]=c.score??0);
        const contentScore=Math.round(([cmap['Content & Keywords'],cmap['Content Quality']].filter(v=>typeof v==='number').reduce((a,b)=>a+b,0))/2||0);
        setChip(chipContent,'Content',`${contentScore} /100`,contentScore);

        const r=data.readability||{};
        const human=clamp01(Math.round(70+(r.score||0)/5-(r.passive_ratio||0)/3));
        const ai=clamp01(100-human);
        setChip(chipWriter,'Writer',human>=60?'Likely Human':'Possibly AI',human);
        setChip(chipHuman,'Human-like',`${human} %`,human);
        setChip(chipAI,'AI-like',`${ai} %`,100-human);

        statF.textContent=r.flesch??'—';statG.textContent='Grade '+(r.grade??'—');
        statInt.textContent=data.quick_stats?.internal_links??0;
        statExt.textContent=data.quick_stats?.external_links??0;
        statRatio.textContent=(data.quick_stats?.text_to_html_ratio??0)+'%';

        const rs=clamp01(r.score||0), rBand=bandName(rs);
        readMw?.classList.remove('good','warn','bad');readMw?.classList.add(rBand);
        readRing?.style.setProperty('--v',rs);readFill?.style.setProperty('--p',rs);
        readNum.textContent=rs+'%';
        readBadge.textContent=rs>=80?'Very Easy To Read':(rs>=60?'Good — Needs More Improvement':'Needs Improvement in Content');
        readBadge.className='pill '+(rs>=80?'score-pill--green':rs>=60?'score-pill--orange':'score-pill--red');
        const grade=(typeof r.grade==='number')?r.grade:null;
        gradeBadge.textContent='Grade '+(grade??'—');
        rbLegend.textContent=(r.language==='non-latin'?'Non-Latin content (LIX-based) — العربية/others supported':'Latin-like content — English & similar');
        setMeter(rbFleschFill,rbFleschVal,r.flesch,(r.flesch??'—'),[0,100],false);
        setMeter(rbASLFill,rbASLVal,r.avg_sentence_len,(r.avg_sentence_len??'—'),[10,30],true);
        setMeter(rbWordsFill,rbWordsVal,r.word_count,(r.word_count??'—'),[0,2000],false);
        setMeter(rbSyllFill,rbSyllVal,r.avg_syllables_per_word,(r.avg_syllables_per_word??'—'),[1.2,2.2],true);
        setMeter(rbTTRFill,rbTTRVal,r.ttr,((r.ttr!=null?r.ttr+'%':'—')),[0,100],false);
        setMeter(rbTriFill,rbTriVal,r.repetition_trigram,((r.repetition_trigram!=null?r.repetition_trigram+'%':'—')),[0,20],true);
        setMeter(rbDigitsFill,rbDigitsVal,r.digits_per_100w,(r.digits_per_100w??'—'),[0,20],true);
        setMeter(rbPassiveFill,rbPassiveVal,r.passive_ratio,((r.passive_ratio!=null?r.passive_ratio+'%':'—')),[0,30],true);
        setMeter(rbSimpleFill,rbSimpleVal,r.simple_words_ratio,((r.simple_words_ratio!=null?r.simple_words_ratio+'%':'—')),[60,100],false);
        if(grade!==null){const bandCl=grade<=7?'good':(grade<=10?'warn':'bad');rbBanner.className='rb-banner '+bandCl;rbBanner.textContent=grade<=7?`Easy to read (Grade ${grade}). Clear and accessible.`:grade<=10?`Good for general audiences (Grade ${grade}).`:`Complex reading level (Grade ${grade}). Use shorter sentences and simpler vocabulary.`}
        buildFixes(r);

        titleVal.textContent=data.content_structure?.title||'—';
        metaVal.textContent=data.content_structure?.meta_description||'—';
        const hs=data.content_structure?.headings||{};
        chipH.textContent=`H1:${(hs.H1||[]).length} • H2:${(hs.H2||[]).length} • H3:${(hs.H3||[]).length}`;
        headingMap.innerHTML='';
        Object.entries(hs).forEach(([lvl,arr])=>{if(!arr||!arr.length)return;const box=document.createElement('div');box.className='card';box.innerHTML=`<div style="font-size:12px;color:#b6c2cf;margin-bottom:6px" class="uppercase">${lvl}</div>`+arr.map(t=>`<div>• ${t}</div>`).join('');headingMap.appendChild(box)});

        chipHttp.textContent='200';
        chipCanon.textContent=(data.page_signals?.canonical||'—')||'—';
        chipRobots.textContent=(data.page_signals?.robots||'—')||'—';
        chipViewport.textContent=data.page_signals?.has_viewport?'yes':'—';
        chipIntChip.textContent=data.quick_stats?.internal_links??0;
        chipSchema.textContent=(data.page_signals?.schema_types||[]).length;

        recsEl.innerHTML='';
        (data.recommendations||[]).forEach(rec=>{const d=document.createElement('div');d.className='card';d.innerHTML=`<span class="pill" style="margin-right:6px">${rec.severity}</span>${rec.text}`;recsEl.appendChild(d)});

        renderCategories(data,url,'');

        try{
          const psi=await callPSI(url);
          const mobile=psi.mobile||{};const desktop=psi.desktop||{};
          const mScore=clamp01(Math.round(mobile.score??mobile.performance??0));
          const dScore=clamp01(Math.round(desktop.score??desktop.performance??0));
          setWheel(ringMobile,fillMobile,numMobile,mwMobile,mScore,'M');
          setWheel(ringDesktop,fillDesktop,numDesktop,mwDesktop,dScore,'D');

          const pick=(...vals)=>{for(const v of vals){const n=Number(v);if(v!==undefined&&v!==null&&!Number.isNaN(n))return n}return null};
          const lcpSeconds=(()=>{const sec=pick(mobile.lcp_s,desktop.lcp_s,psi.lcp_s,psi.metrics?.lcp_s);if(sec!==null)return sec;const ms=pick(mobile.lcp,desktop.lcp,psi.lcp,psi.metrics?.lcp);return ms!==null?ms/1000:null})();
          const cls=pick(mobile.cls,desktop.cls,psi.cls,psi.metrics?.cls);
          const inp=pick(mobile.inp_ms,desktop.inp_ms,psi.inp_ms,psi.metrics?.inp_ms,mobile.inp,desktop.inp,psi.inp);
          const ttfb=pick(mobile.ttfb_ms,desktop.ttfb_ms,psi.ttfb_ms,psi.metrics?.ttfb_ms,psi.ttfb);

          const sLCP=scoreFromBounds(lcpSeconds,2.5,6.0);
          const sCLS=scoreFromBounds(cls,0.10,0.25);
          const sINP=scoreFromBounds(inp,200,500);
          const sTTFB=scoreFromBounds(ttfb,800,1800);

          setSpMeter(lcpBar,lcpVal,lcpSeconds,sLCP,v=>v!=null?`${v.toFixed(2)} s`:'—',lcpMeter);
          setSpMeter(clsBar,clsVal,cls,sCLS,v=>v!=null?`${v.toFixed(3)}`:'—',clsMeter);
          setSpMeter(inpBar,inpVal,inp,sINP,v=>v!=null?`${Math.round(v)} ms`:'—',inpMeter);
          setSpMeter(ttfbBar,ttfbVal,ttfb,sTTFB,v=>v!=null?`${Math.round(v)} ms`:'—',ttfbMeter);

          const tips=[];
          if(lcpSeconds!=null&&lcpSeconds>2.5)tips.push('Improve LCP: preload hero image, compress images (AVIF/WebP), inline critical CSS.');
          if(cls!=null&&cls>0.1)tips.push('Reduce CLS: always set width/height on images/media; avoid layout shifts from ads and embeds.');
          if(inp!=null&&inp>200)tips.push('Lower INP: break up long tasks, defer non-critical JS, reduce third-party scripts.');
          if(ttfb!=null&&ttfb>800)tips.push('Reduce TTFB: enable caching/CDN, optimize server, use HTTP/2 or HTTP/3.');
          if(!tips.length){tips.push('Great job! Keep images optimized and JS lean to maintain fast performance.')}
          psiFixes.innerHTML=tips.map(t=>`<li>✅ ${t}</li>`).join('');

          const topBand=(mScore>=80&&dScore>=80)?'good':((mScore>=60||dScore>=60)?'warn':'bad');
          psiStatus.className='pill '+(topBand==='good'?'score-pill--green':topBand==='warn'?'score-pill--orange':'score-pill--red');
          psiStatus.textContent=topBand==='good'?'🎉 Excellent Speed':topBand==='warn'?'OK':'Needs Work';
        }catch(e){
          psiStatus.textContent='Unavailable';
          psiFixes.innerHTML=`<li>⚠️ ${String(e.message||e)}. Make sure PSI key is set server-side.</li>`;
        }
      }catch(err){
        console.error(err);
        showError('Analyze failed.',String(err.message||err));
      }finally{
        setRunning(false);
      }
    });

    // Utility buttons
    pasteBtn && pasteBtn.addEventListener('click', async e => { e.preventDefault(); try{const t=await navigator.clipboard.readText(); if(t) urlInput.value=t.trim();}catch{} });
    importBtn && importBtn.addEventListener('click',()=>importFile.click());
    importFile && importFile.addEventListener('change',e=>{const f=e.target.files?.[0];if(!f)return;const r=new FileReader();r.onload=()=>{try{const j=JSON.parse(String(r.result||'{}'));if(j.url)urlInput.value=j.url;alert('Imported JSON. Click Analyze to run.')}catch{alert('Invalid JSON file.')}};r.readAsText(f)});
    printBtn && printBtn.addEventListener('click',()=>window.print());
    resetBtn && resetBtn.addEventListener('click',()=>location.reload());
    exportBtn && exportBtn.addEventListener('click',()=>{if(!window.__lastData){alert('Run an analysis first.');return;}const blob=new Blob([JSON.stringify(window.__lastData,null,2)],{type:'application/json'});const a=document.createElement('a');a.href=URL.createObjectURL(blob);a.download='semantic-report.json';a.click();URL.revokeObjectURL(a.href)});
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init, { once: true });
  } else { init(); }
})();
</script>
@endpush

@section('content')
<section class="maxw px-4 pb-10">

  <!-- Title -->
  <div class="title-wrap">
    <div class="king">👑</div>
    <div style="text-align:center">
      <div class="t-grad" style="font-size:26px;line-height:1.1;">Semantic SEO Master Analyzer</div>
      <div class="byline">By <span class="shoail">Shoail Kahoker</span></div>
    </div>
  </div>

  <!-- Legend -->
  <div class="legend"><span class="badge g">Green ≥ 80</span><span class="badge o">Orange 60–79</span><span class="badge r">Red &lt; 60</span></div>

  <!-- Wheel + chips -->
  <div style="display:grid;grid-template-columns:230px 1fr;gap:16px;align-items:center;margin-top:10px">
    <div style="display:grid;place-items:center;border-radius:16px;padding:8px;background:#090916;border:1px solid #ffffff12">
      <div class="mw warn" id="mw">
        <div class="mw-ring" id="mwRing" style="--v:0"></div>
        <div class="mw-fill" id="mwFill" style="--p:0"></div>
        <div class="mw-center" id="mwNum">0%</div>
      </div>
    </div>
    <div class="space-y-2">
      <div style="display:flex;flex-wrap:wrap;gap:6px">
        <span id="chipOverall" class="chip warn"><i>🟧</i><span>Overall: 0 /100</span></span>
        <span id="chipContent" class="chip warn"><i>🟧</i><span>Content: —</span></span>
        <span id="chipWriter"  class="chip"><i>🟧</i><span>Writer: —</span></span>
        <span id="chipHuman"   class="chip"><i>🟧</i><span>Human-like: — %</span></span>
        <span id="chipAI"      class="chip"><i>🟧</i><span>AI-like: — %</span></span>
      </div>
      <div id="overallBar" class="waterbox warn">
        <div class="fill" id="overallFill" style="width:0%"></div>
        <div class="label"><span id="overallPct">0%</span></div>
      </div>
    </div>
  </div>

  <!-- Analyze toolbar -->
  <div class="analyze-wrap" style="margin-top:12px;">
    <div class="url-row">
      <span style="opacity:.75">🌐</span>
      <input id="urlInput" name="url" type="url" placeholder="https://example.com/page" />
      <button id="pasteBtn" type="button" class="paste">Paste</button>
    </div>
    <div style="display:flex;align-items:center;gap:10px;margin-top:10px">
      <label style="display:flex;align-items:center;gap:8px;font-size:12px">
        <input id="autoCheck" type="checkbox" class="accent-emerald-400" checked/> Auto-apply checkmarks (≥ 80)
      </label>
      <div style="flex:1"></div>
      <input id="importFile" type="file" accept="application/json" style="display:none"/>
      <button id="importBtn" type="button" class="btn btn-purple">⇪ Import</button>
      <button id="analyzeBtn" type="button" class="btn btn-green" onclick="return window.__SemAnalyze && window.__SemAnalyze(event)">🔍 Analyze</button>
      <button id="printBtn"   type="button" class="btn btn-blue">🖨️ Print</button>
      <button id="resetBtn"   type="button" class="btn btn-orange">↻ Reset</button>
      <button id="exportBtn"  type="button" class="btn btn-purple">⬇︎ Export</button>
    </div>
    <div id="errorBox"></div>

    <!-- Status chips -->
    <div id="statusChips" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:10px">
      <div class="chip" id="chipHttpWrap"><span class="t-grad">HTTP:</span>&nbsp;<span id="chipHttp">—</span></div>
      <div class="chip" id="chipTitleWrap"><span class="t-grad">Title:</span>&nbsp;<span id="chipTitle">—</span></div>
      <div class="chip" id="chipMetaWrap"><span class="t-grad">Meta desc:</span>&nbsp;<span id="chipMeta">—</span></div>
      <div class="chip"><span class="t-grad">Canonical:</span>&nbsp;<span id="chipCanon">—</span></div>
      <div class="chip"><span class="t-grad">Robots:</span>&nbsp;<span id="chipRobots">—</span></div>
      <div class="chip"><span class="t-grad">Viewport:</span>&nbsp;<span id="chipViewport">—</span></div>
      <div class="chip"><span class="t-grad">H1/H2/H3:</span>&nbsp;<span id="chipH">—</span></div>
      <div class="chip"><span class="t-grad">Internal links:</span>&nbsp;<span id="chipInt">—</span></div>
      <div class="chip"><span class="t-grad">Schema:</span>&nbsp;<span id="chipSchema">—</span></div>
      <div class="chip"><span class="t-grad">Auto-checked:</span>&nbsp;<span id="chipAuto">0</span></div>
    </div>
  </div>

  <!-- Quick Stats -->
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Quick Stats</h3>
    <div style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:10px">
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Readability (Flesch)</div><div id="statFlesch" style="font-size:20px;font-weight:800">—</div><div id="statGrade" style="font-size:12px;color:#94a3b8">—</div></div>
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Links (int / ext)</div><div style="font-size:20px;font-weight:800"><span id="statInt">0</span> / <span id="statExt">0</span></div></div>
      <div class="card"><div style="font-size:12px;color:#b6c2cf">Text/HTML Ratio</div><div id="statRatio" style="font-size:20px;font-weight:800">—</div></div>
    </div>
  </div>

  <!-- ===================== Readability Insights ===================== -->
  <div class="read-card" id="readabilityCard" style="margin-top:16px">
    <div class="rb-head">
      <div class="rb-title">
        <div class="ico">📚</div>
        <div>
          <div class="t-grad" style="font-weight:900;">Readability Insights</div>
          <div class="rb-legend" id="rbLegend">Multilingual analysis — English, العربية, Português</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:6px">
        <span id="readBadge" class="pill">—</span>
        <span id="gradeBadge" class="pill">Grade —</span>
      </div>
    </div>

    <div class="rb-grid">
      <!-- Wheel -->
      <div style="display:grid;place-items:center;border-radius:16px;padding:8px;background:#090916;border:1px solid #ffffff12">
        <div class="mw mw-sm warn" id="readMw">
          <div class="mw-ring" id="readRing" style="--v:0"></div>
          <div class="mw-fill" id="readFill" style="--p:0"></div>
          <div class="mw-center" id="readNum">0%</div>
        </div>
      </div>

      <!-- Metric tiles -->
      <div class="rb-tiles">
        <div class="rb-tile"><div class="rb-row"><div>😊 Flesch Reading Ease</div><div class="rb-val" id="rbFleschVal">—</div></div><div class="rb-meter"><span id="rbFleschFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🧾 Avg Sentence Length</div><div class="rb-val" id="rbASLVal">—</div></div><div class="rb-meter"><span id="rbASLFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🔤 Words</div><div class="rb-val" id="rbWordsVal">—</div></div><div class="rb-meter"><span id="rbWordsFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🅰️ Syllables / Word</div><div class="rb-val" id="rbSyllVal">—</div></div><div class="rb-meter"><span id="rbSyllFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🔀 Lexical Diversity (TTR)</div><div class="rb-val" id="rbTTRVal">—</div></div><div class="rb-meter"><span id="rbTTRFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>♻️ Repetition (tri-gram)</div><div class="rb-val" id="rbTriVal">—</div></div><div class="rb-meter"><span id="rbTriFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div># Digits / 100 words</div><div class="rb-val" id="rbDigitsVal">—</div></div><div class="rb-meter"><span id="rbDigitsFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>🗣️ Passive voice</div><div class="rb-val" id="rbPassiveVal">—</div></div><div class="rb-meter"><span id="rbPassiveFill" style="width:0%"></span></div></div>
        <div class="rb-tile"><div class="rb-row"><div>✨ Simple words</div><div class="rb-val" id="rbSimpleVal">—</div></div><div class="rb-meter"><span id="rbSimpleFill" style="width:0%"></span></div></div>
      </div>
    </div>

    <div class="rb-fixes">
      <h4>💡 Simple Fixes</h4>
      <ul id="rbFixes"><li>Run an analysis to see targeted suggestions.</li></ul>
    </div>

    <div id="rbBanner" class="rb-banner warn">Readability score helps you target Grade 7–9 for most audiences.</div>
  </div>
  <!-- =================== /Readability =================== -->

  <!-- ===================== Site Speed & Core Web Vitals ===================== -->
  <div class="speed-card" id="speedCard">
    <div class="sp-head">
      <div class="sp-title">
        <div class="ico">⚡</div>
        <div>
          <div class="t-grad" style="font-weight:900;">Site Speed & Core Web Vitals</div>
          <div class="sp-note">Uses PageSpeed Insights (Mobile + Desktop)</div>
        </div>
      </div>
      <div style="display:flex;align-items:center;gap:6px">
        <span id="psiStatus" class="pill">Waiting…</span>
      </div>
    </div>

    <div class="sp-wheels">
      <div class="wheel-card">
        <div class="mw mw-sm warn" id="mwMobile">
          <div class="mw-ring" id="ringMobile" style="--v:0"></div>
          <div class="mw-fill" id="fillMobile" style="--p:0"></div>
          <div class="mw-center" id="numMobile">M 0%</div>
        </div>
        <div class="wheel-label">Mobile</div>
      </div>
      <div class="wheel-card">
        <div class="mw mw-sm warn" id="mwDesktop">
          <div class="mw-ring" id="ringDesktop" style="--v:0"></div>
          <div class="mw-fill" id="fillDesktop" style="--p:0"></div>
          <div class="mw-center" id="numDesktop">D 0%</div>
        </div>
        <div class="wheel-label">Desktop</div>
      </div>
    </div>

    <!-- Bars -->
    <div class="sp-grid">
      <div>
        <div class="sp-tile"><div class="sp-row"><div>🏁 LCP (s)</div><div class="sp-val" id="lcpVal">—</div></div><div class="sp-meter" id="lcpMeter"><span id="lcpBar" style="width:0%"></span></div></div>
        <div class="sp-tile"><div class="sp-row"><div>📦 CLS</div><div class="sp-val" id="clsVal">—</div></div><div class="sp-meter" id="clsMeter"><span id="clsBar" style="width:0%"></span></div></div>
        <div class="sp-tile"><div class="sp-row"><div>⚡ INP (ms)</div><div class="sp-val" id="inpVal">—</div></div><div class="sp-meter" id="inpMeter"><span id="inpBar" style="width:0%"></span></div></div>
        <div class="sp-tile"><div class="sp-row"><div>⏱️ TTFB (ms)</div><div class="sp-val" id="ttfbVal">—</div></div><div class="sp-meter" id="ttfbMeter"><span id="ttfbBar" style="width:0%"></span></div></div>
      </div>
    </div>

    <div class="sp-fixes">
      <h4>💡 Speed Suggestions</h4>
      <ul id="psiFixes"><li>Run Analyze to fetch PSI data.</li></ul>
    </div>
  </div>
  <!-- =================== /Site Speed & CWV =================== -->

  <!-- Content Structure -->
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Content Structure</h3>
    <div style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px">
      <div class="card">
        <div style="font-size:12px;color:#b6c2cf">Title</div>
        <div id="titleVal" style="font-weight:600">—</div>
        <div style="font-size:12px;color:#b6c2cf;margin-top:10px">Meta Description</div>
        <div id="metaVal" style="color:#e5e7eb">—</div>
      </div>
      <div class="card">
        <div style="font-size:12px;color:#b6c2cf;margin-bottom:6px">Heading Map</div>
        <div id="headingMap" class="text-sm space-y-2"></div>
      </div>
    </div>
  </div>

  <!-- Recommendations -->
  <div class="card" style="margin-top:16px">
    <h3 class="t-grad" style="font-weight:900;margin:0 0 8px">Recommendations</h3>
    <div id="recs" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px"></div>
  </div>

  <!-- Semantic SEO Ground -->
  <div class="ground-slab">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
      <div class="king">🧭</div>
      <div>
        <div class="t-grad" style="font-weight:900;font-size:18px">Semantic SEO Ground</div>
        <div style="font-size:12px;color:#b6c2cf">Six categories • Five checks each • Click “Improve” for guidance</div>
      </div>
    </div>
    <div id="cats" style="display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px"></div>
  </div>

  <!-- Improve Modal -->
  <dialog id="improveModal" class="rounded-2xl p-0 w-[min(680px,95vw)]" style="border:none;border-radius:16px">
    <div class="card">
      <div style="display:flex;align-items:start;justify-content:space-between;gap:10px">
        <h4 id="improveTitle" class="t-grad" style="font-weight:900;margin:0">Improve</h4>
        <form method="dialog"><button class="pill">Close</button></form>
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
        <a id="improveSearch" target="_blank" class="card" style="text-align:center;display:flex;align-items:center;justify-content:center;background:linear-gradient(90deg,#f472b626,#22d3ee26);border:1px solid #ffffff22;text-decoration:none">
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
