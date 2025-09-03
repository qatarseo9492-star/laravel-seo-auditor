(() => {
  function log(...args){ try{ console.log('[AnalyzeFix]', ...args); }catch(_){} }
  function once(el, type, handler){
    if (!el || !type || !handler) return;
    const key = `__bound_${type}`;
    if (el[key]) return;
    el.addEventListener(type, handler);
    el[key] = true;
  }

  function getCsrf(){
    const meta = document.querySelector('meta[name="csrf-token"]');
    if (meta && meta.content) return meta.content;
    const hidden = document.querySelector('input[name="_token"]');
    return hidden ? hidden.value : null;
  }

  function getUrl(){
    const candidates = [
      '#urlInput', '#inputUrl', '#url', '#pageUrl',
      'input[name="url"]', 'input[type="url"]',
      '.url-input', '.js-url-input'
    ];
    for (const sel of candidates){
      const el = document.querySelector(sel);
      if (el && el.value) return el.value.trim();
    }
    return '';
  }

  async function defaultAnalyze(url){
    const csrf = getCsrf();
    const headers = {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    };
    if (csrf) headers['X-CSRF-TOKEN'] = csrf;

    const res = await fetch('/api/semantic-analyze', {
      method: 'POST',
      headers,
      credentials: 'same-origin',
      body: JSON.stringify({ url })
    });

    // Try to parse JSON even if not ok
    let data = null;
    try { data = await res.json(); } catch(_) {}

    if (res.status === 419){
      throw new Error('CSRF (419). Missing or invalid CSRF token.');
    }
    if (!res.ok){
      const msg = (data && (data.message || data.error)) || `HTTP ${res.status}`;
      const err = new Error('Analyze request failed: ' + msg);
      err.response = data;
      throw err;
    }
    return data;
  }

  function callExistingIfAny(url){
    const fns = [
      'runAnalyze', 'startAnalyze', 'startAnalysis', 'doAnalyze', 'analyze',
      'window.runAnalyze', 'window.startAnalyze', 'window.startAnalysis'
    ];
    for (const name of fns){
      const fn = name.split('.').reduce((acc, k)=> acc && acc[k], window);
      if (typeof fn === 'function'){
        log('Using existing function:', name);
        try { fn(url); return true; } catch(e){ log('Existing fn errored', e); }
      }
    }
    return false;
  }

  async function onAnalyzeClick(ev){
    try{
      ev && ev.preventDefault && ev.preventDefault();
      const url = getUrl();
      if (!url){
        alert('Please enter a valid URL first.');
        return;
      }
      // Prefer existing site logic if present
      if (callExistingIfAny(url)) return;

      // Fallback minimal request so button "does something"
      log('No existing global function found. Using default /api/semantic-analyze');
      const result = await defaultAnalyze(url);
      log('Semantic analyze result:', result);
      // emit an event so the page (if listening) can react
      document.dispatchEvent(new CustomEvent('analysis:semantic:done', { detail: result }));
    }catch(err){
      log('Analyze error:', err);
      // Surface helpful hints without taking over the UI
      if (String(err.message || '').includes('CSRF')){
        console.error('CSRF token missing/invalid. Ensure <meta name="csrf-token" content="{{ csrf_token() }}"> is in your layout and the request includes X-CSRF-TOKEN.');
        alert('Your session might have expired. Please refresh and try again.');
      }
    }
  }

  function wire(){
    const buttonSelectors = [
      '#analyzeBtn', '#analyze_button', '#btnAnalyze', 'button#analyze',
      'button[data-action="analyze"]', '.analyze-btn', '.js-analyze'
    ];
    let wired = false;
    for (const sel of buttonSelectors){
      const btn = document.querySelector(sel);
      if (btn){
        once(btn, 'click', onAnalyzeClick);
        wired = true;
      }
    }
    // Also wire Enter key in URL input
    const urlInputSel = ['#urlInput', '#inputUrl', '#url', '#pageUrl', 'input[name="url"]', 'input[type="url"]', '.url-input', '.js-url-input'];
    for (const sel of urlInputSel){
      const el = document.querySelector(sel);
      if (el){
        once(el, 'keydown', (e) => {
          if (e.key === 'Enter'){ onAnalyzeClick(e); }
        });
      }
    }
    if (!wired){
      // As a last resort, delegate on document for any click on elements with data-action="analyze"
      once(document, 'click', (e) => {
        const t = e.target.closest('[data-action="analyze"]');
        if (t) onAnalyzeClick(e);
      });
    }
    log('Analyze button wiring complete.');
  }

  if (document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', wire, { once: true });
  } else {
    wire();
  }
})();
