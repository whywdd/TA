<?php

namespace App\Http\Controllers;

use App\Models\NeracasaldoModel;
use Illuminate\Http\Request;

class NeracasaldoController extends Controller
{
    public function index()
    {
        $rawTransaksis = NeracasaldoModel::orderBy('kode', 'asc')->get();
        $transaksis = collect();

        foreach ($rawTransaksis as $transaksi) {
            // Menambahkan baris untuk kode utama
            if ($transaksi->kode) {
                $transaksis->push([
                    'kode' => $transaksi->kode,
                    'kategori' => $transaksi->kategori,
                    'keterangan' => $transaksi->keterangan,
                    'nama_karyawan' => $transaksi->nama_karyawan,
                    'debit' => $transaksi->uang_masuk,
                    'kredit' => $transaksi->uang_keluar,
                    'id' => $transaksi->id
                ]);
            }

            // Menambahkan baris untuk kode2
            if ($transaksi->kode2) {
                $transaksis->push([
                    'kode' => $transaksi->kode2,
                    'kategori' => $transaksi->kategori2,
                    'keterangan' => $transaksi->keterangan,
                    'nama_karyawan' => $transaksi->nama_karyawan,
                    'debit' => $transaksi->uang_masuk2,
                    'kredit' => $transaksi->uang_keluar2,
                    'id' => $transaksi->id
                ]);
            }

            // Menambahkan baris untuk kode3
            if ($transaksi->kode3) {
                $transaksis->push([
                    'kode' => $transaksi->kode3,
                    'kategori' => $transaksi->kategori3,
                    'keterangan' => $transaksi->keterangan,
                    'nama_karyawan' => $transaksi->nama_karyawan,
                    'debit' => $transaksi->uang_masuk3,
                    'kredit' => $transaksi->uang_keluar3,
                    'id' => $transaksi->id
                ]);
            }

            // Menambahkan baris untuk kode4
            if ($transaksi->kode4) {
                $transaksis->push([
                    'kode' => $transaksi->kode4,
                    'kategori' => $transaksi->kategori4,
                    'keterangan' => $transaksi->keterangan,
                    'nama_karyawan' => $transaksi->nama_karyawan,
                    'debit' => $transaksi->uang_masuk4,
                    'kredit' => $transaksi->uang_keluar4,
                    'id' => $transaksi->id
                ]);
            }

            // Menambahkan baris untuk kode5
            if ($transaksi->kode5) {
                $transaksis->push([
                    'kode' => $transaksi->kode5,
                    'kategori' => $transaksi->kategori5,
                    'keterangan' => $transaksi->keterangan,
                    'nama_karyawan' => $transaksi->nama_karyawan,
                    'debit' => $transaksi->uang_masuk5,
                    'kredit' => $transaksi->uang_keluar5,
                    'id' => $transaksi->id
                ]);
            }
        }

        // Mengurutkan berdasarkan kode
        $transaksis = $transaksis->sortBy('kode');
        
        return view('Neracasaldo', compact('transaksis'));
    }

    public function create()
    {
        return view('neracasaldo.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'Tanggal' => 'required|date',
            'kode' => 'required|string',
            'kategori' => 'required|string',
        ]);

        NeracasaldoModel::create($request->all());

        return redirect()->route('neracasaldo.index')
            ->with('success', 'Data berhasil ditambahkan');
    }

    public function edit($id)
    {
        $transaksi = NeracasaldoModel::findOrFail($id);
        return view('neracasaldo.edit', compact('transaksi'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Tanggal' => 'required|date',
            'kode' => 'required|string',
            'kategori' => 'required|string',
        ]);

        $transaksi = NeracasaldoModel::findOrFail($id);
        $transaksi->update($request->all());

        return redirect()->route('neracasaldo.index')
            ->with('success', 'Data berhasil diperbarui');
    }

    public function destroy($id)
    {
        $transaksi = NeracasaldoModel::findOrFail($id);
        $transaksi->delete();

        return redirect()->route('neracasaldo.index')
            ->with('success', 'Data berhasil dihapus');
    }
}
