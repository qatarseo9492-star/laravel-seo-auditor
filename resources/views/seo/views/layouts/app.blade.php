<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'SEO Audit Tool' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8fafc;
        }
        .navbar {
            background: #0d6efd;
        }
        .navbar-brand, .nav-link, .navbar-text {
            color: #fff !important;
        }
        .card {
            border-radius: 12px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.1);
        }
        .score-badge {
            font-size: 1.2rem;
            font-weight: bold;
        }
    </style>
</head>
<body>

    {{-- Navigation Bar --}}
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">SEO Audit Tool</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a href="{{ url('/seo-audit') }}" class="nav-link">Audit</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>    

    {{-- Main Content --}}
    <main class="container py-5">
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
