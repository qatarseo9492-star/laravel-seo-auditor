@extends('layouts.app')
@section('title','Admin Dashboard')

@push('styles')
<!-- Tailwind (CDN) + Inter font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          brand: {
            50:'#f4f8ff',100:'#e8f0ff',200:'#cfe0ff',300:'#a9c5ff',
            400:'#7da3ff',500:'#537eff',600:'#3c5dff',700:'#2e46d6',800:'#2739a8',900:'#223284'
          }
        },
        boxShadow: {
          glass: '0 10px 30px rgba(0,0,0,.25), inset 0 1px 0 rgba(255,255,255,.06)'
        }
      },
      fontFamily: { sans:['Inter', 'ui-sans-serif', 'system-ui'] }
    },
    darkMode: 'class'
  }
</script>
<style>
  /* Minimal scoped tweaks */
  .prodash a{ text-decoration: none }
</style>
@endpush

@section('content')
<div class="prodash min-h-screen bg-slate-950/98 text-slate-100">
  <div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
      <div class="flex items-center gap-3">
        <div class="h-9 w-9 rounded-xl bg-gradient-to-br from-brand-400 to-brand-700 grid place-items-center shadow-glass">
          <span class="text-slate-900 font-black">≣</span>
        </div>
        <div>
          <h1 class="text-2xl font-extrabold tracking-tight">Admin Dashboard</h1>
          <p class="text-xs text-slate-400">Realtime overview • Professional theme</p>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <button id="refreshNow" class="px-3 py-2 rounded-lg bg-slate-800/70 hover:bg-slate-800 border border-slate-700 text-sm">Refresh</button>
        @php
          $usersUrl = \Illuminate\Support\Facades\Route::has('admin.users.index') ? route('admin.users.index') : url('/admin/users');
        @endphp
        <a href="{{ $usersUrl }}" class="px-3 py-2 rounded-lg bg-gradient-to-r from-brand-500 to-brand-700 text-slate-900 font-semibold text-sm shadow-glass">Manage Users</a>
      </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
      <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="text-xs text-slate-400">Searches Today</div>
        <div id="kpi-searches-today" class="text-3xl font-extrabold mt-1">0</div>
        <div class="text-xs text-slate-500 mt-2">Past 24h active: <span id="kpi-active-24h">0</span></div>
      </div>
      <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="text-xs text-slate-400">Total Users</div>
        <div id="kpi-total-users" class="text-3xl font-extrabold mt-1">0</div>
        <div class="text-xs text-slate-500 mt-2">Active (5m): <span id="activeLive">0</span></div>
      </div>
      <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="text-xs text-slate-400">OpenAI Cost (24h)</div>
        <div id="kpi-openai-cost-today" class="text-3xl font-extrabold mt-1">$0.0000</div>
        <div class="text-xs text-slate-500 mt-2"><span id="kpi-openai-tokens">0</span> tokens</div>
      </div>
      <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="text-xs text-slate-400">DAU / MAU</div>
        <div class="text-3xl font-extrabold mt-1"><span id="kpi-dau">0</span> / <span id="kpi-mau">0</span></div>
        <div class="text-xs text-slate-500 mt-2">Engagement</div>
      </div>
    </div>

    <!-- Main grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
      <!-- System Health -->
      <div class="lg:col-span-2 rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="flex items-center justify-between mb-3">
          <div>
            <div class="font-bold">System Health</div>
            <div class="text-xs text-slate-400">Live snapshots of core services</div>
          </div>
          <span id="healthChip" class="text-xs px-2 py-1 rounded-full border border-slate-700 text-slate-300">Checking…</span>
        </div>
        <div class="overflow-hidden rounded-xl border border-slate-800">
          <table class="w-full text-sm">
            <thead class="bg-slate-900/70 text-slate-400">
              <tr>
                <th class="text-left p-2">Service</th>
                <th class="text-left p-2">Status</th>
                <th class="text-left p-2">Latency</th>
              </tr>
            </thead>
            <tbody id="system-health" class="divide-y divide-slate-800">
              <tr><td colspan="3" class="p-3 text-slate-500">No data yet.</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- User Limits -->
      <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="font-bold mb-2">User Limits</div>
        @php $ls = $limitsSummary ?? ['enabled'=>0,'disabled'=>0,'default'=>200]; @endphp
        <div class="space-y-2">
          <div class="flex items-center justify-between text-sm"><span class="text-slate-400">Enabled</span><span class="font-semibold">{{ $ls['enabled'] ?? 0 }}</span></div>
          <div class="flex items-center justify-between text-sm"><span class="text-slate-400">Disabled</span><span class="font-semibold">{{ $ls['disabled'] ?? 0 }}</span></div>
          <div class="flex items-center justify-between text-sm"><span class="text-slate-400">Default</span><span class="font-semibold">{{ $ls['default'] ?? 200 }}</span></div>
        </div>
        <button onclick="openUserFinder()" class="mt-3 w-full px-3 py-2 rounded-lg border border-slate-700 hover:bg-slate-800 text-sm">Adjust Limits</button>
      </div>

      <!-- Traffic -->
      <div class="lg:col-span-2 rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="flex items-center justify-between mb-3">
          <div>
            <div class="font-bold">Traffic — 30 days</div>
            <div class="text-xs text-slate-400">Requests per day</div>
          </div>
          <span class="text-xs text-slate-400">Requests</span>
        </div>
        <canvas id="trafficChart" height="96"></canvas>
      </div>

      <!-- Top Queries -->
      <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="font-bold mb-2">Top Queries — 7d</div>
        <div id="topQueries" class="space-y-2 text-sm">
          @foreach(($topQueries ?? []) as $row)
            <div class="flex items-center justify-between">
              <div class="truncate pr-2 text-slate-300">{{ $row['query'] ?? '—' }}</div>
              <div class="text-slate-400">{{ $row['count'] ?? 0 }}</div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- Errors -->
      <div class="rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="font-bold mb-2">Error Digest — 24h</div>
        <div id="errorsDigest" class="space-y-2 text-sm">
          @foreach(($errors ?? []) as $err)
            <div class="flex items-center justify-between">
              <div class="truncate pr-2 text-slate-300">{{ $err['message'] ?? '—' }}</div>
              <div class="text-slate-400">{{ $err['count'] ?? 0 }}</div>
            </div>
          @endforeach
        </div>
      </div>

      <!-- History -->
      <div class="lg:col-span-3 rounded-2xl bg-slate-900/50 border border-slate-800 p-4 shadow-glass">
        <div class="flex items-center justify-between mb-3">
          <div>
            <div class="font-bold">Global Search History</div>
            <div class="text-xs text-slate-400">Newest 100 records</div>
          </div>
          <div class="flex items-center gap-2">
            <input id="historyFilter" placeholder="Filter (email / domain / url)" class="px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 text-sm w-64" />
            <button id="exportCsvBtn" class="px-3 py-2 rounded-lg bg-slate-800/70 hover:bg-slate-800 border border-slate-700 text-sm">Export CSV</button>
          </div>
        </div>
        <div class="overflow-hidden rounded-xl border border-slate-800">
          <table class="w-full text-sm">
            <thead class="bg-slate-900/70 text-slate-400">
              <tr>
                <th class="text-left p-2">When</th>
                <th class="text-left p-2">User</th>
                <th class="text-left p-2">URL / Query</th>
                <th class="text-left p-2">Tool</th>
                <th class="text-left p-2">Tokens</th>
                <th class="text-left p-2">Cost</th>
              </tr>
            </thead>
            <tbody id="global-history" class="divide-y divide-slate-800">
              <tr><td colspan="6" class="p-3 text-slate-500">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Drawer -->
  <div id="userDrawer" class="fixed inset-0 bg-black/50 z-50 hidden" onclick="if(event.target===this) closeUserDrawer()">
    <div class="absolute right-0 top-0 h-full w-full max-w-md bg-slate-950 border-l border-slate-800 p-4 shadow-glass">
      <div class="flex items-start justify-between">
        <div>
          <div id="udTitle" class="font-bold">User</div>
          <div id="udSub" class="text-xs text-slate-400">—</div>
        </div>
        <button class="px-3 py-2 rounded-lg bg-slate-800/70 hover:bg-slate-800 border border-slate-700 text-sm" onclick="closeUserDrawer()">Close</button>
      </div>
      <div id="udBody" class="mt-4 text-sm">Loading…</div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@php
  $liveUrl = \Illuminate\Support\Facades\Route::has('admin.dashboard.live')
    ? route('admin.dashboard.live')
    : url('/admin/dashboard/live');
