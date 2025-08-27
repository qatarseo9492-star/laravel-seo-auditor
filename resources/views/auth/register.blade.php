<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Create account — Semantic SEO Master Analyzer</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Icons & animated icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
  <script src="https://cdn.lordicon.com/lordicon.js"></script>

  <style>
    :root{
      --ink:#eaf1ff;
      --muted:#aab8e0;
      --bg:#090a12;
      --panel: rgba(255,255,255,.06);
      --border: rgba(255,255,255,.14);
      --accent1:#8a5bff;
      --accent2:#00e5ff;
      --accent3:#ff4dd2;
      --accent4:#33ffaa;
      --danger:#ff8aa8;
    }
    *{box-sizing:border-box}
    html,body{margin:0;padding:0}
    body{
      min-height:100vh; color:var(--ink);
      font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Inter, Arial;
      background: var(--bg);
      overflow-x:hidden;
    }

    /* Background inspired by ref: neon mesh + sweeping beams */
    .bg{
      position:fixed; inset:0; z-index:-3;
      background:
        radial-gradient(900px 500px at 85% 10%, rgba(51,255,170,.16), transparent 55%),
        radial-gradient(900px 500px at 15% 90%, rgba(0,229,255,.16), transparent 55%),
        radial-gradient(700px 400px at 50% 0%, rgba(138,91,255,.18), transparent 55%),
        linear-gradient(180deg, #0a0b16 0%, #070811 100%);
    }
    .sweep:before, .sweep:after{
      content:""; position:fixed; inset:-10% -30%; z-index:-2;
      background:
        conic-gradient(from 0deg at 30% 30%, rgba(138,91,255,.20), transparent 70%),
        conic-gradient(from 90deg at 70% 70%, rgba(51,255,170,.18), transparent 70%),
        conic-gradient(from 180deg at 20% 80%, rgba(255,77,210,.16), transparent 70%);
      filter: blur(42px);
      animation: sweep 28s ease-in-out infinite alternate;
      mix-blend-mode: screen;
    }
    .sweep:after{animation-duration: 34s; transform: scale(1.1)}

    .noise{
      position:fixed; inset:0; z-index:-1; pointer-events:none; opacity:.035;
      background-image: url('data:image/svg+xml;utf8,\
        <svg xmlns="http://www.w3.org/2000/svg" width=\"1200\" height=\"600\" viewBox=\"0 0 1200 600\">\
          <filter id=\"n\">\
            <feTurbulence type=\"fractalNoise\" baseFrequency=\"0.95\" numOctaves=\"2\" stitchTiles=\"stitch\"/>\
            <feColorMatrix type=\"saturate\" values=\"0\"/>\
          </filter>\
          <rect width=\"1200\" height=\"600\" filter=\"url(%23n)\" opacity=\"1\"/>\
        </svg>');
      background-size: 300px 150px;
    }

    @keyframes sweep {
      0% { transform: translate(-1%, -1%) scale(1.04) rotate(0deg); }
      100%{ transform: translate(1%, 1%) scale(1.08) rotate(2deg); }
    }

    /* Auth layout */
    .wrap{min-height:100svh; display:grid; place-items:center; padding:32px}
    .card{
      width:min(560px, 92vw);
      border-radius:22px; border:1px solid var(--border);
      background: linear-gradient(180deg, rgba(255,255,255,.06), rgba(255,255,255,.04));
      box-shadow: 0 25px 70px rgba(0,0,0,.45), inset 0 0 0 1px rgba(255,255,255,.06);
      backdrop-filter: blur(12px);
      padding:28px 26px;
    }
    .brand{display:flex; align-items:center; gap:.8rem; margin-bottom:6px}
    .brand-badge{width:42px;height:42px;border-radius:14px;display:grid;place-items:center;color:#061018;
      background: linear-gradient(135deg, var(--accent1), var(--accent2)); box-shadow:0 10px 30px rgba(0,229,255,.28)}
    .brand-badge i{font-size:1.1rem}
    .title{margin:4px 0 4px 0; font-size:1.6rem; font-weight:800}
    .subtitle{margin:0 0 14px 0; opacity:.75}

    /* Inputs */
    .form{display:grid; grid-template-columns: 1fr; gap:14px}
    .row2{display:grid; grid-template-columns: 1fr 1fr; gap:12px}
    @media (max-width:560px){ .row2{grid-template-columns: 1fr} }
    .field{position:relative}
    .input{
      width:100%; border-radius:14px; border:1px solid var(--border);
      background: rgba(255,255,255,.06); color:var(--ink);
      padding: 14px 44px 14px 44px; font-size:1rem; outline:none;
      transition: .18s ease;
    }
    .input:focus{border-color:#a8f5d0; box-shadow:0 0 0 3px rgba(51,255,170,.25)}
    .ficon{position:absolute; left:14px; top:50%; transform:translateY(-50%); opacity:.75}
    .toggle{position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:0; color:var(--muted); cursor:pointer}

    .err{background:rgba(255,138,168,.09); border:1px solid rgba(255,138,168,.5); color:#ffd2dc; padding:10px 12px; border-radius:12px; font-size:.95rem}

    /* Buttons */
    .btn-primary{
      width:100%;
      display:inline-flex; align-items:center; justify-content:center; gap:.6rem;
      padding:12px 14px; border-radius:14px; border:1px solid transparent; cursor:pointer;
      color:#061018; font-weight:700; letter-spacing:.15px;
      background: linear-gradient(135deg, var(--accent4), var(--accent2));
      box-shadow: 0 14px 36px rgba(51,255,170,.25);
      transition:.18s ease;
    }
    .btn-primary:hover{transform:translateY(-1px)}
    .btn-ghost{
      width:100%; margin-top:10px;
      display:inline-flex; align-items:center; justify-content:center; gap:.6rem;
      padding:12px 14px; border-radius:14px; border:1px solid var(--border); cursor:pointer;
      background:var(--panel); color:var(--ink);
    }

    .switch{margin-top:12px; text-align:center; opacity:.85}
    .home{position:fixed; left:16px; bottom:16px; opacity:.75}
    .home a{color:#d9e6ff; text-decoration:none}
    .home a:hover{text-decoration:underline}
  </style>
</head>
<body>
  <div class="bg"></div>
  <div class="sweep"></div>
  <div class="noise"></div>

  <div class="wrap">
    <div class="card">
      <div class="brand">
        <div class="brand-badge"><i class="fa-solid fa-user-plus"></i></div>
        <div>
          <div class="title">Create your account</div>
          <div class="subtitle">Join to unlock Topic Clusters & pro tools</div>
        </div>
      </div>

      <form class="form" method="POST" action="{{ url('/register') }}">
        @csrf

        <div class="field">
          <i class="fa-solid fa-id-card ficon"></i>
          <input class="input" type="text" name="name" value="{{ old('name') }}" placeholder="Your full name" required>
        </div>

        <div class="field">
          <i class="fa-solid fa-at ficon"></i>
          <input class="input" type="email" name="email" value="{{ old('email') }}" placeholder="you@domain.com" required>
        </div>

        <div class="row2">
          <div class="field">
            <i class="fa-solid fa-key ficon"></i>
            <input class="input" id="password" type="password" name="password" placeholder="Choose a strong password" required>
            <button type="button" class="toggle" onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password';">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>

          <div class="field">
            <i class="fa-solid fa-shield-halved ficon"></i>
            <input class="input" id="password_confirmation" type="password" name="password_confirmation" placeholder="Confirm password" required>
            <button type="button" class="toggle" onclick="const i=document.getElementById('password_confirmation'); i.type=i.type==='password'?'text':'password';">
              <i class="fa-regular fa-eye"></i>
            </button>
          </div>
        </div>

        @if ($errors->any())
          <div class="err"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif

        <button class="btn-primary" type="submit">
          <i class="fa-solid fa-user-plus"></i> Create account
        </button>

        <a class="btn-ghost" href="{{ \Illuminate\Support\Facades\Route::has('login') ? route('login') : url('/login') }}">
          <i class="fa-solid fa-right-to-bracket"></i> I already have an account
        </a>

        <div class="switch">By creating an account, you agree to our <a class="link" href="#">Terms</a> and <a class="link" href="#">Privacy</a>.</div>
      </form>
    </div>
  </div>

  <div class="home">
    <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left-long"></i> Back to home</a>
  </div>
</body>
</html>
