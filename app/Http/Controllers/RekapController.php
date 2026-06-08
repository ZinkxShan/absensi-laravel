<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RekapController extends Controller
{
    public function halaman()
    {
        return view('rekap');
    }

    public function apiRekap(Request $request)
    {
        $mode     = $request->query('mode', 'bulan');
        $tahun    = (int) $request->query('tahun', date('Y'));
        $bulan    = (int) $request->query('bulan', date('n'));
        $semester = (int) $request->query('semester', 1);

        $user = Auth::user();

        // Tentukan range tanggal
        if ($mode === 'bulan') {
            $dari    = "$tahun-" . str_pad($bulan, 2, '0', STR_PAD_LEFT) . "-01";
            $sampai  = date('Y-m-t', strtotime($dari));
            $label   = $this->namaBulan($bulan) . " $tahun";
        } elseif ($mode === 'semester') {
            if ($semester == 1) {
                $dari   = "$tahun-07-01";
                $sampai = ($tahun + 1) . "-12-31";
                $label  = "Semester 1 — $tahun/" . ($tahun + 1);
            } else {
                $dari   = ($tahun) . "-01-01";
                $sampai = ($tahun) . "-06-30";
                $label  = "Semester 2 — " . ($tahun - 1) . "/$tahun";
            }
        } else {
            // tahun ajaran: Juli tahun ini — Juni tahun depan
            $dari   = "$tahun-07-01";
            $sampai = ($tahun + 1) . "-06-30";
            $label  = "Tahun Ajaran $tahun/" . ($tahun + 1);
        }

        // Hitung total hari efektif (senin-jumat, bukan hari libur)
        $hariEfektif = $this->hitungHariEfektif($dari, $sampai);

        // Query siswa (filter kelas jika sekretaris)
        $query = DB::table('siswa')->orderBy('kelas')->orderBy('nama_lengkap');
        if ($user->role === 'sekretaris') {
            $query->where('kelas', $user->kelas);
        }
        $siswas = $query->get();

        $rekap = [];

        foreach ($siswas as $siswa) {
            // Ambil semua absensi siswa dalam range
            $absensi = DB::table('absensi')
                ->where('siswa_id', $siswa->id)
                ->whereBetween('tanggal', [$dari, $sampai])
                ->get();

            $hadir      = $absensi->whereIn('status', ['masuk', 'lengkap'])->count();
            $terlambat  = $absensi->whereIn('status', ['terlambat', 'terlambat_lengkap'])->count();
            $izin       = $absensi->where('keterangan', 'izin')->count();
            $sakit      = $absensi->where('keterangan', 'sakit')->count();
            $alpha      = $absensi->where('keterangan', 'alpha')->count();
            $totalHadir = $hadir + $terlambat;
            $tidakHadir = $hariEfektif - $totalHadir - $izin - $sakit - $alpha;
            $tidakHadir = max(0, $tidakHadir);
            $persen     = $hariEfektif > 0
                ? round(($totalHadir / $hariEfektif) * 100, 1)
                : 0;

            $rekap[] = [
                'id'           => $siswa->id,
                'nama_lengkap' => $siswa->nama_lengkap,
                'kelas'        => $siswa->kelas,
                'hadir'        => $hadir,
                'terlambat'    => $terlambat,
                'izin'         => $izin,
                'sakit'        => $sakit,
                'alpha'        => $alpha,
                'tidak_hadir'  => $tidakHadir,
                'total_hadir'  => $totalHadir,
                'persen'       => $persen,
            ];
        }

        return response()->json([
            'label'         => $label,
            'dari'          => $dari,
            'sampai'        => $sampai,
            'hari_efektif'  => $hariEfektif,
            'rekap'         => $rekap,
        ]);
    }

    public function exportExcel(Request $request)
{
    $mode     = $request->query('mode', 'bulan');
    $tahun    = (int) $request->query('tahun', date('Y'));
    $bulan    = (int) $request->query('bulan', date('n'));
    $semester = (int) $request->query('semester', 1);

    // Ambil data rekap (reuse logika apiRekap)
    $data = json_decode($this->apiRekap($request)->getContent(), true);

    $label   = $data['label'];
    $rekap   = $data['rekap'];
    $hariEfektif = $data['hari_efektif'];

    return \Maatwebsite\Excel\Facades\Excel::download(
        new class($label, $rekap, $hariEfektif) implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithHeadings,
            \Maatwebsite\Excel\Concerns\WithStyles,
            \Maatwebsite\Excel\Concerns\WithColumnWidths,
            \Maatwebsite\Excel\Concerns\WithTitle
        {
            public function __construct(
                private string $label,
                private array  $rekap,
                private int    $hariEfektif
            ) {}

            public function title(): string
            {
                return 'Rekap Absensi';
            }

            public function headings(): array
            {
                return [
                    ['REKAP ABSENSI — ' . strtoupper($this->label)],
                    ['Hari Efektif: ' . $this->hariEfektif . ' hari'],
                    [],
                    ['No', 'Nama Lengkap', 'Kelas', 'Hadir', 'Terlambat',
                     'Izin', 'Sakit', 'Alpha', 'Tidak Hadir', 'Total Hadir', '% Kehadiran'],
                ];
            }

            public function array(): array
            {
                $rows = [];
                foreach ($this->rekap as $i => $r) {
                    $rows[] = [
                        $i + 1,
                        $r['nama_lengkap'],
                        $r['kelas'],
                        $r['hadir'],
                        $r['terlambat'],
                        $r['izin'],
                        $r['sakit'],
                        $r['alpha'],
                        $r['tidak_hadir'],
                        $r['total_hadir'],
                        $r['persen'] . '%',
                    ];
                }
                return $rows;
            }

            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
            {
                return [
                    1 => ['font' => ['bold' => true, 'size' => 13]],
                    4 => ['font' => ['bold' => true],
                          'fill' => ['fillType' => 'solid',
                                     'startColor' => ['rgb' => '1a2235']],
                          'font' => ['bold' => true, 'color' => ['rgb' => 'f1f5f9']]],
                ];
            }

            public function columnWidths(): array
            {
                return [
                    'A' => 5,
                    'B' => 30,
                    'C' => 12,
                    'D' => 8,
                    'E' => 12,
                    'F' => 8,
                    'G' => 8,
                    'H' => 8,
                    'I' => 12,
                    'J' => 12,
                    'K' => 14,
                ];
            }
        },
        'rekap_absensi_' . str_replace(' ', '_', strtolower($label)) . '.xlsx',
        \Maatwebsite\Excel\Excel::XLSX
    );
}

    // ── Helpers ──────────────────────────────────────────────────

    private function hitungHariEfektif(string $dari, string $sampai): int
    {
        $hariLibur = DB::table('hari_libur')
            ->whereBetween('tanggal', [$dari, $sampai])
            ->pluck('tanggal')
            ->map(fn($t) => substr($t, 0, 10))
            ->toArray();

        $count   = 0;
        $current = strtotime($dari);
        $end     = strtotime($sampai);

        while ($current <= $end) {
            $dow    = (int) date('N', $current); // 1=Sen, 7=Min
            $tgl    = date('Y-m-d', $current);
            $bukan  = ($dow >= 6); // Sabtu/Minggu
            $libur  = in_array($tgl, $hariLibur);

            if (!$bukan && !$libur) {
                $count++;
            }
            $current = strtotime('+1 day', $current);
        }

        return $count;
    }

    private function namaBulan(int $bulan): string
    {
        $nama = [
            1=>'Januari', 2=>'Februari', 3=>'Maret', 4=>'April',
            5=>'Mei', 6=>'Juni', 7=>'Juli', 8=>'Agustus',
            9=>'September', 10=>'Oktober', 11=>'November', 12=>'Desember'
        ];
        return $nama[$bulan] ?? '';
    }
}