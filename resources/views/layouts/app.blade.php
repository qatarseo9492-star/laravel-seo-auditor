<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SEO Audit Tool' }}</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f7fb;
            color: #333;
        }
        .navbar {
            background: linear-gradient(90deg, #0d6efd, #00b4d8);
        }
        .navbar-brand, .nav-link {
            color: #fff !important;
            font-weight: 500;
        }
        .btn-gradient {
            background: linear-gradient(90deg, #0d6efd, #00b4d8);
            color: #fff;
            border: none;
        }
        .btn-gradient:hover {
            background: linear-gradient(90deg, #0b5ed7, #0096c7);
        }
        .bg-gradient {
            background: linear-gradient(90deg, #0d6efd, #00b4d8);
        }
        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.05);
        }
        footer {
            margin-top: 50px;
            background: #fff;
            border-top: 1px solid #ddd;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">SEO Audit Tool</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="{{ url('/') }}" class="nav-link">Home</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('/seo-audit') }}" class="nav-link">Audit</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>    

    {{-- Main Body --}}
    <main class="container py-5">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer>
        <p>© {{ date('Y') }} SEO Audit Tool • Crafted with ❤️ in Laravel + Bootstrap</p>
    </footer>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
