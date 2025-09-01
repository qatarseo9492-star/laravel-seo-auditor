{{-- resources/views/components/human-ai-ensemble.blade.php
     Drop this component into any Blade page with:  @include('components.human-ai-ensemble')
     JS API:
       setEnsembleScores({
         human: 72, ai: 28,
         confidence: 0.86, // 0–1
         verdict: 'Likely Human', // optional; auto-derived if omitted
         models: [
           { name: 'OpenAI Detector',   humanProb: 0.71, aiProb: 0.29 },
           { name: 'GPTZero',           humanProb: 0.66, aiProb: 0.34 },
           { name: 'Cross-Entropy',     humanProb: 0.78, aiProb: 0.22 },
           { name: 'Burstiness Heur.',  humanProb: 0.75, aiProb: 0.25 },
         ]
       });
--}}

<section id="ai-ensemble" class="panel panel-ensemble" data-component="human-ai-ensemble">
  <div class="panel-head">
    <div class="panel-title">
      <i class="fa-solid fa-user-robot"></i>
      <span>Human vs AI Content — <em>Ensemble</em></span>
    </div>
    <div class="badge verdict" id="ensemble-verdict">—</div>
  </div>

  <div class="panel-body grid">
    <!-- Gauges -->
    <div class="gauges">
      <div class="gauge-card">
        <div class="gauge" data-kind="human">
          <svg viewBox="0 0 120 120" class="ring">
            <circle class="track" cx="60" cy="60" r="52" />
            <circle class="bar human" cx="60" cy="60" r="52" stroke-dasharray="0 360" />
          </svg>
          <div class="gauge-center">
            <div class="gauge-label">Human</div>
            <div class="gauge-value" id="human-val">0%</div>
          </div>
        </div>
      </div>

      <div class="gauge-card">
        <div class="gauge" data-kind="ai">
          <svg viewBox="0 0 120 120" class="ring">
            <circle class="track" cx="60" cy="60" r="52" />
            <circle class="bar ai" cx="60" cy="60" r="52" stroke-dasharray="0 360" />
          </svg>
          <div class="gauge-center">
            <div class="gauge-label">AI</div>
            <div class="gauge-value" id="ai-val">0%</div>
          </div>
        </div>
      </div>

      <div class="gauge-card">
        <div class="confidence">
          <div class="conf-label">Confidence</div>
          <div class="conf-bar">
            <div class="conf-fill" id="conf-fill" style="width:0%"></div>
          </div>
          <div class="conf-value" id="conf-val">0%</div>
        </div>
        <div class="legend">
          <span><i class="fa-solid fa-circle" style="opacity:.85"></i> Human</span>
          <span><i class="fa-solid fa-circle" style="opacity:.5"></i> AI</span>
        </div>
      </div>
    </div>

    <!-- Model votes -->
    <div class="models">
      <div class="models-head">
        <i class="fa-solid fa-wave-square"></i>
        <span>Model Votes & Signals</span>
      </div>
      <ul class="model-list" id="model-list">
        {{-- Filled by JS --}}
      </ul>
      <div class="model-note">
        <i class="fa-regular fa-lightbulb"></i>
        Ensemble blends probabilistic outputs from multiple detectors to reduce single-model bias.
      </div>
    </div>

    <!-- Explanations -->
    <div class="explain">
      <details class="exp-card" open>
        <summary><i class="fa-solid fa-flask"></i> How we decide</summary>
        <div class="exp-body">
          <p>
            We compute a weighted average of Human and AI probabilities across several detectors and heuristics.
            This gives a more stable estimate than relying on any single tool.
          </p>
          <ul class="exp-points">
            <li><strong>Calibrated Probabilities:</strong> Each model’s score is normalized to the same scale.</li>
            <li><strong>Robustness:</strong> Outliers are clipped with winsorization to resist extreme claims.</li>
            <li><strong>Confidence:</strong> Based on agreement between models and signal quality (length, entropy).</li>
          </ul>
        </div>
      </details>

      <details class="exp-card">
        <summary><i class="fa-solid fa-sparkles"></i> Improve Human-likeness</summary>
        <div class="exp-body tips">
          <ul>
            <li>Mix sentence lengths; vary structure and rhythm.</li>
            <li>Add specific details, sources, and lived-experience context.</li>
            <li>Prefer domain vocabulary over generic adjectives.</li>
            <li>Break up template-like phrasing and boilerplate openings.</li>
            <li>Trim excessive hedging or repetitive transitions.</li>
          </ul>
        </div>
      </details>
    </div>
  </div>
</section>
