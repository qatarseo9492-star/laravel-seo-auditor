@extends('layouts.app')
@section('title','Admin — Dashboard')

@push('head')
<style>
  /* =======================  Neon Admin Theme  ======================= */
  :root{
    /* Core palette */
    --bg: #070d1b;             /* page background */
    --bg-2: #0c1327;            /* panels */
    --bg-3: #0f1730;            /* cards */
    --fg: #e6e9f0;              /* text */
    --muted: #9aa6b2;           /* secondary text */

    /* Accents */
    --blue-1: #00c6ff;          /* gradient start */
    --blue-2: #0072ff;          /* gradient end */
    --green-1:#00ff8a; --green-2:#00ffc6;
    --amber-1:#ffd700; --amber-2:#ffa500;
    --mag-1:#ff1493;  --mag-2:#8a2be2;

    --surface: rgba(255,255,255,.06);
    --border-weak: rgba(255,255,255,.10);
    --shadow: 0 10px 30px rgba(0,0,0,.35);
  }

  /* Respect outlines for accessibility */
  :is(a,button,input,select,textarea):focus-visible{
    outline: 2px solid var(--blue-1);
    outline-offset: 2px;
  }

  body{ background: radial-gradient(1200px 600px at 70% -200px, rgba(0,114,255,.20), transparent 60%), var(--bg); }

  .admin-wrap{ max-width: 1200px; margin: 24px auto 80px; padding: 0 16px; color: var(--fg); }

  /* Header */
  .ad-header{ display:flex; align-items:center; gap:16px; justify-content:space-between; margin-bottom: 18px; }
  .ad-title{ font-size: clamp(20px, 2.2vw, 28px); font-weight: 800; letter-spacing: .3px; display:flex; align-items:center; gap:10px; }
  .ad-title .badge{ font-size: 11px; color:#081226; background: linear-gradient(135deg,var(--blue-1),var(--mag-2)); padding:4px 10px; border-radius:999px; box-shadow: 0 0 0 1px rgba(255,255,255,.06) inset, 0 4px 16px rgba(0, 114, 255, .30); }

  .ad-actions{ display:flex; gap:10px; align-items:center; }
  .seg{ background: var(--bg-2); border: 1px solid var(--border-weak); padding:4px; border-radius: 999px; display:flex; gap:4px; box-shadow: var(--shadow); }
  .seg button{ background: transparent; border: 0; color: var(--muted); font-weight: 600; letter-spacing:.2px; padding:8px 12px; border-radius: 999px; cursor:pointer; transition: all .2s ease; }
  .seg button[aria-pressed="true"]{ color: #071228; background: linear-gradient(135deg,var(--blue-1),var(--blue-2)); box-shadow: 0 8px 22px rgba(0,114,255,.35), inset 0 0 0 1px rgba(255,255,255,.12); }

  /* KPI grid */
  .kpi-grid{ display:grid; grid-template-columns: repeat(4, minmax(180px,1fr)); gap:14px; margin: 14px 0 22px; }
  .kpi{ position:relative; background: var(--bg-3); border:1px solid var(--border-weak); border-radius: 18px; padding:16px; box-shadow: var(--shadow); overflow:hidden; }
  .kpi:before{ content:""; position:absolute; inset:-1px; border-radius: 18px; padding:1px; background: linear-gradient(135deg, var(--blue-1), var(--mag-2)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor; mask-composite: exclude; }
  .kpi[data-accent="green"]:before{ background: linear-gradient(135deg,var(--green-1),var(--green-2)); }
  .kpi[data-accent="amber"]:before{ background: linear-gradient(135deg,var(--amber-1),var(--amber-2)); }
  .kpi[data-accent="purple"]:before{ background: linear-gradient(135deg,var(--mag-1),var(--mag-2)); }
  .kpi-title{ font-size:12px; color: var(--muted); letter-spacing:.4px; text-transform: uppercase; }
  .kpi-val{ font-size: clamp(22px, 3.4vw, 34px); font-weight: 900; line-height: 1.1; margin-top: 6px; }
  .kpi-sub{ font-size:12px; color: var(--muted); margin-top: 6px; display:flex; align-items:center; gap:6px; }
  .spark{ display:flex; gap:3px; align-items:flex-end; margin-left:auto; height:16px; }
  .spark i{ width:3px; background: linear-gradient(180deg, rgba(0,198,255,.0), rgba(0,198,255,.9)); border-radius:2px; }

  /* Panels */
  .panels{ display:grid; grid-template-columns: 1.2fr .8fr; gap:14px; margin-bottom: 18px; }
  .panel{ background: var(--bg-2); border:1px solid var(--border-weak); border-radius: 18px; box-shadow: var(--shadow); overflow:hidden; }
  .panel-h{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid var(--border-weak); background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,0)); }
  .panel-h h3{ font-size:14px; letter-spacing:.3px; text-transform: uppercase; color: var(--muted); }
  .panel-b{ padding: 14px; }

  .mini-legend{ display:flex; gap:10px; font-size:12px; color: var(--muted); }
  .dot{ width:8px; height:8px; border-radius:999px; display:inline-block; }
  .dot.blue{ background: linear-gradient(135deg,var(--blue-1),var(--blue-2)); }
  .dot.green{ background: linear-gradient(135deg,var(--green-1),var(--green-2)); }

  /* Tabs */
  .tabs{ display:flex; gap:8px; border-bottom: 1px solid var(--border-weak); padding: 8px; position: sticky; top:0; backdrop-filter: blur(6px); background: rgba(7,13,27,.6); z-index: 1; }
  .tab-btn{ background: var(--bg-2); border:1px solid var(--border-weak); color: var(--muted); padding:8px 12px; border-radius: 999px; font-weight:700; cursor:pointer; transition:.2s; }
  .tab-btn[aria-selected="true"]{ color:#071228; background: linear-gradient(135deg,var(--blue-1),var(--blue-2)); box-shadow: 0 8px 18px rgba(0,114,255,.35); }
  .tab-panel{ display:none; }
  .tab-panel.active{ display:block; }

  /* Tables */
  .table{ width:100%; border-collapse:separate; border-spacing:0; }
  .table th, .table td{ padding: 10px 12px; border-bottom: 1px solid var(--border-weak); vertical-align: middle; }
  .table thead th{ font-size:12px; text-transform:uppercase; letter-spacing:.35px; color: var(--muted); background: rgba(255,255,255,.04); }
  .table tbody tr:hover{ background: rgba(255,255,255,.03); }

  .pill{ background: rgba(255,255,255,.06); border:1px solid var(--border-weak); border-radius: 999px; padding:4px 10px; font-size:12px; }

  /* Inputs & buttons */
  .inline-form{ display:flex; gap:8px; align-items:center; }
  .in{ width:84px; background: var(--bg-3); border:1px solid var(--border-weak); color: var(--fg); border-radius: 10px; padding:7px 10px; }
  .btn{ --gl: var(--blue-1); background: linear-gradient(135deg,var(--blue-1),var(--blue-2)); color:#061024; border:0; border-radius: 12px; padding:8px 12px; font-weight:800; cursor:pointer; box-shadow: 0 10px 20px rgba(0,114,255,.32), inset 0 0 0 1px rgba(255,255,255,.12); }
  .btn.ghost{ color: var(--fg); background: transparent; border:1px solid var(--border-weak); box-shadow:none; }
  .btn.danger{ --gl: #ff3355; background: linear-gradient(135deg,#ff6b6b,#ff3355); color: #1a0910; }

  .note{ color: var(--muted); font-size: 13px; }

  /* Responsive */
  @media (max-width: 1024px){
    .kpi-grid{ grid-template-columns: repeat(2, 1fr); }
    .panels{ grid-template-columns: 1fr; }
  }
  @media (max-width: 640px){ .inline-form{ flex-wrap:wrap; } .in{ width:70px; } }
</style>
@endpush

@section('content')
<div class="admin-wrap">
  <!-- ===== Header ===== -->
  <div class="ad-header">
    <div class="ad-title">
      <span>Admin Dashboard</span>
      <span class="badge">Neon Dark</span>
    </div>
    <div class="ad-actions" role="group" aria-label="Range">
      <div class="seg" id="rangeSeg">
        <button type="button" aria-pressed="true" data-range="today">Today</button>
        <button type="button" aria-pressed="false" data-range="7d">7 Days</button>
        <button type="button" aria-pressed="false" data-range="30d">30 Days</button>
      </div>
    </div>
  </div>

  <!-- ===== KPI Cards ===== -->
  <div class="kpi-grid">
    <div class="kpi" data-accent="blue">
      <div class="kpi-title">Searches Today</div>
      <div class="kpi-val">{{ $stats['searchesToday'] ?? 0 }}</div>
      <div class="kpi-sub">
        <span class="pill">Avg 7d: {{ $stats['avg7d'] ?? '—' }}</span>
        <span class="spark" aria-hidden="true">
          @php $bars = [3,8,6,10,12,7,9]; @endphp
          @foreach($bars as $b)<i style="height:{{ 4+$b*1.6 }}px"></i>@endforeach
        </span>
      </div>
    </div>

    <div class="kpi" data-accent="green">
      <div class="kpi-title">Total Users</div>
      <div class="kpi-val">{{ $stats['totalUsers'] ?? 0 }}</div>
      <div class="kpi-sub"><span class="pill">New today: {{ $stats['newUsers'] ?? 0 }}</span></div>
    </div>

    <div class="kpi" data-accent="amber">
      <div class="kpi-title">OpenAI Cost Today</div>
      <div class="kpi-val">${{ number_format($stats['costToday'] ?? 0, 4) }}</div>
      <div class="kpi-sub"><span class="pill">MTD: ${{ number_format($stats['costMonth'] ?? 0, 2) }}</span></div>
    </div>

    <div class="kpi" data-accent="purple">
      <div class="kpi-title">Active Users (5 min)</div>
      <div class="kpi-val">{{ $stats['active5m'] ?? 0 }}</div>
      <div class="kpi-sub"><span class="pill">Peak today: {{ $stats['peakToday'] ?? 0 }}</span></div>
    </div>
  </div>

  <!-- ===== Panels (Trends & System) ===== -->
  <div class="panels">
    <section class="panel">
      <div class="panel-h">
        <h3>Usage & Cost Overview</h3>
        <div class="mini-legend">
          <span><i class="dot blue"></i> Searches</span>
          <span><i class="dot green"></i> Cost</span>
        </div>
      </div>
      <div class="panel-b">
        <div class="note">Charts placeholder — hook your favorite chart lib or keep the sparklines above. Provide <code>$trendSearches</code> and <code>$trendCost</code> arrays to render.</div>
      </div>
    </section>

    <section class="panel">
      <div class="panel-h"><h3>System Status</h3></div>
      <div class="panel-b">
        <ul class="note" style="line-height:1.9">
          <li>PSI proxy: <strong>{{ ($system['psi'] ?? true) ? 'OK' : 'Down' }}</strong></li>
          <li>OpenAI key: <strong>{{ ($system['openai'] ?? true) ? 'Configured' : 'Missing' }}</strong></li>
          <li>Cache: <strong>{{ ($system['cache'] ?? true) ? 'Warm' : 'Cold' }}</strong></li>
        </ul>
      </div>
    </section>
  </div>

  <!-- ===== Tabs ===== -->
  <nav class="tabs" role="tablist" aria-label="Admin Sections">
    <button class="tab-btn" role="tab" aria-selected="true" aria-controls="tab-users" id="tab-users-btn">User Management</button>
    <button class="tab-btn" role="tab" aria-selected="false" aria-controls="tab-history" id="tab-history-btn">Search History</button>
    <button class="tab-btn" role="tab" aria-selected="false" aria-controls="tab-analytics" id="tab-analytics-btn">Analytics</button>
  </nav>

  <!-- Users -->
  <section id="tab-users" class="tab-panel active" role="tabpanel" aria-labelledby="tab-users-btn">
    <div class="panel" style="margin-top:10px">
      <div class="panel-h"><h3>Users & Limits</h3></div>
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
                <div class="note">{{ $u->email }}</div>
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
            <tr><td colspan="4" class="note">No users yet.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- History -->
  <section id="tab-history" class="tab-panel" role="tabpanel" aria-labelledby="tab-history-btn">
    <div class="panel" style="margin-top:10px">
      <div class="panel-h"><h3>Recent Searches</h3></div>
      <div class="panel-b" style="overflow:auto">
        <table class="table">
          <thead>
            <tr>
              <th>When</th>
              <th>User</th>
              <th>Query / URL</th>
              <th>Tool</th>
              <th>Tokens</th>
              <th>Cost</th>
            </tr>
          </thead>
          <tbody>
          @forelse(($history ?? []) as $h)
            <tr>
              <td>{{ $h->created_at->format('Y-m-d H:i') }}</td>
              <td>
                <div style="font-weight:700">{{ $h->user->name ?? '—' }}</div>
                <div class="note">{{ $h->user->email ?? '' }}</div>
              </td>
              <td style="max-width:460px">
                <div class="note" style="white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $h->query ?? $h->url }}</div>
              </td>
              <td><span class="pill">{{ $h->tool ?? 'Analyzer' }}</span></td>
              <td>{{ $h->tokens ?? '—' }}</td>
              <td>${{ number_format($h->cost ?? 0, 4) }}</td>
            </tr>
          @empty
            <tr><td colspan="6" class="note">Search history will appear here once users start analyzing.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- Analytics -->
  <section id="tab-analytics" class="tab-panel" role="tabpanel" aria-labelledby="tab-analytics-btn">
    <div class="panel" style="margin-top:10px">
      <div class="panel-h"><h3>Top Queries / Pages</h3></div>
      <div class="panel-b">
        <div class="note">Provide <code>$topItems</code> with <em>name</em> and <em>count</em>. Example list below.</div>
        <ul style="margin-top:10px; display:grid; grid-template-columns: repeat(2,1fr); gap:8px">
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
</div>
@endsection

@push('scripts')
<script>
  // Segmented range
  const seg = document.getElementById('rangeSeg');
  if (seg){
    seg.addEventListener('click', (e)=>{
      const btn = e.target.closest('button[data-range]');
      if(!btn) return;
      seg.querySelectorAll('button').forEach(b=> b.setAttribute('aria-pressed','false'));
      btn.setAttribute('aria-pressed','true');
      // You can submit a form or fetch data here using btn.dataset.range
    });
  }

  // Tabs
  const tabButtons = Array.from(document.querySelectorAll('.tab-btn'));
  const tabPanels = Array.from(document.querySelectorAll('.tab-panel'));
  tabButtons.forEach(btn=>{
    btn.addEventListener('click', ()=>{
      tabButtons.forEach(b=> b.setAttribute('aria-selected','false'));
      btn.setAttribute('aria-selected','true');
      tabPanels.forEach(p=> p.classList.remove('active'));
      document.getElementById(btn.getAttribute('aria-controls')).classList.add('active');
    });
  });
</script>
@endpush
