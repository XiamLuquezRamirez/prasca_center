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
        return DB::connection('mysql')->table('ventas_paquetes')
            ->where("historia_clinica_id", $id)
            ->where("estado_control", "PENDIENTE")
            ->where("estado", "ACTIVO")
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
        $paquetes = DB::connection('mysql')->table('ventas_paquetes')
            ->where("id", $id)
            ->first();
        $paquetes->descripion_paquete = DB::connection('mysql')->table('paquetes')
            ->where("id", $paquetes->paquete_id)
            ->first();

         //sumatorio de abonos realizados
         $paquetes->abonos = DB::connection('mysql')->table('pagos')
            ->where("venta_paquete_id", $id)
            ->sum('abono');

        return $paquetes;
    }

    public static function listarPaquetes()
    {
        return DB::connection('mysql')->table('paquetes')
            ->where('estado', 'ACTIVO')
            ->get();
    }

    public static function guardarPaqueteVenta($request)
    {
        try {
            $idPaquete = $request['idPaquete'];
            if ($request['accPaquete'] == 'guardar') {
                DB::beginTransaction();
                try {

                    $idInforme = DB::table('ventas_paquetes')->insertGetId(array_filter([
                        'historia_clinica_id' => $request['idHist'],
                        'paquete_id' => $request['selPaquete'],
                        'fecha_compra' => $request['fechaPaquete'],
                        'sesiones_compradas' => $request['numSesiones'],
                        'sesiones_disponibles' => $request['numSesiones'],
                        'valor_sesion' => $request['precioSesion'],
                        'monto_total' => $request['montoFinal'] ?? '',
                        'saldo' => $request['montoFinal'] ?? '',
                        'estado_control' => 'PENDIENTE',
                        '',
                        'estado_venta' => 'PENDIENTE',
                        '',
                        'estado' => 'ACTIVO'
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idPaquete;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
                DB::beginTransaction();

                try {

                    DB::table('ventas_paquetes')->where('id', $idPaquete)->update(array_filter([
                        'historia_clinica_id' => $request['idHist'],
                        'paquete_id' => $request['selPaquete'],
                        'fecha_compra' => $request['fechaPaquete'],
                        'sesiones_compradas' => $request['numSesiones'],
                        'sesiones_disponibles' => $request['numSesiones'],
                        'valor_sesion' => $request['precioSesion'],
                        'monto_total' => $request['montoFinal'] ?? ''
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idPaquete;
                } catch (\Exception $e) {
                    Log::error('Error al actualizar el informe: ' . $e->getMessage(), [
                        'idPaquete' => $idPaquete,
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
                        'venta_paquete_id' => $request['idVentaPaquete'],
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
                            'id_venta_paquete' => $request['idVentaPaquete'],
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

                    DB::table('ventas_paquetes')->where('id', $request['idVentaPaquete'])->update([
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
