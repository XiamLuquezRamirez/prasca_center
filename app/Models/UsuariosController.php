<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\Profesional;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\Perfil;
use App\Models\Permisos;

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
            
            // Regenerar el ID de sesión para prevenir session fixation
            Session::regenerate();
            
            return redirect('Administracion');
        } else {
            $error = "Usuario ó Contraseña Incorrecta";
            return redirect('/')->with('error', $error);
        }
    }

    public function UpdatePerfil()
    {
        if (Auth::check()) {
            $data = request()->all();
            if (isset($data['fotoUsuario'])) {
                $archivo = $data['fotoUsuario'];
                $nombreOriginal = $archivo->getClientOriginalName();
                $tipoMime = $archivo->getClientMimeType();

                $prefijo = substr(md5(uniqid(rand())), 0, 6);
                $nombreArchivo = self::sanear_string($prefijo . '_' . $nombreOriginal);
                $archivo->move(public_path() . '/app-assets/images/FotosUsuarios/', $nombreArchivo);
                $data['img'] = $nombreArchivo;
            } else {
                $data['img'] = $data['fotoCargada'];
            }

            $perfil = Usuario::cambiosPerfil($data);
            if (request()->ajax()) {
                return response()->json([
                    'estado' => "ok",
                ]);
            }
            
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function VerificarUsuarioPerfil()
    {
        $usu = request()->get('Usuario');
        $usuario = Usuario::verifUsuario($usu);

        if (request()->ajax()) {
            return response()->json([
                'usuario' => $usuario->count(),
            ]);
        }
    }


    public function perfil()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.GestionarPerfil', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function busquedaUsuario(Request $request)
    {
        $idUsuario = $request->input('idUsuario');
        $usuario = Usuario::busquedaUsuario($idUsuario);
        return response()->json($usuario);
    }

    public function buscaPerfil(Request $request)
    {
        $idPerfil = $request->input('idPerfil');
        $perfil = Usuario::busquedaPerfil($idPerfil);
        return response()->json($perfil);
    }

    public function buscaListPerfiles()
    {
        $perfiles = Usuario::listPerfiles();
        return response()->json($perfiles);
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

    public function eliminarPerfil()
    {
        try {
            $idPerfil = request()->input('idPerfil');


            if (!$idPerfil) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de usuario no proporcionado'
                    ],
                    400
                );
            }

            //consultar si el perfil esta relacionado con algun usuario si lo esta regresar mensaje que el perfil no se puede eliminar
            $usuarios = DB::connection('mysql')
                ->table('users')
                ->where('tipo_usuario', $idPerfil)
                ->where('estado', 'ACTIVO')
                ->get();

            if (count($usuarios) > 0) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'El perfil no se puede eliminar porque está relacionado con uno o más usuarios',
                        'resp' => "relacionado"
                    ],
                    400
                );
            }

            $usuario = DB::connection('mysql')
                ->table('perfiles')
                ->where('id', $idPerfil)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($usuario) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Usuario eliminado correctamente',
                        'resp' => "eliminado"
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el usuario o no se pudo eliminar',
                        'resp' => "no encontrado"
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
    public function guardarPerfil(Request $request)
    {
        if (Auth::check()) {

            // Insertar el perfil en la tabla 'perfiles' y obtener su ID
            if ($request->input('accPerfil') == "guardar") {
                $perfilId = DB::connection('mysql')->table('perfiles')->insertGetId([
                    'nombre' => $request->input('nombrePerfil'),
                    'estado' => 'ACTIVO'
                ]);
            } else {
                $perfilId = $request->input('idPerfil');
                DB::connection('mysql')->table('perfiles')
                    ->where('id', $perfilId)
                    ->update([
                        'nombre' => $request->input('nombrePerfil')
                    ]);
                //eliminar permisos del perfil
                DB::connection('mysql')->table('perfil_permiso')
                    ->where('perfil_id', $perfilId)
                    ->delete();
            }


            // Procesar los permisos seleccionados
            if ($request->has('permisos') && !empty($request->input('permisos'))) {
                $permisosSeleccionados = $request->input('permisos');

                // Decodificar si los permisos llegan como cadena JSON
                if (is_string($permisosSeleccionados)) {
                    $permisosSeleccionados = json_decode($permisosSeleccionados, true);
                }

                // Verificar que sea un array válido
                if (!is_array($permisosSeleccionados)) {
                    return response()->json([
                        'message' => 'Formato de permisos inválido.',
                    ], 400);
                }

                // Insertar los permisos en la tabla pivote
                foreach ($permisosSeleccionados as $permiso) {
                    DB::connection('mysql')->table('perfil_permiso')->insert([
                        'perfil_id' => $perfilId,
                        'permiso' => $permiso
                    ]);
                }
            }

            // Retornar respuesta exitosa
            return response()->json([
                'message' => 'Perfil y permisos guardados con éxito.',
                'perfil_id' => $perfilId,
            ]);
        } else {
            // Redirigir si la sesión ha expirado
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
                ->leftJoin('perfiles', 'perfiles.id','users.tipo_usuario')
                ->select('users.id','users.nombre_usuario','perfiles.nombre','users.estado_usuario','users.login_usuario')
                ->where('users.estado', 'ACTIVO');

            if ($search) {
                $usuarios->where(function ($query) use ($search) {
                    $query->where('users.nombre_usuario', 'LIKE', '%' . $search . '%')
                        ->orWhere('users.login_usuario', 'LIKE', '%' . $search . '%');
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
                                    <td>' . $item->nombre . '</td>
                                    <td><span class="badge badge-sm badge-' . $estadoClass . '-light">' . $item->estado_usuario . '</span></td>
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

    public function listaPerfiles(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $perfiles = DB::connection('mysql')
                ->table('perfiles')
                ->where('estado', 'ACTIVO');

            if ($search) {
                $perfiles->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPerfiles = $perfiles->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListPerfiles as $i => $item) {
                if (!is_null($item)) {

                    $tdTable .= '<tr>
                                    <td>' . $item->nombre . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarPerfil(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarPerfil(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListPerfiles->links('Usuario.paginacionUsuarios')->render();

            return response()->json([
                'perfiles' => $tdTable,
                'links' => $pagination,
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function listaLogs(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $losg = DB::connection('mysql')
            ->table('logs')
            ->leftJoin('users', 'users.id', 'logs.user_id')
            ->where(function($query) {
                $query->where('logs.detalles', '!=', '[]')  // Excluir el arreglo vacío
                      ->orWhereNull('logs.detalles');  // Incluir registros sin detalles
            });

            if ($search) {
                $losg->where(function ($query) use ($search) {
                    $query->where('users.nombre_usuario', 'LIKE', '%' . $search . '%');
                });
            }

            $ListLogs= $losg->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListLogs as $i => $item) {
                if (!is_null($item)) {

                    $tdTable .= '<tr>
                                    <td>' . $item->nombre_usuario . '</td>                                   
                                    <td>' . $item->accion . '</td>                                   
                                    <td>' . $item->ip . '</td>                                   
                                    <td>' . $item->detalles. '</td>                                   
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListLogs->links('Usuario.paginacionUsuarios')->render();

            return response()->json([
                'logs' => $tdTable,
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

    public function sanear_string($string)
    {

        $string = trim($string);

        $string = str_replace(
            array('á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'),
            array('a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'),
            $string
        );

        $string = str_replace(
            array('é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'),
            array('e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'),
            $string
        );

        $string = str_replace(
            array('í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'),
            array('i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'),
            $string
        );

        $string = str_replace(
            array('ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'),
            array('o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'),
            $string
        );

        $string = str_replace(
            array('ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'),
            array('u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'),
            $string
        );

        $string = str_replace(
            array('ñ', 'Ñ', 'ç', 'Ç'),
            array('n', 'N', 'c', 'C'),
            $string
        );

        //Esta parte se encarga de eliminar cualquier caracter extraño
        $string = str_replace(
            array(
                "¨", "º", "-", "~", "", "@", "|", "!",
                "·", "$", "%", "&", "/",
                "(", ")", "?", "'", " h¡",
                "¿", "[", "^", "<code>", "]",
                "+", "}", "{", "¨", "´",
                ">", "< ", ";", ",", ":",
                " ",
            ),
            '',
            $string
        );

        return $string;
    }

}
