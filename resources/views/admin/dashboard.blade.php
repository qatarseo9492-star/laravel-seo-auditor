@extends('layouts.app')
@section('title','Admin — Feature Pack Dashboard')

@push('head')
<!-- ===== External libs (CDN) ===== -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-dt@2.1.7/css/dataTables.dataTables.min.css">
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/dropzone@6.0.0-beta.2/dist/dropzone.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.3.0/fonts/remixicon.css" rel="stylesheet" />

<style>
  /* =======================  Neon Pro Admin Theme  ======================= */
  :root{
    --bg: #070d1b;            /* page background */
    --bg-2: #0c1327;           /* panels */
    --bg-3: #0f1730;           /* cards */
    --fg: #e6e9f0;             /* text */
    --muted: #9aa6b2;          /* secondary text */
    --bdr: rgba(255,255,255,.10);

    /* Accents */
    --blue-1: #00c6ff; --blue-2: #0072ff;    /* primary */
    --green-1:#00ff8a; --green-2:#00ffc6;    /* success */
    --amber-1:#ffd700; --amber-2:#ffa500;    /* warn */
    --mag-1:#ff1493;  --mag-2:#8a2be2;       /* purple/pink */

    --shadow: 0 10px 30px rgba(0,0,0,.35);
  }
  body{ background: radial-gradient(1200px 600px at 70% -200px, rgba(0,114,255,.18), transparent 60%), var(--bg); }
  :is(a,button,input,select,textarea):focus-visible{ outline: 2px solid var(--blue-1); outline-offset: 2px; }

  /* Layout */
  .layout{ display:grid; grid-template-columns: 260px 1fr; min-height: calc(100vh - 70px); }
  .aside{ position:sticky; top:64px; align-self:start; height: calc(100vh - 64px); overflow:auto; background: var(--bg-2); border-right:1px solid var(--bdr); }
  .main{ padding:18px; }

  /* Sidebar */
  .brand{ display:flex; align-items:center; gap:10px; padding:14px 16px; font-weight:900; letter-spacing:.4px; }
  .brand i{ font-size:22px; }
  .side-nav{ padding:8px; display:flex; flex-direction:column; gap:6px; }
  .nav-group{ margin-top:10px; }
  .nav-label{ color: var(--muted); font-size:11px; letter-spacing:.6px; text-transform:uppercase; padding:10px 12px; }
  .nav-item{ display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius: 12px; color: var(--fg); text-decoration:none; border:1px solid transparent; }
  .nav-item:hover{ background: rgba(255,255,255,.04); border-color: var(--bdr); }
  .nav-item.active{ background: linear-gradient(135deg,var(--blue-1),var(--blue-2)); color:#071228; box-shadow: 0 10px 18px rgba(0,114,255,.35); }
  .nav-item .badge{ margin-left:auto; background: rgba(255,255,255,.18); padding:2px 8px; border-radius:999px; font-size:11px; }
  details.menu > summary{ list-style:none; cursor:pointer; }
  details.menu[open] > summary .chev{ transform:rotate(90deg); }
  .sub{ margin-left:6px; padding-left:8px; display:flex; flex-direction:column; }
  .sub a{ padding:8px 12px; border-radius: 10px; color: var(--muted); text-decoration:none; }
  .sub a:hover{ color: var(--fg); background: rgba(255,255,255,.04); }

  /* Topbar */
  .topbar{ position: sticky; top:0; z-index: 3; background: rgba(7,13,27,.65); backdrop-filter: blur(8px); border-bottom:1px solid var(--bdr); }
  .topbar-i{ height:64px; max-width: 1280px; margin:0 auto; display:flex; align-items:center; justify-content:space-between; padding:0 12px; }
  .search{ display:flex; align-items:center; gap:10px; flex:1; max-width:520px; background: var(--bg-2); border:1px solid var(--bdr); border-radius: 999px; padding:6px 10px; }
  .search input{ flex:1; background: transparent; border:0; outline:0; color: var(--fg); }
  .actions{ display:flex; align-items:center; gap:12px; }
  .icon-btn{ display:inline-flex; align-items:center; justify-content:center; width:36px; height:36px; border-radius:12px; background: var(--bg-2); border:1px solid var(--bdr); cursor:pointer; }
  .quick-add{ background: linear-gradient(135deg,var(--green-1),var(--green-2)); color:#062015; padding:8px 12px; border-radius:12px; font-weight:800; border:0; box-shadow: 0 10px 18px rgba(0,255,138,.25); }

  /* KPIs */
  .kpis{ display:grid; grid-template-columns: repeat(4, minmax(220px,1fr)); gap:14px; margin: 14px 0 18px; }
  .kpi{ position:relative; background: var(--bg-3); border:1px solid var(--bdr); border-radius: 18px; padding:16px; box-shadow: var(--shadow); overflow:hidden; }
  .kpi:before{ content:""; position:absolute; inset:-1px; border-radius: 18px; padding:1px; background: linear-gradient(135deg, var(--blue-1), var(--mag-2)); -webkit-mask:linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor; mask-composite: exclude; }
  .kpi[data-accent="green"]:before{ background: linear-gradient(135deg,var(--green-1),var(--green-2)); }
  .kpi[data-accent="amber"]:before{ background: linear-gradient(135deg,var(--amber-1),var(--amber-2)); }
  .kpi[data-accent="purple"]:before{ background: linear-gradient(135deg,var(--mag-1),var(--mag-2)); }
  .kpi-title{ font-size:12px; color: var(--muted); letter-spacing:.4px; text-transform: uppercase; }
  .kpi-val{ font-size: clamp(22px, 3.4vw, 34px); font-weight: 900; line-height: 1.1; margin-top: 6px; }
  .kpi-sub{ font-size:12px; color: var(--muted); margin-top: 6px; display:flex; align-items:center; gap:6px; }
  .pill{ background: rgba(255,255,255,.06); border:1px solid var(--bdr); border-radius: 999px; padding:4px 10px; font-size:12px; }

  /* Grid Panels */
  .grid{ display:grid; grid-template-columns: 1.1fr .9fr; gap:14px; }
  .panel{ background: var(--bg-2); border:1px solid var(--bdr); border-radius: 18px; box-shadow: var(--shadow); overflow:hidden; }
  .panel-h{ display:flex; align-items:center; justify-content:space-between; padding:12px 14px; border-bottom:1px solid var(--bdr); background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,0)); }
  .panel-h h3{ font-size:14px; letter-spacing:.3px; text-transform: uppercase; color: var(--muted); }
  .panel-b{ padding: 14px; }

  /* Tables */
  .table{ width:100%; border-collapse:separate; border-spacing:0; }
  .table th, .table td{ padding: 10px 12px; border-bottom: 1px solid var(--bdr); vertical-align: middle; }
  .table thead th{ font-size:12px; text-transform:uppercase; letter-spacing:.35px; color: var(--muted); background: rgba(255,255,255,.04); }
  .table tbody tr:hover{ background: rgba(255,255,255,.03); }

  /* Kanban */
  .kanban{ display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; }
  .kb-col{ background: var(--bg-3); border:1px solid var(--bdr); border-radius: 16px; padding:10px; min-height:180px; }
  .kb-head{ display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; font-weight:800; }
  .kb-card{ background: var(--bg-2); border:1px solid var(--bdr); border-radius:12px; padding:10px; margin-bottom:8px; cursor:grab; }

  /* File manager */
  .files{ display:grid; grid-template-columns: repeat(4, 1fr); gap:10px; }
  .file{ background: var(--bg-3); border:1px solid var(--bdr); border-radius:12px; padding:10px; }

  /* Chat mini */
  .chat{ height:340px; display:flex; flex-direction:column; gap:8px; }
  .chat-log{ flex:1; overflow:auto; border:1px solid var(--bdr); border-radius:12px; padding:10px; background: var(--bg-3); }
  .msg{ margin:6px 0; }
  .from-me{ text-align:right; }

  /* Responsive */
  @media (max-width: 1200px){ .kpis{ grid-template-columns: repeat(2,1fr);} .grid{ grid-template-columns: 1fr; } .files{ grid-template-columns: repeat(2,1fr);} .layout{ grid-template-columns: 76px 1fr;} .nav-label{ display:none;} .nav-item span{ display:none;} }
  @media (max-width: 640px){ .files{ grid-template-columns: 1fr;} .kanban{ grid-template-columns: 1fr;} }
