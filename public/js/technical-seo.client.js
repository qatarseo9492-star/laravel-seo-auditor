/* techseo-neon.css â€” Neon theme for Technical SEO section */
:root{
  --bg-0:#1A1A1A; --bg-1:#262626;
  --fg-0:#EAEAEA; --fg-dim:#B6B6B6;
  --br-weak:rgba(255,255,255,.12);
  --panel:rgba(255,255,255,.04);
  --panel-strong:rgba(255,255,255,.06);
  --c-blue-0:#00C6FF; --c-blue-1:#0072FF;
  --c-green-0:#00FF8A; --c-green-1:#00FFC6;
  --c-gold-0:#FFD700;  --c-gold-1:#FFA500;
  --c-pink-0:#FF1493;  --c-red-0:#FF4500;
  --c-purple-0:#8A2BE2;
  --glow-soft:0 0 12px rgba(0,198,255,.35), 0 0 24px rgba(138,43,226,.25);
  --glow-strong:0 0 10px rgba(0,255,138,.55), 0 0 24px rgba(255,20,147,.45);
}
html,body{background:var(--bg-0);color:var(--fg-0)}
.panel{background:linear-gradient(180deg, var(--panel), rgba(255,255,255,.02));border:1px solid var(--br-weak);border-radius:16px;padding:18px;margin-bottom:16px;box-shadow:0 0 0 1px rgba(0,0,0,.2), 0 6px 18px rgba(0,0,0,.25)}
.h{display:flex;align-items:center;gap:12px;margin-bottom:12px}
.h .card-title{background:linear-gradient(90deg,var(--c-blue-0),var(--c-purple-0),var(--c-pink-0),var(--c-gold-0),var(--c-green-0));-webkit-background-clip:text;background-clip:text;color:transparent;letter-spacing:.2px;text-shadow:var(--glow-soft);animation:floatGlow 4s ease-in-out infinite}
.pill{padding:.28rem .7rem;border-radius:999px;border:1px solid transparent;color:white;font-weight:600;background:linear-gradient(90deg, rgba(255,255,255,.1), rgba(255,255,255,.06)) padding-box,linear-gradient(90deg, var(--c-blue-0), var(--c-purple-0), var(--c-pink-0), var(--c-gold-0), var(--c-green-0)) border-box;box-shadow:var(--glow-soft)}
[data-bar]{position:relative;background:rgba(255,255,255,.06);border:1px solid var(--br-weak);border-radius:12px;overflow:hidden;height:12px}
[data-fill]{height:100%;width:0%;background:linear-gradient(90deg, var(--c-red-0), var(--c-gold-0), var(--c-green-0), var(--c-blue-0), var(--c-purple-0));box-shadow:var(--glow-strong);transition:width .7s cubic-bezier(.2,.8,.2,1)}
table{width:100%;border-collapse:collapse}
thead th{font-weight:700;color:var(--fg-0)}
td,th{border-bottom:1px dashed var(--br-weak);padding:10px 8px;vertical-align:top}
.il-url a{color:var(--fg-0);text-decoration:none;border-bottom:1px dashed rgba(255,255,255,.25)}
.il-url a:hover{filter:brightness(1.15);text-shadow:0 0 8px rgba(0,198,255,.35)}
.img-row{display:grid;grid-template-columns:1.2fr .9fr .9fr;gap:12px;padding:10px;border-bottom:1px dashed var(--br-weak);align-items:center}
.img-row .src a{color:var(--fg-0);text-decoration:none}
.img-row .alt.cur{color:var(--fg-dim)}
.img-row .alt.sug{color:#fff;text-shadow:0 0 8px rgba(0,255,138,.35)}
.tree ul{margin:0 0 0 18px;padding:0;list-style:none}
.tree li{margin:6px 0}
.tree .h1,.tree .h2,.tree .h3,.tree .h4,.tree .h5,.tree .h6{display:inline-block}
.tree .h1{color:#fff}.tree .h2{color:#cde7ff}.tree .h3{color:#d7bfff}.tree .h4{color:#ffc9e5}.tree .h5{color:#ffe6a6}.tree .h6{color:#cfffec}
.input{width:100%;padding:.65rem .85rem;border-radius:12px;color:#fff;background:rgba(255,255,255,.06);border:1px solid var(--br-weak);outline:none;transition:border-color .2s ease, box-shadow .2s ease}
.input:focus{border-color:#00C6FF;box-shadow:0 0 0 3px rgba(0,198,255,.2)}
.btn{cursor:pointer;border-radius:12px;border:1px solid transparent;padding:.6rem .95rem;color:#111;background:linear-gradient(180deg, rgba(255,255,255,.9), rgba(255,255,255,.75)) padding-box,linear-gradient(90deg, var(--c-blue-0), var(--c-purple-0), var(--c-pink-0), var(--c-gold-0), var(--c-green-0)) border-box;transition:transform .08s ease}
.btn:hover{transform:translateY(-1px)}
.btn[disabled], .btn[aria-busy='true']{opacity:.65;cursor:not-allowed}
@keyframes floatGlow{0%{filter:drop-shadow(0 0 0 rgba(0,198,255,0))}50%{filter:drop-shadow(0 0 10px rgba(0,198,255,.35))}100%{filter:drop-shadow(0 0 0 rgba(0,198,255,0))}}
