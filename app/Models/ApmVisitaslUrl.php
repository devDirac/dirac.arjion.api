<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmVisitaslUrl extends Model
{
    use HasFactory;
    protected $table = 'apm_visitasl_url';
    public $timestamps = false;
    protected $fillable = [
        'url',
        'cuenta',
        'fecha_registro',
        'id_usuario'
    ];
}
