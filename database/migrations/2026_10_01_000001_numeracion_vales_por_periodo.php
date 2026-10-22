<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lotes_vales', function (Blueprint $table) {
            $table->unsignedBigInteger('tipo_combustible_id')->nullable()->change();
            $table->unsignedBigInteger('estacion_servicio_id')->nullable()->change();
        });
        // Completar referencias históricas antes de cambiar la unicidad.
        DB::table('vales_combustible')->orderBy('id')->chunkById(200, function ($vales) {
            foreach ($vales as $vale) {
                $lote = DB::table('lotes_vales')->find($vale->lote_vale_id);
                DB::table('vales_combustible')->where('id', $vale->id)->update([
                    'periodo_id' => $lote->periodo_id,
                    'tipo_combustible_id' => $vale->tipo_combustible_id ?? $lote->tipo_combustible_id,
                    'estacion_servicio_id' => $vale->estacion_servicio_id ?? $lote->estacion_servicio_id,
                ]);
            }
        });
        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->dropUnique(['numero_vale']);
            $table->unsignedBigInteger('periodo_id')->nullable(false)->change();
            $table->unique(['periodo_id', 'numero_vale']);
        });
    }

    public function down(): void
    {
        // Impedir una reversión que descartaría números o asociaciones válidas.
        throw new RuntimeException('Esta migración requiere reconciliar los números por período antes de revertirla.');
    }
};
