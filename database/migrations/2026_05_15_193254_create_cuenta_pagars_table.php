<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuenta_pagars', function (Blueprint $table) {
            $table->id();
            $table->string('acreedor');
            $table->decimal('monto', 15, 2);
            $table->date('fecha_vencimiento');
            $table->enum('estado', ['pendiente', 'pagado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_pagars');
    }
};
