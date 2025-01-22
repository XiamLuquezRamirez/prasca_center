<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Auth;

class Usuario extends Model
{
    public static function login($request)
{
    // Obtén el usuario utilizando el modelo Eloquent
    $usuario = \App\Models\User::where('login_usuario', $request['usuario'])
        ->where('estado', 'ACTIVO')
        ->first();

    if ($usuario && \Hash::check($request['pasword'], $usuario->pasword_usuario)) {
        auth()->loginUsingId($usuario->id);

        // Obtener el perfil del usuario
        $perfilId = $usuario->tipo_usuario;

        // Obtener los permisos asociados al perfil
        $permisos = DB::connection('mysql')
            ->table('perfil_permiso')
            ->where('perfil_id', $perfilId)
            ->pluck('permiso') // Devuelve una lista con los nombres de los permisos
            ->toArray();

        // Almacenar los permisos en la sesión
        session(['permisos' => $permisos]);

        // Asigna los permisos al modelo del usuario
        $usuario->permisos = $permisos;

        return $usuario;
    }

    return false;
}


    public static function busquedaUsuario($idUsu)
    {
        return DB::connection('mysql')->table('users')
            ->where("id", $idUsu)
            ->first();
    }

    public static function listPerfiles()
    {
        return DB::connection('mysql')->table('perfiles')
        ->where('estado', 'ACTIVO')
            ->get();
    }

    public static function busquedaPerfil($idUsu)
    {
        $perfil = DB::connection('mysql')->table('perfiles')
            ->where("id", $idUsu)
            ->first();

            // consulta tabla de permisos y agregar al perfil
            $permisos = DB::connection('mysql')->table('perfil_permiso')
            ->where("perfil_id",  $perfil->id)
            ->get();

            $perfil->permisos = $permisos;


        return $perfil;
    }

    public static function Guardar($request)
    {
        try {
            if ($request['accUsuario'] == 'guardar') {
                $respuesta = DB::connection('mysql')->table('users')->insertGetId([
                    'nombre_usuario' => $request['nombre'],
                    'login_usuario' => $request['usuario'],
                    'pasword_usuario' => bcrypt($request['pasw']),
                    'tipo_usuario' => $request['tipo'],
                    'email_usuario' => $request['email'],
                    'estado_usuario' => $request['estado_usuario'],
                    'foto_usuario' => 'avatar-13.png',
                    'estado' => 'ACTIVO'
                ]);
            } else {

                $updateData = [
                    'nombre_usuario' => $request['nombre'],
                    'login_usuario' => $request['usuario'],
                    'tipo_usuario' => $request['tipo'],
                    'email_usuario' => $request['email'],
                    'estado_usuario' => $request['estado_usuario']
                ];

                if (isset($request['pasw'])) {
                    $updateData['password_usuario'] = bcrypt($request['pasw']);
                }

                $respuesta = DB::connection('mysql')->table('users')
                    ->where('id', $request['idUsuario'])
                    ->update($updateData);

                $respuesta = $request['idUsuario'];
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ocurrió un error al procesar el formulario: ' . $e->getMessage(),
            ], 500);
        }
        return  $respuesta;
    }
}
