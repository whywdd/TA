<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataKaryawanModel extends Model
{
    protected $table = 'karyawans';
    
    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'jabatan',
        'gaji'
    ];

    // Jika ingin menggunakan timestamp (created_at & updated_at)
    public $timestamps = true;
}
