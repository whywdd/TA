<?php

namespace App\Http\Controllers;

use App\Models\HomeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        // Get current month data
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Calculate total saldo
        $totalSaldo = HomeModel::selectRaw('SUM(uang_masuk - uang_keluar) as saldo')->first()->saldo ?? 0;

        // Calculate total uang masuk dan uang keluar
        $totalUangMasuk = HomeModel::sum('uang_masuk');
        $totalUangKeluar = HomeModel::sum('uang_keluar');
        
        // Calculate current month's income and expenses
        $currentMonthUangMasuk = HomeModel::whereMonth('Tanggal', $currentMonth)
            ->whereYear('Tanggal', $currentYear)
            ->sum('uang_masuk');
            
        $currentMonthUangKeluar = HomeModel::whereMonth('Tanggal', $currentMonth)
            ->whereYear('Tanggal', $currentYear)
            ->sum('uang_keluar');
            
        // Calculate profit/loss
        $labaRugiTotal = $totalUangMasuk - $totalUangKeluar;
        $labaRugiBulanIni = $currentMonthUangMasuk - $currentMonthUangKeluar;

        // Get monthly totals for chart
        $monthlyTotals = HomeModel::selectRaw('
            DATE_FORMAT(Tanggal, "%Y-%m") as periode,
            SUM(uang_masuk) as total_debit,
            SUM(uang_keluar) as total_kredit,
            SUM(uang_masuk - uang_keluar) as laba_rugi
        ')
        ->groupBy('periode')
        ->orderBy('periode')
        ->get();

        // Get category breakdown
        $categoryTotals = HomeModel::selectRaw('
            kategori,
            SUM(uang_masuk) as total_debit,
            SUM(uang_keluar) as total_kredit,
            SUM(uang_masuk - uang_keluar) as laba_rugi
        ')
        ->groupBy('kategori')
        ->orderBy('total_debit', 'desc')
        ->get();

        // Get recent transactions
        $recentTransactions = HomeModel::orderBy('Tanggal', 'desc')
            ->limit(10)
            ->get();

        // Calculate month-over-month growth
        $lastMonthTotal = HomeModel::whereMonth('Tanggal', Carbon::now()->subMonth()->month)
            ->whereYear('Tanggal', Carbon::now()->subMonth()->year)
            ->sum('uang_masuk');

        $growthPercentage = $lastMonthTotal != 0 
            ? (($currentMonthUangMasuk - $lastMonthTotal) / $lastMonthTotal) * 100 
            : 0;

        return view('home', compact(
            'totalSaldo',
            'totalUangMasuk',
            'totalUangKeluar',
            'currentMonthUangMasuk',
            'currentMonthUangKeluar',
            'labaRugiTotal',
            'labaRugiBulanIni',
            'monthlyTotals',
            'categoryTotals',
            'recentTransactions',
            'growthPercentage'
        ));
    }
}