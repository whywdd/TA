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
        Schema::table('manajemen_stok', function (Blueprint $table) {
            $table->enum('status_tahap', ['bibit', 'remaja', 'indukan', 'siap_bunga', 'bunga_potong'])->default('bibit')->after('akun_coa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('manajemen_stok', function (Blueprint $table) {
            $table->dropColumn('status_tahap');
        });
    }
};
