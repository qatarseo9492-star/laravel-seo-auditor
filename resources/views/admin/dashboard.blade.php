@extends('layouts.app')
@section('title','Admin — Dashboard')

@push('head')
<style>
/* ====== Neon Dark Admin (self-contained) ====== */
:root{
  --bg:#0b0a16; --panel:#111128; --ink:#e8eaf2; --muted:#9aa0b4;
  --br:rgba(255,255,255,.08);
  --g1:#00C6FF; --g2:#0072FF; --g3:#00FF8A; --g4:#00FFC6; --g5:#FFD700; --g6:#FFA500; --g7:#FF4500; --g8:#FF1493; --g9:#8A2BE2;
}
html,body{background:var(--bg);color:var(--ink)}
a{color:inherit}
.admin-wrap{max-width:1200px;margin:18px auto;padding:0 14px}
.bread{display:flex;gap:8px;align-items:center;color:var(--muted);font-size:13px}
.bread .sep{opacity:.5}
.hbar{display:flex;align-items:center;justify-content:space-between;margin:12px 0 16px}
.hbar h1{font-size:20px;letter-spacing:.25px}
.h-actions{display:flex;gap:10px}
.btn{padding:8px 12px;border-radius:10px;border:1px solid var(--br);background:rgba(255,255,255,.03)}
.btn.grad{background:conic-gradient(from 30deg, rgba(0,198,255,.25), rgba(0,255,138,.18), rgba(255,215,0,.18), rgba(255,20,147,.22));}
.grid{display:grid;gap:14px}
.grid.kpi{grid-template-columns:repeat(4,minmax(0,1fr))}
.grid.two{grid-template-columns:1fr 1fr}
.grid.one{grid-template-columns:1fr}
.card{background:var(--panel);border:1px solid var(--br);border-radius:16px;box-shadow:0 10px 22px rgba(0,0,0,.35);overflow:hidden}
.card .hd{display:flex;align-items:center;justify-content:space-between;padding:14px;border-bottom:1px solid var(--br)}
.card .bd{padding:14px}
.kpi{position:relative}
.kpi .v{font-size:26px;font-weight:800}
.kpi .s{font-size:12px;color:var(--muted)}
.kpi .delta{font-size:12px;margin-top:6px}
.kpi .glow{position:absolute;inset:auto -20% -45% -20%;height:120px;background:radial-gradient(600px 140px at 50% 100%, rgba(0,198,255,.16), transparent)}

.donut{display:flex;gap:18px;align-items:center}
.gauge{width:160px;height:160px}
.gauge text{fill:var(--ink);font-size:13px}
.legend{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:8px}
.chip{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.05);border:1px solid var(--br);padding:6px 8px;border-radius:10px;font-size:12px}
.dot{width:10px;height:10px;border-radius:50%}

