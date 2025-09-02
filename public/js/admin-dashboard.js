(function () {
  // ---- Gauge arc (animated) ----
  const g = document.querySelector('.gauge');
  if (g) {
    const score = +g.getAttribute('data-score') || 0;
    const c = g.querySelector('#arc');
    const r = 48, L = 2 * Math.PI * r;

    // start collapsed
    c.setAttribute('stroke-dasharray', `0 ${L.toFixed(1)}`);
    c.style.transition = 'stroke-dasharray 900ms ease-out';

    const target = (Math.max(0, Math.min(100, score)) / 100) * L;
    // animate on next frame
    requestAnimationFrame(() => {
      c.setAttribute('stroke-dasharray', `${target.toFixed(1)} ${(L - target).toFixed(1)}`);
    });
  }

  // ---- Daily usage chart (14d) ----
  const svg = document.getElementById('dailyChart');
  if (svg) {
    const ns = 'http://www.w3.org/2000/svg';
    let pts = [];
    try {
      pts = JSON.parse(svg.getAttribute('data-points') || '[]')
        .map(p => ({ x: p.day, y: +p.total || 0 }));
    } catch (_) {}

    const W = 520, H = 180, pad = 28;
    const maxY = Math.max(10, ...pts.map(p => p.y));
    const sx = i => pad + ((W - 2 * pad) * (i / Math.max(1, pts.length - 1)));
    const sy = v => H - pad - ((H - 2 * pad) * (v / maxY));

    svg.innerHTML = '';

    // Gradients
    const defs = document.createElementNS(ns, 'defs');

    const grad = document.createElementNS(ns, 'linearGradient');
    grad.setAttribute('id', 'chartGrad');
    grad.setAttribute('x1', '0'); grad.setAttribute('x2', '0');
    grad.setAttribute('y1', '0'); grad.setAttribute('y2', '1');
    grad.innerHTML = `
      <stop offset="0%"   stop-color="#00C6FF" stop-opacity="0.35"/>
      <stop offset="50%"  stop-color="#00FF8A" stop-opacity="0.25"/>
      <stop offset="100%" stop-color="#FF4D7E" stop-opacity="0.15"/>
    `;
    defs.appendChild(grad);

    const gradLine = document.createElementNS(ns, 'linearGradient');
    gradLine.setAttribute('id', 'chartGradLine');
    gradLine.setAttribute('x1', '0%'); gradLine.setAttribute('x2', '100%');
    gradLine.innerHTML = `
      <stop offset="0%"   stop-color="#00C6FF"/>
      <stop offset="40%"  stop-color="#00FF8A"/>
      <stop offset="70%"  stop-color="#FFD700"/>
      <stop offset="100%" stop-color="#C44DFF"/>
    `;
    defs.appendChild(gradLine);

    // Axis
    const axis = document.createElementNS(ns, 'line');
    axis.setAttribute('x1', pad); axis.setAttribute('x2', W - pad);
    axis.setAttribute('y1', H - pad); axis.setAttribute('y2', H - pad);
    axis.setAttribute('stroke', 'rgba(255,255,255,.18)');

    svg.append(defs, axis);

    // If no points, draw a faint baseline and exit
    if (pts.length === 0) {
      const baseline = document.createElementNS(ns, 'rect');
      baseline.setAttribute('x', pad);
      baseline.setAttribute('y', H - pad - 1);
      baseline.setAttribute('width', W - 2 * pad);
      baseline.setAttribute('height', 2);
      baseline.setAttribute('fill', 'rgba(255,255,255,.08)');
      svg.append(baseline);
      return;
    }

    // Build path
    const path = ['M', sx(0), sy(pts[0].y)];
    for (let i = 1; i < pts.length; i++) path.push('L', sx(i), sy(pts[i].y));
    const d = path.join(' ');

    // Area
    const area = document.createElementNS(ns, 'path');
    area.setAttribute('d', `${d} L ${sx(pts.length - 1)} ${H - pad} L ${sx(0)} ${H - pad} Z`);
    area.setAttribute('fill', 'url(#chartGrad)');
    area.setAttribute('opacity', '0.6');

    // Line
    const line = document.createElementNS(ns, 'path');
    line.setAttribute('d', d);
    line.setAttribute('fill', 'none');
    line.setAttribute('stroke', 'url(#chartGradLine)');
    line.setAttribute('stroke-width', '2.2');

    svg.append(area, line);
  }

  // ---- Refresh (demo) ----
  document.getElementById('refreshBtn')?.addEventListener('click', () => {
    document.querySelectorAll('.bar span').forEach(el => {
      const n = Math.floor(40 + Math.random() * 60);
      el.style.transition = 'width 0.8s ease';
      el.style.width = n + '%';
    });
  });
})();
