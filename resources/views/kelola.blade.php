<!DOCTYPE html>
<html lang="id">

<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Siswa</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --bg: #0a0e1a; --card: #111827; --card2: #1a2235;
      --border: rgba(255,255,255,0.07); --accent: #22d3ee;
      --accent2: #818cf8; --green: #4ade80; --red: #f87171;
      --text: #f1f5f9; --muted: #64748b;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { background:var(--bg); font-family:'Space Grotesk',sans-serif; color:var(--text); }
    body::before { content:''; position:fixed; inset:0; background-image:linear-gradient(rgba(34,211,238,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,0.03) 1px,transparent 1px); background-size:40px 40px; pointer-events:none; }
    nav { display:flex; align-items:center; justify-content:space-between; padding:1rem 2rem; border-bottom:1px solid var(--border); background:rgba(10,14,26,0.8); backdrop-filter:blur(12px); position:sticky; top:0; z-index:10; }
    .nav-logo { font-weight:700; font-size:1.1rem; }
    .nav-logo span { color:var(--accent); }
    .nav-links { display:flex; gap:0.5rem; }
    .nav-links a { padding:0.4rem 1rem; border-radius:8px; color:var(--muted); text-decoration:none; font-size:0.9rem; font-weight:500; transition:all 0.2s; }
    .nav-links a:hover { color:var(--text); background:var(--card2); }
    .nav-links a.active { color:var(--accent); background:rgba(34,211,238,0.1); }
    .nav-links a.masuk { color:var(--green) }
    .nav-links a.keluar { color:#60a5fa }
    .container { max-width:1000px; margin:0 auto; padding:2rem 1.5rem; }
    h1 { font-size:1.5rem; font-weight:700; margin-bottom:2rem; }
    .grid-2 { display:grid; grid-template-columns:340px 1fr; gap:1.5rem; align-items:start; }
    @media(max-width:768px) { .grid-2 { grid-template-columns:1fr; } }
    .panel { background:var(--card); border:1px solid var(--border); border-radius:16px; padding:1.5rem; }
    .panel-title { font-size:0.78rem; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:1.25rem; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:0.82rem; color:var(--muted); margin-bottom:0.4rem; font-weight:500; }
    .form-group input, .form-group select { width:100%; padding:0.65rem 0.9rem; background:var(--card2); border:1px solid var(--border); border-radius:10px; color:var(--text); font-family:'Space Grotesk',sans-serif; font-size:0.9rem; outline:none; transition:border-color 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color:var(--accent); }
    .form-group input::placeholder { color:var(--muted); }
    .btn { width:100%; padding:0.75rem; border-radius:10px; border:none; cursor:pointer; font-family:'Space Grotesk',sans-serif; font-size:0.9rem; font-weight:600; transition:all 0.2s; }
    .btn-primary { background:linear-gradient(135deg,rgba(34,211,238,0.2),rgba(129,140,248,0.2)); color:var(--accent); border:1px solid rgba(34,211,238,0.3); }
    .btn-primary:hover { background:linear-gradient(135deg,rgba(34,211,238,0.3),rgba(129,140,248,0.3)); }
    .notif { padding:0.65rem 0.9rem; border-radius:8px; font-size:0.85rem; margin-bottom:1rem; display:none; }
    .notif.ok { background:rgba(74,222,128,0.1); color:var(--green); border:1px solid rgba(74,222,128,0.2); }
    .notif.err { background:rgba(248,113,113,0.1); color:var(--red); border:1px solid rgba(248,113,113,0.2); }
    .search-wrap { margin-bottom:1rem; }
    .search-wrap input { width:100%; padding:0.6rem 0.9rem; background:var(--card2); border:1px solid var(--border); border-radius:10px; color:var(--text); font-family:'Space Grotesk',sans-serif; font-size:0.88rem; outline:none; }
    .search-wrap input:focus { border-color:var(--accent); }
    .tabel-wrap { overflow-y:auto; max-height:480px; }
    table { width:100%; border-collapse:collapse; font-size:0.875rem; }
    th { font-size:0.72rem; letter-spacing:1px; text-transform:uppercase; color:var(--muted); font-weight:600; padding:0 0 0.75rem; text-align:left; border-bottom:1px solid var(--border); position:sticky; top:0; background:var(--card); }
    td { padding:0.65rem 0; border-bottom:1px solid var(--border); vertical-align:middle; }
    tr:last-child td { border-bottom:none; }
    .badge-kelas { display:inline-block; padding:0.2rem 0.5rem; border-radius:6px; background:rgba(129,140,248,0.15); color:var(--accent2); font-size:0.8rem; font-weight:500; }
    .kode-text { font-family:'JetBrains Mono',monospace; font-size:0.82rem; color:var(--accent); }
    .btn-small { padding:0.3rem 0.65rem; border-radius:6px; font-size:0.78rem; font-family:'Space Grotesk',sans-serif; font-weight:500; cursor:pointer; border:none; transition:all 0.15s; }
    .btn-qr { background:rgba(34,211,238,0.12); color:var(--accent); }
    .btn-qr:hover { background:rgba(34,211,238,0.22); }
    .btn-hapus { background:rgba(248,113,113,0.12); color:var(--red); }
    .btn-hapus:hover { background:rgba(248,113,113,0.22); }
    .actions { display:flex; gap:0.4rem; }
    .section-title { font-size:1rem; font-weight:600; margin:2rem 0 1rem; color:var(--muted); border-top:1px solid var(--border); padding-top:1.5rem; }
    .badge-role-admin      { display:inline-block; padding:0.2rem 0.5rem; border-radius:6px; background:rgba(34,211,238,0.15); color:var(--accent); font-size:0.8rem; font-weight:500; }
    .badge-role-sekretaris { display:inline-block; padding:0.2rem 0.5rem; border-radius:6px; background:rgba(129,140,248,0.15); color:var(--accent2); font-size:0.8rem; font-weight:500; }
    .badge-role-user       { display:inline-block; padding:0.2rem 0.5rem; border-radius:6px; background:rgba(100,116,139,0.15); color:var(--muted); font-size:0.8rem; font-weight:500; }
    .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.7); display:none; align-items:center; justify-content:center; z-index:100; }
    .modal-overlay.show { display:flex; }
    .modal { background:var(--card); border:1px solid var(--border); border-radius:20px; padding:2rem; width:300px; text-align:center; position:relative; }
    .modal h3 { font-size:1rem; margin-bottom:0.25rem; }
    .modal .sub { color:var(--muted); font-size:0.85rem; margin-bottom:1.25rem; }
    .qr-wrap { background:white; padding:1rem; border-radius:10px; display:inline-block; }
    .qr-wrap img { display:block; width:200px; height:200px; }
    .btn-close { position:absolute; top:1rem; right:1rem; background:var(--card2); border:1px solid var(--border); color:var(--muted); padding:0.25rem 0.6rem; border-radius:6px; cursor:pointer; font-size:0.85rem; }
    .btn-print { margin-top:1rem; width:100%; padding:0.65rem; border-radius:10px; border:none; background:rgba(34,211,238,0.15); color:var(--accent); font-family:'Space Grotesk',sans-serif; font-weight:600; cursor:pointer; font-size:0.9rem; }
    .empty-state { color:var(--muted); font-size:0.9rem; text-align:center; padding:2rem; }
    @media print {
      body * { visibility:hidden; }
      .qr-wrap, .qr-wrap * { visibility:visible; }
      .qr-wrap { position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); }
    }
  </style>
