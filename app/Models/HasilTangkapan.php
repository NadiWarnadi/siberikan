<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HasilTangkapan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'hasil_tangkapan';

    protected $fillable = [
        'nelayan_id',
        'penawaran_id',
        'jenis_ikan_id',
        'berat',
        'grade',
        'harga_per_kg',
        'tanggal_tangkap',
        'status',
        'catatan',
        'foto_ikan',
    ];

    protected $casts = [
        'tanggal_tangkap' => 'date',
        'berat' => 'decimal:2',
        'harga_per_kg' => 'decimal:2',
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

    public function penawaran()
    {
        return $this->belongsTo(Penawaran::class, 'penawaran_id');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(DetailTransaksi::class, 'hasil_tangkapan_id');
    }
}
