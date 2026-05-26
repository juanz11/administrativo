<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisa_transactions', function (Blueprint $table) {
            $table->enum('medio', ['banco', 'efectivo'])->default('efectivo')->after('tipo');
        });
    }

    public function down(): void
    {
        Schema::table('divisa_transactions', function (Blueprint $table) {
            $table->dropColumn('medio');
        });
    }
};
