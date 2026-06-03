<!DOCTYPE html>
<html lang="id">
<head>
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Absensi</title>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>




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
  .dash-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:2rem;flex-wrap:wrap;gap:1rem;}
  .dash-header h1{font-size:1.5rem;font-weight:700;letter-spacing:-0.5px;}
  #tanggal-filter{padding:0.5rem 0.75rem;background:var(--card);border:1px solid var(--border);border-radius:8px;color:var(--text);font-family:'JetBrains Mono',monospace;font-size:0.85rem;outline:none;cursor:pointer;}
  #tanggal-filter::-webkit-calendar-picker-indicator{filter:invert(0.5);}
  .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:1rem;margin-bottom:2rem;}
  .stat-card{background:var(--card);border:1px solid var(--border);border-radius:14px;padding:1.25rem 1.5rem;position:relative;overflow:hidden;}
  .stat-card::after{content:'';position:absolute;inset:0;border-radius:14px;opacity:0.05;}
  .stat-card.total::after{background:#818cf8}.stat-card.hadir::after{background:#4ade80}.stat-card.lengkap::after{background:#60a5fa}.stat-card.blm-keluar::after{background:#fbbf24}.stat-card.absen::after{background:#f87171}
  .stat-label{font-size:0.75rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:0.5rem;}
  .stat-value{font-size:2.25rem;font-weight:700;letter-spacing:-1px;font-family:'JetBrains Mono',monospace;}
  .stat-card.total .stat-value{color:#818cf8}.stat-card.hadir .stat-value{color:var(--green)}.stat-card.lengkap .stat-value{color:var(--blue)}.stat-card.blm-keluar .stat-value{color:var(--yellow)}.stat-card.absen .stat-value{color:var(--red)}
  .stat-card.terlambat::after { background: #f97316; }
  .stat-card.terlambat .stat-value { color: var(--orange); }
  .stat-card.izin::after   { background: #22d3ee; }
  .stat-card.izin .stat-value   { color: var(--accent); }
  .stat-card.sakit::after  { background: #818cf8; }
  .stat-card.sakit .stat-value  { color: var(--accent2); }
  .stat-card.alpha::after  { background: #f87171; }
  .stat-card.alpha .stat-value  { color: var(--red); }
  .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;margin-bottom:1.25rem;}
  @media(max-width:768px){.grid-2{grid-template-columns:1fr;}}
  .panel{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.5rem;}
  .panel-title{font-size:0.78rem;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:var(--muted);margin-bottom:1.25rem;}
  .chart-wrap{position:relative;height:200px;}
  .kelas-list{display:flex;flex-direction:column;gap:0.75rem;}
  .kelas-item{}
  .kelas-meta{display:flex;justify-content:space-between;margin-bottom:0.35rem;}
  .kelas-name{font-size:0.9rem;font-weight:500;}.kelas-count{font-size:0.85rem;color:var(--muted);font-family:'JetBrains Mono',monospace;}
  .progress-bar{height:6px;background:var(--card2);border-radius:99px;overflow:hidden;}
  .progress-fill{height:100%;background:linear-gradient(90deg,var(--green),var(--blue));border-radius:99px;transition:width 0.6s ease;}
  .tabel-wrap{overflow-y:auto;max-height:340px;}
  table{width:100%;border-collapse:collapse;font-size:0.875rem;}
  th{font-size:0.72rem;letter-spacing:1px;text-transform:uppercase;color:var(--muted);font-weight:600;padding:0 0 0.75rem;text-align:left;border-bottom:1px solid var(--border);position:sticky;top:0;background:var(--card);}
  td{padding:0.6rem 0;border-bottom:1px solid var(--border);}
  tr:last-child td{border-bottom:none;}
  .badge-kelas{display:inline-block;padding:0.2rem 0.5rem;border-radius:6px;background:rgba(129,140,248,0.15);color:#818cf8;font-size:0.8rem;font-weight:500;}
  .waktu-masuk{font-family:'JetBrains Mono',monospace;color:var(--green);font-size:0.82rem;}
  .waktu-keluar{font-family:'JetBrains Mono',monospace;color:var(--blue);font-size:0.82rem;}
  .waktu-kosong{font-family:'JetBrains Mono',monospace;color:var(--muted);font-size:0.82rem;}
  .status-badge{display:inline-block;padding:0.15rem 0.5rem;border-radius:6px;font-size:0.75rem;font-weight:600;}
  .status-lengkap{background:rgba(96,165,250,0.15);color:var(--blue);}
  .status-masuk{background:rgba(251,191,36,0.15);color:var(--yellow);}
  .empty-state{color:var(--muted);font-size:0.9rem;text-align:center;padding:2rem;}
  .status-terlambat { background: rgba(249,115,22,0.15); color: var(--orange); }
  .status-terlambat-lengkap { background: rgba(96,165,250,0.15); color: var(--blue); }
  .badge-ket { display:inline-block;padding:0.15rem 0.5rem;border-radius:6px;font-size:0.75rem;font-weight:600; }
  .badge-ket.izin   { background:rgba(34,211,238,0.15);color:var(--accent); }
  .badge-ket.sakit  { background:rgba(129,140,248,0.15);color:var(--accent2); }
  .badge-ket.alpha  { background:rgba(248,113,113,0.15);color:var(--red); }
  .badge-ket.kosong { color:var(--muted); }
  .btn-ket { padding:0.25rem 0.5rem;border-radius:6px;font-size:0.75rem;font-family:'Space Grotesk',sans-serif;font-weight:500;cursor:pointer;border:none; }
</style>
</head>
<body>
<nav>
  <div class="nav-logo">Absensi<span>.</span></div>
  <div class="nav-links">
    <a href="/masuk" class="masuk">Scan Masuk</a>
    <a href="/keluar" class="keluar">Scan Keluar</a>
    <a href="/dashboard" class="active">Dashboard</a>
    <a href="/kelola">Kelola Siswa</a>
  </div>
</nav>
<div class="container">
  <div class="dash-header">
    <h1>Dashboard Absensi</h1>
    <div style="display:flex;align-items:center;gap:0.5rem;">
      <label style="font-size:0.85rem;color:var(--muted)">Tanggal:</label>
      <input type="date" id="tanggal-filter">

      <!-- Taruh di sebelah filter tanggal -->
      <div style="display:flex;align-items:center;gap:0.5rem;">
        <label style="font-size:0.85rem;color:var(--muted)">Batas Masuk:</label>
        <input type="time" id="jam-batas" style="padding:0.5rem 0.75rem;background:var(--card);border:1px solid var(--border);border-radius:8px;color:var(--text);font-family:'JetBrains Mono',monospace;font-size:0.85rem;outline:none;">
        <button onclick="simpanJamBatas()" style="padding:0.5rem 0.75rem;background:rgba(34,211,238,0.1);border:1px solid rgba(34,211,238,0.2);border-radius:8px;color:var(--accent);cursor:pointer;font-size:0.82rem;font-family:'Space Grotesk',sans-serif;">Simpan</button>
      </div>
    </div>
    
  </div>
  <div class="stats-grid">
    <div class="stat-card total"><div class="stat-label">Total Siswa</div><div class="stat-value" id="s-total">—</div></div>
    <div class="stat-card hadir"><div class="stat-label">Hadir</div><div class="stat-value" id="s-hadir">—</div></div>
    <div class="stat-card lengkap"><div class="stat-label">Sudah Keluar</div><div class="stat-value" id="s-lengkap">—</div></div>
    <div class="stat-card blm-keluar"><div class="stat-label">Belum Keluar</div><div class="stat-value" id="s-blm">—</div></div>
    <div class="stat-card terlambat"><div class="stat-label">Terlambat</div><div class="stat-value" id="s-terlambat">—</div></div>
    <div class="stat-card izin"><div class="stat-label">Izin</div><div class="stat-value" id="s-izin">—</div></div>
    <div class="stat-card sakit"><div class="stat-label">Sakit</div><div class="stat-value" id="s-sakit">—</div></div>
    <div class="stat-card alpha"><div class="stat-label">Alpha</div><div class="stat-value" id="s-alpha">—</div></div>
    <div class="stat-card absen"><div class="stat-label">Tidak Hadir</div><div class="stat-value" id="s-absen">—</div></div>
    
  </div>
  <div class="grid-2">
    <div class="panel">
      <div class="panel-title">Tren 7 Hari Terakhir</div>
      <div class="chart-wrap"><canvas id="chart-tren"></canvas></div>
    </div>
    <div class="panel">
      <div class="panel-title">Rekap Per Kelas</div>
      <div class="kelas-list" id="kelas-list"></div>
    </div>
  </div>
  <div class="grid-2">
    <div class="panel">
      <div class="panel-title">Daftar Hadir</div>
      <div class="tabel-wrap">
        <table>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Kelas</th>
              <th>Masuk</th>
              <th>Keluar</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody id="tbody-hadir"></tbody>
        </table>
      </div>
    </div>
    <div class="panel">
      <div class="panel-title">Tidak Hadir</div>
      <div class="tabel-wrap">
        <table>
          <thead>
            <tr>
              <th>Nama</th>
              <th>Kelas</th>
              <th>Keterangan</th>
              <th id="th-aksi"></th> <!-- kosong untuk non-admin/non-sekretaris -->
            </tr>
</thead>
          <tbody id="tbody-absen"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
  const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  const today = new Date().toISOString().slice(0,10);
  const dateInput = document.getElementById('tanggal-filter');
  dateInput.value = today;
  dateInput.addEventListener('change', () => load(dateInput.value));
  let chartTren = null;

  async function load(tanggal) {
    const res = await fetch(`/api/dashboard?tanggal=${tanggal}`);
    const d = await res.json();

    document.getElementById('s-total').textContent     = d.total_siswa;
    document.getElementById('s-hadir').textContent     = d.hadir;
    document.getElementById('s-terlambat').textContent = d.terlambat;
    document.getElementById('s-lengkap').textContent   = d.lengkap;
    document.getElementById('s-blm').textContent       = d.belum_keluar;
    document.getElementById('s-absen').textContent     = d.tidak_hadir;
    document.getElementById('s-izin').textContent      = d.izin;
    document.getElementById('s-sakit').textContent     = d.sakit;
    document.getElementById('s-alpha').textContent     = d.alpha;

    document.getElementById('kelas-list').innerHTML = d.rekap_kelas.map(k => {
      const pct = k.total ? Math.round(k.hadir / k.total * 100) : 0;
      return `<div class="kelas-item">
        <div class="kelas-meta"><span class="kelas-name">Kelas ${k.kelas}</span><span class="kelas-count">${k.hadir}/${k.total} · ${pct}%</span></div>
        <div class="progress-bar"><div class="progress-fill" style="width:${pct}%"></div></div>
      </div>`;
    }).join('') || '<div class="empty-state">Tidak ada data</div>';

    document.getElementById('tbody-hadir').innerHTML = d.daftar_hadir.length
      ? d.daftar_hadir.map(s => {
          let badgeClass, badgeText;
          if (s.terlambat && s.status === 'lengkap') {
            badgeClass = 'status-terlambat-lengkap'; badgeText = 'Terlambat ✓';
          } else if (s.terlambat) {
            badgeClass = 'status-terlambat'; badgeText = 'Terlambat';
          } else if (s.status === 'lengkap') {
            badgeClass = 'status-lengkap'; badgeText = 'Lengkap';
          } else {
            badgeClass = 'status-masuk'; badgeText = 'Masuk';
          }
          return `<tr>
            <td>${s.nama_lengkap}</td>
            <td><span class="badge-kelas">${s.kelas}</span></td>
            <td class="waktu-masuk">${s.waktu_masuk ? s.waktu_masuk.slice(0,5) : '-'}</td>
            <td class="${s.waktu_keluar ? 'waktu-keluar' : 'waktu-kosong'}">${s.waktu_keluar ? s.waktu_keluar.slice(0,5) : '—'}</td>
            <td><span class="status-badge ${badgeClass}">${badgeText}</span></td>
          </tr>`;
        }).join('')
      : '<tr><td colspan="5" class="empty-state">Belum ada yang absen</td></tr>';


    document.getElementById('tbody-absen').innerHTML = d.tidak_hadir_list.length
    ? d.tidak_hadir_list.map(s => {
        const ket = s.keterangan;
        const badgeKet = ket === 'izin'  ? '<span class="badge-ket izin">Izin</span>'
                       : ket === 'sakit' ? '<span class="badge-ket sakit">Sakit</span>'
                       : ket === 'alpha' ? '<span class="badge-ket alpha">Alpha</span>'
                       : '<span class="badge-ket kosong">—</span>';

        const tombolKet = d.is_admin || (d.user_kelas === s.kelas)
            ? `<div style="display:flex;gap:0.3rem;">
                <button class="btn-ket" onclick="setKeterangan(${s.id},'izin')"  style="background:rgba(34,211,238,0.12);color:var(--accent)">Izin</button>
                <button class="btn-ket" onclick="setKeterangan(${s.id},'sakit')" style="background:rgba(129,140,248,0.12);color:var(--accent2)">Sakit</button>
                <button class="btn-ket" onclick="setKeterangan(${s.id},'alpha')" style="background:rgba(248,113,113,0.12);color:var(--red)">Alpha</button>
               </div>`
            : '';

        return `<tr>
            <td>${s.nama_lengkap}</td>
            <td><span class="badge-kelas">${s.kelas}</span></td>
            <td>${badgeKet}</td>
            <td>${tombolKet}</td>
        </tr>`;
    }).join('')
    : '<tr><td colspan="4" class="empty-state">Semua siswa hadir!</td></tr>';
    const labels = d.tren.map(t => { const dt = new Date(t.tanggal + 'T00:00:00'); return dt.toLocaleDateString('id-ID', {day:'numeric',month:'short'}); });
    const values = d.tren.map(t => t.jumlah);
    if (chartTren) chartTren.destroy();
    chartTren = new Chart(document.getElementById('chart-tren'), {
      type: 'bar',
      data: { labels, datasets: [{ data: values, backgroundColor: 'rgba(34,211,238,0.2)', borderColor: '#22d3ee', borderWidth: 1.5, borderRadius: 6 }] },
      options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { ticks: { color: '#64748b', font: { size: 11 } }, grid: { display: false } }, y: { ticks: { color: '#64748b', font: { size: 11 }, stepSize: 1 }, grid: { color: 'rgba(255,255,255,0.05)' }, min: 0 } } }
    });
  }

  async function setKeterangan(siswaId, keterangan) {
    const tanggal = document.getElementById('tanggal-filter').value;
    const res = await fetch('/api/keterangan', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ siswa_id: siswaId, tanggal, keterangan })
    });
    const d = await res.json();
    if (d.status === 'berhasil') load(tanggal); // refresh dashboard
    else alert(d.pesan);
}

  async function loadJamBatas() {
    const res = await fetch('/api/settings/jam-batas');
    const d = await res.json();
    document.getElementById('jam-batas').value = d.jam_batas;
  }

  async function simpanJamBatas() {
    const jam = document.getElementById('jam-batas').value;
    const res = await fetch('/api/settings/jam-batas', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ jam })
    });
    const d = await res.json();
    if (d.status === 'berhasil') alert(`Jam batas berhasil diubah ke ${jam}`);
  }

  loadJamBatas();
  load(today);
</script>
</body>
</html>
