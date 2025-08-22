{{-- resources/views/home.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Semantic SEO Master • Dark Purple + Red</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"/>

  <style>
    :root{
      --bg:#090a12;
      --panel:#0e0f1a;
      --panel-2:#101226;
      --line:#1d2033;
      --text:#e7e9f3;
      --text-dim:#a6acc7;
      --text-muted:#98a1c0;
      --primary:#8b5cf6;   /* purple */
      --accent:#ef4444;    /* red accent */
      --cyan:#22d3ee;      /* cyan */
      --good:#16a34a;      /* success */
      --warn:#efb710;
      --bad:#ef4444;

      --radius:16px;
      --shadow:0 6px 24px rgba(0,0,0,.45);
      --shadow-hover:0 12px 34px rgba(0,0,0,.55);
      --transition:.25s ease;
      --container:1200px;
    }
    *{box-sizing:border-box}
    html,body{height:100%}
    body{
      margin:0;color:var(--text);
      font-family:ui-sans-serif,system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Cantarell,"Noto Sans","Helvetica Neue",Arial;
      background: radial-gradient(1200px 800px at 10% -10%, #111132 0%, transparent 60%),
                  radial-gradient(1000px 700px at 120% 10%, #130f22 0%, transparent 55%),
                  var(--bg);
      overflow-x:hidden;
      -webkit-font-smoothing:antialiased; -moz-osx-font-smoothing:grayscale;
    }
    a{color:var(--cyan);text-decoration:none}
    a:hover{opacity:.92}

    /* ====== Purple + Red Smoke (animated) ====== */
    .bg-smoke{position:fixed;inset:0;z-index:0;pointer-events:none;overflow:hidden}
    .blob{
      position:absolute; width:70vmax; height:70vmax; border-radius:50%;
      filter: blur(70px); mix-blend-mode: screen; opacity:.65; will-change: transform;
      animation: float 34s linear infinite;
    }
    .purple-1{top:-18%; left:-20%; background: radial-gradient(closest-side, rgba(139,92,246,.35), rgba(139,92,246,0) 70%); animation-duration:36s;}
    .purple-2{bottom:-25%; right:-10%; background: radial-gradient(closest-side, rgba(168,85,247,.30), rgba(168,85,247,0) 70%); animation-direction:reverse; animation-duration:30s;}
    .red-1{top:5%; right:10%; background: radial-gradient(closest-side, rgba(239,68,68,.28), rgba(239,68,68,0) 72%); animation-duration:27s;}
    .red-2{bottom:10%; left:15%; background: radial-gradient(closest-side, rgba(244,63,94,.22), rgba(244,63,94,0) 70%); animation-duration:32s;}
    @keyframes float{
      0%{ transform: translate3d(0,0,0) rotate(0deg) }
      25%{ transform: translate3d(6%, -4%, 0) rotate(45deg) }
      50%{ transform: translate3d(-4%, 6%, 0) rotate(100deg) }
      75%{ transform: translate3d(-8%, -6%, 0) rotate(165deg) }
      100%{ transform: translate3d(0,0,0) rotate(360deg) }
    }
    .veil{position:fixed;inset:0;z-index:1;pointer-events:none; background:
      radial-gradient(900px 700px at 0% 0%, rgba(139,92,246,.07), transparent 50%),
      radial-gradient(900px 700px at 100% 10%, rgba(239,68,68,.06), transparent 45%);
    }

    /* Layout */
    .wrap{position:relative;z-index:2;max-width:var(--container);margin:0 auto;padding:28px 5%}
    header.site{display:flex;align-items:center;justify-content:space-between;padding:14px 0 24px;border-bottom:1px solid var(--line)}
    .brand{display:flex;align-items:center;gap:.75rem}
    .brand-badge{width:44px;height:44px;border-radius:12px;display:grid;place-items:center;background:linear-gradient(135deg, rgba(139,92,246,.22), rgba(239,68,68,.22));border:1px solid rgba(255,255,255,.06);color:#fda4af}
    .brand h1{font-size:1.12rem;margin:0}
    .brand small{display:block;color:var(--text-dim)}
    .nav-actions{display:flex;gap:.5rem}
    .btn{border-radius:12px;border:1px solid var(--line);background:rgba(255,255,255,.03);color:var(--text);padding:.6rem .9rem;font-weight:600;cursor:pointer;transition:var(--transition)}
    .btn:hover{transform:translateY(-1px);background:rgba(255,255,255,.06)}
    .btn-primary{background:linear-gradient(135deg,var(--primary),#6d28d9);border-color:transparent}
    .btn-outline{background:transparent}
    .btn-danger{background:linear-gradient(135deg,#dc2626,#ef4444);border-color:transparent}

    /* Hero */
    .hero{display:grid;grid-template-columns:1.1fr .9fr;gap:1.5rem;align-items:center;margin:34px 0 18px;}
    .hero-card{background:linear-gradient(180deg, rgba(139,92,246,.10), rgba(239,68,68,.08));border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:28px;box-shadow:var(--shadow)}
    .hero h2{margin:0 0 .6rem;font-size:1.9rem}
    .hero p{margin:0;color:var(--text-muted)}
    .hero-badge{display:inline-flex;gap:.5rem;align-items:center;font-weight:700;color:#fca5a5}
    .stat{display:flex;gap:1rem;margin-top:1rem;color:var(--text-dim)}
    .stat b{color:var(--text)}
    .side{background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:20px;padding:24px;box-shadow:var(--shadow)}

    /* Analyzer */
    .analyzer{margin-top:22px;background:var(--panel);border:1px solid rgba(255,255,255,.06);border-radius:20px;box-shadow:var(--shadow);padding:28px}
    .section-title{font-size:1.5rem;margin:0 0 .3rem}
    .section-subtitle{margin:0;color:var(--text-dim)}
    .analyze-form input[type="url"]{width:100%;padding:.7rem .9rem;border-radius:12px;border:1px solid var(--line);background:#0b0d1d;color:var(--text)}
    .analyze-form input[type="url"]::placeholder{color:#6b7280}

    .overall-score{display:flex;align-items:center;gap:.6rem;margin-top:.5rem}
    .score-badge{padding:.3rem .65rem;border-radius:999px;font-weight:800;border:1px solid rgba(255,255,255,.10)}
    .score-badge.good{background:rgba(22,163,74,.18);color:#86efac;border-color:rgba(22,163,74,.35)}
    .score-badge.mid{background:rgba(239,183,16,.18);color:#fde68a;border-color:rgba(239,183,16,.35)}
    .score-badge.bad{background:rgba(239,68,68,.18);color:#fecaca;border-color:rgba(239,68,68,.35)}

    .progress-wrap{margin-top:1rem;background:rgba(255,255,255,.02);border:1px solid rgba(255,255,255,.06);border-radius:16px;padding:14px;position:relative}
    .progress-meta{display:flex;justify-content:space-between;align-items:center;margin-bottom:.5rem}
    .progress-label{font-weight:700;color:var(--text)}
    .progress-percent{font-weight
