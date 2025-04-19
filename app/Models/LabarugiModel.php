<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabarugiModel extends Model
{
    protected $table = 'laporan_transaksis';
    protected $guarded = ['id'];
    
    protected $casts = [
        'Tanggal' => 'date',
        'uang_masuk' => 'decimal:2',
        'uang_masuk2' => 'decimal:2',
        'uang_masuk3' => 'decimal:2',
        'uang_masuk4' => 'decimal:2',
        'uang_masuk5' => 'decimal:2',
        'uang_keluar' => 'decimal:2',
        'uang_keluar2' => 'decimal:2',
        'uang_keluar3' => 'decimal:2',
        'uang_keluar4' => 'decimal:2',
        'uang_keluar5' => 'decimal:2',
    ];
}
