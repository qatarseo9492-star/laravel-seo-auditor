@extends('layouts.app')
@section('title','Edit Profile')

@section('content')
<div class="space-y-8">
  @if(session('ok'))
    <div class="pill" style="background:rgba(34,197,94,.18);color:#bbf7d0">{{ session('ok') }}</div>
  @endif
  @if($errors->any())
    <div class="pill" style="background:rgba(239,68,68,.18);color:#fecaca">
      @foreach($errors->all() as $e) • {{ $e }} @endforeach
    </div>
  @endif

  <div class="card">
    <h2 class="t-grad" style="font-weight:900">Profile</h2>
    <form action="{{ route('profile.update') }}" method="post" class="space-y-3">@csrf
      <div><label>Name</label><input name="name" value="{{ old('name',$user->name) }}" class="w-full bg-transparent pill" required></div>
      <div><label>Email</label><input name="email" value="{{ old('email',$user->email) }}" class="w-full bg-transparent pill" required></div>
      <button class="pill" style="background:#22c55e;color:#05240f">Save</button>
    </form>
  </div>

  <div class="card">
    <h2 class="t-grad" style="font-weight:900">Change Password</h2>
    <form action="{{ route('profile.password') }}" method="post" class="space-y-3">@csrf
      <div><label>Current password</label><input type="password" name="current_password" class="w-full bg-transparent pill" required></div>
      <div><label>New password</label><input type="password" name="password" class="w-full bg-transparent pill" required></div>
      <div><label>Confirm password</label><input type="password" name="password_confirmation" class="w-full bg-transparent pill" required></div>
      <button class="pill" style="background:#3b82f6;color:#051227">Update Password</button>
    </form>
  </div>

  <div class="card">
    <h2 class="t-grad" style="font-weight:900">Avatar</h2>
    <form action="{{ route('profile.avatar') }}" method="post" enctype="multipart/form-data" class="space-y-3">@csrf
      <div style="display:flex;align-items:center;gap:12px">
        @php
          $cand = $user->profile_photo_url ?? $user->avatar_url ?? $user->avatar ?? null;
          if ($cand && str_starts_with($cand,'http')) { $avatarUrl = $cand; }
          elseif ($cand) { try { $avatarUrl = \Illuminate\Support\Facades\Storage::url($cand); } catch (\Throwable $e) { $avatarUrl = $cand; } }
          else { $avatarUrl = null; }
        @endphp
        <span class="avatar-wrap" style="width:56px;height:56px">
          <span class="avatar-ring" style="inset:-4px"></span>
          @if($avatarUrl) <img src="{{ $avatarUrl }}" style="width:56px;height:56px;border-radius:9999px;object-fit:cover"> @else
            <span class="avatar-fallback" style="width:56px;height:56px">{{ mb_strtoupper(mb_substr($user->name,0,1)) }}</span>
          @endif
        </span>
        <input type="file" name="avatar" accept="image/*" class="pill" required>
      </div>
      <button class="pill" style="background:#a78bfa;color:#1a063b">Upload</button>
    </form>
  </div>
</div>
@endsection
