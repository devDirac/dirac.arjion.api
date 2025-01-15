<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParametrosAnalisis extends Model
{
    use HasFactory;
    protected $table = 'parametros_analisis';
    public $timestamps = false;
    protected $fillable = [
        'nombre',
        'descripcion',
        'id_usuario'
    ];
}
