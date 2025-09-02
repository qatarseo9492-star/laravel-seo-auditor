@extends('layouts.app')
@section('title','Admin — Dashboard')

@push('head')
<link rel="stylesheet" href="{{ asset('css/admin.css') }}">

{{-- tiny pulse for the gauge arc (kept local to this page) --}}
<style>
  .gauge #arc{
    animation: gaugeGlow 2.8s ease-in-out infinite;
    filter: drop-shadow(0 0 4px rgba(0,198,255,.35)) drop-shadow(0 0 8px rgba(196,77,255,.2));
  }
  @keyframes gaugeGlow{
    0%   { filter: drop-shadow(0 0 3px rgba(0,198,255,.28)) drop-shadow(0 0 6px rgba(196,77,255,.16)); }
    50%  { filter: drop-shadow(0 0 7px rgba(0,198,255,.45)) drop-shadow(0 0 12px rgba(196,77,255,.28)); }
    100% { filter: drop-shadow(0 0 3px rgba(0,198,255,.28)) drop-shadow(0 0 6px rgba(196,77,255,.16)); }
  }
</style>
@endpush

@section('content')
<div class="admin-wrap">
  <div class="bread">
    <span>Admin</span><span class="sep">/</span><span class="muted">Dashboard</span>
    @auth
      @if(auth()->user()->isAdmin()) <span class="sep">•</span><span class="badge ok">Admin</span> @endif
    @endauth
  </div>

  <div class="hbar">
    <h1>Dashboard</h1>
    <div class="h-actions">
      <a href="{{ route('home') }}" class="btn">View Site</a>
      <button class="btn grad" id="refreshBtn">Refresh</button>
    </div>
  </div>

  {{-- Score Wheel --}}
  <div class="card">
    <div class="bd">
      <div class="donut">
        @php
          $score = max(0, min(100, round(
            ($metrics['active_users'] ?? 0) * 0.2 +
            ($metrics['analyze_today'] ?? 0) * 0.3 +
            ($metrics['analyze_month'] ?? 0) * 0.1 +
            (($openaiUsage->tokens ?? 0) > 0 ? 10 : 0) +
            (($psiStats->entries ?? 0) > 0 ? 10 : 0)
          )));
        @endphp
        <svg class="gauge" viewBox="0 0 120 120" data-score="{{ $score }}">
          <defs>
            {{-- updated neon gradient: cyan → green → yellow → hot pink → purple --}}
            <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%"   stop-color="#00C6FF"/>
              <stop offset="30%"  stop-color="#00FF8A"/>
              <stop offset="55%"  stop-color="#FFD700"/>
              <stop offset="80%"  stop-color="#FF4D7E"/>
              <stop offset="100%" stop-color="#C44DFF"/>
            </linearGradient>
          </defs>
          <circle cx="60" cy="60" r="48" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="12"/>
          <circle id="arc" cx="60" cy="60" r="48" fill="none" stroke="url(#grad)" stroke-linecap="round"
                  stroke-width="12" stroke-dasharray="0 999" transform="rotate(-90 60 60)"/>
          <text x="60" y="60" dy="5" text-anchor="middle" font-size="20" font-weight="700">{{ $score }}</text>
          <text x="60" y="80" dy="4" text-anchor="middle" font-size="10" opacity=".8">Overall Score</text>
        </svg>

        <div class="legend">
          <div class="chip"><span class="dot" style="background:#00C6FF"></span>Active Users: <strong>{{ $metrics['active_users'] ?? 0 }}</strong></div>
          <div class="chip"><span class="dot" style="background:#00FF8A"></span>Analyzes Today: <strong>{{ $metrics['analyze_today'] ?? 0 }}</strong></div>
          <div class="chip"><span class="dot" style="background:#FFD700"></span>Analyzes Month: <strong>{{ $metrics['analyze_month'] ?? 0 }}</strong></div>
          <div class="chip"><span class="dot" style="background:#C44DFF"></span>Users Total: <strong>{{ $metrics['users_total'] ?? 0 }}</strong></div>
        </div>
      </div>
    </div>
  </div>

  {{-- KPI cards --}}
  <section class="grid kpi" style="margin-top:14px">
    <article class="card kpi"><div class="hd"><strong>Users</strong></div><div class="bd"><div class="v">{{ $metrics['users_total'] }}</div><div class="s">Total Registered</div><div class="glow"></div></div></article>
    <article class="card kpi"><div class="hd"><strong>Active (30m)</strong></div><div class="bd"><div class="v">{{ $metrics['active_users'] }}</div><div class="s">With recent activity</div><div class="glow"></div></div></article>
    <article class="card kpi"><div class="hd"><strong>Analyzes Today</strong></div><div class="bd"><div class="v">{{ $metrics['analyze_today'] }}</div><div class="s">Total requests</div><div class="glow"></div></div></article>
    <article class="card kpi"><div class="hd"><strong>This Month</strong></div><div class="bd"><div class="v">{{ $metrics['analyze_month'] }}</div><div class="s">Total requests</div><div class="glow"></div></div></article>
  </section>

  {{-- PSI + OpenAI --}}
  <section class="grid two">
    <article class="card"><div class="hd"><h3>PSI Cache</h3></div><div class="bd">
      <p class="s">Cache entries: <strong>{{ $psiStats->entries ?? 0 }}</strong></p>
      <p class="s">Last update: <strong>{{ $psiStats->last_update ?? '—' }}</strong></p>
      <div class="bar"><span style="width:{{ ($psiStats->entries ?? 0)?70:20 }}%"></span></div>
    </div></article>

    <article class="card"><div class="hd"><h3>OpenAI Usage (30d)</h3></div><div class="bd">
      <p class="s">Tokens: <strong>{{ $openaiUsage->tokens ?? 0 }}</strong></p>
      <p class="s">Cost: <strong>${{ number_format($openaiUsage->cost ?? 0,2) }}</strong></p>
      @php $aiPct = min(100,max(0,(int)(($openaiUsage->tokens ?? 0)/max(1,$metrics['analyze_month'] ?? 1)))); @endphp
      <div class="bar"><span style="width:{{ $aiPct }}%"></span></div>
    </div></article>
  </section>

  {{-- Daily usage + Active users --}}
  <section class="grid two">
    <article class="card"><div class="hd"><h3>Daily Usage (14d)</h3></div><div class="bd">
      <svg id="dailyChart" viewBox="0 0 520 180" width="100%" height="180" data-points='@json($dailyUsage)'></svg>
    </div></article>

    <article class="card"><div class="hd"><h3>Active Users (7d)</h3></div><div class="bd">
      <table class="table"><thead><tr><th>User</th><th>Email</th><th>IP</th><th>Country</th><th>Seen</th><th>Req</th></tr></thead><tbody>
        @forelse($topUsers as $u)
          <tr><td>{{ $u->name }}</td><td>{{ $u->email }}</td><td>{{ $u->last_ip }}</td><td>{{ $u->last_country }}</td><td>{{ $u->last_seen_at? $u->last_seen_at->diffForHumans():'—' }}</td><td><span class="badge ok">{{ $u->analyze_logs_count }}</span></td></tr>
        @empty
          <tr><td colspan="6" style="text-align:center;color:var(--muted)">No data</td></tr>
        @endforelse
      </tbody></table>
    </div></article>
  </section>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/admin-dashboard.js') }}" defer></script>
@endpush
