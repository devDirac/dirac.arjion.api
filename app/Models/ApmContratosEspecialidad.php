<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmContratosEspecialidad extends Model
{
    use HasFactory;
    protected $table = 'apm_contratos_especialidad';
    public $timestamps = false;
    protected $fillable = [
        'id_contrato',
        'id_subespecialidad'
    ];
}
