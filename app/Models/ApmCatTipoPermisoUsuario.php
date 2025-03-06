<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmCatTipoPermisoUsuario extends Model
{
    use HasFactory;
    protected $table = 'apm_cat_tipo_permiso_usuario';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'nombre',
        'id_estatus',
        'fecha_registro'
    ];
}
