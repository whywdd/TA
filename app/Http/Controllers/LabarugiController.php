<?php

namespace App\Http\Controllers;

use App\Models\LabarugiModel;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LabarugiController extends Controller
{
    public function index(Request $request)
    {
        // Default periode adalah bulan ini
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $model = new LabarugiModel();

        // Helper function untuk menjumlahkan uang masuk
        $sumUangMasuk = "COALESCE(uang_masuk, 0) + COALESCE(uang_masuk2, 0) + COALESCE(uang_masuk3, 0) + COALESCE(uang_masuk4, 0) + COALESCE(uang_masuk5, 0)";
        
        // Helper function untuk menjumlahkan uang keluar
        $sumUangKeluar = "COALESCE(uang_keluar, 0) + COALESCE(uang_keluar2, 0) + COALESCE(uang_keluar3, 0) + COALESCE(uang_keluar4, 0) + COALESCE(uang_keluar5, 0)";

        // Mengambil data pendapatan operasional (41)
        $pendapatanPenjualan = $model->where('kode', '241001')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangMasuk) as total"))
            ->value('total') ?? 0;

        $pendapatanJasa = $model->where('kode', '241002')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangMasuk) as total"))
            ->value('total') ?? 0;

        // Mengambil data pendapatan non-operasional (42)
        $pendapatanBunga = $model->where('kode', '242001')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangMasuk) as total"))
            ->value('total') ?? 0;

        $pendapatanSewa = $model->where('kode', '242002')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangMasuk) as total"))
            ->value('total') ?? 0;

        $pendapatanKomisi = $model->where('kode', '242003')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangMasuk) as total"))
            ->value('total') ?? 0;

        $pendapatanLain = $model->where('kode', '242004')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangMasuk) as total"))
            ->value('total') ?? 0;

        // Mengambil data beban operasional (51)
        $bebanGaji = $model->where('kode', '251001')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangKeluar) as total"))
            ->value('total') ?? 0;

        $bebanSewa = $model->where('kode', '251002')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangKeluar) as total"))
            ->value('total') ?? 0;

        $bebanUtilitas = $model->where('kode', '251003')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangKeluar) as total"))
            ->value('total') ?? 0;

        $bebanPenyusutan = $model->where('kode', '251004')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangKeluar) as total"))
            ->value('total') ?? 0;

        $bebanSupplies = $model->where('kode', '251005')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangKeluar) as total"))
            ->value('total') ?? 0;

        $bebanIklan = $model->where('kode', '251006')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangKeluar) as total"))
            ->value('total') ?? 0;

        // Mengambil data beban non-operasional (52)
        $bebanBunga = $model->where('kode', '252001')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangKeluar) as total"))
            ->value('total') ?? 0;

        $bebanLain = $model->where('kode', '252002')
            ->whereBetween('Tanggal', [$startDate, $endDate])
            ->select(DB::raw("SUM($sumUangKeluar) as total"))
            ->value('total') ?? 0;

        // Menghitung total pendapatan
        $totalPendapatanOperasional = $pendapatanPenjualan + $pendapatanJasa;
        $totalPendapatanNonOperasional = $pendapatanBunga + $pendapatanSewa + $pendapatanKomisi + $pendapatanLain;
        $totalPendapatan = $totalPendapatanOperasional + $totalPendapatanNonOperasional;

        // Menghitung total beban
        $totalBebanOperasional = $bebanGaji + $bebanSewa + $bebanUtilitas + $bebanPenyusutan + $bebanSupplies + $bebanIklan;
        $totalBebanNonOperasional = $bebanBunga + $bebanLain;
        $totalBeban = $totalBebanOperasional + $totalBebanNonOperasional;

        // Menghitung laba rugi
        $totalLabaRugi = $totalPendapatan - $totalBeban;

        return view('Labarugi', compact(
            'startDate',
            'endDate',
            'pendapatanPenjualan',
            'pendapatanJasa',
            'pendapatanBunga',
            'pendapatanSewa',
            'pendapatanKomisi',
            'pendapatanLain',
            'bebanGaji',
            'bebanSewa',
            'bebanUtilitas',
            'bebanPenyusutan',
            'bebanSupplies',
            'bebanIklan',
            'bebanBunga',
            'bebanLain',
            'totalPendapatanOperasional',
            'totalPendapatanNonOperasional',
            'totalBebanOperasional',
            'totalBebanNonOperasional',
            'totalPendapatan',
            'totalBeban',
            'totalLabaRugi'
        ));
    }
}
