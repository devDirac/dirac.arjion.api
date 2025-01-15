<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmAjustesTemaUsuario extends Model
{
    use HasFactory;
    protected $table = 'apm_ajustes_tema_usuario';
    public $timestamps = false;
    protected $fillable = [
        'color_menu_lateral',
        'tipo_menu_lateral',
        'cabecera_fija',
        'menu_lateral_mini',
        'tema_claro_oscuro',
        'idioma',
        'id_usuario'
    ];
}
