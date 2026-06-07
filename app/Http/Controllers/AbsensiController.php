<?php
namespace App\Http\Controllers;

use App\Models\Absensi;
use App\Models\Siswa;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Maatwebsite\Excel\Facades\Excel;

class AbsensiController extends Controller
{
    // ── Halaman ──────────────────────────────────────────────────────────────────

    public function halamanMasuk()   { return view('masuk'); }
    public function halamanKeluar()  { return view('keluar'); }
    public function halamanDashboard() { return view('dashboard'); }
    public function halamanKelola()  { return view('kelola'); }

    // ── Helper ───────────────────────────────────────────────────────────────────

    private function getJamBatas(): string
    {
        return DB::table('settings')->where('kunci', 'jam_batas_masuk')->value('nilai') ?? '07:15:00';
    }

    // ── Scan Masuk ───────────────────────────────────────────────────────────────

    public function scanMasuk(Request $request): JsonResponse
    {
        $kode  = strtolower(trim($request->input('kode', '')));
        if (!$kode) return response()->json(['status' => 'error', 'pesan' => 'Kode kosong']);

        $siswa = Siswa::where('nama_panggilan', $kode)->first();
        if (!$siswa) return response()->json(['status' => 'error', 'pesan' => "Siswa \"{$kode}\" tidak ditemukan"]);

        $cekLibur = $this->isHariLibur(today()->toDateString());
        if ($cekLibur['libur']) {
            return response()->json([
                'status' => 'error',
                'pesan'  => "Hari ini libur: {$cekLibur['keterangan']}",
            ]);
        }

        $hariIni  = today()->toDateString();
        $existing = Absensi::where('siswa_id', $siswa->id)->where('tanggal', $hariIni)->first();

        if ($existing) {
            return response()->json([
                'status' => 'sudah',
                'nama'   => $siswa->nama_lengkap,
                'kelas'  => $siswa->kelas,
                'waktu'  => substr($existing->waktu_masuk, 0, 5),
                'pesan'  => "{$siswa->nama_lengkap} sudah absen masuk pukul " . substr($existing->waktu_masuk, 0, 5),
            ]);
        }

        $jamBatas    = $this->getJamBatas();
        $waktuNow    = now()->format('H:i:s');
        $isTerlambat = $waktuNow > $jamBatas;

        Absensi::create([
            'siswa_id'    => $siswa->id,
            'tanggal'     => $hariIni,
            'waktu_masuk' => $waktuNow,
            'status'      => $isTerlambat ? 'terlambat' : 'masuk',
        ]);

        return response()->json([
            'status'    => 'berhasil',
            'terlambat' => $isTerlambat,
            'nama'      => $siswa->nama_lengkap,
            'kelas'     => $siswa->kelas,
            'waktu'     => $waktuNow,
            'jam_batas' => substr($jamBatas, 0, 5),
            'pesan'     => $isTerlambat
                ? "⚠️ {$siswa->nama_lengkap} TERLAMBAT! Batas masuk " . substr($jamBatas, 0, 5)
                : "Selamat datang, {$siswa->nama_lengkap}!",
        ]);
    }

    // ── Scan Keluar ──────────────────────────────────────────────────────────────

    public function scanKeluar(Request $request): JsonResponse
    {
        $kode  = strtolower(trim($request->input('kode', '')));
        if (!$kode) return response()->json(['status' => 'error', 'pesan' => 'Kode kosong']);

        $siswa = Siswa::where('nama_panggilan', $kode)->first();
        if (!$siswa) return response()->json(['status' => 'error', 'pesan' => "Siswa \"{$kode}\" tidak ditemukan"]);

        $cekLibur = $this->isHariLibur(today()->toDateString());
        if ($cekLibur['libur']) {
            return response()->json([
                'status' => 'error',
                'pesan'  => "Hari ini libur: {$cekLibur['keterangan']}",
            ]);
        }

        $hariIni  = today()->toDateString();
        $existing = Absensi::where('siswa_id', $siswa->id)->where('tanggal', $hariIni)->first();

        if (!$existing) {
            return response()->json([
                'status' => 'error',
                'nama'   => $siswa->nama_lengkap,
                'kelas'  => $siswa->kelas,
                'pesan'  => "{$siswa->nama_lengkap} belum absen masuk hari ini!",
            ]);
        }

        if ($existing->waktu_keluar !== null) {
            return response()->json([
                'status' => 'sudah',
                'nama'   => $siswa->nama_lengkap,
                'kelas'  => $siswa->kelas,
                'waktu'  => substr($existing->waktu_keluar, 0, 5),
                'pesan'  => "{$siswa->nama_lengkap} sudah absen keluar pukul " . substr($existing->waktu_keluar, 0, 5),
            ]);
        }

        $waktuNow     = now()->format('H:i:s');
        $statusKeluar = $existing->status === 'terlambat' ? 'terlambat_lengkap' : 'lengkap';

        $existing->update(['waktu_keluar' => $waktuNow, 'status' => $statusKeluar]);

        return response()->json([
            'status'      => 'berhasil',
            'nama'        => $siswa->nama_lengkap,
            'kelas'       => $siswa->kelas,
            'waktu'       => $waktuNow,
            'waktu_masuk' => substr($existing->waktu_masuk, 0, 5),
            'pesan'       => "Sampai jumpa, {$siswa->nama_lengkap}!",
        ]);
    }

