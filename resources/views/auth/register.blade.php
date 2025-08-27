@extends('layouts.app')
@section('title', 'Create account')

@section('content')
<div class="auth-wrap">
  <div class="bg-aurora"></div>
  <div class="stars"></div>

  <div class="auth-card">
    <div class="auth-icon">
      <lord-icon src="https://cdn.lordicon.com/oqdmuxru.json" trigger="hover" style="width:70px;height:70px"></lord-icon>
    </div>
    <h1 class="auth-title">Create account</h1>
    <p class="auth-sub">Unlock all pro tools instantly</p>

    <form method="POST" action="{{ url('/register') }}" class="form">
      @csrf
      <label class="label">Name</label>
      <input type="text" name="name" value="{{ old('name') }}" required class="input" placeholder="Your full name">

      <label class="label mt">Email</label>
      <input type="email" name="email" value="{{ old('email') }}" required class="input" placeholder="you@domain.com">

      <label class="label mt">Password</label>
      <input type="password" name="password" required class="input" placeholder="Choose a strong password">

      <label class="label mt">Confirm Password</label>
      <input type="password" name="password_confirmation" required class="input" placeholder="Re-enter password">

      @if ($errors->any())
        <div class="err mt">
          {{ $errors->first() }}
        </div>
      @endif

      <button class="btn-primary mt-lg" type="submit">
        <i class="fa-solid fa-user-plus"></i> Create account
      </button>

      <p class="switch">Already have an account?
        <a href="{{ route('login') }}">Sign in</a>
      </p>
    </form>
  </div>
</div>
@endsection
