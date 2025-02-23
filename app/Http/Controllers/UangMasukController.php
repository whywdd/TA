<?php

namespace App\Http\Controllers;

use App\Models\UangMasukModel; // Import model
use Illuminate\Http\Request;

class UangMasukController extends Controller
{
    public function index()
    {
        return view('UangMasuk');
    }

    public function store(Request $request)
    {
        // Validasi data
        $request->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'total_uang' => 'required|numeric',
        ]);

        // Simpan data ke database
        UangMasukModel::create([
            'tanggal' => $request->tanggal,
            'kategori' => $request->kategori,
            'keterangan' => $request->keterangan,
            'total_uang' => $request->total_uang,
        ]);

        // Redirect atau kembali ke halaman dengan pesan sukses
        return redirect()->back()->with('success', 'Data Uang Masuk berhasil disimpan!');
    }
}