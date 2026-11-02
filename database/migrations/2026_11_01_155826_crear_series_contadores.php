<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('series_correlativas')) {
            throw new RuntimeException('Primero debe existir series_correlativas. No se modificó ningún contador.');
        }
        if (Schema::hasTable('series_contadores')) {
            throw new RuntimeException('series_contadores ya existe. Revise la tabla antes de migrar.');
        }

        $codigos = ['POL-GAS', 'GAMS-GAS', 'GAMS-DIE'];
        $series = DB::table('series_correlativas')
            ->whereIn('codigo_serie', $codigos)
            ->orderBy('anio')
            ->get();

        if ($series->isEmpty()) {
            throw new RuntimeException('No se encontraron las tres series existentes.');
        }

        // No inventar ni reiniciar contadores: exigir las tres series para 2026.
        foreach ($codigos as $codigo) {
            if ($series->where('codigo_serie', $codigo)->where('anio', 2026)->count() !== 1) {
                throw new RuntimeException("Se requiere exactamente una serie {$codigo} de 2026.");
            }
        }

        foreach ($series as $serie) {
            $limite = $serie->codigo_serie === 'GAMS-DIE' ? 9999999 : 999999;
            if ((int) $serie->ultimo_numero < 0 || (int) $serie->ultimo_numero > $limite) {
                throw new RuntimeException("El contador de {$serie->codigo_serie} ({$serie->anio}) excede su longitud.");
            }
        }

        Schema::create('series_contadores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_serie', 16);
            $table->unsignedSmallInteger('anio');
            $table->unsignedBigInteger('ultimo_numero');
            $table->string('prefijo', 32);
            $table->timestamps();
            $table->unique(['codigo_serie', 'anio'], 'series_contadores_codigo_anio_unique');
        });

        foreach ($series as $serie) {
            DB::table('series_contadores')->insert([
                'codigo_serie' => $serie->codigo_serie,
                'anio' => $serie->anio,
                'ultimo_numero' => $serie->ultimo_numero,
                'prefijo' => $serie->codigo_serie . '-' . $serie->anio,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Solo elimina la tabla auxiliar creada por esta migración.
        Schema::dropIfExists('series_contadores');
    }
};

