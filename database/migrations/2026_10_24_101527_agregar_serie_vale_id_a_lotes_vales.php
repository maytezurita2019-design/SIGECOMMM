<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql') {
            Schema::table('lotes_vales', function (Blueprint $table) {
                $table->unsignedBigInteger('serie_vale_id')->nullable()->after('id');
            });

            $serieActiva = DB::table('series_vales')
                ->where('estado', true)
                ->orderByDesc('id')
                ->first();

            if ($serieActiva) {
                DB::table('lotes_vales')
                    ->whereNull('serie_vale_id')
                    ->update([
                        'serie_vale_id' => $serieActiva->id,
                    ]);
            }

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | AGREGAR SERIE A LOS LOTES
        |--------------------------------------------------------------------------
        */

        Schema::table('lotes_vales', function (Blueprint $table) {
            $table->foreignId('serie_vale_id')
                ->nullable()
                ->after('id')
                ->constrained('series_vales')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        /*
        |--------------------------------------------------------------------------
        | ASIGNAR SERIE ACTIVA A LOS LOTES EXISTENTES
        |--------------------------------------------------------------------------
        |
        | Los lotes actuales pertenecen a la serie que actualmente está activa.
        |
        */

        $serieActiva = DB::table('series_vales')
            ->where('estado', true)
            ->orderByDesc('id')
            ->first();

        if (!$serieActiva) {
            throw new RuntimeException(
                'No existe una serie de vales activa para asociar los lotes existentes.'
            );
        }

        DB::table('lotes_vales')
            ->whereNull('serie_vale_id')
            ->update([
                'serie_vale_id' => $serieActiva->id,
            ]);

        /*
        |--------------------------------------------------------------------------
        | HACER OBLIGATORIA LA SERIE
        |--------------------------------------------------------------------------
        */

        DB::statement(
            'ALTER TABLE lotes_vales
             ALTER COLUMN serie_vale_id SET NOT NULL'
        );
    }

    public function down(): void
    {
        Schema::table('lotes_vales', function (Blueprint $table) {
            $table->dropForeign(['serie_vale_id']);
            $table->dropColumn('serie_vale_id');
        });
    }
};
