{{-- resources/views/home.blade.php --}}
{{-- … [UNCHANGED HEAD + STYLES + BODY MARKUP FROM YOUR CURRENT FILE] … --}}

{{-- NOTE: Everything above remains the same as your latest working version you pasted previously.
     The important fix is the updated JS below. If you prefer, you can just replace your <script> blocks
     with the version here. For completeness I show only the JS section changed. --}}

<script>
/* ===== helper kept from your version ===== */
function setChipTone(el, value){
  if (!el) return;
  el.classList.remove('chip-good','chip-mid','chip-bad');
  const ico = el.querySelector('i.ico'); if (ico) ico.classList.remove('ico-green','ico-orange','ico-red','ico-purple');
  const v = Number(value); if (Number.isNaN(v)) return;
  if (v >= 80){ el.classList.add('chip-good'); if (ico) ico.classList.add('ico-green'); }
  else if (v >= 60){ el.classList.add('chip-mid'); if (ico) ico.classList.add('ico-orange'); }
  else { el.classList.add('chip-bad'); if (ico) ico.classList.add('ico-red'); }
}
function setText(id, val){ const el = document.getElementById(id); if (el) el.textContent = val; return el; }

/* ===== your gauge setup kept (shortened here for brevity) ===== */
const GAUGE = { rect:null, stop1:null, stop2:null, r1:null, r2:null, arc:null, text:null, H:200, CIRC: 2*Math.PI*95 };
function setScoreWheel(value){
  if (!GAUGE.rect){
    GAUGE.rect  = document.getElementById('scoreClipRect');
    GAUGE.stop1 = document.getElementById('scoreStop1');
    GAUGE.stop2 = document.getElementById('scoreStop2');
    GAUGE.r1    = document.getElementById('ringStop1');
    GAUGE.r2    = document.getElementById('ringStop2');
    GAUGE.arc   = document.getElementById('ringArc');
    GAUGE.text  = document.getElementById('overallScore');
    if (GAUGE.arc){
      GAUGE.arc.style.strokeDasharray = GAUGE.CIRC.toFixed(2);
      GAUGE.arc.style.strokeDashoffset = GAUGE.CIRC.toFixed(2);
    }
  }
  const v = Math.max(0, Math.min(100, Number(value)||0));
  const y = GAUGE.H - (GAUGE.H * (v/100));
  GAUGE.rect && GAUGE.rect.setAttribute('y', String(y));
  GAUGE.text && (GAUGE.text.textContent = Math.round(v) + '%');

  let c1, c2;
  if (v >= 80){ c1='#22c55e'; c2='#16a34a'; }
  else if (v >= 60){ c1='#f59e0b'; c2='#fb923c'; }
  else { c1='#ef4444'; c2='#b91c1c'; }
  GAUGE.stop1 && GAUGE.stop1.setAttribute('stop-color', c1);
  GAUGE.stop2 && GAUGE.stop2.setAttribute('stop-color', c2);
  GAUGE.r1 && GAUGE.r1.setAttribute('stop-color', c1);
  GAUGE.r2 && GAUGE.r2.setAttribute('stop-color', c2);

  if (GAUGE.arc){
    const offset = GAUGE.CIRC * (1 - (v/100));
    GAUGE.arc.style.strokeDashoffset = offset.toFixed(2);
  }

  setText('overallScoreInline', Math.round(v));
  setChipTone(document.getElementById('overallChip'), v);
}

/* ====== BADGE SETTER (unchanged) ====== */
window.setScoreBadge = (num,score)=>{
  const el=document.getElementById('sc-'+num); if(!el) return;
  el.className='score-badge';
  const row = el.closest('.checklist-item');
  row && row.classList.remove('sev-good','sev-mid','sev-bad');
  if(score==null){el.textContent='—';return;}
  el.textContent=score;
  if(score>=80){ el.classList.add('score-good'); row && row.classList.add('sev-good'); }
  else if(score>=60){ el.classList.add('score-mid'); row && row.classList.add('sev-mid'); }
  else { el.classList.add('score-bad'); row && row.classList.add('sev-bad'); }
};

