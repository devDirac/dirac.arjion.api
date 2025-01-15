<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmReportesVoz extends Model
{
    use HasFactory;
    protected $table = 'apm_reportes_voz';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'titulo',
        'texto_original',
        'resumen',
        'ruta',
        'ids_resumen_generado',
        'fecha_inicio_reporte_generado',
        'fecha_fin_reporte_generado',
        'tipo',
        'img',
        'fecha_registro',
        'id_usuario',
        'id_proyecto',
        'id_contrato',
        'id_frente',
        'id_concepto',
        'id_avance',
    ];
}
