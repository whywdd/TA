<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanController extends Controller
{
    public function index()
    {
        $laporan = LaporanModel::orderBy('Tanggal', 'desc')->get();
        $totalUangMasuk = LaporanModel::sum('uang_masuk');
        $totalUangKeluar = LaporanModel::sum('uang_keluar');
        $totalGaji = LaporanModel::sum('gaji');
        $totalKredit = $totalUangKeluar + $totalGaji;
        $saldo = $totalUangMasuk - $totalKredit;
        
        return view('Laporan', compact('laporan', 'totalUangMasuk', 'totalUangKeluar', 'totalGaji', 'totalKredit', 'saldo'));
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
                        'Keterangan',
                        'Nama Karyawan',
                        'Uang Masuk',
                        'Uang Keluar',
                        'Gaji',
                    ];
                }

                public function map($laporan): array
                {
                    return [
                        $laporan->id,
                        $laporan->Tanggal,
                        $laporan->keterangan,
                        $laporan->nama_karyawan,
                        $laporan->uang_masuk,
                        $laporan->uang_keluar,
                        $laporan->gaji,
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
            $totalUangMasuk = LaporanModel::sum('uang_masuk');
            $totalUangKeluar = LaporanModel::sum('uang_keluar');
            $totalGaji = LaporanModel::sum('gaji');
            $totalKredit = $totalUangKeluar + $totalGaji;
            $saldo = $totalUangMasuk - $totalKredit;
            
            // Generate PDF langsung menggunakan HTML inline
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
                            <th>Keterangan</th>
                            <th>Nama Karyawan</th>
                            <th>Uang Masuk</th>
                            <th>Uang Keluar</th>
                            <th>Gaji</th>
                        </tr>
                    </thead>
                    <tbody>';
                    
                    foreach($laporan as $index => $item) {
                        $html .= '
                        <tr>
                            <td>'.($index + 1).'</td>
                            <td>'.$item->Tanggal.'</td>
                            <td>'.$item->keterangan.'</td>
                            <td>'.$item->nama_karyawan.'</td>
                            <td>Rp '.number_format($item->uang_masuk, 0, ',', '.').'</td>
                            <td>Rp '.number_format($item->uang_keluar, 0, ',', '.').'</td>
                            <td>Rp '.number_format($item->gaji, 0, ',', '.').'</td>
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
                            <td>Rp '.number_format($totalUangKeluar, 0, ',', '.').'</td>
                        </tr>
                        <tr>
                            <td><strong>Total Gaji</strong></td>
                            <td>Rp '.number_format($totalGaji, 0, ',', '.').'</td>
                        </tr>
                        <tr>
                            <td><strong>Total Kredit</strong></td>
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