<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Perfil extends Model
{
    // Otros campos del perfil

    public static function Guardar()
    {
        try {
            $respuesta = DB::connection('mysql')->table('perfiles')->insertGetId([
                'nombre' => request()->input('nombre'),
                'estado' => 'ACTIVO'
            ]);
            return $respuesta;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function Listar()
    {
        return DB::connection('mysql')->table('perfiles')
            ->where('estado', 'ACTIVO')
            ->get();
    }

    public static function Buscar($id)
    {
        return DB::connection('mysql')->table('perfiles')
            ->where('id', $id)
            ->first();
    }

    public static function Actualizar()
    {
        try {
            $respuesta = DB::connection('mysql')->table('perfiles')
                ->where('id', request()->input('id'))
                ->update([
                    'nombre' => request()->input('nombre')
                ]);
            return $respuesta;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public static function Eliminar()
    {
        try {
            $respuesta = DB::connection('mysql')->table('perfiles')
                ->where('id', request()->input('id'))
                ->update([
                    'estado' => 'INACTIVO'
                ]);
            return $respuesta;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
