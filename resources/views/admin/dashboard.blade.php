@extends('layouts.app')
@section('title','Admin — Dashboard')

@push('head')
  {{-- New look / colors (your uploaded CSS) --}}
  <link rel="stylesheet" href="{{ asset('css/admin-v4.css') }}">

  {{-- Chart.js for the traffic line chart --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>

  <style>
    /* tiny helpers used by the view + your JS */
    .v3-pill{display:inline-flex;gap:8px;align-items:center;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);padding:4px 10px;border-radius:999px;font-size:12px}
    .v3-dot{width:8px;height:8px;border-radius:999px;display:inline-block}
    .chart-wrap{position:relative;height:280px}
    .header-gauge svg{width:44px;height:44px}
    .chip.ok .dot,.dot.ok{background:var(--green)}
    .chip.warn{background:rgba(255,191,0,.12);border-color:rgba(255,191,0,.25)}
    .pill{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);font-size:12px}
    .bubble{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);padding:4px 8px;border-radius:8px;font-size:12px}
    .input{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:8px 10px;color:inherit;min-width:220px}
    .muted{opacity:.7}
  </style>
@endpush

@section('content')
@php
  // safe defaults if controller sends nothing
  $k = $kpis ?? [
    'searchesToday' => 0,
    'totalUsers' => 0,
    'cost24h' => 0.0,
    'dau' => 0,
    'mau' => 0,
    'active5m' => 0,
    'dailyLimit' => 100,
  ];
  $liveUrl = \Illuminate\Support\Facades\Route::has('admin.dashboard.live')
           ? route('admin.dashboard.live')
           : url('admin/dashboard/live');
@endphp

