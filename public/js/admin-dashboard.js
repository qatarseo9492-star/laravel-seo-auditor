/* public/js/admin-dashboard.js */
(function () {
  'use strict';

  // ---------- Config / Endpoints ----------
  const S = window.AURORA || {};
  const LIVE_URL =
    window.__ADMIN_LIVE_URL ||
    document.getElementById('dashboard-root')?.dataset.liveUrl ||
    S.live ||
    '/admin/dashboard/live';

  const userLive = S.userLive || ((id) => `/admin/users/${id}/live`);
  const userSave = S.userSave || ((id) => `/admin/users/${id}/limits`);
  const CSRF = S.csrf || document.querySelector('meta[name="csrf-token"]')?.content || '';

  // ---------- Tiny utils ----------
  const $  = (sel, n = document) => n.querySelector(sel);
  const $$ = (sel, n = document) => Array.from(n.querySelectorAll(sel));
  const fmt   = (n) => new Intl.NumberFormat().format(Number(n || 0));
  const money = (n) => '$' + Number(n || 0).toFixed(4);
  const safe  = (v) => (v == null ? '' : String(v));
  const csvEscape = (s) => {
    const t = String(s ?? '').replace(/\r?\n/g, ' ').trim();
    return /[",]/.test(t) ? '"' + t.replace(/"/g, '""') + '"' : t;
  };

  // ---------- KPIs ----------
  function renderKPIs(k) {
    const set = (id, v) => { const el = document.getElementById(id); if (el) el.textContent = v; };
    set('kpi-searches-today', fmt(k.searchesToday));
    set('kpi-total-users', fmt(k.totalUsers));
    set('kpi-openai-cost-today', money(k.cost24h));
    if (document.getElementById('kpi-openai-tokens')) set('kpi-openai-tokens', fmt(k.tokens24h || 0));
    const al = document.getElementById('activeLive'); if (al) al.textContent = fmt(k.active5m);
    const da = document.getElementById('kpi-dau');     if (da) da.textContent = fmt(k.dau);
    const ma = document.getElementById('kpi-mau');     if (ma) ma.textContent = fmt(k.mau);

    // Gauge (simple % display via title)
    const gauge = document.querySelector('.header-gauge');
    if (gauge) {
      const v = Number(k.searchesToday || 0);
      const max = Number(k.dailyLimit || Math.max(100, v));
      const pct = Math.max(0, Math.min(100, Math.round((v / max) * 100)));
      gauge.setAttribute('title', `Daily usage: ${fmt(v)} / ${fmt(max)} (${pct}%)`);
    }
  }

  // ---------- System Health ----------
  function renderServices(services) {
    const tb = $('#system-health tbody'); if (!tb) return;
    tb.innerHTML = '';
    if (!services?.length) {
      tb.innerHTML = '<tr><td colspan="3" class="muted">No data.</td></tr>';
      return;
    }
    let okAll = true;
    services.forEach((s) => {
      okAll = okAll && !!s.ok;
      const dot = s.ok ? '#39d98a' : '#ff5063';
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${safe(s.name)}</td>
        <td><span class="chip ${s.ok ? 'ok' : 'warn'}"><span class="dot" style="background:${dot};margin-right:6px"></span>${s.ok ? 'OK' : 'Down'}</span></td>
        <td>${s.latency_ms ?? '—'}${s.latency_ms ? ' ms' : ''}</td>`;
      tb.appendChild(tr);
    });
    const chip = document.getElementById('healthChip');
    if (chip) {
      chip.className = 'chip ' + (okAll ? 'ok' : 'warn');
      chip.textContent = okAll ? 'Healthy' : 'Issues detected';
    }
  }

  // ---------- Global History ----------
  function renderHistory(rows) {
    const tb = $('#global-history tbody'); if (!tb) return;
    tb.innerHTML = '';
    if (!rows?.length) {
      tb.innerHTML = '<tr><td class="muted" colspan="6">No recent activity.</td></tr>';
      return;
    }
    rows.forEach((r) => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>${safe(r.when)}</td>
        <td>${safe(r.user)}</td>
        <td title="${safe(r.display)}" style="max-width:520px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${safe(r.display)}</td>
        <td>${safe(r.tool)}</td>
        <td>${r.tokens ?? '—'}</td>
        <td>$${r.cost ?? '0.0000'}</td>`;
      tb.appendChild(tr);
    });
  }

  // Bind filter/export once
  function bindHistoryUX() {
    const input = $('#historyFilter');
    if (input && !input.__bound) {
      input.__bound = true;
      input.addEventListener('input', function () {
        const q = this.value.toLowerCase().trim();
        $$('#global-history tbody tr').forEach((tr) => {
          if (!q) { tr.style.display = ''; return; }
          tr.style.display = tr.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    }
    const btn = $('#exportCsvBtn');
    if (btn && !btn.__bound) {
      btn.__bound = true;
      btn.addEventListener('click', function () {
        const rows = [['When', 'User', 'Activity', 'Tool', 'Tokens', 'Cost']];
        $$('#global-history tbody tr').forEach((tr) => {
          if (tr.style.display === 'none') return;
          const cols = Array.from(tr.children).map((td) => {
            const t = td.getAttribute('title') || td.textContent || '';
            return csvEscape(t);
          });
          if (cols.length) rows.push(cols);
        });
        const csv = rows.map((r) => r.join(',')).join('\n');
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url; a.download = 'global-history.csv'; a.click();
        setTimeout(() => URL.revokeObjectURL(url), 0);
      });
    }
  }

  // ---------- Traffic Chart ----------
  function ensureChart() {
    const el = document.getElementById('trafficChart');
    if (!el || !window.Chart) return null;
    if (window.AdminTrafficChart) return window.AdminTrafficChart;
    const ds = { label: 'Daily Analyses', data: [], fill: false, tension: 0.3 };
    window.AdminTrafficChart = new Chart(el, {
      type: 'line',
      data: { labels: [], datasets: [ds] },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
      }
    });
    return window.AdminTrafficChart;
  }

  function renderTraffic(points) {
    const chart = ensureChart();
    if (!chart) return;
    const labels = (points || []).map((p) => p.day.slice(5));
    const data   = (points || []).map((p) => p.count);
    chart.data.labels = labels;
    chart.data.datasets[0].data = data;
    chart.update('none');
  }

  // ---------- Live tick ----------
  async function tick() {
    try {
      const res = await fetch(LIVE_URL + (LIVE_URL.includes('?') ? '&' : '?') + 'fresh=1', {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const d = await res.json();
      if (d.kpis)     renderKPIs(d.kpis);
      if (d.services) renderServices(d.services);
      if (d.history)  renderHistory(d.history);
      if (d.traffic)  renderTraffic(d.traffic);
    } catch (e) {
      if (!window.__ADMIN_DASH_ERR__) {
        console.warn('Dashboard live refresh failed:', e);
        window.__ADMIN_DASH_ERR__ = true;
      }
    }
  }

  // ---------- Optional: User drawer helpers ----------
  window.openUserFinder = function () {
    const id = prompt('Enter user ID to manage:');
    if (id) window.openUserDrawer(id);
  };

  window.openUserDrawer = async function (id) {
    const wrap = document.getElementById('userDrawer');
    const body = document.getElementById('udBody');
    const title = document.getElementById('udTitle');
    if (!wrap || !body || !title) return;
    wrap.classList.remove('hidden'); body.innerHTML = 'Loading…'; title.textContent = 'User #' + id;

    try {
      const res = await fetch(userLive(id), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      const j = await res.json();
      const u = j.user || {};
      const L = j.limit || { daily_limit: 200, is_enabled: true, reason: '' };

      $('#udSub').textContent = `${u.email || ''} — Last seen ${u.last_seen_at || '—'}${u.last_ip ? ` (${u.last_ip})` : ''}`;

      body.innerHTML = `
        <div class="form-grid">
          <div><label>Daily limit</label><input id="udDaily" class="input" type="number" min="0" value="${L.daily_limit}"></div>
          <div><label>Status</label><select id="udEnabled" class="input"><option value="1" ${L.is_enabled ? 'selected' : ''}>Enabled</option><option value="0" ${!L.is_enabled ? 'selected' : ''}>Disabled</option></select></div>
          <div style="grid-column:1/3"><label>Reason</label><input id="udReason" class="input" type="text" value="${L.reason || ''}" placeholder="Optional"></div>
        </div>
        <div style="margin-top:12px;display:flex;gap:8px">
          <button class="btn primary" onclick="saveUserLimit(${id})">Save</button>
          <button class="btn ghost" onclick="closeUserDrawer()">Close</button>
        </div>
        <div style="margin-top:16px">
          <div class="card-sub">Recent Activity</div>
          <div id="udHist" class="list slim" style="max-height:220px;overflow:auto"></div>
        </div>`;

      const hist = Array.isArray(j.latest) ? j.latest : [];
      const udHist = $('#udHist');
      udHist.innerHTML =
        hist.map(h => `<div class="row"><span class="text">${safe(h.created_at)} — ${safe(h.type)}</span><span class="meta">${safe(h.tokens) || ''}</span></div>`).join('') ||
        '<div class="row"><span class="text muted">No recent activity.</span></div>';
    } catch (e) {
      body.innerHTML = '<div class="card-sub" style="color:#ffb3bd">Failed to load user.</div>';
    }
  };

  window.closeUserDrawer = function () {
    const wrap = document.getElementById('userDrawer');
    if (wrap) wrap.classList.add('hidden');
  };

  window.saveUserLimit = async function (id) {
    const payload = {
      daily_limit: Number($('#udDaily')?.value || 200),
      is_enabled: Number($('#udEnabled')?.value || 1),
      reason: $('#udReason')?.value || ''
    };
    try {
      const res = await fetch(userSave(id), {
        method: 'PATCH',
        headers: {
          'Content-Type': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': CSRF
        },
        body: JSON.stringify(payload)
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      alert('Saved'); window.closeUserDrawer();
    } catch (e) {
      alert('Failed to save');
    }
  };

  // ---------- Boot ----------
  document.addEventListener('DOMContentLoaded', function () {
    bindHistoryUX();
    document.getElementById('refreshNow')?.addEventListener('click', tick);
    tick();
    setInterval(tick, 10000);
  });
})();
