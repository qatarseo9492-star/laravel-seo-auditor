{{-- resources/views/semantic/partials/readability.blade.php --}}
@php
  $R = $readability ?? [];

  // Base values (server-side if available; JS will refresh after Analyze)
  $score     = (int)    ($R['score']                ?? 0);
  $grade     =          ($R['grade']                ?? null);
  $badge     =          ($R['badge']                ?? ($score>=80?'Very Easy To Read':($score>=60?'Good — Needs More Improvement':'Needs Improvement in Content')));
  $langNote  =          ($R['language']             ?? 'latin-like');

  $flesch    = is_null($R['flesch'] ?? null) ? null : (float) $R['flesch'];
  $asl       =          ($R['avg_sentence_len']     ?? null);     // words / sentence
  $asw       =          ($R['asw']                  ?? null);     // syllables / word (if present)
  $ttr       =          ($R['ttr']                  ?? null);     // %
  $rep       =          ($R['repetition_trigram']   ?? null);     // %
  $digits100 =          ($R['digits_per_100w']      ?? null);     // per 100 words
  $simple    =          ($R['simple_words_ratio']   ?? null);     // %
  $passive   =          ($R['passive_ratio']        ?? null);     // %
  $words     =          ($R['words'] ?? $R['word_count'] ?? $R['words_count'] ?? null);

  $band = $score>=80 ? 'good' : ($score>=60 ? 'warn' : 'bad');

  $pct = function($v,$min,$max,$invert=false){
      if ($v===null || $v==='') return 0;
      $p = ($v - $min) / max(1, $max - $min) * 100;
      $p = max(0, min(100, $p));
      return (int) round($invert ? 100-$p : $p);
  };

  // initial widths (JS will animate to latest after Analyze)
  $wFlesch = $pct($flesch, 0, 100, false);
  $wASL    = $pct($asl,   10, 30,  true);      // shorter is better
  $wASW    = $pct($asw,  1.2, 2.2, true);      // fewer syllables/word is easier
  $wTTR    = $pct($ttr,    0,100, false);
  $wRep    = $pct($rep,    0, 20,  true);      // less repetition is better
  $wDig    = $pct($digits100,0, 20, true);     // fewer digits is better
  $wSimple = $pct($simple, 60,100, false);
  $wWords  = $words!==null ? $pct($words, 0, 2000, false) : 0;

  // dynamic note & banner text
  $note = 'Multilingual analysis'.($langNote==='non-latin'?' (LIX-based)':' (Flesch & grade blend)').'.';
  if ($grade !== null) {
    if ($grade <= 7)      { $gradeMsg = 'Easy to read (Grade '.$grade.'). Clear and accessible.'; }
    elseif ($grade <= 10) { $gradeMsg = 'Good for general audiences (Grade '.$grade.').'; }
    else                  { $gradeMsg = 'Complex reading level (Grade '.$grade.'). Prefer shorter sentences & simpler words.'; }
  } else {
    $gradeMsg = 'Readability score helps you target Grade 7–9 for most audiences.';
  }

  // initial quick suggestions (JS refines after Analyze)
  $fixes = [];
  if ($asl !== null && $asl > 20)             $fixes[] = 'Break long sentences into 12–16 words.';
  if ($simple !== null && $simple < 80)       $fixes[] = 'Prefer simpler words (clearer synonyms).';
  if ($digits100 !== null && $digits100 > 10) $fixes[] = 'Reduce numeric density; round or group numbers.';
  if ($passive !== null && $passive > 15)     $fixes[] = 'Reduce passive voice; switch to active where possible.';
  if ($rep !== null && $rep > 10)             $fixes[] = 'Trim repeated phrases; vary examples and wording.';
  if (count($fixes) < 3) {
      $fixes = array_values(array_unique(array_merge($fixes, [
          'Add headings and bullets to chunk information.',
          'Use image captions to explain visuals succinctly.',
          'Front-load key points; keep paragraphs short (2–4 lines).'
      ])));
  }

  $bandClass = $grade!==null ? ($grade<=7?'good':($grade<=10?'warn':'bad')) : 'warn';
@endphp

