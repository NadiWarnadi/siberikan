<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penawaran extends Model
{
    use HasFactory;

    protected $table = 'penawarans';
    
    protected $fillable = [
        'kode_penawaran',
        'nelayan_id',
        'jenis_ikan_id',
        'jumlah_kg',
        'harga_per_kg',
        'kualitas',
        'lokasi_tangkapan',
        'kedalaman',
        'tanggal_tangkapan',
        'catatan',
        'foto_ikan',
        'status',
        'alasan_reject',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_tangkapan' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function nelayan()
    {
        return $this->belongsTo(Pengguna::class, 'nelayan_id');
    }

    public function jenisIkan()
    {
        return $this->belongsTo(MasterJenisIkan::class, 'jenis_ikan_id');
    }

    public function approver()
    {
        return $this->belongsTo(Pengguna::class, 'approved_by');
    }

    public function hasilTangkapan()
    {
        return $this->hasOne(HasilTangkapan::class, 'penawaran_id');
    }
}
