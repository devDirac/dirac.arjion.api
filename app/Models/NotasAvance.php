<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotasAvance extends Model
{
    use HasFactory;
    protected $table = 'apm_notas_avance';
    public $timestamps = false;
    protected $fillable = [
        'titulo',
        'nota',
        'id_tipo_nota',
        'id_concepto',
        'id_avance',
        'fecha_registro',
        'id_usuario'
    ];
}
