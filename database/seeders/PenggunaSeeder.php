<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class PenggunaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('penggunas')->insert([
            'nama' => 'Budi',
            'email' => 'budivespaendut@gmail.com',
            'password' => Hash::make('budi1234'),
            'tipe_pengguna' => 'owner',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
