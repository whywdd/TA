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
        // Default date range untuk bulan ini jika tidak ada parameter
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));
        
        try {
            // Query dasar
            $query = DB::table('laporan_transaksis');
            
            // Terapkan filter tanggal
            $query->whereBetween('Tanggal', [$startDate, $endDate]);

            // Ambil semua transaksi
            $transaksis = $query->get();

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

            // Get daily totals for chart
            $monthlyTotals = DB::table('laporan_transaksis')
                ->select(
                    DB::raw('DATE_FORMAT(Tanggal, "%Y-%m-%d") as periode'),
                    DB::raw('SUM(uang_masuk) as total_debit'),
                    DB::raw('SUM(uang_keluar) as total_kredit')
                )
                ->whereBetween('Tanggal', [$startDate, $endDate])
                ->groupBy('periode')
                ->orderBy('periode')
                ->get();

            // Debug untuk memastikan data terisi
            Log::info('Daily Totals:', ['data' => $monthlyTotals->toArray()]);

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

            // Ambil data neraca saldo untuk pie chart
            $rawTransaksis = DB::table('laporan_transaksis')
                ->whereBetween('Tanggal', [$startDate, $endDate])
                ->orderBy('kode', 'asc')
                ->get();
            $totalsPerAkun = [];
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
            $finalTransaksis = [];
            foreach ($totalsPerAkun as $kategori => $data) {
                $kodeAwal = substr($data['kode'], 0, 3);
                $saldo = $data['debit'] - $data['kredit'];
                if (in_array($kodeAwal, ['111', '112']) || in_array($kodeAwal, ['251', '252'])) {
                    if ($saldo != 0) {
                        $finalTransaksis[] = [
                            'kode' => $data['kode'],
                            'kategori' => $kategori,
                            'debit' => $saldo,
                            'kredit' => 0
                        ];
                    }
                } else {
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
            $neracaTransaksis = collect($finalTransaksis);
            $pieLabels = $neracaTransaksis->pluck('kategori')->toArray();
            $pieData = $neracaTransaksis->map(function($item) {
                return abs($item['debit']) + abs($item['kredit']);
            })->toArray();

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
                'beban',
                'pieLabels',
                'pieData'
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

    public function showAll()
    {
        try {
            // Set tanggal default untuk semua data
            $startDate = '1970-01-01'; // Tanggal awal yang sangat lama
            $endDate = '2099-12-31';   // Tanggal akhir yang sangat jauh
            
            // Query dasar tanpa filter tanggal
            $query = DB::table('laporan_transaksis');
            
            // Ambil semua transaksi
            $transaksis = $query->get();

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

            // Get daily totals for chart
            $monthlyTotals = DB::table('laporan_transaksis')
                ->select(
                    DB::raw('DATE_FORMAT(Tanggal, "%Y-%m-%d") as periode'),
                    DB::raw('SUM(uang_masuk) as total_debit'),
                    DB::raw('SUM(uang_keluar) as total_kredit')
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
                ->whereNotNull('kategori')
                ->groupBy('kategori')
                ->get();

            // Get recent transactions
            $recentTransactions = DB::table('laporan_transaksis')
                ->orderBy('Tanggal', 'desc')
                ->limit(10)
                ->get();

            // Calculate growth percentage
            $lastMonthStart = Carbon::now()->subMonth()->startOfMonth();
            $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();
            
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

            // Ambil data neraca saldo untuk pie chart
            $rawTransaksis = DB::table('laporan_transaksis')
                ->orderBy('kode', 'asc')
                ->get();
            $totalsPerAkun = [];
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
            $finalTransaksis = [];
            foreach ($totalsPerAkun as $kategori => $data) {
                $kodeAwal = substr($data['kode'], 0, 3);
                $saldo = $data['debit'] - $data['kredit'];
                if (in_array($kodeAwal, ['111', '112']) || in_array($kodeAwal, ['251', '252'])) {
                    if ($saldo != 0) {
                        $finalTransaksis[] = [
                            'kode' => $data['kode'],
                            'kategori' => $kategori,
                            'debit' => $saldo,
                            'kredit' => 0
                        ];
                    }
                } else {
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
            $neracaTransaksis = collect($finalTransaksis);
            $pieLabels = $neracaTransaksis->pluck('kategori')->toArray();
            $pieData = $neracaTransaksis->map(function($item) {
                return abs($item['debit']) + abs($item['kredit']);
            })->toArray();

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
                'beban',
                'pieLabels',
                'pieData'
            ));

        } catch (\Exception $e) {
            Log::error("Error in HomeController: " . $e->getMessage());
            throw $e;
        }
    }
}