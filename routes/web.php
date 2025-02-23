<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SidebarController;
use App\Http\Controllers\UangMasukController;
use App\Http\Controllers\UangKeluarController;
use App\Http\Controllers\GajiController;
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
Route::post('/uang-keluar', [UangMasukController::class, 'store'])->name('uangkeluar.store');

//untuk Uang Gaji
Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');

//untuk Laporan
Route::get('/Laporan', [LaporanController::class, 'index'])->name('Laporan.index');

//untuk Home
Route::get('/home', [HomeController::class, 'index'])->name('home');

//untuk User
Route::get('/User', [UserController::class, 'index'])->name('User.index');
