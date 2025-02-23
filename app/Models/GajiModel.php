<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiModel extends Model
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
        'kategori',
        'keterangan',
        'uang_masuk',
        'uang_keluar',
        'gaji'
    ];

    // Timestamps
    public $timestamps = true;

    // Jika Anda ingin mengubah nama kolom timestamps
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $casts = [
        'Tanggal' => 'date',
        'uang_masuk' => 'decimal:2',
        'uang_keluar' => 'decimal:2',
        'gaji' => 'decimal:2',
    ];
}
