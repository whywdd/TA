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
        try {
            // Validasi input
            $validated = $request->validate([
                'Tanggal' => 'required|date',
                'kategori' => 'required|string',
                'keterangan' => 'required|string',
                'uang_masuk' => 'required|string',
            ]);

            // Bersihkan format angka dari input uang_masuk
            $uang_masuk = str_replace(['.', ','], '', $request->uang_masuk);

            // Tentukan kode berdasarkan kategori
            $kode = $this->generateKode($validated['kategori']);

            // Simpan data
            UangMasukModel::create([
                'Tanggal' => $validated['Tanggal'],
                'kode' => $kode,
                'kategori' => $validated['kategori'],
                'keterangan' => $validated['keterangan'],
                'uang_masuk' => $uang_masuk,
                'uang_keluar' => 0,
                'gaji' => 0,
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
            case 'modal_pemilik':
                $kodeDasar = 3;
                break;
            case 'pendapatan_penjualan':
                $kodeDasar = 4;
                break;
            case 'pendapatan_jasa':
                $kodeDasar = 4; // Jika ada kode yang sama, bisa disesuaikan
                break;
            default:
                $kodeDasar = 0; // Kode default jika kategori tidak dikenali
        }

        // Ambil jumlah transaksi dengan kode dasar yang sama
        $lastTransaction = UangMasukModel::where('kode', 'like', $kodeDasar . '%')->orderBy('id', 'desc')->first();
        $nextNumber = $lastTransaction ? intval(substr($lastTransaction->kode, 1)) + 1 : 1;

        // Gabungkan kode dasar dengan nomor urut
        return $kodeDasar . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
    }
}