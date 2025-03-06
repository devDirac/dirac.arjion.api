<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacTipoCambioDolar extends Model
{
    use HasFactory;
    protected $table = 'gac_tipo_cambio_dolar';
    public $timestamps = false;
    protected $fillable = [
        'pesos_dolar',
        'fecha',
        'fecha_registro'
    ];
}
