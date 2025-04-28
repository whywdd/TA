<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Default date range
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $laporan = LaporanModel::whereBetween('Tanggal', [$startDate, $endDate])
            ->orderBy('Tanggal', 'desc')
            ->get();
        
        // Hitung total uang masuk (termasuk uang_masuk2-5)
        $totalUangMasuk = LaporanModel::whereBetween('Tanggal', [$startDate, $endDate])
            ->selectRaw('SUM(uang_masuk) + 
                COALESCE(SUM(uang_masuk2), 0) + 
                COALESCE(SUM(uang_masuk3), 0) + 
                COALESCE(SUM(uang_masuk4), 0) + 
                COALESCE(SUM(uang_masuk5), 0) as total')
            ->value('total') ?? 0;
    
        // Hitung total uang keluar (termasuk uang_keluar2-5)
        $totalUangKeluar = LaporanModel::whereBetween('Tanggal', [$startDate, $endDate])
            ->selectRaw('SUM(uang_keluar) + 
                COALESCE(SUM(uang_keluar2), 0) + 
                COALESCE(SUM(uang_keluar3), 0) + 
                COALESCE(SUM(uang_keluar4), 0) + 
                COALESCE(SUM(uang_keluar5), 0) as total')
            ->value('total') ?? 0;
    
        $totalKredit = $totalUangKeluar;
        $saldo = $totalUangMasuk - $totalKredit;
        
        return view('Laporan', compact('laporan', 'totalUangMasuk', 'totalUangKeluar', 'totalKredit', 'saldo', 'startDate', 'endDate'));
    }

    public function filter(Request $request)
    {
        return $this->index($request);
    }

    public function exportExcel()
    {
        try {
            // Buat class export inline
            $export = new class implements FromCollection, WithHeadings, WithMapping {
                public function collection()
                {
                    return LaporanModel::orderBy('Tanggal', 'desc')->get();
                }

                public function headings(): array
                {
                    return [
                        'ID',
                        'Tanggal',
                        'Kode',
                        'Kategori',
                        'Keterangan',
                        'Uang Masuk',
                        'Uang Keluar'
                    ];
                }

                public function map($laporan): array
                {
                    // Hitung total uang masuk untuk baris ini
                    $totalUangMasuk = $laporan->uang_masuk +
                        ($laporan->uang_masuk2 ?? 0) +
                        ($laporan->uang_masuk3 ?? 0) +
                        ($laporan->uang_masuk4 ?? 0) +
                        ($laporan->uang_masuk5 ?? 0);

                    // Hitung total uang keluar untuk baris ini
                    $totalUangKeluar = $laporan->uang_keluar +
                        ($laporan->uang_keluar2 ?? 0) +
                        ($laporan->uang_keluar3 ?? 0) +
                        ($laporan->uang_keluar4 ?? 0) +
                        ($laporan->uang_keluar5 ?? 0);

                    return [
                        $laporan->id,
                        $laporan->Tanggal,
                        $laporan->kode,
                        $laporan->kategori,
                        $laporan->keterangan,
                        $totalUangMasuk,
                        $totalUangKeluar
                    ];
                }
            };

            return Excel::download($export, 'laporan-keuangan-'.date('Y-m-d').'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor Excel: ' . $e->getMessage());
        }
    }
    
    public function exportPDF()
    {
        try {
            $laporan = LaporanModel::orderBy('Tanggal', 'desc')->get();
            
            // Hitung total uang masuk
            $totalUangMasuk = LaporanModel::selectRaw('SUM(uang_masuk) + 
                COALESCE(SUM(uang_masuk2), 0) + 
                COALESCE(SUM(uang_masuk3), 0) + 
                COALESCE(SUM(uang_masuk4), 0) + 
                COALESCE(SUM(uang_masuk5), 0) as total')
                ->value('total') ?? 0;

            // Hitung total uang keluar
            $totalKredit = LaporanModel::selectRaw('SUM(uang_keluar) + 
                COALESCE(SUM(uang_keluar2), 0) + 
                COALESCE(SUM(uang_keluar3), 0) + 
                COALESCE(SUM(uang_keluar4), 0) + 
                COALESCE(SUM(uang_keluar5), 0) as total')
                ->value('total') ?? 0;

            $saldo = $totalUangMasuk - $totalKredit;
            
            // Generate PDF
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Keuangan</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    table, th, td {
                        border: 1px solid #ddd;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    .summary {
                        margin-top: 20px;
                    }
                    .summary table {
                        width: 300px;
                    }
                </style>
            </head>
            <body>
                <h2>Laporan Keuangan</h2>
                <p>Tanggal: '.date('d-m-Y').'</p>
                
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Kode</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th>Uang Masuk</th>
                            <th>Uang Keluar</th>
                        </tr>
                    </thead>
                    <tbody>';
                    
                    foreach($laporan as $index => $item) {
                        // Hitung total uang masuk untuk baris ini
                        $rowUangMasuk = $item->uang_masuk +
                            ($item->uang_masuk2 ?? 0) +
                            ($item->uang_masuk3 ?? 0) +
                            ($item->uang_masuk4 ?? 0) +
                            ($item->uang_masuk5 ?? 0);

                        // Hitung total uang keluar untuk baris ini
                        $rowUangKeluar = $item->uang_keluar +
                            ($item->uang_keluar2 ?? 0) +
                            ($item->uang_keluar3 ?? 0) +
                            ($item->uang_keluar4 ?? 0) +
                            ($item->uang_keluar5 ?? 0);

                        $html .= '
                        <tr>
                            <td>'.($index + 1).'</td>
                            <td>'.$item->Tanggal.'</td>
                            <td>'.$item->kode.'</td>
                            <td>'.$item->kategori.'</td>
                            <td>'.$item->keterangan.'</td>
                            <td>Rp '.number_format($rowUangMasuk, 0, ',', '.').'</td>
                            <td>Rp '.number_format($rowUangKeluar, 0, ',', '.').'</td>
                        </tr>';
                    }
                    
                    $html .= '
                    </tbody>
                </table>
                
                <div class="summary">
                    <h3>Ringkasan</h3>
                    <table>
                        <tr>
                            <td><strong>Total Uang Masuk</strong></td>
                            <td>Rp '.number_format($totalUangMasuk, 0, ',', '.').'</td>
                        </tr>
                        <tr>
                            <td><strong>Total Uang Keluar</strong></td>
                            <td>Rp '.number_format($totalKredit, 0, ',', '.').'</td>
                        </tr>
                        <tr>
                            <td><strong>Saldo</strong></td>
                            <td>Rp '.number_format($saldo, 0, ',', '.').'</td>
                        </tr>
                    </table>
                </div>
            </body>
            </html>';
            
            $pdf = PDF::loadHTML($html);
            return $pdf->download('laporan-keuangan-'.date('Y-m-d').'.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $laporan = LaporanModel::findOrFail($id);
            $laporan->delete();
            return response()->json(['success' => true, 'message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }
}