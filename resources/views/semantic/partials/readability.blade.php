{{-- resources/views/semantic/partials/readability.blade.php --}}
@php
  $R = $readability ?? [];
  $score     = (int)    ($R['score']                ?? 0);
  $grade     =          ($R['grade']                ?? null);
  $badge     =          ($R['badge']                ?? ($score>=80?'Very Easy To Read':($score>=60?'Good — Needs More Improvement':'Needs Improvement in Content')));
  $langNote  =          ($R['language']             ?? 'latin-like');
  $flesch    = is_null($R['flesch'] ?? null) ? null : (float) $R['flesch'];
  $asl       =          ($R['avg_sentence_len']     ?? null);     // words / sentence
  $ttr       =          ($R['ttr']                  ?? null);     // %
  $rep       =          ($R['repetition_trigram']   ?? null);     // %
  $digits100 =          ($R['digits_per_100w']      ?? null);     // per 100 words
  $simple    =          ($R['simple_words_ratio']   ?? null);     // %
  $passive   =          ($R['passive_ratio']        ?? null);     // %
  $wordCount =          ($R['word_count']           ?? null);     // shown only if provided

  $band = $score>=80 ? 'good' : ($score>=60 ? 'warn' : 'bad');

  // helper for 0..100 widths
  $pct = function($v,$min,$max,$invert=false){
      if ($v===null) return 0;
      $p = ($v - $min) / max(1, $max - $min) * 100;
      $p = max(0, min(100, $p));
      return (int) round($invert ? 100-$p : $p);
  };

  // meter widths (tuned for useful ranges)
  $wFlesch = $pct($flesch, 0, 100, false);
  $wASL    = $pct($asl,   10, 30,  true);      // shorter is better
  $wTTR    = $pct($ttr,    0,100, false);
  $wRep    = $pct($rep,    0, 20,  true);      // less repetition is better
  $wDig    = $pct($digits100,0, 20, true);     // fewer digits is better
  $wSimple = $pct($simple, 60,100, false);
  $wWords  = $wordCount ? $pct($wordCount, 0, 2000, false) : null;

  // dynamic note
  $note = 'Multilingual analysis'.($langNote==='non-latin'?' (LIX-based)':' (Flesch & grade blend)').'.';
  if ($grade !== null) {
    if ($grade <= 7)      { $gradeMsg = 'Easy to read (Grade '.$grade.'). Clear and accessible.'; }
    elseif ($grade <= 10) { $gradeMsg = 'Good for general audiences (Grade '.$grade.').'; }
    else                  { $gradeMsg = 'Complex reading level (Grade '.$grade.'). Prefer shorter sentences & simpler words.'; }
  } else {
    $gradeMsg = 'Readability score helps you target Grade 7–9 for most audiences.';
  }

  // simple, actionable fixes based on metrics
  $fixes = [];
  if ($asl !== null && $asl > 20)       $fixes[] = 'Break long sentences into 12–16 words.';
  if ($simple !== null && $simple < 80) $fixes[] = 'Prefer simpler words (clearer synonyms).';
  if ($digits100 !== null && $digits100 > 10) $fixes[] = 'Reduce numeric density; round or group numbers.';
  if ($passive !== null && $passive > 15)     $fixes[] = 'Reduce passive voice; switch to active where possible.';
  if ($rep !== null && $rep > 10)       $fixes[] = 'Trim repeated phrases; vary examples and wording.';

  if (count($fixes) < 3) {
      $fixes = array_values(array_unique(array_merge($fixes, [
          'Add headings and bullets to chunk information.',
          'Use image captions to explain visuals succinctly.',
          'Front-load key points; keep paragraphs short (2–4 lines).'
      ])));
  }

  // tile helper
  $tiles = array_values(array_filter([
      ['icon'=>'😊','label'=>'Flesch Reading Ease','val'=>$flesch,'suffix'=>'','w'=>$wFlesch],
      ['icon'=>'📝','label'=>'Avg Sentence Length','val'=>$asl,'suffix'=>'','w'=>$wASL],
      $wordCount!==null ? ['icon'=>'🔤','label'=>'Words','val'=>$wordCount,'suffix'=>'','w'=>$wWords] : null,
      ['icon'=>'🔁','label'=>'Lexical Diversity (TTR)','val'=>$ttr,'suffix'=>'%','w'=>$wTTR],
      ['icon'=>'♻️','label'=>'Repetition (tri-gram)','val'=>$rep,'suffix'=>'%','w'=>$wRep],
      ['icon'=>'#','label'=>'Digits / 100 words','val'=>$digits100,'suffix'=>'','w'=>$wDig],
      $simple!==null ? ['icon'=>'✨','label'=>'Simple Words','val'=>$simple,'suffix'=>'%','w'=>$wSimple] : null,
  ]));
@endphp

