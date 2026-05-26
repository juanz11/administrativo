<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DivisaTransaction;
use Carbon\Carbon;

class DivisaController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
        ]);

        $fechaDesde = $request->input('fecha_desde');
        $fechaHasta = $request->input('fecha_hasta');

        $transactionsQuery = DivisaTransaction::query();

        if ($fechaDesde) {
            $transactionsQuery->whereDate('fecha', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $transactionsQuery->whereDate('fecha', '<=', $fechaHasta);
        }

        $transactions = $transactionsQuery
            ->orderBy('fecha', 'desc')
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();
        
        // Prepare chart data (last 7 days of transactions or so)
        $fechas = collect();
        $datosEntradas = collect();
        $datosSalidas = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $fechas->push(Carbon::now()->subDays($i)->format('d M'));
            
            $entradas = DivisaTransaction::whereDate('fecha', $date)
                ->where('tipo', 'entrada')
                ->sum('monto');
                
            $salidas = DivisaTransaction::whereDate('fecha', $date)
                ->where('tipo', 'salida')
                ->sum('monto');
                
            $datosEntradas->push($entradas);
            $datosSalidas->push($salidas);
        }

        $saldoActual = DivisaTransaction::where('tipo', 'entrada')->sum('monto') 
                     - DivisaTransaction::where('tipo', 'salida')->sum('monto');

        return view('divisas.index', compact('transactions', 'fechas', 'datosEntradas', 'datosSalidas', 'saldoActual', 'fechaDesde', 'fechaHasta'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'medio' => 'required|in:banco,efectivo',
            'monto' => 'required|numeric|min:0.01',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $transaction = DivisaTransaction::create($request->all());

        // Prepare updated chart data to send back
        $fechas = [];
        $datosEntradas = [];
        $datosSalidas = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $fechas[] = Carbon::now()->subDays($i)->format('d M');
            
            $entradas = DivisaTransaction::whereDate('fecha', $date)
                ->where('tipo', 'entrada')
                ->sum('monto');
                
            $salidas = DivisaTransaction::whereDate('fecha', $date)
                ->where('tipo', 'salida')
                ->sum('monto');
                
            $datosEntradas[] = $entradas;
            $datosSalidas[] = $salidas;
        }

        $saldoActual = DivisaTransaction::where('tipo', 'entrada')->sum('monto') 
                     - DivisaTransaction::where('tipo', 'salida')->sum('monto');

        return response()->json([
            'success' => true,
            'transaction' => $transaction,
            'chartData' => [
                'labels' => $fechas,
                'entradas' => $datosEntradas,
                'salidas' => $datosSalidas
            ],
            'saldoActual' => $saldoActual
        ]);
    }
}
