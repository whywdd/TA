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

    public function edit($id)
    {
        $karyawan = GajiModel::findOrFail($id);
        return view('GajiEdit', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        $karyawan = GajiModel::findOrFail($id);
        $karyawan->update($request->all());
        return redirect()->route('gaji.index')->with('success', 'Data gaji berhasil diperbarui');
    }

    public function destroy($id)
    {
        $karyawan = GajiModel::findOrFail($id);
        $karyawan->delete();
        return response()->json(['success' => true]);
    }
}