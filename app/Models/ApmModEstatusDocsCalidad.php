<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmModEstatusDocsCalidad extends Model
{
    use HasFactory;
    protected $table = 'apm_mod_estatus_docs_calidad';
    public $timestamps = false;
    protected $fillable = [
        'estatus',
        'fecha_registro',
        'id_usuario'
    ];
}
