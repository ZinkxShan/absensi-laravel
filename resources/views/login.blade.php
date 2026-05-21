<!DOCTYPE html>
<html lang="id">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Absensi</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root { --bg:#0a0e1a; --card:#111827; --card2:#1a2235; --border:rgba(255,255,255,0.07); --accent:#22d3ee; --green:#4ade80; --red:#f87171; --text:#f1f5f9; --muted:#64748b; }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { background:var(--bg); font-family:'Space Grotesk',sans-serif; color:var(--text); min-height:100vh; display:flex; align-items:center; justify-content:center; }
        body::before { content:''; position:fixed; inset:0; background-image:linear-gradient(rgba(34,211,238,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,0.03) 1px,transparent 1px); background-size:40px 40px; pointer-events:none; }
        .card { background:var(--card); border:1px solid var(--border); border-radius:20px; padding:2.5rem; width:100%; max-width:400px; position:relative; overflow:hidden; }
        .card::before { content:''; position:absolute; top:0; left:0; right:0; height:2px; background:linear-gradient(90deg,transparent,var(--accent),transparent); }
        .logo { font-size:1.5rem; font-weight:700; margin-bottom:0.25rem; } 
        .logo span { color:var(--accent); }
        .subtitle { color:var(--muted); font-size:0.85rem; margin-bottom:2rem; }
        .form-group { margin-bottom:1.25rem; }
        .form-group label { display:block; font-size:0.82rem; color:var(--muted); margin-bottom:0.4rem; font-weight:500; }
        .form-group input { width:100%; padding:0.75rem 1rem; background:var(--card2); border:1px solid var(--border); border-radius:10px; color:var(--text); font-family:'Space Grotesk',sans-serif; font-size:0.9rem; outline:none; transition:border-color 0.2s; }
        .form-group input:focus { border-color:var(--accent); }
        .form-group input::placeholder { color:var(--muted); }
        .btn { width:100%; padding:0.8rem; border-radius:10px; border:none; cursor:pointer; font-family:'Space Grotesk',sans-serif; font-size:0.95rem; font-weight:600; background:linear-gradient(135deg,rgba(34,211,238,0.2),rgba(129,140,248,0.2)); color:var(--accent); border:1px solid rgba(34,211,238,0.3); transition:all 0.2s; }
        .btn:hover { background:linear-gradient(135deg,rgba(34,211,238,0.3),rgba(129,140,248,0.3)); }
        .error { background:rgba(248,113,113,0.1); color:var(--red); border:1px solid rgba(248,113,113,0.2); padding:0.65rem 0.9rem; border-radius:8px; font-size:0.85rem; margin-bottom:1rem; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">Absensi<span>.</span></div>
        <div class="subtitle">Sistem Absensi SMK Negeri 1 Tenggarong</div>

        @if($errors->has('pesan'))
            <div class="error">{{ $errors->first('pesan') }}</div>
        @endif

        <form method="POST" action="/login">
            @csrf
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" value="{{ old('username') }}" autocomplete="off" autofocus>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn">Masuk</button>
        </form>
    </div>
</body>
</html>