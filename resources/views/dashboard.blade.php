@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<!-- Tarjetas de Resumen -->
<div class="cards-grid">
    <div class="card">
        <div class="card-title">Saldo en Divisas</div>
        <div class="card-value">${{ number_format($saldoDivisas, 2) }}</div>
        <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">
            Entradas: <span style="color: var(--success-color)">+${{ number_format($entradas, 2) }}</span> <br>
            Salidas: <span style="color: var(--danger-color)">-${{ number_format($salidas, 2) }}</span>
        </div>
    </div>

    <div class="card success">
        <div class="card-title">Saldo en Bancos</div>
        <div class="card-value">Bs {{ number_format($saldoBancos, 2) }}</div>
    </div>

    <div class="card warning">
        <div class="card-title">Cuentas por Cobrar</div>
        <div class="card-value">${{ number_format($totalCuentasCobrar, 2) }}</div>
        <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Pendientes de cobro</div>
    </div>

    <div class="card danger">
        <div class="card-title">Cuentas por Pagar</div>
        <div class="card-value">${{ number_format($totalCuentasPagar, 2) }}</div>
        <div style="margin-top: 10px; font-size: 0.85rem; color: var(--text-muted);">Pendientes de pago</div>
    </div>
</div>

<!-- Gráfica de Flujo de Divisas -->
<div class="chart-section">
    <h3 class="section-title">Flujo de Divisas (Últimos 7 días)</h3>
    <canvas id="divisasChart" height="100"></canvas>
</div>

@endsection

@stack('scripts')
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('divisasChart').getContext('2d');
        
        const labels = {!! json_encode($fechas->reverse()->values()) !!};
        const dataEntradas = {!! json_encode($datosEntradas->reverse()->values()) !!};
        const dataSalidas = {!! json_encode($datosSalidas->reverse()->values()) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Entradas',
                        data: dataEntradas,
                        backgroundColor: '#10b981', // success color
                        borderRadius: 4
                    },
                    {
                        label: 'Salidas',
                        data: dataSalidas,
                        backgroundColor: '#ef4444', // danger color
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e5e7eb'
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
