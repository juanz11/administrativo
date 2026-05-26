<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Banco;
use App\Models\CuentaCobrar;
use App\Models\CuentaPagar;
use App\Models\DivisaTransaction;
use Illuminate\Support\Facades\Http;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $divisasQuery = DivisaTransaction::query();

        if ($fechaDesde) {
            $divisasQuery->whereDate('fecha', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $divisasQuery->whereDate('fecha', '<=', $fechaHasta);
        }

        $entradas = (clone $divisasQuery)->whereIn('tipo', ['entrada', 'saldo_inicial'])->sum('monto');
        $salidas = (clone $divisasQuery)->where('tipo', 'salida')->sum('monto');
        $saldoDivisas = $entradas - $salidas;

        // Totales Bancos
        $saldoBancos = Banco::sum('saldo');

        // Totales Cuentas por Cobrar y Pagar
        $totalCuentasCobrar = CuentaCobrar::where('estado', 'pendiente')->sum('monto');
        $totalCuentasPagar = CuentaPagar::where('estado', 'pendiente')->sum('monto');

        $fechas = collect();
        $datosEntradas = collect();
        $datosSalidas = collect();

        $inicioGrafica = $fechaDesde ? now()->parse($fechaDesde) : now()->subDays(6);
        $finGrafica = $fechaHasta ? now()->parse($fechaHasta) : now();

        if ($inicioGrafica->diffInDays($finGrafica) > 31) {
            $inicioGrafica = $finGrafica->copy()->subDays(31);
        }

        for ($fecha = $inicioGrafica->copy(); $fecha->lte($finGrafica); $fecha->addDay()) {
            $fechaConsulta = $fecha->format('Y-m-d');
            $fechas->push($fecha->format('d/m'));

            $entradaDiaQuery = DivisaTransaction::whereIn('tipo', ['entrada', 'saldo_inicial'])->whereDate('fecha', $fechaConsulta);
            $salidaDiaQuery = DivisaTransaction::where('tipo', 'salida')->whereDate('fecha', $fechaConsulta);

            $datosEntradas->push($entradaDiaQuery->sum('monto'));
            $datosSalidas->push($salidaDiaQuery->sum('monto'));
        }

        $tasasDolar = collect();

        try {
            $response = Http::timeout(5)->get('https://ve.dolarapi.com/v1/dolares');

            if ($response->successful()) {
                $tasasDolar = collect($response->json())->keyBy('fuente');
            }
        } catch (\Throwable $exception) {
            $tasasDolar = collect();
        }

        return view('dashboard', compact(
            'saldoDivisas', 'entradas', 'salidas', 
            'saldoBancos', 
            'totalCuentasCobrar', 'totalCuentasPagar',
            'fechas', 'datosEntradas', 'datosSalidas',
            'fechaDesde', 'fechaHasta',
            'tasasDolar'
        ));
    }
}
