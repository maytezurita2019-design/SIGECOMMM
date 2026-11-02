<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->foreignId('uso_registrado_por')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete();

            $table->timestamp('uso_registrado_en')
                ->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->dropForeign(['uso_registrado_por']);
            $table->dropColumn([
                'uso_registrado_por',
                'uso_registrado_en',
            ]);
        });
    }
};