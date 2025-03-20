<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacCatTipoSolicitud extends Model
{
    use HasFactory;
    protected $table = 'gac_cat_tipo_solicitud';
    public $timestamps = false;
    protected $fillable = [
        'clave',
        'nombre',
        'descripcion',
        'requiere_beneficiario',
        'requiere_documentos',
        'requiere_concepto',
        'mostrar_pago_quincenas',
        'muestra_notificar_nomina',
        'requiere_aprobacion_revisor',
        'dias_notifica_pago',
        'estatus',
        'fecha_registro',
        'id_usuario', 
        'requiere_fechaPago'
    ];
}
