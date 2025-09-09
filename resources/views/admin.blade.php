<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard — Aurora v4</title>

    {{-- All necessary styles are embedded directly to prevent any conflicts --}}
    <style>
      :root{
        --bg:#070b1a; --bg-2:#0b1226; --card:#0e1632; --muted:#9aa6b2; --text:#e8eef5;
        --primary:#6ee7ff; --primary-2:#a78bfa; --ok:#19e58b; --warn:#ffb020; --error:#ff5063;
        --shadow: 0 10px 30px rgba(0,0,0,.45), inset 0 1px 0 rgba(255,255,255,.04);
      }
      *{ box-sizing: border-box; }
      body {
        background: radial-gradient(1200px 800px at 0% -10%, rgba(167,139,250,.15), transparent 40%), radial-gradient(900px 480px at 100% 10%, rgba(110,231,255,.12), transparent 45%), var(--bg);
        color: var(--text);
        font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
        margin: 0;
      }
      .aurora-container{ max-width:1200px; margin:24px auto; padding:0 16px; }
      .topbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; flex-wrap: wrap; gap: 10px;}
      .brand{ display:flex; gap:12px; align-items:center; }
      .logo{ width:28px; height:28px; border-radius:8px; background:linear-gradient(135deg,var(--primary),var(--primary-2)); display:inline-flex; align-items:center; justify-content:center; color:#08111e; font-weight:800; box-shadow:0 0 0 2px rgba(255,255,255,.06); }
      .title h1{ margin:0; font-size:22px; font-weight:800; letter-spacing:.2px; }
      .badge{ display:inline-block; margin-top:4px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.1); padding:2px 8px; border-radius:999px; font-size:12px; color:var(--muted); }
      .toolbar{ display:flex; gap:10px; }
      .btn{ border:0; border-radius:12px; padding:10px 14px; font-weight:700; cursor:pointer; transition:.2s box-shadow,.2s transform; text-decoration: none; }
      .btn.primary{ background:linear-gradient(135deg,var(--primary),var(--primary-2)); color:#051225; box-shadow:0 10px 30px rgba(110,231,255,.16); }
      .btn.ghost{ background:rgba(255,255,255,.06); color:var(--text); border:1px solid rgba(255,255,255,.08); }
      .btn.outline{ background:transparent; border:1px solid rgba(255,255,255,.16); color:var(--text); width: 100%; text-align: center; }
      .btn:hover{ box-shadow:0 12px 34px rgba(0,0,0,.5); transform:translateY(-1px); }
      
      .grid{ display:grid; grid-gap:16px; }
      .grid.kpis{ grid-template-columns:repeat(4,1fr); }
      .grid.main{ grid-template-columns:2fr 1fr; grid-auto-rows:minmax(120px,auto); }
      .span-2{ grid-column:span 2; }
      .span-3{ grid-column: 1 / -1; }

      .card{ background:linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.01)); border:1px solid rgba(255,255,255,.08); border-radius:16px; padding:16px; box-shadow:var(--shadow); overflow: hidden; }
      .card-head{ display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
      .card-title{ font-weight:800; }
      .card-sub{ color:var(--muted); font-size:12px; margin-top:4px; }
      .chip{ border-radius:999px; padding:6px 10px; font-size:12px; border:1px solid rgba(255,255,255,.12); }
      .chip.ok{ background:rgba(25,229,139,.08); color:#b9ffd7; }
      
      .kpi .kpi-label{ color:var(--muted); font-size:12px; }
      .kpi .kpi-value{ font-size:28px; font-weight:800; margin-top:6px; }
      .kpi .kpi-sub{ color:var(--muted); font-size:12px; margin-top:4px; }
      
      .table{ width:100%; border-collapse:collapse; }
      .table th{ color:var(--muted); text-align:left; font-weight:600; font-size:12px; padding:10px 12px; border-bottom: 1px solid rgba(255,255,255,.1); }
      .table td{ padding:12px; vertical-align: middle; }
      .table tbody tr:not(:last-child) td { border-bottom: 1px solid rgba(255,255,255,0.05); }
      .table .muted{ color:var(--muted); text-align:center; padding:20px 8px; }
      
      .bubbles{ display:flex; gap:8px; flex-wrap:wrap; margin:10px 0; }
      .bubble{ background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.08); border-radius:999px; padding:6px 10px; color:var(--text); font-size:12px; }
      .dot{ display:inline-block; width:8px; height:8px; border-radius:999px; margin-right:6px; background:rgba(255,255,255,.2); }
      .dot.ok{ background:var(--ok); } .dot.warn{ background:var(--error); }
      
      .input{ background:rgba(255,255,255,.04); border:1px solid rgba(255,255,255,.08); border-radius:10px; padding:8px 10px; color:var(--text); min-width:220px; }
      .head-actions{ display:flex; gap: 8px; align-items:center; }
      
      .user-table .user-name { font-weight: 600; }
      .user-table .user-email { font-size: 12px; color: var(--muted); }
      .btn-details { background: transparent; border: 1px solid rgba(255,255,255,.15); color: var(--text); padding: 6px 12px; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 12px;}
      .btn-details:hover { background: rgba(255,255,255,.05); }
      
      .pagination-links { margin-top: 20px; font-size: 14px; }
      .pagination-links nav { display: flex; justify-content: center; }
      .pagination-links .pagination { display: flex; list-style: none; padding: 0; margin: 0; gap: 4px; }
      .pagination-links .page-item .page-link, .pagination-links .page-item span { padding: 6px 12px; background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 8px; color: var(--muted); text-decoration: none; }
      .pagination-links .page-item.active span { background: linear-gradient(135deg, var(--primary), var(--primary-2)); color: #051225; border-color: var(--primary); }
      .pagination-links .page-item.disabled span { opacity: 0.5; cursor: not-allowed; }
      .pagination-links .page-item a.page-link:hover { background-color: rgba(255, 255, 255, 0.1); }

      @media (max-width: 960px) {
        .grid.kpis{ grid-template-columns:repeat(2,1fr); }
        .grid.main{ grid-template-columns:1fr; }
        .span-2{ grid-column:span 1; }
        .span-3{ grid-column:span 1; }
        .card-head { flex-direction: column; align-items: flex-start; gap: 10px;}
      }

      @media (max-width: 640px) {
        .grid.kpis{ grid-template-columns: 1fr; }
        .user-table-card { overflow-x: auto; }
        .aurora-container{ padding:0 8px; }
        .topbar { justify-content: center; }
      }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>

<div class="aurora-container">
  {{-- TOP BAR --}}
  <div class="topbar">
    <div class="brand">
      <span class="logo">∎</span>
      <div class="title">
        <h1>Admin Dashboard</h1>
        <span class="badge">Aurora v4</span>
      </div>
    </div>
    <div class="toolbar">
      <button class="btn ghost" onclick="window.location.reload()">Refresh</button>
      <a class="btn primary" href="#">Manage Users</a>
    </div>
  </div>

  {{-- KPI CARDS ROW --}}
  <div class="grid kpis">
    <div class="card kpi">
      <div class="kpi-label">Searches Today</div>
      <div class="kpi-value">{{ number_format($stats['searchesToday'] ?? 0) }}</div>
      <div class="kpi-sub">Active (5m): <span>{{ number_format($stats['active5m'] ?? 0) }}</span></div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">Total Users</div>
      <div class="kpi-value">{{ number_format($stats['totalUsers'] ?? 0) }}</div>
      <div class="kpi-sub">DAU: {{ number_format($stats['dau'] ?? 0) }}</div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">OpenAI Cost (24h)</div>
      <div class="kpi-value">${{ number_format($stats['cost24h'] ?? 0, 4) }}</div>
      <div class="kpi-sub"><span>{{ number_format($stats['tokens24h'] ?? 0) }}</span> tokens</div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">DAU / MAU</div>
      <div class="kpi-value"><span>{{ number_format($stats['dau'] ?? 0) }}</span> / <span>{{ number_format($stats['mau'] ?? 0) }}</span></div>
      <div class="kpi-sub">Engagement</div>
    </div>
  </div>

  {{-- MAIN CONTENT GRID --}}
  <div class="grid main">
    {{-- System Health Card --}}
    <div class="card span-2">
      <div class="card-head">
        <div>
          <div class="card-title">System Health</div>
          <div class="card-sub">Live snapshots of core services</div>
        </div>
        <div class="chip ok">All Systems Go</div>
      </div>
      <div style="overflow-x:auto;">
          <table class="table" id="system-health">
            <thead><tr><th>Service</th><th>Status</th><th>Latency</th></tr></thead>
            <tbody>
            @forelse($systemHealth ?? [] as $service)
              <tr>
                <td>{{ $service['name'] }}</td>
                <td>
                    <span class="dot {{ ($service['ok'] ?? false) ? 'ok' : 'warn' }}"></span>
                    {{ $service['status'] }}
                </td>
                <td>{{ $service['latency'] ?? 'N/A' }}ms</td>
              </tr>
            @empty
              <tr><td colspan="3" class="muted">No data yet.</td></tr>
            @endforelse
            </tbody>
          </table>
      </div>
    </div>

    {{-- User Limits Card --}}
    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">User Limits</div>
          <div class="card-sub">Read-only summary</div>
        </div>
      </div>
      <div class="bubbles">
        <div class="bubble"><span class="dot ok"></span> Enabled: <b>{{ $limitsSummary['enabled'] ?? 0 }}</b></div>
        <div class="bubble"><span class="dot warn"></span> Disabled: <b>{{ $limitsSummary['disabled'] ?? 0 }}</b></div>
        <div class="bubble"><span class="dot"></span> Default: <b>{{ $limitsSummary['default'] ?? 200 }}</b></div>
      </div>
      <a href="#" class="btn outline" style="margin-top:10px;">Adjust Limits</a>
    </div>

    {{-- User Overview Card --}}
    <div class="card span-3 user-table-card">
      <div class="card-head">
        <div>
          <div class="card-title">User Overview</div>
          <div class="card-sub">Recent users and their activity</div>
        </div>
        <div class="head-actions">
          <input id="historyFilter" class="input" placeholder="Filter by email or name..." />
        </div>
      </div>
      <div style="overflow-x:auto;">
          <table class="table user-table" id="global-history">
            <thead><tr><th>User</th><th>Daily Searches</th><th>Last Login IP</th><th>Actions</th></tr></thead>
            <tbody>
                @forelse ($users ?? [] as $user)
                <tr>
                    <td>
                        <div class="user-name">{{ $user->name }}</div>
                        <div class="user-email">{{ $user->email }}</div>
                    </td>
                    <td>{{ $user->today_count }} / {{ optional($user->limit)->daily ?? 200 }}</td>
                    <td>{{ $user->last_login_ip ?? 'N/A' }}</td>
                    <td><button class="btn-details">View</button></td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="muted">No users found.</td>
                </tr>
                @endforelse
            </tbody>
          </table>
      </div>
       <div class="pagination-links">
            {{ ($users ?? null) && method_exists($users, 'links') ? $users->links() : '' }}
        </div>
    </div>
  </div>
</div>

</body>
</html>
