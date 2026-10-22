<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrganismoFinanciador extends Model
{
    protected $table = 'organismos_financiadores';

    protected $fillable = [
        'codigo',
        'nombre',
        
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function aperturasProgramaticas(): HasMany
    {
        return $this->hasMany(
            AperturaProgramatica::class,
            'organismo_financiador_id'
        );
    }
}