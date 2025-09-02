/**
 * technical-seo.client.v2.js
 * Robust hooks:
 *  - Click on [data-techseo-analyze] (preferred)
 *  - Auto-listen to common CustomEvents from existing analyzer flows:
 *      'analyze:started', 'analyze:completed', 'analyzer:completed',
 *      'semantic:analyze-completed', 'co:analyze:completed'
 *    and try to read detail.url
 *  - Fallbacks for URL source:
 *      [data-url-input], #targetUrl, #url, input[name="url"], querystring ?url=
 */
(() => {
  "use strict";

  const SEL = {
    urlInput:    '[data-url-input], #targetUrl, #url, input[name="url"]',
    runButton:   '[data-techseo-analyze], #btn-techseo',
    scoreUrl:    '#score-url-structure',
    scoreMeta:   '#score-meta',
    scoreImages: '#score-images',
    scoreInternal: '#score-internal',
    scoreStructure: '#score-structure',
    scoreOverall: '#score-overall-techseo',
    urlIssuesList: '#url-issues',
    metaTitleCurrent:    '#meta-title-current',
    metaDescCurrent:     '#meta-desc-current',
    metaTitleSuggested:  '#meta-title-suggested',
    metaDescSuggested:   '#meta-desc-suggested',
    imagesContainer: '#images-table',
    internalLinksTableBody: '#internal-links-table',
    structureTree: '#structure-tree',
    loadingOverlay: '#techseo-loading-overlay'
  };

  const $  = (s, r=document) => r.querySelector(s);
  const $$ = (s, r=document) => Array.from(r.querySelectorAll(s));

  function escapeHtml(s){return (s??'').toString().replace(/[&<>"']/g,m=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m]))}
  const clamp100 = n => Math.max(0, Math.min(100, Number(n)||0));

  function setScore(el, n){
    if(!el) return; const v = clamp100(n);
    el.textContent = `${v}/100`; el.style.setProperty('--score', v);
    const bar = el.closest('[data-bar]')?.querySelector('[data-fill]'); if(bar) bar.style.width = v+'%';
  }
  function setText(el, val, placeholder='(missing)'){ if(el) el.textContent = (val && String(val).trim()) ? val : placeholder; }
  function setList(el, items){ if(el) el.innerHTML = (items||[]).map(t=>`<li>${escapeHtml(t)}</li>`).join(''); }
  function setInternalLinks(el, rows){
    if(!el) return; el.innerHTML = (rows||[]).map(r=>`<tr><td class="il-url"><a href="${escapeHtml(r.url||'')}" target="_blank" rel="noopener">${escapeHtml(r.url||'')}</a></td><td class="il-anchor">${escapeHtml(r.anchor||r.reason||'')}</td></tr>`).join('');
  }
  function setImages(el, items){
    if(!el) return; el.innerHTML = (items||[]).map(it=>`<div class="img-row"><div class="src"><a href="${escapeHtml(it.src||'')}" target="_blank" rel="noopener">${escapeHtml((it.src||'').split('/').pop()||'(image)')}</a></div><div class="alt cur">${escapeHtml(it.current_alt||'(missing)')}</div><div class="alt sug">${escapeHtml(it.suggested_alt||'')}</div></div>`).join('');
  }
  function renderTree(nodes){ if(!nodes?.length) return ''; return `<ul>`+nodes.map(n=>`<li><span class="h${n.level}">H${n.level}: ${escapeHtml(n.text||'')}</span>${renderTree(n.children||[])}</li>`).join('')+`</ul>`; }
  function setTree(el, data){ if(el) el.innerHTML = renderTree(data||[]); }
  function csrf(){ return document.querySelector('meta[name="csrf-token"]')?.content || ''; }
  function showLoading(on){ const ov = $(SEL.loadingOverlay); if(ov){ ov.style.display = on ? 'grid' : 'none'; } }

  function readUrlFallback(){
    // 1) explicit input
    const el = $(SEL.urlInput);
    if (el?.value?.trim()) return el.value.trim();
    // 2) data attribute on any container
    const c = document.querySelector('[data-current-url]'); if (c?.dataset?.currentUrl) return c.dataset.currentUrl;
    // 3) querystring ?url=
    const u = new URL(location.href); const qs = u.searchParams.get('url'); if (qs) return qs;
    // 4) last payload used
    if (window.TechSEO?.lastPayload?.url) return window.TechSEO.lastPayload.url;
    return '';
  }

  async function apiAnalyze(url){
    const res = await fetch('/api/techseo/analyze', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-TOKEN': csrf()},
      body: JSON.stringify({ url })
    });
    const data = await res.json().catch(()=>({ok:false,error:'Invalid JSON'}));
    if(!res.ok || data.ok === false){ throw new Error((data && (data.error||data.message)) || `HTTP ${res.status}`); }
    return data;
  }

  async function run(url){
    const target = (url||'').trim() || readUrlFallback();
    if(!target){ alert('Enter a URL to analyze.'); return; }
    const btn = $(SEL.runButton);
    try{
      btn && (btn.disabled=true, btn.dataset.loading='1'); showLoading(true);
      const data = await apiAnalyze(target);
      window.TechSEO.lastPayload = data;

      setScore($(SEL.scoreUrl),       data.scores?.url_structure);
      setScore($(SEL.scoreMeta),      data.scores?.meta);
      setScore($(SEL.scoreImages),    data.scores?.images);
      setScore($(SEL.scoreInternal),  data.scores?.internal_links);
      setScore($(SEL.scoreStructure), data.scores?.structure);
      setScore($(SEL.scoreOverall),   data.scores?.overall);

      setList($(SEL.urlIssuesList), data.url_structure?.issues);
      setText($(SEL.metaTitleCurrent),   data.meta?.title);
      setText($(SEL.metaDescCurrent),    data.meta?.description);
      setText($(SEL.metaTitleSuggested), data.meta?.ai?.title || '', '');
      setText($(SEL.metaDescSuggested),  data.meta?.ai?.description || '', '');

      setImages($(SEL.imagesContainer), data.images?.suggestions);
      setInternalLinks($(SEL.internalLinksTableBody), (data.internal_linking?.ai || data.internal_linking?.candidates || []));
      setTree($(SEL.structureTree), data.structure?.tree);

      document.dispatchEvent(new CustomEvent('techseo:updated', { detail: { url: target, data } }));
      return data;
    } catch(err){
      console.error('[TechSEO]', err); alert(err.message || 'Technical SEO analyze failed.');
    } finally {
      btn && (btn.disabled=false, delete btn.dataset.loading); showLoading(false);
    }
  }

  function wire(){
    const btn = $(SEL.runButton);
    const input = $(SEL.urlInput);
    if(btn){ btn.addEventListener('click', ()=> run()); }
    if(input){ input.addEventListener('keydown', e => { if(e.key==='Enter'){ e.preventDefault(); run(); } }); }

    // Listen to various completion events from your main analyzer
    const evtNames = [
      'analyze:completed', 'analyzer:completed', 'semantic:analyze-completed',
      'co:analyze:completed', 'analyze:finished'
    ];
    evtNames.forEach(name => {
      document.addEventListener(name, (e) => {
        const url = e?.detail?.url || readUrlFallback();
        if(url) run(url);
      });
    });

    // Also listen to a generic event if your code dispatches it
    document.addEventListener('semantic:analyze', (e) => {
      const url = e?.detail?.url || readUrlFallback();
      if(url) run(url);
    });
  }

  document.addEventListener('DOMContentLoaded', wire);
  window.TechSEO = Object.freeze({ run, lastPayload:null, config:SEL });
})();
