@extends('layouts.app')

@section('title', 'Admin Dashboard — Aurora v4')

@push('styles')
{{-- We will use your existing v4 CSS file --}}
<link rel="stylesheet" href="{{ asset('css/admin-v4.css') }}">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('content')
<div class="aurora container">
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
      <table class="table" id="system-health">
        <thead><tr><th>Service</th><th>Status</th><th>Latency</th></tr></thead>
        <tbody>
        @forelse($systemHealth ?? [] as $service)
          <tr>
            <td>{{ $service['name'] }}</td>
            <td>
                <span class="dot {{ $service['ok'] ? 'ok' : 'warn' }}"></span>
                {{ $service['status'] }}
            </td>
            <td>{{ $service['latency'] }}ms</td>
          </tr>
        @empty
          <tr><td colspan="3" class="muted">No data yet.</td></tr>
        @endforelse
        </tbody>
      </table>
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
      <button class="btn outline" style="margin-top:10px;">Adjust Limits</button>
    </div>

    {{-- User Overview Card --}}
    <div class="card span-3">
      <div class="card-head">
        <div>
          <div class="card-title">User Overview</div>
          <div class="card-sub">Recent users and their activity</div>
        </div>
        <div class="head-actions">
          <input id="historyFilter" class="input" placeholder="Filter by email or name..." />
        </div>
      </div>
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
       <div class="pagination-links">
            {{ ($users ?? null) ? $users->links() : '' }}
        </div>
    </div>
  </div>
</div>
@endsection

