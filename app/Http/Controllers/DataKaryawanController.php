<?php

namespace App\Http\Controllers;

use App\Models\DataKaryawanModel;
use Illuminate\Http\Request;

class DataKaryawanController extends Controller
{
    public function index()
    {
        return view('DataKaryawan');
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'nama' => 'required|string|max:100',
                'usia' => 'required|integer|min:17|max:65',
                'jabatan' => 'required|string|max:50',
                'gaji' => 'required'
            ]);

            // Bersihkan format angka dari gaji
            $gaji = str_replace('.', '', $request->gaji);

            // Simpan data
            DataKaryawanModel::create([
                'nama' => $request->nama,
                'usia' => $request->usia,
                'jabatan' => $request->jabatan,
                'gaji' => $gaji
            ]);

            // Redirect ke halaman gaji dengan pesan sukses
            return redirect()->route('gaji.index')
                ->with('success', 'Data karyawan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data karyawan: ' . $e->getMessage());
        }
    }
}
