<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmInsumosDocsEntradas extends Model
{
    use HasFactory;
    protected $table = 'apm_insumos_docs_entradas';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'tamanio',
        'tipo',
        'path',
        'id_entrada',
        'id_obra',
        'id_usuario',
        'fecha_registro'
    ];
}
