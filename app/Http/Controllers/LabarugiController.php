<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LabarugiModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LabarugiController extends Controller
{
    private function getKodeAkun($kategori)
    {
        $kategori = trim(strtolower($kategori));
        
        // Pendapatan Operasional (41)
        if (str_contains($kategori, 'pendapatan penjualan')) return '241001';
        if (str_contains($kategori, 'pendapatan jasa')) return '241002';
        
        // Pendapatan Non-Operasional (42)
        if (str_contains($kategori, 'pendapatan bunga')) return '242001';
        if (str_contains($kategori, 'pendapatan sewa')) return '242002';
        if (str_contains($kategori, 'pendapatan komisi')) return '242003';
        if (str_contains($kategori, 'pendapatan lain')) return '242004';
        
        // Beban Operasional (51)
        if (str_contains($kategori, 'beban gaji')) return '251001';
        if (str_contains($kategori, 'beban sewa')) return '251002';
        if (str_contains($kategori, 'beban utilitas')) return '251003';
        if (str_contains($kategori, 'beban penyusutan')) return '251004';
        if (str_contains($kategori, 'beban supplies')) return '251005';
        if (str_contains($kategori, 'beban iklan')) return '251006';
        
        // Beban Non-Operasional (52)
        if (str_contains($kategori, 'beban bunga')) return '252001';
        if (str_contains($kategori, 'beban lain')) return '252002';
        
        return null;
    }

    public function index(Request $request)
    {
        // Default date range
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Inisialisasi array untuk menyimpan data
        $pendapatan = [];
        $beban = [];

        try {
            // Ambil semua transaksi dalam periode
            $transaksis = DB::table('laporan_transaksis')
                ->whereBetween('Tanggal', [$startDate, $endDate])
                ->get();

            foreach ($transaksis as $transaksi) {
                // Proses kategori1
                if (!empty($transaksi->kategori)) {
                    $this->prosesKategori(
                        $transaksi->kategori,
                        $transaksi->uang_masuk ?? 0,
                        $transaksi->uang_keluar ?? 0,
                        $pendapatan,
                        $beban
                    );
                }

                // Proses kategori2-5
                for ($i = 2; $i <= 5; $i++) {
                    $kategori = $transaksi->{"kategori$i"};
                    if (!empty($kategori)) {
                        $this->prosesKategori(
                            $kategori,
                            $transaksi->{"uang_masuk$i"} ?? 0,
                            $transaksi->{"uang_keluar$i"} ?? 0,
                            $pendapatan,
                            $beban
                        );
                    }
                }
            }

            // Hitung total
            $total_pendapatan = array_sum(array_column($pendapatan, 'nominal'));
            $total_beban = abs(array_sum(array_column($beban, 'nominal')));
            $laba_rugi = $total_pendapatan - $total_beban;

            return view('Labarugi', [
                'startDate' => $startDate,
                'endDate' => $endDate,
                'pendapatan' => $pendapatan,
                'beban' => $beban,
                'total_pendapatan' => $total_pendapatan,
                'total_beban' => $total_beban,
                'laba_rugi' => $laba_rugi
            ]);

        } catch (\Exception $e) {
            Log::error("Error in LabarugiController: " . $e->getMessage());
            throw $e;
        }
    }

    private function prosesKategori($kategori, $uang_masuk, $uang_keluar, &$pendapatan, &$beban)
    {
        $kodeAkun = $this->getKodeAkun($kategori);
        if ($kodeAkun) {
            $nominal = $uang_masuk - $uang_keluar;
            $data = [
                'kategori' => $kategori,
                'kode_akun' => $kodeAkun,
                'nominal' => $nominal
            ];

            if (str_starts_with($kodeAkun, '24')) {
                if (isset($pendapatan[$kategori])) {
                    $pendapatan[$kategori]['nominal'] += $nominal;
                } else {
                    $pendapatan[$kategori] = $data;
                }
            } elseif (str_starts_with($kodeAkun, '25')) {
                if (isset($beban[$kategori])) {
                    $beban[$kategori]['nominal'] += $nominal;
                } else {
                    $beban[$kategori] = $data;
                }
            }
        }
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

            // Inisialisasi array untuk menyimpan data
            $pendapatan = [];
            $beban = [];

            // Ambil semua transaksi dalam periode
            $transaksis = DB::table('laporan_transaksis')
                ->whereBetween('Tanggal', [$startDate, $endDate])
                ->get();

            foreach ($transaksis as $transaksi) {
                // Proses kategori1
                if (!empty($transaksi->kategori)) {
                    $this->prosesKategori(
                        $transaksi->kategori,
                        $transaksi->uang_masuk ?? 0,
                        $transaksi->uang_keluar ?? 0,
                        $pendapatan,
                        $beban
                    );
                }

                // Proses kategori2-5
                for ($i = 2; $i <= 5; $i++) {
                    $kategori = $transaksi->{"kategori$i"};
                    if (!empty($kategori)) {
                        $this->prosesKategori(
                            $kategori,
                            $transaksi->{"uang_masuk$i"} ?? 0,
                            $transaksi->{"uang_keluar$i"} ?? 0,
                            $pendapatan,
                            $beban
                        );
                    }
                }
            }

            // Hitung total
            $total_pendapatan = array_sum(array_column($pendapatan, 'nominal'));
            $total_beban = abs(array_sum(array_column($beban, 'nominal')));
            $laba_rugi = $total_pendapatan - $total_beban;

            // Siapkan data untuk Excel
            $rows = [];
            
            // Header
            $rows[] = ['Laporan Laba Rugi'];
            $rows[] = ['Periode: ' . date('F Y', strtotime($startDate))];
            $rows[] = []; // Baris kosong

            // Pendapatan
            $rows[] = ['Pendapatan'];
            foreach ($pendapatan as $item) {
                $rows[] = [
                    ucwords($item['kategori']),
                    number_format(abs($item['nominal']), 0, ',', '.')
                ];
            }
            $rows[] = ['Total Pendapatan', number_format(abs($total_pendapatan), 0, ',', '.')];
            $rows[] = []; // Baris kosong

            // Beban
            $rows[] = ['Beban'];
            foreach ($beban as $item) {
                $rows[] = [
                    ucwords($item['kategori']),
                    number_format(abs($item['nominal']), 0, ',', '.')
                ];
            }
            $rows[] = ['Total Beban', number_format($total_beban, 0, ',', '.')];
            $rows[] = []; // Baris kosong

            // Laba/Rugi
            $rows[] = ['Laba/Rugi Bersih', number_format($laba_rugi, 0, ',', '.')];

            $export = new class($rows) implements \Maatwebsite\Excel\Concerns\FromArray {
                protected $rows;
                public function __construct($rows) { $this->rows = $rows; }
                public function array(): array { return $this->rows; }
            };

            return \Maatwebsite\Excel\Facades\Excel::download($export, 'laporan-laba-rugi-'.date('Y-m-d').'.xlsx');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor Excel: ' . $e->getMessage());
        }
    }

    public function exportPDF(Request $request)
    {
        try {
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
            $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

            // Inisialisasi array untuk menyimpan data
            $pendapatan = [];
            $beban = [];

            // Ambil semua transaksi dalam periode
            $transaksis = DB::table('laporan_transaksis')
                ->whereBetween('Tanggal', [$startDate, $endDate])
                ->get();

            foreach ($transaksis as $transaksi) {
                // Proses kategori1
                if (!empty($transaksi->kategori)) {
                    $this->prosesKategori(
                        $transaksi->kategori,
                        $transaksi->uang_masuk ?? 0,
                        $transaksi->uang_keluar ?? 0,
                        $pendapatan,
                        $beban
                    );
                }

                // Proses kategori2-5
                for ($i = 2; $i <= 5; $i++) {
                    $kategori = $transaksi->{"kategori$i"};
                    if (!empty($kategori)) {
                        $this->prosesKategori(
                            $kategori,
                            $transaksi->{"uang_masuk$i"} ?? 0,
                            $transaksi->{"uang_keluar$i"} ?? 0,
                            $pendapatan,
                            $beban
                        );
                    }
                }
            }

            // Hitung total
            $total_pendapatan = array_sum(array_column($pendapatan, 'nominal'));
            $total_beban = abs(array_sum(array_column($beban, 'nominal')));
            $laba_rugi = $total_pendapatan - $total_beban;

            $html = '<!DOCTYPE html><html><head><title>Laporan Laba Rugi</title><style>
                body{font-family:Arial,sans-serif;font-size:12px;}
                table{width:100%;border-collapse:collapse;margin-bottom:20px;}
                table,th,td{border:1px solid #ddd;}
                th,td{padding:8px;text-align:left;}
                th{background-color:#6b46c1;color:white;}
                .bg-gray-100{background-color:#f3f4f6;}
                .bg-purple-100{background-color:#f3e8ff;}
                .text-right{text-align:right;}
                .font-bold{font-weight:bold;}
                .pl-8{padding-left:2rem;}
            </style></head><body>';
            
            $html .= '<h2 style="text-align:center">Laporan Laba Rugi</h2>';
            $html .= '<p style="text-align:center">Periode: ' . date('F Y', strtotime($startDate)) . '</p>';
            
            $html .= '<table>';
            
            // Header
            $html .= '<tr><th>Keterangan</th><th style="text-align:right">Nominal</th></tr>';
            
            // Pendapatan
            $html .= '<tr class="bg-gray-100"><td colspan="2" class="font-bold">Pendapatan</td></tr>';
            foreach ($pendapatan as $item) {
                $html .= '<tr>';
                $html .= '<td class="pl-8">' . ucwords($item['kategori']) . '</td>';
                $html .= '<td class="text-right">' . number_format(abs($item['nominal']), 0, ',', '.') . '</td>';
                $html .= '</tr>';
            }
            $html .= '<tr class="font-bold"><td>Total Pendapatan</td><td class="text-right">' . number_format(abs($total_pendapatan), 0, ',', '.') . '</td></tr>';
            
            // Beban
            $html .= '<tr class="bg-gray-100"><td colspan="2" class="font-bold">Beban</td></tr>';
            foreach ($beban as $item) {
                $html .= '<tr>';
                $html .= '<td class="pl-8">' . ucwords($item['kategori']) . '</td>';
                $html .= '<td class="text-right">' . number_format(abs($item['nominal']), 0, ',', '.') . '</td>';
                $html .= '</tr>';
            }
            $html .= '<tr class="font-bold"><td>Total Beban</td><td class="text-right">' . number_format($total_beban, 0, ',', '.') . '</td></tr>';
            
            // Laba/Rugi
            $html .= '<tr class="bg-purple-100"><td class="font-bold">Laba/Rugi Bersih</td><td class="text-right font-bold">';
            if ($laba_rugi < 0) {
                $html .= '-Rp ' . number_format(abs($laba_rugi), 0, ',', '.');
            } else {
                $html .= 'Rp ' . number_format($laba_rugi, 0, ',', '.');
            }
            $html .= '</td></tr>';
            
            $html .= '</table></body></html>';

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            return $pdf->download('laporan-laba-rugi-'.date('Y-m-d').'.pdf');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mengekspor PDF: ' . $e->getMessage());
        }
    }
}
