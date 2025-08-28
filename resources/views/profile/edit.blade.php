<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Profile • Semantic SEO</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    body{ background: radial-gradient(900px 600px at 0% 0%, rgba(139,92,246,.25), transparent 60%), radial-gradient(900px 600px at 100% 0%, rgba(96,165,250,.25), transparent 60%), #0b0f1a;}
    .glass{ background: linear-gradient(180deg, rgba(15,23,42,.65), rgba(2,6,23,.55)); border:1px solid rgba(255,255,255,.08); backdrop-filter: blur(10px); }
    .label{ font-size:.8rem; color:#cbd5e1; }
    .inp{ width:100%; background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.15); border-radius:.6rem; padding:.6rem .8rem; color:white; }
  </style>
</head>
<body class="text-slate-100 antialiased">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-bold">Profile & Settings</h1>
      <a href="{{ route('dashboard') }}" class="text-sm text-slate-300 hover:text-white">← Dashboard</a>
    </div>

    @if(session('status'))
      <div class="mt-4 p-3 rounded-lg bg-emerald-500/10 text-emerald-200 border border-emerald-500/30">
        {{ session('status') }}
      </div>
    @endif

    <div class="grid md:grid-cols-3 gap-6 mt-6">
      <!-- Avatar -->
      <div class="glass rounded-2xl p-6 border border-white/10">
        <h2 class="font-semibold">Profile Picture</h2>
        <div class="mt-4">
          @php $avatar = auth()->user()->avatar_path ? asset('storage/'.auth()->user()->avatar_path) : null; @endphp
          @if($avatar)
            <img src="{{ $avatar }}" class="h-24 w-24 rounded-full object-cover border border-white/20" alt="avatar">
          @else
            <div class="h-24 w-24 rounded-full grid place-items-center bg-gradient-to-br from-fuchsia-500 to-sky-500">
              <span class="text-xl font-bold">{{ strtoupper(mb_substr(auth()->user()->name,0,1)) }}</span>
            </div>
          @endif
        </div>
        <form class="mt-4" method="POST" action="{{ route('profile.avatar') }}" enctype="multipart/form-data">
          @csrf
          <label class="label">Upload new avatar</label>
          <input type="file" name="avatar" accept="image/*" class="mt-1 block text-sm">
          @error('avatar')<div class="text-red-300 text-xs mt-1">{{ $message }}</div>@enderror
          <button class="mt-3 px-3 py-2 rounded-lg bg-white text-slate-900 font-semibold">Save Avatar</button>
        </form>
      </div>

      <!-- Basic info -->
      <div class="glass rounded-2xl p-6 border border-white/10 md:col-span-2">
        <h2 class="font-semibold">Profile</h2>
        <form class="mt-4 grid sm:grid-cols-2 gap-4" method="POST" action="{{ route('profile.update') }}">
          @csrf
          <div>
            <label class="label">Name</label>
            <input name="name" class="inp mt-1" value="{{ old('name', auth()->user()->name) }}" required>
            @error('name')<div class="text-red-300 text-xs mt-1">{{ $message }}</div>@enderror
          </div>
          <div>
            <label class="label">Email</label>
            <input class="inp mt-1 opacity-70" value="{{ auth()->user()->email }}" disabled>
          </div>
          <div class="sm:col-span-2">
            <button class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold">Save Changes</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Password -->
    <div class="glass rounded-2xl p-6 border border-white/10 mt-6">
      <h2 class="font-semibold">Change Password</h2>
      <form class="mt-4 grid sm:grid-cols-3 gap-4" method="POST" action="{{ route('profile.password') }}">
        @csrf
        <div>
          <label class="label">Current Password</label>
          <input type="password" name="current_password" class="inp mt-1" required>
          @error('current_password')<div class="text-red-300 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="label">New Password</label>
          <input type="password" name="password" class="inp mt-1" required>
          @error('password')<div class="text-red-300 text-xs mt-1">{{ $message }}</div>@enderror
        </div>
        <div>
          <label class="label">Confirm New Password</label>
          <input type="password" name="password_confirmation" class="inp mt-1" required>
        </div>
        <div class="sm:col-span-3">
          <button class="px-4 py-2 rounded-lg bg-white text-slate-900 font-semibold">Update Password</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
