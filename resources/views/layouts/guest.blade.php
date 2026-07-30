<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Claude Reader')</title>
    <style>
        :root {
            --bg:#f4f5f7; --panel:#fff; --ink:#1f2328; --muted:#6b7280; --border:#e2e4e9;
            --accent:#c15f3c; --accent-soft:#f6ede8; --danger:#b3261e;
        }
        * { box-sizing:border-box; }
        body {
            margin:0; min-height:100vh; background:var(--bg); color:var(--ink);
            font:15px/1.6 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
            display:grid; place-items:center; padding:32px 16px;
        }
        .auth-card {
            width:100%; max-width:400px; background:var(--panel); border:1px solid var(--border);
            border-radius:16px; padding:28px; box-shadow:0 10px 40px rgba(0,0,0,.06);
        }
        .brand { display:flex; align-items:center; gap:10px; margin-bottom:6px; text-decoration:none; color:var(--ink); }
        .brand .logo { width:30px; height:30px; border-radius:8px; background:var(--accent); color:#fff; display:grid; place-items:center; font-weight:700; }
        .brand strong { font-size:17px; }
        h1 { font-size:19px; margin:14px 0 2px; }
        .sub { color:var(--muted); font-size:13.5px; margin:0 0 18px; }
        label { display:block; font-size:13px; font-weight:600; margin:14px 0 5px; }
        input[type=text], input[type=email], input[type=password] {
            width:100%; padding:10px 12px; border:1px solid var(--border); border-radius:9px;
            font-size:14px; background:#fff; color:var(--ink); outline:none;
        }
        input:focus { border-color:var(--accent); box-shadow:0 0 0 3px var(--accent-soft); }
        .row { display:flex; align-items:center; justify-content:space-between; margin-top:14px; font-size:13px; }
        .remember { display:flex; align-items:center; gap:7px; color:var(--muted); font-weight:500; }
        .btn {
            width:100%; margin-top:18px; padding:11px 14px; border:0; border-radius:10px;
            background:var(--accent); color:#fff; font-size:14px; font-weight:600; cursor:pointer;
        }
        .btn:hover { filter:brightness(1.05); }
        a { color:var(--accent); text-decoration:none; }
        a:hover { text-decoration:underline; }
        .alt { text-align:center; margin-top:18px; font-size:13.5px; color:var(--muted); }
        .err { color:var(--danger); font-size:12.5px; margin:5px 0 0; }
        .status { background:var(--accent-soft); color:var(--accent); border-radius:9px; padding:10px 12px; font-size:13px; margin-bottom:16px; }
    </style>
</head>
<body>
    <div class="auth-card">
        <a class="brand" href="/">
            <span class="logo">C</span>
            <strong>Claude Reader</strong>
        </a>
        @yield('content')
    </div>
</body>
</html>
