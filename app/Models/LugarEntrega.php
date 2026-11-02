<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LugarEntrega extends Model
{
    protected $table = 'lugares_entrega';

    protected $fillable = [
        'nombre',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function valesCombustible(): HasMany
    {
        return $this->hasMany(ValeCombustible::class, 'lugar_entrega_id');
    }
}
