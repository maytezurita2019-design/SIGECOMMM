<?php

namespace Tests\Unit;

use App\Models\NumeracionVale;
use PHPUnit\Framework\TestCase;

class NumeracionValeTest extends TestCase
{
    public function test_resuelve_solo_las_tres_series_autorizadas(): void
    {
        $this->assertSame(
            'POL-GAS',
            NumeracionVale::codigoSerie(null, 'POLICIA', 'Gasolina')
        );
        $this->assertSame(
            'GAMS-GAS',
            NumeracionVale::codigoSerie(null, 'GAMS', 'Gasolina')
        );
        $this->assertSame(
            'GAMS-DIE',
            NumeracionVale::codigoSerie(null, 'GAMS', 'Diésel')
        );
    }

    public function test_no_crea_una_serie_policial_para_diesel_ni_combustibles_desconocidos(): void
    {
        $this->assertNull(
            NumeracionVale::codigoSerie(null, 'POLICIA', 'Diésel')
        );
        $this->assertNull(
            NumeracionVale::codigoSerie(null, 'GAMS', 'GLP')
        );
    }

    public function test_formatea_el_numero_segun_su_serie_sin_el_prefijo(): void
    {
        $this->assertSame(
            '000001',
            NumeracionVale::formatearNumero('GAMS-GAS', 2026, 1)
        );
        $this->assertSame(
            '999999',
            NumeracionVale::formatearNumero('POL-GAS', 2026, 999999)
        );
        $this->assertSame(
            '0000001',
            NumeracionVale::formatearNumero('GAMS-DIE', 2026, 1)
        );
        $this->assertSame(
            '0400232',
            NumeracionVale::formatearNumero('GAMS-DIE', 2026, 400232)
        );
    }

    public function test_rechaza_correlativos_fuera_del_limite_de_seis_digitos(): void
    {
        $this->expectException(\RuntimeException::class);

        NumeracionVale::formatearNumero('GAMS-GAS', 2026, 1000000);
    }
}
