<?php

namespace App\Http\Controllers;

use App\Models\DataKaryawanModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

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
                'tanggal_lahir' => 'required|date',
                'jabatan' => 'required|string|max:50',
                'gaji' => 'required'
            ]);

            // Hitung usia
            $tanggalLahir = Carbon::parse($request->tanggal_lahir);
            $usia = $tanggalLahir->age;

            // Validasi usia
            if ($usia < 17 || $usia > 65) {
                return redirect()->back()
                    ->with('error', 'Usia harus antara 17-65 tahun')
                    ->withInput();
            }

            // Bersihkan format angka dari gaji
            $gaji = str_replace('.', '', $request->gaji);

            // Simpan data
            DataKaryawanModel::create([
                'nama' => $request->nama,
                'tanggal_lahir' => $tanggalLahir->timestamp, // Simpan sebagai timestamp
                'jabatan' => $request->jabatan,
                'gaji' => $gaji
            ]);

            // Redirect ke halaman gaji dengan pesan sukses
            return redirect()->route('gaji.index')
                ->with('success', 'Data karyawan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menambahkan data karyawan: ' . $e->getMessage())
                ->withInput();
        }
    }
}
