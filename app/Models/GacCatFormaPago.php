<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacCatFormaPago extends Model
{
    use HasFactory;
    protected $table = 'gac_cat_forma_pago';
    public $timestamps = false;
    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'estatus',
        'fecha_registro',
        'id_usuario'
    ];
}
