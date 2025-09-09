@extends('layouts.app')
@section('title','Admin Dashboard — SX Nova')

@section('content')
<div class="sx-root">
  <div class="sx-wrap">
    <!-- Header -->
    <div class="sx-topbar">
      <div class="sx-brand">
        <div class="sx-logo">◆</div>
        <div class="sx-title">
          <h1>Admin Dashboard</h1>
          <span class="sx-note">Realtime overview • SX Nova</span>
        </div>
      </div>
      <div class="sx-actions">
        <button id="sxRefresh" class="sx-btn sx-ghost">Refresh</button>
        @php $usersUrl = \Illuminate\Support\Facades\Route::has('admin.users.index') ? route('admin.users.index') : url('/admin/users'); @endphp
        <a href="{{ $usersUrl }}" class="sx-btn sx-primary" role="button">Manage Users</a>
      </div>
    </div>

    <!-- KPIs -->
    <div class="sx-grid sx-kpis">
      <div class="sx-card sx-kpi">
        <div class="sx-kpi-label">Searches Today</div>
        <div class="sx-kpi-value" id="kpi-searches-today">0</div>
        <div class="sx-kpi-sub">Past 24h active: <span id="kpi-active-24h">0</span></div>
      </div>
      <div class="sx-card sx-kpi">
        <div class="sx-kpi-label">Total Users</div>
        <div class="sx-kpi-value" id="kpi-total-users">0</div>
        <div class="sx-kpi-sub">Active (5m): <span id="activeLive">0</span></div>
      </div>
      <div class="sx-card sx-kpi">
        <div class="sx-kpi-label">OpenAI Cost (24h)</div>
        <div class="sx-kpi-value" id="kpi-openai-cost-today">$0.0000</div>
        <div class="sx-kpi-sub"><span id="kpi-openai-tokens">0</span> tokens</div>
      </div>
      <div class="sx-card sx-kpi">
        <div class="sx-kpi-label">DAU / MAU</div>
        <div class="sx-kpi-value"><span id="kpi-dau">0</span> / <span id="kpi-mau">0</span></div>
        <div class="sx-kpi-sub">Engagement</div>
      </div>
    </div>

    <!-- Main grid -->
    <div class="sx-grid sx-main">
      <div class="sx-card sx-span-2">
        <div class="sx-head">
          <div>
            <div class="sx-head-title">System Health</div>
            <div class="sx-head-sub">Core services snapshot</div>
          </div>
          <div id="sxHealthChip" class="sx-badge">Checking…</div>
        </div>
        <div class="sx-table-wrap">
          <table class="sx-table">
            <thead><tr><th>Service</th><th>Status</th><th>Latency</th></tr></thead>
            <tbody id="sxSystemHealth">
              <tr><td colspan="3" class="sx-muted">No data yet.</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="sx-card">
        <div class="sx-head">
          <div>
            <div class="sx-head-title">User Limits</div>
            <div class="sx-head-sub">Summary; edit in drawer or users list</div>
          </div>
        </div>
        @php $ls = $limitsSummary ?? ['enabled'=>0,'disabled'=>0,'default'=>200]; @endphp
        <div class="sx-list">
          <div class="sx-row"><span class="sx-text">Enabled</span><span class="sx-mono">{{ $ls['enabled'] ?? 0 }}</span></div>
          <div class="sx-row"><span class="sx-text">Disabled</span><span class="sx-mono">{{ $ls['disabled'] ?? 0 }}</span></div>
          <div class="sx-row"><span class="sx-text">Default</span><span class="sx-mono">{{ $ls['default'] ?? 200 }}</span></div>
        </div>
        <button class="sx-btn sx-ghost sx-block" onclick="sxOpenUserFinder()">Adjust Limits</button>
      </div>

      <div class="sx-card sx-span-2">
        <div class="sx-head">
          <div>
            <div class="sx-head-title">Traffic — 30 days</div>
            <div class="sx-head-sub">Requests per day</div>
          </div>
          <div class="sx-pill">Requests</div>
        </div>
        <canvas id="sxTraffic" height="96"></canvas>
      </div>

      <div class="sx-card">
        <div class="sx-head"><div class="sx-head-title">Top Queries — 7d</div></div>
        <div id="sxTopQueries" class="sx-list">
          @foreach(($topQueries ?? []) as $row)
            <div class="sx-row"><span class="sx-text">{{ $row['query'] ?? '—' }}</span><span class="sx-mono">{{ $row['count'] ?? 0 }}</span></div>
          @endforeach
        </div>
      </div>

      <div class="sx-card">
        <div class="sx-head"><div class="sx-head-title">Error Digest — 24h</div></div>
        <div id="sxErrors" class="sx-list">
          @foreach(($errors ?? []) as $err)
            <div class="sx-row"><span class="sx-text">{{ $err['message'] ?? '—' }}</span><span class="sx-mono">{{ $err['count'] ?? 0 }}</span></div>
          @endforeach
        </div>
      </div>

      <div class="sx-card sx-span-2">
        <div class="sx-head">
          <div>
            <div class="sx-head-title">Global Search History</div>
            <div class="sx-head-sub">Newest 100 records</div>
          </div>
          <div class="sx-actions gap">
            <input id="sxHistFilter" class="sx-input" placeholder="Filter (email / domain / url)" />
            <button id="sxExportCsv" class="sx-btn sx-ghost">Export CSV</button>
          </div>
        </div>
        <div class="sx-table-wrap">
          <table class="sx-table">
            <thead><tr><th>When</th><th>User</th><th>URL / Query</th><th>Tool</th><th>Tokens</th><th>Cost</th></tr></thead>
            <tbody id="sxHistory">
              <tr><td colspan="6" class="sx-muted">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- NEW: Users — Live (add-only) -->
      <div class="sx-card sx-span-2">
        <div class="sx-head">
          <div>
            <div class="sx-head-title">Users — Live</div>
            <div class="sx-head-sub">Top 20 by last seen · search & quick actions</div>
          </div>
          <div class="sx-actions gap">
            <input id="sxUsersSearch" class="sx-input" placeholder="Search email / name / IP" />
            <button id="sxUsersRefresh" class="sx-btn sx-ghost">Refresh</button>
          </div>
        </div>
        <div class="sx-table-wrap">
          <table class="sx-table">
            <thead>
              <tr>
                <th>ID</th><th>User</th><th>Status</th><th>Last seen</th><th>IP / Country</th><th>Limit</th><th>Actions</th>
              </tr>
            </thead>
            <tbody id="sxUsersBody">
              <tr><td colspan="7" class="sx-muted">Loading…</td></tr>
            </tbody>
          </table>
        </div>
      </div>
      <!-- /Users — Live -->
    </div>
  </div>

  <!-- Drawer -->
  <div id="sxDrawer" class="sx-drawer sx-hidden" onclick="if(event.target===this) sxCloseDrawer()">
    <div class="sx-panel">
      <div class="sx-panel-head">
        <div>
          <div id="sxUdTitle" class="sx-panel-title">User</div>
          <div id="sxUdSub" class="sx-panel-sub">—</div>
        </div>
        <button class="sx-btn sx-ghost" onclick="sxCloseDrawer()">Close</button>
      </div>
      <div id="sxUdBody" class="sx-panel-body">Loading…</div>
    </div>
  </div>
