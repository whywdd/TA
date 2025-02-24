<?php

namespace App\Http\Controllers;

use App\Models\GajiModel; // Import model
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index()
    {
        // Ambil hanya data yang memiliki nilai gaji dan tidak null
        $gajiKaryawan = GajiModel::whereNotNull('gaji')
            ->where('gaji', '>', 0)
            ->select('nama_karyawan', 'gaji') // Ambil kolom nama_karyawan dan gaji
            ->get();
            
        return view('Gaji', compact('gajiKaryawan'));
    }
}