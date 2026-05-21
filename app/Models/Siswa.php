<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $fillable = ['nama_panggilan', 'nama_lengkap', 'kelas'];

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'siswa_id');
    }
}
