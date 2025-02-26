<?php

namespace App\Http\Controllers;

use App\Models\UangKeluarModel; // Import model
use Illuminate\Http\Request;

class UangKeluarController extends Controller
{
    public function index()
    {
        return view('UangKeluar');
    }

    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'Tanggal' => 'required|date',
                'kategori' => 'required|string',
                'keterangan' => 'required|string',
                'uang_keluar' => 'required|string',
            ]);

            // Bersihkan format angka dari input uang_keluar
            $uang_keluar = str_replace(['.', ','], '', $request->uang_keluar);

            // Tentukan kode berdasarkan kategori
            $kode = $this->generateKode($validated['kategori']);

            // Simpan data
            UangKeluarModel::create([
                'Tanggal' => $validated['Tanggal'],
                'kode' => $kode,
                'kategori' => $validated['kategori'],
                'keterangan' => $validated['keterangan'],
                'uang_masuk' => null,
                'uang_keluar' => $uang_keluar,
                'gaji' => null,
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
            case 'kas':
                $kodeDasar = 1;
                break;
            case 'utang usaha':
                $kodeDasar = 2;
                break;
            case 'utang bank':
                $kodeDasar = 2;
                break;    
            case 'modal usaha':
                $kodeDasar = 3;
                break;    
            case 'beban listrik':
                $kodeDasar = 5;
                break; 
            case 'beban sewa':
                $kodeDasar = 5;
                break; 
            default:
                $kodeDasar = 0; // Kode default jika kategori tidak dikenali
        }

        // Ambil jumlah transaksi dengan kode dasar yang sama
        $lastTransaction = UangKeluarModel::where('kode', 'like', $kodeDasar . '%')->orderBy('id', 'desc')->first();
        $nextNumber = $lastTransaction ? intval(substr($lastTransaction->kode, 1)) + 1 : 1;

        // Gabungkan kode dasar dengan nomor urut
        return $kodeDasar . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }
}