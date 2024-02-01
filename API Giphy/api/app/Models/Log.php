<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'usuario',
        'servicio',
        'cuerpo_peticion',
        'codigo_respuesta',
        'cuerpo_respuesta',
        'ip_origen',
    ];
}