</style>
@endpush

@section('content')
<!-- ===== Topbar ===== -->
<header class="topbar">
  <div class="topbar-i">
    <div class="brand"><i class="ri-flashlight-fill" style="color:var(--blue-1)"></i> <span>Semantic SEO — Admin</span></div>
    <div class="search"><i class="ri-search-line"></i><input placeholder="Search users, queries, invoices…"/></div>
    <div class="actions">
      <button class="icon-btn" title="Notifications"><i class="ri-notification-3-line"></i></button>
      <button class="icon-btn" title="Theme"><i class="ri-sun-line"></i></button>
      <button class="quick-add"><i class="ri-add-line"></i> New</button>
    </div>
  </div>
</header>

<div class="layout">
  <!-- ===== Sidebar ===== -->
  <aside class="aside">
    <nav class="side-nav">
      <div class="nav-group">
        <div class="nav-label">Dashboard</div>
        <a class="nav-item active" href="#"><i class="ri-dashboard-line"></i><span>Overview</span></a>
        <a class="nav-item" href="#analytics"><i class="ri-line-chart-line"></i><span>Analytics</span></a>
        <a class="nav-item" href="#commerce"><i class="ri-shopping-bag-3-line"></i><span>eCommerce</span><span class="badge">Beta</span></a>
      </div>

      <div class="nav-group">
        <div class="nav-label">Apps</div>
        <details class="menu" open>
          <summary class="nav-item"><i class="ri-mail-line"></i><span>Email</span><i class="ri-arrow-right-s-line chev" style="margin-left:auto"></i></summary>
          <div class="sub">
            <a href="#">Inbox</a>
            <a href="#">Read</a>
            <a href="#">Compose</a>
          </div>
        </details>
        <a class="nav-item" href="#"><i class="ri-chat-1-line"></i><span>Chat</span></a>
        <a class="nav-item" href="#calendar"><i class="ri-calendar-line"></i><span>Calendar</span></a>
        <a class="nav-item" href="#files"><i class="ri-folder-2-line"></i><span>File Manager</span></a>
        <a class="nav-item" href="#kanban"><i class="ri-trello-line"></i><span>Kanban</span></a>
      </div>

      <div class="nav-group">
        <div class="nav-label">UI & Forms</div>
        <a class="nav-item" href="#forms"><i class="ri-input-method-line"></i><span>Forms</span></a>
        <a class="nav-item" href="#tables"><i class="ri-table-2"></i><span>Tables</span></a>
        <a class="nav-item" href="#widgets"><i class="ri-apps-2-line"></i><span>Widgets</span></a>
      </div>

      <div class="nav-group">
        <div class="nav-label">System</div>
        <a class="nav-item" href="#settings"><i class="ri-settings-4-line"></i><span>Settings</span></a>
        <a class="nav-item" href="#"><i class="ri-logout-circle-r-line"></i><span>Logout</span></a>
      </div>
    </nav>
  </aside>

  <!-- ===== Main ===== -->
  <main class="main">

    <!-- KPIs -->
    <section class="kpis">
      <div class="kpi" data-accent="blue">
        <div class="kpi-title">Searches Today</div>
        <div class="kpi-val">{{ $stats['searchesToday'] ?? 124 }}</div>
        <div class="kpi-sub"><span class="pill">Avg 7d: {{ $stats['avg7d'] ?? 98 }}</span></div>
      </div>
      <div class="kpi" data-accent="green">
        <div class="kpi-title">Total Users</div>
        <div class="kpi-val">{{ $stats['totalUsers'] ?? 812 }}</div>
        <div class="kpi-sub"><span class="pill">New today: {{ $stats['newUsers'] ?? 6 }}</span></div>
      </div>
      <div class="kpi" data-accent="amber">
        <div class="kpi-title">OpenAI Cost Today</div>
        <div class="kpi-val">${{ number_format($stats['costToday'] ?? 0.0000, 4) }}</div>
        <div class="kpi-sub"><span class="pill">MTD: ${{ number_format($stats['costMonth'] ?? 32.42, 2) }}</span></div>
      </div>
      <div class="kpi" data-accent="purple">
        <div class="kpi-title">Active Users (5 min)</div>
        <div class="kpi-val">{{ $stats['active5m'] ?? 3 }}</div>
        <div class="kpi-sub"><span class="pill">Peak: {{ $stats['peakToday'] ?? 18 }}</span></div>
      </div>
    </section>

    <!-- Charts + Activity -->
    <section class="grid" id="analytics">
      <div class="panel">
        <div class="panel-h"><h3>Usage & Cost</h3><span class="pill">Last 30 days</span></div>
        <div class="panel-b"><canvas id="lineChart" height="120"></canvas></div>
      </div>
      <div class="panel">
        <div class="panel-h"><h3>Top Tools</h3></div>
        <div class="panel-b"><canvas id="doughnutChart" height="120"></canvas></div>
      </div>
    </section>

    <!-- DataTable + Editor -->
    <section class="grid" style="margin-top:14px">
      <div class="panel">
        <div class="panel-h"><h3>Recent Activity</h3></div>
        <div class="panel-b" style="overflow:auto">
          <table id="activity" class="display" style="width:100%">
            <thead>
              <tr><th>Time</th><th>User</th><th>Action</th><th>Tool</th><th>Tokens</th><th>Cost</th></tr>
            </thead>
            <tbody>
              @for($i=0;$i<14;$i++)
                <tr>
                  <td>{{ now()->subMinutes($i*7)->format('Y-m-d H:i') }}</td>
                  <td>User {{ $i+1 }}</td>
                  <td>Analyzed <em>example.com/page-{{ $i }}</em></td>
                  <td>Analyzer</td>
                  <td>{{ rand(400,1500) }}</td>
                  <td>${{ number_format(rand(1,30)/100, 2) }}</td>
                </tr>
              @endfor
            </tbody>
          </table>
        </div>
      </div>
      <div class="panel" id="forms">
        <div class="panel-h"><h3>Rich Text (Quill)</h3></div>
        <div class="panel-b">
          <div id="editor" style="height:220px"></div>
          <div class="pill" style="margin-top:8px">Use this for announcements or notes</div>
        </div>
      </div>
    </section>

    <!-- Kanban + Chat -->
    <section class="grid" id="kanban" style="margin-top:14px">
      <div class="panel">
        <div class="panel-h"><h3>Kanban — Tasks</h3></div>
        <div class="panel-b">
          <div class="kanban">
            <div class="kb-col" id="kb-todo">
              <div class="kb-head">To do <span class="pill">3</span></div>
              <div class="kb-card">Connect PSI proxy cache</div>
              <div class="kb-card">Wire OpenAI billing report</div>
              <div class="kb-card">User limits REST endpoints</div>
            </div>
            <div class="kb-col" id="kb-doing">
              <div class="kb-head">In progress <span class="pill">2</span></div>
              <div class="kb-card">Content Optimization revamp</div>
              <div class="kb-card">Topic Cluster UX</div>
            </div>
            <div class="kb-col" id="kb-done">
              <div class="kb-head">Done <span class="pill">2</span></div>
              <div class="kb-card">Admin dashboard layout</div>
              <div class="kb-card">Authentication guard</div>
            </div>
          </div>
        </div>
      </div>
      <div class="panel">
        <div class="panel-h"><h3>Team Chat</h3></div>
        <div class="panel-b chat">
          <div class="chat-log" id="chatLog">
            <div class="msg">👋 Welcome! Use this for quick notes.</div>
            <div class="msg from-me">Copy, will finalize limits screen.</div>
          </div>
          <div style="display:flex; gap:8px">
            <input id="chatInput" class="search" style="max-width:none" placeholder="Type message…"/>
            <button class="quick-add" id="sendMsg"><i class="ri-send-plane-2-line"></i></button>
          </div>
        </div>
      </div>
    </section>

    <!-- Calendar + Map -->
    <section class="grid" id="calendar" style="margin-top:14px">
      <div class="panel">
        <div class="panel-h"><h3>Calendar</h3></div>
        <div class="panel-b"><div id="cal" style="height:420px"></div></div>
      </div>
      <div class="panel">
        <div class="panel-h"><h3>Map (Leaflet)</h3></div>
        <div class="panel-b"><div id="map" style="height:420px; border-radius:12px"></div></div>
      </div>
    </section>

    <!-- File Manager + Uploader -->
    <section class="grid" id="files" style="margin-top:14px">
      <div class="panel">
        <div class="panel-h"><h3>Files</h3></div>
        <div class="panel-b">
          <div class="files">
            <div class="file"><i class="ri-file-chart-line"></i> monthly_report.csv <div class="muted"></div></div>
            <div class="file"><i class="ri-image-line"></i> banner.png</div>
            <div class="file"><i class="ri-file-pdf-2-line"></i> invoice-34.pdf</div>
            <div class="file"><i class="ri-database-2-line"></i> export.json</div>
          </div>
        </div>
      </div>
      <div class="panel">
        <div class="panel-h"><h3>Upload (Dropzone)</h3></div>
        <div class="panel-b">
          <form action="{{ route('admin.files.upload') }}" class="dropzone" id="uploader"></form>
          <div class="pill" style="margin-top:8px">Max 5 files, 20MB each</div>
        </div>
      </div>
    </section>

    <!-- Settings -->
    <section class="panel" id="settings" style="margin-top:14px">
      <div class="panel-h"><h3>Settings</h3></div>
      <div class="panel-b">
        <form style="display:grid; grid-template-columns: repeat(3,1fr); gap:10px">
          <label>Daily Search Limit<input class="search" style="max-width:none" type="number" min="0" value="{{ $settings['daily_limit'] ?? 50 }}"/></label>
          <label>Monthly Search Limit<input class="search" style="max-width:none" type="number" min="0" value="{{ $settings['monthly_limit'] ?? 300 }}"/></label>
          <label>Enable OpenAI<input type="checkbox" {{ ($settings['openai'] ?? true) ? 'checked' : '' }}></label>
        </form>
      </div>
    </section>

  </main>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net@2.1.7/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.3/modular/sortable.core.esm.js" type="module"></script>
