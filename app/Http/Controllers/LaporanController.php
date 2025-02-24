<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LaporanModel;

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
}