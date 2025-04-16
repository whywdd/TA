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
                'kategori' => 'required|array',
                'kategori.*' => 'required|string',
                'keterangan' => 'required|string',
                'nominal' => 'required|array',
                'nominal.*' => 'required|string',
                'posisi' => 'required|array',
                'posisi.*' => 'required|in:debit,kredit',
            ]);

            // Inisialisasi data untuk disimpan
            $data = [
                'Tanggal' => $validated['Tanggal'],
                'keterangan' => $validated['keterangan'],
            ];

            // Proses setiap rekening
            foreach ($validated['kategori'] as $index => $kategori) {
                $kode = $this->generateKode($kategori);
                $nominal = str_replace(['.', ','], '', $validated['nominal'][$index]);
                
                // Set kode dan kategori sesuai urutan
                $positionIndex = $index === 0 ? '' : ($index + 1);
                $data["kode" . $positionIndex] = $kode;
                $data["kategori" . $positionIndex] = $kategori;

                // Set uang_masuk atau uang_keluar berdasarkan posisi
                if ($validated['posisi'][$index] === 'debit') {
                    $data["uang_masuk" . $positionIndex] = $nominal;
                    $data["uang_keluar" . $positionIndex] = null;
                } else {
                    $data["uang_masuk" . $positionIndex] = null;
                    $data["uang_keluar" . $positionIndex] = $nominal;
                }
            }

            // Simpan data
            UangMasukModel::create($data);

            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    private function generateKode($kategori)
    {
        // Ambil data-kode dari kategori yang dipilih
        $kodeAkun = '';
        
        switch ($kategori) {
            // Aset Lancar (11)
            case 'kas':
                $kodeAkun = '111001';
                break;
            case 'bank':
                $kodeAkun = '111002';
                break;
            case 'piutang usaha':
                $kodeAkun = '111003';
                break;
            case 'piutang wesel':
                $kodeAkun = '111004';
                break;
            case 'piutang karyawan':
                $kodeAkun = '111005';
                break;
            case 'piutang lain':
                $kodeAkun = '111006';
                break;
            case 'persediaan barang':
                $kodeAkun = '111007';
                break;
            case 'persediaan bahan':
                $kodeAkun = '111008';
                break;
            case 'sewa dibayar dimuka':
                $kodeAkun = '111009';
                break;
            case 'asuransi dibayar_dimuka':
                $kodeAkun = '111010';
                break;
            case 'perlengkapan kantor':
                $kodeAkun = '111011';
                break;
            case 'biaya dibayar dimuka':
                $kodeAkun = '111012';
                break;
            case 'investasi pendek':
                $kodeAkun = '111013';
                break;
    
            // Aset Tetap (12)
            case 'tanah':
                $kodeAkun = '112001';
                break;
            case 'gedung':
                $kodeAkun = '112002';
                break;
            case 'kendaraan':
                $kodeAkun = '112003';
                break;
            case 'mesin':
                $kodeAkun = '112004';
                break;
            case 'perabotan':
                $kodeAkun = '112005';
                break;
            case 'hak paten':
                $kodeAkun = '112006';
                break;
            case 'hak cipta':
                $kodeAkun = '112007';
                break;
            case 'goodwill':
                $kodeAkun = '112008';
                break;
            case 'merek dagang':
                $kodeAkun = '112009';
                break;
    
            // Utang Lancar (21)
            case 'utang usaha':
                $kodeAkun = '121001';
                break;
            case 'utang wesel':
                $kodeAkun = '121002';
                break;
            case 'utang gaji':
                $kodeAkun = '121003';
                break;
            case 'utang bunga':
                $kodeAkun = '121004';
                break;
            case 'utang pajak':
                $kodeAkun = '121005';
                break;
            case 'utang dividen':
                $kodeAkun = '121006';
                break;
    
            // Utang Jangka Panjang (22)
            case 'utang hipotek':
                $kodeAkun = '122001';
                break;
            case 'utang obligasi':
                $kodeAkun = '122002';
                break;
            case 'kredit investasi':
                $kodeAkun = '122003';
                break;
    
            // Modal (Ekuitas) (31)
            case 'modal pemilik':
                $kodeAkun = '131001';
                break;
            case 'modal saham':
                $kodeAkun = '131002';
                break;
            case 'laba ditahan':
                $kodeAkun = '131003';
                break;
            case 'dividen':
                $kodeAkun = '131004';
                break;
            case 'prive':
                $kodeAkun = '131005';
                break;
    
            // Pendapatan Operasional (41)
            case 'pendapatan penjualan':
                $kodeAkun = '241001';
                break;
            case 'pendapatan jasa':
                $kodeAkun = '241002';
                break;
    
            // Pendapatan Non-Operasional (42)
            case 'pendapatan bunga':
                $kodeAkun = '242001';
                break;
            case 'pendapatan sewa':
                $kodeAkun = '242002';
                break;
            case 'pendapatan komisi':
                $kodeAkun = '242003';
                break;
            case 'pendapatan lain':
                $kodeAkun = '242004';
                break;
    
            // Beban Operasional (51)
            case 'beban gaji':
                $kodeAkun = '251001';
                break;
            case 'beban sewa':
                $kodeAkun = '251002';
                break;
            case 'beban utilitas':
                $kodeAkun = '251003';
                break;
            case 'beban penyusutan':
                $kodeAkun = '251004';
                break;
            case 'beban supplies':
                $kodeAkun = '251005';
                break;
            case 'beban iklan':
                $kodeAkun = '251006';
                break;
    
            // Beban Non-Operasional (52)
            case 'beban bunga':
                $kodeAkun = '252001';
                break;
            case 'beban lain':
                $kodeAkun = '252002';
                break;
    
            default:
                $kodeAkun = '0000';
        }
    
        return $kodeAkun;
    }
}