<style>
/* ---------- Readability block (scoped to this partial) ---------- */
.rb-wrap{
  --rb-bg:#0c0f1e;           /* widget background */
  --rb-panel:#111a2a;        /* inner cards */
  --rb-border:rgba(255,255,255,.12);
  --rb-fg:#e6e9f2;
  position:relative;
  border-radius:20px;
  /* animated multicolor outline with glow */
  border:1px solid transparent;
  background:
    linear-gradient(var(--rb-bg),var(--rb-bg)) padding-box,
    conic-gradient(from 0deg,#22d3ee,#a78bfa,#f472b6,#fb7185,#f59e0b,#22c55e,#22d3ee) border-box;
  animation:rbHue 1.2s linear infinite;  /* quick color orbit ~ every second */
  box-shadow:0 0 18px rgba(0,0,0,.35), 0 0 22px rgba(99,102,241,.12), 0 0 28px rgba(34,197,94,.10);
  color:var(--rb-fg);
}
@keyframes rbHue{to{filter:hue-rotate(360deg)}}

.rb-card{background:transparent;padding:18px}
.rb-h{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.rb-h .rb-ico{width:38px;height:38px;display:grid;place-items:center;border-radius:10px;
  background:linear-gradient(135deg,rgba(99,102,241,.35),rgba(236,72,153,.28));border:1px solid var(--rb-border)}
.rb-title{font-weight:900;font-size:20px;background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);
  -webkit-background-clip:text;background-clip:text;color:transparent}
.rb-badge{padding:6px 10px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid var(--rb-border)}
.rb-badge.good{background:rgba(16,185,129,.18);color:#a7f3d0;border-color:rgba(16,185,129,.4)}
.rb-badge.warn{background:rgba(245,158,11,.18);color:#fde68a;border-color:rgba(245,158,11,.4)}
.rb-badge.bad{ background:rgba(239,68,68,.18); color:#fecaca; border-color:rgba(239,68,68,.4)}

.rb-grid{display:grid;grid-template-columns:240px 1fr;gap:16px}
@media (max-width:900px){.rb-grid{grid-template-columns:1fr}}

.rb-wheel{--p:0;--ring:#f59e0b;width:200px;height:200px;margin:auto;position:relative}
.rb-wheel.good{--ring:#22c55e}.rb-wheel.warn{--ring:#f59e0b}.rb-wheel.bad{--ring:#ef4444}
.rb-ring{position:absolute;inset:0;border-radius:50%;
  background:conic-gradient(var(--ring) calc(var(--p)*1%),rgba(255,255,255,.08) 0);
  -webkit-mask:radial-gradient(circle 78px,transparent 100px,#000 100px);
  mask:radial-gradient(circle 78px,transparent 100px,#000 100px);
  box-shadow:inset 0 0 0 12px rgba(255,255,255,.06)}
.rb-fill{position:absolute;inset:22px;border-radius:50%;overflow:hidden;background:#000}
.rb-fill::after{content:"";position:absolute;left:0;right:0;height:100%;
  top:calc(100% - var(--p)*1%);transition:top .9s ease;
  background:var(--rb-water,linear-gradient(to top,#f59e0b,#fde68a));
  -webkit-mask:radial-gradient(120px 18px at 50% 0,#0000 98%,#000 100%); mask:radial-gradient(120px 18px at 50% 0,#0000 98%,#000 100%)}
.rb-wheel.good .rb-fill{--rb-water:linear-gradient(to top,#16a34a,#86efac)}
.rb-wheel.warn .rb-fill{--rb-water:linear-gradient(to top,#f59e0b,#fde68a)}
.rb-wheel.bad  .rb-fill{--rb-water:linear-gradient(to top,#ef4444,#fecaca)}
.rb-num{position:absolute;inset:0;display:grid;place-items:center;font-size:40px;font-weight:900;color:#fff}

.rb-tiles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
@media (max-width:900px){.rb-tiles{grid-template-columns:1fr}}

.rb-tile{background:var(--rb-panel);border:1px solid var(--rb-border);border-radius:16px;padding:14px}
.rb-label{display:flex;align-items:center;gap:8px;font-weight:900}
.rb-metric{font-size:22px;font-weight:900;margin-top:6px}
.rb-meter{margin-top:8px;height:8px;border-radius:9999px;background:rgba(255,255,255,.08);overflow:hidden;border:1px solid var(--rb-border)}
.rb-meter>span{display:block;height:100%;width:0%;transition:width .9s ease;
  background:linear-gradient(90deg,#ef4444,#fde047,#22c55e)}

.rb-simple{background:var(--rb-panel);border:1px solid var(--rb-border);border-radius:16px;padding:14px}
.rb-simple h4{margin:0 0 8px 0;font-weight:900}
.rb-ul{margin:0;padding-left:18px}
.rb-ul li{margin:6px 0}

.rb-band{margin-top:12px;background:linear-gradient(90deg,rgba(34,197,94,.18),rgba(16,185,129,.12));
  border:1px solid rgba(34,197,94,.35);color:#d1fae5;border-radius:14px;padding:12px;font-weight:800}
.rb-band.bad{background:rgba(239,68,68,.14);border-color:rgba(239,68,68,.4);color:#fecaca}
.rb-band.warn{background:rgba(245,158,11,.14);border-color:rgba(245,158,11,.4);color:#fde68a}

.rb-note{font-size:12px;color:#a8b1c7;margin-top:6px}
</style>

<div class="rb-wrap rb-card" id="rb-root">
  <div class="rb-h">
    <div class="rb-ico">📚</div>
    <div>
      <div class="rb-title">Readability Insights</div>
      <div class="rb-note" id="rb-note">{{ $note }}</div>
    </div>
    <div class="rb-badge {{ $band }}" id="rb-badge" style="margin-left:auto">{{ $badge }}</div>
  </div>

  <div class="rb-grid">
    {{-- Score wheel --}}
    <div class="rb-card" style="background:transparent">
      <div class="rb-wheel {{ $band }}" id="rbWheel" style="--p: {{ $score }};">
        <div class="rb-ring"></div>
        <div class="rb-fill"></div>
        <div class="rb-num" id="rb-score">{{ $score }}%</div>
      </div>
      <div class="rb-note" style="text-align:center;margin-top:8px" id="rb-grade-note">
        Grade {{ $grade ?? '—' }} · Language: {{ $langNote==='non-latin'?'Non-Latin (LIX)':'Latin-like' }}
      </div>
    </div>

    {{-- Metrics (all with ids so JS can update after Analyze) --}}
    <div class="rb-tiles">
      <div class="rb-tile"><div class="rb-label">😊 Flesch Reading Ease</div>
        <div class="rb-metric" id="rb-flesch">{{ is_null($flesch)?'—':number_format($flesch,1) }}</div>
        <div class="rb-meter"><span id="bar-flesch" style="width: {{ $wFlesch }}%"></span></div>
      </div>

      <div class="rb-tile"><div class="rb-label">📝 Avg Sentence Length</div>
        <div class="rb-metric" id="rb-asl">{{ is_null($asl)?'—':number_format($asl,1) }}</div>
        <div class="rb-meter"><span id="bar-asl" style="width: {{ $wASL }}%"></span></div>
      </div>

      <div class="rb-tile"><div class="rb-label">🔤 Words</div>
        <div class="rb-metric" id="rb-words">{{ is_null($words)?'—':number_format($words) }}</div>
        <div class="rb-meter"><span id="bar-words" style="width: {{ $wWords }}%"></span></div>
      </div>

      <div class="rb-tile"><div class="rb-label">🔡 Syllables / Word</div>
        <div class="rb-metric" id="rb-asw">{{ is_null($asw)?'—':number_format($asw,2) }}</div>
        <div class="rb-meter"><span id="bar-asw" style="width: {{ $wASW }}%"></span></div>
      </div>

      <div class="rb-tile"><div class="rb-label">🔁 Lexical Diversity (TTR)</div>
        <div class="rb-metric" id="rb-ttr">{{ is_null($ttr)?'—':number_format($ttr,0) }}{{ is_null($ttr)?'':'%' }}</div>
        <div class="rb-meter"><span id="bar-ttr" style="width: {{ $wTTR }}%"></span></div>
      </div>

      <div class="rb-tile"><div class="rb-label">♻️ Repetition (tri-gram)</div>
        <div class="rb-metric" id="rb-tri">{{ is_null($rep)?'—':number_format($rep,0) }}{{ is_null($rep)?'':'%' }}</div>
        <div class="rb-meter"><span id="bar-tri" style="width: {{ $wRep }}%"></span></div>
      </div>

      <div class="rb-tile"><div class="rb-label"># Digits / 100 words</div>
        <div class="rb-metric" id="rb-digits">{{ is_null($digits100)?'—':number_format($digits100,0) }}</div>
        <div class="rb-meter"><span id="bar-digits" style="width: {{ $wDig }}%"></span></div>
      </div>

      <div class="rb-tile"><div class="rb-label">✨ Simple Words</div>
        <div class="rb-metric" id="rb-simple">{{ is_null($simple)?'—':number_format($simple,0) }}{{ is_null($simple)?'':'%' }}</div>
        <div class="rb-meter"><span id="bar-simple" style="width: {{ $wSimple }}%"></span></div>
      </div>

      <div class="rb-tile"><div class="rb-label">🧩 Passive voice</div>
        <div class="rb-metric" id="rb-passive">{{ is_null($passive)?'—':number_format($passive,0) }}{{ is_null($passive)?'':'%' }}</div>
        <div class="rb-meter"><span id="bar-passive" style="width: {{ $pct($passive,0,40,true) }}%"></span></div>
      </div>
    </div>
  </div>

  {{-- Simple fixes --}}
  <div class="rb-simple" style="margin-top:14px">
    <h4>💡 Simple Fixes</h4>
    <ul class="rb-ul" id="rb-fixes">
      @foreach($fixes as $fx)
        <li>✅ {{ $fx }}</li>
      @endforeach
    </ul>
  </div>

  {{-- Grade banner --}}
  <div class="rb-band {{ $bandClass }}" id="rb-grade-banner" role="status" aria-live="polite">
    {{ $gradeMsg }}
  </div>
</div>

<script>
(function(){
  const $ = s => document.querySelector(s);
  const clamp = (n,min,max)=> Math.max(min, Math.min(max, n));
  const pct = (v,min,max,invert=false)=>{
    if (v===null || v===undefined) return 0;
    let p = ((v-min)/Math.max(1,(max-min)))*100;
    p = clamp(p,0,100);
    return invert? 100-p : p;
  };
  const band = s=> s>=80?'good':(s>=60?'warn':'bad');

  function fmt(v,dec=1){ if(v===null||v===undefined) return '—'; const f=Number(v); return Number.isInteger(f)? String(f) : f.toFixed(dec); }

  function applyFixes(R){
    const fixes=[];
    if (R.avg_sentence_len>20) fixes.push('Break long sentences into 12–16 words.');
    if (R.simple_words_ratio!==null && R.simple_words_ratio<80) fixes.push('Prefer simpler words (clearer synonyms).');
    if (R.digits_per_100w>10) fixes.push('Reduce numeric density; round or group numbers.');
    if (R.passive_ratio>15) fixes.push('Reduce passive voice; switch to active where possible.');
    if (R.repetition_trigram>10) fixes.push('Trim repeated phrases; vary examples and wording.');
    if (!fixes.length) fixes.push('Great job! Maintain short sentences and clear, concrete wording.');
    const ul = $('#rb-fixes'); if(!ul) return;
    ul.innerHTML = fixes.map(t=>`<li>✅ ${t}</li>`).join('');
  }

  function updateFrom(r){
    if(!r) return;

    // pick best keys
    const words = r.words ?? r.word_count ?? r.words_count ?? null;
    const asw   = r.asw ?? null;
    const flesch= (r.flesch===null || r.flesch===undefined)? null : r.flesch;

    // meters
    $('#rb-flesch') && ($('#rb-flesch').textContent = flesch===null?'—':fmt(flesch,1));
    $('#bar-flesch') && ($('#bar-flesch').style.width = pct(flesch,0,100,false)+'%');

    $('#rb-asl') && ($('#rb-asl').textContent = fmt(r.avg_sentence_len,1));
    $('#bar-asl') && ($('#bar-asl').style.width = pct(r.avg_sentence_len,10,30,true)+'%');

    $('#rb-words') && ($('#rb-words').textContent = words===null?'—':new Intl.NumberFormat().format(words));
    $('#bar-words') && ($('#bar-words').style.width = pct(words??0,0,2000,false)+'%');

    $('#rb-asw') && ($('#rb-asw').textContent = asw===null?'—':fmt(asw,2));
    $('#bar-asw') && ($('#bar-asw').style.width = pct(asw??0,1.2,2.2,true)+'%');

    $('#rb-ttr') && ($('#rb-ttr').textContent = (r.ttr===null||r.ttr===undefined)?'—':Math.round(r.ttr)+'%');
    $('#bar-ttr') && ($('#bar-ttr').style.width = pct(r.ttr??0,0,100,false)+'%');

    $('#rb-tri') && ($('#rb-tri').textContent = (r.repetition_trigram===null||r.repetition_trigram===undefined)?'—':Math.round(r.repetition_trigram)+'%');
    $('#bar-tri') && ($('#bar-tri').style.width = pct(r.repetition_trigram??0,0,20,true)+'%');

    $('#rb-digits') && ($('#rb-digits').textContent = r.digits_per_100w===null?'—':Math.round(r.digits_per_100w));
    $('#bar-digits') && ($('#bar-digits').style.width = pct(r.digits_per_100w??0,0,20,true)+'%');

    $('#rb-simple') && ($('#rb-simple').textContent = (r.simple_words_ratio===null||r.simple_words_ratio===undefined)?'—':Math.round(r.simple_words_ratio)+'%');
    $('#bar-simple') && ($('#bar-simple').style.width = pct(r.simple_words_ratio??0,60,100,false)+'%');

    $('#rb-passive') && ($('#rb-passive').textContent = (r.passive_ratio===null||r.passive_ratio===undefined)?'—':Math.round(r.passive_ratio)+'%');
    $('#bar-passive') && ($('#bar-passive').style.width = pct(r.passive_ratio??0,0,40,true)+'%');

    // wheel + badges
    const s = clamp(Number(r.score||0),0,100);
    const wb = band(s);
    const wheel = $('#rbWheel'); const scoreNum = $('#rb-score');
    wheel && (wheel.className = 'rb-wheel '+wb);
    wheel && wheel.style.setProperty('--p','0');
    scoreNum && (scoreNum.textContent = s+'%');
    requestAnimationFrame(()=> wheel && wheel.style.setProperty('--p', s));

    const badge = $('#rb-badge');
    if(badge){ badge.className = 'rb-badge '+wb; badge.textContent = r.badge || (s>=80?'Very Easy To Read':(s>=60?'Good — Needs More Improvement':'Needs Improvement in Content')); }

    const note = $('#rb-note');
    if(note){ note.textContent = 'Multilingual analysis ' + (r.language==='non-latin' ? '(LIX-based).' : '(Flesch & grade blend).'); }

    const g = r.grade;
    const banner = $('#rb-grade-banner');
    const bandClass = (g!==null && g!==undefined) ? (g<=7?'good':(g<=10?'warn':'bad')) : 'warn';
    if (banner){
      banner.className = 'rb-band '+bandClass;
      banner.textContent = (g!==null && g!==undefined)
        ? (g<=7?'Easy to read (Grade '+g+'). Clear and accessible.'
          : (g<=10?'Good for general audiences (Grade '+g+').'
          : 'Complex reading level (Grade '+g+'). Prefer shorter sentences & simpler words.'))
        : 'Readability score helps you target Grade 7–9 for most audiences.';
    }

    const gradeNote = $('#rb-grade-note');
    if(gradeNote){ gradeNote.textContent = 'Grade '+(g??'—')+' · Language: '+(r.language==='non-latin'?'Non-Latin (LIX)':'Latin-like'); }

    applyFixes(r);
  }

  // initial (server-provided) render is already visible; now hook Analyze
  function tryUpdateOnce(){
    if (window.__lastData && window.__lastData.readability){
      updateFrom(window.__lastData.readability);
      return true;
    }
    return false;
  }

  // update immediately if data already exists
  if(!tryUpdateOnce()){
    // start a short poll after Analyze is clicked (no changes to your main JS required)
    const btn = document.getElementById('analyzeBtn');
    if(btn){
      btn.addEventListener('click', ()=>{
        const t0 = Date.now();
        const timer = setInterval(()=>{
          if (tryUpdateOnce() || Date.now()-t0 > 30000) clearInterval(timer);
        }, 300);
      });
    } else {
      // fallback: brief background polling for SPA-like flows
      const t0 = Date.now();
      const poll = setInterval(()=>{ if (tryUpdateOnce() || Date.now()-t0>15000) clearInterval(poll); }, 500);
    }
  }
})();
</script>
