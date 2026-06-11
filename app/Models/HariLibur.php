<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $table = 'hari_libur';
    protected $fillable = ['tanggal', 'tanggal_akhir', 'keterangan'];
    protected $casts = [
        'tanggal'       => 'date',
        'tanggal_akhir' => 'date',
    ];
}
