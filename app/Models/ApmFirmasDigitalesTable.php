<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmFirmasDigitalesTable extends Model
{
    use HasFactory;
    protected $table = 'apm_firmas_digitales';
    public $timestamps = false;
    protected $fillable = [
        'id_usuario',
        'clave',
        'firma_txt',
        'imagen',
        'fecha_registro'
    ];
}
