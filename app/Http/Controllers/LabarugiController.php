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
}
