<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MasterJenisIkan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'master_jenis_ikan';

    protected $fillable = [
        'nama_ikan',
        'nama_latin',
        'deskripsi',
    ];

    // Relationships
    public function hasilTangkapan()
    {
        return $this->hasMany(HasilTangkapan::class, 'jenis_ikan_id');
    }
}
