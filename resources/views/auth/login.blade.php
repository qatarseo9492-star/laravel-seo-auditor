@extends('layouts.app')
@section('title', 'Sign in')

@section('content')
<div class="auth-wrap">
  <div class="bg-aurora"></div>
  <div class="stars"></div>

  <div class="auth-card">
    <div class="auth-icon">
      <lord-icon src="https://cdn.lordicon.com/kthelypq.json" trigger="loop" delay="1500" style="width:70px;height:70px"></lord-icon>
    </div>
    <h1 class="auth-title">Welcome back</h1>
    <p class="auth-sub">Sign in to use Topic Clusters & more tools</p>

    <form method="POST" action="{{ url('/login') }}" class="form">
      @csrf
      <label class="label">Email</label>
      <input type="email" name="email" value="{{ old('email') }}" required class="input" placeholder="you@domain.com">

      <label class="label mt">Password</label>
      <input type="password" name="password" required class="input" placeholder="••••••••">

      <label class="remember">
        <input type="checkbox" name="remember"> <span>Remember me</span>
      </label>

      @error('email') <div class="err">{{ $message }}</div> @enderror
      @error('password') <div class="err">{{ $message }}</div> @enderror

      <button class="btn-primary mt-lg" type="submit">
        <i class="fa-solid fa-right-to-bracket"></i> Sign in
      </button>

      <p class="switch">No account?
        <a href="{{ route('register') }}">Create one</a>
      </p>
    </form>
  </div>
</div>
@endsection