<script src="https://cdn.jsdelivr.net/npm/dropzone@6.0.0-beta.2/dist/dropzone-min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
  // DataTable
  new DataTable('#activity', { pageLength: 5, lengthChange: false, order:[[0,'desc']] });

  // Chart.js
  const ctx1 = document.getElementById('lineChart');
  const line = new Chart(ctx1, {
    type: 'line',
    data: { labels: [...Array(30).keys()].map(i => i+1), datasets:[
      { label:'Searches', data:[...Array(30)].map(()=> Math.floor(50+Math.random()*60)), tension:.35, fill:true },
      { label:'Cost', data:[...Array(30)].map(()=> (Math.random()*2).toFixed(2)), yAxisID:'y1' }
    ]},
    options: { scales:{ y:{ beginAtZero:true }, y1:{ beginAtZero:true, position:'right' } }, plugins:{ legend:{ labels:{ color:'#e6e9f0' } }}}});

  const ctx2 = document.getElementById('doughnutChart');
  const dough = new Chart(ctx2, { type:'doughnut', data:{ labels:['Analyzer','Topic Cluster','AI Checker'], datasets:[{ data:[44,32,24] }] }, options:{ plugins:{ legend:{ labels:{ color:'#e6e9f0' }}}}});

  // Quill
  const q = new Quill('#editor', { theme:'snow' });

  // Sortable Kanban (use native drag-drop with a little JS)
  const cols=['kb-todo','kb-doing','kb-done'];
  cols.forEach(id => {
    const el = document.getElementById(id);
    el.addEventListener('dragstart', e=>{ if(e.target.classList.contains('kb-card')){ e.dataTransfer.setData('text/plain', e.target.innerText); e.dataTransfer.effectAllowed='move'; e.target.classList.add('dragging'); }});
    el.addEventListener('dragend', e=> e.target.classList.remove('dragging'));
    el.addEventListener('dragover', e=> e.preventDefault());
    el.addEventListener('drop', e=>{ e.preventDefault(); const dragging = document.querySelector('.dragging'); if(dragging) el.appendChild(dragging); });
    [...el.querySelectorAll('.kb-card')].forEach(c=> c.setAttribute('draggable','true'));
  });

  // Chat demo
  const chatInput=document.getElementById('chatInput');
  const chatLog=document.getElementById('chatLog');
  document.getElementById('sendMsg').addEventListener('click', ()=>{
    if(!chatInput.value.trim()) return; const div=document.createElement('div'); div.className='msg from-me'; div.textContent=chatInput.value; chatLog.appendChild(div); chatInput.value=''; chatLog.scrollTop = chatLog.scrollHeight;
  });

  // Calendar
  const cal = new FullCalendar.Calendar(document.getElementById('cal'), {
    initialView:'dayGridMonth', height: 420, themeSystem:'standard', events:[
      { title:'Release v1.2', start: new Date().toISOString().slice(0,10) },
      { title:'Audit OpenAI Bill', start: new Date(Date.now()+86400000*3).toISOString().slice(0,10) },
    ]
  });
  cal.render();

  // Leaflet map
  const map = L.map('map').setView([31.418, 73.079], 5);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);
  L.marker([31.418, 73.079]).addTo(map).bindPopup('You are here');

  // Dropzone (prevent auto discover from generating errors if multiple exists)
  if (window.Dropzone) {
    Dropzone.autoDiscover = false;
    new Dropzone('#uploader', { maxFiles:5, maxFilesize: 20, addRemoveLinks: true });
  }
</script>
@endpush
