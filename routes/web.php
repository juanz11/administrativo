<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DivisaController;
use App\Http\Controllers\EstadisticasVentasController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('panel.auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/divisas', [DivisaController::class, 'index'])->name('divisas.index');
    Route::post('/divisas', [DivisaController::class, 'store'])->name('divisas.store');

    Route::get('/bancos', [App\Http\Controllers\BancoController::class, 'index'])->name('bancos.index');
    Route::post('/bancos', [App\Http\Controllers\BancoController::class, 'store'])->name('bancos.store');

    Route::prefix('estadisticas-ventas')->name('estadisticas.')->group(function () {
        Route::get('/', [EstadisticasVentasController::class, 'grafica'])->name('grafica');
        Route::get('/importar', [EstadisticasVentasController::class, 'showImportForm'])->name('import.form');
        Route::post('/importar', [EstadisticasVentasController::class, 'import'])->name('import');
        Route::get('/historial', [EstadisticasVentasController::class, 'historial'])->name('historial');
        Route::post('/historial/{id}/seleccionar', [EstadisticasVentasController::class, 'seleccionarImportacion'])->name('historial.seleccionar');
        Route::get('/debug-keys', [EstadisticasVentasController::class, 'debugKeys'])->name('debug.keys');
        Route::get('/reprocesar', [EstadisticasVentasController::class, 'reprocesar'])->name('reprocesar');
    });
});
