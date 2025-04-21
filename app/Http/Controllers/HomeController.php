<?php

namespace App\Http\Controllers;

use App\Models\HomeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
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

    private function prosesKategori($kategori, $uang_masuk, $uang_keluar, &$pendapatan, &$beban)
    {
        $kodeAkun = $this->getKodeAkun($kategori);
        if ($kodeAkun) {
            if (str_starts_with($kodeAkun, '24')) {
                // Untuk pendapatan
                $nominal = $uang_masuk;
                $data = [
                    'kategori' => $kategori,
                    'kode_akun' => $kodeAkun,
                    'nominal' => $nominal
                ];
                
                if (isset($pendapatan[$kategori])) {
                    $pendapatan[$kategori]['nominal'] += $nominal;
                } else {
                    $pendapatan[$kategori] = $data;
                }
            } elseif (str_starts_with($kodeAkun, '25')) {
                // Untuk beban
                $nominal = $uang_keluar;
                $data = [
                    'kategori' => $kategori,
                    'kode_akun' => $kodeAkun,
                    'nominal' => $nominal
                ];
                
                if (isset($beban[$kategori])) {
                    $beban[$kategori]['nominal'] += $nominal;
                } else {
                    $beban[$kategori] = $data;
                }
            }
        }
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

            // Reset total
            $totalUangMasuk = 0;
            $totalUangKeluar = 0;

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
                    
                    // Tambahkan ke total
                    $totalUangMasuk += $transaksi->uang_masuk ?? 0;
                    $totalUangKeluar += $transaksi->uang_keluar ?? 0;
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
                        
                        // Tambahkan ke total
                        $totalUangMasuk += $transaksi->{"uang_masuk$i"} ?? 0;
                        $totalUangKeluar += $transaksi->{"uang_keluar$i"} ?? 0;
                    }
                }
            }

            // Calculate current month totals
            $currentMonthUangMasuk = $totalUangMasuk;
            $currentMonthUangKeluar = $totalUangKeluar;

            // Calculate last month totals for growth percentage
            $lastMonthStart = Carbon::parse($startDate)->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::parse($startDate)->subMonth()->endOfMonth();
            
            $lastMonthTransaksis = DB::table('laporan_transaksis')
                ->whereBetween('Tanggal', [$lastMonthStart, $lastMonthEnd])
            ->get();

            $lastMonthUangMasuk = 0;
            $lastMonthUangKeluar = 0;

            foreach ($lastMonthTransaksis as $transaksi) {
                $lastMonthUangMasuk += $transaksi->uang_masuk ?? 0;
                $lastMonthUangKeluar += $transaksi->uang_keluar ?? 0;
                
                for ($i = 2; $i <= 5; $i++) {
                    $lastMonthUangMasuk += $transaksi->{"uang_masuk$i"} ?? 0;
                    $lastMonthUangKeluar += $transaksi->{"uang_keluar$i"} ?? 0;
                }
            }

            $lastMonthTotal = $lastMonthUangMasuk - $lastMonthUangKeluar;
            $currentMonthTotal = $totalUangMasuk - $totalUangKeluar;
            
            // Calculate growth percentage
            $growthPercentage = $lastMonthTotal != 0 ? 
                (($currentMonthTotal - $lastMonthTotal) / abs($lastMonthTotal)) * 100 : 
                100;

            // Calculate total saldo dan laba rugi sesuai format di Labarugi blade
            $totalSaldo = -($totalUangMasuk) - $totalUangKeluar;
            $labaRugiTotal = -($totalUangMasuk) - $totalUangKeluar;
            $labaRugiBulanIni = -($currentMonthUangMasuk) - $currentMonthUangKeluar;

            // Get category breakdown
            $categoryTotals = collect($pendapatan)->map(function($item) {
                return [
                    'kategori' => $item['kategori'],
                    'total_debit' => $item['nominal'],
                    'total_kredit' => 0
                ];
            })->merge(
                collect($beban)->map(function($item) {
                    return [
                        'kategori' => $item['kategori'],
                        'total_debit' => 0,
                        'total_kredit' => $item['nominal']
                    ];
                })
            )->values();

        // Get monthly totals for chart
        $monthlyTotals = DB::table('laporan_transaksis')
            ->select(
                DB::raw('DATE_FORMAT(Tanggal, "%Y-%m") as periode'),
                    DB::raw('SUM(uang_masuk) + SUM(COALESCE(uang_masuk2, 0)) + SUM(COALESCE(uang_masuk3, 0)) + SUM(COALESCE(uang_masuk4, 0)) + SUM(COALESCE(uang_masuk5, 0)) as total_debit'),
                    DB::raw('SUM(uang_keluar) + SUM(COALESCE(uang_keluar2, 0)) + SUM(COALESCE(uang_keluar3, 0)) + SUM(COALESCE(uang_keluar4, 0)) + SUM(COALESCE(uang_keluar5, 0)) as total_kredit')
            )
            ->groupBy('periode')
            ->orderBy('periode')
            ->get();

        // Get recent transactions
        $recentTransactions = DB::table('laporan_transaksis')
            ->orderBy('Tanggal', 'desc')
            ->limit(10)
            ->get();

            // Debug log untuk memeriksa nilai
            Log::info("Total Uang Masuk: " . $totalUangMasuk);
            Log::info("Total Uang Keluar: " . $totalUangKeluar);
            Log::info("Total Saldo: " . $totalSaldo);
            Log::info("Laba Rugi Total: " . $labaRugiTotal);
            Log::info("Laba Rugi Bulan Ini: " . $labaRugiBulanIni);

        return view('home', compact(
                'startDate',
                'endDate',
            'totalUangMasuk',
            'totalUangKeluar',
                'currentMonthUangMasuk',
                'currentMonthUangKeluar',
                'totalSaldo',
                'growthPercentage',
                'labaRugiTotal',
                'labaRugiBulanIni',
            'monthlyTotals',
            'categoryTotals',
                'recentTransactions',
                'pendapatan',
                'beban'
            ));

        } catch (\Exception $e) {
            Log::error("Error in HomeController: " . $e->getMessage());
            throw $e;
        }
    }

    public function filter(Request $request)
    {
        return $this->index($request);
    }
}