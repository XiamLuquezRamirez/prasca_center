<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Gastos extends Model
{
    public static function Guardar($request)
    {
        try {

            if ($request['accRegistro'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('gastos')->insertGetId([
                    'descripcion' => $request['descripcion'],
                    'fecha_gasto' => $request['fecha'],
                    'categoria' => $request['categoria'],
                    'valor' => $request['valor'] ?? '',
                    'forma_pago' => $request['medioPago'] ?? '',
                    'referencia' => $request['referencia'] ?? '',
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('gastos')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'descripcion' => $request['descripcion'],
                        'fecha_gasto' => $request['fecha'],
                        'valor' => $request['valor'] ?? '',
                        'categoria' => $request['categoria'],
                        'forma_pago' => $request['medioPago'] ?? '',
                        'referencia' => $request['referencia'] ?? '',

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

    public static function recaudosCajaResumen($fecIni)
    {
        // Convierte la fecha de inicio a un objeto DateTime
        $fechaInicio = new \DateTime($fecIni);

        // Utiliza la fecha y hora actual como el último momento
        $fechaFin = new \DateTime();

        $recaudoMes = DB::connection('mysql')
            ->table('medio_pagos')
            ->leftJoin("pagos", "pagos.id", "medio_pagos.id_pago")
            ->leftJoin("servicios", "servicios.id","medio_pagos.id_servicio")
            ->leftJoin("pacientes", "pacientes.id","servicios.id_paciente")
            ->whereBetween('pagos.fecha_pago', [$fechaInicio->format('Y-m-d H:i:s'), $fechaFin->format('Y-m-d H:i:s')])
            ->select("medio_pagos.*", "pacientes.primer_nombre", "pacientes.primer_apellido","pagos.fecha_pago","servicios.descripcion AS servicio")
            ->get();

        return $recaudoMes;
    }

    public static function GuardarCaja($request)
    {
        try {

         
                $respuesta = DB::connection('mysql')->table('cajas')->insertGetId([
                    'usuario' => Auth::user()->id,
                    'saldo_anterior' => $request['saldoAnte'],
                    'abono_inicial' => $request['abono'],
                    'saldo_inicial' => $request['saldoAnte'] + $request['abono'],
                    'fecha_apertura' => $request['fechaApertura'],
                    'recaudos' => '',
                    'gastos' => '',
                    'saldo_cierre' => '',
                    'estado_caja' => 'Abierta',
                    'estado_reg' => 'ACTIVO'
                ]);
            
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
        return  $respuesta;
    }

    public static function BuscarCajas($idCaja)
    { 
        return DB::connection('mysql')->table('cajas')
            ->leftJoin("users", "users.id", "cajas.usuario")
            ->select("cajas.*", "users.nombre_usuario")
            ->where('cajas.id', $idCaja)
            ->first();
    }

    public static function GastosCaja($fecIni)
    {
        // Convierte la fecha de inicio a un objeto DateTime
        $fechaInicio = new  \DateTime($fecIni);

        // Utiliza la fecha y hora actual como el último momento
        $fechaFin = new  \DateTime();

        $recaudoMes = DB::connection('mysql')
            ->table('gastos')
            ->whereBetween('fecha_gasto', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])
            ->where("estado","ACTIVO")
            ->sum('valor');

        return $recaudoMes;
    }
    

    public static function busquedaGasto($id)
    {
        return DB::connection('mysql')->table('gastos')
            ->where("id", $id)
            ->first();
    }

    public static function guardarCat($request)
    {
        try {

            if ($request['accionCate'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('categorias_gastos')->insertGetId([
                    'descripcion' => $request['descripcion'],
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $respuesta = DB::connection('mysql')->table('categorias_gastos')
                    ->where('id', $request['idCategoria'])  // Identificar el registro a actualizar
                    ->update([
                        'descripcion' => $request['descripcion'],

                    ]);
                $respuesta = $request['idCategoria'];
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

    public static function GastosCajaDet($fecIni)
    {
        // Convierte la fecha de inicio a un objeto DateTime
        $fechaInicio = new  \DateTime($fecIni);

        // Utiliza la fecha y hora actual como el último momento
        $fechaFin = new  \DateTime();

        $recaudoMes = DB::connection('mysql')
            ->table('gastos')
            ->leftJoin("categorias_gastos","categorias_gastos.id","gastos.categoria")
            ->whereBetween('fecha_gasto', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])
            ->select("gastos.*","categorias_gastos.descripcion AS ncategoria")
            ->where("gastos.estado","ACTIVO")
            ->get();

        return $recaudoMes;
    }

}
