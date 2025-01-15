<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmActividadesConceptos extends Model
{
    use HasFactory;
    protected $table = 'apm_actividades_conceptos';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'fecha_inicio',
        'fecha_fin',
        'id_responsable',
        'comentarios',
        'fecha_captura',
        'fecha_reprogramada',
        'estatus',
        'cantidad',
        'costo',
        'autorizacion',
        'dias_anticipacion',
        'dias_anticipacion2',
        'clave',
        'fecha_registro',
        'id_usuario'
    ];
}
