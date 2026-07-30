<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Claude Reader — paste, read, organize</title>
    <style>
        :root {
            --bg:#f4f5f7; --panel:#fff; --ink:#1f2328; --muted:#6b7280; --border:#e2e4e9;
            --accent:#c15f3c; --accent-soft:#f6ede8; --code-bg:#0f1115; --code-ink:#e6e8eb;
        }
        * { box-sizing:border-box; }
        body {
            margin:0; background:var(--bg); color:var(--ink);
            font:16px/1.65 -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif;
        }
        header {
            display:flex; align-items:center; gap:10px; padding:16px 24px;
            border-bottom:1px solid var(--border); background:var(--panel);
        }
        header .logo { width:30px; height:30px; border-radius:8px; background:var(--accent); color:#fff; display:grid; place-items:center; font-weight:700; }
        header strong { font-size:16px; }
        header nav { margin-left:auto; display:flex; gap:10px; }
        .btn { padding:8px 15px; border-radius:9px; font-size:14px; font-weight:600; text-decoration:none; border:1px solid var(--border); color:var(--ink); }
        .btn:hover { border-color:var(--accent); }
        .btn.primary { background:var(--accent); color:#fff; border-color:var(--accent); }

        .hero { max-width:960px; margin:0 auto; padding:72px 24px 40px; text-align:center; }
        .hero h1 { font-size:40px; line-height:1.15; margin:0 0 16px; letter-spacing:-.02em; }
        .hero p { font-size:18px; color:var(--muted); max-width:640px; margin:0 auto 28px; }
        .cta { display:flex; gap:12px; justify-content:center; }
        .cta .btn { padding:12px 22px; font-size:15px; }

        .features { max-width:960px; margin:20px auto 0; padding:0 24px 72px; display:grid; grid-template-columns:repeat(3,1fr); gap:18px; }
        .card { background:var(--panel); border:1px solid var(--border); border-radius:14px; padding:20px; }
        .card h3 { margin:0 0 6px; font-size:16px; }
        .card p { margin:0; font-size:14px; color:var(--muted); }
        .icon { font-size:22px; margin-bottom:8px; }

        .demo { max-width:720px; margin:0 auto 72px; padding:0 24px; }
        .demo pre {
            background:var(--code-bg); color:var(--code-ink); border-radius:12px; padding:18px 20px;
            overflow:auto; font:13px/1.6 ui-monospace,SFMono-Regular,Menlo,Consolas,monospace; margin:0;
        }
        footer { text-align:center; color:var(--muted); font-size:13px; padding:24px; border-top:1px solid var(--border); }

        @media (max-width:720px) { .features { grid-template-columns:1fr; } .hero h1 { font-size:30px; } }
    </style>
</head>
<body>
    <header>
        <span class="logo">C</span>
        <strong>Claude Reader</strong>
        <nav>
            @auth
                <a class="btn primary" href="{{ route('reader') }}">Open Reader</a>
            @else
                <a class="btn" href="{{ route('login') }}">Log in</a>
                <a class="btn primary" href="{{ route('register') }}">Create account</a>
            @endauth
        </nav>
    </header>

    <section class="hero">
        <h1>Paste Claude's output.<br>Read it like it was meant to be read.</h1>
        <p>
            Drop in code, tables, and ASCII diagrams from your terminal and see them
            rendered cleanly — with garbled characters automatically repaired. Save
            everything into projects and come back to it anytime.
        </p>
        <div class="cta">
            @auth
                <a class="btn primary" href="{{ route('reader') }}">Open your reader</a>
            @else
                <a class="btn primary" href="{{ route('register') }}">Get started — it's free</a>
                <a class="btn" href="{{ route('login') }}">I already have an account</a>
            @endauth
        </div>
    </section>

    <section class="features">
        <div class="card">
            <div class="icon">🎨</div>
            <h3>Clean rendering</h3>
            <p>Markdown, GFM tables, fenced code, and ASCII diagrams render exactly as intended.</p>
        </div>
        <div class="card">
            <div class="icon">🩹</div>
            <h3>Encoding repair</h3>
            <p>Those <code>‚Äî ‚Üí ¬∑</code> messes from copying out of a terminal are fixed automatically.</p>
        </div>
        <div class="card">
            <div class="icon">🗂️</div>
            <h3>Organized &amp; saved</h3>
            <p>Group pastes into projects and chats. Everything is stored to your account.</p>
        </div>
    </section>

    <section class="demo">
        <pre>┌────────────┐   paste   ┌──────────────┐
│  terminal  │ ────────► │ Claude Reader │
└────────────┘           └──────────────┘
        raw text  →  clean, saved, searchable</pre>
    </section>

    <footer>Built with Laravel &amp; Livewire.</footer>
</body>
</html>
