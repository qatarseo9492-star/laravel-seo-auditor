<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register • Semantic SEO</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen grid place-items-center bg-slate-900 text-slate-100">
  <form method="POST" action="{{ route('register.post') }}" class="w-full max-w-sm p-6 rounded-2xl bg-slate-800 border border-white/10">
    @csrf
    <h1 class="text-xl font-semibold">Create your account</h1>

    <label class="block mt-4 text-sm">Name</label>
    <input name="name" value="{{ old('name') }}" required class="mt-1 w-full px-3 py-2 rounded-lg bg-slate-900 border border-white/10" />
    @error('name') <div class="text-red-300 text-xs mt-1">{{ $message }}</div> @enderror

    <label class="block mt-4 text-sm">Email</label>
    <input name="email" type="email" value="{{ old('email') }}" required class="mt-1 w-full px-3 py-2 rounded-lg bg-slate-900 border border-white/10" />
    @error('email') <div class="text-red-300 text-xs mt-1">{{ $message }}</div> @enderror

    <label class="block mt-4 text-sm">Password</label>
    <input name="password" type="password" required class="mt-1 w-full px-3 py-2 rounded-lg bg-slate-900 border border-white/10" />
    @error('password') <div class="text-red-300 text-xs mt-1">{{ $message }}</div> @enderror

    <label class="block mt-4 text-sm">Confirm Password</label>
    <input name="password_confirmation" type="password" required class="mt-1 w-full px-3 py-2 rounded-lg bg-slate-900 border border-white/10" />

    <button class="mt-5 w-full px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold">Register</button>

    <p class="mt-4 text-xs text-slate-400">Already have an account?
      <a href="{{ route('login') }}" class="underline">Login</a>
    </p>
  </form>
</body>
</html>
