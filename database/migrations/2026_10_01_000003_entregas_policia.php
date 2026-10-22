<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('entregas_policia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lote_vale_id')->constrained('lotes_vales')->restrictOnDelete();
            $table->string('contrato', 150);
            $table->string('dependencia', 150);
            $table->string('recibido_por', 150);
            $table->date('fecha_entrega');
            $table->unsignedInteger('cantidad');
            $table->string('numero_inicial', 6);
            $table->string('numero_final', 6);
            $table->uuid('solicitud_token')->unique();
            $table->foreignId('usuario_id')->constrained('usuarios')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->unsignedBigInteger('vehiculo_id')->nullable()->change();
            $table->date('fecha')->nullable()->change();
            $table->foreignId('entrega_policia_id')->nullable()->constrained('entregas_policia')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Las entregas y sus números reservados deben conservarse.');
    }
};
