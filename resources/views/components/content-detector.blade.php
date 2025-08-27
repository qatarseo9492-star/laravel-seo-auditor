<div x-data="contentDetector()" class="w-full max-w-3xl mx-auto bg-white/5 dark:bg-black/20 backdrop-blur rounded-2xl p-6 shadow-lg border border-white/10">
  <h2 class="text-2xl font-semibold mb-4">Multi-Model Ensemble Content Detection</h2>
  <p class="text-sm opacity-80 mb-4">Paste your text and click Detect. Uses Hugging Face ensemble + local statistics with caching and rate limiting.</p>

  <textarea x-model="text" rows="8" class="w-full rounded-xl p-4 bg-black/20 border border-white/10 focus:outline-none focus:ring focus:ring-indigo-400" placeholder="Paste content here..."></textarea>

  <div class="flex items-center gap-3 mt-4">
    <button @click="detect()" :disabled="loading || !text || text.length < 20" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 disabled:opacity-50">Detect</button>
    <div x-show="loading" class="text-sm animate-pulse">Analyzing…</div>
  </div>

  <template x-if="result">
    <div class="mt-6 space-y-4">
      <div class="flex items-center gap-4">
        <div class="relative w-28 h-28">
          <svg viewBox="0 0 120 120" class="w-28 h-28">
            <circle cx="60" cy="60" r="52" fill="none" stroke="currentColor" class="opacity-20" stroke-width="12"></circle>
            <circle :stroke-dasharray="circumference" :stroke-dashoffset="dashOffset" cx="60" cy="60" r="52" fill="none" :stroke="getWheelColor()" stroke-linecap="round" stroke-width="12" transform="rotate(-90 60 60)"></circle>
          </svg>
          <div class="absolute inset-0 grid place-items-center text-center">
            <div class="text-xl font-bold" x-text="Math.round(result.data.final_score * 100) + '%'"></div>
            <div class="text-xs opacity-80" x-text="result.data.verdict"></div>
          </div>
        </div>
        <div>
          <div class="text-sm">Confidence: <span class="font-semibold" x-text="Math.round(result.data.confidence * 100) + '%'"></span></div>
          <div class="text-sm opacity-80">Signals: <span x-text="result.data.used.join(', ')"></span></div>
        </div>
      </div>

      <details class="bg-black/10 rounded-xl p-4">
        <summary class="cursor-pointer font-medium">Model Breakdown</summary>
        <div class="text-sm mt-2 space-y-1" x-html="modelBreakdown()"></div>
      </details>

      <details class="bg-black/10 rounded-xl p-4">
        <summary class="cursor-pointer font-medium">Statistical Features</summary>
        <pre class="text-xs overflow-x-auto" x-text="JSON.stringify(result.data.stats.features, null, 2)"></pre>
      </details>
    </div>
  </template>
</div>

<script>
function contentDetector(){
  return {
    text: '',
    result: null,
    loading: false,
    circumference: 2 * Math.PI * 52,
    get dashOffset(){
      if(!this.result) return this.circumference;
      const s = this.result.data.final_score ?? 0.5;
      return this.circumference * (1 - s);
    },
    modelBreakdown(){
      if(!this.result) return '';
      const by = this.result.data.by_model || {};
      let html = '<ul class="list-disc pl-6">';
      for (const [key,val] of Object.entries(by)) {
        const pct = val && val.prob_ai != null ? Math.round(val.prob_ai * 100) + '%' : '—';
        html += `<li><b>${key}</b> → ${pct} (w=${val.weight ?? 0})</li>`;
      }
      html += '</ul>';
      return html;
    },
    getWheelColor(){
      if(!this.result) return '#6366f1';
      const s = this.result.data.final_score;
      if(s >= 0.8) return '#ef4444';
      if(s >= 0.6) return '#f59e0b';
      if(s >= 0.4) return '#10b981';
      return '#22d3ee';
    },
    async detect(){
      this.loading = true;
      this.result = null;
      try{
        const res = await fetch('/api/detect', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.content ?? ''
          },
          body: JSON.stringify({content: this.text})
        });
        const json = await res.json();
        this.result = json;
      }catch(e){
        console.error(e);
        alert('Detection failed');
      }finally{
        this.loading = false;
      }
    }
  }
}
</script>
