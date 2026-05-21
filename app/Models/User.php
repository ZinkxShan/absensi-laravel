<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['username', 'password', 'role', 'kelas'];

    protected $hidden = ['password'];

    protected $casts = [    // ← tambah di sini
        'id' => 'integer',
    ];

    public function getAuthIdentifierName(): string
    {
        return 'username';
    }
}