    // ── Dashboard ────────────────────────────────────────────────────────────────

    public function apiDashboard(Request $request): JsonResponse
{
    $tanggal  = $request->query('tanggal', today()->toDateString());
    $infoLibur = $this->isHariLibur($tanggal);
    $user     = auth()->user();
    $isAdmin  = $user->role === 'admin';

    // Sekretaris hanya lihat data kelasnya
    $filterKelas = (!$isAdmin && $user->kelas) ? $user->kelas : null;

    $totalSiswa = $filterKelas
        ? Siswa::where('kelas', $filterKelas)->count()
        : Siswa::count();

    $queryAbsensi = Absensi::where('tanggal', $tanggal)
        ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($s) => $s->where('kelas', $filterKelas)));

    $hadir     = (clone $queryAbsensi)->whereNotNull('waktu_masuk')->count();
    $terlambat = (clone $queryAbsensi)->whereIn('status', ['terlambat', 'terlambat_lengkap'])->count();
    $lengkap   = (clone $queryAbsensi)->whereIn('status', ['lengkap', 'terlambat_lengkap'])->count();
    $izin      = (clone $queryAbsensi)->where('keterangan', 'izin')->count();
    $sakit     = (clone $queryAbsensi)->where('keterangan', 'sakit')->count();
    $alpha     = (clone $queryAbsensi)->where('keterangan', 'alpha')->count();

    $daftarHadir = (clone $queryAbsensi)
        ->whereNotNull('waktu_masuk')
        ->with('siswa')->orderBy('waktu_masuk')->get()
        ->map(fn($a) => [
            'nama_lengkap' => $a->siswa->nama_lengkap,
            'kelas'        => $a->siswa->kelas,
            'waktu_masuk'  => substr($a->waktu_masuk, 0, 5),
            'waktu_keluar' => $a->waktu_keluar ? substr($a->waktu_keluar, 0, 5) : null,
            'terlambat'    => str_contains($a->status, 'terlambat'),
            'status'       => in_array($a->status, ['lengkap', 'terlambat_lengkap']) ? 'lengkap' : 'masuk',
        ]);

    // Siswa tidak hadir — include keterangan
    $tidakHadirQuery = Siswa::when($filterKelas, fn($q) => $q->where('kelas', $filterKelas))
        ->whereNotIn('id', function ($q) use ($tanggal) {
            $q->select('siswa_id')->from('absensi')
              ->where('tanggal', $tanggal)
              ->whereNotNull('waktu_masuk');
        })->orderBy('kelas')->orderBy('nama_lengkap');

    $tidakHadir = $tidakHadirQuery->get()->map(function ($s) use ($tanggal) {
        $absensi = Absensi::where('siswa_id', $s->id)->where('tanggal', $tanggal)->first();
        return [
            'id'           => $s->id,
            'nama_lengkap' => $s->nama_lengkap,
            'kelas'        => $s->kelas,
            'keterangan'   => $absensi?->keterangan ?? null, // null = belum ada keterangan
        ];
    });

    $rekapKelas = Siswa::select('kelas', DB::raw('COUNT(*) as total'))
        ->when($filterKelas, fn($q) => $q->where('kelas', $filterKelas))
        ->groupBy('kelas')->orderBy('kelas')->get()  
        ->map(fn($row) => [
            'kelas' => $row->kelas,
            'total' => $row->total,
            'hadir' => Absensi::whereHas('siswa', fn($q) => $q->where('kelas', $row->kelas))
                ->where('tanggal', $tanggal)->whereNotNull('waktu_masuk')->count(),
        ]);

    $tren = Absensi::select('tanggal', DB::raw('COUNT(*) as jumlah'))
        ->whereNotNull('waktu_masuk')
        ->when($filterKelas, fn($q) => $q->whereHas('siswa', fn($s) => $s->where('kelas', $filterKelas)))
        ->whereBetween('tanggal', [Carbon::parse($tanggal)->subDays(6)->toDateString(), $tanggal])
        ->groupBy('tanggal')->orderBy('tanggal')->get()
        ->map(fn($t) => ['tanggal' => (string)$t->tanggal, 'jumlah' => $t->jumlah]);

    return response()->json([
        'total_siswa'      => $totalSiswa,
        'hadir'            => $hadir,
        'terlambat'        => $terlambat,
        'lengkap'          => $lengkap,
        'belum_keluar'     => $hadir - $lengkap,
        'tidak_hadir'      => $totalSiswa - $hadir,
        'izin'             => $izin,
        'sakit'            => $sakit,
        'alpha'            => $alpha,
        'daftar_hadir'     => $daftarHadir,
        'rekap_kelas'      => $rekapKelas,
        'tidak_hadir_list' => $tidakHadir,
        'tren'             => $tren,
        'is_admin'         => $isAdmin,
        'user_kelas'       => $user->kelas,
        'is_hari_libur'    => $infoLibur['libur'],
        'ket_libur'        => $infoLibur['keterangan'],
    ]);
}

    public function getUsers(): JsonResponse
{
    $users = \App\Models\User::select('id', 'username', 'role', 'kelas')
        ->orderBy('role')
        ->orderBy('username')
        ->get();
    return response()->json($users);
}

    // ── Kelola Siswa ─────────────────────────────────────────────────────────────

    public function getSiswa(): JsonResponse
    {
        return response()->json(Siswa::orderBy('kelas')->orderBy('nama_lengkap')->get());
    }

    public function tambahSiswa(Request $request): JsonResponse
    {
    $panggilan = strtolower(trim($request->input('nama_panggilan', '')));
    $lengkap   = trim($request->input('nama_lengkap', ''));
    $kelas     = trim($request->input('kelas', ''));
    $jurusan   = trim($request->input('jurusan', ''));

    if (!$panggilan || !$lengkap || !$kelas)
        return response()->json(['status' => 'error', 'pesan' => 'Semua field wajib diisi']);

    if (Siswa::where('nama_panggilan', $panggilan)->exists())
        return response()->json(['status' => 'error', 'pesan' => "Nama panggilan \"{$panggilan}\" sudah ada"]);

    Siswa::create(['nama_panggilan' => $panggilan, 'nama_lengkap' => $lengkap, 'kelas' => $kelas, 'jurusan' => $jurusan]);
    return response()->json(['status' => 'berhasil', 'pesan' => 'Siswa berhasil ditambahkan']);
    }

    public function hapusSiswa(int $id): JsonResponse
    {
        Siswa::findOrFail($id)->delete();
        return response()->json(['status' => 'berhasil']);
    }

    // ── Import Siswa (CSV/Excel) ─────────────────────────────────────────────
    public function importSiswa(Request $request): JsonResponse
{
    if (!$request->hasFile('file')) {
        return response()->json(['status' => 'error', 'pesan' => 'File tidak ditemukan']);
    }

    $file = $request->file('file');
    $ext  = strtolower($file->getClientOriginalExtension());

    if (!in_array($ext, ['csv', 'xlsx'])) {
        return response()->json(['status' => 'error', 'pesan' => 'Format file harus CSV atau Excel (.xlsx)']);
    }

    $rows = [];

    if ($ext === 'csv') {
        // Baca CSV
        $handle = fopen($file->getRealPath(), 'r');
        $header = null;
        while (($row = fgetcsv($handle)) !== false) {
            if (!$header) {
                $header = $row;
                continue;
            }
            if (count($row) >= 4) {
                $rows[] = array_combine($header, $row);
            }
        }
        fclose($handle);
    } else {
        // Baca Excel menggunakan maatwebsite/excel
        $data = \Maatwebsite\Excel\Facades\Excel::toArray([], $file);
        $sheet = $data[0] ?? [];
        $header = null;
        foreach ($sheet as $row) {
            if (!$header) {
                $header = array_map('strtolower', array_map('trim', $row));
                continue;
            }
            if (count($row) >= 4) {
                $rows[] = array_combine($header, $row);
            }
        }
    }

    if (empty($rows)) {
        return response()->json(['status' => 'error', 'pesan' => 'File kosong atau format tidak sesuai']);
    }

    $berhasil = 0;
    $diskip   = 0;
    $error    = 0;

    foreach ($rows as $row) {
        $panggilan = strtolower(trim($row['nama_panggilan'] ?? ''));
        $lengkap   = trim($row['nama_lengkap'] ?? '');
        $kelas     = trim($row['kelas'] ?? '');
        $jurusan   = trim($row['jurusan'] ?? '');

        // Skip baris kosong
        if (!$panggilan || !$lengkap || !$kelas) {
            $error++;
            continue;
        }

        // Skip kalau nama panggilan sudah ada
        if (Siswa::where('nama_panggilan', $panggilan)->exists()) {
            $diskip++;
            continue;
        }

        Siswa::create([
            'nama_panggilan' => $panggilan,
            'nama_lengkap'   => $lengkap,
            'kelas'          => $kelas,
            'jurusan'        => $jurusan ?: null,
        ]);
        $berhasil++;
    }

    return response()->json([
        'status'   => 'berhasil',
        'pesan'    => "Import selesai: {$berhasil} ditambahkan, {$diskip} diskip (sudah ada), {$error} error",
        'berhasil' => $berhasil,
        'diskip'   => $diskip,
        'error'    => $error,
    ]);
}

