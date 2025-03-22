<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Login extends Model
{
    use HasFactory;
    
    protected $table = 'penggunas';
    
    protected $fillable = [
        'nama',
        'email',
        'password',
        'tipe_pengguna'
    ];
}
