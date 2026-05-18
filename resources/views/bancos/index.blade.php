@extends('layouts.app')

@section('title', 'Gestión de Bancos')

@section('content')

<style>
    .bancos-grid {
        display: grid;
        grid-template-columns: 1fr 2fr;
        gap: 24px;
        margin-bottom: 24px;
    }
    
    .form-section {
        background-color: var(--card-bg);
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        border: 1px solid var(--border-color);
        align-self: start;
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

    #alert-success {
        display: none;
        background-color: rgba(16, 185, 129, 0.1);
        color: var(--success-color);
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 16px;
        border: 1px solid rgba(16, 185, 129, 0.2);
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .bancos-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="bancos-grid">
    <!-- Formulario de Registro -->
    <div class="form-section">
        <h3 style="margin-bottom: 20px;">Registrar Banco</h3>
        
        <div id="alert-success">Banco registrado exitosamente.</div>
        
        <form id="banco-form">
            @csrf
            
            <div class="form-group">
                <label for="nombre">Nombre del Banco <span style="color: var(--danger-color);">*</span></label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej. Banesco, Mercantil..." required>
            </div>
            
            <div class="form-group">
                <label for="rif">RIF <span style="font-size: 0.8rem; color: var(--text-muted); font-weight: normal;">(Opcional)</span></label>
                <input type="text" id="rif" name="rif" class="form-control" placeholder="Ej. J-12345678-9">
            </div>
            
            <button type="submit" class="btn-submit" id="btn-submit">Guardar Banco</button>
        </form>
    </div>

    <!-- Historial / Tabla -->
    <div class="table-wrapper">
        <h3 style="margin-bottom: 20px;">Bancos Registrados</h3>
        <table class="table" id="bancos-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>RIF</th>
                    <th>Fecha Registro</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bancos as $b)
                <tr>
                    <td>#{{ $b->id }}</td>
                    <td style="font-weight: 600;">{{ $b->nombre }}</td>
                    <td>{{ $b->rif ?: '-' }}</td>
                    <td>{{ $b->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr id="empty-row">
                    <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 20px;">No hay bancos registrados aún.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById('banco-form');
        const btnSubmit = document.getElementById('btn-submit');
        const alertSuccess = document.getElementById('alert-success');
        const tbody = document.querySelector('#bancos-table tbody');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            btnSubmit.disabled = true;
            btnSubmit.textContent = 'Guardando...';

            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());

            fetch("{{ route('bancos.store') }}", {
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
                    
                    // Reset form
                    form.reset();

                    // Remove empty row if exists
                    const emptyRow = document.getElementById('empty-row');
                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    // Update Table
                    const b = res.banco;
                    const fechaString = b.created_at.split('T')[0];
                    const dateParts = fechaString.split('-');
                    const formattedDate = `${dateParts[2]}/${dateParts[1]}/${dateParts[0]}`;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>#${b.id}</td>
                        <td style="font-weight: 600;">${b.nombre}</td>
                        <td>${b.rif || '-'}</td>
                        <td>${formattedDate}</td>
                    `;
                    
                    // Prepend new row
                    tbody.insertBefore(tr, tbody.firstChild);
                }
            })
            .catch(error => {
                console.error("Error:", error);
                alert("Ocurrió un error al guardar el banco.");
            })
            .finally(() => {
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'Guardar Banco';
            });
        });
    });
</script>
@endpush
