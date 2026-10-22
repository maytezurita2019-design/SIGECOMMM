<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aperturas_programaticas', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 50);
            $table->string('descripcion', 255)->nullable();;

            $table->foreignId('fuente_financiamiento_id')
                ->constrained('fuentes_financiamiento')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreignId('organismo_financiador_id')
                ->constrained('organismos_financiadores')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->boolean('estado')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aperturas_programaticas');
    }
};