public function downloadTemplate()
{
    // Buat file xlsx menggunakan maatwebsite/excel
    return \Maatwebsite\Excel\Facades\Excel::download(
        new class implements \Maatwebsite\Excel\Concerns\FromArray,
                              \Maatwebsite\Excel\Concerns\WithHeadings,
                              \Maatwebsite\Excel\Concerns\WithStyles
        {
            public function array(): array
            {
                return [
                    ['budi',  'Budi Santoso',  'XI-RPL',  'Rekayasa Perangkat Lunak'],
                    ['ani',   'Ani Rahayu',    'XI-RPL',  'Rekayasa Perangkat Lunak'],
                    ['doni',  'Doni Prasetyo', 'XI-TJKT', 'Teknik Jaringan Komputer dan Telekomunikasi'],
                ];
            }

            public function headings(): array
            {
                return ['nama_panggilan', 'nama_lengkap', 'kelas', 'jurusan'];
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                // Bold header
                return [
                    1 => ['font' => ['bold' => true]],
                ];
            }
        },
        'template_import_siswa.xlsx',
        \Maatwebsite\Excel\Excel::XLSX
    );
}
    
    // ── Tambah User  ─────────────────────────────────────────
    public function tambahUser(Request $request): JsonResponse
{
    $username = trim($request->input('username', ''));
    $password = trim($request->input('password', ''));
    $role     = trim($request->input('role', 'user'));
    $kelas    = trim($request->input('kelas', ''));

    if (!$username || !$password)
        return response()->json(['status' => 'error', 'pesan' => 'Username dan password wajib diisi']);

    if (strlen($password) < 6)
        return response()->json(['status' => 'error', 'pesan' => 'Password minimal 6 karakter']);

    if ($role === 'sekretaris' && !$kelas)
        return response()->json(['status' => 'error', 'pesan' => 'Sekretaris wajib memiliki kelas']);

    if (\App\Models\User::where('username', $username)->exists())
        return response()->json(['status' => 'error', 'pesan' => "Username \"{$username}\" sudah ada"]);

    \App\Models\User::create([
        'username' => $username,
        'password' => \Illuminate\Support\Facades\Hash::make($password),
        'role'     => $role,
        'kelas'    => $role === 'sekretaris' ? $kelas : null,
    ]);

    return response()->json(['status' => 'berhasil', 'pesan' => 'User berhasil ditambahkan']);
}

