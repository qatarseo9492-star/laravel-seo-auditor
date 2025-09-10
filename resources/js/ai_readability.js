// AI Readability & Humanizer (safe-scoped).
// Requires route POST /api/ai-readability -> AnalyzerController@aiReadability
(function(){
  const $ = (id)=>document.getElementById(id);

  const btn = $('aihAnalyzeBtn');
  if(!btn) return; // partial present but not on this page

  const spin = $('aihBtnSpin');
  const input = $('aihUrl');
  const progress = $('aihProgressMsg');
  const results = $('aihResults');

  function toastOnce(){
    try{
      if(localStorage.getItem('aih_welcome')) return;
      localStorage.setItem('aih_welcome','1');
      const t = document.createElement('div');
      t.className = 'aih-card aih-glow';
      t.style.position='fixed'; t.style.zIndex='60'; t.style.right='16px'; t.style.top='16px';
      t.style.maxWidth='min(460px,90vw)';
      t.innerHTML = `<div style="display:flex;gap:10px;align-items:center">
        <span class="aih-tick"><svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></span>
        <div><div class="aih-title">Welcome! 👋</div><div class="aih-muted">Paste a URL and click <b>Analyze</b>. We’ll estimate how human it reads.</div></div>
      </div>`;
      document.body.appendChild(t);
      setTimeout(()=>t.remove(), 4500);
    }catch(e){}
  }
  toastOnce();

  function setProcessing(msg){
    progress.style.display = 'block';
    progress.innerHTML = `<span class="aih-spin" style="vertical-align:-3px;margin-right:8px"></span>${msg}`;
  }
  function clearProcessing(){ progress.style.display = 'none'; progress.textContent = ''; }

  function setBadge(type, text){
    const badge = $('aihBadge');
    badge.classList.remove('aih-green','aih-orange','aih-red');
    badge.classList.add('aih-' + type);
    $('aihBadgeText').textContent = text;
  }

  async function run(){
    const url = input.value.trim();
    if(!url){ input.focus(); return; }

    btn.disabled = true; spin.style.display='inline-block';
    setBadge('orange', 'Analyzing…');
    results.style.display = 'none';
    setProcessing('Stay with us — fetching the page…');

    try{
      const r = await fetch('/api/ai-readability', {
        method: 'POST',
        headers: {
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({url})
      });
      if(!r.ok){
        const t = await r.text();
        throw new Error(t || 'Request failed');
      }
      setProcessing('Analyzing readability, voice, and AI-likelihood…');
      const data = await r.json();
      clearProcessing();

      $('aihHumanScore').textContent = (data.human_score ?? 0) + '%';
      $('aihAiScore').textContent = (data.ai_score >= 0 ? data.ai_score : '—') + (data.ai_score >= 0 ? '%' : '');
      $('aihReadability').textContent = (data.readability_score ?? 0) + '%';
      $('aihPassive').textContent = (data.passive_ratio ?? 0) + '%';

      setBadge(data.badge_type || 'orange', data.recommendation || 'Analysis complete');

      const tips = Array.isArray(data.suggestions) ? data.suggestions : [];
      const ul = $('aihTips'); ul.innerHTML='';
      tips.forEach(s => { const li = document.createElement('li'); li.textContent = s; ul.appendChild(li); });

      results.style.display = 'block';
    }catch(err){
      clearProcessing();
      alert('Analyze failed: ' + err.message);
    }finally{
      btn.disabled = false; spin.style.display='none';
    }
  }

  btn.addEventListener('click', run);
})();
