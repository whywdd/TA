<?php

namespace App\Http\Controllers;

use App\Models\RekeningModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use PDF;
use Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class RekeningController extends Controller
{
    public function index(Request $request)
    {
        // Default date range
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Pastikan tanggal valid
        try {
            $startDate = Carbon::parse($startDate)->format('Y-m-d');
            $endDate = Carbon::parse($endDate)->format('Y-m-d');
        } catch (\Exception $e) {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        }

        $query = RekeningModel::whereBetween('Tanggal', [$startDate, $endDate]);

        // Filter berdasarkan nama akun jika ada
        if ($request->has('periode')) {
            $query->where('kategori', $request->periode);
        }

        $laporan = $query->orderBy('Tanggal', 'asc')->get();
        
        // Kelompokkan data berdasarkan kategori
        $groupedLaporan = collect();
        
        // Proses semua data transaksi
        foreach ($laporan as $item) {
            // Proses untuk kategori utama
            if (!empty($item->kategori)) {
                $kategori = $item->kategori;
                if (!isset($groupedLaporan[$kategori])) {
                    $groupedLaporan[$kategori] = collect();
                }
                
                $groupedLaporan[$kategori]->push((object)[
                    'id' => $item->id,
                    'Tanggal' => $item->Tanggal,
                    'keterangan' => $item->keterangan,
                    'kode' => $this->generateKode($kategori),
                    'debit' => $item->uang_masuk ?? 0,
                    'kredit' => $item->uang_keluar ?? 0
                ]);
            }
            
            // Proses untuk kategori tambahan (2-5)
            for ($i = 2; $i <= 5; $i++) {
                $kategoriField = "kategori{$i}";
                $uangMasukField = "uang_masuk{$i}";
                $uangKeluarField = "uang_keluar{$i}";
                
                if (!empty($item->$kategoriField)) {
                    $kategori = $item->$kategoriField;
                    if (!isset($groupedLaporan[$kategori])) {
                        $groupedLaporan[$kategori] = collect();
                    }
                    
                    $groupedLaporan[$kategori]->push((object)[
                        'id' => $item->id,
                        'Tanggal' => $item->Tanggal,
                        'keterangan' => $item->keterangan,
                        'kode' => $this->generateKode($kategori),
                        'debit' => $item->$uangMasukField ?? 0,
                        'kredit' => $item->$uangKeluarField ?? 0
                    ]);
                }
            }
        }
        
        // Urutkan transaksi berdasarkan tanggal untuk setiap kategori
        foreach ($groupedLaporan as $kategori => $items) {
            $groupedLaporan[$kategori] = $items->sortBy('Tanggal');
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
                'saldo' => $saldo,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);
        }

        return view('Rekening', compact('groupedLaporan', 'totals', 'totalDebit', 'totalKredit', 'saldo', 'startDate', 'endDate'));
    }

    public function filter(Request $request)
    {
        return $this->index($request);
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

    public function exportExcel(Request $request)
    {
        try {
            // Ambil parameter filter tanggal jika ada
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

            $laporan = RekeningModel::whereBetween('Tanggal', [$startDate, $endDate])->get();
            $groupedLaporan = collect();

            // Proses data seperti di fungsi index
            foreach ($laporan as $item) {
                if (!empty($item->kategori)) {
                    $kategori = $item->kategori;
                    if (!isset($groupedLaporan[$kategori])) {
                        $groupedLaporan[$kategori] = collect();
                    }
                    
                    $groupedLaporan[$kategori]->push((object)[
                        'Tanggal' => $item->Tanggal,
                        'keterangan' => $item->keterangan,
                        'kode' => $this->generateKode($kategori),
                        'debit' => $item->uang_masuk ?? 0,
                        'kredit' => $item->uang_keluar ?? 0
                    ]);
                }
                
                // Proses untuk kategori tambahan (2-5)
                for ($i = 2; $i <= 5; $i++) {
                    $kategoriField = "kategori{$i}";
                    $uangMasukField = "uang_masuk{$i}";
                    $uangKeluarField = "uang_keluar{$i}";
                    
                    if (!empty($item->$kategoriField)) {
                        $kategori = $item->$kategoriField;
                        if (!isset($groupedLaporan[$kategori])) {
                            $groupedLaporan[$kategori] = collect();
                        }
                        
                        $groupedLaporan[$kategori]->push((object)[
                            'Tanggal' => $item->Tanggal,
                            'keterangan' => $item->keterangan,
                            'kode' => $this->generateKode($kategori),
                            'debit' => $item->$uangMasukField ?? 0,
                            'kredit' => $item->$uangKeluarField ?? 0
                        ]);
                    }
                }
            }

            // Buat class export inline
            $export = new class($groupedLaporan) implements FromCollection, WithHeadings {
                protected $data;
                
                public function __construct($data) 
                {
                    $this->data = $data;
                }
                
                public function collection()
                {
                    $exportData = collect();
                    
                    foreach ($this->data as $kategori => $items) {
                        // Tambahkan header untuk setiap kategori
                        $exportData->push([
                            'Nama Akun: ' . $kategori,
                            'Kode Akun: ' . ($items->first()->kode ?? '-'),
                            '', '', '', ''
                        ]);
                        
                        // Tambahkan header kolom
                        $exportData->push([
                            'Tanggal',
                            'Keterangan',
                            'Ref',
                            'Debit',
                            'Kredit',
                            'Saldo'
                        ]);
                        
                        // Tambahkan data transaksi
                        $runningBalance = 0;
                        foreach ($items->sortBy('Tanggal') as $item) {
                            $accountType = substr($item->kode, 0, 3);
                            $runningBalance = $this->calculateBalance($runningBalance, $item->debit, $item->kredit, $accountType);
                            
                            $exportData->push([
                                Carbon::parse($item->Tanggal)->format('d/m/Y'),
                                $item->keterangan,
                                '-',
                                $item->debit > 0 ? number_format($item->debit, 0, ',', '.') : '-',
                                $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-',
                                number_format($runningBalance, 0, ',', '.')
                            ]);
                        }
                        
                        // Tambahkan baris kosong setelah setiap kategori
                        $exportData->push(['', '', '', '', '', '']);
                    }
                    
                    return $exportData;
                }

                public function headings(): array
                {
                    return []; // Header akan ditangani di dalam collection
                }
                
                private function calculateBalance($previousBalance, $debit, $kredit, $accountType) {
                    $balance = $previousBalance;
                    
                    if (in_array($accountType, ['111', '112']) || in_array($accountType, ['251', '252'])) {
                        $balance = $balance + $debit - $kredit;
                    } else {
                        $balance = $balance - $debit + $kredit;
                    }
                    
                    return $balance;
                }
            };

            return Excel::download($export, 'buku-besar-'.date('Y-m-d').'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor Excel: ' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            // Ambil parameter filter tanggal jika ada
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

            $laporan = RekeningModel::whereBetween('Tanggal', [$startDate, $endDate])->get();
            $groupedLaporan = collect();

            // Proses data seperti di fungsi index
            foreach ($laporan as $item) {
                if (!empty($item->kategori)) {
                    $kategori = $item->kategori;
                    if (!isset($groupedLaporan[$kategori])) {
                        $groupedLaporan[$kategori] = collect();
                    }
                    
                    $groupedLaporan[$kategori]->push((object)[
                        'Tanggal' => $item->Tanggal,
                        'keterangan' => $item->keterangan,
                        'kode' => $this->generateKode($kategori),
                        'debit' => $item->uang_masuk ?? 0,
                        'kredit' => $item->uang_keluar ?? 0
                    ]);
                }
                
                // Proses untuk kategori tambahan (2-5)
                for ($i = 2; $i <= 5; $i++) {
                    $kategoriField = "kategori{$i}";
                    $uangMasukField = "uang_masuk{$i}";
                    $uangKeluarField = "uang_keluar{$i}";
                    
                    if (!empty($item->$kategoriField)) {
                        $kategori = $item->$kategoriField;
                        if (!isset($groupedLaporan[$kategori])) {
                            $groupedLaporan[$kategori] = collect();
                        }
                        
                        $groupedLaporan[$kategori]->push((object)[
                            'Tanggal' => $item->Tanggal,
                            'keterangan' => $item->keterangan,
                            'kode' => $this->generateKode($kategori),
                            'debit' => $item->$uangMasukField ?? 0,
                            'kredit' => $item->$uangKeluarField ?? 0
                        ]);
                    }
                }
            }

            // Generate PDF
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Buku Besar</title>
                <style>
                    @page {
                        margin: 20px;
                    }
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                        margin: 0;
                        padding: 0;
                    }
                    .main-container {
                        margin: 0;
                        padding: 0;
                    }
                    .header-content {
                        page-break-inside: avoid;
                        page-break-after: avoid;
                    }
                    .header {
                        margin-bottom: 10px;
                    }
                    h2 {
                        text-align: center;
                        margin: 0 0 5px 0;
                        padding: 0;
                        font-size: 16px;
                    }
                    p.periode {
                        text-align: center;
                        margin: 0;
                        padding: 0;
                        font-size: 12px;
                    }
                    .account-section {
                        page-break-inside: avoid;
                        margin-top: 10px;
                    }
                    .account-info {
                        background-color: #f2f2f2;
                        padding: 8px;
                        margin: 0 0 10px 0;
                        border-radius: 4px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 0;
                    }
                    table, th, td {
                        border: 1px solid #000;
                    }
                    th, td {
                        padding: 6px;
                        text-align: left;
                        font-size: 10px;
                    }
                    th {
                        background-color: #f2f2f2;
                        font-weight: bold;
                    }
                    .text-right {
                        text-align: right;
                    }
                    .text-center {
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class="main-container">
                    <div class="header-content">
                        <div class="header">
                            <h2>Buku Besar</h2>
                            <p class="periode">Periode: ' . date('F Y', strtotime($startDate)) . '</p>
                        </div>';
                
                foreach($groupedLaporan as $kategori => $items) {
                    $kodeAkun = $items->first()->kode ?? '-';
                    $html .= '
                        <div class="account-section">
                            <div class="account-info">
                                <strong>Nama Akun:</strong> ' . $kategori . '
                                <span style="float: right;"><strong>Kode Akun:</strong> ' . $kodeAkun . '</span>
                            </div>
                            
                            <table>
                                <thead>
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Keterangan</th>
                                        <th class="text-center">Ref</th>
                                        <th class="text-right">Debit</th>
                                        <th class="text-right">Kredit</th>
                                        <th class="text-right">Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>';
                            
                            $runningBalance = 0;
                            $accountType = substr($kodeAkun, 0, 3);
                            
                            foreach($items->sortBy('Tanggal') as $item) {
                                if (in_array($accountType, ['111', '112']) || in_array($accountType, ['251', '252'])) {
                                    $runningBalance = $runningBalance + $item->debit - $item->kredit;
                                } else {
                                    $runningBalance = $runningBalance - $item->debit + $item->kredit;
                                }
                                
                                $html .= '
                                <tr>
                                    <td>' . date('d/m/Y', strtotime($item->Tanggal)) . '</td>
                                    <td>' . $item->keterangan . '</td>
                                    <td class="text-center">-</td>
                                    <td class="text-right">' . ($item->debit > 0 ? number_format($item->debit, 0, ',', '.') : '-') . '</td>
                                    <td class="text-right">' . ($item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-') . '</td>
                                    <td class="text-right">' . number_format($runningBalance, 0, ',', '.') . '</td>
                                </tr>';
                            }
                            
                            $html .= '
                            </tbody>
                        </table>
                    </div>';
                }
                
                $html .= '
                </div>
            </body>
            </html>';
            
            $pdf = PDF::loadHTML($html);
            return $pdf->download('buku-besar-'.date('Y-m-d').'.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
