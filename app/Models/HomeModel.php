<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeModel extends Model
{
    protected $table = 'laporan_transaksis';
    protected $guarded = [];
    
    protected $casts = [
        'Tanggal' => 'date',
        'uang_masuk' => 'decimal:2',
        'uang_keluar' => 'decimal:2'
    ];
}
