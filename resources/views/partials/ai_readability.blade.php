{{-- AI Readability & Humanizer (ADD-ON PARTIAL) --}}
{{-- Safe-scoped: no global layout changes. Styles are scoped under .ai-humanizer --}}
<div class="ai-humanizer">
  <section class="aih-wrap">
    <div class="aih-card aih-glow aih-row">
      <div>
        <div class="aih-title">AI Readability &amp; Humanizer</div>
        <div class="aih-muted">Dark section • Glow outlines • Animated buttons &amp; icons</div>
      </div>
      <button id="aihAnalyzeBtn" class="aih-btn">
        <span class="aih-spin" id="aihBtnSpin" style="display:none"></span>
        <span>Analyze URL</span>
      </button>
    </div>

    <div class="aih-card" style="margin-top:14px">
      <label class="aih-muted" for="aihUrl">URL to analyze</label>
      <input id="aihUrl" type="url" placeholder="https://example.com/post" class="aih-input">
      <div id="aihProgressMsg" class="aih-muted" style="margin-top:8px;display:none"></div>
    </div>

    <div id="aihResults" class="aih-card aih-glow" style="margin-top:14px;display:none">
      <div class="aih-badgerow">
        <span id="aihBadge" class="aih-badge aih-green">
          <span class="aih-tick">
            <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
          </span>
          <span id="aihBadgeText">Great job! This content reads like it was written by a human.</span>
        </span>

        <span class="aih-badge" id="aihHumanBadge" data-kind="human">Human-like: <strong id="aihHumanScore">—</strong></span>
        <span class="aih-badge" id="aihAiBadge" data-kind="ai">AI-like: <strong id="aihAiScore">—</strong></span>
        <span class="aih-badge" id="aihReadBadge" data-kind="read">Readability: <strong id="aihReadability">—</strong></span>
        <span class="aih-badge" id="aihPassiveBadge" data-kind="passive">Passive: <strong id="aihPassive">—</strong></span>
      </div>

      <div class="aih-muted aih-strong">Suggestions to sound more human</div>
      <ul id="aihTips" class="aih-tips"></ul>
    </div>
  </section>
</div>

@push('head')
<style>
  /* ===== Scoped styles (won't affect rest of your 1300-line file) ===== */
  .ai-humanizer{ --aih-bg:#0b0f1a; --aih-card:#0f1422; --aih-br:#1f2a44; --aih-ink:#e5e7eb; --aih-muted:#98a2b3; --aih-grad:linear-gradient(90deg,#60a5fa,#22d3ee,#34d399,#fbbf24,#fb7185,#a78bfa,#60a5fa); }
  .ai-humanizer .aih-wrap{max-width:1100px;margin:24px auto;padding:0 12px;color:var(--aih-ink)}
  .ai-humanizer .aih-card{background:var(--aih-card);border:1px solid var(--aih-br);border-radius:18px;padding:16px;position:relative;box-shadow:0 0 0 1px rgba(255,255,255,.03), 0 0 32px rgba(34,211,238,.08)}
  .ai-humanizer .aih-glow:before{content:"";position:absolute;inset:-1px;border-radius:18px;padding:1px;background:var(--aih-grad);-webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);-webkit-mask-composite: xor;mask-composite: exclude;box-shadow:0 0 30px rgba(34,211,238,.25)}
  .ai-humanizer .aih-row{display:flex;align-items:center;justify-content:space-between;gap:14px;flex-wrap:wrap}
  .ai-humanizer .aih-title{font-size:20px;font-weight:900;background:var(--aih-grad);-webkit-background-clip:text;background-clip:text;color:transparent;letter-spacing:.4px}
  .ai-humanizer .aih-muted{color:var(--aih-muted)}
  .ai-humanizer .aih-strong{margin-top:12px;font-weight:700}
  .ai-humanizer .aih-input{width:100%;margin-top:8px;padding:12px 14px;border-radius:12px;border:1px solid #25314c;background:#0d1321;color:var(--aih-ink)}

  .ai-humanizer .aih-btn{display:inline-flex;align-items:center;gap:10px;padding:10px 16px;border-radius:12px;border:1px solid #ffffff22;background:var(--aih-grad);background-size:300% 100%;color:#0b0f1a;font-weight:700;cursor:pointer;transition:transform .15s ease;animation:aih-slide 6s linear infinite}
  .ai-humanizer .aih-btn:hover{transform:translateY(-1px)}
  @keyframes aih-slide{to{background-position:300% 0}}
  .ai-humanizer .aih-spin{width:18px;height:18px;display:inline-block;border-radius:999px;border:3px solid #ffffff99;border-top-color:#fff;animation:aih-spin .9s linear infinite}
  @keyframes aih-spin{to{transform:rotate(360deg)}}

  .ai-humanizer .aih-badge{display:inline-flex;align-items:center;gap:8px;padding:6px 10px;border-radius:999px;font-weight:800;border:1px solid #ffffff14}
  .ai-humanizer #aihBadge.aih-green{background:#052e1a;color:#86efac;border-color:#16a34a;box-shadow:0 0 24px rgba(22,163,74,.25)}
  .ai-humanizer #aihBadge.aih-orange{background:#2f1a05;color:#fbbf24;border-color:#f59e0b;box-shadow:0 0 24px rgba(245,158,11,.25)}
  .ai-humanizer #aihBadge.aih-red{background:#300910;color:#fb7185;border-color:#ef4444;box-shadow:0 0 24px rgba(239,68,68,.25)}

  .ai-humanizer .aih-badge[data-kind="human"]{background:#0b2f2a;border-color:#10b981;color:#a7f3d0}
  .ai-humanizer .aih-badge[data-kind="ai"]{background:#2a0b28;border-color:#ec4899;color:#fbcfe8}
  .ai-humanizer .aih-badge[data-kind="read"]{background:#0b1c2f;border-color:#3b82f6;color:#bfdbfe}
  .ai-humanizer .aih-badge[data-kind="passive"]{background:#2f1a05;border-color:#f59e0b;color:#fde68a}

  .ai-humanizer .aih-badgerow{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
  .ai-humanizer .aih-tick{display:inline-grid;place-items:center;width:28px;height:28px;border-radius:999px;background:conic-gradient(from 0deg,var(--aih-grad));box-shadow:0 0 24px rgba(34,211,238,.25)}
  .ai-humanizer .aih-tick svg{filter:drop-shadow(0 0 6px rgba(255,255,255,.25))}

  .ai-humanizer .aih-tips{margin-top:8px;display:grid;gap:8px;padding-left:18px}
</style>
@endpush

@push('scripts')
<script src="{{ asset('js/ai_readability.js') }}"></script>
@endpush
