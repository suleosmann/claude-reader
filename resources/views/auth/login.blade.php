@extends('layouts.guest')
@section('title', 'Log in — Claude Reader')

@section('content')
    <h1>Welcome back</h1>
    <p class="sub">Log in to your reader.</p>

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
        @error('email') <p class="err">{{ $message }}</p> @enderror

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="current-password">
        @error('password') <p class="err">{{ $message }}</p> @enderror

        <div class="row">
            <label class="remember"><input type="checkbox" name="remember"> Remember me</label>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Forgot password?</a>
            @endif
        </div>

        <button type="submit" class="btn">Log in</button>
    </form>

    @if (Route::has('register'))
        <p class="alt">New here? <a href="{{ route('register') }}">Create an account</a></p>
    @endif
@endsection
