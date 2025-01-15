<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmPermisosUsuarios extends Model
{
    use HasFactory;
    protected $table = 'apm_permisos_usuarios';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'id_permiso',
        'id_usuario',
        'fecha'
    ];
}
