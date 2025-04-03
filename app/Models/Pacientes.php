<?php

namespace App\Models;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use Exception;

class Pacientes extends Model
{
    public static function listOcupaciones()
    {
        return DB::connection('mysql')->table('ocupaciones')
            ->where('estado', 'Activo')
            ->get();
    }

    public static function listMunicipios($muni)
    {
        return DB::connection('mysql')->table('municipios')
            ->where('departamento', $muni)
            ->get();
    }

    public static function recaudoCaja($fecIni)
    {
        // Convierte la fecha de inicio a un objeto DateTime
        $fechaInicio = new \DateTime($fecIni);

        // Utiliza la fecha y hora actual como el último momento
        $fechaFin = new \DateTime();

        $recaudo = DB::connection('mysql')
            ->table('pagos')
            ->whereBetween('fecha_pago', [$fechaInicio->format('Y-m-d'), $fechaFin->format('Y-m-d')])
            ->where("estado", "ACTIVO")
            ->sum('pago_realizado');

        return $recaudo;
    }

    public static function listConsultas()
    {
        return DB::connection('mysql')->table('especialidades')
            ->where('estado', 'ACTIVO')
            ->get();
    }

    public static function listSesiones()
    {
        return DB::connection('mysql')->table('sesiones')
            ->where('estado', 'ACTIVO')
            ->get();
    }

    public static function listPaquetes()
    {
        return DB::connection('mysql')->table('paquetes')
            ->where('estado', 'ACTIVO')
            ->get();
    }

    public static function listPruebas()
    {
        return DB::connection('mysql')->table('pruebas')
            ->where('estado', 'ACTIVO')
            ->get();
    }

    public static function busquedaProfesional()
    {

        return DB::connection('mysql')->table('profesionales')
            ->where('usuario', Auth::user()->id)
            ->where('estado', 'ACTIVO')
            ->exists();
    }

    public static function listDepartamentos()
    {
        return DB::connection('mysql')->table('departamentos')
            ->where('habilitado', 'SI')
            ->get();
    }
    public static function listTipoUsuario()
    {
        return DB::connection('mysql')->table('tipo_usuario')
            ->get();
    }
    public static function listEps()
    {
        return DB::connection('mysql')->table('eps')
            ->get();
    }


    public static function BuscarPaciente($id)
    {
        return DB::connection('mysql')->table('pacientes')
            ->where('id', $id)
            ->first();
    }

