<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Producto extends Model
{

    protected $fillable = [
        'banco_id','codigo','tipo','nombre_comercial','moneda',
        'monto_minimo','monto_maximo',
        'plazo_minimo_meses','plazo_maximo_meses',
        'tea_minimo','tea_maximo',
        'tcea_referencial','tcea_minima',
        'seguro_desgravamen_mensual','seguro_extra',
        'categoria'
    ];

    public function banco() : BelongsTo
    {
        return $this->belongsTo(Banco::class);
    }

}
