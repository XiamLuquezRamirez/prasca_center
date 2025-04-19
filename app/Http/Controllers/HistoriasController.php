<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\HistoriaPsicologica;
use App\Models\CategoriaHCP;
use App\Models\Pacientes;
use App\Models\Paquetes;
use App\Models\Profesional;
use App\Models\Citas;
use Dompdf\Dompdf;
use \PDF;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Support\Facades\Log;

class HistoriasController extends Controller
{
    public function historiaPsicologia()
    {
        if (Auth::check()) {
            return view('HistoriasClinica.psicologia');
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function informePsicologia()
    {
        if (Auth::check()) {
            return view('HistoriasClinica.informePsicologia');
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function informes()
    {
        if (Auth::check()) {
            return view('HistoriasClinica.informeGenerales');
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function informeGeneral()
    {

        if (Auth::check()) {
            $fecha1 = request()->get('fecha1');
            $fecha2 = request()->get('fecha2');

            $fechaInicio = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha1)->format('Y-m-d') . 'T00:00:00';
            $fechaFin = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha2)->format('Y-m-d') . 'T23:59:59';

            $citas = Citas::buscarCitas($fechaInicio, $fechaFin);

            $totalCitas = $citas->count();
            $citasAtendidas = $citas->where('estado', 'Atendida')->count();
            $citasCanceladas = $citas->where('estado', 'Anulada')->count();
            $citasNoConfirmadas = $citas->whereIn('estado', ['Por atender', 'no-confirmada', 'por-atender', 'Confirmada'])->count();

            // Evitar división por cero
            $tasaAsistencia = $totalCitas > 0 ? ($citasAtendidas / $totalCitas) * 100 : 0;

            $citasPorProfesional = $citas->groupBy('nomprof')->map(function ($citas, $nombre) use ($totalCitas) {
                $cantidad = $citas->count();
                $idprof = $citas->first()->idprof;
                return [
                    'idprof' => $idprof,
                    'nombre' => $nombre,
                    'totalCitas' => $cantidad,
                    'porcentaje' => $totalCitas > 0 ? round(($cantidad / $totalCitas) * 100, 2) : 0
                ];
            })->values(); // `values()` para resetear las claves y devolver un array ordenado



            /// informacion del recaudo
            $ventas = HistoriaPsicologica::busquedaServicios($fechaInicio, $fechaFin);

            // Convertimos los precios a enteros
            $ventas = $ventas->map(function ($venta) {
                $venta->precio = (int) $venta->precio;
                return $venta;
            });

            // Contar la cantidad de cada tipo
            $cantidadConsultas = $ventas->where('tipo', 'CONSULTA')->count();
            $cantidadSesiones = $ventas->where('tipo', 'SESION')->count();
            $cantidadPaquetes = $ventas->where('tipo', 'PAQUETE')->count();
            $cantidadTotal = $ventas->count();

            // Obtener los totales de cada tipo
            $totalConsultas = $ventas->where('tipo', 'CONSULTA')->sum('precio');
            $totalSesiones = $ventas->where('tipo', 'SESION')->sum('precio');
            $totalPaquetes = $ventas->where('tipo', 'PAQUETE')->sum('precio');

            // Calcular el total general
            $totalGeneral = $totalConsultas + $totalSesiones + $totalPaquetes;

            //Recaudo 

            $recaudo = DB::connection('mysql')
                ->table('pagos')
                ->where('estado', 'ACTIVO')
                ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                ->sum('pago_realizado');

            $servicio = DB::connection('mysql')
                ->table('servicios')
                ->where('estado', 'ACTIVO')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->sum('precio');


            return response()->json([
                'totalCitas' => $totalCitas,
                'citasAtendidas' => $citasAtendidas,
                'citasCanceladas' => $citasCanceladas,
                'citasNoConfirmadas' => $citasNoConfirmadas,
                'tasaAsistencia' => $tasaAsistencia,
                'citasPorProfesional' => $citasPorProfesional,
                'cantidadConsultas' => $cantidadConsultas,
                'cantidadSesiones' => $cantidadSesiones,
                'cantidadPaquetes' => $cantidadPaquetes,
                'cantidadTotal' => $cantidadTotal,
                'totalConsultas' => $totalConsultas,
                'totalSesiones' => $totalSesiones,
                'totalPaquetes' => $totalPaquetes,
                'totalGeneral' => $totalGeneral,
                'recaudo' => $recaudo,
                'pendiente' => $servicio

            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function otrosInformes()
    {
        if (Auth::check()) {
            $fecha1 = request()->get('fecha1');
            $fecha2 = request()->get('fecha2');

            $fechaInicio = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha1)->format('Y-m-d') . 'T00:00:00';
            $fechaFin = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha2)->format('Y-m-d') . 'T23:59:59';

            $recaudo = DB::connection('mysql')
                ->table('pagos')
                ->selectRaw('DATE(fecha_pago) as fecha, SUM(pago_realizado) as total_recaudo')
                ->where('estado', 'ACTIVO')
                ->whereBetween('fecha_pago', [$fechaInicio, $fechaFin])
                ->groupBy('fecha')
                ->orderBy('fecha', 'asc')
                ->get();

            $citas = DB::connection('mysql')
                ->table('citas')
                ->selectRaw('DATE(inicio) as fecha, COUNT(id) as cant')
                ->where('estado', 'Atendida')
                ->whereBetween('inicio', [$fechaInicio, $fechaFin])
                ->groupBy(DB::raw('DATE(inicio)'))
                ->orderBy('fecha', 'asc')
                ->get();

            return response()->json([
                'recaudo' => $recaudo,
                'citas' => $citas

            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function  guardarVentaConsulta(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        $respuesta = Pacientes::guardarVentaConsulta($data);

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
            'message' => 'Datos guardados exitosamente'
        ]);
    }

    public function  guardarVentaSesion(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401); // Código de error 401: No autorizado
        }

        // Capturar los datos del request
        $data = $request->all();
        $respuesta = Pacientes::guardarVentaSesion($data);

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
            'message' => 'Datos guardados exitosamente'
        ]);
    }

    public function listaPaquetesSel()
    {
        $paquetes = Paquetes::listarPaquetes();
        return response()->json($paquetes);
    }

    public function listaPaquetesModal(Request $request)
    {

        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $idHist = $request->input('idHist');
            $tipoHist = $request->input('tipoHist');

            $paquetes = DB::connection('mysql')
                ->table('servicios')
                ->leftJoin("ventas", "servicios.id", "ventas.id_servicio")
                ->leftJoin('sesiones_paquete_uso', 'ventas.id',  'sesiones_paquete_uso.venta_id')
                ->leftJoin("paquetes", "paquetes.id", "servicios.id_paquete")
                ->where('servicios.estado', 'ACTIVO')
                ->where('servicios.tipo', 'PAQUETE')
                ->where('servicios.id_historia', $idHist)
                ->where('servicios.tipo_historia', $tipoHist)
                ->select(
                    'servicios.id',
                    'servicios.fecha AS fecha_compra',
                    'ventas.total AS monto_total',
                    'ventas.estado_venta AS estado_control',
                    'ventas.cantidad as sesiones_compradas',
                    'paquetes.descripcion',
                    DB::raw('ventas.cantidad - COUNT(sesiones_paquete_uso.id) as sesiones_disponibles')
                )
                ->groupBy(
                    'servicios.id',
                    'servicios.fecha',
                    'ventas.total',
                    'ventas.estado_venta',
                    'ventas.cantidad',
                    'paquetes.descripcion'
                );

            if ($search) {
                $paquetes->where(function ($query) use ($search) {
                    $query->where('paquetes.descripcion', 'LIKE', '%' . $search . '%');
                });
            }

            $ListPaquetes = $paquetes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListPaquetes as $i => $item) {
                if (!is_null($item)) {
                    $valorTotal = number_format($item->monto_total, 2, ',', '.');

                    $tdTable .= '<tr style="cursor: pointer;">
                                    <td>' . $item->descripcion . '</td>
                                    <td>' . $item->fecha_compra . '</td>
                                    <td>' . $item->sesiones_compradas . '</td>
                                    <td>' . $item->sesiones_disponibles . '</td>                                  
                                    <td>$ ' . $valorTotal . '</td>
                                    <td>' . $item->estado_control . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="editarPaquete(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarPaquete(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListPaquetes->links('HistoriasClinica.PaginacionPaquetes')->render();

            return response()->json([
                'paquetes' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function buscaPlanIntervencion(request $request)
    {
        $idHist = $request->input('idHist');
        $planIntervencion = HistoriaPsicologica::busquedaPlanIntervencion($idHist);

        return response()->json([
            'planIntervencion' => $planIntervencion
        ]);
    }

    public function buscaVentaConsulta(request $request)
    {

        $idHist = $request->input('idHist');

        $servicioConsulta = HistoriaPsicologica::busquedaVentaConsulta($idHist);

        if (!$servicioConsulta) {
            $servicioConsulta = HistoriaPsicologica::busquedaConsultaHistoria($idHist);
        }
        return response()->json([
            'servicioConsulta' => $servicioConsulta
        ]);
    }

    public function buscaSesionVenta(request $request)
    {
        $idVenta = $request->input('idVenta');

        $servicio = HistoriaPsicologica::busquedaVentaSesiom($idVenta);

        return response()->json([
            'servicioSesion' => $servicio
        ]);
    }

    public function buscaVentaSesion(request $request)
    {
        if (Auth::check()) {
            $servicio = DB::connection('mysql')
                ->table('servicios')
                ->leftJoin("ventas", "ventas.id_servicio", "servicios.id")
                ->where('servicios.estado', 'ACTIVO')
                ->where('servicios.tipo', 'SESION')
                ->where('servicios.id_historia', $request->input('idHist'))
                ->where('servicios.tipo_historia', $request->input('tipoHistoria'))
                ->select(
                    'servicios.id',
                    'servicios.descripcion',
                    'servicios.precio',
                    'servicios.fecha',
                    'ventas.estado_venta'
                )
                ->get();

            $tdTable = '';
            $const = 1;
            foreach ($servicio as $i => $item) {
                if (!is_null($item)) {
                    $valor = number_format($item->precio, 2, ',', '.');
                    $fecha = \Carbon\Carbon::parse($item->fecha)->format('d/m/Y h:i A');
                    $tdTable .= '<tr>
                               
                                <td>' . $item->descripcion . '</td>
                                <td>' . $fecha . '</td>
                                <td>$' . $valor . '</td>                              
                                <td>' . $item->estado_venta . '</td>
                                <td class="table-action min-w-100">
                                    <a data-id="' . $item->id . '" data-estado="' . $item->estado_venta . '" onclick="editarVentaSesion(this);" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                            data-feather="edit-2"></i></a>
                                    <a data-id="' . $item->id . '" data-estado="' . $item->estado_venta . '" onclick="eliminarVentaSesion(this);" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                            data-feather="trash"></i></a>
                                </td>
                            </tr>';
                    $const++;
                }
            }

            return response()->json([
                'ventasSesiones' => $tdTable,
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaHistoriaPsicologica(Request $request)
    {
        if (Auth::check()) {
            $idHist = $request->input('idHist');
            $historia = HistoriaPsicologica::busquedaHistoria($idHist);

            $pacientes = Pacientes::busquedaPaciente($historia->id_paciente);

            $antecedentesPersonales = HistoriaPsicologica::busquedaAntecedentes($historia->id);
            $antecedentesFamiliares = HistoriaPsicologica::busquedaAntFamiliares($historia->id);
            $areaAjuste = HistoriaPsicologica::busquedaAreaAjuste($historia->id);
            $interconuslta = HistoriaPsicologica::busquedaInterconsulta($historia->id);
            $examenMental = HistoriaPsicologica::busquedaExamenMental($historia->id);

            $antecedentesPrenatales = HistoriaPsicologica::busquedaAntPrenatales($historia->id);
            $antecedentesNatales = HistoriaPsicologica::busquedaAntNatales($historia->id);
            $antecedentesPosnatales = HistoriaPsicologica::busquedaAntPosnatales($historia->id);
            $desarrolloPsicomotor = HistoriaPsicologica::desarrolloPsicomotor($historia->id);

            $historiaCon = self::consultasLateral($historia->id);

            return response()->json([
                'historia' => $historia,
                'paciente' => $pacientes,
                'antecedentesPersonales' => $antecedentesPersonales,
                'antecedentesFamiliares' => $antecedentesFamiliares,
                'areaAjuste' => $areaAjuste,
                'interconuslta' => $interconuslta,
                'examenMental' => $examenMental,
                'antecedentesPrenatales' => $antecedentesPrenatales,
                'antecedentesNatales' => $antecedentesNatales,
                'antecedentesPosnatales' => $antecedentesPosnatales,
                'desarrolloPsicomotor' => $desarrolloPsicomotor,
                'historialConsultas' => $historiaCon
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
    public function buscaHistoriaPsicologicaInforme(Request $request)
    {
        if (Auth::check()) {
            $idPaciente = $request->input('idPaciente');
            $historia = HistoriaPsicologica::busquedaHistoriaPaciente($idPaciente);
            $pacientes = Pacientes::busquedaPaciente($historia->id_paciente);
            $interconuslta = HistoriaPsicologica::busquedaInterconsulta($historia->id);

            return response()->json([
                'historia' => $historia,
                'paciente' => $pacientes,
                'interconuslta' => $interconuslta
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function consultasLateral($idHistoria)
    {

        $historialConsultas = HistoriaPsicologica::historialConsultas($idHistoria);

        $historiaCon = "";
        $mt = "mt-4";
        foreach ($historialConsultas as $i => $item) {

            if ($i > 0) {
                $mt = "mb-0";
            }

            $historiaCon .= '<div class="' . $mt . '">
            <div class="pb-20">
                <div class="dropdown float-end">
                    <a href="#" class="dropdown-toggle no-caret"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="mdi mdi-dots-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end">
                    <a href="javascript:verEvolucion(' . $item->id . ');"
                            class="dropdown-item"><i class="fa fa-eye"></i> Ver</a>    
                    <a style="display:none;" href="javascript:imprimirConsulta(' . $item->id . ');"
                            class="dropdown-item"><i class="fa fa-print"></i> Imprimir</a>
                    </div> <!-- item-->
                </div>
                    <p class="fs-16">' . date('d/m/Y g:i:s A', strtotime($item->fecha_consulta)) . '</p>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div
                                class="bg-transparent h-50 w-50 border border-light product_icon text-center">
                                <p class="mb-0 fs-20 w-50 fw-600 l-h-40"><i
                                    class="fa fa-stethoscope"
                                    aria-hidden="true"></i>
                                </p>
                            </div>
                            <div class="d-flex flex-column font-weight-500 mx-10">
                                <a href="#"
                                    class="text-dark hover-primary mb-1  fs-15">' . $item->consulta . '</a>
                                <span class="text-fade"><i
                                    class="fa fa-fw fa-circle fs-10 text-success"></i>
                                    ' . $item->diagnostico . '</span>
                            </div>
                        </div>
                        <div>
                            <div class="d-flex flex-column font-weight-500">
                                <span class="text-fade text-end"><i
                                        class="fa fa-user-md"></i> ' . $item->profesional . '</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
        }
        return $historiaCon;
    }

    public function eliminarConsulta()
    {
        try {
            $idConsulta = request()->input('idConsulta');
            if (!$idConsulta) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la consulta no proporcionada'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('consultas_psicologica')
                ->where('id', $idConsulta)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);



            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Consulta eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la consulta o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la consulta',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }
    public function eliminarPaquete()
    {
        try {
            $idPaquete = request()->input('idPaquete');
            if (!$idPaquete) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID del paquete no proporcionado'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('servicios')
                ->where('id', $idPaquete)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);


            if ($consulta) {
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
    public function eliminarSesionVenta()
    {
        try {
            $idVenta = request()->input('idVenta');
            if (!$idVenta) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la Sesión no proporcionado'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('servicios')
                ->where('id', $idVenta)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);



            if ($consulta) {
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
                        'message' => 'No se encontró la Sesión o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la Sesión )',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarHistoria()
    {
        try {
            $idHistoria = request()->input('idHistoria');
            if (!$idHistoria) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la historia no proporcionada'
                    ],
                    400
                );
            }

            // eliminar historia con delete
            $consulta = DB::connection('mysql')
                ->table('historia_clinica')
                ->where('id', $idHistoria)
                ->update([
                    'estado_registro' => 'ELIMINADO',
                ]);

            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Historia eliminada correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la historia o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la hisotria',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function eliminarInforme()
    {
        try {
            $idReg = request()->input('idReg');
            if (!$idReg) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la Informe no proporcionado'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('informe_evolucion')
                ->where('id', $idReg)
                ->update([
                    'estado' => 'ELIMINADO',
                ]);

            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Informe eliminado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la consulta o no se pudo eliminar'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar eliminar la consulta',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function cerrarHistoria()
    {
        try {
            $idHist = request()->input('idHist');
            $estado = request()->input('estado');
            if (!$idHist) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'ID de la historia no proporcionada'
                    ],
                    400
                );
            }

            $consulta = DB::connection('mysql')
                ->table('historia_clinica')
                ->where('id', $idHist)
                ->update([
                    'estado_hitoria' => ($estado == 'abierta' ? 'cerrada' : 'abierta'),
                ]);


            if ($consulta) {
                return response()->json(
                    [
                        'success' => true,
                        'message' => 'Estado de la historia cambiado correctamente'
                    ]
                );
            } else {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No se encontró la historia'
                    ],
                    404
                );
            }
        } catch (\Exception $e) {
            // Manejar cualquier error o excepción
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Ocurrió un error al intentar cambiar el estado de la historia',
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    public function buscaConsultaPsicologica(Request $request)
    {
        $idConsulta = $request->input('idConsulta');
        $consulta = HistoriaPsicologica::busquedaConsulta($idConsulta);

        return response()->json([
            'consulta' => $consulta
        ]);
    }
    public function buscaPaqueteVenta(Request $request)
    {
        $idPaquete = $request->input('idPaquete');
        $paquetes = Paquetes::busquedaPaquetesVentas($idPaquete);

        return response()->json([
            'paquete' => $paquetes
        ]);
    }

    public function buscaInformePsicologica(Request $request)
    {
        $idInforme = $request->input('idInforme');
        $informe = HistoriaPsicologica::busquedaInforme($idInforme);

        return response()->json([
            'informe' => $informe
        ]);
    }

    public function buscaProfesionalHistoria(Request $request)
    {
        $idProf = $request->input('idProf');
        $profesional = Profesional::busquedaProfesionalHitoria($idProf);

        //responder si se encontro el profesional con estado
        return response()->json([
            'profesional' => $profesional
        ]);


        return response()->json($profesional);
    }

    public function buscaCUPS(Request $request)
    {
        $id = $request->get('id');
        $query = $request->get('q');
        $page = $request->get('page', 1); // Página actual
        $perPage = 30; // Número de resultados por página

        // Si se proporciona un ID, devolver solo ese registro
        if ($id) {
            $resultado = DB::connection('mysql')
                ->table('referencia_cups')
                ->where('id', $id)
                ->select('id', DB::raw('CONCAT(codigo, " - ", nombre) as text'))
                ->first();

            if ($resultado) {
                return response()->json($resultado); // Devuelve el registro único
            }

            return response()->json(null, 404); // Registro no encontrado
        }

        // Búsqueda por término
        $resultados = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('nombre', 'like', '%' . $query . '%')
            ->orWhere('codigo', 'like', '%' . $query . '%')
            ->select('id', DB::raw('CONCAT(codigo, " - ", nombre) as text'))
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Total de resultados para paginación
        $total = DB::connection('mysql')
            ->table('referencia_cups')
            ->where('nombre', 'like', '%' . $query . '%')
            ->orWhere('codigo', 'like', '%' . $query . '%')
            ->count();

        // Formato de respuesta compatible con Select2
        return response()->json([
            'data' => $resultados,
            'total_count' => $total
        ]);
    }

    public function obtenerOpcionesHCP()
    {

        $categorias = CategoriaHCP::with('opciones')->get();
        return response()->json($categorias);
    }

    public function buscaCIE(Request $request)
    {
        $id = $request->get('id');
        $query = $request->get('q');
        $page = $request->get('page', 1);
        $perPage = 30;

        if ($id) {
            $resultado = DB::connection('mysql')
                ->table('referencia_cie10')
                ->where('id', $id)
                ->select('id', DB::raw('CONCAT(codigo, " - ", nombre) as text'))
                ->first();

            if ($resultado) {
                return response()->json($resultado); // Devuelve el registro único
            }

            return response()->json(null, 404); // Registro no encontrado
        }

        $resultados = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('nombre', 'like', '%' . $query . '%')
            ->orWhere('codigo', 'like', '%' . $query . '%')
            ->select('id', DB::raw('CONCAT(codigo, " - ", nombre) as text'))
            ->skip(($page - 1) * $perPage)
            ->take($perPage)
            ->get();

        // Total de resultados para paginación
        $total = DB::connection('mysql')
            ->table('referencia_cie10')
            ->where('nombre', 'like', '%' . $query . '%')
            ->orWhere('codigo', 'like', '%' . $query . '%')
            ->count();

        // Formato de respuesta compatible con Select2
        return response()->json([
            'data' => $resultados,
            'total_count' => $total
        ]);
    }

    public function guardarPlanIntervencion()
    {
        try {
            $data = request()->all();
            $respuesta = HistoriaPsicologica::guardarPlanIntervencion($data);

            if ($respuesta) {
                return response()->json([
                    'success' => true,
                    'title' => '¡Buen trabajo!',
                    'message' => 'Plan de intervención guardado correctamente',
                    'id' => $respuesta
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'title' => '¡Opps salio algo mal!',
                    'message' => 'No se pudo guardar el plan de intervención'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'title' => '¡Opps salio algo mal!',
                'message' => 'Ocurrió un error al intentar guardar el plan de intervención',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function guardarHistoriaPsicologica(Request $request)
    {


        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        try {
            $data = $request->all();
            $respuesta = HistoriaPsicologica::Guardar($data);

            return response()->json($respuesta);
        } catch (\Exception $e) {
            Log::error('Error en guardarHistoriaPsicologica: ' . $e->getMessage(), [
                'data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la solicitud',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function listaConsultasModal(Request $request)
    {
        if (Auth::check()) {
            $perPage = 5; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            $idHist = request()->get('idHist');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $consultas = DB::connection('mysql')
                ->table('consultas_psicologica')
                ->leftJoin("referencia_cups", "referencia_cups.id", "consultas_psicologica.codigo_consulta")
                ->leftJoin("referencia_cie10", "referencia_cie10.id", "consultas_psicologica.impresion_diagnostica")
                ->leftJoin("profesionales", "profesionales.id", "consultas_psicologica.id_profesional")
                ->where("consultas_psicologica.estado", "ACTIVO")
                ->where("consultas_psicologica.id_historia", $idHist)
                ->orderBy('consultas_psicologica.fecha_consulta', 'desc')
                ->select(
                    'consultas_psicologica.id',
                    'consultas_psicologica.fecha_consulta',
                    'referencia_cups.nombre AS consulta',
                    'referencia_cie10.nombre AS diagnostico',
                    'profesionales.nombre AS profesional'
                );

            if ($search) {
                $consultas->where(function ($query) use ($search) {
                    $query->where('profesionales.nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('referencia_cups.nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('referencia_cie10.nombre', 'LIKE', '%' . $search . '%');
                });
            }

            $ListConsultas = $consultas->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListConsultas as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . date('d/m/Y g:i:s A', strtotime($item->fecha_consulta)) . '</td>
                                    <td>' . $item->consulta . '</td>
                                    <td>' . $item->diagnostico . '</td>
                                    <td>' . $item->profesional . '</td>
                                    <td class="table-action min-w-150">
                                        <a onclick="imprimirConsulta(' . $item->id . ');" style="cursor: pointer;" title="Imprimir" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="file-text"></i></a>
                                        <a onclick="editarConsulta(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarConsulta(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListConsultas->links('Adminitraccion.Paginacion')->render();

            $consutlasLateral = self::consultasLateral($idHist);

            return response()->json([
                'consultas' => $tdTable,
                'links' => $pagination,
                'historialConsultas' => $consutlasLateral
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function informePsicologiaList(Request $request)
    {
        if (Auth::check()) {
            $perPage = 5; // Número de posts por página
            $page = request()->get('page', 1);
            $idPaciente = request()->get('idPac');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $informes = DB::connection('mysql')
                ->table('informe_evolucion')
                ->leftJoin("profesionales", "profesionales.id", "informe_evolucion.id_profesional")
                ->where("informe_evolucion.estado", "ACTIVO")
                ->where("informe_evolucion.id_paciente", $idPaciente)
                ->orderBy('informe_evolucion.fecha_creacion', 'desc')
                ->select(
                    'informe_evolucion.id',
                    'informe_evolucion.fecha_creacion',
                    'profesionales.nombre AS profesional'
                );


            $ListInformes = $informes->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;
            $const = 1;
            foreach ($ListInformes as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . $const . '</td>
                                    <td>' . $item->profesional . '</td>
                                    <td>' . date('d/m/Y g:i:s A', strtotime($item->fecha_creacion)) . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="generarPDF(' . $item->id . ');" style="cursor: pointer;" title="Imprimir" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="file-text"></i></a>
                                        <a onclick="editarInforme(' . $item->id . ');" style="cursor: pointer;" title="Editar" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="edit-2"></i></a>
                                        <a onclick="eliminarInforme(' . $item->id . ');" style="cursor: pointer;" title="Eliminar" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="trash"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                    $const++;
                }
            }
            $pagination = $ListInformes->links('HistoriasClinica.PaginacionConsultas')->render();

            return response()->json([
                'informes' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function  guardarConsultaPsicologica(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        $data = $request->all();

        $respuesta = HistoriaPsicologica::guardarConsulta($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = 'success';
            $message = 'La operación fue realizada exitosamente.';
            $title = '¡Buen trabajo!';
        } else {
            $message = 'No se pudo realizada la operación.';
            $estado = 'warning';
            $title = '¡Opps salio algo mal!';
        }
        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' =>  $message,
            'title' =>  $title
        ]);
    }
    public function  guardarInformePsicologica(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        $data = $request->all();

        $respuesta = HistoriaPsicologica::guardarInforme($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = 'success';
            $message = 'La operación fue realizada exitosamente.';
            $title = '¡Buen trabajo!';
        } else {
            $message = 'No se pudo realizada la operación.';
            $estado = 'warning';
            $title = '¡Opps salio algo mal!';
        }
        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' =>  $message,
            'title' =>  $title
        ]);
    }


    public function  guardarPaqueteVenta(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'estado' => 'error',
                'mensaje' => 'Su sesión ha terminado.',
            ], 401);
        }

        $data = $request->all();

        $respuesta = Pacientes::guardarPaqueteVenta($data);

        // Verificar el resultado y preparar la respuesta
        if ($respuesta) {
            $estado = 'success';
            $message = 'La operación fue realizada exitosamente.';
            $title = '¡Buen trabajo!';
        } else {
            $message = 'No se pudo realizada la operación.';
            $estado = 'warning';
            $title = '¡Opps salio algo mal!';
        }
        // Retornar la respuesta en formato JSON
        return response()->json([
            'success' => $estado,
            'id' => $respuesta,
            'message' =>  $message,
            'title' =>  $title
        ]);
    }

    public function listaHistoriasPsicologica(Request $request)
    {

        if (Auth::check()) {
            $perPage = 5; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }

            $historias = DB::connection('mysql')
                ->table('historia_clinica')
                ->leftJoin('pacientes', 'historia_clinica.id_paciente', '=', 'pacientes.id')
                ->leftJoin('profesionales', 'historia_clinica.id_profesional', 'profesionales.id')

                ->where('estado_registro', 'ACTIVO')
                ->orderBy('historia_clinica.fecha_historia', 'desc')
                ->select(
                    "historia_clinica.id",
                    DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) as identificacion_completa"),
                    DB::raw("CONCAT(primer_nombre,' ',segundo_nombre,' ',primer_apellido,' ', segundo_apellido) as nombre_completo"),
                    "historia_clinica.fecha_historia",
                    "historia_clinica.tipologia",
                    "historia_clinica.estado_hitoria",
                    "pacientes.fecha_nacimiento",
                    'historia_clinica.codigo_consulta',
                    'pacientes.id as id_paciente',
                    'profesionales.nombre as profesional',
                    'historia_clinica.porcentaje_completitud'
                );

            if ($search) {
                $historias->where(function ($query) use ($search) {
                    $query->where('pacientes.identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_apellido', 'LIKE', '%' . $search . '%');
                });
            }

            $ListHistoria = $historias->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListHistoria as $i => $item) {
                if (!is_null($item)) {

                    if ($item->estado_hitoria == "abierta") {
                        $estado = "<i  class='fa fa-unlock'></i> Abierta";
                        $class = "text-success";
                        $disabled = "";
                    } else {
                        $estado = "<i class='fa fa-unlock-alt'></i> Cerrada";
                        $class = "text-danger";
                        $disabled = "disabled";
                    }

                    $fechaNacimiento = $item->fecha_nacimiento;
                    $fechaNacimiento = \Carbon\Carbon::parse($fechaNacimiento);
                    $fechaActual = \Carbon\Carbon::now();
                    $diferencia = $fechaActual->diff($fechaNacimiento);
                    $edadTexto = "{$diferencia->y} años, {$diferencia->m} meses, y {$diferencia->d} días";

                    $porcentajeCompletitud = $item->porcentaje_completitud;
                    if ($porcentajeCompletitud >= 90) {
                        $bgColor = 'bg-success'; // Verde intenso
                    } elseif ($porcentajeCompletitud >= 70) {
                        $bgColor = 'bg-primary'; // Azul
                    } elseif ($porcentajeCompletitud >= 50) {
                        $bgColor = 'bg-info'; // Celeste
                    } elseif ($porcentajeCompletitud >= 20) {
                        $bgColor = 'bg-warning'; // Amarillo/naranja
                    } else {
                        $bgColor = 'bg-danger'; // Rojo
                    }
                    
                    $tdTable .= ' <div class="box pull-up">
                                <div class="box-body">
                                    <div class="d-md-flex justify-content-between align-items-center">
                                        <div>
                                            <p><span class="text-primary">Historia Clínica</span> | 
                                            <span class="text-fade">Tipo: Neuropsicológica - ' . $item->tipologia . '</span> 
                                            | <span class="text-primary">Profesional:</span> 
                                            <span class="text-fade" style="text-transform: capitalize;">' . $item->profesional . '</span>
                                            </p>
                                            <h3 class="mb-0 fw-500">Paciente: ' . $item->identificacion_completa . ' - ' . $item->nombre_completo . '</h3>
                                        </div> 
                                        <div class="mt-10 mt-md-0">
                                    <div class="btn-group mb-5 mr-10">
								    <div style="margin-right: 20px;">
                                        <div>
                                            <p class="text-fade m-0">Completada</p>
                                                <div> <label
                                                    id="tasaPorcentaje">' . $porcentajeCompletitud. '%</label>
                                                <div
                                                    class="progress progress-lg">
                                                        <div id="tasaBarraPorcentaje"
                                                        class="progress-bar ' . $bgColor . '"
                                                        role="progressbar"
                                                            style="width: ' . $porcentajeCompletitud . '%"
                                                        aria-valuenow="75"
                                                        aria-valuemin="0"
                                                        aria-valuemax="100">
                                                </div>
                                                </div>
                                                </div>
                                            </div>
								    </div>
								</div> 
                                <button type="button" data-id="' . $item->id . '" dapta-tipo="' . $item->tipologia . '" onclick="verHistoria(this)" class="waves-effect waves-light btn btn-info mb-5"><i class="fa fa-search"></i> Ver detalle</button>
                                </div>
                                    </div>
                                    <hr>
                                    <div class="d-md-flex justify-content-between align-items-center">
                                        <div class="d-flex justify-content-start align-items-center">
                                        <div class=" mx-20 min-w-70">
                                                <p class="mb-0 text-fade">Edad</p>
                                                <h6 class="mb-0">' . $edadTexto . '</h6>
                                            </div>    
                                        <div >
                                                <p class="mb-0 text-fade">Fecha de Creación</p>
                                                <h6 class="mb-0">' . date('d/m/Y g:i:s A', strtotime($item->fecha_historia)) . '</h6>
                                            </div>
                                            <div style="cursor:pointer;" data-id="' . $item->id . '" data-estado="' . $item->estado_hitoria . '" onclick="cerrarHistoria(this)" class="mx-lg-50 mx-20 min-w-70">
                                                <p class="mb-0 text-fade">Estado</p>
                                                <h6 class="mb-0 ' . $class . '">' . $estado . '</h6>
                                            </div>
                                        </div>
                                        <div class="mt-10 mt-md-0">
                                            <button type="button" ' . $disabled . ' data-id="' . $item->id . '" data-tipo="' . $item->tipologia . '" onclick="editarHistoria(this);"
                                                class="waves-effect waves-light btn btn-primary btn-flat"><i
                                                    class="fa fa-edit me-10"></i>Editar</button>
                                            <button type="button" data-id="' . $item->id . '" data-estado="' . $item->estado_hitoria . '" onclick="evolucionHistoria(this);"
                                                class="waves-effect waves-light btn btn-secondary btn-flat"><i
                                                    class="fa fa-arrow-right me-10"></i>Evolución</button>
                                            <button type="button" data-id="' . $item->id . '"  onclick="PlanIntervencionHistoria(this);"
                                                class="waves-effect waves-light btn btn-warning btn-flat"><i
                                                    class="fa fa-list me-10"></i>Plan de intervención</button>
                                            <button type="button" onclick="imprimirHistoria(' . $item->id . ');"
                                                class="waves-effect waves-light btn btn-info btn-flat"><i
                                                    class="fa fa-print me-10"></i>Imprimir</button>
                                            <button type="button" onclick="eliminarHistoria(' . $item->id . ');"
                                                class="waves-effect waves-light btn btn-danger btn-flat"><i
                                                    class="fa fa-trash-o me-10"></i>Eliminar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                    $x++;
                }
            }

            $pagination = $ListHistoria->links('HistoriasClinica.Paginacion')->render();

            return response()->json([
                'historias' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function listaPacientesInformePsicologia(Request $request)
    {
        if (Auth::check()) {
            $perPage = 10; // Número de posts por página
            $page = request()->get('page', 1);
            $search = request()->get('search');
            if (!is_numeric($page)) {
                $page = 1; // Establecer un valor predeterminado si no es numérico
            }



            $pacientesEvol = DB::connection('mysql')
                ->table('historia_clinica')
                ->leftJoin('profesionales', 'profesionales.id', 'historia_clinica.id_profesional')
                ->leftJoin('pacientes', 'pacientes.id', '=', 'historia_clinica.id_paciente')
                ->where('historia_clinica.estado_registro', 'ACTIVO')
                ->select(
                    'pacientes.id',
                    'profesionales.nombre as profesional',
                    DB::raw("CONCAT(pacientes.tipo_identificacion, ' ', pacientes.identificacion) as identificacion"),
                    DB::raw("CONCAT(primer_nombre, ' ', segundo_nombre, ' ', primer_apellido, ' ', segundo_apellido) as nombre"),
                    DB::raw('MAX(historia_clinica.fecha_historia) as fecha_historia')
                )
                ->groupBy('pacientes.id', 'tipo_identificacion', 'identificacion', 'primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido', 'profesionales.nombre');



            if ($search) {
                $pacientesEvol->where(function ($query) use ($search) {
                    $query->where('pacientes.identificacion', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_nombre', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.primer_apellido', 'LIKE', '%' . $search . '%')
                        ->orWhere('pacientes.segundo_apellido', 'LIKE', '%' . $search . '%');
                });
            }


            $ListPacientesEvol = $pacientesEvol->paginate($perPage, ['*'], 'page', $page);

            $tdTable = '';
            $x = ($page - 1) * $perPage + 1;

            foreach ($ListPacientesEvol as $i => $item) {
                if (!is_null($item)) {
                    $tdTable .= '<tr>
                                    <td>' . $item->identificacion . ' - ' . $item->nombre . '</td>
                                    <td>' . date('d/m/Y g:i:s A', strtotime($item->fecha_historia)) . '</td>
                                    <td>' . $item->profesional . '</td>
                                    <td class="table-action min-w-100">
                                        <a onclick="verHistorial(' . $item->id . ');" style="cursor: pointer;" title="Ver historial" class="text-fade hover-primary"><i class="align-middle"
                                                data-feather="eye"></i></a>
                                        <a onclick="imprimirInforme(' . $item->id . ');" style="cursor: pointer;" title="Imprimir informe" class="text-fade hover-warning"><i class="align-middle"
                                                data-feather="file-text"></i></a>
                                    </td>
                                </tr>';
                    $x++;
                }
            }

            $pagination = $ListPacientesEvol->links('HistoriasClinica.Paginacion')->render();

            return response()->json([
                'pacientesEvol' => $tdTable,
                'links' => $pagination
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function verHistorialEvoluciones(Request $request)
    {
        if (Auth::check()) {
            $idPaciente = $request->input('idPaciente');
            $historia = HistoriaPsicologica::busquedaHistoriaPaciente($idPaciente);
            $evoluciones = self::consultasLateral($historia->id);

            return response()->json([
                'evoluciones' => $evoluciones
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function buscaEvolucionPsicologica()
    {
        if (Auth::check()) {
            $idEvolucion = request()->input('idEvolucion');
            $evolucion = HistoriaPsicologica::busquedaConsultaDetalle($idEvolucion);
            $evolucion = "
            <div class='card'>
                <div class='card-body' style='max-height: 500px; overflow-y: auto;'>
                    <h3 class='modal-title'>Detalles de la evolución</h3>
                    <p><strong>Consulta:</strong> {$evolucion->consulta}</p>
                    <p><strong>Profesional:</strong> {$evolucion->profesional}</p>
                    <p><strong>Fecha de consulta:</strong><br>" . date('d/m/Y g:i:s A', strtotime($evolucion->fecha_consulta)) . "</p>
                    <p><strong>Diagnóstico:</strong><br>{$evolucion->diagnostico}</p>
                    <p><strong>Objetivo de la Sesión:</strong><br>{$evolucion->objetivo_sesion}</p>
                    <p><strong>Técnicas Utilizadas:</strong><br>{$evolucion->tecnicas_utilizadas}</p>
                    <p><strong>Actividades Específicas:</strong><br>{$evolucion->actividades_especificas}</p>
                    <p><strong>Evaluación / Indicadores de Éxito:</strong><br>{$evolucion->evaluacion_indicadores}</p>
                    <p><strong>Evolución de la Sesión:</strong><br>{$evolucion->evolucion_sesion}</p>
                </div>
            </div>";
            return response()->json([
                'evolucion' => $evolucion
            ]);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }


    public function imprimirInformePsicologia(Request $request)
    {
        if (Auth::check()) {
            $pdf = new Dompdf();
            $idInforme = $request->input('idInforme');
            $informe = HistoriaPsicologica::busquedaInforme($idInforme);

            // Ruta absoluta al logo
            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = now()->format('d-m-Y');
            $horaElaboracion = now()->format('H:i:s A');

            $paciente = Pacientes::busquedaPaciente($informe->id_paciente);

            $profesional = Profesional::busquedaProfesional($informe->id_profesional);
            $firmaPath = public_path('app-assets/images/firmasProfesionales/' . $profesional->firma);
            $firmaData = base64_encode(file_get_contents($firmaPath));
            $firma = 'data:image/png;base64,' . $firmaData;


            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y h:i A');


            $html = '<head>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        border-width: 0.1px;
                    }
                    th, td {
                        border: 0.1px solid black;
                        padding: 4px;
                    }
                    th {
                        background-color: #EAEBF4;
                    }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                    }
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    hr {
                        border-width: 0.1px;
                        border-color: #333;
                        border-style: solid;
                    }
                        
                </style>
            </head>';

            $html .= '<div style="page-break-after: always;">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0;"><img src="' . $logo . '" style="width: 200px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top;">';
            $html .= '<p style="margin: 0;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p style="margin: 0;">PSICÒLOGA - T.P. No. 259542</p>';
            $html .= '<p style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td class="no-border" colspan="2" style="text-align: center; padding: 1px;background-color: transparent;"> <h3>INFORME DE EVOLUCIÓN PSICOLÓGICA</h3></td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<table>
                    <tr>
                        <td ><b>FECHA DE ELABORACIÓN:</b> ' . $fechaElaboracion . '</td>
                        <td ><b>HORA:</b> ' . $horaElaboracion . '</td>
                    </tr>
                </table>';

            $html .= '<div class="section" >
                <h4 style="background-color: #EAEBF4; padding: 6px; margin-bottom: 10px;">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</h4>
                <table style="width: 100%; border-collapse: collapse; border: none;">
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block;">NOMBRE:</span>
                            <span style="border-bottom: 1px solid #ccc; padding-bottom: 2px;">' .
                $paciente->primer_nombre . ' ' .
                $paciente->segundo_nombre . ' ' .
                $paciente->primer_apellido . ' ' .
                $paciente->segundo_apellido .
                '</span>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block;">NACIMIENTO:</span>
                            <span style="border-bottom: 1px solid #ccc; padding-bottom: 2px;">' .
                $fechaNacimiento . ' - ' . $paciente->lugar_nacimiento .
                '</span>
                                    </td>
                                </tr>
                    <tr>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                            <span style="font-weight: bold; width: 120px; display: inline-block;">IDENTIFICACIÓN:</span>
                            <span style="border-bottom: 1px solid #ccc; padding-bottom: 2px;">' .
                $paciente->tipo_identificacion . ' ' . $paciente->identificacion .
                '</span>
                                    </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 30%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block;">EDAD:</span>
                            <span style="border-bottom: 1px solid #ccc; padding-bottom: 2px;">' .
                $paciente->edad .
                '</span>
                                    </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 30%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block;">SEXO:</span>
                            <span style="border-bottom: 1px solid #ccc; padding-bottom: 2px;">' .
                (($paciente->sexo === "M") ? "Mujer" : "Hombre") .
                '</span>
                        </td>
                    </tr>                   
                </table>
            </div>';

            $html .= '<div class="section">
                    <h4>2. IMPRESIÓN DIAGNÓSTICA:</h4>
                    <p>' . $informe->impresion_diagnostica->diagnostico . '</p>
                </div>

                <div class="section">
                    <h4>3. REMISIÓN:</h4>
                     ' . $informe->remision . '
                </div>

                <div class="section">
                    <h4>4. RESUMEN DE LA EVALUACIÓN PSICOLÓGICA INICIAL</h4>
                    ' . $informe->resumen_evaluacion_psicologica . '
                </div>

                <div class="section">
                    <h4>5. OBJETIVOS TERAPÈUTICOS INICIALES</h4>
                    ' . $informe->resumen_evaluacion_psicologica . '
                </div>';

            $html .= '<div class="section">
                    <h4>6. EVOLUCIÓN DEL TRATAMIENTO PSICOLÓGICO - ACTUAL</h4>
                        ' . $informe->objetivos_terapeuticos_iniciales . '
                </div>';

            $html .= '<div class="section">
                    <h4>7. EVALUACIÒN ACTUAL</h4>
                        ' . $informe->evaluacion_actual . '
                </div>';

            $html .= '

                <div class="section">
                    <h4>8. PLAN DE TRATAMIENTO DE CONTINUIDAD</h4>
                     ' . $informe->plan_tratamiento_continuidad . '
                </div>

                <div class="section">
                    <h4>9. INTERVENCIÓN INTERDISCIPLINARIA Y MULTIDISCIPLINARIA</h4>
                    <table>
                        <tr><td>9.1. INTERVENCIÓN POR PSIQUIATRÍA (MEDICACIÓN)</td></tr>
                        <tr><td>' . $informe->intervencion_psiquiatria . '</td></tr>
                        <tr><td>9.2. INTERVENCIÓN POR NEUROLOGÍA (MEDICACIÓN)</td></tr>
                        <tr><td>' . $informe->intervencion_neurologia . '</td></tr>
                        <tr><td>9.3. INTERVENCIÓN POR NEUROPSICOLOGÍA (INFORME)</td></tr>
                        <tr><td>' . $informe->intervencion_neuropsicologia . '</td></tr>
                    </table>
                </div>

                <div class="section">
                    <h4>10. SUGERENCIA PARA INTERCONSULTAS</h4>
                    ' . $informe->sugerencias_interconsultas . '
                </div>

                <div class="section">
                    <h4>11. OBSERVACIONES Y RECOMENDACIONES</h4>
                    ' . $informe->observaciones_recomendaciones . '
                </div>

                <div class="section">
                    <table style="width:100%; border-collapse: collapse; background-color: transparent;">
                        <tr><td class="no-border"><b>' . $profesional->nombre . '</b></td></tr>
                        <tr><td class="no-border"><img width="200" src="' . $firma . '" /></td></tr>
                       
                        <tr><td class="no-border"><b>TARJETA PROFESIONAL: ' . $profesional->registro . '</b></td></tr>
                    </table>
                </div></div>
            </body>
            </html>';

            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $pdfContent = $pdf->output();

            // Encabezados de respuesta para el archivo PDF
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="InformeEvolucion.pdf"',
            ];

            return response($pdfContent, 200, $headers);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function imprimirHistoria(Request $request)
    {
        $idHist = $request->input('idHist');

        $historia = HistoriaPsicologica::busquedaHistoria($idHist);


        $pacientes = Pacientes::busquedaPaciente($historia->id_paciente);
        $antecedentesPersonales = HistoriaPsicologica::busquedaAntecedentes($historia->id);
        $antecedentesFamiliares = HistoriaPsicologica::busquedaAntFamiliares($historia->id);
        $areaAjuste = HistoriaPsicologica::busquedaAreaAjuste($historia->id);
        $interconuslta = HistoriaPsicologica::busquedaInterconsulta($historia->id);
        $examenMental = HistoriaPsicologica::busquedaExamenMental($historia->id);

        $antecedentesPrenatales = HistoriaPsicologica::busquedaAntPrenatales($historia->id);
        $antecedentesNatales = HistoriaPsicologica::busquedaAntNatales($historia->id);
        $antecedentesPosnatales = HistoriaPsicologica::busquedaAntPosnatales($historia->id);
        $desarrolloPsicomotor = HistoriaPsicologica::desarrolloPsicomotor($historia->id);

        $data = [
            'historia' => $historia,
            'paciente' => $pacientes,
            'antecedentesPersonales' => $antecedentesPersonales,
            'antecedentesFamiliares' => $antecedentesFamiliares,
            'areaAjuste' => $areaAjuste,
            'interconuslta' => $interconuslta,
            'examenMental' => $examenMental,
            'antecedentesPrenatales' => $antecedentesPrenatales,
            'antecedentesNatales' => $antecedentesNatales,
            'antecedentesPosnatales' => $antecedentesPosnatales,
            'desarrolloPsicomotor' => $desarrolloPsicomotor
        ];

        $pdf = PDF::loadView('imprimir.imprimirHistoria', $data)->setPaper('a4');

        $fileName = 'Historia_Psicologica_' . $idHist . '.pdf';
        $filePath = 'historias_Psicologica/' . $fileName;
        $pdf->save(public_path($filePath));
        $url = asset($filePath);

        return response()->json(['url' => $url]);
    }

    public function imprimirConsulta(Request $request)
    {

        $idConsulta = $request->input('idConsulta');

        if (Auth::check()) {
            $pdf = new Dompdf();

            $consulta = HistoriaPsicologica::busquedaConsultaImprimir($idConsulta);


            $idPaciente = DB::connection('mysql')->table('historia_clinica')
                ->where('id', $consulta->id_historia)
                ->value('id_paciente');

            // Ruta absoluta al logo
            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = \Carbon\Carbon::parse($consulta->fecha_consulta)->format('d/m/Y h:i A');

            $paciente = Pacientes::busquedaPaciente($idPaciente);

            $profesional = Profesional::busquedaProfesional($consulta->id_profesional);
            $firmaPath = public_path('app-assets/images/firmasProfesionales/' . $profesional->firma);
            $firmaData = base64_encode(file_get_contents($firmaPath));
            $firma = 'data:image/png;base64,' . $firmaData;


            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y h:i A');


            $html = '<head>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        border-width: 0.1px;
                    }
                    th, td {
                        border: 0.1px solid black;
                        padding: 4px;
                    }
                    th {
                        background-color: #EAEBF4;
                    }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                    }
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    hr {
                        border-width: 0.1px;
                        border-color: #333;
                        border-style: solid;
                    }
                        
                    section {
                        margin-bottom: 10px;
                    }

                    .section p {
                       margin: 0;
                       padding: 0;
                    }
                </style>
            </head>';

            $html .= '<div style="page-break-after: always;">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0;"><img src="' . $logo . '" style="width: 200px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top;">';
            $html .= '<p style="margin: 0;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p style="margin: 0;">PSICÒLOGA - T.P. No. 259542</p>';
            $html .= '<p style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td class="no-border" colspan="2" style="text-align: center; padding: 1px;background-color: transparent;"> <h3>RESULTADO DE CONSULTA PSICOLÓGICA</h3></td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<table>
                    <tr>
                        <td ><b>FECHA DE ELABORACIÓN:</b> ' . $fechaElaboracion . '</td>
                    </tr>
                </table>';

            $html .= '<div class="section" >
                <h4 style="background-color: #EAEBF4; padding: 6px; margin-bottom: 10px;">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</h4>
                <table style="width: 100%; border-collapse: collapse; border: none;">
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block;">NOMBRE:</span>
                            <span style="border-bottom: 1px solid #ccc;">' .
                $paciente->primer_nombre . ' ' .
                $paciente->segundo_nombre . ' ' .
                $paciente->primer_apellido . ' ' .
                $paciente->segundo_apellido .
                '</span>
                                    </td>
                                </tr>
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block;">NACIMIENTO:</span>
                            <span style="border-bottom: 1px solid #ccc; ">' .
                $fechaNacimiento . ' - ' . $paciente->lugar_nacimiento .
                '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                            <span style="font-weight: bold; width: 150px; display: inline-block;">IDENTIFICACIÓN:</span>
                            <span style="border-bottom: 1px solid #ccc; ">' .
                $paciente->tipo_identificacion . ' ' . $paciente->identificacion .
                '</span>
                                    </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block;">EDAD:</span>
                            <span style="border-bottom: 1px solid #ccc; ">' .
                $paciente->edad .
                '</span>
                                    </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 20%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block;">SEXO:</span>
                            <span style="border-bottom: 1px solid #ccc; ">' .
                (($paciente->sexo === "M") ? "Mujer" : "Hombre") .
                '</span>
                                    </td>
                                </tr>
                            </table>
            </div>';

            $html .= '<div class="section">
                    <h4>2. DETALLE DE LA CONSULTA</h4>
                </div>';

            $html .= '<div class="section">
                    <h4>CONSULTA PSICOLÓGICA</h4>
                    <p>' . $consulta->codigo_consulta->consulta . '</p>
                </div>

                <div class="section">
                    <h4>MOTIVO DE CONSULTA:</h4>
                     ' . $consulta->motivo . '
                </div>

                <div class="section">
                    <h4>IMPRESIÓN DIAGNÓSTICA:</h4>
                    ' . $consulta->impresion_diagnostica->diagnostico . '
                </div>';

            if ($consulta->otra_impresion_diagnostica) {
                $html .= '<div class="section">
                        <h4>OTRO TIPO DE IMPRESIÓN DIAGNÓSTICA:</h4>
                    ' . $consulta->otra_impresion_diagnostica . '
                </div>';
            }

            $html .= '<div class="section">
                    <h4>OBJETIVO DE LA SESSIÓN</h4>
                        ' . $consulta->objetivo_sesion . '
                </div>';

            $html .= '<div class="section">
                    <h4>TÉCNICAS UTILIZADAS</h4>
                        ' . $consulta->tecnicas_utilizadas . '
                </div>';

            $html .= '

                <div class="section">
                    <h4>ACTIVIDADES ESPECÍFICAS</h4>
                     ' . $consulta->actividades_especificas . '
                </div>


                <div class="section">
                    <h4>EVALUACIÓN / INDICADORES DE EXITO</h4>
                    ' . $consulta->evaluacion_indicadores . '
                </div>

                <div class="section">
                    <h4>EVOLUCIÓN DE LA SESSIÓN</h4>
                    ' . $consulta->evolucion_sesion . '
                </div>

                <div class="section" style="text-align: left; margin-top: 20px;">
                    <table style="width:100%; border-collapse: collapse; background-color: transparent;">
                        <tr><td class="no-border" style="text-align: left;"><b>' . $profesional->nombre . '</b></td></tr>
                        <tr><td class="no-border" style="text-align: left;"><img width="100" src="' . $firma . '" /></td></tr>                       
                        <tr><td class="no-border" style="text-align: left; font-size: 10px;"><b>TARJETA PROFESIONAL: ' . $profesional->registro . '</b></td></tr>
                    </table>
                </div></div>
            </body>
            </html>';

            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $pdfContent = $pdf->output();

            // Encabezados de respuesta para el archivo PDF
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="resultado_consulta_psicologica.pdf"',
            ];


            $pdfContent = $pdf->output();

            return response($pdfContent, 200, $headers);
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }

    public function enviarConsulta(Request $request)
    {
        $idConsulta = $request->input('idConsulta');


        if (Auth::check()) {
            $pdf = new Dompdf();

            $consulta = HistoriaPsicologica::busquedaConsultaImprimir($idConsulta);


            $idPaciente = DB::connection('mysql')->table('historia_clinica')
                ->where('id', $consulta->id_historia)
                ->value('id_paciente');

            // Ruta absoluta al logo
            $logoPath = public_path('app-assets/images/logo/logo_prasca.png');

            // Convertir la imagen a base64
            $logoData = base64_encode(file_get_contents($logoPath));
            $logo = 'data:image/png;base64,' . $logoData;

            $fechaElaboracion = \Carbon\Carbon::parse($consulta->fecha_consulta)->format('d/m/Y h:i A');

            $paciente = Pacientes::busquedaPaciente($idPaciente);

            $profesional = Profesional::busquedaProfesional($consulta->id_profesional);
            $firmaPath = public_path('app-assets/images/firmasProfesionales/' . $profesional->firma);
            $firmaData = base64_encode(file_get_contents($firmaPath));
            $firma = 'data:image/png;base64,' . $firmaData;


            $fechaNacimiento = \Carbon\Carbon::parse($paciente->fecha_nacimiento)->format('d/m/Y h:i A');


            $html = '<head>
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        border-width: 0.1px;
                    }
                    th, td {
                        border: 0.1px solid black;
                        padding: 4px;
                    }
                    th {
                        background-color: #EAEBF4;
                    }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                    }
                    .no-border {
                        border: none;
                        text-align: center;
                    }
                    hr {
                        border-width: 0.1px;
                        border-color: #333;
                        border-style: solid;
                    }
                
                    section {
                        margin-bottom: 10px;
                    }

                    .section p {
                       margin: 0;
                       padding: 0;
                    }
                </style>
            </head>';

            $html .= '<div style="page-break-after: always;">';
            $html .= '<table style="width:100%; border-collapse: collapse; background-color: transparent;">';
            $html .= '<tr>';
            $html .= '<td class="no-border" style="padding: 0;"><img src="' . $logo . '" style="width: 200px; height: auto;"></td>';
            $html .= '<td class="no-border" style="padding: 0; vertical-align: top;">';
            $html .= '<p style="margin: 0;">DRA. MARIA ISABEL PUMAREJO</p>';
            $html .= '<p style="margin: 0;">PSICÒLOGA - T.P. No. 259542</p>';
            $html .= '<p style="margin: 0;">Calle 11 # 11 - 07 San Joaquin</p>';
            $html .= '<p style="margin: 0;">Teléfono: 312 5678078</p>';
            $html .= '</td>';
            $html .= '</tr>';
            $html .= '<tr>';
            $html .= '<td class="no-border" colspan="2" style="text-align: center; padding: 1px;background-color: transparent;"> <h3>RESULTADO DE CONSULTA PSICOLÓGICA</h3></td>';
            $html .= '</tr>';
            $html .= '</table>';

            $html .= '<table>
                    <tr>
                        <td ><b>FECHA DE ELABORACIÓN:</b> ' . $fechaElaboracion . '</td>
                    </tr>
                </table>';

            $html .= '<div class="section" >
                <h4 style="background-color: #EAEBF4; padding: 6px; margin-bottom: 10px;">1. DATOS DE IDENTIFICACIÓN DEL PACIENTE</h4>
                <table style="width: 100%; border-collapse: collapse; border: none;">
                    <tr>
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block;">NOMBRE:</span>
                            <span style="border-bottom: 1px solid #ccc;">' .
                $paciente->primer_nombre . ' ' .
                $paciente->segundo_nombre . ' ' .
                $paciente->primer_apellido . ' ' .
                $paciente->segundo_apellido .
                '</span>
                        </td>
                    </tr>
                    <tr>                 
                        <td colspan="3" style="border: none; padding: 8px 4px 4px 4px;">
                            <span style="font-weight: bold; width: 120px; display: inline-block;">NACIMIENTO:</span>
                            <span style="border-bottom: 1px solid #ccc; ">' .
                $fechaNacimiento . ' - ' . $paciente->lugar_nacimiento .
                '</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                            <span style="font-weight: bold; width: 150px; display: inline-block;">IDENTIFICACIÓN:</span>
                            <span style="border-bottom: 1px solid #ccc; ">' .
                $paciente->tipo_identificacion . ' ' . $paciente->identificacion .
                '</span>
                        </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 40%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block;">EDAD:</span>
                            <span style="border-bottom: 1px solid #ccc; ">' .
                $paciente->edad .
                '</span>
                        </td>
                        <td style="border: none; padding: 8px 4px 4px 4px; width: 20%;">
                            <span style="font-weight: bold; width: 60px; display: inline-block;">SEXO:</span>
                            <span style="border-bottom: 1px solid #ccc; ">' .
                (($paciente->sexo === "M") ? "Mujer" : "Hombre") .
                '</span>
                        </td>
                    </tr>
                </table>
            </div>';

            $html .= '<div class="section">
                    <h4>2. DETALLE DE LA CONSULTA</h4>
                </div>';

            $html .= '<div class="section">
                    <h4>CONSULTA PSICOLÓGICA</h4>
                    <p>' . $consulta->codigo_consulta->consulta . '</p>
                </div>

                <div class="section">
                    <h4>MOTIVO DE CONSULTA:</h4>
                     ' . $consulta->motivo . '
                </div>

                <div class="section">
                    <h4>IMPRESIÓN DIAGNÓSTICA:</h4>
                    ' . $consulta->impresion_diagnostica->diagnostico . '
                </div>';

            if ($consulta->otra_impresion_diagnostica) {
                $html .= '<div class="section">
                        <h4>OTRO TIPO DE IMPRESIÓN DIAGNÓSTICA:</h4>
                    ' . $consulta->otra_impresion_diagnostica . '
                </div>';
            }

            $html .= '<div class="section">
                    <h4>OBJETIVO DE LA SESSIÓN</h4>
                        ' . $consulta->objetivo_sesion . '
                </div>';

            $html .= '<div class="section">
                    <h4>TÉCNICAS UTILIZADAS</h4>
                        ' . $consulta->tecnicas_utilizadas . '
                </div>';

            $html .= '

                <div class="section">
                    <h4>ACTIVIDADES ESPECÍFICAS</h4>
                     ' . $consulta->actividades_especificas . '
                </div>


                <div class="section">
                    <h4>EVALUACIÓN / INDICADORES DE EXITO</h4>
                    ' . $consulta->evaluacion_indicadores . '
                </div>

                <div class="section">
                    <h4>EVOLUCIÓN DE LA SESSIÓN</h4>
                    ' . $consulta->evolucion_sesion . '
                </div>

                <div class="section" style="text-align: left; margin-top: 20px;">
                    <table style="width:100%; border-collapse: collapse; background-color: transparent;">
                        <tr><td class="no-border" style="text-align: left;"><b>' . $profesional->nombre . '</b></td></tr>
                        <tr><td class="no-border" style="text-align: left;"><img width="100" src="' . $firma . '" /></td></tr>                       
                        <tr><td class="no-border" style="text-align: left; font-size: 10px;"><b>TARJETA PROFESIONAL: ' . $profesional->registro . '</b></td></tr>
                    </table>
                </div></div>
            </body>
            </html>';

            $pdf->loadHtml($html);
            $pdf->setPaper('A4', 'portrait');
            $pdf->render();

            $pdfContent = $pdf->output();

            // Encabezados de respuesta para el archivo PDF
            $headers = [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="resultado_consulta_psicologica.pdf"',
            ];


            $pdfContent = $pdf->output();


            if ($paciente->email == "" || $paciente->email == null) {
                return response()->json(['resultado' => "noCorreo"]);
            } else {
                //enviar al correo del paciente el pdf
                $mail = new PHPMailer(true);
                $mensaje = 'Se ha enviado un archivo adjunto con el resultado de la consulta psicologica';
                $asunto = 'Resultado de Consulta Psicologica - Prasca Center';

                $contenido = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
                    <html xmlns='http://www.w3.org/1999/xhtml'>
                    <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
                    <meta name='viewport' content='width=device-width, initial-scale=1' />
                    <title>Narrative Invitation Email</title>
                    <link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css'>
                    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js'></script>
                    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js'></script>
                    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js'></script>
                    <style type='text/css'>
            
                    /* Take care of image borders and formatting */
            
                    img {
                        max-width: 600px;
                        outline: none;
                        text-decoration: none;
                        -ms-interpolation-mode: bicubic;
                    }
            
                    a {
                        border: 0;
                        outline: none;
                    }
            
                    a img {
                        border: none;
                    }
            
                    /* General styling */
            
                    td, h1, h2, h3  {
                        font-family: Helvetica, Arial, sans-serif;
                        font-weight: 400;
                    }
            
                    td {
                        font-size: 13px;
                        line-height: 19px;
                        text-align: left;
                    }
            
                    body {
                        -webkit-font-smoothing:antialiased;
                        -webkit-text-size-adjust:none;
                        width: 100%;
                        height: 100%;
                        color: #37302d;
                        background: #ffffff;
                    }
            
                    table {
                        border-collapse: collapse !important;
                    }
            
            
                    h1, h2, h3, h4 {
                        padding: 0;
                        margin: 0;
                        color: #444444;
                        font-weight: 400;
                        line-height: 110%;
                    }
            
                    h1 {
                        font-size: 35px;
                    }
            
                    h2 {
                        font-size: 30px;
                    }
            
                    h3 {
                        font-size: 24px;
                    }
            
                    h4 {
                        font-size: 18px;
                        font-weight: normal;
                    }
            
                    .important-font {
                        color: #21BEB4;
                        font-weight: bold;
                    }
            
                    .hide {
                        display: none !important;
                    }
            
                    .force-full-width {
                        width: 100% !important;
                    }
            
                    .rps_16ec table#x_main-wrapper {
                        border-collapse: collapse;
                        border-spacing: 0;
                        border: none;
                        margin: 0 auto;
                        width: 100%;
                      }
            
                      .rps_16ec #x_greeting {
                        text-align: center;
                      }
            
                      .rps_16ec table.x_appt-data {
                        width: auto;
                        margin: 0 auto;
                      }
            
                      .rps_16ec .x_data-row {
                        margin: 0 auto;
                        width: auto;
                      }
            
                      .rps_16ec .x_appt-data tr:first-child td {
                        padding-top: 12px;
                      }
            
                      .rps_16ec .x_data-row .x_label {
                        width: 25%;
                        font-weight: bold;
                        color: #0097cc;
                        text-align: right;
                      }
            
                      .rps_16ec .x_header td {
                        background: #0097cc;
                        padding: 3px;
                        color: #fafafa;
                        text-align: center;
                      }
            
                      .rps_16ec #x_initial-text {
                        padding: 18px 0;
                        line-height: 1.4em;
                      }
            
                      .rps_16ec .x_appt-data tr:first-child td {
                        padding-top: 12px;
                      }
                      .rps_16ec .x_data-row .x_label, .rps_16ec .x_data-row .x_data {
                        padding: 4px;
                          padding-top: 4px;
                      }
            
                    </style>
            
                    <style type='text/css' media='screen'>
                        @media screen {
                            @import url(http://fonts.googleapis.com/css?family=Open+Sans:400);
            
                            /* Thanks Outlook 2013! */
                            td, h1, h2, h3 {
                            font-family: 'Open Sans', 'Helvetica Neue', Arial, sans-serif !important;
                            }
                        }
                    </style>
            
                    <style type='text/css' media='only screen and (max-width: 600px)'>
                        /* Mobile styles */
                        @media only screen and (max-width: 600px) {
            
                        table[class='w320'] {
                            width: 320px !important;
                        }
            
                        table[class='w300'] {
                            width: 300px !important;
                        }
            
                        table[class='w290'] {
                            width: 290px !important;
                        }
            
                        td[class='w320'] {
                            width: 320px !important;
                        }
            
                        td[class~='mobile-padding'] {
                            padding-left: 14px !important;
                            padding-right: 14px !important;
                        }
            
                        td[class*='mobile-padding-left'] {
                            padding-left: 14px !important;
                        }
            
                        td[class*='mobile-padding-right'] {
                            padding-right: 14px !important;
                        }
            
                        td[class*='mobile-padding-left-only'] {
                            padding-left: 14px !important;
                            padding-right: 0 !important;
                        }
            
                        td[class*='mobile-padding-right-only'] {
                            padding-right: 14px !important;
                            padding-left: 0 !important;
                        }
            
                        td[class*='mobile-block'] {
                            display: block !important;
                            width: 100% !important;
                            text-align: left !important;
                            padding-left: 0 !important;
                            padding-right: 0 !important;
                            padding-bottom: 15px !important;
                        }
            
                        td[class*='mobile-no-padding-bottom'] {
                            padding-bottom: 0 !important;
                        }
            
                        td[class~='mobile-center'] {
                            text-align: center !important;
                        }
            
                        table[class*='mobile-center-block'] {
                            float: none !important;
                            margin: 0 auto !important;
                        }
            
                        *[class*='mobile-hide'] {
                            display: none !important;
                            width: 0 !important;
                            height: 0 !important;
                            line-height: 0 !important;
                            font-size: 0 !important;
                        }
            
                        td[class*='mobile-border'] {
                            border: 0 !important;
                        }
                        }
                    </style>
                    </head>
                    <body class='body' style='padding:0; margin:0; display:block; background:#ffffff; -webkit-text-size-adjust:none' bgcolor='#ffffff'>
                    <div class='rps_16ec'>
                    <div>
                    <table id='x_main-wrapper'>
                    <thead id='x_logo'>
                    <tr>
                    <th>
                    <img data-imagetype='External' src='" . $logo . "' width = '200px'  alt='PERFECTA' class='x_responsive'> 
                    </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <td id='x_greeting'>
                    Estimad@  <strong style='text-transform: capitalize;'> " . $paciente->primer_nombre . " " . $paciente->segundo_nombre . " " . $paciente->primer_apellido . " " . $paciente->segundo_apellido . ",</strong>
                    </td>
                    </tr>
                    <tr>
                    <td  id='x_initial-text'>
                    " . $mensaje . "
                    </td>
                    </tr>
                    <div>
                    </body>
                    </html>";

                try {
                    require base_path("vendor/autoload.php");
                    $mail->isSMTP();
                    $mail->Host = 'mail.prascacenter.com';  // Servidor SMTP de tu hosting
                    $mail->SMTPAuth = true;
                    $mail->Username = 'notificaciones@prascacenter.com'; // Tu correo completo
                    $mail->Password = 'isabel_2025*'; // Tu contraseña
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // O ENCRYPTION_SMTPS si usas SSL
                    $mail->Port = 587; // 465 si usas SSL, 587 para TLS

                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';
                    // Opcional: Si hay problemas con el certificado SSL
                    $mail->SMTPOptions = [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                            'allow_self_signed' => true
                        ]
                    ];

                    // Configuración del correo
                    $mail->setFrom('notificaciones@prascacenter.com', 'Prasca Center');
                    $mail->addAddress($paciente->email, $paciente->primer_nombre . ' ' . $paciente->segundo_nombre . ' ' . $paciente->primer_apellido . ' ' . $paciente->segundo_apellido); // Correo y nombre del destinatario
                    $mail->isHTML(true);
                    $mail->Subject = $asunto;
                    $mail->Body = $contenido;
                    $mail->addStringAttachment($pdfContent, 'resultado_consulta_psicologica.pdf');
                    // Enviar el correo
                    $mail->send();
                    return response()->json(['resultado' => "enviado"]);
                } catch (Exception $e) {
                    return response()->json(['resultado' => "error"]);
                }
            }
        } else {
            return redirect("/")->with("error", "Su Sesión ha Terminado");
        }
    }
}
