@extends('layouts.app')
@section('title','Admin — SEO Essentials')

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@2.1.7/css/dataTables.dataTables.min.css">
<style>
  /* ==================== Neon SEO Admin (Focused Pack) ==================== */
  :root{
    --bg:#070d1b; --bg-2:#0c1327; --bg-3:#0f1730; --fg:#e6e9f0; --muted:#9aa6b2; --bdr:rgba(255,255,255,.10);
    --blue-1:#00c6ff; --blue-2:#0072ff; --green-1:#00ff8a; --green-2:#00ffc6; --amber-1:#ffd700; --amber-2:#ffa500; --mag-1:#ff1493; --mag-2:#8a2be2;
    --shadow:0 10px 30px rgba(0,0,0,.35);
  }
  body{ background: radial-gradient(1200px 600px at 70% -200px, rgba(0,114,255,.18), transparent 60%), var(--bg); }
  :is(a,button,input,select,textarea):focus-visible{ outline:2px solid var(--blue-1); outline-offset:2px; }

  /* Layout */
  .layout{ display:grid; grid-template-columns: 220px 1fr; gap:0; min-height: calc(100vh - 64px); }
  .aside{ position:sticky; top:64px; align-self:start; height: calc(100vh - 64px); overflow:auto; background: var(--bg-2); border-right:1px solid var(--bdr); }
  .main{ padding:18px; }

  /* Sidebar */
  .brand{ font-weight:900; letter-spacing:.4px; padding:14px 16px; }
  .side-nav{ padding:8px; display:flex; flex-direction:column; gap:6px; }
  .nav-label{ color: var(--muted); font-size:11px; letter-spacing:.6px; text-transform:uppercase; padding:8px 12px; }
  .nav-item{ display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:12px; color:var(--fg); text-decoration:none; border:1px solid transparent; }
  .nav-item:hover{ background: rgba(255,255,255,.04); border-color: var(--bdr); }
  .nav-item.active{ background: linear-gradient(135deg,var(--blue-1),var(--blue-2)); color:#071228; box-shadow: 0 10px 18px rgba(0,114,255,.35); }

  /* Topbar */
  .topbar{ position: sticky; top:0; z-index: 3; background: rgba(7,13,27,.65); backdrop-filter: blur(8px); border-bottom:1px solid var(--bdr); }
  .topbar-i{ height:64px; max-width: 1280px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; padding:0 12px; }
  .pill{ background: rgba(255,255,255,.06); border:1px solid var(--bdr); border-radius: 999px; padding:4px 10px; font-size:12px; }

  /* KPI */
  .kpis{ display:grid; grid-template-columns: repeat(4, minmax(220px,1fr)); gap:14px; margin: 14px 0 18px; }
  .kpi{ position:relative; background: var(--bg-3); border:1px solid var(--bdr); border-radius: 18px; padding:16px; box-shadow: var(--shadow); overflow:hidden; }
  .kpi:before{ content:""; position:absolute; inset:-1px; border-radius: 18px; padding:1px; background: linear-gradient(135deg, var(--blue-1), var(--mag-2)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor; mask-composite: exclude; }
  .kpi[data-accent="green"]:before{ background: linear-gradient(135deg,var(--green-1),var(--green-2)); }
  .kpi[data-accent="amber"]:before{ background: linear-gradient(135deg,var(--amber-1),var(--amber-2)); }
  .kpi[data-accent="purple"]:before{ background: linear-gradient(135deg,var(--mag-1),var(--mag-2)); }
  .kpi-title{ font-size:12px; color: var(--muted); text-transform: uppercase; letter-spacing:.35px; }
  .kpi-val{ font-size: clamp(22px, 3.4vw, 34px); font-weight: 900; line-height: 1.1; margin-top:6px; }

  /* Panels */
  .grid{ display:grid; grid-template-columns: 1.1fr .9fr; gap:14px; }
  .panel{ background: var(--bg-2); border:1px solid var(--bdr); border-radius: 18px; box-shadow: var(--shadow); overflow:hidden; }
  .panel-h{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid var(--bdr); background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,0)); }
  .panel-h h3{ font-size:14px; letter-spacing:.3px; text-transform: uppercase; color: var(--muted); }
  .panel-b{ padding: 14px; }

  /* Table */
  .table{ width:100%; border-collapse:separate; border-spacing:0; }
  .table th, .table td{ padding: 10px 12px; border-bottom: 1px solid var(--bdr); vertical-align: middle; }
  .table thead th{ font-size:12px; text-transform:uppercase; letter-spacing:.35px; color: var(--muted); background: rgba(255,255,255,.04); }
  .table tbody tr:hover{ background: rgba(255,255,255,.03); }

  .in{ width:84px; background: var(--bg-3); border:1px solid var(--bdr); color: var(--fg); border-radius: 10px; padding:7px 10px; }
  .btn{ background: linear-gradient(135deg,var(--blue-1),var(--blue-2)); color:#061024; border:0; border-radius: 12px; padding:8px 12px; font-weight:800; cursor:pointer; box-shadow: 0 10px 20px rgba(0,114,255,.32), inset 0 0 0 1px rgba(255,255,255,.12); }
  .btn.ghost{ color: var(--fg); background: transparent; border:1px solid var(--bdr); box-shadow:none; }
  .btn.danger{ background: linear-gradient(135deg,#ff6b6b,#ff3355); color:#1a0910; }

  @media (max-width: 1100px){ .layout{ grid-template-columns: 72px 1fr; } .nav-label{ display:none; } .nav-item span{ display:none; } .kpis{ grid-template-columns: repeat(2,1fr);} .grid{ grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<header class="topbar"><div class="topbar-i"><div class="brand">Admin • SEO Essentials</div><div class="pill">Focused for Semantic Analyzer</div></div></header>

<div class="layout">
  <aside class="aside">
    <nav class="side-nav">
      <div class="nav-label">Sections</div>
      <a class="nav-item active" href="#overview"><span>Overview</span></a>
      <a class="nav-item" href="#users"><span>Users</span></a>
      <a class="nav-item" href="#history"><span>Search History</span></a>
      <a class="nav-item" href="#analytics"><span>Analytics</span></a>
      <a class="nav-item" href="#settings"><span>Settings</span></a>
    </nav>
  </aside>

  <main class="main">
    <!-- ===== Overview ===== -->
    <section id="overview">
      <div class="kpis">
        <div class="kpi" data-accent="blue">
          <div class="kpi-title">Searches Today</div>
          <div class="kpi-val">{{ $stats['searchesToday'] ?? 0 }}</div>
        </div>
        <div class="kpi" data-accent="green">
          <div class="kpi-title">Total Users</div>
          <div class="kpi-val">{{ $stats['totalUsers'] ?? 0 }}</div>
        </div>
        <div class="kpi" data-accent="amber">
          <div class="kpi-title">OpenAI Cost Today</div>
          <div class="kpi-val">${{ number_format($stats['costToday'] ?? 0, 4) }}</div>
        </div>
        <div class="kpi" data-accent="purple">
          <div class="kpi-title">Active Users (5 min)</div>
          <div class="kpi-val">{{ $stats['active5m'] ?? 0 }}</div>
        </div>
      </div>

      <div class="grid">
        <div class="panel">
          <div class="panel-h"><h3>Usage & Cost (30d)</h3><span class="pill">Chart.js</span></div>
          <div class="panel-b"><canvas id="usageChart" height="120"></canvas></div>
        </div>
        <div class="panel">
          <div class="panel-h"><h3>System Status</h3></div>
          <div class="panel-b">
            <ul class="table" style="list-style:none; padding:0; margin:0">
              <li class="table-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--bdr)">PSI Proxy <span class="pill">{{ ($system['psi'] ?? true) ? 'OK' : 'Down' }}</span></li>
              <li class="table-row" style="display:flex; justify-content:space-between; padding:8px 0; border-bottom:1px solid var(--bdr)">OpenAI Key <span class="pill">{{ ($system['openai'] ?? true) ? 'Configured' : 'Missing' }}</span></li>
              <li class="table-row" style="display:flex; justify-content:space-between; padding:8px 0;">Cache <span class="pill">{{ ($system['cache'] ?? true) ? 'Warm' : 'Cold' }}</span></li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== Users ===== -->
    <section id="users" style="margin-top:16px">
      <div class="panel">
        <div class="panel-h"><h3>User Management</h3></div>
        <div class="panel-b" style="overflow:auto">
          <table class="table">
            <thead><tr><th>User</th><th>Status / Searches (Today / Month)</th><th>Limits (Daily / Monthly)</th><th>Actions</th></tr></thead>
            <tbody>
              @forelse(($users ?? []) as $u)
                <tr>
                  <td>
                    <div style="font-weight:700">{{ $u->name }}</div>
                    <div style="color:var(--muted)">{{ $u->email }}</div>
                  </td>
                  <td>
                    <span class="pill">{{ $u->status ?? 'Active' }}</span>
                    <span class="pill">{{ $u->today_count ?? 0 }} / {{ $u->month_count ?? 0 }}</span>
                  </td>
                  <td>
                    <form class="inline-form" action="{{ route('admin.users.updateLimits', $u->id) }}" method="POST">
                      @csrf
                      <input class="in" type="number" name="daily" value="{{ $u->limit_daily ?? 50 }}" min="0">
                      <input class="in" type="number" name="monthly" value="{{ $u->limit_monthly ?? 300 }}" min="0">
                      <button class="btn" type="submit">Save</button>
                    </form>
                  </td>
                  <td>
                    <form class="inline-form" action="{{ route('admin.users.toggleBan', $u->id) }}" method="POST" onsubmit="return confirm('Ban/unban this user?')">
                      @csrf
                      <button class="btn danger" type="submit">{{ ($u->banned ?? false) ? 'Unban' : 'Ban' }}</button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr><td colspan="4" style="color:var(--muted)">No users yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- ===== History ===== -->
    <section id="history" style="margin-top:16px">
      <div class="panel">
        <div class="panel-h"><h3>Search History</h3><button class="btn ghost" onclick="window.exportCSV?.()">Export CSV</button></div>
        <div class="panel-b" style="overflow:auto">
          <table id="historyTable" class="display" style="width:100%">
            <thead>
              <tr><th>When</th><th>User</th><th>Query / URL</th><th>Tool</th><th>Tokens</th><th>Cost</th></tr>
            </thead>
            <tbody>
              @forelse(($history ?? []) as $h)
                <tr>
                  <td>{{ optional($h->created_at)->format('Y-m-d H:i') }}</td>
                  <td>{{ $h->user->email ?? '—' }}</td>
                  <td style="max-width:460px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $h->query ?? $h->url }}</td>
                  <td>{{ $h->tool ?? 'Analyzer' }}</td>
                  <td>{{ $h->tokens ?? '—' }}</td>
                  <td>${{ number_format($h->cost ?? 0, 4) }}</td>
                </tr>
              @empty
                @for($i=0;$i<6;$i++)
                  <tr>
                    <td>{{ now()->subMinutes($i*9)->format('Y-m-d H:i') }}</td>
                    <td>demo@site.com</td>
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

    <!-- ===== Analytics ===== -->
    <section id="analytics" style="margin-top:16px">
      <div class="panel">
        <div class="panel-h"><h3>Top Queries / Pages</h3></div>
        <div class="panel-b">
          <ul style="margin:0; padding:0; list-style:none; display:grid; grid-template-columns: repeat(2,1fr); gap:8px">
            @foreach(($topItems ?? []) as $it)
              <li class="kpi" style="padding:10px" data-accent="blue">
                <div style="display:flex; justify-content:space-between; align-items:center">
                  <span style="font-weight:700; max-width:76%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $it['name'] }}</span>
                  <span class="pill">{{ $it['count'] }}</span>
                </div>
              </li>
            @endforeach
            @if(empty($topItems))
              @for($i=1;$i<=6;$i++)
                <li class="kpi" style="padding:10px" data-accent="blue">
                  <div style="display:flex; justify-content:space-between; align-items:center">
                    <span style="font-weight:700">Placeholder item {{ $i }}</span>
                    <span class="pill">{{ rand(10,120) }}</span>
                  </div>
                </li>
              @endfor
            @endif
          </ul>
        </div>
      </div>
    </section>

    <!-- ===== Settings ===== -->
    <section id="settings" style="margin-top:16px">
      <div class="panel">
        <div class="panel-h"><h3>Settings</h3></div>
        <div class="panel-b">
          <form style="display:grid; grid-template-columns: repeat(3,1fr); gap:10px">
            <label>Daily Search Limit
              <input class="in" style="width:100%" type="number" min="0" value="{{ $settings['daily_limit'] ?? 50 }}"/>
            </label>
            <label>Monthly Search Limit
              <input class="in" style="width:100%" type="number" min="0" value="{{ $settings['monthly_limit'] ?? 300 }}"/>
            </label>
            <label>OpenAI Enabled
              <input type="checkbox" {{ ($settings['openai'] ?? true) ? 'checked' : '' }}>
            </label>
          </form>
        </div>
      </div>
    </section>
  </main>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.7/js/dataTables.min.js"></script>
<script>
  // Chart (Usage & Cost)
  const ctx = document.getElementById('usageChart');
  if(ctx){
    new Chart(ctx, { type:'line', data:{ labels:[...Array(30).keys()].map(i=>i+1), datasets:[
      { label:'Searches', data:[...Array(30)].map(()=> Math.floor(50+Math.random()*60)), tension:.35, fill:true },
      { label:'Cost', data:[...Array(30)].map(()=> (Math.random()*2).toFixed(2)), yAxisID:'y1' }
    ]}, options:{ scales:{ y:{ beginAtZero:true }, y1:{ beginAtZero:true, position:'right' } }, plugins:{ legend:{ labels:{ color:'#e6e9f0' } }}});
  }

  // DataTable (Search History)
  if (window.DataTable){ new DataTable('#historyTable', { pageLength: 8, lengthChange: false, order:[[0,'desc']] }); }

  // Export CSV utility (client-side)
  window.exportCSV = () => {
    const rows = [...document.querySelectorAll('#historyTable tbody tr')].map(tr=> [...tr.children].map(td=> '"'+td.innerText.replace(/"/g,'\"')+'"').join(','));
    const csv = ['When,User,Query/URL,Tool,Tokens,Cost', ...rows].join('\n');
    const blob = new Blob([csv], {type:'text/csv'});
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'search-history.csv'; a.click(); URL.revokeObjectURL(a.href);
  };

  // Sidebar active state on anchor navigation
  const links = document.querySelectorAll('.side-nav .nav-item');
  links.forEach(a=> a.addEventListener('click', ()=>{ links.forEach(l=> l.classList.remove('active')); a.classList.add('active'); }));
</script>
@endpush
