<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmCatOrigenDestino extends Model
{
    use HasFactory;
    protected $table = 'apm_cat_origen_destino';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'tipo',
        'id_obra',
        'id_usuario',
        'fecha_registro'
    ];
}
