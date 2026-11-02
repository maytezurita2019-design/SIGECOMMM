<?php

namespace Tests\Feature;

use App\Models\EstacionServicio;
use App\Models\LugarEntrega;
use App\Models\NumeracionVale;
use App\Models\SerieCorrelativa;
use App\Models\TipoCombustible;
use App\Models\ValeCombustible;
use App\Models\Vehiculo;
use Tests\TestCase;

class ImprimirValeCombustibleTest extends TestCase
{
    public function test_issued_diesel_print_keeps_empty_quantity_and_assignment_blank(): void
    {
        $html = $this->renderVale([
            'estado' => 'EMITIDO',
            'cantidad_litros' => null,
            'detalle_asignacion' => null,
            'observacion' => 'No usar como detalle',
        ], 'DIESEL OIL');

        $this->assertStringContainsString('VALE DE COMBUSTIBLE (DIESEL OIL)', $html);
        $this->assertStringContainsString('data-print-format="gams-diesel"', $html);
        $this->assertMatchesRegularExpression('/<main class="vale vale-diesel" data-print-format="gams-diesel">/', $html);
        $this->assertStringContainsString('N.º 0000453', $html);
        $this->assertStringContainsString('images/logo-sacaba.jpg', $html);
        $this->assertStringContainsString('#08712f', $html);
        $this->assertStringContainsString('@page diesel-gams', $html);
        $this->assertStringContainsString('size: letter portrait;', $html);
        $this->assertStringContainsString('width: 215.9mm;', $html);
        $this->assertStringContainsString('height: 139.7mm;', $html);
        $this->assertStringContainsString('Lugar de entrega:', $html);
        $this->assertStringContainsString(
            '<span class="diesel-linea"></span>',
            $html
        );
        $this->assertStringContainsString('Kilometraje/Horómetro:', $html);
        $this->assertStringNotContainsString('No usar como detalle', $html);
        $this->assertStringNotContainsString('P/Unit. Bs/Lts:', $html);
        $this->assertStringNotContainsString('Costo total:', $html);
        $this->assertStringNotContainsString('Dependencia:', $html);
        $this->assertStringNotContainsString('Contrato:', $html);
        $this->assertStringNotContainsString('PROVEEDOR', $html);
        $this->assertSame(3, substr_count($html, 'class="firma"'));

        $posiciones = array_map(
            fn (string $campo) => strpos($html, $campo),
            [
                'Lugar de entrega:',
                'Cantidad:',
                'Detalle:',
                'Nro. de Placa:',
                'Código de vehículo:',
                'Kilometraje/Horómetro:',
                'Sacaba:',
            ]
        );
        $this->assertNotContains(false, $posiciones);
        $ordenados = $posiciones;
        sort($ordenados);
        $this->assertSame($ordenados, $posiciones);
    }

    public function test_completed_diesel_print_shows_real_liters_and_assignment_detail(): void
    {
        $html = $this->renderVale([
            'estado' => 'COMPLETADO',
            'cantidad_litros' => '125.5',
            'detalle_asignacion' => 'MANTENIMIENTO DE EQUIPO MUNICIPAL',
        ], 'DIESEL OIL');

        $this->assertStringContainsString('125.5000000000000', $html);
        $this->assertStringContainsString('MANTENIMIENTO DE EQUIPO MUNICIPAL', $html);
        $this->assertStringContainsString('TEC. I ADMINISTRACIÓN DE', $html);
        $this->assertStringContainsString('DIRECTOR ADMINISTRATIVO', $html);
    }

    public function test_diesel_print_shows_the_assigned_delivery_place(): void
    {
        $vale = $this->buildVale([
            'estado' => 'EMITIDO',
            'lugar_entrega_id' => 6,
        ], 'DIESEL OIL');
        $vale->setRelation(
            'estacionServicio',
            (new EstacionServicio())->forceFill(['nombre' => 'YPFB'])
        );
        $vale->setRelation(
            'lugarEntrega',
            (new LugarEntrega())->forceFill(['nombre' => 'Almacén central'])
        );

        $html = view('vales_combustible.imprimir', [
            'valeCombustible' => $vale,
            'numeroValeFormateado' => $this->numeroValeFormateado(
                'GAMS-DIE',
                (int) $vale->numero_vale
            ),
        ])->render();

        $this->assertStringContainsString('Lugar de entrega:</span>', $html);
        $this->assertStringContainsString('>Almacén central</span>', $html);
        $this->assertStringNotContainsString('Estación de servicio:', $html);
        $this->assertStringNotContainsString('YPFB', $html);
    }

    public function test_historical_diesel_print_leaves_unassigned_place_blank(): void
    {
        $html = $this->renderVale(['estado' => 'EMITIDO'], 'DIESEL');

        $this->assertStringContainsString(
            '<span class="diesel-etiqueta">Lugar de entrega:</span>',
            $html
        );
        $this->assertStringContainsString('<span class="diesel-linea"></span>', $html);
    }

