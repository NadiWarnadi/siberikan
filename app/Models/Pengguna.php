<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Pengguna extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'peran',
        'alamat',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    // Relationships
    public function hasilTangkapan()
    {
        return $this->hasMany(HasilTangkapan::class, 'nelayan_id');
    }

    public function transaksiSebagaiTengkulak()
    {
        return $this->hasMany(Transaksi::class, 'tengkulak_id');
    }

    public function transaksiSebagaiPembeli()
    {
        return $this->hasMany(Transaksi::class, 'pembeli_id');
    }

    public function pengiriman()
    {
        return $this->hasMany(Pengiriman::class, 'sopir_id');
    }

    public function retur()
    {
        return $this->hasMany(ReturBarang::class, 'pembeli_id');
    }
}