</head>

<body>
  <nav>
    <div class="nav-logo">Absensi<span>.</span></div>
    <div class="nav-links">
      <a href="/masuk" class="masuk">Scan Masuk</a>
      <a href="/keluar" class="keluar">Scan Keluar</a>
      <a href="/dashboard">Dashboard</a>
      <a href="/kelola" class="active">Kelola Siswa</a>
    </div>
  </nav>

  <div class="container">
    <h1>Kelola Siswa</h1>

    {{-- ── Panel Siswa ─────────────────────────────────────────── --}}
    <div class="grid-2">
      <div class="panel">
        <div class="panel-title">Tambah Siswa Baru</div>
        <div class="notif" id="notif"></div>
        <div class="form-group">
          <label>Nama Panggilan (isi QR Code)</label>
          <input type="text" id="f-panggilan" placeholder="contoh: budi, ani, raka" autocomplete="off">
        </div>
        <div class="form-group">
          <label>Nama Lengkap</label>
          <input type="text" id="f-lengkap" placeholder="Nama lengkap siswa" autocomplete="off">
        </div>
        <div class="form-group">
          <label>Kelas</label>
          <input type="text" id="f-kelas" placeholder="contoh: X-A, XI-RPL, XII-IPA" autocomplete="off">
        </div>
        <div class="form-group">
          <label>Jurusan</label>
          <select id="f-jurusan">
            <option value="">-- Pilih Jurusan --</option>
            <option value="Pemasaran">Pemasaran</option>
            <option value="Akuntansi dan Keuangan Lembaga">Akuntansi dan Keuangan Lembaga</option>
            <option value="Manajemen Perkantoran dan Layanan Bisnis">Manajemen Perkantoran dan Layanan Bisnis</option>
            <option value="Teknik Jaringan Komputer dan Telekomunikasi">Teknik Jaringan Komputer dan Telekomunikasi</option>
            <option value="Kuliner">Kuliner</option>
            <option value="Perhotelan">Perhotelan</option>
            <option value="Rekayasa Perangkat Lunak">Rekayasa Perangkat Lunak</option>
          </select>
        </div>
        <button class="btn btn-primary" onclick="tambahSiswa()">+ Tambah Siswa</button>
      </div>

      <div class="panel">
        <div class="panel-title">Data Siswa</div>
        <div class="search-wrap">
          <input type="text" id="search" placeholder="Cari nama atau kelas..." oninput="filterSiswa()">
        </div>
        <div class="tabel-wrap">
          <table>
            <thead><tr><th>Nama</th><th>Kelas</th><th>Kode QR</th><th></th></tr></thead>
            <tbody id="tbody-siswa"></tbody>
          </table>
        </div>
      </div>
    </div>

    {{-- ── Panel User ──────────────────────────────────────────── --}}
    <div class="section-title">Kelola User</div>
    <div class="grid-2">
      <div class="panel">
        <div class="panel-title">Tambah User Baru</div>
        <div class="notif" id="notif-user"></div>
        <div class="form-group">
          <label>Username</label>
          <input type="text" id="u-username" placeholder="contoh: guru1, sekretaris_xirpl" autocomplete="off">
        </div>
        <div class="form-group">
          <label>Password</label>
          <input type="password" id="u-password" placeholder="Minimal 6 karakter">
        </div>
        <div class="form-group">
          <label>Role</label>
          <select id="u-role" onchange="toggleKelasInput()">
            <option value="user">User (Scan saja)</option>
            <option value="sekretaris">Sekretaris (Dashboard)</option>
            <option value="admin">Admin (Semua akses)</option>
          </select>
        </div>
        <div class="form-group" id="kelas-input" style="display:none;">
          <label>Kelas Sekretaris</label>
          <input type="text" id="u-kelas" placeholder="contoh: XI-RPL, X-A" autocomplete="off">
        </div>
        <button class="btn btn-primary" onclick="tambahUser()">+ Tambah User</button>
      </div>

      <div class="panel">
        <div class="panel-title">Daftar User</div>
        <div class="tabel-wrap">
          <table>
            <thead><tr><th>Username</th><th>Role</th><th>Kelas</th><th></th></tr></thead>
            <tbody id="tbody-user"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  {{-- Modal QR --}}
  <div class="modal-overlay" id="modal-qr">
    <div class="modal">
      <button class="btn-close" onclick="closeModal()">✕</button>
      <h3 id="modal-nama">—</h3>
      <p class="sub" id="modal-kelas">—</p>
      <div class="qr-wrap">
        <img id="modal-img" src="" alt="QR Code">
      </div>
      <button class="btn-print" onclick="window.print()">🖨️ Cetak QR Code</button>
    </div>
  </div>

  <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // ── Siswa ────────────────────────────────────────────────────────────────
    let semuaSiswa = [];

    async function loadSiswa() {
        const res = await fetch('/api/siswa');
        semuaSiswa = await res.json();
        renderSiswa(semuaSiswa);
    }

    function renderSiswa(list) {
        const tbody = document.getElementById('tbody-siswa');
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Belum ada data siswa</td></tr>';
            return;
        }
        tbody.innerHTML = list.map(s => `
        <tr>
            <td>${s.nama_lengkap}</td>
            <td><span class="badge-kelas">${s.kelas}</span></td>
            <td><span class="kode-text">${s.nama_panggilan}</span></td>
            <td><div class="actions">
                <button class="btn-small btn-qr" onclick="showQR(${s.id},'${s.nama_panggilan}','${s.nama_lengkap}','${s.kelas}')">QR Code</button>
                <button class="btn-small btn-qr" onclick="window.open('/kartu/${s.id}','_blank')">Kartu</button>
                <button class="btn-small btn-hapus" onclick="hapusSiswa(${s.id},'${s.nama_lengkap}')">Hapus</button>
            </div></td>
        </tr>`).join('');
    }

    function filterSiswa() {
        const q = document.getElementById('search').value.toLowerCase();
        renderSiswa(semuaSiswa.filter(s =>
            s.nama_lengkap.toLowerCase().includes(q) ||
            s.kelas.toLowerCase().includes(q) ||
            s.nama_panggilan.toLowerCase().includes(q)
        ));
    }

    async function tambahSiswa() {
        const panggilan = document.getElementById('f-panggilan').value.trim();
        const lengkap   = document.getElementById('f-lengkap').value.trim();
        const kelas     = document.getElementById('f-kelas').value.trim();
        const jurusan   = document.getElementById('f-jurusan').value.trim();
        const notif     = document.getElementById('notif');

        const res = await fetch('/api/siswa', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ nama_panggilan: panggilan, nama_lengkap: lengkap, kelas, jurusan })
        });
        const d = await res.json();
        notif.className = 'notif ' + (d.status === 'berhasil' ? 'ok' : 'err');
        notif.textContent = d.pesan;
        notif.style.display = 'block';
        setTimeout(() => notif.style.display = 'none', 3000);
        if (d.status === 'berhasil') {
            document.getElementById('f-panggilan').value = '';
            document.getElementById('f-lengkap').value = '';
            document.getElementById('f-kelas').value = '';
            document.getElementById('f-jurusan').value = '';
            loadSiswa();
        }
    }

    async function hapusSiswa(id, nama) {
        if (!confirm(`Hapus siswa "${nama}"? Data absensinya juga akan dihapus.`)) return;
        await fetch(`/api/siswa/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        loadSiswa();
    }

    async function showQR(id, kode, nama, kelas) {
        document.getElementById('modal-nama').textContent = nama;
        document.getElementById('modal-kelas').textContent = `Kelas ${kelas} · Kode: ${kode}`;
        document.getElementById('modal-img').src = '';
        document.getElementById('modal-qr').classList.add('show');
        const res = await fetch(`/api/qrcode/${kode}`);
        const d = await res.json();
        document.getElementById('modal-img').src = d.qrcode;
    }

    function closeModal() {
        document.getElementById('modal-qr').classList.remove('show');
    }

    // ── User ─────────────────────────────────────────────────────────────────
    function toggleKelasInput() {
        const role = document.getElementById('u-role').value;
        document.getElementById('kelas-input').style.display = role === 'sekretaris' ? 'block' : 'none';
    }

    async function loadUser() {
        const res = await fetch('/api/users');
        const users = await res.json();
        renderUser(users);
    }

    function renderUser(list) {
        const tbody = document.getElementById('tbody-user');
        if (!list.length) {
            tbody.innerHTML = '<tr><td colspan="4" class="empty-state">Belum ada user</td></tr>';
            return;
        }
        tbody.innerHTML = list.map(u => `
        <tr>
            <td>${u.username}</td>
            <td><span class="badge-role-${u.role}">${u.role}</span></td>
            <td>${u.kelas ?? '—'}</td>
            <td><button class="btn-small btn-hapus" onclick="hapusUser(${u.id},'${u.username}')">Hapus</button></td>
        </tr>`).join('');
    }

    async function tambahUser() {
        const username = document.getElementById('u-username').value.trim();
        const password = document.getElementById('u-password').value.trim();
        const role     = document.getElementById('u-role').value;
        const kelas    = document.getElementById('u-kelas').value.trim();
        const notif    = document.getElementById('notif-user');

        const res = await fetch('/api/users', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ username, password, role, kelas })
        });
        const d = await res.json();
        notif.className = 'notif ' + (d.status === 'berhasil' ? 'ok' : 'err');
        notif.textContent = d.pesan;
        notif.style.display = 'block';
        setTimeout(() => notif.style.display = 'none', 3000);
        if (d.status === 'berhasil') {
            document.getElementById('u-username').value = '';
            document.getElementById('u-password').value = '';
            document.getElementById('u-kelas').value = '';
            document.getElementById('u-role').value = 'user';
            document.getElementById('kelas-input').style.display = 'none';
            loadUser();
        }
    }

    async function hapusUser(id, username) {
        if (!confirm(`Hapus user "${username}"?`)) return;
        const res = await fetch(`/api/users/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF },
        });
        const d = await res.json();
        if (d.status === 'berhasil') loadUser();
        else alert(d.pesan);
    }

    loadSiswa();
    loadUser();
  </script>
</body>
</html>