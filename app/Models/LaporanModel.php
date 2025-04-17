<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanModel extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan
    protected $table = 'laporan_transaksis'; // Ganti dengan nama tabel yang sesuai

    // Primary key
    protected $primaryKey = 'id';

    // Menentukan apakah primary key adalah auto-increment
    public $incrementing = true;

    // Tipe data primary key
    protected $keyType = 'int';

    // Kolom yang dapat diisi
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
        'uang_masuk',
        'uang_masuk2',
        'uang_masuk3',
        'uang_masuk4',
        'uang_masuk5',
        'uang_keluar',
        'uang_keluar2',
        'uang_keluar3',
        'uang_keluar4',
        'uang_keluar5'
    ];

    // Timestamps
    public $timestamps = true;

    // Jika Anda ingin mengubah nama kolom timestamps
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

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
        'uang_keluar5' => 'decimal:2'
    ];
}
