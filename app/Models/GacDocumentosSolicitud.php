<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacDocumentosSolicitud extends Model
{
    use HasFactory;
    protected $table = 'gac_documentos_solicitud';
    public $timestamps = false;
    protected $fillable = [
        'importe',
        'nombre_corto',
        'descripcion',
        'tipo_moneda',
        'documento_valido',
        'descripcion_documento_validado',
        'nombre_documento',
        'ruta',
        'id_solicitud',
        'id_usuario',
        'estatus',
        'fecha_registro',
        'es_valido_revisor',
        'comentarios_supervisor',
        'idrevisor'
    ];
}
