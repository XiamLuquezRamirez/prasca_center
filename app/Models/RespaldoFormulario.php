<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RespaldoFormulario extends Model
{
    protected $table = 'respaldo_formularios';

    protected $fillable = ['datos', 'user_id', 'formulario', 'paciente'];

    protected $casts = [
        'datos' => 'array',
    ];
}
