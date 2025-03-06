<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmNotasFrente extends Model
{
    use HasFactory;
    protected $table = 'apm_notas_frente';
    public $timestamps = false;
    protected $fillable = [
        'titulo',
        'nota',
        'id_tipo_nota',
        'id_frente',
        'fecha_registro',
        'id_usuario'
    ];
}