    public function test_gasoline_and_police_diesel_keep_the_existing_print_layout(): void
    {
        $gasolina = $this->renderVale([
            'estado' => 'EMITIDO',
        ], 'GASOLINA');
        $policiaDiesel = $this->renderVale([
            'estado' => 'UTILIZADO',
            'entrega_policia_id' => 17,
        ], 'DIESEL OIL');
        $diesel = $this->renderVale([
            'estado' => 'EMITIDO',
        ], 'DIESEL');
        $dieselAcentuado = $this->renderVale([
            'estado' => 'EMITIDO',
        ], 'DIÉSEL');

        $this->assertMatchesRegularExpression('/VALE DE COMBUSTIBLE\s*\(GASOLINA\)/', $gasolina);
        $this->assertStringContainsString('data-print-format="standard"', $gasolina);
        $this->assertMatchesRegularExpression('/<main class="vale\s*" data-print-format="standard">/', $gasolina);
        $this->assertStringContainsString('P/Unit. Bs/Lts:', $gasolina);
        $this->assertStringContainsString('Estación Municipal', $gasolina);
        $this->assertStringContainsString('Entregué conforme', $gasolina);
        $this->assertMatchesRegularExpression('/VALE DE COMBUSTIBLE\s*\(DIESEL OIL\)/', $policiaDiesel);
        $this->assertStringContainsString('data-print-format="standard"', $policiaDiesel);
        $this->assertMatchesRegularExpression('/<main class="vale\s*" data-print-format="standard">/', $policiaDiesel);
        $this->assertStringContainsString('P/Unit. Bs/Lts:', $policiaDiesel);
        $this->assertStringContainsString('PROVEEDOR', $policiaDiesel);
        $this->assertMatchesRegularExpression('/VALE DE COMBUSTIBLE\s*\(DIESEL OIL\)/', $diesel);
        $this->assertStringContainsString('data-print-format="gams-diesel"', $diesel);
        $this->assertMatchesRegularExpression('/<main class="vale vale-diesel" data-print-format="gams-diesel">/', $diesel);
        $this->assertStringContainsString('data-print-format="gams-diesel"', $dieselAcentuado);
    }

    public function test_printed_number_uses_width_of_the_associated_correlative_series(): void
    {
        $diesel = $this->renderVale([
            'numero_vale' => 400232,
        ], 'DIESEL', 'GAMS-DIE');
        $gasolina = $this->renderVale([
            'numero_vale' => 400232,
        ], 'GASOLINA', 'GAMS-GAS');
        $policia = $this->renderVale([
            'numero_vale' => 400232,
            'entrega_policia_id' => 17,
        ], 'GASOLINA', 'POL-GAS');

        $this->assertStringContainsString('N.º 0400232', $diesel);
        $this->assertStringContainsString('N.º 400232', $gasolina);
        $this->assertStringContainsString('N.º 400232', $policia);
    }

    private function renderVale(array $attributes, string $tipoCombustible, ?string $codigoSerie = null): string
    {
        $vale = $this->buildVale($attributes, $tipoCombustible);
        $codigoSerie ??= $vale->entrega_policia_id
            ? 'POL-GAS'
            : NumeracionVale::codigoSerie(null, 'GAMS', $tipoCombustible);
        $vale->setAttribute('anio', 2026);
        $vale->setRelation(
            'serieCorrelativa',
            (new SerieCorrelativa())->forceFill([
                'codigo_serie' => $codigoSerie,
                'anio' => 2026,
            ])
        );

        return view('vales_combustible.imprimir', [
            'valeCombustible' => $vale,
            'numeroValeFormateado' => $this->numeroValeFormateado(
                $codigoSerie,
                (int) $vale->numero_vale
            ),
        ])->render();
    }

    private function numeroValeFormateado(string $codigoSerie, int $numero): string
    {
        $numeroCompleto = NumeracionVale::formatear($codigoSerie, 2026, $numero);

        return substr($numeroCompleto, strrpos($numeroCompleto, '-') + 1);
    }

    private function buildVale(array $attributes, string $tipoCombustible): ValeCombustible
    {
        $vale = new ValeCombustible();
        $vale->forceFill(array_merge([
            'id' => 453,
            'numero_vale' => 453,
            'fecha' => '2026-10-30',
            'created_at' => now(),
            'entrega_policia_id' => null,
        ], $attributes));

        $vale->setRelation(
            'tipoCombustible',
            (new TipoCombustible())->forceFill(['nombre' => $tipoCombustible])
        );
        $vale->setRelation(
            'estacionServicio',
            (new EstacionServicio())->forceFill(['nombre' => 'Estación Municipal'])
        );
        $vale->setRelation(
            'vehiculo',
            (new Vehiculo())->forceFill([
                'placa' => 'ABC-123',
                'codigo' => 'AP-01',
            ])
        );

        return $vale;
    }
}
