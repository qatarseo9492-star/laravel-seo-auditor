@extends('layouts.app')

@section('title', 'Admin Dashboard — Aurora v4')

@push('styles')
<link rel="stylesheet" href="/css/admin-v4.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')
<div class="aurora container">
  <div class="topbar">
    <div class="brand">
      <span class="logo">∎</span>
      <div class="title">
        <h1>Admin Dashboard</h1>
        <span class="badge">Aurora v4</span>
      </div>
    </div>
    <div class="toolbar">
      <button class="btn ghost" id="refreshNow">Refresh</button>
      @php
        $usersUrl = \Illuminate\Support\Facades\Route::has('admin.users.index')
          ? route('admin.users.index')
          : url('/admin/users');
      @endphp
      <a class="btn primary" href="{{ $usersUrl }}">Manage Users</a>
    </div>
  </div>

  {{-- KPI row --}}
  <div class="grid kpis">
    <div class="card kpi">
      <div class="kpi-label">Searches Today</div>
      <div class="kpi-value" id="kpi-searches-today">0</div>
      <div class="kpi-sub">Past 24h active: <span id="kpi-active-24h">0</span></div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">Total Users</div>
      <div class="kpi-value" id="kpi-total-users">0</div>
      <div class="kpi-sub">Active (5m): <span id="activeLive">0</span></div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">OpenAI Cost (24h)</div>
      <div class="kpi-value" id="kpi-openai-cost-today">$0.0000</div>
      <div class="kpi-sub"><span id="kpi-openai-tokens">0</span> tokens</div>
    </div>
    <div class="card kpi">
      <div class="kpi-label">DAU / MAU</div>
      <div class="kpi-value"><span id="kpi-dau">0</span> / <span id="kpi-mau">0</span></div>
      <div class="kpi-sub">Engagement</div>
    </div>
  </div>

  <div class="grid main">
    <div class="card span-2">
      <div class="card-head">
        <div>
          <div class="card-title">System Health</div>
          <div class="card-sub">Live snapshots of core services</div>
        </div>
        <div class="chip ok" id="healthChip">Checking…</div>
      </div>
      <table class="table" id="system-health">
        <thead><tr><th>Service</th><th>Status</th><th>Latency</th></tr></thead>
        <tbody><tr><td colspan="3" class="muted">No data yet.</td></tr></tbody>
      </table>
    </div>

    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">User Limits</div>
          <div class="card-sub">Read-only summary. Manage within drawer or users list.</div>
        </div>
      </div>
      @php $ls = $limitsSummary ?? ['enabled'=>0,'disabled'=>0,'default'=>200]; @endphp
      <div class="bubbles">
        <div class="bubble"><span class="dot ok"></span> Enabled: <b>{{ $ls['enabled'] ?? 0 }}</b></div>
        <div class="bubble"><span class="dot warn"></span> Disabled: <b>{{ $ls['disabled'] ?? 0 }}</b></div>
        <div class="bubble"><span class="dot"></span> Default: <b>{{ $ls['default'] ?? 200 }}</b></div>
      </div>
      <button class="btn outline" onclick="openUserFinder()">Adjust Limits</button>
    </div>

    <div class="card span-2">
      <div class="card-head">
        <div>
          <div class="card-title">Traffic — 30 days</div>
          <div class="card-sub">Requests per day</div>
        </div>
        <div class="pill">Requests</div>
      </div>
      <canvas id="trafficChart" height="96"></canvas>
    </div>

    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">Top Queries — 7d</div>
          <div class="card-sub">Most frequent inputs</div>
        </div>
      </div>
      <div id="topQueries" class="list slim">
        @foreach(($topQueries ?? []) as $row)
          <div class="row"><span class="text">{{ $row['query'] ?? '—' }}</span><span class="meta">{{ $row['count'] ?? 0 }}</span></div>
        @endforeach
      </div>
    </div>

    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">Error Digest — 24h</div>
          <div class="card-sub">Grouped by message</div>
        </div>
      </div>
      <div id="errorsDigest" class="list slim">
        @foreach(($errors ?? []) as $err)
          <div class="row"><span class="text">{{ $err['message'] ?? '—' }}</span><span class="meta">{{ $err['count'] ?? 0 }}</span></div>
        @endforeach
      </div>
    </div>

    <div class="card span-2">
      <div class="card-head">
        <div>
          <div class="card-title">Global Search History</div>
          <div class="card-sub">Newest 100 records</div>
        </div>
        <div class="head-actions">
          <input id="historyFilter" class="input" placeholder="Filter (email/domain/url)" />
          <button id="exportCsvBtn" class="btn ghost">Export CSV</button>
        </div>
      </div>
      <table class="table" id="global-history">
        <thead><tr><th>When</th><th>User</th><th>URL / Query</th><th>Tool</th><th>Tokens</th><th>Cost</th></tr></thead>
        <tbody><tr><td colspan="6" class="muted">Loading…</td></tr></tbody>
      </table>
    </div>

    <div class="card">
      <div class="card-head">
        <div>
          <div class="card-title">Online — 5 min</div>
          <div class="card-sub">Presence snapshot</div>
        </div>
      </div>
      <div id="onlineList" class="list">
        <div class="row"><span class="text muted">Waiting for data…</span></div>
      </div>
    </div>
  </div>
</div>

{{-- User Drawer --}}
<div id="userDrawer" class="drawer hidden" onclick="if(event.target===this) closeUserDrawer()">
  <div class="panel">
    <div class="panel-head">
      <div>
        <div class="panel-title" id="udTitle">User</div>
        <div class="panel-sub" id="udSub">—</div>
      </div>
      <button class="btn ghost" onclick="closeUserDrawer()">Close</button>
    </div>
    <div class="panel-body" id="udBody">Loading…</div>
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
  window.AURORA = {
    live: @json($liveUrl),
    userLive: function(id){ return (@json(url('/admin/users')) + '/' + id + '/live'); },
    userSave: function(id){ return (@json(url('/admin/users')) + '/' + id + '/limits'); },
    csrf: '{{ csrf_token() }}'
  };
</script>
<script src="/js/admin-v4.js" defer></script>
@endpush
