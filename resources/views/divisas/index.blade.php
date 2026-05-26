@extends('layouts.app')

@section('title', 'Gestión de Divisas')

@section('content')

<style>
    .divisas-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .form-section, .chart-section-wrapper {
        background-color: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
    }
    
    .form-group {
        margin-bottom: 16px;
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
        transition: border-color 0.2s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
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
        transition: background-color 0.2s;
    }
    
    .btn-submit:hover {
        background-color: var(--primary-hover);
    }
    
    .table-wrapper {
        background-color: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        overflow-x: auto;
    }
    
    .table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .table th, .table td {
        padding: 12px 16px;
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }
    
    .table th {
        background-color: var(--bg-color);
        color: var(--text-muted);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
    }
    
    .badge {
        padding: 4px 8px;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .badge-entrada {
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
    }
    
    .badge-salida {
        background-color: rgba(239, 68, 68, 0.1);
        color: var(--danger-color);
    }

    #alert-success {
        display: none;
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .filter-form {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        align-items: end;
        margin-bottom: 20px;
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

    /* Responsive */
    @media (max-width: 1024px) {
        .divisas-grid {
            grid-template-columns: 1fr;
        }

        .filter-form {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="divisas-grid">
    <!-- Formulario de Registro -->
    <div class="form-section">
        <h3 style="margin-bottom: 20px;">Registrar Movimiento</h3>
        
        <div id="alert-success">Transacción registrada exitosamente.</div>
        
        <form id="divisa-form">
            @csrf
            <div class="form-group">
                <label for="tipo">Tipo de Operación</label>
                <select id="tipo" name="tipo" class="form-control" required>
                    <option value="entrada">Entrada (+)</option>
                    <option value="salida">Salida (-)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="medio">Medio</label>
                <select id="medio" name="medio" class="form-control" required>
                    <option value="banco">Banco</option>
                    <option value="efectivo">Efectivo</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="monto">Monto ($)</label>
                <input type="number" id="monto" name="monto" class="form-control" step="0.01" min="0.01" required>
            </div>
            
            <div class="form-group">
                <label for="fecha">Fecha</label>
                <input type="date" id="fecha" name="fecha" class="form-control" value="{{ date('Y-m-d') }}" required>
            </div>
            
            <div class="form-group">
                <label for="descripcion">Descripción</label>
                <input type="text" id="descripcion" name="descripcion" class="form-control" placeholder="Ej. Pago de cliente, Compra de insumos...">
            </div>
            
            <button type="submit" class="btn-submit" id="btn-submit">Guardar Transacción</button>
        </form>
    </div>

    <!-- Gráfica -->
    <div class="chart-section-wrapper">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Flujo de Divisas (Últimos 7 días)</h3>
            <div style="text-align: right;">
                <span style="font-size: 0.85rem; color: var(--text-muted); text-transform: uppercase;">Saldo Actual</span><br>
                <strong style="font-size: 1.5rem;" id="saldo-actual">${{ number_format($saldoActual, 2) }}</strong>
            </div>
        </div>
        <canvas id="divisasChart" height="120"></canvas>
    </div>
</div>

<!-- Historial / Tabla -->
<div class="table-wrapper">
    <h3 style="margin-bottom: 20px;">Historial de Movimientos</h3>

    <form method="GET" action="{{ route('divisas.index') }}" class="filter-form">
        <div class="form-group" style="margin-bottom: 0;">
            <label for="fecha_desde">Desde</label>
            <input type="date" id="fecha_desde" name="fecha_desde" class="form-control" value="{{ $fechaDesde }}">
        </div>

        <div class="form-group" style="margin-bottom: 0;">
            <label for="fecha_hasta">Hasta</label>
            <input type="date" id="fecha_hasta" name="fecha_hasta" class="form-control" value="{{ $fechaHasta }}">
        </div>

        <button type="submit" class="btn-submit">Filtrar</button>
        <a href="{{ route('divisas.index') }}" class="btn-secondary">Limpiar</a>
    </form>

    @if ($errors->any())
        <div style="color: var(--danger-color); font-weight: 600; margin-bottom: 16px;">
            {{ $errors->first() }}
        </div>
    @endif

    <table class="table" id="transactions-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Fecha</th>
                <th>Descripción</th>
                <th>Medio</th>
                <th>Tipo</th>
                <th>Monto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $t)
            <tr>
                <td>#{{ $t->id }}</td>
                <td>{{ $t->fecha->format('d/m/Y') }}</td>
                <td>{{ $t->descripcion ?? '-' }}</td>
                <td>{{ ucfirst($t->medio ?? 'efectivo') }}</td>
                <td>
                    <span class="badge {{ $t->tipo == 'entrada' ? 'badge-entrada' : 'badge-salida' }}">
                        {{ ucfirst($t->tipo) }}
                    </span>
                </td>
                <td style="font-weight: 600; color: {{ $t->tipo == 'entrada' ? 'var(--success-color)' : 'var(--danger-color)' }}">
                    {{ $t->tipo == 'entrada' ? '+' : '-' }}${{ number_format($t->monto, 2) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 20px;">No hay movimientos para el filtro seleccionado.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('divisasChart').getContext('2d');
        
        let chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($fechas) !!},
                datasets: [
                    {
                        label: 'Entradas',
                        data: {!! json_encode($datosEntradas) !!},
                        backgroundColor: '#10b981',
                        borderRadius: 4
                    },
                    {
                        label: 'Salidas',
                        data: {!! json_encode($datosSalidas) !!},
                        backgroundColor: '#ef4444',
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: { beginAtZero: true, grid: { color: '#e5e7eb' } }
                }
            }
        });

        const form = document.getElementById('divisa-form');
        const btnSubmit = document.getElementById('btn-submit');
        const alertSuccess = document.getElementById('alert-success');
        const tbody = document.querySelector('#transactions-table tbody');
        const saldoActualEl = document.getElementById('saldo-actual');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Guardando...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            fetch("{{ route('divisas.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': formData.get('_token'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(res => {
                if(res.success) {
                    // Update Alert
                    alertSuccess.style.display = 'block';
                    setTimeout(() => alertSuccess.style.display = 'none', 3000);
                    
                    // Reset form partially
                    form.reset();
                    document.getElementById('fecha').value = new Date().toISOString().split('T')[0];

                    // Update Chart Data
                    chart.data.labels = res.chartData.labels;
                    chart.data.datasets[0].data = res.chartData.entradas;
                    chart.data.datasets[1].data = res.chartData.salidas;
                    chart.update();

                    // Update Saldo Actual
                    saldoActualEl.textContent = '$' + parseFloat(res.saldoActual).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});

                    // Update Table
                    const t = res.transaction;
                    const fechaString = t.fecha.split('T')[0];
                    const dateParts = fechaString.split('-');
                    const formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;
                    const badgeClass = t.tipo === 'entrada' ? 'badge-entrada' : 'badge-salida';
                    const amountColor = t.tipo === 'entrada' ? 'var(--success-color)' : 'var(--danger-color)';
                    const amountSign = t.tipo === 'entrada' ? '+' : '-';
                    const formattedMonto = parseFloat(t.monto).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                    const medio = t.medio ? t.medio.charAt(0).toUpperCase() + t.medio.slice(1) : 'Efectivo';

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${t.id}</td>
                        <td>${formattedDate}</td>
                        <td>${t.descripcion || '-'}</td>
                        <td>${medio}</td>
                        <td><span class="badge ${badgeClass}">${t.tipo.charAt(0).toUpperCase() + t.tipo.slice(1)}</span></td>
                        <td style="font-weight: 600; color: ${amountColor}">${amountSign}$${formattedMonto}</td>
                    `;
                    
                    // Prepend new row
                    tbody.insertBefore(tr, tbody.firstChild);
                    
                    // Remove 21st row if table gets too long
                    if (tbody.children.length > 20) {
                        tbody.removeChild(tbody.lastChild);
                    }
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Ocurrió un error al guardar la transacción.");
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'Guardar Transacción';
            });
        });
    });
</script>
@endpush
