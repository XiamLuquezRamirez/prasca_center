<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Especialidades;
use App\Models\Entidades;
use App\Models\CUPS;
use App\Models\CIE10;
use App\Models\Componentes;

class CatalogoController extends Controller
{
    // ==================== ESPECIALIDADES ====================

    public function Especialidades()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarEspecialidades', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
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
                ->select(
                    'nombre',
                    'precio',
                    'id'
                );

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
                    $valor = number_format($item->precio, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $const . '</td>
                                    <td>' . e($item->nombre) . '</td>
                                    <td>$' .  $valor . '</td>
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

    public function guardarEspecialidad(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $request->validate([
                'accRegistro' => 'required|in:guardar,actualizar',
                'nombre'      => 'required|string|max:255',
                'valor'       => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

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

    public function busquedaEspecialidad(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $especialidad = Especialidades::busquedaEspecialidad($idRegistro);
        return response()->json($especialidad);
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

    public function cargarListaEsp()
    {
        $especialidades = DB::connection('mysql')
            ->table('especialidades')
            ->where('estado', 'ACTIVO')
            ->get();
        return response()->json($especialidades);
    }

    // ==================== CUPS ====================

    public function CUPS()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarCUPS', compact('bandera'));
        }
    }

    public function listaCUPS(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $cups = DB::connection('mysql')
                ->table('referencia_cups')
                ->where('estado', 'ACTIVO')
                ->select(
                    'nombre',
                    'id',
                    'codigo',
                    'habilitado'
                );

            if ($search) {
                $cups->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListCUPS = $cups->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListCUPS as $i => $item) {
                if (!is_null($item)) {
                    if ($item->habilitado == 'SI') {
                        $habilitado = '<span class="badge bg-success">Habilitado</span>';
                    } else {
                        $habilitado = '<span class="badge bg-danger">Deshabilitado</span>';
                    }
                    $tdTable .= '<tr>
                                    <td>' . e($item->codigo) . '</td>
                                    <td>' . e($item->nombre) . '</td>
                                    <td>' . $habilitado . '</td>
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
            $pagination = $ListCUPS->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'cups' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function guardarCUPS(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $request->validate([
                'accRegistro' => 'required|in:guardar,actualizar',
                'codigo'      => 'required|string|max:20',
                'nombre'      => 'required|string|max:255',
                'habilitado'  => 'required',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = CUPS::guardar($data);

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

    public function buscaCUPS(Request $request)
    {
        $id = $request->input('idRegistro');
        $cups = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('id', $id)
            ->first();

        return response()->json($cups);
    }

    public function eliminarCUPS(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        $idReg = $request->input('idReg');
        $cups = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('id', $idReg)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'CUPS eliminada correctamente'
        ]);
    }

    public function verificarCodigoCUPS(Request $request)
    {
        $codigo = $request->input('codigo');
        $id = $request->input('idRegistro');
        $cupsExistente = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('id', '!=', $id)
            ->where('codigo', $codigo)
            ->exists();

        return response()->json(!$cupsExistente);
    }

    // ==================== SERVICIOS HABILITADOS (cups_servicio_habilitado) ====================

    public function listaServiciosHabilitados(Request $request)
    {
        if (!Auth::check()) return redirect('/')->with('error', 'Su Sesión ha Terminado');

        $perPage = 10;
        $page    = is_numeric($request->get('page', 1)) ? $request->get('page', 1) : 1;
        $search  = $request->get('search', '');

        $query = DB::table('cups_servicio_habilitado');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('codigo_cups', 'LIKE', "%{$search}%")
                  ->orWhere('nombre_servicio', 'LIKE', "%{$search}%");
            });
        }

        $registros = $query->orderBy('codigo_cups')->paginate($perPage, ['*'], 'page', $page);

        $grupos = ['01' => 'Consulta externa', '02' => 'Urgencias', '03' => 'Hospitalización',
                   '04' => 'Procedimientos quirúrgicos', '05' => 'Apoyo diagnóstico'];
        $modalidades = ['01' => 'Intramural', '02' => 'Extramural', '03' => 'Unidad móvil',
                        '04' => 'Domiciliaria', '05' => 'Telesalud'];

        $tdTable = '';
        foreach ($registros as $item) {
            $grupo    = $grupos[$item->grupo_servicio]    ?? $item->grupo_servicio;
            $modalidad = $modalidades[$item->modalidad]   ?? $item->modalidad;
            $estado   = $item->activo
                ? '<span class="badge bg-success">Activo</span>'
                : '<span class="badge bg-danger">Inactivo</span>';
            $tdTable .= '<tr>
                <td>' . e($item->codigo_cups) . '</td>
                <td>' . e($item->codigo_servicio) . '</td>
                <td>' . e($item->nombre_servicio) . '</td>
                <td>' . e($grupo) . '</td>
                <td>' . e($modalidad) . '</td>
                <td>' . $estado . '</td>
                <td class="table-action min-w-100">
                    <a onclick="editarServicioHabilitado(' . $item->id . ');" style="cursor:pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle" data-feather="edit-2"></i></a>
                    <a onclick="eliminarServicioHabilitado(' . $item->id . ');" style="cursor:pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle" data-feather="trash"></i></a>
                </td>
            </tr>';
        }

        return response()->json([
            'registros' => $tdTable,
            'links'     => $registros->links('Adminitraccion.Paginacion')->render(),
        ]);
    }

    public function guardarServicioHabilitado(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);

        try {
            $request->validate([
                'codigo_cups'      => 'required|string|max:10',
                'codigo_servicio'  => 'required|integer',
                'nombre_servicio'  => 'required|string|max:200',
                'grupo_servicio'   => 'required|string|max:2',
                'modalidad'        => 'required|string|max:2',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $acc = $request->input('accRegistroSH', 'guardar');
        $id  = $request->input('idRegistroSH');

        if ($acc === 'editar' && $id) {
            DB::table('cups_servicio_habilitado')->where('id', $id)->update([
                'codigo_cups'     => $request->codigo_cups,
                'codigo_servicio' => $request->codigo_servicio,
                'nombre_servicio' => $request->nombre_servicio,
                'grupo_servicio'  => $request->grupo_servicio,
                'modalidad'       => $request->modalidad,
                'activo'          => $request->boolean('activo', true) ? 1 : 0,
            ]);
        } else {
            DB::table('cups_servicio_habilitado')->insert([
                'codigo_cups'     => $request->codigo_cups,
                'codigo_servicio' => $request->codigo_servicio,
                'nombre_servicio' => $request->nombre_servicio,
                'grupo_servicio'  => $request->grupo_servicio,
                'modalidad'       => $request->modalidad,
                'activo'          => 1,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Guardado correctamente']);
    }

    public function buscarServicioHabilitado(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);
        $registro = DB::table('cups_servicio_habilitado')->where('id', $request->input('idRegistroSH'))->first();
        return response()->json($registro);
    }

    public function eliminarServicioHabilitado(Request $request)
    {
        if (!Auth::check()) return response()->json(['success' => false], 401);
        DB::table('cups_servicio_habilitado')->where('id', $request->input('idReg'))->delete();
        return response()->json(['success' => true, 'message' => 'Eliminado correctamente']);
    }

    public function getServicioHabilitadoPorCUPS(Request $request)
    {
        $idCups = $request->get('codigo_cups');

        $codigoCups = DB::table('referencia_cups')
            ->where('id', $idCups)
            ->where('estado', 'ACTIVO')
            ->first();

        $registro = DB::table('cups_servicio_habilitado')
            ->where('codigo_cups', $codigoCups->codigo)
            ->where('activo', 1)
            ->first();
            

        if (!$registro) return response()->json(['found' => false]);

        return response()->json([
            'found'            => true,
            'codigo_servicio'  => $registro->codigo_servicio,
            'nombre_servicio'  => $registro->nombre_servicio,
            'grupo_servicio'   => $registro->grupo_servicio,
            'modalidad'        => $registro->modalidad,
        ]);
    }

    // ==================== CIE10 ====================

    public function CIE10()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarCIE10', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaCIE10(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $cie10 = DB::connection('mysql')
                ->table('referencia_cie10')
                ->where('estado', 'ACTIVO')
                ->select(
                    'nombre',
                    'id',
                    'codigo',
                    'clasificacion',
                    'habilitado'
                );

            if ($search) {
                $cie10->where(function ($query) use ($search) {
                    $query->where('nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListCIE10 = $cie10->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListCIE10 as $i => $item) {
                if (!is_null($item)) {
                    if ($item->habilitado == 'SI') {
                        $habilitado = '<span class="badge bg-success">Habilitado</span>';
                    } else {
                        $habilitado = '<span class="badge bg-danger">Deshabilitado</span>';
                    }
                    $tdTable .= '<tr>
                                    <td>' . e($item->codigo) . '</td>
                                    <td>' . e($item->nombre) . '</td>
                                    <td>' . e($item->clasificacion) . '</td>
                                    <td>' . $habilitado . '</td>
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
            $pagination = $ListCIE10->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'cie10' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function guardarCIE10(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $request->validate([
                'accRegistro'   => 'required|in:guardar,actualizar',
                'codigo'        => 'required|string|max:20',
                'nombre'        => 'required|string|max:255',
                'habilitado'    => 'required',
                'clasificacion' => 'required|string|max:10',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = CIE10::guardar($data);

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

    public function buscaCIE10(Request $request)
    {
        $id = $request->input('idRegistro');
        $cie10 = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('id', $id)
            ->first();

        return response()->json($cie10);
    }

    public function eliminarCIE10(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 401);
        }
        $idReg = $request->input('idReg');
        $cie10 = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('id', $idReg)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'CIE10 eliminada correctamente'
        ]);
    }

    public function verificarCodigoCIE10(Request $request)
    {
        $codigo = $request->input('codigo');
        $id = $request->input('idRegistro');
        $cie10Existente = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('id', '!=', $id)
            ->where('codigo', $codigo)
            ->exists();

        return response()->json(!$cie10Existente);
    }

    // ==================== ENTIDADES ====================

    public function Entidades()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarEPS', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaEntidades(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $entidades = DB::connection('mysql')
                ->table('eps')
                ->where('estado', 'ACTIVO')
                ->select(
                    'entidad',
                    'id',
                    'codigo'
                );

            if ($search) {
                $entidades->where(function ($query) use ($search) {
                    $query->where('entidad', 'LIKE', '%' . $search . '%');
                });
            }

            $ListEntidades = $entidades->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListEntidades as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . e($item->codigo) . '</td>
                                    <td>' . e($item->entidad) . '</td>
                                    <td class="table-action min-w-100">
                                     <a href="javascript:void(0)" onclick="verServiciosVenta(' . $item->id . ');" style="cursor: pointer;" title="Venta de servicios" class="text-fade hover-info"><i class="align-middle"
                                    data-feather="shopping-cart"></i></a>
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
            $pagination = $ListEntidades->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'entidades' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function guardarEntidades(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $request->validate([
                'accRegistro' => 'required|in:guardar,actualizar',
                'nit'         => 'required|string|max:20',
                'codigo'      => 'required|string|max:20',
                'nombre'      => 'required|string|max:255',
                'email'       => 'required|email|max:255',
                'telefono'    => 'required|string|max:20',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Entidades::guardar($data);

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

    public function buscaEntidad(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $entidad = Entidades::busquedaEntidad($idRegistro);
        return response()->json($entidad);
    }

    public function eliminarEntidad()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la entidad no proporcionada'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('eps')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Entidad eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la entidad o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la entidad',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function verificarCodigoEntidad(Request $request)
    {
        $codigo = $request->input('codigo');
        $idEPS = $request->input('id');

        $pacienteExistente = DB::table('eps')
            ->where('codigo', $codigo)
            ->when($idEPS, function ($query) use ($idEPS) {

                $query->where('id', '!=', $idEPS);
            })
            ->exists();

        return response()->json(!$pacienteExistente); // Devuelve true si NO existe duplicado, false si ya está registrado

    }

    // ==================== COMPONENTES ====================

    public function Componentes()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarComponentes', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaComponentes(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $componentes = DB::connection('mysql')
                ->table('opciones_hc_psicologia')
                ->leftJoin('categorias_hc_psicologia', 'categorias_hc_psicologia.id', 'opciones_hc_psicologia.categoria_id')
                ->where('categorias_hc_psicologia.estado', 'ACTIVO')
                ->select(
                    'opciones_hc_psicologia.id',
                    'opciones_hc_psicologia.opcion',
                    'categorias_hc_psicologia.nombre'
                );

            if ($search) {
                $componentes->where(function ($query) use ($search) {
                    $query->where('opciones_hc_psicologia.opcion', 'LIKE', '%' . $search . '%')
                        ->orWhere('categorias_hc_psicologia.nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListComponentes = $componentes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            foreach ($ListComponentes as $i => $item) {
                if (!is_null($item)) {

                    $tdTable .= '<tr>
                                    <td>' . e($item->nombre) . '</td>
                                    <td>' . e($item->opcion) . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarRegistro(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;

                }
            }
            $pagination = $ListComponentes->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'componentes' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function guardarComponente(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $request->validate([
                'accRegistro' => 'required|in:guardar,actualizar',
                'componente'  => 'required|string|max:255',
                'categoria'   => 'required|integer',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Componentes::guardar($data);

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

    public function buscarComponente(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $componente = Componentes::buscarComponente($idRegistro);
        return response()->json($componente);
    }

    public function eliminarComponente()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del componente no proporcionado'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('opciones_hc_psicologia')
                ->where('id', $idReg)
                ->delete();

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Componente eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el componente o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el componente',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function listaCategoriasSelect()
    {
        $categorias = DB::connection('mysql')
            ->table('categorias_hc_psicologia')
            ->where('estado', 'ACTIVO')
            ->get();
        return response()->json($categorias);
    }
}
