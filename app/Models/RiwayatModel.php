<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiwayatModel extends Model
{
    use HasFactory;

    protected $table = 'riwayat';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'nama_user',
        'tanggal',
        'waktu',
        'aksi',
        'keterangan'
    ];

    // Relasi dengan model User
    public function user()
    {
        return $this->belongsTo(Login::class, 'user_id');
    }
} 