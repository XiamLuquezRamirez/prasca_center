<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;


class Componentes extends Model
{
    public static function Guardar($request)
    {
        try {

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('opciones_hc_psicologia')->insertGetId([
                    'opcion' => $request['componente'],
                    'categoria_id' => $request['categoria']
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('opciones_hc_psicologia')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'opcion' => $request['componente'],
                        'categoria_id' => $request['categoria']

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

    public static function buscarComponente($idRegistro)
    {
        return DB::connection('mysql')->table('opciones_hc_psicologia')
            ->where('id', $idRegistro)
            ->first();
    }
}
