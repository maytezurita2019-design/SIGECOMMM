<?php

namespace Tests\Feature;

use App\Models\PrecioCombustible;
use App\Models\TipoCombustible;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PrecioCombustibleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::dropIfExists('precios_combustible');
        Schema::dropIfExists('tipos_combustible');

        Schema::create('tipos_combustible', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique();
            $table->string('unidad_medida', 30)->default('Litros');
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('precios_combustible', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tipo_combustible_id');
            $table->decimal('precio_unitario', 12, 4);
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->string('motivo')->nullable();
            $table->unsignedBigInteger('usuario_id')->nullable();
            $table->timestamps();
        });
    }

    public function test_registra_historial_y_cierra_el_precio_anterior(): void
    {
        $tipo = TipoCombustible::create([
            'nombre' => 'Gasolina',
            'unidad_medida' => 'Litros',
            'estado' => true,
        ]);

        $anterior = PrecioCombustible::registrar([
            'tipo_combustible_id' => $tipo->id,
            'precio_unitario' => 6.90,
            'fecha_inicio' => '2026-01-01',
            'motivo' => 'Ajuste inicial',
        ]);

        $nuevo = PrecioCombustible::registrar([
            'tipo_combustible_id' => $tipo->id,
            'precio_unitario' => 7.00,
            'fecha_inicio' => '2026-04-18',
            'motivo' => 'Aumento por ajuste institucional',
        ]);

        $this->assertSame('2026-04-17', $anterior->fresh()->fecha_fin->format('Y-m-d'));
        $this->assertNull($nuevo->fecha_fin);
        $this->assertSame('7.0000', (string) $nuevo->precio_unitario);
    }

    public function test_no_permite_vigencias_superpuestas_para_un_mismo_combustible(): void
    {
        $tipo = TipoCombustible::create([
            'nombre' => 'Diésel',
            'unidad_medida' => 'Litros',
            'estado' => true,
        ]);

        PrecioCombustible::create([
            'tipo_combustible_id' => $tipo->id,
            'precio_unitario' => 5.60,
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-01-31',
            'motivo' => 'Precio base',
        ]);

        $this->expectException(\Illuminate\Validation\ValidationException::class);

        PrecioCombustible::registrar([
            'tipo_combustible_id' => $tipo->id,
            'precio_unitario' => 5.80,
            'fecha_inicio' => '2026-01-15',
            'motivo' => 'Cambio no permitido',
        ]);
    }
}