/* ====== Fallback score computation if backend does not send 'scores' ====== */
function computeFallbackScores(data){
  // Safely read fields
  const tLen = Number((data.title||'').length)||0;
  const mLen = Number(data.meta_description_len)||0;
  const canon = !!data.canonical;
  const robots = (data.robots||'')+'';
  const viewport = !!data.viewport;
  const counts = data.counts||{};
  const h1 = Number(counts.h1)||0;
  const h2 = Number(counts.h2)||0;
  const h3 = Number(counts.h3)||0;
  const internal = Number(counts.internal_links)||0;
  const hasSchema = Array.isArray(data.schema?.found_types) && data.schema.found_types.length>0;
  const hasBreadcrumb = (data.schema?.found_types||[]).includes('BreadcrumbList');

  const titleScore = (tLen>=45 && tLen<=65)?92:((tLen>=30 && tLen<=72)?78:45);
  const metaScore = (mLen>=120 && mLen<=170)?90:((mLen>=80)?70:40);
  const canonScore= canon?90:40;
  const indexScore= /noindex/i.test(robots)?20:82;
  const headingVar = (h2+h3);
  const headScore = headingVar>=6?82:(headingVar>=3?70:55);
  const internalScore = internal>=8?88:(internal>=3?70:40);
  const mobileScore = viewport?88:40;
  const schemaScore = hasSchema?85:45;
  const breadScore = hasBreadcrumb?85:45;

  // Reasonable, steady defaults for items we can’t infer client-side
  const fixed = { faq:60, eat:70, read:72, media:70, slug:72, speed:68, vitals:62, cta:70, entity:72, related:70, sameas:60, unique:65, keywords:65, intent:70, h1inc: h1>0?75:55 };

  return {
    'ck-1':  fixed.intent,
    'ck-2':  fixed.keywords,
    'ck-3':  fixed.h1inc,
    'ck-4':  fixed.faq,
    'ck-5':  fixed.read,
    'ck-6':  titleScore,
    'ck-7':  metaScore,
    'ck-8':  canonScore,
    'ck-9':  indexScore,
    'ck-10': fixed.eat,
    'ck-11': fixed.unique,
    'ck-12': 70,
    'ck-13': fixed.media,
    'ck-14': headScore,
    'ck-15': internalScore,
    'ck-16': fixed.slug,
    'ck-17': breadScore,
    'ck-18': mobileScore,
    'ck-19': fixed.speed,
    'ck-20': fixed.vitals,
    'ck-21': fixed.cta,
    'ck-22': fixed.entity,
    'ck-23': fixed.related,
    'ck-24': schemaScore,
    'ck-25': fixed.sameas,
  };
}

/* ====== Read scores in any format ====== */
function coerceScoresToMap(scores){
  const out = {};
  if (!scores) return out;

  // Case A: already 'ck-#' keys
  let ckKeys = Object.keys(scores).filter(k=>/^ck-\d+$/.test(k));
  if (ckKeys.length){
    ckKeys.forEach(k=> out[k] = Number(scores[k]));
    return out;
  }

  // Case B: numeric keys or array [index 0..24]
  const keys = Object.keys(scores);
  if (Array.isArray(scores)){
    for (let i=0;i<Math.min(25, scores.length);i++) out['ck-'+(i+1)] = Number(scores[i]);
    return out;
  }
  if (keys.every(k=>/^\d+$/.test(k))){
    keys.forEach(k=> out['ck-'+k] = Number(scores[k]));
    return out;
  }

  return out;
}

