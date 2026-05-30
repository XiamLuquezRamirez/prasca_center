<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Profesional;

class ProfesionalController extends Controller
{
    public function Profesionales()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarProfesionales', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaProfesionales(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10;
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1;
            }

            $profesionales = DB::connection('mysql')
                ->table('profesionales')
                ->where('estado', 'ACTIVO')
                ->select('identificacion', 'nombre', 'correo', 'id');

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
                                    <td>' . e($item->identificacion) . '</td>
                                    <td>' . e($item->nombre) . '</td>
                                    <td>' . e($item->correo) . '</td>
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

    public function guardarProfesional(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $request->validate([
                'accRegistro'    => 'required|in:guardar,actualizar',
                'nombre'         => 'required|string|max:255',
                'identificacion' => 'required|string|max:20',
                'email'          => 'required|email|max:255',
                'tipo'           => 'required|string',
                'estado'         => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();

        if (isset($data['firmaProf'])) {
            $archivo = $data['firmaProf'];
            $nombreOriginal = $archivo->getClientOriginalName();

            $prefijo = substr(md5(uniqid(rand())), 0, 6);
            $nombreArchivo = self::sanear_string($prefijo . '_' . $nombreOriginal);

            $archivo->move(public_path() . '/app-assets/images/firmasProfesionales/', $nombreArchivo);
            $data['firma'] = $nombreArchivo;
        } else {
            if ($data['accRegistro'] == 'guardar') {
                $data['firma'] = "sinFima.jpg";
            } else {
                $data['firma'] = $data['firmaOriginal'];
            }
        }

        $respuesta = Profesional::guardar($data);

        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }

        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' => 'Datos guardados'
        ]);
    }

    public function cargarListaProf()
    {
        $profesionales = DB::connection('mysql')
            ->table('profesionales')
            ->where('estado', 'ACTIVO')
            ->get();
        return response()->json($profesionales);
    }

    public function verificarIdentProfesional(Request $request)
    {
        $identificacion = $request->input('identificacion');
        $profesionalExistente = DB::table('profesionales')
            ->where('identificacion', $identificacion)
            ->exists();

        return response()->json(!$profesionalExistente);
    }

    public function busquedaProfesional(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $profesional = Profesional::busquedaProfesional($idRegistro);
        return response()->json($profesional);
    }

    public function eliminarProfesional()
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
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
                        'message' => 'Profesional eliminado correctamente'
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

    private function sanear_string($string)
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
