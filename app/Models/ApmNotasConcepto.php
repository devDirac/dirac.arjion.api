<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmNotasConcepto extends Model
{
    use HasFactory;
    protected $table = 'apm_notas_concepto';
    public $timestamps = false;
    protected $fillable = [
        'titulo',
        'nota',
        'id_tipo_nota',
        'id_concepto',
        'fecha_registro',
        'id_usuario'
    ];
}
