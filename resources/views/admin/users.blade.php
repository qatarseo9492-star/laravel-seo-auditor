@extends('layouts.app')
@section('title','Users — Live')

@section('content')
<div class="sx-root">
  <div class="sx-wrap">

    <div class="sx-topbar">
      <div class="sx-brand">
        <div class="sx-logo">◆</div>
        <div class="sx-title">
          <h1>Users — Live</h1>
          <span class="sx-note">Search, limits, ban/unban, sessions</span>
        </div>
      </div>
      <div class="sx-actions">
        <a href="{{ route('admin.dashboard') }}" class="sx-btn sx-ghost">Back to Dashboard</a>
      </div>
    </div>

    <div class="sx-card sx-span-2">
      <div class="sx-head">
        <div><div class="sx-head-title">Top 20 by last seen · search & quick actions</div></div>
        <div class="sx-actions gap">
          <input id="sxUsersSearch" class="sx-input" placeholder="Search email / name / IP" />
          <button id="sxUsersRefresh" class="sx-btn sx-ghost">Refresh</button>
        </div>
      </div>

      <div class="sx-table-wrap">
        <table class="sx-table">
          <thead>
          <tr>
            <th>ID</th><th>User</th><th>Status</th><th>Last seen</th><th>IP / Country</th><th>Limit</th><th>Actions</th>
          </tr>
          </thead>
          <tbody id="sxUsersBody">
            <tr><td colspan="7" class="sx-muted">Loading…</td></tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">
