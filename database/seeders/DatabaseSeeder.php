<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        \App\Models\Banco::create(['nombre' => 'Banesco', 'numero_cuenta' => '0134-XXXX-XXXX', 'saldo' => 15000]);
        \App\Models\Banco::create(['nombre' => 'Mercantil', 'numero_cuenta' => '0105-XXXX-XXXX', 'saldo' => 5000]);

        \App\Models\DivisaTransaction::create(['tipo' => 'saldo_inicial', 'monto' => 1000, 'descripcion' => 'Saldo Inicial Caja', 'fecha' => now()->subDays(10)]);
        \App\Models\DivisaTransaction::create(['tipo' => 'entrada', 'monto' => 500, 'descripcion' => 'Venta Cliente A', 'fecha' => now()->subDays(5)]);
        \App\Models\DivisaTransaction::create(['tipo' => 'salida', 'monto' => 200, 'descripcion' => 'Pago Proveedor', 'fecha' => now()->subDays(2)]);

        \App\Models\CuentaCobrar::create(['deudor' => 'Cliente Mayorista', 'monto' => 1200, 'fecha_vencimiento' => now()->addDays(15)]);
        \App\Models\CuentaCobrar::create(['deudor' => 'Cliente Detal', 'monto' => 300, 'fecha_vencimiento' => now()->addDays(5)]);

        \App\Models\CuentaPagar::create(['acreedor' => 'Proveedor Insumos', 'monto' => 800, 'fecha_vencimiento' => now()->addDays(10)]);
    }
}
