@extends('layouts.app')
@section('title','Semantic SEO Master Analyzer 2.0')

@section('content')
<style>
  /* Page theme + utilities */
  :root{
    --bg-1:#0b1220;--bg-2:#0f1630;--bg-3:#121631;
    --brand:#7c3aed; --brand2:#22d3ee; --ok:#10b981; --warn:#f59e0b; --bad:#ef4444;
    --muted:#a1a1aa; --panel:#111827; --panelBorder:rgba(255,255,255,.08);
  }
  body{
  background:
    radial-gradient(900px 600px at 85% -10%, rgba(34,211,238,.15), transparent 60%),
    radial-gradient(900px 600px at 10% 120%, rgba(244,63,94,.12), transparent 60%),
    linear-gradient(180deg, #070b1a, #0a1125 55%, #0a1125);
}

  .glass{ background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.04)); border:1px solid var(--panelBorder); backdrop-filter: blur(10px); }
  .chip{ font-size:.7rem; padding:.25rem .5rem; border-radius:.5rem; border:1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.06); }
  .title-grad{ background:linear-gradient(90deg,#22d3ee,#7c3aed 45%,#f43f5e 80%); -webkit-background-clip:text; background-clip:text; color:transparent; }
  .shadow-soft{ box-shadow: 0 14px 60px rgba(0,0,0,.22); }

  /* Score wheel */
  .wheel svg{ overflow:visible; }
  .wheel .track{ stroke: rgba(255,255,255,.12); }
  .wheel .bar{ stroke-linecap: round; filter: drop-shadow(0 4px 14px rgba(124,58,237,.35)); }

  /* Water bar canvas wraps */
  .water-wrap{ position:relative; overflow:hidden; border-radius:16px; border:1px solid var(--panelBorder); }
  .water-wrap canvas{ display:block; width:100%; height:100%; }
  .water-overlay{ position:absolute; inset:0; display:flex; align-items:center; justify-content:center; font-weight:800; letter-spacing:.5px; }

  /* Checklist */
  .check-item{ display:flex; align-items:flex-start; gap:.75rem; padding:.6rem .8rem; border-radius:14px; border:1px solid var(--panelBorder); background:rgba(255,255,255,.04); }
  .check-score{ min-width:46px; padding:.25rem .5rem; font-weight:700; border-radius:10px; text-align:center; font-size:.8rem; }
  .glow-ok{ box-shadow:0 0 0 4px rgba(16,185,129,.10), 0 0 18px rgba(16,185,129,.35) inset; }
  .glow-warn{ box-shadow:0 0 0 4px rgba(245,158,11,.10), 0 0 18px rgba(245,158,11,.35) inset; }
  .glow-bad{ box-shadow:0 0 0 4px rgba(239,68,68,.10), 0 0 18px rgba(239,68,68,.35) inset; }
  .cat-head{ font-weight:700; letter-spacing:.3px; }

  /* Modal */
  .modal{ position:fixed; inset:0; display:none; align-items:center; justify-content:center; background:rgba(0,0,0,.55); backdrop-filter: blur(6px); z-index:60; }
  .modal.open{ display:flex; }
  .modal-card{ width:min(680px,92vw); border-radius:20px; padding:1rem; }
  .modal a{ color:#60a5fa; text-decoration:underline; }

  /* Background tech lines canvas */
  

/* Static neon-wave background */
#bgWaves{position:fixed;inset:0;z-index:-1;width:100%;height:100%;pointer-events:none;}
</style>



<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 text-slate-100">
  <div class="flex items-center justify-between gap-4 mb-6">
    <div>
      <div class="chip">Analyzer</div>
      <h1 class="text-2xl sm:text-3xl font-extrabold mt-2">
        <span class="title-grad">Semantic SEO Master Analyzer 2.0</span>
      </h1>
      <p class="text-slate-300/90 mt-1">Analyze any URL for content, structure, technical signals, and entity context. Get a beautiful score wheel, quick stats, and actionable improvements.</p>
    </div>
  </div>

  <!-- Analyze form -->
  <form id="semanticForm" class="glass rounded-2xl p-4 sm:p-5 shadow-soft">
    <div class="grid gap-3 md:grid-cols-[1fr,280px]">
      <div class="grid gap-3 sm:grid-cols-[1fr,240px]">
        <input name="url" type="url" required placeholder="https://example.com/article"
               class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-cyan-400/50">
        <input name="target_keyword" type="text" placeholder="Target keyword (optional)"
               class="w-full px-4 py-3 rounded-xl bg-slate-900/60 border border-white/10 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-fuchsia-400/50">
      </div>
      <div>
        <button id="analyzeBtn" class="w-full px-4 py-3 rounded-xl bg-gradient-to-r from-cyan-400 via-fuchsia-500 to-violet-600 hover:from-cyan-300 hover:via-fuchsia-400 hover:to-violet-500 text-white font-semibold shadow-soft transition-all active:scale-[.99]" type="submit">
          Analyze URL
        </button>
      </div>
    </div>
    <!-- Water bar loader -->
    <div id="waterWrap" class="water-wrap h-20 mt-4 hidden">
      <canvas id="waterCanvas"></canvas>
      <div class="water-overlay text-xl"><span id="waterLabel">Loading… 0%</span></div>
    </div>
  </form>

  <!-- Results -->
  <div id="resultWrap" class="mt-8 hidden space-y-8">

    <!-- Top: Score wheel + badge and Quick Stats -->
    <div class="grid lg:grid-cols-3 gap-6">
      <!-- Wheel -->
      <div class="glass rounded-2xl p-6 shadow-soft">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-lg">Overall Score</h3>
          <span id="wheelBadge" class="hidden px-3 py-1 rounded-full text-xs font-bold bg-emerald-400/15 text-emerald-300 border border-emerald-400/30">Great Work — Well Optimized</span>
        </div>
        <div class="grid grid-cols-[220px,1fr] gap-6 mt-4 items-center">
          <div class="wheel">
            <svg id="wheelSvg" width="220" height="220" viewBox="0 0 220 220">
              <defs>
                <linearGradient id="gradWheel" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%"   stop-color="#22d3ee"/>
                  <stop offset="50%"  stop-color="#7c3aed"/>
                  <stop offset="100%" stop-color="#f43f5e"/>
                </linearGradient>
              </defs>
              <g transform="translate(110,110)">
                <circle class="track" r="92" fill="none" stroke-width="18"/>
                <circle id="wheelBar" class="bar" r="92" fill="none" stroke="url(#gradWheel)" stroke-width="18"
                        stroke-dasharray="577" stroke-dashoffset="577" transform="rotate(-90)"/>
                <text id="wheelText" x="0" y="8" text-anchor="middle" font-size="42" font-weight="800" fill="white">0</text>
                <text id="wheelSub" x="0" y="38" text-anchor="middle" font-size="12" fill="rgba(255,255,255,.65)">Score</text>
              </g>
            </svg>
          </div>
          <div class="text-sm">
            <div class="flex items-center gap-2">
              <span class="inline-block h-2.5 w-2.5 rounded-full bg-emerald-400"></span> ≥ 80 Excellent
            </div>
            <div class="flex items-center gap-2 mt-1">
              <span class="inline-block h-2.5 w-2.5 rounded-full bg-amber-400"></span> 60–79 Needs Optimization
            </div>
            <div class="flex items-center gap-2 mt-1">
              <span class="inline-block h-2.5 w-2.5 rounded-full bg-rose-400"></span> &lt; 60 Needs Significant Work
            </div>
            <p id="wheelLabel" class="mt-4 text-slate-300/90">—</p>
          </div>
        </div>
      </div>

      <!-- Quick Stats -->
      <div class="lg:col-span-2 grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="glass rounded-2xl p-5 shadow-soft">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-slate-300/80">Readability</p>
              <div class="text-3xl font-extrabold" id="qsReadability">—</div>
            </div>
            <div class="h-10 w-10 rounded-xl grid place-items-center bg-emerald-500/15 border border-emerald-400/30 text-emerald-300">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M3 5h18M3 12h18M3 19h18"/></svg>
            </div>
          </div>
          <div id="qsGrade" class="mt-1 text-xs text-slate-300/80">— grade band</div>
        </div>

        <div class="glass rounded-2xl p-5 shadow-soft">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-slate-300/80">Words</p>
              <div class="text-3xl font-extrabold" id="qsWords">—</div>
            </div>
            <div class="h-10 w-10 rounded-xl grid place-items-center bg-cyan-500/15 border border-cyan-400/30 text-cyan-300">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
            </div>
          </div>
          <div class="mt-1 text-xs text-slate-300/80">Word count</div>
        </div>

        <div class="glass rounded-2xl p-5 shadow-soft">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-xs text-slate-300/80">Links</p>
              <div class="text-3xl font-extrabold"><span id="qsInt">0</span>/<span id="qsExt">0</span></div>
            </div>
            <div class="h-10 w-10 rounded-xl grid place-items-center bg-fuchsia-500/15 border border-fuchsia-400/30 text-fuchsia-300">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M10 13a5 5 0 0 1 0-7l1-1a5 5 0 0 1 7 7l-1 1"/><path d="M14 11a5 5 0 0 1 0 7l-1 1a5 5 0 0 1-7-7l1-1"/></svg>
            </div>
          </div>
          <div class="mt-1 text-xs text-slate-300/80">internal / external</div>
        </div>
      </div>
    </div>

    <!-- Recommendations -->
    <div class="glass rounded-2xl p-6 shadow-soft">
      <div class="flex items-center justify-between">
        <h3 class="font-bold text-lg">Recommendations</h3>
      </div>
      <div id="recsList" class="mt-4 grid md:grid-cols-2 xl:grid-cols-3 gap-3"></div>
    </div>

    <!-- Content Structure -->
    <div class="grid lg:grid-cols-3 gap-6">
      <div class="glass rounded-2xl p-6 shadow-soft lg:col-span-2">
        <h3 class="font-bold text-lg">Content Structure</h3>
        <div class="grid sm:grid-cols-2 gap-4 mt-4">
          <div class="rounded-xl border border-white/10 bg-white/5 p-4">
            <div class="text-xs text-slate-300/80">Title</div>
            <div id="csTitle" class="mt-1 font-semibold">—</div>
          </div>
          <div class="rounded-xl border border-white/10 bg-white/5 p-4">
            <div class="text-xs text-slate-300/80">Meta description</div>
            <div id="csMeta" class="mt-1 text-slate-100">—</div>
          </div>
        </div>
        <div class="mt-4">
          <h4 class="text-sm font-semibold text-slate-200/90">Heading Map</h4>
          <div id="headingMap" class="mt-2 grid sm:grid-cols-2 lg:grid-cols-3 gap-3"></div>
        </div>
      </div>

      <!-- Readability card -->
      <div class="glass rounded-2xl p-6 shadow-soft">
        <div class="flex items-center justify-between">
          <h3 class="font-bold text-lg">Readability</h3>
          <span id="readBadge" class="hidden px-2.5 py-1 rounded-full text-[11px] font-bold border">Badge</span>
        </div>
        <div class="grid grid-cols-[140px,1fr] gap-4 mt-3 items-center">
          <div class="wheel">
            <svg id="readWheel" width="140" height="140" viewBox="0 0 220 220">
              <defs>
                <linearGradient id="gradRead" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%"   stop-color="#34d399"/>
                  <stop offset="50%"  stop-color="#f59e0b"/>
                  <stop offset="100%" stop-color="#ef4444"/>
                </linearGradient>
              </defs>
              <g transform="translate(110,110) scale(.75)">
                <circle class="track" r="92" fill="none" stroke-width="18"/>
                <circle id="readBar" class="bar" r="92" fill="none" stroke="url(#gradRead)" stroke-width="18"
                        stroke-dasharray="577" stroke-dashoffset="577" transform="rotate(-90)"/>
                <text id="readText" x="0" y="8" text-anchor="middle" font-size="40" font-weight="800" fill="white">—</text>
                <text x="0" y="34" text-anchor="middle" font-size="12" fill="rgba(255,255,255,.65)">Flesch</text>
              </g>
            </svg>
          </div>
          <div class="text-sm">
            <div class="flex items-center justify-between">
              <span class="text-slate-300/80">Grade Level</span>
              <span id="readGrade" class="font-semibold">—</span>
            </div>
            <div class="mt-3">
              <div class="h-2 w-full rounded-full bg-white/10">
                <div id="readBarInline" class="h-2 rounded-full" style="width:0%; background:linear-gradient(90deg,#ef4444,#f59e0b,#34d399);"></div>
              </div>
              <div id="readHelp" class="text-xs text-slate-300/80 mt-2">—</div>
              <button id="readImproveBtn" class="mt-3 px-3 py-1.5 rounded-lg bg-white/10 hover:bg-white/15 border border-white/15 text-xs">How to improve</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Checklists -->
    <div>
      <h3 class="text-xl font-extrabold mb-3">Semantic SEO Ground</h3>
      <div id="catsWrap" class="grid lg:grid-cols-2 xl:grid-cols-3 gap-6"></div>
    </div>

  </div>
</section>

<!-- Improve Modal -->
<div id="improveModal" class="modal">
  <div class="modal-card glass shadow-soft border border-white/10">
    <div class="p-4 sm:p-5">
      <div class="flex items-start justify-between gap-4">
        <div>
          <div class="chip">Improve</div>
          <h4 id="improveTitle" class="mt-2 font-bold text-lg">Improvement tips</h4>
        </div>
        <button id="modalClose" class="p-2 rounded-lg hover:bg-white/10">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
      </div>
      <p id="improveAdvice" class="mt-3 text-slate-200/90"></p>
      <div class="mt-4 flex items-center gap-3">
        <a id="improveLink" href="#" target="_blank" rel="noopener" class="px-3 py-2 rounded-lg bg-gradient-to-r from-cyan-400 to-fuchsia-500 text-slate-900 font-semibold">Google tips</a>
        <button id="modalOk" class="px-3 py-2 rounded-lg bg-white/10 border border-white/15">Close</button>
      </div>
    </div>
  </div>
</div>


@endsection
