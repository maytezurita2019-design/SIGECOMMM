<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes_vales', function (Blueprint $table) {
            $table->dropForeign(['tipo_combustible_id']);
            $table->dropForeign(['estacion_servicio_id']);

            $table->dropColumn([
                'tipo_combustible_id',
                'estacion_servicio_id',
            ]);
        });
    }

    public function down(): void
    {
        Schema::table('lotes_vales', function (Blueprint $table) {
            $table->foreignId('tipo_combustible_id')
                ->nullable()
                ->constrained('tipos_combustible')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('estacion_servicio_id')
                ->nullable()
                ->constrained('estaciones_servicio')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });
    }
};