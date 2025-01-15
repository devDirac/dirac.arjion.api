<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmProgramasFinancierosGuardados extends Model
{
    use HasFactory;
    protected $table = 'apm_programas_financieros_guardados';
    public $timestamps = false;
    protected $fillable = [
        'fecha',
        'motivo',
        'convenio',
        'id_usuario',
        'fecha_registro',
        'id_contrato'
    ];
}
