<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>@yield('title','Semantic SEO Master Analyzer 2.0')</title>

  <!-- Inter + Tailwind CDN -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Inter','ui-sans-serif','system-ui'] },
          colors: {
            brand: {
              50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',
              500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81'
            }
          }
        }
      }
    }
  </script>
  <style>html{scroll-behavior:smooth}</style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased">

  <!-- Top Menu Bar (above header) -->
  <nav class="w-full border-b border-slate-200 bg-white/90 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="h-14 flex items-center justify-between">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-semibold">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-brand-600" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 5v10h-2V7h2zm-1 12a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5z"/></svg>
          <span>Semantic SEO Master Analyzer 2.0</span>
        </a>

        <div class="hidden md:flex items-center gap-6">
          <a href="{{ url('/semantic-analyzer') }}" class="hover:text-brand-700">Semantic Analyzer</a>
          <a href="{{ url('/ai-content-checker') }}" class="hover:text-brand-700">AI Content Checker</a>
          <a href="{{ url('/topic-cluster') }}" class="hover:text-brand-700">Topic Analysis</a>
        </div>

        <div class="flex items-center gap-3">
          <a href="{{ url('/login') }}" class="text-sm px-3 py-1.5 rounded-lg border border-slate-300 hover:bg-slate-100">Login</a>
          <a href="{{ url('/register') }}" class="text-sm px-3 py-1.5 rounded-lg bg-brand-600 text-white hover:bg-brand-700">Signup</a>
        </div>
      </div>
    </div>
  </nav>

  @yield('content')

  <footer class="border-t mt-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 text-sm text-slate-600">
      <p>© {{ date('Y') }} Semantic SEO Master Analyzer 2.0 · Built for speed, clarity and action.</p>
    </div>
  </footer>
</body>
</html>
