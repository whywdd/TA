<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UangMasukModel extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan
    protected $table = 'uang_masuk';

    // Primary key
    protected $primaryKey = 'id';

    // Menentukan apakah primary key adalah auto-increment
    public $incrementing = true;

    // Tipe data primary key
    protected $keyType = 'int';

    // Kolom yang dapat diisi
    protected $fillable = [
        'tanggal',
        'kategori',
        'keterangan',
        'total_uang',
    ];

    // Timestamps
    public $timestamps = true;

    // Jika Anda ingin mengubah nama kolom timestamps
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}