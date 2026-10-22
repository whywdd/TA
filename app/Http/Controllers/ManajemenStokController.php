<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ManajemenStokController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Dummy data untuk preview UI
        $stoks = collect([
            (object)[
                'id' => 1,
                'kode_barang' => 'ANG-BLL-001',
                'nama_barang' => 'Anggrek Bulan Putih',
                'kategori' => 'Indukan',
                'satuan' => 'Pohon',
                'stok' => 25,
                'reorder_point' => 10,
                'harga_beli' => 100000,
                'harga_jual' => 150000,
                'lokasi' => 'GH-01 / A-01'
            ],
            (object)[
                'id' => 2,
                'kode_barang' => 'ANG-BLL-002',
                'nama_barang' => 'Anggrek Bulan Merah',
                'kategori' => 'Indukan',
                'satuan' => 'Pohon',
                'stok' => 5,
                'reorder_point' => 10,
                'harga_beli' => 120000,
                'harga_jual' => 175000,
                'lokasi' => 'GH-01 / A-02'
            ],
            (object)[
                'id' => 3,
                'kode_barang' => 'SED-GLD-001',
                'nama_barang' => 'Seedling Gold',
                'kategori' => 'Seedling',
                'satuan' => 'Pot',
                'stok' => 50,
                'reorder_point' => 20,
                'harga_beli' => 25000,
                'harga_jual' => 35000,
                'lokasi' => 'GH-02 / B-01'
            ],
            (object)[
                'id' => 4,
                'kode_barang' => 'PUP-ORG-001',
                'nama_barang' => 'Pupuk Organik Cair',
                'kategori' => 'Sarana Produksi/Pupuk',
                'satuan' => 'Botol',
                'stok' => 100,
                'reorder_point' => 30,
                'harga_beli' => 15000,
                'harga_jual' => 25000,
                'lokasi' => 'Gudang / C-01'
            ],
        ]);

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
        // Validasi (nanti akan dihubungkan dengan database)
        $validated = $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
            'stok_awal' => 'required|numeric|min:0',
            'reorder_point' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'akun_coa_persediaan' => 'required',
            'akun_coa_penjualan' => 'required',
            'akun_coa_hpp' => 'required',
        ]);

        // Dummy redirect untuk preview
        return redirect()->route('manajemen-stok.index')->with('success', 'Data barang berhasil ditambahkan (UI Preview)');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        // Dummy data untuk preview UI
        $stok = (object)[
            'id' => $id,
            'kode_barang' => 'ANG-BLL-001',
            'nama_barang' => 'Anggrek Bulan Putih',
            'kategori' => 'Indukan',
            'satuan' => 'Pohon',
            'stok' => 25,
            'reorder_point' => 10,
            'harga_beli' => 100000,
            'harga_jual' => 150000,
            'akun_coa_persediaan' => 'persediaan barang',
            'akun_coa_penjualan' => 'pendapatan penjualan',
            'akun_coa_hpp' => 'beban lain',
            'lokasi_greenhouse' => 'GH-01',
            'rak' => 'A-01',
            'foto_produk' => null,
            'deskripsi' => 'Anggrek bulan putih kualitas premium'
        ];

        return view('manajemen-stok.edit', compact('stok'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validasi (nanti akan dihubungkan dengan database)
        $validated = $request->validate([
            'kode_barang' => 'required',
            'nama_barang' => 'required',
            'kategori' => 'required',
            'satuan' => 'required',
            'reorder_point' => 'required|numeric|min:0',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'akun_coa_persediaan' => 'required',
            'akun_coa_penjualan' => 'required',
            'akun_coa_hpp' => 'required',
        ]);

        // Dummy redirect untuk preview
        return redirect()->route('manajemen-stok.index')->with('success', 'Data barang berhasil diperbarui (UI Preview)');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // Dummy redirect untuk preview
        return redirect()->route('manajemen-stok.index')->with('success', 'Data barang berhasil dihapus (UI Preview)');
    }
}