<style>
/* same SX styles as dashboard (trimmed) */
.sx-root{--bg:#0c1226;--bg2:#101833;--card:#141e3e;--text:#e9eef6;--muted:#9aa6b2;--line:rgba(255,255,255,.08);
--a:#62b5ff;--b:#9c8cff;font-family:Inter,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;background:var(--bg);}
.sx-root *{box-sizing:border-box}
.sx-wrap{max-width:1200px;margin:24px auto;padding:0 16px;color:var(--text)}
.sx-topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.sx-brand{display:flex;gap:12px;align-items:center}
.sx-logo{width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,var(--a),var(--b));display:grid;place-items:center;color:#071022;font-weight:900}
.sx-title h1{margin:0;font-size:24px;font-weight:900}
.sx-note{font-size:12px;color:var(--muted)}
.sx-actions{display:flex;gap:8px}
.sx-btn{border:1px solid var(--line);border-radius:12px;padding:10px 14px;font-weight:700;cursor:pointer;background:rgba(255,255,255,.06);color:var(--text)}
.sx-btn.sx-ghost:hover{background:rgba(255,255,255,.1)}
.sx-table-wrap{overflow:auto;border-radius:12px;border:1px solid var(--line)}
.sx-table{width:100%;border-collapse:separate;border-spacing:0}
.sx-table thead th{font-size:12px;color:var(--muted);text-align:left;padding:10px;border-bottom:1px solid var(--line);background:rgba(255,255,255,.02)}
.sx-table tbody td{padding:10px;border-bottom:1px dashed var(--line)}
.sx-muted{color:var(--muted);text-align:center;padding:18px}
.sx-input{background:rgba(255,255,255,.04);border:1px solid var(--line);border-radius:10px;padding:8px 10px;color:var(--text);min-width:240px}
</style>

@php
  $usersTable = route('admin.users.table');
  $patchLimit = url('/admin/users');
  $patchBan   = url('/admin/users');
  $sessions   = url('/admin/users');
  $csrf       = csrf_token();
@endphp
<script>
(function(){
  const USERS_TABLE = @json($usersTable);
  const LIMIT_URL   = (id)=> @json($patchLimit) + '/' + id + '/limits';
  const BAN_URL     = (id)=> @json($patchBan)   + '/' + id + '/ban';
  const SESS_URL    = (id)=> @json($sessions)   + '/' + id + '/sessions';
  const CSRF        = @json($csrf);

  const $ = (s,n=document)=>n.querySelector(s);
  const $$= (s,n=document)=>Array.from(n.querySelectorAll(s));
  const safe=(v)=> (v==null?'':String(v));

  async function loadUsers(){
    try{
      const q = ($('#sxUsersSearch')?.value||'').trim();
      const qs = q ? ('?q='+encodeURIComponent(q)) : '';
      const res = await fetch(USERS_TABLE + qs, {
        headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
        credentials:'same-origin'
      });
      const j = await res.json();
      renderUsers(j.rows || []);
    }catch(e){
      renderError('Failed to load');
    }
  }

  function renderError(msg){
    const tb = $('#sxUsersBody'); if(!tb) return;
    tb.innerHTML = `<tr><td colspan="7" class="sx-muted">${msg}</td></tr>`;
  }

  function renderUsers(rows){
    const tb = $('#sxUsersBody'); if(!tb) return;
    if(!rows.length){ return renderError('No users.'); }
    tb.innerHTML='';
    rows.forEach(r=>{
      const status = r.banned ? 'Banned' : (r.enabled ? 'Enabled' : 'Disabled');
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${r.id}</td>
        <td><div class="sx-text" title="${safe(r.email)}">${safe(r.name||'—')}<br><span class="sx-note">${safe(r.email)}</span></div></td>
        <td>${status}</td>
        <td>${safe(r.last_seen||'—')}</td>
        <td>${safe(r.ip||'—')} ${r.country?('· '+safe(r.country)) : ''}</td>
        <td>
          <input type="number" min="0" value="${r.limit}" style="width:86px" class="sx-input" id="sxL${r.id}">
          <select class="sx-input" id="sxE${r.id}" style="width:110px">
            <option value="1" ${r.enabled?'selected':''}>Enabled</option>
            <option value="0" ${!r.enabled?'selected':''}>Disabled</option>
          </select>
        </td>
        <td>
          <button class="sx-btn sx-ghost" onclick="saveLimit(${r.id})">Save</button>
          <button class="sx-btn sx-ghost" onclick="toggleBan(${r.id})">${r.banned?'Unban':'Ban'}</button>
          <button class="sx-btn sx-ghost" onclick="showSessions(${r.id})">Sessions</button>
        </td>`;
      tb.appendChild(tr);
    });
  }

  window.saveLimit = async function(id){
    const daily = Number(document.getElementById('sxL'+id).value || 200);
    const en    = Number(document.getElementById('sxE'+id).value || 1);
    try{
      const res = await fetch(LIMIT_URL(id), {
        method:'PATCH',
        headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},
        credentials:'same-origin',
        body: JSON.stringify({ daily_limit: daily, is_enabled: en, reason: '' })
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      loadUsers();
    }catch(e){ alert('Save failed'); }
  }

  window.toggleBan = async function(id){
    try{
      const res = await fetch(BAN_URL(id), {
        method:'PATCH',
        headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},
        credentials:'same-origin'
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      loadUsers();
    }catch(e){ alert('Ban/Unban failed'); }
  }

  window.showSessions = async function(id){
    try{
      const res = await fetch(SESS_URL(id), {
        headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'},
        credentials:'same-origin'
      });
      const j = await res.json();
      const list = (j.rows||[]).map(s => `${s.login_at} → ${s.logout_at} — ${s.ip || '—'} ${s.country?('· '+s.country):''}`).join('\n') || 'No sessions.';
      alert(list);
    }catch(e){ alert('Failed to load sessions'); }
  }

  document.getElementById('sxUsersRefresh')?.addEventListener('click', loadUsers);
  document.getElementById('sxUsersSearch')?.addEventListener('input', () => {
    clearTimeout(window._sxUsersT); window._sxUsersT=setTimeout(loadUsers, 300);
  });

  loadUsers(); setInterval(loadUsers, 15000);
})();
</script>
@endsection
