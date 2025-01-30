<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Especialidades;
use App\Models\Profesional;
use App\Models\Entidades;
use App\Models\HistoriaPsicologica;
use App\Models\Paquetes;


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
    public function Recaudos()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Recaudos.gestionRecaudos', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function Entidades()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarEPS', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
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
    public function Profesionales()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Adminitraccion.gestionarProfesionales', compact('bandera'));
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
                                    <td>' . $item->codigo . '</td>
                                    <td>' . $item->entidad . '</td>
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
            $pagination = $ListEntidades->links('Adminitraccion.Paginacion')->render();

            return response()->json([
                'entidades' => $tdTable,
                'links' => $pagination
            ]);
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

    public function listaVentasPacientes(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $paquetes = DB::connection('mysql')
                ->table('ventas_paquetes')
                ->leftJoin('historia_clinica', 'ventas_paquetes.historia_clinica_id',  'historia_clinica.id')
                ->leftJoin('pacientes', 'historia_clinica.id_paciente',  'pacientes.id')
                ->leftJoin('paquetes', 'ventas_paquetes.paquete_id',  'paquetes.id')
                ->where('ventas_paquetes.estado', 'ACTIVO')
                ->where('ventas_paquetes.estado_venta', 'PENDIENTE')
                ->select(
                    'ventas_paquetes.id',
                    'paquetes.descripcion',
                    DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ',pacientes.segundo_apellido) as nombre_paciente"),
                    'ventas_paquetes.fecha_compra',
                    'ventas_paquetes.sesiones_disponibles',
                    'ventas_paquetes.sesiones_compradas',
                    'ventas_paquetes.saldo'
                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    $query->where('paquetes.descripcion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $saldo = number_format($item->saldo, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>
                                        <div style="cursor: pointer" onclick="realizarPago(' . $item->id . ');"
                                            class="bg-primary-light h-50 w-50 l-h-60 rounded text-center">
                                            <span class="fa fa-dollar fs-24"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="text-dark fw-600 hover-primary m-0">' . $item->identificacion_completa . ' - ' . $item->nombre_paciente . '</h5>
                                        <span class="text-fade d-block fs-14"><strong>Paquete: </strong> ' . $item->descripcion . '</span>
                                    </td>
                                    <td>
                                        <h3>' . $item->fecha_compra . '</h3>
                                    </td>
                                    <td>
                                    <div class="text-center"><h3>' . $item->sesiones_disponibles . '/' . $item->sesiones_compradas . '</h3></div>
                                        
                                    </td>
                                    <td>
                                        <h3>$ ' . $saldo . '</h3>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListPaquetes->links('Recaudos.PaginacionRecaudos')->render();

            //consultar pagos pendientes 
            $pagosPendientes = DB::connection('mysql')
            ->table('pagos')
            ->where('estado', 'ACTIVO')
            ->whereColumn('pago_realizado', '<', 'pago_total') // Comparar columnas
            ->count();

            /// consultar ventas con saldo
            $ventasConSaldo = DB::connection('mysql')
                ->table('ventas_paquetes')
                ->where('estado', 'ACTIVO')
                ->where('estado_venta', 'PENDIENTE')
                ->where('saldo', '>', 0)
                ->count();

            /// consutlar recaudo de mes
            $recaudoMes = DB::connection('mysql')
                ->table('pagos')
                ->where('estado', 'ACTIVO')
                ->whereMonth('fecha_pago', date('m'))
                ->sum('pago_realizado');

            //consutlar recaudo de dia
            $recaudoDia = DB::connection('mysql')
                ->table('pagos')
                ->where('estado', 'ACTIVO')
                ->whereDate('fecha_pago', date('Y-m-d'))
                ->sum('pago_realizado');


            //consultar historial de pagos realizados mostra los ultimo 5 pagos realizados ordenados por fecha
            $historialPagos = DB::connection('mysql')
                ->table('pagos')
                ->leftJoin('ventas_paquetes', 'pagos.venta_paquete_id', 'ventas_paquetes.id')
                ->leftJoin('paquetes', 'ventas_paquetes.paquete_id', 'paquetes.id')
                ->leftJoin('historia_clinica', 'ventas_paquetes.historia_clinica_id', 'historia_clinica.id')
                ->leftJoin('pacientes', 'historia_clinica.id_paciente', 'pacientes.id')
                ->where('pagos.estado', 'ACTIVO')
                ->orderBy('fecha_pago', 'desc')
                ->select(
                    'pagos.id',
                    'pagos.pago_realizado',
                    'pagos.fecha_pago',
                    'pacientes.primer_nombre',
                    'pacientes.primer_apellido',
                    'paquetes.descripcion'
                    
                )
                ->limit(5)
                ->get();


            return response()->json([
                'paquetesVentas' => $tdTable,
                'links' => $pagination,
                'pagosPendientes' => $pagosPendientes,
                'ventasConSaldo' => $ventasConSaldo,
                'recaudoMes' => $recaudoMes,
                'recaudoDia' => $recaudoDia,
                'historialPagos' => $historialPagos
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function otraInformacionRecaudos(Request $request)
    {
        if (Auth::check()) {
            //consultar pagos pendientes 
            $pagosPendientes = DB::connection('mysql')
            ->table('pagos')
            ->where('estado', 'ACTIVO')
            ->whereColumn('pago_realizado', '<', 'pago_total') // Comparar columnas
            ->count();

            /// consultar ventas con saldo
            $ventasConSaldo = DB::connection('mysql')
                ->table('ventas_paquetes')
                ->where('estado', 'ACTIVO')
                ->where('estado_venta', 'PENDIENTE')
                ->where('saldo', '>', 0)
                ->count();

            /// consutlar recaudo de mes
            $recaudoMes = DB::connection('mysql')
                ->table('pagos')
                ->where('estado', 'ACTIVO')
                ->whereMonth('fecha_pago', date('m'))
                ->sum('pago_realizado');

            //consutlar recaudo de dia
            $recaudoDia = DB::connection('mysql')
                ->table('pagos')
                ->where('estado', 'ACTIVO')
                ->whereDate('fecha_pago', date('Y-m-d'))
                ->sum('pago_realizado');


            //consultar historial de pagos realizados mostra los ultimo 5 pagos realizados ordenados por fecha
            $historialPagos = DB::connection('mysql')
                ->table('pagos')
                ->leftJoin('ventas_paquetes', 'pagos.venta_paquete_id', 'ventas_paquetes.id')
                ->leftJoin('paquetes', 'ventas_paquetes.paquete_id', 'paquetes.id')
                ->leftJoin('historia_clinica', 'ventas_paquetes.historia_clinica_id', 'historia_clinica.id')
                ->leftJoin('pacientes', 'historia_clinica.id_paciente', 'pacientes.id')
                ->where('pagos.estado', 'ACTIVO')
                ->orderBy('fecha_pago', 'desc')
                ->select(
                    'pagos.id',
                    'pagos.pago_realizado',
                    'pagos.fecha_pago',
                    'pacientes.primer_nombre',
                    'pacientes.primer_apellido',
                    'paquetes.descripcion'
                    
                )
                ->limit(5)
                ->get();

            return response()->json([              
                'pagosPendientes' => $pagosPendientes,
                'ventasConSaldo' => $ventasConSaldo,
                'recaudoMes' => $recaudoMes,
                'recaudoDia' => $recaudoDia,
                'historialPagos' => $historialPagos
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    

    public function listaVentasPacientesPagos(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('pagePago', 1);
            $search = request()->get('searchPago');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $paquetes = DB::connection('mysql')
                ->table('ventas_paquetes')
                ->leftJoin('historia_clinica', 'ventas_paquetes.historia_clinica_id',  'historia_clinica.id')
                ->leftJoin('pacientes', 'historia_clinica.id_paciente',  'pacientes.id')
                ->leftJoin('paquetes', 'ventas_paquetes.paquete_id',  'paquetes.id')
                ->leftJoin('pagos', 'ventas_paquetes.id',  'pagos.venta_paquete_id')
                ->where('ventas_paquetes.estado', 'ACTIVO')
                ->where('pagos.estado', 'ACTIVO')
                ->where('ventas_paquetes.estado_venta', 'PAGADO')
                ->select(
                    'ventas_paquetes.id',
                    'paquetes.descripcion',
                    DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(pacientes.primer_nombre,' ',pacientes.segundo_nombre,' ',pacientes.primer_apellido,' ',pacientes.segundo_apellido) as nombre_paciente"),
                    'pagos.fecha_pago',
                    'pagos.pago_realizado',

                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    $query->where('paquetes.descripcion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $pago_realizado = number_format($item->pago_realizado, 2, ',', '.');
                    $tdTable .= '<tr>
                                    <td>
                                        <div style="cursor: pointer" onclick="verPago(' . $item->id . ');"
                                            class="bg-primary-light h-50 w-50 l-h-60 rounded text-center">
                                            <span class="fa fa-search fs-24"></span>
                                        </div>
                                    </td>
                                    <td>
                                        <h5 class="text-dark fw-600 hover-primary m-0">' . $item->identificacion_completa . ' - ' . $item->nombre_paciente . '</h5>
                                        <span class="text-fade d-block fs-14"><strong>Paquete: </strong> ' . $item->descripcion . '</span>
                                    </td>
                                    <td>
                                        <h3>' . $item->fecha_pago . '</h3>
                                    </td>                                  
                                    <td>
                                        <h3>$ ' . $pago_realizado . '</h3>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListPaquetes->links('Recaudos.PaginacionRecaudosPagos')->render();

            return response()->json([
                'paquetesVentas' => $tdTable,
                'links' => $pagination,
            ]);
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
    public function Perfiles()
    {
        if (Auth::check()) {
            $bandera = "";
            return view('Usuario.gestionarPerfiles', compact('bandera'));
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function  guardarEspecialidad(Request $request)
    {
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
    public function  guardarPagoVenta(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        $respuesta = Paquetes::GuardarPagoPaquete($data);

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
   

    public function  guardarEntidades(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
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
    public function  guardarPaquete(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        // Guardar la información del paciente
        $respuesta = Paquetes::guardar($data);

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

    public function  guardarProfesional(Request $request)
    {
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
                ->select(
                    'nombre',
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

    public function cargarListaProf()
    {
        $profesionales = DB::connection('mysql')
            ->table('profesionales')
            ->where('estado', 'ACTIVO')
            ->get();
        return response()->json($profesionales);
    }
    public function cargarListaEsp()
    {
        $especialidades = DB::connection('mysql')
            ->table('especialidades')
            ->where('estado', 'ACTIVO')
            ->get();
        return response()->json($especialidades);
    }


    public function verificarIdentProfesional(Request $request)
    {
        $identificacion = $request->input('identificacion');
        // Verificar si el usuario ya está registrado
        $profesionalExistente = DB::table('profesionales')
            ->where('identificacion', $identificacion)
            ->exists();

        return response()->json(!$profesionalExistente);
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

    public function busquedaEspecialidad(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $especialidad = Especialidades::busquedaEspecialidad($idRegistro);
        return response()->json($especialidad);
    }
    public function buscaEntidad(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $entidad = Entidades::busquedaEntidad($idRegistro);
        return response()->json($entidad);
    }

    public function detalleVentaPaquetePaciente(Request $request)
    {
        $idVenta = $request->input('idVenta');
        $PaqueteVenta = Paquetes::busquedaPaquetesVentas($idVenta);
        
        $historialpagos = DB::connection('mysql')
            ->table('pagos')
            ->leftJoin('medio_pagos', 'pagos.id', 'medio_pagos.id_pago')
            ->leftJoin("users", "pagos.usuario", "users.id")
            ->where('venta_paquete_id', $idVenta)
            ->where('pagos.estado', 'ACTIVO')
            ->select(
                'pagos.id',
                'pagos.pago_realizado',
                'pagos.fecha_pago',
                'medio_pagos.id as idMedioPago',
                DB::raw("CASE
                WHEN medio_pagos.medio_pago = 'e' THEN 'Efectivo'
                WHEN medio_pagos.medio_pago = 't' THEN 'Transferencia'
                WHEN medio_pagos.medio_pago = 'tc' THEN 'Tarjeta de débito'
                ELSE 'Tarjeta de crédito' END as nombreMedioPago"),
                'medio_pagos.referencia',
                'users.nombre_usuario'
                
            )
            ->get();
            
            return response()->json([
                'PaqueteVenta' => $PaqueteVenta,
                'historialpagos' => $historialpagos
            ]);

        return response()->json($PaqueteVenta);
    }
    public function detalleVentaPagosPaciente(Request $request)
    {
        $idVenta = $request->input('idVenta');
        $PaqueteVenta = Paquetes::busquedaPaquetesVentas($idVenta);
        $historialpagos = DB::connection('mysql')
            ->table('pagos')
            ->leftJoin('users', 'pagos.usuario', 'users.id')
            ->where('venta_paquete_id', $idVenta)
            ->where('pagos.estado', 'ACTIVO')
            ->select(
                'pagos.id',
                'pagos.pago_realizado',
                'pagos.fecha_pago',
                'users.nombre_usuario'
            )
            ->get();

            return response()->json([
                'PaqueteVenta' => $PaqueteVenta,
                'historialpagos' => $historialpagos
            ]);

        return response()->json($PaqueteVenta);
    }

    public function buscarPaquete(Request $request)
    {
        $idRegistro = $request->input('idRegistro');
        $entidad = Paquetes::busquedaPaquetes($idRegistro);
        return response()->json($entidad);
    }

    public function busquedaProfesional(Request $request)
    {
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
