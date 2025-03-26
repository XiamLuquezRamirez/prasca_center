<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;


class Pruebas extends Model
{
    public static function guardar($request)
    {
        try {

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('pruebas')->insertGetId([
                    'descripcion' => $request['descripcion'],
                    'precio' => $request['valor'],
                    'observaciones' => $request['observaciones'] ?? '',
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('pruebas')
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

    public static function busquedaPaquetesVentas($id){
        $pruebas = DB::connection('mysql')->table('servicios')
        ->leftJoin('ventas', 'servicios.id', 'ventas.id_servicio')
        ->where("servicios.id", $id)
        ->select('servicios.id',
        'servicios.id_paquete',
        'servicios.precio',
        'servicios.fecha',
        'ventas.valor',
        'ventas.cantidad',
        'servicios.descripcion',
        'servicios.tipo'
        )
        ->first();
   
    $pruebas->descripion_prueba = DB::connection('mysql')->table('pruebas')
        ->where("id", $pruebas->id_paquete)
        ->first();
    return $pruebas;
    }

    public static function busquedaPruebas($idRegistro)
    {
        $prueba = DB::connection('mysql')->table('pruebas')->where('id', $idRegistro)->first();
        return $prueba;
    }

    public static function listarPruebas()
    {
        $pruebas = DB::connection('mysql')->table('pruebas')->where('estado', 'ACTIVO')->get();
        return $pruebas;
    }

    public static function guardarPruebaVenta($request)
    {
        try {
            $idPrueba = $request['idVentaPrueba'];
            if ($request['accVentaPrueba'] == 'guardar') {
                DB::beginTransaction();
                try {
                    $idPruebaVenta = DB::table('servicios')->insertGetId(array_filter([
                        'tipo' => 'PRUEBAS',
                        'descripcion' => $request['descripcionPrueba'],
                        'id_historia' => $request['idHist'],
                        'precio' => $request['precioPrueba'],
                        'estado' => 'ACTIVO',
                        'tipo_historia' => $request['tipoHistoria'],
                        'fecha' => $request['fechaPrueba'] ,
                        'id_paquete' => $request['selPrueba'],
                        'id_paciente' => $request['idPacienteVentaPrueba'],
                    ]));

                    $idVenta = DB::table('ventas')->insertGetId(array_filter([
                        'id_servicio' => $idPruebaVenta,
                        'id_historia' => $request['idHist'],
                        'usuario' => Auth::user()->id,
                        'valor' => $request['precioPrueba'],
                        'cantidad' => '1',
                        'total' => $request['precioPrueba'],
                        'estado_venta' => 'PENDIENTE',
                        'saldo' => $request['precioPrueba']
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idPruebaVenta;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
                DB::beginTransaction();

                try {

                    DB::table('servicios')->where('id', $request['idVentaPrueba'])->update(array_filter([
                        'descripcion' => $request['descripcionPrueba'],
                        'id_paquete' => $request['selPrueba'],
                        'precio' => $request['precioPrueba'],
                        'fecha' => $request['fechaPrueba']
                    ]));

                    DB::table('ventas')->where('id_servicio', $request['idVentaPrueba'])->update(array_filter([
                        'valor' => $request['precioPrueba'],
                        'total' => $request['precioPrueba'],
                        'cantidad' => '1',
                        'saldo' => $request['precioPrueba']
                    ]));
                    DB::commit();

                    // Confirmar transacción
                    DB::commit();
                    return  $idPrueba;
                } catch (\Exception $e) {
                    Log::error('Error al actualizar el informe: ' . $e->getMessage(), [
                        'idPrueba' => $idPrueba,
                        'data' => $request
                    ]);
                    DB::rollBack();
                    throw $e;
                }
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
    }
}
