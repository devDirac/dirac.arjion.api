<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacBitacoraEventos extends Model
{
    use HasFactory;
    protected $table = 'gac_bitacora_eventos';
    public $timestamps = false;
    protected $fillable = [
        'evento',
        'descripcion',
        'id_usuario',
        'tipo',
        'id_tabla',
        'id_ref'
    ];
}
