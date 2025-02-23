<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('laporan_transaksis', function (Blueprint $table) {
            $table->id();
            $table->Date('Tanggal');
            $table->string('kode');
            $table->string('kategori');
            $table->text('keterangan');
            $table->text('nama_karyawan');
            $table->decimal('uang_masuk', 15, 2);
            $table->decimal('uang_keluar', 15, 2);
            $table->decimal('gaji', 15, 2);
            $table->timestamps();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_transaksis');
    }
};
