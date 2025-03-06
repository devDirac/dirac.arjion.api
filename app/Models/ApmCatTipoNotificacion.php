<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmCatTipoNotificacion extends Model
{
    use HasFactory;
    protected $table = 'apm_cat_tipo_notificacion';
    public $timestamps = false;
    protected $fillable = [
        'tipo_notificacion',
        'descripcion'
    ];
}
