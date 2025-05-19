<?php

namespace App\Http\Controllers;

use App\Models\NeracasaldoModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\NeracaSaldoExport;

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

    public function exportExcel(Request $request)
    {
        try {
            // Ambil parameter filter tanggal
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

            $rawTransaksis = NeracasaldoModel::whereBetween('Tanggal', [$startDate, $endDate])
                ->orderBy('kode', 'asc')
                ->get();
            
            $processedData = [];
            $totalsPerAkun = [];
            $totalDebit = 0;
            $totalKredit = 0;

            // Proses data transaksi
            foreach ($rawTransaksis as $transaksi) {
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

                // Proses semua kolom transaksi
                $processAkun($transaksi->kode, $transaksi->kategori, $transaksi->uang_masuk, $transaksi->uang_keluar);
                $processAkun($transaksi->kode2, $transaksi->kategori2, $transaksi->uang_masuk2, $transaksi->uang_keluar2);
                $processAkun($transaksi->kode3, $transaksi->kategori3, $transaksi->uang_masuk3, $transaksi->uang_keluar3);
                $processAkun($transaksi->kode4, $transaksi->kategori4, $transaksi->uang_masuk4, $transaksi->uang_keluar4);
                $processAkun($transaksi->kode5, $transaksi->kategori5, $transaksi->uang_masuk5, $transaksi->uang_keluar5);
            }

            // Proses saldo untuk setiap akun
            foreach ($totalsPerAkun as $kategori => $data) {
                $kodeAwal = substr($data['kode'], 0, 3);
                $saldo = $data['debit'] - $data['kredit'];
                
                // Reset nilai debit dan kredit
                $debit = 0;
                $kredit = 0;

                // Logika untuk menentukan posisi saldo
                if (in_array($kodeAwal, ['111', '112', '251', '252'])) {
                    // Aktiva dan Beban
                    if ($saldo > 0) {
                        $debit = $saldo;
                    } else {
                        $kredit = abs($saldo);
                    }
                } else {
                    // Pasiva dan Pendapatan
                    if ($saldo < 0) {
                        $kredit = abs($saldo);
                    } else {
                        $debit = $saldo;
                    }
                }

                if ($saldo != 0) {
                    $processedData[] = [
                        'kode' => $data['kode'],
                        'kategori' => $kategori,
                        'debit' => $debit,
                        'kredit' => $kredit
                    ];
                    $totalDebit += $debit;
                    $totalKredit += $kredit;
                }
            }

            // Tambahkan baris total
            $processedData[] = [
                'kode' => '',
                'kategori' => 'Total',
                'debit' => $totalDebit,
                'kredit' => $totalKredit
            ];

            // Buat class export inline dengan format yang diperbaiki
            $export = new class($processedData) implements FromCollection, WithHeadings {
                protected $data;
                
                public function __construct($data) 
                {
                    // Format data untuk Excel
                    $formattedData = collect($data)->map(function ($item) {
                        return [
                            'kode' => $item['kode'],
                            'kategori' => $item['kategori'],
                            'debit' => $item['debit'] > 0 ? $item['debit'] : 0,
                            'kredit' => $item['kredit'] > 0 ? $item['kredit'] : 0
                        ];
                    });
                    
                    $this->data = $formattedData;
                }
                
                public function collection()
                {
                    return $this->data;
                }

                public function headings(): array
                {
                    return [
                        'Kode',
                        'Nama Akun',
                        'Debit',
                        'Kredit'
                    ];
                }
            };

            return Excel::download($export, 'neraca-saldo-'.date('Y-m-d').'.xlsx');
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

            $rawTransaksis = NeracasaldoModel::whereBetween('Tanggal', [$startDate, $endDate])
                ->orderBy('kode', 'asc')
                ->get();
            
            $processedData = [];
            $totalsPerAkun = [];
            $totalDebit = 0;
            $totalKredit = 0;

            // Proses data seperti di fungsi index
            foreach ($rawTransaksis as $transaksi) {
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

                $processAkun($transaksi->kode, $transaksi->kategori, $transaksi->uang_masuk, $transaksi->uang_keluar);
                $processAkun($transaksi->kode2, $transaksi->kategori2, $transaksi->uang_masuk2, $transaksi->uang_keluar2);
                $processAkun($transaksi->kode3, $transaksi->kategori3, $transaksi->uang_masuk3, $transaksi->uang_keluar3);
                $processAkun($transaksi->kode4, $transaksi->kategori4, $transaksi->uang_masuk4, $transaksi->uang_keluar4);
                $processAkun($transaksi->kode5, $transaksi->kategori5, $transaksi->uang_masuk5, $transaksi->uang_keluar5);
            }

            foreach ($totalsPerAkun as $kategori => $data) {
                $kodeAwal = substr($data['kode'], 0, 3);
                $saldo = $data['debit'] - $data['kredit'];
                
                // Reset nilai debit dan kredit
                $debit = 0;
                $kredit = 0;

                // Logika untuk menentukan posisi saldo
                if (in_array($kodeAwal, ['111', '112', '251', '252'])) {
                    // Aktiva dan Beban
                    if ($saldo > 0) {
                        $debit = $saldo;
                    } else {
                        $kredit = abs($saldo);
                    }
                } else {
                    // Pasiva dan Pendapatan
                    if ($saldo < 0) {
                        $kredit = abs($saldo);
                    } else {
                        $debit = $saldo;
                    }
                }

                if ($saldo != 0) {
                    $processedData[] = [
                        'kode' => $data['kode'],
                        'kategori' => $kategori,
                        'debit' => $debit,
                        'kredit' => $kredit
                    ];
                    $totalDebit += $debit;
                    $totalKredit += $kredit;
                }
            }

            // Tambahkan baris total
            $processedData[] = [
                'kode' => '',
                'kategori' => 'Total',
                'debit' => $totalDebit,
                'kredit' => $totalKredit
            ];

            // Generate PDF dengan style yang diperbaiki
            $html = '
            <!DOCTYPE html>
            <html>
            <head>
                <title>Neraca Saldo</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 12px;
                    }
                    h2 {
                        text-align: center;
                        margin-bottom: 5px;
                    }
                    p.periode {
                        text-align: center;
                        margin-top: 0;
                        margin-bottom: 20px;
                        font-size: 11px;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-bottom: 20px;
                    }
                    table, th, td {
                        border: 1px solid #000;
                    }
                    th, td {
                        padding: 8px;
                        text-align: left;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    .text-right {
                        text-align: right;
                    }
                    .total-row td {
                        font-weight: bold;
                        background-color: #f2f2f2;
                    }
                    .amount {
                        text-align: right;
                        white-space: nowrap;
                    }
                </style>
            </head>
            <body>
                <h2>Neraca Saldo</h2>
                <p class="periode">Periode: ' . date('F Y', strtotime($startDate)) . '</p>
                
                <table>
                    <thead>
                        <tr>
                            <th style="width: 15%;">Kode</th>
                            <th style="width: 45%;">Nama Akun</th>
                            <th style="width: 20%;" class="amount">Debit</th>
                            <th style="width: 20%;" class="amount">Kredit</th>
                        </tr>
                    </thead>
                    <tbody>';
                    
                    foreach($processedData as $data) {
                        if ($data['kategori'] !== 'Total') {
                            $html .= '
                            <tr>
                                <td>'.$data['kode'].'</td>
                                <td>'.$data['kategori'].'</td>
                                <td class="amount">'.($data['debit'] > 0 ? number_format($data['debit'], 0, ',', '.') : '-').'</td>
                                <td class="amount">'.($data['kredit'] > 0 ? number_format($data['kredit'], 0, ',', '.') : '-').'</td>
                            </tr>';
                        }
                    }
                    
                    $html .= '
                    </tbody>
                    <tfoot>
                        <tr class="total-row">
                            <td colspan="2" style="text-align: right;">Total:</td>
                            <td class="amount">'.number_format($totalDebit, 0, ',', '.').'</td>
                            <td class="amount">'.number_format($totalKredit, 0, ',', '.').'</td>
                        </tr>
                    </tfoot>
                </table>
            </body>
            </html>';
            
            $pdf = PDF::loadHTML($html);
            return $pdf->download('neraca-saldo-'.date('Y-m-d').'.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