<div id="dashboard-root" class="container" data-live-url="{{ $liveUrl }}">
  {{-- Top bar --}}
  <div class="topbar">
    <div class="brand">
      <div class="logo">S</div>
      <div class="title">
        <h1>Semantic SEO — Admin</h1>
        <span class="badge">v4 • Neon Aurora</span>
      </div>
    </div>

    <div class="toolbar">
      <a class="btn primary"
         href="{{ Route::has('admin.announcements.create') ? route('admin.announcements.create') : url('admin/announcements/create') }}">
        Create Announcement
      </a>
      <a class="btn ghost"
         href="{{ Route::has('admin.users') ? route('admin.users') : url('admin/users') }}">
        Find a User
      </a>
      <button type="button" class="btn outline" onclick="window.print()">Generate Report</button>
    </div>
  </div>

  {{-- KPI row --}}
  <div class="grid kpis">
    <div class="card kpi">
      <div class="card-head">
        <div>
          <div class="kpi-label">Searches Today</div>
          <div id="kpi-searches-today" class="kpi-value">{{ number_format($k['searchesToday']) }}</div>
          <div class="kpi-sub">Live last 24h</div>
        </div>
        <div class="header-gauge"
             title="Daily usage gauge"
             data-value="{{ $k['searchesToday'] }}"
             data-max="{{ $k['dailyLimit'] ?: max(100, $k['searchesToday']) }}">
          <svg viewBox="0 0 36 36" aria-hidden="true" focusable="false">
            <path stroke="var(--primary)" stroke-width="3" fill="none" stroke-linecap="round"
                  d="M18 2a16 16 0 1 1 0 32a16 16 0 1 1 0-32"></path>
          </svg>
        </div>
      </div>
    </div>

    <div class="card kpi">
      <div class="card-head">
        <div>
          <div class="kpi-label">Total Users</div>
          <div id="kpi-total-users" class="kpi-value">{{ number_format($k['totalUsers']) }}</div>
          <div class="kpi-sub">All time</div>
        </div>
        <span class="chip ok"><span class="dot ok"></span>Active <span id="activeLive">{{ number_format($k['active5m']) }}</span> / 5m</span>
      </div>
    </div>

    <div class="card kpi">
      <div class="card-head">
        <div>
          <div class="kpi-label">OpenAI Cost (24h)</div>
          <div id="kpi-openai-cost-today" class="kpi-value">${{ number_format((float)$k['cost24h'], 4) }}</div>
          <div class="kpi-sub">Tokens billed</div>
        </div>
        <span class="chip warn">Budget</span>
      </div>
    </div>

    <div class="card kpi">
      <div class="card-head">
        <div>
          <div class="kpi-label">DAU / MAU</div>
          <div class="kpi-value">
            <span id="kpi-dau">{{ number_format($k['dau']) }}</span> /
            <span id="kpi-mau">{{ number_format($k['mau']) }}</span>
          </div>
          <div class="kpi-sub">Daily vs Monthly Actives</div>
        </div>
        <span class="chip">Engagement</span>
      </div>
    </div>
  </div>

  {{-- Main grid: Traffic + Health + History --}}
  <div class="grid main" style="margin-top:16px">
    {{-- Traffic chart --}}
    <div class="card span-2">
      <div class="card-head">
        <div>
          <div class="card-title">User Growth & Analyses</div>
          <div class="card-sub">Daily analyses over the last 14 days</div>
        </div>
        <div class="bubbles">
          <span class="bubble">MRR: {{ $mrr ?? '—' }}</span>
          <span class="bubble">Plans:
            <a href="{{ Route::has('admin.plans') ? route('admin.plans') : url('admin/plans') }}">manage</a>
          </span>
        </div>
      </div>
      <div class="chart-wrap">
        <canvas id="trafficChart" aria-label="Daily analyses chart" role="img"></canvas>
      </div>
    </div>

    {{-- System health --}}
    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">System Health</div>
          <div class="card-sub">Live service status & latency</div>
        </div>
        <span class="pill">Auto-refresh 10s</span>
      </div>
      <table id="system-health" class="table" aria-describedby="System health status">
        <thead>
          <tr>
            <th>Service</th>
            <th>Status</th>
            <th>Latency</th>
          </tr>
        </thead>
        <tbody>
          <tr><td class="muted" colspan="3">Loading…</td></tr>
        </tbody>
      </table>
      <div class="list slim" style="margin-top:8px">
        <div class="row"><div class="text">DB</div><div class="text"><span class="dot ok"></span>Connected</div></div>
        <div class="row"><div class="text">Queue</div><div class="text"><span class="dot"></span>Idle</div></div>
      </div>
    </div>

    {{-- Global history --}}
    <div class="card span-2">
      <div class="card-head">
        <div>
          <div class="card-title">Global History (Latest)</div>
          <div class="card-sub">Live feed of analyses and actions</div>
        </div>
        <div class="bubbles">
          <input id="historyFilter" class="input" placeholder="Filter by user or text…" aria-label="Filter history">
          <button id="exportCsvBtn" class="btn outline">Export CSV</button>
        </div>
      </div>
      <table id="global-history" class="table">
        <thead>
          <tr>
            <th>When</th>
            <th>User</th>
            <th>Activity</th>
            <th>Tool</th>
            <th>Tokens</th>
            <th>Cost</th>
          </tr>
        </thead>
        <tbody>
          <tr><td class="muted" colspan="6">Loading…</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- Quick tabs linking to existing pages (defensive routes) --}}
  <div class="grid" style="margin-top:16px">
    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">User Management</div>
          <div class="card-sub">Searchable list, limits, impersonate</div>
        </div>
        <a class="btn ghost"
           href="{{ Route::has('admin.users') ? route('admin.users') : url('admin/users') }}">Open</a>
      </div>
      <p class="card-sub">Go to Users to grant credits, suspend, reset password, or view usage logs.</p>
    </div>

    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">Subscriptions & Billing</div>
          <div class="card-sub">Plans, MRR/ARR, coupons</div>
        </div>
        <a class="btn ghost"
           href="{{ Route::has('admin.billing') ? route('admin.billing') : url('admin/billing') }}">Open</a>
      </div>
      <p class="card-sub">Create plans, track revenue, view transactions, manage discounts.</p>
    </div>

    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">Announcements</div>
          <div class="card-sub">Global banners & popups</div>
        </div>
        <a class="btn ghost"
           href="{{ Route::has('admin.announcements.index') ? route('admin.announcements.index') : url('admin/announcements') }}">Open</a>
      </div>
      <p class="card-sub">Publish maintenance notices or feature launches.</p>
    </div>
  </div>
</div>

{{-- Your uploaded JS handles live refresh, table fill, CSV export, gauge animation, etc. --}}
<script src="{{ asset('js/admin-dashboard.js') }}" defer></script>

{{-- Boot Chart.js (safe if Chart not yet loaded) and expose live URL for JS --}}
<script>
  document.addEventListener('DOMContentLoaded', function(){
    // expose live JSON endpoint for any script that needs it
    var root = document.getElementById('dashboard-root');
    window.__ADMIN_LIVE_URL = root ? root.dataset.liveUrl : '{{ $liveUrl }}';

    // prepare the line chart; your admin-dashboard.js can push data via window.AdminTrafficChart
    var canvas = document.getElementById('trafficChart');
    if (canvas && window.Chart) {
      var ds = { label: 'Daily Analyses', data: [], fill: false, tension: 0.3 };
      window.AdminTrafficChart = new Chart(canvas, {
        type: 'line',
        data: { labels: [], datasets: [ds] },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true } }
        }
      });
    }
  });
</script>
@endsection
