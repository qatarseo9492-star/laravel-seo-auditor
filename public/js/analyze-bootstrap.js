/*! analyze-bootstrap.js â€” makes the Analyze button work even if markup varies */
(function(){
  function q(sel,ctx){ try{ return (ctx||document).querySelector(sel); }catch(e){ return null; } }
  function qa(sel,ctx){ try{ return Array.prototype.slice.call((ctx||document).querySelectorAll(sel)); }catch(e){ return []; } }
  function log(){ try{ console.log.apply(console, arguments); }catch(e){} }
  function hud(msg){ 
    var box = q('#analyzeStatus');
    if (!box) return;
    var t = q('.t', box); 
    box.style.display = 'block';
    if (t) t.textContent = msg;
  }
  function getEndpoint(){
    var meta = q('meta[name="analyze-endpoint"]');
    if (meta && meta.getAttribute('content')) return meta.getAttribute('content');
    if (window.ANALYZE_ENDPOINT) return String(window.ANALYZE_ENDPOINT);
    return '/ajax/analyze-url';
  }
  function getCsrf(){
    var meta = q('meta[name="csrf-token"]');
    return meta && meta.getAttribute('content') ? meta.getAttribute('content') : '';
  }
  function ensureIds(){
    // URL input
    var url = q('#analyzeUrl') || q('input[name="url"]') || qa('input[type="text"]').find(function(el){ return /https?:\/\//i.test(el.placeholder||'') || /url/i.test(el.name||'') || /http/i.test(el.value||''); });
    if (url && !url.id) url.id = 'analyzeUrl';
    // Button
    var btn = q('#analyzeBtn') || q('[data-action="analyze"]') || q('[data-analyze]') || q('.js-analyze') ||
              qa('button').find(function(b){ return /analy/i.test(b.textContent||'') || /SEMSEO_go/.test(b.getAttribute('onclick')||''); });
    if (btn && !btn.id) btn.id = 'analyzeBtn';
    return {url: url, btn: btn};
  }
  async function analyze(){
    try{
      if (window.APP && window.APP.loggedIn === false) { hud('Please login to analyze.'); return; }
      var ids = ensureIds();
      var input = ids.url;
      if (!input) { hud('URL input not found'); return; }
      var v = String((input.value||'').trim());
      if (!v) { hud('Please enter a valid URL'); return; }
      if (!/^https?:\/\//i.test(v)) v = 'https://'+v;
      var endpoint = getEndpoint(); log('ANALYZE_URL =>', endpoint);
      hud('Analyzingâ€¦');
      var resp = await fetch(endpoint, {
        method: 'POST',
        headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':getCsrf()},
        credentials: 'same-origin',
        body: JSON.stringify({ url: v })
      });
      var ct = (resp.headers.get('Content-Type')||'').toLowerCase();
      if (!resp.ok) throw new Error(resp.status===401?'Please login to analyze.':resp.status===419?'Session expired, refresh & login.':'Analyze failed ('+resp.status+').');
      if (ct.indexOf('application/json') === -1) throw new Error('Please login to analyze this URL.');
      var data = await resp.json();
      // Apply scores to badges/rows (1..25)
      if (data && data.itemScores){
        Object.keys(data.itemScores).forEach(function(k){
          var idx = parseInt(k,10);
          var sc = data.itemScores[k];
          var badge = q('#sc-'+idx);
          var row = q('#ck-'+idx) && q('#ck-'+idx).closest('.checklist-item');
          if (badge && !isNaN(sc)){
            badge.textContent = sc;
            badge.classList.remove('score-good','score-mid','score-bad');
            if (sc >= 80) badge.classList.add('score-good');
            else if (sc >= 60) badge.classList.add('score-mid');
            else badge.classList.add('score-bad');
          }
          if (row && !isNaN(sc)){
            row.classList.remove('sev-good','sev-mid','sev-bad');
            if (sc >= 80) row.classList.add('sev-good');
            else if (sc >= 60) row.classList.add('sev-mid');
            else row.classList.add('sev-bad');
            var cb = q('input[type="checkbox"]', row);
            if (cb && sc >= 80) cb.checked = true;
          }
        });
      }
      if (typeof window.updateCategoryBars === 'function') { try{ window.updateCategoryBars(); }catch(e){} }
      if (typeof window.setScoreWheel === 'function') {
        var ov = parseInt((data && data.overall),10);
        var cs = parseInt((data && data.contentScore),10);
        try{ window.setScoreWheel(!isNaN(ov) ? ov : (!isNaN(cs) ? cs : 0)); }catch(e){}
      }
      hud('Done'); setTimeout(function(){ hud(''); q('#analyzeStatus').style.display='none'; }, 1200);
    }catch(err){
      log(err); hud(err && err.message ? err.message : 'Failed to analyze URL');
    }
  }
  function bind(){
    var ids = ensureIds();
    if (ids.btn){
      ids.btn.addEventListener('click', function(e){ e.preventDefault(); analyze(); });
      if (ids.btn.tagName === 'BUTTON' && ids.btn.type !== 'button') ids.btn.type='button';
    }
    var url = ids.url;
    if (url) url.addEventListener('keydown', function(e){ if(e.key==='Enter'){ e.preventDefault(); analyze(); } });
    window.SEMSEO_go = function(){ analyze(); };
    // run once to set meters
    if (typeof window.updateCategoryBars === 'function') { try{ window.updateCategoryBars(); }catch(e){} }
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', bind);
  else bind();
})();
