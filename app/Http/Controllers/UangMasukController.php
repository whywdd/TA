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

            // Simpan data dengan nilai null untuk uang_keluar dan gaji
            UangMasukModel::create([
                'Tanggal' => $validated['Tanggal'],
                'kode' => $kode,
                'kategori' => $validated['kategori'],
                'keterangan' => $validated['keterangan'],
                'uang_masuk' => $uang_masuk,
                'uang_keluar' => null, // Mengizinkan null
                'gaji' => null, // Mengizinkan null
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
        
        // Ekstrak prefix kode dari value kategori
        $kategoriPrefix = explode('_', $kategori)[0];
        
        switch ($kategoriPrefix) {
            // Harta (Aset)
            case 'kas':
            case 'bank':
            case 'piutang':
            case 'persediaan':
            case 'sewa':
            case 'asuransi':
            case 'perlengkapan':
            case 'biaya':
            case 'investasi':
            case 'tanah':
            case 'gedung':
            case 'kendaraan':
            case 'mesin':
            case 'perabotan':
            case 'hak':
            case 'goodwill':
            case 'merek':
                $kodeDasar = 1;
                break;
            
            // Utang (Kewajiban)
            case 'utang':
            case 'kredit':
                $kodeDasar = 2;
                break;
            
            // Modal (Ekuitas)
            case 'modal':
            case 'laba':
            case 'dividen':
            case 'prive':
                $kodeDasar = 3;
                break;
            
            // Pendapatan
            case 'pendapatan':
                $kodeDasar = 4;
                break;
            
            // Beban
            case 'beban':
                $kodeDasar = 5;
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