@extends('layouts.app')
@section('title','Admin — Dashboard')

@push('head')
  {{-- Your uploaded CSS (theme) --}}
  <link rel="stylesheet" href="{{ asset('css/admin-v4.css') }}">

  {{-- Chart.js for the traffic line chart --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js" defer></script>

  <style>
    /* ===================== FIX PACK (UI polish + outline removal) ===================== */
    :root{
      /* softer, accessible focus color */
      --ring: rgba(255,255,255,.25);
      --muted: rgba(229,231,235,.75);
    }
    /* remove any global debug outlines */
    a, button, .btn, .chip, .bubble, .pill, .card, .table, .table * { outline: 0 !important; }
    :focus { outline: 0 !important; box-shadow: none !important; }
    :focus-visible { outline: 2px solid var(--ring); outline-offset: 2px; border-radius: 10px; }

    /* cards & layout spacing */
    .container{max-width:1200px;margin:0 auto;padding:20px;}
    .grid{ display:grid; gap:16px; }
    .kpis{ grid-template-columns: repeat(4,minmax(0,1fr)); }
    .main{ grid-template-columns: 2fr 1fr; }
    @media (max-width: 1100px){ .kpis{grid-template-columns:repeat(2,minmax(0,1fr));} .main{grid-template-columns:1fr;} }
    @media (max-width: 640px){ .kpis{grid-template-columns:1fr;} }

    .card{
      position:relative;
      border:1px solid rgba(255,255,255,.08);
      border-radius:16px;
      padding:18px;
      background:linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,0));
      box-shadow:0 8px 24px rgba(0,0,0,.25);
    }
    .kpis .card{ min-height:132px; }
    .card-head{ display:flex; justify-content:space-between; align-items:flex-start; gap:16px; }
    .card-title{ font-weight:700; letter-spacing:.2px; }
    .card-sub{ color:var(--muted); font-size:13px; margin-top:2px; }

    .topbar{ display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; }
    .brand{ display:flex; align-items:center; gap:12px; }
    .logo{ width:36px;height:36px;border-radius:12px;display:flex;align-items:center;justify-content:center;
           background:linear-gradient(135deg,#27e1ff 0%,#8a5cff 100%); color:#061022; font-weight:900; }
    .title h1{ margin:0; font-size:24px; }
    .badge{ font-size:12px; padding:2px 8px; border-radius:999px; border:1px solid rgba(255,255,255,.12); }

    .btn{ display:inline-flex; align-items:center; gap:8px; padding:8px 12px; border-radius:10px;
          border:1px solid rgba(255,255,255,.14); background:rgba(255,255,255,.04); color:inherit; text-decoration:none; }
    .btn.primary{ background:linear-gradient(90deg,#27e1ff 0%,#8a5cff 100%); border-color:transparent; color:#061022; font-weight:700; }
    .btn.outline{ background:transparent; }
    .btn.ghost{ background:rgba(255,255,255,.04); }

    .bubble{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);padding:4px 8px;border-radius:8px;font-size:12px}
    .pill{display:inline-flex;align-items:center;padding:4px 10px;border-radius:999px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);font-size:12px}
    .chip{display:inline-flex;align-items:center;gap:6px;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);font-size:12px}
    .dot{width:8px;height:8px;border-radius:50%;background:rgba(255,255,255,.5)}
    .dot.ok{background:#39d98a}
    .chip.warn{background:rgba(255,191,0,.12);border-color:rgba(255,191,0,.25)}

    .chart-wrap{ position:relative; height:280px; }
    .header-gauge svg{ width:44px; height:44px; }

    .table{ width:100%; border-collapse:collapse; }
    .table thead th{ text-align:left; padding:10px 8px; font-size:12px; color:var(--muted); border-bottom:1px solid rgba(255,255,255,.08); }
    .table tbody td{ padding:10px 8px; border-bottom:1px solid rgba(255,255,255,.05); }
    .input{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:8px;padding:8px 10px;color:inherit;min-width:220px}
    .muted{opacity:.7}
    /* =================== /FIX PACK =================== */
  </style>
@endpush

@section('content')
@php
  $k = $kpis ?? [
    'searchesToday' => 0, 'totalUsers' => 0, 'cost24h' => 0.0,
    'dau' => 0, 'mau' => 0, 'active5m' => 0, 'dailyLimit' => 100,
  ];
  $liveUrl = \Illuminate\Support\Facades\Route::has('admin.dashboard.live')
           ? route('admin.dashboard.live') : url('admin/dashboard/live');
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
        <div class="header-gauge" title="Daily usage gauge"
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
        <span class="chip"><span class="dot ok"></span>Active <span id="activeLive">{{ number_format($k['active5m']) }}</span> / 5m</span>
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

  {{-- Quick tabs with defensive routes --}}
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

{{-- Your uploaded JS can still run. Below is a safe fallback so the page never looks empty. --}}
<script src="{{ asset('js/admin-dashboard.js') }}" defer></script>

<script>
  document.addEventListener('DOMContentLoaded', function(){
    // Expose live URL for any script
    var root = document.getElementById('dashboard-root');
    window.__ADMIN_LIVE_URL = root ? root.dataset.liveUrl : '{{ $liveUrl }}';

    // Prepare Chart.js line chart (dataset will be filled by refresh below)
    var canvas = document.getElementById('trafficChart');
    if (canvas && window.Chart) {
      var ds = { label: 'Daily Analyses', data: [], fill: false, tension: 0.3 };
      window.AdminTrafficChart = new Chart(canvas, {
        type: 'line',
        data: { labels: [], datasets: [ds] },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: { y: { beginAtZero: true } }
        }
      });
    }

    // -------- Fallback updater (runs even if admin-dashboard.js does not) --------
    (function(){
      const LIVE = window.__ADMIN_LIVE_URL;
      const $ = (s)=>document.querySelector(s);

      function setText(id, val){ const el=document.getElementById(id); if(el) el.textContent=val; }

      function fillKpis(k){
        if(!k) return;
        setText('kpi-searches-today', (k.searchesToday||0).toLocaleString());
        setText('kpi-total-users', (k.totalUsers||0).toLocaleString());
        setText('kpi-openai-cost-today', '$'+Number(k.cost24h||0).toFixed(4));
        setText('kpi-dau', (k.dau||0).toLocaleString());
        setText('kpi-mau', (k.mau||0).toLocaleString());
        var g = document.querySelector('.header-gauge');
        if (g){ g.dataset.value = k.searchesToday||0; g.dataset.max = k.dailyLimit || Math.max(100,k.searchesToday||0); }
      }

      function fillHealth(list){
        const tbody = $('#system-health tbody');
        if(!tbody) return;
        if(!list || !list.length){ tbody.innerHTML = '<tr><td class="muted" colspan="3">No data</td></tr>'; return; }
        tbody.innerHTML = list.map(s => `
          <tr>
            <td>${s.name}</td>
            <td>${s.ok ? '<span class="chip"><span class="dot ok"></span>OK</span>' : '<span class="chip warn">Down</span>'}</td>
            <td>${(s.latency_ms ?? '—')} ms</td>
          </tr>`).join('');
      }

      function fillHistory(rows){
        const tb = $('#global-history tbody');
        if(!tb) return;
        if(!rows || !rows.length){ tb.innerHTML = '<tr><td class="muted" colspan="6">No recent activity.</td></tr>'; return; }
        tb.innerHTML = rows.map(r => `
          <tr>
            <td>${r.when ?? '—'}</td>
            <td>${r.user ?? '—'}</td>
            <td>${r.display ?? '—'}</td>
            <td>${r.tool ?? '—'}</td>
            <td>${r.tokens ?? '—'}</td>
            <td>$${r.cost ?? '0.0000'}</td>
          </tr>`).join('');
      }

      function fillTraffic(series){
        if(!window.AdminTrafficChart || !series) return;
        const labels = series.map(x=>x.day.slice(5));
        const data = series.map(x=>x.count);
        window.AdminTrafficChart.data.labels = labels;
        window.AdminTrafficChart.data.datasets[0].data = data;
        window.AdminTrafficChart.update();
      }

      async function refresh(){
        try{
          const res = await fetch(LIVE, { headers:{'X-Requested-With':'XMLHttpRequest'} });
          const j = await res.json();
          fillKpis(j.kpis);
          fillHealth(j.services);
          fillHistory(j.history);
          fillTraffic(j.traffic);
        }catch(e){ /* silent fallback */ }
      }

      // Filter + CSV
      const filter = document.getElementById('historyFilter');
      if(filter){
        filter.addEventListener('input', function(){
          const q = this.value.toLowerCase();
          document.querySelectorAll('#global-history tbody tr').forEach(tr=>{
            tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
          });
        });
      }
      document.getElementById('exportCsvBtn')?.addEventListener('click', function(){
        const rows = [['When','User','Activity','Tool','Tokens','Cost']];
        document.querySelectorAll('#global-history tbody tr').forEach(tr=>{
          const cols = Array.from(tr.children).map(td=>td.textContent.trim());
          if(cols.length) rows.push(cols);
        });
        const csv = rows.map(r=>r.map(v=>`"${v.replace(/"/g,'""')}"`).join(',')).join('\n');
        const blob = new Blob([csv], {type:'text/csv'});
        const a = document.createElement('a');
        a.href = URL.createObjectURL(blob); a.download = 'global-history.csv'; a.click();
      });

      refresh(); setInterval(refresh, 10000);
    })();
  });
</script>
@endsection
