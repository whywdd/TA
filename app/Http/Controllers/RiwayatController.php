<?php

namespace App\Http\Controllers;

use App\Models\RiwayatModel;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RiwayatController extends Controller
{
    public function index()
    {
        $riwayat = RiwayatModel::orderBy('tanggal', 'desc')
                              ->orderBy('waktu', 'desc')
                              ->get();
        
        return view('Riwayat', compact('riwayat'));
    }

    public function store($userId, $namaUser, $aksi, $keterangan = '')
    {
        $now = Carbon::now();
        
        RiwayatModel::create([
            'user_id' => $userId,
            'nama_user' => $namaUser,
            'tanggal' => $now->format('Y-m-d'),
            'waktu' => $now->format('H:i:s'),
            'aksi' => $aksi,
            'keterangan' => $keterangan
        ]);
    }
} 