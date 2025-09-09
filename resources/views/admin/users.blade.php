@extends('layouts.app')
@section('title','Users — Live')

@push('head')
  <link rel="stylesheet" href="{{ asset('css/admin-v4.css') }}">
  <style>
    /* Neon Aurora users page (lightweight, complements admin-v4.css) */
    .container{max-width:1200px;margin:0 auto;padding:20px}
    .card{border:1px solid rgba(255,255,255,.08);border-radius:16px;padding:18px;background:linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,0));box-shadow:0 8px 24px rgba(0,0,0,.25)}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px}
    .brand{display:flex;gap:12px;align-items:center}
    .logo{width:32px;height:32px;border-radius:10px;background:linear-gradient(135deg,#27e1ff,#8a5cff);display:grid;place-items:center;color:#071022;font-weight:900}
    .title h1{margin:0;font-size:24px;font-weight:900}
    .note{font-size:12px;color:rgba(229,231,235,.75)}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;border:1px solid rgba(255,255,255,.14);background:rgba(255,255,255,.04);color:inherit;text-decoration:none;cursor:pointer}
    .btn.primary{background:linear-gradient(90deg,#27e1ff 0%,#8a5cff 100%);border-color:transparent;color:#061022;font-weight:700}
    .btn.ghost:hover{background:rgba(255,255,255,.08)}
    .input{background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.1);border-radius:12px;padding:10px 12px;color:inherit;min-width:260px}
    .head{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
    .head-title{font-weight:800}
    .controls{display:flex;gap:10px;align-items:center}
    .table{width:100%;border-collapse:collapse}
    .table thead th{text-align:left;padding:10px 8px;font-size:12px;color:rgba(229,231,235,.75);border-bottom:1px solid rgba(255,255,255,.08);background:rgba(255,255,255,.02)}
    .table tbody td{padding:10px 8px;border-bottom:1px dashed rgba(255,255,255,.08);vertical-align:top}
    .muted{color:rgba(229,231,235,.75);text-align:center;padding:18px}
    @media (max-width:900px){ .input{min-width:200px} }
  </style>
@endpush

@section('content')
@php
  $back = \Illuminate\Support\Facades\Route::has('admin.dashboard') ? route('admin.dashboard') : url('admin/dashboard');
  $usersTable = \Illuminate\Support\Facades\Route::has('admin.users.table') ? route('admin.users.table') : url('admin/users/table');

  // Action endpoints (show buttons even if endpoints not yet wired; calls will fail gracefully)
  $patchBase = url('admin/users'); // results in /admin/users/{id}/limits  etc.
  $csrf = csrf_token();
@endphp

<div class="container" id="users-root"
     data-table-url="{{ $usersTable }}"
     data-base-url="{{ $patchBase }}"
     data-csrf="{{ $csrf }}">
  <div class="topbar">
    <div class="brand">
      <div class="logo">◆</div>
      <div class="title">
        <h1>Users — Live</h1>
        <div class="note">Search, limits, ban/unban, sessions</div>
      </div>
    </div>
    <a href="{{ $back }}" class="btn ghost">Back to Dashboard</a>
  </div>

  <div class="card">
    <div class="head">
      <div>
        <div class="head-title">Top 20 by last seen · search & quick actions</div>
      </div>
      <div class="controls">
        <input id="uSearch" class="input" placeholder="Search email / name / IP">
        <button id="uRefresh" class="btn">Refresh</button>
      </div>
    </div>

    <table class="table" id="uTable">
      <thead>
        <tr>
          <th>ID</th>
          <th>User</th>
          <th>Status</th>
          <th>Last seen</th>
          <th>IP / Country</th>
          <th>Limit</th>
          <th style="width:170px">Actions</th>
        </tr>
      </thead>
      <tbody id="uBody">
        <tr><td class="muted" colspan="7">Loading…</td></tr>
      </tbody>
    </table>
  </div>
</div>

<script>
(function(){
  const root = document.getElementById('users-root');
  const TABLE = root?.dataset.tableUrl || '/admin/users/table';
  const BASE  = root?.dataset.baseUrl  || '/admin/users';
  const CSRF  = root?.dataset.csrf     || (document.querySelector('meta[name="csrf-token"]')?.content || '');

  const $  = (s,n=document)=>n.querySelector(s);
  const $$ = (s,n=document)=>Array.from(n.querySelectorAll(s));
  const safe = (v)=> v==null ? '' : String(v);
  const fmt  = (n)=> new Intl.NumberFormat().format(Number(n||0));

  function limitUrl(id){ return BASE + '/' + id + '/limits'; }
  function banUrl(id){   return BASE + '/' + id + '/ban'; }
  function sessUrl(id){  return BASE + '/' + id + '/sessions'; }

  async function loadUsers(){
    try{
      const q = ($('#uSearch')?.value || '').trim();
      const url = new URL(TABLE, window.location.origin);
      if (q) url.searchParams.set('q', q);
      const res = await fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} });
      if(!res.ok) throw new Error('HTTP '+res.status);
      const j = await res.json();
      renderUsers(Array.isArray(j.rows) ? j.rows : []);
    }catch(e){
      renderError('Failed to load users.');
    }
  }

  function renderError(msg){
    const tb = $('#uBody'); if(!tb) return;
    tb.innerHTML = `<tr><td colspan="7" class="muted">${safe(msg)}</td></tr>`;
  }

  // r keys expected from controller: id, user, email, last_seen, ip, limit
  function rowHtml(r){
    const status = r.banned ? 'Banned' : (r.enabled === 0 ? 'Disabled' : 'Enabled');
    const limitInput = `<input type="number" min="0" class="input" style="width:90px" id="L${r.id}" value="${r.limit ?? ''}">`;
    const enabledSel = `<select class="input" id="E${r.id}" style="width:110px">
        <option value="1" ${(r.enabled ?? 1) ? 'selected':''}>Enabled</option>
        <option value="0" ${!(r.enabled ?? 1) ? 'selected':''}>Disabled</option>
      </select>`;
    return `
      <tr>
        <td>${fmt(r.id)}</td>
        <td>
          <div style="font-weight:600">${safe(r.user || r.email || '—')}</div>
          <div style="opacity:.7;font-size:12px">${safe(r.email || '')}</div>
        </td>
        <td>${status}</td>
        <td>${safe(r.last_seen || '—')}</td>
        <td>${safe(r.ip || '—')}${r.country ? ' · ' + safe(r.country) : ''}</td>
        <td>${limitInput}<div style="height:8px"></div>${enabledSel}</td>
        <td style="display:flex;gap:8px;flex-wrap:wrap">
          <button class="btn" data-act="save" data-id="${r.id}">Save</button>
          <button class="btn" data-act="ban"  data-id="${r.id}">${r.banned ? 'Unban' : 'Ban'}</button>
          <button class="btn" data-act="sess" data-id="${r.id}">Sessions</button>
        </td>
      </tr>`;
  }

  function renderUsers(rows){
    const tb = $('#uBody'); if(!tb) return;
    if(!rows.length){ renderError('No users.'); return; }
    tb.innerHTML = rows.map(rowHtml).join('');
  }

  async function saveLimit(id){
    const daily = Number(document.getElementById('L'+id)?.value || 0);
    const en    = Number(document.getElementById('E'+id)?.value || 1);
    try{
      const res = await fetch(limitUrl(id), {
        method:'PATCH',
        headers:{'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF},
        body: JSON.stringify({ daily_limit: daily, is_enabled: en, reason: '' })
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      await loadUsers();
    }catch(e){ alert('Save failed'); }
  }

  async function toggleBan(id){
    try{
      const res = await fetch(banUrl(id), {
        method:'PATCH',
        headers:{'X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':CSRF}
      });
      if(!res.ok) throw new Error('HTTP '+res.status);
      await loadUsers();
    }catch(e){ alert('Ban/Unban failed'); }
  }

  async function showSessions(id){
    try{
      const res = await fetch(sessUrl(id), { headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'} });
      if(!res.ok) throw new Error('HTTP '+res.status);
      const j = await res.json();
      const list = (j.rows||[]).map(s => `${s.login_at} → ${s.logout_at||'—'} — ${s.ip || '—'} ${s.country?('· '+s.country):''}`).join('\n') || 'No sessions.';
      alert(list);
    }catch(e){ alert('Failed to load sessions'); }
  }

  // Events
  document.getElementById('uRefresh')?.addEventListener('click', loadUsers);
  document.getElementById('uSearch')?.addEventListener('input', () => {
    clearTimeout(window.__usersDelay); window.__usersDelay = setTimeout(loadUsers, 300);
  });

  document.getElementById('uTable')?.addEventListener('click', (e)=>{
    const btn = e.target.closest('button[data-act]'); if(!btn) return;
    const id = btn.getAttribute('data-id'); const act = btn.getAttribute('data-act');
    if (act === 'save') return saveLimit(id);
    if (act === 'ban')  return toggleBan(id);
    if (act === 'sess') return showSessions(id);
  });

  // Boot
  loadUsers();
  setInterval(loadUsers, 15000);
})();
</script>
@endsection
