<!DOCTYPE html>
<html lang="id">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pengaturan Hari Libur</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root { --bg:#0a0e1a;--card:#111827;--card2:#1a2235;--border:rgba(255,255,255,0.07);--accent:#22d3ee;--accent2:#818cf8;--green:#4ade80;--red:#f87171;--text:#f1f5f9;--muted:#64748b; }
    *{margin:0;padding:0;box-sizing:border-box;}
    body{background:var(--bg);font-family:'Space Grotesk',sans-serif;color:var(--text);}
    body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(34,211,238,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,0.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;}
    nav{display:flex;align-items:center;justify-content:space-between;padding:1rem 2rem;border-bottom:1px solid var(--border);background:rgba(10,14,26,0.8);backdrop-filter:blur(12px);position:sticky;top:0;z-index:10;}
    .nav-logo{font-weight:700;font-size:1.1rem;}.nav-logo span{color:var(--accent);}
    .nav-links{display:flex;gap:0.5rem;}
    .nav-links a{padding:0.4rem 1rem;border-radius:8px;color:var(--muted);text-decoration:none;font-size:0.9rem;font-weight:500;transition:all 0.2s;}
    .nav-links a:hover{color:var(--text);background:var(--card2);}
    .nav-links a.active{color:var(--accent);background:rgba(34,211,238,0.1);}
    .nav-links a.masuk{color:var(--green)}.nav-links a.keluar{color:#60a5fa}
    .container{max-width:1000px;margin:0 auto;padding:2rem 1.5rem;}
    h1{font-size:1.5rem;font-weight:700;margin-bottom:2rem;}
    .grid-2{display:grid;grid-template-columns:340px 1fr;gap:1.5rem;align-items:start;}
    @media(max-width:768px){.grid-2{grid-template-columns:1fr;}}
    .panel{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.5rem;}
    .panel-title{font-size:0.78rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:1.25rem;}
    .form-group{margin-bottom:1rem;}
    .form-group label{display:block;font-size:0.82rem;color:var(--muted);margin-bottom:0.4rem;font-weight:500;}
    .form-group input{width:100%;padding:0.65rem 0.9rem;background:var(--card2);border:1px solid var(--border);border-radius:10px;color:var(--text);font-family:'Space Grotesk',sans-serif;font-size:0.9rem;outline:none;transition:border-color 0.2s;}
    .form-group input:focus{border-color:var(--accent);}
    .form-group input::placeholder{color:var(--muted);}
    .btn{width:100%;padding:0.75rem;border-radius:10px;border:none;cursor:pointer;font-family:'Space Grotesk',sans-serif;font-size:0.9rem;font-weight:600;transition:all 0.2s;}
    .btn-primary{background:linear-gradient(135deg,rgba(34,211,238,0.2),rgba(129,140,248,0.2));color:var(--accent);border:1px solid rgba(34,211,238,0.3);}
    .btn-primary:hover{background:linear-gradient(135deg,rgba(34,211,238,0.3),rgba(129,140,248,0.3));}
    .notif{padding:0.65rem 0.9rem;border-radius:8px;font-size:0.85rem;margin-bottom:1rem;display:none;}
    .notif.ok{background:rgba(74,222,128,0.1);color:var(--green);border:1px solid rgba(74,222,128,0.2);}
    .notif.err{background:rgba(248,113,113,0.1);color:var(--red);border:1px solid rgba(248,113,113,0.2);}
    .info-box{background:rgba(34,211,238,0.05);border:1px solid rgba(34,211,238,0.1);border-radius:10px;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.85rem;color:var(--muted);}
    .tabel-wrap{overflow-y:auto;max-height:480px;}
    table{width:100%;border-collapse:collapse;font-size:0.875rem;}
    th{font-size:0.72rem;letter-spacing:1px;text-transform:uppercase;color:var(--muted);font-weight:600;padding:0 0 0.75rem;text-align:left;border-bottom:1px solid var(--border);position:sticky;top:0;background:var(--card);}
    td{padding:0.65rem 0;border-bottom:1px solid var(--border);vertical-align:middle;}
    tr:last-child td{border-bottom:none;}
    .btn-small{padding:0.3rem 0.65rem;border-radius:6px;font-size:0.78rem;font-family:'Space Grotesk',sans-serif;font-weight:500;cursor:pointer;border:none;transition:all 0.15s;}
    .btn-hapus{background:rgba(248,113,113,0.12);color:var(--red);}
    .btn-hapus:hover{background:rgba(248,113,113,0.22);}
    .empty-state{color:var(--muted);font-size:0.9rem;text-align:center;padding:2rem;}
  </style>
</head>
<body>
  <nav>
    <div class="nav-logo">Absensi<span>.</span></div>
    <div class="nav-links">
      <a href="/masuk" class="masuk">Scan Masuk</a>
      <a href="/keluar" class="keluar">Scan Keluar</a>
      <a href="/dashboard">Dashboard</a>
      <a href="/kelola">Kelola Siswa</a>
      <a href="/kelola-user">Kelola User</a>
      <a href="/hari-libur" class="active">Hari Libur</a>
    </div>
  </nav>

  <div class="container">
    <h1>Pengaturan Hari Libur</h1>
    <div class="grid-2">
      <div class="panel">
        <div class="panel-title">Tambah Hari Libur</div>
        <div class="notif" id="notif-libur"></div>
        <div class="info-box">
          Sabtu & Minggu otomatis libur. Tambahkan hari libur khusus di sini.
        </div>
        <div class="form-group">
          <label>Tanggal</label>
          <input type="date" id="hl-tanggal">
        </div>
        <div class="form-group">
          <label>Keterangan</label>
          <input type="text" id="hl-keterangan" placeholder="contoh: Idul Fitri, HUT RI, Libur Semester">
        </div>
        <button class="btn btn-primary" onclick="tambahHariLibur()">+ Tambah Hari Libur</button>
      </div>

      <div class="panel">
        <div class="panel-title">Daftar Hari Libur Khusus</div>
        <div class="tabel-wrap">
          <table>
            <thead><tr><th>Tanggal</th><th>Keterangan</th><th></th></tr></thead>
            <tbody id="tbody-libur"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    async function loadHariLibur() {
        const res = await fetch('/api/hari-libur');
        const list = await res.json();
        renderHariLibur(list);
    }

    function renderHariLibur(list) {
        const tbody = document.getElementById('tbody-libur');
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="3" class="empty-state">Belum ada hari libur khusus</td></tr>';
            return;
        }
        tbody.innerHTML = list.map(h => `
        <tr>
            <td style="font-family:'JetBrains Mono',monospace;font-size:0.82rem;color:var(--accent)">${h.tanggal}</td>
            <td>${h.keterangan}</td>
            <td><button class="btn-small btn-hapus" onclick="hapusHariLibur(${h.id},'${h.keterangan}')">Hapus</button></td>
        </tr>`).join('');
    }

    async function tambahHariLibur() {
        const tanggal    = document.getElementById('hl-tanggal').value;
        const keterangan = document.getElementById('hl-keterangan').value.trim();
        const notif      = document.getElementById('notif-libur');

        const res = await fetch('/api/hari-libur', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ tanggal, keterangan })
        });
        const d = await res.json();
        notif.className = 'notif ' + (d.status === 'berhasil' ? 'ok' : 'err');
        notif.textContent = d.pesan;
        notif.style.display = 'block';
        setTimeout(() => notif.style.display = 'none', 3000);
        if (d.status === 'berhasil') {
            document.getElementById('hl-tanggal').value = '';
            document.getElementById('hl-keterangan').value = '';
            loadHariLibur();
        }
    }

    async function hapusHariLibur(id, keterangan) {
        if (!confirm(`Hapus hari libur "${keterangan}"?`)) return;
        await fetch(`/api/hari-libur/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        loadHariLibur();
    }

    loadHariLibur();
  </script>
</body>
</html>