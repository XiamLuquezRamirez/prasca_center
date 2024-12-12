<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaHCP extends Model
{
    use HasFactory;
    protected $table = 'categorias_hc_psicologia';

    public function opciones()
    {
        return $this->hasMany(OpcionHCP::class, 'categoria_id');
    }
}
