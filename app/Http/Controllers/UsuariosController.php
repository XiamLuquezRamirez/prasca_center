<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Profesional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class UsuariosController extends Controller
{
    public function Login()
    {
        $respuesta = Usuario::login(request()->all());

        if ($respuesta) {

            $profesional = Profesional::busquedaProfesionalUsu(Auth::id());

            if ($profesional) {
                Session::put('identificacionProfesional', $profesional->identificacion);
                Session::put('nombreProfesional', $profesional->nombre);
                Session::put('registroProfesional', $profesional->registro);
                Session::put('firmaProfesional', $profesional->firma);
            }
            return redirect('Administracion');
        } else {
            $error = "Usuario ó Contraseña Inconrrecta";
            return redirect('/')->with('error', $error);
        }
    }

    public function busquedaUsuario(Request $request)
    {
        $idUsuario = $request->input('idUsuario');
        $fauna = Usuario::busquedaUsuario($idUsuario);
        return response()->json($fauna);
    }

    public function Administracion()
    {
        if (Auth::check()) {
            return view('inicio');
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function eliminarUsuario()
    {
        try {
            $idUsuario = request()->input('idUsuario');


            if (!$idUsuario) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de usuario no proporcionado'
                    ],
                    400
                );
            }

            $usuario = DB::connection('mysql')
                ->table('users')
                ->where('id', $idUsuario)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($usuario) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Usuario eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el usuario o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el usuario',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function guardarUsuario(Request $request)
    {

        if (Auth::check()) {
            $data = $request->all();

            $usuario = Usuario::Guardar($data);
            if ($usuario) {
                return response()->json(
                    [
                        'success' => true
                    ]
                );
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaUsuarios(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $usuarios = DB::connection('mysql')
                ->table('users')
                ->where('estado', 'ACTIVO');

            if ($search) {
                $usuarios->where(function ($query) use ($search) {
                    $query->where('nombre_usuario', 'LIKE', '%' . $search . '%')
                        ->orWhere('login_usuario', 'LIKE', '%' . $search . '%');
                });
            }

            $ListUsuarios = $usuarios->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListUsuarios as $i => $item) {
                if (!is_null($item)) {

                    $estadoClass =  $item->estado_usuario == 'Habilitada' ? 'success' : 'danger';

                    $tdTable .= '<tr>
                                    <td>' . $item->nombre_usuario . '</td>
                                    <td>' . $item->login_usuario . '</td>
                                    <td>' . $item->tipo_usuario . '</td>
                                    <td><span class="badge badge-sm badge-'.$estadoClass.'-light">'.$item->estado_usuario.'</span></td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarUsuario(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarUsuario(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListUsuarios->links('Usuario.paginacionUsuarios')->render();

            return response()->json([
                'usuarios' => $tdTable,
                'links' => $pagination,
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    

    public function Logout()
    {
        Auth::logout();
        return redirect("/")->with("error", "Su Sesión ha Terminado");
    }

    public function verificarUsuario(Request $request)
    {
        $usuario = $request->input('usuario');
        $usuarioOriginal = $request->input('usuarioOriginal');

        // Si el usuario es el mismo que el original, no hay problema
        if ($usuario === $usuarioOriginal) {
            return response()->json(true); // El usuario es válido porque no ha cambiado
        }

        // Verificar si el usuario ya está registrado
        $usuarioExistente = DB::table('users')
            ->where('login_usuario', $usuario)
            ->where('estado', 'ACTIVO')
            ->exists();

        return response()->json(!$usuarioExistente); // Devuelve true si NO existe, false si ya está registrado
    }

}
