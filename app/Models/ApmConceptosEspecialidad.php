<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmConceptosEspecialidad extends Model
{
    use HasFactory;
    protected $table = 'apm_conceptos_especialidad';
    public $timestamps = false;
    protected $fillable = [
        'id_concepto',
        'id_subespecialidad'
    ];
}
