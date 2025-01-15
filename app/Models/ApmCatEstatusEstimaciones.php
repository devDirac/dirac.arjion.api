<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApmCatEstatusEstimaciones extends Model
{
    use HasFactory;
    protected $table = 'apm_cat_estatus_estimaciones';
    public $timestamps = false;
    protected $fillable = [
        'evento',
        'descripcion',
        'estatus'
    ];
}
