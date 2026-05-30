<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Pruebas;
use App\Models\Sesiones;
use App\Models\Paquetes;
use App\Models\Asesorias;
use App\Models\Servicios;

class ServicioController extends Controller
{
    public function Pruebas()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarPruebas', compact('bandera'));
        }
    }

    public function listaPruebas(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $paquetes = DB::connection('mysql')
                ->table('pruebas')
                ->where('estado', 'ACTIVO')
                ->select(
                    'descripcion',
                    'id',
                    'precio'
                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    $query->where('descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td>$ ' . $valor . '</td>
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
            $pagination = $ListPaquetes->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'pruebas' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function  guardarPrueba(Request $request)
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
                'descripcion' => 'required|string|max:255',
                'valor'       => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Pruebas::guardar($data);

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

    public function buscarPrueba(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $prueba = Pruebas::busquedaPruebas($idRegistro);
        return response()->json($prueba);
    }

    public function eliminarPrueba()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la prueba no proporcionado'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('pruebas')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Pruea eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la prueba o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la prueba',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function Sesiones()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.GestionarSesiones', compact('bandera'));
        }
    }

    public function listaSesiones(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $sesiones = DB::connection('mysql')
                ->table('sesiones')
                ->where('estado', 'ACTIVO')
                ->select(
                    'descripcion',
                    'id',
                    'precio'
                );

            if ($search) {
                $sesiones->where(function ($query) use ($search) {
                    $query->where('descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListSesiones = $sesiones->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListSesiones as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td>$ ' . $valor . '</td>
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
            $pagination = $ListSesiones->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'sesiones' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function  guardarSesion(Request $request)
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
                'descripcion' => 'required|string|max:255',
                'valor'       => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Sesiones::guardar($data);

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

    public function buscarSesion(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $sesion = Sesiones::busquedaSesiones($idRegistro);
        return response()->json($sesion);
    }

    public function eliminarSesion()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la sesión no proporcionado'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('sesiones')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Sesión eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la sesión o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la sesión',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function Paquetes()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarPaquetes', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaPaquetes(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $paquetes = DB::connection('mysql')
                ->table('paquetes')
                ->where('estado', 'ACTIVO')
                ->select(
                    'descripcion',
                    'id',
                    'precio_por_sesion'
                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    $query->where('descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio_por_sesion, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td>$ ' . $valor . '</td>
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
            $pagination = $ListPaquetes->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'paquetes' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function  guardarPaquete(Request $request)
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
                'descripcion' => 'required|string|max:255',
                'valor'       => 'required|numeric|min:0',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Paquetes::Guardar($data);

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

    public function buscarPaquete(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $paquete = Paquetes::busquedaPaquetes($idRegistro);
        return response()->json($paquete);
    }

    public function eliminarPaquete()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del paquete no proporcionado'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('paquetes')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Paquete eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró el paquete o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar el paquete',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function Asesorias()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarAsesorias', compact('bandera'));
        }
    }

    public function AsesoriasList()
    {
        $asesorias = Asesorias::listaAsesorias();
        return response()->json($asesorias);
    }

    public function listaAsesorias(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $asesorias = DB::connection('mysql')
                ->table('asesorias')
                ->where('estado', 'ACTIVO')
                ->select(
                    'descripcion',
                    'id',
                    'valor',
                    'tiempo'
                );

            if ($search) {
                $asesorias->where(function ($query) use ($search) {
                    $query->where('descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListAsesorias = $asesorias->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListAsesorias as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->valor, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>' . $item->descripcion . '</td>
                                    <td>$ ' . $valor . '</td>
                                    <td>' . $item->tiempo . '</td>
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
            $pagination = $ListAsesorias->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'asesorias' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function  guardarAsesoria(Request $request)
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
                'descripcion' => 'required|string|max:255',
                'valor'       => 'required|numeric|min:0',
                'tiempo'      => 'required|string|max:50',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'errors' => $e->errors()], 422);
        }

        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Asesorias::Guardar($data);

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

    public function buscarAsesoria(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $asesoria = Asesorias::busquedaAsesorias($idRegistro);
        return response()->json($asesoria);
    }

    public function eliminarAsesoria()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la asesoria no proporcionado'
                    ],
                    400
                );
            }


            $paciente = DB::connection('mysql')
                ->table('asesorias')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($paciente) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Asesoria eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la asesoria o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la asesoria',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function guardarVentaAsesoria(Request $request)
    {
        $data = $request->all();
        $respuesta = Asesorias::guardarVentaAsesoria($data);
        if ($respuesta) {
            $estado = true;
        } else {
            $estado = false;
        }
        return response()->json([
            'success' => $estado,
            'message' => 'Datos guardados'
        ]);
    }

    public function eliminarVentaAsesoria(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $estado = Servicios::eliminarServicioVenta($idRegistro);
        if ($estado) {
            $estado = true;
        } else {
            $estado = false;
        }
        return response()->json([
            'success' => $estado,
            'message' => 'Datos eliminados'
        ]);
    }

    public function buscaVentaAsesoria(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $asesoria = Servicios::buscaServicioVenta($idRegistro);
        return response()->json($asesoria);
    }

    public function listaServiciosVenta(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $servicios = Asesorias::listaServiciosVenta($idRegistro);
        $html = '';
        $cont = 1;
        foreach ($servicios as $servicio) {
            $html .= '<tr>
                        <td>' . $cont . '</td>
                        <td>' . $servicio->descripcion . '</td>
                        <td>$' . number_format($servicio->valor, 2, ',', '.') . '</td>
                        <td>' . date('d/m/Y', strtotime($servicio->fecha)) . '</td>
                        <td>' . $servicio->estado_venta . '</td>
                        <td>
                            <a href="javascript:void(0)" onclick="editarRegistroVenta(' . $servicio->id . ');" class="btn btn-primary"><i class="fa fa-edit"></i></a>
                            <a href="javascript:void(0)" onclick="eliminarRegistroVenta(' . $servicio->id . ');" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                        </td>
                    </tr>';
            $cont++;
        }
        return response()->json($html);
    }
}
