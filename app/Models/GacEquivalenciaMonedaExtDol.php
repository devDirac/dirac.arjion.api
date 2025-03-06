<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacEquivalenciaMonedaExtDol extends Model
{
    use HasFactory;
    protected $table = 'gac_equivalencia_moneda_ext_dol';
    public $timestamps = false;
    protected $fillable = [
        'pais',
        'moneda',
        'valor_en_dolar',
        'fecha',
        'fecha_registro'
    ];
}
