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

    public function exportExcel(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
            $laporan = LaporanModel::whereBetween('Tanggal', [$startDate, $endDate])
                ->orderBy('Tanggal', 'desc')
                ->get();

            $rows = [];
            $no = 1;
            $totalDebit = 0;
            $totalKredit = 0;

            foreach ($laporan as $item) {
                // Cek apakah semua nilai adalah debit
                $allDebit = true;
                $debitValues = [
                    $item->uang_masuk,
                    $item->uang_masuk2,
                    $item->uang_masuk3,
                    $item->uang_masuk4,
                    $item->uang_masuk5
                ];
                
                $validDebitValues = array_filter($debitValues, function($value) {
                    return $value !== null && $value > 0;
                });

                if ($item->uang_keluar > 0 || 
                    ($item->uang_keluar2 ?? 0) > 0 || 
                    ($item->uang_keluar3 ?? 0) > 0 || 
                    ($item->uang_keluar4 ?? 0) > 0 || 
                    ($item->uang_keluar5 ?? 0) > 0) {
                    $allDebit = false;
                }

                // Proses data untuk setiap baris
                $rowDebit = 0;
                $rowKredit = 0;

                if ($allDebit) {
                    $lastDebitValue = end($validDebitValues);
                    $firstDebitValue = reset($validDebitValues);
                    
                    if (count($validDebitValues) > 1) {
                        $rowDebit = $firstDebitValue;
                        $rowKredit = $lastDebitValue;
                    } else {
                        $rowDebit = $firstDebitValue;
                        $rowKredit = $firstDebitValue;
                    }
                } else {
                    $rowDebit = $item->uang_masuk + 
                              ($item->uang_masuk2 ?? 0) + 
                              ($item->uang_masuk3 ?? 0) + 
                              ($item->uang_masuk4 ?? 0) + 
                              ($item->uang_masuk5 ?? 0);
                              
                    $rowKredit = $item->uang_keluar + 
                               ($item->uang_keluar2 ?? 0) + 
                               ($item->uang_keluar3 ?? 0) + 
                               ($item->uang_keluar4 ?? 0) + 
                               ($item->uang_keluar5 ?? 0);
                }

                $totalDebit += $rowDebit;
                $totalKredit += $rowKredit;

                // Gabungkan semua kode akun
                $kodeAkun = $item->kode;
                if (!empty($item->kode2)) $kodeAkun .= "\n" . $item->kode2;
                if (!empty($item->kode3)) $kodeAkun .= "\n" . $item->kode3;
                if (!empty($item->kode4)) $kodeAkun .= "\n" . $item->kode4;
                if (!empty($item->kode5)) $kodeAkun .= "\n" . $item->kode5;

                // Gabungkan semua nama akun
                $namaAkun = $item->kategori;
                if (!empty($item->kategori2)) $namaAkun .= "\n" . $item->kategori2;
                if (!empty($item->kategori3)) $namaAkun .= "\n" . $item->kategori3;
                if (!empty($item->kategori4)) $namaAkun .= "\n" . $item->kategori4;
                if (!empty($item->kategori5)) $namaAkun .= "\n" . $item->kategori5;

                $rows[] = [
                    'No' => $no++,
                    'Tanggal' => date('d/m/Y', strtotime($item->Tanggal)),
                    'Kode Akun' => $kodeAkun,
                    'Nama Akun' => $namaAkun,
                    'Keterangan' => $item->keterangan . ' ' . $item->nama_karyawan,
                    'Debit' => $rowDebit > 0 ? number_format($rowDebit, 0, ',', '.') : '-',
                    'Kredit' => $rowKredit > 0 ? number_format($rowKredit, 0, ',', '.') : '-'
                ];
            }

            // Tambahkan baris total
            $rows[] = [
                'No' => '',
                'Tanggal' => '',
                'Kode Akun' => '',
                'Nama Akun' => '',
                'Keterangan' => 'Total',
                'Debit' => number_format($totalDebit, 0, ',', '.'),
                'Kredit' => number_format($totalKredit, 0, ',', '.')
            ];

            $export = new class($rows) implements FromCollection, WithHeadings {
                protected $rows;
                public function __construct($rows) { $this->rows = $rows; }
                public function collection() { return collect($this->rows); }
                public function headings(): array {
                    return ['No', 'Tanggal', 'Kode Akun', 'Nama Akun', 'Keterangan', 'Debit', 'Kredit'];
                }
            };

            return Excel::download($export, 'laporan-keuangan-'.date('Y-m-d').'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor Excel: ' . $e->getMessage());
        }
    }
    
    public function exportPDF(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
            $laporan = LaporanModel::whereBetween('Tanggal', [$startDate, $endDate])
                ->orderBy('Tanggal', 'desc')
                ->get();

            $rows = '';
            $no = 1;
            $totalDebit = 0;
            $totalKredit = 0;

            foreach ($laporan as $item) {
                // Cek apakah semua nilai adalah debit
                $allDebit = true;
                $debitValues = [
                    $item->uang_masuk,
                    $item->uang_masuk2,
                    $item->uang_masuk3,
                    $item->uang_masuk4,
                    $item->uang_masuk5
                ];
                
                $validDebitValues = array_filter($debitValues, function($value) {
                    return $value !== null && $value > 0;
                });

                if ($item->uang_keluar > 0 || 
                    ($item->uang_keluar2 ?? 0) > 0 || 
                    ($item->uang_keluar3 ?? 0) > 0 || 
                    ($item->uang_keluar4 ?? 0) > 0 || 
                    ($item->uang_keluar5 ?? 0) > 0) {
                    $allDebit = false;
                }

                // Proses data untuk setiap baris
                $rowDebit = 0;
                $rowKredit = 0;

                if ($allDebit) {
                    $lastDebitValue = end($validDebitValues);
                    $firstDebitValue = reset($validDebitValues);
                    
                    if (count($validDebitValues) > 1) {
                        $rowDebit = $firstDebitValue;
                        $rowKredit = $lastDebitValue;
                    } else {
                        $rowDebit = $firstDebitValue;
                        $rowKredit = $firstDebitValue;
                    }
                } else {
                    $rowDebit = $item->uang_masuk + 
                              ($item->uang_masuk2 ?? 0) + 
                              ($item->uang_masuk3 ?? 0) + 
                              ($item->uang_masuk4 ?? 0) + 
                              ($item->uang_masuk5 ?? 0);
                              
                    $rowKredit = $item->uang_keluar + 
                               ($item->uang_keluar2 ?? 0) + 
                               ($item->uang_keluar3 ?? 0) + 
                               ($item->uang_keluar4 ?? 0) + 
                               ($item->uang_keluar5 ?? 0);
                }

                $totalDebit += $rowDebit;
                $totalKredit += $rowKredit;

                // Gabungkan semua kode akun
                $kodeAkun = $item->kode;
                if (!empty($item->kode2)) $kodeAkun .= "<br>" . $item->kode2;
                if (!empty($item->kode3)) $kodeAkun .= "<br>" . $item->kode3;
                if (!empty($item->kode4)) $kodeAkun .= "<br>" . $item->kode4;
                if (!empty($item->kode5)) $kodeAkun .= "<br>" . $item->kode5;

                // Gabungkan semua nama akun
                $namaAkun = $item->kategori;
                if (!empty($item->kategori2)) $namaAkun .= "<br>" . $item->kategori2;
                if (!empty($item->kategori3)) $namaAkun .= "<br>" . $item->kategori3;
                if (!empty($item->kategori4)) $namaAkun .= "<br>" . $item->kategori4;
                if (!empty($item->kategori5)) $namaAkun .= "<br>" . $item->kategori5;

                $rows .= '<tr>';
                $rows .= '<td style="text-align:center">'.$no++.'</td>';
                $rows .= '<td>'.date('d/m/Y', strtotime($item->Tanggal)).'</td>';
                $rows .= '<td>'.$kodeAkun.'</td>';
                $rows .= '<td>'.$namaAkun.'</td>';
                $rows .= '<td>'.$item->keterangan.' '.$item->nama_karyawan.'</td>';
                $rows .= '<td style="text-align:right">'.($rowDebit > 0 ? number_format($rowDebit, 0, ',', '.') : '-').'</td>';
                $rows .= '<td style="text-align:right">'.($rowKredit > 0 ? number_format($rowKredit, 0, ',', '.') : '-').'</td>';
                $rows .= '</tr>';
            }

            // Tambahkan baris total
            $rows .= '<tr style="font-weight:bold;background-color:#f9f9f9;">';
            $rows .= '<td colspan="5" style="text-align:right">Total</td>';
            $rows .= '<td style="text-align:right">'.number_format($totalDebit, 0, ',', '.').'</td>';
            $rows .= '<td style="text-align:right">'.number_format($totalKredit, 0, ',', '.').'</td>';
            $rows .= '</tr>';

            $html = '<!DOCTYPE html><html><head><title>Laporan Keuangan</title><style>
                body{font-family:Arial,sans-serif;font-size:12px;}
                table{width:100%;border-collapse:collapse;margin-bottom:20px;}
                table,th,td{border:1px solid #ddd;}
                th,td{padding:8px;text-align:left;}
                th{background-color:#f2f2f2;}
                tfoot td{font-weight:bold;background-color:#f9f9f9;}
                .text-right{text-align:right;}
                .text-center{text-align:center;}
                td{vertical-align:top;}
            </style></head><body>';
            $html .= '<h2 style="text-align:center">Laporan Keuangan</h2>';
            $html .= '<p style="text-align:center">Periode: '.date('d/m/Y', strtotime($startDate)).' - '.date('d/m/Y', strtotime($endDate)).'</p>';
            $html .= '<table><thead><tr>
                <th style="text-align:center">No</th>
                <th>Tanggal</th>
                <th>Kode Akun</th>
                <th>Nama Akun</th>
                <th>Keterangan</th>
                <th style="text-align:right">Debit</th>
                <th style="text-align:right">Kredit</th>
            </tr></thead><tbody>';
            $html .= $rows;
            $html .= '</tbody></table></body></html>';

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