<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManajemenStok extends Model
{
    use HasFactory;

    protected $table = 'manajemen_stok';

    protected $fillable = [
        'kode_barang',
        'nama_barang',
        'satuan',
        'stok',
        'harga_beli',
        'akun_coa',
        'status_tahap',
        'deskripsi',
    ];
}