@endphp
<script>
(function(){
  const LIVE_URL = @json($liveUrl);
  const USER_URL = (id) => @json(url('/admin/users')) + '/' + id + '/live';
  const LIMIT_URL = (id) => @json(url('/admin/users')) + '/' + id + '/limits';
  const CSRF = @json(csrf_token());

  const $ = (s,n=document)=>n.querySelector(s);
  const $$= (s,n=document)=>Array.from(n.querySelectorAll(s));
  const fmt = (n)=> new Intl.NumberFormat().format(Number(n||0));
  const money = (n)=> '$'+(Number(n||0).toFixed(4));
  const safe = (v)=> (v==null?'':String(v));

  function renderKPIs(k){
    const set=(id,v)=>{ const el=document.getElementById(id); if(el) el.textContent=v; };
    set('kpi-searches-today', fmt(k.searchesToday));
    set('kpi-total-users', fmt(k.totalUsers));
    set('kpi-openai-cost-today', money(k.cost24h));
    set('kpi-openai-tokens', fmt(k.tokens24h));
    const al = document.getElementById('activeLive'); if(al) al.textContent = fmt(k.active5m);
    const da = document.getElementById('kpi-dau'); if(da) da.textContent = fmt(k.dau);
    const ma = document.getElementById('kpi-mau'); if(ma) ma.textContent = fmt(k.mau);
  }

  function renderServices(services){
    const tb = document.getElementById('system-health'); if(!tb) return;
    tb.innerHTML='';
    if(!services?.length){ tb.innerHTML = '<tr><td colspan="3" class="p-3 text-slate-500">No data.</td></tr>'; return; }
    let okAll = true;
    services.forEach(s=>{
      okAll = okAll && !!s.ok;
      const badge = s.ok ? 'text-emerald-300 border-emerald-400/30 bg-emerald-500/10' : 'text-rose-300 border-rose-400/30 bg-rose-500/10';
      const tr=document.createElement('tr');
      tr.className='';
      tr.innerHTML = `<td class="p-2">${safe(s.name)}</td>
        <td class="p-2"><span class="text-xs px-2 py-1 rounded-full border ${badge}">${s.ok?'Operational':'Down'}</span></td>
        <td class="p-2">${s.latency_ms ?? '—'} ${s.latency_ms?'ms':''}</td>`;
      tb.appendChild(tr);
    });
    const chip = document.getElementById('healthChip');
    if(chip){ chip.textContent = okAll ? 'Healthy' : 'Issues detected'; chip.className = 'text-xs px-2 py-1 rounded-full border ' + (okAll?'border-emerald-400/30 bg-emerald-500/10 text-emerald-300':'border-amber-400/30 bg-amber-500/10 text-amber-200'); }
  }

  function renderHistory(rows){
    const tb = document.getElementById('global-history'); if(!tb) return;
    tb.innerHTML='';
    if(!rows?.length){ tb.innerHTML = '<tr><td colspan="6" class="p-3 text-slate-500">No recent history.</td></tr>'; return; }
    rows.forEach(r=>{
      const tr=document.createElement('tr');
      tr.innerHTML = `<td class="p-2">${safe(r.when)}</td><td class="p-2">${safe(r.user)}</td>
        <td class="p-2" title="${safe(r.display)}"><div class="truncate max-w-[680px]">${safe(r.display)}</div></td>
        <td class="p-2">${safe(r.tool)}</td><td class="p-2">${r.tokens ?? '—'}</td><td class="p-2">${r.cost ?? '0.0000'}</td>`;
      tb.appendChild(tr);
    });

    const input = document.getElementById('historyFilter');
    if (input && !input._bound) {
      input._bound = true;
      input.addEventListener('input', function(){
        const q = this.value.toLowerCase().trim();
        $$('#global-history tr').forEach(tr=>{
          if(!q){ tr.style.display=''; return; }
          tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    }
    const btn = document.getElementById('exportCsvBtn');
    if (btn && !btn._bound){
      btn._bound = true;
      btn.addEventListener('click', () => {
        const rows = [['When','User','URL/Query','Tool','Tokens','Cost']];
        $$('#global-history tr').forEach(tr=>{
          if(tr.style.display==='none') return;
          const tds = Array.from(tr.querySelectorAll('td'));
          if (tds.length) rows.push(tds.map(td=> td.innerText.replace(/\n/g,' ').trim()));
        });
        const csv = rows.map(r=> r.map(v=> /[",]/.test(v) ? '"' + v.replace(/"/g,'""') + '"' : v).join(',')).join('\n');
        const blob = new Blob([csv], {type:'text/csv;charset=utf-8'});
        const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'global-history.csv'; a.click();
        setTimeout(()=>URL.revokeObjectURL(a.href), 0);
      });
    }
  }

  function renderTraffic(points){
    const el = document.getElementById('trafficChart'); if(!el) return;
    const labels = (points||[]).map(p=>p.day);
    const data   = (points||[]).map(p=>p.count);
    if (!window._trafficChart){
      window._trafficChart = new Chart(el, {
        type:'line',
        data:{ labels, datasets:[{ label:'Requests', data, tension:.35, borderWidth:2, fill:true }]},
        options:{ plugins:{ legend:{ display:false }}, scales:{ y:{ ticks:{ color:'#9aa6b2'}}, x:{ ticks:{ color:'#9aa6b2'}}}}
      });
    } else {
      _trafficChart.data.labels = labels;
      _trafficChart.data.datasets[0].data = data;
      _trafficChart.update('none');
    }
  }

  async function tick(){
    try{
      const res = await fetch(LIVE_URL + '?fresh=1', { headers:{'X-Requested-With':'XMLHttpRequest'} });
      if(!res.ok) return;
      const d = await res.json();
      if(d.kpis) renderKPIs(d.kpis);
      if(d.services) renderServices(d.services);
      if(d.history) renderHistory(d.history);
      if(d.traffic) renderTraffic(d.traffic);
    }catch(e){}
  }

  // Drawer
  window.openUserFinder = function(){
    const id = prompt('Enter user ID to manage:');
    if (id) openUserDrawer(id);
  };
  window.openUserDrawer = async function(id){
    const wrap = document.getElementById('userDrawer'); const body = document.getElementById('udBody');
    const title = document.getElementById('udTitle'); const sub = document.getElementById('udSub');
    wrap.classList.remove('hidden'); body.innerHTML = 'Loading…'; title.textContent = 'User #'+id; sub.textContent='—';
    try {
      const res = await fetch(@json(url('/admin/users'))+'/'+id+'/live', { headers:{'X-Requested-With':'XMLHttpRequest'} });
      const j = await res.json(); const u=j.user||{}; const L=j.limit||{daily_limit:200,is_enabled:true,reason:''};
      sub.textContent = (u.email||'') + ' — Last seen ' + (u.last_seen_at||'—') + (u.last_ip? (' ('+u.last_ip+')') : '');
      body.innerHTML = `
        <div class="grid grid-cols-2 gap-3">
          <div>
            <label class="text-xs text-slate-400">Daily limit</label>
            <input id="udDaily" class="mt-1 px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 w-full" type="number" min="0" value="${L.daily_limit}"/>
          </div>
          <div>
            <label class="text-xs text-slate-400">Status</label>
            <select id="udEnabled" class="mt-1 px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 w-full">
              <option value="1" ${L.is_enabled?'selected':''}>Enabled</option>
              <option value="0" ${!L.is_enabled?'selected':''}>Disabled</option>
            </select>
          </div>
          <div class="col-span-2">
            <label class="text-xs text-slate-400">Reason</label>
            <input id="udReason" class="mt-1 px-3 py-2 rounded-lg bg-slate-900 border border-slate-800 w-full" type="text" value="${L.reason||''}" placeholder="Optional"/>
          </div>
        </div>
        <div class="mt-3 flex gap-2">
          <button class="px-3 py-2 rounded-lg bg-gradient-to-r from-brand-500 to-brand-700 text-slate-900 font-semibold text-sm" onclick="saveUserLimit(${id})">Save</button>
          <button class="px-3 py-2 rounded-lg bg-slate-800/70 hover:bg-slate-800 border border-slate-700 text-sm" onclick="closeUserDrawer()">Close</button>
        </div>`;
    } catch(e){ body.innerHTML = '<div class="text-rose-300">Failed to load user.</div>'; }
  };
  window.closeUserDrawer = function(){ document.getElementById('userDrawer').classList.add('hidden'); };
  window.saveUserLimit = async function(id){
    const payload = {
      daily_limit: Number(document.getElementById('udDaily').value||200),
      is_enabled: Number(document.getElementById('udEnabled').value||1),
      reason: document.getElementById('udReason').value || ''
    };
    try{
      const res = await fetch(@json(url('/admin/users'))+'/'+id+'/limits', {
        method:'PATCH',
        headers:{ 'Content-Type':'application/json', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload)
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      alert('Saved'); closeUserDrawer();
    }catch(e){ alert('Failed to save'); }
  };

  document.getElementById('refreshNow')?.addEventListener('click', tick);
  tick(); setInterval(tick, 10000);
})();
</script>
@endpush
