<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class Asesorias extends Model
{
    public static function Guardar($request)
    {
        try {

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('asesorias')->insertGetId([
                    'descripcion' => $request['descripcion'],
                    'valor' => $request['valor'],
                    'tiempo' => $request['tiempo'],
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('asesorias')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'descripcion' => $request['descripcion'],
                        'valor' => $request['valor'],
                        'tiempo' => $request['tiempo'],

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
    public static function busquedaAsesorias($idRegistro)
    {
        $asesoria = DB::connection('mysql')->table('asesorias')->where('id', $idRegistro)->first();
        return $asesoria;
    }
    public static function listaAsesorias() {
        $asesorias = DB::connection('mysql')->table('asesorias')->
        select('id', 'descripcion', 'valor', 'tiempo')
        ->where('estado', 'ACTIVO')
        ->get();
        return $asesorias;
    }


    public static function listaServiciosVenta($idRegistro) {
        $servicios = DB::connection('mysql')->table('servicios')
        ->join('asesorias', 'servicios.id_tipo_servicio', 'asesorias.id')
        ->join('ventas', 'servicios.id', 'ventas.id_servicio')
        ->where('servicios.id_paciente', $idRegistro)
        ->where('tipo_servicio', 'EPS')
        ->where('tipo', 'ASESORIA')
        ->where('servicios.estado', 'ACTIVO')
        ->select('servicios.id', 
        'servicios.precio', 
        'servicios.fecha', 
        'asesorias.descripcion', 
        'ventas.valor', 
        'ventas.total', 
        'ventas.saldo',
        'ventas.estado_venta')
        ->get();
        return $servicios;
    }

    public static function guardarVentaAsesoria($request){
        DB::beginTransaction();
        try {
            if($request['accVentaAsesoria'] == 'guardar'){
            $respuesta = DB::connection('mysql')->table('servicios')->insertGetId([
                'tipo' => 'ASESORIA',
                'precio' => $request['valorAsesoria'],
                'estado' => 'ACTIVO',
                'fecha' => $request['fechaAsesoria'],
                'id_tipo_servicio' => $request['tipoAsesoria'],
                'id_paciente' => $request['idEPS'],
                'tipo_servicio' => 'EPS'
            
            ]);

            $idVenta = DB::table('ventas')->insertGetId(array_filter([
                'id_servicio' => $respuesta,
                'id_paciente' => $request['idEPS'],
                'usuario' => Auth::user()->id,
                'valor' => $request['valorAsesoria'],
                'cantidad' => '1',
                'total' => $request['valorAsesoria'],
                'estado_venta' => 'PENDIENTE',
                'saldo' => $request['valorAsesoria']
            ]));

            }else{
                $respuesta = DB::connection('mysql')->table('servicios')
                ->where('id', $request['idVentaAsesoria'])
                ->update([
                'tipo' => 'ASESORIA',
                'precio' => $request['valorAsesoria'],
                'estado' => 'ACTIVO',
                'fecha' => $request['fechaAsesoria'],
                'id_tipo_servicio' => $request['tipoAsesoria'],
                'id_paciente' => $request['idEPS'],
                'tipo_servicio' => 'EPS'
                ]);

                DB::table('ventas')->where('id_servicio', $request['idVentaAsesoria'])->update(array_filter([
                    'valor' => $request['valorAsesoria'],
                    'total' => $request['valorAsesoria'],
                    'saldo' => $request['valorAsesoria'],
                ]));

            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
        return  $respuesta;
    }
}