.table{width:100%;border-collapse:separate;border-spacing:0 8px}
.table th{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);text-align:left;padding:0 10px}
.table td{background:#0f1023;border:1px solid var(--br);padding:10px}
.table tr td:first-child{border-top-left-radius:10px;border-bottom-left-radius:10px}
.table tr td:last-child{border-top-right-radius:10px;border-bottom-right-radius:10px}
.badge{font-size:11px;border-radius:6px;padding:3px 8px;border:1px solid var(--br)}
.badge.ok{background:rgba(0,255,138,.12)}
.badge.warn{background:rgba(255,215,0,.14)}

.bar{height:10px;background:rgba(255,255,255,.06);border:1px solid var(--br);border-radius:999px;overflow:hidden}
.bar span{display:block;height:100%;background:linear-gradient(90deg,var(--g1),var(--g3),var(--g5),var(--g8));box-shadow:0 0 14px rgba(0,198,255,.35) inset}

@media (max-width:1024px){ .grid.kpi{grid-template-columns:repeat(2,minmax(0,1fr))} .grid.two{grid-template-columns:1fr} }
</style>
@endpush

@section('content')
<div class="admin-wrap">
  {{-- Breadcrumbs (role-aware example) --}}
  <div class="bread">
    <span>Admin</span><span class="sep">/</span><span class="muted">Dashboard</span>
    @auth
      @if(auth()->user()->isAdmin()) <span class="sep">•</span><span class="badge ok">Admin</span> @endif
    @endauth
  </div>

  {{-- Header with SVG Score Wheel --}}
  <div class="hbar">
    <h1>Dashboard</h1>
    <div class="h-actions">
      <a href="{{ route('home') }}" class="btn">View Site</a>
      <button class="btn grad" id="refreshBtn">Refresh</button>
    </div>
  </div>

  <div class="card">
    <div class="bd">
      <div class="donut">
        {{-- SVG Radial Gauge (overall health) --}}
        @php
          // simple demo formula; tweak in controller if you prefer
          $score = max(0, min(100, round(
            ($metrics['active_users'] ?? 0) * 0.2 +
            ($metrics['analyze_today'] ?? 0) * 0.3 +
            ($metrics['analyze_month'] ?? 0) * 0.1 +
            (($openaiUsage->tokens ?? 0) > 0 ? 10 : 0) +
            (($psiStats->entries ?? 0) > 0 ? 10 : 0)
          )));
        @endphp
        <svg class="gauge" viewBox="0 0 120 120"
             data-score="{{ $score }}"
             data-colors='["@g1","@g3","@g5","@g8"]'>
          <defs>
            <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="0%">
              <stop offset="0%"  stop-color="#00C6FF"/>
              <stop offset="35%" stop-color="#00FF8A"/>
              <stop offset="70%" stop-color="#FFD700"/>
              <stop offset="100%" stop-color="#FF1493"/>
            </linearGradient>
          </defs>
          <circle cx="60" cy="60" r="48" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="12"/>
          <circle id="arc" cx="60" cy="60" r="48" fill="none" stroke="url(#grad)" stroke-linecap="round"
                  stroke-width="12" stroke-dasharray="0 999" transform="rotate(-90 60 60)"/>
          <text x="60" y="60" dy="5" text-anchor="middle" font-size="20" font-weight="700">{{ $score }}</text>
          <text x="60" y="80" dy="4" text-anchor="middle" font-size="10" opacity=".8">Overall Score</text>
        </svg>

        <div class="legend">
          <div class="chip"><span class="dot" style="background:#00C6FF"></span>Active Users: <strong>{{ number_format($metrics['active_users'] ?? 0) }}</strong></div>
          <div class="chip"><span class="dot" style="background:#00FF8A"></span>Analyzes Today: <strong>{{ number_format($metrics['analyze_today'] ?? 0) }}</strong></div>
          <div class="chip"><span class="dot" style="background:#FFD700"></span>Analyzes Month: <strong>{{ number_format($metrics['analyze_month'] ?? 0) }}</strong></div>
          <div class="chip"><span class="dot" style="background:#FF1493"></span>Users Total: <strong>{{ number_format($metrics['users_total'] ?? 0) }}</strong></div>
        </div>
      </div>
    </div>
  </div>

  {{-- KPIs --}}
  <section class="grid kpi" style="margin-top:14px">
    <article class="card kpi">
      <div class="hd"><strong>Users</strong></div>
      <div class="bd">
        <div class="v">{{ number_format($metrics['users_total'] ?? 0) }}</div>
        <div class="s">Total Registered</div>
        <div class="glow"></div>
      </div>
    </article>

    <article class="card kpi">
      <div class="hd"><strong>Active (30m)</strong></div>
      <div class="bd">
        <div class="v">{{ number_format($metrics['active_users'] ?? 0) }}</div>
        <div class="s">With recent activity</div>
        <div class="glow"></div>
      </div>
    </article>

    <article class="card kpi">
      <div class="hd"><strong>Analyzes Today</strong></div>
      <div class="bd">
        <div class="v">{{ number_format($metrics['analyze_today'] ?? 0) }}</div>
        <div class="s">Total requests</div>
        <div class="glow"></div>
      </div>
    </article>

    <article class="card kpi">
      <div class="hd"><strong>This Month</strong></div>
      <div class="bd">
        <div class="v">{{ number_format($metrics['analyze_month'] ?? 0) }}</div>
        <div class="s">Total requests</div>
        <div class="glow"></div>
      </div>
    </article>
  </section>

  {{-- PSI + OpenAI panels --}}
  <section class="grid two">
    <article class="card">
      <div class="hd"><h3>PSI Cache</h3></div>
      <div class="bd">
        <p class="s">Cache entries: <strong>{{ number_format($psiStats->entries ?? 0) }}</strong></p>
        <p class="s">Last update: <strong>{{ $psiStats->last_update ?? '—' }}</strong></p>
        <div class="bar" style="margin-top:10px" title="PSI health">
          @php
            $psiHealth = min(100, max(0, ($psiStats->entries ?? 0) ? 70 : 20));
          @endphp
          <span style="width: {{ $psiHealth }}%"></span>
        </div>
      </div>
    </article>

    <article class="card">
      <div class="hd"><h3>OpenAI Usage (30d)</h3></div>
      <div class="bd">
        <p class="s">Total tokens: <strong>{{ number_format((int)($openaiUsage->tokens ?? 0)) }}</strong></p>
        <p class="s">Cost (USD): <strong>${{ number_format((float)($openaiUsage->cost ?? 0), 2) }}</strong></p>
        @php
          $aiPct = min(100, max(0, (int) (($openaiUsage->tokens ?? 0)/ max(1, ($metrics['analyze_month'] ?? 1)) )));
        @endphp
        <div class="bar" style="margin-top:10px" title="AI intensity">
          <span style="width: {{ $aiPct }}%"></span>
        </div>
      </div>
    </article>
  </section>

  {{-- Daily usage & Active users --}}
  <section class="grid two">
    <article class="card">
      <div class="hd"><h3>Daily Usage (14 days)</h3></div>
      <div class="bd">
        <svg id="dailyChart" viewBox="0 0 520 180" width="100%" height="180"
             data-points='@json($dailyUsage)'></svg>
      </div>
    </article>

    <article class="card">
      <div class="hd"><h3>Active Users (7d)</h3></div>
      <div class="bd">
        <table class="table">
          <thead><tr><th>User</th><th>Email</th><th>IP</th><th>Country</th><th>Seen</th><th>Req</th></tr></thead>
          <tbody>
          @forelse($topUsers as $u)
            <tr>
              <td>{{ $u->name ?? '—' }}</td>
              <td>{{ $u->email ?? '—' }}</td>
              <td>{{ $u->last_ip ?? '—' }}</td>
              <td>{{ $u->last_country ?? '—' }}</td>
              <td>{{ $u->last_seen_at ? $u->last_seen_at->diffForHumans() : '—' }}</td>
              <td><span class="badge ok">{{ $u->analyze_logs_count }}</span></td>
            </tr>
          @empty
            <tr><td colspan="6" style="text-align:center;color:var(--muted)">No data yet</td></tr>
          @endforelse
          </tbody>
        </table>
      </div>
    </article>
  </section>
</div>
@endsection

@push('scripts')
<script>
/* ====== Gauge & Mini Chart (pure SVG) ====== */
(function(){
  // Gauge arc
  const g = document.querySelector('.gauge');
  if(g){
    const score = +g.getAttribute('data-score') || 0;
    const c = g.querySelector('#arc');
    const r = 48, L = 2*Math.PI*r;
    const dash = (Math.max(0,Math.min(100,score))/100)*L;
    c.setAttribute('stroke-dasharray', dash.toFixed(1)+' '+(L-dash).toFixed(1));
  }

  // Mini area chart for daily usage
  const svg = document.getElementById('dailyChart');
  if(svg){
    const raw = svg.getAttribute('data-points');
    let pts = [];
    try{ pts = JSON.parse(raw).map(p=>({x:p.day, y:+p.total})); }catch(e){}
    const W = 520, H = 180, pad = 28;
    const maxY = Math.max(10, ...pts.map(p=>p.y));
    const sx = (i)=> pad + ( (W-2*pad) * (i/Math.max(1,pts.length-1)) );
    const sy = (v)=> H-pad - ( (H-2*pad) * (v/maxY) );

    const path = ['M', sx(0), sy((pts[0]||{y:0}).y)];
    for(let i=1;i<pts.length;i++){ path.push('L', sx(i), sy(pts[i].y)); }
    const d = path.join(' ');

    const area = document.createElementNS('http://www.w3.org/2000/svg','path');
    area.setAttribute('d', d + ` L ${sx(pts.length-1)} ${H-pad} L ${sx(0)} ${H-pad} Z`);
    area.setAttribute('fill','rgba(0,198,255,.18)');
    area.setAttribute('stroke','none');

    const line = document.createElementNS('http://www.w3.org/2000/svg','path');
    line.setAttribute('d', d);
    line.setAttribute('fill','none');
    line.setAttribute('stroke','#00C6FF');
    line.setAttribute('stroke-width','2');

    const axis = document.createElementNS('http://www.w3.org/2000/svg','line');
    axis.setAttribute('x1', pad); axis.setAttribute('x2', W-pad);
    axis.setAttribute('y1', H-pad); axis.setAttribute('y2', H-pad);
    axis.setAttribute('stroke','rgba(255,255,255,.15)');

    svg.innerHTML='';
    svg.append(axis, area, line);
  }

  // Refresh demo (re-animates bars)
  document.getElementById('refreshBtn')?.addEventListener('click', ()=>{
    document.querySelectorAll('.bar span').forEach(el=>{
      const n = Math.floor(40+Math.random()*60);
      el.style.width = n+'%';
    });
  });
})();
</script>
@endpush