    public static function guardar($request)
    {
        try {
            if ($request['accPacientes'] == 'guardar') {
                $idpaciente = DB::connection('mysql')->table('pacientes')->insertGetId([
                    'tipo_identificacion' => $request['tipoId'],
                    'identificacion' => $request['identificacion'],
                    'tipo_usuario' => $request['tipoUsuario'] ?? '',
                    'fecha_nacimiento' => $request['fechaNacimiento'],
                    'edad' => $request['edad'],
                    'primer_nombre' => $request['primerNombre'],
                    'segundo_nombre' => $request['segundoNombre'] ?? '',
                    'primer_apellido' => $request['primerApellido'],
                    'segundo_apellido' => $request['segundoApellido'] ?? '',
                    'sexo' => $request['sexo'] ?? '',
                    'ocupacion' => $request['ocupacion'] ?? '',
                    'lateralidad' => $request['lateralidad'] ?? '',
                    'religion' => $request['religion'] ?? '',
                    'lugar_nacimiento' => $request['lugarNacimiento'] ?? '',
                    'email' => $request['email'],
                    'telefono' => $request['telefono'],
                    'direccion' => $request['direccion'] ?? '',
                    'zona_residencial' => $request['zonaResidencial'],
                    'departamento' => $request['departamento'],
                    'municipio' => $request['municipio'],
                    'estado_civil' => $request['estadocivil'] ?? '',
                    'observaciones' => $request['observaciones'] ?? '',
                    'foto' => $request['img'],
                    'acompanante' => $request['nombreAcompanante'] ?? '',
                    'parentesco' =>  $request['parentesco'] ?? '',
                    'telefono_acompanate' =>  $request['telefonoAcompanante'] ?? '',
                    'eps' =>  $request['eps'] ?? '',
                    'estado' => 'ACTIVO',
                    'completo' => $request['camposVacios']
                ]);

                if (isset($request['archivo']) && is_array($request['archivo'])) {
                    foreach ($request['archivo'] as $key => $archivo) {
                        DB::connection('mysql')->table('anexos_pacientes')->insert([
                            'paciente' => $idpaciente,
                            'origen' => 'PACIENTE',
                            'url' => $archivo,
                            'tipo_archivo' => $request['tipoArc'][$key] ?? null,
                            'nombre_archivo' => $request['nombre'][$key] ?? null,
                            'peso' => $request['peso'][$key] ?? null,
                        ]);
                    }
                }
                
            } else {

                $respuesta = DB::connection('mysql')->table('pacientes')
                    ->where('id', $request['idPaciente'])  // Identificar el registro a actualizar
                    ->update([
                        'tipo_identificacion' => $request['tipoId'],
                        'identificacion' => $request['identificacion'],
                        'tipo_usuario' => $request['tipoUsuario'] ?? '',
                        'fecha_nacimiento' => $request['fechaNacimiento'],
                        'edad' => $request['edad'],
                        'primer_nombre' => $request['primerNombre'],
                        'segundo_nombre' => $request['segundoNombre'] ?? '',
                        'primer_apellido' => $request['primerApellido'],
                        'segundo_apellido' => $request['segundoApellido'] ?? '',
                        'sexo' => $request['sexo'] ?? '',
                        'ocupacion' => $request['ocupacion'] ?? '',
                        'lateralidad' => $request['lateralidad'] ?? '',
                        'religion' => $request['religion'] ?? '',
                        'lugar_nacimiento' => $request['lugarNacimiento'] ?? '',
                        'email' => $request['email'],
                        'telefono' => $request['telefono'],
                        'direccion' => $request['direccion'] ?? '',
                        'zona_residencial' => $request['zonaResidencial'],
                        'departamento' => $request['departamento'],
                        'municipio' => $request['municipio'],
                        'estado_civil' => $request['estadocivil'] ?? '',
                        'observaciones' => $request['observaciones'] ?? '',
                        'foto' => $request['img'],
                        'acompanante' => $request['nombreAcompanante'] ?? '',
                        'parentesco' =>  $request['parentesco'] ?? '',
                        'telefono_acompanate' =>  $request['telefonoAcompanante'] ?? '',
                        'eps' =>  $request['eps'] ?? '',
                        'estado' => 'ACTIVO',
                        'completo' => $request['camposVacios']
                    ]);

                $idpaciente = $request['idPaciente'];

                if (isset($request['archivo']) && is_array($request['archivo'])) {
                    foreach ($request['archivo'] as $key => $archivo) {
                        DB::connection('mysql')->table('anexos_pacientes')->insert([
                            'paciente' => $idpaciente,
                            'origen' => 'PACIENTE',
                            'url' => $archivo,
                            'tipo_archivo' => $request['tipoArc'][$key] ?? null,
                            'nombre_archivo' => $request['nombre'][$key] ?? null,
                            'peso' => $request['peso'][$key] ?? null,
                        ]);
                    }
                }
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
        return  $idpaciente;
    }
    public static function guardarPendiente($request)
    {
        try {
            $fechaFormateada = date("Y-m-d", strtotime(str_replace('/', '-', $request['fechaNacimiento'])));

            $respuesta = DB::connection('mysql')->table('pacientes')->insertGetId([
                'tipo_identificacion' => $request['tipoId'],
                'identificacion' => $request['identificacion'],
                'tipo_usuario' => $request['tipoUsuario'] ?? '',
                'fecha_nacimiento' => $fechaFormateada,
                'edad' => $request['edad'],
                'primer_nombre' => $request['primerNombre'],
                'segundo_nombre' => $request['segundoNombre'] ?? '',
                'primer_apellido' => $request['primerApellido'],
                'segundo_apellido' => $request['segundoApellido'] ?? '',
                'sexo' => $request['sexo'] ?? '',
                'email' => $request['email'],
                'telefono' => $request['telefono'],
                'direccion' => $request['direccion'] ?? '',
                'zona_residencial' => $request['zonaResidencial'],
                'observaciones' => $request['observaciones'] ?? '',
                'estado' => 'ACTIVO',
                'foto' => 'default.jpg',
                'completo' => false
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

    public static function busquedaPacienteAnexos($idPac)
    {
        $anexos = DB::connection('mysql')->table('anexos_pacientes')
            ->where("paciente", $idPac)
            ->where("origen", "PACIENTE")
            ->get();

        return $anexos;
    }

    public static function busquedaPaciente($idPac)
    {

        $paciente = DB::connection('mysql')->table('pacientes')
            ->where("id", $idPac)
            ->where("estado", "ACTIVO")
            ->first();


        if ($paciente->eps) {
            $paciente->eps_info = DB::connection('mysql')->table('eps')
                ->where("id", $paciente->eps)
                ->first();
        } else {
            $paciente->eps_info = "Sin EPS";
        }

        $paciente->departamento_info = DB::connection('mysql')->table('departamentos')
            ->where("codigo", $paciente->departamento)
            ->first();

        $paciente->municipio_info = DB::connection('mysql')->table('municipios')
            ->where("codigo", $paciente->municipio)
            ->first();

        $fechaNacimiento = $paciente->fecha_nacimiento;
        $fechaNacimiento = \Carbon\Carbon::parse($fechaNacimiento);
        $fechaActual = \Carbon\Carbon::now();
        $diferencia = $fechaActual->diff($fechaNacimiento);
        $edadTexto = "{$diferencia->y} años, {$diferencia->m} meses, y {$diferencia->d} días";

        $paciente->edadTexto = $edadTexto;

        return $paciente;
    }

    public static function guardarVentaConsulta($request)
    {
        try {


            DB::beginTransaction();
            try {
                if ($request['accHistoriaVenta'] == "editar") {
                    DB::table('servicios')->where('id', $request['idConsultaVenta'])->update(array_filter([
                        'id_tipo_servicio' => $request['consultaVenta'],
                        'precio' => $request['valorServConsulta'],
                        'fecha' => $request['fechaVentaConsulta'] . ' ' . $request['horaSeleccionadaVentaConsulta'],
                        'tipo_servicio' => $request['tipoServicioConsulta']
                    ]));
                    DB::table('ventas')->where('id_servicio', $request['idConsultaVenta'])->update(array_filter([
                        'valor' => $request['valorServConsulta'],
                        'total' => $request['valorServConsulta'],
                        'saldo' => $request['valorServConsulta'],
                    ]));
                    DB::commit();
                    return  $request['idConsultaVenta'];
                } else {
                    $idConsultaVenta = DB::table('servicios')->insertGetId(array_filter([
                        'id_tipo_servicio' => $request['consultaVenta'],
                        'tipo' => 'CONSULTA',
                        'precio' => $request['valorServConsulta'],
                        'estado' => 'ACTIVO',
                        'id_paciente' => $request['idPaciente'],
                        'fecha' => $request['fechaVentaConsulta'] . ' ' . $request['horaSeleccionadaVentaConsulta'],
                        'tipo_servicio' => $request['tipoServicioConsulta']
                    ]));

                    $idVenta = DB::table('ventas')->insertGetId(array_filter([
                        'id_servicio' => $idConsultaVenta,
                        'id_paciente' => $request['idPaciente'],
                        'usuario' => Auth::user()->id,
                        'valor' => $request['valorServConsulta'],
                        'cantidad' => '1',
                        'total' => $request['valorServConsulta'],
                        'estado_venta' => 'PENDIENTE',
                        'saldo' => $request['valorServConsulta'],
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idConsultaVenta;
                }
            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
    }

    public static function guardarVentaSesion($request)
    {
        try {

            DB::beginTransaction();
            try {
                if ($request['accHistoriaVentaSesion'] == "editar") {
                    DB::table('servicios')->where('id', $request['idVentaSesion'])->update(array_filter([
                        'id_tipo_servicio' => $request['sesionVenta'],
                        'precio' => $request['valorServSesion'],
                        'fecha' => $request['fechaVentaSesion'] . ' ' . $request['horaSeleccionadaVentaSesion'],
                        'tipo_servicio' => $request['tipoServicioSesion']
                    ]));
                    DB::table('ventas')->where('id_servicio', $request['idVentaSesion'])->update(array_filter([
                        'valor' => $request['valorServSesion'],
                        'total' => $request['valorServSesion'],
                        'saldo' => $request['valorServSesion'],
                    ]));
                    DB::commit();
                    return  $request['idVentaSesion'];
                } else {
                    $idSesionVenta = DB::table('servicios')->insertGetId(array_filter([
                        'id_tipo_servicio' => $request['sesionVenta'],
                        'tipo' => 'SESION',
                        'precio' => $request['valorServSesion'],
                        'estado' => 'ACTIVO',
                        'id_paciente' => $request['idPaciente'],
                        'fecha' => $request['fechaVentaSesion'] . ' ' . $request['horaSeleccionadaVentaSesion'],
                        'tipo_servicio' => $request['tipoServicioSesion']
                    ]));

                    $idVenta = DB::table('ventas')->insertGetId(array_filter([
                        'id_servicio' => $idSesionVenta,
                        'id_paciente' => $request['idPaciente'],
                        'usuario' => Auth::user()->id,
                        'valor' => $request['valorServSesion'],
                        'cantidad' => '1',
                        'total' => $request['valorServSesion'],
                        'estado_venta' => 'PENDIENTE',
                        'saldo' => $request['valorServSesion'],
                    ]));

                    // Confirmar transacción
                    DB::commit();
                    return  $idSesionVenta;
                }
            } catch (\Exception $e) {
                // Revertir transacción en caso de error
                DB::rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            // Manejo del error
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
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
                        'precio' => $request['montoFinal'],
                        'estado' => 'ACTIVO',
                        'fecha' => $request['fechaPaquete'] ,
                        'id_tipo_servicio' => $request['selPaquete'],
                        'id_paciente' => $request['idPaciente'],
                        'tipo_servicio' => $request['tipoServicioPaquete']
                    ]));

                    $idVenta = DB::table('ventas')->insertGetId(array_filter([
                        'id_servicio' => $idPaqueteVenta,
                        'id_paciente' => $request['idPaciente'],
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
                        'id_tipo_servicio' => $request['selPaquete'],
                        'precio' => $request['montoFinal'],
                        'fecha' => $request['fechaPaquete'],
                        'tipo_servicio' => $request['tipoServicioPaquete']
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
}
