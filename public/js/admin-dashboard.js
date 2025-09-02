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

  // Daily usage chart
  const svg = document.getElementById('dailyChart');
  if(svg){
    let pts = [];
    try{ pts = JSON.parse(svg.getAttribute('data-points')).map(p=>({x:p.day, y:+p.total})); }catch(e){}
    const W=520,H=180,pad=28;
    const maxY=Math.max(10,...pts.map(p=>p.y));
    const sx=i=>pad+( (W-2*pad)*(i/Math.max(1,pts.length-1)) );
    const sy=v=>H-pad-( (H-2*pad)*(v/maxY) );
    const path=['M',sx(0),sy((pts[0]||{y:0}).y)];
    for(let i=1;i<pts.length;i++) path.push('L',sx(i),sy(pts[i].y));
    const d=path.join(' ');
    const ns="http://www.w3.org/2000/svg";
    const area=document.createElementNS(ns,'path');
    area.setAttribute('d',d+` L ${sx(pts.length-1)} ${H-pad} L ${sx(0)} ${H-pad} Z`);
    area.setAttribute('fill','rgba(0,198,255,.18)');
    const line=document.createElementNS(ns,'path');
    line.setAttribute('d',d);line.setAttribute('fill','none');
    line.setAttribute('stroke','#00C6FF');line.setAttribute('stroke-width','2');
    const axis=document.createElementNS(ns,'line');
    axis.setAttribute('x1',pad);axis.setAttribute('x2',W-pad);
    axis.setAttribute('y1',H-pad);axis.setAttribute('y2',H-pad);
    axis.setAttribute('stroke','rgba(255,255,255,.15)');
    svg.innerHTML='';svg.append(axis,area,line);
  }

  // Refresh demo
  document.getElementById('refreshBtn')?.addEventListener('click', ()=>{
    document.querySelectorAll('.bar span').forEach(el=>{
      const n=Math.floor(40+Math.random()*60);
      el.style.width=n+'%';
    });
  });
})();
