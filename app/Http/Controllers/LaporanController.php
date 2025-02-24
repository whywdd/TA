<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;

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
        return Excel::download(new LaporanExport, 'laporan-keuangan-'.date('Y-m-d').'.xlsx');
    }
    
    public function exportPDF()
    {
        $laporan = LaporanModel::orderBy('Tanggal', 'desc')->get();
        $totalUangMasuk = LaporanModel::sum('uang_masuk');
        $totalUangKeluar = LaporanModel::sum('uang_keluar');
        $totalGaji = LaporanModel::sum('gaji');
        $totalKredit = $totalUangKeluar + $totalGaji;
        $saldo = $totalUangMasuk - $totalKredit;
        
        $pdf = PDF::loadView('laporan-pdf', compact(
            'laporan', 
            'totalUangMasuk', 
            'totalUangKeluar', 
            'totalGaji', 
            'totalKredit', 
            'saldo'
        ));
        
        return $pdf->download('laporan-keuangan-'.date('Y-m-d').'.pdf');
    }
}