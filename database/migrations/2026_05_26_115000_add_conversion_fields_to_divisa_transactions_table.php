<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisa_transactions', function (Blueprint $table) {
            $table->enum('moneda_original', ['USD', 'VES'])->default('USD')->after('medio');
            $table->decimal('monto_original', 15, 2)->nullable()->after('moneda_original');
            $table->decimal('tasa_cambio', 15, 4)->nullable()->after('monto_original');
        });
    }

    public function down(): void
    {
        Schema::table('divisa_transactions', function (Blueprint $table) {
            $table->dropColumn(['moneda_original', 'monto_original', 'tasa_cambio']);
        });
    }
};
