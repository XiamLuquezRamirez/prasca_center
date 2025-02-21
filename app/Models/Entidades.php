<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Exception;

use Illuminate\Database\Eloquent\Model;

class Entidades extends Model
{
    public static function Guardar($request)
    {
        try {

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('eps')->insertGetId([
                    'nit' => $request['nit'],
                    'codigo' => $request['codigo'],
                    'entidad' => $request['nombre'],
                    'observaciones' => $request['observaciones'] ?? '',
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('eps')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'nit' => $request['nit'],
                        'codigo' => $request['codigo'],
                        'entidad' => $request['nombre'],
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

    public static function busquedaEntidad($id){
        return DB::connection('mysql')->table('eps')
        ->where("id", $id)
        ->first();
    }
}
