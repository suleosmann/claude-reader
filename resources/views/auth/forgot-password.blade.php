@extends('layouts.guest')
@section('title', 'Forgot password — Claude Reader')

@section('content')
    <h1>Forgot your password?</h1>
    <p class="sub">Enter your email and we'll send you a reset link.</p>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email') <p class="err">{{ $message }}</p> @enderror

        <button type="submit" class="btn">Email password reset link</button>
    </form>

    <p class="alt"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection
