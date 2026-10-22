<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lotes_vales', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('numero_inicial');
            $table->unsignedBigInteger('numero_final');
            $table->date('fecha_recepcion');

            $table->foreignId('periodo_id')
                ->constrained('periodos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('tipo_combustible_id')
                ->constrained('tipos_combustible')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('estacion_servicio_id')
                ->constrained('estaciones_servicio')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lotes_vales');
    }
};