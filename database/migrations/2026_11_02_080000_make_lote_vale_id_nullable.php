<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columna = DB::connection()->withoutPretending(function (): ?array {
            if (!Schema::hasTable('vales_combustible') || !Schema::hasColumn('vales_combustible', 'lote_vale_id')) {
                return null;
            }

            return collect(Schema::getColumns('vales_combustible'))
                ->firstWhere('name', 'lote_vale_id');
        });

        if ($columna === null) {
            throw new RuntimeException('No existe vales_combustible.lote_vale_id; no se modificó el esquema.');
        }

        if (!$columna['nullable']) {
            Schema::table('vales_combustible', function (Blueprint $table): void {
                $table->foreignId('lote_vale_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        throw new RuntimeException('No se puede volver obligatorio lote_vale_id: los vales GAMS se emiten sin lote.');
    }
};
