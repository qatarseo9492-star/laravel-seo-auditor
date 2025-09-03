/**
 * analyze_button_fix.js
 * Safe, idempotent listener that wires #analyzeBtn to POST /api/semantic-analyze
 * Requires:
 *   - <meta name="csrf-token" content="{{ csrf_token() }}">
 *   - <button id="analyzeBtn">Analyze</button>
 *   - <input id="targetUrl" type="url">
 * Does not change any of your existing logic; just sends the request
 * and dispatches a custom event with the response for your existing
 * UI code to hook into.
 */
(function () {
  if (window.__ANALYZE_BTN_BOUND__) return;
  window.__ANALYZE_BTN_BOUND__ = true;

  function $(id){ return document.getElementById(id); }
  const btn = $('analyzeBtn');
  const urlInput = $('targetUrl');
  if (!btn || !urlInput) { console.warn('[analyze_button_fix] Missing #analyzeBtn or #targetUrl'); return; }

  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

  btn.addEventListener('click', async (ev) => {
    ev.preventDefault();

    const url = (urlInput.value || '').trim();
    if (!url) { alert('Please enter a URL.'); urlInput.focus(); return; }

    btn.disabled = true;
    btn.dataset.loading = '1';

    try {
      const res = await fetch('/api/semantic-analyze', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf
        },
        body: JSON.stringify({ url })
      });

      if (res.status === 401) { alert('Please log in to analyze.'); return; }
      if (res.status === 429) { alert('You have reached your usage quota.'); return; }

      const json = await res.json().catch(() => ({}));
      if (!res.ok || json?.ok === false) {
        console.error('[analyze_button_fix] Server error:', json);
        alert(json?.error || json?.message || 'Analysis failed.');
        return;
      }

      // Bubble the result for your existing UI to consume.
      // Listen elsewhere with: window.addEventListener('semantic:analyzed', (e) => { console.log(e.detail); });
      window.dispatchEvent(new CustomEvent('semantic:analyzed', { detail: json }));
      console.log('[analyze_button_fix] Analysis complete:', json);
    } catch (err) {
      console.error('[analyze_button_fix] Network error:', err);
      alert('Network or server error.');
    } finally {
      btn.disabled = false;
      delete btn.dataset.loading;
    }
  });
})();
