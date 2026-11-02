<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('vehiculos', 'detalle_asignacion_predeterminado')) {
            Schema::table('vehiculos', function (Blueprint $table) {
                $table->text('detalle_asignacion_predeterminado')->nullable();
            });
        }

        if (!Schema::hasColumn('vales_combustible', 'detalle_asignacion')) {
            Schema::table('vales_combustible', function (Blueprint $table) {
                $table->text('detalle_asignacion')->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('vales_combustible', 'detalle_asignacion')) {
            Schema::table('vales_combustible', function (Blueprint $table) {
                $table->dropColumn('detalle_asignacion');
            });
        }

        if (Schema::hasColumn('vehiculos', 'detalle_asignacion_predeterminado')) {
            Schema::table('vehiculos', function (Blueprint $table) {
                $table->dropColumn('detalle_asignacion_predeterminado');
            });
        }
    }
};
