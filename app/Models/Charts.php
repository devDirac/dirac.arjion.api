<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Charts extends Model
{
    use HasFactory;
    protected $table = 'charts';
    public $timestamps = false;
    protected $fillable = [
        'id',
        'scriptSQL',
        'titulo',
        'tipoGrafica',
        'esVertical',
        'esApilado',
        'rellenaEspacioEnlineal',
        'size'
    ];
}
