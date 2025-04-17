<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RekeningModel extends Model
{
    protected $table = 'laporan_transaksis';
    
    protected $fillable = [
        'Tanggal',
        'kode',
        'kode2',
        'kode3',
        'kode4',
        'kode5',
        'kategori',
        'kategori2',
        'kategori3',
        'kategori4',
        'kategori5',
        'keterangan',
        'nama_karyawan',
        'uang_masuk',
        'uang_masuk2',
        'uang_masuk3',
        'uang_masuk4',
        'uang_masuk5',
        'uang_keluar',
        'uang_keluar2',
        'uang_keluar3',
        'uang_keluar4',
        'uang_keluar5',
        'gaji'
    ];

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
        'gaji' => 'decimal:2'
    ];
}
