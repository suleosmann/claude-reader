<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Claude Reader' }}</title>
    @livewireStyles
    <style>
        html, body { height: 100%; margin: 0; }
        body { background: #f4f5f7; }
        html.cr-dark-root, html.cr-dark-root body { background: #0f1115; }
        .cr-topbar {
            height: 4rem; display: flex; align-items: center; gap: 12px; padding: 0 18px;
            background: #ffffff; border-bottom: 1px solid #e2e4e9;
            font: 14px/1.4 -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .cr-topbar .who { margin-left: auto; color: #6b7280; font-size: 13px; }
        .cr-topbar form { margin: 0; }
        .cr-logout { border: 1px solid #e2e4e9; background: #fff; color: #1f2328; padding: 6px 12px; border-radius: 8px; font-size: 13px; cursor: pointer; }
        .cr-logout:hover { border-color: #c15f3c; }
    </style>
</head>
<body class="h-full">
    <div class="cr-topbar">
        <strong>Claude Reader</strong>
        <span class="who">{{ auth()->user()?->name }}</span>
        @if (Route::has('logout'))
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="cr-logout">Log out</button>
            </form>
        @endif
    </div>

    {{ $slot }}

    @livewireScripts
</body>
</html>
