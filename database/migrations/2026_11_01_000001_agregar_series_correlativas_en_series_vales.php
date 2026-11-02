<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $preflight = DB::connection()->withoutPretending(function (): array {
            foreach (['series_vales', 'lotes_vales', 'vales_combustible', 'periodos', 'tipos_combustible', 'entregas_policia'] as $tabla) {
                if (!Schema::hasTable($tabla)) {
                    throw new RuntimeException("No se puede crear la numeración: falta la tabla {$tabla}.");
                }
            }

            if (Schema::hasTable('series_correlativas')) {
                throw new RuntimeException('La tabla series_correlativas ya existe; revise su estructura antes de continuar.');
            }

            $maximosHistoricos = [
                'POL-GAS' => (int) (DB::table('vales_combustible')
                    ->where('anio', '<=', 2026)
                    ->whereNotNull('entrega_policia_id')
                    ->max('numero_vale') ?? 0),
                'GAMS-GAS' => 0,
                'GAMS-DIE' => 0,
            ];

            $seriesIniciales = [
                ['POL-GAS', 'POLICIA', 'GASOLINA', 100090],
                ['GAMS-GAS', 'GAMS', 'GASOLINA', 300130],
                ['GAMS-DIE', 'GAMS', 'DIESEL', 400230],
            ];
            $menorCorrelativoGams = min($seriesIniciales[1][3], $seriesIniciales[2][3]);

            $valesGams = DB::table('vales_combustible as vales')
                ->leftJoin('tipos_combustible as tipos', 'tipos.id', '=', 'vales.tipo_combustible_id')
                ->where('vales.anio', '<=', 2026)
                ->whereNull('vales.entrega_policia_id')
                ->get(['vales.numero_vale', 'tipos.nombre as combustible']);

            foreach ($valesGams as $vale) {
                $combustible = mb_strtoupper(trim((string) $vale->combustible));

                if (str_contains($combustible, 'GASOLINA')) {
                    $codigo = 'GAMS-GAS';
                } elseif (str_contains($combustible, 'DIESEL') || str_contains($combustible, 'DIÉSEL')) {
                    $codigo = 'GAMS-DIE';
                } else {
                    if ((int) $vale->numero_vale >= $menorCorrelativoGams) {
                        throw new RuntimeException(
                            "No se puede clasificar un vale GAMS histórico con número {$vale->numero_vale}; podría entrar en conflicto con una serie inicial. Reconcilie ese registro antes de crear series_correlativas."
                        );
                    }

                    continue;
                }

                $maximosHistoricos[$codigo] = max($maximosHistoricos[$codigo], (int) $vale->numero_vale);
            }

            $maximoLote = DB::table('lotes_vales')
                ->join('periodos', 'periodos.id', '=', 'lotes_vales.periodo_id')
                ->where('periodos.anio', '<=', 2026)
                ->max('lotes_vales.numero_final');

            foreach ($seriesIniciales as [$codigo, , , $referencia]) {
                $ultimoHistorico = max(
                    $maximosHistoricos[$codigo],
                    $codigo === 'POL-GAS' ? (int) ($maximoLote ?? 0) : 0
                );

                if ($ultimoHistorico > $referencia) {
                    throw new RuntimeException(
                        "No se puede iniciar {$codigo} en {$referencia}: existe un correlativo histórico o reservado hasta {$ultimoHistorico}. Reconcilie esa serie antes de crear los registros iniciales."
                    );
                }
            }

            $indicesVales = Schema::getIndexes('vales_combustible');

            return compact('seriesIniciales', 'indicesVales');
        });

        $seriesIniciales = $preflight['seriesIniciales'];
        Schema::create('series_correlativas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_serie', 32);
            $table->string('institucion', 32);
            $table->string('combustible', 32);
            $table->unsignedBigInteger('ultimo_numero');
            $table->unsignedSmallInteger('anio');
            $table->timestamps();
            $table->unique(['codigo_serie', 'anio'], 'series_correlativas_codigo_anio_unique');
        });

        Schema::table('vales_combustible', function (Blueprint $table) {
            $table->foreignId('serie_correlativa_id')
                ->nullable()
                ->constrained('series_correlativas')
                ->restrictOnDelete();
            $table->unique(
                ['serie_correlativa_id', 'numero_vale'],
                'vales_correlativa_numero_unique'
            );
        });

        foreach ($preflight['indicesVales'] as $index) {
            if (
                ($index['unique'] ?? false)
                && in_array(
                    $index['columns'] ?? [],
                    [['numero_vale'], ['anio', 'numero_vale']],
                    true
                )
            ) {
                Schema::table('vales_combustible', function (Blueprint $table) use ($index) {
                    $table->dropUnique($index['name']);
                });
            }
        }

        Schema::table('lotes_vales', function (Blueprint $table) {
            $table->foreignId('serie_correlativa_id')
                ->nullable()
                ->constrained('series_correlativas')
                ->restrictOnDelete();
        });

        foreach ($seriesIniciales as [$codigo, $institucion, $combustible, $referencia]) {
            DB::table('series_correlativas')->insert([
                'codigo_serie' => $codigo,
                'institucion' => $institucion,
                'combustible' => $combustible,
                'ultimo_numero' => $referencia,
                'anio' => 2026,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        throw new RuntimeException(
            'La numeración correlativa contiene datos operativos; la reversión requiere un procedimiento manual y no elimina series ni historiales.'
        );
    }
};
