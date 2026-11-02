<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParteDiario extends Model
{
    use HasFactory;

    protected $table = 'partes_diarios';

    protected $fillable = [
        'vehiculo_id',
        'fecha',
        'presentado',
        'odometro_funciona',
        'km_inicial',
        'km_final',
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'presentado' => 'boolean',
        'odometro_funciona' => 'boolean',
        'km_inicial' => 'integer',
        'km_final' => 'integer',
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULAR KILÓMETROS RECORRIDOS
    |--------------------------------------------------------------------------
    */

    public function getKmRecorridosAttribute(): ?int
    {
        if (
            $this->presentado === false ||
            $this->odometro_funciona === false ||
            $this->km_inicial === null ||
            $this->km_final === null ||
            $this->km_final < $this->km_inicial
        ) {
            return null;
        }

        return $this->km_final - $this->km_inicial;
    }
}