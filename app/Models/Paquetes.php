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
        $paquetes = DB::connection('mysql')->table('servicios')
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
       
        $paquetes->descripion_paquete = DB::connection('mysql')->table('paquetes')
            ->where("id", $paquetes->id_paquete)
            ->first();

         //sumatorio de abonos realizados
        //  $paquetes->abonos = DB::connection('mysql')->table('pagos')
        //     ->where("venta_paquete_id", $id)
        //     ->sum('abono');

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
            $idPaquete = $request['idVentaPaquete'];
            if ($request['accVentaPaquete'] == 'guardar') {
                DB::beginTransaction();
                try {
                    $idPaqueteVenta = DB::table('servicios')->insertGetId(array_filter([
                        'tipo' => 'PAQUETE',
                        'descripcion' => $request['descripcionVentaPaquete'],
                        'id_historia' => $request['idHist'],
                        'precio' => $request['montoFinal'],
                        'estado' => 'ACTIVO',
                        'tipo_historia' => $request['tipoHistoria'],
                        'fecha' => $request['fechaPaquete'] ,
                        'id_paquete' => $request['selPaquete'],
                        'id_paciente' => $request['idPacienteVentaPaquete'],
                    ]));

                    $idVenta = DB::table('ventas')->insertGetId(array_filter([
                        'id_servicio' => $idPaqueteVenta,
                        'id_historia' => $request['idHist'],
                        'usuario' => Auth::user()->id,
                        'valor' => $request['precioSesion'],
                        'cantidad' => $request['numSesiones'],
                        'total' => $request['montoFinal'],
                        'estado_venta' => 'PENDIENTE',
                        'saldo' => $request['montoFinal']
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idPaqueteVenta;
                } catch (\Exception $e) {
                    // Revertir transacción en caso de error
                    DB::rollBack();
                    throw $e;
                }
            } else {
                DB::beginTransaction();

                try {

                    DB::table('servicios')->where('id', $request['idVentaPaquete'])->update(array_filter([
                        'descripcion' => $request['descripcionVentaPaquete'],
                        'id_paquete' => $request['selPaquete'],
                        'precio' => $request['montoFinal'],
                        'fecha' => $request['fechaPaquete'],
                    ]));

                    DB::table('ventas')->where('id_servicio', $request['idVentaPaquete'])->update(array_filter([
                        'valor' => $request['precioSesion'],
                        'total' => $request['montoFinal'],
                        'cantidad' => $request['numSesiones'],
                        'saldo' => $request['montoFinal'],
                    ]));
                    DB::commit();

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
