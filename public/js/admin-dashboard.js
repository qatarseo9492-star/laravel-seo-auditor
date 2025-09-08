/**
 * Admin Dashboard — Live v3
 * - Keeps your existing tab + gauge behavior
 * - Adds 10s auto-refresh for KPIs, System Health, Global History
 * - Optional: updates a Chart.js line chart if you expose it as window.AdminTrafficChart
 * - Optional: filters + exports Global History if #historyFilter / #exportCsvBtn exist
 */

(function () {
  // ========== Utilities ==========
  const $ = (sel, node = document) => node.querySelector(sel);
  const $$ = (sel, node = document) => Array.from(node.querySelectorAll(sel));

  const fmt = (n) => new Intl.NumberFormat().format(Number(n || 0));
  const money = (n) => "$" + (Number(n || 0).toFixed(4));
  const safeTxt = (v) => (v == null ? "" : String(v));
  const once = (fn) => {
    let done = false;
    return (...args) => {
      if (done) return;
      done = true;
      try { fn(...args); } catch (e) { /* noop */ }
    };
  };

  // Debounce
  function debounce(fn, wait) {
    let t;
    return function (...args) {
      clearTimeout(t);
      t = setTimeout(() => fn.apply(this, args), wait);
    };
  }

  // CSV export
  function toCsv(rows, headers) {
    const esc = (v) => {
      const s = String(v ?? "");
      return /[",\n]/.test(s) ? '"' + s.replace(/"/g, '""') + '"' : s;
    };
    const head = headers.map(esc).join(",");
    const body = rows.map((r) => headers.map((h) => esc(r[h])).join(",")).join("\n");
    return head + "\n" + body;
  }

  // ========== Tabs (keeps your old behavior) ==========
  window.openTab = function (event, tabName) {
    let i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tab-content");
    for (i = 0; i < tabcontent.length; i++) tabcontent[i].style.display = "none";
    tablinks = document.getElementsByClassName("tab-button");
    for (i = 0; i < tablinks.length; i++) tablinks[i].className = tablinks[i].className.replace(" active", "");
    const tgt = document.getElementById(tabName);
    if (tgt) tgt.style.display = "block";
    if (event && event.currentTarget) event.currentTarget.className += " active";
  };

  // ========== Header gauge (keeps your old behavior) ==========
  function animateGauge() {
    const gaugeElement = document.querySelector(".header-gauge");
    if (!gaugeElement) return;
    const gaugeProgress = document.getElementById("dailyUsageGauge");
    if (!gaugeProgress) return;
    const value = parseInt(gaugeElement.dataset.value || "0", 10);
    const max = parseInt(gaugeElement.dataset.max || "100", 10);
    const percentage = Math.min(100, (value / Math.max(1, max)) * 100);
    const circumference = 2 * Math.PI * 15.9155; // Radius from SVG path
    const offset = circumference - (percentage / 100) * circumference;
    gaugeProgress.style.strokeDasharray = `${circumference} ${circumference}`;
    gaugeProgress.style.strokeDashoffset = circumference;
    setTimeout(() => (gaugeProgress.style.strokeDashoffset = offset), 100);
  }

  // ========== Live updates (every 10s) ==========
  let historyData = [];
  let currentAbort = null;

  function renderKPIs(k) {
    const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };
    set("kpi-searches-today", fmt(k.searchesToday));
    set("kpi-total-users", fmt(k.totalUsers));
    set("kpi-openai-cost-today", money(k.cost24h));
    set("kpi-dau", fmt(k.dau));
    set("kpi-mau", fmt(k.mau));
    const al = document.getElementById("activeLive");
    if (al) al.textContent = fmt(k.active5m);
  }

  function renderServices(services) {
    const table = document.getElementById("system-health");
    if (!table) return;
    // Prefer tbody if exists
    const body = table.querySelector("tbody") || table;
    body.innerHTML = "";
    if (!services || !services.length) {
      const tr = document.createElement("tr");
      tr.innerHTML = `<td colspan="3" style="color:#9aa6b2">No data.</td>`;
      body.appendChild(tr);
      return;
    }
    services.forEach((s) => {
      const dot = s.ok ? "#00ff8a" : "#ff3b30";
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${safeTxt(s.name)}</td>
        <td><span class="v3-pill"><span class="v3-dot" style="background:${dot}"></span>${s.ok ? "Operational" : "Down"}</span></td>
        <td>${s.latency_ms ?? "—"} ${s.latency_ms ? "ms" : ""}</td>
      `;
      body.appendChild(tr);
    });
  }

  function renderHistory(rows) {
    historyData = Array.isArray(rows) ? rows : [];
    const tb = $("#global-history tbody");
    if (!tb) return;
    tb.innerHTML = "";
    if (!historyData.length) {
      tb.innerHTML = '<tr><td colspan="6" style="color:#9aa6b2">No recent history.</td></tr>';
      return;
    }
    (historyData).forEach((r) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${safeTxt(r.when)}</td>
        <td>${safeTxt(r.user)}</td>
        <td style="max-width:520px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${safeTxt(r.display)}</td>
        <td>${safeTxt(r.tool)}</td>
        <td>${r.tokens ?? "—"}</td>
        <td>${r.cost ?? "0.0000"}</td>
      `;
      tb.appendChild(tr);
    });
  }

  function filterHistory(term) {
    const tb = $("#global-history tbody");
    if (!tb || !historyData.length) return;
    const q = (term || "").toLowerCase().trim();
    const rows = !q
      ? historyData
      : historyData.filter((r) => {
          return (
            safeTxt(r.user).toLowerCase().includes(q) ||
            safeTxt(r.display).toLowerCase().includes(q)
          );
        });
    tb.innerHTML = "";
    if (!rows.length) {
      tb.innerHTML = '<tr><td colspan="6" style="color:#9aa6b2">No matches.</td></tr>';
      return;
    }
    rows.forEach((r) => {
      const tr = document.createElement("tr");
      tr.innerHTML = `
        <td>${safeTxt(r.when)}</td>
        <td>${safeTxt(r.user)}</td>
        <td style="max-width:520px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${safeTxt(r.display)}</td>
        <td>${safeTxt(r.tool)}</td>
        <td>${r.tokens ?? "—"}</td>
        <td>${r.cost ?? "0.0000"}</td>
      `;
      tb.appendChild(tr);
    });
  }

  function bindHistoryFilterAndExport() {
    const input = document.getElementById("historyFilter");
    const btn = document.getElementById("exportCsvBtn");
    if (input) input.addEventListener("input", debounce(() => filterHistory(input.value), 150));
    if (btn) {
      btn.addEventListener("click", () => {
        const headers = ["when", "user", "display", "tool", "tokens", "cost"];
        const q = (input && input.value) ? input.value : "";
        const rows = (q ? historyData.filter(r => (safeTxt(r.user)+safeTxt(r.display)).toLowerCase().includes(q.toLowerCase())) : historyData);
        const csv = toCsv(rows, headers);
        const blob = new Blob([csv], { type: "text/csv;charset=utf-8" });
        const url = URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "global-history.csv";
        document.body.appendChild(a);
        a.click();
        setTimeout(() => { document.body.removeChild(a); URL.revokeObjectURL(url); }, 0);
      });
    }
  }

  function updateTrafficChart(points) {
    if (!window.AdminTrafficChart || !Array.isArray(points)) return;
    const labels = points.map((p) => p.day);
    const data = points.map((p) => p.count);
    window.AdminTrafficChart.data.labels = labels;
    window.AdminTrafficChart.data.datasets[0].data = data;
    window.AdminTrafficChart.update("none");
  }

  async function tick() {
    // prevent overlapping requests
    if (currentAbort) currentAbort.abort();
    const ctl = new AbortController();
    currentAbort = ctl;
    try {
      const res = await fetch("/admin/dashboard/live?fresh=1", {
        headers: { "X-Requested-With": "XMLHttpRequest" },
        signal: ctl.signal,
      });
      if (!res.ok) return;
      const d = await res.json();
      if (d.kpis) renderKPIs(d.kpis);
      if (d.services) renderServices(d.services);
      if (d.history) renderHistory(d.history);
      if (d.traffic) updateTrafficChart(d.traffic);
    } catch (e) {
      // silent; page continues to work even if endpoint is absent
    }
  }

  // ========== Boot ==========
  document.addEventListener("DOMContentLoaded", function () {
    animateGauge();
    bindHistoryFilterAndExport();
    // initial fetch + interval
    tick();
    setInterval(tick, 10000); // every 10s
  });
})();
