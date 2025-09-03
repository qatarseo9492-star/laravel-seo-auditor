@extends('layouts.app')
@section('title','Admin — Dashboard')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@2.1.7/css/dataTables.dataTables.min.css">
<style>
  :root{
    --bg:#070d1b; --bg-2:#0c1327; --bg-3:#0f1730; --fg:#e6e9f0; --muted:#9aa6b2; --bdr:rgba(255,255,255,.10);
    --blue-1:#00c6ff; --blue-2:#0072ff; --green-1:#00ff8a; --green-2:#00ffc6; --amber-1:#ffd700; --amber-2:#ffa500; --mag-1:#ff1493; --mag-2:#8a2be2;
    --shadow:0 10px 30px rgba(0,0,0,.35);
  }
  body{ background: radial-gradient(1200px 600px at 70% -200px, rgba(0,114,255,.18), transparent 60%), var(--bg); }
  :is(a,button,input,select,textarea):focus-visible{ outline:2px solid var(--blue-1); outline-offset:2px; }
  .wrap{ max-width: 1320px; margin:18px auto 80px; padding:0 14px; color:var(--fg); }

  .hdr{ display:flex; align-items:center; justify-content:space-between; gap:12px; margin-bottom:8px; }
  .title{ font-weight:900; letter-spacing:.3px; font-size:clamp(20px,2.2vw,28px); display:flex; gap:10px; align-items:center;}
  .badge{ background:linear-gradient(135deg,var(--blue-1),var(--mag-2)); color:#071228; padding:4px 10px; border-radius:999px; font-size:11px; box-shadow:0 8px 18px rgba(0,114,255,.30); }
  .pill{ background: rgba(255,255,255,.06); border:1px solid var(--bdr); border-radius:999px; padding:4px 10px; font-size:12px; color:var(--fg); }

  /* KPI */
  .kpis{ display:grid; grid-template-columns: repeat(4, minmax(220px,1fr)); gap:14px; margin: 14px 0 18px; }
  .kpi{ position:relative; background: var(--bg-3); border:1px solid var(--bdr); border-radius: 18px; padding:16px; box-shadow: var(--shadow); overflow:hidden; }
  .kpi:before{ content:""; position:absolute; inset:-1px; border-radius: 18px; padding:1px; background: linear-gradient(135deg, var(--blue-1), var(--mag-2));
    -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor; mask-composite: exclude; }
  .kpi[data-accent="green"]:before{ background:linear-gradient(135deg,var(--green-1),var(--green-2)); }
  .kpi[data-accent="amber"]:before{ background:linear-gradient(135deg,var(--amber-1),var(--amber-2)); }
  .kpi[data-accent="purple"]:before{ background:linear-gradient(135deg,var(--mag-1),var(--mag-2)); }
  .kpi-title{ font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:.35px; }
  .kpi-val{ font-size: clamp(22px, 3.4vw, 34px); font-weight: 900; line-height: 1.1; margin-top:6px; }

  /* Panels */
  .grid{ display:grid; grid-template-columns: 1.1fr .9fr; gap:14px; }
  .panel{ background: var(--bg-2); border:1px solid var(--bdr); border-radius: 18px; box-shadow: var(--shadow); overflow:hidden; }
  .panel-h{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid var(--bdr); background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,0)); }
  .panel-h h3{ font-size:14px; letter-spacing:.3px; text-transform:uppercase; color:var(--muted); }
  .panel-b{ padding: 14px; }

  /* Table */
  .table{ width:100%; border-collapse:separate; border-spacing:0; }
  .table th, .table td{ padding: 10px 12px; border-bottom: 1px solid var(--bdr); vertical-align: middle; }
  .table thead th{ font-size:12px; text-transform:uppercase; letter-spacing:.35px; color: var(--muted); background: rgba(255,255,255,.04); }
  .table tbody tr:hover{ background: rgba(255,255,255,.03); }

  .in{ width:92px; background: var(--bg-3); border:1px solid var(--bdr); color: var(--fg); border-radius: 10px; padding:7px 10px; }
  .btn{ background: linear-gradient(135deg,var(--blue-1),var(--blue-2)); color:#061024; border:0; border-radius: 12px; padding:8px 12px; font-weight:800; cursor:pointer; box-shadow: 0 10px 20px rgba(0,114,255,.32), inset 0 0 0 1px rgba(255,255,255,.12); }
  .btn.ghost{ color: var(--fg); background: transparent; border:1px solid var(--bdr); box-shadow:none; }
  .btn.danger{ background: linear-gradient(135deg,#ff6b6b,#ff3355); color:#1a0910; }
  .btn.slim{ padding:6px 10px; border-radius:10px; font-weight:700; }

  /* Drawer (user history) */
  .drawer{ position:fixed; top:0; right:-560px; width:560px; height:100vh; background:var(--bg-2); border-left:1px solid var(--bdr); box-shadow: -20px 0 40px rgba(0,0,0,.4);
    transition:right .26s ease; z-index: 50; display:flex; flex-direction:column; }
  .drawer.open{ right:0; }
  .drawer-h{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid var(--bdr); }
  .drawer-b{ padding:12px 14px; overflow:auto; flex:1; }

  @media (max-width: 1100px){ .kpis{ grid-template-columns: repeat(2,1fr);} .grid{ grid-template-columns: 1fr; } .drawer{ width:100%; } }
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="hdr">
    <div class="title">Admin Dashboard <span class="badge">SEO Essentials</span></div>
    <div class="pill">Only admins see global history & usage</div>
  </div>

  {{-- KPIs --}}
  <section class="kpis">
    <div class="kpi" data-accent="blue"><div class="kpi-title">Searches Today</div><div class="kpi-val">{{ $stats['searchesToday'] ?? ($searchesToday ?? 0) }}</div></div>
    <div class="kpi" data-accent="green"><div class="kpi-title">Total Users</div><div class="kpi-val">{{ $stats['totalUsers'] ?? ($totalUsers ?? 0) }}</div></div>
    <div class="kpi" data-accent="amber"><div class="kpi-title">OpenAI Cost Today</div><div class="kpi-val">${{ number_format($stats['costToday'] ?? ($openAiCostToday ?? 0), 4) }}</div></div>
    <div class="kpi" data-accent="purple"><div class="kpi-title">Active Users (live)</div><div class="kpi-val" id="activeLive">{{ $stats['active5m'] ?? ($activeUsers ?? 0) }}</div></div>
  </section>

  {{-- Usage & System --}}
  <section class="grid">
    <div class="panel">
      <div class="panel-h"><h3>OpenAI Usage — Cost (30 days)</h3><span class="pill">USD</span></div>
      <div class="panel-b"><canvas id="openaiChart" height="120"></canvas></div>
    </div>
    <div class="panel">
      <div class="panel-h"><h3>PSI Usage — Requests (30 days)</h3><span class="pill">Count / avg ms</span></div>
      <div class="panel-b"><canvas id="psiChart" height="120"></canvas></div>
    </div>
  </section>

  {{-- System & Presence --}}
  <section class="grid" style="margin-top:14px">
    <div class="panel">
      <div class="panel-h"><h3>System Status</h3></div>
      <div class="panel-b">
        <ul class="table" style="list-style:none; padding:0; margin:0">
          <li class="table-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--bdr)">
            PSI Proxy <span class="pill">{{ ($system['psi'] ?? null) === null ? '—' : (($system['psi'] ?? false) ? 'OK' : 'Down') }}</span>
          </li>
          <li class="table-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--bdr)">
            OpenAI Key <span class="pill">{{ ($system['openai'] ?? null) === null ? '—' : (($system['openai'] ?? false) ? 'Configured' : 'Missing') }}</span>
          </li>
          <li class="table-row" style="display:flex; justify-content:space-between; padding:8px 0;">
            Cache <span class="pill">{{ ($system['cache'] ?? null) === null ? '—' : (($system['cache'] ?? false) ? 'Warm' : 'Cold') }}</span>
          </li>
        </ul>
      </div>
    </div>

    <div class="panel">
      <div class="panel-h"><h3>Active Users (Last Seen / Login / Logout)</h3><button class="btn slim ghost" id="refreshPresence">Refresh</button></div>
      <div class="panel-b" style="overflow:auto">
        <table class="table" id="presenceTbl">
          <thead><tr><th>User</th><th>Last Seen</th><th>Last Login</th><th>Last Logout</th><th>Status</th></tr></thead>
          <tbody>
            @foreach(($presence['online'] ?? []) as $p)
              <tr>
                <td>{{ $p['email'] ?? '—' }}</td>
                <td>{{ $p['last_seen_at'] ?? '—' }}</td>
                <td>{{ $p['last_login_at'] ?? '—' }}</td>
                <td>{{ $p['last_logout_at'] ?? '—' }}</td>
                <td><span class="pill">{{ $p['status'] ?? '—' }}</span></td>
              </tr>
            @endforeach
            @if(empty($presence['online']))<tr><td colspan="5" style="color:var(--muted)">No recent activity.</td></tr>@endif
          </tbody>
        </table>
      </div>
    </div>
  </section>

  {{-- Users --}}
  <section style="margin-top:16px">
    <div class="panel">
      <div class="panel-h">
        <h3>Users</h3>
        <div>
          <button class="btn slim ghost" id="openPwdModal">Change Password</button>
        </div>
      </div>
      <div class="panel-b" style="overflow:auto">
        <table class="table">
          <thead>
            <tr>
              <th>User</th>
              <th>Status / Searches (Today / Month)</th>
              <th>Limits (Daily / Monthly)</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
          @forelse(($users ?? []) as $u)
            <tr>
              <td>
                <div style="font-weight:700">{{ $u->name }}</div>
                <div style="color:var(--muted)">{{ $u->email }}</div>
                <button class="btn slim ghost" data-user-id="{{ $u->id }}" data-user-email="{{ $u->email }}" onclick="openUserDrawer(this)">View History</button>
              </td>
              <td>
                <span class="pill">{{ ($u->banned ?? false) ? 'Banned' : 'Active' }}</span>
                <span class="pill">{{ $u->today_count ?? 0 }} / {{ $u->month_count ?? 0 }}</span>
              </td>
              <td>
                <form class="inline-form"
                      action="{{ route('admin.users.limit', $u->id) }}"
                      method="POST">
                  @csrf
                  @method('PATCH')
                  <input class="in" type="number" name="daily" value="{{ optional($u->limit)->daily ?? 50 }}" min="0">
                  <input class="in" type="number" name="monthly" value="{{ optional($u->limit)->monthly ?? 300 }}" min="0">
                  <button class="btn" type="submit">Save</button>
                </form>
              </td>
              <td>
                <form class="inline-form"
                      action="{{ route('admin.users.ban', $u->id) }}"
                      method="POST"
                      onsubmit="return confirm('Ban/unban this user?')">
                  @csrf
                  @method('PATCH')
                  <button class="btn danger" type="submit">{{ ($u->banned ?? false) ? 'Unban' : 'Ban' }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="4" style="color:var(--muted)">No users yet.</td></tr>
          @endforelse
          </tbody>
        </table>

        @if(method_exists(($users ?? null),'links'))
          <div style="margin-top:10px">{{ $users->links() }}</div>
        @endif
      </div>
    </div>
  </section>

  {{-- Global History --}}
  <section style="margin-top:16px">
    <div class="panel">
      <div class="panel-h">
        <h3>Global Search History</h3>
        <div>
          <input id="histFilter" placeholder="Filter (email/domain/url)" class="in" style="width:260px">
          <button class="btn slim ghost" onclick="window.exportCSV?.()">Export CSV</button>
        </div>
      </div>
      <div class="panel-b" style="overflow:auto">
        <table id="historyTable" class="display" style="width:100%">
          <thead>
            <tr><th>When</th><th>User</th><th>Domain</th><th>URL / Query</th><th>Tool</th><th>Tokens</th><th>Cost</th></tr>
          </thead>
          <tbody>
          @php
            function _domain($u) {
              if(!$u) return '';
              $p = parse_url($u);
              return isset($p['host']) ? $p['host'] : '';
            }
          @endphp
          @forelse(($history ?? []) as $h)
            @php
              $disp = $h->display ?? ($h->query ?? ($h->keyword ?? ($h->search_term ?? ($h->url ?? ''))));
              $url  = $h->url ?? (filter_var($disp, FILTER_VALIDATE_URL) ? $disp : null);
              $dom  = _domain($url);
            @endphp
            <tr>
              <td>{{ optional($h->created_at)->format('Y-m-d H:i') }}</td>
              <td data-email="{{ optional($h->user)->email ?? '' }}">{{ optional($h->user)->email ?? '—' }}</td>
              <td>{{ $dom }}</td>
              <td style="max-width:460px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">
                {{ $disp }}
              </td>
              <td>{{ $h->tool ?? 'Analyzer' }}</td>
              <td>{{ $h->tokens ?? '—' }}</td>
              <td>${{ number_format($h->cost ?? 0, 4) }}</td>
            </tr>
          @empty
            @for($i=0;$i<6;$i++)
              <tr>
                <td>{{ now()->subMinutes($i*9)->format('Y-m-d H:i') }}</td>
                <td>demo@site.com</td>
                <td>example.com</td>
                <td>https://example.com/page-{{ $i }}</td>
                <td>Analyzer</td>
                <td>{{ rand(400,1500) }}</td>
                <td>${{ number_format(rand(1,30)/100, 4) }}</td>
              </tr>
            @endfor
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

</div>

{{-- ===== User Drawer (per-user history) ===== --}}
<aside class="drawer" id="userDrawer" aria-hidden="true">
  <div class="drawer-h">
    <strong id="drawerTitle">User History</strong>
    <button class="btn slim ghost" onclick="closeUserDrawer()">Close</button>
  </div>
  <div class="drawer-b">
    <table class="table" id="userHistTbl">
      <thead><tr><th>When</th><th>URL / Query</th><th>Tool</th><th>Tokens</th><th>Cost</th></tr></thead>
      <tbody><tr><td colspan="5" style="color:var(--muted)">Loading…</td></tr></tbody>
    </table>
  </div>
</aside>

{{-- ===== Password Modal ===== --}}
<dialog id="pwdModal" style="border:none; border-radius:14px; padding:0; width:min(520px, 96vw); background:var(--bg-2); color:var(--fg);">
  <form method="dialog" style="padding:14px; border-bottom:1px solid var(--bdr)">
    <strong>Change User Password</strong>
    <button class="btn slim ghost" value="cancel" style="float:right">Close</button>
  </form>
  <form id="pwdForm" method="POST" style="padding:14px" onsubmit="return submitPwdChange(event)">
    @csrf
    @method('PATCH')
    <input type="hidden" name="user_id" id="pwdUserId">
    <label>Email
      <input class="in" id="pwdEmail" style="width:100%" disabled>
    </label>
    <label style="display:block; margin-top:10px">New Password
      <input class="in" type="password" name="new_password" id="pwdNew" style="width:100%" minlength="8" required>
    </label>
    <div style="display:flex; gap:10px; margin-top:12px">
      <button class="btn" type="submit">Update Password</button>
      <button class="btn ghost" type="button" onclick="document.getElementById('pwdModal').close()">Cancel</button>
    </div>
  </form>
</dialog>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.7/js/dataTables.min.js"></script>
<script>
  // ------ Data passed from controller (safe fallbacks) ------
  const openAiDaily = @json($openAiDaily ?? ['labels'=>[], 'costs'=>[]]);
  const psiDaily    = @json($psiDaily ?? ['labels'=>[], 'counts'=>[], 'avg_ms'=>[]]);

  // ------ Charts ------
  (function(){
    const oc = document.getElementById('openaiChart');
    if (oc && openAiDaily.labels.length){
      new Chart(oc, {
        type:'line',
        data:{ labels: openAiDaily.labels, datasets:[ { label:'Cost', data: openAiDaily.costs, tension:.35, fill:true } ] },
        options:{ scales:{ y:{ beginAtZero:true }}, plugins:{ legend:{ labels:{ color:'#e6e9f0' } } } }
      });
    }
    const pc = document.getElementById('psiChart');
    if (pc && psiDaily.labels.length){
      const ds = [{ label:'Requests', data: psiDaily.counts, tension:.35, fill:true }];
      if ((psiDaily.avg_ms||[]).length){ ds.push({ label:'Avg ms', data: psiDaily.avg_ms, yAxisID:'y1' }); }
      new Chart(pc, {
        type:'line',
        data:{ labels: psiDaily.labels, datasets: ds },
        options:{ scales:{ y:{ beginAtZero:true }, y1:{ beginAtZero:true, position:'right' }}, plugins:{ legend:{ labels:{ color:'#e6e9f0' } } } }
      });
    }
  })();

  // ------ DataTable (Global History) ------
  let histDT;
  (function(){
    if (!window.DataTable) return;
    histDT = new DataTable('#historyTable', { pageLength: 12, lengthChange: false, order:[[0,'desc']] });
    const input = document.getElementById('histFilter');
    if (input) input.addEventListener('input', ()=> histDT.search(input.value).draw());
  })();

  // Export CSV
  window.exportCSV = () => {
    const rows = [...document.querySelectorAll('#historyTable tbody tr')].map(tr=> [...tr.children].map(td=> '"'+td.innerText.replace(/"/g,'\\"')+'"').join(','));
    const csv = ['When,User,Domain,URL/Query,Tool,Tokens,Cost', ...rows].join('\n');
    const blob = new Blob([csv], {type:'text/csv'});
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'global-history.csv'; a.click(); URL.revokeObjectURL(a.href);
  };

  // ------ Live Presence (auto-refresh every 30s via the SAME route) ------
  async function refreshPresence(){
    try{
      const res = await fetch(`{{ route('admin.dashboard') }}?partial=presence`, { headers:{'X-Requested-With':'fetch'} });
      if(!res.ok) return;
      const data = await res.json();
      document.getElementById('activeLive').textContent = data.activeCount ?? 0;

      const tbody = document.querySelector('#presenceTbl tbody');
      tbody.innerHTML = '';
      (data.online||[]).forEach(p=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${p.email||'—'}</td><td>${p.last_seen_at||'—'}</td><td>${p.last_login_at||'—'}</td><td>${p.last_logout_at||'—'}</td><td><span class="pill">${p.status||'—'}</span></td>`;
        tbody.appendChild(tr);
      });
      if(!(data.online||[]).length){
        const tr = document.createElement('tr'); tr.innerHTML = `<td colspan="5" style="color:var(--muted)">No recent activity.</td>`; tbody.appendChild(tr);
      }
    }catch(e){}
  }
  document.getElementById('refreshPresence')?.addEventListener('click', refreshPresence);
  setInterval(refreshPresence, 30000);

  // ------ Per-user drawer ------
  window.openUserDrawer = async (btn)=>{
    const id = btn.getAttribute('data-user-id');
    const email = btn.getAttribute('data-user-email');
    document.getElementById('drawerTitle').textContent = `History — ${email}`;
    const dr = document.getElementById('userDrawer'); dr.classList.add('open');

    const tbody = document.querySelector('#userHistTbl tbody');
    tbody.innerHTML = `<tr><td colspan="5" style="color:var(--muted)">Loading…</td></tr>`;
    try{
      const res = await fetch(`{{ route('admin.dashboard') }}?partial=userHistory&user_id=${encodeURIComponent(id)}`, { headers:{'X-Requested-With':'fetch'} });
      const data = await res.json();
      tbody.innerHTML = '';
      (data.items||[]).forEach(h=>{
        const tr = document.createElement('tr');
        tr.innerHTML = `<td>${h.when||''}</td><td style="max-width:320px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${h.display||''}</td><td>${h.tool||''}</td><td>${h.tokens??'—'}</td><td>${h.cost??'0.0000'}</td>`;
        tbody.appendChild(tr);
      });
      if(!(data.items||[]).length){
        tbody.innerHTML = `<tr><td colspan="5" style="color:var(--muted)">No history.</td></tr>`;
      }
    }catch(e){
      tbody.innerHTML = `<tr><td colspan="5" style="color:var(--muted)">Failed to load.</td></tr>`;
    }
  };
  window.closeUserDrawer = ()=> document.getElementById('userDrawer').classList.remove('open');

  // ------ Password Change (admin) ------
  const pwdModal = document.getElementById('pwdModal');
  document.getElementById('openPwdModal')?.addEventListener('click', ()=>{
    const row = document.querySelector('table.table tbody tr'); // pick first user row to preload form; you can add a per-user button if preferred
    if(row){
      const email = row.querySelector('td:nth-child(1) div[style*="color"]').textContent.trim();
      const id = row.querySelector('[data-user-id]')?.getAttribute('data-user-id');
      document.getElementById('pwdEmail').value = email||'';
      document.getElementById('pwdUserId').value = id||'';
    }
    pwdModal.showModal();
  });

  async function submitPwdChange(e){
    e.preventDefault();
    const id = document.getElementById('pwdUserId').value;
    const pass = document.getElementById('pwdNew').value;
    if(!id || !pass) return;
    // POST to existing ban route with a special field new_password (Controller checks & updates)
    const res = await fetch(`{{ url('/admin/users') }}/${encodeURIComponent(id)}/ban`, {
      method:'POST',
      headers:{'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept':'application/json'},
      body: new URLSearchParams({ _method:'PATCH', new_password: pass })
    });
    pwdModal.close();
    alert(res.ok ? 'Password updated.' : 'Request sent. Verify controller handles new_password.');
    return false;
  }
  window.submitPwdChange = submitPwdChange;
</script>
@endpush
