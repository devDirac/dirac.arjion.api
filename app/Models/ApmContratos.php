<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmContratos extends Model
{
    use HasFactory;
    protected $table = 'apm_contratos';
    public $timestamps = false;
    protected $fillable = [
            'contrato',
            'id_contrato',
            'id_contratista',
            'fecha_inicio',
            'fecha_final',
            'importe',
            'estatus',
            'id_cliente',
            'id_responsable',
            'autorizado',
            'id_autorizador',
            'plantilla',
            'terminado',
            'nota',
            'id_tipo_contrato',
            'id_obra_principal',
            'id_tipo_proyecto',
            'pep',
            'moneda',
            'anticipo',
            'categoria',
            'alertas',
            'fecha_limite',
            'reclasificacion',
            'propietario',
            'fecha_registro',
            'tipo_contrato_ext',
            'tolerancia',
            'estatus_firma',
            'contrato_liberado',
            'clasificacion_contrato',
            'tipo_cambio',
            'id_especialidad',
            'id_usuario'
    ];
}
