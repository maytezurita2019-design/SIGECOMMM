<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_contrasena', function (Blueprint $table) {

            $table->id();

            // Usuario que solicita recuperar su contraseña
            $table->foreignId('usuario_id')
                ->constrained('usuarios')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            // Estado de la solicitud:
            // pendiente, atendida o rechazada
            $table->string('estado', 20)
                ->default('pendiente');

            // Fecha en que el administrador atendió la solicitud
            $table->timestamp('fecha_atencion')
                ->nullable();

            // Administrador que atendió la solicitud
            $table->foreignId('atendido_por')
                ->nullable()
                ->constrained('usuarios')
                ->nullOnDelete()
                ->cascadeOnUpdate();

            // IP desde donde se realizó la solicitud
            $table->string('ip_solicitud', 45)
                ->nullable();

            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('solicitudes_contrasena');
    }
};