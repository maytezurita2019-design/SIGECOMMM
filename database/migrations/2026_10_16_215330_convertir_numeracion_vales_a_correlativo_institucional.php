<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Crear las series de numeración
        |--------------------------------------------------------------------------
        |
        | Una serie representa una etapa del correlativo institucional.
        | El cambio de año NO crea una nueva serie.
        |
        */

        Schema::create('series_vales', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 100);

            $table->unsignedBigInteger('numero_inicial')
                ->default(1);

            $table->unsignedBigInteger('ultimo_numero')
                ->default(0);

            $table->boolean('estado')
                ->default(true);

            $table->text('motivo')->nullable();

            $table->timestamps();
        });

        /*
        |--------------------------------------------------------------------------
        | 2. Obtener el mayor número histórico
        |--------------------------------------------------------------------------
        */

     $ultimoVale = (int) (
    DB::table('vales_combustible')
        ->selectRaw('MAX(CAST(numero_vale AS BIGINT)) as ultimo')
        ->value('ultimo') ?? 0
);

$ultimoPolicia = (int) (
    DB::table('entregas_policia')
        ->selectRaw('MAX(CAST(numero_final AS BIGINT)) as ultimo')
        ->value('ultimo') ?? 0
);

$ultimoNumero = max(
    $ultimoVale,
    $ultimoPolicia
);

        /*
        |--------------------------------------------------------------------------
        | 3. Crear la primera serie institucional
        |--------------------------------------------------------------------------
        */

        $serieId = DB::table('series_vales')->insertGetId([
            'nombre' => 'Serie inicial',
            'numero_inicial' => 1,
            'ultimo_numero' => $ultimoNumero,
            'estado' => true,
            'motivo' => 'Serie creada a partir de la numeración existente.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | 4. Preparar vales_combustible
        |--------------------------------------------------------------------------
        */

        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->foreignId('serie_vale_id')
                ->nullable()
                ->after('id')
                ->constrained('series_vales')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        DB::table('vales_combustible')
            ->update([
                'serie_vale_id' => $serieId,
            ]);

        /*
        |--------------------------------------------------------------------------
        | 5. Eliminar la restricción única anterior año + número
        |--------------------------------------------------------------------------
        */

        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->dropUnique(['anio', 'numero_vale']);
        });

        /*
        |--------------------------------------------------------------------------
        | 6. Convertir numero_vale a BIGINT
        |--------------------------------------------------------------------------
        */

        DB::statement(
            'ALTER TABLE vales_combustible
             ALTER COLUMN numero_vale
             TYPE BIGINT
             USING numero_vale::BIGINT'
        );

        /*
        |--------------------------------------------------------------------------
        | 7. Nueva unicidad: serie + número
        |--------------------------------------------------------------------------
        */

        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->unique(
                ['serie_vale_id', 'numero_vale'],
                'vales_serie_numero_unique'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | 8. Entregas a Policía sin límite de 6 dígitos
        |--------------------------------------------------------------------------
        */

        DB::statement(
            'ALTER TABLE entregas_policia
             ALTER COLUMN numero_inicial
             TYPE BIGINT
             USING numero_inicial::BIGINT'
        );

        DB::statement(
            'ALTER TABLE entregas_policia
             ALTER COLUMN numero_final
             TYPE BIGINT
             USING numero_final::BIGINT'
        );

        /*
        |--------------------------------------------------------------------------
        | 9. La tabla de numeración anual deja de utilizarse
        |--------------------------------------------------------------------------
        */

        Schema::dropIfExists('numeraciones_vales');
    }

    public function down(): void
    {
        throw new RuntimeException(
            'Esta migración modifica la numeración institucional y requiere reversión manual.'
        );
    }
};