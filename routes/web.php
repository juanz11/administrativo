<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisaController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/divisas', [DivisaController::class, 'index'])->name('divisas.index');
Route::post('/divisas', [DivisaController::class, 'store'])->name('divisas.store');

Route::get('/bancos', [App\Http\Controllers\BancoController::class, 'index'])->name('bancos.index');
Route::post('/bancos', [App\Http\Controllers\BancoController::class, 'store'])->name('bancos.store');
