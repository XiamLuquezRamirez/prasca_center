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
    public static function listDepartamentos()
    {
        return DB::connection('mysql')->table('departamentos')
            ->where('habilitado', 'SI')
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
            $fechaFormateada = date("Y-m-d", strtotime(str_replace('/', '-', $request['fechaNacimiento'])));
            if ($request['accPacientes'] == 'guardar') {
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
                    'estado' => 'ACTIVO',
                    'completo' => true
                ]);

            } else {
            $fechaFormateada = date("Y-m-d", strtotime(str_replace('/', '-', $request['fechaNacimiento'])));

                $respuesta = DB::connection('mysql')->table('pacientes')
                ->where('id', $request['idPaciente'])  // Identificar el registro a actualizar
                ->update([
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
                    'estado' => 'ACTIVO'
                   
                ]);

                $respuesta = $request['idPaciente'];
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

    
    public static function busquedaPaciente($idPac){
        return DB::connection('mysql')->table('pacientes')
        ->where("id", $idPac)
        ->first();
    }

}
