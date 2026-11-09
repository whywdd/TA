<?php

namespace App\Http\Controllers;

use App\Models\LaporanModel;
use App\Models\ManajemenStok;
use Illuminate\Http\Request;

class ManajemenStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $stoks = ManajemenStok::all();
        return view('manajemen-stok.index', compact('stoks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('manajemen-stok.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi
        $validated = $request->validate([
            'nama_barang' => 'required',
            'satuan' => 'required',
            'stok' => 'required|numeric|min:0',
            'harga_beli' => 'required',
            'status_tahap' => 'required',
            'opsi_pembayaran' => 'required',
        ]);

        // Auto generate SKU
        $lastSku = ManajemenStok::orderBy('id', 'desc')->first();
        $lastNumber = $lastSku ? (int)substr($lastSku->kode_barang, -3) : 0;
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        $kodeBarang = 'SKU-' . $newNumber;

        // Akun COA otomatis
        $akunCoa = 'persediaan barang dagang';

        // Convert format Indonesia ke angka murni (hapus titik ribuan)
        $hargaBeli = str_replace('.', '', $request->harga_beli);

        // Simpan ke manajemen_stok
        $manajemenStok = ManajemenStok::create([
            'kode_barang' => $kodeBarang,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'stok' => $request->stok,
            'harga_beli' => $hargaBeli,
            'akun_coa' => $akunCoa,
            'status_tahap' => $request->status_tahap,
            'deskripsi' => $request->deskripsi,
        ]);

        // Generate kode akun untuk jurnal
        $kodePersediaan = $this->generateKodeAkun($akunCoa);
        $totalNilai = $request->stok * $hargaBeli;

        // Generate jurnal otomatis berdasarkan opsi pembayaran
        if ($request->opsi_pembayaran == 'tunai') {
            // Bayar Tunai: Debit Persediaan, Kredit Kas
            $laporanData = [
                'Tanggal' => date('Y-m-d'),
                'keterangan' => 'Pembelian - ' . $request->nama_barang . ' (' . $request->satuan . ') - Bayar Tunai',
                'kode' => '111007', // Persediaan Barang Dagang
                'kategori' => $akunCoa,
                'uang_masuk' => $totalNilai,
                'uang_keluar' => 0,
                'kode2' => '111001', // Kas
                'kategori2' => 'kas',
                'uang_masuk2' => 0,
                'uang_keluar2' => $totalNilai,
            ];
        } else {
            // Belum Bayar: Debit Persediaan, Kredit Utang Usaha
            $laporanData = [
                'Tanggal' => date('Y-m-d'),
                'keterangan' => 'Pembelian - ' . $request->nama_barang . ' (' . $request->satuan . ') - Belum Bayar',
                'kode' => '111007', // Persediaan Barang Dagang
                'kategori' => $akunCoa,
                'uang_masuk' => $totalNilai,
                'uang_keluar' => 0,
                'kode2' => '121001', // Utang Usaha
                'kategori2' => 'utang usaha',
                'uang_masuk2' => 0,
                'uang_keluar2' => $totalNilai,
            ];
        }

        // Simpan jurnal ke laporan_transaksis
        LaporanModel::create($laporanData);

        return redirect()->route('manajemen-stok.index')->with('success', 'Data barang berhasil ditambahkan dan jurnal pembelian otomatis dicatat');
    }

    private function generateKodeAkun($kategori)
    {
        switch ($kategori) {
            case 'persediaan barang dagang':
                return '111007';
            case 'persediaan bahan':
                return '111008';
            default:
                return '111007';
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $stok = ManajemenStok::findOrFail($id);
        return view('manajemen-stok.edit', compact('stok'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi (stok tidak divalidasi karena read-only di edit)
        $validated = $request->validate([
            'nama_barang' => 'required',
            'satuan' => 'required',
            'harga_beli' => 'required',
            'status_tahap' => 'required',
        ]);

        // Convert format Indonesia ke angka murni (hapus titik ribuan)
        $hargaBeli = str_replace('.', '', $request->harga_beli);

        $stok = ManajemenStok::findOrFail($id);
        $stok->update([
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga_beli' => $hargaBeli,
            'akun_coa' => 'persediaan barang dagang', // Otomatis
            'status_tahap' => $request->status_tahap,
            'deskripsi' => $request->deskripsi,
        ]);

        return redirect()->route('manajemen-stok.index')->with('success', 'Data barang berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stok = ManajemenStok::findOrFail($id);
        $stok->delete();

        return redirect()->route('manajemen-stok.index')->with('success', 'Data barang berhasil dihapus');
    }
}
