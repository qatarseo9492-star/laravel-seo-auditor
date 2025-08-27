<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Sign in — Semantic SEO Master Analyzer</title>
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
      --success:#5cf3b5;
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

    /* === Ultra background inspired by ref: layered beams + mesh + bokeh === */
    .bg{
      position:fixed; inset:0; z-index:-3;
      background:
        radial-gradient(900px 500px at 90% -10%, rgba(255,77,210,.14), transparent 60%),
        radial-gradient(700px 400px at 10% 0%, rgba(138,91,255,.18), transparent 55%),
        radial-gradient(900px 550px at 60% 100%, rgba(0,229,255,.14), transparent 55%),
        linear-gradient(180deg, #0a0b16 0%, #070811 100%);
      filter: saturate(110%);
    }
    .beams, .beams:before, .beams:after{
      content:""; position:fixed; inset:-20% -40%; z-index:-2;
      background:
        conic-gradient(from 0deg at 20% 40%, rgba(138,91,255,.22), transparent 70%),
        conic-gradient(from 90deg at 80% 60%, rgba(0,229,255,.18), transparent 70%),
        conic-gradient(from 180deg at 40% 70%, rgba(255,77,210,.16), transparent 70%);
      filter: blur(40px);
      animation: drift 26s ease-in-out infinite alternate;
      mix-blend-mode: screen;
    }
    .beams:before{animation-duration:32s; transform: scale(1.1)}
    .beams:after {animation-duration:38s; transform: scale(1.2)}

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

    @keyframes drift {
      0% { transform: translate(-2%, -2%) scale(1.05) rotate(0deg); }
      100%{ transform: translate(2%, 2%) scale(1.08) rotate(3deg); }
    }

    /* === Auth layout === */
    .wrap{min-height:100svh; display:grid; place-items:center; padding:32px}
    .card{
      width:min(520px, 92vw);
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
    .form{display:flex; flex-direction:column; gap:14px}
    .field{position:relative}
    .input{
      width:100%; border-radius:14px; border:1px solid var(--border);
      background: rgba(255,255,255,.06); color:var(--ink);
      padding: 14px 44px 14px 44px; font-size:1rem; outline:none;
      transition: .18s ease;
    }
    .input:focus{border-color:#a8b7ff; box-shadow:0 0 0 3px rgba(138,91,255,.25)}
    .ficon{position:absolute; left:14px; top:50%; transform:translateY(-50%); opacity:.75}
    .toggle{position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:0; color:var(--muted); cursor:pointer}
    .err{background:rgba(255,138,168,.09); border:1px solid rgba(255,138,168,.5); color:#ffd2dc; padding:10px 12px; border-radius:12px; font-size:.95rem}

    /* Check + actions */
    .row{display:flex; align-items:center; justify-content:space-between; gap:10px; margin-top:4px}
    .remember{display:flex; align-items:center; gap:.5rem; opacity:.85}
    .link{color:#a3c7ff; text-decoration:none}
    .link:hover{text-decoration:underline}

    /* Buttons */
    .btn-primary{
      width:100%;
      display:inline-flex; align-items:center; justify-content:center; gap:.6rem;
      padding:12px 14px; border-radius:14px; border:1px solid transparent; cursor:pointer;
      color:#061018; font-weight:700; letter-spacing:.15px;
      background: linear-gradient(135deg, var(--accent3), var(--accent2));
      box-shadow: 0 14px 36px rgba(255,77,210,.25);
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

    /* Footer link */
    .home{position:fixed; left:16px; bottom:16px; opacity:.75}
    .home a{color:#d9e6ff; text-decoration:none}
    .home a:hover{text-decoration:underline}

    @media (max-width:520px){
      .wrap{padding:18px}
      .title{font-size:1.4rem}
    }
  </style>
</head>
<body>
  <div class="bg"></div>
  <div class="beams"></div>
  <div class="noise"></div>

  <div class="wrap">
    <div class="card">
      <div class="brand">
        <div class="brand-badge"><i class="fa-solid fa-brain"></i></div>
        <div>
          <div class="title">Welcome back</div>
          <div class="subtitle">Sign in to use Topic Clusters & all tools</div>
        </div>
      </div>

      <form class="form" method="POST" action="{{ url('/login') }}">
        @csrf

        <div class="field">
          <i class="fa-solid fa-at ficon"></i>
          <input class="input" type="email" name="email" value="{{ old('email') }}" placeholder="you@domain.com" required>
        </div>

        <div class="field">
          <i class="fa-solid fa-key ficon"></i>
          <input class="input" id="password" type="password" name="password" placeholder="••••••••" required>
          <button type="button" class="toggle" onclick="const i=document.getElementById('password'); i.type=i.type==='password'?'text':'password';">
            <i class="fa-regular fa-eye"></i>
          </button>
        </div>

        <div class="row">
          <label class="remember">
            <input type="checkbox" name="remember"> <span>Remember me</span>
          </label>
          @if (\Illuminate\Support\Facades\Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="link">Forgot password?</a>
          @endif
        </div>

        @if ($errors->any())
          <div class="err"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif

        <button class="btn-primary" type="submit">
          <i class="fa-solid fa-right-to-bracket"></i> Sign in
        </button>

        <a class="btn-ghost" href="{{ \Illuminate\Support\Facades\Route::has('register') ? route('register') : url('/register') }}">
          <i class="fa-solid fa-user-plus"></i> Create a new account
        </a>

        <div class="switch">By continuing, you agree to our <a class="link" href="#">Terms</a> and <a class="link" href="#">Privacy</a>.</div>
      </form>
    </div>
  </div>

  <div class="home">
    <a href="{{ route('home') }}"><i class="fa-solid fa-arrow-left-long"></i> Back to home</a>
  </div>
</body>
</html>
