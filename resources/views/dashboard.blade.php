@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')

<style>
    .dashboard-filter {
        background-color: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        margin-bottom: 24px;
    }

    .dolar-rates {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .dolar-card {
        background-color: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 20px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }

    .dolar-card-title {
        color: var(--text-muted);
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .dolar-card-value {
        color: var(--text-main);
        font-size: 1.75rem;
        font-weight: 800;
    }

    .dolar-card-date {
        color: var(--text-muted);
        font-size: 0.8rem;
        margin-top: 8px;
    }

    .filter-form {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        align-items: end;
    }

    .form-group label {
        display: block;
        font-weight: 500;
        margin-bottom: 8px;
        color: var(--text-main);
    }

    .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        font-size: 1rem;
    }

    .btn-submit {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        width: 100%;
    }

    .btn-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--bg-color);
        color: var(--text-main);
        border: 1px solid var(--border-color);
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
    }

    @media (max-width: 1024px) {
        .filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="dolar-rates">
    @php($tasa = $tasasDolar->get('oficial'))
    <div class="dolar-card">
        <div class="dolar-card-title">Dólar Oficial</div>
        <div class="dolar-card-value">
            @if ($tasa)
                Bs {{ number_format($tasa['promedio'], 2, ',', '.') }}
            @else
                No disponible
            @endif
        </div>
        @if ($tasa)
            <div class="dolar-card-date">
                Actualizado: {{ \Carbon\Carbon::parse($tasa['fechaActualizacion'])->format('d/m/Y H:i') }}
            </div>
        @endif
    </div>
</div>

<div class="dashboard-filter">
    <form method="GET" action="{{ route('dashboard') }}" class="filter-form">
        <div class="form-group">
            <label for="fecha_desde">Desde</label>
            <input type="date" id="fecha_desde" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}">
        </div>

        <div class="form-group">
            <label for="fecha_hasta">Hasta</label>
            <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}">
        </div>

        <button type="submit" class="btn-submit">Filtrar</button>
        <a href="{{ route('dashboard') }}" class="btn-secondary">Limpiar</a>
    </form>

    @if ($errors->any())
        <div style="color: var(--danger-color); font-weight: 600; margin-top: 16px;">
            {{ $errors->first() }}
        </div>
    @endif
</div>

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
    <h3 class="section-title">Flujo de Divisas</h3>
    <canvas id="divisasChart" height="100"></canvas>
</div>

@endsection

@stack('scripts')
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('divisasChart').getContext('2d');
        
        const labels = {!! json_encode($fechas->values()) !!};
        const dataEntradas = {!! json_encode($datosEntradas->values()) !!};
        const dataSalidas = {!! json_encode($datosSalidas->values()) !!};

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
