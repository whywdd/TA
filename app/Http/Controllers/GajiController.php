<?php

namespace App\Http\Controllers;

use App\Models\GajiModel; // Import model
use Illuminate\Http\Request;
use Carbon\Carbon;

class GajiController extends Controller
{
    public function index()
    {
        // Ambil data dari tabel karyawans
        $gajiKaryawan = GajiModel::whereNotNull('gaji')
            ->where('gaji', '>', 0)
            ->select('id', 'nama', 'tanggal_lahir', 'jabatan', 'gaji', 'created_at')
            ->get()
            ->map(function ($karyawan) {
                // Hitung usia dari timestamp
                $usia = Carbon::createFromTimestamp($karyawan->tanggal_lahir)->age;
                $karyawan->usia = $usia;
                return $karyawan;
            });
            
        return view('Gaji', compact('gajiKaryawan'));
    }

    // Tambahkan method untuk API
    public function show($id)
    {
        $karyawan = GajiModel::findOrFail($id);
        // Hitung usia untuk response
        $karyawan->usia = Carbon::createFromTimestamp($karyawan->tanggal_lahir)->age;
        return response()->json($karyawan);
    }

    public function edit($id)
    {
        $karyawan = GajiModel::findOrFail($id);
        // Konversi timestamp ke format date untuk form
        $karyawan->tanggal_lahir = Carbon::createFromTimestamp($karyawan->tanggal_lahir)->format('Y-m-d');
        return view('GajiEdit', compact('karyawan'));
    }

    public function update(Request $request, $id)
    {
        try {
            $karyawan = GajiModel::findOrFail($id);
            
            // Konversi tanggal lahir ke timestamp
            $tanggalLahir = Carbon::parse($request->tanggal_lahir)->timestamp;
            
            // Bersihkan format angka dari gaji
            $request->merge([
                'gaji' => str_replace('.', '', $request->gaji),
                'tanggal_lahir' => $tanggalLahir
            ]);

            $karyawan->update($request->all());
            return redirect()->route('gaji.index')
                ->with('success', 'Data gaji berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data gaji: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $karyawan = GajiModel::findOrFail($id);
            $karyawan->delete();
            return response()->json([
                'success' => true,
                'message' => 'Data gaji berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data gaji: ' . $e->getMessage()
            ]);
        }
    }
}