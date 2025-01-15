<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GacTest1 extends Model
{
    use HasFactory;

    protected $table = 'gac_test1';
    public $timestamps = false;
    protected $fillable = [
        'nombre'
    ];
}
