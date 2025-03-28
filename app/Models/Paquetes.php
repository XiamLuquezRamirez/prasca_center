<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Paquetes extends Model
{
    public static function Guardar($request)
    {
        try {

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('paquetes')->insertGetId([
                    'descripcion' => $request['descripcion'],
                    'precio_por_sesion' => $request['valor'],
                    'observaciones' => $request['observaciones'] ?? '',
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('paquetes')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'descripcion' => $request['descripcion'],
                        'precio_por_sesion' => $request['valor'],
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

    public static function paqueteActivo($id)
    {
        return DB::connection('mysql')->table('servicios')
            ->where("servicios.id_historia", $id)
            ->leftJoin('ventas', 'servicios.id', 'ventas.id_servicio')
            ->where("ventas.estado_venta", "PENDIENTE")
            ->where("servicios.estado", "ACTIVO")
            ->where("servicios.tipo_historia", "PSICOLOGIA")
            ->first();
    }
    public static function paqueteActivoNeuro($id)
    {
        return DB::connection('mysql')->table('servicios')
            ->where("servicios.id_historia", $id)
            ->leftJoin('ventas', 'servicios.id', 'ventas.id_servicio')
            ->where("ventas.estado_venta", "PENDIENTE")
            ->where("servicios.estado", "ACTIVO")
            ->where("servicios.tipo", "PAQUETE")
            ->where("servicios.tipo_historia", "NEUROPSICOLOGIA")
            ->first();
    }

    public static function busquedaPaquetes($id)
    {
        return DB::connection('mysql')->table('paquetes')
            ->where("id", $id)
            ->first();
    }

    public static function busquedaPaquetesVentas($id)
    {
        $servicio = DB::connection('mysql')->table('servicios')
            ->leftJoin('ventas', 'servicios.id', 'ventas.id_servicio')
            ->where("servicios.id", $id)
            ->select('servicios.id',
            'servicios.id_tipo_servicio',
            'servicios.precio',
            'servicios.fecha',
            'ventas.valor',
            'ventas.cantidad',
            'servicios.tipo'
            )
            ->first();
        if($servicio->tipo == 'PAQUETE'){
            $servicio->descripcion= DB::connection('mysql')->table('paquetes')
                ->where("id", $servicio->id_tipo_servicio)
                ->select('descripcion')
            ->first();
        }else if($servicio->tipo == 'SESION'){
            $servicio->descripcion = DB::connection('mysql')->table('sesiones')
                ->where("id", $servicio->id_tipo_servicio)
                ->select('descripcion')
            ->first();
        }else if($servicio->tipo == 'PRUEBAS'){
            $servicio->descripcion = DB::connection('mysql')->table('pruebas')
                ->where("id", $servicio->id_tipo_servicio)
                ->select('descripcion as descripcion')
            ->first();
        }else if($servicio->tipo == 'CONSULTA'){
            $servicio->descripcion = DB::connection('mysql')->table('especialidades')
                ->where("id", $servicio->id_tipo_servicio)
                ->select('nombre as descripcion')
            ->first();
        }
        return $servicio;
    }

    public static function listarPaquetes()
    {
        return DB::connection('mysql')->table('paquetes')
            ->where('estado', 'ACTIVO')
            ->get();
    }

    
    

    public static function GuardarPagoPaquete($request)
    {
        try {
            $idPago = $request['idPago'];
            if ($request['accPago'] == 'guardar') {
                DB::beginTransaction();
                try {
                    $pagoRealizado = 0;
                    if ($request['abono'] == 0) {
                        $pagoRealizado = $request['valotTotalVentPaq'];
                    } else {
                        $pagoRealizado = $request['abono'];
                    }

                    $idPago = DB::connection('mysql')->table('pagos')->insertGetId([
                        'id_servicio' => $request['idVentaServicio'],
                        'pago_total' => $request['valotTotalVentPaq'],
                        'abono' => $request['abono'],
                        'pago_realizado' => $pagoRealizado,
                        'usuario' => Auth::user()->id,
                        'fecha_pago' => $request['fechaPago'],
                        'estado' => 'ACTIVO'
                    ]);

                    // insertar medios de pagos

                    foreach ($request["selMedioPago"] as $key => $val) {
                        $respuesta = DB::connection('mysql')->table('medio_pagos')->insert([
                            'id_pago' => $idPago,
                            'id_servicio' => $request['idVentaServicio'],
                            'medio_pago' => $request["selMedioPago"][$key],
                            'valor' => $request["valorPago"][$key],
                            'referencia' => $request["referenciaPago"][$key],
                        ]);
                    }

                    // Actualizar el saldo del paquete
                    $saldo = $request['valotTotalVentPaq'] - $pagoRealizado;
                    if ($saldo == 0) {
                        $estado = 'PAGADO';
                    } else {
                        $estado = 'PENDIENTE';
                    }

                    DB::table('ventas')->where('id_servicio', $request['idVentaServicio'])->update([
                        'saldo' => $saldo,
                        'estado_venta' =>$estado 
                    ]);

                    // Confirmar transacción
                    DB::commit();
                    return  $idPago;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
                DB::beginTransaction();

                try {

                    // Confirmar transacción
                    DB::commit();
                    return  $idPago;
                } catch (\Exception $e) {
                    Log::error('Error al actualizar el informe: ' . $e->getMessage(), [
                        'idPago' => $idPago,
                        'data' => $request->all()
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
