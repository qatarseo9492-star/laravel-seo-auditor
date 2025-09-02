/**
 * technical-seo.client.js
 * ---------------------------------------------------------
 * Fills the "Technical SEO" layout with real results
 * from POST /api/techseo/analyze { url }.
 *
 * Drop-in: no layout rewrite required. Adjust the selectors
 * in SEL below to match your Blade IDs/classes if needed.
 *
 * Requirements:
 * - <meta name="csrf-token" content="{{ csrf_token() }}">
 * - A URL input and a button to trigger analysis.
 *
 * Exposes window.TechSEO with two helpers:
 *   - TechSEO.run(url)     -> runs analysis and updates the UI
 *   - TechSEO.lastPayload  -> last JSON payload received
 */
(() => {
  "use strict";

  // ========================= Config =========================
  // Map these to your actual markup. If your IDs differ, update here.
  const SEL = {
    // Inputs / buttons
    urlInput:    '[data-url-input], #targetUrl, input[name="analyze_url"]',
    runButton:   '[data-techseo-analyze], #btn-techseo',

    // Score badges or containers (text is set to "NN/100")
    scoreUrl:    '#score-url-structure',
    scoreMeta:   '#score-meta',
    scoreImages: '#score-images',
    scoreInternal: '#score-internal',
    scoreStructure: '#score-structure',
    scoreOverall: '#score-overall-techseo',

    // URL structure
    urlIssuesList: '#url-issues', // <ul>

    // Meta containers
    metaTitleCurrent:    '#meta-title-current',
    metaDescCurrent:     '#meta-desc-current',
    metaTitleSuggested:  '#meta-title-suggested',
    metaDescSuggested:   '#meta-desc-suggested',

    // Images suggestions container (free HTML)
    imagesContainer: '#images-table',

    // Internal links suggestions (expects <tbody> or container)
    internalLinksTableBody: '#internal-links-table',

    // Headings tree container
    structureTree: '#structure-tree',

    // Optional global loading overlay (add if you want)
    loadingOverlay: '#techseo-loading-overlay'
  };

  // If your progress bars use a width-based fill, mark the score wrapper with [data-bar]
  // and the fill element inside with [data-fill]. This script will set width: NN%.
  // Example:
  // <div class="meter" data-bar><div class="fill" data-fill style="width:0%"></div></div>

  // ========================= Utils ==========================
  const $  = (sel, root=document) => root.querySelector(sel);
  const $$ = (sel, root=document) => Array.from(root.querySelectorAll(sel));

  function escapeHtml(s) {
    return (s ?? '').toString().replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'
    })[m]);
  }

  function clamp01(n) { n = Number(n) || 0; return Math.max(0, Math.min(1, n)); }
  function clamp100(n) { n = Number(n) || 0; return Math.max(0, Math.min(100, n)); }

  function setScore(el, n) {
    if (!el) return;
    const v = clamp100(n);
    // Text badge: "NN/100"
    try { el.textContent = `${v}/100`; } catch {}
    // CSS var for fancy styles if you want
    try { el.style.setProperty('--score', v); } catch {}

    // Progress bar fill if present
    const bar = el.closest('[data-bar]')?.querySelector('[data-fill]');
    if (bar) bar.style.width = `${v}%`;
  }

  function setText(el, val, placeholder='(missing)') {
    if (!el) return;
    el.textContent = (val && String(val).trim().length) ? val : placeholder;
  }

  function setList(el, items) {
    if (!el) return;
    const list = Array.isArray(items) ? items : [];
    el.innerHTML = list.map(t => `<li>${escapeHtml(t)}</li>`).join('');
  }

  function setInternalLinks(el, rows) {
    if (!el) return;
    const list = Array.isArray(rows) ? rows : [];
    const html = list.map(r => {
      const url = r.url || '';
      const anchor = r.anchor || r.reason || '';
      return `<tr>
        <td class="il-url"><a href="${escapeHtml(url)}" target="_blank" rel="noopener">${escapeHtml(url)}</a></td>
        <td class="il-anchor">${escapeHtml(anchor)}</td>
      </tr>`;
    }).join('');
    el.innerHTML = html;
  }

  function setImages(el, items) {
    if (!el) return;
    const list = Array.isArray(items) ? items : [];
    el.innerHTML = list.map(it => {
      const srcName = (it.src || '').split('/').pop();
      return `<div class="img-row">
        <div class="src"><a href="${escapeHtml(it.src || '')}" target="_blank" rel="noopener">${escapeHtml(srcName || '(image)')}</a></div>
        <div class="alt cur">${escapeHtml(it.current_alt || '(missing)')}</div>
        <div class="alt sug">${escapeHtml(it.suggested_alt || '')}</div>
      </div>`;
    }).join('');
  }

  function renderTree(nodes) {
    if (!nodes || !nodes.length) return '';
    return `<ul>` + nodes.map(n => `
      <li>
        <span class="h${n.level}">H${n.level}: ${escapeHtml(n.text || '')}</span>
        ${renderTree(n.children || [])}
      </li>
    `).join('') + `</ul>`;
  }

  function setTree(el, data) {
    if (!el) return;
    el.innerHTML = renderTree(data);
  }

  function showLoading(isOn) {
    const ov = $(SEL.loadingOverlay);
    if (!ov) return;
    ov.style.display = isOn ? 'grid' : 'none';
  }

  function csrfToken() {
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.content : '';
  }

  async function apiAnalyze(url) {
    const res = await fetch('/api/techseo/analyze', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken()
      },
      body: JSON.stringify({ url })
    });
    const data = await res.json();
    if (!res.ok || !data || data.ok === false) {
      const msg = (data && (data.error || data.message)) || `HTTP ${res.status}`;
      throw new Error(`Technical SEO analyze failed: ${msg}`);
    }
    return data;
  }

  // Debounce helper if you want to auto-run when input changes
  function debounce(fn, ms=400) {
    let t;
    return (...args) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...args), ms);
    };
  }

  // ========================= App ============================
  async function run(url, opts={}) {
    const btn = $(SEL.runButton);
    try {
      const target = (url || '').trim() || ($(SEL.urlInput)?.value || '').trim();
      if (!target) throw new Error('Please enter a URL.');

      btn && (btn.disabled = true);
      btn && (btn.dataset.loading = '1');
      showLoading(true);

      const data = await apiAnalyze(target);
      window.TechSEO.lastPayload = data;

      // Scores
      setScore($(SEL.scoreUrl),       data.scores?.url_structure);
      setScore($(SEL.scoreMeta),      data.scores?.meta);
      setScore($(SEL.scoreImages),    data.scores?.images);
      setScore($(SEL.scoreInternal),  data.scores?.internal_links);
      setScore($(SEL.scoreStructure), data.scores?.structure);
      setScore($(SEL.scoreOverall),   data.scores?.overall);

      // URL structure
      setList($(SEL.urlIssuesList), data.url_structure?.issues);

      // Meta (current + AI suggestions if present)
      setText($(SEL.metaTitleCurrent),   data.meta?.title);
      setText($(SEL.metaDescCurrent),    data.meta?.description);
      setText($(SEL.metaTitleSuggested), data.meta?.ai?.title || '' , '');
      setText($(SEL.metaDescSuggested),  data.meta?.ai?.description || '' , '');

      // Images ALT suggestions
      setImages($(SEL.imagesContainer), data.images?.suggestions);

      // Internal links (AI preferred, fallback to heuristic)
      const linkRows = data.internal_linking?.ai || data.internal_linking?.candidates || [];
      setInternalLinks($(SEL.internalLinksTableBody), linkRows);

      // Headings tree
      setTree($(SEL.structureTree), data.structure?.tree || []);

      // Optional: emit an event so other modules can respond
      document.dispatchEvent(new CustomEvent('techseo:updated', { detail: { url: target, data } }));

      return data;
    } catch (err) {
      console.error(err);
      alert(err.message || 'Technical SEO analyze failed.');
      throw err;
    } finally {
      const btn = $(SEL.runButton);
      btn && (btn.disabled = false);
      btn && delete btn.dataset.loading;
      showLoading(false);
    }
  }

  function wire() {
    const btn = $(SEL.runButton);
    const input = $(SEL.urlInput);

    if (btn) {
      btn.addEventListener('click', () => run());
      // Keyboard shortcut: Enter in input triggers run
      if (input) {
        input.addEventListener('keydown', (e) => {
          if (e.key === 'Enter') {
            e.preventDefault();
            run();
          }
        });
      }
    }
  }

  // Init
  document.addEventListener('DOMContentLoaded', () => {
    try { wire(); } catch (e) { console.error('TechSEO wire error', e); }
  });

  // Public API
  window.TechSEO = Object.freeze({
    run,
    lastPayload: null,
    config: SEL
  });
})();
