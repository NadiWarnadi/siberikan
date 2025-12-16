<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuktiSerahTerima extends Model
{
    use HasFactory;

    protected $table = 'bukti_serah_terima';

    protected $fillable = [
        'pengiriman_id',
        'nama_penerima',
        'waktu_terima',
        'foto_bukti',
        'catatan',
    ];

    protected $casts = [
        'waktu_terima' => 'datetime',
    ];

    // Relationships
    public function pengiriman()
    {
        return $this->belongsTo(Pengiriman::class, 'pengiriman_id');
    }
}
