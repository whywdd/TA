<?php

namespace App\Http\Controllers;

use App\Models\NeracasaldoModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NeracasaldoController extends Controller
{
    public function index(Request $request)
    {
        // Default date range
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $rawTransaksis = NeracasaldoModel::whereBetween('Tanggal', [$startDate, $endDate])
            ->orderBy('kode', 'asc')
            ->get();
        $totalsPerAkun = [];

        foreach ($rawTransaksis as $transaksi) {
            // Fungsi untuk menambahkan atau memperbarui total per akun
            $processAkun = function($kode, $kategori, $debit, $kredit) use (&$totalsPerAkun) {
                if (!empty($kode) && !empty($kategori)) {
                    if (!isset($totalsPerAkun[$kategori])) {
                        $totalsPerAkun[$kategori] = [
                            'kode' => $kode,
                            'kategori' => $kategori,
                            'debit' => 0,
                            'kredit' => 0
                        ];
                    }
                    $totalsPerAkun[$kategori]['debit'] += floatval($debit ?? 0);
                    $totalsPerAkun[$kategori]['kredit'] += floatval($kredit ?? 0);
                }
            };

            // Proses untuk semua kategori
            $processAkun($transaksi->kode, $transaksi->kategori, $transaksi->uang_masuk, $transaksi->uang_keluar);
            $processAkun($transaksi->kode2, $transaksi->kategori2, $transaksi->uang_masuk2, $transaksi->uang_keluar2);
            $processAkun($transaksi->kode3, $transaksi->kategori3, $transaksi->uang_masuk3, $transaksi->uang_keluar3);
            $processAkun($transaksi->kode4, $transaksi->kategori4, $transaksi->uang_masuk4, $transaksi->uang_keluar4);
            $processAkun($transaksi->kode5, $transaksi->kategori5, $transaksi->uang_masuk5, $transaksi->uang_keluar5);
        }

        // Hitung saldo akhir dan atur posisi debit/kredit sesuai jenis akun
        $finalTransaksis = [];
        foreach ($totalsPerAkun as $kategori => $data) {
            $kodeAwal = substr($data['kode'], 0, 3);
            $saldo = $data['debit'] - $data['kredit'];
            
            // Tentukan posisi saldo (debit/kredit) berdasarkan jenis akun
            if (in_array($kodeAwal, ['111', '112']) || in_array($kodeAwal, ['251', '252'])) {
                // Aktiva dan Beban: saldo normal di debit
                if ($saldo != 0) {
                    $finalTransaksis[] = [
                        'kode' => $data['kode'],
                        'kategori' => $kategori,
                        'debit' => $saldo,
                        'kredit' => 0
                    ];
                }
            } else {
                // Pasiva dan Pendapatan: saldo normal di kredit
                if ($saldo != 0) {
                    $finalTransaksis[] = [
                        'kode' => $data['kode'],
                        'kategori' => $kategori,
                        'debit' => 0,
                        'kredit' => -$saldo
                    ];
                }
            }
        }

        // Urutkan berdasarkan kode
        usort($finalTransaksis, function($a, $b) {
            return $a['kode'] <=> $b['kode'];
        });
        
        // Konversi ke collection setelah selesai
        $transaksis = collect($finalTransaksis);
        
        return view('Neracasaldo', compact('transaksis', 'startDate', 'endDate'));
    }

    public function filter(Request $request)
    {
        return $this->index($request);
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

    public function show($id)
    {
        $transaksi = NeracasaldoModel::findOrFail($id);
        return view('neracasaldo.show', compact('transaksi'));
    }
}