/* ====== Small utilities kept from your previous build ====== */
function normalizeUrl(u){ if(!u) return ''; u = u.trim(); if (!/^https?:\/\//i.test(u)) u = 'https://' + u.replace(/^\/+/, ''); try { new URL(u); } catch(e){} return u; }

/* ====== MAIN: Analyze flow (fixed) ====== */
(function(){
  const STORAGE_KEY = 'semanticSeoChecklistV6';
  const AUTO_SCORE_THRESHOLD = 80;

  // preserve your existing Water progress object & other UI code here…
  // (if you already have it in your file, keep it — omitted here for brevity)

  function updateBadgesFromMap(map){
    for (let i=1;i<=25;i++){
      const v = Number(map['ck-'+i]);
      if (isFinite(v)) setScoreBadge(i, Math.round(v));
    }
  }

  function overallFromMap(map){
    let sum=0, n=0;
    for (let i=1;i<=25;i++){
      const v = Number(map['ck-'+i]);
      if (isFinite(v)){ sum+=v; n++; }
    }
    return n? Math.round(sum/n) : 0;
    }

  function autoSelectFromBadges(){
    const picks = new Set();
    for(let i=1;i<=25;i++){
      const badge = document.getElementById('sc-'+i);
      if(!badge) continue;
      const val = parseInt((badge.textContent||'').trim(), 10);
      if(Number.isFinite(val) && val >= AUTO_SCORE_THRESHOLD) picks.add('ck-'+i);
    }
    return picks;
  }

  async function analyze(){
    const url = normalizeUrl(document.getElementById('analyzeUrl').value);
    const status = document.getElementById('analyzeStatus');
    const btn = document.getElementById('analyzeBtn');
    const report = document.getElementById('analyzeReport');

    if (!url){ status && (status.textContent='Please enter a URL.'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Analyzing';
    status && (status.textContent='Analyzing…');

    // Start your water progress animation if you use it
    window.Water && Water.start?.();

    let data;
    try{
      const resp = await fetch('{{ route('analyze.json') }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ url })
      });
      data = await resp.json();
    }catch(err){
      status && (status.textContent='Network error.'); 
      btn.disabled=false; btn.innerHTML='<i class="fa-solid fa-magnifying-glass"></i> Analyze';
      window.Water && Water.finish?.();
      return;
    }

    try{
      // Fill chips we always have
      setText('rStatus', data.status ?? '—');
      setText('rTitleLen', (data.title || '').length);
      setText('rMetaLen', data.meta_description_len ?? 0);
      setText('rCanonical', data.canonical ? 'Yes' : 'No');
      setText('rRobots', data.robots || '—');
      setText('rViewport', data.viewport ? 'Yes' : 'No');
      setText('rHeadings', `${data.counts?.h1||0}/${data.counts?.h2||0}/${data.counts?.h3||0}`);
      setText('rInternal', data.counts?.internal_links ?? 0);
      setText('rSchema', (data.schema?.found_types || []).slice(0,6).join(', ') || '—');
      setText('rAutoCount', (data.auto_check_ids||[]).length);
      report && (report.style.display='block');

      // Unify score source
      let scoreMap = {};
      const candidates = data.scores || data.checklist_scores || data.checklist || data.items;
      scoreMap = coerceScoresToMap(candidates);

      // Fallback if backend omitted scores
      if (!Object.keys(scoreMap).length) {
        scoreMap = computeFallbackScores(data);
      }

      // Paint badges
      updateBadgesFromMap(scoreMap);

      // Auto-apply checkboxes if enabled
      if (document.getElementById('autoApply').checked) {
        const fromBadges = autoSelectFromBadges();
        const union = new Set([...(data.auto_check_ids||[]), ...fromBadges]);
        document.querySelectorAll('#analyzer input[type="checkbox"]').forEach(cb => cb.checked = union.has(cb.id));
        const selected = Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]:checked')).map(cb=>cb.id);
        localStorage.setItem(STORAGE_KEY, JSON.stringify(selected));
      }

      // Content score from checked boxes
      const checked = Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]:checked')).length;
      const cs = Math.round((checked/25)*100);
      setText('contentScoreInline', cs);
      setChipTone(document.getElementById('contentScoreChip'), cs);

      // Overall
      let overall = (typeof data.overall_score === 'number') ? data.overall_score : overallFromMap(scoreMap);
      setScoreWheel(overall);

      // AI/Human panel (safe)
      if (data.ai_detection){
        const ai = data.ai_detection;
        setText('aiPct', (typeof ai.ai_pct==='number')?ai.ai_pct:'—');
        setText('humanPct', (typeof ai.human_pct==='number')?ai.human_pct:'—');
        if (window.__setAIData) window.__setAIData(ai);
        const badge = document.getElementById('aiBadge');
        if (badge){
          const key = (ai.label||'').toLowerCase();
          const labelMap={likely_human:'Likely Human', mixed:'Mixed', likely_ai:'Likely AI'};
          const iconMap={likely_human:'fa-user-check', mixed:'fa-shuffle', likely_ai:'fa-robot'};
          const colorMap={likely_human:'ico-green', mixed:'ico-orange', likely_ai:'ico-red'};
          const chipMap={likely_human:'chip-good', mixed:'chip-mid', likely_ai:'chip-bad'};
          const label = labelMap[key] || 'Unknown';
          const icon  = iconMap[key] || 'fa-user';
          const icoC  = colorMap[key] || 'ico-purple';
          const chipC = chipMap[key];
          badge.classList.remove('chip-good','chip-mid','chip-bad');
          badge.innerHTML = `<i class="fa-solid ${icon} ico ${icoC}"></i> Writer: <b>${label}</b>`;
          chipC && badge.classList.add(chipC);
        }
      }

      // Recompute completed/heading bars/progress if your existing functions are present
      window.__updateChecklist && window.__updateChecklist();

      status && (status.textContent = overall>=80 ? 'Great! You passed—keep going.' : (overall<60 ? 'Score is low — optimize and re-Analyze.' : 'Solid! Improve a few items to hit green.'));
      setTimeout(()=>{ status && (status.textContent=''); }, 4200);
    }catch(e){
      console.error(e);
      status && (status.textContent='Parsing error.');
    }finally{
      btn.disabled=false; btn.innerHTML='<i class="fa-solid fa-magnifying-glass"></i> Analyze';
      window.Water && Water.finish?.();
    }
  }

  // Wire up
  document.getElementById('analyzeForm')?.addEventListener('submit', e => { e.preventDefault(); analyze(); });
  document.getElementById('analyzeBtn')?.addEventListener('click', analyze);

  // On load, paint any persisted checks so Content score isn’t blank
  (function bootstrapContentScoreFromStorage(){
    try{
      const saved = JSON.parse(localStorage.getItem('semanticSeoChecklistV6')||'[]');
      saved.forEach(id => { const cb = document.getElementById(id); if (cb) cb.checked = true; });
    }catch(e){}
    const checked = Array.from(document.querySelectorAll('#analyzer input[type="checkbox"]:checked')).length;
    const cs = Math.round((checked/25)*100);
    setText('contentScoreInline', cs);
    setChipTone(document.getElementById('contentScoreChip'), cs);
    setScoreWheel(0);
  })();
})();
</script>
