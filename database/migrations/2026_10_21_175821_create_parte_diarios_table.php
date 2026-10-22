<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partes_diarios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vehiculo_id')
                ->constrained('vehiculos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->date('fecha');

            $table->boolean('presentado')->default(false);

            $table->text('observacion')->nullable();

            $table->timestamps();

            // Evita registrar dos veces el mismo vehículo
            // en la misma fecha.
            $table->unique(
                ['vehiculo_id', 'fecha'],
                'partes_diarios_vehiculo_fecha_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partes_diarios');
    }
};