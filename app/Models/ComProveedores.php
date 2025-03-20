<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComProveedores extends Model
{
    use HasFactory;
    protected $table = 'com_proveedores';
    public $timestamps = false;
    protected $fillable = [
        'nombre'
    ];
}
