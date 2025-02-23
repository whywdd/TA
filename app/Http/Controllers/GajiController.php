<?php

namespace App\Http\Controllers;

use App\Models\GajiModel; // Import model
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index()
    {
        // Ambil semua data gaji dari database
        $gajiKaryawan = GajiModel::all(); // Anda bisa menambahkan filter atau pagination jika diperlukan
        return view('Gaji', compact('gajiKaryawan')); // Kirim data ke view
    }
}