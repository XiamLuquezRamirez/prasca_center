<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Especialidades extends Model
{
    public static function Guardar($request)
    {
        try {  

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('especialidades')->insertGetId([
                    'nombre' => $request['nombre'],
                    'observacion' => $request['observaciones'] ?? '',
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('especialidades')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'nombre' => $request['nombre'],
                        'observacion' => $request['observaciones'] ?? '',

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

    public static function busquedaEspecialidad($id){
        return DB::connection('mysql')->table('especialidades')
        ->where("id", $id)
        ->first();
    }
}
