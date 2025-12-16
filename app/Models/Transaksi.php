<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'transaksi';

    protected $fillable = [
        'kode_transaksi',
        'tengkulak_id',
        'pembeli_id',
        'tanggal_transaksi',
        'total_harga',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'total_harga' => 'decimal:2',
    ];

    // Relationships
    public function tengkulak()
    {
        return $this->belongsTo(Pengguna::class, 'tengkulak_id');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pengguna::class, 'pembeli_id');
    }

    public function details()
    {
        return $this->hasMany(DetailTransaksi::class, 'transaksi_id');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'transaksi_id');
    }

    public function retur()
    {
        return $this->hasMany(ReturBarang::class, 'transaksi_id');
    }
}
