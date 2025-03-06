<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacCatConceptos extends Model
{
    use HasFactory;
    protected $table = 'gac_cat_conceptos';
    public $timestamps = false;
    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'categoria',
        'estatus',
        'fecha_registro',
        'id_usuario'
    ];
}
