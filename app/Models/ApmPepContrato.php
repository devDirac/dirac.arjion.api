<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmPepContrato extends Model
{
    use HasFactory;
    protected $table = 'apm_pep_contrato';
    public $timestamps = false;
    protected $fillable = [
        'id_pep',
        'id_contrato',
        'fecha_registro',
        'id_usuario'
    ];
}
