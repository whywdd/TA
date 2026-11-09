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
            'kode_barang' => 'required|unique:manajemen_stok,kode_barang',
            'nama_barang' => 'required',
            'satuan' => 'required',
            'stok' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'opsi_pembayaran' => 'required',
        ]);

        // Akun COA otomatis
        $akunCoa = 'persediaan barang dagang';

        // Simpan ke manajemen_stok
        $manajemenStok = ManajemenStok::create([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'stok' => $request->stok,
            'harga_beli' => $request->harga_beli,
            'akun_coa' => $akunCoa,
            'deskripsi' => $request->deskripsi,
        ]);

        // Generate kode akun untuk jurnal
        $kodePersediaan = $this->generateKodeAkun($akunCoa);
        $totalNilai = $request->stok * $request->harga_beli;

        // Generate jurnal otomatis berdasarkan opsi pembayaran
        if ($request->opsi_pembayaran == 'tunai') {
            // Bayar Tunai: Debit Persediaan, Kredit Kas
            $laporanData = [
                'Tanggal' => date('Y-m-d'),
                'keterangan' => 'Pembelian - ' . $request->nama_barang . ' (' . $request->satuan . ') - Bayar Tunai',
                'kode' => $kodePersediaan,
                'kategori' => $request->akun_coa,
                'uang_masuk' => $totalNilai,
                'uang_keluar' => 0,
                'kode2' => '1101', // Kas
                'kategori2' => 'kas',
                'uang_masuk2' => 0,
                'uang_keluar2' => $totalNilai,
            ];
        } else {
            // Belum Bayar: Debit Persediaan, Kredit Utang Usaha
            $laporanData = [
                'Tanggal' => date('Y-m-d'),
                'keterangan' => 'Pembelian - ' . $request->nama_barang . ' (' . $request->satuan . ') - Belum Bayar',
                'kode' => $kodePersediaan,
                'kategori' => $request->akun_coa,
                'uang_masuk' => $totalNilai,
                'uang_keluar' => 0,
                'kode2' => '2101', // Utang Usaha
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
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'satuan' => 'required',
            'harga_beli' => 'required|numeric|min:0',
        ]);

        $stok = ManajemenStok::findOrFail($id);
        $stok->update([
            'kode_barang' => $request->kode_barang,
            'nama_barang' => $request->nama_barang,
            'satuan' => $request->satuan,
            'harga_beli' => $request->harga_beli,
            'akun_coa' => 'persediaan barang dagang', // Otomatis
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
