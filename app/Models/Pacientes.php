<?php

namespace App\Models;


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

    public static function recaudoCaja($fecIni){
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

    public static function Guardar($request)
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
                    'completo' => true
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
                        'estado' => 'ACTIVO'
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
    public static function guardarPend($request)
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
}
