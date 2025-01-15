<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmModDocsBitacoraC extends Model
{
    use HasFactory;

    protected $table = 'apm_mod_docs_bitacora_c';
    public $timestamps = false;
    protected $fillable = [
        'id_documento',
        'id_usuario',
        'id_estatus',
        'descripcion',
        'fecha_registro'
    ];
}
