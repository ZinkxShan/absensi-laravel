<!DOCTYPE html>
<html lang="id">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Rekap Absensi</title>
  <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
  <style>
    :root { --bg:#0a0e1a;--card:#111827;--card2:#1a2235;--border:rgba(255,255,255,0.07);--accent:#22d3ee;--accent2:#818cf8;--green:#4ade80;--blue:#60a5fa;--yellow:#fbbf24;--orange:#f97316;--red:#f87171;--text:#f1f5f9;--muted:#64748b; }
    *{margin:0;padding:0;box-sizing:border-box;}
    body{background:var(--bg);font-family:'Space Grotesk',sans-serif;color:var(--text);}
    body::before{content:'';position:fixed;inset:0;background-image:linear-gradient(rgba(34,211,238,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(34,211,238,0.03) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;}
    nav{display:flex;align-items:center;justify-content:space-between;padding:1rem 2rem;border-bottom:1px solid var(--border);background:rgba(10,14,26,0.8);backdrop-filter:blur(12px);position:sticky;top:0;z-index:10;}
    .nav-logo{font-weight:700;font-size:1.1rem;}.nav-logo span{color:var(--accent);}
    .nav-links{display:flex;gap:0.5rem;}
    .nav-links a{padding:0.4rem 1rem;border-radius:8px;color:var(--muted);text-decoration:none;font-size:0.9rem;font-weight:500;transition:all 0.2s;}
    .nav-links a:hover{color:var(--text);background:var(--card2);}
    .nav-links a.active{color:var(--accent);background:rgba(34,211,238,0.1);}
    .nav-links a.masuk{color:var(--green)}.nav-links a.keluar{color:var(--blue)}
    .container{max-width:1200px;margin:0 auto;padding:2rem 1.5rem;}
    .page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;}
    .page-header h1{font-size:1.5rem;font-weight:700;}
    .filter-bar{display:flex;align-items:center;gap:0.75rem;flex-wrap:wrap;background:var(--card);border:1px solid var(--border);border-radius:14px;padding:1rem 1.25rem;margin-bottom:1.5rem;}
    .filter-bar label{font-size:0.82rem;color:var(--muted);font-weight:500;}
    .filter-bar select, .filter-bar input{padding:0.5rem 0.75rem;background:var(--card2);border:1px solid var(--border);border-radius:8px;color:var(--text);font-family:'Space Grotesk',sans-serif;font-size:0.85rem;outline:none;}
    .filter-bar select:focus, .filter-bar input:focus{border-color:var(--accent);}
    .btn-filter{padding:0.5rem 1.25rem;border-radius:8px;border:none;cursor:pointer;font-family:'Space Grotesk',sans-serif;font-size:0.85rem;font-weight:600;transition:all 0.2s;background:linear-gradient(135deg,rgba(34,211,238,0.2),rgba(129,140,248,0.2));color:var(--accent);border:1px solid rgba(34,211,238,0.3);}
    .btn-filter:hover{background:linear-gradient(135deg,rgba(34,211,238,0.3),rgba(129,140,248,0.3));}
    .btn-export{padding:0.5rem 1.25rem;border-radius:8px;border:none;cursor:pointer;font-family:'Space Grotesk',sans-serif;font-size:0.85rem;font-weight:600;transition:all 0.2s;background:rgba(74,222,128,0.15);color:var(--green);border:1px solid rgba(74,222,128,0.3);}
    .btn-export:hover{background:rgba(74,222,128,0.25);}
    .info-label{font-size:0.85rem;color:var(--muted);}
    .info-label span{color:var(--accent);font-family:'JetBrains Mono',monospace;font-weight:600;}
    .panel{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.5rem;}
    .panel-title{font-size:0.78rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:1.25rem;}
    .tabel-wrap{overflow-x:auto;}
    table{width:100%;border-collapse:collapse;font-size:0.875rem;}
    th{font-size:0.72rem;letter-spacing:1px;text-transform:uppercase;color:var(--muted);font-weight:600;padding:0.6rem 0.75rem 0.75rem;text-align:center;border-bottom:1px solid var(--border);white-space:nowrap;}
    th:first-child, th:nth-child(2), th:nth-child(3){text-align:left;}
    td{padding:0.6rem 0.75rem;border-bottom:1px solid var(--border);text-align:center;vertical-align:middle;}
    td:first-child, td:nth-child(2), td:nth-child(3){text-align:left;}
    tr:last-child td{border-bottom:none;}
    tr:hover td{background:rgba(255,255,255,0.02);}
    .badge-kelas{display:inline-block;padding:0.2rem 0.5rem;border-radius:6px;background:rgba(129,140,248,0.15);color:var(--accent2);font-size:0.8rem;font-weight:500;}
    .num{font-family:'JetBrains Mono',monospace;font-size:0.85rem;}
    .num-hadir{color:var(--green);}
    .num-terlambat{color:var(--orange);}
    .num-izin{color:var(--accent);}
    .num-sakit{color:var(--accent2);}
    .num-alpha{color:var(--red);}
    .num-tidak{color:var(--muted);}
    .persen-bar{display:flex;align-items:center;gap:0.5rem;min-width:100px;}
    .persen-track{flex:1;height:6px;background:var(--card2);border-radius:99px;overflow:hidden;}
    .persen-fill{height:100%;border-radius:99px;transition:width 0.4s ease;}
    .persen-text{font-family:'JetBrains Mono',monospace;font-size:0.8rem;color:var(--muted);min-width:40px;}
    .empty-state{color:var(--muted);font-size:0.9rem;text-align:center;padding:3rem;}
    .loading{color:var(--muted);font-size:0.9rem;text-align:center;padding:3rem;}
  </style>
</head>
<body>
  <nav>
    <div class="nav-logo">Absensi<span>.</span></div>
    <div class="nav-links">
      <a href="/masuk" class="masuk">Scan Masuk</a>
      <a href="/keluar" class="keluar">Scan Keluar</a>
      <a href="/dashboard">Dashboard</a>
      <a href="/rekap" class="active">Rekap</a>
      <a href="/kelola">Kelola Siswa</a>
      <a href="/kelola-user">Kelola User</a>
      <a href="/hari-libur">Hari Libur</a>
    </div>
  </nav>

  <div class="container">
    <div class="page-header">
      <h1>Rekap Absensi</h1>
      <button class="btn-export" onclick="exportExcel()">⬇ Export Excel</button>
    </div>

    {{-- Filter Bar --}}
    <div class="filter-bar">
      <label>Jangka Waktu:</label>
      <select id="f-mode" onchange="toggleFilter()">
        <option value="bulan">Per Bulan</option>
        <option value="semester">Per Semester</option>
        <option value="tahun">Per Tahun Ajaran</option>
      </select>

      <div id="filter-bulan" style="display:flex;align-items:center;gap:0.75rem;">
        <label>Bulan:</label>
        <select id="f-bulan">
          <option value="1">Januari</option><option value="2">Februari</option>
          <option value="3">Maret</option><option value="4">April</option>
          <option value="5">Mei</option><option value="6">Juni</option>
          <option value="7">Juli</option><option value="8">Agustus</option>
          <option value="9">September</option><option value="10">Oktober</option>
          <option value="11">November</option><option value="12">Desember</option>
        </select>
      </div>

      <div id="filter-semester" style="display:none;align-items:center;gap:0.75rem;">
        <label>Semester:</label>
        <select id="f-semester">
          <option value="1">Semester 1 (Jul–Des)</option>
          <option value="2">Semester 2 (Jan–Jun)</option>
        </select>
      </div>

      <label>Tahun:</label>
      <input type="number" id="f-tahun" value="{{ date('Y') }}" min="2020" max="2099" style="width:90px;">

      <button class="btn-filter" onclick="loadRekap()">🔍 Tampilkan</button>
    </div>

    {{-- Info --}}
    <div style="margin-bottom:1rem;" id="info-label"></div>

    {{-- Tabel --}}
    <div class="panel">
      <div class="panel-title">Data Rekap</div>
      <div class="tabel-wrap">
        <table>
          <thead>
            <tr>
              <th>No</th>
              <th>Nama Lengkap</th>
              <th>Kelas</th>
              <th>Hadir</th>
              <th>Terlambat</th>
              <th>Izin</th>
              <th>Sakit</th>
              <th>Alpha</th>
              <th>Tdk Hadir</th>
              <th>Total Hadir</th>
              <th>% Kehadiran</th>
            </tr>
          </thead>
          <tbody id="tbody-rekap">
            <tr><td colspan="11" class="loading">Pilih filter dan klik Tampilkan</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script>
    let lastParams = '';

    function toggleFilter() {
        const mode = document.getElementById('f-mode').value;
        document.getElementById('filter-bulan').style.display    = mode === 'bulan'    ? 'flex' : 'none';
        document.getElementById('filter-semester').style.display = mode === 'semester' ? 'flex' : 'none';
    }

    function getParams() {
        const mode     = document.getElementById('f-mode').value;
        const tahun    = document.getElementById('f-tahun').value;
        const bulan    = document.getElementById('f-bulan').value;
        const semester = document.getElementById('f-semester').value;
        return `mode=${mode}&tahun=${tahun}&bulan=${bulan}&semester=${semester}`;
    }

    async function loadRekap() {
        const params = getParams();
        lastParams   = params;

        document.getElementById('tbody-rekap').innerHTML =
            '<tr><td colspan="11" class="loading">⏳ Memuat data...</td></tr>';
        document.getElementById('info-label').innerHTML = '';

        const res = await fetch(`/api/rekap?${params}`);
        const d   = await res.json();

        document.getElementById('info-label').innerHTML =
            `<span class="info-label">📅 <strong>${d.label}</strong> &nbsp;|&nbsp; Hari Aktif Sekolah: <span>${d.hari_efektif} hari</span> &nbsp;|&nbsp; Total Siswa: <span>${d.rekap.length}</span></span>`;

        if (!d.rekap.length) {
            document.getElementById('tbody-rekap').innerHTML =
                '<tr><td colspan="11" class="empty-state">Tidak ada data</td></tr>';
            return;
        }

        document.getElementById('tbody-rekap').innerHTML = d.rekap.map((r, i) => {
            const pct   = r.persen;
            const color = pct >= 80 ? '#4ade80' : pct >= 60 ? '#fbbf24' : '#f87171';
            return `<tr>
                <td class="num">${i + 1}</td>
                <td>${r.nama_lengkap}</td>
                <td><span class="badge-kelas">${r.kelas}</span></td>
                <td class="num num-hadir">${r.hadir}</td>
                <td class="num num-terlambat">${r.terlambat}</td>
                <td class="num num-izin">${r.izin}</td>
                <td class="num num-sakit">${r.sakit}</td>
                <td class="num num-alpha">${r.alpha}</td>
                <td class="num num-tidak">${r.tidak_hadir}</td>
                <td class="num" style="color:var(--text);font-weight:600;">${r.total_hadir}</td>
                <td>
                    <div class="persen-bar">
                        <div class="persen-track">
                            <div class="persen-fill" style="width:${pct}%;background:${color}"></div>
                        </div>
                        <span class="persen-text" style="color:${color}">${pct}%</span>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function exportExcel() {
        if (!lastParams) {
            alert('Tampilkan data rekap terlebih dahulu!');
            return;
        }
        window.open(`/api/rekap/export?${lastParams}`, '_blank');
    }

    // Set bulan sekarang sebagai default
    document.getElementById('f-bulan').value = new Date().getMonth() + 1;
  </script>
</body>
</html>