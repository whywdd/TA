<?php

namespace App\Http\Controllers;

use App\Models\GajiModel; // Import model
use Illuminate\Http\Request;

class GajiController extends Controller
{
    public function index()
    {
        // Ambil data dari tabel karyawans termasuk usia dan jabatan
        $gajiKaryawan = GajiModel::whereNotNull('gaji')
            ->where('gaji', '>', 0)
            ->select('id', 'nama', 'usia', 'jabatan', 'gaji', 'created_at')
            ->get();
            
        return view('Gaji', compact('gajiKaryawan'));
    }

    // Tambahkan method untuk API
    public function show($id)
    {
        $karyawan = GajiModel::findOrFail($id);
        return response()->json($karyawan);
    }
}