<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DivisaTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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

        $tasaOficial = null;

        try {
            $response = Http::timeout(5)->get('https://ve.dolarapi.com/v1/dolares');

            if ($response->successful()) {
                $tasaOficial = collect($response->json())->firstWhere('fuente', 'oficial')['promedio'] ?? null;
            }
        } catch (\Throwable $exception) {
            $tasaOficial = null;
        }

        return view('divisas.index', compact('transactions', 'fechas', 'datosEntradas', 'datosSalidas', 'saldoActual', 'fechaDesde', 'fechaHasta', 'tasaOficial'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'medio' => 'required|in:banco,efectivo',
            'moneda_original' => 'required|in:USD,VES',
            'monto_original' => 'required|numeric|min:0.01',
            'tasa_cambio' => 'required_if:moneda_original,VES|nullable|numeric|min:0.01',
            'fecha' => 'required|date',
            'descripcion' => 'nullable|string|max:255',
        ]);

        $data = $request->only([
            'tipo',
            'medio',
            'moneda_original',
            'monto_original',
            'tasa_cambio',
            'fecha',
            'descripcion',
        ]);

        $data['monto'] = $data['moneda_original'] === 'VES'
            ? round($data['monto_original'] / $data['tasa_cambio'], 2)
            : round($data['monto_original'], 2);

        if ($data['moneda_original'] === 'USD') {
            $data['tasa_cambio'] = null;
        }

        $transaction = DivisaTransaction::create($data);

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
