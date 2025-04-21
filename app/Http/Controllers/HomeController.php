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

    private function getAccountType($kodeAkun) {
        $kode = (string)$kodeAkun;
        if (str_starts_with($kode, '111') || str_starts_with($kode, '112')) {
            return 'AKTIVA';
        } else if (str_starts_with($kode, '121') || str_starts_with($kode, '122') || str_starts_with($kode, '131')) {
            return 'PASIVA';
        } else if (str_starts_with($kode, '241') || str_starts_with($kode, '242')) {
            return 'PENDAPATAN';
        } else if (str_starts_with($kode, '251') || str_starts_with($kode, '252')) {
            return 'BEBAN';
        }
        return 'UNKNOWN';
    }

    private function calculateBalance($previousBalance, $debit, $kredit, $accountType) {
        $balance = $previousBalance;
        
        switch($accountType) {
            case 'AKTIVA':
                // Aktiva: bertambah di debit, berkurang di kredit
                $balance = $balance + $debit - $kredit;
                break;
            case 'PASIVA':
            case 'PENDAPATAN':
                // Pasiva & Pendapatan: bertambah di kredit, berkurang di debit
                $balance = $balance - $debit + $kredit;
                break;
            case 'BEBAN':
                // Beban: bertambah di debit, berkurang di kredit
                $balance = $balance + $debit - $kredit;
                break;
            default:
                $balance = $balance + $debit - $kredit;
        }
        
        return $balance;
    }

    public function index(Request $request)
    {
        // Default date range
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        try {
            // Ambil semua transaksi dalam periode
            $transaksis = DB::table('laporan_transaksis')
                ->whereBetween('Tanggal', [$startDate, $endDate])
                ->get();

            // Array untuk menyimpan total per kategori
            $totalsPerAkun = [];
            $totalPendapatan = 0;
            $totalBeban = 0;

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

            foreach ($transaksis as $transaksi) {
                // Proses untuk semua kategori
                for ($i = 1; $i <= 5; $i++) {
                    $kategoriField = $i === 1 ? 'kategori' : "kategori{$i}";
                    $uangMasukField = $i === 1 ? 'uang_masuk' : "uang_masuk{$i}";
                    $uangKeluarField = $i === 1 ? 'uang_keluar' : "uang_keluar{$i}";
                    
                    if (!empty($transaksi->$kategoriField)) {
                        $kodeAkun = $this->getKodeAkun($transaksi->$kategoriField);
                        if ($kodeAkun) {
                            $processAkun(
                                $kodeAkun,
                                $transaksi->$kategoriField,
                                $transaksi->$uangMasukField,
                                $transaksi->$uangKeluarField
                            );

                            // Hitung total berdasarkan jenis akun
                            $accountType = $this->getAccountType($kodeAkun);
                            if ($accountType === 'PENDAPATAN') {
                                $totalPendapatan = $this->calculateBalance(
                                    $totalPendapatan,
                                    floatval($transaksi->$uangMasukField ?? 0),
                                    floatval($transaksi->$uangKeluarField ?? 0),
                                    'PENDAPATAN'
                                );
                            } elseif ($accountType === 'BEBAN') {
                                $totalBeban = $this->calculateBalance(
                                    $totalBeban,
                                    floatval($transaksi->$uangMasukField ?? 0),
                                    floatval($transaksi->$uangKeluarField ?? 0),
                                    'BEBAN'
                                );
                            }
                        }
                    }
                }
            }

            // Hitung laba rugi
            $labaRugiTotal = $totalPendapatan - $totalBeban;
            $labaRugiBulanIni = $labaRugiTotal;

            // Kelompokkan data untuk tampilan
            $pendapatan = [];
            $beban = [];
            foreach ($totalsPerAkun as $kategori => $data) {
                $accountType = $this->getAccountType($data['kode']);
                if ($accountType === 'PENDAPATAN') {
                    $pendapatan[$kategori] = [
                        'kategori' => $kategori,
                        'kode_akun' => $data['kode'],
                        'nominal' => $this->calculateBalance(0, $data['debit'], $data['kredit'], 'PENDAPATAN')
                    ];
                } elseif ($accountType === 'BEBAN') {
                    $beban[$kategori] = [
                        'kategori' => $kategori,
                        'kode_akun' => $data['kode'],
                        'nominal' => $this->calculateBalance(0, $data['debit'], $data['kredit'], 'BEBAN')
                    ];
                }
            }

            // Get monthly totals for chart
            $monthlyTotals = DB::table('laporan_transaksis')
                ->select(
                    DB::raw('DATE_FORMAT(Tanggal, "%Y-%m") as periode'),
                    DB::raw('SUM(CASE WHEN LEFT(kategori, 3) IN ("241", "242") THEN uang_masuk ELSE 0 END) as total_debit'),
                    DB::raw('SUM(CASE WHEN LEFT(kategori, 3) IN ("251", "252") THEN uang_keluar ELSE 0 END) as total_kredit')
                )
                ->groupBy('periode')
                ->orderBy('periode')
                ->get();

            // Get category totals for distribution chart
            $categoryTotals = DB::table('laporan_transaksis')
                ->select(
                    'kategori',
                    DB::raw('SUM(CASE WHEN LEFT(kategori, 3) IN ("241", "242") THEN uang_masuk ELSE 0 END) as total_debit'),
                    DB::raw('SUM(CASE WHEN LEFT(kategori, 3) IN ("251", "252") THEN uang_keluar ELSE 0 END) as total_kredit')
                )
                ->whereBetween('Tanggal', [$startDate, $endDate])
                ->whereNotNull('kategori')
                ->groupBy('kategori')
                ->get();

            // Get recent transactions
            $recentTransactions = DB::table('laporan_transaksis')
                ->orderBy('Tanggal', 'desc')
                ->limit(10)
                ->get();

            // Calculate growth percentage
            $lastMonthStart = Carbon::parse($startDate)->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::parse($startDate)->subMonth()->endOfMonth();
            
            $lastMonthData = DB::table('laporan_transaksis')
                ->whereBetween('Tanggal', [$lastMonthStart, $lastMonthEnd])
                ->get();

            $lastMonthPendapatan = 0;
            $lastMonthBeban = 0;

            foreach ($lastMonthData as $transaksi) {
                $kodeAkun = $this->getKodeAkun($transaksi->kategori);
                if ($kodeAkun) {
                    $accountType = $this->getAccountType($kodeAkun);
                    if ($accountType === 'PENDAPATAN') {
                        $lastMonthPendapatan = $this->calculateBalance(
                            $lastMonthPendapatan,
                            floatval($transaksi->uang_masuk ?? 0),
                            floatval($transaksi->uang_keluar ?? 0),
                            'PENDAPATAN'
                        );
                    } elseif ($accountType === 'BEBAN') {
                        $lastMonthBeban = $this->calculateBalance(
                            $lastMonthBeban,
                            floatval($transaksi->uang_masuk ?? 0),
                            floatval($transaksi->uang_keluar ?? 0),
                            'BEBAN'
                        );
                    }
                }
            }

            $lastMonthLabaRugi = $lastMonthPendapatan - $lastMonthBeban;
            $currentMonthLabaRugi = $labaRugiBulanIni;
            
            $growthPercentage = $lastMonthLabaRugi != 0 ? 
                (($currentMonthLabaRugi - $lastMonthLabaRugi) / abs($lastMonthLabaRugi)) * 100 : 
                100;

            return view('home', compact(
                'startDate',
                'endDate',
                'totalPendapatan',
                'totalBeban',
                'labaRugiTotal',
                'labaRugiBulanIni',
                'growthPercentage',
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