<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class NumeracionVale
{
    public static function codigoSerie(
        ?string $codigoSerie = null,
        ?string $institucion = null,
        ?string $combustible = null
    ): ?string {
        if ($codigoSerie !== null && trim($codigoSerie) !== '') {
            $codigo = strtoupper(trim($codigoSerie));
            return in_array($codigo, ['POL-GAS', 'GAMS-GAS', 'GAMS-DIE'], true)
                ? $codigo : null;
        }

        $institucion = mb_strtoupper(trim((string) $institucion));
        $combustible = mb_strtoupper(trim((string) $combustible));
        $esGasolina = str_contains($combustible, 'GASOLINA');
        $esDiesel = str_contains($combustible, 'DIESEL') || str_contains($combustible, 'DIÉSEL');

        if (in_array($institucion, ['POLICIA', 'POL'], true)) {
            return $esGasolina ? 'POL-GAS' : null;
        }
        if ($institucion === 'GAMS') {
            return $esGasolina ? 'GAMS-GAS' : ($esDiesel ? 'GAMS-DIE' : null);
        }
        return null;
    }

    public static function bloquear(
        ?int $anio = null,
        ?string $codigoSerie = null,
        ?string $institucion = null,
        ?string $combustible = null
    ): object {
        $anio ??= (int) now()->year;
        $codigo = static::codigoSerie($codigoSerie, $institucion, $combustible);
        if ($codigo === null) {
            throw new RuntimeException('Serie o combinación institución/combustible no permitida.');
        }

        // IMPORTANTE: la reserva y avanzar() deben ejecutarse en la misma
        // DB::transaction() que guarda el vale/lote. El bloqueo no sobrevive
        // al final de una transacción.
        if (DB::transactionLevel() < 1) {
            throw new RuntimeException('La emisión debe ejecutarse dentro de DB::transaction().');
        }

        // Orden de bloqueo consistente para prevenir carreras y deadlocks.
        $contador = DB::table('series_contadores')
            ->where('codigo_serie', $codigo)
            ->where('anio', $anio)
            ->lockForUpdate()
            ->first();

        if (!$contador) {
            throw new RuntimeException(
                "La serie {$codigo} para {$anio} no está configurada. " .
                'Un usuario autorizado debe decidir si continúa o reinicia la numeración.'
            );
        }

        $serie = DB::table('series_correlativas')
            ->where('codigo_serie', $codigo)
            ->where('anio', $anio)
            ->lockForUpdate()
            ->first();

        if (!$serie) {
            throw new RuntimeException("Falta la serie histórica {$codigo} de {$anio}; no se emitió ningún vale.");
        }
        if (($serie->estado ?? 'ACTIVA') !== 'ACTIVA') {
            throw new RuntimeException(
                "La serie {$codigo} de {$anio} está EN_CONFIGURACION y no permite emisión oficial."
            );
        }
        if ((int) $serie->ultimo_numero !== (int) $contador->ultimo_numero) {
            throw new RuntimeException("Contadores desincronizados para {$codigo} de {$anio}; emisión bloqueada.");
        }
        static::validarLimite($codigo, (int) $contador->ultimo_numero);

        // Conservar el id histórico que usan las FK de lotes y vales.
        $serie->ultimo_numero = (int) $contador->ultimo_numero;
        return $serie;
    }

    public static function siguiente(
        ?int $anio = null,
        ?string $codigoSerie = null,
        ?string $institucion = null,
        ?string $combustible = null
    ): int {
        // Lectura de vista previa, NO reserva un número.
        $anio ??= (int) now()->year;
        $codigo = static::codigoSerie($codigoSerie, $institucion, $combustible);
        if ($codigo === null) {
            throw new RuntimeException('Serie no permitida.');
        }
        $contador = DB::table('series_contadores')
            ->where('codigo_serie', $codigo)->where('anio', $anio)->first();
        if (!$contador) {
            throw new RuntimeException("La serie {$codigo} de {$anio} requiere configuración.");
        }
        $serie = DB::table('series_correlativas')
            ->where('codigo_serie', $codigo)
            ->where('anio', $anio)
            ->first();
        if (!$serie) {
            throw new RuntimeException("Falta la serie histórica {$codigo} de {$anio}.");
        }
        if ((int) $serie->ultimo_numero !== (int) $contador->ultimo_numero) {
            throw new RuntimeException("Contadores desincronizados para {$codigo} de {$anio}.");
        }
        $siguiente = (int) $contador->ultimo_numero + 1;
        static::validarLimite($codigo, $siguiente);
        return $siguiente;
    }

    public static function estado(string $codigoSerie, int $anio): ?string
    {
        $codigo = static::codigoSerie($codigoSerie);
        if ($codigo === null) {
            throw new RuntimeException('Serie no permitida.');
        }

        $estado = DB::table('series_correlativas')
            ->where('codigo_serie', $codigo)
            ->where('anio', $anio)
            ->value('estado');

        return $estado === null ? null : (string) $estado;
    }

    public static function avanzar(object $serie, int $ultimoNumero): void
    {
        if (DB::transactionLevel() < 1) {
            throw new RuntimeException('El avance del correlativo requiere una transacción activa.');
        }
        $codigo = (string) $serie->codigo_serie;
        $anio = (int) $serie->anio;
        static::validarLimite($codigo, $ultimoNumero);

        $contador = DB::table('series_contadores')
            ->where('codigo_serie', $codigo)->where('anio', $anio)
            ->lockForUpdate()->first();
        if (!$contador) {
            throw new RuntimeException('No existe el contador de la serie seleccionada.');
        }
        $actual = (int) $contador->ultimo_numero;
        $historico = DB::table('series_correlativas')
            ->where('id', $serie->id)->lockForUpdate()->first();
        if (!$historico || (int) $historico->ultimo_numero !== $actual) {
            throw new RuntimeException('Los contadores no coinciden. No se actualizaron.');
        }
        if (($historico->estado ?? 'ACTIVA') !== 'ACTIVA') {
            throw new RuntimeException('Solo se puede avanzar una serie ACTIVA mediante una operación autorizada.');
        }
        if ($ultimoNumero < $actual) {
            // No retroceder al editar lotes antiguos.
            return;
        }
        if ($ultimoNumero === $actual) {
            return;
        }
        DB::table('series_contadores')->where('id', $contador->id)->update([
            'ultimo_numero' => $ultimoNumero, 'updated_at' => now(),
        ]);
        DB::table('series_correlativas')->where('id', $serie->id)->update([
            'ultimo_numero' => $ultimoNumero, 'updated_at' => now(),
        ]);
    }

    public static function formatear(string $codigoSerie, int $anio, int $numero): string
    {
        $codigoSerie = strtoupper(trim($codigoSerie));
        if ($anio < 2000 || $anio > 9999) {
            throw new RuntimeException('Gestión no válida.');
        }
        static::validarLimite($codigoSerie, $numero);
        $digitos = $codigoSerie === 'GAMS-DIE' ? 7 : 6;
        return sprintf('%s-%d-%s', $codigoSerie, $anio,
            str_pad((string) $numero, $digitos, '0', STR_PAD_LEFT));
    }

    public static function formatearNumero(string $codigoSerie, int $anio, int $numero): string
    {
        $formateado = static::formatear($codigoSerie, $anio, $numero);

        return substr($formateado, strrpos($formateado, '-') + 1);
    }

    private static function validarLimite(string $codigo, int $numero): void
    {
        if (!in_array($codigo, ['POL-GAS', 'GAMS-GAS', 'GAMS-DIE'], true)) {
            throw new RuntimeException('Serie correlativa no permitida.');
        }
        $limite = $codigo === 'GAMS-DIE' ? 9999999 : 999999;
        if ($numero < 0 || $numero > $limite) {
            throw new RuntimeException("El correlativo de {$codigo} excede su longitud permitida.");
        }
    }
}