</div>

<!-- Styles (namespaced so they won't collide with your global CSS) -->
<style>
.sx-root{--bg:#0c1226;--bg2:#101833;--card:#141e3e;--text:#e9eef6;--muted:#9aa6b2;--line:rgba(255,255,255,.08);
--a:#62b5ff;--b:#9c8cff;--ok:#13e18a;--warn:#ffb020;--err:#ff5a6b;
font-family:Inter, system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif}
.sx-root *{box-sizing:border-box}
.sx-wrap{max-width:1200px;margin:24px auto;padding:0 16px;color:var(--text)}
.sx-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.sx-brand{display:flex;gap:12px;align-items:center}
.sx-logo{width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,var(--a),var(--b));display:grid;place-items:center;color:#071022;font-weight:900}
.sx-title h1{margin:0;font-size:24px;font-weight:900}
.sx-note{font-size:12px;color:var(--muted)}
.sx-actions{display:flex;gap:8px}
.sx-actions.gap{align-items:center}
.sx-btn{border:1px solid var(--line);border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;background:rgba(255,255,255,.06);color:var(--text)}
.sx-btn.sx-ghost:hover{background:rgba(255,255,255,.1)}
.sx-btn.sx-primary{background:linear-gradient(135deg,var(--a),var(--b));color:#0a1330}
.sx-btn.sx-block{width:100%;margin-top:10px}
.sx-grid{display:grid;grid-gap:16px}
.sx-kpis{grid-template-columns:repeat(4,1fr)}
.sx-main{grid-template-columns:2fr 1fr}
.sx-span-2{grid-column:span 2}
.sx-card{background:linear-gradient(180deg,rgba(255,255,255,.035),rgba(255,255,255,.015));border:1px solid var(--line);border-radius:16px;padding:16px;box-shadow:0 10px 28px rgba(0,0,0,.4)}
.sx-head{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px}
.sx-head-title{font-weight:800}
.sx-head-sub{font-size:12px;color:var(--muted)}
.sx-pill{border:1px solid var(--line);border-radius:999px;padding:4px 8px;color:var(--muted);font-size:12px}
.sx-kpi .sx-kpi-label{font-size:12px;color:var(--muted)}
.sx-kpi .sx-kpi-value{font-size:28px;font-weight:800;margin-top:6px}
.sx-kpi .sx-kpi-sub{font-size:12px;color:var(--muted);margin-top:4px}
.sx-table-wrap{overflow:auto;border-radius:12px;border:1px solid var(--line)}
.sx-table{width:100%;border-collapse:separate;border-spacing:0}
.sx-table thead th{font-size:12px;color:var(--muted);text-align:left;padding:10px;border-bottom:1px solid var(--line);background:rgba(255,255,255,.02)}
.sx-table tbody td{padding:10px;border-bottom:1px dashed var(--line)}
.sx-table .sx-muted{color:var(--muted);text-align:center;padding:18px}
.sx-list{margin-top:6px}
.sx-row{display:flex;justify-content:space-between;gap:8px;padding:8px 0;border-bottom:1px dashed var(--line)}
.sx-text{max-width:70%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.sx-mono{font-variant-numeric:tabular-nums}
.sx-badge{border-radius:999px;padding:6px 10px;font-size:12px;border:1px solid var(--line);color:#bfead8;background:rgba(19,225,138,.08)}
.sx-input{background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:10px;padding:8px 10px;color:var(--text);min-width:240px}
/* Drawer */
.sx-drawer{position:fixed;inset:0;background:rgba(0,0,0,.45);display:flex;justify-content:flex-end;z-index:9999}
.sx-hidden{display:none}
.sx-panel{width:min(420px,100%);height:100%;background:var(--bg2);border-left:1px solid var(--line);display:flex;flex-direction:column}
.sx-panel-head{display:flex;justify-content:space-between;align-items:flex-start;padding:16px;border-bottom:1px solid var(--line)}
.sx-panel-title{font-weight:800}
.sx-panel-sub{font-size:12px;color:var(--muted)}
.sx-panel-body{padding:16px;overflow:auto}
.sx-form{display:grid;grid-template-columns:1fr 1fr;gap:8px}
@media (max-width:980px){ .sx-kpis{grid-template-columns:repeat(2,1fr)} .sx-main{grid-template-columns:1fr} .sx-span-2{grid-column:span 1} }
</style>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@php $liveUrl = \Illuminate\Support\Facades\Route::has('admin.dashboard.live') ? route('admin.dashboard.live') : url('/admin/dashboard/live'); @endphp
<script>
(function(){
  const LIVE = @json($liveUrl);
  const USER = (id)=> @json(url('/admin/users')) + '/' + id + '/live';
  const LIMIT= (id)=> @json(url('/admin/users')) + '/' + id + '/limits';
  const USERS_TABLE = @json(url('/admin/users/table'));
  const SESSIONS = (id)=> @json(url('/admin/users')) + '/' + id + '/sessions';
  const CSRF = @json(csrf_token());

  const $ = (s,n=document)=>n.querySelector(s);
  const $$= (s,n=document)=>Array.from(n.querySelectorAll(s));
  const fmt = (n)=> new Intl.NumberFormat().format(Number(n||0));
  const money=(n)=>'$'+(Number(n||0).toFixed(4));
  const safe =(v)=> (v==null?'':String(v));

  function setText(id, v){ const el=document.getElementById(id); if(el) el.textContent=v; }

  function renderKPIs(k){
    setText('kpi-searches-today', fmt(k.searchesToday));
    setText('kpi-total-users', fmt(k.totalUsers));
    setText('kpi-openai-cost-today', money(k.cost24h));
    setText('kpi-openai-tokens', fmt(k.tokens24h));
    setText('activeLive', fmt(k.active5m));
    setText('kpi-dau', fmt(k.dau)); setText('kpi-mau', fmt(k.mau));
    setText('kpi-active-24h', fmt(k.active24h ?? 0));
  }

  function renderServices(list){
    const tb = $('#sxSystemHealth'); if(!tb) return;
    tb.innerHTML='';
    if(!list?.length){ tb.innerHTML='<tr><td colspan="3" class="sx-muted">No data.</td></tr>'; return; }
    let allOk = true;
    list.forEach(s=>{
      allOk = allOk && !!s.ok;
      const tag = s.ok ? 'Operational' : 'Down';
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${safe(s.name)}</td>
                      <td>${tag}</td>
                      <td>${s.latency_ms ?? '—'} ${s.latency_ms?'ms':''}</td>`;
      tb.appendChild(tr);
    });
    const chip = $('#sxHealthChip');
    if (chip){ chip.textContent = allOk ? 'Healthy' : 'Issues detected'; chip.style.background = allOk ? 'rgba(19,225,138,.08)' : 'rgba(255,176,32,.08)'; chip.style.color = allOk ? '#bfead8' : '#ffe0b3'; }
  }

  function renderTraffic(points){
    const el = document.getElementById('sxTraffic'); if(!el) return;
    const labels = (points||[]).map(p=>p.day);
    const data = (points||[]).map(p=>p.count);
    if (!window.sxChart){
      window.sxChart = new Chart(el, { type:'line', data:{ labels, datasets:[{label:'Requests',data,tension:.35,borderWidth:2,fill:true}]},
        options:{plugins:{legend:{display:false}}, scales:{y:{ticks:{color:'#9aa6b2'}}, x:{ticks:{color:'#9aa6b2'}}}} });
    } else {
      sxChart.data.labels = labels; sxChart.data.datasets[0].data = data; sxChart.update('none');
    }
  }

  function renderHistory(rows){
    const tb = $('#sxHistory'); if(!tb) return;
    tb.innerHTML='';
    if(!rows?.length){ tb.innerHTML='<tr><td colspan="6" class="sx-muted">No recent history.</td></tr>'; return; }
    rows.forEach(r=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${safe(r.when)}</td><td>${safe(r.user)}</td>
        <td title="${safe(r.display)}"><div style="max-width:680px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${safe(r.display)}</div></td>
        <td>${safe(r.tool)}</td><td>${r.tokens ?? '—'}</td><td>${r.cost ?? '0.0000'}</td>`;
      tb.appendChild(tr);
    });

    const input = document.getElementById('sxHistFilter');
    if (input && !input._bound){
      input._bound = true;
      input.addEventListener('input', function(){
        const q = this.value.toLowerCase().trim();
        $$('#sxHistory tr').forEach(tr=>{
          tr.style.display = !q || tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    }
    const btn = document.getElementById('sxExportCsv');
    if (btn && !btn._bound){
      btn._bound = true;
      btn.addEventListener('click', ()=>{
        const rows = [['When','User','URL/Query','Tool','Tokens','Cost']];
        $$('#sxHistory tr').forEach(tr=>{
          if(tr.style.display==='none') return;
          const cells = Array.from(tr.querySelectorAll('td')).map(td=>td.innerText.replace(/\n/g,' ').trim());
          if (cells.length) rows.push(cells);
        });
        const csv = rows.map(r=>r.map(v=> /[",]/.test(v) ? '"'+v.replace(/"/g,'""')+'"' : v).join(',')).join('\n');
        const blob = new Blob([csv],{type:'text/csv;charset=utf-8'});
        const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='global-history.csv'; a.click();
        setTimeout(()=>URL.revokeObjectURL(a.href),0);
      });
    }
  }

  // ---- NEW: Users — Live panel helpers ----
  async function sxLoadUsers(){
    try{
      const q  = (document.getElementById('sxUsersSearch')?.value || '').trim();
      const qs = q ? ('?q=' + encodeURIComponent(q)) : '';
      const res = await fetch(USERS_TABLE + qs, {
        headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
        credentials:'same-origin'
      });
      const text = await res.text();
      let j; try { j = JSON.parse(text); } catch(e){ return sxUsersRenderError('Non-JSON response'); }
      sxUsersRender(j.rows || []);
    }catch(e){
      sxUsersRenderError('Failed to load');
    }
  }
  function sxUsersRenderError(msg){
    const tb = document.getElementById('sxUsersBody');
    if (!tb) return;
    tb.innerHTML = `<tr><td colspan="7" class="sx-muted">${msg}</td></tr>`;
  }
  function sxUsersRender(rows){
    const tb = document.getElementById('sxUsersBody'); if(!tb) return;
    if(!rows.length){ tb.innerHTML = `<tr><td colspan="7" class="sx-muted">No users.</td></tr>`; return; }
    tb.innerHTML = '';
    rows.forEach(r=>{
      const status = r.banned ? 'Banned' : (r.enabled ? 'Enabled' : 'Disabled');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${r.id}</td>
        <td><div class="sx-text" title="${r.email}">${r.name || '—'}<br><span class="sx-head-sub">${r.email}</span></div></td>
        <td>${status}</td>
        <td>${r.last_seen}</td>
        <td>${r.ip || '—'} ${r.country ? '· '+r.country : ''}</td>
        <td>
          <input type="number" min="0" value="${r.limit}" style="width:86px" class="sx-input" id="sxL${r.id}">
          <select class="sx-input" id="sxE${r.id}" style="width:110px">
            <option value="1" ${r.enabled?'selected':''}>Enabled</option>
            <option value="0" ${!r.enabled?'selected':''}>Disabled</option>
          </select>
        </td>
        <td>
          <button class="sx-btn sx-ghost" onclick="sxOpenDrawer(${r.id})">Manage</button>
          <button class="sx-btn sx-ghost" onclick="sxSaveLimitInline(${r.id})">Save</button>
          <button class="sx-btn sx-ghost" onclick="sxToggleBan(${r.id})">${r.banned?'Unban':'Ban'}</button>
          <button class="sx-btn sx-ghost" onclick="sxShowSessions(${r.id})">Sessions</button>
        </td>`;
      tb.appendChild(tr);
    });
  }
  async function sxSaveLimitInline(id){
    const daily = Number(document.getElementById('sxL'+id).value || 200);
    const en    = Number(document.getElementById('sxE'+id).value || 1);
    try {
      const res = await fetch(@json(url('/admin/users'))+'/'+id+'/limits', {
        method:'PATCH',
        headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': CSRF},
        credentials:'same-origin',
        body: JSON.stringify({ daily_limit: daily, is_enabled: en, reason: '' })
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      sxLoadUsers();
    } catch(e){ alert('Save failed'); }
  }
  async function sxToggleBan(id){
    try {
      const res = await fetch(@json(url('/admin/users'))+'/'+id+'/ban', {
        method:'PATCH',
        headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN': CSRF},
        credentials:'same-origin'
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      sxLoadUsers();
    } catch(e){ alert('Ban/Unban failed'); }
  }
  async function sxShowSessions(id){
    try {
      const res = await fetch(SESSIONS(id), {
        headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
        credentials:'same-origin'
      });
      const j = await res.json();
      const list = (j.rows || []).map(s=>`<div class="sx-row"><span class="sx-text">${s.login_at} → ${s.logout_at}</span><span class="sx-mono">${s.ip} ${s.country?'· '+s.country:''}</span></div>`).join('') || '<div class="sx-head-sub">No sessions.</div>';
      // reuse drawer
      sxOpenDrawer(id);
      const body = document.getElementById('sxUdBody');
      body.innerHTML += `<div style="margin-top:12px"><div class="sx-head-title">Sessions</div>${list}</div>`;
    } catch(e){ alert('Failed to load sessions'); }
  }
  // ---- /Users — Live ----

  async function tick(){
    try{
      const res = await fetch(LIVE + '?fresh=1', {
        headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
        credentials:'same-origin'
      });
      const text = await res.text();
      let d; try { d = JSON.parse(text); } catch(e){ return; }
      d.kpis && renderKPIs(d.kpis);
      d.services && renderServices(d.services);
      d.traffic && renderTraffic(d.traffic);
      d.history && renderHistory(d.history);
    }catch(e){/* silent */}
  }

  // Drawer
  window.sxOpenUserFinder = function(){ const id = prompt('Enter user ID to manage:'); if(id) sxOpenDrawer(id); };
  window.sxOpenDrawer = async function(id){
    const wrap = document.getElementById('sxDrawer'); const body = document.getElementById('sxUdBody');
    const title = document.getElementById('sxUdTitle'); const sub = document.getElementById('sxUdSub');
    wrap.classList.remove('sx-hidden'); body.textContent='Loading…'; title.textContent='User #'+id; sub.textContent='—';
    try{
      const res = await fetch(USER(id), { headers:{'X-Requested-With':'XMLHttpRequest'}, credentials:'same-origin' });
      const j = await res.json(); const u=j.user||{}; const L=j.limit||{daily_limit:200,is_enabled:true,reason:''};
      sub.textContent = (u.email||'') + ' — Last seen ' + (u.last_seen_at||'—') + (u.last_ip? (' ('+u.last_ip+')') : '');
      body.innerHTML = `
        <div class="sx-form">
          <div><label class="sx-head-sub">Daily limit</label><input id="sxUdDaily" class="sx-input" type="number" min="0" value="${L.daily_limit}"></div>
          <div><label class="sx-head-sub">Status</label><select id="sxUdEnabled" class="sx-input"><option value="1" ${L.is_enabled?'selected':''}>Enabled</option><option value="0" ${!L.is_enabled?'selected':''}>Disabled</option></select></div>
          <div style="grid-column:1/3"><label class="sx-head-sub">Reason</label><input id="sxUdReason" class="sx-input" type="text" value="${L.reason||''}" placeholder="Optional"></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px">
          <button class="sx-btn sx-primary" onclick="sxSaveLimit(${id})">Save</button>
          <button class="sx-btn sx-ghost" onclick="sxCloseDrawer()">Close</button>
        </div>`;
    }catch(e){ body.innerHTML = '<div style="color:#ffb3bd">Failed to load user.</div>'; }
  };
  window.sxCloseDrawer = function(){ document.getElementById('sxDrawer').classList.add('sx-hidden'); };
  window.sxSaveLimit = async function(id){
    const payload = { daily_limit:Number(document.getElementById('sxUdDaily').value||200), is_enabled:Number(document.getElementById('sxUdEnabled').value||1), reason:document.getElementById('sxUdReason').value||'' };
    try{
      const res = await fetch(LIMIT(id), { method:'PATCH', headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF}, credentials:'same-origin', body: JSON.stringify(payload) });
      if(!res.ok) throw new Error('HTTP '+res.status);
      alert('Saved'); sxCloseDrawer(); sxLoadUsers();
    }catch(e){ alert('Failed to save'); }
  };

  // Hooks
  document.getElementById('sxRefresh')?.addEventListener('click', tick);
  document.getElementById('sxUsersRefresh')?.addEventListener('click', sxLoadUsers);
  document.getElementById('sxUsersSearch')?.addEventListener('input', () => { clearTimeout(window._sxUsersT); window._sxUsersT=setTimeout(sxLoadUsers, 300); });

  // initial loads and intervals
  tick(); setInterval(tick, 10000);
  sxLoadUsers(); setInterval(sxLoadUsers, 15000);
})();
</script>
@endsection
