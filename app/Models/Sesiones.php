<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Sesiones extends Model
{
    public static function guardar($request)
    {
        try {

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('sesiones')->insertGetId([
                    'descripcion' => $request['descripcion'],
                    'precio' => $request['valor'],
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('sesiones')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'descripcion' => $request['descripcion'],
                        'precio' => $request['valor'],
                        'observaciones' => $request['observaciones'] ?? '',

                    ]);
                $respuesta = $request['idRegistro'];
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
        return  $respuesta;
    
    }
    public static function busquedaSesiones($idRegistro)
    {
        $sesion = DB::connection('mysql')->table('sesiones')->where('id', $idRegistro)->first();
        return $sesion;
    }

}
