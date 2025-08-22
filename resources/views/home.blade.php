{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Semantic SEO Master • Dark Purple + Red Smoke</title>

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#0b0a10; --panel:#0f0f1b; --panel-2:#151428; --line:#231f33;
      --text:#ecebf6; --text-dim:#a9a5c6; --text-muted:#9da0bf;
      --primary:#8b5cf6; --secondary:#ef4444; --accent:#22d3ee;
      --good:#10b981; --warn:#f59e0b; --bad:#ef4444;
      --radius:16px; --shadow:0 8px 28px rgba(0,0,0,.5); --shadow-hover:0 14px 40px rgba(0,0,0,.6);
      --transition:.25s ease; --container:1200px;
    }
    *{box-sizing:border-box} html,body{height:100%}
    body{
      margin:0;font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Noto Sans","Helvetica Neue",Arial;
      color:var(--text);
      background: radial-gradient(1200px 700px at 0% -10%, #1a1133 0%, transparent 55%),
                  radial-gradient(1100px 800px at 120% 0%, #1a0f22 0%, transparent 50%),
                  var(--bg);
      overflow-x:hidden; -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }
    a{color:var(--accent);text-decoration:none} a:hover{opacity:.9}

    /* Smoke */
    .bg-smoke{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
    .bg-smoke .blob{
      position:absolute; width:60vmax; height:60vmax; border-radius:50%;
      filter: blur(70px); mix-blend-mode: screen; will-change: transform;
      animation: float 36s linear infinite;
    }
    .blob.purple{ background: radial-gradient(closest-side, rgba(139,92,246,.35), rgba(139,92,246,0) 70%); }
    .blob.red   { background: radial-gradient(closest-side, rgba(239,68,68,.28), rgba(239,68,68,0) 70%); }
    .b1{top:-18%; left:-15%} .b2{bottom:-22%; right:-10%; animation-direction:reverse; animation-duration:30s}
    .b3{top:10%; right:15%; animation-duration:28s} .b4{bottom:10%; left:25%; animation-duration:40s}
    @keyframes float{0%{transform:translate3d(0,0,0) rotate(0)}25%{transform:translate3d(7%,-5%,0) rotate(50deg)}50%{transform:translate3d(-6%,7%,0) rotate(110deg)}75%{transform:translate3d(-10%,-8%,0) rotate(170deg)}100%{transform:translate3d(0,0,0) rotate(360deg)}}
    .veil{position:fixed;inset:0;z-index:1;pointer-events:none;background: radial-gradient(1200px 800px at 0% 0%, rgba(139,92,246,.08), transparent 50%), radial-gradient(900px 700px at 100% 10%, rgba(239,68,68,.06), transparent 45%)}

    .wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
    header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 24px;border-bottom:1px solid var(--line);backdrop-filter:saturate(140%) blur(10px);background:rgba(15,15,27,.35)}
    .brand{display:flex;align-items:center;gap:.75rem}
    .brand-badge{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg, rgba(139,92,246,.2), rgba(239,68,68,.18));border:1px solid rgba(255,255,255,.06); color:#fca5a5}
    .brand small{display:block;color:var(--text-dim)}
    .btn{border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.03);color:var(--text);padding:.6rem .9rem;font-weight:600;cursor:pointer;transition:var(--transition)}
    .btn:hover{transform:translateY(-1px);background:rgba(255,255,255,.06)}
    .btn-primary{background:linear-gradient(135deg,var(--primary),#6d28d9);border-color:transparent}
    .btn-danger{background:linear-gradient(135deg,#b91c1c,#ef4444);border-color:transparent}

    /* Beautiful hero heading */
    .hero-heading{
      font-size:3rem; font-weight:900; text-align:center; line-height:1.05; margin:.2rem 0 .35rem;
      letter-spacing:.5px;
      background: linear-gradient(90deg, #c445ff, #ff0044 60%, #ff7a59 100%);
      -webkit-background-clip:text; -webkit-text-fill-color:transparent;
      text-shadow: 0 0 22px rgba(196,69,255,.18);
    }
    .hero-heading span{ filter:drop-shadow(0 10px 20px rgba(196,69,255,.15)); }
    .hero-sub{ text-align:center; color:var(--text-dim); margin:0 0 1.2rem }

    .hero{display:grid;grid-template-columns:1.1fr .9fr;gap:1.5rem;align-items:center;margin:12px 0 18px}
    .hero-card{background:linear-gradient(180deg, rgba(139,92,246,.10), rgba(239,68,68,.07));border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:28px;box-shadow:var(--shadow)}
    .side{background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:24px;box-shadow:var(--shadow)}

    .analyzer{margin-top:22px;background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:20px;box-shadow:var(--shadow);padding:28px}
    .section-title{font-size:1.5rem;margin:0 0 .3rem} .section-subtitle{margin:0;color:var(--text-dim)}

    .analyze-form input[type="url"]{width:100%;padding:.7rem .9rem;border-radius:12px;border:1px solid var(--line);background:#0b0d1d;color:var(--text)}
    .analyze-form input[type="url"]::placeholder{color:#70738f}

    /* Score wheel */
    .score-area{display:flex;gap:1rem;align-items:center;justify-content:center;margin:1rem 0 0}
    .score-container { width: 180px; }
    .score-wheel { width: 100%; height: auto; transform: rotate(-90deg); }
    .score-wheel circle { fill: none; stroke-width: 12; stroke-linecap: round; }
    .score-wheel .bg { stroke: rgba(255,255,255,.1); }
    .score-wheel .progress {
      stroke: url(#grad);
      stroke-dasharray: 339; /* 2πr where r=54 */
      stroke-dashoffset: 339;
      transition: stroke-dashoffset .6s ease;
      filter: drop-shadow(0 0 8px rgba(196,69,255,.35));
    }
    .score-text {
      font-size: 2.2rem; font-weight: 900; fill: #fff; transform: rotate(90deg);
      text-shadow: 0 0 14px rgba(255,0,68,.25);
    }

    /* Progress bar under wheel */
    .progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px;position:relative}
    .progress-meta{display:flex;gap:.75rem;flex-wrap:wrap;align-items:center;justify-content:space-between;margin-bottom:.5rem}
    .progress-percent{font-weight:900;color:#c4b5fd}
    .overall-chip{font-weight:900;padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.12); background:rgba(139,92,246,.12)}
    .progress-bar{width:100%;height:12px;border-radius:999px;background:#0b1220;overflow:hidden;border:1px solid #101826}
    .progress-fill{height:100%;background:linear-gradient(90deg,var(--primary),var(--secondary));width:0%;transition:width .35s ease}
    .progress-caption{color:var(--text-muted);font-size:.95rem;margin-top:.5rem}
    .save-toast{position:absolute;right:10px;top:-12px;transform:translateY(-100%);background:rgba(16,185,129,.18);color:#a7f3d0;border:1px solid rgba(134,239,172,.4);padding:.35rem .6rem;border-radius:999px;font-weight:700;box-shadow:var(--shadow)}

    /* Category cards + Beautiful checklist items */
    .analyzer-grid{margin-top:1.2rem;display:grid;grid-template-columns:repeat(12,1fr);gap:1rem}
    .category-card{grid-column:span 6;background:var(--panel-2);border:1px solid rgba(255,255,255,.06);border-top:3px solid var(--primary);border-radius:16px;padding:14px;transition:var(--transition);box-shadow:var(--shadow)}
    .category-card:hover{transform:translateY(-3px);box-shadow:var(--shadow-hover)}
    .category-head{display:grid;grid-template-columns:auto 1fr auto;gap:.75rem;align-items:center}
    .category-icon{width:42px;height:42px;border-radius:50%;background:rgba(139,92,246,.18);color:#c4b5fd;display:inline-flex;align-items:center;justify-content:center}
    .category-title{margin:0;font-size:1.08rem} .category-sub{margin:.15rem 0 0;color:var(--text-dim);font-size:.95rem}
    .chip{padding:.2rem .55rem;border-radius:999px;font-weight:800;font-size:.85rem;background:rgba(139,92,246,.12);color:#c7d2fe;border:1px solid rgba(139,92,246,.25)}

    .checklist{list-style:none;margin:10px 0 0;padding:0}
    .checklist-item{
      display:grid;grid-template-columns:1fr auto auto;gap:.5rem;align-items:center;
      padding:.6rem .6rem;border-radius:14px;border:1px solid rgba(255,255,255,.08);
      background:linear-gradient(180deg, rgba(255,255,255,.03), rgba(255,255,255,.02));
      transition:transform .2s ease, box-shadow .2s ease, background .2s ease;
    }
    .checklist-item + .checklist-item{margin-top:.25rem}
    .checklist-item:hover{transform:translateY(-2px);background:rgba(255,255,255,.05);box-shadow:0 8px 30px rgba(0,0,0,.25)}
    .checklist-item label{display:flex;align-items:flex-start;gap:.65rem;cursor:pointer}
    .checklist-item input[type="checkbox"]{width:18px;height:18px;margin:.1rem .55rem 0 0;accent-color:var(--primary)}
    .score-badge{font-weight:800;font-size:.9rem;padding:.25rem .6rem;border-radius:999px;border:1px solid rgba(255,255,255,.12);background:rgba(255,255,255,.06);color:#fff;min-width:44px;text-align:center}
    .score-good{background:rgba(16,185,129,.2); border-color:rgba(16,185,129,.4)}
    .score-mid{ background:rgba(245
