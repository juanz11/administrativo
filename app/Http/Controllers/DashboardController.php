<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Banco;
use App\Models\CuentaCobrar;
use App\Models\CuentaPagar;
use App\Models\DivisaTransaction;

class DashboardController extends Controller
{
    public function index()
    {
        // Totales de Divisas
        $entradas = DivisaTransaction::whereIn('tipo', ['entrada', 'saldo_inicial'])->sum('monto');
        $salidas = DivisaTransaction::where('tipo', 'salida')->sum('monto');
        $saldoDivisas = $entradas - $salidas;

        // Totales Bancos
        $saldoBancos = Banco::sum('saldo');

        // Totales Cuentas por Cobrar y Pagar
        $totalCuentasCobrar = CuentaCobrar::where('estado', 'pendiente')->sum('monto');
        $totalCuentasPagar = CuentaPagar::where('estado', 'pendiente')->sum('monto');

        // Datos para Gráficas (últimos 7 días)
        $fechas = collect();
        $datosEntradas = collect();
        $datosSalidas = collect();

        for ($i = 6; $i >= 0; $i--) {
            $fecha = now()->subDays($i)->format('Y-m-d');
            $fechas->push($fecha);
            
            $entradaDia = DivisaTransaction::where('tipo', 'entrada')->whereDate('fecha', $fecha)->sum('monto');
            $salidaDia = DivisaTransaction::where('tipo', 'salida')->whereDate('fecha', $fecha)->sum('monto');
            
            $datosEntradas->push($entradaDia);
            $datosSalidas->push($salidaDia);
        }

        return view('dashboard', compact(
            'saldoDivisas', 'entradas', 'salidas', 
            'saldoBancos', 
            'totalCuentasCobrar', 'totalCuentasPagar',
            'fechas', 'datosEntradas', 'datosSalidas'
        ));
    }
}
