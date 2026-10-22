<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntregaPolicia extends Model
{
    protected $table = 'entregas_policia';
    protected $guarded = ['id'];
    protected $casts = ['fecha_entrega' => 'date'];

    public function loteVale() { return $this->belongsTo(LoteVale::class); }
    public function vales() { return $this->hasMany(ValeCombustible::class, 'entrega_policia_id'); }
}
