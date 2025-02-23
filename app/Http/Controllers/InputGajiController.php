<?php

namespace App\Http\Controllers;

use App\Models\InputGajiModel; // Import model
use Illuminate\Http\Request;

class InputGajiController extends Controller
{
    public function index()
    {
        return view('InputGaji');
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'Tanggal' => 'required|date',
                'kategori' => 'required|string',
                'nama_karyawan' => 'required|string',
                'gaji' => 'required|string',
            ]);

            // Bersihkan format angka dari input gaji
            $gaji = str_replace(['.', ','], '', $request->gaji);

            // Tentukan kode berdasarkan kategori
            $kode = $this->generateKode($validated['kategori']);

            // Simpan data
            InputGajiModel::create([
                'Tanggal' => $validated['Tanggal'],
                'kode' => $kode,
                'kategori' => $validated['kategori'],
                'nama_karyawan' => $validated['nama_karyawan'],
                'keterangan' => null,
                'uang_masuk' => 0,
                'uang_keluar' => 0,
                'gaji' => $gaji,
            ]);

            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    private function generateKode($kategori)
    {
        // Tentukan kode dasar berdasarkan kategori
        $kodeDasar = 0;
        switch ($kategori) {
            case 'utang_gaji':
                $kodeDasar = 2;
                break;
            case 'beban_gaji':
                $kodeDasar = 5;
                break;
            default:
                $kodeDasar = 0; // Kode default jika kategori tidak dikenali
        }

        // Ambil jumlah transaksi dengan kode dasar yang sama
        $lastTransaction = InputGajiModel::where('kode', 'like', $kodeDasar . '%')->orderBy('id', 'desc')->first();
        $nextNumber = $lastTransaction ? intval(substr($lastTransaction->kode, 1)) + 1 : 1;

        // Gabungkan kode dasar dengan nomor urut
        return $kodeDasar . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }
}