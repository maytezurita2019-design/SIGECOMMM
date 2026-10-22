<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use RuntimeException;

class NumeracionVale
{
    public static function bloquear(int $anio): object
    {
        $serie = DB::table('series_vales')
            ->where('estado', true)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        if (!$serie) {
            throw new RuntimeException(
                'No existe una serie de vales activa.'
            );
        }

        return $serie;
    }

    public static function siguiente(int $anio): int
    {
        $ultimoNumero = DB::table('series_vales')
            ->where('estado', true)
            ->orderByDesc('id')
            ->value('ultimo_numero');

        if ($ultimoNumero === null) {
            throw new RuntimeException(
                'No existe una serie de vales activa.'
            );
        }

        return ((int) $ultimoNumero) + 1;
    }
}
