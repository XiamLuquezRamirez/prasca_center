<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Servicios extends Model
{
    use HasFactory;

    public static function buscaServicioVenta($idServicio){
        
        $servicio = DB::connection('mysql')
        ->table('servicios')
        ->where('id', '=', $idServicio)
        ->first();

        if ($servicio->tipo == 'PAQUETE') {
            $venta = DB::connection('mysql')
            ->table('ventas')
            ->where('id_servicio', '=', $idServicio)
            ->first();

            $servicio->cantidad = $venta->cantidad;
        }
        return $servicio;
    }
}
