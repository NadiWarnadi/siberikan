<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengiriman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengiriman';

    protected $fillable = [
        'transaksi_id',
        'sopir_id',
        'nomor_resi',
        'tanggal_kirim',
        'tanggal_estimasi',
        'tanggal_diterima',
        'alamat_tujuan',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_kirim' => 'date',
        'tanggal_estimasi' => 'date',
        'tanggal_diterima' => 'date',
    ];

    // Relationships
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function sopir()
    {
        return $this->belongsTo(Pengguna::class, 'sopir_id');
    }

    public function buktiSerahTerima()
    {
        return $this->hasOne(BuktiSerahTerima::class, 'pengiriman_id');
    }
}
