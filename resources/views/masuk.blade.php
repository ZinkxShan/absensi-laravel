<!DOCTYPE html>
<html lang="id">

<head>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Absensi — Scan Masuk</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap"
    rel="stylesheet">
  <style>
    :root {
      --bg: #0a0e1a;
      --card: #111827;
      --card2: #1a2235;
      --border: rgba(255, 255, 255, 0.07);
      --green: #4ade80;
      --yellow: #fbbf24;
      --red: #f87171;
      --text: #f1f5f9;
      --muted: #64748b;
      --accent: #4ade80;
      --accent-dim: rgba(74, 222, 128, 0.15);
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: var(--bg);
      font-family: 'Space Grotesk', sans-serif;
      color: var(--text);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: linear-gradient(rgba(74, 222, 128, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(74, 222, 128, 0.03) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
    }

    nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 1rem 2rem;
      border-bottom: 1px solid var(--border);
      background: rgba(10, 14, 26, 0.8);
      backdrop-filter: blur(12px);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    .nav-logo {
      font-weight: 700;
      font-size: 1.1rem;
    }

    .nav-logo span {
      color: var(--green);
    }

    .nav-links {
      display: flex;
      gap: 0.5rem;
    }

    .nav-links a {
      padding: 0.4rem 1rem;
      border-radius: 8px;
      color: var(--muted);
      text-decoration: none;
      font-size: 0.9rem;
      font-weight: 500;
      transition: all 0.2s;
    }

    .nav-links a:hover {
      color: var(--text);
      background: var(--card2);
    }

    .nav-links a.active-masuk {
      color: var(--green);
      background: rgba(74, 222, 128, 0.1);
    }

    .nav-links a.active-keluar {
      color: #60a5fa;
      background: rgba(96, 165, 250, 0.1);
    }

    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 2rem 1rem;
      gap: 2rem;
    }

    .clock-wrap {
      text-align: center;
    }

    #clock {
      font-family: 'JetBrains Mono', monospace;
      font-size: 3.5rem;
      font-weight: 600;
      letter-spacing: 2px;
      color: var(--green);
    }

    #tanggal {
      color: var(--muted);
      font-size: 0.9rem;
      margin-top: 0.25rem;
    }

    .mode-badge {
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      padding: 0.4rem 1.25rem;
      border-radius: 99px;
      background: rgba(74, 222, 128, 0.15);
      border: 1px solid rgba(74, 222, 128, 0.3);
      color: var(--green);
      font-size: 0.9rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .mode-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--green);
      animation: pulse 1.5s infinite;
    }

    @keyframes pulse {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.4;
      }
    }

    .scan-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 2.5rem;
      width: 100%;
      max-width: 480px;
      position: relative;
      overflow: hidden;
    }

    .scan-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: linear-gradient(90deg, transparent, var(--green), transparent);
    }

    .scan-label {
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 2px;
      color: var(--muted);
      text-transform: uppercase;
      margin-bottom: 0.75rem;
    }

    .scan-input-wrap {
      position: relative;
    }

    #input-barcode {
      width: 100%;
      padding: 1rem 1rem 1rem 3rem;
      background: var(--card2);
      border: 1px solid var(--border);
      border-radius: 12px;
      color: var(--text);
      font-family: 'JetBrains Mono', monospace;
      font-size: 1.1rem;
      outline: none;
      transition: border-color 0.2s;
    }

    #input-barcode:focus {
      border-color: var(--green);
    }

    #input-barcode::placeholder {
      color: var(--muted);
      font-size: 0.9rem;
      font-family: 'Space Grotesk', sans-serif;
    }

    .scan-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: var(--muted);
      font-size: 1.2rem;
    }

    .scan-hint {
      margin-top: 0.75rem;
      font-size: 0.82rem;
      color: var(--muted);
      text-align: center;
    }

    .result-box {
      margin-top: 1.5rem;
      border-radius: 14px;
      padding: 1.25rem 1.5rem;
      display: none;
      animation: slideIn 0.3s ease;
      align-items: center;
      gap: 1rem;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(8px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .result-box.berhasil {
      background: rgba(74, 222, 128, 0.1);
      border: 1px solid rgba(74, 222, 128, 0.25);
    }

    .result-box.sudah {
      background: rgba(251, 191, 36, 0.1);
      border: 1px solid rgba(251, 191, 36, 0.25);
    }

    .result-box.error {
      background: rgba(248, 113, 113, 0.1);
      border: 1px solid rgba(248, 113, 113, 0.25);
    }

    .result-icon {
      font-size: 2rem;
      flex-shrink: 0;
    }

    .result-nama {
      font-size: 1.1rem;
      font-weight: 600;
    }

    .result-detail {
      font-size: 0.85rem;
      color: var(--muted);
      margin-top: 0.15rem;
    }

    .result-waktu {
      margin-left: auto;
      flex-shrink: 0;
      font-family: 'JetBrains Mono', monospace;
      font-size: 1rem;
      font-weight: 600;
    }

    .result-box.berhasil .result-waktu {
      color: var(--green);
    }

    .result-box.sudah .result-waktu {
      color: var(--yellow);
    }

    .recent-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 1.5rem;
      width: 100%;
      max-width: 480px;
    }

    .recent-title {
      font-size: 0.8rem;
      font-weight: 600;
      letter-spacing: 2px;
      color: var(--muted);
      text-transform: uppercase;
      margin-bottom: 1rem;
    }

    .recent-list {
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
    }

    .recent-item {
      display: flex;
      align-items: center;
      padding: 0.6rem 0.75rem;
      background: var(--card2);
      border-radius: 10px;
      animation: fadeIn 0.3s ease;
      gap: 0.75rem;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
      }

      to {
        opacity: 1;
      }
    }

    .recent-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--green);
      flex-shrink: 0;
    }

    .recent-nama {
      flex: 1;
      font-size: 0.9rem;
      font-weight: 500;
    }

    .recent-kelas {
      font-size: 0.8rem;
      color: var(--muted);
    }

    .recent-time {
      font-family: 'JetBrains Mono', monospace;
      font-size: 0.8rem;
      color: var(--muted);
    }

    .empty-state {
      color: var(--muted);
      font-size: 0.9rem;
      text-align: center;
      padding: 1rem;
    }

    .result-box.terlambat {
      background: rgba(251, 191, 36, 0.1);
      border: 1px solid rgba(251, 191, 36, 0.25);
    }
    .result-box.terlambat .result-waktu {
      color: var(--yellow);
    }
  </style>
