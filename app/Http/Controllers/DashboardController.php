<?php

namespace App\Http\Controllers;

use App\Models\LoteVale;
use App\Models\Usuario;
use App\Models\ValeCombustible;
use App\Models\Vehiculo;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | DATOS GENERALES
        |--------------------------------------------------------------------------
        */

        $vehiculosActivos = Vehiculo::where('estado', true)->count();

        $totalVales = ValeCombustible::count();

        $lotesActivos = LoteVale::where('estado', true)->count();

        $usuariosActivos = Usuario::where('estado', true)->count();


        /*
        |--------------------------------------------------------------------------
        | CONSUMO REAL
        |--------------------------------------------------------------------------
        |
        | GAMS:
        | Solo COMPLETADO.
        |
        | POLICÍA:
        | Solo UTILIZADO.
        |
        | No se incluyen pendientes ni anulados.
        |
        */

        $consumoReal = ValeCombustible::where(function ($q) {

            $q->where(function ($gams) {

                $gams->whereNull('entrega_policia_id')
                    ->where('estado', 'COMPLETADO');

            })->orWhere(function ($policia) {

                $policia->whereNotNull('entrega_policia_id')
                    ->where('estado', 'UTILIZADO');

            });

        });


        /*
        |--------------------------------------------------------------------------
        | TOTALES HISTÓRICOS
        |--------------------------------------------------------------------------
        */

        $totalLitros = (clone $consumoReal)
            ->sum('cantidad_litros');

        $totalConsumido = (clone $consumoReal)
            ->sum('total');


        /*
        |--------------------------------------------------------------------------
        | ESTADOS DE VALES
        |--------------------------------------------------------------------------
        */

        $pendientes = ValeCombustible::whereNull('entrega_policia_id')
            ->where('estado', 'EMITIDO')
            ->count();

        $completados = ValeCombustible::whereNull('entrega_policia_id')
            ->where('estado', 'COMPLETADO')
            ->count();

        $anulados = ValeCombustible::where('estado', 'ANULADO')
            ->count();


        /*
        |--------------------------------------------------------------------------
        | FECHAS
        |--------------------------------------------------------------------------
        */

        $hoy = now();

        $anioActual = $hoy->year;

        $mesActual = $hoy->month;

        $inicioMesActual = $hoy->copy()
            ->startOfMonth();

        $finMesActual = $hoy->copy()
            ->endOfMonth();


        $inicioMesAnterior = $hoy->copy()
            ->subMonthNoOverflow()
            ->startOfMonth();

        $finMesAnterior = $hoy->copy()
            ->subMonthNoOverflow()
            ->endOfMonth();


        /*
        |--------------------------------------------------------------------------
        | CONSUMO DEL MES ACTUAL
        |--------------------------------------------------------------------------
        */

        $litrosMesActual = (clone $consumoReal)
            ->whereBetween('fecha', [
                $inicioMesActual->toDateString(),
                $finMesActual->toDateString(),
            ])
            ->sum('cantidad_litros');


        $totalMesActual = (clone $consumoReal)
            ->whereBetween('fecha', [
                $inicioMesActual->toDateString(),
                $finMesActual->toDateString(),
            ])
            ->sum('total');


        $valesMesActual = (clone $consumoReal)
            ->whereBetween('fecha', [
                $inicioMesActual->toDateString(),
                $finMesActual->toDateString(),
            ])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | CONSUMO DEL MES ANTERIOR
        |--------------------------------------------------------------------------
        */

        $litrosMesAnterior = (clone $consumoReal)
            ->whereBetween('fecha', [
                $inicioMesAnterior->toDateString(),
                $finMesAnterior->toDateString(),
            ])
            ->sum('cantidad_litros');


        $totalMesAnterior = (clone $consumoReal)
            ->whereBetween('fecha', [
                $inicioMesAnterior->toDateString(),
                $finMesAnterior->toDateString(),
            ])
            ->sum('total');


        /*
        |--------------------------------------------------------------------------
        | COMPARACIÓN CON EL MES ANTERIOR
        |--------------------------------------------------------------------------
        */

        if ((float) $litrosMesAnterior > 0) {

            $variacionConsumo = (
                (
                    (float) $litrosMesActual
                    - (float) $litrosMesAnterior
                )
                / (float) $litrosMesAnterior
            ) * 100;

        } elseif ((float) $litrosMesActual > 0) {

            $variacionConsumo = 100;

        } else {

            $variacionConsumo = 0;

        }


        /*
        |--------------------------------------------------------------------------
        | NOMBRES DE MESES
        |--------------------------------------------------------------------------
        */

        $nombresMeses = [
            1  => 'Enero',
            2  => 'Febrero',
            3  => 'Marzo',
            4  => 'Abril',
            5  => 'Mayo',
            6  => 'Junio',
            7  => 'Julio',
            8  => 'Agosto',
            9  => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre',
        ];

        $nombreMesActual = $nombresMeses[$mesActual];

        $nombreMesAnterior =
            $nombresMeses[$inicioMesAnterior->month];


        /*
        |--------------------------------------------------------------------------
        | CONSUMO DIARIO DEL MES
        |--------------------------------------------------------------------------
        |
        | Estos datos servirán para:
        |
        | - calendario
        | - gráfico del mes
        | - detectar días de mayor consumo
        |
        */

        $registrosMes = (clone $consumoReal)
            ->whereBetween('fecha', [
                $inicioMesActual->toDateString(),
                $finMesActual->toDateString(),
            ])
            ->get([
                'fecha',
                'cantidad_litros',
                'total',
            ]);


        $diasDelMes = $hoy->copy()
            ->daysInMonth;


        $consumoDiario = [];

        for ($dia = 1; $dia <= $diasDelMes; $dia++) {

            $consumoDiario[$dia] = [
                'litros' => 0,
                'total' => 0,
                'vales' => 0,
            ];

        }


        foreach ($registrosMes as $registro) {

            if (!$registro->fecha) {
                continue;
            }

            $dia = $registro->fecha->day;

            $consumoDiario[$dia]['litros'] +=
                (float) ($registro->cantidad_litros ?? 0);

            $consumoDiario[$dia]['total'] +=
                (float) ($registro->total ?? 0);

            $consumoDiario[$dia]['vales']++;

        }


        /*
        |--------------------------------------------------------------------------
        | DÍA DE MAYOR CONSUMO
        |--------------------------------------------------------------------------
        */

        $diaMayorConsumo = null;

        $mayorConsumo = 0;


        foreach ($consumoDiario as $dia => $datos) {

            if ($datos['litros'] > $mayorConsumo) {

                $mayorConsumo = $datos['litros'];

                $diaMayorConsumo = $dia;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | CONSUMO ANUAL
        |--------------------------------------------------------------------------
        */

        $consumosAnio = (clone $consumoReal)
            ->whereYear('fecha', $anioActual)
            ->get([
                'fecha',
                'cantidad_litros',
            ]);


        $meses = [
            'Ene',
            'Feb',
            'Mar',
            'Abr',
            'May',
            'Jun',
            'Jul',
            'Ago',
            'Sep',
            'Oct',
            'Nov',
            'Dic',
        ];


        $consumoMensual = array_fill(
            0,
            12,
            0.0
        );


        foreach ($consumosAnio as $registro) {

            if ($registro->fecha) {

                $consumoMensual[
                    $registro->fecha->month - 1
                ] += (float) (
                    $registro->cantidad_litros ?? 0
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | ÚLTIMOS VALES
        |--------------------------------------------------------------------------
        */

        $ultimosVales = ValeCombustible::with([
                'vehiculo.unidad'
            ])
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->limit(6)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | LOTE ACTUAL
        |--------------------------------------------------------------------------
        */

        $loteActual = LoteVale::with('periodo')
            ->where('estado', true)
            ->whereHas('periodo', function ($q) {

                $q->where('estado', true);

            })
            ->orderByDesc('periodo_id')
            ->orderBy('numero_inicial')
            ->get()
            ->first(function ($lote) {

                return $lote->siguiente_numero !== null;

            });


        /*
        |--------------------------------------------------------------------------
        | VISTA
        |--------------------------------------------------------------------------
        */

        return view('dashboard.index', compact(

            'vehiculosActivos',
            'totalVales',
            'totalLitros',
            'totalConsumido',
            'lotesActivos',
            'usuariosActivos',

            'pendientes',
            'completados',
            'anulados',

            'ultimosVales',

            'anioActual',
            'mesActual',

            'meses',
            'consumoMensual',

            'loteActual',

            'nombreMesActual',
            'nombreMesAnterior',

            'litrosMesActual',
            'litrosMesAnterior',

            'totalMesActual',
            'totalMesAnterior',

            'valesMesActual',

            'variacionConsumo',

            'consumoDiario',

            'diaMayorConsumo',
            'mayorConsumo'

        ));
    }
}