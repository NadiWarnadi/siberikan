<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityLog extends Model
{
    use HasFactory;

    protected $table = 'security_logs';
    protected $fillable = [
        'pengguna_id',
        'tipe_event',
        'deskripsi',
        'ip_address',
        'user_agent',
        'status_code',
        'request_url',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class);
    }
}