public function hapusUser(int $id): JsonResponse
{
    $user = \App\Models\User::findOrFail($id);

    if ($user->id === auth()->id())
        return response()->json(['status' => 'error', 'pesan' => 'Tidak bisa menghapus akun sendiri']);

    $user->delete();
    return response()->json(['status' => 'berhasil']);
}

    public function inputKeterangan(Request $request): JsonResponse
{
    $siswaId    = $request->input('siswa_id');
    $tanggal    = $request->input('tanggal');
    $keterangan = $request->input('keterangan'); // izin, sakit, alpha

    $user = auth()->user();

    // Validasi: sekretaris hanya bisa input untuk kelasnya sendiri
    if ($user->role === 'sekretaris') {
        $siswa = Siswa::findOrFail($siswaId);
        if ($siswa->kelas !== $user->kelas) {
            return response()->json([
                'status' => 'error',
                'pesan'  => 'Anda hanya bisa input keterangan untuk kelas ' . $user->kelas
            ]);
        }
    }

    // Cek apakah sudah hadir (tidak boleh input keterangan kalau sudah scan)
    $sudahHadir = Absensi::where('siswa_id', $siswaId)
        ->where('tanggal', $tanggal)
        ->whereNotNull('waktu_masuk')
        ->exists();

    if ($sudahHadir) {
        return response()->json([
            'status' => 'error',
            'pesan'  => 'Siswa sudah hadir, tidak bisa diberi keterangan'
        ]);
    }

    // Update atau buat record absensi dengan keterangan
    Absensi::updateOrCreate(
        ['siswa_id' => $siswaId, 'tanggal' => $tanggal],
        ['status' => 'tidak_hadir', 'keterangan' => $keterangan]
    );

    return response()->json(['status' => 'berhasil', 'pesan' => 'Keterangan berhasil disimpan']);
}

    // ── QR Code ──────────────────────────────────────────────────────────────────

    public function getQrCode(string $kode): JsonResponse
{
    // Ganti png → svg
    $svg = QrCode::format('svg')->size(200)->errorCorrection('M')->generate($kode);
    $base64 = 'data:image/svg+xml;base64,' . base64_encode($svg);
    return response()->json(['qrcode' => $base64]);
}

    // ── Settings ─────────────────────────────────────────────────────────────────

    public function getJamBatasApi(): JsonResponse
    {
        return response()->json(['jam_batas' => substr($this->getJamBatas(), 0, 5)]);
    }

    public function setJamBatas(Request $request): JsonResponse
    {
        $jam = trim($request->input('jam', ''));
        if (!preg_match('/^\d{2}:\d{2}$/', $jam))
            return response()->json(['status' => 'error', 'pesan' => 'Format jam tidak valid, gunakan HH:MM']);

        DB::table('settings')->updateOrInsert(
            ['kunci' => 'jam_batas_masuk'],
            ['nilai' => $jam . ':00', 'updated_at' => now()]
        );
        return response()->json(['status' => 'berhasil', 'jam_batas' => $jam]);
    }

    // ── Hari Libur ───────────────────────────────────────────────────────────────

    private function isHariLibur(string $tanggal): array
    {
    // Cek Sabtu (6) dan Minggu (0)
    $dayOfWeek = date('w', strtotime($tanggal));
    if ($dayOfWeek == 0) return ['libur' => true, 'keterangan' => 'Hari Minggu'];
    if ($dayOfWeek == 6) return ['libur' => true, 'keterangan' => 'Hari Sabtu'];

    // Cek hari libur khusus
    $hariLibur = \App\Models\HariLibur::where('tanggal', $tanggal)->first();
    if ($hariLibur) return ['libur' => true, 'keterangan' => $hariLibur->keterangan];

    return ['libur' => false, 'keterangan' => ''];
}

    public function getHariLibur(): JsonResponse
    {
    $list = \App\Models\HariLibur::orderBy('tanggal')->get()
        ->map(fn($h) => [
            'id'          => $h->id,
            'tanggal'     => $h->tanggal->format('Y-m-d'),
            'tanggal_fmt' => $h->tanggal->translatedFormat('d F Y'),
            'keterangan'  => $h->keterangan,
        ]);
    return response()->json($list);
    }

    public function tambahHariLibur(Request $request): JsonResponse
    {
        $tanggal    = trim($request->input('tanggal', ''));
        $keterangan = trim($request->input('keterangan', ''));

        if (!$tanggal || !$keterangan)
            return response()->json(['status' => 'error', 'pesan' => 'Tanggal dan keterangan wajib diisi']);

        if (\App\Models\HariLibur::where('tanggal', $tanggal)->exists())
            return response()->json(['status' => 'error', 'pesan' => 'Tanggal ini sudah ada di daftar hari libur']);

        \App\Models\HariLibur::create(['tanggal' => $tanggal, 'keterangan' => $keterangan]);
        return response()->json(['status' => 'berhasil', 'pesan' => 'Hari libur berhasil ditambahkan']);
    }

    public function hapusHariLibur(int $id): JsonResponse
    {
        \App\Models\HariLibur::findOrFail($id)->delete();
        return response()->json(['status' => 'berhasil']);
    }

}