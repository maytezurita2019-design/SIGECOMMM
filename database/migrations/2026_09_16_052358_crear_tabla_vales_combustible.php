<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vales_combustible', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('numero_vale')->unique();
            $table->date('fecha');

            $table->foreignId('lote_vale_id')
                ->constrained('lotes_vales')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('vehiculo_id')
                ->constrained('vehiculos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('apertura_programatica_id')
                ->constrained('aperturas_programaticas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('estacion_servicio_id')
                ->nullable()
                ->constrained('estaciones_servicio')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('tipo_combustible_id')
                ->nullable()
                ->constrained('tipos_combustible')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('periodo_id')
                ->nullable()
                ->constrained('periodos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('numero_factura', 50)->nullable();

            // Se completan cuando se registra la carga de combustible.
            $table->decimal('cantidad_litros', 12, 4)->nullable();
            $table->decimal('precio_unitario', 12, 4)->nullable();
            $table->decimal('total', 14, 2)->nullable();

            $table->unsignedBigInteger('kilometraje')->nullable();

            $table->text('observacion')->nullable();

            $table->string('estado', 20)->default('EMITIDO');
            $table->text('motivo_anulacion')->nullable();
            $table->timestamp('fecha_anulacion')->nullable();
            $table->string('documento_respaldo')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vales_combustible');
    }
};