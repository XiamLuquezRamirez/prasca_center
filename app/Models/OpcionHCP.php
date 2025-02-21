<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcionHCP extends Model
{
    use HasFactory;
    protected $table = 'opciones_hc_psicologia';

    protected $fillable = ['categoria_id', 'opcion'];
}
