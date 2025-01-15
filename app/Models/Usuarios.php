<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarios extends Model {
    use HasFactory;
    protected $table = 'usuarios';
    public $timestamps = false;
    protected $fillable = [
        'id_user',
        'nombre',
        'apellidos',
        'correo',
        'phone'
    ];
}
