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
Route::get('/dashboard', [SidebarController::class, 'index'])->name('dashboard');


//untuk Uang Masuk dan keluar 
Route::get('/uang-masuk', [UangMasukController::class, 'index'])->name('uang-masuk.index');
Route::post('/uang-masuk', [UangMasukController::class, 'store'])->name('uangmasuk.store');
Route::get('/uang-keluar', [UangKeluarController::class, 'index'])->name('uang-keluar.index');
Route::post('/uang-keluar', [UangKeluarController::class, 'store'])->name('uangkeluar.store');
Route::get('/input-gaji', [InputGajiController::class, 'index'])->name('input-gaji.index');
Route::post('/input-gaji', [InputGajiController::class, 'store'])->name('input-gaji.store');

//untuk Uang Gaji
Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
Route::delete('/gaji/{id}', [GajiController::class, 'destroy'])->name('gaji.destroy');
Route::get('/gaji/{id}/edit', [GajiController::class, 'edit'])->name('gaji.edit');
Route::put('/gaji/{id}', [GajiController::class, 'update'])->name('gaji.update');

// Tambahkan Data Karyawan
Route::get('/data-karyawan', [DataKaryawanController::class, 'index'])->name('data-karyawan.index');
Route::post('/data-karyawan', [DataKaryawanController::class, 'store'])->name('karyawan.store');
Route::resource('modal-karyawan', ModalKaryawanController::class);

//untuk Laporan
Route::get('/Laporan', [LaporanController::class, 'index'])->name('Laporan.index');
Route::get('laporan/export-excel', [App\Http\Controllers\LaporanController::class, 'exportExcel'])->name('laporan.export-excel');
Route::get('laporan/export-pdf', [App\Http\Controllers\LaporanController::class, 'exportPDF'])->name('laporan.export-pdf');
Route::delete('/laporan/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');

//untuk Home
Route::get('/home', [HomeController::class, 'index'])->name('home');

//untuk User
Route::get('/User', [UserController::class, 'index'])->name('User.index');
