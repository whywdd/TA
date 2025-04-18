<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabarugiModel extends Model
{
    protected $table = 'laporan_transaksis';
    protected $guarded = ['id'];

    // Konstanta untuk kode akun pendapatan (uang masuk)
    const KODE_PENDAPATAN = [
        'OPERASIONAL' => [
            '241001' => 'pendapatan penjualan',
            '241002' => 'pendapatan jasa'
        ],
        'NON_OPERASIONAL' => [
            '242001' => 'pendapatan bunga',
            '242002' => 'pendapatan sewa',
            '242003' => 'pendapatan komisi',
            '242004' => 'pendapatan lain'
        ]
    ];

    // Konstanta untuk kode akun beban (uang keluar)
    const KODE_BEBAN = [
        'OPERASIONAL' => [
            '251001' => 'beban gaji',
            '251002' => 'beban sewa',
            '251003' => 'beban utilitas',
            '251004' => 'beban penyusutan',
            '251005' => 'beban supplies',
            '251006' => 'beban iklan'
        ],
        'NON_OPERASIONAL' => [
            '252001' => 'beban bunga',
            '252002' => 'beban lain'
        ]
    ];

    // Scope untuk filter berdasarkan periode
    public function scopePeriode($query, $startDate, $endDate)
    {
        return $query->whereBetween('Tanggal', [$startDate, $endDate]);
    }

    // Scope untuk pendapatan operasional (uang masuk)
    public function scopePendapatanOperasional($query)
    {
        return $query->whereIn('kode', array_keys(self::KODE_PENDAPATAN['OPERASIONAL']));
    }

    // Scope untuk pendapatan non-operasional (uang masuk)
    public function scopePendapatanNonOperasional($query)
    {
        return $query->whereIn('kode', array_keys(self::KODE_PENDAPATAN['NON_OPERASIONAL']));
    }

    // Scope untuk beban operasional (uang keluar)
    public function scopeBebanOperasional($query)
    {
        return $query->whereIn('kode', array_keys(self::KODE_BEBAN['OPERASIONAL']));
    }

    // Scope untuk beban non-operasional (uang keluar)
    public function scopeBebanNonOperasional($query)
    {
        return $query->whereIn('kode', array_keys(self::KODE_BEBAN['NON_OPERASIONAL']));
    }

    // Helper untuk mengecek apakah kode termasuk pendapatan
    public static function isPendapatan($kode)
    {
        return in_array($kode, array_merge(
            array_keys(self::KODE_PENDAPATAN['OPERASIONAL']),
            array_keys(self::KODE_PENDAPATAN['NON_OPERASIONAL'])
        ));
    }

    // Helper untuk mengecek apakah kode termasuk beban
    public static function isBeban($kode)
    {
        return in_array($kode, array_merge(
            array_keys(self::KODE_BEBAN['OPERASIONAL']),
            array_keys(self::KODE_BEBAN['NON_OPERASIONAL'])
        ));
    }
}
