<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Login • Semantic SEO</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen grid place-items-center bg-slate-900 text-slate-100">
  <form method="POST" action="{{ route('login.post') }}" class="w-full max-w-sm p-6 rounded-2xl bg-slate-800 border border-white/10">
    @csrf
    <h1 class="text-xl font-semibold">Welcome back</h1>

    <label class="block mt-4 text-sm">Email</label>
    <input name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full px-3 py-2 rounded-lg bg-slate-900 border border-white/10" />
    @error('email') <div class="text-red-300 text-xs mt-1">{{ $message }}</div> @enderror

    <label class="block mt-4 text-sm">Password</label>
    <input name="password" type="password" required class="mt-1 w-full px-3 py-2 rounded-lg bg-slate-900 border border-white/10" />

    <label class="inline-flex items-center gap-2 mt-3 text-sm">
      <input type="checkbox" name="remember" class="rounded"> Remember me
    </label>

    <button class="mt-5 w-full px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold">Login</button>

    <p class="mt-4 text-xs text-slate-400">No account?
      <a href="{{ route('register') }}" class="underline">Register</a>
    </p>
  </form>
</body>
</html>
