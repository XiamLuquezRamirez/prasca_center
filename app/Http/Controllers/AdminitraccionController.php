<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Especialidades;
use App\Models\Profesional;


class AdminitraccionController extends Controller
{
    public function Especialidades()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarEspecialidades', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function Profesionales()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarProfesionales', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function Usuarios()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Usuario.gestionarUsuarios', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

  
    public function  guardarEspecialidad(Request $request){
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();  
        // Guardar la información del paciente
        $respuesta = Especialidades::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }
    public function  guardarProfesional(Request $request){
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();

        if (isset($data['firmaProf'])) {
            $archivo = $data['firmaProf'];
            $nombreOriginal = $archivo->getClientOriginalName();

            // Generar un nombre único para el archivo
            $prefijo = substr(md5(uniqid(rand())), 0, 6);
            $nombreArchivo = self::sanear_string($prefijo . '_' . $nombreOriginal);

            // Guardar el archivo en la ruta especificada
            $archivo->move(public_path() . '/app-assets/images/firmasProfesionales/', $nombreArchivo);
            $data['firma'] = $nombreArchivo;
        } else {
            if ($data['accRegistro'] == 'guardar') {
                $data['firma'] = "sinFima.jpg";
            } else {
                $data['firma'] = $data['firmaCargada'];
            }
        }

        // Guardar la información del paciente
        $respuesta = Profesional::guardar($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function listaEspecialidades(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $especialidades = DB::connection('mysql')
            ->table('especialidades')
            ->where('estado', 'ACTIVO')
            ->select('nombre', 
                'id' );
        
            if ($search) {
                $especialidades->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
              });
            }

            $ListEspecialidades = $especialidades->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListEspecialidades as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . $const . '</td>
                                    <td>' . $item->nombre . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListEspecialidades->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'especialidades' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaProfesionales(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $profesionales = DB::connection('mysql')
            ->table('profesionales')
            ->where('estado', 'ACTIVO')
            ->select('identificacion','nombre', 'correo', 'id');
        
            if ($search) {
                $profesionales->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
              });
            }

            $ListProfesionales = $profesionales->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListProfesionales as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . $const . '</td>
                                    <td>' . $item->identificacion . '</td>
                                    <td>' . $item->nombre . '</td>
                                    <td>' . $item->correo . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }

            $pagination = $ListProfesionales->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'profesionales' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function cargarListaProf(){
        $profesionales = DB::connection('mysql')
        ->table('profesionales')
        ->where('estado', 'ACTIVO')
        ->get();
        return response()->json($profesionales);
    }
    public function cargarListaEsp(){
        $especialidades = DB::connection('mysql')
        ->table('especialidades')
        ->where('estado', 'ACTIVO')
        ->get();
        return response()->json($especialidades);
    }
  

    public function verificarIdentProfesional(Request $request){
        $identificacion = $request->input('identificacion');
        // Verificar si el usuario ya está registrado
        $profesionalExistente = DB::table('profesionales')
            ->where('identificacion', $identificacion)
            ->exists();

        return response()->json(!$profesionalExistente);
    }

    public function busquedaEspecialidad(Request $request){
        $idRegistro = $request->input('idRegistro');
        $especialidad = Especialidades::busquedaEspecialidad($idRegistro);
        return response()->json($especialidad);
    }

    public function busquedaProfesional(Request $request){
        $idRegistro = $request->input('idRegistro');
        $profesional = Profesional::busquedaProfesional($idRegistro);
        return response()->json($profesional);
    }

    public function eliminarEspecialidad()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la especialidad no proporcionado'
                    ],
                    400
                );
            }

         
            $paciente = DB::connection('mysql')
                ->table('especialidades')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

              if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Profesinal eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el profesional o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el profesional',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
    public function eliminarProfesional()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del profesional no proporcionado'
                    ],
                    400
                );
            }

            
            $profesional = Profesional::busquedaProfesional($idReg);
         
            $usuario = DB::connection('mysql')
            ->table('users')
            ->where('id', $profesional->idUsuario)
            ->update([
                'estado' => 'ELIMINADO',
            ]); 

            $paciente = DB::connection('mysql')
                ->table('profesionales')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Especialidad eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la especialidad o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la especialidad',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
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
                "¨",
                "º",
                "-",
                "~",
                "",
                "@",
                "|",
                "!",
                "·",
                "$",
                "%",
                "&",
                "/",
                "(",
                ")",
                "?",
                "'",
                " h¡",
                "¿",
                "[",
                "^",
                "<code>",
                "]",
                "+",
                "}",
                "{",
                "¨",
                "´",
                ">",
                "< ",
                ";",
                ",",
                ":",
                " ",
            ),
            '',
            $string
        );

        return $string;
    }
}
