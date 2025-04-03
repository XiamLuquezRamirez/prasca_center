<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class CIE10 extends Model
{
    public static function Guardar($request)
    {
        try {  

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('referencia_cie10')->insertGetId([
                    'codigo' => $request['codigo'],
                    'nombre' => $request['nombre'],
                    'descripcion' => $request['descripcion'] ?? '',
                    'habilitado' => $request['habilitado'],
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('referencia_cie10')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'codigo' => $request['codigo'],
                        'nombre' => $request['nombre'],
                        'descripcion' => $request['descripcion'] ?? '',
                        'habilitado' => $request['habilitado'],
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
}
