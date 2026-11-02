<?php

namespace Tests\Unit;

use App\Models\ParteDiario;
use PHPUnit\Framework\TestCase;

class ParteDiarioTest extends TestCase
{
    public function test_it_calculates_kilometers_even_when_odometer_status_is_unset(): void
    {
        $parte = new ParteDiario([
            'presentado' => true,
            'km_inicial' => 345,
            'km_final' => 456,
        ]);

        $this->assertSame(111, $parte->km_recorridos);
    }

    public function test_it_preserves_a_zero_distance(): void
    {
        $parte = new ParteDiario([
            'presentado' => true,
            'km_inicial' => 100,
            'km_final' => 100,
        ]);

        $this->assertSame(0, $parte->km_recorridos);
    }

    public function test_it_returns_null_when_either_odometer_reading_is_missing(): void
    {
        $sinInicial = new ParteDiario([
            'km_final' => 456,
        ]);
        $sinFinal = new ParteDiario([
            'km_inicial' => 345,
        ]);

        $this->assertNull($sinInicial->km_recorridos);
        $this->assertNull($sinFinal->km_recorridos);
    }

    public function test_it_does_not_calculate_for_unpresented_parts_or_damaged_odometer(): void
    {
        $noPresentado = new ParteDiario([
            'presentado' => false,
            'km_inicial' => 345,
            'km_final' => 456,
        ]);
        $odometroDanado = new ParteDiario([
            'presentado' => true,
            'odometro_funciona' => false,
            'km_inicial' => 345,
            'km_final' => 456,
        ]);

        $this->assertNull($noPresentado->km_recorridos);
        $this->assertNull($odometroDanado->km_recorridos);
    }

    public function test_it_returns_null_when_final_reading_is_lower_than_initial(): void
    {
        $parte = new ParteDiario([
            'km_inicial' => 456,
            'km_final' => 345,
        ]);

        $this->assertNull($parte->km_recorridos);
    }
}
