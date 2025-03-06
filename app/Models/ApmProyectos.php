<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmProyectos extends Model
{
    use HasFactory;
    protected $table = 'apm_proyectos';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'id_obra',
        'created_at',
        'updated_at'
    ];
}
