<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmBitacoraEventos extends Model
{
    use HasFactory;
    protected $table = 'apm_bitacora_eventos';
    public $timestamps = false;
    protected $fillable = [
        'evento',
        'descripcion',
        'tabla',
        'id_referencia',
        'id_usuario'
    ];
}
