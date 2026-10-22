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
        'observacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'presentado' => 'boolean',
    ];

    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
