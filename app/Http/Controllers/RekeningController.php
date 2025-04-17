<?php

namespace App\Http\Controllers;

use App\Models\RekeningModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use PDF;
use Excel;

class RekeningController extends Controller
{
    public function index(Request $request)
    {
        $query = RekeningModel::query();

        // Filter berdasarkan nama akun jika ada
        if ($request->has('periode')) {
            $query->where('kategori', $request->periode);
        }

        // Filter berdasarkan tanggal jika ada
        if ($request->has('startDate')) {
            $query->whereDate('Tanggal', '>=', $request->startDate);
        }

        $laporan = $query->orderBy('Tanggal', 'asc')->get();
        
        // Kelompokkan data berdasarkan kategori
        $groupedLaporan = collect();
        
        // Kelompokkan berdasarkan kategori 1-5
        for ($i = 1; $i <= 5; $i++) {
            $kategoriField = $i === 1 ? 'kategori' : "kategori{$i}";
            $uangMasukField = $i === 1 ? 'uang_masuk' : "uang_masuk{$i}";
            $uangKeluarField = $i === 1 ? 'uang_keluar' : "uang_keluar{$i}";
            
            $filteredLaporan = $laporan->filter(function ($item) use ($kategoriField) {
                return !empty($item->$kategoriField);
            });
            
            foreach ($filteredLaporan->groupBy($kategoriField) as $kategori => $items) {
                if (!empty($kategori)) {
                    $groupedLaporan[$kategori] = $items->map(function ($item) use ($kategoriField, $uangMasukField, $uangKeluarField) {
                        // Gunakan kode yang sesuai dengan nama kategori
                        $kode = $this->generateKode($item->$kategoriField);
                        
                        return (object)[
                            'id' => $item->id,
                            'Tanggal' => $item->Tanggal,
                            'keterangan' => $item->keterangan,
                            'kode' => $kode,
                            'debit' => $item->$uangMasukField ?? 0,
                            'kredit' => $item->$uangKeluarField ?? 0
                        ];
                    });
                }
            }
        }
        
        // Hitung total untuk setiap kategori
        $totals = [];
        foreach ($groupedLaporan as $kategori => $items) {
            $totals[$kategori] = [
                'debit' => $items->sum('debit'),
                'kredit' => $items->sum('kredit')
            ];
            $totals[$kategori]['saldo'] = $totals[$kategori]['debit'] - $totals[$kategori]['kredit'];
        }

        // Hitung total keseluruhan
        $totalDebit = array_sum(array_column($totals, 'debit'));
        $totalKredit = array_sum(array_column($totals, 'kredit'));
        $saldo = $totalDebit - $totalKredit;

        if ($request->ajax()) {
            return response()->json([
                'data' => $groupedLaporan,
                'totals' => $totals,
                'totalDebit' => $totalDebit,
                'totalKredit' => $totalKredit,
                'saldo' => $saldo
            ]);
        }

        return view('Rekening', compact('groupedLaporan', 'totals', 'totalDebit', 'totalKredit', 'saldo'));
    }

    private function generateKode($kategori)
    {
        // Ambil data-kode dari kategori yang dipilih
        $kodeAkun = '';
        
        switch (strtolower($kategori)) {
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

    public function destroy($id)
    {
        try {
            $laporan = RekeningModel::findOrFail($id);
            $laporan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function exportExcel()
    {
        $laporan = RekeningModel::all();
        $groupedLaporan = $laporan->groupBy('kategori');
        
        return Excel::download(function($excel) use ($groupedLaporan) {
            $excel->sheet('Rekening', function($sheet) use ($groupedLaporan) {
                $sheet->loadView('exports.rekening-excel', compact('groupedLaporan'));
            });
        }, 'rekening-' . Carbon::now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPDF()
    {
        $laporan = RekeningModel::all();
        $groupedLaporan = $laporan->groupBy('kategori');
        $pdf = PDF::loadView('exports.rekening-pdf', compact('groupedLaporan'));
        
        return $pdf->download('rekening-' . Carbon::now()->format('Y-m-d') . '.pdf');
    }
}
