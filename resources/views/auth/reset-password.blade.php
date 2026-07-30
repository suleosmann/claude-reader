@extends('layouts.guest')
@section('title', 'Reset password — Claude Reader')

@section('content')
    <h1>Set a new password</h1>
    <p class="sub">Choose a new password for your account.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <label for="email">Email</label>
        <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
        @error('email') <p class="err">{{ $message }}</p> @enderror

        <label for="password">New password</label>
        <input id="password" type="password" name="password" required autocomplete="new-password">
        @error('password') <p class="err">{{ $message }}</p> @enderror

        <label for="password_confirmation">Confirm password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        @error('password_confirmation') <p class="err">{{ $message }}</p> @enderror

        <button type="submit" class="btn">Reset password</button>
    </form>

    <p class="alt"><a href="{{ route('login') }}">Back to log in</a></p>
@endsection
