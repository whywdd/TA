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
    Route::get('/User', [UserController::class, 'index'])->name('User.index');
    Route::get('/User/create', [UserController::class, 'create'])->name('User.create');
    Route::post('/User', [UserController::class, 'store'])->name('User.store');
    Route::get('/User/{id}/edit', [UserController::class, 'edit'])->name('User.edit');
    Route::put('/User/{id}', [UserController::class, 'update'])->name('User.update');
    Route::delete('/User/{id}', [UserController::class, 'destroy'])->name('User.destroy');
    
    // Routes untuk manajemen user
    Route::get('/users/data', [UserController::class, 'getData']);
    Route::post('/users/create', [UserController::class, 'create']);
});
