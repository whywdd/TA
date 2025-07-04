<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiModel extends Model
{
    use HasFactory;

    // Nama tabel yang digunakan
    protected $table = 'karyawans';

    // Primary key
    protected $primaryKey = 'id';

    // Menentukan apakah primary key adalah auto-increment
    public $incrementing = true;

    // Tipe data primary key
    protected $keyType = 'int';

    // Kolom yang dapat diisi
    protected $fillable = [
        'nama',
        'tanggal_lahir',
        'jabatan',
        'gaji'
    ];

    // Timestamps
    public $timestamps = true;

    // Jika Anda ingin mengubah nama kolom timestamps
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $casts = [
        'gaji' => 'decimal:2',
        'tanggal_lahir' => 'integer'
    ];
}
