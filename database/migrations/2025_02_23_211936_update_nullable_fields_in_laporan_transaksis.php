<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNullableFieldsInLaporanTransaksis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('laporan_transaksis', function (Blueprint $table) {
            // Ubah kolom-kolom menjadi nullable
            $table->string('keterangan')->nullable()->change();
            $table->string('nama_karyawan')->nullable()->change();
            $table->decimal('uang_masuk', 10, 2)->default(0)->change();
            $table->decimal('uang_keluar', 10, 2)->default(0)->change();
            $table->decimal('gaji', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('laporan_transaksis', function (Blueprint $table) {
            // Kembalikan kolom-kolom menjadi tidak nullable
            $table->string('keterangan')->nullable(false)->change();
            $table->string('nama_karyawan')->nullable(false)->change();
            $table->decimal('uang_masuk', 10, 2)->nullable(false)->change();
            $table->decimal('uang_keluar', 10, 2)->nullable(false)->change();
            $table->decimal('gaji', 10, 2)->nullable(false)->change();
        });
    }
}