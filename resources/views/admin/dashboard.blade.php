@extends('layouts.app')
@section('title','Admin Dashboard — Final')

@push('styles')
<!-- Self-contained professional theme (no external CSS frameworks) -->
<style>
:root{
  --bg:#0b1022; --bg-2:#0e1530; --card:#121a3a;
  --text:#e8eef5; --muted:#9aa6b2; --line:rgba(255,255,255,.08);
  --accent:#7da3ff; --accent-2:#6ee7ff;
  --ok:#19e58b; --warn:#ffb020; --err:#ff5063;
  --shadow: 0 10px 30px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.04);
}
*{box-sizing:border-box}
body{background:radial-gradient(1200px 800px at -10% -10%, rgba(125,163,255,.15), transparent 40%),
radial-gradient(900px 480px at 110% 10%, rgba(110,231,255,.12), transparent 45%), var(--bg); color:var(--text);
font-family: Inter, system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif}
a{color:inherit}
.container{max-width:1180px;margin:24px auto;padding:0 16px}
.header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
.brand{display:flex;gap:12px;align-items:center}
.logo{width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,var(--accent),var(--accent-2));display:grid;place-items:center;
box-shadow:0 0 0 2px rgba(255,255,255,.06);color:#08111e;font-weight:900}
.badge{display:inline-block;margin-top:2px;background:rgba(255,255,255,.06);border:1px solid var(--line);padding:4px 10px;border-radius:999px;
font-size:12px;color:var(--muted)}
h1{margin:0;font-size:26px;font-weight:900;letter-spacing:.2px}
.btn{border:1px solid var(--line);background:rgba(255,255,255,.06);color:var(--text);border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;
box-shadow:var(--shadow);transition:.2s transform}
.btn:hover{transform:translateY(-1px)}
.btn.primary{background:linear-gradient(135deg,var(--accent),var(--accent-2));color:#06142a}
.btn.ghost{background:rgba(255,255,255,.06)}
.btn.small{padding:6px 10px;font-size:12px}
.grid{display:grid;grid-gap:16px}
.kpis{grid-template-columns:repeat(4,1fr)}
.main{grid-template-columns:2fr 1fr;grid-auto-rows:minmax(120px,auto)}
.card{background:linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01));border:1px solid var(--line);border-radius:16px;
padding:16px;box-shadow:var(--shadow)}
.card-head{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px}
.card-title{font-weight:800}
.card-sub{color:var(--muted);font-size:12px;margin-top:4px}
.kpi .kpi-label{color:var(--muted);font-size:12px}
.kpi .kpi-value{font-size:28px;font-weight:800;margin-top:6px}
.kpi .kpi-sub{color:var(--muted);font-size:12px;margin-top:4px}
.table{width:100%;border-collapse:separate;border-spacing:0 8px}
.table th{color:var(--muted);text-align:left;font-weight:700;font-size:12px;padding:6px 8px}
.table td{background:rgba(255,255,255,.02);border:1px solid var(--line);padding:10px 12px}
.table tr td:first-child{border-top-left-radius:10px;border-bottom-left-radius:10px}
.table tr td:last-child{border-top-right-radius:10px;border-bottom-right-radius:10px}
.table .muted{color:var(--muted);text-align:center;background:transparent;border:0;padding:20px 8px}
.pill{border:1px solid var(--line);border-radius:999px;padding:4px 8px;color:var(--muted);font-size:12px}
.status{border-radius:999px;padding:6px 10px;font-size:12px;border:1px solid var(--line)}
.status.ok{background:rgba(25,229,139,.08);color:#b9ffd7}
.status.warn{background:rgba(255,176,32,.08);color:#ffddac}
.status.err{background:rgba(255,80,99,.08);color:#ffb3bd}
.list .row{display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px dashed var(--line)}
.list .row .text{white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:72%}
.input{background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:10px;padding:8px 10px;color:var(--text);min-width:220px}
.drawer{position:fixed;inset:0;background:rgba(0,0,0,.45);display:flex;justify-content:flex-end;z-index:10000}
.drawer.hidden{display:none}
.panel{width:min(420px,100%);height:100%;background:var(--bg-2);border-left:1px solid var(--line);box-shadow:var(--shadow);display:flex;flex-direction:column}
.panel-head{display:flex;justify-content:space-between;align-items:flex-start;padding:16px;border-bottom:1px solid var(--line)}
.panel-title{font-weight:800}
.panel-sub{color:var(--muted);font-size:12px;margin-top:4px}
.panel-body{padding:16px;overflow:auto}
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
@media (max-width:960px){.kpis{grid-template-columns:repeat(2,1fr)} .main{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="container">
  <div class="header">
    <div class="brand">
      <div class="logo">≣</div>
      <div>
        <h1>Admin Dashboard</h1>
        <div class="badge">Final Pro Theme</div>
      </div>
    </div>
    <div class="toolbar">
      <button id="refreshNow" class="btn ghost small">Refresh</button>
      @php $usersUrl = \Illuminate\Support\Facades\Route::has('admin.users.index') ? route('admin.users.index') : url('/admin/users'); @endphp
      <a class="btn primary small" href="{{ $usersUrl }}">Manage Users</a>
    </div>
  </div>

  <div class="grid kpis">
    <div class="card kpi"><div class="kpi-label">Searches Today</div><div class="kpi-value" id="kpi-searches-today">0</div><div class="kpi-sub">Past 24h active: <span id="kpi-active-24h">0</span></div></div>
    <div class="card kpi"><div class="kpi-label">Total Users</div><div class="kpi-value" id="kpi-total-users">0</div><div class="kpi-sub">Active (5m): <span id="activeLive">0</span></div></div>
    <div class="card kpi"><div class="kpi-label">OpenAI Cost (24h)</div><div class="kpi-value" id="kpi-openai-cost-today">$0.0000</div><div class="kpi-sub"><span id="kpi-openai-tokens">0</span> tokens</div></div>
    <div class="card kpi"><div class="kpi-label">DAU / MAU</div><div class="kpi-value"><span id="kpi-dau">0</span> / <span id="kpi-mau">0</span></div><div class="kpi-sub">Engagement</div></div>
  </div>

  <div class="grid main" style="margin-top:16px">
    <div class="card" style="grid-column: span 2;">
      <div class="card-head">
        <div><div class="card-title">System Health</div><div class="card-sub">Live snapshots of core services</div></div>
        <div class="status" id="healthChip">Checking…</div>
      </div>
      <table class="table">
        <thead><tr><th>Service</th><th>Status</th><th>Latency</th></tr></thead>
        <tbody id="system-health"><tr><td colspan="3" class="muted">No data yet.</td></tr></tbody>
      </table>
    </div>

    <div class="card">
      <div class="card-head"><div><div class="card-title">User Limits</div><div class="card-sub">Summary. Manage via drawer or users list.</div></div></div>
      @php $ls = $limitsSummary ?? ['enabled'=>0,'disabled'=>0,'default'=>200]; @endphp
      <div class="list" style="margin-top:6px">
        <div class="row"><span class="text">Enabled</span><span>{{ $ls['enabled'] ?? 0 }}</span></div>
        <div class="row"><span class="text">Disabled</span><span>{{ $ls['disabled'] ?? 0 }}</span></div>
        <div class="row"><span class="text">Default</span><span>{{ $ls['default'] ?? 200 }}</span></div>
      </div>
      <button class="btn ghost small" style="margin-top:10px" onclick="openUserFinder()">Adjust Limits</button>
    </div>

    <div class="card" style="grid-column: span 2;">
      <div class="card-head"><div><div class="card-title">Traffic — 30 days</div><div class="card-sub">Requests per day</div></div><div class="pill">Requests</div></div>
      <canvas id="trafficChart" height="96"></canvas>
    </div>

    <div class="card">
      <div class="card-head"><div><div class="card-title">Top Queries — 7d</div><div class="card-sub">Most frequent inputs</div></div></div>
      <div id="topQueries" class="list">
        @foreach(($topQueries ?? []) as $row)
          <div class="row"><span class="text">{{ $row['query'] ?? '—' }}</span><span class="meta">{{ $row['count'] ?? 0 }}</span></div>
        @endforeach
      </div>
    </div>

    <div class="card">
      <div class="card-head"><div><div class="card-title">Error Digest — 24h</div><div class="card-sub">Grouped by message</div></div></div>
      <div id="errorsDigest" class="list">
        @foreach(($errors ?? []) as $err)
          <div class="row"><span class="text">{{ $err['message'] ?? '—' }}</span><span class="meta">{{ $err['count'] ?? 0 }}</span></div>
        @endforeach
      </div>
    </div>

    <div class="card" style="grid-column: span 2;">
      <div class="card-head">
        <div><div class="card-title">Global Search History</div><div class="card-sub">Newest 100 records</div></div>
        <div><input id="historyFilter" class="input" placeholder="Filter (email / domain / url)" /> <button id="exportCsvBtn" class="btn ghost small" style="margin-left:6px">Export CSV</button></div>
      </div>
      <table class="table">
        <thead><tr><th>When</th><th>User</th><th>URL / Query</th><th>Tool</th><th>Tokens</th><th>Cost</th></tr></thead>
        <tbody id="global-history"><tr><td colspan="6" class="muted">Loading…</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Drawer -->
<div id="userDrawer" class="drawer hidden" onclick="if(event.target===this) closeUserDrawer()">
  <div class="panel">
    <div class="panel-head">
      <div><div id="udTitle" class="panel-title">User</div><div id="udSub" class="panel-sub">—</div></div>
      <button class="btn ghost small" onclick="closeUserDrawer()">Close</button>
    </div>
    <div class="panel-body" id="udBody">Loading…</div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@php $liveUrl = \Illuminate\Support\Facades\Route::has('admin.dashboard.live') ? route('admin.dashboard.live') : url('/admin/dashboard/live'); @endphp
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
    if(!services?.length){ tb.innerHTML = '<tr><td colspan="3" class="muted">No data.</td></tr>'; return; }
    let okAll = true;
    services.forEach(s=>{
      okAll = okAll && !!s.ok;
      const cls = s.ok?'ok':(s.latency_ms ? 'warn' : 'err');
      const label = s.ok?'Operational':'Down';
      const tr=document.createElement('tr');
      tr.innerHTML = `<td>${safe(s.name)}</td>
        <td><span class="status ${s.ok?'ok':'err'}">${label}</span></td>
        <td>${s.latency_ms ?? '—'} ${s.latency_ms?'ms':''}</td>`;
      tb.appendChild(tr);
    });
    const chip = document.getElementById('healthChip');
    if (chip){ chip.className = 'status ' + (okAll?'ok':'warn'); chip.textContent = okAll?'Healthy':'Issues detected'; }
  }

  function renderHistory(rows){
    const tb = document.getElementById('global-history'); if(!tb) return;
    tb.innerHTML='';
    if(!rows?.length){ tb.innerHTML = '<tr><td colspan="6" class="muted">No recent history.</td></tr>'; return; }
    rows.forEach(r=>{
      const tr=document.createElement('tr');
      tr.innerHTML = `<td>${safe(r.when)}</td><td>${safe(r.user)}</td>
        <td title="${safe(r.display)}" style="max-width:640px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${safe(r.display)}</td>
        <td>${safe(r.tool)}</td><td>${r.tokens ?? '—'}</td><td>${r.cost ?? '0.0000'}</td>`;
      tb.appendChild(tr);
    });
    const input = document.getElementById('historyFilter');
    if (input && !input._bound){
      input._bound = true;
      input.addEventListener('input', function(){
        const q = this.value.toLowerCase().trim();
        $$('#global-history tr').forEach(tr=>{
          if(!q) tr.style.display='';
          else tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
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
        setTimeout(()=>URL.revokeObjectURL(a.href),0);
      });
    }
  }

  function renderTraffic(points){
    const el = document.getElementById('trafficChart'); if(!el) return;
    const labels = (points||[]).map(p=>p.day);
    const data = (points||[]).map(p=>p.count);
    if (!window._trafficChart){
      window._trafficChart = new Chart(el, { type:'line', data:{ labels, datasets:[{ label:'Requests', data, tension:.35, borderWidth:2, fill:true }]},
        options:{ plugins:{ legend:{ display:false }}, scales:{ y:{ ticks:{ color:'#9aa6b2'}}, x:{ ticks:{ color:'#9aa6b2'}}}} });
    } else { _trafficChart.data.labels=labels; _trafficChart.data.datasets[0].data=data; _trafficChart.update('none'); }
  }

  async function tick(){
    try{
      const res = await fetch(@json($liveUrl) + '?fresh=1', { headers:{'X-Requested-With':'XMLHttpRequest'} });
      if(!res.ok) return;
      const d = await res.json();
      d.kpis && renderKPIs(d.kpis);
      d.services && renderServices(d.services);
      d.history && renderHistory(d.history);
      d.traffic && renderTraffic(d.traffic);
    }catch(e){}
  }

  // User drawer basics
  window.openUserFinder = function(){ const id = prompt('Enter user ID to manage:'); if(id) openUserDrawer(id); };
  window.openUserDrawer = async function(id){
    const wrap = document.getElementById('userDrawer'); const body = document.getElementById('udBody'); const title = document.getElementById('udTitle'); const sub = document.getElementById('udSub');
    wrap.classList.remove('hidden'); body.innerHTML='Loading…'; title.textContent='User #'+id; sub.textContent='—';
    try{
      const res = await fetch(@json(url('/admin/users'))+'/'+id+'/live', { headers:{'X-Requested-With':'XMLHttpRequest'} });
      const j = await res.json(); const u=j.user||{}; const L=j.limit||{daily_limit:200,is_enabled:true,reason:''};
      sub.textContent = (u.email||'') + ' — Last seen ' + (u.last_seen_at||'—') + (u.last_ip? (' ('+u.last_ip+')') : '');
      body.innerHTML = `
        <div class="form-grid">
          <div><label>Daily limit</label><input id="udDaily" class="input" type="number" min="0" value="${L.daily_limit}"></div>
          <div><label>Status</label><select id="udEnabled" class="input"><option value="1" ${L.is_enabled?'selected':''}>Enabled</option><option value="0" ${!L.is_enabled?'selected':''}>Disabled</option></select></div>
          <div style="grid-column:1/3"><label>Reason</label><input id="udReason" class="input" type="text" value="${L.reason||''}" placeholder="Optional"></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px">
          <button class="btn primary small" onclick="saveUserLimit(${id})">Save</button>
          <button class="btn ghost small" onclick="closeUserDrawer()">Close</button>
        </div>`;
    }catch(e){ body.innerHTML = '<div style="color:#ffb3bd">Failed to load user.</div>'; }
  };
  window.closeUserDrawer = function(){ document.getElementById('userDrawer').classList.add('hidden'); };
  window.saveUserLimit = async function(id){
    const payload = { daily_limit: Number(document.getElementById('udDaily').value||200), is_enabled: Number(document.getElementById('udEnabled').value||1), reason: document.getElementById('udReason').value||'' };
    try{
      const res = await fetch(@json(url('/admin/users'))+'/'+id+'/limits', { method:'PATCH', headers:{ 'Content-Type':'application/json', 'X-Requested-With':'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF }, body: JSON.stringify(payload) });
      if(!res.ok) throw new Error('HTTP '+res.status); alert('Saved'); closeUserDrawer();
    }catch(e){ alert('Failed to save'); }
  };

  document.getElementById('refreshNow')?.addEventListener('click', tick);
  tick(); setInterval(tick, 10000);
})();
</script>
@endpush
