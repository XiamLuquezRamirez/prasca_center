<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;

class Profesional extends Model
{
    public static function Guardar($request)
    {

        try {
            if ($request['accRegistro'] == 'guardar') {

                $usuarioId = DB::connection('mysql')->table('users')->insertGetId([
                    'nombre_usuario' => $request['nombre'],
                    'login_usuario' => $request['usuario'],
                    'pasword_usuario' => password_hash($request['pasword'], PASSWORD_DEFAULT),
                    'tipo_usuario' => 'Profesional',
                    'email_usuario' => $request['email'],
                    'foto_usuario' => 'avatar-13.png',
                    'estado_usuario' => $request['estado'],                  
                    'estado' => 'ACTIVO'
                ]);

                $respuesta = DB::connection('mysql')->table('profesionales')->insertGetId([
                    'identificacion' => $request['identificacion'],
                    'nombre' => $request['nombre'],
                    'correo' => $request['email'],
                    'celular' => $request['telefono'] ?? '',
                    'observaciones' => $request['observaciones'] ?? '',
                    'usuario' => $usuarioId,
                    'registro' => $request['registroProf'],
                    'firma' => $request['firma'],                    
                    'estado' => 'ACTIVO'
                ]);
            } else {
                if ($request['pasword'] != "") {
                    $respuesta = DB::connection('mysql')->table('users')
                        ->where('login_usuario', $request['usuarioOriginal'])  // Identificar el registro a actualizar
                        ->update([
                            'nombre_usuario' => $request['nombre'],
                            'login_usuario' => $request['usuario'],
                            'pasword_usuario' => password_hash($request['pasword'], PASSWORD_DEFAULT),
                            'email_usuario' => $request['email'],
                            'estado_usuario' => $request['estado'],
                        ]);
                } else {
                    $respuesta = DB::connection('mysql')->table('users')
                        ->where('login_usuario', $request['usuarioOriginal'])  // Identificar el registro a actualizar
                        ->update([
                            'nombre_usuario' => $request['nombre'],
                            'login_usuario' => $request['usuario'],
                            'email_usuario' => $request['email'],
                            'estado_usuario' => $request['estado'],
                        ]);
                }



                $respuesta = DB::connection('mysql')->table('profesionales')
                    ->where('id', $request['idRegistro'])  // Identificar el registro a actualizar
                    ->update([
                        'nombre' => $request['nombre'],
                        'correo' => $request['email'],
                        'celular' => $request['telefono'] ?? '',
                        'observaciones' => $request['observaciones'] ?? '',
                        'registro' => $request['registroProf'],
                        'firma' => $request['firma']
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


    public static function busquedaProfesional($id)
    {
        $profesional =  DB::connection('mysql')->table('profesionales')
            ->join("users", "users.id", "profesionales.usuario")
            ->where("profesionales.id", $id)
            ->select("profesionales.*", "users.login_usuario", "users.estado_usuario", "users.id as idUsuario")
            ->first();

        return $profesional;
    }
    public static function busquedaProfesionalHitoria($usu)
    {
        $profesional =  DB::connection('mysql')->table('profesionales')
            ->join("users", "users.id", "profesionales.usuario")
            ->where("profesionales.usuario", $usu)
            ->select("profesionales.*", "users.login_usuario", "users.estado_usuario", "users.id as idUsuario")
            ->first();

        return $profesional;
    }
    public static function busquedaProfesionalUsu($usu)
    {
        $profesional = DB::connection('mysql')->table('profesionales')
            ->join("users", "users.id", "profesionales.usuario")
            ->where("profesionales.usuario", $usu)
            ->select("profesionales.*", "users.login_usuario", "users.estado_usuario", "users.id as idUsuario")
            ->first();

        return $profesional;
    }
}
