@extends('layouts.guest')
@section('title', 'Create account — Claude Reader')

@section('content')
    <h1>Create your account</h1>
    <p class="sub">Start saving and reading your pastes.</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <label for="name">Name</label>
        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
        @error('name') <p class="err">{{ $message }}</p> @enderror

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
        @error('email') <p class="err">{{ $message }}</p> @enderror

        <label for="password">Password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password') <p class="err">{{ $message }}</p> @enderror

        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        @error('password_confirmation') <p class="err">{{ $message }}</p> @enderror

        <button type="submit" class="btn">Create account</button>
    </form>

    <p class="alt">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
@endsection