<style>
/* ---------- Readability block (scoped) ---------- */
.rb-wrap{--rb-bg:#0d0e1e;--rb-panel:#111e2f;--rb-border:rgba(255,255,255,.12);--rb-fg:#e6e9f2}
.rb-card{background:var(--rb-bg);border:1px solid var(--rb-border);border-radius:18px;padding:18px}
.rb-h{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.rb-h .rb-ico{width:36px;height:36px;display:grid;place-items:center;border-radius:10px;
  background:linear-gradient(135deg,rgba(99,102,241,.3),rgba(236,72,153,.25));border:1px solid var(--rb-border)}
.rb-title{font-weight:900;font-size:20px;background:linear-gradient(90deg,#67e8f9,#a78bfa,#fb7185,#f59e0b,#22c55e);
  -webkit-background-clip:text;background-clip:text;color:transparent}
.rb-badge{padding:6px 10px;border-radius:9999px;font-size:12px;font-weight:800;border:1px solid var(--rb-border)}
.rb-badge.good{background:rgba(16,185,129,.15);color:#a7f3d0;border-color:rgba(16,185,129,.35)}
.rb-badge.warn{background:rgba(245,158,11,.15);color:#fde68a;border-color:rgba(245,158,11,.35)}
.rb-badge.bad{ background:rgba(239,68,68,.15); color:#fecaca; border-color:rgba(239,68,68,.35)}

.rb-grid{display:grid;grid-template-columns:260px 1fr;gap:16px}
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
.rb-num{position:absolute;inset:0;display:grid;place-items:center;font-size:42px;font-weight:900;color:#fff}

.rb-tiles{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px}
@media (max-width:900px){.rb-tiles{grid-template-columns:1fr}}

.rb-tile{background:var(--rb-panel);border:1px solid var(--rb-border);border-radius:16px;padding:14px}
.rb-label{display:flex;align-items:center;gap:8px;font-weight:800}
.rb-metric{font-size:22px;font-weight:900;margin-top:6px}
.rb-meter{margin-top:8px;height:8px;border-radius:9999px;background:rgba(255,255,255,.08);overflow:hidden;border:1px solid var(--rb-border)}
.rb-meter>span{display:block;height:100%;width:0%;transition:width .9s ease;
  background:linear-gradient(90deg,#ef4444,#fde047,#22c55e)}

.rb-simple{background:var(--rb-panel);border:1px solid var(--rb-border);border-radius:16px;padding:14px}
.rb-simple h4{margin:0 0 8px 0;font-weight:900}
.rb-ul{margin:0;padding-left:18px}
.rb-ul li{margin:6px 0}

.rb-band{margin-top:12px;background:linear-gradient(90deg,rgba(34,197,94,.18),rgba(16,185,129,.12));
  border:1px solid rgba(34,197,94,.3);color:#d1fae5;border-radius:14px;padding:12px;font-weight:700}
.rb-band.bad{background:rgba(239,68,68,.12);border-color:rgba(239,68,68,.35);color:#fecaca}
.rb-band.warn{background:rgba(245,158,11,.12);border-color:rgba(245,158,11,.35);color:#fde68a}

/* footnote */
.rb-note{font-size:12px;color:#a8b1c7;margin-top:6px}
</style>

<div class="rb-wrap rb-card" id="rb-root">
  <div class="rb-h">
    <div class="rb-ico">📚</div>
    <div>
      <div class="rb-title">Readability Insights</div>
      <div class="rb-note">{{ $note }}</div>
    </div>
    <div class="rb-badge {{ $band }}" style="margin-left:auto">{{ $badge }}</div>
  </div>

  <div class="rb-grid">
    {{-- Score wheel --}}
    <div class="rb-card" style="background:var(--rb-panel)">
      <div class="rb-wheel {{ $band }}" id="rbWheel" style="--p: {{ $score }};">
        <div class="rb-ring"></div>
        <div class="rb-fill"></div>
        <div class="rb-num">{{ $score }}%</div>
      </div>
      <div class="rb-note" style="text-align:center;margin-top:8px">
        Grade {{ $grade ?? '—' }} · Language: {{ $langNote==='non-latin'?'Non-Latin (LIX)':'Latin-like' }}
      </div>
    </div>

    {{-- Metrics --}}
    <div class="rb-tiles">
      @foreach($tiles as $t)
        <div class="rb-tile">
          <div class="rb-label">
            <span>{{ $t['icon'] }}</span>
            <span>{{ $t['label'] }}</span>
          </div>
          <div class="rb-metric">
            {{ is_null($t['val']) ? '—' : number_format($t['val'], is_float($t['val']) && floor($t['val'])!=$t['val'] ? 1 : 0) }}{{ $t['suffix'] }}
          </div>
          <div class="rb-meter"><span style="width: {{ $t['w'] }}%"></span></div>
        </div>
      @endforeach
    </div>
  </div>

  {{-- Simple fixes --}}
  <div class="rb-simple" style="margin-top:14px">
    <h4>💡 Simple Fixes</h4>
    <ul class="rb-ul">
      @foreach($fixes as $fx)
        <li>✅ {{ $fx }}</li>
      @endforeach
    </ul>
  </div>

  {{-- Grade banner --}}
  @php
    $bandClass = $grade!==null ? ($grade<=7?'good':($grade<=10?'warn':'bad')) : 'warn';
  @endphp
  <div class="rb-band {{ $bandClass }}" role="status" aria-live="polite">
    {{ $gradeMsg }}
  </div>
</div>

<script>
  // Nothing heavy here—just ensure the bars/wheel animate when inserted.
  (function(){
    const root = document.getElementById('rb-root');
    if(!root) return;
    // trigger reflow for CSS transitions
    requestAnimationFrame(()=> {
      root.querySelectorAll('.rb-meter>span').forEach(el=>{
        const w = el.style.width; el.style.width = '0%';
        requestAnimationFrame(()=> el.style.width = w);
      });
      const wheel = document.getElementById('rbWheel');
      if(wheel){
        const p = wheel.style.getPropertyValue('--p') || '0';
        wheel.style.setProperty('--p','0');
        requestAnimationFrame(()=> wheel.style.setProperty('--p', p));
      }
    });
  })();
</script>
