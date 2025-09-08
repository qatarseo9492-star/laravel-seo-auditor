@extends('layouts.app')
@section('title','Admin — Dashboard v3')

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

  /* KPI cards */
  .kpis{ display:grid; grid-template-columns: repeat(5, minmax(180px,1fr)); gap:14px; margin: 14px 0 18px; }
  .kpi{ position:relative; background: var(--bg-3); border:1px solid var(--bdr); border-radius: 18px; padding:16px; box-shadow: var(--shadow); overflow:hidden; }
  .kpi:before{ content:""; position:absolute; inset:-1px; border-radius: 18px; padding:1px; background: linear-gradient(135deg, var(--blue-1), var(--mag-2));
    -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor; mask-composite: exclude; }
  .kpi-title{ font-size:12px; color:var(--muted); text-transform:uppercase; letter-spacing:.35px; }
  .kpi-val{ font-size: clamp(22px, 3.4vw, 34px); font-weight: 900; line-height: 1.1; margin-top:6px; }

  /* Panels / grids */
  .grid{ display:grid; grid-template-columns: 1.1fr .9fr; gap:14px; }
  .panel{ background: var(--bg-2); border:1px solid var(--bdr); border-radius: 18px; box-shadow: var(--shadow); overflow:hidden; }
  .panel-h{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid var(--bdr); background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,0)); }
  .panel-h h3{ font-size:14px; letter-spacing:.3px; text-transform:uppercase; color:var(--muted); }
  .panel-b{ padding: 14px; }
  .gap12{display:grid; grid-template-columns: repeat(2,1fr); gap:12px;}

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
  .save-btn{min-width:70px}
  .save-btn[disabled]{opacity:.6; cursor:not-allowed}

  /* Activity feed */
  .activity{list-style:none; padding:0; margin:0;}
  .activity li{display:flex; gap:10px; align-items:center; padding:8px 0; border-bottom:1px solid var(--bdr);}
  .chip{font-size:11px; padding:4px 8px; border-radius:999px; border:1px solid var(--bdr); background:rgba(255,255,255,.05);}

  /* Presence */
  .status-dot{width:8px; height:8px; border-radius:6px; display:inline-block; margin-right:6px; background:#22d3ee;}

  /* Per-user dashboard modal */
  .ud-modal{position:fixed; inset:0; display:none; background:rgba(0,0,0,.45); backdrop-filter:blur(2px); z-index:60;}
  .ud-modal.open{display:block;}
  .ud-card{position:absolute; right:0; top:0; height:100vh; width:min(1080px, 100%); background:var(--bg-2); border-left:1px solid var(--bdr); box-shadow:-24px 0 56px rgba(0,0,0,.45); display:flex; flex-direction:column;}
  .ud-head{padding:16px; border-bottom:1px solid var(--bdr); display:flex; align-items:center; gap:10px;}
  .ud-hero{display:grid; grid-template-columns: 1.2fr .8fr; gap:12px; padding:12px;}
  .ud-col{background:var(--bg-2); border:1px solid var(--bdr); border-radius:14px; overflow:hidden;}
  .ud-col-h{display:flex; justify-content:space-between; padding:10px 12px; border-bottom:1px solid var(--bdr); background:rgba(255,255,255,.04);}
  .ud-col-b{padding:12px;}
  @media (max-width:1100px){ .kpis{ grid-template-columns: repeat(2,1fr);} .grid{ grid-template-columns: 1fr; } .ud-hero{grid-template-columns:1fr;} }

  /* === v3 Additions (non-breaking) === */
  .v3-grid{display:grid;grid-template-columns:2fr 1fr;gap:14px;margin:14px 0}
  @media (max-width:1024px){.v3-grid{grid-template-columns:1fr}}
  .v3-card{background:var(--bg-2, #0c1327);border:1px solid var(--bdr, rgba(255,255,255,.1));
           border-radius:16px;padding:16px;box-shadow:var(--shadow, 0 10px 30px rgba(0,0,0,.35))}
  .v3-sec-title{font-weight:700;margin:6px 0 10px}
  .v3-table{width:100%;border-collapse:collapse}
  .v3-table th,.v3-table td{border-bottom:1px solid var(--bdr, rgba(255,255,255,.1));padding:10px 8px;text-align:left;font-size:14px}
  .v3-pill{font-size:12px;padding:4px 8px;border-radius:999px;border:1px solid var(--bdr, rgba(255,255,255,.1));background:rgba(255,255,255,.04);display:inline-flex;gap:6px;align-items:center}
  .v3-dot{width:8px;height:8px;border-radius:999px;display:inline-block}
  .v3-good{color:var(--green-1, #00ff8a)} .v3-warn{color:var(--amber-1, #ffd700)} .v3-bad{color:var(--red-1, #ff3b30)}
  .v3-btn{background:linear-gradient(90deg,var(--blue-1,#00c6ff),var(--blue-2,#0072ff));border:none;color:#05101a;font-weight:700;padding:10px 12px;border-radius:10px;cursor:pointer;text-decoration:none;display:inline-block}
</style>
@endpush

@section('content')
<div class="wrap">
  <div class="hdr">
    <div class="title">Admin Dashboard <span class="badge">SEO Analytics</span></div>
    <div class="pill">Admin-only</div>
  </div>

  {{-- KPI CARDS --}}
  <section class="kpis">
    <div class="kpi"><div class="kpi-title">Searches Today</div><div class="kpi-val id="kpi-searches-today"">{{ $stats['searchesToday'] ?? ($searchesToday ?? 0) }}</div></div>
    <div class="kpi"><div class="kpi-title">Total Users</div><div class="kpi-val id="kpi-total-users"">{{ $stats['totalUsers'] ?? ($totalUsers ?? 0) }}</div></div>
    <div class="kpi"><div class="kpi-title">OpenAI Cost Today</div><div class="kpi-val id="kpi-openai-cost-today"">${{ number_format($stats['costToday'] ?? ($openAiCostToday ?? 0), 4) }}</div></div>
    <div class="kpi"><div class="kpi-title">DAU / MAU</div><div class="kpi-val"><span id="kpi-dau">0</span> / <span id="kpi-mau">0</span></div></div>
    <div class="kpi"><div class="kpi-title">Active (5m live)</div><div class="kpi-val" id="activeLive">{{ $stats['active5m'] ?? ($activeUsers ?? 0) }}</div></div>
  </section>
  {{-- v3 ADDITIONS: System Health + User Limits (read-only) --}}
  <section class="v3-grid">
    <div class="v3-card">
      <div class="v3-sec-title">System Health</div>
      <table class="v3-table id="system-health"">
        <tr><th>Service</th><th>Status</th><th>Latency</th></tr>
        @php $services = $services ?? []; @endphp
        @forelse($services as $s)
          <tr>
            <td>{{ $s['name'] }}</td>
            <td>
              @php $ok = !empty($s['ok']); @endphp
              <span class="v3-pill">
                <span class="v3-dot" style="background: {{ $ok ? 'var(--green-1,#00ff8a)' : 'var(--red-1,#ff3b30)' }}"></span>
                {{ $ok ? 'Operational' : 'Down' }}
              </span>
            </td>
            <td>{{ $s['latency_ms'] ?? '—' }} {{ isset($s['latency_ms']) ? 'ms' : '' }}</td>
          </tr>
        @empty
          <tr><td colspan="3" style="color:var(--muted)">Hook this to DB/Redis/Queue checks later.</td></tr>
        @endforelse
      </table>
    </div>
    <div class="v3-card">
      <div class="v3-sec-title">User Limits</div>
      @php $ls = $limitsSummary ?? []; @endphp
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
        <span class="v3-pill"><span class="v3-dot" style="background:var(--green-1,#00ff8a)"></span> Enabled: {{ number_format($ls['enabled'] ?? 0) }}</span>
        <span class="v3-pill"><span class="v3-dot" style="background:var(--red-1,#ff3b30)"></span> Disabled: {{ number_format($ls['disabled'] ?? 0) }}</span>
        <span class="v3-pill"><span class="v3-dot" style="background:var(--blue-1,#00c6ff)"></span> Default Limit: {{ number_format($ls['default'] ?? 200) }}</span>
      </div>
      <a href="{{ url('/admin/users') }}" class="v3-btn">Manage Users</a>
      <div class="hint" style="margin-top:8px">Read-only summary; toggles live on the Users page.</div>
    </div>
  </section>


  {{-- CHARTS: OpenAI & PSI --}}
  <section class="grid">
    <div class="panel">
      <div class="panel-h"><h3>OpenAI Usage — Cost (30 days)</h3><span class="pill">USD</span></div>
      <div class="panel-b"><canvas id="openaiChart" height="120"></canvas></div>
    </div>
    <div class="panel">
      <div class="panel-h"><h3>PSI Usage — Requests (30 days)</h3><span class="pill">Count</span></div>
      <div class="panel-b"><canvas id="psiChart" height="120"></canvas></div>
    </div>
  </section>

  {{-- ACTIVITY + CORE ANALYTICS --}}
  <section class="grid" style="margin-top:14px">
    <div class="panel">
      <div class="panel-h"><h3>Real-time Activity</h3><button class="btn slim ghost" onclick="loadActivity()">Refresh</button></div>
      <div class="panel-b" style="max-height:260px; overflow:auto">
        <ul class="activity" id="activityList"><li style="color:var(--muted)">Loading…</li></ul>
      </div>
    </div>

    <div class="panel">
      <div class="panel-h"><h3>Core Analytics</h3></div>
      <div class="panel-b gap12">
        <div class="kpi"><div class="kpi-title">DAU</div><div class="kpi-val">{{ $stats['dau'] ?? 0 }}</div></div>
        <div class="kpi"><div class="kpi-title">MAU</div><div class="kpi-val">{{ $stats['mau'] ?? 0 }}</div></div>
        <div class="kpi"><div class="kpi-title">New this month</div><div class="kpi-val">{{ $stats['new'] ?? 0 }}</div></div>
        <div class="kpi"><div class="kpi-title">Returning</div><div class="kpi-val">{{ $stats['returning'] ?? 0 }}</div></div>
      </div>
    </div>
  </section>

  {{-- SYSTEM + PRESENCE --}}
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
                <td><span class="status-dot"></span>{{ $p['email'] ?? '—' }}</td>
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

  {{-- USERS --}}
  <section style="margin-top:16px">
    <div class="panel">
      <div class="panel-h"><h3>Users</h3></div>
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
                <button class="btn slim ghost"
                        data-user-id="{{ $u->id }}"
                        data-user-email="{{ $u->email }}"
                        data-user-name="{{ $u->name }}"
                        onclick="openUserDash(this)">View Dashboard</button>
              </td>
              <td>
                <span class="pill">{{ ($u->banned ?? false) ? 'Banned' : 'Active' }}</span>
                <span class="pill">{{ $u->today_count ?? 0 }} / {{ $u->month_count ?? 0 }}</span>
              </td>
              <td>
                <form class="inline-form user-limit-form"
                      action="{{ route('admin.users.limit', $u->id) }}"
                      method="POST" onsubmit="return saveLimitAjax(event, this)">
                  @csrf @method('PATCH')
                  <input class="in" type="number" name="daily"   value="{{ optional($u->limit)->daily ?? 50 }}" min="0">
                  <input class="in" type="number" name="monthly" value="{{ optional($u->limit)->monthly ?? 300 }}" min="0">
                  <button class="btn save-btn" type="submit">Save</button>
                </form>
              </td>
              <td>
                <form class="inline-form"
                      action="{{ route('admin.users.ban', $u->id) }}"
                      method="POST"
                      onsubmit="return confirm('Ban/unban this user?')">
                  @csrf @method('PATCH')
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

  {{-- GLOBAL HISTORY (ADMIN ONLY) --}}
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
            function _domain($u) { if(!$u) return ''; $p=parse_url($u); return $p['host']??''; }
          @endphp
          @forelse(($history ?? []) as $h)
            @php
              $disp = $h->display ?? ($h->query ?? ($h->keyword ?? ($h->search_term ?? ($h->url ?? ''))));
              $url  = $h->url ?? (filter_var($disp, FILTER_VALIDATE_URL) ? $disp : null);
              $dom  = _domain($url);
            @endphp
            <tr>
              <td>{{ optional($h->created_at)->format('Y-m-d H:i') }}</td>
              <td>{{ optional($h->user)->email ?? '—' }}</td>
              <td>{{ $dom }}</td>
              <td style="max-width:460px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis">{{ $disp }}</td>
              <td>{{ $h->tool ?? 'Analyzer' }}</td>
              <td>{{ $h->tokens ?? '—' }}</td>
              <td>${{ number_format($h->cost ?? 0, 4) }}</td>
            </tr>
          @empty
            <tr><td colspan="7" style="color:var(--muted)">No history yet.</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </section>
</div>

{{-- ===== PER-USER DASHBOARD MODAL ===== --}}
<div class="ud-modal" id="ud" aria-hidden="true">
  <div class="ud-card">
    <div class="ud-head">
      <div style="font-weight:900" id="udTitle">User Dashboard</div>
      <div class="chip" id="udPlan">Plan: Free</div>
      <div style="margin-left:auto" class="chip" id="udStatus">Active</div>
      <button class="btn slim ghost" onclick="closeUserDash()">Close</button>
    </div>

    <div class="ud-hero">
      <div class="ud-col">
        <div class="ud-col-h"><strong>Overview</strong></div>
        <div class="ud-col-b">
          <div class="gap12">
            <div class="kpi"><div class="kpi-title">Today</div><div class="kpi-val" id="udToday">0</div></div>
            <div class="kpi"><div class="kpi-title">This Month</div><div class="kpi-val" id="udMonth">0</div></div>
            <div class="kpi"><div class="kpi-title">Total</div><div class="kpi-val" id="udTotal">0</div></div>
            <div class="kpi"><div class="kpi-title">Sessions</div><div class="kpi-val" id="udSessions">0</div></div>
          </div>
          <div class="gap12" style="margin-top:12px">
            <div class="ud-col">
              <div class="ud-col-h"><strong>Daily Usage (14d)</strong></div>
              <div class="ud-col-b"><canvas id="udDaily" height="120"></canvas></div>
            </div>
            <div class="ud-col">
              <div class="ud-col-h"><strong>Today’s Usage</strong></div>
              <div class="ud-col-b">
                <canvas id="udWheel" height="160"></canvas>
                <div style="color:var(--muted); margin-top:6px">Login: <span id="udLogin">—</span> • IP: <span id="udIP">—</span> • Country: <span id="udCountry">—</span></div>
              </div>
            </div>
          </div>
          <div class="gap12" style="margin-top:12px">
            <div class="kpi"><div class="kpi-title">Avg Session (sec)</div><div class="kpi-val" id="udAvgSess">0</div></div>
            <div class="kpi"><div class="kpi-title">Bounce %</div><div class="kpi-val" id="udBounce">0</div></div>
            <div class="kpi"><div class="kpi-title">Conv. %</div><div class="kpi-val" id="udConv">0</div></div>
            <div class="kpi"><div class="kpi-title">Limits</div><div class="kpi-val"><span id="udLimitDay">0</span>/<span id="udLimitMon">0</span></div></div>
          </div>
        </div>
      </div>

      <div class="ud-col">
        <div class="ud-col-h"><strong>User Tech & Channels</strong></div>
        <div class="ud-col-b">
          <div class="gap12">
            <div><canvas id="udDevice" height="120"></canvas></div>
            <div><canvas id="udBrowser" height="120"></canvas></div>
          </div>
          <div class="gap12" style="margin-top:12px">
            <div><canvas id="udOS" height="120"></canvas></div>
            <div><canvas id="udChannels" height="120"></canvas></div>
          </div>
        </div>
      </div>
    </div>

    <div class="ud-col" style="margin:12px">
      <div class="ud-col-h"><strong>Daily History (latest 100)</strong></div>
      <div class="ud-col-b" style="overflow:auto">
        <table class="table" id="udHist">
          <thead><tr><th>When</th><th>Country</th><th>URL / Query</th><th>Tool</th><th>Tokens</th><th>Cost</th></tr></thead>
          <tbody><tr><td colspan="6" style="color:var(--muted)">Loading…</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.7/js/dataTables.min.js"></script>
<script>
  // ---------- Data from controller (safe fallbacks) ----------
  const openAiDaily = @json($openAiDaily ?? ['labels'=>[], 'costs'=>[]]);
  const psiDaily    = @json($psiDaily ?? ['labels'=>[], 'counts'=>[]]);

  // ---------- Charts: OpenAI & PSI ----------
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
      new Chart(pc, {
        type:'line',
        data:{ labels: psiDaily.labels, datasets:[ { label:'Requests', data: psiDaily.counts, tension:.35, fill:true } ] },
        options:{ scales:{ y:{ beginAtZero:true }}, plugins:{ legend:{ labels:{ color:'#e6e9f0' } } } }
      });
    }
  })();

  // ---------- Activity Feed ----------
  async function loadActivity(){
    const res = await fetch(`{{ route('admin.dashboard') }}?partial=activity`, {headers:{'X-Requested-With':'fetch'}});
    if(!res.ok) return;
    const data = await res.json();
    const ul = document.getElementById('activityList'); ul.innerHTML = '';
    (data.items||[]).forEach(it=>{
      const li = document.createElement('li');
      li.innerHTML = `<span class="chip">${it.when}</span>
                      <span class="chip">${it.tool}</span>
                      <strong>${it.email}</strong>
                      <span style="color:var(--muted);max-width:60%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${it.display||''}</span>`;
      ul.appendChild(li);
    });
    if(!(data.items||[]).length) ul.innerHTML = '<li style="color:var(--muted)">No recent activity.</li>';
  }
  loadActivity(); setInterval(loadActivity, 30000);

  // ---------- Presence ----------
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
        tr.innerHTML = `<td><span class="status-dot"></span>${p.email||'—'}</td><td>${p.last_seen_at||'—'}</td><td>${p.last_login_at||'—'}</td><td>${p.last_logout_at||'—'}</td><td><span class="pill">${p.status||'—'}</span></td>`;
        tbody.appendChild(tr);
      });
      if(!(data.online||[]).length){
        const tr = document.createElement('tr'); tr.innerHTML = `<td colspan="5" style="color:var(--muted)">No recent activity.</td>`; tbody.appendChild(tr);
      }
    }catch(e){}
  }
  document.getElementById('refreshPresence')?.addEventListener('click', refreshPresence);
  setInterval(refreshPresence, 30000);

  // ---------- DataTable (Global History) ----------
  let histDT;
  (function(){
    if (!window.DataTable) return;
    histDT = new DataTable('#historyTable', { pageLength: 12, lengthChange: false, order:[[0,'desc']] });
    const input = document.getElementById('histFilter');
    if (input) input.addEventListener('input', ()=> histDT.search(input.value).draw());
  })();

  // CSV export
  window.exportCSV = () => {
    const rows = [...document.querySelectorAll('#historyTable tbody tr')].map(tr=> [...tr.children].map(td=> '"'+td.innerText.replace(/"/g,'\\"')+'"').join(','));
    const csv = ['When,User,Domain,URL/Query,Tool,Tokens,Cost', ...rows].join('\n');
    const blob = new Blob([csv], {type:'text/csv'});
    const a = document.createElement('a'); a.href = URL.createObjectURL(blob); a.download = 'global-history.csv'; a.click(); URL.revokeObjectURL(a.href);
  };

  // ---------- Professional limits editor (AJAX) ----------
  async function saveLimitAjax(e, form){
    e.preventDefault();
    const btn = form.querySelector('.save-btn'); btn.disabled = true; btn.textContent='Saving…';
    const res = await fetch(form.action, {method:'POST', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content}, body:new FormData(form)});
    btn.disabled = false; btn.textContent = res.ok ? 'Saved' : 'Save';
    if(!res.ok) alert('Save failed');
    setTimeout(()=>{ btn.textContent='Save'; }, 1200);
    return false;
  }
  window.saveLimitAjax = saveLimitAjax;

  // ---------- Per-user Dashboard ----------
  let udCharts = {};
  function closeUserDash(){ document.getElementById('ud').classList.remove('open'); }
  async function openUserDash(btn){
    const id = btn.getAttribute('data-user-id');
    const name = btn.getAttribute('data-user-name');
    const email= btn.getAttribute('data-user-email');
    document.getElementById('udTitle').textContent = `${name} — ${email}`;
    document.getElementById('ud').classList.add('open');

    const res = await fetch(`{{ route('admin.dashboard') }}?partial=userDashboard&user_id=${encodeURIComponent(id)}`, {headers:{'X-Requested-With':'fetch'}});
    const d = res.ok ? await res.json() : {};

    // Badges / status
    document.getElementById('udPlan').textContent   = `Plan: ${d.plan?.name || 'Free'}`;
    document.getElementById('udStatus').textContent = d.account?.status || '—';

    // KPIs
    document.getElementById('udToday').textContent   = d.stats?.today ?? 0;
    document.getElementById('udMonth').textContent   = d.stats?.month ?? 0;
    document.getElementById('udTotal').textContent   = d.stats?.total ?? 0;
    document.getElementById('udSessions').textContent= d.stats?.sessions ?? 0;
    document.getElementById('udAvgSess').textContent = d.stats?.avgSessionSec ?? 0;
    document.getElementById('udBounce').textContent  = d.stats?.bounceRate ?? 0;
    document.getElementById('udConv').textContent    = d.stats?.conversionRate ?? 0;
    document.getElementById('udLimitDay').textContent= d.limits?.daily ?? 0;
    document.getElementById('udLimitMon').textContent= d.limits?.monthly ?? 0;

    // Presence
    document.getElementById('udLogin').textContent   = d.account?.last_login_at || '—';
    document.getElementById('udIP').textContent      = d.account?.ip || '—';
    document.getElementById('udCountry').textContent = d.account?.country || '—';

    // Destroy old charts
    for (const k in udCharts){ try{ udCharts[k].destroy(); }catch{} } udCharts = {};

    const C = Chart;
    // Daily 14d
    const labels = (d.daily||[]).map(x=>x.d), values=(d.daily||[]).map(x=>x.c);
    if (document.getElementById('udDaily') && C) {
      udCharts.daily = new C(document.getElementById('udDaily'), {
        type:'bar', data:{labels,datasets:[{label:'Searches', data:values}]},
        options:{scales:{y:{beginAtZero:true}}}
      });
    }
    // Wheel (usage today vs limit)
    const pct = d.stats?.usagePct ?? 0;
    if (document.getElementById('udWheel') && C) {
      udCharts.wheel = new C(document.getElementById('udWheel'), {
        type:'doughnut', data:{labels:['Used','Free'], datasets:[{data:[pct, Math.max(0,100-pct)]}]},
        options:{cutout:'70%', plugins:{legend:{display:false}, tooltip:{enabled:false}}}
      });
      // center label
      setTimeout(()=>{
        const cnv = document.getElementById('udWheel');
        const ctx = cnv.getContext('2d');
        ctx.save(); ctx.fillStyle='#e6e9f0'; ctx.font='700 20px system-ui'; ctx.textAlign='center';
        ctx.fillText(`${pct}%`, cnv.width/2, cnv.height/2+6); ctx.restore();
      },80);
    }
    // Device / Browser / OS / Channels pies
    const pie = (el, obj)=>{ const labs=Object.keys(obj||{}), dat=Object.values(obj||{}); if(!labs.length) return;
      udCharts[el] = new C(document.getElementById(el), {type:'doughnut', data:{labels:labs, datasets:[{data:dat}]}, options:{plugins:{legend:{position:'bottom'}}}});
    };
    pie('udDevice', d.device); pie('udBrowser', d.browser); pie('udOS', d.os); pie('udChannels', d.channels);

    // History table
    const tb = document.querySelector('#udHist tbody'); tb.innerHTML='';
    (d.history||[]).forEach(h=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${h.when||''}</td><td>${h.country||''}</td>
                      <td style="max-width:520px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${h.display||''}</td>
                      <td>${h.tool||''}</td><td>${h.tokens??'—'}</td><td>${h.cost??'0.0000'}</td>`;
      tb.appendChild(tr);
    });
    if(!(d.history||[]).length){ tb.innerHTML = `<tr><td colspan="6" style="color:var(--muted)">No history.</td></tr>`; }
  }
  window.openUserDash = openUserDash; window.closeUserDash = closeUserDash;

</script>

@push('scripts')
<script>
(function () {
  const LIVE_URL = (window.DASH_LIVE_URL || '/admin/dashboard/live');
  const USER_URL = (id) => `/admin/users/${id}/live`;
  const LIMIT_URL = (id) => `/admin/users/${id}/limits`;
  const CSRF = '{{ csrf_token() }}';

  const $ = (sel, n=document) => n.querySelector(sel);
  const fmt = (n) => new Intl.NumberFormat().format(Number(n||0));
  const money = (n) => '$' + (Number(n||0).toFixed(4));
  const txt = (id, v) => { const el=document.getElementById(id); if(el) el.textContent = v; };

  function renderKPIs(k){
    txt('kpi-searches-today', fmt(k.searchesToday));
    txt('kpi-total-users', fmt(k.totalUsers));
    txt('kpi-openai-cost-today', money(k.cost24h));
    txt('kpi-dau', fmt(k.dau)); txt('kpi-mau', fmt(k.mau));
    const al = document.getElementById('activeLive'); if (al) al.textContent = fmt(k.active5m);
  }

  function renderServices(list){
    const tbl = document.querySelector('#system-health tbody') || document.querySelector('#system-health');
    if (!tbl) return;
    const hasBody = !!document.querySelector('#system-health tbody');
    const rows = (list||[]).map(s=>{
      const dot = s.ok ? '#00ff8a' : '#ff3b30';
      return `<tr>
        <td>${s.name||''}</td>
        <td><span class="v3-pill"><span class="v3-dot" style="background:${dot}"></span>${s.ok?'Operational':'Down'}</span></td>
        <td>${s.latency_ms ?? '—'} ${s.latency_ms?'ms':''}</td>
      </tr>`;
    }).join('') || `<tr><td colspan="3" style="color:var(--muted)">No data.</td></tr>`;
    if (hasBody) { tbl.innerHTML = rows; } else { document.getElementById('system-health').innerHTML = rows; }
  }

  function renderHistory(rows){
    const tb = document.querySelector('#global-history tbody');
    if (!tb) return;
    tb.innerHTML = '';
    if (!rows || !rows.length){
      tb.innerHTML = '<tr><td colspan="6" style="color:#9aa6b2">No recent history.</td></tr>';
      return;
    }
    rows.forEach(r=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${r.when||''}</td>
        <td>${r.user||''}</td>
        <td style="max-width:520px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${r.display||''}</td>
        <td>${r.tool||''}</td>
        <td>${r.tokens ?? '—'}</td>
        <td>${r.cost ?? '0.0000'}</td>`;
      tb.appendChild(tr);
    });
  }

  async function tick(){
    try{
      const res = await fetch(LIVE_URL + '?fresh=1', { headers:{'X-Requested-With':'XMLHttpRequest'} });
      if (!res.ok) return;
      const data = await res.json();
      if (data.kpis) renderKPIs(data.kpis);
      if (data.services) renderServices(data.services);
      if (data.history) renderHistory(data.history);
      if (data.traffic && window.AdminTrafficChart){
        const labels = data.traffic.map(x=>x.day);
        const counts = data.traffic.map(x=>x.count);
        window.AdminTrafficChart.data.labels = labels;
        window.AdminTrafficChart.data.datasets[0].data = counts;
        window.AdminTrafficChart.update('none');
      }
    }catch(e){ /* silent */ }
  }

  // User drawer (inline manage)
  window.openUserDrawer = async function(userId){
    const panel = document.getElementById('userDrawer');
    const body  = document.getElementById('udBody');
    if (!panel || !body) return;
    panel.classList.remove('hidden');
    body.innerHTML = '<div style="opacity:.7">Loading...</div>';
    try{
      const res = await fetch(USER_URL(userId), { headers:{'X-Requested-With':'XMLHttpRequest'} });
      const d = await res.json();
      const u = d.user || {};
      const L = d.limit || { daily_limit:200, is_enabled:true, reason:'' };
      document.getElementById('udTitle').textContent = (u.name||u.email||('User #'+userId));
      body.innerHTML = `
        <div class="v3-card" style="margin-bottom:10px">
          <div style="font-weight:700;margin-bottom:6px">Account</div>
          <div>Email: ${u.email||''}</div>
          <div>Last seen: ${u.last_seen_at||'—'} (${u.last_ip||'—'} ${u.last_country?'- '+u.last_country:''})</div>
          <div>Last login: ${u.last_login_at||'—'}</div>
          <div>Last logout: ${u.last_logout_at||'—'}</div>
        </div>
        <div class="v3-card" style="margin-bottom:10px">
          <div style="font-weight:700;margin-bottom:6px">Limits</div>
          <label style="display:flex;gap:6px;align-items:center;margin-bottom:6px">
            <input type="checkbox" id="udEnabled" ${L.is_enabled ? 'checked' : ''} /> Limits enabled
          </label>
          <label>Daily limit <input id="udDaily" type="number" min="0" value="${L.daily_limit}" style="max-width:140px;margin-left:8px"/></label>
          <input id="udReason" type="text" placeholder="Reason (optional)" value="${L.reason||''}" class="form-control" style="margin-top:8px"/>
          <div style="margin-top:8px;display:flex;gap:8px">
            <button class="v3-btn" onclick="saveUserLimit(${userId})">Save</button>
            <button class="v3-btn" style="background:linear-gradient(90deg,#ff6a6a,#ff3b30)" onclick="closeUserDrawer()">Close</button>
          </div>
        </div>
        <div class="v3-card">
          <div style="font-weight:700;margin-bottom:6px">Recent Activity</div>
          <div id="udHist" style="max-height:220px;overflow:auto"></div>
        </div>`;
      // render latest rows
      const hist = Array.isArray(d.latest) ? d.latest : [];
      const histDiv = document.getElementById('udHist');
      histDiv.innerHTML = hist.map(h=>`<div style="padding:4px 0;border-bottom:1px solid rgba(255,255,255,.08)">
        <div>${h.created_at||''} — ${h.type||'—'}</div>
        <div style="opacity:.8">${h.query||h.url||h.domain||''}</div>
      </div>`).join('') || '<div style="opacity:.7">No recent activity.</div>';
    }catch(e){
      body.innerHTML = '<div style="color:#ff3b30">Failed to load user.</div>';
    }
  };

  window.closeUserDrawer = function(){
    const panel = document.getElementById('userDrawer');
    if (panel) panel.classList.add('hidden');
  };

  window.saveUserLimit = async function(userId){
    const daily = Number(document.getElementById('udDaily').value || 200);
    const enabled = document.getElementById('udEnabled').checked ? 1 : 0;
    const reason = document.getElementById('udReason').value || '';
    try{
      const res = await fetch(LIMIT_URL(userId), {
        method:'PATCH',
        headers:{
          'Content-Type':'application/json',
          'X-CSRF-TOKEN': CSRF,
          'X-Requested-With':'XMLHttpRequest'
        },
        body: JSON.stringify({ daily_limit: daily, is_enabled: enabled, reason })
      });
      if (!res.ok) throw new Error('HTTP '+res.status);
      alert('Saved.');
    }catch(e){
      alert('Failed to save.');
    }
  };

  // boot
  window.addEventListener('load', function(){
    tick();
    setInterval(tick, 10000);
  });
})();
</script>

{{-- Inline drawer (hidden until used) --}}
<style>
  .drawer-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.4);display:flex;justify-content:flex-end;z-index:9999}
  .drawer{width:min(420px,100%);height:100%;background:var(--bg-2,#0c1327);border-left:1px solid rgba(255,255,255,.12);padding:16px;overflow:auto}
  .hidden{display:none}
</style>
<div id="userDrawer" class="drawer-backdrop hidden" onclick="if(event.target===this) closeUserDrawer()">
  <div class="drawer v3-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
      <div id="udTitle" style="font-weight:800">User</div>
      <button class="v3-btn" onclick="closeUserDrawer()">Close</button>
    </div>
    <div id="udBody">Loading…</div>
  </div>
</div>
@endpush

@endpush
