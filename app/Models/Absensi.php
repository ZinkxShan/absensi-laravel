<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    protected $fillable = ['siswa_id', 'tanggal', 'waktu_masuk', 'waktu_keluar', 'status', 'keterangan'];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function isLengkap(): bool
    {
        return in_array($this->status, ['lengkap', 'terlambat_lengkap']);
    }
}
