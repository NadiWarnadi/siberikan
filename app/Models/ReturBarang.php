<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReturBarang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'retur_barang';

    protected $fillable = [
        'transaksi_id',
        'pembeli_id',
        'tanggal_retur',
        'alasan',
        'keterangan',
        'jumlah_pengembalian',
        'status',
    ];

    protected $casts = [
        'tanggal_retur' => 'date',
        'jumlah_pengembalian' => 'decimal:2',
    ];

    // Relationships
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function pembeli()
    {
        return $this->belongsTo(Pengguna::class, 'pembeli_id');
    }
}
