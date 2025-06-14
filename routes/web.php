<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\UangMasukController;
use App\Http\Controllers\UangKeluarController;
use App\Http\Controllers\InputGajiController;
use App\Http\Controllers\ModalKaryawanController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\DataKaryawanController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RekeningController;
use App\Http\Controllers\NeracasaldoController;
use App\Http\Controllers\LabarugiController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RiwayatController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Middleware group untuk routes yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [SidebarController::class, 'index'])->name('dashboard');
    Route::get('/uang-masuk', [UangMasukController::class, 'index'])->name('uang-masuk.index');
    Route::post('/uang-masuk', [UangMasukController::class, 'store'])->name('uangmasuk.store');
    Route::get('/uang-keluar', [UangKeluarController::class, 'index'])->name('uang-keluar.index');
    Route::post('/uang-keluar', [UangKeluarController::class, 'store'])->name('uangkeluar.store');
    Route::get('/input-gaji', [InputGajiController::class, 'index'])->name('input-gaji.index');
    Route::post('/input-gaji', [InputGajiController::class, 'store'])->name('input-gaji.store');
    Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
    Route::delete('/gaji/{id}', [GajiController::class, 'destroy'])->name('gaji.destroy');
    Route::get('/gaji/{id}/edit', [GajiController::class, 'edit'])->name('gaji.edit');
    Route::put('/gaji/{id}', [GajiController::class, 'update'])->name('gaji.update');
    Route::get('/data-karyawan', [DataKaryawanController::class, 'index'])->name('data-karyawan.index');
    Route::post('/data-karyawan', [DataKaryawanController::class, 'store'])->name('karyawan.store');
    Route::resource('modal-karyawan', ModalKaryawanController::class);
    Route::get('/Laporan', [LaporanController::class, 'index'])->name('Laporan.index');
    Route::get('laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
    Route::get('laporan/export-pdf', [LaporanController::class, 'exportPDF'])->name('laporan.export-pdf');
    Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::get('/home/filter', [HomeController::class, 'filter'])->name('home.filter');
    Route::get('/home/all', [HomeController::class, 'showAll'])->name('home.all');
    Route::get('/User', [UserController::class, 'index'])->name('User.index');
    Route::get('/User/create', [UserController::class, 'create'])->name('User.create');
    Route::post('/User', [UserController::class, 'store'])->name('User.store');
    Route::get('/User/{id}/edit', [UserController::class, 'edit'])->name('User.edit');
    Route::put('/User/{id}', [UserController::class, 'update'])->name('User.update');
    Route::delete('/User/{id}', [UserController::class, 'destroy'])->name('User.destroy');
    
    // Routes untuk manajemen user
    Route::get('/users/data', [UserController::class, 'getData']);
    Route::post('/users/create', [UserController::class, 'create']);

    // Routes untuk Rekening
    Route::get('/rekening', [RekeningController::class, 'index'])->name('rekening.index');
    Route::delete('/rekening/{id}', [RekeningController::class, 'destroy'])->name('rekening.destroy');
    Route::get('/rekening/export-excel', [RekeningController::class, 'exportExcel'])->name('rekening.export-excel');
    Route::get('/rekening/export-pdf', [RekeningController::class, 'exportPDF'])->name('rekening.export-pdf');
    Route::get('/rekening/filter', [RekeningController::class, 'filter'])->name('rekening.filter');
    
    // Routes untuk Laporan
    Route::get('/laporan/filter', [LaporanController::class, 'filter'])->name('laporan.filter');
    
    // Routes untuk Neraca Saldo
    Route::get('/neracasaldo/filter', [NeracasaldoController::class, 'filter'])->name('neracasaldo.filter');
    Route::get('/neracasaldo/export-excel', [NeracasaldoController::class, 'exportExcel'])->name('neracasaldo.export-excel');
    Route::get('/neracasaldo/export-pdf', [NeracasaldoController::class, 'exportPDF'])->name('neracasaldo.export-pdf');
    Route::resource('neracasaldo', NeracasaldoController::class);
    
    // Routes untuk Laba Rugi
    Route::get('/labarugi', [LabarugiController::class, 'index'])->name('labarugi.index');
    Route::get('/labarugi/filter', [LabarugiController::class, 'filter'])->name('labarugi.filter');
    Route::get('/labarugi/export-excel', [LabarugiController::class, 'exportExcel'])->name('labarugi.export-excel');
    Route::get('/labarugi/export-pdf', [LabarugiController::class, 'exportPDF'])->name('labarugi.export-pdf');

    // Route untuk Riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat.index');
});

Route::get('/register', [RegisterController::class, 'index'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