</head>

<body>
  <nav>
    <div class="nav-logo">Absensi<span>.</span></div>
    <div class="nav-links">
      <a href="/masuk" class="active-masuk">Scan Masuk</a>
      <a href="/keluar">Scan Keluar</a>
      <a href="/dashboard">Dashboard</a>
      <a href="/kelola">Kelola Siswa</a>
      <a href="/kelola-user">Kelola User</a>
      <a href="/hari-libur">Hari Libur</a>
    </div>
  </nav>
  <main>
    <div class="clock-wrap">
      <div id="clock">00:00:00</div>
      <div id="tanggal"></div>
    </div>

    <div class="mode-badge">
      <div class="mode-dot"></div> Absen Masuk
    </div>

    <div class="scan-card">
      <div class="scan-label">Scan QR Code — Masuk</div>
      <div class="scan-input-wrap">
        <span class="scan-icon">⊞</span>
        <input type="text" id="input-barcode" placeholder="Scan QR Code kartu siswa..." autocomplete="off" autofocus>
      </div>
      <p class="scan-hint">Scanner otomatis — tidak perlu menekan tombol apapun</p>
      <div class="result-box" id="result-box">
        <div class="result-icon" id="result-icon"></div>
        <div>
          <div class="result-nama" id="result-nama"></div>
          <div class="result-detail" id="result-kelas"></div>
        </div>
        <div class="result-waktu" id="result-waktu"></div>
      </div>
    </div>

    <div class="recent-card">
      <div class="recent-title">Baru Saja Masuk</div>
      <ul class="recent-list" id="recent-list">
        <li class="empty-state">Belum ada absensi masuk hari ini</li>
      </ul>
    </div>
  </main>


  <script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


    const HARI = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const BULAN = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    function updateClock() {
      const now = new Date();
      document.getElementById('clock').textContent = now.toTimeString().slice(0, 8);
      document.getElementById('tanggal').textContent = `${HARI[now.getDay()]}, ${now.getDate()} ${BULAN[now.getMonth()]} ${now.getFullYear()}`;
    }
    updateClock(); setInterval(updateClock, 1000);

    const inp = document.getElementById('input-barcode');
    const resultBox = document.getElementById('result-box');
    let clearTimer, recentItems = [];
    let buffer = '', scanTimer = null;

    document.addEventListener('keypress', (e) => {
      inp.focus();
      if (e.key === 'Enter') {
        if (buffer.length > 0) { prosesQR(buffer); buffer = ''; }
        return;
      }
      buffer += e.key;
      clearTimeout(scanTimer);
      scanTimer = setTimeout(() => { if (buffer.length > 0) { prosesQR(buffer); buffer = ''; } }, 150);
    });
    document.addEventListener('click', () => inp.focus());

    async function prosesQR(kode) {
    inp.value = kode;
    try {
        const res = await fetch('/api/scan/masuk', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,   // ← masuk ke dalam headers
            },
            body: JSON.stringify({ kode })  // ← body di luar headers, tapi masih di dalam fetch
        });
        const data = await res.json();
        inp.value = '';
        showResult(data);
    } catch {
        showResult({ status: 'error', pesan: 'Koneksi ke server gagal' });
    }
}

    function showResult(data) {
    clearTimeout(clearTimer);
    resultBox.className = 'result-box ' + (data.terlambat ? 'terlambat' : data.status);
    resultBox.style.display = 'flex';
    document.getElementById('result-icon').textContent = data.status === 'berhasil'
        ? (data.terlambat ? '⚠' : '✓')
        : data.status === 'sudah' ? '⚠' : '✗';
    document.getElementById('result-nama').textContent = data.nama || data.pesan || 'Error';
    document.getElementById('result-kelas').textContent = data.kelas
        ? `Kelas ${data.kelas}` + (data.terlambat ? ` · Terlambat! Batas ${data.jam_batas}` : '') + (data.status === 'sudah' ? ' · Sudah absen masuk' : '')
        : data.pesan || '';
    document.getElementById('result-waktu').textContent = data.waktu ? data.waktu.slice(0, 5) : '';
    if (data.status === 'berhasil') addRecent(data);
    clearTimer = setTimeout(() => { resultBox.style.display = 'none'; }, 4000);
}

    function addRecent(data) {
      recentItems.unshift(data);
      if (recentItems.length > 6) recentItems.pop();
      document.getElementById('recent-list').innerHTML = recentItems.map(d => `
      <li class="recent-item">
        <div class="recent-dot"></div>
        <div class="recent-nama">${d.nama}</div>
        <div class="recent-kelas">Kelas ${d.kelas}</div>
        <div class="recent-time">${d.waktu.slice(0, 5)}</div>
      </li>`).join('');
    }
  </script>
</body>

</html>