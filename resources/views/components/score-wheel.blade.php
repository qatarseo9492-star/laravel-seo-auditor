{{-- Reusable score wheel component
Usage:
<x-score-wheel id="overall" :value="0" :size="260" ticks="20"/>
<script> setScoreWheel('overall', 82); </script>
--}}
@props([
    'id'    => null,   // required for JS updates
    'value' => 0,      // 0–100 initial value
    'size'  => 260,    // px or any CSS size
    'card'  => true,   // glass card wrapper
    'ticks' => 20,     // tick marks
])

@php
  $wheelId = $id ?: ('wheel-'.uniqid());
  $sizePx  = is_numeric($size) ? $size.'px' : $size;
@endphp

<div class="{{ $card ? 'score-card' : '' }}" data-wheel-id="{{ $wheelId }}" style="width:{{ $sizePx }};max-width:100%;">
  @if($card)<div class="score-glow"></div>@endif

  <svg class="score-wheel" viewBox="0 0 120 120" aria-label="Overall score">
    <g class="score-ticks" transform="translate(60,60) rotate(-90)"></g>
    <circle class="bg" cx="60" cy="60" r="54"/>
    <circle class="track" cx="60" cy="60" r="54"/>
    <circle class="progress" cx="60" cy="60" r="54"/>
    <text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle"
          class="score-text" id="{{ $wheelId }}-text">0%</text>
  </svg>
</div>

@once
  <svg width="0" height="0" aria-hidden="true">
    <defs>
      <linearGradient id="gradGood" x1="0%" y1="0%" x2="100%">
        <stop offset="0%" stop-color="#22c55e"/><stop offset="100%" stop-color="#16a34a"/>
      </linearGradient>
      <linearGradient id="gradMid" x1="0%" y1="0%" x2="100%">
        <stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#fb923c"/>
      </linearGradient>
      <linearGradient id="gradBad" x1="0%" y1="0%" x2="100%">
        <stop offset="0%" stop-color="#ef4444"/><stop offset="100%" stop-color="#b91c1c"/>
      </linearGradient>
    </defs>
  </svg>

  <style>
    .score-card{
      position:relative; padding:14px; border-radius:18px;
      background:linear-gradient(180deg,rgba(255,255,255,.06),rgba(255,255,255,.03));
      border:1px solid rgba(255,255,255,.12);
      box-shadow:0 12px 40px rgba(0,0,0,.55);
    }
    .score-glow{
      position:absolute; inset:-1px; border-radius:18px; padding:1px;
      background:conic-gradient(from 210deg,#36e6ff33,#8d69ff33,#ffb44d33,#ff3b5c33,#36e6ff33);
      -webkit-mask:linear-gradient(#000 0 0) content-box,linear-gradient(#000 0 0);
      -webkit-mask-composite:xor; mask-composite:exclude; pointer-events:none;
    }
    .score-wheel{width:100%; height:auto; transform:rotate(-90deg)}
    .score-wheel circle{fill:none; stroke-linecap:round}
    .score-wheel .bg{stroke:rgba(255,255,255,.10); stroke-width:16}
    .score-wheel .track{stroke:rgba(255,255,255,.06); stroke-width:16}
    .score-wheel .progress{
      stroke:url(#gradBad); stroke-width:16;
      stroke-dasharray:339; stroke-dashoffset:339;
      transition:stroke-dashoffset .7s cubic-bezier(.2,.9,.2,1), stroke .25s ease, filter .25s ease;
      filter:drop-shadow(0 0 12px rgba(141,105,255,.28));
    }
    .score-ticks line{stroke:rgba(255,255,255,.18); stroke-width:1}
    .score-text{
      font-size:clamp(2.1rem,4.5vw,3.2rem);
      font-weight:1000; fill:#fff; transform:rotate(90deg);
      text-shadow:0 0 18px rgba(255,59,92,.25);
    }
  </style>

  <script>
    (function(){
      const CIRC = 339;

      // Global API available to all pages:
      // Awesome — I turned the wheel into a reusable Blade component so you can drop it anywhere and control it with a single JS call:
      // setScoreWheel('<id>', <value 0–100>)
      window.setScoreWheel = window.setScoreWheel || function(id, value){
        const host = document.querySelector('[data-wheel-id="'+id+'"]');
        if(!host) return;
        const circle = host.querySelector('.progress');
        const text   = host.querySelector('.score-text');
        const v = Math.max(0, Math.min(100, Number(value)||0));
        const offset = CIRC - (v/100)*CIRC;
        circle.style.strokeDashoffset = offset;
        if(v>=80)      circle.setAttribute('stroke','url(#gradGood)');
        else if(v>=60) circle.setAttribute('stroke','url(#gradMid)');
        else           circle.setAttribute('stroke','url(#gradBad)');
        text.textContent = Math.round(v) + '%';
      };

      // helper for ticks
      window.__buildWheelTicks = window.__buildWheelTicks || function(host, count){
        const g = host.querySelector('.score-ticks');
        if(!g || g.childNodes.length) return;
        for(let i=0;i<count;i++){
          const a = (i/count)*360 * Math.PI/180;
          const x1 = Math.cos(a)*54, y1 = Math.sin(a)*54;
          const x2 = Math.cos(a)*50, y2 = Math.sin(a)*50;
          const l = document.createElementNS('http://www.w3.org/2000/svg','line');
          l.setAttribute('x1',x1); l.setAttribute('y1',y1);
          l.setAttribute('x2',x2); l.setAttribute('y2',y2);
          g.appendChild(l);
        }
      };
    })();
  </script>
@endonce

<script>
  // Build ticks & set initial value for THIS instance
  (function(){
    const host = document.querySelector('[data-wheel-id="{{ $wheelId }}"]');
    if(!host) return;
    window.__buildWheelTicks(host, {{ (int)$ticks }});
    window.setScoreWheel('{{ $wheelId }}', {{ (int)$value }});
  })();
</script>
