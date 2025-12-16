<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTransaksi extends Model
{
    use HasFactory;

    protected $table = 'detail_transaksi';

    protected $fillable = [
        'transaksi_id',
        'hasil_tangkapan_id',
        'jumlah_kg',
        'harga_satuan',
        'subtotal',
    ];

    protected $casts = [
        'jumlah_kg' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    // Relationships
    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id');
    }

    public function hasilTangkapan()
    {
        return $this->belongsTo(HasilTangkapan::class, 'hasil_tangkapan_id');
    }
}
