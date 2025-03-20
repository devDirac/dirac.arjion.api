<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacCuentasBancariasUsuario extends Model
{
    use HasFactory;
    protected $table = 'gac_cuentas_bancarias_usuario';
    public $timestamps = false;
    protected $fillable = [
        'banco',
        'cuenta',
        'clabe',
        'alias',
        'ruta',
        'id_usuario'
    ];
}
