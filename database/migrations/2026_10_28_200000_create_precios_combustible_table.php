<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('precios_combustible', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_combustible_id')
                ->constrained('tipos_combustible')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->decimal('precio_unitario', 12, 4);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('motivo')->nullable();
            $table->foreignId('usuario_id')
                ->nullable()
                ->constrained('usuarios')
                ->cascadeOnUpdate()
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['tipo_combustible_id', 'fecha_inicio', 'fecha_fin'], 'precios_combustible_vigencia_idx');
        });

        DB::statement("CREATE UNIQUE INDEX precios_combustible_unica_vigencia_idx
            ON precios_combustible (tipo_combustible_id, fecha_inicio)
            WHERE fecha_fin IS NULL");
    }

    public function down(): void
    {
        Schema::dropIfExists('precios_combustible');
    }
};
